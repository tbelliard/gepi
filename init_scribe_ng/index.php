<?php
/*
 * $Id$
 *
 * Copyright 2001, 2010 Thomas Belliard
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
$titre_page = "Outil d'initialisation de l'année pour l'annuaire LDAP Scribe NG";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
?>
<p class=bold><a href="../gestion/index.php#init_scribe_ng"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>
<p>Vous allez effectuer l'initialisation de l'année scolaire qui vient de débuter, directement depuis l'annuaire LDAP de Scribe NG. Celui-ci doit donc avoir été préalablement alimenté. Cette procédure ne s'effectue qu'une seule fois.</p>

<?php
	echo "<p>Avez-vous pensé à effectuer les différentes opérations de fin d'année et préparation de nouvelle année à la page <a href='../gestion/changement_d_annee.php' style='font-weight:bold;'>Changement d'année</a>&nbsp?</p>\n";
?>

<ul>
<li>Au cours de la procédure, le cas échéant, certaines données de l'année passée seront définitivement effacées de la base GEPI (élèves, notes, appréciations, ...) . Seules seront conservées les données suivantes :<br /><br />
- les données relatives aux établissements,<br />
- les données relatives aux classes : intitulés courts, intitulés longs, nombre de périodes et noms des périodes,<br />
- les données relatives aux matières : identifiants et intitulés complets,<br />
- les données relatives aux utilisateurs personnels dans l'établissement (professeurs, administrateurs, ...). Concernant les professeurs, les matières enseignées par les professeurs sont conservées (et éventuellement complétées en phase 5),<br />
- Les données relatives aux différents types d'AID.</li><br />

<li>L'initialisation s'effectue en différentes phases :<br />
    <ul>
    <br />
    <li><a href='etape1.php'>Procéder à la première phase</a> d'importation des élèves et de cr&eacute;ation des classes.</li>
    <br />
    <li><a href='etape2.php'>Procéder à la deuxième phase</a> de cr&eacute;ation des p&eacute;riodes et d'affectation des &eacute;l&egrave;ves dans les classes.</li>
    <br />
    <li><a href='etape3.php'>Procéder à la troisième phase</a> d'importation des responsables l&eacute;gaux des &eacute;l&egrave;ves.</li>
    <br />
    <li><a href='etape4.php'>Procéder à la quatrième phase</a> d'importation des professeurs.</li>
    <br />
    <li><a href='etape5.php'>Procéder à la cinquième phase</a> d'importation des matières.</li>
    <br />
    <li><a href='etape6.php'>Procéder à la sixième phase</a> d'importation des enseignements.</li>
    <br />
    <li><a href='etape7.php'>Procéder à la septième phase</a> d'importation des personnels non-enseignants.</li>
    <br />
    <br />
</li>
<li>Une fois toute la procédure d'initialisation des données terminée, il vous sera possible d'effectuer toutes les modifications nécessaires au cas par cas par le biais des outils de gestion inclus dans <b>GEPI</b>.</li>
</ul>
<?php require("../lib/footer.inc.php");?>
