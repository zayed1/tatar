<?php
require(".".DIRECTORY_SEPARATOR."core-f".DIRECTORY_SEPARATOR."boot.php");

class GPage extends SecureGamePage

{



        function __construct(){

                parent::__construct();

                $this->viewFile = "link.phtml";

                $this->contentCssClass = "forum";

        }

        function load()

                {

           parent::load();



                }

}

$p = new GPage();

$p->run();

?>
