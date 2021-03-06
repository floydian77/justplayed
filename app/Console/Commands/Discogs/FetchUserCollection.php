<?php

namespace App\Console\Commands\Discogs;


use App\Helpers\DiscogsHelper;
use App\Helpers\RedisHash;
use Illuminate\Support\Facades\Redis;

class FetchUserCollection extends DiscogsCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discogs:fetch-user-collection {--user=1 : User id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch discogs user collection';

    private $releases = array();

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

        $this->fetchUserCollection();
        $this->line('Fetched user collection');

        $this->storeUserCollection();
        $this->line('Stored user collection');
    }

    /**
     * Fetch user collection from discogs.
     *
     * @param int $page
     * @return mixed
     */
    private function fetchUserCollection($page = 1)
    {
        $response = $this->service
            ->getCollectionItemsByFolder([
                'username' => $this->username,
                'folder_id' => 0,
                'per_page' => 100,
                'page' => $page
            ]);
        $this->releases = array_merge($this->releases, $response['releases']);
        $this->line(sprintf(
            'Fetched page %d / %d from user collection',
            $page,
            $response['pagination']['pages']
        ));

        if ($page < $response['pagination']['pages']) {
            return $this->fetchUserCollection($page + 1);
        }

        return $response;
    }

    /**
     * Store user collection to redis.
     *
     * @return void
     */
    private function storeUserCollection()
    {
        Redis::pipeline(function($pipe) {
            $pipe->del(RedisHash::collection($this->id));

            foreach($this->releases as $release) {
                $pipe->hset(
                    RedisHash::collection($this->id),
                    $release['instance_id'],
                    json_encode($release)
                );
            }
        });
    }
}
