<?php
/* $Id$ */
/*
* Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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





//======================================================================================
// Section checkAccess() à décommenter en prenant soin d'ajouter le droit correspondant:
// INSERT INTO droits VALUES('/mod_notanet/index.php','V','V','F','F','F','F','F','F','Accès à l accueil Notanet','');
// Pour décommenter le passage, il suffit de supprimer le 'slash-etoile' ci-dessus et l'étoile-slash' ci-dessous.
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
//======================================================================================


//**************** EN-TETE *****************
$titre_page = "Notanet: Accueil";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

// Bibliothèque pour Notanet et Fiches brevet
//include("lib_brevets.php");

echo "<div class='noprint'>\n";
echo "<p class='bold'><a href='../accueil.php'>Accueil</a>";
echo "</p>\n";
echo "</div>\n";


$sql="CREATE TABLE IF NOT EXISTS notanet (
  login varchar(50) NOT NULL default '',
  ine text NOT NULL,
  id_mat tinyint(4) NOT NULL,
  notanet_mat varchar(255) NOT NULL,
  matiere varchar(50) NOT NULL,
  note varchar(4) NOT NULL default '',
  note_notanet varchar(4) NOT NULL,
  id_classe smallint(6) NOT NULL default '0'
);";
$create_table=mysql_query($sql);

$sql="CREATE TABLE IF NOT EXISTS notanet_app (
  login varchar(50) NOT NULL,
  id_mat tinyint(4) NOT NULL,
  matiere varchar(50) NOT NULL,
  appreciation text NOT NULL,
  id int(11) NOT NULL auto_increment,
  PRIMARY KEY  (id)
);";
$create_table=mysql_query($sql);

$sql="CREATE TABLE IF NOT EXISTS notanet_corresp (
  id int(11) NOT NULL auto_increment,
  type_brevet tinyint(4) NOT NULL,
  id_mat tinyint(4) NOT NULL,
  notanet_mat varchar(255) NOT NULL default '',
  matiere varchar(50) NOT NULL default '',
  statut enum('imposee','optionnelle','non dispensee dans l etablissement') NOT NULL default 'imposee',
  PRIMARY KEY  (id)
);";
$create_table=mysql_query($sql);

$sql="CREATE TABLE IF NOT EXISTS notanet_ele_type (
  login varchar(50) NOT NULL,
  type_brevet tinyint(4) NOT NULL,
  PRIMARY KEY  (login)
);";
$create_table=mysql_query($sql);

$sql="CREATE TABLE IF NOT EXISTS notanet_verrou (
id_classe TINYINT NOT NULL ,
type_brevet TINYINT NOT NULL ,
verrouillage CHAR( 1 ) NOT NULL
);";
$create_table=mysql_query($sql);

$sql="CREATE TABLE IF NOT EXISTS notanet_socles (
login VARCHAR( 50 ) NOT NULL ,
b2i ENUM( 'MS', 'ME', 'MN', 'AB', '' ) NOT NULL ,
a2 ENUM( 'MS', 'ME', 'MN', 'AB', '' ) NOT NULL ,
lv VARCHAR( 50 ) NOT NULL ,
PRIMARY KEY ( login )
);";
$create_table=mysql_query($sql);

$sql="CREATE TABLE IF NOT EXISTS notanet_avis (
login VARCHAR( 50 ) NOT NULL ,
favorable ENUM( 'O', 'N', '' ) NOT NULL ,
avis TEXT NOT NULL ,
PRIMARY KEY ( login )
);";
$create_table=mysql_query($sql);

echo "<p>Voulez-vous: ";
//echo "<br />\n";
echo "</p>\n";
//echo "<ul>\n";
if($_SESSION['statut']=="administrateur") {
	echo "<ol>\n";
	echo "<li><a href='select_eleves.php'>Effectuer les associations Elèves/Type de brevet</a></li>\n";

	echo "<li><a href='select_matieres.php'>Effectuer les associations Type de brevet/Matières</a>  (<i>en précisant le statut: imposées et options</i>)</li>\n";

	echo "<li><a href='saisie_b2i_a2.php'>Saisir les 'notes' B2i et niveau A2 de langue</a> (<i>nécessaire pour réaliser ensuite l'extraction des moyennes</i>)</li>\n";

	echo "<li><a href='extract_moy.php'>Effectuer une extraction des moyennes, affichage et traitement des cas particuliers</a></li>\n";

	echo "<li><a href='corrige_extract_moy.php'>Corriger l'extraction des moyennes</a></li>\n";

	echo "<li><a href='choix_generation_csv.php?extract_mode=tous'>Générer un export Notanet</a> pour tous les élèves de telle(s) ou telle(s) classe(s) ou juste une sélection (cf. select_eleves.php)</li>\n";

	echo "<li><a href='verrouillage_saisie_app.php'>Verrouiller/déverrouiller la saisie des appréciations pour les fiches brevet</a><br />La saisie n'est possible pour les professeurs que si l'extraction des moyennes a été effectuée.</li>\n";

	echo "<li><a href='saisie_avis.php'>Saisir l'avis du chef d'établissement</a>.</li>\n";

	echo "<li><p>Générer les fiches brevet selon le modèle de:</p>
	<ul>
		<li><a href='poitiers/fiches_brevet.php'>Poitiers</a></li>
		<li><a href='rouen/fiches_brevet.php'>Rouen</a></li>
	</ul>
</li>\n";
	//echo "<li><a href='#'>Vider les tables notanet</a></li>\n";
	//echo "<li><a href=''></a></li>\n";
	echo "</ol>\n";

	echo "<p><b>NOTES:</b> Pour un bon fonctionnement du dispositif, il faut parcourir les points ci-dessus dans l'ordre.</p>\n";
}
else {
	echo "<ul>\n";
	echo "<li><a href='saisie_app.php'>Saisir les appréciations pour les fiches brevet</a></li>\n";
	echo "</ul>\n";
}

require("../lib/footer.inc.php");
?>
