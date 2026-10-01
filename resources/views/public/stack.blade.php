@extends('layouts.public')

@section('title', 'Stack tecnológico | Carlos Codex')
@section('meta_description', 'Las tecnologías, lenguajes y metodologías con las que desarrollo webs, aplicaciones móviles y sistemas a medida.')

@section('body-class', 'antialiased font-sans flex flex-col min-h-dynamic transition-colors duration-300 text-gray-900 dark:text-gray-100 about-ai-dots-page')

@section('content')
<style>
        /* Skill cards: CTA bajo la tarjeta — en móvil al entrar en vista; en desktop al hover */
        .skill-card .skill-cta-hint {
          opacity: 0;
          transition: opacity 0.35s ease;
        }
        @media (max-width: 767.98px) {
          .skill-card.skill-card--in-view .skill-cta-hint {
            opacity: 1;
          }
        }
        @media (min-width: 768px) {
          .skill-card:hover .skill-cta-hint {
            opacity: 1;
          }
        }

        html.skills-modal-open,
        body.skills-modal-open {
          overflow: hidden !important;
          overscroll-behavior: none;
        }
        .skills-modal-overlay {
          position: fixed !important;
          inset: 0 !important;
          z-index: 2147483000 !important;
          display: flex;
          align-items: center;
          justify-content: center;
          padding: 16px;
          overflow: hidden;
          isolation: isolate;
        }
        .skills-modal-backdrop {
          position: absolute;
          inset: 0;
          z-index: 0;
          background: rgba(17, 24, 39, 0.74);
          backdrop-filter: blur(8px);
          -webkit-backdrop-filter: blur(8px);
        }
        .skills-modal-stage {
          position: relative;
          z-index: 1;
          width: 100%;
          max-width: 72rem;
          max-height: min(92dvh, 920px);
          min-height: 0;
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: center;
          gap: 16px;
          overflow: visible;
          pointer-events: none;
          padding: 4px;
        }
        .skills-modal-panel {
          max-height: calc(min(92dvh, 920px) - 8px);
          overflow-y: auto;
          overscroll-behavior: contain;
          scrollbar-width: none;
          will-change: transform, opacity;
        }
        .skills-modal-panel::-webkit-scrollbar {
          display: none;
        }
        @media (min-width: 768px) {
          .skills-modal-stage {
            flex-direction: row;
            gap: 24px;
          }
        }
</style>
<div class="relative w-full min-h-dynamic overflow-x-clip bg-transparent dark:bg-transparent">
    <div class="pointer-events-none absolute inset-x-0 top-0 -bottom-12 z-0 overflow-hidden" aria-hidden="true">
        <x-ai-dots-background variant="section" />
    </div>
    <!-- CONTENEDOR PRINCIPAL -->
    <div class="relative z-10 max-w-6xl mx-auto px-6 lg:px-8 public-page-top pb-12 lg:pb-20">

        <!-- CABECERA -->
        <section class="mb-14">
            <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 dark:text-white mb-8 tracking-tight">
                🧰 Stack <span class="text-indigo-600 dark:text-indigo-400">tecnológico</span>
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-300 leading-relaxed max-w-3xl">
                Trabajo con una gran gama de tecnologías de desarrollo, tanto web (Portfolios, CRMs, ERPs y CMS) como en aplicaciones móviles y multiplataforma. Todo en <strong>completo fullstack</strong>.
            </p>
        </section>

        <!-- TARJETAS DE SKILLS -->
        <section id="skills" x-data="skillsComponent()" class="relative">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <template x-for="(skill, key) in skillsData" :key="key">
                <div @click="openModal(key)" class="skill-card js-spotlight-card !bg-gray-50 dark:!bg-gray-800/50 border border-gray-100 dark:border-gray-700 group cursor-pointer rounded-2xl p-6 transition-all duration-300 hover:shadow-lg hover:-translate-y-1 flex flex-col h-full relative overflow-hidden" data-reveal>
                    <div class="flex items-center gap-4 mb-5 relative z-10">
                        <div :class="`p-3 rounded-xl ${skill.bg} ${skill.color} transition-transform group-hover:scale-110`">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="skill.icon"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-tight" x-text="skill.title"></h3>
                    </div>
                    <div class="flex flex-wrap gap-2 mt-auto relative z-10">
                        <template x-for="(tech, index) in skill.technologies.slice(0, 4)" :key="index">
                            <img :src="tech.badge" :alt="tech.name" class="h-6 rounded shadow-sm">
                        </template>
                        <span x-show="skill.technologies.length > 4" class="flex items-center px-2 py-1 text-xs font-bold text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 rounded" x-text="`+${skill.technologies.length - 4}`"></span>
                    </div>
                    <div class="skill-cta-hint mt-5 pt-4 border-t border-gray-100 dark:border-gray-800 flex justify-between items-center gap-3 text-sm font-medium text-indigo-600 dark:text-indigo-400 relative z-10">
                        <span>
                            <span class="md:hidden">Pulsa para ver más detalles</span>
                            <span class="hidden md:inline">Ver detalle de tecnologías</span>
                        </span>
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </div>
                </div>
            </template>
        </div>

        <template x-teleport="body">
        <div
            x-show="modalOpen"
            style="display: none;"
            class="skills-modal-overlay"
            role="dialog"
            aria-modal="true"
            @keydown.escape.window="closeModal()"
        >
            <div
                x-show="modalOpen"
                x-transition.opacity.duration.300ms
                @click="closeModal()"
                class="skills-modal-backdrop"
            ></div>
            <div class="skills-modal-stage">
                <div x-show="modalOpen"
                    x-transition:enter="ease-out duration-500"
                    x-transition:enter-start="opacity-0 translate-y-8 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-300"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-8 sm:scale-95"
                    class="skills-modal-panel relative z-20 bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-xl border border-gray-200 dark:border-gray-700 transition-all duration-500 pointer-events-auto">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50">
                        <div class="flex items-center gap-3">
                            <template x-if="activeSkill">
                                <div :class="`p-2 rounded-lg ${activeSkill.bg} ${activeSkill.color}`">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="activeSkill.icon"></path></svg>
                                </div>
                            </template>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white" x-text="activeSkill?.title"></h3>
                        </div>
                        <button @click="closeModal()" class="text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <div class="px-6 py-6">
                        <div x-show="activeSkill?.image" class="mb-5 overflow-hidden rounded-xl h-32 md:h-40 bg-gray-100 dark:bg-gray-800">
                            <img :src="activeSkill?.image" :alt="activeSkill?.title" class="w-full h-full object-cover opacity-90">
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 text-sm mb-6 bg-gray-50 dark:bg-gray-800/50 p-4 rounded-xl" x-html="activeSkill?.description"></p>
                        <h4 class="text-xs font-bold tracking-wider uppercase text-gray-500 dark:text-gray-400 mb-4 flex items-center gap-2">
                            Haz clic en una tecnología
                            <svg class="w-4 h-4 text-indigo-500 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path></svg>
                        </h4>
                        <div class="flex flex-wrap gap-3 mb-2">
                            <template x-for="(tech, index) in activeSkill?.technologies" :key="index">
                                <img :src="tech.badge" :alt="tech.name"
                                    @click="openTech(tech)"
                                    class="h-8 rounded shadow-sm transition-all duration-300 cursor-pointer ring-offset-2 dark:ring-offset-gray-900"
                                    :class="activeTech === tech ? 'ring-2 ring-indigo-500 scale-105 opacity-100' : 'hover:scale-105 opacity-80 hover:opacity-100 grayscale-[20%] hover:grayscale-0'">
                            </template>
                        </div>
                    </div>
                </div>
                <div x-show="showTechDetails"
                    x-transition:enter="transition-all duration-500 cubic-bezier(0.4, 0, 0.2, 1)"
                    x-transition:enter-start="opacity-0 !-mt-[20rem] md:!mt-0 md:!-ml-[24rem] scale-95"
                    x-transition:enter-end="opacity-100 mt-4 md:mt-0 md:ml-6 scale-100"
                    x-transition:leave="transition-all duration-300 ease-in"
                    x-transition:leave-start="opacity-100 mt-4 md:mt-0 md:ml-6 scale-100"
                    x-transition:leave-end="opacity-0 !-mt-[20rem] md:!mt-0 md:!-ml-[24rem] scale-95"
                    class="skills-modal-panel relative z-10 w-full max-w-sm bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-indigo-100 dark:border-gray-700 pointer-events-auto mt-4 md:mt-0 md:ml-6">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <img :src="activeTech?.badge" :alt="activeTech?.name" class="h-8 rounded shadow-sm">
                            <button @click="closeTech()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 bg-gray-100 dark:bg-gray-700 p-1 rounded-full transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        <h4 class="text-lg font-extrabold text-gray-900 dark:text-white mb-2">Mi experiencia</h4>
                        <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed" x-text="activeTech?.description"></p>
                    </div>
                </div>
            </div>
        </div>
        </template>
        </section>

        <!-- CTA FINAL -->
        <div class="mt-20 text-center">
            <p class="text-lg text-gray-600 dark:text-gray-300 mb-6">¿Quieres verlo aplicado en proyectos reales?</p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('public.projects') }}" class="inline-flex items-center justify-center px-8 py-4 text-base font-bold text-white transition-all duration-200 bg-indigo-600 rounded-lg hover:bg-indigo-700 hover:shadow-lg hover:-translate-y-1">
                    Explora mis proyectos
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
                <a href="{{ route('public.contact') }}" class="group inline-flex items-center justify-center font-semibold text-gray-900 dark:text-white bg-white/80 dark:bg-gray-800/60 border border-gray-300 dark:border-gray-600 hover:border-indigo-500 hover:text-indigo-600 dark:hover:border-indigo-400 dark:hover:text-indigo-300 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 hover:-translate-y-1 backdrop-blur-sm px-8 py-4 text-base">
                    ¿Hablamos?
                </a>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<!-- Tarjetas skills técnicas -->
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('skillsComponent', () => ({
        modalOpen: false,
        activeSkill: null,
        activeTech: null,
        showTechDetails: false,
        _skillCardsIo: null,
        _skillCardsMqHandler: null,

        init() {
            this.$nextTick(() => this.bindSkillCardViewportHints());
        },

        bindSkillCardViewportHints() {
            const sectionEl = this.$el;
            if (!sectionEl || !window.IntersectionObserver) return;

            const mq = window.matchMedia('(max-width: 767px)');
            const clearClasses = () => {
                sectionEl.querySelectorAll('.skill-card').forEach((el) => el.classList.remove('skill-card--in-view'));
            };
            const stop = () => {
                if (this._skillCardsIo) {
                    this._skillCardsIo.disconnect();
                    this._skillCardsIo = null;
                }
                clearClasses();
            };
            const start = () => {
                if (this._skillCardsIo) return;
                this._skillCardsIo = new IntersectionObserver(
                    (entries) => {
                        entries.forEach((e) => {
                            e.target.classList.toggle('skill-card--in-view', e.isIntersecting);
                        });
                    },
                    { threshold: 0.35, rootMargin: '0px 0px -6% 0px' },
                );
                sectionEl.querySelectorAll('.skill-card').forEach((el) => this._skillCardsIo.observe(el));
            };
            const sync = () => {
                if (mq.matches) start();
                else stop();
            };

            if (this._skillCardsMqHandler) {
                if (typeof mq.removeEventListener === 'function') {
                    mq.removeEventListener('change', this._skillCardsMqHandler);
                } else {
                    mq.removeListener(this._skillCardsMqHandler);
                }
            }
            this._skillCardsMqHandler = sync;
            if (typeof mq.addEventListener === 'function') mq.addEventListener('change', this._skillCardsMqHandler);
            else mq.addListener(this._skillCardsMqHandler);
            sync();
        },
        
        skillsData: {
            web: {
                title: 'Desarrollo Web & Frameworks',
                image: 'https://images.pexels.com/photos/1181675/pexels-photo-1181675.jpeg?auto=compress&cs=tinysrgb&w=800',
                icon: 'M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9',
                color: 'text-indigo-600 dark:text-indigo-400',
                bg: 'bg-indigo-50 dark:bg-indigo-900/30',
                description: 'Mi núcleo de trabajo diario. Monto webs de portfolio, tiendas online, ERPs(Gestión de recursos internos para negocios) y CRMs.(Sistemas internos para gestión de clientes).',
                technologies:[
                    { name: 'Laravel', badge: '/img/badges/1e130961e075.svg', description: 'Mi framework principal de backend: Me permite desplegar webs sólidas en minutos. Lo utilizo a diario para gestionar autenticaciones seguras y orquestar toda la lógica de negocio de mis proyectos usando Eloquent ORM.' },
                    { name: 'PHP', badge: '/img/badges/f5c6b71286fe.svg', description: 'Es el estándar en desarrollo web; PHP es el motor de la mayoría de mis desarrollos backend. He evolucionado con el lenguaje, aprovechando su tipado fuerte en las últimas versiones para escribir código limpio, moderno y orientado a objetos.' },
                    { name: 'JavaScript', badge: '/img/badges/77d25046793a.svg', description: 'Lo uso para dar vida a mis interfaces. Desde manipular el DOM de forma directa hasta consumir mis propias APIs asíncronas, es mi herramienta clave para crear una experiencia de usuario fluida.' },
                    { name: 'Tailwind CSS', badge: '/img/badges/4ae9e1648062.svg', description: 'Mi framework CSS de cabecera. Es una mejora a simplemente usar CSS: Agiliza enormemente mi flujo de trabajo maquetando directamente en el HTML lo cual crea un código más limpio y mejor arquitectura. CSS todavía tiene sus usos, especialmente para elementos repetitivos/consistentes.' },
                    { name: 'HTML5', badge: '/img/badges/363d3478c7fc.svg', description: 'La base de todo proyecto web. He usado HTML en todos mis proyectos web ( Aunque obviamente en proyectos con CMS no se usa apenas pues se programa mediante bloques, lo cual puede servir para proyectos rápidos y simples, pero no hay nada tan flexible y básico para diseñar web como HTML).' },
                    { name: 'CSS3', badge: '/img/badges/f34158676dea.svg', description: 'Aunque use frameworks CSS, el uso de CSS nativo sigue teniendo cabida para los detalles precisos o cuando se repite un estilo en varios elementos. Además también lo he trabajado en proyectos no tan modernos mientras trabajé con empresas de ERP.' },
                    { name: 'jQuery', badge: '/img/badges/7f621bde77c4.svg', description: 'Me ha salvado la vida al tomar el relevo de proyectos heredados. Aún lo utilizo para dar mantenimiento a sistemas más antiguos o implementar scripts rápidos de validación.' },
                    { name: 'Bootstrap', badge: '/img/badges/c02f51fc90f2.svg', description: 'Mi opción rápida y segura cuando necesito levantar el panel de administración de un CRM o un dashboard interno. Me permite entregar prototipos funcionales y estables en tiempo récord.' }
                ]
            },
            movil: {
                title: 'Desarrollo Multiplataforma & Móvil',
                image: 'https://www.addevice.io/storage/ckeditor/uploads/images/65f840d316353_mobile.app.development.1920.1080.png',
                icon: 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z',
                color: 'text-green-600 dark:text-green-400',
                bg: 'bg-green-50 dark:bg-green-900/30',
                description: 'Desarrollo apps nativas para Android que luego también puedo adaptar a dispositivos de Apple (IOS). Además he diseñado videojuegos en Unity para móviles y VR.',
                technologies:[
                    { name: 'Kotlin', badge: '/img/badges/3e7765600493.svg', description: 'Es el lenguaje recomendado para el desarrollo móvil y lo he estado usando intensivamente al desarrollar apps nativas como mi aplicación "Platorama". Es uno de los lenguajes con los que más familiarizado estoy al haber pasado mucho tiempo desarrollando en Android Studio.' },
                    { name: 'Android Studio', badge: '/img/badges/e19734b13425.svg', description: 'Mi centro de operaciones para crear apps móviles. Aquí es donde gestiono todo el ciclo de vida: desde el diseño de la interfaz y la inyección de dependencias, hasta el perfilado de rendimiento y la compilación final.' },
                    { name: 'C++', badge: '/img/badges/562c88e924d4.svg', description: 'C++ es un lenguaje pilar de la programación y actualmente sigue teniendo uso para programación a bajo nivel. Aunque no estoy tan familiarizado con él, sí que lo he estudiado y estado usando durante un tiempo para diseñar juegos en Unreal Engine.' },
                    { name: 'C#', badge: '/img/badges/fadd1176c129.svg', description: 'El lenguaje que utilizo principalmente como motor lógico detrás de Unity. Con él he programado comportamientos complejos, físicas y herramientas personalizadas orientadas a objetos.' },
                    { name: 'Unity', badge: '/img/badges/507011fae431.svg', description: 'Mi motor de desarrollo de confianza para desarrollar apps interactivas y videojuegos. Lo he utilizado para desarrollar tanto videojuegos (de móvil y PC) como simulaciones y entornos inmersivos de realidad virtual (VR).' }
                ]
            },
            ecommerce: {
                title: 'E-commerce, ERPs & CMS',
                image: 'https://images.pexels.com/photos/4968391/pexels-photo-4968391.jpeg?auto=compress&cs=tinysrgb&w=800',
                icon: 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
                color: 'text-pink-600 dark:text-pink-400',
                bg: 'bg-pink-50 dark:bg-pink-900/30',
                description: 'Digitalizo negocios implementando tiendas online y desarrollando programas internos de gestión de los recursos (ERP) y clientes (CRM).',
                technologies:[
                    { name: 'PrestaShop', badge: '/img/badges/b27b6bbdb681.svg', description: 'Lo utilizo para montar tiendas online rápidamente con un sistema ya establecido. He trabajado con él durante mi trabajo como programador en "Al Rescate". No solo lo he configurado, también he desarrollado módulos a medida en PHP y adaptado plantillas para cubrir flujos de venta B2B y B2C muy específicos.' },
                    { name: 'Dolibarr ERP', badge: '/img/badges/2a794847ac0e.svg', description: 'He usado este sistema para digitalizar la gestión de empresas durante mi trabajo en "Al rescate". Lo he usado para darle a clientes el control total de facturación, almacén e incluso lo he sincronizado por API con sus tiendas web.' },
                    { name: 'Stripe', badge: '/img/badges/4353a39426d3.svg', description: 'Pagos online y facturación: Checkout, Payment Intents, webhooks y cuentas conectadas cuando hace falta marketplace. Lo integro desde backend (Laravel u otros) para no depender de plugins rígidos y controlar flujos, idempotencia y seguridad (SCA, 3DS) al detalle.' },
                    { name: 'Laravel Cashier', badge: '/img/badges/40e6ea524c21.svg', description: 'Para SaaS y tiendas con suscripciones en Laravel: planes, pruebas, renovaciones y portal de facturación del cliente sobre Stripe. Encaja con mi stack habitual y sube el nivel frente a solo “instalar un plugin de pago”.' }
                ]
            },
            bbdd: {
                title: 'Bases de Datos (SGBD)',
                image: 'https://images.pexels.com/photos/669615/pexels-photo-669615.jpeg?auto=compress&cs=tinysrgb&w=800',
                icon: 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4',
                color: 'text-blue-600 dark:text-blue-400',
                bg: 'bg-blue-50 dark:bg-blue-900/30',
                description: 'Todo proyecto complejo en el que trabajo requiere gestión de datos: Diseño estructuras de datos buscando los mejores patrones de diseño para asegurar la integridad y escalabilidad de los datos.',
                technologies:[
                    { name: 'MySQL', badge: '/img/badges/395e67aa27fb.svg', description: 'El pilar de los datos de mis proyectos web. Diseño esquemas relacionales desde cero, optimizo índices para acelerar búsquedas y lanzo consultas SQL crudas complejas para reportes internos.' },
                    { name: 'MariaDB', badge: '/img/badges/bf8e773c789f.svg', description: 'La alternativa de código abierto y altísimo rendimiento que suelo montar cuando configuro mis propios servidores Linux, dándome total tranquilidad en la gestión de miles de registros.' },
                    { name: 'Firebase', badge: '/img/badges/0f4cb4423c97.svg', description: 'He usado mucho Firebase en proyectos de desarrollo móvil como en la red social que desarrollé: "Platorama". Además utilizo su base de datos NoSQL para sincronización en tiempo real, autenticación de usuarios y envíos masivos de notificaciones Push.' },
                    { name: 'SQLite', badge: '/img/badges/da4850e1b748.svg', description: 'Mi comodín ligero. Lo utilizo para el almacenamiento local persistente en mis apps Android (para que funcionen offline) y para ejecutar baterías de testing ultrarrápidas en Laravel.' },
                    { name: 'phpMyAdmin', badge: '/img/badges/a66f789cbc49.svg', description: 'La herramienta visual clásica a la que recurro en entornos de hosting compartido para hacer volcados rápidos de datos o gestionar privilegios de usuarios directamente en producción.' },
                    { name: 'HeidiSQL', badge: '/img/badges/76608b557f7c.svg', description: 'El cliente SQL que abro cada día en mi equipo. Me permite conectarme remotamente a las bases de datos de mis clientes para lanzar scripts de mantenimiento o hacer migraciones masivas.' }
                ]
            },
            infra: {
                title: 'Infraestructura & DevOps',
                image: 'https://images.pexels.com/photos/1181354/pexels-photo-1181354.jpeg?auto=compress&cs=tinysrgb&w=800',
                icon: 'M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2',
                color: 'text-orange-600 dark:text-orange-400',
                bg: 'bg-orange-50 dark:bg-orange-900/30',
                description: 'No solo escribo código, también lo pongo en producción. Publico las aplicaciones, gestiono los servidores, el control de versiones y el posicionamiento en motores de búsqueda (SEO).',
                technologies:[
                    { name: 'Docker', badge: '/img/badges/4a0b7990fd5c.svg', description: 'Lo uso para acabar con el problema de "en mi máquina funciona" cuando pretendo compartir el proyecto o migrarlo a un servidor. Containerizando con entornos como Sail, garantizo que el código se comporte exactamente igual en mi PC que en el servidor.' },
                    { name: 'Nginx', badge: '/img/badges/53549340feab.svg', description: 'El motor de mis servidores VPS, esta web y la mayoría de webs que he hecho las hosteo en mi servidor privado con Nginx. Lo configuro como proxy inverso para despachar aplicaciones web y soportar grandes picos de concurrencia de forma supereficiente.' },
                    { name: 'Apache', badge: '/img/badges/8de1e97aa08f.svg', description: 'Aunque actualmente prefiera usar Nginx sobre Apache por ser más moderno, veloz y optimizado, he usado mucho Apache en mi tiempo desarrollando en "Al Rescate" usando XAMPP y aprecio que todavía tiene algunas ventajas como servidor, especialmente para contenido dinámico y complejo.' },
                    { name: 'Git', badge: '/img/badges/b95677e7de4c.svg', description: 'Es la herramienta que más uso pues es fundamental en cualquier proyecto de desarrollo de software: Me permite trabajar con ramas estructuradas, experimentar sin romper nada y contar con puntos de guardado.' },
                    { name: 'GitHub', badge: '/img/badges/df3262e9902b.svg', description: 'El hogar de mi código. Además de mis repositorios Git en la nube, lo utilizo para establecer flujos de trabajo profesionales donde puedo trabajar con otros desarrolladores, automatizar los despliegues a producción (CI/CD) o participar en proyectos públicos.' },
                    { name: 'Postman', badge: '/img/badges/93f7dd8a58e9.svg', description: 'Mi banco de pruebas en proyectos web. Antes de escribir una sola línea en el frontend, lo uso para estresar y validar mis APIs, asegurándome de que cada endpoint responda con la data exacta.' },
                    { name: 'Bash', badge: '/img/badges/4c53680fccb7.svg', description: 'Paso gran parte de mi tiempo conectado a servidores Linux por SSH a través de Bash. En la terminal, actualizo dependencias, administro el contenido o ejecuto mis propios scripts para automatizar rutinas pesadas, como los sistemas de copias de seguridad.' },
                    { name: 'FileZilla', badge: '/img/badges/51e1d9a65cfb.svg', description: 'Mi herramienta SFTP: Aunque gestionar servidores por Bash suele ser suficiente, a menudo uso Filezilla para conectarme de forma rápida por SFTP a servidores para comprobarlos o administrar el contenido de forma rápida si no se trata de muchos archivos (En cuyo caso preferiría subir un .zip y descomprimirlo con bash).' }
                ]
            },
            arquitectura: {
                title: 'Arquitectura y Patrones',
                image: 'https://miro.medium.com/v2/resize:fit:1200/1*RiuRKtGDcgBQgoI9-JE-kg.jpeg',
                icon: 'M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                color: 'text-purple-600 dark:text-purple-400',
                bg: 'bg-purple-50 dark:bg-purple-900/30',
                description: 'La diferencia entre un código que "funciona" y uno "profesional". Me tomo en serio estudiar y aplicar principios de ingeniería para crear software escalable y libre de deuda técnica.',
                technologies:[
                    { name: 'Clean Architecture', badge: '/img/badges/1578cdd9dc58.svg', description: 'Me permite tener código separado por responsabilidades y escalable. Aislando el núcleo del negocio de la infraestructura consigo que cambiar de base de datos o framework en el futuro no implique reescribir toda la aplicación.' },
                    { name: 'SOLID Principles', badge: '/img/badges/897bc2e25e59.svg', description: 'Considero que son principios básicos que todo programador debe conocer para un buen código. Aplicar estos principios permite escribir un código modular y testeable, que no se convierta en una pesadilla cuando haya que hacerle mantenimiento años después.' },
                    { name: 'Design Patterns', badge: '/img/badges/7b9360a19e5c.svg', description: 'No reinvento la rueda. Ante problemas de diseño recurrentes, aplico patrones probados (Observer, Factory, Repository, Singleton) para que mis soluciones sean elegantes y entendibles por otros.' },
                    { name: 'MVVM', badge: '/img/badges/91208e192526.svg', description: 'La arquitectura que estructura mis apps móviles modernas como "Platorama". Desacoplar la interfaz gráfica de la lógica de negocio me ha permitido tener interfaces reactivas, predecibles y fáciles de probar.' },
                    { name: 'REST APIs', badge: '/img/badges/ee4cf04d0957.svg', description: 'Es como comunico mis sistemas. Me aseguro de diseñar APIs sin estado y sumamente lógicas, utilizando los verbos HTTP correctos, tokens JWT y códigos de estado semánticos en cada respuesta.' }
                ]
            }
        },
        
        openModal(skillKey) {
            document.documentElement.classList.add('skills-modal-open');
            document.body.classList.add('skills-modal-open');
            this.activeSkill = this.skillsData[skillKey];
            this.showTechDetails = false;
            this.activeTech = null;
            this.modalOpen = true;
        },
        closeModal() {
            this.modalOpen = false;
            this.showTechDetails = false;
            setTimeout(() => {
                this.activeSkill = null;
                this.activeTech = null;
            }, 500); // Espera a que termine la animación css
            document.documentElement.classList.remove('skills-modal-open');
            document.body.classList.remove('skills-modal-open');
        },
        openTech(tech) {
            // Si hace click en la misma que ya está abierta, la cierra
            if (this.activeTech === tech) {
                this.closeTech();
            } else {
                this.activeTech = tech;
                this.showTechDetails = true;
            }
        },
        closeTech() {
            this.showTechDetails = false;
            setTimeout(() => this.activeTech = null, 400);
        }
    }))
})
</script>
@endpush
