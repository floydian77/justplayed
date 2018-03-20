<?php

namespace App\Models;


use Illuminate\Support\Facades\Redis;

class ScrobbleQueue
{
    private const signature = 'lastfm:queue';

    /**
     * Add tracks to the queue.
     *
     * @param $tracks
     * @return mixed
     */
    public static function add($tracks)
    {
        return Redis::pipeline(function($pipe) use ($tracks) {
            foreach ($tracks as $track) {
                if (!array_key_exists('played', $track)) continue;

                $pipe->lpush(
                    self::signature,
                    json_encode($track)
                );
            }
        });
    }

    /**
     * Get all tracks in queue.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function all($assoc = false)
    {
        $_queue = Redis::lrange(
            self::signature,
            0,
            -1
        );

        $queue = collect();
        foreach ($_queue as $track) {
            $queue->push(json_decode($track, $assoc));
        }

        return $queue;
    }

    /**
     * Clear queue.
     *
     * @return mixed
     */
    public static function clear()
    {
        return Redis::del(self::signature);
    }
}