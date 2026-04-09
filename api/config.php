<?php
// API Configuration
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

define('API_SECRET', 'tatar_api_2026_secret_key');

// Load app config
require_once(dirname(__FILE__) . '/../core-f/config-f/s1.php');

function getDb() {
    global $AppConfig, $_apiDb;
    $port = isset($AppConfig['db']['port']) ? intval($AppConfig['db']['port']) : 3306;
    $conn = mysqli_connect(
        $AppConfig['db']['host'],
        $AppConfig['db']['user'],
        $AppConfig['db']['password'],
        $AppConfig['db']['database'],
        $port
    );
    if (!$conn) {
        jsonError('Database connection failed', 500);
    }
    mysqli_set_charset($conn, 'utf8mb4');
    $_apiDb = $conn;
    return $conn;
}

function generateToken($playerId, $pwdHash) {
    $payload = $playerId . ':' . hash('sha256', $playerId . ':' . $pwdHash . ':' . API_SECRET);
    return base64_encode($payload);
}

function verifyToken($token) {
    $decoded = base64_decode($token);
    if (!$decoded || strpos($decoded, ':') === false) return null;

    $parts = explode(':', $decoded, 2);
    $playerId = intval($parts[0]);
    $hash = $parts[1];

    if ($playerId <= 0) return null;

    $db = getDb();
    $playerId = mysqli_real_escape_string($db, $playerId);
    $result = mysqli_query($db, "SELECT id, pwd, name FROM p_players WHERE id='$playerId'");
    $player = mysqli_fetch_assoc($result);
    mysqli_close($db);

    if (!$player) return null;

    $expected = hash('sha256', $player['id'] . ':' . $player['pwd'] . ':' . API_SECRET);
    if (!hash_equals($expected, $hash)) return null;

    return $player;
}

function requireAuth() {
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (strpos($header, 'Bearer ') !== 0) {
        jsonError('Unauthorized', 401);
    }
    $token = substr($header, 7);
    $player = verifyToken($token);
    if (!$player) {
        jsonError('Invalid token', 401);
    }
    return $player;
}

function closeDb() {
    global $_apiDb;
    if (isset($_apiDb) && $_apiDb) {
        mysqli_close($_apiDb);
        $_apiDb = null;
    }
}

function jsonSuccess($data) {
    closeDb();
    echo json_encode(['ok' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
    exit;
}

function jsonError($message, $code = 400) {
    closeDb();
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}
