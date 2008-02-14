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
			/*
			$quels_parents = mysql_query("SELECT distinct(r.pers_id), r.nom, r.prenom, r.civilite, r.mel " .
					"FROM resp_pers r, responsables2 re, classes c, j_eleves_classes jec, eleves e WHERE (" .
					"r.login = '' AND " .
					"r.pers_id = re.pers_id AND " .
					"re.ele_id = e.ele_id AND " .
					"e.login = jec.login AND " .
					"jec.id_classe = c.id)");
			*/
			$sql = "SELECT distinct(r.pers_id), r.nom, r.prenom, r.civilite, r.mel " .
					"FROM resp_pers r, responsables2 re, classes c, j_eleves_classes jec, eleves e WHERE (" .
					"r.login = '' AND " .
					"r.pers_id = re.pers_id AND " .
					"re.ele_id = e.ele_id AND " .
					"e.login = jec.login AND " .
					"jec.id_classe = c.id AND " .
					"(re.resp_legal='1' OR re.resp_legal='2'))";
			//echo "$sql<br />";
			$quels_parents = mysql_query($sql);
			if (!$quels_parents) $msg .= mysql_error();
		} elseif (is_numeric($_POST['classe'])) {
			/*
			$quels_parents = mysql_query("SELECT distinct(r.pers_id), r.nom, r.prenom, r.civilite, r.mel " .
					"FROM resp_pers r, responsables2 re, classes c, j_eleves_classes jec, eleves e WHERE (" .
					"r.login = '' AND " .
					"r.pers_id = re.pers_id AND " .
					"re.ele_id = e.ele_id AND " .
					"e.login = jec.login AND " .
					"jec.id_classe = '" . $_POST['classe']."')");
			*/
			$sql="SELECT distinct(r.pers_id), r.nom, r.prenom, r.civilite, r.mel " .
					"FROM resp_pers r, responsables2 re, classes c, j_eleves_classes jec, eleves e WHERE (" .
					"r.login = '' AND " .
					"r.pers_id = re.pers_id AND " .
					"re.ele_id = e.ele_id AND " .
					"e.login = jec.login AND " .
					"jec.id_classe = '" . $_POST['classe']."' AND " .
					"(re.resp_legal='1' OR re.resp_legal='2'))";
			//echo "$sql<br />";
			$quels_parents = mysql_query($sql);
			if (!$quels_parents) $msg .= mysql_error();
		} else {
			$error = true;
			$msg .= "Vous devez sélectionner au moins une classe !<br />";
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
		// Cas particlulier de LCS : les responsables ne disposent pas d'un compte leur permettant de s'identifier sur le SSO. Dans ce cas, il accéderont à GEPI localement grâce à un identifiant et un mot de passe générés par GEPI."
    if ($nb_comptes > 0 AND getSettingValue('use_sso') != "cas" AND getSettingValue("use_sso") != "lemon" AND getSettingValue("use_sso") != "ldap_scribe") {
    	if ($create_mode == "individual") {
				// Mode de création de compte individuel. On fait un lien spécifique pour la fiche de bienvenue
				$msg .= "<br/><a target='_blank' href='reset_passwords.php?user_login=".$reg_login."'>";
			} else {
				// On est ici en mode de création par classe
				// Si on opère sur toutes les classes, on ne spécifie aucune classe
				// =====================
				// MODIF: boireaus 20071102
				if ($_POST['classe'] == "all") {
				    $msg .= "<br/><a target='_blank' href='reset_passwords.php?user_status=responsable&amp;mode=html&amp;creation_comptes_classe=y'>Imprimer la ou les fiche(s) de bienvenue (Impression HTML)</a>";
					$msg .= "<br/><a target='_blank' href='reset_passwords.php?user_status=responsable&amp;mode=csv&amp;creation_comptes_classe=y'>Imprimer la ou les fiche(s) de bienvenue (Export CSV)</a>";
				} elseif (is_numeric($_POST['classe'])) {
					$msg .= "<br/><a target='_blank' href='reset_passwords.php?user_status=responsable&amp;user_classe=".$_POST['classe']."&amp;mode=html&amp;creation_comptes_classe=y'>Imprimer la ou les fiche(s) de bienvenue (Impression HTML)</a>";
					$msg .= "<br/><a target='_blank' href='reset_passwords.php?user_status=responsable&amp;user_classe=".$_POST['classe']."&amp;mode=csv&amp;creation_comptes_classe=y'>Imprimer la ou les fiche(s) de bienvenue (Export CSV)</a>";
				}
				// =====================
			}
			// =====================
			// MODIF: boireaus 20071102
			//$msg .= "<br/>Vous devez effectuer cette opération maintenant !";
			$msg .= "<br/>Pour initialiser le(s) mot(s) de passe, vous devez suivre ce lien maintenant !";
			// =====================
		}
	}
}

//**************** EN-TETE *****************
$titre_page = "Créer des comptes d'accès responsables";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<p class=bold><a href="edit_responsable.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>
</p>
<?php

$afficher_tous_les_resp=isset($_POST['afficher_tous_les_resp']) ? $_POST['afficher_tous_les_resp'] : "n";
$critere_recherche=isset($_POST['critere_recherche']) ? $_POST['critere_recherche'] : "";
$critere_recherche=ereg_replace("[^a-zA-ZÀÄÂÉÈÊËÎÏÔÖÙÛÜ½¼Ççàäâéèêëîïôöùûü_ -]", "", $critere_recherche);

//$quels_parents = mysql_query("SELECT * FROM resp_pers WHERE login='' ORDER BY nom,prenom");
//$quels_parents = mysql_query("SELECT * FROM resp_pers WHERE login='' ORDER BY nom,prenom");
$sql="SELECT * FROM resp_pers rp WHERE rp.login=''";

// Effectif total sans login:
$nb = mysql_num_rows(mysql_query($sql));

if($afficher_tous_les_resp!='y'){
	if($critere_recherche!=""){
		$sql.=" AND rp.nom like '%".$critere_recherche."%'";
	}
}
$sql.=" ORDER BY rp.nom, rp.prenom";

// Effectif sans login avec filtrage sur le nom:
$nb1 = mysql_num_rows(mysql_query($sql));

if($afficher_tous_les_resp!='y'){
	if($critere_recherche==""){
		$sql.=" LIMIT 20";
	}
}
//echo "$sql<br />\n";
$quels_parents = mysql_query($sql);


/*
$sql="SELECT rp.*, e.nom as ele_nom, e.prenom as ele_prenom,c.classe
						FROM resp_pers rp, responsables2 r, eleves e, j_eleves_classes jec, classes c
						WHERE rp.login='' AND
							rp.pers_id=r.pers_id AND
							r.ele_id=e.ele_id AND
							(re.resp_legal='1' OR re.resp_legal='2') AND
							jec.login=e.login AND
							jec.id_classe=c.id
						ORDER BY rp.nom,rp.prenom";
$quels_parents = mysql_query($sql);
*/

//$nb = mysql_num_rows($quels_parents);

// Effectif sans login avec filtrage sur le nom et limitation à un max de 20:
$nb2 = mysql_num_rows($quels_parents);

if($nb==0){
	echo "<p>Tous les responsables ont un login.</p>\n";
}
else{
	//echo "<p>Les $nb responsables ci-dessous n'ont pas encore de compte utilisateur.</p>\n";
	echo "<p>$nb responsables n'ont pas encore de compte utilisateur.</p>\n";

	if (getSettingValue("mode_generation_login") == null) {
		echo "<p><b>ATTENTION !</b> Vous n'avez pas défini le mode de génération des logins. Allez sur la page de <a href='../gestion/param_gen.php'>gestion générale</a> pour définir le mode que vous souhaitez utiliser. Par défaut, les logins seront générés au format pnom tronqué à 8 caractères (ex: ADURANT).</p>\n";
	}
	// Cas particlulier de LCS : les responsables ne disposent pas d'un compte leur permettant de s'identifier sur le SSO. Dans ce cas, il accéderont à GEPI localement grâce à un identifiant et un mot de passe générés par GEPI."
  if ((getSettingValue('use_sso') == "cas" OR getSettingValue("use_sso") == "lemon"  OR getSettingValue("use_sso") == "ldap_scribe")) {
		echo "<p><b>Note :</b> Vous utilisez une authentification externe à Gepi (SSO). Aucun mot de passe ne sera donc assigné aux utilisateurs que vous vous apprêté à créer. Soyez certain de générer les login selon le même format que pour votre source d'authentification SSO.</p>\n";
	}
	if (getSettingValue('use_sso') == "lcs") {
		echo "<p><b>Note :</b> Vous utilisez une authentification externe à Gepi (LCS). Les responsables ne disposent pas d'un compte leur permettant de s'identifier sur le SSO. Par conséquent, il accéderont à GEPI localement grâce à un identifiant et un mot de passe générés par GEPI.
    <br /><b>Remarque</b> : l'adresse pour se connecter localement est du type : http://mon.site.fr/gepi/login.php?local=y (ne pas omettre \"<b>?local=y</b>\").</p>\n";
	}

	echo "<p><b>Créer des comptes par lot</b> :</p>\n";
	echo "<blockquote>\n";
	echo "<p>Sélectionnez une classe ou bien l'ensemble des classes puis cliquez sur 'valider'.</p>\n";
	echo "<form action='create_responsable.php' method='post'>\n";
	echo "<input type='hidden' name='mode' value='classe' />\n";
	//===========================
	// AJOUT: boireaus 20071102
	echo "<input type='hidden' name='creation_comptes_classe' value='y' />\n";
	//===========================
	echo "<select name='classe' size='1'>\n";
	echo "<option value='none'>Sélectionnez une classe</option>\n";
	echo "<option value='all'>Toutes les classes</option>\n";

	$quelles_classes = mysql_query("SELECT id,classe FROM classes ORDER BY classe");
	while ($current_classe = mysql_fetch_object($quelles_classes)) {
		echo "<option value='".$current_classe->id."'>".$current_classe->classe."</option>\n";
	}
	echo "</select>\n";
	echo "<input type='submit' name='Valider' value='Valider' />\n";
	echo "</form>\n";
	echo "</blockquote>\n";

	//echo "<br />\n";
	echo "<p><b>Créer des comptes individuellement</b> :</p>\n";
	echo "<blockquote>\n";

	echo "<p>";
	if(($afficher_tous_les_resp!='y')&&($critere_recherche=="")){
		echo "Au plus $nb2 responsables sont affichés ci-dessous (<i>pour limiter le temps de chargement de la page</i>).<br />\n";
	}
	echo "Utilisez le formulaire de recherche pour adapter la recherche.";
	echo "</p>\n";

	//===================================
	//echo "<div style='border:1px solid black;'>\n";
	echo "<form enctype='multipart/form-data' name='form_rech' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	echo "<table style='border:1px solid black;'>\n";
	echo "<tr>\n";
	echo "<td valign='top' rowspan='3'>\n";
	echo "Filtrage:";
	echo "</td>\n";
	echo "<td>\n";
	echo "<input type='submit' name='filtrage' value='Afficher' /> les responsables sans login dont le <b>nom</b> contient: ";
	echo "<input type='text' name='critere_recherche' value='$critere_recherche' />\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td>\n";
	echo "ou";
	echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td>\n";
	echo "<input type='button' name='afficher_tous' value='Afficher tous les responsables sans login' onClick=\"document.getElementById('afficher_tous_les_resp').value='y'; document.form_rech.submit();\" />\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<input type='hidden' name='afficher_tous_les_resp' id='afficher_tous_les_resp' value='n' />\n";
	echo "</form>\n";
	//echo "</div>\n";
	//===================================
	echo "<br />\n";

	echo "<p>Cliquez sur le bouton 'Créer' d'un responsable pour créer un compte associé.</p>\n";

	echo "<table class='boireaus'>\n";
	$alt=1;
	while ($current_parent = mysql_fetch_object($quels_parents)) {

		$sql="SELECT DISTINCT e.ele_id, e.nom, e.prenom, c.classe, r.resp_legal
				FROM responsables2 r, eleves e, j_eleves_classes jec, classes c
				WHERE r.pers_id='".$current_parent->pers_id."' AND
					(r.resp_legal='1' OR r.resp_legal='2') AND
					r.ele_id=e.ele_id AND
					jec.login=e.login AND
					jec.id_classe=c.id";
		//echo "$sql<br />";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0){
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
				//echo "<td valign='top'>\n";
				echo "<td>\n";
				echo "<form action='create_responsable.php' method='post'>\n";
				echo "<input type='hidden' name='mode' value='individual' />\n";
				echo "<input type='hidden' name='pers_id' value='".$current_parent->pers_id."' />\n";
				echo "<input type='submit' value='Créer' />\n";
				echo "</form>\n";
				echo "<td>".strtoupper($current_parent->nom)." ".ucfirst(strtolower($current_parent->prenom))."</td>\n";

				//echo "<td>Responsable légal de:</td>\n";
				echo "<td>\n";
				while($lig_ele=mysql_fetch_object($test)){
					echo "Responsable légal $lig_ele->resp_legal de ".ucfirst(strtolower($lig_ele->prenom))." ".strtoupper($lig_ele->nom)." (<i>$lig_ele->classe</i>)<br />\n";
				}
				echo "</td>\n";
			echo "</tr>\n";
		}
	}
	echo "</table>\n";
	echo "</blockquote>\n";
}
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>