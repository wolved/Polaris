<?php
/**
 * Contact formulier verwerking
 *
 * Ontvangt POST-data van contact.php, valideert, sanitiseert
 * en simuleert het verzenden van een e-mail (log naar bestand).
 * Redirect daarna terug naar contact.php met een statuscode.
 */

// Alleen POST-verzoeken accepteren
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /contact.php');
    exit;
}

// Sanitisatie
$name    = trim(strip_tags($_POST['name']    ?? ''));
$email   = trim(strip_tags($_POST['email']   ?? ''));
$subject = trim(strip_tags($_POST['subject'] ?? ''));
$message = trim(strip_tags($_POST['message'] ?? ''));
$privacy = isset($_POST['privacy']);

// Validatie
$errors = [];

if (empty($name)) {
    $errors[] = 'Naam ontbreekt.';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Ongeldig e-mailadres.';
}

$allowedSubjects = ['technisch', 'algemeen', 'samenwerking', 'demo', 'overige'];
if (empty($subject) || !in_array($subject, $allowedSubjects, true)) {
    $errors[] = 'Ongeldig onderwerp.';
}

if (strlen($message) < 20) {
    $errors[] = 'Bericht is te kort (min. 20 tekens).';
}

if (!$privacy) {
    $errors[] = 'Privacybeleid niet geaccepteerd.';
}

// Redirect met error-status bij fouten
if (!empty($errors)) {
    header('Location: /contact.php?status=error');
    exit;
}

// Verwerking
// Simuleert email, maar logt de inzending gewoon nr bestand
$logDir  = __DIR__ . '/logs';
$logFile = $logDir . '/contact.log';

if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

$timestamp  = date('Y-m-d H:i:s');
$shortMsg   = mb_substr($message, 0, 200);
$logEntry   = "[{$timestamp}] Van: {$name} <{$email}> | Onderwerp: {$subject} | Bericht: {$shortMsg}\n";

file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);

// Redirect naar succespagina
header('Location: /contact.php?status=success');
exit;
