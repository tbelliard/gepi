<?php

/*
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
<p class=bold><a href="../gestion/index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>

<p>Vous allez effectuer l'initialisation de l'année scolaire qui vient de débuter.</p>
<ul>
<li>
	<p>Au cours de la procédure, le cas échéant, certaines données de l'année passée seront définitivement effacées de la base GEPI (élèves, notes, appréciations, ...).<br />
	Seules seront conservées les données suivantes :<br /></p>
	<ul>
		<li><p>les données relatives aux établissements,</p></li>
		<li><p>les données relatives aux classes : intitulés courts, intitulés longs, nombre de périodes et noms des périodes,</p></li>
		<li><p>les données relatives aux matières : identifiants et intitulés complets,</p></li>
		<li><p>les données relatives aux utilisateurs (professeurs, administrateurs, ...). Concernant les professeurs, les matières enseignées par les professeurs sont conservées,</p></li>
		<li><p>Les données relatives aux différents types d'AID.</p></li>
	</ul>
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

	<p>L'initialisation s'effectue en quatre phases, chacune nécessitant un fichier GEP particulier <b>ou des CSV</b>:</p>
	<ul>
		<li><p>Vous devez disposer des fichiers F_ELE.DBF et F_ERE.DBF générés par l'AutoSco.<br />
		Générer le F_ELE.CSV correspondant au F_ELE.DBF depuis Sconet est assez facile sauf pour l'ERENO qui n'est pas récupéré et du coup générer un F_ERE.CSV n'est pas commode.<br />Il faut en effet fixer arbitrairement un ERENO pour faire le lien entre parents et enfants et ne récupérer que les entrées souhaitées de Sconet pour les parents (<i>on récupère là plus de deux lignes par élève...</i>)...<br />Bref, j'ai laissé en plan.</p></li>
		<li><p>Vous pouvez générer les fichiers F_TMT.CSV, F_MEN.CSV et F_GPD.CSV à l'aide de l'export XML de STS une fois l'emploi du temps remonté.</p>
		<p>Vous pouvez également compléter partiellement le F_WIND.CSV de cette façon.<br />
		Partiellement parce que certains champs ne sont pas récupérés:</p>
		<ul>
			<!--li>le NUMEN (INDNNI) utilisé comme mot de passe par défaut par GEPI n'est pas récupéré.<br />
			Vous devrez compléter les champs mots-de-passe (<i>ou accepter les mots de passe aléatoires proposés (les noter/imprimer pour transmettre aux collègues)</i>).</li-->
			<li>le NUMEN (INDNNI) utilisé comme mot de passe par défaut par GEPI n'est pas récupéré.<br />
			Il est alors proposé de définir un mot de passe aléatoire ou d'utiliser la date de naissance à la place.</li>
			<li>La civilité n'est pas récupérée non plus (<i>mais il est assez facile de la compléter</i>).</li>
			<li>Enfin, le champ FONCCO n'est pas rempli non plus (<i>mais c'est en principe 'ENS' pour tous les enseignants</i>).</li>
		</ul>
		<p><a href='lecture_xml_sts_emp.php'>Générer les fichiers CSV à partir de l'export XML de STS</a>.</p></li>
	</ul>
</li>
<li>
	<p>Pour procéder aux importations:</p>
	<ul>
		<li><p><a href='step1.php'>Procéder à la première phase</a> d'importation des élèves,  de constitution des classes et d'affectation des élèves dans les classes : le fichier <b>F_ELE.DBF</b> est requis.</p></li>
		<li><p><a href='responsables.php'>Procéder à la deuxième phase</a> d'importation des responsables des élèves : le fichier <b>F_ERE.DBF</b> est requis.</p></li>
		<li><p><a href='disciplines_csv.php'>Procéder à la troisième phase</a> d'importation des matières : le fichier <b>F_tmt.csv</b> est requis.</p></li>
		<li><p><a href='prof_csv.php'>Procéder à la quatrième phase</a> d'importation des professeurs : le fichier <b>F_wind.csv</b> est requis.</p></li>
		<li><p><a href='prof_disc_classe_csv.php'>Procéder à la cinquième phase</a> d'affectation des matières à chaque professeur, d'affectation des professeurs dans chaque classe  et de définition des options suivies par les élèves : les fichiers <b>F_men.csv</b> et <b>F_gpd.csv</b> sont requis.</p></li>

		<li><p><a href='init_pp.php'>Procéder à la sixième phase</a>: Initialisation des professeurs principaux.</p></li>

		<li><p><a href='clean_tables.php'>Procéder à la septième phase</a> de nettoyage des données : les données inutiles importées à partir des fichiers GEP lors des différentes phases d'initialisation seront effacées !</p></li>

	</ul>
</li>
<li>
	<p>Une fois toute la procédure d'initialisation des données terminée, il vous sera possible d'effectuer toutes les modifications nécessaires au cas par cas par le biais des outils de gestion inclus dans <b>GEPI</b>.</p>
</li>
</ul>
<?php require("../lib/footer.inc.php");?>