<?php
/*
 *
 * Copyright 2001-2004 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

//**************** EN-TETE *****************
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *************
$fiche=isset($_POST["fiche"]) ? $_POST["fiche"] : (isset($_GET["fiche"]) ? $_GET["fiche"] : "personnels");

$nom = 'BONNOT';
$prenom = 'Jean';
$identifiant = "JBONNOT";
$mdp = "5Cdff45DF";
$email = 'jbonnot@ici.fr';

switch ($fiche) {
case 'personnels' :
	$impression = getSettingValue("Impression");
	$nb_fiches = getSettingValue("ImpressionNombre");
	break;
case 'parents' :
	$impression = getSettingValue("ImpressionFicheParent");
	$nb_fiches = getSettingValue("ImpressionNombreParent");
	break;
case 'responsables' :
	$impression = getSettingValue("ImpressionFicheParent");
	$nb_fiches = getSettingValue("ImpressionNombreParent");
	break;
case 'eleves' :
	$impression = getSettingValue("ImpressionFicheEleve");
	$nb_fiches = getSettingValue("ImpressionNombreEleve");
	break;
}

for ($i=0;$i<$nb_fiches;$i++) {
	echo "<p>A l'attention de  <span class = \"bold\">" . $nom . " " . $prenom . "</span>";
	echo "<br />Nom de login : <span class = \"bold\">" . $identifiant. "</span>";
	echo "<br />Mot de passe : <span class = \"bold\">" . $mdp . "</span>";
	echo "<br />Adresse E-mail : <span class = \"bold\">" . $email . "</span>";
	echo "</p>";

	echo "<p style='font-variant:small-caps;color:red;'>La ligne donnant le mot de passe de l'utilisateur
ne figure sur la fiche <b>QUE SI</b> cette dernière est imprimée dès la création de l'utilisateur.</p>";

	echo $impression;
}
require("../lib/footer.inc.php");
?>
