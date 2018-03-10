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
        $hash = "user:$id:discogs:folders:";
        $_folders = Redis::hgetall($hash);

        $folders = collect();
        foreach ($_folders as $folder) {
            $folder = json_decode($folder);
            $folders = $folders->merge([$folder]);
        }

        return view('collection.index')
            ->with('folders', $folders);
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

        return redirect()
            ->route('collection.index')
            ->with('status', 'Synchronized collection');
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
