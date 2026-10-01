/**
 * Carrusel automático de la experiencia profesional («Sobre mí»).
 *
 * Mientras la fila se muestra como carrusel horizontal (móvil/tablet), se
 * desplaza sola de un extremo a otro: acelera y frena suavemente, espera un
 * momento en cada extremo y vuelve en sentido contrario. En cuanto la persona
 * la toca o la desplaza, se para; retoma tras unos segundos sin interacción
 * desde donde la haya dejado. Respeta «reducir movimiento».
 *
 * Fluidez: el avance automático mueve la lista con transform (sub-píxel y en
 * la GPU) en vez de scrollLeft, que el navegador redondea y da tirones a
 * velocidades lentas. Al tocar, el desplazamiento se traspasa a scrollLeft
 * para que el gesto manual parta exactamente del mismo punto.
 */

const SPEED = 28; // px por segundo en el tramo central
const HOLD = 1800; // ms de espera en cada extremo
const RESUME_DELAY = 3500; // ms sin tocar antes de seguir

const desktop = window.matchMedia('(min-width: 1024px)');
const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

// Curva coseno: parte y llega con velocidad cero, sin frenazos
const ease = (t) => (1 - Math.cos(Math.PI * t)) / 2;

function setup(scroller) {
    const list = scroller.querySelector('ol');

    let running = false;
    let frame = null;
    let visible = false;
    let usingTransform = false;
    let position = 0;
    let max = 0;
    let direction = 1;
    let segment = null;
    let holdUntil = 0;
    let pausedUntil = 0;

    const toTransform = () => {
        position = scroller.scrollLeft;
        max = Math.max(0, scroller.scrollWidth - scroller.clientWidth);
        scroller.scrollLeft = 0;
        list.style.transform = `translate3d(${-position}px, 0, 0)`;
        usingTransform = true;
    };

    const toScroll = () => {
        if (!usingTransform) return;
        list.style.transform = '';
        scroller.scrollLeft = position;
        usingTransform = false;
    };

    const tick = (now) => {
        frame = requestAnimationFrame(tick);

        if (!visible || document.hidden || now < pausedUntil) {
            segment = null; // al seguir, arranca suave desde donde esté
            return;
        }
        if (!usingTransform) toTransform();
        if (max <= 0 || now < holdUntil) return;

        if (!segment) {
            const to = direction > 0 ? max : 0;
            const distance = Math.abs(to - position);
            if (distance < 1) {
                direction = -direction;
                return;
            }
            // La curva recorre el tramo central a ~SPEED; los extremos, más despacio
            const duration = Math.max(1200, (distance / SPEED) * 1000 * 1.4);
            segment = { from: position, to, start: now, duration };
        }

        const t = Math.min(1, (now - segment.start) / segment.duration);
        position = segment.from + (segment.to - segment.from) * ease(t);
        list.style.transform = `translate3d(${-position}px, 0, 0)`;

        if (t === 1) {
            segment = null;
            direction = -direction;
            holdUntil = now + HOLD;
        }
    };

    const interact = () => {
        pausedUntil = performance.now() + RESUME_DELAY;
        toScroll();
        // Al retomar, sigue hacia el extremo más lejano
        direction = scroller.scrollLeft > max / 2 ? -1 : 1;
        holdUntil = 0;
    };

    // Inercia del gesto: mientras siga desplazándose, no retomar
    const onScroll = () => {
        if (!usingTransform) pausedUntil = performance.now() + RESUME_DELAY;
    };

    const onResize = () => {
        if (usingTransform) {
            toScroll();
            toTransform();
        }
        segment = null;
    };

    const observer = new IntersectionObserver(([entry]) => {
        visible = entry.isIntersecting;
    });

    const events = [
        ['pointerdown', interact],
        ['touchstart', interact],
        ['wheel', interact],
        ['scroll', onScroll],
    ];

    const start = () => {
        // El ajuste por tarjeta pelearía con el avance continuo
        scroller.classList.remove('snap-x', 'snap-mandatory');
        list.style.willChange = 'transform';
        events.forEach(([name, handler]) => scroller.addEventListener(name, handler, { passive: true }));
        window.addEventListener('resize', onResize, { passive: true });
        observer.observe(scroller);
        running = true;
        frame = requestAnimationFrame(tick);
    };

    const stop = () => {
        if (!running) return;
        cancelAnimationFrame(frame);
        observer.disconnect();
        events.forEach(([name, handler]) => scroller.removeEventListener(name, handler));
        window.removeEventListener('resize', onResize);
        list.style.transform = '';
        list.style.willChange = '';
        usingTransform = false;
        segment = null;
        scroller.scrollLeft = 0;
        scroller.classList.add('snap-x', 'snap-mandatory');
        running = false;
    };

    const update = () => {
        if (desktop.matches || reducedMotion.matches) {
            stop();
        } else if (!running) {
            start();
        }
    };

    desktop.addEventListener('change', update);
    reducedMotion.addEventListener('change', update);
    update();
}

document.querySelectorAll('[data-experience-autoscroll]').forEach(setup);
