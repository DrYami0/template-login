<?php

class Utilisateur {


    public static function trouverParEmail(string $email): ?array {
        $pdo = obtenirPDO();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

  
    public static function trouverParId(int $id): ?array {
        $pdo = obtenirPDO();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

  
    public static function creer(string $username, string $email, string $password, string $nom, string $prenom): int {
        $pdo = obtenirPDO();
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users 
                (username, nom, prenom, email, password_hash, role, status, created_at) 
                VALUES (?, ?, ?, ?, ?, 'user', 'pending', NOW())";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username, $nom, $prenom, $email, $password_hash]);

        return (int)$pdo->lastInsertId();
    }

  
    public static function definirStatut(int $id, string $status): bool {
        $pdo = obtenirPDO();
        $stmt = $pdo->prepare('UPDATE users SET status = ?, token = NULL, token_expires = NULL WHERE id = ?');
        return $stmt->execute([$status, $id]);
    }

   
    public static function approuver(string $token): bool {
        $pdo = obtenirPDO();
        $stmt = $pdo->prepare("
            UPDATE users 
            SET status = 'active', token = NULL, token_expires = NULL 
            WHERE token = ? AND token_expires > NOW()
        ");
        return $stmt->execute([$token]);
    }


    public static function rejeter(string $token): bool {
        $pdo = obtenirPDO();
        $stmt = $pdo->prepare("DELETE FROM users WHERE token = ?");
        return $stmt->execute([$token]);
    }


    public static function listerEnAttente(): array {
        $pdo = obtenirPDO();
        $stmt = $pdo->query("
            SELECT id, username, nom, prenom, email, created_at 
            FROM users 
            WHERE status = 'pending' 
            ORDER BY created_at DESC
        ");
        return $stmt->fetchAll();
    }

    
    public static function listerTous(): array {
        $pdo = obtenirPDO();
        $stmt = $pdo->query("
            SELECT id, username, nom, prenom, email, role, status, created_at 
            FROM users 
            ORDER BY created_at DESC
        ");
        return $stmt->fetchAll();
    }
}