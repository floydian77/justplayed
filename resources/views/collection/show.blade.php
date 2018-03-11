@extends('layouts.app')

@section('navbar_primary_title', 'Collection')

@section('navbar_secondary')
    @include('collection._navbar', ['title' => 'Show'])
@stop

@section('content')
    <h1>Release</h1>

    @dump($release)
@stop