<?php
/*
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

//======================================================================================

$sql="SELECT 1=1 FROM droits WHERE id='/mod_genese_classes/liste_options.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_genese_classes/liste_options.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Genèse des classes: Liste des options de classes existantes',
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

$projet=isset($_POST['projet']) ? $_POST['projet'] : (isset($_GET['projet']) ? $_GET['projet'] : NULL);
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : NULL;

#for i in nom prenom sexe naissance login elenoet ele_id no_gep email matiere;do echo "\$$i=isset(\$_POST['$i']) ? \$_POST['$i'] : NULL;";done
$nom=isset($_POST['nom']) ? $_POST['nom'] : NULL;
$prenom=isset($_POST['prenom']) ? $_POST['prenom'] : NULL;
$sexe=isset($_POST['sexe']) ? $_POST['sexe'] : NULL;
$naissance=isset($_POST['naissance']) ? $_POST['naissance'] : NULL;
$champ_login=isset($_POST['champ_login']) ? $_POST['champ_login'] : NULL;
$elenoet=isset($_POST['elenoet']) ? $_POST['elenoet'] : NULL;
$ele_id=isset($_POST['ele_id']) ? $_POST['ele_id'] : NULL;
$no_gep=isset($_POST['no_gep']) ? $_POST['no_gep'] : NULL;
$email=isset($_POST['email']) ? $_POST['email'] : NULL;
$classe=isset($_POST['classe']) ? $_POST['classe'] : NULL;
$matiere=isset($_POST['matiere']) ? $_POST['matiere'] : NULL;

//==========================================
function LETTRE_COLONNE($num_col) {
	$alpha='ABCDEFGHIJKLMNOPQRSTUVWXYZ';

	if($num_col<26) {
		$n=$num_col-1;
		return mb_substr($alpha,$n,1);
	}
	else {
		return "";
	}
}

function suppr_accents($chaine){
	$caract_accentues=array("à","â","ä","ç","é","è","ê","ë","î","ï","ô","ö","ù","û","ü");
	$caract_sans_accent=array("a","a","a","c","e","e","e","e","i","i","o","o","u","u","u");

	$retour=$chaine;
	for($i=0;$i<count($caract_accentues);$i++){
		$retour=str_replace($caract_accentues[$i],$caract_sans_accent[$i],$retour);
	}
	return $retour;
}
//==========================================

$tab_champs=array('nom', 'prenom', 'sexe', 'naissance', 'champ_login', 'elenoet', 'ele_id', 'no_gep', 'email', 'classe');

//if(isset($_POST['choix_param'])) {
if(isset($_POST['valider_param'])) {
	if((!isset($_POST['type_export']))||($_POST['type_export']!='ods')) {
		$fich="";
		$ligne_entete="";
		$cpt=0;
		for($i=0;$i<count($id_classe);$i++) {
			$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE jec.login=e.login AND jec.id_classe='$id_classe[$i]' ORDER BY nom,prenom;";
			//$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE jec.login=e.login AND jec.id_classe='$id_classe[$i]' AND (e.date_sortie IS NULL OR e.date_sortie NOT LIKE '20%') ORDER BY nom,prenom;";
			$res=mysql_query($sql);
			while($lig=mysql_fetch_object($res)) {
				$ligne="";
	
				if((isset($nom))&&($nom=='y')) {
					$ligne.=$lig->nom.";";
					if($cpt==0) {$ligne_entete.="NOM;";}
				}
	
				if((isset($prenom))&&($prenom=='y')) {
					$ligne.=$lig->prenom.";";
					if($cpt==0) {$ligne_entete.="PRENOM;";}
				}
	
				if((isset($sexe))&&($sexe=='y')) {
					$ligne.=$lig->sexe.";";
					if($cpt==0) {$ligne_entete.="SEXE;";}
				}
	
				if((isset($naissance))&&($naissance=='y')) {
					$ligne.=$lig->naissance.";";
					if($cpt==0) {$ligne_entete.="NAISSANCE;";}
				}
	
				if((isset($champ_login))&&($champ_login=='y')) {
					$ligne.=$lig->login.";";
					if($cpt==0) {$ligne_entete.="LOGIN;";}
				}
	
				if((isset($elenoet))&&($elenoet=='y')) {
					$ligne.=$lig->elenoet.";";
					if($cpt==0) {$ligne_entete.="ELENOET;";}
				}
	
				if((isset($ele_id))&&($ele_id=='y')) {
					$ligne.=$lig->ele_id.";";
					if($cpt==0) {$ligne_entete.="ELE_ID;";}
				}
	
				if((isset($no_gep))&&($no_gep=='y')) {
					$ligne.=$lig->no_gep.";";
					if($cpt==0) {$ligne_entete.="INE;";}
				}
	
				if((isset($email))&&($email=='y')) {
					$ligne.=$lig->email.";";
					if($cpt==0) {$ligne_entete.="EMAIL;";}
				}
	
				if((isset($classe))&&($classe=='y')) {
					if($cpt==0) {$ligne_entete.="CLASSE;";}
	
					$sql="SELECT DISTINCT c.classe FROM classes c, j_eleves_classes jec WHERE jec.login='$lig->login' AND jec.id_classe=c.id ORDER BY jec.periode;";
					$res_clas=mysql_query($sql);
					$cpt2=0;
					while($lig_clas=mysql_fetch_object($res_clas)) {
						if($cpt2>0) {$ligne.=" ";}
						$ligne.=$lig_clas->classe;
						$cpt2++;
					}
					$ligne.=";";
				}
	
				if((isset($matiere))&&(count($matiere)>0)) {
					for($j=0;$j<count($matiere);$j++) {
						if($cpt==0) {$ligne_entete.="$matiere[$j];";}
	
						$sql="SELECT 1=1 FROM j_groupes_matieres jgm, j_eleves_groupes jeg WHERE jeg.login='$lig->login' AND jgm.id_groupe=jeg.id_groupe AND jgm.id_matiere='$matiere[$j]';";
						//echo "$sql<br />";
						$res_grp=mysql_query($sql);
						if(mysql_num_rows($res_grp)>0) {$ligne.="1";}
						$ligne.=";";
					}
				}
	
				if($cpt==0) {$fich.=$ligne_entete."\n";}
				$fich.=$ligne."\n";
				$cpt++;
			}
		}
	
	
		$nom_fic="options_eleves_gepi_".suppr_accents(preg_replace("/'/","&apos;",preg_replace('/[" ]/','',$projet)))."_".date("Ymd_Hi").".csv";
		send_file_download_headers('text/x-csv',$nom_fic);
	
		//echo $fich;
		echo echo_csv_encoded($fich);
		die();
	}
	else {

		$user_temp_directory=get_user_temp_directory();

		$fichier_content_xml=fopen("../temp/".$user_temp_directory."/content.xml","w+");
	
		$ecriture=fwrite($fichier_content_xml,'<?xml version="1.0" encoding="UTF-8"?>
<office:document-content xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0" xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0" xmlns:presentation="urn:oasis:names:tc:opendocument:xmlns:presentation:1.0" xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0" xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0" xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0" xmlns:math="http://www.w3.org/1998/Math/MathML" xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0" xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0" xmlns:ooo="http://openoffice.org/2004/office" xmlns:ooow="http://openoffice.org/2004/writer" xmlns:oooc="http://openoffice.org/2004/calc" xmlns:dom="http://www.w3.org/2001/xml-events" xmlns:xforms="http://www.w3.org/2002/xforms" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:rpt="http://openoffice.org/2005/report" xmlns:of="urn:oasis:names:tc:opendocument:xmlns:of:1.2" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:grddl="http://www.w3.org/2003/g/data-view#" xmlns:tableooo="http://openoffice.org/2009/table" xmlns:field="urn:openoffice:names:experimental:ooo-ms-interop:xmlns:field:1.0" xmlns:formx="urn:openoffice:names:experimental:ooxml-odf-interop:xmlns:form:1.0" xmlns:css3t="http://www.w3.org/TR/css3-text/" office:version="1.2"><office:scripts/><office:font-face-decls><style:font-face style:name="Arial1" svg:font-family="Arial" style:font-family-generic="swiss"/><style:font-face style:name="Times New Roman" svg:font-family="&apos;Times New Roman&apos;" style:font-family-generic="swiss"/><style:font-face style:name="Arial" svg:font-family="Arial" style:font-family-generic="swiss" style:font-pitch="variable"/><style:font-face style:name="DejaVu Sans Condensed" svg:font-family="&apos;DejaVu Sans Condensed&apos;" style:font-family-generic="system" style:font-pitch="variable"/></office:font-face-decls><office:automatic-styles><style:style style:name="co1" style:family="table-column"><style:table-column-properties fo:break-before="auto" style:column-width="4.262cm"/></style:style><style:style style:name="co2" style:family="table-column"><style:table-column-properties fo:break-before="auto" style:column-width="1.956cm"/></style:style><style:style style:name="co3" style:family="table-column"><style:table-column-properties fo:break-before="auto" style:column-width="2.27cm"/></style:style><style:style style:name="co4" style:family="table-column"><style:table-column-properties fo:break-before="auto" style:column-width="2.267cm"/></style:style><style:style style:name="ro1" style:family="table-row"><style:table-row-properties style:row-height="0.497cm" fo:break-before="auto" style:use-optimal-row-height="true"/></style:style><style:style style:name="ro2" style:family="table-row"><style:table-row-properties style:row-height="0.499cm" fo:break-before="auto" style:use-optimal-row-height="true"/></style:style><style:style style:name="ta1" style:family="table" style:master-page-name="PageStyle_5f_Feuille1"><style:table-properties table:display="true" style:writing-mode="lr-tb"/></style:style><number:date-style style:name="N37" number:automatic-order="true"><number:day number:style="long"/><number:text>/</number:text><number:month number:style="long"/><number:text>/</number:text><number:year/></number:date-style><style:style style:name="ce1" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties style:glyph-orientation-vertical="0" style:diagonal-bl-tr="none" style:diagonal-tl-br="none" style:text-align-source="fix" style:repeat-content="false" fo:wrap-option="no-wrap" fo:border="0.06pt solid #000000" style:direction="ltr" fo:padding="0.071cm" style:rotation-angle="0" style:rotation-align="none" style:shrink-to-fit="false" style:vertical-align="automatic" style:vertical-justify="auto"/><style:paragraph-properties fo:text-align="start" css3t:text-justify="auto" fo:margin-left="0cm" style:writing-mode="page"/></style:style><style:style style:name="ce2" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties style:glyph-orientation-vertical="0" style:diagonal-bl-tr="none" style:diagonal-tl-br="none" style:text-align-source="fix" style:repeat-content="false" fo:wrap-option="no-wrap" fo:border="0.06pt solid #000000" style:direction="ltr" fo:padding="0.071cm" style:rotation-angle="0" style:rotation-align="none" style:shrink-to-fit="false" style:vertical-align="automatic" style:vertical-justify="auto"/><style:paragraph-properties fo:text-align="start" css3t:text-justify="auto" fo:margin-left="0cm" style:writing-mode="page"/><style:text-properties style:use-window-font-color="true" style:text-outline="false" style:text-line-through-style="none" style:font-name="Arial1" fo:font-size="10pt" fo:font-style="normal" fo:text-shadow="none" style:text-underline-style="none" fo:font-weight="normal" style:font-size-asian="10pt" style:font-style-asian="normal" style:font-weight-asian="normal" style:font-name-complex="Arial1" style:font-size-complex="10pt" style:font-style-complex="normal" style:font-weight-complex="normal"/><style:map style:condition="is-true-formula(ISODD(ROW([Feuille1.A3])))" style:apply-style-name="Ligne_5f_impaire" style:base-cell-address="Feuille1.A3"/></style:style><style:style style:name="ce3" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties style:text-align-source="fix" style:repeat-content="false"/><style:paragraph-properties fo:text-align="center" fo:margin-left="0cm"/></style:style><style:style style:name="ce4" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties style:glyph-orientation-vertical="0" style:diagonal-bl-tr="none" style:diagonal-tl-br="none" style:text-align-source="fix" style:repeat-content="false" fo:wrap-option="no-wrap" fo:border="0.06pt solid #000000" style:direction="ltr" fo:padding="0.071cm" style:rotation-angle="0" style:rotation-align="none" style:shrink-to-fit="false" style:vertical-align="automatic" style:vertical-justify="auto"/><style:paragraph-properties fo:text-align="center" css3t:text-justify="auto" fo:margin-left="0cm" style:writing-mode="page"/></style:style><style:style style:name="ce5" style:family="table-cell" style:parent-style-name="Default" style:data-style-name="N37"><style:table-cell-properties style:glyph-orientation-vertical="0" style:diagonal-bl-tr="none" style:diagonal-tl-br="none" style:text-align-source="fix" style:repeat-content="false" fo:wrap-option="no-wrap" fo:border="0.06pt solid #000000" style:direction="ltr" fo:padding="0.071cm" style:rotation-angle="0" style:rotation-align="none" style:shrink-to-fit="false" style:vertical-align="automatic" style:vertical-justify="auto"/><style:paragraph-properties fo:text-align="center" css3t:text-justify="auto" fo:margin-left="0cm" style:writing-mode="page"/><style:map style:condition="is-true-formula(ISODD(ROW([Feuille1.A3])))" style:apply-style-name="Ligne_5f_impaire" style:base-cell-address="Feuille1.A3"/></style:style><style:style style:name="ce6" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties style:glyph-orientation-vertical="0" style:text-align-source="fix" style:repeat-content="false" fo:wrap-option="no-wrap" style:direction="ltr" fo:padding="0.071cm" style:rotation-angle="0" style:rotation-align="none" style:shrink-to-fit="false" style:vertical-align="automatic" style:vertical-justify="auto"/><style:paragraph-properties fo:text-align="center" css3t:text-justify="auto" fo:margin-left="0cm" style:writing-mode="page"/></style:style><style:style style:name="ce7" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties style:glyph-orientation-vertical="0" style:diagonal-bl-tr="none" style:diagonal-tl-br="none" style:text-align-source="fix" style:repeat-content="false" fo:wrap-option="no-wrap" fo:border="0.06pt solid #000000" style:direction="ltr" fo:padding="0.071cm" style:rotation-angle="0" style:rotation-align="none" style:shrink-to-fit="false" style:vertical-align="automatic" style:vertical-justify="auto"/><style:paragraph-properties fo:text-align="center" css3t:text-justify="auto" fo:margin-left="0cm" style:writing-mode="page"/><style:map style:condition="is-true-formula(ISODD(ROW([Feuille1.A3])))" style:apply-style-name="Ligne_5f_impaire" style:base-cell-address="Feuille1.A3"/></style:style><style:style style:name="ce8" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties style:glyph-orientation-vertical="0" style:diagonal-bl-tr="none" style:diagonal-tl-br="none" style:text-align-source="fix" style:repeat-content="false" fo:wrap-option="no-wrap" fo:border="0.06pt solid #000000" style:direction="ltr" fo:padding="0.071cm" style:rotation-angle="0" style:rotation-align="none" style:shrink-to-fit="false" style:vertical-align="automatic" style:vertical-justify="auto"/><style:paragraph-properties fo:text-align="center" css3t:text-justify="auto" fo:margin-left="0cm" style:writing-mode="page"/><style:map style:condition="is-true-formula(ISODD(ROW([Feuille1.A3])))" style:apply-style-name="Ligne_5f_impaire" style:base-cell-address="Feuille1.A3"/></style:style><style:style style:name="ce9" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties style:text-align-source="fix" style:repeat-content="false" fo:border="0.06pt solid #000000"/><style:paragraph-properties fo:text-align="center" fo:margin-left="0cm"/></style:style><style:style style:name="ce10" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties fo:border="0.06pt solid #000000"/><style:map style:condition="is-true-formula(ISODD(ROW([Feuille1.A3])))" style:apply-style-name="Ligne_5f_impaire" style:base-cell-address="Feuille1.A3"/></style:style><style:style style:name="ce11" style:family="table-cell" style:parent-style-name="Default"><style:map style:condition="is-true-formula(ISODD(ROW([Feuille1.A3])))" style:apply-style-name="Ligne_5f_impaire" style:base-cell-address="Feuille1.A3"/></style:style></office:automatic-styles>');

		$ecriture=fwrite($fichier_content_xml,'<office:body><office:spreadsheet><table:calculation-settings table:case-sensitive="false" table:use-regular-expressions="false"/><table:table table:name="Feuille1" table:style-name="ta1"><office:forms form:automatic-focus="false" form:apply-design-mode="false"/>');

		$nb_lig=0;
		//==========================================
		$nb_col=0;
		for($i=0;$i<count($tab_champs);$i++) {
			if(isset($_POST[$tab_champs[$i]])) {
				if(($tab_champs[$i]=='nom')||($tab_champs[$i]=='prenom')) {
					$ecriture=fwrite($fichier_content_xml,'<table:table-column table:style-name="co1" table:default-cell-style-name="Default"/>');
				}
				else {
					$ecriture=fwrite($fichier_content_xml,'<table:table-column table:style-name="co2" table:default-cell-style-name="ce6"/>');
				}
				$nb_col++;
			}
		}

		for($i=0;$i<count($matiere);$i++) {
			$ecriture=fwrite($fichier_content_xml,'<table:table-column table:style-name="co2" table:default-cell-style-name="ce6"/>');
			$nb_col++;
		}

		$nb_col_vides=1023-$nb_col;
		$ecriture=fwrite($fichier_content_xml,'<table:table-column table:style-name="co4" table:number-columns-repeated="'.$nb_col_vides.'" table:default-cell-style-name="Default"/>');
		//==========================================
		// Ligne 1
		$ecriture=fwrite($fichier_content_xml,'<table:table-row table:style-name="ro1">');

		$nb_champs_retenus=0;
		for($i=0;$i<count($tab_champs);$i++) {
			if(isset($_POST[$tab_champs[$i]])) {
				$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce3" office:value-type="string"><text:p>'.strtoupper($tab_champs[$i]).'</text:p></table:table-cell>');
				$nb_champs_retenus++;
			}
		}

		if((isset($matiere))&&(count($matiere)>0)) {
			for($i=0;$i<count($matiere);$i++) {
				$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce3" office:value-type="string"><text:p>'.strtoupper($matiere[$i]).'</text:p></table:table-cell>');
			}
		}

		$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce4" office:value-type="string"><text:p>Redoublement</text:p></table:table-cell>');
		$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce9" office:value-type="string"><text:p>Depart</text:p></table:table-cell>');

		$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:number-columns-repeated="'.$nb_col_vides.'"/>');
		$ecriture=fwrite($fichier_content_xml,'</table:table-row>');
		$nb_lig++;
		//==========================================
		// Ligne 2
		$ecriture=fwrite($fichier_content_xml,'<table:table-row table:style-name="ro1">');

		$num_col=1;
		$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce1" office:value-type="string"><text:p>Effectif:</text:p></table:table-cell>');

		//$num_col++;
		$decalage=$nb_champs_retenus-1;
		$num_col+=$decalage;
		$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce4" table:number-columns-repeated="'.$decalage.'"/>');

		if((isset($matiere))&&(count($matiere)>0)) {
			for($i=0;$i<count($matiere);$i++) {
				$num_col++;
				$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce4" table:formula="of:=SUM([.'.LETTRE_COLONNE($num_col).'3:.'.LETTRE_COLONNE($num_col).'500])" office:value-type="float" office:value="0"><text:p>0</text:p></table:table-cell>');
			}
		}

		$num_col++;
		$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce4" table:formula="of:=SUM([.'.LETTRE_COLONNE($num_col).'3:.'.LETTRE_COLONNE($num_col).'500])" office:value-type="float" office:value="0"><text:p>0</text:p></table:table-cell>');
		$num_col++;
		$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce4" table:formula="of:=SUM([.'.LETTRE_COLONNE($num_col).'3:.'.LETTRE_COLONNE($num_col).'500])" office:value-type="float" office:value="0"><text:p>0</text:p></table:table-cell>');

		$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:number-columns-repeated="'.$nb_col_vides.'"/>');
		$ecriture=fwrite($fichier_content_xml,'</table:table-row>');

		$nb_lig++;
		//==========================================
		// Lignes élèves

		for($k=0;$k<count($id_classe);$k++) {
			$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE jec.login=e.login AND jec.id_classe='$id_classe[$k]' ORDER BY nom,prenom;";
			//$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE jec.login=e.login AND jec.id_classe='$id_classe[$k]' AND (e.date_sortie IS NULL OR e.date_sortie NOT LIKE '20%') ORDER BY nom,prenom;";
			$res=mysql_query($sql);
			while($lig=mysql_fetch_object($res)) {
				$ecriture=fwrite($fichier_content_xml,'<table:table-row table:style-name="ro2">');

				for($i=0;$i<count($tab_champs);$i++) {
					if(isset($_POST[$tab_champs[$i]])) {

						if($tab_champs[$i]=='classe') {
							$chaine_classes="";
							$sql="SELECT DISTINCT c.classe FROM classes c, j_eleves_classes jec WHERE jec.login='$lig->login' AND jec.id_classe=c.id ORDER BY jec.periode;";
							$res_clas=mysql_query($sql);
							$cpt2=0;
							while($lig_clas=mysql_fetch_object($res_clas)) {
								if($cpt2>0) {$chaine_classes.=" ";}
								$chaine_classes.=$lig_clas->classe;
								$cpt2++;
							}

							//$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce2" office:value-type="string"><text:p>'.suppr_accents(preg_replace("/'/","&apos;",preg_replace('/"/','',$chaine_classes))).'</text:p></table:table-cell>');
							$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce8" office:value-type="string"><text:p>'.suppr_accents(preg_replace("/'/","&apos;",preg_replace('/"/','',$chaine_classes))).'</text:p></table:table-cell>');
						}
						else {
							$champ_courant=$tab_champs[$i];
							//$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce2" office:value-type="string"><text:p>'.suppr_accents(preg_replace("/'/","&apos;",preg_replace('/"/','',$lig->$champ_courant))).'</text:p></table:table-cell>');
							$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce8" office:value-type="string"><text:p>'.suppr_accents(preg_replace("/'/","&apos;",preg_replace('/"/','',$lig->$champ_courant))).'</text:p></table:table-cell>');
						}
					}
				}

				if((isset($matiere))&&(count($matiere)>0)) {
					for($j=0;$j<count($matiere);$j++) {
	
						$sql="SELECT 1=1 FROM j_groupes_matieres jgm, j_eleves_groupes jeg WHERE jeg.login='$lig->login' AND jgm.id_groupe=jeg.id_groupe AND jgm.id_matiere='$matiere[$j]';";
						//echo "$sql<br />";
						$res_grp=mysql_query($sql);
						if(mysql_num_rows($res_grp)>0) {
							$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce8" office:value-type="float" office:value="1"><text:p>1</text:p></table:table-cell>');
						}
						else {
							$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce7" office:value-type="string"><text:p></text:p></table:table-cell>');
						}
					}
				}

				$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce7" office:value-type="string"><text:p></text:p></table:table-cell>');
				$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:style-name="ce7" office:value-type="string"><text:p></text:p></table:table-cell>');


				$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:number-columns-repeated="'.$nb_col_vides.'"/></table:table-row>');
				$nb_lig++;
			}
		}
		//==========================================
		$nb_lig_vides=1048565-$nb_lig;
		$ecriture=fwrite($fichier_content_xml,'<table:table-row table:style-name="ro1" table:number-rows-repeated="'.$nb_lig_vides.'">');
		$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:number-columns-repeated="'.$nb_col_vides.'"/>');
		$ecriture=fwrite($fichier_content_xml,'</table:table-row>');
		//==========================================
		$ecriture=fwrite($fichier_content_xml,'<table:table-row table:style-name="ro1">');
		$ecriture=fwrite($fichier_content_xml,'<table:table-cell table:number-columns-repeated="'.$nb_col_vides.'"/>');
		$ecriture=fwrite($fichier_content_xml,'</table:table-row>');
		//==========================================
		$ecriture=fwrite($fichier_content_xml,'</table:table>');
		$ecriture=fwrite($fichier_content_xml,'</office:spreadsheet>');
		$ecriture=fwrite($fichier_content_xml,'</office:body>');
		$ecriture=fwrite($fichier_content_xml,'</office:document-content>');
		//==========================================

		$fermeture=fclose($fichier_content_xml);
		
		set_time_limit(3000);

		$fichier_liste="options_eleves_gepi_".suppr_accents(preg_replace("/'/","&apos;",preg_replace('/[" ]/','',$projet)))."_".date("Ymd_Hi");

		if(file_exists("../lib/ss_zip.class.php")){
			//require_once("ss_zip.class.php");
			require_once("../lib/ss_zip.class.php");

			$zip= new ss_zip('',6);
			$zip->add_file("../temp/".$user_temp_directory."/content.xml",'content.xml');
			$zip->add_file('liste_options_ods/meta.xml','meta.xml');
			$zip->add_file('liste_options_ods/mimetype','mimetype');
			$zip->add_file('liste_options_ods/settings.xml','settings.xml');
			$zip->add_file('liste_options_ods/styles.xml','styles.xml');
			$zip->add_file('liste_options_ods/META-INF/manifest.xml','META-INF/manifest.xml');
			$zip->save("../temp/".$user_temp_directory."/$fichier_liste.zip");

			rename("../temp/".$user_temp_directory."/$fichier_liste.zip","../temp/".$user_temp_directory."/".$fichier_liste.".ods");
		}
		else {

			$path = path_niveau();
			$chemin_temp = $path."temp/".get_user_temp_directory()."/";

			if (!defined('PCLZIP_TEMPORARY_DIR') || constant('PCLZIP_TEMPORARY_DIR')!=$chemin_temp) {
				@define( 'PCLZIP_TEMPORARY_DIR', $chemin_temp);
			}

			$nom_fic=$fichier_liste.".ods";
			$chemin_stockage = $chemin_temp."/".$nom_fic;
			$chemin_modele_ods='liste_options_ods';

			$dossier_a_traiter=$chemin_temp."liste_options_".strftime("%Y%m%d%H%M%S");

			@mkdir($dossier_a_traiter);
			copy("../temp/".$user_temp_directory."/content.xml", $dossier_a_traiter."/content.xml");

			@mkdir($dossier_a_traiter."/META-INF");

			$tab_fich_tmp=array('META-INF/manifest.xml', 'settings.xml', 'meta.xml', 'mimetype', 'styles.xml');
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

		$lien_fichier_ods="<p>Fichier&nbsp;: <a href='../temp/".$user_temp_directory."/".$fichier_liste.".ods'>".$fichier_liste.".ods</a></p>\n";

	}
}

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Genèse classe: Liste des options";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc.php");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

if((!isset($projet))||($projet=="")) {
	echo "<p style='color:red'>ERREUR: Le projet n'est pas choisi.</p>\n";
	require("../lib/footer.inc.php");
	die();
}

//echo "<div class='noprint'>\n";
echo "<p class='bold'><a href='index.php?projet=$projet'".insert_confirm_abandon().">Retour</a>";
if((isset($_POST['choix_param']))||(isset($_POST['valider_param']))) {
	echo " | <a href='".$_SERVER['PHP_SELF']."?projet=$projet'".insert_confirm_abandon().">Choisir d'autres options</a>";
}
echo "</p>\n";
//echo "</div>\n";

echo "<h2>Projet $projet</h2>\n";

if(isset($lien_fichier_ods)) {
	echo $lien_fichier_ods;
	require("../lib/footer.inc.php");
	die();
}

$sql="SELECT id_classe FROM gc_divisions WHERE projet='$projet' AND statut='actuelle';";
$res=mysql_query($sql);
while($lig=mysql_fetch_object($res)) {
	$tab_id_classe[]=$lig->id_classe;
}

echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">\n";
echo "<input type='hidden' name='projet' value='".$projet."' />\n";
if(!isset($_POST['choix_param'])) {
	echo "<table summary='Choix des paramètres'>\n";
	echo "<tr>\n";
	echo "<td valign='top'>\n";
		echo "<p>Choix des classes&nbsp;:\n";
		echo "</p>\n";
		
		$sql="SELECT id,classe FROM classes ORDER BY classe;";
		$res_classes=mysql_query($sql);
		$nb_classes=mysql_num_rows($res_classes);
		
		// Affichage sur 4/5 colonnes
		$nb_classes_par_colonne=round($nb_classes/3);
		
		echo "<table width='100%' summary='Choix des classes'>\n";
		echo "<tr valign='top' align='center'>\n";
		
		$cpt_i = 0;
		
		echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
		echo "<td align='left'>\n";
		
		while($lig_clas=mysql_fetch_object($res_classes)) {
		
			//affichage 2 colonnes
			if(($cpt_i>0)&&(round($cpt_i/$nb_classes_par_colonne)==$cpt_i/$nb_classes_par_colonne)){
				echo "</td>\n";
				echo "<td align='left'>\n";
			}
		
			echo "<input type='checkbox' name='id_classe[]' id='id_classe_$cpt_i' value='$lig_clas->id' ";
			echo "onchange=\"checkbox_champ_change('id_classe_$cpt_i'); changement();\" ";
			if(in_array($lig_clas->id,$tab_id_classe)) {echo "checked ";$temp_style=" style='font-weight:bold;'";} else {$temp_style="";}
			echo "/><label for='id_classe_$cpt_i'><span id='texte_id_classe_$cpt_i'$temp_style>$lig_clas->classe</span></label>\n";
			echo "<input type='hidden' name='classe[$lig_clas->id]' value='$lig_clas->classe' />\n";
			echo "<br />\n";
			$cpt_i++;
		}
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
	echo "</td>\n";
	echo "<td valign='top'>\n";
		echo "<p>Choix des informations à faire apparaître&nbsp;:<br />\n";
		echo "<blockquote>\n";
		echo "<p><input type='checkbox' name='nom' id='nom' value='y' checked ";
		echo "onchange=\"checkbox_champ_change('nom')\" ";
		echo "/><label for='nom'><span id='texte_nom'>Nom</span></label><br />\n";

		echo "<input type='checkbox' name='prenom' id='prenom' value='y' checked ";
		echo "onchange=\"checkbox_champ_change('prenom')\" ";
		echo "/><label for='prenom'><span id='texte_prenom'>Prénom</span></label><br />\n"; 

		echo "<input type='checkbox' name='sexe' id='sexe' value='y' checked ";
		echo "onchange=\"checkbox_champ_change('sexe')\" ";
		echo "/><label for='sexe'><span id='texte_sexe'>sexe</span></label><br />\n"; 

		echo "<input type='checkbox' name='naissance' id='naissance' value='y' checked ";
		echo "onchange=\"checkbox_champ_change('naissance')\" ";
		echo "/><label for='naissance'><span id='texte_naissance'>naissance</span></label><br />\n"; 

		echo "<input type='checkbox' name='champ_login' id='login' value='y' ";
		echo "onchange=\"checkbox_champ_change('login')\" ";
		echo "/><label for='login'><span id='texte_login'>Login</span></label><br />\n"; 

		echo "<input type='checkbox' name='elenoet' id='elenoet' value='y' checked ";
		echo "onchange=\"checkbox_champ_change('elenoet')\" ";
		echo "/><label for='elenoet'><span id='texte_elenoet'>Numéro elenoet</span></label><br />\n"; 

		echo "<input type='checkbox' name='ele_id' id='ele_id' value='y' ";
		echo "onchange=\"checkbox_champ_change('ele_id')\" ";
		echo "/><label for='ele_id'><span id='texte_ele_id'>Numéro ele_id</span></label><br />\n"; 

		echo "<input type='checkbox' name='no_gep' id='no_gep' value='y' ";
		echo "onchange=\"checkbox_champ_change('no_gep')\" ";
		echo "/><label for='no_gep'><span id='texte_no_gep'>Numéro INE</span></label><br />\n"; 

		echo "<input type='checkbox' name='email' id='email' value='y' ";
		echo "onchange=\"checkbox_champ_change('email')\" ";
		echo "/><label for='email'><span id='texte_email'>email</span></label><br />\n"; 
		
		echo "<input type='checkbox' name='classe' id='classe' value='y' checked ";
		echo "onchange=\"checkbox_champ_change('classe')\" ";
		echo "/><label for='classe'><span id='texte_classe'>Classe</span></label></p>\n"; 
		echo "</blockquote>\n";
	echo "</td>\n";
	echo "<td valign='top'>\n";
		echo "<p>Choix des matières à faire apparaître&nbsp;:<br />\n";
		echo "<blockquote>\n";
	
		$tab_options=array();
		$sql="SELECT * FROM gc_options WHERE projet='$projet';";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {
			while ($lig=mysql_fetch_object($res)) {
				$tab_options[]=$lig->opt;
			}
		}

		$chaine_champs_matiere="";
		$sql="SELECT matiere FROM matieres ORDER BY matiere;";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {
			$cpt=0;
			echo "<div style='width: 20em; height: 20em; overflow: auto;'>\n";
			while($lig=mysql_fetch_object($res)) {
				if($cpt>0) {$chaine_champs_matiere.=", ";}
				echo "<input type='checkbox' name='matiere[]' id='matiere_$cpt' value='$lig->matiere' ";
				if(in_array($lig->matiere,$tab_options)) {echo "checked ";}
				echo "onchange=\"checkbox_champ_change('matiere_$cpt')\" ";
				echo "/><label for='matiere_$cpt'><span id='texte_matiere_$cpt'>$lig->matiere</span></label><br />\n";
				$chaine_champs_matiere.="'matiere_$cpt'";
				$cpt++;
			}
			echo "</div>\n";
		}
		echo "</blockquote>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	
	echo "<p><input type='submit' name='choix_param' value='Valider' /></p>\n";

	$chaine_champs="";
	for($i=0;$i<count($tab_champs);$i++) {
		if($i>0) {$chaine_champs.=", ";}
		$chaine_champs.="'".$tab_champs[$i]."'";
	}

	echo js_checkbox_change_style('checkbox_champ_change', 'texte_', 'y');

	echo "<script type='text/javascript'>
var champs=new Array($chaine_champs, $chaine_champs_matiere);
for(i=0;i<champs.length;i++) {
	if(document.getElementById(champs[i])) {
		if(document.getElementById(champs[i]).checked==true) {
			document.getElementById('texte_'+champs[i]).style.fontWeight='bold';
		}
		else {
			document.getElementById('texte_'+champs[i]).style.fontWeight='normal';
		}
	}
}
</script>\n";

}
else {
	$liste_classes="";
	for($i=0;$i<count($id_classe);$i++) {
		if($i>0) {$liste_classes.=", ";}
		$liste_classes.=get_class_from_id($id_classe[$i]);

		echo "<input type='hidden' name='id_classe[]' value='".$id_classe[$i]."' />\n";
	}
	echo "<p>Vous avez choisi les classes $liste_classes</p>\n";

	$liste_champs="";
	for($i=0;$i<count($tab_champs);$i++) {
		if(isset($_POST[$tab_champs[$i]])) {
			echo "<input type='hidden' name='".$tab_champs[$i]."' value='y' />\n";
			if($i>0) {$liste_champs.=", ";}
			$liste_champs.=$tab_champs[$i];
		}
	}
	echo "<p>Vous souhaitez faire apparaître les champs&nbsp;: $liste_champs</p>\n";

	$liste_matiere="";
	$li_select_matiere="";
	for($i=0;$i<count($matiere);$i++) {
		if($i>0) {$liste_matiere.=", ";}
		$liste_matiere.=$matiere[$i];

		$li_select_matiere.="<li>\n";
		$li_select_matiere.="<select name='matiere[$i]'>\n";
		for($j=0;$j<count($matiere);$j++) {
			$li_select_matiere.="<option value='".$matiere[$j]."'";
			if($i==$j) {$li_select_matiere.=" selected='true'";}
			$li_select_matiere.=">$matiere[$j]</option>\n";
		}
		$li_select_matiere.="</select>\n";
		$li_select_matiere.="</li>\n";
	}

	echo "<p>Veuillez choisir l'ordre des colonnes options parmi&nbsp;:&nbsp;";
	echo $liste_matiere;
	echo "</p>\n";

	echo "<ol>\n";
	echo $li_select_matiere;
	echo "</ol>\n";



	echo "<p>Type d'export&nbsp;: ";
	echo "<input type='radio' name='type_export' id='type_export_csv' value='csv' checked /><label for='type_export_csv'>CSV</label> ou \n";
	echo "<input type='radio' name='type_export' id='type_export_ods' value='ods' /><label for='type_export_ods'>ODS</label></p>\n";

	echo "<p><input type='submit' name='valider_param' value='Valider' /></p>\n";
}
echo "</form>\n";

echo "<p>Cette page est destinée à générer un CSV des options à pointer en conseil de classe pour les préparatifs de conception de classe de l'année suivante.<br />
Ce fichier correctement dûment sera réclamé à l'étape 4 'Importer les options futures des élèves d'après un CSV'.<br />
Il conviendra d'ajouter des lignes de totaux (SOMME()) à l'aide du tableur si vous souhaitez faire un usage autre de ce fichier que l'import dans le module 'Genèse des classes'.<br />
Les champs comme login, elenoet, ele_id,... sont destinés à faciliter l'import en retour des choix dans les tables 'gc_*'.</p>\n";


require("../lib/footer.inc.php");
?>
