<?php

namespace App\Console\Commands\Discogs;


use App\Helpers\DiscogsHelper;
use Illuminate\Support\Facades\Redis;

class FetchReleases extends DiscogsCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discogs:fetch-releases {--user=1 : User id}';

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
        parent::handle();

        $this->info("Fetching all releases\n");

        $ids = $this->fetchIds();
        $total = count($ids);

        if($total == 0) {
            $this->line('No releases to fetch.');
            return;
        }

        $n = 1;
        foreach($ids as $id) {
            $this->line(sprintf(
                "Fetching release:%d (%d / %d)",
                $id, $n, $total
            ));

            $release = $this->fetchRelease($id);
            $this->storeRelease($release);

            $n++;
        }
    }

    /**
     * Fetch release from discogs.
     *
     * @param $id
     * @return mixed
     */
    private function fetchRelease($id)
    {
        $release = $this->service
            ->getRelease([
                'id' => $id
            ]);

        $this->line(sprintf(
            "Fetched release %d: %s\n",
            $id,
            $release['title']
        ));

        return $release;
    }

    /**
     * Store release in redis.
     *
     * @param $release
     */
    private function  storeRelease($release)
    {
        Redis::hset(
            'discogs:releases',
            $release->id,
            json_encode($release)
        );
    }

    /**
     * Get ids of releases that not have been fetched yet.
     *
     * @return array
     */
    private function fetchIds()
    {
        // Get ids of all releases in user collection.
        $userCollectionHashName = "user:$this->id:discogs:collection";
        $collection = Redis::hgetall($userCollectionHashName);
        $collectionIds = array();
        foreach($collection as $item) {
            $release = json_decode($item);
            array_push($collectionIds, $release->id);
        }

        // Get ids of all fetched releases
        $releasesHashName = "discogs:releases";
        $releaseIds = Redis::hkeys($releasesHashName);

        // Get ids of releases to fetch
        $fetchIds = array_diff($collectionIds, $releaseIds);

        return $fetchIds;
    }
}
