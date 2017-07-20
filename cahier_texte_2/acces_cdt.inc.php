<?php
/*
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
$action="acces_dynamique";


//On vérifie si le module est activé
if (!acces_cdt()) {
	die("Le module n'est pas activé.");
}


require($prefixe_arbo_acces_cdt."/cahier_texte_2/cdt_lib.php");

//**************** EN-TETE *****************
$titre_page = "Cahier de textes - Accès";

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



	$chaine_info_prof="";
	if(isset($login_prof)) {
		$chaine_info_prof=" de ".civ_nom_prenom($login_prof)." ";
	}
	else {
		$login_prof=$_SESSION['login'];
	}

	// Préparation de l'arborescence
	//$nom_export="export_cdt_".$login_prof."_".strftime("%Y%m%d_%H%M%S");


	//arbo_export_cdt($nom_export, $dirname);

	$chaine_id_groupe="";

	// Générer la page d'index
	$html="";
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


//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

echo "<hr width='200px' />\n";

// Dans la page générée, permettre de masquer via JavaScript telle ou telle catégorie Notices ou devoirs,...
for($i=0;$i<count($id_groupe);$i++) {
	
	unset($chaine_cpt_classe);

	$tab_dates=array();
	$tab_dates2=array();
	$tab_chemin_url=array();

	$tab_notices=array();
	$tab_dev=array();

	$html="";
	
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
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	$cpt=0;
	while($lig=mysqli_fetch_object($res)) {

		$date_notice=strftime("%a %d %b %y", $lig->date_ct);
		if(!in_array($date_notice,$tab_dates)) {
			$tab_dates[]=$date_notice;
			$tab_dates2[]=$lig->date_ct;
		}
		$tab_notices[$date_notice][$cpt]['id_ct']=$lig->id_ct;
		$tab_notices[$date_notice][$cpt]['id_login']=$lig->id_login;
		$tab_notices[$date_notice][$cpt]['contenu']=$lig->contenu;
		$cpt++;

	}

	$sql="SELECT ctd.* FROM ct_devoirs_entry ctd WHERE (contenu != ''
		AND date_ct != ''
		AND date_ct >= '".$timestamp_debut_export."'
		AND date_ct <= '".$timestamp_fin_export."'
		AND id_groupe='".$id_groupe[$i]."'
		) ORDER BY date_ct DESC;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	$cpt=0;
	while($lig=mysqli_fetch_object($res)) {
		$date_dev=strftime("%a %d %b %y", $lig->date_ct);
		if(!in_array($date_dev,$tab_dates)) {
			$tab_dates[]=$date_dev;
			$tab_dates2[]=$lig->date_ct;
		}
		$tab_dev[$date_dev][$cpt]['id_ct']=$lig->id_ct;
		$tab_dev[$date_dev][$cpt]['id_login']=$lig->id_login;
		$tab_dev[$date_dev][$cpt]['contenu']=$lig->contenu;
		$cpt++;
	}
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

	echo "<hr width='200px' />\n";
}

echo "<p><br /></p>\n";
require($prefixe_arbo_acces_cdt."/lib/footer.inc.php");
?>
