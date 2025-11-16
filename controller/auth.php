<?php
session_start();
require_once __DIR__ . '/../require/config.php';
require_once __DIR__ . '/../require/db.php';
require_once __DIR__ . '/../require/mailer.php';
require_once __DIR__ . '/../model/Utilisateur.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'signup') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
        $_SESSION['error'] = 'Email ou mot de passe invalide (min 6 caractères).';
        header('Location: ' . BASE_URL . 'signup.html');
        exit;
    }

    if (Utilisateur::trouverParEmail($email)) {
        $_SESSION['error'] = 'Email déjà enregistré.';
        header('Location: ' . BASE_URL . 'signup.html');
        exit;
    }

    $userId = Utilisateur::creer($email, $password);

    $sujet = 'Nouvelle inscription en attente d'approbation';
    $corps = "Un nouvel utilisateur s'est inscrit:\n\nEmail: {
$email}\nID utilisateur: {
$userId}\n\nVeuillez approuver ou refuser dans le BackOffice.";
    envoyerMailAdmin(ADMIN_EMAIL, $sujet, $corps);

    $_SESSION['message'] = 'Inscription réussie — en attente d'approbation administrateur.';
    header('Location: ' . BASE_URL . 'login.html');
    exit;
}

if ($action === 'login') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $user = Utilisateur::trouverParEmail($email);
    if (!$user || !password_verify($password, $user['password_hash'])) {
        $_SESSION['error'] = 'Identifiants invalides.';
        header('Location: ' . BASE_URL . 'login.html');
        exit;
    }

    if ($user['status'] !== 'active') {
        $_SESSION['error'] = 'Compte non actif. Veuillez attendre l'approbation administrateur.';
        header('Location: ' . BASE_URL . 'login.html');
        exit;
    }

    $_SESSION['user'] = [
        'id' => (int)$user['id'],
        'email' => $user['email'],
        'role' => $user['role'],
    ];
    header('Location: ' . BASE_URL . 'index.html');
    exit;
}

if ($action === 'logout') {
    session_destroy();
    header('Location: ' . BASE_URL . 'index.html');
    exit;
}

http_response_code(400);
echo 'Bad request';