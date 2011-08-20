<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
/**
 * Entête de l'affichage html des relevé de notes
 * 
 * @license GNU/GPL, 
 * @package Carnet_de_notes
 * @subpackage affichage
 * @see getSettingValue()
 */

	$RneEtablissement=getSettingValue("gepiSchoolRne") ? getSettingValue("gepiSchoolRne") : "";
	$gepiSchoolName=getSettingValue("gepiSchoolName") ? getSettingValue("gepiSchoolName") : "gepiSchoolName";
	$gepiSchoolAdress1=getSettingValue("gepiSchoolAdress1") ? getSettingValue("gepiSchoolAdress1") : "";
	$gepiSchoolAdress2=getSettingValue("gepiSchoolAdress2") ? getSettingValue("gepiSchoolAdress2") : "";
	$gepiSchoolZipCode=getSettingValue("gepiSchoolZipCode") ? getSettingValue("gepiSchoolZipCode") : "";
	$gepiSchoolCity=getSettingValue("gepiSchoolCity") ? getSettingValue("gepiSchoolCity") : "";
	$gepiSchoolPays=getSettingValue("gepiSchoolPays") ? getSettingValue("gepiSchoolPays") : "";

	$gepiYear=getSettingValue("gepiYear") ? getSettingValue("gepiYear") : ((strftime("%m")>7) ? ((strftime("%Y")-1)."-".strftime("%Y")) : (strftime("%Y")."-".strftime("%Y")+1));

	$logo_etab=getSettingValue("logo_etab") ? getSettingValue("logo_etab") : "";

	echo "<html>
<head>
<meta HTTP-EQUIV='Content-Type' content='text/html; charset=iso-8859-1' />
<META HTTP-EQUIV='Pragma' CONTENT='no-cache' />
<META HTTP-EQUIV='Cache-Control' CONTENT='no-cache' />
<META HTTP-EQUIV='Expires' CONTENT='0' />
<title>".$gepiSchoolName." : Edition des relevés de notes</title>
<link rel='stylesheet' type='text/css' href='../style.css' />\n";


	//========================================

	// Portion des styles et initialisations à reprendre dans le cas d'une insertion des relevés de notes entre les bulletins
	include("initialisations_header_releves_html.php");

	echo "<!-- Styles du relevé HTML -->\n";
	echo $style_releve_notes_html;

	//======================================
	// Portion des styles pouvant entrer en concurrence avec ceux du bulletin HTML dans le cas d'une insertion des relevés de notes entre les bulletins

	echo "	<link rel='shortcut icon' type='image/x-icon' href='../favicon.ico' />
	<link rel='icon' type='image/ico' href='../favicon.ico' />\n";

	if(isset($style_screen_ajout)){
		// Styles paramétrables depuis l'interface:
		if($style_screen_ajout=='y'){
			// La variable $style_screen_ajout se paramètre dans le /lib/global.inc
			// C'est une sécurité... il suffit de passer la variable à 'n' pour désactiver ce fichier CSS et éventuellement rétablir un accès après avoir imposé une couleur noire sur noire
			echo "<link rel='stylesheet' type='text/css' href='$gepiPath/style_screen_ajout.css' />\n";
		}
	}

	// Le $releve_body_marginleft n'est pas pris en compte dans le cas d'une insertion des relevés de notes entre les bulletins
	// C'est le $bull_body_marginleft qui est alors utilisé pour le relévé et le bulletin.

	$releve_body_marginleft=getSettingValue("releve_body_marginleft") ? getSettingValue("releve_body_marginleft") : 1;

	echo "<style type='text/css'>

	body {
		margin-left: ".$releve_body_marginleft."px;
	}

	@media screen{
		#infodiv {
			float: right;
			width: 20em;
			/*height: 50px;*/
			/*border:1px solid black;*/
			background-color: white;
		}

		.espacement_bulletins {
			width: 100%;
			height: 50px;
			border:1px solid red;
			background-color: white;
		}
	}
	@media print{
		.noprint{
			display: none;
		}

		#infodiv {
			display:none;
		}

		.espacement_bulletins {
			display:none;
		}

		#remarques_bas_de_page {
			display:none;
		}

		.alerte_erreur {
			display:none;
		}
	}
</style>\n";


	echo "</head>\n";
	echo "<body>\n";
	echo "<div>\n";

?>