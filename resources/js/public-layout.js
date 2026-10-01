// 1. Scroll to Top
const scrollBtn = document.getElementById('scrollToTopBtn');
const footer    = document.getElementById('main-footer');
let _fabRaf = null;

function syncScrollToTopFab() {
    if (!scrollBtn) return;
    if (_fabRaf) return;
    _fabRaf = requestAnimationFrame(() => {
        _fabRaf = null;

        // Visibilidad
        if (window.scrollY > 300) {
            scrollBtn.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-10');
            scrollBtn.classList.add('opacity-100', 'translate-y-0');
        } else {
            scrollBtn.classList.add('opacity-0', 'pointer-events-none', 'translate-y-10');
            scrollBtn.classList.remove('opacity-100', 'translate-y-0');
        }

        // Posición: sube por encima del footer si este ya es visible
        if (footer) {
            const gap        = 32; // bottom-8
            const footerTop  = footer.getBoundingClientRect().top;
            const vh         = window.innerHeight;
            scrollBtn.style.bottom = (footerTop < vh
                ? gap + (vh - footerTop)
                : gap) + 'px';
        }
    });
}

if (scrollBtn) {
    window.addEventListener('scroll', syncScrollToTopFab, { passive: true });
    window.addEventListener('resize', syncScrollToTopFab);
    syncScrollToTopFab();
}

// 2. Seguimiento del ratón (Tarjetas + textos con efectos)
document.addEventListener('DOMContentLoaded', () => {
    // Las tarjetas (.js-spotlight-card, .js-project-card) las sigue spotlight.js
    const nameSpotlights = document.querySelectorAll('.js-footer-name-spotlight');
    const designSpotlights = document.querySelectorAll('.js-footer-design-spotlight');

    document.addEventListener('mousemove', (e) => {
        nameSpotlights.forEach((nameSpotlight) => {
            const rect = nameSpotlight.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            if (x >= 0 && x <= rect.width && y >= 0 && y <= rect.height) {
                nameSpotlight.style.setProperty('--mouse-x', `${x}px`);
                nameSpotlight.style.setProperty('--mouse-y', `${y}px`);
            }
        });

        designSpotlights.forEach((designSpotlight) => {
            const rect = designSpotlight.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            designSpotlight.style.setProperty('--mouse-x', `${x}px`);
            designSpotlight.style.setProperty('--mouse-y', `${y}px`);

            // Proximity fade-in before entering text area (smooth approach)
            const dx = x < 0 ? -x : (x > rect.width ? x - rect.width : 0);
            const dy = y < 0 ? -y : (y > rect.height ? y - rect.height : 0);
            const distance = Math.hypot(dx, dy);
            const sigma = 95;
            const proximity = Math.exp(-(distance * distance) / (2 * sigma * sigma));
            designSpotlight.style.setProperty('--design-proximity', proximity.toFixed(3));
        });
    });

    nameSpotlights.forEach((nameSpotlight) => {
        const sweep = nameSpotlight.querySelector('.footer-name-vfx-sweep');
        if (!sweep) return;
        nameSpotlight.addEventListener('mouseleave', () => {
            function resetPosition(e) {
                if (e.propertyName === 'opacity') {
                    sweep.style.backgroundPosition = '0 0';
                    sweep.removeEventListener('transitionend', resetPosition);
                }
            }
            sweep.addEventListener('transitionend', resetPosition);
        });
    });

    // Hero desktop wave: letters repel away from mouse (Y-aware)
    const heroWaveNodes = document.querySelectorAll('.js-hero-wave');
    const isDesktopWave = window.matchMedia('(min-width: 1024px)');

    function bindHeroWave(node) {
        const chars = Array.from(node.querySelectorAll('.hero-wave-char'));
        if (!chars.length) return;

        const sigmaX = 40;        // horizontal spread
        const sigmaY = 105;       // vertical spread
        const maxShiftX = 6;      // max horizontal attraction
        const maxShiftY = 11;     // max vertical attraction
        const lerp = 0.16;        // smoother easing
        const currentShifts = chars.map(() => ({ x: 0, y: 0 }));
        const targetShifts = chars.map(() => ({ x: 0, y: 0 }));
        let mouseX = null;
        let mouseY = null;

        const updateTargets = () => {
            if (!isDesktopWave.matches || mouseX === null) {
                for (let i = 0; i < targetShifts.length; i++) {
                    targetShifts[i].x = 0;
                    targetShifts[i].y = 0;
                }
                return;
            }

            chars.forEach((ch, i) => {
                const rect = ch.getBoundingClientRect();
                const centerX = rect.left + rect.width / 2;
                const centerY = rect.top + rect.height / 2;
                const dx = mouseX - centerX;
                const dy = mouseY - centerY;

                // Smooth radial influence around cursor
                const influence = Math.exp(
                    -((dx * dx) / (2 * sigmaX * sigmaX) + (dy * dy) / (2 * sigmaY * sigmaY))
                );

                // Continuous attraction on both axes (no sign flip jump)
                targetShifts[i].x = maxShiftX * Math.tanh(dx / 75) * influence;
                targetShifts[i].y = maxShiftY * Math.tanh(dy / 70) * influence;
            });
        };

        const animate = () => {
            updateTargets();
            chars.forEach((ch, i) => {
                currentShifts[i].x += (targetShifts[i].x - currentShifts[i].x) * lerp;
                currentShifts[i].y += (targetShifts[i].y - currentShifts[i].y) * lerp;
                if (Math.abs(currentShifts[i].x) < 0.02) currentShifts[i].x = 0;
                if (Math.abs(currentShifts[i].y) < 0.02) currentShifts[i].y = 0;
                ch.style.setProperty('--hero-shift-x', currentShifts[i].x.toFixed(2) + 'px');
                ch.style.setProperty('--hero-shift-y', currentShifts[i].y.toFixed(2) + 'px');
            });
            requestAnimationFrame(animate);
        };

        window.addEventListener('mousemove', (e) => {
            mouseX = e.clientX;
            mouseY = e.clientY;
        });
        window.addEventListener('mouseleave', () => {
            mouseX = null;
            mouseY = null;
        });

        requestAnimationFrame(animate);
    }

    heroWaveNodes.forEach(bindHeroWave);

    // Hero foto perfil (PC): spotlight en el ratón solo visible en el aro; se apaga hacia el centro; clic = barrido conic
    const profileRings = document.querySelectorAll('.js-profile-ring');
    const canHoverProfileRing = window.matchMedia('(hover: hover), (any-hover: hover)');
    let profileRingRaf = 0;
    let profileRingMx = 0;
    let profileRingMy = 0;

    function profileRingSweepStartFromPointer(ring, clientX, clientY) {
        const rect = ring.getBoundingClientRect();
        const cx = rect.left + rect.width / 2;
        const cy = rect.top + rect.height / 2;
        const dx = clientX - cx;
        const dy = clientY - cy;
        const jsDeg = Math.atan2(dy, dx) * (180 / Math.PI);
        const mouseAngleCss = ((jsDeg + 90) % 360 + 360) % 360;
        const wedgeCenterLocal = 336;
        const deg = ((mouseAngleCss - wedgeCenterLocal) % 360 + 360) % 360;
        return deg.toFixed(2) + 'deg';
    }

    function updateProfileRingSpotlight(ring, clientX, clientY) {
        if (ring.classList.contains('profile-ring--sweep')) return;

        const rect = ring.getBoundingClientRect();
        const cx = rect.left + rect.width / 2;
        const cy = rect.top + rect.height / 2;
        const dx = clientX - cx;
        const dy = clientY - cy;
        const dist = Math.hypot(dx, dy);
        const radius = Math.min(rect.width, rect.height) / 2;
        const rimRadius = radius - 1;

        if (ring.classList.contains('profile-ring--rim-holdoff')) {
            ring.style.setProperty('--profile-ring-proximity', '0');
            return;
        }

        // Centro del spotlight = posición real del ratón
        const spotX = clientX - rect.left;
        const spotY = clientY - rect.top;

        const distToRim = Math.abs(dist - rimRadius);
        const sigmaRim = Math.max(28, radius * 0.34);
        const proximity = Math.exp(-(distToRim * distToRim) / (2 * sigmaRim * sigmaRim));
        const insideFactor = dist < rimRadius ? (1 - (dist / rimRadius)) : 0;
        const centerBoost = Math.pow(insideFactor, 0.62);
        const diameter = (radius * 2) + 4;
        const spotSize = 72 + (82 * proximity) + ((diameter - 56) * centerBoost);
        const edgeIntensity = Math.pow(proximity, 1.6) * 0.5;
        const centerIntensity = centerBoost * 0.44;
        const visibleProximity = Math.min(1, Math.max(edgeIntensity, centerIntensity));

        if (visibleProximity < 0.002) {
            ring.style.setProperty('--profile-ring-proximity', '0');
            ring.style.setProperty('--profile-ring-spot-size', '58px');
            return;
        }

        ring.style.setProperty('--profile-ring-spot-x', spotX.toFixed(2) + 'px');
        ring.style.setProperty('--profile-ring-spot-y', spotY.toFixed(2) + 'px');
        ring.style.setProperty('--profile-ring-proximity', visibleProximity.toFixed(4));
        ring.style.setProperty('--profile-ring-spot-size', spotSize.toFixed(2) + 'px');
    }

    function flushProfileRingsFromPointer() {
        profileRingRaf = 0;
        profileRings.forEach((ring) => {
            updateProfileRingSpotlight(ring, profileRingMx, profileRingMy);
        });
    }

    document.addEventListener('mousemove', (e) => {
        profileRingMx = e.clientX;
        profileRingMy = e.clientY;
        if (!profileRingRaf) {
            profileRingRaf = requestAnimationFrame(flushProfileRingsFromPointer);
        }
    });

    window.addEventListener('blur', () => {
        profileRings.forEach((ring) => {
            ring.style.setProperty('--profile-ring-proximity', '0');
            ring.style.setProperty('--profile-ring-spot-size', '58px');
            ring.style.removeProperty('--profile-ring-spot-x');
            ring.style.removeProperty('--profile-ring-spot-y');
        });
    });

    profileRings.forEach((ring) => {
        ring.addEventListener('click', () => {
            ring.style.setProperty(
                '--profile-ring-sweep-start',
                profileRingSweepStartFromPointer(ring, profileRingMx, profileRingMy)
            );
            ring.classList.remove('profile-ring--sweep');
            void ring.offsetWidth;
            ring.classList.add('profile-ring--sweep');
        });

        ring.addEventListener('animationend', (e) => {
            if (e.animationName !== 'profile-ring-sweep') return;
            ring.classList.remove('profile-ring--sweep');
            ring.style.removeProperty('--profile-ring-sweep-start');
            if (canHoverProfileRing.matches) {
                ring.classList.add('profile-ring--rim-holdoff');
            }
        });

        ring.addEventListener('mouseleave', () => {
            ring.classList.remove('profile-ring--rim-holdoff');
        });
    });
});

// 3. Barra de Progreso Vertical
window.addEventListener('scroll', () => {
    const progressBar = document.getElementById('scroll-progress-bar');
    const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
    const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    progressBar.style.height = (winScroll / height) * 100 + "%";
});
