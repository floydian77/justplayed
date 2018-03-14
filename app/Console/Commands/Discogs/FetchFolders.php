<?php

namespace App\Console\Commands\Discogs;

use Illuminate\Console\Command;

class FetchFolders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discogs:fetch-folders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch all user folders';

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
