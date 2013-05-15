<?php

/*
 *
 *
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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


$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");


$titre_page = "Consultation d'un ".$gepiSettings['denomination_eleve'];
// fonctions complémentaires et/ou librairies utiles


// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == "c") {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == "0") {
    header("Location: ../logout.php?auto=1");
    die();
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}

// ======================== CSS et js particuliers ========================
$utilisation_win = "oui";
$utilisation_jsdivdrag = "oui";

$avec_js_et_css_edt="y";

if (getSettingValue("active_module_absence")=='2') {
  $style_specifique[] = "mod_abs2/lib/abs_style";
  $javascript_specifique[] = "lib/tablekit";
$dojo=true;
}
$style_specifique[] = "eleves/visu_eleve";

$ele_login=isset($_POST['ele_login']) ? $_POST['ele_login'] : (isset($_GET['ele_login']) ? $_GET['ele_login'] : NULL);
$onglet=isset($_POST['onglet']) ? $_POST['onglet'] : (isset($_GET['onglet']) ? $_GET['onglet'] : NULL);
$onglet2=isset($_POST['onglet2']) ? $_POST['onglet2'] : (isset($_GET['onglet2']) ? $_GET['onglet2'] : NULL);
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);

$quitter_la_page=isset($_POST['quitter_la_page']) ? $_POST['quitter_la_page'] : (isset($_GET['quitter_la_page']) ? $_GET['quitter_la_page'] : NULL);
if((isset($quitter_la_page))&&($quitter_la_page=='y')) {
	$chaine_quitter_page_ou_non="&amp;quitter_la_page=y";
	$champ_quitter_page_ou_non="<input type='hidden' name='quitter_la_page' value='y' />\n";
}
else {
	$chaine_quitter_page_ou_non="";
	$champ_quitter_page_ou_non="";
}

$annee = strftime("%Y");
$mois = strftime("%m");
$jour = strftime("%d");

if($mois>7) {$date_debut_tmp="01/09/$annee";} else {$date_debut_tmp="01/09/".($annee-1);}

$date_debut_disc=isset($_POST['date_debut_disc']) ? $_POST['date_debut_disc'] : (isset($_SESSION['date_debut_disc']) ? $_SESSION['date_debut_disc'] : $date_debut_tmp);
$date_fin_disc=isset($_POST['date_fin_disc']) ? $_POST['date_fin_disc'] : (isset($_SESSION['date_fin_disc']) ? $_SESSION['date_fin_disc'] : "$jour/$mois/$annee");

// ===================== entete Gepi ======================================//
require_once("../lib/header.inc.php");
// ===================== fin entete =======================================//

$page="visu_eleve.php";

//debug_var();

include('visu_eleve.inc.php');

?>

<?php
// Inclusion du bas de page
require_once("../lib/footer.inc.php");
?>
