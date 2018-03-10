<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="{{asset('favicon.ico')}}">

    <title>{{config('app.name')}}</title>

    <!-- Bootstrap core CSS -->
    <link href="{{asset('css/app.css')}}" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="{{asset('css/starter-template.css')}}" rel="stylesheet">
</head>

<body>

<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
    <a class="navbar-brand" href="{{url('/')}}">{{config('app.name')}}</a>
    @if(Auth::user())
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault"
                aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarsExampleDefault">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="{{url('/')}}" id="dropdown01" data-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false">
                        @switch(Route::getCurrentRoute()->uri)
                            @case('collection')
                            Collection
                            @break

                            @case('settings')
                            Settings
                            @break

                            @default
                            Home
                        @endswitch
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdown01">
                        <a class="dropdown-item" href="{{route('collection.index')}}">Collection</a>
                        <a class="dropdown-item" href="{{route('settings.index')}}">Settings</a>
                        <a id="logout-link" class="dropdown-item" href="{{route('logout')}}">Logout</a>
                    </div>
                    <form id="logout-form" action="{{route('logout')}}" method="post">
                        @csrf
                    </form>
                </li>
            </ul>
            <form class="form-inline my-2 my-lg-0">
                <input class="form-control mr-sm-2" type="text" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
            </form>
        </div>
    @endif
</nav>

<main role="main" class="container">
    @yield('content')
</main><!-- /.container -->

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="{{asset('js/app.js')}}"></script>
<script>
    $('#logout-link').on("click", function (event) {
        event.preventDefault();
        $('#logout-form').submit();
    })
</script>
</body>
</html>
