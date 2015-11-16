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
//verifieTableCree();

$idListe = NULL;
$nomListe = NULL;
$sexeListe = NULL;
$classeListe = NULL;
$nbColonneListe = NULL;
$photoListe = NULL;

// $id_groupe = isset($_SESSION['liste_perso']['id_groupe']) ? $_SESSION['liste_perso']['id_groupe'] : NULL;
// $id_aid = isset($_SESSION['liste_perso']['id_aid']) ? $_SESSION['liste_perso']['id_aid'] : NULL;
$id_groupe = NULL;
$id_aid = NULL;

//$colonnes = array();

//==============================================
// Action demandée
//==============================================
$tableauChoisi = filter_input(INPUT_POST, 'tableauChoisi') ? filter_input(INPUT_POST, 'tableauChoisi') : NULL;
$nouvelleListe = filter_input(INPUT_POST, 'nouvelleListe') === 'Nouvelle liste' ? TRUE : NULL;
$sauveDefinitionListe = filter_input(INPUT_POST, 'sauveDefinitionListe') === 'Sauvegarder' ? TRUE : FALSE;
$supprimerDefinitionListe = filter_input(INPUT_POST, 'sauveDefinitionListe') === 'Supprimer' ? TRUE : FALSE;
$sauveTitreColonne = filter_input(INPUT_POST, 'action') === 'sauveTitreColonne' ? TRUE : FALSE;
$chargeEleves = filter_input(INPUT_POST, 'action') === 'choixEleves' ? TRUE : FALSE;
$sauveModifieCaseColonne = filter_input(INPUT_POST, 'action') === 'sauveModifieCaseColonne' ? TRUE : FALSE;
//Je n'ai pas trouvé d'équivalent à filter_input pour un tableau :( Régis 
$idElevesChoisis = isset($_POST['elevesChoisis']) && count($_POST['elevesChoisis']) ? $_POST['elevesChoisis'] : NULL;
$supprimeEleve = filter_input(INPUT_POST, 'eleveASupprimer') ? filter_input(INPUT_POST, 'eleveASupprimer') : NULL;
$supprimeColonne = filter_input(INPUT_POST, 'supprimeColonne') === 'Supprimer' ? TRUE : FALSE;
$reculeColonne = filter_input(INPUT_POST, 'deplaceColonne') === '-1' ? TRUE : FALSE;
$avanceColonne = filter_input(INPUT_POST, 'deplaceColonne') === '1' ? TRUE : FALSE;

if ($nouvelleListe) { //===== Nouvelle liste =====
	$idListe = "";
	unset($_SESSION['liste_perso']);
	sauveDefListe($idListe,"", "n", "n", "n", 0);
	$idListe = Dernier_id();
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
	
} elseif ($chargeEleves) { //===== On charge les élèves =====
	$eleve_col = new PropelCollection();
	$id_groupe = (int)filter_input(INPUT_POST, 'id_groupe') != -1 ? filter_input(INPUT_POST, 'id_groupe') : NULL;
	if ($id_groupe !==NULL ) {
		$id_aid = NULL;
		
		//TODO : à mettre dans une fonction
		if ($utilisateur->getStatut() == "professeur") {
			$current_groupe = GroupeQuery::create()->filterByUtilisateurProfessionnel($utilisateur)->findPk($id_groupe);
		} else {
			$current_groupe = GroupeQuery::create()->findPk($id_groupe);
		}
		
		$query = EleveQuery::create();
		$query->useJEleveGroupeQuery()->filterByGroupe($current_groupe)->endUse();
		$query->where('Eleve.DateSortie is NULL')
            ->orderBy('Eleve.Nom','asc')
            ->orderBy('Eleve.Prenom','asc')
            ->distinct();
		$eleve_col = $query->find();
		
	} else {
		//TODO : à mettre dans une fonction
		$id_aid = (int)filter_input(INPUT_POST, 'id_aid') != -1 ? filter_input(INPUT_POST, 'id_aid') : NULL;
		if ($id_aid !==NULL ) {
			$current_aid = AidDetailsQuery::create()->findPk($id_aid);
			
			$query = EleveQuery::create();
			$query->useJAidElevesQuery()
			   ->filterByIdAid($current_aid->getId())
			   ->endUse()
			   ->where('Eleve.DateSortie is NULL')
			   ->orderBy('Eleve.Nom','asc')
			   ->orderBy('Eleve.Prenom','asc')
			   ->distinct();
			$eleve_col = $query->find();
		} else {
			if(count($idElevesChoisis)) {
				// TODO : enregistrer les élèves et mettre tout ça dans des fonctions
				EnregistreElevesChoisis($idElevesChoisis, $_SESSION['liste_perso']['id']);
			}
		}
	}
	$idListe = isset($_SESSION['liste_perso']['id']) ? $_SESSION['liste_perso']['id'] : NULL;
} 
elseif ($supprimeEleve) {
	SupprimeEleve($supprimeEleve, $_SESSION['liste_perso']['id']);
	$idListe = isset($_SESSION['liste_perso']['id']) ? $_SESSION['liste_perso']['id'] : NULL;
} 
elseif ($sauveModifieCaseColonne) {
	$login = filter_input(INPUT_POST, 'login');
	$idDef= filter_input(INPUT_POST, 'id_def');
	$idColonne = filter_input(INPUT_POST, 'id_col');
	$contenu = filter_input(INPUT_POST, 'contenu');
	$id = filter_input(INPUT_POST, 'id');
	ModifieCaseColonneEleve($login, $idDef, $idColonne, $contenu, $id);
	$idListe = isset($_SESSION['liste_perso']['id']) ? $_SESSION['liste_perso']['id'] : NULL;
}
elseif ($supprimeColonne) { //===== On supprime une colonne
	$idCol = filter_input(INPUT_POST, 'colonneASupprime') ? filter_input(INPUT_POST, 'colonneASupprime') : NULL;
	$liste = filter_input(INPUT_POST, 'idListe') ? filter_input(INPUT_POST, 'idListe') : NULL;
	if ($idCol !== NULL) {
		SupprimeColonne($liste, $idCol);
	}
	$idListe = isset($_SESSION['liste_perso']['id']) ? $_SESSION['liste_perso']['id'] : NULL;
}
elseif ($avanceColonne) {
	$idColonneABouge = filter_input(INPUT_POST, 'colonneABouge');
	$idListe = filter_input(INPUT_POST, 'idListe');
	AvanceColonne($idColonneABouge, $idListe);
}
elseif ($reculeColonne) {
	$idColonneABouge = filter_input(INPUT_POST, 'colonneABouge');
	$idListe = filter_input(INPUT_POST, 'idListe');
	ReculeColonne($idColonneABouge, $idListe);
}
else { //===== Sinon on vérifie s'il y a une liste en mémoire
	$idListe = isset($_SESSION['liste_perso']['id']) ? $_SESSION['liste_perso']['id'] : '';
}

//==============================================
//Charge tableau
//==============================================
chargeListe($idListe);
$eleve_choisi_col = ChargeEleves($idListe);
$donneesTableau = ChargeColonnesEleves($idListe, $eleve_choisi_col);

$groupe_col = $utilisateur->getGroupes();
$aid_col = $utilisateur->getAidDetailss();

$idListe = $_SESSION['liste_perso']['id'] ;
$nomListe = $_SESSION['liste_perso']['nom'] ;
$sexeListe = $_SESSION['liste_perso']['sexe'] ;
$classeListe = $_SESSION['liste_perso']['classe'] ;
$nbColonneListe = $_SESSION['liste_perso']['nbColonne'] ;
$photoListe = $_SESSION['liste_perso']['photo'] ;
$colonnes = $_SESSION['liste_perso']['colonnes'] ;
$colonnes2 = $_SESSION['liste_perso']['colonnes'] ;

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
<div class="noprint">
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
    <li class="menu_liste"
		id='menu_lien_affichage'
		onclick=" masque('construction'); desactiver('construction');
			masque('tableau'); desactiver('tableau');
			masque('eleves');desactiver('eleves');
			affiche('affichage'); activer ('affichage'); "
		>
		<a href="#lien_affichage">Modifier</a>
	</li>
	</li>
    <li class="menu_liste"
		id='menu_lien_aide'
		onclick="afficheAide(); return false;"
		title="Ouvre un popup"
		>
		<span>Aide</span>
	</li>
</ul>
<!-- Choix de la liste ou création d'une liste -->
<div id="tableau" class="div_construit">
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
if ($tableau->num_rows) {
	while ($obj = $tableau->fetch_object()) { ?>
				<option value="<?php echo $obj->id; ?>"
						<?php if ($obj->id === $idListe) { echo " selected='selected' "; } ?>
						><?php echo $obj->nom; ?></option>
<?php }
 }?>		
			</select>
			<input type="submit" id="sauveChoixTableau" name="sauveChoixTableau" value="Afficher" />
			<input type="submit" id="nouvelleListe" name="nouvelleListe" value="Nouvelle liste" />
		</fieldset>
	</form>
</div>
<script type="text/javascript" >
	document.getElementById('sauveChoixTableau').classList.add('invisible');
</script>
<!-- Ajout des élèves à la liste -->
<div id="eleves" class="div_construit">
	<p><a id='lien_eleves'></a></p>
	<form action="index.php" name="formAjouteEleve" method="post">
		<fieldset class="center">
			<legend>Ajouter des membres à la liste</legend>
			<span <?php if ($idListe === NULL ||$idListe === "" ) { echo "class='invisible' ";} ?>>
			<p>
				<input type="hidden" name="action" value="choixEleves"/>
				<label for="id_groupe">Groupe : </label>
				<select id="id_groupe" name="id_groupe" class="small"<?php
					if(($_SESSION['statut']=='professeur')&&(!getSettingAOui('abs2_saisie_prof_decale'))&&(!getSettingAOui('abs2_saisie_prof_decale_journee'))) {
						echo " onchange=\"document.forms['form_choix_groupe'].submit();\"";
					}
				?>>
					<option value='-1'>choisissez un groupe</option>
<?php
foreach ($groupe_col as $group) {
?>
					<option value='<?php echo $group->getId(); ?>'>
						<?php echo $group->getNameAvecClasses(); ?>
					</option>
<?php } ?>
				</select>
				
<?php if (isset ($aid_col) && !$aid_col->isEmpty()) { ?>
				<label for="id_aid">AID : </label>
				<select id="id_aid" name="id_aid" class="small">
					<option value='-1'>choisissez une aid</option>
<?php foreach ($aid_col as $aid) { ?>
					<option value='<?php echo $aid->getPrimaryKey(); ?>'>
						<?php echo $aid->getNom(); ?>
					</option>
<?php } ?>
				</select>
<?php } ?>
				<button type="submit">Valider</button>
				
			</p>
<?php if (isset ($eleve_col) && !$eleve_col->isEmpty()) { ?>
			<p>
				<input type="hidden" name="lastIdGroupe" value="<?php echo $id_groupe; ?>" />
				<input type="hidden" name="lastIdAID" value="<?php echo $id_aid; ?>" />
				<select multiple="multiple" id="elevesChoisis" name="elevesChoisis[]" size="5">
					<option value='-1'>choisissez un ou des élèves</option>
<?php foreach ($eleve_col as $eleve) { ?>
					<option value='<?php echo $eleve->getLogin(); ?>'>
						<?php echo $eleve->getNom(); ?> <?php echo $eleve->getPrenom(); ?>
					</option>
<?php } ?>		
					
				</select>
			</p>
<?php } ?>
			</span>
		</fieldset>
	</form>
</div>
<!-- Ajouter des colonnes -->
<div id="construction" class="div_construit">
	<p><a id='lien_construction'></a></p>
	<fieldset>
		<legend>Ajouter des colonnes / supprimer la liste</legend>
		<form action="index.php" 
			  name="formAjouteColonne" 
			  method="post"
			  style="display:table; width:100%;"
			  >
			<p class=" colG65" <?php if ($idListe === NULL ||$idListe === "" ) { echo "style='display:none;' ";} ?>>
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
			<p class=" colC20" <?php if ($idListe === NULL ||$idListe === "" ) { echo "style='display:none;' ";} ?>>
				<label for="nbColonneListe">Nombre de colonnes</label>
				<input type="text" 
					   maxlength="2"
					   name="nbColonneListe" 
					   id="nbColonneListe"
					   value="<?php if(isset($colonnes) && $colonnes) {echo $colonnes->num_rows;} else {echo 0;} ?>"
					   size="1"
					   />
			</p>
			<p class=" colD15" <?php if ($idListe === NULL ||$idListe === "" ) { echo "style='display:none;' ";} ?>>
				<input type="hidden" id="idListe" name="idListe" value="<?php echo $idListe; ?>" />
				<input type="submit" id="sauveDefinitionListe" name="sauveDefinitionListe" value="Sauvegarder" />
				<input type="submit" 
					   id="supprimeDefinitionListe" 
					   name="supprimeDefinitionListe" 
					   value="Supprimer"
					   title="Supprimer la liste"
					   style="margin-top: 1em;"
					   />
			</p>
		</form>
	</fieldset>
</div>
<!-- Supprimer des colonnes -->
<div id="affichage" class="div_construit">
	<p><a id='lien_affichage'></a></p>
	<fieldset class="center" style="display:inline;">
		<legend>Supprimer des colonnes</legend>
		<span <?php if ($idListe === NULL ||$idListe === "" ) { echo "class='invisible' ";} ?>>
		<form action="index.php" name="formSupprimeColonne" method="post">
			<p>
				<select id="colonneASupprime" name="colonneASupprime">
					<option value='-1'>choisissez la colonne à supprimer</option>		
<?php
if(isset($colonnes) && $colonnes && $colonnes->num_rows) {
	while ($col = $colonnes->fetch_object()) {
?>
					<option <?php if(!$col->titre) {echo " style='color:red;' "; } ?> value="<?php echo $col->id; ?>"><?php if($col->titre) {echo $col->titre;} else {echo "Colonne non nommée";} ?></option>
<?php
	}
	$colonnes->data_seek(0);
}
?>
				</select>
			</p>
			<p>
				<input type="hidden" id="idListe" name="idListe" value="<?php echo $idListe; ?>" />
				<input type="submit" id="supprimeColonne" name="supprimeColonne" value="Supprimer" />
			</p>
		</form>
			</span>
	</fieldset>
	<fieldset class="center" style="display:inline;">
		<legend>Déplacer une colonne</legend>
		<span <?php if ($idListe === NULL ||$idListe === "" ) { echo "class='invisible' ";} ?>>
		<form action="index.php" name="formBougeColonne" method="post">
			<p>
				<select id="colonneABouge" name="colonneABouge">
					<option value='-1'>choisissez la colonne à déplacer</option>		
<?php
if(isset($colonnes) && $colonnes && $colonnes->num_rows) {
	while ($col = $colonnes->fetch_object()) {
?>
					<option <?php if(!$col->titre) {echo " style='color:red;' "; } ?> value="<?php echo $col->id; ?>"><?php if($col->titre) {echo $col->titre;} else {echo "Colonne non nommée";} ?></option>
<?php
	}
	$colonnes->data_seek(0);
}
?>
				</select>
			</p>
			<p>
				<input type="hidden" id="idListe" name="idListe" value="<?php echo $idListe; ?>" />
				<button type="submit" id="reculeColonne" name="deplaceColonne" value="-1">← reculer d'une colonne ←</button>
				<button type="submit" id="avanceColonne" name="deplaceColonne" value="1">→ avancer d'une colonne →</button>
			</p>
		</form>
		</span>
	</fieldset>
</div>

<!-- Aide -->

<div id="aide" class="div_aide">
	<p><strong>Listes perso</strong> → choisir une liste ou en créer une nouvelle.</p>
	<p><strong>Construction</strong> → donner un nom à la liste, choisir les colonnes prédéfinies, déterminer le nombre de colonnes libres puis en ajouter au besoin. Il est aussi possible de supprimer définitivement la liste</p>
	<p><strong>Élèves</strong> → ajouter des élèves en les choisissant dans ses listes.</p>
	<p><strong>Modifier</strong> → supprimer les colonnes ou modifier leurs places.</p>
	<p>Cliquez dans les entêtes de colonnes pour créer leur titre ou le modifier.</p>
	<p>Cliquez dans les cellules pour en modifier le contenu.</p>
	<p>Cliquez en dehors pour enregistrer.</p>
	<p>
		Les points rouges <img src='../images/bulle_rouge.png' alt='image supprime' />
		permettent de supprimer une ligne ou le contenu d'une cellule.
	</p>
</div>
</div>
<div id="laListe" class="div_tableauListe">
	<fieldset id="cadre_laListe">
		<table id="tableauListe">
			<caption>
				<?php echo $nomListe; ?>
			</caption>
			<thead>
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
				<th onclick="inverse('<?php echo $colonne->id; ?>')"
					title="Cliquer sur le titre de la colonne pour le modifier">
					<form action="index.php" 
						  name="formModifieTitre" 
						  method="post" 
						  id="formModifieTitre<?php echo $colonne->id; ?>" 
						  style="margin: 0;padding: 0; display: inline;">
						<span class="invisible" 
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
							<input type="hidden" 
								   name="action" 
								   id="sauveSupprimeCol<?php echo $colonne->id; ?>" 
								   value="sauveTitreColonne" />
							
						</span>
						<span id="vision<?php echo $colonne->id; ?>"
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
				
			</thead>
			<tbody>
<?php if (isset($eleve_choisi_col) && $eleve_choisi_col) {
	 foreach ($eleve_choisi_col as $elv_choisi) { ?>
			<tr>
				<td>
					<form action="index.php" 
						  name="formSupprimeEleve" 
						  method="post" 
						  id="formSupprimeEleve<?php echo $elv_choisi->getLogin(); ?>" 
						  style="margin: 0;padding: 0;padding-left: .5em;">
						<img src="../images/bulle_rouge.png" 
							 class="imageSupprime"
							 onclick="supprime('<?php echo $elv_choisi->getLogin(); ?>', '<?php echo $elv_choisi->getNom(); ?>', '<?php echo $elv_choisi->getPrenom(); ?>'); return(false)" 
							 style="cursor:pointer;"
							 title="supprimer <?php echo $elv_choisi->getNom(); ?> <?php echo $elv_choisi->getPrenom(); ?>"
							 alt="Supprimer" />
					<?php echo $elv_choisi->getNom(); ?> <?php echo $elv_choisi->getPrenom(); ?>
						<input type="submit" name="supprimeEleve" id="supprime_<?php echo $elv_choisi->getLogin(); ?>" value="supprimer" />
						<input type="hidden" name="eleveASupprimer" value="<?php echo $elv_choisi->getLogin(); ?>" />
<script type="text/javascript" >
	masque('supprime_<?php echo $elv_choisi->getLogin(); ?>');
</script>		
					</form>
				</td>				
<?php	if ($sexeListe) { ?>
				<td class="center"><?php echo $elv_choisi->getSexe(); ?></td>	
<?php	} ?>
				
<?php	if ($classeListe) { ?>
				<td class="center"><?php echo $elv_choisi->getClasse()->getNom(); ?></td>	
<?php	} ?>
				
<?php	if ($photoListe) { ?>
				<td class="center">
<?php		if ($elv_choisi->getElenoet()) { ?>
					<img src="../photos/eleves/<?php echo $elv_choisi->getElenoet(); ?>.jpg" style="width: 64px;" alt=""/>
<?php		} ?>
				</td>		
<?php	}
		if(isset($colonnes) && $colonnes && $colonnes->num_rows) {
			$i=1;
			foreach ($colonnes as $colonne) {
				$contenuCase = '';
				$idCase = '';
				if(isset($donneesTableau[$elv_choisi->getLogin()][$colonne['id']]['contenu'])) {
					$contenuCase = $donneesTableau[$elv_choisi->getLogin()][$colonne['id']]['contenu'];
				}
				if(isset($donneesTableau[$elv_choisi->getLogin()][$colonne['id']]['id'])) {
					$idCase = $donneesTableau[$elv_choisi->getLogin()][$colonne['id']]['id'];
				}
				?>
				<td  onclick="inverse('<?php echo $elv_choisi->getLogin(); ?>_<?php echo $colonne['id_def'] ; ?>_<?php echo $colonne['id']; ?>'); " >
					<form action="index.php" 
						  name="formModifieColonneEleve" 
						  method="post" 
						  id="formModifieColonneEleve<?php echo $elv_choisi->getLogin(); ?>_<?php echo $colonne['id_def'] ; ?>_<?php echo $colonne['id']; ?>"
						  style="margin: 0;padding: 0;padding-left: .5em;">
						<span class="invisible" 
							  id="saisie<?php echo $elv_choisi->getLogin(); ?>_<?php echo $colonne['id_def'] ; ?>_<?php echo $colonne['id']; ?>"
							  >
							<img src="../images/bulle_rouge.png"
								 class="imageSupprime"
								 onclick="supprimeContenu('<?php echo $idCase; ?>'); return(false)" 
								 style="cursor:pointer;"
								 title="supprimer "
								 alt="Supprimer"
								  />
							<input type="text" 
								   name="contenu" 
								   value="<?php echo $contenuCase; ?>"
								   id="entree<?php echo $elv_choisi->getLogin(); ?>_<?php echo $colonne['id_def'] ; ?>_<?php echo $colonne['id']; ?>"
								   onblur="this.form.submit()"
								   />
							<input type="hidden" name="id" value="<?php echo $idCase; ?>" />
							<input type="hidden" name="id_col" value="<?php echo $colonne['id']; ?>" />
							<input type="hidden" name="id_def" value="<?php echo $colonne['id_def']; ?>" />
							<input type="hidden" name="login" value="<?php echo $elv_choisi->getLogin(); ?>" />
							<input type="hidden" name="action" value="sauveModifieCaseColonne" />
						</span>
							<!-- <?php echo $elv_choisi->getLogin(); ?>_<?php echo $colonne['id_def'] ; ?>_<?php echo $colonne['id']; ?> -->
						<span id="vision<?php echo $elv_choisi->getLogin(); ?>_<?php echo $colonne['id_def'] ; ?>_<?php echo $colonne['id']; ?>"
							  style="cursor:pointer;"
							  >
							<?php echo $contenuCase; ?>
						</span>
					</form>
						
<script type="text/javascript" >
	//masque('supprime_<?php echo $elv_choisi->getLogin(); ?>_<?php echo $colonne['id_def'] ; ?>_<?php echo $colonne['id']; ?>');
</script>		
				</td>
<?php			$i++;
			}	
		} ?>	
			</tr>
<?php }
} ?>
			</tbody>
		</table>
		<?php if (isset($eleve_choisi_col))  {echo '<p>'.$eleve_choisi_col->count().' élèves</p>';} else {echo '<p>0 élève choisi</p>';} ?> 
	</fieldset>
</div>

<script type="text/javascript" >
	afficher_cacher("aide");
	afficher_cacher("eleves");
	afficher_cacher("construction");
	afficher_cacher("affichage");
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
if ($chargeEleves) {
?>
<script type="text/javascript" >
	desactiver("tableau");
	activer("eleves");
</script>
<?php
}
if ($sauveDefinitionListe) {
?>
<script type="text/javascript" >
	desactiver("tableau");
	activer("construction");
</script>
<?php
}
if ($supprimeColonne) {
?>
<script type="text/javascript" >
	desactiver("tableau");
	activer("affichage");
</script>	
<?php
}


require_once("../lib/footer.inc.php");
