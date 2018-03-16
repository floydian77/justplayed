<?php

namespace App\Helpers;


use Discogs\ClientFactory;
use Discogs\Subscriber\ThrottleSubscriber;

class DiscogsHelper
{
    const DEFAULT_TRACK_DURATION = "3:00";

    /**
     * Initialize Discogs service.
     *
     * @param $token Discogs user personal access token
     * @return \GuzzleHttp\Command\Guzzle\GuzzleClient
     */
    public static function discogsService($token)
    {
        $client = ClientFactory::factory([
            'defaults' => [
                'headers' => [
                    'User-Agent' => 'justplayed/0.0 +https://github.com/floydian77/justplayed'
                ],
                'query' => [
                    'token' => $token
                ]
            ]
        ]);
        $client->getHttpClient()->getEmitter()->attach(new ThrottleSubscriber());
        return $client;
    }

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

    /**
     * Convert duration string to seconds.
     *
     * @example 4:30 => 270
     *
     * @param $duration
     * @return false|int
     */
    public static function durationToSeconds($duration = self::DEFAULT_TRACK_DURATION)
    {
        // Add hour to string when duration is less then a hour.
        if (substr_count($duration, ':') == 1) {
            $duration = "0:$duration";
        }
        $seconds = strtotime(
            sprintf(
                "1970-01-01 %s UTC",
                $duration
            )
        );

        return $seconds;
    }
}