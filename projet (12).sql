-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 18 fév. 2026 à 15:55
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

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
-- Structure de la table `emplacement`
--

CREATE TABLE `emplacement` (
  `emp_id` int(11) NOT NULL,
  `emp_localisation` varchar(100) DEFAULT NULL,
  `emp_batiment` varchar(50) DEFAULT NULL,
  `emp_etage` varchar(20) DEFAULT NULL,
  `emp_num_bureau` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `emplacement`
--

INSERT INTO `emplacement` (`emp_id`, `emp_localisation`, `emp_batiment`, `emp_etage`, `emp_num_bureau`) VALUES
(1, 'Alger', 'Bâtiment A', '1', '101'),
(2, 'Alger', 'Bâtiment B', '2', '202'),
(3, 'Oran', 'Bâtiment C', '0', '10'),
(4, 'Oran', 'Bâtiment D', '3', '305'),
(5, 'Bejaia', 'Bâtiment E', '1', '110'),
(6, 'Bejaia', 'Bâtiment F', '2', '210'),
(7, 'Constantine', 'Bâtiment G', '4', '402'),
(8, 'Annaba', 'Bâtiment H', '2', '205');

-- --------------------------------------------------------

--
-- Structure de la table `intervention`
--

CREATE TABLE `intervention` (
  `int_id` int(11) NOT NULL,
  `int_type` varchar(50) DEFAULT NULL,
  `int_date` date DEFAULT NULL,
  `int_desc` text DEFAULT NULL,
  `int_status` varchar(50) DEFAULT NULL,
  `piece_remplacee` varchar(100) DEFAULT NULL,
  `operations_effectuees` text DEFAULT NULL,
  `tic_id` int(11) DEFAULT NULL,
  `tech_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `materiel`
--

CREATE TABLE `materiel` (
  `mat_code` int(11) NOT NULL,
  `mat_categ` enum('Ordinateur','PC Portable','Écran','Imprimante','Scanner') NOT NULL,
  `mat_marque` varchar(50) DEFAULT NULL,
  `mat_statut` enum('Disponible','Attribué','En panne','Réformé','Perdu') DEFAULT 'Disponible',
  `mat_fin_gar` date DEFAULT NULL,
  `usr_id` int(11) NOT NULL,
  `emp_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `materiel`
--

INSERT INTO `materiel` (`mat_code`, `mat_categ`, `mat_marque`, `mat_statut`, `mat_fin_gar`, `usr_id`, `emp_id`) VALUES
(1545112, 'Imprimante', 'hp', 'Réformé', '2026-02-15', 19, 1);

-- --------------------------------------------------------

--
-- Structure de la table `structure`
--

CREATE TABLE `structure` (
  `str_id` int(11) NOT NULL,
  `str_name` varchar(100) DEFAULT NULL,
  `str_type` enum('departement','centre','service') NOT NULL,
  `parent_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `structure`
--

INSERT INTO `structure` (`str_id`, `str_name`, `str_type`, `parent_id`) VALUES
(1, 'Département Informatique', 'departement', NULL),
(2, 'Département Ressources Humaines', 'departement', NULL),
(3, 'Département Finance', 'departement', NULL),
(4, 'Centre Réseaux', 'centre', 1),
(5, 'Centre Développement', 'centre', 1),
(6, 'Centre Recrutement', 'centre', 2),
(7, 'Centre Paie', 'centre', 3),
(8, 'Service Support', 'service', 1),
(9, 'Service Sécurité Informatique', 'service', 1),
(10, 'Service Recrutement', 'service', 2),
(11, 'Service Formation Interne', 'service', 2),
(12, 'Service Comptabilité', 'service', 3),
(13, 'Service Audit', 'service', 3);

-- --------------------------------------------------------

--
-- Structure de la table `technicien`
--

CREATE TABLE `technicien` (
  `tech_id` int(11) NOT NULL,
  `tech_nom` varchar(100) DEFAULT NULL,
  `tech_tel_int` varchar(20) DEFAULT NULL,
  `tech_email` varchar(100) DEFAULT NULL,
  `tech_password` varchar(255) DEFAULT NULL,
  `tech_role` varchar(50) DEFAULT NULL,
  `tech_date_creation` date DEFAULT NULL,
  `tech_date_update` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `technicien`
--

INSERT INTO `technicien` (`tech_id`, `tech_nom`, `tech_tel_int`, `tech_email`, `tech_password`, `tech_role`, `tech_date_creation`, `tech_date_update`) VALUES
(1, 'mourad123', NULL, 'moura9@gmail.com', '$2y$10$capjLFGkGMzyhTJX46Xd2.bj7I2UPCEb4uGDc4vktSh6i4UIAY8ni', 'technicien', '2026-02-10', '2026-02-10'),
(2, 'azezae', NULL, 'azzazeaeae@gmail.com', '$2y$10$wDhoDzrktos9pce0h4dYSOxcJ8isOaKQDOpYsY3eCXRxt822a71ku', 'technicien', '2026-02-10', '2026-02-10'),
(3, 'azezee', NULL, 'a@gmail.com', '$2y$10$zs/AhH47y0hmq0sm/MV7CeY54rJ3VZhPEykv558DraLVNNVnAgPI.', 'technicien', '2026-02-10', '2026-02-10'),
(4, 'Admin', NULL, 'admin@gmail.com', '$2y$10$Wh1OzTNrafP8w7aWDMcg5u4Ez3vwRZ3E5e957aXZabZ4igPOgtJyq', 'admin', '2026-02-10', '2026-02-10');

-- --------------------------------------------------------

--
-- Structure de la table `ticket`
--

CREATE TABLE `ticket` (
  `tic_id` int(11) NOT NULL,
  `tic_type` varchar(50) DEFAULT NULL,
  `tic_statut` varchar(50) DEFAULT NULL,
  `tic_description` text DEFAULT NULL,
  `tic_urgence` varchar(20) DEFAULT NULL,
  `tic_date` date DEFAULT NULL,
  `usr_id` int(11) DEFAULT NULL,
  `mat_inv` varchar(50) DEFAULT NULL,
  `tech_id` int(11) DEFAULT NULL,
  `mat_code` int(11) DEFAULT NULL,
  `tic_demande` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `ticket`
--

INSERT INTO `ticket` (`tic_id`, `tic_type`, `tic_statut`, `tic_description`, `tic_urgence`, `tic_date`, `usr_id`, `mat_inv`, `tech_id`, `mat_code`, `tic_demande`) VALUES
(1, 'intranet', 'nouveau', NULL, '1', '2026-02-18', 20, NULL, NULL, NULL, 'mourad13'),
(2, 'materiel', 'nouveau', 'okn', '1', '2026-02-18', 20, NULL, NULL, NULL, 'mourad13');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `usr_id` int(11) NOT NULL,
  `usr_nom` varchar(100) DEFAULT NULL,
  `usr_prenom` varchar(100) NOT NULL,
  `usr_telephone` varchar(20) DEFAULT NULL,
  `usr_email` varchar(100) DEFAULT NULL,
  `usr_password` varchar(255) DEFAULT NULL,
  `usr_date_creation` date DEFAULT NULL,
  `usr_date_update` date DEFAULT NULL,
  `str_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`usr_id`, `usr_nom`, `usr_prenom`, `usr_telephone`, `usr_email`, `usr_password`, `usr_date_creation`, `usr_date_update`, `str_id`) VALUES
(19, 'mourad', 'k', '15131', 'mourad1@gmail.com', '$2y$10$hJp82j1SXhx9qO8VxtfV7Om7Lqy0C0SUv71oEsMWUL5ZwKpk7TY..', '2026-02-12', '2026-02-18', 5),
(20, 'mourad13', '', NULL, 'mourad2@gmail.com', '$2y$10$6lcX5XrfvTbkgukJgpGTZ.YCg5PNdHMc/V3cvU.FgbB5AR2IXzHMS', '2026-02-18', '2026-02-18', 12),
(21, 'mourad135', '', NULL, 'mourad3@gmail.com', '$2y$10$J6pBmaJLHFjbVB7pX66eMurP1oVs2XE4xdIqsLPjDEpgbtiqnIuS2', '2026-02-18', '2026-02-18', 3),
(22, 'mourad', 'boudali', NULL, 'mouradboudali99@gmail.com', '$2y$10$SkIHNQYbWWBap9RE.ldOUuZmUngWpCM6CbtWz4PrtDY.u0iPgR43u', '2026-02-18', '2026-02-18', 8);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `emplacement`
--
ALTER TABLE `emplacement`
  ADD PRIMARY KEY (`emp_id`);

--
-- Index pour la table `intervention`
--
ALTER TABLE `intervention`
  ADD PRIMARY KEY (`int_id`),
  ADD KEY `fk_intervention_ticket` (`tic_id`),
  ADD KEY `fk_intervention_technicien` (`tech_id`);

--
-- Index pour la table `materiel`
--
ALTER TABLE `materiel`
  ADD PRIMARY KEY (`mat_code`),
  ADD KEY `fk_materiel_emplacement` (`emp_id`),
  ADD KEY `fk_materiel_user` (`usr_id`);

--
-- Index pour la table `structure`
--
ALTER TABLE `structure`
  ADD PRIMARY KEY (`str_id`),
  ADD KEY `fk_structure_parent` (`parent_id`);

--
-- Index pour la table `technicien`
--
ALTER TABLE `technicien`
  ADD PRIMARY KEY (`tech_id`);

--
-- Index pour la table `ticket`
--
ALTER TABLE `ticket`
  ADD PRIMARY KEY (`tic_id`),
  ADD KEY `fk_ticket_user` (`usr_id`),
  ADD KEY `fk_ticket_technicien` (`tech_id`),
  ADD KEY `fk_ticket_materiel` (`mat_code`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`usr_id`),
  ADD KEY `fk_user_structure` (`str_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `emplacement`
--
ALTER TABLE `emplacement`
  MODIFY `emp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `technicien`
--
ALTER TABLE `technicien`
  MODIFY `tech_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `ticket`
--
ALTER TABLE `ticket`
  MODIFY `tic_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `usr_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `intervention`
--
ALTER TABLE `intervention`
  ADD CONSTRAINT `fk_intervention_technicien` FOREIGN KEY (`tech_id`) REFERENCES `technicien` (`tech_id`),
  ADD CONSTRAINT `fk_intervention_ticket` FOREIGN KEY (`tic_id`) REFERENCES `ticket` (`tic_id`);

--
-- Contraintes pour la table `materiel`
--
ALTER TABLE `materiel`
  ADD CONSTRAINT `fk_materiel_emplacement` FOREIGN KEY (`emp_id`) REFERENCES `emplacement` (`emp_id`),
  ADD CONSTRAINT `fk_materiel_user` FOREIGN KEY (`usr_id`) REFERENCES `user` (`usr_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `structure`
--
ALTER TABLE `structure`
  ADD CONSTRAINT `fk_structure_parent` FOREIGN KEY (`parent_id`) REFERENCES `structure` (`str_id`);

--
-- Contraintes pour la table `ticket`
--
ALTER TABLE `ticket`
  ADD CONSTRAINT `fk_ticket_materiel` FOREIGN KEY (`mat_code`) REFERENCES `materiel` (`mat_code`),
  ADD CONSTRAINT `fk_ticket_technicien` FOREIGN KEY (`tech_id`) REFERENCES `technicien` (`tech_id`),
  ADD CONSTRAINT `fk_ticket_user` FOREIGN KEY (`usr_id`) REFERENCES `user` (`usr_id`);

--
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_user_structure` FOREIGN KEY (`str_id`) REFERENCES `structure` (`str_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
