<?php

namespace App\Console\Commands\Discogs;


use Illuminate\Support\Facades\Redis;

class FetchMasters extends DiscogsCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discogs:fetch-masters {--user=1 : User id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch all master releases';

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

        $this->info("Fetching all masters\n");

        $ids = $this->fetchIds();
        $total = count($ids);

        if($total == 0) {
            $this->line('No masters to fetch.');
            return;
        }

        $n = 1;
        foreach($ids as $id) {
            $this->line(sprintf(
                "Fetching master:%d (%d / %d)",
                $id, $n, $total
            ));

            $master = $this->fetchMaster($id);
            $this->storeMaster($master);

            $n++;
        }
    }

    private function fetchMaster($id)
    {
        $master = $this->service
            ->getMaster([
                'id' => $id
            ]);

        $this->line(sprintf(
            "Fetched master %d: %s\n",
            $id,
            $master['title']
        ));

        return $master;
    }

    private function storeMaster($master)
    {
        Redis::hset(
            'discogs:masters',
            $master['id'],
            json_encode($master)
        );
    }

    /**
     * Get ids of masters that not have been fetched yet.
     *
     * @return array
     */
    private function fetchIds()
    {
        // Get all master ids from releases
        $releases = Redis::hgetall('discogs:releases');
        $releaseIds = array();
        foreach($releases as $release) {
            $release = json_decode($release);
            if (empty($release->master_id)) continue;
            array_push($releaseIds, $release->master_id);
        }
        $releaseIds = array_unique($releaseIds);

        // Get all ids from stored masters
        $masterIds = Redis::hkeys('discogs:masters');

        $fetchIds = array_diff($releaseIds, $masterIds);

        return $fetchIds;
    }
}
