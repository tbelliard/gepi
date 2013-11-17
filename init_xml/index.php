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
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
?>
<p class="bold"><a href="../gestion/index.php#init_xml"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>

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
<?php

	if((getSettingValue('use_sso')=="lcs")||(getSettingValue('use_sso')=="ldap_scribe")) {
		echo "<p style='color:red;'><b>ATTENTION&nbsp;:</b> Vous utilisez un serveur LCS ou SCRIBE.<br />
		Il existe un mode d'initialisation de l'année propre à <a href='../init_lcs/index.php'>LCS</a> d'une part et à SCRIBE d'autre part (<i><a href='../init_scribe/index.php'>Scribe</a> et <a href='../init_scribe_ng/index.php'>Scribe_ng</a></i>).<br />
		Si vous initialisez l'année avec le mode XML, vous ne pourrez pas utiliser les comptes de votre serveur LCS/SCRIBE par la suite pour accéder à GEPI.<br />Réfléchissez-y à deux fois avant de poursuivre.</p>\n";
		echo "<br />\n";
	}

	echo "<p>Avez-vous pensé à effectuer les différentes opérations de fin d'année et préparation de nouvelle année à la page <a href='../gestion/changement_d_annee.php' style='font-weight:bold;'>Changement d'année</a>&nbsp?</p>\n";

	$sql="CREATE TABLE IF NOT EXISTS ldap_bx (
		id INT( 11 ) NOT NULL AUTO_INCREMENT ,
		login_u VARCHAR( 200 ) NOT NULL ,
		nom_u VARCHAR( 200 ) NOT NULL ,
		prenom_u VARCHAR( 200 ) NOT NULL ,
		statut_u VARCHAR( 50 ) NOT NULL ,
		identite_u VARCHAR( 50 ) NOT NULL ,
		PRIMARY KEY ( id )
		) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$create_table=mysqli_query($GLOBALS["___mysqli_ston"], $sql);

?>
</p>
<ul>
<li>
	<p>Au cours de la procédure, le cas échéant, certaines données de l'année passée seront définitivement effacées de la base GEPI (<em>élèves, notes, appréciations,...</em>).<br />
	Seules seront conservées les données suivantes&nbsp;:<br /></p>
	<ul>
		<li><p>les données relatives aux établissements,</p></li>
		<li><p>les données relatives aux classes : intitulés courts, intitulés longs, nombre de périodes et noms des périodes,</p></li>
		<li><p>les données relatives aux matières : identifiants et intitulés complets,</p></li>
		<li><p>les données relatives aux utilisateurs (professeurs, administrateurs, ...). Concernant les professeurs, les matières enseignées par les professeurs sont conservées,</p></li>
		<li><p>Les données relatives aux différents types d'AID.</p></li>
	</ul>
</li>
<li>
	<p>Professeurs, matières,...&nbsp;: <a href='lecture_xml_sts_emp.php'>Générer les fichiers CSV à partir de l'export XML de STS</a>.</p>
	<p>Elèves&nbsp;: <a href='lecture_xml_sconet.php'>Générer les fichiers CSV à partir des exports XML de Sconet</a>.</p>
</li>
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

	<p>Pour procéder aux importations:</p>
	<ul>
		<li><p><a href='step1.php'>Procéder à la première phase</a> d'importation des élèves,  de constitution des classes et d'affectation des élèves dans les classes : le fichier <b>ELEVES.CSV</b> est requis.</p></li>
		<li><p><a href='responsables.php'>Procéder à la deuxième phase</a> d'importation des responsables des élèves : les fichiers <b>PERSONNES.CSV</b>, <b>RESPONSABLES.CSV</b> et <b>ADRESSES.CSV</b> sont requis.</p></li>
		<li><p><a href='disciplines_csv.php'>Procéder à la troisième phase</a> d'importation des matières : le fichier <b>F_tmt.csv</b> est requis.</p></li>
		<li><p><a href='prof_csv.php?a=a<?php echo add_token_in_url();?>'>Procéder à la quatrième phase</a> d'importation des professeurs : le fichier <b>F_wind.csv</b> est requis.</p></li>
		<li><p><a href='prof_disc_classe_csv.php?a=a<?php echo add_token_in_url();?>'>Procéder à la cinquième phase</a> d'affectation des matières à chaque professeur, d'affectation des professeurs dans chaque classe  et de définition des options suivies par les élèves : les fichiers <b>F_men.csv</b> et <b>F_gpd.csv</b> sont requis.</p></li>

		<li><p><a href='init_pp.php'>Procéder à la sixième phase</a>: Initialisation des professeurs principaux.</p></li>

		<li><p><a href='clean_tables.php?a=a<?php echo add_token_in_url();?>'>Procéder à la septième phase</a> de nettoyage des données : les données inutiles importées à partir des fichiers GEP lors des différentes phases d'initialisation seront effacées&nbsp;!</p></li>

	</ul>
</li>
<li>
	<p>Une fois toute la procédure d'initialisation des données terminée, il vous sera possible d'effectuer toutes les modifications nécessaires au cas par cas par le biais des outils de gestion inclus dans <b>GEPI</b>.</p>
</li>
</ul>
<?php require("../lib/footer.inc.php");?>
