<?php
require( ".".DIRECTORY_SEPARATOR."core-f".DIRECTORY_SEPARATOR."boot.php" );
class GPage extends securegamepage
{
    public function __construct()
    {
        parent::__construct();
        $this->viewFile = "logout.phtml";
        $this->contentCssClass = "logout";
    }
    public function load( )
    {
        if ( $this->player->isSpy )
        {
            $gameStatus = $this->player->gameStatus;
            $uid = $this->player->prevPlayerId;
            $this->player = new Player( );
            $this->player->playerId = $uid;
            $this->player->isAgent = FALSE;
            $this->player->gameStatus = $gameStatus;
            $this->player->save( );
            $this->redirect( "village1" );
        }
        else
        {
            $this->player->logout( );
            unset($_SESSION);
            $this->player = NULL;
        }
    }
    public function preRender( )
    {
    }
}
$p = new GPage( );
$p->run( );
?>
