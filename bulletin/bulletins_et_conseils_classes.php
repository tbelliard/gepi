<?php
/*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
*
* This file is part of GEPI.
*
* GEPI is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* GEPI is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with GEPI; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// Initialisations files
require_once("../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

$sql="SELECT 1=1 FROM droits WHERE id='/bulletin/bulletins_et_conseils_classes.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/bulletin/bulletins_et_conseils_classes.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Bulletins et conseils de classe',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

// Section checkAccess() à décommenter en prenant soin d'ajouter le droit correspondant:
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$id_classe=isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL;

//**************** EN-TETE *****************
$titre_page = "Bulletins et conseils de classe";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc.php");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

if(!isset($id_classe)) {
	echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>

<h2>Bulletins et conseils de classe</h2>

<p>Choisissez une classe&nbsp;:</p>

<script type='text/javascript'>
	function afficher_action_classe(id_classe) {
		new Ajax.Updater($('div_action_conseil_de_classe'), '$gepiPath/lib/ajax_action.php?mode=actions_conseil_classe&id_classe='+id_classe,{method: 'get'});
		afficher_div('div_infobulle_action_conseil_de_classe', 'y', 10, 10);
	}
</script>";

	$texte_infobulle="<div id='div_action_conseil_de_classe'></div>";
	$tabdiv_infobulle[]=creer_div_infobulle('div_infobulle_action_conseil_de_classe', "Bulletins et conseils de classe","",$texte_infobulle,"",40,0,'y','y','n','n');

	$tab_txt=array();
	$tab_lien=array();
	$tab_extra=array();
	$sql=retourne_sql_mes_classes();
	$res_mes_classes=mysqli_query($GLOBALS["mysqli"], $sql);
	while($lig_mes_classes=mysqli_fetch_object($res_mes_classes)) {
		$tab_txt[]=$lig_mes_classes->classe;
		$tab_lien[]=$_SERVER['PHP_SELF']."?id_classe=".$lig_mes_classes->id;
		$tab_extra[]=" onclick=\"afficher_action_classe($lig_mes_classes->id);return false;\"";
	}

	$nbcol=3;
	echo "<div style='margin-left:3em;'>";
	tab_liste($tab_txt,$tab_lien,$nbcol,NULL,$tab_extra);
	echo "</div>";
}
else {
	echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>
 | <a href='".$_SERVER['PHP_SELF']."'>Choisir une autre classe</a></p>

<h2>Bulletins et conseils de classe</h2>
".affiche_choix_action_conseil_de_classe($id_classe);
}

require("../lib/footer.inc.php");
?>
