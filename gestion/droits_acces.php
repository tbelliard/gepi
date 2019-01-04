<?php
/*
*
* Copyright 2001-2019 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

// Initialisation des feuilles de style après modification pour améliorer l'accessibilité
$accessibilite="y";

// Begin standart header
$titre_page = "Droits d'accès";
$niveau_arbo = 1;
$gepiPathJava="./..";

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
// Check access

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

include "../class_php/gestion/class_droit_acces_template.php";


// ====== Initialisation des messages =====
$tbs_message = '';
$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
// on demande une validation quitte sans enregistrer les changements
$messageEnregistrer = $themessage;

// ====== Inclusion des balises head et du bandeau =====

include_once("../lib/header_template.inc.php");


/****************************************************************
			FIN HAUT DE PAGE
****************************************************************/
if (!suivi_ariane($_SERVER['PHP_SELF'],$titre_page))
		echo "erreur lors de la création du fil d'ariane";
/****************************************************************
			ENREGISTREMENT DES DONNÉES SI BESOIN
****************************************************************/

// Load settings
if (!loadSettings()) {
	die("Erreur chargement settings");
}
if (isset($_POST['OK'])) {
  $droitAffiche= new class_droit_acces_template($_POST);
} else {
  $droitAffiche= new class_droit_acces_template();
}
//debug_var();

if(isset($_POST['is_posted'])) {
	check_token(false);
}

include('droits_acces.inc.php');

/*
	Exemple:
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Archivage années antérieures';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_annees_anterieures');
*/

foreach($tab_droits_acces as $statutItem => $current_statut_item) {
	//echo "$statutItem<br />";
	foreach($current_statut_item as $titreItem => $current_item) {
		/*
		echo "$titreItem<pre>";
		print_r($current_item);
		echo "</pre>";
		echo "<hr />";
		*/
		//$titreItem=$current_item['titreItem'];
		$texteItem=$current_item['texteItem'];

		if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
			$tbs_message = 'Erreur lors du chargement de '.$titreItem;
	}
}

$tbs_message = $droitAffiche->get_erreurs();

if (isset($_POST['OK']) AND ($tbs_message=='')) {
	$tbs_message = "Les modifications ont été enregistrées !";
	$post_reussi=TRUE;
}
$tbs_msg=$tbs_message;

/****************************************************************
			BAS DE PAGE
****************************************************************/
$tbs_microtime	="";
$tbs_pmv="";
require_once ("../lib/footer_template.inc.php");

/****************************************************************
			On s'assure que le nom du gabarit est bien renseigné
****************************************************************/
if ((!isset($_SESSION['rep_gabarits'])) || (empty($_SESSION['rep_gabarits']))) {
	$_SESSION['rep_gabarits']="origine";
}

//==================================
// Décommenter la ligne ci-dessous pour afficher les variables $_GET, $_POST, $_SESSION et $_SERVER pour DEBUG:
// $affiche_debug=debug_var();


$nom_gabarit = '../templates/'.$_SESSION['rep_gabarits'].'/gestion/droit_acces_template.php';

$tbs_last_connection=""; // On n'affiche pas les dernières connexions
include($nom_gabarit);

// ------ on vide les tableaux -----
unset($droitAffiche);


?>
