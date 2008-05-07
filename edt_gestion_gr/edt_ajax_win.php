<?php
/**
 *
 *
 * @version $Id$
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
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
   header("Location:../utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

/*/ Sécurité
if (!checkAccess()) {
    header("Location: ./logout.php?auto=2");
    die();
}*/

// ===================== fin de l'initialisation ================
// ===================== VARIABLES ==============================

$id_gr = isset($_GET["id_gr"]) ? $_GET["id_gr"] : NULL;
$classe = isset($_GET["classe"]) ? $_GET["classe"] : NULL;
$action = isset($_GET["action"]) ? $_GET["action"] : NULL;
$id = isset($id_gr) ? substr($id_gr, 3) : NULL;
$id2 = isset($id_gr) ? substr($id_gr, 4) : NULL;

// ==========================fin des variables ==================


if ($action == 'modifier') {
	// On renvoie un select avec la liste des classes
	// on recherche la liste des classes
	$query = mysql_query("SELECT id, classe FROM classes ORDER BY id");
	$nbre = mysql_num_rows($query);

	echo '
	<select name="choix_classe">
			<option value="plusieurs">Plusieurs classes</option>
			';

		for($i = 0; $i < $nbre; $i++){
			$classes[$i] = mysql_result($query, $i, "id");
			$nom[$i] = mysql_result($query, $i, "classe");
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
	$query_maj = mysql_query($sql_maj)
										OR trigger_error('Impossible de modifier la classe', E_USER_ERROR);

	$nom_cl = mysql_fetch_array(mysql_query("SELECT classe FROM classes WHERE id = '".$classe."'"));

	echo '<p onclick="classeEdtAjax(\'id_'.$id.'\', \''.$classe.'\', \'modifier\');">'.$nom_cl["classe"].'&nbsp;<span style="color: green; font-size:0.5em;">(ok !)</span></p>';

}elseif($action == 'type'){

	echo '
		<select name="">
			<option value="rien">Type de subdivision</option>
			<option value="plusieurs" onclick="classeEdtAjax(\''.$id_gr.'\', \'plusieurs\', \'type_en\');">Plusieurs classes</option>
			<option value="classe" onclick="classeEdtAjax(\''.$id_gr.'\', \'classe\', \'type_en\');">Une classe (ent.)</option>
			<option value="demi" onclick="classeEdtAjax(\''.$id_gr.'\', \'demi\', \'type_en\');">Subdivision d\'une classe</option>
		</select>';

}elseif($action == 'type_en'){

	// On enregistre les modifications
	echo 'RAS : '.$classe;

}
?>
