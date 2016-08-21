<?php
/* $Id: imprime_ooo_2.php $ */
/*
* Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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



include_once('../../mod_ooo/lib/lib_mod_ooo.php');
include_once('../../tbs/tbs_class.php');
include_once('../../mod_ooo/lib/tbsooo_class.php');
//define( 'PCLZIP_TEMPORARY_DIR', $tempdir );
define( 'PCLZIP_TEMPORARY_DIR', '../../temp/' );
include_once('../../lib/pclzip.lib.php');





//*****************************************************************************************************************************************


//=======================================
// AFFICHAGE DES DONNÉES
//=======================================

// Et maintenant on s'occupe du fichier proprement dit

//
//Les variables à modifier pour le traitement  du modèle ooo
//
//Le chemin et le nom du fichier ooo à traiter (le modèle de document)
switch($type_brevet){
	case '0':
		//$nom_fichier_modele_ooo  ='fb_CLG_lv2.ods';
		$nom_fichier_modele_ooo  ='fb_serie_generale.ods';
// Collège LV2
	break;
	case '1':
		$nom_fichier_modele_ooo ='fb_CLG_dp6.ods';
// Collège DP6
	break;
	case '2':
		$nom_fichier_modele_ooo ='fb_PRO.ods';
// Professionnel sans option
	break;
	case '3':
		$nom_fichier_modele_ooo ='fb_PRO_dp6.ods';
// Professionnel DP6
	break;
	case '4':
		$nom_fichier_modele_ooo ='fb_PRO_agri.ods';
// Professionnel agricole
	break;
	case '5':
		$nom_fichier_modele_ooo  ='fb_TECHNO.ods';
// Technologique sans option
	break;
	case '6':
		$nom_fichier_modele_ooo ='fb_TECHNO_dp6.ods';
// Technologique DP6
	break;
	case '7':
		$nom_fichier_modele_ooo ='fb_TECHNO_agri.ods';
// Technologique agricole
	break;
	default:
	die();
}

// Par defaut tmp
$tempdirOOo="../../temp/".$tempdir;
$nom_dossier_temporaire = $tempdirOOo;
//par defaut content.xml
$nom_fichier_xml_a_traiter ='content.xml';
//les chemins contenant les données
$fb_gab_perso=getSettingValue("fb_gab_perso");
if($fb_gab_perso=="1"){ 
  // Gestion du multisite
  if ($_SESSION['rne']!='') {
	$rne=$_SESSION['rne']."/";
  } else {
	$rne='';
  }
  $nom_dossier_modele_a_utiliser="../../mod_ooo/mes_modeles/".$rne;
}
else{
  $nom_dossier_modele_a_utiliser="../../mod_ooo/modeles_gepi/";
}
// TODO vérifier les chemins comme /mod_ooo/lib/chemin.inc.php

// Création d'une classe  TBS OOo class
$OOo = new clsTinyButStrongOOo;
$OOo->SetDataCharset('UTF-8');

// setting the object
$OOo->SetProcessDir($nom_dossier_temporaire ); //dossier où se fait le traitement (décompression / traitement / compression)
// create a new openDocument document from the template with an unique id
//$OOo->createFrom($nom_dossier_modele_a_utiliser.$nom_fichier_modele_ooo); // le chemin du fichier est indiqué à partir de l'emplacement de ce fichier
// create a new openDocument document from the template with an unique id 
$OOo->NewDocFromTpl($nom_dossier_modele_a_utiliser.$nom_fichier_modele_ooo); // le chemin du fichier est indiqué à partir de l'emplacement de ce fichier
// merge data with openDocument file named 'content.xml'
//$OOo->loadXml($nom_fichier_xml_a_traiter); //Le fichier qui contient les variables et doit être parsé (il sera extrait)
$OOo->LoadXmlFromDoc($nom_fichier_xml_a_traiter); //Le fichier qui contient les variables et doit être parsé (il sera extrait)


// Traitement des tableaux
// On insère ici les lignes concernant la gestion des tableaux

$OOo->MergeBlock('eleves',$tab_eleves_OOo);
$OOo->SaveXmlToDoc(); //traitement du fichier extrait

//Génération du nom du fichier
$now = gmdate('d_M_Y_H:i:s');
$nom_fichier_modele = explode('.',$nom_fichier_modele_ooo);
$nom_fic = $nom_fichier_modele[0]."_".$now.".".$nom_fichier_modele[1];
/*
header('Expires: ' . $now);
if (my_ereg('MSIE', $_SERVER['HTTP_USER_AGENT'])) {
	header('Content-Disposition: inline; filename="' . $nom_fic . '"');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
}
else {
	header('Content-Disposition: attachment; filename="' . $nom_fic . '"');
	header('Pragma: no-cache');
}
// display
*/
send_file_download_headers($OOo->GetMimetypeDoc(), $nom_fic, 'attachment');
//header('Content-type: '.$OOo->GetMimetypeDoc());
//header('Content-type: '.$OOo->getMimetype());
header('Content-Length: '.filesize($OOo->GetPathnameDoc()));
//header('Content-Length: '.filesize($OOo->GetPathname()));


$OOo->FlushDoc(); //envoi du fichier traité
$OOo->RemoveDoc(); //suppression des fichiers de travail

//$OOo->sendResponse(); //envoi du fichier traité
$OOo->remove(); //suppression des fichiers de travail
// Fin de traitement des tableaux
$OOo->close();

//=======================================
// FIN AFFICHAGE DES DONNÉES
//=======================================



?>
