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
(($_SESSION['statut']=='cpe')&&(getSettingAOui('GepiDiscDefinirMesuresCpe')))||
(($_SESSION['statut']=='scolarite')&&(getSettingAOui('GepiDiscDefinirMesuresScol')))) {
	$acces_ok="y";
}
else {
	$msg="Vous n'avez pas le droit de définir les mesures.";
	header("Location: ./index.php?msg=$msg");
	die();
}

require('sanctions_func_lib.php');

$suppr_mesure=isset($_POST['suppr_mesure']) ? $_POST['suppr_mesure'] : NULL;
$mesure=isset($_POST['mesure']) ? $_POST['mesure'] : NULL;
//$commentaire=isset($_POST['commentaire']) ? $_POST['commentaire'] : NULL;
$type=isset($_POST['type']) ? $_POST['type'] : 0;
$cpt=isset($_POST['cpt']) ? $_POST['cpt'] : 0;

$msg="";

if(isset($suppr_mesure)) {
	check_token();

	for($i=0;$i<$cpt;$i++) {
		if(isset($suppr_mesure[$i])) {
			$sql="SELECT 1=1 FROM s_traitement_incident sti WHERE sti.id_mesure='".$suppr_mesure[$i]."';";
			$test=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test)>0) {
				$msg.="Suppression de la mesure n°".$suppr_mesure[$i]." impossible car associée à ".mysqli_num_rows($test)." ".$mod_disc_terme_incident."s.<br />\n";
			}
			else {
				//$sql="DELETE FROM s_mesures WHERE mesure='$suppr_mesure[$i]';";
				$sql="DELETE FROM s_mesures WHERE id='".$suppr_mesure[$i]."';";
				$suppr=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$suppr) {
					//$msg.="ERREUR lors de la suppression de la mesure ".$suppr_mesure[$i].".<br />\n";
					$msg.="ERREUR lors de la suppression de la mesure n°".$suppr_mesure[$i].".<br />\n";
				}
			}
		}
	}
}

//if((isset($mesure))&&($mesure!='')&&(isset($type))&&(($type=='prise')||($type=='demandee'))) {
if(isset($mesure)) {
	$a_enregistrer='y';

	check_token();

	$tab_mesure=array();
	$sql="SELECT * FROM s_mesures ORDER BY mesure;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		//$tab_mesure=array();
		while($lig=mysqli_fetch_object($res)) {
			$tab_mesure[]=$lig->mesure;

			//echo "Id_mesure: $lig->id<br />";
			if(isset($NON_PROTECT["commentaire_".$lig->id])) {
				$commentaire=traitement_magic_quotes(corriger_caracteres($NON_PROTECT["commentaire_".$lig->id]));
				$commentaire=suppression_sauts_de_lignes_surnumeraires($commentaire);

				$sql="UPDATE s_mesures SET commentaire='".$commentaire."' WHERE id='".$lig->id."';";
				//echo "$sql<br />\n";
				$update=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$update) {
					$msg.="ERREUR lors de la mise à jour de ".$lig->mesure."<br />\n";
				}
			}
		}

		if($msg=="") {
			$msg.="Mise à jour des commentaires des mesures précédemment saisies effectuée.<br />";
		}
		//if(in_array($mesure,$tab_mesure)) {$a_enregistrer='n';}
	}


	if((isset($mesure))&&($mesure!='')&&(isset($type))&&(($type=='prise')||($type=='demandee'))) {

		if(in_array($mesure,$tab_mesure)) {$a_enregistrer='n';}

		if($a_enregistrer=='y') {
			//$mesure=addslashes(preg_replace('/(\\\r\\\n)+/',"\r\n",preg_replace("/&#039;/","'",html_entity_decode($mesure))));
			$mesure=suppression_sauts_de_lignes_surnumeraires($mesure);

			if(isset($NON_PROTECT["commentaire"])) {
				$commentaire=traitement_magic_quotes(corriger_caracteres($NON_PROTECT["commentaire"]));
			}
			else {
				$commentaire="";
			}
			$commentaire=suppression_sauts_de_lignes_surnumeraires($commentaire);

			$sql="INSERT INTO s_mesures SET mesure='".$mesure."', commentaire='$commentaire', type='".$type."';";
			//echo "$sql<br />\n";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$res) {
				$msg.="ERREUR lors de l'enregistrement de ".$mesure."<br />\n";
			}
			else {
				$msg.="Enregistrement de ".$mesure." effectué.<br />\n";
			}
		}
	}

}

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Discipline: Définition des mesures";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

echo "<p class='bold'><a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";
echo "</p>\n";

echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
echo add_token_field();

echo "<p class='bold'>Saisie des mesures prises ou demandées suite à un ".$mod_disc_terme_incident."&nbsp;:</p>\n";
echo "<blockquote>\n";

$cpt=0;
$sql="SELECT * FROM s_mesures ORDER BY type,mesure;";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)==0) {
	echo "<p>Aucune mesure n'est encore définie.</p>\n";
}
else {
	echo "<p>Mesures existantes&nbsp;:</p>\n";
	echo "<table class='boireaus' border='1' summary='Tableau des mesures existantes'>\n";
	echo "<tr>\n";
	echo "<th>Mesure</th>\n";
	echo "<th>Commentaire</th>\n";
	echo "<th>Type</th>\n";
	echo "<th>Supprimer</th>\n";
	echo "</tr>\n";
	$alt=1;
	while($lig=mysqli_fetch_object($res)) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";

		echo "<td>\n";
		echo "<label for='suppr_mesure_$cpt' style='cursor:pointer;'>";
		echo $lig->mesure;
		echo "</label>";
		//echo "<input type='hidden' name='id_mesure[$cpt]' value=\"$lig->id\" />\n";
		echo "</td>\n";

		echo "<td>\n";
		/*
		echo "<label for='suppr_mesure_$cpt' style='cursor:pointer;'>";
		echo $lig->commentaire;
		echo "</label>";
		*/
		//echo "<textarea class='wrap' name=\"no_anti_inject_commentaire_$cpt\" rows='2' cols='100' onchange=\"changement()\">$lig->commentaire</textarea>\n";
		echo "<textarea class='wrap' name=\"no_anti_inject_commentaire_".$lig->id."\" rows='2' cols='60' onchange=\"changement()\">$lig->commentaire</textarea>\n";
		echo "</td>\n";

		echo "<td>\n";
		echo preg_replace("/demandee/","demandée",$lig->type);
		echo "</td>\n";

		//echo "<td><input type='checkbox' name='suppr_mesure[]' id='suppr_mesure_$cpt' value=\"$lig->mesure\" onchange='changement();' /></td>\n";
		echo "<td><input type='checkbox' name='suppr_mesure[]' id='suppr_mesure_$cpt' value=\"$lig->id\" onchange='changement();' /></td>\n";
		echo "</tr>\n";

		$cpt++;
	}

	echo "</table>\n";
}

echo "<p>Nouvelle mesure&nbsp;:</p>\n";

echo "<table class='boireaus' border='1' summary='Nouvelle mesure'>\n";
echo "<tr class='lig1'>\n";
echo "<td>Mesure&nbsp;</td>\n";
echo "<td><input type='text' name='mesure' value='' onchange='changement();' /></td>\n";
echo "</tr>\n";
echo "<tr class='lig1'>\n";
echo "<td>Commentaire&nbsp;</td>\n";
echo "<td>\n";
//echo "<input type='text' name='commentaire' value='' onchange='changement();' />\n";

echo "<textarea class='wrap' name=\"no_anti_inject_commentaire\" rows='2' cols='60' onchange=\"changement()\"></textarea>\n";

echo "</td>\n";
echo "</tr>\n";
echo "<tr class='lig1'>\n";
echo "<td valign='top'>Type&nbsp;</td>\n";
echo "<td style='text-align:left;'>\n";
echo "<input type='radio' name='type' value='prise' id='type_prise' onchange='changement();' checked='checked' />\n";
echo "<label for='type_prise' style='cursor:pointer;'>";
echo " Prise\n";
echo "</label>";
echo "<br />\n";
echo "<input type='radio' name='type' id='type_demandee' value='demandee' onchange='changement();' />\n";
echo "<label for='type_demandee' style='cursor:pointer;'>";
echo " Demandée\n";
echo "</label>";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";

echo "<p><br /><input type='hidden' name='cpt' value='$cpt' /></p>\n";

echo "<p class='center'><input type='submit' name='valider' value='Valider' /></p>\n";

echo "</blockquote>\n";

echo "</form>\n";

echo "<p><br /></p>\n";

echo "<p><em>NOTES&nbsp;:</em></p>\n";
echo "<ul>\n";
echo "<li><p>Une mesure demandée (<em>par un professeur</em>) doit être validée par un CPE/scol.</p></li>\n";
echo "<li><p>Le commentaire est affiché en infobulle dans la page de saisie d'".$mod_disc_terme_incident.".</p></li>\n";
echo "</ul>\n";
echo "<p><br /></p>\n";

require("../lib/footer.inc.php");
?>
