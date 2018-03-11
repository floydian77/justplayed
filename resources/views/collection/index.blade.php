@extends('layouts.app')

@section('navbar_primary_title', 'Collection')

@section('navbar_secondary')
    @include('collection._navbar', ['title' => 'Index'])
@stop

@section('content')
    <div>
        <h1>Collection</h1>

        @include('partials._status')

        <h2>Folders</h2>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Count</th>
                    <th>id</th>
                </tr>
                </thead>
                <tbody>
                @foreach($folders as $folder)
                    <tr>
                        <td>{{$folder->name}}</td>
                        <td>{{$folder->count}}</td>
                        <td>{{$folder->id}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <h2>Collection</h2>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>instance_id (this)</th>
                    <th>folder_id</th>
                    <th>id</th>
                </tr>
                </thead>
                <tbody>
                @foreach($collection as $item)
                    <tr>
                        <td>{{$item->instance_id}}</td>
                        <td>{{$item->folder_id}}</td>
                        <td>{{$item->id}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    </div>
@stop