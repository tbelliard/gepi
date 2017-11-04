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
// Pour desactiver le correctif temporaire javascript sur les liens de retour professeur.
//$liens_retour_ok="y";
$liens_retour_ok2="y";

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
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Cache-Control" content="no-cache" />
<meta http-equiv="Expires" content="0" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta http-equiv="Content-Style-Type" content="text/css" />';

		//if((isset($niveau_arbo))&&($niveau_arbo==0)) {
		if((isset($n_arbo))&&($n_arbo==0)) {
			$pref_arbo=".";
		}
		elseif((isset($n_arbo))&&($n_arbo==2)) {
			$pref_arbo="../..";
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
<link rel='stylesheet' type='text/css' href='$pref_arbo/style_screen_ajout.css' />
<script type='text/javascript' src='$pref_arbo/js/brainjar_drag.js'></script>
<script type='text/javascript' src='$pref_arbo/js/position.js'></script>
<script type='text/javascript' src='$pref_arbo/js/selection_notices.js'></script>\n";
		$entete.="<title>$titre</title>\n";
		$entete.="</head>\n";
		$entete.="<body>\n";

		$style_box="color: #000000; border: 1px solid #000000; padding: 0px; position: absolute; z-index: 1;";
		$style_bar="color: #ffffff; cursor: move; font-weight: bold; padding: 0px;";
		$style_close="color: #ffffff; cursor: move; font-weight: bold; float:right; width: 16px; margin-right: 1px;";

		$id='archive_selection_notices';
		$largeur="30em";
		$hauteur="10";
		$hauteur_unite="em";

		$div="<div id='$id' class='infobulle_corps' style='$style_box width: ".$largeur."; height: ".$hauteur.$hauteur_unite."; top:0px; left:0px; display:none;'>\n";
			// Barre de titre:
			$div.="<div class='infobulle_entete' style='$style_bar width: ".$largeur."' onmousedown=\"dragStart(event, '$id')\">\n";

				$div.="<div style='$style_close'><a href=\"javascript:cacher_div('$id')\">";
					$div.="<img src='../images/icons/close16.png' width='16' height='16' alt='Fermer' title='Fermer' />";
				$div.="</a></div>\n";

				$div.="<div style='$style_close'><a href=\"#\" onclick=\"document.getElementById('".$id."_contenu_notices').innerHTML='';return false;\">";
					$div.="<img src='../images/icons/trash.png' width='16' height='16' alt='Vider la sélection' title='Vider la sélection' />";
				$div.="</a></div>\n";

				$div.="<span style='padding-left: 1px;'>Votre sélection</span>\n";
			$div.="</div>\n";

			$div.="<div id='".$id."_contenu_corps'>";
				$hauteur_hors_titre=$hauteur-1; // Le calcul n'est correct que dans le cas où l'unité est 'em'
				$div.="<div style='width: ".$largeur."; height: ".$hauteur_hors_titre.$hauteur_unite."; overflow: auto;'>\n";

					$div.="<form action=\"<?php echo \$_SERVER['PHP_SELF'];?>\" method='post'>\n";
					$div.="<?php if(\$_SESSION['statut']=='professeur') {echo \"<p><input type='submit' value='Valider/copier la sélection' /></p>\";}?>\n";
					//$div.="<p><input type='submit' value='Valider/copier la sélection' /></p>\n";
					$div.="<textarea style='display:none' name='".$id."_textarea' id='".$id."_textarea'></textarea>\n";
					$div.="</form>\n";

					$div.="<div style='padding-left: 1px;' id='".$id."_contenu_notices'>\n";
					$div.="</div>\n";

				$div.="</div>\n";
			$div.="</div>\n";

		$div.="</div>\n";

		$entete.=$div;

		return $entete;
	}


	function html_pied_de_page() {
		$pied_de_page="";
		// A FAIRE

		$pied_de_page.="<div id='EmSize' style='visibility:hidden; position:absolute; left:1em; top:1em;'></div>

<script type='text/javascript'>
	var ele=document.getElementById('EmSize');
	var em2px=ele.offsetLeft
	//alert('1em == '+em2px+'px');

	temporisation_chargement='ok';
</script>\n";

		$pied_de_page.="</body>\n";
		$pied_de_page.="</html>\n";

		return $pied_de_page;
	}

	function lignes_cdt($tab_dates, $tab_notices, $tab_dev,$dossier_documents="",$mode="") {
		global $temoin_erreur;
		global $ne_pas_afficher_colonne_vide;
		global $action;

		$html="<table class='boireaus' style='margin:3px;' border='1' summary='CDT'>\n";
		$html.="<tr>
	<th>Date</th>
	<th id='th_colonne_t' style='width:40%;'>Travaux à faire</th>
	<th id='th_colonne_c' style='width:40%;'>Compte-rendu de séance</th>
</tr>\n";
		$alt=1;
		for($k=0;$k<count($tab_dates);$k++) {
			//$html.="<div style='border:1px solid black; margin:3px; padding: 3px;'>\n";

			$alt=$alt*(-1);
			$html.="<tr class='lig$alt'>\n";
			$html.="<td style='width:12%; text-align: center; padding: 3px;'>\n";
			$html.="<h3 class='see_all_h3'>$tab_dates[$k]</h3>\n";
			$html.="</td>\n";

			if(($ne_pas_afficher_colonne_vide!='y')||(($ne_pas_afficher_colonne_vide=='y')&&(count($tab_dev)>0))) {
				//$html.="<td class='see_all_notice couleur_bord_tableau_notice color_fond_notices_t' style='width:40%; text-align:left; padding: 3px;'>\n";
				$html.="<td id='td_colonne_t_$k' style='width:40%; text-align:left; padding: 3px;'>\n";
				if(isset($tab_dev[$tab_dates[$k]])) {
					foreach($tab_dev[$tab_dates[$k]] as $key => $value) {
						// A VOIR: PB avec les bordures.
						$html.="<div id='conteneur_notice_t_".$value['id_ct']."' class='see_all_notice couleur_bord_tableau_notice color_fond_notices_t' style='margin: 1px; padding: 1px; border: 1px solid black; width: 99%;'>";
							$html.="<div class='noprint' style='float:right;width:3em;'><a href=\"javascript:ajouter_contenu_notice_a_ma_selection('contenu_notice_t_".$value['id_ct']."')\" title='Ajouter le contenu de la notice à ma sélection'><img src='../images/icons/add.png' width='16' height='16' /></a> <a href=\"javascript:afficher_contenu_selection()\" title='Voir le  contenu de ma sélection'><img src='../images/icons/chercher.png' width='16' height='16' /></a></div>\n";
							$html.="<div id='contenu_notice_t_".$value['id_ct']."'>\n";
								$contenu_notice_courante=$value['contenu'];
								$adj=my_affiche_docs_joints($value['id_ct'],"t");
								if($adj!='') {
									$contenu_notice_courante.="<div style='border: 1px dashed black'>\n";
									$contenu_notice_courante.=$adj;
									$contenu_notice_courante.="</div>\n";
	
									if($dossier_documents!='') {
										$tab_documents_joints=my_tab_docs_joints($value['id_ct'],"t");
										my_transfert_docs_joints($tab_documents_joints,$dossier_documents,$mode);
									}
								}
								$html.=$contenu_notice_courante;
							$html.="</div>\n";
						$html.="</div>\n";
					}
				}
				else {
					$html.="&nbsp;\n";
				}
				$html.="</td>\n";
			}

			if(($ne_pas_afficher_colonne_vide!='y')||(($ne_pas_afficher_colonne_vide=='y')&&(count($tab_notices)>0))) {
				//$html.="<td class='see_all_notice couleur_bord_tableau_notice color_fond_notices_c' style='width:40%; text-align:left; padding: 3px;'>\n";
				$html.="<td id='td_colonne_c_$k' style='width:40%; text-align:left; padding: 3px;'>\n";
				if(isset($tab_notices[$tab_dates[$k]])) {
					foreach($tab_notices[$tab_dates[$k]] as $key => $value) {
						$html.="<div id='conteneur_notice_c_".$value['id_ct']."' class='see_all_notice couleur_bord_tableau_notice color_fond_notices_c' style='margin: 1px; padding: 1px; border: 1px solid black; width: 99%;'>";
							$html.="<div class='noprint' style='float:right;width:3em;'><a href=\"javascript:ajouter_contenu_notice_a_ma_selection('contenu_notice_c_".$value['id_ct']."')\" title='Ajouter le contenu de la notice à ma sélection'><img src='../images/icons/add.png' width='16' height='16' /></a> <a href=\"javascript:afficher_contenu_selection()\" title='Voir le  contenu de ma sélection'><img src='../images/icons/chercher.png' width='16' height='16' /></a></div>\n";
							$html.="<div id='contenu_notice_c_".$value['id_ct']."'>\n";
								$html.=$value['contenu'];
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
						$html.="</div>\n";
					}
				}
				else {
					$html.="&nbsp;\n";
				}
				$html.="</td>\n";
			}
			$html.="</tr>\n";

			//$html.="<div style='clear:both;'></div>\n";

		}
		$html.="</table>\n";

		return $html;
	}

	function my_affiche_docs_joints($id_ct, $type_notice) {
		global $tab_chemin_url;
		global $action;

		if((isset($action))&&($action=='acces')) {
			$pref_documents="../../../";
		}
		elseif((isset($action))&&($action=='acces_dynamique')) {
			$pref_documents="../";
		}
		else {
			$pref_documents="";
		}

		$nb_doc_joints_visibles=0;

		// documents joints
		$html = '';
		//$architecture="/documents/cl_dev";
		if ($type_notice == "t") {
			$sql = "SELECT titre, emplacement, visible_eleve_parent FROM ct_devoirs_documents WHERE id_ct_devoir='$id_ct' ORDER BY 'titre'";
		} else if ($type_notice == "c") {
			$sql = "SELECT titre, emplacement, visible_eleve_parent FROM ct_documents WHERE id_ct='$id_ct' ORDER BY 'titre'";
		}
		$res = sql_query($sql);
		if (($res) and (sql_count($res)!=0)) {
			$html_tmp= "<span class='petit'>Document(s) joint(s):</span>";
			//$html_tmp.= "<br />\$pref_documents=$pref_documents";
			$html_tmp.= "<ul style=\"padding-left: 15px;\">";
			for ($i=0; ($row = sql_row($res,$i)); $i++) {
				if(isset($_SESSION['statut']) && ((($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable'))||
					((getSettingValue('cdt_possibilite_masquer_pj')!='y')&&(($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable')))||
					((getSettingValue('cdt_possibilite_masquer_pj')=='y')&&($row[2]==true)&&(($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable'))))
				) {
					$titre = $row[0];
					$emplacement = $pref_documents.$row[1];
					//$html_tmp.= "<li style=\"padding: 0px; margin: 0px; font-size: 80%;\"><a onclick=\"window.open(this.href, '_blank'); return false;\" href=\"$emplacement\">$titre</a></li>";
					$html_tmp.= "<li style=\"padding: 0px; margin: 0px; font-size: 80%;\"><a href=\"$emplacement\" target=\"_blank\">$titre</a></li>";

					$tab_chemin_url[]=$emplacement;
					$nb_doc_joints_visibles++;
				}
			}
			$html_tmp .= "</ul>";
		}

		if($nb_doc_joints_visibles>0) {$html.=$html_tmp;}

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
				//$res=creer_rep_docs_joints($dossier_documents, $dossier_courant, "../../../../../..");
				$res=creer_rep_docs_joints($dossier_documents, $dossier_courant);
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
					elseif(!$res) {
							echo "<span style='color:red; margin-left: 3em;'>Il semble que la copie du fichier $tab_documents_joints[$loop] vers $dossier_documents/$dossier_courant/$fichier_courant ait échoué.</span><br />\n";
							$temoin_erreur="y";
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

	//function creer_index_logout($path, $pref_arbo_logout) {
	function creer_index_logout($path) {
		// $pref_arbo_logout n'est plus utilisé
		global $gepiPath;

		if (!file_exists($path)) {return false;}
		else {
			$ok = false;
			if ($f = @fopen("$path/.test", "w")) {
				@fputs($f, '<'.'?php $ok = true; ?'.'>');
				@fclose($f);
				include("$path/.test");
				if($ok) {
					if ($f = @fopen("$path/index.html", "w")) {
						//@fputs($f, '<script type="text/javascript">document.location.replace("'.$pref_arbo_logout.'/logout.php")</script>');
						@fputs($f, '<script type="text/javascript">document.location.replace("'.$gepiPath.'/logout.php?auto=1")</script>');
						@fclose($f);
					}
				}
			}
			return $ok;
		}
	}

	//function creer_rep_docs_joints($base, $subdir, $pref_arbo_logout) {
	function creer_rep_docs_joints($base, $subdir) {
		// $pref_arbo_logout n'est plus utilisé
		global $gepiPath;

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
					//@fputs($f, '<script type="text/javascript">document.location.replace("'.$pref_arbo_logout.'/login.php")</script>');
					@fputs($f, '<script type="text/javascript">document.location.replace("'.$gepiPath.'/logout.php?auto=1")</script>');
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

	function get_dossier_docs_joints_cdt() {
		global $multisite;

		$dossier="";

		if(((isset($multisite))&&($multisite=='y'))||(getSettingValue('multisite')=='y')) {
			if(isset($_COOKIE['RNE'])) {
				$dossier="../documents/".$_COOKIE['RNE'];
			}
		}
		else {
			$dossier="../documents";
		}

		return $dossier;
	}

	/*
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
	*/

	function arbo_export_cdt($nom_export, $dirname) {
		global $dossier_export; // Pour récupérer la valeur hors de la fonction
		global $tab_fichiers_a_zipper; // Pour récupérer le tableau de valeurs hors de la fonction
		global $action;

		if((isset($action))&&($action=='acces')) {
			$dossier_export="../documents/".$dirname."/".$nom_export;
		}
		else {
			$dossier_export="../temp/".$dirname."/".$nom_export;
		}

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

	}

	function enregistrement_creation_acces_cdt($chemin, $description_acces, $date1_acces, $date2_acces, $id_groupe) {
		if(count($id_groupe)==0) {
			echo "<p style='color:red'>Aucun groupe n'a été sélectionné.</p>\n";
			return false;
		}
		else {
			$sql="CREATE TABLE IF NOT EXISTS acces_cdt (id INT(11) NOT NULL auto_increment,
					description TEXT NOT NULL,
					chemin VARCHAR(255) NOT NULL DEFAULT '',
					date1 DATETIME NOT NULL default '0000-00-00 00:00:00',
					date2 DATETIME NOT NULL default '0000-00-00 00:00:00',
					PRIMARY KEY (id)
					) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
			$create_table=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$create_table) {
				echo "<p style='color:red'>Erreur lors de la création de la table 'acces_cdt':<br />$sql</p>\n";
				return false;
			}
			else {
				$sql="CREATE TABLE IF NOT EXISTS acces_cdt_groupes (id INT(11) NOT NULL auto_increment,
						id_acces INT(11) NOT NULL,
						id_groupe INT(11) NOT NULL,
						PRIMARY KEY (id)
						) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
				$create_table=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$create_table) {
					echo "<p style='color:red'>Erreur lors de la création de la table 'acces_cdt_groupes':<br />$sql</p>\n";
					return false;
				}
				else {
					$sql="INSERT INTO acces_cdt SET description='".addslashes($description_acces)."', chemin='$chemin', date1='$date1_acces', date2='$date2_acces';";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$insert) {
						echo "<p style='color:red'>Erreur lors de la création de l'enregistrement dans la table 'acces_cdt':<br />$sql</p>\n";
						return false;
					}
					else {
						$id_acces=((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
	
						$retour=true;
						for($loop=0;$loop<count($id_groupe);$loop++) {
							$sql="INSERT INTO acces_cdt_groupes SET id_acces='$id_acces', id_groupe='$id_groupe[$loop]';";
							$insert=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$insert) {
								echo "<p style='color:red'>Erreur lors de la création de l'enregistrement dans la table 'acces_cdt_groupes'&nbsp;:<br />$sql</p>\n";
								$retour=false;
							}
						}

						return $retour;
					}
				}
			}
		}
	}

	function get_dates_notices_et_dev($id_groupe, $date_debut, $date_fin, $timestamp_debut, $timestamp_fin, $avec_notices="y", $avec_dev="y") {
		// Passer en paramètres $date_debut et $date_fin (au format jj/mm/aaa) ou bien directement les $timestamp_debut et $timestamp_fin

		global $current_ordre;

		$tab_dates=array();
		$tab_dates2=array();
	
		$tab_notices=array();
		$tab_dev=array();

		if($timestamp_debut=="") {
			$tmp_tab=explode("/",$date_debut);
			$jour=$tmp_tab[0];
			$mois=$tmp_tab[1];
			$annee=$tmp_tab[2];
			$timestamp_debut=mktime(0,0,0,$mois,$jour,$annee);
		}

		if($timestamp_fin=="") {
			$tmp_tab=explode("/",$date_fin);
			$jour=$tmp_tab[0];
			$mois=$tmp_tab[1];
			$annee=$tmp_tab[2];
			$timestamp_fin=mktime(0,0,0,$mois,$jour,$annee);
		}

		if($avec_notices=="y") {
			$sql="SELECT cte.* FROM ct_entry cte WHERE (contenu != ''
				AND date_ct != ''
				AND date_ct >= '".$timestamp_debut."'
				AND date_ct <= '".$timestamp_fin."'
				AND id_groupe='".$id_groupe."'
				) ORDER BY date_ct DESC, heure_entry DESC;";
			//echo "$sql<br />";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			$cpt=0;
			while($lig=mysqli_fetch_object($res)) {
		
				//echo "$lig->date_ct<br />";
				$date_notice=my_strftime("%a %d %b %y", $lig->date_ct);
				if(!in_array($date_notice,$tab_dates)) {
					$tab_dates[]=$date_notice;
					$tab_dates2[]=$lig->date_ct;
				}
				$tab_notices[$date_notice][$cpt]['id_ct']=$lig->id_ct;
				$tab_notices[$date_notice][$cpt]['id_login']=$lig->id_login;
				$tab_notices[$date_notice][$cpt]['contenu']=$lig->contenu;
				$tab_notices[$date_notice][$cpt]['date_ct']=$lig->date_ct;
				//echo " <span style='color:red'>\$tab_notices[$date_notice][$cpt]['contenu']=$lig->contenu</span><br />";
				$cpt++;
			}
		}


		if($avec_dev=="y") {
			$sql="SELECT ctd.* FROM ct_devoirs_entry ctd WHERE (contenu != ''
				AND date_ct != ''
				AND date_ct >= '".$timestamp_debut."'
				AND date_ct <= '".$timestamp_fin."'
				AND id_groupe='".$id_groupe."'
				) ORDER BY date_ct DESC;";
			//echo "$sql<br />";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			$cpt=0;
			$timestamp_courant=time();
			while($lig=mysqli_fetch_object($res)) {
				if(($lig->date_visibilite_eleve=="")||
				(($lig->date_visibilite_eleve!="")&&(mysql_date_to_unix_timestamp($lig->date_visibilite_eleve)<=$timestamp_courant))||
				(verif_groupe_appartient_prof($lig->id_groupe)==1)) {
					//echo "$lig->date_ct<br />";
					$date_dev=my_strftime("%a %d %b %y", $lig->date_ct);
					if(!in_array($date_dev,$tab_dates)) {
						$tab_dates[]=$date_dev;
						$tab_dates2[]=$lig->date_ct;
					}
					$tab_dev[$date_dev][$cpt]['id_ct']=$lig->id_ct;
					$tab_dev[$date_dev][$cpt]['id_login']=$lig->id_login;
					$tab_dev[$date_dev][$cpt]['contenu']=$lig->contenu;
					$tab_dev[$date_dev][$cpt]['special']=$lig->special;
					$tab_dev[$date_dev][$cpt]['date_ct']=$lig->date_ct;
					//echo " <span style='color:green'>\$tab_dev[$date_dev][$cpt]['contenu']=$lig->contenu</span><br />";
					$cpt++;
				}
			}
		}

		//echo "\$current_ordre=$current_ordre<br />";
		//sort($tab_dates);
		if($current_ordre=='ASC') {
			array_multisort ($tab_dates, SORT_DESC, SORT_NUMERIC, $tab_dates2, SORT_ASC, SORT_NUMERIC);
		}
		else {
			array_multisort ($tab_dates, SORT_ASC, SORT_NUMERIC, $tab_dates2, SORT_DESC, SORT_NUMERIC);
		}

		return array($tab_dates, $tab_notices, $tab_dev);
	}

	function devoirs_tel_jour($id_classe, $date_jour, $afficher_enseignement_sans_devoir="y") {
		global $color_fond_notices, $gepiPath;

		$dossier_documents="";
		$mode="";

		$tmp_tab=explode("/",$date_jour);
		$jour=$tmp_tab[0];
		$mois=$tmp_tab[1];
		$annee=$tmp_tab[2];
		$timestamp_debut=mktime(0,0,0,$mois,$jour,$annee);
		$timestamp_fin=$timestamp_debut+24*3600-1;

		$hier=$timestamp_debut-24*3600;
		$demain=$timestamp_debut+24*3600;
		$retour="<div style='float:right; width: 35px;'>\n";
		$retour.="<a href=\"javascript: getWinDevoirsDeLaClasse().setAjaxContent('./ajax_devoirs_classe.php?id_classe=$id_classe&today='+$hier)\" title=\"Jour précédent\"><img src='../images/icons/back.png' width='16' height='16' alt='Jour précédent' /></a>\n";
		$retour.="<a href=\"javascript: getWinDevoirsDeLaClasse().setAjaxContent('./ajax_devoirs_classe.php?id_classe=$id_classe&today='+$demain)\" title=\"Jour suivant\"><img src='../images/icons/forward.png' width='16' height='16' alt='Jour suivant' /></a>\n";
		$retour.="</div>\n";

		$retour.="<p class='bold'>";
		// Jour précédents... comment gérer les liens selon que c'est affiché en infobulle ou en page classique
		$retour.=get_class_from_id($id_classe)."&nbsp;: Travaux à faire pour le $date_jour";
		// Jour suivant
		$retour.="</p>\n";
		$retour.="<table class='boireaus' style='margin:3px;' border='1' summary='CDT'>\n";
		// Boucle sur les groupes de la classe
		//$groups=get_groups_for_clas($id_classe);

		$sql="select g.id from groupes g, j_groupes_classes j where (g.id = j.id_groupe and j.id_classe = '" . $id_classe . "') ORDER BY j.priorite, g.name";
		//echo "$sql<br />";
		$query=mysqli_query($GLOBALS["mysqli"], $sql);
		$tab_champs=array('classes', 'profs');
		$alt=1;
		$cpt=0;
		while($lig=mysqli_fetch_object($query)) {
			$current_group=get_group($lig->id, $tab_champs);
			$id_groupe=$current_group['id'];

			unset($tmp_tab);
			unset($tab_dates);
			unset($tab_notices);
			unset($tab_dev);

			$tmp_tab=get_dates_notices_et_dev($id_groupe, "", "", $timestamp_debut, $timestamp_fin, "y", "y");
			$tab_dates=$tmp_tab[0];
			$tab_notices=$tmp_tab[1];
			$tab_dev=$tmp_tab[2];
			unset($tmp_tab);

			if(($afficher_enseignement_sans_devoir=="y")||(count($tab_dev)>0)) {
				$retour.="";

					$alt=$alt*(-1);
					$retour.="<tr class='lig$alt'>\n";
					$retour.="<td style='width:12%; text-align: center; padding: 3px;'>\n";
					$retour.="<h3 class='see_all_h3'>".$current_group['name']."</h3>\n";
					//$retour.="<br />\n";
					$retour.="(<span style='font-size:small; font-variant:italic; '>".$current_group['description']."</span>)<br /><span style='font-size:small;'>".$current_group["profs"]["proflist_string"]."</span>\n";
					$retour.="</td>\n";

					$retour.="<td style='width:40%; text-align:left; padding: 3px;'>\n";
					for($k=0;$k<count($tab_dates);$k++) {
						if(isset($tab_dev[$tab_dates[$k]])) {
							foreach($tab_dev[$tab_dates[$k]] as $key => $value) {
								$retour.="<div class='see_all_notice couleur_bord_tableau_notice color_fond_notices_t' style='margin: 1px; padding: 1px; border: 1px solid black; width: 99%; background-color:".$color_fond_notices['t']."'>";

								if($value['special']=="controle") {
									$retour.="<div style='float:right; width:16px;'><img src='$gepiPath/images/icons/flag2.gif' class='icone16' alt='Contrôle' title=\"Un contrôle/évaluation est programmé pour le ".my_strftime("%A %d/%m/%Y", $value['date_ct'])."\" /></div>";
								}

								$retour.=$value['contenu'];
								$adj=my_affiche_docs_joints($value['id_ct'],"t");
								if($adj!='') {
									$retour.="<div style='border: 1px dashed black'>\n";
									$retour.=$adj;
									$retour.="</div>\n";
								}
								$retour.="</div>\n";
							}
							$cpt++;
						}
						else {
							$retour.="&nbsp;\n";
						}
					}
					$retour.="</td>\n";
					$retour.="</tr>\n";
			}
		}
		$retour.="</table>\n";

		if($cpt==0) {$retour.="<p>Aucun travail n'est (<i>encore</i>) demandé pour cette date.</p>\n";}

		return $retour;

	}

	function affiche_notice_privee_groupe_jour($id_groupe, $date_jour) {
		$retour="";

		$timestamp_debut=timestamp_from_date_jour($date_jour);
		$timestamp_fin=$timestamp_debut+24*3600-1;

		// Date de la notice précédente/suivante
		$precedent="";
		$suivant="";

		$sql="SELECT * FROM ct_private_entry WHERE id_groupe='$id_groupe' AND contenu != ''
				AND date_ct != ''
				AND date_ct < '".$timestamp_debut."'
				ORDER BY date_ct DESC;";
		//echo "$sql<br />\n";
		$res_prec=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_prec)>0) {
			$lig=mysqli_fetch_object($res_prec);
			$precedent=$lig->date_ct;
		}
		/*
		echo "\$precedent&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;=$precedent&nbsp;&nbsp;";
		if($precedent!="") {echo date_from_timestamp($precedent);}
		echo "<br />";
		*/

		$sql="SELECT * FROM ct_private_entry WHERE id_groupe='$id_groupe' AND contenu != ''
				AND date_ct != ''
				AND date_ct > '".$timestamp_fin."'
				ORDER BY date_ct ASC;";
		//echo "$sql<br />\n";
		$res_suiv=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_suiv)>0) {
			$lig=mysqli_fetch_object($res_suiv);
			$suivant=$lig->date_ct;
		}
		/*
		echo "\$suivant&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;=$suivant&nbsp;&nbsp;";
		if($suivant!="") {echo date_from_timestamp($suivant);}
		echo "<br />";
		*/

		if(($suivant!='')||($precedent!='')) {
			$retour.="<div style='float: left; width: 20px; margin-top: 0.5em;'>\n";
			if($precedent!='') {
				$retour.="<a href=\"javascript: getWinListeNoticesPrivees().setAjaxContent('./ajax_liste_notices_privees.php?id_groupe=$id_groupe&today=$precedent');\" title=\"Notice privée précédente\"><img src='../images/up.png' width='18' height='18' /></a>";
				$retour.="<br />\n";
			}
			else {
				$retour.="<div style='width: 18px; height: 18px;'>&nbsp;</div>\n";
			}

			if($suivant!='') {
				$retour.="<a href=\"javascript: getWinListeNoticesPrivees().setAjaxContent('./ajax_liste_notices_privees.php?id_groupe=$id_groupe&today=$suivant');\" title=\"Notice privée suivante\"><img src='../images/down.png' width='18' height='18' /></a>";
			}
			$retour.="</div>\n";
		}


		$tab_champs=array('classes');
		$current_group=get_group($id_groupe, $tab_champs);
		$info_groupe=$current_group['name']." (<em>".$current_group['description']."</em>) en ".$current_group['classlist_string'];
		$sql="SELECT * FROM ct_private_entry WHERE id_groupe='$id_groupe' AND contenu != ''
				AND date_ct != ''
				AND date_ct >= '".$timestamp_debut."'
				AND date_ct <= '".$timestamp_fin."'
				ORDER BY date_ct;";
		//echo "$sql<br />\n";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {

			while($lig=mysqli_fetch_object($res)) {
				$notice_privee = CahierTexteNoticePriveePeer::retrieveByPK($lig->id_ct);

				//$retour.="<div style='border: 1px solid black; margin: 0.5em; background-color:".$couleur_cellule['p']."'>\n";
				$retour.="<div style='border: 1px solid black; margin: 0.5em; margin-left:25px; background-color: #f6f3a8'>\n";
				$retour.="<div style='float: right; width: 4em; margin-right: 2em;'>".date_from_timestamp($lig->date_ct)."</div>\n";

				if(my_strtoupper($lig->id_login)==my_strtoupper($_SESSION['login'])) {
					$retour .= '<div style="margin: 0px; float: left;">';
					$retour .=("<a href=\"#\" onclick=\"javascript:
						id_groupe = '".$lig->id_groupe."';
						getWinEditionNotice().setAjaxContent('ajax_edition_notice_privee.php?id_ct=".$lig->id_ct."',{ onComplete: function() {	initWysiwyg();}});
						updateCalendarWithUnixDate(".$lig->date_ct.");
						getWinListeNotices();
						new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=".$lig->id_groupe."',{ onComplete:function() {updateDivModification();}});
						object_en_cours_edition = 'notice_privee';
					");
					$retour .=("\">");
					$retour .=("<img style=\"border: 0px;\" src=\"../images/edit16.png\" alt=\"modifier\" title=\"modifier\" /></a>\n");
					$retour .=(" ");

					if ($notice_privee != null) {
						$retour .=("<a href=\"#\" onclick=\"javascript:
							contenu_a_copier = '".addslashes(htmlspecialchars($lig->contenu))."';
							ct_a_importer_class='".get_class($notice_privee)."';
							id_ct_a_importer='".$lig->id_ct."';
							new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=".$lig->id_groupe."&ct_a_importer_class=".get_class($notice_privee)."&id_ct_a_importer=".$lig->id_ct."',{ onComplete:function() {updateDivModification();} });
							getWinListeNoticesPrivees().setAjaxContent('./ajax_liste_notices_privees.php?id_groupe=".$lig->id_groupe."&today=$lig->date_ct');
							\"><img style=\"border: 0px;\" src=\"");
						if (isset($_SESSION['ct_a_importer']) && $_SESSION['ct_a_importer'] == $notice_privee) {
							$retour .=("../images/icons/copy-16-gold.png");
						} else {
							$retour .=("../images/icons/copy-16.png");
						}
						$retour .=("\" alt=\"Copier\" title=\"Copier\" /></a>\n");
					}
					$retour .=(" ");

					$retour .=("<a href=\"#\" onclick=\"javascript:
					suppressionNoticePrivee('".my_strftime("%A %d %B %Y", $lig->date_ct)."','".$lig->id_ct."', '".$lig->id_groupe."','".add_token_in_js_func()."');
					new Ajax.Updater('affichage_derniere_notice', 'ajax_affichage_dernieres_notices.php', {onComplete : function () {updateDivModification();}});
					getWinListeNoticesPrivees().setAjaxContent('./ajax_liste_notices_privees.php?id_groupe=".$lig->id_groupe."&today=all');
					return false;
					\"><img style=\"border: 0px;\" src=\"../images/delete16.png\" alt=\"supprimer\" title=\"supprimer\" /></a>\n");
					$retour .= '</div>';
				}

				$retour.=$lig->contenu;
				$retour.="</div>\n";
			}
		}
		else {$retour.="Aucune Notice Privée pour cet enseignement (".$info_groupe.") le $date_jour.";}

		return $retour;
	}

	function affiche_toutes_notices_privees_groupe($id_groupe) {
		$retour="";

		$tab_champs=array('classes');
		$current_group=get_group($id_groupe, $tab_champs);
		$info_groupe=$current_group['name']." (<em>".$current_group['description']."</em>) en ".$current_group['classlist_string'];
		$sql="SELECT * FROM ct_private_entry WHERE id_groupe='$id_groupe' AND contenu != ''
				AND date_ct != ''
				ORDER BY date_ct;";
		//echo "$sql<br />\n";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {

			$id_ct_np_prec="";
			while($lig=mysqli_fetch_object($res)) {
				$notice_privee = CahierTexteNoticePriveePeer::retrieveByPK($lig->id_ct);

				//$retour.="<div style='border: 1px solid black; margin: 0.5em; background-color:".$couleur_cellule['p']."'>\n";
				$retour.="<a name='liste_NP_notice_privee_".$lig->id_ct."'></a>\n";
				$retour.="<div style='border: 1px solid black; margin: 0.5em; margin-left:25px; background-color: #f6f3a8'>\n";
				$retour.="<div style='float: right; width: 4em; margin-right: 2em;'>".date_from_timestamp($lig->date_ct)."</div>\n";

				if(my_strtoupper($lig->id_login)==my_strtoupper($_SESSION['login'])) {
					$retour .= '<div style="margin: 0px; float: left;">';
					$retour .=("<a href=\"#\" onclick=\"javascript:
						id_groupe = '".$lig->id_groupe."';
						getWinEditionNotice().setAjaxContent('ajax_edition_notice_privee.php?id_ct=".$lig->id_ct."',{ onComplete: function() {	initWysiwyg();}});
						updateCalendarWithUnixDate(".$lig->date_ct.");
						getWinListeNotices();
						new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=".$lig->id_groupe."',{ onComplete:function() {updateDivModification();}});
						object_en_cours_edition = 'notice_privee';
					");
					$retour .=("\">");
					$retour .=("<img style=\"border: 0px;\" src=\"../images/edit16.png\" alt=\"modifier\" title=\"modifier\" /></a>\n");
					$retour .=(" ");

					if ($notice_privee != null) {
						$retour .=("<a href=\"#\" onclick=\"javascript:
							contenu_a_copier = '".addslashes(htmlspecialchars($lig->contenu))."';
							ct_a_importer_class='".get_class($notice_privee)."';
							id_ct_a_importer='".$lig->id_ct."';
							new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=".$lig->id_groupe."&ct_a_importer_class=".get_class($notice_privee)."&id_ct_a_importer=".$lig->id_ct."',{ onComplete:function() {updateDivModification();} });
							getWinListeNoticesPrivees().setAjaxContent('./ajax_liste_notices_privees.php?id_groupe=".$lig->id_groupe."&today=all#liste_NP_notice_privee_".$id_ct_np_prec."');
							\"><img style=\"border: 0px;\" src=\"");
						if (isset($_SESSION['ct_a_importer']) && $_SESSION['ct_a_importer'] == $notice_privee) {
							$retour .=("../images/icons/copy-16-gold.png");
						} else {
							$retour .=("../images/icons/copy-16.png");
						}
						$retour .=("\" alt=\"Copier\" title=\"Copier\" /></a>\n");
					}
					$retour .=(" ");

					$retour .=("<a href=\"#\" onclick=\"javascript:
					suppressionNoticePrivee('".my_strftime("%A %d %B %Y", $lig->date_ct)."','".$lig->id_ct."', '".$lig->id_groupe."','".add_token_in_js_func()."');
					new Ajax.Updater('affichage_derniere_notice', 'ajax_affichage_dernieres_notices.php', {onComplete : function () {updateDivModification();}});
					getWinListeNoticesPrivees().setAjaxContent('./ajax_liste_notices_privees.php?id_groupe=".$lig->id_groupe."&today=all#liste_NP_notice_privee_".$id_ct_np_prec."');
					return false;
					\"><img style=\"border: 0px;\" src=\"../images/delete16.png\" alt=\"supprimer\" title=\"supprimer\" /></a>\n");
					$retour .= '</div>';
				}

				$retour.=$lig->contenu;
				$retour.="</div>\n";

				$id_ct_np_prec=$lig->id_ct;
			}
		}
		else {$retour.="Aucune Notice Privée pour cet enseignement (".$info_groupe.").";}

		return $retour;
	}

	function date_from_timestamp($timestamp) {
		return strftime("%d/%m/%Y", $timestamp);
	}

	function timestamp_from_date_jour($date_jour) {
		$tmp_tab=explode("/",$date_jour);
		$jour=$tmp_tab[0];
		$mois=$tmp_tab[1];
		$annee=$tmp_tab[2];
		$timestamp=mktime(0,0,0,$mois,$jour,$annee);
		return $timestamp;
	}

	function get_liste_dates_np($id_groupe) {
		$tab_dates=array();

		$sql="SELECT * FROM ct_private_entry WHERE id_groupe='$id_groupe' AND contenu != ''
				AND date_ct != ''
				ORDER BY date_ct;";
		echo "$sql<br />\n";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			while($lig=mysqli_fetch_object($res)) {
				$tab_dates[]=$lig->date_ct;
			}
		}

		return $tab_dates;
	}

	// Archivage des images de formules mathématiques insérées dans les notices
	// (téléchargées dans les docs joints, mais sans enregistrement dans les tables ct_*documents,
	// elles n'étaient pas copiées)
	function archiver_images_formules_maths($id_groupe) {
		global $dossier_documents;

		// A vérifier: chemin source dans le cas multisite?
		if(file_exists("../documents/cl".$id_groupe)) {
			if(!file_exists($dossier_documents)) {
				//echo "Création de $dossier_documents<br />";
				mkdir($dossier_documents, 0777);
				@chmod($dossier_documents, 0777);
			}

			$destination=$dossier_documents."/cl".$id_groupe;
			if(!file_exists($destination)) {
				//echo "Création de $destination<br />";
				mkdir($destination, 0777);
				// Sans le chmod, je me retrouve avec des droits bizarres ne permettant pas de copier les fichiers:
				// dr----x--t 2 www-data www-data 4,0K nov.   1 15:20 cl2675
				@chmod($destination, 0777);
			}

			if(file_exists($destination)) {
				// Parcours des fichiers "joints":
				$handle=opendir("../documents/cl".$id_groupe);
				$n=0;
				while ($file = readdir($handle)) {
					if((preg_match("/^[0-9]{8}_[0-9]{6}.gif$/", $file))||
					(preg_match("/^[0-9]{8}_[0-9]{6}.png$/", $file))||
					(preg_match("/^[0-9]{8}_[0-9]{6}.emf$/", $file))||
					(preg_match("/^[0-9]{8}_[0-9]{6}.swf$/", $file))||
					(preg_match("/^[0-9]{8}_[0-9]{6}.svg$/", $file))||
					(preg_match("/^[0-9]{8}_[0-9]{6}.pdf$/", $file))) {
						//echo "copy(\"../documents/cl\".$id_groupe.\"/\".$file, $destination.\"/\".$file);<br />";
						copy("../documents/cl".$id_groupe."/".$file, $destination."/".$file);
						//echo "'".$file."'<br />";
						$n++;
					}
				}
				closedir($handle);
			}
		}

		if(file_exists("../documents/cl_dev".$id_groupe)) {
			if(!file_exists($dossier_documents)) {
				mkdir($dossier_documents, 0777);
				@chmod($dossier_documents, 0777);
			}

			$destination=$dossier_documents."/cl_dev".$id_groupe;
			if(!file_exists($destination)) {
				mkdir($destination, 0777);
				@chmod($destination, 0777);
			}

			if(file_exists($destination)) {
				// Parcours des fichiers "joints":
				$handle=opendir("../documents/cl_dev".$id_groupe);
				$n=0;
				while ($file = readdir($handle)) {
					if((preg_match("/^[0-9]{8}_[0-9]{6}.gif$/", $file))||
					(preg_match("/^[0-9]{8}_[0-9]{6}.png$/", $file))||
					(preg_match("/^[0-9]{8}_[0-9]{6}.emf$/", $file))||
					(preg_match("/^[0-9]{8}_[0-9]{6}.swf$/", $file))||
					(preg_match("/^[0-9]{8}_[0-9]{6}.svg$/", $file))||
					(preg_match("/^[0-9]{8}_[0-9]{6}.pdf$/", $file))) {
						copy("../documents/cl_dev".$id_groupe."/".$file, $destination."/".$file);
						$n++;
					}
				}
				closedir($handle);
			}
		}
	}
?>
