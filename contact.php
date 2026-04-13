<?php
/**
 * Contactpagina
 * Layout van contactpagina gevonden op dribble
 */

$pageTitle = 'Contact';

// Feedback status na redirect vanuit contact_action.php
$status = isset($_GET['status']) ? $_GET['status'] : null;
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<main class="flex-1">

    <!-- MINI HERO -->
    <section class="relative overflow-hidden bg-gradient-to-br from-navy-900 via-navy-800 to-navy-950">
        <!-- Decoratieve achtergrond -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute -top-10 left-1/4 w-72 h-72 bg-accent rounded-full blur-3xl"></div>
            <div class="absolute -bottom-10 right-1/4 w-80 h-80 bg-accent-light rounded-full blur-3xl"></div>
        </div>
        <div class="absolute inset-0 opacity-[0.04]"
            style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 32px 32px;">
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-24">
            <div class="max-w-2xl mx-auto text-center">

                <!-- Badge -->
                <div
                    class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm border border-white/15 rounded-full px-4 py-1.5 mb-6">
                    <svg class="w-3.5 h-3.5 text-accent-light" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span class="text-white/80 text-xs font-medium tracking-wide uppercase">Neem contact op</span>
                </div>

                <h1 class="text-4xl sm:text-6xl font-header text-white mb-4">Contact</h1>
                <p class="text-white/60 text-lg leading-relaxed">
                    Heb je een vraag, technisch probleem of wil je meer weten over Polaris?
                    Ik sta klaar om je te helpen.
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

    <!-- MAIN CONTENT -->
    <section class="py-16 sm:py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Feedbackbanner na redirect -->
            <?php if ($status === 'success'): ?>
                <div class="mb-10 max-w-3xl mx-auto" data-animate>
                    <div
                        class="flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-4">
                        <svg class="w-5 h-5 text-emerald-500 mt-0.5 flex-shrink-0" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="font-semibold text-sm">Bericht verzonden!</p>
                            <p class="text-sm text-emerald-700 mt-0.5">We hebben je bericht ontvangen en antwoorden
                                zo snel mogelijk, normaal gezien binnen 24 uur.</p>
                        </div>
                    </div>
                </div>
            <?php elseif ($status === 'error'): ?>
                <div class="mb-10 max-w-3xl mx-auto" data-animate>
                    <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 rounded-xl p-4">
                        <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="font-semibold text-sm">Er ging iets mis</p>
                            <p class="text-sm text-red-700 mt-0.5">Controleer of alle velden correct zijn ingevuld
                                en probeer het opnieuw.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-10 lg:gap-16 items-start">

                <!-- LINKS: Contact-informatie -->
                <div class="lg:col-span-2 space-y-8" data-animate>

                    <div>
                        <p class="text-accent font-header text-lg uppercase tracking-wider mb-2">Hoe kunnen we
                            helpen?</p>
                        <h2 class="text-3xl sm:text-4xl font-header text-navy-900 mb-4">Contactgegevens</h2>
                        <p class="text-slate-500 leading-relaxed">
                            Of je nu een technische vraag hebt, wil samenwerken of gewoon meer informatie zoekt; ik sta klaar. Gebruik het formulier of neem direct contact op.
                        </p>
                    </div>

                    <!-- Contact-items -->
                    <div class="space-y-3">

                        <!-- E-mail -->
                        <div
                            class="group flex items-center gap-4 p-4 bg-white rounded-xl border border-slate-200/80 shadow-sm hover:shadow-md hover:border-accent/25 transition-all duration-200">
                            <div
                                class="w-11 h-11 bg-accent/10 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-accent/20 transition-colors duration-200">
                                <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-0.5">
                                    E-mail</p>
                                <a href="mailto:info@polaris.be"
                                    class="text-navy-900 font-medium hover:text-accent transition-colors duration-200">info@polaris.be</a>
                            </div>
                        </div>

                        <!-- Telefoon -->
                        <div
                            class="group flex items-center gap-4 p-4 bg-white rounded-xl border border-slate-200/80 shadow-sm hover:shadow-md hover:border-accent/25 transition-all duration-200">
                            <div
                                class="w-11 h-11 bg-accent/10 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-accent/20 transition-colors duration-200">
                                <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-0.5">
                                    Telefoon</p>
                                <a href="tel:+3253123456"
                                    class="text-navy-900 font-medium hover:text-accent transition-colors duration-200">+32
                                    53 123 456</a>
                            </div>
                        </div>

                        <!-- Locatie -->
                        <div
                            class="group flex items-center gap-4 p-4 bg-white rounded-xl border border-slate-200/80 shadow-sm hover:shadow-md hover:border-accent/25 transition-all duration-200">
                            <div
                                class="w-11 h-11 bg-accent/10 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-accent/20 transition-colors duration-200">
                                <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-0.5">
                                    Locatie</p>
                                <p class="text-navy-900 font-medium leading-tight">Parklaan 101</p>
                                <p class="text-slate-500 text-sm">9300 Aalst, België</p>
                            </div>
                        </div>

                        <!-- Beschikbaarheid -->
                        <div
                            class="group flex items-center gap-4 p-4 bg-white rounded-xl border border-slate-200/80 shadow-sm hover:shadow-md hover:border-accent/25 transition-all duration-200">
                            <div
                                class="w-11 h-11 bg-accent/10 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-accent/20 transition-colors duration-200">
                                <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-0.5">
                                    Beschikbaarheid</p>
                                <p class="text-navy-900 font-medium leading-tight">Ma – Vr: 08:00 – 18:00</p>
                                <p class="text-slate-500 text-sm">Antwoord binnen 24 uur</p>
                            </div>
                        </div>
                    </div>

                    <!-- Live status banner -->
                    <div class="flex items-center gap-3 bg-navy-900 rounded-xl px-5 py-4">
                        <span class="relative flex size-2.5 flex-shrink-0">
                            <span
                                class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex size-2.5 rounded-full bg-emerald-500"></span>
                        </span>
                        <p class="text-white/70 text-sm">
                            <span class="text-white font-medium">Online</span>; gemiddelde reactietijd
                            <span class="text-accent-light font-medium">≤ 2 uur</span>
                        </p>
                    </div>
                </div>

                <!-- RECHTS: Formulier -->
                <div class="lg:col-span-3" data-animate data-stagger="2">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">

                        <!-- Form header -->
                        <div class="px-8 sm:px-10 pt-8 pb-6 border-b border-slate-100">
                            <h3 class="text-2xl font-header text-navy-900">Stuur een bericht</h3>
                            <p class="text-slate-500 text-sm mt-1">Velden gemarkeerd met
                                <span class="text-accent font-medium">*</span> zijn verplicht.
                            </p>
                        </div>

                        <!-- Voor nu word alles gewoon gelogged in een bestandje -->
                        <form id="contact-form" action="/actions/contact_action.php" method="POST"
                            class="px-8 sm:px-10 py-8 space-y-6" novalidate>

                            <!-- Naam + E-mail -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                                <!-- Naam -->
                                <div>
                                    <label for="name"
                                        class="block text-sm font-medium text-slate-700 mb-1.5">Volledige
                                        naam <span class="text-accent">*</span></label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="1.5"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <input type="text" id="name" name="name" required
                                            placeholder="Jan Janssen"
                                            class="w-full pl-11 pr-4 py-3 border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-all duration-200">
                                    </div>
                                </div>

                                <!-- E-mail -->
                                <div>
                                    <label for="email"
                                        class="block text-sm font-medium text-slate-700 mb-1.5">E-mailadres
                                        <span class="text-accent">*</span></label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="1.5"
                                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <input type="email" id="email" name="email" required
                                            placeholder="jan@voorbeeld.be"
                                            class="w-full pl-11 pr-4 py-3 border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-all duration-200">
                                    </div>
                                </div>
                            </div>

                            <!-- Onderwerp (select) -->
                            <div>
                                <label for="subject"
                                    class="block text-sm font-medium text-slate-700 mb-1.5">Onderwerp
                                    <span class="text-accent">*</span></label>
                                <div class="relative">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                    </div>
                                    <select id="subject" name="subject" required
                                        class="w-full pl-11 pr-10 py-3 border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-all duration-200 appearance-none bg-white">
                                        <option value="" disabled selected>Kies een onderwerp…</option>
                                        <option value="technisch">Technische ondersteuning</option>
                                        <option value="algemeen">Algemene vraag</option>
                                        <option value="samenwerking">Samenwerking / partnership</option>
                                        <option value="demo">Demo aanvragen</option>
                                        <option value="overige">Overige</option>
                                    </select>
                                    <div
                                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Bericht -->
                            <div>
                                <label for="message"
                                    class="block text-sm font-medium text-slate-700 mb-1.5">Bericht
                                    <span class="text-accent">*</span></label>
                                <textarea id="message" name="message" required rows="6"
                                    placeholder="Beschrijf je vraag of opmerking zo gedetailleerd mogelijk…"
                                    class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-all duration-200 resize-none"></textarea>
                                <p class="mt-1.5 text-xs text-slate-400">Minimum 20 tekens.</p>
                            </div>

                            <!-- Privacy akkoord -->
                            <div class="flex items-start gap-3">
                                <input type="checkbox" id="privacy" name="privacy" required
                                    class="w-4 h-4 mt-0.5 rounded border-slate-300 text-accent focus:ring-accent/40 flex-shrink-0 cursor-pointer">
                                <label for="privacy" class="text-sm text-slate-600 leading-relaxed cursor-pointer">
                                    Ik ga akkoord met het
                                    <a href="/privacy.php"
                                        class="text-accent hover:text-accent-dark underline underline-offset-2 transition-colors">privacybeleid</a>
                                    en geef toestemming voor de verwerking van mijn gegevens.
                                    <span class="text-accent">*</span>
                                </label>
                            </div>

                            <!-- Divider -->
                            <div class="h-px bg-slate-100"></div>

                            <!-- Submit -->
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <p class="text-xs text-slate-400 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    Je gegevens worden nooit gedeeld met derden.
                                </p>

                                <!-- Bericht versturen knop, icon doet een beetje raar maar het werkt -->
                                <button type="submit" id="submit-btn"
                                    class="inline-flex items-center justify-center gap-2.5 bg-accent hover:bg-accent-dark text-white px-8 py-3.5 rounded-xl text-sm font-semibold transition-all duration-300 shadow-lg shadow-accent/25 hover:shadow-accent/40 hover:-translate-y-0.5 disabled:opacity-60 disabled:cursor-not-allowed disabled:translate-y-0 disabled:shadow-none whitespace-nowrap">
                                    <span id="btn-text">Verstuur bericht</span>
                                    <svg id="btn-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.2" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                    </svg>

                                    <!-- Loading spinner -->
                                    <svg id="btn-spinner" class="w-4 h-4 hidden animate-spin" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>

</main>

<?php include 'includes/footer.php'; ?>

<script>
    // Loading/disabled state op submit
    const contactForm = document.getElementById('contact-form');
    const submitBtn = document.getElementById('submit-btn');
    const btnText = document.getElementById('btn-text');
    const btnIcon = document.getElementById('btn-icon');
    const btnSpinner = document.getElementById('btn-spinner');

    if (contactForm && submitBtn) {
        contactForm.addEventListener('submit', (e) => {
            // Laat HTML5-validatie eerst zijn werk doen
            if (!contactForm.checkValidity()) {
                contactForm.reportValidity();
                e.preventDefault();
                return;
            }

            // Toon laadstatus
            submitBtn.disabled = true;
            btnText.textContent = 'Versturen…';
            btnIcon.classList.add('hidden');
            btnSpinner.classList.remove('hidden');
        });
    }
</script>
