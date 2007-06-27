<?php
/*
 * $Id$
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
$create_mode = isset($_POST["mode"]) ? $_POST["mode"] : NULL;

if ($create_mode == "classe" OR $create_mode == "individual") {
	// On a une demande de création, on continue

	// On veut alimenter la variable $quels_parents avec un résultat mysql qui contient
	// la liste des parents pour lesquels on veut créer un compte
	$error = false;
	$msg = "";
	if ($create_mode == "individual") {
		// $_POST['pers_id'] est filtré automatiquement contre les injections SQL, on l'utilise directement
		$test = mysql_query("SELECT count(e.login) FROM eleves e, responsables2 re WHERE (e.ele_id = re.ele_id AND re.pers_id = '" . $_POST['pers_id'] ."')");
		if (mysql_result($test, 0) == "0") {
			$error = true;
			$msg .= "Erreur lors de la création de l'utilisateur : aucune association avec un élève n'a été trouvée !<br/>";
		} else {
			$quels_parents = mysql_query("SELECT r.* FROM resp_pers r, responsables2 re WHERE (" .
				"r.login = '' AND " .
				"r.pers_id = re.pers_id AND " .
				"re.pers_id = '" . $_POST['pers_id'] ."')");
		}
	} else {
		// On est en mode 'classe'
		if ($_POST['classe'] == "all") {
			$quels_parents = mysql_query("SELECT distinct(r.pers_id), r.nom, r.prenom, r.civilite, r.mel " .
					"FROM resp_pers r, responsables2 re, classes c, j_eleves_classes jec, eleves e WHERE (" .
					"r.login = '' AND " .
					"r.pers_id = re.pers_id AND " .
					"re.ele_id = e.ele_id AND " .
					"e.login = jec.login AND " .
					"jec.id_classe = c.id)");
			if (!$quels_parents) $msg .= mysql_error();
		} elseif (is_numeric($_POST['classe'])) {
			$quels_parents = mysql_query("SELECT distinct(r.pers_id), r.nom, r.prenom, r.civilite, r.mel " .
					"FROM resp_pers r, responsables2 re, classes c, j_eleves_classes jec, eleves e WHERE (" .
					"r.login = '' AND " .
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

	if (!$error) {
		$nb_comptes = 0;
		while ($current_parent = mysql_fetch_object($quels_parents)) {
			// Création du compte utilisateur pour le responsable considéré
			$reg_login = generate_unique_login($current_parent->nom, $current_parent->prenom, getSettingValue("mode_generation_login"));
			$reg = true;
			$reg = mysql_query("INSERT INTO utilisateurs SET " .
					"login = '" . $reg_login . "', " .
					"nom = '" . addslashes($current_parent->nom) . "', " .
					"prenom = '". addslashes($current_parent->prenom) ."', " .
					"password = '', " .
					"civilite = '" . $current_parent->civilite."', " .
					"email = '" . $current_parent->mel . "', " .
					"statut = 'responsable', " .
					"etat = 'actif', " .
					"change_mdp = 'n'");

			if (!$reg) {
				$msg .= "Erreur lors de la création du compte ".$reg_login."<br/>";
			} else {
				$sql="UPDATE resp_pers SET login = '" . $reg_login . "' WHERE (pers_id = '" . $current_parent->pers_id . "')";
				$reg2 = mysql_query($sql);
				//$msg.="$sql<br />";
				$nb_comptes++;
			}
		}
		if ($nb_comptes == 1) {
			$msg .= "Un compte a été créé avec succès.<br/>";
		} elseif ($nb_comptes > 1) {
			$msg .= $nb_comptes." comptes ont été créés avec succès.<br/>";
		}

		// On propose de mettre à zéro les mots de passe et d'imprimer les fiches bienvenue seulement
		// si au moins un utilisateur a été créé et si on n'est pas en mode SSO.
		if ($nb_comptes > 0 AND getSettingValue('use_sso') != "cas" AND getSettingValue("use_sso") != "lemon" AND getSettingValue("use_sso") != "lcs" AND getSettingValue("use_sso") != "ldap_scribe") {
			if ($create_mode == "individual") {
				// Mode de création de compte individuel. On fait un lien spécifique pour la fiche de bienvenue
				$msg .= "<br/><a target='_blank' href='reset_passwords.php?user_login=".$reg_login."'>";
			} else {
				// On est ici en mode de création par classe
				// Si on opère sur toutes les classes, on ne spécifie aucune classe
				if ($_POST['classe'] == "all") {
				    $msg .= "<br/><a target='_blank' href='reset_passwords.php?user_status=responsable&amp;mode=html'>Imprimer la ou les fiche(s) de bienvenue (Impression HTML)</a>";
					$msg .= "<br/><a target='_blank' href='reset_passwords.php?user_status=responsable&amp;mode=csv'>Imprimer la ou les fiche(s) de bienvenue (Export CSV)</a>";
				} elseif (is_numeric($_POST['classe'])) {
					$msg .= "<br/><a target='_blank' href='reset_passwords.php?user_status=responsable&amp;user_classe=".$_POST['classe']."&amp;mode=html'>Imprimer la ou les fiche(s) de bienvenue (Impression HTML)</a>";
					$msg .= "<br/><a target='_blank' href='reset_passwords.php?user_status=responsable&amp;user_classe=".$_POST['classe']."&amp;mode=csv'>Imprimer la ou les fiche(s) de bienvenue (Export CSV)</a>";
				}
			}
			$msg .= "<br/>Vous devez effectuer cette opération maintenant !";
		}
	}
}

//**************** EN-TETE *****************
$titre_page = "Créer des comptes d'accès responsables";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<p class=bold><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>
</p>
<?php
//$quels_parents = mysql_query("SELECT * FROM resp_pers WHERE login='' ORDER BY nom,prenom");
$quels_parents = mysql_query("SELECT * FROM resp_pers WHERE login='' ORDER BY nom,prenom");
$nb = mysql_num_rows($quels_parents);
if($nb==0){
	echo "<p>Tous les responsables ont un login.</p>\n";
}
else{
	echo "<p>Les $nb responsables ci-dessous n'ont pas encore de compte utilisateur.</p>";
	if (getSettingValue("mode_generation_login") == null) {
		echo "<p><b>ATTENTION !</b> Vous n'avez pas défini le mode de génération des logins. Allez sur la page de <a href='../gestion/param_gen.php'>gestion générale</a> pour définir le mode que vous souhaitez utiliser. Par défaut, les logins seront générés au format pnom tronqué à 8 caractères (ex: ADURANT).</p>";
	}
	if ((getSettingValue('use_sso') == "cas" OR getSettingValue("use_sso") == "lemon"  OR getSettingValue("use_sso") == "lcs" OR getSettingValue("use_sso") == "ldap_scribe")) {
		echo "<p><b>Note :</b> Vous utilisez une authentification externe à Gepi (SSO). Aucun mot de passe ne sera donc assigné aux utilisateurs que vous vous apprêté à créer. Soyez certain de générer les login selon le même format que pour votre source d'authentification SSO.</p>";
	}

	echo "<p><b>Créer des comptes par lot</b> : sélectionnez une classe ou bien l'ensemble des classes puis cliquez sur 'valider'.";
	echo "<form action='create_responsable.php' method='post'>";
	echo "<input type='hidden' name='mode' value='classe' />";
	echo "<select name='classe' size='1'>";
	echo "<option value='none'>Sélectionnez une classe</option>";
	echo "<option value='all'>Toutes les classes</option>";

	$quelles_classes = mysql_query("SELECT id,classe FROM classes ORDER BY classe");
	while ($current_classe = mysql_fetch_object($quelles_classes)) {
		echo "<option value='".$current_classe->id."'>".$current_classe->classe."</option>";
	}
	echo "</select>";
	echo "<input type='submit' name='Valider' value='Valider' />";
	echo "</form>";
	echo "<br/>";
	echo "<p><b>Créer des comptes individuellement</b> : cliquez sur le bouton 'Créer' d'un responsable pour créer un compte associé.</p>";
	echo "<table>";
	while ($current_parent = mysql_fetch_object($quels_parents)) {
		echo "<tr>";
			echo "<td>";
			echo "<form action='create_responsable.php' method='post'>";
			echo "<input type='hidden' name='mode' value='individual'/>";
			echo "<input type='hidden' name='pers_id' value='".$current_parent->pers_id."'/>";
			echo "<input type='submit' value='Créer'/>";
			echo "</form>";
			echo "<td>".$current_parent->nom." ".$current_parent->prenom."</td>";
		echo "</tr>";
	}
	echo "</table>";
}
require("../lib/footer.inc.php");
?>