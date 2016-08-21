<?php
/*
 * $Id: index.php 2554 2008-10-12 14:49:29Z crob $
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

// SQL : INSERT INTO droits VALUES ( '/mod_ooo/formulaire_retenue.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Modèle Ooo : formulaire retenue', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/mod_ooo/formulaire_retenue.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Modèle Ooo : formulaire retenue', '');;";
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
	die();
}


//debug_var();


// recupération des parametres
$is_posted=isset($_POST['is_posted']) ? $_POST['is_posted'] : (isset($_GET['is_posted']) ? $_GET['is_posted'] : NULL);

if (isset($is_posted)) {
  if ($is_posted=='y') {
		$_SESSION['retenue_date']=isset($_POST['date']) ? $_POST['date'] : (isset($_GET['date']) ? $_GET['date'] : NULL);
		$_SESSION['retenue_nom_prenom_elv']=isset($_POST['nom_prenom_elv']) ? $_POST['nom_prenom_elv'] : (isset($_GET['nom_prenom_elv']) ? $_GET['nom_prenom_elv'] : NULL);
		$_SESSION['retenue_classe_elv']=isset($_POST['classe_elv']) ? $_POST['classe_elv'] : (isset($_GET['classe_elv']) ? $_GET['classe_elv'] : NULL);
		$_SESSION['retenue_nom_resp']=isset($_POST['nom_resp']) ? $_POST['nom_resp'] : (isset($_GET['nom_resp']) ? $_GET['nom_resp'] : NULL);
		$_SESSION['retenue_fct_resp']=isset($_POST['fct_resp']) ? $_POST['fct_resp'] : (isset($_GET['fct_resp']) ? $_GET['fct_resp'] : NULL);

		if (isset($NON_PROTECT["motif"])){
				$_SESSION['retenue_motif']=$NON_PROTECT["motif"];
		}		
		if (isset($NON_PROTECT["travail"])){
				$_SESSION['retenue_travail']=$NON_PROTECT["travail"];
		}
  // on renvoie sur retenue.php avec mode=formulaire
  header("Location: ./retenue.php?mode=formulaire_retenue");
		die();
  }
}


// End standart header
$titre_page = "Formulaire de retenue";
require_once("../lib/header.inc.php");

echo "<p class='bold'><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";
echo "<p>&nbsp;</p>";
echo "<p class='bold'>Détails de la retenue";
echo "&nbsp;</p>\n";

echo "<form enctype='multipart/form-data' action='./formulaire_retenue.php' method='post' name='formulaire'>\n";
$alt=1;
// Si aucune date n'est encore saisie, proposer la date du jour
	$annee = strftime("%Y");
	$mois = strftime("%m");
	$jour = strftime("%d");
	$display_date = $jour."/".$mois."/".$annee;

	echo "<table class='boireaus' border='1' summary='Details incident'>\n";
	
	// Date 
	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>\n";
	echo "Date de l'incident&nbsp;:";
	echo "</td>\n";

	echo "<td style='text-align:left;'>\n";
	//Configuration du calendrier
	include("../lib/calendrier/calendrier.class.php");
	$cal = new Calendrier("formulaire", "date");

	echo "<input type='text' name='date' id='date' size='10' value=\"".$display_date."\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />\n";
	echo "<a href=\"#calend\" onClick=\"".$cal->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Petit calendrier\" /></a>\n";
	echo "</td>\n";
	echo "<td width='1%' style='text-align:right;'>\n";
	echo "<input type='submit' name='enregistrer' value='G&eacute;n&eacute;rer la retenue' />\n";
	echo "</td>\n";
	echo "</tr>\n";

	//Nom et prénom de l'élève
	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>\n";
	echo "Nom et prénom de l'élève&nbsp;:";
	echo "</td>\n";
	echo "<td style='text-align:left;'";
	echo " colspan='2'";
	echo ">\n";
	echo "<input type='text' name='nom_prenom_elv' id='id_nom_prenom_elv' size='40' value=\"\" />\n";
	echo "</td>\n";

	
	//Classe de l'élève
	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>\n";
	echo "Classe de l'élève&nbsp;:";
	echo "</td>\n";
	echo "<td style='text-align:left;'";
	echo " colspan='2'";
	echo ">\n";
	echo "<input type='text' name='classe_elv' id='id_classe_elv' size='20' value=\"\" />\n";
	echo "</td>\n";

	//Motif
	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>\n";
	echo "Motif de la retenue&nbsp;:";
	echo "</td>\n";
	echo "<td style='text-align:left;'";
	echo " colspan='2'";
	echo ">\n";
	echo "<textarea id=\"id_motif\" class='wrap' name=\"no_anti_inject_motif\" rows='4' cols='80'></textarea>\n";
	echo "</td>\n";

	//travail
	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>\n";
	echo "Travail donné&nbsp;:";
	echo "</td>\n";
	echo "<td style='text-align:left;'";
	echo " colspan='2'";
	echo ">\n";
	echo "<textarea id=\"id_travail\" class='wrap' name=\"no_anti_inject_travail\" rows='3' cols='80'></textarea>\n";
	echo "</td>\n";

	
	//Nom du responsable de la retenue
	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>\n";
	echo "Nom du responsable&nbsp;:";
	echo "</td>\n";
	echo "<td style='text-align:left;'";
	echo " colspan='2'";
	echo ">\n";
	echo "<input type='text' name='nom_resp' id='id_nom_resp' size='40' value=\"\" />\n";
	echo "</td>\n";

	
	//fonction du responsable
	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>\n";
	echo "Fonction responsable&nbsp;:";
	echo "</td>\n";
	echo "<td style='text-align:left;'";
	echo " colspan='2'";
	echo ">\n";
	echo "<input type='text' name='fct_resp' id='id_fct_resp' size='25' value=\"\" />\n";
	echo "</td>\n";

    echo "</table>";
	
echo "<input type='hidden' name='is_posted' value='y' />\n";
echo "</form>\n";
echo "<p>&nbsp;</p>";
echo "<p class='bold'>REMARQUE : Les retenues saisies par le formulaire ci-dessus ne sont pas enregistrées dans le module discipline.";
echo "&nbsp;</p>\n";
	

require("../lib/footer.inc.php");

?>
