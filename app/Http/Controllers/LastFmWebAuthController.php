<?php

namespace App\Http\Controllers;


use App\Helpers\SettingsHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use LastFmApi\Api\AuthApi;
use LastFmApi\Exception\InvalidArgumentException;

class LastFmWebAuthController extends Controller
{
    /**
     * Show last.fm settings, connect or disconnect.
     *
     * @return $this
     */
    public function index()
    {
        $authUrl = sprintf(
            "http://www.last.fm/api/auth/?api_key=%s&cb=%s",
            env('LASTFM_API_KEY'),
            route('lastfm.auth.connect')
        );

        $id = Auth::id();
        $lastfmUsername = SettingsHelper::get("settings:user:$id:lastfm:username");

        return view('lastfm.auth.index')
            ->with('authUrl', $authUrl)
            ->with('lastfmUsername', $lastfmUsername);
    }

    public function disconnect()
    {
        $id = Auth::id();
        SettingsHelper::del(
            "settings:user:$id:lastfm:username",
            "settings:user:$id:lastfm:sessionToken",
            "settings:user:$id:lastfm:subscriber"
        );

        return redirect()
            ->route('lastfm.auth.index')
            ->with('warning', sprintf(
                "Removed last.fm session. However you need still disconnect %s from last.fm %s",
                config('app.name'),
                "https://www.last.fm/settings/applications"
            ));
    }

    /**
     * Try to get session key and store it in Redis store.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function connect(Request $request)
    {
        $token = $request->get('token');

        try {
            $response = new AuthApi(
                'getsession', [
                    'apiKey' => env('LASTFM_API_KEY'),
                    'apiSecret' => env('LASTFM_SECRET'),
                    'token' => $token
                ]
            );

            $id = Auth::id();

            SettingsHelper::set("settings:user:$id:lastfm:username", $response->username);
            SettingsHelper::set("settings:user:$id:lastfm:sessionKey", $response->sessionKey);
            SettingsHelper::set("settings:user:$id:lastfm:subscriber", $response->subscriber);

            return redirect()
                ->route('lastfm.auth.index')
                ->with('status', sprintf(
                    "Hi %s, thanks for authorizing %s.",
                    $response->username,
                    config('app.name')
                ));

        } catch (InvalidArgumentException $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
}