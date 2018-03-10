<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="{{url('/')}}" id="dropdown_secondary" data-toggle="dropdown"
       aria-haspopup="true" aria-expanded="false">
        {{$title}}
    </a>
    <div class="dropdown-menu" aria-labelledby="dropdown_secondary">
        <a class="dropdown-item" href="{{route('collection.index')}}">Index</a>
        <a class="dropdown-item" href="{{route('collection.syncForm')}}">Synchronize</a>
    </div>
</li>