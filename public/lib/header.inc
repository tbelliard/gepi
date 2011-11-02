<?php
/*
 * $Id: header.inc 3136 2009-05-14 15:28:47Z crob $
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// Pour le multisite, on doit récupérer le RNE de l'établissement
$rne = isset($_GET['rne']) ? $_GET['rne'] : (isset($_POST['rne']) ? $_POST['rne'] : 'aucun');
$aff_get_rne = $aff_get_rne_bis = NULL;
if ($rne != 'aucun') {
	$aff_get_rne = '&amp;rne=' . $rne;
	$aff_get_rne_bis = '?rne=' . $rne;
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="fr">
<head>
<meta HTTP-EQUIV="Content-Type" content="text/html; charset=iso-8859-1" />
<META HTTP-EQUIV="Pragma" CONTENT="no-cache" />
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache" />
<META HTTP-EQUIV="Expires" CONTENT="0" />
<title><?php echo getSettingValue("gepiSchoolName");
if ((isset($id_classe)) and ($id_classe != -1)) echo " - $classe_nom";
if (isset($id_groupe)) echo "- $matiere_nom ";
?></title>
<link rel="stylesheet" type="text/css" href="<?php echo($gepiPath); ?>/style.css" />
<script src="../lib/functions.js" type="text/javascript" language="javascript"></script>
<LINK REL="SHORTCUT ICON" type="image/x-icon" href="../favicon.ico" />

<?php
echo '<script type="text/javascript" src="'.$gepiPath.'/lib/prototype.js"></script>'."\n";
if(isset($style_screen_ajout)){
	// Styles paramétrables depuis l'interface:
	if($style_screen_ajout=='y'){
		// La variable $style_screen_ajout se paramètre dans le /lib/global.inc
		// C'est une sécurité... il suffit de passer la variable à 'n' pour désactiver ce fichier CSS et éventuellement rétablir un accès après avoir imposé une couleur noire sur noire
		echo "<link rel='stylesheet' type='text/css' href='$gepiPath/style_screen_ajout.css' />\n";
	}
}
?>


</head>
<body>
<?php if (isset($titre_page)) {

	echo "<!-- Header start -->\n";
	echo "<div id='header'>\n";
	//echo "<table id='table_header' border='0' width='100%' height='100px;' style='color: white; background-image: url(\"$gepiPath/images/background/darkfade.png\"); margin: 0; padding: 0;'>\n";

	if(getSettingValue('gepi_stylesheet')=='style'){
		//echo "<table id='table_header' border='0' width='100%' style='color: white; background-image: url(\"$gepiPath/images/background/darkfade.png\"); background-repeat: repeat-x; margin: 0; padding: 0;'>\n";

		if(getSettingValue('utiliser_degrade')=='y'){
			$degrade_entete="degrade1";
		}
		else{
			$degrade_entete="darkfade";
		}

		echo "<table id='table_header' border='0' width='100%' style='color: white; background-image: url(\"$gepiPath/images/background/$degrade_entete.png\"); background-repeat: repeat-x; margin: 0; padding: 0;'>\n";
	}
	else{
		echo "<table id='table_header' border='0' width='100%'>\n";
	}

	echo "<tr>\n";
		echo "<td rowspan='2' style='margin:0;padding:0;'><div style='width:0px; height:96px; margin:0;padding:0;'></div></td>\n";

		echo "<!-- Page title, access rights -->\n";
		echo "<td id='td_headerLeft' rowspan='2'>\n";

		// Pour empêcher de trop réduire la taille de la cellule de gauche
		echo "<div style='width:380px;'></div>\n";
		echo "<div style='margin-top:20px; margin-bottom:15px;'><h3>".$titre_page."</h3></div>\n";

		echo "</td>\n";

		echo "<td id='td_headerTopRight'>\n";

		// Pour empêcher de trop réduire la taille de la cellule de droite
		echo "<div style='width:400px;'></div>\n";

    	echo getSettingValue("gepiSchoolName"). " - ".$titre_page." : module public de consultation";

		echo "</td>\n";
		echo "</tr>\n";



		echo "<tr>\n";
		// Les TD précédents sont dans des rowspan

		echo "<td id='td_headerBottomRight' style='padding-bottom: 5px;'>\n";
		echo "<span class='grey'>";
		    echo "<a href=\"".$page_accueil. $aff_get_rne ."\">Accueil</a> - ";
		    echo "<a href=\"http://gepi.mutualibre.org/\" target='_blank'>Visiter le site de GEPI</a> - ";
			if (getSettingValue("contact_admin_mailto")=='y') {
				$gepiAdminAdress=getSettingValue("gepiAdminAdress");
				$tmp_date=getdate();
				echo "<a href='mailto:$gepiAdminAdress?Subject=Gepi&amp;body=";
				if($tmp_date['hours']>=18) {echo "Bonsoir";} else {echo "Bonjour";}
				echo ",%0d%0aCordialement.'>Contacter l'administrateur</a>";
			}
			else {
			    echo "<a href=\"javascript:centrerpopup('contacter_admin_pub.php" . $aff_get_rne_bis . "',600,480,'scrollbars=yes,statusbar=no,resizable=yes')\">Contacter l'administrateur</a>";
			}
		echo "</span>\n";

		echo "</td>\n";
	echo "</tr>\n";

	echo "</table>\n";
	echo "\n";
	echo "</div>\n";
	echo "<!-- Header end -->\n";
}
   ?>
 <div id="container">