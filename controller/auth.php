<?php
session_start();
require_once __DIR__ . '/../require/config.php';
require_once __DIR__ . '/../require/mailer.php';
require_once __DIR__ . '/../model/Utilisateur.php';

// Make sure BASE_URL is properly defined
if (!defined('BASE_URL') || BASE_URL === '_BASE_URL_') {
    // Change this to your local domain or live domain
    define('BASE_URL', 'http://localhost/template-login/'); // for local testing
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {

    case 'signup':
        $username = trim($_POST['username'] ?? '');
        $nom      = trim($_POST['nom'] ?? '');
        $prenom   = trim($_POST['prenom'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validation
        if (empty($username) || strlen($username) > 50) {
            $_SESSION['signup_error'] = 'Le nom d\'utilisateur est requis et doit contenir maximum 50 caractères.';
            header('Location: ' . rtrim(BASE_URL, '/') . '/view/auth/login.php');
            exit;
        }
        if (empty($nom) || empty($prenom)) {
            $_SESSION['signup_error'] = 'Le nom et le prénom sont obligatoires.';
            header('Location: ' . rtrim(BASE_URL, '/') . '/view/auth/login.php');
            exit;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['signup_error'] = 'Adresse email invalide.';
            header('Location: ' . rtrim(BASE_URL, '/') . '/view/auth/login.php');
            exit;
        }
        if (strlen($password) < 6) {
            $_SESSION['signup_error'] = 'Le mot de passe doit contenir au moins 6 caractères.';
            header('Location: ' . rtrim(BASE_URL, '/') . '/view/auth/login.php');
            exit;
        }

        $pdo = obtenirPDO();
        $check = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check->execute([$username, $email]);
        if ($check->fetch()) {
            $checkUser = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $checkUser->execute([$username]);
            $_SESSION['signup_error'] = $checkUser->fetch() 
                ? 'Ce nom d\'utilisateur existe déjà. Veuillez choisir un autre nom d\'utilisateur.'
                : 'Cet email est déjà utilisé.';
            header('Location: ' . rtrim(BASE_URL, '/') . '/view/auth/login.php');
            exit;
        }

        $userId = Utilisateur::creer($username, $email, $password, $nom, $prenom);

        // Generate token
        $token = bin2hex(random_bytes(32));
        $pdo->prepare("UPDATE users SET token = ?, token_expires = DATE_ADD(NOW(), INTERVAL 48 HOUR) WHERE id = ?")
            ->execute([$token, $userId]);

        // Build safe links
        $base = rtrim(BASE_URL, '/');
        $acceptLink = $base . "/controller/admin.php?action=approve&token=" . $token;
        $rejectLink = $base . "/controller/admin.php?action=reject&token=" . $token;

        // Email content
        $sujet = "Nouvelle inscription en attente d'approbation";
        $corps = "
            <h2>Nouvelle demande d'inscription</h2>
            <p><strong>Nom d'utilisateur :</strong> {$username}</p>
            <p><strong>Nom complet :</strong> {$prenom} {$nom}</p>
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

        $_SESSION['message'] = 'Inscription réussie ! En attente de l’approbation de l’administrateur.';
        header('Location: ' . $base . '/view/auth/login.php');
        exit;
        break;

    case 'login':
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $user = Utilisateur::trouverParEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $_SESSION['error'] = 'Email ou mot de passe incorrect.';
            header('Location: ' . rtrim(BASE_URL, '/') . '/view/auth/login.php');
            exit;
        }

        if ($user['status'] !== 'active') {
            $_SESSION['error'] = 'Votre compte est en attente d’approbation ou a été rejeté.';
            header('Location: ' . rtrim(BASE_URL, '/') . '/view/auth/login.php');
            exit;
        }

        $_SESSION['user'] = [
            'id'       => (int)$user['id'],
            'email'    => $user['email'],
            'username' => $user['username'],
            'nom'      => $user['nom'],
            'prenom'   => $user['prenom'],
            'role'     => $user['role']
        ];

        header('Location: ' . rtrim(BASE_URL, '/') . '/PerFran-master/index.html');
        exit;
        break;

    case 'logout':
        session_unset();
        session_destroy();
        header('Location: ' . rtrim(BASE_URL, '/') . '/view/auth/login.php');
        exit;
        break;

    default:
        http_response_code(400);
        echo 'Requête invalide.';
        exit;
}
