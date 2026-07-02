<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"> 
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Favicons - Deve vir logo após charset para melhor compatibilidade -->
    <!-- Fallback para raiz do public (padrão dos navegadores) -->
    <link rel="icon" type="image/x-icon" href="{{ url('/favicon.ico') }}?v={{ time() }}">
    <link rel="shortcut icon" href="{{ url('/favicon.ico') }}?v={{ time() }}" type="image/x-icon">
    <!-- Versões específicas -->
    <link rel="icon" type="image/x-icon" href="{{ url('favicons/favicon.ico') }}?v={{ time() }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ url('favicons/favicon.ico') }}?v={{ time() }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ url('favicons/favicon.ico') }}?v={{ time() }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ url('favicons/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ url('favicons/android-chrome-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ url('favicons/android-chrome-512x512.png') }}">
    <link rel="manifest" href="{{ url('favicons/site.webmanifest') }}">
    
    <meta name="description" content="Centro de apoio escolar especializado em acompanhamento pedagógico">
    <meta name="theme-color" content="#ec4899">
    <meta name="author" content="Corujinha">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Corujinha - Centro de Apoio Escolar')</title>
    
    <!-- Tailwind CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- CSS do front-end: base, principal e responsivo -->
    <link href="{{ asset('css/main_principal.css') }}" rel="stylesheet">
    <link href="{{ asset('css/main.css') }}" rel="stylesheet">
    <link href="{{ asset('css/responsive.css') }}" rel="stylesheet">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
    
    @stack('styles')
</head>
<body class="bg-white">
    @include('components.header')
    
    <main>
        @yield('content')
    </main>
    
    @include('components.footer')
    
    <!-- JS do front-end -->
    <script src="{{ asset('plugins/jquery/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('js/scroll.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="{{ asset('js/pwa-register.js') }}"></script>
    
    @stack('scripts')
</body>
</html>

