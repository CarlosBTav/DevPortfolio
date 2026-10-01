@extends('layouts.public')

@section('title', 'Documentación de proyecto | Carlos Codex')
@section('meta_description', 'Documentación privada de proyecto compartida por enlace directo.')
@section('robots', 'noindex, nofollow, noarchive')

@section('body-class', 'antialiased font-sans flex flex-col min-h-dynamic transition-colors duration-300 text-gray-900 dark:text-gray-100 about-ai-dots-page')

@section('content')
<div class="relative w-full min-h-dynamic overflow-x-hidden bg-transparent dark:bg-transparent">
    {{-- section: el fondo acompaña la altura del documento (viewport cortaba al hacer scroll) --}}
    <div class="pointer-events-none absolute inset-0 z-0 overflow-hidden" aria-hidden="true">
        <x-ai-dots-background variant="section" />
    </div>

    <section class="relative z-10 public-page-top pb-20">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8" data-documentation-client-notes>
            @php
                $documentationLogos = $documentation['logos'] ?? [];
            @endphp
            <div class="rounded-3xl border border-gray-200 bg-white/90 p-8 shadow-sm dark:border-gray-700 dark:bg-gray-800/80">
                <div class="flex flex-col gap-4 sm:gap-5">
                    <div class="flex items-start gap-4 sm:gap-5">
                        @if(! empty($documentationLogos['small']['src']))
                            <img
                                src="{{ $documentationLogos['small']['src'] }}"
                                alt="{{ $documentationLogos['small']['label'] ?? 'Favicon' }} — {{ $documentation['client_name'] ?? $documentation['title'] ?? 'Cliente' }}"
                                class="h-12 w-12 shrink-0 rounded-2xl object-contain sm:h-14 sm:w-14"
                                width="56"
                                height="56"
                                loading="lazy"
                                decoding="async"
                            />
                        @endif
                        <div class="min-w-0 flex-1 pt-0.5">
                            <p class="text-xs uppercase tracking-[0.18em] text-indigo-600 dark:text-indigo-300">Documentación compartida</p>
                            <h1 class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $documentation['title'] ?? 'Proyecto' }}</h1>
                        </div>
                    </div>
                    @if(!empty($documentation['related_documentation']))
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            <a href="{{ route('public.documentation', $documentation['related_documentation']['slug']) }}" class="font-medium text-indigo-600 underline decoration-indigo-300 underline-offset-2 hover:text-indigo-500 dark:text-indigo-300 dark:decoration-indigo-500/60 dark:hover:text-indigo-200">
                                {{ $documentation['related_documentation']['label'] }}
                            </a>
                        </p>
                    @endif
                    @if(!empty($documentation['domain']))
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            Dominio:
                            <a href="https://{{ $documentation['domain'] }}" target="_blank" rel="noopener noreferrer" class="font-medium text-indigo-600 underline decoration-indigo-300 underline-offset-2 hover:text-indigo-500 dark:text-indigo-300 dark:decoration-indigo-500/60 dark:hover:text-indigo-200">
                                {{ $documentation['domain'] }}
                            </a>
                        </p>
                    @endif
                    @if(!empty($documentation['development_url']))
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            URL de desarrollo:
                            <a href="{{ $documentation['development_url'] }}" target="_blank" rel="noopener noreferrer" class="font-medium text-indigo-600 underline decoration-indigo-300 underline-offset-2 hover:text-indigo-500 dark:text-indigo-300 dark:decoration-indigo-500/60 dark:hover:text-indigo-200">
                                {{ $documentation['development_url'] }}
                            </a>
                        </p>
                    @endif
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Actualizado: <span class="font-normal">{{ $documentation['updated_at'] ?? now()->toDateString() }}</span>
                    </p>
                </div>

                @if(! empty($documentationLogos['large']['src']))
                    <div class="mt-6 border-t border-gray-200 pt-6 dark:border-gray-600">
                        <h2 class="text-sm font-semibold uppercase tracking-[0.15em] text-gray-500 dark:text-gray-300">
                            Logotipos
                        </h2>
                        <div class="mt-4">
                            <p class="mb-2 text-xs font-medium text-gray-500 dark:text-gray-400">
                                {{ $documentationLogos['large']['label'] ?? 'Logo grande' }}
                            </p>
                            <div class="inline-block rounded-lg border border-gray-200 bg-gray-50/80 p-3 dark:border-gray-600 dark:bg-gray-700/40">
                                <img
                                    src="{{ $documentationLogos['large']['src'] }}"
                                    alt="Logo grande — {{ $documentation['client_name'] ?? $documentation['title'] ?? 'Cliente' }}"
                                    class="max-h-24 w-auto max-w-[min(100%,16rem)] object-contain sm:max-h-28"
                                    loading="lazy"
                                    decoding="async"
                                />
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            @include('partials.documentation-basic-info', [
                'basicInfo' => $documentation['basic_information'] ?? [],
            ])

            @php
                $allowClientNotes = $documentation['allow_client_notes'] ?? false;
                $pendingList = $documentation['pending_information'] ?? [];
            @endphp

            @if(!empty($documentation['confirmed_information']) && is_array($documentation['confirmed_information']))
                <div class="mt-8 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h2 class="text-sm font-semibold uppercase tracking-[0.15em] text-gray-500 dark:text-gray-300">
                        Información de la web
                    </h2>
                    <dl class="mt-4 space-y-3">
                        @foreach($documentation['confirmed_information'] as $item)
                            <div class="rounded-xl border border-gray-200 bg-gray-50/70 px-4 py-3 dark:border-gray-600 dark:bg-gray-700/40">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-300">
                                    {{ $item['label'] ?? 'Dato' }}
                                </dt>
                                <dd class="mt-1 text-sm text-gray-800 dark:text-gray-100">
                                    @if(!empty($item['href']))
                                        <span class="inline-flex flex-wrap items-center gap-2">
                                            @if(($item['icon'] ?? '') === 'instagram')
                                                <svg class="h-5 w-5 shrink-0 text-pink-600 dark:text-pink-400" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                                                </svg>
                                            @endif
                                            <a href="{{ $item['href'] }}" target="_blank" rel="noopener noreferrer" class="font-medium text-indigo-600 underline decoration-indigo-300 underline-offset-2 hover:text-indigo-500 dark:text-indigo-300 dark:decoration-indigo-500/60 dark:hover:text-indigo-200">
                                                {{ $item['value'] ?? $item['href'] }}
                                            </a>
                                        </span>
                                    @else
                                        {{ $item['value'] ?? '-' }}
                                    @endif
                                </dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            @endif

            @if(! empty($pendingList) && is_array($pendingList))
                <div class="mt-8 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800" id="informacion-por-aclarar">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h2 class="text-sm font-semibold uppercase tracking-[0.15em] text-gray-500 dark:text-gray-300">
                            Información por aclarar
                        </h2>
                    </div>

                    @if($errors->has('body') && old('pending_item_index') !== null && old('pending_item_index') !== '')
                        <div class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900 dark:border-red-900/60 dark:bg-red-950/40 dark:text-red-100" role="alert">
                            {{ $errors->first('body') }}
                        </div>
                    @endif

                    @if(session('documentation_note_success') && session('documentation_pending_response_index') !== null)
                        <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 dark:border-emerald-800/80 dark:bg-emerald-950/50 dark:text-emerald-100" role="status">
                            Gracias: tu respuesta se ha guardado debajo de este punto hasta que la incorpore a la documentación.
                        </div>
                    @endif

                    <ul class="mt-4 space-y-4 text-sm text-gray-700 dark:text-gray-200">
                        @foreach($pendingList as $item)
                            <li
                                id="pending-item-{{ $loop->index }}"
                                class="rounded-xl border border-gray-200 bg-gray-50/60 px-4 py-3 dark:border-gray-600 dark:bg-gray-900/35"
                                @if($allowClientNotes)
                                    x-data="{ open: @js(old('pending_item_index') !== null && (int) old('pending_item_index') === $loop->index) }"
                                @endif
                            >
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <p class="min-w-0 flex-1 leading-relaxed text-gray-800 dark:text-gray-100">
                                        @if(is_array($item) && ! empty($item['html']))
                                            {!! $item['html'] !!}
                                        @else
                                            {{ $item }}
                                        @endif
                                    </p>
                                    @if($allowClientNotes)
                                        <button
                                            type="button"
                                            @click="open = !open"
                                            class="shrink-0 rounded-lg border border-indigo-200 bg-white px-3 py-1.5 text-xs font-semibold text-indigo-700 shadow-sm transition hover:bg-indigo-50 dark:border-indigo-500/40 dark:bg-gray-800 dark:text-indigo-300 dark:hover:bg-indigo-950/40"
                                        >
                                            <span x-show="!open">Responder</span>
                                            <span x-show="open" x-cloak>Cerrar</span>
                                        </button>
                                    @endif
                                </div>

                                @if($allowClientNotes)
                                    @php
                                        $itemNotes = $pendingNotesByIndex->get($loop->index) ?? collect();
                                    @endphp
                                    <ul
                                        id="documentation-pending-notes-list-{{ $loop->index }}"
                                        class="@if($itemNotes->isEmpty()) hidden @endif mt-3 space-y-3 border-t border-gray-200 pt-3 dark:border-gray-600"
                                    >
                                        @foreach($itemNotes as $pnote)
                                            <li class="rounded-lg border border-gray-200/80 bg-white/90 px-3 py-2 dark:border-gray-600/80 dark:bg-gray-800/60">
                                                <div class="flex flex-wrap items-start justify-between gap-2">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $pnote->created_at->timezone(config('app.timezone'))->translatedFormat('d M Y, H:i') }}
                                                    </p>
                                                    @auth
                                                        <form
                                                            method="post"
                                                            action="{{ route('documentation.notes.destroy', [$slug, $pnote]) }}"
                                                            class="shrink-0"
                                                            data-documentation-delete-form
                                                            data-confirm="¿Eliminar esta respuesta de forma permanente?"
                                                        >
                                                            @csrf
                                                            @method('DELETE')
                                                            <button
                                                                type="submit"
                                                                class="text-xs font-semibold text-red-700 underline decoration-red-300 underline-offset-2 hover:text-red-600 dark:text-red-400 dark:decoration-red-500/50 dark:hover:text-red-300"
                                                            >
                                                                Eliminar
                                                            </button>
                                                        </form>
                                                    @endauth
                                                </div>
                                                <div class="mt-1.5 whitespace-pre-wrap text-sm text-gray-900 dark:text-gray-100">{!! nl2br(e($pnote->body)) !!}</div>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <div x-show="open" x-cloak class="mt-3 space-y-3 border-t border-gray-200 pt-3 dark:border-gray-600">
                                        <form method="post" action="{{ route('public.documentation.notes.store', $slug) }}" class="space-y-3" data-documentation-note-form>
                                            @csrf
                                            <div class="documentation-note-form-error hidden rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-900 dark:border-red-900/60 dark:bg-red-950/40 dark:text-red-100" role="alert"></div>
                                            <input type="hidden" name="pending_item_index" value="{{ $loop->index }}" />
                                            <div>
                                                <label for="documentation-pending-body-{{ $loop->index }}" class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">Tu respuesta</label>
                                                <textarea
                                                    id="documentation-pending-body-{{ $loop->index }}"
                                                    name="body"
                                                    rows="4"
                                                    required
                                                    minlength="3"
                                                    maxlength="8000"
                                                    class="block w-full rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:placeholder:text-gray-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/25"
                                                    placeholder="Escribe aquí la información que falta para este punto…"
                                                >{{ old('pending_item_index') !== null && (int) old('pending_item_index') === $loop->index ? old('body') : '' }}</textarea>
                                            </div>
                                            <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                                                Enviar respuesta
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($documentation['allow_client_notes'] ?? false)
                <div id="apuntes-cliente" class="mt-8 scroll-mt-28 space-y-6">
                    @if(session('documentation_note_success'))
                        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 dark:border-emerald-800/80 dark:bg-emerald-950/50 dark:text-emerald-100" role="status">
                            @if(session('documentation_pending_response_index') !== null)
                                Gracias: tu respuesta queda registrada en el punto correspondiente de «Información por aclarar» hasta que lo incorpore a la documentación.
                            @else
                                Gracias: tu apunte se ha guardado y aparecerá abajo hasta que lo incorpore a la documentación.
                            @endif
                        </div>
                    @endif

                    @if(session('documentation_note_deleted'))
                        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 dark:border-emerald-800/80 dark:bg-emerald-950/50 dark:text-emerald-100" role="status">
                            Apunte eliminado.
                        </div>
                    @endif

                    @if($errors->has('body') && (old('pending_item_index') === null || old('pending_item_index') === ''))
                        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900 dark:border-red-900/60 dark:bg-red-950/40 dark:text-red-100" role="alert">
                            {{ $errors->first('body') }}
                        </div>
                    @endif

                    <div class="rounded-2xl border border-amber-200 bg-amber-50/90 p-6 shadow-sm dark:border-amber-900/50 dark:bg-amber-950/30">
                        <h2 class="text-sm font-semibold uppercase tracking-[0.15em] text-amber-900/90 dark:text-amber-200/90">
                            Apuntes enviados (pendientes de ordenar)
                        </h2>
                        <p class="mt-2 text-sm text-amber-950/80 dark:text-amber-100/80">
                            Aquí aparece solo lo que envíes desde el formulario de abajo, tal cual, hasta que lo integre en las secciones de arriba. Las respuestas a «Información por aclarar» se muestran únicamente junto a cada punto.
                        </p>
                        <p
                            id="documentation-general-notes-empty"
                            class="mt-4 text-sm italic text-amber-900/70 dark:text-amber-200/60 @unless($generalClientNotes->isEmpty()) hidden @endunless"
                        >
                            Todavía no hay apuntes en esta caja.
                        </p>
                        <ul
                            id="documentation-general-notes-list"
                            class="mt-4 space-y-4 @if($generalClientNotes->isEmpty()) hidden @endif"
                        >
                            @foreach($generalClientNotes as $note)
                                <li class="rounded-xl border border-amber-200/80 bg-white/80 px-4 py-3 dark:border-amber-800/40 dark:bg-gray-900/40">
                                    <div class="flex flex-wrap items-start justify-between gap-2">
                                        <p class="text-xs text-amber-800/80 dark:text-amber-300/70">
                                            {{ $note->created_at->timezone(config('app.timezone'))->translatedFormat('d M Y, H:i') }}
                                        </p>
                                        @auth
                                            <form
                                                method="post"
                                                action="{{ route('documentation.notes.destroy', [$slug, $note]) }}"
                                                class="shrink-0"
                                                data-documentation-delete-form
                                                data-confirm="¿Eliminar este apunte de forma permanente?"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    class="text-xs font-semibold text-red-700 underline decoration-red-300 underline-offset-2 hover:text-red-600 dark:text-red-400 dark:decoration-red-500/50 dark:hover:text-red-300"
                                                >
                                                    Eliminar
                                                </button>
                                            </form>
                                        @endauth
                                    </div>
                                    <div class="mt-2 whitespace-pre-wrap text-sm text-gray-900 dark:text-gray-100">{!! nl2br(e($note->body)) !!}</div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <h2 class="text-sm font-semibold uppercase tracking-[0.15em] text-gray-500 dark:text-gray-300">
                            Añadir más información
                        </h2>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            Escribe aquí cualquier detalle adicional (textos, ideas, referencias). Se guardará en la caja de arriba.
                        </p>
                        <form method="post" action="{{ route('public.documentation.notes.store', $slug) }}" class="mt-4 space-y-4" data-documentation-note-form>
                            @csrf
                            <div class="documentation-note-form-error hidden rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900 dark:border-red-900/60 dark:bg-red-950/40 dark:text-red-100" role="alert"></div>
                            <div>
                                <label for="documentation-note-body" class="sr-only">Información adicional</label>
                                <textarea
                                    id="documentation-note-body"
                                    name="body"
                                    rows="5"
                                    required
                                    minlength="3"
                                    maxlength="8000"
                                    class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:placeholder:text-gray-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/25"
                                    placeholder="Ej.: queremos destacar el taller de reparaciones, cambiar el texto del hero…"
                                >{{ old('body') }}</textarea>
                            </div>
                            <div class="flex flex-wrap items-center gap-3">
                                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                                    Enviar apunte
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>
@endsection

@push('scripts')
    @vite('resources/js/documentation-notes.js')
@endpush
