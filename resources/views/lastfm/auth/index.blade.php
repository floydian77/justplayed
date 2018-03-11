@extends('layouts.app')

@section('navbar_primary_title', 'Settings')

@section('navbar_secondary')
    @include('settings._navbar', ['title' => 'last.fm'])
@stop

@section('content')
    <h1>Connect with last.fm</h1>

    @include('partials._status')

    <a href="{{$authUrl}}" class="btn btn-lg btn-warning">
        Connect with last.fm
    </a>
@stop