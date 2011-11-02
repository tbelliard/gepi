<?php
/*
 * $Id: security_panel.php 7479 2011-07-22 09:45:27Z crob $
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

$tri=isset($_POST['tri']) ? $_POST['tri'] : (isset($_GET['tri']) ? $_GET['tri'] : 'nom');
$user_login=isset($_POST['user_login']) ? $_POST['user_login'] : (isset($_GET['user_login']) ? $_GET['user_login'] : NULL);
$afficher_les_alertes_d_un_compte=isset($_POST['afficher_les_alertes_d_un_compte']) ? $_POST['afficher_les_alertes_d_un_compte'] : (isset($_GET['afficher_les_alertes_d_un_compte']) ? $_GET['afficher_les_alertes_d_un_compte'] : 'n');

$user_login2=isset($_POST['user_login2']) ? $_POST['user_login2'] : (isset($_GET['user_login2']) ? $_GET['user_login2'] : '');

if (isset($_GET['action'])) {
	check_token();
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
	if (!$res) {echo mysql_error();}
}

//**************** EN-TETE *****************
$titre_page = "Sécurité Gepi";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

//debug_var();

echo "<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>\n";
echo add_token_field();
echo "<p class='bold'><a href='index.php#security_panel'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | <a href='security_policy.php'>Définir la politique de sécurité</a> | ";
$sql="SELECT 1=1 FROM tentatives_intrusion WHERE statut='';";
$test_arch=mysql_query($sql);
if(mysql_num_rows($test_arch)>0) {
	echo "<a href='security_panel_archives.php'>Historique des alertes sécurité</a>";
}

//else {
//	echo "Historique des alertes sécurité";

	if($tri=='nom') {
		$sql="SELECT DISTINCT u.login, u.nom, u.prenom, u.statut, count(t.login) AS nb FROM utilisateurs u, tentatives_intrusion t WHERE t.login=u.login GROUP BY u.login ORDER BY u.nom, u.prenom;";
	}
	else {
		$sql="SELECT DISTINCT u.login, u.nom, u.prenom, u.statut, count(t.login) AS nb FROM utilisateurs u, tentatives_intrusion t WHERE t.login=u.login GROUP BY u.login ORDER BY nb, u.nom, u.prenom;";
	}

	$res_login_alerte=mysql_query($sql);
	if(mysql_num_rows($res_login_alerte)>0) {
		echo " | ";

		echo "<select name='user_login' onchange=\"document.forms['form1'].submit();\">\n";
		echo "<option value=''>---</option>\n";
		while($lig_login_alerte=mysql_fetch_object($res_login_alerte)) {
			echo "<option value='$lig_login_alerte->login'";
			if((isset($user_login))&&($lig_login_alerte->login==$user_login)) {echo " selected='true'";}
			echo ">$lig_login_alerte->nom $lig_login_alerte->prenom ($lig_login_alerte->nb)</option>\n";
		}
		echo "</select>\n";
	}

	// Lien: Trier par nom/prénom ou nombre d'alertes
	echo " Trier par <a href='".$_SERVER['PHP_SELF']."?tri=nom'>nom</a> / <a href='".$_SERVER['PHP_SELF']."?tri=score'>score</a>";

	echo "<input type='hidden' name='afficher_les_alertes_d_un_compte' value='y' />\n";

	echo "<input type='submit' name='filtrer_user' id='filtrer_user' value='Filtrer' />\n";



	//$sql="SELECT DISTINCT description, count(description) AS nb FROM tentatives_intrusion WHERE description LIKE 'Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login :%' GROUP BY description ORDER BY description;";

	//$sql="SELECT DISTINCT description, count(description) AS nb FROM tentatives_intrusion WHERE description LIKE 'Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login :%' GROUP BY description ORDER BY nb DESC;";
	$sql="SELECT DISTINCT description, count(description) AS nb FROM tentatives_intrusion WHERE description LIKE 'Tentative de connexion avec un mot de passe incorrect.%(login :%' GROUP BY description ORDER BY nb DESC;";
	$res_erreur_mdp=mysql_query($sql);
	// Récupérer les logins
	if(mysql_num_rows($res_erreur_mdp)>0) {
		echo " | ";

		echo "Erreur mot de passe&nbsp;: ";
		echo "<select name='user_login2' onchange=\"document.forms['form1'].submit();\">\n";
		echo "<option value=''>---</option>\n";
		while($lig_erreur_login=mysql_fetch_object($res_erreur_mdp)) {
			$tab_tmp=explode(':', $lig_erreur_login->description);
			// On vire l'espace au début et la parenthèse à la fin
			$current_login=substr($tab_tmp[1],1,strlen($tab_tmp[1])-2);

			echo "<option value='$current_login'";
			if((isset($user_login2))&&($current_login==$user_login2)) {echo " selected='true'";}
			echo ">$current_login ($lig_erreur_login->nb)</option>\n";
		}
		echo "</select>\n";
	}


/*

SELECT DISTINCT adresse_ip, count(adresse_ip) as nb FROM tentatives_intrusion GROUP BY adresse_ip ORDER BY nb DESC;

*/
//}
echo "</p>\n";

echo "<script type='text/javascript'>
	if(document.getElementById('filtrer_user')) {
		document.getElementById('filtrer_user').style.display='none';
	}
</script>\n";

echo "</form>\n";

if($user_login2!='') {
	$user_login=$user_login2;
}

//=======================================================================================================
// Seulement les alertes de $user_login:
if(($afficher_les_alertes_d_un_compte=="y")&&($user_login!='')) {
	//$sql="SELECT 1=1 FROM utilisateurs WHERE login='$user_login';";
	//$test=mysql_query($sql);
	//if(mysql_num_rows($test)==0) {

	//$sql="SELECT u.login, u.nom, u.prenom, u.email, u.statut, u.etat, u.niveau_alerte, u.observation_securite, u.date_verrouillage, u.ticket_expiration FROM utilisateurs u WHERE (u.login = '".$user_login . "');";
	$sql="SELECT u.* FROM utilisateurs u WHERE (u.login='".$user_login."');";
	//echo "$sql<br />";
	$user_req = mysql_query($sql);
	if(mysql_num_rows($user_req)>0) {
		//$user=mysql_fetch_object($user_req);
		$user=mysql_fetch_array($user_req, MYSQL_ASSOC);

		echo "<p>Affichage des alertes concernant le compte ";
		if($user['statut']=='eleve') {
			echo "<a href='../eleves/modify_eleve.php?eleve_login=$user_login'>$user_login</a>";
		}
		elseif($user['statut']=='responsable') {
			$infos_user=get_infos_from_login_utilisateur($user_login);
			echo "<a href='../responsables/modify_resp.php?pers_id=".$infos_user['pers_id']."'>$user_login</a>";
		}
		else {
			echo "<a href='../utilisateurs/modify_user.php?user_login=$user_login'>$user_login</a>";
		}

		if($user['email']!="") {
			$lien_mail="<a href=\"mailto:".$user['email']."?subject="."[Gepi]: Votre compte";
			if($_SESSION['email']!='') {
				$lien_mail.="&amp;bcc=".$_SESSION['email'];
			}
			$lien_mail.="&amp;body=Bonjour%20".$user['civilite']."%20".$user['nom']."%20".substr(ucfirst($user['prenom']),0,1).".,%0A%0a"."%0A%0a"."%0A%0a"."%0A%0a"."Cordialement."."-- "."%0A%0a".$_SESSION['prenom']."%20".$_SESSION['nom']."\">";
	
			$lien_mail.=" <img src='../images/icons/mail.png' width='16' height='16' />";
	
			$lien_mail.="</a>";
	
			echo $lien_mail;
		}
		echo "</p>\n";

		$tab_champs=array('change_mdp', 'date_verrouillage', 'ticket_expiration', 'niveau_alerte', 'observation_securite');

		$alt=1;
		echo "<table class='boireaus' summary=''>\n";
		for($i=0;$i<count($tab_champs);$i++) {
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
			echo "<td>$tab_champs[$i]</td>\n";
			$current_field=$tab_champs[$i];
			echo "<td>$user[$current_field]</td>\n";
			echo "</tr>\n";
		}
		echo "</table>\n";
		echo "<br />\n";
	}
	else {
		echo "<p>Affichage des alertes concernant le compte <b>$user_login</b> qui n'existe pas ou plus.</p>\n";
	}

	if($user_login2=='') {
		$nom_champ_user_login='user_login';
	}
	else {
		$nom_champ_user_login='user_login2';
	}

	echo "<table class='boireaus' style='width: 90%;'>\n";
	echo "<tr>\n";
	echo "<th colspan='5'>Alertes récentes</th>\n";
	echo "</tr>\n";
	
	echo "<tr>\n";
	echo "<th style='width: 20%;'>\n";
	echo "<a href='".$_SERVER['PHP_SELF']."?$nom_champ_user_login=$user_login&amp;afficher_les_alertes_d_un_compte=y&amp;order_by=login";
	if(isset($tri)) {echo "&amp;tri=$tri";}
	echo "' style='display:inline;'>Utilisateur</a>";
	echo " / ";
	echo "<a href='".$_SERVER['PHP_SELF']."?$nom_champ_user_login=$user_login&amp;afficher_les_alertes_d_un_compte=y&amp;order_by=ip";
	if(isset($tri)) {echo "&amp;tri=$tri";}
	echo "' style='display:inline;'>IP</a>";

	echo "</th>\n";
/*
	echo "<th style='width: 20%;'>\n";
	echo "</th>\n";
*/
	echo "<th>\n";
	// Le tri par date est le mode standard... pas besoin de paramètre
	echo "<a href='".$_SERVER['PHP_SELF']."?$nom_champ_user_login=$user_login&amp;afficher_les_alertes_d_un_compte=y";
	if(isset($tri)) {echo "&amp;tri=$tri";}
	echo "' style='display:inline;'>Date</a>";
	echo "</th>\n";
	echo "<th>\n";
	echo "<a href='".$_SERVER['PHP_SELF']."?$nom_champ_user_login=$user_login&amp;afficher_les_alertes_d_un_compte=y&amp;order_by=niveau";
	if(isset($tri)) {echo "&amp;tri=$tri";}
	echo "' style='display:inline;'>Niv</a>";
	echo "</th>\n";
	echo "<th>Description</th>\n";
	echo "<th style='width: 20%;'>Actions</th>\n";
	echo "</tr>\n";
	
	//$req = mysql_query("SELECT t.* FROM tentatives_intrusion t WHERE (t.statut = 'new') ORDER BY t.date DESC");
	if($user_login2=='') {
		$sql="SELECT t.* FROM tentatives_intrusion t WHERE (t.statut = 'new' AND t.login='$user_login') ORDER BY ";
	}
	else {
		$sql="SELECT t.* FROM tentatives_intrusion t WHERE (t.statut = 'new' AND t.description='Tentative de connexion avec un mot de passe incorrect. Ce peut être simplement une faute de frappe. Cette alerte n''est significative qu''en cas de répétition. (login : $user_login)') ORDER BY ";
	}
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
	if (!$req) {echo mysql_error();}

	$alt=1;
	while ($row = mysql_fetch_object($req)) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover'>\n";
		echo "<td>\n";
		/*
		$user = null;
		if ($row->login != "-") {
			// On récupère des informations sur l'utilisateur :
			$user_req = mysql_query("SELECT u.login, u.nom, u.prenom, u.statut, u.etat, u.niveau_alerte, u.observation_securite FROM utilisateurs u WHERE (u.login = '".$row->login . "')");
			$user = mysql_fetch_object($user_req);
		}
		*/

		if (!empty($user)) {
			echo $user['login'] ." - ".$row->adresse_ip."<br/>\n";
			echo "<b>".$user['prenom'] . " " . $user['nom']."</b>";
			echo "<br/>".$user['statut'];
			if ($user['etat'] == "actif") {
				//echo " (compte actif)";
				echo " (<span style='color:green;'>compte actif</span>)";
			} else {
				//echo " (compte désactivé)";
				echo " (<span style='color:red;'>compte désactivé</span>)";
			}
			echo "<br/>Score cumulé : ".$user['niveau_alerte'];
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
			if ($user['etat'] == "actif") {
				echo "<a style='padding: 2px;' href='security_panel.php?action=desactiver&amp;afficher_les_alertes_d_un_compte=y&amp;user_login=".$user_login;
				if($user_login2!='') {echo "&amp;user_login2=$user_login2";}
				if(isset($tri)) {echo "&amp;tri=$tri";}
				if(isset($order_by)) {echo "&amp;order_by=$order_by";}
				echo add_token_in_url()."'>Désactiver le compte</a>";
			} else {
				echo "<a style='padding: 2px;' href='security_panel.php?action=activer&amp;afficher_les_alertes_d_un_compte=y&amp;user_login=".$user_login;
				if($user_login2!='') {echo "&amp;user_login2=$user_login2";}
				if(isset($tri)) {echo "&amp;tri=$tri";}
				if(isset($order_by)) {echo "&amp;order_by=$order_by";}
				echo add_token_in_url()."'>Réactiver le compte</a>";
			}
			echo "<br />\n";
			if ($user['observation_securite'] == 0) {
				echo "<a style='padding: 2px;' href='security_panel.php?action=observer&amp;afficher_les_alertes_d_un_compte=y&amp;user_login=".$user_login;
				if($user_login2!='') {echo "&amp;user_login2=$user_login2";}
				if(isset($tri)) {echo "&amp;tri=$tri";}
				if(isset($order_by)) {echo "&amp;order_by=$order_by";}
				echo add_token_in_url()."'>Placer en observation</a>";
			} else {
				echo "<a style='padding: 2px;' href='security_panel.php?action=stop_observation&amp;afficher_les_alertes_d_un_compte=y&amp;user_login=".$user_login;
				if($user_login2!='') {echo "&amp;user_login2=$user_login2";}
				if(isset($tri)) {echo "&amp;tri=$tri";}
				if(isset($order_by)) {echo "&amp;order_by=$order_by";}
				echo add_token_in_url()."'>Retirer l'observation</a>";
			}
			echo "<br />\n";
			echo "<a style='padding: 2px;' href='security_panel.php?action=reinit_cumul&amp;afficher_les_alertes_d_un_compte=y&amp;user_login=".$user_login;
			if($user_login2!='') {echo "&amp;user_login2=$user_login2";}
			if(isset($tri)) {echo "&amp;tri=$tri";}
			if(isset($order_by)) {echo "&amp;order_by=$order_by";}
			echo add_token_in_url()."'>Réinitialiser cumul</a>";
			echo "</p>\n";
		} else {
			echo "<p class='small'><i>Aucune action disponible</i><br />(l'alerte n'est pas liée à un utilisateur du système)</p>\n";
		}

		echo "</td>\n";
		echo "</tr>\n";
	}
	echo "</table>\n";

	require("../lib/footer.inc.php");
	die();
}
//=======================================================================================================
// Toutes les alertes:
echo "<p>Les alertes 'récentes' (non archivées) sont celles dont le niveau est pris en compte sur la page d'accueil (information 'Niveaux cumulés'). Pour remettre à zéro le compteur de la page d'accueil, il vous suffit de cliquer sur 'Archiver'.</p>\n";


$sql="SELECT u.login, u.nom, u.prenom, u.statut, u.etat, u.niveau_alerte FROM utilisateurs u WHERE (u.observation_securite = '1') ORDER BY u.niveau_alerte DESC;";
$req_observation = mysql_query($sql);
if (!$req_observation) {echo mysql_error();}
elseif(mysql_num_rows($req_observation)>0) {
	echo "<p style='color:red'><a href='#utilisateurs_en_observation'>".mysql_num_rows($req_observation)." utilisateur(s)</a> en <b>observation</b>.</p>\n";
}

// Comptes désactivés
$sql="SELECT DISTINCT u.login, u.nom, u.prenom, u.statut, u.etat, u.niveau_alerte FROM utilisateurs u, tentatives_intrusion t WHERE (u.etat='inactif' AND t.login=u.login AND t.statut='new');";
$req_desactive=mysql_query($sql);
if (!$req_desactive) {echo mysql_error();}
elseif(mysql_num_rows($req_desactive)>0) {
	echo "<p style='color:red'><a href='#utilisateurs_desactives'>".mysql_num_rows($req_desactive)." utilisateur(s)</a> avec alerte dans cette page ont leur <b>compte désactivé</b>.</p>\n";
}


//echo "<table class='menu' style='width: 90%;'>\n";
echo "<table class='boireaus' style='width: 90%;'>\n";
echo "<tr>\n";
echo "<th colspan='5'>Alertes récentes (<a href='security_panel.php?action=archiver".add_token_in_url()."'>archiver</a>)</th>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<th style='width: 20%;'>\n";
echo "<a href='".$_SERVER['PHP_SELF']."?order_by=login' style='display:inline;'>Utilisateur</a>";
echo "/";
echo "<a href='".$_SERVER['PHP_SELF']."?order_by=ip' style='display:inline;'>IP</a>";
echo "</th>\n";
echo "<th>\n";
// Le tri par date est le mode standard... pas besoin de paramètre
echo "<a href='".$_SERVER['PHP_SELF']."' style='display:inline;'>Date</a>";
echo "</th>\n";
echo "<th>\n";
echo "<a href='".$_SERVER['PHP_SELF']."?order_by=niveau' style='display:inline;'>Niv</a>";
echo "</th>\n";
echo "<th>Description</th>\n";
echo "<th style='width: 20%;'>Actions</th>\n";
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
$alt=1;
while ($row = mysql_fetch_object($req)) {
	$alt=$alt*(-1);
	echo "<tr class='lig$alt white_hover'>\n";
	echo "<td>\n";
	$user = null;
	if ($row->login != "-") {
		// On récupère des informations sur l'utilisateur :
		$user_req = mysql_query("SELECT u.login, u.nom, u.prenom, u.statut, u.etat, u.niveau_alerte, u.observation_securite FROM utilisateurs u WHERE (u.login = '".$row->login . "')");
		$user = mysql_fetch_object($user_req);
	}

	if (!empty($user)) {
		echo $user->login ." - ".$row->adresse_ip."<br/>\n";
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
			if(isset($tri)) {echo "&amp;tri=$tri";}
			if(isset($order_by)) {echo "&amp;order_by=$order_by";}
			echo add_token_in_url()."'>Désactiver le compte</a>";
		} else {
			echo "<a style='padding: 2px;' href='security_panel.php?action=activer&amp;user_login=".$user->login;
			if(isset($tri)) {echo "&amp;tri=$tri";}
			if(isset($order_by)) {echo "&amp;order_by=$order_by";}
			echo add_token_in_url()."'>Réactiver le compte</a>";
		}
		echo "<br />\n";
		if ($user->observation_securite == 0) {
			echo "<a style='padding: 2px;' href='security_panel.php?action=observer&amp;user_login=".$user->login;
			if(isset($tri)) {echo "&amp;tri=$tri";}
			if(isset($order_by)) {echo "&amp;order_by=$order_by";}
			echo add_token_in_url()."'>Placer en observation</a>";
		} else {
			echo "<a style='padding: 2px;' href='security_panel.php?action=stop_observation&amp;user_login=".$user->login;
			if(isset($tri)) {echo "&amp;tri=$tri";}
			if(isset($order_by)) {echo "&amp;order_by=$order_by";}
			echo add_token_in_url()."'>Retirer l'observation</a>";
		}
		echo "<br />\n";
		echo "<a style='padding: 2px;' href='security_panel.php?action=reinit_cumul&amp;user_login=".$user->login;
		if(isset($tri)) {echo "&amp;tri=$tri";}
		if(isset($order_by)) {echo "&amp;order_by=$order_by";}
		echo add_token_in_url()."'>Réinitialiser cumul</a>";
		echo "</p>\n";
	} else {
		echo "<p class='small'><i>Aucune action disponible</i><br />(l'alerte n'est pas liée à un utilisateur du système)</p>\n";
	}
	echo "</td>\n";
	echo "</tr>\n";
}
echo "</table>\n";


echo "<a name='utilisateurs_en_observation'></a>\n";
if(mysql_num_rows($req_observation)==0) {
	echo "<p>Aucun utilisateur n'est en observation.</p>\n";
}
else {
	echo "<p>".mysql_num_rows($req_observation)." utilisateur(s) en observation&nbsp;:</p>\n";
	//echo "<table class='menu'>\n";
	echo "<table class='boireaus'>\n";
	echo "<tr>\n";
	echo "<th colspan='3'>Utilisateurs en observation</th>\n";
	echo "</tr>\n";
	
	echo "<tr>\n";
	echo "<th style='width: 200px;'>Utilisateur</th>\n";
	echo "<th style='width: 50px;'>Cumul actuel</th>\n";
	echo "<th style='width: auto;'>Actions</th>\n";
	echo "</tr>\n";
	
	/*
	$sql="SELECT u.login, u.nom, u.prenom, u.statut, u.etat, u.niveau_alerte FROM utilisateurs u WHERE (u.observation_securite = '1') ORDER BY u.niveau_alerte DESC;";
	$req_observation = mysql_query($sql);
	if (!$req_observation) {echo mysql_error();}
	*/
	$alt=1;
	while ($row = mysql_fetch_object($req_observation)) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover'>\n";
		echo "<td>\n";
		echo $row->login ." - ".$row->statut ."<br/>\n";
		echo "<b>".$row->prenom . " " . $row->nom."</b>";
		echo "<br/>\n";
		if ($row->etat == "actif") {
			echo "Compte actif";
		} else {
			echo "Compte désactivé";
		}
		echo "</td>\n";
		echo "<td>".$row->niveau_alerte."</td>\n";
		echo "<td>\n";
			echo "<p>\n";
			if ($row->etat == "actif") {
				echo "<a style='padding: 2px;' href='security_panel.php?action=desactiver&amp;user_login=".$row->login;
				if(isset($tri)) {echo "&amp;tri=$tri";}
				if(isset($order_by)) {echo "&amp;order_by=$order_by";}
				echo add_token_in_url()."'>Désactiver le compte</a>";
				echo "<br />";
			} else {
				echo "<a style='padding: 2px;' href='security_panel.php?action=activer&amp;user_login=".$row->login;
				if(isset($tri)) {echo "&amp;tri=$tri";}
				if(isset($order_by)) {echo "&amp;order_by=$order_by";}
				echo add_token_in_url()."'>Réactiver le compte</a>";
				echo "<br />";
			}
			echo "<a style='padding: 2px;' href='security_panel.php?action=stop_observation&amp;user_login=".$row->login;
			if(isset($tri)) {echo "&amp;tri=$tri";}
			if(isset($order_by)) {echo "&amp;order_by=$order_by";}
			echo add_token_in_url()."'>Retirer l'observation</a>";
			echo "<br />";
	
			echo "<a style='padding: 2px;' href='security_panel.php?action=reinit_cumul&amp;user_login=".$row->login;
			if(isset($tri)) {echo "&amp;tri=$tri";}
			if(isset($order_by)) {echo "&amp;order_by=$order_by";}
			echo add_token_in_url()."'>Réinitialiser cumul</a>";
			echo "</p>\n";
		echo "</td>\n";
		echo "</tr>\n";
	}
	echo "</table>\n";
}


echo "<a name='utilisateurs_desactives'></a>\n";
if(mysql_num_rows($req_desactive)==0) {
	echo "<p>Aucun utilisateur avec alerte dans cette page n'est désactivé.</p>\n";
}
else {
	echo "<p>".mysql_num_rows($req_desactive)." utilisateur(s) avec alerte dans cette page sont désactivés&nbsp;:</p>\n";
	//echo "<table class='menu'>\n";
	echo "<table class='boireaus'>\n";
	echo "<tr>\n";
	echo "<th colspan='3'>Utilisateurs désactivés</th>\n";
	echo "</tr>\n";
	
	echo "<tr>\n";
	echo "<th style='width: 200px;'>Utilisateur</th>\n";
	echo "<th style='width: 50px;'>Cumul actuel</th>\n";
	echo "<th style='width: auto;'>Actions</th>\n";
	echo "</tr>\n";
	
	/*
	$sql="SELECT u.login, u.nom, u.prenom, u.statut, u.etat, u.niveau_alerte FROM utilisateurs u WHERE (u.observation_securite = '1') ORDER BY u.niveau_alerte DESC;";
	$req_observation = mysql_query($sql);
	if (!$req_observation) {echo mysql_error();}
	*/
	$alt=1;
	while ($row = mysql_fetch_object($req_desactive)) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover'>\n";
		echo "<td>\n";
		echo $row->login ." - ".$row->statut ."<br/>\n";
		echo "<b>".$row->prenom . " " . $row->nom."</b>";
		echo "<br/>\n";
		if ($row->etat == "actif") {
			echo "Compte actif";
		} else {
			echo "Compte désactivé";
		}
		echo "</td>\n";
		echo "<td>".$row->niveau_alerte."</td>\n";
		echo "<td>\n";
			echo "<p>\n";
			if ($row->etat == "actif") {
				echo "<a style='padding: 2px;' href='security_panel.php?action=desactiver&amp;user_login=".$row->login;
				if(isset($tri)) {echo "&amp;tri=$tri";}
				if(isset($order_by)) {echo "&amp;order_by=$order_by";}
				echo add_token_in_url()."'>Désactiver le compte</a>";
				echo "<br />";
			} else {
				echo "<a style='padding: 2px;' href='security_panel.php?action=activer&amp;user_login=".$row->login;
				if(isset($tri)) {echo "&amp;tri=$tri";}
				if(isset($order_by)) {echo "&amp;order_by=$order_by";}
				echo add_token_in_url()."'>Réactiver le compte</a>";
				echo "<br />";
			}
			echo "<a style='padding: 2px;' href='security_panel.php?action=stop_observation&amp;user_login=".$row->login;
			if(isset($tri)) {echo "&amp;tri=$tri";}
			if(isset($order_by)) {echo "&amp;order_by=$order_by";}
			echo add_token_in_url()."'>Retirer l'observation</a>";
			echo "<br />";

			echo "<a style='padding: 2px;' href='security_panel.php?action=reinit_cumul&amp;user_login=".$row->login;
			if(isset($tri)) {echo "&amp;tri=$tri";}
			if(isset($order_by)) {echo "&amp;order_by=$order_by";}
			echo add_token_in_url()."'>Réinitialiser cumul</a>";
			echo "</p>\n";
		echo "</td>\n";
		echo "</tr>\n";
	}
	echo "</table>\n";
	echo "<p><br /></p>\n";
}

require("../lib/footer.inc.php");
?>