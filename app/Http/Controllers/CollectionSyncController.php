<?php

namespace App\Http\Controllers;

use App\Helpers\DiscogsHelper;
use App\Helpers\SettingsHelper;
use Discogs\ClientFactory;
use Discogs\Subscriber\ThrottleSubscriber;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class CollectionSyncController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('collection.sync.index');
    }

    /**
     * Fetch release from Discogs and store in redis.
     *
     * @param $id
     * @return mixed
     */
    public function fetchRelease($id)
    {
        // Fetch release from discogs
        $response = $this->discogsService()
            ->getRelease([
                'id' => $id
            ]);

        $release = json_encode($response);
        $key = sprintf(
            "release:%d",
            $response['id']
        );

        Redis::set($key, $release);
        return redirect()
            ->route('collection.show', $id)
            ->with('status', 'Fetched release successfully.');
    }

    /**
     * Initialize Discogs service.
     *
     * @return \GuzzleHttp\Command\Guzzle\GuzzleClient
     */
    private function discogsService()
    {
        $token = SettingsHelper::get(
            sprintf(
                'settings:user:%d:discogs:token',
                Auth::id()
            )
        );
        $client = DiscogsHelper::discogsService($token);

        return $client;
    }
}
