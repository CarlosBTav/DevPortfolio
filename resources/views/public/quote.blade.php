@extends('layouts.public')

@section('title', 'Presupuesto | Carlos Codex')
@section('meta_description', 'Presupuesto privado compartido por enlace directo.')
@section('robots', 'noindex, nofollow, noarchive')

@section('body-class', 'antialiased font-sans flex flex-col min-h-dynamic transition-colors duration-300 text-gray-900 dark:text-gray-100 about-ai-dots-page')

@section('content')
<div class="relative min-h-dynamic overflow-x-hidden bg-transparent dark:bg-transparent">
    <x-ai-dots-background variant="viewport" />

    <section class="relative z-10 public-page-top pb-20">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-3xl border border-gray-200 bg-white/90 p-8 shadow-sm dark:border-gray-700 dark:bg-gray-800/80">
            <p class="text-xs uppercase tracking-[0.18em] text-indigo-600 dark:text-indigo-300">Presupuesto</p>
            <h1 class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $quote['title'] ?? 'Propuesta' }}</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                Actualizado: <span class="font-medium">{{ $quote['updated_at'] ?? now()->toDateString() }}</span>
            </p>
        </div>

        @php
            $items = collect($quote['items'] ?? []);
            $total = $items->sum(fn ($item) => (float) ($item['price'] ?? 0));
            $currency = $quote['currency'] ?? 'EUR';
        @endphp

        <div class="mt-8 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/60">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">Servicio</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">Descripción</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">Precio</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($items as $item)
                            <tr>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $item['name'] ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $item['description'] ?? '-' }}</td>
                                <td class="px-6 py-4 text-right text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ number_format((float) ($item['price'] ?? 0), 2, ',', '.') }} {{ $currency }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-300">
                                    No hay líneas de presupuesto para esta versión.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-50 dark:bg-gray-900/60">
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-right text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-300">Total</td>
                            <td class="px-6 py-4 text-right text-base font-bold text-indigo-600 dark:text-indigo-300">
                                {{ number_format($total, 2, ',', '.') }} {{ $currency }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        @if(!empty($quote['notes']) && is_array($quote['notes']))
            <div class="mt-8 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h2 class="text-sm font-semibold uppercase tracking-[0.15em] text-gray-500 dark:text-gray-300">Condiciones</h2>
                <ul class="mt-3 space-y-2 text-sm text-gray-700 dark:text-gray-200">
                    @foreach($quote['notes'] as $note)
                        <li>- {{ $note }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
    </section>
</div>
@endsection
