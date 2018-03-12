@extends('layouts.app')

@section('navbar_primary_title', 'Settings')

@section('navbar_secondary')
    @include('settings._navbar', ['title' => 'last.fm'])
@stop

@section('content')
    <h1>Last.fm</h1>

    @include('partials._status')

    @if(empty($lastfmUsername))

        <h2>Connect</h2>
        <a href="{{$authUrl}}" class="btn btn-lg btn-warning">
            Connect with last.fm
        </a>

    @else
        <h2>Disconnect</h2>

        <div>
            {{config('app.name')}} is authorized with last.fm for {{$lastfmUsername}}.

            <br>
            <a href="{{route('lastfm.auth.disconnect')}}" class="btn btn-lg btn-danger">
                Disconnect with last.fm
            </a>
        </div>
    @endif
@stop