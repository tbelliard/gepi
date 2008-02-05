<?php

/**
 * @version $Id$
 * @copyright 2008
 *
 * Fichier qui renvoie un select des professeurs de l'établissement
 * pour l'intégrer dans un fomulaire
 *
 */
// On récupère les infos utiles pour le fonctionnement des requêtes sql
$niveau_arbo = 2;
require_once("../../lib/initialisations.inc.php");

// Resume session
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
   header("Location: ../../utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == '0') {
    header("Location: ../../logout.php?auto=1");
    die();
}

// Sécurité : éviter que quelqu'un appelle ce fichier seul
$serveur_script = $_SERVER["SCRIPT_NAME"];
$analyse = explode("/", $serveur_script);
	if ($analyse[4] == "select_professeurs.php") {
		die();
	}

$increment = isset($nom_select) ? $nom_select : "liste_professeur";

echo '<select name ="'.$increment.'">';
	// on recherche la liste des professeurs
	$query = mysql_query("SELECT login, nom, prenom FROM utilisateurs WHERE statut = 'professeur'");
	$nbre = mysql_num_rows($query);
	for($i = 0; $i < $nbre; $i++){
		$utilisateur[$i] = mysql_result($query, $i, "login");
		$nom[$i] = mysql_result($query, $i, "nom");
		$prenom[$i] = mysql_result($query, $i, "prenom");

		echo '
		<option value="'.$utilisateur[$i].'">'.$nom[$i].' '.$prenom[$i].'</option>'."\n";
	}
echo '</select>';
?>