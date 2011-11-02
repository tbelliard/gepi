<?php
/* $Id: genere_ods.php 7341 2011-06-27 10:37:37Z crob $ */
/*
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

//======================================================================================

$sql="SELECT 1=1 FROM droits WHERE id='/mod_genese_classes/genere_ods.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_genese_classes/genere_ods.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Génèse des classes: Génération d un fichier ODS de listes',
statut='';";
$insert=mysql_query($sql);
}

//======================================================================================
// Section checkAccess() à décommenter en prenant soin d'ajouter le droit correspondant:
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
//======================================================================================

	function suppr_accents($chaine){
		$caract_accentues=array("à","â","ä","ç","é","è","ê","ë","î","ï","ô","ö","ù","û","ü");
		$caract_sans_accent=array("a","a","a","c","e","e","e","e","i","i","o","o","u","u","u");

		$retour=$chaine;
		for($i=0;$i<count($caract_accentues);$i++){
			$retour=str_replace($caract_accentues[$i],$caract_sans_accent[$i],$retour);
		}
		return $retour;
	}

	$fichier_csv=isset($_POST['fichier_csv']) ? $_POST['fichier_csv'] : (isset($_GET['fichier_csv']) ? $_GET['fichier_csv'] : '');
	$fichier_liste=preg_replace("/\.csv$/","",$fichier_csv);

	//$detail=(isset($_POST['detail'])) ? $_POST['detail'] : '';
	$detail=isset($_POST['detail']) ? $_POST['detail'] : (isset($_GET['detail']) ? $_GET['detail'] : '');

	$projet=isset($_POST['projet']) ? $_POST['projet'] : (isset($_GET['projet']) ? $_GET['projet'] : '');

	$user_temp_directory=get_user_temp_directory();

	//**************** EN-TETE *****************
	$titre_page = "Génèse classe: Fichier ODS";
	//echo "<div class='noprint'>\n";
	require_once("../lib/header.inc");
	//echo "</div>\n";
	//**************** FIN EN-TETE *****************

	echo "<h2>Projet $projet</h2>\n";

	echo "<h3>Génération d'un classeur (<i>ODS</i>)</h3>\n";

	//if(($fichier_csv=='')||(!file_exists("csv/$fichier_csv"))){
	if(($fichier_csv=='')||(!file_exists("../temp/".$user_temp_directory."/$fichier_csv"))){
		echo "<p><b>ERREUR:</b> Aucun fichier CSV n'a été fourni.</p>\n";
		echo "</body>\n</html>\n";
		exit();
	}

	if($detail=="oui"){

		//$fichier_content_xml=fopen("ods/content.xml","w+");
		$fichier_content_xml=fopen("../temp/".$user_temp_directory."/content.xml","w+");
	
		$ecriture=fwrite($fichier_content_xml,'<?xml version="1.0" encoding="UTF-8"?>
<office:document-content xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0" xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0" xmlns:presentation="urn:oasis:names:tc:opendocument:xmlns:presentation:1.0" xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0" xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0" xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0" xmlns:math="http://www.w3.org/1998/Math/MathML" xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0" xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0" xmlns:ooo="http://openoffice.org/2004/office" xmlns:ooow="http://openoffice.org/2004/writer" xmlns:oooc="http://openoffice.org/2004/calc" xmlns:dom="http://www.w3.org/2001/xml-events" xmlns:xforms="http://www.w3.org/2002/xforms" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:field="urn:openoffice:names:experimental:ooxml-odf-interop:xmlns:field:1.0" office:version="1.1"><office:scripts/><office:font-face-decls><style:font-face style:name="Arial" svg:font-family="Arial" style:font-family-generic="swiss" style:font-pitch="variable"/><style:font-face style:name="Bitstream Vera Sans" svg:font-family="&apos;Bitstream Vera Sans&apos;" style:font-family-generic="system" style:font-pitch="variable"/></office:font-face-decls>');
		$ecriture=fwrite($fichier_content_xml,'<office:automatic-styles><style:style style:name="co1" style:family="table-column"><style:table-column-properties fo:break-before="auto" style:column-width="3.604cm"/></style:style><style:style style:name="co2" style:family="table-column"><style:table-column-properties fo:break-before="auto" style:column-width="1.339cm"/></style:style><style:style style:name="co3" style:family="table-column"><style:table-column-properties fo:break-before="auto" style:column-width="0.944cm"/></style:style><style:style style:name="co4" style:family="table-column"><style:table-column-properties fo:break-before="auto" style:column-width="1.208cm"/></style:style><style:style style:name="co5" style:family="table-column"><style:table-column-properties fo:break-before="auto" style:column-width="0.769cm"/></style:style><style:style style:name="co6" style:family="table-column"><style:table-column-properties fo:break-before="auto" style:column-width="2.267cm"/></style:style><style:style style:name="ro1" style:family="table-row"><style:table-row-properties style:row-height="0.453cm" fo:break-before="auto" style:use-optimal-row-height="true"/></style:style><style:style style:name="ta1" style:family="table" style:master-page-name="Default"><style:table-properties table:display="true" style:writing-mode="lr-tb"/></style:style><number:text-style style:name="N100"><number:text-content/></number:text-style><style:style style:name="ce1" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties fo:border-bottom="none" fo:border-left="0.018cm solid #000000" fo:border-right="none" fo:border-top="0.018cm solid #000000"/><style:text-properties fo:font-size="8pt" fo:font-weight="bold" style:font-size-asian="8pt" style:font-weight-asian="bold" style:font-size-complex="8pt" style:font-weight-complex="bold"/></style:style><style:style style:name="ce2" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties fo:border-bottom="0.018cm solid #000000" fo:border-left="0.018cm solid #000000" fo:border-right="none" fo:border-top="none"/><style:text-properties fo:font-size="8pt" style:font-size-asian="8pt" style:font-size-complex="8pt"/></style:style><style:style style:name="ce3" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties fo:border="0.018cm solid #000000"/><style:text-properties fo:font-size="8pt" fo:font-weight="bold" style:font-size-asian="8pt" style:font-weight-asian="bold" style:font-size-complex="8pt" style:font-weight-complex="bold"/></style:style><style:style style:name="ce4" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties fo:border="0.018cm solid #000000"/><style:text-properties fo:font-size="8pt" style:font-size-asian="8pt" style:font-size-complex="8pt"/></style:style><style:style style:name="ce5" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties fo:border="none"/><style:text-properties fo:font-size="8pt" style:font-size-asian="8pt" style:font-size-complex="8pt"/></style:style><style:style style:name="ce6" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties fo:border-bottom="none" fo:border-left="0.018cm solid #000000" fo:border-right="none" fo:border-top="none"/><style:text-properties fo:font-size="8pt" style:font-size-asian="8pt" style:font-size-complex="8pt"/></style:style><style:style style:name="ce7" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties fo:border-bottom="none" style:text-align-source="fix" style:repeat-content="false" fo:border-left="none" fo:border-right="none" fo:border-top="0.018cm solid #000000"/><style:paragraph-properties fo:text-align="center" fo:margin-left="0cm"/><style:text-properties fo:font-size="8pt" style:font-size-asian="8pt" style:font-size-complex="8pt"/></style:style><style:style style:name="ce8" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties fo:border-bottom="0.018cm solid #000000" style:text-align-source="fix" style:repeat-content="false" fo:border-left="none" fo:border-right="none" fo:border-top="none"/><style:paragraph-properties fo:text-align="center" fo:margin-left="0cm"/><style:text-properties fo:font-size="8pt" style:font-size-asian="8pt" style:font-size-complex="8pt"/></style:style><style:style style:name="ce9" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties style:text-align-source="fix" style:repeat-content="false" fo:border="0.018cm solid #000000"/><style:paragraph-properties fo:text-align="center" fo:margin-left="0cm"/><style:text-properties fo:font-size="8pt" fo:font-weight="bold" style:font-size-asian="8pt" style:font-weight-asian="bold" style:font-size-complex="8pt" style:font-weight-complex="bold"/></style:style><style:style style:name="ce10" style:family="table-cell" style:parent-style-name="Default" style:data-style-name="N100"><style:table-cell-properties style:text-align-source="fix" style:repeat-content="false" fo:border="0.018cm solid #000000"/><style:paragraph-properties fo:text-align="center" fo:margin-left="0cm"/><style:text-properties fo:font-size="8pt" fo:font-weight="bold" style:font-size-asian="8pt" style:font-weight-asian="bold" style:font-size-complex="8pt" style:font-weight-complex="bold"/></style:style><style:style style:name="ce11" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties style:text-align-source="fix" style:repeat-content="false" fo:border="0.018cm solid #000000"/><style:paragraph-properties fo:text-align="center" fo:margin-left="0cm"/><style:text-properties fo:font-size="8pt" style:font-size-asian="8pt" style:font-size-complex="8pt"/></style:style><style:style style:name="ce12" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties style:text-align-source="fix" style:repeat-content="false" fo:border="none"/><style:paragraph-properties fo:text-align="center" fo:margin-left="0cm"/><style:text-properties fo:font-size="8pt" style:font-size-asian="8pt" style:font-size-complex="8pt"/></style:style><style:style style:name="ce13" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties style:text-align-source="fix" style:repeat-content="false"/><style:paragraph-properties fo:text-align="center" fo:margin-left="0cm"/></style:style><style:style style:name="ce14" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties style:text-align-source="fix" style:repeat-content="false" fo:border="0.018cm solid #000000"/><style:paragraph-properties fo:text-align="center" fo:margin-left="0cm"/></style:style><style:style style:name="ce15" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties fo:border-bottom="none" style:text-align-source="fix" style:repeat-content="false" fo:border-left="none" fo:border-right="0.018cm solid #000000" fo:border-top="0.018cm solid #000000"/><style:paragraph-properties fo:text-align="center" fo:margin-left="0cm"/><style:text-properties fo:font-size="8pt" style:font-size-asian="8pt" style:font-size-complex="8pt"/></style:style><style:style style:name="ce16" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties fo:border-bottom="0.018cm solid #000000" style:text-align-source="fix" style:repeat-content="false" fo:border-left="none" fo:border-right="0.018cm solid #000000" fo:border-top="none"/><style:paragraph-properties fo:text-align="center" fo:margin-left="0cm"/><style:text-properties fo:font-size="8pt" style:font-size-asian="8pt" style:font-size-complex="8pt"/></style:style><style:style style:name="ce17" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties fo:border="0.018cm solid #000000"/></style:style><style:style style:name="ce18" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties fo:border-bottom="none" style:text-align-source="fix" style:repeat-content="false" fo:border-left="none" fo:border-right="0.018cm solid #000000" fo:border-top="none"/><style:paragraph-properties fo:text-align="center" fo:margin-left="0cm"/><style:text-properties fo:font-size="8pt" style:font-size-asian="8pt" style:font-size-complex="8pt"/></style:style></office:automatic-styles>');
		$ecriture=fwrite($fichier_content_xml,'<office:body><office:spreadsheet>');
		//$ecriture=fwrite($fichier_content_xml,'');

		//$nom_feuillet="Feuillet_1";
		$nom_feuillet=$projet;

		$ecriture=fwrite($fichier_content_xml,'<table:table table:name="'.$nom_feuillet.'" table:style-name="ta1" table:print="false"><office:forms form:automatic-focus="false" form:apply-design-mode="false"/>');

		$ecriture=fwrite($fichier_content_xml,'<table:table-column table:style-name="co1" table:default-cell-style-name="Default"/><table:table-column table:style-name="co2" table:default-cell-style-name="ce13"/><table:table-column table:style-name="co3" table:default-cell-style-name="ce13"/><table:table-column table:style-name="co4" table:default-cell-style-name="ce13"/><table:table-column table:style-name="co2" table:default-cell-style-name="ce13"/><table:table-column table:style-name="co5" table:default-cell-style-name="Default"/>');


		$nb_ptvirg=0;
		//$fich_source_csv=fopen("csv/$fichier_csv","r");
		$fich_source_csv=fopen("../temp/".$user_temp_directory."/$fichier_csv","r");
		while(!feof($fich_source_csv)) {
			$ligne=fgets($fich_source_csv,4096);
			$n=strlen(preg_replace("/[^;]/","",$ligne));
			if($n>$nb_ptvirg) {$nb_ptvirg=$n;}
		}
		$nb_ptvirg=$nb_ptvirg-1; // On supprime le point virgule en fin de ligne

		if($nb_ptvirg<1) {$nb_ptvirg=3;} // Pour éviter des blagues avec des nombres de colonnes négatifs

		$cpt=0;
		//$fich_source_csv=fopen("csv/$fichier_csv","r");
		$fich_source_csv=fopen("../temp/".$user_temp_directory."/$fichier_csv","r");
		if($fich_source_csv){
			while(!feof($fich_source_csv)) {
				$ligne=fgets($fich_source_csv,4096);
	
				// Bricolage pas chouette pour changer le séparateur du CSV
				$ligne_tmp=preg_replace("/°/"," ",preg_replace("/;/",",",preg_replace('/,/',' ',$ligne)));
	
				//$ligne_corrigee=trim(suppr_accents(preg_replace("/'/","&apos;",preg_replace('/"/','',$ligne_tmp))));
				$ligne_corrigee=trim(suppr_accents(preg_replace("/'/","&apos;",preg_replace('/"/','',preg_replace('/°/','Â°',$ligne_tmp)))));
				//echo "<p>\$ligne=$ligne<br>\n";
				//echo "\$ligne_corrigee=$ligne_corrigee</p>\n";
	
				//$tabligne=explode(',',$ligne_corrigee);
				$tabligne=explode(',',$ligne_corrigee);
	
				if($ligne_corrigee=='') {
					// Ligne vide entre deux requetes
					$ecriture=fwrite($fichier_content_xml,'<table:table-row table:style-name="ro1">');
					$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce5"/>');
					$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce12" table:number-columns-repeated="'.$nb_ptvirg.'"/><table:table-cell/>');
					// Le repeated 4 doit correspondre à la situation sans LV3
					$ecriture=fwrite($fichier_content_xml,'</table:table-row>');
				}
				elseif(!isset($tabligne[1])) {
					if(substr($tabligne[0],0,9)=="Requete n") {
						$ecriture=fwrite($fichier_content_xml,'<table:table-row table:style-name="ro1">');
						$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce1" office:value-type="string"><text:p>'.$tabligne[0].'</text:p></table:table-cell>');
						$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce7" table:number-columns-repeated="'.($nb_ptvirg-1).'"/>');
						//$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce15"/><table:table-cell/>');
						$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce15"/>');
						$ecriture=fwrite($fichier_content_xml,'</table:table-row>');
					}
					else {
						// Lignes Avec et sans telles options
						/*
						$ecriture=fwrite($fichier_content_xml,'<table:table-row table:style-name="ro1">');
						$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce2" office:value-type="string"><text:p>'.$tabligne[0].'</text:p></table:table-cell>');
						$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce8" table:number-columns-repeated="'.($nb_ptvirg-1).'"/>');
						//$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce16"/><table:table-cell/>');
						$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce16"/>');
						$ecriture=fwrite($fichier_content_xml,'</table:table-row>');
						*/
						$ecriture=fwrite($fichier_content_xml,'<table:table-row table:style-name="ro1">');
						$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce6" office:value-type="string"><text:p>'.$tabligne[0].'</text:p></table:table-cell>');
						$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce12" table:number-columns-repeated="'.($nb_ptvirg-1).'"/>');
						$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce18"/>');
						$ecriture=fwrite($fichier_content_xml,'</table:table-row>');
					}
				}
				elseif(($tabligne[0]=="Eleve")&&($tabligne[1]=="Clas.act")) {
					$ecriture=fwrite($fichier_content_xml,'<table:table-row table:style-name="ro1">');
					$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce3" office:value-type="string"><text:p>'.$tabligne[0].'</text:p></table:table-cell>');
					$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce9" office:value-type="string"><text:p>'.$tabligne[1].'</text:p></table:table-cell>');
					/*
					if((isset($tabligne[2]))&&($tabligne[2]!="")) {$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce9" office:value-type="string"><text:p>'.$tabligne[2].'</text:p></table:table-cell>');}
					if((isset($tabligne[3]))&&($tabligne[3]!="")) {$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce9" office:value-type="string"><text:p>'.$tabligne[3].'</text:p></table:table-cell>');}
					if((isset($tabligne[4]))&&($tabligne[4]!="")) {$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce9" office:value-type="string"><text:p>'.$tabligne[4].'</text:p></table:table-cell>');}
					*/
					for($j=2;$j<=$nb_ptvirg;$j++) {
						if((isset($tabligne[$j]))&&($tabligne[$j]!="")) {
							$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce9" office:value-type="string"><text:p>'.$tabligne[$j].'</text:p></table:table-cell>');
						}
						else {
							$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce9" office:value-type="string"><text:p>-</text:p></table:table-cell>');
						}
					}
	
					//$ecriture=fwrite($fichier_content_xml,'<table:table-cell/>');
					$ecriture=fwrite($fichier_content_xml,'</table:table-row>');
				}
				elseif(substr($tabligne[0],0,12)=="Eff.select :") {
					$ecriture=fwrite($fichier_content_xml,'<table:table-row table:style-name="ro1">');
					$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce3" office:value-type="string"><text:p>'.$tabligne[0].'</text:p></table:table-cell>');
					$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce10" office:value-type="string"><text:p>'.$tabligne[1].'</text:p></table:table-cell>');
					$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce9" table:number-columns-repeated="'.($nb_ptvirg-1).'"/><table:table-cell/>');
					$ecriture=fwrite($fichier_content_xml,'</table:table-row>');
				}
				else {
					//if(!isset($tabligne[2])) {$tabligne[2]=" ";}
					//if(!isset($tabligne[3])) {$tabligne[3]=" ";}
					//if(!isset($tabligne[4])) {$tabligne[4]=" ";}
	
					$ecriture=fwrite($fichier_content_xml,'<table:table-row table:style-name="ro1">');
					$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce4" office:value-type="string"><text:p>'.$tabligne[0].'</text:p></table:table-cell>');
					$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce11" office:value-type="string"><text:p>'.$tabligne[1].'</text:p></table:table-cell>');
					//for($j=2;$j<=count($tabligne);$j++) {
					for($j=2;$j<=$nb_ptvirg;$j++) {
						if((isset($tabligne[$j]))&&($tabligne[$j]!="")) {
							$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce11" office:value-type="string"><text:p>'.$tabligne[$j].'</text:p></table:table-cell>');
						}
						else {
							$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce11" office:value-type="string"><text:p>-</text:p></table:table-cell>');
						}
					}
					/*
					for($j=count($tabligne)+1;$j<=$nb_ptvirg;$j++) {
						$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce12" office:value-type="string"><text:p></text:p></table:table-cell>');
					}
					*/
					//if((isset($tabligne[2]))&&($tabligne[2]!="")) {$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce12" office:value-type="string"><text:p>'.$tabligne[2].'</text:p></table:table-cell>');}
					//if((isset($tabligne[3]))&&($tabligne[3]!="")) {$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce12" office:value-type="string"><text:p>'.$tabligne[3].'</text:p></table:table-cell>');}
					//if((isset($tabligne[4]))&&($tabligne[4]!="")) {$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce12" office:value-type="string"><text:p>'.$tabligne[4].'</text:p></table:table-cell>');}
					//$ecriture=fwrite($fichier_content_xml,'<table:table-cell/>');
	
					//$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce17"/>');
					$ecriture=fwrite($fichier_content_xml,'</table:table-row>');
				}
	
				$cpt++;
			}
	
			$nb_lig_fin=65536-$cpt-19;
	
			$ecriture=fwrite($fichier_content_xml,'<table:table-row table:style-name="ro1" table:number-rows-repeated="'.$nb_lig_fin.'">');
			//$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:number-columns-repeated="'.($nb_ptvirg+2).'"/>');
			$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:number-columns-repeated="'.($nb_ptvirg+1).'"/>');
			$ecriture=fwrite($fichier_content_xml,'</table:table-row>');
	
			$ecriture=fwrite($fichier_content_xml,'<table:table-row table:style-name="ro1">');
			//$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:number-columns-repeated="'.($nb_ptvirg+2).'"/>');
			$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:number-columns-repeated="'.($nb_ptvirg+1).'"/>');
			$ecriture=fwrite($fichier_content_xml,'</table:table-row>');
		
			$ecriture=fwrite($fichier_content_xml,'</table:table><table:table table:name="Feuille2" table:style-name="ta1" table:print="false"><table:table-column table:style-name="co6" table:default-cell-style-name="Default"/><table:table-row table:style-name="ro1"><table:table-cell/></table:table-row></table:table><table:table table:name="Feuille3" table:style-name="ta1" table:print="false"><table:table-column table:style-name="co6" table:default-cell-style-name="Default"/><table:table-row table:style-name="ro1"><table:table-cell/></table:table-row></table:table></office:spreadsheet></office:body></office:document-content>');
			$fermeture=fclose($fich_source_csv);
			$fermeture=fclose($fichier_content_xml);
			
			set_time_limit(3000);
			//require_once("ss_zip.class.php");
			require_once("../lib/ss_zip.class.php");
		
			$zip= new ss_zip('',6);
			//$zip->add_file('sxc/content.xml');
			//$zip->add_file("ods/content.xml",'content.xml');
			$zip->add_file("../temp/".$user_temp_directory."/content.xml",'content.xml');
			$zip->add_file('ods/meta.xml','meta.xml');
			$zip->add_file('ods/mimetype','mimetype');
			$zip->add_file('ods/settings.xml','settings.xml');
			$zip->add_file('ods/styles.xml','styles.xml');
			$zip->add_file('ods/META-INF/manifest.xml','META-INF/manifest.xml');
			//$zip->save("ods/$fichier_liste.zip");
			$zip->save("../temp/".$user_temp_directory."/$fichier_liste.zip");
	
			//rename("ods/$fichier_liste.zip","ods/$fichier_liste.ods");
			//rename("../temp/".$user_temp_directory."/$fichier_liste.zip","../temp/".$user_temp_directory."/$fichier_liste.ods");
			rename("../temp/".$user_temp_directory."/$fichier_liste.zip","../temp/".$user_temp_directory."/".$fichier_liste."_detail.ods");
	
			//echo "<a href='ods/".$fichier_liste.".ods'>$fichier_liste.ods</a>\n";
			//echo "<p>Fichier&nbsp;: <a href='../temp/".$user_temp_directory."/".$fichier_liste.".ods' onclick=\"setTimeout('self.close()',3000);return true;\">$fichier_liste.ods</a></p>\n";
			echo "<p>Fichier&nbsp;: <a href='../temp/".$user_temp_directory."/".$fichier_liste."_detail.ods' onclick=\"setTimeout('self.close()',3000);return true;\">".$fichier_liste."_detail.ods</a></p>\n";
		}
		else{
			echo "<p>Erreur lors de l'ouverture du fichier CSV.</p>\n";

			$fermeture=fclose($fichier_content_xml);
		}
	}
	else{

		// ATTENTION: LA SUITE N'EST PAS CORRIGEE DU TOUT... C'EST UN MODELE DIFFERENT
		//echo "<p>Modèle sans détails non encore géré.</p>";
		//die();

		//$fichier_content_xml=fopen("ods/content.xml","w+");
		$fichier_content_xml=fopen("../temp/".$user_temp_directory."/content.xml","w+");

		$ecriture=fwrite($fichier_content_xml,'<?xml version="1.0" encoding="UTF-8"?>
<office:document-content xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0" xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0" xmlns:presentation="urn:oasis:names:tc:opendocument:xmlns:presentation:1.0" xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0" xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0" xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0" xmlns:math="http://www.w3.org/1998/Math/MathML" xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0" xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0" xmlns:ooo="http://openoffice.org/2004/office" xmlns:ooow="http://openoffice.org/2004/writer" xmlns:oooc="http://openoffice.org/2004/calc" xmlns:dom="http://www.w3.org/2001/xml-events" xmlns:xforms="http://www.w3.org/2002/xforms" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:field="urn:openoffice:names:experimental:ooxml-odf-interop:xmlns:field:1.0" office:version="1.1"><office:scripts/><office:font-face-decls><style:font-face style:name="Arial" svg:font-family="Arial" style:font-family-generic="swiss" style:font-pitch="variable"/><style:font-face style:name="Bitstream Vera Sans" svg:font-family="&apos;Bitstream Vera Sans&apos;" style:font-family-generic="system" style:font-pitch="variable"/></office:font-face-decls>');

		$ecriture=fwrite($fichier_content_xml,'<office:automatic-styles><style:style style:name="co1" style:family="table-column"><style:table-column-properties fo:break-before="auto" style:column-width="3.604cm"/></style:style><style:style style:name="co2" style:family="table-column"><style:table-column-properties fo:break-before="auto" style:column-width="1.339cm"/></style:style><style:style style:name="co3" style:family="table-column"><style:table-column-properties fo:break-before="auto" style:column-width="0.944cm"/></style:style><style:style style:name="co4" style:family="table-column"><style:table-column-properties fo:break-before="auto" style:column-width="1.208cm"/></style:style><style:style style:name="co5" style:family="table-column"><style:table-column-properties fo:break-before="auto" style:column-width="0.769cm"/></style:style><style:style style:name="co6" style:family="table-column"><style:table-column-properties fo:break-before="auto" style:column-width="2.267cm"/></style:style><style:style style:name="ro1" style:family="table-row"><style:table-row-properties style:row-height="0.453cm" fo:break-before="auto" style:use-optimal-row-height="true"/></style:style><style:style style:name="ta1" style:family="table" style:master-page-name="Default"><style:table-properties table:display="true" style:writing-mode="lr-tb"/></style:style><number:text-style style:name="N100"><number:text-content/></number:text-style><style:style style:name="ce1" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties fo:border-bottom="none" fo:border-left="0.018cm solid #000000" fo:border-right="0.018cm solid #000000" fo:border-top="0.018cm solid #000000"/><style:text-properties fo:font-size="8pt" fo:font-weight="bold" style:font-size-asian="8pt" style:font-weight-asian="bold" style:font-size-complex="8pt" style:font-weight-complex="bold"/></style:style><style:style style:name="ce2" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties fo:border-bottom="none" fo:border-left="0.018cm solid #000000" fo:border-right="0.018cm solid #000000" fo:border-top="none"/><style:text-properties fo:font-size="8pt" style:font-size-asian="8pt" style:font-size-complex="8pt"/></style:style><style:style style:name="ce3" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties fo:border="0.018cm solid #000000"/><style:text-properties fo:font-size="8pt" fo:font-weight="bold" style:font-size-asian="8pt" style:font-weight-asian="bold" style:font-size-complex="8pt" style:font-weight-complex="bold"/></style:style><style:style style:name="ce4" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties fo:border="0.018cm solid #000000"/><style:text-properties fo:font-size="8pt" style:font-size-asian="8pt" style:font-size-complex="8pt"/></style:style><style:style style:name="ce5" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties fo:border="none"/><style:text-properties fo:font-size="8pt" style:font-size-asian="8pt" style:font-size-complex="8pt"/></style:style><style:style style:name="ce6" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties style:text-align-source="fix" style:repeat-content="false" fo:border="none"/><style:paragraph-properties fo:text-align="center" fo:margin-left="0cm"/><style:text-properties fo:font-size="8pt" style:font-size-asian="8pt" style:font-size-complex="8pt"/></style:style><style:style style:name="ce7" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties style:text-align-source="fix" style:repeat-content="false" fo:border="none"/><style:paragraph-properties fo:text-align="center" fo:margin-left="0cm"/><style:text-properties fo:font-size="8pt" fo:font-weight="bold" style:font-size-asian="8pt" style:font-weight-asian="bold" style:font-size-complex="8pt" style:font-weight-complex="bold"/></style:style><style:style style:name="ce8" style:family="table-cell" style:parent-style-name="Default" style:data-style-name="N100"><style:table-cell-properties style:text-align-source="fix" style:repeat-content="false" fo:border="none"/><style:paragraph-properties fo:text-align="center" fo:margin-left="0cm"/><style:text-properties fo:font-size="8pt" fo:font-weight="bold" style:font-size-asian="8pt" style:font-weight-asian="bold" style:font-size-complex="8pt" style:font-weight-complex="bold"/></style:style><style:style style:name="ce9" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties style:text-align-source="fix" style:repeat-content="false"/><style:paragraph-properties fo:text-align="center" fo:margin-left="0cm"/></style:style></office:automatic-styles>');

		$ecriture=fwrite($fichier_content_xml,'<office:body><office:spreadsheet>');
	
		//$nom_feuillet="Feuillet_1";
		$nom_feuillet=$projet;
	
		$ecriture=fwrite($fichier_content_xml,'<table:table table:name="'.$nom_feuillet.'" table:style-name="ta1" table:print="false">');
	
		$ecriture=fwrite($fichier_content_xml,'<office:forms form:automatic-focus="false" form:apply-design-mode="false"/><table:table-column table:style-name="co1" table:default-cell-style-name="Default"/><table:table-column table:style-name="co2" table:default-cell-style-name="ce9"/><table:table-column table:style-name="co3" table:default-cell-style-name="ce9"/><table:table-column table:style-name="co4" table:default-cell-style-name="ce9"/><table:table-column table:style-name="co2" table:default-cell-style-name="ce9"/><table:table-column table:style-name="co5" table:default-cell-style-name="Default"/>');
	
		$cpt=0;
		//$fich_source_csv=fopen("csv/$fichier_csv","r");
		$fich_source_csv=fopen("../temp/".$user_temp_directory."/$fichier_csv","r");
		if($fich_source_csv) {
			while(!feof($fich_source_csv)){
				$ligne=fgets($fich_source_csv,4096);
	
				// Bricolage pas chouette pour changer le séparateur du CSV
				$ligne_tmp=preg_replace("/°/"," ",preg_replace("/;/",",",preg_replace('/,/',' ',$ligne)));
	
				//$ligne_corrigee=trim(suppr_accents(preg_replace("/'/","&apos;",preg_replace('/"/','',$ligne_tmp))));
				$ligne_corrigee=trim(suppr_accents(preg_replace("/'/","&apos;",preg_replace('/"/','',preg_replace('/°/','Â°',$ligne_tmp)))));
				//echo "<p>\$ligne=$ligne<br>\n";
				//echo "\$ligne_corrigee=$ligne_corrigee</p>\n";
	
				//$tabligne=explode(',',$ligne_corrigee);
				$tabligne=explode(',',$ligne_corrigee);
	
				if($ligne_corrigee=='') {
					// Ligne vide entre deux requetes
					$ecriture=fwrite($fichier_content_xml,'<table:table-row table:style-name="ro1">');
					$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce5"/>');
					$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce6" table:number-columns-repeated="4"/><table:table-cell/>');
					$ecriture=fwrite($fichier_content_xml,'</table:table-row>');
				}
				elseif(!isset($tabligne[1])) {
					if(substr($tabligne[0],0,9)=="Requete n") {
						$ecriture=fwrite($fichier_content_xml,'<table:table-row table:style-name="ro1">');
						$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce1" office:value-type="string"><text:p>'.$tabligne[0].'</text:p></table:table-cell>');
						$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce6" table:number-columns-repeated="4"/><table:table-cell/>');
						$ecriture=fwrite($fichier_content_xml,'</table:table-row>');
					}
					else {
						// Lignes Avec et sans telles options
	
						$ecriture=fwrite($fichier_content_xml,'<table:table-row table:style-name="ro1">');
						$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce2" office:value-type="string"><text:p>'.$tabligne[0].'</text:p></table:table-cell>');
						$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce6" table:number-columns-repeated="4"/><table:table-cell/>');
						$ecriture=fwrite($fichier_content_xml,'</table:table-row>');
	
					}
				}
				elseif(($tabligne[0]=="Eleve")&&($tabligne[1]=="Clas.act")) {
					$ecriture=fwrite($fichier_content_xml,'<table:table-row table:style-name="ro1">');
					$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce3" office:value-type="string"><text:p>'.$tabligne[0].'</text:p></table:table-cell>');
					$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce7" table:number-columns-repeated="4"/><table:table-cell/>');
					$ecriture=fwrite($fichier_content_xml,'</table:table-row>');
				}
				elseif(substr($tabligne[0],0,12)=="Eff.select :") {
					$ecriture=fwrite($fichier_content_xml,'<table:table-row table:style-name="ro1">');
					$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce3" office:value-type="string"><text:p>'.$tabligne[0].'</text:p></table:table-cell>');
					$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce8"/>');
					$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce7" table:number-columns-repeated="3"/><table:table-cell/>');
					$ecriture=fwrite($fichier_content_xml,'</table:table-row>');
				}
				else {
					$ecriture=fwrite($fichier_content_xml,'<table:table-row table:style-name="ro1">');
					$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce4" office:value-type="string"><text:p>'.$tabligne[0].'</text:p></table:table-cell>');
					$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce6" table:number-columns-repeated="4"/><table:table-cell/>');
					$ecriture=fwrite($fichier_content_xml,'</table:table-row>');
				}
	
				$cpt++;
			}
			//$ecriture=fwrite($fichier_content_xml,'</table:table></office:body></office:document-content>');
			//$fermeture=fclose($fich_source_csv);
			//$fermeture=fclose($fichier_content_xml);
	
	
			$nb_lig_fin=65536-$cpt-19;
	
			$ecriture=fwrite($fichier_content_xml,'<table:table-row table:style-name="ro1" table:number-rows-repeated="2">');
			$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce5"/>');
			$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce6" table:number-columns-repeated="4"/><table:table-cell/>');
			$ecriture=fwrite($fichier_content_xml,'</table:table-row>');
	
			$ecriture=fwrite($fichier_content_xml,'<table:table-row table:style-name="ro1" table:number-rows-repeated="'.$nb_lig_fin.'">');
			$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:number-columns-repeated="6"/>');
			$ecriture=fwrite($fichier_content_xml,'</table:table-row>');
	
			$ecriture=fwrite($fichier_content_xml,'<table:table-row table:style-name="ro1">');
			$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:number-columns-repeated="6"/>');
			$ecriture=fwrite($fichier_content_xml,'</table:table-row>');
		
			$ecriture=fwrite($fichier_content_xml,'</table:table>');
		
			$ecriture=fwrite($fichier_content_xml,'<table:table table:name="Feuille2" table:style-name="ta1" table:print="false"><table:table-column table:style-name="co6" table:default-cell-style-name="Default"/><table:table-row table:style-name="ro1"><table:table-cell/></table:table-row></table:table><table:table table:name="Feuille3" table:style-name="ta1" table:print="false"><table:table-column table:style-name="co6" table:default-cell-style-name="Default"/><table:table-row table:style-name="ro1"><table:table-cell/></table:table-row></table:table></office:spreadsheet></office:body></office:document-content>');
			$fermeture=fclose($fich_source_csv);
			$fermeture=fclose($fichier_content_xml);
			
	
			set_time_limit(3000);
			//require_once("ss_zip.class.php");
			require_once("../lib/ss_zip.class.php");
		
			$zip= new ss_zip('',6);
			//$zip->add_file('sxc/content.xml');
			//$zip->add_file("ods/content.xml",'content.xml');
			$zip->add_file("../temp/".$user_temp_directory."/content.xml",'content.xml');
			$zip->add_file('ods/meta.xml','meta.xml');
			$zip->add_file('ods/mimetype','mimetype');
			$zip->add_file('ods/settings.xml','settings.xml');
			$zip->add_file('ods/styles.xml','styles.xml');
			$zip->add_file('ods/META-INF/manifest.xml','META-INF/manifest.xml');
			//$zip->save("ods/$fichier_liste.zip");
			$zip->save("../temp/".$user_temp_directory."/$fichier_liste.zip");
	
			//rename("ods/$fichier_liste.zip","ods/$fichier_liste.ods");
			rename("../temp/".$user_temp_directory."/$fichier_liste.zip","../temp/".$user_temp_directory."/$fichier_liste.ods");
	
			//echo "<a href='ods/".$fichier_liste.".ods'>$fichier_liste.ods</a>\n";
			//echo "<a href='../temp/".$user_temp_directory."/".$fichier_liste.".ods'>$fichier_liste.ods</a>\n";
			echo "<p>Fichier&nbsp;: <a href='../temp/".$user_temp_directory."/".$fichier_liste.".ods' onclick=\"setTimeout('self.close()',3000);return true;\">$fichier_liste.ods</a></p>\n";
		}
		else{
			echo "<p>Erreur lors de l'ouverture du fichier CSV.</p>\n";

			$fermeture=fclose($fichier_content_xml);
		}
	}
?>

</body>
</html>
