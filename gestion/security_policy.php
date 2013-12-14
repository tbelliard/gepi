<?php
/*
 *
 * Copyright 2001-2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// Enregistrement des données postées

if (isset($_POST) and !empty($_POST)) {
	check_token();

	// Envoyer un email à l'administrateur systématiquement
	if (isset($_POST['security_alert_email_admin'])) {
		$reg = "yes";
	} else {
		$reg = "no";
	}
	if (!saveSetting(("security_alert_email_admin"), $reg)) {
		$msg = "Erreur lors de l'enregistrement de security_alert_email_admin !";
	}

	// Niveau minimal pour l'envoi du mail
	if (isset($_POST['security_alert_email_min_level'])) {
		$reg = $_POST['security_alert_email_min_level'];
		if (!is_numeric($reg)) $reg = 1;
		if (!saveSetting(("security_alert_email_min_level"), $reg)) {
			$msg = "Erreur lors de l'enregistrement de security_alert_email_min_level !";
		}
	}

	// Niveau d'alerte 1
	
	// Utilisateur sans antécédent
	
	// Seuil
	if (isset($_POST['security_alert1_normal_cumulated_level'])) {
		$reg = $_POST['security_alert1_normal_cumulated_level'];
		if (!is_numeric($reg)) $reg = 1;
		if (!saveSetting(("security_alert1_normal_cumulated_level"), $reg)) {
			$msg = "Erreur lors de l'enregistrement de security_alert1_normal_cumulated_level !";
		}
	}

	// Envoyer un email à l'administrateur
	if (isset($_POST['security_alert1_normal_email_admin'])) {
		$reg = "yes";
	} else {
		$reg = "no";
	}
    if (!saveSetting(("security_alert1_normal_email_admin"), $reg)) {
        $msg = "Erreur lors de l'enregistrement de security_alert1_normal_email_admin !";
    }

	// Désactiver le compte de l'utilisateur
	if (isset($_POST['security_alert1_normal_block_user'])) {
		$reg = "yes";
	} else {
		$reg = "no";
	}
    if (!saveSetting(("security_alert1_normal_block_user"), $reg)) {
        $msg = "Erreur lors de l'enregistrement de security_alert1_normal_block_user !";
    }

	// Utilisateur surveillé
	
	// Seuil
	if (isset($_POST['security_alert1_probation_cumulated_level'])) {
		$reg = $_POST['security_alert1_probation_cumulated_level'];
		if (!is_numeric($reg)) $reg = 1;
		if (!saveSetting(("security_alert1_probation_cumulated_level"), $reg)) {
			$msg = "Erreur lors de l'enregistrement de security_alert1_probation_cumulated_level !";
		}
	}

	// Envoyer un email à l'administrateur
	if (isset($_POST['security_alert1_probation_email_admin'])) {
		$reg = "yes";
	} else {
		$reg = "no";
	}
    if (!saveSetting(("security_alert1_probation_email_admin"), $reg)) {
        $msg = "Erreur lors de l'enregistrement de security_alert1_probation_email_admin !";
    }

	// Désactiver le compte de l'utilisateur
	if (isset($_POST['security_alert1_probation_block_user'])) {
		$reg = "yes";
	} else {
		$reg = "no";
	}
    if (!saveSetting(("security_alert1_probation_block_user"), $reg)) {
        $msg = "Erreur lors de l'enregistrement de security_alert1_probation_block_user !";
    }

	// Niveau d'alerte 2
	
	// Utilisateur sans antécédent
	
	// Seuil
	if (isset($_POST['security_alert2_normal_cumulated_level'])) {
		$reg = $_POST['security_alert2_normal_cumulated_level'];
		if (!is_numeric($reg)) $reg = 1;
		if (!saveSetting(("security_alert2_normal_cumulated_level"), $reg)) {
			$msg = "Erreur lors de l'enregistrement de security_alert2_normal_cumulated_level !";
		}
	}
	
	// Envoyer un email à l'administrateur
	if (isset($_POST['security_alert2_normal_email_admin'])) {
		$reg = "yes";
	} else {
		$reg = "no";
	}
    if (!saveSetting(("security_alert2_normal_email_admin"), $reg)) {
        $msg = "Erreur lors de l'enregistrement de security_alert2_normal_email_admin !";
    }

	// Désactiver le compte de l'utilisateur
	if (isset($_POST['security_alert2_normal_block_user'])) {
		$reg = "yes";
	} else {
		$reg = "no";
	}
    if (!saveSetting(("security_alert2_normal_block_user"), $reg)) {
        $msg = "Erreur lors de l'enregistrement de security_alert2_normal_block_user !";
    }

	// Utilisateur surveillé
	
	// Seuil
	if (isset($_POST['security_alert2_probation_cumulated_level'])) {
		$reg = $_POST['security_alert2_probation_cumulated_level'];
		if (!is_numeric($reg)) $reg = 1;
		if (!saveSetting(("security_alert2_probation_cumulated_level"), $reg)) {
			$msg = "Erreur lors de l'enregistrement de security_alert2_probation_cumulated_level !";
		}
	}
	
	// Envoyer un email à l'administrateur
	if (isset($_POST['security_alert2_probation_email_admin'])) {
		$reg = "yes";
	} else {
		$reg = "no";
	}
    if (!saveSetting(("security_alert2_probation_email_admin"), $reg)) {
        $msg = "Erreur lors de l'enregistrement de security_alert2_probation_email_admin !";
    }

	// Désactiver le compte de l'utilisateur
	if (isset($_POST['security_alert2_probation_block_user'])) {
		$reg = "yes";
	} else {
		$reg = "no";
	}
    if (!saveSetting(("security_alert2_probation_block_user"), $reg)) {
        $msg = "Erreur lors de l'enregistrement de security_alert2_probation_block_user !";
    }

	if (empty($msg)) {
		$msg = "Les données ont bien été enregistrées.";
	}

}

if (isset($_POST['csrf_mode'])) {
	check_token();

	if (!saveSetting(("csrf_mode"), $_POST['csrf_mode'])) {
		$msg = "Erreur lors de l'enregistrement de csrf_mode !";
	}
	else {
		$sql="SELECT * FROM infos_actions WHERE titre='Paramétrage csrf_mode requis';";
		$res_test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_test)>0) {
			while($lig_ia=mysqli_fetch_object($res_test)) {
				$sql="DELETE FROM infos_actions_destinataires WHERE id_info='$lig_ia->id';";
				$del=mysqli_query($GLOBALS["mysqli"], $sql);
				if($del) {
					$sql="DELETE FROM infos_actions WHERE id='$lig_ia->id';";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);
				}
			}
		}
	}
}

if (isset($_POST['form_anti_inject_var_SERVER'])) {
	check_token();

	$anti_inject_var_SERVER=isset($_POST['anti_inject_var_SERVER']) ? $_POST['anti_inject_var_SERVER'] : "y";
	if (!saveSetting(("anti_inject_var_SERVER"), $anti_inject_var_SERVER)) {
		$msg = "Erreur lors de l'enregistrement de anti_inject_var_SERVER à la valeur $anti_inject_var_SERVER !";
	}
}

//**************** EN-TETE *********************
$titre_page = "Politique de sécurité";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

//on récupère le chemin de la page d'appel pour en faire le lien de retour
if(isset($_SERVER['HTTP_REFERER'])) {
	$url_retour = parse_url($_SERVER['HTTP_REFERER']);

	if($_SERVER['PHP_SELF']==$url_retour['path']) {
		$url_retour['path']='index.php#security_policy';
	}
}
else {
	$url_retour['path']='index.php#security_policy';
}
/*
foreach($url_retour as $key => $value) {
	echo "\$url_retour['$key']=$value<br />";
}
debug_var();
//$_SERVER['PHP_SELF']
*/

echo "<p class='bold'><a href='".$url_retour['path']."'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>\n";

echo "<form action='security_policy.php' method='post'>\n";
echo add_token_field();
echo "<center><input type='submit' value='Enregistrer' /></center>\n";

// Gestion des tentatives d'intrusion
echo "<h2>Gestion des tentatives d'intrusion</h2>\n";
echo "<div style='margin-left:3em;'>\n";
	
	// Options générales
	echo "<h3>Options générales</h3>\n";
	echo "<div style='margin-left:3em;'>\n";
		echo "<input type='checkbox' name='security_alert_email_admin' value='yes'";
		if (getSettingValue("security_alert_email_admin") == "yes") echo " CHECKED";
		echo " />\n";
		echo " Envoyer systématiquement un email à l'administrateur lors d'une tentative d'intrusion.<br/>\n";
		echo "Niveau de gravité minimal pour l'envoi du mail : ";
		echo "<select name='security_alert_email_min_level' size='1'>\n";
		for ($i = 1; $i <= 3;$i++) {
			echo "<option value='$i'";
			if (getSettingValue("security_alert_email_min_level") == $i) echo " SELECTED";
			echo ">$i</option>\n";
		}
		echo "</select>\n";
	echo "</div>\n";

	// Seuils d'alerte et actions à entreprendre
	echo "<h3>Seuils d'alertes</h3>\n";
	echo "<div style='margin-left:3em;'>\n";
		echo "<p>Vous pouvez définir deux seuils d'alerte et leurs actions associées. Ces seuils s'appliquent aux tentatives d'intrusion effectuées par les utilisateurs de Gepi. Chaque tentative a un niveau de gravité de 1 à 3 ; les seuils correspondent au cumul de ces niveaux de gravité pour un même utilisateur.</p>\n";
		echo "<p>Un utilisateur peut être placé en observation par l'administrateur, avec des seuils d'alerte distincts. Cela permet de définir une politique plus restrictive en cas de récidive.</p>\n";
		
		echo "<table class='normal' summary=\"Seuils d'alerte\">\n";
		echo "<tr>\n";
		echo "<th>Seuil</th>\n";
		echo "<th>Utilisateur sans antécédent</th>\n";
		echo "<th>Utilisateur surveillé</th>\n";
		echo "</tr>\n";
		
		// Niveau d'alerte 1
		echo "<tr>\n";
		echo "<td>\n";
		echo "<p>Seuil 1</p>\n";
		echo "</td>\n";
		echo "<td>\n";
		
		// Utilisateur sans antécédent
		echo "Niveau cumulé : ";
		echo "<select name='security_alert1_normal_cumulated_level' size='1'>\n";
		for ($i = 1; $i <= 15;$i++) {
			echo "<option value='$i'";
			if (getSettingValue("security_alert1_normal_cumulated_level") == $i) echo " SELECTED";
			echo ">$i</option>\n";
		}
		echo "</select>\n";
		echo "<br/>Actions :<br/>\n";
		echo "<input type='checkbox' name='security_alert1_normal_email_admin' value='yes'";
		if (getSettingValue("security_alert1_normal_email_admin") == "yes") echo " CHECKED";
		echo " /> Envoyer un email à l'administrateur<br/>\n";
		echo "<input type='checkbox' name='security_alert1_normal_block_user' value='yes'";
		if (getSettingValue("security_alert1_normal_block_user") == "yes") echo " CHECKED";
		echo " /> Désactiver le compte de l'utilisateur<br/>\n";
		echo "</td>\n";
		echo "<td>\n";
		
		// Utilisateur en observation
		echo "Niveau cumulé : ";
		echo "<select name='security_alert1_probation_cumulated_level' size='1'>\n";
		for ($i = 1; $i <= 15;$i++) {
			echo "<option value='$i'";
			if (getSettingValue("security_alert1_probation_cumulated_level") == $i) echo " SELECTED";
			echo ">$i</option>\n";
		}
		echo "</select>\n";
		echo "<br/>Actions :<br/>\n";
		echo "<input type='checkbox' name='security_alert1_probation_email_admin' value='yes'";
		if (getSettingValue("security_alert1_probation_email_admin") == "yes") echo " CHECKED";
		echo " /> Envoyer un email à l'administrateur<br/>\n";
		echo "<input type='checkbox' name='security_alert1_probation_block_user' value='yes'";
		if (getSettingValue("security_alert1_probation_block_user") == "yes") echo " CHECKED";
		echo " /> Désactiver le compte de l'utilisateur<br/>\n";
		echo "</td>\n";
		echo "</tr>\n";
		
		// Niveau d'alerte 2
		echo "<tr>\n";
		echo "<td>\n";
		echo "<p>Seuil 2</p>\n";
		echo "</td>\n";
		echo "<td>\n";
		
		// Utilisateur sans antécédent
		echo "Niveau cumulé : ";
		echo "<select name='security_alert2_normal_cumulated_level' size='1'>\n";
		for ($i = 1; $i <= 15;$i++) {
			echo "<option value='$i'";
			if (getSettingValue("security_alert2_normal_cumulated_level") == $i) echo " SELECTED";
			echo ">$i</option>\n";
		}
		echo "</select>\n";
		echo "<br/>Actions :<br/>\n";
		echo "<input type='checkbox' name='security_alert2_normal_email_admin' value='yes'";
		if (getSettingValue("security_alert2_normal_email_admin") == "yes") echo " CHECKED";
		echo " /> Envoyer un email à l'administrateur<br/>\n";
		echo "<input type='checkbox' name='security_alert2_normal_block_user' value='yes'";
		if (getSettingValue("security_alert2_normal_block_user") == "yes") echo " CHECKED";
		echo " /> Désactiver le compte de l'utilisateur<br/>\n";
		echo "</td>\n";
		echo "<td>\n";
		
		// Utilisateur en observation
		echo "Niveau cumulé : ";
		echo "<select name='security_alert2_probation_cumulated_level' size='1'>\n";
		for ($i = 1; $i <= 15;$i++) {
			echo "<option value='$i'";
			if (getSettingValue("security_alert2_probation_cumulated_level") == $i) echo " SELECTED";
			echo ">$i</option>\n";
		}
		echo "</select>\n";
		echo "<br/>Actions :<br/>\n";
		echo "<input type='checkbox' name='security_alert2_probation_email_admin' value='yes'";
		if (getSettingValue("security_alert2_probation_email_admin") == "yes") echo " CHECKED";
		echo " /> Envoyer un email à l'administrateur<br/>\n";
		echo "<input type='checkbox' name='security_alert2_probation_block_user' value='yes'";
		if (getSettingValue("security_alert2_probation_block_user") == "yes") echo " CHECKED";
		echo " /> Désactiver le compte de l'utilisateur<br/>\n";
		echo "</td>\n";
		echo "</tr>\n";
		
		echo "</table>\n";
		echo "<br/><br/>\n";
	echo "</div>\n";
echo "</div>\n";


echo "<h2><a name='csrf_mode'></a>CSRF</h2>";
echo "<div style='margin-left:3em;'>\n";

	echo "<p>";
	echo "<input type='radio' id='csrf_mode_vide' name='csrf_mode' value='' ";
	if(getSettingValue('csrf_mode')=='') {echo "checked ";}
	echo "/><label for='csrf_mode_vide'> Laisser faire l'enregistrement sans même informer l'administrateur (<i>fortement déconseillé</i>)</label>.<br />";

	echo "<input type='radio' id='csrf_mode_mail_seul' name='csrf_mode' value='mail_seul' ";
	if(getSettingValue('csrf_mode')=='mail_seul') {echo "checked ";}
	echo "/><label for='csrf_mode_mail_seul'> Envoyer un mail à l'administrateur, mais laisser faire l'enregistrement (<i>déconseillé, parce que certains dégats ne sont pas simples à réparer</i>)</label>.<br />";

	echo "<input type='radio' id='csrf_mode_strict' name='csrf_mode' value='strict' ";
	if(getSettingValue('csrf_mode')=='strict') {echo "checked ";}
	echo "/><label for='csrf_mode_strict'> Refuser l'enregistrement et envoyer un mail à l'administrateur (<i>conseillé</i>)</label>.";

	echo "</p>\n";

	echo "<p>Il est recommandé de se prémunir d'éventuelles attaques CSRF dont les utilisateurs pourraient être victimes.<br />
Vous devriez choisir le dernier mode ci-dessus.<br />
Voir <a href='http://fr.wikipedia.org/wiki/CSRF'>http://fr.wikipedia.org/wiki/CSRF</a> pour plus de détails.</p>\n";
echo "</div>\n";


echo "<h2><a name='Divers'></a>Divers</h2>";
echo "<div style='margin-left:3em;'>\n";

	echo "<p>";
	echo "<input type='hidden' id='form_anti_inject_var_SERVER' name='form_anti_inject_var_SERVER' value='1' />\n";
	echo "<input type='checkbox' id='anti_inject_var_SERVER' name='anti_inject_var_SERVER' value='n' ";
	if(getSettingValue('anti_inject_var_SERVER')=='n') {echo "checked ";}
	echo "/><label for='anti_inject_var_SERVER'> Désactiver le filtrage anti_inject sur la variable \$_SERVER</label>.<br />";
	echo "</p>\n";

	echo "<p>Cela peut être nécessaire avec des serveurs sous M\$Window$.</p>\n";
echo "</div>\n";

echo "<center><input type='submit' value='Enregistrer' /></center>\n";
echo "</form>\n";
echo "<br/><br/><br/>\n";
require("../lib/footer.inc.php");
?>
