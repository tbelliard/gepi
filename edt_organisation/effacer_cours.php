<?php

/**
 * Fichier destiné à permettre la suppression d'un cours
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
require_once("./choix_langue.php");

$titre_page = TITLE_DELETE_LESSON;
$affiche_connexion = 'yes';
$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions edt

require_once("./fonctions_edt.php");
require_once("./fonctions_cours.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

// Sécurité

if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}
// Sécurité supplémentaire par rapport aux paramètres du module EdT / Calendrier
if (param_edt($_SESSION["statut"]) != "yes" OR ( ($_SESSION["statut"] == 'professeur') AND (getSettingValue("edt_remplir_prof") != 'y') )) {
	Die(ASK_AUTHORIZATION_TO_ADMIN);
}
// On vérifie que le droit soit le bon pour le profil scolarité
	$autorise = "non";
if ($_SESSION["statut"] == "administrateur") {
	$autorise = "oui";
}
elseif ($_SESSION["statut"] == "scolarite" AND $gepiSettings['scolarite_modif_cours'] == "y") {
	$autorise = "oui";
}
elseif(($_SESSION["statut"] == 'professeur') AND (getSettingValue("edt_remplir_prof") == 'y')){
  $autorise = "oui";
}
else {
	$autorise = "non";
	exit('Vous n\'êtes pas autorisé à modifier les cours des emplois du temps, contacter l\'administrateur de Gepi');
}


// ===== Initialisation des variables =====
$type_edt = isset($_GET["type_edt"]) ? $_GET["type_edt"] : (isset($_POST["type_edt"]) ? $_POST["type_edt"] : NULL);
$identite = isset($_GET["identite"]) ? $_GET["identite"] : (isset($_POST["identite"]) ? $_POST["identite"] : NULL);
$supprimer_cours = isset($_GET["supprimer_cours"]) ? $_GET["supprimer_cours"] : (isset($_POST["supprimer_cours"]) ? $_POST["supprimer_cours"] : NULL);
$confirme_suppression = isset($_GET["confirme_suppression"]) ? "yes" : (isset($_POST["confirme_suppression"]) ? "yes" : "no");
$annuler_suppression = isset($_GET["annuler_suppression"]) ? "yes" : (isset($_POST["annuler_suppression"]) ? "yes" : "no");
$period_id=isset($_GET['period_id']) ? $_GET['period_id'] : (isset($_POST['period_id']) ? $_POST['period_id'] : NULL);
$message = "";

// ================= On supprime un cours si ça a été demandé

if (isset($supprimer_cours) AND $confirme_suppression=="yes") {
    if ($_SESSION["statut"] == "professeur" AND getSettingValue("edt_remplir_prof") == 'y' AND my_strtolower($identite) != my_strtolower($_SESSION["login"])){
      $message = CANT_DELETE_OTHER_COURSE;
    }
    else if (($_SESSION["statut"] == "administrateur") OR ($_SESSION["statut"] == "scolarite") OR (($_SESSION["statut"] == "professeur") AND (getSettingValue("edt_remplir_prof") == 'y'))){

        // ---- formattage du paramètre pour éviter une injection SQL
        settype($supprimer_cours, "int");
        // ---- En cas de rechargement de page, on ne fait pas la suppression deux fois.
        $test_avant_effacement = mysql_query("SELECT * FROM edt_cours WHERE id_cours= '".$supprimer_cours."'");
        if (mysql_num_rows($test_avant_effacement) != 0) {
            // --- suppression effective.
            $effacer_cours = mysql_query("DELETE FROM edt_cours WHERE id_cours = '".$supprimer_cours."'") OR die ('Impossible d\'effacer ce cours');
            if (!$effacer_cours) {
	            $message = DELETE_FAILURE;
            }
            else {
	            $message = DELETE_SUCCESS;
            }
        }
        else {
	        $message = DELETE_NOTHING;
        }
    }
    else {
	        $message = DELETE_BAD_RIGHTS;
    }
}

$_SESSION["message"] = $message;

/*/ CSS et js particulier à l'EdT
$javascript_specifique = "edt_organisation/script/fonctions_edt";
$style_specifique = "templates/".NameTemplateEDT()."/css/style_edt";

// +++++++++++++++ entête de Gepi +++++++++
// +++++++++++++++ entête de Gepi +++++++++

// On ajoute le menu EdT
require_once("./menu.inc.php");
*/
if ($confirme_suppression=="yes" OR $annuler_suppression=="yes"){
	$aff_refresh = "onload=\"window.close();\"";
}else{
	$aff_refresh = "onunload=\"window.opener.location.href='./index_edt.php?visioedt=prof1&amp;login_edt=".$identite."&amp;type_edt_2=prof';\"";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html lang="fr">
	<head>
	<title>Gepi - Supprimer un cours</title>
	<link rel="stylesheet" type="text/css" href="./style_edt.css" />
	<script type='text/javascript' src='./script/fonctions_edt.js'></script>
	</head>
	<body <?php echo $aff_refresh; ?>>


<div id="edt_popup_contain">

<!-- la page du corps de l'EdT -->

	<div id="edt_popup_lecorps">

<?php

// Si tout est ok, on affiche le cours reçu en GET ou POST
if ($autorise == "oui") {

	// On affiche les différents items du cours
echo '
<div class="ButtonBarCenter">
	<fieldset>
		<legend>'.DELETE_CONFIRM.'</legend>
		<form action="effacer_cours.php" method="post">
		    <input type="hidden" name="supprimer_cours" value="'.$supprimer_cours.'" />
		    <input type="hidden" name="type_edt" value="'.$type_edt.'" />
		    <input type="hidden" name="identite" value="'.$identite.'" />	';
echo '	
		<input type="submit" name="confirme_suppression" value="'.CONFIRM_BUTTON.'" />
		<input type="submit" name="annuler_suppression" value="'.ABORT_BUTTON.'" />
		
		</form>';
echo '
	</fieldset>
</div>
	';

}// if $autorise...
else {
	die();
}
?>

	</div>

<?php

// inclusion du footer
require("../lib/footer.inc.php");
?>