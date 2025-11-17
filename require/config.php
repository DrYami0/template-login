<?php

define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', '2a10_projet');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');


define('_BASE_URL_', 'http://localhost/template-login/');
define('BASE_URL', rtrim(_BASE_URL_, '/') . '/');

define('ADMIN_EMAIL', 'louayfkiri06@gmail.com');

define('MAILER', 'smtp');  

define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'louayfkiri06@gmail.com');
define('SMTP_PASS', 'mjsp lxyi pmbd gmue'); 
define('SMTP_SECURE', 'tls');

try {
    $dsn = "mysql:host=" . DB_HOST 
         . ";port=" . DB_PORT 
         . ";dbname=" . DB_NAME 
         . ";charset=" . DB_CHARSET;

    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_STRINGIFY_FETCHES  => false
    ]);
} catch (PDOException $e) {
    error_log("DB Connection Error: " . $e->getMessage());
    die("Erreur de connexion à la base de données. Veuillez réessayer plus tard.");
}

function obtenirPDO(): PDO {
    global $pdo;
    return $pdo;
}

if (MAILER === 'smtp' && file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}
