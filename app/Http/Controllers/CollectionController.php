<?php

namespace App\Http\Controllers;

use App\Helpers\RedisHash;
use Illuminate\Support\Facades\Auth;

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
        $folders = RedisHash::hgetall(
            RedisHash::folders(Auth::id())
        );
        $this->userCollection = collect(
            RedisHash::hgetall(
                RedisHash::collection(Auth::id())
            )
        );
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
        $release = RedisHash::hget(
            RedisHash::releases(),
            $id
        );

        return view('collection.show')
            ->with('release', $release);
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
}
