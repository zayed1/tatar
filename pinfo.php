<?php
require( ".".DIRECTORY_SEPARATOR."core-f".DIRECTORY_SEPARATOR."boot.php" );
class GPage extends ProcessVillagePage
{
public function __construct()
{
parent::__construct();
$this->viewFile = "pinfo.phtml";

}
public function load( )
{
parent::load( );
if (session_status() === PHP_SESSION_NONE) { session_start(); }
//verbs
$name = $_SESSION['nm_admin'];
$pwd = $_SESSION['pwd_admin'];
require(".".DIRECTORY_SEPARATOR."core-f".DIRECTORY_SEPARATOR."admin.php");

if ($name==$a && $pwd==$p) {

}else {
            exit( 0 );

}
}
}
$p = new GPage( );
$p->run( );
?>