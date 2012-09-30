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
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année pour les serveurs LCS";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
?>
<p class='bold'><a href="../gestion/index.php#init_lcs"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>
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
	echo "<p>Avez-vous pensé à effectuer les différentes opérations de fin d'année et préparation de nouvelle année à la page <a href='../gestion/changement_d_annee.php' style='font-weight:bold;'>Changement d'année</a>&nbsp?</p>\n";
?>
<ul>
<li>Au cours de la procédure, le cas échéant, certaines données de l'année passée seront définitivement effacées de la base GEPI (<em>élèves, notes, appréciations,...</em>). Seules seront conservées les données suivantes :<br /><br />
- les données relatives aux établissements,<br />
- les données relatives aux classes : intitulés courts, intitulés longs, nombre de périodes et noms des périodes,<br />
- les données relatives aux matières : identifiants et intitulés complets,<br />
- les données relatives aux utilisateurs (<em>professeurs, administrateurs,...</em>). Concernant les professeurs, les matières enseignées par les professeurs sont conservées,<br />
- Les données relatives aux différents types d'AID.</li><br />

<li>L'initialisation s'effectue en différentes phases :<br />
    <ul>
    <br />
    <li><a href='eleves.php'>Procéder à la première phase</a> d'importation des élèves, de constitution des classes et d'affectation des élèves dans les classes.</li>
    <br />
    <li><a href='professeurs.php'>Procéder à la deuxième phase</a> d'importation des professeurs.</li>
    <br />
    <li><a href='disciplines.php'>Procéder à la troisième phase</a> d'importation des matières et d'affectation de ce matières aux professeurs.</li>
    <br />
    <li><a href='affectations.php'>Procéder à la quatrième phase</a> d'affectation des matières et des professeurs aux classes.</li>
    <br />
    <br />
</li>
<li>Une fois toute la procédure d'initialisation des données terminée, il vous sera possible d'effectuer toutes les modifications nécessaires au cas par cas par le biais des outils de gestion inclus dans <b>GEPI</b>.</li>
</ul>
<?php require("../lib/footer.inc.php");?>
