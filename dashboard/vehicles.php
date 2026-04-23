<?php
/**
 * Voertuigen
 * Overzicht met interactieve Leaflet kaart, statusbeheer en CRUD.
 */

session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

require_once '../includes/db.php';

$pageTitle = 'Voertuigen';
$success   = $_GET['success'] ?? null;
$error     = $_GET['error']   ?? null;
$openPanel = isset($_GET['nieuw']);

// Alle voertuigen ophalen
$voertuigen = getPDO()->query(
    "SELECT * FROM vehicles ORDER BY naam ASC"
)->fetchAll();

// Statistieken
$statsRaw = getPDO()->query(
    "SELECT
        COUNT(*) AS totaal,
        SUM(status = 'beschikbaar') AS beschikbaar,
        SUM(status = 'in_gebruik')  AS in_gebruik,
        SUM(status = 'onderhoud')   AS onderhoud
     FROM vehicles"
)->fetch();

// JSON voor Leaflet
$voertuigenJson = json_encode(array_map(fn($v) => [
    'id'          => (int) $v['id'],
    'naam'        => $v['naam'],
    'type'        => $v['type'],
    'kenteken'    => $v['kenteken'],
    'status'      => $v['status'],
    'lat'         => (float) $v['lat'],
    'lng'         => (float) $v['lng'],
    'locatie_naam'=> $v['locatie_naam'] ?? '',
], $voertuigen), JSON_UNESCAPED_UNICODE);

$voertuigTypes = [
    'Politievoertuig', 'Interventiewagen', 'Commandovoertuig',
    'Motor', 'Lichte vrachtwagen', 'Overig',
];
?>
<!DOCTYPE html>
<html lang="nl" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voertuigen - Polaris</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: {
                            50:'#e7eaf0',100:'#c3c9d9',200:'#9ba5bf',300:'#7280a5',
                            400:'#546591',500:'#354a7e',600:'#2f4376',700:'#27396b',
                            800:'#1f3061',900:'#121f4e',950:'#0a1330',
                        },
                        accent: { DEFAULT:'#3b82f6', light:'#60a5fa', dark:'#2563eb' },
                    },
                    fontFamily: { body: ['DM Sans','sans-serif'] },
                },
            },
        }
    </script>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">

    <style>
        /* Leaflet popup tweaks */
        .leaflet-popup-content-wrapper {
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            border: 1px solid rgba(226,232,240,0.8);
            padding: 0;
            overflow: hidden;
        }
        .leaflet-popup-content { margin: 0; }
        .leaflet-popup-tip-container { margin-top: -1px; }
        /* Kaart cursor in placement mode */
        #map.placement-mode { cursor: crosshair !important; }
        #map.placement-mode .leaflet-interactive { cursor: crosshair !important; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-body antialiased">

<div class="flex h-screen overflow-hidden">

    <?php include '../includes/sidebar.php'; ?>

    <div class="flex-1 lg:ml-64 flex flex-col overflow-hidden">

        <!-- Topbar -->
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 lg:px-8 flex-shrink-0 z-10">
            <div class="flex items-center gap-4">
                <button id="sidebar-toggle" class="lg:hidden p-2 rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div>
                    <h1 class="text-lg font-bold text-navy-900">Voertuigen</h1>
                    <p class="text-xs text-slate-400 hidden sm:block">Beheer en locatiebewaking van het wagenpark</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <!-- Mini stat badges -->
                <div class="hidden sm:flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-full text-xs font-medium">
                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                        <?php echo $statsRaw['beschikbaar']; ?> beschikbaar
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 border border-amber-200 text-amber-700 rounded-full text-xs font-medium">
                        <span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span>
                        <?php echo $statsRaw['in_gebruik']; ?> in gebruik
                    </span>
                    <?php if ($statsRaw['onderhoud'] > 0): ?>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 border border-slate-200 text-slate-600 rounded-full text-xs font-medium">
                        <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span>
                        <?php echo $statsRaw['onderhoud']; ?> onderhoud
                    </span>
                    <?php endif; ?>
                </div>
                <button onclick="openPanel()" class="inline-flex items-center gap-2 bg-accent hover:bg-accent-dark text-white px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 shadow-lg shadow-accent/25">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Toevoegen
                </button>
            </div>
        </header>

        <!-- Flash berichten (absoluut over alles, auto-hide) -->
        <?php if ($success || $error): ?>
        <div id="flash-msg" class="absolute top-20 left-1/2 -translate-x-1/2 z-50 flex items-center gap-3 px-5 py-3 rounded-xl shadow-lg text-sm font-medium
            <?php echo $success ? 'bg-emerald-600 text-white' : 'bg-red-600 text-white'; ?>">
            <?php if ($success): ?>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <?php echo match($success) {
                    'aangemaakt' => 'Voertuig toegevoegd aan het systeem.',
                    'bewerkt'    => 'Voertuig bijgewerkt.',
                    'bijgewerkt' => 'Status bijgewerkt.',
                    'locatie'    => 'Locatie bijgewerkt.',
                    'verwijderd' => 'Voertuig verwijderd.',
                    default      => 'Actie geslaagd.',
                }; ?>
            <?php else: ?>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <?php echo match($error) {
                    'validatie' => 'Vul alle verplichte velden in.',
                    'db'        => 'Databasefout. Probeer opnieuw.',
                    default     => 'Er is een fout opgetreden.',
                }; ?>
            <?php endif; ?>
        </div>
        <script>setTimeout(() => { const el = document.getElementById('flash-msg'); if(el) el.style.display='none'; }, 3500);</script>
        <?php endif; ?>

        <!-- Twee-panel layout: lijst links, kaart rechts -->
        <div class="flex-1 flex overflow-hidden">

            <!-- Voertuigenlijst -->
            <div class="w-80 flex-shrink-0 border-r border-slate-200 flex flex-col bg-white overflow-hidden">

                <!-- Zoek + filter -->
                <div class="p-3 border-b border-slate-100">
                    <div class="relative mb-2">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" id="search-vehicles" placeholder="Zoek voertuig…"
                            class="w-full pl-9 pr-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-all">
                    </div>
                    <!-- Status filter pills -->
                    <div class="flex gap-1.5">
                        <button data-filter="all"     class="filter-pill active px-3 py-1.5 rounded-lg text-xs font-medium transition-all bg-navy-900 text-white">Alle <span class="opacity-70"><?php echo $statsRaw['totaal']; ?></span></button>
                        <button data-filter="beschikbaar" class="filter-pill px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-slate-600 hover:bg-slate-100">✅ <?php echo $statsRaw['beschikbaar']; ?></button>
                        <button data-filter="in_gebruik"  class="filter-pill px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-slate-600 hover:bg-slate-100">🟡 <?php echo $statsRaw['in_gebruik']; ?></button>
                        <button data-filter="onderhoud"   class="filter-pill px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-slate-600 hover:bg-slate-100">⚙️ <?php echo $statsRaw['onderhoud']; ?></button>
                    </div>
                </div>

                <!-- Lijst -->
                <div id="vehicle-list" class="flex-1 overflow-y-auto divide-y divide-slate-50">
                    <?php if (empty($voertuigen)): ?>
                        <div class="flex flex-col items-center justify-center h-full text-slate-400 p-8 text-center">
                            <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 17h.01M16 17h.01M3 11l1.5-5A2 2 0 016.4 4.5h11.2a2 2 0 011.9 1.5L21 11M3 11v6a1 1 0 001 1h1m16-7v6a1 1 0 01-1 1h-1M3 11h18"/></svg>
                            <p class="text-sm font-medium">Geen voertuigen</p>
                            <button onclick="openPanel()" class="mt-3 text-xs text-accent hover:text-accent-dark font-medium">Eerste toevoegen →</button>
                        </div>
                    <?php else: ?>
                        <?php foreach ($voertuigen as $v):
                            $statusCfg = match($v['status']) {
                                'beschikbaar' => ['dot' => 'bg-emerald-500', 'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'label' => 'Beschikbaar'],
                                'in_gebruik'  => ['dot' => 'bg-amber-500',   'badge' => 'bg-amber-50 text-amber-700 border-amber-200',       'label' => 'In gebruik'],
                                'onderhoud'   => ['dot' => 'bg-slate-400',   'badge' => 'bg-slate-100 text-slate-600 border-slate-200',       'label' => 'Onderhoud'],
                                default       => ['dot' => 'bg-slate-400',   'badge' => 'bg-slate-100 text-slate-500 border-slate-200',       'label' => ucfirst($v['status'])],
                            };
                        ?>
                            <div class="vehicle-item p-3.5 hover:bg-slate-50 transition-colors cursor-pointer group"
                                 data-id="<?php echo $v['id']; ?>"
                                 data-status="<?php echo $v['status']; ?>"
                                 data-naam="<?php echo htmlspecialchars($v['naam'], ENT_QUOTES); ?>"
                                 onclick="flyToVehicle(<?php echo $v['id']; ?>, <?php echo $v['lat']; ?>, <?php echo $v['lng']; ?>)">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex items-center gap-2.5 flex-1 min-w-0">
                                        <!-- Status dot -->
                                        <div class="relative flex-shrink-0 mt-0.5">
                                            <div class="w-9 h-9 bg-navy-50 rounded-xl flex items-center justify-center">
                                                <svg class="w-5 h-5 text-navy-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 17h.01M16 17h.01M3 11l1.5-5A2 2 0 016.4 4.5h11.2a2 2 0 011.9 1.5L21 11M3 11v6a1 1 0 001 1h1m16-7v6a1 1 0 01-1 1h-1M3 11h18"/></svg>
                                            </div>
                                            <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full border-2 border-white <?php echo $statusCfg['dot']; ?>"></span>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-navy-900 truncate"><?php echo htmlspecialchars($v['naam'], ENT_QUOTES, 'UTF-8'); ?></p>
                                            <p class="text-xs text-slate-500 truncate"><?php echo htmlspecialchars($v['type'], ENT_QUOTES, 'UTF-8'); ?>
                                                <?php if ($v['kenteken']): ?>
                                                    · <span class="font-mono"><?php echo htmlspecialchars($v['kenteken'], ENT_QUOTES, 'UTF-8'); ?></span>
                                                <?php endif; ?>
                                            </p>
                                            <?php if ($v['locatie_naam']): ?>
                                                <p class="text-xs text-slate-400 truncate mt-0.5 flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                                    <?php echo htmlspecialchars($v['locatie_naam'], ENT_QUOTES, 'UTF-8'); ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <!-- Edit + delete knoppen (verschijnen on hover) -->
                                    <div class="flex items-center gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0" onclick="event.stopPropagation()">
                                        <button type="button" onclick="editVehicle(<?php echo $v['id']; ?>)"
                                            class="p-1.5 rounded-lg text-slate-300 hover:text-accent hover:bg-accent/10 transition-colors"
                                            title="Bewerken">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </button>
                                        <form method="POST" action="/actions/vehicle_action.php" class="inline"
                                              onsubmit="event.stopPropagation(); return confirm('<?php echo htmlspecialchars($v['naam'], ENT_QUOTES); ?> verwijderen?')">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $v['id']; ?>">
                                            <button type="submit" class="p-1.5 rounded-lg text-slate-300 hover:text-red-500 hover:bg-red-50 transition-colors"
                                                title="Verwijderen">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <!-- Status wissel pills -->
                                <div class="flex gap-1.5 mt-2.5" onclick="event.stopPropagation()">
                                    <!-- Emojis gebruikt maar zien er niet zo leuk uit -->
                                    <?php foreach (['beschikbaar' => '✅', 'in_gebruik' => '🟡', 'onderhoud' => '⚙️'] as $st => $emoji): ?>
                                        <?php if ($st !== $v['status']): ?>
                                            <form method="POST" action="/actions/vehicle_action.php" class="inline">
                                                <input type="hidden" name="action" value="update_status">
                                                <input type="hidden" name="id" value="<?php echo $v['id']; ?>">
                                                <input type="hidden" name="status" value="<?php echo $st; ?>">
                                                <button type="submit"
                                                    class="px-2 py-1 rounded-lg text-xs text-slate-500 hover:bg-emerald-200 transition-colors border border-slate-200">
                                                    <?php echo $emoji; ?> <?php echo ucfirst(str_replace('_', ' ', $st)); ?>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Footer teller -->
                <div class="px-4 py-2.5 border-t border-slate-100 text-xs text-slate-400 flex-shrink-0">
                    <?php echo $statsRaw['totaal']; ?> voertuig<?php echo $statsRaw['totaal'] !== 1 ? 'en' : ''; ?> in het systeem
                </div>
            </div>

            <!-- Leaflet kaart -->
            <div class="flex-1 relative isolate z-0">
                <div id="map" class="absolute inset-0"></div>

                <!-- Legenda (over de kaart) -->
                <div class="absolute bottom-6 right-6 z-[1000] bg-white rounded-2xl border border-slate-200/80 shadow-lg p-4">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Legenda</p>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2.5">
                            <span class="w-3 h-3 rounded-full bg-emerald-500 flex-shrink-0"></span>
                            <span class="text-xs text-slate-600">Beschikbaar</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <span class="w-3 h-3 rounded-full bg-amber-500 flex-shrink-0"></span>
                            <span class="text-xs text-slate-600">Bezig met melding</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <span class="w-3 h-3 rounded-full bg-slate-400 flex-shrink-0"></span>
                            <span class="text-xs text-slate-600">Onbeschikbaar</span>
                        </div>
                    </div>
                </div>

                <!-- Placement mode banner -->
                <div id="placement-banner" class="hidden absolute top-4 left-1/2 -translate-x-1/2 z-[1000] bg-navy-900 text-white px-5 py-2.5 rounded-xl shadow-xl text-sm font-medium flex items-center gap-2">
                    <svg class="w-4 h-4 text-accent-light" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Klik op de kaart om de locatie in te stellen
                    <button onclick="cancelPlacement()" class="ml-2 text-white/60 hover:text-white">✕</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Voertuig toevoegen -->
<div id="panel-backdrop" class="fixed inset-0 bg-black/40 z-[1500] hidden" onclick="closePanel()"></div>

<div id="slide-panel" class="fixed right-0 top-0 h-full w-full sm:w-[460px] bg-white shadow-2xl z-[1600] transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col">
    <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 flex-shrink-0">
        <div>
            <h2 id="panel-title" class="text-lg font-bold text-navy-900">Voertuig toevoegen</h2>
            <p id="panel-subtitle" class="text-sm text-slate-400 mt-0.5">Registreer een nieuw voertuig in het systeem</p>
        </div>
        <button onclick="closePanel()" class="p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <form id="vehicle-form" method="POST" action="/actions/vehicle_action.php" class="flex-1 overflow-y-auto px-6 py-6 space-y-5">
        <input type="hidden" name="action" id="form-action" value="create">
        <input type="hidden" name="id"     id="form-id"     value="">
        <input type="hidden" name="lat"    id="form-lat"    value="50.8503">
        <input type="hidden" name="lng"    id="form-lng"    value="4.3517">

        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Naam / Callsign <span class="text-accent">*</span></label>
                <input type="text" name="naam" required placeholder="bv. PW-07"
                    class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-all">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Type <span class="text-accent">*</span></label>
                <div class="relative">
                    <select name="type" required
                        class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-all appearance-none bg-white">
                        <option value="" disabled selected>Kies…</option>
                        <?php foreach ($voertuigTypes as $t): ?>
                            <option value="<?php echo $t; ?>"><?php echo $t; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Kenteken</label>
                <input type="text" name="kenteken" placeholder="1-ABC-234"
                    class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-all font-mono">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Status</label>
            <div class="grid grid-cols-3 gap-2">
                <?php
                $statusOptions = [
                    'beschikbaar' => ['emoji' => '✅', 'label' => 'Beschikbaar', 'color' => 'emerald'],
                    'in_gebruik'  => ['emoji' => '🟡', 'label' => 'Bezig met melding',  'color' => 'amber'],
                    'onderhoud'   => ['emoji' => '⚙️', 'label' => 'Onbeschikbaar',   'color' => 'slate'],
                ];
                foreach ($statusOptions as $val => $cfg): ?>
                    <label class="flex flex-col items-center gap-1.5 p-3 border-2 border-slate-200 rounded-xl cursor-pointer transition-all hover:border-accent/50 has-[:checked]:border-accent has-[:checked]:bg-accent/5">
                        <input type="radio" name="status" value="<?php echo $val; ?>" <?php echo $val === 'beschikbaar' ? 'checked' : ''; ?> class="sr-only">
                        <span class="text-lg"><?php echo $cfg['emoji']; ?></span>
                        <span class="text-xs font-medium text-slate-600"><?php echo $cfg['label']; ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Locatie sectie -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Locatie</label>
            <input type="text" name="locatie_naam" id="form-locatie-naam" placeholder="bv. Brussel Centrum"
                class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-all mb-2">

            <!-- Coördinaten display -->
            <div class="flex items-center gap-2 p-3 bg-slate-50 rounded-xl border border-slate-200">
                <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span id="coords-display" class="text-xs text-slate-500 font-mono flex-1">50.8503, 4.3517 (standaard)</span>
                <button type="button" onclick="startPlacement()"
                    class="flex items-center gap-1.5 px-3 py-1.5 bg-accent text-white rounded-lg text-xs font-medium hover:bg-accent-dark transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5"/></svg>
                    Prik op kaart
                </button>
            </div>
        </div>

        <div class="pt-2 flex gap-3">
            <button type="submit" id="form-submit"
                class="flex-1 inline-flex items-center justify-center gap-2 bg-accent hover:bg-accent-dark text-white py-3 rounded-xl text-sm font-semibold transition-all shadow-lg shadow-accent/25">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span id="form-submit-label">Voertuig registreren</span>
            </button>
            <button type="button" onclick="closePanel()"
                class="px-5 py-3 border border-slate-200 text-slate-600 hover:bg-slate-50 rounded-xl text-sm font-medium transition-colors">
                Annuleren
            </button>
        </div>
    </form>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="/assets/js/app.js"></script>
<script>
// Data uit PHP
const VEHICLES = <?php echo $voertuigenJson; ?>;

// Kaart initialiseren
const map = L.map('map', {
    center: [50.8503, 4.3517],
    zoom: 12,
    zoomControl: false,
});

// Zoom controls rechts boven
L.control.zoom({ position: 'topright' }).addTo(map);

// OpenStreetMap tiles (gratis, geen API key)
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
}).addTo(map);

// Marker icoon genereren op basis van status
function vehicleIcon(status) {
    const colors = {
        beschikbaar: '#10b981',
        in_gebruik:  '#f59e0b',
        onderhoud:   '#94a3b8',
    };
    const color = colors[status] || '#94a3b8';
    const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 17h.01M16 17h.01M3 11l1.5-5A2 2 0 016.4 4.5h11.2a2 2 0 011.9 1.5L21 11M3 11v6a1 1 0 001 1h1m16-7v6a1 1 0 01-1 1h-1M3 11h18"/></svg>`;
    return L.divIcon({
        html: `<div style="width:40px;height:40px;background:${color};border:3px solid white;border-radius:50%;box-shadow:0 3px 10px rgba(0,0,0,0.25);display:flex;align-items:center;justify-content:center;">${svg}</div>`,
        className: '',
        iconSize:   [40, 40],
        iconAnchor: [20, 20],
        popupAnchor:[0, -24],
    });
}

// Popup HTML genereren
function popupHtml(v) {
    const statusLabels = { beschikbaar: 'Beschikbaar', in_gebruik: 'Bezig met melding', onderhoud: 'Onbeschikbaar' };
    const statusColors = {
        beschikbaar: 'background:#d1fae5;color:#065f46;border:1px solid #a7f3d0',
        in_gebruik:  'background:#fef3c7;color:#92400e;border:1px solid #fde68a',
        onderhoud:   'background:#f1f5f9;color:#475569;border:1px solid #e2e8f0',
    };
    const bColor = statusColors[v.status] || statusColors.onderhoud;

    return `
    <div style="font-family:'DM Sans',sans-serif;padding:16px;min-width:200px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
            <div style="width:36px;height:36px;background:#eef2ff;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="1.5"><path d="M8 17h.01M16 17h.01M3 11l1.5-5A2 2 0 016.4 4.5h11.2a2 2 0 011.9 1.5L21 11M3 11v6a1 1 0 001 1h1m16-7v6a1 1 0 01-1 1h-1M3 11h18"/></svg>
            </div>
            <div>
                <p style="font-size:15px;font-weight:700;color:#0a1330;margin:0;">${v.naam}</p>
                <p style="font-size:12px;color:#64748b;margin:0;">${v.type}</p>
            </div>
        </div>
        ${v.kenteken ? `<p style="font-size:12px;color:#64748b;margin:0 0 8px;font-family:monospace;">🪪 ${v.kenteken}</p>` : ''}
        ${v.locatie_naam ? `<p style="font-size:12px;color:#64748b;margin:0 0 10px;">📍 ${v.locatie_naam}</p>` : ''}
        <span style="display:inline-block;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600;${bColor}">
            ${statusLabels[v.status] || v.status}
        </span>
    </div>`;
}

// Markers plaatsen
const markers = {};

VEHICLES.forEach(v => {
    if (v.lat && v.lng) {
        const marker = L.marker([v.lat, v.lng], { icon: vehicleIcon(v.status) })
            .addTo(map)
            .bindPopup(popupHtml(v), { maxWidth: 280 });

        marker.on('click', () => highlightListItem(v.id));
        markers[v.id] = marker;
    }
});

// Kaart centreren op alle markers als er voertuigen zijn
if (VEHICLES.length > 0) {
    const validVehicles = VEHICLES.filter(v => v.lat && v.lng);
    if (validVehicles.length > 0) {
        const group = L.featureGroup(Object.values(markers));
        map.fitBounds(group.getBounds().pad(0.2));
    }
}

// Naar voertuig vliegen
function flyToVehicle(id, lat, lng) {
    if (markers[id]) {
        map.flyTo([lat, lng], 15, { duration: 1 });
        setTimeout(() => markers[id].openPopup(), 900);
        highlightListItem(id);
    }
}

function highlightListItem(id) {
    document.querySelectorAll('.vehicle-item').forEach(el => {
        el.classList.toggle('bg-accent/5', parseInt(el.dataset.id) === id);
        el.classList.toggle('border-l-2', parseInt(el.dataset.id) === id);
        el.classList.toggle('border-accent', parseInt(el.dataset.id) === id);
    });
}

// Zoek en filter
const searchInput = document.getElementById('search-vehicles');
let currentFilter = 'all';

searchInput.addEventListener('input', applyFilters);

document.querySelectorAll('.filter-pill').forEach(btn => {
    btn.addEventListener('click', () => {
        currentFilter = btn.dataset.filter;
        document.querySelectorAll('.filter-pill').forEach(b => {
            b.classList.remove('bg-navy-900', 'text-white');
            b.classList.add('text-slate-600', 'hover:bg-slate-100');
        });
        btn.classList.add('bg-navy-900', 'text-white');
        btn.classList.remove('text-slate-600', 'hover:bg-slate-100');
        applyFilters();
    });
});

function applyFilters() {
    const q = searchInput.value.toLowerCase();
    document.querySelectorAll('.vehicle-item').forEach(el => {
        const naam   = (el.dataset.naam   || '').toLowerCase();
        const status = (el.dataset.status || '');
        const matchQ = naam.includes(q);
        const matchF = currentFilter === 'all' || status === currentFilter;
        el.style.display = (matchQ && matchF) ? '' : 'none';
    });
}

// Locatie-prik modus
let placementMode = false;
let previewMarker = null;

function startPlacement() {
    placementMode = true;
    document.getElementById('map').classList.add('placement-mode');
    document.getElementById('placement-banner').classList.remove('hidden');

    const panel    = document.getElementById('slide-panel');
    const backdrop = document.getElementById('panel-backdrop');
    panel.style.pointerEvents = 'none';
    panel.style.opacity = '0.4';
    backdrop.style.pointerEvents = 'none';
    backdrop.style.opacity = '0';
}

function cancelPlacement() {
    placementMode = false;
    document.getElementById('map').classList.remove('placement-mode');
    document.getElementById('placement-banner').classList.add('hidden');

    const panel    = document.getElementById('slide-panel');
    const backdrop = document.getElementById('panel-backdrop');
    panel.style.pointerEvents = '';
    panel.style.opacity = '';
    backdrop.style.pointerEvents = '';
    backdrop.style.opacity = '';

    if (previewMarker) { map.removeLayer(previewMarker); previewMarker = null; }
}

map.on('click', (e) => {
    if (!placementMode) return;

    const { lat, lng } = e.latlng;
    document.getElementById('form-lat').value = lat.toFixed(7);
    document.getElementById('form-lng').value = lng.toFixed(7);
    document.getElementById('coords-display').textContent = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;

    if (previewMarker) map.removeLayer(previewMarker);
    previewMarker = L.marker([lat, lng], {
        icon: L.divIcon({
            html: `<div style="width:36px;height:36px;background:#3b82f6;border:3px solid white;border-radius:50%;box-shadow:0 3px 10px rgba(59,130,246,0.4);display:flex;align-items:center;justify-content:center;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M12 4v16m8-8H4"/></svg>
            </div>`,
            className: '',
            iconSize: [36, 36],
            iconAnchor: [18, 18],
        })
    }).addTo(map).bindPopup('<div style="padding:8px;font-size:12px;font-family:DM Sans,sans-serif;">📍 Locatie ingesteld</div>').openPopup();

    cancelPlacement();
});

// Slide panel: create / edit dual mode
const VEHICLES_BY_ID = Object.fromEntries(VEHICLES.map(v => [v.id, v]));

function resetVehicleForm() {
    const form = document.getElementById('vehicle-form');
    form.reset();
    document.getElementById('form-id').value   = '';
    document.getElementById('form-lat').value  = '50.8503';
    document.getElementById('form-lng').value  = '4.3517';
    document.getElementById('coords-display').textContent = '50.8503, 4.3517 (standaard)';
    form.querySelector('input[name="status"][value="beschikbaar"]').checked = true;
}

function openPanel() {
    document.getElementById('form-action').value = 'create';
    document.getElementById('panel-title').textContent    = 'Voertuig toevoegen';
    document.getElementById('panel-subtitle').textContent = 'Registreer een nieuw voertuig in het systeem';
    document.getElementById('form-submit-label').textContent = 'Voertuig registreren';
    resetVehicleForm();
    showPanel();
}

function editVehicle(id) {
    const v = VEHICLES_BY_ID[id];
    if (!v) return;

    const form = document.getElementById('vehicle-form');
    document.getElementById('form-action').value = 'update';
    document.getElementById('form-id').value     = v.id;
    document.getElementById('panel-title').textContent    = 'Voertuig bewerken';
    document.getElementById('panel-subtitle').textContent = v.naam;
    document.getElementById('form-submit-label').textContent = 'Wijzigingen opslaan';

    form.elements['naam'].value         = v.naam         ?? '';
    form.elements['type'].value         = v.type         ?? '';
    form.elements['kenteken'].value     = v.kenteken     ?? '';
    form.elements['locatie_naam'].value = v.locatie_naam ?? '';
    document.getElementById('form-lat').value = v.lat;
    document.getElementById('form-lng').value = v.lng;
    document.getElementById('coords-display').textContent =
        `${parseFloat(v.lat).toFixed(5)}, ${parseFloat(v.lng).toFixed(5)}`;

    const statusRadio = form.querySelector(`input[name="status"][value="${v.status}"]`);
    if (statusRadio) statusRadio.checked = true;

    showPanel();
}

function showPanel() {
    document.getElementById('slide-panel').classList.remove('translate-x-full');
    document.getElementById('panel-backdrop').classList.remove('hidden');
}

function closePanel() {
    document.getElementById('slide-panel').classList.add('translate-x-full');
    document.getElementById('panel-backdrop').classList.add('hidden');
    if (previewMarker) { map.removeLayer(previewMarker); previewMarker = null; }
    cancelPlacement();
}

<?php if ($openPanel): ?>openPanel();<?php endif; ?>
</script>
</body>
</html>
