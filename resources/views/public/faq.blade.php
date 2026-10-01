@extends('layouts.public')

@section('title', 'Preguntas frecuentes | Carlos Codex')
@section('meta_description', 'Respuestas sobre desarrollo web, apps móviles y disponibilidad para contrataciones: plazos, precios, mantenimiento, modalidades y publicación en tiendas.')

@php
    $faqCategories = [
        [
            'id' => 'web',
            'label' => 'Web',
            'short' => 'Web',
            'description' => 'Sitios, tiendas online y herramientas en el navegador.',
            'items' => [
                ['q' => '¿Trabajas con webs nuevas o rediseños?', 'a' => 'Ambas opciones. Primero evalúo si conviene optimizar lo existente o reconstruir para ganar rendimiento y escalabilidad.'],
                ['q' => '¿Incluyes mantenimiento en proyectos web?', 'a' => 'Sí, puedo incluir soporte evolutivo para mejoras, contenidos y nuevas integraciones.'],
                ['q' => '¿Cómo se define el precio de una web o tienda?', 'a' => 'Según alcance, diseño, integraciones y catálogo. Te doy una propuesta clara con fases y entregables.'],
                ['q' => '¿Cuánto suele tardar un proyecto web?', 'a' => 'Una landing o web pequeña puede ser cuestión de semanas; e-commerce y plataformas a medida suelen llevar más. Los hitos y fechas quedan reflejados en la propuesta.'],
                ['q' => '¿Qué necesito aportar para arrancar?', 'a' => 'Contenidos o briefing de textos, branding si lo hay y acceso a dominio/hosting cuando toque integrar o desplegar. Si falta algo, te indico opciones.'],
            ],
        ],
        [
            'id' => 'phone-app',
            'label' => 'App móvil',
            'short' => 'App móvil',
            'description' => 'Apps para iOS, Android y productos relacionados.',
            'items' => [
                ['q' => '¿Trabajas solo Android?', 'a' => 'Puedo trabajar Android nativo y también propuestas multiplataforma según objetivos, presupuesto y plazos.'],
                ['q' => '¿Puedes mejorar una app existente?', 'a' => 'Sí. Audito el estado actual y planteo un plan progresivo para mejorar rendimiento, UX y arquitectura.'],
                ['q' => '¿También haces backend para la app?', 'a' => 'Si necesitas APIs, panel de administración o integraciones, puedo cubrir la solución de extremo a extremo.'],
                ['q' => '¿Qué implica publicar en App Store y Google Play?', 'a' => 'Cuentas de desarrollador, assets, políticas de las tiendas y revisiones. Te guío en el proceso y preparo builds listos para enviar.'],
                ['q' => '¿Cuánto tarda un MVP móvil?', 'a' => 'Depende del alcance y de si hay backend nuevo. Priorizamos un núcleo usable y bien probado antes de escalar funcionalidades.'],
            ],
        ],
        [
            'id' => 'empleabilidad',
            'label' => 'Empleabilidad',
            'short' => 'Empleabilidad',
            'description' => 'Disponibilidad para contrataciones en empresa, modalidades y condiciones.',
            'items' => [
                ['q' => '¿Estás disponible para contrataciones en una empresa?', 'a' => 'Sí. Estoy abierto a incorporaciones en empresa siempre que el proyecto y las condiciones encajen. Puedo compaginarlo con clientes propios o trabajar en exclusiva, lo hablamos.'],
                ['q' => '¿Trabajas en remoto, híbrido o presencial?', 'a' => 'Prefiero remoto o híbrido por productividad, pero valoro propuestas presenciales según ubicación y proyecto.'],
                ['q' => '¿Qué tipo de contrato te interesa?', 'a' => 'Indefinido, por proyecto o como freelance facturando. Lo importante es que el alcance y la dedicación estén claros desde el principio.'],
                ['q' => '¿Cuál es tu disponibilidad horaria?', 'a' => 'Jornada completa o parcial según el caso. Soy flexible con franjas horarias siempre que haya una ventana razonable de solape con el equipo.'],
                ['q' => '¿Puedes enviar CV y referencias?', 'a' => 'Sí. Tengo CV actualizado y puedo aportar referencias de proyectos y clientes anteriores. Escríbeme y te paso enlace directo.'],
            ],
        ],
    ];

    $defaultCategoryId = 'web';
@endphp

@push('structured-data')
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => collect($faqCategories)
        ->flatMap(fn ($category) => $category['items'])
        ->map(fn ($item) => [
            '@type' => 'Question',
            'name' => $item['q'],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $item['a'],
            ],
        ])
        ->values()
        ->all(),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
<section class="relative mx-3 public-page-top pb-24 md:mx-6 lg:mx-10">
    <div
        x-data="{
            active: (new URLSearchParams(window.location.search).get('cat')) || @js($defaultCategoryId),
            order: @js(array_column($faqCategories, 'id')),
            setActive(id) {
                this.active = id;
                const url = new URL(window.location.href);
                url.searchParams.set('cat', id);
                window.history.replaceState({}, '', url);
            },
            nextId(currentId) {
                const index = this.order.indexOf(currentId);
                if (index === -1) return this.order[0];
                return this.order[(index + 1) % this.order.length];
            },
        }"
        class="mx-auto max-w-screen-xl px-4"
    >
        <nav class="text-sm text-gray-600 dark:text-gray-400" aria-label="Migas de pan">
            <a href="{{ route('home') }}" class="font-medium text-indigo-600 transition hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">Inicio</a>
            <span class="mx-2 text-gray-400" aria-hidden="true">/</span>
            <a href="{{ route('public.services') }}" class="font-medium text-indigo-600 transition hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">Servicios</a>
            <span class="mx-2 text-gray-400" aria-hidden="true">/</span>
            <span class="text-gray-900 dark:text-gray-200">Preguntas frecuentes</span>
        </nav>

        <header class="mt-8 rounded-3xl border border-gray-200 bg-white/90 p-8 shadow-sm dark:border-gray-800 dark:bg-gray-900/85 md:p-12">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-400">Ayuda</p>
            <h1 class="mt-3 text-3xl font-extrabold leading-tight text-gray-900 dark:text-white md:text-4xl">
                Preguntas frecuentes
            </h1>
            <p class="mt-4 max-w-3xl text-lg text-gray-600 dark:text-gray-300">
                Elige una categoría para ver solo esas preguntas. Puedes saltar de una a otra cuando quieras.
            </p>

            <div class="mt-8 flex flex-wrap gap-3" role="tablist" aria-label="Categorías de preguntas frecuentes">
                @foreach ($faqCategories as $category)
                    <button
                        type="button"
                        role="tab"
                        :aria-selected="active === @js($category['id']) ? 'true' : 'false'"
                        :tabindex="active === @js($category['id']) ? 0 : -1"
                        @click="setActive(@js($category['id']))"
                        :class="active === @js($category['id'])
                            ? 'bg-indigo-600 text-white border-transparent shadow-sm hover:bg-indigo-700'
                            : 'border-gray-300 text-gray-800 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-800'"
                        class="inline-flex items-center gap-2 rounded-xl border px-5 py-2.5 text-sm font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:focus-visible:ring-offset-gray-900"
                    >
                        <x-faq-category-icon :category-id="$category['id']" class="h-5 w-5" />
                        {{ $category['label'] }}
                    </button>
                @endforeach
            </div>
        </header>

        <div id="cc-faq-list" class="mt-10 space-y-12 scroll-mt-32 lg:scroll-mt-36">
            @foreach ($faqCategories as $category)
                <article
                    id="{{ $category['id'] }}"
                    x-show="active === @js($category['id'])"
                    x-cloak
                    x-transition:enter="transition-opacity ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity ease-linear duration-0"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="rounded-2xl border border-gray-200 bg-white p-7 dark:border-gray-800 dark:bg-gray-900 md:p-8"
                >
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-200" aria-hidden="true">
                            <x-faq-category-icon :category-id="$category['id']" class="h-6 w-6" />
                        </span>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $category['label'] }}</h2>
                    </div>
                    <p class="mt-2 text-gray-600 dark:text-gray-300">{{ $category['description'] }}</p>

                    <div class="cc-faq-group mt-8" data-faq-group>
                        @foreach ($category['items'] as $item)
                            <x-faq-item :question="$item['q']">
                                {!! nl2br(e($item['a'])) !!}
                            </x-faq-item>
                        @endforeach
                    </div>

                    @php
                        $currentIndex = array_search($category['id'], array_column($faqCategories, 'id'), true);
                        $nextCategory = $faqCategories[($currentIndex + 1) % count($faqCategories)];
                    @endphp
                    <div class="mt-8 flex justify-center">
                        <button
                            type="button"
                            @click="setActive(nextId(@js($category['id'])))"
                            class="inline-flex items-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-5 py-2.5 text-sm font-semibold text-indigo-700 transition hover:bg-indigo-100 dark:border-indigo-400/30 dark:bg-indigo-500/10 dark:text-indigo-300 dark:hover:bg-indigo-500/20"
                        >
                            <x-faq-category-icon :category-id="$nextCategory['id']" class="h-4 w-4" />
                            Ver más preguntas sobre {{ $nextCategory['short'] }}
                            <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 10h11M11 6l4 4-4 4"/></svg>
                        </button>
                    </div>
                </article>
            @endforeach
        </div>

        <p class="mt-10 text-center text-sm text-gray-600 dark:text-gray-400">
            <a href="{{ route('home') }}" class="font-semibold text-indigo-600 transition hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">← Volver al inicio</a>
        </p>
    </div>
</section>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
