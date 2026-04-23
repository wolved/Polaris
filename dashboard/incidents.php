<?php
/**
 * Meldingen
 * Volledig overzicht van alle meldingen met aanmaken, statuswijziging en verwijderen.
 */

session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

require_once '../includes/db.php';

$pageTitle = 'Meldingen';

// Flash-berichten
$success = $_GET['success'] ?? null;
$error   = $_GET['error']   ?? null;

// Status-filter
$filter = in_array($_GET['status'] ?? '', ['open', 'in_progress', 'closed']) ? $_GET['status'] : 'all';

// Aantallen per status voor de filterbadges
$counts = ['all' => 0, 'open' => 0, 'in_progress' => 0, 'closed' => 0];
foreach (getPDO()->query("SELECT status, COUNT(*) AS cnt FROM reports GROUP BY status")->fetchAll() as $r) {
    $counts[$r['status']] = (int) $r['cnt'];
    $counts['all'] += (int) $r['cnt'];
}

// Meldingen ophalen (met filter)
if ($filter === 'all') {
    $stmt = getPDO()->query("SELECT reports.*, users.full_name AS created_by FROM reports JOIN users ON reports.created_by = users.id ORDER BY created_at DESC");
} else {
    $stmt = getPDO()->prepare("SELECT reports.*, users.full_name AS created_by FROM reports JOIN users ON reports.created_by = users.id WHERE status = ? ORDER BY created_at DESC");
    $stmt->execute([$filter]);
}
$meldingen = $stmt->fetchAll();

// Slide-panel direct openen als ?nieuw=1 meegegeven is
$openPanel = isset($_GET['nieuw']);

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
                    <h1 class="text-lg font-bold text-navy-900">Meldingen</h1>
                    <p class="text-xs text-slate-400 hidden sm:block">Beheer en opvolging van alle incidenten</p>
                </div>
            </div>
            <button onclick="openPanel()" class="inline-flex items-center gap-2 bg-accent hover:bg-accent-dark text-white px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 shadow-lg shadow-accent/25 hover:shadow-accent/40">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nieuwe melding
            </button>
        </header>

        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-slate-50">

            <!-- Flash berichten -->
            <?php if ($success): ?>
                <div class="mb-5 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3 text-sm">
                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <?php echo match($success) {
                        'aangemaakt'  => 'Melding succesvol aangemaakt.',
                        'bijgewerkt'  => 'Status succesvol bijgewerkt.',
                        'verwijderd'  => 'Melding verwijderd.',
                        default       => 'Actie geslaagd.',
                    }; ?>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <?php echo match($error) {
                        'validatie'  => 'Vul alle verplichte velden in.',
                        'wachtwoord' => 'Wachtwoord moet minimaal 6 tekens bevatten.',
                        'bestaat'    => 'Gebruikersnaam bestaat al.',
                        'db'         => 'Databasefout. Probeer opnieuw.',
                        default      => 'Er is een fout opgetreden.',
                    }; ?>
                </div>
            <?php endif; ?>

            <!-- Filter tabs + count -->
            <div class="bg-white rounded-2xl border border-slate-200/80 mb-6">
                <div class="flex items-center gap-1 p-3 overflow-x-auto">
                    <?php
                    $tabs = [
                        'all'            => ['label' => 'Alle',          'color' => 'slate'],
                        'open'           => ['label' => 'Open',          'color' => 'red'],
                        'in_progress' => ['label' => 'In behandeling','color' => 'amber'],
                        'closed'       => ['label' => 'Gesloten',      'color' => 'emerald'],
                    ];
                    foreach ($tabs as $key => $tab):
                        $isActive = $filter === $key;
                        $activeClass = $isActive
                            ? 'bg-navy-900 text-white'
                            : 'text-slate-600 hover:bg-slate-100';
                    ?>
                        <a href="?status=<?php echo $key; ?>"
                           class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium whitespace-nowrap transition-all duration-200 <?php echo $activeClass; ?>">
                            <?php echo $tab['label']; ?>
                            <span class="text-xs font-bold px-1.5 py-0.5 rounded-full <?php echo $isActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500'; ?>">
                                <?php echo $counts[$key] ?? 0; ?>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Tabel -->
            <div class="bg-white rounded-2xl border border-slate-200/80">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Soort</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider hidden md:table-cell">Locatie</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider hidden lg:table-cell">Aangemeld door</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Datum</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider hidden lg:table-cell">Prioriteit</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Acties</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (empty($meldingen)): ?>
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center gap-2 text-slate-400">
                                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                            <p class="text-sm font-medium">Geen meldingen gevonden</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($meldingen as $m):
                                    $ref = 'MELD-' . date('Y', strtotime($m['created_at'])) . '-' . str_pad($m['id'], 4, '0', STR_PAD_LEFT);
                                    $statusLabel = normaliseerStatus($m['status']);
                                    $prioLabel   = normaliseerPrioriteit($m['priority']);
                                    $statusKleur = match($statusLabel) {
                                        'Open'           => 'bg-red-50 text-red-700 border-red-200',
                                        'In behandeling' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'Afgerond'       => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        default          => 'bg-slate-50 text-slate-600 border-slate-200',
                                    };
                                    $prioKleur = match($prioLabel) {
                                        'Hoog'     => 'text-red-600',
                                        'Gemiddeld' => 'text-amber-600',
                                        default    => 'text-slate-400',
                                    };
                                ?>
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-4 text-xs font-mono font-medium text-navy-900 whitespace-nowrap"><?php echo $ref; ?></td>
                                        <td class="px-6 py-4 text-sm font-medium text-slate-800"><?php echo htmlspecialchars($m['title'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="px-6 py-4 text-sm text-slate-500 hidden md:table-cell max-w-[200px] truncate"><?php echo htmlspecialchars($m['locatie'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="px-6 py-4 text-sm text-slate-500 hidden lg:table-cell"><?php echo htmlspecialchars($m['created_by'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="px-6 py-4 text-sm text-slate-500 whitespace-nowrap"><?php echo date('d M Y H:i', strtotime($m['created_at'])); ?></td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border <?php echo $statusKleur; ?>">
                                                <?php echo $statusLabel; ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 hidden lg:table-cell">
                                            <span class="text-xs font-medium <?php echo $prioKleur; ?>"><?php echo $prioLabel; ?></span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <!-- Snelle statuswissel -->
                                                <?php if ($m['status'] !== 'in_progress'): ?>
                                                    <form method="POST" action="/actions/report_action.php" class="inline">
                                                        <input type="hidden" name="action" value="update_status">
                                                        <input type="hidden" name="id" value="<?php echo $m['id']; ?>">
                                                        <input type="hidden" name="status" value="in_progress">
                                                        <input type="hidden" name="redirect" value="/dashboard/incidents.php?status=<?php echo $filter; ?>">
                                                        <button type="submit" title="Zet op In behandeling"
                                                            class="p-1.5 rounded-lg text-amber-500 hover:bg-amber-50 transition-colors">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                <?php if ($m['status'] !== 'closed'): ?>
                                                    <form method="POST" action="/actions/report_action.php" class="inline">
                                                        <input type="hidden" name="action" value="update_status">
                                                        <input type="hidden" name="id" value="<?php echo $m['id']; ?>">
                                                        <input type="hidden" name="status" value="closed">
                                                        <input type="hidden" name="redirect" value="/dashboard/incidents.php?status=<?php echo $filter; ?>">
                                                        <button type="submit" title="Markeer als gesloten"
                                                            class="p-1.5 rounded-lg text-emerald-500 hover:bg-emerald-50 transition-colors">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                <!-- Verwijderen -->
                                                <form method="POST" action="/actions/report_action.php" class="inline"
                                                      onsubmit="return confirm('Melding <?php echo $ref; ?> verwijderen?')">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo $m['id']; ?>">
                                                    <button type="submit" title="Verwijderen"
                                                        class="p-1.5 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="h-4"></div>
        </main>
    </div>
</div>

<!-- SLIDE PANEL: Nieuwe melding -->
<div id="panel-backdrop" class="fixed inset-0 bg-black/40 z-40 hidden transition-opacity duration-300" onclick="closePanel()"></div>

<div id="slide-panel" class="fixed right-0 top-0 h-full w-full sm:w-[480px] bg-white shadow-2xl z-50 transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col">
    <!-- Panel header -->
    <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 flex-shrink-0">
        <div>
            <h2 class="text-lg font-bold text-navy-900">Nieuwe melding</h2>
            <p class="text-sm text-slate-400 mt-0.5">Registreer een nieuw incident in het systeem</p>
        </div>
        <button onclick="closePanel()" class="p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <!-- Formulier -->
    <form method="POST" action="/actions/report_action.php" class="flex-1 overflow-y-auto px-6 py-6 space-y-5">
        <input type="hidden" name="action" value="create">

        <!-- Soort incident -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                Soort incident <span class="text-accent">*</span>
            </label>
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

        <!-- Locatie -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                Locatie <span class="text-accent">*</span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <input type="text" name="locatie" required placeholder="Straat, huisnummer, gemeente"
                    class="w-full pl-11 pr-4 py-3 border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-all duration-200">
            </div>
        </div>

        <!-- Prioriteit + Status naast elkaar -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Prioriteit</label>
                <select name="priority"
                    class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-all duration-200 appearance-none bg-white">
                    <option value="hoog">🔴 Hoog</option>
                    <option value="gemiddeld" selected>🟡 Gemiddeld</option>
                    <option value="laag">🟢 Laag</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Status</label>
                <select name="status"
                    class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-all duration-200 appearance-none bg-white">
                    <option value="open" selected>Open</option>
                    <option value="in_behandeling">In behandeling</option>
                    <option value="afgerond">Afgerond</option>
                </select>
            </div>
        </div>

        <!-- Beschrijving -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Beschrijving</label>
            <textarea name="description" rows="4" placeholder="Extra details over het incident…"
                class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-all duration-200 resize-none"></textarea>
        </div>

        <!-- Aangemeld door (readonly, uit sessie) -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Aangemeld door</label>
            <input type="text" value="<?php echo htmlspecialchars($_SESSION['user_full_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" readonly
                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-500 cursor-not-allowed">
        </div>

        <!-- Acties -->
        <div class="pt-2 flex gap-3">
            <button type="submit"
                class="flex-1 inline-flex items-center justify-center gap-2 bg-accent hover:bg-accent-dark text-white py-3 rounded-xl text-sm font-semibold transition-all duration-200 shadow-lg shadow-accent/25">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Melding registreren
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
// Direct openen als ?nieuw=1 in de URL staat
<?php if ($openPanel): ?>openPanel();<?php endif; ?>
</script>
</body>
</html>
