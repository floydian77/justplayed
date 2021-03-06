<?php

namespace App\Console\Commands\Discogs;

use App\Helpers\RedisHash;
use Illuminate\Support\Facades\Redis;

class FetchFolders extends DiscogsCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discogs:fetch-folders {--user=1 : User id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch all user collection folders';

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
        $this->info('Fetching all user collection folders');

        $response = $this->fetchUserCollectionFolders();
        $this->line('Fetched user collection folders');

        $this->setUserCollectionFolders($response['folders']);
        $this->line('Setted user collection folders');
    }

    /**
     * Fetch user collection folders from discogs.
     *
     * @return mixed
     */
    private function fetchUserCollectionFolders()
    {
        // Call discogs service to fetch folders.
        $response = $this->service
            ->getCollectionFolders([
                'username' => $this->username
            ]);

        return $response;
    }

    /**
     * Store user collection folders in redis.
     *
     * @param $folders
     */
    private function setUserCollectionFolders($folders)
    {
        Redis::pipeline(function($pipe) use ($folders) {
            $pipe->del(
                RedisHash::folders($this->id)
            );
            foreach($folders as $folder) {
                $pipe->hset(
                    RedisHash::folders($this->id),
                    $folder['id'],
                    json_encode($folder)
                );
            }
        });
    }
}
