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

/*
	function html_entete($titre='Cahier de textes',$niveau_arbo=1,$avec_init_php='n',$chaine_login_prof="") {
		$entete="";

		// trunk/documents/archives/etablissement/cahier_texte_2010_2011/cdt/index_classes.html

		if($avec_init_php=='y') {
			if($niveau_arbo==0) {
				$pref_arbo="../../../..";
				$n_arbo=4;
			}
			else {
				$pref_arbo="../../../../..";
				$n_arbo=5;
			}

			$entete.='<?php

$niveau_arbo='.$n_arbo.';
require_once("../../../../../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == "c") {
	header("Location: '.$pref_arbo.'/utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == "0") {
	header("Location: '.$pref_arbo.'/logout.php?auto=1");
	die();
}

$tab_statuts=array("administrateur","professeur","scolarite","cpe","autre");
if(!in_array($_SESSION["statut"],$tab_statuts)) {
	header("Location: '.$pref_arbo.'/logout.php?auto=1");
	die();
}

// Ajouter une autre filtrage selon le statut...
// ... et des retours différents

$tab_login=array('.$chaine_login_prof.');
if(($_SESSION["statut"]=="professeur")&&(!in_array($_SESSION["login"],$tab_login))) {
	header("Location: '.$pref_arbo.'/logout.php?auto=1");
	die();
}

?>
';

			//$entete.="";
		}

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
*/

	function html_entete($titre='Cahier de textes',$n_arbo=1,$avec_init_php='n',$chaine_login_prof="") {
		$entete="";

		// trunk/documents/archives/etablissement/cahier_texte_2010_2011/cdt/index_classes.html

		if($avec_init_php=='y') {
			if($n_arbo==0) {
				$pref_arbo="../..";

				$niveau_arbo=4;
			}
			else {
				$pref_arbo="../../..";

				$niveau_arbo=5;
			}

			$entete.='<?php

$niveau_arbo='.$niveau_arbo.';
$chaine_login_prof="'.$chaine_login_prof.'";
require_once("'.$pref_arbo.'/entete.php");

?>
';

			//$entete.="";
		}

		$entete.='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Cache-Control" content="no-cache" />
<meta http-equiv="Expires" content="0" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta http-equiv="Content-Style-Type" content="text/css" />';

		if((isset($niveau_arbo))&&($niveau_arbo==0)) {
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

	function lignes_cdt($tab_dates, $tab_notices, $tab_dev,$dossier_documents="",$mode="") {
		global $temoin_erreur;

		$html="<table class='boireaus' style='margin:3px;' border='1' summary='CDT'>\n";
		$alt=1;
		for($k=0;$k<count($tab_dates);$k++) {
			//$html.="<div style='border:1px solid black; margin:3px; padding: 3px;'>\n";

			$alt=$alt*(-1);
			$html.="<tr class='lig$alt'>\n";
			$html.="<td style='width:12%; text-align: center; padding: 3px;'>\n";
			$html.="<h3 class='see_all_h3'>$tab_dates[$k]</h3>\n";
			$html.="</td>\n";

			//$html.="<td class='see_all_notice couleur_bord_tableau_notice color_fond_notices_t' style='width:40%; text-align:left; padding: 3px;'>\n";
			$html.="<td style='width:40%; text-align:left; padding: 3px;'>\n";
			if(isset($tab_dev[$tab_dates[$k]])) {
				foreach($tab_dev[$tab_dates[$k]] as $key => $value) {
					$html.="<div class='see_all_notice couleur_bord_tableau_notice color_fond_notices_t' style='margin: 1px; padding: 1px; border: 1px solid black; width: 99%;'>".$value['contenu'];
					$adj=my_affiche_docs_joints($value['id_ct'],"t");
					if($adj!='') {
						$html.="<div style='border: 1px dashed black'>\n";
						$html.=$adj;
						$html.="</div>\n";

						if($dossier_documents!='') {
							$tab_documents_joints=my_tab_docs_joints($value['id_ct'],"t");
							my_transfert_docs_joints($tab_documents_joints,$dossier_documents,$mode);
						}
					}
					$html.="</div>\n";
				}
			}
			else {
				$html.="&nbsp;\n";
			}
			$html.="</td>\n";

			//$html.="<td class='see_all_notice couleur_bord_tableau_notice color_fond_notices_c' style='width:40%; text-align:left; padding: 3px;'>\n";
			$html.="<td style='width:40%; text-align:left; padding: 3px;'>\n";
			if(isset($tab_notices[$tab_dates[$k]])) {
				foreach($tab_notices[$tab_dates[$k]] as $key => $value) {
					$html.="<div class='see_all_notice couleur_bord_tableau_notice color_fond_notices_c' style='margin: 1px; padding: 1px; border: 1px solid black; width: 99%;'>".$value['contenu'];
					$adj=my_affiche_docs_joints($value['id_ct'],"c");
					if($adj!='') {
						$html.="<div style='border: 1px dashed black'>\n";
						$html.=$adj;
						$html.="</div>\n";

						if($dossier_documents!='') {
							$tab_documents_joints=my_tab_docs_joints($value['id_ct'],"c");
							my_transfert_docs_joints($tab_documents_joints,$dossier_documents,$mode);
						}
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

		return $html;
	}

	function my_affiche_docs_joints($id_ct,$type_notice) {
		global $tab_chemin_url;

		// documents joints
		$html = '';
		//$architecture="/documents/cl_dev";
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

	function my_tab_docs_joints($id_ct,$type_notice) {
		$tab_documents_joints=array();

		if ($type_notice == "t") {
			$sql = "SELECT titre, emplacement FROM ct_devoirs_documents WHERE id_ct_devoir='$id_ct' ORDER BY 'titre'";
		} else if ($type_notice == "c") {
			$sql = "SELECT titre, emplacement FROM ct_documents WHERE id_ct='$id_ct' ORDER BY 'titre'";
		}
		
		$res = sql_query($sql);
		if (($res) and (sql_count($res)!=0)) {
			for ($i=0; ($row = sql_row($res,$i)); $i++) {
					$titre = $row[0];
					$emplacement = $row[1];

					$tab_documents_joints[]=$emplacement;

			}
		}
		return $tab_documents_joints;
	}

	function my_transfert_docs_joints($tab_documents_joints,$dossier_documents,$mode) {
		global $temoin_erreur;

		//echo "\$mode=$mode<br />";

		for($loop=0;$loop<count($tab_documents_joints);$loop++) {
			$dossier_courant=preg_replace('|^../documents/|','',dirname($tab_documents_joints[$loop]));
			$fichier_courant=basename($tab_documents_joints[$loop]);
			//echo "\$dossier_courant=$dossier_courant<br />";
			//echo "\$fichier_courant=$fichier_courant<br />";
			//echo "\$tab_documents_joints[$loop]=$tab_documents_joints[$loop]<br />";

			$transferer_doc="y";
			if(!file_exists($dossier_documents."/".$dossier_courant)) {
				//echo "Le dossier $dossier_documents/$dossier_courant n'existe pas encore<br />";
				//$res=mkdir("$dossier_documents/$dossier_courant");
				$res=creer_rep_docs_joints($dossier_documents, $dossier_courant, "../../../../../..");
				if(!$res) {
					echo "<span style='color:red; margin-left: 3em;'>Erreur lors de la préparation de l'arborescence $dossier_documents/$dossier_courant</span><br />\n";
					$transferer_doc="n";

					$temoin_erreur="y";
				}
			}

			//echo "\$transferer_doc=$transferer_doc<br />";

			if($transferer_doc=="y") {
				if(file_exists($tab_documents_joints[$loop])) {
					$res=copy($tab_documents_joints[$loop],"$dossier_documents/$dossier_courant/$fichier_courant");
	
					//echo "\$res=copy($tab_documents_joints[$loop],\"$dossier_documents/$dossier_courant/$fichier_courant\")<br />";
					//echo "\$res=$res<br />";
	
					if(($res)&&($mode=='transfert')) {
						if(!unlink($tab_documents_joints[$loop])) {
							echo "<span style='color:red; margin-left: 3em;'>Erreur lors de la suppression de $tab_documents_joints[$loop]</span><br />\n";
	
							$temoin_erreur="y";
						}
						/*
						else {
							echo "<span style='color:green; margin-left: 3em;'>Suppression de $tab_documents_joints[$loop] effectuée</span><br />\n";
						}
						*/
					}
				}
				else {
						echo "<span style='color:red; margin-left: 3em;'>Il semble que le fichier $tab_documents_joints[$loop] n'existe pas.</span><br />\n";

						$temoin_erreur="y";
				}
			}

			//echo "<br />";
		}
	}

	function unhtmlentities($chaineHtml)
	{
		$tmp = get_html_translation_table(HTML_ENTITIES);
		$tmp = array_flip ($tmp);
		$chaineTmp = strtr ($chaineHtml, $tmp);
		return $chaineTmp;
	}

	function creer_index_logout($path, $pref_arbo_logout) {
		if (!file_exists($path)) {return false;}
		else {
			$ok = false;
			if ($f = @fopen("$path/.test", "w")) {
				@fputs($f, '<'.'?php $ok = true; ?'.'>');
				@fclose($f);
				include("$path/.test");
				if($ok) {
					if ($f = @fopen("$path/index.html", "w")) {
						@fputs($f, '<script type="text/javascript">document.location.replace("'.$pref_arbo_logout.'/login.php")</script>');
						@fclose($f);
					}
				}
			}
			return $ok;
		}
	}

	function creer_rep_docs_joints($base, $subdir, $pref_arbo_logout) {
		$path = $base.'/'.$subdir;
		if (file_exists($path)) {return true;}
	
		@mkdir($path, 0777);
		@chmod($path, 0777);
		$ok = false;
		if ($f = @fopen("$path/.test", "w")) {
			@fputs($f, '<'.'?php $ok = true; ?'.'>');
			@fclose($f);
			include("$path/.test");
			if($ok) {
				if ($f = @fopen("$path/index.html", "w")) {
					@fputs($f, '<script type="text/javascript">document.location.replace("'.$pref_arbo_logout.'/login.php")</script>');
					@fclose($f);
				}
			}
		}
		return $ok;
	}

	function get_dossier_etab_cdt_archives() {
		global $multisite;

		$dossier="";

		if(((isset($multisite))&&($multisite=='y'))||(getSettingValue('multisite')=='y')) {
			if(isset($_COOKIE['RNE'])) {
				$dossier=$_COOKIE['RNE'];
			}
		}
		else {
			$dossier="etablissement";
		}

		return $dossier;
	}

	//=======================================================
	// Fonction récupérée dans /mod_ooo/lib/lib_mod_ooo.php

	//$repaussi==true ~> efface aussi $rep
	//retourne true si tout s'est bien passé,
	//false si un fichier est resté (problème de permission ou attribut lecture sous Win
	//dans tous les cas, le maximum possible est supprimé.
	function deltree($rep,$repaussi=true) {
		static $niv=0;
		$niv++;
		if (!is_dir($rep)) {return false;}
		$handle=opendir($rep);
		if (!$handle) {return false;}
		while ($entree=readdir($handle)) {
			if (is_dir($rep.'/'.$entree)) {
				if ($entree!='.' && $entree!='..') {
					$ok=deltree($rep.'/'.$entree);
				}
				else {$ok=true;}
			}
			else {
				$ok=@unlink($rep.'/'.$entree);
			}
		}
		closedir($handle);
		$niv--;
		if ($niv || $repaussi) $ok &= @rmdir($rep);
		return $ok;
	}
	//=======================================================

?>
