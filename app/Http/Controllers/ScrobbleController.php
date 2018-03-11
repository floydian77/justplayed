<?php

namespace App\Http\Controllers;


use App\Helpers\DiscogsHelper;
use App\Helpers\SettingsHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use LastFmApi\Api\AuthApi;
use LastFmApi\Api\TrackApi;
use LastFmApi\Exception\InvalidArgumentException;

class ScrobbleController extends Controller
{
    public function scrobble(Request $request, $release)
    {
        $key = sprintf(
            "release:%d",
            $release
        );
        $release = json_decode(Redis::get($key));

        $artist = DiscogsHelper::mergeArtists($release->artists);
        $tracks = collect($release->tracklist);

        // Add timestamps
        $tracks = $tracks->reverse();
        $timestamp = time();
        foreach ($tracks as $track) {
            if ($track->type_ != "track") continue;

            $timestamp -= DiscogsHelper::durationToSeconds($track->duration);
            $track->timestamp = $timestamp;
        }
        $tracks = $tracks->reverse();

        // Scrobble params
        $_params = array();
        foreach ($tracks as $track) {
            if ($track->type_ != "track") continue;

            $_track = array();
            $_track['artist'] = $artist;
            $_track['position'] = $track->position;
            $_track['track'] = $track->title;
            $_track['timestamp'] = $track->timestamp;

            array_push($_params, $_track);
        }

        $id = Auth::id();
        try {
            $session = new AuthApi(
                'setsession', [
                    'apiKey' => env('LASTFM_API_KEY'),
                    'apiSecret' => env('LASTFM_SECRET'),
                    'sessionKey' => SettingsHelper::get("settings:user:$id:lastfm:sessionKey"),
                    'username' => SettingsHelper::get("settings:user:$id:lastfm:username"),
                    'subscriber' => SettingsHelper::get("settings:user:$id:lastfm:subscriber")
                ]
            );

            $trackApi = new TrackApi($session);
            $response = $trackApi->scrobble($_params);

            if ($response) {
                return redirect()
                    ->back()
                    ->with('status', 'Scrobbled successfully.');
            }
            return redirect()
                ->back()
                ->with('error', 'Failed to scrobble?');
        } catch (InvalidArgumentException $e) {
            dd($e);
        }

        dd($_params);
    }
}