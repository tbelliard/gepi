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
	if (empty($_GET['export_pdf']) and empty($_POST['export_pdf'])) { $export_pdf = ''; }
	   else { if (isset($_GET['export_pdf'])) { $export_pdf = $_GET['export_pdf']; } if (isset($_POST['export_pdf'])) { $export_pdf = $_POST['export_pdf']; } }

// LE GRAPHIQUE

$graph = new Graph(550, 250);

$graph->shadow->setPosition(Shadow::RIGHT_BOTTOM);
$graph->shadow->setSize(4);

$graph->setBackgroundGradient(
	new LinearGradient(
		new Color(240, 240, 240, 0),
		new White,
		0
	)
);

$genres = array(
	'Janvier'	=> 60,
	'Février'	=> 135,
	'Mars'		=> 30,
	'Avril'		=> 60,
	'Mai'		=> 15,
	'Juin'		=> 60,
	'Juillet'	=> 135,
	'Aout'		=> 30,
	'Septembre'	=> 60,
	'Octobre'	=> 15,
	'Novembre'	=> 60,
	'Décembre'	=> 15,
);

$x = array_keys($_SESSION['donnee_e']);
//$y = array_values($_SESSION['donnee_e']);

// conversion si mois
	if ( $echelle_x === 'M') {
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



$pie = new Pie(array_values($_SESSION['donnee_e']), Pie::COLORED);

// remarque Pie::COLORED autre variable : COLORED, AQUA, DARK, EARTH

// les étiquettes
	// précision de l'étiquette, nombre de chiffre après la virgule
	$pie->setLabelPrecision(1);
	// position de l'étiquette
	$pie->setLabelPosition(-5);
	// n'affiché que les étiquettes supérieur à 5
	//$pie->setLabelMinimum(5);
	// police de caractère des étiquettes
	$pie->label->setFont(new Tuffy(8));
	// fond de couleur des étiquettes
	$pie->label->setBackgroundColor(new White(50));
	// padding des étiquettes
	$pie->label->setPadding(2, 2, 2, 2);

// la légende
	// tableau des noms dans la légende
	$pie->setLegend($x);
	// positionement de la légende
	$pie->legend->setPosition(1.45, .45);

// le camember
	// positionement du camembert
	$pie->setCenter(.35, .50);
	// taille du camembert
	$pie->setSize(.60, .70);
	// encadrement des différentes partie du camembert
		// remplacer 1.0.9 par setBorderColor
		// $pie->setBorder(new Black());
	$pie->setBorderColor(new Black());
	// mode 3D du camembert
	$pie->set3D(15);
	// couleur de fond du camembert
	//$pie->setBackgroundColor(new White(0));
	// part à séparer ?
	//$pie->explode();

// le titre
	// le texte du titre
	$pie->title->set($donnee_titre[0]);
	// emplacement du titre
	$pie->title->move(10, -20);
	// police de caractère du titre
	$pie->title->setFont(new TuffyBold(10));
	// le fond de couleur du titre
	$pie->title->setBackgroundColor(new White(50));
	// les espacement dans le cadre du titre
	$pie->title->setPadding(5, 5, 2, 2);
	// encadrement du titre
	$pie->title->border->setColor(new Black());

$graph->add($pie);

if ( $export_pdf === 'oui' ) { $graph->draw('../../documents/aa.png'); }
  else { $graph->draw(); }

?>
