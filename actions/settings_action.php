<?php
/**
 * Settings Acties
 * Allemaal verwerken aanpassingen vanuit /dashboard/settings.php
 */

session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

require_once __DIR__ . '/../includes/db.php';

// Hulpfunctie: welke kolommen bestaan in de users tbl
function userKolommen(): array
{
    static $cols = null;
    if ($cols !== null) {
        return $cols;
    }
    try {
        $rows = getPDO()->query('SHOW COLUMNS FROM users')->fetchAll();
        $cols = array_column($rows, 'Field');
    } catch (\Throwable) {
        $cols = [];
    }
    return $cols;
}

/** Redirect terug naar de settings pagina met een status. */
function terugNaarSettings(string $sectie, string $key = '', string $waarde = ''): never
{
    $url = '/dashboard/settings.php#' . $sectie;
    if ($key !== '') {
        $sep = str_contains($url, '?') ? '&' : '?';
        $url = '/dashboard/settings.php?' . $key . '=' . urlencode($waarde) . '#' . $sectie;
    }
    header('Location: ' . $url);
    exit;
}

$actie = $_POST['action'] ?? '';
$userId = (int) $_SESSION['user_id'];
$kolommen = userKolommen();

try {
    switch ($actie) {

        // Profiel bijwerken
        case 'update_profile':
            $fullName = trim($_POST['full_name'] ?? '');
            $username = trim($_POST['username']  ?? '');
            $email    = trim($_POST['email']     ?? '');
            $telefoon = trim($_POST['telefoon']  ?? '');

            if ($fullName === '' || $username === '') {
                terugNaarSettings('profiel', 'error', 'validatie');
            }

            if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                terugNaarSettings('profiel', 'error', 'email_ongeldig');
            }

            // Gebruikersnaam mag niet al door een andere gebruiker gebruikt worden
            $check = getPDO()->prepare('SELECT id FROM users WHERE username = ? AND id <> ? LIMIT 1');
            $check->execute([$username, $userId]);
            if ($check->fetchColumn()) {
                terugNaarSettings('profiel', 'error', 'username_bestaat');
            }

            // Bouw dynamisch de UPDATE op basis van bestaande kolommen
            $velden = ['full_name = ?', 'username = ?'];
            $waarden = [$fullName, $username];

            if (in_array('email', $kolommen, true)) {
                $velden[]  = 'email = ?';
                $waarden[] = $email;
            }
            if (in_array('telefoon', $kolommen, true)) {
                $velden[]  = 'telefoon = ?';
                $waarden[] = $telefoon;
            }
            if (in_array('last_profile_update', $kolommen, true)) {
                $velden[] = 'last_profile_update = NOW()';
            }

            $waarden[] = $userId;

            $sql = 'UPDATE users SET ' . implode(', ', $velden) . ' WHERE id = ?';
            getPDO()->prepare($sql)->execute($waarden);

            // Sessie bijwerken zodat sidebar / topbar direct de nieuwe naam tonen
            $_SESSION['user_full_name'] = $fullName;
            $_SESSION['user_username']  = $username;

            terugNaarSettings('profiel', 'success', 'profiel_opgeslagen');

        // Wachtwoord wijzigen
        case 'change_password':
            $huidig  = $_POST['current_password'] ?? '';
            $nieuw   = $_POST['new_password']     ?? '';
            $bevest  = $_POST['confirm_password'] ?? '';

            if ($huidig === '' || $nieuw === '' || $bevest === '') {
                terugNaarSettings('beveiliging', 'error', 'validatie');
            }
            if ($nieuw !== $bevest) {
                terugNaarSettings('beveiliging', 'error', 'ww_mismatch');
            }
            if (strlen($nieuw) < 8) {
                terugNaarSettings('beveiliging', 'error', 'ww_kort');
            }

            $stmt = getPDO()->prepare('SELECT password FROM users WHERE id = ? LIMIT 1');
            $stmt->execute([$userId]);
            $hash = $stmt->fetchColumn();

            if (!$hash || !password_verify($huidig, $hash)) {
                terugNaarSettings('beveiliging', 'error', 'ww_fout');
            }

            $nieuweHash = password_hash($nieuw, PASSWORD_BCRYPT);

            $velden = ['password = ?'];
            $waarden = [$nieuweHash];

            if (in_array('last_password_change', $kolommen, true)) {
                $velden[] = 'last_password_change = NOW()';
            }
            $waarden[] = $userId;

            $sql = 'UPDATE users SET ' . implode(', ', $velden) . ' WHERE id = ?';
            getPDO()->prepare($sql)->execute($waarden);

            terugNaarSettings('beveiliging', 'success', 'ww_gewijzigd');

        // Systeem voorkeuren
        case 'update_preferences':
            $taal           = in_array($_POST['taal'] ?? '', ['nl', 'fr', 'en'], true) ? $_POST['taal'] : 'nl';
            $theme          = in_array($_POST['theme'] ?? '', ['light', 'dark', 'system'], true) ? $_POST['theme'] : 'light';
            $refreshInterval = max(10, min(600, (int) ($_POST['refresh_interval'] ?? 60)));
            $timezone       = trim($_POST['timezone'] ?? 'Europe/Brussels');

            $_SESSION['preferences'] = [
                'taal'              => $taal,
                'theme'             => $theme,
                'refresh_interval'  => $refreshInterval,
                'timezone'          => $timezone,
                'notify_email'      => isset($_POST['notify_email']),
                'notify_dashboard'  => isset($_POST['notify_dashboard']),
                'notify_sms'        => isset($_POST['notify_sms']),
            ];

            terugNaarSettings('voorkeuren', 'success', 'voorkeuren_opgeslagen');

        // Operationele instellingen
        case 'update_operational':
            $_SESSION['operational'] = [
                'eenheid'            => trim($_POST['eenheid'] ?? ''),
                'dispatch'           => in_array($_POST['dispatch'] ?? '', ['auto', 'manueel', 'hybride'], true) ? $_POST['dispatch'] : 'auto',
                'prioriteit_alerts'  => isset($_POST['prioriteit_alerts']),
                'auto_status'        => isset($_POST['auto_status']),
            ];

            terugNaarSettings('operationeel', 'success', 'operationeel_opgeslagen');

        default:
            header('Location: /dashboard/settings.php');
            exit;
    }
} catch (\PDOException $e) {
    if ($e->getCode() === '23000') {
        terugNaarSettings($actie === 'update_profile' ? 'profiel' : 'beveiliging', 'error', 'username_bestaat');
    }
    terugNaarSettings('profiel', 'error', 'db');
} catch (\RuntimeException) {
    terugNaarSettings('profiel', 'error', 'db');
}
