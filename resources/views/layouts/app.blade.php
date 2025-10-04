<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Laravel'))</title>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('assets/css/styles.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet">
    @stack('styles')
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    @if (Request::is('dashboard') || Request::is('gift-store') || Request::is('instrument-rental') || Request::is('music-studio') || Request::is('reports') || Request::is('settings') || Request::is('supermarket') || Request::is('users'))
        
    
     <div class="main-app" id="mainApp">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Navigation -->
            <nav class="top-navbar" id="topNavbar">
                <!-- Top navigation content will be loaded here -->
            </nav>

    @include('layouts.dashboard-header')
    @yield('content')
    @include('layouts.dashboard-footer')

    @else

    <div class="login-container">
    <div class="container">
        @include('layouts.auth-header')


    @endif



    {{-- <div id="app">
        <main class="py-4"> --}}
            @yield('content')
        {{-- </main>
    </div> --}}


    @if (Request::is('dashboard') || Request::is('gift-store') || Request::is('instrument-rental') || Request::is('music-studio') || Request::is('reports') || Request::is('settings') || Request::is('supermarket') || Request::is('users'))
        </div>
     </div>
    @else
        @include('layouts.auth-footer')

    </div>
</div>
    @endif
    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
