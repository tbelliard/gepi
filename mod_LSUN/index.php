<?php

/*
*
* Copyright 2016 Régis Bouguin
*
* This file is part of GEPI.
*
* GEPI is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 3 of the License, or
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


// TODO : rechercher les mettre ça ailleurs

$niveau_arbo = "1";

//$tab_type_grp=get_tab_types_groupe();

$_AP = 1;
$_EPI = 2;
$_Parcours = 3;

// Initialisations files
include("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");

include_once 'lib/requetes_tables.php';

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

//recherche de l'utilisateur avec propel
$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

//On vérifie si le module est activé
if (getSettingValue("active_module_LSUN")!='y') {
    die("Le module n'est pas activé.");
}


//==============================================
include_once 'lib/fonctions.php';
//==============================================

$corrigeMEF = filter_input(INPUT_POST, 'corrigeMEF');

//debug_var();

if($corrigeMEF == 'y') {
	enregistreMEF();
}

if (MefAppartenanceAbsent()) {
	include_once 'getAppartenances.php';
	die();
}



//==============================================
//$style_specifique[] = "lib/style";
//$tbs_CSS_spe[] = "lib/style";
$tbs_CSS_spe[] = array('rel'=>"stylesheet", 'type'=>"text/css", 'fichier'=>"lib/style.css", 'media'=>"screen");
$titre_page = "AP - EPI - parcours";
if (!suivi_ariane($_SERVER['PHP_SELF'],'AP-EPI')) {
	echo "erreur lors de la création du fil d'ariane";
}
// $utilisation_jsdivdrag = "non";
// $_SESSION['cacher_header'] = "y";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//**************** en professeur *************
if ($utilisateur->getStatut()=="professeur") {
	include_once 'afficheProf.php';
	
//**************** en scolarite *************
} elseif ($utilisateur->getStatut()=="scolarite") {
	echo 'Affichage scolarité';
	
//**************** en cpe *************
} elseif ($utilisateur->getStatut()=="cpe") {
	echo 'Affichage CPE';

//**************** en administrateur *************	
} elseif ($utilisateur->getStatut()=="administrateur") {
	//echo 'Affichage administrateur';
	include_once 'requetes/requetesAdministrateur.php';
	include_once 'administrateur.php';
	
	
}

//debug_var();
//**************** Pied de page *****************
require_once("../lib/footer.inc.php");
//**************** Fin de pied de page *****************

