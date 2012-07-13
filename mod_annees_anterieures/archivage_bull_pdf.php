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
//include("../lib/initialisationsPropel.inc.php");
//require_once("./fonctions_annees_anterieures.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();};

// INSERT INTO droits VALUES ('/mod_annees_anterieures/archivage_bull_pdf.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génération archives bulletins PDF', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}


$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);

$generer_fichiers_pdf_archivage=isset($_POST['generer_fichiers_pdf_archivage']) ? $_POST['generer_fichiers_pdf_archivage'] : (isset($_GET['generer_fichiers_pdf_archivage']) ? $_GET['generer_fichiers_pdf_archivage'] : NULL);

$archivage_fichiers_bull_pdf_auto=isset($_POST['archivage_fichiers_bull_pdf_auto']) ? $_POST['archivage_fichiers_bull_pdf_auto'] : (isset($_GET['archivage_fichiers_bull_pdf_auto']) ? $_GET['archivage_fichiers_bull_pdf_auto'] : "n");

// Si le module n'est pas activé...
if($gepiSettings['active_annees_anterieures'] !="y"){
	header("Location: ../logout.php?auto=1");
	die();
}

$msg="";

if (isset($_GET['suppr_temp']) and ($_GET['suppr_temp']=="y")) {
	check_token();

	$dossier_archivage_pdf=$_GET['dossier_archivage_pdf'];
	$dossier_archivage_pdf_nettoye=preg_replace("/[^A-Za-z0-9_]/","",$dossier_archivage_pdf);
	if(($dossier_archivage_pdf=='')||($dossier_archivage_pdf!=$dossier_archivage_pdf_nettoye)) {
		$msg.="Le dossier à supprimer '$dossier_archivage_pdf' n'est pas convenable: $dossier_archivage_pdf_nettoye";
	}
	else {
		$suppr=deltree("../temp/".get_user_temp_directory()."/".$dossier_archivage_pdf);
		if ($suppr) {
			$msg .= "Succès de la suppression.";
		} else {
			$msg .= "Échec de la suppression.<br />";
			//$msg .= "Un <a href='../gestion/gestion_temp_dir.php'>nettoyage des dossiers temporaires</a> est recommandée.";
			$msg .= "Un Nettoyage des dossiers temporaires est recommandée.";
		}
	}

	if(isset($_SESSION['chgt_annee'])) {
		$dest="../gestion/changement_d_annee.php";
	}
	else {
		$dest="./index.php";
	}

	header("Location: $dest?msg=$msg");
	die();
}

if(isset($_GET['chgt_annee'])) {$_SESSION['chgt_annee']="y";}

//$themessage  = 'Etes-vous sûr de vouloir supprimer toutes les données concerant cette année ?';

//**************** EN-TETE *****************
$titre_page = "Générer les bulletins PDF par élève";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************


if(!isset($generer_fichiers_pdf_archivage)){
	echo "<div class='norme'><p class=bold><a href='";
	if(isset($_SESSION['chgt_annee'])) {
		echo "../gestion/changement_d_annee.php";
	}
	else {
		echo "./index.php";
	}
	echo "'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | \n";
	echo "</p></div>\n";

	echo "<p>Pour chaque élève, un fichier PDF des N périodes de l'année va être généré dans un dossier temporaire.</p>\n";

	//echo "<p><a href='".$_SERVER['PHP_SELF']."?generer_fichiers_pdf_archivage=y".add_token_in_url()."'>Générer les PDF par élève</a></p>\n";

	echo "<form enctype=\"multipart/form-data\" name= \"formulaire\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
	echo add_token_field();

	echo "<p>Nommage des fichiers&nbsp;:<br />Inclure dans le nom de fichier pour chaque élève&nbsp;:</p>\n";
	echo "<input type='checkbox' id='arch_bull_nom_prenom' name='arch_bull_nom_prenom' value='yes'";
	$arch_bull_nom_prenom=getPref($_SESSION['login'],'arch_bull_nom_prenom', 'yes');
	if($arch_bull_nom_prenom=='yes') {echo " checked";}
	echo " /><label for='arch_bull_nom_prenom'> Nom prénom de l'élève</label><br />\n";

	echo "<input type='checkbox' id='arch_bull_INE' name='arch_bull_INE' value='yes'";
	$arch_bull_INE=getPref($_SESSION['login'],'arch_bull_INE', 'yes');
	if($arch_bull_INE=='yes') {echo " checked";}
	echo " /><label for='arch_bull_INE'> INE (<em>numéro national de l'élève</em>)</label><br />\n";

	echo "<input type='checkbox' id='arch_bull_annee_scolaire' name='arch_bull_annee_scolaire' value='yes'";
	$arch_bull_annee_scolaire=getPref($_SESSION['login'],'arch_bull_annee_scolaire', 'yes');
	if($arch_bull_annee_scolaire=='yes') {echo " checked";}
	echo " /><label for='arch_bull_annee_scolaire'> Année scolaire</label><br />\n";

	echo "<input type='checkbox' id='arch_bull_date_edition' name='arch_bull_date_edition' value='yes'";
	$arch_bull_date_edition=getPref($_SESSION['login'],'arch_bull_date_edition', 'yes');
	if($arch_bull_date_edition=='yes') {echo " checked";}
	echo " /><label for='arch_bull_date_edition'> Date d'édition</label><br />\n";

	echo "<input type='checkbox' id='arch_bull_classe' name='arch_bull_classe' value='yes'";
	$arch_bull_classe=getPref($_SESSION['login'],'arch_bull_classe', 'yes');
	if($arch_bull_classe=='yes') {echo " checked";}
	echo " /><label for='arch_bull_classe'> Classe de l'élève</label><br />\n";

	echo "<p>Parcourir les élèves par tranches de&nbsp;: <input type='text' name='arch_bull_eff_tranche' size='2' value='".getPref($_SESSION['login'],'arch_bull_eff_tranche',10)."' /><br />\n";
	echo "<input type='hidden' name='generer_fichiers_pdf_archivage' value='y' />\n";
	echo "<input type=\"submit\" name='ok' value=\"Générer les PDF par élève\" style=\"font-variant: small-caps;\" /></p>\n";
	echo "</form>\n";

	echo "<br />\n";
	echo "<p><em>NOTES&nbsp;:</em></p>
	<ul>
		<li>L'opération d'archivage est assez lourde.<br />Si vous parcourez les élèves par trop grosses tranches, vous risquez de dépasser le 'max_execution_time' de votre serveur.</li>
		<li>L'un au moins des champs permettant d'identifier l'élève doit être sélectionné.<br />Si, ni le Nom_prénom, ni l'INE ne sont sélectionnés, les deux champs seront automatiquement sélectionnés.</li>
	</ul>\n";
}
else {
	function zip_bull_pdf($dossier_a_traiter) {
		$path = path_niveau();
		$dirname = "backup/".getSettingValue("backup_directory")."/";
		if (!defined('PCLZIP_TEMPORARY_DIR') || constant('PCLZIP_TEMPORARY_DIR')!=$path.$dirname) {
			@define( 'PCLZIP_TEMPORARY_DIR', $path.$dirname );
		}

		require_once($path.'lib/pclzip.lib.php');

		$dossier_a_traiter="../temp/".get_user_temp_directory()."/".$dossier_a_traiter;

		$nom_fichier="_bulletins_pdf_individuels_eleves_".strftime('%Y%m%d_%H%M%S').".zip";
		$chemin_stockage = $path.$dirname.$nom_fichier;
		$dossier_dans_archive = "_bulletins_pdf_individuels_eleves_".strftime('%Y%m%d'); //le nom du dossier dans l'archive créée

		if ($chemin_stockage !='') {
			$archive = new PclZip($chemin_stockage);
			$v_list = $archive->create($dossier_a_traiter,
				  PCLZIP_OPT_REMOVE_PATH,$dossier_a_traiter,
				  PCLZIP_OPT_ADD_PATH, $dossier_dans_archive);

			if ($v_list == 0) {
				return "Erreur : ".$archive->errorInfo(TRUE);
			} else {
				return "Archive zip créée&nbsp;: <a href='$chemin_stockage'>$nom_fichier</a>";
			}
		}
	}

	echo "<div class='norme'><p class=bold><a href='";
	if(isset($_SESSION['chgt_annee'])) {
		echo "../gestion/changement_d_annee.php";
	}
	else {
		echo "./index.php";
	}
	echo "'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";

	check_token(false);

	//$sql="SELECT * FROM classes ORDER BY classe;";
	$sql="SELECT DISTINCT c.* FROM classes c, periodes p WHERE c.id=p.id_classe ORDER BY classe;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		echo "<p>Aucune classe trouvée.</p>";
		require("../lib/footer.inc.php");
		die();
	}

	$cpt=0;
	$tab_classe=array();
	while($lig=mysql_fetch_object($res)) {
		$tab_classe[$cpt]['id_classe']=$lig->id;
		$tab_classe[$cpt]['classe']=$lig->classe;
		//echo "<p>\$tab_classe[$cpt]['id_classe']=".$tab_classe[$cpt]['id_classe']."<br />";
		//echo "\$tab_classe[$cpt]['classe']=".$tab_classe[$cpt]['classe']."</p>";
		$cpt++;
	}

	if(isset($id_classe)) {
		$dossier_archivage_pdf=getPref($_SESSION['login'], 'dossier_archivage_pdf', 'bulletins_pdf_individuels_eleves_'.strftime('%Y%m%d'));

		if(isset($_GET['ele_chgt_classe'])) {
			$sql="SELECT DISTINCT col1 FROM tempo2;";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)>0) {
				echo "<p>Il reste à traiter ".mysql_num_rows($test)." élève(s) ayant changé de classe en cours d'année.</p>\n";
				$ele_chgt_classe="y";
			}
			else {
				echo "<p>L'archivage est terminé.</p>\n";

				//echo "<p style='color:red'>Il reste à réaliser le Zip des fichiers PDF.</p>";
				echo "<p>".zip_bull_pdf($dossier_archivage_pdf)."</p>\n";

				echo "<p>Dossier temporaire d'archivage&nbsp;: <a href='../temp/".get_user_temp_directory()."/".$dossier_archivage_pdf."/' target='_blank'>$dossier_archivage_pdf</a></p>\n";

				echo "<br /><p>Pour des questions de place, il est recommandé de <a href='".$_SERVER['PHP_SELF']."?suppr_temp=y&amp;dossier_archivage_pdf=$dossier_archivage_pdf".add_token_in_url()."'>supprimer le dossier temporaire</a>.<br />Commencez cependant par récupérer l'archive ZIP<br />(<em>si vous n'en faites rien, vous pourrez néanmoins récupérer cette archive dans la page de <a href='../gestion/accueil_sauve.php'>Sauvegarde/Restauration de Gepi</a></em>).</p>";

				require("../lib/footer.inc.php");
				die();
			}
		}
		else {
			// Reste-t-il des élèves à parcourir dans cette classe?
			$sql="SELECT col2 FROM tempo2 WHERE col1='$id_classe';";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)>0) {
				for($loop=0;$loop<$cpt;$loop++) {
					if($tab_classe[$loop]['id_classe']==$id_classe) {
						$classe=$tab_classe[$loop]['classe'];
						break;
					}
				}

				echo "<p>Il reste ".mysql_num_rows($test)." élève(s) à parcourir dans la classe de $classe.<br />";
			}
			else {
				// Recherche de la classe suivante:
				/*
				echo "Recherche de la classe suivante<br />";
				echo "<pre>";
				echo print_r($tab_classe);
				echo "</pre>";
				*/
				$trouve="n";
				for($loop=0;$loop<$cpt;$loop++) {
					if((isset($tab_classe[$loop-1]['id_classe']))&&($tab_classe[$loop-1]['id_classe']==$id_classe)) {
						//echo "\$tab_classe[$loop-1]['id_classe']=".$tab_classe[$loop-1]['id_classe']."<br />";
						$id_classe=$tab_classe[$loop]['id_classe'];
						$classe=$tab_classe[$loop]['classe'];
						$trouve="y";
						break;
					}
				}

				if($trouve=='n') {
					// On a parcouru toutes les classes:
					echo "<p>Toutes les classes ont été parcourues.<br />Il ne reste que les élèves ayant changé de classe à traiter.</p>\n";

					$sql="SELECT DISTINCT login, id_classe FROM j_eleves_classes ORDER BY login, id_classe;";
					$res_ele_classe=mysql_query($sql);
					if(mysql_num_rows($res)>0) {
						//$tab_login_ele_chgt_classe=array();
						//$tab_id_classe_chgt_classe=array();

						// Normalement, à ce stade, la table est vide
						$sql="TRUNCATE tempo2;";
						$menage=mysql_query($sql);

						$ele_prec="";
						while($lig=mysql_fetch_object($res_ele_classe)) {
							if($lig->login==$ele_prec) {
								/*
								if(!in_array($lig->login, $tab_login_ele_chgt_classe)) {
									$tab_login_ele_chgt_classe[]=$lig->login;
								}

								if(!in_array($lig->id_classe, $tab_id_classe_chgt_classe)) {
									$tab_id_classe_chgt_classe[]=$lig->id_classe;
								}
								*/

								$sql="INSERT INTO tempo2 SET col1='$lig->login';";
								$insert=mysql_query($sql);
							}
							$ele_prec=$lig->login;
						}
					}

					if(!isset($tab_login_ele_chgt_classe)) {
						echo "<p>Aucun élève n'a changé de classe en cours d'année.</p>\n";

						echo "<p>Dossier temporaire d'archivage&nbsp;: <a href='../temp/".get_user_temp_directory()."/".$dossier_archivage_pdf."/' target='_blank'>$dossier_archivage_pdf</a></p>\n";

						echo "<br />\n";

						//echo "<p style='color:red'>Il reste à réaliser le Zip des fichiers PDF.</p>";
						echo "<p>".zip_bull_pdf($dossier_archivage_pdf)."</p>\n";

						echo "<br /><p>Pour des questions de place, il est recommandé de <a href='".$_SERVER['PHP_SELF']."?suppr_temp=y&amp;dossier_archivage_pdf=$dossier_archivage_pdf".add_token_in_url()."'>supprimer le dossier temporaire</a>.<br />Commencez cependant par récupérer l'archive ZIP<br />(<em>si vous n'en faites rien, vous pourrez néanmoins récupérer cette archive dans la page de <a href='../gestion/accueil_sauve.php'>Sauvegarde/Restauration de Gepi</a></em>).</p>";

						require("../lib/footer.inc.php");
						die();
					}
					else {
						$ele_chgt_classe="y";
					}
				}
			}
		}
	}
	else {
		// Premier passage:
		$arch_bull_eff_tranche=isset($_POST['arch_bull_eff_tranche']) ? $_POST['arch_bull_eff_tranche'] : 10;
		if((!is_numeric($arch_bull_eff_tranche))||($arch_bull_eff_tranche<1)) {$arch_bull_eff_tranche=10;}
		savePref($_SESSION['login'],'arch_bull_eff_tranche',$arch_bull_eff_tranche);

		$arch_bull_nom_prenom=isset($_POST['arch_bull_nom_prenom']) ? $_POST['arch_bull_nom_prenom'] : 'no';
		$arch_bull_INE=isset($_POST['arch_bull_INE']) ? $_POST['arch_bull_INE'] : 'no';
		if(($arch_bull_nom_prenom!='yes')&&($arch_bull_INE!='yes')) {
			$arch_bull_nom_prenom='yes';
			$arch_bull_INE='yes';
		}
		savePref($_SESSION['login'],'arch_bull_nom_prenom',$arch_bull_nom_prenom);
		savePref($_SESSION['login'],'arch_bull_INE',$arch_bull_INE);

		$arch_bull_annee_scolaire=isset($_POST['arch_bull_annee_scolaire']) ? $_POST['arch_bull_annee_scolaire'] : 'no';
		savePref($_SESSION['login'],'arch_bull_annee_scolaire',$arch_bull_annee_scolaire);

		$arch_bull_date_edition=isset($_POST['arch_bull_date_edition']) ? $_POST['arch_bull_date_edition'] : 'no';
		savePref($_SESSION['login'],'arch_bull_date_edition',$arch_bull_date_edition);

		$arch_bull_classe=isset($_POST['arch_bull_classe']) ? $_POST['arch_bull_classe'] : 'no';
		savePref($_SESSION['login'],'arch_bull_classe',$arch_bull_classe);

		$dossier_archivage_pdf=savePref($_SESSION['login'], 'dossier_archivage_pdf', 'bulletins_pdf_individuels_eleves_'.strftime('%Y%m%d'));
		@mkdir("../temp/".get_user_temp_directory()."/".$dossier_archivage_pdf);

		// On va faire la liste des élèves:
		$sql="TRUNCATE tempo2;";
		$menage=mysql_query($sql);

		$sql="INSERT INTO tempo2 (SELECT DISTINCT id_classe, login FROM j_eleves_classes ORDER BY id_classe, login);";
		$insert=mysql_query($sql);

		// On commence avec la première classe:
		$id_classe=$tab_classe[0]['id_classe'];
		$classe=$tab_classe[0]['classe'];
	}

	//echo "<form enctype=\"multipart/form-data\" name= \"formulaire\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
	echo "<form enctype=\"multipart/form-data\" name= \"formulaire\" action=\"../bulletin/bull_index.php\" method=\"post\">\n";

	// Boucler sur les classes
	if(isset($ele_chgt_classe)) {
		echo "<p>Archivage des élèves ayant changé de classe en cours d'année&nbsp;: ";
		echo "<input type='hidden' name='ele_chgt_classe' value='y' />\n";
		// Ce témoin rendra inopérantes les valeurs des champs tab_id_classe[] et tous_les_eleves
	}
	else {
		echo "<p>Archiver la classe de $classe&nbsp;: ";
	}
	echo "<input type='hidden' name='mode_bulletin' value='pdf' />\n";
	echo "<input type='hidden' name='type_bulletin' value='-1' />\n";

	echo "<input type='hidden' name='bull_pdf_debug' value='n' />\n";
	echo "<input type='hidden' name='generer_fichiers_pdf_archivage' value='y' />\n";

	echo "<input type='hidden' name='choix_periode_num' value='fait' />\n";

	echo "<input type='hidden' name='un_seul_bull_par_famille' value='oui' />\n";

	echo "<input type='hidden' name='b_adr_pg' value='xx' />\n";

	echo "<input type='hidden' name='bouton_valide_select_eleves1' value='Valider' />\n";
	echo "<input type='hidden' name='valide_select_eleves' value='y' />\n";

	echo "<input type='hidden' name='tab_id_classe[]' value='$id_classe' />\n";

	// Pour ne pas avoir à poster la liste des élèves: (sauf pour la dernière étape avec les élèves qui ont changé de classe)
	echo "<input type='hidden' name='tous_les_eleves' value='y' />\n";

	// Pour ne pas avoir à faire la liste des périodes à ce stade:
	echo "<input type='hidden' name='tab_periode_num[]' value='1' />\n";
	echo "<input type='hidden' name='toutes_les_periodes' value='y' />\n";

	echo "<br />\n";
	echo "<input type='checkbox' name='archivage_fichiers_bull_pdf_auto' id='archivage_fichiers_bull_pdf_auto' value='y' ";
	if($archivage_fichiers_bull_pdf_auto=='y') {echo "checked ";}
	echo "/><label for='archivage_fichiers_bull_pdf_auto'> Boucler automatiquement sur la liste des ";
	if(isset($ele_chgt_classe)) {
		echo "élèves";
	}
	else {
		echo "classes";
	}
	echo "</label>\n";

	echo "<br />\n";
	echo "<span id='bouton_validation'><input type='submit' name='valider' value='Valider' /></span>\n";
	echo "</p>\n";

	echo "</form>\n";

	if($archivage_fichiers_bull_pdf_auto=='y') {
		echo "<script type='text/javascript'>
	document.getElementById('bouton_validation').innerHTML='Dans un instant... ou un peu plus;)';
	setTimeout('document.formulaire.submit()', 2000);
</script>\n";
	}

}

echo "<br />\n";
require("../lib/footer.inc.php");
?>
