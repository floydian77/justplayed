@extends('layouts.app')

@section('navbar_primary_title', 'Settings')

@section('navbar_secondary')
    @include('settings._navbar', ['title' => 'Main'])
@stop

@section('content')
    <div>
        <h1>Settings</h1>

        @if(session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{route('settings.update')}}" method="post">
            @method('put')
            @csrf

            <h2>Discogs</h2>

            Get a personal access token at <a href="https://www.discogs.com/settings/developers" target="_blank">Discogs
                >>
                Settings >> Developers</a>

            @foreach($keys as $key => $placeholder)
                @unless(strpos($key, 'discogs'))
                    @continue
                @endunless

                <div class="form-group">
                    <input name="{{$key}}" placeholder="{{$placeholder}}" value="{{$settings->get($key)}}"
                           class="form-control" required>
                </div>
            @endforeach

            <div class="form-group">
                <input type="submit" value="Save" class="btn btn-lg btn-primary">
            </div>
        </form>
    </div>
@stop