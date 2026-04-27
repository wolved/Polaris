/**
 * Hoofdscript
 * 
 * Bevat alle client-side functionaliteit:
 * - Mobiel menu toggle (publieke navbar)
 * - Sidebar toggle (dashboard)
 * - Scroll-gebaseerde animaties (generiek data-animate systeem)
 * - Navbar scroll-effect
 * - Verticale scroll-navigatie dots (homepage)
 * - Stats teller-animatie
 * - Smooth scroll voor ankerlinks
 * - Wachtwoord toggle (login)
 * - Klok (dashboard topbar)
 * word nog aangevuldd
 */

document.addEventListener('DOMContentLoaded', () => {

    // Mobiel menu toggle

    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuIconOpen = document.getElementById('menu-icon-open');
    const menuIconClose = document.getElementById('menu-icon-close');

    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', () => {
            const isOpen = !mobileMenu.classList.contains('hidden');

            // Toggle menu zichtbaarheid
            mobileMenu.classList.toggle('hidden');

            // Toggle iconen (hamburger ↔ kruis)
            if (menuIconOpen && menuIconClose) {
                menuIconOpen.classList.toggle('hidden');
                menuIconClose.classList.toggle('hidden');
            }

            // ARIA-attribuut updaten voor toegankelijkheid
            mobileMenuBtn.setAttribute('aria-expanded', !isOpen);
            mobileMenuBtn.setAttribute('aria-label', isOpen ? 'Menu openen' : 'Menu sluiten');
        });

        // Sluit mobiel menu bij klik op een link
        mobileMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
                if (menuIconOpen && menuIconClose) {
                    menuIconOpen.classList.remove('hidden');
                    menuIconClose.classList.add('hidden');
                }
                mobileMenuBtn.setAttribute('aria-expanded', 'false');
                mobileMenuBtn.setAttribute('aria-label', 'Menu openen');
            });
        });
    }

    // Dashboard sidebar, Toggle op mobiel

    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebarOverlay = document.getElementById('sidebar-overlay');

    if (sidebar && sidebarToggle) {
        // Open sidebar
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.add('sidebar-open');
            if (sidebarOverlay) {
                sidebarOverlay.classList.remove('hidden');
            }
            document.body.style.overflow = 'hidden';
        });

        // Sluit sidebar via overlay
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', closeSidebar);
        }

        // Sluit sidebar bij klik op een link (mobiel)
        sidebar.querySelectorAll('.sidebar-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 1024) {
                    closeSidebar();
                }
            });
        });

        // Sluit sidebar bij resize naar desktop
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                closeSidebar();
            }
        });
    }

    function closeSidebar() {
        if (sidebar) sidebar.classList.remove('sidebar-open');
        if (sidebarOverlay) sidebarOverlay.classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Navbar scroll-effect

    const nav = document.getElementById('main-nav');

    if (nav) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 10) {
                nav.classList.add('shadow-lg');
            } else {
                nav.classList.remove('shadow-lg');
            }
        }, { passive: true });
    }

    // Generiek scroll animatie systeem, gevonden op github.

    const scrollObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const animClass = el.dataset.animateClass || 'animate-fade-in-up';
                el.classList.add(animClass);
                scrollObserver.unobserve(el);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px',
    });

    document.querySelectorAll('[data-animate]').forEach(el => {
        el.style.opacity = '0';
        const stagger = el.dataset.stagger;
        if (stagger) el.classList.add(`stagger-${stagger}`);
        scrollObserver.observe(el);
    });

    // Stats teller-animatie, telt ze op met een mooie animatie

    const statCounters = document.querySelectorAll('[data-count-to]');

    if (statCounters.length > 0) {
        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const target = parseInt(el.dataset.countTo, 10);
                    const suffix = el.dataset.countSuffix || '';
                    const duration = 1600;
                    const startTime = performance.now();

                    const tick = (now) => {
                        const progress = Math.min((now - startTime) / duration, 1);
                        const eased = 1 - Math.pow(1 - progress, 4);
                        el.textContent = Math.round(eased * target) + suffix;
                        if (progress < 1) requestAnimationFrame(tick);
                    };

                    requestAnimationFrame(tick);
                    counterObserver.unobserve(el);
                }
            });
        }, { threshold: 0.6 });

        statCounters.forEach(el => counterObserver.observe(el));
    }

    // Smooth scroll voor ankerlinks
    // TODO: Later ook alles uit animeren als je naar boven scrollt

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', (e) => {
            const targetId = anchor.getAttribute('href');
            if (targetId === '#') return;

            const targetEl = document.querySelector(targetId);
            if (targetEl) {
                e.preventDefault();
                targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // Verticale scroll-navigatie dots
    // Enkel actief op pagina's met #scroll-nav

    const scrollNav = document.getElementById('scroll-nav');

    if (scrollNav) {

        // Secties die gevolgd worden (id weergavenaam in tooltip)
        const NAV_SECTIONS = [
            { id: 'hero',         label: 'Start'        },
            { id: 'features',     label: 'Functies'     },
            { id: 'how-it-works', label: 'Werkwijze'    },
            { id: 'preview',      label: 'Preview'    },
            { id: 'security',     label: 'Beveiliging'  },
            { id: 'cta',          label: 'Aan de slag'  },
            { id: 'stats',        label: 'Statistieken' },
        ];

        // Bouw elke dot als een <button> aan en voeg toe aan de nav
        NAV_SECTIONS.forEach(({ id, label }) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.dataset.target = id;
            btn.setAttribute('aria-label', `Navigeer naar sectie: ${label}`);

            btn.className = 'scroll-dot group relative flex items-center justify-end w-9 h-9';

            btn.innerHTML = `
                <span class="dot-tooltip absolute right-full mr-3 px-2.5 py-1 rounded-md
                             bg-navy-900/90 backdrop-blur-sm text-white text-xs font-medium
                             whitespace-nowrap opacity-0 translate-x-1
                             group-hover:opacity-100 group-hover:translate-x-0">
                    ${label}
                </span>
                <span class="dot-inner block w-2.5 h-2.5 rounded-full
                             bg-slate-400/50 ring-1 ring-slate-400/20
                             group-hover:bg-accent group-hover:scale-125">
                </span>
            `;

            btn.addEventListener('click', () => {
                document.getElementById(id)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });

            scrollNav.appendChild(btn);
        });

        // Markeer de dot die overeenkomt met de actieve sectie
        const setActiveDot = (activeId) => {
            scrollNav.querySelectorAll('.scroll-dot').forEach(dot => {
                dot.classList.toggle('is-active', dot.dataset.target === activeId);
            });
        };

        // Verzamel de sectie-elementen in de volgorde van de pagina
        const sectionEls = NAV_SECTIONS
            .map(({ id }) => document.getElementById(id))
            .filter(Boolean);

        // Bepaal de actieve sectie: de sectie waarvan de bovenkant het
        // dichtst bij 40% van de viewport ligt (maar er al voorbij is)
        // Verberg de volledige nav zolang de hero actief is
        const updateActiveSection = () => {
            const triggerY = window.scrollY + window.innerHeight * 0.4;
            let activeId = sectionEls[0]?.id;

            for (const section of sectionEls) {
                if (section.offsetTop <= triggerY) {
                    activeId = section.id;
                }
            }

            if (activeId) setActiveDot(activeId);

            // Toon de nav pas zodra de hero buiten beeld is
            const heroVisible = activeId === 'hero';
            scrollNav.classList.toggle('opacity-0', heroVisible);
            scrollNav.classList.toggle('pointer-events-none', heroVisible);
        };

        window.addEventListener('scroll', updateActiveSection, { passive: true });
        updateActiveSection(); // Direct initialiseren bij laden
    }

    // Wachtwoord tonen/verbergen, VOOR LOGIN PAGINA

    const togglePassword = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');
    const eyeOpen = document.getElementById('eye-open');
    const eyeClosed = document.getElementById('eye-closed');

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';

            if (eyeOpen && eyeClosed) {
                eyeOpen.classList.toggle('hidden');
                eyeClosed.classList.toggle('hidden');
            }
        });
    }

    // Live klok in de topbar, handig voor de hulpdiensten

    const clockEl = document.getElementById('current-time');

    if (clockEl) {
        function updateClock() {
            const now = new Date();
            clockEl.textContent = now.toLocaleTimeString('nl-BE', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
            });
        }

        updateClock();
        setInterval(updateClock, 1000);
    }

});
