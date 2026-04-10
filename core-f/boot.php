<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


ob_start();
//error_reporting(E_ALL ^ E_NOTICE);
//ini_set('display_errors', 'On');
define( "ROOT_PATH", realpath( dirname( dirname( __FILE__ ) ) ).DIRECTORY_SEPARATOR );
define( "APP_PATH", ROOT_PATH."core-f".DIRECTORY_SEPARATOR );
define( "LIB_PATH", ROOT_PATH."core-f/sql-f".DIRECTORY_SEPARATOR );
define( "MODEL_PATH", APP_PATH."mod-f".DIRECTORY_SEPARATOR );
define( "VIEW_PATH", APP_PATH."ph-f".DIRECTORY_SEPARATOR );
$sessionPath = APP_PATH . 'cache-f';
if (!is_dir($sessionPath)) { mkdir($sessionPath, 0777, true); }
ini_set('session.save_path', $sessionPath);
date_default_timezone_set('Asia/Kuwait');
ignore_user_abort( TRUE );
set_time_limit( 0 );

if ( isset( $_SERVER['HTTP_ACCEPT_ENCODING'] ) && substr_count( $_SERVER['HTTP_ACCEPT_ENCODING'], "gzip" ) )
{
    ob_implicit_flush( 0 );
    if ( @ob_start( array( "ob_gzhandler", 9 ) ) )
    {
        header( "Content-Encoding: gzip" );
    }
}


///shadi


// start corn job alq0rsan
$ifile = "core-f/mod-f/smart/cornjob.txt";
if(!file_exists($ifile))
{
$ifile=fopen($ifile, "w");fclose($ifile);
} else {
if(filectime($ifile) >= 5)
{
unlink($ifile);
$dir = 'core-f/mod-f/smart/qqw/'; 
foreach(glob($dir.'*') as $v){ 
$last_modified = filectime($v);
$fm[$v] = filectime($v);
}
if(isset($fm) && count($fm)>0)
{
asort($fm);
$k = 0;
foreach($fm as $key => $value)
{
$k++;
if ($k <= 5) {
if(time()-$value >= 5) {
unlink($key);
}
}
}
}
}
}


header( "Date: ".gmdate( "D, d M Y H:i:s" )." GMT" );
header( "Last-Modified: ".gmdate( "D, d M Y H:i:s" )." GMT" );
header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
require( LIB_PATH."webservice.php" );
require( LIB_PATH."webhelper.php" );
require( APP_PATH."components.php" );
$GLOBALS['cd'] = new ClientData();
$GLOBALS['wh'] = new WebHelper();
$cookie = $GLOBALS['cd']->getinstance( );
if (isset ($_GET['s1'])) {
setcookie( "con", "s1", time() + 60*60*24 );
$cookie->con = 's1';
} else 
if (isset ($_GET['s2'])) {
setcookie( "con", "s2", time() + 60*60*24 );
$cookie->con = 's2';
} else 
if (isset ($_GET['s3'])) {
setcookie( "con", "s3", time() + 60*60*24 );
$cookie->con = 's3';
} else 
if (isset ($_GET['s4'])) {
setcookie( "con", "s4", time() + 60*60*24 );
$cookie->con = 's4';
} else 
if (isset ($_GET['s5'])) {
setcookie( "con", "s5", time() + 60*60*24 );
$cookie->con = 's5';
}
if ($cookie->con == '') {
$cookie->con = 's1';
}
require( APP_PATH."config-f/".$cookie->con.".php" );
require( LIB_PATH."widget.php" );
require( APP_PATH."metadata.php" );
require( MODEL_PATH."base.php" );
require( APP_PATH."mywidgets.php" );
require( APP_PATH."cache.php" );
set_time_limit( 0 );
require( APP_PATH."ls-f/smartservs-ls-v7.php" );
$cookie = $GLOBALS['cd']->getinstance( );
$GLOBALS['AppConfig']['system']['lang'] = $cookie->uiLang;
require( APP_PATH."".$GLOBALS['AppConfig']['system']['lang'].".php" );

// API token auto-login for mobile app WebView
if (isset($_GET['api_token']) && !empty($_GET['api_token'])) {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    $tokenKey = Player::getKey();
    if (!isset($_SESSION[$tokenKey]) || $_SESSION[$tokenKey] === NULL) {
        $apiToken = $_GET['api_token'];
        $apiSecret = 'tatar_api_2026_secret_key';
        $decoded = base64_decode($apiToken);
        if ($decoded && strpos($decoded, ':') !== false) {
            $parts = explode(':', $decoded, 2);
            $tokenPlayerId = intval($parts[0]);
            $tokenHash = $parts[1];
            if ($tokenPlayerId > 0) {
                $db_port = isset($GLOBALS['AppConfig']['db']['port']) ? intval($GLOBALS['AppConfig']['db']['port']) : 3306;
                $tokenLink = @mysqli_connect($GLOBALS['AppConfig']['db']['host'], $GLOBALS['AppConfig']['db']['user'], $GLOBALS['AppConfig']['db']['password'], $GLOBALS['AppConfig']['db']['database'], $db_port);
                if ($tokenLink) {
                    $tr = mysqli_query($tokenLink, "SELECT id, name, pwd, pwd1 FROM p_players WHERE id=$tokenPlayerId");
                    $tp = mysqli_fetch_assoc($tr);
                    if ($tp) {
                        $expectedHash = hash('sha256', $tp['id'] . ':' . $tp['pwd'] . ':' . $apiSecret);
                        if (hash_equals($expectedHash, $tokenHash)) {
                            // Valid token - create session
                            $apiPlayer = new Player();
                            $apiPlayer->playerId = intval($tp['id']);
                            $apiPlayer->isAgent = 0;
                            $apiPlayer->gameStatus = 0;
                            $apiPlayer->save();
                            $_SESSION['pwd'] = md5($tp['pwd1'] ?? '');
                            $_SESSION['is_rig'] = $tp['name'];
                            // Set client cookie for persistence
                            $cookie->uname = $tp['name'];
                            $cookie->upwd = $tp['pwd1'] ?? '';
                            $cookie->save();
                        }
                    }
                    mysqli_close($tokenLink);
                }
            }
        }
    }
}

$tempdata = explode( " ", microtime( ) );
$data1 = $tempdata[0];
$data2 = $tempdata[1];
$__scriptStart = ( double )$data1 + ( double )$data2;
if($_GET)
{
        foreach($_GET as $key=>$value)
        {
            if(is_array($_GET[$key]))
            {
                   array_map('protect',$_GET[$key]);
            }
            else
            {
                  $_GET[$key] = stripslashes(htmlspecialchars(addslashes(trim($value))));
            }
        }
}
