<?php
/**
 * $Id$
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * 
 * @package General
 * @subpackage Installation
 * @todo à corriger W3C
*/

/* This file is part of GEPI.
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
//test version de php
if (version_compare(PHP_VERSION, '5') < 0) {
    die('GEPI nécessite PHP5 pour fonctionner');
}
header('Content-Type: text/html; charset=UTF-8');
require_once("../lib/global.inc.php");
$nom_fic = "../secure/connect.inc.php";

function test_ecriture_secure() {
	$ok = 'no';
	if ($f = @fopen("../secure/test", "w")) {
		@fputs($f, '<'.'?php $ok = "yes"; ?'.'>');
		@fclose($f);
		include("../secure/test");
		$del = @unlink("../secure/test");
	}
	return $ok;
}


function begin_html() {
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Cache-Control" content="no-cache" />
<meta http-equiv="Expires" content="0" />

<title>Installation de GEPI</title>

<link rel="stylesheet" href="../css/style.css" type="text/css" />
<link rel="shortcut icon" type="image/x-icon" href="../favicon.ico" />
<link rel="icon" type="image/ico" href="../favicon.ico" />
</head>
<body>
	<div style="width: 30em; margin: auto;">
<?php
}

function end_html() {
	?>
	</div>
</body>
</html>
<?php
}

unset($etape);
$etape = isset($_POST["etape"]) ? $_POST["etape"] : (isset($_GET["etape"]) ? $_GET["etape"] : NULL);


if (file_exists($nom_fic)) {
	require_once("../secure/connect.inc.php");
	if (@($GLOBALS["mysqli"] = mysqli_connect("$dbHost",  "$dbUser",  "$dbPass"))) {
		if (@((bool)mysqli_query($GLOBALS["mysqli"], "USE `$dbDb`"))) {
			$call_test = @mysqli_query($GLOBALS["mysqli"], "SELECT * FROM setting WHERE name='sessionMaxLength'");
			$test2 = @mysqli_num_rows($call_test);
			$call_test = @mysqli_query($GLOBALS["mysqli"], "SELECT * FROM utilisateurs");
			$test3 = @mysqli_num_rows($call_test);
			if (($test2 !=0) and ($test3 !=0)) {
				begin_html();
				if ($etape == 5) {
					echo "<br /><h2 class='gepi'>Dernière étape : C'est terminé !</h2>\n";
					echo "<p>&nbsp;</p>\n";
					echo "<p>Vous pouvez maintenant commencer à utiliser GEPI ...</p>\n";
					echo "<p>Pour vous connecter la première fois en tant qu'administrateur, utilisez le nom de connection \"admin\" et le mot de passe \"azerty\". N'oubliez pas de changer le mot de passe !</p>\n";
					echo "<br /><center><a href = '../login.php'>Se connecter à GEPI</a></center>\n";
				} else {
					echo "<h2 class='gepi'>Espace interdit - GEPI est déjà installé.</h2>\n";
				}
				end_html();
				die();
			}
		}
	}
}

if ($etape == 4) {

	begin_html();

	echo "<br /><h2 class='gepi'>Quatrième étape : Création des tables de la base</h2>\n";
	echo "<p>";

	$link = ($GLOBALS["mysqli"] = mysqli_connect($_POST['adresse_db'],  $_POST['login_db'],  $_POST['pass_db']));

	if ($_POST['choix_db'] == "new_gepi") {
		$sel_db = $_POST['table_new'];
		$result=mysqli_query($GLOBALS["mysqli"], "CREATE DATABASE `$sel_db`;");
	}
	else {
		$sel_db = $_POST['choix_db'];
	}
	((bool)mysqli_query($GLOBALS["mysqli"], "USE `$sel_db`"));
	mysqli_query($GLOBALS["mysqli"], "SET NAMES UTF8");
	$queryBase = mysqli_query($GLOBALS["mysqli"], "ALTER DATABASE  CHARACTER SET utf8 COLLATE utf8_general_ci");
	

	$fd = fopen("../sql/structure_gepi.sql", "r");
	$result_ok = 'yes';
	while (!feof($fd)) {
		//=============================================
		// MODIF: boireaus d'après P.Chadefaux 20071110
		//$query = fgets($fd, 5000);
		// Ligne 113 du structure_gepi.sql, le CREATE TABLE `model_bulletin` comporte 6799 caractères.
		//$query = fgets($fd, 8000);
		//=============================================
		$query=" ";
		while ((mb_substr($query,-1)!=";") && (!feof($fd))) {
			$t_query = fgets($fd, 8000);
			if (mb_substr($t_query,0,3)!="-- ") $query.=$t_query;
			$query = trim($query); 
		}
		//=============================================
		// MODIF: boireaus 20080218
		//if (mb_substr($query,-1)==";") {
		//if((mb_substr($query,-1)==";")&&(mb_substr($query,0,3)!="-- ")) {
		//=============================================
		if ($query!="") {
			$reg = mysqli_query($GLOBALS["mysqli"], $query);
			if (!$reg) {
				echo "<p><font color=red>ERROR</font> : '$query' : ";
				echo "<p>Erreur retournée : ".mysqli_error($GLOBALS["mysqli"])."</p>\n";
				$result_ok = 'no';
			}
		}
	}
	fclose($fd);

	if ($result_ok == 'yes') {
		$fd = fopen("../sql/data_gepi.sql", "r");
		while (!feof($fd)) {
			$query = fgets($fd, 5000);
			$query = trim($query);
			//=============================================
			// MODIF: boireaus 20080218
			//if (mb_substr($query,-1)==";") {
			if((mb_substr($query,-1)==";")&&(mb_substr($query,0,3)!="-- ")) {
			//=============================================
				$reg = mysqli_query($GLOBALS["mysqli"], $query);
				if (!$reg) {
					echo "<p><font color=red>ERROR</font> : '$query'</p>\n";
					echo "<p>Erreur retournée : ".mysqli_error($GLOBALS["mysqli"])."</p>\n";
					$result_ok = 'no';
				}
			}
		}
		fclose($fd);
	}

	if ($result_ok == 'yes') {
		$ok = 'yes';
		if (file_exists($nom_fic)) @unlink($nom_fic);
		if (file_exists("../secure/connect.cfg")) @unlink("../secure/connect.cfg");
		$f = @fopen($nom_fic, "wb");
		if (!$f) {
			$ok = 'no';
		} else {
			$url = parse_url($_SERVER['REQUEST_URI']);
			//$pathgepi = explode("/",$url['path']);
			//$gepipath = "/".$pathgepi[1];
			$gepipath = mb_substr($url['path'], 0, -24);
			$conn = "<"."?php\n";
			$conn .= "# La ligne suivante est à modifier si vous voulez utiliser le multisite\n";
                        $conn .= "# Regardez le fichier modeles/connect-modele.inc.php pour information\n";
			$conn .= "\$multisite = 'n';\n";
			$conn .= "# Les cinq lignes suivantes sont à modifier selon votre configuration\n";
			$conn .= "# Pensez à renommer ce fichier connect.cfg.php en connect.inc.php\n";
			$conn .= "#\n";
			$conn .= "# ligne suivante : le nom du serveur qui herberge votre base mysql.\n";
			$conn .= "# Si c'est le même que celui qui heberge les scripts, mettre \"localhost\"\n";
			$conn .= "\$dbHost=\"".$_POST['adresse_db']."\";\n";
			$conn .= "# ligne suivante : le nom de votre base mysql\n";
			$conn .= "\$dbDb=\"$sel_db\";\n";
			$conn .= "# ligne suivante : le nom de l'utilisateur mysql qui a les droits sur la base\n";
			$conn .= "\$dbUser=\"".$_POST['login_db']."\";\n";
			$conn .= "# ligne suivante : le mot de passe de l'utilisateur mysql ci-dessus\n";
			$conn .= "\$dbPass=\"".$_POST['pass_db']."\";\n";
			$conn .= "# Chemin relatif vers GEPI\n";
			$conn .= "\$gepiPath=\"$gepipath\";\n";
			$conn .= "#\n";
			$conn .= "\$db_nopersist=true;\n";
			$conn .= "#\n";
			$conn .= "# Authentification par CAS ?\n";
			$conn .= "# Si vous souhaitez intégrer Gepi dans un environnement SSO avec CAS,\n";
			$conn .= "# vous devrez renseigner le fichier /secure/config_cas.inc.php avec les\n";
			$conn .= "# informations nécessaires à l'identification du serveur CAS\n";
			$conn .= "\$use_cas = false; // false|true\n";
			$conn .= "?".">";

			@fputs($f, $conn);
			if (!@fclose($f)) $ok='no';
		}

	// si 'encodage_nom_photo'=="yes" création du fichier témoin 
	// pour l'encodage des noms de fichier des photos élèves
	if ($result_ok == 'yes') {
	// on récupère la valeur de 'encodage_nom_photo' dans la table 'setting'
		$R_encodage=@mysqli_query($GLOBALS["mysqli"], "SELECT `VALUE` FROM `setting` WHERE `NAME`='encodage_nom_photo' LIMIT 1");
		if (!$R_encodage) {$ok='no';
		} else {
				if ($t_encodage=@mysqli_fetch_assoc($R_encodage)) {
				$encodage=$t_encodage["VALUE"];
					if ($encodage=="yes") {
						// on récupère la valeur de 'alea_nom_photo' dans la table 'setting'
						$R_alea=@mysqli_query($GLOBALS["mysqli"], "SELECT `VALUE` FROM `setting` WHERE `NAME`='alea_nom_photo' LIMIT 1");
						if (!$R_alea) {$ok='no';
						} else {
							if ($t_alea=@mysqli_fetch_assoc($R_alea)) {
								$alea=$t_alea["VALUE"];
								// on crée le fichier témoin
								$fic_temoin=@fopen("../photos/eleves/encodage_active.txt","w");
								if (!$fic_temoin) {
									$ok = 'no';
								} else {
									// la valeur à écrire doit être conforme à la fonction 'encode_nom_photo()' de 'lib/share.inc.php'
									$retour=@fwrite($fic_temoin,substr(md5($alea."nom_photo"),0,5)."nom_photo");
									if ($retour===false || !@fclose($fic_temoin)) $ok='no';
									}
								}
							}
						}
					}
				}
	}

		if ($ok == 'yes') {
			echo "<B>La structure de votre base de données est installée.</B>\n<p>Vous pouvez passer à l'étape suivante.</p>\n";
			echo "<FORM ACTION='install.php' METHOD='post'>\n";
			echo "<INPUT TYPE='hidden' NAME='etape' VALUE='5' />\n";
			echo "<DIV align='right'><INPUT TYPE='submit' CLASS='fondl' NAME='Valider' VALUE='Suivant >>' /></div>\n";
			echo "</FORM>\n";
		}
	}

	if (($result_ok != 'yes') || ($ok != 'yes')) {
		echo "<p><strong>L'opération a échoué.</strong> Retournez à la page précédente, sélectionnez une autre base ou créez-en une nouvelle. Vérifiez les informations fournies par votre hébergeur.</p>\n";
	}

	end_html();

}

else if ($etape == 3) {

	begin_html();

	echo "<h1 class='gepi'>Troisième étape : Choix de votre base</h1>\n";
	echo "<p>&nbsp;</p>\n";

	echo "<form action='install.php' method='post'>\n";
	echo "<p><input type='hidden' name='etape' value='4' />\n";
	echo "<input type='hidden' name='adresse_db'  value=\"".$_POST['adresse_db']."\" size='40' />\n";
	echo "<input type='hidden' name='login_db' value=\"".$_POST['login_db']."\" />\n";
	echo "<input type='hidden' name='pass_db' value=\"".$_POST['pass_db']."\" /></p>\n";

	$link = @($GLOBALS["mysqli"] = mysqli_connect($_POST['adresse_db'], $_POST['login_db'], $_POST['pass_db']));
	$result = @(($___mysqli_tmp = mysqli_query($GLOBALS["mysqli"], "SHOW DATABASES")) ? $___mysqli_tmp : false);

	echo "<fieldset><label><strong>Choisissez votre base :</strong><br /></label>\n";
	$checked = false;
	if ($result AND (($n = @mysqli_num_rows($result)) > 0)) {
		echo "<p><strong>Le serveur MySQL contient plusieurs bases de données.<br />Sélectionnez celle dans laquelle vous voulez implanter GEPI</strong></p>\n";
		echo "<ul>\n";
		$bases = "";
		for ($i = 0; $i < $n; $i++) {
			$table_nom = ((mysqli_data_seek($result,  $i) && (($___mysqli_tmp = mysqli_fetch_row($result)) !== NULL)) ? array_shift($___mysqli_tmp) : false);
			//$base = "<input name=\"choix_db\" value=\"".$table_nom."\" type='radio' id='tab$i'";
			//$base_fin = " /><label for='tab$i'>".$table_nom."</label><br />\n";
			$base = "<li style='list-style-type:none;'><input name=\"choix_db\" value=\"".$table_nom."\" type='radio' id='tab$i'";
			$base_fin = " /><label for='tab$i'>".$table_nom."</label></li>\n";
			if ($table_nom == $_POST['login_db']) {
				$bases = "$base checked='checked'$base_fin".$bases;
				$checked = true;
			}
			else {
				$bases .= "$base$base_fin\n";
			}
		}
		echo $bases."</ul>\n";
		echo "<p>ou... </p>";
	}
	else {
		echo "<strong>Le programme d'installation n'a pas pu lire les noms des bases de données installées.</strong>Soit aucune base n'est disponible, soit la fonction permettant de lister les bases a été désactivée pour des raisons de sécurité.</p>\n";
		if ($_POST['login_db']) {
			echo "<p>Dans la seconde alternative, il est probable qu'une base portant votre nom de connexion soit utilisable :<p>\n";
			echo "<ul>\n";
			echo "<input name=\"choix_db\" value=\"".$_POST['login_db']."\" type='radio' id='stand' CHECKED />\n";
			echo "<label for='stand'>".$_POST['login_db']."</label><br />\n";
			echo "</ul>\n";
			echo "<p>ou... </p>";
			$checked = true;
		}
	}
	echo "<INPUT NAME=\"choix_db\" VALUE=\"new_gepi\" TYPE=Radio id='nou'";
	if (!$checked) echo " CHECKED";
	echo " /> <label for='nou'>Créer une nouvelle base de données :</label> ";
	echo "<INPUT TYPE='text' NAME='table_new' CLASS='fondo' VALUE=\"gepi\" SIZE='20' /></fieldset>\n\n";
	echo "<p><b>Attention</b> : lors de la prochaine étape :</p>\n";
	echo "<ul>\n";
	if (file_exists($nom_fic)) echo "<li>le fichier \"".$nom_fic."\" sera actualisé avec les données que vous avez fourni,</li>\n";
	echo "<LI>les tables GEPI seront créées dans la base sélectionnée. Si celle-ci contient déjà des tables GEPI, ces tables, ainsi que les données qu'elles contiennent, seront supprimées et remplacées par une nouvelle structure.</LI>\n</ul>\n";

	echo "<div style='text-align:right'><input type='submit' class='fondl' name='Valider' value='Suivant >>' /></div>\n";


	echo "</form>\n";

	end_html();

}

else if ($etape == 2) {
	begin_html();

	echo "<br /><h2 class='gepi'>Deuxième étape : Essai de connexion au serveur Mysql</h2>\n";

	echo "<!--";
	$link = ($GLOBALS["mysqli"] = mysqli_connect($_POST['adresse_db'], $_POST['login_db'], $_POST['pass_db']));
	$db_connect = ((is_object($GLOBALS["mysqli"])) ? mysqli_errno($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false));
	echo "-->\n";

	//echo "<p>\n";

	if (($db_connect=="0") && $link){
		echo "<B>La connexion a réussi.</B><p> Vous pouvez passer à l'étape suivante.</p>\n";

		echo "<form action='install.php' method='post'>\n";
		echo "<p><input type='hidden' name='etape' value='3' />\n";
		echo "<input type='hidden' name='adresse_db'  value=\"".$_POST['adresse_db']."\" size='40' />\n";
		echo "<input type='hidden' name='login_db' value=\"".$_POST['login_db']."\" />\n";
		echo "<input type='hidden' name='pass_db' value=\"".$_POST['pass_db']."\" /></p>\n";

		echo "<div style='text-align:right'><p><input type='submit' class='fondl' name='Valider' value='Suivant >>' /></p></div>\n";

		echo "</form>\n";
	}
	else {
		echo "<B>La connexion au serveur MySQL a échoué.</B>\n";
		echo "<p>Revenez à la page précédente, et vérifiez les informations que vous avez fournies.</p>\n";
		echo mysqli_error($GLOBALS["mysqli"]);
	}

	end_html();

}
else if ($etape == 1) {
	begin_html();

	echo "<br />\n<h2 class='gepi'>Première étape : la connexion MySQL</h2>\n";

	echo "<P>Vous devez avoir en votre possession les codes de connexion au serveur MySQL. Si ce n'est pas le cas, contactez votre hébergeur ou bien l'administrateur technique du serveur sur lequel vous voulez implanter GEPI.</p>\n";

	unset($adresse_db);
	$adresse_db = isset($_POST["adresse_db"]) ? $_POST["adresse_db"] : 'localhost';
	$login_db = '';
	$pass_db = '';

	echo "<FORM ACTION='install.php' METHOD='post'>\n";
	echo "<INPUT TYPE='hidden' NAME='etape' VALUE='2' />\n";
	echo "<fieldset><label><B>Adresse de la base de donnée</B><br /></label>\n";
	echo "(Souvent cette adresse correspond à celle de votre site, parfois elle correspond à la mention &laquo;localhost&raquo;, parfois elle est laissée totalement vide.)<br />\n";
	echo "<INPUT  TYPE='text' NAME='adresse_db' CLASS='formo' VALUE=\"$adresse_db\" SIZE='40' /></fieldset><br />\n";
        
        echo "<fieldset><label><B>L'identifiant de connexion</B><br /></label>\n";
        echo "<INPUT TYPE='text' NAME='login_db' CLASS='formo' VALUE=\"$login_db\" SIZE='40' /></fieldset><br />\n";

	echo "<fieldset style='margin:.5em'><label><strong>Le mot de passe de connexion</strong><br /></label>\n";
	echo "<input type='password' name='pass_db' class='formo' value=\"$pass_db\" size='40' /></fieldset>\n";

	echo "<div style='text-align:right'><p><input type='submit' class='fondl' name='Valider' value='Suivant >>' /></p></div>\n";
	echo "</form>\n";

	end_html();

} else if (!$etape) {
	$affiche_etape0 = 'no';
	$file_existe = 'no';
	if (file_exists($nom_fic)) {
		$affiche_etape0 = 'yes';
		$file_existe = 'yes';
	}
	// on test la possibilité d'écrire dans le répertoire
	$test_write = test_ecriture_secure();
	if ($test_write == 'no') $affiche_etape0 = 'yes';

	if ($affiche_etape0 == 'yes') {
		begin_html();
		echo "<h1 class='gepi'>Installation de la base Mysql</h1>\n";
		echo "<form action='install.php' method='post'>\n";
		if ($test_write == 'no') {
			echo "<h3 class='gepi'>Problème de droits d'accès :</h3>\n";
			echo "<p>Le répertoire \"/secure\" n'est pas accessible en écriture.</p>\n";
			echo "<P>Utilisez votre client FTP afin de régler ce problème ou bien contactez l'administrateur technique. Une fois cette manipulation effectuée, vous pourrez continuer en cliquant sur le bouton en bas de la page.</p>\n";
			echo "<INPUT TYPE='hidden' NAME='etape' VALUE='' />\n";
		} else {
			echo "<input type='hidden' name='etape' value='1' /></p>\n";
		}
		if ($file_existe == 'yes') {
			echo "<h3 class='gepi'>Présence d'un fichier ".$nom_fic." :</h3>\n";
			echo "<p>Un fichier nommé <b>\"connect.inc.php\"</b> est actuellement présent dans le répertoire \"/secure\".
			C'est peut-être la trace d'une ancienne installation. Par ailleurs, ce fichier contient peut-être les informations de connexion à la base MySql que vous souhaitez conserver.
			<br /><b>Attention : ce fichier et ce qu'il contient sera supprimé lors de cette nouvelle installation</b>.</p>\n";
		}


		echo "<p><input type='submit' class='fondl' Value = 'Continuer' name='Continuer' /></p>\n";
		echo "</form>\n";
		end_html();
	} else {
		header("Location: ./install.php?etape=1");
	}
}
?>
