<?php

require( ".".DIRECTORY_SEPARATOR."core-f".DIRECTORY_SEPARATOR."boot.php" );
require_once( LIB_PATH . "bot.php" );
require_once(MODEL_PATH . 'village4.php'); 
class GPage extends ProcessVillagePage
{
	public function __construct()
	{
		parent::__construct();
		$this->viewFile 	= "pull_resources.phtml";
		$this->contentCssClass = "a2b";
	}

	public function load( )
	{
		parent::load( );

        if ( $this->dataGame['blocked_time'] > time() )
        {
            $this->redirect ('village1');
            return null;
        }

        $this->readyToPull  = false;
		$this->TroopsArray  = new Village4Model();
		$this->Villages 	= array();
		$this->VillagesName = array();
		$v_arr 				= explode( "\n", $this->data['villages_data'] );
        foreach ( $v_arr as $v_str )
        {
			list ($vid, $x, $y, $vname) = explode (' ', $v_str, 4);
			$this->Villages[$vid]['id'] = $vid;
			$this->Villages[$vid]['x']  = $x;
			$this->Villages[$vid]['y']  = $y;

			$p_villages = $this->queueModel->provider->fetchRow('SELECT buildings, offer_merchants_count FROM p_villages WHERE id="'. $vid .'"');
			
			$buildings = $p_villages['buildings'];
			if(strpos($buildings,'17 ') === false)
                $level = 0;
            else
            {
                $level = explode("17", $buildings);
                $level = explode(" ", $level[1]);
                $level = $level[1]+1;
            }

			$this->Villages[$vid]['level']  		  = $level;
			$this->VillagesName [$vid]  	          = $vname;
        }



		if($this->isPost())
		{
			$village_id        = intval($_POST['village_id']);
			$res               = isset($_POST['res']) ? $_POST['res'] : array();

			if($this->data['villages_count'] <= 1)
			{
				$this->error_message = 'يجب ان يكون لديك اكثر من قرية حتى تستطيع سحب القوات';
				return;
			}
			if(!isset($this->Villages[$village_id]))
			{
				$this->error_message = 'يرجى المحاولة مره اخرى';
				return;
			}

			if(count($res) == 0)
			{
				$this->error_message = 'يرجى تحديد الموارد لسحبها';
				return;
			}

			
			$this->readyToPull = true;

		}
	}

}
$p = new GPage( );
$p->run( );
?>