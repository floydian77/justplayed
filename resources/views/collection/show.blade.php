@extends('layouts.app')

@section('navbar_primary_title', 'Collection')

@section('navbar_secondary')
    @include('collection._navbar', ['title' => 'Show'])
@stop

@section('content')
    <h1>
        <a href="">
            {{DiscogsHelper::mergeArtists($release->artists)}} | {{$release->title}}
        </a>
    </h1>

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
            <tr>
                <th>Notes</th>
                <td>{!! nl2br($release->notes) !!}</td>
            </tr>
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
@stop