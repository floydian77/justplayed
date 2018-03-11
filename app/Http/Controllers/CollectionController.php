<?php

namespace App\Http\Controllers;

use App\Helpers\SettingsHelper;
use Discogs\ClientFactory;
use Discogs\Subscriber\ThrottleSubscriber;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class CollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $id = Auth::id();

        // Get folders and put decoded json in a collection.
        $folderHashName = "user:$id:discogs:folders:";
        $_folders = Redis::hgetall($folderHashName);
        $folders = collect();
        foreach ($_folders as $folder) {
            $folder = json_decode($folder);
            $folders->put($folder->id, $folder);
        }

        // Get collection and put decoded json in a collection.
        $collectionHashName = "user:$id:discogs:collection";
        $_collection = Redis::hgetall($collectionHashName);
        $collection = collect();
        foreach ($_collection as $release) {
            $release = json_decode($release);
            $release->_artist = $this->mergeArtists($release->basic_information->artists);
            $collection->put($release->id, $release);
        }

        // Sort on artist, year.
        $collection = $collection->sort(function ($a, $b) {
            if ($a->_artist === $b->_artist) {
                return $a->basic_information->year > $b->basic_information->year;
            }
            return $a->_artist > $b->_artist;
        });

        // Return view.
        return view('collection.index')
            ->with('folders', $folders)
            ->with('collection', $collection);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    public function showSyncForm()
    {
        return view('collection.sync');
    }

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
     * Merge all artists from a release into one string and clean up name,
     * eg 'Epica (2)' => 'Epica'
     *
     * @param $artists
     * @return string
     */
    private function mergeArtists($artists)
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

    private function syncCollection($username, $page = 1)
    {
        $id = Auth::id();
        $hash = "user:$id:discogs:collection";

        if ($page == 1) {
            Redis::del($hash);
        }

        $response = $this->discogsClient()
            ->getCollectionItemsByFolder([
                'username' => $username,
                'folder_id' => 0,
                'per_page' => 100,
                'page' => $page
            ]);

        foreach ($response['releases'] as $release) {
            $json = json_encode($release);
            $json->_artist = $this->mergeArtists($json->basic_information->artists);
            Redis::hset($hash, $release['instance_id'], $json);
        }

        $pages = $response['pagination']['pages'];
        if ($page < $pages) {
            return $this->syncCollection($username, $page + 1);
        }

        return $response;
    }

    private function syncFolders($username)
    {
        $response = $this->discogsClient()
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

    private function discogsClient()
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
}
