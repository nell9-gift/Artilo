// public/js/welcome.js
// GSAP + ScrollTrigger · Artilo landing page

document.addEventListener('DOMContentLoaded', () => {

    // ─── GSAP Setup ─────────────────────────────────────────────
    gsap.registerPlugin(ScrollTrigger);

    // ─── NAVBAR scroll compact ──────────────────────────────────
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
        navbar.classList.toggle('scrolled', window.scrollY > 60);
    }, { passive: true });


    // ═══════════════════════════════════════════════════════════
    // HERO – animations d'entrée (comme avant, conservées)
    // ═══════════════════════════════════════════════════════════
    const DELAYS = {
        bg      : 50,
        eyebrow : 100,
        title   : 280,
        subtitle: 460,
        ctas    : 620,
        artisans: [800, 1150, 1500, 1850],
        badge   : 2300,
        stats   : [2100, 2250, 2400, 2550],
    };

    const show = (el, delay) => {
        if (!el) return;
        setTimeout(() => el.classList.add('visible'), delay);
    };

    const showAll = (els, delays) => {
        els.forEach((el, i) => show(el, delays[i] ?? delays[delays.length - 1]));
    };

    const heroBg    = document.getElementById('heroBg');
    const heroSweep = document.getElementById('heroSweep');

    setTimeout(() => {
        if (heroBg)    heroBg.classList.add('visible');
        if (heroSweep) heroSweep.classList.add('sweeping');
    }, DELAYS.bg);

    show(document.querySelector('.hero-eyebrow'),  DELAYS.eyebrow);
    show(document.querySelector('.hero-title'),    DELAYS.title);
    show(document.querySelector('.hero-subtitle'), DELAYS.subtitle);
    show(document.querySelector('.hero-ctas'),     DELAYS.ctas);

    const artisans = document.querySelectorAll('.artisan-figure');
    showAll(Array.from(artisans), DELAYS.artisans);

    show(document.querySelector('.artisan-badge'), DELAYS.badge);

    const stats = document.querySelectorAll('.stat-item');
    showAll(Array.from(stats), DELAYS.stats);

    // Compteurs héro
    const animateCounter = (el, target, suffix) => {
        let start = 0;
        const step = target / 60;
        const timer = setInterval(() => {
            start += step;
            if (start >= target) {
                start = target;
                clearInterval(timer);
            }
            const num = target >= 1000
                ? Math.round(start).toLocaleString('fr-FR')
                : Math.round(start);
            el.innerHTML = num + `<span>${suffix}</span>`;
        }, 16);
    };

    document.querySelectorAll('.stat-number[data-target]').forEach(el => {
        const t       = parseInt(el.dataset.target);
        const suffix  = el.querySelector('span')?.textContent ?? '';
        setTimeout(() => animateCounter(el, t, suffix), DELAYS.stats[3] + 200);
    });


    // ═══════════════════════════════════════════════════════════
    // OUTILS FLOTTANTS – parallaxe au scroll
    // ═══════════════════════════════════════════════════════════
    const toolItems = document.querySelectorAll('.tool-item[data-speed]');

    toolItems.forEach(tool => {
        const speed = parseFloat(tool.dataset.speed);
        gsap.to(tool, {
            y: () => speed * 200,
            rotation: speed * 15,
            ease: 'none',
            scrollTrigger: {
                trigger : '.tools-section',
                start   : 'top bottom',
                end     : 'bottom top',
                scrub   : 1.5,
            }
        });
    });

    // Contenu tools section apparition
    gsap.from('.tools-content', {
        opacity: 0,
        y: 50,
        duration: 1,
        ease: 'power3.out',
        scrollTrigger: {
            trigger : '.tools-section',
            start   : 'top 70%',
            toggleActions: 'play none none reverse',
        }
    });


    // ═══════════════════════════════════════════════════════════
    // COMMENT ÇA MARCHE – steps reveal + pipe fluid
    // ═══════════════════════════════════════════════════════════
    const howSection = document.getElementById('howSection');
    const pipeFluid  = document.getElementById('pipeFluid');

    // Fluid dans le tuyau lié au scroll
    if (howSection && pipeFluid) {
        ScrollTrigger.create({
            trigger : howSection,
            start   : 'top 80%',
            end     : 'bottom 20%',
            scrub   : true,
            onUpdate(self) {
                pipeFluid.style.height = (self.progress * 100) + '%';
            }
        });
    }

    // Steps reveal
    document.querySelectorAll('.step-content').forEach((step) => {
        ScrollTrigger.create({
            trigger : step,
            start   : 'top 80%',
            onEnter() { step.classList.add('visible'); },
        });
    });


    // ═══════════════════════════════════════════════════════════
    // NOS MÉTIERS – carousel
    // ═══════════════════════════════════════════════════════════
    const carousel = document.getElementById('tradesCarousel');
    const prevBtn  = document.getElementById('carouselPrev');
    const nextBtn  = document.getElementById('carouselNext');
    const dotsEl   = document.getElementById('carouselDots');

    if (carousel && prevBtn && nextBtn) {
        const cardWidth = () => {
            const card = carousel.querySelector('.trade-card');
            return card ? card.offsetWidth + 20 : 260; // gap 1.25rem ≈ 20px
        };

        const totalCards = carousel.querySelectorAll('.trade-card').length;
        const visibleCards = () => Math.floor(carousel.offsetWidth / cardWidth());

        let currentIndex = 0;

        // Créer les dots
        const numDots = Math.ceil(totalCards / Math.max(visibleCards(), 1));
        for (let i = 0; i < numDots; i++) {
            const dot = document.createElement('div');
            dot.className = 'carousel-dot' + (i === 0 ? ' active' : '');
            dot.addEventListener('click', () => goTo(i));
            dotsEl.appendChild(dot);
        }

        const updateDots = (idx) => {
            dotsEl.querySelectorAll('.carousel-dot').forEach((d, i) => {
                d.classList.toggle('active', i === idx);
            });
        };

        const goTo = (idx) => {
            const max = Math.ceil(totalCards / Math.max(visibleCards(), 1)) - 1;
            currentIndex = Math.max(0, Math.min(idx, max));
            const offset = currentIndex * cardWidth() * Math.max(visibleCards(), 1);
            carousel.scrollTo({ left: offset, behavior: 'smooth' });
            updateDots(currentIndex);
        };

        nextBtn.addEventListener('click', () => goTo(currentIndex + 1));
        prevBtn.addEventListener('click', () => goTo(currentIndex - 1));

        // Keyboard
        document.addEventListener('keydown', (e) => {
            const section = document.getElementById('tradesSection');
            const rect = section?.getBoundingClientRect();
            if (!rect) return;
            if (rect.top < window.innerHeight && rect.bottom > 0) {
                if (e.key === 'ArrowRight') goTo(currentIndex + 1);
                if (e.key === 'ArrowLeft')  goTo(currentIndex - 1);
            }
        });

        // Swipe tactile
        let touchStartX = 0;
        carousel.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; }, { passive: true });
        carousel.addEventListener('touchend', e => {
            const diff = touchStartX - e.changedTouches[0].clientX;
            if (Math.abs(diff) > 50) {
                diff > 0 ? goTo(currentIndex + 1) : goTo(currentIndex - 1);
            }
        });
    }

    // Trades section reveal
    gsap.from('.trades-header', {
        opacity: 0,
        y: 40,
        duration: .9,
        ease: 'power3.out',
        scrollTrigger: {
            trigger : '.trades-section',
            start   : 'top 70%',
        }
    });


    // ═══════════════════════════════════════════════════════════
    // POURQUOI ARTILO – scie + cards reveal
    // ═══════════════════════════════════════════════════════════
    const sawBlade = document.getElementById('sawBlade');

    if (sawBlade) {
        // Rotation de la scie liée au scroll
        gsap.to(sawBlade, {
            rotation: 360,
            ease: 'none',
            scrollTrigger: {
                trigger : '.why-section',
                start   : 'top bottom',
                end     : 'bottom top',
                scrub   : 2,
            }
        });
    }

    document.querySelectorAll('.why-card').forEach((card, i) => {
        ScrollTrigger.create({
            trigger : card,
            start   : 'top 80%',
            onEnter() {
                setTimeout(() => card.classList.add('visible'), i * 150);
            }
        });
    });


    // ═══════════════════════════════════════════════════════════
    // ZONE DE COUVERTURE – carte + compteurs
    // ═══════════════════════════════════════════════════════════
    const coverageMap = document.getElementById('coverageMap');

    ScrollTrigger.create({
        trigger : '.coverage-section',
        start   : 'top 65%',
        onEnter() {
            if (coverageMap) coverageMap.classList.add('visible');

            // Apparition progressive des points sur la carte
            const dots = document.querySelectorAll('.city-dot');
            dots.forEach((dot, i) => {
                setTimeout(() => dot.classList.add('visible'), i * 300);
            });

            // Compteurs couverture
            document.querySelectorAll('.cov-number[data-target]').forEach(el => {
                const t = parseInt(el.dataset.target);
                let val = 0;
                const step = t / 50;
                const interval = setInterval(() => {
                    val += step;
                    if (val >= t) { val = t; clearInterval(interval); }
                    el.textContent = t >= 1000
                        ? Math.round(val).toLocaleString('fr-FR') + '+'
                        : Math.round(val) + (t === 120 ? '' : '+');
                }, 20);
            });
        }
    });

    // City tags interactivity
    document.querySelectorAll('.city-tag').forEach(tag => {
        tag.addEventListener('click', () => {
            document.querySelectorAll('.city-tag').forEach(t => t.classList.remove('active'));
            tag.classList.add('active');

            // Highlight le point sur la carte
            const city = tag.dataset.city;
            document.querySelectorAll('.city-dot').forEach(d => {
                d.setAttribute('r', d.id === `dot-${city}` ? '10' : '6');
                d.setAttribute('fill', d.id === `dot-${city}` ? '#F59E0B' : '#5B21B6');
            });
        });
    });


    // ═══════════════════════════════════════════════════════════
    // HOW HEADER – parallax léger
    // ═══════════════════════════════════════════════════════════
    gsap.to('.how-header', {
        y: -40,
        ease: 'none',
        scrollTrigger: {
            trigger : '.how-section',
            start   : 'top bottom',
            end     : 'center center',
            scrub   : 1,
        }
    });


    // ═══════════════════════════════════════════════════════════
    // CTA FINAL – apparition
    // ═══════════════════════════════════════════════════════════
    gsap.from('.cta-content', {
        opacity: 0,
        y: 60,
        scale: .97,
        duration: 1,
        ease: 'power3.out',
        scrollTrigger: {
            trigger : '.cta-section',
            start   : 'top 70%',
        }
    });


    // ═══════════════════════════════════════════════════════════
    // FOOTER – fade in
    // ═══════════════════════════════════════════════════════════
    gsap.from('.footer-inner > *', {
        opacity: 0,
        y: 30,
        duration: .7,
        stagger: .12,
        ease: 'power2.out',
        scrollTrigger: {
            trigger : '.footer',
            start   : 'top 85%',
        }
    });


    // ═══════════════════════════════════════════════════════════
    // PARALLAX GLOBAL – fond hero au scroll
    // ═══════════════════════════════════════════════════════════
    gsap.to('.hero-bg', {
        y: 80,
        ease: 'none',
        scrollTrigger: {
            trigger : '.hero',
            start   : 'top top',
            end     : 'bottom top',
            scrub   : true,
        }
    });

    gsap.to('.hero-artisans', {
        y: 40,
        ease: 'none',
        scrollTrigger: {
            trigger : '.hero',
            start   : 'top top',
            end     : 'bottom top',
            scrub   : 1,
        }
    });

    gsap.to('.hero-text', {
        y: 20,
        ease: 'none',
        scrollTrigger: {
            trigger : '.hero',
            start   : 'top top',
            end     : 'bottom top',
            scrub   : 1.5,
        }
    });


    // ═══════════════════════════════════════════════════════════
    // Refresh ScrollTrigger après chargement images
    // ═══════════════════════════════════════════════════════════
    window.addEventListener('load', () => {
        ScrollTrigger.refresh();
    });

});