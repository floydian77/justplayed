@extends('layouts.app')

@section('navbar_primary_title', 'Collection')

@section('navbar_secondary')
    @include('collection._navbar', ['title' => 'Synchronize'])
@stop

@section('content')
    <div>
        <h1>Synchronize</h1>
    </div>
@stop