<?php

/*
 * $Id: definir_roles.php 2554 2008-10-12 14:49:29Z crob $
 *
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

// SQL : INSERT INTO droits VALUES ( '/mod_discipline/definir_lieux.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Définir les lieux', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/definir_lieux.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Définir les lieux', '');;";
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
	die();
}

if(strtolower(substr(getSettingValue('active_mod_discipline'),0,1))!='y') {
	$mess=rawurlencode("Vous tentez d accéder au module Discipline qui est désactivé !");
	tentative_intrusion(1, "Tentative d'accès au module Discipline qui est désactivé.");
	header("Location: ../accueil.php?msg=$mess");
	die();
}

require('sanctions_func_lib.php');

$msg="";

$suppr_lieu=isset($_POST['suppr_lieu']) ? $_POST['suppr_lieu'] : NULL;
$lieu=isset($_POST['lieu']) ? $_POST['lieu'] : NULL;
$cpt=isset($_POST['cpt']) ? $_POST['cpt'] : 0;

if(isset($suppr_lieu)) {
	for($i=0;$i<$cpt;$i++) {
		if(isset($suppr_lieu[$i])) {
			$sql="DELETE FROM s_lieux_incidents WHERE id='$suppr_lieu[$i]';";
			$suppr=mysql_query($sql);
			if(!$suppr) {
				//$msg.="ERREUR lors de la suppression de la qualité n°".$suppr_lieu[$i].".<br />\n";
				$msg.="ERREUR lors de la suppression du lieu n°".$suppr_lieu[$i].".<br />\n";
			}
			else {
				$msg.="Suppression du lieu n°".$suppr_lieu[$i].".<br />\n";
			}
		}
	}
}

if((isset($lieu))&&($lieu!='')) {
	$a_enregistrer='y';

	$sql="SELECT lieu FROM s_lieux_incidents ORDER BY lieu;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$tab_lieu=array();
		while($lig=mysql_fetch_object($res)) {
			$tab_lieu[]=$lig->lieu;
		}

		if(in_array($lieu,$tab_lieu)) {$a_enregistrer='n';}
	}

	if($a_enregistrer=='y') {
		//$lieu=addslashes(my_ereg_replace('(\\\r\\\n)+',"\r\n",my_ereg_replace("&#039;","'",html_entity_decode($lieu))));
		$lieu=my_ereg_replace('(\\\r\\\n)+',"\r\n",$lieu);

		$sql="INSERT INTO s_lieux_incidents SET lieu='".$lieu."';";
		$res=mysql_query($sql);
		if(!$res) {
			$msg.="ERREUR lors de l'enregistrement de ".$lieu."<br />\n";
		}
		else {
			$msg.="Enregistrement de ".$lieu."<br />\n";
		}
	}
}

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
//$titre_page = "Sanctions: Définition des qualités";
$titre_page = "Discipline: Définition des lieux";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

//debug_var();

echo "<p class='bold'><a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";
echo "</p>\n";

echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";

//echo "<p class='bold'>Saisie des qualités dans un incident&nbsp;:</p>\n";
echo "<p class='bold'>Saisie les lieux des incidents&nbsp;:</p>\n";
echo "<blockquote>\n";

$cpt=0;
$sql="SELECT * FROM s_lieux_incidents ORDER BY lieu;";
$res=mysql_query($sql);
if(mysql_num_rows($res)==0) {
	//echo "<p>Aucune qualité n'est encore définie.</p>\n";
	echo "<p>Aucun lieu n'est encore défini.</p>\n";
}
else {
	//echo "<p>Qualités existantes&nbsp;:</p>\n";
	//echo "<table class='boireaus' border='1' summary='Tableau des qualités existantes'>\n";
	echo "<p>Lieux existants&nbsp;:</p>\n";
	echo "<table class='boireaus' border='1' summary='Tableau des lieux existants'>\n";
	echo "<tr>\n";
	//echo "<th>Qualité</th>\n";
	echo "<th>Lieu</th>\n";
	echo "<th>Supprimer</th>\n";
	echo "</tr>\n";
	$alt=1;
	while($lig=mysql_fetch_object($res)) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";

		echo "<td>\n";
		echo "<label for='suppr_lieu_$cpt' style='cursor:pointer;'>";
		echo $lig->lieu;
		echo "</label>";
		echo "</td>\n";

		echo "<td><input type='checkbox' name='suppr_lieu[]' id='suppr_lieu_$cpt' value=\"$lig->id\" onchange='changement();' /></td>\n";
		echo "</tr>\n";

		$cpt++;
	}

	echo "</table>\n";
}
echo "</blockquote>\n";

echo "<p>Nouveau lieu&nbsp;: <input type='text' name='lieu' value='' onchange='changement();' /></p>\n";

echo "<input type='hidden' name='cpt' value='$cpt' />\n";

echo "<p><input type='submit' name='valider' value='Valider' /></p>\n";
echo "</form>\n";

echo "<p><br /></p>\n";

require("../lib/footer.inc.php");
?>