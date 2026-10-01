@extends('layouts.public')

@section('title', 'Desarrollo Web | Carlos Codex')
@section('meta_description', 'Creo webs rápidas y orientadas a conversión para negocios: desde landing pages hasta plataformas personalizadas con panel de gestión.')
@section('body-class', 'antialiased bg-white text-gray-900 dark:bg-slate-950 dark:text-slate-100 font-sans flex flex-col min-h-dynamic transition-colors duration-300')

@php
    $contactUrl = route('public.contact', ['interest' => 'web']);
    $whatsappUrl = filled($whatsappPhone ?? null)
        ? 'https://wa.me/' . $whatsappPhone . '?text=' . rawurlencode('Hola Carlos, me interesa el servicio de desarrollo web.')
        : null;

    $processSteps = [
        [
            'title' => '01. Diagnóstico',
            'description' => 'Defino objetivos, público y alcance realista del proyecto.',
            'icon' => '<svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m20 20-3.5-3.5"/></svg>',
        ],
        [
            'title' => '02. Propuesta',
            'description' => 'Entrego roadmap, tiempos y entregables para validar sin sorpresas.',
            'icon' => '<svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"/><path stroke-linecap="round" d="M9 12h6M9 16h6"/></svg>',
        ],
        [
            'title' => '03. Construcción',
            'description' => 'Desarrollo por bloques con revisiones periódicas y feedback continuo.',
            'icon' => '<svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m8 9-3 3 3 3"/><path stroke-linecap="round" stroke-linejoin="round" d="m16 15 3-3-3-3"/></svg>',
        ],
        [
            'title' => '04. Lanzamiento',
            'description' => 'Publicación, medición inicial y plan de mejoras con foco en resultados.',
            'icon' => '<svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09Z"/><path stroke-linecap="round" stroke-linejoin="round" d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"/></svg>',
        ],
    ];
@endphp

@section('content')
<section class="relative w-full public-page-top public-page-top--compact pb-20 lg:pb-24">
    <div class="pointer-events-none absolute inset-0 bg-white dark:bg-slate-950"></div>
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_18%_12%,rgba(129,140,248,0.14),transparent_40%),radial-gradient(circle_at_82%_18%,rgba(139,92,246,0.1),transparent_36%)] dark:bg-[radial-gradient(circle_at_18%_12%,rgba(129,140,248,0.2),transparent_40%),radial-gradient(circle_at_82%_18%,rgba(139,92,246,0.16),transparent_36%)]"></div>

    <div class="relative z-10 mx-auto max-w-screen-xl space-y-20 px-4 sm:px-6 lg:px-10 lg:space-y-24">
        <div class="grid grid-cols-1 items-center gap-10 xl:grid-cols-[minmax(0,1.05fr)_minmax(0,0.95fr)] xl:gap-12">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-indigo-600 dark:text-indigo-300">Servicio</p>
                <h1 class="mt-3 max-w-2xl text-3xl font-extrabold leading-tight text-gray-900 dark:text-white md:text-5xl">
                    Desarrollo web para <span class="text-indigo-600 dark:text-indigo-400">convertir visitas en clientes</span>
                </h1>
                <p class="mt-5 max-w-2xl text-base leading-relaxed text-gray-600 dark:text-gray-300 md:text-lg">
                    Diseño y desarrollo sitios web y plataformas a medida para negocios que necesitan vender mejor, automatizar tareas y transmitir confianza desde el primer clic.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ $contactUrl }}" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-indigo-500">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-4l-4 4v-4Z" />
                        </svg>
                        Abrir asistente de contacto
                    </a>
                    <a href="{{ route('public.projects') }}" class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-6 py-3 text-sm font-semibold text-gray-800 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-transparent dark:text-gray-200 dark:hover:bg-gray-900">
                        Ver proyectos
                    </a>
                </div>

                <div class="mt-8 flex flex-wrap gap-x-6 gap-y-3 text-sm text-gray-600 dark:text-gray-300">
                    <div class="inline-flex items-center gap-2">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-indigo-50 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-300">
                            <svg viewBox="0 0 20 20" class="h-4 w-4 fill-current" aria-hidden="true"><path d="M7.63 13.23 4.4 10l-1.4 1.41 4.63 4.62L17 6.66l-1.41-1.41z"/></svg>
                        </span>
                        Enfoque en resultados
                    </div>
                    <div class="inline-flex items-center gap-2">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-indigo-50 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-300">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l3.5 2M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        </span>
                        Sitios rápidos y seguros
                    </div>
                    <div class="inline-flex items-center gap-2">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-indigo-50 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-300">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path stroke-linecap="round" stroke-linejoin="round" d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </span>
                        Acompañamiento cercano
                    </div>
                </div>
            </div>

            @include('public.services.partials.hero-mockup')
        </div>

        <div>
            <div class="mx-auto max-w-2xl text-center">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-indigo-600 dark:text-indigo-300">Cómo puedo ayudarte</p>
                <h2 class="mt-3 text-2xl font-bold text-gray-900 dark:text-white md:text-3xl">¿Empezamos de cero o mejoramos lo que ya tienes?</h2>
                <p class="mt-3 text-sm leading-relaxed text-gray-600 dark:text-gray-300 md:text-base">Hago webs nuevas de todo tipo y también pongo a punto las que ya existen. Cuéntame dónde estás y te propongo el camino más corto.</p>
            </div>

            <div class="mt-10 grid grid-cols-1 gap-6 lg:grid-cols-2">
                <article class="flex flex-col rounded-2xl border border-gray-200 bg-gray-50 p-7 dark:border-gray-800 dark:bg-gray-900/70">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-300">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
                        </span>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-indigo-600 dark:text-indigo-300">Desde cero</p>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Crear tu web</h3>
                        </div>
                    </div>
                    <p class="mt-4 text-sm leading-relaxed text-gray-600 dark:text-gray-300">Del tipo que necesite tu negocio. Siempre a medida, nunca de plantilla.</p>
                    <div class="mt-5 grid flex-1 grid-cols-1 gap-3 sm:grid-cols-2">
                        <div class="rounded-xl border border-gray-200 bg-white p-4 transition hover:border-indigo-300 hover:shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:hover:border-indigo-500/50">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 shrink-0 text-indigo-600 dark:text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="7" width="18" height="13" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">Portfolio o web corporativa</p>
                            </div>
                            <p class="mt-1.5 text-xs leading-relaxed text-gray-500 dark:text-gray-400">Presenta tu negocio y genera confianza.</p>
                        </div>
                        <div class="rounded-xl border border-gray-200 bg-white p-4 transition hover:border-indigo-300 hover:shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:hover:border-indigo-500/50">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 shrink-0 text-indigo-600 dark:text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path stroke-linecap="round" stroke-linejoin="round" d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">Tienda online</p>
                            </div>
                            <p class="mt-1.5 text-xs leading-relaxed text-gray-500 dark:text-gray-400">Catálogo, pagos y gestión de pedidos.</p>
                        </div>
                        <div class="rounded-xl border border-gray-200 bg-white p-4 transition hover:border-indigo-300 hover:shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:hover:border-indigo-500/50">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 shrink-0 text-indigo-600 dark:text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">ERP / CRM a medida</p>
                            </div>
                            <p class="mt-1.5 text-xs leading-relaxed text-gray-500 dark:text-gray-400">La gestión de tu negocio, sin hojas de cálculo.</p>
                        </div>
                        <div class="rounded-xl border border-gray-200 bg-white p-4 transition hover:border-indigo-300 hover:shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:hover:border-indigo-500/50">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 shrink-0 text-indigo-600 dark:text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3Z"/></svg>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">CMS / Blog</p>
                            </div>
                            <p class="mt-1.5 text-xs leading-relaxed text-gray-500 dark:text-gray-400">Publica y edita sin depender de nadie.</p>
                        </div>
                        <div class="rounded-xl border border-gray-200 bg-white p-4 transition hover:border-indigo-300 hover:shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:hover:border-indigo-500/50">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 shrink-0 text-indigo-600 dark:text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">Reservas y citas</p>
                            </div>
                            <p class="mt-1.5 text-xs leading-relaxed text-gray-500 dark:text-gray-400">Tu agenda se llena sola, sin llamadas.</p>
                        </div>
                        <div class="rounded-xl border border-gray-200 bg-white p-4 transition hover:border-indigo-300 hover:shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:hover:border-indigo-500/50">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 shrink-0 text-indigo-600 dark:text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m16 18 6-6-6-6M8 6l-6 6 6 6"/></svg>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">Web app a medida</p>
                            </div>
                            <p class="mt-1.5 text-xs leading-relaxed text-gray-500 dark:text-gray-400">¿Algo que no existe? Lo construimos.</p>
                        </div>
                    </div>
                </article>

                <article class="flex flex-col rounded-2xl border border-gray-200 bg-gray-50 p-7 dark:border-gray-800 dark:bg-gray-900/70">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-500/15 dark:text-amber-300">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76Z"/></svg>
                        </span>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-amber-600 dark:text-amber-300">Ya existe</p>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Mejorar tu web actual</h3>
                        </div>
                    </div>
                    <p class="mt-4 text-sm leading-relaxed text-gray-600 dark:text-gray-300">No siempre hace falta rehacerla: muchas veces basta con ponerla a punto. ¿Te suena alguna de estas?</p>
                    <ul class="mt-6 flex-1 space-y-5">
                        <li>
                            <p class="text-sm font-semibold leading-relaxed text-gray-900 dark:text-white">«Va lenta y en el móvil se ve mal»</p>
                            <p class="mt-1.5 flex items-start gap-2 text-sm leading-relaxed text-gray-600 dark:text-gray-300">
                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-amber-500 dark:text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m15 10 5 5-5 5"/><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v7a4 4 0 0 0 4 4h12"/></svg>
                                Optimizo el rendimiento y adapto el diseño a cualquier pantalla.
                            </p>
                        </li>
                        <li>
                            <p class="text-sm font-semibold leading-relaxed text-gray-900 dark:text-white">«Se quedó anticuada y no da buena imagen»</p>
                            <p class="mt-1.5 flex items-start gap-2 text-sm leading-relaxed text-gray-600 dark:text-gray-300">
                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-amber-500 dark:text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m15 10 5 5-5 5"/><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v7a4 4 0 0 0 4 4h12"/></svg>
                                La rediseño de arriba abajo sin que pierdas tu posicionamiento.
                            </p>
                        </li>
                        <li>
                            <p class="text-sm font-semibold leading-relaxed text-gray-900 dark:text-white">«Me preocupa la seguridad»</p>
                            <p class="mt-1.5 flex items-start gap-2 text-sm leading-relaxed text-gray-600 dark:text-gray-300">
                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-amber-500 dark:text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m15 10 5 5-5 5"/><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v7a4 4 0 0 0 4 4h12"/></svg>
                                Audito, refuerzo y activo copias de seguridad automáticas.
                            </p>
                        </li>
                        <li>
                            <p class="text-sm font-semibold leading-relaxed text-gray-900 dark:text-white">«No tengo quien la mantenga»</p>
                            <p class="mt-1.5 flex items-start gap-2 text-sm leading-relaxed text-gray-600 dark:text-gray-300">
                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-amber-500 dark:text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m15 10 5 5-5 5"/><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v7a4 4 0 0 0 4 4h12"/></svg>
                                Mantenimiento continuo: actualizaciones, mejoras y soporte. Tú te olvidas.
                            </p>
                        </li>
                    </ul>
                </article>
            </div>

            <div class="mt-6 rounded-2xl border border-indigo-100 bg-indigo-50/60 px-6 py-5 dark:border-indigo-500/20 dark:bg-indigo-500/10">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Elijas el camino que elijas, esto va de serie:</p>
                    <ul class="flex flex-wrap gap-x-6 gap-y-2 text-sm text-gray-700 dark:text-gray-300">
                        <li class="flex items-center gap-2"><svg class="h-4 w-4 shrink-0 text-indigo-600 dark:text-indigo-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M7.63 13.23 4.4 10l-1.4 1.41 4.63 4.62L17 6.66l-1.41-1.41z"/></svg>Diseño a medida</li>
                        <li class="flex items-center gap-2"><svg class="h-4 w-4 shrink-0 text-indigo-600 dark:text-indigo-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M7.63 13.23 4.4 10l-1.4 1.41 4.63 4.62L17 6.66l-1.41-1.41z"/></svg>Optimizada para SEO y velocidad</li>
                        <li class="flex items-center gap-2"><svg class="h-4 w-4 shrink-0 text-indigo-600 dark:text-indigo-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M7.63 13.23 4.4 10l-1.4 1.41 4.63 4.62L17 6.66l-1.41-1.41z"/></svg>Panel para gestionarla tú mismo</li>
                        <li class="flex items-center gap-2"><svg class="h-4 w-4 shrink-0 text-indigo-600 dark:text-indigo-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M7.63 13.23 4.4 10l-1.4 1.41 4.63 4.62L17 6.66l-1.41-1.41z"/></svg>Soporte tras el lanzamiento</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="text-center">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white md:text-3xl">Proceso de trabajo</h2>
            <p class="mx-auto mt-3 max-w-2xl text-sm leading-relaxed text-gray-600 dark:text-gray-300 md:text-base">
                Un proceso claro y colaborativo para que sepas qué esperar en cada fase del proyecto.
            </p>

            <x-process-steps :steps="$processSteps" />
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)] lg:items-start">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white md:text-3xl">Proyectos destacados</h2>
                <p class="mt-3 max-w-md text-sm leading-relaxed text-gray-600 dark:text-gray-300">
                    Una muestra de proyectos web recientes con foco en conversión, rendimiento y experiencia de usuario.
                </p>
                <a href="{{ route('public.projects') }}" class="mt-6 inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-800 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-transparent dark:text-gray-200 dark:hover:bg-gray-900">
                    Ver todos los proyectos
                    <svg viewBox="0 0 20 20" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 10h11M11 6l4 4-4 4"/></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                @forelse ($featuredProjects as $project)
                    @php
                        $images = $project->images ?? [];
                        $coverImage = $images[0] ?? ($project->image_url ?? null);
                    @endphp
                    <article class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900/70">
                        <div class="aspect-[4/3] overflow-hidden bg-gray-100 dark:bg-gray-800">
                            @if ($coverImage)
                                @php $cover = \App\Support\ImageDerivatives::img($coverImage); @endphp
                                <img src="{{ $cover['src'] }}"
                                     @if($cover['srcset']) srcset="{{ $cover['srcset'] }}" sizes="(min-width: 768px) 33vw, 100vw" @endif
                                     @if($cover['width'] && $cover['height']) width="{{ $cover['width'] }}" height="{{ $cover['height'] }}" @endif
                                     alt="{{ $project->title }}" class="h-full w-full object-cover" loading="lazy" decoding="async">
                            @else
                                <div class="flex h-full items-center justify-center text-sm text-gray-400">Sin imagen</div>
                            @endif
                        </div>
                        <div class="space-y-3 p-4">
                            <div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ $project->title }}</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Plataforma web</p>
                            </div>
                            @if ($project->technologies->isNotEmpty())
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($project->technologies->take(3) as $technology)
                                        <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300">{{ $technology->name }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </article>
                @empty
                    <p class="col-span-full rounded-2xl border border-dashed border-gray-300 px-6 py-10 text-center text-sm text-gray-500 dark:border-gray-700">Aún no hay proyectos públicos para mostrar.</p>
                @endforelse
            </div>
        </div>

        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white md:text-3xl">Inversión</h2>
            <p class="mt-3 max-w-2xl text-sm leading-relaxed text-gray-600 dark:text-gray-300">
                Rangos orientativos para sitios, plataformas y e-commerce. El presupuesto final depende del alcance, integraciones y plazos.
            </p>

            <div class="mt-8 grid grid-cols-1 gap-5 overflow-visible xl:grid-cols-4 xl:pt-12">
                <article class="flex h-full flex-col rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900/70">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Web básica</p>
                    <p class="mt-2 text-2xl font-bold tracking-tight text-indigo-600 dark:text-indigo-400">Desde 190€</p>
                    <ul class="mt-4 flex-1 space-y-2.5 text-sm text-gray-600 dark:text-gray-300">
                        <li class="flex gap-2.5"><svg class="mt-0.5 h-4 w-4 shrink-0 text-indigo-600 dark:text-indigo-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M7.63 13.23 4.4 10l-1.4 1.41 4.63 4.62L17 6.66l-1.41-1.41z"/></svg>Sitio institucional con diseño a medida</li>
                        <li class="flex gap-2.5"><svg class="mt-0.5 h-4 w-4 shrink-0 text-indigo-600 dark:text-indigo-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M7.63 13.23 4.4 10l-1.4 1.41 4.63 4.62L17 6.66l-1.41-1.41z"/></svg>Hasta 5 páginas principales</li>
                        <li class="flex gap-2.5"><svg class="mt-0.5 h-4 w-4 shrink-0 text-indigo-600 dark:text-indigo-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M7.63 13.23 4.4 10l-1.4 1.41 4.63 4.62L17 6.66l-1.41-1.41z"/></svg>Formularios de contacto y captación</li>
                    </ul>
                </article>

                <div class="relative flex h-full flex-col overflow-visible xl:block">
                    <div class="flex shrink-0 items-center justify-center rounded-t-2xl rounded-b-none border border-b-0 border-indigo-600 bg-indigo-600 px-5 py-4 text-center shadow-sm xl:absolute xl:inset-x-0 xl:top-0 xl:z-10 xl:-translate-y-full">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-white">Más elegido</p>
                    </div>
                    <article class="flex h-full flex-col rounded-t-none rounded-b-2xl border border-indigo-600 bg-white shadow-sm dark:bg-gray-900/70">
                        <div class="flex flex-1 flex-col p-5">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">Plataforma / Web App</p>
                            <p class="mt-2 text-2xl font-bold tracking-tight text-indigo-600 dark:text-indigo-400">Desde 290€</p>
                            <ul class="mt-4 flex-1 space-y-2.5 text-sm text-gray-600 dark:text-gray-300">
                                <li class="flex gap-2.5"><svg class="mt-0.5 h-4 w-4 shrink-0 text-indigo-600 dark:text-indigo-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M7.63 13.23 4.4 10l-1.4 1.41 4.63 4.62L17 6.66l-1.41-1.41z"/></svg>Panel de administración y roles</li>
                                <li class="flex gap-2.5"><svg class="mt-0.5 h-4 w-4 shrink-0 text-indigo-600 dark:text-indigo-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M7.63 13.23 4.4 10l-1.4 1.41 4.63 4.62L17 6.66l-1.41-1.41z"/></svg>Integraciones con APIs y CRM</li>
                                <li class="flex gap-2.5"><svg class="mt-0.5 h-4 w-4 shrink-0 text-indigo-600 dark:text-indigo-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M7.63 13.23 4.4 10l-1.4 1.41 4.63 4.62L17 6.66l-1.41-1.41z"/></svg>Autenticación de usuarios y despliegue</li>
                            </ul>
                        </div>
                    </article>
                </div>

                <article class="flex h-full flex-col rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900/70">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">E-commerce</p>
                    <p class="mt-2 text-2xl font-bold tracking-tight text-indigo-600 dark:text-indigo-400">Desde 500€</p>
                    <ul class="mt-4 flex-1 space-y-2.5 text-sm text-gray-600 dark:text-gray-300">
                        <li class="flex gap-2.5"><svg class="mt-0.5 h-4 w-4 shrink-0 text-indigo-600 dark:text-indigo-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M7.63 13.23 4.4 10l-1.4 1.41 4.63 4.62L17 6.66l-1.41-1.41z"/></svg>Catálogo de productos y variantes</li>
                        <li class="flex gap-2.5"><svg class="mt-0.5 h-4 w-4 shrink-0 text-indigo-600 dark:text-indigo-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M7.63 13.23 4.4 10l-1.4 1.41 4.63 4.62L17 6.66l-1.41-1.41z"/></svg>Carrito, pagos y gestión de pedidos</li>
                        <li class="flex gap-2.5"><svg class="mt-0.5 h-4 w-4 shrink-0 text-indigo-600 dark:text-indigo-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M7.63 13.23 4.4 10l-1.4 1.41 4.63 4.62L17 6.66l-1.41-1.41z"/></svg>Stock, envíos e impuestos configurables</li>
                    </ul>
                </article>

                <aside class="flex h-full flex-col justify-between rounded-2xl bg-[#111827] p-6 text-white dark:bg-[#0b1024]">
                    <div>
                        <h3 class="text-xl font-bold">¿Listo para empezar?</h3>
                        <p class="mt-3 text-sm leading-relaxed text-slate-300">Cuéntame tu proyecto y te devuelvo una propuesta clara con alcance, tiempos y próximos pasos.</p>
                    </div>
                    <div class="mt-6 space-y-4">
                        <a href="{{ $contactUrl }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-indigo-500">
                            <svg viewBox="0 0 24 24" class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-4l-4 4v-4Z" />
                            </svg>
                            Abrir asistente de contacto
                        </a>
                        @if ($whatsappUrl)
                            <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-white/20 bg-white/5 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                                <svg viewBox="0 0 24 24" class="h-5 w-5 shrink-0" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.435 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
                                Escribir por WhatsApp
                            </a>
                        @endif
                        <ul class="space-y-2 text-sm text-slate-300">
                            <li class="flex items-center gap-2"><svg class="h-4 w-4 text-emerald-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M7.63 13.23 4.4 10l-1.4 1.41 4.63 4.62L17 6.66l-1.41-1.41z"/></svg>Respuesta en menos de 24hs</li>
                            <li class="flex items-center gap-2"><svg class="h-4 w-4 text-emerald-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M7.63 13.23 4.4 10l-1.4 1.41 4.63 4.62L17 6.66l-1.41-1.41z"/></svg>Primera consulta sin costo</li>
                        </ul>
                    </div>
                </aside>
            </div>
        </div>

        <div class="mx-auto grid grid-cols-1 gap-8 lg:max-w-3xl">
            <article class="rounded-2xl border border-gray-200 bg-white p-7 dark:border-gray-800 dark:bg-gray-900/70">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Preguntas frecuentes</h2>
                <div class="cc-faq-group mt-6" data-faq-group>
                    <x-faq-item question="¿Trabajas con webs nuevas o rediseños?">
                        Ambas opciones. Primero evalúo si conviene optimizar lo existente o reconstruir para ganar rendimiento y escalabilidad.
                    </x-faq-item>
                    <x-faq-item question="¿Incluyes mantenimiento?">
                        Sí, puedo incluir soporte evolutivo para mejoras, contenidos y nuevas integraciones.
                    </x-faq-item>
                    <x-faq-item question="¿Cómo se define el precio?">
                        Según alcance, complejidad e integraciones. Te doy una propuesta clara con fases y entregables.
                    </x-faq-item>
                </div>
            </article>

            @if(false)
            <article class="rounded-2xl border border-gray-200 bg-gray-50 p-7 dark:border-gray-800 dark:bg-gray-900/70">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Lo que dicen mis clientes</h2>
                <blockquote class="mt-6 border-l-4 border-indigo-500 pl-5 text-base leading-relaxed text-gray-700 dark:text-gray-300">
                    “Carlos entendió rápido lo que necesitábamos y nos entregó una web clara, rápida y fácil de mantener. El proceso fue transparente de principio a fin.”
                </blockquote>
                <div class="mt-6 flex items-center gap-4">
                    <img src="https://ui-avatars.com/api/?name=Maria+Gonzalez&background=4f46e5&color=fff" alt="María González" class="h-12 w-12 rounded-full object-cover" loading="lazy">
                    <div>
                        <div class="flex items-center gap-1 text-amber-400" aria-label="5 de 5 estrellas">
                            @for ($i = 0; $i < 5; $i++)
                                <svg viewBox="0 0 20 20" class="h-4 w-4 fill-current" aria-hidden="true"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292Z"/></svg>
                            @endfor
                        </div>
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">María González</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Directora de Marketing</p>
                    </div>
                </div>
            </article>
            @endif
        </div>

        <div class="grid grid-cols-1 gap-4 border-t border-gray-200 pt-10 sm:grid-cols-2 lg:grid-cols-4 dark:border-gray-800">
            <div class="flex items-start gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-300">
                    <svg viewBox="0 0 20 20" class="h-5 w-5 fill-current" aria-hidden="true"><path d="M7.63 13.23 4.4 10l-1.4 1.41 4.63 4.62L17 6.66l-1.41-1.41z"/></svg>
                </span>
                <div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Rendimiento</p>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Sitios rápidos y optimizados.</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-300">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m20 20-3.5-3.5"/></svg>
                </span>
                <div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">SEO técnico</p>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Estructura lista para posicionar.</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-300">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="5" y="11" width="14" height="10" rx="2"/><path stroke-linecap="round" d="M8 11V8a4 4 0 1 1 8 0v3"/></svg>
                </span>
                <div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Seguridad</p>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Buenas prácticas y actualizaciones.</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-300">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M3.3 7.7 12 12.5l8.7-4.8M12 22V12.5"/></svg>
                </span>
                <div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Escalabilidad</p>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Diseñado para crecer contigo.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
