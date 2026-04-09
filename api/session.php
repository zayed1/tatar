<?php
// Creates a PHP session from API token - used by WebView to establish cookies
require_once(__DIR__ . '/config.php');

$token = $_GET['token'] ?? '';
if (!$token) {
    http_response_code(401);
    echo 'Missing token';
    exit;
}

$player = verifyToken($token);
if (!$player) {
    http_response_code(401);
    echo 'Invalid token';
    exit;
}

// Start PHP session and set player data (same as login.php does)
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$playerId = intval($player['id']);
$playerName = $player['name'];

// Load game config
require_once(dirname(__DIR__) . '/core-f/config-f/s1.php');
$db_port = isset($GLOBALS['AppConfig']['db']['port']) ? intval($GLOBALS['AppConfig']['db']['port']) : 3306;
$link = mysqli_connect($GLOBALS['AppConfig']['db']['host'], $GLOBALS['AppConfig']['db']['user'], $GLOBALS['AppConfig']['db']['password'], $GLOBALS['AppConfig']['db']['database'], $db_port);

if ($link) {
    $result = mysqli_query($link, "SELECT pwd, pwd1 FROM p_players WHERE id=$playerId");
    $row = mysqli_fetch_assoc($result);
    if ($row) {
        // Set session like normal login
        $_SESSION['pwd'] = md5($row['pwd1'] ?? '');
        $_SESSION['is_rig'] = $playerName;

        // Set Player object in session
        require_once(dirname(__DIR__) . '/core-f/components.php');
        $p = new Player();
        $p->playerId = $playerId;
        $p->isAgent = 0;
        $p->gameStatus = 0;
        $p->save();

        // Set cookie
        $key = Player::getKey();
        require_once(dirname(__DIR__) . '/core-f/sql-f/webhelper.php');

        // Update session
        $usersession = session_id();
        mysqli_query($link, "UPDATE p_players SET UserSession='$usersession', last_login_date=NOW() WHERE id=$playerId");
    }
    mysqli_close($link);
}

// Redirect to requested page
$redirect = $_GET['redirect'] ?? 'village1';
header('Location: /' . $redirect . '?platform=app');
exit;
