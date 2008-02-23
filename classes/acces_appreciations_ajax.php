<?php
	// Initialisations files
	require_once("../lib/initialisations.inc.php");

	if(isset($_GET['date'])) {
		//echo "date=".$_GET['date'];
		$tmp_tabdate=explode("/",$_GET['date']);

		$timestamp_limite=mktime(0,0,0,$tmp_tabdate[1],$tmp_tabdate[0],$tmp_tabdate[2]);
		$timestamp_courant=time();

		if($timestamp_courant>$timestamp_limite) {echo "<span style='color:green;'>Accessible à la date du jour</span>";} else {echo "<span style='color:red;'>Inaccessible à la date du jour</span>";}
	}
	else {
		//new Ajax.Updater($(id_div),'acces_appreciations_ajax.php?classe_periode='+classe_periode+'accessible='+accessible,{method: 'get'});

		echo "<label for='".$_GET['statut']."_acces_".$_GET['classe_periode']."' style='cursor: pointer;'><input type='checkbox' name='".$_GET['statut']."_acces_".$_GET['classe_periode']."' id='".$_GET['statut']."_acces_".$_GET['classe_periode']."' value='y' ";
		echo "onchange=\"modif_couleur('".$_GET['statut']."_acces_".$_GET['classe_periode']."','".$_GET['statut']."_accessible_".$_GET['classe_periode']."');changement();\" ";
		/*
		if($_GET['accessible']=="y") {
			echo "checked ";
			echo "/> <span id='accessible_".$_GET['classe_periode']."' style='color:green;'>Accessible</span></label>\n";
		}
		else {
		*/
			echo "/> <span id='".$_GET['statut']."_accessible_".$_GET['classe_periode']."' style='color:red;'>Accessible</span></label>\n";
		//}

	}
?>