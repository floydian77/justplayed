@extends('layouts.app')

@section('content')
    @if(session('error'))
        <div class="alert alert-warning">
            {{session('error')}}
        </div>
    @endif

    <form action="{{route('authenticate')}}" method="post">
        @csrf

        <div class="form-group">
            <input name="email" type="email" class="form-control" placeholder="email" value="{{old('email')}}" required>
        </div>

        <div class="form-group">
            <input name="password" type="password" class="form-control" placeholder="password" required>
        </div>

        <div class="form-group">
            <input type="submit" value="Login" class="btn btn-lg btn-primary">
        </div>
    </form>
@stop