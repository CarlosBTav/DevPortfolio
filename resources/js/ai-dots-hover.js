/**
 * Interacción de la home sobre un patrón estático: estela, desplazamiento y
 * onda click/tap. Solo se repintan pequeños tiles cercanos a los efectos.
 * El bucle termina al disiparse la interacción; no hay ondas autónomas.
 */
import './ai-dots-touch.js';

const TRAIL_LIFE = 650;
const TRAIL_MAX = 24;
const TRAIL_MIN_STEP = 4;
const TRAIL_SIGMA = 24;
const TRAIL_CUTOFF2 = (3 * TRAIL_SIGMA * 1.5 * 1.54) ** 2;
const MAX_TILES = 48;
const MAX_RIPPLES = 2;
const MAX_DISPLACEMENT = 18;
const MAX_LIFE = 3400;
const reduced = matchMedia('(prefers-reduced-motion: reduce)');
const hover = matchMedia('(hover: hover) and (pointer: fine)');
const touch = window.AiDotsTouch;

document.querySelectorAll('[data-ai-dots-hover]').forEach((root) => {
    const prototype = root.querySelector('.ai-dots-hover-canvas');
    if (!prototype?.getContext('2d') || !touch) return;
    const spacing = Number(root.dataset.spacing) || 28;
    const size = spacing * 8;
    const dpr = Math.min(window.devicePixelRatio || 1, 2);
    const pool = [];
    const tiles = new Map();
    const dots = new Map();
    const trail = [];
    const ripples = touch.createRippleState();
    const mouse = { x: -1000, y: -1000, px: -1000, py: -1000, speed: 0, ready: false };
    let frame = null;
    let rect = null;
    let onScreen = true;
    let lastInput = 0;
    let lastFrame = 0;
    let color;
    let background;

    const keyFor = (x, y) => `${x},${y}`;
    const hide = () => {
        if (frame !== null) cancelAnimationFrame(frame);
        frame = null;
        trail.length = 0;
        ripples.list.length = 0;
        dots.clear();
        tiles.clear();
        mouse.ready = false;
        mouse.speed = 0;
        mouse.x = mouse.y = -1000;
        lastFrame = 0;
        pool.forEach((tile) => {
            tile.used = false;
            tile.canvas.style.opacity = '0';
        });
    };
    const canRun = () => onScreen && !document.hidden && !reduced.matches;
    const schedule = () => {
        if (frame === null && canRun()) frame = requestAnimationFrame(animate);
    };
    const pointFromEvent = (event) => {
        rect = root.getBoundingClientRect();
        const x = event.clientX - rect.left;
        const y = event.clientY - rect.top;
        return x >= 0 && y >= 0 && x <= rect.width && y <= rect.height ? { x, y } : null;
    };

    function acquireTile(col, row) {
        let tile = pool.find((item) => !item.used);
        if (!tile) {
            const canvas = pool.length ? prototype.cloneNode() : prototype;
            if (pool.length) root.appendChild(canvas);
            canvas.width = size * dpr;
            canvas.height = size * dpr;
            canvas.style.width = `${size}px`;
            canvas.style.height = `${size}px`;
            const ctx = canvas.getContext('2d');
            ctx.scale(dpr, dpr);
            tile = { canvas, ctx };
            pool.push(tile);
        }
        tile.used = true;
        tile.left = col * size - spacing / 2;
        tile.top = row * size - spacing / 2;
        tile.canvas.style.transform = `translate3d(${tile.left}px, ${tile.top}px, 0)`;
        tile.canvas.style.opacity = '1';
        return tile;
    }

    function neededTiles() {
        const needed = new Map();
        const addArea = (x, y, radius) => {
            const left = Math.max(0, x - radius, -rect.left - spacing);
            const right = Math.min(rect.width, x + radius, innerWidth - rect.left + spacing);
            const top = Math.max(0, y - radius, -rect.top - spacing);
            const bottom = Math.min(rect.height, y + radius, innerHeight - rect.top + spacing);
            for (let row = Math.floor((top + spacing / 2) / size); row <= Math.floor((bottom + spacing / 2) / size); row++) {
                for (let col = Math.floor((left + spacing / 2) / size); col <= Math.floor((right + spacing / 2) / size); col++) {
                    if (needed.size >= MAX_TILES) return;
                    needed.set(keyFor(col, row), [col, row]);
                }
            }
        };
        // Las interacciones nuevas tienen prioridad si se alcanza el límite.
        for (let i = ripples.list.length - 1; i >= 0; i--) addArea(ripples.list[i].x, ripples.list[i].y, 360);
        for (let i = trail.length - 1; i >= 0; i--) addArea(trail[i].x, trail[i].y, Math.sqrt(TRAIL_CUTOFF2));
        for (const dot of dots.values()) {
            if (Math.hypot(dot.x - dot.baseX, dot.y - dot.baseY) > 0.12 || Math.hypot(dot.vx, dot.vy) > 0.015) {
                addArea(dot.baseX, dot.baseY, spacing);
            }
        }
        return needed;
    }

    function visibility(dot, now) {
        let boost = touch.visibilityBoost(dot.baseX, dot.baseY, ripples, now, { maxRadius: 280 });
        for (const pt of trail) {
            const dx = pt.x - dot.baseX;
            const dy = pt.y - dot.baseY;
            const d2 = dx * dx + dy * dy;
            if (d2 > TRAIL_CUTOFF2) continue;
            const angle = Math.atan2(dy, dx);
            const wobble = 1 + 0.34 * Math.sin(angle * 3 + pt.seed) + 0.2 * Math.sin(angle * 5 - pt.seed * 1.7);
            const sigma = TRAIL_SIGMA * pt.s * wobble;
            boost += Math.exp(-d2 / (sigma * sigma)) * (1 - (now - pt.t) / TRAIL_LIFE) * pt.a * 0.95;
        }
        return Math.min(1, boost);
    }

    function animate(now) {
        frame = null;
        if (!canRun() || !rect || now - lastInput > MAX_LIFE) {
            hide();
            return;
        }
        // Un paso físico a 60 Hz como en la home; no multiplicar fuerzas en
        // pantallas de 120/144 Hz ni acumular varios frames si el equipo va lento.
        if (lastFrame && now - lastFrame < 1000 / 60 - 0.5) {
            schedule();
            return;
        }
        lastFrame = lastFrame ? lastFrame + Math.max(1, Math.floor((now - lastFrame) / (1000 / 60))) * (1000 / 60) : now;
        touch.pruneRipples(ripples, now, touch.RIPPLE_DURATION);
        while (trail.length && now - trail[0].t >= TRAIL_LIFE) trail.shift();
        const distance = Math.min(72, Math.hypot(mouse.x - mouse.px, mouse.y - mouse.py));
        mouse.speed += (distance - mouse.speed) * 0.1;
        mouse.px = mouse.x;
        mouse.py = mouse.y;

        const needed = neededTiles();
        if (!needed.size) {
            hide();
            return;
        }
        for (const [key, tile] of tiles) {
            if (needed.has(key)) continue;
            tile.used = false;
            tile.canvas.style.opacity = '0';
            tiles.delete(key);
        }
        for (const [key, [col, row]] of needed) {
            if (!tiles.has(key)) tiles.set(key, acquireTile(col, row));
        }

        const activeDots = new Set();
        for (const tile of tiles.values()) {
            // Un margen de una celda permite dibujar puntos desplazados que
            // crucen el borde del tile sin duplicar su punto estático de origen.
            for (let gx = -spacing / 2; gx <= size + spacing / 2; gx += spacing) {
                for (let gy = -spacing / 2; gy <= size + spacing / 2; gy += spacing) {
                    const x = tile.left + gx;
                    const y = tile.top + gy;
                    const key = keyFor(x, y);
                    if (!dots.has(key)) dots.set(key, { x, y, baseX: x, baseY: y, vx: 0, vy: 0 });
                    activeDots.add(key);
                }
            }
        }
        for (const [key, dot] of dots) {
            if (!activeDots.has(key)) { dots.delete(key); continue; }
            touch.applyRippleForces(dot, ripples, now);
            const dx = mouse.x - dot.x;
            const dy = mouse.y - dot.y;
            const dist = Math.hypot(dx, dy);
            if (trail.length && dist > 0 && dist < 140 && mouse.speed > 0.5) {
                const power = Math.min(1.2, mouse.speed * 0.005);
                const force = (140 - dist) / 140 * power;
                dot.vx -= dx / dist * force;
                dot.vy -= dy / dist * force;
            }
            dot.x += dot.vx;
            dot.y += dot.vy;
            dot.vx *= 0.9;
            dot.vy *= 0.9;
            // Mantener la fuerza hover de la home y acelerar suavemente el
            // retorno al agotarse la estela/onda, para dormir en pocos segundos.
            const spring = trail.length || ripples.list.length ? 0.007 : 0.08;
            dot.x += (dot.baseX - dot.x) * spring;
            dot.y += (dot.baseY - dot.y) * spring;
            const offset = Math.hypot(dot.x - dot.baseX, dot.y - dot.baseY);
            if (offset > MAX_DISPLACEMENT) {
                dot.x = dot.baseX + (dot.x - dot.baseX) / offset * MAX_DISPLACEMENT;
                dot.y = dot.baseY + (dot.y - dot.baseY) / offset * MAX_DISPLACEMENT;
            }
            dot.light = visibility(dot, now);
        }
        for (const tile of tiles.values()) {
            const ctx = tile.ctx;
            ctx.globalAlpha = 1;
            ctx.fillStyle = background;
            ctx.fillRect(0, 0, size, size);
            ctx.fillStyle = color;
            // Ocho grupos de brillo, en vez de un fill separado por punto.
            const buckets = Array.from({ length: 9 }, () => []);
            for (let gx = -spacing / 2; gx <= size + spacing / 2; gx += spacing) {
                for (let gy = -spacing / 2; gy <= size + spacing / 2; gy += spacing) {
                    const dot = dots.get(keyFor(tile.left + gx, tile.top + gy));
                    buckets[Math.round(dot.light * 8)].push(dot);
                }
            }
            for (let level = 0; level <= 8; level++) {
                if (!buckets[level].length) continue;
                ctx.globalAlpha = 0.28 + level / 8 * 0.72;
                ctx.beginPath();
                for (const dot of buckets[level]) {
                    const x = dot.x - tile.left;
                    const y = dot.y - tile.top;
                    const radius = 1 + dot.light * 0.62;
                    ctx.moveTo(x + radius, y);
                    ctx.arc(x, y, radius, 0, Math.PI * 2);
                }
                ctx.fill();
            }
        }
        schedule();
    }

    const readTheme = () => {
        const styles = getComputedStyle(root);
        color = `rgb(${styles.getPropertyValue('--ai-dots-dot').trim() || '232, 118, 74'})`;
        background = styles.getPropertyValue('--ai-dots-canvas-bg').trim() || 'rgb(245, 245, 240)';
        if (tiles.size) schedule();
    };
    readTheme();
    new MutationObserver(readTheme).observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
    window.addEventListener('pointermove', (event) => {
        if (event.pointerType === 'touch' || !hover.matches || !canRun()) return;
        const point = pointFromEvent(event);
        if (!point) return;
        const now = performance.now();
        const last = trail[trail.length - 1];
        if (last && Math.hypot(point.x - last.x, point.y - last.y) < TRAIL_MIN_STEP) return;
        mouse.x = point.x;
        mouse.y = point.y;
        if (!mouse.ready) {
            mouse.px = point.x;
            mouse.py = point.y;
            mouse.ready = true;
        }
        trail.push({ ...point, t: now, s: 0.5 + Math.random(), a: 0.6 + Math.random() * 0.5, seed: Math.random() * Math.PI * 2 });
        if (trail.length > TRAIL_MAX) trail.shift();
        lastInput = now;
        schedule();
    }, { passive: true });
    document.addEventListener('pointerdown', (event) => {
        if (!canRun() || event.button !== 0 || !['mouse', 'touch', 'pen'].includes(event.pointerType)) return;
        const point = pointFromEvent(event);
        if (!point) return;
        lastInput = performance.now();
        touch.spawnRipple(ripples, point.x, point.y, lastInput);
        if (ripples.list.length > MAX_RIPPLES) ripples.list.shift();
        schedule();
    }, { passive: true });
    document.addEventListener('pointerout', (event) => {
        // Al levantar el dedo se emite pointerout: la onda debe continuar.
        if (event.pointerType !== 'touch' && !event.relatedTarget) hide();
    }, { passive: true });
    window.addEventListener('scroll', hide, { passive: true });
    window.addEventListener('resize', hide, { passive: true });
    window.addEventListener('blur', hide);
    document.addEventListener('visibilitychange', hide);
    reduced.addEventListener('change', hide);
    hover.addEventListener('change', hide);
    new IntersectionObserver(([entry]) => {
        onScreen = entry.isIntersecting;
        if (!onScreen) hide();
    }).observe(root);
});
