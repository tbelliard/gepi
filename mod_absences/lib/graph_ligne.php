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
require_once "../../artichow/LinePlot.class.php";


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

// Mes fonctions
	include("functions.php");

// Variable prédéfinit
	date_default_timezone_set('Europe/Paris');
	$date_act = date('Y-m-d');

// Variable non définit
	if (empty($_GET['donnee_titre']) and empty($_POST['donnee_titre'])) { $donnee_titre = ''; }
	   else { if (isset($_GET['donnee_titre'])) { $donnee_titre = $_GET['donnee_titre']; } if (isset($_POST['donnee_titre'])) { $donnee_titre = $_POST['donnee_titre']; } }
	if (empty($_GET['echelle_x']) and empty($_POST['echelle_x'])) { $echelle_x = ''; }
	   else { if (isset($_GET['echelle_x'])) { $echelle_x = $_GET['echelle_x']; } if (isset($_POST['echelle_x'])) { $echelle_x = $_POST['echelle_x']; } }
	if (empty($_GET['echelle_y']) and empty($_POST['echelle_y'])) { $echelle_y = ''; }
	   else { if (isset($_GET['echelle_y'])) { $echelle_y = $_GET['echelle_y']; } if (isset($_POST['echelle_y'])) { $echelle_y = $_POST['echelle_y']; } }
	if (empty($_GET['donnee_label']) and empty($_POST['donnee_label'])) { $donnee_label = ''; }
	   else { if (isset($_GET['donnee_label'])) { $donnee_label = $_GET['donnee_label']; } if (isset($_POST['donnee_label'])) { $donnee_label = $_POST['donnee_label']; } }

// LE GRAPHIQUE

$x = array_keys($_SESSION['donnee_e']);
$y = array_values($_SESSION['donnee_e']);
//$y2 = array_values($_SESSION['donnee_e']);

// conversion si mois
	if ( $echelle_x === 'M' ) {
		$i = 0;
		$x_recharge = $x;
		while ( !empty($x[$i]) )
		{
			$valeur = explode('-',$x_recharge[$i]);
			$x[$i] = convert_num_mois_court($valeur[1]).' '.$valeur[0];
			$valeur = '';
			$i = $i + 1;
		}
	}
// si semaine conversion
	if ( $echelle_x === 'J') {
		$i = 0;
		$x_recharge = $x;
		while ( !empty($x[$i]) )
		{
			$x[$i] = date_fr($x_recharge[$i]);
			$i = $i + 1;
		}
	}


// si en donnee_y === heure alors on converti les munites en heures pour affiché le label sur le graphique
	if ( $echelle_y === 'H' ) {
		$i = 0;
		while ( !empty($y[$i]) )
		{
			$y2[$i] = convert_minutes_heures($y[$i]);
			$i = $i + 1;
		}
	}
	if ( $echelle_y === 'E' ) {
		$y2 = $y;
	}

	if ( $echelle_y === 'D' ) {
		$y2 = $y;
	}

		// titre de l'axe X
		if($echelle_x === 'M') { $titre_axe_x = "Mois"; }
		if($echelle_x === 'J') { $titre_axe_x = "Jour"; }
		if($echelle_x === 'P') { $titre_axe_x = "Période)"; }
		if($echelle_x === 'C') { $titre_axe_x = "Classe"; }
		if($echelle_x === 'E') { $titre_axe_x = "Elève"; }

		//titre de l'axe Y
		if($echelle_y === 'H') { $titre_axe_y = "Total de minute"; }
		if($echelle_y === 'E') { $titre_axe_y = "Total"; }
		if($echelle_y === 'D') { $titre_axe_y = "Demi-journée"; }

// donnée de l'axe Y
//$y = array('60','135','30','60','15','60','135','30','60','15','60','15');
// donnée des étiquette du graphique
//$y2 = array('1h','2h15','0h30','1h','0h15','1h','2h15','0h30','1h','0h15','1h','2h15');
// donnée de l'axe X
//$x = array('janvier','février','mars','avril','mai','juin','juillet','aout','septembre','octobre','novembre','decembre');

//$y = array($nb_mois[11],$nb_mois[10],$nb_mois[9],$nb_mois[8],$nb_mois[7],$nb_mois[6],$nb_mois[5],$nb_mois[4],$nb_mois[3],$nb_mois[2],$nb_mois[1],$nb_mois[0]);
//$x = array($mois_aff[11],$mois_aff[10],$mois_aff[9],$mois_aff[8],$mois_aff[7],$mois_aff[6],$mois_aff[5],$mois_aff[4],$mois_aff[3],$mois_aff[2],$mois_aff[1],$mois_aff[0]);

// Creation du graphique
$graph = new Graph(550, 300);
$graph->setAntiAliasing(TRUE);

$plot = new LinePlot($y);

$plot->grid->setNoBackground();

// TITRE
	// titre du graphique
	$plot->title->set($donnee_titre[0]);
	// police de caractère du titre
	$plot->title->setFont(new Tuffy(10));
	// fond du cadre du titre
	$plot->title->setBackgroundColor(new Color(255, 255, 255, 25));
	// bord du cadre du titre
	$plot->title->border->show();
	// espacement du texte dans le cadre du titre
	$plot->title->setPadding(3, 3, 3, 3);
	// positionement du cadre titre
	$plot->title->move(0, 20);

// LE GRAPHIQUE
	// Change la couleur de fond de la grille
	$plot->grid->setBackgroundColor(new Color(255, 255, 255, 30));
	// décalement du 0 et de la fin
	$plot->setSpace(4, 4, 10, 0);
	// padding du graphique
	$plot->setPadding(50, 15, 10, 60);
	// couleur de dégradé du fond du graphique
	$plot->setBackgroundGradient(
	    new LinearGradient(
	        new Color(210, 210, 210),
	        new Color(255, 255, 255),
	        0
	    )
	);
	// couleur de la ligne
	$plot->setColor(new Color(0, 0, 150, 20));
	// couleur de dégradé sous la ligne
	$plot->setFillGradient(
	    new LinearGradient(
	        new Color(150, 150, 210),
	        new Color(245, 245, 245),
	        90
	    )
	);
	// point rouge sur le graphique
	$plot->mark->setType(MARK_CIRCLE);
	// affiche les bord du graphique
	$plot->mark->border->show();

// AXE Y
	// définie les traits grand petit tout les 10 point
	$plot->yAxis->setNumberByTick('minor', 'major', 5);

// titre de l'axe Y
	// disposition du titre de l'axe Y
	$plot->yAxis->title->move(-15, 0);
	// texte du titre de l'axe Y
	$plot->yAxis->title->set($titre_axe_y);
	// police de caractère du titre de l'axe Y
	$plot->yAxis->title->setFont(new Tuffy(10));

	// label du graphique
	$plot->label->set($y2);
	// disposition du label sur le graphique
	$plot->label->move(0, 10);
	// On donne aux étiquettes un dégradé de fond
	$plot->label->setBackgroundGradient(
	      new LinearGradient(
	         new Color(250, 250, 250, 10),
	         new Color(255, 200, 200, 30),
	         0
	      )
	);
	// Bordure des étiquettes
	$plot->label->border->setColor(new Color(20, 20, 20, 20));
	// Enfin, on ajoute un espace interne entre la bordure et le texte des étiquettes
	$plot->label->setPadding(3, 1, 1, 0);


// AXE X
	// donnée de l'axe X
	$plot->xAxis->setLabelText($x);
	// police de caractère de l'axe X
	$plot->xAxis->label->setFont(new Tuffy(8));
	// rotation du texte de l'axe X en degré
	$plot->xAxis->label->setAngle("30");
	// positionement du texte de l'axe X
	$plot->xAxis->label->move(10, 0);
	// alignement du texte de l'axe X
	$plot->xAxis->label->setAlign(LABEL_RIGHT, LABEL_BOTTOM);
	// padding de l'axe X
	$plot->xAxis->label->setPadding(0, 0, 0, 0);

// titre de l'axe X
	// disposition du titre de l'axe X
	$plot->xAxis->title->move(0, 20);
	// texte du titre de l'axe X
	$plot->xAxis->title->set($titre_axe_x);
	// police de caractère du titre de l'axe X
	$plot->xAxis->title->setFont(new Tuffy(10));

$graph->add($plot);
$graph->draw();
?>

