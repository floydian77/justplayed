<?php

namespace App\Console\Commands\Discogs;


class FetchUserCollection extends DiscogsCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discogs:fetch-user-collection {id=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch discogs user collection';

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
        parent::handle();
        $this->info('Fetching user collection');
    }
}
