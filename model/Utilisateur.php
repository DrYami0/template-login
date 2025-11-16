<?php
require_once __DIR__ . '/../require/db.php';

class Utilisateur {
    public static function trouverParEmail(string $email) {
        $pdo = obtenirPDO();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public static function trouverParId(int $id) {
        $pdo = obtenirPDO();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function creer(string $email, string $password, string $role = 'user'): int {
        $pdo = obtenirPDO();
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $status = 'pending';
        $stmt = $pdo->prepare('INSERT INTO users (email, password_hash, role, status, created_at) VALUES (?, ?, ?, ?, NOW())');
        $stmt->execute([$email, $password_hash, $role, $status]);
        return (int)$pdo->lastInsertId();
    }

    public static function definirStatut(int $id, string $status): bool {
        $pdo = obtenirPDO();
        $stmt = $pdo->prepare('UPDATE users SET status = ? WHERE id = ?');
        return $stmt->execute([$status, $id]);
    }

    public static function listerEnAttente(): array {
        $pdo = obtenirPDO();
        $stmt = $pdo->query("SELECT id, email, role, status, created_at FROM users WHERE status = 'pending' ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }
}