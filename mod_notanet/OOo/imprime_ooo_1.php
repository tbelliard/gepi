<?php
/* $Id: imprime_ooo_1.php $ */
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



include_once('../../mod_ooo/lib/tinyButStrong.class.php');
include_once('../../mod_ooo/lib/tinyDoc.class.php');








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
		$nom_fichier_modele_ooo  ='fb_CLG_lv2.ods';
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
  $nom_dossier_modele_a_utiliser="../../mod_ooo/mes_modeles/";
}
else{
  $nom_dossier_modele_a_utiliser="../../mod_ooo/modeles_gepi/";
}

// Création d'une classe tinyDoc
$OOo = new tinyDoc();

// Choix du module de dézippage
$dezippeur=getSettingValue("fb_dezip_ooo");
if ($dezippeur==1){
  $OOo->setZipMethod('shell');
  $OOo->setZipBinary('zip');
  $OOo->setUnzipBinary('unzip');
}
else{
  $OOo->setZipMethod('ziparchive');
}


// setting the object
$OOo->SetProcessDir($nom_dossier_temporaire ); //dossier où se fait le traitement (décompression / traitement / compression)
// create a new openoffice document from the template with an unique id
$OOo->createFrom($nom_dossier_modele_a_utiliser.$nom_fichier_modele_ooo); // le chemin du fichier est indiqué à partir de l'emplacement de ce fichier
// merge data with openoffice file named 'content.xml'
$OOo->loadXml($nom_fichier_xml_a_traiter); //Le fichier qui contient les variables et doit être parsé (il sera extrait)


// Traitement des tableaux
// On insère ici les lignes concernant la gestion des tableaux

// $OOo->mergeXmlBlock('eleves',$tab_eleves_OOo);

$OOo->mergeXml(
	array(
		'name'      => 'eleves',
		'type'      => 'block',
		'data_type' => 'array',
		'charset'   => 'ISO 8859-15'
	 ),$tab_eleves_OOo);

$OOo->SaveXml(); //traitement du fichier extrait


$OOo->sendResponse(); //envoi du fichier traité
$OOo->remove(); //suppression des fichiers de travail
// Fin de traitement des tableaux
$OOo->close();

//=======================================
// FIN AFFICHAGE DES DONNÉES
//=======================================



?>
