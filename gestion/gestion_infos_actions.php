<?php
/**
 *
 * $Id$
 *
 * @copyright Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stéphane Boireau
 * @package Gestion
*/

/*
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

/**
 * Fichiers d'initialisation
 */
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


$sql="SELECT 1=1 FROM droits WHERE id='/gestion/gestion_infos_actions.php';";
$res_test=mysql_query($sql);
if (mysql_num_rows($res_test)==0) {
	$sql="INSERT INTO droits VALUES ('/gestion/gestion_infos_actions.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des actions en attente signalées en page d accueil.', '1');";
	$res_insert=mysql_query($sql);
}
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$suppr=isset($_POST['suppr']) ? $_POST['suppr'] : array();
$nature=isset($_POST['nature']) ? $_POST['nature'] : (isset($_GET['nature']) ? $_GET['nature'] : NULL);
$dest=isset($_POST['dest']) ? $_POST['dest'] : (isset($_GET['dest']) ? $_GET['dest'] : NULL);

$msg="";

if(count($suppr)>0) {
	check_token();

	for($i=0;$i<count($suppr);$i++) {
		if(!del_info_action($suppr[$i])) {
			$msg.="Erreur lors de la suppression de l'action n°".$suppr[$i]."<br />";
		}
	}
}

//**************** EN-TETE *********************
$titre_page = "Gestion actions en attente";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>\n";

$sql="SELECT * FROM infos_actions ORDER BY date;";
$res=mysql_query($sql);
if(mysql_num_rows($res)==0) {
	echo "<p>Aucune action en attente n'est enregistrée.</p>\n";
	require("../lib/footer.inc.php");
	die();
}


if(!isset($dest)) {
	$sql="SELECT DISTINCT valeur FROM infos_actions_destinataires WHERE nature='statut' ORDER BY valeur;";
	$res2=mysql_query($sql);
	$nb_statuts_dest=mysql_num_rows($res2);

	$sql="SELECT DISTINCT valeur FROM infos_actions_destinataires WHERE nature='individu' ORDER BY valeur;";
	$res3=mysql_query($sql);
	$nb_individus_dest=mysql_num_rows($res3);

	if($nb_statuts_dest+$nb_individus_dest!=1) {
		echo "<p>Afficher les actions en attente pour&nbsp;: <a href='".$_SERVER['PHP_SELF']."?nature=tout'>Tout</a> ou</p>\n";

		if($nb_statuts_dest>0) {
			echo "<p>un statut&nbsp;:";
			while($lig2=mysql_fetch_object($res2)) {
				echo " <a href='".$_SERVER['PHP_SELF']."?nature=statut&amp;dest=$lig2->valeur'>$lig2->valeur</a><br />";
			}
			echo "</p>\n";
		}

		if($nb_individus_dest>0) {
			echo "<p>un individu&nbsp;:";
			while($lig3=mysql_fetch_object($res3)) {
				echo "<a href='".$_SERVER['PHP_SELF']."?nature=individu&amp;dest=$lig3->valeur'>$lig3->valeur</a><br />";
			}
			echo "</p>\n";
		}
		require("../lib/footer.inc.php");
		die();
	}
}


echo "<p>Vous pouvez supprimer par lots des signalement d'actions en attente&nbsp;:</p>\n";

echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
echo add_token_field();

echo "<table class='boireaus'>\n";
echo "<tr>\n";
echo "<th colspan='3'>Action en attente</th>\n";
echo "<th rowspan='2'>Supprimer";

echo "<br />\n";

echo "<a href=\"javascript:CocheColonne();changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' title='Tout cocher' /></a> / <a href=\"javascript:DecocheColonne();changement();\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' title='Tout décocher' /></a>";

echo "</th>\n";

echo "<tr>\n";
echo "<th>Date</th>\n";
echo "<th>Titre</th>\n";
echo "<th>Description</th>\n";
echo "</tr>\n";

$cpt=0;
$alt=1;
while($lig=mysql_fetch_object($res)) {
	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	echo "<td>".formate_date($lig->date)."</td>\n";
	echo "<td>$lig->titre</td>\n";
	echo "<td>$lig->description</td>\n";
	echo "<td><input type='checkbox' name='suppr[]' id='suppr_$cpt' value='$lig->id' /></td>\n";
	echo "</tr>\n";
	$cpt++;
}
echo "</table>\n";

echo " <input type='submit' value='Supprimer'>\n";
echo "</form>\n";

echo "<script type='text/javascript'>

	function CocheColonne() {
		for (var ki=0;ki<$cpt;ki++) {
			if(document.getElementById('suppr_'+ki)){
				document.getElementById('suppr_'+ki).checked = true;
			}
		}
	}

	function DecocheColonne() {
		for (var ki=0;ki<$cpt;ki++) {
			if(document.getElementById('suppr_'+ki)){
				document.getElementById('suppr_'+ki).checked = false;
			}
		}
	}
</script>";

require("../lib/footer.inc.php");
?>
