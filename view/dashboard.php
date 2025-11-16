<?php
session_start();
require_once __DIR__ . '/../require/config.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'view/auth/login.php');
    exit;
}

$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }
        
        body {
            background: linear-gradient(to right, #e2e2e2, #c9d6ff);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .dashboard {
            background: white;
            padding: 50px;
            border-radius: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.35);
            text-align: center;
            max-width: 600px;
            width: 100%;
        }
        
        h1 {
            color: #2da0a8;
            margin-bottom: 30px;
            font-size: 2em;
        }
        
        .user-info {
            margin: 30px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 15px;
        }
        
        .user-info p {
            margin: 10px 0;
            font-size: 16px;
            color: #333;
        }
        
        .user-info strong {
            color: #2da0a8;
        }
        
        .badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-left: 10px;
        }
        
        .badge-admin {
            background: #8f44fd;
            color: white;
        }
        
        .badge-user {
            background: #2da0a8;
            color: white;
        }
        
        .btn {
            background: #2da0a8;
            color: white;
            padding: 12px 35px;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 10px 5px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(45, 160, 168, 0.4);
        }
        
        .btn-admin {
            background: #8f44fd;
        }
        
        .btn-admin:hover {
            box-shadow: 0 5px 15px rgba(143, 68, 253, 0.4);
        }
        
        .btn-logout {
            background: #dc3545;
        }
        
        .btn-logout:hover {
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }
        
        .welcome-icon {
            font-size: 4em;
            color: #2da0a8;
            margin-bottom: 20px;
        }
        
        .actions {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="welcome-icon">
            <i class="fas fa-user-circle"></i>
        </div>
        
        <h1>Bienvenue sur le Tableau de Bord !</h1>
        
        <div class="user-info">
            <p>
                <strong>Email :</strong> <?= htmlspecialchars($user['email']) ?>
            </p>
            <p>
                <strong>Rôle :</strong> 
                <span class="badge <?= $user['role'] === 'admin' ? 'badge-admin' : 'badge-user' ?>">
                    <?= $user['role'] === 'admin' ? 'Administrateur' : 'Utilisateur' ?>
                </span>
            </p>
        </div>
        
        <div class="actions">
            <?php if ($user['role'] === 'admin'): ?>
                <a href="<?= BASE_URL ?>controller/admin.php?action=list_pending" class="btn btn-admin">
                    <i class="fas fa-users-cog"></i> Gérer les Utilisateurs en Attente
                </a>
                <br>
            <?php endif; ?>
            
            <a href="<?= BASE_URL ?>controller/auth.php?action=logout" class="btn btn-logout">
                <i class="fas fa-sign-out-alt"></i> Se Déconnecter
            </a>
        </div>
    </div>
</body>
</html>