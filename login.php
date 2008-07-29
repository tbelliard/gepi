<?php
/* $Id$
*
* Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// Pour le multisite
if (isset($_GET["rne"])) {
	setcookie('RNE', $_GET["rne"]);
}

// Vérification de la bonne installation de GEPI
require_once("./utilitaires/verif_install.php");

$niveau_arbo = 0;

// On indique qu'il faut créer des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

// Initialisations files
require_once("./lib/initialisations.inc.php");

# On redirige vers le login SSO si le login local ou ldap n'est pas activé.
if ($session_gepi->auth_sso && !$session_gepi->auth_locale && !$session_gepi->auth_ldap) {
	header("Location:login_sso.php");
	exit();
}


// Test de mise à jour : si on détecte que la base n'est à jour avec les nouveaux
// paramètres utilisés pour l'authentification, on redirige vers maj.php pour
// une mise à jour, normale ou forcée.
if (!isset($gepiSettings['auth_sso'])) {
	header("Location:utilitaires/maj.php");
	exit();
}

// Authentification Classique et Ldap
//-----------------------------------


if ($session_gepi->auth_locale && isset($_POST['login']) && isset($_POST['no_anti_inject_password'])) {

	$auth = $session_gepi->authenticate($_POST['login'], $NON_PROTECT['password']);

	if ($auth == "1") {
		// On renvoie à la page d'accueil
		session_write_close();
		header("Location: ./accueil.php");
		die();

	} else {
		header("Location: ./login_failure.php?error=".$auth);
		die();
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!-- correction Regis html xmlns + majuscules -->
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Cache-Control" content="no-cache" />
<meta http-equiv="Expires" content="0" />
<!-- correction Regis : déclaration par défaut pour les scripts et les mises en page-->
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta http-equiv="Content-Style-Type" content="text/css" />

<title><?php echo getSettingValue("gepiSchoolName"); ?> : base de données élèves | Identifiez vous...</title>
<?php
	$style = getSettingValue("gepi_stylesheet");
	if (empty($style)) $style = "style";
	?>
<link rel="stylesheet" type="text/css" href="./<?php echo $style;?>.css" />
<script src="lib/functions.js" type="text/javascript"></script>
<link rel="shortcut icon" type="image/x-icon" href="./favicon.ico" />
<link rel="icon" type="image/ico" href="./favicon.ico" />
<?php
	// Styles paramétrables depuis l'interface:
	if($style_screen_ajout=='y'){
		// La variable $style_screen_ajout se paramètre dans le /lib/global.inc
		// C'est une sécurité... il suffit de passer la variable à 'n' pour désactiver ce fichier CSS et éventuellement rétablir un accès après avoir imposé une couleur noire sur noire
		echo "<link rel='stylesheet' type='text/css' href='$gepiPath/style_screen_ajout.css' />\n";
	}
?>
</head>
<body onload="document.getElementById('login').focus()">
<?php

// Conteneur de l'ensemble de la page
echo "<div id='new_div_login' class='center'>\n";

	//==================================
	//On vérifie si le module est activé
	if (getSettingValue("active_cahiers_texte")=='y' and getSettingValue("cahier_texte_acces_public") == "yes" and getSettingValue("disable_login")!='yes') {
		echo "<div style='margin-top:3em;'><a href=\"./public/index.php?id_classe=-1\">\n";
		echo "<img src='./images/icons/cahier_texte.png' alt='' class='link' /> Consulter les cahiers de texte</a> (accès public)\n";
		echo "</div>\n";

		// Ajout d'un espace vertical
		echo "<div style='margin-top:2em;'>&nbsp;</div>\n";
	}
	else{
		// Ajout d'un espace vertical
		//echo "<div style='margin-top:6em;'>&nbsp;</div>\n";
		echo "<div style='margin-top:5em;'>&nbsp;</div>\n";
	}
	//==================================


	//==================================
	//if ((getSettingValue("disable_login"))=='yes') echo "<br /><br /><font color=\"red\" size=\"+1\">Le site est en cours de maintenance et temporairement inaccessible.<br />Veuillez nous excuser de ce dérangement et réessayer de vous connecter ultérieurement.</font><br />";
	if ((getSettingValue("disable_login"))!='no'){
		//echo "<br /><br />\n<font color=\"red\" size=\"+1\">Le site est en cours de maintenance et temporairement inaccessible.<br />Veuillez nous excuser de ce dérangement et réessayer de vous connecter ultérieurement.</font><br />\n";
		echo "<font color=\"red\" size=\"+1\">Le site est en cours de maintenance et temporairement inaccessible.<br />Veuillez nous excuser de ce dérangement et réessayer de vous connecter ultérieurement.</font><br />\n";
	}
	//==================================

	echo "<form action='login.php' method='post' style='width: 100%; margin-top: 24px; margin-bottom: 48px;'>\n";
	// Utilisation multisite
	if (getSettingValue("multisite") == "y" AND isset($_GET["rne"]) AND $_GET["rne"] != '') {
		echo '	<input type="hidden" name="rne" value="'.$_GET["rne"].'" />';
	}
	// correction Regis : align='center' invalide en Strict
	// echo "<div align='center'>\n";
	echo "<div style='width:400px;margin-right:auto;margin-left:auto'>\n";

		echo "<div id='div_login'>\n";

			echo "<div id='div_login_entete'>\n";
				//echo "<h2>".getSettingValue("gepiSchoolName")."</h2>\n";
				// Correction Régis login.php : h2 doit suivre h1
				echo "<h1>".getSettingValue("gepiSchoolName")."</h1>\n";
				echo "<p class='annee'>".getSettingValue("gepiYear")."</p>\n";
			echo "</div>\n";


			if (isset($message)) {
				echo("<p style='color:red; margin:0; padding:0;'>" . $message . "</p>");
			} else {
				echo "<p style='margin:0; padding:0;'>Afin d'utiliser Gepi, vous devez vous identifier.</p>";
			}

			echo "<div>&nbsp;</div>\n";
			//echo "<table style='width: 25em; margin:0; padding-top: 10px; padding-right: 15px; padding-left: 15px; margin-left: auto;' cellpadding='3' cellspacing='0'>\n";
			//border='1'
			echo "<table cellpadding='3' cellspacing='0' summary=\"Saisie de compte/mot de passe\">\n";

			echo "<tr>
				<td style='text-align:center; width:80px; margin-left: 15px;'><img src='images/icons/lock.png' width='48' height='48' alt='Cadenas' /></td>
				<td>
					<table summary=\"Saisie de compte/mot de passe (bis)\">
					<tr>
						<td style='text-align: right; width: 50%; font-variant: small-caps;'><label for='login'>Identifiant</label></td>
						<td style='text-align: center; width: 40%;'><input type='text' id='login' name='login' size='16' tabindex='1' /></td>
					</tr>
					<tr>
						<td style='text-align: right; width: 50%; font-variant: small-caps;'><label for='no_anti_inject_password'>Mot de passe</label></td>
						<td style='text-align: center; width: 40%;'><input type='password' id='no_anti_inject_password' name='no_anti_inject_password' size='16' onkeypress='capsDetect(arguments[0]);' tabindex='2' /></td>
					</tr>
					</table>
				</td>
			</tr>
			</table>\n";

			//border='1'
			echo "<table width='100%' cellpadding='3' cellspacing='0' summary=\"Récupération de mot de passe\">
			<tr>
				<!--td style='text-align: center; padding-top: 10px;'-->\n";
				// padding-left: 15px
				echo "<td style='text-align: left; padding-top: 10px; padding-left: 10px;'>\n";

				if (getSettingValue("enable_password_recovery") == "yes") {
					echo "<a class='small' href='recover_password.php'>Mot de passe oublié ?</a>";
				}

				echo "&nbsp;</td>\n";

				echo "<td style='text-align: center; padding-top: 10px;'><input type='submit' name='submit' value='Valider' style='font-variant: small-caps;' tabindex='3' /></td>\n";
			echo "</tr>\n";
			echo "</table>\n";

		echo "</div>\n";
	echo "</div>\n";
	echo "</form>\n";

	?>

	<script type="text/javascript">
	<!--
		//function mel(destinataire){
		//	chaine_mel = "mailto:"+destinataire+"?subject=[GEPI]";
		function pigeon(a,b){
			chaine_mel = "mailto:"+a+"_CHEZ_"+b+"?subject=[GEPI]";
			//chaine_mel += "&body=Bonjour,\r\nCordialement.\r\n";
			//chaine_mel += "&body=Bonjour,\\r\\nCordialement.\\r\\n";
			chaine_mel += "&body=Pour que le mail parvienne à son destinataire, pensez à remplacer la chaine de caractères _CHEZ_ par un @";
			//chaine_mel += "&body=Bonjour";
			location.href = chaine_mel;
		}

		/*
		function pigeon2(tab){
			chaine_tmp="";
			for(i=0;i<tab.length;i=i+2){
				chaine_tmp=chaine_tmp+","+tab[i]+"_CHEZ_"+tab[i+1];
			}
			alert("chaine_tmp="+chaine_tmp);
			chaine_mel = "mailto:"+a+"_CHEZ_"+b+"?subject=[GEPI]";
			//chaine_mel += "&body=Bonjour,\r\nCordialement.\r\n";
			//chaine_mel += "&body=Bonjour,\\r\\nCordialement.\\r\\n";
			chaine_mel += "&body=Pour que le mail parvienne à son destinataire, pensez à remplacer la chaine de caractères _CHEZ_ par un @";
			//chaine_mel += "&body=Bonjour";
			location.href = chaine_mel;
		}
		*/

		function pigeon2(){
			chaine_tmp="";
			for(i=0;i<adm_adr.length;i=i+2){
				chaine_tmp=chaine_tmp+","+adm_adr[i]+"_CHEZ_"+adm_adr[i+1];
			}
			chaine_tmp=chaine_tmp.substring(1);
			//alert("chaine_tmp="+chaine_tmp);
			chaine_mel = "mailto:"+chaine_tmp+"?subject=[GEPI]";
			//chaine_mel += "&body=Bonjour,\r\nCordialement.\r\n";
			//chaine_mel += "&body=Bonjour,\\r\\nCordialement.\\r\\n";
			chaine_mel += "&body=Pour que le mail parvienne à son destinataire, pensez à remplacer la chaîne de caractères _CHEZ_ par un @ dans l'adresse du destinataire.";
			//chaine_mel += "&body=Bonjour";
			location.href = chaine_mel;
		}


	-->
	</script>

	<div class="center" style="margin-bottom: 32px;">
	<?php
	if ($session_gepi->auth_sso) {
		echo "<p><a href='login_sso.php'><img src='images/icons/forward_.png' /> Se connecter en utilisant le service d'authentification unique</a></p><br/>";
	}
	?>
	<p><a href="javascript:centrerpopup('gestion/info_vie_privee.php',700,480,'scrollbars=yes,statusbar=no,resizable=yes')"><img src='./images/icons/vie_privee.png' alt='' class='link' /> Informations vie privée</a></p>

	<?php
		if(getSettingValue("gepiAdminAdressPageLogin")!='n'){
			$gepiAdminAdress=getSettingValue("gepiAdminAdress");
			//$tmp_adr=explode("@",$gepiAdminAdress);
			//echo("<a href=\"javascript:pigeon('$tmp_adr[0]','$tmp_adr[1]');\">[Contacter l'administrateur]</a> \n");

			//echo "$gepiAdminAdress<br />";

			$compteur=0;
			$tab_adr=array();
			$tmp_adr1=explode(",",$gepiAdminAdress);
			for($i=0;$i<count($tmp_adr1);$i++){
				//echo "\$tmp_adr1[$i]=$tmp_adr1[$i]<br />";
				$tmp_adr2=explode("@",$tmp_adr1[$i]);
				//echo "\$tmp_adr2[0]=$tmp_adr2[0]<br />";
				//echo "\$tmp_adr2[1]=$tmp_adr2[1]<br />";
				if((isset($tmp_adr2[0]))&&(isset($tmp_adr2[1]))) {
					$tab_adr[$compteur]=$tmp_adr2[0];
					$compteur++;
					$tab_adr[$compteur]=$tmp_adr2[1];
					$compteur++;
				}
			}

			echo "<script type='text/javascript'>\n";
			echo "adm_adr=new Array();\n";
			for($i=0;$i<count($tab_adr);$i++){
				echo "adm_adr[$i]='$tab_adr[$i]';\n";
			}
			echo "</script>\n";

			if(count($tab_adr)>0){
				//echo("<a href=\"javascript:pigeon2(adm_adr);\">[Contacter l'administrateur]</a> \n");
				echo("<p><a href=\"javascript:pigeon2();\">[Contacter l'administrateur]</a></p>\n");
			}
		}
	?>

	</div>


	<div id="new_login_footer">
		<a href="http://gepi.mutualibre.org/">GEPI : Outil de gestion, de suivi, et de visualisation graphique des résultats scolaires (écoles, collèges, lycées)</a><br />
		Copyright &copy; 2001-2008
		<?php
			reset($gepiAuthors);
			$i = 0;
			while (list($name, $adress) = each($gepiAuthors)) {
				if ($i != "0") echo ", ";
				//echo("<a href=\"mailto:" . $adress . "\">" . $name . "</a> ");
				$tmp_adr=explode("@",$adress);
				//echo("<a href=\"javascript:pigeon('" . $adress . "');\">" . $name . "</a> ");
				if((isset($tmp_adr[0]))&&(isset($tmp_adr[1]))) {
					echo("<a href=\"javascript:pigeon('$tmp_adr[0]','$tmp_adr[1]');\">" . $name . "</a> \n");
				}
				$i++;
			}
			echo "<br/><br/>\n";
			echo "<img src='".$gepiPath."/images/php-powered.png' alt='php powered' />&nbsp;\n";
			echo "<img src='".$gepiPath."/images/mysql-powered.png' alt='mysql powered' />\n";

		?>
	</div>

	<?php
		if(getSettingValue("gepi_pmv")!="n"){
			if (@file_exists($gepiPath."/pmv.php")) require ($gepiPath."/pmv.php");
		}
	?>

</div>
</body>
</html>
