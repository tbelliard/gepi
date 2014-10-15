<?php
/*
 *
 * Copyright 2009-2012 Josselin Jacquard, Stephane Boireau
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
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

header('Content-Type: text/html; charset=utf-8');

// On désamorce une tentative de contournement du traitement anti-injection lorsque register_globals=on
if (isset($_GET['traite_anti_inject']) OR isset($_POST['traite_anti_inject'])) $traite_anti_inject = "yes";

// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

require_once("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");
//echo("Debug Locale : ".setLocale(LC_TIME,0));

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}


$sql="SELECT 1=1 FROM droits WHERE id='/cahier_texte_2/ajax_affichage_car_spec.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/cahier_texte_2/ajax_affichage_car_spec.php',
administrateur='F',
professeur='V',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='CDT2: Caractères spéciaux à insérer',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}


if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}


//On vérifie si le module est activé
if (getSettingValue("active_cahiers_texte")!='y') {
	die("Le module n'est pas activé.");
}

$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

$cdt2_car_spec_liste=getPref($_SESSION['login'], "cdt2_car_spec_liste", "");

if($cdt2_car_spec_liste=="") {
	echo "<p style='color:red'>Aucun caractère spécial n'a encore été choisi.<br /><a href='cdt_choix_caracteres.php' title=\"Modifier la liste des caractères spéciaux.\" target='_blank'><img src='../images/edit16.png' class='icone16' alt='Editer' /> Sélectionner les caractères préférés</a></p>";
}
else {
	$tab=explode(';', $cdt2_car_spec_liste);

	echo "<div style='float:right; width:20px; text-align:center;'><a href='cdt_choix_caracteres.php' title=\"Modifier la liste des caractères spéciaux.\" target='_blank'><img src='../images/edit16.png' class='icone16' alt='Editer' /></a></div>";

	echo "<p>Cliquez sur le caractère à insérer</p>";

	// Le dernier est vide
	// Exemple de contenu: &pi;&lArr;&rArr;&hArr;&isin;&notin;&asymp;&ne;&le;&ge;&perp;
	//                     Après le dernier point virgule, c'est vide.
	for($loop=0;$loop<count($tab)-1;$loop++) {
		if($loop%2==0) {
			$bg="white";
		}
		else {
			$bg="silver";
		}
		echo "<div style='float:left; width:3em;'><input type='button' name='bouton_$loop' value=\"".$tab[$loop].";\" style='background-color:$bg;' onclick=\"insere_texte_dans_ckeditor('".$tab[$loop].";')\" /></div>";
	}
}
?>
