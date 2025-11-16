-- ========================================
-- Base de données : 2a10_projet
-- ========================================
CREATE DATABASE IF NOT EXISTS `2a10_projet` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `2a10_projet`;

-- ========================================
-- Table : users
-- Gestion complète des utilisateurs
-- ========================================
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  
  -- Informations personnelles
  `username` VARCHAR(50) UNIQUE,
  `nom` VARCHAR(100),
  `prenom` VARCHAR(100),
  
  -- Authentification
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  
  -- Rôle & Statut
  `role` ENUM('user', 'admin') NOT NULL DEFAULT 'user',
  `status` ENUM('pending', 'active', 'rejected') NOT NULL DEFAULT 'pending',
  
  -- Approbation par email
  `token` VARCHAR(64) NULL,
  `token_expires` DATETIME NULL,
  
  -- Dates
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================
-- Index pour performance
-- ========================================
CREATE INDEX idx_status ON users(status);
CREATE INDEX idx_email ON users(email);
CREATE INDEX idx_token ON users(token);

-- ========================================
-- Utilisateur admin par défaut
-- Email : louay.fkiri@esprit.tn
-- Mot de passe : 123456
-- ========================================
INSERT INTO `users` (
  `username`, `email`, `password_hash`, `nom`, `prenom`, 
  `role`, `status`
) VALUES (
  'louayfkiri',
  'louay.fkiri@esprit.tn',
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- hash de "123456"
  'Fkiri',
  'Louay',
  'admin',
  'active'
);
