<?php
/*
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stéphane Boireau, Julien Jocal
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

// Il faudrait ajouter de la sécurité, mais je ne sais pas quoi :?


// ===================== Initialisation des variables ===================
//echo $_SERVER["SERVER_NAME"];
$nbre = isset($_GET["nbre"]) ? $_GET["nbre"] : NULL;
$lesrne = isset($_GET["lesrne"]) ? $_GET["lesrne"] : NULL;
$aff_options = NULL;

$rne = explode("|", $lesrne);
	for($a = 0 ; $a < $nbre ; $a++){

		// On affiche les différentes propositions
		$aff_options .= '<option value="'.$rne[$a].'">'.$rne[$a].'</option>'."\n";

	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Choix de son RNE</title>
	</head>

	<body>

<div style="width: 500px; height: 300px; background-color: lightblue; margin-left: 200px; margin-top: 200px; text-align: center;">
	<h3>Choisissez votre &eacute;tablissement en fonction de son RNE : </h3>

		<form method="get" action="login_sso.php" id="liste_rne">
			<input type="hidden" name="action" value="choix" />

			<select name="rne" onchange="document.getElementById('liste_rne').submit();">
				<option value="RNE">Choix du rne</option>
<?php
	echo $aff_options;
?>
			</select>
		</form>
</div>

<p>Nous sommes le <?php echo date("d m Y"); ?> &agrave; <?php echo date("h:i:s"); ?> et votre adresse IP est <?php echo $_SERVER["REMOTE_ADDR"] ; ?>.</p>

	</body>
</html>
