<?php

/**
 *
 * @version $Id$
 *
 * Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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

$titre_page = "Gérer les groupes de l'EdT<br />Professeurs";
$affiche_connexion = "oui";
$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions complémentaires et/ou librairies utiles


// Resume session
$resultat_session = resumeSession();
if ($resultat_session == "c") {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == "0") {
    header("Location: ../logout.php?auto=1");
    die();
}

// Sécurité
if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}

// ======================= Initialisation des variables ================
$id_gr = isset($_GET["id_gr"]) ? $_GET["id_gr"] : NULL;
$classe_e = isset($_GET["cla"]) ? $_GET["cla"] : NULL;

// ============================ Traitement des données ========================== //
$query_p = mysql_query("SELECT login, nom, prenom FROM utilisateurs WHERE statut = 'professeur' AND etat = 'actif' ORDER BY nom, prenom")
						OR trigger_error('Impossible de lire la liste des professeurs.', E_USER_ERROR);
	$nbre_p = mysql_num_rows($query_p);


	$aff_select_profs .= '
	<select name="choix_prof" onchange="">
		<option value="plusieurs">Plusieurs professeurs</option>
	';

	for($i = 0 ; $i < $nbre_p ; $i++){

		$login_p[$i] = mysql_result($query_p, $i, "login");
		$nom_p[$i] = mysql_result($query_p, $i, "nom");
		$prenom_p[$i] = mysql_result($query_p, $i, "prenom");

		$aff_select_profs .= '
		<option value="'.$login_p[$i].'">'.$nom_p[$i].' '.$prenom_p[$i].'</option>';

	}
	$aff_select_profs .= '</select>';



// =========================== Fin du traitement des données ==================== //


// ======================== CSS et js particuliers ========================
$utilisation_win = "oui";
$utilisation_jsdivdrag = "non";
$javascript_specifique = "edt_gestion_gr/script/fonctions_edt2.js";
$style_specifique = "edt_gestion_gr/style2_edt.css";

// ===================== entete Gepi ======================================//
require_once("../lib/header.inc");
// ===================== fin entete =======================================//

?>

<hr />


<hr />

<form name="ch_profs" action="edt_liste_profs.php" metho="post">

	<fieldset id="choix_prof">

		<legend>Ajouter un professeur</legend>

			<input type="hidden" name="action" value="ajouter" />

			<?php echo $affselect_profs; ?>

	</fieldset>

</form>

<?php
// Inclusion du bas de page
require_once("./lib/footer.inc.php");
?>
