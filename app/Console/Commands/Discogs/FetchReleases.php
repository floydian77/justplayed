<?php

namespace App\Console\Commands\Discogs;


use Illuminate\Support\Facades\Redis;

class FetchReleases extends DiscogsCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discogs:fetch-releases {id=1}';

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

        $this->info("Fetching all releases");

        $ids = $this->fetchIds();
        $total = count($ids);
        foreach($ids as $key => $id) {
            $this->line(sprintf(
                "Fetching release:%d (%d / %d)",
                $id, $key+1, $total
            ));
            $this->call('discogs:fetch-release', [
                'id' => $id
            ]);
        }

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