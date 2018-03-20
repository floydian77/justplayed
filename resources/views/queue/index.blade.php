@extends('layouts.app')

@section('navbar_primary_title', 'Queue')

@section('content')
    <h1>Queue ({{count($queue)}})</h1>

    @include('partials._status')

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Artist</th>
                <th>Album</th>
                <th>Position</th>
                <th>Track</th>
                <th>Duration</th>
            </tr>
            </thead>
            <tbody>
            @foreach($queue as $track)
                <tr>
                    <td>{{$track->artist}}</td>
                    <td>{{$track->album}}</td>
                    <td>{{$track->position}}</td>
                    <td>{{$track->track}}</td>
                    <td>{{$track->duration}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <a href="{{route('lastfm.scrobble.queue.clear')}}" class="btn btn-lg btn-danger">Clear</a>
@stop
