<?php
/** Instellingen */

session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

require_once '../includes/db.php';

$pageTitle = 'Instellingen';

// Helpers
function userHeeftKolom(string $kolom): bool
{
    static $cache = null;
    if ($cache === null) {
        try {
            $cache = array_column(
                getPDO()->query('SHOW COLUMNS FROM users')->fetchAll(),
                'Field'
            );
        } catch (\Throwable) {
            $cache = [];
        }
    }
    return in_array($kolom, $cache, true);
}

function auditDatum(?string $ts): string
{
    if (!$ts) {
        return '—';
    }
    $t = strtotime($ts);
    return $t ? date('d M Y · H:i', $t) : '—';
}

// Huidige gebruiker ophalen
$stmt = getPDO()->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch() ?: [];

$fullName = $user['full_name'] ?? '';
$username = $user['username']  ?? '';
$email    = $user['email']     ?? '';
$telefoon = $user['telefoon']  ?? '';
$role     = $user['role']      ?? '';
$userId   = (int)($user['id']  ?? 0);
$isAdmin  = strtolower($role) === 'admin';

$lastLogin          = $user['last_login']          ?? null;
$lastPasswordChange = $user['last_password_change'] ?? null;
$lastProfileUpdate  = $user['last_profile_update']  ?? null;
$createdAt          = $user['created_at']           ?? null;

// Voorkeuren uit sessie (fallback naar defaults)
$prefs = $_SESSION['preferences'] ?? [];
$prefTaal           = $prefs['taal']             ?? 'nl';
$prefTheme          = $prefs['theme']            ?? 'light';
$prefRefresh        = (int)($prefs['refresh_interval'] ?? 60);
$prefTimezone       = $prefs['timezone']         ?? 'Europe/Brussels';
$prefNotifyEmail    = $prefs['notify_email']     ?? true;
$prefNotifyDash     = $prefs['notify_dashboard'] ?? true;
$prefNotifySms      = $prefs['notify_sms']       ?? false;

$ops = $_SESSION['operational'] ?? [];
$opEenheid          = $ops['eenheid']           ?? '';
$opDispatch         = $ops['dispatch']          ?? 'auto';
$opPrioAlerts       = $ops['prioriteit_alerts'] ?? true;
$opAutoStatus       = $ops['auto_status']       ?? false;

// Flash berichten
$success = $_GET['success'] ?? null;
$error   = $_GET['error']   ?? null;

$flashSuccessTekst = match ($success) {
    'profiel_opgeslagen'      => 'Profielgegevens succesvol bijgewerkt.',
    'ww_gewijzigd'            => 'Wachtwoord succesvol gewijzigd.',
    'voorkeuren_opgeslagen'   => 'Voorkeuren succesvol opgeslagen.',
    'operationeel_opgeslagen' => 'Operationele instellingen opgeslagen.',
    default                   => $success ? 'Wijziging succesvol opgeslagen.' : null,
};

$flashErrorTekst = match ($error) {
    'validatie'         => 'Vul alle verplichte velden in.',
    'email_ongeldig'    => 'Emailadres is niet geldig.',
    'username_bestaat'  => 'Deze gebruikersnaam is al in gebruik.',
    'ww_mismatch'       => 'De twee nieuwe wachtwoorden komen niet overeen.',
    'ww_kort'           => 'Nieuw wachtwoord moet minstens 8 tekens bevatten.',
    'ww_fout'           => 'Huidig wachtwoord is onjuist.',
    'db'                => 'Databasefout. Probeer opnieuw.',
    default             => $error ? 'Er is een fout opgetreden.' : null,
};

// Initialen voor avatar omdat we avatar niet gaan gebruiken
$naamDelen = explode(' ', trim($fullName ?: 'U'));
$initialen = strtoupper(
    ($naamDelen[0][0] ?? 'U') . (count($naamDelen) > 1 ? end($naamDelen)[0] : '')
);

// Formeel personeelsnummer
$personeelsnr = 'PL-' . str_pad((string)$userId, 5, '0', STR_PAD_LEFT);
?>

<?php include '../includes/header.php'; ?>

<div class="flex h-screen overflow-hidden">

    <?php include '../includes/sidebar.php'; ?>

    <div class="flex-1 lg:ml-64 flex flex-col overflow-hidden">

        <header
            class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 lg:px-8 flex-shrink-0">
            <div class="flex items-center gap-4">
                <button id="sidebar-toggle"
                    class="lg:hidden p-2 rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-colors"
                    aria-label="Sidebar openen">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div>
                    <h1 class="text-lg font-bold text-navy-900">Instellingen</h1>
                    <p class="text-xs text-slate-400 hidden sm:block">
                        Beheer je profiel, beveiliging en systeemvoorkeuren
                    </p>
                </div>
            </div>

            <div class="hidden md:flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Account actief
                </span>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-slate-50">

            <!-- Flash berichten -->
            <?php if ($flashSuccessTekst): ?>
                <div class="mb-5 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3 text-sm">
                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <?php echo htmlspecialchars($flashSuccessTekst, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>
            <?php if ($flashErrorTekst): ?>
                <div class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <?php echo htmlspecialchars($flashErrorTekst, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <!-- Hero card: gebruiker samenvatting -->
            <div class="relative overflow-hidden bg-gradient-to-br from-navy-900 via-navy-800 to-navy-950 rounded-2xl border border-navy-800/60 shadow-xl mb-8">
                <div class="absolute inset-0 opacity-[0.04]"
                     style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 28px 28px;"></div>
                <div class="absolute -top-20 -right-10 w-64 h-64 bg-accent/20 rounded-full blur-3xl"></div>

                <div class="relative p-6 sm:p-8 flex flex-col lg:flex-row lg:items-center gap-6">
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-accent/20 border border-accent/30 flex items-center justify-center flex-shrink-0 backdrop-blur-sm">
                            <span class="text-accent-light text-xl sm:text-2xl font-bold"><?php echo $initialen; ?></span>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h2 class="text-lg sm:text-xl font-bold text-white truncate">
                                    <?php echo htmlspecialchars($fullName ?: 'Onbekende gebruiker', ENT_QUOTES, 'UTF-8'); ?>
                                </h2>
                            </div>
                            <p class="text-sm text-white/60 mt-0.5">
                                <?php echo htmlspecialchars(ucfirst($role ?: 'Operateur'), ENT_QUOTES, 'UTF-8'); ?>
                                &nbsp;·&nbsp; <span class="font-mono text-white/50"><?php echo $personeelsnr; ?></span>
                            </p>
                        </div>
                    </div>

                    <div class="lg:ml-auto grid grid-cols-3 gap-2 sm:gap-3">
                        <div class="px-3 sm:px-4 py-2.5 rounded-xl bg-white/5 border border-white/10">
                            <p class="text-[10px] uppercase tracking-wider text-white/40 font-semibold">Sessie</p>
                            <p class="text-sm font-semibold text-white flex items-center gap-1.5 mt-0.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                Veilig
                            </p>
                        </div>
                        <div class="px-3 sm:px-4 py-2.5 rounded-xl bg-white/5 border border-white/10">
                            <p class="text-[10px] uppercase tracking-wider text-white/40 font-semibold">2FA</p>
                            <p class="text-sm font-semibold text-white/80 flex items-center gap-1.5 mt-0.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                Niet geactiveerd
                            </p>
                        </div>
                        <div class="px-3 sm:px-4 py-2.5 rounded-xl bg-white/5 border border-white/10">
                            <p class="text-[10px] uppercase tracking-wider text-white/40 font-semibold">Lid sinds</p>
                            <p class="text-sm font-semibold text-white mt-0.5">
                                <?php echo $createdAt ? date('M Y', strtotime($createdAt)) : '—'; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grid: sticky sectie nav + content -->
            <div class="grid grid-cols-1 xl:grid-cols-[260px_1fr] gap-6">

                <!-- Sectie navigatie -->
                <aside class="xl:sticky xl:top-6 h-max">
                    <nav class="bg-white rounded-2xl border border-slate-200/80 p-2">
                        <a href="#profiel" class="settings-nav flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors" data-section="profiel">
                            <span class="w-8 h-8 rounded-lg bg-accent/10 text-accent flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </span>
                            Profiel
                        </a>
                        <a href="#beveiliging" class="settings-nav flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors" data-section="beveiliging">
                            <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </span>
                            Beveiliging
                        </a>
                        <a href="#voorkeuren" class="settings-nav flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors" data-section="voorkeuren">
                            <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </span>
                            Systeem
                        </a>
                        <a href="#operationeel" class="settings-nav flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors" data-section="operationeel">
                            <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </span>
                            Operationeel
                        </a>
                        <?php if ($isAdmin): ?>
                            <a href="#audit" class="settings-nav flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors" data-section="audit">
                                <span class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                </span>
                                Audit & activiteit
                                <span class="ml-auto text-[10px] font-semibold uppercase tracking-wider text-red-500">Admin</span>
                            </a>
                        <?php endif; ?>
                    </nav>

                    <div class="hidden xl:block mt-4 rounded-2xl border border-slate-200/80 bg-white p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-accent/10 text-accent flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-navy-900">Hulp nodig?</p>
                                <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">
                                    Contacteer je systeembeheerder voor rechten of account­wijzigingen.
                                </p>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Content -->
                <div class="space-y-6">

                    <!-- Profiel -->
                    <section id="profiel" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm scroll-mt-6">
                        <div class="px-6 py-5 border-b border-slate-100 flex items-start justify-between gap-4">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl bg-accent/10 text-accent flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <div>
                                    <h2 class="text-base font-bold text-navy-900">Profiel instellingen</h2>
                                    <p class="text-sm text-slate-500 mt-0.5">Persoonsgegevens zichtbaar voor collega's binnen het portaal.</p>
                                </div>
                            </div>
                        </div>

                        <form method="POST" action="/actions/settings_action.php" id="profile-form" class="p-6 space-y-5">
                            <input type="hidden" name="action" value="update_profile">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Volledige naam <span class="text-accent">*</span></label>
                                    <input type="text" name="full_name" required
                                        data-original="<?php echo htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8'); ?>"
                                        value="<?php echo htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8'); ?>"
                                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-all duration-200">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Gebruikersnaam <span class="text-accent">*</span></label>
                                    <input type="text" name="username" required
                                        data-original="<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>"
                                        value="<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>"
                                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-800 font-mono placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-all duration-200">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">E‑mailadres</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        </div>
                                        <input type="email" name="email"
                                            data-original="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>"
                                            value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>"
                                            placeholder="voornaam.naam@polaris.be"
                                            class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-all duration-200">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Telefoonnummer</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        </div>
                                        <input type="tel" name="telefoon"
                                            data-original="<?php echo htmlspecialchars($telefoon, ENT_QUOTES, 'UTF-8'); ?>"
                                            value="<?php echo htmlspecialchars($telefoon, ENT_QUOTES, 'UTF-8'); ?>"
                                            placeholder="+32 4xx xx xx xx"
                                            class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-all duration-200">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Personeelsnummer</label>
                                    <div class="flex items-center gap-2 px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-sm font-mono text-slate-600">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                        <?php echo $personeelsnr; ?>
                                        <span class="ml-auto text-[10px] uppercase tracking-wider text-slate-400 font-sans">Niet wijzigbaar</span>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Functie / rol</label>
                                    <div class="flex items-center gap-2 px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-sm text-slate-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-accent"></span>
                                        <span class="font-medium"><?php echo htmlspecialchars(ucfirst($role ?: '—'), ENT_QUOTES, 'UTF-8'); ?></span>
                                        <span class="ml-auto text-[10px] uppercase tracking-wider text-slate-400">Toegewezen door beheerder</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Save button (verschijnt enkel bij wijzigingen) -->
                            <div id="profile-save-bar"
                                 class="hidden -mx-6 -mb-6 mt-6 px-6 py-4 border-t border-slate-100 bg-slate-50/70 rounded-b-2xl flex items-center justify-between gap-3">
                                <p class="text-xs text-slate-500 flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                    Je hebt niet‑opgeslagen wijzigingen
                                </p>
                                <div class="flex items-center gap-2">
                                    <button type="button" id="profile-reset"
                                        class="px-4 py-2 border border-slate-200 text-slate-600 hover:bg-white rounded-xl text-sm font-medium transition-colors">
                                        Annuleren
                                    </button>
                                    <button type="submit"
                                        class="inline-flex items-center gap-2 bg-accent hover:bg-accent-dark text-white px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 shadow-lg shadow-accent/25">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Profiel opslaan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </section>

                    <!-- Beveiliging -->
                    <section id="beveiliging" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm scroll-mt-6">
                        <div class="px-6 py-5 border-b border-slate-100 flex items-start justify-between gap-4">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </div>
                                <div>
                                    <h2 class="text-base font-bold text-navy-900">Beveiliging</h2>
                                    <p class="text-sm text-slate-500 mt-0.5">Houd je account veilig met een sterk, uniek wachtwoord.</p>
                                </div>
                            </div>
                        </div>

                        <form method="POST" action="/actions/settings_action.php" class="p-6 space-y-5" id="password-form">
                            <input type="hidden" name="action" value="change_password">

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Huidig wachtwoord <span class="text-accent">*</span></label>
                                <input type="password" name="current_password" required autocomplete="current-password"
                                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-all duration-200">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nieuw wachtwoord <span class="text-accent">*</span></label>
                                    <input type="password" name="new_password" required minlength="8" autocomplete="new-password" id="new-password"
                                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-all duration-200">
                                    <p class="mt-1.5 text-xs text-slate-400">Min. 8 tekens. Gebruik hoofdletters, cijfers en symbolen.</p>

                                    <!-- Sterkte indicator -->
                                    <div class="mt-2 flex items-center gap-1.5" id="pw-strength">
                                        <span class="h-1.5 flex-1 rounded-full bg-slate-100 transition-colors"></span>
                                        <span class="h-1.5 flex-1 rounded-full bg-slate-100 transition-colors"></span>
                                        <span class="h-1.5 flex-1 rounded-full bg-slate-100 transition-colors"></span>
                                        <span class="h-1.5 flex-1 rounded-full bg-slate-100 transition-colors"></span>
                                        <span class="text-[11px] text-slate-400 font-medium ml-2 w-16 text-right" id="pw-label">Zwak</span>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Bevestig nieuw wachtwoord <span class="text-accent">*</span></label>
                                    <input type="password" name="confirm_password" required minlength="8" autocomplete="new-password"
                                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-all duration-200">
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-2">
                                <p class="text-xs text-slate-500 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Je wordt na wijziging niet uitgelogd.
                                </p>
                                <button type="submit"
                                    class="inline-flex items-center gap-2 bg-navy-900 hover:bg-navy-800 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                    Wachtwoord wijzigen
                                </button>
                            </div>
                        </form>
                    </section>

                    <!-- Systeem voorkeuren -->
                    <section id="voorkeuren" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm scroll-mt-6">
                        <div class="px-6 py-5 border-b border-slate-100 flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-navy-900">Systeem voorkeuren</h2>
                                <p class="text-sm text-slate-500 mt-0.5">Pas het portaal aan naar jouw manier van werken.</p>
                            </div>
                        </div>

                        <form method="POST" action="/actions/settings_action.php" class="p-6 space-y-6">
                            <input type="hidden" name="action" value="update_preferences">

                            <!-- Taal + Timezone -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Taal van de interface</label>
                                    <select name="taal"
                                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-all duration-200 bg-white">
                                        <option value="nl" <?php echo $prefTaal === 'nl' ? 'selected' : ''; ?>>Nederlands</option>
                                        <option value="fr" <?php echo $prefTaal === 'fr' ? 'selected' : ''; ?>>Français</option>
                                        <option value="en" <?php echo $prefTaal === 'en' ? 'selected' : ''; ?>>English</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Tijdzone</label>
                                    <select name="timezone"
                                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-all duration-200 bg-white">
                                        <?php
                                        $tzOpties = [
                                            'Europe/Brussels' => '🇧🇪 Brussel (GMT+1)',
                                            'Europe/Amsterdam' => '🇳🇱 Amsterdam (GMT+1)',
                                            'Europe/Paris'    => '🇫🇷 Parijs (GMT+1)',
                                            'Europe/London'   => '🇬🇧 Londen (GMT+0)',
                                            'UTC'             => '🌐 UTC',
                                        ];
                                        foreach ($tzOpties as $tz => $label):
                                        ?>
                                            <option value="<?php echo $tz; ?>" <?php echo $prefTimezone === $tz ? 'selected' : ''; ?>>
                                                <?php echo $label; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Notificatie voorkeuren -->
                            <div>
                                <p class="text-sm font-semibold text-navy-900 mb-3">Notificatie voorkeuren</p>
                                <div class="space-y-2">
                                    <?php
                                    $notifs = [
                                        ['notify_email',     'E‑mail notificaties', 'Ontvang belangrijke meldingen in je mailbox.', $prefNotifyEmail],
                                        ['notify_dashboard', 'Dashboard alerts',    'Toon live meldingen in het dashboard.',         $prefNotifyDash],
                                        ['notify_sms',       'SMS berichten',       'Enkel voor kritieke operationele waarschuwingen.', $prefNotifySms],
                                    ];
                                    foreach ($notifs as [$naam, $titel, $omschr, $aan]):
                                    ?>
                                        <label class="flex items-center justify-between gap-3 p-3 rounded-xl border border-slate-200 hover:border-slate-300 transition-colors cursor-pointer">
                                            <div>
                                                <p class="text-sm font-medium text-navy-900"><?php echo $titel; ?></p>
                                                <p class="text-xs text-slate-500 mt-0.5"><?php echo $omschr; ?></p>
                                            </div>
                                            <span class="relative inline-block">
                                                <input type="checkbox" name="<?php echo $naam; ?>" class="peer sr-only" <?php echo $aan ? 'checked' : ''; ?>>
                                                <span class="w-11 h-6 bg-slate-200 peer-checked:bg-accent rounded-full transition-colors block"></span>
                                                <span class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow-sm transition-transform peer-checked:translate-x-5 block"></span>
                                            </span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Theme + Refresh -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <p class="text-sm font-medium text-slate-700 mb-2">Thema</p>
                                    <div class="grid grid-cols-3 gap-2">
                                        <?php
                                        $themes = [
                                            ['light',  'Licht',    'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z'],
                                            ['dark',   'Donker',   'M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z'],
                                            ['system', 'Systeem',  'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                                        ];
                                        foreach ($themes as [$val, $label, $icon]):
                                            $aan = $prefTheme === $val;
                                        ?>
                                            <label class="relative cursor-pointer">
                                                <input type="radio" name="theme" value="<?php echo $val; ?>" class="peer sr-only" <?php echo $aan ? 'checked' : ''; ?>>
                                                <div class="flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 border-slate-200 peer-checked:border-accent peer-checked:bg-accent/5 transition-all">
                                                    <svg class="w-5 h-5 text-slate-500 peer-checked:text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="<?php echo $icon; ?>"/>
                                                    </svg>
                                                    <span class="text-xs font-medium text-slate-700"><?php echo $label; ?></span>
                                                </div>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                    <p class="mt-2 text-[11px] text-slate-400 italic">Donker thema binnenkort beschikbaar.</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                        Dashboard refresh interval
                                        <span class="text-slate-400 font-normal">· <span id="refresh-label"><?php echo $prefRefresh; ?></span> seconden</span>
                                    </label>
                                    <input type="range" name="refresh_interval" min="10" max="300" step="10" value="<?php echo $prefRefresh; ?>"
                                        oninput="document.getElementById('refresh-label').textContent = this.value"
                                        class="w-full accent-accent">
                                    <div class="flex justify-between text-[10px] text-slate-400 mt-1">
                                        <span>10s</span><span>60s</span><span>5 min</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end pt-2 border-t border-slate-100">
                                <button type="submit"
                                    class="inline-flex items-center gap-2 bg-accent hover:bg-accent-dark text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 shadow-lg shadow-accent/25">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Voorkeuren opslaan
                                </button>
                            </div>
                        </form>
                    </section>

                    <!-- Operationele instellingen -->
                    <section id="operationeel" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm scroll-mt-6">
                        <div class="px-6 py-5 border-b border-slate-100 flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-navy-900">Operationele instellingen</h2>
                                <p class="text-sm text-slate-500 mt-0.5">Configureer jouw dispatch‑ en meldingsvoorkeuren op het terrein.</p>
                            </div>
                        </div>

                        <form method="POST" action="/actions/settings_action.php" class="p-6 space-y-5">
                            <input type="hidden" name="action" value="update_operational">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Standaard eenheid / regio</label>
                                    <select name="eenheid"
                                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-all duration-200 bg-white">
                                        <option value="">— Geen voorkeur —</option>
                                        <?php
                                        $eenheden = ['Brussel Centrum', 'Antwerpen Noord', 'Gent West', 'Luik Zuid', 'Brugge', 'Namen', 'Hasselt'];
                                        foreach ($eenheden as $eh):
                                        ?>
                                            <option value="<?php echo $eh; ?>" <?php echo $opEenheid === $eh ? 'selected' : ''; ?>>
                                                <?php echo $eh; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Dispatch voorkeur</label>
                                    <select name="dispatch"
                                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-all duration-200 bg-white">
                                        <option value="auto"    <?php echo $opDispatch === 'auto'    ? 'selected' : ''; ?>>Automatisch (aanbevolen)</option>
                                        <option value="manueel" <?php echo $opDispatch === 'manueel' ? 'selected' : ''; ?>>Manueel</option>
                                        <option value="hybride" <?php echo $opDispatch === 'hybride' ? 'selected' : ''; ?>>Hybride</option>
                                    </select>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="flex items-center justify-between gap-3 p-3 rounded-xl border border-slate-200 hover:border-slate-300 transition-colors cursor-pointer">
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-navy-900">Prioriteitsmeldingen</p>
                                            <p class="text-xs text-slate-500 mt-0.5">Ontvang directe alerts voor hoge‑prioriteit incidenten.</p>
                                        </div>
                                    </div>
                                    <span class="relative inline-block flex-shrink-0">
                                        <input type="checkbox" name="prioriteit_alerts" class="peer sr-only" <?php echo $opPrioAlerts ? 'checked' : ''; ?>>
                                        <span class="w-11 h-6 bg-slate-200 peer-checked:bg-accent rounded-full transition-colors block"></span>
                                        <span class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow-sm transition-transform peer-checked:translate-x-5 block"></span>
                                    </span>
                                </label>

                                <label class="flex items-center justify-between gap-3 p-3 rounded-xl border border-slate-200 hover:border-slate-300 transition-colors cursor-pointer">
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-navy-900">Automatische statusupdates</p>
                                            <p class="text-xs text-slate-500 mt-0.5">Laat het systeem de status van meldingen automatisch bijwerken.</p>
                                        </div>
                                    </div>
                                    <span class="relative inline-block flex-shrink-0">
                                        <input type="checkbox" name="auto_status" class="peer sr-only" <?php echo $opAutoStatus ? 'checked' : ''; ?>>
                                        <span class="w-11 h-6 bg-slate-200 peer-checked:bg-accent rounded-full transition-colors block"></span>
                                        <span class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow-sm transition-transform peer-checked:translate-x-5 block"></span>
                                    </span>
                                </label>
                            </div>

                            <div class="flex justify-end pt-2 border-t border-slate-100">
                                <button type="submit"
                                    class="inline-flex items-center gap-2 bg-accent hover:bg-accent-dark text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 shadow-lg shadow-accent/25">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Instellingen opslaan
                                </button>
                            </div>
                        </form>
                    </section>

                    <!-- Audit & activiteit (admin only) -->
                    <?php if ($isAdmin): ?>
                        <section id="audit" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm scroll-mt-6">
                            <div class="px-6 py-5 border-b border-slate-100 flex items-start justify-between gap-4">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    </div>
                                    <div>
                                        <h2 class="text-base font-bold text-navy-900">Audit & activiteit</h2>
                                        <p class="text-sm text-slate-500 mt-0.5">Beveiligingslogboek en recente activiteit op je account.</p>
                                    </div>
                                </div>
                                <span class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-50 border border-red-200 text-red-700 text-[11px] font-medium uppercase tracking-wider flex-shrink-0">
                                    Enkel toegankelijk voor administratoren
                                </span>
                            </div>

                            <div class="p-6 space-y-6">
                                <!-- Key timestamps -->
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                                        <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Laatste login
                                        </div>
                                        <p class="text-sm font-semibold text-navy-900 mt-2"><?php echo auditDatum($lastLogin); ?></p>
                                    </div>
                                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                                        <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                            Laatste wachtwoord wijziging
                                        </div>
                                        <p class="text-sm font-semibold text-navy-900 mt-2"><?php echo auditDatum($lastPasswordChange); ?></p>
                                    </div>
                                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                                        <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Laatste profiel update
                                        </div>
                                        <p class="text-sm font-semibold text-navy-900 mt-2"><?php echo auditDatum($lastProfileUpdate); ?></p>
                                    </div>
                                </div>

                                <!-- Activiteit tijdlijn (illustratief) -->
                                <div>
                                    <div class="flex items-center justify-between mb-3">
                                        <p class="text-sm font-semibold text-navy-900">Recente activiteit</p>
                                        <span class="text-[11px] text-slate-400 uppercase tracking-wider font-medium">Laatste 24u</span>
                                    </div>

                                    <div class="space-y-3">
                                        <?php
                                        $activiteit = [
                                            ['type' => 'login',   'label' => 'Succesvolle login',            'ip' => '10.12.4.88',   'tijd' => date('d M Y · H:i'),                          'kleur' => 'emerald'],
                                            ['type' => 'update',  'label' => 'Profielgegevens bijgewerkt',   'ip' => '10.12.4.88',   'tijd' => date('d M Y · H:i', strtotime('-2 hours')),   'kleur' => 'blue'],
                                            ['type' => 'session', 'label' => 'Nieuwe sessie gestart',        'ip' => '10.12.4.88',   'tijd' => date('d M Y · H:i', strtotime('-5 hours')),   'kleur' => 'slate'],
                                            ['type' => 'perm',    'label' => 'Permissies gecontroleerd',     'ip' => 'system',       'tijd' => date('d M Y · H:i', strtotime('-9 hours')),   'kleur' => 'slate'],
                                        ];
                                        foreach ($activiteit as $act):
                                            $kleur = match($act['kleur']) {
                                                'emerald' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                                                'blue'    => 'bg-blue-50 text-blue-600 border-blue-200',
                                                default   => 'bg-slate-100 text-slate-500 border-slate-200',
                                            };
                                        ?>
                                            <div class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:bg-slate-50/60 transition-colors">
                                                <div class="w-8 h-8 rounded-lg <?php echo $kleur; ?> border flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-navy-900 truncate"><?php echo htmlspecialchars($act['label'], ENT_QUOTES, 'UTF-8'); ?></p>
                                                    <p class="text-xs text-slate-500 mt-0.5">
                                                        IP <span class="font-mono"><?php echo $act['ip']; ?></span>
                                                        &nbsp;·&nbsp; <?php echo $act['tijd']; ?>
                                                    </p>
                                                </div>
                                                <span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-full bg-slate-50 border border-slate-200 text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                                                    <?php echo $act['type']; ?>
                                                </span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <p class="text-[11px] text-slate-400 italic">
                                    Deze module is nog niet beschikbaar, enkel mockdata is hier zichtbaar.
                                </p>
                            </div>
                        </section>
                    <?php endif; ?>

                    <div class="h-4"></div>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="/assets/js/app.js"></script>
<script>
// Profiel form: save bar enkel tonen bij wijzigingen
(function() {
    const form = document.getElementById('profile-form');
    if (!form) return;

    const saveBar   = document.getElementById('profile-save-bar');
    const resetBtn  = document.getElementById('profile-reset');
    const inputs    = form.querySelectorAll('input[data-original]');

    function isDirty() {
        for (const el of inputs) {
            if ((el.value ?? '') !== (el.dataset.original ?? '')) return true;
        }
        return false;
    }

    function syncBar() {
        saveBar.classList.toggle('hidden', !isDirty());
    }

    inputs.forEach(el => el.addEventListener('input', syncBar));

    resetBtn?.addEventListener('click', () => {
        inputs.forEach(el => { el.value = el.dataset.original ?? ''; });
        syncBar();
    });
})();

// Wachtwoord sterkte indicator dankjewel stackoverflow!
(function() {
    const pw    = document.getElementById('new-password');
    const bars  = document.querySelectorAll('#pw-strength > span.h-1\\.5');
    const label = document.getElementById('pw-label');
    if (!pw || !bars.length) return;

    const levels = [
        { label: 'Zwak',       cls: 'bg-red-400',     text: 'text-red-500'     },
        { label: 'Zwak',       cls: 'bg-red-400',     text: 'text-red-500'     },
        { label: 'Matig',      cls: 'bg-amber-400',   text: 'text-amber-500'   },
        { label: 'Goed',       cls: 'bg-blue-400',    text: 'text-blue-500'    },
        { label: 'Uitstekend', cls: 'bg-emerald-500', text: 'text-emerald-600' },
    ];

    pw.addEventListener('input', () => {
        const v = pw.value;
        let score = 0;
        if (v.length >= 8)                score++;
        if (/[A-Z]/.test(v))              score++;
        if (/[0-9]/.test(v))              score++;
        if (/[^A-Za-z0-9]/.test(v))       score++;

        const cur = levels[score];
        bars.forEach((b, i) => {
            b.className = 'h-1.5 flex-1 rounded-full transition-colors ' + (i < score ? cur.cls : 'bg-slate-100');
        });
        label.className = 'text-[11px] font-medium ml-2 w-16 text-right ' + (score ? cur.text : 'text-slate-400');
        label.textContent = cur.label;
    });
})();

// Sectie nav: actieve markering op basis van scroll
(function() {
    const navLinks = document.querySelectorAll('.settings-nav');
    if (!navLinks.length) return;

    const activeCls = ['bg-accent/10', 'text-navy-900'];
    const inactiveCls = ['text-slate-600', 'hover:bg-slate-50'];

    function activate(id) {
        navLinks.forEach(a => {
            const actief = a.dataset.section === id;
            a.classList.toggle('bg-accent/10', actief);
            a.classList.toggle('text-navy-900', actief);
            a.classList.toggle('font-semibold', actief);
            a.classList.toggle('text-slate-600', !actief);
        });
    }

    // Eerste sectie actief op load, of hash target
    const hash = location.hash.replace('#', '');
    activate(hash || 'profiel');

    // Observer voor scroll syncing
    const sections = document.querySelectorAll('section[id]');
    const io = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) activate(e.target.id);
        });
    }, { rootMargin: '-40% 0px -55% 0px', threshold: 0 });
    sections.forEach(s => io.observe(s));

    // Smooth scroll naar sectie
    navLinks.forEach(a => {
        a.addEventListener('click', (e) => {
            const id = a.dataset.section;
            const target = document.getElementById(id);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                history.replaceState(null, '', '#' + id);
                activate(id);
            }
        });
    });
})();
</script>
</body>
</html>
