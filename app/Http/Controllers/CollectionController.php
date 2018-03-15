<?php

namespace App\Http\Controllers;

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
        $folders = $this->getFolders();
        $collection = $this->getCollection();

        // Return view.
        return view('collection.index')
            ->with('folders', $folders)
            ->with('collection', $collection);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $release = $this->getRelease($id);

        return view('collection.show')
            ->with('release', $release);
    }

    /**
     * Get collection from redis and sort it.
     *
     * @return \Illuminate\Support\Collection|static
     */
    private function getCollection()
    {
        $id = Auth::id();

        // Get collection and put decoded json in a collection.
        $collectionHashName = "user:$id:discogs:collection";
        $_collection = Redis::hgetall($collectionHashName);
        $collection = collect();
        foreach ($_collection as $release) {
            $release = json_decode($release);
            $collection->put($release->id, $release);
        }

        // Sort on artist, year.
        $collection = $collection->sort(function ($a, $b) {
            if ($a->_artist === $b->_artist) {
                return $a->basic_information->year > $b->basic_information->year;
            }
            return $a->_artist > $b->_artist;
        });

        return $collection;
    }

    /**
     * Get folders from redis and sort it.
     *
     * @return \Illuminate\Support\Collection
     */
    private function getFolders()
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

        return $folders;
    }

    /**
     * Get release from redis.
     *
     * @param $id
     * @return mixed
     */
    private function getRelease($id)
    {
        $release = json_decode(Redis::hget(
            'discogs:releases',
            $id)
        );

        return $release;
    }
}
