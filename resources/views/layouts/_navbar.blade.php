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
                    <a class="nav-link dropdown-toggle" href="{{url('/')}}" id="dropdown_primary" data-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false">
                        @yield('navbar_primary_title', 'Primary')
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdown_primary">
                        <a class="dropdown-item" href="{{route('collection.index')}}">Collection</a>
                        <a class="dropdown-item" href="{{route('settings.edit')}}">Settings</a>
                        <a class="dropdown-item" href="{{route('lastfm.scrobble.queue-show')}}">Queue</a>
                        <a id="logout-link" class="dropdown-item" href="{{route('logout')}}">Logout</a>
                    </div>
                    <form id="logout-form" action="{{route('logout')}}" method="post">
                        @csrf
                    </form>
                </li>
                @section('navbar_secondary')
                @show
            </ul>
            @section('navbar_form')
            @show
        </div>
    @endif
</nav>