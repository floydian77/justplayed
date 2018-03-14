<?php

namespace App\Console\Commands\Discogs;

use Illuminate\Console\Command;

class SyncCollection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discogs:sync-collection {id=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize discogs collection';

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
        $this->call('discogs:fetch-folders', [
            'id' => $this->argument('id')
        ]);
    }
}
