<?php
defined('APP_ROOT') OR exit('No direct script access allowed');

class PSGCController
{
    private const BASE = 'https://psgc.gitlab.io/api/';

    /**
     * Fetch JSON from PSGC. Uses cURL first (works when allow_url_fopen is off; better on Windows/XAMPP).
     */
    private static function fetch(string $endpoint): array
    {
        $url = self::BASE . ltrim($endpoint, '/');
        $body = self::http_get($url);
        if ($body === null || $body === '') {
            return [];
        }

        $data = json_decode($body, true);
        return is_array($data) ? $data : [];
    }

    private static function http_get(string $url): ?string
    {
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            $opts = [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_CONNECTTIMEOUT => 15,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_HTTPHEADER => [
                    'User-Agent: Dominium/1.0',
                    'Accept: application/json',
                ],
            ];
            // Local XAMPP often lacks a CA bundle; relaxed TLS only in dev.
            if (defined('IS_DEV') && IS_DEV) {
                $opts[CURLOPT_SSL_VERIFYPEER] = false;
                $opts[CURLOPT_SSL_VERIFYHOST] = 0;
            }
            curl_setopt_array($ch, $opts);
            $body = curl_exec($ch);
            $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($body !== false && $code >= 200 && $code < 300) {
                return $body;
            }
        }

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: Dominium/1.0\r\nAccept: application/json\r\n",
                'timeout' => 60,
            ],
            'ssl' => [
                'verify_peer' => !(defined('IS_DEV') && IS_DEV),
                'verify_peer_name' => !(defined('IS_DEV') && IS_DEV),
            ],
        ]);

        $body = @file_get_contents($url, false, $context);
        return $body === false ? null : $body;
    }

    private static function is_psgc_code(string $code): bool
    {
        return $code !== '' && preg_match('/^[0-9]{9,10}$/', $code) === 1;
    }

    public static function regions(): void
    {
        json_response(self::fetch('regions'));
    }

    public static function provinces(): void
    {
        $provinces = self::fetch('provinces');
        $regionCode = trim($_GET['regionCode'] ?? '');

        if ($regionCode !== '') {
            $provinces = array_values(array_filter($provinces, function ($province) use ($regionCode) {
                return isset($province['regionCode']) && $province['regionCode'] === $regionCode;
            }));
        }

        json_response($provinces);
    }

    public static function cities(): void
    {
        $provinceCode = trim($_GET['provinceCode'] ?? '');

        if ($provinceCode === '' || !self::is_psgc_code($provinceCode)) {
            json_response([]);
            return;
        }

        // Per-province endpoint is small; avoids downloading the full cities dataset (timeouts / memory).
        $path = 'provinces/' . rawurlencode($provinceCode) . '/cities-municipalities';
        json_response(self::fetch($path));
    }
}
