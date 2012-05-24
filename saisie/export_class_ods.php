<?php
/*
 *
 * Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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


if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}


//On vérifie si le module est activé
if(getSettingValue("export_cn_ods")!='y') {
    die("L'export ODS n'est pas activé.");
}


unset($nettoyage);
$nettoyage=isset($_GET["nettoyage"]) ? $_GET["nettoyage"] : NULL;


$user_tmp=get_user_temp_directory();
if(!$user_tmp){
	$msg="Votre dossier temporaire n'est pas accessible.";
    header("Location: index.php?msg=".rawurlencode($msg));
    die();
}

//$chemin_temp="../temp/".getSettingValue("temp_directory");
$chemin_temp="../temp/".$user_tmp;

$chemin_modele_ods="export_note_app_modele_ods";

if(isset($nettoyage)){
	if(!my_ereg(".ods$",$nettoyage)){
		$msg="Le fichier n'est pas d'extension ODS.";
	}
	elseif(!my_ereg("^".$_SESSION['login'],$nettoyage)){
		$msg="Vous tentez de supprimer des fichiers qui ne vous appartiennent pas.";
	}
	else{
		if(mb_strlen(my_ereg_replace("[a-zA-Z0-9_.]","",strtr($nettoyage,"-","_")))!=0){
			$msg="Le fichier proposé n'est pas valide: '".my_ereg_replace("[a-zA-Z0-9_.]","",strtr($nettoyage,"-","_"))."'";
		}
		else{
			if(!file_exists("$chemin_temp/$nettoyage")){
				$msg="Le fichier choisi n'existe pas.";
			}
			else{
				unlink("$chemin_temp/$nettoyage");
				$msg=rawurlencode("Suppression réussie!");
			}
		}
	}

    header("Location: index.php?msg=$msg");
    die();
}






unset($id_groupe);
//$id_groupe=isset($_POST["id_groupe"]) ? $_POST["id_groupe"] : (isset($_GET["id_groupe"]) ? $_GET["id_groupe"] : NULL);
//$periode_num=isset($_POST["periode_num"]) ? $_POST["periode_num"] : (isset($_GET["periode_num"]) ? $_GET["periode_num"] : NULL);
$id_groupe=isset($_POST["id_groupe"]) ? $_POST["id_groupe"] : (isset($_GET["id_groupe"]) ? $_GET["id_groupe"] : 'ERREUR');
unset($periode_num);
$periode_num=isset($_POST["periode_num"]) ? $_POST["periode_num"] : (isset($_GET["periode_num"]) ? $_GET["periode_num"] : 'ERREUR');



if((mb_strlen(my_ereg_replace("[0-9]","",$id_groupe))!=0)||(mb_strlen(my_ereg_replace("[0-9]","",$periode_num))!=0)){
	$msg="Une au moins des valeurs id_groupe ou periode_num est invalide.";
    header("Location: index.php?msg=$msg");
    die();
}



// On teste si le professeur est bien associé au groupe
if($_SESSION['statut']!='secours') {
	$sql="SELECT 1=1 FROM j_groupes_professeurs WHERE login='".$_SESSION['login']."' AND id_groupe='$id_groupe'";
	$res_test=mysql_query($sql);
	if (mysql_num_rows($res_test)==0) {
		$mess=rawurlencode("Vous tentez d'accéder à des données qui ne vous appartiennent pas !");
		header("Location: index.php?msg=$mess");
		die();
	}
}
//
// On dispose donc pour la suite de deux variables :
// id_groupe
// periode_num



$current_group = get_group($id_groupe);
$id_classe = $current_group["classes"]["list"][0];

if (count($current_group["classes"]["list"]) > 1) {
    $multiclasses = true;
} else {
    $multiclasses = false;
    $order_by = "nom";
}


include "../lib/periodes.inc.php";


$matiere_nom = $current_group["matiere"]["nom_complet"];
$matiere_nom_court = $current_group["matiere"]["matiere"];
$nom_classe = $current_group["classlist_string"];

$periode_query = mysql_query("SELECT * FROM periodes WHERE id_classe = '$id_classe' ORDER BY num_periode");
$nom_periode = mysql_result($periode_query, $periode_num-1, "nom_periode");


//**************** EN-TETE *****************
$titre_page = "Export des notes/appréciations";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

$nom_fic=$_SESSION['login'];
$nom_fic.="_notes_appreciations";
$nom_fic.="_".my_ereg_replace("[^a-zA-Z0-9_. - ]","",remplace_accents($current_group['description'],'all'));
$nom_fic.="_".my_ereg_replace("[^a-zA-Z0-9_. - ]","",remplace_accents($current_group["classlist_string"],'all'));
$nom_fic.="_".my_ereg_replace("[^a-zA-Z0-9_. - ]","",remplace_accents($nom_periode,'all'));


// Génération d'un fichier tableur ODS

$instant=getdate();
$heure=$instant['hours'];
$minute=$instant['minutes'];
$seconde=$instant['seconds'];
$mois=$instant['mon'];
$jour=$instant['mday'];
$annee=$instant['year'];
$chaine_tmp="$annee-".sprintf("%02d",$mois)."-".sprintf("%02d",$jour)."-".sprintf("%02d",$heure)."-".sprintf("%02d",$minute)."-".sprintf("%02d",$seconde);

$nom_fic.="_".$chaine_tmp.".ods";



// Fichier content.xml
//echo "\$tmp_fich=$tmp_fich<br />\n";
$tmp_fich="content_".$_SESSION['login'];
//echo "\$tmp_fich=$tmp_fich<br />\n";
$tmp_fich.="_".strtr(microtime()," ","_");
//echo "\$tmp_fich=$tmp_fich<br />\n";
$tmp_fich.=".xml";
//echo "\$tmp_fich=$tmp_fich<br />\n";

$tmp_fich=$chemin_temp."/".$tmp_fich;

$fichier_tmp_xml=fopen("$tmp_fich","w+");
/*
$ecriture=fwrite($fichier_tmp_xml,'<?xml version="1.0" encoding="UTF-8"?>
<office:document-content xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0" xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0" xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0" xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0" xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0" xmlns:math="http://www.w3.org/1998/Math/MathML" xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0" xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0" xmlns:ooo="http://openoffice.org/2004/office" xmlns:ooow="http://openoffice.org/2004/writer" xmlns:oooc="http://openoffice.org/2004/calc" xmlns:dom="http://www.w3.org/2001/xml-events" xmlns:xforms="http://www.w3.org/2002/xforms" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" office:version="1.0"><office:scripts/><office:font-face-decls><style:font-face style:name="DejaVu Sans" svg:font-family="&apos;DejaVu Sans&apos;" style:font-family-generic="swiss" style:font-pitch="variable"/><style:font-face style:name="DejaVu Sans1" svg:font-family="&apos;DejaVu Sans&apos;" style:font-family-generic="system" style:font-pitch="variable"/></office:font-face-decls><office:automatic-styles><style:style style:name="co1" style:family="table-column"><style:table-column-properties fo:break-before="auto" style:column-width="2.701cm"/></style:style><style:style style:name="co2" style:family="table-column"><style:table-column-properties fo:break-before="auto" style:column-width="2.267cm"/></style:style><style:style style:name="co3" style:family="table-column"><style:table-column-properties fo:break-before="auto" style:column-width="11.957cm"/></style:style><style:style style:name="co4" style:family="table-column"><style:table-column-properties fo:break-before="auto" style:column-width="11.319cm"/></style:style><style:style style:name="ro1" style:family="table-row"><style:table-row-properties style:row-height="0.843cm" fo:break-before="auto" style:use-optimal-row-height="false"/></style:style><style:style style:name="ro2" style:family="table-row"><style:table-row-properties style:row-height="1.041cm" fo:break-before="auto" style:use-optimal-row-height="false"/></style:style><style:style style:name="ro3" style:family="table-row"><style:table-row-properties style:row-height="0.436cm" fo:break-before="auto" style:use-optimal-row-height="true"/></style:style><style:style style:name="ro4" style:family="table-row"><style:table-row-properties style:row-height="1.277cm" fo:break-before="auto" style:use-optimal-row-height="true"/></style:style><style:style style:name="ta1" style:family="table" style:master-page-name="Default"><style:table-properties table:display="true" style:writing-mode="lr-tb"/></style:style><style:style style:name="ce1" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties style:vertical-align="middle"/><style:text-properties fo:font-weight="bold"/></style:style><style:style style:name="ce2" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties style:text-align-source="value-type" style:repeat-content="false" style:vertical-align="top"/></style:style><style:style style:name="ce3" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties style:text-align-source="fix" style:repeat-content="false" style:vertical-align="middle"/><style:paragraph-properties fo:text-align="center" fo:margin-left="0cm"/><style:text-properties fo:font-weight="bold"/></style:style><style:style style:name="ce4" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties style:text-align-source="value-type" style:repeat-content="false" style:vertical-align="top"/><style:paragraph-properties fo:margin-left="0cm"/></style:style><style:style style:name="ce5" style:family="table-cell" style:parent-style-name="Default" style:data-style-name="N0"><style:table-cell-properties style:text-align-source="value-type" style:repeat-content="false" style:vertical-align="top"/><style:paragraph-properties fo:margin-left="0cm"/></style:style><style:style style:name="ce6" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties style:text-align-source="fix" style:repeat-content="false"/><style:paragraph-properties fo:text-align="center" fo:margin-left="0cm"/></style:style><style:style style:name="ce7" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties style:text-align-source="value-type" style:repeat-content="false" fo:wrap-option="wrap" style:vertical-align="top"/><style:text-properties style:font-name="DejaVu Sans" style:font-name-asian="DejaVu Sans1" style:font-name-complex="DejaVu Sans1"/></style:style><style:style style:name="ce8" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties fo:wrap-option="wrap"/></style:style><style:style style:name="P1" style:family="paragraph"><style:paragraph-properties fo:text-align="center"/></style:style></office:automatic-styles>');
*/
$ecriture=fwrite($fichier_tmp_xml,'<?xml version="1.0" encoding="UTF-8"?>
<office:document-content xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0" xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0" xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0" xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0" xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0" xmlns:math="http://www.w3.org/1998/Math/MathML" xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0" xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0" xmlns:ooo="http://openoffice.org/2004/office" xmlns:ooow="http://openoffice.org/2004/writer" xmlns:oooc="http://openoffice.org/2004/calc" xmlns:dom="http://www.w3.org/2001/xml-events" xmlns:xforms="http://www.w3.org/2002/xforms" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" office:version="1.0"><office:scripts/><office:font-face-decls><style:font-face style:name="DejaVu Sans" svg:font-family="&apos;DejaVu Sans&apos;" style:font-family-generic="swiss" style:font-pitch="variable"/><style:font-face style:name="DejaVu Sans1" svg:font-family="&apos;DejaVu Sans&apos;" style:font-family-generic="system" style:font-pitch="variable"/></office:font-face-decls><office:automatic-styles><style:style style:name="co1" style:family="table-column"><style:table-column-properties fo:break-before="auto" style:column-width="2.701cm"/></style:style><style:style style:name="co2" style:family="table-column"><style:table-column-properties fo:break-before="auto" style:column-width="2.267cm"/></style:style><style:style style:name="co3" style:family="table-column"><style:table-column-properties fo:break-before="auto" style:column-width="11.957cm"/></style:style><style:style style:name="co4" style:family="table-column"><style:table-column-properties fo:break-before="auto" style:column-width="17.26cm"/></style:style><style:style style:name="co5" style:family="table-column"><style:table-column-properties fo:break-before="auto" style:column-width="2.208cm"/></style:style><style:style style:name="ro1" style:family="table-row"><style:table-row-properties style:row-height="0.843cm" fo:break-before="auto" style:use-optimal-row-height="false"/></style:style><style:style style:name="ro2" style:family="table-row"><style:table-row-properties style:row-height="1.041cm" fo:break-before="auto" style:use-optimal-row-height="false"/></style:style><style:style style:name="ro3" style:family="table-row"><style:table-row-properties style:row-height="0.436cm" fo:break-before="auto" style:use-optimal-row-height="true"/></style:style><style:style style:name="ro4" style:family="table-row"><style:table-row-properties style:row-height="0.459cm" fo:break-before="auto" style:use-optimal-row-height="true"/></style:style><style:style style:name="ro5" style:family="table-row"><style:table-row-properties style:row-height="0.868cm" fo:break-before="auto" style:use-optimal-row-height="true"/></style:style><style:style style:name="ro6" style:family="table-row"><style:table-row-properties style:row-height="0.485cm" fo:break-before="auto" style:use-optimal-row-height="false"/></style:style><style:style style:name="ro7" style:family="table-row"><style:table-row-properties style:row-height="1.277cm" fo:break-before="auto" style:use-optimal-row-height="true"/></style:style><style:style style:name="ta1" style:family="table" style:master-page-name="Default"><style:table-properties table:display="true" style:writing-mode="lr-tb"/></style:style><style:style style:name="ce1" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties style:vertical-align="middle"/><style:text-properties fo:font-weight="bold"/></style:style><style:style style:name="ce2" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties style:text-align-source="value-type" style:repeat-content="false" style:vertical-align="top"/></style:style><style:style style:name="ce3" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties style:text-align-source="fix" style:repeat-content="false" style:vertical-align="middle"/><style:paragraph-properties fo:text-align="center" fo:margin-left="0cm"/><style:text-properties fo:font-weight="bold"/></style:style><style:style style:name="ce4" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties style:text-align-source="value-type" style:repeat-content="false" style:vertical-align="top"/><style:paragraph-properties fo:margin-left="0cm"/></style:style><style:style style:name="ce5" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties style:text-align-source="fix" style:repeat-content="false"/><style:paragraph-properties fo:text-align="center" fo:margin-left="0cm"/></style:style><style:style style:name="ce6" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties style:text-align-source="fix" style:repeat-content="false" fo:wrap-option="wrap" style:vertical-align="middle"/><style:paragraph-properties fo:text-align="start"/><style:text-properties fo:font-weight="bold"/></style:style><style:style style:name="ce7" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties style:text-align-source="fix" style:repeat-content="false" fo:wrap-option="wrap" style:vertical-align="top"/><style:paragraph-properties fo:text-align="start"/></style:style><style:style style:name="ce8" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties style:text-align-source="fix" style:repeat-content="false" fo:wrap-option="wrap"/><style:paragraph-properties fo:text-align="start"/></style:style><style:style style:name="ce9" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties style:text-align-source="fix" style:repeat-content="false" fo:wrap-option="wrap"/><style:paragraph-properties fo:text-align="center" fo:margin-left="0cm"/><style:text-properties fo:font-weight="bold"/></style:style><style:style style:name="ce10" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties style:text-align-source="value-type" style:repeat-content="false" fo:wrap-option="wrap" style:vertical-align="top"/><style:text-properties style:font-name="DejaVu Sans" style:font-name-asian="DejaVu Sans1" style:font-name-complex="DejaVu Sans1"/></style:style><style:style style:name="ce11" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties fo:wrap-option="wrap"/></style:style><style:style style:name="P1" style:family="paragraph"><style:paragraph-properties fo:text-align="center"/></style:style></office:automatic-styles>');

//$ecriture=fwrite($fichier_tmp_xml,'</office:automatic-styles><office:body><office:spreadsheet><table:table table:name="Notes" table:style-name="ta1" table:print="false">');

/*
$ecriture=fwrite($fichier_tmp_xml,'<office:body><office:spreadsheet><table:table table:name="Note_App" table:style-name="ta1" table:print="false"><office:forms form:automatic-focus="false" form:apply-design-mode="false"><form:form form:name="Standard" form:apply-filter="true" form:command-type="table" form:control-implementation="ooo:com.sun.star.form.component.Form" office:target-frame="" xlink:href=""><form:properties><form:property form:property-name="GroupBy" office:value-type="string" office:string-value=""/><form:property form:property-name="HavingClause" office:value-type="string" office:string-value=""/><form:property form:property-name="MaxRows" office:value-type="float" office:value="0"/><form:property form:property-name="UpdateCatalogName" office:value-type="string" office:string-value=""/><form:property form:property-name="UpdateSchemaName" office:value-type="string" office:string-value=""/><form:property form:property-name="UpdateTableName" office:value-type="string" office:string-value=""/></form:properties><form:button form:name="PushButton" form:control-implementation="ooo:com.sun.star.form.component.CommandButton" form:id="control1" form:label="Export CSV" office:target-frame="" xlink:href="" form:image-data="" form:delay-for-repeat="PT0.50S" form:image-position="center"><form:properties><form:property form:property-name="DefaultControl" office:value-type="string" office:string-value="com.sun.star.form.control.CommandButton"/></form:properties><office:event-listeners><script:event-listener script:language="ooo:script" script:event-name="form:performaction" xlink:href="vnd.sun.star.script:Standard.Export_CSV.Main?language=Basic&amp;location=document"/></office:event-listeners></form:button></form:form></office:forms><table:table-column table:style-name="co1" table:default-cell-style-name="Default"/><table:table-column table:style-name="co2" table:default-cell-style-name="ce6"/><table:table-column table:style-name="co3" table:default-cell-style-name="Default"/><table:table-row table:style-name="ro1"><table:table-cell table:style-name="ce1" office:value-type="string"><text:p>IDENTIFIANT</text:p></table:table-cell><table:table-cell table:style-name="ce3" office:value-type="string"><text:p>NOTE</text:p></table:table-cell><table:table-cell table:style-name="ce1" office:value-type="string"><text:p>APPRECIATION</text:p><draw:control table:end-cell-address="Note_App.C1" table:end-x="8.356cm" table:end-y="0.727cm" draw:z-index="0" draw:text-style-name="P1" svg:width="2.356cm" svg:height="0.674cm" svg:x="5.999cm" svg:y="0.052cm" draw:control="control1"/></table:table-cell></table:table-row>');
*/

$ecriture=fwrite($fichier_tmp_xml,'<office:body><office:spreadsheet><table:table table:name="Note_App" table:style-name="ta1" table:print="false"><office:forms form:automatic-focus="false" form:apply-design-mode="false"><form:form form:name="Standard" form:apply-filter="true" form:command-type="table" form:control-implementation="ooo:com.sun.star.form.component.Form" office:target-frame="" xlink:href=""><form:properties><form:property form:property-name="GroupBy" office:value-type="string" office:string-value=""/><form:property form:property-name="HavingClause" office:value-type="string" office:string-value=""/><form:property form:property-name="MaxRows" office:value-type="float" office:value="0"/><form:property form:property-name="UpdateCatalogName" office:value-type="string" office:string-value=""/><form:property form:property-name="UpdateSchemaName" office:value-type="string" office:string-value=""/><form:property form:property-name="UpdateTableName" office:value-type="string" office:string-value=""/></form:properties><form:button form:name="PushButton" form:control-implementation="ooo:com.sun.star.form.component.CommandButton" form:id="control1" form:label="Export CSV" office:target-frame="" xlink:href="" form:image-data="" form:delay-for-repeat="PT0.50S" form:image-position="center"><form:properties><form:property form:property-name="DefaultControl" office:value-type="string" office:string-value="com.sun.star.form.control.CommandButton"/></form:properties><office:event-listeners><script:event-listener script:language="ooo:script" script:event-name="form:performaction" xlink:href="vnd.sun.star.script:Standard.Export_CSV.Main?language=Basic&amp;location=document"/></office:event-listeners></form:button></form:form></office:forms><table:table-column table:style-name="co1" table:default-cell-style-name="Default"/><table:table-column table:style-name="co2" table:default-cell-style-name="ce5"/><table:table-column table:style-name="co3" table:default-cell-style-name="ce8"/><table:table-row table:style-name="ro1"><table:table-cell table:style-name="ce1" office:value-type="string"><text:p>IDENTIFIANT</text:p></table:table-cell><table:table-cell table:style-name="ce3" office:value-type="string"><text:p>NOTE</text:p></table:table-cell><table:table-cell table:style-name="ce6" office:value-type="string"><text:p>APPRECIATION</text:p><draw:control table:end-cell-address="Note_App.C1" table:end-x="8.356cm" table:end-y="0.727cm" draw:z-index="0" draw:text-style-name="P1" svg:width="2.356cm" svg:height="0.674cm" svg:x="5.999cm" svg:y="0.052cm" draw:control="control1"/></table:table-cell></table:table-row>');





$sql="SELECT login FROM j_eleves_groupes WHERE id_groupe='$id_groupe' AND periode='$periode_num' ORDER BY login";
$res_ele=mysql_query($sql);
$nb_ele=mysql_num_rows($res_ele);
if($nb_ele>0){
	while($lig_ele=mysql_fetch_object($res_ele)){
		$ecriture=fwrite($fichier_tmp_xml,'<table:table-row table:style-name="ro2"><table:table-cell table:style-name="ce2" office:value-type="string"><text:p>'.$lig_ele->login.'</text:p></table:table-cell>');

		$sql="SELECT note,statut FROM matieres_notes WHERE login='$lig_ele->login' AND periode='$periode_num' AND id_groupe='$id_groupe'";
		$res_note=mysql_query($sql);
		if(mysql_num_rows($res_note)){
			$lig_note=mysql_fetch_object($res_note);
			if($lig_note->statut==''){
				$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell table:style-name="ce4" office:value-type="float" office:value="'.my_ereg_replace(',','.',$lig_note->note).'"><text:p>'.my_ereg_replace('.',',',$lig_note->note).'</text:p></table:table-cell>');

				// ATTENTION: Il va falloir vérifier qu'à l'import on gère bien 13.5 et 13,5
			}
			else{
				$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell table:style-name="ce4" office:value-type="string"><text:p>'.$lig_note->statut.'</text:p></table:table-cell>');
			}
		}
		else{
			$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell table:style-name="ce4"/>');
		}


		$sql="SELECT appreciation FROM matieres_appreciations WHERE login='$lig_ele->login' AND periode='$periode_num' AND id_groupe='$id_groupe'";
		$res_appreciation=mysql_query($sql);
		if(mysql_num_rows($res_appreciation)){
			$lig_appreciation=mysql_fetch_object($res_appreciation);
			$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell table:style-name="ce7" office:value-type="string"><text:p>'.nl2br($lig_appreciation->appreciation).'</text:p></table:table-cell></table:table-row>');


			// Il doit falloir remplacer les accents par leur valeur en UTF8
		}
		else{
			//$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell table:style-name="ce2"/></table:table-row>');
			$ecriture=fwrite($fichier_tmp_xml,'<table:table-cell table:style-name="ce7"/></table:table-row>');
		}
	}
}

$nb_lig_restantes=65534-$nb_ele;

$ecriture=fwrite($fichier_tmp_xml,'<table:table-row table:style-name="ro3" table:number-rows-repeated="'.$nb_lig_restantes.'"><table:table-cell table:number-columns-repeated="3"/></table:table-row><table:table-row table:style-name="ro3"><table:table-cell table:number-columns-repeated="3"/></table:table-row></table:table>');


//$ecriture=fwrite($fichier_tmp_xml,'<table:table table:name="Infos" table:style-name="ta1" table:print="false"><table:table-column table:style-name="co2" table:default-cell-style-name="Default"/><table:table-column table:style-name="co4" table:default-cell-style-name="Default"/><table:table-row table:style-name="ro3"><table:table-cell table:number-columns-repeated="2"/></table:table-row><table:table-row table:style-name="ro4"><table:table-cell/><table:table-cell table:style-name="ce7" office:value-type="string"><text:p>Vous devez saisir les appréciations sur une seule ligne (dans une seule cellule) et ne pas y saisir de point-virgule. Ce qui suit le point-virgule ne serait pas évalué lors de l&apos;export CSV.</text:p></table:table-cell></table:table-row></table:table></office:spreadsheet></office:body></office:document-content>');

$ecriture=fwrite($fichier_tmp_xml,'<table:table table:name="Infos" table:style-name="ta1" table:print="false"><table:table-column table:style-name="co4" table:default-cell-style-name="Default"/><table:table-column table:style-name="co5" table:default-cell-style-name="ce11"/><table:table-row table:style-name="ro4"><table:table-cell table:style-name="ce9" office:value-type="string"><text:p>INFORMATIONS</text:p></table:table-cell><table:table-cell table:style-name="Default"/></table:table-row><table:table-row table:style-name="ro3"><table:table-cell/><table:table-cell table:style-name="Default"/></table:table-row><table:table-row table:style-name="ro5"><table:table-cell table:style-name="ce10" office:value-type="string"><text:p>Vous devez saisir les appréciations sur une seule ligne (dans une seule cellule) et ne pas y saisir de point-virgule. Ce qui suit le point-virgule ne serait pas évalué lors de l&apos;export CSV.</text:p></table:table-cell><table:table-cell table:style-name="Default"/></table:table-row><table:table-row table:style-name="ro6"><table:table-cell table:style-name="ce11"/><table:table-cell table:style-name="Default"/></table:table-row><table:table-row table:style-name="ro7"><table:table-cell table:style-name="ce11" office:value-type="string"><text:p>Lors de la génération de l&apos;export CSV, les points-virgules sont remplacés par des chaînes de caractères __POINT-VIRGULE__, d&apos;autres caractères susceptibles de causer des soucis sont également recherchés et traités.</text:p></table:table-cell><table:table-cell table:style-name="Default"/></table:table-row><table:table-row table:style-name="ro5"><table:table-cell table:style-name="ce11" office:value-type="string"><text:p>L&apos;absence de ces caractères peut provoquer des affichages sans conséquences (Â«Â Terme recherché introuvableÂ Â»). C&apos;est normal et sans conséquences, contentez-vous de cliquer sur OK.</text:p></table:table-cell><table:table-cell table:style-name="Default"/></table:table-row><table:table-row table:style-name="ro3"><table:table-cell table:style-name="ce11"/><table:table-cell table:style-name="Default"/></table:table-row><table:table-row table:style-name="ro5"><table:table-cell table:style-name="ce11" office:value-type="string"><text:p>Lors de la génération de l&apos;export CSV, le feuillet Note_App est enregistré au format CSV. Cela provoque un avertissement comme quoi seul ce feuillet est enregistré dans le CSV. C&apos;est normal.</text:p></table:table-cell><table:table-cell table:style-name="Default"/></table:table-row><table:table-row table:style-name="ro4"><table:table-cell table:style-name="ce11" office:value-type="string"><text:p>Pour finir, le fichier ODS est réenregistré si bien que toutes les informations sont conservées.</text:p></table:table-cell><table:table-cell table:style-name="Default"/></table:table-row><table:table-row table:style-name="ro3"><table:table-cell table:style-name="ce11"/><table:table-cell table:style-name="Default"/></table:table-row><table:table-row table:style-name="ro4"><table:table-cell table:style-name="ce9" office:value-type="string"><text:p>MACROS</text:p></table:table-cell><table:table-cell table:style-name="Default"/></table:table-row><table:table-row table:style-name="ro3"><table:table-cell table:style-name="ce11"/><table:table-cell table:style-name="Default"/></table:table-row><table:table-row table:style-name="ro5"><table:table-cell table:style-name="ce11" office:value-type="string"><text:p>Il se peut que vous ayez droit à un avertissement concernant la présence de macros dans ce fichier. C&apos;est normal. Ces macros servent à générer le fichier CSV.</text:p></table:table-cell><table:table-cell table:style-name="Default"/></table:table-row><table:table-row table:style-name="ro3"><table:table-cell table:style-name="ce11"/><table:table-cell table:style-name="Default"/></table:table-row><table:table-row table:style-name="ro7"><table:table-cell table:style-name="ce11" office:value-type="string"><text:p>Il peut aussi arriver que vous ne puissiez pas utiliser ces macros parce que le niveau de sécurité d&apos;OpenOffice serait trop élevé. Vous pouvez alors ajuster le niveau de sécurité dans le menu Outils/Options/OpenOffice.org/Sécurité/Sécurité des macros</text:p></table:table-cell><table:table-cell table:style-name="Default"/></table:table-row><table:table-row table:style-name="ro3" table:number-rows-repeated="65520"><table:table-cell table:number-columns-repeated="2"/></table:table-row><table:table-row table:style-name="ro3"><table:table-cell table:number-columns-repeated="2"/></table:table-row></table:table></office:spreadsheet></office:body></office:document-content>');

$fermeture=fclose($fichier_tmp_xml);




if(file_exists("../lib/ss_zip.class.php")){
	//set_time_limit(3000);
	require_once("../lib/ss_zip.class.php");

	$zip= new ss_zip('',6);
	//$zip->add_file('sxc/content.xml');
	$zip->add_file("$tmp_fich",'content.xml');

	// On n'ajoute pas les dossiers, ni les fichiers vides... ss_zip ne le supporte pas...
	// ... et OpenOffice a l'air de supporter l'absence de ces dossiers/fichiers.


	/*
	Configurations2/accelerator/current.xml
	META-INF/manifest.xml
	settings.xml
	Basic/script-lc.xml
	Basic/Standard/script-lb.xml
	Basic/Standard/Export_CSV.xml
	meta.xml
	Thumbnails/thumbnail.png
	mimetype
	styles.xml
	content.xml
	*/


	$zip->add_file($chemin_modele_ods.'/Basic/script-lc.xml', 'Basic/script-lc.xml');
	$zip->add_file($chemin_modele_ods.'/Basic/Standard/script-lb.xml', 'Basic/Standard/script-lb.xml');
	$zip->add_file($chemin_modele_ods.'/Basic/Standard/Export_CSV.xml', 'Basic/Standard/Export_CSV.xml');

	// On ne met pas ce fichier parce que sa longueur vide fait une blague pour ss_zip.
	//$zip->add_file($chemin_modele_ods.'/Configurations2/accelerator/current.xml', 'Configurations2/accelerator/current.xml');

	$zip->add_file($chemin_modele_ods.'/META-INF/manifest.xml', 'META-INF/manifest.xml');
	$zip->add_file($chemin_modele_ods.'/settings.xml', 'settings.xml');
	$zip->add_file($chemin_modele_ods.'/meta.xml', 'meta.xml');
	//$zip->add_file($chemin_modele_ods.'/modele_ods/Thumbnails', 'Thumbnails');
	$zip->add_file($chemin_modele_ods.'/Thumbnails/thumbnail.png', 'Thumbnails/thumbnail.png');
	$zip->add_file($chemin_modele_ods.'/mimetype', 'mimetype');
	$zip->add_file($chemin_modele_ods.'/styles.xml', 'styles.xml');

	$zip->save("$tmp_fich.zip");


	if(file_exists("$chemin_temp/$nom_fic")){unlink("$chemin_temp/$nom_fic");}
	//rename("$tmp_fich.zip","$chemin_modele_ods/$chaine_tmp.$nom_fic");
	rename("$tmp_fich.zip","$chemin_temp/$nom_fic");

	// Suppression du fichier content...xml
	unlink($tmp_fich);
}
else {

	$path = path_niveau();
	$chemin_temp = $path."temp/".get_user_temp_directory()."/";

	if (!defined('PCLZIP_TEMPORARY_DIR') || constant('PCLZIP_TEMPORARY_DIR')!=$chemin_temp) {
		@define( 'PCLZIP_TEMPORARY_DIR', $chemin_temp);
	}

	$chemin_stockage = $chemin_temp."/".$nom_fic;

	$dossier_a_traiter=$chemin_temp."export_app_".strftime("%Y%m%d%H%M%S");
	@mkdir($dossier_a_traiter);
	copy($tmp_fich, $dossier_a_traiter."/content.xml");

	@mkdir($dossier_a_traiter."/Basic");
	@mkdir($dossier_a_traiter."/Basic/Standard");
	@mkdir($dossier_a_traiter."/META-INF");
	@mkdir($dossier_a_traiter."/Thumbnails");

	$tab_fich_tmp=array('Basic/script-lc.xml', 'Basic/Standard/script-lb.xml', '/Basic/Standard/Export_CSV.xml', 'META-INF/manifest.xml', 'settings.xml', 'meta.xml', 'Thumbnails/thumbnail.png', 'mimetype', 'styles.xml');
	for($loop=0;$loop<count($tab_fich_tmp);$loop++) {
		copy($chemin_modele_ods.'/'.$tab_fich_tmp[$loop], $dossier_a_traiter."/".$tab_fich_tmp[$loop]);
	}

	require_once($path.'lib/pclzip.lib.php');

	if ($chemin_stockage !='') {
		if(file_exists("$chemin_stockage")) {unlink("$chemin_stockage");}

		//echo "\$chemin_stockage=$chemin_stockage<br />";
		//echo "\$dossier_a_traiter=$dossier_a_traiter<br />";

		$archive = new PclZip($chemin_stockage);
		$v_list = $archive->create($dossier_a_traiter,
			  PCLZIP_OPT_REMOVE_PATH,$dossier_a_traiter,
			  PCLZIP_OPT_ADD_PATH, '');

		if ($v_list == 0) {
			echo "<p style='color:red'>Erreur : ".$archive->errorInfo(TRUE)."</p>";
		}
		/*
		else {
			$msg="Archive zip créée&nbsp;: <a href='$chemin_stockage'>$chemin_stockage</a>";
		}
		*/

		deltree($dossier_a_traiter);
	}

}



//$titre=htmlspecialchars($current_group['description'])." (".$nom_periode.")";
$titre=htmlspecialchars($current_group['name']." ".$current_group["classlist_string"]." (".$nom_periode.")");
$titre.=" - EXPORT";

// Mettre la ligne de liens de retour,...
echo "<div class='norme'><p class='bold'>\n";
echo "<a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil </a>|\n";
echo "<a href='index.php?id_groupe=".$current_group["id"]."&amp;periode_num=$periode_num'> ".htmlspecialchars($current_group['name']." ".$current_group["classlist_string"]." (".$nom_periode.")")." </a>|\n";
echo "</div>\n";


echo "<h2>$titre</h2>\n";

echo "<p>Télécharger: <a href='$chemin_temp/$nom_fic'>$nom_fic</a></p>\n";

/*
echo "filetype($chemin_modele_ods/$nom_fic)=".filetype("$chemin_modele_ods/$nom_fic")."<br />\n";

$fp=fopen("$chemin_modele_ods/$nom_fic","r");
$ligne=fgets($fp, 4096);
fclose($fp);

echo "<pre>$ligne</pre>";
*/

// AJOUTER UN LIEN POUR FAIRE LE MENAGE... et permettre à l'admin de faire le ménage.
echo "<p>Pour ne pas encombrer inutilement le serveur et par soucis de confidentialité, il est recommandé de supprimer le fichier du serveur après récupération du fichier ci-dessus.<br />\n";
echo "<a href='".$_SERVER['PHP_SELF']."?nettoyage=$nom_fic'>Supprimer le fichier</a>.";
//echo "<a href='".$_SERVER['PHP_SELF']."?nettoyage=".$nom_fic."_truc'>Supprimer le fichier 2</a>.";
//echo "<a href='".$_SERVER['PHP_SELF']."?nettoyage=_truc".$nom_fic."'>Supprimer le fichier 3</a>.";
echo "</p>\n";



require("../lib/footer.inc.php");
?>
