<?php

namespace App\Helpers;


class DiscogsHelper
{
    /**
     * Merge all artists from a release into one string and clean up name,
     * eg 'Epica (2)' => 'Epica'
     *
     * @param $artists
     * @return string
     */
    public static function mergeArtists($artists)
    {
        $_artists = array();
        foreach ($artists as $artist) {
            $name = $artist->name;
            if (strrpos($name, ' (')) {
                $name = trim(substr($name, 0, strrpos($name, ' (')));
            }
            array_push($_artists, $name);
        }

        return implode(', ', $_artists);
    }
}