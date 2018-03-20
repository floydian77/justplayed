<?php

namespace App\Http\Controllers;


use App\Helpers\DiscogsHelper;
use App\Helpers\SettingsHelper;
use App\Http\Requests\ScrobbleRequest;
use App\Models\ScrobbleQueue;
use Illuminate\Support\Facades\Auth;
use LastFmApi\Api\AuthApi;
use LastFmApi\Api\TrackApi;
use LastFmApi\Exception\InvalidArgumentException;

class ScrobbleController extends Controller
{
    /**
     * Scrobble or queue tracks.
     *
     * @param ScrobbleRequest $request
     * @param $release
     * @return \Illuminate\Http\RedirectResponse|void
     * @throws \LastFmApi\Exception\NotAuthenticatedException
     */
    public function index(ScrobbleRequest $request, $release)
    {
        $tracks = collect($request->get('track'));

        if ($request->get('submit') == 'Scrobble') {
            return $this->scrobble($tracks->reverse());
        }

        if ($request->get('submit') == 'Queue') {
            return $this->queue($tracks);
        }
    }

    /**
     * Show queue.
     *
     * @return $this
     */
    public function showQueue()
    {
        $queue = ScrobbleQueue::all();

        return view('queue.index')
            ->with('queue', $queue);
    }

    /**
     * Clear queue.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clearQueue()
    {
        ScrobbleQueue::clear();

        return redirect()
            ->back()
            ->with('status', 'Queue cleared');
    }

    /**
     * Scrobble queue.
     *
     * @todo clear queue.
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \LastFmApi\Exception\NotAuthenticatedException
     */
    public function scrobbleQueue()
    {
        $queue = ScrobbleQueue::all(true);

        return $this->scrobble($queue);
    }

    /**
     * Add tracks to queue.
     *
     * @param $tracks
     * @return \Illuminate\Http\RedirectResponse
     */
    private function queue($tracks)
    {
        ScrobbleQueue::add($tracks);

        return redirect()
            ->back()
            ->with('status', sprintf(
                "%d tracks added to the queue",
                count($tracks)
            ));
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