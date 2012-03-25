<?php
/*
* $Id$
*
 * Copyright 2001, 2002 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Christian Chapel
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

$niveau_arbo = 2;
// Initialisations files
require_once("../../lib/initialisations.inc.php");

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);

//On vérifie si le module est activé
if (getSettingValue("active_module_absence")!='y') {
    die("Le module n'est pas activé.");
}


// Resume session

$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../../logout.php?auto=1");
    die();
};

if (!checkAccess()) {
    header("Location: ../../logout.php?auto=1");
    die();
}
?>

<script type="text/javascript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}

// Scrolling bug fixing by Pierre Gardenat
	NS4 = (document.layers) ? 1 : 0;
	IE4 = (document.all) ? 1 : 0;
	W3C = (document.getElementById) ? 1 : 0;
// W3C stands for the W3C standard, implemented in Mozilla (and Netscape 6) and IE5

// Function show(evt, name)
function showdiv ( evt, name ) {
  if (IE4) {
    evt = window.event;
  }

  var currentX,
      currentY,
      x,
      y,
      docWidth,
      docHeight,
      layerWidth,
      layerHeight,
      ele;


  if ( W3C ) {
    ele = document.getElementById(name);
    currentX = evt.clientX,
    currentY = evt.clientY+document.body.scrollTop+10;
    docWidth = document.width;
    docHeight = document.height;
    layerWidth = ele.style.width;
    layerHeight = ele.style.height;

  } else if ( NS4 ) {
    ele = document.layers[name];
    currentX = evt.pageX,
    currentY = evt.pageY;
    docWidth = document.width;
    docHeight = document.height;
    layerWidth = ele.clip.width;
    layerHeight = ele.clip.height;

  } else {  // meant for IE4
    ele = document.all[name];
    currentX = evt.clientX,
    currentY = evt.clientY+document.body.scrollTop+10;
    docHeight = document.body.offsetHeight;
    docWidth = document.body.offsetWidth;
    //var layerWidth = document.all[name].offsetWidth;
    // for some reason, this doesnt seem to work... so set it to 200
    layerWidth = 200;
    layerHeight = ele.offsetHeight;
  }


  if ( ( currentX + layerWidth ) > docWidth ) {
    x = ( currentX - layerWidth );
  }
  else {
    x = currentX;
  }
  if ( ( currentY + layerHeight ) >= docHeight ) {
     y = ( currentY - layerHeight - 20 );
  }
  else {
    y = currentY + 20;
  }
// (for debugging purpose) alert("docWidth " + docWidth + ", docHeight " + docHeight + "\nlayerWidth " + layerWidth + ", layerHeight " + layerHeight + "\ncurrentX " + currentX + ", currentY " + currentY + "\nx " + x + ", y " + y);


  if ( NS4 ) {
    //ele.xpos = parseInt ( x );
    ele.left = parseInt ( x );
    //ele.ypos = parseInt ( y );
    ele.top = parseInt ( y );
    ele.visibility = "show";
  } else {  // IE4 & W3C
    ele.style.left = parseInt ( x );
    ele.style.top = parseInt ( y );
    ele.style.visibility = "visible";
  }
}

function hidediv ( name ) {
  if (W3C) {
    document.getElementById(name).style.visibility = "hidden";
  } else if (NS4) {
    document.layers[name].visibility = "hide";
  } else {

      document.all[name].style.visibility = "hidden";

  }
}
//-->
</script>
<?php

//**************** EN-TETE *****************
$titre_page = "Gestion des absences";
require_once("../../lib/header.inc.php");
//**************** FIN EN-TETE *****************


//Configuration du calendrier
include("../../lib/calendrier/calendrier.class.php");
$cal_1 = new Calendrier("form1", "du");
$cal_2 = new Calendrier("form1", "au");

//choix du tri pour le tableau
$tri = (isset($_POST['tri']) AND $_POST['tri'] != '') ? $_POST['tri'] : 'nom, prenom';


// ===== Les fonctions du module absences ====== //

include("./functions.php");

//Quelques variabales
$datej = date('Y-m-d');
$annee_scolaire = annee_en_cours_t($datej);
$date_ce_jour = date('d/m/Y');


// ===== VARIABLES ===== //
    if (empty($_GET['submit2']) and empty($_POST['submit2'])) {$submit2="";}
      else { if (isset($_GET['submit2'])) {$submit2=$_GET['submit2'];} if (isset($_POST['submit2'])) {$submit2=$_POST['submit2'];} }
    if (empty($_GET['type']) and empty($_POST['type'])) {$type="A";}
      else { if (isset($_GET['type'])) {$type=$_GET['type'];} if (isset($_POST['type'])) {$type=$_POST['type'];} }
    if (empty($_GET['justifie']) and empty($_POST['justifie'])) {$justifie="";}
      else { if (isset($_GET['justifie'])) {$justifie=$_GET['justifie'];} if (isset($_POST['justifie'])) {$justifie=$_POST['justifie'];} }
    if (empty($_GET['nonjustifie']) and empty($_POST['nonjustifie'])) {$nonjustifie="";}
      else { if (isset($_GET['nonjustifie'])) {$nonjustifie=$_GET['nonjustifie'];} if (isset($_POST['nonjustifie'])) {$nonjustifie=$_POST['nonjustifie'];} }
    if (empty($_GET['motif']) and empty($_POST['motif'])) {$motif="";}
      else { if (isset($_GET['motif'])) {$motif=$_GET['motif'];} if (isset($_POST['motif'])) {$motif=$_POST['motif'];} }
    if (empty($_GET['classe_choix']) and empty($_POST['classe_choix'])) {$classe_choix="";}
      else { if (isset($_GET['classe_choix'])) {$classe_choix=$_GET['classe_choix'];} if (isset($_POST['classe_choix'])) {$classe_choix=$_POST['classe_choix'];} }
    if (empty($_GET['eleve_choix']) and empty($_POST['eleve_choix'])) {$eleve_choix="";}
      else { if (isset($_GET['eleve_choix'])) {$eleve_choix=$_GET['eleve_choix'];} if (isset($_POST['eleve_choix'])) {$eleve_choix=$_POST['eleve_choix'];} }
    if (empty($_GET['du']) and empty($_POST['du'])) {$du="";}
      else { if (isset($_GET['du'])) {$du=$_GET['du'];} if (isset($_POST['du'])) {$du=$_POST['du'];} }
    if (empty($_GET['au']) and empty($_POST['au'])) {$au="";}
      else { if (isset($_GET['au'])) {$au=$_GET['au'];} if (isset($_POST['au'])) {$au=$_POST['au'];} }
    if (empty($_GET['recherche']) and empty($_POST['recherche'])) {$recherche="";}
      else { if (isset($_GET['recherche'])) {$recherche=$_GET['recherche'];} if (isset($_POST['recherche'])) {$recherche=$_POST['recherche'];} }

// Utilisation d'un critère pour garder la date après avoir trié
if ($type == "A" and $submit2 == "" and empty($_POST['tri'])) {
	$type="A"; $justifie="1"; $nonjustifie="1"; $motif="tous"; $classe_choix="tous"; $eleve_choix="tous"; $du="$date_ce_jour"; $au="jj/mm/aaaa";
} elseif ($type == "D" and $submit2 == "" and empty($_POST['tri'])) {
	$type="D"; $justifie="1"; $nonjustifie="1"; $motif="tous"; $classe_choix="tous"; $eleve_choix="tous"; $du="$date_ce_jour"; $au="jj/mm/aaaa";
} elseif ($type == "R" and $submit2 == "" and empty($_POST['tri'])) {
	$type="R"; $justifie="1"; $nonjustifie="1"; $motif="tous"; $classe_choix="tous"; $eleve_choix="tous"; $du="$date_ce_jour"; $au="jj/mm/aaaa";
} elseif ($type == "I" and $submit2 == "" and empty($_POST['tri'])) {
	$type="I"; $justifie="1"; $nonjustifie="1"; $motif="tous"; $classe_choix="tous"; $eleve_choix="tous"; $du="$date_ce_jour"; $au="jj/mm/aaaa";
}

if ($du == "jj/mm/aaaa" or $du == "") {$du = $date_ce_jour; }
if ($au == "jj/mm/aaaa" or $au == "") {$au = $du; }

$pagedarriver = isset($_GET['pagedarriver']) ? $_GET['pagedarriver'] : (isset($_POST['pagedarriver']) ? $_POST['pagedarriver'] : '');


//REQUETE

// On ajoute un paramètre sur les élèves de ce CPE en particulier
$sql_eleves_cpe = "SELECT e_login FROM j_eleves_cpe WHERE cpe_login = '".$_SESSION['login']."'";
$query_eleves_cpe = mysql_query($sql_eleves_cpe) OR die('Erreur SQL ! <br />' . $sql_eleves_cpe . ' <br /> ' . mysql_error());
$test_cpe = array();

$test_nbre_eleves_cpe = mysql_num_rows($query_eleves_cpe);
while($test_eleves_cpe = mysql_fetch_array($query_eleves_cpe)){
	$test_cpe[] = $test_eleves_cpe['e_login'];
}

//requête pour lister les motifs d'absence
$requete_liste_motif = "SELECT init_motif_absence, def_motif_absence FROM ".$prefix_base."absences_motifs ORDER BY init_motif_absence ASC";

//requete sur les champs des tableaux
//Extension de la requete pour affichage des classes dans le tableau (DIDIER)
if ($motif == "tous" and $classe_choix == "tous" AND $eleve_choix == "tous" AND $justifie == "1" AND $nonjustifie == "1") {

	$requete_recherche = "SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes , ".$prefix_base."classes
	  								WHERE type_absence_eleve = '".$type."'
									AND eleve_absence_eleve = ".$prefix_base."eleves.login
									AND ((d_date_absence_eleve >= '".date_sql($du)."'
							  			AND d_date_absence_eleve <= '".date_sql($au)."')
							  			OR (a_date_absence_eleve >= '".date_sql($du)."'
							  			AND a_date_absence_eleve <= '".date_sql($au)."')
										OR (d_date_absence_eleve <= '".date_sql($du)."'
							  			AND a_date_absence_eleve >= '".date_sql($au)."'))
										AND ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login
										AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id
										GROUP BY ".$prefix_base."absences_eleves.id_absence_eleve
										ORDER BY ".$tri." ASC";


} elseif ($motif == "tous" AND $classe_choix == "tous" AND $eleve_choix == "tous" AND $justifie == "1" AND $nonjustifie != "1") {

	$requete_recherche = "SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes , ".$prefix_base."classes
									WHERE type_absence_eleve = '".$type."'
									AND eleve_absence_eleve = ".$prefix_base."eleves.login
									AND ((d_date_absence_eleve >= '".date_sql($du)."'
										AND d_date_absence_eleve <= '".date_sql($au)."')
										OR (a_date_absence_eleve >= '".date_sql($du)."'
										AND a_date_absence_eleve <= '".date_sql($au)."')
										OR (d_date_absence_eleve <= '".date_sql($du)."'
							  			AND a_date_absence_eleve >= '".date_sql($au)."'))
									AND justify_absence_eleve = 'O'
								     AND ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login
										AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id
										GROUP BY ".$prefix_base."absences_eleves.id_absence_eleve
										ORDER BY ".$tri." ASC";

}elseif ($motif == "tous" AND $classe_choix == "tous" AND $eleve_choix == "tous" AND $justifie != "1" AND $nonjustifie == "1") {

	$requete_recherche = "SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes , ".$prefix_base."classes
									WHERE type_absence_eleve = '".$type."'
									AND eleve_absence_eleve = ".$prefix_base."eleves.login
									AND ((d_date_absence_eleve >= '".date_sql($du)."'
										AND d_date_absence_eleve <= '".date_sql($au)."')
										OR (a_date_absence_eleve >= '".date_sql($du)."'
										AND a_date_absence_eleve <= '".date_sql($au)."')
										OR (d_date_absence_eleve <= '".date_sql($du)."'
							  			AND a_date_absence_eleve >= '".date_sql($au)."'))
									AND justify_absence_eleve != 'O'
								AND ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login
										AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id
										GROUP BY ".$prefix_base."absences_eleves.id_absence_eleve
										ORDER BY ".$tri." ASC";

} elseif ($motif != "tous" AND $classe_choix == "tous" AND $eleve_choix == "tous" AND $justifie == "1" AND $nonjustifie == "1") { //Spécifie le motif

	$requete_recherche = "SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes , ".$prefix_base."classes
									WHERE type_absence_eleve = '".$type."'
									AND eleve_absence_eleve = ".$prefix_base."eleves.login
									AND ((d_date_absence_eleve >= '".date_sql($du)."'
										AND d_date_absence_eleve <= '".date_sql($au)."')
										OR (a_date_absence_eleve >= '".date_sql($du)."'
										AND a_date_absence_eleve <= '".date_sql($au)."')
										OR (d_date_absence_eleve <= '".date_sql($du)."'
							  			AND a_date_absence_eleve >= '".date_sql($au)."'))
									AND motif_absence_eleve = '".$motif."'
								AND ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login
										AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id
										GROUP BY ".$prefix_base."absences_eleves.id_absence_eleve
										ORDER BY ".$tri." ASC";

} elseif ($motif != "tous" AND $classe_choix == "tous" AND $eleve_choix == "tous" AND $justifie == "1" AND $nonjustifie != "1") {

	$requete_recherche = "SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes , ".$prefix_base."classes
									WHERE type_absence_eleve = '".$type."'
									AND eleve_absence_eleve = ".$prefix_base."eleves.login
									AND ((d_date_absence_eleve >= '".date_sql($du)."'
										AND d_date_absence_eleve <= '".date_sql($au)."')
										OR (a_date_absence_eleve >= '".date_sql($du)."'
										AND a_date_absence_eleve <= '".date_sql($au)."')
										OR (d_date_absence_eleve <= '".date_sql($du)."'
							  			AND a_date_absence_eleve >= '".date_sql($au)."'))
									AND justify_absence_eleve = 'O'
									AND motif_absence_eleve = '".$motif."'
								AND ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login
										AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id
										GROUP BY ".$prefix_base."absences_eleves.id_absence_eleve
										ORDER BY ".$tri." ASC";

} elseif ($motif != "tous" AND $classe_choix == "tous" AND $eleve_choix == "tous" AND $justifie != "1" AND $nonjustifie == "1") {

	$requete_recherche = "SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes , ".$prefix_base."classes
									WHERE type_absence_eleve = '".$type."'
									AND eleve_absence_eleve = ".$prefix_base."eleves.login
									AND ((d_date_absence_eleve >= '".date_sql($du)."'
										AND d_date_absence_eleve <= '".date_sql($au)."')
										OR (a_date_absence_eleve >= '".date_sql($du)."'
										AND a_date_absence_eleve <= '".date_sql($au)."')
										OR (d_date_absence_eleve <= '".date_sql($du)."'
							  			AND a_date_absence_eleve >= '".date_sql($au)."'))
									AND justify_absence_eleve != 'O'
									AND motif_absence_eleve = '".$motif."'
								AND ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login
										AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id
										GROUP BY ".$prefix_base."absences_eleves.id_absence_eleve
										ORDER BY ".$tri." ASC";

}

//avec spécification des classes
if ($motif == "tous" AND $classe_choix != "tous" AND $eleve_choix == "tous" AND $justifie == "1" AND $nonjustifie == "1") {

	$requete_recherche = "SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes
									WHERE type_absence_eleve = '".$type."'
									AND eleve_absence_eleve = ".$prefix_base."eleves.login
									AND ((d_date_absence_eleve >= '".date_sql($du)."'
										AND d_date_absence_eleve <= '".date_sql($au)."')
										OR (a_date_absence_eleve >= '".date_sql($du)."'
										AND a_date_absence_eleve <= '".date_sql($au)."')
										OR (d_date_absence_eleve <= '".date_sql($du)."'
							  			AND a_date_absence_eleve >= '".date_sql($au)."'))
									AND ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login
									AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id
									AND ".$prefix_base."classes.id='".$classe_choix."'
								GROUP BY ".$prefix_base."absences_eleves.id_absence_eleve
								ORDER BY ".$tri." ASC";

} elseif ($motif == "tous" AND $classe_choix != "tous" AND $eleve_choix == "tous" AND $justifie == "1" AND $nonjustifie != "1") {

	$requete_recherche = "SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes
									WHERE type_absence_eleve = '".$type."'
									AND eleve_absence_eleve = ".$prefix_base."eleves.login
									AND ((d_date_absence_eleve >= '".date_sql($du)."'
										AND d_date_absence_eleve <= '".date_sql($au)."')
										OR (a_date_absence_eleve >= '".date_sql($du)."'
										AND a_date_absence_eleve <= '".date_sql($au)."')
										OR (d_date_absence_eleve <= '".date_sql($du)."'
							  			AND a_date_absence_eleve >= '".date_sql($au)."'))
									AND justify_absence_eleve = 'O'
									AND ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login
									AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id
									AND ".$prefix_base."classes.id='".$classe_choix."'
								GROUP BY ".$prefix_base."absences_eleves.id_absence_eleve
								ORDER BY ".$tri." ASC";

} elseif ($motif == "tous" AND $classe_choix != "tous" AND $eleve_choix == "tous" AND $justifie != "1" AND $nonjustifie == "1") {

	$requete_recherche = "SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes
									WHERE type_absence_eleve = '".$type."'
									AND eleve_absence_eleve = ".$prefix_base."eleves.login
									AND ((d_date_absence_eleve >= '".date_sql($du)."'
										AND d_date_absence_eleve <= '".date_sql($au)."')
										OR (a_date_absence_eleve >= '".date_sql($du)."'
										AND a_date_absence_eleve <= '".date_sql($au)."')
										OR (d_date_absence_eleve <= '".date_sql($du)."'
							  			AND a_date_absence_eleve >= '".date_sql($au)."'))
									AND justify_absence_eleve != 'O'
									AND ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login
									AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id
									AND ".$prefix_base."classes.id='".$classe_choix."'
								GROUP BY ".$prefix_base."absences_eleves.id_absence_eleve
								ORDER BY ".$tri." ASC";

} elseif ($motif != "tous" AND $classe_choix != "tous" AND $eleve_choix == "tous" AND $justifie == "1" AND $nonjustifie == "1") { //Spécifie le motif

	$requete_recherche = "SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes
									WHERE type_absence_eleve = '".$type."'
									AND eleve_absence_eleve = ".$prefix_base."eleves.login
									AND ((d_date_absence_eleve >= '".date_sql($du)."'
										AND d_date_absence_eleve <= '".date_sql($au)."')
										OR (a_date_absence_eleve >= '".date_sql($du)."'
										AND a_date_absence_eleve <= '".date_sql($au)."')
										OR (d_date_absence_eleve <= '".date_sql($du)."'
							  			AND a_date_absence_eleve >= '".date_sql($au)."'))
									AND motif_absence_eleve = '".$motif."'
									AND ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login
									AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id
									AND ".$prefix_base."classes.id='".$classe_choix."'
								GROUP BY ".$prefix_base."absences_eleves.id_absence_eleve
								ORDER BY ".$tri." ASC";

} elseif ($motif != "tous" AND $classe_choix != "tous" AND $eleve_choix == "tous" AND $justifie == "1" AND $nonjustifie != "1") {

	$requete_recherche = "SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes
									WHERE type_absence_eleve = '".$type."'
									AND eleve_absence_eleve = ".$prefix_base."eleves.login
									AND ((d_date_absence_eleve >= '".date_sql($du)."'
										AND d_date_absence_eleve <= '".date_sql($au)."')
										OR (a_date_absence_eleve >= '".date_sql($du)."'
										AND a_date_absence_eleve <= '".date_sql($au)."')
										OR (d_date_absence_eleve <= '".date_sql($du)."'
							  			AND a_date_absence_eleve >= '".date_sql($au)."'))
									AND justify_absence_eleve = 'O'
									AND motif_absence_eleve = '".$motif."'
									AND ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login
									AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id
									AND ".$prefix_base."classes.id='".$classe_choix."'
								GROUP BY ".$prefix_base."absences_eleves.id_absence_eleve
								ORDER BY ".$tri." ASC";

} elseif ($motif != "tous" AND $classe_choix != "tous" AND $eleve_choix == "tous" AND $justifie != "1" AND $nonjustifie == "1") {

	$requete_recherche = "SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes
									WHERE type_absence_eleve = '".$type."'
									AND eleve_absence_eleve = ".$prefix_base."eleves.login
									AND ((d_date_absence_eleve >= '".date_sql($du)."'
										AND d_date_absence_eleve <= '".date_sql($au)."')
										OR (a_date_absence_eleve >= '".date_sql($du)."'
										AND a_date_absence_eleve <= '".date_sql($au)."')
										OR (d_date_absence_eleve <= '".date_sql($du)."'
							  			AND a_date_absence_eleve >= '".date_sql($au)."'))
									AND justify_absence_eleve != 'O'
									AND motif_absence_eleve = '".$motif."'
									AND ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login
									AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id
									AND ".$prefix_base."classes.id='".$classe_choix."'
								GROUP BY ".$prefix_base."absences_eleves.id_absence_eleve
								ORDER BY ".$tri." ASC";

} elseif ($motif == "tous" AND $classe_choix != "tous" AND $eleve_choix != "tous" AND $justifie == "1" AND $nonjustifie == "1") { //avec spécification des eleves

	$requete_recherche = "SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes
									WHERE type_absence_eleve = '".$type."'
									AND eleve_absence_eleve = ".$prefix_base."eleves.login
									AND ((d_date_absence_eleve >= '".date_sql($du)."'
									AND d_date_absence_eleve <= '".date_sql($au)."')
										OR (a_date_absence_eleve >= '".date_sql($du)."'
										AND a_date_absence_eleve <= '".date_sql($au)."')
										OR (d_date_absence_eleve <= '".date_sql($du)."'
							  			AND a_date_absence_eleve >= '".date_sql($au)."'))
									AND ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login
									AND ".$prefix_base."eleves.login='".$eleve_choix."'
									AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id
									AND ".$prefix_base."classes.id='".$classe_choix."'
								GROUP BY ".$prefix_base."absences_eleves.id_absence_eleve
								ORDER BY ".$tri." ASC";

} elseif ($motif == "tous" AND $classe_choix != "tous" AND $eleve_choix != "tous" AND $justifie == "1" AND $nonjustifie != "1") {

	$requete_recherche = "SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes
									WHERE type_absence_eleve = '".$type."'
									AND eleve_absence_eleve = ".$prefix_base."eleves.login
									AND ((d_date_absence_eleve >= '".date_sql($du)."'
										AND d_date_absence_eleve <= '".date_sql($au)."')
										OR (a_date_absence_eleve >= '".date_sql($du)."'
										AND a_date_absence_eleve <= '".date_sql($au)."')
										OR (d_date_absence_eleve <= '".date_sql($du)."'
							  			AND a_date_absence_eleve >= '".date_sql($au)."'))
									AND justify_absence_eleve = 'O'
									AND ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login
									AND ".$prefix_base."eleves.login='".$eleve_choix."'
									AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id
									AND ".$prefix_base."classes.id='".$classe_choix."'
								GROUP BY ".$prefix_base."absences_eleves.id_absence_eleve
								ORDER BY ".$tri." ASC";

} elseif ($motif == "tous" AND $classe_choix != "tous" AND $eleve_choix != "tous" AND $justifie != "1" AND $nonjustifie == "1") {

	$requete_recherche = "SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes
									WHERE type_absence_eleve = '".$type."'
									AND eleve_absence_eleve = ".$prefix_base."eleves.login
									AND ((d_date_absence_eleve >= '".date_sql($du)."'
										AND d_date_absence_eleve <= '".date_sql($au)."')
										OR (a_date_absence_eleve >= '".date_sql($du)."'
										AND a_date_absence_eleve <= '".date_sql($au)."')
										OR (d_date_absence_eleve <= '".date_sql($du)."'
							  			AND a_date_absence_eleve >= '".date_sql($au)."'))
									AND justify_absence_eleve != 'O'
									AND ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login
									AND ".$prefix_base."eleves.login='".$eleve_choix."'
									AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id
									AND ".$prefix_base."classes.id='".$classe_choix."'
								GROUP BY ".$prefix_base."absences_eleves.id_absence_eleve
								ORDER BY ".$tri." ASC";

} elseif ($motif != "tous" AND $classe_choix != "tous" AND $eleve_choix != "tous" AND $justifie == "1" AND $nonjustifie == "1") { //Spécifie le motif

	$requete_recherche = "SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes
									WHERE type_absence_eleve = '".$type."'
									AND eleve_absence_eleve = ".$prefix_base."eleves.login
									AND ((d_date_absence_eleve >= '".date_sql($du)."'
										AND d_date_absence_eleve <= '".date_sql($au)."')
										OR (a_date_absence_eleve >= '".date_sql($du)."'
										AND a_date_absence_eleve <= '".date_sql($au)."')
										OR (d_date_absence_eleve <= '".date_sql($du)."'
							  			AND a_date_absence_eleve >= '".date_sql($au)."'))
									AND motif_absence_eleve = '".$motif."'
									AND ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login
									AND ".$prefix_base."eleves.login='".$eleve_choix."'
									AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id
									AND ".$prefix_base."classes.id='".$classe_choix."'
								GROUP BY ".$prefix_base."absences_eleves.id_absence_eleve
								ORDER BY ".$tri." ASC";

} elseif ($motif != "tous" AND $classe_choix != "tous" AND $eleve_choix != "tous" AND $justifie == "1" AND $nonjustifie != "1") {

	$requete_recherche = "SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes
									WHERE type_absence_eleve = '".$type."'
									AND eleve_absence_eleve = ".$prefix_base."eleves.login
									AND ((d_date_absence_eleve >= '".date_sql($du)."'
										AND d_date_absence_eleve <= '".date_sql($au)."')
										OR (a_date_absence_eleve >= '".date_sql($du)."'
										AND a_date_absence_eleve <= '".date_sql($au)."')
										OR (d_date_absence_eleve <= '".date_sql($du)."'
							  			AND a_date_absence_eleve >= '".date_sql($au)."'))
									AND justify_absence_eleve = 'O'
									AND motif_absence_eleve = '".$motif."'
									AND ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login
									AND ".$prefix_base."eleves.login='".$eleve_choix."'
									AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id
									AND ".$prefix_base."classes.id='".$classe_choix."'
								GROUP BY ".$prefix_base."absences_eleves.id_absence_eleve
								ORDER BY ".$tri." ASC";

} elseif ($motif != "tous" AND $classe_choix != "tous" AND $eleve_choix != "tous" AND $justifie != "1" AND $nonjustifie == "1") {

	$requete_recherche = "SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes
									WHERE type_absence_eleve = '".$type."'
									AND eleve_absence_eleve = ".$prefix_base."eleves.login
									AND ((d_date_absence_eleve >= '".date_sql($du)."'
										AND d_date_absence_eleve <= '".date_sql($au)."')
										OR (a_date_absence_eleve >= '".date_sql($du)."'
										AND a_date_absence_eleve <= '".date_sql($au)."')
										OR (d_date_absence_eleve <= '".date_sql($du)."'
							  			AND a_date_absence_eleve >= '".date_sql($au)."'))
									AND justify_absence_eleve != 'O'
									AND motif_absence_eleve = '".$motif."'
									AND ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login
									AND ".$prefix_base."eleves.login='".$eleve_choix."'
									AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id
									AND ".$prefix_base."classes.id='".$classe_choix."'
								GROUP BY ".$prefix_base."absences_eleves.id_absence_eleve
								ORDER BY ".$tri." ASC";

}

if ($recherche == "afficher")
{
	if ($classe_choix == "tous") {
		$requete_liste_eleve = "SELECT login, nom, prenom FROM ".$prefix_base."eleves ORDER BY nom, prenom ASC";
	} else {
		//$requete_liste_eleve = "SELECT ".$prefix_base."eleves.login, ".$prefix_base."eleves.nom, ".$prefix_base."eleves.prenom, ".$prefix_base."j_eleves_classes.login, ".$prefix_base."j_eleves_classes.id_classe, ".$prefix_base."j_eleves_classes.periode, ".$prefix_base."classes.id, ".$prefix_base."classes.classe, ".$prefix_base."classes.nom_complet FROM ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes WHERE ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id AND ".$prefix_base."classes.id='".$classe_choix."' GROUP BY ".$prefix_base."eleves.nom, ".$prefix_base."eleves.prenom ORDER BY nom, prenom ASC";
		$requete_liste_eleve = "SELECT ".$prefix_base."eleves.login, ".$prefix_base."eleves.nom, ".$prefix_base."eleves.prenom, ".$prefix_base."j_eleves_classes.login, ".$prefix_base."j_eleves_classes.id_classe, ".$prefix_base."j_eleves_classes.periode, ".$prefix_base."classes.id, ".$prefix_base."classes.classe, ".$prefix_base."classes.nom_complet FROM ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes WHERE ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id AND ".$prefix_base."classes.id='".$classe_choix."' GROUP BY ".$prefix_base."eleves.login, ".$prefix_base."eleves.nom, ".$prefix_base."eleves.prenom ORDER BY nom, prenom ASC"; // 20100430
	}
	$requete_liste_classe = "SELECT id, classe, nom_complet FROM ".$prefix_base."classes ORDER BY nom_complet DESC";
}

if( $pagedarriver === 'gestion_absences') {
	echo "<p class='bold'> <a href=\"../gestion/gestion_absences.php\"><img src='../../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";
}elseif( $pagedarriver === 'prof_ajout_abs') {
	echo "<p class='bold'> <a href=\"../professeurs/prof_ajout_abs.php\"><img src='../../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";
}

/* div de centrage du tableau pour ie5 */ ?>
<div style="text-align:center">
	<form method="post" action="" name="form1">
		<fieldset style="width: 400px; margin: auto;">
			<legend class="legend_texte">&nbsp;Recherche&nbsp;</legend>

          	<div style="text-align:left">[ <?php if($recherche=="cacher" or $recherche=="") { ?>
				<a href="tableau.php?recherche=afficher&amp;pagedarriver=<?php echo $pagedarriver; ?>">afficher</a><?php } if($recherche=="afficher") { ?>
				<a href="tableau.php?recherche=cacher&amp;pagedarriver=<?php echo $pagedarriver; ?>">cacher</a><?php } ?> ]
				<br />

    <?php if ($recherche == "afficher") { ?>

				Type
				<select name="type" id="type">
					<option value="A" <?php if ($type == "A") {?>selected="selected"<?php } ?>>Absence</option>
					<option value="R" <?php if ($type == "R") {?>selected="selected"<?php } ?>>Retard</option>
					<option value="D" <?php if ($type == "D") {?>selected="selected"<?php } ?>>Dispense</option>
					<option value="I" <?php if ($type == "I") {?>selected="selected"<?php } ?>>Infirmerie</option>
				</select>
				Justifiée <input name="justifie" type="checkbox" id="justifie3" value="1" <?php if ($justifie == "1") {?>checked="checked"<?php } ?> />
				Non justifiée <input name="nonjustifie" type="checkbox" id="nonjustifie" value="1" <?php if ($nonjustifie == "1") {?>checked="checked"<?php } ?> /><br />

    <?php if($type == "A" OR $type == "R") { ?>

				Motif
             	<select name="motif" id="motif">
                 	<option value="tous" <?php if (empty($motif)) {?>selected="selected"<?php } ?>>tous</option>

	<?php
		$resultat_liste_motif = mysql_query($requete_liste_motif) or die('Erreur SQL !'.$requete_liste_classe.'<br />'.mysql_error());
		while ( $data_liste_motif = mysql_fetch_array ($resultat_liste_motif))
		{
			if ($motif==$data_liste_motif['init_motif_absence']) {
				$selected = "selected='selected'";
			} else {
				$selected = "";
			} ?>
					<option value="<?php echo $data_liste_motif['init_motif_absence']; ?>" <?php echo $selected; ?>><?php echo $data_liste_motif['init_motif_absence']." - ".$data_liste_motif['def_motif_absence']; ?></option>
    <?php
		} ?>
				</select><br />
    <?php } else { ?>
				<input type="hidden" name="motif" value="tous" />
	<?php } ?>
				Classes
				<select name="classe_choix" id="classe_choix">
					<option value="tous" <?php if (empty($classe_choix)) {?>selected="selected"<?php } ?>>toutes</option>
    <?php
		$resultat_liste_classe = mysql_query($requete_liste_classe) or die('Erreur SQL !'.$requete_liste_classe.'<br />'.mysql_error());
		While ( $data_liste_classe = mysql_fetch_array ($resultat_liste_classe))
		{
			if ($classe_choix==$data_liste_classe['id']) {
				$selected = "selected='selected'";
			} else {
				$selected = "";
			}?>

					<option value="<?php echo $data_liste_classe['id']; ?>" <?php echo $selected; ?>><?php echo $data_liste_classe['nom_complet']; ?></option>

    <?php
		} ?>
				</select>
	<?php if($classe_choix != "tous") { ?>
			<br />
				Elèves
            	<select name="eleve_choix" id="eleve_choix" style="width: 25em;">
					<option value="tous" <?php if (empty($eleve_choix)) {?>selected<?php } ?>>tous</option>
	<?php
		$resultat_liste_eleve = mysql_query($requete_liste_eleve) or die('Erreur SQL !'.$requete_liste_eleve.'<br />'.mysql_error());
		While ( $data_liste_eleve = mysql_fetch_array ($resultat_liste_eleve))
		{
			if ($eleve_choix==$data_liste_eleve['login']) {
				$selected = "selected='selected'";
			} else {
				$selected = "";
			}?>

					<option value="<?php echo $data_liste_eleve['login']; ?>" <?php echo $selected; ?>><?php echo strtoupper($data_liste_eleve['nom'])." ".ucfirst($data_liste_eleve['prenom']); ?></option>

    <?php
		} ?>

            	</select>
	<?php } else { ?>
		  	<input type="hidden" name="eleve_choix" value="tous" /><?php } ?><br />
			Du
			<input name="du" type="text" id="du" value="<?php if (empty($du)) {?>jj/mm/aaaa<?php } else {echo $du; }?>" size="12" maxlength="12" /><a href="#calend" onclick="<?php echo $cal_1->get_strPopup('../../lib/calendrier/pop.calendrier.php', 350, 170); ?>"><img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a>
			Au
			<input name="au" type="text" id="au" value="<?php if (empty($au)) {?>jj/mm/aaaa<?php } else {echo $au; }?>" size="12" maxlength="12" /><a href="#calend" onclick="<?php echo $cal_2->get_strPopup('../../lib/calendrier/pop.calendrier.php', 350, 170); ?>"><img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a>
			<input type="hidden" name="pagedarriver" value="<?php echo $pagedarriver; ?>" />
			<input type="hidden" name="tri" value=""/>
			<input type="submit" name="submit2" value="Valider" /><br />
    <?php } ?>

<p>[ <a href="export_csv.php?type=<?php echo $type; ?>&amp;justifie=<?php echo $justifie; ?>&amp;nonjustifie=<?php echo $nonjustifie; ?>&amp;motif=<?php echo $motif; ?>&amp;classe_choix=<?php echo $classe_choix; ?>&amp;eleve_choix=<?php echo $eleve_choix; ?>&amp;du=<?php echo $du; ?>&amp;au=<?php echo $au; ?>">Exportation des données en csv</a> ]</p>

<?php /* ajout impression pdf didier */ ?>
<p>[<a href="tableau_pdf.php?type=<?php echo $type; ?>&amp;justifie=<?php echo $justifie; ?>&amp;nonjustifie=<?php echo $nonjustifie; ?>&amp;motif=<?php echo $motif; ?>&amp;classe_choix=<?php echo $classe_choix; ?>&amp;eleve_choix=<?php echo $eleve_choix; ?>&amp;du=<?php echo $du; ?>&amp;au=<?php echo $au; ?>&amp;tri=<?php echo $tri; ?>" target="_blank">Impression en Pdf</a>]</p>
	</div>
	</fieldset>
</form>
<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<div class="centre"><span class="norme_absence_bleu">Un passage avec la souris sur le NOM permet d'afficher la fiche de l'&eacute;l&egrave;ve</span></div>


<?php
if ($type == "A" or $type == "tous")
{

	$execution_div = mysql_query($requete_recherche) or die('Erreur SQL !'.$requete_recherche.'<br />'.mysql_error());
	while ( $data_div = mysql_fetch_array( $execution_div ) )
	{
		if (in_array($data_div['eleve_absence_eleve'], $test_cpe) OR $test_nbre_eleves_cpe === 0) {

 ?>
	<div id="d<?php echo $data_div['id_absence_eleve']; ?>" style="position: absolute; z-index: 20; visibility: hidden; top: 0px; left: 0px;">
		<table border="0" cellpadding="2" cellspacing="2" class="tableau_calque_information">
			<tr>
				<td class="texte_fondjaune_calque_information"><?php echo "<b>".strtoupper($data_div['nom'])."</b> ".ucfirst($data_div['prenom']); ?> élève de <?php echo "<b>".classe_de($data_div['login'])."</b>"; $id_classe_eleve = classe_de($data_div['login']); ?> &agrave; &eacute;t&eacute; absent<?php if ($data_div['sexe'] == "F") { ?>e<?php } ?><br />le <?php echo date_frl($data_div['d_date_absence_eleve']); ?><?php if (($data_div['a_date_absence_eleve'] != $data_div['d_date_absence_eleve'] and $data_div['a_date_absence_eleve'] != "") or $data_div['a_date_absence_eleve'] == "0000-00-00") { ?> au <?php echo date_frl($data_div['a_date_absence_eleve']); ?><?php } ?><br /><?php if ($data_div['a_heure_absence_eleve'] == "00:00:00" or $data_div['a_heure_absence_eleve'] == "") { ?>à <?php } else { ?>de <?php } ?><?php echo heure($data_div['d_heure_absence_eleve']); ?> <?php if ($data_div['a_heure_absence_eleve'] == "00:00:00" or $data_div['a_heure_absence_eleve'] == "") { } else { ?> à <?php ?> <?php echo heure($data_div['a_heure_absence_eleve']); } ?></td>
				<?php
				// On construit la ligne de la justification
				if ($data_div['justify_absence_eleve'] == "O") {
					$class = 'norme_absence_vert';
					$style = '';
					$texte = 'a donn&eacute; pour justification : ';
				} elseif($data_div['justify_absence_eleve'] == "T") {
					$class = "norme_absence_vert";
					$style = ' style="color: orange;"';
					$texte = 'a justifi&eacute; par t&eacute;l&eacute;phone : ';
				} else {
					$class = 'norme_absence_rouge';
					$style = '';
					$texte = 'N\'a pas donn&eacute; de justification';
				}

				if (getSettingValue("active_module_trombinoscopes")=='y') {
					$nom_photo = nom_photo($data_div['elenoet']);
					$photo = $nom_photo;
					//if (($nom_photo == "") or (!(file_exists($photo)))) {
					if (($nom_photo == NULL) or (!(file_exists($photo)))) {
						$photo = "../../mod_trombinoscopes/images/trombivide.jpg";
					}
					$valeur=redimensionne_image($photo);
				?>
				<td style="width: 60px; vertical-align: top" rowspan="4"><img src="<?php echo $photo; ?>" style="width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px; border: 0px" alt="" title="" /></td><?php
                } ?>
                </tr>
                <tr>
                	<td class="norme_absence">Pour le motif : <?php echo motif_de($data_div['motif_absence_eleve']); ?></td>
                </tr>
                <tr>
                	<td class="norme_absence"><span class="<?php echo $class; ?>"<?php echo $style; ?>><b><?php echo $texte; ?></b></span></td>
                 </tr>
                 <tr>
                   <td class="norme_absence"><?php if(!empty($data_div['info_justify_absence_eleve'])) { ?><blockquote><?php echo $data_div['info_justify_absence_eleve']; ?></blockquote><?php } ?></td>
                 </tr>
				 <?php
				  // vérification de la page d'arrivée pour affichage telephone didier
				 if( $pagedarriver === 'gestion_absences') { ?>
				 <tr class="texte_fondjaune_calque_information">
                <td colspan="2">
                <?php

				// gestion de l'affichage des numéro de téléphone didier
				$info_responsable = tel_responsable($data_div['ele_id']);

				$telephone = ''; $telephone_pers = ''; $telephone_prof = ''; $telephone_port = '';

				if ( !empty($info_responsable) )
				{
					// L'affichage du numéro de téléphone du responsable 1
					$nbre = count($info_responsable);
					for ($i = 0 ; $i < $nbre ; $i++){
						if ($info_responsable[$i]['resp_legal'] == '1') {

							$ident_resp = ' <span style="font-size: 0.8em;">(' . $info_responsable[$i]['nom'] . ' ' . $info_responsable[$i]['prenom'] . ' : resp n° ' . $info_responsable[$i]['resp_legal'] . ')</span>';
							if ( $info_responsable[$i]['tel_pers'] != '' ) {
								$telephone_pers = '<br />Pers. <strong>'.present_tel($info_responsable[$i]['tel_pers']).'</strong> ';
							}
							if ( $info_responsable[$i]['tel_prof'] != ''  ) {
								$telephone_prof = '<br />Prof. <strong>'.present_tel($info_responsable[$i]['tel_prof']).'</strong> ';
							}
							if ( $info_responsable[$i]['tel_port'] != ''  ) {
								$telephone_port = '<br />Port.<img src="../images/attention.png" alt="Attention numéro surtaxé" title="Attention numéro surtaxé" border="0" height="14" width="14" /> '.present_tel($info_responsable[$i]['tel_port']);
							}
						}
					}

				}

				if ( $telephone_pers != '' and $telephone_prof === '' ) {
					$telephone = $telephone_pers;
				}elseif ( $telephone_pers === '' and $telephone_prof != '' ) {
					$telephone = $telephone_prof;
				}elseif ( $telephone_pers != '' and $telephone_prof != '' ) {
					$telephone = $telephone_pers . ' ' . $telephone_prof;
				}
				if ( $telephone_pers === '' and $telephone_prof === '' and $telephone_port != '' ) {
					$telephone = ''; //$telephone_port . ' ! surtaxe';
				}

				if ( $telephone_pers != '' or $telephone_prof != '' or $telephone_port != '' ) {
					$telephone = 'Téléphone responsable : '.$telephone;
				} else {
					$telephone = 'Aucun numéro de téléphone disponible';
				}

				echo $telephone . $ident_resp . $telephone_port;

		  		?>
                </td>
              </tr>
			  <?php } ?>
            </table>
      </div>
<?php
		}
	} ?>


<?php /* div de centrage du tableau pour ie5 */ ?>
<div style="text-align:center">
  <table style="margin: auto; width: 600px;" border="0" cellspacing="2" cellpadding="0">
     <tr>
	<?php /* modification tableau pour ajout colonne classe et motif  didier   */ ?>
      <td colspan="7" class="titre_tableau_gestion" nowrap><b>Absence</b></td>
    </tr>
    <tr class="fond_vert">
<?/* Ajout d'une possibilité de tri sur les colonnes en mode afficher  didier*/ ?>
      <td rowspan="2" class="norme_absence_blanc"><?php if ($recherche == "afficher") { ?><a onClick="javascript:document.form1.tri.value='nom, prenom';document.form1.submit()">Nom Pr&eacute;nom</a><?php } else {echo 'Nom Pr&eacute;nom';}?></td>
	  <td rowspan="2" class="norme_absence_blanc"><?php if ($recherche == "afficher") { ?><a onClick="javascript:document.form1.tri.value='classe';document.form1.submit()">Classe</a><?php } else {echo 'Classe';}?></td>
      <td colspan="2" class="norme_absence_blanc"><?php if ($recherche == "afficher") { ?><a onClick="javascript:document.form1.tri.value='d_date_absence_eleve';document.form1.submit()">Date</a><?php } else {echo 'Date';}?></td>
      <td colspan="2" class="norme_absence_blanc"><?php if ($recherche == "afficher") { ?><a onClick="javascript:document.form1.tri.value='d_heure_absence_eleve';document.form1.submit()">Heure</a><?php } else {echo 'Heure';}?></td>
	  <td rowspan="2" class="norme_absence_blanc"><?php if ($recherche == "afficher") { ?><a onClick="javascript:document.form1.tri.value='motif_absence_eleve';document.form1.submit()">Motif</a><?php } else {echo 'Motif';}?></td>
    </tr>
    <tr class="fond_vert">
      <td class="norme_absence_blanc">Du</td>
      <td class="norme_absence_blanc">Au</td>
      <td class="norme_absence_blanc">Debut</td>
      <td class="norme_absence_blanc">Fin</td>

    </tr>
    <?php
      $init = "";
      $init_v = "";
      $ic = 1;
      $execution_recherche = mysql_query($requete_recherche) or die('Erreur SQL !'.$requete_recherche.'<br />'.mysql_error());
      while ( $data_recherche = mysql_fetch_array( $execution_recherche ) )
	  {
		if (in_array($data_recherche['eleve_absence_eleve'], $test_cpe) OR $test_nbre_eleves_cpe === 0) {

           if ($ic==1) {
				$ic=2;
				$couleur_cellule="td_tableau_absence_1";
			} else {
				$couleur_cellule="td_tableau_absence_2";
				$ic=1;
			}
    ?>
	<?php /*ajout des colonnes classe et motif */?>
    <tr class="<?php echo $couleur_cellule; ?>" onmouseover="showdiv(event, 'd<?php echo $data_recherche['id_absence_eleve']; ?>'); return true;" onmouseout="hidediv('d<?php echo $data_recherche['id_absence_eleve']; ?>'); return true;">
      <td class="norme_absence"><?php echo "<b>".strtoupper($data_recherche['nom'])."</b><br />".ucfirst($data_recherche['prenom']); ?></td>
      <td class="norme_absence centre"><?php echo $data_recherche['classe']; ?></td>
      <td class="norme_absence centre"><?php echo date_frc($data_recherche['d_date_absence_eleve']); ?></td>
      <td class="norme_absence centre"><?php if ($data_recherche['a_date_absence_eleve'] != "") { echo date_frc($data_recherche['a_date_absence_eleve']); } ?></td>
      <td class="norme_absence centre"><?php echo heure($data_recherche['d_heure_absence_eleve']); ?></td>
      <td class="norme_absence centre"><?php echo heure($data_recherche['a_heure_absence_eleve']); ?></td>
      <td class="norme_absence centre"><?php echo $data_recherche['motif_absence_eleve']; ?></td>
    </tr>
    <?php
		}
	} ?>
  </table>
<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>


<?php
}
if ($type == "R" or $type == "tous") { ?>
<?php
	$execution_div = mysql_query($requete_recherche) or die('Erreur SQL !'.$requete_recherche.'<br />'.mysql_error());
	while ( $data_div = mysql_fetch_array( $execution_div ) )
	{
		if (in_array($data_div['eleve_absence_eleve'], $test_cpe) OR $test_nbre_eleves_cpe === 0) {

 ?>
   <div id="d<?php echo $data_div['id_absence_eleve']; ?>" style="position: absolute; z-index: 20; visibility: hidden; top: 0px; left: 0px;">
		<table border="0" cellpadding="2" cellspacing="2" class="tableau_calque_information">
			<tr>
				<td class="texte_fondjaune_calque_information"><?php echo "<b>".strtoupper($data_div['nom'])."</b> ".ucfirst($data_div['prenom']); ?> élève de <?php echo "<b>".classe_de($data_div['login'])."</b>"; $id_classe_eleve = classe_de($data_div['login']); ?> est arrivé<?php if ($data_div['sexe'] == "F") { ?>e<?php } ?> en retard<br /> le <?php echo date_frl($data_div['d_date_absence_eleve']); ?><br /> à <?php if ($data_div['d_heure_absence_eleve'] == "") {} else { echo heure($data_div['d_heure_absence_eleve']);} ?></td>
                  <?php if (getSettingValue("active_module_trombinoscopes")=='y') {
                  $nom_photo = nom_photo($data_div['elenoet']);
                  $photo = $nom_photo;
                  if (($nom_photo == "") or (!(file_exists($photo)))) { $photo = "../../mod_trombinoscopes/images/trombivide.jpg"; }
		 $valeur=redimensionne_image($photo);
                  ?>
				<td style="width: 60px; vertical-align: top" rowspan="4"><img src="<?php echo $photo; ?>" style="width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px; border: 0px" alt="" title="" /></td><?php
                  } ?>
			</tr>
			<tr>
				<td class="norme_absence">Pour le motif : <?php echo motab($data_div['motif_absence_eleve']); ?></td>
			</tr>
			<tr>
				<td class="norme_absence"><?php if ($data_div['justify_absence_eleve'] == "O") {?><span class="norme_absence_vert"><b>a donn&eacute;e pour justification : </b>
			   <?php } elseif($data_div['justify_absence_eleve'] == "T") { ?><span class="norme_absence_vert" style="color: orange;"><b>a justifi&eacute; par t&eacute;l&eacute;phone </b>
			   																	<?php } else { ?><span class="norme_absence_rouge"><b>N'a pas donn&eacute; de justification</b>
			   <?php } ?></span></td>
			</tr>
			<tr>
				<td class="norme_absence"><?php if(!empty($data_div['info_justify_absence_eleve'])) { ?><blockquote><?php echo $data_div['info_justify_absence_eleve']; ?></blockquote><?php } ?></td>
			</tr>
			  <?php
			  // vérification de la page d'arrivée pour affichage telephone didier
			  if( $pagedarriver === 'gestion_absences') { ?>
			<tr class="texte_fondjaune_calque_information">
				<td colspan="2">
                <?php

				// gestion de l'affichage des numéro de téléphone didier
				$info_responsable = tel_responsable($data_div['ele_id']);

				$telephone = ''; $telephone_pers = ''; $telephone_prof = ''; $telephone_port = '';

				if ( !empty($info_responsable) )
				{
					// L'affichage du numéro de téléphone du responsable 1
					$nbre = count($info_responsable);
					for ($i = 0 ; $i < $nbre ; $i++){
						if ($info_responsable[$i]['resp_legal'] == '1') {

							$ident_resp = ' <span style="font-size: 0.8em;">(' . $info_responsable[$i]['nom'] . ' ' . $info_responsable[$i]['prenom'] . ' : resp n° ' . $info_responsable[$i]['resp_legal'] . ')</span>';
							if ( $info_responsable[$i]['tel_pers'] != '' ) {
								$telephone_pers = '<br />Pers. <strong>'.present_tel($info_responsable[$i]['tel_pers']).'</strong> ';
							}
							if ( $info_responsable[$i]['tel_prof'] != ''  ) {
								$telephone_prof = '<br />Prof. <strong>'.present_tel($info_responsable[$i]['tel_prof']).'</strong> ';
							}
							if ( $info_responsable[$i]['tel_port'] != ''  ) {
								$telephone_port = '<br />Port.<img src="../images/attention.png" alt="Attention numéro surtaxé" title="Attention numéro surtaxé" border="0" height="14" width="14" /> '.present_tel($info_responsable[$i]['tel_port']);
							}
						}
					}

				}

				if ( $telephone_pers != '' and $telephone_prof === '' ) { $telephone = $telephone_pers; }
				if ( $telephone_pers === '' and $telephone_prof != '' ) { $telephone = $telephone_prof; }
				if ( $telephone_pers != '' and $telephone_prof != '' ) { $telephone = $telephone_pers . ' ' . $telephone_prof; }
				if ( $telephone_pers === '' and $telephone_prof === '' and $telephone_port != '' ) { $telephone = $telephone_port . ' ! surtaxe'; }

				if ( $telephone_pers != '' or $telephone_prof != '' or $telephone_port != '' ) { $telephone = 'Téléphone responsable : '.$telephone; }
				else { $telephone = 'Aucun numéro de téléphone disponible'; }

				echo $telephone . $ident_resp . $telephone_port;

		  		?>
                </td>
              </tr>
			  <?php } ?>
        </table>
  </div>
<?php	}
	} ?>

<?php /* div de centrage du tableau pour ie5  correction oubli didier */?>
<div style="text-align:center">
  <table style="margin: auto; width: 600px;" border="0" cellspacing="2" cellpadding="0">
    <tr>
	<?php /* modification nombre colonnes didier */?>
      <td colspan="5" class="titre_tableau_gestion" nowrap><b>Retard</b></td>
    </tr>
    <tr class="fond_vert">
	<?php /* ajout possibilité de tri sur colonnes en mode afficher didier */?>
      <td class="norme_absence_blanc"><?php if ($recherche == "afficher") { ?><a onClick="javascript:document.form1.tri.value='nom, prenom';document.form1.submit()">Nom Pr&eacute;nom</a><?php } else {echo 'Nom Pr&eacute;nom';}?></td>
      <td class="norme_absence_blanc"><?php if ($recherche == "afficher") { ?><a onClick="javascript:document.form1.tri.value='classe';document.form1.submit()">Classe</a><?php } else {echo 'Le';}?></td>
	  <td class="norme_absence_blanc"><?php if ($recherche == "afficher") { ?><a onClick="javascript:document.form1.tri.value='d_date_absence_eleve';document.form1.submit()">Le</a><?php } else {echo 'Le';}?></td>
      <td class="norme_absence_blanc"><?php if ($recherche == "afficher") { ?><a onClick="javascript:document.form1.tri.value='d_heure_absence_eleve';document.form1.submit()">A</a><?php } else {echo 'A';}?></td>
      <td class="norme_absence_blanc"><?php if ($recherche == "afficher") { ?><a onClick="javascript:document.form1.tri.value='info_justify_absence_eleve';document.form1.submit()">Cause</a><?php } else {echo 'Cause';}?></td>
    </tr>
    <?php
    $init = "";
    $init_v = "";
    $ic = 1;
    $execution_recherche = mysql_query($requete_recherche) or die('Erreur SQL !'.$requete_recherche.'<br />'.mysql_error());
    while ( $data_recherche = mysql_fetch_array( $execution_recherche ) )
	{
		if (in_array($data_recherche['eleve_absence_eleve'], $test_cpe) OR $test_nbre_eleves_cpe === 0) {

			if ($ic==1) {
				$ic=2;
				$couleur_cellule="td_tableau_absence_1";
			} else {
				$couleur_cellule="td_tableau_absence_2";
				$ic=1;
			}
    ?>
    <tr class="<?php echo $couleur_cellule; ?>" onmouseover="window.status='Voir cette entrée'; showdiv(event, 'd<?php echo $data_recherche['id_absence_eleve']; ?>'); return true;" onmouseout="hidediv('d<?php echo $data_recherche['id_absence_eleve']; ?>'); return true;">
      <td class="norme_absence"><?php echo "<b>".strtoupper($data_recherche['nom'])."</b><br />".ucfirst($data_recherche['prenom']); ?></td>
      <?php /* ajout colonne classe didier */ ?>
	  <td class="norme_absence centre"><?php echo $data_recherche['classe']; ?></td>
      <td class="norme_absence centre"><?php echo date_frc($data_recherche['d_date_absence_eleve']); ?></td>
      <td class="norme_absence centre"><?php echo heure($data_recherche['d_heure_absence_eleve']); ?></td>
      <td class="norme_absence centre"><?php echo $data_recherche['info_justify_absence_eleve']; ?></td>
    </tr>
    <?php }
	} ?>
  </table>
<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>


<?php
}
if ($type == "D" or $type == "tous") { ?>
<?php
	$execution_div = mysql_query($requete_recherche) or die('Erreur SQL !'.$requete_recherche.'<br />'.mysql_error());
    while ( $data_div = mysql_fetch_array( $execution_div ) )
	{
		if (in_array($data_div['eleve_absence_eleve'], $test_cpe) OR $test_nbre_eleves_cpe === 0) {

?>
   <div id="d<?php echo $data_div['id_absence_eleve']; ?>" style="position: absolute; z-index: 20; visibility: hidden; top: 0px; left: 0px;">
       <table border="0" cellpadding="2" cellspacing="2" class="tableau_calque_information">
            <tr>
              <td class="texte_fondjaune_calque_information"><?php echo "<b>".strtoupper($data_div['nom'])."</b> ".ucfirst($data_div['prenom']); ?> élève de <?php echo "<b>".classe_de($data_div['login'])."</b>"; $id_classe_eleve = classe_de($data_div['login']); ?> est dispensé<?php if ($data_div['sexe'] == "F") { ?>e<?php } ?><br /> du <?php echo date_frl($data_div['d_date_absence_eleve']); ?> au <?php echo date_frl($data_div['a_date_absence_eleve']); ?><br />plus d'info : <?php echo $data_div['info_absence_eleve']; ?></td>
           </tr>
            <tr>
              <td class="norme_absence">Pour le motif : <?php echo motab($data_div['motif_absence_eleve']); ?></td>
            </tr>
            <tr>
              <td class="norme_absence"><?php if ($data_div['justify_absence_eleve'] != "O") {?><span class="norme_absence_rouge"><b>N'a pas donn&eacute; de justification</b><?php } else { ?><span class="norme_absence_vert"><b>a donn&eacute; pour justification : </b><?php } ?></span></td>
            </tr>
            <tr>
              <td class="norme_absence"><?php if(!empty($data_div['info_justify_absence_eleve'])) { ?><blockquote><?php echo $data_div['info_justify_absence_eleve']; ?></blockquote><?php } ?></td>
            </tr>
			 <?php
			 // vérification de la page d'arrivée pour affichage telephone didier
			 if( $pagedarriver === 'gestion_absences') { ?>
			<tr class="texte_fondjaune_calque_information">
                <td colspan="2">
                <?php

				// gestion de l'affichage des numéro de téléphone didier
				$info_responsable = tel_responsable($data_div['ele_id']);

				$telephone = ''; $telephone_pers = ''; $telephone_prof = ''; $telephone_port = '';

				if ( !empty($info_responsable) )
				{
					// L'affichage du numéro de téléphone du responsable 1
					$nbre = count($info_responsable);
					for ($i = 0 ; $i < $nbre ; $i++){
						if ($info_responsable[$i]['resp_legal'] == '1') {

							$ident_resp = ' <span style="font-size: 0.8em;">(' . $info_responsable[$i]['nom'] . ' ' . $info_responsable[$i]['prenom'] . ' : resp n° ' . $info_responsable[$i]['resp_legal'] . ')</span>';
							if ( $info_responsable[$i]['tel_pers'] != '' ) {
								$telephone_pers = '<br />Pers. <strong>'.present_tel($info_responsable[$i]['tel_pers']).'</strong> ';
							}
							if ( $info_responsable[$i]['tel_prof'] != ''  ) {
								$telephone_prof = '<br />Prof. <strong>'.present_tel($info_responsable[$i]['tel_prof']).'</strong> ';
							}
							if ( $info_responsable[$i]['tel_port'] != ''  ) {
								$telephone_port = '<br />Port.<img src="../images/attention.png" alt="Attention numéro surtaxé" title="Attention numéro surtaxé" border="0" height="14" width="14" /> '.present_tel($info_responsable[$i]['tel_port']);
							}
						}
					}

				}

				if ( $telephone_pers != '' and $telephone_prof === '' ) { $telephone = $telephone_pers; }
				if ( $telephone_pers === '' and $telephone_prof != '' ) { $telephone = $telephone_prof; }
				if ( $telephone_pers != '' and $telephone_prof != '' ) { $telephone = $telephone_pers . ' ' . $telephone_prof; }
				if ( $telephone_pers === '' and $telephone_prof === '' and $telephone_port != '' ) { $telephone = $telephone_port . ' ! surtaxe'; }

				if ( $telephone_pers != '' or $telephone_prof != '' or $telephone_port != '' ) { $telephone = 'Téléphone responsable : '.$telephone; }
				else { $telephone = 'Aucun numéro de téléphone disponible'; }

				echo $telephone . $ident_resp . $telephone_port;

		  		?>
                </td>
              </tr>
			  <?php } ?>
       </table>
  </div>
<?php 	}
	} ?>

<?php /* div de centrage du tableau pour ie5 */ ?>
<div style="text-align:center">
  <table style="margin: auto; width: 600px;" border="0" cellspacing="2" cellpadding="0">
    <tr>
	<?php /* modification nombre colonne pour ajout classe didier */ ?>
      <td colspan="5" class="titre_tableau_gestion" nowrap><b>Dispense</b></td>
    </tr>
    <tr class="fond_vert">
	<?php /* ajout possibilité de tri par colonne en mode afficher  et ajout colonne classe didier*/ ?>
      <td rowspan="2" class="norme_absence_blanc"><?php if ($recherche == "afficher") { ?><a onClick="javascript:document.form1.tri.value='nom, prenom';document.form1.submit()">Nom Pr&eacute;nom</a><?php } else {echo 'Nom Pr&eacute;nom';}?></td>
	  <td rowspan="2" class="norme_absence_blanc"><?php if ($recherche == "afficher") { ?><a onClick="javascript:document.form1.tri.value='classe';document.form1.submit()">Classe</a><?php } else {echo 'Classe';}?></td>
      <td colspan="2" class="norme_absence_blanc"><?php if ($recherche == "afficher") { ?><a onClick="javascript:document.form1.tri.value='d_date_absence_eleve';document.form1.submit()">Date</a><?php } else {echo 'Date';}?></td>
      <td rowspan="2" class="norme_absence_blanc"><?php if ($recherche == "afficher") { ?><a onClick="javascript:document.form1.tri.value='info_absence_eleve';document.form1.submit()">Horaire</a><?php } else {echo 'Horaire';}?></td>
    </tr>
    <tr class="fond_vert">
      <td class="norme_absence_blanc">Du</td>
      <td class="norme_absence_blanc">Au</td>
    </tr>
    <?php
	$init = "";
	$init_v = "";
	$ic = 1;
	$execution_recherche = mysql_query($requete_recherche) or die('Erreur SQL !'.$requete_recherche.'<br />'.mysql_error());
    while ( $data_recherche = mysql_fetch_array( $execution_recherche ) )
	{
		if (in_array($data_recherche['eleve_absence_eleve'], $test_cpe) OR $test_nbre_eleves_cpe === 0) {

			if ($ic==1) {
				$ic=2;
				$couleur_cellule="td_tableau_absence_1";
			} else {
				$couleur_cellule="td_tableau_absence_2";
				$ic=1;
			}

    ?>
    <tr class="<?php echo $couleur_cellule; ?>" onmouseover="window.status='Voir cette entrée'; showdiv(event, 'd<?php echo $data_recherche['id_absence_eleve']; ?>'); return true;" onmouseout="hidediv('d<?php echo $data_recherche['id_absence_eleve']; ?>'); return true;">
      <td class="norme_absence"><?php echo "<b>".strtoupper($data_recherche['nom'])."</b><br />".ucfirst($data_recherche['prenom']); ?><br /></td>
      <?php /* ajout colonne classe didier*/ ?>
	  <td class="norme_absence centre"><?php echo $data_recherche['classe']; ?></td>
      <td class="norme_absence centre"><?php echo date_frc($data_recherche['d_date_absence_eleve']); ?></td>
      <td class="norme_absence centre"><?php echo date_frc($data_recherche['a_date_absence_eleve']); ?></td>
      <td class="norme_absence centre"><?php echo $data_recherche['info_absence_eleve']; ?></td>
    </tr>
    <?php
    	}
	} ?>
  </table>
<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>

<?php
}
if ($type == "I" or $type == "tous") { ?>
<?php
      $execution_div = mysql_query($requete_recherche) or die('Erreur SQL !'.$requete_recherche.'<br />'.mysql_error());
    while ( $data_div = mysql_fetch_array( $execution_div ) )
	{
		if (in_array($data_div['eleve_absence_eleve'], $test_cpe) OR $test_nbre_eleves_cpe === 0) {
 ?>
   <div id="d<?php echo $data_div['id_absence_eleve']; ?>" style="position: absolute; z-index: 20; visibility: hidden; top: 0px; left: 0px;">
		<table border="0" cellpadding="2" cellspacing="2" class="tableau_calque_information">
			<tr>
				<td class="texte_fondjaune_calque_information"><?php echo "<b>".strtoupper($data_div['nom'])."</b> ".ucfirst($data_div['prenom']); ?> élève de <?php echo "<b>".classe_de($data_div['login'])."</b>"; $id_classe_eleve = classe_de($data_div['login']); ?> est allé<?php if ($data_div['sexe'] == "F") { ?>e<?php } ?> à l'infirmerie<br />le <?php echo date_frl($data_div['d_date_absence_eleve']); ?><br />de <?php echo heure($data_div['d_heure_absence_eleve']); ?> à <?php echo heure($data_div['a_heure_absence_eleve']); ?></td>

            <?php if (getSettingValue("active_module_trombinoscopes")=='y') {
				$nom_photo = nom_photo($data_div['elenoet']);
				$photo = "../../photos/eleves/".$nom_photo;
				if (($nom_photo == "") or (!(file_exists($photo)))) {
					$photo = "../../mod_trombinoscopes/images/trombivide.jpg";
				}
				$valeur = redimensionne_image($photo);

			?>
				<td style="width: 60px; vertical-align: top" rowspan="4"><img src="<?php echo $photo; ?>" style="width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px; border: 0px" alt="" title="" /></td><?php
                  } ?>
            </tr>
            <tr>
				<td class="norme_absence">Pour le motif : <?php echo motab($data_div['motif_absence_eleve']); ?></td>
            </tr>
            <tr>
				<td class="norme_absence"><?php if(!empty($data_div['info_justify_absence_eleve'])) { ?><blockquote><?php echo $data_div['info_justify_absence_eleve']; ?></blockquote><?php } ?></td>
			</tr>
			<?php
             // vérification de la page d'arrivée pour affichage telephone didier
			  if( $pagedarriver === 'gestion_absences') { ?>

            <tr class="texte_fondjaune_calque_information">
                <td colspan="2">
            <?php

				// gestion de l'affichage des numéro de téléphone
				$info_responsable = tel_responsable($data_div['ele_id']);

				$telephone = ''; $telephone_pers = ''; $telephone_prof = ''; $telephone_port = '';

				if ( !empty($info_responsable) )
				{
					// L'affichage du numéro de téléphone du responsable 1
					$nbre = count($info_responsable);
					for ($i = 0 ; $i < $nbre ; $i++){
						if ($info_responsable[$i]['resp_legal'] == '1') {

							$ident_resp = ' <span style="font-size: 0.8em;">(' . $info_responsable[$i]['nom'] . ' ' . $info_responsable[$i]['prenom'] . ' : resp n° ' . $info_responsable[$i]['resp_legal'] . ')</span>';
							if ( $info_responsable[$i]['tel_pers'] != '' ) {
								$telephone_pers = '<br />Pers. <strong>'.present_tel($info_responsable[$i]['tel_pers']).'</strong> ';
							}
							if ( $info_responsable[$i]['tel_prof'] != ''  ) {
								$telephone_prof = '<br />Prof. <strong>'.present_tel($info_responsable[$i]['tel_prof']).'</strong> ';
							}
							if ( $info_responsable[$i]['tel_port'] != ''  ) {
								$telephone_port = '<br />Port.<img src="../images/attention.png" alt="Attention numéro surtaxé" title="Attention numéro surtaxé" border="0" height="14" width="14" /> '.present_tel($info_responsable[$i]['tel_port']);
							}
						}
					}

				}

				if ( $telephone_pers != '' and $telephone_prof === '' ) { $telephone = $telephone_pers; }
				if ( $telephone_pers === '' and $telephone_prof != '' ) { $telephone = $telephone_prof; }
				if ( $telephone_pers != '' and $telephone_prof != '' ) { $telephone = $telephone_pers . ' ' . $telephone_prof; }
				if ( $telephone_pers === '' and $telephone_prof === '' and $telephone_port != '' ) { $telephone = $telephone_port . ' ! surtaxe'; }

				if ( $telephone_pers != '' or $telephone_prof != '' or $telephone_port != '' ) { $telephone = 'Téléphone responsable : '.$telephone; }
				else { $telephone = 'Aucun numéro de téléphone disponible'; }

				echo $telephone . $ident_resp . $telephone_port;

		  		?>
				</td>
			</tr>
			  <?php } ?>
		</table>
	</div>
<?php	}
	} ?>

<?php /* div de centrage du tableau pour ie5 */ ?>
<div style="text-align:center">
	<table style="margin: auto; width: 600px;" border="0" cellspacing="2" cellpadding="0">
		<tr>
		<?php /* modification nombre colonnes didier */ ?>
      <td colspan="5" class="titre_tableau_gestion" nowrap><b>Infirmerie</b></td>
    </tr>
    <tr class="fond_vert">
	<?php /* ajout du tri par colonne en mode afficher didier */ ?>
      <td class="norme_absence_blanc centre"><?php if ($recherche == "afficher") { ?><a onClick="javascript:document.form1.tri.value='nom, prenom';document.form1.submit()">Nom Pr&eacute;nom</a><?php } else {echo 'Nom Pr&eacute;nom';}?></td>
	  <td class="norme_absence_blanc centre"><?php if ($recherche == "afficher") { ?><a onClick="javascript:document.form1.tri.value='classe';document.form1.submit()">Classe</a><?php } else {echo 'Classe';}?></td>
      <td class="norme_absence_blanc centre"><?php if ($recherche == "afficher") { ?><a onClick="javascript:document.form1.tri.value='d_date_absence_eleve';document.form1.submit()">Date</a><?php } else {echo 'Date';}?></td>
      <td class="norme_absence_blanc centre"><?php if ($recherche == "afficher") { ?><a onClick="javascript:document.form1.tri.value='d_heure_absence_eleve';document.form1.submit()">De</a><?php } else {echo 'De';}?></td>
      <td class="norme_absence_blanc centre"><?php if ($recherche == "afficher") { ?><a onClick="javascript:document.form1.tri.value='a_heure_absence_eleve';document.form1.submit()">A</a><?php } else {echo 'A';}?></td>
    </tr>
	<?php
	$init = "";
	$init_v = "";
	$ic = 1;
	$execution_recherche = mysql_query($requete_recherche) or die('Erreur SQL !'.$requete_recherche.'<br />'.mysql_error());
    while ( $data_recherche = mysql_fetch_array( $execution_recherche ) )
	{
		if (in_array($data_recherche['eleve_absence_eleve'], $test_cpe) OR $test_nbre_eleves_cpe === 0) {

			if ($ic==1) {
				$ic=2;
				$couleur_cellule="td_tableau_absence_1";
			} else {
				$couleur_cellule="td_tableau_absence_2";
				$ic=1;
			}
    ?>
		<tr class="<?php echo $couleur_cellule; ?>" onmouseover="window.status='Voir cette entrée'; showdiv(event, 'd<?php echo $data_recherche['id_absence_eleve']; ?>'); return true;" onmouseout="hidediv('d<?php echo $data_recherche['id_absence_eleve']; ?>'); return true;">
			<td class="norme_absence"><?php echo "<b>".strtoupper($data_recherche['nom'])."</b><br />".ucfirst($data_recherche['prenom']); ?></td>
            <?php /* ajout colonne classe didier */ ?>
			<td class="norme_absence centre"><?php echo $data_recherche['classe']; ?></td>
			<td class="norme_absence centre"><?php echo date_frc($data_recherche['d_date_absence_eleve']); ?></td>
			<td class="norme_absence centre"><?php echo heure($data_recherche['d_heure_absence_eleve']); ?></td>
			<td class="norme_absence centre"><?php echo heure($data_recherche['a_heure_absence_eleve']); ?></td>
		</tr>
    <?php
    	}
	} ?>
  </table>
<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php } ?>
<p><br /></p>
<p><br /></p>
<p><br /></p>

<?php require("../../lib/footer.inc.php"); ?>
