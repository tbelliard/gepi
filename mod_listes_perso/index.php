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


//On vérifie si le module est activé
//if (getSettingValue("active_module_liste_perso")!='y') {
if (getSettingAOui("active_module_liste_perso")) {
    //die("Le module n'est pas activé.");
}
if ($utilisateur->getStatut()!=="cpe" 
   && $utilisateur->getStatut()!=="scolarite" 
   && $utilisateur->getStatut()!=="professeur")  {
	header("Location: ../logout.php?auto=1");
	die("Vous n'avez pas les droits pour cette page.");
}


	


//==============================================
$style_specifique[] = "mod_listes_perso/lib/style_liste";
$javascript_specifique = "mod_listes_perso/lib/js_listes_perso";
$titre_page = "Listes personnelles";
$utilisation_jsdivdrag = "non";
$_SESSION['cacher_header'] = "y";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

?>

<ul class="menu_entete_liste">
    <li class="menu_liste"
		id='menu_lien_tableau'
		onclick=" affiche('tableau'); activer('tableau');
			masque('construction'); desactiver('construction');
			masque('eleves'); desactiver('eleves');
			masque('affichage'); desactiver('affichage'); "
		>
		<a href="#lien_tableau">Listes perso</a>
	</li>
    <li class="menu_liste"
		id='menu_lien_affichage'
		onclick=" masque('construction'); desactiver('construction');
			masque('tableau'); desactiver('tableau');
			masque('eleves');desactiver('eleves');
			affiche('affichage'); activer ('affichage'); "
		>
		<a href="#lien_affichage">Modifier</a>
	</li>
    <li class="menu_liste" 
		id='menu_lien_eleves'
		onclick="affiche('eleves'); activer ('eleves');
			masque('tableau'); desactiver('tableau');
			masque('affichage');desactiver('affichage');
			masque('construction'); desactiver ('construction'); "
		>
		<a href="#lien_eleves">Élèves</a>
	</li>
    <li class="menu_liste" 
		id='menu_lien_construction'
		onclick="affiche('construction');  activer('construction');
			masque('tableau'); desactiver ('tableau');
			masque('affichage');  desactiver('affichage');
			masque('eleves');  desactiver('eleves'); "
		>
		<a href="#lien_construction">Construction</a>
	</li>
</ul>

<div id="tableau" class="div_construit" style="display:block;">
	<p><a id='lien_tableau'></a></p>
	<form action="index.php" name="formChoixTableau" method="post">
		<fieldset>
			<legend>Choix de la liste</legend>
			<select name="tableauChoisi" 
					id="tableauChoisi"
					onchange="this.form.submit()" >
				<option value="">Choisissez une liste</option>
				<option value="1">choix2</option>
			</select>
			<input type="submit" id="sauveChoixTableau" name="sauveChoixTableau" value="Afficher" />
		</fieldset>
	</form>
</div>
<script type="text/javascript" >
	document.getElementById('sauveChoixTableau').classList.add('invisible');
</script>

<div id="affichage" class="div_construit" style="display:block;">
	<p><a id='lien_affichage'></a></p>
	<form action="index.php" name="formSauveTableau" method="post">
		<fieldset class="center">
			<legend>Modifier et sauvegarder</legend>
			<input type="submit" id="sauveChoixTableau" name="sauveTableau" value="Sauvegarder" />
			<input type="reset" id="reinitialiseTableau" name="reinitialiseTableau" value="Réinitialiser" />
		</fieldset>
	</form>
</div>

<div id="eleves" class="div_construit" style="display:block;">
	<p><a id='lien_eleves'></a></p>
	<form action="index.php" name="formAjouteEleve" method="post">
		<fieldset class="center">
			<legend>Ajouter des membres à la liste</legend>
		</fieldset>
	</form>
</div>

<div id="construction" class="div_construit" style="display:block;">
	<p><a id='lien_construction'></a></p>
	<form action="index.php" name="formAjouteColonne" method="post">
		<fieldset class="center">
			<legend>Ajouter/supprimer des colonnes</legend>
		</fieldset>
	</form>
</div>
<div id="laListe" class="div_construit" style="display:block;">
	
</div>

<script type="text/javascript" >
	afficher_cacher("affichage");
	afficher_cacher("eleves");
	afficher_cacher("construction");
	activer("tableau");
</script>
<?php
require_once("../lib/footer.inc.php");
