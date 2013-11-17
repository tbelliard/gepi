<?php
/*
 *
 * Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// Initialisation des variables
$create_mode = isset($_POST["mode"]) ? $_POST["mode"] : NULL;

$_POST['reg_auth_mode'] = (!isset($_POST['reg_auth_mode']) OR !in_array($_POST['reg_auth_mode'], array("auth_locale", "auth_ldap", "auth_sso"))) ? "auth_locale" : $_POST['reg_auth_mode'];

// Passer à 'y' pour provoquer l'affichage des requetes:
$debug_create_resp="n";

if ($create_mode == "classe" OR $create_mode == "individual") {
	// On a une demande de création, on continue
	check_token();

	// On veut alimenter la variable $quels_parents avec un résultat mysql qui contient
	// la liste des parents pour lesquels on veut créer un compte
	$error = false;
	$msg = "";
	if ($create_mode == "individual") {
		//echo "grouik : ".$_POST['pers_id'];
		// $_POST['pers_id'] est filtré automatiquement contre les injections SQL, on l'utilise directement
		$sql="SELECT count(e.login) FROM eleves e, responsables2 re WHERE (e.ele_id = re.ele_id AND re.pers_id = '" . $_POST['pers_id'] ."')";
		if($debug_create_resp=="y") {echo "$sql<br />\n";}
		$test = mysqli_query($GLOBALS["mysqli"], $sql);
		if (old_mysql_result($test, 0) == "0") {
			$error = true;
			$msg .= "Erreur lors de la création de l'utilisateur : aucune association avec un élève n'a été trouvée !<br/>";
		} else {
			//$quels_parents = mysql_query("SELECT r.* FROM resp_pers r, responsables2 re WHERE (" .
			//$sql="SELECT DISTINCT(r.*) FROM resp_pers r, responsables2 re WHERE (" .
			$sql="SELECT DISTINCT r.* FROM resp_pers r, responsables2 re WHERE (" .
				"r.login = '' AND " .
				"r.pers_id = re.pers_id AND " .
				"re.pers_id = '" . $_POST['pers_id'] ."')";
			if($debug_create_resp=="y") {echo "$sql<br />\n";}
			$quels_parents = mysqli_query($GLOBALS["mysqli"], $sql);
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
			if($debug_create_resp=="y") {echo "$sql<br />\n";}
			$quels_parents = mysqli_query($GLOBALS["mysqli"], $sql);
			if (!$quels_parents) $msg .= ((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false));
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
			if($debug_create_resp=="y") {echo "$sql<br />\n";}
			$quels_parents = mysqli_query($GLOBALS["mysqli"], $sql);
			if (!$quels_parents) $msg .= ((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false));
		} else {
			$error = true;
			$msg .= "Vous devez sélectionner au moins une classe !<br />";
		}
	}

	if (!$error) {
		$nb_comptes = 0;
		while ($current_parent = mysqli_fetch_object($quels_parents)) {

			// Dans le cas où Gepi est intégré dans un ENT, on va chercher les logins
			if (getSettingValue("use_ent") == "y") {
				// Charge à l'organisme utilisateur de pourvoir à cette fonctionnalité
				// le code suivant n'est qu'une méthode proposée pour relier Gepi à un ENT
				$bx = 'oui';
				if (isset($bx) AND $bx == 'oui') {
					// On va chercher le login de l'utilisateur dans la table créée
					// C'est à ce niveau qu'il faut faire les modifications

					$sql_p = "SELECT login_u FROM ldap_bx
											WHERE nom_u = '".strtoupper($current_parent->nom)."'
											AND prenom_u = '".strtoupper($current_parent->prenom)."'
											AND statut_u = 'teacher'";
					if($debug_create_resp=="y") {echo "$sql_p<br />\n";}
					$query_p = mysqli_query($GLOBALS["mysqli"], $sql_p);
					$nbre = mysqli_num_rows($query_p);

					if ($nbre >= 1 AND $nbre < 2) {
						$reg_login = old_mysql_result($query_p, 0,"login_u");
					}
					else {
						// Il faudrait alors proposer une alternative à ce cas et permettre de chercher à la main le bon responsable dans la source
						//$reg_login = "erreur_".$k; // en attendant une solution viable, on génère le login du responsable
						$reg_login = generate_unique_login($current_parent->nom, $current_parent->prenom, getSettingValue("mode_generation_login_responsable"), getSettingValue("mode_generation_login_responsable_casse"));
					}
				}
			} else {
				// Création du compte utilisateur pour le responsable considéré
				//echo "\$reg_login = generate_unique_login($current_parent->nom, $current_parent->prenom, ".getSettingValue("mode_generation_login").");<br />\n";
				$reg_login = generate_unique_login($current_parent->nom, $current_parent->prenom, getSettingValue("mode_generation_login_responsable"), getSettingValue("mode_generation_login_responsable_casse"));
				// generate_unique_login() peut retourner 'false' en cas de pb
			}

			if(($reg_login)&&($reg_login!='')) {
				//check_token();

				// Si on a un accès LDAP en écriture, on créé le compte sur le LDAP
				// On ne procède que si le LDAP est configuré en écriture, qu'on a activé
				// l'auth LDAP ou SSO, et que c'est un de ces deux modes qui a été choisi pour cet utilisateur.
				if (LDAPServer::is_setup() && $gepiSettings["ldap_write_access"] == "yes" && ($session_gepi->auth_ldap || $session_gepi->auth_sso) && ($_POST['reg_auth_mode'] == 'auth_ldap' || $_POST['reg_auth_mode'] == 'auth_sso')) {
					$write_ldap = true;
					$write_ldap_success = false;
					// On tente de créer l'utilisateur sur l'annuaire LDAP
					$ldap_server = new LDAPServer();
					if ($ldap_server->test_user($_POST['new_login'])) {
						// L'utilisateur a été trouvé dans l'annuaire. On ne l'enregistre pas.
						$write_ldap_success = true;
						$msg.= "L'utilisateur n'a pas pu être ajouté à l'annuaire LDAP, car il y est déjà présent. Il va néanmoins être créé dans la base Gepi.";
					} else {
						$write_ldap_success = $ldap_server->add_user($reg_login, $current_parent->nom, $current_parent->prenom, $current_parent->mel, $current_parent->civilite, '', 'responsable');
					}
				} else {
					$write_ldap = false;
				}
	
				if (!$write_ldap || ($write_ldap && $write_ldap_success)) {
					$reg = true;
					if ($_POST['reg_auth_mode'] == "auth_locale") {
						$reg_auth = "gepi";
					} elseif ($_POST['reg_auth_mode'] == "auth_ldap") {
						$reg_auth = "ldap";
					} elseif ($_POST['reg_auth_mode'] == "auth_sso") {
						$reg_auth = "sso";
					}
					$sql="INSERT INTO utilisateurs SET " .
							"login = '" . $reg_login . "', " .
							"nom = '" . addslashes($current_parent->nom) . "', " .
							"prenom = '". addslashes($current_parent->prenom) ."', " .
							"password = '', " .
							"civilite = '" . $current_parent->civilite."', " .
							"email = '" . $current_parent->mel . "', " .
							"statut = 'responsable', " .
							"etat = 'actif', " .
							"auth_mode = '".$reg_auth."', " .
							"change_mdp = 'n'";
					if($debug_create_resp=="y") {echo "$sql<br />\n";}
					$reg = mysqli_query($GLOBALS["mysqli"], $sql);
	
					if (!$reg) {
						$msg .= "Erreur lors de la création du compte ".$reg_login."<br/>";
					} else {
						$sql="UPDATE resp_pers SET login = '" . $reg_login . "' WHERE (pers_id = '" . $current_parent->pers_id . "')";
						$reg2 = mysqli_query($GLOBALS["mysqli"], $sql);
						if($debug_create_resp=="y") {echo "$sql<br />\n";}
						//$msg.="$sql<br />";
						$nb_comptes++;

						// Ménage:
						$sql="SELECT id FROM infos_actions WHERE titre LIKE 'Nouveau responsable%($current_parent->pers_id)';";
						if($debug_create_resp=="y") {echo "$sql<br />\n";}
						$res_actions=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res_actions)>0) {
							while($lig_action=mysqli_fetch_object($res_actions)) {
								$menage=del_info_action($lig_action->id);
								if(!$menage) {$msg.="Erreur lors de la suppression de l'action en attente en page d'accueil à propos de $reg_login<br />";}
							}
						}
					}
				} else {
					$msg .= "Erreur lors de la création du compte ".$reg_login." : l'utilisateur n'a pas pu être créé sur l'annuaire LDAP.<br/>";
				}
			}
			else {
				$msg .= "Erreur lors de la génération d'un login pour '$current_parent->nom $current_parent->prenom'.<br/>";
			}
		}
		if ($nb_comptes == 1) {
			$msg .= "Un compte a été créé avec succès.<br/>";
		} elseif ($nb_comptes > 1) {
			$msg .= $nb_comptes." comptes ont été créés avec succès.<br/>";
		}

		// On propose de mettre à zéro les mots de passe et d'imprimer les fiches bienvenue seulement
		// si au moins un utilisateur a été créé et si on n'est pas en mode SSO (sauf accès LDAP en écriture).

		// nouveaux_seulement
		$chaine_nouveaux_seulement="";
		if((isset($_POST['nouveaux_seulement']))&&($_POST['nouveaux_seulement'])) {
			$chaine_nouveaux_seulement="&amp;nouveaux_seulement=y";
			saveSetting('creer_comptes_parents_nouveaux_seulement', 'y');
		}
		else {
			saveSetting('creer_comptes_parents_nouveaux_seulement', 'n');
		}

		if(isset($_POST['fiches_bienvenue_un_jeu_par_parent'])) {
			saveSetting('fiches_bienvenue_un_jeu_par_parent', 'y');
		}
		else {
			saveSetting('fiches_bienvenue_un_jeu_par_parent', 'n');
		}

		if ($nb_comptes > 0 && ($_POST['reg_auth_mode'] == "auth_locale" || $gepiSettings['ldap_write_access'] == "yes")) {
			if ($create_mode == "individual") {
				// Mode de création de compte individuel. On fait un lien spécifique pour la fiche de bienvenue
				$msg .= "<br/><a target='_blank' href='reset_passwords.php?user_login=".$reg_login.add_token_in_url()."'>";
				$msg .= "Pour initialiser le(s) mot(s) de passe, vous devez suivre ce lien maintenant !";
				$msg .= "</a>";
			} else {
				// On est ici en mode de création par classe
				// Si on opère sur toutes les classes, on ne spécifie aucune classe
				// =====================
				if ($_POST['classe'] == "all") {
				    $msg .= "<br/><a target='_blank' href='reset_passwords.php?user_status=responsable&amp;mode=html&amp;creation_comptes_classe=y".$chaine_nouveaux_seulement.add_token_in_url()."'>Imprimer la ou les fiche(s) de bienvenue (Impression HTML)</a>";
				    $msg .= " ou <a target='_blank' href='reset_passwords.php?user_status=responsable&amp;mode=html&amp;affiche_adresse_resp=y&amp;creation_comptes_classe=y".$chaine_nouveaux_seulement.add_token_in_url()."'>(Impression HTML avec adresse)</a>";
					$msg .= "<br/><a target='_blank' href='reset_passwords.php?user_status=responsable&amp;mode=csv&amp;creation_comptes_classe=y".$chaine_nouveaux_seulement.add_token_in_url()."'>Imprimer la ou les fiche(s) de bienvenue (Export CSV)</a>";
					$msg.="<br/>";
				} elseif (is_numeric($_POST['classe'])) {
					$msg .= "<br/><a target='_blank' href='reset_passwords.php?user_status=responsable&amp;user_classe=".$_POST['classe']."&amp;mode=html&amp;creation_comptes_classe=y".$chaine_nouveaux_seulement.add_token_in_url()."'>Imprimer la ou les fiche(s) de bienvenue (Impression HTML)</a>";
					$msg .= " ou <a target='_blank' href='reset_passwords.php?user_status=responsable&amp;user_classe=".$_POST['classe']."&amp;mode=html&amp;affiche_adresse_resp=y&amp;creation_comptes_classe=y".$chaine_nouveaux_seulement.add_token_in_url()."'>(Impression HTML avec adresse)</a>";
					$msg .= "<br/><a target='_blank' href='reset_passwords.php?user_status=responsable&amp;user_classe=".$_POST['classe']."&amp;mode=csv&amp;creation_comptes_classe=y".$chaine_nouveaux_seulement.add_token_in_url()."'>Imprimer la ou les fiche(s) de bienvenue (Export CSV)</a>";
					$msg.="<br/>";
				}
				// =====================
				$msg .= "Pour initialiser le(s) mot(s) de passe, vous devez suivre ce lien maintenant !";
			}
			// =====================
			// MODIF: boireaus 20071102
			//$msg .= "<br/>Vous devez effectuer cette opération maintenant !";
			//$msg .= "Pour initialiser le(s) mot(s) de passe, vous devez suivre ce lien maintenant !";
			// =====================
		} else {
			if ($nb_comptes > 0) {
				$msg .= "Vous avez créé un ou des comptes d'accès en mode SSO ou LDAP, mais sans avoir configuré l'accès LDAP en écriture. En conséquence, vous ne pouvez pas générer de mot de passe pour les utilisateurs.<br/>";
			}
		}
	}
}

//**************** EN-TETE *****************
$titre_page = "Créer des comptes d'accès responsables";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
?>
<p class='bold'><a href="edit_responsable.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>
</p>
<?php

//debug_var();

if(getSettingValue('auth_sso')=='lcs') {
	echo "<p style='color:red; text-indent: -7em; margin-left: 7em;'><b>ATTENTION&nbsp;:</b> Il convient de choisir pour les parents un format de login différent de celui des comptes des utilisateurs élèves et professeurs (<em>comptes de l'annuaire LDAP</em>).<br />Sinon, avec l'arrivée de nouveaux élèves en cours d'année, il peut arriver qu'un élève obtienne un login déjà attribué à un responsable dans Gepi.<br />Pour choisir le format de login des responsables, consultez la page <a href='../gestion/param_gen.php#format_login_resp'>Configuration générale</a>.<br />";
	$mode_generation_login_responsable=getSettingValue('mode_generation_login_responsable');
	echo "Le format de login responsable est actuellement <strong>$mode_generation_login_responsable</strong>";
	echo "</p>\n";
}

$afficher_tous_les_resp=isset($_POST['afficher_tous_les_resp']) ? $_POST['afficher_tous_les_resp'] : (isset($_GET['afficher_tous_les_resp']) ? $_GET['afficher_tous_les_resp'] : "n");
$critere_recherche=isset($_POST['critere_recherche']) ? $_POST['critere_recherche'] : (isset($_GET['critere_recherche']) ? $_GET['critere_recherche'] : "");
$critere_recherche=preg_replace("/[^a-zA-ZÀÄÂÉÈÊËÎÏÔÖÙÛÜ½¼Ççàäâéèêëîïôöùûü_ -]/u", "", $critere_recherche);

$critere_recherche_rl0=isset($_POST['critere_recherche_rl0']) ? $_POST['critere_recherche_rl0'] : (isset($_GET['critere_recherche_rl0']) ? $_GET['critere_recherche_rl0'] : "");
$critere_recherche_rl0=preg_replace("/[^a-zA-ZÀÄÂÉÈÊËÎÏÔÖÙÛÜ½¼Ççàäâéèêëîïôöùûü_ -]/u", "", $critere_recherche_rl0);
$filtrage_rl0=isset($_POST['filtrage_rl0']) ? $_POST['filtrage_rl0'] : (isset($_GET['filtrage_rl0']) ? $_GET['filtrage_rl0'] : NULL);
if(isset($filtrage_rl0)) {
	$critere_recherche=$critere_recherche_rl0;
	$mode_recherche='rl0';
}

//$quels_parents = mysql_query("SELECT * FROM resp_pers WHERE login='' ORDER BY nom,prenom");
//$quels_parents = mysql_query("SELECT * FROM resp_pers WHERE login='' ORDER BY nom,prenom");
//$sql="SELECT * FROM resp_pers rp WHERE rp.login=''";

// Effectif total sans login:
//$sql="SELECT 1=1 FROM resp_pers rp WHERE rp.login=''";
if((isset($mode_recherche))&&($mode_recherche=='rl0')) {
	$sql="SELECT DISTINCT rp.pers_id FROM resp_pers rp, responsables2 r WHERE rp.login='' AND rp.pers_id=r.pers_id AND r.resp_legal='0';";
}
else {
	$sql="SELECT DISTINCT rp.pers_id FROM resp_pers rp, responsables2 r WHERE rp.login='' AND rp.pers_id=r.pers_id AND (r.resp_legal='1' OR r.resp_legal='2');";
}
$nb = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], $sql));

$sql="SELECT * FROM resp_pers rp WHERE rp.login=''";

if($afficher_tous_les_resp!='y'){
	if($critere_recherche!=""){
		$sql.=" AND rp.nom like '%".$critere_recherche."%'";
	}
}
$sql.=" ORDER BY rp.nom, rp.prenom";
if($debug_create_resp=="y") {echo "$sql<br />\n";}

// Effectif sans login avec filtrage sur le nom:
$nb1 = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], $sql));

if($afficher_tous_les_resp!='y'){
	if($critere_recherche==""){
		$sql.=" LIMIT 20";
	}
}
//echo "$sql<br />\n";
$quels_parents = mysqli_query($GLOBALS["mysqli"], $sql);


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
$nb2 = mysqli_num_rows($quels_parents);


$nb_resp_legal_0_sans_compte=0;
if(getSettingAOui('GepiMemesDroitsRespNonLegaux')) {
	$sql="SELECT DISTINCT rp.pers_id FROM resp_pers rp, responsables2 r WHERE rp.login='' AND rp.pers_id=r.pers_id AND r.resp_legal='0';";
	$res_resp_legal_0=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_resp_legal_0_sans_compte=mysqli_num_rows($res_resp_legal_0);
}

$nb_resp_legal_1_ou_2_sans_compte=0;
$sql="SELECT DISTINCT rp.pers_id FROM resp_pers rp, responsables2 r WHERE rp.login='' AND rp.pers_id=r.pers_id AND (r.resp_legal='1' OR r.resp_legal='2');";
$res_resp_legal_1_ou_2=mysqli_query($GLOBALS["mysqli"], $sql);
$nb_resp_legal_1_ou_2_sans_compte=mysqli_num_rows($res_resp_legal_1_ou_2);


if(($nb_resp_legal_1_ou_2_sans_compte==0)&&($nb_resp_legal_0_sans_compte==0)) {
	echo "<p class='bold'>Informations&nbsp;:</p>
<blockquote>
	<p>Tous les responsables légaux 1 et 2 ont un login, ou bien aucun responsable n'est présent dans la base de Gepi.</p>
</blockquote>\n";
}
else{
	echo "<p class='bold'>Informations&nbsp;:</p>
<blockquote>\n";

	//echo "<p>Les $nb responsables ci-dessous n'ont pas encore de compte utilisateur.</p>\n";
	if($nb_resp_legal_1_ou_2_sans_compte>1) {
		echo "<p>$nb_resp_legal_1_ou_2_sans_compte responsables légaux 1 ou 2 n'ont pas encore de compte utilisateur.</p>\n";
	}
	elseif($nb_resp_legal_1_ou_2_sans_compte==1) {
		echo "<p>$nb_resp_legal_1_ou_2_sans_compte responsable légal 1 ou 2 n'a pas encore de compte utilisateur.</p>\n";
	}
	else {
		echo "<p>Tous les responsables légaux 1 ou 2 ont un compte utilisateur.</p>\n";
	}

	if($nb_resp_legal_0_sans_compte>0) {
		echo "<p>$nb_resp_legal_0_sans_compte responsables non légaux n'ont pas encore de compte utilisateur.</p>\n";
	}
	elseif($nb_resp_legal_0_sans_compte==1) {
		echo "<p>$nb_resp_legal_0_sans_compte responsable non légal n'a pas encore de compte utilisateur.</p>\n";
	}

	if($critere_recherche!="") {
		if($nb>1) {
			echo "<p>$nb responsables correspondent à votre recherche.</p>\n";
		}
		elseif($nb==1) {
			echo "<p>$nb responsable correspond à votre recherche.</p>\n";
		}
		else {
			echo "<p>Aucun responsable ne correspond à votre recherche.</p>\n";
		}
	}


	echo "<p><em>Note : vous ne pouvez créer de comptes d'accès que pour les responsables d'élèves associés à des classes.</em></p>\n";

	if ((getSettingValue("mode_generation_login_responsable") == null)||(getSettingValue("mode_generation_login_responsable") == "")) {
		echo "<p><b>ATTENTION !</b> Vous n'avez pas défini le mode de génération des logins. Allez sur la page de <a href='../gestion/param_gen.php'>gestion générale</a> pour définir le mode que vous souhaitez utiliser. Par défaut, les logins seront générés au format pnom tronqué à 8 caractères (ex: ADURANT).</p>\n";
	}
	if (!$session_gepi->auth_locale && $gepiSettings['ldap_write_access'] != "yes") {
		echo "<p><b>Note :</b> Vous utilisez une authentification externe à Gepi (LDAP ou SSO) sans avoir défini d'accès en écriture à l'annuaire LDAP. Aucun mot de passe ne sera donc assigné aux utilisateurs que vous vous apprêtez à créer. Soyez certain de générer les login selon le même format que pour votre source d'authentification SSO.</p>\n";
	}

	echo "</blockquote>\n";

	echo "<p><b>Créer des comptes par lot</b> :</p>\n";
	echo "<blockquote>\n";
	echo "<form action='create_responsable.php' method='post' style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\"); padding:5px;'>\n";
	echo add_token_field();

	echo "<input type='hidden' name='mode' value='classe' />\n";
	echo "<input type='hidden' name='creation_comptes_classe' value='y' />\n";
	echo "<p>Sélectionnez le mode d'authentification appliqué aux comptes :</p>";

	echo "<select name='reg_auth_mode' size='1'>";
	if ($session_gepi->auth_locale) {
		echo "<option value='auth_locale'>Authentification locale (base Gepi)</option>";
	}
	if ($session_gepi->auth_ldap) {
		echo "<option value='auth_ldap'>Authentification LDAP</option>";
	}
	if ($session_gepi->auth_sso) {
		echo "<option value='auth_sso'>Authentification unique (SSO)</option>";
	}
	echo "</select>";

	echo "<p>Sélectionnez une classe ou bien l'ensemble des classes puis cliquez sur 'valider'.</p>\n";

	echo "<select name='classe' size='1'>\n";
	echo "<option value='none'>Sélectionnez une classe</option>\n";
	echo "<option value='all'>Toutes les classes</option>\n";

	$quelles_classes = mysqli_query($GLOBALS["mysqli"], "SELECT id,classe FROM classes ORDER BY classe");
	while ($current_classe = mysqli_fetch_object($quelles_classes)) {
		echo "<option value='".$current_classe->id."'>".$current_classe->classe."</option>\n";
	}
	echo "</select>\n";

	echo "<br />\n";
	echo "<input type='checkbox' name='nouveaux_seulement' id='nouveaux_seulement' value='y' ";
	if(getSettingAOui('creer_comptes_parents_nouveaux_seulement')) {echo "checked ";}
	echo "/><label for='nouveaux_seulement'> Ne pas générer de fiche bienvenue pour les comptes existants</label><br />\n";
	echo "<input type='checkbox' name='fiches_bienvenue_un_jeu_par_parent' id='fiches_bienvenue_un_jeu_par_parent' value='y' ";
	if(getSettingAOui('fiches_bienvenue_un_jeu_par_parent')) {echo "checked ";}
	echo "/><label for='fiches_bienvenue_un_jeu_par_parent'> Ne pas générer autant de jeux de fiches bienvenue par parent qu'il y a d'enfant<br />(<em>ce qui donnerait 3 fiches par parent s'il y a 3 enfants dans l'établissement (soit 6 fiches par couple)</em>).</label><br />\n";
	echo "<input type='submit' name='Valider' value='Valider' />\n";
	echo "</form>\n";


	include("randpass.php");

	echo "<p style='font-size:small;'>Lors de la création, les comptes reçoivent un mot de passe aléatoire choisi parmi les caractères suivants&nbsp;: ";
	if (LOWER_AND_UPPER) {
		if(EXCLURE_CARACT_CONFUS) {
			$alphabet = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'j', 'k', 'm', 'n', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
		'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
		}
		else {
			$alphabet = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
		'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
		}
	} else {
		if(EXCLURE_CARACT_CONFUS) {
			$alphabet = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'j', 'k', 'm', 'n', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
		}
		else {
			$alphabet = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
		}
	}
	$cpt=0;
	foreach($alphabet as $key => $value) {
		if($cpt>0) {echo ", ";}
		echo $value;
		$cpt++;
	}

	if(EXCLURE_CARACT_CONFUS) {
		$cpt=2;
	}
	else {
		$cpt=0;
	}
	for($i=$cpt;$i<=9;$i++) {
		echo ", $i";
	}
	echo ".</p>\n";

	echo "<br />\n";

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

	//debug_var();

	//===================================
	//echo "<div style='border:1px solid black;'>\n";
	echo "<form enctype='multipart/form-data' name='form_rech' action='".$_SERVER['PHP_SELF']."' method='post' style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\"); padding:5px;'>\n";
	//style='border:1px solid black;' 
	echo "<table summary=\"Filtrage\">\n";
	echo "<tr>\n";
	echo "<td valign='top' rowspan='3'>\n";
	echo "Filtrage:";
	echo "</td>\n";
	echo "<td>\n";
	echo "<input type='submit' name='filtrage' value='Afficher' /> les responsables (<em>légaux 1 et 2</em>) sans login dont le <b>nom</b> contient&nbsp;: ";
	echo "<input type='text' name='critere_recherche' value='";
	//if((isset($filtrage))&&($filtrage==)) {
		echo $critere_recherche;
	//}
	echo "' />\n";

	echo "<br />\n";

	echo "<input type='submit' name='filtrage_rl0' value='Afficher' /> les responsables non responsables légaux (<em>resp_legal=0</em>) sans login dont le <b>nom</b> contient&nbsp;: ";
	echo "<input type='text' name='critere_recherche_rl0' value='$critere_recherche' />\n";

	echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td>\n";
	echo "ou";
	echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td>\n";
	echo "<input type='button' name='afficher_tous' value='Afficher tous les responsables légaux 1 et 2 sans login' onClick=\"document.getElementById('afficher_tous_les_resp').value='y'; document.form_rech.submit();\" />\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<input type='hidden' name='afficher_tous_les_resp' id='afficher_tous_les_resp' value='n' />\n";
	echo "</form>\n";
	//echo "</div>\n";
	//===================================
	echo "<br />\n";

	echo "<p>Cliquez sur le bouton 'Créer' d'un responsable pour créer un compte associé.</p>\n";
	echo "<form id='form_create_one_resp' action='create_responsable.php' method='post' style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\"); padding:5px;'>\n";
	//=====================
	// Sécurité: 20101118
	echo add_token_field();
	//=====================
	echo "<input type='hidden' name='mode' value='individual' />\n";
	echo "<input id='create_pers_id' type='hidden' name='pers_id' value='' />\n";

	echo "<input type='hidden' name='critere_recherche' value='$critere_recherche' />\n";
	echo "<input type='hidden' name='afficher_tous_les_resp' value='$afficher_tous_les_resp' />\n";

	// Sélection du mode d'authentification
	echo "<p>Mode d'authentification : <select name='reg_auth_mode' size='1' title=\"Mode d'authentification pour les comptes à créer avec les boutons ci-dessous.\">";
	if ($session_gepi->auth_locale) {
		echo "<option value='auth_locale'";
		if((isset($reg_auth_mode))&&($reg_auth_mode=='auth_locale')) {
			echo " selected";
		}
		echo ">Authentification locale (base Gepi)</option>";
	}
	if ($session_gepi->auth_ldap) {
		echo "<option value='auth_ldap'";
		if((isset($reg_auth_mode))&&($reg_auth_mode=='auth_ldap')) {
			echo " selected";
		}
		echo ">Authentification LDAP</option>";
	}
	if ($session_gepi->auth_sso) {
		echo "<option value='auth_sso'";
		if((isset($reg_auth_mode))&&($reg_auth_mode=='auth_sso')) {
			echo " selected";
		}
		echo ">Authentification unique (SSO)</option>";
	}
	echo "</select>";
	echo "</p>";


	echo "<table class='boireaus' border='1' summary=\"Créer\">\n";
	$alt=1;
	while ($current_parent = mysqli_fetch_object($quels_parents)) {
		if((isset($mode_recherche))&&($mode_recherche=='rl0')) {
			$sql="SELECT DISTINCT e.ele_id, e.nom, e.prenom, c.classe, r.resp_legal
					FROM responsables2 r, eleves e, j_eleves_classes jec, classes c
					WHERE r.pers_id='".$current_parent->pers_id."' AND
						r.resp_legal='0' AND
						r.ele_id=e.ele_id AND
						jec.login=e.login AND
						jec.id_classe=c.id";
		}
		else {
			$sql="SELECT DISTINCT e.ele_id, e.nom, e.prenom, c.classe, r.resp_legal
					FROM responsables2 r, eleves e, j_eleves_classes jec, classes c
					WHERE r.pers_id='".$current_parent->pers_id."' AND
						(r.resp_legal='1' OR r.resp_legal='2') AND
						r.ele_id=e.ele_id AND
						jec.login=e.login AND
						jec.id_classe=c.id";
		}
		if($debug_create_resp=="y") {echo "$sql<br />\n";}
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)>0){
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
				echo "<td>\n";
				echo "<input type='submit' value='Créer' onclick=\"$('create_pers_id').value='".$current_parent->pers_id."'; $('form_create_one_resp').submit();\" />\n";
				echo "<td>".casse_mot($current_parent->nom,'maj')." ".casse_mot($current_parent->prenom,'majf2')."</td>\n";
				echo "<td>\n";
				while($lig_ele=mysqli_fetch_object($test)){
					echo "Responsable légal $lig_ele->resp_legal de ".casse_mot($lig_ele->prenom,'majf2')." ".casse_mot($lig_ele->nom,'maj')." (<i>$lig_ele->classe</i>)<br />\n";
				}
				echo "</td>\n";
			echo "</tr>\n";
		}
	}
	echo "</table>\n";
	echo "</form>";
	echo "</blockquote>\n";
}
echo "<p><br /></p>\n";

echo "<p><em>NOTES&nbsp;:</em></p>
<a name='bloc_adresse'></a>
<blockquote>
<p>Si vous générez des Fiches bienvenue avec Bloc adresse du responsable de l'élève, il peut arriver que si les paramètres sont mal choisis, l'adresse n'apparaisse pas... ou hors champ.</p>\n";

echo "<p>Contrôler les paramétrages aberrants pour un format <a href='".$_SERVER['PHP_SELF']."?check_param_bloc_adresse_html=a4#bloc_adresse'>A4</a> ou un un format <a href='".$_SERVER['PHP_SELF']."?check_param_bloc_adresse_html=a3#bloc_adresse'>A3</a></p>";

if(isset($_GET['check_param_bloc_adresse_html'])) {
	if($_GET['check_param_bloc_adresse_html']=='a4') {
		echo "<p>Contrôle des paramètres pour la version A4&nbsp;:</p>";
		$retour_check=check_param_bloc_adresse_html('a4');
	}
	else {
		echo "<p>Contrôle des paramètres pour la version A3&nbsp;:</p>";
		$retour_check=check_param_bloc_adresse_html('a3');
	}

	if($retour_check=='') {
		echo "<p style='color:green'>";
		echo "Pas de valeur aberrante trouvée.";
	}
	else {
		echo "<p style='color:red'>";
		echo "".$retour_check;
	}
	echo "</p>";
}

echo "<br /><p style='text-indent: -6em; margin-left: 6em;'><em>Remarque&nbsp;:</em> Le bloc adresse des responsables d'un élève est positionné dans les bulletins HTML et Fiches Bienvenue avec les mêmes paramètres.<br />Ils sont définis dans la page <a href='../bulletin/param_bull.php#bloc_adresse'>Paramètres d'impression des bulletins</a></p>\n";
echo "</blockquote>\n";
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
