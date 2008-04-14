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

if (isset($_GET['action'])) {
	// Une action a été demandée
	switch ($_GET['action']) {
		case "activer":
			$res = mysql_query("UPDATE utilisateurs SET etat = 'actif' WHERE (login = '".$_GET['user_login']."')");
			break;
		case "desactiver":
			$res = mysql_query("UPDATE utilisateurs SET etat = 'inactif' WHERE (login = '".$_GET['user_login']."')");
			break;
		case "observer":
			$res = mysql_query("UPDATE utilisateurs SET observation_securite = '1' WHERE (login = '".$_GET['user_login']."')");
			break;
		case "stop_observation":
			$res = mysql_query("UPDATE utilisateurs SET observation_securite = '0' WHERE (login = '".$_GET['user_login']."')");
			break;
		case "reinit_cumul":
			$res = mysql_query("UPDATE utilisateurs SET niveau_alerte = '0' WHERE (login = '".$_GET['user_login']."')");
			break;
		case "archiver":
			$res = mysql_query("UPDATE tentatives_intrusion SET statut = '' WHERE (statut = 'new')");
			break;
	}
	if (!$res) echo mysql_error();
}

//**************** EN-TETE *****************
$titre_page = "Sécurité Gepi";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
echo "<p class=bold><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | <a href='security_policy.php'>Définir la politique de sécurité</a> | ";
$sql="SELECT 1=1 FROM tentatives_intrusion WHERE statut='';";
$test_arch=mysql_query($sql);
if(mysql_num_rows($test_arch)>0) {
	echo "<a href='security_panel_archives.php'>Historique des alertes sécurités</a>";
}
else {
	echo "Historique des alertes sécurités";
}
echo "</p>\n";

echo "<p>Les alertes 'récentes' (non archivées) sont celles dont le niveau est pris en compte sur la page d'accueil (information 'Niveaux cumulés'). Pour remettre à zéro le compteur de la page d'accueil, il vous suffit de cliquer sur 'Archiver'.</p>\n";

echo "<table class='menu' style='width: 90%;'>\n";
echo "<tr>\n";
echo "<th colspan='5'>Alertes récentes (<a href='security_panel.php?action=archiver'>archiver</a>)</th>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td style='width: 20%;'>\n";
echo "<a href='".$_SERVER['PHP_SELF']."?order_by=login' style='display:inline;'>Utilisateur</a>";
echo "/";
echo "<a href='".$_SERVER['PHP_SELF']."?order_by=ip' style='display:inline;'>IP</a>";
echo "</td>\n";
echo "<td>\n";
// Le tri par date est le mode standard... pas besoin de paramètre
echo "<a href='".$_SERVER['PHP_SELF']."' style='display:inline;'>Date</a>";
echo "</td>\n";
echo "<td>\n";
echo "<a href='".$_SERVER['PHP_SELF']."?order_by=niveau' style='display:inline;'>Niv</a>";
echo "</td>\n";
echo "<td>Description</td>\n";
echo "<td style='width: 20%;'>Actions</td>\n";
echo "</tr>\n";

//$req = mysql_query("SELECT t.* FROM tentatives_intrusion t WHERE (t.statut = 'new') ORDER BY t.date DESC");
$sql="SELECT t.* FROM tentatives_intrusion t WHERE (t.statut = 'new') ORDER BY ";
if(isset($_GET['order_by'])) {
	$order_by=$_GET['order_by'];
	if($order_by=='login') {
		$sql.="t.login, ";
	}
	elseif($order_by=='ip') {
		$sql.="t.adresse_ip, ";
	}
	elseif($order_by=='niveau') {
		$sql.="t.niveau DESC, ";
	}
	else {
		unset($order_by);
	}
}
$sql.="t.date DESC";
$req = mysql_query($sql);
if (!$req) echo mysql_error();
while ($row = mysql_fetch_object($req)) {
	echo "<tr>\n";
	echo "<td>";
	$user = null;
	if ($row->login != "-") {
		// On récupère des informations sur l'utilisateur :
		$user_req = mysql_query("SELECT u.login, u.nom, u.prenom, u.statut, u.etat, u.niveau_alerte, u.observation_securite FROM utilisateurs u WHERE (u.login = '".$row->login . "')");
		$user = mysql_fetch_object($user_req);
	}

	if (!empty($user)) {
		echo $user->login ." - ".$row->adresse_ip."<br/>";
		echo "<b>".$user->prenom . " " . $user->nom."</b>";
		echo "<br/>".$user->statut;
		if ($user->etat == "actif") {
			//echo " (compte actif)";
			echo " (<span style='color:green;'>compte actif</span>)";
		} else {
			//echo " (compte désactivé)";
			echo " (<span style='color:red;'>compte désactivé</span>)";
		}
		echo "<br/>Score cumulé : ".$user->niveau_alerte;
	} else {
		echo "<b>Attaque extérieure</b><br/>";
		echo "Adresse IP : ".$row->adresse_ip."<br/>";
	}
	echo "</td>";
	echo "<td>".$row->date."</td>";
	echo "<td>".$row->niveau."</td>";
	echo "<td><p class='small'><b>Page : ".$row->fichier."</b><br/>".stripslashes($row->description)."</p></td>";
	echo "<td>";
	if (!empty($user)) {
		echo "<p>";
		if ($user->etat == "actif") {
			echo "<a style='padding: 2px;' href='security_panel.php?action=desactiver&amp;user_login=".$user->login."'>Désactiver le compte</a>";
		} else {
			echo "<a style='padding: 2px;' href='security_panel.php?action=activer&amp;user_login=".$user->login."'>Réactiver le compte</a>";
		}
		if ($user->observation_securite == 0) {
			echo "<a style='padding: 2px;' href='security_panel.php?action=observer&amp;user_login=".$user->login."'>Placer en observation</a>";
		} else {
			echo "<a style='padding: 2px;' href='security_panel.php?action=stop_observation&amp;user_login=".$user->login."'>Retirer l'observation</a>";
		}
		echo "<a style='padding: 2px;' href='security_panel.php?action=reinit_cumul&amp;user_login=".$user->login."'>Réinitialiser cumul</a>";
		echo "</p>";
	} else {
		echo "<p class='small'><i>Aucune action disponible</i><br />(l'alerte n'est pas liée à un utilisateur du système)</p>";
	}
	echo "</td>\n";
	echo "</tr>";
}
echo "</table>";


echo "<table class='menu'>\n";
echo "<tr>\n";
echo "<th colspan='3'>Utilisateurs en observation</th>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td style='width: 200px;'>Utilisateur</td>";
echo "<td style='width: 50px;'>Cumul actuel</td>";
echo "<td style='width: auto;'>Actions</td>\n";
echo "</tr>";

$req = mysql_query("SELECT u.login, u.nom, u.prenom, u.statut, u.etat, u.niveau_alerte FROM utilisateurs u WHERE (u.observation_securite = '1') ORDER BY u.niveau_alerte DESC");
if (!$req) echo mysql_error();
while ($row = mysql_fetch_object($req)) {
	echo "<tr>\n";
	echo "<td>";
	echo $row->login ." - ".$row->statut ."<br/>";
	echo "<b>".$row->prenom . " " . $row->nom."</b>";
	echo "<br/>";
	if ($row->etat == "actif") {
		echo "Compte actif";
	} else {
		echo "Compte désactivé";
	}
	echo "</td>";
	echo "<td>".$row->niveau_alerte."</td>";
	echo "<td>";
		echo "<p>";
		if ($row->etat == "actif") {
			echo "<a style='padding: 2px;' href='security_panel.php?action=desactiver&amp;user_login=".$row->login."'>Désactiver le compte</a>";
		} else {
			echo "<a style='padding: 2px;' href='security_panel.php?action=activer&amp;user_login=".$row->login."'>Réactiver le compte</a>";
		}
		echo "<a style='padding: 2px;' href='security_panel.php?action=stop_observation&amp;user_login=".$row->login."'>Retirer l'observation</a>";
		echo "<a style='padding: 2px;' href='security_panel.php?action=reinit_cumul&amp;user_login=".$row->login."'>Réinitialiser cumul</a>";
		echo "</p>";
	echo "</td>\n";
	echo "</tr>";
}
echo "</table>";

require("../lib/footer.inc.php");
?>