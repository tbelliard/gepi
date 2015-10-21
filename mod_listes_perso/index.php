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

// a bouger vers définition de la base et mise à jour
verifieTableCree();

$idListe = NULL;
$nomListe = NULL;
$sexeListe = NULL;
$classeListe = NULL;
$nbColonneListe = NULL;
$photoListe = NULL;
//$colonnes = array();

//==============================================
// Action demandée
//==============================================
$tableauChoisi = filter_input(INPUT_POST, 'tableauChoisi') ? filter_input(INPUT_POST, 'tableauChoisi') : NULL;
$nouvelleListe = filter_input(INPUT_POST, 'nouvelleListe') === 'Nouvelle liste' ? TRUE : NULL;
$sauveDefinitionListe = filter_input(INPUT_POST, 'sauveDefinitionListe') === 'Sauvegarder' ? TRUE : FALSE;
$supprimerDefinitionListe = filter_input(INPUT_POST, 'sauveDefinitionListe') === 'Supprimer' ? TRUE : FALSE;
$sauveTitreColonne = filter_input(INPUT_POST, 'action') === 'sauveTitreColonne' ? TRUE : FALSE;

if ($nouvelleListe) { //===== Nouvelle liste =====
	$idListe = "";
	unset($_SESSION['liste_perso']);

} elseif ($tableauChoisi) { //===== Choix d'une liste =====
	if ((int)$tableauChoisi === -1) {
		$idListe = '';
	} else {
		$idListe = $tableauChoisi;
	}
	unset($_SESSION['liste_perso']);
	
	//charger définition de liste

} elseif ($sauveDefinitionListe) { //===== Création/modification d'une liste =====
	unset($_SESSION['liste_perso']);
	
	//charger définition de liste
	$idListe = DonneeEnPostOuSession('idListe', 'id', '');
	$nomListe = DonneeEnPostOuSession('nomListe', 'nom',FALSE);
	$sexeListe = DonneeEnPostOuSession('sexeListe', 'sexe',FALSE);
	$classeListe = DonneeEnPostOuSession('classeListe', 'classe',FALSE);
	$photoListe = DonneeEnPostOuSession('photoListe', 'photo',0);
	$colonnes = isset($_SESSION['liste_perso']['colonnes']) ? $_SESSION['liste_perso']['colonnes'] : NULL;
	$nbColonneListe = DonneeEnPostOuSession('nbColonneListe', 'nbColonne',0);
	
	if (strlen($nomListe)) {
		sauveDefListe($idListe,$nomListe, $sexeListe, $classeListe, $photoListe, $nbColonneListe);
		//===== On met tout en session =====
		$_SESSION['liste_perso']['id'] = $idListe;
		$_SESSION['liste_perso']['nom'] = $nomListe;
		$_SESSION['liste_perso']['sexe'] = $sexeListe;
		$_SESSION['liste_perso']['classe'] = $classeListe;
		$_SESSION['liste_perso']['nbColonne'] = $nbColonneListe;
		$_SESSION['liste_perso']['photo'] = $photoListe;
		$colonnes = LitColonnes($idListe);
		$_SESSION['liste_perso']['colonnes'] = $colonnes;
	}
	
} elseif ($supprimerDefinitionListe) { //===== Suppression d'une liste =====
		
} elseif ($sauveTitreColonne) { //===== Un titre de colonne a changé =====
	SauveTitreColonne();
	$idListe = filter_input(INPUT_POST, 'id_def');
} else { //===== Sinon on vérifie s'il y a une liste en mémoire
	$idListe = isset($_SESSION['liste_perso']['id']) ? $_SESSION['liste_perso']['id'] : '';
}

//==============================================
//Charge tableau
//==============================================
chargeListe($idListe);

$idListe = $_SESSION['liste_perso']['id'] ;
$nomListe = $_SESSION['liste_perso']['nom'] ;
$sexeListe = $_SESSION['liste_perso']['sexe'] ;
$classeListe = $_SESSION['liste_perso']['classe'] ;
$nbColonneListe = $_SESSION['liste_perso']['nbColonne'] ;
$photoListe = $_SESSION['liste_perso']['photo'] ;
$colonnes = $_SESSION['liste_perso']['colonnes'] ;

// debug_var(); // Ne fonctionne pas, $_SESSION['liste_perso']['colonnes'] est un objet, non géré par debug_var()
// var_dump($_POST);
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
				<option value="-1">Choisissez une liste</option>
<?php
$tableau = chargeTableau();
while ($obj = $tableau->fetch_object()) { ?>
				<option value="<?php echo $obj->id; ?>"
						<?php if ($obj->id === $idListe) { echo " selected='selected' "; } ?>
						><?php echo $obj->nom; ?></option>
<?php }	?>		
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
			<legend>Ajouter/supprimer des colonnes</legend>
			<p class="colonne colG65">
				<input type="hidden"
					   name="idListe"
					   value="<?php if ($idListe) {echo $idListe;} ?>"
					   />
				<input type="text" 
					   maxlength="50" 
					   placeholder="Nom de la nouvelle liste" 
					   name="nomListe" 
					   value="<?php if ($nomListe) {echo $nomListe;} ?>"
					   />
				Afficher les colonnes :
				<label for="sexeListe">Sexe&nbsp;→</label>
				<input type="checkbox" 
					   name="sexeListe" 
					   id="sexeListe" 
					   value=1
					   <?php if (isset($sexeListe) && $sexeListe) {echo " checked='checked' ";} ?>
					   />
				<label for="classeListe">Classe&nbsp;→</label>
				<input type="checkbox" 
					   name="classeListe" 
					   id="classeListe" 
					   value=1
					   <?php if ($classeListe) {echo " checked='checked' ";} ?>
					   />
				<label for="photoListe">Photos&nbsp;→</label>
				<input type="checkbox" 
					   name="photoListe" 
					   id="photoListe" 
					   value=1
					   <?php if ($photoListe) {echo " checked='checked' ";} ?>
					   />
			</p>
			<p class="colonne colC20">
				<label for="nbColonneListe">Nombre de colonnes</label>
				<input type="text" 
					   maxlength="2"
					   name="nbColonneListe" 
					   id="nbColonneListe"
					   value="<?php if(isset($colonnes) && $colonnes) {echo $colonnes->num_rows;} else {echo 0;} ?>"
					   size="1"
					   />
			</p>
			<p class="colonne colD15">
				<input type="hidden" id="idListe" name="idListe" value="<?php echo $idListe; ?>" />
				<input type="submit" id="sauveDefinitionListe" name="sauveDefinitionListe" value="Sauvegarder" />
				<input type="submit" 
					   id="supprimeDefinitionListe" 
					   name="supprimeDefinitionListe" 
					   value="Supprimer"
					   title="Supprimer la liste"
					   />
			</p>
		</fieldset>
	</form>
</div>
<div id="laListe" class="div_construit" style="display:block;">
	<?php //<form action="index.php" name="formModifieTableau" method="post"> ?>
		<fieldset id="cadre_laListe">
			<table id="tableauListe">
				<caption>
					<input id="sauveDonneesTableau" 
						   type="submit" 
						   name="sauveDonneesTableau" 
						   value="<?php echo $nomListe; ?>"
						   title="Sauvrgarder le tableau"
						   />
				</caption>
				<tr>
					<th>Nom Prénom</th>
<?php if ($sexeListe) { ?>
					<th>Sexe</th>	
<?php } ?>
<?php if ($classeListe) { ?>
					<th>Classe</th>	
<?php } ?>
<?php if ($photoListe) { ?>
					<th>Photo</th>	
<?php }
if(isset($colonnes) && $colonnes && $colonnes->num_rows) {
	while ($colonne = $colonnes->fetch_object()) {
?>	
					<th onclick="inverse('<?php echo $colonne->id; ?>')">
						<form action="index.php" 
							  name="formModifieTitre" 
							  method="post" 
							  id="formModifieTitre<?php echo $colonne->id; ?>" 
							  style="margin: 0;padding: 0;">
							<span name="enSaisie" 
								  class="invisible" 
								  id="saisie<?php echo $colonne->id; ?>"
								  >
								<input type="text" 
									   name="titre" 
									   value="<?php echo $colonne->titre; ?>"
									   id="entree<?php echo $colonne->id; ?>"
									   onblur="this.form.submit()"
									   />
								<input type="hidden" name="id" value="<?php echo $colonne->id; ?>" />
								<input type="hidden" name="id_def" value="<?php echo $colonne->id_def; ?>" />
								<input type="hidden" name="action" value="sauveTitreColonne" />
							</span>
							<span name="enVision" 
								  id="vision<?php echo $colonne->id; ?>"
								  style="cursor:pointer;"
								  >
								<?php echo $colonne->titre; ?>
							</span>
						</form>
					</th>		
<?php 	
	}
}
?>				
				</tr>
			</table>
		</fieldset>
	<?php //</form> ?>
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
