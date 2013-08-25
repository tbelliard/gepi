<?php
/*
 *
 * Copyright 2001-2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// Begin standart header

// Initialisations files
require_once("../lib/initialisations.inc.php");

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

$msg="";

// Enregistrement de la durée de conservation des données

if (isset($_POST['duree'])) {
	check_token();

    if (!saveSetting(("duree_conservation_logs"), $_POST['duree'])) {
        $msg.= "Erreur lors de l'enregistrement de la durée de conservation des connexions !<br />";
    } else {
        $msg.= "La durée de conservation des connexions a été enregistrée.<br />Le changement sera pris en compte après la prochaine connexion à GEPI.<br />";
    }
}


if (isset($_POST['auth_options_posted']) && $_POST['auth_options_posted'] == "1") {
	check_token();

	if (isset($_POST['auth_sso'])) {
	    if (!in_array($_POST['auth_sso'], array("none","lemon","cas","lcs"))) {
	    	$_POST['auth_sso'] = "none";
	    }
		saveSetting("auth_sso", $_POST['auth_sso']);
	}

	if (isset($_POST['sso_cas_table'])) {
	    if ($_POST['sso_cas_table'] != "yes") {
	    	$_POST['sso_cas_table'] = "no";
	    }
	} else {
		$_POST['sso_cas_table'] = "no";
	}
	saveSetting("sso_cas_table", $_POST['sso_cas_table']);

	if (isset($_POST['auth_locale'])) {
	    if ($_POST['auth_locale'] != "yes") {
	    	$_POST['auth_locale'] = "no";
	    }
	} else {
		$_POST['auth_locale'] = "no";
	}
	saveSetting("auth_locale", $_POST['auth_locale']);

	if (isset($_POST['auth_ldap'])) {
	    if ($_POST['auth_ldap'] != "yes") {
	    	$_POST['auth_ldap'] = "no";
	    }
	} else {
		$_POST['auth_ldap'] = "no";
	}
	saveSetting("auth_ldap", $_POST['auth_ldap']);

	if (isset($_POST['auth_simpleSAML'])) {
	    if ($_POST['auth_simpleSAML'] != "yes") {
	    	$_POST['auth_simpleSAML'] = "no";
	    }
	} else {
		$_POST['auth_simpleSAML'] = "no";
	}
	saveSetting("auth_simpleSAML", $_POST['auth_simpleSAML']);

	if (isset($_POST['auth_simpleSAML_source'])) {
		saveSetting("auth_simpleSAML_source", $_POST['auth_simpleSAML_source']);
	}

	if (isset($_POST['ldap_write_access'])) {
	    if ($_POST['ldap_write_access'] != "yes") {
	    	$_POST['ldap_write_access'] = "no";
	    }
	} else {
		$_POST['ldap_write_access'] = "no";
	}
	saveSetting("ldap_write_access", $_POST['ldap_write_access']);

    	if (isset($_POST['sso_display_portail'])) {
	    if ($_POST['sso_display_portail'] != "yes") {
	    	$_POST['sso_display_portail'] = "no";
	    }
	} else {
		$_POST['sso_display_portail'] = "no";
	}
	saveSetting("sso_display_portail", $_POST['sso_display_portail']);
	
        if (isset($_POST['sso_hide_logout'])) {
	    if ($_POST['sso_hide_logout'] != "yes") {
	    	$_POST['sso_hide_logout'] = "no";
	    }
	} else {
		$_POST['sso_hide_logout'] = "no";
	}
	saveSetting("sso_hide_logout", $_POST['sso_hide_logout']);
    
    
    	if (isset($_POST['sso_url_portail'])) {
	    saveSetting("sso_url_portail", $_POST['sso_url_portail']);
	}
    
    
	if (isset($_POST['may_import_user_profile'])) {
	    if ($_POST['may_import_user_profile'] != "yes") {
	    	$_POST['may_import_user_profile'] = "no";
	    }
	} else {
		$_POST['may_import_user_profile'] = "no";
	}
	saveSetting("may_import_user_profile", $_POST['may_import_user_profile']);

	if (isset($_POST['sso_scribe'])) {
	    if ($_POST['sso_scribe'] != "yes") {
	    	$_POST['sso_scribe'] = "no";
	    }
	} else {
		$_POST['sso_scribe'] = "no";
	}
	saveSetting("sso_scribe", $_POST['sso_scribe']);


	if (isset($_POST['gepiEnableIdpSaml20'])) {
	    if ($_POST['gepiEnableIdpSaml20'] != "yes") {
	    	$_POST['gepiEnableIdpSaml20'] = "no";
	    }
	} else {
		$_POST['gepiEnableIdpSaml20'] = "no";
	}
	saveSetting("gepiEnableIdpSaml20", $_POST['gepiEnableIdpSaml20']);
	
  	if (isset($_POST['sacocheUrl'])) {
		$sacocheUrl = $_POST['sacocheUrl'];
		if (mb_substr($sacocheUrl,mb_strlen($sacocheUrl)-1,1) == '/') {$sacocheUrl = substr($sacocheUrl,0, mb_strlen($sacocheUrl)-1);} //on enleve le / a  la fin
  		saveSetting("sacocheUrl", $_POST['sacocheUrl']);
	}
		
  	if (isset($_POST['sacoche_base'])) {
		saveSetting("sacoche_base", $_POST['sacoche_base']);
	}
		
	if (isset($_POST['statut_utilisateur_defaut'])) {
	    if (!in_array($_POST['statut_utilisateur_defaut'], array("professeur","responsable","eleve"))) {
	    	$_POST['statut_utilisateur_defaut'] = "professeur";
	    }
		saveSetting("statut_utilisateur_defaut", $_POST['statut_utilisateur_defaut']);
	}
	
	if (isset($_POST['login_sso_url'])) {
		saveSetting("login_sso_url", $_POST['login_sso_url']);
	}

  if (isset($_POST['cas_attribut_prenom'])) {
	    saveSetting("cas_attribut_prenom", $_POST['cas_attribut_prenom']);
	}
  if (isset($_POST['cas_attribut_nom'])) {
	    saveSetting("cas_attribut_nom", $_POST['cas_attribut_nom']);
	}
  if (isset($_POST['cas_attribut_email'])) {
	    saveSetting("cas_attribut_email", $_POST['cas_attribut_email']);
	}
}

// Load settings

if (!loadSettings()) {
    die("Erreur chargement settings");
}

// Suppression du journal de connexion

if (isset($_POST['valid_sup_logs']) ) {
	check_token();

    $sql = "delete from log where END < now()";
    $res = sql_query($sql);
    if ($res) {
       $msg.= "La suppression des entrées dans le journal de connexion a été effectuée.<br />";
    } else {
       $msg.= "Il y a eu un problème lors de la suppression des entrées dans le journal de connexion.<br />";
    }
}

// Changement de mot de passe obligatoire
if (isset($_POST['valid_chgt_mdp'])) {
	check_token();

	if ((!$session_gepi->auth_ldap && !$session_gepi->auth_sso) || getSettingValue("ldap_write_access")) {
    	$sql = "UPDATE utilisateurs SET change_mdp='y' where login != '".$_SESSION['login']."'";
	} else {
		$sql = "UPDATE utilisateurs SET change_mdp='y' WHERE (login != '".$_SESSION['login']."' AND auth_mode != 'ldap' AND auth_mode != 'sso')";
	}

    $res = sql_query($sql);
    if ($res) {
       $msg.= "La demande de changement obligatoire de mot de passe a été enregistrée.<br />";
    } else {
       $msg.= "Il y a eu un problème lors de l'enregistrement de la demande de changement obligatoire de mot de passe.<br />";
    }
}


//Activation / désactivation de la procédure de réinitialisation du mot de passe par email
if (isset($_POST['enable_password_recovery'])) {
	check_token();

    if (!saveSetting("enable_password_recovery", $_POST['enable_password_recovery'])) {
        $msg.= "Il y a eu un problème lors de l'enregistrement du paramètre d'activation/désactivation de la procédure de récupération automatisée des mots de passe.<br />";
    } else {
        $msg.= "L'enregistrement du paramètre d'activation/désactivation de la procédure de récupération automatisée des mots de passe a été effectué avec succès.<br />";
    }
}


if (isset($_POST['GepiResp_obtenir_compte_et_motdepasse'])) {
	check_token();

    if (!saveSetting("GepiResp_obtenir_compte_et_motdepasse", $_POST['GepiResp_obtenir_compte_et_motdepasse'])) {
        $msg.= "Il y a eu un problème lors de l'enregistrement du paramètre d'activation/désactivation de la procédure de demande de compte/mot de passe.<br />";
    } else {
        $msg.= "L'enregistrement du paramètre d'activation/désactivation de la procédure de demande de compte/mot de passe a été effectué avec succès.<br />";
    }

	if (isset($_POST['SendMail_obtenir_compte_et_motdepasse'])) {
		if (!saveSetting("SendMail_obtenir_compte_et_motdepasse", $_POST['SendMail_obtenir_compte_et_motdepasse'])) {
		    $msg.= "Il y a eu un problème lors de l'enregistrement du paramètre d'envoi de mail dans la procédure de demande de compte/mot de passe.<br />";
		} else {
		    $msg.= "L'enregistrement du paramètre d'envoi de mail dans la procédure de demande de compte/mot de passe a été effectué avec succès.<br />";
		}
	}

	if (isset($_POST['DestMail_obtenir_compte_et_motdepasse'])) {
		if (!saveSetting("DestMail_obtenir_compte_et_motdepasse", $_POST['DestMail_obtenir_compte_et_motdepasse'])) {
		    $msg.= "Il y a eu un problème lors de l'enregistrement du paramètre destinataire du mail dans la procédure de demande de compte/mot de passe.<br />";
		} else {
		    $msg.= "L'enregistrement du paramètre destinataire du dans la procédure de demande de compte/mot de passe a été effectué avec succès.<br />";
		}
	}

	if (isset($_POST['RegBaseAdm_obtenir_compte_et_motdepasse'])) {
		$RegBaseAdm_obtenir_compte_et_motdepasse="yes";
	}
	else {
		$RegBaseAdm_obtenir_compte_et_motdepasse="no";
	}
	if (!saveSetting("RegBaseAdm_obtenir_compte_et_motdepasse", $RegBaseAdm_obtenir_compte_et_motdepasse)) {
	    $msg.= "Il y a eu un problème lors de l'enregistrement du paramètre RegBaseAdm_obtenir_compte_et_motdepasse.<br />";
	} else {
	    $msg.= "L'enregistrement du paramètre RegBaseAdm_obtenir_compte_et_motdepasse a été effectué avec succès.<br />";
	}

	if (isset($_POST['RegBaseScol_obtenir_compte_et_motdepasse'])) {
		$RegBaseScol_obtenir_compte_et_motdepasse="yes";
	}
	else {
		$RegBaseScol_obtenir_compte_et_motdepasse="no";
	}
	if (!saveSetting("RegBaseScol_obtenir_compte_et_motdepasse", $RegBaseScol_obtenir_compte_et_motdepasse)) {
	    $msg.= "Il y a eu un problème lors de l'enregistrement du paramètre RegBaseScol_obtenir_compte_et_motdepasse.<br />";
	} else {
	    $msg.= "L'enregistrement du paramètre RegBaseScol_obtenir_compte_et_motdepasse a été effectué avec succès.<br />";
	}

	if (isset($_POST['RegBaseCpe_obtenir_compte_et_motdepasse'])) {
		$RegBaseCpe_obtenir_compte_et_motdepasse="yes";
	}
	else {
		$RegBaseCpe_obtenir_compte_et_motdepasse="no";
	}
	if (!saveSetting("RegBaseCpe_obtenir_compte_et_motdepasse", $RegBaseCpe_obtenir_compte_et_motdepasse)) {
	    $msg.= "Il y a eu un problème lors de l'enregistrement du paramètre RegBaseCpe_obtenir_compte_et_motdepasse.<br />";
	} else {
	    $msg.= "L'enregistrement du paramètre RegBaseCpe_obtenir_compte_et_motdepasse a été effectué avec succès.<br />";
	}
}

// End standart header
//=====================================
$titre_page = "Options de connexion";
require_once("../lib/header.inc.php");
//=====================================
isset($mode_navig);
$mode_navig = isset($_POST["mode_navig"]) ? $_POST["mode_navig"] : (isset($_GET["mode_navig"]) ? $_GET["mode_navig"] : NULL);
if ($mode_navig == 'accueil') {
    $retour = "../accueil.php";
} else {
    $retour = "index.php#options_connect";
}

echo "<p class=bold><a href=\"".$retour."\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>\n";


//
// Activation/désactivation de la procédure de récupération du mot de passe
//
echo "<h3 class='gepi'>Mots de passe perdus</h3>\n";
echo "<form action=\"options_connect.php\" method=\"post\">\n";
echo add_token_field();
echo "<input type='radio' name='enable_password_recovery' value='no' id='label_1b'";
if (getSettingValue("enable_password_recovery")=='no') echo " checked ";
echo " /> <label for='label_1b' style='cursor: pointer;'>Désactiver la procédure automatisée de récupération de mot de passe</label>\n";

echo "<br /><input type='radio' name='enable_password_recovery' value='yes' id='label_2b'";
if (getSettingValue("enable_password_recovery")=='yes') echo " checked ";
echo " /> <label for='label_2b' style='cursor: pointer;'>Activer la procédure automatisée de récupération de mot de passe</label>\n";

echo "<center><input type=\"submit\" value=\"Valider\" /></center>\n";
echo "</form>\n";

echo"<hr class=\"header\" style=\"margin-top: 32px; margin-bottom: 24px;\"/>\n";

//
// Activation/désactivation de la procédure de demande de compte/mot de passe
//
echo "<h3 class='gepi'>Demande de compte et mot de passe</h3>\n";
echo "<form action=\"options_connect.php\" method=\"post\">\n";
echo add_token_field();
echo "<p>Permettre aux responsables de demander ou récupérer un compte/mot de passe.<br />\n";
echo "<input type='radio' name='GepiResp_obtenir_compte_et_motdepasse' value='no' id='GepiResp_obtenir_compte_et_motdepasse_no'";
if (!getSettingAOui("GepiResp_obtenir_compte_et_motdepasse")) echo " checked ";
echo " /> <label for='GepiResp_obtenir_compte_et_motdepasse_no' style='cursor: pointer;'>Désactiver l'accès au formulaire de demande de compte/mot de passe pour les responsables</label>\n";

echo "<br /><input type='radio' name='GepiResp_obtenir_compte_et_motdepasse' value='yes' id='GepiResp_obtenir_compte_et_motdepasse_yes'";
if (getSettingAOui("GepiResp_obtenir_compte_et_motdepasse")) echo " checked ";
echo " /> <label for='GepiResp_obtenir_compte_et_motdepasse_yes' style='cursor: pointer;'>Activer l'accès au formulaire de demande de compte/mot de passe pour les responsables</label></p>\n";

echo "<br />
<p>Envoyer un courriel à l'adresse suivante quand un responsable formule une demande&nbsp;:<br />
<input type='radio' name='SendMail_obtenir_compte_et_motdepasse' value='no' id='SendMail_obtenir_compte_et_motdepasse_no'";
if (!getSettingAOui("SendMail_obtenir_compte_et_motdepasse")) echo " checked ";
echo " /> <label for='SendMail_obtenir_compte_et_motdepasse_no' style='cursor: pointer;'>Non</label>
<br /><input type='radio' name='SendMail_obtenir_compte_et_motdepasse' value='yes' id='SendMail_obtenir_compte_et_motdepasse_yes'";
if (getSettingAOui("SendMail_obtenir_compte_et_motdepasse")) echo " checked ";
echo " /> <label for='SendMail_obtenir_compte_et_motdepasse_yes' style='cursor: pointer;'>Oui</label><br />
à destination de <input type='text' name='DestMail_obtenir_compte_et_motdepasse' value='";
if(getSettingValue('DestMail_obtenir_compte_et_motdepasse')!='') {echo getSettingValue('DestMail_obtenir_compte_et_motdepasse');} else {echo getSettingValue('gepiSchoolEmail');}
echo "' /></p>\n";

echo "<br />
<p>Enregistrer la demande dans la base et la faire apparaitre en page d'accueil pour le/les statuts suivants&nbsp;:<br />
<input type='checkbox' name='RegBaseAdm_obtenir_compte_et_motdepasse' value='yes' id='RegBaseAdm_obtenir_compte_et_motdepasse'";
if (getSettingAOui("RegBaseAdm_obtenir_compte_et_motdepasse")) echo " checked ";
echo " /> <label for='RegBaseAdm_obtenir_compte_et_motdepasse' style='cursor: pointer;'>administrateur</label>
<br />
<input type='checkbox' name='RegBaseScol_obtenir_compte_et_motdepasse' value='yes' id='RegBaseScol_obtenir_compte_et_motdepasse'";
if (getSettingAOui("RegBaseScol_obtenir_compte_et_motdepasse")) echo " checked ";
echo " /> <label for='RegBaseScol_obtenir_compte_et_motdepasse' style='cursor: pointer;'>scolarité</label>
<br />
<input type='checkbox' name='RegBaseCpe_obtenir_compte_et_motdepasse' value='yes' id='RegBaseCpe_obtenir_compte_et_motdepasse'";
if (getSettingAOui("RegBaseCpe_obtenir_compte_et_motdepasse")) echo " checked ";
echo " /> <label for='RegBaseCpe_obtenir_compte_et_motdepasse' style='cursor: pointer;'>cpe</label>
</p>\n";

echo "<center><input type=\"submit\" value=\"Valider\" /></center>\n";
echo "</form>\n";

echo "<p style='text-indent:-5em; margin-left:5em;'><em>NOTES&nbsp;:</em> Le responsable dispose en page de login d'un lien pour remplir un formulaire avec nom, prénom, email et indication sur le nom, prénom et classe d'un des enfants.<br />
Le document doit être imprimé et déposé à l'Administration pour finaliser la demande.<br />
Cette précaution est destinée à éviter des usurpations d'identité.</p>\n";

echo"<hr class=\"header\" style=\"margin-top: 32px; margin-bottom: 24px;\"/>\n";

//
// Changement du mot de passe obligatoire
//
// Cette option n'est proposée que si les mots de passe sont éditables dans Gepi
//
if ($session_gepi->auth_locale ||
		(($session_gepi->auth_ldap || $session_gepi->auth_sso)
				&& getSettingValue("ldap_write_access") == "yes")) {
echo "<h3 class='gepi'>Changement du mot de passe obligatoire lors de la prochaine connexion</h3>\n";
echo "<p><b>ATTENTION : </b>En validant le bouton ci-dessous, <b>tous les utilisateurs</b> dont le mot de passe est éditable par Gepi (les utilisateurs locaux, ou bien tous les utilisateurs si un accès LDAP en écriture a été configuré) seront amenés à changer leur mot de passe lors de leur prochaine connexion.</p>\n";
echo "<form action=\"options_connect.php\" name=\"form_chgt_mdp\" method=\"post\">\n";
echo add_token_field();
echo "<center><input type=\"submit\" name=\"valid_chgt_mdp\" value=\"Valider\" onclick=\"return confirmlink(this, 'Êtes-vous sûr de vouloir forcer le changement de mot de passe de tous les utilisateurs ?', 'Confirmation')\" /></center>\n";
echo "<input type=hidden name=mode_navig value='$mode_navig' />\n";
echo "</form><hr class=\"header\" style=\"margin-top: 32px; margin-bottom: 24px;\"/>\n";
}

//
// Paramétrage du Single Sign-On
//

echo "<h3 class='gepi'>Mode d'authentification</h3>\n";
echo "<p><span style='color: red'><strong>Attention !</strong></span> Ne modifiez ces paramètres que si vous savez vraiment ce que vous faites ! Si vous activez l'authentification SSO et que vous ne pouvez plus vous connecter à Gepi en administrateur, vous pouvez utiliser la variable \$block_sso dans le fichier /lib/global.inc pour désactiver le SSO et rebasculer en authentification locale. Il est donc vivement recommandé de créer un compte administrateur local (<em>dont le login n'interfèrera pas avec un login SSO</em>) avant d'activer le SSO.</p>\n";
echo "<p>Gepi permet d'utiliser plusieurs modes d'authentification en parallèle. Les combinaisons les plus courantes seront une authentification locale avec une authentification LDAP, ou bien une authentification locale et une authentification unique (<em>utilisant un serveur d'authentification distinct</em>).</p>\n";
echo "<p>Le mode d'authentification est explicitement spécifié pour chaque utilisateur dans la base de données de Gepi. Assurez-vous que le mode défini correspond effectivement au mode utilisé par l'utilisateur.</p>\n";
echo "<p>Dans le cas d'une authentification externe (<em>LDAP ou SSO</em>), aucun mot de passe n'est stocké dans la base de données de Gepi.</p>\n";
echo "<p>Si vous paramétrez un accès LDAP en écriture, les mots de passe des utilisateurs pourront être modifiés directement à travers Gepi, même pour les modes LDAP et SSO. L'administrateur pourra également éditer les données de base de l'utilisateur (<em>nom, prénom, email</em>). Lorsque vous activez l'accès LDAP en écriture, assurez-vous que le paramétrage sur le serveur LDAP permet à l'utilisateur de connexion LDAP de modifier les champs login, mot de passe, nom, prénom et email.</p>\n";
echo "<p>Si vous utilisez CAS, vous devez entrer les informations de configuration du serveur CAS dans le fichier /secure/config_cas.inc.php (<em>un modèle de configuration se trouve dans le fichier /secure/modeles/config_cas-modele.inc.php</em>).</p>\n";
echo "<p>Si vous utilisez l'authentification sur serveur LDAP, ou bien que vous activez l'accès LDAP en écriture, vous devez renseigner le fichier /secure/config_ldap.inc.php avec les informations nécessaires pour se connecter au serveur (<em>un modèle se trouve dans /secure/modeles/config_ldap-modele.inc.php</em>).</p>\n";
echo "<form action=\"options_connect.php\" name=\"form_auth\" method=\"post\">\n";
echo add_token_field();

echo "<p><strong>Modes d'authentification :</strong></p>\n";
echo "<p><input type='checkbox' name='auth_locale' value='yes' id='label_auth_locale'";
if (getSettingValue("auth_locale")=='yes') echo " checked ";
echo " /> <label for='label_auth_locale' style='cursor: pointer;'>Authentification autonome (sur la base de données de Gepi)</label>\n";

$ldap_setup_valid = LDAPServer::is_setup();
echo "<br/><input type='checkbox' name='auth_ldap' value='yes' id='label_auth_ldap'";
if (getSettingValue("auth_ldap")=='yes' && $ldap_setup_valid) echo " checked ";
if (!$ldap_setup_valid) echo " disabled";
echo " /> <label for='label_auth_ldap' style='cursor: pointer;'>Authentification LDAP";
if (!$ldap_setup_valid) echo " <em>(sélection impossible : le fichier /secure/config_ldap.inc.php n'est pas présent)</em>\n";
echo "</label>\n";


//on va voir si il y a simplesaml de configuré
if (file_exists(dirname(__FILE__).'/../lib/simplesaml/config/authsources.php')) {
	echo "<br/><input type='checkbox' name='auth_simpleSAML' value='yes' id='label_auth_simpleSAML'";
	if (getSettingValue("auth_simpleSAML")=='yes') echo " checked ";
	echo " /> <label for='label_auth_simpleSAML' style='cursor: pointer;'>Authentification simpleSAML";
	echo "</label>\n";
	
	echo "<br/>\n<select name=\"auth_simpleSAML_source\" size=\"1\">\n";
	echo "<option value='unset'></option>";
	include_once(dirname(__FILE__).'/../lib/simplesaml/lib/_autoload.php');
	$config = SimpleSAML_Configuration::getOptionalConfig('authsources.php');
	$sources = $config->getOptions();
	foreach($sources as $source) {
		echo "<option value='$source'";
		if ($source == getSettingValue("auth_simpleSAML_source")) {
			echo 'selected';
		}
		echo ">";
		echo $source;
		echo "</option>";
	}
	echo "</select>\n";
} else  {
	echo "<input type='hidden' name='auth_simpleSAML' value='no' />";
}
echo "</p>\n";

echo "<p>Service d'authentification unique : ";

echo "<br/><input type='radio' name='auth_sso' value='none' id='no_sso'";
if (getSettingValue("auth_sso")=='none') echo " checked ";
echo " /> <label for='no_sso' style='cursor: pointer;'>Non utilisé</label>\n";

$lcs_setup_valid = file_exists("../secure/config_lcs.inc.php") ? true : false;
echo "<br/><input type='radio' name='auth_sso' value='lcs' id='lcs'";
if (getSettingValue("auth_sso")=='lcs' && $lcs_setup_valid) echo " checked ";
if (!$lcs_setup_valid) echo " disabled";
echo " /> <label for='lcs' style='cursor: pointer;'>LCS";
if (!$lcs_setup_valid) echo " <em>(sélection impossible : le fichier /secure/config_lcs.inc.php n'est pas présent)</em>\n";
echo "</label>\n";

$cas_setup_valid = file_exists("../secure/config_cas.inc.php") ? true : false;
echo "<br /><input type='radio' name='auth_sso' value='cas' id='label_2'";
if (getSettingValue("auth_sso")=='cas' && $cas_setup_valid) echo " checked ";
if (!$cas_setup_valid) echo " disabled";
echo " /> <label for='label_2' style='cursor: pointer;'>CAS";
if (!$cas_setup_valid) echo " <em>(sélection impossible : le fichier /secure/config_cas.inc.php n'est pas présent)</em>\n";
echo "</label>\n";


echo "<br /><input type='radio' name='auth_sso' value='lemon' id='label_3'";
if (getSettingValue("auth_sso")=='lemon') echo " checked ";
echo " /> <label for='label_3' style='cursor: pointer;'>LemonLDAP</label>\n";
echo "</p>\n";
echo "<p><em>Remarque&nbsp;:</em> les changements n'affectent pas les sessions en cours.";

//on va voir si il y a simplesaml de configuré
if (file_exists(dirname(__FILE__).'/../lib/simplesaml/metadata/saml20-idp-hosted.php')) {
	echo "<p><strong>Fourniture d'identité :</strong></p>\n";
	echo "<p><input type='checkbox' name='gepiEnableIdpSaml20' value='yes' id='gepiEnableIdpSaml20'";
	if (getSettingValue("gepiEnableIdpSaml20")=='yes') echo " checked ";
	echo " /> <label for='gepiEnableIdpSaml20' style='cursor: pointer;'>Fournir une identification SAML 2.0</label>\n";
	echo "<p>\n";
	echo "<label for='sacocheUrl' style='cursor: pointer;'>Adresse du service qui va se connecter si possible en https (<em>exemple : https://localhost/mon-appli</em>) </label>\n";
	echo "<input type='text' size='60' name='sacocheUrl' value='".getSettingValue("sacocheUrl")."' id='sacocheUrl' />\n<br/>";
	echo "<label for='sacoche_base' style='cursor: pointer;'>Numéro de base sacoche (<em>laisser vide si votre instalation de sacoche est mono établissement</em>)</label>\n";
	echo "<input type='text' size='5' name='sacoche_base' value='".getSettingValue("sacoche_base")."' id='sacoche_base' />\n<br/>";
	echo 'pour une configuration manuelle, modifier le fichier /lib/simplesaml/metadate/saml20-sp-remote.php';
        try {
            require_once('../lib/simplesaml/lib/_autoload.php');
            $config = SimpleSAML_Configuration::getConfig();
            //$cert_file =SimpleSAML_Utilities::resolveCert('server.crt');
            $metadata = SimpleSAML_Metadata_MetaDataStorageHandler::getMetadataHandler();
            $spMetadata = $metadata->getMetaDataConfig('gepi-idp', 'saml20-idp-hosted');
            if ($spMetadata != null) {
                $cert_file =SimpleSAML_Utilities::resolveCert($spMetadata->getString('certificate'));
                $x509cert = file_get_contents($cert_file);
                echo '<br/>Empreinte (fingerprint) du certificat x509 (à renseigner dans votre logiciel founisseur de service) : ';
                $lines = explode("\n", $x509cert);

                $data = '';

                foreach($lines as $line) {
                        /* Remove '\r' from end of line if present. */
                        $line = rtrim($line);
                        if($line === '-----BEGIN CERTIFICATE-----') {
                                /* Delete junk from before the certificate. */
                                $data = '';
                        } elseif($line === '-----END CERTIFICATE-----') {
                                /* Ignore data after the certificate. */
                                break;
                        } elseif($line === '-----BEGIN PUBLIC KEY-----') {
                                /* This isn't an X509 certificate. */
                                return NULL;
                        } else {
                                /* Append the current line to the certificate data. */
                                $data .= $line;
                        }
                }

                /* $data now contains the certificate as a base64-encoded string. The fingerprint
                    * of the certificate is the sha1-hash of the certificate.
                    */
                echo strtolower(sha1(base64_decode($data)));
            }
        } catch (Exception $e) {
            echo '<br/>Impossible d\'afficher l\'empreinte (fingerprint) du certificat : '.$e->getMessage();
        }
        echo "</p>\n";
        
}


echo "<p><strong>Options supplémentaires :</strong></p>\n";

echo "<p><input type='checkbox' name='may_import_user_profile' value='yes' id='label_import_user_profile'";
if (getSettingValue("may_import_user_profile")=='yes' && $ldap_setup_valid) echo " checked ";
if (!$ldap_setup_valid) echo " disabled";
echo " /> <label for='label_import_user_profile' style='cursor: pointer;'>Import à la volée des comptes utilisateurs authentifiés correctement (en LDAP ou SSO).";
if (!$ldap_setup_valid) echo " <em>(sélection impossible : le fichier /secure/config_ldap.inc.php n'est pas présent)</em>\n";
echo "</label>\n";
echo "</p>\n";

echo "<p><input type='checkbox' name='sso_scribe' value='yes' id='label_sso_scribe'";
if (getSettingValue("sso_scribe")=='yes' && $ldap_setup_valid) echo " checked ";
if (!$ldap_setup_valid) echo " disabled";
echo " /> <label for='label_sso_scribe' style='cursor: pointer;'>Utilisation avec l'annuaire LDAP de Scribe NG, versions 2.2 et supérieures (<em>permet l'import à la volée de données plus complètes lorsque cet ENT est utilisé et que l'option 'Import à la volée', ci-dessus, est cochée</em>).";
if (!$ldap_setup_valid) echo " <em>(sélection impossible : le fichier /secure/config_ldap.inc.php n'est pas présent)</em>\n";
echo "</label>\n";
echo "</p>\n";

echo "<p>Statut par défaut appliqué en cas d'impossibilité de déterminer le statut lors de l'import :";
echo "<br/>\n<select name=\"statut_utilisateur_defaut\" size=\"1\">\n";
echo "<option ";
if(isset($gepiSettings['statut_utilisateur_defaut'])) {$statut_defaut = $gepiSettings['statut_utilisateur_defaut'];}else {$statut_defaut="professeur";}
if ($statut_defaut == "professeur") echo "selected";
echo " value='professeur'>Professeur</option>\n";
echo "<option ";
if ($statut_defaut == "eleve") echo "selected";
echo " value='eleve'>Élève</option>\n";
echo "<option ";
if ($statut_defaut == "responsable") echo "selected";
echo " value='responsable'>Responsable légal</option>\n";
echo "</select>\n";
echo "</p>\n";

echo "<p><input type='checkbox' name='ldap_write_access' value='yes' id='label_ldap_write_access'";
if (getSettingValue("ldap_write_access")=='yes' && $ldap_setup_valid) echo " checked ";
if (!$ldap_setup_valid) echo " disabled";
echo " /> <label for='label_ldap_write_access' style='cursor: pointer;'>Accès LDAP en écriture.";
if (!$ldap_setup_valid) echo " <em>(sélection impossible : le fichier /secure/config_ldap.inc.php n'est pas présent)</em>\n";
echo "</label>\n";
echo "</p>\n";

echo "<p><input type='checkbox' name='sso_display_portail' value='yes' id='label_sso_display_portail'";
if ($gepiSettings['sso_display_portail'] == 'yes') echo " checked ";
echo " /> <label for='label_sso_display_portail' style='cursor: pointer;'>Sessions SSO uniquement : afficher un lien vers un portail (<em>vous devez renseigner le champ ci-dessous</em>).";
echo "</label>\n";
echo "</p>\n";

echo "<p>\n";
echo "<label for='label_sso_url_portail' style='cursor: pointer;'>Adresse complète du portail : </label>\n";
echo "<input type='text' size='60' name='sso_url_portail' value='".$gepiSettings['sso_url_portail']."' id='label_sso_url_portail' />\n";
echo "</p>\n";

echo "<p><input type='checkbox' name='sso_hide_logout' value='yes' id='label_sso_hide_logout'";
if ($gepiSettings['sso_hide_logout'] == 'yes') echo " checked='checked' ";
echo " /> <label for='label_sso_hide_logout' style='cursor: pointer;'>Sessions SSO uniquement : masquer le lien de déconnexion (<em>soyez sûr que l'utilisateur dispose alors d'un moyen alternatif de se déconnecter</em>).";
echo "</label>\n";
echo "</p>\n";

echo "<p>SSO CAS uniquement : import automatique d'attributs supplémentaires</p>";
echo "<p>Si les champs ci-dessous sont renseignés, Gepi essaiera systématiquement de mettre à jour les informations de l'utilisateur à partir des attributs transmis par le serveur CAS.</p>";

echo "<label for='cas_attribut_prenom' style='cursor: pointer;'>Attribut 'prénom'</label>\n";
echo "<p><input type='text' size='20' name='cas_attribut_prenom' value='".getSettingValue('cas_attribut_prenom')."' id='cas_attribut_prenom'/>";
echo "</p>\n";

echo "<label for='cas_attribut_nom' style='cursor: pointer;'>Attribut 'nom'</label>\n";
echo "<p><input type='text' size='20' name='cas_attribut_nom' value='".getSettingValue('cas_attribut_nom')."' id='cas_attribut_nom'/>";
echo "</p>\n";

echo "<label for='cas_attribut_email' style='cursor: pointer;'>Attribut 'email'</label>\n";
echo "<p><input type='text' size='20' name='cas_attribut_email' value='".getSettingValue('cas_attribut_email')."' id='cas_attribut_email'/>";
echo "</p>\n";

echo "<br/>\n";
echo "<p>\n";
echo "<label for='login_sso_url' style='cursor: pointer;'>Fichier d'identification SSO alternatif (<em>à utiliser à la place de login_sso.php</em>) : </label>\n";
echo "<input type='text' size='60' name='login_sso_url' value='".getSettingValue('login_sso_url')."' id='login_sso_url' />\n";

echo "</p>\n";

echo "<br/>\n";

echo "<p><input type='checkbox' name='sso_cas_table' value='yes' id='sso_cas_table'";
if ($gepiSettings['sso_cas_table'] == 'yes') echo " checked='checked' ";
echo " /> <label for='sso_cas_table' style='cursor: pointer;'>Sessions SSO CAS uniquement : utiliser une table de correspondance .";
echo "</label>\n";
echo "</p>\n";

echo "<center><input type=\"submit\" name=\"auth_mode_submit\" value=\"Valider\" onclick=\"return confirmlink(this, 'Êtes-vous sûr de vouloir changer le mode d\' authentification ?', 'Confirmation')\" /></center>\n";

echo "<input type='hidden' name='auth_options_posted' value='1' />\n";
echo "<input type=hidden name=mode_navig value='$mode_navig' />\n";

echo "</form>



<hr class=\"header\" style=\"margin-top: 32px; margin-bottom: 24px;\" />\n";



//
// Durée de conservation des logs
//
echo "<h3 class='gepi'>Durée de conservation des connexions</h3>\n";
echo "<p>Conformément à la loi loi informatique et liberté 78-17 du 6 janvier 1978, la durée de conservation de ces données doit être déterminée et proportionnée aux finalités de leur traitement.
Cependant par sécurité, il est conseillé de conserver une trace des connexions sur un laps de temps suffisamment long.
</p>\n";
echo "<form action=\"options_connect.php\" name=\"form_chgt_duree\" method=\"post\">\n";
echo add_token_field();
echo "Durée de conservation des informations sur les connexions : <select name=\"duree\" size=\"1\">\n";
echo "<option ";
$duree = getSettingValue("duree_conservation_logs");
if ($duree == 30) echo "selected";
echo " value=30>Un mois</option>\n";
echo "<option ";
if ($duree == 60) echo "selected";
echo " value=60>Deux mois</option>\n";
echo "<option ";
if ($duree == 183) echo "selected";
echo " value=183>Six mois</option>\n";
echo "<option ";
if ($duree == 365) echo "selected";
echo " value=365>Un an</option>\n";
echo "</select>\n";
echo "<input type=\"submit\" name=\"Valider\" value=\"Enregistrer\" />\n";
echo "<input type=hidden name=mode_navig value='$mode_navig' />\n";
echo "</form>\n";
//
// Nettoyage du journal
//
?>
<hr class="header" style="margin-top: 32px; margin-bottom: 24px;"/>
<h3 class='gepi'>Suppression de toutes les entrées du journal de connexion</h3>
<?php
$sql = "select START from log order by END";
$res = sql_query($sql);
$logs_number = sql_count($res);
$row = sql_row($res, 0);
$annee = mb_substr($row[0],0,4);
$mois =  mb_substr($row[0],5,2);
$jour =  mb_substr($row[0],8,2);
echo "<p>Nombre d'entrées actuellement présentes dans le journal de connexion : <b>".$logs_number."</b><br />\n";
echo "Actuellement, le journal contient l'historique des connexions depuis le <b>".$jour."/".$mois."/".$annee."</b></p>\n";
echo "<p><b>ATTENTION : </b>En validant le bouton ci-dessous, <b>toutes les entrées du journal de connexion (hormis les connexions en cours) seront supprimées</b>.</p>\n";
echo "<form action=\"options_connect.php\" name=\"form_sup_logs\" method=\"post\">\n";
echo add_token_field();
echo "<center><input type=\"submit\" name=\"valid_sup_logs\" value=\"Valider\" onclick=\"return confirmlink(this, 'Êtes-vous sûr de vouloir supprimer tout l\'historique du journal de connexion ?', 'Confirmation')\" /></center>\n";
echo "<input type=hidden name=mode_navig value='$mode_navig' />\n";
echo "</form><br/>\n";

require("../lib/footer.inc.php");
?>
