<?php
require( ".".DIRECTORY_SEPARATOR."core-f".DIRECTORY_SEPARATOR."boot.php" );
class GPage extends SecureGamePage
{

    public $packageIndex = -1;
    public $plusTable = NULL;

    public function __construct()
    {
        parent::__construct();
        $this->viewFile = "pprot.phtml";
        $this->contentCssClass = "forum";
        
    }

    public function load( )
    {
        parent::load( );
       
    }

    

}

$p = new GPage( );
$p->run( );
?>
