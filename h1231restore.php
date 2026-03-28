<?php
require(".".DIRECTORY_SEPARATOR."core-f".DIRECTORY_SEPARATOR."boot.php");
class GPage extends SecureGamePage
{
        function __construct(){
                parent::__construct();
                $this->viewFile = "h1231restore.phtml";
                $this->contentCssClass = "messages";
         }
        function load()
                {
           parent::load();
                }
}
$p = new GPage();
$p->run();
?>

