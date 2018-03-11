<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class BladeHelperServiceProvider extends ServiceProvider
{
    protected $helpers = [
        'DiscogsHelper'
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        foreach ($this->helpers as $helper) {
            $helper_path = app_path() . '/Helpers/Blade/' . $helper . '.php';

            if (\File::isFile($helper_path)) {
                require_once $helper_path;
            }
        }
    }
}
