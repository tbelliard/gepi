<?php
/*
 * $Id$
 *
 * Copyright 2001-2017 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//debug_var();

$matiere=isset($_POST['matiere']) ? $_POST['matiere'] : (isset($_GET['matiere']) ? $_GET['matiere'] : NULL);

$msg = '';
if (isset($_POST['modifier_associations'])) {
	check_token();

	$nb_modif=0;
	$matiere_groupe=isset($_POST["matiere_groupe"]) ? $_POST["matiere_groupe"] : array();
	foreach($matiere_groupe as $id_groupe => $current_matiere) {
		$current_group=get_group($id_groupe);

		if($current_group["matiere"]["matiere"]!=$current_matiere) {
			$reg_nom_groupe=$current_group["name"];
			$reg_nom_complet=$current_group["description"];
			$reg_matiere=$current_matiere;
			$reg_clazz=$current_group["classes"]["list"];
			$reg_professeurs=$current_group["profs"]["list"];
			$code_modalite_elect_eleves=$current_group["modalites"];

			foreach ($current_group["periodes"] as $period) {
				$reg_eleves[$period["num_periode"]] = $current_group["eleves"][$period["num_periode"]]["list"];
			}

			$tab_profs_matiere=array();
			$sql="SELECT DISTINCT id_professeur FROM j_professeurs_matieres WHERE id_matiere='$current_matiere';";
			$res_prof_matiere=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_prof_matiere)>0){
				while($lig_prof_matiere=mysqli_fetch_object($res_prof_matiere)){
					$tab_profs_matiere[]=$lig_prof_matiere->id_professeur;
				}
			}

			// On vérifie que les profs de la liste sont bien associés à la matière:
			for($loo=0;$loo<count($reg_professeurs);$loo++) {
				if(!in_array($reg_professeurs[$loo], $tab_profs_matiere)) {
					$sql="SELECT MAX(ordre_matieres) AS max_ordre_matiere FROM j_professeurs_matieres WHERE id_professeur='".$reg_professeurs[$loo]."';";
					//echo "$sql<br />";
					$res_ordre=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_ordre)==0) {
						$ordre_matiere=1;
					}
					else {
						$ordre_matiere=old_mysql_result($res_ordre, 0, "max_ordre_matiere")+1;
					}

					$sql="INSERT INTO j_professeurs_matieres SET id_professeur='".$reg_professeurs[$loo]."', id_matiere='".$current_matiere."', ordre_matieres='$ordre_matiere';";
					//echo "$sql<br />";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
				}
			}

			$create = update_group($id_groupe, $reg_nom_groupe, $reg_nom_complet, $reg_matiere, $reg_clazz, $reg_professeurs, $reg_eleves, $code_modalite_elect_eleves);
			if (!$create) {
				$msg .= "Erreur lors de la mise à jour du groupe ".get_info_grp($id_groupe).".<br />";
			}
			else {
				$nb_modif++;
			}
		}
	}
	$msg.=$nb_modif." modification(s) effectuée(s) (".strftime("%d/%m/%Y à %H:%M:%S").").<br />";
}

$javascript_specifique[] = "lib/tablekit";
$utilisation_tablekit="ok";

$themessage = 'Des modifications ont été effectuées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Associations matières/enseignements";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

echo "<form action='".$_SERVER['PHP_SELF']."' name='form0' method='post'>";
if(!isset($matiere)) {
	echo "
	<p class='bold'><a href=\"index.php\" ".insert_confirm_abandon()."><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
}
else {
	echo "
	<p class='bold'><a href=\"modify_matiere.php?current_matiere=".$matiere."\" ".insert_confirm_abandon()."><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
}

$chaine_options_matieres="";
$sql="SELECT * FROM matieres ORDER BY matiere;";
$res_mat=mysqli_query($GLOBALS["mysqli"], $sql);
while($lig_mat=mysqli_fetch_object($res_mat)) {
	$selected='';
	if((isset($matiere))&&($lig_mat->matiere==$matiere)) {
		$selected=" selected='true'";
	}
	$chaine_options_matieres.="
				<option value=\"".$lig_mat->matiere."\"".$selected.">".$lig_mat->matiere." (".$lig_mat->nom_complet.")</option>";
}

echo "
		 | 
		<select name='matiere' onchange=\"document.forms[0].submit();\">
			".$chaine_options_matieres."
		</select>
	</p>
</form>";
?>
<p>La présente page permet de faire la liste des enseignements associés à telle matière,<br />
et de modifier les associations.</p>
<?php

	if(!isset($matiere)) {

		echo "<p>Choisissez une matière parmi les matières associées à des enseignements.</p>";

		$sql="SELECT DISTINCT m.*, COUNT(jgm.id_groupe) AS nb_grp FROM matieres m, j_groupes_matieres jgm WHERE jgm.id_matiere=m.matiere GROUP BY m.matiere HAVING COUNT(jgm.id_groupe)>0 ORDER BY m.matiere, m.nom_complet;";
		$call_data = mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($call_data)==0) {
			echo "<p style='color:red'>Aucune matière n'a été trouvée.</p>";
			require("../lib/footer.inc.php");
			die();
		}

		$chaine_bull="";
		if(getSettingAOui("active_bulletins")) {
			$chaine_bull="
			<th class='number'>Nombre d'enseignements associés visibles sur les bulletins</th>";
		}

		echo "
<table class='boireaus boireaus_alt resizable sortable'>
	<thead>
		<tr>
			<th class='text'>Matière</th>
			<th class='text'>Nom complet</th>
			<th class='number'>Nombre d'enseignements associés</th>".$chaine_bull."
		</tr>
	</thead>
	<tbody>";
		while($lig=mysqli_fetch_object($call_data)) {
			$chaine_bull="";
			if(getSettingAOui("active_bulletins")) {
				$sql="SELECT DISTINCT id_groupe FROM j_groupes_matieres jgm WHERE jgm.id_matiere='".$lig->matiere."' AND jgm.id_groupe NOT IN (SELECT id_groupe FROM j_groupes_visibilite WHERE domaine='bulletins' AND visible='n');";
				$test = mysqli_query($GLOBALS["mysqli"], $sql);
				$chaine_bull="
			<td>".mysqli_num_rows($test)."</td>";
			}

			echo "
		<tr>
			<td><a href='".$_SERVER["PHP_SELF"]."?matiere=".$lig->matiere."' title=\"Voir les enseignements associés.\">".$lig->matiere."</a></td>
			<td>".$lig->nom_complet."</td>
			<td>".$lig->nb_grp."</td>".$chaine_bull."
		</tr>";
		}
		echo "
	</tbody>
</table>";


		require("../lib/footer.inc.php");
		die();
	}

	$sql="SELECT nom_complet, priority, categorie_id, code_matiere from matieres WHERE matiere='".$matiere."';";
	$call_data = mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($call_data)==0) {
		echo "<p style='color:red'>La matière proposée ($matiere) n'existe pas.</p>";
		require("../lib/footer.inc.php");
		die();
	}
	$lig_matiere=mysqli_fetch_object($call_data);
	$matiere_nom_complet = $lig_matiere->nom_complet;
	$matiere_priorite = $lig_matiere->priority;
	$matiere_cat_id = $lig_matiere->categorie_id;
	$code_matiere = $lig_matiere->code_matiere;

	echo "<p style='margin-top:1em;'>Liste des enseignements associés à <strong>".$matiere." (".$matiere_nom_complet.")&nbsp;:</strong></p>";

	$sql="SELECT DISTINCT jgm.id_groupe FROM j_groupes_matieres jgm, 
							j_groupes_classes jgc, 
							classes c 
						WHERE jgm.id_matiere='".$matiere."' AND 
							jgm.id_groupe=jgc.id_groupe AND 
							jgc.id_classe=c.id 
						ORDER BY c.classe;";
	//echo "$sql<br />";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p style='color:red'>La matière proposée ($matiere) n'est associée à aucun enseignement.</p>";
		require("../lib/footer.inc.php");
		die();
	}

	$nb_col_visibilite=0;
	if(getSettingAOui("active_bulletins")) {
		$nb_col_visibilite++;
	}
	if(getSettingAOui("active_carnets_notes")) {
		$nb_col_visibilite++;
	}
	if(getSettingAOui("active_cahiers_texte")) {
		$nb_col_visibilite++;
	}

	echo "
<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='modifier_associations' value='y' />
		<input type='hidden' name='matiere' value=\"$matiere\" />
		<table class='boireaus boireaus_alt resizable sortable'>
			<thead>
				<tr>
					<th class='nosort' colspan='4'>Enseignement</th>
					<!--th class='text' rowspan='2'>Classe</th-->
					<th class='number' rowspan='2'>
						Matière associée
				
					</th>".(($nb_col_visibilite>0) ? "
					<th class='nosort' colspan='".$nb_col_visibilite."'>Visibilité</th>" : "")."
				</tr>
				<tr>
					<th class='text'>Nom</th>
					<th class='text'>Description</th>
					<th class='text'>Professeur(s)</th>
					<th class='text'>Classe</th>".((getSettingAOui("active_bulletins")) ? "
					<th class='nosort'>Bulletins</th>" : "")."".((getSettingAOui("active_carnets_notes")) ? "
					<th class='nosort'>C.Notes</th>" : "")."".((getSettingAOui("active_cahiers_texte")) ? "
					<th class='nosort'>CdTextes</th>" : "")."
				</tr>
			</thead>
			<tbody>";
	while($lig=mysqli_fetch_object($res)) {
		$current_group=get_group($lig->id_groupe);

		echo "
				<tr>
					<td><a href='../groupes/edit_group.php?id_groupe=".$lig->id_groupe."' title=\"Éditer l'enseignement.\" target='_blank'>".$current_group["name"]."</a></td>
					<td>".$current_group["description"]."</td>
					<td>".$current_group["profs"]["proflist_string"]."</td>
					<td>".$current_group["classlist_string"]."</td>
					<td>
						<select name='matiere_groupe[".$lig->id_groupe."]'>
							".$chaine_options_matieres."
						</select>
					</td>".((getSettingAOui("active_bulletins")) ? "
					<td>".(((isset($current_group["visibilite"]["bulletins"]))&&($current_group["visibilite"]["bulletins"]=="n")) ? "<img src='../images/disabled.png' class='icone20' alt='Non' title=\"Enseignement non affiché sur les bulletins\" />" : "<img src='../images/enabled.png' class='icone20' alt='Oui' title=\"Enseignement affiché sur les bulletins\" />")."</td>" : "")."".((getSettingAOui("active_carnets_notes")) ? "
					<td>".(((isset($current_group["visibilite"]["cahier_notes"]))&&($current_group["visibilite"]["cahier_notes"]=="n")) ? "<img src='../images/disabled.png' class='icone20' alt='Non' title=\"Enseignement non affiché dans les carnets de notes\" />" : "<img src='../images/enabled.png' class='icone20' alt='Oui' title=\"Enseignement affiché dans les carnets de notes\" />")."</td>" : "")."".((getSettingAOui("active_cahiers_texte")) ? "
					<td>".(((isset($current_group["visibilite"]["cahier_texte"]))&&($current_group["visibilite"]["cahier_texte"]=="n")) ? "<img src='../images/disabled.png' class='icone20' alt='Non' title=\"Enseignement non affiché dans les cahiers de textes\" />" : "<img src='../images/enabled.png' class='icone20' alt='Oui' title=\"Enseignement affiché dans les cahiers de textes\" />")."</td>" : "")."
				</tr>";
	}
	echo "
			</tbody>
		</table>
		<p><input type='submit' value=\"Valider les modifications\" /></p>
	</fieldset>
</form>";

	require("../lib/footer.inc.php");

?>
