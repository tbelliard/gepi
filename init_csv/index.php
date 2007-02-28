<?php
/*
 * Last modification  : 15/09/2006
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
};


if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
die();
}

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<p class=bold><a href="../gestion/index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>

<p>Vous allez effectuer l'initialisation de l'année scolaire qui vient de débuter.</p>
<ul>
<li>Au cours de la procédure, le cas échéant, certaines données de l'année passée seront définitivement effacées de la base GEPI (élèves, notes, appréciations, ...) . Seules seront conservées les données suivantes, qui seront seulement mises à jour si nécessaire :<br /><br />
- les données relatives aux établissements,<br />
- les données relatives aux classes : intitulés courts, intitulés longs, nombre de périodes et noms des périodes,<br />
- les données relatives aux matières : identifiants et intitulés complets,<br />
- les données relatives aux utilisateurs (professeurs, administrateurs, ...). Concernant les professeurs, les matières enseignées par les professeurs sont conservées,<br />
- Les données relatives aux différents types d'AID.</li><br />

<li>L'initialisation s'effectue en plusieurs phases successives, chacune nécessitant un fichier CSV spécifique, que vous devrez fournir au bon format :<br />
    <ul>
    <br />
    <li><a href='eleves.php'>Procéder à la première phase</a> d'importation des élèves. <b>g_eleves.csv</b> est requis.
    	<br/>Il doit contenir, dans l'ordre les champs suivants :
    	<br/>Nom ; Prénom ; Date de naissance ; n° identifiant interne (étab) ; n° identifiant national ; Code établissement précédent ; Doublement (OUI | NON) ; Régime (INTERN | EXTERN | IN.EX. | DP DAN) ; Sexe (F ou M)</li>
    <br />
    
    <li><a href='responsables.php'>Procéder à la deuxième phase</a> d'importation des responsables des élèves : le fichier <b>g_responsables.csv</b> est requis.
    	<br/>Il doit contenir, dans l'ordre, les champs suivants :
    	<br/>n° d'identifiant élève interne à l'établissement ; Nom du responsable ; Prénom du responsable ; Civilité ;  Ligne 1 Adresse ; Ligne 2 Adresse ; Code postal ; Commune</li>
    <br />
    
    <li><a href='disciplines.php'>Procéder à la troisième phase</a> d'importation des matières : le fichier <b>g_disciplines.csv</b> est requis.
    	<br/>Il doit contenir, dans l'ordre, les champs suivants :
    	<br/>Nom court matière ; Nom long matière</li>
    <br />
    
    <li><a href='professeurs.php'>Procéder à la quatrième phase</a> d'importation des professeurs : le fichier <b>g_professeurs.csv</b> est requis.
    	<br/>Il doit contenir, dans l'ordre, les champs suivants :
    	<br/>Nom ; Prénom ; Civilité ; Adresse e-mail</li>
    <br />
    
    <li><a href='eleves_classes.php'>Procéder à la cinquième phase</a> d'affectation des élèves aux classes  : le fichier <b>g_eleves_classes.csv</b> requis.
    	<br/>Il doit contenir, dans l'ordre, les champs suivants :
    	<br/>n° d'identifiant élève interne à l'établissement ; Identifiant court de la classe
    	<br/>Remarque : cette opération créé automatiquement les classes dans Gepi, mais ne leur attribue qu'un nom court (identifiant). Vous devrez ajouter le nom long par l'interface de gestion des classes.</li>
    <br />

    
    <li><a href='prof_disc_classes.php'>Procéder à la sixième phase</a> d'affectation des matières à chaque professeur et d'affectation des professeurs dans chaque classe : le fichier <b>g_prof_disc_classes.csv</b> requis. Cette importation va définir les compétences des professeurs et créer les groupes d'enseignement dans chaque classe.
    	<br />Il doit contenir, dans l'ordre, les champs suivants :
    	<br />Login du professeur ; Nom court de la matière ; Le ou les identifiants de classe (séparés par des !) ; Le type de cours (CG (= cours général) | OPT (= option))
    	<br />Remarques :
    	<br />Si le dernier champ est vide et qu'une seule classe est présente dans le troisième champ, le type sera défini comme "général". S'il est vide et que plusieurs classes ont été définies, alors le type sera défini comme "option".
    	<br />Lorsque l'enseignement est général, tous les élèves de la classe sont automatiquement associés à cet enseignement.
    	<br />Lorsque l'enseignement est une option, aucun élève n'y est associé, l'association se faisant à la septième étape.
    	<br />Attention ! Ne mettez plusieurs classes pour une même matière que s'il s'agit d'un seul enseignement ! Si un professeur enseigne la même matière dans deux classes différentes, il faut alors deux lignes distinctes dans le fichier CSV, avec une seule classe définie pour chaque ligne.</li>
    <br />
    
    <li><a href='eleves_options.php'>Procéder à la septième phase</a> d'affectation des élèves à chaque groupe d'option : le fichier <b>g_eleves_options.csv</b> est requis.
    	<br/>Il doit contenir, dans l'ordre, les champs suivants :
    	<br/>n° d'identifiant élève interne à l'établissement ; Identifiants des matières suivies en option, séparés par des !
    	<br/>Remarque : si plusieurs groupes avec la même matière sont trouvés dans la classe de l'élève, alors l'élève sera associé à tous ces différents groupes.</li>
    <br />
    </ul>
    <br />
</li>
<li>Une fois toute la procédure d'initialisation des données terminée, il vous sera possible d'effectuer toutes les modifications nécessaires au cas par cas par le biais des outils de gestion inclus dans <b>GEPI</b>.</li>
</ul>
<?php require("../lib/footer.inc.php");?>