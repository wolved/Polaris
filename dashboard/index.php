<?php
/**
 * Dashboard
 *
 * Hoofdpagina van het interne portaal.
 * Bevat: stat cards, recente meldingen tabel, snelle acties etc
 */

session_start();

// Sessiebescherming: niet ingelogd dan terug naar login pagina
if (empty($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

require_once '../includes/db.php';

$pageTitle = 'Dashboard';

// Open meldingen
$stmtOpen = getPDO()->query("SELECT COUNT(*) FROM reports WHERE status = 'open'");
$aantalOpen = (int) $stmtOpen->fetchColumn();

// Aantal nieuwe open meldingen van vandaag (badge "+X vandaag")
$stmtVandaag = getPDO()->prepare(
    "SELECT COUNT(*) FROM reports WHERE status = 'open' AND DATE(created_at) = CURDATE()"
);
$stmtVandaag->execute();
$openVandaag = (int) $stmtVandaag->fetchColumn();

// Actieve interventies
$stmtInterv = getPDO()->query("SELECT COUNT(*) FROM interventies WHERE status = 'active'");
$aantalActief = (int) $stmtInterv->fetchColumn();

// Geregistreerde gebruikers (personen)
$stmtUsers = getPDO()->query("SELECT COUNT(*) FROM users");
$aantalPersonen = (int) $stmtUsers->fetchColumn();

// Nieuwe gebruikers deze kalendermaand (badge "+X deze maand")
$stmtMaand = getPDO()->query(
    "SELECT COUNT(*) FROM users
     WHERE YEAR(created_at) = YEAR(CURDATE())
       AND MONTH(created_at) = MONTH(CURDATE())"
);
$nieuweDezeeMaand = (int) $stmtMaand->fetchColumn();

// Voertuigen
$aantalVoertuigen  = 0;
$aantalBeschikbaar = 0;
try {
    $aantalVoertuigen  = (int) getPDO()->query("SELECT COUNT(*) FROM vehicles")->fetchColumn();
    $aantalBeschikbaar = (int) getPDO()->query("SELECT COUNT(*) FROM vehicles WHERE status = 'beschikbaar'")->fetchColumn();
} catch (\Throwable) {
}

// Recente meldingen (laatste 5)
$stmtMeldingen = getPDO()->query(
    "SELECT reports.*, users.full_name AS created_by
     FROM reports
     JOIN users ON reports.created_by = users.id
     ORDER BY created_at DESC
     LIMIT 5"
);

$rows = $stmtMeldingen->fetchAll();

// array structure maken
$recenteMeldingen = [];
foreach ($rows as $row) {
    $recenteMeldingen[] = [
        'id'         => 'MELD-' . date('Y', strtotime($row['created_at']))
                        . '-' . str_pad((string) $row['id'], 4, '0', STR_PAD_LEFT),
        'type'       => htmlspecialchars($row['title'],   ENT_QUOTES, 'UTF-8'),
        'locatie'    => htmlspecialchars($row['locatie'], ENT_QUOTES, 'UTF-8'),
        'tijd'       => date('H:i', strtotime($row['created_at'])),
        'status'     => normaliseerStatus($row['status']),
        'prioriteit' => normaliseerPrioriteit($row['priority']),
    ];
}
?>

<?php include '../includes/header.php'; ?>

<!-- Dashboard layout: sidebar + main content -->
<div class="flex h-screen overflow-hidden">

    <!-- Sidebar include -->
    <?php include '../includes/sidebar.php'; ?>

    <!-- Hoofdcontent -->
    <div class="flex-1 lg:ml-64 flex flex-col overflow-hidden">

        <!-- Topbar -->
        <header
            class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 lg:px-8 flex-shrink-0">
            <div class="flex items-center gap-4">
                <button id="sidebar-toggle"
                    class="lg:hidden p-2 rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-colors"
                    aria-label="Sidebar openen">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div>
                    <h1 class="text-lg font-bold text-navy-900">Dashboard</h1>
                    <p class="text-xs text-slate-400 hidden sm:block">Overzicht van alle operationele activiteiten</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <!-- Notificatie knop -->
                <button
                    class="relative p-2 rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <?php if ($aantalOpen > 0): ?>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full"></span>
                    <?php endif; ?>
                </button>

                <!-- Datum/tijd -->
                <div class="hidden md:block text-right">
                    <p class="text-sm font-medium text-slate-700"><?php echo date('d M Y'); ?></p>
                    <p class="text-xs text-slate-400" id="current-time"></p>
                </div>
            </div>
        </header>

        <!-- Scrollbare content -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-slate-50">

                <!-- STAT CARDS, met open meldingen, actieve interventies, geregistreerde personen en voertuigen in systeem -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

                <!-- Open meldingen -->
                <div
                    class="bg-white rounded-2xl border border-slate-200/80 p-5 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-11 h-11 bg-red-50 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-red-500 bg-red-50 px-2 py-1 rounded-full">
                            +<?php echo $openVandaag; ?> vandaag
                        </span>
                    </div>
                    <p class="text-2xl font-bold text-navy-900"><?php echo $aantalOpen; ?></p>
                    <p class="text-sm text-slate-500 mt-0.5">Open meldingen</p>
                </div>

                <!-- Actieve interventies -->
                <div
                    class="bg-white rounded-2xl border border-slate-200/80 p-5 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-11 h-11 bg-amber-50 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-amber-600 bg-amber-50 px-2 py-1 rounded-full">
                            <?php echo $aantalActief; ?> actief
                        </span>
                    </div>
                    <p class="text-2xl font-bold text-navy-900"><?php echo $aantalActief; ?></p>
                    <p class="text-sm text-slate-500 mt-0.5">Actieve interventies</p>
                </div>

                <!-- Geregistreerde personen -->
                <div
                    class="bg-white rounded-2xl border border-slate-200/80 p-5 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-11 h-11 bg-blue-50 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded-full">
                            +<?php echo $nieuweDezeeMaand; ?> deze maand
                        </span>
                    </div>
                    <p class="text-2xl font-bold text-navy-900"><?php echo number_format($aantalPersonen, 0, ',', '.'); ?></p>
                    <p class="text-sm text-slate-500 mt-0.5">Geregistreerde personen</p>
                </div>

                <!-- Voertuigen in systeem -->
                <div
                    class="bg-white rounded-2xl border border-slate-200/80 p-5 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-11 h-11 bg-emerald-50 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M8 17h.01M16 17h.01M3 11l1.5-5A2 2 0 016.4 4.5h11.2a2 2 0 011.9 1.5L21 11M3 11v6a1 1 0 001 1h1m16-7v6a1 1 0 01-1 1h-1M3 11h18" />
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">
                            <?php echo $aantalBeschikbaar; ?> beschikbaar
                        </span>
                    </div>
                    <p class="text-2xl font-bold text-navy-900"><?php echo $aantalVoertuigen; ?></p>
                    <p class="text-sm text-slate-500 mt-0.5">Voertuigen in systeem</p>
                </div>
            </div>

            <!-- Recent meldingen, met tabel van laatste 5 meldingen -->
            <div class="bg-white rounded-2xl border border-slate-200/80 mb-8">
                <!-- Header -->
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-navy-900">Recente meldingen</h2>
                        <p class="text-sm text-slate-500 mt-0.5">Laatste 5 meldingen in het systeem</p>
                    </div>
                    <a href="/dashboard/incidents.php"
                        class="text-sm text-accent hover:text-accent-dark font-medium transition-colors flex items-center gap-1">
                        Alle bekijken
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                <!-- Tabel -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                    ID</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                    Type</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider hidden md:table-cell">
                                    Locatie</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                    Tijd</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider hidden lg:table-cell">
                                    Prioriteit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (empty($recenteMeldingen)): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-400">
                                        Geen meldingen gevonden in het systeem.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recenteMeldingen as $melding): ?>
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-4 text-sm font-mono font-medium text-navy-900">
                                            <?php echo $melding['id']; ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-700"><?php echo $melding['type']; ?></td>
                                        <td class="px-6 py-4 text-sm text-slate-500 hidden md:table-cell">
                                            <?php echo $melding['locatie']; ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-500"><?php echo $melding['tijd']; ?></td>
                                        <td class="px-6 py-4">
                                            <?php
                                            $statusKleur = match ($melding['status']) {
                                                'Open'           => 'bg-red-50 text-red-700 border-red-200',
                                                'In behandeling' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                'Afgerond'       => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                default          => 'bg-slate-50 text-slate-600 border-slate-200',
                                            };
                                            ?>
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border <?php echo $statusKleur; ?>">
                                                <?php echo $melding['status']; ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 hidden lg:table-cell">
                                            <?php
                                            $prioKleur = match ($melding['prioriteit']) {
                                                'Hoog'     => 'text-red-600',
                                                'Gemiddeld' => 'text-amber-600',
                                                'Laag'     => 'text-slate-500',
                                                default    => 'text-slate-400',
                                            };
                                            ?>
                                            <span
                                                class="text-xs font-medium <?php echo $prioKleur; ?>"><?php echo $melding['prioriteit']; ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Snelle acties, met knoppen voor nieuwe melding, persoon opzoeken en verslag opstellen -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6">
                <h2 class="text-base font-bold text-navy-900 mb-1">Snelle acties</h2>
                <p class="text-sm text-slate-500 mb-5">Veelgebruikte bewerkingen snel starten</p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                    <!-- Nieuwe melding -->
                    <a href="/dashboard/incidents.php?nieuw=1"
                        class="group flex items-center gap-4 p-4 rounded-xl border border-slate-200 hover:border-accent/30 hover:shadow-md transition-all duration-200">
                        <div
                            class="w-12 h-12 bg-accent/10 rounded-xl flex items-center justify-center group-hover:bg-accent/20 transition-colors flex-shrink-0">
                            <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-navy-900">Nieuwe melding</p>
                            <p class="text-xs text-slate-500">Registreer een nieuw incident</p>
                        </div>
                    </a>

                    <!-- Persoon opzoeken -->
                    <a href="/dashboard/persons.php"
                        class="group flex items-center gap-4 p-4 rounded-xl border border-slate-200 hover:border-accent/30 hover:shadow-md transition-all duration-200">
                        <div
                            class="w-12 h-12 bg-accent/10 rounded-xl flex items-center justify-center group-hover:bg-accent/20 transition-colors flex-shrink-0">
                            <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-navy-900">Persoon opzoeken</p>
                            <p class="text-xs text-slate-500">Zoek in het personenregister</p>
                        </div>
                    </a>

                    <!-- Verslag opstellen -->
                    <a href="/dashboard/reports.php?nieuw=1"
                        class="group flex items-center gap-4 p-4 rounded-xl border border-slate-200 hover:border-accent/30 hover:shadow-md transition-all duration-200">
                        <div
                            class="w-12 h-12 bg-accent/10 rounded-xl flex items-center justify-center group-hover:bg-accent/20 transition-colors flex-shrink-0">
                            <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-navy-900">Verslag opstellen</p>
                            <p class="text-xs text-slate-500">Maak een nieuw rapport aan</p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="h-4"></div>
        </main>
    </div>
</div>

<!-- Eigen JavaScript -->
<script src="/assets/js/app.js"></script>
</body>

</html>
