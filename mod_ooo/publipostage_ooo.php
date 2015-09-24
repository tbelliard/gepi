<?php
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

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

// SQL : INSERT INTO droits VALUES ( '/mod_ooo/publipostage_ooo.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'V', 'Modèle Ooo : Publipostage', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/mod_ooo/publipostage_ooo.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'V', 'Modèle Ooo : Publipostage', '');";
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
	die();
}


include_once('./lib/lib_mod_ooo.php'); //les fonctions
$nom_fichier_modele_ooo =''; //variable à initialiser à blanc pour inclure le fichier suivant et éviter une notice. Pour les autres inclusions, cela est inutile.
include_once('./lib/chemin.inc.php'); // le chemin des dossiers contenant les  modèles

$path=$nom_dossier_modele_a_utiliser.$_SESSION['login'];

$num_fich=isset($_POST['num_fich']) ? $_POST['num_fich'] : (isset($_GET['num_fich']) ? $_GET['num_fich'] : NULL);
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$id_groupe=isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);

$mode_pub=isset($_POST['mode_pub']) ? $_POST['mode_pub'] : (isset($_GET['mode_pub']) ? $_GET['mode_pub'] : "");

if((isset($num_fich))&&((isset($id_classe))||(isset($id_groupe)))) {
	if(!isset($msg)) {
		$msg="";
	}

	$num_periode=isset($_POST['num_periode']) ? $_POST['num_periode'] : (isset($_GET['num_periode']) ? $_GET['num_periode'] : 'nimporte');
	if($num_periode=='nimporte') {
		$sql_ajout_jec="";
		$sql_ajout_jeg="";
	}
	else {
		$sql_ajout_jec=" AND jec.periode='$num_periode'";
		$sql_ajout_jeg=" AND jeg.periode='$num_periode'";
	}

	$tab_file=get_tab_file($path);

	$tableau_des_fichiers_generes=array();
	$chemin_temp="../temp/".get_user_temp_directory();

	//debug_var();
	if((isset($mode_pub))&&($mode_pub=="un_fichier_par_selection")) {
		if(isset($id_classe)) {
			for($i=0;$i<count($id_classe);$i++) {
				$tab_eleves_OOo=array();
				$nb_eleve=0;

				$classe=get_class_from_id($id_classe[$i]);

				// Ajout d'un test dans le cas prof
				$acces_classe="n";
				if($_SESSION['statut']!='professeur') {
					$acces_classe="y";
				}
				elseif(getSettingAOui('OOoAccesTousEleProf')) {
					$acces_classe="y";
				}
				elseif(is_prof_classe($_SESSION['login'], $id_classe[$i])) {
					$acces_classe="y";
				}

				if($acces_classe!="y") {
					$msg.="Accès non autorisé aux informations élèves de la classe $classe.<br />";
				}
				else {
					$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE jec.login=e.login AND jec.id_classe='$id_classe[$i]'".$sql_ajout_jec." ORDER BY e.nom, e.prenom;";
					$res=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res)>0) {
						while($lig=mysqli_fetch_object($res)) {
							$tab_eleves_OOo[$nb_eleve]=array();

							$tab_eleves_OOo[$nb_eleve]['login']=$lig->login;
							$tab_eleves_OOo[$nb_eleve]['nom']=$lig->nom;
							$tab_eleves_OOo[$nb_eleve]['prenom']=$lig->prenom;
							$tab_eleves_OOo[$nb_eleve]['ine']=$lig->no_gep;
							$tab_eleves_OOo[$nb_eleve]['elenoet']=$lig->elenoet;
							$tab_eleves_OOo[$nb_eleve]['ele_id']=$lig->ele_id;
							$tab_eleves_OOo[$nb_eleve]['fille']="";
							if($lig->sexe=='F') {$tab_eleves_OOo[$nb_eleve]['fille']="e";} // ajouter un e à née si l'élève est une fille
							$tab_eleves_OOo[$nb_eleve]['date_nais']=formate_date($lig->naissance);
							$tab_eleves_OOo[$nb_eleve]['lieu_nais']=""; // on initialise les champs pour ne pas avoir d'erreurs
							if(getSettingValue('ele_lieu_naissance')=="y") {
								$tab_eleves_OOo[$nb_eleve]['lieu_nais']=preg_replace ( '@<[\/\!]*?[^<>]*?>@si'  , ''  , get_commune($lig->lieu_naissance,1)) ;
							} // récupérer la commune

							$tab_eleves_OOo[$nb_eleve]['classe']=$classe;

							$nb_eleve++;
						}
					}
				}

				if(count($tab_eleves_OOo)>0) {
					$mode_ooo="imprime";
	
					include_once('../tbs/tbs_class.php');
					include_once('../tbs/plugins/tbs_plugin_opentbs.php');
	
					$OOo = new clsTinyButStrong;
					$OOo->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);
	
					$nom_dossier_modele_a_utiliser = $path."/";// le chemin du fichier est indiqué à partir de l'emplacement de ce fichier
					$nom_fichier_modele_ooo = $tab_file[$num_fich];

					$OOo->LoadTemplate($nom_dossier_modele_a_utiliser.$nom_fichier_modele_ooo, OPENTBS_ALREADY_UTF8);
	
					$OOo->MergeBlock('eleves',$tab_eleves_OOo);

					$nom_fic = remplace_accents($classe, "all")."_".$nom_fichier_modele_ooo;

					$tableau_des_fichiers_generes[]=$nom_fic;

					$OOo->Show(OPENTBS_FILE, $chemin_temp."/".$nom_fic);
					$msg.="Fichier $classe : <a href='$chemin_temp/$nom_fic' target='_blank'>$nom_fic</a><br />";

				}
				else {
					$msg.="Aucun élève n'a été extrait pour la classe de $classe.<br />";
				}

			}
		}
		else {
			for($i=0;$i<count($id_groupe);$i++) {
				$tab_eleves_OOo=array();
				$nb_eleve=0;

				$current_group=get_group($id_groupe[$i]);
				//$info_grp=get_info_grp($id_groupe[$i], array('description', 'matieres', 'classes', 'profs'), "");
				$info_grp=get_info_grp($id_groupe[$i], array('matieres', 'classes', 'profs'), "");

				// Ajout d'un test dans le cas prof
				if(($_SESSION['statut']=='professeur')&&(!check_prof_groupe($_SESSION['login'], $id_groupe[$i]))) {
					$msg.="Accès non autorisé aux informations élèves pour l'enseignement".get_info_grp($id_groupe[$i]).".<br />";
				}
				else {
					$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_groupes jeg WHERE jeg.login=e.login AND jeg.id_groupe='$id_groupe[$i]'".$sql_ajout_jeg." ORDER BY e.nom, e.prenom;";
					$res=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res)>0) {
						while($lig=mysqli_fetch_object($res)) {
							$tab_eleves_OOo[$nb_eleve]=array();

							$tab_eleves_OOo[$nb_eleve]['login']=$lig->login;
							$tab_eleves_OOo[$nb_eleve]['nom']=$lig->nom;
							$tab_eleves_OOo[$nb_eleve]['prenom']=$lig->prenom;
							$tab_eleves_OOo[$nb_eleve]['ine']=$lig->no_gep;
							$tab_eleves_OOo[$nb_eleve]['fille']="";
							if($lig->sexe=='F') {$tab_eleves_OOo[$nb_eleve]['fille']="e";} // ajouter un e à née si l'élève est une fille
							$tab_eleves_OOo[$nb_eleve]['date_nais']=formate_date($lig->naissance);
							$tab_eleves_OOo[$nb_eleve]['lieu_nais']=""; // on initialise les champs pour ne pas avoir d'erreurs
							if(getSettingValue('ele_lieu_naissance')=="y") {
								$tab_eleves_OOo[$nb_eleve]['lieu_nais']=preg_replace ( '@<[\/\!]*?[^<>]*?>@si'  , ''  , get_commune($lig->lieu_naissance,1)) ;
							} // récupérer la commune

							$tab_eleves_OOo[$nb_eleve]['classe']=$current_group['classlist_string'];

							$nb_eleve++;
						}
					}
				}

				if(count($tab_eleves_OOo)>0) {
					$mode_ooo="imprime";
	
					include_once('../tbs/tbs_class.php');
					include_once('../tbs/plugins/tbs_plugin_opentbs.php');
	
					$OOo = new clsTinyButStrong;
					$OOo->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);
	
					$nom_dossier_modele_a_utiliser = $path."/";// le chemin du fichier est indiqué à partir de l'emplacement de ce fichier
					$nom_fichier_modele_ooo = $tab_file[$num_fich];

					$OOo->LoadTemplate($nom_dossier_modele_a_utiliser.$nom_fichier_modele_ooo, OPENTBS_ALREADY_UTF8);
	
					$OOo->MergeBlock('eleves',$tab_eleves_OOo);


					$nom_fic = remplace_accents($info_grp, "all")."_".$nom_fichier_modele_ooo;

					$tableau_des_fichiers_generes[]=$nom_fic;

					$OOo->Show(OPENTBS_FILE, $chemin_temp."/".$nom_fic);
					$msg.="Fichier $info_grp : <a href='$chemin_temp/$nom_fic' target='_blank'>$nom_fic</a><br />";

				}
				else {
					$msg.="Aucun élève n'a été extrait pour l'enseignement $info_grp.<br />";
				}
			}
		}

		if(isset($_POST['zipper'])) {
			//$tableau_des_fichiers_generes[]=$nom_fic;

			if (!defined('PCLZIP_TEMPORARY_DIR') || constant('PCLZIP_TEMPORARY_DIR')!=$chemin_temp) {
				@define( 'PCLZIP_TEMPORARY_DIR', $chemin_temp);
			}

			$fichier_zip="publipostage_ooo_".strftime("%Y-%m-%d_%H%M%S").".zip";
			$chemin_fichier_zip=$chemin_temp."/".$fichier_zip;

			require_once('../lib/pclzip.lib.php');

			$nb_fich_zippes=0;
			$archive = new PclZip($chemin_fichier_zip);
			for($loop=0;$loop<count($tableau_des_fichiers_generes);$loop++) {
				$v_list = $archive->add($chemin_temp."/".$tableau_des_fichiers_generes[$loop],
								PCLZIP_OPT_REMOVE_PATH,$chemin_temp);
				if ($v_list == 0) {
					$msg.="Erreur (".$tableau_des_fichiers_generes[$loop].") : ".$archive->errorInfo(TRUE)."<br />";
				}
				else {
					$nb_fich_zippes++;
				}
			}

			if ($nb_fich_zippes>0) {
				$msg.="Archive zip créée ($nb_fich_zippes fichiers)&nbsp;: <a href='$chemin_fichier_zip'>$fichier_zip</a>";
			}

		}

		unset($id_classe);
		unset($id_groupe);
	}
	else {
		// Extraction en un seul fichier

		$tab_eleves_OOo=array();
		$nb_eleve=0;

		if(isset($id_classe)) {
			for($i=0;$i<count($id_classe);$i++) {
				$classe=get_class_from_id($id_classe[$i]);

				// Ajout d'un test dans le cas prof
				$acces_classe="n";
				if($_SESSION['statut']!='professeur') {
					$acces_classe="y";
				}
				elseif(getSettingAOui('OOoAccesTousEleProf')) {
					$acces_classe="y";
				}
				elseif(is_prof_classe($_SESSION['login'], $id_classe[$i])) {
					$acces_classe="y";
				}

				if($acces_classe!="y") {
					$msg.="Accès non autorisé aux informations élèves de la classe $classe.<br />";
				}
				else {
					$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE jec.login=e.login AND jec.id_classe='$id_classe[$i]'".$sql_ajout_jec." ORDER BY e.nom, e.prenom;";
					$res=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res)>0) {
						while($lig=mysqli_fetch_object($res)) {
							$tab_eleves_OOo[$nb_eleve]=array();

							$tab_eleves_OOo[$nb_eleve]['login']=$lig->login;
							$tab_eleves_OOo[$nb_eleve]['nom']=$lig->nom;
							$tab_eleves_OOo[$nb_eleve]['prenom']=$lig->prenom;
							$tab_eleves_OOo[$nb_eleve]['ine']=$lig->no_gep;
							$tab_eleves_OOo[$nb_eleve]['elenoet']=$lig->elenoet;
							$tab_eleves_OOo[$nb_eleve]['ele_id']=$lig->ele_id;
							$tab_eleves_OOo[$nb_eleve]['fille']="";
							if($lig->sexe=='F') {$tab_eleves_OOo[$nb_eleve]['fille']="e";} // ajouter un e à née si l'élève est une fille
							$tab_eleves_OOo[$nb_eleve]['date_nais']=formate_date($lig->naissance);
							$tab_eleves_OOo[$nb_eleve]['lieu_nais']=""; // on initialise les champs pour ne pas avoir d'erreurs
							if(getSettingValue('ele_lieu_naissance')=="y") {
								$tab_eleves_OOo[$nb_eleve]['lieu_nais']=preg_replace ( '@<[\/\!]*?[^<>]*?>@si'  , ''  , get_commune($lig->lieu_naissance,1)) ;
							} // récupérer la commune

							$tab_eleves_OOo[$nb_eleve]['classe']=$classe;

							$nb_eleve++;
						}
					}
				}
			}
		}
		else {
			for($i=0;$i<count($id_groupe);$i++) {
				$current_group=get_group($id_groupe[$i]);

				// Ajout d'un test dans le cas prof
				if(($_SESSION['statut']=='professeur')&&(!check_prof_groupe($_SESSION['login'], $id_groupe[$i]))) {
					$msg.="Accès non autorisé aux informations élèves pour l'enseignement".get_info_grp($id_groupe[$i]).".<br />";
				}
				else {
					$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_groupes jeg WHERE jeg.login=e.login AND jeg.id_groupe='$id_groupe[$i]'".$sql_ajout_jeg." ORDER BY e.nom, e.prenom;";
					$res=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res)>0) {
						while($lig=mysqli_fetch_object($res)) {
							$tab_eleves_OOo[$nb_eleve]=array();

							$tab_eleves_OOo[$nb_eleve]['login']=$lig->login;
							$tab_eleves_OOo[$nb_eleve]['nom']=$lig->nom;
							$tab_eleves_OOo[$nb_eleve]['prenom']=$lig->prenom;
							$tab_eleves_OOo[$nb_eleve]['ine']=$lig->no_gep;
							$tab_eleves_OOo[$nb_eleve]['fille']="";
							if($lig->sexe=='F') {$tab_eleves_OOo[$nb_eleve]['fille']="e";} // ajouter un e à née si l'élève est une fille
							$tab_eleves_OOo[$nb_eleve]['date_nais']=formate_date($lig->naissance);
							$tab_eleves_OOo[$nb_eleve]['lieu_nais']=""; // on initialise les champs pour ne pas avoir d'erreurs
							if(getSettingValue('ele_lieu_naissance')=="y") {
								$tab_eleves_OOo[$nb_eleve]['lieu_nais']=preg_replace ( '@<[\/\!]*?[^<>]*?>@si'  , ''  , get_commune($lig->lieu_naissance,1)) ;
							} // récupérer la commune

							$tab_eleves_OOo[$nb_eleve]['classe']=$current_group['classlist_string'];

							$nb_eleve++;
						}
					}
				}
			}
		}

		if(count($tab_eleves_OOo)>0) {
			$mode_ooo="imprime";
	
			include_once('../tbs/tbs_class.php');
			include_once('../tbs/plugins/tbs_plugin_opentbs.php');
	
			$OOo = new clsTinyButStrong;
			$OOo->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);
	
			$nom_dossier_modele_a_utiliser = $path."/";// le chemin du fichier est indiqué à partir de l'emplacement de ce fichier
			$nom_fichier_modele_ooo = $tab_file[$num_fich];

			$OOo->LoadTemplate($nom_dossier_modele_a_utiliser.$nom_fichier_modele_ooo, OPENTBS_ALREADY_UTF8);
	
			$OOo->MergeBlock('eleves',$tab_eleves_OOo);

			$nom_fic = $nom_fichier_modele_ooo;

			$OOo->Show(OPENTBS_DOWNLOAD, $nom_fic);
			$OOo->remove(); //suppression des fichiers de travail
			$OOo->close();

			die();
		}
		else {
			$msg.="Aucun élève n'a été extrait.<br />";
			unset($id_classe);
			unset($id_groupe);
		}
	}
}
elseif(isset($_GET['suppr_fich'])) {
	check_token();

	if(!preg_match('/^[0-9]$/',$_GET['suppr_fich'])) {
		$msg="Numéro de fichier invalide : ".$_GET['suppr_fich']."<br />\n";
	}
	else {
		$tab_file=get_tab_file($path);

		if(!file_exists($path."/".$tab_file[$_GET['suppr_fich']])) {
			$msg="Le fichier ".$_GET['suppr_fich']." n'existe pas.<br />\n";
		}
		else {
			$menage=unlink($path."/".$tab_file[$_GET['suppr_fich']]);
			if($menage) {$msg="Fichier ".$tab_file[$_GET['suppr_fich']]." supprimé.<br />";}
			else {$msg="Erreur lors de la suppression du fichier ".$tab_file[$_GET['suppr_fich']]."<br />";}
		}
	}
}

//**************** EN-TETE *****************
$titre_page = "Modèle Open Office - Publipostage";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

?>

<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>

<?php if(!isset($num_fich)) { ?>
	</p>
<?php 
	if(isset($_FILES['monfichier'])) {
		check_token(false);

		$t=$_FILES['monfichier'];
		
		$monfichiername=$t['name'];
		$monfichiertype=$t['type'];
		$monfichiersize=$t['size'];
		$monfichiertmp_name=$t['tmp_name'];

		$upload_modele_ooo_autorise="n";
		if($_SESSION['statut']=='administrateur') {
			$upload_modele_ooo_autorise="y";
		}
		elseif(($_SESSION['statut']=='scolarite')&&(getSettingValue('OOoUploadScol')=='yes')) {
			$upload_modele_ooo_autorise="y";
		}
		elseif(($_SESSION['statut']=='cpe')&&(getSettingValue('OOoUploadCpe')=='yes')) {
			$upload_modele_ooo_autorise="y";
		}
		elseif(($_SESSION['statut']=='professeur')&&(getSettingValue('OOoUploadProf')=='yes')) {
			$upload_modele_ooo_autorise="y";
		}	
		if($upload_modele_ooo_autorise!='y') { 
?>
	<p style='color:red'>Action non autorisée&nbsp;: Upload d'un modèle personnalisé.</p>
<?php
			tentative_intrusion(1, "Tentative non autorisée d'upload d'un modèle OOo (".$monfichiername.")");
		}
		else {

			if ($monfichiername=="") {
?>
	<p style='color:red'>Pas de fichier indiqué ! Il faut recommencer...</p>
<?php
			}
			else {
				$fichiercopie=mb_strtolower($monfichiername);

				$cible=array();
				if($_SESSION['statut']=='administrateur') {
					$login_user=isset($_POST['login_user']) ? $_POST['login_user'] : NULL;
					if(!isset($login_user)) {
						$login_user=array();
						$login_user[]=$_SESSION['login'];
					}
					for($i=0;$i<count($login_user);$i++) {
						$temoin_erreur="n";
						$path_user=$nom_dossier_modele_a_utiliser.$login_user[$i];
						if(!file_exists($path_user)) {
							$creation=mkdir($path_user);
							if(!$creation) {
?>
	<p style='color:red;'>
		ERREUR lors de la création du dossier de modèle openDocument pour <?php echo $login_user[$i] ?>
	</p>
<?php
								$temoin_erreur="y";
							}
						}

						if($temoin_erreur=="n") {
							if(!file_exists($path_user."/index.html")) {
								if(!creation_index_redir_login($path_user,1)) {
									echo "<p style='color:red;'>ERREUR lors de la création d'un index dans votre dossier de modèle openDocument pour ".$login_user[$i]."</p>\n";
								}
							}

							$cible[]=$path_user."/".$fichiercopie;
						}
					}
				}
				else {
					$cible[]=$path."/".$fichiercopie;
				}

				/*
				if (!move_uploaded_file($monfichiertmp_name,$cible)) {
					echo "<p style='color:red'>Erreur de copie<br />\n";
					echo "Origine     : $monfichiername <br />\n";
					echo "Destination : $cible<br />";
					echo "La copie ne s'est pas effectuée !\n Vérifiez la taille du fichier (max 512ko)</p>\n";
				}
				else {
					echo "<p style='color:red;'>Le fichier $cible a été copié correctement.</p>\n";
				}
				*/

				$nb_copies=0;
				for($i=0;$i<count($cible);$i++) {
					$res_copy=copy($monfichiertmp_name , $cible[$i]);
					if(!$res_copy) {echo "<p style='color:red'>Echec de la mise en place du fichier ".$cible[$i]."</p>";}
					else {
						$nb_copies++;
					}
				}
				if($nb_copies>0) {
					echo "<p style='color:red'>Fichier mis en place pour $nb_copies utilisateur(s).</p>\n";
				}
			}
		}
	}
	
	if(file_exists($path)) {
		$tab_file=get_tab_file($path);
	
		if(count($tab_file)==0) {
			$upload_modele_ooo_autorise="n";
			echo "<p style='color:red;'>Vous n'avez aucun modèle.";

			if($_SESSION['statut']=='administrateur') {$upload_modele_ooo_autorise="y";}
			elseif(($_SESSION['statut']=='scolarite')&&(getSettingValue('OOoUploadScol')=='yes')) {$upload_modele_ooo_autorise="y";}
			elseif(($_SESSION['statut']=='cpe')&&(getSettingValue('OOoUploadCpe')=='yes')) {$upload_modele_ooo_autorise="y";}
			elseif(($_SESSION['statut']=='professeur')&&(getSettingValue('OOoUploadProf')=='yes')) {$upload_modele_ooo_autorise="y";}

			if($upload_modele_ooo_autorise!="y") {
				echo "<br />\n";
				echo "Et vous n'avez pas l'autorisation d'uploader vos modèles.";
			}

			echo "</p>\n";
		}
		else {
			// Lister les modèles existants
			echo "<p>Utiliser le modèle&nbsp;:<br />";
			for($i=0;$i<count($tab_file);$i++) {
				echo "<a href='".$_SERVER['PHP_SELF']."?num_fich=$i' title=\"Effectuer un publipostage OOo avec ce fichier modèle\">".$tab_file[$i]." <img src='../images/icons/print.png' class='icone16' alt='Imprimer' /></a> - <a href='mes_modeles/".$_SESSION['login']."/".$tab_file[$i]."' target='_blank' title=\"Éditer le fichier ".$tab_file[$i]."
pour (par exemple) modifier/améliorer ce modèle
et le proposer au publipostage par la suite.\"><img src='../images/edit16.png' width='16' height='16' alt='Éditer' /></a> - <a href='".$_SERVER['PHP_SELF']."?suppr_fich=$i".add_token_in_url()."' title=\"Supprimer le fichier ".$tab_file[$i]."\"><img src='../images/delete16.png' width='16' height='16' alt='Supprimer' /></a><br />";
			}
			echo "</p>\n";
		}
	}
	else {
		$creation=mkdir($path);
		if(!$creation) {
			echo "<p style='color:red;'>ERREUR lors de la création de votre dossier de modèle openDocument</p>\n";
			require_once("../lib/footer.inc.php");
			die();
		}
	}
	
	if(!file_exists($path."/index.html")) {
		if(!creation_index_redir_login($path,1)) {
			echo "<p style='color:red;'>ERREUR lors de la création d'un index dans votre dossier de modèle openDocument</p>\n";
		}
	}

	$upload_modele_ooo_autorise="n";
	if($_SESSION['statut']=='administrateur') {$upload_modele_ooo_autorise="y";}
	elseif(($_SESSION['statut']=='scolarite')&&(getSettingValue('OOoUploadScol')=='yes')) {$upload_modele_ooo_autorise="y";}
	elseif(($_SESSION['statut']=='cpe')&&(getSettingValue('OOoUploadCpe')=='yes')) {$upload_modele_ooo_autorise="y";}
	elseif(($_SESSION['statut']=='professeur')&&(getSettingValue('OOoUploadProf')=='yes')) {$upload_modele_ooo_autorise="y";}

	if($upload_modele_ooo_autorise=='y') {
		echo "<form method='post' ENCTYPE='multipart/form-data' action='".$_SERVER['PHP_SELF']."' style='margin-top:1em;'>\n";
		echo add_token_field();
		echo "<p>Mettre en place un nouveau modèle&nbsp;:</p>\n";
		echo "<INPUT TYPE=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"512000\">";
	
		if($_SESSION['statut']=='administrateur') {
			echo "<p>Pour quel(s) utilisateur(s) souhaitez-vous mettre en place le modèle&nbsp;? ";
			echo "<a href='javascript:cocher_decocher(true)'>Tout cocher</a> / <a href='javascript:cocher_decocher(false)'>Tout décocher</a>\n";
			echo "</p>\n";

			echo liste_checkbox_utilisateurs(array('administrateur', 'scolarite', 'cpe', 'professeur'), array($_SESSION['login']));

		}
	
		echo "<p>Fichier modèle&nbsp;:&nbsp;<input type='file' name='monfichier' value='il a cliqué le bougre'></p>\n";
		echo "<p class='center'><input type='submit' name='btn' Align='middle' value='Envoyer' /></p>\n";
		echo "</form>\n";

		echo "<p><i>NOTES&nbsp;:</i></p>\n";
		echo "<ul>\n";
			echo "<li>\n";
				echo "<p style='margin-left:3em;'>Le fichier fourni peut utiliser les champs suivants&nbsp;:</p>\n";
				echo "<ul style='margin-left:3em;'>\n";
				echo "<li>[eleves.nom]</li>\n";
				echo "<li>[eleves.prenom]</li>\n";
				echo "<li>[eleves.sexe]</li>\n";
				echo "<li>[eleves.date_nais]</li>\n";
				if(getSettingValue('ele_lieu_naissance')=="y") {
					echo "<li>[eleves.lieu_nais]</li>\n";
				}
				echo "<li>[eleves.classe]</li>\n";
				echo "<li>[eleves.ine]</li>\n";
				echo "<li>[eleves.elenoet]</li>\n";
				echo "<li>[eleves.ele_id]</li>\n";
				echo "<li>[eleves.login]</li>\n";
				echo "</ul>\n";
			echo "</li>\n";
			echo "<li>\n";
				echo "<p>Des exemples de modèles sont disponibles&nbsp;: <a href='http://www.sylogix.org/projects/gepi/wiki/Publipostage_ooo'>http://www.sylogix.org/projects/gepi/wiki/Publipostage_ooo</a></p>\n";
			echo "</li>\n";
		echo "</ul>\n";

		echo "<script type='text/javascript'>
".js_checkbox_change_style()."
".js_change_style_radio()."
</script>";

		if($_SESSION['statut']=='administrateur') {
			echo "<p style='color:red'>A FAIRE : Permettre à l'administrateur de faire le ménage dans les fichiers modèles des autres utilisateurs.<br />Permettre de limiter les champs auxquels ont accès les utilisateurs selon leur statut.</p>\n";
		}
	}
}
else {
?>
	| <a href='<?php echo $_SERVER['PHP_SELF']; ?>'>Choisir un autre modèle</a>
<?php
	if((!isset($id_classe))&&(!isset($id_groupe))) {
?>
</p>
<?php
	
		$tab_file=get_tab_file($path);
	
		// Choix de la classe/groupe
		$cpt_js=0;

		$sql="SELECT MAX(num_periode) AS maxper FROM periodes p, classes c WHERE c.id=p.id_classe;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
?>
<p style='colore:red'>Aucune classe avec période(s) n'a été trouvée.</p>
<?php
			require_once("../lib/footer.inc.php");
			die();
		}
		$lig=mysqli_fetch_object($res);
		$maxper=$lig->maxper;

		if($_SESSION['statut']=='professeur') {
			if(getSettingAOui('OOoAccesTousEleProf')) {
				$sql="SELECT c.id, c.classe FROM classes c ORDER BY c.classe;";
			}
			else {
				$sql="SELECT DISTINCT c.id, c.classe FROM classes c, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE c.id=jgc.id_classe AND jgc.id_groupe=jgp.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe;";
			}
		}
		else {
			$sql="SELECT c.id, c.classe FROM classes c ORDER BY c.classe;";
		}
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
?>
<form method='post' enctype='multipart/form-data' action='<?php echo $_SERVER['PHP_SELF']; ?>' id='form1'>
	<fieldset class='fieldset_opacite50'>
		<p>
			Pour quelle(s) classe(s) souhaitez-vous imprimer le document 
			<strong><?php echo $tab_file[$num_fich]; ?></strong>&nbsp;?
			<a href="javascript:cocher_decocher('id_classe_', true)">
				Cocher
			</a>
			/
			<a href="javascript:cocher_decocher('id_classe_', false)">
				décocher
			</a>
			toutes les classes
			<input type='hidden' name='num_fich' value='<?php echo $num_fich; ?>' />
		</p>
<?php
			echo add_token_field();

			$nombreligne=mysqli_num_rows($res);
			$nbcol=3;
			$nb_par_colonne=round($nombreligne/$nbcol);
?>
		<table width='100%' summary="Tableau de choix des classes">
			<tr valign='top' align='center'>
				<td align='left'>
<?php
			$cpt=0;
			while($lig=mysqli_fetch_object($res)) {
				if(($cpt>0)&&(round($cpt/$nb_par_colonne)==$cpt/$nb_par_colonne)){
?>
				</td>
				<td align='left'>
<?php
				}
?>
					<input type='checkbox' 
						   name='id_classe[]' 
						   id='id_classe_<?php echo $cpt; ?>' 
						   value='<?php echo $lig->id; ?>'
						   onchange="checkbox_change('id_classe_<?php echo $cpt; ?>')"
						   />
					<label for='id_classe_<?php echo $cpt; ?>'>
						<span id='texte_id_classe_<?php echo $cpt; ?>'><?php echo $lig->classe; ?></span>
					</label>
					<br />

<?php
				$cpt++;
			}
?>
				</td>
			</tr>
		</table>
		<p style='text-indent:-3em; margin-left:3em;'>
			Extraire les élèves inscrits dans les classes choisies&nbsp;:
			<br />
			<input type='radio' name='num_periode' id='num_periode_nimporte' value='nimporte' checked='checked' />
			<label for='num_periode_nimporte' id='texte_num_periode_nimporte'>Quelle que soit la période</label>
			<br />
<?php
			for($loop=1;$loop<=$maxper;$loop++) {
?>
			<input type='radio' name='num_periode' id='num_periode_<?php echo $loop; ?>' value='<?php echo $loop; ?>' />
			<label for='num_periode_<?php echo $loop; ?>' id='texte_num_periode_<?php echo $loop; ?>'>
				Période <?php echo $loop; ?>
			</label>
			<br />
<?php
			}
?>
		</p>
		<p>
			<input type='radio' name='mode_pub' id='mode_pub' 
				   value='' checked=checked'' 
				   onchange="change_style_radio();" />
			<label for='mode_pub' id='texte_mode_pub' style='font-weight:bold;'>
				Générer un seul fichier même si vous sélectionnez plusieurs classes
			</label>
			<br />ou<br />
			<input type='radio' name='mode_pub' id='mode_pub2' value='un_fichier_par_selection' 
				   onchange="change_style_radio();" />
			<label for='mode_pub2' id='texte_mode_pub2'>
				Générer un fichier par classe sélectionnée.
			</label>
			<br />
			<span style='margin-left:2em;'>
				<input type='checkbox' name='zipper' id='zipper' value='y' 
					   onchange="checkbox_change(this.id); check_choix_zip('');" />
				<label for='zipper' id='texte_zipper'>
					Dans ce deuxième cas, zipper l'ensemble de ces fichiers en une seule archive ZIP.
				</label>
			</span>
			<br />
		</p>
		<p class='center'>
			<input type='submit' value='Envoyer' id='bouton_submit' />
			<input type='button' value='Envoyer' id='bouton_submit_js'
				   onclick="valider_publipostage('form1', 'id_classe_')" style='display:none;'
				   />
		</p>
	</fieldset>
</form>
<?php
			$cpt_js=$cpt;
		}

		if($_SESSION['statut']=='professeur') {
			$groups=get_groups_for_prof($_SESSION['login']);
			if(count($groups)>0) {
?>
Ou
<?php
/* <form method='post' enctype='multipart/form-data' action='<?php echo $_SERVER['PHP_SELF']; ?>' id='form1'>*/
?>
<form method='post' enctype='multipart/form-data' action='<?php echo $_SERVER['PHP_SELF']; ?>' id='form2'>
	<fieldset class='fieldset_opacite50'>
		<p>
			Pour quel enseignement souhaitez-vous imprimer le document
			<strong><?php echo $tab_file[$num_fich]; ?></strong>&nbsp;?
			<a href="javascript:cocher_decocher('id_groupe_', true)">Cocher</a>
			/
			<a href="javascript:cocher_decocher('id_groupe_', false)">décocher</a>
			tous les enseignements
			<input type='hidden' name='num_fich' value='<?php echo $num_fich; ?>' />
		</p>
		<table width='100%' summary="Tableau de choix des classes">
			<tr valign='top' align='center'>
				<td align='left'>
<?php
				echo add_token_field();

				$nombreligne=mysqli_num_rows($res);
				$nbcol=3;
				$nb_par_colonne=round($nombreligne/$nbcol);

				for($i=0;$i<count($groups);$i++) {
					$current_group=$groups[$i];

					if(($i>0)&&(round($i/$nb_par_colonne)==$i/$nb_par_colonne)){
?>
				</td>
				<td align='left'>
<?php
					}
?>
					<input type='checkbox' name='id_groupe[]' id='id_groupe_<?php echo $i; ?>' 
						   value='<?php echo $current_group['id']; ?>'
						   onchange="checkbox_change('id_groupe_<?php echo $i; ?>')"
						   />
					<label for='id_groupe_<?php echo $i; ?>'>
						<span id='texte_id_groupe_<?php echo $i; ?>'>
							<?php echo $current_group['name']; ?>
							(<i><?php echo $current_group['classlist_string']; ?></i>)
						</span>
					</label>
					<br />
<?php
				}
?>
				</td>
			</tr>
		</table>
		<p style='text-indent:-3em; margin-left:3em;'>
			Extraire les élèves inscrits dans les classes choisies&nbsp;:
			<br />
			<input type='radio' name='num_periode' id='num_periode2_nimporte' value='nimporte' checked='checked' />
			<label for='num_periode2_nimporte' id='texte_num_periode2_nimporte'>
				Quelle que soit la période
			</label>
			<br />
			
<?php

				for($loop=1;$loop<=$maxper;$loop++) {
?>
			<input type='radio' name='num_periode' id='num_periode2_<?php echo $loop; ?>' 
				   value='<?php $loop; ?>' />
			<label for='num_periode2_<?php echo $loop; ?>' id='texte_num_periode2_<?php echo $loop; ?>'>
				Période <?php echo $loop; ?></label><br />
<?php
				}
?>
		</p>
		<p>
			<input type='radio' name='mode_pub' id='mode_pub3' value='' 
				   checked='checked' onchange="change_style_radio();" />
			<label for='mode_pub3' id='texte_mode_pub3' style='font-weight:bold;'>
				Générer un seul fichier même si vous sélectionnez plusieurs classes
			</label>
			<br />ou<br />
			<input type='radio' name='mode_pub' id='mode_pub4' value='un_fichier_par_selection' 
				   onchange="change_style_radio();" />
			<label for='mode_pub4' id='texte_mode_pub4'>Générer un fichier par classe sélectionnée.</label>
			<br />
			<span style='margin-left:2em;'>
				<input type='checkbox' name='zipper' id='zipper2' value='y' 
					   onchange="checkbox_change(this.id); check_choix_zip('2');" />
				<label for='zipper2' id='texte_zipper2'>
					Dans ce deuxième cas, zipper l'ensemble de ces fichiers en une seule archive ZIP.
				</label>
			</span>
			<br />
		</p>
		<p class='center'>
			<input type='submit' value='Envoyer' id='bouton_submit2' />
			<input type='button' value='Envoyer' id='bouton_submit_js2' 
				   onclick="valider_publipostage('form2', 'id_groupe_')" style='display:none;' />
		</p>
	</fieldset>
</form>
<?php

				if(count($groups)>$cpt_js) {
					$cpt_js=count($groups);
				}

			}
		}

?>
<script type='text/javascript'>
<?php
		echo js_checkbox_change_style()." ".js_change_style_radio()
?>
function cocher_decocher(prefixe_id, mode) {
	for (var k=0;k<<?php echo $cpt_js; ?>;k++) {
		if(document.getElementById(prefixe_id+k)){
			document.getElementById(prefixe_id+k).checked=mode;
			checkbox_change(prefixe_id+k);
		}
	}
}

if(document.getElementById('bouton_submit')) {
	document.getElementById('bouton_submit').style.display='none';
}
if(document.getElementById('bouton_submit2')) {
	document.getElementById('bouton_submit2').style.display='none';
}
if(document.getElementById('bouton_submit_js')) {
	document.getElementById('bouton_submit_js').style.display='';
}
if(document.getElementById('bouton_submit_js2')) {
	document.getElementById('bouton_submit_js2').style.display='';
}

function valider_publipostage(form_id, prefixe_id) {
	var envoyer='n';
	for(k=0;k<<?php echo $cpt_js; ?>;k++) {
		if(document.getElementById(prefixe_id+k)){
			if(document.getElementById(prefixe_id+k).checked==true) {
				envoyer='y';
				break;
			}
		}
	}

	if(envoyer=='n') {
		alert('Aucun groupe ou classe n\'a été sélectionné.');
	}
	else {
		document.getElementById(form_id).submit();
	}
}

function check_choix_zip(num) {
	if(document.getElementById('zipper'+num)){
		if(document.getElementById('zipper'+num).checked==true) {
			if(num=='') {
				document.getElementById('mode_pub2').checked=true;
			}
			else {
				document.getElementById('mode_pub4').checked=true;
			}
			change_style_radio();
		}
	}
}

</script>

<p style='margin-top:1em; margin-left:3.5em; text-indent:-3.5em; line-height: 1.5em;'>
	<em>Note&nbsp;:</em> 
	Si vous générez un fichier par classe, imprimer les fichiers un par un peut être fastidieux.
	<br />
	Vous pouvez effectuer l'impression en ligne de commande.<br />
	Téléchargez le Zip, extrayez le dans un nouveau dossier et de là&nbsp;:<br />
	Vers l'imprimante par défaut&nbsp;:<br />
	&nbsp;&nbsp;&nbsp;
	<span style='color:white; background-color:black'>libreoffice -p *.ods</span>
	<br />
	Ou vers une imprimante particulière (<em>nommée Toshiba_estudio dans l'exemple qui suit</em>)&nbsp;:
	<br />
	&nbsp;&nbsp;&nbsp;
	<span style='color:white; background-color:black; padding:3px;'>
		libreoffice --pt Toshiba_estudio *.ods
	</span>
	<br />
	Voir l'aide 
	<a href='https://help.libreoffice.org/Common/Starting_the_Software_With_Parameters/fr'>
		https://help.libreoffice.org/Common/Starting_the_Software_With_Parameters/fr
	</a> 
	pour plus de détails.
</p>
<?php

	}
	else {
?>
	| <a href='".$_SERVER['PHP_SELF']."?num_fich=$num_fich'>Choisir une autre classe ou enseignement</a>
</p>
<p>
	PLOP
</p>
<?php
	}
}

require_once("../lib/footer.inc.php");
?>
