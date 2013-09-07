<?php
/*
 *
 * Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// On indique qu'il faut crée des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

// Initialisations files
require_once("../lib/initialisations.inc.php");

// Initialisation des variables
$user_login = isset($_POST["user_login"]) ? $_POST["user_login"] : (isset($_GET["user_login"]) ? $_GET["user_login"] : NULL);

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

$mdp_INE=isset($_POST["mdp_INE"]) ? $_POST["mdp_INE"] : NULL;
$ine_password=isset($_POST["ine_password"]) ? $_POST["ine_password"] : NULL;
$ine_password=my_ereg_replace("[^A-Za-z0-9]","",$ine_password);

if (isset($_POST['valid']) and ($_POST['valid'] == "yes")) {

	check_token();

    $user_statut = sql_query1("SELECT statut FROM utilisateurs WHERE login='".$user_login."'");
    if (($user_statut == 'professeur') or ($user_statut == 'cpe') or ($user_statut == 'responsable')) {
        // Mot de passe comportant des lettres et des chiffres
        $flag = 0;
	}
    else {
        // Mot de passe comportant des lettres et des chiffres et au moins un caractère spécial
        $flag = 1;
	}

	if(($mdp_INE=='y')&&($user_statut=='eleve')&&($ine_password!="")) {
		$auth_mode = mysql_result(mysql_query("SELECT auth_mode FROM utilisateurs WHERE login = '".$user_login."'"), 0);
		if ($auth_mode != "gepi" && $gepiSettings['ldap_write_access'] == 'yes') {
			// On est en mode d'écriture LDAP
			$ldap_server = new LDAPServer;
			$reg_data = $ldap_server->update_user($user_login, '', '', '', '', $ine_password,'');
		} else {
			// On est en mode base de données
			$reg_data = Session::change_password_gepi($user_login,$ine_password);
		}

		//ajout Eric En cas de réinitialisation par l'admin, il faut forcer à la première connexion la changement du mot de passe
		if ($_SESSION['statut'] == 'administrateur') {
			$reg_data = mysql_query("UPDATE utilisateurs SET change_mdp = 'y' WHERE login='".$user_login."'");
		}

		if (!$reg_data) {
			$msg = "Erreur lors de l'enregistrement du mot de passe !";
		} else {
			$msg="Le mot de passe a été changé ($user_login:$ine_password) !";
		}
	}
	else {
		if ($_POST['no_anti_inject_password'] != $_POST['reg_password2'])  {
			$msg = "Erreur lors de la saisie : les deux mots de passe ne sont pas identiques, veuillez recommencer !";
		} else if (!(verif_mot_de_passe($NON_PROTECT['password'],$flag))) {
			$msg = "Erreur lors de la saisie du mot de passe (<em>voir les recommandations</em>), veuillez recommencer !";
			if((isset($info_verif_mot_de_passe))&&($info_verif_mot_de_passe!="")) {$msg.="<br />".$info_verif_mot_de_passe;}
		} else {
			$auth_mode = mysql_result(mysql_query("SELECT auth_mode FROM utilisateurs WHERE login = '".$user_login."'"), 0);
			if ($auth_mode != "gepi" && $gepiSettings['ldap_write_access'] == 'yes') {
				// On est en mode d'écriture LDAP
				$ldap_server = new LDAPServer;
				$reg_data = $ldap_server->update_user($user_login, '', '', '', '', $NON_PROTECT['password'],'');
			} else {
				// On est en mode base de données
                                $reg_data = Session::change_password_gepi($user_login,$NON_PROTECT['password']);
			}

			//ajout Eric En cas de réinitialisation par l'admin, il faut forcer à la première connexion la changement du mot de passe
			if ($_SESSION['statut'] == 'administrateur') {
				$reg_data = mysql_query("UPDATE utilisateurs SET change_mdp = 'y' WHERE login='".$user_login."'");
			}

			if (!$reg_data) {
				$msg = "Erreur lors de l'enregistrement du mot de passe !";
			} else {
				$msg="Le mot de passe a été changé !";
			}
		}
	}
}

// On appelle les informations de l'utilisateur
if (isset($user_login) and ($user_login!='')) {
    $call_user_info = mysql_query("SELECT nom,prenom,statut,auth_mode FROM utilisateurs WHERE login='".$user_login."'");
    $auth_mode = mysql_result($call_user_info, "0", "auth_mode");
    $user_statut = mysql_result($call_user_info, "0", "statut");
    $user_nom = mysql_result($call_user_info, "0", "nom");
    $user_prenom = mysql_result($call_user_info, "0", "prenom");
}

//**************** EN-TETE *****************
$titre_page = "Gestion des utilisateurs | Modifier un mot de passe";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
?>
<p class='bold'><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link' /> Retour</a> | <a href="help.php">Aide</a></p>
<?php
// dans le cas de LCS, existence d'utilisateurs locaux reprérés grâce au champ password non vide.
$testpassword = sql_query1("select password from utilisateurs where login = '".$user_login."'");
if ($testpassword == -1) $testpassword = '';
if ($auth_mode != "gepi" && $gepiSettings['ldap_write_access'] != "yes") {
    echo "Vous ne pouvez pas changer le mot de passe des utilisateurs lorsque Gepi est configuré pour utiliser une authentification extérieure et que vous n'avez pas accès à l'annuaire LDAP en écriture.";
    echo "</div>\n";
    echo "</body></html>\n";
    die();
}

echo "<h2>Changement du mot de passe</h2>\n";

if (mb_strtoupper($user_login) != mb_strtoupper($_SESSION['login'])) {
    if (($user_statut == 'professeur') or ($user_statut == 'cpe') or ($user_statut == 'responsable')) {
        // Mot de passe comportant des lettres et des chiffres
        $flag = 0;
	}
    else {
        // Mot de passe comportant des lettres et des chiffres et au moins un caractère spécial
        $flag = 1;
	}
    echo "<form enctype=\"multipart/form-data\" action=\"change_pwd.php\" method='post'>\n";

	echo add_token_field();

	echo "<table class='boireaus'>\n";
	echo "<tr class='lig1'>\n";
	echo "<th>Identifiant&nbsp;: </th>\n";
	echo "<td>$user_login</td>\n";
	echo "</tr>\n";

	echo "<tr class='lig-1'>\n";
	echo "<th>Nom&nbsp;: </th>\n";
	echo "<td>$user_nom</td>\n";
	echo "</tr>\n";

	echo "<tr class='lig1'>\n";
	echo "<th>Prénom&nbsp;: </th>\n";
	echo "<td>$user_prenom</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

    echo "<p>Il est fortement conseillé de ne pas choisir un mot de passe trop simple.
    <br /><br /><b>Attention : le mot de passe doit comporter ".getSettingValue("longmin_pwd")." caractères minimum. ";
    if ($flag == 1)
        echo "Il doit comporter au moins une lettre, au moins un chiffre et au moins un caractère spécial (#, *,...)";
    else
        echo "Il doit comporter au moins une lettre et au moins un chiffre.";
    echo "</b></p>\n";
    echo "<br />\n";
	echo "<table summary='Mot de passe'>
	<tr>
		<td>Nouveau mot de passe (<em>".getSettingValue("longmin_pwd")." caractères minimum</em>) : </td>
		<td>
			<input type='password' name='no_anti_inject_password' id='no_anti_inject_password' size='20' />
			".input_password_to_text('no_anti_inject_password')."
		</td>
	</tr>
	<tr>
		<td>Nouveau mot de passe (<em>à confirmer</em>) :</td>
		<td>
			<input type='password' name='reg_password2' id='reg_password2' size='20' />
			".input_password_to_text('reg_password2')."
		</td>
	</tr>
</table>
<input type='hidden' name='valid' value=\"yes\" />
<input type='hidden' name='user_login' value='".$user_login."' />\n";

	echo "<br /><center><input type='submit' value='Enregistrer' /></center>";

	$user_statut = sql_query1("select statut from utilisateurs where login='".$user_login."';");
	if($user_statut=='eleve') {
		$sql="SELECT no_gep FROM eleves WHERE login='$user_login';";
		$res_ine=mysql_query($sql);
		if(mysql_num_rows($res_ine)>0){
			$lig_ine=mysql_fetch_object($res_ine);
			if($lig_ine->no_gep!='') {
				echo "<input type='hidden' name='ine_password' value=\"$lig_ine->no_gep\" />\n";
				echo "<p><input type='checkbox' name='mdp_INE' id='mdp_INE' value='y' /> <label for='mdp_INE' style='cursor:pointer'>Utiliser le numéro national de l'élève (<em>INE</em>) comme mot de passe initial lorsqu'il est renseigné.</label></p>\n";
			}
		}
	}

	echo "</form>\n";
} else {
    echo "<p>Pour des raisons de sécurité, veuillez utiliser le module \"mon compte\" accessible à partir de la page d'accueil pour changer votre mot de passe !</p>";
}
require("../lib/footer.inc.php");
?>
