<?php
session_start();
require_once __DIR__ . '/../require/config.php';
require_once __DIR__ . '/../require/mailer.php';
require_once __DIR__ . '/../model/Utilisateur.php';

if (!defined('BASE_URL') || BASE_URL === '_BASE_URL_') {
    define('BASE_URL', 'http://localhost/template-login/');
}

$action = $_POST['action'] ?? $_GET['action'] ?? 'dashboard';


if ($action === 'approve' || $action === 'reject') {
    $token = $_GET['token'] ?? '';
    if (empty($token)) {
        die("Token manquant.");
    }

    $pdo = obtenirPDO();
    $stmt = $pdo->prepare("SELECT id, email FROM users WHERE token = ? AND token_expires > NOW() AND status = 'pending' LIMIT 1");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if (!$user) {
        die("Lien invalide ou expiré.");
    }

    if ($action === 'approve') {
        Utilisateur::definirStatut($user['id'], 'active');
        $pdo->prepare("UPDATE users SET token = NULL, token_expires = NULL WHERE id = ?")->execute([$user['id']]);
        $sujet = "Votre compte a été approuvé !";
        $corps = "Félicitations ! Votre compte est maintenant actif. Vous pouvez vous connecter.";
    } else {
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$user['id']]);
        $sujet = "Votre inscription a été refusée";
        $corps = "Malheureusement, votre demande d'inscription a été refusée par l'administrateur.";
    }

    envoyerMailAdmin($user['email'], $sujet, $corps);

    echo "<p>Action effectuée avec succès.</p>";
    echo "<p><a href='" . rtrim(BASE_URL, '/') . "/view/auth/login.php'>Retour à la page de connexion</a></p>";
    exit;
}


if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['error'] = "Accès refusé. Administrateur requis.";
    header('Location: ' . rtrim(BASE_URL, '/') . '/view/auth/login.php');
    exit;
}

$pdo = obtenirPDO();

switch ($action) {
    case 'list_pending':
        $pendings = Utilisateur::listerEnAttente();
        $pageTitle = "Utilisateurs en attente d'approbation";
        require __DIR__ . '/../view/BackOffice/pending.php';
        break;

    case 'list_all':
        $stmt = $pdo->query("SELECT id, email, role, status, created_at FROM users ORDER BY created_at DESC");
        $users = $stmt->fetchAll();
        $pageTitle = "Gestion des utilisateurs";
        require __DIR__ . '/../view/BackOffice/users_list.php';
        break;

    case 'edit':
        $id = (int)($_GET['id'] ?? 0);
        $user = Utilisateur::trouverParId($id);
        if (!$user) {
            $_SESSION['error'] = "Utilisateur introuvable.";
            header('Location: ' . rtrim(BASE_URL, '/') . '/controller/admin.php?action=list_all');
            exit;
        }
        $pageTitle = "Modifier l'utilisateur";
        require __DIR__ . '/../view/BackOffice/user_edit.php';
        break;

    case 'update':
        $id = (int)($_POST['id'] ?? 0);
        $email = trim($_POST['email'] ?? '');
        $role = $_POST['role'] ?? 'user';
        $status = $_POST['status'] ?? 'active';

        if ($id <= 0 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Données invalides.";
            header('Location: ' . rtrim(BASE_URL, '/') . '/controller/admin.php?action=edit&id=' . $id);
            exit;
        }

        $pdo->prepare("UPDATE users SET email = ?, role = ?, status = ? WHERE id = ?")
            ->execute([$email, $role, $status, $id]);

        $_SESSION['message'] = "Utilisateur mis à jour avec succès.";
        header('Location: ' . rtrim(BASE_URL, '/') . '/controller/admin.php?action=list_all');
        exit;
        break;

    case 'delete':
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0 && $id !== $_SESSION['user']['id']) {
            $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
            $_SESSION['message'] = "Utilisateur supprimé.";
        } else {
            $_SESSION['error'] = "Impossible de supprimer cet utilisateur.";
        }
        header('Location: ' . rtrim(BASE_URL, '/') . '/controller/admin.php?action=list_all');
        exit;
        break;

    default:
        $totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $pendingCount = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'pending'")->fetchColumn();
        $pageTitle = "Tableau de bord administrateur";
        require __DIR__ . '/../view/BackOffice/dashboard.php';
        break;
}
