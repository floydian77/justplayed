<?php

namespace App\Http\Controllers;

use App\Helpers\RedisHash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CollectionController extends Controller
{
    private $userCollection;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $folder_id = intval($request->get('folder'));

        $folders = RedisHash::hgetall(
            RedisHash::folders(Auth::id())
        );
        ksort($folders);

        $this->userCollection = collect(
            RedisHash::hgetall(
                RedisHash::collection(Auth::id())
            )
        );

        $this->filterCollection($folder_id);
        $this->sortCollection();

        $this->userCollection = $this->userCollection
            ->groupBy(function ($item, $key) {
                return substr($item->_artist, 0, 1);
            });

        // Return view.
        return view('collection.index')
            ->with('collection', $this->userCollection)
            ->with('folders', $folders)
            ->with('folder_id', $folder_id);
    }

    private function filterCollection($folder_id)
    {
        // Folder.id 0 All, don't filter
        if ($folder_id == 0) return;

        $this->userCollection = $this->userCollection
            ->filter(function ($item) use ($folder_id) {
                return $item->folder_id == $folder_id;
            });
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

        $tracks = collect();
        foreach ($release->tracklist as $track) {
            $track->scrobbleable = $this->isScrobbleable($track);
            $tracks->push($track);

            if (empty($track->sub_tracks)) continue;
            if (!$track->scrobbleable && $track->type_ == 'index') {
                foreach ($track->sub_tracks as $subtrack) {
                    $subtrack->scrobbleable = true;
                    $tracks->push($subtrack);
                }
            }
        }

        return view('collection.show')
            ->with('release', $release)
            ->with('tracks', $tracks);
    }

    /**
     * Check if a track is srobbleable.
     *
     * @param $track
     * @return bool
     */
    private function isScrobbleable($track)
    {
        // Headings are not scrobbleable.
        if ($track->type_ == 'heading') return false;

        // Tracks are scrobbleable.
        if ($track->type_ == 'track') return true;

        if ($track->type_ == 'index') {
            // Index with a duration is scrobbleable.
            if (!empty($track->duration)) return true;

            // Check if subtracks has duration.
            $hasDuration = false;
            foreach ($track->sub_tracks as $subtrack) {
                if (!empty($subtrack->duration)) {
                    $hasDuration = true;
                    break;
                }
            }

            // Index without duration and subtracks without duration is scrobbleable.
            if (!$hasDuration) return true;

            // Index without duration is not scrobbleable
            if (empty($track->duration)) return false;
        }

        return false;
    }
}
