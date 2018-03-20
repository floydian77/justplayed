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
        @if(count($errors->all()))
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
        @endif

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

        <div id='tracklist' class="table-responsive">
            <form action="{{route('lastfm.scrobble', $release->id)}}" method="post">
                @csrf
                <table class="table table-striped">
                    @foreach($tracks as $pos => $track)
                        <tr class="{{$track->type_ == "heading" ? "track-heading" : ''}} {{$track->type_ == "index" ? "track-index" : ''}}">
                            <td>
                                @if(!empty($track->scrobbleable))
                                    <input type="checkbox" name="track[{{$pos}}][played]" checked>
                                    <input type="hidden" name="track[{{$pos}}][artist]"
                                           value="{{DiscogsHelper::mergeArtists(empty($track->artists) ? $release->artists : $track->artists)}}">
                                    <input type="hidden" name="track[{{$pos}}][album]" value="{{$release->title}}">
                                    <input type="hidden" name="track[{{$pos}}][position]" value="{{$track->position}}">
                                    <input type="hidden" name="track[{{$pos}}][track]" value="{{$track->title}}">
                                    <input type="hidden" name="track[{{$pos}}][duration]" value="{{$track->duration}}">
                                @endif
                            </td>
                            <td>
                                {{$track->position}}
                            </td>
                            <td>
                                @if($track->scrobbleable)
                                    {{DiscogsHelper::mergeArtists(empty($track->artists) ? $release->artists : $track->artists)}}
                                @endif
                            </td>
                            <td>
                                {{$track->title}}
                            </td>
                            <td>
                                {{$track->duration}}
                            </td>
                        </tr>
                    @endforeach
                </table>
                <input type="submit" name="submit" value="Scrobble" class="btn btn-lg btn-danger">
                <input type="submit" name="submit" value="Queue" class="btn btn-lg btn-primary">
            </form>
        </div>
    @else
        <div class="alert alert-danger">
            Release not found.
        </div>
    @endunless
@stop

@section('scripts')
    <script>
        $('#tracklist').on('click', '.track-heading', (e) => {
            var tracks = $(e.target.parentElement).nextUntil(".track-heading");
            $.each(tracks, (index, track) => {
                var box = $($(tracks[index]).children()[0]).children()[0];
                var checked = $(box).prop('checked');
                $(box).prop('checked', !checked);
            });
        });
    </script>
@stop