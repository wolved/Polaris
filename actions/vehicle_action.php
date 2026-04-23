<?php
/**
 * Vehicle Action
 * Verwerkt aanmaken, status-update, locatie-update en verwijderen van voertuigen.
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

        // Nieuw voertuig toevoegen
        case 'create':
            $naam        = trim($_POST['naam']        ?? '');
            $type        = trim($_POST['type']        ?? '');
            $kenteken    = trim($_POST['kenteken']    ?? '');
            $status      = $_POST['status']           ?? 'beschikbaar';
            $lat         = (float) ($_POST['lat']     ?? 50.8503);
            $lng         = (float) ($_POST['lng']     ?? 4.3517);
            $locatieNaam = trim($_POST['locatie_naam'] ?? '');

            if ($naam === '' || $type === '') {
                header('Location: /dashboard/vehicles.php?error=validatie&nieuw=1');
                exit;
            }

            $geldigeStatussen = ['beschikbaar', 'in_gebruik', 'onderhoud'];
            if (!in_array($status, $geldigeStatussen, true)) $status = 'beschikbaar';

            $stmt = getPDO()->prepare(
                "INSERT INTO vehicles (naam, type, kenteken, status, lat, lng, locatie_naam, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, NOW())"
            );
            $stmt->execute([$naam, $type, $kenteken, $status, $lat, $lng, $locatieNaam]);

            header('Location: /dashboard/vehicles.php?success=aangemaakt');
            exit;

        // Voertuig bewerken (naam, type, kenteken, status, locatie)
        case 'update':
            $id          = (int) ($_POST['id']         ?? 0);
            $naam        = trim($_POST['naam']         ?? '');
            $type        = trim($_POST['type']         ?? '');
            $kenteken    = trim($_POST['kenteken']     ?? '');
            $status      = $_POST['status']            ?? 'beschikbaar';
            $lat         = (float) ($_POST['lat']      ?? 50.8503);
            $lng         = (float) ($_POST['lng']      ?? 4.3517);
            $locatieNaam = trim($_POST['locatie_naam'] ?? '');

            if ($id <= 0 || $naam === '' || $type === '') {
                header('Location: /dashboard/vehicles.php?error=validatie');
                exit;
            }

            $geldigeStatussen = ['beschikbaar', 'in_gebruik', 'onderhoud'];
            if (!in_array($status, $geldigeStatussen, true)) $status = 'beschikbaar';

            $stmt = getPDO()->prepare(
                "UPDATE vehicles
                 SET naam = ?, type = ?, kenteken = ?, status = ?,
                     lat = ?, lng = ?, locatie_naam = ?, updated_at = NOW()
                 WHERE id = ?"
            );
            $stmt->execute([$naam, $type, $kenteken, $status, $lat, $lng, $locatieNaam, $id]);

            header('Location: /dashboard/vehicles.php?success=bewerkt');
            exit;

        // Status bijwerken
        case 'update_status':
            $id     = (int) ($_POST['id']     ?? 0);
            $status = $_POST['status']        ?? '';

            $geldigeStatussen = ['beschikbaar', 'in_gebruik', 'onderhoud'];

            if ($id <= 0 || !in_array($status, $geldigeStatussen, true)) {
                header('Location: /dashboard/vehicles.php?error=validatie');
                exit;
            }

            $stmt = getPDO()->prepare("UPDATE vehicles SET status = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$status, $id]);

            header('Location: /dashboard/vehicles.php?success=bijgewerkt');
            exit;

        // Locatie bijwerken
        case 'update_location':
            $id          = (int) ($_POST['id']         ?? 0);
            $lat         = (float) ($_POST['lat']      ?? 0);
            $lng         = (float) ($_POST['lng']      ?? 0);
            $locatieNaam = trim($_POST['locatie_naam'] ?? '');

            if ($id <= 0) {
                header('Location: /dashboard/vehicles.php?error=validatie');
                exit;
            }

            $stmt = getPDO()->prepare(
                "UPDATE vehicles SET lat = ?, lng = ?, locatie_naam = ?, updated_at = NOW() WHERE id = ?"
            );
            $stmt->execute([$lat, $lng, $locatieNaam, $id]);

            header('Location: /dashboard/vehicles.php?success=locatie');
            exit;

        // Voertuig verwijderen
        case 'delete':
            $id = (int) ($_POST['id'] ?? 0);

            if ($id <= 0) {
                header('Location: /dashboard/vehicles.php?error=validatie');
                exit;
            }

            $stmt = getPDO()->prepare("DELETE FROM vehicles WHERE id = ?");
            $stmt->execute([$id]);

            header('Location: /dashboard/vehicles.php?success=verwijderd');
            exit;

        default:
            header('Location: /dashboard/vehicles.php');
            exit;
    }
} catch (RuntimeException) {
    header('Location: /dashboard/vehicles.php?error=db');
    exit;
}
