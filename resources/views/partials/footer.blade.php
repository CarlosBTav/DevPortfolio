{{-- Footer: por encima de main (capas blur/transform en algunas vistas) --}}
@php
    $isHome = request()->routeIs('home');
    $contactFunnelHref = route('public.contact');
    $brandMain = config('app.name', 'Portfolio');
    $mail = trim((string) config('contact.email'));
    $gh = trim((string) config('contact.github'));
    $li = trim((string) config('contact.linkedin'));
    $tel = trim((string) config('contact.phone'));
    $loc = trim((string) config('contact.location'));
    $hasFooterContact = $mail !== '' || $gh !== '' || $li !== '' || $tel !== '' || $loc !== '';
@endphp

<div class="page-entry-reveal relative z-20 mt-auto min-w-0 w-full">
    <footer
        id="main-footer"
        class="site-footer js-footer-border-spotlight relative z-0 overflow-hidden rounded-t-[2.25rem] bg-slate-950 sm:rounded-t-[2.75rem] lg:rounded-t-[3rem] text-gray-300 shadow-[0_-20px_60px_-25px_rgba(2,6,23,0.45)] ring-1 ring-white/[0.16]"
    >
        <div class="relative z-[1] max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-16">
            <div class="grid grid-cols-1 gap-10 md:grid-cols-2 lg:grid-cols-3">
                {{-- Marca --}}
                <div>
                    <a href="{{ route('home') }}" class="flex flex-col leading-tight cursor-pointer">
                        {{-- hero-name-vfx: quita el ::after rectangular (halo detrás del texto) que con overflow-hidden del footer se recorta feo; sin espacios entre <span> para no separar letras --}}
                        <span class="js-footer-name-spotlight js-hero-wave hero-name-vfx footer-name-wrapper footer-magnetic-vfx relative inline-block text-2xl font-bold text-white tracking-tight whitespace-nowrap">
                            <span class="footer-name-vfx-base">@foreach (mb_str_split($brandMain) as $i => $char)<span class="footer-name-char hero-wave-char" style="--char-index: {{ $i }}">{{ $char }}</span>@endforeach</span>
                            <span class="footer-name-vfx-sweep" aria-hidden="true">@foreach (mb_str_split($brandMain) as $i => $char)<span class="footer-name-char hero-wave-char" style="--char-index: {{ $i }}">{{ $char }}</span>@endforeach</span>
                        </span>
                        <span class="mt-1 text-sm font-semibold text-teal-200">
                            <span class="js-footer-design-spotlight footer-design-wrapper relative inline-block">
                                <span class="footer-design-base">Full Stack Developer</span>
                                <span class="footer-design-reflection" aria-hidden="true">Full Stack Developer</span>
                            </span>
                        </span>
                    </a>
                    <p class="mt-4 text-sm leading-relaxed text-gray-400">
                        Desarrollo de aplicaciones y webs para particulares y empresas. Cuéntame tu idea y la convertimos en producto.
                    </p>
                    <p class="mt-4 text-xs text-gray-500">
                        &copy; {{ date('Y') }} {{ $brandMain }}. Todos los derechos reservados.
                    </p>
                </div>

                {{-- Navegación --}}
                <div>
                    <h4 class="text-white font-bold mb-4">Navegación</h4>
                    <ul class="space-y-3 text-sm">
                        <li>
                            <a href="{{ route('home') }}" class="hover:text-teal-400 transition">Inicio</a>
                        </li>
                        <li>
                            <a href="{{ route('public.about') }}" class="hover:text-teal-400 transition">Sobre mí</a>
                        </li>
                        <li>
                            <a href="{{ route('public.projects') }}" class="hover:text-teal-400 transition">Proyectos</a>
                        </li>
                        <li>
                            <a href="{{ route('public.services') }}" class="hover:text-teal-400 transition">Servicios</a>
                        </li>
                        <li>
                            <a href="{{ $contactFunnelHref }}" class="hover:text-teal-400 transition">Contacto / asistente</a>
                        </li>
                    </ul>
                </div>

                {{-- Contacto --}}
                <div>
                    <h4 class="text-white font-bold mb-4">Redes</h4>
                    <ul class="space-y-3 text-sm">
                        @if ($loc !== '')
                            <li class="flex items-start gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-teal-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                </svg>
                                <span class="whitespace-pre-line">{{ $loc }}</span>
                            </li>
                        @endif
                        @if ($tel !== '')
                            <li>
                                <a href="tel:{{ preg_replace('/\s+/', '', $tel) }}" class="group flex items-center gap-3 text-gray-300 transition hover:text-teal-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-teal-400 hover-pop" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.964 3.852a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                    </svg>
                                    <span>{{ $tel }}</span>
                                </a>
                            </li>
                        @endif
                        @if ($mail !== '')
                            <li>
                                <a href="mailto:{{ $mail }}" class="group flex items-center gap-3 text-gray-300 transition hover:text-teal-400">
                                    <x-icons.email class="h-5 w-5 shrink-0 text-teal-400 icon-email" />
                                    <span>{{ $mail }}</span>
                                </a>
                            </li>
                        @endif
                        @if ($gh !== '')
                            <li>
                                <a href="{{ $gh }}" target="_blank" rel="noopener noreferrer" class="group flex items-center gap-3 text-gray-300 transition hover:text-teal-400">
                                    <x-icons.github class="h-5 w-5 shrink-0 text-teal-400 icon-github" />
                                    <span>GitHub</span>
                                </a>
                            </li>
                        @endif
                        @if ($li !== '')
                            <li>
                                <a href="{{ $li }}" target="_blank" rel="noopener noreferrer" class="group flex items-center gap-3 text-gray-300 transition hover:text-teal-400">
                                    <x-icons.linkedin class="h-5 w-5 shrink-0 text-teal-400 icon-linkedin" />
                                    <span>LinkedIn</span>
                                </a>
                            </li>
                        @endif
                        @unless ($hasFooterContact)
                            <li>
                                <a href="{{ $contactFunnelHref }}" class="inline-flex items-center gap-2 text-teal-200 hover:text-teal-400 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                    </svg>
                                    Asistente de contacto
                                </a>
                            </li>
                        @endunless
                    </ul>
                </div>
            </div>
        </div>
    </footer>
</div>
