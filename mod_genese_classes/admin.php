<?php
/* $Id$ */
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
}



$sql="SELECT 1=1 FROM droits WHERE id='/mod_genese_classes/admin.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_genese_classes/admin.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Génèse des classes: Activation/désactivation',
statut='';";
$insert=mysql_query($sql);
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
    if (!saveSetting("active_mod_genese_classes", $_POST['activer'])) {
		$msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation !";
	}
	else {$msg = "Enregistrement effectué.";}
}

//**************** EN-TETE *****************
$titre_page = "Génèse classe: Activation/désactivation";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

//debug_var();

//echo "<div class='noprint'>\n";
echo "<p class='bold'><a href='../accueil_modules.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
echo "</p>\n";
//echo "</div>\n";

echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>\n";

echo "<p>
<input type='radio' name='activer' id='activer_y' value='y' ";
if (getSettingValue('active_mod_genese_classes')=='y') {echo ' checked';}
echo " />&nbsp;<label for='activer_y' style='cursor: pointer;'>Activer le module Génèse des classes</label><br />
<input type='radio' name='activer' id='activer_n' value='n' ";
if (getSettingValue('active_mod_genese_classes')=='n') {echo ' checked';}
echo " />&nbsp;<label for='activer_n' style='cursor: pointer;'>Désactiver le module Génèse des classes</label>
<br />\n";

echo " <input type='submit' name='valider' value='Valider' /></p>\n";

echo "</form>\n";

require("../lib/footer.inc.php");
?>
