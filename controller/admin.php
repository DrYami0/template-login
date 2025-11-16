<?php
session_start();
require_once __DIR__ . '/../require/config.php';
require_once __DIR__ . '/../require/db.php';
require_once __DIR__ . '/../require/mailer.php';
require_once __DIR__ . '/../model/Utilisateur.php';

if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ' . BASE_URL . 'login.html');
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'list_pending') {
    $pendings = Utilisateur::listerEnAttente();
    require __DIR__ . '/../view/BackOffice/pending.php';
    exit;
}

if ($action === 'approve' || $action === 'reject') {
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) {
        $_SESSION['error'] = 'ID utilisateur invalide';
        header('Location: ' . BASE_URL . 'controller/admin.php?action=list_pending');
        exit;
    }
    $status = $action === 'approve' ? 'active' : 'rejected';
    Utilisateur::definirStatut($id, $status);
    $user = Utilisateur::trouverParId($id);
    if ($user) {
        $sujet = $status === 'active' ? 'Compte approuvé' : 'Compte refusé';
        $corps = $status === 'active' ? 'Votre compte a été approuvé. Vous pouvez maintenant vous connecter.' : 'Votre demande d'inscription a été refusée par l'administrateur.';
        envoyerMailAdmin($user['email'], $sujet, $corps);
    }
    $_SESSION['message'] = 'Statut de l'utilisateur mis à jour.';
    header('Location: ' . BASE_URL . 'controller/admin.php?action=list_pending');
    exit;
}

http_response_code(400);
echo 'Bad request';