<?php

/**
 * @copyright 2008-2011
 *
 * Fichier qui renvoie un select des professeurs de l'établissement
 * pour l'intégrer dans un fomulaire
 *
 */
// On récupère les infos utiles pour le fonctionnement des requêtes sql
$niveau_arbo = 1;
require_once("../lib/initialisations.inc.php");

// Sécurité : éviter que quelqu'un appelle ce fichier seul
$serveur_script = $_SERVER["SCRIPT_NAME"];
$analyse = explode("/", $serveur_script);
$analyse[4] = isset($analyse[4]) ? $analyse[4] : NULL;
	if ($analyse[4] == "select_aid_groupes.php") {
		die();
	}

$increment = isset($nom_select) ? $nom_select : "liste_aid_groupes";
$id_select = isset($nom_id_select) ? ' id="'.$nom_id_select.'"' : NULL;
$test_selected = isset($nom_selected) ? $nom_selected : NULL;

$id_groupe_defaut="";
$sql="SELECT id FROM groupes WHERE name LIKE '%$valeur%' LIMIT 1;";
$res_grp=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_grp)>0) {
	$lig_grp_def=mysqli_fetch_object($res_grp);
	$id_groupe_defaut=$lig_grp_def->id;
}
else {
	$tmp_val=preg_replace("/[^A-Za-z0-9]/","%", $valeur);
	$sql="SELECT id FROM groupes WHERE name LIKE '%$tmp_val%' LIMIT 1;";
	$res_grp=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_grp)>0) {
		$lig_grp_def=mysqli_fetch_object($res_grp);
		$id_groupe_defaut=$lig_grp_def->id;
	}
}

$tab_mat_ligne=array();
if(isset($tab_matiere[$valeur])) {
	//$sql="SELECT DISTINCT id_groupe FROM j_groupes_matieres WHERE id_matiere='".mysql_real_escape_string($tab_matiere[$valeur])."';";
	$sql="SELECT DISTINCT jgm.id_groupe FROM j_groupes_matieres jgm, j_groupes_classes jgc, classes c WHERE jgm.id_matiere='".mysqli_real_escape_string($GLOBALS["mysqli"], $tab_matiere[$valeur])."' AND jgm.id_groupe=jgc.id_groupe AND jgc.id_classe=c.id ORDER BY c.classe;";
	//echo "$sql<br />";
	$res_mat=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_mat)>0) {
		while($lig_mat=mysqli_fetch_object($res_mat)) {
			$tab_mat_ligne[]=$lig_mat->id_groupe;
		}
	}
}

echo '
	<select name ="'.$increment.'"'.$id_select.' onmouseover="if(document.getElementById(\'texte_nomGepi'.$l.'\')) {document.getElementById(\'texte_nomGepi'.$l.'\').style.backgroundColor=\'yellow\'}" onmouseout="if(document.getElementById(\'texte_nomGepi'.$l.'\')) {document.getElementById(\'texte_nomGepi'.$l.'\').style.backgroundColor=\'\'}">
		<option value="aucun">Liste des AID et des groupes</option>
			<optgroup label="Les AID">';
	// on recherche la liste des AID
	$query = mysqli_query($GLOBALS["mysqli"], "SELECT id, nom FROM aid");
	$nbre = mysqli_num_rows($query);
	for($i = 0; $i < $nbre; $i++){
		$nom[$i] = old_mysql_result($query, $i, "nom");
		$indice_aid[$i] = old_mysql_result($query, $i, "id");
		/*/ On récupère le nom précis de cette AID
		$query2 = mysql_query("SELECT nom FROM aid WHERE id = '".$indice_aid[$i]."' ORDER BY nom");
		$nom_aid = old_mysql_result($query2, 0,"nom");
		$query3 = mysql_query("SELECT login FROM j_aid_eleves WHERE indice_aid = '".$indice_aid[$i]."'");
		$nbre_eleves = mysql_num_rows($query3);
		 ('.$nom_aid.' avec '.$nbre_eleves.' élèves)*/
		// On teste le selected
		if ($nom[$i] == $test_selected) {
			$selected = ' selected="selected"';
		}else{
			$selected = '';
		}
		echo '
		<option value="AID|'.$indice_aid[$i].'"'.$selected.'>'.$nom[$i].'</option>';
	}
	echo '
			</optgroup>
';

	if(count($tab_mat_ligne)>0) {

		echo '
			<optgroup label="Les groupes de la matière">';
		//$sql="SELECT g.id, g.description, g.name FROM groupes g, j_groupes_matieres jgm WHERE jgm.id_groupe=g.id AND jgm.id_matiere='".mysql_real_escape_string($tab_matiere[$valeur])."' ORDER BY description";
		$sql="SELECT DISTINCT g.id, g.description, g.name FROM groupes g, j_groupes_matieres jgm, j_groupes_classes jgc, classes c WHERE jgm.id_groupe=g.id AND jgm.id_matiere='".mysqli_real_escape_string($GLOBALS["mysqli"], $tab_matiere[$valeur])."' AND jgm.id_groupe=jgc.id_groupe AND jgc.id_classe=c.id ORDER BY c.classe, g.description";
		$query = mysqli_query($GLOBALS["mysqli"], $sql);
		$nbre_groupes = mysqli_num_rows($query);
		for($a = 0; $a < $nbre_groupes; $a++){
			$id_groupe[$a]["id"] = old_mysql_result($query, $a, "id");
			$id_groupe[$a]["description"] = old_mysql_result($query, $a, "description");
			$id_groupe[$a]["name"] = old_mysql_result($query, $a, "name");

			// On récupère toutes les infos pour l'affichage
			// On n'utilise pas getGroup() car elle est trop longue et récupère trop de choses dont on n'a pas besoin

			$query1 = mysqli_query($GLOBALS["mysqli"], "SELECT classe FROM j_groupes_classes jgc, classes c WHERE jgc.id_classe = c.id AND jgc.id_groupe = '".$id_groupe[$a]["id"]."'");
			$chaine_classe="";
			$cpt_classe=0;
			while($lig_classe=mysqli_fetch_object($query1)) {
				if($cpt_classe>0) {$chaine_classe.=", ";}
				$chaine_classe.=$lig_classe->classe;
				$cpt_classe++;
			}

			// On teste le selected après s'être assuré qu'il n'était pas déjà renseigné
				if ($id_groupe[$a]["description"] == $test_selected) {
					$selected = ' selected="selected"';
				} elseif($id_groupe[$a]["id"] == $id_groupe_defaut) {
					$selected = ' selected="selected"';
				} else {
					$selected = '';
				}

			$info_groupe=get_info_grp($id_groupe[$a]["id"]);

			//echo '		<option value="'.$id_groupe[$a]["id"].'"'.$selected.'>'.$id_groupe[$a]["description"].'('.$classe[0].')</option>';
			echo '		<option value="'.$id_groupe[$a]["id"].'"'.$selected;
			if((in_array($id_groupe[$a]["id"], $tab_mat_ligne))||((strstr($val, "?")==false)&&(preg_match("/$val/", $info_groupe)))) {
				echo ' style="color:blue;"';
			}
			//echo '>'.$id_groupe[$a]["description"].'('.$classe[0].') ('.$id_groupe[$a]["name"].')</option>';
			echo '>';

			/*
			echo $id_groupe[$a]["name"].' - '.$id_groupe[$a]["description"].' (';
			echo $chaine_classe;
			echo ') ('.$id_groupe[$a]["name"].')';
			*/
			echo $info_groupe;

			echo '</option>';
		}
		echo '
			</optgroup>
';
	}

	echo '
			<optgroup label="Les groupes">';
	//$query = mysql_query("SELECT id, description, name FROM groupes ORDER BY description");
	//$sql="SELECT g.id, g.description, g.name FROM groupes g, j_groupes_matieres jgm WHERE jgm.id_groupe=g.id AND jgm.id_matiere!='".mysql_real_escape_string($tab_matiere[$valeur])."' ORDER BY description";
	$sql="SELECT DISTINCT g.id, g.description, g.name FROM groupes g, j_groupes_matieres jgm, j_groupes_classes jgc, classes c WHERE jgm.id_groupe=g.id AND jgm.id_matiere!='".mysqli_real_escape_string($GLOBALS["mysqli"], $tab_matiere[$valeur])."' AND jgm.id_groupe=jgc.id_groupe AND jgc.id_classe=c.id ORDER BY g.name, g.description, c.classe";
	$query = mysqli_query($GLOBALS["mysqli"], $sql);
	$nbre_groupes = mysqli_num_rows($query);
	for($a = 0; $a < $nbre_groupes; $a++){
		$id_groupe[$a]["id"] = old_mysql_result($query, $a, "id");
		$id_groupe[$a]["description"] = old_mysql_result($query, $a, "description");
		$id_groupe[$a]["name"] = old_mysql_result($query, $a, "name");

		// On récupère toutes les infos pour l'affichage
		// On n'utilise pas getGroup() car elle est trop longue et récupère trop de choses dont on n'a pas besoin

		$query1 = mysqli_query($GLOBALS["mysqli"], "SELECT classe FROM j_groupes_classes jgc, classes c WHERE jgc.id_classe = c.id AND jgc.id_groupe = '".$id_groupe[$a]["id"]."'");
		//$classe = mysql_fetch_array($query1);
		$chaine_classe="";
		$cpt_classe=0;
		while($lig_classe=mysqli_fetch_object($query1)) {
			if($cpt_classe>0) {$chaine_classe.=", ";}
			$chaine_classe.=$lig_classe->classe;
			$cpt_classe++;
		}

		// On teste le selected après s'être assuré qu'il n'était pas déjà renseigné
			if ($id_groupe[$a]["description"] == $test_selected) {
				$selected = ' selected="selected"';
			} elseif($id_groupe[$a]["id"] == $id_groupe_defaut) {
				$selected = ' selected="selected"';
			} else {
				$selected = '';
			}

		$info_groupe=get_info_grp($id_groupe[$a]["id"]);

		//echo '		<option value="'.$id_groupe[$a]["id"].'"'.$selected.'>'.$id_groupe[$a]["description"].'('.$classe[0].')</option>';
		echo '		<option value="'.$id_groupe[$a]["id"].'"'.$selected;
		if((in_array($id_groupe[$a]["id"], $tab_mat_ligne))||((strstr($val, "?")==false)&&(preg_match("/$val/", $info_groupe)))) {
			echo ' style="color:blue;"';
		}
		//echo '>'.$id_groupe[$a]["description"].'('.$classe[0].') ('.$id_groupe[$a]["name"].')</option>';
		//echo '>'.$id_groupe[$a]["description"].' (';
		echo '>';

		/*
		echo $id_groupe[$a]["name"].' - '.$id_groupe[$a]["description"].' (';
		echo $chaine_classe;
		echo ') ('.$id_groupe[$a]["name"].')';
		*/
		echo $info_groupe;

		echo '</option>';
	}
echo '
			</optgroup>
';
echo '	</select>';

?>
