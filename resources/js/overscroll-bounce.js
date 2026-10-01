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
const RELEASE = 'translate 520ms cubic-bezier(0.34, 1.56, 0.64, 1)';
const WHEEL_IDLE = 140; // ms sin rueda para considerar que se ha soltado
const KICK_GAIN = 28; // px de rebote por cada px/ms de velocidad al llegar al borde
const KICK_MAX = 0.12; // rebote máximo, en fracción del ancho visible
const KICK_MIN_SPEED = 0.25; // px/ms; por debajo, la inercia ya llega casi parada
const COAST_IDLE = 160; // ms sin eventos de scroll para dar la inercia por acabada

const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

// Cuanto más se estira, menos avanza; nunca llega a recorrer `size`
const rubber = (distance, size) =>
    Math.sign(distance) * (1 - 1 / ((Math.abs(distance) * RESISTANCE) / size + 1)) * size;

export function overscrollBounce(scroller, { content = scroller.firstElementChild } = {}) {
    if (!content) return () => {};

    let stretch = 0; // distancia arrastrada más allá del borde (sin amortiguar)
    let startX = 0;
    let startY = 0;
    let lastX = 0;
    let axis = null; // 'x' | 'y' según la dirección del gesto
    let wheelTimer = null;
    let coasting = false; // inercia tras soltar el dedo
    let coastTimer = null;
    let kick = null;
    let prevLeft = scroller.scrollLeft;
    let prevTime = 0;
    let velocity = 0; // px/ms, positivo hacia el final

    const maxScroll = () => scroller.scrollWidth - scroller.clientWidth;
    const atStart = () => scroller.scrollLeft <= 0;
    const atEnd = () => scroller.scrollLeft >= maxScroll() - 1;

    const paint = () => {
        const offset = rubber(stretch, scroller.clientWidth);
        content.style.translate = offset ? `${offset}px 0` : '';
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
        content.style.transition = 'none';
        paint();
    };

    const release = () => {
        if (stretch === 0) return;
        stretch = 0;
        content.style.transition = RELEASE;
        paint();
    };

    // La inercia ha llegado al borde: el contenido se pasa un poco y vuelve
    const bounceFromInertia = (speed) => {
        const distance = Math.min(Math.abs(speed) * KICK_GAIN, scroller.clientWidth * KICK_MAX);
        const offset = -Math.sign(speed) * distance;
        kick?.cancel();
        kick = content.animate(
            [
                { translate: '0px 0', easing: 'cubic-bezier(0.15, 0.75, 0.35, 1)' },
                { translate: `${offset}px 0`, offset: 0.28, easing: 'cubic-bezier(0.34, 1.4, 0.64, 1)' },
                { translate: '0px 0' },
            ],
            { duration: 640 },
        );
    };

    const onScroll = () => {
        const now = performance.now();
        const left = scroller.scrollLeft;
        const dt = now - prevTime;
        const wasAtEdge = prevLeft <= 0 || prevLeft >= maxScroll() - 1;
        if (dt > 0 && dt < 100) velocity = (left - prevLeft) / dt;
        prevLeft = left;
        prevTime = now;

        if (!coasting) return;
        clearTimeout(coastTimer);
        coastTimer = setTimeout(() => (coasting = false), COAST_IDLE);

        const arrived = !wasAtEdge && (atStart() || atEnd());
        if (arrived && Math.abs(velocity) >= KICK_MIN_SPEED && !reducedMotion.matches) {
            coasting = false;
            bounceFromInertia(velocity);
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
        kick?.cancel();
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
        kick?.cancel();
        content.style.translate = '';
        content.style.transition = '';
        scroller.style.overscrollBehaviorX = '';
    };
}

document.querySelectorAll('[data-overscroll-bounce]').forEach((scroller) => {
    const selector = scroller.dataset.overscrollBounce;
    overscrollBounce(scroller, {
        content: selector ? scroller.querySelector(selector) : undefined,
    });
});
