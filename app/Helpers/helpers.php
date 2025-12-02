<?php

use App\Models\Setting;

if (!function_exists('setting')) {
    /**
     * Ayar değerini getir
     * 
     * @param string $key Ayar anahtarı (örn: 'auth.registration_enabled')
     * @param mixed $default Varsayılan değer
     * @return mixed
     */
    function setting(string $key, $default = null)
    {
        return Setting::get($key, $default);
    }
}
