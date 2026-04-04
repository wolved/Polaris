/**
 * Hoofdscript
 * 
 * Bevat alle client-side functionaliteit:
 * - Mobiel menu toggle (publieke navbar)
 * - Sidebar toggle (dashboard)
 * - Scroll-gebaseerde animaties
 * - Navbar scroll-effect
 * - Wachtwoord toggle (login)
 * - Klok (dashboard topbar)
 * !!!word nog aangevuld
 */

document.addEventListener('DOMContentLoaded', () => {

    // ============================================
    // Mobiel menu toggle
    // ============================================

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

    // ============================================
    // Dashboard sidebar, Toggle op mobiel
    // ============================================

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

    // ============================================
    // Navbar scroll-effect
    // ============================================

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

    // ============================================
    // Scroll-animaties
    // ============================================

    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px',
    };

    const animateOnScroll = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in-up');
                animateOnScroll.unobserve(entry.target); // Animeer slechts eenmaal
            }
        });
    }, observerOptions);

    // Observeer alle feature cards op de homepage
    document.querySelectorAll('#features .group').forEach((card, index) => {
        card.style.opacity = '0';
        card.classList.add(`stagger-${index + 1}`);
        animateOnScroll.observe(card);
    });

    // ============================================
    // Smooth scroll voor ankerlinks
    // ============================================

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

    // ============================================
    // Wachtwoord tonen/verbergen
    // ============================================

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

    // ============================================
    // Live klok in de topbar
    // Handig voor de hulpdiensten
    // ============================================

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
