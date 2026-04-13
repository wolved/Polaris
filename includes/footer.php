<!-- 
    Footer Module
    Sluit ook <body> en <html> af.
-->
<footer class="bg-navy-950 text-white/60 mt-auto">
    <!-- Bovenste footer: links & info -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">

            <!-- Kolom 1: Over Polaris -->
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <img src="/assets/images/polarisLogo.png" alt="Polaris" class="h-9 w-auto">
                    <span class="text-white font-bold text-lg">Polaris</span>
                </div>
                <p class="text-sm leading-relaxed max-w-xs">
                    Hobbyproject van Lars V. student ICT Programmeren, bij CVO Groeipunt.
                    <br><br>
                    Polaris is een fictief interventie en meldingsportaal voor efficiënt operationeel beheer en
                    coördinatie.
                </p>
            </div>

            <!-- Kolom 2: Snelle links -->
            <div>
                <h3 class="text-white font-semibold text-sm uppercase tracking-wider ml-5 mb-4">Navigatie</h3>
                <ul class="space-y-2 ml-5">
                    <li><a href="/" class="text-sm hover:text-white transition-colors duration-200">Home</a></li>
                    <li><a href="#features" class="text-sm hover:text-white transition-colors duration-200">Features</a>
                    </li>
                    <li><a href="#" class="text-sm hover:text-white transition-colors duration-200">Meldingen</a></li>
                    <li><a href="#" class="text-sm hover:text-white transition-colors duration-200">Contact</a></li>
                </ul>
            </div>

            <!-- Kolom 3: Frequently Asked Questions -->
            <div>
                <h3 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">FAQ</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-sm hover:text-white transition-colors duration-200">Wat is Polaris?</a>
                    </li>
                    <li><a href="#" class="text-sm hover:text-white transition-colors duration-200">Hoe werkt het?</a>
                    </li>
                    <li><a href="#" class="text-sm hover:text-white transition-colors duration-200">Hoe meld ik een
                            incident?</a></li>
                    <li><a href="#" class="text-sm hover:text-white transition-colors duration-200">Hoe neem ik contact
                            op?</a></li>
                </ul>
            </div>

            <!-- Kolom 4: Contact -->
            <div>
                <h3 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">Contact</h3>
                <ul class="space-y-2 text-sm">
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <a href="mailto:info@polaris.be">info@polaris.be</a>
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <a href="https://maps.app.goo.gl/V5QTb2u1gufSAxTBA">Parklaan 101, 9300 Aalst</a>
                    </li>
                </ul>

                <h3 class="text-white font-semibold text-sm uppercase tracking-wider mt-6 mb-2">Belangrijke Links</h3>
                <ul class="space-y-2">
                    <li><a href="/privacy.php"
                            class="text-sm hover:text-white transition-colors duration-200">Privacybeleid</a>
                    </li>
                    <li><a href="/algemene-voorwaarden.php"
                            class="text-sm hover:text-white transition-colors duration-200">Gebruiksvoorwaarden</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Onderste footer: copyright -->
    <div class="border-t border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-2">
                <p class="text-xs">&copy; <?php echo date('Y'); ?> Polaris. Alle rechten voorbehouden.</p>
                <p class="text-xs">Fictief portaal, geen officiële overheidsdienst.</p>
            </div>
        </div>
    </div>
</footer>

<!-- Eigen JavaScript -->
<script src="/assets/js/app.js"></script>
</body>

</html>