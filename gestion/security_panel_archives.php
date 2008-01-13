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


if (isset($_POST['duree2'])) {
   $duree2 = $_POST['duree2'];
} else {
   $duree2 = '20dernieres';
}

//**************** EN-TETE *****************
$titre_page = "Sécurité Gepi - Archives -";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
echo "<p class=bold><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | <a href='security_policy.php'>Définir la politique de sécurité</a> | <a href='security_panel.php'>Panneau de sécurité</a></p>";
echo "<br>";
echo "<form action=\"security_panel_archives.php\" name=\"form_affiche_alerte\" method=\"post\">";
echo "Afficher l'historique des alertes : <select name=\"duree2\" size=\"1\">";
echo "<option ";
if ($duree2 == '20dernieres') echo "selected";
echo " value='20dernieres'>les 20 dernières</option>";
echo "<option ";
if ($duree2 == 2) echo "selected";
echo " value=2>depuis Deux jours</option>";
echo "<option ";
if ($duree2 == 7) echo "selected";
echo " value=7>depuis Une semaine</option>";
echo "<option ";
if ($duree2 == 15) echo "selected";
echo " value=15 >depuis Quinze jours</option>";
echo "<option ";
if ($duree2 == 30) echo "selected";
echo " value=30>depuis Un mois</option>";
echo "<option ";
if ($duree2 == 60) echo "selected";
echo " value=60>depuis Deux mois</option>";
echo "<option ";
if ($duree2 == 183) echo "selected";
echo " value=183>depuis Six mois</option>";
echo "<option ";
if ($duree2 == 365) echo "selected";
echo " value=365>depuis Un an</option>";
echo "<option ";
if ($duree2 == 'all') echo "selected";
echo " value='all'>depuis Le début</option>";
echo "</select>";
echo " <input type=\"submit\" name=\"Valider\" value=\"Valider\" /><br /><br />";
echo "</form>";

echo "<table class='menu' style='width: 90%;'>\n";
echo "<tr>\n";
echo "<th colspan='5'>Historique des alertes</th>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td style='width: 20%;'>Utilisateur</td>";
echo "<td>Date</td>";
echo "<td>Niv.</td>";
echo "<td>Description</td>";
echo "<td style='width: 20%;'>Actions</td>\n";
echo "</tr>";

$requete = '';
$requete1 = '';
if ($duree2 != 'all') {$requete = "(t.date > now() - interval " . $duree2 . " day) ";}
if ($duree2 == '20dernieres') {$requete1 = "LIMIT 0,20"; $requete='1';}
if ($duree2 == 'all') {$requete='1';}

$sql ="SELECT t.* FROM tentatives_intrusion t WHERE ((t.statut != 'new') AND ".$requete.") ORDER BY t.date DESC ".$requete1;

//echo $sql;

$req = mysql_query($sql);
if (!$req) echo mysql_error();
while ($row = mysql_fetch_object($req)) {
	$user = null;
	echo "<tr>\n";
	echo "<td>";
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
			echo " (compte actif)";
		} else {
			echo " (compte désactivé)";
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


require("../lib/footer.inc.php");
?>