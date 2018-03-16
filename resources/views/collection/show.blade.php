@extends('layouts.app')

@section('navbar_primary_title', 'Collection')

@section('navbar_secondary')
    @include('collection._navbar', ['title' => 'Show'])
@stop

@section('content')
    @unless(empty($release))
        <h1>
            <a href="">
                {{DiscogsHelper::mergeArtists($release->artists)}} | {{$release->title}}
            </a>
        </h1>

        @include('partials._status');

        <h2>Details</h2>

        <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <th>Labels</th>
                    <td>{{DiscogsHelper::mergeLabels($release->labels)}}</td>
                </tr>
                <tr>
                    <th>Formats</th>
                    <td>{{DiscogsHelper::mergeFormats($release->formats)}}</td>
                </tr>
                <tr>
                    <th>Released</th>
                    <td>{{$release->year}}</td>
                </tr>
                <tr>
                    <th>Discogs</th>
                    <td>
                        <a href="{{$release->uri}}" target="_blank">r{{$release->id}}</a>
                    </td>
                </tr>
                @unless(empty($release->notes))
                    <tr>
                        <th>Notes</th>
                        <td>{!! nl2br($release->notes) !!}</td>
                    </tr>
                @endunless
            </table>
        </div>

        <h2>Tracks</h2>

        <div class="table-responsive">
            <table class="table table-striped">
                @foreach($release->tracklist as $track)
                    <tr>
                        <td>{{$track->position}}</td>
                        <td>{{$track->title}}</td>
                        <td>{{$track->duration}}</td>
                    </tr>
                @endforeach
            </table>
        </div>

        <div>
            <form action="{{route('lastfm.scrobble', $release->id)}}" method="post">
                @csrf

                <input type="submit" value="Scrobble" class="btn btn-lg btn-danger">
            </form>
        </div>
    @else
        <div class="alert alert-danger">
            Release not found.
        </div>
    @endunless
@stop