<?php

/*
 * $Id$
 *
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
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
	die();
}

/*
if (!function_exists("dbase_open"))  {
    $msg = "ATTENTION : PHP n'est pas configuré pour gérer les fichiers GEP (dbf). L'extension  d_base n'est pas active. Adressez-vous à l'administrateur du serveur pour corriger le problème.";
}
*/

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<p class='bold'><a href="../gestion/index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>

<p>Vous allez effectuer l'initialisation de l'année scolaire qui vient de débuter.<br />
<?php
	$sql="SELECT 1=1 FROM matieres_notes LIMIT 1;";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0) {
		echo "Avez-vous pensé à <a href='#' onmouseover=\"afficher_div('archivage','y',20,20);\" onclick=\"return false;\">archiver</a> l'année qui se termine ?</p>\n";
		$texte="<p>L'archivage de l'année en cours vous permettra, une fois passé à l'année suivante, de consulter les bulletins antérieurs de chacun de vos élèves, pour peu qu'ils aient été scolarisés dans votre établissement.</p><p>Cela nécessite l'activation du <a href='../mod_annees_anterieures/admin.php'>module 'Années antérieures'</a>.</p>";
		$tabdiv_infobulle[]=creer_div_infobulle('archivage',"Archivage d'une année","",$texte,"",30,0,'y','y','n','n');
	}
?>
<ul>
	<li>
		<p>Au cours de la procédure, le cas échéant, certaines données de l'année passée seront définitivement effacées de la base GEPI (<i>élèves, notes, appréciations, ...</i>).<br />
		Seules seront conservées les données suivantes :<br /></p>
		<ul>
			<li><p>les données relatives aux établissements,</p></li>
			<li><p>les données relatives aux classes : intitulés courts, intitulés longs, nombre de périodes et noms des périodes,</p></li>
			<li><p>les données relatives aux matières : identifiants et intitulés complets,</p></li>
			<li><p>les données relatives aux utilisateurs (<i>professeurs, administrateurs, ...</i>). Concernant les professeurs, les matières enseignées par les professeurs sont conservées,</p></li>
			<li><p>Les données relatives aux différents types d'AID.</p></li>
		</ul>
	</li>
	<li>
		<p>Pour procéder aux importations, quatre fichiers sont requis:</p>
		<p>Les trois premiers, 'ElevesAvecAdresses.xml', 'Nomenclature.xml', 'ResponsablesAvecAdresses.xml', doivent être récupérés depuis l'application web Sconet.<br />
		Demandez gentiment à votre secrétaire de se rendre dans 'Sconet/Accès Base élèves mode normal/Exploitation/Exports standard/Exports XML génériques' pour récupérer les fichiers 'ElevesAvecAdresses.xml', 'Nomenclature.xml' et 'ResponsablesAvecAdresses.xml'.</p>
		<p>Le dernier, 'sts_emp_RNE_ANNEE.xml', doit être récupéré depuis l'application STS/web.<br />
		Demandez gentiment à votre secrétaire d'accéder à STS-web et d'effectuer le parcours suivant: 'Mise à jour/Exports/Emplois du temps'</p>

		<?php
		//==================================
		// RNE de l'établissement pour comparer avec le RNE de l'établissement de l'année précédente
		$gepiSchoolRne=getSettingValue("gepiSchoolRne") ? getSettingValue("gepiSchoolRne") : "";
		//==================================
		if($gepiSchoolRne=="") {
			echo "<p><b style='color:red;'>Attention</b>: Le RNE de l'établissement n'est pas renseigné dans 'Gestion générale/<a href='../gestion/param_gen.php' target='_blank'>Configuration générale</a>'<br />Cela peut perturber l'import de l'établissement d'origine des élèves.<br />Vous devriez corriger avant de poursuivre.</p>\n";
		}
		?>

		<ul>
			<li>
				<p><a href='step1.php'>Procéder à la première phase</a> d'importation des élèves, de constitution des classes et d'affectation des élèves dans les classes : le fichier <b>ElevesAvecAdresses.xml</b> (<i>ou ElevesSansAdresses.xml</i>) et le fichier <b>Nomenclature.xml</b> sont requis.<br />
				Le deuxième fichier sert à identifier les noms courts des options des élèves (<i>le premier fichier ne contient que les codes numériques de ces options</i>).</p>
			</li>
			<li>
				<p><a href='responsables.php'>Procéder à la deuxième phase</a> d'importation des responsables des élèves : le fichier <b>ResponsablesAvecAdresses.xml</b> est requis.</p>
			</li>
			<li>
				<p><a href='disciplines_csv.php'>Procéder à la troisième phase</a> d'importation des matières : le fichier <b>sts_emp_RNE_ANNEE.xml</b> est requis.</p>
			</li>
			<li>
				<p><a href='professeurs.php'>Procéder à la quatrième phase</a> d'importation des professeurs.<br />
				Le fichier <b>sts_emp_RNE_ANNEE.xml</b> doit avoir été fourni à l'étape précédente pour pouvoir être à nouveau lu lors de cette étape.</p>
			</li>
			<li>
				<p><a href='prof_disc_classe_csv.php'>Procéder à la cinquième phase</a> d'affectation des matières à chaque professeur, d'affectation des professeurs dans chaque classe  et de définition des options suivies par les élèves.<br />
				Le fichier <b>sts_emp_RNE_ANNEE.xml</b> doit avoir été fourni deux étapes auparavant pour pouvoir être à nouveau lu lors de cette étape.</p>
			</li>
			<li>
				<p><a href='init_pp.php'>Procéder à la sixième phase</a>: Initialisation des professeurs principaux.</p>
			</li>
			<li>
				<p><a href='clean_tables.php'>Procéder à la septième phase</a> de nettoyage des données : les données inutiles importées à partir des fichiers GEP lors des différentes phases d'initialisation seront effacées !</p>
			</li>
			<li>
				<p><a href='clean_temp.php'>Procéder à la phase de nettoyage des fichiers</a>: Supprimer les fichiers XML et CSV qui n'auraient pas été supprimés auparavant.</p>
			</li>
		</ul>
	</li>
	<li>
		<p>Une fois toute la procédure d'initialisation des données terminée, il vous sera possible d'effectuer toutes les modifications nécessaires au cas par cas par le biais des outils de gestion inclus dans <b>GEPI</b>.</p>
	</li>
</ul>
<?php require("../lib/footer.inc.php");?>