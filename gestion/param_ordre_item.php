<?php
/*
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

// Initialisation des feuilles de style après modification pour améliorer l'accessibilité
$accessibilite="y";

// Begin standart header
$titre_page = "Ordre des items";
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

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

include "../class_php/class_menu_general.php";
include "../class_php/class_page_accueil.php";
include "../class_php/class_accueil_ordre_menu.php";
include "../class_php/class_accueil_change_menu.php";


// on demande une validation quitte sans enregistrer les changements
$messageEnregistrer="Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?";

// ====== Inclusion des balises head et du bandeau =====
//$msg = "Essai message";

include_once("../lib/header_template.inc.php");

/****************************************************************
			FIN HAUT DE PAGE
****************************************************************/

if (!suivi_ariane($_SERVER['PHP_SELF'],$titre_page))
		echo "erreur lors de la création du fil d'ariane";

/****************************************************************
			ENREGISTREMENT DES DONNÉES SI BESOIN
****************************************************************/


/***** On vérifie si des données sont envoyées en POST *****/
$enregistrer=isset($_POST['btn_enregistrer']) ? TRUE : NULL;
$initialiser=isset($_POST['btn_reinitialiser']) ? TRUE : NULL;
$optimiser=isset($_POST['btn_optimiser']) ? TRUE : NULL;
if ($optimiser){$enregistrer=TRUE;} // On enregistre l'ordre des menus puis on optimise

if ($initialiser){
  $sql="DROP TABLE IF EXISTS `mn_ordre_accueil`;";
  if (!mysqli_query($GLOBALS["mysqli"], $sql)){
	$tbs_msg= "erreur lors de la suppression de la table mn_ordre_accueil";
  }
  $sql="DROP TABLE IF EXISTS `mn_ordre_accueil_item`;";
  if (!mysqli_query($GLOBALS["mysqli"], $sql)){
	$tbs_msg= "erreur lors de la suppression de la table mn_ordre_accueil_item";
  }

}else if ($enregistrer){

/***** On crée la table si elle n'existe pas *****/
$sql="CREATE TABLE IF NOT EXISTS mn_ordre_accueil (
id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
statut VARCHAR( 25 ) NOT NULL ,
bloc VARCHAR( 50 ) NOT NULL ,
num_menu INT NOT NULL ,
nouveau_nom VARCHAR( 50 ) NOT NULL
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";

$sql2="CREATE TABLE IF NOT EXISTS mn_ordre_accueil_item (
id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
statut VARCHAR( 25 ) NOT NULL ,
bloc VARCHAR( 50 ) NOT NULL ,
num_menu INT NOT NULL ,
num_item INT NOT NULL ,
nouveau_nom VARCHAR( 50 ) NOT NULL
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";

  if (!mysqli_query($GLOBALS["mysqli"], $sql)){
	$tbs_msg= "erreur lors de la création de la table mn_ordre_accueil";
  }else if (!mysqli_query($GLOBALS["mysqli"], $sql2)){
	$tbs_msg= "erreur lors de la création de la table mn_ordre_accueil_item";
  } else {
// les tables existent
	
	$changeOrdre= new class_accueil_change_menu($_POST);
	if($optimiser){
	  $changeOrdre->optimiseMenu();
	}
	$tbs_msg=$changeOrdre->message;
  }
  unset($changeOrdre);
}

$menuAffiche=array();

/****************************************************************
			CONSTRUCTION DE LA PAGE
****************************************************************/

/****************************************************************/
/* ----- Menu administrateur ----- */
$menuAffiche['administrateur']=new class_accueil_ordre_menu('administrateur', $gepiSettings, $niveau_arbo,$ordre_menus);

/****************************************************************/
/* ----- Menu professeur ----- */
$menuAffiche['professeur']=new class_accueil_ordre_menu('professeur', $gepiSettings, $niveau_arbo,$ordre_menus);

/****************************************************************/
/* ----- Menu scolarite ----- */
$menuAffiche['scolarite']=new class_accueil_ordre_menu('scolarite', $gepiSettings, $niveau_arbo,$ordre_menus);

/****************************************************************/
/* ----- Menu eleve ----- */
$menuAffiche['eleve']=new class_accueil_ordre_menu('eleve', $gepiSettings, $niveau_arbo,$ordre_menus);

/****************************************************************/
/* ----- Menu cpe ----- */
$menuAffiche['cpe']=new class_accueil_ordre_menu('cpe', $gepiSettings, $niveau_arbo,$ordre_menus);
/****************************************************************/
/* ----- Menu secours ----- */
$menuAffiche['secours']=new class_accueil_ordre_menu('secours', $gepiSettings, $niveau_arbo,$ordre_menus);
/****************************************************************/
/* ----- Menu responsable ----- */
$menuAffiche['responsable']=new class_accueil_ordre_menu('responsable', $gepiSettings, $niveau_arbo,$ordre_menus);



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


$nom_gabarit = '../templates/'.$_SESSION['rep_gabarits'].'/gestion/ordre_item_template.php';

$tbs_last_connection=""; // On n'affiche pas les dernières connexions
include($nom_gabarit);

// ------ on vide les tableaux -----
unset($menuAffiche);




?>
