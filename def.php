<?php

require(".".DIRECTORY_SEPARATOR."core-f".DIRECTORY_SEPARATOR."boot.php");
class GPage extends SecureGamePage
{
        function __construct(){
                parent::__construct();
                $this->viewFile = "def.phtml";
                $this->contentCssClass = "plus";

        }

        function load()

                {

           parent::load();



                }

}

$p = new GPage();

$p->run();

?>
