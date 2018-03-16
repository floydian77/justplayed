@extends('layouts.app')

@section('navbar_primary_title', 'Collection')

@section('navbar_secondary')
    @include('collection._navbar', ['title' => 'Index'])
@stop

@section('content')
    <div>
        <h1>Collection</h1>

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
                @foreach($collection as $release)
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
                </tbody>
            </table>
        </div>

    </div>
@stop