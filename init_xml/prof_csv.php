<?php
@set_time_limit(0);
/*
* $Id$
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
extract($_POST, EXTR_OVERWRITE);

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

check_token();

include("../lib/initialisation_annee.inc.php");
$liste_tables_del = $liste_tables_del_etape_professeurs;

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : Importation des matières";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

?>
<p class="bold"><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil initialisation</a></p>
<?php
echo "<center><h3 class='gepi'>Quatrième phase d'initialisation<br />Importation des professeurs</h3></center>";

if (!isset($step1)) {
	$j=0;
	$flag=0;
	while (($j < count($liste_tables_del)) and ($flag==0)) {
		$test = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW TABLES LIKE '$liste_tables_del[$j]'"));
		if($test==1){
			if (old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
				$flag=1;
			}
		}
		$j++;
	}

	$test = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM utilisateurs WHERE statut='professeur'"),0);
	if ($test != 0) {$flag=1;}

	if ($flag != 0){
		echo "<p><b>ATTENTION ...</b><br />";
		echo "Des données concernant les professeurs sont actuellement présentes dans la base GEPI<br /></p>";
		echo "<p>Si vous poursuivez la procédure les données telles que notes, appréciations, ... seront effacées.</p>";
		echo "<ul><li>Seules la table contenant les utilisateurs (<em>professeurs, admin,...</em>) et la table mettant en relation les matières et les professeurs seront conservées.</li>";
		echo "<li>Les professeurs de l'année passée présents dans la base GEPI et non présents dans la base CSV de cette année ne sont pas effacés de la base GEPI mais simplement déclarés \"inactifs\".</li>";
		echo "</ul>";
		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>";
		echo add_token_field();
		echo "<input type=hidden name='step1' value='y' />";
		echo "<input type='submit' name='confirm' value='Poursuivre la procédure' />";
		echo "</form>";
		echo "</div>";
		echo "<p><br /></p>";
		require("../lib/footer.inc.php");
		die();
	}
}

if (!isset($is_posted)) {
	$j=0;
	while ($j < count($liste_tables_del)) {
		$test = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW TABLES LIKE '$liste_tables_del[$j]'"));
		if($test==1){
			if (old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
				$del = @mysqli_query($GLOBALS["mysqli"], "DELETE FROM $liste_tables_del[$j]");
			}
		}
		$j++;
	}
	$del = @mysqli_query($GLOBALS["mysqli"], "DELETE FROM tempo2");

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method=post>";
	echo add_token_field();
	echo "<p>Importation du fichier <b>F_wind.csv</b> contenant les données relatives aux professeurs.";
	echo "<p>Veuillez préciser le nom complet du fichier <b>F_wind.csv</b>.";
	echo "<input type=hidden name='is_posted' value='yes' />";
	echo "<input type=hidden name='step1' value='y' />";
	echo "<p><input type='file' size='80' name='dbf_file' />";


	echo "<br /><br /><p>Quelle formule appliquer pour la génération du login ?</p>\n";

	//if(getSettingValue("use_ent")!='y') {
	// A MODIFIER : Pouvoir gérer use_ent et NetCollege ITOP hors 27:
	if ((getSettingValue("use_ent")!='y')||(preg_match("/^027/", getSettingValue('gepiSchoolRne')))) {
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

	// A MODIFIER : Pouvoir gérer use_ent et NetCollege ITOP hors 27:
	if ((getSettingValue("use_ent") == 'y')&&(!preg_match("/^027/", getSettingValue('gepiSchoolRne')))) {
		echo "<input type='radio' name='login_gen_type' id='login_gen_type_ent' value='ent' checked=\"checked\" />\n";
		echo "<label for='login_gen_type_ent'  style='cursor: pointer;'>Les logins sont produits par un ENT (<span title=\"cette case permet l'utilisation de la table 'ldap_bx', assurez vous qu'elle soit remplie avec les bonnes informations.\">Attention !</span>)</label>\n";
		echo "<br />\n";
	}
	echo "<br />\n";

	// Modifications jjocal dans le cas où c'est un serveur CAS qui s'occupe de tout
	if((getSettingValue("use_sso") == "cas")||(getSettingValue('auth_sso')=="lcs")) {
		$checked1 = ' checked="checked"';
		$checked0 = '';
	}else{
		$checked1 = '';
		$checked0 = ' checked="checked"';
	}

	echo "<p>Ces comptes seront-ils utilisés en Single Sign-On avec CAS ou LemonLDAP ? (<i>laissez 'non' si vous ne savez pas de quoi il s'agit</i>)</p>\n";
	echo "<input type='radio' name='sso' id='sso_n' value='no'".$checked0." /> <label for='sso_n' style='cursor: pointer;'>Non</label>\n";
	echo "<br /><input type='radio' name='sso' id='sso_y' value='yes'".$checked1." /> <label for='sso_y' style='cursor: pointer;'>Oui (<em>aucun mot de passe ne sera généré</em>)</label>\n";
	echo "<br />\n";
	echo "<br />\n";

	echo "<p><input type='submit' value='Valider' /></p>\n";
	echo "</form>\n";
	echo "<p><br /></p>\n";

} else {

	$dbf_file = isset($_FILES["dbf_file"]) ? $_FILES["dbf_file"] : NULL;
	// On commence par rendre inactifs tous les professeurs
	$req = mysqli_query($GLOBALS["mysqli"], "UPDATE utilisateurs set etat='inactif' where statut = 'professeur'");

	// on efface la ligne "display_users" dans la table "setting" de façon à afficher tous les utilisateurs dans la page  /utilisateurs/index.php
	$req = mysqli_query($GLOBALS["mysqli"], "DELETE from setting where NAME = 'display_users'");

	if(mb_strtoupper($dbf_file['name']) == "F_WIND.CSV") {
		$fp=fopen($dbf_file['tmp_name'],"r");
		if(!$fp) {
		echo "<p>Impossible d'ouvrir le fichier CSV !</p>";
		echo "<a href='".$_SERVER['PHP_SELF']."?a=a".add_token_in_url()."'>Cliquer ici </a> pour recommencer !</center></p>";
		} else {
			// on constitue le tableau des champs à extraire
			$tabchamps = array("AINOMU","AIPREN","AICIVI","NUMIND","FONCCO","INDNNI" );

			$nblignes=0;
			while (!feof($fp)) {
				$ligne = fgets($fp, 4096);
				if($nblignes==0){

					// Quand on enregistre en CSV des fichiers DBF de GEP avec OpenOffice, les champs sont renommés avec l'ajout de ',...' en fin de nom de champ.
					// On ne retient pas ces ajouts pour $en_tete
					$temp=explode(";",$ligne);
					for($i=0;$i<sizeof($temp);$i++){
						$temp2=explode(",",$temp[$i]);
						$en_tete[$i]=$temp2[0];
					}

					$nbchamps=sizeof($en_tete);
				}
				$nblignes++;
			}
			fclose ($fp);

			$cpt_tmp=0;
			for ($k = 0; $k < count($tabchamps); $k++) {
				for ($i = 0; $i < count($en_tete); $i++) {
					if (trim($en_tete[$i]) == $tabchamps[$k]) {
						$tabindice[$cpt_tmp]=$i;
						$cpt_tmp++;
					}
				}
			}

			echo "<p>Dans le tableau ci-dessous, les identifiants en rouge correspondent à des professeurs nouveaux dans la base GEPI. les identifiants en vert correspondent à des professeurs détectés dans les fichiers CSV mais déjà présents dans la base GEPI.<br /><br />Il est possible que certains professeurs ci-dessous, bien que figurant dans le fichier CSV, ne soient plus en exercice dans votre établissement cette année. C'est pourquoi il vous sera proposé en fin de procédure d'initialsation, un nettoyage de la base afin de supprimer ces données inutiles.</p>";

			$alt=1;
			echo "<table class='boireaus' border=1 cellpadding=2 cellspacing=2>";
			echo "<tr><th><p class=\"small\">Identifiant du professeur</p></th><th><p class=\"small\">Nom</p></th><th><p class=\"small\">Prénom</p></th><th>Mot de passe *</th></tr>";
			srand();
			$nb_reg_no = 0;
			//=========================
			$fp=fopen($dbf_file['tmp_name'],"r");
			// On lit une ligne pour passer la ligne d'entête:
			$ligne = fgets($fp, 4096);
			//=========================
			for($k = 1; ($k < $nblignes+1); $k++){
				if(!feof($fp)){
					$ligne = preg_replace('/"/','',fgets($fp, 4096));
					if(trim($ligne)!=""){
						$tabligne=explode(";",$ligne);
						for($i = 0; $i < count($tabchamps); $i++) {
							$affiche[$i] = nettoyer_caracteres_nom($tabligne[$tabindice[$i]], "an", "' ._-", "");
						}
						//Civilité
						$civilite = '';
						if ($affiche[2] = "ML") $civilite = "Mlle";
						if ($affiche[2] = "MM") $civilite = "Mme";
						if ($affiche[2] = "M.") $civilite = "M.";


						$prenoms = explode(" ",$affiche[1]);
						$premier_prenom = $prenoms[0];
						$prenom_compose = '';
						if (isset($prenoms[1])) {$prenom_compose = $prenoms[0]."-".$prenoms[1];}
	
						$lcs_prof_en_erreur="n";
						if(getSettingValue('auth_sso')=='lcs') {
							$lcs_prof_en_erreur="y";
							$exist = 'no';
							if($prof[$k]["id"]!='') {
								$login_prof_gepi=get_lcs_login($affiche[3], 'professeur');
								//echo "get_lcs_login(".$affiche[3].", 'professeur')=".$login_prof_gepi."<br />";
								if($login_prof_gepi!='') {
									$lcs_prof_en_erreur="n";
									$sql="SELECT 1=1 FROM utilisateurs WHERE login='$login_prof_gepi';";
									$test_exist_prof=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($test_exist_prof)>0) {
										$exist = 'yes';
									}
									else {
										$exist = 'no';
									}
								}
								else {
									$lcs_prof_en_erreur="y";
								}
							}
						}
						else {
							// On effectue d'abord un test sur le NUMIND
							$sql="select login from utilisateurs where (
							numind='".$affiche[3]."' and
							numind!='' and
							statut='professeur')";
							//echo "<tr><td>$sql</td></tr>";
							$test_exist = mysqli_query($GLOBALS["mysqli"], $sql);
							$result_test = mysqli_num_rows($test_exist);
							if ($result_test == 0) {
								// On tente ensuite une reconnaissance sur nom/prénom, si le test NUMIND a échoué
								$sql="select login from utilisateurs where (
								nom='".mysqli_real_escape_string($GLOBALS["mysqli"], $affiche[0])."' and
								prenom = '".mysqli_real_escape_string($GLOBALS["mysqli"], $premier_prenom)."' and
								statut='professeur')";
								// Pour debug:
								//echo "$sql<br />";
								$test_exist = mysqli_query($GLOBALS["mysqli"], $sql);
								$result_test = mysqli_num_rows($test_exist);
								if ($result_test == 0) {
									if ($prenom_compose != '') {
										$test_exist2 = mysqli_query($GLOBALS["mysqli"], "select login from utilisateurs
										where (
										nom='".mysqli_real_escape_string($GLOBALS["mysqli"], $affiche[0])."' and
										prenom = '".mysqli_real_escape_string($GLOBALS["mysqli"], $prenom_compose)."' and
										statut='professeur'
										)");
										$result_test2 = mysqli_num_rows($test_exist2);
										if ($result_test2 == 0) {
											$exist = 'no';
										} else {
											$exist = 'yes';
											$login_prof_gepi = old_mysql_result($test_exist2,0,'login');
										}
									} else {
										$exist = 'no';
									}
								} else {
									$exist = 'yes';
									$login_prof_gepi = old_mysql_result($test_exist,0,'login');
								}
							}
							else {
								$exist = 'yes';
								$login_prof_gepi = old_mysql_result($test_exist,0,'login');
							}
						}


						$alt=$alt*(-1);
						if($lcs_prof_en_erreur=="y") {
							echo "<tr class='lig$alt'>\n";
							echo "<td><p><font color='red'>Non trouvé dans l'annuaire LDAP</font></p></td><td><p>".$prof[$k]["nom_usage"]."</p></td><td><p>".$premier_prenom."</p></td><td>&nbsp;</td></tr>\n";
						}
						else {
							if ($exist == 'no') {

								// Aucun professeur ne porte le même nom dans la base GEPI. On va donc rentrer ce professeur dans la base
	
								$affiche[1] = nettoyer_caracteres_nom($affiche[1], "a", " _-", "");

								if($_POST['login_gen_type'] == 'ent'){
									// A MODIFIER : Pouvoir gérer use_ent et NetCollege ITOP hors 27:
									if ((getSettingValue("use_ent") == 'y')&&(!preg_match("/^027/", getSettingValue('gepiSchoolRne')))) {
										// Charge à l'organisme utilisateur de pourvoir à cette fonctionnalité
										// le code suivant n'est qu'une méthode proposée pour relier Gepi à un ENT
										$bx = 'oui';
										if (isset($bx) AND $bx == 'oui') {
											// On va chercher le login de l'utilisateur dans la table créée
											$sql_p = "SELECT login_u FROM ldap_bx
														WHERE nom_u = '".my_strtoupper($affiche[0])."'
														AND prenom_u = '".my_strtoupper($affiche[1])."'
														AND statut_u = 'teacher'";
											$query_p = mysqli_query($GLOBALS["mysqli"], $sql_p);
											$nbre = mysqli_num_rows($query_p);
											if ($nbre >= 1 AND $nbre < 2) {
												$temp1 = old_mysql_result($query_p, 0,"login_u");
											}else{
												// Il faudrait alors proposer une alternative à ce cas
												$temp1 = "erreur_".$k;
											}
										}
									}
									else{
										die('Vous n\'avez pas autorisé Gepi à utiliser un ENT');
									}
								}
								else {
									$temp1=generate_unique_login($affiche[0], $affiche[1], $_POST['login_gen_type'], $_POST['login_gen_type_casse']);
								}

								if((!$temp1)||($temp1=="")) {
									$temp1="erreur_";
								}

								$login_prof = $temp1;
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
								$affiche[0] = nettoyer_caracteres_nom($affiche[0], "a", " _-", "");
								// Mot de passe
								if (mb_strlen($affiche[5])>2 and $affiche[4]=="ENS" and $_POST['sso'] == "no") {
									//
									$pwd = md5(trim($affiche[5])); //NUMEN
									//$mess_mdp = "NUMEN";
									$mess_mdp = "Mot de passe dans le fichier fourni";
									//echo "<tr><td colspan='4'>NUMEN: $affiche[5] $pwd</td></tr>";
								} elseif ($_POST['sso']== "no") {
									$pwd = md5(rand (1,9).rand (1,9).rand (1,9).rand (1,9).rand (1,9).rand (1,9));
									$mess_mdp = $pwd;
									//echo "<tr><td colspan='4'>Choix 2: $pwd</td></tr>";
									//$mess_mdp = "Inconnu (compte bloqué)";
								} elseif ($_POST['sso'] == "yes") {
									$pwd = '';
									$mess_mdp = "aucun (sso)";
									//echo "<tr><td colspan='4'>sso</td></tr>";
								}
	
								// utilise le prénom composé s'il existe, plutôt que le premier prénom
	
								$sql="INSERT INTO utilisateurs SET login='$login_prof', nom='".mysqli_real_escape_string($GLOBALS["mysqli"], $affiche[0])."', prenom='".mysqli_real_escape_string($GLOBALS["mysqli"], $premier_prenom)."', civilite='$civilite', password='$pwd', statut='professeur', etat='actif', change_mdp='y', numind='$affiche[3]'";
								$res = mysqli_query($GLOBALS["mysqli"], $sql);
								// Pour debug:
								//echo "<tr><td colspan='4'>$sql</td></tr>";
	
								if(!$res){$nb_reg_no++;}
								$res = mysqli_query($GLOBALS["mysqli"], "INSERT INTO tempo2 VALUES ('".$login_prof."', '".$affiche[3]."')");
								echo "<tr class='lig$alt white_hover'><td><p><font color='red'>".$login_prof."</font></p></td><td><p>".$affiche[0]."</p></td><td><p>".$premier_prenom."</p></td><td>".$mess_mdp."</td></tr>\n";
							} else {
								// On corrige aussi les nom/prénom/civilité et numind parce que la reconnaissance a aussi pu se faire sur le nom/prénom
								$res = mysqli_query($GLOBALS["mysqli"], "UPDATE utilisateurs set etat='actif', nom='$affiche[0]', prenom='$premier_prenom', civilite='$civilite', numind='$affiche[3]' where login='".$login_prof_gepi."'");
	
								if(!$res) $nb_reg_no++;
								$res = mysqli_query($GLOBALS["mysqli"], "INSERT INTO tempo2 VALUES ('".$login_prof_gepi."', '".$affiche[3]."')");
								echo "<tr class='lig$alt white_hover'><td><p><font color='green'>".$login_prof_gepi."</font></p></td><td><p>".$affiche[0]."</p></td><td><p>".$affiche[1]."</p></td><td>Inchangé</td></tr>\n";
							}
						}
					}
				}
			}
				//dbase_close($fp);
			fclose($fp);
			echo "</table>";
			if ($nb_reg_no != 0) {
				echo "<p>Lors de l'enregistrement des données il y a eu $nb_reg_no erreurs. Essayez de trouvez la cause de l'erreur et recommencez la procédure avant de passer à l'étape suivante.";
			} else {
				echo "<p>L'importation des professeurs dans la base GEPI a été effectuée avec succès !</p>";

				/*
				echo "<p><b>* Précision sur les mots de passe (en non-SSO) :</b><br />
				(il est conseillé d'imprimer cette page)</p>
				<ul>
				<li>Lorsqu'un nouveau professeur est inséré dans la base GEPI, son mot de passe lors de la première
				connexion à GEPI est son NUMEN.</li>
				<li>Si le NUMEM n'est pas disponible dans le fichier F_wind.csv, GEPI génère aléatoirement
				un mot de passe.</li></ul>";
				*/
				echo "<p><b>* Précision sur les mots de passe (<em>en non-SSO</em>) :</b><br />
				(<em>il est conseillé d'imprimer cette page</em>)</p>
				<ul>
				<li>Lorsqu'un nouveau professeur est inséré dans la base GEPI, son mot de passe lors de la première
				connexion à GEPI est celui inscrit dans le F_wind.csv.</li>
				<li>Si le mot de passe n'est pas disponible dans le fichier F_wind.csv, GEPI génère aléatoirement
				un mot de passe.</li></ul>";
				echo "<p><b>Dans tous les cas le nouvel utilisateur est amené à changer son mot de passe lors de sa première connexion.</b></p>";
				echo "<br /><p>Vous pouvez procéder à la cinquième phase d'affectation des matières à chaque professeur, d'affectation des professeurs dans chaque classe et de définition des options suivies par les élèves.</p>";
			}
			//echo "<center><p><b><a href='prof_disc_classe.php'>Procéder à la cinquième phase d'initialisation</a></b></p></center><br /><br />";
			echo "<center><p><b><a href='prof_disc_classe_csv.php?a=a".add_token_in_url()."'>Procéder à la cinquième phase d'initialisation</a></b></p></center><br /><br />";
		}
	} else if (trim($dbf_file['name'])=='') {
		echo "<p>Aucun fichier n'a été sélectionné !<br />";
		echo "<a href='".$_SERVER['PHP_SELF']."?a=a".add_token_in_url()."'>Cliquer ici </a> pour recommencer !</center></p>";

	} else {
		echo "<p>Le fichier sélectionné n'est pas valide !<br />";
		echo "<a href='".$_SERVER['PHP_SELF']."?a=a".add_token_in_url()."'>Cliquer ici </a> pour recommencer !</center></p>";
	}
}
require("../lib/footer.inc.php");
?>
