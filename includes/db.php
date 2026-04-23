<?php


// ENV Bestand opladen
function loadEnv(): array
{
    $envPath = dirname(__DIR__) . '/.env';

    if (!file_exists($envPath)) {
        return [];
    }

    $vars = [];
    foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
        $vars[trim($key)] = trim($value);
    }

    return $vars;
}

function getPDO(): PDO
{
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    $env = loadEnv();

    $host   = $env['DB_HOST'] ?? 'localhost';
    $port   = $env['DB_PORT'] ?? '3306';
    $name   = $env['DB_NAME'] ?? '';
    $user   = $env['DB_USER'] ?? 'root';
    $pass   = $env['DB_PASS'] ?? '';

    $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (PDOException) {
        // Gooit nooit databasedetails terug naar de browser
        throw new RuntimeException('Databaseverbinding mislukt. Controleer je .env-instellingen.');
    }

    return $pdo;
}

/**
 * Normaliseer een statuswaarde uit de DB naar de weergavenaam die de dashboard-UI verwacht
 */
function normaliseerStatus(string $status): string
{
    return match (strtolower(trim($status))) {
        'open'                       => 'Open',
        'pending', 'in_behandeling', 'in_progress'     => 'In behandeling',
        'closed'                     => 'Gesloten',
        default                      => ucfirst($status),
    };
}

/**
 * Normaliseer een prioriteitswaarde uit de DB naar de weergavenaam die de dashboard-UI verwacht
 */
function normaliseerPrioriteit(string $priority): string
{
    return match (strtolower(trim($priority))) {
        'high'   => 'Hoog',
        'medium' => 'Gemiddeld',
        'low'    => 'Laag',
        default             => ucfirst($priority),
    };
}
