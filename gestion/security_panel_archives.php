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


if (isset($_POST['duree2'])) {
   $duree2 = $_POST['duree2'];
} else {
	if (isset($_GET['duree2'])) {
		$duree2 = $_GET['duree2'];
	} else {
		$duree2 = '20dernieres';
	}
}

if(($duree2!="20dernieres")&&
	($duree2!="2")&&
	($duree2!="7")&&
	($duree2!="15")&&
	($duree2!="30")&&
	($duree2!="60")&&
	($duree2!="183")&&
	($duree2!="365")&&
	($duree2!="all")
	) {
		$duree2="20dernieres";
}

//**************** EN-TETE *****************
$titre_page = "Sécurité Gepi - Archives -";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
echo "<p class=bold><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | <a href='security_policy.php'>Définir la politique de sécurité</a> | <a href='security_panel.php'>Panneau de sécurité</a></p>\n";
echo "<br />\n";
echo "<form action=\"security_panel_archives.php\" name=\"form_affiche_alerte\" method=\"post\">\n";
echo "Afficher l'historique des alertes : <select name=\"duree2\" size=\"1\">\n";
echo "<option ";
if ($duree2 == '20dernieres') echo "selected";
echo " value='20dernieres'>les 20 dernières</option>\n";
echo "<option ";
if ($duree2 == 2) echo "selected";
echo " value=2>depuis Deux jours</option>\n";
echo "<option ";
if ($duree2 == 7) echo "selected";
echo " value=7>depuis Une semaine</option>\n";
echo "<option ";
if ($duree2 == 15) echo "selected";
echo " value=15 >depuis Quinze jours</option>\n";
echo "<option ";
if ($duree2 == 30) echo "selected";
echo " value=30>depuis Un mois</option>\n";
echo "<option ";
if ($duree2 == 60) echo "selected";
echo " value=60>depuis Deux mois</option>\n";
echo "<option ";
if ($duree2 == 183) echo "selected";
echo " value=183>depuis Six mois</option>\n";
echo "<option ";
if ($duree2 == 365) echo "selected";
echo " value=365>depuis Un an</option>\n";
echo "<option ";
if ($duree2 == 'all') echo "selected";
echo " value='all'>depuis Le début</option>\n";
echo "</select>\n";
echo " <input type=\"submit\" name=\"Valider\" value=\"Valider\" /><br /><br />\n";
echo "</form>\n";

//echo "<table class='menu' style='width: 90%;'>\n";
echo "<table class='boireaus' style='width: 90%;'>\n";
echo "<tr>\n";
echo "<th colspan='5'>Historique des alertes</th>\n";
echo "</tr>\n";

/*
echo "<tr>\n";
echo "<td style='width: 20%;'>Utilisateur</td>\n";
echo "<td>Date</td>\n";
echo "<td>Niv.</td>\n";
echo "<td>Description</td>\n";
echo "<td style='width: 20%;'>Actions</td>\n";
echo "</tr>\n";
*/
echo "<tr>\n";
echo "<th style='width: 20%;'>\n";
echo "<a href='".$_SERVER['PHP_SELF']."?order_by=login";
if(isset($duree2)){echo "&amp;duree2=$duree2";}
echo "' style='display:inline;'>Utilisateur</a>\n";
echo "/";
echo "<a href='".$_SERVER['PHP_SELF']."?order_by=ip";
if(isset($duree2)){echo "&amp;duree2=$duree2";}
echo "' style='display:inline;'>IP</a>\n";
echo "</th>\n";
echo "<th>\n";
// Le tri par date est le mode standard... pas besoin de paramètre
echo "<a href='".$_SERVER['PHP_SELF'];
if(isset($duree2)){echo "?duree2=$duree2";}
echo "' style='display:inline;'>Date</a>\n";
echo "</th>\n";
echo "<th>\n";
echo "<a href='".$_SERVER['PHP_SELF']."?order_by=niveau";
if(isset($duree2)){echo "&amp;duree2=$duree2";}
echo "' style='display:inline;'>Niv</a>\n";
echo "</th>\n";
echo "<th>Description</th>\n";
echo "<th style='width: 20%;'>Actions</th>\n";
echo "</tr>\n";

$requete = '';
$requete1 = '';
if ($duree2 != 'all') {$requete = "(t.date > now() - interval " . $duree2 . " day) ";}
if ($duree2 == '20dernieres') {$requete1 = "LIMIT 0,20"; $requete='1';}
if ($duree2 == 'all') {$requete='1';}

//$sql ="SELECT t.* FROM tentatives_intrusion t WHERE ((t.statut != 'new') AND ".$requete.") ORDER BY t.date DESC ".$requete1;
$sql ="SELECT t.* FROM tentatives_intrusion t WHERE ((t.statut != 'new') AND ".$requete.")";

$sql.=" ORDER BY ";
if(isset($_GET['order_by'])) {
	$order_by=$_GET['order_by'];
	if($order_by=='niveau') {
		$sql.="t.niveau DESC, ";
	}
	elseif($order_by=='login') {
		$sql.="t.login, ";
	}
	elseif($order_by=='ip') {
		$sql.="t.adresse_ip, ";
	}
	else {
		unset($order_by);
	}
}
$sql.="t.date DESC ".$requete1;

//echo $sql;

$req = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
if (!$req) echo ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false));
$alt=1;
while ($row = mysqli_fetch_object($req)) {
	$alt=$alt*(-1);
	$user = null;
	echo "<tr class='lig$alt white_hover'>\n";
	echo "<td>\n";
	if ($row->login != "-") {
		// On récupère des informations sur l'utilisateur :
		$user_req = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT u.login, u.nom, u.prenom, u.statut, u.etat, u.niveau_alerte, u.observation_securite FROM utilisateurs u WHERE (u.login = '".$row->login . "')");
		$user = mysqli_fetch_object($user_req);
	}

	if (!empty($user)) {
		echo $user->login ." - ".$row->adresse_ip."<br/>\n";
		echo "<b>".$user->prenom . " " . $user->nom."</b>\n";
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
		echo "<b>Attaque extérieure</b><br/>\n";
		echo "Adresse IP : ".$row->adresse_ip."<br/>\n";
	}
	echo "</td>\n";
	echo "<td>".$row->date."</td>\n";
	echo "<td>".$row->niveau."</td>\n";
	echo "<td><p class='small'><b>Page : ".$row->fichier."</b><br/>".stripslashes($row->description)."</p></td>\n";
	echo "<td>\n";
	if (!empty($user)) {
		echo "<p>\n";
		if ($user->etat == "actif") {
			echo "<a style='padding: 2px;' href='security_panel.php?action=desactiver&amp;user_login=".$user->login;
			if(isset($order_by)) {echo "&amp;order_by=$order_by";}
			echo add_token_in_url()."'>Désactiver le compte</a>\n";
		} else {
			echo "<a style='padding: 2px;' href='security_panel.php?action=activer&amp;user_login=".$user->login;
			if(isset($order_by)) {echo "&amp;order_by=$order_by";}
			echo add_token_in_url()."'>Réactiver le compte</a>\n";
		}
		echo "<br />\n";
		if ($user->observation_securite == 0) {
			echo "<a style='padding: 2px;' href='security_panel.php?action=observer&amp;user_login=".$user->login;
			if(isset($order_by)) {echo "&amp;order_by=$order_by";}
			echo add_token_in_url()."'>Placer en observation</a>\n";
		} else {
			echo "<a style='padding: 2px;' href='security_panel.php?action=stop_observation&amp;user_login=".$user->login;
			if(isset($order_by)) {echo "&amp;order_by=$order_by";}
			echo add_token_in_url()."'>Retirer l'observation</a>\n";
		}
		echo "<br />\n";
		echo "<a style='padding: 2px;' href='security_panel.php?action=reinit_cumul&amp;user_login=".$user->login;
		if(isset($order_by)) {echo "&amp;order_by=$order_by";}
		echo add_token_in_url()."'>Réinitialiser cumul</a>\n";
		echo "</p>\n";
	} else {
		echo "<p class='small'><i>Aucune action disponible</i><br />(l'alerte n'est pas liée à un utilisateur du système)</p>\n";
	}
	echo "</td>\n";
	echo "</tr>\n";
}
echo "</table>\n";


require("../lib/footer.inc.php");
?>