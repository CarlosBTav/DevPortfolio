@extends('layouts.public')

@section('title', 'Servicios | Carlos Codex')
@section('meta_description', 'Servicios de desarrollo web, apps y soluciones a medida para convertir ideas en productos digitales reales.')
@section('body-class', 'antialiased bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100 font-sans flex flex-col min-h-dynamic transition-colors duration-300')

<style>

    /* CTA inferior: panel claro / oscuro + icono agenda + botón con borde luminoso */
    html:not(.dark) .services-bottom-cta {
        background-color: #ffffff;
        border: 1px solid rgba(99, 102, 241, 0.18);
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05);
    }

    html.dark .services-bottom-cta {
        background-color: #111133;
        border: 1px solid rgba(129, 140, 248, 0.22);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04);
    }

    .services-bottom-cta__icon-ring {
        width: 3.5rem;
        height: 3.5rem;
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    html:not(.dark) .services-bottom-cta__icon-ring {
        background-color: #e0e7ff;
    }

    html.dark .services-bottom-cta__icon-ring {
        background-color: #9999ff;
    }

    html:not(.dark) .services-bottom-cta__icon-ring svg {
        color: #4338ca;
    }

    html.dark .services-bottom-cta__icon-ring svg {
        color: #0b0b2a;
    }

    html:not(.dark) .services-bottom-cta__btn {
        color: #ffffff;
        background: #4f46e5;
        border: 1px solid rgba(79, 70, 229, 0.35);
        box-shadow: 0 10px 24px rgba(79, 70, 229, 0.18);
    }

    html:not(.dark) .services-bottom-cta__btn:hover {
        background: #4338ca;
        border-color: rgba(67, 56, 202, 0.45);
        box-shadow: 0 12px 28px rgba(79, 70, 229, 0.24);
    }

    html.dark .services-bottom-cta__btn {
        color: #fff;
        background: rgba(10, 12, 45, 0.45);
        border: 1px solid rgba(255, 255, 255, 0.28);
        box-shadow:
            0 0 0 1px rgba(199, 210, 254, 0.12),
            0 0 14px rgba(255, 255, 255, 0.08),
            0 0 28px rgba(129, 140, 248, 0.12);
    }

    html.dark .services-bottom-cta__btn:hover {
        border-color: rgba(255, 255, 255, 0.55);
        background: rgba(20, 24, 70, 0.55);
        box-shadow:
            0 0 0 1px rgba(226, 232, 255, 0.35),
            0 0 18px rgba(255, 255, 255, 0.14),
            0 0 36px rgba(165, 180, 252, 0.22);
    }

    .services-bottom-cta__btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.75rem 1.35rem;
        border-radius: 9999px;
        font-weight: 600;
        font-size: 0.9375rem;
        text-decoration: none;
        transition: border-color 0.25s ease, box-shadow 0.25s ease, background-color 0.25s ease;
    }

    .services-bottom-cta__btn:focus-visible {
        outline: 2px solid rgba(199, 210, 254, 0.75);
        outline-offset: 3px;
    }
</style>

@php
    $processSteps = [
        [
            'title' => '01. Descubrimiento',
            'description' => 'Entiendo tu idea, objetivo y necesidades.',
            'icon' => '<svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m20 20-3.5-3.5"/></svg>',
        ],
        [
            'title' => '02. Propuesta',
            'description' => 'Te presento solución, plan y presupuesto.',
            'icon' => '<svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"/><path stroke-linecap="round" d="M9 12h6M9 16h6"/></svg>',
        ],
        [
            'title' => '03. Desarrollo',
            'description' => 'Construyo, pruebo y te mantengo al tanto.',
            'icon' => '<svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m8 9-3 3 3 3"/><path stroke-linecap="round" stroke-linejoin="round" d="m16 15 3-3-3-3"/></svg>',
        ],
        [
            'title' => '04. Entrega y soporte',
            'description' => 'Lanzo, asesoro y sigo acompañándote.',
            'icon' => '<svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09Z"/><path stroke-linecap="round" stroke-linejoin="round" d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"/></svg>',
        ],
    ];
@endphp

@section('content')
{{-- Página servicios: fondo full-bleed + contenido anclado al max-width --}}
<section class="relative min-h-screen w-full pt-32 pb-20 lg:pt-36 text-slate-900 dark:text-slate-100">
    {{-- Capas de fondo (sólido + gradientes decorativos): claro / oscuro --}}
    <div class="pointer-events-none absolute inset-0 bg-slate-50 dark:bg-slate-950"></div>
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_20%_18%,rgba(129,140,248,0.18),transparent_42%),radial-gradient(circle_at_80%_28%,rgba(139,92,246,0.14),transparent_38%),linear-gradient(180deg,rgba(248,250,252,0.98)_0%,rgba(241,245,249,1)_100%)] dark:bg-[radial-gradient(circle_at_20%_18%,rgba(129,140,248,0.25),transparent_42%),radial-gradient(circle_at_80%_28%,rgba(139,92,246,0.20),transparent_38%),linear-gradient(180deg,rgba(6,12,36,0.94)_0%,rgba(4,8,28,0.98)_100%)]"></div>

    <div class="relative isolate z-10 max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-10 space-y-6 py-2 md:py-4">
                {{-- Hero: copy + ilustración desktop (xl) --}}
                <div class="relative z-0 grid grid-cols-1 xl:grid-cols-2 gap-8 items-center">
                    {{-- Columna texto y badges --}}
                    <div class="relative z-20">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-indigo-600 dark:text-indigo-300">Servicios</p>
                        <h1 class="mt-3 text-3xl md:text-5xl font-extrabold leading-tight text-slate-900 dark:text-white max-w-2xl">
                            Soluciones digitales a medida para tu negocio
                        </h1>
                        <p class="mt-4 text-slate-600 dark:text-slate-300 text-base md:text-lg max-w-2xl">
                            Desarrollo webs, apps y productos escalables con enfoque en negocio, claridad técnica y resultados reales.
                        </p>
                        <div class="mt-6 flex flex-wrap items-center gap-5 text-sm text-slate-700 dark:text-slate-200">
                            <div class="inline-flex items-center gap-2">
                                <span class="inline-flex h-5 w-5 items-center justify-center rounded-full border border-indigo-400/60 text-indigo-700 dark:border-indigo-300/70 dark:text-indigo-200">
                                    <svg viewBox="0 0 20 20" class="h-3.5 w-3.5 fill-current" aria-hidden="true"><path d="M7.63 13.23 4.4 10l-1.4 1.41 4.63 4.62L17 6.66l-1.41-1.41z"/></svg>
                                </span>
                                Código limpio y escalable
                            </div>
                            <div class="inline-flex items-center gap-2">
                                <span class="inline-flex h-5 w-5 items-center justify-center rounded-full border border-indigo-400/60 text-indigo-700 dark:border-indigo-300/70 dark:text-indigo-200">
                                    <svg viewBox="0 0 20 20" class="h-3.5 w-3.5 fill-current" aria-hidden="true"><path d="M7.63 13.23 4.4 10l-1.4 1.41 4.63 4.62L17 6.66l-1.41-1.41z"/></svg>
                                </span>
                                Entrega a tiempo
                            </div>
                        </div>
                    </div>

                    @include('public.services.partials.hero-mockup')

                </div>

                {{-- Grid principal: dos cards de servicios --}}
                <div class="relative z-30 grid grid-cols-1 lg:grid-cols-2 gap-5">
                    {{-- Card: desarrollo web --}}
                    <article class="rounded-2xl border border-indigo-200/80 bg-white shadow-sm dark:border-indigo-400/20 dark:bg-[#0f172a] dark:shadow-none p-6 md:p-7">
                        <div class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-200">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M2.8 9h18.4M2.8 15h18.4M12 3a15.3 15.3 0 0 1 0 18M12 3a15.3 15.3 0 0 0 0 18"/></svg>
                        </div>
                        <h2 class="mt-4 text-2xl font-bold text-slate-900 dark:text-white">Desarrollo Web</h2>
                        <p class="mt-3 text-slate-600 dark:text-slate-300">
                            Sitios y aplicaciones web modernas, rápidas y optimizadas para convertir visitantes en clientes.
                        </p>
                        <ul class="mt-5 list-none space-y-3 text-sm text-slate-600 dark:text-slate-300">
                            <li class="flex items-start gap-3">
                                <span class="mt-0.5 inline-flex h-6 w-6 shrink-0 items-center justify-center rounded border border-indigo-300/55 bg-indigo-50 text-indigo-700 dark:border-indigo-500/35 dark:bg-indigo-950 dark:text-indigo-200" aria-hidden="true">
                                    <svg viewBox="0 0 20 20" class="h-4 w-4 fill-current"><path d="M7.63 13.23 4.4 10l-1.4 1.41 4.63 4.62L17 6.66l-1.41-1.41z"/></svg>
                                </span>
                                <span>Páginas institucionales y landing pages</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="mt-0.5 inline-flex h-6 w-6 shrink-0 items-center justify-center rounded border border-indigo-300/55 bg-indigo-50 text-indigo-700 dark:border-indigo-500/35 dark:bg-indigo-950 dark:text-indigo-200" aria-hidden="true">
                                    <svg viewBox="0 0 20 20" class="h-4 w-4 fill-current"><path d="M7.63 13.23 4.4 10l-1.4 1.41 4.63 4.62L17 6.66l-1.41-1.41z"/></svg>
                                </span>
                                <span>E-commerce y tiendas online</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="mt-0.5 inline-flex h-6 w-6 shrink-0 items-center justify-center rounded border border-indigo-300/55 bg-indigo-50 text-indigo-700 dark:border-indigo-500/35 dark:bg-indigo-950 dark:text-indigo-200" aria-hidden="true">
                                    <svg viewBox="0 0 20 20" class="h-4 w-4 fill-current"><path d="M7.63 13.23 4.4 10l-1.4 1.41 4.63 4.62L17 6.66l-1.41-1.41z"/></svg>
                                </span>
                                <span>Web apps y dashboards</span>
                            </li>
                        </ul>
                        <a href="{{ route('public.services.web') }}" class="mt-6 inline-flex w-full items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 font-semibold text-white transition hover:bg-indigo-500">
                            Ver más sobre desarrollo web
                        </a>
                    </article>

                    {{-- Card: desarrollo de apps --}}
                    <article class="rounded-2xl border border-indigo-200/80 bg-white shadow-sm dark:border-indigo-400/20 dark:bg-[#0f172a] dark:shadow-none p-6 md:p-7">
                        <div class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-200">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="7" y="2.5" width="10" height="19" rx="2.5"/><path d="M11 18.5h2"/></svg>
                        </div>
                        <h2 class="mt-4 text-2xl font-bold text-slate-900 dark:text-white">Desarrollo de Apps</h2>
                        <p class="mt-3 text-slate-600 dark:text-slate-300">
                            Apps móviles nativas y multiplataforma para iOS y Android, listas para escalar tu idea.
                        </p>
                        <ul class="mt-5 list-none space-y-3 text-sm text-slate-600 dark:text-slate-300">
                            <li class="flex items-start gap-3">
                                <span class="mt-0.5 inline-flex h-6 w-6 shrink-0 items-center justify-center rounded border border-indigo-300/55 bg-indigo-50 text-indigo-700 dark:border-indigo-500/35 dark:bg-indigo-950 dark:text-indigo-200" aria-hidden="true">
                                    <svg viewBox="0 0 20 20" class="h-4 w-4 fill-current"><path d="M7.63 13.23 4.4 10l-1.4 1.41 4.63 4.62L17 6.66l-1.41-1.41z"/></svg>
                                </span>
                                <span>Apps nativas y multiplataforma</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="mt-0.5 inline-flex h-6 w-6 shrink-0 items-center justify-center rounded border border-indigo-300/55 bg-indigo-50 text-indigo-700 dark:border-indigo-500/35 dark:bg-indigo-950 dark:text-indigo-200" aria-hidden="true">
                                    <svg viewBox="0 0 20 20" class="h-4 w-4 fill-current"><path d="M7.63 13.23 4.4 10l-1.4 1.41 4.63 4.62L17 6.66l-1.41-1.41z"/></svg>
                                </span>
                                <span>APIs y conexiones con servicios</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="mt-0.5 inline-flex h-6 w-6 shrink-0 items-center justify-center rounded border border-indigo-300/55 bg-indigo-50 text-indigo-700 dark:border-indigo-500/35 dark:bg-indigo-950 dark:text-indigo-200" aria-hidden="true">
                                    <svg viewBox="0 0 20 20" class="h-4 w-4 fill-current"><path d="M7.63 13.23 4.4 10l-1.4 1.41 4.63 4.62L17 6.66l-1.41-1.41z"/></svg>
                                </span>
                                <span>Publicación en stores</span>
                            </li>
                        </ul>
                        <a href="{{ route('public.services.app') }}" class="mt-6 inline-flex w-full items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 font-semibold text-white transition hover:bg-indigo-500">
                            Ver más sobre apps
                        </a>
                    </article>
                </div>

                {{-- Proceso de trabajo con el cliente --}}
                <div class="relative z-30 rounded-2xl border border-indigo-200/80 bg-slate-100 dark:border-indigo-400/20 dark:bg-slate-950 p-5 md:p-6">
                    <h3 class="text-center text-xl font-bold text-slate-900 dark:text-white">Así trabajo contigo</h3>
                    <x-process-steps :steps="$processSteps" />
                </div>


                {{-- Resumen FAQ + enlace a página completa --}}
                <div class="relative z-30 rounded-[20px] border border-indigo-200/80 bg-white px-5 py-8 shadow-sm sm:px-8 sm:py-9 lg:px-10 lg:py-10 dark:border-indigo-400/25 dark:bg-[#080c24] dark:shadow-[inset_0_1px_0_rgba(255,255,255,0.06)]">
                    <h2 class="text-xl font-bold text-slate-900 sm:text-2xl dark:text-white">Preguntas frecuentes</h2>
                    <p class="mt-2 max-w-2xl text-sm text-slate-600 dark:text-slate-400">Respuestas rápidas antes de escribirme. La lista completa está organizada por tipo de proyecto.</p>
                    <div class="cc-faq-group mt-6" data-faq-group>
                        <x-faq-item question="¿Cuánto tarda un proyecto?">
                            <p>Depende del tipo de proyecto:</p>
                            <ul class="mt-3 list-disc space-y-2 pl-5">
                                <li><strong>Webs básicas:</strong> prototipo en 1-2 días; después, unos días de pulido e integración de tu feedback.</li>
                                <li><strong>Tienda online:</strong> prototipo en 2-3 días; luego, alrededor de una semana de pulido e integración de feedback.</li>
                                <li><strong>ERP/CRM:</strong> según la complejidad; el prototipo puede empezar desde el primer día.</li>
                            </ul>
                        </x-faq-item>
                        <x-faq-item question="¿Cómo trabajamos?">
                            Fases de descubrimiento, propuesta, desarrollo con revisiones y entrega. Mantienes visibilidad del progreso y puedes dar feedback en cada etapa.
                        </x-faq-item>
                        <x-faq-item question="¿Puedo pagar por fases?">
                            Sí: en la propuesta se pueden definir pagos ligados a hitos o entregas, para que encaje con tu flujo de caja.
                        </x-faq-item>
                    </div>
                    <p class="mt-6">
                        <a href="{{ route('public.services.faq') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 transition hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                            Ver más preguntas frecuentes
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 10h11M11 6l4 4-4 4"/></svg>
                        </a>
                    </p>
                </div>

                {{-- CTA final: panel con brillo; acciones → página de contacto (id para #servicios-contacto) --}}
                <div id="servicios-contacto" class="services-bottom-cta relative z-30 flex flex-col gap-6 rounded-[20px] px-6 py-7 sm:px-9 sm:py-8 md:flex-row md:items-center md:justify-between md:gap-8">
                    <div class="flex min-w-0 flex-1 items-center gap-4 sm:gap-5">
                        <div class="services-bottom-cta__icon-ring" aria-hidden="true">
                            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <path d="M16 2v4M8 2v4M3 10h18"></path>
                                <path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01"></path>
                            </svg>
                        </div>
                        <div class="min-w-0 space-y-1">
                            <h3 class="text-xl font-bold leading-snug text-slate-900 sm:text-2xl dark:text-white">
                                ¿Listo para llevar tu negocio al siguiente nivel?
                            </h3>
                            <p class="text-sm leading-relaxed text-slate-600 sm:text-[0.95rem] dark:text-slate-300">
                                Abre el asistente de contacto y cuéntame tu caso; te respondo en menos de 24 horas.
                            </p>
                        </div>
                    </div>
                    <div class="flex w-full shrink-0 flex-col gap-3 sm:w-auto md:items-end">
                        <a href="{{ route('public.contact') }}" class="services-bottom-cta__btn self-stretch text-center md:self-auto md:whitespace-nowrap">
                            Empezemos tu proyecto
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                        @if(filled(config('contact.email')))
                            <a href="mailto:{{ config('contact.email') }}" class="inline-flex w-full items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-800 transition hover:bg-slate-50 dark:border-white/10 dark:bg-transparent dark:text-slate-200 dark:hover:bg-white/5 sm:w-auto">
                                Correo
                            </a>
                        @endif
                    </div>
                </div>
    </div>
</section>

@endsection
