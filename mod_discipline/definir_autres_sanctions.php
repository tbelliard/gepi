<?php

/*
 *
 * Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

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

// SQL : INSERT INTO droits VALUES ( '/mod_discipline/definir_autres_sanctions.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Définir types sanctions', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/definir_autres_sanctions.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Définir types sanctions', '');;";
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

$acces_ok="n";
if(($_SESSION['statut']=='administrateur')||
(($_SESSION['statut']=='cpe')&&(getSettingAOui('GepiDiscDefinirSanctionsCpe')))||
(($_SESSION['statut']=='scolarite')&&(getSettingAOui('GepiDiscDefinirSanctionsScol')))) {
	$acces_ok="y";
}
else {
	$msg="Vous n'avez pas le droit de définir les rôles dans les incidents.";
	header("Location: ./index.php?msg=$msg");
	die();
}

require('sanctions_func_lib.php');

$msg="";

$suppr_nature=isset($_POST['suppr_nature']) ? $_POST['suppr_nature'] : NULL;

$nature=isset($_POST['nature']) ? $_POST['nature'] : NULL;
$type=isset($_POST['type']) ? $_POST['type'] : NULL;
$cpt=isset($_POST['cpt']) ? $_POST['cpt'] : 0;

if(isset($suppr_nature)) {
	check_token();

	for($i=0;$i<$cpt;$i++) {
		if(isset($suppr_nature[$i])) {
			//$sql="SELECT 1=1 FROM s_autres_sanctions WHERE id_nature='$suppr_nature[$i]';";
			$sql="SELECT 1=1 FROM s_sanctions WHERE id_nature_sanction='$suppr_nature[$i]';";
			//echo "$sql<br />";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)>0) {
				$msg.="Il n'est pas possible de supprimer le type de sanction n°".$suppr_nature[$i]." parce qu'il est associé à une ou des sanctions déjà saisies pour un ou des élèves.<br />\n";
			}
			else {
				$sql="DELETE FROM s_types_sanctions2 WHERE id_nature='$suppr_nature[$i]';";
				$suppr=mysql_query($sql);
				if(!$suppr) {
					$msg.="ERREUR lors de la suppression de la nature n°".$suppr_nature[$i].".<br />\n";
				}
				else {
					$msg.="Suppression de la nature n°".$suppr_nature[$i].".<br />\n";
				}
			}
		}
	}
}

//if((isset($nature))&&($nature!='')&&(isset($type))&&(($type=='prise')||($type=='demandee'))) {
if(isset($nature)) {
	$a_enregistrer='y';

	check_token();

	$sql="SELECT * FROM s_types_sanctions2 ORDER BY nature;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$tab_nature=array();
		while($lig=mysql_fetch_object($res)) {
			$tab_nature[]=$lig->nature;
		}

		if(in_array($nature,$tab_nature)) {
			$a_enregistrer='n';
			$msg.="La nature ".$nature." existe déjà.<br />\n";
		}
	}


	if((isset($nature))&&($nature!='')) {
		if($a_enregistrer=='y') {
			if(!in_array($type, $types_autorises)) {
				$msg.="Le type de sanction choisi n'est pas autorisé&nbsp;: ".$type.".<br />\n";
			}
			else {
				$nature=suppression_sauts_de_lignes_surnumeraires($nature);

				$sql="INSERT INTO s_types_sanctions2 SET nature='".$nature."', type='".$type."';";
				//echo "$sql<br />\n";
				$res=mysql_query($sql);
				if(!$res) {
					$msg.="ERREUR lors de l'enregistrement de ".$nature."<br />\n";
				}
				else {
					$msg.="Enregistrement de ".$nature."<br />\n";
				}
			}
		}
	}
}

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Discipline: Définition des types de sanctions";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

echo "<p class='bold'><a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";
echo "</p>\n";

echo "<p>Les types de sanctions prédéfinis sont: Retenue, Exclusion, Travail.<br />
La présente page est destinée à ajouter d'autres types de sanctions (<i>'mise au pilori', 'flagellation avec des orties', 'regarder Questions pour un champion',... selon les goûts de l'établissement en matière de supplices divers;o</i>).</p>
<p>Vous pouvez maintenant aussi ajouter des sanctions variantes de retenue, d'exclusion,... en en précisant le type.</p>\n";

echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
echo add_token_field();

echo "<p class='bold'>Saisie de types de sanctions&nbsp;:</p>\n";
echo "<blockquote>\n";

$cpt=0;
$sql="SELECT * FROM s_types_sanctions2 ORDER BY type, nature;";
$res=mysql_query($sql);
if(mysql_num_rows($res)==0) {
	echo "<p>Aucune sanction supplémentaire n'est encore définie.</p>\n";
}
else {
	echo "<p>Sanctions existantes&nbsp;:</p>\n";
	echo "<table class='boireaus' border='1' summary='Tableau des sanctions existantes'>\n";
	echo "<tr>\n";
	echo "<th>Nature</th>\n";
	echo "<th>Type</th>\n";
	echo "<th>Supprimer</th>\n";
	echo "</tr>\n";
	$alt=1;
	while($lig=mysql_fetch_object($res)) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";

		echo "<td>\n";
		echo "<label for='suppr_nature_$cpt' style='cursor:pointer;'>";
		echo $lig->nature;
		echo "</label>";
		echo "</td>\n";

		echo "<td>\n";
		echo $lig->type;
		echo "</td>\n";

		echo "<td>";
		$sql="SELECT 1=1 FROM s_sanctions WHERE id_nature_sanction='$lig->id_nature';";
		//echo "$sql<br />";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)==0) {
			echo "<input type='checkbox' name='suppr_nature[]' id='suppr_nature_$cpt' value=\"$lig->id_nature\" onchange='changement();' />";
		}
		else {
			echo "<span title='Cette nature de sanction est associée à ".mysql_num_rows($test)." sanction(s) donnée(s).'>Nature associée</span>";
		}
		echo "</td>\n";
		echo "</tr>\n";

		$cpt++;
	}

	echo "</table>\n";
}

echo "<p>Nouvelle nature&nbsp;: <input type='text' name='nature' value='' onchange='changement();' />\n";
echo " de type <select name='type'>\n";
echo "<option value='retenue'>Retenue</option>\n";
echo "<option value='exclusion'>Exclusion</option>\n";
echo "<option value='travail'>Travail</option>\n";
echo "<option value='autre' selected>Autre</option>\n";
echo "</select>\n";
echo "</p>\n";

echo "<input type='hidden' name='cpt' value='$cpt' />\n";

echo "<p><input type='submit' name='valider' value='Valider' /></p>\n";

echo "</blockquote>\n";
echo "</form>\n";
echo "<p><br /></p>\n";

echo "<p><em>NOTES&nbsp;:</em></p>\n";
echo "<ul>\n";
echo "<li>Le type Retenue permet de définir une date, une heure, une durée, un travail, des reports,...</li>\n";
echo "<li>Le type Exclusion permet de définir une date, une durée,...</li>\n";
echo "<li>Le type Travail permet de définir une date de retour, une heure de retour, un travail,...</li>\n";
echo "<li>Le type Autre permet seulement de définir une description.</li>\n";
echo "</ul>\n";
echo "<p><br /></p>\n";

require("../lib/footer.inc.php");
?>
