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

// Si on est en traitement par lot, on sÈlectionne tout de suite la liste des utilisateurs impliquÈs
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
		$msg .= "Vous devez sÈlectionner au moins une classe !<br />";
	}
}

// Trois actions sont possibles depuis cette page : activation, dÈsactivation et suppression.
// L'Èdition se fait directement sur la page de gestion des responsables

if ($action == "rendre_inactif") {
	// DÈsactivation d'utilisateurs actifs
	if ($mode == "individual") {
		// DÈsactivation pour un utilisateur unique
		$test = mysql_result(mysql_query("SELECT count(login) FROM utilisateurs WHERE (login = '" . $_GET['parent_login']."' AND etat = 'actif')"), 0);
		if ($test == "0") {
			$msg .= "Erreur lors de la dÈsactivation de l'utilisateur : celui-ci n'existe pas ou bien est dÈj‡ inactif.";
		} else {
			$res = mysql_query("UPDATE utilisateurs SET etat='inactif' WHERE (login = '".$_GET['parent_login']."')");
			if ($res) {
				$msg .= "L'utilisateur ".$_GET['parent_login'] . " a ÈtÈ dÈsactivÈ.";
			} else {
				$msg .= "Erreur lors de la dÈsactivation de l'utilisateur.";
			}
		}
	} elseif ($mode == "classe" and !$error) {
		// Pour tous les parents qu'on a dÈj‡ sÈlectionnÈs un peu plus haut, on dÈsactive les comptes
		while ($current_parent = mysql_fetch_object($quels_parents)) {
			$test = mysql_result(mysql_query("SELECT count(login) FROM utilisateurs WHERE login = '" . $current_parent->login ."'"), 0);
			if ($test > 0) {
				// L'utilisateur existe bien dans la tables utilisateurs, on dÈsactive
				$res = mysql_query("UPDATE utilisateurs SET etat = 'inactif' WHERE login = '" . $current_parent->login . "'");
				if (!$res) {
					$msg .= "Erreur lors de la dÈsactivation du compte ".$current_parent->login."<br />";
				} else {
					$nb_comptes++;
				}
			}
		}
		$msg .= "$nb_comptes comptes ont ÈtÈ dÈsactivÈs.";
	}
} elseif ($action == "rendre_actif") {
	// Activation d'utilisateurs prÈalablement dÈsactivÈs
	if ($mode == "individual") {
		// Activation pour un utilisateur unique
		$test = mysql_result(mysql_query("SELECT count(login) FROM utilisateurs WHERE (login = '" . $_GET['parent_login']."' AND etat = 'inactif')"), 0);
		if ($test == "0") {
			$msg .= "Erreur lors de la dÈsactivation de l'utilisateur : celui-ci n'existe pas ou bien est dÈj‡ actif.";
		} else {
			$res = mysql_query("UPDATE utilisateurs SET etat='actif' WHERE (login = '".$_GET['parent_login']."')");
			if ($res) {
				$msg .= "L'utilisateur ".$_GET['parent_login'] . " a ÈtÈ activÈ.";
			} else {
				$msg .= "Erreur lors de l'activation de l'utilisateur.";
			}
		}
	} elseif ($mode == "classe") {
		// Pour tous les parents qu'on a dÈj‡ sÈlectionnÈs un peu plus haut, on dÈsactive les comptes
		while ($current_parent = mysql_fetch_object($quels_parents)) {
			$test = mysql_result(mysql_query("SELECT count(login) FROM utilisateurs WHERE login = '" . $current_parent->login ."'"), 0);
			if ($test > 0) {
				// L'utilisateur existe bien dans la tables utilisateurs, on dÈsactive
				$res = mysql_query("UPDATE utilisateurs SET etat = 'actif' WHERE login = '" . $current_parent->login . "'");
				if (!$res) {
					$msg .= "Erreur lors de l'activation du compte ".$current_parent->login."<br />";
				} else {
					$nb_comptes++;
				}
			}
		}
		$msg .= "$nb_comptes comptes ont ÈtÈ activÈs.";
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
				$msg .= "L'utilisateur ".$_GET['parent_login'] . " a ÈtÈ supprimÈ.";
				$res2 = mysql_query("UPDATE resp_pers SET login='' WHERE login = '".$_GET['parent_login'] . "'");
			} else {
				$msg .= "Erreur lors de la suppression de l'utilisateur.";
			}
		}
	} elseif ($mode == "classe") {
		// Pour tous les parents qu'on a dÈj‡ sÈlectionnÈs un peu plus haut, on dÈsactive les comptes
		while ($current_parent = mysql_fetch_object($quels_parents)) {
			$test = mysql_result(mysql_query("SELECT count(login) FROM utilisateurs WHERE login = '" . $current_parent->login ."'"), 0);
			if ($test > 0) {
				// L'utilisateur existe bien dans la tables utilisateurs, on dÈsactive
				$res = mysql_query("DELETE FROM utilisateurs WHERE login = '" . $current_parent->login . "'");
				if (!$res) {
					$msg .= "Erreur lors de l'activation du compte ".$current_parent->login."<br />";
				} else {
					$res = mysql_query("UPDATE resp_pers SET login = '' WHERE login = '" . $current_parent->login ."'");
					$nb_comptes++;
				}
			}
		}
		$msg .= "$nb_comptes comptes ont ÈtÈ supprimÈs.";
	}
} elseif ($action == "reinit_password") {
	if ($mode != "classe") {
		$msg .= "Erreur : Vous devez sÈlectionner une classe.";
	} elseif ($mode == "classe") {
		if ($_POST['classe'] == "all") {
			$msg .= "Vous allez rÈinitialiser les mots de passe de tous les utilisateurs ayant le statut 'responsable'.<br />Si vous Ítes vraiment s˚r de vouloir effectuer cette opÈration, cliquez sur le lien ci-dessous :";
			$msg .= "<br /><a href=\"reset_passwords.php?user_status=responsable&amp;mode=html\" target='_blank'>RÈinitialiser les mots de passe (Impression HTML)</a>";
			$msg .= "<br /><a href=\"reset_passwords.php?user_status=responsable&amp;mode=csv\" target='_blank'>RÈinitialiser les mots de passe (Export CSV)</a>";
		} else if (is_numeric($_POST['classe'])) {
			$msg .= "Vous allez rÈinitialiser les mots de passe de tous les utilisateurs ayant le statut 'responsable' pour cette classe.<br />Si vous Ítes vraiment s˚r de vouloir effectuer cette opÈration, cliquez sur le lien ci-dessous :";
			$msg .= "<br /><a href=\"reset_passwords.php?user_status=responsable&amp;user_classe=".$_POST['classe']."&amp;mode=html\" target='_blank'>RÈinitialiser les mots de passe (Impression HTML)</a>";
			$msg .= "<br /><a href=\"reset_passwords.php?user_status=responsable&amp;user_classe=".$_POST['classe']."&amp;mode=csv\" target='_blank'>RÈinitialiser les mots de passe (Export CSV)</a>";
		}
	}
}

//**************** EN-TETE *****************
$titre_page = "Modifier des comptes responsables";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<p class=bold>
<a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> |
<a href="create_responsable.php"> Ajouter de nouveaux comptes</a>
<?php
	if ((getSettingValue('use_sso') != "cas" and getSettingValue("use_sso") != "lemon" and getSettingValue('use_sso') != "lcs" and getSettingValue("use_sso") != "ldap_scribe") OR $block_sso) {
	// Eric Faut-il garder la ligne ?
	//  echo " | <a href=\"reset_passwords.php?user_status=responsable\" onclick=\"javascript:return confirm(' tes-vous s˚r de vouloir effectuer cette opÈration ?\\n Celle-ci est irrÈversible, et rÈinitialisera les mots de passe de tous les utilisateurs ayant le statut \'responsable\' et marquÈs actifs, avec un mot de passe alpha-numÈrique gÈnÈrÈ alÈatoirement.\\n En cliquant sur OK, vous lancerez la procÈdure, qui gÈnËrera une page contenant les fiches-bienvenue ‡ imprimer immÈdiatement pour distribution aux utilisateurs concernÈs.')\">RÈinitialiser mots de passe</a>";
	}

	$quels_parents = mysql_query("SELECT u.*, r.pers_id FROM utilisateurs u, resp_pers r WHERE (u.statut = 'responsable' AND r.login = u.login) ORDER BY u.nom,u.prenom");
	if(mysql_num_rows($quels_parents)==0){
		echo "<p>Aucun compte responsable n'existe encore.<br />Vous pouvez ajouter des comptes responsables ‡ l'aide du lien ci-dessus.</p>\n";
		require("../lib/footer.inc.php");
		die;
	}
	echo "</p>\n";

	//echo "<p><b>Actions par lot</b> :";

	echo "<form action='edit_responsable.php' method='post'>\n";

	echo "<p style='font-weight:bold;'>Actions par lot pour les comptes responsables existants : </p>\n";
	echo "<blockquote>\n";
	echo "<p>\n";
	echo "<select name='classe' size='1'>\n";
	echo "<option value='none'>SÈlectionnez une classe</option>\n";
	echo "<option value='all'>Toutes les classes</option>\n";

	//$quelles_classes = mysql_query("SELECT id,classe FROM classes ORDER BY classe");
	$quelles_classes = mysql_query("SELECT DISTINCT c.id,c.classe FROM classes c,
																		j_eleves_classes jec,
																		eleves e,
																		responsables2 r,
																		resp_pers rp,
																		utilisateurs u
										WHERE jec.login=e.login AND
												e.ele_id=r.ele_id AND
												r.pers_id=rp.pers_id AND
												rp.login=u.login AND
												jec.id_classe=c.id
										ORDER BY classe");

	while ($current_classe = mysql_fetch_object($quelles_classes)) {
		echo "<option value='".$current_classe->id."'>".$current_classe->classe."</option>\n";
	}
	echo "</select>\n";

	echo "<br />\n";

	echo "<input type='hidden' name='mode' value='classe' />\n";
	echo "<input type='radio' name='action' value='rendre_inactif' /> Rendre inactif\n";
	echo "<input type='radio' name='action' value='rendre_actif' style='margin-left: 20px;'/> Rendre actif \n";
	echo "<input type='radio' name='action' value='reinit_password' style='margin-left: 20px;'/> RÈinitialiser mots de passe\n";
	echo "<input type='radio' name='action' value='supprimer' style='margin-left: 20px;' /> Supprimer<br />\n";
	echo "&nbsp;<input type='submit' name='Valider' value='Valider' />\n";
	echo "</p>\n";

	echo "</blockquote>\n";
	echo "</form>\n";


	echo "<p><br /></p>\n";

	echo "<p><b>Liste des comptes responsables existants</b> :</p>\n";
	echo "<blockquote>\n";

	$afficher_tous_les_resp=isset($_POST['afficher_tous_les_resp']) ? $_POST['afficher_tous_les_resp'] : "n";
	$critere_recherche=isset($_POST['critere_recherche']) ? $_POST['critere_recherche'] : "";
	$critere_recherche=ereg_replace("[^a-zA-Z¿ƒ¬…» ÀŒœ‘÷Ÿ€‹Ωº«Á‡‰‚ÈËÍÎÓÔÙˆ˘˚¸_ -]", "", $critere_recherche);
  	$critere_recherche_login=isset($_POST['critere_recherche_login']) ? $_POST['critere_recherche_login'] : "";
	$critere_recherche_login=ereg_replace("[^a-zA-Z¿ƒ¬…» ÀŒœ‘÷Ÿ€‹Ωº«Á‡‰‚ÈËÍÎÓÔÙˆ˘˚¸_ -]", "", $critere_recherche_login);

	//====================================
	
	echo "<form enctype='multipart/form-data' name='form_rech' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	echo "<table style='border:1px solid black;'>\n";
	echo "<tr>\n";
	echo "<td valign='top' rowspan='4'>\n";
	echo "Filtrage:";
	echo "</td>\n";
	echo "<td>\n";
	echo "<input type='submit' name='filtrage' value='Afficher' /> les responsables ayant un login dont le <b>nom</b> contient: ";
	echo "<input type='text' name='critere_recherche' value='$critere_recherche' />\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td>\n";
	echo "<input type='submit' name='filtrage' value='Afficher' /> les responsables ayant un <b>login</b> qui contient: ";
	echo "<input type='text' name='critere_recherche_login' value='$critere_recherche_login' />\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td>\n";
	echo "ou";
	echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td>\n";
	echo "<input type='button' name='afficher_tous' value='Afficher tous les responsables ayant un login' onClick=\"document.getElementById('afficher_tous_les_resp').value='y'; document.form_rech.submit();\" />\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<input type='hidden' name='afficher_tous_les_resp' id='afficher_tous_les_resp' value='n' />\n";
	echo "</form>\n";
	//====================================
	echo "<br />\n";

?>
<!--table border="1"-->
<table class='boireaus'>
<tr>
	<th>Identifiant</th>
	<th>Nom PrÈnom</th>
	<th>Responsable de</th>
	<th>Etat</th>
	<th>Actions</th>
</tr>
<?php
//$quels_parents = mysql_query("SELECT u.*, r.pers_id FROM utilisateurs u, resp_pers r WHERE (u.statut = 'responsable' AND r.login = u.login) ORDER BY u.nom,u.prenom");

$sql="SELECT u.*, r.pers_id FROM utilisateurs u, resp_pers r WHERE (u.statut = 'responsable' AND r.login = u.login";

if($afficher_tous_les_resp!='y'){
	if($critere_recherche!=""){
		$sql.=" AND u.nom like '%".$critere_recherche."%'";
	} else {
		if($critere_recherche_login!=""){
			$sql.=" AND u.login like '%".$critere_recherche_login."%'";
		}
    }
}
$sql.=") ORDER BY u.nom,u.prenom";

// Effectif sans login avec filtrage sur le nom:
$nb1 = mysql_num_rows(mysql_query($sql));

if($afficher_tous_les_resp!='y'){
	if($critere_recherche==""){
		$sql.=" LIMIT 20";
	}
}

$quels_parents = mysql_query($sql);
$alt=1;
while ($current_parent = mysql_fetch_object($quels_parents)) {
	$alt=$alt*(-1);
	echo "<tr class='lig$alt' style='text-align:center;'>\n";
		echo "<td>";
			echo "<a href='../responsables/modify_resp.php?pers_id=".$current_parent->pers_id."'>".$current_parent->login."</a>";
		echo "</td>\n";
		echo "<td>";
			echo $current_parent->nom . " " . $current_parent->prenom;
		echo "</td>\n";
		echo "<td>";
		$sql="SELECT DISTINCT e.nom,e.prenom,c.classe FROM eleves e,
												j_eleves_classes jec,
												classes c,
												responsables2 r
											WHERE e.login=jec.login AND
												jec.id_classe=c.id AND
												r.ele_id=e.ele_id AND
												r.pers_id='$current_parent->pers_id'
											ORDER BY e.nom,e.prenom";
		$res_enfants=mysql_query($sql);
		//echo "$sql<br />";
		if(mysql_num_rows($res_enfants)==0){
			echo "<span style='color:red;'>Aucun ÈlËve</span>";
		}
		else{
			while($current_enfant=mysql_fetch_object($res_enfants)){
				echo ucfirst(strtolower($current_enfant->prenom))." ".strtoupper($current_enfant->nom)." (<i>".$current_enfant->classe."</i>)<br />\n";
			}
		}
		echo "</td>\n";
		echo "<td align='center'>";
			if ($current_parent->etat == "actif") {
				echo "<font color='green'>".$current_parent->etat."</font>";
				echo "<br />";
				echo "<a href='edit_responsable.php?action=rendre_inactif&amp;mode=individual&amp;parent_login=".$current_parent->login."'>DÈsactiver";
			} else {
				echo "<font color='red'>".$current_parent->etat."</font>";
				echo "<br />";
				echo "<a href='edit_responsable.php?action=rendre_actif&amp;mode=individual&amp;parent_login=".$current_parent->login."'>Activer";
			}
			echo "</a>";
		echo "</td>\n";
		echo "<td>";
		echo "<a href='edit_responsable.php?action=supprimer&amp;mode=individual&amp;parent_login=".$current_parent->login."' onclick=\"javascript:return confirm(' tes-vous s˚r de vouloir supprimer l\'utilisateur ?')\">Supprimer</a>";

		if($current_parent->etat == "actif"){
			echo "<br />";
			//echo "<a href=\"reset_passwords.php?user_login=".$current_parent->login."\" onclick=\"javascript:return confirm(' tes-vous s˚r de vouloir effectuer cette opÈration ?\\n Celle-ci est irrÈversible, et rÈinitialisera le mot de passe de l\'utilisateur avec un mot de passe alpha-numÈrique gÈnÈrÈ alÈatoirement.\\n En cliquant sur OK, vous lancerez la procÈdure, qui gÈnËrera une page contenant la fiche-bienvenue ‡ imprimer immÈdiatement pour distribution ‡ l\'utilisateur concernÈ.')\" target='change'>RÈinitialiser le mot de passe</a>";
			echo "RÈinitialiser le mot de passe : <a href=\"reset_passwords.php?user_login=".$current_parent->login."&amp;user_status=responsable&amp;mode=html\" onclick=\"javascript:return confirm(' tes-vous s˚r de vouloir effectuer cette opÈration ?\\n Celle-ci est irrÈversible, et rÈinitialisera le mot de passe de l\'utilisateur avec un mot de passe alpha-numÈrique gÈnÈrÈ alÈatoirement.\\n En cliquant sur OK, vous lancerez la procÈdure, qui gÈnËrera une page contenant la fiche-bienvenue ‡ imprimer immÈdiatement pour distribution ‡ l\'utilisateur concernÈ.')\" target='_blank'>AlÈatoirement</a>";
			echo " - <a href=\"change_pwd.php?user_login=".$current_parent->login."\" onclick=\"javascript:return confirm(' tes-vous s˚r de vouloir effectuer cette opÈration ?\\n Celle-ci rÈinitialisera le mot de passe de l\'utilisateur avec un mot de passe que vous choisirez.\\n En cliquant sur OK, vous lancerez une page qui vous demandera de saisir un mot de passe et de le valider.')\" target='_blank'>choisi </a>";
		}
		echo "</td>\n";
	echo "</tr>\n";
}
echo "</table>\n";
echo "</blockquote>\n";

?>

<?php
if (mysql_num_rows($quels_parents) == "0") {
	echo "<p>Pour crÈer de nouveaux comptes d'accËs associÈs aux responsables d'ÈlËves dÈfinis dans Gepi, vous devez cliquer sur le lien 'Ajouter de nouveaux comptes' ci-dessus.</p>";
}
require("../lib/footer.inc.php");
?>