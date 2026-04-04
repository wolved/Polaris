<?php
/**
 * Homepage design
 * 
 * Hoofdpagina van Polaris
 * Laadt de herbruikbare header, navbar en footer modules
 * !nog niet afgewerkt
 */

// Paginatitel (wordt gebruikt in header.php)
$pageTitle = 'Home';
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<main class="flex-1">

    <!-- ==================== HERO SECTION ==================== -->
    <section id="hero" class="relative overflow-hidden bg-gradient-to-br from-navy-900 via-navy-800 to-navy-950">
        <!-- Decoratieve achtergrond-elementen -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-20 left-10 w-72 h-72 bg-accent rounded-full blur-3xl"></div>
            <div class="absolute bottom-10 right-20 w-96 h-96 bg-accent-light rounded-full blur-3xl"></div>
        </div>
        <!-- Subtiel raster-patroon -->
        <div class="absolute inset-0 opacity-5"
            style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 40px 40px;">
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 sm:py-32 lg:py-40">
            <div class="max-w-3xl mx-auto text-center">
                <!-- Logo -->
                <div class="flex justify-center mb-6">
                    <img src="/assets/images/polarisLogo.png" alt="Polaris Logo"
                        class="h-24 sm:h-28 w-auto animate-float">
                </div>

                <!-- Badge -->
                <div
                    class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm border border-white/15 rounded-full px-4 py-1.5 mb-8">
                    <span class="relative flex size-2">
                        <span
                            class="absolute inline-flex h-full w-full animate-ping rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex size-2 rounded-full bg-amber-500"></span>
                    </span>
                    <span class="text-white/80 text-xs font-medium tracking-wide uppercase">In ontwikkeling</span>
                </div>

                <!-- Titel -->
                <h1 class="text-4xl sm:text-5xl lg:text-8xl font-header text-white leading-tight mb-6">
                    Welkom bij
                    <span
                        class="block bg-gradient-to-r from-accent-light to-blue-300 bg-clip-text text-transparent">Polaris</span>
                </h1>

                <!-- Beschrijving -->
                <p class="text-lg sm:text-xl text-white/70 leading-relaxed mb-10 max-w-2xl mx-auto">
                    Het centraal interventie- en meldingsportaal voor operationeel beheer.
                    Beheer meldingen, volg interventies op en centraliseer alle operationele gegevens op één plek.
                </p>

                <!-- CTA knoppen -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="#features"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-accent hover:bg-accent-dark text-white px-8 py-3.5 rounded-xl text-xl font-header transition-all duration-300 shadow-xl shadow-accent/30 hover:shadow-accent/50 hover:-translate-y-0.5">
                        Meer informatie
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </a>
                    <a href="/login.php"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/20 text-white px-8 py-3.5 rounded-xl text-xl font-header transition-all duration-300 hover:-translate-y-0.5">
                        Aanmelden
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Onderkant wave-scheiding -->
        <div class="absolute bottom-0 left-0 right-0">
            <svg class="w-full h-12 sm:h-16 text-slate-50" viewBox="0 0 1440 54" fill="currentColor"
                preserveAspectRatio="none">
                <path
                    d="M0 22L60 16.7C120 11 240 0.7 360 0.7C480 0.7 600 11 720 16.7C840 22 960 22 1080 19.3C1200 16.7 1320 11 1380 8.3L1440 5.5V54H1380C1320 54 1200 54 1080 54C960 54 840 54 720 54C600 54 480 54 360 54C240 54 120 54 60 54H0V22Z" />
            </svg>
        </div>
    </section>

    <!-- ==================== FEATURES SECTION ==================== -->
    <section id="features" class="py-20 sm:py-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Section header -->
            <div class="text-center mb-16">
                <p class="text-accent font-header text-xl uppercase tracking-wider mb-3">Functionaliteiten</p>
                <h2 class="text-3xl sm:text-6xl font-header text-navy-900 mb-4">Alles wat je nodig hebt</h2>
                <div class="w-12 h-0.5 bg-sky-500 mx-auto mt-5 mb-5"></div>
                <div class="w-1/2 mx-auto">
                    <p class="text-slate-500 text-lg max-w-2xl mx-auto">
                        Polaris biedt een compleet overzicht van alle operationele taken, meldingen en interventies
                        alles in één centraal platform.
                    </p>
                </div>
            </div>

            <!-- Feature cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                <!-- Card 1: Meldingen beheren -->
                <div
                    class="group bg-white rounded-2xl border border-slate-200/80 p-8 shadow-sm hover:shadow-xl hover:border-accent/30 transition-all duration-300 hover:-translate-y-1">
                    <div
                        class="w-14 h-14 bg-accent/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-accent/20 transition-colors duration-300">
                        <svg class="w-7 h-7 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-navy-900 mb-3">Meldingen beheren</h3>
                    <p class="text-slate-500 leading-relaxed">
                        Ontvang, classificeer en behandel meldingen in real-time. Van urgente noodoproepen tot
                        routinecontroles, alles op één scherm.
                    </p>
                </div>

                <!-- Card 2: Interventies opvolgen -->
                <div
                    class="group bg-white rounded-2xl border border-slate-200/80 p-8 shadow-sm hover:shadow-xl hover:border-accent/30 transition-all duration-300 hover:-translate-y-1">
                    <div
                        class="w-14 h-14 bg-accent/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-accent/20 transition-colors duration-300">
                        <svg class="w-7 h-7 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-navy-900 mb-3">Interventies opvolgen</h3>
                    <p class="text-slate-500 leading-relaxed">
                        Monitor actieve interventies op een live kaart. Volg de status van elk team en elke opdracht in
                        real-time.
                    </p>
                </div>

                <!-- Card 3: Gegevens centraliseren -->
                <div
                    class="group bg-white rounded-2xl border border-slate-200/80 p-8 shadow-sm hover:shadow-xl hover:border-accent/30 transition-all duration-300 hover:-translate-y-1">
                    <div
                        class="w-14 h-14 bg-accent/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-accent/20 transition-colors duration-300">
                        <svg class="w-7 h-7 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-navy-900 mb-3">Gegevens centraliseren</h3>
                    <p class="text-slate-500 leading-relaxed">
                        Alle operationele data op één centrale plek. Dossiers, rapporten en statistieken
                        altijd up-to-date en veilig opgeslagen.
                    </p>
                </div>

            </div>
        </div>
    </section>

    <section class="py-28 bg-slate-50">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div
                class="relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-navy-950 via-navy-900 to-slate-900 p-12 sm:p-20 text-center">

                <!-- Glow effects -->
                <div class="absolute inset-0 bg-gradient-to-r from-accent/10 via-transparent to-accent/10"></div>
                <div class="absolute -top-32 -right-32 w-96 h-96 bg-accent/20 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-sky-500/20 rounded-full blur-3xl"></div>

                <!-- Content -->
                <div class="relative z-10 max-w-3xl mx-auto">

                    <p class="text-l uppercase tracking-[0.4em] text-accent-light mb-4">
                        Polaris v0.1
                    </p>

                    <h2 class="font-header text-4xl sm:text-6xl text-white mb-6 leading-tight">
                        Neem controle over elke interventie
                    </h2>

                    <p class="text-slate-300 text-lg mb-10">
                        Centraliseer meldingen, coördineer teams en reageer sneller dan ooit. Polaris geeft u realtime
                        inzicht en volledige controle over uw operaties.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 justify-center">

                        <a href="login.php"
                            class="px-10 py-4 bg-accent text-white font-semibold rounded-xl transition-all duration-300 shadow-lg hover:shadow-accent/40 hover:scale-105">
                            Ga naar portaal →
                        </a>

                        <a href="#features"
                            class="px-10 py-4 bg-white/10 text-white font-semibold rounded-xl border border-white/20 hover:bg-white/20 transition-all duration-300">
                            Bekijk functionaliteiten
                        </a>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== STATS SECTION ==================== -->
    <section class="py-24 border-y border-white/10 bg-navy-800">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-10 text-center">

                <div class="space-y-3">
                    <p class="font-header text-5xl text-accent-light">24/7</p>
                    <p class="text-xs uppercase tracking-[0.3em] text-white/40">Beschikbaarheid</p>
                </div>

                <div class="space-y-3">
                    <p class="font-header text-5xl text-accent-light">150+</p>
                    <p class="text-xs uppercase tracking-[0.3em] text-white/40">Actieve eenheden</p>
                </div>

                <div class="space-y-3">
                    <p class="font-header text-5xl text-accent-light">99.9%</p>
                    <p class="text-xs uppercase tracking-[0.3em] text-white/40">Uptime</p>
                </div>

                <div class="space-y-3">
                    <p class="font-header text-5xl text-accent-light">&lt;2s</p>
                    <p class="text-xs uppercase tracking-[0.3em] text-white/40">Responstijd</p>
                </div>

            </div>
        </div>
    </section>


</main>

<?php include 'includes/footer.php'; ?>