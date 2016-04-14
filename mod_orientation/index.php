<?php
/*
 *
 * Copyright 2001-2016 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

// Initialisations files
require_once("../lib/initialisations.inc.php");

// Resume session
//$resultat_session = resumeSession();
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

$sql="SELECT 1=1 FROM droits WHERE id='/mod_orientation/index.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_orientation/index.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Accueil orientation',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

// Check access
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

$sql="CREATE TABLE IF NOT EXISTS o_orientations (
id int(11) NOT NULL AUTO_INCREMENT,
login varchar(50) NOT NULL,
id_orientation int(11) NOT NULL,
rang int(3) NOT NULL,
commentaire text NOT NULL,
date datetime NOT NULL,
PRIMARY KEY (id)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$resultat_creation_table=mysqli_query($GLOBALS["mysqli"], $sql);

$sql="CREATE TABLE IF NOT EXISTS o_orientations_base (
id int(11) NOT NULL AUTO_INCREMENT,
titre varchar(255) NOT NULL,
description text NOT NULL,
PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$resultat_creation_table=mysqli_query($GLOBALS["mysqli"], $sql);

$sql="CREATE TABLE IF NOT EXISTS o_orientations_mefs (
id int(11) NOT NULL AUTO_INCREMENT,
id_orientation int(11) NOT NULL,
mef_code varchar(50) NOT NULL,
PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$resultat_creation_table=mysqli_query($GLOBALS["mysqli"], $sql);

$sql="CREATE TABLE IF NOT EXISTS o_voeux (
id int(11) NOT NULL AUTO_INCREMENT,
login varchar(50) NOT NULL,
id_orientation int(11) NOT NULL,
rang int(3) NOT NULL,
date datetime NOT NULL,
saisi_par varchar(50) NOT NULL,
PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$resultat_creation_table=mysqli_query($GLOBALS["mysqli"], $sql);

$msg="";

$javascript_specifique[] = "lib/tablekit";
$utilisation_tablekit="ok";
//================================
$titre_page = "Orientation";
require_once("../lib/header.inc.php");
//================================

//debug_var();

echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>

<h2>Accueil Orientation</h2>

<p>Ce module est destiné à saisir les voeux et orientations proposées par le conseil de classe.</p>
<ul>";

if($_SESSION['statut']=='administrateur') {
	echo "
	<li><a href='admin.php'>Administrer le module</a></li>
	<li><a href='saisie_types_orientation.php'>Saisir les types d'orientation</a></li>";
}
elseif((($_SESSION['statut']=='scolarite')&&(getSettingAOui('OrientationSaisieTypeScolarite')))||
(($_SESSION['statut']=='cpe')&&(getSettingAOui('OrientationSaisieTypeCpe')))||
(($_SESSION['statut']=='professeur')&&(getSettingAOui('OrientationSaisieTypePP'))&&(is_pp($_SESSION['login'])))) {
	echo "
	<li><a href='saisie_types_orientation.php'>Saisir les types d'orientation</a></li>";
}

if(($_SESSION['statut']=='administrateur')||
(($_SESSION['statut']=='scolarite')&&(getSettingAOui('OrientationSaisieVoeuxScolarite')))||
(($_SESSION['statut']=='cpe')&&(getSettingAOui('OrientationSaisieVoeuxCpe')))||
(($_SESSION['statut']=='professeur')&&(getSettingAOui('OrientationSaisieVoeuxPP'))&&(is_pp($_SESSION['login'])))) {
	echo "
	<li><a href='saisie_voeux.php'>Saisir les voeux des élèves</a></li>";
}

if(($_SESSION['statut']=='administrateur')||
(($_SESSION['statut']=='scolarite')&&(getSettingAOui('OrientationSaisieOrientationScolarite')))||
(($_SESSION['statut']=='cpe')&&(getSettingAOui('OrientationSaisieOrientationCpe')))||
(($_SESSION['statut']=='professeur')&&(getSettingAOui('OrientationSaisieOrientationPP'))&&(is_pp($_SESSION['login'])))) {
	echo "
	<li><a href='saisie_orientation.php'>Saisir les orientations proposées par le conseil de classe</a></li>";
}

echo "
	<li><a href='consulter_orientation.php'>Consulter les voeux et orientations proposées par le conseil de classe</a></li>";

echo "
	<!--
	<li></li>
	-->
</ul>

<p style='color:red;margin-top:1em;'><em>A FAIRE&nbsp;:</em></p>
<ul>
	<li>Pouvoir ne faire apparaitre que le 1er voeu/orientation sur le bulletin.</li>
	<li>Permettre la saisie des voeux en parent/élève.</li>
	<li>Permettre de produire un PDF des voeux formulés, des orientations proposées.</li>
</ul>

<p><br /></p>\n";

// Pouvoir demander l'ajout de tel type orientation?

require("../lib/footer.inc.php");
?>
