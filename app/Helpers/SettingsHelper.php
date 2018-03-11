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
}