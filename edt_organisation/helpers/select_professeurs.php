<?php

/**
 * @version $Id: select_professeurs.php 1830 2008-05-18 14:01:00Z jjocal $
 * @copyright 2008
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
	if ($analyse[4] == "select_professeurs.php") {
		die();
	}

$increment = isset($nom_select) ? $nom_select : "liste_professeur"; // name du select
$id_select = isset($nom_id_select) ? (' id="'.$nom_id_select.'"') : NULL; // id du select
if (!isset($nom_selected)) {
	$nom_selected = isset($nom_prof) ? $nom_prof : NULL; // permet de définir le selected
}

echo '
	<select name ="'.$increment.'"'.$id_select.'>
		<option value="aucun">Liste des professeurs</option>';
	// on recherche la liste des professeurs
	$query = mysql_query("SELECT login, nom, prenom FROM utilisateurs
						WHERE statut = 'professeur' AND
						etat = 'actif'
						ORDER BY nom, prenom");
	$nbre = mysql_num_rows($query);
	$verif = 0;
	for($i = 0; $i < $nbre; $i++){

		$utilisateur[$i] = mysql_result($query, $i, "login");
		$nom[$i] = mysql_result($query, $i, "nom");
		$nom_m[$i] = strtoupper(remplace_accents(mysql_result($query, $i, "nom"), 'all_nospace'));
		$prenom[$i] = mysql_result($query, $i, "prenom");

		//Pour les noms composés, on ajoute un test
		$test = explode(" ", $nom_m[$i]);
		// On détermine le selected si c'est possible
		if ($nom_m[$i] == $nom_selected) {

			$verif++; // on crée une marque pour afficher un couleur si il y a une interrogation sur le résultat
			$selected = ' selected="selected"';
		}elseif ($test[0] == $nom_selected) {
			$verif++; // on crée une marque pour afficher un couleur si il y a une interrogation sur le résultat
			$selected = ' selected="selected"';
		}else{
			$selected = '';
		}

		echo '
		<option value="'.$utilisateur[$i].'"'.$selected.'>'.$nom[$i].'&nbsp;&nbsp;'.$prenom[$i].'</option>';
	}
	// On teste la marque $verif[$i] et on calcule le nombre de réponses positives
	if ($verif >= 2) {
		$its_ok = '&nbsp;<span style="color: orange; font-weight: bold;">(?)</span>';
	}elseif($verif == 0){
		$its_ok = '&nbsp;<span style="color: red; font-weight: bold;">(non trouvée)</span>';
	}elseif($verif == 1){
		$its_ok = '&nbsp;<span style="color: green; font-weight: bold;">(ok ?)</span>';
	}else{
		$its_ok = '';
	}
echo '</select>&nbsp;'.$its_ok;
?>