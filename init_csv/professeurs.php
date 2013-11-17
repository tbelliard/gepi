<?php
@set_time_limit(0);
/*
*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

include("../lib/initialisation_annee.inc.php");
$liste_tables_del = $liste_tables_del_etape_professeurs;

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : Importation des professeurs";
require_once("../lib/header.inc.php");
//************** FIN EN-TETE ***************

$en_tete=isset($_POST['en_tete']) ? $_POST['en_tete'] : "no";

?>
<p class="bold"><a href="index.php#professeurs"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil initialisation</a></p>
<?php

echo "<center><h3 class='gepi'>Quatrième phase d'initialisation<br />Importation des professeurs</h3></center>\n";


if (!isset($_POST["action"])) {
	//
	// On sélectionne le fichier à importer
	//

	echo "<p>Vous allez effectuer la quatrième étape : elle consiste à importer le fichier <b>g_professeurs.csv</b> contenant les données des professeurs.</p>\n";
	echo "<p>Les champs suivants doivent être présents, dans l'ordre, et <b>séparés par un point-virgule</b> : </p>\n";
	echo "<ul><li>Nom</li>\n" .
			"<li>Prénom</li>\n" .
			"<li>Civilité</li>\n" .
			"<li>Adresse e-mail</li>\n" .
			"</ul>\n";
	echo "<p>Veuillez préciser le nom complet du fichier <b>g_professeurs.csv</b>.</p>\n";
	echo "<form enctype='multipart/form-data' action='professeurs.php' method='post'>\n";
	echo add_token_field();
	echo "<input type='hidden' name='action' value='upload_file' />\n";
	echo "<p><input type=\"file\" size=\"80\" name=\"csv_file\" />\n";

	echo "<br />\n";
	echo "<label for='en_tete' style='cursor:pointer;'>Si le fichier à importer comporte une première ligne d'en-tête (non vide) à ignorer, <br />cocher la case ci-contre</label>&nbsp;<input type='checkbox' name='en_tete' id='en_tete' value='yes' checked />\n";


	echo "<br /><br /><p>Quelle formule appliquer pour la génération du login ?<br />\n";

	if(getSettingValue("use_ent")!='y') {
		$default_login_gen_type=getSettingValue('mode_generation_login');
		if(($default_login_gen_type=='')||(!check_format_login($default_login_gen_type))) {$default_login_gen_type='nnnnnnnnnnnnnnnnnnnn';}
	}
	else {
		$default_login_gen_type="";
	}

	if(getSettingValue('auth_sso')=="lcs") {
		echo "<span style='color:red'>Votre Gepi utilise une authentification LCS; Le format de login ci-dessous ne sera pas pris en compte. Les comptes doivent avoir été importés dans l'annuaire LDAP du LCS avant d'effectuer l'import dans GEPI.</span><br />\n";
	}

	echo champ_input_choix_format_login('login_gen_type', $default_login_gen_type);

	if (getSettingValue("use_ent") == "y") {
		echo "<input type='radio' name='login_gen_type' id='login_gen_type_ent' value='ent' checked=\"checked\" />\n";
		echo "<label for='login_gen_type_ent'  style='cursor: pointer;'>Les logins sont produits par un ENT (<span title=\"cette case permet l'utilisation de la table 'ldap_bx', assurez vous qu'elle soit remplie avec les bonnes informations.\">Attention !</span>)</label>\n";
		echo "<br />\n";
	}
	echo "<br />\n";

	echo "<br />\n</p>\n<p>Quel mode d'authentification est utilisé ?  (laissez 'Gepi' si vous ne savez pas de quoi il s'agit)</p>\n";
	echo "<p>\n<input type='radio' name='sso' value='gepi' checked /> Gepi";
	echo "<br />\n<input type='radio' name='sso' value='sso' /> SSO (aucun mot de passe ne sera généré)";
	echo "<br />\n<input type='radio' name='sso' value='ldap' /> Ldap (aucun mot de passe ne sera généré)</p>\n";
	echo "<p><input type='submit' value='Valider' /></p>\n";
	echo "</form>\n";

} else {
	//
	// Quelque chose a été posté
	//
	if ($_POST['action'] == "save_data") {
		check_token(false);
		//
		// On enregistre les données dans la base.
		// Le fichier a déjà été affiché, et l'utilisateur est sûr de vouloir enregistrer
		//

		// Première étape : on vide les tables

		echo "<p><em>On vide d'abord les tables suivantes&nbsp;:</em> ";
		$j=0;
		$k=0;
		while ($j < count($liste_tables_del)) {
			$sql="SHOW TABLES LIKE '".$liste_tables_del[$j]."';";
			//echo "$sql<br />";
			$test = sql_query1($sql);
			if ($test != -1) {
				if($k>0) {echo ", ";}
				$sql="SELECT 1=1 FROM $liste_tables_del[$j];";
				$res_test_tab=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_test_tab)>0) {
					$sql="DELETE FROM $liste_tables_del[$j];";
					$del = @mysqli_query($GLOBALS["mysqli"], $sql);
					echo "<b>".$liste_tables_del[$j]."</b>";
					echo " (".mysqli_num_rows($res_test_tab).")";
				}
				else {
					echo $liste_tables_del[$j];
				}
				$k++;
			}
			$j++;
		}

		// On passe tous les utilisateurs en etat "inactif"
		echo "<br />\n";
		echo "<p><em>On passe tous les utilisateurs en etat 'inactif' pour ne réactiver par la suite que les professeurs encore présents.</em> ";

		$res = mysqli_query($GLOBALS["mysqli"], "UPDATE utilisateurs SET etat='inactif' WHERE statut = 'professeur'");

		$sql="SELECT * FROM temp_profs;";
		$res_temp=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_temp)==0) {
			echo "<p style='color:red'>ERREUR&nbsp;: Aucun professeur n'a été trouvé&nbsp;???</p>\n";
			echo "<p><br /></p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		echo "<br />\n";
		echo "<p><em>On remplit la table 'utilisateurs' pour créer les nouveaux comptes et on ré-active d'anciens comptes&nbsp;:</em> ";

		$i = 0;
		// Compteur d'erreurs
		$error = 0;
		// Compteur d'enregistrement
		$total = 0;
		$total_deja_presents = 0;
		while ($lig=mysqli_fetch_object($res_temp)) {
			$reg_nom = $lig->nom;
			$reg_prenom = $lig->prenom;
			$reg_civilite = $lig->civilite;
			$reg_email = $lig->email;
			$reg_login = $lig->login;
			$reg_sso = $lig->sso;

			// On nettoie et on vérifie :
			$reg_nom=my_strtoupper(nettoyer_caracteres_nom($reg_nom, "a", " '_-", ""));
			if (mb_strlen($reg_nom) > 50) $reg_nom = mb_substr($reg_nom, 0, 50);

			$reg_prenom=nettoyer_caracteres_nom($reg_prenom, "a", " '_-", "");
			if (mb_strlen($reg_prenom) > 50) $reg_prenom = mb_substr($reg_prenom, 0, 50);

			if ($reg_civilite != "M." AND $reg_civilite != "MME" AND $reg_civilite != "MLLE") { $reg_civilite = "";}

			if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", $reg_email)) $reg_email = "-";

			// Déjà fait avant:
			$reg_login = preg_replace("/[^A-Za-z0-9._]/","",trim(my_strtoupper($reg_login)));
			if (mb_strlen($reg_login) > 50) $reg_login = mb_substr($reg_login, 0, 50);

			// Maintenant que tout est propre, on fait un test pour voir si le compte n'existe pas déjà

			$test = mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(login) FROM utilisateurs WHERE login = '" . $reg_login . "'"), 0);

			if ($test == 0) {
				// Test négatif : aucun professeur avec ce login. On enregistre.

				$reg_password = "";
				switch($reg_sso){
					case "ldap":
						$auth_mode = "ldap";
						$change_mdp = "n";
						break;
					case "sso":
						$auth_mode = "sso";
						$change_mdp = "n";
						break;
					default:
						$auth_mode = "gepi";
						$change_mdp = "y";
						// On génère un password :
						$feed = "0123456789abcdefghijklmnopqrstuvwxyz";
						for ($t=0; $t < 20; $t++){
							$reg_password .= mb_substr($feed, rand(0, mb_strlen($feed)-1), 1);
						}
						$reg_password = md5($reg_password);
						break;
				}


				$insert = mysqli_query($GLOBALS["mysqli"], "INSERT INTO utilisateurs SET " .
						"login = '" . $reg_login . "', " .
						"nom = '" . ((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $reg_nom) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")) . "', " .
						"prenom = '" . ((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $reg_prenom) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")) . "', " .
						"civilite = '" . $reg_civilite . "', " .
						"password = '" . $reg_password . "', " .
						"email = '" . ((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $reg_email) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")) . "', " .
						"statut = 'professeur', " .
						"etat = 'actif', " .
						"change_mdp = '" . $change_mdp . "', " .
						"auth_mode = '" . $auth_mode . "' " );

				if (!$insert) {
					$error++;
					echo "<span style='color:red'>".((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)).'<span><br />';
				} else {
					$total++;
				}
			} else {
				// Le login existe déjà. On passe l'utilisateur à nouveau en état 'actif'
				$res = mysqli_query($GLOBALS["mysqli"], "UPDATE utilisateurs SET etat = 'actif' WHERE login = '" . $reg_login . "'");
				$total_deja_presents++;
			}


			$i++;
		}

		if ($error > 0) {echo "<p style='color:red'>Il y a eu " . $error . " erreurs.</p>\n";}
		if ($total > 0) {echo "<p>" . $total . " professeurs ont été enregistrés.</p>\n";}
		if($total_deja_presents>0) {echo "<p>" . $total_deja_presents . " professeurs déjà présents.</p>\n";}

		echo "<p><a href='index.php#professeurs'>Revenir à la page précédente</a></p>\n";


	} else if ($_POST['action'] == "upload_file") {
		check_token(false);
		//
		// Le fichier vient d'être envoyé et doit être traité
		// On va donc afficher le contenu du fichier tel qu'il va être enregistré dans Gepi
		// en proposant des champs de saisie pour modifier les données si on le souhaite
		//

		$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;

		// On vérifie le nom du fichier... Ce n'est pas fondamentalement indispensable, mais
		// autant forcer l'utilisateur à être rigoureux
		if(my_strtolower($csv_file['name']) == "g_professeurs.csv") {

			// Le nom est ok. On ouvre le fichier
			$fp=fopen($csv_file['tmp_name'],"r");

			if(!$fp) {
				// Aie : on n'arrive pas à ouvrir le fichier... Pas bon.
				echo "<p>Impossible d'ouvrir le fichier CSV !</p>\n";
				echo "<p><a href='professeurs.php'>Cliquer ici </a> pour recommencer !</p>\n";
			} else {

				// Fichier ouvert ! On attaque le traitement

				// On va stocker toutes les infos dans un tableau
				// Une ligne du CSV pour une entrée du tableau
				$data_tab = array();

				//=========================
				// On lit une ligne pour passer la ligne d'entête:
				if($en_tete=="yes") {
					$ligne = fgets($fp, 4096);
				}
				//=========================

				$k = 0;
				while (!feof($fp)) {
					$ligne = fgets($fp, 4096);
					if(trim($ligne)!="") {

						$tabligne=explode(";",$ligne);

						// 0 : Nom
						// 1 : Prénom
						// 2 : Civilité
						// 3 : Adresse email

						// On nettoie et on vérifie :
						$tabligne[0]=my_strtoupper(nettoyer_caracteres_nom($tabligne[0], "a", " '_-", ""));
						$tabligne[0]=preg_replace("/'/"," ",$tabligne[0]);
						if (mb_strlen($tabligne[0]) > 50) $tabligne[0] = mb_substr($tabligne[0], 0, 50);

						$tabligne[1]=nettoyer_caracteres_nom($tabligne[1], "a", " '_-", "");
						$tabligne[1]=preg_replace("/'/"," ",$tabligne[1]);
						if (mb_strlen($tabligne[1]) > 50) $tabligne[1] = mb_substr($tabligne[1], 0, 50);

						if ($tabligne[2] != "M." AND $tabligne[2] != "MME" AND $tabligne[2] != "MLLE") { $tabligne[2] = "";}

						$tabligne[3] = preg_replace("/\"/", "", trim($tabligne[3]));
						if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", $tabligne[3])) {$tabligne[3] = "-";}


						// On regarde si le prof existe déjà dans la base
						$test = mysqli_query($GLOBALS["mysqli"], "SELECT login FROM utilisateurs WHERE (nom = '" . $tabligne[0] . "' AND prenom = '" . $tabligne[1] . "')");
						$prof_exists = false;
						if (mysqli_num_rows($test) == 0) {

							// On génère le login

							$reg_nom_login = preg_replace("/\040/","_", $tabligne[0]);
							$reg_prenom_login = remplace_accents($tabligne[1]);
							$reg_prenom_login = preg_replace("/[^a-zA-Z.\-]/", "", $reg_prenom_login);

							if($_POST['login_gen_type'] == 'ent'){
		
								if (getSettingValue("use_ent") == "y") {
									// Charge à l'organisme utilisateur de pourvoir à cette fonctionnalité
									// le code suivant n'est qu'une méthode proposée pour relier Gepi à un ENT
									$bx = 'oui';
									if (isset($bx) AND $bx == 'oui') {
										// On va chercher le login de l'utilisateur dans la table créée
										$sql_p = "SELECT login_u FROM ldap_bx
													WHERE nom_u = '".my_strtoupper($reg_nom_login)."'
													AND prenom_u = '".my_strtoupper($reg_prenom_login)."'
													AND statut_u = 'teacher'";
										$query_p = mysqli_query($GLOBALS["mysqli"], $sql_p);
										$nbre = mysqli_num_rows($query_p);
										if ($nbre >= 1 AND $nbre < 2) {
											$login_prof = mysql_result($query_p, 0,"login_u");
										}else{
											// Il faudrait alors proposer une alternative à ce cas
											$login_prof = "erreur_".$k;
										}
									}
								}
								else{
									die('Vous n\'avez pas autorisé Gepi à utiliser un ENT');
								}
							}
							else {
								$login_prof=generate_unique_login($reg_nom_login, $reg_prenom_login, $_POST['login_gen_type'], $_POST['login_gen_type_casse']);
							}
						} else {
							// Le prof semble déjà exister. On récupère son login actuel
							$login_prof = mysql_result($test, 0, "login");
							$prof_exists = true;
						}

						if((!$login_prof)||($login_prof=="")) {

							$login_prof = "erreur_";

							// On teste l'unicité du login que l'on vient de créer
							$m = 2;
							$test_unicite = 'no';
							$temp = $login_prof;
							while ($test_unicite != 'yes') {
								$test_unicite = test_unique_login($login_prof);
								if ($test_unicite != 'yes') {
									$login_prof = $temp.$m;
									$m++;
								}
							}
						}

						$data_tab[$k] = array();
						$data_tab[$k]["nom"] = $tabligne[0];
						$data_tab[$k]["prenom"] = $tabligne[1];
						$data_tab[$k]["civilite"] = $tabligne[2];
						$data_tab[$k]["email"] = $tabligne[3];
						$data_tab[$k]["reg_login"] = $login_prof;
						$data_tab[$k]["prof_exists"] = $prof_exists;
						//$data_tab[$k]["sso"] = $_POST['login_gen_type'];
						$data_tab[$k]["sso"] = $_POST['sso'];

					}
					$k++;
				}

				fclose($fp);

				// Fin de l'analyse du fichier.
				// Maintenant on va afficher tout ça.

				$sql="CREATE TABLE IF NOT EXISTS temp_profs (
				id int(11) NOT NULL auto_increment,
				login varchar(100) NOT NULL default '', 
				nom varchar(50) NOT NULL default '', 
				prenom varchar(50) NOT NULL default '', 
				civilite varchar(50) NOT NULL default '', 
				email varchar(100) NOT NULL default '', 
				sso varchar(50) NOT NULL default '',
				PRIMARY KEY  (id)
				);";
				$create_table = mysqli_query($GLOBALS["mysqli"], $sql);

				$sql="TRUNCATE TABLE temp_profs;";
				$vide_table = mysqli_query($GLOBALS["mysqli"], $sql);

				$nb_error=0;

				echo "<form enctype='multipart/form-data' action='professeurs.php' method='post'>\n";
				echo add_token_field();
				echo "<input type='hidden' name='action' value='save_data' />\n";
				echo "<table border='1' class='boireaus' summary='Tableau des professeurs'>\n";
				echo "<tr><th>Login</th><th>Nom</th><th>Prénom</th><th>Civilité</th><th>Email</th><th>Authentification</th></tr>\n";

				$alt=1;
				for ($i=0;$i<$k-1;$i++) {
					$alt=$alt*(-1);
					echo "<tr class='lig$alt'>\n";
					if ($data_tab[$i]["prof_exists"]) {
						echo "<td style='color: blue;'>\n";
					} else {
						echo "<td>\n";
					}

					$sql="INSERT INTO temp_profs SET login='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $data_tab[$i]["reg_login"]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."',
					nom='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $data_tab[$i]["nom"]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."',
					prenom='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $data_tab[$i]["prenom"]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."',
					civilite='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $data_tab[$i]["civilite"]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."',
					email='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $data_tab[$i]["email"]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."',
					sso='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $data_tab[$i]["sso"]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."';";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$insert) {
						echo "<span style='color:red'>";
						echo $data_tab[$i]["reg_login"];
 						echo "</span>";
						$nb_error++;
					}
					else {
						echo $data_tab[$i]["reg_login"];
					}

					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["nom"];
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["prenom"];
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["civilite"];
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["email"];
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["sso"];
					echo "</td>\n";
					echo "</tr>\n";
				}

				echo "</table>\n";

				if($nb_error>0) {
					echo "<p><span style='color:red'>$nb_error erreur(s) détectée(s) lors de la préparation.</span></p>\n";
				}

				echo "<p><input type='submit' value='Enregistrer' /></p>\n";

				echo "</form>\n";
			}

		} else if (trim($csv_file['name'])=='') {

			echo "<p>Aucun fichier n'a été sélectionné !<br />\n";
			echo "<a href='professeurs.php'>Cliquer ici </a> pour recommencer !</p>\n";

		} else {
			echo "<p>Le fichier sélectionné n'est pas valide !<br />\n";
			echo "<a href='professeurs.php'>Cliquer ici </a> pour recommencer !</p>\n";
		}
	}
}
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
