@extends('layouts.public')

@section('title', 'Proyectos de desarrollo | Carlos Codex')
@section('meta_description', 'Selección de proyectos de desarrollo web, aplicaciones y soluciones digitales creadas para clientes y necesidades reales.')
@section('canonical', $projects->url($projects->currentPage()))

@section('body-class', 'antialiased font-sans flex flex-col min-h-dynamic transition-colors duration-300 text-gray-900 dark:text-gray-100 about-ai-dots-page projects-ai-dots-page')

@section('content')
@php
    $projectCount = $projects->count();
    $gridClass = match (true) {
        $projectCount === 0 => 'grid grid-cols-1 gap-10',
        $projectCount === 1 => 'grid grid-cols-1 gap-10 max-w-lg mx-auto w-full',
        $projectCount === 2 => 'grid grid-cols-1 gap-10 md:grid-cols-2',
        $projectCount === 3 => 'grid grid-cols-1 gap-10 md:grid-cols-3',
        $projectCount === 4 => 'grid grid-cols-1 gap-10 md:grid-cols-2',
        $projectCount === 5 => 'grid grid-cols-1 gap-10 md:grid-cols-2 lg:grid-cols-6',
        $projectCount === 6 => 'grid grid-cols-1 gap-10 md:grid-cols-2 lg:grid-cols-3',
        $projectCount === 7 => 'grid grid-cols-1 gap-10 md:grid-cols-2 lg:grid-cols-6',
        $projectCount === 8 => 'grid grid-cols-1 gap-10 md:grid-cols-2 lg:grid-cols-6',
        default => 'grid grid-cols-1 gap-10 md:grid-cols-2 lg:grid-cols-3',
    };
@endphp
<div class="relative w-full min-h-dynamic overflow-x-hidden bg-transparent dark:bg-transparent">
    {{-- section: el fondo acompaña la altura del documento (viewport cortaba al hacer scroll) --}}
    <div class="pointer-events-none absolute inset-0 z-0 overflow-hidden" aria-hidden="true">
        <x-ai-dots-background variant="section" :hover-only="true" :spacing="22" />
    </div>
    <!-- public-page-top: evitar que el header fijo tape el contenido -->
    <div class="relative z-10 max-w-screen-xl px-4 mx-auto public-page-top pb-16 min-h-dynamic">

    <div class="text-center mb-16">
            <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white">Echa un vistazo a mis trabajos</h1>
        <p class="mt-4 text-lg text-gray-500 dark:text-gray-400">Explora algunos de mis trabajos y desarrollos.</p>
        <div class="w-24 h-1 bg-indigo-600 mx-auto mt-6 rounded"></div>
    </div>

    <div class="{{ $gridClass }}">
        @forelse($projects as $index => $project)
            @php
                $i = $index + 1;
                $cellClass = 'min-w-0 h-full';
                if ($projectCount === 5 && $i <= 3) {
                    $cellClass .= ' lg:col-span-2';
                } elseif ($projectCount === 5 && $i === 4) {
                    $cellClass .= ' lg:col-span-2 lg:col-start-2';
                } elseif ($projectCount === 5 && $i === 5) {
                    $cellClass .= ' lg:col-span-2 lg:col-start-4';
                } elseif ($projectCount === 7) {
                    if ($i <= 3) {
                        $cellClass .= ' lg:col-span-2';
                    } elseif ($i === 4) {
                        $cellClass .= ' lg:col-span-2 lg:col-start-2';
                    } elseif ($i === 5) {
                        $cellClass .= ' lg:col-span-2 lg:col-start-4';
                    } elseif ($i === 6) {
                        $cellClass .= ' lg:col-span-2 lg:col-start-2 lg:row-start-3';
                    } else {
                        $cellClass .= ' lg:col-span-2 lg:col-start-4 lg:row-start-3';
                    }
                } elseif ($projectCount === 8) {
                    if ($i <= 3) {
                        $cellClass .= ' lg:col-span-2';
                    } elseif ($i <= 6) {
                        $cellClass .= ' lg:col-span-2';
                    } elseif ($i === 7) {
                        $cellClass .= ' lg:col-span-2 lg:col-start-2 lg:row-start-3';
                    } else {
                        $cellClass .= ' lg:col-span-2 lg:col-start-4 lg:row-start-3';
                    }
                }
            @endphp
            <div class="{{ $cellClass }}">
                <x-project-card :project="$project" :include-private-links="true" />
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500 dark:text-gray-400 text-lg">No hay proyectos públicos para mostrar en este momento.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-12">
        {{ $projects->links() }}
    </div>
    </div>
</div>
@endsection
