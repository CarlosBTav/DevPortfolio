/**
 * Rebote elástico al llegar al borde de un contenedor con scroll horizontal.
 *
 * Si se sigue arrastrando cuando ya no queda más por desplazar, el contenido
 * cede cada vez menos (efecto goma, como la barra de volumen de Android) y al
 * soltar vuelve a su sitio con un pequeño rebote, en vez de pararse en seco.
 * Funciona con el dedo y con el desplazamiento horizontal del trackpad. Si se
 * suelta con impulso y la inercia llega al borde, también rebota en lugar de
 * frenar en seco, con un rebote proporcional a la velocidad de llegada.
 *
 * Todo el movimiento lo resuelve un único muelle amortiguado que parte de la
 * posición y la velocidad reales, así que nunca hay saltos: ni al llegar con
 * el ajuste por tarjeta (scroll-snap), que frena muy despacio, ni al volver a
 * tocar mientras rebota.
 *
 * Uso: añade `data-overscroll-bounce` al contenedor con scroll; se mueve su
 * primer hijo. Para otro elemento: `data-overscroll-bounce="selector"`.
 * Desde JS: `overscrollBounce(scroller, { content })` devuelve una función
 * para desmontarlo.
 *
 * Se aplica con la propiedad CSS `translate`, independiente de `transform`,
 * así que convive con otros efectos que muevan el mismo elemento (p. ej. el
 * carrusel automático de «Sobre mí»).
 */

const RESISTANCE = 0.55; // cuanto menor, más dura la goma
const WHEEL_IDLE = 140; // ms sin rueda para considerar que se ha soltado
const COAST_IDLE = 160; // ms sin eventos de scroll para dar la inercia por acabada
const VELOCITY_WINDOW = 60; // ms de muestras de scroll para estimar la velocidad
const KICK_MAX = 0.12; // rebote máximo, en fracción del ancho visible

// Muelle amortiguado: una sola física para soltar el estirón y para el rebote
// de la inercia, así el movimiento siempre parte de la posición y velocidad
// reales (sin saltos) y se asienta con una pizca de elasticidad
const OMEGA = 0.015; // rad/ms: rigidez (≈ medio segundo hasta asentarse)
const ZETA = 0.82; // < 1: se pasa un poquito de largo antes de quedarse quieto
const OMEGA_D = OMEGA * Math.sqrt(1 - ZETA * ZETA);

const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

// Cuanto más se estira, menos avanza; nunca llega a recorrer `size`
const rubber = (distance, size) =>
    Math.sign(distance) * (1 - 1 / ((Math.abs(distance) * RESISTANCE) / size + 1)) * size;

// Inversa de `rubber`: qué estirón corresponde a un desplazamiento ya pintado
const unrubber = (offset, size) => {
    const ratio = Math.min(Math.abs(offset) / size, 0.99);
    return Math.sign(offset) * (size / RESISTANCE) * (1 / (1 - ratio) - 1);
};

// Posición del muelle en el instante t (ms) partiendo de x0 px con v0 px/ms
const springAt = (x0, v0, t) => {
    const decay = Math.exp(-ZETA * OMEGA * t);
    const b = (v0 + ZETA * OMEGA * x0) / OMEGA_D;
    return decay * (x0 * Math.cos(OMEGA_D * t) + b * Math.sin(OMEGA_D * t));
};

export function overscrollBounce(scroller, { content = scroller.firstElementChild } = {}) {
    if (!content) return () => {};

    let stretch = 0; // distancia arrastrada más allá del borde (sin amortiguar)
    let offset = 0; // desplazamiento pintado ahora mismo
    let startX = 0;
    let startY = 0;
    let lastX = 0;
    let axis = null; // 'x' | 'y' según la dirección del gesto
    let wheelTimer = null;
    let coasting = false; // inercia (o ajuste a tarjeta) tras soltar el dedo
    let coastTimer = null;
    let spring = null; // { x0, v0, start, frame }
    let samples = []; // [{ t, left }] del scroll reciente

    const maxScroll = () => scroller.scrollWidth - scroller.clientWidth;
    const atStart = () => scroller.scrollLeft <= 0;
    const atEnd = () => scroller.scrollLeft >= maxScroll() - 1;

    const paint = (value) => {
        offset = Math.abs(value) < 0.05 ? 0 : value;
        content.style.translate = offset ? `${offset}px 0` : '';
    };

    const stopSpring = () => {
        if (!spring) return;
        cancelAnimationFrame(spring.frame);
        spring = null;
    };

    const runSpring = (x0, v0) => {
        stopSpring();
        const step = (now) => {
            const t = now - spring.start;
            const x = springAt(x0, v0, t);
            // Asentado: ya no se aprecia movimiento
            if (t > 120 && Math.abs(x) < 0.3 && Math.abs(springAt(x0, v0, t + 16) - x) < 0.1) {
                spring = null;
                paint(0);
                return;
            }
            paint(x);
            spring.frame = requestAnimationFrame(step);
        };
        spring = { start: performance.now(), frame: requestAnimationFrame(step) };
    };

    // Si se toca mientras rebota, se sigue desde donde está, sin saltar
    const grab = () => {
        if (!spring) return;
        stopSpring();
        stretch = offset ? unrubber(offset, scroller.clientWidth) : 0;
        if (stretch !== 0) scroller.scrollLeft = stretch > 0 ? 0 : maxScroll();
    };

    const pull = (delta) => {
        // delta > 0: el contenido se mueve a la derecha (tirando del inicio)
        if (maxScroll() <= 0) return; // ahora mismo no hay scroll (p. ej. rejilla en escritorio)
        if (stretch === 0) {
            if (!((delta > 0 && atStart()) || (delta < 0 && atEnd()))) return;
        }
        const next = stretch + delta;
        // Al deshacer el estirón se vuelve al scroll normal sin pasarse
        stretch = Math.sign(next) === Math.sign(stretch) || stretch === 0 ? next : 0;
        // El scroll nativo también se mueve al volver hacia dentro: se fija al borde
        if (stretch !== 0) scroller.scrollLeft = stretch > 0 ? 0 : maxScroll();
        paint(rubber(stretch, scroller.clientWidth));
    };

    const release = () => {
        if (stretch === 0) return;
        stretch = 0;
        runSpring(offset, 0);
    };

    // Velocidad media de los últimos milisegundos; si el scroll se ha
    // parado o va a trompicones, cuenta como lenta (nunca un valor viejo)
    const velocity = (now) => {
        samples = samples.filter((sample) => now - sample.t <= VELOCITY_WINDOW);
        if (samples.length < 2) return 0;
        const first = samples[0];
        const last = samples[samples.length - 1];
        const dt = last.t - first.t;
        return dt > 0 ? (last.left - first.left) / dt : 0;
    };

    // La inercia ha llegado al borde: el contenido sigue con la misma
    // velocidad, se frena como contra una goma y vuelve a su sitio
    const bounceFromInertia = (speed) => {
        // El muelle que sale de 0 con v0 llega a ≈ 0,42·v0/ω; la velocidad se
        // satura suavemente para no pasar nunca de KICK_MAX del ancho visible
        const limit = (scroller.clientWidth * KICK_MAX * OMEGA) / 0.42;
        const v0 = -limit * Math.tanh(speed / limit);
        runSpring(0, v0);
    };

    const onScroll = () => {
        const now = performance.now();
        const prev = samples[samples.length - 1];
        const wasAtEdge = prev ? prev.left <= 0 || prev.left >= maxScroll() - 1 : true;
        samples.push({ t: now, left: scroller.scrollLeft });

        if (!coasting) return;
        clearTimeout(coastTimer);
        coastTimer = setTimeout(() => (coasting = false), COAST_IDLE);

        if (!wasAtEdge && (atStart() || atEnd()) && !reducedMotion.matches) {
            coasting = false;
            bounceFromInertia(velocity(now));
        }
    };

    const onTouchEnd = () => {
        const wasStretched = stretch !== 0;
        release();
        // Si se soltó estirando, ya rebota; si no, queda la inercia por vigilar
        coasting = !wasStretched;
        clearTimeout(coastTimer);
        coastTimer = setTimeout(() => (coasting = false), COAST_IDLE);
    };

    const onTouchStart = (event) => {
        coasting = false;
        grab();
        const touch = event.touches[0];
        startX = lastX = touch.clientX;
        startY = touch.clientY;
        axis = null;
    };

    const onTouchMove = (event) => {
        if (reducedMotion.matches) return;
        const touch = event.touches[0];
        if (!axis) {
            const dx = Math.abs(touch.clientX - startX);
            const dy = Math.abs(touch.clientY - startY);
            if (dx < 4 && dy < 4) return;
            axis = dx > dy ? 'x' : 'y';
        }
        const delta = touch.clientX - lastX;
        lastX = touch.clientX;
        if (axis === 'x') pull(delta);
    };

    const onWheel = (event) => {
        if (reducedMotion.matches) return;
        if (Math.abs(event.deltaX) <= Math.abs(event.deltaY)) return;
        grab();
        pull(-event.deltaX);
        clearTimeout(wheelTimer);
        wheelTimer = setTimeout(release, WHEEL_IDLE);
    };

    // Sin esto, Android añadiría su propio brillo/estirado y el navegador
    // podría interpretar el gesto como «atrás»
    scroller.style.overscrollBehaviorX = 'contain';

    const events = [
        ['touchstart', onTouchStart],
        ['touchmove', onTouchMove],
        ['touchend', onTouchEnd],
        ['touchcancel', release],
        ['wheel', onWheel],
        ['scroll', onScroll],
    ];
    events.forEach(([name, handler]) => scroller.addEventListener(name, handler, { passive: true }));

    return () => {
        events.forEach(([name, handler]) => scroller.removeEventListener(name, handler));
        clearTimeout(wheelTimer);
        clearTimeout(coastTimer);
        stopSpring();
        content.style.translate = '';
        scroller.style.overscrollBehaviorX = '';
    };
}

document.querySelectorAll('[data-overscroll-bounce]').forEach((scroller) => {
    const selector = scroller.dataset.overscrollBounce;
    overscrollBounce(scroller, {
        content: selector ? scroller.querySelector(selector) : undefined,
    });
});
