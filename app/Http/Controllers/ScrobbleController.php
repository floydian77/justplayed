<?php

namespace App\Http\Controllers;


use App\Helpers\DiscogsHelper;
use App\Helpers\SettingsHelper;
use App\Http\Requests\ScrobbleRequest;
use Illuminate\Support\Facades\Auth;
use LastFmApi\Api\AuthApi;
use LastFmApi\Api\TrackApi;
use LastFmApi\Exception\InvalidArgumentException;

class ScrobbleController extends Controller
{
    public function index(ScrobbleRequest $request, $release)
    {
        if ($request->get('submit') == 'Scrobble') {
            return $this->scrobble(collect($request->get('track')));
        }

        if ($request->get('submit') == 'Queue') {
            return $this->queue();
        }
    }

    private function queue()
    {
        dd('should queue');
    }

    /**
     * Scrobble tracks.
     *
     * @param $tracks
     * @return \Illuminate\Http\RedirectResponse
     * @throws \LastFmApi\Exception\NotAuthenticatedException
     */
    private function scrobble($tracks)
    {
        // Scrobble params
        $tracks = $tracks->reverse();
        $_params = array();
        $timestamp = time();
        foreach ($tracks as $track) {
            if (!array_key_exists('played', $track)) continue;

            $timestamp -= DiscogsHelper::durationToSeconds($track['duration']);

            $_track = array();
            $_track['artist'] = $track['artist'];
            $_track['album'] = $track['album'];
            $_track['position'] = $track['position'];
            $_track['track'] = $track['track'];
            $_track['timestamp'] = $timestamp;

            array_push($_params, $_track);
        }
        $_params = array_reverse($_params);

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
    }
}