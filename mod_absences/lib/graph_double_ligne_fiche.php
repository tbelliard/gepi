<?php
/*
* $Id: graph_double_ligne_fiche.php 2147 2008-07-23 09:01:04Z tbelliard $
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
require_once "../../artichow/Pie.class.php";

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

require_once "../../artichow/LinePlot.class.php";

// Mes fonctions
//	include("functions.php");

// Variable prédéfinit
date_default_timezone_set ('Europe/Paris');
//	date_default_timezone_set('Europe/Paris');
/*		if ( function_exists('date_default_timezone_get') ) {
			date_default_timezone_set('UTC');
			date_default_timezone_get();
		} else {
				localtime();
			}*/
	$date_act = date('Y-m-d');

// Variable non d�finit
	if (empty($_GET['donnee_titre']) and empty($_POST['donnee_titre'])) { $donnee_titre = ''; }
	   else { if (isset($_GET['donnee_titre'])) { $donnee_titre = $_GET['donnee_titre']; } if (isset($_POST['donnee_titre'])) { $donnee_titre = $_POST['donnee_titre']; } }
	if (empty($_GET['echelle_x']) and empty($_POST['echelle_x'])) { $echelle_x = ''; }
	   else { if (isset($_GET['echelle_x'])) { $echelle_x = $_GET['echelle_x']; } if (isset($_POST['echelle_x'])) { $echelle_x = $_POST['echelle_x']; } }
	if (empty($_GET['echelle_y']) and empty($_POST['echelle_y'])) { $echelle_y = ''; }
	   else { if (isset($_GET['echelle_y'])) { $echelle_y = $_GET['echelle_y']; } if (isset($_POST['echelle_y'])) { $echelle_y = $_POST['echelle_y']; } }
	if (empty($_GET['donnee_label']) and empty($_POST['donnee_label'])) { $donnee_label = ''; }
	   else { if (isset($_GET['donnee_label'])) { $donnee_label = $_GET['donnee_label']; } if (isset($_POST['donnee_label'])) { $donnee_label = $_POST['donnee_label']; } }

// LE GRAPHIQUE

  $days = array(
      'Lundi',
      'Mardi',
      'Mercredi',
      'Jeudi',
      'Vendredi',
      'Samedi',
      'Dimanche'
   );

//$graph = new Graph(388, 200);
$graph = new Graph(370, 200);

   // définition des couleur bleu et rouge
   $bleu = new Color(0, 0, 200);
   $rouge = new Color(200, 0, 0);

   $group = new PlotGroup;
   // padding du graphique
   $group->setPadding(40, 40);
   // décalement du 0 et de la fin
//   $group->setSpace(4, 4, 10, 0);
   // couleur de fond du graphique
   $group->setBackgroundColor(
      new Color(255, 255, 255)
   );

//   $values_absences = array(12, 5, 20, 32, 15, 4, 12, 5, 20, 32, 15, 4);
	$values_absences = $_SESSION['axe_y_abs'];
//   $values_retards = array(1, 0, 0, 2, 0, 0, 0, 0, 0, 2, 0, 0);
	$values_retards = $_SESSION['axe_y_ret'];
//   $x = array('jan.', 'fev.', 'mar.', 'avr.', 'mai', 'jui.', 'juil.', 'aou.', 'sep.', 'oct.', 'nov.', 'dec.');
	$x = $_SESSION['axe_x'];


   // les absences
   $plot = new LinePlot($values_absences);
   $plot->setColor($rouge);
   $plot->setYAxis(PLOT_LEFT); //Plot::LEFT
	// épaisseur du trait
	$plot->setThickness(2);

	// point rouge sur le graphique
	$plot->mark->setFill($rouge);
	$plot->mark->setType(MARK_SQUARE);

   $group->add($plot);
	// pas de chiffre après la virgule
	   $group->axis->left->setLabelPrecision(1);
   $group->axis->left->setColor($rouge);
   $group->axis->left->title->move(-5, 0);
   $group->axis->left->title->set("Absences");

   // les retards
   $plot = new LinePlot($values_retards);
	$plot->xAxis->setLabelText($x);
   $plot->setColor($bleu);
   $plot->setYAxis(PLOT_RIGHT); //Plot::RIGHT
	// type de trait
	$plot->setStyle(LINE_DOTTED); //Line::DOTTED
		// Change le style de ligne (Line::SOLID, Line::DOTTED ou Line::DASHED).

	// point noir sur le graphique
	$plot->mark->setFill($bleu);
	$plot->mark->setType(MARK_CIRCLE);
	   $plot->mark->setSize(7);
	   $plot->mark->setFill(new White);
	   $plot->mark->border->show();

/*
    * const int CIRCLE := 1
    * const int SQUARE := 2
    * const int TRIANGLE := 3
    * const int INVERTED_TRIANGLE := 4
    * const int RHOMBUS := 5
    * const int CROSS := 6
    * const int PLUS := 7
    * const int IMAGE := 8
    * const int STAR := 9
    * const int PAPERCLIP := 10
    * const int BOOK := 11

*/

   $group->add($plot);
	// pas de chiffre après la virgule
	   $group->axis->right->setLabelPrecision(1);
   $group->axis->right->setColor($bleu);
   $group->axis->right->title->set("Retard");


// AXE X

	// donnée de l'axe X
	$group->axis->bottom->setLabelText($x);
        // police de caractère de l'axe X
	$group->axis->bottom->label->setFont(new Tuffy(8));
	// rotation du texte de l'axe X en degré
	$group->axis->bottom->label->setAngle("30");
	// positionement du texte de l'axe X
	$group->axis->bottom->label->move(10, 0);
	// alignement du texte de l'axe X
	$group->axis->bottom->label->setAlign(LABEL_RIGHT, LABEL_BOTTOM);
	// padding de l'axe X
	$group->axis->bottom->label->setPadding(0, 0, 0, 0);


   $graph->add($group);

$graph->draw();

$graph->deleteAllCache();
?>
