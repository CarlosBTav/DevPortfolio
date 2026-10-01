<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.seo')
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('img/favicon.png') }}?v={{ filemtime(public_path('img/favicon.png')) }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ filemtime(public_path('favicon.ico')) }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    {{-- Las fuentes se piden en modo CORS: sin crossorigin, esa conexión anticipada no se reutiliza para ellas --}}
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link href="https://fonts.bunny.net/css?family=figtree:400,600,800&display=swap" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite([
        'resources/css/app.css',
        'resources/css/public-layout.css',
        'resources/css/spotlight.css',
        'resources/js/app.js',
        'resources/js/public-layout.js',
        'resources/js/spotlight.js',
    ])

    {{-- Must run before first paint to avoid flashing the wrong color theme. --}}
    <script>
        /* Sync with the 'color-theme' key used by the theme-toggle component */
        if (localStorage['color-theme'] === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="@yield('body-class','antialiased bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-gray-100 font-sans flex flex-col min-h-dynamic transition-colors duration-300')">
    
    <div id="scroll-progress-container"><div id="scroll-progress-bar"></div></div>

    <!-- Incluimos el Menú de navegación -->
    @include('partials.navbar')

    <!-- AQUÍ SE INYECTARÁ EL CONTENIDO DE LA VISTA -->
    <main class="relative z-10 flex-grow min-w-0">
        @yield('content')
    </main>

    <!-- Incluimos el pie de página -->
    @include('partials.footer')

    <!-- BOTÓN SCROLL TO TOP -->
    <div id="scrollToTopBtn" onclick="window.scrollTo({top: 0, behavior: 'smooth'})" class="fixed right-8 z-[80] bottom-8 opacity-0 pointer-events-none translate-y-10">
        <div class="h-12 w-12 rounded-full bg-indigo-600 hover:bg-indigo-500 dark:bg-indigo-600 dark:hover:bg-indigo-500 cursor-pointer flex items-center justify-center shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/50 transition-colors duration-300" role="button" tabindex="0">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="text-white"><path d="m18 15-6-6-6 6"></path></svg>
        </div>
    </div>


    @if(config('services.umami.src') && config('services.umami.website_id') && ! request()->is('dashboard', 'dashboard/*', 'admin', 'admin/*', 'profile', 'profile/*', 'login', 'register'))
        <script defer src="{{ config('services.umami.src') }}" data-website-id="{{ config('services.umami.website_id') }}"></script>
    @endif

    <!-- Espacio para inyectar scripts específicos de cada vista -->
    @stack('scripts')
</body>
</html>
