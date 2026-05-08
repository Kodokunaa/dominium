<?php
defined('APP_ROOT') OR exit('No direct script access allowed');

/**
 * Simple Router Class based on LavaLust
 * Ronald M. Marasigan
 */
class Router
{
    private array $routes = [];
    private array $global_middleware = [];
    private string $csrf_token = '';

    private ?array $pending_route = null;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        $this->csrf_token = $_SESSION['csrf_token'];
    }

    public function get(string $path, string|callable $handler, array $middleware = []): self
    {
        $this->commit_pending_route(); // save previous if any

        $this->pending_route = [
            'method'     => 'GET',
            'path'       => trim($path, '/'),
            'handler'    => $handler,
            'middleware' => $middleware,
        ];
        return $this;
    }

    public function post(string $path, string|callable|array $handler, array $middleware = []): self
    {
        $this->commit_pending_route();

        $this->pending_route = [
            'method'     => 'POST',
            'path'       => trim($path, '/'),
            'handler'    => $handler,
            'middleware' => $middleware,
        ];
        return $this;
    }

    public function put(string $path, string|callable $handler, array $middleware = []): self
    {
        $this->commit_pending_route();

        $this->pending_route = [
            'method'     => 'PUT',
            'path'       => trim($path, '/'),
            'handler'    => $handler,
            'middleware' => $middleware,
        ];
        return $this;
    }

    public function patch(string $path, string|callable $handler, array $middleware = []): self
    {
        $this->commit_pending_route();

        $this->pending_route = [
            'method'     => 'PATCH',
            'path'       => trim($path, '/'),
            'handler'    => $handler,
            'middleware' => $middleware,
        ];
        return $this;
    }

    public function delete(string $path, string|callable $handler, array $middleware = []): self
    {
        $this->commit_pending_route();

        $this->pending_route = [
            'method'     => 'DELETE',
            'path'       => trim($path, '/'),
            'handler'    => $handler,
            'middleware' => $middleware,
        ];
        return $this;
    }

    public function any(string $path, string|callable $handler, array $middleware = []): self
    {
        $this->commit_pending_route();

        $this->pending_route = [
            'method'     => 'ANY',
            'path'       => trim($path, '/'),
            'handler'    => $handler,
            'middleware' => $middleware,
        ];
        return $this;
    }

    public function middleware(string|callable $mw): self
    {
        if ($this->pending_route === null) {
            throw new RuntimeException(
                "Cannot call ->middleware() without a preceding route definition (get/post/put/etc.)"
            );
        }

        $this->pending_route['middleware'][] = $mw;
        return $this;
    }

    private function commit_pending_route(): void
    {
        if ($this->pending_route !== null) {
            $this->routes[] = $this->pending_route;
            $this->pending_route = null;
        }
    }

    public function group(array $attributes, callable $callback): self
    {
        $this->commit_pending_route();

        $previous_count = count($this->routes);

        $callback($this);

        $this->commit_pending_route();

        $new_routes = array_slice($this->routes, $previous_count);

        foreach ($new_routes as &$route) {
            // Apply prefix
            if (!empty($attributes['prefix'])) {
                $prefix = trim($attributes['prefix'], '/');
                if ($prefix !== '') {
                    $routePath = ltrim($route['path'], '/');
                    $route['path'] = $routePath === '' ? $prefix : $prefix . '/' . $routePath;
                }
            }

            // Merge middleware
            if (!empty($attributes['middleware'])) {
                $route['middleware'] = array_merge(
                    (array) $attributes['middleware'],
                    $route['middleware'] ?? []
                );
            }
        }

        array_splice($this->routes, $previous_count, count($new_routes), $new_routes);

        return $this;
    }

    public function add_global_middleware(callable $middleware): self
    {
        $this->global_middleware[] = $middleware;
        return $this;
    }

    public function csrf_field(): void
    {
        echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($this->csrf_token) . '">';
    }

    public function is_csrf_valid(): bool
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if (!in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return true;
        }

        $submitted = $_POST['csrf_token'] ?? '';
        return $submitted !== '' && hash_equals($this->csrf_token, $submitted);
    }

    private function regenerate_csrf(): void
    {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $this->csrf_token = $_SESSION['csrf_token'];
    }

    public function get_base_path(): string
    {
        $script = $_SERVER['SCRIPT_NAME'] ?? '';
        $script = preg_replace('#/index\.php$#', '', $script);
        $script = preg_replace('#/index$#', '', $script);
        $base = '/' . trim($script, '/') . '/';
        return $base === '//' ? '/' : $base;
    }

    public function run(): void
    {
        $this->commit_pending_route();

        $base_path = $this->get_base_path();

        $request_method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

        if (str_starts_with($uri, $base_path)) {
            $uri = substr($uri, strlen($base_path) - 1) ?: '/';
        }

        $uri = trim($uri, '/');
        $uri_parts = $uri === '' ? [] : explode('/', $uri);

        foreach ($this->routes as $route) {
            $method_match = $route['method'] === 'ANY' || $route['method'] === $request_method;
            if (!$method_match) {
                continue;
            }

            $route_parts = $route['path'] === '' ? [] : explode('/', $route['path']);
            $params = [];
            $match = true;
            $i = 0;

            while ($i < count($route_parts)) {
                $part = $route_parts[$i];

                if (preg_match('/^{(.+?)\?}$/', $part, $m)) {
                    $param_name = $m[1];
                    $params[$param_name] = $uri_parts[$i] ?? null;
                } elseif (preg_match('/^{(.+?)}$/', $part, $m)) {
                    $param_name = $m[1];
                    if (!isset($uri_parts[$i])) {
                        $match = false;
                        break;
                    }
                    $params[$param_name] = $uri_parts[$i];
                } else {
                    if (!isset($uri_parts[$i]) || $part !== $uri_parts[$i]) {
                        $match = false;
                        break;
                    }
                }

                $i++;
            }

            if ($i < count($uri_parts)) {
                $match = false;
            }

            if (!$match) {
                continue;
            }

            $all_middleware = array_merge(
                $this->global_middleware,
                $route['middleware'] ?? []
            );

            try {
                foreach ($all_middleware as $mw) {
                    $callable = null;

                    if (is_callable($mw)) {
                        $callable = $mw;
                    }
                    elseif (is_string($mw)) {
                        $file = $mw;

                        if (!str_ends_with($file, '.php')) {
                            $file .= '.php';
                        }

                        if (!str_contains($file, '/')) {
                            $file = dirname(__DIR__) . "/app/middlewares/$file";
                        }

                        if (file_exists($file)) {
                            $included = include $file;

                            if (is_callable($included)) {
                                $callable = $included;
                            } else {
                                if (defined('IS_DEV') && IS_DEV) {
                                    throw new RuntimeException(
                                        "Middleware file '$file' must return a callable. Returned: " .
                                        var_export($included, true)
                                    );
                                }
                                $callable = fn() => true;
                            }
                        }
                        else {
                            if (defined('IS_DEV') && IS_DEV) {
                                throw new RuntimeException("Middleware file not found: $file");
                            }
                            $callable = fn() => true;
                        }
                    }
                    else {
                        // Completely invalid type
                        if (defined('IS_DEV') && IS_DEV) {
                            throw new RuntimeException(
                                "Middleware must be callable or string filename. Got: " . gettype($mw)
                            );
                        }
                        $callable = fn() => true;
                    }

                    $continue = $callable($request_method, $params);

                    if ($continue === false) {
                        $this->render_error_page(403, 'Access denied by middleware.');
                    }
                }

                if (in_array($request_method, ['POST', 'PUT', 'PATCH', 'DELETE'])
                    && !$this->is_csrf_valid()) {
                    $this->render_error_page(403, 'CSRF token validation failed. Please refresh the page and try again.');
                }

                $this->execute_handler($route['handler'], $params);

                if (in_array($request_method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
                    $this->regenerate_csrf();
                }

            } catch (Throwable $e) {
                error_log('Router error: ' . $e->getMessage());
                $this->render_error_page(500, 'Server error: ' . $e->getMessage());
            }

            return;
        }

        http_response_code(404);
        include dirname(__DIR__) . '/app/views/errors/404.php';
        exit;
    }

    private function render_error_page(int $status, string $message): void
    {
        http_response_code($status);
        $error = (defined('IS_DEV') && IS_DEV)
            ? htmlspecialchars($message, ENT_QUOTES, 'UTF-8')
            : ($status === 403 ? 'Access denied or request expired. Please refresh and try again.' : 'Something went wrong. Please try again later.');

        $view = dirname(__DIR__) . '/app/views/errors/' . $status . '.php';
        if (!file_exists($view)) {
            $view = dirname(__DIR__) . '/app/views/errors/500.php';
        }

        include $view;
        exit;
    }

    private function execute_handler(string|callable|array $handler, array $params): void
    {
        if (is_array($handler) && count($handler) === 2) {
            [$class, $method] = $handler;

            if (is_string($class) && is_string($method)) {
                if (!class_exists($class)) {
                    $controllerFile = APP_ROOT . '/app/controllers/' . $class . '.php';
                    if (file_exists($controllerFile)) {
                        include_once $controllerFile;
                    }
                }

                if (class_exists($class) && method_exists($class, $method)) {
                    $reflection = new ReflectionMethod($class, $method);
                    $parameters = $reflection->getParameters();
                    $args = [];
                    
                    foreach ($parameters as $param) {
                        $paramName = $param->getName();
                        if (isset($params[$paramName])) {
                            $args[] = $params[$paramName];
                        } elseif ($param->isDefaultValueAvailable()) {
                            $args[] = $param->getDefaultValue();
                        } else {
                            $args[] = null;
                        }
                    }
                    
                    call_user_func_array([$class, $method], $args);
                    return;
                }
            }
        }

        if (is_callable($handler)) {
            $reflection = new ReflectionFunction($handler);
            $parameters = $reflection->getParameters();
            $args = [];
            
            foreach ($parameters as $param) {
                $paramName = $param->getName();
                if (isset($params[$paramName])) {
                    $args[] = $params[$paramName];
                } elseif ($param->isDefaultValueAvailable()) {
                    $args[] = $param->getDefaultValue();
                } else {
                    $args[] = null;
                }
            }
            
            call_user_func_array($handler, $args);
            return;
        }

        if (is_string($handler) && str_contains($handler, '@')) {
            [$class, $method] = explode('@', $handler, 2);

            if (!class_exists($class)) {
                $controllerFile = APP_ROOT . '/app/controllers/' . $class . '.php';
                if (file_exists($controllerFile)) {
                    include_once $controllerFile;
                }
            }

            if (class_exists($class) && method_exists($class, $method)) {
                call_user_func_array([$class, $method], $params);
                return;
            }
        }

        throw new RuntimeException("Invalid handler: " . var_export($handler, true));
    }
}