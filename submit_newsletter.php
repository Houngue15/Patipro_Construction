<?php
/**
 * BatiPro Construction - Traitement de l'inscription newsletter
 * 
 * Recoit un email en POST (JSON), valide, sanitize,
 * et insere en base de donnees MySQL.
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

if (!$data) {
    $data = $_POST;
}

// --- Extraction et validation ---
$email = isset($data['email']) ? htmlspecialchars(trim(strip_tags($data['email'])), ENT_QUOTES, 'UTF-8') : '';

if (empty($email)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'error' => 'L\'adresse email est requise.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'error' => 'L\'adresse email n\'est pas valide.']);
    exit;
}

// --- Insertion en base de donnees ---
try {
    $pdo = getDbConnection();
    
    // Verifier si l'email existe deja
    $checkStmt = $pdo->prepare('SELECT id FROM newsletter WHERE email = :email');
    $checkStmt->execute([':email' => $email]);
    
    if ($checkStmt->fetch()) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Cette adresse email est deja inscrite a notre newsletter.'
        ]);
        exit;
    }
    
    $stmt = $pdo->prepare(
        'INSERT INTO newsletter (email, created_at) VALUES (:email, NOW())'
    );
    
    $stmt->execute([':email' => $email]);
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Merci pour votre inscription a notre newsletter !'
    ]);
    
} catch (PDOException $e) {
    error_log('BatiPro - Erreur newsletter : ' . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Une erreur interne est survenue. Veuillez reessayer plus tard.'
    ]);
}
