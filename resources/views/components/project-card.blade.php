{{-- Tarjeta de proyecto --}}
@props(['project', 'includePrivateLinks' => false])

<article class="group project-card js-project-card bg-gray-50 dark:bg-gray-800 rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700 flex flex-col h-full shadow-sm 
    hover:shadow-2xl hover:shadow-indigo-500/10 hover:-translate-y-2 hover:border-indigo-500/50 transition-all duration-300 relative">
    
    {{-- Detalle: Línea de acento superior que se expande --}}
    <div class="absolute bottom-0 left-0 w-0 h-1 bg-indigo-600 group-hover:w-full transition-all duration-500 z-30"></div>

    @php
        $images = $project->images ?? [];
        if (empty($images) && !empty($project->image_url)) {
            $images = [$project->image_url];
        }
        $displayLinks = $project->relationLoaded('links')
            ? ($includePrivateLinks ? $project->links : $project->links->filter->isPublic())
            : ($includePrivateLinks ? $project->links()->get() : $project->publicLinks()->get());
    @endphp

    <!-- LLAMADA AL COMPONENTE CARRUSEL -->
    <div class="relative z-10 overflow-hidden rounded-t-2xl js-project-carousel-frame">
        <x-carousel :images="$images" :alt="'Captura del proyecto '.$project->title" />
        <div class="project-carousel-border pointer-events-none absolute inset-0 rounded-t-2xl z-20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
    </div>

    <!-- Contenido -->
    <div class="p-6 flex-grow flex flex-col relative z-10 overflow-hidden">
        {{-- Resplandor localizado solo en el contenido (no sobre el carrusel) --}}
        <div class="absolute -bottom-24 -right-24 w-48 h-48 bg-indigo-500/5 rounded-full blur-3xl group-hover:bg-indigo-500/10 transition-colors duration-500 pointer-events-none z-0"></div>

        @if(! empty($project->categories))
            <div class="relative z-10 flex flex-wrap gap-2 mb-3">
                @foreach($project->categories as $category)
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold uppercase tracking-wide bg-indigo-100 text-indigo-800 dark:bg-indigo-500/15 dark:text-indigo-300">
                        {{ $category }}
                    </span>
                @endforeach
            </div>
        @endif

        {{-- El título cambia de color cuando la tarjeta recibe hover --}}
        <h3 class="relative z-10 text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-300">
            {{ $project->title }}
        </h3>
        
        <p class="relative z-10 text-gray-600 dark:text-gray-400 text-sm mb-6 flex-grow leading-relaxed">
            {{ $project->description }}
        </p>

        @if($displayLinks->isNotEmpty())
        <div class="relative z-10 flex flex-wrap gap-3 mt-auto pt-5 border-t border-gray-100 dark:border-gray-700/50">
            @foreach($displayLinks as $link)
                @if($link->isGitHub())
                    <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer"
                    class="group/repo relative inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wide text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-500/30 overflow-hidden transition-all duration-300 hover:shadow-lg hover:shadow-indigo-500/20 hover:border-indigo-600 dark:hover:border-indigo-400">
                        <span class="absolute inset-0 w-full h-full bg-indigo-600 dark:bg-indigo-500 -translate-x-full group-hover/repo:translate-x-0 transition-transform duration-300 ease-out z-0"></span>
                        <x-icons.github class="relative z-10 w-4 h-4 transition-all duration-300 group-hover/repo:text-white group-hover/repo:rotate-12 group-hover/repo:scale-110" />
                        <span class="relative z-10 transition-colors duration-300 group-hover/repo:text-white">
                            {{ $link->label() }}
                        </span>
                    </a>
                @else
                    <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer"
                    class="group/demo relative inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wide text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 overflow-hidden bg-white/60 dark:bg-gray-900/45 backdrop-blur-sm transition-all duration-300 hover:bg-gray-900/[0.04] dark:hover:bg-white/[0.06] hover:border-gray-300 dark:hover:border-gray-500 hover:shadow-md hover:shadow-gray-900/5 dark:hover:shadow-white/5">
                        <span class="absolute inset-0 rounded-xl bg-gradient-to-br from-indigo-500/0 via-indigo-500/0 to-cyan-500/0 group-hover/demo:from-indigo-500/[0.05] group-hover/demo:via-indigo-500/[0.02] group-hover/demo:to-cyan-500/[0.05] dark:group-hover/demo:from-indigo-300/[0.07] dark:group-hover/demo:via-indigo-300/[0.03] dark:group-hover/demo:to-cyan-300/[0.07] transition-colors duration-300 z-0"></span>
                        @if($link->isApp())
                            <x-icons.play-store class="relative z-10 w-4 h-4 transition-all duration-300 group-hover/demo:text-gray-900 dark:group-hover/demo:text-white group-hover/demo:scale-105" />
                        @else
                            <x-icons.eye class="demo-eye-blink relative z-10 w-4 h-4 transition-all duration-300 group-hover/demo:text-gray-900 dark:group-hover/demo:text-white group-hover/demo:scale-105" />
                        @endif
                        <span class="relative z-10 transition-colors duration-300 group-hover/demo:text-gray-900 dark:group-hover/demo:text-white">
                            {{ $link->label() }}
                        </span>
                    </a>
                @endif
            @endforeach
        </div>
        @endif
    </div>

</article>