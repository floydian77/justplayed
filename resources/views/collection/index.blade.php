@extends('layouts.app')

@section('navbar_primary_title', 'Collection')

@section('navbar_secondary')
    @include('collection._navbar', ['title' => 'Index'])
@stop

@section('content')
    <div class="starter-template">
        <h1>Collection</h1>
        <p class="lead">
            Collection ...
        </p>
    </div>
@stop