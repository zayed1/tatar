<?php
// Creates a PHP session from API token - used by WebView
// This file does NOT use config.php JSON headers - it redirects instead

// Suppress errors for clean redirect
error_reporting(0);
ini_set('display_errors', '0');

define('API_SECRET', 'tatar_api_2026_secret_key');
require_once(dirname(__DIR__) . '/core-f/config-f/s1.php');

$token = $_GET['token'] ?? '';
$redirect = $_GET['redirect'] ?? 'village1';

if (!$token) {
    header('Location: /login?platform=app');
    exit;
}

// Verify token
$decoded = base64_decode($token);
if (!$decoded || strpos($decoded, ':') === false) {
    header('Location: /login?platform=app');
    exit;
}
$parts = explode(':', $decoded, 2);
$playerId = intval($parts[0]);
$hash = $parts[1];

if ($playerId <= 0) {
    header('Location: /login?platform=app');
    exit;
}

// Connect to DB
$db_port = isset($AppConfig['db']['port']) ? intval($AppConfig['db']['port']) : 3306;
$link = mysqli_connect($AppConfig['db']['host'], $AppConfig['db']['user'], $AppConfig['db']['password'], $AppConfig['db']['database'], $db_port);
if (!$link) {
    header('Location: /login?platform=app');
    exit;
}

// Verify token against DB
$result = mysqli_query($link, "SELECT id, pwd, pwd1, name FROM p_players WHERE id=$playerId");
$player = mysqli_fetch_assoc($result);
if (!$player) {
    mysqli_close($link);
    header('Location: /login?platform=app');
    exit;
}

$expected = hash('sha256', $player['id'] . ':' . $player['pwd'] . ':' . API_SECRET);
if (!hash_equals($expected, $hash)) {
    mysqli_close($link);
    header('Location: /login?platform=app');
    exit;
}

// Token valid - create PHP session
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$_SESSION['pwd'] = md5($player['pwd1'] ?? '');
$_SESSION['is_rig'] = $player['name'];

// Set Player object in session
require_once(dirname(__DIR__) . '/core-f/components.php');
$p = new Player();
$p->playerId = $playerId;
$p->isAgent = 0;
$p->gameStatus = 0;
$p->save();

// Set ClientData cookie (required by game pages)
$cookie = new ClientData();
$cookie->uname = $player['name'];
$cookie->upwd = $player['pwd1'] ?? '';
$cookieKey = $p->getKey();
setcookie($cookieKey, base64_encode(serialize($cookie)), time() + 86400 * 30, '/');

// Update session ID in DB
$usersession = session_id();
mysqli_query($link, "UPDATE p_players SET UserSession='$usersession', last_login_date=NOW() WHERE id=$playerId");
mysqli_close($link);

// Redirect to game page
header('Location: /' . $redirect . '?platform=app');
exit;
