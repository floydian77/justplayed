@if(session('error'))
    <div class="alert alert-danger">
        {{session('error')}}
    </div>
@endif

@if(session('status'))
    <div class="alert alert-success">
        {{session('status')}}
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-warning">
        {{session('warning')}}
    </div>
@endif