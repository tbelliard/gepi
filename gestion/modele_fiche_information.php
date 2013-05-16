<?php
/*
 *
 * Copyright 2001-2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

if(!isset($_GET['user_login'])) {
	$nom = 'BONNOT';
	$prenom = 'Jean';
	$identifiant = "JBONNOT";
	$mdp = "5Cdff45DF";
	$email = 'jbonnot@ici.fr';
	$fiche_demo="y";
}
else {
	$sql="SELECT * FROM utilisateurs WHERE login='".$_GET['user_login']."';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		echo "<p style='color:red'>Le login proposé (<em>".$_GET['user_login']."</em>) n'existe pas</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$lig=mysql_fetch_object($res);
	$nom = casse_mot($lig->nom, "maj");
	$prenom = casse_mot($lig->prenom, "majf2");
	$identifiant = $_GET['user_login'];
	$email = $lig->email;

	if($fiche=="eleves") {
		$chaine_classes=get_chaine_liste_noms_classes_from_ele_login($_GET['user_login']);
	}
	elseif($fiche=="responsables") {
		$tab=get_enfants_from_resp_login($_GET['user_login'], 'avec_classe');
		$chaine_enfants="";
		for($loop=1;$loop<count($tab);$loop+=2) {
			if($loop>1) {
				$chaine_enfants.=", ";
			}
			$chaine_enfants.=$tab[$loop];
		}
	}

	$fiche_demo="n";
}

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
	echo "<table><tr><td>A l'attention de </td><td><span class = \"bold\">" . $nom . " " . $prenom . "</span></td></tr>
	<tr><td>Nom de login : </td><td class = \"bold\">" . $identifiant. "</td></tr>";
	if(isset($mdp)) {
		echo "
	<tr><td>Mot de passe : </td><td class = \"bold\">" . $mdp . "</td></tr>";
	}
	if(isset($email)) {
		echo "
	<tr><td>Adresse E-mail : </td><td class = \"bold\">" . $email . "</td></tr>";
	}

	if(isset($chaine_enfants)) {
		echo "
	<tr><td>Responsable de : </td><td class = \"bold\">" . $chaine_enfants . "</td></tr>";
	}
	elseif($chaine_classes) {
		echo "
	<tr><td>Classe : </td><td class = \"bold\">" . $chaine_classes . "</td></tr>";
	}

	echo "
</table>";

	if($fiche_demo=="y") {
		echo "<p style='font-variant:small-caps;color:red;'>La ligne donnant le mot de passe de l'utilisateur
ne figure sur la fiche <b>QUE SI</b> cette dernière est imprimée dès la création de l'utilisateur.</p>";
	}

	echo $impression;
}
require("../lib/footer.inc.php");
?>
