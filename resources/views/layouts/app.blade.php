<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title> 

    @yield('css')
    
    <!-- Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/app.css?v=' . config('app.version')) }}">
    <link rel="stylesheet" href="{{ asset('css/responsive-tables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}">

    <script>
        window.App = {!! json_encode([
                'signedIn' => Auth::check()
            ]) !!};
    </script>

</head>
<body>
    <div id="app">
        @if(auth()->check())
            <navigation can_manage="{{ auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin') }}" title="{{ config('app.name', 'Laravel') }}" user="{{ auth()->user()->name }}"></navigation>
        @endif

        @yield('content')
        
        <flash></flash>
    </div>
    
    @if(auth()->check()) 
    <footer class="footer">
        <div class="container">
            <div class="content has-text-centered">
                <p>
                    <strong>{{ config('app.name', 'Laravel') }}</strong> by <a href="https://welory.com.my">WELORY INNOVATION SDN BHD</a>. &copy; {{ now()->year }} <a href="https://parcelhub.com.my">PPS GLOBAL NETWORK SDN BHD</a>.
                </p>
            </div>
        </div>

    </footer>
    @endif
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bulma-steps@2.2.1/dist/js/bulma-steps.min.js">
    </script>
    <script src="{{ asset('js/form.js?v=' . config('app.version')) }}"></script>
    <script src="{{ asset('js/app.js?v=' . config('app.version')) }}"></script>
    <script src="{{ asset('js/jquery-1.7.1.min.js') }}"></script>
    <script src="{{ asset('js/responsive-tables.js') }}"></script>
    <script src="{{ asset('js/broadcast.js') }}"></script>
    @yield('js')

</body>
</html>
