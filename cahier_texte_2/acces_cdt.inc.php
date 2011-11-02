<?php
/*
* @version: $Id: acces_cdt.inc.php 6844 2011-04-28 16:39:42Z crob $
*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Gabriel Fischer
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
//$accessibilite="y";

//https://127.0.0.1/steph/gepi-trunk/documents/acces_cdt_BIDON/acces_cdt.php
$niveau_arbo=2;
$prefixe_arbo_acces_cdt="../..";

// Initialisations files
//require_once($prefixe_arbo_acces_cdt."/lib/initialisations.inc.php");
//require_once($prefixe_arbo_acces_cdt."/lib/transform_functions.php");

/*
// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

$sql="SELECT 1=1 FROM droits WHERE id='/cahier_texte_2/export_cdt.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/cahier_texte_2/export_cdt.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Export de CDT',
statut='';";
$insert=mysql_query($sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
*/

//On vérifie si le module est activé
if (getSettingValue("active_cahiers_texte")!='y') {
	die("Le module n'est pas activé.");
}

/*
//$display_date_debut=isset($_POST['display_date_debut']) ? $_POST['display_date_debut'] : NULL;
//$display_date_fin=isset($_POST['display_date_fin']) ? $_POST['display_date_fin'] : NULL;

//=======================
//Configuration du calendrier
include($prefixe_arbo_acces_cdt."/lib/calendrier/calendrier.class.php");
//$cal1 = new Calendrier("form_choix_edit", "display_date_debut");
//$cal2 = new Calendrier("form_choix_edit", "display_date_fin");
$cal1 = new Calendrier("formulaire", "display_date_debut");
$cal2 = new Calendrier("formulaire", "display_date_fin");
$cal3 = new Calendrier("formulaire", "date2_acces");
//=======================

//=======================
// Pour éviter de refaire le choix des dates en revenant ici, on utilise la SESSION...
$annee = strftime("%Y");
$mois = strftime("%m");
$jour = strftime("%d");
$heure = strftime("%H");
$minute = strftime("%M");

if($mois>7) {$date_debut_tmp="01/09/$annee";} else {$date_debut_tmp="01/09/".($annee-1);}

$action=isset($_POST['action']) ? $_POST['action'] : "export_zip";
*/

require($prefixe_arbo_acces_cdt."/cahier_texte_2/cdt_lib.php");

//**************** EN-TETE *****************
$titre_page = "Cahier de textes - Accès";
//require_once($prefixe_arbo_acces_cdt."/lib/header.inc");
$n_arbo=2;
echo html_entete('Cahier de textes',$n_arbo,'n',$login_prof);
//**************** FIN EN-TETE *************

//debug_var();

//==============================
// Le choix des groupes est fait
//==============================

// Préparation de l'arborescence

$gepiSchoolName=getSettingValue('gepiSchoolName');
$gepiYear=getSettingValue('gepiYear');

/*
$dirname=get_user_temp_directory();
if(!$dirname) {
	echo "<p style='color:red;'>Problème avec le dossier temporaire.</p>\n";
	require($prefixe_arbo_acces_cdt."/lib/footer.inc.php");
	die();
}

if($_SESSION['statut']=='professeur') {
	echo "</p>\n";
}
else {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Retour au choix des classes ou du professeur</a>";
	echo "</p>\n";
}
*/

//echo "<div id='div_archive_zip'></div>\n";
//echo "<p class='bold'>Affichage des cahiers de textes extraits</p>\n";

// Récupérer le max de getSettingValue("begin_bookings") et $display_date_debut
$tmp_tab=explode("/",$display_date_debut);
$jour=$tmp_tab[0];
$mois=$tmp_tab[1];
$annee=$tmp_tab[2];
$date_debut_tmp=mktime(0,0,0,$mois,$jour,$annee);
$timestamp_debut_export=max(getSettingValue("begin_bookings"),$date_debut_tmp);

// Récupérer le min de getSettingValue("end_bookings") et $display_date_fin
$tmp_tab=explode("/",$display_date_fin);
$jour=$tmp_tab[0];
$mois=$tmp_tab[1];
$annee=$tmp_tab[2];
$date_fin_tmp=mktime(0,0,0,$mois,$jour,$annee);
$timestamp_fin_export=max(getSettingValue("end_bookings"),$date_fin_tmp);

// Permettre de choisir l'ordre dans lequel exporter?
$current_ordre='ASC';

//echo "\$action=$action<br />";

/*
if($action=='acces') {
	$length = rand(35, 45);
	for($len=$length,$r='';strlen($r)<$len;$r.=chr(!mt_rand(0,2)? mt_rand(48,57):(!mt_rand(0,1) ? mt_rand(65,90) : mt_rand(97,122))));
	$dirname = "acces_cdt_".$r;
	$create = mkdir("../documents/".$dirname, 0700);
	if(!$create) {
		echo "<p style='color:red;'>Problème avec le dossier temporaire../documents/".$dirname."</p>\n";
		require($prefixe_arbo_acces_cdt."/lib/footer.inc.php");
		die();
	}

	// Enregistrement dans la base de cet accès ouvert
	// Il faut y stocker la liste des login profs concernés pour afficher en page d'accueil la présence d'un cdt ouvert en consultation
	$date1_acces="$annee-$mois-$jour $heure:$minute:00";
	$date2_acces=isset($_POST['date2_acces']) ? $_POST['date2_acces'] : "";

	if($date2_acces=='') {
		$date2_acces=$date1_acces;
	}
	else {
		$tab_tmp_date=explode('/',$date2_acces);
		$date2_acces=$tab_tmp_date[2]."-".$tab_tmp_date[1]."-".$tab_tmp_date[0]." $heure:$minute:00";
	}

	$description_acces=isset($_POST['description_acces']) ? $_POST['description_acces'] : "Test";
}
*/
//echo "\$dirname=$dirname<br />";


	$chaine_info_prof="";
	if(isset($login_prof)) {
		$chaine_info_prof=" de ".civ_nom_prenom($login_prof)." ";
	}
	else {
		$login_prof=$_SESSION['login'];
	}

	// Préparation de l'arborescence
	//$nom_export="export_cdt_".$login_prof."_".strftime("%Y%m%d_%H%M%S");
/*
	if($action=='acces') {
		$chemin_acces="documents/".$dirname."/".$nom_export."/index.html";
		$res=enregistrement_creation_acces_cdt($chemin_acces, $description_acces, $date1_acces, $date2_acces, $id_groupe);
		if(!$res) {
			echo "<p style='color:red;'>Erreur lors de l'enregistrement de la mise en place de l'accès.</p>\n";
			require($prefixe_arbo_acces_cdt."/lib/footer.inc.php");
			die();
		}
	}
*/

	//arbo_export_cdt($nom_export, $dirname);

	$chaine_id_groupe="";

	// Générer la page d'index
	$html="";
	//$html.=html_entete();
	$html.="<h1 style='text-align:center;'>Cahiers de textes $chaine_info_prof<br />(".$gepiSchoolName." - ".$gepiYear.")</h1>\n";
	$html.="<p>Cahier de textes (<i>$display_date_debut - $display_date_fin</i>) de&nbsp;:</p>\n";
	$html.="<ul>\n";
	for($i=0;$i<count($id_groupe);$i++) {
	
		// ===================================================
		// ===================================================
		// A FAIRE
		// VERIFIER QUE LE PROFESSEUR EST ASSOCIE A CE GROUPE
		// ===================================================
		// ===================================================
	
		$tab_champs=array('classes');
		$current_group=get_group($id_groupe[$i],$tab_champs);
		//$current_group=get_group($id_groupe[$i]);
	
		if($i>0) {
			$chaine_id_groupe.=", ";
		}
		$chaine_id_groupe.="'".$id_groupe[$i]."'";
	
		$nom_groupe=preg_replace("/[^A-Za-z0-9]/","_",remplace_accents($current_group['name'],'all'));
		$description_groupe=preg_replace("/[^A-Za-z0-9]/","_",remplace_accents($current_group['description'],'all'));
		$classlist_string_groupe=preg_replace("/[^A-Za-z0-9]/","_",remplace_accents($current_group['classlist_string'],'all'));
		$nom_page_html_groupe=$id_groupe[$i]."_".$nom_groupe."_"."$description_groupe"."_".$classlist_string_groupe.".html";
	
		$nom_fichier[$id_groupe[$i]]=$nom_page_html_groupe;
		$nom_detaille_groupe[$id_groupe[$i]]=$current_group['name']." (<i>".$current_group['description']." en (".$current_group['classlist_string'].")</i>)";
	
		$nom_detaille_groupe_non_html[$id_groupe[$i]]=$current_group['name']." (".$current_group['description']." en (".$current_group['classlist_string']."))";
	
		$html.="<li><a id='lien_id_groupe_$id_groupe[$i]' href='cahier_texte/$nom_page_html_groupe'>".$current_group['name']." (<i>".$current_group['description']." en (".$current_group['classlist_string'].")</i>)</a></li>\n";
	}
	$html.="</ul>\n";
	//$html.=html_pied_de_page();
	
	//================================================================
	// Affichage dans la page d'export de ce qui va être fourni en zip
	echo "<a name='affichage_page_index'></a>";
	echo "<div style='border: 1px solid black;'>\n";
	echo $html;
	echo "</div>\n";

	// Précaution
	$chaine_id_groupe=preg_replace("/^,/","",$chaine_id_groupe);

	// Correctif des liens tels qu'affichés dans la page
	echo "<script type='text/javascript'>
		tab_id_groupe=new Array($chaine_id_groupe);
		for(i=0;i<tab_id_groupe.length;i++) {
			if(document.getElementById('lien_id_groupe_'+tab_id_groupe[i])) {
				document.getElementById('lien_id_groupe_'+tab_id_groupe[i]).href='#cible_lien_id_groupe_'+tab_id_groupe[i];
			}
		}
	</script>\n";
	//================================================================
	
	$html=html_entete("Index des cahiers de textes",0).$html;
	$html.=html_pied_de_page();

/*
	$f=fopen($dossier_export."/index.html","w+");
	fwrite($f,$html);
	fclose($f);
	
	$tab_fichiers_a_zipper[]=$dossier_export."/index.html";
*/
//require($prefixe_arbo_acces_cdt."/lib/footer.inc.php");
//die();

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

echo "<hr width='200px' />\n";

// Dans la page générée, permettre de masquer via JavaScript telle ou telle catégorie Notices ou devoirs,...
for($i=0;$i<count($id_groupe);$i++) {
	//echo "\$id_groupe[$i]=$id_groupe[$i]<br />";

	unset($chaine_cpt_classe);

	$tab_dates=array();
	$tab_dates2=array();
	$tab_chemin_url=array();

	$tab_notices=array();
	$tab_dev=array();

	$html="";
	//$html.=html_entete();

		// On a choisi un professeur
		$html.="<div id='div_lien_retour_".$id_groupe[$i]."' class='noprint' style='float:right; width:6em'><a id='lien_retour_".$id_groupe[$i]."' href='../index.html'>Retour</a></div>\n";

	$html.="<a name='cible_lien_id_groupe_".$id_groupe[$i]."'></a>\n";

	$html.="<h1 style='text-align:center;'>Cahiers de textes (".$gepiSchoolName." - ".$gepiYear.")</h1>\n";
	$html.="<p style='text-align:center;'>Extraction du $display_date_debut au $display_date_fin</p>\n";
	$html.="<h2 style='text-align:center;'>Cahier de textes de ".$nom_detaille_groupe[$id_groupe[$i]]." (<i>$display_date_debut - $display_date_fin</i>)&nbsp;:</h2>\n";

	$sql="SELECT cte.* FROM ct_entry cte WHERE (contenu != ''
		AND date_ct != ''
		AND date_ct >= '".$timestamp_debut_export."'
		AND date_ct <= '".$timestamp_fin_export."'
		AND id_groupe='".$id_groupe[$i]."'
		) ORDER BY date_ct DESC, heure_entry DESC;";
	//echo "$sql<br />";
	$res=mysql_query($sql);
	$cpt=0;
	while($lig=mysql_fetch_object($res)) {

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
		AND id_groupe='".$id_groupe[$i]."'
		) ORDER BY date_ct DESC;";
	//echo "$sql<br />";
	$res=mysql_query($sql);
	$cpt=0;
	while($lig=mysql_fetch_object($res)) {
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

	$html.=lignes_cdt($tab_dates, $tab_notices, $tab_dev);

	//================================================================
	echo "<div style='border: 1px solid black;'>\n";
	echo $html;
	echo "</div>\n";

	echo "<script type='text/javascript'>
	if(document.getElementById('div_lien_retour_".$id_groupe[$i]."')) {
		//document.getElementById('div_lien_retour_".$id_groupe[$i]."').style.display='none';
		if(document.getElementById('lien_retour_".$id_groupe[$i]."')) {
			document.getElementById('lien_retour_".$id_groupe[$i]."').href='#affichage_page_index';
		}
";
	if(isset($chaine_cpt_classe)) {
		echo "
		tab_cpt_classe=new Array($chaine_cpt_classe);
		for(i=0;i<tab_cpt_classe.length;i++) {
			if(document.getElementById('lien_retour_".$id_groupe[$i]."_'+tab_cpt_classe[i])) {
				document.getElementById('lien_retour_".$id_groupe[$i]."_'+tab_cpt_classe[i]).href='#affichage_page_index';
			}
		}
";
	}
	echo "
	}
</script>\n";
	//================================================================

	$html=html_entete("CDT: ".$nom_detaille_groupe_non_html[$id_groupe[$i]],1).$html;
	$html.=html_pied_de_page();
/*
	$f=fopen($dossier_export."/cahier_texte/".$nom_fichier[$id_groupe[$i]],"w+");
	fwrite($f,$html);
	fclose($f);

	$tab_fichiers_a_zipper[]=$dossier_export."/cahier_texte/".$nom_fichier[$id_groupe[$i]];

	if(count($tab_chemin_url)) {
		$fichier_url=$dossier_export."/url_documents.txt";
		$f=fopen($fichier_url,"a+");
		for($k=0;$k<count($tab_chemin_url);$k++) {
			fwrite($f,$tab_chemin_url[$k]."\n");
		}
		fclose($f);

		$tab_fichiers_a_zipper[]=$fichier_url;
	}
*/
	echo "<hr width='200px' />\n";
}


// Générer des fichiers URL_documents.txt (URL seule), URL_documents.csv (chemin;URL), script bash/batch/auto-it pour télécharger en créant/parcourant l'arborescence des documents
/*
if(isset($_SERVER['HTTP_REFERER'])) {
	$tmp=explode("?",$_SERVER['HTTP_REFERER']);
	//$chemin_site=my_ereg_replace("/cahier_texte_2/export_cdt.php$","",$tmp[0]);
	$chemin_site=preg_replace("#/cahier_texte_2#","",dirname($tmp[0]));

	$fichier_url_site=$dossier_export."/url_site.txt";
	$f=fopen($fichier_url_site,"a+");
	fwrite($f,$chemin_site."\n");
	fclose($f);

	$tab_fichiers_a_zipper[]=$fichier_url_site;
}

if($action=='export_zip') {
	require_once("../lib/pclzip.lib.php");
	
	$fichier_archive="../temp/$dirname/".$nom_export.".zip";
	$archive = new PclZip($fichier_archive);
	$v_list = $archive->create($tab_fichiers_a_zipper,"","../temp/$dirname/");
	if($v_list==0) {
		echo "<p>Cahiers de textes extraits&nbsp;: <a href='$dossier_export'>$dossier_export</a></p>\n";
	
		echo "<p style='color:red;'>ERREUR lors de la création de l'archive&nbsp;:<br />";
		echo $archive->errorInfo(true);
		echo "</p>\n";
	}
	else {
		$basename_fichier_archive=basename($fichier_archive);
		echo "<p class='bold'>Archive des cahiers de textes extraits&nbsp;: <a href='$fichier_archive'>$basename_fichier_archive</a></p>\n";
	
		echo "<script type='text/javascript'>
	if(document.getElementById('div_archive_zip')) {
		document.getElementById('div_archive_zip').innerHTML=\"<p class='bold'>Archive des cahiers de textes extraits&nbsp;: <a href='$fichier_archive'>$basename_fichier_archive</a></p>\"
	}
</script>\n";
	}

	// On fait le ménage
	for($i=0;$i<count($tab_fichiers_a_zipper);$i++) {
		//echo "unlink($tab_fichiers_a_zipper[$i]);<br />";
		unlink($tab_fichiers_a_zipper[$i]);
	}
	
	rmdir($dossier_export."/cahier_texte");
	rmdir($dossier_export."/css");
	rmdir($dossier_export);
}
elseif($action=='acces') {

	$chaine_info_texte="<br /><p><b>Information&nbsp;:</b><br />Le(s) cahier(s) de textes extrait(s) est(sont) accessible(s) sans authentification à l'adresse suivante&nbsp;:<br /><a href='$dossier_export/index.html' target='_blank'>$dossier_export/index.html</a><br />Consultez la page, copiez l'adresse en barre d'adresse et transmettez la à qui vous souhaitez.<br />N'oubliez pas de supprimer cet accès lorsqu'il ne sera plus utile.<br />&nbsp;</p>";

	echo $chaine_info_texte;

	echo "<script type='text/javascript'>

	if(document.getElementById('div_archive_zip')) {
		document.getElementById('div_archive_zip').innerHTML=\"$chaine_info_texte\";
	}

	//url=document.location;
	//alert(url);
	//var reg = new RegExp('cahier_texte_2/export_cdt.*','');
	//alert(document.location.replace(reg,''));
</script>\n";

}
*/


/*
require('../fpdf/fpdf.php');
require('../fpdf/ex_fpdf.php');

define('FPDF_FONTPATH','../fpdf/font/');
define('LargeurPage','210');
define('HauteurPage','297');

require_once("../impression/class_pdf.php");
require_once ("../impression/liste.inc.php");

$marge_haut = 10 ;
$marge_droite = 10 ;
$marge_gauche = 10 ;
$marge_bas = 10 ;
$marge_reliure = 1 ;
$avec_emplacement_trous = 1 ;

if ($marge_reliure==1) {
  if ($marge_gauche < 18) {$marge_gauche = 18;}
}


//Calcul de la Zone disponible
$EspaceX = LargeurPage - $marge_droite - $marge_gauche ;
$EspaceY = HauteurPage - $marge_haut - $marge_bas;

$X_tableau = $marge_gauche;


//entête classe et année scolaire
$L_entete_classe = 65;
$H_entete_classe = 14;
$X_entete_classe = $EspaceX - $L_entete_classe + $marge_gauche;
$Y_entete_classe = $marge_haut;

$X_entete_matiere = $marge_gauche;
$Y_entete_matiere = $marge_haut;
$L_entete_discipline = 65;
$H_entete_discipline = 14;

$pdf=new rel_PDF("P","mm","A4");
$pdf->SetTopMargin($marge_haut);
$pdf->SetRightMargin($marge_droite);
$pdf->SetLeftMargin($marge_gauche);
$pdf->SetAutoPageBreak(true, $marge_bas);

$pdf->AddPage("P"); //ajout d'une page au document
$pdf->SetDrawColor(0,0,0);
$pdf->SetFont('Arial');
$pdf->SetXY(20,20);
$pdf->SetFontSize(14);
$pdf->Cell(90,7, "TEST",0,2,'');

$pdf->SetXY(20,40);
$pdf->SetFontSize(10);
$pdf->Cell(150,7, "Blablabla.",0,2,'');

$nom_releve=date("Ymd_Hi");
$nom_releve = 'Test'.'.pdf';
//send_file_download_headers('application/pdf',$nom_releve);
$pdf->Output("../temp/".get_user_temp_directory()."/".$nom_releve,'F');

echo "<p><a href='../temp/".get_user_temp_directory()."/".$nom_releve."'>$nom_releve</a></p>";
//die();
*/

echo "<p><br /></p>\n";
require($prefixe_arbo_acces_cdt."/lib/footer.inc.php");
?>
