<?php

require_once( LIB_PATH."mysql.php" );
class ModelBase extends MysqlModel
{

    public function __construct()
    {
        parent::__construct();
        $this->provider->debug = FALSE;
        $this->provider->properties = $GLOBALS['AppConfig']['db'];
    }

}

?>
