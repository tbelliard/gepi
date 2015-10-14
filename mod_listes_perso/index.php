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
   && $utilisateur->getStatut()!=="professeur") {
	header("Location: ../logout.php?auto=1");
	die("Vous n'avez pas les droits pour cette page.");
}

include_once 'lib/fonction_listes.php';

// a bouger vers définition de la base et ùise à jour
verifieTableCree();

//==============================================
//Choix liste
//==============================================
//$flag = filter_input(INPUT_POST, '') ? filter_input(INPUT_POST, '') : NULL;
$nouvelleListe = filter_input(INPUT_POST, 'nouvelleListe') === 'Nouvelle liste' ? TRUE : NULL;
$tableauChoisi = filter_input(INPUT_POST, 'tableauChoisi') ? filter_input(INPUT_POST, 'tableauChoisi') : NULL;
if ($nouvelleListe) {
	$idListe = "";
	unset($_SESSION['liste_perso']);
} elseif ($tableauChoisi) {
	$idListe = $tableauChoisi;
	unset($_SESSION['liste_perso']);
} else {
	$idListe = isset($_SESSION['liste_perso']['id']) ? $_SESSION['liste_perso']['id'] : '';
}
chargeListe($idListe);



//==============================================
//Définition de liste
//==============================================
$sauveDefinitionListe = filter_input(INPUT_POST, 'sauveDefinitionListe') === 'Sauvegarde' ? TRUE : FALSE;
$supprimerDefinitionListe = filter_input(INPUT_POST, 'sauveDefinitionListe') === 'Supprimer' ? TRUE : FALSE;

if ($sauveDefinitionListe) {
	unset($_SESSION['liste_perso']);
}

$idListe = filter_input(INPUT_POST, 'idListe') ? filter_input(INPUT_POST, 'idListe') : (isset($_SESSION['liste_perso']['idListe']) ? $_SESSION['liste_perso']['idListe'] : '');
$nomListe = filter_input(INPUT_POST, 'nomListe') ? filter_input(INPUT_POST, 'nomListe') : (isset($_SESSION['liste_perso']['nomListe']) ? $_SESSION['liste_perso']['nomListe'] : FALSE);
$sexeListe = filter_input(INPUT_POST, 'sexeListe') ? TRUE : (isset($_SESSION['liste_perso']['sexeListe']) ? $_SESSION['liste_perso']['sexeListe'] : FALSE);
$classeListe = filter_input(INPUT_POST, 'classeListe') ? TRUE : (isset($_SESSION['liste_perso']['classeListe']) ? $_SESSION['liste_perso']['classeListe'] : FALSE);
$nbColonneListe = filter_input(INPUT_POST, 'nbColonneListe') ? filter_input(INPUT_POST, 'nbColonneListe') : (isset($_SESSION['liste_perso']['nbColonneListe']) ? $_SESSION['liste_perso']['nbColonneListe'] : 0);
$photoListe = filter_input(INPUT_POST, 'photoListe') ? filter_input(INPUT_POST, 'photoListe') : (isset($_SESSION['liste_perso']['photo']) ? $_SESSION['liste_perso']['photo'] : 0);

if ($sauveDefinitionListe) {
	if (strlen($nomListe)) {
		sauveDefListe($idListe,$nomListe, $sexeListe, $classeListe, $photoListe, $nbColonneListe);
	}
}





debug_var();
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
			<input type="submit" id="nouvelleListe" name="nouvelleListe" value="Nouvelle liste" />
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
			<input type="submit" id="sauveTableau" name="sauveTableau" value="Sauvegarder" />
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
		<fieldset>
			<p>
				<legend>Ajouter/supprimer des colonnes</legend>
				<input type="text" 
					   maxlength="50" 
					   placeholder="Nom de la nouvelle liste" 
					   name="nomListe" 
					   value="<?php if ($_SESSION['liste_perso']['nom']) {echo $_SESSION['liste_perso']['nom'];} ?>"
					   />
				Afficher les colonnes :
				<label for="sexeListe">Sexe</label>
				<input type="checkbox" 
					   name="sexeListe" 
					   id="sexeListe" 
					   value=1
					   <?php if (isset($_SESSION['liste_perso']['sexe']) && $_SESSION['liste_perso']['sexe']) {echo " checked='checked' ";} ?>
					   />
				<label for="classeListe">Classe</label>
				<input type="checkbox" 
					   name="classeListe" 
					   id="classeListe" 
					   value=1
					   <?php if ($_SESSION['liste_perso']['classe']) {echo " checked='checked' ";} ?>
					   />
				<label for="photoListe">Photos</label>
				<input type="checkbox" 
					   name="photoListe" 
					   id="photoListe" 
					   value=1
					   <?php if ($_SESSION['liste_perso']['photo']) {echo " checked='checked' ";} ?>
					   />
				<label for="nbColonneListe">Nombre de colonnes</label>
				<input type="text" 
					   maxlength="2"
					   name="nbColonneListe" 
					   id="nbColonneListe"
					   value="<?php echo count($_SESSION['liste_perso']['colonnes']); ?>"
					   size="1"
					   />
				<input type="hidden" id="idListe" name="idListe" value="<?php echo $idListe ?>" />
				<input type="submit" id="sauveDefinitionListe" name="sauveDefinitionListe" value="Sauvegarder" />
			</p>
			<p>
				<input type="submit" id="sauveDefinitionListe" name="sauveDefinitionListe" value="Supprimer" />
			</p>
		</fieldset>
	</form>
</div>
<div id="laListe" class="div_construit" style="display:block;">
	<form action="index.php" name="formAjouteColonne" method="post">
		<fieldset id="cadre_laListe">
			<legend></legend>
	
		</fieldset>
	</form>
</div>

<script type="text/javascript" >
	afficher_cacher("affichage");
	afficher_cacher("eleves");
	afficher_cacher("construction");
	activer("tableau");
</script>
<?php
if ($nouvelleListe) {
?>
<script type="text/javascript" >
	desactiver("tableau");
	activer("construction");
</script>
<?php
}

require_once("../lib/footer.inc.php");
