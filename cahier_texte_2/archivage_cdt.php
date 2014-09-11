<?php
/*
*
* Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Gabriel Fischer
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

// Initialisation des feuilles de style après modification pour améliorer l'accessibilité
$accessibilite="y";

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

$sql="SELECT 1=1 FROM droits WHERE id='/cahier_texte_2/archivage_cdt.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/cahier_texte_2/archivage_cdt.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Archivage des CDT',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//On vérifie si le module est activé
if (getSettingValue("active_cahiers_texte")!='y') {
	die("Le module n'est pas activé.");
}

$step=isset($_POST['step']) ? $_POST['step'] : (isset($_GET['step']) ? $_GET['step'] : NULL);
$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

$confirmer_ecrasement=isset($_POST['confirmer_ecrasement']) ? $_POST['confirmer_ecrasement'] : (isset($_GET['confirmer_ecrasement']) ? $_GET['confirmer_ecrasement'] : 'n');

include('cdt_lib.php');

//**************** EN-TETE *****************
$titre_page = "Cahier de textes - Archivage";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *************

//debug_var();

//===================================
// Permettre de choisir l'ordre dans lequel exporter?
$current_ordre='ASC';

$dossier_etab=get_dossier_etab_cdt_archives();
//===================================

if(isset($_GET['chgt_annee'])) {$_SESSION['chgt_annee']="y";}

echo "<p class='bold'><a href='";
if(isset($_SESSION['chgt_annee'])) {
	echo "../gestion/changement_d_annee.php";
}
else {
	echo "../cahier_texte_admin/index.php";
}
echo "'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

//echo "<br />\$dossier_etab=$dossier_etab<br />";

if($dossier_etab=="") {
	echo "</p>\n";

	echo "<p style='color:red'>Le dossier d'archivage de l'établissement n'a pas pu être identifié.<br />
Cela ne devrait pas arriver sauf si votre Gepi était auparavant en 'multisite' et qu'il ne l'est plus.<br />
En quittant le mode multisite, il se peut que vous ayez oublié laissé un enregistrement 'multisite=y' dans la table 'setting'.<br />
Dans ce cas, passer la valeur à 'n' règlera le problème.</p>\n";
	require("../lib/footer.inc.php");
	die();
}

// Création d'un espace entre le bandeau et le reste 
//echo "<p></p>\n";

//On vérifie si le module est activé
if (getSettingValue("active_cahiers_texte")!='y') {
	echo "</p>\n";

	echo "<p class='grand centre_texte'>Le cahier de textes n'est pas accessible pour le moment.</p>\n";
	require("../lib/footer.inc.php");
	die();
}

echo " | <a href='../documents/archives/index.php'>Années archivées</a>";

echo "</p>\n";

if(!isset($step)) {

	// A FAIRE: Si multisite, ne pas permettre d'aller plus loin si le RNE n'est pas renseigné? ou utiliser le RNE récupéré de... la session?

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
	echo "<p>Vous allez archiver les cahiers de textes.</p>\n";

	$annee=preg_replace('/[^0-9a-zA-Z_-]/','_',getSettingValue('gepiYear'));
	echo "<p>Année&nbsp;: <input type='text' name='annee' value='$annee' /><br />(<i>les caractères autorisés sont les chiffres (de 0 à 9), les lettres non accentuées et les tirets (- et _)</i>)</p>\n";
	echo "<p>\n";
	echo "<input type='radio' id='mode_transfert' name='mode' value='transfert' /><label for='mode_transfert'> archiver les cahiers de textes et <b>supprimer les documents joints</b> après transfert</label><br />\n";
	echo "<input type='radio' id='mode_copie' name='mode' value='copie' checked /><label for='mode_copie'> archiver les cahiers de textes, <b>sans supprimer les documents joints</b> après archivage</label>.</p>\n";
	echo add_token_field();
	echo "<input type='hidden' name='step' value='1' />\n";
	echo "<p><input type='submit' value='Valider' /></p>\n";
	echo "</form>\n";

	echo "<p><br /></p>\n";
	echo "<p><em>NOTES&nbsp;:</em></p>\n";
	echo "<ul>\n";
	echo "<li><p>La procédure d'archivage est normalement utilisée en fin d'année.</p></li>\n";
	echo "<li><p>Lors de l'archivage, les cahiers de textes sont parcourus pour mettre en place une arborescence copie de l'arborescence des cahiers de textes.<br />La procédure ne vide pas les tables des cahiers de textes.</p></li>\n";
	echo "<li><p>Si vous souhaitez tester la procédure d'archivage, vous pouvez, à n'importe quel moment de l'année, effectuer un archivage sans transfert des documents joints.<br />Une arborescence copie sera mise en place.<br />Vous pourrez la consulter... et la supprimer si vous le souhaitez sans impact sur les cahiers de textes en cours d'utilisation.<br />En revanche, si vous cochez Transfert, les documents joints aux cahiers de textes seront déplacés.<br />Un professeur qui consulterait son cahier de textes de l'année courante, trouverait ses comptes-rendus, mais les documents joints ne seraient plus disponibles.</p></li>\n";
	echo "<li><p>En fin d'année, il est recommandé d'effectuer un archivage avec transfert des documents pour ne pas laisser de scories pour les enseignements des années suivantes (<em>et éviter d'encombrer l'arborescence du serveur de fichiers inutiles</em>).</p><p>Une fois l'archivage de fin d'année effectué, vous pourrez vider les tables des cahiers de textes dans <a href='../utilitaires/clean_tables.php'>Gestion générale/Nettoyage des tables</a><br />(<em>ce nettoyage 'manuel' des tables n'est pas indispensable; il est effectué automatiquement lors de l'initialisation de l'année si vous ne faites pas une initialisation tout à la main</em>)</p></li>\n";
	echo "<li><p>Dans les archives de CDT, les professeurs ne pourront consulter que leurs propres cahiers de textes.<br />Les comptes de statut 'administrateur', 'scolarite' auront accès à toutes les archives de cahiers de textes.<br />Les autres statuts n'y auront aucun accès.</p></li>\n";
	echo "</ul>\n";
}
else {
	check_token();

	$annee=isset($_POST['annee']) ? $_POST['annee'] : (isset($_GET['annee']) ? $_GET['annee'] : "");

	$annee_ini=$annee;
	$annee=preg_replace('/[^0-9a-zA-Z_-]/','',$annee);

	if($annee=="") {
		echo "<p style='color:red'>Le nom d'année fourni '$annee_ini' n'est pas valable.</p>\n";
		echo "<p><a href='".$_SERVER['PHP_SELF']."'>Retour</a></p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	// Stocker date archivage strftime("%Y%m%d_%H%M%S")

	// Sécurité:
	if(($dossier_etab=='index.php')||($dossier_etab=='entete.php')) {
		echo "<p style='color:red'>Le nom de dossier établissement '$dossier_etab' n'est pas valable.</p>\n";
		echo "<p><a href='".$_SERVER['PHP_SELF']."'>Retour</a></p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$dossier_annee="../documents/archives/".$dossier_etab."/cahier_texte_".$annee;
	$dossier_cdt=$dossier_annee."/cdt";
	$dossier_documents=$dossier_annee."/documents";
	$dossier_css=$dossier_annee."/css";

	if($step==1) {
		// Remplissage d'une table temporaire avec la liste des groupes.
		$sql="TRUNCATE TABLE tempo2;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(!$res) {
			echo "<p style='color:red'>ABANDON&nbsp;: Il s'est produit un problème lors du nettoyage de la table 'tempo2'.</p>\n";
			echo "<p><br /></p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		//$sql="INSERT INTO tempo2 SELECT id,name FROM groupes;";
		// On ne retient que les groupes associés à des classes... les autres sont des scories qui devraient être supprimées par un Nettoyage de la base
		$sql="INSERT INTO tempo2 SELECT id,name FROM groupes WHERE id IN (SELECT DISTINCT id_groupe FROM j_groupes_classes WHERE id_groupe NOT IN (SELECT id_groupe FROM j_groupes_visibilite WHERE domaine='cahier_texte' AND visible='n'));";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(!$res) {
			echo "<p style='color:red'>ABANDON&nbsp;: Il s'est produit un problème lors de l'insertion de la liste des groupes dans la table 'tempo2'.</p>\n";
			echo "<p><br /></p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		$sql="CREATE TABLE IF NOT EXISTS tempo3_cdt (id_classe int(11) NOT NULL default '0', classe varchar(255) NOT NULL default '', matiere varchar(255) NOT NULL default '', enseignement varchar(255) NOT NULL default '', id_groupe int(11) NOT NULL default '0', fichier varchar(255) NOT NULL default '') ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(!$res) {
			echo "<p style='color:red'>ABANDON&nbsp;: Erreur lors de la création de la table temporaire 'tempo3_cdt'.</p>\n";
			echo "<p><br /></p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		$sql="TRUNCATE TABLE tempo3_cdt;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(!$res) {
			echo "<p style='color:red'>ABANDON&nbsp;: Il s'est produit un problème lors du nettoyage de la table 'tempo3_cdt'.</p>\n";
			echo "<p><br /></p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		if(!file_exists("../documents/archives/")) {
			$res=mkdir("../documents/archives/");
			if(!$res) {
				echo "<p style='color:red;'>Erreur lors de la préparation de l'arborescence ../documents/archives/</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
		}

		if(!file_exists("../documents/archives/".$dossier_etab)) {
			//$res=mkdir("../documents/archives/".$dossier_etab);
			//$res=creer_rep_docs_joints("../documents/archives/", $dossier_etab, "../../..");
			$res=creer_rep_docs_joints("../documents/archives/", $dossier_etab);
		}

		if(!file_exists("../documents/archives/".$dossier_etab."/index.html")) {
			//$res=creer_index_logout("../documents/archives/".$dossier_etab, "../../..");
			$res=creer_index_logout("../documents/archives/".$dossier_etab);
		}

		// Page HTML à faire à ce niveau pour accéder aux différentes années...
		// Stocker dans une table la liste des années archivées?

		if(file_exists($dossier_annee)) {
			if($confirmer_ecrasement!='y') {
				echo "<p style='color:red;'>Le dossier $dossier_annee existe déjà.</p>\n";
	
				// CONFIRMER
				echo "<p>Voulez-vous, malgré tout, procéder à nouveau à l'archivage des cahiers de textes?<br />Les pages archivées seront écrasées.<br />Vous devriez peut-être commencer par télécharger les pages actuellement archivées par précaution.</p>\n";
	
				echo "<p><a href='".$_SERVER['PHP_SELF']."?confirmer_ecrasement=y&amp;step=1&amp;mode=$mode&amp;annee=$annee".add_token_in_url()."'>Archiver à nouveau</a>.</p>";
	
				require("../lib/footer.inc.php");
				die();
			}

			echo "<p style='font-weight: bold;'>Le dossier $dossier_annee existe déjà.</p>\n";
			echo "<p>Les pages précédemment archivées seront écrasées.</p>\n";

		}
		else {
			$res=mkdir($dossier_annee);
		}

		if(!file_exists($dossier_annee."/index.html")) {
			//$res=creer_index_logout($dossier_annee, "../../../..");
			$res=creer_index_logout($dossier_annee);
		}

		if(!file_exists($dossier_cdt)) {
			$res=mkdir($dossier_cdt);
			if(!$res) {
				echo "<p style='color:red;'>Erreur lors de la préparation de l'arborescence $dossier_cdt</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
		}
		if(!file_exists($dossier_documents)) {
			$res=mkdir($dossier_documents);
			if(!$res) {
				echo "<p style='color:red;'>Erreur lors de la préparation de l'arborescence $dossier_documents</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
		}

		if(!file_exists($dossier_documents."/index.html")) {
			//$res=creer_index_logout($dossier_annee, "../../../../..");
			$res=creer_index_logout($dossier_annee);
		}

		// On copie les feuilles de style pour:
		// 1. Se prémunir de modifications de styles dans des versions ultérieures de Gepi
		// 2. Permettre d'avoir un code couleur variant par année par exemple
		if(!file_exists($dossier_css)) {
			$res=mkdir($dossier_css);
			if(!$res) {
				echo "<p style='color:red;'>Erreur lors de la préparation de l'arborescence $dossier_css</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
		}
	
		// Copie des feuilles de styles
		$tab_styles=array("style.css", "style_old.css", "style_screen_ajout.css", "accessibilite.css", "accessibilite_print.css", "portable.css");
		for($i=0;$i<count($tab_styles);$i++) {
			if(file_exists("../".$tab_styles[$i])) {
				copy("../".$tab_styles[$i],$dossier_annee."/".$tab_styles[$i]);
			}
		}
	
		// Copie des feuilles de styles
		$tab_styles=array('bandeau_r01.css',
						'bandeau_r01_ie6.css',
						'bandeau_r01_ie7.css',
						'bandeau_r01_ie.css',
						'style.css',
						'style_ecran.css',
						'style_ecran_login.css',
						'style_ecran_login_IE.css',
						'style_imprime.css',
						'style_telephone.css',
						'style_telephone_login.css');
		for($i=0;$i<count($tab_styles);$i++) {
			if(file_exists("../css/".$tab_styles[$i])) {
				copy("../css/".$tab_styles[$i],$dossier_css."/".$tab_styles[$i]);
			}
		}

		if(!file_exists($dossier_annee."/images")) {
			$res=mkdir($dossier_annee."/images");
		}
		if(file_exists($dossier_annee."/images")) {
			$tab_img=array("add.png", "chercher.png", "close16.png","trash.png");
			for($i=0;$i<count($tab_img);$i++) {
				copy("../images/icons/".$tab_img[$i],$dossier_annee."/images/".$tab_img[$i]);
			}
		}

		if(!file_exists($dossier_annee."/js")) {
			$res=mkdir($dossier_annee."/js");
		}
		if(file_exists($dossier_annee."/js")) {
			$tab_js=array("position.js", "brainjar_drag.js");
			for($i=0;$i<count($tab_js);$i++) {
				copy("../lib/".$tab_js[$i],$dossier_annee."/js/".$tab_js[$i]);
			}
		}

		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
		echo "<p>Les préparatifs sont faits.<br />Passons à l'archivage proprement dit&nbsp;:\n";
		echo add_token_field();
		echo "<input type='hidden' name='step' value='2' />\n";
		echo "<input type='hidden' name='mode' value='$mode' />\n";
		echo "<input type='hidden' name='annee' value='$annee' />\n";
		echo "<input type='submit' value='Archiver' />\n";
		echo "</p>\n";
		echo "</form>\n";
	}
	else {

		$gepiSchoolName=getSettingValue('gepiSchoolName');
		$gepiYear=getSettingValue('gepiYear');

		$timestamp_debut_export=getSettingValue("begin_bookings");
		$timestamp_fin_export=getSettingValue("end_bookings");

		$display_date_debut=strftime("%d/%m/%Y", getSettingValue("begin_bookings"));
		$display_date_fin=strftime("%d/%m/%Y", getSettingValue("end_bookings"));

		echo "<p>Les notices vont être extraites pour des dates entre le $display_date_debut et le $display_date_fin</p>";

		$largeur_tranche=10;

		$temoin_erreur="n";

		$extension="php";

		//$nom_fichier=array();

		function corrige_nom_fichier($chaine) {
			//return preg_replace('/[^A-Za-z0-9\.-]/','_',preg_replace('/&/','et',unhtmlentities(remplace_accents($chaine,'all'))));
			return preg_replace("/_$/", "", preg_replace("/_{2,}/", "_", preg_replace('/[^A-Za-z0-9\.\-]/','_',remplace_accents(preg_replace('/&/','et',unhtmlentities($chaine)),'all'))));
		}

		$sql="SELECT * FROM tempo2 LIMIT $largeur_tranche;";
		$res_grp=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_grp)>0) {
			echo "<p><b>Archivage de</b>&nbsp;:<br />\n";
			while($lig_grp=mysqli_fetch_object($res_grp)) {
				$id_groupe=$lig_grp->col1;
				//echo "<p>\$id_groupe=$id_groupe<br />";
				$current_group=get_group($id_groupe);

				// ====================================================
				// Page de l'enseignement n°$id_groupe de l'archive CDT
				// ====================================================

				/*
				$nom_groupe=preg_replace('/&/','et',unhtmlentities(remplace_accents($current_group['name'],'all')));
				$description_groupe=preg_replace('/&/','et',unhtmlentities(remplace_accents($current_group['description'],'all')));
				$classlist_string_groupe=preg_replace('/&/','et',unhtmlentities(remplace_accents($current_group['classlist_string'],'all')));
				*/
				$nom_groupe=corrige_nom_fichier($current_group['name']);
				$description_groupe=corrige_nom_fichier($current_group['description']);
				$classlist_string_groupe=corrige_nom_fichier($current_group['classlist_string']);
				$nom_page_html_groupe=strtr($id_groupe."_".$nom_groupe."_".$description_groupe."_".$classlist_string_groupe.".$extension","/","_");

				//$nom_complet_matiere=preg_replace('/&/','et',unhtmlentities(remplace_accents($current_group['matiere']['nom_complet'],'all')));
				//$nom_enseignement=preg_replace('/&/','et',unhtmlentities(remplace_accents($nom_groupe." (".$description_groupe.")",'all')));
				$nom_complet_matiere=preg_replace("/_$/", "", preg_replace("/_{2,}/", "_", remplace_accents(preg_replace('/&/','et',unhtmlentities($current_group['matiere']['nom_complet'])),'all')));
				$nom_enseignement=preg_replace("/_$/", "", preg_replace("/_{2,}/", "_", remplace_accents(preg_replace('/&/','et',unhtmlentities($nom_groupe." (".$description_groupe.")")),'all')));


				$nom_detaille_groupe=$current_group['name']." (<i>".$current_group['description']." en (".$current_group['classlist_string'].")</i>)";

				$nom_detaille_groupe_non_html=$current_group['name']." (".$current_group['description']." en (".$current_group['classlist_string']."))";

				echo $nom_detaille_groupe."<br />";

				archiver_images_formules_maths($id_groupe);

				$nom_fichier=$nom_page_html_groupe;

				$tab_dates=array();
				$tab_dates2=array();
				$tab_chemin_url=array();
				$tab_notices=array();
				$tab_dev=array();


				$chaine_login_prof="";
				for($loop=0;$loop<count($current_group["profs"]["list"]);$loop++) {
					if($loop>0) {$chaine_login_prof.=", ";}
					$chaine_login_prof.="'".$current_group["profs"]["list"][$loop]."'";
				}

				$content="";
		
				//=====================
				// Le retour doit être différent pour un prof et pour les autres statuts
				$content.='<?php
if($_SESSION["statut"]=="professeur") {
	echo "<div id=\'div_lien_retour\' class=\'noprint\' style=\'float:right; width:6em\'><a href=\'cdt_".$_SESSION["login"].".'.$extension.'\'>Retour</a></div>\n";
}
else {
';
				foreach($current_group['classes']['classes'] as $key => $value) {
					$content.='echo "<div class=\'noprint\' style=\'float:right; width:6em; margin: 3px; text-align:center; border: 1px solid black;\'><a href=\'classe_'.$value["id"].'.'.$extension.'\'>'.$value["classe"].'</a></div>\n";';
				}

				foreach($current_group['profs']['list'] as $key => $login_prof) {
					$content.='echo "<div class=\'noprint\' style=\'float:right; width:10em; margin: 3px; text-align:center; border: 1px solid black;\'><a href=\'cdt_'.$login_prof.'.'.$extension.'\'>'.$current_group['profs']['users'][$login_prof]['civilite'].' '.$current_group['profs']['users'][$login_prof]['nom'].' '.my_strtoupper(mb_substr($current_group['profs']['users'][$login_prof]['prenom'],0,1)).'</a></div>\n";';
				}

				$content.='}
?>
';
				//=====================

				$content.="<h1 style='text-align:center;'>Cahiers de textes (".$gepiSchoolName." - ".$gepiYear.")</h1>\n";
				$content.="<p style='text-align:center;'>Extraction du $display_date_debut au $display_date_fin</p>\n";
				$content.="<h2 style='text-align:center;'>Cahier de textes de ".$nom_detaille_groupe." (<i>$display_date_debut - $display_date_fin</i>)&nbsp;:</h2>\n";
		
				$sql="SELECT cte.* FROM ct_entry cte WHERE (contenu != ''
					AND date_ct != ''
					AND date_ct >= '".$timestamp_debut_export."'
					AND date_ct <= '".$timestamp_fin_export."'
					AND id_groupe='".$id_groupe."'
					) ORDER BY date_ct DESC, heure_entry DESC;";
				//echo "$sql<br />";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				$cpt=0;
				while($lig=mysqli_fetch_object($res)) {
					//echo "$lig->date_ct<br />";
					$date_notice=strftime("%a %d %b %y", $lig->date_ct);
					if(!in_array($date_notice,$tab_dates)) {
						$tab_dates[]=$date_notice;
						$tab_dates2[]=$lig->date_ct;
					}
					$tab_notices[$date_notice][$cpt]['id_ct']=$lig->id_ct;
					$tab_notices[$date_notice][$cpt]['id_login']=$lig->id_login;
					$tab_notices[$date_notice][$cpt]['contenu']=$lig->contenu;
					//echo " <span style='color:red'>\$tab_notices[$date_notice][$cpt]['contenu']=$lig->contenu</span><br />";
					$cpt++;
				}
			
				$sql="SELECT ctd.* FROM ct_devoirs_entry ctd WHERE (contenu != ''
					AND date_ct != ''
					AND date_ct >= '".$timestamp_debut_export."'
					AND date_ct <= '".$timestamp_fin_export."'
					AND id_groupe='".$id_groupe."'
					) ORDER BY date_ct DESC;";
				//echo "$sql<br />";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				$cpt=0;
				while($lig=mysqli_fetch_object($res)) {
					//echo "$lig->date_ct<br />";
					$date_dev=strftime("%a %d %b %y", $lig->date_ct);
					if(!in_array($date_dev,$tab_dates)) {
						$tab_dates[]=$date_dev;
						$tab_dates2[]=$lig->date_ct;
					}
					$tab_dev[$date_dev][$cpt]['id_ct']=$lig->id_ct;
					$tab_dev[$date_dev][$cpt]['id_login']=$lig->id_login;
					$tab_dev[$date_dev][$cpt]['contenu']=$lig->contenu;
					//echo " <span style='color:green'>\$tab_dev[$date_dev][$cpt]['contenu']=$lig->contenu</span><br />";
					$cpt++;
				}
				//echo "\$current_ordre=$current_ordre<br />";
				//sort($tab_dates);
				if($current_ordre=='ASC') {
					array_multisort ($tab_dates, SORT_DESC, SORT_NUMERIC, $tab_dates2, SORT_ASC, SORT_NUMERIC);
				}
				else {
					array_multisort ($tab_dates, SORT_ASC, SORT_NUMERIC, $tab_dates2, SORT_DESC, SORT_NUMERIC);
				}
/*
echo "<pre>";
print_r($tab_notices);
echo "</pre>";

echo "<pre>";
print_r($tab_dev);
echo "</pre>";
*/
				$content.=lignes_cdt($tab_dates, $tab_notices, $tab_dev,$dossier_documents,$mode);

				/*
				echo "<div style='border: 1px solid black;'>\n";
				echo $content;
				echo "</div>\n";
		
				echo "<script type='text/javascript'>
	if(document.getElementById('div_lien_retour_".$id_groupe."')) {
		document.getElementById('div_lien_retour_".$id_groupe."').style.display='none';
	}
</script>\n";
				*/

				$content=html_entete("CDT: ".$nom_detaille_groupe_non_html,1,'y',"$chaine_login_prof").$content;
				$content.=html_pied_de_page();

				//echo "\$dossier_cdt=$dossier_cdt<br />";
				//echo "\$nom_fichier=$nom_fichier<br />";
				$f=fopen($dossier_cdt."/".$nom_fichier,"w+");
				fwrite($f,$content);
				fclose($f);

				foreach($current_group["classes"]["classes"] as $key => $value) {
					// Pour ne créer les liens que pour les cahiers de textes non vides
					if(count($tab_dates)>0) {
						//$sql="INSERT INTO tempo3_cdt SET id_classe='".$value['id']."', classe='".$value['classe']." (".$value['nom_complet'].")"."', matiere='$nom_complet_matiere', enseignement='$nom_enseignement', id_groupe='".$id_groupe."', fichier='$nom_fichier';";
						$sql="INSERT INTO tempo3_cdt SET id_classe='".$value['id']."', classe='".addslashes($value['classe'])." (".addslashes($value['nom_complet']).")"."', matiere='".addslashes($nom_complet_matiere)."', enseignement='".addslashes($nom_enseignement)."', id_groupe='".$id_groupe."', fichier='$nom_fichier';";
						$insert=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$insert) {
							$temoin_erreur="y";
		
							echo "<p style='color:red'>ERREUR lors de l'enregistrement dans 'tempo3_cdt'&nbsp;: $sql</p>\n";
						}
					}
				}

				$sql="DELETE FROM tempo2 WHERE col1='$id_groupe';";
				$menage=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$menage) {
					$temoin_erreur="y";

					echo "<p style='color:red'>ERREUR lors du nettoyage de 'tempo2'&nbsp;: $sql</p>\n";
				}

				// A FAIRE: Ajouter à une liste? pour construire par la suite les pages d'index?

			}

			echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
			echo add_token_field();
			echo "<input type='hidden' name='step' value='2' />\n";
			echo "<input type='hidden' name='mode' value='$mode' />\n";
			echo "<input type='hidden' name='annee' value='$annee' />\n";
			echo "<p><input type='submit' value='Suite' /></p>\n";
			echo "</form>\n";

			if($temoin_erreur!='y') {
				echo "<script type='text/javascript'>
	setTimeout('document.formulaire.submit()',1000);
</script>\n";
			}


		}
		else {
			// Les pages des enseignements n°$id_groupe de l'archive CDT ont été générés à l'étape précedente

			echo "<p>L'archivage des enseignements est réalisé.<br />Les pages d'index vont maintenant être créées.</p>\n";

			// ============================
			// Page racine de l'archive CDT
			// ============================
			//$sql="SELECT * FROM tempo3_cdt ORDER BY classe, matiere;";
			$sql="SELECT DISTINCT id_classe, classe FROM tempo3_cdt ORDER BY classe;";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {

				$content='<div id=\'div_lien_retour\' class=\'noprint\' style=\'float:right; width:6em\'><a href=\'../../../index.'.$extension.'\'>Retour</a></div>';

				$content.="<h1 style='text-align:center;'>Cahiers de textes (".$gepiSchoolName." - ".$gepiYear.")</h1>\n";
				$content.="<p style='text-align:center;'>Extraction du $display_date_debut au $display_date_fin\n";
				$content.="<br />\n";
				$content.="(<i>Archivage effectué le ".strftime("%d/%m/%Y à %H:%M:%S")."</i>)\n";
				$content.="</p>\n";

				$content.="<h2 style='text-align:center;'>Classes&nbsp;:</h2>\n";

				$content.="<div align='center'>\n";
				$content.="<table summary='Tableau des classes'>\n";
				while($lig_class=mysqli_fetch_object($res)) {
					//$content.="Classe de <a href='classe_".$lig_class->id_classe.".$extension'>".$lig_class->classe."</a><br />";
					$content.="<tr><td>Classe de </td><td><a href='classe_".$lig_class->id_classe.".$extension'>".$lig_class->classe."</a></td></tr>\n";
					//$sql="SELECT * FROM tempo3_cdt WHERE classe='$lig_class->classe';";
				}
				$content.="</table>\n";
				$content.="</div>\n";

				$content.="<p><br /></p>\n";

				$content=html_entete("CDT: Index des classes",1,'y').$content;
				$content.=html_pied_de_page();
		
				$f=fopen($dossier_cdt."/index_classes.$extension","w+");
				fwrite($f,$content);
				fclose($f);
			}


			// =======================================
			// Page index des classes de l'archive CDT
			// =======================================
			$sql="SELECT DISTINCT id_classe, classe FROM tempo3_cdt ORDER BY classe;";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				while($lig_class=mysqli_fetch_object($res)) {

					$content='<div id=\'div_lien_retour\' class=\'noprint\' style=\'float:right; width:6em\'><a href=\'index_classes.'.$extension.'\'>Retour</a></div>';

					$content.="<h1 style='text-align:center;'>Cahiers de textes (".$gepiSchoolName." - ".$gepiYear.")</h1>\n";
					$content.="<p style='text-align:center;'>Extraction du $display_date_debut au $display_date_fin\n";
					$content.="<br />\n";
					$content.="(<i>Archivage effectué le ".strftime("%d/%m/%Y à %H:%M:%S")."</i>)\n";
					$content.="</p>\n";
	
					$content.="<h2 style='text-align:center;'>Classe de $lig_class->classe&nbsp;:</h2>\n";

					$sql="SELECT * FROM tempo3_cdt WHERE classe='".addslashes($lig_class->classe)."';";
					//echo "$sql<br />";
					$res2=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res2)>0) {
						$content.="<div align='center'>\n";
						$content.="<table summary='Tableau des enseignements'>\n";
						while($lig_mat=mysqli_fetch_object($res2)) {
							//$content.="<b>$lig_mat->matiere</b>&nbsp;:<a href='$lig_mat->fichier'> $lig_mat->enseignement</a><br />";

							$sql="SELECT DISTINCT u.* FROM utilisateurs u, j_groupes_professeurs jgp, tempo3_cdt t WHERE t.id_groupe=jgp.id_groupe AND u.login=jgp.login AND t.fichier='$lig_mat->fichier';";
							$res3=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res3)>0) {
								$liste_profs="";
								while($lig_prof=mysqli_fetch_object($res3)) {
									if($liste_profs!="") {$liste_profs.=", ";}
									$liste_profs.=$lig_prof->civilite." ".my_strtoupper($lig_prof->nom)." ".casse_mot($lig_prof->prenom, 'majf2');
								}
							}

							$content.="<tr><td><b>$lig_mat->matiere</b>&nbsp;:</td><td><a href='$lig_mat->fichier'> $lig_mat->enseignement</a></td><td>$liste_profs</td></tr>\n";
						}
						$content.="</table>\n";
						$content.="</div>\n";
					}

					$content=html_entete("CDT: Classe de ".$lig_class->classe,1,'y').$content;
					$content.=html_pied_de_page();
			
					$f=fopen($dossier_cdt."/classe_".$lig_class->id_classe.".$extension","w+");
					fwrite($f,$content);
					fclose($f);
				}
			}

			// ===========================================
			// Page index des professeurs de l'archive CDT
			// ===========================================
			$sql="SELECT DISTINCT u.* FROM tempo3_cdt t, j_groupes_professeurs jgp, utilisateurs u WHERE jgp.id_groupe=t.id_groupe AND jgp.login=u.login ORDER BY u.nom, u.prenom;";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				$content='<div id=\'div_lien_retour\' class=\'noprint\' style=\'float:right; width:6em\'><a href=\'index.'.$extension.'\'>Retour</a></div>';

				$content.="<h1 style='text-align:center;'>Cahiers de textes (".$gepiSchoolName." - ".$gepiYear.")</h1>\n";
				$content.="<p style='text-align:center;'>Extraction du $display_date_debut au $display_date_fin\n";
				$content.="<br />\n";
				$content.="(<i>Archivage effectué le ".strftime("%d/%m/%Y à %H:%M:%S")."</i>)\n";
				$content.="</p>\n";

				$content.="<h2 style='text-align:center;'>Professeurs&nbsp;:</h2>\n";

				$content.="<div align='center'>\n";
				while($lig_prof=mysqli_fetch_object($res)) {
					$content.="<a href='cdt_".$lig_prof->login.".$extension'> $lig_prof->civilite ".my_strtoupper($lig_prof->nom)." ".casse_mot($lig_prof->prenom, 'majf2')."</a><br />";

					$sql="SELECT * FROM tempo3_cdt t, j_groupes_professeurs jgp WHERE jgp.id_groupe=t.id_groupe AND jgp.login='$lig_prof->login' ORDER BY classe, matiere;";
					$res2=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res2)>0) {
						// ================================================================================================
						// Page index des enseignements du professeur courant ((essoufflé) dans la boucle) de l'archive CDT
						// ================================================================================================
						//$content2='<div id=\'div_lien_retour\' class=\'noprint\' style=\'float:right; width:6em\'><a href=\'index_professeurs.'.$extension.'\'>Retour</a></div>';
						$content2='<div id=\'div_lien_retour\' class=\'noprint\' style=\'float:right; width:6em\'><a href=\'';
						$content2.='<?php'."\n";
						//$content2.='if($_SESSION["statut"]=="professeur") {echo "CDT_".$_SESSION["login"];} else {echo "index_professeurs";}'."\n";
						$content2.='if($_SESSION["statut"]=="professeur") {echo "../../../index";} else {echo "index_professeurs";}'."\n";
						$content2.='?>';
						$content2.='.';
						$content2.=$extension.'\'>Retour</a></div>';

						$content2.="<h1 style='text-align:center;'>Cahiers de textes (".$gepiSchoolName." - ".$gepiYear.")</h1>\n";
						$content2.="<p style='text-align:center;'>Extraction du $display_date_debut au $display_date_fin\n";
						$content2.="<br />\n";
						$content2.="(<i>Archivage effectué le ".strftime("%d/%m/%Y à %H:%M:%S")."</i>)\n";
						$content2.="</p>\n";
		
						$content2.="<h2 style='text-align:center;'>Professeur&nbsp;: $lig_prof->civilite ".my_strtoupper($lig_prof->nom)." ".casse_mot($lig_prof->prenom, 'majf2')."</h2>\n";

						$content2.="<div align='center'>\n";
						$content2.="<table border='0' summary='Tableau des enseignements de $lig_prof->civilite ".my_strtoupper($lig_prof->nom)." ".casse_mot($lig_prof->prenom, 'majf2')."'>\n";
						$classe_prec="";
						$cpt=0;
						while($lig_clas_mat=mysqli_fetch_object($res2)) {
							if($lig_clas_mat->classe!=$classe_prec) {
								if($classe_prec!="") {
									$content2.="</td>\n";
									$content2.="</tr>\n";
								}

								$classe_prec=$lig_clas_mat->classe;

								$content2.="<tr>\n";
								$content2.="<td style='vertical-align:top;'>$lig_clas_mat->classe</td>\n";
								$content2.="<td>\n";

							}
							$content2.="<b>$lig_clas_mat->matiere</b>&nbsp;:<a href='$lig_clas_mat->fichier'> $lig_clas_mat->enseignement</a><br />";

							$cpt++;
						}
						if($cpt>0) {
							$content2.="</td>\n";
							$content2.="</tr>\n";
						}
						$content2.="</table>\n";
						$content2.="</div>\n";

						$content2=html_entete("CDT: Professeur ".$lig_prof->civilite." ".my_strtoupper($lig_prof->nom)." ".casse_mot($lig_prof->prenom, 'majf2'),1,'y',"'$lig_prof->login'").$content2;
						$content2.=html_pied_de_page();
				
						$f=fopen($dossier_cdt."/cdt_".$lig_prof->login.".$extension","w+");
						fwrite($f,$content2);
						fclose($f);
					}

				}
				$content.="</div>\n";

				$content.="<p><br /></p>\n";

				$content=html_entete("CDT: Liste des professeurs",1,'y').$content;
				$content.=html_pied_de_page();
		
				$f=fopen($dossier_cdt."/index_professeurs.$extension","w+");
				fwrite($f,$content);
				fclose($f);
			}


			// ==========================================================
			// Page de choix Index_classe ou Index_profs de l'archive CDT
			// ==========================================================
			// Faire en dessous une page qui parcourt les sous-dossiers d'années
			$content='<div id=\'div_lien_retour\' class=\'noprint\' style=\'float:right; width:6em\'><a href=\'../../../index.'.$extension.'\'>Retour</a></div>';

			$content.="<h1 style='text-align:center;'>Cahiers de textes (".$gepiSchoolName." - ".$gepiYear.")</h1>\n";
			$content.="<p style='text-align:center;'>Extraction du $display_date_debut au $display_date_fin\n";
			$content.="<br />\n";
			$content.="(<i>Archivage effectué le ".strftime("%d/%m/%Y à %H:%M:%S")."</i>)\n";
			$content.="</p>\n";

			$content.="<div align='center'>\n";

			$content.="<p><a href='index_classes.".$extension."'>Index des classes</a></p>\n";
			$content.="<p><a href='index_professeurs.".$extension."'>Index des professeurs</a></p>\n";
			$content.="</div>\n";

			$content=html_entete("CDT: Index",1,'y').$content;
			$content.=html_pied_de_page();
	
			$f=fopen($dossier_cdt."/index.$extension","w+");
			fwrite($f,$content);
			fclose($f);

			echo "<p>Terminé.<br />Les pages d'index ont maintenant été créées.</p>\n";

		}
	}
}
echo "<p><br /></p>\n";

// Evaluer le nom du dossier établissement selon le cas multisite ou non.<br />
// Calculer l'année à archiver selon la date courante ou d'après le paramétrage 'gepiYear'... ou proposer de saisir un autre nom d'année.<br /><br />
//Ajouter les liens dans le cahier de textes des profs... et scol? cpe?<br /><br />
echo "<p style='color:red'>A FAIRE: Ne pas proposer le lien vers les années archivées si aucune année n'est archivée pour l'utilisateur courant (variable selon qu'on est prof ou pas)</p>\n";

require("../lib/footer.inc.php");
die();

?>
