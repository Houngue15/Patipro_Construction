<?php
/**
 * BatiPro Construction - Traitement du formulaire de contact
 * 
 * Recoit les donnees du formulaire en POST (JSON),
 * valide, sanitize, et insere en base de donnees MySQL.
 * Retourne une reponse JSON.
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Gestion des requetes preflight CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Accepter uniquement les requetes POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Methode non autorisee. Utilisez POST.']);
    exit;
}

require_once __DIR__ . '/config.php';

// --- Lecture du corps de la requete ---
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);
error_log(print_r($data, true));

if (!$data) {
    // Fallback: essayer les donnees POST classiques (formulaire standard)
    $data = $_POST;
}

// --- Fonctions de validation et sanitization ---

/**
 * Nettoie une chaine : supprime balises HTML, espaces superflus
 */
function sanitizeString(string $value): string
{
    return htmlspecialchars(trim(strip_tags($value)), ENT_QUOTES, 'UTF-8');
}

/**
 * Valide un email
 */
function isValidEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valide un numero de telephone (format international ou francais)
 */
function isValidPhone(string $phone): bool
{
    if (empty($phone)) return true; // Le telephone est optionnel
    return (bool) preg_match('/^[\+]?[0-9\s\-\.\(\)]{7,20}$/', $phone);
}

/**
 * Valide le choix du service
 */
function isValidService(string $service): bool
{
    $allowedServices = ['', 'batiment', 'route', 'location', 'autre'];
    return in_array($service, $allowedServices, true);
}

// --- Extraction et sanitization des champs ---
$prenom    = isset($data['prenom'])    ? sanitizeString($data['prenom'])    : '';
$nom       = isset($data['nom'])       ? sanitizeString($data['nom'])       : '';
$email     = isset($data['email'])     ? sanitizeString($data['email'])     : '';
$telephone = isset($data['telephone']) ? sanitizeString($data['telephone']) : '';
$service   = isset($data['service'])   ? sanitizeString($data['service'])   : '';
$message   = isset($data['message'])   ? sanitizeString($data['message'])   : '';

// --- Validation ---
$errors = [];

if (empty($prenom)) {
    $errors[] = 'Le prenom est requis.';
} elseif (mb_strlen($prenom) > 100) {
    $errors[] = 'Le prenom ne doit pas depasser 100 caracteres.';
}

if (empty($nom)) {
    $errors[] = 'Le nom est requis.';
} elseif (mb_strlen($nom) > 100) {
    $errors[] = 'Le nom ne doit pas depasser 100 caracteres.';
}

if (empty($email)) {
    $errors[] = 'L\'email est requis.';
} elseif (!isValidEmail($email)) {
    $errors[] = 'L\'adresse email n\'est pas valide.';
}

if (!isValidPhone($telephone)) {
    $errors[] = 'Le numero de telephone n\'est pas valide.';
}

if (!isValidService($service)) {
    $errors[] = 'Le service selectionne n\'est pas valide.';
}

if (empty($message)) {
    $errors[] = 'Le message est requis.';
} elseif (mb_strlen($message) > 5000) {
    $errors[] = 'Le message ne doit pas depasser 5000 caracteres.';
}

// S'il y a des erreurs de validation
if (!empty($errors)) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'errors'  => $errors
    ]);
    exit;
}

// --- Insertion en base de donnees ---
try {
    $pdo = getDbConnection();
    
    $stmt = $pdo->prepare(
        'INSERT INTO contacts (prenom, nom, email, telephone, service, message, ip_address, created_at)
         VALUES (:prenom, :nom, :email, :telephone, :service, :message, :ip_address, NOW())'
    );
    
    $stmt->execute([
        ':prenom'     => $prenom,
        ':nom'        => $nom,
        ':email'      => $email,
        ':telephone'  => $telephone ?: null,
        ':service'    => $service ?: null,
        ':message'    => $message,
        ':ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
    ]);
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Votre message a ete envoye avec succes. Notre equipe vous repondra dans les plus brefs delais.'
    ]);
    
} catch (PDOException $e) {
    // En production, ne pas exposer le message d'erreur SQL
    error_log('BatiPro - Erreur base de donnees : ' . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Une erreur interne est survenue. Veuillez reessayer plus tard.'
    ]);
}
