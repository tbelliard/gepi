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

$titre_page = "Emploi du temps - Aide à la maintenance";
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
// INSERT INTO droits VALUES ('/edt_organisation/aid_maintenance.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'aide à la maintenance', '');

$sql="SELECT 1=1 FROM droits WHERE id='/edt_organisation/aide_maintenance.php';";
$res_test=mysql_query($sql);
if (mysql_num_rows($res_test)==0) {
	$sql="INSERT INTO droits VALUES ('/edt_organisation/aide_maintenance.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'aide à la maintenance edt', '');";
	$res_insert=mysql_query($sql);
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

	<h2><strong>Aide à la maintenance</strong></h2>
	<p>Vous devez régulièrement vérifier les tables d'emplois du temps de GEPI étant donné que le module est complètement autonome. Vous devez notamment vérifier et corriger ces tables dans les cas suivants :</p>
<p>1. <strong>Suppression d'un prof : </strong>Quand vous supprimez un prof de la base, il n'est pas supprimé automatiquement des emplois du temps.</p>
<p>2. <strong>Suppression d'un enseignement</strong> : Quand vous supprimez un enseignement d'une classe de la base, il n'est pas supprimé automatiquement des emplois du temps et ceci peut générer des erreurs.</p>
<p>3. <strong>Changement de classe</strong> : Quand vous déplacez un élève d'une classe à l'autre en cours d'année, vous devez vérifier ses affectations dans les AIDs qui ne sont pas modifiées automatiquement. Ceci peut poser des problèmes d'affichage dans le cas où l'élève a été affecté dans un AID "classe" comme par exemple des groupes de langue. En effet, un élève de 4A, affecté en 4C restera dans les AIDs de la 4A malgré sa nouvelle classe. De ce fait, si on visionne l'emploi du temps de la 4C, on va voir apparaître tous les AIDs dans lesquels cet élève est inscrit, c'est-à-dire les AIDs de la 4A. </p>
<p>4. <strong>Remplaçants</strong> : Quand vous créez un remplaçant, son emploi du temps n'est pas créé automatiquement. Vous devez dans ce cas transférer l'emploi du temps du prof remplacé. Pour cela, à partir du menu, allez dans Création, Transférer/Supprimer un edt.</p>

<p>Pour les 3 premiers cas cités ci-dessus, il suffit de lancer la procédure de vérification et de correction à partir du menu Maintenance, Vérifier/Corriger la base.</p>

<?php
$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
echo "</div>";
echo "</div>";
}

// inclusion du footer
require("../lib/footer.inc.php");
?>