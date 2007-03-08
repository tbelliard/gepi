<?php
/*
 * $Id$
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

// Initialisation des variables
$mode = isset($_POST["mode"]) ? $_POST["mode"] : (isset($_GET["mode"]) ? $_GET["mode"] : false);
$action = isset($_POST["action"]) ? $_POST["action"] : (isset($_GET["action"]) ? $_GET["action"] : false);

$msg = '';

// Si on est en traitement par lot, on sélectionne tout de suite la liste des utilisateurs impliqués
$error = false;
if ($mode == "classe") {
	$nb_comptes = 0;
	if ($_POST['classe'] == "all") {
		$quels_parents = mysql_query("SELECT distinct(r.login) " .
				"FROM resp_pers r, responsables2 re, classes c, j_eleves_classes jec, eleves e WHERE (" .
				"r.pers_id = re.pers_id AND " .
				"re.ele_id = e.ele_id AND " .
				"e.login = jec.login AND " .
				"jec.id_classe = c.id)");
		if (!$quels_parents) $msg .= mysql_error();
	} elseif (is_numeric($_POST['classe'])) {
		$quels_parents = mysql_query("SELECT distinct(r.login) " .
				"FROM resp_pers r, responsables2 re, classes c, j_eleves_classes jec, eleves e WHERE (" .
				"r.pers_id = re.pers_id AND " .
				"re.ele_id = e.ele_id AND " .
				"e.login = jec.login AND " .
				"jec.id_classe = '" . $_POST['classe']."')");
		if (!$quels_parents) $msg .= mysql_error();
	} else {
		$error = true;
		$msg .= "Vous devez sélectionner au moins une classe !<br/>";
	}
}

// Trois actions sont possibles depuis cette page : activation, désactivation et suppression.
// L'édition se fait directement sur la page de gestion des responsables

if ($action == "rendre_inactif") {
	// Désactivation d'utilisateurs actifs
	if ($mode == "individual") {
		// Désactivation pour un utilisateur unique
		$test = mysql_result(mysql_query("SELECT count(login) FROM utilisateurs WHERE (login = '" . $_GET['parent_login']."' AND etat = 'actif')"), 0);
		if ($test == "0") {
			$msg .= "Erreur lors de la désactivation de l'utilisateur : celui-ci n'existe pas ou bien est déjà inactif.";
		} else {
			$res = mysql_query("UPDATE utilisateurs SET etat='inactif' WHERE (login = '".$_GET['parent_login']."')");
			if ($res) {
				$msg .= "L'utilisateur ".$_GET['parent_login'] . " a été désactivé.";
			} else {
				$msg .= "Erreur lors de la désactivation de l'utilisateur.";
			}
		}
	} elseif ($mode == "classe" and !$error) {
		// Pour tous les parents qu'on a déjà sélectionnés un peu plus haut, on désactive les comptes
		while ($current_parent = mysql_fetch_object($quels_parents)) {
			$test = mysql_result(mysql_query("SELECT count(login) FROM utilisateurs WHERE login = '" . $current_parent->login ."'"), 0);
			if ($test > 0) {
				// L'utilisateur existe bien dans la tables utilisateurs, on désactive
				$res = mysql_query("UPDATE utilisateurs SET etat = 'inactif' WHERE login = '" . $current_parent->login . "'");
				if (!$res) {
					$msg .= "Erreur lors de la désactivation du compte ".$current_parent->login."<br/>";
				} else {
					$nb_comptes++;
				}
			}
		}
		$msg .= "$nb_comptes comptes ont été désactivés.";		
	}
} elseif ($action == "rendre_actif") {
	// Activation d'utilisateurs préalablement désactivés
	if ($mode == "individual") {
		// Activation pour un utilisateur unique
		$test = mysql_result(mysql_query("SELECT count(login) FROM utilisateurs WHERE (login = '" . $_GET['parent_login']."' AND etat = 'inactif')"), 0);
		if ($test == "0") {
			$msg .= "Erreur lors de la désactivation de l'utilisateur : celui-ci n'existe pas ou bien est déjà actif.";
		} else {
			$res = mysql_query("UPDATE utilisateurs SET etat='actif' WHERE (login = '".$_GET['parent_login']."')");
			if ($res) {
				$msg .= "L'utilisateur ".$_GET['parent_login'] . " a été activé.";
			} else {
				$msg .= "Erreur lors de l'activation de l'utilisateur.";
			}
		}
	} elseif ($mode == "classe") {
		// Pour tous les parents qu'on a déjà sélectionnés un peu plus haut, on désactive les comptes
		while ($current_parent = mysql_fetch_object($quels_parents)) {
			$test = mysql_result(mysql_query("SELECT count(login) FROM utilisateurs WHERE login = '" . $current_parent->login ."'"), 0);
			if ($test > 0) {
				// L'utilisateur existe bien dans la tables utilisateurs, on désactive
				$res = mysql_query("UPDATE utilisateurs SET etat = 'actif' WHERE login = '" . $current_parent->login . "'");
				if (!$res) {
					$msg .= "Erreur lors de l'activation du compte ".$current_parent->login."<br/>";
				} else {
					$nb_comptes++;
				}
			}
		}
		$msg .= "$nb_comptes comptes ont été activés.";		
	}
	
} elseif ($action == "supprimer") {
	// Suppression d'un ou plusieurs utilisateurs
	if ($mode == "individual") {
		// Suppression pour un utilisateur unique
		$test = mysql_result(mysql_query("SELECT count(login) FROM utilisateurs WHERE (login = '" . $_GET['parent_login']."')"), 0);
		if ($test == "0") {
			$msg .= "Erreur lors de la suppression de l'utilisateur : celui-ci n'existe pas.";
		} else {
			$res = mysql_query("DELETE FROM utilisateurs WHERE (login = '".$_GET['parent_login']."')");
			if ($res) {
				$msg .= "L'utilisateur ".$_GET['parent_login'] . " a été supprimé.";
				$res2 = mysql_query("UPDATE resp_pers SET login='' WHERE login = '".$_GET['parent_login'] . "'");
			} else {
				$msg .= "Erreur lors de la suppression de l'utilisateur.";
			}
		}
	} elseif ($mode == "classe") {
		// Pour tous les parents qu'on a déjà sélectionnés un peu plus haut, on désactive les comptes
		while ($current_parent = mysql_fetch_object($quels_parents)) {
			$test = mysql_result(mysql_query("SELECT count(login) FROM utilisateurs WHERE login = '" . $current_parent->login ."'"), 0);
			if ($test > 0) {
				// L'utilisateur existe bien dans la tables utilisateurs, on désactive
				$res = mysql_query("DELETE FROM utilisateurs WHERE login = '" . $current_parent->login . "'");
				if (!$res) {
					$msg .= "Erreur lors de l'activation du compte ".$current_parent->login."<br/>";
				} else {
					$res = mysql_query("UPDATE resp_pers SET login = '' WHERE login = '" . $current_parent->login ."'");
					$nb_comptes++;
				}
			}
		}
		$msg .= "$nb_comptes comptes ont été supprimés.";		
	}
} elseif ($action == "reinit_password") {
	if ($mode != "classe") {
		$msg .= "Erreur : Vous devez sélectionner une classe.";
	} elseif ($mode == "classe") {
		if ($_POST['classe'] == "all") {
			$msg .= "Vous allez réinitialiser les mots de passe de tous les utilisateurs ayant le statut 'responsable'.<br/>Si vous êtes vraiment sûr de vouloir effectuer cette opération, cliquez sur le lien ci-dessous :<br/>";
			$msg .= "<a href=\"reset_passwords.php?user_status=responsable\" target='_blank'>Réinitialiser les mots de passe</a>";			
		} else if (is_numeric($_POST['classe'])) {
			$msg .= "Vous allez réinitialiser les mots de passe de tous les utilisateurs ayant le statut 'responsable' pour cette classe.<br/>Si vous êtes vraiment sûr de vouloir effectuer cette opération, cliquez sur le lien ci-dessous :<br/>";
			$msg .= "<a href=\"reset_passwords.php?user_status=responsable&amp;user_classe=".$_POST['classe']."\" target='_blank'>Réinitialiser les mots de passe</a>";			
		}
	}
}

//**************** EN-TETE *****************
$titre_page = "Modifier un compte parent";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<p class=bold>
<a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>
<?php
if ((getSettingValue('use_sso') != "cas" and getSettingValue("use_sso") != "lemon" and getSettingValue('use_sso') != "lcs" and getSettingValue("use_sso") != "ldap_scribe") OR $block_sso) {
    echo " | <a href=\"reset_passwords.php?user_status=responsable\" onclick=\"javascript:return confirm('Êtes-vous sûr de vouloir effectuer cette opération ?\\n Celle-ci est irréversible, et réinitialisera les mots de passe de tous les utilisateurs ayant le statut \'responsable\' et marqués actifs, avec un mot de passe alpha-numérique généré aléatoirement.\\n En cliquant sur OK, vous lancerez la procédure, qui génèrera une page contenant les fiches-bienvenue à imprimer immédiatement pour distribution aux utilisateurs concernés.')\">Réinitialiser mots de passe</a>";
}
echo "</p>";
	echo "<p><b>Actions par lot</b> :";
	echo "<form action='edit_responsable.php' method='post'>";
	echo "<input type='hidden' name='mode' value='classe' />";
	echo "<input type='radio' name='action' value='rendre_inactif' /> Rendre inactif";
	echo "<input type='radio' name='action' value='rendre_actif' style='margin-left: 20px;'/> Rendre actif ";
	echo "<input type='radio' name='action' value='reinit_password' style='margin-left: 20px;'/> Réinitialiser mots de passe";
	echo "<input type='radio' name='action' value='supprimer' style='margin-left: 20px;' /> Supprimer<br/>";
	echo "<br/>";
	echo "<select name='classe' size='1'>";
	echo "<option value='none'>Sélectionnez une classe</option>";
	echo "<option value='all'>Toutes les classes</option>";
	$quelles_classes = mysql_query("SELECT id,classe FROM classes ORDER BY classe");
	while ($current_classe = mysql_fetch_object($quelles_classes)) {
		echo "<option value='".$current_classe->id."'>".$current_classe->classe."</option>";
	}
	echo "</select>";
	echo "&nbsp;<input type='submit' name='Valider' value='Valider' />";
	echo "</form>";
?>
<br/><br/>
<table border="1">
<tr>
	<th>Identifiant</th><th>Nom Prénom</th><th>Etat</th><th>Actions</th>
</tr>
<?php
$quels_parents = mysql_query("SELECT u.*, r.pers_id FROM utilisateurs u, resp_pers r WHERE (u.statut = 'responsable' AND r.login = u.login) ORDER BY u.nom,u.prenom");

while ($current_parent = mysql_fetch_object($quels_parents)) {
	echo "<tr>\n";
		echo "<td>";
			echo "<a href='../responsables/modify_resp.php?pers_id=".$current_parent->pers_id."'>".$current_parent->login."</a>";
		echo "</td>\n";
		echo "<td>";
			echo $current_parent->nom . " " . $current_parent->prenom;
		echo "</td>\n";
		echo "<td>";
			echo $current_parent->etat;
			echo "<br/>";
			if ($current_parent->etat == "actif") {
				echo "<a href='edit_responsable.php?action=rendre_inactif&amp;mode=individual&amp;parent_login=".$current_parent->login."'>Désactiver";
			} else {
				echo "<a href='edit_responsable.php?action=rendre_actif&amp;mode=individual&amp;parent_login=".$current_parent->login."'>Activer";
			}
			echo "</a>";
		echo "</td>\n";
		echo "<td>";
		echo "<a href='edit_responsable.php?action=supprimer&amp;mode=individual&amp;parent_login=".$current_parent->login."' onclick=\"javascript:return confirm('Êtes-vous sûr de vouloir supprimer l\'utilisateur ?')\">Supprimer</a><br/>";
		echo "<a href=\"reset_passwords.php?user_login=".$current_parent->login."\" onclick=\"javascript:return confirm('Êtes-vous sûr de vouloir effectuer cette opération ?\\n Celle-ci est irréversible, et réinitialisera le mot de passe de l\'utilisateur avec un mot de passe alpha-numérique généré aléatoirement.\\n En cliquant sur OK, vous lancerez la procédure, qui génèrera une page contenant la fiche-bienvenue à imprimer immédiatement pour distribution à l\'utilisateur concerné.')\" target='change'>Réinitialiser le mot de passe</a>";
		echo "</td>\n";
	echo "</tr>\n";
}
?>
</table>
<?php require("../lib/footer.inc.php");?>