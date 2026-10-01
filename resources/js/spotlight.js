/* efecto visual de spotlight en tarjetas */

document.addEventListener('DOMContentLoaded', () => {
    /** Pixels outside the footer where the border spotlight still fades in. */
    const FOOTER_SPOTLIGHT_PROXIMITY_PX = 180;
    /** Same idea for el header fijo — un poco más amplio porque la barra es pequeña. */
    const HEADER_SPOTLIGHT_PROXIMITY_PX = 200;

    const footers = () => document.querySelectorAll('.js-footer-border-spotlight');
    const headerShells = () => document.querySelectorAll('.js-header-border-spotlight');

    const setFooterSpotlightOpacities = (opacityValue) => {
        footers().forEach((el) => {
            el.style.setProperty('--footer-spotlight-opacity', opacityValue);
        });
    };

    const setHeaderMouseLayerOpacities = (opacityValue) => {
        headerShells().forEach((el) => {
            el.style.setProperty('--header-mouse-layer-opacity', opacityValue);
        });
    };

    const proximityOpacity = (clientX, clientY, rect, maxPx) => {
        const dx = Math.max(rect.left - clientX, 0, clientX - rect.right);
        const dy = Math.max(rect.top - clientY, 0, clientY - rect.bottom);
        const dist = Math.hypot(dx, dy);

        return dist >= maxPx ? 0 : 1 - dist / maxPx;
    };

    /**
     * Origen del sistema de coords del spotlight del shell: esquina sup-izda del área que ocupan los ::before/::after
     * (padding edge), no la del border-box crudo — coincide con radial-gradient en el pseudo.
     */
    const getShellSpotlightOrigin = (shell) => {
        const br = shell.getBoundingClientRect();
        const cs = getComputedStyle(shell);
        const bl = parseFloat(cs.borderLeftWidth) || 0;
        const bt = parseFloat(cs.borderTopWidth) || 0;

        return { left: br.left + bl, top: br.top + bt };
    };

    /** Centro horizontal del texto del enlace (mejor que el border-box cuando el layout inline se desvía). */
    const getNavLinkContentCenterX = (link) => {
        const box = link.getBoundingClientRect();
        try {
            const range = document.createRange();
            range.selectNodeContents(link);
            const r = range.getBoundingClientRect();
            range.detach?.();
            if (r.width > 0) {
                return r.left + r.width / 2;
            }
        } catch {
            /* ignore */
        }

        return box.left + box.width / 2;
    };

    /** Centro inferior del ítem activo/highlight (opción desktop visible; si no, móvil) en coords del borde animado */
    const updateHeaderActiveNavSpotlight = () => {
        headerShells().forEach((header) => {
            const shellRect = header.getBoundingClientRect();
            const origin = getShellSpotlightOrigin(header);

            let link = header.querySelector('.srn-center .srn-link.is-active');
            if (!link || link.getBoundingClientRect().width === 0) {
                link = header.querySelector('.srn-mobile-link.is-active');
            }

            if (!link || shellRect.width <= 4 || shellRect.height <= 4) {
                header.style.setProperty('--header-nav-active-strength', '0');

                return;
            }

            const lr = link.getBoundingClientRect();
            if (lr.width === 0 || lr.height === 0) {
                header.style.setProperty('--header-nav-active-strength', '0');

                return;
            }
            const cx = getNavLinkContentCenterX(link);
            const x = cx - origin.left;
            const y = lr.bottom - origin.top - 1;

            header.style.setProperty('--nav-active-x', `${x}px`);
            header.style.setProperty('--nav-active-y', `${y}px`);
            header.style.setProperty('--header-nav-active-strength', '1');
        });
    };

    // Un evento mousemove puede llegar varias veces por frame: se procesa solo el último
    let pointer = null;
    let pointerFrame = null;

    const applyPointer = () => {
        pointerFrame = null;
        const e = pointer;
        // Primero se leen todas las posiciones y luego se escribe: alternar ambas
        // obligaría a recalcular estilos una vez por tarjeta
        const cards = [...document.querySelectorAll('.js-spotlight-card, .js-project-card')];
        const rects = cards.map((card) => card.getBoundingClientRect());
        cards.forEach((card, i) => {
            card.style.setProperty('--mouse-x', `${e.clientX - rects[i].left}px`);
            card.style.setProperty('--mouse-y', `${e.clientY - rects[i].top}px`);
        });

        footers().forEach((footer) => {
            const rect = footer.getBoundingClientRect();
            footer.style.setProperty('--mouse-x', `${e.clientX - rect.left}px`);
            footer.style.setProperty('--mouse-y', `${e.clientY - rect.top}px`);
            footer.style.setProperty(
                '--footer-spotlight-opacity',
                String(proximityOpacity(e.clientX, e.clientY, rect, FOOTER_SPOTLIGHT_PROXIMITY_PX)),
            );
        });

        headerShells().forEach((header) => {
            const rect = header.getBoundingClientRect();
            const o = getShellSpotlightOrigin(header);
            header.style.setProperty('--mouse-x', `${e.clientX - o.left}px`);
            header.style.setProperty('--mouse-y', `${e.clientY - o.top}px`);
            header.style.setProperty(
                '--header-mouse-layer-opacity',
                String(proximityOpacity(e.clientX, e.clientY, rect, HEADER_SPOTLIGHT_PROXIMITY_PX)),
            );
        });
    };

    const handleMouseMove = (e) => {
        pointer = { clientX: e.clientX, clientY: e.clientY };
        pointerFrame ??= requestAnimationFrame(applyPointer);
    };

    document.addEventListener('mousemove', handleMouseMove, { passive: true });
    document.documentElement.addEventListener('mouseleave', () => {
        setFooterSpotlightOpacities('0');
        setHeaderMouseLayerOpacities('0');
    });

    updateHeaderActiveNavSpotlight();

    let navFrame = null;
    const scheduleNavSpotlight = () => {
        navFrame ??= requestAnimationFrame(() => {
            navFrame = null;
            updateHeaderActiveNavSpotlight();
        });
    };

    ['scroll', 'resize'].forEach((evt) => {
        window.addEventListener(evt, scheduleNavSpotlight, { passive: true });
    });

    const navRoot = document.getElementById('site-redesign-nav');
    navRoot?.addEventListener(
        'click',
        scheduleNavSpotlight,
        { passive: true },
    );

    document.querySelectorAll('.js-header-border-spotlight').forEach((el) => {
        const ro = new ResizeObserver(scheduleNavSpotlight);
        ro.observe(el);
    });
});