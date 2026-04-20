<?php
/**
 * Over Polaris
 * Informatiepagina over het project, de technologie en de ontwikkelaar.
 */
$pageTitle = 'Over Polaris';
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<main class="flex-1">

    <!-- HERO -->
    <section class="relative overflow-hidden bg-gradient-to-br from-navy-900 via-navy-800 to-navy-950">
        <!-- Decoratieve achtergrond -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute -top-10 left-1/4 w-72 h-72 bg-accent rounded-full blur-3xl"></div>
            <div class="absolute -bottom-10 right-1/4 w-80 h-80 bg-accent-light rounded-full blur-3xl"></div>
        </div>
        <div class="absolute inset-0 opacity-[0.04]"
            style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 32px 32px;">
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28">
            <div class="max-w-3xl mx-auto text-center">

                <!-- Badge -->
                <div
                    class="hero-anim-badge inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm border border-white/15 rounded-full px-4 py-1.5 mb-6">
                    <svg class="w-3.5 h-3.5 text-accent-light" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-white/80 text-xs font-medium tracking-wide uppercase">Over het project</span>
                </div>

                <h1 class="hero-anim-title text-4xl sm:text-5xl lg:text-8xl font-header text-white leading-tight mb-6">
                    Over
                    <span class="bg-gradient-to-r from-accent-light to-blue-300 bg-clip-text text-transparent">Polaris</span>
                </h1>

                <p class="hero-anim-desc text-lg sm:text-xl text-white/60 leading-relaxed max-w-2xl mx-auto">
                    Gebouwd met de ambitie om operationeel beheer te moderniseren en te centraliseren.
                </p>
            </div>
        </div>

        <!-- Wave scheiding -->
        <div class="absolute bottom-0 left-0 right-0">
            <svg class="w-full h-10 text-slate-50" viewBox="0 0 1440 40" fill="currentColor"
                preserveAspectRatio="none">
                <path
                    d="M0 20L48 17.3C96 14.7 192 9.3 288 8C384 6.7 480 9.3 576 12C672 14.7 768 17.3 864 18.7C960 20 1056 20 1152 18.7C1248 17.3 1344 14.7 1392 13.3L1440 12V40H1392C1344 40 1248 40 1152 40C1056 40 960 40 864 40C768 40 672 40 576 40C480 40 384 40 288 40C192 40 96 40 48 40H0V20Z" />
            </svg>
        </div>
    </section>

    <!-- OVER HET PROJECT -->
    <section class="py-20 sm:py-28 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">

                <!-- Tekst -->
                <div data-animate>
                    <p class="text-accent font-header text-xl uppercase tracking-wider mb-3">Het project</p>
                    <h2 class="text-3xl sm:text-5xl font-header text-navy-900 mb-4">Wat is Polaris?</h2>
                    <div class="w-12 h-0.5 bg-sky-500 mt-4 mb-6"></div>

                    <div class="space-y-4 text-slate-500 leading-relaxed">
                        <p>
                            Polaris is een <span class="text-navy-900 font-medium">fictief interventie & meldingsportaal</span>
                            ontworpen als een centraal platform voor operationeel beheer en coördinatie. Het systeem
                            biedt een realtime overzicht van meldingen, interventies en operationele middelen.
                        </p>
                        <p>
                            Geïnspireerd door het Nederlandse MEOS systeem, dat politieagenten ondersteunt om efficiënt en
                            mobieler te werken. Polaris is een applicatie die toont dat de Belgische hulpdiensten kunnen profiteren van digitale
                            tools om hun werk te verbeteren.
                        </p>
                        <p>
                            Het project is ontwikkeld als onderdeel van de opleiding
                            <span class="text-navy-900 font-medium">ICT Programmeren</span> aan CVO Groeipunt
                            en dient als eindproject te functioneren. Dit is dus niet een applicatie die je zomaar zou kunnen
                            gebruiken in de praktijk.
                        </p>
                    </div>
                </div>

                <!-- Highlights grid -->
                <div class="grid grid-cols-2 gap-4" data-animate data-stagger="2">
                    <div class="group bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm hover:shadow-lg hover:border-accent/25 transition-all duration-300 hover:-translate-y-1">
                        <div class="w-12 h-12 bg-accent/10 rounded-xl flex items-center justify-center mb-4 group-hover:bg-accent/20 transition-colors duration-300">
                            <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-navy-900 mb-1">Realtime meldingen</h3>
                        <p class="text-slate-500 text-sm leading-relaxed">Directe registratie en classificatie van incidenten.</p>
                    </div>
                    <div class="group bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm hover:shadow-lg hover:border-accent/25 transition-all duration-300 hover:-translate-y-1">
                        <div class="w-12 h-12 bg-accent/10 rounded-xl flex items-center justify-center mb-4 group-hover:bg-accent/20 transition-colors duration-300">
                            <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-navy-900 mb-1">Live dispatching</h3>
                        <p class="text-slate-500 text-sm leading-relaxed">Coördinatie van teams en middelen op locatie.</p>
                    </div>
                    <div class="group bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm hover:shadow-lg hover:border-accent/25 transition-all duration-300 hover:-translate-y-1">
                        <div class="w-12 h-12 bg-accent/10 rounded-xl flex items-center justify-center mb-4 group-hover:bg-accent/20 transition-colors duration-300">
                            <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-navy-900 mb-1">Operationeel inzicht</h3>
                        <p class="text-slate-500 text-sm leading-relaxed">Statistieken en rapporten in één overzicht.</p>
                    </div>
                    <div class="group bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm hover:shadow-lg hover:border-accent/25 transition-all duration-300 hover:-translate-y-1">
                        <div class="w-12 h-12 bg-accent/10 rounded-xl flex items-center justify-center mb-4 group-hover:bg-accent/20 transition-colors duration-300">
                            <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-navy-900 mb-1">Veilig & privé</h3>
                        <p class="text-slate-500 text-sm leading-relaxed">Rolgebaseerde toegang en data-encryptie.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- TECH STACK -->
    <section class="py-20 sm:py-28 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Section header -->
            <div class="text-center mb-16" data-animate>
                <p class="text-accent font-header text-xl uppercase tracking-wider mb-3">Technologie</p>
                <h2 class="text-3xl sm:text-6xl font-header text-navy-900 mb-4">Gebouwd met</h2>
                <div class="w-12 h-0.5 bg-sky-500 mx-auto mt-5 mb-5"></div>
                <div class="w-1/2 mx-auto">
                    <p class="text-slate-500 text-lg max-w-2xl mx-auto">
                        De technologieën en tools die samen de ruggengraat van Polaris vormen,
                        van frontend tot infrastructuur.
                    </p>
                </div>
            </div>

            <!-- Tech cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                <!-- PHP -->
                <div data-animate data-stagger="1"
                    class="group bg-slate-50 rounded-2xl border border-slate-200/80 p-7 hover:bg-white hover:shadow-xl hover:border-accent/25 transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-accent/10 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-accent/20 transition-colors duration-300">
                            <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-navy-900">PHP</h3>
                            <p class="text-accent text-xs font-medium uppercase tracking-wider">Backend</p>
                        </div>
                    </div>
                    <p class="text-slate-500 text-sm leading-relaxed">
                        De volledige serverside logica, van authenticatie en sessies tot data-verwerking
                        en formuliervalidatie, draait op PHP.
                    </p>
                </div>

                <!-- MySQL -->
                <div data-animate data-stagger="2"
                    class="group bg-slate-50 rounded-2xl border border-slate-200/80 p-7 hover:bg-white hover:shadow-xl hover:border-accent/25 transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-accent/10 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-accent/20 transition-colors duration-300">
                            <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-navy-900">MySQL</h3>
                            <p class="text-accent text-xs font-medium uppercase tracking-wider">Database</p>
                        </div>
                    </div>
                    <p class="text-slate-500 text-sm leading-relaxed">
                        Alle meldingen, gebruikers, interventies en operationele gegevens worden opgeslagen
                        en bevraagd via een relationele MySQL-database.
                    </p>
                </div>

                <!-- TailwindCSS -->
                <div data-animate data-stagger="3"
                    class="group bg-slate-50 rounded-2xl border border-slate-200/80 p-7 hover:bg-white hover:shadow-xl hover:border-accent/25 transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-accent/10 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-accent/20 transition-colors duration-300">
                            <svg class="w-6 h-6 text-accent" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.001 4.8c-3.2 0-5.2 1.6-6 4.8 1.2-1.6 2.6-2.2 4.2-1.8.913.228 1.565.89 2.288 1.624C13.666 10.618 15.027 12 18.001 12c3.2 0 5.2-1.6 6-4.8-1.2 1.6-2.6 2.2-4.2 1.8-.913-.228-1.565-.89-2.288-1.624C16.337 6.182 14.976 4.8 12.001 4.8zm-6 7.2c-3.2 0-5.2 1.6-6 4.8 1.2-1.6 2.6-2.2 4.2-1.8.913.228 1.565.89 2.288 1.624 1.177 1.194 2.538 2.576 5.512 2.576 3.2 0 5.2-1.6 6-4.8-1.2 1.6-2.6 2.2-4.2 1.8-.913-.228-1.565-.89-2.288-1.624C10.337 13.382 8.976 12 6.001 12z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-navy-900">Tailwind CSS</h3>
                            <p class="text-accent text-xs font-medium uppercase tracking-wider">Styling</p>
                        </div>
                    </div>
                    <p class="text-slate-500 text-sm leading-relaxed">
                        Het gehele visuele ontwerp is gebouwd met TailwindCSS,
                        en een kleurenpalet + typografie die specifiek is voor het Polaris-thema.
                    </p>
                </div>

                <!-- JavaScript -->
                <div data-animate data-stagger="4"
                    class="group bg-slate-50 rounded-2xl border border-slate-200/80 p-7 hover:bg-white hover:shadow-xl hover:border-accent/25 transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-accent/10 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-accent/20 transition-colors duration-300">
                            <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-navy-900">JavaScript</h3>
                            <p class="text-accent text-xs font-medium uppercase tracking-wider">Interactiviteit</p>
                        </div>
                    </div>
                    <p class="text-slate-500 text-sm leading-relaxed">
                        Ik heb gekozen om geen React of dergelijke te gebruiken, enkel vanilla JS.
                        Puur om het ook simpel te houden.
                    </p>
                </div>

                <!-- Docker -->
                <div data-animate data-stagger="5"
                    class="group bg-slate-50 rounded-2xl border border-slate-200/80 p-7 hover:bg-white hover:shadow-xl hover:border-accent/25 transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-accent/10 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-accent/20 transition-colors duration-300">
                            <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-navy-900">OrbStack & Proxmox</h3>
                            <p class="text-accent text-xs font-medium uppercase tracking-wider">Infrastructuur</p>
                        </div>
                    </div>
                    <p class="text-slate-500 text-sm leading-relaxed">
                        Alle final versies van de code worden opgeslagen in een Proxmox VE omgeving,
                        en lokaal word alles gehost in een OrbStack omgeving.
                    </p>
                </div>

                <!-- Git -->
                <div data-animate data-stagger="6"
                    class="group bg-slate-50 rounded-2xl border border-slate-200/80 p-7 hover:bg-white hover:shadow-xl hover:border-accent/25 transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-accent/10 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-accent/20 transition-colors duration-300">
                            <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-navy-900">Git & GitHub</h3>
                            <p class="text-accent text-xs font-medium uppercase tracking-wider">Versiebeheer</p>
                        </div>
                    </div>
                    <p class="text-slate-500 text-sm leading-relaxed">
                        Alle code wordt openbaar op GitHub gehouden, en wordt gepubliceerd naar de live versie van de website.
                    </p>
                </div>

            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-28 bg-slate-50">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div
                class="relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-navy-950 via-navy-900 to-slate-900 p-12 sm:p-20 text-center">

                <!-- Glow effects -->
                <div class="absolute inset-0 bg-gradient-to-r from-accent/10 via-transparent to-accent/10"></div>
                <div class="absolute -top-32 -right-32 w-96 h-96 bg-accent/20 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-sky-500/20 rounded-full blur-3xl"></div>

                <!-- Content -->
                <div class="relative z-10 max-w-3xl mx-auto" data-animate>

                    <p class="text-l uppercase tracking-[0.4em] text-accent-light mb-4">
                        Meer ontdekken?
                    </p>

                    <h2 class="font-header text-4xl sm:text-6xl text-white mb-6 leading-tight">
                        Bekijk Polaris in actie
                    </h2>

                    <p class="text-slate-300 text-lg mb-10">
                        Benieuwd naar het resultaat? Bekijk het dashboard, ontdek de features
                        of neem direct contact op voor vragen of samenwerking.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="/login.php"
                            class="px-10 py-4 bg-accent text-white font-semibold rounded-xl transition-all duration-300 shadow-lg hover:shadow-accent/40 hover:scale-105">
                            Ga naar portaal →
                        </a>
                        <a href="/contact.php"
                            class="px-10 py-4 bg-white/10 text-white font-semibold rounded-xl border border-white/20 hover:bg-white/20 transition-all duration-300">
                            Neem contact op
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<?php include 'includes/footer.php'; ?>
