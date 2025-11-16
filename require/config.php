<?php
/**
 * Configuration principale du projet
 * Fichier : require/config.php
 * À placer dans : /template-login/require/config.php
 */

// === SÉCURITÉ : Empêcher l'accès direct ===
if (!defined('SECURE_ACCESS')) {
    die('Accès direct interdit.');
}
define('SECURE_ACCESS', true);

/* ========================================
   BASE DE DONNÉES (XAMPP / MySQL)
   ======================================== */
define('DB_HOST', '127.0.0.1');           // ou 'localhost'
define('DB_PORT', '3306');
define('DB_NAME', '2a10_projet');         // Nom de ta base
define('DB_USER', 'root');                // XAMPP par défaut
define('DB_PASS', '');                    // Mot de passe vide par défaut sur XAMPP
define('DB_CHARSET', 'utf8mb4');

/* ========================================
   URL DE BASE DU PROJET
   Exemple : http://localhost/template-login/
   Doit TOUJOURS se terminer par un "/"
   ======================================== */
define('_BASE_URL_', '/template-login/');
define('BASE_URL', _BASE_URL_); // Compatibilité avec ton code existant

/* ========================================
   ADMIN (pour notifications d'inscription)
   ======================================== */
define('ADMIN_EMAIL', 'louay.fkiri@esprit.tn');

/* ========================================
   MAILER : Envoi d'emails
   'phpmail' → fonction mail() de PHP
   'smtp'    → PHPMailer (recommandé pour Gmail)
   ======================================== */
define('MAILER', 'phpmail'); // Change en 'smtp' si tu veux Gmail

/* ========================================
   CONFIGURATION SMTP (uniquement si MAILER = 'smtp')
   Exemple Gmail :
   - Host: smtp.gmail.com
   - Port: 587 (tls) ou 465 (ssl)
   - Utilise un "App Password" (16 caractères)
   ======================================== */
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'tonemail@gmail.com');     // À REMPLIR
define('SMTP_PASS', 'ton-app-password');       // À REMPLIR (16 caractères)
define('SMTP_SECURE', 'tls'); // 'tls' ou 'ssl'

/* ========================================
   AUTRES CONSTANTES
   ======================================== */
define('SITE_NAME', '2A10 - Gestion Étudiants');
define('SITE_VERSION', '1.0.0');

/* ========================================
   INCLUSION DE PHPMailer (si Composer installé)
   ======================================== */
if (MAILER === 'smtp' && file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

/* ========================================
   CONNEXION PDO (via db.php)
   ======================================== */
require_once __DIR__ . '/db.php';
