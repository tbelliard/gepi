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

//======================================================================================

$sql="SELECT 1=1 FROM droits WHERE id='/mod_genese_classes/select_classes.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_genese_classes/select_classes.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Genèse des classes: Choix des classes',
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

$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : NULL;
$classe=isset($_POST['classe']) ? $_POST['classe'] : NULL;
$classes_futures=isset($_POST['classes_futures']) ? $_POST['classes_futures'] : NULL;
$classes_futures=preg_replace("/[^A-za-z0-9_,]/","",$classes_futures);
if($classes_futures=="") {unset($classes_futures);}

$choix_classes=isset($_POST['choix_classes']) ? $_POST['choix_classes'] : NULL;

if((isset($choix_classes))&&((isset($id_classe))||(isset($classes_futures)))) {
	$nb_reg1=0;
	$nb_reg2=0;
	$nb_err=0;

	$sql="DELETE FROM gc_divisions WHERE projet='$projet';";
	//echo "$sql<br />";
	$suppr=mysqli_query($GLOBALS["mysqli"], $sql);

	if(isset($id_classe)) {
		for($i=0;$i<count($id_classe);$i++) {
			// Il faudrait contrôler que les classes sont valides et éviter certains caractères pour 'classe'.
			$sql="INSERT INTO gc_divisions SET projet='$projet', id_classe='".$id_classe[$i]."', classe='".$classe[$id_classe[$i]]."', statut='actuelle';";
			//echo "$sql<br />";
			if($insert=mysqli_query($GLOBALS["mysqli"], $sql)) {$nb_reg1++;} else {$nb_err++;}
		}
	}
	
	if((isset($classes_futures))&&($classes_futures!="")) {
		$tab_tmp=explode(",",$classes_futures);
		for($i=0;$i<count($tab_tmp);$i++) {
			// Il faudrait contrôler que les classes sont valides et éviter certains caractères pour 'classe'.
			$sql="INSERT INTO gc_divisions SET projet='$projet', id_classe='0', classe='".$tab_tmp[$i]."', statut='future';";
			//echo "$sql<br />";
			if($insert=mysqli_query($GLOBALS["mysqli"], $sql)) {$nb_reg2++;} else {$nb_err++;}
		}
	}

	if($nb_err==0) {
		$msg="Regénération de la liste des classes effectuée: ";
		$msg.="$nb_reg1 classes actuelles et $nb_reg2 classes futures enregistrées.";
	}
	else {
		$msg="ERREUR lors de la regénération de la liste des classes: ";
		$msg.="$nb_reg1 classes actuelles et $nb_reg2 classes futures enregistrées.";
	}
}

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Genèse classe: Choix classes";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc.php");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

if((!isset($projet))||($projet=="")) {
	echo "<p style='color:red'>ERREUR: Le projet n'est pas choisi.</p>\n";
	require("../lib/footer.inc.php");
	die();
}

//echo "<div class='noprint'>\n";
echo "<p class='bold'><a href='index.php?projet=$projet'".insert_confirm_abandon().">Retour</a>";
echo "</p>\n";
//echo "</div>\n";

echo "<h2>Projet $projet</h2>\n";

$tab_id_div=array();
$tab_classe_fut=array();
$classes_futures="";
$sql="SELECT * FROM gc_divisions WHERE projet='$projet';";
$res_div=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_div)>0) {
	while($lig_div=mysqli_fetch_object($res_div)) {
		if($lig_div->statut=='actuelle') {
			$tab_id_div[]=$lig_div->id_classe;
		}
		else {
			$tab_classe_fut[]=$lig_div->classe;
			if($classes_futures!="") {$classes_futures.=",";}
			$classes_futures.=$lig_div->classe;
		}
	}
}

echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">\n";

$sql="SELECT id,classe FROM classes ORDER BY classe;";
$res_classes=mysqli_query($GLOBALS["mysqli"], $sql);
$nb_classes=mysqli_num_rows($res_classes);
// Ajouter des classes
echo "<p>Liste des classes actuelles&nbsp;:\n";
echo "</p>\n";

// Affichage sur 4/5 colonnes
$nb_classes_par_colonne=round($nb_classes/4);

echo "<table width='100%' summary='Choix des classes'>\n";
echo "<tr valign='top' align='center'>\n";

$cpt_i = 0;

echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
echo "<td align='left'>\n";

while($lig_clas=mysqli_fetch_object($res_classes)) {

	//affichage 2 colonnes
	if(($cpt_i>0)&&(round($cpt_i/$nb_classes_par_colonne)==$cpt_i/$nb_classes_par_colonne)){
		echo "</td>\n";
		echo "<td align='left'>\n";
	}

	echo "<input type='checkbox' name='id_classe[]' id='id_classe_$cpt_i' value='$lig_clas->id' ";
	if(in_array($lig_clas->id,$tab_id_div)) {echo "checked ";$temp_style=" style='font-weight:bold;'";} else {$temp_style="";}
	echo "onchange=\"checkbox_change('id_classe_$cpt_i');changement()\" ";
	echo "/><label for='id_classe_$cpt_i'><span id='texte_id_classe_$cpt_i'$temp_style>$lig_clas->classe</span></label>";
	echo "<input type='hidden' name='classe[$lig_clas->id]' value='$lig_clas->classe' />";
	echo "<br />\n";
	$cpt_i++;
}

echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";

echo js_checkbox_change_style('checkbox_change', 'texte_', 'y');

echo "<p>Ajouter une ou des classes futures&nbsp;:\n";
echo " <input type='text' name='classes_futures' value='$classes_futures' onchange=\"changement()\" /><br />\n";
echo "(<i>pour saisir plusieurs classes, mettre une virgule entre les classes</i>)</p>\n";

echo "<input type='hidden' name='projet' value='$projet' />\n";
echo "<p><input type='submit' name='choix_classes' value='Valider' /></p>\n";
echo "</form>\n";


echo "</blockquote>\n";

require("../lib/footer.inc.php");
?>
