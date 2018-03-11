<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Redis;

class SettingsHelper
{
    /**
     * Get setting.
     *
     * @param $key
     * @return string
     */
    public static function get($key)
    {
        $value = decrypt(Redis::get($key));
        return $value;
    }

    /**
     * Encrypt and set setting in Redis store.
     *
     * @param $key
     * @param $value
     * @return mixed
     */
    public static function set($key, $value)
    {
        $value = encrypt($value);
        return Redis::set($key, $value);
    }
}