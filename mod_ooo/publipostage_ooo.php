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

if((isset($num_fich))&&((isset($id_classe))||(isset($id_groupe)))) {

	$tab_file=get_tab_file($path);

	$tab_eleves_OOo=array();
	$nb_eleve=0;

	if(isset($id_classe)) {
		for($i=0;$i<count($id_classe);$i++) {
			$classe=get_class_from_id($id_classe[$i]);

			$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE jec.login=e.login AND jec.id_classe='$id_classe[$i]' ORDER BY e.nom, e.prenom;";
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
	else {
		for($i=0;$i<count($id_groupe);$i++) {
			$current_group=get_group($id_groupe[$i]);

			$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_groupes jeg WHERE jeg.login=e.login AND jeg.id_groupe='$id_groupe[$i]' ORDER BY e.nom, e.prenom;";
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

echo "<p class='bold'><a href='../accueil.php";
echo "'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

if(!isset($num_fich)) {
	echo "</p>\n";

	if(isset($_FILES['monfichier'])) {
		check_token(false);

		$t=$_FILES['monfichier'];
		
		$monfichiername=$t['name'];
		$monfichiertype=$t['type'];
		$monfichiersize=$t['size'];
		$monfichiertmp_name=$t['tmp_name'];

		$upload_modele_ooo_autorise="n";
		if($_SESSION['statut']=='administrateur') {$upload_modele_ooo_autorise="y";}
		elseif(($_SESSION['statut']=='scolarite')&&(getSettingValue('OOoUploadScol')=='yes')) {$upload_modele_ooo_autorise="y";}
		elseif(($_SESSION['statut']=='cpe')&&(getSettingValue('OOoUploadCpe')=='yes')) {$upload_modele_ooo_autorise="y";}
		elseif(($_SESSION['statut']=='professeur')&&(getSettingValue('OOoUploadProf')=='yes')) {$upload_modele_ooo_autorise="y";}
	
		if($upload_modele_ooo_autorise!='y') {
			echo "<p style='color:red'>Action non autorisée&nbsp;: Upload d'un modèle personnalisé.</p>\n";
			tentative_intrusion(1, "Tentative non autorisée d'upload d'un modèle OOo (".$monfichiername.")");
		}
		else {

			if ($monfichiername=="") {
				echo "<p style='color:red'>Pas de fichier indiqué ! Il faut recommencer...</p>\n";
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
								echo "<p style='color:red;'>ERREUR lors de la création du dossier de modèle openDocument pour ".$login_user[$i]."</p>\n";
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
				echo "<a href='".$_SERVER['PHP_SELF']."?num_fich=$i' title=\"Effectuer un publipostage OOo avec ce fichier modèle\">".$tab_file[$i]."</a> - <a href='mes_modeles/".$_SESSION['login']."/".$tab_file[$i]."' target='_blank'><img src='../images/edit16.png' width='16' height='16' title=\"Éditer le fichier ".$tab_file[$i]."\" /></a> - <a href='".$_SERVER['PHP_SELF']."?suppr_fich=$i".add_token_in_url()."'><img src='../images/delete16.png' width='16' height='16' title=\"Supprimer le fichier ".$tab_file[$i]."\" /></a><br />";
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
		echo "<form method='post' ENCTYPE='multipart/form-data' action='".$_SERVER['PHP_SELF']."'>\n";
		echo add_token_field();
		echo "<p>Mettre en place un nouveau modèle&nbsp;:</p>\n";
		echo "<INPUT TYPE=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"512000\">";
	
		if($_SESSION['statut']=='administrateur') {
			echo "<p>Pour quel(s) utilisateur(s) souhaitez-vous mettre en place le modèle&nbsp;? ";
			echo "<a href='javascript:cocher_decocher(true)'>Tout cocher</a> / <a href='javascript:cocher_decocher(false)'>Tout décocher</a>\n";
			echo "</p>\n";

			echo liste_checkbox_utilisateurs(array('administrateur', 'scolarite', 'cpe', 'professeur'), array($_SESSION['login']));
			/*
			$sql="SELECT login, civilite, nom, prenom, statut FROM utilisateurs WHERE statut='administrateur' OR statut='scolarite' OR statut='cpe' OR statut='professeur' AND etat='actif' ORDER BY statut, login, nom, prenom;";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)>0) {
				$nombreligne=mysql_num_rows($res);
				$nbcol=3;
				$nb_par_colonne=round($nombreligne/$nbcol);
	
				echo "<table width='100%' summary=\"Tableau de choix des utilisateurs auxquels distribuer le modèle\">\n";
				echo "<tr valign='top' align='center'>\n";
				echo "<td align='left'>\n";

				$cpt=0;
				$statut_prec="";
				while($lig=mysql_fetch_object($res)) {
					if(($cpt>0)&&(round($cpt/$nb_par_colonne)==$cpt/$nb_par_colonne)){
						echo "</td>\n";
						echo "<td align='left'>\n";
					}
	
					if($lig->statut!=$statut_prec) {
						echo "<p><b>".ucfirst($lig->statut)."</b><br />\n";
						$statut_prec=$lig->statut;
					}
	
					echo "<input type='checkbox' name='login_user[]' id='login_user_$cpt' value='$lig->login' ";
					echo "onchange=\"checkbox_change('login_user_$cpt')\" ";
					if($lig->login==$_SESSION['login']) {
						echo "checked ";
						$temp_style=" style='font-weight: bold;'";
					}
					else {
						$temp_style="";
					}
					echo "/><label for='login_user_$cpt'><span id='texte_login_user_$cpt'$temp_style>$lig->civilite $lig->nom $lig->prenom</span></label><br />\n";
	
					$cpt++;
				}
				echo "</td>\n";
				echo "</tr>\n";
				echo "</table>\n";

				echo "<script type='text/javascript'>
function cocher_decocher(mode) {
	for (var k=0;k<$cpt;k++) {
		if(document.getElementById('login_user_'+k)){
			document.getElementById('login_user_'+k).checked=mode;
			checkbox_change('login_user_'+k);
		}
	}
}
</script>\n";

			}
			*/
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

		if($_SESSION['statut']=='administrateur') {
			echo "<p style='color:red'>A FAIRE : Permettre à l'administrateur de faire le ménage dans les fichiers modèles des autres utilisateurs.<br />Permettre de limiter les champs auxquels ont accès les utilisateurs selon leur statut.</p>\n";
		}
	}
}
else {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir un autre modèle</a>";
	if((!isset($id_classe))&&(!isset($id_groupe))) {
		echo "</p>\n";
	
		$tab_file=get_tab_file($path);
	
		// Choix de la classe/groupe
	
		if($_SESSION['statut']=='professeur') {
			$sql="SELECT c.id, c.classe FROM classes c, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE c.id=jgc.id_classe AND jgc.id_groupe=jgp.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe;";
		}
		else {
			$sql="SELECT c.id, c.classe FROM classes c ORDER BY c.classe;";
		}
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			echo "<form method='post' ENCTYPE='multipart/form-data' action='".$_SERVER['PHP_SELF']."'>\n";
			echo "<p>Pour quelle(s) classe(s) souhaitez-vous imprimer le document <b>".$tab_file[$num_fich]."</b>&nbsp;?";
			echo " <a href=\"javascript:cocher_decocher('id_classe_', true)\">Cocher</a> / <a href=\"javascript:cocher_decocher('id_classe_', false)\">décocher</a> toutes les classes\n";
			echo "</p>\n";
			echo add_token_field();
			echo "<input type='hidden' name='num_fich' value='$num_fich' />\n";

			$nombreligne=mysqli_num_rows($res);
			$nbcol=3;
			$nb_par_colonne=round($nombreligne/$nbcol);

			echo "<table width='100%' summary=\"Tableau de choix des classes\">\n";
			echo "<tr valign='top' align='center'>\n";
			echo "<td align='left'>\n";

			$cpt=0;
			while($lig=mysqli_fetch_object($res)) {
				if(($cpt>0)&&(round($cpt/$nb_par_colonne)==$cpt/$nb_par_colonne)){
					echo "</td>\n";
					echo "<td align='left'>\n";
				}

				echo "<input type='checkbox' name='id_classe[]' id='id_classe_$cpt' value='$lig->id' ";
				echo "onchange=\"checkbox_change('id_classe_$cpt')\" ";
				echo "/><label for='id_classe_$cpt'><span id='texte_id_classe_$cpt'>$lig->classe</span></label><br />\n";

				$cpt++;
			}
			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";

			echo "<input type='submit' value='Envoyer' />\n";
			echo "</form>\n";
		}
	
		if($_SESSION['statut']=='professeur') {
			$groups=get_groups_for_prof($_SESSION['login']);
			if(count($groups)>0) {
				echo "<form method='post' ENCTYPE='multipart/form-data' action='".$_SERVER['PHP_SELF']."'>\n";
				echo "<p>Pour quel enseignement souhaitez-vous imprimer le document ".$tab_file[$num_fich]."&nbsp;?";
				echo " <a href=\"javascript:cocher_decocher('id_groupe_', true)\">Cocher</a> / <a href=\"javascript:cocher_decocher('id_groupe_', false)\">décocher</a> tous les enseignements\n";
				echo "</p>\n";
				echo add_token_field();
				echo "<input type='hidden' name='num_fich' value='$num_fich' />\n";

				$nombreligne=mysqli_num_rows($res);
				$nbcol=3;
				$nb_par_colonne=round($nombreligne/$nbcol);
	
				echo "<table width='100%' summary=\"Tableau de choix des classes\">\n";
				echo "<tr valign='top' align='center'>\n";
				echo "<td align='left'>\n";

				for($i=0;$i<count($groups);$i++) {
					$current_group=$groups[$i];

					if(($i>0)&&(round($i/$nb_par_colonne)==$i/$nb_par_colonne)){
						echo "</td>\n";
						echo "<td align='left'>\n";
					}
					echo "<input type='checkbox' name='id_groupe[]' id='id_groupe_$i' value='".$current_group['id']."' ";
					echo "onchange=\"checkbox_change('id_groupe_$i')\" ";
					echo "/><label for='id_groupe_$i'><span id='texte_id_groupe_$i'>".$current_group['name']." (<i>".$current_group['classlist_string']."</i>)</span></label><br />\n";
				}
				echo "</td>\n";
				echo "</tr>\n";
				echo "</table>\n";
				echo "<p class='center'><input type='submit' value='Envoyer' /></p>\n";
				echo "</form>\n";
			}
		}

		echo "<script type='text/javascript'>
function cocher_decocher(prefixe_id, mode) {
	for (var k=0;k<$cpt;k++) {
		if(document.getElementById(prefixe_id+k)){
			document.getElementById(prefixe_id+k).checked=mode;
			checkbox_change(prefixe_id+k);
		}
	}
}

</script>\n";

	}
	else {
		echo " | <a href='".$_SERVER['PHP_SELF']."?num_fich=$num_fich'>Choisir une autre classe ou enseignement</a>";
		echo "</p>\n";

		echo "PLOP";
	}
}


echo "<script type='text/javascript'>
function checkbox_change(id_cpt) {
	if(document.getElementById(id_cpt)) {
		if(document.getElementById(id_cpt).checked) {
			document.getElementById('texte_'+id_cpt).style.fontWeight='bold';
		}
		else {
			document.getElementById('texte_'+id_cpt).style.fontWeight='normal';
		}
	}
}
</script>\n";


require_once("../lib/footer.inc.php");
?>
