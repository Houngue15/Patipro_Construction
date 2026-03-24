<?php
/**
 * BatiPro Construction - Configuration de la base de donnees
 */

// --- Parametres de connexion MySQL ---
define('DB_HOST', 'localhost');
define('DB_NAME', 'batipro_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

/**
 * Etablit et retourne une connexion PDO a la base de donnees MySQL.
 * Active le mode exception pour la gestion des erreurs.
 *
 * @return PDO Instance de connexion PDO
 * @throws PDOException En cas d'echec de connexion
 */
function getDbConnection(): PDO
{
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    return new PDO($dsn, DB_USER, DB_PASS, $options);
}
