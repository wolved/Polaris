<?php
/**
 * Verslagen
 * Overzicht van formele verslagen op basis van meldingen.
 * Nieuwe verslagen worden aangemaakt als een melding met status afgerond
 * zodat je ze nog opnieuw kan lezen
 */

session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

require_once '../includes/db.php';

$pageTitle = 'Verslagen';

$success = $_GET['success'] ?? null;
$error   = $_GET['error']   ?? null;
$openPanel = isset($_GET['nieuw']);

// Verslagen = alle afgeronde meldingen + alle andere meldingen, gesorteerd op datum
// Filter op periode
$periode = $_GET['periode'] ?? 'all';

$whereClauses = [];
$params = [];

if ($periode === 'vandaag') {
    $whereClauses[] = "DATE(created_at) = CURDATE()";
} elseif ($periode === 'week') {
    $whereClauses[] = "created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
} elseif ($periode === 'maand') {
    $whereClauses[] = "created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
}

// Optioneel zoeken
$zoek = trim($_GET['q'] ?? '');
if ($zoek !== '') {
    $whereClauses[] = "(title LIKE ? OR locatie LIKE ? OR description LIKE ?)";
    $params = array_merge($params, ["%$zoek%", "%$zoek%", "%$zoek%"]);
}

$where = $whereClauses ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

$stmt = getPDO()->prepare("SELECT * FROM reports $where ORDER BY created_at DESC");
$stmt->execute($params);
$verslagen = $stmt->fetchAll();

// Statistieken voor de header
$stats = getPDO()->query("SELECT
    COUNT(*) AS totaal,
    SUM(status = 'closed') AS closed,
    SUM(status = 'open') AS open,
    SUM(status = 'in_progress') AS in_progress
FROM reports")->fetch();

$incidentTypes = ['Inbraak', 'Verkeersongeval', 'Diefstal', 'Vandalisme', 'Geluidsoverlast',
                  'Vechtpartij', 'Brandstichting', 'Fraude', 'Drugsgerelateerd', 'Overig'];
?>

<?php include '../includes/header.php'; ?>

<div class="flex h-screen overflow-hidden">

    <?php include '../includes/sidebar.php'; ?>

    <div class="flex-1 lg:ml-64 flex flex-col overflow-hidden">

        <!-- Topbar -->
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 lg:px-8 flex-shrink-0">
            <div class="flex items-center gap-4">
                <button id="sidebar-toggle" class="lg:hidden p-2 rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-colors" aria-label="Sidebar openen">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div>
                    <h1 class="text-lg font-bold text-navy-900">Verslagen</h1>
                    <p class="text-xs text-slate-400 hidden sm:block">Alle geregistreerde incidentverslagen</p>
                </div>
            </div>
            <button onclick="openPanel()" class="inline-flex items-center gap-2 bg-accent hover:bg-accent-dark text-white px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 shadow-lg shadow-accent/25">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Verslag opstellen
            </button>
        </header>

        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-slate-50">

            <!-- Flash berichten -->
            <?php if ($success): ?>
                <div class="mb-5 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3 text-sm">
                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <?php echo match($success) {
                        'aangemaakt' => 'Verslag succesvol geregistreerd.',
                        'verwijderd' => 'Verslag verwijderd.',
                        default      => 'Actie geslaagd.',
                    }; ?>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <?php echo match($error) {
                        'validatie' => 'Vul alle verplichte velden in.',
                        'db'        => 'Databasefout. Probeer opnieuw.',
                        default     => 'Er is een fout opgetreden.',
                    }; ?>
                </div>
            <?php endif; ?>

            <!-- Statkaarten -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                <?php
                $statCards = [
                    ['label' => 'Totaal', 'value' => $stats['totaal'], 'kleur' => 'navy', 'bg' => 'bg-navy-50', 'text' => 'text-navy-700'],
                    ['label' => 'Open', 'value' => $stats['open'], 'bg' => 'bg-red-50', 'text' => 'text-red-700'],
                    ['label' => 'In behandeling', 'value' => $stats['in_progress'], 'bg' => 'bg-amber-50', 'text' => 'text-amber-700'],
                    ['label' => 'Afgerond', 'value' => $stats['closed'], 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-700'],
                ];
                foreach ($statCards as $card): ?>
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-4">
                        <div class="w-9 h-9 <?php echo $card['bg']; ?> rounded-xl flex items-center justify-center mb-3">
                            <span class="text-sm font-bold <?php echo $card['text']; ?>"><?php echo $card['value']; ?></span>
                        </div>
                        <p class="text-md font-medium text-navy-900"><?php echo $card['label']; ?></p>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Filter + Zoek -->
            <div class="bg-white rounded-2xl border border-slate-200/80 mb-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 p-3">
                    <!-- Periode filter -->
                    <div class="flex items-center gap-1">
                        <?php
                        $periodes = ['all' => 'Alle', 'vandaag' => 'Vandaag', 'week' => '7 dagen', 'maand' => '30 dagen'];
                        foreach ($periodes as $key => $label):
                            $isActive = $periode === $key;
                            $q = $zoek ? '&q=' . urlencode($zoek) : '';
                        ?>
                            <a href="?periode=<?php echo $key; ?><?php echo $q; ?>"
                               class="px-3 py-2 rounded-xl text-sm font-medium whitespace-nowrap transition-all duration-200 <?php echo $isActive ? 'bg-navy-900 text-white' : 'text-slate-600 hover:bg-slate-100'; ?>">
                                <?php echo $label; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <!-- Zoek -->
                    <form method="GET" class="flex-1 flex items-center gap-2 sm:ml-auto">
                        <input type="hidden" name="periode" value="<?php echo $periode; ?>">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" name="q" value="<?php echo htmlspecialchars($zoek, ENT_QUOTES, 'UTF-8'); ?>"
                                placeholder="Zoek op type, locatie of beschrijving…"
                                class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-all duration-200">
                        </div>
                        <button type="submit" class="px-3 py-2 bg-navy-900 text-white rounded-xl text-sm font-medium transition-colors hover:bg-navy-800">Zoeken</button>
                        <?php if ($zoek): ?>
                            <a href="?periode=<?php echo $periode; ?>" class="px-3 py-2 border border-slate-200 text-slate-600 hover:bg-slate-50 rounded-xl text-sm font-medium">✕</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- Tabel / Kaarten -->
            <div class="space-y-3">
                <?php if (empty($verslagen)): ?>
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-12 text-center">
                        <div class="flex flex-col items-center gap-3 text-slate-400">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p class="text-sm font-medium">Geen verslagen gevonden</p>
                            <button onclick="openPanel()" class="mt-2 text-sm text-accent hover:text-accent-dark font-medium transition-colors">
                                Eerste verslag opstellen →
                            </button>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($verslagen as $v):
                        $ref = 'VRSL-' . date('Y', strtotime($v['created_at'])) . '-' . str_pad($v['id'], 4, '0', STR_PAD_LEFT);
                        $statusLabel = normaliseerStatus($v['status']);
                        $prioLabel   = normaliseerPrioriteit($v['priority']);
                        $statusKleur = match($statusLabel) {
                            'Open'           => 'bg-red-50 text-red-700 border-red-200',
                            'In behandeling' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'Afgerond'       => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            default          => 'bg-slate-50 text-slate-600 border-slate-200',
                        };
                    ?>
                        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 hover:shadow-md transition-all duration-200">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-start gap-4 flex-1 min-w-0">
                                    <!-- Icoon -->
                                    <div class="w-10 h-10 bg-navy-50 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <svg class="w-5 h-5 text-navy-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-3 flex-wrap mb-1">
                                            <span class="font-mono text-xs text-slate-400"><?php echo $ref; ?></span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border <?php echo $statusKleur; ?>"><?php echo $statusLabel; ?></span>
                                            <span class="text-xs text-slate-400"><?php
                                                echo match($prioLabel) {
                                                    'Hoog'     => '🔴 Hoog',
                                                    'Gemiddeld' => '🟡 Gemiddeld',
                                                    'Laag'     => '🟢 Laag',
                                                    default    => $prioLabel,
                                                };
                                            ?></span>
                                        </div>
                                        <h3 class="text-sm font-semibold text-navy-900 truncate">
                                            <?php echo htmlspecialchars($v['title'], ENT_QUOTES, 'UTF-8'); ?>
                                        </h3>
                                        <div class="flex items-center gap-4 mt-1.5 flex-wrap">
                                            <span class="flex items-center gap-1 text-xs text-slate-500">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                <?php echo htmlspecialchars($v['locatie'], ENT_QUOTES, 'UTF-8'); ?>
                                            </span>
                                            <?php if (!empty($v['created_by'])): ?>
                                                <span class="flex items-center gap-1 text-xs text-slate-500">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                    <?php echo htmlspecialchars($v['created_by'], ENT_QUOTES, 'UTF-8'); ?>
                                                </span>
                                            <?php endif; ?>
                                            <span class="flex items-center gap-1 text-xs text-slate-400">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <?php echo date('d M Y H:i', strtotime($v['created_at'])); ?>
                                            </span>
                                        </div>
                                        <?php if (!empty($v['description'])): ?>
                                            <p class="mt-2 text-sm text-slate-500 line-clamp-2">
                                                <?php echo htmlspecialchars($v['description'], ENT_QUOTES, 'UTF-8'); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <!-- Acties -->
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <?php if ($v['status'] !== 'closed'): ?>
                                        <form method="POST" action="/actions/report_action.php" class="inline">
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="id" value="<?php echo $v['id']; ?>">
                                            <input type="hidden" name="status" value="closed">
                                            <input type="hidden" name="redirect" value="/dashboard/reports.php">
                                            <button type="submit" title="Markeer als gesloten"
                                                class="p-2 rounded-xl text-emerald-600 hover:bg-emerald-50 border border-emerald-200 transition-colors text-xs font-medium flex items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                <span class="hidden sm:inline">Sluiten</span>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <form method="POST" action="/actions/report_action.php" class="inline"
                                          onsubmit="return confirm('Verslag <?php echo $ref; ?> verwijderen?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $v['id']; ?>">
                                        <button type="submit" title="Verwijderen"
                                            class="p-2 rounded-xl text-slate-400 hover:text-red-500 hover:bg-red-50 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="h-4"></div>
        </main>
    </div>
</div>

<!-- Verslag opstellen -->
<div id="panel-backdrop" class="fixed inset-0 bg-black/40 z-40 hidden" onclick="closePanel()"></div>

<div id="slide-panel" class="fixed right-0 top-0 h-full w-full sm:w-[480px] bg-white shadow-2xl z-50 transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col">
    <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 flex-shrink-0">
        <div>
            <h2 class="text-lg font-bold text-navy-900">Verslag opstellen</h2>
            <p class="text-sm text-slate-400 mt-0.5">Registreer een nieuw incidentverslag</p>
        </div>
        <button onclick="closePanel()" class="p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <form method="POST" action="/actions/report_action.php" class="flex-1 overflow-y-auto px-6 py-6 space-y-5">
        <input type="hidden" name="action" value="create">

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Soort incident <span class="text-accent">*</span></label>
            <div class="relative">
                <select name="title" required
                    class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-all duration-200 appearance-none bg-white">
                    <option value="" disabled selected>Kies een type…</option>
                    <?php foreach ($incidentTypes as $t): ?>
                        <option value="<?php echo $t; ?>"><?php echo $t; ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Locatie <span class="text-accent">*</span></label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <input type="text" name="locatie" required placeholder="Straat, huisnummer, gemeente"
                    class="w-full pl-11 pr-4 py-3 border border-slate-300 rounded-xl text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-all duration-200">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Prioriteit</label>
                <select name="priority"
                    class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-all duration-200 appearance-none bg-white">
                    <option value="high">🔴 Hoog</option>
                    <option value="medium" selected>🟡 Gemiddeld</option>
                    <option value="low">🟢 Laag</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Status</label>
                <select name="status"
                    class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-all duration-200 appearance-none bg-white">
                    <option value="open">Open</option>
                    <option value="in_progress">In behandeling</option>
                    <option value="closed" selected>Gesloten</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Verslag / beschrijving <span class="text-accent">*</span></label>
            <textarea name="description" rows="5" required placeholder="Beschrijf het incident in detail…"
                class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-all duration-200 resize-none"></textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Opgesteld door</label>
            <input type="text" value="<?php echo htmlspecialchars($_SESSION['user_full_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" readonly
                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-500 cursor-not-allowed">
        </div>

        <div class="pt-2 flex gap-3">
            <button type="submit"
                class="flex-1 inline-flex items-center justify-center gap-2 bg-accent hover:bg-accent-dark text-white py-3 rounded-xl text-sm font-semibold transition-all duration-200 shadow-lg shadow-accent/25">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Verslag opslaan
            </button>
            <button type="button" onclick="closePanel()"
                class="px-5 py-3 border border-slate-200 text-slate-600 hover:bg-slate-50 rounded-xl text-sm font-medium transition-colors">
                Annuleren
            </button>
        </div>
    </form>
</div>

<script src="/assets/js/app.js"></script>
<script>
    function openPanel() {
        document.getElementById('slide-panel').classList.remove('translate-x-full');
        document.getElementById('panel-backdrop').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closePanel() {
        document.getElementById('slide-panel').classList.add('translate-x-full');
        document.getElementById('panel-backdrop').classList.add('hidden');
        document.body.style.overflow = '';
    }
    <?php if ($openPanel): ?>openPanel();<?php endif; ?>
</script>

</body>
</html>
