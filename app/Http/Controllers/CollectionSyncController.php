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
     * Synchronize collection.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sync()
    {
        $username = SettingsHelper::get(
            sprintf(
                'settings:user:%d:discogs:username',
                Auth::id()
            )
        );

        $this->syncFolders($username);
        $this->syncCollection($username);

        return redirect()
            ->route('collection.index')
            ->with('status', 'Synchronized collection');
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
     * Synchronize collection from Discogs and store in Redis.
     * Response is paginated, when there are more pages it calls it self.
     * Response time could be slow with larger collections, maybe to large.
     *
     * @todo Response time could be to long, nginx could throw an 504 Gateway Time-out error.
     *
     * @param $username
     * @param int $page
     * @return mixed
     */
    private function syncCollection($username, $page = 1)
    {
        $id = Auth::id();
        $hash = "user:$id:discogs:collection";

        if ($page == 1) {
            Redis::del($hash);
        }

        $response = $this->discogsService()
            ->getCollectionItemsByFolder([
                'username' => $username,
                'folder_id' => 0,
                'per_page' => 100,
                'page' => $page
            ]);

        foreach ($response['releases'] as $release) {
            $release = json_decode(json_encode($release));
            $release->_artist = DiscogsHelper::mergeArtists($release->basic_information->artists);

            $json = json_encode($release);

            Redis::hset($hash, $release->instance_id, $json);
        }

        $pages = $response['pagination']['pages'];
        if ($page < $pages) {
            return $this->syncCollection($username, $page + 1);
        }

        return $response;
    }

    /**
     * Get all folders and store them.
     *
     * @param $username
     * @return mixed
     */
    private function syncFolders($username)
    {
        $response = $this->discogsService()
            ->getCollectionFolders([
                'username' => $username
            ]);

        $id = Auth::id();
        $hash = "user:$id:discogs:folders:";

        Redis::del($hash);
        foreach ($response['folders'] as $folder) {
            $json = json_encode($folder);
            Redis::hset($hash, $folder['id'], $json);
        }

        return $response;
    }
}
