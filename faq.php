<?php
require( ".".DIRECTORY_SEPARATOR."core-f".DIRECTORY_SEPARATOR."boot.php" );
class GPage extends ProcessVillagePage
{
public function __construct()
{
parent::__construct();
$this->viewFile = "faq.phtml";
$this->contentCssClass = "village1";
}
public function load( )
{
parent::load( );
}
}
$p = new GPage( );
$p->run( );
?>