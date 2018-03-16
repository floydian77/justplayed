<?php

namespace App\Http\Controllers;

use App\Helpers\RedisHash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class CollectionController extends Controller
{
    private $userCollection;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $folders = $this->getFolders();
        $this->getCollection();
        $this->sortCollection();

        // Return view.
        return view('collection.index')
            ->with('folders', $folders)
            ->with('collection', $this->userCollection);
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
        // Get collection and put decoded json in a collection.
        $_collection = Redis::hgetall(
            RedisHash::collection(Auth::id())
        );
        $collection = collect();
        foreach ($_collection as $release) {
            $release = json_decode($release);
            $collection->put($release->id, $release);
        }

        $this->userCollection = $collection;
    }

    private function sortCollection()
    {
        // Sort on artist, year.
        $this->userCollection = $this->userCollection->sort(function ($a, $b) {
            if ($a->_artist === $b->_artist) {
                if ($a->_year_master == $b->_year_master) {
                    return $a->basic_information->year > $b->basic_information->year;
                }
                return $a->_year_master > $b->_year_master;
            }
            return $a->_artist > $b->_artist;
        });
    }

    /**
     * Get folders from redis and sort it.
     *
     * @return \Illuminate\Support\Collection
     */
    private function getFolders()
    {
        // Get folders and put decoded json in a collection.
        $_folders = Redis::hgetall(
            RedisHash::folders(Auth::id())
        );
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
            RedisHash::releases(),
            $id)
        );

        return $release;
    }
}
