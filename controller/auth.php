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
        $_SESSION['signup_error'] = 'Email or password invalid (min 6 characters).';
        header('Location: ' . BASE_URL . 'view/auth/login.php');
        exit;
    }

    if (Utilisateur::trouverParEmail($email)) {
        $_SESSION['signup_error'] = 'Email already registered.';
        header('Location: ' . BASE_URL . 'view/auth/login.php');
        exit;
    }

    $userId = Utilisateur::creer($email, $password);

    $sujet = 'New registration pending approval';
    $corps = "A new user has registered:\n\nEmail: {$email}\nUser ID: {$userId}\n\nPlease approve or reject in the BackOffice.";
    envoyerMailAdmin(ADMIN_EMAIL, $sujet, $corps);

    $_SESSION['message'] = 'Registration successful — awaiting administrator approval.';
    header('Location: ' . BASE_URL . 'view/auth/login.php');
    exit;
}

if ($action === 'login') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $user = Utilisateur::trouverParEmail($email);
    if (!$user || !password_verify($password, $user['password_hash'])) {
        $_SESSION['error'] = 'Invalid credentials.';
        header('Location: ' . BASE_URL . 'view/auth/login.php');
        exit;
    }

    if ($user['status'] !== 'active') {
        $_SESSION['error'] = 'Account not active. Please wait for administrator approval.';
        header('Location: ' . BASE_URL . 'view/auth/login.php');
        exit;
    }

    $_SESSION['user'] = [
        'id' => (int)$user['id'],
        'email' => $user['email'],
        'role' => $user['role'],
    ];
    header('Location: ' . BASE_URL . 'view/dashboard.php');
    exit;
}

if ($action === 'logout') {
    session_destroy();
    header('Location: ' . BASE_URL . 'view/auth/login.php');
    exit;
}

http_response_code(400);
echo 'Bad request';