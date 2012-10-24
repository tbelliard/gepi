<?php
/**
 *
 * $Id$
 *
 * @copyright Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stéphane Boireau
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
	$sql="INSERT INTO droits VALUES ('/gestion/gestion_infos_actions.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Gestion des actions en attente signalées en page d accueil.', '1');";
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

	$cpt_suppr=0;

	if($_SESSION['statut']=='administrateur') {
		for($i=0;$i<count($suppr);$i++) {
			if(!del_info_action($suppr[$i])) {
				$msg.="Erreur lors de la suppression de l'action n°".$suppr[$i]."<br />";
			}
			else {
				$cpt_suppr++;
			}
		}
	}
	else {
		for($i=0;$i<count($suppr);$i++) {
			if(!del_info_action($suppr[$i], $_SESSION['login'], $_SESSION['statut'])) {
				$msg.="Erreur lors de la suppression de l'action n°".$suppr[$i]."<br />";
			}
			else {
				$cpt_suppr++;
			}
		}
	}

	if($cpt_suppr>0) {
		$msg.="Suppression de $cpt_suppr action(s).<br />";
	}
}

//**************** EN-TETE *********************
$titre_page = "Gestion actions en attente";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";

$sql="SELECT * FROM infos_actions ORDER BY date;";
$res=mysql_query($sql);
if(mysql_num_rows($res)==0) {
	echo "</p>\n";

	echo "<p>Aucune action en attente n'est enregistrée.</p>\n";
	require("../lib/footer.inc.php");
	die();
}

if($_SESSION['statut']!='administrateur') {
	$nature="individu";
	$dest=$_SESSION['login'];
}

if(!isset($nature)) {
	$sql="SELECT DISTINCT valeur FROM infos_actions_destinataires WHERE nature='statut' ORDER BY valeur;";
	$res2=mysql_query($sql);
	$nb_statuts_dest=mysql_num_rows($res2);

	$sql="SELECT DISTINCT valeur FROM infos_actions_destinataires WHERE nature='individu' ORDER BY valeur;";
	$res3=mysql_query($sql);
	$nb_individus_dest=mysql_num_rows($res3);

	if($nb_statuts_dest+$nb_individus_dest!=1) {
		echo "<p>Afficher les actions en attente pour&nbsp;:<br /><a href='".$_SERVER['PHP_SELF']."?nature=tout'>Tous les utilisateurs et tous les statuts</a> ou<br />\n";

		if($nb_statuts_dest>0) {
			echo "<p>pour un statut particulier&nbsp;:";
			$cpt_statut=0;
			while($lig2=mysql_fetch_object($res2)) {
				if($cpt_statut>0) {echo ", ";}
				echo " <a href='".$_SERVER['PHP_SELF']."?nature=statut&amp;dest=$lig2->valeur'>$lig2->valeur</a>";
				$cpt_statut++;
			}
			echo "</p>\n";
		}

		if($nb_individus_dest>0) {
			echo "<p>pour un utilisateur en particulier&nbsp;:";
			$cpt_utilisateur=0;
			while($lig3=mysql_fetch_object($res3)) {
				if($cpt_utilisateur>0) {echo ", ";}
				echo "<a href='".$_SERVER['PHP_SELF']."?nature=individu&amp;dest=$lig3->valeur'>$lig3->valeur</a>";
				$cpt_utilisateur++;
			}
			echo "</p>\n";
		}
		require("../lib/footer.inc.php");
		die();
	}
}

echo " | <a href='".$_SERVER['PHP_SELF']."'>Gestion des actions en attente</a>";

if(($nature=='tout')||(!isset($dest))) {
	$sql="SELECT ia.* FROM infos_actions ia ORDER BY date;";
}
elseif($nature=='individu') {
	$nom_prenom_dest=civ_nom_prenom($dest);
	echo "<p class='bold'>Actions en attente pour ".$nom_prenom_dest." (<em>$dest</em>)"."</p>\n";

	$sql="SELECT ia.* FROM infos_actions ia, infos_actions_destinataires iad WHERE
	ia.id=iad.id_info AND
	iad.nature='individu' AND
	iad.valeur='".$dest."'
	ORDER BY date;";
}
elseif($nature=='statut') {
	echo "<p class='bold'>Actions en attente pour le statut $dest</p>\n";

	$sql="SELECT ia.* FROM infos_actions ia, infos_actions_destinataires iad WHERE
	ia.id=iad.id_info AND
	iad.nature='statut' AND
	iad.valeur='".$dest."'
	ORDER BY date;";
}
else {
	echo "</p>\n";

	echo "<p style='color:red'>Erreur&nbsp;: Mode non choisi</p>\n";
	require("../lib/footer.inc.php");
	die();
}

$res=mysql_query($sql);
if(mysql_num_rows($res)==0) {
	echo "</p>\n";

	if($nature=='tout') {
		echo "<p>Aucune action en attente n'est (<em>plus</em>) enregistrée.</p>\n";
	}
	else {
		echo "<p>Aucune action en attente n'est (<em>plus</em>) enregistrée spécifiquement pour ";
		if($nature=='individu') {
			echo $nom_prenom_dest." (<em>$dest</em>)";
		}
		else {
			echo "le statut $dest";
		}
		echo ".</p>\n";
	}

	echo "<p><br /></p>
<p style='text-indent:-4em; margin-left:4em;'><em>NOTE&nbsp;:</em> S'il subsiste des actions en attente en page d'accueil, elles concernent peut-être ";
	if($_SESSION['statut']=='administrateur') {
		echo "le";
	}
	else {
		echo "votre";
	}
	echo " statut.<br />Seul un administrateur peut supprimer par lots des actions en attente concernant un statut.<br />Vous pouvez cependant supprimer une par une les actions en page d'accueil.</p>\n";

	require("../lib/footer.inc.php");
	die();
}

echo "<p>Vous pouvez supprimer par lots des signalement d'actions en attente&nbsp;:</p>\n";

echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
echo add_token_field();
echo " <input type='hidden' name='nature' value='$nature'>\n";
if(isset($dest)) {
	echo " <input type='hidden' name='dest' value='$dest'>\n";
}

echo "<table class='boireaus'>\n";
echo "<tr>\n";
if($_SESSION['statut']!='administrateur') {
	echo "<th colspan='3'>Action en attente</th>\n";
}
else {
	echo "<th colspan='4'>Action en attente</th>\n";
}
echo "<th rowspan='2'>\n";

echo " <input type='submit' value='Supprimer'>\n";

echo "<br />\n";

echo "<a href=\"javascript:CocheColonne();changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' title='Tout cocher' /></a> / <a href=\"javascript:DecocheColonne();changement();\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' title='Tout décocher' /></a>";

echo "</th>\n";

echo "<tr>\n";
echo "<th>Date</th>\n";
echo "<th>Titre</th>\n";
echo "<th>Description</th>\n";
if($_SESSION['statut']=='administrateur') {
	echo "<th>Concernant</th>\n";
}
echo "</tr>\n";

$cpt=0;
$alt=1;
while($lig=mysql_fetch_object($res)) {
	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	echo "<td><label for='suppr_$cpt'>".formate_date($lig->date, "y")."</label></td>\n";
	echo "<td><label for='suppr_$cpt'>$lig->titre</label></td>\n";
	echo "<td>".nl2br(preg_replace("/\\\\n/","\n",$lig->description))."</td>\n";

	if($_SESSION['statut']=='administrateur') {
		echo "<td>";
		$sql="SELECT * FROM infos_actions_destinataires WHERE id_info='$lig->id' ORDER BY valeur;";
		$res_iad=mysql_query($sql);
		if(mysql_num_rows($res_iad)) {
			$cpt_iad=0;
			while($lig_iad=mysql_fetch_object($res_iad)) {
				if($cpt_iad>0) {echo ", ";}
				if($lig_iad->nature=='statut') {
					echo "<span title='Statut $lig_iad->valeur'>".$lig_iad->valeur."</span>";
				}
				else {
					echo civ_nom_prenom($lig_iad->valeur);
				}
				$cpt_iad++;
			}
		}
		echo "</td>\n";
	}

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
