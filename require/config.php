<?php
// Project config - EDIT BEFORE USE
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'your_database');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');

// If project is hosted in a subfolder, update BASE_URL (must end with '/')
define('BASE_URL', '/');

// Admin email for signup notifications
define('ADMIN_EMAIL', 'admin@example.com');

// Mailer: 'phpmail' or 'smtp'
define('MAILER', 'phpmail');

// SMTP settings (only used if MAILER === 'smtp')
define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'smtp_user@example.com');
define('SMTP_PASS', 'smtp_password');
define('SMTP_SECURE', 'tls'); // 'tls' or 'ssl'