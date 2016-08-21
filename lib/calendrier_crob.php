<?php
/**
 *
 * Copyright 2016 Stephane Boireau
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
//require_once("../lib/initialisationsPropel.inc.php");
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

$sql="SELECT 1=1 FROM droits WHERE id='/lib/calendrier_crob.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
	$sql="INSERT INTO droits SET id='/lib/calendrier_crob.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Calendrier',
statut='';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : "");

$mois=isset($_POST['mois']) ? $_POST['mois'] : (isset($_GET['mois']) ? $_GET['mois'] : NULL);
$annee=isset($_POST['annee']) ? $_POST['annee'] : (isset($_GET['annee']) ? $_GET['annee'] : NULL);

//debug_var();

include("./calendrier_crob.inc.php");
if((isset($mois))&&(isset($annee))&&(isset($id_classe))&&(isset($mode))&&($mode=="popup")) {
	if((!preg_match("/^[0-9]{1,2}$/", $mois))||($mois<1)||($mois>12)) {
		echo "Valeur mois $mois invalide.";
		die();
	}
	if((!preg_match("/^[0-9]{4}$/", $annee))) {
		echo "Valeur année $annee invalide.";
		die();
	}
	if(($id_classe!="")&&(!preg_match("/^[0-9]*$/", $id_classe))) {
		echo "Valeur id_classe $id_classe invalide.";
		die();
	}

	echo affiche_calendrier_crob($mois, $annee, $id_classe, $mode);
	die();
}

//**************** DEBUT EN-TETE ***************
if((!isset($mode))||($mode!="popup")) {
	$titre_page = "Calendrier";
	$_SESSION['cacher_header'] = "y";
	require_once("../lib/header.inc.php");
}
//**************** FIN EN-TETE *****************

echo "
<p style='margin-bottom:1em;'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>

<h1>Calendrier</h1>

<br />";

echo affiche_calendrier_crob($mois, $annee, $mode);

if((!isset($mode))||($mode!="popup")) {
	require_once("../lib/footer.inc.php");
}
?>
