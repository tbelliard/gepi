<?php
@set_time_limit(0);
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

include("../lib/initialisation_annee.inc.php");
$liste_tables_del = $liste_tables_del_etape_eleves;

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : Importation des élèves - Etape 1";
require_once("../lib/header.inc.php");
//************** FIN EN-TETE ***************

//==================================
// RNE de l'établissement pour comparer avec le RNE de l'établissement de l'année précédente
$gepiSchoolRne=getSettingValue("gepiSchoolRne") ? getSettingValue("gepiSchoolRne") : "";
//==================================

$en_tete=isset($_POST['en_tete']) ? $_POST['en_tete'] : "no";

//debug_var();
// Passer à 'y' pour afficher les requêtes
$debug_ele="n";

?>
<p class="bold"><a href="index.php#eleves"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil initialisation</a></p>
<?php

echo "<center><h3 class='gepi'>Première phase d'initialisation<br />Importation des élèves</h3></center>\n";


if (!isset($_POST["action"])) {
	//
	// On sélectionne le fichier à importer
	//

	if(isset($_SESSION['init_csv_ligne_entete'])&&($_SESSION['init_csv_ligne_entete']=="no")) {
		$checked="";
	}
	else {
		$checked=" checked";
	}

	echo "<p>Vous allez effectuer la première étape : elle consiste à importer le fichier <b>g_eleves.csv</b> contenant les données élèves.</p>\n";
	echo "<p>Les champs suivants doivent être présents, dans l'ordre, et <b>séparés par un point-virgule</b> : </p>\n";
	echo "<ul><li>Nom</li>\n" .
			"<li>Prénom</li>\n" .
			"<li>Date de naissance au format JJ/MM/AAAA</li>\n" .
			"<li>n° identifiant interne à l'établissement<br />(<em>indispensable : c'est ce numéro qui est utilisé pour faire la liaison lors des autres importations</em>)</li>\n" .
			"<li>n° identifiant national</li>\n" .
			"<li>Code établissement précédent</li>\n" .
			"<li>Doublement (<em>OUI ou NON</em>)</li>\n" .
			"<li>Régime (<em>INTERN ou EXTERN ou IN.EX. ou DP DAN</em>)</li>\n" .
			"<li>Sexe (<em>F ou M</em>)</li>\n" .
			"</ul>\n";
	echo "<p>Veuillez préciser le nom complet du fichier <b>g_eleves.csv</b>.</p>\n";
	echo "<form enctype='multipart/form-data' action='eleves.php' method='post'>\n";
	echo add_token_field();
	echo "<input type='hidden' name='action' value='upload_file' />\n";
	echo "<p><input type=\"file\" size=\"80\" name=\"csv_file\" />\n";
	echo "<p><label for='en_tete' style='cursor:pointer;'>Si le fichier à importer comporte une première ligne d'en-tête (<em>non vide</em>) à ignorer, <br />cocher la case ci-contre</label>&nbsp;<input type='checkbox' name='en_tete' id='en_tete' value='yes'$checked /></p>\n";
	echo "<p><input type='submit' value='Valider' /></p>\n";
	echo "</form>\n";

	$sql="SELECT 1=1 FROM utilisateurs WHERE statut='eleve';";
	if($debug_ele=='y') {echo "<span style='color:green;'>$sql</span><br />";}
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)>0) {
		$sql="SELECT 1=1 FROM tempo_utilisateurs WHERE statut='eleve';";
		if($debug_ele=='y') {echo "<span style='color:green;'>$sql</span><br />";}
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)==0) {
			echo "<p style='color:red'>Il existe un ou des comptes élèves de l'année passée, et vous n'avez pas mis ces comptes en réserve pour imposer le même login/mot de passe cette année.<br />Est-ce bien un choix délibéré ou un oubli de votre part?<br />Pour conserver ces login/mot de de passe de façon à ne pas devoir re-distribuer ces informations (<em>et éviter de perturber ces utilisateurs</em>), vous pouvez procéder à la mise en réserve avant d'initialiser l'année dans la page <a href='../gestion/changement_d_annee.php'>Changement d'année</a> (<em>vous y trouverez aussi la possibilité de conserver les comptes parents et bien d'autres actions à ne pas oublier avant l'initialisation</em>).</p>\n";
		}
	}

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

		// Suppression des comptes d'élèves:
		echo "<br />\n";
		echo "<p><em>On supprime les anciens comptes élèves...</em> ";
		$sql="DELETE FROM utilisateurs WHERE statut='eleve';";
		$del=mysqli_query($GLOBALS["mysqli"], $sql);

		$i = 0;
		// Compteur d'erreurs
		$error = 0;
		// Compteur d'enregistrement
		$total = 0;

		// Il faut que les comptes disposant d'un compte élève l'an dernier passent en premier pour récupérer leur login sans qu'il se produise une collision si un nouveau passe avant.
		//$sql="SELECT * FROM temp_gep_import2;";
		$sql="(SELECT t.* FROM temp_gep_import2 t, tempo_utilisateurs tu WHERE t.ELENOET=tu.identifiant2) UNION (SELECT * FROM temp_gep_import2 WHERE ELENOET NOT IN (SELECT identifiant2 FROM tempo_utilisateurs));";
		$res_temp=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_temp)==0) {
			echo "<p style='color:red'>ERREUR&nbsp;: Aucun élève n'a été trouvé&nbsp;???</p>\n";
			echo "<p><br /></p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		echo "<br />\n";
		echo "<p><em>On remplit les tables 'eleves', 'j_eleves_regime', 'j_eleves_etablissements'&nbsp;:</em> ";

		//while (true) {
		while ($lig=mysqli_fetch_object($res_temp)) {
			$reg_nom = $lig->ELENOM;
			$reg_prenom = $lig->ELEPRE;
			$reg_naissance = $lig->ELEDATNAIS;
			$reg_id_int = $lig->ELENOET;
			$reg_id_nat = $lig->ELENONAT;
			$reg_etab_prec = $lig->ETOCOD_EP;
			$reg_double = $lig->ELEDOUBL;
			$reg_regime = $lig->ELEREG;
			$reg_sexe = $lig->ELESEXE;

			//==========================
			// DEBUG
			//echo "<p>\$reg_nom=$reg_nom<br />\n";
			//echo "\$reg_prenom=$reg_prenom<br />\n";
			//echo "\$reg_id_int=$reg_id_int<br />\n";
			//==========================

			// On nettoie et on vérifie :
			$reg_nom=nettoyer_caracteres_nom(my_strtoupper($reg_nom), "a", " '_-", "");
			$reg_nom=preg_replace("/'/", " ", $reg_nom);

			if (mb_strlen($reg_nom) > 50) $reg_nom = mb_substr($reg_nom, 0, 50);
			$reg_prenom=nettoyer_caracteres_nom($reg_prenom, "a", " '_-", "");
			$reg_prenom=preg_replace("/'/", " ", $reg_prenom);

			if (mb_strlen($reg_prenom) > 50) $reg_prenom = mb_substr($reg_prenom, 0, 50);
			$naissance = explode("/", $reg_naissance);
			if (!preg_match("/[0-9]/", $naissance[0]) OR mb_strlen($naissance[0]) > 2 OR mb_strlen($naissance[0]) == 0) $naissance[0] = "00";
			if (mb_strlen($naissance[0]) == 1) $naissance[0] = "0" . $naissance[0];

			if (!preg_match("/[0-9]/", $naissance[1]) OR mb_strlen($naissance[1] OR mb_strlen($naissance[1]) == 0) > 2) $naissance[1] = "00";
			if (mb_strlen($naissance[1]) == 1) $naissance[1] = "0" . $naissance[1];

			if (!preg_match("/[0-9]/", $naissance[2]) OR mb_strlen($naissance[2]) > 4 OR mb_strlen($naissance[2]) == 3 OR mb_strlen($naissance[2]) == 1) $naissance[2] = "00";
			if (mb_strlen($naissance[2]) == 1) $naissance[2] = "0" . $naissance[2];

			//$reg_naissance = mktime(0, 0, 0, $naissance[1], $naissance[0], $naissance[2]);
			$reg_naissance = $naissance[2] . "-" . $naissance[1] . "-" . $naissance[0];
			$reg_id_int = preg_replace("/[^0-9]/","",trim($reg_id_int));

			$reg_id_nat = preg_replace("/[^A-Z0-9]/","",trim($reg_id_nat));

			$reg_etab_prec = preg_replace("/[^A-Z0-9]/","",trim($reg_etab_prec));

			$reg_double = trim(my_strtoupper($reg_double));
			if ($reg_double != "OUI" AND $reg_double != "NON") $reg_double = "NON";


			$reg_regime = trim(my_strtoupper($reg_regime));
			if ($reg_regime != "INTERN" AND $reg_regime != "EXTERN" AND $reg_regime != "IN.EX." AND $reg_regime != "DP DAN") $reg_regime = "DP DAN";

			if ($reg_sexe != "F" AND $reg_sexe != "M") $reg_sexe = "F";

			// Maintenant que tout est propre, on fait un test sur la table eleves pour s'assurer que l'élève n'existe pas déjà.
			// Ca permettra d'éviter d'enregistrer des élèves en double

			$sql="SELECT count(login) FROM eleves WHERE elenoet = '" . $reg_id_int . "';";
			if($debug_ele=='y') {echo "<br /><p><span style='color:coral;'>$sql -&gt; $test enregistrement.</span><br />";}
			$test = old_mysql_result(mysqli_query($GLOBALS["mysqli"], $sql), 0);

			//==========================
			// DEBUG
			//echo "\$reg_id_int=$reg_id_int<br />\n";
			//echo "\$test=$test<br />\n";
			//==========================

			if ($test == 0) {
				// Test négatif : aucun élève avec cet ID... on enregistre !
				$reg_login="";

				if($reg_id_int!='') {
					$sql="SELECT * FROM tempo_utilisateurs WHERE identifiant2='".$reg_id_int."' AND statut='eleve';";
					if($debug_ele=='y') {echo "<span style='color:green;'>$sql</span><br />";}
					$res_tmp_u=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_tmp_u)>0) {
						$lig_tmp_u=mysqli_fetch_object($res_tmp_u);
						$reg_login=$lig_tmp_u->login;
						if($debug_ele=='y') {echo "<span style='color:green;'>On récupère de tempo_utilisateurs le login $reg_login</span><br />";}
					}
				}
	
				if($reg_login=="") {
					$default_login_gen_type=getSettingValue('mode_generation_login_eleve');
					if(($default_login_gen_type=='')||(!check_format_login($default_login_gen_type))) {$default_login_gen_type='nnnnnnnnn_p';}

					$default_login_gen_type_casse=getSettingValue('mode_generation_login_eleve_casse');
					if(($default_login_gen_type_casse!='min')&&($default_login_gen_type_casse!='maj')) {$default_login_gen_type_casse='min';}

					//$reg_login=generate_unique_login($reg_nom, $reg_prenom, $default_login_gen_type, 'maj');
					$reg_login=generate_unique_login($reg_nom, $reg_prenom, $default_login_gen_type, $default_login_gen_type_casse);
					if($debug_ele=='y') {echo "<span style='color:blue;'>Login nouvellement généré pour '$reg_nom $reg_prenom' : '$reg_login'</span><br />";}
				}

				if((!$reg_login)||($reg_login=="")) {
					echo "<span style='color:red'><b>Erreur</b> lors de la génération d'un login pour ".$reg_nom." ".$reg_prenom.".</span><br />\n";
				}
				else {

					// Normalement on a maintenant un login dont on est sûr qu'il est unique...

					//==========================
					// DEBUG
					//echo "On va enregistrer l'élève avec le login \$reg_login=$reg_login</p>\n";
					//==========================

					// On insere les données

					$sql="INSERT INTO eleves SET " .
							"no_gep = '" . $reg_id_nat . "', " .
							"login = '" . $reg_login . "', " .
							"nom = '" . mysqli_real_escape_string($GLOBALS["mysqli"], $reg_nom) . "', " .
							"prenom = '" . mysqli_real_escape_string($GLOBALS["mysqli"], $reg_prenom) . "', " .
							"sexe = '" . $reg_sexe . "', " .
							"naissance = '" . $reg_naissance . "', " .
							"elenoet = '" . $reg_id_int . "', " .
							"ereno = '" . $reg_id_int . "';";
					if($debug_ele=='y') {echo "<span style='color:blue;'>$sql</span><br />";}
					$insert = mysqli_query($GLOBALS["mysqli"], $sql);
					if (!$insert) {
						$error++;
						echo "<span style='color:red'><b>ERREUR&nbsp;: </b>".mysqli_error($GLOBALS["mysqli"])."</span><br />\n";
					} else {
						$total++;

						// On re-crée le compte utilisateur s'il existait l'année précédente (mais en déclarant le compte inactif)
						if($reg_id_int!='') {
							$sql="SELECT * FROM tempo_utilisateurs WHERE identifiant2='".$reg_id_int."' AND statut='eleve';";
							if($debug_ele=='y') {echo "<span style='color:green;'>$sql</span><br />";}
							$res_tmp_u=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res_tmp_u)>0) {
								$lig_tmp_u=mysqli_fetch_object($res_tmp_u);

								$sql="INSERT INTO utilisateurs SET login='".$lig_tmp_u->login."', nom='".mysqli_real_escape_string($GLOBALS["mysqli"], $reg_nom)."', prenom='".mysqli_real_escape_string($GLOBALS["mysqli"], $reg_prenom)."', ";
								if($reg_sexe=='M') {
									$sql.="civilite='M', ";
								}
								else {
									$sql.="civilite='MLLE', ";
								}
								$sql.="password='".$lig_tmp_u->password."', salt='".$lig_tmp_u->salt."', email='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig_tmp_u->email)."', statut='eleve', etat='inactif', change_mdp='n', auth_mode='".$lig_tmp_u->auth_mode."';";
								if($debug_ele=='y') {echo "<span style='color:blue;'>$sql</span><br />";}
								$insert_u=mysqli_query($GLOBALS["mysqli"], $sql);
								if(!$insert_u) {
									echo "<span style='color:red'><b>Erreur</b> lors de la re-création du compte utilisateur pour ".$reg_nom." ".$reg_prenom.".</span><br />\n";
								}

							}
						}

						// On enregistre l'établissement d'origine, le régime, et si l'élève est redoublant
						//============================================
						if (($reg_etab_prec != '')&&($reg_id_int != '')) {
							if($gepiSchoolRne!="") {
								if($gepiSchoolRne!=$reg_etab_prec) {
									$sql="SELECT 1=1 FROM j_eleves_etablissements WHERE id_eleve='$reg_id_int';";
									$test_etab=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($test_etab)==0){
										$sql="INSERT INTO j_eleves_etablissements SET id_eleve='$reg_id_int', id_etablissement='$reg_etab_prec';";
										$insert_etab=mysqli_query($GLOBALS["mysqli"], $sql);
										if (!$insert_etab) {
											//echo "<p>Erreur lors de l'enregistrement de l'appartenance de l'élève $reg_nom $reg_prenom à l'établissement $reg_etab_prec.</p>\n";
											$error++;
											echo "<span style='color:red'>".mysqli_error($GLOBALS["mysqli"]).'<span><br />';
										}
									}
									else {
										$sql="UPDATE j_eleves_etablissements SET id_etablissement='$reg_etab_prec' WHERE id_eleve='$reg_id_int';";
										$update_etab=mysqli_query($GLOBALS["mysqli"], $sql);
										if (!$update_etab) {
											//echo "<p>Erreur lors de l'enregistrement de l'appartenance de l'élève $reg_nom $reg_prenom à l'établissement $reg_etab_prec.</p>\n";
											$error++;
											echo "<span style='color:red'>".mysqli_error($GLOBALS["mysqli"]).'<span><br />';
										}
									}
								}
							}
							else {
								// Si le RNE de l'établissement courant (celui du GEPI) n'est pas renseigné, on insère les nouveaux enregistrements, mais on ne met pas à jour au risque d'écraser un enregistrement correct avec l'info que l'élève de 1ère était en 2nde dans le même établissement.
								// Il suffira de faire un
								//       DELETE FROM j_eleves_etablissements WHERE id_etablissement='$gepiSchoolRne';
								// une fois le RNE renseigné.
								$sql="SELECT 1=1 FROM j_eleves_etablissements WHERE id_eleve='$reg_id_int';";
								$test_etab=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($test_etab)==0){
									$sql="INSERT INTO j_eleves_etablissements SET id_eleve='$reg_id_int', id_etablissement='$reg_etab_prec';";
									$insert_etab=mysqli_query($GLOBALS["mysqli"], $sql);
									if (!$insert_etab) {
										//echo "<p>Erreur lors de l'enregistrement de l'appartenance de l'élève $reg_nom $reg_prenom à l'établissement $reg_etab_prec.</p>\n";
										$error++;
										echo "<span style='color:red'>".mysqli_error($GLOBALS["mysqli"]).'<span><br />';
									}
								}
							}

						}
						//============================================

						if ($reg_double == "OUI") {
							$reg_double = "R";
						} else {
							$reg_double = "-";
						}

						if ($reg_regime == "INTERN") {
							$reg_regime = "int.";
						} else if ($reg_regime == "EXTERN") {
							$reg_regime = "ext.";
						} else if ($reg_regime == "DP DAN") {
							$reg_regime = "d/p";
						} else if ($reg_regime == "IN.EX.") {
							$reg_regime = "i-e";
						}

						$insert3 = mysqli_query($GLOBALS["mysqli"], "INSERT INTO j_eleves_regime SET login = '" . $reg_login . "', doublant = '" . $reg_double . "', regime = '" . $reg_regime . "'");
						if (!$insert3) {
							$error++;
							echo "<span style='color:red'>".mysqli_error($GLOBALS["mysqli"]).'<span><br />';
						}
					}
				}
			}
			$i++;
			//if (!isset($_POST['ligne'.$i.'_nom'])) break 1;
		}

		if ($error > 0) {echo "<p><span style='color:red'>Il y a eu " . $error . " erreur(s).</span></p>\n";}
		if ($total > 0) {echo "<p>" . $total . " élèves ont été enregistrés.</p>\n";}

		echo "<p><a href='index.php#eleves'>Revenir à la page précédente</a></p>\n";

		// On sauvegarde le témoin du fait qu'il va falloir convertir pour remplir les nouvelles tables responsables:
		saveSetting("conv_new_resp_table", 0);

	} else if ($_POST['action'] == "upload_file") {
		check_token(false);
		//
		// Le fichier vient d'être envoyé et doit être traité
		// On va donc afficher le contenu du fichier tel qu'il va être enregistré dans Gepi
		// en proposant des champs de saisie pour modifier les données si on le souhaite
		//

		if(isset($en_tete)) {
			$_SESSION['init_csv_ligne_entete']=$en_tete;
		}

		$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;

		// On vérifie le nom du fichier... Ce n'est pas fondamentalement indispensable, mais
		// autant forcer l'utilisateur à être rigoureux
		if(my_strtolower($csv_file['name']) == "g_eleves.csv") {

			// Le nom est ok. On ouvre le fichier
			$fp=fopen($csv_file['tmp_name'],"r");

			if(!$fp) {
				// Aie : on n'arrive pas à ouvrir le fichier... Pas bon.
				echo "<p>Impossible d'ouvrir le fichier CSV !</p>\n";
				echo "<p><a href='eleves.php'>Cliquer ici </a> pour recommencer !</p>\n";
			} else {

				// Fichier ouvert ! On attaque le traitement

				// On va stocker toutes les infos dans un tableau
				// Une ligne du CSV pour une entrée du tableau
				$data_tab = array();

				//=========================
				// On lit une ligne pour passer la ligne d'entête:
				if($en_tete=="yes") {
					$ligne = fgets($fp, 4096);
					echo "<p>A titre d'information, la ligne d'entête passée est la suivante&nbsp;:<br />
					<span style='color:green'>$ligne</span><br />
					Si il ne s'agit pas d'une ligne d'entête, vous pouvez <a href='".$_SERVER['PHP_SELF']."'>refaire cette étape</a>.</p>";
				}
				//=========================

				$cpt_INE_manquant=0;
				$msg_INE_manquant="";
				$k = 0;
				$nat_num = array();
				while (!feof($fp)) {
					$ligne = ensure_utf8(fgets($fp, 4096));
					if(trim($ligne)!="") {

						$tabligne=explode(";",$ligne);

						// 0 : Nom
						// 1 : Prénom
						// 2 : Date de naissance
						// 3 : identifiant interne
						// 4 : identifiant national
						// 5 : établissement précédent
						// 6 : Doublement (OUI || NON)
						// 7 : Régime : INTERN || EXTERN || IN.EX. || DP DAN
						// 8 : Sexe : F || M

						// On nettoie et on vérifie :
						//=====================================
						$tabligne[0]=nettoyer_caracteres_nom($tabligne[0], "a", " '_-", "");
						$tabligne[0]=preg_replace("/'/", " ", $tabligne[0]);
						if (mb_strlen($tabligne[0]) > 50) {$tabligne[0] = mb_substr($tabligne[0], 0, 50);}

						$tabligne[1]=nettoyer_caracteres_nom($tabligne[1], "a", " '_-", "");
						$tabligne[1]=preg_replace("/'/", " ", $tabligne[1]);
						if (mb_strlen($tabligne[1]) > 50) $tabligne[1] = mb_substr($tabligne[1], 0, 50);

						$naissance = explode("/", $tabligne[2]);
						if (!preg_match("/[0-9]/", $naissance[0]) OR mb_strlen($naissance[0]) > 2 OR mb_strlen($naissance[0]) == 0) $naissance[0] = "00";
						if (mb_strlen($naissance[0]) == 1) $naissance[0] = "0" . $naissance[0];

						// Au cas où la date de naissance serait vraiment mal fichue:
						if(!isset($naissance[1])) {
							$naissance[1]="00";
						}

						if (!preg_match("/[0-9]/", $naissance[1]) OR mb_strlen($naissance[1] OR mb_strlen($naissance[1]) == 0) > 2) $naissance[1] = "00";
						if (mb_strlen($naissance[1]) == 1) $naissance[1] = "0" . $naissance[1];

						// Au cas où la date de naissance serait vraiment mal fichue:
						if(!isset($naissance[2])) {
							$naissance[2]="0000";
						}

						if (!preg_match("/[0-9]/", $naissance[2]) OR mb_strlen($naissance[2]) > 4 OR mb_strlen($naissance[2]) == 3 OR mb_strlen($naissance[2]) < 2) $naissance[2] = "0000";

						$tabligne[2] = $naissance[0] . "/" . $naissance[1] . "/" . $naissance[2];

						$tabligne[3] = preg_replace("/[^0-9]/","",trim($tabligne[3]));

						$tabligne[4] = preg_replace("/[^A-Z0-9]/","",trim($tabligne[4]));
						$tabligne[4] = preg_replace("/\"/", "", $tabligne[4]);

						$tabligne[5] = preg_replace("/[^A-Z0-9]/","",trim($tabligne[5]));
						$tabligne[5] = preg_replace("/\"/", "", $tabligne[5]);

						$tabligne[6] = trim(my_strtoupper($tabligne[6]));
						$tabligne[6] = preg_replace("/\"/", "", $tabligne[6]);
						if ($tabligne[6] != "OUI" AND $tabligne[6] != "NON") $tabligne[6] = "NON";


						$tabligne[7] = trim(my_strtoupper($tabligne[7]));
						$tabligne[7] = preg_replace("/\"/", "", $tabligne[7]);
						if ($tabligne[7] != "INTERN" AND $tabligne[7] != "EXTERN" AND $tabligne[7] != "IN.EX." AND $tabligne[7] != "DP DAN") $tabligne[7] = "DP DAN";

						$tabligne[8] = trim(my_strtoupper($tabligne[8]));
						$tabligne[8] = preg_replace("/\"/", "", $tabligne[8]);
						if ($tabligne[8] != "F" AND $tabligne[8] != "M") $tabligne[8] = "F";

						if ($tabligne[4] != "" AND !in_array($tabligne[4], $nat_num)) {
							$nat_num[] = $tabligne[4];
							$data_tab[$k] = array();
							$data_tab[$k]["nom"] = $tabligne[0];
							$data_tab[$k]["prenom"] = $tabligne[1];
							$data_tab[$k]["naissance"] = $tabligne[2];
							$data_tab[$k]["id_int"] = $tabligne[3];
							$data_tab[$k]["id_nat"] = $tabligne[4];
							$data_tab[$k]["etab_prec"] = $tabligne[5];
							$data_tab[$k]["doublement"] = $tabligne[6];
							$data_tab[$k]["regime"] = $tabligne[7];
							$data_tab[$k]["sexe"] = $tabligne[8];
							// On incrémente pour le prochain enregistrement
							$k++;
						}
						elseif($tabligne[4]== "") {
							$cpt_INE_manquant++;
							$msg_INE_manquant.=$tabligne[0]." ".$tabligne[1]." (".$tabligne[2].") sans numéro national (ne sera pas enregistré(e)).<br />";
						}
						elseif(in_array($tabligne[4], $nat_num)) {
							$cpt_INE_manquant++;
							$msg_INE_manquant.=$tabligne[0]." ".$tabligne[1]." (".$tabligne[2].") a le même numéro INE (".$tabligne[4].") qu'un autre élève pris en compte sans numéro national (ne sera pas enregistré(e)).<br />";
						}
					}
				}

				fclose($fp);

				// Fin de l'analyse du fichier.
				// Maintenant on va afficher tout ça.

				echo "<form enctype='multipart/form-data' action='eleves.php' method='post'>\n";
				echo add_token_field();
				echo "<input type='hidden' name='action' value='save_data' />\n";
				echo "<table class='boireaus' border='1' summary='Tableau des élèves'>\n";
				echo "<tr><th>Nom</th><th>Prénom</th><th>Sexe</th><th>Date de naissance</th><th>n° étab.</th><th>n° nat.</th><th>Code étab.</th><th>Double.</th><th>Régime</th></tr>\n";

				$chaine_mysql_collate="";
				$sql="CREATE TABLE IF NOT EXISTS temp_gep_import2 (
				ID_TEMPO varchar(40) NOT NULL default '',
				LOGIN varchar(40) $chaine_mysql_collate NOT NULL default '',
				ELENOM varchar(40) $chaine_mysql_collate NOT NULL default '',
				ELEPRE varchar(40) $chaine_mysql_collate NOT NULL default '',
				ELESEXE varchar(40) $chaine_mysql_collate NOT NULL default '',
				ELEDATNAIS varchar(40) $chaine_mysql_collate NOT NULL default '',
				ELENOET varchar(40) $chaine_mysql_collate NOT NULL default '',
				ELE_ID varchar(40) $chaine_mysql_collate NOT NULL default '',
				ELEDOUBL varchar(40) $chaine_mysql_collate NOT NULL default '',
				ELENONAT varchar(40) $chaine_mysql_collate NOT NULL default '',
				ELEREG varchar(40) $chaine_mysql_collate NOT NULL default '',
				DIVCOD varchar(40) $chaine_mysql_collate NOT NULL default '',
				ETOCOD_EP varchar(40) $chaine_mysql_collate NOT NULL default '',
				ELEOPT1 varchar(40) $chaine_mysql_collate NOT NULL default '',
				ELEOPT2 varchar(40) $chaine_mysql_collate NOT NULL default '',
				ELEOPT3 varchar(40) $chaine_mysql_collate NOT NULL default '',
				ELEOPT4 varchar(40) $chaine_mysql_collate NOT NULL default '',
				ELEOPT5 varchar(40) $chaine_mysql_collate NOT NULL default '',
				ELEOPT6 varchar(40) $chaine_mysql_collate NOT NULL default '',
				ELEOPT7 varchar(40) $chaine_mysql_collate NOT NULL default '',
				ELEOPT8 varchar(40) $chaine_mysql_collate NOT NULL default '',
				ELEOPT9 varchar(40) $chaine_mysql_collate NOT NULL default '',
				ELEOPT10 varchar(40) $chaine_mysql_collate NOT NULL default '',
				ELEOPT11 varchar(40) $chaine_mysql_collate NOT NULL default '',
				ELEOPT12 varchar(40) $chaine_mysql_collate NOT NULL default '',
				LIEU_NAISSANCE varchar(50) $chaine_mysql_collate NOT NULL default '',
				MEL varchar(255) $chaine_mysql_collate NOT NULL default ''
				);";
				$create_table = mysqli_query($GLOBALS["mysqli"], $sql);

				$sql="TRUNCATE TABLE temp_gep_import2;";
				$vide_table = mysqli_query($GLOBALS["mysqli"], $sql);

				$nb_error=0;

				$alt=1;
				for ($i=0;$i<$k;$i++) {
					$alt=$alt*(-1);
					echo "<tr class='lig$alt'>\n";
					echo "<td>\n";

					$sql="INSERT INTO temp_gep_import2 SET id_tempo='$i',
					elenom='".mysqli_real_escape_string($GLOBALS["mysqli"], $data_tab[$i]["nom"])."',
					elepre='".mysqli_real_escape_string($GLOBALS["mysqli"], $data_tab[$i]["prenom"])."',
					elesexe='".mysqli_real_escape_string($GLOBALS["mysqli"], $data_tab[$i]["sexe"])."',
					eledatnais='".mysqli_real_escape_string($GLOBALS["mysqli"], $data_tab[$i]["naissance"])."',
					elenoet='".mysqli_real_escape_string($GLOBALS["mysqli"], $data_tab[$i]["id_int"])."',
					elenonat='".mysqli_real_escape_string($GLOBALS["mysqli"], $data_tab[$i]["id_nat"])."',
					etocod_ep='".mysqli_real_escape_string($GLOBALS["mysqli"], $data_tab[$i]["etab_prec"])."',
					eledoubl='".mysqli_real_escape_string($GLOBALS["mysqli"], $data_tab[$i]["doublement"])."',
					elereg='".mysqli_real_escape_string($GLOBALS["mysqli"], $data_tab[$i]["regime"])."';";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$insert) {
						echo "<span style='color:red'>".$data_tab[$i]["nom"]."</span>";
						$nb_error++;
					}
					else {
						echo $data_tab[$i]["nom"];
					}
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["prenom"];
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["sexe"];
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["naissance"];
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["id_int"];
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["id_nat"];
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["etab_prec"];
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["doublement"];
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["regime"];
					echo "</td>\n";
					echo "</tr>\n";
				}

				echo "</table>\n";
				echo "<p>$k élèves ont été détectés dans le fichier.</p>\n";

				if($nb_error>0) {
					echo "<p><span style='color:red'>$nb_error erreur(s) détectée(s) lors de la préparation.</span></p>\n";
				}

				if($cpt_INE_manquant>0) {
					echo "<p style='color:red'>".$cpt_INE_manquant." élève(s) ne sera(ont) pas enregistrés&nbsp;:<br />";
					echo $msg_INE_manquant."</p>";
				}

				echo "<p><input type='submit' value='Enregistrer' /></p>\n";

				echo "</form>\n";
			}

		} else if (trim($csv_file['name'])=='') {

			echo "<p>Aucun fichier n'a été sélectionné !<br />\n";
			echo "<a href='eleves.php'>Cliquer ici </a> pour recommencer !</p>\n";

		} else {
			echo "<p>Le fichier sélectionné n'est pas valide !<br />";
			echo "<a href='eleves.php'>Cliquer ici </a> pour recommencer !</p>";
		}
	}
}
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
