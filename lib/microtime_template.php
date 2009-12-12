<?php
/*
 $Id: microtime_template.php $
*/


/* ---------Variables envoyées au gabarit
*	$tbs_microtime							nombre de secondes pour charger la page
*/

$tbs_microtime="";

if ($gepiShowGenTime == "yes") {
   $pageload_endtime = microtime(true);
   $pageload_time = $pageload_endtime - $pageload_starttime;
   $tbs_microtime=$pageload_time;
}
?>
