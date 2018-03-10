@extends('layouts.app')

@section('content')
    <div>
        <h1>Settings</h1>

        @if(session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <h2>Discogs</h2>

        Get a personal access token at <a href="https://www.discogs.com/settings/developers" target="_blank">Discogs >>
            Settings >> Developers</a>

        <form action="{{route('settings.update')}}" method="post">
            @method('put')
            @csrf

            <div class="form-group">
                <input name="discogs_user" placeholder="Discogs username" value="{{$settings->get('discogs_user')}}"
                       class="form-control" required>
            </div>

            <div class="form-group">
                <input name="discogs_token" placeholder="Personal access token"
                       value="{{$settings->get('discogs_token')}}" class="form-control" required>
            </div>

            <div class="form-group">
                <input type="submit" value="Save" class="btn btn-lg btn-primary">
            </div>
        </form>
    </div>
@stop