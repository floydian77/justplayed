<?php

namespace App\Console\Commands\Discogs;


use App\Helpers\DiscogsHelper;
use App\Helpers\RedisHash;
use Illuminate\Support\Facades\Redis;

class ProcessUserCollection extends DiscogsCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discogs:post-process {--user=1 : User id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Post process user collection';

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

        $collection = $this->decode(
            Redis::hgetall(RedisHash::collection($this->id))
        );
        $releases = $this->decode(
            Redis::hgetall(RedisHash::releases())
        );
        $masters = $this->decode(
            Redis::hgetall(RedisHash::masters())
        );

        foreach($collection as $key => $item) {
            // Add _artist
            $item->_artist = DiscogsHelper::mergeArtists(
                $item->basic_information->artists
            );

            // Add _year_master
            $release = $releases[$item->id];
            if (!empty($release->master_id)) {
                $master = $masters[$release->master_id];
                $item->_year_master = $master->year;
            }
            else {
                $item->_year_master = 0;
            }

            $collection[$key] = $item;
        }

        Redis::pipeline(function($pipe) use ($collection) {
            foreach($collection as $item) {
                $pipe->hset(
                    RedisHash::collection($this->id),
                    $item->instance_id,
                    json_encode($item)
                );
            }

        });
    }

    /**
     * Loop through array and json decode each element.
     *
     * @param $array
     * @return array
     */
    private function decode($array)
    {
        $_data = array();
        foreach($array as $key => $value) {
            $_data[$key] = json_decode($value);
        }

        return $_data;
    }
}
