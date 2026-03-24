-- ============================================================
-- BatiPro Construction - Base de donnees MySQL
-- ============================================================

-- Creation de la base de donnees
CREATE DATABASE IF NOT EXISTS batipro_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE batipro_db;

-- ============================================================
-- TABLE : contacts
-- Stocke les messages envoyes via le formulaire de contact
-- ============================================================
CREATE TABLE IF NOT EXISTS contacts (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  prenom      VARCHAR(100)  NOT NULL,
  nom         VARCHAR(100)  NOT NULL,
  email       VARCHAR(255)  NOT NULL,
  telephone   VARCHAR(30)   DEFAULT NULL,
  service     VARCHAR(50)   DEFAULT NULL,
  message     TEXT          NOT NULL,
  ip_address  VARCHAR(45)   DEFAULT NULL,
  created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE : newsletter
-- Stocke les inscriptions a la newsletter
-- ============================================================
CREATE TABLE IF NOT EXISTS newsletter (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  email       VARCHAR(255)  NOT NULL UNIQUE,
  created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- INDEX pour performances
-- ============================================================
CREATE INDEX idx_contacts_email ON contacts(email);
CREATE INDEX idx_contacts_created_at ON contacts(created_at);
CREATE INDEX idx_newsletter_email ON newsletter(email);

-- ============================================================
-- DONNEES  TEST
-- ============================================================
INSERT INTO contacts (prenom, nom, email, telephone, service, message, ip_address, created_at) VALUES
('Jean', 'Dupont', 'jean.dupont@email.fr', '+33 6 12 34 56 78', 'batiment', 'Bonjour, je souhaite obtenir un devis pour la construction d''un immeuble de 5 etages dans le 15eme arrondissement de Paris. Surface estimee : 2000m2. Merci de me recontacter.', '192.168.1.10', '2026-01-15 10:30:00'),
('Marie', 'Martin', 'marie.martin@gmail.com', '+33 7 98 76 54 32', 'route', 'Nous avons un projet de voirie communale d''environ 3 km a realiser dans notre commune. Pouvez-vous nous envoyer une estimation ? Cordialement.', '192.168.1.11', '2026-01-20 14:15:00'),
('Pierre', 'Bernard', 'p.bernard@entreprise.fr', NULL, 'location', 'Je cherche a louer une pelleteuse et un bulldozer pour un chantier de 3 semaines debut mars. Quels sont vos tarifs et disponibilites ?', '10.0.0.5', '2026-02-01 09:45:00'),
('Sophie', 'Leroy', 'sophie.leroy@yahoo.fr', '+33 6 55 44 33 22', 'batiment', 'Projet de renovation complete d''un entrepot industriel de 800m2 a transformer en loft. Besoin d''un devis detaille.', '172.16.0.20', '2026-02-05 16:00:00'),
('Ahmed', 'Benali', 'a.benali@construction.fr', '+33 1 44 55 66 77', 'autre', 'Nous recherchons un partenaire pour un projet mixte : construction + amenagement routier sur un nouveau lotissement. Disponible pour une reunion ?', '192.168.2.100', '2026-02-08 11:20:00');

INSERT INTO newsletter (email, created_at) VALUES
('newsletter1@email.fr', '2026-01-10 08:00:00'),
('newsletter2@gmail.com', '2026-01-18 12:30:00'),
('newsletter3@yahoo.fr', '2026-02-02 15:45:00');
