<?php
/**
* @   PROJECT WAS MADE FOR SMART SERVS
* @   WHATS APP : 00966501494220 
* @   VISIT : WWW.REDSEA-H.COM
* @   ALL COPY RIGHTS RESERVED PROGRAMMED BY RED SEA HOST 
* @   THIS PROJECT WAS MADE BY THE REGISTERED RED SEA HOST UNDER THE NAME OF WWW.REDSEA-H.COM
**/
require(".".DIRECTORY_SEPARATOR."core-f".DIRECTORY_SEPARATOR."boot.php");
require_once(MODEL_PATH."chat.php");
require_once(MODEL_PATH."wordsfilter.php");
class GPage  extends securegamepage{

        public $chats = NULL;
        public $Filter = NULL;

        public function __construct(){
                $this->customLogoutAction = TRUE;
                parent::__construct();
                if($this->player == NULL ) exit(0);
                $this->layoutViewFile = $this->viewFile = NULL;
                $GLOBALS['_GET']['_a1_'] = "";
    }

    public function load(){
                parent::load();
                $this->Filter = new FilterWordsModel();
                $m = new ChatModel();
                $this->chats = $m->GetFromChat();
                $storCtat = array();

                $colors = ["#84674e",'#0b91b2','#6ebe22','#a93eab','#3559b0']; 
                while($this->chats->next()){
                        $redseahost = $this->Filter->FilterWords( $this->chats->row['text'] );

                        $storCtat[$this->chats->row['ID']] = array(date( "H:i", $this->chats->row['date'] ),$this->chats->row['username'],$redseahost,$this->chats->row['userid'],$this->chats->row['ID']);
                        if(!empty($this->chats->row['userid'])){

                        $storCtat[$this->chats->row['ID']] = array(date( "H:i", $this->chats->row['date'] ),$this->chats->row['username'],$redseahost,$this->chats->row['userid'],$this->chats->row['ID'],$colors[$this->chats->row['userid']%5]);
                        }

                }
                ksort($storCtat);
                foreach($storCtat as $ChatLine){
                       echo 
    "<div class=\"msgln\"><b class=\"player d-flex\" >
        <a style=\"color:".$ChatLine[5]. "!important;\" href=\"profile.php?uid=".$ChatLine[3]."\" target=\"_blank\">".
        $ChatLine[1]."</a> </b> <span class=\"msg\">".$ChatLine[2]."
        <span class=\"time-msg \">(".$ChatLine[0].")
        ";if($this->player->playerId == 1){
                echo "<a class='del' href='chat.php?del=".$ChatLine[4]."' >حذف</a>";
        }
        echo "
        </span></span><br>
    </div>";  
//     echo "<div class=\"msgln\">(".$ChatLine[0].") <b><a href=\"profile.php?uid=".$ChatLine[3]."\" target=\"_blank\">".$ChatLine[1]."</a></b>: ".$ChatLine[2]."<br></div>";
                }
                $m->dispose();  
        }
}
$p = new GPage();
$p->run();
?>