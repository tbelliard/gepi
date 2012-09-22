<?php

/*
 * $Id$
 *
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
};

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
die();
}

// Page bourrinée... la gestion du token n'est pas faite... et ne sera faite que si quelqu'un utilise encore ce mode d'initialisation et le manifeste sur la liste de diffusion gepi-users
check_token();

if (!function_exists("dbase_open"))  {
    $msg = "ATTENTION : PHP n'est pas configuré pour gérer les fichiers GEP (dbf). L'extension  d_base n'est pas active. Adressez-vous à l'administrateur du serveur pour corriger le problème.";
}

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
?>
<p class=bold>|<a href="../gestion/index.php">Retour</a>|</p>

<p><strong>Vous allez effectuer l'initialisation de l'année scolaire qui vient de débuter.</strong><br />
(<em>c'est une opération que vous ne devez effectuer qu'<span style='color:red'>une seule fois par an</span>.<br />
<?php

	if(getSettingValue("import_maj_xml_sconet")==1) {
		echo "Pour mettre à jour la base avec les informations saisies en cours d'année dans Sconet pour les changements d'adresses, arrivées d'èlèves,...<br />il faut effectuer une <a href='../responsables/maj_import.php'>Mise à jour d'après Sconet</a></em>)<br />";
	}
	else {
		echo "L'initialisation d'année ne convient pas pour prendre en compte les changements d'adresses, arrivées d'èlèves,...</em>)<br />";
	}
?>
<br />
<ul>
<li><p>Au cours de la procédure, le cas échéant, certaines données de l'année passée seront définitivement effacées de la base GEPI (élèves, notes, appréciations, ...) . Seules seront conservées les données suivantes :<br /><br />
- les données relatives aux établissements,<br />
- les données relatives aux classes : intitulés courts, intitulés longs, nombre de périodes et noms des périodes,<br />
- les données relatives aux matières : identifiants et intitulés complets,<br />
- les données relatives aux utilisateurs (professeurs, administrateurs, ...). Concernant les professeurs, les matières enseignées par les professeurs sont conservées,<br />
- Les données relatives aux différents types d'AID.<br />&nbsp;</p></li>

<li>
	<?php
	//==================================
	// RNE de l'établissement pour comparer avec le RNE de l'établissement de l'année précédente
	$gepiSchoolRne=getSettingValue("gepiSchoolRne") ? getSettingValue("gepiSchoolRne") : "";
	//==================================
	if($gepiSchoolRne=="") {
		echo "<p><b style='color:red;'>Attention</b>: Le RNE de l'établissement n'est pas renseigné dans 'Gestion générale/<a href='../gestion/param_gen.php' target='_blank'>Configuration générale</a>'<br />Cela peut perturber l'import de l'établissement d'origine des élèves.<br />Vous devriez corriger avant de poursuivre.</p>\n";
	}
	?>

	<p>L'initialisation s'effectue en quatre phases, chacune nécessitant un fichier GEP particulier :</p>
    <ul>
    <li><p><a href='step1.php'>Procéder à la première phase</a> d'importation des élèves,  de constitution des classes et d'affectation des élèves dans les classes : le fichier <b>F_ELE.DBF</b> est requis.<br />&nbsp;</p></li>
    <li><p><a href='responsables.php'>Procéder à la deuxième phase</a> d'importation des responsables des élèves : le fichier <b>F_ERE.DBF</b> est requis.<br />&nbsp;</p></li>
    <li><p><a href='disciplines.php'>Procéder à la troisième phase</a> d'importation des matières : le fichier <b>F_tmt.dbf</b> est requis.<br />&nbsp;</p></li>
    <li><p><a href='professeurs.php'>Procéder à la quatrième phase</a> d'importation des professeurs : le fichier <b>F_wind.dbf</b> est requis.<br />&nbsp;</p></li>
    <li><p><a href='prof_disc_classe.php'>Procéder à la cinquième phase</a> d'affectation des matières à chaque professeur, d'affectation des professeurs dans chaque classe  et de définition des options suivies par les élèves : les fichiers <b>F_men.dbf</b> et <b>F_gpd.dbf</b> sont requis.<br />&nbsp;</p></li>
    <li><p><a href='clean_tables.php'>Procéder à la sixième phase</a> de nettoyage des données : les données inutiles importées à partir des fichiers GEP lors des différentes phases d'initialisation seront effacées !<br />&nbsp;</p></li>
    </ul>
</li>
<li><p>Une fois toute la procédure d'initialisation des données terminée, il vous sera possible d'effectuer toutes les modifications nécessaires au cas par cas par le biais des outils de gestion inclus dans <b>GEPI</b>.<br />&nbsp;</p></li>
</ul>
<?php
require("../lib/footer.inc.php");
?>
