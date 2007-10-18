<?php

/*
* $Id$
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

// Initialisations files
require_once("../lib/initialisations.inc.php");


// Resume session
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
};


if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

// Initialisation
$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);

include "../lib/periodes.inc.php";
//**************** EN-TETE *****************
$titre_page = "Saisie des absences";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

if (!isset($id_classe)) {
	echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
	if ($_SESSION['statut']=="cpe") {
		$calldata = mysql_query("SELECT DISTINCT c.* FROM classes c, j_eleves_cpe e, j_eleves_classes jc WHERE (e.cpe_login = '".$_SESSION['login']."' AND jc.login = e.e_login AND c.id = jc.id_classe)  ORDER BY classe");
	} else {
		$calldata = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
	}

	$nombreligne = mysql_num_rows($calldata);
	echo "</p>\n<p>Total : $nombreligne classes - ";
	echo "Cliquez sur la classe pour laquelle vous souhaitez saisir les absences :</p>\n";
	echo "<p>Remarque : s'affichent toutes les classes pour lesquelles vous êtes responsable du suivi d'un moins un élève de la classe.</p>\n";

	/*
	$i = 0;
	while ($i < $nombreligne){
		$id_classe = mysql_result($calldata, $i, "id");
		$classe_liste = mysql_result($calldata, $i, "classe");
		echo "<br /><a href='index.php?id_classe=$id_classe'>$classe_liste</a>\n";
		$i++;

	}
	*/

	$i = 0;
	unset($tab_lien);
	unset($tab_txt);
	while ($i < $nombreligne){
		$tab_lien[$i] = "index.php?id_classe=".mysql_result($calldata, $i, "id");
		$tab_txt[$i] = mysql_result($calldata, $i, "classe");
		$i++;

	}
	tab_liste($tab_txt,$tab_lien,3);



	echo "<br />\n";
} else {
	// On choisit la période :
	echo "<p class=bold><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Choisir une autre classe</a></p>\n";

	$call_classe = mysql_query("SELECT classe FROM classes WHERE id = '$id_classe'");
	$classe = mysql_result($call_classe, "0", "classe");
	echo "<h2>Classe de ".$classe."</h2>\n";
	echo "<p><b>Saisie manuelle - Choisissez la période : </b></p>\n<ul>\n";
	$i="1";
	while ($i < $nb_periode) {
		if ($ver_periode[$i] == "N") {
			echo "<li><a href='saisie_absences.php?id_classe=$id_classe&amp;periode_num=$i'>".ucfirst($nom_periode[$i])."</a></li>\n";
		} else {
			echo "<li>".ucfirst($nom_periode[$i])." (".$gepiClosedPeriodLabel.")</li>\n";
		}
	$i++;
	}
	echo "</ul>\n";
	$i="1";
	// On propose l'importation à partir d'un fichier GEP
	while ($i < $nb_periode) {
		if ($ver_periode[$i] == "N") {
			echo "<p class='bold'>".ucfirst($nom_periode[$i])." - Importation à partir du fichier F_EABS.DBF de la base GEP (fichier F_NOMA.DBF également requis) :</p>\n";
			echo "<ul><li><a href='import_absences_gep.php?id_classe=$id_classe&amp;periode_num=$i'>Importer les absences à partir du fichier F_EABS.DBF</a></li></ul>\n";
		}
		$i++;
	}

	$i="1";
	// On propose l'importation à partir d'un fichier GEP
	while ($i < $nb_periode) {
		if ($ver_periode[$i] == "N") {
			echo "<p class='bold'>".ucfirst($nom_periode[$i])." - Importation à partir du fichier <b>exportAbsences.xml</b> de <b>Sconet</b> :</p>\n";
			echo "<ul><li><a href='import_absences_sconet.php?id_classe=$id_classe&amp;periode_num=$i'>Importer les absences à partir du fichier exportAbsences.xml</a></li></ul>\n";
		}
		$i++;
	}
}
require "../lib/footer.inc.php";
?>