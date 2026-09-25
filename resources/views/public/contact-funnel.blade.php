@extends('layouts.public')

@section('title', 'Contacto | Carlos Codex')
@section('meta_description', 'Cuéntame qué necesitas en unos pasos sencillos: desarrollo web, apps móviles o colaboración profesional. Agenda una llamada, WhatsApp o correo.')

@section('body-class', 'antialiased bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100 font-sans flex flex-col min-h-dynamic transition-colors duration-300')

@section('content')
@php
    $directContactEmail = trim((string) (config('contact.email') ?: config('mail.from.address', '')));
@endphp

<div class="relative min-h-dynamic overflow-x-hidden pb-24 pt-36 md:pt-44">
    <div class="pointer-events-none absolute inset-0 -z-10 overflow-hidden" aria-hidden="true">
        <div class="absolute -left-1/4 top-0 h-[460px] w-[460px] rounded-full bg-indigo-500/20 blur-3xl dark:bg-indigo-500/25"></div>
        <div class="absolute -right-1/4 top-20 h-[420px] w-[420px] rounded-full bg-cyan-400/15 blur-3xl dark:bg-cyan-500/20"></div>
        <div class="absolute left-1/2 top-10 h-[300px] w-[580px] -translate-x-1/2 rounded-full bg-violet-500/12 blur-3xl dark:bg-violet-500/20"></div>
        <div class="absolute -right-1/4 bottom-0 h-[380px] w-[380px] rounded-full bg-violet-500/10 blur-3xl dark:bg-violet-600/15"></div>
    </div>

    <div
        class="relative z-10 mx-auto max-w-lg px-4 sm:px-6"
        x-data="window.contactFunnelWizard(@js(route('contact.store')), @js(csrf_token()))"
        x-init="initFromQuery(); initStageLayout()"
    >
        <header class="mb-8 text-center sm:mb-10">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-400">Asistente de contacto</p>
            <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-3xl">
                Te guío en unos pasos breves
            </h1>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                Cuéntame lo esencial y te propongo el siguiente paso más rápido para avanzar.
            </p>
        </header>

        <div class="mb-6 h-1.5 overflow-hidden rounded-full bg-slate-200/80 dark:bg-slate-800">
            <div
                class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-violet-500 transition-[width] duration-500 ease-out"
                :style="{ width: progressPercent + '%' }"
            ></div>
        </div>

        <div class="relative min-h-[320px] rounded-2xl border border-slate-200/80 bg-white/90 p-6 shadow-xl shadow-slate-900/5 backdrop-blur-sm dark:border-slate-700/80 dark:bg-slate-900/70 dark:shadow-black/40 sm:p-8">
            <template x-if="done">
                <div class="flex flex-col items-center justify-center py-10 text-center" x-transition.opacity.duration.300ms>
                    <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-300">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white">Listo, mensaje enviado</h2>
                    <p class="mt-2 max-w-sm text-sm text-slate-600 dark:text-slate-400" x-text="doneMessage"></p>
                    <a href="{{ route('home') }}" class="mt-8 inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500">
                        Volver al inicio
                    </a>
                </div>
            </template>

            <template x-if="!done">
                <div class="transition-[min-height] duration-300 ease-out" :style="{ minHeight: stageMinHeight }">
                    <p x-show="error" class="mb-4 rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800 dark:border-red-900/50 dark:bg-red-950/40 dark:text-red-200" x-text="error"></p>

                    {{-- Slides del asistente --}}
                    <div
                        class="cf-stage-stack"
                        :style="{ minHeight: stageBodyHeight, '--cf-enter-x': transitionDirection > 0 ? '22px' : '-22px', '--cf-leave-x': transitionDirection > 0 ? '-16px' : '16px' }"
                        x-effect="currentStage; callType; syncStageHeight()"
                    >
                        {{-- 1. Interés --}}
                        <div class="cf-stage" data-cf-stage="interest" x-show="currentStage === 'interest'" x-transition:enter="cf-stage-enter" x-transition:enter-start="cf-stage-enter-start" x-transition:enter-end="cf-stage-enter-end" x-transition:leave="cf-stage-leave" x-transition:leave-start="cf-stage-leave-start" x-transition:leave-end="cf-stage-leave-end" x-cloak>
                            <h2 class="text-lg font-semibold text-slate-900 dark:text-white sm:text-xl">¿Qué te trae por aquí?</h2>
                            <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">Elige la opción que mejor encaje; podrás concretar después.</p>
                            <div class="mt-6 grid gap-3">
                                <button type="button" class="cf-choice" :class="{ 'cf-choice--on': interest === 'web' }" @click="interest = 'web'">
                                    <span class="text-sm font-semibold text-slate-900 dark:text-white">🌐 Proyecto web o producto digital</span>
                                    <span class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Webs, paneles, integraciones, e‑commerce…</span>
                                </button>
                                <button type="button" class="cf-choice" :class="{ 'cf-choice--on': interest === 'mobile' }" @click="interest = 'mobile'">
                                    <span class="text-sm font-semibold text-slate-900 dark:text-white">📱 Desarrollo móvil</span>
                                    <span class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Apps nativas o multiplataforma</span>
                                </button>
                                <button type="button" class="cf-choice" :class="{ 'cf-choice--on': interest === 'careers' }" @click="interest = 'careers'">
                                    <span class="text-sm font-semibold text-slate-900 dark:text-white">🤝 Talento, partnering u oferta laboral</span>
                                    <span class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Propuestas profesionales, colaboración o recruiting</span>
                                </button>
                            </div>
                        </div>

                        {{-- 2. Brief --}}
                        <div class="cf-stage" data-cf-stage="brief" x-show="currentStage === 'brief'" x-transition:enter="cf-stage-enter" x-transition:enter-start="cf-stage-enter-start" x-transition:enter-end="cf-stage-enter-end" x-transition:leave="cf-stage-leave" x-transition:leave-start="cf-stage-leave-start" x-transition:leave-end="cf-stage-leave-end" x-cloak>
                            <h2 class="text-lg font-semibold text-slate-900 dark:text-white sm:text-xl">Cuéntamelo en pocas líneas</h2>
                            <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">Objetivo, contexto o plazos: con un párrafo basta para empezar.</p>
                            <label class="mt-5 block">
                                <span class="sr-only">Tu mensaje</span>
                                <textarea
                                    x-model="brief"
                                    rows="5"
                                    minlength="10"
                                    maxlength="4000"
                                    class="mt-2 w-full resize-none rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-600 dark:bg-slate-950 dark:text-white dark:placeholder:text-slate-500"
                                    placeholder="Ej.: Buscamos renovar la web corporativa y conectar el CRM con…"
                                ></textarea>
                            </label>
                            <p class="mt-1.5 text-xs text-slate-500">Mínimo 10 caracteres.</p>
                        </div>

                        {{-- 3. Canal --}}
                        <div class="cf-stage" data-cf-stage="channel" x-show="currentStage === 'channel'" x-transition:enter="cf-stage-enter" x-transition:enter-start="cf-stage-enter-start" x-transition:enter-end="cf-stage-enter-end" x-transition:leave="cf-stage-leave" x-transition:leave-start="cf-stage-leave-start" x-transition:leave-end="cf-stage-leave-end" x-cloak>
                            <h2 class="text-lg font-semibold text-slate-900 dark:text-white sm:text-xl">¿Cómo prefieres que sigamos?</h2>
                            <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">Lo más ágil suele ser una videollamada corta; también puedo responderte por WhatsApp o correo.</p>
                            <div class="mt-6 grid gap-3">
                                <button type="button" class="cf-choice cf-choice--featured" :class="{ 'cf-choice--on': channel === 'schedule_call' }" @click="channel = 'schedule_call'">
                                    <span class="absolute right-3 top-2 rounded-full bg-indigo-100 px-2 py-0.5 text-[0.65rem] font-bold uppercase tracking-wider text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-200">Recomendado</span>
                                    <span class="text-sm font-semibold text-slate-900 dark:text-white">📞 Agendar una llamada de 15 min</span>
                                    <span class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Elige día, franja y canal (vídeo, teléfono o WhatsApp)</span>
                                </button>
                                <button type="button" class="cf-choice" :class="{ 'cf-choice--on': channel === 'whatsapp' }" @click="channel = 'whatsapp'">
                                    <span class="text-sm font-semibold text-slate-900 dark:text-white">💬 Prefiero WhatsApp</span>
                                    <span class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Te respondo cuando pueda mirar el móvil con calma</span>
                                </button>
                                <button type="button" class="cf-choice" :class="{ 'cf-choice--on': channel === 'email' }" @click="channel = 'email'">
                                    <span class="text-sm font-semibold text-slate-900 dark:text-white">✉️ Prefiero correo electrónico</span>
                                    <span class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Válido si necesitas adjuntar documentos</span>
                                </button>
                            </div>
                        </div>

                        {{-- Agenda: día --}}
                        <div class="cf-stage" data-cf-stage="sched_day" x-show="currentStage === 'sched_day'" x-transition:enter="cf-stage-enter" x-transition:enter-start="cf-stage-enter-start" x-transition:enter-end="cf-stage-enter-end" x-transition:leave="cf-stage-leave" x-transition:leave-start="cf-stage-leave-start" x-transition:leave-end="cf-stage-leave-end" x-cloak>
                            <h2 class="text-lg font-semibold text-slate-900 dark:text-white sm:text-xl">¿Qué día te viene mejor?</h2>
                            <p x-show="weekendNow()" class="mt-2 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-900 dark:border-amber-800/50 dark:bg-amber-950/40 dark:text-amber-100">
                                Los fines de semana no agendo. Elige un día laborable de la lista.
                            </p>
                            <div class="mt-5 grid gap-2.5">
                                <template x-for="(opt, idx) in bookingDayChoices" :key="opt.label + idx">
                                    <button type="button" class="cf-choice text-left" :class="{ 'cf-choice--on': day === opt.label }" @click="day = opt.label">
                                        <span class="text-sm font-semibold text-slate-900 dark:text-white" x-text="opt.label"></span>
                                        <span class="mt-0.5 text-xs text-slate-500 dark:text-slate-400" x-text="opt.subtitle"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        {{-- Agenda: franja --}}
                        <div class="cf-stage" data-cf-stage="sched_time" x-show="currentStage === 'sched_time'" x-transition:enter="cf-stage-enter" x-transition:enter-start="cf-stage-enter-start" x-transition:enter-end="cf-stage-enter-end" x-transition:leave="cf-stage-leave" x-transition:leave-start="cf-stage-leave-start" x-transition:leave-end="cf-stage-leave-end" x-cloak>
                            <h2 class="text-lg font-semibold text-slate-900 dark:text-white sm:text-xl">¿Qué franja horaria prefieres?</h2>
                            <div class="mt-5 grid gap-2.5">
                                <button type="button" class="cf-choice" :class="{ 'cf-choice--on': time === 'Mañana' }" @click="time = 'Mañana'">
                                    <span class="text-sm font-semibold text-slate-900 dark:text-white">Mañana</span>
                                    <span class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">9:00 – 12:00</span>
                                </button>
                                <button type="button" class="cf-choice" :class="{ 'cf-choice--on': time === 'Tarde' }" @click="time = 'Tarde'">
                                    <span class="text-sm font-semibold text-slate-900 dark:text-white">Tarde</span>
                                    <span class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">16:00 – 19:00</span>
                                </button>
                                <button type="button" class="cf-choice" :class="{ 'cf-choice--on': time === 'Me adapto' }" @click="time = 'Me adapto'">
                                    <span class="text-sm font-semibold text-slate-900 dark:text-white">Me adapto</span>
                                    <span class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Te propongo opciones</span>
                                </button>
                            </div>
                        </div>

                        {{-- Agenda: tipo de canal --}}
                        <div class="cf-stage" data-cf-stage="sched_type" x-show="currentStage === 'sched_type'" x-transition:enter="cf-stage-enter" x-transition:enter-start="cf-stage-enter-start" x-transition:enter-end="cf-stage-enter-end" x-transition:leave="cf-stage-leave" x-transition:leave-start="cf-stage-leave-start" x-transition:leave-end="cf-stage-leave-end" x-cloak>
                            <h2 class="text-lg font-semibold text-slate-900 dark:text-white sm:text-xl">¿Cómo te conectamos?</h2>
                            <div class="mt-5 grid gap-2.5">
                                <button type="button" class="cf-choice" :class="{ 'cf-choice--on': callType === 'Videollamada' }" @click="callType = 'Videollamada'">
                                    <span class="text-sm font-semibold text-slate-900 dark:text-white">Videollamada</span>
                                    <span class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Te envío enlace por email</span>
                                </button>
                                <button type="button" class="cf-choice" :class="{ 'cf-choice--on': callType === 'Llamada telefónica' }" @click="callType = 'Llamada telefónica'">
                                    <span class="text-sm font-semibold text-slate-900 dark:text-white">Llamada telefónica</span>
                                    <span class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Indica un número de contacto</span>
                                </button>
                                <button type="button" class="cf-choice" :class="{ 'cf-choice--on': callType === 'WhatsApp' }" @click="callType = 'WhatsApp'">
                                    <span class="text-sm font-semibold text-slate-900 dark:text-white">WhatsApp</span>
                                    <span class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Te escribo o llamo por WhatsApp</span>
                                </button>
                            </div>
                        </div>

                        {{-- Agenda: datos + envío --}}
                        <div class="cf-stage" data-cf-stage="sched_contact" x-show="currentStage === 'sched_contact'" x-transition:enter="cf-stage-enter" x-transition:enter-start="cf-stage-enter-start" x-transition:enter-end="cf-stage-enter-end" x-transition:leave="cf-stage-leave" x-transition:leave-start="cf-stage-leave-start" x-transition:leave-end="cf-stage-leave-end" x-cloak>
                            <h2 class="text-lg font-semibold text-slate-900 dark:text-white sm:text-xl">Tus datos de contacto</h2>
                            <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">Resumen: <span class="font-medium text-slate-800 dark:text-slate-200" x-text="day"></span> · <span x-text="time"></span> · <span x-text="callType"></span></p>
                            <div class="mt-5 space-y-4">
                                <div>
                                    <label for="cf-name" class="mb-1 block text-xs font-medium text-slate-700 dark:text-slate-300">Nombre</label>
                                    <input id="cf-name" type="text" x-model="name" autocomplete="name" class="cf-input" placeholder="Tu nombre" />
                                </div>
                                <div x-show="callType === 'Videollamada'">
                                    <label for="cf-email-v" class="mb-1 block text-xs font-medium text-slate-700 dark:text-slate-300">Email (para el enlace)</label>
                                    <input id="cf-email-v" type="email" x-model="email" autocomplete="email" class="cf-input" placeholder="tu@email.com" />
                                </div>
                                <div x-show="callType === 'Videollamada'">
                                    <label for="cf-phone-o" class="mb-1 block text-xs font-medium text-slate-700 dark:text-slate-300">Teléfono <span class="font-normal text-slate-500">(opcional)</span></label>
                                    <input id="cf-phone-o" type="tel" x-model="phone" autocomplete="tel" class="cf-input" placeholder="Por si acaso" />
                                </div>
                                <div x-show="callType === 'Llamada telefónica' || callType === 'WhatsApp'">
                                    <label for="cf-phone" class="mb-1 block text-xs font-medium text-slate-700 dark:text-slate-300">Teléfono</label>
                                    <input id="cf-phone" type="tel" x-model="phone" autocomplete="tel" class="cf-input" placeholder="+34…" />
                                </div>
                            </div>
                        </div>

                        {{-- WhatsApp async --}}
                        <div class="cf-stage" data-cf-stage="wa_contact" x-show="currentStage === 'wa_contact'" x-transition:enter="cf-stage-enter" x-transition:enter-start="cf-stage-enter-start" x-transition:enter-end="cf-stage-enter-end" x-transition:leave="cf-stage-leave" x-transition:leave-start="cf-stage-leave-start" x-transition:leave-end="cf-stage-leave-end" x-cloak>
                            <h2 class="text-lg font-semibold text-slate-900 dark:text-white sm:text-xl">Tu WhatsApp</h2>
                            <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">Número con prefijo internacional. El correo es opcional.</p>
                            <div class="mt-5 space-y-4">
                                <div>
                                    <label for="cf-wa-name" class="mb-1 block text-xs font-medium text-slate-700 dark:text-slate-300">Nombre</label>
                                    <input id="cf-wa-name" type="text" x-model="name" autocomplete="name" class="cf-input" />
                                </div>
                                <div>
                                    <label for="cf-wa-phone" class="mb-1 block text-xs font-medium text-slate-700 dark:text-slate-300">WhatsApp</label>
                                    <input id="cf-wa-phone" type="tel" x-model="phone" autocomplete="tel" class="cf-input" required />
                                </div>
                                <div>
                                    <label for="cf-wa-email" class="mb-1 block text-xs font-medium text-slate-700 dark:text-slate-300">Email <span class="font-normal text-slate-500">(opcional)</span></label>
                                    <input id="cf-wa-email" type="email" x-model="email" autocomplete="email" class="cf-input" />
                                </div>
                            </div>
                        </div>

                        {{-- Email async --}}
                        <div class="cf-stage" data-cf-stage="mail_contact" x-show="currentStage === 'mail_contact'" x-transition:enter="cf-stage-enter" x-transition:enter-start="cf-stage-enter-start" x-transition:enter-end="cf-stage-enter-end" x-transition:leave="cf-stage-leave" x-transition:leave-start="cf-stage-leave-start" x-transition:leave-end="cf-stage-leave-end" x-cloak>
                            <h2 class="text-lg font-semibold text-slate-900 dark:text-white sm:text-xl">Tu correo</h2>
                            <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">Te responderé por email. El teléfono es opcional.</p>
                            <div class="mt-5 space-y-4">
                                <div>
                                    <label for="cf-em-name" class="mb-1 block text-xs font-medium text-slate-700 dark:text-slate-300">Nombre</label>
                                    <input id="cf-em-name" type="text" x-model="name" autocomplete="name" class="cf-input" />
                                </div>
                                <div>
                                    <label for="cf-em-mail" class="mb-1 block text-xs font-medium text-slate-700 dark:text-slate-300">Email</label>
                                    <input id="cf-em-mail" type="email" x-model="email" autocomplete="email" required class="cf-input" />
                                </div>
                                <div>
                                    <label for="cf-em-phone" class="mb-1 block text-xs font-medium text-slate-700 dark:text-slate-300">Teléfono <span class="font-normal text-slate-500">(opcional)</span></label>
                                    <input id="cf-em-phone" type="tel" x-model="phone" autocomplete="tel" class="cf-input" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="relative mt-8 flex w-full items-center justify-end gap-3 border-t border-slate-100 pt-6 dark:border-slate-800">
                        <button
                            type="button"
                            class="absolute left-0 top-6 text-sm font-medium text-slate-600 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-white"
                            x-show="stageIndex > 0"
                            x-transition.opacity.duration.200ms
                            :disabled="sending"
                            @click="back()"
                        >
                            ← Atrás
                        </button>
                        <div class="flex shrink-0 gap-2">
                            <template x-if="!isSubmitStage">
                                <button
                                    type="button"
                                    class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 transition hover:bg-indigo-500 disabled:cursor-not-allowed disabled:opacity-40"
                                    :disabled="!canContinue || sending"
                                    @click="next()"
                                >
                                    Continuar
                                </button>
                            </template>
                            <template x-if="isSubmitStage">
                                <button
                                    type="button"
                                    class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 transition hover:bg-indigo-500 disabled:cursor-wait disabled:opacity-70"
                                    :disabled="sending || !canSubmit"
                                    @click="submit()"
                                >
                                    <span x-show="!sending">Enviar</span>
                                    <span x-show="sending">Enviando…</span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <p class="mt-8 text-center text-xs text-slate-500 dark:text-slate-500">
            ¿Prefieres escribirme directamente?
            @if($directContactEmail !== '')
                <a href="mailto:{{ $directContactEmail }}" class="font-medium text-indigo-600 underline decoration-indigo-600/30 underline-offset-2 hover:text-indigo-500 dark:text-indigo-400">Correo</a>
            @else
                <a href="{{ route('public.contact') }}" class="font-medium text-indigo-600 underline decoration-indigo-600/30 underline-offset-2 hover:text-indigo-500 dark:text-indigo-400">Abrir asistente</a>
            @endif
        </p>
    </div>
</div>

<style>
    .cf-stage-stack {
        --cf-enter-x: 22px;
        --cf-leave-x: -16px;
        position: relative;
        overflow: hidden;
        transition: min-height 0.34s cubic-bezier(0.22, 1, 0.36, 1);
    }
    .cf-stage {
        position: absolute;
        inset: 0;
        width: 100%;
        transform-origin: 50% 42%;
        will-change: opacity, transform, filter;
    }
    .cf-stage-enter {
        z-index: 2;
        transition: opacity 0.32s cubic-bezier(0.22, 1, 0.36, 1),
            transform 0.32s cubic-bezier(0.22, 1, 0.36, 1),
            filter 0.32s cubic-bezier(0.22, 1, 0.36, 1);
    }
    .cf-stage-leave {
        z-index: 1;
        pointer-events: none;
        transition: opacity 0.22s cubic-bezier(0.4, 0, 0.2, 1),
            transform 0.22s cubic-bezier(0.4, 0, 0.2, 1),
            filter 0.22s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .cf-stage-enter-start {
        opacity: 0;
        filter: blur(6px);
        transform: translate3d(var(--cf-enter-x), 10px, 0) scale(0.982);
    }
    .cf-stage-enter-end,
    .cf-stage-leave-start {
        opacity: 1;
        filter: blur(0);
        transform: translate3d(0, 0, 0) scale(1);
    }
    .cf-stage-leave-end {
        opacity: 0;
        filter: blur(4px);
        transform: translate3d(var(--cf-leave-x), -6px, 0) scale(0.986);
    }
    .cf-choice {
        position: relative;
        display: flex;
        width: 100%;
        flex-direction: column;
        border-radius: 0.75rem;
        border: 1px solid rgb(226 232 240);
        background: rgb(255 255 255);
        padding: 0.875rem 1rem;
        text-align: left;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .dark .cf-choice {
        border-color: rgb(71 85 105 / 0.8);
        background: rgb(2 6 23 / 0.8);
    }
    .cf-choice:hover {
        border-color: rgb(165 180 252);
    }
    .dark .cf-choice:hover {
        border-color: rgb(129 140 248 / 0.5);
    }
    .cf-choice--on {
        border-color: rgb(99 102 241);
        box-shadow: 0 0 0 2px rgb(99 102 241 / 0.25);
    }
    .dark .cf-choice--on {
        border-color: rgb(129 140 248);
    }
    .cf-choice--featured {
        padding-top: 1.75rem;
    }
    .cf-input {
        width: 100%;
        border-radius: 0.75rem;
        border: 1px solid rgb(226 232 240);
        background: rgb(255 255 255);
        padding: 0.625rem 0.875rem;
        font-size: 0.875rem;
        line-height: 1.25rem;
        color: rgb(15 23 42);
    }
    .cf-input::placeholder {
        color: rgb(148 163 184);
    }
    .cf-input:focus {
        border-color: rgb(99 102 241);
        outline: none;
        box-shadow: 0 0 0 2px rgb(99 102 241 / 0.2);
    }
    .dark .cf-input {
        border-color: rgb(71 85 105);
        background: rgb(2 6 23);
        color: rgb(248 250 252);
    }
    .dark .cf-input::placeholder {
        color: rgb(100 116 139);
    }
    @media (prefers-reduced-motion: reduce) {
        .cf-stage-stack,
        .cf-stage-enter,
        .cf-stage-leave {
            transition-duration: 1ms;
        }
        .cf-stage-enter-start,
        .cf-stage-leave-end {
            filter: none;
            transform: none;
        }
    }
</style>
@endsection

@push('scripts')
<script>
window.contactFunnelWizard = function contactFunnelWizard(actionUrl, csrfToken) {
    return {
        actionUrl,
        csrfToken,
        stageIndex: 0,
        transitionDirection: 1,
        interest: null,
        brief: '',
        channel: null,
        day: '',
        time: '',
        callType: 'Videollamada',
        name: '',
        phone: '',
        email: '',
        sending: false,
        error: '',
        done: false,
        doneMessage: '',
        stageBodyHeight: '320px',
        stageResizeHandler: null,

        initFromQuery() {
            const q = new URLSearchParams(window.location.search).get('interest');
            if (q === 'web' || q === 'mobile' || q === 'careers') {
                this.interest = q;
            }
        },
        initStageLayout() {
            this.syncStageHeight();
            this.stageResizeHandler = () => this.syncStageHeight();
            window.addEventListener('resize', this.stageResizeHandler, { passive: true });
        },
        syncStageHeight() {
            this.$nextTick(() => {
                const activeStage = this.$root.querySelector(`[data-cf-stage="${this.currentStage}"]`);
                if (!activeStage) return;

                this.stageBodyHeight = `${Math.ceil(activeStage.scrollHeight)}px`;
            });
        },

        stripTime(d) {
            const x = new Date(d);
            x.setHours(12, 0, 0, 0);
            return x;
        },
        isWeekendDate(d) {
            const wd = this.stripTime(d).getDay();
            return wd === 0 || wd === 6;
        },
        todayBase() {
            return this.stripTime(new Date());
        },
        weekendNow() {
            return this.isWeekendDate(this.todayBase());
        },
        addDays(d, n) {
            const x = new Date(this.stripTime(d));
            x.setDate(x.getDate() + n);
            return x;
        },
        sameDay(a, b) {
            return this.stripTime(a).getTime() === this.stripTime(b).getTime();
        },
        get bookingDayChoices() {
            const today = this.todayBase();
            const out = [];
            let d = new Date(today);
            while (out.length < 3) {
                if (!this.isWeekendDate(d)) {
                    let label;
                    let subtitle = 'Día laborable';
                    if (this.sameDay(d, today)) {
                        label = 'Hoy';
                        subtitle = 'Si hay hueco disponible';
                    } else if (this.sameDay(d, this.addDays(today, 1))) {
                        label = 'Mañana';
                        subtitle = 'Sin prisa, pero pronto';
                    } else {
                        label = new Intl.DateTimeFormat('es-ES', { weekday: 'long', day: 'numeric', month: 'short' }).format(d);
                        label = label.charAt(0).toUpperCase() + label.slice(1);
                    }
                    out.push({ label, subtitle });
                    if (out.length >= 3) break;
                }
                d = this.addDays(d, 1);
            }
            return out;
        },

        get stages() {
            const s = ['interest', 'brief', 'channel'];
            if (!this.channel) return s;
            if (this.channel === 'schedule_call') {
                return [...s, 'sched_day', 'sched_time', 'sched_type', 'sched_contact'];
            }
            if (this.channel === 'whatsapp') return [...s, 'wa_contact'];
            if (this.channel === 'email') return [...s, 'mail_contact'];
            return s;
        },
        get currentStage() {
            return this.stages[this.stageIndex] || 'interest';
        },
        get progressPercent() {
            const n = this.stages.length;
            if (n <= 1) return 5;
            return Math.min(100, Math.round(((this.stageIndex + 1) / n) * 100));
        },
        get stageMinHeight() {
            const heights = {
                interest: '360px',
                brief: '360px',
                channel: '380px',
                sched_day: '390px',
                sched_time: '390px',
                sched_type: '390px',
                sched_contact: '420px',
                wa_contact: '390px',
                mail_contact: '390px',
            };
            return heights[this.currentStage] || '360px';
        },
        get isSubmitStage() {
            return ['sched_contact', 'wa_contact', 'mail_contact'].includes(this.currentStage);
        },
        get canContinue() {
            if (this.currentStage === 'interest') return !!this.interest;
            if (this.currentStage === 'brief') return (this.brief || '').trim().length >= 10;
            if (this.currentStage === 'channel') return !!this.channel;
            if (this.currentStage === 'sched_day') return !!this.day;
            if (this.currentStage === 'sched_time') return !!this.time;
            if (this.currentStage === 'sched_type') return !!this.callType;
            return false;
        },
        get canSubmit() {
            if (this.currentStage === 'sched_contact') {
                if (!this.name.trim()) return false;
                if (this.callType === 'Videollamada') {
                    return !!this.email.trim();
                }
                return !!this.phone.trim();
            }
            if (this.currentStage === 'wa_contact') {
                return !!this.name.trim() && !!this.phone.trim();
            }
            if (this.currentStage === 'mail_contact') {
                return !!this.name.trim() && !!this.email.trim();
            }
            return false;
        },

        next() {
            if (!this.canContinue) return;
            if (this.stageIndex < this.stages.length - 1) {
                this.transitionDirection = 1;
                this.stageIndex += 1;
                this.syncStageHeight();
            }
            this.error = '';
        },
        back() {
            if (this.stageIndex <= 0) return;
            this.transitionDirection = -1;
            this.stageIndex -= 1;
            this.syncStageHeight();
            const st = this.currentStage;
            if (st === 'channel') {
                this.channel = null;
                this.day = '';
                this.time = '';
                this.callType = 'Videollamada';
            }
            if (st === 'interest') {
                this.channel = null;
                this.day = '';
                this.time = '';
                this.callType = 'Videollamada';
            }
            this.error = '';
        },

        bookingSummaryLine() {
            return `Quiero agendar una llamada de 15 min. Preferencia: ${this.day || 'sin indicar'}. Franja: ${this.time || 'sin indicar'}. Canal: ${this.callType}.`;
        },
        bookingBody() {
            return `${this.bookingSummaryLine()}\n\nSobre el proyecto:\n${this.brief.trim()}`;
        },

        async submit() {
            if (!this.canSubmit || this.sending) return;
            this.sending = true;
            this.error = '';
            const fd = new FormData();
            fd.append('_token', this.csrfToken);
            fd.append('name', this.name.trim());
            fd.append('inquiry_type', this.interest);

            try {
                if (this.currentStage === 'sched_contact') {
                    fd.append('form_context', 'booking');
                    fd.append('booking_channel', this.callType);
                    fd.append('content', this.bookingBody());
                    if (this.callType === 'Videollamada') {
                        fd.append('email', this.email.trim());
                        if (this.phone.trim()) fd.append('phone', this.phone.trim());
                    } else {
                        fd.append('phone', this.phone.trim());
                        if (this.email.trim()) fd.append('email', this.email.trim());
                    }
                    if (this.interest === 'web') {
                        fd.append('web_products[]', 'other');
                    }
                } else if (this.currentStage === 'wa_contact') {
                    fd.append('form_context', 'funnel_whatsapp');
                    fd.append('content', this.brief.trim());
                    fd.append('phone', this.phone.trim());
                    if (this.email.trim()) fd.append('email', this.email.trim());
                } else if (this.currentStage === 'mail_contact') {
                    fd.append('form_context', 'funnel_email');
                    fd.append('content', this.brief.trim());
                    fd.append('email', this.email.trim());
                    if (this.phone.trim()) fd.append('phone', this.phone.trim());
                }

                const res = await fetch(this.actionUrl, {
                    method: 'POST',
                    body: fd,
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });
                const payload = await res.json().catch(() => ({}));
                if (!res.ok) {
                    const msg = payload.errors
                        ? Object.values(payload.errors).flat()[0]
                        : payload.message || 'No se ha podido enviar. Revisa los datos e inténtalo de nuevo.';
                    throw new Error(msg);
                }
                this.doneMessage = payload.message || 'Gracias. Te contestaré lo antes posible.';
                this.done = true;
            } catch (e) {
                this.error = e.message || 'Error de red. Inténtalo otra vez.';
            } finally {
                this.sending = false;
            }
        },
    };
};
</script>
@endpush
