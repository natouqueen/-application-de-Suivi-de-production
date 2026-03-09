-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 25 sep. 2025 à 13:34
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `projet`
--

-- --------------------------------------------------------

--
-- Structure de la table `client`
--

DROP TABLE IF EXISTS `client`;
CREATE TABLE IF NOT EXISTS `client` (
  `id_client` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_client`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `client`
--

INSERT INTO `client` (`id_client`, `nom`, `email`, `telephone`, `adresse`) VALUES
(1, 'Jean Dupont', 'jean@example.com', '690000000', 'Douala'),
(2, 'Marie Claire', 'marie@example.com', '691111111', 'Yaoundé'),
(3, 'mefenza', 'n14208666@gmail.com', '675773387', 'yaounde'),
(4, 'nken', 'nken@gmail.com', '645258974', 'yaounde'),
(5, 'kimi', NULL, '698798948', 'kimi@gmail.com');

-- --------------------------------------------------------

--
-- Structure de la table `commande`
--

DROP TABLE IF EXISTS `commande`;
CREATE TABLE IF NOT EXISTS `commande` (
  `id_commande` int NOT NULL AUTO_INCREMENT,
  `id_client` int DEFAULT NULL,
  `date_commande` date DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `quantite` int DEFAULT NULL,
  `produit` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id_commande`),
  KEY `id_client` (`id_client`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `commande`
--

INSERT INTO `commande` (`id_commande`, `id_client`, `date_commande`, `total`, `quantite`, `produit`) VALUES
(1, 1, '2025-02-01', 3500.00, NULL, NULL),
(2, 2, '2025-02-05', 2500.00, NULL, NULL),
(4, 2, '2025-09-03', NULL, 100, 'produit Z'),
(5, 3, '2025-07-04', NULL, 258963, 'produit H'),
(6, 5, '2025-09-22', NULL, 1233, 'produit X');

-- --------------------------------------------------------

--
-- Structure de la table `commande_produit`
--

DROP TABLE IF EXISTS `commande_produit`;
CREATE TABLE IF NOT EXISTS `commande_produit` (
  `id_commande` int NOT NULL,
  `id_produit` int NOT NULL,
  `quantite` int DEFAULT NULL,
  PRIMARY KEY (`id_commande`,`id_produit`),
  KEY `id_produit` (`id_produit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `commande_produit`
--

INSERT INTO `commande_produit` (`id_commande`, `id_produit`, `quantite`) VALUES
(1, 1, 2),
(1, 2, 1),
(2, 2, 1);

-- --------------------------------------------------------

--
-- Structure de la table `fournisseur`
--

DROP TABLE IF EXISTS `fournisseur`;
CREATE TABLE IF NOT EXISTS `fournisseur` (
  `id_fournisseur` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `contact` varchar(100) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_fournisseur`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `fournisseur`
--

INSERT INTO `fournisseur` (`id_fournisseur`, `nom`, `contact`, `telephone`, `adresse`, `email`) VALUES
(1, 'nina', 'Paul', '699999999', 'douala', 'nina@gmail.com'),
(2, 'paul', 'Alice', '688888888', 'younde', 'paul@gmail.com'),
(3, 'natacha', NULL, '675773387', 'Garoua', 'n2058694@gmail.com'),
(4, 'Dupons', NULL, '698754215', 'Douala', 'dupons@gmail.com'),
(5, 'sindy', NULL, '658754212', 'Yaoundé', 'sindy@gmail.com'),
(6, 'dupons', NULL, '698754215', 'Limbé', 'dupons@gmail.com');

-- --------------------------------------------------------

--
-- Structure de la table `privilege`
--

DROP TABLE IF EXISTS `privilege`;
CREATE TABLE IF NOT EXISTS `privilege` (
  `id_privilege` int NOT NULL AUTO_INCREMENT,
  `nom_privilege` varchar(100) NOT NULL,
  PRIMARY KEY (`id_privilege`),
  UNIQUE KEY `nom_privilege` (`nom_privilege`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `privilege`
--

INSERT INTO `privilege` (`id_privilege`, `nom_privilege`) VALUES
(10, 'Accéder aux rapports'),
(5, 'Accéder et configurer rapports'),
(6, 'Configurer données système'),
(1, 'Configurer paramètres système'),
(9, 'Consulter et éditer produits'),
(2, 'Définir seuils qualité'),
(12, 'Enregistrer données de production'),
(11, 'Enregistrer produits et types de produits'),
(13, 'Gérer la production'),
(3, 'Gérer utilisateurs'),
(7, 'Maintenance et mise à jour système'),
(14, 'Modifier un produit'),
(8, 'Saisir résultats analyses'),
(4, 'Surveiller processus');

-- --------------------------------------------------------

--
-- Structure de la table `production`
--

DROP TABLE IF EXISTS `production`;
CREATE TABLE IF NOT EXISTS `production` (
  `id_production` int NOT NULL AUTO_INCREMENT,
  `id_produit` int DEFAULT NULL,
  `date_production` date DEFAULT NULL,
  `quantite_produite` int DEFAULT NULL,
  PRIMARY KEY (`id_production`),
  KEY `id_produit` (`id_produit`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `production`
--

INSERT INTO `production` (`id_production`, `id_produit`, `date_production`, `quantite_produite`) VALUES
(1, 1, '2025-01-10', 100),
(2, 2, '2025-01-15', 200),
(3, 1, '2025-09-04', 50),
(4, 4, '2025-09-05', 500),
(5, 1, '2025-09-04', 50);

-- --------------------------------------------------------

--
-- Structure de la table `produit`
--

DROP TABLE IF EXISTS `produit`;
CREATE TABLE IF NOT EXISTS `produit` (
  `id_produit` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `id_fournisseur` int DEFAULT NULL,
  PRIMARY KEY (`id_produit`),
  KEY `id_fournisseur` (`id_fournisseur`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `produit`
--

INSERT INTO `produit` (`id_produit`, `nom`, `prix`, `id_fournisseur`) VALUES
(1, 'Produit X', 1000.00, 1),
(2, 'Produit Y', 2500.00, 3),
(3, 'produit Z', 500000.00, 2),
(4, 'produit H', 2589621.00, 4);

-- --------------------------------------------------------

--
-- Structure de la table `rapport`
--

DROP TABLE IF EXISTS `rapport`;
CREATE TABLE IF NOT EXISTS `rapport` (
  `id_rapport` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(150) DEFAULT NULL,
  `contenu` text,
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  `id_utilisateur` int DEFAULT NULL,
  PRIMARY KEY (`id_rapport`),
  KEY `id_utilisateur` (`id_utilisateur`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `rapport`
--

INSERT INTO `rapport` (`id_rapport`, `titre`, `contenu`, `date_creation`, `id_utilisateur`) VALUES
(5, 'Stock (auto)', '???? hÉtat du stock :\n- Produit X : 500000 unités\n- Produit Y : 20000 unités\n- produit Z : 30000 unités\n- produit H : 50000 unités\n', '2025-09-25 09:07:26', NULL),
(6, 'Commandes (auto)', '???? Résumé des commandes :\n- Nombre : 5\n- Montant total : 6000.00 FCFA\n', '2025-09-25 10:29:35', NULL),
(8, 'Fournisseurs (auto)', '???? Liste des fournisseurs :\n- nina | Contact: Paul | Email: nina@gmail.com\n- paul | Contact: Alice | Email: paul@gmail.com\n- natacha | Contact:  | Email: n2058694@gmail.com\n- Dupons | Contact:  | Email: dupons@gmail.com\n- sindy | Contact:  | Email: sindy@gmail.com\n- dupons | Contact:  | Email: dupons@gmail.com\n', '2025-09-25 11:08:22', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `role`
--

DROP TABLE IF EXISTS `role`;
CREATE TABLE IF NOT EXISTS `role` (
  `id_role` int NOT NULL AUTO_INCREMENT,
  `nom_role` varchar(50) NOT NULL,
  PRIMARY KEY (`id_role`),
  UNIQUE KEY `nom_role` (`nom_role`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `role`
--

INSERT INTO `role` (`id_role`, `nom_role`) VALUES
(1, 'Administrateur'),
(2, 'operateur'),
(3, 'Utilisateur');

-- --------------------------------------------------------

--
-- Structure de la table `role_privilege`
--

DROP TABLE IF EXISTS `role_privilege`;
CREATE TABLE IF NOT EXISTS `role_privilege` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_role` int DEFAULT NULL,
  `id_privilege` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_role` (`id_role`),
  KEY `id_privilege` (`id_privilege`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `role_privilege`
--

INSERT INTO `role_privilege` (`id`, `id_role`, `id_privilege`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 1, 5),
(6, 1, 6),
(7, 1, 7),
(8, 2, 8),
(9, 2, 9),
(10, 2, 10),
(11, 2, 11),
(12, 3, 12),
(13, 3, 13),
(14, 3, 14),
(15, 1, 10),
(16, 1, 5),
(17, 1, 6),
(18, 1, 1),
(19, 1, 9),
(20, 1, 2),
(21, 1, 12),
(22, 1, 11),
(23, 1, 13),
(24, 1, 3),
(25, 1, 7),
(26, 1, 14),
(27, 1, 8),
(28, 1, 4),
(30, 2, 4),
(31, 2, 5),
(32, 2, 6),
(33, 2, 7),
(34, 2, 8),
(35, 2, 9),
(36, 3, 9);

-- --------------------------------------------------------

--
-- Structure de la table `stock`
--

DROP TABLE IF EXISTS `stock`;
CREATE TABLE IF NOT EXISTS `stock` (
  `id_stock` int NOT NULL AUTO_INCREMENT,
  `id_produit` int DEFAULT NULL,
  `quantite` int DEFAULT '0',
  PRIMARY KEY (`id_stock`),
  UNIQUE KEY `id_produit` (`id_produit`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `stock`
--

INSERT INTO `stock` (`id_stock`, `id_produit`, `quantite`) VALUES
(1, 1, 500000),
(2, 2, 20000),
(3, 3, 30000),
(4, 4, 50000);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id_utilisateur` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `id_role` int DEFAULT NULL,
  PRIMARY KEY (`id_utilisateur`),
  UNIQUE KEY `email` (`email`),
  KEY `id_role` (`id_role`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id_utilisateur`, `nom`, `email`, `mot_de_passe`, `id_role`) VALUES
(1, 'Admin', 'admin@example.com', '$2y$10$HtJmXzBggdFFa58jeErjo.QBiLtxZI3t8MbmBXsodDgiDHy/CLdF6', 1),
(2, 'operateur', 'manager@example.com', '$2y$10$PwbAKbNLvEjE5FRseI67UuVxzsadYbRCJam.p2TKwF5QYHtnA0IfG', 2),
(3, 'utilisateur', 'user@example.com', '$2y$10$WaRYw5EnKmmxU7bRkM1wCuRKln6BT2meXc3hfT/ukjOvzYKCpO3P6', 3);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `commande`
--
ALTER TABLE `commande`
  ADD CONSTRAINT `commande_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `client` (`id_client`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `commande_produit`
--
ALTER TABLE `commande_produit`
  ADD CONSTRAINT `commande_produit_ibfk_1` FOREIGN KEY (`id_commande`) REFERENCES `commande` (`id_commande`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `commande_produit_ibfk_2` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `production`
--
ALTER TABLE `production`
  ADD CONSTRAINT `production_ibfk_1` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `produit`
--
ALTER TABLE `produit`
  ADD CONSTRAINT `produit_ibfk_1` FOREIGN KEY (`id_fournisseur`) REFERENCES `fournisseur` (`id_fournisseur`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `rapport`
--
ALTER TABLE `rapport`
  ADD CONSTRAINT `rapport_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `role_privilege`
--
ALTER TABLE `role_privilege`
  ADD CONSTRAINT `role_privilege_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `role` (`id_role`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `role_privilege_ibfk_2` FOREIGN KEY (`id_privilege`) REFERENCES `privilege` (`id_privilege`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `stock`
--
ALTER TABLE `stock`
  ADD CONSTRAINT `stock_ibfk_1` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD CONSTRAINT `utilisateur_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `role` (`id_role`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
