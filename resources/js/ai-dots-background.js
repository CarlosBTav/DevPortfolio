/**
 * Fondo de puntos animado (componente x-ai-dots-background).
 *
 * Rendimiento: el dibujo es idéntico al original, pero cada frame hace mucho
 * menos trabajo, lo que en móvil marcaba la diferencia entre ~8 y 60 fps:
 * - Las curvas de las ondas se muestrean una sola vez al arrancar, no por punto
 *   y por frame; y solo se miden los tramos que caen cerca del punto en X.
 * - El movimiento de cada onda se calcula una vez por frame, no por punto.
 * - Solo se pintan los puntos visibles (más un margen): en «Sobre mí» el canvas
 *   ocupa toda la altura de la página y la mayoría queda fuera de pantalla.
 * - Los puntos se agrupan por opacidad y se rellenan de una vez.
 * - El color se lee al cambiar de tema, no con getComputedStyle en cada frame.
 * - La animación se detiene si el fondo no está en pantalla o la pestaña oculta.
 */
import './ai-dots-touch.js';

const DEG = Math.PI / 180;
const CURVE_STEPS = 26;
const ALPHA_LEVELS = 64;
const R_MIN = 0.55;
const R_MAX = 1.62;
const A_MIN = 0.16;
const A_MAX = 1;
// Píxeles por encima y por debajo de la pantalla que también se repintan, para
// que al desplazarse rápido no asomen puntos de un frame anterior
const OVERSCAN = 0.75;
// Con las cintas mezcladas en el canvas cada píxel repintado cuesta mucho más,
// así que el margen es mínimo: tras el primer frame (que pinta el canvas entero)
// lo que asome sin repintar es el frame anterior, prácticamente igual
const MERGED_OVERSCAN = 0.1;

// Trail of recent pointer positions (desktop). Dots light up along it and fade out.
const TRAIL_LIFE = 650; // ms each trail point stays lit
const TRAIL_MAX = 60; // max stored points
const TRAIL_MIN_STEP = 4; // min px between sampled points
const TRAIL_SIGMA = 24; // px base radius of light around each point (small = tight spots)
// Radio máximo de un punto de la estela (s ≤ 1.5, wobble ≤ 1.54); a 3σ su aporte
// es < 0,0002 y se descarta sin calcular ángulos ni exponenciales
const TRAIL_CUTOFF2 = (3 * TRAIL_SIGMA * 1.5 * 1.54) ** 2;

const WAVE_DEFS = [
    {
        duration: 7500,
        alternateReverse: false,
        radius: 68,
        radiusPulse: 0.16,
        phase: 0.2,
        keys: [
            { x: -0.3, y: -0.1, rotate: -5, scaleX: 0.95, scaleY: 0.72 },
            { x: 0, y: 0.15, rotate: 3, scaleX: 1.08, scaleY: 1.2 },
            { x: 0.3, y: -0.15, rotate: -2, scaleX: 1.02, scaleY: 0.86 },
        ],
        segments: [
            [[-200, 200], [100, 50], [300, 350], [600, 200]],
            [[600, 200], [900, 50], [1100, 350], [1400, 200]],
        ],
    },
    {
        duration: 9500,
        alternateReverse: true,
        radius: 54,
        radiusPulse: 0.18,
        phase: 2.1,
        keys: [
            { x: 0.3, y: 0.15, rotate: 5, scaleX: 1.08, scaleY: 1.12 },
            { x: 0, y: -0.1, rotate: -2, scaleX: 0.92, scaleY: 0.84 },
            { x: -0.3, y: 0.1, rotate: 3, scaleX: 1.12, scaleY: 1.28 },
        ],
        segments: [
            [[-200, 200], [200, 350], [400, 50], [700, 200]],
            [[700, 200], [1000, 350], [1200, 50], [1400, 200]],
        ],
    },
    {
        duration: 11000,
        alternateReverse: false,
        radius: 61,
        radiusPulse: 0.2,
        phase: 4.2,
        keys: [
            { x: -0.18, y: 0.18, rotate: 7, scaleX: 1.1, scaleY: 0.78 },
            { x: 0.12, y: -0.06, rotate: -5, scaleX: 0.88, scaleY: 1.22 },
            { x: 0.24, y: 0.14, rotate: 4, scaleX: 1.04, scaleY: 0.96 },
        ],
        segments: [
            [[-200, 180], [80, 310], [280, 90], [540, 210]],
            [[540, 210], [800, 330], [980, 80], [1400, 180]],
        ],
    },
];

function smoothstep(t) {
    if (t < 0) return 0;
    if (t > 1) return 1;
    return t * t * (3 - 2 * t);
}

function easeInOut(t) {
    return 0.5 - Math.cos(t * Math.PI) * 0.5;
}

function lerp(a, b, t) {
    return a + (b - a) * t;
}

function cubicPoint(points, t) {
    const mt = 1 - t;
    return [
        mt * mt * mt * points[0][0] + 3 * mt * mt * t * points[1][0] + 3 * mt * t * t * points[2][0] + t * t * t * points[3][0],
        mt * mt * mt * points[0][1] + 3 * mt * mt * t * points[1][1] + 3 * mt * t * t * points[2][1] + t * t * t * points[3][1],
    ];
}

/** Polilínea de cada onda en un Float64Array: [ax, ay, bx, by, minX, maxX] por tramo. */
function samplePolyline(segments) {
    const out = new Float64Array(segments.length * CURVE_STEPS * 6);
    let k = 0;
    for (const segment of segments) {
        let last = cubicPoint(segment, 0);
        for (let i = 1; i <= CURVE_STEPS; i++) {
            const next = cubicPoint(segment, i / CURVE_STEPS);
            out[k++] = last[0];
            out[k++] = last[1];
            out[k++] = next[0];
            out[k++] = next[1];
            out[k++] = Math.min(last[0], next[0]);
            out[k++] = Math.max(last[0], next[0]);
            last = next;
        }
    }
    return out;
}

const POLYLINES = WAVE_DEFS.map((def) => samplePolyline(def.segments));

function interpolateKeyframes(keys, progress) {
    let start = keys[0];
    let end = keys[1];
    let local = progress / 0.5;
    if (progress > 0.5) {
        start = keys[1];
        end = keys[2];
        local = (progress - 0.5) / 0.5;
    }
    local = easeInOut(local);
    return {
        x: lerp(start.x, end.x, local),
        y: lerp(start.y, end.y, local),
        rotate: lerp(start.rotate, end.rotate, local),
        scaleX: lerp(start.scaleX, end.scaleX, local),
        scaleY: lerp(start.scaleY, end.scaleY, local),
    };
}

function waveMotion(def, now) {
    const total = now / def.duration;
    const iteration = Math.floor(total);
    let progress = total - iteration;
    if ((def.alternateReverse && iteration % 2 === 0) || (!def.alternateReverse && iteration % 2 === 1)) {
        progress = 1 - progress;
    }
    return interpolateKeyframes(def.keys, progress);
}

/**
 * Distancia mínima a la polilínea, pero solo si es menor que `limit`: los tramos
 * cuyo rango en X ya queda a más de `limit` no pueden acercarse más y se saltan.
 */
function distanceToPolyline(px, py, poly, limit) {
    let closest = limit;
    for (let k = 0; k < poly.length; k += 6) {
        if (px < poly[k + 4] - closest || px > poly[k + 5] + closest) continue;
        const ax = poly[k];
        const ay = poly[k + 1];
        const vx = poly[k + 2] - ax;
        const vy = poly[k + 3] - ay;
        const wx = px - ax;
        const wy = py - ay;
        const len = vx * vx + vy * vy || 1;
        let t = (wx * vx + wy * vy) / len;
        if (t < 0) t = 0;
        else if (t > 1) t = 1;
        const dx = px - (ax + vx * t);
        const dy = py - (ay + vy * t);
        const d = Math.sqrt(dx * dx + dy * dy);
        if (d < closest) closest = d;
    }
    return closest;
}

/** Parámetros de cada onda que no dependen del punto: se calculan una vez por frame. */
function frameWaves(now, cw, ch) {
    return WAVE_DEFS.map((def, i) => {
        const motion = waveMotion(def, now);
        return {
            poly: POLYLINES[i],
            radius: def.radius * (1 + Math.sin(now * 0.00055 + def.phase) * def.radiusPulse),
            offX: motion.x * cw,
            offY: motion.y * ch,
            cos: Math.cos(-motion.rotate * DEG),
            sin: Math.sin(-motion.rotate * DEG),
            scaleX: motion.scaleX,
            scaleY: motion.scaleY,
        };
    });
}

const COS10 = Math.cos(10 * DEG);
const SIN10 = Math.sin(10 * DEG);

function waveBandFactor(gx, gy, cw, ch, waves) {
    // Giro de 10° del contenedor de las ondas, común a las tres
    const rx = gx - cw * 0.5;
    const ry = gy - ch * 0.5;
    const baseX = rx * COS10 - ry * SIN10;
    const baseY = rx * SIN10 + ry * COS10;
    const toViewX = 1000 / Math.max(cw * 2, 1);
    const toViewY = 400 / Math.max(ch, 1);

    let strongest = 0;
    for (let i = 0; i < waves.length; i++) {
        const w = waves[i];
        const x = baseX - w.offX;
        const y = baseY - w.offY;
        const vx = ((x * w.cos - y * w.sin) / w.scaleX + cw) * toViewX;
        const vy = ((x * w.sin + y * w.cos) / w.scaleY + ch * 0.5) * toViewY;
        const d = distanceToPolyline(vx, vy, w.poly, w.radius);
        if (d >= w.radius) continue;
        const band = 1 - smoothstep(d / w.radius);
        if (band > strongest) strongest = band;
    }
    return strongest;
}

function initOneRoot(root) {
    const canvas = root.querySelector('.js-ai-dots-canvas');
    const glow = root.querySelector('.js-ai-dots-glow');
    const maskLayer = root.querySelector('.ai-dots-mask-layer');
    if (!canvas || !canvas.getContext) return;

    const ctx = canvas.getContext('2d');
    let dots = [];
    let spacing = 28;
    const spacingAttr = parseInt(root.dataset.spacing, 10);
    if (spacingAttr >= 8 && spacingAttr <= 60) spacing = spacingAttr;
    const touchHitEl = root.dataset.aiDotsHit === 'stage'
        ? root.closest('.hr-photo-stage') || root.parentElement || canvas
        : canvas;
    const mouse = { x: -1000, y: -1000, px: -1000, py: -1000, speed: 0, radius: 140, ready: false };
    const trail = [];
    const touch = window.AiDotsTouch;
    const ripples = touch ? touch.createRippleState() : { list: [] };
    let touchPrimary = touch ? touch.isTouchPrimary() : false;
    let unbindRipples = null;
    let mouseMoveHandler = null;
    const buckets = Array.from({ length: ALPHA_LEVELS + 1 }, () => []);

    // Cintas y brillos se mezclan dentro del canvas si el navegador puede (ver createMaskCompositor)
    const compositor = createMaskCompositor(root, canvas, maskLayer, bakeWaves(root));

    let dotColor = '';
    const readTheme = () => {
        dotColor = 'rgb(' + (getComputedStyle(root).getPropertyValue('--ai-dots-dot').trim() || '232, 118, 74') + ')';
        compositor?.readTheme();
    };
    readTheme();
    // El tema cambia la clase de <html>; es lo único que altera los colores
    new MutationObserver(readTheme).observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });

    function visibilityForDot(gx, gy, cw, ch, now, waves) {
        let v = waveBandFactor(gx, gy, cw, ch, waves);
        if (touch && ripples.list.length > 0) {
            v += touch.visibilityBoost(gx, gy, ripples, now, { maxRadius: 280 });
        }
        if (!touchPrimary && trail.length > 0) {
            let tb = 0;
            for (let ti = 0; ti < trail.length; ti++) {
                const pt = trail[ti];
                const age = now - pt.t;
                if (age >= TRAIL_LIFE) continue;
                const tdx = pt.x - gx;
                const tdy = pt.y - gy;
                const d2 = tdx * tdx + tdy * tdy;
                if (d2 > TRAIL_CUTOFF2) continue;
                const fade = 1 - age / TRAIL_LIFE;
                // Irregular, organic blob: deform the radius by angle so edges are blotchy.
                const ang = Math.atan2(tdy, tdx);
                const wobble = 1 + 0.34 * Math.sin(ang * 3 + pt.seed) + 0.2 * Math.sin(ang * 5 - pt.seed * 1.7);
                const sig = TRAIL_SIGMA * pt.s * wobble;
                tb += Math.exp(-d2 / (sig * sig)) * fade * pt.a;
            }
            if (tb > 1) tb = 1;
            v += tb * 0.95;
        }
        if (v > 1) v = 1;
        else if (v < 0) v = 0;
        return v;
    }

    function updateDot(dot, now) {
        if (touch && ripples.list.length > 0) {
            touch.applyRippleForces(dot, ripples, now);
        }
        if (!touchPrimary) {
            const dx = mouse.x - dot.x;
            const dy = mouse.y - dot.y;
            const distance = Math.sqrt(dx * dx + dy * dy);
            if (distance < mouse.radius && mouse.speed > 0.5) {
                const force = (mouse.radius - distance) / mouse.radius;
                const angle = Math.atan2(dy, dx);
                let power = mouse.speed * 0.005;
                if (power > 1.2) power = 1.2;
                dot.vx -= Math.cos(angle) * force * power;
                dot.vy -= Math.sin(angle) * force * power;
            }
        }
        dot.x += dot.vx;
        dot.y += dot.vy;
        dot.vx *= 0.9;
        dot.vy *= 0.9;
        dot.x += (dot.baseX - dot.x) * 0.007;
        dot.y += (dot.baseY - dot.y) * 0.007;
    }

    let layoutW = 0;
    let layoutH = 0;

    function syncSize() {
        const w = Math.max(1, Math.floor(canvas.clientWidth));
        const h = Math.max(1, Math.floor(canvas.clientHeight));
        if (w === layoutW && h === layoutH && dots.length > 0) {
            return;
        }

        const prevByBase = new Map();
        for (const d of dots) prevByBase.set(d.baseX + ',' + d.baseY, d);

        layoutW = w;
        layoutH = h;
        if (canvas.width !== w || canvas.height !== h) {
            canvas.width = w;
            canvas.height = h;
        }

        dots = [];
        for (let x = 0; x <= canvas.width; x += spacing) {
            for (let y = 0; y <= canvas.height; y += spacing) {
                const prev = prevByBase.get(x + ',' + y);
                dots.push(prev
                    ? { x: prev.x, y: prev.y, baseX: x, baseY: y, vx: prev.vx, vy: prev.vy }
                    : { x, y, baseX: x, baseY: y, vx: 0, vy: 0 });
            }
        }
        // Tras cambiar el tamaño del canvas se pierde lo pintado: repintar entero
        paintedTop = 0;
        paintedBottom = canvas.height;
        fullRepaint = true;
    }

    // Franja del canvas pintada en el frame anterior, para borrar solo lo necesario
    let paintedTop = 0;
    let paintedBottom = 0;
    let fullRepaint = true;

    // Si el equipo no da para 60 fps de forma sostenida, el fondo se actualiza a
    // 30: la animación es lenta y apenas se nota, y deja aire al scroll y a los toques
    let lastNow = 0;
    let frameTime = 16.7;
    let measured = 0;
    let halfRate = false;
    let skip = false;

    function animate(now) {
        frame = requestAnimationFrame(animate);
        const delta = now - lastNow;
        lastNow = now;
        // Saltos grandes (pestaña en segundo plano, recién reanudado) no cuentan
        if (delta > 0 && delta < 250 && !halfRate) {
            frameTime += (delta - frameTime) * 0.05;
            if (++measured > 90 && frameTime > 22) halfRate = true;
        }
        if (halfRate && (skip = !skip)) return;

        const cw = canvas.width;
        const ch = canvas.height;

        // Franja visible del canvas (en sus coordenadas), con margen arriba y abajo
        const rect = canvas.getBoundingClientRect();
        const viewH = window.innerHeight;
        const scaleY = rect.height > 0 ? ch / rect.height : 1;
        const overscan = compositor ? MERGED_OVERSCAN : OVERSCAN;
        let top = Math.max(0, Math.floor((-rect.top - viewH * overscan) * scaleY));
        let bottom = Math.min(ch, Math.ceil((viewH * (1 + overscan) - rect.top) * scaleY));
        if (fullRepaint && compositor) {
            top = 0;
            bottom = ch;
        }
        fullRepaint = false;

        if (touch) {
            touch.pruneRipples(ripples, now);
            if (maskLayer && glow && !compositor) {
                touch.updateGlow(glow, maskLayer, canvas, ripples, now);
            }
        }
        if (!touchPrimary) {
            while (trail.length && now - trail[0].t >= TRAIL_LIFE) trail.shift();
            let ds = Math.sqrt(Math.pow(mouse.x - mouse.px, 2) + Math.pow(mouse.y - mouse.py, 2));
            if (ds > 72) ds = 72;
            mouse.speed += (ds - mouse.speed) * 0.1;
            mouse.px = mouse.x;
            mouse.py = mouse.y;
        }

        const waves = frameWaves(now, cw, ch);
        for (const bucket of buckets) bucket.length = 0;

        for (let i = 0; i < dots.length; i++) {
            const dot = dots[i];
            updateDot(dot, now);
            // Margen de 4 px: el radio del punto no debe quedar cortado en el borde
            if (dot.y < top - 4 || dot.y > bottom + 4) continue;
            const v = visibilityForDot(dot.baseX, dot.baseY, cw, ch, now, waves);
            dot.r = R_MIN + (R_MAX - R_MIN) * v;
            buckets[Math.round(v * ALPHA_LEVELS)].push(dot);
        }

        if (compositor) {
            // Canvas opaco: fondo, puntos y encima la capa de cintas mezclada
            compositor.paintBackground(ctx, cw, top, bottom);
        } else {
            const clearTop = Math.min(top, paintedTop);
            const clearBottom = Math.max(bottom, paintedBottom);
            ctx.clearRect(0, clearTop, cw, clearBottom - clearTop);
        }
        paintedTop = top;
        paintedBottom = bottom;

        ctx.fillStyle = dotColor;
        for (let level = 0; level <= ALPHA_LEVELS; level++) {
            const bucket = buckets[level];
            if (!bucket.length) continue;
            ctx.globalAlpha = A_MIN + (A_MAX - A_MIN) * (level / ALPHA_LEVELS);
            ctx.beginPath();
            for (let j = 0; j < bucket.length; j++) {
                const dot = bucket[j];
                ctx.moveTo(dot.x + dot.r, dot.y);
                ctx.arc(dot.x, dot.y, dot.r, 0, Math.PI * 2);
            }
            ctx.fill();
        }
        ctx.globalAlpha = 1;

        compositor?.paintMask(ctx, cw, top, bottom, ripples, now);
    }

    // Solo se anima mientras el fondo está en pantalla y la pestaña visible
    let frame = null;
    let onScreen = true;
    const run = () => {
        const shouldRun = onScreen && !document.hidden;
        if (shouldRun && frame === null) frame = requestAnimationFrame(animate);
        else if (!shouldRun && frame !== null) {
            cancelAnimationFrame(frame);
            frame = null;
        }
    };
    if (typeof IntersectionObserver !== 'undefined') {
        new IntersectionObserver(([entry]) => {
            onScreen = entry.isIntersecting;
            run();
        }).observe(root);
    }
    document.addEventListener('visibilitychange', run);

    function setMouseFromClient(clientX, clientY) {
        const rect = canvas.getBoundingClientRect();
        mouse.x = clientX - rect.left;
        mouse.y = clientY - rect.top;

        // Sample the pointer path into the light trail (desktop only).
        if (!touchPrimary) {
            const nowTs = performance.now();
            const lastPt = trail.length ? trail[trail.length - 1] : null;
            if (!lastPt || Math.hypot(mouse.x - lastPt.x, mouse.y - lastPt.y) >= TRAIL_MIN_STEP) {
                trail.push({
                    x: mouse.x,
                    y: mouse.y,
                    t: nowTs,
                    s: 0.5 + Math.random() * 1.0, // random size → uneven spots
                    a: 0.6 + Math.random() * 0.5, // random intensity
                    seed: Math.random() * 6.283, // random angular phase → organic edges
                });
                if (trail.length > TRAIL_MAX) trail.shift();
            }
        }

        if (!mouse.ready) {
            mouse.px = mouse.x;
            mouse.py = mouse.y;
            mouse.speed = 0;
            mouse.ready = true;
        }
    }

    function onMouseMove(e) {
        if ('pointerType' in e && e.pointerType === 'touch') return;
        // Fuera de pantalla no se pinta: no hace falta seguir el ratón
        if (frame === null) return;
        setMouseFromClient(e.clientX, e.clientY);
    }

    function applyInteractionMode(isTouch) {
        touchPrimary = isTouch;
        root.classList.toggle('ai-dots-bg-stack--pointer-hover', !isTouch);
        trail.length = 0;
        mouse.ready = false;
        mouse.x = -1000;
        mouse.y = -1000;
        mouse.speed = 0;
        if (unbindRipples) {
            unbindRipples();
            unbindRipples = null;
        }
        if (mouseMoveHandler) {
            window.removeEventListener('mousemove', mouseMoveHandler);
            mouseMoveHandler = null;
        }
        if (isTouch && touch) {
            if (glow) glow.style.opacity = '0';
            unbindRipples = touch.bindTouchRipples(canvas, ripples, null, { hitElement: touchHitEl });
        } else {
            ripples.list.length = 0;
            // No follow-glow on desktop: the lit dot trail is the light now.
            if (glow) glow.style.opacity = '0';
            mouseMoveHandler = onMouseMove;
            window.addEventListener('mousemove', mouseMoveHandler, { passive: true });
            if (touch) {
                unbindRipples = touch.bindRipples(canvas, ripples, null, {
                    hitElement: touchHitEl,
                    allowMouse: true,
                });
            }
        }
    }

    if (typeof ResizeObserver !== 'undefined') {
        new ResizeObserver(syncSize).observe(canvas.parentElement || root);
    }
    window.addEventListener('resize', syncSize);
    if (window.visualViewport) {
        window.visualViewport.addEventListener('resize', () => {
            const w = Math.max(1, Math.floor(canvas.clientWidth));
            const h = Math.max(1, Math.floor(canvas.clientHeight));
            if (w !== layoutW || h !== layoutH) {
                syncSize();
            }
        }, { passive: true });
    }

    applyInteractionMode(touchPrimary);
    if (touch) {
        touch.watchTouchMode(applyInteractionMode);
    }

    syncSize();
    run();
}

/*
 * Cintas difuminadas: con `filter: blur()` en CSS, el navegador repite el
 * desenfoque en cada frame porque el canvas de debajo cambia, aunque las cintas
 * no lo hagan (en móvil, de ~12 a ~30 fps solo por esto). Aquí se difuminan una
 * vez en un bitmap a ¼ de resolución (con 32 px de desenfoque no se aprecia) y
 * las animaciones CSS mueven ese bitmap igual que movían el SVG.
 */
const BAKE_SCALE = 0.25;
const supportsCanvasFilter = (() => {
    try {
        return typeof document.createElement('canvas').getContext('2d').filter === 'string';
    } catch {
        return false;
    }
})();

function bakeWaves(root) {
    const container = root.querySelector('.ai-dots-wave-container');
    if (!container || !supportsCanvasFilter) return null;
    const svgs = [...container.querySelectorAll('svg.ai-dots-wave-path')];
    const blur = parseFloat((getComputedStyle(container).filter.match(/blur\(([\d.]+)px\)/) || [])[1]);
    if (!svgs.length || !(blur > 0)) return null;

    // Margen para el halo del desenfoque, que en CSS se sale de la caja del SVG
    const pad = Math.ceil(blur * 3);
    const baked = svgs.map((svg) => {
        const canvas = document.createElement('canvas');
        canvas.className = svg.getAttribute('class');
        canvas.setAttribute('aria-hidden', 'true');
        Object.assign(canvas.style, {
            top: `-${pad}px`,
            left: `-${pad}px`,
            width: `calc(100% + ${pad * 2}px)`,
            height: `calc(100% + ${pad * 2}px)`,
        });
        svg.after(canvas);
        return { svg, canvas };
    });

    const paint = () => {
        const w = container.offsetWidth;
        const h = container.offsetHeight;
        if (!w || !h) return;
        const cw = Math.ceil((w + pad * 2) * BAKE_SCALE);
        const ch = Math.ceil((h + pad * 2) * BAKE_SCALE);
        const src = document.createElement('canvas');
        src.width = cw;
        src.height = ch;
        const sctx = src.getContext('2d');

        for (const { svg, canvas } of baked) {
            const [, , vbW, vbH] = svg.getAttribute('viewBox').split(/\s+/).map(Number);
            sctx.setTransform(1, 0, 0, 1, 0, 0);
            sctx.clearRect(0, 0, cw, ch);
            // El SVG recorta los trazos en su caja (preserveAspectRatio="none")
            sctx.translate(pad * BAKE_SCALE, pad * BAKE_SCALE);
            sctx.beginPath();
            sctx.rect(0, 0, w * BAKE_SCALE, h * BAKE_SCALE);
            sctx.clip();
            sctx.scale((w * BAKE_SCALE) / vbW, (h * BAKE_SCALE) / vbH);
            for (const path of svg.querySelectorAll('path')) {
                sctx.lineWidth = parseFloat(path.getAttribute('stroke-width')) || 1;
                sctx.lineCap = path.getAttribute('stroke-linecap') || 'butt';
                sctx.strokeStyle = getComputedStyle(path).stroke;
                sctx.stroke(new Path2D(path.getAttribute('d')));
            }

            canvas.width = cw;
            canvas.height = ch;
            const ctx = canvas.getContext('2d');
            ctx.filter = `blur(${blur * BAKE_SCALE}px)`;
            ctx.drawImage(src, 0, 0);
        }
        // Solo cuando los bitmaps ya están pintados se retiran el SVG y el filtro CSS
        container.style.filter = 'none';
        for (const { svg } of baked) svg.style.display = 'none';
    };

    let scheduled = null;
    const schedule = () => {
        scheduled ??= requestAnimationFrame(() => {
            scheduled = null;
            paint();
        });
    };
    paint();
    // Los colores cambian con el tema y el tamaño con la página
    new MutationObserver(schedule).observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
    if (typeof ResizeObserver !== 'undefined') new ResizeObserver(schedule).observe(container);
    return { container, canvases: baked.map(({ canvas }) => canvas) };
}

/*
 * Mezcla de las cintas dentro del canvas. En CSS la capa `.ai-dots-mask-layer`
 * (fondo + cintas + brillos de los toques) se mezcla con `mix-blend-mode` sobre
 * el canvas, que cambia en cada frame: el navegador tiene que copiar y volver a
 * mezclar toda la pantalla en cada frame, y sin una GPU potente (o sin
 * aceleración) se queda en pocos fps. Como el canvas es opaco y la capa también,
 * el resultado de la mezcla es exactamente la fórmula del modo de fusión, que
 * el canvas 2D tiene igual (`globalCompositeOperation`). Así que se pinta la
 * capa a ¼ de resolución (todo en ella está difuminado) y se mezcla en el
 * canvas; la capa CSS queda oculta y solo sirve para animar las cintas.
 */
const MASK_SCALE = 0.25;
const GLOW_SIZE = 680; // .js-ai-dots-glow: 680×680, border-radius 50 %
const GLOW_BLUR = 40;
const RIPPLE_DURATION = 2400;

function supportsBlend(mode) {
    try {
        const ctx = document.createElement('canvas').getContext('2d');
        ctx.globalCompositeOperation = mode;
        return ctx.globalCompositeOperation === mode;
    } catch {
        return false;
    }
}

/** Divide por las comas de primer nivel (no las de dentro de rgba(...)). */
function splitTopLevel(text) {
    const parts = [];
    let depth = 0;
    let start = 0;
    for (let i = 0; i < text.length; i++) {
        if (text[i] === '(') depth++;
        else if (text[i] === ')') depth--;
        else if (text[i] === ',' && depth === 0) {
            parts.push(text.slice(start, i).trim());
            start = i + 1;
        }
    }
    parts.push(text.slice(start).trim());
    return parts;
}

/** El brillo de un toque, ya difuminado, como bitmap a ¼ de resolución. */
function bakeGlowSprite(gradientCss) {
    const inner = gradientCss.slice(gradientCss.indexOf('(') + 1, gradientCss.lastIndexOf(')'));
    const stops = splitTopLevel(inner)
        // La primera parte puede ser la forma («circle at 50% 50%»), no un color
        .filter((part) => !/^(circle|ellipse|closest|farthest|at)\b/.test(part))
        .map((part) => part.match(/^(.*?)\s+(-?[\d.]+)%$/))
        .filter(Boolean)
        .map(([, color, at]) => ({ color: color.trim(), at: parseFloat(at) / 100 }));
    if (!stops.length) return null;

    // `transparent` es negro transparente: el canvas lo mezclaría oscureciendo el
    // borde, mientras que CSS no. Se usa el color vecino con alfa 0, como hace CSS
    const probe = document.createElement('canvas').getContext('2d');
    const rgb = (color) => {
        probe.fillStyle = '#000';
        probe.fillStyle = color;
        const value = probe.fillStyle;
        if (value.startsWith('#')) return [1, 3, 5].map((i) => parseInt(value.slice(i, i + 2), 16));
        return value.match(/[\d.]+/g).slice(0, 3).map(Number);
    };
    stops.forEach((stop, i) => {
        if (stop.color !== 'transparent') return;
        const neighbour = stops[i - 1] && stops[i - 1].color !== 'transparent' ? stops[i - 1] : stops[i + 1];
        stop.color = neighbour ? `rgba(${rgb(neighbour.color).join(', ')}, 0)` : 'rgba(0, 0, 0, 0)';
    });

    const pad = GLOW_BLUR * 3;
    const size = Math.ceil((GLOW_SIZE + pad * 2) * MASK_SCALE);
    const shape = document.createElement('canvas');
    shape.width = shape.height = size;
    const sctx = shape.getContext('2d');
    sctx.scale(MASK_SCALE, MASK_SCALE);
    const c = pad + GLOW_SIZE / 2;
    // `circle` sin tamaño en CSS es farthest-corner: el radio llega a la esquina
    const gradient = sctx.createRadialGradient(c, c, 0, c, c, (GLOW_SIZE / 2) * Math.SQRT2);
    stops.forEach(({ color, at }) => gradient.addColorStop(Math.min(1, Math.max(0, at)), color));
    sctx.beginPath();
    sctx.arc(c, c, GLOW_SIZE / 2, 0, Math.PI * 2);
    sctx.fillStyle = gradient;
    sctx.fill();

    const sprite = document.createElement('canvas');
    sprite.width = sprite.height = size;
    const ctx = sprite.getContext('2d');
    ctx.filter = `blur(${GLOW_BLUR * MASK_SCALE}px)`;
    ctx.drawImage(shape, 0, 0);
    return sprite;
}

/** Matriz de un elemento respecto a su offsetParent: posición + transform con su origen. */
function elementMatrix(el, style) {
    const m = new DOMMatrix().translateSelf(el.offsetLeft, el.offsetTop);
    if (style.transform && style.transform !== 'none') {
        const [ox, oy] = style.transformOrigin.split(' ').map(parseFloat);
        m.translateSelf(ox, oy).multiplySelf(new DOMMatrix(style.transform)).translateSelf(-ox, -oy);
    }
    return m;
}

function createMaskCompositor(root, canvas, maskLayer, baked) {
    if (!maskLayer || !baked || typeof DOMMatrix === 'undefined') return null;
    if (!supportsBlend('lighten') || !supportsBlend('soft-light') || !supportsBlend('multiply')) return null;

    const { container, canvases: waves } = baked;
    const off = document.createElement('canvas');
    const octx = off.getContext('2d');
    let canvasBg = '';
    let maskBg = '';
    let maskBlend = 'normal';
    let glowBlend = 'normal';
    let glowSprite = null;

    // La capa CSS ya no se pinta (ni se mezcla), pero sigue animando las cintas
    maskLayer.style.visibility = 'hidden';
    maskLayer.style.mixBlendMode = 'normal';

    const readTheme = () => {
        const style = getComputedStyle(root);
        const prop = (name, fallback) => style.getPropertyValue(name).trim() || fallback;
        canvasBg = getComputedStyle(canvas).backgroundColor;
        maskBg = prop('--ai-dots-mask-bg', canvasBg);
        maskBlend = prop('--ai-dots-mask-blend', 'normal');
        glowBlend = prop('--ai-dots-glow-blend', 'normal');
        try {
            glowSprite = bakeGlowSprite(prop('--ai-dots-glow-gradient', ''));
        } catch {
            glowSprite = null; // sin brillo en los toques, pero el fondo sigue
        }
    };

    const op = (mode) => (mode === 'normal' ? 'source-over' : mode);

    return {
        readTheme,

        paintBackground(ctx, cw, top, bottom) {
            ctx.globalCompositeOperation = 'source-over';
            ctx.fillStyle = canvasBg;
            ctx.fillRect(0, top, cw, bottom - top);
        },

        paintMask(ctx, cw, top, bottom, ripples, now) {
            const ow = Math.ceil(cw * MASK_SCALE);
            const oh = Math.ceil((bottom - top) * MASK_SCALE) + 1;
            if (ow < 1 || oh < 1) return;
            if (off.width !== ow || off.height < oh) {
                off.width = ow;
                off.height = oh;
            }

            const base = new DOMMatrix().scaleSelf(MASK_SCALE, MASK_SCALE).translateSelf(0, -top);
            octx.setTransform(1, 0, 0, 1, 0, 0);
            octx.globalCompositeOperation = 'source-over';
            octx.globalAlpha = 1;
            octx.fillStyle = maskBg;
            octx.fillRect(0, 0, ow, oh);

            // Cintas: misma posición que tendrían en CSS en este instante de su animación
            const containerMatrix = base.multiply(elementMatrix(container, getComputedStyle(container)));
            for (const wave of waves) {
                if (!wave.width) continue;
                const m = containerMatrix
                    .multiply(elementMatrix(wave, getComputedStyle(wave)))
                    .scaleSelf(wave.offsetWidth / wave.width, wave.offsetHeight / wave.height);
                octx.setTransform(m);
                octx.drawImage(wave, 0, 0);
            }

            // Brillos de los toques (antes eran divs dentro de la capa CSS)
            if (glowSprite && ripples.list.length) {
                octx.globalCompositeOperation = op(glowBlend);
                for (const ripple of ripples.list) {
                    const p = (now - ripple.t) / RIPPLE_DURATION;
                    if (p < 0 || p >= 1) continue;
                    const expand = 1 - Math.pow(1 - p, 2.15);
                    const scale = 0.22 + expand * 1.65;
                    octx.globalAlpha = (1 - p) * (1 - p) * 0.72;
                    octx.setTransform(base
                        .translate(ripple.x, ripple.y)
                        .scaleSelf(scale / MASK_SCALE, scale / MASK_SCALE)
                        .translateSelf(-glowSprite.width / 2, -glowSprite.height / 2));
                    octx.drawImage(glowSprite, 0, 0);
                }
            }

            ctx.globalCompositeOperation = op(maskBlend);
            ctx.drawImage(off, 0, 0, ow, oh, 0, top, ow / MASK_SCALE, oh / MASK_SCALE);
            ctx.globalCompositeOperation = 'source-over';
        },
    };
}

document.querySelectorAll('[data-ai-dots-root]').forEach((root) => {
    if (root.dataset.aiDotsInit === '1') return;
    root.dataset.aiDotsInit = '1';
    initOneRoot(root);
});
