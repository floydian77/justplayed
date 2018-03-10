<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class SettingsController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $settings = collect(Redis::hgetall($this->hashName()));

        return view('settings.edit')
            ->with('settings', $settings);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        Redis::hset($this->hashName(), 'discogs_user', $request->get('discogs_user'));
        Redis::hset($this->hashName(), 'discogs_token', $request->get('discogs_token'));

        return redirect()
            ->route('settings.edit')
            ->with('status', 'Settings successfully updated.');
    }

    /**
     * @return string
     */
    private function hashName()
    {
        return sprintf("settings:user:%d", Auth::id());
    }
}
