<?php
/*
*
* Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Sandrine Dangreville
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

$variables_non_protegees = 'yes';

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

// SQL : INSERT INTO droits VALUES ( '/mod_discipline/avertir_famille_html.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Avertir famille incident', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/avertir_famille_html.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Avertir famille incident', '');;";
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

if(mb_strtolower(mb_substr(getSettingValue('active_mod_discipline'),0,1))!='y') {
	$mess=rawurlencode("Vous tentez d accéder au module Discipline qui est désactivé !");
	tentative_intrusion(1, "Tentative d'accès au module Discipline qui est désactivé.");
	header("Location: ../accueil.php?msg=$mess");
	die();
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php

	$RneEtablissement=getSettingValue("gepiSchoolRne") ? getSettingValue("gepiSchoolRne") : "";
	$gepiSchoolName=getSettingValue("gepiSchoolName") ? getSettingValue("gepiSchoolName") : "gepiSchoolName";
	$gepiSchoolAdress1=getSettingValue("gepiSchoolAdress1") ? getSettingValue("gepiSchoolAdress1") : "";
	$gepiSchoolAdress2=getSettingValue("gepiSchoolAdress2") ? getSettingValue("gepiSchoolAdress2") : "";
	$gepiSchoolZipCode=getSettingValue("gepiSchoolZipCode") ? getSettingValue("gepiSchoolZipCode") : "";
	$gepiSchoolCity=getSettingValue("gepiSchoolCity") ? getSettingValue("gepiSchoolCity") : "";
	$gepiSchoolPays=getSettingValue("gepiSchoolPays") ? getSettingValue("gepiSchoolPays") : "";

	$gepiSchoolTel=getSettingValue("gepiSchoolTel") ? getSettingValue("gepiSchoolTel") : "";
	$gepiSchoolFax=getSettingValue("gepiSchoolFax") ? getSettingValue("gepiSchoolFax") : "";

	$gepiYear=getSettingValue("gepiYear") ? getSettingValue("gepiYear") : ((strftime("%m")>7) ? ((strftime("%Y")-1)."-".strftime("%Y")) : (strftime("%Y")."-".strftime("%Y")+1));

	$logo_etab=getSettingValue("logo_etab") ? getSettingValue("logo_etab") : "";

	/*
	$addressblock_length=70;
	$addressblock_padding_top=50;
	$addressblock_padding_text=5;
	$addressblock_padding_right=20;
	$addressblock_font_size=12;
	*/
	$addressblock_length=getSettingValue('addressblock_length');
	$addressblock_padding_top=getSettingValue('addressblock_padding_top');
	$addressblock_padding_text=getSettingValue('addressblock_padding_text');
	$addressblock_padding_right=getSettingValue('addressblock_padding_right');
	$addressblock_font_size=getSettingValue('addressblock_font_size');

	$margin_left=10;
	$margin_right=10;
	$margin_top=10;
	$margin_bottom=10;

	$debug='n';

	$ele_login=isset($_POST['ele_login']) ? $_POST['ele_login'] : (isset($_GET['ele_login']) ? $_GET['ele_login'] : array());

	if (isset($NON_PROTECT["courrier"])){
		//$courrier=traitement_magic_quotes(corriger_caracteres($NON_PROTECT["courrier"]));
		$courrier=$NON_PROTECT["courrier"];

		// Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
		$courrier=suppression_sauts_de_lignes_surnumeraires($courrier);

	}
	else {
		$courrier="";
	}

	require('sanctions_func_lib.php');

	function redimensionne_image($photo){
		global $bull_photo_largeur_max, $bull_photo_hauteur_max;

		// prendre les informations sur l'image
		$info_image=getimagesize($photo);
		// largeur et hauteur de l'image d'origine
		$largeur=$info_image[0];
		$hauteur=$info_image[1];

		// calcule le ratio de redimensionnement
		$ratio_l=$largeur/$bull_photo_largeur_max;
		$ratio_h=$hauteur/$bull_photo_hauteur_max;
		$ratio=($ratio_l>$ratio_h)?$ratio_l:$ratio_h;

		// définit largeur et hauteur pour la nouvelle image
		$nouvelle_largeur=round($largeur/$ratio);
		$nouvelle_hauteur=round($hauteur/$ratio);

		return array($nouvelle_largeur, $nouvelle_hauteur);
	}


	// <link rel='stylesheet' type='text/css' href='../style_screen_ajout.css' />

	echo "<html>
<head>
<meta HTTP-EQUIV='Content-Type' content='text/html; charset=utf-8' />
<META HTTP-EQUIV='Pragma' CONTENT='no-cache' />
<META HTTP-EQUIV='Cache-Control' CONTENT='no-cache' />
<META HTTP-EQUIV='Expires' CONTENT='0' />
<title>".$gepiSchoolName." : Communication</title>
<link rel='stylesheet' type='text/css' href='../style.css' />\n";

	if(isset($style_screen_ajout)){

		// Styles paramétrables depuis l'interface:
		if($style_screen_ajout=='y'){
			// La variable $style_screen_ajout se paramètre dans le /lib/global.inc
			// C'est une sécurité... il suffit de passer la variable à 'n' pour désactiver ce fichier CSS et éventuellement rétablir un accès après avoir imposé une couleur noire sur noire
			echo "\n<link rel='stylesheet' type='text/css' href='$gepiPath/style_screen_ajout.css' />\n";
		}
	}

	echo "<link rel='shortcut icon' type='image/x-icon' href='../favicon.ico' />
<link rel='icon' type='image/ico' href='../favicon.ico' />\n";
	echo "</head>\n";
	echo "<body>\n";


	$tab_adresses=tab_lignes_adresse($ele_login);

	$addressblock_debug="y";

	$bull_affiche_tel="y";
	$bull_affiche_fax="y";

	$bull_affich_nom_etab="y";
	$bull_affich_adr_etab="y";


/*
	echo "<div style='margin-left:".$margin_left."mm;
margin-top:".$margin_top."mm;
margin-right:".$margin_right."mm;
margin-bottom:".$margin_bottom."mm;
'>\n";
*/



	// Logo et étab en haut à gauche:
	echo "<div style='float:left;
left:0px;
top:0px;
text-align: left;
/*width:40%;*/
";
	if($debug=='y') {
		echo "
/* debug */
border: 1px solid green;\n";
	}
	echo "'>\n";

	echo "<table summary='Tableau du logo et infos établissement'";
	//if($addressblock_debug=="y"){echo " border='1'";}
	echo ">\n";
	echo "<tr>\n";

	$nom_fic_logo = $logo_etab;
	$nom_fic_logo_c = "../images/".$nom_fic_logo;

	if (($nom_fic_logo != '') and (file_exists($nom_fic_logo_c))) {
		echo "<td style=\"text-align: left;\"><img src=\"".$nom_fic_logo_c."\" border=\"0\" alt=\"Logo\" /></td>\n";
	}
	echo "<td style='text-align: left;'>";
	echo "<p>";
	if($bull_affich_nom_etab=="y"){
		echo "<span class=\"bgrand\">".$gepiSchoolName."</span>";
	}
	if($bull_affich_adr_etab=="y"){
		echo "<br />\n".$gepiSchoolAdress1."<br />\n".$gepiSchoolAdress2."<br />\n".$gepiSchoolZipCode." ".$gepiSchoolCity;
		if($bull_affiche_tel=="y"){echo "<br />\nTel: ".$gepiSchoolTel;}
		if($bull_affiche_fax=="y"){echo "<br />\nFax: ".$gepiSchoolFax;}
	}
	echo "</p>\n";

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "</div>\n";





	/*
	$ligne1="";
	$ligne2="";
	$ligne3="";
	*/

	$ligne1=$tab_adresses[0][0];
	$ligne2=$tab_adresses[1][0];
	$ligne3=$tab_adresses[2][0];


	// Cadre adresse du responsable:
	echo "<div style='float:right;
width:".$addressblock_length."mm;
padding-top:".$addressblock_padding_top."mm;
padding-bottom:".$addressblock_padding_text."mm;
padding-right:".$addressblock_padding_right."mm;\n";
//if($addressblock_debug=="y"){echo "border: 1px solid blue;\n";}
echo "font-size: ".$addressblock_font_size."pt;\n";

	if($debug=='y') {
		echo "
/* debug */
border: 1px solid blue;\n";
	}
	echo "'>
<div align='left'>
$ligne1<br />
$ligne2<br />
$ligne3
</div>
</div>\n";

	echo "<div style='clear: both;'></div>\n";

	echo "<h1 align='center'>Notification d'incident</h1>\n";

	echo "<div style='margin:3em;";
	if($debug=='y') {echo " border: 1px solid red;";}
	echo "'>".nl2br($courrier)."</div>\n";

/*
	echo "<pre style='color:red;'><b>A FAIRE:</b> Paramètres:
Avec/sans logo, tel/fax
Marges
Dimensions et position du bloc adresse resp.
Taille des polices
</pre>\n";
*/

//	echo "</div>\n";

	echo "</body>\n";
	echo "</html>\n";
?>

