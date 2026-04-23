<?php
/**
 * Person Action
 * Verwerkt aanmaken en verwijderen van personen (gebruikers).
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

        // Nieuw persoon aanmaken
        case 'create':
            $fullName = trim($_POST['full_name'] ?? '');
            $username = trim($_POST['username']  ?? '');
            $password = trim($_POST['password']  ?? '');
            $role     = trim($_POST['role']      ?? '');

            if ($fullName === '' || $username === '' || $password === '' || $role === '') {
                header('Location: /dashboard/persons.php?error=validatie&nieuw=1');
                exit;
            }

            if (strlen($password) < 6) {
                header('Location: /dashboard/persons.php?error=wachtwoord&nieuw=1');
                exit;
            }

            $hashed = password_hash($password, PASSWORD_BCRYPT);

            $stmt = getPDO()->prepare(
                "INSERT INTO users (username, password, full_name, role, created_at)
                 VALUES (?, ?, ?, ?, NOW())"
            );
            $stmt->execute([$username, $hashed, $fullName, $role]);

            header('Location: /dashboard/persons.php?success=aangemaakt');
            exit;

        // Persoon verwijderen
        case 'delete':
            $id = (int) ($_POST['id'] ?? 0);

            // Voorkom dat men zichzelf verwijdert
            if ($id <= 0 || $id === (int) $_SESSION['user_id']) {
                header('Location: /dashboard/persons.php?error=validatie');
                exit;
            }

            $stmt = getPDO()->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);

            header('Location: /dashboard/persons.php?success=verwijderd');
            exit;

        default:
            header('Location: /dashboard/persons.php');
            exit;
    }
} catch (RuntimeException) {
    header('Location: /dashboard/persons.php?error=db');
    exit;
} catch (\PDOException $e) {
    // Gebruikersnaam bestaat al (duplicate key)
    if ($e->getCode() === '23000') {
        header('Location: /dashboard/persons.php?error=bestaat&nieuw=1');
        exit;
    }
    header('Location: /dashboard/persons.php?error=db');
    exit;
}
