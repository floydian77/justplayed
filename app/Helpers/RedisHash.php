<?php

namespace App\Helpers;


use Illuminate\Support\Facades\Redis;

class RedisHash
{
    /**
     * Hash name for user collection.
     *
     * @param $id
     * @return string
     */
    public static function collection($id)
    {
        return sprintf(
            "user:%d:discogs:collection",
            $id
        );
    }

    /**
     * Hash name for user collection folders.
     *
     * @param $id
     * @return string
     */
    public static function folders($id)
    {
        return sprintf(
            "user:%d:discogs:folders",
            $id
        );
    }

    /**
     * Hash name for releases.
     *
     * @return string
     */
    public static function releases()
    {
        return "discogs:releases";
    }

    /**
     * Hash name for masters.
     *
     * @return string
     */
    public static function masters()
    {
        return "discogs:masters";
    }

    /**
     * Hash name for artists.
     *
     * @return string
     */
    public static function artists()
    {
        return "discogs:artists";
    }

    /**
     * List with artists that are placeholders, such as Various.
     *
     * @return string
     */
    public static function artistsIgnore()
    {
        return "discogs:artists:ignore";
    }

    /**
     * Get hash from redis and json decode all items.
     *
     * @param $hash
     * @return array
     */
    public static function hgetall($hash)
    {
        $result = Redis::hgetall($hash);
        $data = array();
        foreach ($result as $key => $value) {
            $data[$key] = json_decode($value);
        }

        return $data;
    }

    /**
     * Get key from hash and json decode it.
     *
     * @param $hash
     * @param $key
     * @return mixed
     */
    public static function hget($hash, $key)
    {
        $result = Redis::hget($hash, $key);

        return json_decode($result);
    }
}