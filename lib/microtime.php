<?php
/**
 * Affichage du temps de génération de la page
 */
if ($gepiShowGenTime == "yes") {
   $pageload_endtime = microtime(true);
   $pageload_time = $pageload_endtime - $pageload_starttime;
   echo "<p class='microtime'>Page générée en ".$pageload_time." sec</p>";
}
?>