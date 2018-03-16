<?php

namespace App\Console\Commands\Discogs;


use App\Helpers\DiscogsHelper;
use App\Helpers\SettingsHelper;
use Illuminate\Console\Command;

abstract class DiscogsCommand extends Command
{
    protected $service;

    protected $id;
    protected $username;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->init();
    }

    /**
     * Initialize discogs service.
     *
     * @return void
     */
    private function init()
    {
        $this->id = $this->option('user');

        $this->username = SettingsHelper::get(sprintf(
            "settings:user:%d:discogs:username",
            $this->id
        ));

        $token = SettingsHelper::get(sprintf(
            "settings:user:%d:discogs:token",
            $this->id
        ));

        $this->service = DiscogsHelper::discogsService($token);
    }
}