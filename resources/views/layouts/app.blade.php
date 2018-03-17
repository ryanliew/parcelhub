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
    <link rel="stylesheet" href="{{ asset('css/app.css?v=1.0.7') }}">
    
    <script>
        window.App = {!! json_encode([
                'signedIn' => Auth::check()
            ]) !!};
    </script>

</head>
<body>
    <div id="app">
        @if(auth()->check())
            <navigation can_manage="{{ auth()->user()->hasRole('admin') }}" title="{{ config('app.name', 'Laravel') }}" user="{{ auth()->user()->name }}"></navigation>
        @endif

        @yield('content')
        
        <flash></flash>
    </div>
    
    @if(auth()->check()) 
    <footer class="footer">
        <div class="container">
            <div class="content has-text-centered">
                <p>
                    <strong>{{ config('app.name', 'Laravel') }}</strong> by <a href="https://jgthms.com">Ryan Liew</a>. The source code is licensed
                    <a href="http://opensource.org/licenses/mit-license.php">MIT</a>. The website content
                    is licensed <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/">CC BY NC SA 4.0</a>.
                </p>
            </div>
        </div>

    </footer>
    @endif
    <script src="{{ asset('js/form.js?v=1.0.5') }}"></script>
    <script src="{{ asset('js/app.js?v=1.9.3') }}"></script>

    @yield('js')


</body>
</html>
