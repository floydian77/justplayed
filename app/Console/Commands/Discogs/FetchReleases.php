<?php

namespace App\Console\Commands\Discogs;

use Illuminate\Console\Command;

class FetchReleases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discogs:fetch-releases';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch all releases from user collection';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
    }
}
