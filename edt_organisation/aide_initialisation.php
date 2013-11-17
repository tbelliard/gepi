<?php

/**
 *
 *
 *
 * Copyright 2001, 2010 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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

$titre_page = "Emploi du temps - Aide à l'initialisation";
$affiche_connexion = 'yes';
$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions edt
require_once("./fonctions_edt.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

// Sécurité
// ajout de la ligne suivante dans 'sql/data_gepi.sql' et 'utilitaires/updates/access_rights.inc.php'
// INSERT INTO droits VALUES ('/edt_organisation/aide_initialisation.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F','aide à l\'initialisation', '');
$sql="SELECT 1=1 FROM droits WHERE id='/edt_organisation/aide_initialisation.php';";
$res_test=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
if (mysqli_num_rows($res_test)==0) {
	$sql="INSERT INTO droits VALUES ('/edt_organisation/aide_initialisation.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F','aide à l\'initialisation', '');";
	$res_insert=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}
if ($_SESSION["statut"] != "administrateur") {
	Die('Vous devez demander à votre administrateur l\'autorisation de voir cette page.');
}
// CSS et js particulier à l'EdT
$javascript_specifique = "edt_organisation/script/fonctions_edt";
$style_specifique = "templates/".NameTemplateEDT()."/css/style_edt";

//++++++++++ l'entête de Gepi +++++
require_once("../lib/header.inc.php");
//++++++++++ fin entête +++++++++++
//++++++++++ le menu EdT ++++++++++
require_once("./menu.inc.php");
//++++++++++ fin du menu ++++++++++
?>
<br />
<!-- la page du corps de l'EdT -->

	<div id="lecorps">

<?php
    require_once("./menu.inc.new.php");
$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
    echo ("<div class=\"fenetre\">\n");
    echo("<div class=\"contenu\">
		<div class=\"coingh\"></div>
        <div class=\"coindh\"></div>
        <div class=\"partiecentralehaut\"></div>
        <div class=\"droite\"></div>
        <div class=\"gauche\"></div>
        <div class=\"coingb\"></div>
		<div class=\"coindb\"></div>
		<div class=\"partiecentralebas\"></div>\n");
}        
?>

<h2><strong>Aide à l'initialisation</strong></h2>
<p>Le module d'emplois du temps de GEPI a trois vocations : </p>
<p><strong>Objectif 1 </strong>: proposer les emplois du temps des classes, des salles, des profs et de chaque élève à l'année ou sur des périodes définies.</p>
<p><strong>Objectif 2 </strong>: proposer des outils de recherche de salles libres.</p>
<p><strong>Objectif 3 </strong>: permettre aux enseignants d'utiliser le module d'absences.</p>
<p>Avant de pouvoir utiliser tout ceci, vous devez remplir les emplois du temps des enseignants manuellement ou automatiquement. Pour se faire, nous vous proposons plusieurs scenarii possibles.</p>

<?php
$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
echo "</div>";
echo "</div>";
}
?>

<?php
$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
    echo ("<div class=\"fenetre\">\n");
    echo("<div class=\"contenu\">
		<div class=\"coingh\"></div>
        <div class=\"coindh\"></div>
        <div class=\"partiecentralehaut\"></div>
        <div class=\"droite\"></div>
        <div class=\"gauche\"></div>
        <div class=\"coingb\"></div>
		<div class=\"coindb\"></div>
		<div class=\"partiecentralebas\"></div>\n");
}        
?>
<p><h3><strong>Scenario 1 : Initialisation manuelle des emplois du temps à l'année</strong></h3></p>
<p>C'est le scenario le plus simple et le plus intuitif à mettre en oeuvre. La procédure ci-dessous n'est pas forcément à prendre strictement dans l'ordre proposé mais chaque étape doit être réalisée avec soin.</p>
<p><strong>1. Semaines : </strong>Vous devez définir les semaines alternées s'il y en a dans votre établissement (cas typique Semaine A - Semaine B). Dans la version actuelle, le module d'emploi du temps ne gère pas simultanément une alternance sur plus de deux semaines. Dans le menu, cliquez sur [Création] [Créer/éditer les semaines] </p>
<p><strong>2. Horaires : </strong>Vous devez également définir les jours et les horaires d'ouverture de votre établissement. Dans le menu, cliquez sur [Création] [Définir les horaires d'ouverture] </p>
<p><strong>3. Créneaux : </strong>Vous devez définir les créneaux qui découpent la journée de cours. Pour le module d'emploi du temps, tous les créneaux définis sont par défaut d'une heure (et ce quelque soit l'heure de début et de fin d'un créneau). Par la suite, le module d'emploi du temps vous laisse la possibilité pour la saisie des cours de diviser chaque créneau en deux sous créneaux de 30 minutes. Rien ne vous empêche de définir un créneau de 1h30 mais pour les emplois du temps, ce sera un créneau basique d'une heure. C'est souvent le cas lorsque l'on définit la pause déjeuner qui, dans la plupart des cas, dure 1h30 (12h00 - 13h30). Pour ce cas précis, vous pouvez définir ce créneau Repas de 12h00 à 13h30. Cependant, il faut garder à l'esprit que ce sera un créneau d'une heure qui sera retenu. Dans le menu, cliquez sur [Création] [Définir les créneaux] </p>
<p><strong>4. AID : </strong>Vous devez créer les cours qui ne font pas partie des enseignements définis dans les classes. Ceci est le cas lorsque des groupes sont définis dans certaines disciplines (exemple : SVT Gr1, Techno GrA etc...). C'est aussi le cas lorsque certains enseignants ont des responsabilités tel que les heures de Labo (SVT, Histoire/Géo), les créneaux UNSS et de Coordonnateur pour l'EPS, les ateliers en général qui n'entrent pas dans les enseignements "traditionnels". Pour tous ces cas, il faut définir ce que l'on appelle des AID. A partir de la page d'accueil GEPI, cliquez sur [Gestion des bases] [Gestion des AID] </p>
<p><strong>5. Salles : </strong>Vous devez définir les salles dans lesquelles se déroulent les cours. Dans le menu, cliquez sur [Création] [Créer/éditer les salles] </p>
<p><strong>6. Emplois du temps profs : </strong>Le remplissage des emplois du temps se fait à partir de ceux des enseignants. Le module d'emploi du temps se charge alors de construire les emplois du temps des classes, de chaque élève et des salles. Dans ce scenario, la saisie se fait manuellement à partir de l'emploi du temps de chaque enseignant. Vous pouvez choisir de remplir vous même ces emplois du temps ou déléguer cette tache à chaque enseignant. Cette délégation se fait à partir de [Gestion des Modules] [Emplois du temps]. Pour créer les emplois du temps profs, cliquez sur [Création][Initialisation manuelle].</p>
<?php
$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
echo "</div>";
echo "</div>";
}
?>


<?php
$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
    echo ("<div class=\"fenetre\">\n");
    echo("<div class=\"contenu\">
		<div class=\"coingh\"></div>
        <div class=\"coindh\"></div>
        <div class=\"partiecentralehaut\"></div>
        <div class=\"droite\"></div>
        <div class=\"gauche\"></div>
        <div class=\"coingb\"></div>
		<div class=\"coindb\"></div>
		<div class=\"partiecentralebas\"></div>\n");
}        
?>
<p><h3><strong>Scenario 2 : Initialisation manuelle des emplois du temps par période</strong></h3></p>
<p>Ce scenario est un peu plus complexe à mettre en place que le précédent puisqu'il nécessite la gestion de périodes sur l'année. L'idée ici est de créer des périodes (ce peut être des trimestres, des demi-trimestres, des mois, des semaines ou un mélange de tout cela) dans lesquels on définit les emplois du temps (en fait, on définit les edt dans le première période et on les duplique dans les suivantes). L'avantage du découpage en période est que les emplois du temps peuvent varier d'une période à l'autre. Ceci est très utile si certains enseignements n'existent pas à l'année (enseignements semestriels). On peut également se servir de ce découpage en prévision de semaines spéciales pour lesquelles les emplois du temps sont différents (semaines d'examens blancs, semaines de stages en 3ème). La phase préliminaire est identique à celle du scenario 1 :</p>
<p>1. Vous réalisez les 5 premières étapes vues précédemment (Semaines, Horaires, Créneaux, AID, Salles)</p>
<p><strong>2. Périodes : </strong>Vous allez définir la ou les périodes qui vont découper l'année scolaire. Evitez le chevauchement des périodes qui n'est pas une fonctionnalité prévue dans le module d'emploi du temps. A partir du menu, cliquez sur [Création] [Créer/éditer les périodes].</p>
<p><strong>3. Emplois du temps profs : </strong>Le remplissage des emplois du temps se fait à partir de ceux des enseignants. Le module d'emploi du temps se charge alors de construire les emplois du temps des classes, de chaque élève et des salles. Dans ce scenario, la saisie se fait manuellement à partir de l'emploi du temps de chaque enseignant. Vous pouvez choisir de remplir vous même ces emplois du temps ou déléguer cette tache à chaque enseignant. Cette délégation se fait à partir de [Gestion des Modules] [Emplois du temps]. Pour créer les emplois du temps profs, cliquez sur [Création][Initialisation manuelle]. <strong>Pour pouvoir utiliser le système des périodes et ainsi pouvoir modifier les emplois du temps dans chacune de ces périodes, il faut commencer par saisir les emplois du temps de la première période et les dupliquer via l'interface des périodes en faisant de simples copier/coller.</strong></p>
<?php
$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
echo "</div>";
echo "</div>";
}
?>

<?php
$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
    echo ("<div class=\"fenetre\">\n");
    echo("<div class=\"contenu\">
		<div class=\"coingh\"></div>
        <div class=\"coindh\"></div>
        <div class=\"partiecentralehaut\"></div>
        <div class=\"droite\"></div>
        <div class=\"gauche\"></div>
        <div class=\"coingb\"></div>
		<div class=\"coindb\"></div>
		<div class=\"partiecentralebas\"></div>\n");
}        
?>
<p><h3><strong>Scenario 3 : Initialisation automatique par importation</strong></h3></p>
<p>Ce scenario est différent des deux précédents au moment du remplissage des emplois du temps. Au lieu de faire une saisie manuelle, on importe un fichier contenant tous les emplois du temps. Ce fichier doit être formaté de façon à être reconnu par GEPI. Actuellement, le module d'emploi du temps vous propose trois formats d'importation possible : un simple fichier .csv, un fichier d'export du logiciel UnDeuxTemps, un export de type Charlemagne. La difficulté de cette importation est de taille : il faut faire coincider les enseignements contenus dans GEPI avec ceux contenus dans le fichier importé. D'ailleurs, il n'est pas rare de devoir finir ce travail d'importation manuellement pour quelques enseignements. Pour que cette importation se passe au mieux, le travail préliminaire suivant est indispensable.</p>
<p>1. Vous réalisez dans un premier temps les étapes 1 et 2 du scenario précédent.</p>
<p><strong>2. Importation du fichier : </strong>Cliquez sur [Création] [Initialisation automatique] et suivez les étapes qui correspondent au  type d'import réalisé.</strong></p>
<?php
$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
echo "</div>";
echo "</div>";
}
?>
<?php
// inclusion du footer
require("../lib/footer.inc.php");
?>