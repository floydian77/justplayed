<?php

namespace App\Console\Commands\Discogs;


use App\Helpers\RedisHash;
use GuzzleHttp\Command\Exception\CommandException;
use Illuminate\Support\Facades\Redis;

class FetchArtists extends DiscogsCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discogs:fetch-artists {--user=1 : User id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch all artists';

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
        $this->info("Fetching all artists\n");

        $ids = $this->fetchIds();

        $total = count($ids);
        if ($total == 0) {
            $this->line('No artists to fetch');
        }

        $n = 1;
        foreach ($ids as $id) {
            $this->line(sprintf(
                "Fetching artist:%d (%d / %d)",
                $id,
                $n,
                $total
            ));

            // Fetch release
            $artist = $this->fetchArtist($id);
            // Store it
            $this->storeArtist($artist);

            $n++;
        }
    }

    /**
     * Fetch artist from discogs for given $id.
     *
     * @todo use list instead of hash.
     *
     * @param $id
     * @return mixed
     */
    private function fetchArtist($id)
    {
        $artist = null;

        try {
            $artist = $this->service
                ->getArtist([
                    'id' => $id
                ]);

            $this->line(sprintf(
                "Fetched artist:%d %s\n",
                $id,
                $artist['name']
            ));
        }
        catch(CommandException $e) {
            $this->line("Ignored artist:$id");

            Redis::hset(
                RedisHash::artistsIgnore(),
                $id,
                null
            );
        }

        return $artist;
    }

    /**
     * Store artist in redis.
     *
     * @param $artist
     */
    private function storeArtist($artist)
    {
        Redis::hset(
            RedisHash::artists(),
            $artist['id'],
            json_encode($artist)
        );
    }

    /**
     * Get all artist ids that needs to be fetched.
     *
     * @return array
     */
    private function fetchIds()
    {
        $releases = RedisHash::hgetall(
            RedisHash::releases()
        );

        $all = array();
        foreach($releases as $release) {
            foreach ($release->artists as $artist) {
                array_push($all, $artist->id);
            }
        }
        $all = array_unique($all);

        $have = Redis::hkeys(RedisHash::artists());
        $ignore = Redis::hkeys(RedisHash::artistsIgnore());

        $need = array_diff($all, array_merge($have, $ignore));

        return $need;
    }
}
