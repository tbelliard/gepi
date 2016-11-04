<?php

/*
 *
 * Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

//$variables_non_protegees = 'yes';

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

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
	die();
}

if(mb_strtolower(mb_substr(getSettingValue('active_mod_discipline'),0,1))!='y') {
	$mess=rawurlencode("Vous tentez d accéder au module Discipline qui est désactivé !");
	tentative_intrusion(1, "Tentative d'accès au module Discipline qui est désactivé.");
	header("Location: ../accueil.php?msg=$mess");
	die();
}

require('sanctions_func_lib.php');

$acces_ok="n";
if(($_SESSION['statut']=='administrateur')||
(($_SESSION['statut']=='cpe')&&(getSettingAOui('GepiDiscDefinirRolesCpe')))||
(($_SESSION['statut']=='scolarite')&&(getSettingAOui('GepiDiscDefinirRolesScol')))) {
	$acces_ok="y";
}
else {
	$msg="Vous n'avez pas le droit de définir les rôles dans les ".$mod_disc_terme_incident."s.";
	header("Location: ./index.php?msg=$msg");
	die();
}

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// REMARQUE: Le terme de 'qualité' a été remplacé par 'rôle'
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

$msg="";

$suppr_qualite=isset($_POST['suppr_qualite']) ? $_POST['suppr_qualite'] : NULL;
$qualite=isset($_POST['qualite']) ? $_POST['qualite'] : NULL;
$cpt=isset($_POST['cpt']) ? $_POST['cpt'] : 0;

if(isset($suppr_qualite)) {
	check_token();

	for($i=0;$i<$cpt;$i++) {
		if(isset($suppr_qualite[$i])) {
			$current_qualite=get_valeur_champ("s_qualites", "id='".$suppr_qualite[$i]."'", "qualite");
			$sql="SELECT 1=1 FROM s_protagonistes WHERE qualite='".$current_qualite."';";
			//echo "$sql<br />";
			$test=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test)>0) {
				$msg.="Suppression impossible&nbsp;: Rôle associé à ".mysqli_num_rows($test)." protagoniste(s) d'incident(s).<br />";
			}
			else {
				$sql="DELETE FROM s_qualites WHERE id='$suppr_qualite[$i]';";
				$suppr=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$suppr) {
					//$msg.="ERREUR lors de la suppression de la qualité n°".$suppr_qualite[$i].".<br />\n";
					$msg.="ERREUR lors de la suppression du rôle n°".$suppr_qualite[$i].".<br />\n";
				}
				else {
					$msg.="Suppression du rôle n°".$suppr_qualite[$i].".<br />\n";
				}
			}
		}
	}
}

if((isset($qualite))&&($qualite!='')) {
	$a_enregistrer='y';

	$sql="SELECT qualite FROM s_qualites ORDER BY qualite;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$tab_qualite=array();
		while($lig=mysqli_fetch_object($res)) {
			$tab_qualite[]=$lig->qualite;
		}

		if(in_array($qualite,$tab_qualite)) {$a_enregistrer='n';}
	}

	if($a_enregistrer=='y') {
		check_token();

		$qualite=suppression_sauts_de_lignes_surnumeraires($qualite);

		$sql="INSERT INTO s_qualites SET qualite='".mysqli_real_escape_string($GLOBALS["mysqli"], $qualite)."';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(!$res) {
			$msg.="ERREUR lors de l'enregistrement de ".$qualite."<br />\n";
		}
		else {
			$msg.="Enregistrement de ".$qualite."<br />\n";
		}
	}
}

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Discipline: Définition des rôles";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

echo "<p class='bold'><a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";
echo "</p>\n";

echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
echo add_token_field();

//echo "<p class='bold'>Saisie des qualités dans un incident&nbsp;:</p>\n";
echo "<p class='bold'>Saisie des rôles dans un ".$mod_disc_terme_incident."&nbsp;:</p>\n";
echo "<blockquote>\n";

$cpt=0;
$sql="SELECT * FROM s_qualites ORDER BY qualite;";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)==0) {
	//echo "<p>Aucune qualité n'est encore définie.</p>\n";
	echo "<p>Aucun rôle n'est encore défini.</p>\n";
}
else {
	//echo "<p>Qualités existantes&nbsp;:</p>\n";
	//echo "<table class='boireaus' border='1' summary='Tableau des qualités existantes'>\n";
	echo "<p>Rôles existants&nbsp;:</p>\n";
	echo "<table class='boireaus' border='1' summary='Tableau des rôles existants'>\n";
	echo "<tr>\n";
	//echo "<th>Qualité</th>\n";
	echo "<th>Rôle</th>\n";
	echo "<th>Supprimer</th>\n";
	echo "</tr>\n";
	$alt=1;
	while($lig=mysqli_fetch_object($res)) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";

		echo "<td>\n";
		echo "<label for='suppr_qualite_$cpt' style='cursor:pointer;'>";
		echo $lig->qualite;
		echo "</label>";
		echo "</td>\n";

		echo "<td>";
		$sql="SELECT 1=1 FROM s_protagonistes WHERE qualite='".$lig->qualite."';";
		//echo "$sql<br/>";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)>0) {
			echo "<span style='color:red'>Rôle associé à ".mysqli_num_rows($test)." protagoniste(s) d'incident(s)</span>";
		}
		else {
		
			echo "<input type='checkbox' name='suppr_qualite[]' id='suppr_qualite_$cpt' value=\"$lig->id\" onchange='changement();' />";
		}
		echo "</td>\n";
		echo "</tr>\n";

		$cpt++;
	}

	echo "</table>\n";
}
echo "</blockquote>\n";

echo "<p>Nouveau rôle&nbsp;: <input type='text' name='qualite' value='' onchange='changement();' /></p>\n";

echo "<input type='hidden' name='cpt' value='$cpt' />\n";

echo "<p><input type='submit' name='valider' value='Valider' /></p>\n";
echo "</form>\n";

echo "<p><br /></p>\n";

require("../lib/footer.inc.php");
?>
