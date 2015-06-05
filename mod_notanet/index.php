<?php
/* $Id$ */
/*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}





//======================================================================================
// Section checkAccess() à décommenter en prenant soin d'ajouter le droit correspondant:
// INSERT INTO droits VALUES('/mod_notanet/index.php','V','V','F','F','F','F','F','F','Accès à l accueil Notanet','');
// Pour décommenter le passage, il suffit de supprimer le 'slash-etoile' ci-dessus et l'étoile-slash' ci-dessous.
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
//======================================================================================

//==============================================
/* Ajout des droits pour fiches_brevet.php dans la table droits */
$sql="SELECT 1=1 FROM droits WHERE id='/mod_notanet/OOo/fiches_brevet.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_notanet/OOo/fiches_brevet.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Fiches brevet openDocument',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

/* Ajout des droits pour imprime_ooo.php dans la table droits */
$sql="SELECT 1=1 FROM droits WHERE id='/mod_notanet/OOo/imprime_ooo.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_notanet/OOo/imprime_ooo.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Imprime fiches brevet openDocument',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

//==============================================

$sql="SELECT 1=1 FROM droits WHERE id='/mod_notanet/fb_rouen_pdf.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_notanet/fb_rouen_pdf.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Fiches brevet PDF pour Rouen',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

$sql="SELECT 1=1 FROM droits WHERE id='/mod_notanet/fb_montpellier_pdf.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_notanet/fb_montpellier_pdf.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Fiches brevet PDF pour Montpellier',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

$sql="SELECT 1=1 FROM droits WHERE id='/mod_notanet/fb_creteil_pdf.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_notanet/fb_creteil_pdf.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Fiches brevet PDF pour Creteil',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

$sql="SELECT 1=1 FROM droits WHERE id='/mod_notanet/fb_lille_pdf.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_notanet/fb_lille_pdf.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Fiches brevet PDF pour Lille',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}


$sql="SELECT 1=1 FROM droits WHERE id='/mod_notanet/saisie_param.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_notanet/saisie_param.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Fiches brevet: Saisie des paramètres',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

/* Ajout des droits pour saisie_socle_commun.php dans la table droits */
$sql="SELECT 1=1 FROM droits WHERE id='/mod_notanet/saisie_socle_commun.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_notanet/saisie_socle_commun.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Notanet: Saisie socle commun',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

/* Ajout des droits pour saisie_notes.php dans la table droits */
$sql="SELECT 1=1 FROM droits WHERE id='/mod_notanet/saisie_notes.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_notanet/saisie_notes.php',
administrateur='V',
professeur='V',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Notanet: Saisie de notes',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}



if(!isset($msg)) {$msg="";}
//===========================================================
// Modification du type des champs id_mat pour pouvoir dépasser 127
$query=mysqli_query($GLOBALS["mysqli"], "ALTER TABLE notanet CHANGE id_mat id_mat INT( 4 ) NOT NULL;");
if(!$query) {
	$msg.="Erreur lors de la modification du type du champ 'id_mat' de la table 'notanet'.<br />Cela risque de poser problème si vous devez saisir des notes de Langue Vivante Régionale.<br />";
}

$query = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE notanet_corresp CHANGE id_mat id_mat INT( 4 ) NOT NULL;");
if(!$query) {
	$msg.="Erreur lors de la modification du type du champ 'id_mat' de la table 'notanet_corresp'.<br />Cela risque de poser problème si vous devez saisir des notes de Langue Vivante Régionale.<br />";
}

$query = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE notanet_app CHANGE id_mat id_mat INT( 4 ) NOT NULL;");
if(!$query) {
	$msg.="Erreur lors de la modification du type du champ 'id_mat' de la table 'notanet_app'.<br />Cela risque de poser problème si vous devez saisir des notes de Langue Vivante Régionale.<br />";
}
//===========================================================

$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM notanet_corresp LIKE 'mode';"));
if ($test_champ==0) {
	$query = mysqli_query($mysqli, "ALTER TABLE notanet_corresp ADD mode varchar(20) NOT NULL default 'extract_moy';");
if(!$query) {
	$msg.="Erreur lors de l'ajout du champ 'mode' à la table 'notanet_corresp'.<br />";
	}
}

$query = mysqli_query($GLOBALS["mysqli"], "CREATE TABLE IF NOT EXISTS notanet_saisie (login VARCHAR( 50 ) NOT NULL, id_mat INT(4), matiere VARCHAR(50), note VARCHAR(4), PRIMARY KEY ( login )) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
//===========================================================

if((isset($_GET['ouvrir_saisie']))&&
(($_GET['ouvrir_saisie']=='y')||($_GET['ouvrir_saisie']=='n'))&&
(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite'))) {
	check_token();

	if(saveSetting('notanet_saisie_note_ouverte', $_GET['ouvrir_saisie'])) {
		$msg="Modification effectuée.<br />";
	}
	else {
		$msg="Erreur.<br />";
	}
}


//**************** EN-TETE *****************
$titre_page = "Notanet: Accueil";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc.php");
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
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

$sql="CREATE TABLE IF NOT EXISTS notanet_app (
  login varchar(50) NOT NULL,
  id_mat tinyint(4) NOT NULL,
  matiere varchar(50) NOT NULL,
  appreciation text NOT NULL,
  id int(11) NOT NULL auto_increment,
  PRIMARY KEY  (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

$sql="CREATE TABLE IF NOT EXISTS notanet_corresp (
  id int(11) NOT NULL auto_increment,
  type_brevet tinyint(4) NOT NULL,
  id_mat tinyint(4) NOT NULL,
  notanet_mat varchar(255) NOT NULL default '',
  matiere varchar(50) NOT NULL default '',
  statut enum('imposee','optionnelle','non dispensee dans l etablissement') NOT NULL default 'imposee',
  PRIMARY KEY  (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

$sql="CREATE TABLE IF NOT EXISTS notanet_ele_type (
  login varchar(50) NOT NULL,
  type_brevet tinyint(4) NOT NULL,
  PRIMARY KEY  (login)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

$sql="CREATE TABLE IF NOT EXISTS notanet_verrou (
id_classe TINYINT NOT NULL ,
type_brevet TINYINT NOT NULL ,
verrouillage CHAR( 1 ) NOT NULL
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

$sql="CREATE TABLE IF NOT EXISTS notanet_socles (
login VARCHAR( 50 ) NOT NULL ,
b2i ENUM( 'MS', 'ME', 'MN', 'AB', '' ) NOT NULL ,
a2 ENUM( 'MS', 'ME', 'MN', 'AB', '' ) NOT NULL ,
lv VARCHAR( 50 ) NOT NULL ,
PRIMARY KEY ( login )
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

$sql="CREATE TABLE IF NOT EXISTS notanet_avis (
login VARCHAR( 50 ) NOT NULL ,
favorable ENUM( 'O', 'N', '' ) NOT NULL ,
avis TEXT NOT NULL ,
PRIMARY KEY ( login )
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

$sql="CREATE TABLE IF NOT EXISTS notanet_lvr (
id int(11) NOT NULL auto_increment,
intitule VARCHAR( 255 ) NOT NULL ,
PRIMARY KEY ( id )
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

$sql="CREATE TABLE IF NOT EXISTS notanet_lvr_ele (
id int(11) NOT NULL auto_increment,
login VARCHAR( 255 ) NOT NULL ,
id_lvr INT( 11 ) NOT NULL ,
note ENUM ('', 'VA','NV') NOT NULL DEFAULT '',
PRIMARY KEY ( id )
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

$sql="CREATE TABLE IF NOT EXISTS notanet_socle_commun (
id INT(11) NOT NULL auto_increment,
login VARCHAR( 50 ) NOT NULL ,
champ VARCHAR( 10 ) NOT NULL ,
valeur ENUM( 'MS', 'ME', 'MN', 'AB', '' ) NOT NULL ,
PRIMARY KEY ( id )
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$create_table=mysqli_query($GLOBALS["mysqli"], $sql);


if($_SESSION['statut']=="administrateur") {
	$truncate_tables=isset($_GET['truncate_tables']) ? $_GET['truncate_tables'] : NULL;
	if($truncate_tables=='y') {
		check_token();

		echo "<p class='bold'>Nettoyage des tables Notanet&nbsp;:</p>\n";

		$table_a_vider=array('notanet', 'notanet_avis', 'notanet_app', 'notanet_verrou', 'notanet_socles', 'notanet_ele_type', 'notanet_saisie');

		echo "<div style='margin-left:3em;'>\n";
		if(!isset($_GET['confirmer'])) {
			echo "<p>Vous allez vider les tables&nbsp;: \n";
			echo $table_a_vider[0];
			for($i=1;$i<count($table_a_vider);$i++) {
				echo ", ".$table_a_vider[$i];
			}
			echo "</p>\n";
			echo "<p>Cette opération, <strong>irréversible</strong>, ne devrait être effectuée que pour éliminer des scories éventuelles des saisies et extractions de l'année précédente.</p>\n";
			echo "<p><a href='".$_SERVER['PHP_SELF']."?truncate_tables=y&amp;confirmer=y".add_token_in_url()."' onclick=\"return confirm('Vous allez vider les tables notanet et perdre les associations élèves/brevets, extractions, appréciations notanet et avis notanet. Etes-vous sûr?')\">Confirmer le nettoyage des tables Notanet</a>.</p>\n";
		}
		else {
			$msg="";
			for($i=0;$i<count($table_a_vider);$i++) {
				$sql="TRUNCATE TABLE $table_a_vider[$i];";
				$del=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$del) {
					$msg.="<span style='color:red'>Erreur lors du nettoyage de la table '$table_a_vider[$i]'</span><br />\n";
				}
			}
			if($msg=='') {echo "<p style='margin-left: 3em;'>Nettoyage effectué.</p>\n";} else {echo $msg;}
		}
		echo "<p><br /></p>\n";
		echo "</div>\n";
	}
}

echo "<p>Voulez-vous: ";
//echo "<br />\n";
echo "</p>\n";
//echo "<ul>\n";
if($_SESSION['statut']=="administrateur") {
	echo "<ol>\n";
	echo "<li><a href='saisie_param.php'>Saisir les paramètres Académie, Session,...</a>.</li>\n";
	echo "<li><a href='select_eleves.php'>Effectuer les associations Elèves/Type de brevet</a></li>\n";

	echo "<li><a href='select_matieres.php'>Effectuer les associations Type de brevet/Matières</a>  (<i>en précisant le statut: imposées et options</i>)</li>\n";

	//echo "<li><a href='saisie_b2i_a2.php'>Saisir les 'notes' B2i et niveau A2 de langue</a> (<i>nécessaire pour réaliser ensuite l'extraction des moyennes</i>)</li>\n";

	// A FAIRE : 20140326 : Mettre un test sur le fait qu'il y a de telles notes à saisir

	if(!getSettingAOui("notanet_saisie_note_ouverte")) {
		echo "<li>La saisie de notes est actuellement fermée.<br />
		<a href='".$_SERVER['PHP_SELF']."?ouvrir_saisie=y".add_token_in_url()."'>Ouvrir les saisies de notes</a>.<br />
		<a href='saisie_notes.php'>Consulter les 'notes' dans les enseignements dont la note n'est pas la moyenne des 3 trimestres (<i>APSA en EPS</i>)</li>\n";
	}
	else {
		echo "<li><a href='saisie_notes.php'>Saisir les 'notes' dans les enseignements dont la note n'est pas la moyenne des 3 trimestres (<i>APSA en EPS</i>)<br />
		<a href='".$_SERVER['PHP_SELF']."?ouvrir_saisie=n".add_token_in_url()."'>Fermer les saisies de notes</a>.<br />
		</li>\n";
	}

	echo "<li><a href='saisie_lvr.php'>Saisir les 'notes' de Langue Vivante Régionale</a> (<i>si un tel enseignement est évalué dans l'établissement</i>)</li>\n";

	echo "<li><a href='extract_moy.php'>Effectuer une extraction des moyennes, affichage et traitement des cas particuliers</a></li>\n";

	echo "<li><a href='corrige_extract_moy.php'>Corriger l'extraction des moyennes</a></li>\n";

	echo "<li><a href='choix_generation_csv.php?extract_mode=tous'>Générer un export Notanet</a> pour tous les élèves de telle(s) ou telle(s) classe(s) ou juste une sélection (cf. select_eleves.php)</li>\n";

	echo "<li><a href='saisie_socle_commun.php'>Saisir ou importer les résultats du Socle commun.</li>\n";

	echo "<li><a href='verrouillage_saisie_app.php'>Verrouiller/déverrouiller la saisie des appréciations pour les fiches brevet</a><br />La saisie n'est possible pour les professeurs que si l'extraction des moyennes a été effectuée.</li>\n";

	echo "<li><a href='saisie_avis.php'>Saisir l'avis du chef d'établissement</a>.</li>\n";

	echo "<li><a href='verif_saisies.php'>Vérifications avant impression</a>.</li>\n";

	echo "<li><p>Générer les fiches brevet selon le modèle de:</p>
	<ul>\n";
	/*
	echo "		<li><a href='poitiers/fiches_brevet.php'>Poitiers</a></li>
		<li><a href='rouen/fiches_brevet.php'>Rouen (<i>version HTML</i>)</a> - <a href='fb_rouen_pdf.php'>version PDF</a></li>
		<li><a href='fb_montpellier_pdf.php'>Montpellier (<i>version PDF</i>)</a></li>
		<li><a href='fb_creteil_pdf.php'>Creteil (<i>version PDF</i>)</a></li>
		<li><a href='fb_lille_pdf.php'>Lille (<i>version PDF</i>)</a></li>\n";

	$gepi_version=getSettingValue('version');
	if(($gepi_version!='1.5.1')&&($gepi_version!='1.5.0')) {  
	*/
		echo "		<li><a href='OOo/imprime_ooo.php'>Modèle au format OpenOffice</a> <a href='https://www.sylogix.org/projects/gepi/wiki/GepiDoc_fbOooCalc'><img src='../images/icons/ico_question.png' alt='aide construction gabarit' title='Aide pour utiliser les gabarits .ods pour éditer les fiches brevets' title='Aide pour utiliser les gabarits .ods pour éditer les fiches brevets' /></a></li>\n";
	//}
	echo "	</ul>
</li>\n";
	//echo "<li><a href='#'>Vider les tables notanet</a></li>\n";
	//echo "<li><a href=''></a></li>\n";
	echo "</ol>\n";

	echo "<p>Il peut arriver que lors de l'import du CSV dans Notanet certains élèves soient en erreur avec un message du genre&nbsp;:<br />
<span style='color:red'>&nbsp;&nbsp;&nbsp;1234567890M Identifiant national inconnu dans la base de données</span><br />
La page suivante peut vous aider à <a href='recherche_ine.php'>identifier rapidement ces élèves</a>.</p>\n";

	echo "<p style='margin-top:1em;'>Au changement d'année: <a href='".$_SERVER['PHP_SELF']."?truncate_tables=y".add_token_in_url()."'>Vider les saisies Notanet antérieures</a>.</p>\n";

	echo "<p style='margin-top:1em;'><b>NOTES:</b> Pour un bon fonctionnement du dispositif, il faut parcourir les points ci-dessus dans l'ordre.<br />
	Voir <a href='https://www.sylogix.org/projects/gepi/wiki/Module_notanet' target='_blank'>https://www.sylogix.org/projects/gepi/wiki/Module_notanet</a></p>\n";
}
elseif($_SESSION['statut']=="scolarite") {
	echo "<ul>\n";
	//echo "<li><a href='saisie_b2i_a2.php'>Saisir les 'notes' B2i et niveau A2 de langue</a> (<i>nécessaire pour réaliser ensuite l'extraction des moyennes</i>)</li>\n";


	// A FAIRE : 20140326 : Mettre un test sur le fait qu'il y a de telles notes à saisir

	if(!getSettingAOui("notanet_saisie_note_ouverte")) {
		echo "<li>La saisie de notes est actuellement fermée.<br />
		<a href='".$_SERVER['PHP_SELF']."?ouvrir_saisie=y".add_token_in_url()."'>Ouvrir les saisies de notes</a>.<br />
		<a href='saisie_notes.php'>Consulter les 'notes' dans les enseignements dont la note n'est pas la moyenne des 3 trimestres (<i>APSA en EPS</i>)</li>\n";
	}
	else {
		echo "<li><a href='saisie_notes.php'>Saisir les 'notes' dans les enseignements dont la note n'est pas la moyenne des 3 trimestres (<i>APSA en EPS</i>)<br />
		<a href='".$_SERVER['PHP_SELF']."?ouvrir_saisie=n".add_token_in_url()."'>Fermer les saisies de notes</a>.<br />
		</li>\n";
	}

	echo "<li><a href='saisie_lvr.php'>Saisir les 'notes' de Langue Vivante Régionale</a> (<i>si un tel enseignement est évalué dans l'établissement</i>)</li>\n";

	echo "<li><a href='saisie_avis.php'>Saisir l'avis du chef d'établissement</a>.</li>\n";

	echo "<li><a href='verif_saisies.php'>Vérifications avant impression</a>.</li>\n";

	if(acces('/mod_notanet/OOo/imprime_ooo.php', 'scolarite')) {
		echo "<li><p>Générer les fiches brevet selon le modèle de:</p>
	<ul>\n";
		echo "		<li><a href='OOo/imprime_ooo.php'>Modèle au format openDocument</a> <a href='https://www.sylogix.org/projects/gepi/wiki/GepiDoc_fbOooCalc'><img src='../images/icons/ico_question.png' alt='aide construction gabarit' title='Aide pour utiliser les gabarits .ods pour éditer les fiches brevets' title='Aide pour utiliser les gabarits .ods pour éditer les fiches brevets' /></a></li>\n";
	//}
	echo "	</ul>
</li>\n";
	}
	echo "</ul>\n";

	echo "<p><b>NOTES:</b> Pour un bon fonctionnement du dispositif, plusieurs opérations doivent auparavant être réalisées en statut administrateur.</p>\n";
}
elseif($_SESSION['statut']=="secours") {
	echo "<ul>\n";

	// Test sur le fait qu'il y a de telles notes à saisir pour le prof connecté
	$sql="SELECT DISTINCT jeg.id_groupe FROM notanet_ele_type net,
				j_eleves_groupes jeg,
				j_groupes_matieres jgm,
				notanet_corresp nc
			WHERE net.login=jeg.login AND
				jeg.id_groupe=jgm.id_groupe AND
				jgm.id_matiere=nc.matiere AND
				nc.mode='saisie';";
	//echo "$sql<br />";
	$res_matiere_notanet=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_matiere_notanet)>0) {
		if(!getSettingAOui("notanet_saisie_note_ouverte")) {
			echo "<li>La saisie de notes est actuellement fermée.<br />
			<a href='saisie_notes.php'>Consulter les 'notes' dans les enseignements dont la note n'est pas la moyenne des 3 trimestres (<i>APSA en EPS</i>)</li>\n";
		}
		else {
			echo "<li><a href='saisie_notes.php'>Saisir les 'notes' dans les enseignements dont la note n'est pas la moyenne des 3 trimestres (<i>APSA en EPS</i>)</li>\n";
		}
	}
	echo "<li><a href='saisie_app.php'>Saisir les appréciations pour les fiches brevet</a></li>\n";
	echo "</ul>\n";
}
else {
	echo "<ul>\n";

	// Test sur le fait qu'il y a de telles notes à saisir pour le prof connecté
	$sql="SELECT DISTINCT jgp.id_groupe FROM notanet_ele_type net,
				j_eleves_groupes jeg,
				j_groupes_professeurs jgp,
				j_groupes_matieres jgm,
				notanet_corresp nc
			WHERE net.login=jeg.login AND
				jeg.id_groupe=jgp.id_groupe AND
				jgp.login='".$_SESSION['login']."' AND
				jeg.id_groupe=jgm.id_groupe AND
				jgm.id_matiere=nc.matiere AND
				nc.mode='saisie';";
	//echo "$sql<br />";
	$res_matiere_notanet=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_matiere_notanet)>0) {
		if(!getSettingAOui("notanet_saisie_note_ouverte")) {
			echo "<li>La saisie de notes est actuellement fermée.<br />
			<a href='saisie_notes.php'>Consulter les 'notes' dans les enseignements dont la note n'est pas la moyenne des 3 trimestres (<i>APSA en EPS</i>)</li>\n";
		}
		else {
			echo "<li><a href='saisie_notes.php'>Saisir les 'notes' dans les enseignements dont la note n'est pas la moyenne des 3 trimestres (<i>APSA en EPS</i>)</li>\n";
		}
	}
	echo "<li><a href='saisie_app.php'>Saisir les appréciations pour les fiches brevet</a></li>\n";
	echo "</ul>\n";
}

//<a href="notes_structure_pdf.php">Test PDF</a>

?>
<?php
require("../lib/footer.inc.php");
?>
