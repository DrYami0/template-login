<?php
session_start();
require_once __DIR__ . '/../require/config.php';
require_once __DIR__ . '/../require/db.php';
require_once __DIR__ . '/../require/mailer.php';
require_once __DIR__ . '/../model/Utilisateur.php';

// Définir BASE_URL si pas encore fait (au casé)
if (!defined('BASE_URL')) {
    define('BASE_URL', _BASE_URL_);
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {

    // ========================================
    // INSCRIPTION (Créer compte + Envoi email admin)
    // ========================================
    case 'signup':
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['signup_error'] = 'Adresse email invalide.';
            header('Location: ' . BASE_URL . 'view/auth/login.php');
            exit;
        }

        if (strlen($password) < 6) {
            $_SESSION['signup_error'] = 'Le mot de passe doit contenir au moins 6 caractères.';
            header('Location: ' . BASE_URL . 'view/auth/login.php');
            exit;
        }

        // Vérifier si l'email existe déjà
        if (Utilisateur::trouverParEmail($email)) {
            $_SESSION['signup_error'] = 'Cet email est déjà utilisé.';
            header('Location: ' . BASE_URL . 'view/auth/login.php');
            exit;
        }

        // Créer l'utilisateur (status = pending)
        $userId = Utilisateur::creer($email, $password);

        // Générer un token d'approbation
        $token = bin2hex(random_bytes(32));
        $pdo->prepare("UPDATE users SET token = ?, token_expires = DATE_ADD(NOW(), INTERVAL 48 HOUR) WHERE id = ?")
            ->execute([$token, $userId]);

        // Lien d'approbation
        $acceptLink = BASE_URL . "controller/admin.php?action=approve&token=" . $token;
        $rejectLink = BASE_URL . "controller/admin.php?action=reject&token=" . $token;

        // Email à l'admin
        $sujet = "Nouvelle inscription en attente d'approbation";
        $corps = "
            <h2>Nouvelle inscription</h2>
            <p><strong>Email :</strong> {$email}</p>
            <p><strong>ID :</strong> {$userId}</p>
            <hr>
            <p>
                <a href='{$acceptLink}' style='background:#27ae60;color:white;padding:12px 20px;text-decoration:none;border-radius:5px;margin:5px;display:inline-block;'>Approuver</a>
                <a href='{$rejectLink}' style='background:#c0392b;color:white;padding:12px 20px;text-decoration:none;border-radius:5px;margin:5px;display:inline-block;'>Refuser</a>
            </p>
            <small>Lien expire dans 48 heures.</small>
        ";

        envoyerMailAdmin(ADMIN_EMAIL, $sujet, $corps);

        // Message à l'utilisateur
        $_SESSION['message'] = 'Inscription réussie ! En attente de l’approbation de l’administrateur.';
        header('Location: ' . BASE_URL . 'view/auth/login.php');
        exit;
        break;


    // ========================================
    // CONNEXION
    // ========================================
    case 'login':
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $user = Utilisateur::trouverParEmail($email);

        // Vérification mot de passe
        if (!$user || !password_verify($password, $user['password_hash'])) {
            $_SESSION['error'] = 'Email ou mot de passe incorrect.';
            header('Location: ' . BASE_URL . 'view/auth/login.php');
            exit;
        }

        // Vérification statut
        if ($user['status'] !== 'approved') {
            $_SESSION['error'] = 'Votre compte est en attente d’approbation par l’administrateur.';
            header('Location: ' . BASE_URL . 'view/auth/login.php');
            exit;
        }

        // Connexion réussie
        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'email' => $user['email'],
            'role' => $user['role'],
            'nom' => $user['nom'] ?? '',
            'prenom' => $user['prenom'] ?? ''
        ];

        header('Location: ' . BASE_URL . 'view/dashboard.php');
        exit;
        break;


    // ========================================
    // DÉCONNEXION
    // ========================================
    case 'logout':
        session_unset();
        session_destroy();
        header('Location: ' . BASE_URL . 'view/auth/login.php');
        exit;
        break;


    // ========================================
    // ERREUR 400
    // ========================================
    default:
        http_response_code(400);
        echo 'Requête invalide.';
        exit;
}
