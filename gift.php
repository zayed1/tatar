<?php
require( ".".DIRECTORY_SEPARATOR."core-f".DIRECTORY_SEPARATOR."boot.php" );
#require_once( MODEL_PATH."admin.php" );
class GPage extends SecureGamePage
{

    public $packageIndex = -1;
    public $plusTable = NULL;

    public function __construct()
    {
        parent::__construct();
        $this->viewFile = "gift.phtml";
        $this->contentCssClass = "plus";

    }

    public function load( )
    {
        parent::load( );

    }



}

$p = new GPage( );
$p->run( );
?>

