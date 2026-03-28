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
class GPage extends securegamepage{

        public $chats = NULL;
        public $Filter = NULL;

        public function GPage(){
                parent::securegamepage();
                $this->viewFile = "chat.phtml";
                $this->contentCssClass = "player";
        }

        public function load(){
                parent::load();
                $this->Filter = new FilterWordsModel();
                
                $m = new ChatModel();
                $this->player->chat_count =0;
                $m->provider->executeQuery2("UPDATE p_players SET chat_count=0 WHERE id=%s",array($this->player->playerId));
                if(isset($_GET['del']) && $this->player->playerId == 1){

                        $m->provider->executeQuery("DELETE FROM g_chat  WHERE id=".$_GET['del']);
                }
                if($this->isPost() && isset($_POST['text'])){
                $redseahost = stripslashes(htmlspecialchars(trim($_POST['text'])));
                $m->SendToChat( $this->data['name'], $this->player->playerId, $redseahost );
                }
                $m->DeleteOldChat();
                $this->chats = $m->GetFromChat();
                $m->dispose();
        }
}
$p = new GPage();
$p->run();
mysql_close();
?>