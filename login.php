<?php
/**
 * Loginpagina
 * 
 * Nog geen echte authenticatie, alleen voor demo
 */

$pageTitle = 'Inloggen';
?>

<?php include 'includes/header.php'; ?>

<div
    class="min-h-screen flex items-center justify-center bg-gradient-to-br from-navy-950 via-navy-900 to-navy-800 relative overflow-hidden">

    <!-- Decoratieve achtergrond elementen -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-1/4 -left-20 w-80 h-80 bg-accent rounded-full blur-3xl"></div>
        <div class="absolute bottom-1/4 -right-20 w-96 h-96 bg-accent-light rounded-full blur-3xl"></div>
    </div>
    <!-- Subtiel raster-patroon -->
    <div class="absolute inset-0 opacity-[0.03]"
        style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 32px 32px;">
    </div>

    <!-- Login card -->
    <div class="relative w-full max-w-md mx-4">
        <div class="bg-white rounded-2xl shadow-2xl p-8 sm:p-10">

            <!-- Logo en titel -->
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4">
                    <img src="/assets/images/polarisLogo.png" alt="Polaris Logo" class="h-20 w-auto">
                </div>
                <h1 class="text-2xl font-bold text-navy-900 mb-1">Inloggen</h1>
                <p class="text-slate-500 text-sm">Intern interventie- en meldingsportaal</p>
            </div>

            <!-- Foutmelding placeholder (verborgen standaard) -->
            <div id="login-error" class="hidden mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm text-red-700">Ongeldige gebruikersnaam of wachtwoord.</p>
                </div>
            </div>

            <!-- Login formulier -->
            <form action="#" method="POST" class="space-y-5">

                <!-- Gebruikersnaam -->
                <div>
                    <label for="username" class="block text-sm font-medium text-slate-700 mb-1.5">Gebruikersnaam</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input type="text" id="username" name="username" required
                            placeholder="Voer je gebruikersnaam in"
                            class="w-full pl-11 pr-4 py-3 border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent transition-all duration-200">
                    </div>
                </div>

                <!-- Wachtwoord -->
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Wachtwoord</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input type="password" id="password" name="password" required
                            placeholder="Voer je wachtwoord in"
                            class="w-full pl-11 pr-12 py-3 border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent transition-all duration-200">
                        <!-- Wachtwoord tonen/verbergen -->
                        <button type="button" id="toggle-password"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                            <svg id="eye-open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg id="eye-closed" class="w-5 h-5 hidden" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Onthoud mij -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember"
                            class="w-4 h-4 rounded border-slate-300 text-accent focus:ring-accent/50">
                        <span class="text-sm text-slate-600">Onthoud mij</span>
                    </label>
                    <a href="#" class="text-sm text-accent hover:text-accent-dark font-medium transition-colors">
                        Wachtwoord vergeten?
                    </a>
                </div>

                <!-- Login knop -->
                <button type="submit"
                    class="w-full bg-accent hover:bg-accent-dark text-white py-3 rounded-xl text-sm font-semibold transition-all duration-300 shadow-lg shadow-accent/25 hover:shadow-accent/40 hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    Inloggen
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </form>

            <!-- Divider -->
            <div class="my-6 flex items-center gap-4">
                <div class="flex-1 h-px bg-slate-200"></div>
                <span class="text-xs text-slate-400 uppercase tracking-wide">of</span>
                <div class="flex-1 h-px bg-slate-200"></div>
            </div>

            <!-- Terug naar homepage -->
            <div class="text-center">
                <a href="/"
                    class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-navy-900 font-medium transition-colors duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Terug naar homepagina
                </a>
            </div>
        </div>

        <!-- Disclaimer -->
        <p class="text-center text-xs text-white/40 mt-6">
            &copy; <?php echo date('Y'); ?> Polaris. Fictief portaal, geen officiële overheidsdienst.
        </p>
    </div>
</div>

<!-- Eigen JavaScript, app.js bevat alle generieke functies -->
<script src="/assets/js/app.js"></script>
</body>

</html>