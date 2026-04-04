<!-- 
    Navbar Module
    Herbruikbare navigatiebalk. Wordt ingeladen na de header.
-->
<nav id="main-nav" class="bg-navy-900/95 backdrop-blur-md border-b border-white/10 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            <!-- Logo & merknaam -->
            <a href="/" class="flex items-center gap-3 group">
                <img src="/assets/images/polarisIcon.png" alt="Polaris" class="h-9 w-auto">
                <span class="text-white font-bold text-xl tracking-tight">Polaris</span>
            </a>

            <!-- Desktop navigatie -->
            <div class="hidden md:flex items-center gap-1">
                <a href="/"
                    class="nav-link text-white/80 hover:text-white hover:bg-white/10 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">Home</a>
                <a href="#features"
                    class="nav-link text-white/80 hover:text-white hover:bg-white/10 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">Features</a>
                <a href="#"
                    class="nav-link text-white/80 hover:text-white hover:bg-white/10 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">Meldingen</a>
                <a href="#"
                    class="nav-link text-white/80 hover:text-white hover:bg-white/10 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">Contact</a>
                <div class="ml-3 pl-3 border-l border-white/20">
                    <a href="#"
                        class="inline-flex items-center gap-2 bg-accent hover:bg-accent-dark text-white px-5 py-2 rounded-lg text-sm font-semibold transition-all duration-200 shadow-lg shadow-accent/25 hover:shadow-accent/40">
                        Aanmelden
                    </a>
                </div>
            </div>

            <!-- Mobiel menu-knop -->
            <button id="mobile-menu-btn" type="button"
                class="md:hidden inline-flex items-center justify-center p-2 rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition-colors duration-200"
                aria-label="Menu openen">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path id="menu-icon-open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16" />
                    <path id="menu-icon-close" class="hidden" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobiel menu (standaard verborgen) -->
    <div id="mobile-menu" class="hidden md:hidden border-t border-white/10">
        <div class="px-4 py-3 space-y-1">
            <a href="/"
                class="block text-white/80 hover:text-white hover:bg-white/10 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200">Home</a>
            <a href="#features"
                class="block text-white/80 hover:text-white hover:bg-white/10 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200">Features</a>
            <a href="#"
                class="block text-white/80 hover:text-white hover:bg-white/10 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200">Meldingen</a>
            <a href="#"
                class="block text-white/80 hover:text-white hover:bg-white/10 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200">Contact</a>
            <div class="pt-2">
                <a href="#"
                    class="block text-center bg-accent hover:bg-accent-dark text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200">Aanmelden</a>
            </div>
        </div>
    </div>
</nav>