<?php
/**
 * Report Action
 * Verwerkt aanmaken, status-update en verwijderen van meldingen.
 * Verwachte POST-velden: action, + velden afhankelijk van actie.
 */

session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

require_once __DIR__ . '/../includes/db.php';

$actie = $_POST['action'] ?? '';

try {
    switch ($actie) {

        // Nieuwe melding aanmaken
        case 'create':
            $title       = trim($_POST['title']       ?? '');
            $locatie     = trim($_POST['locatie']     ?? '');
            $description = trim($_POST['description'] ?? '');
            $status      = $_POST['status']           ?? 'open';
            $priority    = $_POST['priority']         ?? 'medium';
            $created_by  = $_SESSION['user_id'];

            if ($title === '' || $locatie === '') {
                header('Location: /dashboard/incidents.php?error=validatie&nieuw=1');
                exit;
            }

            // Toegestane waarden valideren
            $geldigeStatussen  = ['open', 'in_progress', 'closed'];
            $geldigePrioriteiten = ['high', 'medium', 'low'];

            if (!in_array($status, $geldigeStatussen, true)) $status = 'open';
            if (!in_array($priority, $geldigePrioriteiten, true)) $priority = 'medium';

            $stmt = getPDO()->prepare(
                "INSERT INTO reports (title, description, status, priority, locatie, created_by, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, NOW())"
            );
            $stmt->execute([$title, $description, $status, $priority, $locatie, $created_by]);

            header('Location: /dashboard/incidents.php?success=aangemaakt');
            exit;

        // Status van melding bijwerken
        case 'update_status':
            $id        = (int) ($_POST['id']     ?? 0);
            $status    = $_POST['status']        ?? '';
            $redirect  = $_POST['redirect']      ?? '/dashboard/incidents.php';

            $geldigeStatussen = ['open', 'in_progress', 'closed'];

            if ($id <= 0 || !in_array($status, $geldigeStatussen, true)) {
                header('Location: ' . $redirect . '?error=validatie');
                exit;
            }

            $stmt = getPDO()->prepare("UPDATE reports SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);

            header('Location: ' . $redirect . '?success=bijgewerkt');
            exit;

        // Melding verwijderen
        case 'delete':
            $id = (int) ($_POST['id'] ?? 0);

            if ($id <= 0) {
                header('Location: /dashboard/incidents.php?error=validatie');
                exit;
            }

            $stmt = getPDO()->prepare("DELETE FROM reports WHERE id = ?");
            $stmt->execute([$id]);

            header('Location: /dashboard/incidents.php?success=verwijderd');
            exit;

        default:
            header('Location: /dashboard/incidents.php');
            exit;
    }
} catch (Exception $e) {
    header('Location: /dashboard/incidents.php?error=db&message=' . urlencode($e->getMessage()));
    exit;
}
