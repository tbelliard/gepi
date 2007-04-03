<?php
/*
 * $Id$
 *
 * Copyright 2001-2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// Enregistrement des données postées

if (isset($_POST) and !empty($_POST)) {

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

} // Fin : if isset($_POST)
//**************** EN-TETE *********************
$titre_page = "Politique de sécurité";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

echo "<p class=bold><a href='security_panel.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

echo "<form action='security_policy.php' method='post'>";
echo "<center><input type='submit' value='Enregistrer' /></center>";

// Options générales
echo "<h2>Options générales</h2>";
echo "<input type='checkbox' name='security_alert_email_admin' value='yes'";
if (getSettingValue("security_alert_email_admin") == "yes") echo " CHECKED";
echo " />";
echo " Envoyer systématiquement un email à l'administrateur lors d'une tentative d'intrusion.<br/>\n";
echo "Niveau de gravité minimal pour l'envoi du mail : ";
echo "<select name='security_alert_email_min_level' size='1'>";
for ($i = 1; $i <= 3;$i++) {
	echo "<option value='$i'";
	if (getSettingValue("security_alert_email_min_level") == $i) echo " SELECTED";
	echo ">$i</option>";
}
echo "</select>";
// Seuils d'alerte et actions à entreprendre
echo "<h2>Seuils d'alertes</h2>";
echo "<p>Vous pouvez définir deux seuils d'alerte et leurs actions associées. Ces seuils s'appliquent aux tentatives d'intrusion effectuées par les utilisateurs de Gepi. Chaque tentative a un niveau de gravité de 1 à 3 ; les seuils correspondent au cumul de ces niveaux de gravité pour un même utilisateur.</p>";
echo "<p>Un utilisateur peut être placé en observation par l'administrateur, avec des seuils d'alerte distincts. Cela permet de définir une politique plus restrictive en cas de récidive.</p>";

echo "<table class='normal'>";
echo "<tr><th>Niveau</th><th>Utilisateur sans antécédent</th><th>Utilisateur surveillé</th></tr>";

// Niveau d'alerte 1
echo "<tr><td>";
echo "<p>Niveau 1</p>";
echo "</td><td>";

// Utilisateur sans antécédent
echo "Seuil cumulé : ";
echo "<select name='security_alert1_normal_cumulated_level' size='1'>";
for ($i = 1; $i <= 15;$i++) {
	echo "<option value='$i'";
	if (getSettingValue("security_alert1_normal_cumulated_level") == $i) echo " SELECTED";
	echo ">$i</option>";
}
echo "</select>";
echo "<br/>Actions :<br/>";
echo "<input type='checkbox' name='security_alert1_normal_email_admin' value='yes'";
if (getSettingValue("security_alert1_normal_email_admin") == "yes") echo " CHECKED";
echo " /> Envoyer un email à l'administrateur<br/>";
echo "<input type='checkbox' name='security_alert1_normal_block_user' value='yes'";
if (getSettingValue("security_alert1_normal_block_user") == "yes") echo " CHECKED";
echo " /> Désactiver le compte de l'utilisateur<br/>";
echo "</td><td>";

// Utilisateur en observation
echo "Seuil cumulé : ";
echo "<select name='security_alert1_probation_cumulated_level' size='1'>";
for ($i = 1; $i <= 15;$i++) {
	echo "<option value='$i'";
	if (getSettingValue("security_alert1_probation_cumulated_level") == $i) echo " SELECTED";
	echo ">$i</option>";
}
echo "</select>";
echo "<br/>Actions :<br/>";
echo "<input type='checkbox' name='security_alert1_probation_email_admin' value='yes'";
if (getSettingValue("security_alert1_probation_email_admin") == "yes") echo " CHECKED";
echo " /> Envoyer un email à l'administrateur<br/>";
echo "<input type='checkbox' name='security_alert1_probation_block_user' value='yes'";
if (getSettingValue("security_alert1_probation_block_user") == "yes") echo " CHECKED";
echo " /> Désactiver le compte de l'utilisateur<br/>";
echo "</td></tr>";

// Niveau d'alerte 2
echo "<tr><td>";
echo "<p>Niveau 2</p>";
echo "</td><td>";

// Utilisateur sans antécédent
echo "Seuil cumulé : ";
echo "<select name='security_alert2_normal_cumulated_level' size='1'>";
for ($i = 1; $i <= 15;$i++) {
	echo "<option value='$i'";
	if (getSettingValue("security_alert2_normal_cumulated_level") == $i) echo " SELECTED";
	echo ">$i</option>";
}
echo "</select>";
echo "<br/>Actions :<br/>";
echo "<input type='checkbox' name='security_alert2_normal_email_admin' value='yes'";
if (getSettingValue("security_alert2_normal_email_admin") == "yes") echo " CHECKED";
echo " /> Envoyer un email à l'administrateur<br/>";
echo "<input type='checkbox' name='security_alert2_normal_block_user' value='yes'";
if (getSettingValue("security_alert2_normal_block_user") == "yes") echo " CHECKED";
echo " /> Désactiver le compte de l'utilisateur<br/>";
echo "</td><td>";

// Utilisateur en observation
echo "Seuil cumulé : ";
echo "<select name='security_alert2_probation_cumulated_level' size='1'>";
for ($i = 1; $i <= 15;$i++) {
	echo "<option value='$i'";
	if (getSettingValue("security_alert2_probation_cumulated_level") == $i) echo " SELECTED";
	echo ">$i</option>";
}
echo "</select>";
echo "<br/>Actions :<br/>";
echo "<input type='checkbox' name='security_alert2_probation_email_admin' value='yes'";
if (getSettingValue("security_alert2_probation_email_admin") == "yes") echo " CHECKED";
echo " /> Envoyer un email à l'administrateur<br/>";
echo "<input type='checkbox' name='security_alert2_probation_block_user' value='yes'";
if (getSettingValue("security_alert2_probation_block_user") == "yes") echo " CHECKED";
echo " /> Désactiver le compte de l'utilisateur<br/>";
echo "</td></tr>";

echo "</table>";
echo "<br/><br/>";
echo "<center><input type='submit' value='Enregistrer' /></center>";
echo "</form>";
echo "<br/><br/><br/>";
require("../lib/footer.inc.php");
?>