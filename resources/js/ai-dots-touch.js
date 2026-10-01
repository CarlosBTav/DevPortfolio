/**
 * AI dots ripple interaction (tap/click).
 * Touch-primary: ripples on tap. Desktop: hover + click ripples.
 * Ripples emphasize glow/size (visibility), not dot displacement.
 */
(function (global) {
    'use strict';

    var TOUCH_MQ = '(hover: none) and (pointer: coarse)';
    var RIPPLE_DURATION = 2400;
    var RIPPLE_MAX = 6;

    function isTouchPrimary() {
        return global.matchMedia(TOUCH_MQ).matches;
    }

    function createRippleState() {
        return { list: [] };
    }

    function spawnRipple(state, x, y, now) {
        state.list.push({ x: x, y: y, t: now != null ? now : performance.now() });
        if (state.list.length > RIPPLE_MAX) {
            state.list.shift();
        }
    }

    function pruneRipples(state, now, maxAge) {
        var limit = maxAge != null ? maxAge : RIPPLE_DURATION + 120;
        var kept = [];
        for (var i = 0; i < state.list.length; i++) {
            if (now - state.list[i].t < limit) kept.push(state.list[i]);
        }
        state.list = kept;
    }

    /** Ease-out expansion: ring slows as it travels outward (water-drop feel). */
    function rippleRadius(progress, maxR) {
        return maxR * (1 - Math.pow(1 - progress, 2.15));
    }

    function rippleBoostAt(gx, gy, ripple, now, opts) {
        var duration = (opts && opts.duration) || RIPPLE_DURATION;
        var maxR = (opts && opts.maxRadius) || 280;
        var age = now - ripple.t;
        if (age >= duration) return 0;

        var p = age / duration;
        var dist = Math.hypot(gx - ripple.x, gy - ripple.y);
        var ringR = rippleRadius(p, maxR);

        // Brief impact flash at the center (first ~10% of animation)
        var flash = 0;
        if (p < 0.1) {
            var flashP = p / 0.1;
            var flashSigma = 22 + flashP * 18;
            flash = Math.exp(-(dist * dist) / (flashSigma * flashSigma)) * (1 - flashP) * 0.45;
        }

        // Main expanding ring — sharp crest, dissipates over time
        var ringWidth = 34 + p * 22;
        var ringDist = Math.abs(dist - ringR);
        var ringCrest = Math.exp(-(ringDist * ringDist) / (ringWidth * ringWidth));
        var ringDissolve = (1 - p) * (1 - p * 0.35);
        var ring = ringCrest * ringDissolve * 0.98;

        // Soft trailing wake inside the ring (subtle, no push)
        var wake = 0;
        if (dist < ringR && p > 0.05 && p < 0.85) {
            var wakeSigma = ringR * 0.55 + 20;
            wake = Math.exp(-(dist * dist) / (wakeSigma * wakeSigma)) * (1 - p) * 0.12;
        }

        return flash + ring + wake;
    }

    function visibilityBoost(gx, gy, state, now, opts) {
        var boost = 0;
        for (var i = 0; i < state.list.length; i++) {
            boost += rippleBoostAt(gx, gy, state.list[i], now, opts);
        }
        if (boost > 1) return 1;
        return boost;
    }

    /**
     * Water-drop ripple: as the expanding crest passes a dot it gets a gentle outward
     * nudge, then a slight inward pull behind the crest — so the dot bobs and settles
     * (a drop hitting water seen from above), instead of being shoved hard outward.
     */
    function applyRippleForces(dot, state, now) {
        for (var r = 0; r < state.list.length; r++) {
            var ripple = state.list[r];
            var age = now - ripple.t;
            if (age > RIPPLE_DURATION * 0.7) continue;

            var dx = dot.x - ripple.x;
            var dy = dot.y - ripple.y;
            var dist = Math.hypot(dx, dy);
            if (dist < 1) continue;

            var progress = age / RIPPLE_DURATION;
            var ringR = rippleRadius(progress, 280);

            // Signed distance to the crest: positive ahead of the wave, negative behind.
            var signed = dist - ringR;
            var crestWidth = 46;
            var falloff = Math.exp(-(signed * signed) / (crestWidth * crestWidth));
            if (falloff < 0.01) continue;

            // Derivative-of-gaussian profile: push outward on the leading edge, pull
            // inward on the trailing edge → the dot oscillates and returns to rest.
            var oscillation = -(signed / crestWidth) * falloff;
            var force = oscillation * (1 - progress) * 0.12;
            dot.vx += (dx / dist) * force;
            dot.vy += (dy / dist) * force;
        }
    }

    function updateGlow(glow, maskLayer, canvas, state, now) {
        if (!glow) return;

        var active = [];
        for (var i = 0; i < state.list.length; i++) {
            if (now - state.list[i].t < RIPPLE_DURATION) {
                active.push(state.list[i]);
            }
        }

        // Sin ondas activas ni brillos por apagar no hay nada que tocar: así no se
        // fuerza un cálculo de layout en cada frame
        if (!active.length && !glow._aiDotsLit) return;
        glow._aiDotsLit = active.length > 0;

        glow.style.opacity = '0';

        if (!maskLayer) return;
        if (!glow._aiDotsPool) glow._aiDotsPool = [];

        while (glow._aiDotsPool.length < active.length) {
            var clone = document.createElement('div');
            clone.className = 'js-ai-dots-ripple-glow ai-dots-glow';
            maskLayer.appendChild(clone);
            glow._aiDotsPool.push(clone);
        }

        var maskRect = maskLayer ? maskLayer.getBoundingClientRect() : canvas.getBoundingClientRect();
        var canvasRect = canvas.getBoundingClientRect();

        for (var g = 0; g < glow._aiDotsPool.length; g++) {
            var el = glow._aiDotsPool[g];
            var ripple = active[g];

            if (!ripple) {
                el.style.opacity = '0';
                continue;
            }

            var age = now - ripple.t;
            var p = age / RIPPLE_DURATION;
            var expand = rippleRadius(p, 1);

            el.style.left = (canvasRect.left - maskRect.left + ripple.x) + 'px';
            el.style.top = (canvasRect.top - maskRect.top + ripple.y) + 'px';
            el.style.transform = 'translate(-50%, -50%) scale(' + (0.22 + expand * 1.65) + ')';
            el.style.opacity = String((1 - p) * (1 - p) * 0.72);
        }
    }

    function isInsideRect(el, clientX, clientY) {
        var rect = el.getBoundingClientRect();
        return (
            clientX >= rect.left &&
            clientX <= rect.right &&
            clientY >= rect.top &&
            clientY <= rect.bottom
        );
    }

  function bindRipples(canvas, state, onRipple, options) {
        var hitEl = (options && options.hitElement) || canvas;
        var allowMouse = !!(options && options.allowMouse);

        function onPointerDown(e) {
            if (e.pointerType === 'mouse' && !allowMouse) return;
            if (e.pointerType !== 'mouse' && e.pointerType !== 'touch' && e.pointerType !== 'pen') return;
            if (!isInsideRect(hitEl, e.clientX, e.clientY)) return;
            var rect = canvas.getBoundingClientRect();
            var x = e.clientX - rect.left;
            var y = e.clientY - rect.top;
            spawnRipple(state, x, y);
            if (typeof onRipple === 'function') onRipple(x, y);
        }

        document.addEventListener('pointerdown', onPointerDown, { passive: true });
        return function () {
            document.removeEventListener('pointerdown', onPointerDown);
        };
    }

    function bindTouchRipples(canvas, state, onRipple, options) {
        var opts = options || {};
        opts.allowMouse = false;
        return bindRipples(canvas, state, onRipple, opts);
    }

    function watchTouchMode(callback) {
        var mq = global.matchMedia(TOUCH_MQ);
        var handler = function () {
            callback(isTouchPrimary());
        };
        if (typeof mq.addEventListener === 'function') {
            mq.addEventListener('change', handler);
        } else if (typeof mq.addListener === 'function') {
            mq.addListener(handler);
        }
        return handler;
    }

    global.AiDotsTouch = {
        isTouchPrimary: isTouchPrimary,
        createRippleState: createRippleState,
        spawnRipple: spawnRipple,
        pruneRipples: pruneRipples,
        visibilityBoost: visibilityBoost,
        applyRippleForces: applyRippleForces,
        updateGlow: updateGlow,
        bindRipples: bindRipples,
        bindTouchRipples: bindTouchRipples,
        watchTouchMode: watchTouchMode,
        RIPPLE_DURATION: RIPPLE_DURATION,
    };
})(typeof window !== 'undefined' ? window : globalThis);
