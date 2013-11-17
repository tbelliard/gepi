<?php

/**
 *
 *
 *
 * Copyright 2001, 2002 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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

/*/ table de données sur le module de gestion des groupes EdT
edt_gr_nom (id, nom, nom_long, subdivision_type, subdivision)
	- id entier autoincrémenté
	- nom et nom_long permettent de préciser deux niveaux (deux longueurs de noms)
	- subdivision_type peut être de trois types : classe, demi ou autre
	- subdivision précise l'id de la classe dans les cas où subdivision_type = classe ou demi. Sinon 'plusieurs'

edt_gr_eleves (id, id_gr_nom, id_eleve)
	- id entier autoincrémenté
	- id_gr_nom renvoie à l'id de la table edt_gr_nom
	- id_eleve renvoie à l'id_eleve de la table eleves

edt_gr_profs (id, id_gr_nom, id_utilisateurs)
	- id entier autoincrémenté
	- id_gr_nom renvoie à l'id de la table edt_gr_nom
	- id_utilisateurs renvoie au login de la table utilisateurs (professeurs ou 'autre' uniquement).

edt_gr_classes (id, id_gr_nom, id_classe)
	- id entier autoincrémenté
	- id_gr_nom renvoie à l'id de la table edt_gr_nom
	- id_classe renvoie l'id de la table classes.
*/

// ========== Initialisation =============

$titre_page = "Gérer les groupes de l'EdT";
$affiche_connexion = 'yes';
$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
   header("Location:../utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

// Sécurité
if (!checkAccess()) {
    header("Location: ./logout.php?auto=2");
    die();
}

// ===================== fin de l'initialisation ================

// ============================== VARIABLES ===============================

$aff_liste_gr = $msg_gr = $msg_gr_del = $style_nom_gr = $aff_select_classes = $aff_select_profs = NULL;
$auto = 'oui';
$style_fieldset = ' style="display: none;"';
$action = isset($_POST["action"]) ? $_POST["action"] : (isset($_GET["action"]) ? $_GET["action"] : NULL);
$nom_gr = isset($_POST["nom_gr"]) ? $_POST["nom_gr"] : NULL;
$nom_long_gr = isset($_POST["nom_long_gr"]) ? $_POST["nom_long_gr"] : NULL;
$type = isset($_POST["type"]) ? $_POST["type"] : NULL;
$choix_classe = isset($_POST["choix_classe"]) ? $_POST["choix_classe"] : NULL;
$id_gr = isset($_GET["id_gr"]) ? $_GET["id_gr"] : NULL;
$prof = isset($_POST["prof"]) ? $_POST["prof"] : NULL;
$choix_prof = isset($_POST["choix_prof"]) ? $_POST["choix_prof"] : NULL;
//$ = isset($_POST[""]) ? $_POST[""] : NULL;

// ============================= fin des VARIABLES ========================

// L'action "ajouter"
if ($action == "ajouter_gr") {
	// On vérifie si le nom est bien saisie
	if ($nom_gr == '') {
		$msg_gr .= '<span class="red">Il faut renseigner le nom !</span>';
		$style_nom_gr = 'style="background-color: orange; border: 1px solid red;"';
		$style_fieldset = ' style="display: block;"';
		$auto = 'non';

	}else{
		// On vérifie aussi que le nom n'existe pas déjà
		$verif = mysqli_fetch_array(mysqli_query($GLOBALS["mysqli"], "SELECT id FROM edt_gr_nom WHERE nom = '".$nom_gr."'"));
		if ($verif >= 1) {
			// Ce nom existe déjà, il ne faut pas de doublon ;)
			$msg_gr = '<p>Ce nom existe déjà, veuillez le modifier avant de sauvegarder.</p>';
			$style_nom_gr = 'style="background-color: orange; border: 1px solid red;"';
			$style_fieldset = ' style="display: block;"';
			$auto = 'non';
		}

		// On vérifie aussi que le champ subdivision corresponde au type de groupe
		if ($type == "autre") { $choix_classe = "plusieurs";}

		if ($type == "classe" OR $type == "demi") {
			// Dans ce cas, il faut avoir choisi une classe

			if (!is_numeric($choix_classe)) {
				$msg_gr .= '<span class="red">Il faut renseigner la classe !</span>';
				$style_nom_gr = 'style="background-color: orange; border: 1px solid red;"';
				$style_fieldset = ' style="display: block;"';
				$auto = 'non';
			}
		}

		// On enregistre dans la base si c'est bon
		if ($auto == 'oui') {
			$sql_e = "INSERT INTO edt_gr_nom (id, nom, nom_long, subdivision_type, subdivision)
									VALUES ('', '".$nom_gr."', '".$nom_long_gr."', '".$type."', '".$choix_classe."')";
			$query_e = mysqli_query($GLOBALS["mysqli"], $sql_e) OR trigger_error('Impossible d\'enregistrer dans la base '.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), E_USER_WARNING);
			//$id_gr_nom = mysql_insert_id($query_e);
			// Avec une connexion permanente à la base, impossible de récupérer l'id
			$select_id = mysqli_query($GLOBALS["mysqli"], "SELECT id FROM edt_gr_nom WHERE nom = '".$nom_gr."' LIMIT 1");
			$id_gr_nom = mysql_result($select_id, 0,"id");

			if ($choix_prof != NULL AND $choix_prof != 'plusieurs' AND $prof != 'plusieurs') {
				// On ajoute aussi une ligne pour le/les professeurs de ce edt_gr
				$sql_p = "INSERT INTO edt_gr_profs (id, id_gr_nom, id_utilisateurs)
									VALUES ('', '".$id_gr_nom."', '".$choix_prof."')";
				//$query_p = mysql_query($sql_p) OR trigger_error("Impossible d'enregistrer le nom du professeur.".$sql_p, E_USER_NOTICE);
				$query_p = mysqli_query($GLOBALS["mysqli"], $sql_p) OR DIE('ERREUR'.$sql_p);

			}
		}
	}
}elseif($action == "effacer_gr"){

	if (is_numeric($id_gr)) {
		// On peut alors effacer ce groupe EdT
		$query_del = mysqli_query($GLOBALS["mysqli"], "DELETE FROM edt_gr_nom WHERE id = '".$id_gr."' LIMIT 1") OR trigger_error('Erreur lors de la suppression ', E_USER_NOTICE);

	}else{

		$msg_gr_del = '<p>Impossible d\'effacer ce groupe car son identifiant n\'existe pas.</p>';

	}
}

// On affiche la liste des groupes qui existent
$sql_g = "SELECT * FROM edt_gr_nom ORDER BY nom";
$query_g = mysqli_query($GLOBALS["mysqli"], $sql_g) OR trigger_error('Impossible de lire les tables de la BDD : '.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), E_USER_ERROR);

// on recherche la liste des classes
//	$query = mysql_query("SELECT id, classe FROM classes ORDER BY classe");
//	$nbre = mysql_num_rows($query);


$a = 0;
while($gr = mysqli_fetch_array($query_g)){

	// On formate l'affichage des groupes de l'EdT

	$gr_nom_long = (isset($gr["nom_long"]) AND $gr["nom_long"] != '') ? $gr["nom_long"] : '-';
	$aff_liste_gr .= '
		<tr>
			<td style="cursor: pointer;" id="id00_'.$gr["id"].'">
				<p onclick="classeEdtAjax(\'id00_'.$gr["id"].'\', \''.$gr["nom"].'\', \'nom_gr\');" title="Modifier ce nom">'.$gr["nom"].'</p></td>
			<td style="cursor: pointer;" id="id000_'.$gr["id"].'">
				<p onclick="classeEdtAjax(\'id000_'.$gr["id"].'\', \''.$gr_nom_long.'\', \'nom_gr2\');" title="Modifier ce nom long">'.$gr_nom_long.'</p></td>
			<td style="cursor: pointer;" id="id2_'.$gr["id"].'">
				<p onclick="classeEdtAjax(\'id2_'.$gr["id"].'\', \''.$gr["subdivision_type"].'\', \'type\');" title="Modifier le type">'.$gr["subdivision_type"].'</p>
			</td>
			<td style="cursor: pointer;" id="id_'.$gr["id"].'">
	';

		// On vérifie que la subdivision est bien renseignée
		$aff_subdivision = (isset($gr["subdivision"]) AND $gr["subdivision"] != '') ? $gr["subdivision"] : '-+-';

	if ($gr["subdivision_type"] == "demi" OR $gr["subdivision_type"] == "classe") {
		$aff_liste_gr .= '
			<p style="cursor: pointer;" onclick="classeEdtAjax(\'id_'.$gr["id"].'\', \''.$gr["subdivision"].'\', \'modifier\');">
			'.$aff_subdivision.'
			</p>
		';
	}else{

		$aff_liste_gr .= '-';

	}


	$aff_liste_gr .= '
			</td>
			<td style="cursor: pointer;" onclick="ouvrirWin(\''.$gr["id"].'\', \'liste_e\');">Afficher</td>
			<td style="cursor: pointer;" onclick="ouvrirWin(\''.$gr["id"].'\', \'liste_p\');">Afficher</td>
			<td><p><a href="./edt_aff_gr.php?action=effacer_gr&amp;id_gr='.$gr["id"].'"><img src="../images/icons/delete.png" alt="effacer ce groupe" title="Effacer ce groupe" />'.$msg_gr_del.'</a></p></td>
		</tr>
	';
	$a++;
}

// la liste des classes pour la création de nouveaux groupes :

	$query = mysqli_query($GLOBALS["mysqli"], "SELECT id, classe FROM classes ORDER BY id");
	$nbre = mysqli_num_rows($query);

	$aff_select_classes .= '
	<select name="choix_classe">
			<option value="plusieurs">Plusieurs classes</option>
			';

		for($i = 0; $i < $nbre; $i++){
			$classes[$i] = mysql_result($query, $i, "id");
			$nom[$i] = mysql_result($query, $i, "classe");

			$aff_select_classes .= '
			<option value="'.$classes[$i].'">'.$nom[$i].'</option>';
		}
		$aff_select_classes .= '</select>'."\n";

// La liste des professeurs pour la création de nouveaux groupes :

	$query_p = mysqli_query($GLOBALS["mysqli"], "SELECT login, nom, prenom FROM utilisateurs WHERE statut = 'professeur' AND etat = 'actif' ORDER BY nom, prenom")
						OR trigger_error('Impossible de lire la liste des professeurs.', E_USER_ERROR);
	$nbre_p = mysqli_num_rows($query_p);


	$aff_select_profs .= '
	<select name="choix_prof">
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


// ++++++++++++++++++++++ Header +++++++++++++++++++++++++++++++++++++
$style_specifique = "/edt_gestion_gr/style2_edt";
$javascript_specifique = "edt_gestion_gr/script/fonctions_edt2";
$utilisation_win = 'oui';
require_once("../lib/header.inc.php");
echo '
<!-- fin du header -->
';
// +++++++++++++++++++++ fin du header ++++++++++++++++++++++++
?>

<h4><a href="../edt_organisation/index_edt.php"><img src="../images/icons/back.png" alt='Retour' class='back_link' />Retour</a>&nbsp;|&nbsp;
<span class="gepi">Cliquez sur l'&eacute;l&eacute;ment &agrave; modifier.</span></h4>
<br />

<!-- Liste des gr -->
<div id="listeGr">
	<form action="edt_aff_gr.php" method="post">
	<table summary="Liste des groupes">
		<tr>
			<th>Nom</th>
			<th>Nom long</th>
			<th>Type de subdivision</th>
			<th>Classes concern&eacute;es</th>
			<th>Liste des &eacute;l&egrave;ves</th>
			<th>Liste des professeurs</th>
			<th>Supprimer ce groupe</th>
		</tr>
	<?php echo $aff_liste_gr; ?>

	</table>
		<input type="hidden" name="action" value="modifier" />
		<!--<input type="submit" name="modifier" value="Enregistrer les modifications" />-->
	</form>

</div>

<br />

<!-- Ajouter un gr -->
<div id="ajoutGr">
	<p onclick='changerDisplayDiv("ajoutGr2");' class="p">Ajouter un groupe</p>
	<fieldset id="ajoutGr2"<?php echo $style_fieldset; ?>>
		<legend>&nbsp;Nouveau groupe d'&eacute;l&egrave;ves pour l'EdT&nbsp;</legend>

		<form name="ajout" action="edt_aff_gr.php" method="post">
			<input type="hidden" name="action" value="ajouter_gr" />

			<div id="ajoutGr3" style="Display: none;">
				Liste des classes (si le groupe est constitu&eacute; d'&eacute;l&egrave;ves venant d'une seul classe) :
				<br />
				<?php echo $aff_select_classes; ?>

			</div>
			<div id="ajoutGr4" style="Display: none;">
				Liste des professeurs (s'il y a qu'un seul professeur qui effectue ce cours)
				<br />
				<?php echo $aff_select_profs; ?>

			</div>

			<p>
			<label for="nomGr" title="Tel qu'il doit apparaitre dans l'EdT">Nom</label>
			<input type="text" id="nomGr" name="nom_gr" value="<?php echo $nom_gr; ?>"<?php echo $style_nom_gr; ?> />
			</p>
			<p>
			<label for="nomLongGr" title="Si n&eacute;cessaire !">Autre nom</label>
			<input type="text" id="nomLongGr" name="nom_long_gr" value="<?php echo $nom_long_gr; ?>" />
			</p>

			<p>
			<label for="typeI">Plusieurs classes</label>
			<input type="radio" id="typeI" name="type" value="autre" checked="checked" onclick="versHide('ajoutGr3');" />
			</p>
			<p>
			<label for="typeC" title="Ce groupe correspond &agrave; l'&eacute;ffectif complet d'une classe">Classe enti&egrave;re</label>
			<input type="radio" id="typeC" name="type" value="classe" onclick="versShow('ajoutGr3');" />
			</p>
			<p>
			<label for="typeD" title="Ce groupe correspond &agrave; une partie d'une seule classe">Subdivision d'une classe</label>
			<input type="radio" id="typeD" name="type" value="demi" onclick="versShow('ajoutGr3');" />
			</p>

			<p>
			<label for="unprof">Un seul professeur</label>
			<input type="radio" id="unprof" name="prof" value="unseul" onclick="versShow('ajoutGr4');" />
			</p>
			<p>
			<label for="profs">Plusieurs professeurs</label>
			<input type="radio" id="profs" name="prof" value="plusieurs" onclick="versHide('ajoutGr4');" />
			</p>

			<input type="submit" name="enregistrer" value="Ajouter ce groupe EdT" />
			<?php echo $msg_gr; ?>

		</form>
	</fieldset>
</div>

<?php
require_once("../lib/footer.inc.php");
?>