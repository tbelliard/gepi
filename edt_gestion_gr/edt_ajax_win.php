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

// ========== Initialisation =============

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
// ===================== VARIABLES ==============================

$id_gr = isset($_GET["id_gr"]) ? $_GET["id_gr"] : NULL;
$classe = isset($_GET["classe"]) ? $_GET["classe"] : NULL;
$action = isset($_GET["action"]) ? $_GET["action"] : NULL;
$id = isset($id_gr) ? mb_substr($id_gr, 3) : NULL;
$id2 = isset($id_gr) ? mb_substr($id_gr, 4) : NULL;
$id3 = isset($id_gr) ? mb_substr($id_gr, 5) : NULL;
$id4 = isset($id_gr) ? mb_substr($id_gr, 6) : NULL;

// ==========================fin des variables ==================


if ($action == 'modifier') {
	// On renvoie un select avec la liste des classes
	// on recherche la liste des classes
	$query = mysqli_query($GLOBALS["mysqli"], "SELECT id, classe FROM classes ORDER BY id");
	$nbre = mysqli_num_rows($query);

	echo '
	<select name="choix_classe">
			<option value="plusieurs">Plusieurs classes</option>
			';

		for($i = 0; $i < $nbre; $i++){
			$classes[$i] = old_mysql_result($query, $i, "id");
			$nom[$i] = old_mysql_result($query, $i, "classe");
			// On détermine le selected si c'est possible
			if ($classes[$i] == $classe) {
				$selected = ' selected="selected"';
			}else{
				$selected = '';
			}

			echo '
			<option value="'.$classes[$i].'"'.$selected.' onclick="classeEdtAjax(\''.$id_gr.'\', \''.$classes[$i].'\', \'enregistrer\');">'.$nom[$i].'</option>';
		}
		echo '</select>'."\n";

}elseif($action == 'enregistrer'){
	// On récupère le nom de la classe et on l'affiche après l'avoir enregistré
	$sql_maj = "UPDATE edt_gr_nom SET subdivision = '".$classe."' WHERE id = '".$id."'";
	$query_maj = mysqli_query($GLOBALS["mysqli"], $sql_maj)
										OR trigger_error('Impossible de modifier la classe', E_USER_ERROR);

	$nom_cl = mysqli_fetch_array(mysqli_query($GLOBALS["mysqli"], "SELECT classe FROM classes WHERE id = '".$classe."'"));

	echo '<p onclick="classeEdtAjax(\'id_'.$id.'\', \''.$classe.'\', \'modifier\');">'.$nom_cl["classe"].'&nbsp;<span style="color: green; font-size:0.5em;">(ok !)</span></p>';

}elseif($action == 'type'){

	echo '
		<select name="">
			<option value="rien">Type de subdivision</option>
			<option value="autre" onclick="classeEdtAjax(\''.$id_gr.'\', \'autre\', \'type_en\');">Plusieurs classes</option>
			<option value="classe" onclick="classeEdtAjax(\''.$id_gr.'\', \'classe\', \'type_en\');">Une classe (ent.)</option>
			<option value="demi" onclick="classeEdtAjax(\''.$id_gr.'\', \'demi\', \'type_en\');">Subdivision d\'une classe</option>
		</select>';

}elseif($action == 'type_en'){

	$type = $classe; // car on garde la variable du début (c'est plus simple)
	// On enregistre les modifications

	$sql_type = "UPDATE edt_gr_nom SET subdivision_type = '".$type."' WHERE id = '".$id2."'";
	$query_type = mysqli_query($GLOBALS["mysqli"], $sql_type);

	if ($query_type) {
		// et on renvoie ce qu'il faut pour l'affichage
		echo '
		<p onclick="classeEdtAjax(\'id2_'.$id2.'\', \''.$type.'\', \'type\');" title="Modifier le type">'.$type.'&nbsp;
			<span style="color: green; font-size:0.5em;">(ok !)</span></p>';
	}else{
		echo '
		<p title="'.$sql_type.'" style="color: red;">Erreur !</p>';
	}

}elseif($action == "nom_gr"){
	// On vérifie quelques petits trucs
	if (is_numeric($id3)) {

		$infos =  ($classe) ? urldecode($classe) : NULL; // car on garde la variable du début
		// On envoie alors le bon input qui permet d'afficher le nom cours et le nom long

		echo '
			<input type="text" id="fr_'.$id3.'" value="'.$infos.'" onblur="classeEdtAjax(\'id00_'.$id3.'\', \'fr_'.$id3.'\', \'nom_en\');" />
		';

	}
}elseif($action == "nom_gr2"){

	// On vérifie quelques petits trucs
	if (is_numeric($id4)) {

		$infos = (isset($classe) AND $classe != "-") ? urldecode($classe) : NULL; // car on garde la variable du début

		// On envoie alors le bon input qui permet d'afficher le nom cours et le nom long

		echo '
			<input type="text" id="fra_'.$id4.'" value="'.$infos.'" onblur="classeEdtAjax(\'id000_'.$id4.'\', \'fra_'.$id4.'\', \'nom_en2\');" />
		';

	}

}elseif($action == "nom_en"){

	if (is_numeric($id3)) {

		$nom =  ($classe) ? urldecode($classe) : NULL;; // car on garde la variable du début
		// On enregistre la modification
		$sql_nom = "UPDATE edt_gr_nom SET nom = '".$nom."' WHERE id = '".$id3."'";
		$query_nom = mysqli_query($GLOBALS["mysqli"], $sql_nom);

		if ($query_nom) {
			echo '
			<p onclick="classeEdtAjax(\'id00_'.$id3.'\', \''.$nom.'\', \'nom_gr\');" title="Modifier le nom">'.$nom.'&nbsp;
				<span style="color: green; font-size:0.5em;">(ok !)</span></p>';
		}else{

			echo '
			<p title="'.$sql_nom.'" style="color: red;">Erreur !</p>';

		}

	}

}elseif($action == "nom_en2"){

	if (is_numeric($id4)) {

		$nom_lg =  ($classe) ? urldecode($classe) : NULL;; // car on garde la variable du début
		// On enregistre la modification
		$sql_nom_lg = "UPDATE edt_gr_nom SET nom_long = '".$nom_lg."' WHERE id = '".$id4."'";
		$query_nom_lg = mysqli_query($GLOBALS["mysqli"], $sql_nom_lg);

		if ($query_nom_lg) {
			echo '
		<p onclick="classeEdtAjax(\'id000_'.$id4.'\', \''.$nom_lg.'\', \'nom_gr2\');" title="Modifier le nom long">'.$nom_lg.'&nbsp;
			<span style="color: green; font-size:0.5em;">(ok !)</span></p>';
		}else{
			echo '
			<p title="'.$sql_nom_lg.'" style="color: red;">Erreur !</p>';
		}

	}

}
?>
