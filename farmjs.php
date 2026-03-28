<?php
####################################################
##   o99g@hotmil.com     &&   Abdullh El/enzi     ##
##   xh20s@hotmil.com    &&   Abdullh / Alq0rsan  ##
##   No Email *******    &&   mohammed / Aminos   ##
##   dotk.love@gmail.com &&   DOTK / BY           ##
####################################################
$http = $_SERVER['HTTP_REFERER'];
if(strstr(strtolower($http), strtolower('farm.php')) == true && (strstr(strtolower($http), strtolower('tatar8.com')) == true || strstr(strtolower($http), strtolower('localhost')) == true)) {
include("core-f/style-f/js/secretfarmjs.js");
exit;
}
include("core-f/style-f/js/jquery.js");
mysql_close();
?>
