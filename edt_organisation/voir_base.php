<?php

/**
 *
 *
 * @version $Id: voir_base.php $
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

$titre_page = "Emploi du temps - Voir la base";
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
// INSERT INTO droits VALUES ('/edt_organisation/voir_base.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'voir la table edt_cours', '');

$sql="SELECT 1=1 FROM droits WHERE id='/edt_organisation/voir_base.php';";
$res_test=mysql_query($sql);
if (mysql_num_rows($res_test)==0) {
	$sql="INSERT INTO droits VALUES ('/edt_organisation/voir_base.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F','voir la table edt_cours', '');";
	$res_insert=mysql_query($sql);
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}

if ($_SESSION["statut"] != "administrateur") {
	Die('Vous devez demander à votre administrateur l\'autorisation de voir cette page.');
}


// ===== Initialisation des variables =====
$start_list = isset($_GET["start_list"]) ? $_GET["start_list"] : (isset($_POST["start_list"]) ? $_POST["start_list"] : 0);
$trier = isset($_GET["trier"]) ? $_GET["trier"] : (isset($_POST["trier"]) ? $_POST["trier"] : 0);

// ============================================ 

if ($trier == 1) {
    $colonne = "id_cours";
}
else if ($trier == 2) {
    $colonne = "id_groupe";
}
else if ($trier == 3) {
    $colonne = "id_salle";
}
else if ($trier == 4) {
    $colonne = "jour_semaine";
}
else if ($trier == 5) {
    $colonne = "id_definie_periode";
}
else if ($trier == 6) {
    $colonne = "duree";
}
else if ($trier == 7) {
    $colonne = "heuredeb_dec";
}
else if ($trier == 8) {
    $colonne = "id_semaine";
}
else if ($trier == 9) {
    $colonne = "id_calendrier";
}
else if ($trier == 10) {
    $colonne = "modif_edt";
}
else if ($trier == 11) {
    $colonne = "login_prof";
}
else {
    $colonne = "id_cours";
}


// CSS et js particulier à l'EdT
$javascript_specifique = "edt_organisation/script/fonctions_edt";
$style_specifique = "templates/".NameTemplateEDT()."/css/style_edt";

//++++++++++ l'entête de Gepi +++++
require_once("../lib/header.inc");
//++++++++++ fin entête +++++++++++
//++++++++++ le menu EdT ++++++++++
require_once("./menu.inc.php");
//++++++++++ fin du menu ++++++++++

?>


<br/>
<!-- la page du corps de l'EdT -->

	<div id="lecorps">

	<?php 
        require_once("./menu.inc.new.php"); ?>

<h2><strong>Voir la table edt_cours</strong></h2>
<?php

	echo "<div class=\"titre_voir_base_7\"><a href=\"./voir_base.php?trier=1 \" >id_cours</a></div>";
	echo "<div class=\"titre_voir_base_7\"><a href=\"./voir_base.php?trier=2 \" >id_groupe</a></div>";
	echo "<div class=\"titre_voir_base_7\"><a href=\"./voir_base.php?trier=3 \" >id_salle</a></div>";
	echo "<div class=\"titre_voir_base_7\"><a href=\"./voir_base.php?trier=4 \" title=\"jour_semaine\">jour_semaine</a></div>";
	echo "<div class=\"titre_voir_base_7\"><a href=\"./voir_base.php?trier=5\" title=\"id_definie_periode\">id_definie_periode</a></div>";
	echo "<div class=\"titre_voir_base_7\"><a href=\"./voir_base.php?trier=6\" >duree</a></div>";
	echo "<div class=\"titre_voir_base_7\"><a href=\"./voir_base.php?trier=7\" title=\"heuredeb_dec\">heuredeb_dec</a></div>";
	echo "<div class=\"titre_voir_base_7\"><a href=\"./voir_base.php?trier=8\" title=\"id_semaine\">id_semaine</a></div>";
	echo "<div class=\"titre_voir_base_7\"><a href=\"./voir_base.php?trier=9\" title=\"id_calendrier\">id_calendrier</a></div>";
	echo "<div class=\"titre_voir_base_7\"><a href=\"./voir_base.php?trier=10\" >modif_edt</a></div>";
	echo "<div class=\"titre_voir_base_15\"><a href=\"./voir_base.php?trier=11\" >login_prof</a></div>";
    echo "<div style=\"clear:both;\"></div>";

    $req_cours = mysql_query("SELECT * FROM edt_cours ORDER BY ".$colonne." ASC  LIMIT ".$start_list.",50 ");
	while ($rep_cours = mysql_fetch_array($req_cours)) {


	    echo "<div class=\"cellule_voir_base_7\">".$rep_cours["id_cours"]."</div>";
	    echo "<div class=\"cellule_voir_base_7\">".$rep_cours["id_groupe"]."</div>";
	    echo "<div class=\"cellule_voir_base_7\">".$rep_cours["id_salle"]."</div>";
	    echo "<div class=\"cellule_voir_base_7\">".$rep_cours["jour_semaine"]."</div>";
	    echo "<div class=\"cellule_voir_base_7\">".$rep_cours["id_definie_periode"]."</div>";
	    echo "<div class=\"cellule_voir_base_7\">".$rep_cours["duree"]."</div>";
	    echo "<div class=\"cellule_voir_base_7\">".$rep_cours["heuredeb_dec"]."</div>";
	    echo "<div class=\"cellule_voir_base_7\">".$rep_cours["id_semaine"]."</div>";
	    echo "<div class=\"cellule_voir_base_7\">".$rep_cours["id_calendrier"]."</div>";
	    echo "<div class=\"cellule_voir_base_7\">".$rep_cours["modif_edt"]."</div>";
	    echo "<div class=\"cellule_voir_base_15\">".$rep_cours["login_prof"]."</div>";
        echo "<div style=\"clear:both;\"></div>";
	}
    $next_start = $start_list+50;

    $previous_start = $start_list-50;

    if ($previous_start < 0) {
        $previous_start = 0;
    }
	echo "<div style=\"float:left;text-align:center;font-size:45%;padding:3px;margin:1px;border:1px solid black;width:20%;background-color:#FAFFBD;\"><a href=\"./voir_base.php?start_list=".$previous_start."&amp;trier=".$trier." \" >précédent</a></div>";
	echo "<div style=\"float:left;text-align:center;font-size:45%;padding:3px;margin:1px;border:1px solid black;width:20%;background-color:#FAFFBD;\"><a href=\"./voir_base.php?start_list=".$next_start."&amp;trier=".$trier." \" >suivant</a></div>";    
    echo "<div style=\"clear:both;\"></div>";


?>
<?php
// inclusion du footer
require("../lib/footer.inc.php");
?>