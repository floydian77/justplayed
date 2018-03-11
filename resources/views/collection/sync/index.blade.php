@extends('layouts.app')

@section('navbar_primary_title', 'Collection')

@section('navbar_secondary')
    @include('collection._navbar', ['title' => 'Synchronize'])
@stop

@section('content')
    <div>
        <h1>Synchronize</h1>

        <div>
            Synchronize collection, click only once, be patient.
        </div>

        <div>

            <a id="sync-link" href="{{route('sync.sync')}}" class="btn btn-lg btn-warning">
                Synchronize collection
            </a>
            <form id="sync-form" action="{{route('sync.sync')}}" method="post">
                @method('put')
                @csrf
            </form>
        </div>
    </div>
@stop

@section('scripts')
    <script>
        $('#sync-link').on("click", function (event) {
            event.preventDefault();

            $('#sync-form').submit();
        })
    </script>
@stop