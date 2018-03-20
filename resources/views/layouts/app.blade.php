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
    <link href="{{asset('css/justplayed.css')}}" rel="stylesheet">
</head>

<body>

@include('layouts._navbar')

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
@section('scripts')
@show
</body>
</html>
