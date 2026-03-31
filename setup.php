<?php
// Standalone installer for first-time setup on Railway

// -----------------------------------------------------------------------
// SECURITY GATE: This file is blocked by default in production.
// To enable it temporarily, set the environment variable before running:
//   export ALLOW_SETUP=true   (Linux/macOS shell or Railway/Docker env)
// Remove or unset the variable again once setup is complete.
// -----------------------------------------------------------------------
if (getenv('ALLOW_SETUP') !== 'true') {
    die('Access Denied');
}

error_reporting(E_ALL & ~E_WARNING & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 1);
ini_set('opcache.enable', 0);
set_time_limit(300);

define("ROOT_PATH", realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
define("APP_PATH", ROOT_PATH . "core-f" . DIRECTORY_SEPARATOR);
define("LIB_PATH", ROOT_PATH . "core-f/sql-f" . DIRECTORY_SEPARATOR);
define("MODEL_PATH", APP_PATH . "mod-f" . DIRECTORY_SEPARATOR);
define("VIEW_PATH", APP_PATH . "ph-f" . DIRECTORY_SEPARATOR);

date_default_timezone_set('Asia/Kuwait');

require(APP_PATH . "config-f/s1.php");
require_once(APP_PATH . "components.php");
require_once(MODEL_PATH . "base.php");

// Security check - must provide install key
$key = isset($_GET['key']) ? $_GET['key'] : '';
$installKey = $GLOBALS['AppConfig']['system']['installkey'];

if ($key !== $installKey) {
    die('<h2>Access Denied</h2><p>Usage: setup.php?key=YOUR_INSTALL_KEY</p>');
}

// Quick admin action: make player admin
if (isset($_GET['make_admin'])) {
    $playerName = $_GET['make_admin'];
    $db = new ModelBase();
    $db->provider->properties = $GLOBALS['AppConfig']['db'];
    $db->provider->executeQuery("UPDATE p_players SET player_type=2 WHERE name='%s'", array($playerName));
    echo "<h2>Done! Player '$playerName' is now admin (player_type=2)</h2>";
    echo "<p><a href='login.php'>Go to login</a></p>";
    $db->dispose();
    exit;
}

echo "<h1>Tatar Game - Database Setup</h1>";
echo "<pre>";

// Test database connection first
echo "Testing database connection...\n";
$db = new ModelBase();
$db->provider->properties = $GLOBALS['AppConfig']['db'];
if (!$db->provider->open()) {
    die("ERROR: Cannot connect to database! Check your credentials.\n");
}
echo "OK - Connected to database successfully!\n\n";

// Load metadata
require(APP_PATH . "metadata.php");

// Remove install lock file if exists
$ifile = ROOT_PATH . "core-f/stx/install.txt";
if (file_exists($ifile)) {
    unlink($ifile);
    echo "Removed old install lock file.\n";
}

// Ensure stx directory exists
if (!is_dir(ROOT_PATH . "core-f/stx")) {
    mkdir(ROOT_PATH . "core-f/stx", 0777, true);
}

echo "Running database setup...\n";

require_once(MODEL_PATH . "register.php");
require_once(MODEL_PATH . "queue.php");
require_once(MODEL_PATH . "install.php");

$m = new SetupModel();
$map_size = $GLOBALS['SetupMetadata']['map_size'];
$adminEmail = $GLOBALS['AppConfig']['system']['admin_email'];

// Only run _createTables and _createMap for fresh install
$m->_createTables();
echo "Tables created!\n";

$m->_createMap($map_size);
echo "Map created (size: $map_size)!\n";

$m->_createBerq($map_size);
echo "Berq created!\n";

if ($m->_createAdminPlayer($map_size, $adminEmail)) {
    echo "Admin player created!\n";

    $raiseTime = (strtotime(Date('Y/m/d 20:00:00', strtotime("+" . $GLOBALS['AppConfig']['system']['artef'] . " days"))) - time());
    $raiseTime1 = (strtotime(Date('Y/m/d 20:00:00', strtotime("+" . $GLOBALS['AppConfig']['system']['tatar'] . " days"))) - time());
    $raiseTime2 = (strtotime(Date('Y/m/d 20:00:00', strtotime("+" . $GLOBALS['AppConfig']['system']['start'] . " days"))) - time());

    $queueModel = new QueueModel();
    $queueModel->addTask(new QueueTask(QS_TATAR_RAISE, 0, $raiseTime1));
    $queueModel->addTask(new QueueTask(QS_TATAR_ART, 0, $raiseTime));
    $queueModel->addTask(new QueueTask(QS_STOPGAME, 0, $raiseTime2));
    echo "Queue tasks created!\n";
}

$m->dispose();

echo "\n=============================\n";
echo "SETUP COMPLETE!\n";
echo "=============================\n";
echo "\nAdmin username: " . $GLOBALS['AppConfig']['system']['adminName'] . "\n";
echo "Admin email: $adminEmail\n";
echo "\nYou can now go to: /login\n";
echo "</pre>";
?>
