<?php

namespace App\Console\Commands\Discogs;

use Illuminate\Console\Command;

class Sync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discogs:sync {--user=1 : User id}';

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
            '--user' => $this->option('user')
        ]);

        $this->call('discogs:fetch-user-collection', [
            '--user' => $this->option('user')
        ]);

        $this->call('discogs:fetch-releases', [
            '--user' => $this->option('user')
        ]);

        $this->call('discogs:fetch-masters', [
            '--user' => $this->option('user')
        ]);

        $this->call('discogs:fetch-artists', [
            '--user' => $this->option('user')
        ]);

        $this->call('discogs:post-process', [
            '--user' => $this->option('user')
        ]);
    }
}
