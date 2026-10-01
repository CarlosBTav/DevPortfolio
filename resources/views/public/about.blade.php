@extends('layouts.public')

@section('title', 'Sobre mí | Carlos Codex')
@section('meta_description', 'Conoce mi trayectoria, experiencia y forma de trabajar como desarrollador Full Stack especializado en productos digitales.')

@section('body-class', 'antialiased font-sans flex flex-col min-h-dynamic transition-colors duration-300 text-gray-900 dark:text-gray-100 about-ai-dots-page')

@section('content')
<div class="relative w-full min-h-dynamic overflow-x-clip bg-transparent dark:bg-transparent">
    {{-- section: el fondo cubre toda la altura del contenido (viewport fijo cortaba al hacer scroll).
         -bottom-12 lo prolonga bajo el footer para rellenar el hueco de sus esquinas redondeadas --}}
    <div class="pointer-events-none absolute inset-x-0 top-0 -bottom-12 z-0 overflow-hidden" aria-hidden="true">
        <x-ai-dots-background variant="section" />
    </div>
    <!-- CONTENEDOR PRINCIPAL -->
    <div class="relative z-10 max-w-4xl mx-auto px-6 lg:px-8 public-page-top pb-12 lg:pb-20">

        <!-- 1. SECCIÓN INTRODUCCIÓN (NARRATIVA) -->
        <section class="mb-20">
            <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 dark:text-white mb-8 tracking-tight">
                👨‍💻 Sobre <span class="text-indigo-600 dark:text-indigo-400">mí</span>
            </h1>
            
            <div class="prose prose-lg dark:prose-invert text-gray-600 dark:text-gray-300 leading-relaxed max-w-none">
                <p class="mb-6">
                    Llevo más de <span class="font-bold text-gray-900 dark:text-white">7 años escribiendo código</span> (y borrando también, prueba y error). Mi recorrido fue tal que así:
                </p>
                <p class="mb-6">
                    Empecé mi carrera como <span class="font-bold text-gray-900 dark:text-white">técnico en electrónica aeroespacial y diseño de PCBs</span>. Aunque me encantaba el hardware, desde los 17 años mi pasión era programar videojuegos en mi tiempo libre.
                     Al final, el lado del software me pudo: Dejé mi trabajo, me licencié en <span class="font-semibold text-indigo-600 dark:text-indigo-400">Desarrollo de Aplicaciones Web (DAW)</span> y <span class="font-semibold text-indigo-600 dark:text-indigo-400">Multiplataforma (DAM)</span>, y cambié la electrónica por el código (aunque actualmente sigo aprovechando esos conocimientos al programar para un laboratorio aeroespacial).
                </p>
                <p>
                    Al principio trabajé desarrollando sistemas de control remoto para iOS y actualmente me especializo en desarrollo <strong>Web Fullstack</strong> (Especialmente Servidores, ERPs y CRMs) y <strong>Aplicaciones Multiplataforma</strong> (Android Studio con Compose Multiplatform).
                </p>
            </div>
        </section>

        <hr class="border-gray-200 dark:border-gray-800 mb-20">

        <!-- 2. EL HUMANO DETRÁS DEL CÓDIGO (GRID DE TARJETAS) -->
        <section class="mb-20">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-10 flex items-center gap-3">
                <span>🎸</span> El Humano detrás del código
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Card Música -->
                <div class="js-spotlight-card group !bg-gray-50 dark:!bg-gray-800/50 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 relative overflow-hidden">
                    <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-full flex items-center justify-center text-2xl mb-4">🎹</div>
                    <h3 class="font-bold text-gray-900 dark:text-white text-lg mb-2">Soy músico</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Toco el piano, la guitarra y el saxofón. Me lo paso bien componiendo y estudiando teoría musical cuando no estoy compilando.
                    </p>
                </div>

                <!-- Card Idiomas -->
                <div class="js-spotlight-card group !bg-gray-50 dark:!bg-gray-800/50 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 relative overflow-hidden">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center text-2xl mb-4">🌍</div>
                    <h3 class="font-bold text-gray-900 dark:text-white text-lg mb-2">Intento de políglota</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Hablo <strong>Inglés</strong> avanzado y <strong>Coreano</strong> intermedio. Actualmente en plena batalla estudiando <strong>Chino</strong> (¡Pregúntame sobre los palacios mentales!).
                    </p>
                </div>

                <!-- Card Mecanografía -->
                <div class="js-spotlight-card group !bg-gray-50 dark:!bg-gray-800/50 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 relative overflow-hidden">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center text-2xl mb-4">⌨️</div>
                    <h3 class="font-bold text-gray-900 dark:text-white text-lg mb-2">Mecanografía</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Top mundial español con un récord de <span class="text-indigo-600 dark:text-indigo-400 font-bold">147 palabras por minuto</span>. Sí, pico código bastante rápido jaja.
                    </p>
                </div>
            </div>
        </section>

        <!-- 3. EXPERIENCIA PROFESIONAL (EMPRESAS POR ÁMBITO, ORDEN CRONOLÓGICO HORIZONTAL) -->
        @php
            $trackStyles = [
                'software' => [
                    'chip' => 'bg-indigo-100 dark:bg-indigo-900/30',
                    'rule' => 'from-indigo-200 dark:from-indigo-800/60',
                    'line' => 'bg-indigo-200 dark:bg-indigo-800/60',
                    'fade' => 'from-indigo-200 dark:from-indigo-800/60',
                    'dot' => 'bg-indigo-600 dark:bg-indigo-400',
                    'monogram' => 'from-indigo-500 to-violet-600',
                    'company' => 'text-indigo-600 dark:text-indigo-400',
                    // 4 tarjetas: rejilla a partir de lg; por debajo, carrusel horizontal
                    'scroller' => 'lg:mx-0 lg:px-0 lg:overflow-visible lg:pb-0',
                    'list' => 'lg:w-full lg:grid',
                    'item' => 'lg:w-auto',
                ],
                'electronics' => [
                    'chip' => 'bg-amber-100 dark:bg-amber-900/30',
                    'rule' => 'from-amber-200 dark:from-amber-800/50',
                    'line' => 'bg-amber-200 dark:bg-amber-800/50',
                    'fade' => 'from-amber-200 dark:from-amber-800/50',
                    'dot' => 'bg-amber-500 dark:bg-amber-400',
                    'monogram' => 'from-amber-400 to-orange-600',
                    'company' => 'text-amber-600 dark:text-amber-400',
                    // Pocas tarjetas: ocupan todo el ancho ya desde md
                    'scroller' => 'md:mx-0 md:px-0 md:overflow-visible md:pb-0',
                    'list' => 'md:w-full md:grid',
                    'item' => 'md:w-auto',
                ],
            ];
        @endphp
        <section class="mb-20">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-10 flex items-center gap-3">
                <span>💼</span> Experiencia Profesional
            </h2>

            <div class="space-y-12">
                @foreach ($experienceTracks as $trackKey => $track)
                    @php $style = $trackStyles[$trackKey] ?? $trackStyles['software']; @endphp
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <span class="w-10 h-10 rounded-xl {{ $style['chip'] }} flex items-center justify-center text-xl shrink-0" aria-hidden="true">
                                @isset($track['icon_component'])
                                    <x-dynamic-component :component="$track['icon_component']" class="w-7 h-7" />
                                @else
                                    {{ $track['icon'] }}
                                @endisset
                            </span>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $track['label'] }}</h3>
                            <span class="h-px flex-1 bg-gradient-to-r {{ $style['rule'] }} to-transparent" aria-hidden="true"></span>
                        </div>

                        {{-- pt-3: el halo del punto «actual» sobresale por arriba y el scroll horizontal lo recortaba --}}
                        <div class="-mx-6 px-6 scroll-px-6 overflow-x-auto snap-x snap-mandatory pt-3 pb-4 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden {{ $style['scroller'] }}"
                            data-overscroll-bounce="ol"
                            @if ($track['autoscroll'] ?? false) data-experience-autoscroll @endif>
                            <ol class="flex gap-5 w-max {{ $style['list'] }}" style="grid-template-columns: repeat({{ $track['jobs']->count() }}, minmax(0, 1fr));">
                                @foreach ($track['jobs'] as $job)
                                    @php
                                        $monogram = \Illuminate\Support\Str::of($job['company'])
                                            ->explode(' ')
                                            ->take(2)
                                            ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))
                                            ->implode('');
                                    @endphp
                                    <li class="relative snap-start w-64 sm:w-72 {{ $style['item'] }} pt-9 flex">
                                        {{-- Tramo del eje temporal hasta la siguiente empresa; el último se desvanece --}}
                                        @if ($loop->last)
                                            <span class="absolute top-[7px] left-0 right-0 h-0.5 bg-gradient-to-r {{ $style['fade'] }} to-transparent" data-segment aria-hidden="true"></span>
                                        @else
                                            <span class="absolute top-[7px] left-0 -right-5 h-0.5 {{ $style['line'] }}" data-segment aria-hidden="true"></span>
                                        @endif
                                        <span class="absolute top-0 left-6 flex w-4 h-4" aria-hidden="true">
                                            @if ($job['current'])
                                                <span class="absolute inline-flex w-full h-full rounded-full {{ $style['dot'] }} opacity-60 animate-ping motion-reduce:animate-none"></span>
                                            @endif
                                            <span class="relative inline-flex w-4 h-4 rounded-full {{ $style['dot'] }} border-[3px] border-white dark:border-gray-900"></span>
                                        </span>

                                        <article class="js-spotlight-card group flex-1 flex flex-col !bg-gray-50 dark:!bg-gray-800/50 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 relative overflow-hidden">
                                            <div class="flex items-start justify-between gap-3 mb-4">
                                                {{-- Las iniciales quedan debajo del logo: si la imagen falla, se retira y aparecen --}}
                                                <span class="relative w-12 h-12 rounded-xl bg-gradient-to-br {{ $style['monogram'] }} text-white font-extrabold text-sm tracking-wide flex items-center justify-center shadow-md shrink-0 overflow-hidden" aria-hidden="true">
                                                    {{ $monogram }}
                                                    @isset($job['logo'])
                                                        <img src="{{ asset($job['logo']) }}" alt="" width="48" height="48" loading="lazy" decoding="async"
                                                            class="absolute inset-0 w-full h-full object-contain {{ ($job['logo_fill'] ?? false) ? '' : 'bg-white p-1.5' }}"
                                                            onerror="this.onerror=null;this.remove()">
                                                    @endisset
                                                </span>
                                                @if ($job['current'])
                                                    <span class="inline-flex items-center gap-1.5 py-0.5 px-2.5 rounded-full bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 text-[11px] font-bold uppercase tracking-wider">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                        Actual
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="font-bold text-gray-900 dark:text-white leading-snug">{{ $job['company'] }}</p>
                                            @isset($job['group'])
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $job['group'] }}</p>
                                            @endisset
                                            <p class="{{ $style['company'] }} text-sm font-medium mt-1 mb-3">{{ $job['role'] }}</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{{ $job['description'] }}</p>
                                        </article>
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- 4. EDUCACIÓN (GRID SIMPLE) -->
        <section>
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-10 flex items-center gap-3">
                <span>🎓</span> Educación
            </h2>
            
            <div class="grid gap-6 md:grid-cols-2">
                <!-- Grado Superior DAW -->
                <div class="js-spotlight-card group !bg-gray-50 dark:!bg-gray-800/50 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 flex items-start gap-4 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 relative overflow-hidden">
                    <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-full flex items-center justify-center text-2xl shrink-0">🖥️</div>
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white">Desarrollo de Aplicaciones Web (DAW)</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Sep 2025 - Ene 2026</p>
                    </div>
                </div>

                <!-- Grado Superior DAM -->
                <div class="js-spotlight-card group !bg-gray-50 dark:!bg-gray-800/50 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 flex items-start gap-4 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 relative overflow-hidden">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center text-2xl shrink-0">📱</div>
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white">Desarrollo de Aplicaciones Multiplataforma (DAM)</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Ago 2021 - Jun 2023</p>
                    </div>
                </div>

                <!-- Mantenimiento -->
                <div class="js-spotlight-card group !bg-gray-50 dark:!bg-gray-800/50 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 flex items-start gap-4 md:col-span-2 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 relative overflow-hidden">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center text-2xl shrink-0">⚡</div>
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white">Mantenimiento Electrónico</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Formación técnica previa</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA FINAL -->
        <div class="mt-20 text-center">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-3">¿Te apetece ver más?</h2>
            <p class="text-gray-600 dark:text-gray-300 mb-8">Echa un vistazo a lo que he construido y a las herramientas con las que trabajo.</p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-10">
                <a href="{{ route('public.projects') }}" class="group inline-flex items-center justify-center font-semibold text-gray-900 dark:text-white bg-white/80 dark:bg-gray-800/60 border border-gray-300 dark:border-gray-600 hover:border-indigo-500 hover:text-indigo-600 dark:hover:border-indigo-400 dark:hover:text-indigo-300 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 hover:-translate-y-1 backdrop-blur-sm px-8 py-4 text-base">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"></path></svg>
                    Explora mis proyectos
                </a>
                <a href="{{ route('public.stack') }}" class="group inline-flex items-center justify-center font-semibold text-gray-900 dark:text-white bg-white/80 dark:bg-gray-800/60 border border-gray-300 dark:border-gray-600 hover:border-indigo-500 hover:text-indigo-600 dark:hover:border-indigo-400 dark:hover:text-indigo-300 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 hover:-translate-y-1 backdrop-blur-sm px-8 py-4 text-base">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                    Ver mi stack tecnológico
                </a>
            </div>
            <a href="{{ route('public.contact') }}" class="inline-flex items-center justify-center px-8 py-4 text-base font-bold text-white transition-all duration-200 bg-indigo-600 rounded-lg hover:bg-indigo-700 hover:shadow-lg hover:-translate-y-1">
                ¿Hablamos?
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>

    </div>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/overscroll-bounce.js', 'resources/js/experience-autoscroll.js'])
@endpush
