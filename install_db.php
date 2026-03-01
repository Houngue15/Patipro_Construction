<?php
/**
 * BatiPro Construction - Script d'installation de la base de donnees
 * 
 * A executer UNE SEULE FOIS lors de l'installation.
 * 
 

require_once __DIR__ . '/config.php';

try {
    $pdo = getDbConnection();

    // ============================================================
    // Table : contacts
    // Stocke les messages du formulaire de contact
    // ============================================================
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS contacts (
            id          INT AUTO_INCREMENT PRIMARY KEY,
            prenom      VARCHAR(100)  NOT NULL,
            nom         VARCHAR(100)  NOT NULL,
            email       VARCHAR(255)  NOT NULL,
            telephone   VARCHAR(30)   DEFAULT NULL,
            service     VARCHAR(50)   DEFAULT NULL,
            message     TEXT          NOT NULL,
            ip_address  VARCHAR(45)   DEFAULT NULL,
            lu          TINYINT(1)    NOT NULL DEFAULT 0,
            created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_email (email),
            INDEX idx_created (created_at),
            INDEX idx_lu (lu)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // ============================================================
    // Table : newsletter
    // Stocke les inscriptions a la newsletter
    // ============================================================
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS newsletter (
            id          INT AUTO_INCREMENT PRIMARY KEY,
            email       VARCHAR(255)  NOT NULL UNIQUE,
            actif       TINYINT(1)    NOT NULL DEFAULT 1,
            created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_email (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    echo "Installation terminee avec succes !\n";
    echo "Tables creees : contacts, newsletter\n";
    echo "\n** IMPORTANT : Supprimez ce fichier (install_db.php) apres l'installation pour des raisons de securite. **\n";

} catch (PDOException $e) {
    echo "Erreur lors de l'installation : " . $e->getMessage() . "\n";
    exit(1);
}
