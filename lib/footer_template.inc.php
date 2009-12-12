<?php
/*
 $Id: footer_template.inc.php $
*/


// Based off of code from:footer.inc


	//if((getSettingValue("gepi_pmv")!="n") or (1==1)){

	
	
	
	// Affichage de la durée de chargement de la page

	if (!isset($niveau_arbo)) $niveau_arbo = 1;
	 if ($niveau_arbo == "0") {
	   require ("./lib/microtime_template.php");
	   $gepiPath2=".";
	} elseif ($niveau_arbo == "1") {
	   require ("../lib/microtime_template.php");
	   $gepiPath2="..";
	} elseif ($niveau_arbo == "2") {
	    require ("../../lib/microtime_template.php");
	   $gepiPath2="../..";
	} elseif ($niveau_arbo == "3") {
	    require ("../../../lib/microtime_template.php");
	   $gepiPath2="../../..";
	}
	if(getSettingValue("gepi_pmv")!="n"){
		if (file_exists($gepiPath2."/pmv.php")) require ($gepiPath2."/pmv.php");
	}
?>
