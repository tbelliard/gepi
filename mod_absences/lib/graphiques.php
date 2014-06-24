<?php
/*
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

//**************** EN-TETE *****************
$titre_page = "Gestion des absences";
require_once("../../lib/header.inc.php");
//**************** FIN EN-TETE *****************

        // voir numero d'erreur = 2047 toutes les erreurs
        //echo(error_reporting());
//Configuration du calendrier
include("../../lib/calendrier/calendrier.class.php");
$cal_1 = new Calendrier("form1", "du");
$cal_2 = new Calendrier("form1", "au");

//mes fonctions
include("../lib/functions.php");

    $date_ce_jour = date('d/m/Y');

//VARIABLE
    if (empty($_GET['submit2']) AND empty($_POST['submit2'])) {$submit2="";}
      else { if (isset($_GET['submit2'])) {$submit2=$_GET['submit2'];} if (isset($_POST['submit2'])) {$submit2=$_POST['submit2'];} }
    if (empty($_GET['type']) AND empty($_POST['type'])) {$type="A";}
      else { if (isset($_GET['type'])) {$type=$_GET['type'];} if (isset($_POST['type'])) {$type=$_POST['type'];} }
    if (empty($_GET['justifie']) AND empty($_POST['justifie'])) {$justifie="";}
      else { if (isset($_GET['justifie'])) {$justifie=$_GET['justifie'];} if (isset($_POST['justifie'])) {$justifie=$_POST['justifie'];} }
    if (empty($_GET['nonjustifie']) AND empty($_POST['nonjustifie'])) {$nonjustifie="";}
      else { if (isset($_GET['nonjustifie'])) {$nonjustifie=$_GET['nonjustifie'];} if (isset($_POST['nonjustifie'])) {$nonjustifie=$_POST['nonjustifie'];} }
    if (empty($_GET['motif']) AND empty($_POST['motif'])) {$motif="";}
      else { if (isset($_GET['motif'])) {$motif=$_GET['motif'];} if (isset($_POST['motif'])) {$motif=$_POST['motif'];} }
    if (empty($_GET['classe_choix']) AND empty($_POST['classe_choix'])) {$classe_choix="";}
      else { if (isset($_GET['classe_choix'])) {$classe_choix=$_GET['classe_choix'];} if (isset($_POST['classe_choix'])) {$classe_choix=$_POST['classe_choix'];} }
    if (empty($_GET['eleve_choix']) AND empty($_POST['eleve_choix'])) {$eleve_choix="";}
      else { if (isset($_GET['eleve_choix'])) {$eleve_choix=$_GET['eleve_choix'];} if (isset($_POST['eleve_choix'])) {$eleve_choix=$_POST['eleve_choix'];} }
    if (empty($_GET['du']) AND empty($_POST['du'])) {$du="";}
      else { if (isset($_GET['du'])) {$du=$_GET['du'];} if (isset($_POST['du'])) {$du=$_POST['du'];} }
    if (empty($_GET['au']) AND empty($_POST['au'])) {$au="";}
      else { if (isset($_GET['au'])) {$au=$_GET['au'];} if (isset($_POST['au'])) {$au=$_POST['au'];} }
    if (empty($_GET['recherche']) AND empty($_POST['recherche'])) {$recherche="";}
      else { if (isset($_GET['recherche'])) {$recherche=$_GET['recherche'];} if (isset($_POST['recherche'])) {$recherche=$_POST['recherche'];} }

      if ($type == "A" and $submit2 == "") {$type="A"; $justifie="1"; $nonjustifie="1"; $motif="tous"; $classe_choix="tous"; $eleve_choix="tous"; $du="$date_ce_jour"; $au="jj/mm/aaaa";}
      if ($type == "D" and $submit2 == "") {$type="D"; $justifie="1"; $nonjustifie="1"; $motif="tous"; $classe_choix="tous"; $eleve_choix="tous"; $du="$date_ce_jour"; $au="jj/mm/aaaa";}
      if ($type == "R" and $submit2 == "") {$type="R"; $justifie="1"; $nonjustifie="1"; $motif="tous"; $classe_choix="tous"; $eleve_choix="tous"; $du="$date_ce_jour"; $au="jj/mm/aaaa";}
      if ($type == "I" and $submit2 == "") {$type="I"; $justifie="1"; $nonjustifie="1"; $motif="tous"; $classe_choix="tous"; $eleve_choix="tous"; $du="$date_ce_jour"; $au="jj/mm/aaaa";}
      if ($du == "jj/mm/aaaa" OR $du == "") {$du = $date_ce_jour; }
      if ($au == "jj/mm/aaaa" OR $au == "") {$au = $du; }

//Quelle que variabale
  $datej = date('Y-m-d');
  $annee_scolaire=annee_en_cours_t($datej);

//REQUETE

//requête pour liste les motif d'absence
$requete_liste_motif = "SELECT init_motif_absence, def_motif_absence FROM ".$prefix_base."absences_motifs ORDER BY init_motif_absence ASC";

//requete sur les champs des tableaus
if ($motif == "tous" AND $classe_choix == "tous" AND $eleve_choix == "tous" AND $justifie == "1" AND $nonjustifie == "1")
   {
      $requete_recherche = "SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves WHERE type_absence_eleve = '".$type."' AND eleve_absence_eleve = login AND ((d_date_absence_eleve >= '".date_sql($du)."' AND d_date_absence_eleve <= '".date_sql($au)."') OR (a_date_absence_eleve >= '".date_sql($du)."' AND a_date_absence_eleve <= '".date_sql($au)."')) ORDER BY nom, prenom ASC";
   }

if ($motif == "tous" AND $classe_choix == "tous" AND $eleve_choix == "tous" AND $justifie == "1" AND $nonjustifie != "1")
   {
      $requete_recherche = "SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves WHERE type_absence_eleve = '".$type."' AND eleve_absence_eleve = login AND ((d_date_absence_eleve >= '".date_sql($du)."' AND d_date_absence_eleve <= '".date_sql($au)."') OR (a_date_absence_eleve >= '".date_sql($du)."' AND a_date_absence_eleve <= '".date_sql($au)."')) AND justify_absence_eleve = 'O' ORDER BY nom, prenom ASC";
   }

if ($motif == "tous" AND $classe_choix == "tous" AND $eleve_choix == "tous" AND $justifie != "1" AND $nonjustifie == "1")
   {
      $requete_recherche = "SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves WHERE type_absence_eleve = '".$type."' AND eleve_absence_eleve = login AND ((d_date_absence_eleve >= '".date_sql($du)."' AND d_date_absence_eleve <= '".date_sql($au)."') OR (a_date_absence_eleve >= '".date_sql($du)."' AND a_date_absence_eleve <= '".date_sql($au)."')) AND justify_absence_eleve != 'O' ORDER BY nom, prenom ASC";
   }

//Spécifie le motif
if ($motif != "tous" AND $classe_choix == "tous" AND $eleve_choix == "tous" AND $justifie == "1" AND $nonjustifie == "1")
   {
      $requete_recherche = "SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves WHERE type_absence_eleve = '".$type."' AND eleve_absence_eleve = login AND ((d_date_absence_eleve >= '".date_sql($du)."' AND d_date_absence_eleve <= '".date_sql($au)."') OR (a_date_absence_eleve >= '".date_sql($du)."' AND a_date_absence_eleve <= '".date_sql($au)."'))  AND motif_absence_eleve = '".$motif."' ORDER BY nom, prenom ASC";
   }

if ($motif != "tous" AND $classe_choix == "tous" AND $eleve_choix == "tous" AND $justifie == "1" AND $nonjustifie != "1")
   {
      $requete_recherche = "SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves WHERE type_absence_eleve = '".$type."' AND eleve_absence_eleve = login AND ((d_date_absence_eleve >= '".date_sql($du)."' AND d_date_absence_eleve <= '".date_sql($au)."') OR (a_date_absence_eleve >= '".date_sql($du)."' AND a_date_absence_eleve <= '".date_sql($au)."')) AND justify_absence_eleve = 'O' AND motif_absence_eleve = '".$motif."' ORDER BY nom, prenom ASC";
   }

if ($motif != "tous" AND $classe_choix == "tous" AND $eleve_choix == "tous" AND $justifie != "1" AND $nonjustifie == "1")
   {
      $requete_recherche = "SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves WHERE type_absence_eleve = '".$type."' AND eleve_absence_eleve = login AND ((d_date_absence_eleve >= '".date_sql($du)."' AND d_date_absence_eleve <= '".date_sql($au)."') OR (a_date_absence_eleve >= '".date_sql($du)."' AND a_date_absence_eleve <= '".date_sql($au)."')) AND justify_absence_eleve != 'O' AND motif_absence_eleve = '".$motif."' ORDER BY nom, prenom ASC";
   }

//avec spécification des classes
if ($motif == "tous" AND $classe_choix != "tous" AND $eleve_choix == "tous" AND $justifie == "1" AND $nonjustifie == "1")
   {
      $requete_recherche = "SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes WHERE type_absence_eleve = '".$type."' AND eleve_absence_eleve = ".$prefix_base."eleves.login AND ((d_date_absence_eleve >= '".date_sql($du)."' AND d_date_absence_eleve <= '".date_sql($au)."') OR (a_date_absence_eleve >= '".date_sql($du)."' AND a_date_absence_eleve <= '".date_sql($au)."')) AND ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id AND ".$prefix_base."classes.id='".$classe_choix."' GROUP BY ".$prefix_base."absences_eleves.id_absence_eleve ORDER BY nom, prenom ASC";
   }

if ($motif == "tous" AND $classe_choix != "tous" AND $eleve_choix == "tous" AND $justifie == "1" AND $nonjustifie != "1")
   {
      $requete_recherche = "SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes WHERE type_absence_eleve = '".$type."' AND eleve_absence_eleve = ".$prefix_base."eleves.login AND ((d_date_absence_eleve >= '".date_sql($du)."' AND d_date_absence_eleve <= '".date_sql($au)."') OR (a_date_absence_eleve >= '".date_sql($du)."' AND a_date_absence_eleve <= '".date_sql($au)."')) AND justify_absence_eleve = 'O' AND ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id AND ".$prefix_base."classes.id='".$classe_choix."' GROUP BY ".$prefix_base."absences_eleves.id_absence_eleve ORDER BY nom, prenom ASC";
   }

if ($motif == "tous" AND $classe_choix != "tous" AND $eleve_choix == "tous" AND $justifie != "1" AND $nonjustifie == "1")
   {
      $requete_recherche = "SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes WHERE type_absence_eleve = '".$type."' AND eleve_absence_eleve = ".$prefix_base."eleves.login AND ((d_date_absence_eleve >= '".date_sql($du)."' AND d_date_absence_eleve <= '".date_sql($au)."') OR (a_date_absence_eleve >= '".date_sql($du)."' AND a_date_absence_eleve <= '".date_sql($au)."')) AND justify_absence_eleve != 'O' AND ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id AND ".$prefix_base."classes.id='".$classe_choix."' GROUP BY ".$prefix_base."absences_eleves.id_absence_eleve ORDER BY nom, prenom ASC";
   }

//Spécifie le motif
if ($motif != "tous" AND $classe_choix != "tous" AND $eleve_choix == "tous" AND $justifie == "1" AND $nonjustifie == "1")
   {
      $requete_recherche = "SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes WHERE type_absence_eleve = '".$type."' AND eleve_absence_eleve = ".$prefix_base."eleves.login AND ((d_date_absence_eleve >= '".date_sql($du)."' AND d_date_absence_eleve <= '".date_sql($au)."') OR (a_date_absence_eleve >= '".date_sql($du)."' AND a_date_absence_eleve <= '".date_sql($au)."'))  AND motif_absence_eleve = '".$motif."' AND ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id AND ".$prefix_base."classes.id='".$classe_choix."' GROUP BY ".$prefix_base."absences_eleves.id_absence_eleve ORDER BY nom, prenom ASC";
   }

if ($motif != "tous" AND $classe_choix != "tous" AND $eleve_choix == "tous" AND $justifie == "1" AND $nonjustifie != "1")
   {
      $requete_recherche = "SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes WHERE type_absence_eleve = '".$type."' AND eleve_absence_eleve = ".$prefix_base."eleves.login AND ((d_date_absence_eleve >= '".date_sql($du)."' AND d_date_absence_eleve <= '".date_sql($au)."') OR (a_date_absence_eleve >= '".date_sql($du)."' AND a_date_absence_eleve <= '".date_sql($au)."')) AND justify_absence_eleve = 'O' AND motif_absence_eleve = '".$motif."' AND ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id AND ".$prefix_base."classes.id='".$classe_choix."' GROUP BY ".$prefix_base."absences_eleves.id_absence_eleve ORDER BY nom, prenom ASC";
   }

if ($motif != "tous" AND $classe_choix != "tous" AND $eleve_choix == "tous" AND $justifie != "1" AND $nonjustifie == "1")
   {
      $requete_recherche = "SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes WHERE type_absence_eleve = '".$type."' AND eleve_absence_eleve = ".$prefix_base."eleves.login AND ((d_date_absence_eleve >= '".date_sql($du)."' AND d_date_absence_eleve <= '".date_sql($au)."') OR (a_date_absence_eleve >= '".date_sql($du)."' AND a_date_absence_eleve <= '".date_sql($au)."')) AND justify_absence_eleve != 'O' AND motif_absence_eleve = '".$motif."' AND ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id AND ".$prefix_base."classes.id='".$classe_choix."' GROUP BY ".$prefix_base."absences_eleves.id_absence_eleve ORDER BY nom, prenom ASC";
   }

//avec spécification des eleves
if ($motif == "tous" AND $classe_choix != "tous" AND $eleve_choix != "tous" AND $justifie == "1" AND $nonjustifie == "1")
   {
      $requete_recherche = "SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes WHERE type_absence_eleve = '".$type."' AND eleve_absence_eleve = ".$prefix_base."eleves.login AND ((d_date_absence_eleve >= '".date_sql($du)."' AND d_date_absence_eleve <= '".date_sql($au)."') OR (a_date_absence_eleve >= '".date_sql($du)."' AND a_date_absence_eleve <= '".date_sql($au)."')) AND ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login AND ".$prefix_base."eleves.login='".$eleve_choix."' AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id AND ".$prefix_base."classes.id='".$classe_choix."' GROUP BY ".$prefix_base."absences_eleves.id_absence_eleve ORDER BY nom, prenom ASC";
   }

if ($motif == "tous" AND $classe_choix != "tous" AND $eleve_choix != "tous" AND $justifie == "1" AND $nonjustifie != "1")
   {
      $requete_recherche = "SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes WHERE type_absence_eleve = '".$type."' AND eleve_absence_eleve = ".$prefix_base."eleves.login AND ((d_date_absence_eleve >= '".date_sql($du)."' AND d_date_absence_eleve <= '".date_sql($au)."') OR (a_date_absence_eleve >= '".date_sql($du)."' AND a_date_absence_eleve <= '".date_sql($au)."')) AND justify_absence_eleve = 'O' AND ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login AND ".$prefix_base."eleves.login='".$eleve_choix."' AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id AND ".$prefix_base."classes.id='".$classe_choix."' GROUP BY ".$prefix_base."absences_eleves.id_absence_eleve ORDER BY nom, prenom ASC";
   }

if ($motif == "tous" AND $classe_choix != "tous" AND $eleve_choix != "tous" AND $justifie != "1" AND $nonjustifie == "1")
   {
      $requete_recherche = "SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes WHERE type_absence_eleve = '".$type."' AND eleve_absence_eleve = ".$prefix_base."eleves.login AND ((d_date_absence_eleve >= '".date_sql($du)."' AND d_date_absence_eleve <= '".date_sql($au)."') OR (a_date_absence_eleve >= '".date_sql($du)."' AND a_date_absence_eleve <= '".date_sql($au)."')) AND justify_absence_eleve != 'O' AND ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login AND ".$prefix_base."eleves.login='".$eleve_choix."' AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id AND ".$prefix_base."classes.id='".$classe_choix."' GROUP BY ".$prefix_base."absences_eleves.id_absence_eleve ORDER BY nom, prenom ASC";
   }

//Spécifie le motif
if ($motif != "tous" AND $classe_choix != "tous" AND $eleve_choix != "tous" AND $justifie == "1" AND $nonjustifie == "1")
   {
      $requete_recherche = "SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes WHERE type_absence_eleve = '".$type."' AND eleve_absence_eleve = ".$prefix_base."eleves.login AND ((d_date_absence_eleve >= '".date_sql($du)."' AND d_date_absence_eleve <= '".date_sql($au)."') OR (a_date_absence_eleve >= '".date_sql($du)."' AND a_date_absence_eleve <= '".date_sql($au)."'))  AND motif_absence_eleve = '".$motif."' AND ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login AND ".$prefix_base."eleves.login='".$eleve_choix."' AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id AND ".$prefix_base."classes.id='".$classe_choix."' GROUP BY ".$prefix_base."absences_eleves.id_absence_eleve ORDER BY nom, prenom ASC";
   }

if ($motif != "tous" AND $classe_choix != "tous" AND $eleve_choix != "tous" AND $justifie == "1" AND $nonjustifie != "1")
   {
      $requete_recherche = "SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes WHERE type_absence_eleve = '".$type."' AND eleve_absence_eleve = ".$prefix_base."eleves.login AND ((d_date_absence_eleve >= '".date_sql($du)."' AND d_date_absence_eleve <= '".date_sql($au)."') OR (a_date_absence_eleve >= '".date_sql($du)."' AND a_date_absence_eleve <= '".date_sql($au)."')) AND justify_absence_eleve = 'O' AND motif_absence_eleve = '".$motif."' AND ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login AND ".$prefix_base."eleves.login='".$eleve_choix."' AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id AND ".$prefix_base."classes.id='".$classe_choix."' GROUP BY ".$prefix_base."absences_eleves.id_absence_eleve ORDER BY nom, prenom ASC";
   }

if ($motif != "tous" AND $classe_choix != "tous" AND $eleve_choix != "tous" AND $justifie != "1" AND $nonjustifie == "1")
   {
      $requete_recherche = "SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes WHERE type_absence_eleve = '".$type."' AND eleve_absence_eleve = ".$prefix_base."eleves.login AND ((d_date_absence_eleve >= '".date_sql($du)."' AND d_date_absence_eleve <= '".date_sql($au)."') OR (a_date_absence_eleve >= '".date_sql($du)."' AND a_date_absence_eleve <= '".date_sql($au)."')) AND justify_absence_eleve != 'O' AND motif_absence_eleve = '".$motif."' AND ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login AND ".$prefix_base."eleves.login='".$eleve_choix."' AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id AND ".$prefix_base."classes.id='".$classe_choix."' GROUP BY ".$prefix_base."absences_eleves.id_absence_eleve ORDER BY nom, prenom ASC";
   }


if ($recherche == "afficher")
{
  if ($classe_choix == "tous") { $requete_liste_eleve = "SELECT login, nom, prenom FROM ".$prefix_base."eleves ORDER BY nom, prenom ASC";
  } else { 
		//$requete_liste_eleve = "SELECT ".$prefix_base."eleves.login, ".$prefix_base."eleves.nom, ".$prefix_base."eleves.prenom, ".$prefix_base."j_eleves_classes.login, ".$prefix_base."j_eleves_classes.id_classe, ".$prefix_base."j_eleves_classes.periode, ".$prefix_base."classes.id, ".$prefix_base."classes.classe, ".$prefix_base."classes.nom_complet FROM ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes WHERE ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id AND ".$prefix_base."classes.id='".$classe_choix."' GROUP BY ".$prefix_base."eleves.nom, ".$prefix_base."eleves.prenom ORDER BY nom, prenom ASC"; 
		$requete_liste_eleve = "SELECT ".$prefix_base."eleves.login, ".$prefix_base."eleves.nom, ".$prefix_base."eleves.prenom, ".$prefix_base."j_eleves_classes.login, ".$prefix_base."j_eleves_classes.id_classe, ".$prefix_base."j_eleves_classes.periode, ".$prefix_base."classes.id, ".$prefix_base."classes.classe, ".$prefix_base."classes.nom_complet FROM ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes WHERE ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id AND ".$prefix_base."classes.id='".$classe_choix."' GROUP BY ".$prefix_base."eleves.login, ".$prefix_base."eleves.nom, ".$prefix_base."eleves.prenom ORDER BY nom, prenom ASC"; // 20100430
	}
    $requete_liste_classe = "SELECT id, classe, nom_complet FROM ".$prefix_base."classes ORDER BY nom_complet DESC";
}

?>
<p class=bold>|<a href='../gestion/gestion_absences.php'>Retour</a>|
</p>
<?php /* div de centrage du tableau pour ie5 */ ?>
<div style="text-align:center">
    <form method="post" action="" name="form1">
      <fieldset style="width: 400px; margin: auto;">
        <legend class="legend_texte">&nbsp;Recherche&nbsp;</legend>
          <div style="text-align:left">[ <?php if($recherche=="cacher" or $recherche=="") { ?><a href="graphiques.php?recherche=afficher">afficher</a><?php } if($recherche=="afficher") { ?><a href="graphiques.php?recherche=cacher">cacher</a><?php } ?> ]<br />
        <?php if ($recherche == "afficher") { ?>
             Type
               <select name="type" id="type">
                  <option value="A" <?php if ($type == "A") {?>selected<?php } ?>>Absence</option>
                  <option value="R" <?php if ($type == "R") {?>selected<?php } ?>>Retard</option>
                  <option value="D" <?php if ($type == "D") {?>selected<?php } ?>>Dispense</option>
                  <option value="I" <?php if ($type == "I") {?>selected<?php } ?>>Infirmerie</option>
               </select><br />
           Classe
              <select name="classe_choix" id="classe_choix">
                 <option value="tous" <?php if (empty($classe_choix)) {?>selected<?php } ?>>tous</option>
                    <?php
                    $resultat_liste_classe = mysqli_query($GLOBALS["mysqli"], $requete_liste_classe) or die('Erreur SQL !'.$requete_liste_classe.'<br />'.mysqli_error($GLOBALS["mysqli"]));
                    While ( $data_liste_classe = mysqli_fetch_array($resultat_liste_classe)) {
                           if ($classe_choix==$data_liste_classe['id']) {$selected = "selected"; } else {$selected = ""; }?>
                          <option value="<?php echo $data_liste_classe['id']; ?>" <?php echo $selected; ?>><?php echo $data_liste_classe['nom_complet']; ?></option>
                    <?php } ?>
             </select><br />
          <?php if($classe_choix != "tous") { ?>
          Elève
            <select name="eleve_choix" id="eleve_choix">
                <option value="tous" <?php if (empty($eleve_choix)) {?>selected<?php } ?>>tous</option>
                    <?php
                    $resultat_liste_eleve = mysqli_query($GLOBALS["mysqli"], $requete_liste_eleve) or die('Erreur SQL !'.$requete_liste_eleve.'<br />'.mysqli_error($GLOBALS["mysqli"]));
                    While ( $data_liste_eleve = mysqli_fetch_array($resultat_liste_eleve)) {
                          if ($eleve_choix==$data_liste_eleve['login']) {$selected = "selected"; } else {$selected = ""; }?>
                          <option value="<?php echo $data_liste_eleve['login']; ?>" <?php echo $selected; ?>><?php echo strtoupper($data_liste_eleve['nom'])." ".ucfirst($data_liste_eleve['prenom']); ?></option>
                    <?php } ?>
                <option>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
            </select>
          <?php } else { ?><input type="hidden" name="eleve_choix" value="tous" /><?php } ?><br />
          <input type="submit" name="submit2" value="Valider" /><br />
    <?php } ?>
    </div>
  </fieldset>
</form>
<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php /* <div class="centre"><img src="view_artichow_absences.php?type_1=<?php echo $type; ?>&amp;classe_1=<?php echo $classe_choix; ?>&amp;eleve_1=<?php echo $eleve_choix; ?>" title="graphique" alt="graphique" /></div> */?>
<div class="centre"><img src="view_artichow_absences.php?type_1=<?php echo $type; ?>&amp;classe_1=<?php echo $classe_choix; ?>&amp;eleve_1=<?php echo $eleve_choix; ?>" /></div>
<div class="centre"><img src="graph_camembert.php" /></div>
<div class="centre"><img src="graph_ligne.php" /></div>

<a href="graph_ligne.php">aaaa</a>
