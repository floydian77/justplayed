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
                    <th>Year</th>
                    <th>Folder</th>
                </tr>
                </thead>
                <tbody>
                @foreach($collection as $item)
                    <tr>
                        <td>{{$item->basic_information->artists[0]->name}}</td>
                        <td>{{$item->basic_information->title}}</td>
                        <td>{{$item->basic_information->year}}</td>
                        <td>{{$folders[$item->folder_id]->name}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    </div>
@stop