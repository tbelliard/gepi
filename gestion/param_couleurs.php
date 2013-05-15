<?php
/*
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// INSERT INTO droits VALUES ('/gestion/param_couleurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Définition des couleurs pour Gepi', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

$tab_panneau_affichage=array('fil_blanc','fil_noir','fil_bleu','fil_vert','filet_noir','filet_noir_2','liege');

// Liste des composantes
$comp=array('R','V','B');

function hex2nb($carac) {
	switch(mb_strtoupper($carac)) {
		case "A":
			return 10;
			break;
		case "B":
			return 11;
			break;
		case "C":
			return 12;
			break;
		case "D":
			return 13;
			break;
		case "E":
			return 14;
			break;
		case "F":
			return 15;
			break;
		default:
			return $carac;
			break;
	}
}

function tab_rvb($couleur) {
	$compR=mb_substr($couleur,0,2);
	$compV=mb_substr($couleur,2,2);
	$compB=mb_substr($couleur,4,2);

	//echo "\$compR=$compR<br />";
	//echo "\$compV=$compV<br />";
	//echo "\$compB=$compB<br />";

	$tabcomp=array();

	$tabcomp['R']=hex2nb(mb_substr($compR,0,1))*16+hex2nb(mb_substr($compR,1,1));
	$tabcomp['V']=hex2nb(mb_substr($compV,0,1))*16+hex2nb(mb_substr($compV,1,1));
	$tabcomp['B']=hex2nb(mb_substr($compB,0,1))*16+hex2nb(mb_substr($compB,1,1));

	return $tabcomp;
}


/*
function genere_degrade($couleur_haut,$couleur_bas,$hauteur,$chemin_img) {
	//$hauteur=100;

	$im=imagecreate(1,$hauteur);

	$comp=array('R','V','B');

	$tab_haut=array();
	$tab_haut=tab_rvb($couleur_haut);

	$tab_bas=array();
	$tab_bas=tab_rvb($couleur_bas);

	for($x=0;$x<$hauteur;$x++) {
		$ratio=array();
		for($i=0;$i<count($comp);$i++) {
			$ratio[$comp[$i]]=$tab_haut[$comp[$i]]+$x*($tab_bas[$comp[$i]]-$tab_haut[$comp[$i]])/$hauteur;
		}
		$color=imagecolorallocate($im,$ratio['R'],$ratio['V'],$ratio['B']);
		imagesetpixel($im,0,$x,$color);
	}
	imagepng($im,$chemin_img);
}
*/
function genere_degrade($couleur_haut,$couleur_bas,$hauteur,$chemin_img,$mode="") {
	//$hauteur=100;

	$debug_img="n";
	if($debug_img=="n") {
		$im=imagecreate(1,$hauteur);
	}
	else {
		$im=imagecreate(100,$hauteur);
	}

	$comp=array('R','V','B');

	$tab_haut=array();
	$tab_haut=tab_rvb($couleur_haut);

	$tab_bas=array();
	$tab_bas=tab_rvb($couleur_bas);

	if($mode=="") {
		for($x=0;$x<$hauteur;$x++) {
			$ratio=array();
			for($i=0;$i<count($comp);$i++) {
				$ratio[$comp[$i]]=$tab_haut[$comp[$i]]+$x*($tab_bas[$comp[$i]]-$tab_haut[$comp[$i]])/$hauteur;
			}
			$color=imagecolorallocate($im,$ratio['R'],$ratio['V'],$ratio['B']);
			if($debug_img=="n") {
				imagesetpixel($im,0,$x,$color);
			}
			else {
				for($j=0;$j<100;$j++) {
					imagesetpixel($im,$j,$x,$color);
				}
			}
		}
	}
	else {
		$y=round(2*$hauteur/3);

		for($x=0;$x<$y;$x++) {
			$ratio=array();
			for($i=0;$i<count($comp);$i++) {
				//$ratio[$comp[$i]]=$tab_haut[$comp[$i]]+$x*($tab_bas[$comp[$i]]-$tab_haut[$comp[$i]])/$hauteur;
				$ratio[$comp[$i]]=$tab_bas[$comp[$i]]+($y-$x)*($tab_haut[$comp[$i]]-$tab_bas[$comp[$i]])/$y;
				//if($i==0) {echo "\$ratio[$comp[$i]]=\$tab_bas[$comp[$i]]+($y-$x)*(\$tab_haut[$comp[$i]]-\$tab_bas[$comp[$i]])/$y=".$tab_bas[$comp[$i]]."+($y-$x)*(".$tab_haut[$comp[$i]]."-".$tab_bas[$comp[$i]].")/$y=".$ratio[$comp[$i]]."<br />";}
			}
			$color=imagecolorallocate($im,$ratio['R'],$ratio['V'],$ratio['B']);
			if($debug_img=="n") {
				imagesetpixel($im,0,$x,$color);
			}
			else {
				for($j=0;$j<100;$j++) {
					imagesetpixel($im,$j,$x,$color);
				}
			}
		}

		for($x=$y;$x<$hauteur;$x++) {
			$ratio=array();
			for($i=0;$i<count($comp);$i++) {
				$ratio[$comp[$i]]=$tab_haut[$comp[$i]]+($x-$hauteur)*($tab_haut[$comp[$i]]-$tab_bas[$comp[$i]])/($hauteur-$y);
			}
			$color=imagecolorallocate($im,$ratio['R'],$ratio['V'],$ratio['B']);
			if($debug_img=="n") {
				imagesetpixel($im,0,$x,$color);
			}
			else {
				for($j=0;$j<100;$j++) {
					imagesetpixel($im,$j,$x,$color);
				}
			}
		}

	}
	imagepng($im,$chemin_img);
}


$tab_items=array('utiliser_couleurs_perso', 'style_body_backgroundcolor', 'utiliser_degrade', 'degrade_haut', 'degrade_bas', 'utiliser_couleurs_perso_infobulles', 'couleur_infobulle_fond_entete', 'couleur_infobulle_fond_corps', 'utiliser_couleurs_perso_lig_tab_alt', 'couleur_lig_entete', 'couleur_lig_alt1', 'couleur_lig_alt_1', 'utiliser_cahier_texte_perso', 'fond_notices_c', 'entete_fond_c', 'cellule_c', 'cellule_alt_c', 'fond_notices_t', 'entete_fond_t', 'cellule_t', 'cellule_alt_t', 'fond_notices_i', 'entete_fond_i', 'cellule_i', 'cellule_alt', 'fond_notices_f', 'cellule_f', 'police_travaux', 'police_matieres', 'bord_tableau_notice', 'cellule_gen', 'couleur_fond_postit');

if((isset($_GET['export_couleurs']))&&($_GET['export_couleurs']=='y')) {

	$csv="";
	for($i=0;$i<count($tab_items);$i++) {
		$nom=$tab_items[$i];
		$valeur=getSettingValue($tab_items[$i]);
		$csv.=$nom.";".$valeur."\n";
	}

	$nom_fic="gepi_modele_de_couleurs_".strftime("%Y%m%d_%H%M%S").".csv";
	send_file_download_headers('text/x-csv',$nom_fic);
	echo $csv;
	die();
}
elseif(isset($_POST['valide_import_couleurs'])) {

	check_token();

	$post_max_size=ini_get('post_max_size');
	$upload_max_filesize=ini_get('upload_max_filesize');
	$max_execution_time=ini_get('max_execution_time');
	$memory_limit=ini_get('memory_limit');

	$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;

	if(!is_uploaded_file($csv_file['tmp_name'])) {
		$msg="L'upload du fichier a échoué.<br />\n";

		$msg.="<p>Les variables du php.ini peuvent peut-être expliquer le problème:<br />\n";
		$msg.="post_max_size=$post_max_size<br />\n";
		$msg.="upload_max_filesize=$upload_max_filesize<br />\n";
	}
	else {
		if(!file_exists($csv_file['tmp_name'])){
			$msg="Le fichier aurait été uploadé... mais ne serait pas présent/conservé.<br />\n";

			$msg.="Les variables du php.ini peuvent peut-être expliquer le problème:<br />\n";
			$msg.="post_max_size=$post_max_size<br />\n";
			$msg.="upload_max_filesize=$upload_max_filesize<br />\n";
			$msg.="et le volume de ".$csv_file['name']." serait<br />\n";
			$msg.="\$csv_file['size']=".volume_human($csv_file['size'])."<br />\n";
		}
		else {
			/*
			$tempdir=get_user_temp_directory();

			$source_file=$csv_file['tmp_name'];
			$dest_file="../temp/".$tempdir."/gepi_modele_de_couleurs.csv";
			$res_copy=copy("$source_file" , "$dest_file");

			if(!$res_copy){
				$msg="La copie du fichier vers le dossier temporaire a échoué.<br />Vérifiez que l'utilisateur ou le groupe apache ou www-data a accès au dossier temp/$tempdir<br />\n";
			}
			else{
			*/

				$dest_file=$csv_file['tmp_name'];

				//echo "<p>La copie du fichier vers le dossier temporaire a réussi.</p>\n";

				$fp=fopen($dest_file,"r");
				if(!$fp) {
					$msg="Erreur lors de l'ouverture du fichier $dest_file<br />\n";
				}
				else {
					$msg="";
					$cpt_reg=0;
					while (!feof($fp)) {
						$ligne=trim(fgets($fp, 4096));
						if($ligne!="") {
							$tmp_tab=explode(";", $ligne);
							$nom=$tmp_tab[0];
							$valeur=$tmp_tab[1];

							if(!in_array($nom, $tab_items)) {
								$msg.="Item '$nom' inconnu (non importé).<br />";
							}
							else {
								if(!saveSetting($nom, $valeur)) {
									$msg.="Erreur lors de l'enregistrement de l'item '$nom' avec la valeur '$valeur'.<br />";
								}
								else {
									$cpt_reg++;
								}
							}
						}
					}
					if($cpt_reg>0) {
						$msg.="$cpt_reg items enregistrés.<br />";
						$msg.="Validez le formulaire en bas de page pour regénérer le fichier de styles CSS.<br />";
					}
				}

			//}
		}
	}
}


// Liste des couleurs,... paramétrables
$tab=array();
$tab[0]='style_body_backgroundcolor';
// NOTE: Pour JavaScript, on n'a pas le droit au '-' dans un nom de variable


//if(isset($_POST['ok'])) {
if(isset($_POST['is_posted'])) {
	check_token();

	$err_no=0;
	$msg="";

	//if(isset($_POST['style_body_backgroundcolor'])) {

	$reinitialiser="n";
	if(isset($_POST['secu'])) {
		if($_POST['secu']=='y') {
			$reinitialiser='y';
		}
	}

	if($reinitialiser=='y') {
		if(saveSetting('style_body_backgroundcolor','')) {
			if ($GLOBALS['multisite'] == 'y') {
				$fich=fopen("../style_screen_ajout_".getSettingValue('gepiSchoolRne').".css","w+");
			} else {
				$fich=fopen("../style_screen_ajout.css","w+");
			}
			fwrite($fich,"/*
Ce fichier est destiné à recevoir des paramètres définis depuis la page /gestion/param_couleurs.php
Chargé juste avant la section <body> dans le /lib/header.inc,
ses propriétés écrasent les propriétés définies auparavant dans le </head>.
*/
");
			fclose($fich);
			$msg.="Réinitialisation effectuée.";
		}
		else {
			$msg.="Erreur lors de la réinitialisation.";
		}
	}
	else {
		$temoin_modif=0;
		$temoin_fichier_regenere=0;
		$nb_err=0;

		if(isset($_POST['utiliser_couleurs_perso'])) {
			if(!saveSetting('utiliser_couleurs_perso','y')) {
				$msg.="Erreur lors de la sauvegarde de 'utiliser_couleurs_perso'. ";
				$nb_err++;
			}

			if(isset($_POST['style_body_backgroundcolor'])) {
				if(saveSetting('style_body_backgroundcolor',$_POST['style_body_backgroundcolor'])) {
					if ($GLOBALS['multisite'] == 'y') {
						$fich=fopen("../style_screen_ajout_".getSettingValue('gepiSchoolRne').".css","w+");
					} else {
						$fich=fopen("../style_screen_ajout.css","w+");
					}
					fwrite($fich,"/*
Ce fichier est destiné à recevoir des paramètres définis depuis la page /gestion/param_couleurs.php
Chargé juste avant la section <body> dans le /lib/header.inc,
ses propriétés écrasent les propriétés définies auparavant dans le </head>.
*/

@media screen  {
	body {
		background: #".$_POST['style_body_backgroundcolor'].";
	}
}
");
					fclose($fich);
					//$msg.="Enregistrement effectué. ";
					$temoin_modif++;
					$temoin_fichier_regenere++;
				}
				else {
					$msg.="Erreur lors de la sauvegarde de 'style_body_backgroundcolor'. ";
					$nb_err++;
				}
			}
		}
		else {
			if(!saveSetting('utiliser_couleurs_perso','n')) {
				$msg.="Erreur lors de la sauvegarde de 'utiliser_couleurs_perso'. ";
				$nb_err++;
			}

			if ($GLOBALS['multisite'] == 'y') {
				$fich=fopen("../style_screen_ajout_".getSettingValue('gepiSchoolRne').".css","w+");
			} else {
				$fich=fopen("../style_screen_ajout.css","w+");
			}
			fwrite($fich,"/*
Ce fichier est destiné à recevoir des paramètres définis depuis la page /gestion/param_couleurs.php
Chargé juste avant la section <body> dans le /lib/header.inc,
ses propriétés écrasent les propriétés définies auparavant dans le </head>.
*/
");
			fclose($fich);
			//$msg.="Enregistrement effectué. ";
			$temoin_modif++;
			$temoin_fichier_regenere++;
		}


		if(isset($_POST['degrade_double_bandeau'])) {
			saveSetting('degrade_double_bandeau','y');
		}
		else {
			saveSetting('degrade_double_bandeau','n');
		}
		if(isset($_POST['degrade_double_bandeau_small'])) {
			saveSetting('degrade_double_bandeau_small','y');
		}
		else {
			saveSetting('degrade_double_bandeau_small','n');
		}
		if(isset($_POST['degrade_double_barre_menu'])) {
			saveSetting('degrade_double_barre_menu','y');
		}
		else {
			saveSetting('degrade_double_barre_menu','n');
		}


		if(isset($_POST['utiliser_degrade'])) {
			if(!saveSetting('utiliser_degrade','y')) {
				$msg.="Erreur lors de la sauvegarde de 'utiliser_degrade'. ";
				$nb_err++;
			}

			if(isset($_POST['degrade_haut'])) {
				if((mb_strlen(preg_replace("/[0-9A-F]/","",my_strtoupper($_POST['degrade_haut'])))!=0)||(mb_strlen($_POST['degrade_haut'])!=6)) {
					$degrade_haut="020202";
				}
				else {
					$degrade_haut=$_POST['degrade_haut'];
				}

				if(saveSetting('degrade_haut',$degrade_haut)) {
					//$msg.="Enregistrement effectué. ";
					$temoin_modif++;
				}
				else {
					$msg.="Erreur lors de la sauvegarde de 'degrade_haut'. ";
					$nb_err++;
				}
			}

			if(isset($_POST['degrade_bas'])) {
				if((strlen(preg_replace("/[0-9A-F]/","",my_strtoupper($_POST['degrade_bas'])))!=0)||(strlen($_POST['degrade_bas'])!=6)) {
					$degrade_bas="4A4A59";
				}
				else {
					$degrade_bas=$_POST['degrade_bas'];
				}

				if(saveSetting('degrade_bas',$_POST['degrade_bas'])) {
					//$msg.="Enregistrement effectué. ";
					$temoin_modif++;
				}
				else {
					$msg.="Erreur lors de la sauvegarde de 'degrade_bas'. ";
					$nb_err++;
				}
			}

			if($nb_err==0) {
				/*
				if($temoin_fichier_regenere==0) {
				}
				else {
				}
				*/


				// Générer l'image...

				//genere_degrade($degrade_haut,$degrade_bas,100,"../images/background/degrade1.png");
				if(getSettingValue('degrade_double_bandeau')=='y') {$parametre_double_degrade='double';} else {$parametre_double_degrade='';}
				genere_degrade($degrade_haut,$degrade_bas,100,"../images/background/degrade1.png",$parametre_double_degrade);
				if(getSettingValue('degrade_double_bandeau_small')=='y') {$parametre_double_degrade='double';} else {$parametre_double_degrade='';}
				genere_degrade($degrade_haut,$degrade_bas,40,"../images/background/degrade1_small.png",$parametre_double_degrade);
				if(getSettingValue('degrade_double_barre_menu')=='y') {$parametre_double_degrade='double';} else {$parametre_double_degrade='';}
				genere_degrade($degrade_bas,$degrade_haut,20,"../images/background/degrade1_very_small.png",$parametre_double_degrade);

				if ($GLOBALS['multisite'] == 'y') {
					$fich=fopen("../style_screen_ajout_".getSettingValue('gepiSchoolRne').".css","w+");
				} else {
					$fich=fopen("../style_screen_ajout.css","a+");
				}

				fwrite($fich,"

div#header {
	background-color: #$degrade_bas;
}

fieldset#login_box div#header {
	background-image: url(\"./images/background/degrade1_small.png\");
}

#table_header {
	background-image: url(\"./images/background/degrade1.png\");
}

#div_login_entete {
	background: #$degrade_bas;
	background-image: url(\"./images/background/degrade1.png\");
}

#essaiMenu {
	background: #$degrade_bas;
}

/* Bandeau */
.degrade1 {
	background-color:#$degrade_bas;
}

#menu_barre {
	background-image: url(\"./images/background/degrade1_very_small.png\");
}
.menu_barre_bottom {
	background-color: #$degrade_haut;
}
/* ul ul pour ne masquer par défaut que les sous-menus */
#menu_barre ul ul {
	background-color: #$degrade_bas;
}

#menu_barre li:hover {
	background-color: #$degrade_haut;
}
#menu_barre li.sfhover {
	background-color: #$degrade_haut;
}

");

/*
//Pour old-style... problème: Si on le met cela s'applique même en new-style.

#td_headerTopRight {
	background-color: #$degrade_haut;
}

#td_headerBottomRight{
	background-color: #$degrade_haut;
}
*/

				fclose($fich);
			}
		}
		else {
			if(file_exists("../images/background/degrade1.png")) {unlink("../images/background/degrade1.png");}
			if(file_exists("../images/background/degrade1_small.png")) {unlink("../images/background/degrade1_small.png");}
			if(file_exists("../images/background/degrade1_very_small.png")) {unlink("../images/background/degrade1_very_small.png");}
			if(!saveSetting('utiliser_degrade','n')) {
				$msg.="Erreur lors de la sauvegarde de 'utiliser_degrade'. ";
				$nb_err++;
			}
		}





		if(isset($_POST['utiliser_couleurs_perso_infobulles'])) {
			if(!saveSetting('utiliser_couleurs_perso_infobulles','y')) {
				$msg.="Erreur lors de la sauvegarde de 'utiliser_couleurs_perso_infobulles'. ";
				$nb_err++;
			}

			//couleur_infobulle_fond_corps
			//couleur_infobulle_fond_entete

			if(isset($_POST['couleur_infobulle_fond_entete'])) {
				if((strlen(preg_replace("/[0-9A-F]/","",my_strtoupper($_POST['couleur_infobulle_fond_entete'])))!=0)||(strlen($_POST['couleur_infobulle_fond_entete'])!=6)) {
					$couleur_infobulle_fond_entete="4a4a59";
				}
				else {
					$couleur_infobulle_fond_entete=$_POST['couleur_infobulle_fond_entete'];
				}

				if(saveSetting('couleur_infobulle_fond_entete',$couleur_infobulle_fond_entete)) {
					//$msg.="Enregistrement effectué. ";
					$temoin_modif++;
				}
				else {
					$msg.="Erreur lors de la sauvegarde de 'couleur_infobulle_fond_entete'. ";
					$nb_err++;
				}
			}

			if(isset($_POST['couleur_infobulle_fond_corps'])) {
				if((strlen(preg_replace("/[0-9A-F]/","",my_strtoupper($_POST['couleur_infobulle_fond_corps'])))!=0)||(strlen($_POST['couleur_infobulle_fond_corps'])!=6)) {
					$couleur_infobulle_fond_corps="EAEAEA";
				}
				else {
					$couleur_infobulle_fond_corps=$_POST['couleur_infobulle_fond_corps'];
				}

				if(saveSetting('couleur_infobulle_fond_corps',$couleur_infobulle_fond_corps)) {
					//$msg.="Enregistrement effectué. ";
					$temoin_modif++;
				}
				else {
					$msg.="Erreur lors de la sauvegarde de 'couleur_infobulle_fond_corps'. ";
					$nb_err++;
				}
			}

			if($nb_err==0) {
				if ($GLOBALS['multisite'] == 'y') {
					$fich=fopen("../style_screen_ajout_".getSettingValue('gepiSchoolRne').".css","w+");
				} else {
					$fich=fopen("../style_screen_ajout.css","a+");
				}
				fwrite($fich,"
.infobulle_entete {
	background-color: #$couleur_infobulle_fond_entete;
}
.infobulle_corps {
	background-color: #$couleur_infobulle_fond_corps;
}

/*=====================================================*/
/* Utilisation des couleurs d'info bulles pour les onglets de gestion/droits_acces.php*/

.onglet {
	background-color: #$couleur_infobulle_fond_corps;
}

.contenu_onglet {
	background-color: #$couleur_infobulle_fond_corps;
}

.contenu_onglet2 {
	background-color: #$couleur_infobulle_fond_corps;
}

/*=====================================================*/
/* Utilisation des couleurs d'info bulles pour les onglets d'absences*/

/* Intérieur des onglets de mod_abs2*/
div.css-panes {
	background-color: #$couleur_infobulle_fond_corps;
	border-top:1px solid black;
}

/* Etiquette de l'onglet sélectionné */
ul.css-tabs a.current {
	background-color: #$couleur_infobulle_fond_corps;
	border-bottom:2px solid #$couleur_infobulle_fond_corps;
}

/* Etiquettes non sélectionnées*/
ul.css-tabs a {
	background-color:#$couleur_infobulle_fond_corps;
}

ul.css-tabs a:hover {
	/*background-color: #$couleur_infobulle_fond_corps;*/
	background-color: white;
}

/* Fond de l'ensemble Etiquettes+Onglets */
ul.css-tabs {
	background-color:#".$_POST['style_body_backgroundcolor'].";
}
/*=====================================================*/
span.conteneur_infobulle_css:hover span.infobulle_css {
   background: #$couleur_infobulle_fond_corps;
   border: 1px solid #$couleur_infobulle_fond_entete;
   border-top: 4px solid #$couleur_infobulle_fond_entete;
}
span.conteneur_infobulle_css:hover div.infobulle_css {
   background: #$couleur_infobulle_fond_corps;
   border: 1px solid #$couleur_infobulle_fond_entete;
   border-top: 4px solid #$couleur_infobulle_fond_entete;
}
div.conteneur_infobulle_css:hover div.infobulle_css {
   background: #$couleur_infobulle_fond_corps;
   border: 1px solid #$couleur_infobulle_fond_entete;
   border-top: 4px solid #$couleur_infobulle_fond_entete;
}
/*=====================================================*/
/* Style des DIV d'aide,... accessibles par F2,... */
div.info_abs {
	background-color: #".$_POST['style_body_backgroundcolor'].";
	border: 1px solid #$couleur_infobulle_fond_entete;
}
/*=====================================================*/
/* Utilisation des couleurs d'info bulles pour cahier_texte_2/consultation2.css */
.cdt_cadre_semaine {
	background-color: #$couleur_infobulle_fond_corps;
}
/*=====================================================*/
");
				fclose($fich);
			}

		}
		else {
			if(!saveSetting('utiliser_couleurs_perso_infobulles','n')) {
				$msg.="Erreur lors de la sauvegarde de 'utiliser_couleurs_perso_infobulles'. ";
				$nb_err++;
			}
		}




		//=========================================
		if(isset($_POST['utiliser_couleurs_perso_lig_tab_alt'])) {
			if(!saveSetting('utiliser_couleurs_perso_lig_tab_alt','y')) {
				$msg.="Erreur lors de la sauvegarde de 'utiliser_couleurs_perso_lig_tab_alt'. ";
				$nb_err++;
			}

			if(isset($_POST['couleur_lig_entete'])) {
				if((strlen(preg_replace("/[0-9A-F]/","",my_strtoupper($_POST['couleur_lig_entete'])))!=0)||(strlen($_POST['couleur_lig_entete'])!=6)) {
					$couleur_lig_entete="fff5b1";
				}
				else {
					$couleur_lig_entete=$_POST['couleur_lig_entete'];
				}

				if(saveSetting('couleur_lig_entete',$couleur_lig_entete)) {
					//$msg.="Enregistrement effectué. ";
					$temoin_modif++;
				}
				else {
					$msg.="Erreur lors de la sauvegarde de 'couleur_lig_entete'. ";
					$nb_err++;
				}
			}

			if(isset($_POST['couleur_lig_alt1'])) {
				if((strlen(preg_replace("/[0-9A-F]/","",my_strtoupper($_POST['couleur_lig_alt1'])))!=0)||(strlen($_POST['couleur_lig_alt1'])!=6)) {
					$couleur_lig_alt1="ffefd5";
				}
				else {
					$couleur_lig_alt1=$_POST['couleur_lig_alt1'];
				}

				if(saveSetting('couleur_lig_alt1',$couleur_lig_alt1)) {
					//$msg.="Enregistrement effectué. ";
					$temoin_modif++;
				}
				else {
					$msg.="Erreur lors de la sauvegarde de 'couleur_lig_alt1'. ";
					$nb_err++;
				}
			}

			if(isset($_POST['couleur_lig_alt_1'])) {
				if((strlen(preg_replace("/[0-9A-F]/","",my_strtoupper($_POST['couleur_lig_alt_1'])))!=0)||(strlen($_POST['couleur_lig_alt_1'])!=6)) {
					$couleur_lig_alt_1="F0FFF0";
				}
				else {
					$couleur_lig_alt_1=$_POST['couleur_lig_alt_1'];
				}

				if(saveSetting('couleur_lig_alt_1',$couleur_lig_alt_1)) {
					//$msg.="Enregistrement effectué. ";
					$temoin_modif++;
				}
				else {
					$msg.="Erreur lors de la sauvegarde de 'couleur_lig_alt_1'. ";
					$nb_err++;
				}
			}

			if($nb_err==0) {
				if ($GLOBALS['multisite'] == 'y') {
					$fich=fopen("../style_screen_ajout_".getSettingValue('gepiSchoolRne').".css","w+");
				} else {
					$fich=fopen("../style_screen_ajout.css","a+");
				}
				fwrite($fich,"
.gestion_temp_dir th {
	background-color: #$couleur_lig_entete;
}
.gestion_temp_dir .lig-1 {
	background-color: #$couleur_lig_alt_1;
}
.gestion_temp_dir .lig1 {
	background-color: #$couleur_lig_alt1;
}

.boireaus th {
	background-color: #$couleur_lig_entete;
}
.boireaus .lig-1 {
	background-color: #$couleur_lig_alt_1;
}
.boireaus .lig1 {
	background-color: #$couleur_lig_alt1;
}

.boireaus_alt tr:nth-child(even) {
	background-color: #$couleur_lig_alt1;
}
.boireaus_alt tr:nth-child(odd) {
	background-color: #$couleur_lig_alt_1;
}

");
				fclose($fich);
			}

		}
		else {
			if(!saveSetting('utiliser_couleurs_perso_lig_tab_alt','n')) {
				$msg.="Erreur lors de la sauvegarde de 'utiliser_couleurs_perso_lig_tab_alt'. ";
				$nb_err++;
			}
		}


		if(isset($_POST['couleur_fond_postit'])) {
			if((strlen(preg_replace("/[0-9A-F]/","",my_strtoupper($_POST['couleur_fond_postit'])))!=0)||(strlen($_POST['couleur_fond_postit'])!=6)) {
				$couleur_fond_postit="ffff00";
			}
			else {
				$couleur_fond_postit=$_POST['couleur_fond_postit'];
			}

			if(saveSetting('couleur_fond_postit',$couleur_fond_postit)) {
				//$msg.="Enregistrement effectué. ";
				$temoin_modif++;

				if ($GLOBALS['multisite'] == 'y') {
					$fich=fopen("../style_screen_ajout_".getSettingValue('gepiSchoolRne').".css","w+");
				} else {
					$fich=fopen("../style_screen_ajout.css","a+");
				}
				fwrite($fich,"
.postit {
	background-color: #$couleur_fond_postit;
}
");
				fclose($fich);

			}
			else {
				$msg.="Erreur lors de la sauvegarde de 'couleur_fond_postit'. ";
				$nb_err++;
			}
		}
		//=========================================
		// paramétrer le style du panneau d'affichage
		//=========================================
		if(isset($_POST['select_panneau_affichage'])) {
			$selected_panel = $_POST['select_panneau_affichage'];
			if(($selected_panel=='---')||(in_array($_POST['select_panneau_affichage'],$tab_panneau_affichage))) {
				if(saveSetting('style_panneau_affichage',$selected_panel)) {
					//$msg.="Enregistrement effectué. ";
	
					if(in_array($_POST['select_panneau_affichage'],$tab_panneau_affichage)) {
	
						if ($GLOBALS['multisite'] == 'y') {
							$fich=fopen("../style_screen_ajout_".getSettingValue('gepiSchoolRne').".css","w+");
						} else {
							$fich=fopen("../style_screen_ajout.css","a+");
						}
						fwrite($fich,"

.panneau_droite {
	background-image:url('./images/modeles/".$selected_panel."/right.png');
	background-repeat: repeat-y;
	background-position: right;
	position:absolute;
	top:0px;
	right:-40px;
	height:100%;
	width:40px;
}
.panneau_gauche {
	background-image:url('./images/modeles/".$selected_panel."/left.png');
	background-repeat: repeat-y;
	background-position: right;
	position:absolute;
	top:0px;
	left:-33px;
	height:100%;
	width:33px;
}

.panneau_haut {
	height:33px;
	background-image:url('./images/modeles/".$selected_panel."/top.png');
	background-repeat: repeat-x;
	position:absolute;
	width:100%;
	top:-33px;
	left:00px;
}
.panneau_centre {
	background-image:url('./images/modeles/".$selected_panel."/center.png');
	background-repeat: repeat;
	width:100%;
	top:0px;
	left:00px;
	color:black;
}
.panneau_bas {
	height:40px;
	position:absolute;
	background-image:url('./images/modeles/".$selected_panel."/bottom.png');
	background-repeat: repeat-x;
	width:100%;
	bottom:-40px;
	left:00px;
}
.panneau_coingh {
	width:33px;
	position:absolute;
	height:33px;
	background-image:url('./images/modeles/".$selected_panel."/top_left.png');
	top:-33px;
	left:-33px;		
	background-repeat: no-repeat;
}
.panneau_coindh {
	position:absolute;
	width:40px;
	height:33px;
	background-image:url('./images/modeles/".$selected_panel."/top_right.png');
	top:-33px;
	right:-40px;
	background-repeat: no-repeat;
}
.panneau_coingb {
	position:absolute;
	bottom:-40px;
	left:-33px;
	width:33px;
	height:40px;
	background-image:url('./images/modeles/".$selected_panel."/bottom_left.png');
	float: left;
}
.panneau_coindb {
	position:absolute;
	bottom:-40px;
	right:-40px;
	width:40px;
	height:40px;
	background-image:url('./images/modeles/".$selected_panel."/bottom_right.png');
	float:right;
}

");
						fclose($fich);
					}
				}
				else {
					$msg.="Erreur lors de la sauvegarde de 'style_panneau_affichage'.<br />";
					$nb_err++;
				}
			}
			else {
				$msg.="Valeur de 'style_panneau_affichage' incorrecte.<br />";
				$nb_err++;
			}
		}

		//=========================================
		// utiliser_cahier_texte_perso
		//=========================================

		$poste_notice_nom=array("fond_notices_c", "entete_fond_c", "cellule_c", "cellule_alt_c", "fond_notices_t", "entete_fond_t", "cellule_t", "cellule_alt_t", "fond_notices_i", "entete_fond_i", "cellule_i", "cellule_alt", "fond_notices_f", "cellule_f", "police_travaux", "police_matieres", "bord_tableau_notice", "cellule_gen");
		$poste_notice_couleur=array("C7FF99", "C7FF99", "E5FFCF", "D3FFAF", "FFCCCF", "FFCCCF", "FFEFF0", "FFDFE2", "ACACFF", "ACACFF", "EFEFFF", "C8C8FF", "FFFF80", "FFFFDF", "FF4444", "green", "6F6968", "F6F7EF");
		$poste_notice_classe=array("color_fond_notices_c", "couleur_entete_fond_c", "couleur_cellule_c", "couleur_cellule_alt_c", "color_fond_notices_t", "couleur_entete_fond_t", "couleur_cellule_t", "couleur_cellule_alt_t", "color_fond_notices_i", "couleur_entete_fond_i", "couleur_cellule_i", "couleur_cellule_alt_i", "color_fond_notices_f", "couleur_cellule_f", "color_police_travaux", "color_police_matieres ", "couleur_bord_tableau_notice", "couleur_cellule_gen");
		$poste_type_couleur=array("background-color", "background-color", "background-color", "background-color", "background-color", "background-color", "background-color", "background-color", "background-color", "background-color", "background-color", "background-color", "background-color", "background-color", "color", "color", "border-color", "background-color");

		if(isset($_POST['utiliser_cahier_texte_perso'])) {
			if(!saveSetting('utiliser_cahier_texte_perso','y')) {
				$msg.="Erreur lors de la sauvegarde de 'utiliser_cahier_texte_perso'. ";
				$nb_err++;
			}

			if($nb_err==0) {
				if ($GLOBALS['multisite'] == 'y') {
					$fich=fopen("../style_screen_ajout_".getSettingValue('gepiSchoolRne').".css","w+");
				} else {
					$fich=fopen("../style_screen_ajout.css","a+");
				}
					fwrite($fich,"
/* Classes des notices du cahier de texte */
");
				fclose($fich);
			}

			for($i=0;$i<count($poste_notice_nom);$i++) {
				if(isset($_POST[$poste_notice_nom[$i]])) {
					if((strlen(preg_replace("/[0-9A-F]/","",my_strtoupper($_POST[$poste_notice_nom[$i]])))!=0)||(strlen($_POST[$poste_notice_nom[$i]])!=6)) {
						$couleur_poste=$poste_notice_couleur[$i];
					}
					else {
						$couleur_poste=$_POST[$poste_notice_nom[$i]];
					}

					if(saveSetting($poste_notice_nom[$i],$couleur_poste)) {
						$temoin_modif++;
					}
					else {
						$msg.="Erreur lors de la sauvegarde de '".$poste_notice_nom[$i]."'. ";
						$nb_err++;
					}
				}

				if($nb_err==0) {
					if ($GLOBALS['multisite'] == 'y') {
						$fich=fopen("../style_screen_ajout_".getSettingValue('gepiSchoolRne').".css","w+");
					} else {
						$fich=fopen("../style_screen_ajout.css","a+");
					}
						fwrite($fich,"
.".$poste_notice_classe[$i]." {
	".$poste_type_couleur[$i].": #".$couleur_poste.";
}
	");
					fclose($fich);
				}

			}
		}
		else {
			if(!saveSetting('utiliser_cahier_texte_perso','n')) {
				$msg.="Erreur lors de la sauvegarde de 'utiliser_cahier_texte_perso'. ";
				$nb_err++;
			}
			else {
				for($i=0;$i<count($poste_notice_nom);$i++) {
					if(saveSetting($poste_notice_nom[$i],$poste_notice_couleur[$i])) {
						$temoin_modif++;
					}
					else {
						$msg.="Erreur lors de la sauvegarde de '".$poste_notice_nom[$i]."'. ";
						$nb_err++;
					}
				}
			}

		}


		//=========================================

/*
//$temoin_fichier_regenere
				if($temoin_modif==0) {
				}
				else {
				}
				fclose($fich);
*/
	}
}

//**************** EN-TETE *****************
$titre_page = "Couleurs et Modèles GEPI";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

include("../lib/couleurs_ccm.php"); 

//echo "<div class='norme'><p class='bold'><a href='param_gen.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n"; ?>
	<div class='norme'>
	<p class='bold'>
		<?php if(!isset($_GET['import_couleurs'])) { ?>
		<a href='index.php#param_couleurs'><img src='../images/icons/back.png' alt='' class='back_link'/> Retour</a>
		| Choix modèle&nbsp;:
		<select name='choix_modele' id='choix_modele' onchange="valide_modele($('choix_modele').options[$('choix_modele').selectedIndex].value)">
			<option value=''>---</option>
			<option value='rose'>Rose</option>
			<option value='vert'>Vert</option>
			<option value='bleu'>Bleu</option>
			<option value='chocolat'>Chocolat</option>
			<option value='ngb'>Noir_Gris_Blanc</option>
		</select>
		<?php }	else { ?>
		<a href='param_couleurs.php'><img src='../images/icons/back.png' alt='' class='back_link'/> Retour</a>
		<?php } ?>
		| <a href='param_couleurs.php?export_couleurs=y'>Exporter les couleurs de votre modèle</a>
		| <a href='param_couleurs.php?import_couleurs=y'>Importer les couleurs depuis un CSV</a>
	</p>
	</div>
<?php
//echo "<img src='../images/background/degrade1_very_small.png' />";

if((isset($_GET['import_couleurs']))&&($_GET['import_couleurs']=='y')) {

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	echo add_token_field();
	echo "<p>Veuillez fournir le fichier de CSV des correspondances de votre modèle de couleur&nbsp;:<br />\n";
	echo "<input type=\"file\" size=\"65\" name=\"csv_file\" /><br />\n";
	echo "<input type='hidden' name='valide_import_couleurs' value='yes' />\n";
	echo "<p><input type='submit' value='Valider' /></p>\n";
	echo "</form>\n";

	require("../lib/footer.inc.php");
	die();
}

/*
foreach($_POST as $post => $val) {
	echo $post.' : '.$val."<br />\n";
}
*/

/*
echo "<div id='div_tmp'>";
aff_tab_couleurs_ccm('div_tmp','id_style_body_backgroundcolor_R','id_style_body_backgroundcolor_V','id_style_body_backgroundcolor_B','style_body_backgroundcolor');
echo "</div>";
*/
aff_tab_couleurs_ccm('div_choix_couleur');

?>


<script type='text/javascript'>
// <![CDATA[



	var hexa=new Array("0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F");

	/*
	function affichecouleur(motif) {
		compR=eval("document.forms['tab']."+motif+"_R.value");
		compV=eval("document.forms['tab']."+motif+"_V.value");
		compB=eval("document.forms['tab']."+motif+"_B.value");

		hex1=Math.floor(compR/16);
		hex2=compR-hex1*16;
		couleur=hexa[hex1]+""+hexa[hex2];

		hex1=Math.floor(compV/16);
		hex2=compV-hex1*16;
		couleur=couleur+""+hexa[hex1]+""+hexa[hex2];

		hex1=Math.floor(compB/16);
		hex2=compB-hex1*16;
		couleur=couleur+""+hexa[hex1]+""+hexa[hex2];

		//alert(couleur);

		document.getElementById(motif).style.backgroundColor="#"+couleur;
	}
	*/


	function calculecouleur(motif) {
		compR=eval("document.forms['tab']."+motif+"_R.value");
		compV=eval("document.forms['tab']."+motif+"_V.value");
		compB=eval("document.forms['tab']."+motif+"_B.value");

		hex1=Math.floor(compR/16);
		hex2=compR-hex1*16;
		couleur=hexa[hex1]+""+hexa[hex2];

		hex1=Math.floor(compV/16);
		hex2=compV-hex1*16;
		couleur=couleur+""+hexa[hex1]+""+hexa[hex2];

		hex1=Math.floor(compB/16);
		hex2=compB-hex1*16;
		couleur=couleur+""+hexa[hex1]+""+hexa[hex2];

		return couleur;
	}

	function affichecouleur(motif) {
		document.getElementById(motif).style.backgroundColor="#"+calculecouleur(motif);
	}

	function delai_affichecouleur(motif) {
		//alert('motif='+motif);
		setTimeout("affichecouleur("+motif+")",1000);
	}


	//var liste=new Array('style_body_backgroundcolor');
	// var liste=new Array('style_body_backgroundcolor', 'degrade_haut', 'degrade_bas', 'couleur_infobulle_fond_corps', 'couleur_infobulle_fond_entete', 'couleur_lig_alt1', 'couleur_lig_alt_1');
	var liste=new Array('style_body_backgroundcolor', 'degrade_haut', 'degrade_bas', 'couleur_infobulle_fond_corps', 'couleur_infobulle_fond_entete', 'couleur_lig_entete', 'couleur_lig_alt1', 'couleur_lig_alt_1', 'police_travaux', 'police_matieres', 'bord_tableau_notice', 'cellule_gen', 'fond_notices_c', 'fond_notices_t', 'fond_notices_i', 'fond_notices_f', 'entete_fond_c', 'entete_fond_t', 'entete_fond_i', 'cellule_c', 'cellule_t', 'cellule_i', 'cellule_f', 'cellule_alt_c', 'cellule_alt_t', 'cellule_alt_i', 'couleur_fond_postit');

	function init() {
		for(i=0;i<liste.length;i++) {
			eval("affichecouleur('"+liste[i]+"')");
		}
	}

	function calcule_et_valide() {
		for(i=0;i<liste.length;i++) {
			champ=eval("document.forms['tab']."+liste[i])
			champ.value=calculecouleur(liste[i]);
		}
		document.forms['tab'].submit();
	}

	//function reinitialiser() {
	function reinit() {
		document.forms['tab'].secu.value='y';
		document.forms['tab'].submit();
	}



	var tabmotif=new Array();
	// #EAEAEA
	// 14*16+10
	tabmotif['style_body_backgroundcolor_R']="234";
	tabmotif['style_body_backgroundcolor_V']="234";
	tabmotif['style_body_backgroundcolor_B']="234";
	//020202
	tabmotif['degrade_haut_R']="2";
	tabmotif['degrade_haut_V']="2";
	tabmotif['degrade_haut_B']="2";
	//4A4A59
	// 4*16+10 et 5*16+9
	tabmotif['degrade_bas_R']="74";
	tabmotif['degrade_bas_V']="74";
	tabmotif['degrade_bas_B']="89";

	// #fff5b1
	tabmotif['couleur_lig_entete_R']=255;
	tabmotif['couleur_lig_entete_V']=245;
	tabmotif['couleur_lig_entete_B']=177;

	// papayawhip #FFEFD5
	tabmotif['couleur_lig_alt1_R']=255;
	tabmotif['couleur_lig_alt1_V']=239;
	tabmotif['couleur_lig_alt1_B']=213;

	// honeydew #F0FFF0
	tabmotif['couleur_lig_alt_1_R']=240;
	tabmotif['couleur_lig_alt_1_V']=255;
	tabmotif['couleur_lig_alt_1_B']=240;


	tabmotif['couleur_fond_postit_R']=255;
	tabmotif['couleur_fond_postit_V']=255;
	tabmotif['couleur_fond_postit_B']=0;


	// Cahier de texte : Compte rendu
	// #C7FF99
	tabmotif['fond_notices_c_R']=199;
	tabmotif['fond_notices_c_V']=255;
	tabmotif['fond_notices_c_B']=153;
	tabmotif['entete_fond_c_R']=199;
	tabmotif['entete_fond_c_V']=255;
	tabmotif['entete_fond_c_B']=153;
	// #E5FFCF
	tabmotif['cellule_c_R']=229;
	tabmotif['cellule_c_V']=255;
	tabmotif['cellule_c_B']=207;
	// #D3FFAF
	tabmotif['cellule_alt_c_R']=211;
	tabmotif['cellule_alt_c_V']=255;
	tabmotif['cellule_alt_c_B']=175;



	// Cahier de texte : Travail à faire
	//	#FFCCCF
	tabmotif['fond_notices_t_R']=255;
	tabmotif['fond_notices_t_V']=204;
	tabmotif['fond_notices_t_B']=207;
	tabmotif['entete_fond_t_R']=255;
	tabmotif['entete_fond_t_V']=204;
	tabmotif['entete_fond_t_B']=207;
	// #FFEFF0
	tabmotif['cellule_t_R']=255;
	tabmotif['cellule_t_V']=239;
	tabmotif['cellule_t_B']=240;
	// #FFDFE2
	tabmotif['cellule_alt_t_R']=255;
	tabmotif['cellule_alt_t_V']=223;
	tabmotif['cellule_alt_t_B']=226;

	// Cahier de texte : Informations générales
	//	#ACACFF
	tabmotif['fond_notices_i_R']=172;
	tabmotif['fond_notices_i_V']=172;
	tabmotif['fond_notices_i_B']=255;
	// #EFEFFF
	tabmotif['entete_fond_i_R']=172;
	tabmotif['entete_fond_i_V']=172;
	tabmotif['entete_fond_i_B']=255;
	tabmotif['cellule_i_R']=239;
	tabmotif['cellule_i_V']=239;
	tabmotif['cellule_i_B']=255;
	// #C8C8FF
	tabmotif['cellule_alt_i_R']=200;
	tabmotif['cellule_alt_i_V']=200;
	tabmotif['cellule_alt_i_B']=255;

	// Cahier de texte : Travaux futurs
	//	#FFFF80
	tabmotif['fond_notices_f_R']=255;
	tabmotif['fond_notices_f_V']=255;
	tabmotif['fond_notices_f_B']=128;
	// #FFFFDF
	tabmotif['cellule_f_R']=255;
	tabmotif['cellule_f_V']=255;
	tabmotif['cellule_f_B']=223;

	// Cahier de texte : Couleurs générales
	//	#FF4444
	tabmotif['police_travaux_R']=255;
	tabmotif['police_travaux_V']=68;
	tabmotif['police_travaux_B']=68;
	//	#008000
	tabmotif['police_matieres_R']=0;
	tabmotif['police_matieres_V']=128;
	tabmotif['police_matieres_B']=0;
	//	#6F6968
	tabmotif['bord_tableau_notice_R']=111;
	tabmotif['bord_tableau_notice_V']=105;
	tabmotif['bord_tableau_notice_B']=104;
	//	#F6F7EF
	tabmotif['cellule_gen_R']=246;
	tabmotif['cellule_gen_V']=247;
	tabmotif['cellule_gen_B']=239;


	function reinit_couleurs(motif) {
		comp_motif=motif+"_R";
		champ_R=eval("document.forms['tab']."+comp_motif);
		champ_R.value=tabmotif[comp_motif];

		comp_motif=motif+"_V";
		champ_V=eval("document.forms['tab']."+comp_motif);
		champ_V.value=tabmotif[comp_motif];

		comp_motif=motif+"_B";
		champ_B=eval("document.forms['tab']."+comp_motif);
		champ_B.value=tabmotif[comp_motif];

		//calcule_et_valide();
		affichecouleur(motif);

		//return false;
	}
	// ========================================================================================
	//
	//					Création des observers généraux mousedown, mouseup
	//
	// ========================================================================================
	window.onload = function() {
		
		$('select_panneau_affichage').observe('change', function(event) {
			var selected_panel = $$('select#select_panneau_affichage option').find(function(ele){return ele.selected}).value;
			$('panneau_coingh').setStyle({backgroundImage: 'url(../images/modeles/'+selected_panel+'/top_left.png)'});
			$('panneau_haut').setStyle({backgroundImage: 'url(../images/modeles/'+selected_panel+'/top.png)'});
			$('panneau_coindh').setStyle({backgroundImage: 'url(../images/modeles/'+selected_panel+'/top_right.png)'});
			$('panneau_gauche').setStyle({backgroundImage: 'url(../images/modeles/'+selected_panel+'/left.png)'});
			$('panneau_centre').setStyle({backgroundImage: 'url(../images/modeles/'+selected_panel+'/center.png)'});
			$('panneau_droite').setStyle({backgroundImage: 'url(../images/modeles/'+selected_panel+'/right.png)'});
			$('panneau_coingb').setStyle({backgroundImage: 'url(../images/modeles/'+selected_panel+'/bottom_left.png)'});
			$('panneau_bas').setStyle({backgroundImage: 'url(../images/modeles/'+selected_panel+'/bottom.png)'});
			$('panneau_coindb').setStyle({backgroundImage: 'url(../images/modeles/'+selected_panel+'/bottom_right.png)'});
		});

	}

//]]>
</script>
<!--noscript>
</noscript-->

<p>Définissez les couleurs pour l'interface GEPI et gérez vos modèles.
<!--Dans sa version actuelle, seule la couleur de fond de la page peut être paramétrée depuis cette page.-->
</p>

<?php

/*
// Tableau des couleurs HTML:
$tab_html_couleurs=Array("aliceblue","antiquewhite","aqua","aquamarine","azure","beige","bisque","black","blanchedalmond","blue","blueviolet","brown","burlywood","cadetblue","chartreuse","chocolate","coral","cornflowerblue","cornsilk","crimson","cyan","darkblue","darkcyan","darkgoldenrod","darkgray","darkgreen","darkkhaki","darkmagenta","darkolivegreen","darkorange","darkorchid","darkred","darksalmon","darkseagreen","darkslateblue","darkslategray","darkturquoise","darkviolet","deeppink","deepskyblue","dimgray","dodgerblue","firebrick","floralwhite","forestgreen","fuchsia","gainsboro","ghostwhite","gold","goldenrod","gray","green","greenyellow","honeydew","hotpink","indianred","indigo","ivory","khaki","lavender","lavenderblush","lawngreen","lemonchiffon","lightblue","lightcoral","lightcyan","lightgoldenrodyellow","lightgreen","lightgrey","lightpink","lightsalmon","lightseagreen","lightskyblue","lightslategray","lightsteelblue","lightyellow","lime","limegreen","linen","magenta","maroon","mediumaquamarine","mediumblue","mediumorchid","mediumpurple","mediumseagreen","mediumslateblue","mediumspringgreen","mediumturquoise","mediumvioletred","midnightblue","mintcream","mistyrose","moccasin","navajowhite","navy","oldlace","olive","olivedrab","orange","orangered","orchid","palegoldenrod","palegreen","paleturquoise","palevioletred","papayawhip","peachpuff","peru","pink","plum","powderblue","purple","red","rosybrown","royalblue","saddlebrown","salmon","sandybrown","seagreen","seashell","sienna","silver","skyblue","slateblue","slategray","snow","springgreen","steelblue","tan","teal","thistle","tomato","turquoise","violet","wheat","white","whitesmoke","yellow","yellowgreen");
*/

	// Initialisation
	$tabcouleurs=array();
	$tabcouleurs['style_body_backgroundcolor']=array();
	$style_body_backgroundcolor=getSettingValue('style_body_backgroundcolor');
	//echo "\$style_body_backgroundcolor=$style_body_backgroundcolor<br />";
	if($style_body_backgroundcolor!="") {
/*
		$compR=mb_substr($style_body_backgroundcolor,0,2);
		$compV=mb_substr($style_body_backgroundcolor,2,2);
		$compB=mb_substr($style_body_backgroundcolor,4,2);

		//echo "\$compR=$compR<br />";
		//echo "\$compV=$compV<br />";
		//echo "\$compB=$compB<br />";

		$nb_compR=hex2nb(mb_substr($compR,0,1))*16+hex2nb(mb_substr($compR,1,1));
		$nb_compV=hex2nb(mb_substr($compV,0,1))*16+hex2nb(mb_substr($compV,1,1));
		$nb_compB=hex2nb(mb_substr($compB,0,1))*16+hex2nb(mb_substr($compB,1,1));

		//echo "\$nb_compR=$nb_compR<br />";
		//echo "\$nb_compV=$nb_compV<br />";
		//echo "\$nb_compB=$nb_compB<br />";

		$tabcouleurs['style_body_backgroundcolor']['R']=$nb_compR;
		$tabcouleurs['style_body_backgroundcolor']['V']=$nb_compV;
		$tabcouleurs['style_body_backgroundcolor']['B']=$nb_compB;
*/

	$tabcouleurs['style_body_backgroundcolor']=tab_rvb($style_body_backgroundcolor);
} else {
	// #EAEAEA
	// 14*16+10
	$tabcouleurs['style_body_backgroundcolor']['R']=234;
	$tabcouleurs['style_body_backgroundcolor']['V']=234;
	$tabcouleurs['style_body_backgroundcolor']['B']=234;
}


/*
	$tabcouleurs['style_body_backgroundcolor']=array();
	$style_body_backgroundcolor=getSettingValue('style_body_backgroundcolor');
	//echo "\$style_body_backgroundcolor=$style_body_backgroundcolor<br />";
	if($style_body_backgroundcolor!="") {
		$tabcouleurs['style_body_backgroundcolor']=tab_rvb($style_body_backgroundcolor);
	}
	else {
		// #EAEAEA
		// 14*16+10
		$tabcouleurs['style_body_backgroundcolor']['R']=234;
		$tabcouleurs['style_body_backgroundcolor']['V']=234;
		$tabcouleurs['style_body_backgroundcolor']['B']=234;
	}
*/

echo "<form id='tab' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
echo add_token_field();
	echo "<h2><strong>Couleurs:</strong></h2>\n";
	// echo "<blockquote>\n";
		echo "<div class='tableau_param_couleur'>\n";
			// echo "<tr>\n";
				// echo "<td>\n";
					echo "<input type='checkbox' name='utiliser_couleurs_perso' id='utiliser_couleurs_perso' value='y' ";
					if(getSettingValue('utiliser_couleurs_perso')=='y') {
						echo "checked='checked' ";
					}
					echo "/> ";
					echo "<label for='utiliser_couleurs_perso' style='cursor: pointer;'>Utiliser des couleurs personnalisées.</label>\n";
				// echo "</td>\n";
			// echo "</tr>\n";
			// echo "<tr>\n";
			// 	echo "<td>\n";
			// 		echo "&nbsp;";
			// 	echo "</td>\n";
				// echo "<td>\n";
					echo "<table class='tableau_change_couleur' summary=\"arrière plan changement de couleur : colonne 3 rouge, colonne 4 vert, colonne 5 bleu, colonne 7 validation\">\n";
						echo "<tr class='fond_blanc'>\n";
							echo "<td class='texte_gras'>\nMotif\n</td>\n";
							echo "<td class='texte_gras'>\nPropriété\n</td>\n";
							for($j=0;$j<count($comp);$j++) {
								echo "<td class='texte_gras'>\n$comp[$j]\n</td>\n";
							}
							echo "<td class='texte_gras'>\nAperçu\n</td>\n";
							echo "<td class='texte_gras'>\nRéinitialisation\n</td>\n";
						echo "</tr>\n";
						for($i=0;$i<count($tab);$i++) {
							echo "<tr>\n";
								//echo "<td>$tab[$i]</td>\n";
								//echo "<td>Couleur de fond de page: <a href='couleur.php?objet=Fond'></a></td>\n";
								echo "<td>\n";
									echo "Couleur de fond de page\n";
									//echo "<a href='couleur.php?objet=".$tab[$i]."'></a>
								echo "</td>\n";
								echo "<td>\nbody{background-color: #XXXXXX;}\n</td>\n";
								for($j=0;$j<count($comp);$j++) {
									/*
									$sql="SELECT value FROM setting WHERE name='".$tab[$i]."_".$comp[$j]."'";
									$res_couleur=mysql_query($sql);
									if(mysql_num_rows($res_couleur)>0) {
										$tmp=mysql_fetch_object($res_couleur);
										$tabcouleurs[$tab[$i]][$comp[$j]]=$tmp->value;
									}
									*/
									echo "<td>\n";
									//echo "$sql<br />";
									//echo "<input type='text' name='".$tab[$i]."_".$comp[$j]."' value='".$tabcouleurs[$tab[$i]][$comp[$j]]."' size='3' onblur='affichecouleur(\"".$tab[$i]."\")' />\n";

									//echo "<input type='text' name='".$tab[$i]."_".$comp[$j]."' id='id_".$tab[$i]."_".$comp[$j]."' value='".$tabcouleurs[$tab[$i]][$comp[$j]]."' size='3' onChange='delai_affichecouleur(\"".$tab[$i]."\")' onkeydown=\"clavier_2(this.id,event);\" />\n";

									echo "<label for='id_".$tab[$i]."_".$comp[$j]."' class='invisible'>".$comp[$j]." fond ".$comp[$j]."</label>\n";
									echo "<input type='text' name='".$tab[$i]."_".$comp[$j]."' id='id_".$tab[$i]."_".$comp[$j]."' value='".$tabcouleurs[$tab[$i]][$comp[$j]]."' size='3' onblur='affichecouleur(\"".$tab[$i]."\")' onkeydown=\"clavier_2(this.id,event,0,255);\" autocomplete='off' />\n";
									echo "</td>\n";
								}

								//echo "<td id='".$tab[$i]."'>\n";
								echo "<td id='".$tab[$i]."'";

								echo " onclick=\"document.getElementById('id_couleur_r').value='id_".$tab[$i]."_R';";
								echo "document.getElementById('id_couleur_v').value='id_".$tab[$i]."_V';";
								echo "document.getElementById('id_couleur_b').value='id_".$tab[$i]."_B';";
								echo "document.getElementById('id_couleur_motif').value='".$tab[$i]."';";
								echo "afficher_div('div_choix_couleur','y',10,-200)\">";

								// Champ calculé/mis à jour par la fonction JavaScript calcule_et_valide() lors de la validation du formulaire:
								echo "<input type='hidden' name='$tab[$i]' value='$tab[$i]' />\n";
								echo "&nbsp;&nbsp;&nbsp;</td>\n";

								echo "<td>\n";
								//echo "<a href='#' onclick='reinit_couleurs(\"$tab[$i]\");return false;'>Réinitialiser</a>\n";
								echo "<a href='#' onclick='reinit_couleurs(\"$tab[$i]\");return false;'>Réinitialiser</a>\n";
								//echo "<a href='javascript:reinit_couleurs(\"$tab[$i]\");'>Réinitialiser</a>\n";
								//echo "<input type='button' name='reinit$i' value='Réinitialiser' onclick='javascript:reinit_couleurs(\"$tab[$i]\");' />\n";

								/*
								echo " <a href='#' onclick=\"document.getElementById('id_couleur_r').value='id_".$tab[$i]."_R';";
								echo "document.getElementById('id_couleur_v').value='id_".$tab[$i]."_V';";
								echo "document.getElementById('id_couleur_b').value='id_".$tab[$i]."_B';";
								echo "document.getElementById('id_couleur_motif').value='".$tab[$i]."';";
								echo "afficher_div('div_choix_couleur','y',10,-200)\">Choix</a>";
								*/

								echo "</td>\n";
							echo "</tr>\n";
						}
					echo "</table>\n";
				// echo "</td>\n";
			// echo "</tr>\n";
		echo "</div>\n";
	// echo "</blockquote>\n";





	echo "<h2>\n<strong>Dégradé:</strong>\n</h2>\n";
	// echo "<blockquote>\n";
		echo "<div class='tableau_param_couleur'>\n";
			// echo "<tr>\n";
				// echo "<td>\n";
					echo "<input type='checkbox' name='utiliser_degrade' id='utiliser_degrade' value='y' ";
					if(getSettingValue('utiliser_degrade')=='y') {
						echo "checked='checked' ";
					}
					echo "/> ";
				// echo "</td>\n";
				// echo "<td>\n";
					echo "<label for='utiliser_degrade' style='cursor: pointer;'>Générer/utiliser un dégradé personnalisé pour l'entête de page.</label>\n";
				// echo "</td>\n";
			// echo "</tr>\n";

			// echo "<tr>\n";
				// echo "<td>\n";
					// echo "&nbsp;";
				// echo "</td>\n";
				// echo "<td>\n";
					echo "<table class='tableau_change_couleur' summary=\"bandeau changement de couleur : ligne 2 dégradé haut, ligne 3 dégradé bas, colonne 2 rouge, colonne 3 vert, colonne 4 bleu, colonne 6 validation\">\n";
						echo "<tr class='fond_blanc'>\n";
							echo "<td class='texte_gras'>Couleur</td>\n";
							for($j=0;$j<count($comp);$j++) {
								echo "<td class='texte_gras'>$comp[$j]</td>\n";
							}
							echo "<td class='texte_gras'>Aperçu</td>\n";
							echo "<td class='texte_gras'>Réinitialisation</td>\n";
							echo "<td rowspan='3' style='text-align: left;'>\n";
							echo "<input type='checkbox' name='degrade_double_bandeau' id='degrade_double_bandeau' value='y' ";
							if(getSettingValue('degrade_double_bandeau')=='y') {echo "checked ";}
							echo "/><label for='degrade_double_bandeau'> Utiliser un double dégradé sur le bandeau d'entête.</label><br />\n";
							echo "<input type='checkbox' name='degrade_double_bandeau_small' id='degrade_double_bandeau_small' value='y' ";
							if(getSettingValue('degrade_double_bandeau_small')=='y') {echo "checked ";}
							echo "/><label for='degrade_double_bandeau_small'> Utiliser un double dégradé sur le bandeau d'entête réduit.</label><br />\n";
							echo "<input type='checkbox' name='degrade_double_barre_menu' id='degrade_double_barre_menu' value='y' ";
							if(getSettingValue('degrade_double_barre_menu')=='y') {echo "checked ";}
							echo "/><label for='degrade_double_barre_menu'> Utiliser un double dégradé sur la barre de menus.</label><br />\n";
							echo "</td>\n";
						echo "</tr>\n";

						$tab_degrade=array("degrade_haut","degrade_bas");

						$degrade_haut=getSettingValue('degrade_haut');
						if($degrade_haut!="") {
							$tabcouleurs['degrade_haut']=tab_rvb($degrade_haut);
						}
						else {
							$tabcouleurs['degrade_haut']['R']=2;
							$tabcouleurs['degrade_haut']['V']=2;
							$tabcouleurs['degrade_haut']['B']=2;
						}

						$degrade_bas=getSettingValue('degrade_bas');
						if($degrade_bas!="") {
							$tabcouleurs['degrade_bas']=tab_rvb($degrade_bas);
						}
						else {
							$tabcouleurs['degrade_bas']['R']=74;
							$tabcouleurs['degrade_bas']['V']=74;
							$tabcouleurs['degrade_bas']['B']=89;
						}

						for($i=0;$i<count($tab_degrade);$i++) {
							echo "<tr>\n";

								echo "<td>$tab_degrade[$i]";
								echo "</td>\n";

								for($j=0;$j<count($comp);$j++) {
									/*
									$sql="SELECT value FROM setting WHERE name='".$tab_degrade[$i]."_".$comp[$j]."'";
									$res_couleur=mysql_query($sql);
									if(mysql_num_rows($res_couleur)>0) {
										$tmp=mysql_fetch_object($res_couleur);
										$tabcouleurs[$tab_degrade[$i]][$comp[$j]]=$tmp->value;
									}
									*/
									echo "<td>\n";
									echo "<label for='id_".$tab_degrade[$i]."_".$comp[$j]."' class='invisible'>".($i+1)."$comp[$j] degrade ".($i+1)."</label>\n";
										echo "<input type='text' name='".$tab_degrade[$i]."_".$comp[$j]."' id='id_".$tab_degrade[$i]."_".$comp[$j]."' value='".$tabcouleurs[$tab_degrade[$i]][$comp[$j]]."' size='3' onblur='affichecouleur(\"".$tab_degrade[$i]."\")' onkeydown=\"clavier_2(this.id,event,0,255);\" autocomplete='off' />\n";

									echo "</td>\n";
								}
								//echo "<td id='".$tab_degrade[$i]."'>\n";

								echo "<td id='".$tab_degrade[$i]."'";

								echo " onclick=\"document.getElementById('id_couleur_r').value='id_".$tab_degrade[$i]."_R';";
								echo "document.getElementById('id_couleur_v').value='id_".$tab_degrade[$i]."_V';";
								echo "document.getElementById('id_couleur_b').value='id_".$tab_degrade[$i]."_B';";
								echo "document.getElementById('id_couleur_motif').value='".$tab_degrade[$i]."';";
								echo "afficher_div('div_choix_couleur','y',10,-200)\">";

								// Champ calculé/mis à jour par la fonction JavaScript calcule_et_valide() lors de la validation du formulaire:
									echo "<input type='hidden' name='$tab_degrade[$i]' value='$tab_degrade[$i]' />\n";
								echo "&nbsp;&nbsp;&nbsp;</td>\n";

								echo "<td>\n";
									echo "<a href='#' onclick='reinit_couleurs(\"$tab_degrade[$i]\");return false;'>Réinitialiser</a>\n";
								echo "</td>\n";


							echo "</tr>\n";
						}
					echo "</table>\n";
				// echo "</td>\n";
			// echo "</tr>\n";
		echo "</div>\n";
	// echo "</blockquote>\n";











	$tabcouleurs['couleur_infobulle_fond_entete']=array();
	$couleur_infobulle_fond_entete=getSettingValue('couleur_infobulle_fond_entete');
	if($couleur_infobulle_fond_entete!="") {
		$tabcouleurs['couleur_infobulle_fond_entete']=tab_rvb($couleur_infobulle_fond_entete);
	}
	else {
		// #4a4a59
		// 4*16+10=74 et 5*16+9=89
		$tabcouleurs['couleur_infobulle_fond_entete']['R']=74;
		$tabcouleurs['couleur_infobulle_fond_entete']['V']=74;
		$tabcouleurs['couleur_infobulle_fond_entete']['B']=89;
	}

	$tabcouleurs['couleur_infobulle_fond_corps']=array();
	$couleur_infobulle_fond_corps=getSettingValue('couleur_infobulle_fond_corps');
	if($couleur_infobulle_fond_corps!="") {
		$tabcouleurs['couleur_infobulle_fond_corps']=tab_rvb($couleur_infobulle_fond_corps);
	}
	else {
		// #EAEAEA
		// 14*16+10=234
		$tabcouleurs['couleur_infobulle_fond_corps']['R']=234;
		$tabcouleurs['couleur_infobulle_fond_corps']['V']=234;
		$tabcouleurs['couleur_infobulle_fond_corps']['B']=234;
	}

	echo "<h2><strong>Couleurs des 'infobulles':</strong></h2>\n";
	// echo "<blockquote>\n";
		echo "<div class='tableau_param_couleur'>\n";
		// echo "<tr>\n";
			// echo "<td>\n";
				echo "<input type='checkbox' name='utiliser_couleurs_perso_infobulles' id='utiliser_couleurs_perso_infobulles' value='y' ";
				if(getSettingValue('utiliser_couleurs_perso_infobulles')=='y') {
					echo "checked='checked' ";
				}
				echo "/> ";
			// echo "</td>\n";
			// echo "<td>\n";
				echo "<label for='utiliser_couleurs_perso_infobulles' style='cursor: pointer;'>Utiliser des couleurs personnalisées pour les infobulles.</label>\n";
			// echo "</td>\n";
		// echo "</tr>\n";

		// echo "<tr>\n";
			// echo "<td>\n";
				// echo "&nbsp;";
			// echo "</td>\n";
			// echo "<td>\n";
				echo "<table class='tableau_change_couleur' summary=\"infobulles changement de couleurs : ligne 2 entête, ligne 3 corps, colonne 2 rouge, colonne 3 vert, colonne 4 bleu, colonne 6 validation\">\n";

					echo "<tr class='fond_blanc'>\n";
						echo "<td class='texte_gras'>\nMotif\n</td>\n";
						for($j=0;$j<count($comp);$j++) {
							echo "<td class='texte_gras'>\n$comp[$j]\n</td>\n";
						}
						echo "<td class='texte_gras'>\nAperçu\n</td>\n";
						echo "<td class='texte_gras'>\nRéinitialisation\n</td>\n";

					echo "</tr>\n";

					echo "<tr>\n";
						echo "<td>\n";
							echo "Couleur de fond de l'entête des infobulles\n";
						echo "</td>\n";
						for($j=0;$j<count($comp);$j++) {
							/*
							$sql="SELECT value FROM setting WHERE name='".$tab[$i]."_".$comp[$j]."'";
							$res_couleur=mysql_query($sql);
							if(mysql_num_rows($res_couleur)>0) {
								$tmp=mysql_fetch_object($res_couleur);
								$tabcouleurs[$tab[$i]][$comp[$j]]=$tmp->value;
							}
							*/

							echo "<td>\n";
							echo "<label for='id_couleur_infobulle_fond_entete_".$comp[$j]."' class='invisible'>".$comp[$j]."E entête ".$comp[$j]."</label>\n";
							echo "<input type='text' name='couleur_infobulle_fond_entete_".$comp[$j]."' id='id_couleur_infobulle_fond_entete_".$comp[$j]."' value='".$tabcouleurs['couleur_infobulle_fond_entete'][$comp[$j]]."' size='3' onblur='affichecouleur(\"couleur_infobulle_fond_entete\")' onkeydown=\"clavier_2(this.id,event,0,255);\" autocomplete='off' />\n";
							echo "</td>\n";
						}
						//echo "<td id='couleur_infobulle_fond_entete'>\n";

						echo "<td id='couleur_infobulle_fond_entete'";

						echo " onclick=\"document.getElementById('id_couleur_r').value='id_couleur_infobulle_fond_entete_R';";
						echo "document.getElementById('id_couleur_v').value='id_couleur_infobulle_fond_entete_V';";
						echo "document.getElementById('id_couleur_b').value='id_couleur_infobulle_fond_entete_B';";
						echo "document.getElementById('id_couleur_motif').value='couleur_infobulle_fond_entete';";
						echo "afficher_div('div_choix_couleur','y',10,-200)\">";

						// Champ calculé/mis à jour par la fonction JavaScript calcule_et_valide() lors de la validation du formulaire:
							echo "<input type='hidden' name='couleur_infobulle_fond_entete' value='couleur_infobulle_fond_entete' />\n";
						echo "&nbsp;&nbsp;&nbsp;</td>\n";
						echo "<td>\n";
							echo "<a href='#' onclick='reinit_couleurs(\"couleur_infobulle_fond_entete\");return false;'>Réinitialiser</a>\n";
						echo "</td>\n";
						echo "</tr>\n";

						echo "<tr>\n";
							echo "<td>\n";
								echo "Couleur de fond du corps des infobulles";
							echo "</td>\n";
							for($j=0;$j<count($comp);$j++) {
								/*
								$sql="SELECT value FROM setting WHERE name='".$tab[$i]."_".$comp[$j]."'";
								$res_couleur=mysql_query($sql);
								if(mysql_num_rows($res_couleur)>0) {
									$tmp=mysql_fetch_object($res_couleur);
									$tabcouleurs[$tab[$i]][$comp[$j]]=$tmp->value;
								}
								*/

								echo "<td>\n";
									echo "<label for='id_couleur_infobulle_fond_corps_".$comp[$j]."' class='invisible'>".$comp[$j]."C corps ".$comp[$j]."</label>\n";
									echo "<input type='text' name='couleur_infobulle_fond_corps_".$comp[$j]."' id='id_couleur_infobulle_fond_corps_".$comp[$j]."' value='".$tabcouleurs['couleur_infobulle_fond_corps'][$comp[$j]]."' size='3' onblur='affichecouleur(\"couleur_infobulle_fond_corps\")' onkeydown=\"clavier_2(this.id,event,0,255);\" autocomplete='off' />\n";
								echo "</td>\n";
							}
							//echo "<td id='couleur_infobulle_fond_corps'>\n";
							echo "<td id='couleur_infobulle_fond_corps'";

							echo " onclick=\"document.getElementById('id_couleur_r').value='id_couleur_infobulle_fond_corps_R';";
							echo "document.getElementById('id_couleur_v').value='id_couleur_infobulle_fond_corps_V';";
							echo "document.getElementById('id_couleur_b').value='id_couleur_infobulle_fond_corps_B';";
							echo "document.getElementById('id_couleur_motif').value='couleur_infobulle_fond_corps';";
							echo "afficher_div('div_choix_couleur','y',10,-200)\">";

							// Champ calculé/mis à jour par la fonction JavaScript calcule_et_valide() lors de la validation du formulaire:
								echo "<input type='hidden' name='couleur_infobulle_fond_corps' value='couleur_infobulle_fond_corps' />\n";
							echo "&nbsp;&nbsp;&nbsp;</td>\n";
							echo "<td>\n";
								echo "<a href='#' onclick='reinit_couleurs(\"couleur_infobulle_fond_corps\");return false;'>Réinitialiser</a>\n";
							echo "</td>\n";
						echo "</tr>\n";

					echo "</table>\n";
				// echo "</td>\n";
			// echo "</tr>\n";
		echo "</div>\n";
	// echo "</blockquote>\n";




		//=========================================

		$tabcouleurs['couleur_lig_entete']=array();
		$couleur_lig_entete=getSettingValue('couleur_lig_entete');
		if($couleur_lig_entete!="") {
			$tabcouleurs['couleur_lig_entete']=tab_rvb($couleur_lig_entete);
		}
		else {
			// #fff5b1
			$tabcouleurs['couleur_lig_entete']['R']=255;
			$tabcouleurs['couleur_lig_entete']['V']=245;
			$tabcouleurs['couleur_lig_entete']['B']=177;
		}

		$tabcouleurs['couleur_lig_alt1']=array();
		$couleur_lig_alt1=getSettingValue('couleur_lig_alt1');
		if($couleur_lig_alt1!="") {
			$tabcouleurs['couleur_lig_alt1']=tab_rvb($couleur_lig_alt1);
		}
		else {
			// papayawhip #FFEFD5
			$tabcouleurs['couleur_lig_alt1']['R']=255;
			$tabcouleurs['couleur_lig_alt1']['V']=239;
			$tabcouleurs['couleur_lig_alt1']['B']=213;
		}

		$tabcouleurs['couleur_lig_alt_1']=array();
		$couleur_lig_alt_1=getSettingValue('couleur_lig_alt_1');
		if($couleur_lig_alt_1!="") {
			$tabcouleurs['couleur_lig_alt_1']=tab_rvb($couleur_lig_alt_1);
		}
		else {
			// honeydew #F0FFF0
			$tabcouleurs['couleur_lig_alt_1']['R']=240;
			$tabcouleurs['couleur_lig_alt_1']['V']=255;
			$tabcouleurs['couleur_lig_alt_1']['B']=240;
		}

	echo "<h2><strong>Couleurs des lignes alternées dans les tableaux:</strong></h2>\n";
		echo "<div class='tableau_param_couleur'>\n";
					echo "<input type='checkbox' name='utiliser_couleurs_perso_lig_tab_alt' id='utiliser_couleurs_perso_lig_tab_alt' value='y' ";
					if(getSettingValue('utiliser_couleurs_perso_lig_tab_alt')=='y') {
						echo "checked='checked' ";
					}
					echo "/> ";
					echo "<label for='utiliser_couleurs_perso_lig_tab_alt' style='cursor: pointer;'>couleurs des tableaux.</label>\n";

					echo "<table class='tableau_change_couleur' summary=\"tableau changement de couleur : ligne 2 lignes impaires, ligne 3 lignes paires, colonne 2 rouge, colonne 3 vert, colonne 4 bleu, colonne 6 validation\">\n";

						echo "<tr class='fond_blanc'>\n";
							echo "<td class='texte_gras'>Motif</td>\n";
							for($j=0;$j<count($comp);$j++) {
								echo "<td class='texte_gras'>$comp[$j]</td>\n";
							}
							echo "<td class='texte_gras'>Aperçu</td>\n";
							echo "<td class='texte_gras'>Réinitialisation</td>\n";

						echo "</tr>\n";

						echo "<tr>\n";
							echo "<td>Couleur ligne d'entête";
							echo "</td>\n";
							for($j=0;$j<count($comp);$j++) {
								echo "<td>\n";
									echo "<label for='id_couleur_lig_entete_".$comp[$j]."' class='invisible'>".$comp[$j]." P lignes paires ".$comp[$j]."</label>\n";
									echo "<input type='text' name='couleur_lig_entete_".$comp[$j]."' id='id_couleur_lig_entete_".$comp[$j]."' value='".$tabcouleurs['couleur_lig_entete'][$comp[$j]]."' size='3' onblur='affichecouleur(\"couleur_lig_entete\")' onkeydown=\"clavier_2(this.id,event,0,255);\" autocomplete='off' />\n";
								echo "</td>\n";
							}
							echo "<td id='couleur_lig_entete'";

							echo " onclick=\"document.getElementById('id_couleur_r').value='id_couleur_lig_entete_R';";
							echo "document.getElementById('id_couleur_v').value='id_couleur_lig_entete_V';";
							echo "document.getElementById('id_couleur_b').value='id_couleur_lig_entete_B';";
							echo "document.getElementById('id_couleur_motif').value='couleur_lig_entete';";
							echo "afficher_div('div_choix_couleur','y',10,-200)\">";

							// Champ calculé/mis à jour par la fonction JavaScript calcule_et_valide() lors de la validation du formulaire:
								echo "<input type='hidden' name='couleur_lig_entete' value='couleur_lig_entete' />\n";
							echo "&nbsp;&nbsp;&nbsp;</td>\n";
							echo "<td>\n";
								echo "<a href='#' onclick='reinit_couleurs(\"couleur_lig_entete\");return false;'>Réinitialiser</a>\n";
							echo "</td>\n";
						echo "</tr>\n";

						echo "<tr>\n";
							echo "<td>Couleur de ligne 1";
							echo "</td>\n";
							for($j=0;$j<count($comp);$j++) {
								echo "<td>\n";
									echo "<label for='id_couleur_lig_alt1_".$comp[$j]."' class='invisible'>".$comp[$j]." P lignes paires ".$comp[$j]."</label>\n";
									echo "<input type='text' name='couleur_lig_alt1_".$comp[$j]."' id='id_couleur_lig_alt1_".$comp[$j]."' value='".$tabcouleurs['couleur_lig_alt1'][$comp[$j]]."' size='3' onblur='affichecouleur(\"couleur_lig_alt1\")' onkeydown=\"clavier_2(this.id,event,0,255);\" autocomplete='off' />\n";
								echo "</td>\n";
							}
							echo "<td id='couleur_lig_alt1'";

							echo " onclick=\"document.getElementById('id_couleur_r').value='id_couleur_lig_alt1_R';";
							echo "document.getElementById('id_couleur_v').value='id_couleur_lig_alt1_V';";
							echo "document.getElementById('id_couleur_b').value='id_couleur_lig_alt1_B';";
							echo "document.getElementById('id_couleur_motif').value='couleur_lig_alt1';";
							echo "afficher_div('div_choix_couleur','y',10,-200)\">";

							// Champ calculé/mis à jour par la fonction JavaScript calcule_et_valide() lors de la validation du formulaire:
								echo "<input type='hidden' name='couleur_lig_alt1' value='couleur_lig_alt1' />\n";
							echo "&nbsp;&nbsp;&nbsp;</td>\n";
							echo "<td>\n";
								echo "<a href='#' onclick='reinit_couleurs(\"couleur_lig_alt1\");return false;'>Réinitialiser</a>\n";
							echo "</td>\n";
						echo "</tr>\n";

						echo "<tr>\n";
							echo "<td>Couleur de ligne -1";
							echo "</td>\n";
							for($j=0;$j<count($comp);$j++) {
								echo "<td>\n";
									echo "<label for='id_couleur_lig_alt_1_".$comp[$j]."' class='invisible'>".$comp[$j]." I lignes impaires ".$comp[$j]."</label>\n";
									echo "<input type='text' name='couleur_lig_alt_1_".$comp[$j]."' id='id_couleur_lig_alt_1_".$comp[$j]."' value='".$tabcouleurs['couleur_lig_alt_1'][$comp[$j]]."' size='3' onblur='affichecouleur(\"couleur_lig_alt_1\")' onkeydown=\"clavier_2(this.id,event,0,255);\" autocomplete='off' />\n";
								echo "</td>\n";
							}
							echo "<td id='couleur_lig_alt_1'";

							echo " onclick=\"document.getElementById('id_couleur_r').value='id_couleur_lig_alt_1_R';";
							echo "document.getElementById('id_couleur_v').value='id_couleur_lig_alt_1_V';";
							echo "document.getElementById('id_couleur_b').value='id_couleur_lig_alt_1_B';";
							echo "document.getElementById('id_couleur_motif').value='couleur_lig_alt_1';";
							echo "afficher_div('div_choix_couleur','y',10,-200)\">";

							// Champ calculé/mis à jour par la fonction JavaScript calcule_et_valide() lors de la validation du formulaire:
								echo "<input type='hidden' name='couleur_lig_alt_1' value='couleur_lig_alt_1' />\n";
							echo "&nbsp;&nbsp;&nbsp;</td>\n";
							echo "<td>\n";
								echo "<a href='#' onclick='reinit_couleurs(\"couleur_lig_alt_1\");return false;'>Réinitialiser</a>\n";
							echo "</td>\n";
						echo "</tr>\n";

					echo "</table>\n";
		echo "</div>\n";













		//=========================================

		$tabcouleurs['couleur_fond_postit']=array();
		$couleur_fond_postit=getSettingValue('couleur_fond_postit');
		if($couleur_fond_postit!="") {
			$tabcouleurs['couleur_fond_postit']=tab_rvb($couleur_fond_postit);
		}
		else {
			// #fff5b1
			$tabcouleurs['couleur_fond_postit']['R']=255;
			$tabcouleurs['couleur_fond_postit']['V']=255;
			$tabcouleurs['couleur_fond_postit']['B']=0;
		}


		echo "<h2><strong>Couleurs diverses:</strong></h2>\n";
		echo "<div class='tableau_param_couleur'>\n";

			echo "<table class='tableau_change_couleur' summary=\"Tableau de diverses couleurs\">\n";

				echo "<tr class='fond_blanc'>\n";
					echo "<td class='texte_gras'>Motif</td>\n";
					for($j=0;$j<count($comp);$j++) {
						echo "<td class='texte_gras'>$comp[$j]</td>\n";
					}
					echo "<td class='texte_gras'>Aperçu</td>\n";
					echo "<td class='texte_gras'>Réinitialisation</td>\n";

				echo "</tr>\n";

				echo "<tr>\n";
					echo "<td>Couleur de fond des Messages en Panneau d'affichage";
					echo "</td>\n";
					for($j=0;$j<count($comp);$j++) {
						echo "<td>\n";
							echo "<label for='id_couleur_fond_postit_".$comp[$j]."' class='invisible'>".$comp[$j]." Panneau ".$comp[$j]."</label>\n";
							echo "<input type='text' name='couleur_fond_postit_".$comp[$j]."' id='id_couleur_fond_postit_".$comp[$j]."' value='".$tabcouleurs['couleur_fond_postit'][$comp[$j]]."' size='3' onblur='affichecouleur(\"couleur_fond_postit\")' onkeydown=\"clavier_2(this.id,event,0,255);\" autocomplete='off' />\n";
						echo "</td>\n";
					}
					echo "<td id='couleur_fond_postit'";

					echo " onclick=\"document.getElementById('id_couleur_r').value='id_couleur_fond_postit_R';";
					echo "document.getElementById('id_couleur_v').value='id_couleur_fond_postit_V';";
					echo "document.getElementById('id_couleur_b').value='id_couleur_fond_postit_B';";
					echo "document.getElementById('id_couleur_motif').value='couleur_fond_postit';";
					echo "afficher_div('div_choix_couleur','y',10,-200)\">";

					// Champ calculé/mis à jour par la fonction JavaScript calcule_et_valide() lors de la validation du formulaire:
						echo "<input type='hidden' name='couleur_fond_postit' value='couleur_fond_postit' />\n";
					echo "&nbsp;&nbsp;&nbsp;</td>\n";
					echo "<td>\n";
						echo "<a href='#' onclick='reinit_couleurs(\"couleur_fond_postit\");return false;'>Réinitialiser</a>\n";
					echo "</td>\n";
				echo "</tr>\n";

			echo "</table>\n";
		echo "</div>\n";












/* ===== couleurs du cahier de texte ===== */

	echo "<h2>\n<strong>Notices cahier de textes :</strong>\n</h2>\n";
	echo "<div class='tableau_param_couleur'>\n";
		echo "<input type='checkbox' name='utiliser_cahier_texte_perso' id='utiliser_cahier_texte_perso' value='y' ";
		if(getSettingValue('utiliser_cahier_texte_perso')=='y') {
			echo "checked='checked' ";
		}
		echo "/> ";
		echo "<label for='utiliser_cahier_texte_perso' style='cursor: pointer;'>Couleurs personnalisées dans le cahier de textes.</label>\n";

//=== initialisation des couleurs ===

		// tableaux de noms
		$tab_ct_couleur_fond=array("fond_notices", "entete_fond", "cellule", "cellule_alt");
		$tab_ct_couleur_classe=array("color_fond_notices", "couleur_entete_fond", "couleur_cellule", "couleur_cellule_alt");
		$tab_ct_nom_couleur_fond=array("fond des notices", "entête des notices", "notices", "cellule_alt");
		$tab_ct_notice=array("c","t","i","f");
		$tab_ct_nom_notice=array("Compte rendu de séance","Travail à faire","Informations générales","Rappel des travaux à faire");
		$tab_ct_police_bordure=array("police_travaux","police_matieres","bord_tableau_notice","cellule_gen");
		$tab_ct_nom_police_bordure=array("police des notices travaux","police des matieres","bord des tableaux","Couleur générale des cellules");

// ----- Notices -----

		// Couleurs d'origine
		$tab_ct_couleur_origine=array("fond_notices", "entete_fond", "cellule", "cellule_alt");
		$tab_ct_couleur_origine[]=array("c","t","i","f");
		$tab_ct_couleur_origine[][]=array();
		$tab_ct_couleur_origine["fond_notices"]["c"]="C7FF99";
		$tab_ct_couleur_origine["entete_fond"]["c"]="C7FF99";
		$tab_ct_couleur_origine["cellule"]["c"]="E5FFCF";
		$tab_ct_couleur_origine["cellule_alt"]["c"]="D3FFAF";
		$tab_ct_couleur_origine["fond_notices"]["t"]="FFCCCF";
		$tab_ct_couleur_origine["entete_fond"]["t"]="FFCCCF";
		$tab_ct_couleur_origine["cellule"]["t"]="FFEFF0";
		$tab_ct_couleur_origine["cellule_alt"]["t"]="FFDFE2";
		$tab_ct_couleur_origine["fond_notices"]["i"]="ACACFF";
		//$tab_ct_couleur_origine["entete_fond"]["i"]="EFEFFF";
		$tab_ct_couleur_origine["entete_fond"]["i"]="ACACFF";
		$tab_ct_couleur_origine["cellule"]["i"]="EFEFFF";
		$tab_ct_couleur_origine["cellule_alt"]["i"]="C8C8FF";
		$tab_ct_couleur_origine["fond_notices"]["f"]="FFFF80";
		$tab_ct_couleur_origine["cellule"]["f"]="FFFFDF";

		// Affectation des couleurs
		for($i=0;$i<count($tab_ct_notice);$i++) {
			for($j=0;$j<count($tab_ct_couleur_fond);$j++) {
				$tabcouleurs[$tab_ct_couleur_fond[$j].'_'.$tab_ct_notice[$i]]=array();
				$couleur_traite=getSettingValue($tab_ct_couleur_fond[$j].'_'.$tab_ct_notice[$i]);
				if($couleur_traite!="") {
					$tabcouleurs[$tab_ct_couleur_fond[$j].'_'.$tab_ct_notice[$i]]=tab_rvb($couleur_traite);
				} else {
					if (isset($tab_ct_couleur_origine[$tab_ct_couleur_fond[$j]][$tab_ct_notice[$i]])) {
						$tabcouleurs[$tab_ct_couleur_fond[$j].'_'.$tab_ct_notice[$i]]=tab_rvb($tab_ct_couleur_origine[$tab_ct_couleur_fond[$j]][$tab_ct_notice[$i]]);
					}
				}
			}
		}

		// Tableau de réglage des couleurs
		for($i=0;$i<count($tab_ct_notice);$i++) {
		// Titre de la notice
			echo "<h3>$tab_ct_nom_notice[$i]</h3>";
			echo "<table class='tableau_change_couleur' summary=\"cahier de texte changement de couleur\">\n";
				// entête
				echo "<tr class='fond_blanc'>\n";
					echo "<td class='texte_gras'>Couleur</td>\n";
					for($j=0;$j<count($comp);$j++) {
						echo "<td class='texte_gras'>$comp[$j]</td>\n";
					}
					echo "<td class='texte_gras'>Aperçu</td>\n";
					echo "<td class='texte_gras'>Réinitialisation</td>\n";
				echo "</tr>\n";
				// Données de la couleur
					for($j=0;$j<count($tab_ct_couleur_fond);$j++) {
						if (isset($tab_ct_couleur_origine[$tab_ct_couleur_fond[$j]][$tab_ct_notice[$i]])) {
							echo "<tr>\n";
								echo "<td>".$tab_ct_nom_couleur_fond[$j]."</td>";
								// couleurs RVB
								for($k=0;$k<count($comp);$k++) {
									echo "<td>\n";
								echo "<label for='".$tab_ct_couleur_fond[$j]."_".$tab_ct_notice[$i]."_".$comp[$k]."' class='invisible'>".$comp[$k]." ".$tab_ct_notice[$i]." ".$tab_ct_couleur_fond[$j]."</label>\n";
								echo "<input type='text' name='".$tab_ct_couleur_fond[$j]."_".$tab_ct_notice[$i]."_".$comp[$k]."' id='".$tab_ct_couleur_fond[$j]."_".$tab_ct_notice[$i]."_".$comp[$k]."' value='".$tabcouleurs[$tab_ct_couleur_fond[$j].'_'.$tab_ct_notice[$i]][$comp[$k]]."' size='3' onblur='affichecouleur(\"".$tab_ct_couleur_fond[$j]."_".$tab_ct_notice[$i]."\")' onkeydown=\"clavier_2(this.id,event,0,255);\" autocomplete='off' />\n</td>\n";
								}

								//echo "<td id='".$tab_ct_couleur_fond[$j]."_".$tab_ct_notice[$i]."'>";

								echo "<td id='".$tab_ct_couleur_fond[$j]."_".$tab_ct_notice[$i]."'";

								echo " onclick=\"document.getElementById('id_couleur_r').value='".$tab_ct_couleur_fond[$j]."_".$tab_ct_notice[$i]."_R';";
								echo "document.getElementById('id_couleur_v').value='".$tab_ct_couleur_fond[$j]."_".$tab_ct_notice[$i]."_V';";
								echo "document.getElementById('id_couleur_b').value='".$tab_ct_couleur_fond[$j]."_".$tab_ct_notice[$i]."_B';";
								echo "document.getElementById('id_couleur_motif').value='".$tab_ct_couleur_fond[$j]."_".$tab_ct_notice[$i]."';";
								echo "afficher_div('div_choix_couleur','y',10,-200)\">";


									echo "<input type='hidden' name='".$tab_ct_couleur_fond[$j]."_".$tab_ct_notice[$i]."' value='".$tab_ct_couleur_fond[$j]."_".$tab_ct_notice[$i]."' />\n";
								echo "</td>\n";
								echo "<td>\n";
									echo "<a href='#' onclick='reinit_couleurs(\"".$tab_ct_couleur_fond[$j]."_".$tab_ct_notice[$i]."\");return false;'>Réinitialiser</a>\n";
								echo "</td>\n";
							echo "</tr>\n";
						}
					}
			// Fin nom de la couleur
			echo "</table>\n";
			// Fin nom de la notice
		}
// ----- Fin fonds des notices -----

// ----- Couleurs communes à toutes les notices -----

		// Couleurs d'origine
		$tab_ct_couleur_gen_origine=array("police_travaux","police_matieres","bord_tableau_notice","cellule_gen");
		$tab_ct_couleur_gen_origine["police_travaux"]="FF4444";
		$tab_ct_couleur_gen_origine["police_matieres"]="008000";
		$tab_ct_couleur_gen_origine["bord_tableau_notice"]="6F6968";
		$tab_ct_couleur_gen_origine["cellule_gen"]="F6F7EF";

		// Affectation des couleurs
		for($j=0;$j<count($tab_ct_police_bordure);$j++) {
			$tabcouleurs[$tab_ct_police_bordure[$j]]=array();
			$couleur_traite=getSettingValue($tab_ct_police_bordure[$j]);
			if($couleur_traite!="") {
				$tabcouleurs[$tab_ct_police_bordure[$j]]=tab_rvb($couleur_traite);
			} else {
				if (isset($tab_ct_couleur_gen_origine[$tab_ct_police_bordure[$j]])) {
					$tabcouleurs[$tab_ct_police_bordure[$j]]=tab_rvb($tab_ct_couleur_gen_origine[$tab_ct_police_bordure[$j]]);
				}
			}
		}

		// Tableau de réglage des couleurs
		// Titre de la notice
		echo "<h3>Polices, bordures ...</h3>";
		echo "<table class='tableau_change_couleur' summary=\"cahier de texte changement de couleur\">\n";
			// entête
			echo "<tr class='fond_blanc'>\n";
				echo "<td class='texte_gras'>Couleur</td>\n";
				for($j=0;$j<count($comp);$j++) {
					echo "<td class='texte_gras'>$comp[$j]</td>\n";
				}
				echo "<td class='texte_gras'>Aperçu</td>\n";
				echo "<td class='texte_gras'>Réinitialisation</td>\n";
			echo "</tr>\n";
			// Données de la couleur
			for($i=0;$i<count($tab_ct_police_bordure);$i++) {
				echo "<tr>\n";
					echo "<td>".$tab_ct_nom_police_bordure[$i]."</td>\n";
						// couleurs RVB
						for($j=0;$j<count($comp);$j++) {
							echo "<td>\n";
								echo "<label for='".$tab_ct_police_bordure[$i]."_".$comp[$j]."' class='invisible'>".$tab_ct_police_bordure[$i]."_".$comp[$j]."</label>\n";
								echo "<input type='text' name='".$tab_ct_police_bordure[$i]."_".$comp[$j]."' id='".$tab_ct_police_bordure[$i]."_".$comp[$j]."' value='".$tabcouleurs[$tab_ct_police_bordure[$i]][$comp[$j]]."' size='3' onblur='affichecouleur(\"".$tab_ct_police_bordure[$i]."\")' onkeydown=\"clavier_2(this.id,event,0,255);\" autocomplete='off' />\n";
							echo "</td>\n";
						}
					//echo "<td id='".$tab_ct_police_bordure[$i]."'>\n";
					echo "<td id='".$tab_ct_police_bordure[$i]."'";

					echo " onclick=\"document.getElementById('id_couleur_r').value='".$tab_ct_police_bordure[$i]."_R';";
					echo "document.getElementById('id_couleur_v').value='".$tab_ct_police_bordure[$i]."_V';";
					echo "document.getElementById('id_couleur_b').value='".$tab_ct_police_bordure[$i]."_B';";
					echo "document.getElementById('id_couleur_motif').value='".$tab_ct_police_bordure[$i]."';";
					echo "afficher_div('div_choix_couleur','y',10,-200)\">";



						echo "<input type='hidden' name='".$tab_ct_police_bordure[$i]."' value='".$tab_ct_police_bordure[$i]."' />\n";
					echo "</td>\n";
					echo "<td>\n";
						echo "<a href='#' onclick='reinit_couleurs(\"".$tab_ct_police_bordure[$i]."\");return false;'>Réinitialiser</a>\n";
					echo "</td>\n";
				echo "</tr>\n";
			}
		echo "</table>\n";

// Fin couleurs communes


	echo "</div>\n";

?>
<!-- ====================== Paramétrage du panneau d'affichage ========================== -->	

<?php
		//$tab_style = array('fil_blanc','fil_noir','fil_bleu','fil_vert','filet_noir','filet_noir_2','liege');
		$tab_style = $tab_panneau_affichage;
		$nom_style = array(	'fil_blanc' => 'fil de fer blanc',
							'fil_noir' => 'fil de fer noir',
							'fil_bleu' => 'fil de fer bleu',
							'fil_vert' => 'fil de fer vert',
							'filet_noir' => 'filet noir',
							'filet_noir_2' => 'filet noir avec baguette',
							'liege' => 'plaque de liège'); 
		$selected_style = null; 
		if (getSettingValue('style_panneau_affichage')) {
			$selected_style = getSettingValue('style_panneau_affichage');
		}
?>
	<div style="clear:both;"></div>
	<div class="panneau_affichage">
		<div class="panneau_central">
			<div id="panneau_coingh" class="panneau_coingh"></div>
			<div id="panneau_coindh" class="panneau_coindh"></div>
			<div id="panneau_haut" class="panneau_haut"></div>
			<div id="panneau_droite" class="panneau_droite"></div>
			<div id="panneau_gauche" class="panneau_gauche"></div>
			<div id="panneau_coingb" class="panneau_coingb"></div>
			<div id="panneau_coindb" class="panneau_coindb"></div>
			<div id="panneau_bas" class="panneau_bas"></div>
			<div id="panneau_centre" class="panneau_centre">	
				<div style="margin-bottom:10px;opacity:1;"><strong>Choisissez votre panneau d'affichage</strong></div>
				<div style="text-align:center;opacity:1;">

					<select id="select_panneau_affichage" name="select_panneau_affichage">
						<option value="---">---</option>
						<?php foreach ($tab_style as $style) : ?> 
						<?php if ($style==$selected_style) {
								$display_selected = " selected";
							}
							else {
								$display_selected = "";
							} ?>
						<option value="<?php echo $style; ?>" <?php echo $display_selected; ?>><?php echo $nom_style[$style]; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
		</div>
	</div>	
	<div style="clear:both;"></div>	
<?php
/*============================================*/
/* 	 ça marche, il manque enregistrement    */
/*============================================*/


		//=========================================


	echo "<p class='decale_bas'>\n";
	echo "<input type='hidden' name='is_posted' value='1' />\n";
	// echo "<p style='text-align:center;'><input type='submit' name='ok' value='Valider' /></p>\n";
	echo "<input type='button' name='ok' value='Valider' onclick='calcule_et_valide()' />\n</p>\n";


	echo "<h2><strong>Remarque:</strong></h2>";
	// echo "<blockquote>\n";
		echo "<p>Il peut arriver qu'il faille insister après validation pour que le navigateur recharge bien la page (<em>problème de cache du navigateur</em>).<br />Vous pouvez forcer le rechargement avec CTRL+MAJ+R.</p>\n";
	// echo "</blockquote>\n";

	/*
		//echo "<input type='text' name='truc' value='100' size='3' onkeypress='test_clavier(\"truc\")' />\n";
		//echo "<input type='text' name='truc' value='100' size='3' onkeypress='xKey(\"Event\")' />\n";
		//echo "<input type='text' name='truc' value='100' size='3' onkeypress='xKey(Event)' />\n";
		echo "<input type='text' name='truc' id='id_truc' value='100' size='3' onkeydown=\"clavier_2(this.id,event);\" />\n";
		//echo "<input type='text' name='truc' id='id_truc' value='100' size='3' onKeyPress=\"clavier_2(this.id,event);\" />\n";
	*/
	//echo "<p><br /></p>\n";

	// echo "<div class='centre_texte'>\n";
		echo "<div class='panneau_secour'>\n";
			echo "Le bouton ci-dessous est une 'sécurité'<br />pour réinitialiser les couleurs<br />si jamais vous en arriviez à obtenir quelque chose<br />comme du texte noir sur un fond noir.<br />\n";
			echo "<input type='hidden' name='secu' value='n' />\n";
			//echo "<input type='button' name='reinitialiser' value='Réinitialiser' onclick='reinitialiser()' /></div>\n";
			echo "<input type='button' name='reinitialiser' value='Réinitialiser' onclick='reinit()' />\n";
		echo "</div>\n";
	// echo "</div>\n";

	echo "<script type='text/javascript'>
		setTimeout('init()',500);





// Liste simplifiée
var liste_style=new Array('style_body_backgroundcolor', 'degrade_haut', 'degrade_bas', 'couleur_infobulle_fond_corps', 'couleur_infobulle_fond_entete', 'couleur_lig_entete', 'couleur_lig_alt1', 'couleur_lig_alt_1');

//,'couleur_fond_postit'

function valide_modele(choix) {
	var choix_valide='n';

	// Rose
	if(choix=='rose') {
		choix_valide='y';

		var id_style_body_backgroundcolor_R=250
		var id_style_body_backgroundcolor_V=220
		var id_style_body_backgroundcolor_B=220
		
		// Haut du dégradé
		var id_degrade_haut_R=160
		var id_degrade_haut_V=80
		var id_degrade_haut_B=80
		
		// Bas du dégradé
		var id_degrade_bas_R=200
		var id_degrade_bas_V=80
		var id_degrade_bas_B=80
		
		// Couleur de fond de l'entête des infobulles
		var id_couleur_infobulle_fond_entete_R=200
		var id_couleur_infobulle_fond_entete_V=80
		var id_couleur_infobulle_fond_entete_B=80
		
		// Couleur de fond du corps des infobulles
		var id_couleur_infobulle_fond_corps_R=250
		var id_couleur_infobulle_fond_corps_V=180
		var id_couleur_infobulle_fond_corps_B=180

		// Couleur des lignes alternées
		var id_couleur_entete_R=255
		var id_couleur_entete_V=239
		var id_couleur_entete_B=213

		var id_couleur_lig1_R=255
		var id_couleur_lig1_V=239
		var id_couleur_lig1_B=213

		var id_couleur_lig_1_R=240
		var id_couleur_lig_1_V=255
		var id_couleur_lig_1_B=240
	}
	//=============================================================
	// Vert
	if(choix=='vert') {	
		choix_valide='y';

		var id_style_body_backgroundcolor_R=230
		var id_style_body_backgroundcolor_V=250
		var id_style_body_backgroundcolor_B=230
		
		// Haut du dégradé
		var id_degrade_haut_R=80
		var id_degrade_haut_V=140
		var id_degrade_haut_B=80
		
		// Bas du dégradé
		var id_degrade_bas_R=80
		var id_degrade_bas_V=180
		var id_degrade_bas_B=80
		
		// Couleur de fond de l'entête des infobulles
		var id_couleur_infobulle_fond_entete_R=80
		var id_couleur_infobulle_fond_entete_V=180
		var id_couleur_infobulle_fond_entete_B=80
		
		// Couleur de fond du corps des infobulles
		var id_couleur_infobulle_fond_corps_R=200
		var id_couleur_infobulle_fond_corps_V=250
		var id_couleur_infobulle_fond_corps_B=200

		// Couleur des lignes alternées
		var id_couleur_entete_R=255
		var id_couleur_entete_V=239
		var id_couleur_entete_B=213

		var id_couleur_lig1_R=255
		var id_couleur_lig1_V=239
		var id_couleur_lig1_B=213

		var id_couleur_lig_1_R=240
		var id_couleur_lig_1_V=255
		var id_couleur_lig_1_B=240
	}	
	//=============================================================
	// Bleu
	if(choix=='bleu') {
		choix_valide='y';

		var id_style_body_backgroundcolor_R=230
		var id_style_body_backgroundcolor_V=230
		var id_style_body_backgroundcolor_B=250
		
		// Haut du dégradé
		var id_degrade_haut_R=60
		var id_degrade_haut_V=60
		var id_degrade_haut_B=100
		
		// Bas du dégradé
		var id_degrade_bas_R=80
		var id_degrade_bas_V=80
		var id_degrade_bas_B=160
		
		// Couleur de fond de l'entête des infobulles
		var id_couleur_infobulle_fond_entete_R=80
		var id_couleur_infobulle_fond_entete_V=80
		var id_couleur_infobulle_fond_entete_B=160
		
		// Couleur de fond du corps des infobulles
		var id_couleur_infobulle_fond_corps_R=200
		var id_couleur_infobulle_fond_corps_V=200
		var id_couleur_infobulle_fond_corps_B=250

		// Couleur des lignes alternées
		var id_couleur_entete_R=255
		var id_couleur_entete_V=239
		var id_couleur_entete_B=213

		var id_couleur_lig1_R=255
		var id_couleur_lig1_V=239
		var id_couleur_lig1_B=213

		var id_couleur_lig_1_R=240
		var id_couleur_lig_1_V=255
		var id_couleur_lig_1_B=240
	}
	//=============================================================
	// Chocolat
	if(choix=='chocolat') {
		choix_valide='y';

		var id_style_body_backgroundcolor_R=226
		var id_style_body_backgroundcolor_V=198
		var id_style_body_backgroundcolor_B=170
		
		// Haut du dégradé
		var id_degrade_haut_R=53
		var id_degrade_haut_V=26
		var id_degrade_haut_B=0
		
		// Bas du dégradé
		var id_degrade_bas_R=147
		var id_degrade_bas_V=77
		var id_degrade_bas_B=0
		
		// Couleur de fond de l'entête des infobulles
		var id_couleur_infobulle_fond_entete_R=180
		var id_couleur_infobulle_fond_entete_V=100
		var id_couleur_infobulle_fond_entete_B=0
		
		// Couleur de fond du corps des infobulles
		var id_couleur_infobulle_fond_corps_R=216
		var id_couleur_infobulle_fond_corps_V=180
		var id_couleur_infobulle_fond_corps_B=160

		// Couleur des lignes alternées
		var id_couleur_entete_R=255
		var id_couleur_entete_V=239
		var id_couleur_entete_B=213

		var id_couleur_lig1_R=255
		var id_couleur_lig1_V=239
		var id_couleur_lig1_B=213

		var id_couleur_lig_1_R=240
		var id_couleur_lig_1_V=255
		var id_couleur_lig_1_B=240
	}
	//=============================================================
	if(choix=='ngb') {
		choix_valide='y';

		var id_style_body_backgroundcolor_R=254
		var id_style_body_backgroundcolor_V=254
		var id_style_body_backgroundcolor_B=254
		
		// Haut du dégradé
		var id_degrade_haut_R=29
		var id_degrade_haut_V=29
		var id_degrade_haut_B=29
		
		// Bas du dégradé
		var id_degrade_bas_R=90
		var id_degrade_bas_V=90
		var id_degrade_bas_B=90
		
		// Couleur de fond de l'entête des infobulles
		var id_couleur_infobulle_fond_entete_R=230
		var id_couleur_infobulle_fond_entete_V=230
		var id_couleur_infobulle_fond_entete_B=230
		
		// Couleur de fond du corps des infobulles
		var id_couleur_infobulle_fond_corps_R=180
		var id_couleur_infobulle_fond_corps_V=180
		var id_couleur_infobulle_fond_corps_B=180

		// Couleur des lignes alternées
		var id_couleur_entete_R=90
		var id_couleur_entete_V=90
		var id_couleur_entete_B=90

		var id_couleur_lig1_R=120
		var id_couleur_lig1_V=120
		var id_couleur_lig1_B=120

		var id_couleur_lig_1_R=230
		var id_couleur_lig_1_V=230
		var id_couleur_lig_1_B=230
	}
	//=============================================================

	if(choix_valide=='y') {
		for(i=0;i<liste_style.length;i++) {
			document.getElementById('id_'+liste_style[i]+'_R').value=eval('id_'+liste_style[i]+'_R');
			document.getElementById('id_'+liste_style[i]+'_V').value=eval('id_'+liste_style[i]+'_V');
			document.getElementById('id_'+liste_style[i]+'_B').value=eval('id_'+liste_style[i]+'_B');

			affichecouleur(liste_style[i]);
		}

		document.getElementById('utiliser_couleurs_perso').checked=true;
		document.getElementById('utiliser_degrade').checked=true;
		document.getElementById('utiliser_couleurs_perso_infobulles').checked=true;

		//document.forms['tab'].submit();
		calcule_et_valide();
	}
}

</script>\n<!--noscript></noscript-->";

	/*
	echo "<a href=\"javascript:valide_modele('rose')\">Rose</a><br />";
	echo "<a href=\"javascript:valide_modele('vert')\">Vert</a><br />";
	echo "<a href=\"javascript:valide_modele('bleu')\">Bleu</a><br />";
	*/

?>	
	
	
	
	
	
<?php	
echo "</form>\n";

require("../lib/footer.inc.php");
?>
