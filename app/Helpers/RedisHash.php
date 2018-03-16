<?php

namespace App\Helpers;


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
}