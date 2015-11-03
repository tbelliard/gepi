<?php
/**
 *
 *
 * Copyright 2015 Régis Bouguin
 *
 * This file and the mod_abs2 module is distributed under GPL version 3, or
 * (at your option) any later version.
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

// Initialisations files
include("../lib/initialisationsPropel.inc.php");
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

//recherche de l'utilisateur avec propel
$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

$sauver = filter_input(INPUT_POST, 'sauver') ==="enregistrer" ? TRUE : FALSE ;
$ouvre = filter_input(INPUT_POST, 'ouvre') ? filter_input(INPUT_POST, 'ouvre') : 'n';

//==============================================
include_once 'lib/fonction_listes.php';
//==============================================

if ($sauver) {
	if (EnregistreDroitListes($ouvre)) {
		if (DroitSurListeOuvert()) {
			$msg = "<span class='vert'>Les enseignants peuvent créer des listes personnelles</span>";
		} else {
			$msg = "Les enseignants n'ont pas accès aux listes personnelles";
		}
	}
}

$droit = DroitSurListeOuvert();

//==============================================
$style_specifique[] = "mod_listes_perso/lib/style_liste";
$javascript_specifique = "mod_listes_perso/lib/js_listes_perso";
$titre_page = "Ouverture des listes personnelles";
$utilisation_jsdivdrag = "non";
$_SESSION['cacher_header'] = "y";
if (!suivi_ariane($_SERVER['PHP_SELF'],"Listes personnelles")) {echo "erreur lors de la création du fil d'ariane";}
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

?>

<h2>Ouverture du module <em>Listes personnelles</em></h2>
<form action="index_admin.php" method="post" name="formulaire" id="formulaire">
	<fieldset class="tableau">
		<p>
			<span class="colonne colAdmin colGaucheAdmin" >
				<input type="radio" 
					   id="ouvreDroit" 
					   name="ouvre"
					   <?php if($droit) {echo " checked='checked' ";} ?>
					   value="y" />
			</span>
			<span class="colonne colAdmin" >
				<label for="ouvreDroit">Ouvrir les droits de créer des listes personnelles</label>
			</span>
		</p>
		<p>
			<span class="colonne colAdmin colGaucheAdmin">
				<input type="radio" 
					   id="fermeDroit" 
					   name="ouvre"
					   <?php if(!$droit) {echo " checked='checked' ";} ?>
					   value="n" />
			</span>
			<span class="colonne colAdmin" >
				<label for="fermeDroit">Interdire la création de listes personnelles</label>
			</span>
		</p>
		<p>
			<input type="submit" name="sauver" id="sauver" value="enregistrer" />
		</p>		
	</fieldset>
</form>


<?php
require_once("../lib/footer.inc.php");
