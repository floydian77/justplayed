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
        $settings = collect();
        foreach ($this->keys() as $key => $value) {
            $settings = $settings->merge($this->get($key));
        }

        return view('settings.edit')
            ->with('keys', $this->keys())
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
        foreach (array_keys($this->keys()) as $key) {
            $this->set($key, $request->get($key));
        }

        return redirect()
            ->route('settings.edit')
            ->with('status', 'Settings successfully updated.');
    }

    /**
     * Generate keys foreach setting.
     *
     * @return array
     */
    private function keys()
    {
        $id = Auth::id();

        return [
            "settings:user:$id:discogs:username" => 'Discogs username',
            "settings:user:$id:discogs:token" => 'Personal access token'
        ];
    }


    /**
     * Get decrypted $value of $key.
     *
     * @param $key
     * @return array
     */
    private function get($key)
    {
        $value = decrypt(Redis::get($key));
        return [$key => $value];
    }

    /**
     * Encrypt $value and set.
     *
     * @param $key
     * @param $value
     * @return mixed
     */
    private function set($key, $value)
    {
        $value = encrypt($value);
        return Redis::set($key, $value);
    }
}
