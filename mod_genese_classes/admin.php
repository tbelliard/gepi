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
$accessibilite="y";
$titre_page = "Genèse classe: Activation/désactivation";
$niveau_arbo = 1;
$gepiPathJava="./..";
$post_reussi=FALSE;
$msg = '';



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



$sql="SELECT 1=1 FROM droits WHERE id='/mod_genese_classes/admin.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_genese_classes/admin.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Genèse des classes: Activation/désactivation',
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


$msg = '';
if (isset($_POST['activer'])) {
	check_token();
	if (!saveSetting("active_mod_genese_classes", $_POST['activer'])) {
		$msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation !";
	}
	else {
		$msg = "Enregistrement effectué.";
		$post_reussi=TRUE;
	}
}

//**************** EN-TETE *****************
//$titre_page = "Genèse classe: Activation/désactivation";
//echo "<div class='noprint'>\n";
//echo "</div>\n";
//**************** FIN EN-TETE *****************


// ====== Inclusion des balises head et du bandeau =====
include_once("../lib/header_template.inc.php");

if (!suivi_ariane($_SERVER['PHP_SELF'],"Gestion Genèse classe"))
		echo "erreur lors de la création du fil d'ariane";
/****************************************************************
			FIN HAUT DE PAGE
****************************************************************/



/****************************************************************
			BAS DE PAGE
****************************************************************/
$tbs_microtime	="";
$tbs_pmv="";
require_once ("../lib/footer_template.inc.php");

/****************************************************************
			On s'assure que le nom du gabarit est bien renseigné
****************************************************************/
if ((!isset($_SESSION['rep_gabarits'])) || (empty($_SESSION['rep_gabarits']))) {
	$_SESSION['rep_gabarits']="origine";
}

//==================================
// Décommenter la ligne ci-dessous pour afficher les variables $_GET, $_POST, $_SESSION et $_SERVER pour DEBUG:
// $affiche_debug=debug_var();


$nom_gabarit = '../templates/'.$_SESSION['rep_gabarits'].'/mod_genese_classes/admin_template.php';

$tbs_last_connection=""; // On n'affiche pas les dernières connexions
include($nom_gabarit);

/*






//debug_var();

//echo "<div class='noprint'>\n";
echo "<p class='bold'><a href='../accueil_modules.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
echo "</p>\n";
//echo "</div>\n";

echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>\n";

echo "<p>
<input type='radio' name='activer' id='activer_y' value='y' ";
if (getSettingValue('active_mod_genese_classes')=='y') {echo ' checked';}
echo " />&nbsp;<label for='activer_y' style='cursor: pointer;'>Activer le module Genèse des classes</label><br />
<input type='radio' name='activer' id='activer_n' value='n' ";
if (getSettingValue('active_mod_genese_classes')=='n') {echo ' checked';}
echo " />&nbsp;<label for='activer_n' style='cursor: pointer;'>Désactiver le module Genèse des classes</label>
<br />\n";

echo " <input type='submit' name='valider' value='Valider' /></p>\n";

echo "</form>\n";

require("../lib/footer.inc.php");
 * 
 */
?>
