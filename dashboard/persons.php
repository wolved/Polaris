<?php
/**
 * Personen
 * Overzicht van alle systeemgebruikers/personen met zoekfunctie en toevoegen.
 */

session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

require_once '../includes/db.php';

$pageTitle = 'Personen';

$success = $_GET['success'] ?? null;
$error   = $_GET['error']   ?? null;
$openPanel = isset($_GET['nieuw']);

// Zoekterm
$zoek = trim($_GET['q'] ?? '');

// Personen ophalen (met optionele zoekterm)
if ($zoek !== '') {
    $stmt = getPDO()->prepare(
        "SELECT id, full_name, username, role, created_at
         FROM users
         WHERE full_name LIKE ? OR username LIKE ?
         ORDER BY full_name ASC"
    );
    $stmt->execute(["%$zoek%", "%$zoek%"]);
} else {
    $stmt = getPDO()->query(
        "SELECT id, full_name, username, role, created_at
         FROM users
         ORDER BY full_name ASC"
    );
}
$personen = $stmt->fetchAll();

$rollen = ['admin', 'meldkamer', 'agent'];
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
                    <h1 class="text-lg font-bold text-navy-900">Personen</h1>
                    <p class="text-xs text-slate-400 hidden sm:block">Systeemgebruikers en personeelsbeheer</p>
                </div>
            </div>
            <button onclick="openPanel()" class="inline-flex items-center gap-2 bg-accent hover:bg-accent-dark text-white px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 shadow-lg shadow-accent/25">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Persoon toevoegen
            </button>
        </header>

        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-slate-50">

            <!-- Flash berichten -->
            <?php if ($success): ?>
                <div class="mb-5 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3 text-sm">
                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <?php echo match($success) {
                        'aangemaakt' => 'Persoon succesvol toegevoegd.',
                        'verwijderd' => 'Persoon verwijderd uit het systeem.',
                        default      => 'Actie geslaagd.',
                    }; ?>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <?php echo match($error) {
                        'validatie'  => 'Vul alle verplichte velden in.',
                        'wachtwoord' => 'Wachtwoord moet minimaal 6 tekens bevatten.',
                        'bestaat'    => 'Gebruikersnaam bestaat al. Kies een andere.',
                        'db'         => 'Databasefout. Probeer opnieuw.',
                        default      => 'Er is een fout opgetreden.',
                    }; ?>
                </div>
            <?php endif; ?>

            <!-- Zoekbalk + teller -->
            <div class="bg-white rounded-2xl border border-slate-200/80 mb-6">
                <form method="GET" class="flex items-center gap-3 p-3">
                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" name="q" value="<?php echo htmlspecialchars($zoek, ENT_QUOTES, 'UTF-8'); ?>"
                            placeholder="Zoek op naam of gebruikersnaam…"
                            class="w-full pl-11 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-all duration-200">
                    </div>
                    <button type="submit" class="px-4 py-2.5 bg-navy-900 hover:bg-navy-800 text-white rounded-xl text-sm font-medium transition-colors">Zoeken</button>
                    <?php if ($zoek): ?>
                        <a href="/dashboard/persons.php" class="px-4 py-2.5 border border-slate-200 text-slate-600 hover:bg-slate-50 rounded-xl text-sm font-medium transition-colors">Wissen</a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Tabel -->
            <div class="bg-white rounded-2xl border border-slate-200/80">
                <?php if ($zoek): ?>
                    <div class="px-6 py-3 border-b border-slate-100 text-sm text-slate-500">
                        <?php echo count($personen); ?> resultaat<?php echo count($personen) !== 1 ? 'en' : ''; ?> voor "<strong><?php echo htmlspecialchars($zoek, ENT_QUOTES, 'UTF-8'); ?></strong>"
                    </div>
                <?php endif; ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Naam</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider hidden md:table-cell">Gebruikersnaam</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Rol</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider hidden lg:table-cell">Aangemeld op</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Acties</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (empty($personen)): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center gap-2 text-slate-400">
                                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            <p class="text-sm font-medium">Geen personen gevonden</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($personen as $p):
                                    $naamDelen = explode(' ', trim($p['full_name']));
                                    $init = strtoupper(($naamDelen[0][0] ?? 'U') . (count($naamDelen) > 1 ? end($naamDelen)[0] : ''));
                                    $isZelf = (int)$p['id'] === (int)$_SESSION['user_id'];
                                ?>
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-4 text-xs font-mono text-slate-400">#<?php echo $p['id']; ?></td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 bg-accent/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                                    <span class="text-accent text-xs font-bold"><?php echo $init; ?></span>
                                                </div>
                                                <span class="text-sm font-medium text-navy-900">
                                                    <?php echo htmlspecialchars($p['full_name'], ENT_QUOTES, 'UTF-8'); ?>
                                                    <?php if ($isZelf): ?>
                                                        <span class="ml-1.5 text-xs text-accent font-normal">(jij)</span>
                                                    <?php endif; ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-500 font-mono hidden md:table-cell"><?php echo htmlspecialchars($p['username'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-navy-50 text-navy-700 border border-navy-200/80">
                                                <?php echo htmlspecialchars(ucfirst($p['role']), ENT_QUOTES, 'UTF-8'); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-500 hidden lg:table-cell"><?php echo date('d M Y', strtotime($p['created_at'])); ?></td>
                                        <td class="px-6 py-4 text-right">
                                            <?php if (!$isZelf): ?>
                                                <form method="POST" action="/actions/person_action.php" class="inline"
                                                      onsubmit="return confirm('<?php echo htmlspecialchars($p['full_name'], ENT_QUOTES, 'UTF-8'); ?> verwijderen?')">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                                    <button type="submit" title="Verwijderen"
                                                        class="p-1.5 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-xs text-slate-300">—</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-3 border-t border-slate-100 text-xs text-slate-400">
                    Totaal: <?php echo count($personen); ?> persoon/personen
                </div>
            </div>

            <div class="h-4"></div>
        </main>
    </div>
</div>

<!-- Persoon toevoegen -->
<div id="panel-backdrop" class="fixed inset-0 bg-black/40 z-40 hidden" onclick="closePanel()"></div>

<div id="slide-panel" class="fixed right-0 top-0 h-full w-full sm:w-[440px] bg-white shadow-2xl z-50 transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col">
    <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 flex-shrink-0">
        <div>
            <h2 class="text-lg font-bold text-navy-900">Persoon toevoegen</h2>
            <p class="text-sm text-slate-400 mt-0.5">Nieuw account aanmaken in het systeem</p>
        </div>
        <button onclick="closePanel()" class="p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <form method="POST" action="/actions/person_action.php" class="flex-1 overflow-y-auto px-6 py-6 space-y-5">
        <input type="hidden" name="action" value="create">

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Volledige naam <span class="text-accent">*</span></label>
            <input type="text" name="full_name" required placeholder="Voornaam Achternaam"
                class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-all duration-200">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Gebruikersnaam <span class="text-accent">*</span></label>
            <input type="text" name="username" required placeholder="bv. j.devries"
                class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-all duration-200">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Tijdelijk wachtwoord <span class="text-accent">*</span></label>
            <input type="password" name="password" required placeholder="Min. 6 tekens"
                class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-all duration-200">
            <p class="mt-1 text-xs text-slate-400">De gebruiker kan dit later wijzigen.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Rol <span class="text-accent">*</span></label>
            <select name="role" required
                class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-all duration-200 appearance-none bg-white">
                <option value="" disabled selected>Kies een rol…</option>
                <?php foreach ($rollen as $r): ?>
                    <option value="<?php echo $r; ?>"><?php echo ucfirst($r); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="pt-2 flex gap-3">
            <button type="submit"
                class="flex-1 inline-flex items-center justify-center gap-2 bg-accent hover:bg-accent-dark text-white py-3 rounded-xl text-sm font-semibold transition-all duration-200 shadow-lg shadow-accent/25">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Toevoegen
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
