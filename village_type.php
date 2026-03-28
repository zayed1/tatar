<?php

require(".".DIRECTORY_SEPARATOR."core-f".DIRECTORY_SEPARATOR."boot.php");
require_once( MODEL_PATH."village3.php" );
class GPage extends SecureGamePage
{

    public $packageIndex = -1;
    public $plusTable = NULL;

    public function __construct( )
    {
        parent::__construct( );
        $this->viewFile        = "village_type.phtml";
        $this->contentCssClass = "signup";

    }

    public function load( )
    {
        parent::load();
		
		$db_port = isset($GLOBALS['AppConfig']['db']['port']) ? intval($GLOBALS['AppConfig']['db']['port']) : 3306;
		$link = mysqli_connect($GLOBALS['AppConfig']['db']['host'],$GLOBALS['AppConfig']['db']['user'],$GLOBALS['AppConfig']['db']['password'],$GLOBALS['AppConfig']['db']['database'],$db_port) or die(mysqli_connect_error());
        
        if ( $this->dataGame['blocked_time'] > time() )
        {
            $this->redirect ('village1.php');
            return null;
        }
        
        $this->type = 
		!isset($_GET['oasis']) 
		? 
		( !isset($_GET['new_oasis'])
		? 0 : 2 ) 
		: 1;

        $villageId  = isset( $_GET['id'] ) && 0 < floatval( $_GET['id'] ) ? floatval( $_GET['id'] ) : 0;

        if ( $villageId <= 0 )
        {
            $this->redirect( "map.php" );
            exit;
        }

        $m                 = new VillageModel( );
        $this->mapItemData = $m->getMapItemData( $villageId );

        if ( !is_array( $this->mapItemData ) )
        {
                $m->dispose( );
                $this->redirect( "map.php" );
                exit;
        }
        if ( 0 < floatval( $this->mapItemData['player_id'] ) )
        {
                $m->dispose( );
                $this->redirect( "map.php" );
                exit;
        }
        if ( $this->mapItemData['is_oasis'] && $this->type == 0 )
        {
                $m->dispose( );
                $this->redirect( "map.php" );
                exit;
        }
        if ( !$this->mapItemData['is_oasis'] && $this->type == 1 )
        {
                $m->dispose( );
                $this->redirect( "map.php" );
                exit;
        }
        if ( $this->mapItemData['is_oasis'] && $this->type == 2 )
        {
                $m->dispose( );
                $this->redirect( "map.php" );
                exit;
        }

        $p_queue = $this->queueModel->provider->fetchRow('SELECT id FROM p_queue WHERE to_village_id="'. $villageId .'"');

        $this->error_message = '';
        if($this->isPost() && $this->type == 2)
        {
            $fid = floatval($_POST['fid']);

            if( $fid < 1 || 12 < $fid)
            {
                $this->error_message = 'يرجى اختيار نوع الواحة بشكل صحيح';
                return;
            }
            if($this->data['gold_num'] < $this->appConfig['Game']['village_type_new_oasis'])
            {
                $this->error_message = 'لا يوجد لديك عدد ذهب كافي';
                return;
            }
            if($p_queue != null)
            {
                $this->error_message = 'لا يمكنك تعديل هذه الواحة, يرجى البحث عن واحة أخرى';
                return;
            }

            $this->queueModel->provider->executeQuery2 ("UPDATE p_players SET gold_num = gold_num-". $this->appConfig['Game']['village_type_new_oasis'] ." WHERE id='". $this->player->playerId ."'");
            $this->queueModel->provider->executeQuery2 ("UPDATE `p_villages` SET 
			`field_maps_id` = '0' ,
			`tribe_id` = '4' ,
			`is_oasis` = '1' ,
			`resources` = '1 50000000 50000000 50000000 80000000 0
			,2 50000000 50000000 50000000 80000000 0,
			3 50000000 50000000 50000000 80000000 0,
			4 50000000 50000000 50000000 80000000 0' ,
			`troops_num` = 
			'-1:31 ".rand(1,100).",36 ".rand(1,100).",39 ".rand(1,100)."' ,
			`creation_date` = NOW() ,
			`image_num` =". $fid ."
			WHERE `id`='". $villageId ."'")or die(mysqli_error($link));
            $this->redirect( "village3.php?id=". $villageId );
            exit;

        }
		
		if($this->isPost() && $this->type == 0)
        {
            $fid = floatval($_POST['fid']);

            if( $fid <= 0 || 13 < $fid)
            {
                $this->error_message = 'يرجى اختيار نوع القرية بشكل صحيح';
                return;
            }
            if( $fid == $this->mapItemData['field_maps_id'])
            {
                $this->error_message = 'يرجى عدم اختيار نفس نوع القرية';
                return;
            }
            if($this->data['gold_num'] < $this->appConfig['Game']['village_type'])
            {
                $this->error_message = 'لا يوجد لديك عدد ذهب كافي';
                return;
            }
            if($p_queue != null)
            {
                $this->error_message = 'لا يمكنك تعديل هذه القرية, يرجى البحث عن قرية أخرى';
                return;
            }

            $this->queueModel->provider->executeQuery2 ("UPDATE p_players SET gold_num=gold_num-". $this->appConfig['Game']['village_type'] ." WHERE id='". $this->player->playerId ."'");
            $this->queueModel->provider->executeQuery2 ("UPDATE p_villages SET field_maps_id=". $fid ." WHERE id='". $villageId ."'");
            $this->redirect( "village3.php?&id=". $villageId );
            exit;

        }

        if($this->isPost() && $this->type == 1)
        {
            $fid = floatval($_POST['fid']);

            if( $fid < 1 || 12 < $fid)
            {
                $this->error_message = 'يرجى اختيار نوع الواحة بشكل صحيح';
                return;
            }
            if( $fid == $this->mapItemData['field_maps_id'])
            {
                $this->error_message = 'يرجى عدم اختيار نفس نوع الواحة';
                return;
            }
            if($this->data['gold_num'] < $this->appConfig['Game']['village_type2'])
            {
                $this->error_message = 'لا يوجد لديك عدد ذهب كافي';
                return;
            }
            if($p_queue != null)
            {
                $this->error_message = 'لا يمكنك تعديل هذه الواحة, يرجى البحث عن واحة أخرى';
                return;
            }

            $this->queueModel->provider->executeQuery2 ("UPDATE p_players SET gold_num=gold_num-". $this->appConfig['Game']['village_type2'] ." WHERE id='". $this->player->playerId ."'");
            $this->queueModel->provider->executeQuery2 ("UPDATE p_villages SET image_num=". $fid ." WHERE id='". $villageId ."'");
            $this->redirect( "village3.php?id=". $villageId );
            exit;

        }

    }



}

$p = new GPage( );
$p->run( );
?>