<?php

/**
 * ajax_appreciations.php
 * Fichier qui permet la sauvegarde automatique des appréciations au fur et à mesure de leur saisie
 *
 * @version $Id$
 * @copyright 2007
 */

// ============== Initialisation ===================
$affiche_connexion = 'yes';
$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");


// Resume session
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

/*/ Sécurité
if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}*/

// Initialisation des variables
$var1 = isset($_POST["var1"]) ? $_POST["var1"] : (isset($_GET["var1"]) ? $_GET["var1"] : NULL);
$var2 = isset($_POST["var2"]) ? $_POST["var2"] : (isset($_GET["var2"]) ? $_GET["var2"] : NULL);
$appreciation = isset($_POST["var3"]) ? $_POST["var3"] : (isset($_GET["var3"]) ? $_GET["var3"] : NULL);
$professeur = isset($_SESSION["statut"]) ? $_SESSION["statut"] : NULL;

// ========== Fin de l'initialisation de la page =============

// On détermine si les variables envoyées sont bonnes ou pas
$verif_var1 = explode("_t", $var1);
	// On vérifie que le login de l'élève soit valable et qu'il corresponde à l'enseignement envoyé par var2
	$verif_eleve = mysql_query("SELECT login FROM j_eleves_groupes
			WHERE login = '".$verif_var1[0]."'
			AND id_groupe = '".$var2."'
			AND periode = '".$verif_var1[1]."'")
			or die('Erreur de verif_var1 : '.mysql_error());

	// On vérifie que le prof logué peut saisir ces appréciations
	$verif_prof = mysql_query("SELECT login FROM j_groupes_professeurs WHERE id_groupe = '".$var2."'");
		if (mysql_num_rows($verif_prof) >= 1) {
			// On ne fait rien
		} else {
			die('Vous ne pouvez pas saisir d\'appr&eacute;ciations pour cet &eacute;l&eagrave;ve');
		}

	if (mysql_num_rows($verif_eleve) !== 0 AND mysql_num_rows($verif_prof) !== 0) {
		// On vérifie si cette appréciation existe déjà ou non
		$verif_appreciation = mysql_query("SELECT appreciation FROM matieres_appreciations WHERE login = '".$verif_var1[0]."' AND id_groupe = '".$var2."' AND periode = '".$verif_var1[1]."'");
		// Si elle existe, on la met à jour
		if (mysql_num_rows($verif_appreciation) == 1) {
			$miseajour = mysql_query("UPDATE matieres_appreciations SET appreciation = '".utf8_decode($appreciation)."' WHERE login = '".$verif_var1[0]."' AND id_groupe = '".$var2."' AND periode = '".$verif_var1[1]."'");
		} else {
			//sinon on crée une nouvelle appréciation si l'appréciation n'est pas vide
			if ($appreciation != "") {
				$sauvegarde = mysql_query("INSERT INTO matieres_appreciations SET login = '".$verif_var1[0]."', id_groupe = '".$var2."', periode = '".$verif_var1[1]."', appreciation = '".utf8_decode($appreciation)."'");
			}

		}
	}

?>