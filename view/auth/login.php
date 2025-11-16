<?php
session_start();
require_once __DIR__ . '/../../require/config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css" />
    <title>Connexion - Votre Application</title>
</head>
<body>
    <div class="container" id="container">
        <div class="form-container sign-up">
            <form action="<?= BASE_URL ?>controller/auth.php" method="POST">
                <input type="hidden" name="action" value="signup">
                <h1>Créer un Compte</h1>
                <div class="social-icons">
                    <a href="#" class="icon"><i class="fa-brands fa-google-plus-g"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-github"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
                <span>ou utilisez votre email pour l'inscription</span>
                <input type="email" name="email" placeholder="Email" required />
                <input type="password" name="password" placeholder="Mot de passe (min 6 caractères)" required minlength="6" />
                <?php if (isset($_SESSION['signup_error'])): ?>
                    <p style="color: red; font-size: 12px; margin-top: 10px;">
                        <?= htmlspecialchars($_SESSION['signup_error']) ?>
                    </p>
                    <?php unset($_SESSION['signup_error']); ?>
                <?php endif; ?>
                <button type="submit">S'inscrire</button>
            </form>
        </div>

   
        <div class="form-container sign-in">
            <form action="<?= BASE_URL ?>controller/auth.php" method="POST">
                <input type="hidden" name="action" value="login">
                <h1>Se Connecter</h1>
                <div class="social-icons">
                    <a href="#" class="icon"><i class="fa-brands fa-google-plus-g"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-github"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
                <span>ou utilisez votre email et mot de passe</span>
                <input type="email" name="email" placeholder="Email" required />
                <input type="password" name="password" placeholder="Mot de passe" required />
                <?php if (isset($_SESSION['error'])): ?>
                    <p style="color: red; font-size: 12px; margin-top: 10px;">
                        <?= htmlspecialchars($_SESSION['error']) ?>
                    </p>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                <?php if (isset($_SESSION['message'])): ?>
                    <p style="color: #2da0a8; font-size: 12px; margin-top: 10px;">
                        <?= htmlspecialchars($_SESSION['message']) ?>
                    </p>
                    <?php unset($_SESSION['message']); ?>
                <?php endif; ?>
                <a href="#">Mot de passe oublié ?</a>
                <button type="submit">Se Connecter</button>
            </form>
        </div>

       
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Bon Retour !</h1>
                    <p>Entrez vos informations personnelles pour utiliser toutes les fonctionnalités du site</p>
                    <button class="hidden" id="login">Se Connecter</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Bonjour, Ami !</h1>
                    <p>Inscrivez-vous avec vos informations personnelles pour utiliser toutes les fonctionnalités du site</p>
                    <button class="hidden" id="register">S'inscrire</button>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= BASE_URL ?>assets/js/script.js"></script>
</body>
</html>