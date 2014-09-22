<?php
/*
*
* Copyright 2001, 2014 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stepĥane Boireau
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

// Initialisation des feuilles de style après modification pour améliorer l'accessibilité
$accessibilite="y";

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

$sql="SELECT 1=1 FROM droits WHERE id='/cahier_texte_admin/suppr_docs_joints_cdt.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/cahier_texte_admin/suppr_docs_joints_cdt.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Suppression des documents joints aux CDT',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//On vérifie si le module est activé
if (getSettingValue("active_cahiers_texte")!='y') {
	die("Le module n'est pas activé.");
}

$step=isset($_POST['step']) ? $_POST['step'] : (isset($_GET['step']) ? $_GET['step'] : NULL);
$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

/*
$confirmer_ecrasement=isset($_POST['confirmer_ecrasement']) ? $_POST['confirmer_ecrasement'] : (isset($_GET['confirmer_ecrasement']) ? $_GET['confirmer_ecrasement'] : 'n');
*/

include('../cahier_texte_2/cdt_lib.php');

$dossier_docs_joints=get_dossier_docs_joints_cdt();

if((isset($_POST['suppr_docs_joints_cdt']))||(isset($_POST['suppr_acces_inspecteur']))) {
	check_token();

	$msg="";

	$handle=opendir($dossier_docs_joints);
	$m=0;
	$n=0;
	while ($file = readdir($handle)) {
		if((isset($_POST['suppr_docs_joints_cdt']))&&
		((preg_match("/^cl[0-9]*$/", $file))||(preg_match("/^cl_dev[0-9]*$/", $file)))) {
			$chemin="$dossier_docs_joints/$file";
			$suppr=deltree($chemin, TRUE);
			if(!$suppr) {
				$msg.="Erreur lors de la suppression de $chemin<br />";
			}
			else {
				$n++;
			}
		}
		elseif((isset($_POST['suppr_acces_inspecteur']))&&(preg_match("/^acces_cdt_/", $file))) {
			$chemin="$dossier_docs_joints/$file";
			$suppr=deltree($chemin, TRUE);
			if(!$suppr) {
				$msg.="Erreur lors de la suppression de $chemin<br />";
			}
			else {
				$n++;
			}
		}
	}
	closedir($handle);

	if($n>0) {
		$msg.="$n dossier(s) de documents joints supprimé(s).<br />";
	}

	if($m>0) {
		$msg.="$m dossier(s) d'accès inspecteur supprimé(s).<br />";
	}
}

//**************** EN-TETE *****************
$titre_page = "Cahier de textes - Suppression docs";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *************

//debug_var();

if(isset($_GET['chgt_annee'])) {$_SESSION['chgt_annee']="y";}

echo "<p class='bold'><a href='";
if(isset($_SESSION['chgt_annee'])) {
	echo "../gestion/changement_d_annee.php";
}
else {
	echo "../cahier_texte_admin/index.php";
}
echo "'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>

<h2>Suppression des documents joints aux cahiers de textes numériques</h2>

<p>Si vous n'avez pas archivé les cahiers de textes en déplaçant les documents joints vers le dossier d'archivage, des dossiers risquent de prendre de la place dans votre arborescence.<br />
De plus les documents joints aux CDT de l'année précédente n'ont rien à faire ailleurs que dans le dossier d'archivage.</p>
<p>Vous devriez supprimer les documents de l'année qui s'est terminée avant de commencer une nouvelle année.<br />
La présente page est là pour cela.</p>
<p style='margin-bottom:1em;'>Prenez tout de même soin d'<a href='../cahier_texte_2/archivage_cdt.php'>archiver d'abord les cahiers de textes</a>.</p>";

if($dossier_docs_joints=="") {
	echo "
<p><span style='color:red'>ANOMALIE&nbsp;:</span> Le dossier des documents joints aux CDT n'a pas été trouvé.</p>";

	require("../lib/footer.inc.php");
	die();
}

$handle=opendir($dossier_docs_joints);
$m=0;
$n=0;
while ($file = readdir($handle)) {
	if((preg_match("/^cl[0-9]*$/", $file))||(preg_match("/^cl_dev[0-9]*$/", $file))) {
		$n++;
	}
	elseif(preg_match("/^acces_cdt_/", $file)) {
		$m++;
	}
}
closedir($handle);

if($n==0) {
	echo "<p>Aucun dossier de documents joints à un CDT n'existe.</p>
<p>Aucune suppression n'est nécessaire.</p>";
}
else {
	echo "<p>$n dossier(s) de documents joint à un CDT n'existe(nt).</p>";
}

if($m>0) {
	echo "<p>$m dossier(s) d'accès inspecteur existent.</p>
<p>Si les inspections ont eu lieu, vous pouvez supprimer ces dossiers.</p>";
}

if(($n>0)||($m>0)) {
	echo "
<form action=\"".$_SERVER['PHP_SELF']."\" method='post' style='margin-top:1em;'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<p>Supprimer les dossiers suivants&nbsp;:";
	if($n>0) {
		echo "<br />
		<input type='checkbox' name='suppr_docs_joints_cdt' id='suppr_docs_joints_cdt' value='y' onchange=\"checkbox_change('suppr_docs_joints_cdt')\" /><label for='suppr_docs_joints_cdt' id='texte_suppr_docs_joints_cdt' />Le(s) $n dossier(s) de documents joints.</label>";
	}
	if($m>0) {
		echo "<br />
		<input type='checkbox' name='suppr_acces_inspecteur' id='suppr_acces_inspecteur' value='y' onchange=\"checkbox_change('suppr_acces_inspecteur')\" /><label for='suppr_acces_inspecteur' id='texte_suppr_acces_inspecteur' />Le(s) $m dossier(s) accès inspecteur.</label>";
	}
	echo "
		</p>

		<p><input type='submit' value='Procéder à la suppression' /></p>

		<p style='text-indent:-6em;margin-left:6em;'><span style='color:red'>ATTENTION&nbsp;:</span> L'opération est <strong>irréversible</strong>.<br />
		Prenez soin d'archiver les cahiers de textes avant si ce n'est pas encore fait.</p>
	</fieldset>
</form>

<script type='text/javascript'>
".js_checkbox_change_style('checkbox_change', 'texte_', "n", 0.5)."

checkbox_change('suppr_docs_joints_cdt');
checkbox_change('suppr_acces_inspecteur');
</script>";

}


// PROPOSER UNE PAGE DU MEME TYPE DANS mod_discipline/discipline_admin.php


require("../lib/footer.inc.php");
?>
