<?php
/*
* @version: $Id$
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
$accessibilite="y";

// Initialisations files
require_once("../lib/initialisations.inc.php");
require_once("../lib/transform_functions.php");

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

//On vérifie si le module est activé
if (getSettingValue("active_cahiers_texte")!='y') {
	die("Le module n'est pas activé.");
}

//$display_date_debut=isset($_POST['display_date_debut']) ? $_POST['display_date_debut'] : NULL;
//$display_date_fin=isset($_POST['display_date_fin']) ? $_POST['display_date_fin'] : NULL;

//=======================
//Configuration du calendrier
include("../lib/calendrier/calendrier.class.php");
//$cal1 = new Calendrier("form_choix_edit", "display_date_debut");
//$cal2 = new Calendrier("form_choix_edit", "display_date_fin");
$cal1 = new Calendrier("formulaire", "display_date_debut");
$cal2 = new Calendrier("formulaire", "display_date_fin");
//=======================

//=======================
// Pour éviter de refaire le choix des dates en revenant ici, on utilise la SESSION...
$annee = strftime("%Y");
$mois = strftime("%m");
$jour = strftime("%d");

if($mois>7) {$date_debut_tmp="01/09/$annee";} else {$date_debut_tmp="01/09/".($annee-1);}

//$display_date_debut=isset($_POST['display_date_debut']) ? $_POST['display_date_debut'] : (isset($_SESSION['display_date_debut']) ? $_SESSION['display_date_debut'] : $jour."/".$mois."/".$annee);
$display_date_debut=isset($_POST['display_date_debut']) ? $_POST['display_date_debut'] : (isset($_SESSION['display_date_debut']) ? $_SESSION['display_date_debut'] : $date_debut_tmp);

$display_date_fin=isset($_POST['display_date_fin']) ? $_POST['display_date_fin'] : (isset($_SESSION['display_date_fin']) ? $_SESSION['display_date_fin'] : $jour."/".$mois."/".$annee);
//=======================

//$id_groupe=isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);
$id_groupe=isset($_POST['id_groupe']) ? $_POST['id_groupe'] : NULL;

if(isset($_GET['id_groupe'])) {
	$id_groupe=array();
	$id_groupe[0]=$_GET['id_groupe'];
}

$tab_fichiers_a_zipper=array();

//**************** EN-TETE *****************
$titre_page = "Cahier de textes - Export";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *************

//debug_var();

echo "<p class='bold'><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

// Création d'un espace entre le bandeau et le reste 
//echo "<p></p>\n";

//On vérifie si le module est activé
if (getSettingValue("active_cahiers_texte")!='y') {
	echo "</p>\n";

	echo "<p class='grand centre_texte'>Le cahier de textes n'est pas accessible pour le moment.</p>\n";
	require("../lib/footer.inc.php");
	die();
}

// Pour les non-professeurs, on choisit d'abord les classes
if($_SESSION['statut']!='professeur') {
	if(!isset($id_classe)) {
		echo "</p>\n";
	
		echo "<p class='bold'>Choix des classes&nbsp;:</p>\n";
	
		// Liste des classes avec élève:
		$sql="SELECT DISTINCT c.* FROM j_eleves_classes jec, classes c WHERE (c.id=jec.id_classe) ORDER BY c.classe;";
		$call_classes=mysql_query($sql);
	
		$nb_classes=mysql_num_rows($call_classes);
		if($nb_classes==0){
			echo "<p>Aucune classe avec élève affecté n'a été trouvée.</p>\n";
			require("../lib/footer.inc.php");
			die();
		}
	
		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
		// Affichage sur 3 colonnes
		$nb_classes_par_colonne=round($nb_classes/3);
	
		echo "<table width='100%' summary='Choix des classes'>\n";
		echo "<tr valign='top' align='center'>\n";
	
		$cpt = 0;
	
		echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
		echo "<td align='left'>\n";
	
		while($lig_clas=mysql_fetch_object($call_classes)) {
	
			//affichage 2 colonnes
			if(($cpt>0)&&(round($cpt/$nb_classes_par_colonne)==$cpt/$nb_classes_par_colonne)){
				echo "</td>\n";
				echo "<td align='left'>\n";
			}
	
			echo "<label id='label_tab_id_classe_$cpt' for='tab_id_classe_$cpt' style='cursor: pointer;'><input type='checkbox' name='id_classe[]' id='tab_id_classe_$cpt' value='$lig_clas->id' onchange='change_style_classe($cpt)' /> $lig_clas->classe</label>";
			echo "<br />\n";
			$cpt++;
		}
	
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
	
		echo "<p><a href='#' onClick='ModifCase(true)'>Tout cocher</a> / <a href='#' onClick='ModifCase(false)'>Tout décocher</a></p>\n";
	
		echo "<p><input type='submit' value='Valider' /></p>\n";
		echo "</form>\n";
	
		echo "<script type='text/javascript'>
	function ModifCase(mode) {
		for (var k=0;k<$cpt;k++) {
			if(document.getElementById('tab_id_classe_'+k)){
				document.getElementById('tab_id_classe_'+k).checked = mode;
				change_style_classe(k);
			}
		}
	}

	function change_style_classe(num) {
		if(document.getElementById('tab_id_classe_'+num)) {
			if(document.getElementById('tab_id_classe_'+num).checked) {
				document.getElementById('label_tab_id_classe_'+num).style.fontWeight='bold';
			}
			else {
				document.getElementById('label_tab_id_classe_'+num).style.fontWeight='normal';
			}
		}
	}

	</script>\n";

		require("../lib/footer.inc.php");
		die();
	}
}

//===============================================================
if($_SESSION['statut']=='professeur') {
	echo "</p>\n";

	echo "<p class='bold'>Choix des matières/enseignements&nbsp;:</p>\n";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";

	$cpt=0;
	$groups=get_groups_for_prof($_SESSION['login']);
	echo "<table class='boireaus' summary='Choix des enseignements'>\n";
	echo "<tr>\n";
	echo "<th>\n";
	echo "<a href='#' onClick='tout_cocher(true);return false;'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href='#' onClick='tout_cocher(false);return false;'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
	echo "</th>\n";
	echo "<th>Enseignement</th>\n";
	echo "<th>Description</th>\n";
	echo "<th>Classes</th>\n";
	echo "</tr>\n";
	$alt=1;
	foreach($groups as $current_group) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover'>\n";
		echo "<td>\n";
		echo "<input type='checkbox' name='id_groupe[]' id='id_groupe_$cpt' value='".$current_group['id']."' onchange='change_style_groupe($cpt)' />\n";
		echo "</td>\n";
		echo "<td>\n";
		echo "<label id='label_groupe_$cpt' for='id_groupe_$cpt'> ".$current_group['name']."</label>\n";
		echo "</td>\n";
		echo "<td>\n";
		echo $current_group['description'];
		echo "</td>\n";
		echo "<td>\n";
		echo $current_group['classlist_string'];
		echo "</td>\n";
		echo "</tr>\n";
		$cpt++;
	}
	echo "</table>\n";

	echo "<input type='hidden' name='choix_enseignements' value='y' />\n";

	//echo "<p style='color:red'>A FAIRE: Ajouter le choix Du/Au à ce niveau</p>\n";
	echo "<p>";
	echo "<label for='choix_periode_dates' style='cursor: pointer;'> \nExporter le(s) cahier(s) de textes de la date : </label>";

    echo "<input type='text' name = 'display_date_debut' size='10' value = \"".$display_date_debut."\" onfocus=\"document.getElementById('choix_periode_dates').checked=true;\" />";
    echo "<label for='choix_periode_dates' style='cursor: pointer;'><a href=\"#calend\" onClick=\"".$cal1->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" alt=\"Calendrier\" border=\"0\" /></a>\n";

    echo "&nbsp;à la date : </label>";
    echo "<input type='text' name = 'display_date_fin' size='10' value = \"".$display_date_fin."\" onfocus=\"document.getElementById('choix_periode_dates').checked=true;\" />";
    echo "<label for='choix_periode_dates' style='cursor: pointer;'><a href=\"#calend\" onClick=\"".$cal2->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" alt=\"Calendrier\" border=\"0\" /></a>\n";
	echo "<br />\n";
    echo " (<i>Veillez à respecter le format jj/mm/aaaa</i>)</label>\n";
	echo "</p>\n";

	echo "<p><input type='submit' value='Valider' /></p>\n";
	echo "</form>\n";

	echo "<script type='text/javascript'>

	function tout_cocher(mode) {
		for (var k=0;k<$cpt;k++) {
			if(document.getElementById('id_groupe_'+k)){
				document.getElementById('id_groupe_'+k).checked = mode;
				change_style_groupe(k);
			}
		}
	}

	function change_style_groupe(num) {
		//if(document.getElementById('id_groupe_'+num)) {
		if((document.getElementById('id_groupe_'+num))&&(document.getElementById('label_groupe_'+num))) {
			if(document.getElementById('id_groupe_'+num).checked) {
				document.getElementById('label_groupe_'+num).style.fontWeight='bold';
			}
			else {
				document.getElementById('label_groupe_'+num).style.fontWeight='normal';
			}
		}
	}

</script>\n";


}
else {
	// Pour les non-professeurs
	if(!isset($choix_enseignements)) {
		echo " | <a href='".$_SERVER['PHP_SELF']."'>Retour au choix des classes</a>";
		echo "</p>\n";

		echo "<p class='bold'>Choix des matières/enseignements&nbsp;:</p>\n";

		echo "<p style='color:red'>A FAIRE: MODE NON PROF à implémenter</p>\n";
	
		require("../lib/footer.inc.php");
		die();

		//+++++++++++++++++++++++++++++++++++++++++++++++++++
/*
		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
	
		echo "<ul style='list-style-type: none;'>\n";
		echo "<li>\n";
		echo "<input type='radio' name='choix_enseignements' id='choix_enseignements_tous' value='toutes' onchange='display_div_liste_enseignements()' checked /><label for='choix_enseignements_tous'> Tous les enseignements/matières</label>\n";
		echo "</li>\n";
		echo "<li>\n";
		echo "<input type='radio' name='choix_enseignements' id='choix_enseignements_certains' onchange='display_div_liste_enseignements()' value='certains' /><label for='choix_enseignements_certains'> Certains enseignements/matières seulement</label>\n";
	
		echo "<div id='div_liste_enseignements' style='margin-left: 2em;'>\n";
	
		echo "<div id='div_coche_lot' style='float: right; width: 20em;'></div>\n";
	
		$tab_id_matiere=array();
		$tab_liste_index_grp_matiere=array();
		$cpt=0;
		for($i=0;$i<count($id_classe);$i++) {
			//$sql="SELECT DISTINCT g.id, g.name, g.description FROM groupes g, j_groupes_classes jgc WHERE (g.id=jgc.id_groupe and jgc.id_classe='".$id_classe[$i]."') ORDER BY jgc.priorite, g.name";
			$sql="SELECT DISTINCT g.id, g.name, g.description, jgm.id_matiere FROM groupes g, j_groupes_classes jgc, j_groupes_matieres jgm WHERE (g.id=jgc.id_groupe AND jgm.id_groupe=jgc.id_groupe AND jgc.id_classe='".$id_classe[$i]."') ORDER BY jgc.priorite, g.name";
			//echo "$sql<br />";
			$call_group = mysql_query($sql);
			$nombre_ligne = mysql_num_rows($call_group);
			if($nombre_ligne==0) {
				echo "<p style='color:red;'>Aucun enseignement n'est défini dans la classe de ".get_class_from_id($id_classe[$i]).".</p>\n";
			}
			else {
	
				echo "<input type='hidden' name='id_classe[]' value='$id_classe[$i]' />\n";
	
				$first_grp[$id_classe[$i]]=$cpt;
				echo "<table class='boireaus' summary='Classe n°$id_classe[$i]'/>\n";
				echo "<tr>\n";
				echo "<th colspan='3'>\n";
				echo "Classe de ".get_class_from_id($id_classe[$i])."\n";
				echo "</th>\n";
				echo "</tr>\n";
	
				echo "<tr>\n";
				echo "<th>\n";
				//echo "Cocher/décocher\n";
				echo "<p><a href='#' onClick='ModifCase(".$id_classe[$i].",true);return false;'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href='#' onClick='ModifCase(".$id_classe[$i].",false);return false;'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a></p>\n";
				echo "</th>\n";
				echo "<th>Enseignement</th>\n";
				echo "<th>Professeur</th>\n";
				echo "</tr>\n";
	
				$alt=1;
				while($lig_grp=mysql_fetch_object($call_group)) {
					$alt=$alt*(-1);
					echo "<tr class='lig$alt white_hover'>\n";
					echo "<td>\n";
					echo "<input type='checkbox' name='id_groupe_".$id_classe[$i]."[]' id='id_groupe_$cpt' value='$lig_grp->id' onchange='change_style_groupe($cpt)' checked />\n";
					echo "</td>\n";
					echo "<td style='text-align:left; font-weight: bold;'><label for='id_groupe_$cpt' id='label_groupe_$cpt'>$lig_grp->name (<i>$lig_grp->description</i>)</label></td>\n";
					echo "<td style='text-align:left;'>\n";
					$sql="SELECT DISTINCT nom,prenom,civilite FROM utilisateurs u, j_groupes_professeurs jgp WHERE u.login=jgp.login AND jgp.id_groupe='$lig_grp->id' ORDER BY u.nom, u.prenom;";
					$res_prof_grp=mysql_query($sql);
					if(mysql_num_rows($res_prof_grp)>0) {
						$lig_prof_grp=mysql_fetch_object($res_prof_grp);
						echo $lig_prof_grp->civilite." ".strtoupper($lig_prof_grp->nom)." ".casse_mot($lig_prof_grp->prenom,"majf2");
						while($lig_prof_grp=mysql_fetch_object($res_prof_grp)) {
							echo ", ";
							echo $lig_prof_grp->civilite." ".strtoupper($lig_prof_grp->nom)." ".casse_mot($lig_prof_grp->prenom,"majf2");
						}
					}
					echo "</td>\n";
					echo "</tr>\n";
	
					$tab_liste_index_grp_matiere[$lig_grp->id_matiere][]=$cpt;
					if(!in_array($lig_grp->id_matiere, $tab_id_matiere)) {$tab_id_matiere[]=$lig_grp->id_matiere;}
	
					$cpt++;
				}
				echo "</table>\n";
				$last_grp[$id_classe[$i]]=$cpt;
			}
			echo "<br />\n";
		}
	
		echo "<p><a href='javascript:ModifToutesCases(true)'>Cocher</a> / <a href='javascript:ModifToutesCases(false)'>décocher</a> tous les enseignements</p>\n";
	
		echo "</div>\n";
	
		echo "</li>\n";
	
		echo "</ul>\n";
	
		echo "<p><input type='submit' value='Valider' /></p>\n";
		echo "</form>\n";
	
		$chaine_div_coche_lot="Pour toutes les classes,<br />";
	
		for($j=0;$j<count($tab_id_matiere);$j++) {
			$chaine_div_coche_lot.="<a href='javascript:coche_lot($j,true)'>Cocher</a> / <a href='javascript:coche_lot($j,false)'>décocher</a> $tab_id_matiere[$j]<br />";
	
			for($k=0;$k<count($tab_liste_index_grp_matiere[$tab_id_matiere[$j]]);$k++) {
				if(!isset($chaine_array_index[$j])) {
					//$chaine_array_index[$j]="tab_index_$j=new Array(";
					$chaine_array_index[$j]="tab_index[$j]=new Array(";
					$chaine_array_index[$j].=$tab_liste_index_grp_matiere[$tab_id_matiere[$j]][$k];
				}
				else {
					$chaine_array_index[$j].=", ".$tab_liste_index_grp_matiere[$tab_id_matiere[$j]][$k];
				}
			}
			if(isset($chaine_array_index[$j])) {
				$chaine_array_index[$j].=");";
			}
		}
		$chaine_div_coche_lot.="<a href='javascript:ModifToutesCases(true)'>Cocher</a> / <a href='javascript:ModifToutesCases(false)'>décocher</a>  tous les enseignements";
	
		echo "<script type='text/javascript'>
		document.getElementById('div_liste_enseignements').style.display='none';
	
		function display_div_liste_enseignements() {
			if(document.getElementById('choix_enseignements_certains').checked==true) {
				document.getElementById('div_liste_enseignements').style.display='block';
			}
			else {
				document.getElementById('div_liste_enseignements').style.display='none';
			}
		}
	
		if(document.getElementById('div_coche_lot')) {
			document.getElementById('div_coche_lot').innerHTML=\"$chaine_div_coche_lot\";
		}
	
		function coche_lot(num,mode) {
			tab_index=new Array();
	";
	
		for($j=0;$j<count($tab_id_matiere);$j++) {
			echo "		".$chaine_array_index[$j];
		}
	
		echo "
			tab=tab_index[num];
			for(k=0;k<tab.length;k++) {
				//alert('id_groupe_'+tab[k]);
				if(document.getElementById('id_groupe_'+tab[k])) {
					document.getElementById('id_groupe_'+tab[k]).checked = mode;
					change_style_groupe(tab[k]);
				}
			}
		}
	
		function ModifToutesCases(mode) {
	";
		for($i=0;$i<count($id_classe);$i++) {
			if($temoin_classe[$i]=='y') {
				echo "		ModifCase(".$id_classe[$i].",mode);\n";
			}
		}
	
		echo "	}
	
		function ModifCase(id_classe,mode) {
			var first_grp=new Array();
			var last_grp=new Array();\n";
	
		for($i=0;$i<count($id_classe);$i++) {
			if($temoin_classe[$i]=='y') {
				echo "		first_grp[".$id_classe[$i]."]=".$first_grp[$id_classe[$i]].";
			last_grp[".$id_classe[$i]."]=".$last_grp[$id_classe[$i]].";\n";
			}
		}
	
		echo "
			for (var k=first_grp[id_classe];k<last_grp[id_classe];k++) {
				if(document.getElementById('id_groupe_'+k)){
					document.getElementById('id_groupe_'+k).checked = mode;
					change_style_groupe(k);
				}
			}
		}
	
		function change_style_groupe(num) {
			//if(document.getElementById('id_groupe_'+num)) {
			if((document.getElementById('id_groupe_'+num))&&(document.getElementById('label_groupe_'+num))) {
				if(document.getElementById('id_groupe_'+num).checked) {
					document.getElementById('label_groupe_'+num).style.fontWeight='bold';
				}
				else {
					document.getElementById('label_groupe_'+num).style.fontWeight='normal';
				}
			}
		}
	
	</script>\n";
	
*/
	}
}

if(isset($id_groupe)&&($_SESSION['statut']=='professeur')) {
	// A déplacer en entête par la suite pour générer le fichier plutôt que de l'afficher.

	$gepiSchoolName=getSettingValue('gepiSchoolName');
	$gepiYear=getSettingValue('gepiYear');

	function html_entete($titre='Cahier de textes',$niveau_arbo=1) {
		$entete="";
		// A FAIRE

		$entete.='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Cache-Control" content="no-cache" />
<meta http-equiv="Expires" content="0" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta http-equiv="Content-Style-Type" content="text/css" />';

		if($niveau_arbo==0) {
			$pref_arbo=".";
		}
		else {
			$pref_arbo="..";
		}

		$entete.="<link rel='stylesheet' type='text/css' href='$pref_arbo/style.css' />
<link rel='stylesheet' type='text/css' href='$pref_arbo/accessibilite.css' media='screen' />
<link rel='stylesheet' type='text/css' href='$pref_arbo/accessibilite_print.css' media='print' />
<link rel='stylesheet' type='text/css' href='$pref_arbo/portable.css' media='handheld' />
<link title='bandeau' rel='stylesheet' type='text/css' href='$pref_arbo/css/bandeau_r01.css' media='screen' />
<!--[if lte IE 7]>
<link title='bandeau' rel='stylesheet' type='text/css' href='$pref_arbo/css/bandeau_r01_ie.css' media='screen' />
<![endif]-->
<!--[if lte IE 6]>
<link title='bandeau' rel='stylesheet' type='text/css' href='$pref_arbo/css/bandeau_r01_ie6.css' media='screen' />
<![endif]-->
<!--[if IE 7]>
<link title='bandeau' rel='stylesheet' type='text/css' href='$pref_arbo/css/bandeau_r01_ie7.css' media='screen' />
<![endif]-->
<link rel='stylesheet' type='text/css' href='$pref_arbo/style_screen_ajout.css' />\n";
		$entete.="<title>$titre</title>\n";
		$entete.="</head>\n";
		$entete.="<body>\n";

		return $entete;
	}

	function html_pied_de_page() {
		$pied_de_page="";
		// A FAIRE

		$pied_de_page.="</body>\n";
		$pied_de_page.="</html>\n";

		return $pied_de_page;
	}


	function my_affiche_docs_joints($id_ct,$type_notice) {
		global $tab_chemin_url;

		// documents joints
		$html = '';
		$architecture="/documents/cl_dev";
		if ($type_notice == "t") {
			$sql = "SELECT titre, emplacement FROM ct_devoirs_documents WHERE id_ct_devoir='$id_ct' ORDER BY 'titre'";
		} else if ($type_notice == "c") {
			$sql = "SELECT titre, emplacement FROM ct_documents WHERE id_ct='$id_ct' ORDER BY 'titre'";
		}
		
		$res = sql_query($sql);
		if (($res) and (sql_count($res)!=0)) {
			$html .= "<span class='petit'>Document(s) joint(s):</span>";
			//$html .= "<ul type=\"disc\" style=\"padding-left: 15px;\">";
			$html .= "<ul style=\"padding-left: 15px;\">";
			for ($i=0; ($row = sql_row($res,$i)); $i++) {
					$titre = $row[0];
					$emplacement = $row[1];
					//$html .= "<li style=\"padding: 0px; margin: 0px; font-family: arial, sans-serif; font-size: 80%;\"><a href=\"$emplacement\" target=\"blank\">$titre</a></li>";
					// Ouverture dans une autre fenêtre conservée parce que si le fichier est un PDF, un TXT, un HTML ou tout autre document susceptible de s'ouvrir dans le navigateur, on risque de refermer sa session en croyant juste refermer le document.
					// alternative, utiliser un javascript
					$html .= "<li style=\"padding: 0px; margin: 0px; font-family: arial, sans-serif; font-size: 80%;\"><a onclick=\"window.open(this.href, '_blank'); return false;\" href=\"$emplacement\">$titre</a></li>";

					$tab_chemin_url[]=$emplacement;

			}
			$html .= "</ul>";
		}
		return $html;
	}

	$dirname=get_user_temp_directory();
	if(!$dirname) {
		echo "<p style='color:red;'>Problème avec le dossier temporaire.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	// Préparation de l'arborescence
	$nom_export="export_cdt_".strftime("%Y%m%d_%H%M%S");
	$dossier_export="../temp/".$dirname."/".$nom_export;
	$creation=mkdir($dossier_export);
	if(!$creation) {
		echo "<p style='color:red;'>Erreur lors de la préparation de l'arborescence $dossier_export</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$creation=mkdir($dossier_export."/cahier_texte");
	if(!$creation) {
		echo "<p style='color:red;'>Erreur lors de la préparation de l'arborescence ".$dossier_export."/cahier_texte.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$creation=mkdir($dossier_export."/css");
	if(!$creation) {
		echo "<p style='color:red;'>Erreur lors de la préparation de l'arborescence ".$dossier_export."/css</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	// Copie des feuilles de styles
	$tab_styles=array("style.css", "style_old.css", "style_screen_ajout.css");
	for($i=0;$i<count($tab_styles);$i++) {
		if(file_exists("../".$tab_styles[$i])) {
			copy("../".$tab_styles[$i],$dossier_export."/".$tab_styles[$i]);

			$tab_fichiers_a_zipper[]=$dossier_export."/".$tab_styles[$i];
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
		//echo "copy(\"../css/\".$tab_styles[$i],$dossier_export.\"/css/\".$tab_styles[$i])<br />";
		copy("../css/".$tab_styles[$i],$dossier_export."/css/".$tab_styles[$i]);

		$tab_fichiers_a_zipper[]=$dossier_export."/css/".$tab_styles[$i];
	}

	echo "<div id='div_archive_zip'></div>\n";
	echo "<p class='bold'>Affichage des cahiers de textes extraits</p>\n";

	// Générer la page d'index
	$html="";
	//$html.=html_entete();
	$html.="<h1 style='text-align:center;'>Cahiers de textes (".$gepiSchoolName." - ".$gepiYear.")</h1>\n";
	$html.="<p>Cahier de textes (<i>$display_date_debut - $display_date_fin</i>) de&nbsp;:</p>\n";
	$html.="<ul>\n";
	for($i=0;$i<count($id_groupe);$i++) {
		$current_group=get_group($id_groupe[$i]);

		$nom_groupe=remplace_accents($current_group['name'],'all');
		$description_groupe=remplace_accents($current_group['description'],'all');
		$classlist_string_groupe=remplace_accents($current_group['classlist_string'],'all');
		$nom_page_html_groupe=$id_groupe[$i]."_".$nom_groupe."_"."$description_groupe"."_".$classlist_string_groupe.".html";

		$nom_fichier[$id_groupe[$i]]=$nom_page_html_groupe;
		$nom_detaille_groupe[$id_groupe[$i]]=$current_group['name']." (<i>".$current_group['description']." en (".$current_group['classlist_string'].")</i>)";

		$html.="<li><a href='cahier_texte/$nom_page_html_groupe'>".$current_group['name']." (<i>".$current_group['description']." en (".$current_group['classlist_string'].")</i>)</a></li>";
	}
	$html.="</ul>\n";
	//$html.=html_pied_de_page();

	echo "<div style='border: 1px solid black;'>\n";
	echo $html;
	echo "</div>\n";

	$html=html_entete("Index des cahiers de textes",0).$html;
	$html.=html_pied_de_page();

	$f=fopen($dossier_export."/index.html","w+");
	fwrite($f,$html);
	fclose($f);

	$tab_fichiers_a_zipper[]=$dossier_export."/index.html";

	echo "<hr width='200px' />\n";



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

	// Dans la page générée, permettre de masquer via JavaScript telle ou telle catégorie Notices ou devoirs,...
	for($i=0;$i<count($id_groupe);$i++) {
		$tab_dates=array();
		$tab_dates2=array();
		$tab_chemin_url=array();

		$html="";
		//$html.=html_entete();

		$html.="<div id='div_lien_retour_".$id_groupe[$i]."' class='noprint' style='float:right; width:6em'><a href='../index.html'>Retour</a></div>\n";

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
			/*
			$notice_visible="y";
			if($lig->date_ct>time()) {
				if(($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable')) {
					$notice_visible="n";
				}
			}
	
			if($notice_visible=="y") {
			*/
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
			//}
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

/*
		for($k=0;$k<count($tab_dates);$k++) {
			//$html.="<div style='border:1px solid black; margin:3px; padding: 3px;'>\n";

			$html.="<div class='infobulle_corps' style='float:left; width:12%; text-align: center; border:1px solid black; margin:3px; padding: 3px;'>\n";
			$html.="<h3 class='see_all_h3'>$tab_dates[$k]</h3>\n";
			$html.="</div>\n";

			if((isset($tab_dev[$tab_dates[$k]]))||(isset($tab_notices[$tab_dates[$k]]))) {
				//$html.="<div class='see_all_notice couleur_bord_tableau_notice color_fond_notices_t' style='float:left; width:40%; border:1px solid black; margin:3px; padding: 3px;'>\n";
				if(isset($tab_dev[$tab_dates[$k]])) {
					//$html.="<div class='see_all_notice couleur_bord_tableau_notice color_fond_notices_t' style='margin-left: 14%; width:40%; border:1px solid black; padding: 3px;'>\n";
					$html.="<div class='see_all_notice couleur_bord_tableau_notice color_fond_notices_t' style='float: left; width:40%; border:1px solid black; padding: 3px;'>\n";
					foreach($tab_dev[$tab_dates[$k]] as $key => $value) {
						$html.="<div class='see_all_notice couleur_bord_tableau_notice color_fond_notices_t' style='margin: 1px; padding: 1px; border: 1px solid black; width: 99%;'>".$value['contenu'];
						$adj=affiche_docs_joints($value['id_ct'],"t");
						if($adj!='') {
							$html.="<div style='border: 1px dashed black'>\n";
							$html.=$adj;
							$html.="</div>\n";
						}
						$html.="</div>\n";
					}
					$html.="</div>\n";
				}
				else {
					$html.="<div style='float: left; width:40%; padding: 3px;'>\n";
					$html.="&nbsp;\n";
					$html.="</div>\n";
				}
				//$html.="</div>\n";

				//$html.="<div class='see_all_notice couleur_bord_tableau_notice color_fond_notices_c' style='float:left; width:40%; border:1px solid black; margin:3px; padding: 3px;'>\n";
				if(isset($tab_notices[$tab_dates[$k]])) {
					//$html.="<div class='see_all_notice couleur_bord_tableau_notice color_fond_notices_c' style='margin-left: 56%; width:40%; border:1px solid black; padding: 3px;'>\n";
					$html.="<div class='see_all_notice couleur_bord_tableau_notice color_fond_notices_c' style='float:left; width:40%; border:1px solid black; margin:3px; padding: 3px;'>\n";
					foreach($tab_notices[$tab_dates[$k]] as $key => $value) {
						$html.="<div class='see_all_notice couleur_bord_tableau_notice color_fond_notices_c' style='margin: 1px; padding: 1px; border: 1px solid black; width: 99%;'>".$value['contenu'];
						$adj=affiche_docs_joints($value['id_ct'],"c");
						if($adj!='') {
							$html.="<div style='border: 1px dashed black'>\n";
							$html.=$adj;
							$html.="</div>\n";
						}
						$html.="</div>\n";
					}
					$html.="</div>\n";
				}
				else {
					$html.="<div style='float: left; width:40%; padding: 3px;'>\n";
					$html.="&nbsp;\n";
					$html.="</div>\n";
				}

				//$html.="</div>\n";
				//$html.="</div>\n";

				$html.="<div style='clear:both;'></div>\n";

			}

		}
*/


		$html.="<table class='boireaus' style='margin:3px;' border='1' summary='CDT'>\n";
		for($k=0;$k<count($tab_dates);$k++) {
			//$html.="<div style='border:1px solid black; margin:3px; padding: 3px;'>\n";

			$html.="<tr>\n";
			$html.="<td style='width:12%; text-align: center; padding: 3px;'>\n";
			$html.="<h3 class='see_all_h3'>$tab_dates[$k]</h3>\n";
			$html.="</td>\n";

			$html.="<td class='see_all_notice couleur_bord_tableau_notice color_fond_notices_t' style='width:40%; text-align:left; padding: 3px;'>\n";
			if(isset($tab_dev[$tab_dates[$k]])) {
				foreach($tab_dev[$tab_dates[$k]] as $key => $value) {
					$html.="<div class='see_all_notice couleur_bord_tableau_notice color_fond_notices_t' style='margin: 1px; padding: 1px; border: 1px solid black; width: 99%;'>".$value['contenu'];
					$adj=affiche_docs_joints($value['id_ct'],"t");
					if($adj!='') {
						$html.="<div style='border: 1px dashed black'>\n";
						$html.=$adj;
						$html.="</div>\n";
					}
					$html.="</div>\n";
				}
			}
			else {
				$html.="&nbsp;\n";
			}
			$html.="</td>\n";

			$html.="<td class='see_all_notice couleur_bord_tableau_notice color_fond_notices_c' style='width:40%; text-align:left; padding: 3px;'>\n";
			if(isset($tab_notices[$tab_dates[$k]])) {
				foreach($tab_notices[$tab_dates[$k]] as $key => $value) {
					$html.="<div class='see_all_notice couleur_bord_tableau_notice color_fond_notices_c' style='margin: 1px; padding: 1px; border: 1px solid black; width: 99%;'>".$value['contenu'];
					$adj=affiche_docs_joints($value['id_ct'],"c");
					if($adj!='') {
						$html.="<div style='border: 1px dashed black'>\n";
						$html.=$adj;
						$html.="</div>\n";
					}
					$html.="</div>\n";
				}
			}
			else {
				$html.="&nbsp;\n";
			}
			$html.="</td>\n";
			$html.="</tr>\n";

			//$html.="<div style='clear:both;'></div>\n";

		}
		$html.="</table>\n";

		echo "<div style='border: 1px solid black;'>\n";
		echo $html;
		echo "</div>\n";

		echo "<script type='text/javascript'>
	if(document.getElementById('div_lien_retour_".$id_groupe[$i]."')) {
		document.getElementById('div_lien_retour_".$id_groupe[$i]."').style.display='none';
	}
</script>\n";

		$html=html_entete("CDT: ".$nom_detaille_groupe[$id_groupe[$i]],1).$html;
		$html.=html_pied_de_page();

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

		echo "<hr width='200px' />\n";
	}


	// Générer des fichiers URL_documents.txt (URL seule), URL_documents.csv (chemin;URL), script bash/batch/auto-it pour télécharger en créant/parcourant l'arborescence des documents

	if(isset($_SERVER['HTTP_REFERER'])) {
		$tmp=explode("?",$_SERVER['HTTP_REFERER']);
		//$chemin_site=my_ereg_replace("/cahier_texte_2/export_cdt.php$","",$tmp[0]);
		$chemin_site=my_ereg_replace("/cahier_texte_2","",dirname($tmp[0]));
	
		$fichier_url_site=$dossier_export."/url_site.txt";
		$f=fopen($fichier_url_site,"a+");
		fwrite($f,$chemin_site."\n");
		fclose($f);
	
		$tab_fichiers_a_zipper[]=$fichier_url_site;
	}

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

		// On fait le ménage
		for($i=0;$i<count($tab_fichiers_a_zipper);$i++) {
			unlink($tab_fichiers_a_zipper[$i]);
		}
		rmdir($dossier_export."/cahier_texte");
		rmdir($dossier_export."/css");
		rmdir($dossier_export);
	}
}
else {
	// A FAIRE pour les non profs
}

echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
