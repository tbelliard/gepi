<?php
/*
 * Last modification  : 01/09/2005
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);


// Resume session
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
};

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}
//**************** EN-TETE *****************************
//$titre_page = "Gestion des utilisateurs | Réinitialisation des mots de passe";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

// On appelle la lib utilisée pour la génération des mots de passe
include("randpass.php");


$user_login = isset($_POST["user_login"]) ? $_POST["user_login"] : (isset($_GET["user_login"]) ? $_GET["user_login"] : false);
$user_status = isset($_POST["user_status"]) ? $_POST["user_status"] : (isset($_GET["user_status"]) ? $_GET["user_status"] : false);
$user_classe = isset($_POST["user_classe"]) ? $_POST["user_classe"] : (isset($_GET["user_classe"]) ? $_GET["user_classe"] : false);


// Il faut être sûr que l'on ne fait pas de réinitialisation accidentelle de tous les utilisateurs...
// On bloque donc l'opération si jamais un des trois paramètres n'a pas été passé correctement, pour une raison ou une autre.

if ($user_login AND $user_login == $_SESSION['login']) {
	$user_login = false;
	echo "<p>ERREUR ! Utilisez l'interface 'Gérer mon compte' pour changer votre mot de passe !</p>";
	echo "</body></html>";
	die();
}

if ($user_status and $user_status != "scolarite" and $user_status != "professeur" and $user_status != "cpe" and $user_status != "secours" and $user_status != "responsable" and $user_status != "eleve") {
	$user_status = false;
	echo "<p>ERREUR ! L'identifiant de statut est erroné. L'opération ne peut pas continuer.</p>";
	echo "</body></html>";
	die();
}

if ($user_classe AND !is_numeric($user_classe)) {
	$user_classe = false;
	echo "<p>ERREUR ! L'identifiant de la classe est erroné. L'opération ne peut pas continuer.</p>";
	echo "</body></html>";
	die();
}
//----

//TODO: Sans doute faudrait-il ajouter des tests ici, si jamais un jour quelqu'un d'autre que l'administrateur peut accéder à la page.
if ($user_login) {
	// Si on est ici, c'est qu'on a demandé la réinitialisation du mot de passe d'un seul utilisateur. C'est simple :)
		$call_user_info = mysql_query("SELECT * FROM utilisateurs WHERE (" .
				"login = '" . $user_login ."' and " .
				"etat='actif' and " .
				"statut != 'administrateur')");
} else {

	if ($user_status) {
		if ($user_classe) {
			// On a un statut et une classe. Cette opération s'applique soit aux élèves soit aux parents
			if ($user_status == "responsable") {
				// Sélection de tous les responsables d'élèves de la classe donnée
				$call_user_info = mysql_query("SELECT distinct(u.login), u.nom, u.prenom, u.statut, u.password, u.email " .
						"FROM utilisateurs u, resp_pers r, responsables2 re, classes c, j_eleves_classes jec, eleves e WHERE (" .
						"u.login = r.login AND " .
						"u.statut = 'responsable' AND " .
						"r.pers_id = re.pers_id AND " .
						"re.ele_id = e.ele_id AND " .
						"e.login = jec.login AND " .
						"jec.id_classe = '".$user_classe."')");
				
			} elseif ($user_status == "eleve") {
				// Sélection de tous les utilisateurs élèves de la classe donnée
				$call_user_info = mysql_query("SELECT distinct(u.login), u.nom, u.prenom, u.statut, u.password, u.email " .
						"FROM utilisateurs u, classes c, j_eleves_classes jec WHERE (" .
						"u.login = jec.login AND " .
						"jec.id_classe = '".$user_classe."')");
			}
		} else {
			// Ici, on ne s'occupe pas de la classe, donc on sélectionne tous les utilisateurs pour le statut considéré,
			// quel qu'il soit
			$call_user_info = mysql_query("SELECT * FROM utilisateurs WHERE (" .
					"login != '" . $_SESSION['login'] . "' AND " .
					"etat = 'actif' AND " .
					"statut = '" . $user_status . "')");
		}
	} else {
		// Ni statut ni classe ni login n'ont été transmis. On sélectionne alors tous les personnels de l'établissement,
		// c'est à dire tout le monde sauf l'administrateur connecté actuellement, les parents, et les élèves.
		
		$call_user_info = mysql_query("SELECT * FROM utilisateurs WHERE (" .
				"login!='" . $_SESSION['login'] . "' and " .
				"etat='actif' and " .
				"(statut = 'professeur' OR " .
				"statut = 'scolarite' OR " .
				"statut = 'cpe' OR " .
				"statut = 'secours'))");
	}
}

$nb_users = mysql_num_rows($call_user_info);
$p = 0;

while ($p < $nb_users) {

    $user_login = mysql_result($call_user_info, $p, "login");
    $user_nom = mysql_result($call_user_info, $p, "nom");
    $user_prenom = mysql_result($call_user_info, $p, "prenom");
    $user_password = mysql_result($call_user_info, $p, "password");
    $user_statut = mysql_result($call_user_info, $p, "statut");
    $user_email = mysql_result($call_user_info, $p, "email");


    // On réinitialise le mot de passe
    $new_password = pass_gen();
    $save_new_pass = mysql_query("UPDATE utilisateurs SET password='" . md5($new_password) . "', change_mdp = 'y' WHERE login='" . $user_login . "'");


    $call_matieres = mysql_query("SELECT * FROM j_professeurs_matieres j WHERE j.id_professeur = '$user_login' ORDER BY ordre_matieres");
    $nb_mat = mysql_num_rows($call_matieres);
    $k = 0;
    while ($k < $nb_mat) {
        $user_matiere[$k] = mysql_result($call_matieres, $k, "id_matiere");
        $k++;
    }

    $call_data = mysql_query("SELECT * FROM classes");
    $nombre_classes = mysql_num_rows($call_data);
    $i = 0;
    while ($i < $nombre_classes){
        $classe[$i] = mysql_result($call_data, $i, "classe");
        $i++;
    }

    $impression = getSettingValue("Impression");

    echo "<p>A l'attention de  <span class = \"bold\">" . $user_prenom . " " . $user_nom . "</span>";
    echo "<br />Nom de login : <span class = \"bold\">" . $user_login . "</span>";
    echo "<br />Mot de passe : <span class = \"bold\">" . $new_password . "</span>";
    echo "<br />Adresse E-mail : <span class = \"bold\">" . $user_email . "</span>";
    echo "</p>";
    echo $impression;
    echo "<p class=saut>&nbsp</p>";
    $p++;

}

require("../lib/footer.inc.php");
?>