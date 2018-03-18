@extends('layouts.app')

@section('navbar_primary_title', 'Collection')

@section('navbar_secondary')
    @include('collection._navbar', ['title' => 'Index'])
@stop

@section('navbar_form')
    <form class="form-inline my-2 my-lg-0">
        <select id="folder_id" class="form-control mr-sm-2">
            @foreach($folders as $folder)
                <option value="{{$folder->id}}" {{ $folder_id == $folder->id ? 'selected' : '' }}>
                    {{$folder->name}}
                </option>
            @endforeach
        </select>
    </form>
@stop

@section('content')
    <div>
        <h1>Folder: {{$folders[$folder_id]->name}} ({{$folders[$folder_id]->count}})</h1>

        @include('partials._status')

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Artist</th>
                    <th>Title</th>
                    <th>Released</th>
                    <th>Rereleased</th>
                    <th>Folder</th>
                </tr>
                </thead>
                <tbody>
                @foreach($collection as $key => $group)
                    <tr>
                        <td colspan="5" class="text-center"><h2>{{$key}}</h2></td>
                    </tr>
                    @foreach($group as $release)
                        <tr>
                            <td>{{$release->_artist}}</td>
                            <td>
                                <a href="{{route('collection.show', $release->id)}}">
                                    {{$release->basic_information->title}}
                                </a>
                            </td>
                            <td>{{$release->_year_master}}</td>
                            <td>{{$release->basic_information->year}}</td>
                            <td>{{$folders[$release->folder_id]->name}}</td>
                        </tr>
                    @endforeach
                @endforeach
                </tbody>
            </table>
        </div>

    </div>
@stop

@section('scripts')
    <script>
        $('#folder_id').on("change", (e) => {
            $(location).attr('href', "?folder=".concat($('#folder_id').val()));
        });
    </script>
@stop