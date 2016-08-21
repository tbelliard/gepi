<?php
/*
* Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
};

//======================================================================================

$sql="SELECT 1=1 FROM droits WHERE id='/mod_genese_classes/saisie_contraintes_opt_classe.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_genese_classes/saisie_contraintes_opt_classe.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Genèse des classes: Saisie des contraintes options/classes',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

//======================================================================================
// Section checkAccess() à décommenter en prenant soin d'ajouter le droit correspondant:
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
//======================================================================================

$projet=isset($_POST['projet']) ? $_POST['projet'] : (isset($_GET['projet']) ? $_GET['projet'] : NULL);

$is_posted=isset($_POST['is_posted']) ? $_POST['is_posted'] : NULL;

if((isset($is_posted))&&(isset($projet))) {
	// Validation des contraintes
	$nb_err=0;
	$nb_reg=0;
	$nb_suppr=0;

	if(isset($_POST['ajouter'])) {

		$clas_fut=isset($_POST['clas_fut']) ? $_POST['clas_fut'] : array();
		$sans_lv1=isset($_POST['sans_lv1']) ? $_POST['sans_lv1'] : array();
		$sans_lv2=isset($_POST['sans_lv2']) ? $_POST['sans_lv2'] : array();
		$sans_lv3=isset($_POST['sans_lv3']) ? $_POST['sans_lv3'] : array();
		$sans_autre=isset($_POST['sans_autre']) ? $_POST['sans_autre'] : array();


		for($j=0;$j<count($clas_fut);$j++) {
			for($i=0;$i<count($sans_lv1);$i++) {
				$sql="INSERT INTO gc_options_classes SET projet='$projet', classe_future='$clas_fut[$j]', opt_exclue='$sans_lv1[$i]';";
				if($res=mysqli_query($GLOBALS["mysqli"], $sql)) {
					$nb_reg++;
				}
				else {
					$nb_err++;
				}
			}

			for($i=0;$i<count($sans_lv1);$i++) {
				$sql="INSERT INTO gc_options_classes SET projet='$projet', classe_future='$clas_fut[$j]', opt_exclue='$sans_lv1[$i]';";
				if($res=mysqli_query($GLOBALS["mysqli"], $sql)) {
					$nb_reg++;
				}
				else {
					$nb_err++;
				}
			}

			for($i=0;$i<count($sans_lv2);$i++) {
				$sql="INSERT INTO gc_options_classes SET projet='$projet', classe_future='$clas_fut[$j]', opt_exclue='$sans_lv2[$i]';";
				if($res=mysqli_query($GLOBALS["mysqli"], $sql)) {
					$nb_reg++;
				}
				else {
					$nb_err++;
				}
			}

			for($i=0;$i<count($sans_lv3);$i++) {
				$sql="INSERT INTO gc_options_classes SET projet='$projet', classe_future='$clas_fut[$j]', opt_exclue='$sans_lv3[$i]';";
				if($res=mysqli_query($GLOBALS["mysqli"], $sql)) {
					$nb_reg++;
				}
				else {
					$nb_err++;
				}
			}

			for($i=0;$i<count($sans_autre);$i++) {
				$sql="INSERT INTO gc_options_classes SET projet='$projet', classe_future='$clas_fut[$j]', opt_exclue='$sans_autre[$i]';";
				if($res=mysqli_query($GLOBALS["mysqli"], $sql)) {
					$nb_reg++;
				}
				else {
					$nb_err++;
				}
			}
		}

		if($nb_err>0) {$msg="$nb_err erreurs lors de l'ajout.";}
		elseif($nb_reg>0) {$msg="$nb_reg enregistrements.";}
	}
	elseif(isset($_POST['suppr'])) {

		$suppr=isset($_POST['suppr']) ? $_POST['suppr'] : array();
		for($i=0;$i<count($suppr);$i++) {
			$sql="DELETE FROM gc_options_classes WHERE projet='$projet' AND id='$suppr[$i]';";
			if($del=mysqli_query($GLOBALS["mysqli"], $sql)) {
				$nb_suppr++;
			}
			else {
				$nb_err++;
			}
		}

		if($nb_err>0) {$msg="$nb_err erreurs lors de la suppression.";}
		elseif($nb_suppr>0) {$msg="$nb_suppr suppressions.";}
	}
}

//**************** EN-TETE *****************
$titre_page = "Genèse classe: Contraintes options/classes";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc.php");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

//debug_var();

if((!isset($projet))||($projet=="")) {
	echo "<p style='color:red'>ERREUR: Le projet n'est pas choisi.</p>\n";
	require("../lib/footer.inc.php");
	die();
}

//echo "<div class='noprint'>\n";
echo "<p class='bold'><a href='index.php?projet=$projet'>Retour</a>";
echo "</p>\n";
//echo "</div>\n";


$sql="SELECT DISTINCT classe FROM gc_divisions WHERE projet='$projet' AND statut='future' ORDER BY classe;";
$res_clas_fut=mysqli_query($GLOBALS["mysqli"], $sql);
$nb_clas_fut=mysqli_num_rows($res_clas_fut);
if($nb_clas_fut==0) {
	echo "<p>Aucune classe future n'est encore définie pour ce projet.</p>\n";
	require("../lib/footer.inc.php");
	die();
}

$sql="SELECT DISTINCT opt FROM gc_options WHERE projet='$projet' ORDER BY opt;";
$res_opt=mysqli_query($GLOBALS["mysqli"], $sql);
$nb_opt=mysqli_num_rows($res_opt);
if($nb_opt==0) {
	echo "<p>Aucune option n'est encore définie pour ce projet.</p>\n";
	require("../lib/footer.inc.php");
	die();
}

echo "<h2>Projet $projet</h2>\n";

echo "<p>Vous pouvez saisir ici les options que l'on ne doit pas trouver sur certaines classes.</p>\n";

$sql="SELECT DISTINCT opt FROM gc_options WHERE projet='$projet' AND type='lv1' ORDER BY opt;";
$res_lv1=mysqli_query($GLOBALS["mysqli"], $sql);
$nb_lv1=mysqli_num_rows($res_lv1);

$sql="SELECT DISTINCT opt FROM gc_options WHERE projet='$projet' AND type='lv2' ORDER BY opt;";
$res_lv2=mysqli_query($GLOBALS["mysqli"], $sql);
$nb_lv2=mysqli_num_rows($res_lv2);

$sql="SELECT DISTINCT opt FROM gc_options WHERE projet='$projet' AND type='lv3' ORDER BY opt;";
$res_lv3=mysqli_query($GLOBALS["mysqli"], $sql);
$nb_lv3=mysqli_num_rows($res_lv3);

$sql="SELECT DISTINCT opt FROM gc_options WHERE projet='$projet' AND type='autre' ORDER BY opt;";
$res_autre=mysqli_query($GLOBALS["mysqli"], $sql);
$nb_autre=mysqli_num_rows($res_autre);

$cpt=0;
if($nb_lv1>0) {$cpt++;}
if($nb_lv2>0) {$cpt++;}
if($nb_lv3>0) {$cpt++;}
if($nb_autre>0) {$cpt++;}

echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='ajout'>
<fieldset class='fieldset_opacite50'>\n";
echo "<table class='boireaus' border='1' summary='Choix des contraintes'>\n";
echo "<tr>\n";
echo "<th rowspan='2'>Classe future</th>\n";
echo "<th colspan='$cpt'>Options non autorisées</th>\n";
echo "</tr>\n";

echo "<tr>\n";
if($nb_lv1>0) {echo "<th>LV1</th>\n";}
if($nb_lv2>0) {echo "<th>LV2</th>\n";}
if($nb_lv3>0) {echo "<th>LV3</th>\n";}
if($nb_autre>0) {echo "<th>Autre option</th>\n";}
echo "</tr>\n";

echo "<tr>\n";
echo "<td style='vertical-align:top; padding:2px;' class='lig-1'>\n";
$cpt=0;
while($lig=mysqli_fetch_object($res_clas_fut)) {
	echo "<input type='checkbox' name='clas_fut[]' id='clas_fut_$cpt' value='$lig->classe' /><label for='clas_fut_$cpt'>$lig->classe</label><br />\n";
	$cpt++;
}
echo "</td>\n";

$cpt=0;
if($nb_lv1>0) {
	echo "<td style='vertical-align:top; padding:2px;' class='lig-1'>\n";
	while($lig=mysqli_fetch_object($res_lv1)) {
		echo "<input type='checkbox' name='sans_lv1[]' id='opt_$cpt' value='$lig->opt' />\n";
		echo "<label for='opt_$cpt'>$lig->opt</label>\n";
		echo "<br />\n";
		$cpt++;
	}
	echo "</td>\n";
}

if($nb_lv2>0) {
	echo "<td style='vertical-align:top; padding:2px;' class='lig-1'>\n";
	while($lig=mysqli_fetch_object($res_lv2)) {
		echo "<input type='checkbox' name='sans_lv2[]' id='opt_$cpt' value='$lig->opt' />\n";
		echo "<label for='opt_$cpt'>$lig->opt</label>\n";
		echo "<br />\n";
		$cpt++;
	}
	echo "</td>\n";
}

if($nb_lv3>0) {
	echo "<td style='vertical-align:top; padding:2px;' class='lig-1'>\n";
	while($lig=mysqli_fetch_object($res_lv3)) {
		echo "<input type='checkbox' name='sans_lv3[]' id='opt_$cpt' value='$lig->opt' />\n";
		echo "<label for='opt_$cpt'>$lig->opt</label>\n";
		echo "<br />\n";
		$cpt++;
	}
	echo "</td>\n";
}

if($nb_autre>0) {
	echo "<td style='vertical-align:top; padding:2px;' class='lig-1'>\n";
	while($lig=mysqli_fetch_object($res_autre)) {
		echo "<input type='checkbox' name='sans_autre[]' id='opt_$cpt' value='$lig->opt' />\n";
		echo "<label for='opt_$cpt'>$lig->opt</label>\n";
		echo "<br />\n";
		$cpt++;
	}
	echo "</td>\n";
}



echo "</tr>\n";
echo "</table>\n";

echo "<input type='hidden' name='is_posted' value='y' />\n";
echo "<input type='hidden' name='projet' value='$projet' />\n";
echo "<p align='center'><input type='submit' name='ajouter' value='Ajouter' /></p>\n";

echo "</fieldset>
</form>\n";


//================================


$sql="SELECT * FROM gc_options_classes WHERE projet='$projet' ORDER BY classe_future,opt_exclue;";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)>0) {
	echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='suppr'>
	<fieldset class='fieldset_opacite50'>\n";
	echo "<p>Si vous souhaitez supprimer des contraintes préalablement définies, cochez et validez&nbsp;:</p>\n";
	$cpt=0;
	$classe_prec="";
	$alt=1;
	echo "<table class='boireaus' border='1' summary='Contraintes saisies'>\n";
	while($lig=mysqli_fetch_object($res)) {
		if($lig->classe_future!=$classe_prec) {
			if($cpt>0) {echo "</td></tr>\n";}
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'><td style='text-align:left;'>\n";
		}
		echo "<input type='checkbox' name='suppr[]' id='suppr_$cpt' value='$lig->id' /><label for='suppr_$cpt'> <b>$lig->classe_future</b>&nbsp;: Pas de $lig->opt_exclue</label><br />\n";
		$classe_prec=$lig->classe_future;
		$cpt++;
	}
	//echo "</p>\n";
	echo "</td></tr>\n";
	echo "</table>\n";
	echo "<input type='hidden' name='is_posted' value='y' />\n";
	echo "<input type='hidden' name='projet' value='$projet' />\n";
	echo "<p align='center'><input type='submit' name='supprimer' value='Supprimer' /></p>\n";
	echo "
	</fieldset>
</form>\n";
}

echo "<p><br /></p>
<p><em>NOTE&nbsp;:</em> Cette page de saisie des contraintes ne permet  de saisir actuellementque des contraintes simples.<br />
Vous poyvez préciser que vous ne pouvez pas avoir telle option dans telle(s) classe(s), mais in n'est pas encore possible de préciser que vous ne voulez pas telle combinaison d'options sur telle classe.</p>\n";

require("../lib/footer.inc.php");
?>
