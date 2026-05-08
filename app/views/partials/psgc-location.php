<script>
document.addEventListener('DOMContentLoaded', () => {
    const provinceSelect = document.querySelector('#province');
    const citySelect = document.querySelector('#city');
    if (!provinceSelect || !citySelect) {
        return;
    }

    const apiBase = '<?= url('api/psgc') ?>';
    const selectedProvince = provinceSelect.dataset.selected || '';
    const selectedCity = '<?= esc(dominium_form_value('city')) ?>' || citySelect.dataset.selected || '';
    const provinceMap = new Map();

    function setProvinceError(message) {
        provinceSelect.innerHTML = '';
        const opt = document.createElement('option');
        opt.value = '';
        opt.textContent = message;
        provinceSelect.appendChild(opt);
    }

    function setCityError(message) {
        citySelect.innerHTML = '';
        const opt = document.createElement('option');
        opt.value = '';
        opt.textContent = message;
        citySelect.appendChild(opt);
    }

    async function fetchJson(url) {
        try {
            const response = await fetch(url, { credentials: 'same-origin' });
            const text = await response.text();
            if (!response.ok) {
                return { ok: false, data: [] };
            }
            const data = JSON.parse(text);
            return { ok: true, data: Array.isArray(data) ? data : [] };
        } catch (e) {
            return { ok: false, data: [] };
        }
    }

    async function loadCities(provinceCode, selected = '') {
        citySelect.innerHTML = '<option value="">Loading cities...</option>';
        const { ok, data: cities } = await fetchJson(
            `${apiBase}/cities?provinceCode=${encodeURIComponent(provinceCode)}`
        );
        if (!ok || cities.length === 0) {
            setCityError(cities.length === 0 && ok ? 'No locations found for this province.' : 'Could not load cities. Try again or refresh.');
            return;
        }

        citySelect.innerHTML = '<option value="">Select a city / municipality</option>';
        cities.sort((a, b) => (a.name || '').localeCompare(b.name || '')).forEach((city) => {
            const option = document.createElement('option');
            option.value = city.name;
            option.textContent = city.name;
            if (city.name === selected) {
                option.selected = true;
            }
            citySelect.appendChild(option);
        });
    }

    async function loadProvinces() {
        provinceSelect.innerHTML = '<option value="">Loading provinces...</option>';
        const { ok, data: provinces } = await fetchJson(`${apiBase}/provinces`);

        if (!ok || provinces.length === 0) {
            setProvinceError(
                provinces.length === 0 && ok
                    ? 'No provinces returned. Check server can reach psgc.gitlab.io.'
                    : 'Could not load provinces. Check your connection and refresh.'
            );
            citySelect.innerHTML = '<option value="">Province required first</option>';
            return;
        }

        provinceSelect.innerHTML = '<option value="">Select a province</option>';
        provinces
            .sort((a, b) => (a.name || '').localeCompare(b.name || ''))
            .forEach((province) => {
                const option = document.createElement('option');
                option.value = province.name;
                option.textContent = province.name;
                provinceMap.set(province.name, province);

                if (province.name === selectedProvince) {
                    option.selected = true;
                }

                provinceSelect.appendChild(option);
            });

        if (provinceSelect.value) {
            const province = provinceMap.get(provinceSelect.value);
            if (province && province.code) {
                await loadCities(province.code, selectedCity);
            }
        }
    }

    provinceSelect.addEventListener('change', async () => {
        const province = provinceMap.get(provinceSelect.value);
        if (!province) {
            citySelect.innerHTML = '<option value="">Select a province first</option>';
            return;
        }
        await loadCities(province.code);
    });

    loadProvinces();
});
</script>
