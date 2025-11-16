<?php
/**
 * Point d'entrée de l'application
 * Redirige vers la page appropriée selon l'état de connexion
 */

session_start();
require_once __DIR__ . '/require/config.php';

// Vérifier si l'utilisateur est connecté
if (isset($_SESSION['user'])) {
    // Utilisateur connecté → rediriger vers le tableau de bord
    header('Location: ' . BASE_URL . 'view/dashboard.php');
} else {
    // Utilisateur non connecté → rediriger vers la page de connexion
    header('Location: ' . BASE_URL . 'view/auth/login.php');
}
exit;