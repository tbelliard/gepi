<?php
/*
*
* Copyright 2001-2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

/*

if (isset($_POST['OK'])) {
	if (isset($_POST['GepiRubConseilProf'])) {
		$temp = 'yes';
	} else {
		$temp = 'no';
	}
	if (!saveSetting("GepiRubConseilProf", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiRubConseilProf !";
	}

	if (isset($_POST['CommentairesTypesPP'])) {
		$temp = 'yes';
	} else {
		$temp = 'no';
	}
	if (!saveSetting("CommentairesTypesPP", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de CommentairesTypesPP !";
	}

	if (isset($_POST['GepiRubConseilScol'])) {
		$temp = 'yes';
	} else {
		$temp = 'no';
	}
	if (!saveSetting("GepiRubConseilScol", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiRubConseilScol !";
	}

	if (isset($_POST['CommentairesTypesScol'])) {
		$temp = 'yes';
	} else {
		$temp = 'no';
	}
	if (!saveSetting("CommentairesTypesScol", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de CommentairesTypesScol !";
	}

	if (isset($_POST['GepiProfImprBul'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiProfImprBul", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiProfImprBul !";
	}

	if (isset($_POST['GepiProfImprBulSettings'])) {
		$temp = "yes";
	} else {
		$temp ="no";
	}
	if (!saveSetting("GepiProfImprBulSettings", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiProfImprBulSettings !";
	}


	if (isset($_POST['GepiAccesRestrAccesAppProfP'])) {
		$temp = "yes";
	} else {
		$temp ="no";
	}
	if (!saveSetting("GepiAccesRestrAccesAppProfP", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesRestrAccesAppProfP !";
	}


	if (isset($_POST['GepiAdminImprBulSettings'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAdminImprBulSettings", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAdminImprBulSettings !";
	}

	if (isset($_POST['GepiScolImprBulSettings'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiScolImprBulSettings", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiScolImprBulSettings !";
	}

	if (isset($_POST['GepiAccesReleveScol'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesReleveScol", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesReleveScol !";
	}

	if (isset($_POST['GepiAccesReleveCpe'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesReleveCpe", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesReleveCpe !";
	}

	if (isset($_POST['GepiAccesCdtCpeRestreint'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesCdtCpeRestreint", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesCdtCpeRestreint !";
	}

	if (isset($_POST['GepiAccesCdtCpe'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesCdtCpe", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesCdtCpe !";
	}

	if (isset($_POST['GepiAccesCdtScol'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesCdtScol", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesCdtScol !";
	}

	if (isset($_POST['GepiAccesCdtScolRestreint'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesCdtScolRestreint", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesCdtScolRestreint !";
	}

	if (isset($_POST['GepiAccesReleveProfP'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesReleveProfP", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesReleveProfP !";
	}
	if (isset($_POST['GepiAccesReleveProf'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesReleveProf", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesReleveProf !";
	}
	if (isset($_POST['GepiAccesReleveProfTousEleves'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesReleveProfTousEleves", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesReleveProf !";
	}
	if (isset($_POST['GepiAccesReleveProfToutesClasses'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesReleveProfToutesClasses", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesReleveProfToutesClasses !";
	}
	if (isset($_POST['GepiAccesMoyennesProf'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesMoyennesProf", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesMoyennesProf !";
	}
	if (isset($_POST['GepiAccesMoyennesProfTousEleves'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesMoyennesProfTousEleves", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesMoyennesProfTousEleves !";
	}
	if (isset($_POST['GepiAccesMoyennesProfToutesClasses'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesMoyennesProfToutesClasses", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesMoyennesProfToutesClasses !";
	}
	if (isset($_POST['GepiAccesReleveEleve'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesReleveEleve", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesReleveEleve !";
	}

if (isset($_POST['GepiAccesOptionsReleveEleve'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesOptionsReleveEleve", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesOptionsReleveEleve !";
	}

	if (isset($_POST['GepiAccesCahierTexteEleve'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesCahierTexteEleve", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesCahierTexteEleve !";
	}

	if (isset($_POST['GepiAccesReleveParent'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesReleveParent", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesReleveParent !";
	}

	if (isset($_POST['GepiAccesOptionsReleveParent'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesOptionsReleveParent", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesOptionsReleveParent !";
	}

	if (isset($_POST['GepiAccesCahierTexteParent'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesCahierTexteParent", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesCahierTexteParent !";
	}

	if (isset($_POST['GepiPasswordReinitProf'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiPasswordReinitProf", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiPasswordReinitProf !";
	}

	if (isset($_POST['GepiPasswordReinitScolarite'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiPasswordReinitScolarite", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiPasswordReinitScolarite !";
	}

	if (isset($_POST['GepiPasswordReinitCpe'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiPasswordReinitCpe", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiPasswordReinitCpe !";
	}

	if (isset($_POST['GepiPasswordReinitAdmin'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiPasswordReinitAdmin", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiPasswordReinitAdmin !";
	}

	if (isset($_POST['GepiPasswordReinitEleve'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiPasswordReinitEleve", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiPasswordReinitEleve !";
	}

	if (isset($_POST['GepiPasswordReinitParent'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiPasswordReinitParent", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiPasswordReinitParent !";
	}

	if (isset($_POST['GepiAccesEquipePedaEleve'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesEquipePedaEleve", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesEquipePedaEleve !";
	}

	if (isset($_POST['GepiAccesEquipePedaParent'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesEquipePedaParent", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesEquipePedaParent !";
	}

	if (isset($_POST['GepiAccesEquipePedaEmailEleve'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesEquipePedaEmailEleve", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesEquipePedaEmailEleve !";
	}

	if (isset($_POST['GepiAccesCpePPEmailEleve'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesCpePPEmailEleve", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesCpePPEmailEleve !";
	}

	if (isset($_POST['GepiAccesEquipePedaEmailParent'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesEquipePedaEmailParent", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesEquipePedaEmailParent !";
	}

	if (isset($_POST['GepiAccesCpePPEmailParent'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesCpePPEmailParent", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesCpePPEmailParent !";
	}
	if (isset($_POST['GepiAccesBulletinSimpleProf'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesBulletinSimpleProf", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesBulletinSimpleProf !";
	}
	if (isset($_POST['GepiAccesBulletinSimpleProfTousEleves'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesBulletinSimpleProfTousEleves", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesBulletinSimpleProfTousEleves !";
	}
	if (isset($_POST['GepiAccesBulletinSimpleProfToutesClasses'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesBulletinSimpleProfToutesClasses", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesBulletinSimpleProfToutesClasses !";
	}
	if (isset($_POST['GepiAccesBulletinSimpleParent'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesBulletinSimpleParent", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesBulletinSimpleParent !";
	}

	if (isset($_POST['GepiAccesBulletinSimpleEleve'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesBulletinSimpleEleve", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesBulletinSimpleEleve !";
	}

	if (isset($_POST['GepiAccesGraphEleve'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesGraphEleve", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesGraphEleve !";
	}

	if (isset($_POST['GepiAccesGraphParent'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesGraphParent", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesGraphParent !";
	}


	if (isset($_POST['GepiAccesVisuToutesEquipProf'])) {
		$temp = 'yes';
	} else {
		$temp = 'no';
	}
	if (!saveSetting("GepiAccesVisuToutesEquipProf", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesVisuToutesEquipProf !";
	}

	if (isset($_POST['GepiAccesVisuToutesEquipScol'])) {
		$temp = 'yes';
	} else {
		$temp = 'no';
	}
	if (!saveSetting("GepiAccesVisuToutesEquipScol", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesVisuToutesEquipScol !";
	}

	if (isset($_POST['GepiAccesVisuToutesEquipCpe'])) {
		$temp = 'yes';
	} else {
		$temp = 'no';
	}
	if (!saveSetting("GepiAccesVisuToutesEquipCpe", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesVisuToutesEquipCpe !";
	}

	// Années antérieures
	if (isset($_POST['AAProfTout'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("AAProfTout", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de AAProfTout !";
	}

	if (isset($_POST['AAProfPrinc'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("AAProfPrinc", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de AAProfPrinc !";
	}

	if (isset($_POST['AAProfClasses'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("AAProfClasses", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de AAProfClasses !";
	}

	if (isset($_POST['AAProfGroupes'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("AAProfGroupes", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de AAProfGroupes !";
	}

	if (isset($_POST['AACpeTout'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("AACpeTout", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de AACpeTout !";
	}

	if (isset($_POST['AACpeResp'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("AACpeResp", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de AACpeResp !";
	}

	if (isset($_POST['AAScolTout'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("AAScolTout", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de AAScolTout !";
	}

	if (isset($_POST['AAScolResp'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("AAScolResp", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de AAScolResp !";
	}

	if (isset($_POST['AAResponsable'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("AAResponsable", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de AAResponsable !";
	}

	if (isset($_POST['AAEleve'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("AAEleve", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de AAEleve !";
	}


	if (isset($_POST['GepiAccesModifMaPhotoProfesseur'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesModifMaPhotoProfesseur", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesModifMaPhotoProfesseur !";
	}

	if (isset($_POST['GepiAccesModifMaPhotoAdministrateur'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesModifMaPhotoAdministrateur", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesModifMaPhotoAdministrateur !";
	}

	if (isset($_POST['GepiAccesModifMaPhotoScolarite'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesModifMaPhotoScolarite", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesModifMaPhotoScolarite !";
	}

	if (isset($_POST['GepiAccesModifMaPhotoCpe'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesModifMaPhotoCpe", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesModifMaPhotoCpe !";
	}

	if (isset($_POST['GepiAccesModifMaPhotoEleve'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesModifMaPhotoEleve", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesModifMaPhotoEleve !";
	}

	if (isset($_POST['GepiAccesGestElevesProfP'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesGestElevesProfP", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesGestElevesProfP !";
	}

	if (isset($_POST['GepiAccesGestPhotoElevesProfP'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesGestPhotoElevesProfP", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesGestPhotoElevesProfP !";
	}


	if (isset($_POST['GepiAccesBulletinSimplePP'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesBulletinSimplePP", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesBulletinSimplePP !";
	}



	if (isset($_POST['appreciations_types_profs'])) {
		if (!saveSetting("appreciations_types_profs", 'yes')) {
			$msg .= "Erreur lors de l'enregistrement de l'autorisation d'utilisation d'appréciations-types pour les ".$gepiSettings['denomination_professeurs']." !";
		}
	}
	else{
		if (!saveSetting("appreciations_types_profs", 'no')) {
			$msg .= "Erreur lors de l'enregistrement de l'interdiction d'utilisation d'appréciations-types pour les ".$gepiSettings['denomination_professeurs']." !";
		}
	}

	if (isset($_POST['GepiAccesSaisieEctsProf'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesSaisieEctsProf", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesSaisieEctsProf !";
	}

	if (isset($_POST['GepiAccesRecapitulatifEctsProf'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesRecapitulatifEctsProf", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesRecapitulatifEctsProf !";
	}

	if (isset($_POST['GepiAccesSaisieEctsPP'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesSaisieEctsPP", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesSaisieEctsPP !";
	}

	if (isset($_POST['GepiAccesSaisieEctsScolarite'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesSaisieEctsScolarite", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesSaisieEctsScolarite !";
	}

	if (isset($_POST['GepiAccesRecapitulatifEctsScolarite'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesRecapitulatifEctsScolarite", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesRecapitulatifEctsScolarite !";
	}


	if (isset($_POST['GepiAccesEditionDocsEctsPP'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesEditionDocsEctsPP", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesEditionDocsEctsPP !";
	}

	if (isset($_POST['GepiAccesEditionDocsEctsScolarite'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesEditionDocsEctsScolarite", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesEditionDocsEctsScolarite !";
	}

	if (isset($_POST['GepiAccesGestElevesProf'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesGestElevesProf", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesGestElevesProf !";
	}

	if (isset($_POST['autoriser_correction_bulletin'])) {
		$temp = "y";
	} else {
		$temp = "n";
	}
	if (!saveSetting("autoriser_correction_bulletin", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de autoriser_correction_bulletin!";
	}

	if(getSettingValue('active_mod_discipline')=='y') {
		if (isset($_POST['visuDiscProfClasses'])) {
			$temp = "yes";
		} else {
			$temp = "no";
		}
		if (!saveSetting("visuDiscProfClasses", $temp)) {
			$msg .= "Erreur lors de l'enregistrement de visuDiscProfClasses !";
		}

		if (isset($_POST['visuDiscProfGroupes'])) {
			$temp = "yes";
		} else {
			$temp = "no";
		}
		if (!saveSetting("visuDiscProfGroupes", $temp)) {
			$msg .= "Erreur lors de l'enregistrement de visuDiscProfGroupes !";
		}
	}

	$tab_droits_ele_trombi=array('GepiAccesEleTrombiTousEleves',
'GepiAccesEleTrombiElevesClasse',
'GepiAccesEleTrombiPersonnels',
'GepiAccesEleTrombiProfsClasse');
	for($i=0;$i<count($tab_droits_ele_trombi);$i++) {
		if (isset($_POST[$tab_droits_ele_trombi[$i]])) {
			$temp = "yes";
		} else {
			$temp = "no";
		}
		if (!saveSetting("$tab_droits_ele_trombi[$i]", $temp)) {
			$msg .= "Erreur lors de l'enregistrement de $tab_droits_ele_trombi[$i] !";
		}
		*/
		/*
		else {
			$msg .= "Enregistrement de $tab_droits_ele_trombi[$i]=$temp !<br />";
		}
		*/
		/*
	}
}

// Load settings
if (!loadSettings()) {
	die("Erreur chargement settings");
}
if (isset($_POST['is_posted']) and ($msg=='')) $msg = "Les modifications ont été enregistrées !";

//debug_var();


//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
		*/
//**************** EN-TETE *****************
// End standart header
		/*
$titre_page = "Droits d'accès";
//**************** FIN EN-TETE *****************
?>
<p class='bold'><a href="index.php"<?php
echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
?>><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>

?>
<form action="droits_acces.php" method="post" id="form1" style="width: 100%;">
<table class='menu' style='width: 90%; margin-left: auto; margin-right: auto;' cellpadding="10" summary='Paramétrage des droits'>
	<tr>
		<th colspan="2">
		Paramétrage des droits d'accès
		</th>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">Un <?php echo $gepiSettings['denomination_professeur']; ?></td>
		<td>
			<table border='0' summary='Professeur'>
			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesReleveProf" id="GepiAccesReleveProf" value="yes" <?php if (getSettingValue("GepiAccesReleveProf")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesReleveProf' style='cursor: pointer;'> a accès aux relevés de notes des <?php echo $gepiSettings['denomination_eleves']; ?> des classes dans lesquelles il enseigne</label></td>
			</tr>
		 */
$statutItem="enseignant";
$titreItem='GepiAccesReleveProf';
$texteItem="a accès aux relevés de notes des ".$gepiSettings['denomination_eleves']." des classes dans lesquelles il enseigne";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*
			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesReleveProfTousEleves" id="GepiAccesReleveProfTousEleves" value="yes" <?php if (getSettingValue("GepiAccesReleveProfTousEleves")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesReleveProfTousEleves' style='cursor: pointer;'> a accès aux relevés de notes de tous les <?php echo $gepiSettings['denomination_eleves']; ?> des classes dans lesquelles il enseigne (<em>si case non cochée, le <?php echo $gepiSettings['denomination_professeur']; ?> ne voit que les <?php echo $gepiSettings['denomination_eleves']; ?> de ses groupes d'enseignement et pas les autres <?php echo $gepiSettings['denomination_eleves']; ?> des classes concernées</em>)</label></td>
			</tr>
		 */
$titreItem='GepiAccesReleveProfTousEleves';
$texteItem="a accès aux relevés de notes de tous les ".$gepiSettings['denomination_eleves']." des classes dans lesquelles il enseigne
  <br />(<em>si case non cochée, le ".$gepiSettings['denomination_professeur']." ne voit que les ".$gepiSettings['denomination_eleves']." de ses groupes d'enseignement et pas les autres ".$gepiSettings['denomination_eleves']." des classes concernées</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesReleveProfToutesClasses" id="GepiAccesReleveProfToutesClasses" value="yes" <?php if (getSettingValue("GepiAccesReleveProfToutesClasses")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesReleveProfToutesClasses' style='cursor: pointer;'> a accès aux relevés de notes des <?php echo $gepiSettings['denomination_eleves']; ?> de toutes les classes</label></td>
			</tr>
		 */
$titreItem='GepiAccesReleveProfToutesClasses';
$texteItem="a accès aux relevés de notes des ".$gepiSettings['denomination_eleves']." de toutes les classes";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesMoyennesProf" id="GepiAccesMoyennesProf" value="yes" <?php if (getSettingValue("GepiAccesMoyennesProf")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesMoyennesProf' style='cursor: pointer;'> a accès aux moyennes des <?php echo $gepiSettings['denomination_eleves']; ?> des classes dans lesquelles il enseigne</label></td>
			</tr>
		 */
$titreItem='GepiAccesMoyennesProf';
$texteItem="a accès aux moyennes des ".$gepiSettings['denomination_eleves']." des classes dans lesquelles il enseigne";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesMoyennesProfTousEleves" id="GepiAccesMoyennesProfTousEleves" value="yes" <?php if (getSettingValue("GepiAccesMoyennesProfTousEleves")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesMoyennesProfTousEleves' style='cursor: pointer;'> a accès aux moyennes de tous les <?php echo $gepiSettings['denomination_eleves']; ?> des classes dans lesquelles il enseigne (<em>si case non cochée, le <?php echo $gepiSettings['denomination_professeur']; ?> ne voit que les <?php echo $gepiSettings['denomination_eleves']; ?> de ses groupes d'enseignement et pas les autres <?php echo $gepiSettings['denomination_eleves']; ?> des classes concernées</em>)</label></td>
			</tr>
		 */
$titreItem='GepiAccesMoyennesProfTousEleves';
$texteItem="a accès aux moyennes de tous les ".$gepiSettings['denomination_eleves']." des classes dans lesquelles il enseigne
  <br />(<em>si case non cochée, le ".$gepiSettings['denomination_professeur']." ne voit que les ".$gepiSettings['denomination_eleves']." de ses groupes d'enseignement et pas les autres ".$gepiSettings['denomination_eleves']." des classes concernées</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesMoyennesProfToutesClasses" id="GepiAccesMoyennesProfToutesClasses" value="yes" <?php if (getSettingValue("GepiAccesMoyennesProfToutesClasses")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesMoyennesProfToutesClasses' style='cursor: pointer;'> a accès aux moyennes des <?php echo $gepiSettings['denomination_eleves']; ?> de toutes les classes</label></td>
			</tr>
		 */
$titreItem='GepiAccesMoyennesProfToutesClasses';
$texteItem="a accès aux moyennes des ".$gepiSettings['denomination_eleves']." de toutes les classes";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesBulletinSimpleProf" id="GepiAccesBulletinSimpleProf" value="yes" <?php if (getSettingValue("GepiAccesBulletinSimpleProf")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesBulletinSimpleProf' style='cursor: pointer;'> a accès aux bulletins simples des <?php echo $gepiSettings['denomination_eleves']; ?> des classes dans lesquelles il enseigne</label></td>
			</tr>
		 */
$titreItem='GepiAccesBulletinSimpleProf';
$texteItem="a accès aux bulletins simples des ".$gepiSettings['denomination_eleves']." des classes dans lesquelles il enseigne";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesBulletinSimpleProfTousEleves" id="GepiAccesBulletinSimpleProfTousEleves" value="yes" <?php if (getSettingValue("GepiAccesBulletinSimpleProfTousEleves")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesBulletinSimpleProfTousEleves' style='cursor: pointer;'> a accès aux bulletins simples de tous les <?php echo $gepiSettings['denomination_eleves']; ?> des classes dans lesquelles il enseigne (<em>si case non cochée, le <?php echo $gepiSettings['denomination_professeur']; ?> ne voit que les <?php echo $gepiSettings['denomination_eleves']; ?> de ses groupes d'enseignement et pas les autres <?php echo $gepiSettings['denomination_eleves']; ?> des classes concernées</em>)</label></td>
			</tr>
		 */
$titreItem='GepiAccesBulletinSimpleProfTousEleves';
$texteItem="a accès aux bulletins simples de tous les ".$gepiSettings['denomination_eleves']." des classes dans lesquelles il enseigne
  <br />(<em>si case non cochée, le ".$gepiSettings['denomination_professeur']." ne voit que les ".$gepiSettings['denomination_eleves']." de ses groupes d'enseignement et pas les autres ".$gepiSettings['denomination_eleves']." des classes concernées</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesBulletinSimpleProfToutesClasses" id="GepiAccesBulletinSimpleProfToutesClasses" value="yes" <?php if (getSettingValue("GepiAccesBulletinSimpleProfToutesClasses")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesBulletinSimpleProfToutesClasses' style='cursor: pointer;'> a accès aux bulletins simples des <?php echo $gepiSettings['denomination_eleves']; ?> de toutes les classes</label></td>
			</tr>
		 */
$titreItem='GepiAccesBulletinSimpleProfToutesClasses';
$texteItem="a accès aux bulletins simples des ".$gepiSettings['denomination_eleves']." de toutes les classes";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="appreciations_types_profs" id="appreciations_types_profs" value="y" <?php if (getSettingValue("appreciations_types_profs")=='y') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='appreciations_types_profs' style='cursor: pointer;'> peut utiliser des appréciations-types sur les bulletins.</label></td>
			</tr>
		 */
$titreItem='appreciations_types_profs';
$texteItem="peut utiliser des appréciations-types sur les bulletins.";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="autoriser_correction_bulletin" id="autoriser_correction_bulletin" value="y" <?php if (getSettingValue("autoriser_correction_bulletin")=='y') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='autoriser_correction_bulletin' style='cursor: pointer;'> peut solliciter des corrections de ses appréciations sur les bulletins une fois la période (<em>partiellement</em>) close (<em>pour reformuler une appréciation, corriger des fautes... de frappe;</em>).</label></td>
			</tr>
		 */
$titreItem='autoriser_correction_bulletin';
$texteItem="peut solliciter des corrections de ses appréciations sur les bulletins une fois la période (<em>partiellement</em>) close (<em>pour reformuler une appréciation, corriger des fautes... de frappe</em>).";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;



$titreItem='autoriser_signalement_faute_app_prof';
$texteItem="peut signaler, en période ouverte ou partiellement close, (<em>aux professeurs concernés</em>) des fautes (<em>de frappe;</em>) dans les appréciations des bulletins (<em>pour leur permettre corriger avant impression des bulletins</em>).";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiPasswordReinitProf" id="GepiPasswordReinitProf" value="yes" <?php if (getSettingValue("GepiPasswordReinitProf")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiPasswordReinitProf' style='cursor: pointer;'> peut réinitialiser lui-même son mot de passe perdu (<em>si fonction activée</em>)</label></td>
			</tr>
		 */
$titreItem='GepiPasswordReinitProf';
$texteItem="peut réinitialiser lui-même son mot de passe perdu (<em>si fonction activée</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesVisuToutesEquipProf" id="GepiAccesVisuToutesEquipProf" value="yes" <?php if (getSettingValue("GepiAccesVisuToutesEquipProf")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesVisuToutesEquipProf' style='cursor: pointer;'> a accès à la Visualisation de toutes les équipes</label></td>
			</tr>
		 */
$titreItem='GepiAccesVisuToutesEquipProf';
$texteItem="a accès à la Visualisation de toutes les équipes";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<!-- Années antérieures -->
			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="AAProfTout" id="AAProfTout" value="yes" <?php if (getSettingValue("AAProfTout")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='AAProfTout' style='cursor: pointer;'> a accès aux données d'années antérieures pour tous les <?php echo $gepiSettings['denomination_eleves']; ?></label></td>
			</tr>
		 */
$titreItem='AAProfTout';
$texteItem="a accès aux données d'années antérieures pour tous les ".$gepiSettings['denomination_eleves'];
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*
			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="AAProfClasses" id="AAProfClasses" value="yes" <?php if (getSettingValue("AAProfClasses")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='AAProfClasses' style='cursor: pointer;'> a accès aux données antérieures des <?php echo $gepiSettings['denomination_eleves']; ?> des classes pour lesquelles il fournit un enseignement<br />
				(<em>sans nécessairement avoir tous les <?php echo $gepiSettings['denomination_eleves']; ?> de la classe</em>)</label></td>
			</tr>
		 */
$titreItem='AAProfClasses';
$texteItem="a accès aux données antérieures des ".$gepiSettings['denomination_eleves']." des classes pour lesquelles il fournit un enseignement
			<br />(<em>sans nécessairement avoir tous les ".$gepiSettings['denomination_eleves']." de la classe</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*
			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="AAProfGroupes" id="AAProfGroupes" value="yes" <?php if (getSettingValue("AAProfGroupes")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='AAProfGroupes' style='cursor: pointer;'> a accès aux données antérieures des <?php echo $gepiSettings['denomination_eleves']; ?> des groupes auxquels il enseigne<br />
				(<em>il a ces <?php echo $gepiSettings['denomination_eleves']; ?> en classe</em>)
				</label></td>
			</tr>
		 */
$titreItem='AAProfGroupes';
$texteItem="a accès aux données antérieures des ".$gepiSettings['denomination_eleves']." des groupes auxquels il enseigne
		  <br />(<em>il a ces ".$gepiSettings['denomination_eleves']." en classe</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesGestElevesProf" id="GepiAccesGestElevesProf" value="yes" <?php if (getSettingValue("GepiAccesGestElevesProf")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesGestElevesProf' style='cursor: pointer;'> a accès aux fiches des <?php echo $gepiSettings['denomination_eleves']; ?> dont il est professeur.</label>
				</td>
			</tr>
		 */
$titreItem='GepiAccesGestElevesProf';
$texteItem="a accès aux fiches des ".$gepiSettings['denomination_eleves']." dont il est professeur.";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesModifMaPhotoProfesseur" id="GepiAccesModifMaPhotoProfesseur" value="yes" <?php if (getSettingValue("GepiAccesModifMaPhotoProfesseur")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesModifMaPhotoProfesseur' style='cursor: pointer;'> a le droit d'envoyer/modifier lui-même sa photo dans 'Gérer mon compte'
				</label></td>
			</tr>
		 */
$titreItem='GepiAccesModifMaPhotoProfesseur';
$texteItem="a le droit d'envoyer/modifier lui-même sa photo dans 'Gérer mon compte'";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<?php
				if(getSettingValue('active_mod_discipline')=='y') {
			?>
				<tr valign='top'>
					<td style='border: 0px;'><input type="checkbox" name="visuDiscProfClasses" id="visuDiscProfClasses" value="yes" <?php if (getSettingValue("visuDiscProfClasses")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
					<td style='border: 0px;'><label for='visuDiscProfClasses' style='cursor: pointer;'> peut visualiser dans le module Discipline les incidents concernant les élèves de ses classes.</label><br />
					(<span style='font-size: small;'>Par défaut un professeur ne voit que les incidents qu'il a déclaré ou le concernant directement comme protagoniste</span>)</td>
				</tr>

				<tr valign='top'>
					<td style='border: 0px;'><input type="checkbox" name="visuDiscProfGroupes" id="visuDiscProfGroupes" value="yes" <?php if (getSettingValue("visuDiscProfGroupes")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
					<td style='border: 0px;'><label for='visuDiscProfGroupes' style='cursor: pointer;'> peut visualiser dans le module Discipline les incidents concernant les élèves de ses enseignements.</label><br />
					(<span class='small'>Par défaut un professeur ne voit que les incidents qu'il a déclaré ou le concernant directement comme protagoniste</span>)</td>
				</tr>
			<?php
				}
			?>
		 */
if(getSettingValue('active_mod_discipline')=='y') {
  
  $titreItem='visuDiscProfClasses';
  $texteItem="peut visualiser dans le module Discipline les incidents concernant les élèves de ses classes.
	<br />(<em>Par défaut un professeur ne voit que les incidents qu'il a déclaré ou le concernant directement comme protagoniste)</em>";
  if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	$tbs_message = 'Erreur lors du chargement de '.$titreItem;
  
  $titreItem='visuDiscProfGroupes';
  $texteItem="peut visualiser dans le module Discipline les incidents concernant les élèves de ses enseignements.
	<br />(<em>Par défaut un professeur ne voit que les incidents qu'il a déclaré ou le concernant directement comme protagoniste)</em>";
  if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	$tbs_message = 'Erreur lors du chargement de '.$titreItem;

	if(getSettingValue('active_mod_ooo')=='y') {
		$titreItem='imprDiscProfRetenueOOo';
		$texteItem="peut imprimer dans le module Discipline une demande de Retenue au format OpenOffice pour un élève pour lequel le professeur saisit un incident";
		if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
			$tbs_message = 'Erreur lors du chargement de '.$titreItem;
	}
}
/*
      
      <!-- ECTS -->
			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesSaisieEctsProf" id="GepiAccesSaisieEctsProf" value="yes" <?php if (getSettingValue("GepiAccesSaisieEctsProf")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesSaisieEctsProf' style='cursor: pointer;'> a accès à la pré-saisie des mentions ECTS pour ses groupes.</label></td>
			</tr>
		 */
  $titreItem='GepiAccesSaisieEctsProf';
  $texteItem="a accès à la pré-saisie des mentions ECTS pour ses groupes.";
  if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	$tbs_message = 'Erreur lors du chargement de '.$titreItem;
/* 

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesRecapitulatifEctsProf" id="GepiAccesRecapitulatifEctsProf" value="yes" <?php if (getSettingValue("GepiAccesRecapitulatifEctsProf")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesRecapitulatifEctsProf' style='cursor: pointer;'> a accès aux récapitulatifs globaux des crédits ECTS pour ses classes.</label></td>
			</tr>
			</table>
		</td>
	</tr>
		 */
  $titreItem='GepiAccesRecapitulatifEctsProf';
  $texteItem="a accès aux récapitulatifs globaux des crédits ECTS pour ses classes.";
  if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	$tbs_message = 'Erreur lors du chargement de '.$titreItem;

if(getSettingValue('active_mod_ooo')=='y') {
  $titreItem='OOoUploadProf';
  $texteItem="a accès à l'upload de fichiers modèles OpenOffice personnels.";
  if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	$tbs_message = 'Erreur lors du chargement de '.$titreItem;
}
//=======================================================================================
/*
 *
 *
 *
 *
	<tr>
		<!-- Professeur principal-->
		<td style="font-variant: small-caps;">Un <?php echo getSettingValue("gepi_prof_suivi"); ?></td>
		<td>
			<table border='0' summary='Professeur principal'>
			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiRubConseilProf" id="GepiRubConseilProf" value="yes" <?php if (getSettingValue("GepiRubConseilProf")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiRubConseilProf' style='cursor: pointer;'> peut saisir les avis du conseil de classe pour sa classe</label></td>
			</tr>
		 */
$statutItem="professeur_principal";
$titreItem='GepiRubConseilProf';
$texteItem="peut saisir les avis du conseil de classe pour sa classe";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="CommentairesTypesPP" id="CommentairesTypesPP" value="yes" <?php if (getSettingValue("CommentairesTypesPP")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='CommentairesTypesPP' style='cursor: pointer;'> peut utiliser des commentaires-types dans ses saisies d'avis du conseil de classe<br />(<em>sous réserve de pouvoir saisir les avis du conseil de classe</em>)</label></td>
			</tr>
		 */
$titreItem='CommentairesTypesPP';
$texteItem="peut utiliser des commentaires-types dans ses saisies d'avis du conseil de classe
	  <br />(<em>sous réserve de pouvoir saisir les avis du conseil de classe</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiProfImprBul" id="GepiProfImprBul" value="yes" <?php if (getSettingValue("GepiProfImprBul")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiProfImprBul' style='cursor: pointer;'> édite/imprime les bulletins périodiques des classes dont il a la charge.<br />
				<span class='small'>(<em>Par défaut, seul un utilisateur ayant le statut scolarité peut éditer les bulletins</em>)</span></label></td>
			</tr>
		 */
$titreItem='GepiProfImprBul';
$texteItem="édite/imprime les bulletins périodiques des classes dont il a la charge.<br />
			(<em>Par défaut, seul un utilisateur ayant le statut scolarité peut éditer les bulletins</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiProfImprBulSettings" id="GepiProfImprBulSettings" value="yes" <?php if (getSettingValue("GepiProfImprBulSettings")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiProfImprBulSettings' style='cursor: pointer;'> a accès au paramétrage de l'impression des bulletins (<em>lorsqu'il est autorisé à éditer/imprimer les bulletins</em>)</label></td>
			</tr>
		 */
$titreItem='GepiProfImprBulSettings';
$texteItem="a accès au paramétrage de l'impression des bulletins (<em>lorsqu'il est autorisé à éditer/imprimer les bulletins</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;



$titreItem='autoriser_signalement_faute_app_pp';
$texteItem="peut signaler, en période ouverte ou partiellement close, (<em>à ses collègues professeurs</em>) des fautes (<em>de frappe;</em>) dans les appréciations des bulletins (<em>pour leur permettre corriger avant impression des bulletins</em>).";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;


/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesRestrAccesAppProfP" id="GepiAccesRestrAccesAppProfP" value="yes" <?php if (getSettingValue("GepiAccesRestrAccesAppProfP")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesRestrAccesAppProfP' style='cursor: pointer;'> a accès au paramétrage des accès <?php echo $gepiSettings['denomination_responsables']; ?>/<?php echo $gepiSettings['denomination_eleves']; ?> aux appréciations/avis des classes dont il est <?php echo getSettingValue("gepi_prof_suivi"); ?></label></td>
			</tr>
		 */
$titreItem='GepiAccesRestrAccesAppProfP';
$texteItem="a accès au paramétrage des accès ".$gepiSettings['denomination_responsables']."/".$gepiSettings['denomination_eleves']." aux appréciations/avis des classes dont il est ".getSettingValue("gepi_prof_suivi");
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesBulletinSimplePP" id="GepiAccesBulletinSimplePP" value="yes" <?php if (getSettingValue("GepiAccesBulletinSimplePP")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesBulletinSimplePP' style='cursor: pointer;'> a accès aux bulletins simples des <?php echo $gepiSettings['denomination_eleves']; ?> dont il est <?php echo getSettingValue("gepi_prof_suivi");?></label></td>
			</tr>
		 */
$titreItem='GepiAccesBulletinSimplePP';
$texteItem="a accès aux bulletins simples des ".$gepiSettings['denomination_eleves']." dont il est ".getSettingValue("gepi_prof_suivi");
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesReleveProfP" id="GepiAccesReleveProfP" value="yes" <?php if (getSettingValue("GepiAccesReleveProfP")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesReleveProfP' style='cursor: pointer;'> a accès aux relevés des classes dont il est <?php echo getSettingValue("gepi_prof_suivi"); ?></label></td>
			</tr>
		 */
$titreItem='GepiAccesReleveProfP';
$texteItem="a accès aux relevés des classes dont il est ".getSettingValue("gepi_prof_suivi");
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesGestElevesProfP" id="GepiAccesGestElevesProfP" value="yes" <?php if (getSettingValue("GepiAccesGestElevesProfP")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesGestElevesProfP' style='cursor: pointer;'> a accès aux fiches des <?php echo $gepiSettings['denomination_eleves']; ?> dont il est <?php echo getSettingValue("gepi_prof_suivi"); ?></label>
				</td>
			</tr>
		 */
$titreItem='GepiAccesGestElevesProfP';
$texteItem="a accès aux fiches des ".$gepiSettings['denomination_eleves']." dont il est ".getSettingValue("gepi_prof_suivi");
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'>
				<input type="checkbox" name="GepiAccesGestPhotoElevesProfP" id="GepiAccesGestPhotoElevesProfP" value="yes" <?php if (getSettingValue("GepiAccesGestPhotoElevesProfP")=='yes') echo 'checked="checked"'; ?> onchange='changement();' />
				</td>
				<td style='border: 0px;'><label for='GepiAccesGestPhotoElevesProfP' style='cursor: pointer;'>
				 a accès à l'upload des photos de ses <?php echo $gepiSettings['denomination_eleves']; ?> si le module trombinoscope est activé et si le <?php echo $gepiSettings['denomination_professeur']; ?> a accès aux fiches <?php echo $gepiSettings['denomination_eleves']; ?> (<em>ci-dessus</em>).</label></td>
			</tr>
		 */
$titreItem='GepiAccesGestPhotoElevesProfP';
$texteItem="a accès à l'upload des photos de ses ".$gepiSettings['denomination_eleves']." si le module
  		trombinoscope est activé et si le ".$gepiSettings['denomination_professeur']." a accès aux fiches
		".$gepiSettings['denomination_eleves']." (<em>ci-dessus</em>).";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<!-- Années antérieures -->
			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="AAProfPrinc" id="AAProfPrinc" value="yes" <?php if (getSettingValue("AAProfPrinc")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='AAProfPrinc' style='cursor: pointer;'> a accès aux données d'années antérieures des <?php echo $gepiSettings['denomination_eleves']; ?> dont il est <?php echo $gepiSettings['denomination_professeur']; ?> principal</label></td>
			</tr>
		 */
$titreItem='AAProfPrinc';
$texteItem="a accès aux données d'années antérieures des ".$gepiSettings['denomination_eleves']." dont il est ".$gepiSettings['denomination_professeur']." principal";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*
      
      <!-- ECTS -->
      <tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesSaisieEctsPP" id="GepiAccesSaisieEctsPP" value="yes" <?php if (getSettingValue("GepiAccesSaisieEctsPP")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesSaisieEctsPP' style='cursor: pointer;'> peut saisir les crédits ECTS pour sa classe</label></td>
			</tr>
		 */
$titreItem='GepiAccesSaisieEctsPP';
$texteItem="peut saisir les crédits ECTS pour sa classe";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

 *  <tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesEditionDocsEctsPP" id="GepiAccesEditionDocsEctsPP" value="yes" <?php if (getSettingValue("GepiAccesEditionDocsEctsPP")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesEditionDocsEctsPP' style='cursor: pointer;'> peut éditer les relevés ECTS pour sa classe</label></td>
			</tr>
			</table>
		</td>
	</tr>
		 */
$titreItem='GepiAccesEditionDocsEctsPP';
$texteItem="peut éditer les relevés ECTS pour sa classe";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;



$titreItem='modExbPP';
$texteItem="peut créer des examens blancs pour les classes dont il est ".getSettingValue('gepi_prof_suivi');
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;


/*

 * <tr>
		<td style="font-variant: small-caps;">La scolarité</td>
		<td>
			<table border='0' summary='Scolarité'>
			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiRubConseilScol" id="GepiRubConseilScol" value="yes" <?php if (getSettingValue("GepiRubConseilScol")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiRubConseilScol' style='cursor: pointer;'> peut saisir les avis du conseil de classe</label></td>
			</tr>
		 */
$statutItem="scolarite";
$titreItem='GepiRubConseilScol';
$texteItem="peut saisir les avis du conseil de classe";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="CommentairesTypesScol" id="CommentairesTypesScol" value="yes" <?php if (getSettingValue("CommentairesTypesScol")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='CommentairesTypesScol' style='cursor: pointer;'> peut utiliser des commentaires-types dans ses saisies d'avis du conseil de classe<br />(<em>sous réserve de pouvoir saisir les avis du conseil de classe</em>)</label></td>
			</tr>
		 */
$titreItem='CommentairesTypesScol';
$texteItem="peut utiliser des commentaires-types dans ses saisies d'avis du conseil de classe<br />
			(<em>sous réserve de pouvoir saisir les avis du conseil de classe</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiScolImprBulSettings" id="GepiScolImprBulSettings" value="yes" <?php if (getSettingValue("GepiScolImprBulSettings")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiScolImprBulSettings' style='cursor: pointer;'> a accès au paramétrage de l'impression des bulletins</label></td>
			</tr>
		 */
$titreItem='GepiScolImprBulSettings';
$texteItem="a accès au paramétrage de l'impression des bulletins";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;


$titreItem='autoriser_signalement_faute_app_scol';
$texteItem="peut signaler, en période ouverte ou partiellement close, (<em>aux professeurs concernés</em>) des fautes (<em>de frappe;</em>) dans les appréciations des bulletins (<em>pour leur permettre corriger avant impression des bulletins</em>).";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;


/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesReleveScol" id="GepiAccesReleveScol" value="yes" <?php if (getSettingValue("GepiAccesReleveScol")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesReleveScol' style='cursor: pointer;'> a accès à tous les relevés de notes de toutes les classes</label></td>
			</tr>
		 */
$titreItem='GepiAccesReleveScol';
$texteItem="a accès à tous les relevés de notes de toutes les classes";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesCdtScol" id="GepiAccesCdtScol" value="yes" <?php if (getSettingValue("GepiAccesCdtScol")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesCdtScol' style='cursor: pointer;'> a accès à tous les cahiers de textes</label></td>
			</tr>
		 */
$titreItem='GepiAccesCdtScol';
$texteItem="a accès à tous les cahiers de textes";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesCdtScolRestreint" id="GepiAccesCdtScolRestreint" value="yes" <?php if (getSettingValue("GepiAccesCdtScolRestreint")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesCdtScolRestreint' style='cursor: pointer;'> a accès aux cahiers de textes des <?php echo $gepiSettings['denomination_eleves']; ?> dont il a la responsabilité<br /><em>bloque l'affichage des cahiers de textes de toutes les classes</em></label></td>
			</tr>
		 */
$titreItem='GepiAccesCdtScolRestreint';
$texteItem="a accès aux cahiers de textes des ".$gepiSettings['denomination_eleves']." dont il a la responsabilité<br />
			<em>bloque l'affichage des cahiers de textes de toutes les classes</em>";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

// ====== Visa des cahiers de texte =====
$titreItem='GepiAccesCdtVisa';
$texteItem="Peut viser les cahiers de textes ";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

// ====== Droits sur la page cahiers de texte =====
if (getSettingValue('GepiAccesCdtScolRestreint') =='yes'
		||getSettingValue('GepiAccesCdtScol')=='yes'
		||getSettingValue('GepiAccesCdtVisa')=='yes'){
  // il faut pouvoir voir les cahiers de textes
  if (!$droitAffiche->ouvreDroits($statutItem, '', "/cahier_texte_2/see_all.php",'yes'))
	$tbs_message = "Erreur lors de l'enregistrement des droits de /cahier_texte_2/see_all.php";
  if (!$droitAffiche->ouvreDroits($statutItem, '', "/cahier_texte/see_all.php",'yes'))
	$tbs_message = "Erreur lors de l'enregistrement des droits de /cahier_texte/see_all.php";
} else {
  // il ne faut pas pouvoir voir les cahiers de textes même en accès direct à la page
  if (!$droitAffiche->ouvreDroits($statutItem, '', "/cahier_texte_2/see_all.php",'no'))
	$tbs_message = "Erreur lors de l'enregistrement des droits de /cahier_texte_2/see_all.php";
  if (!$droitAffiche->ouvreDroits($statutItem, '', "/cahier_texte/see_all.php",'no'))
	$tbs_message = "Erreur lors de l'enregistrement des droits de /cahier_texte/see_all.php";
}

// ====== Droits sur la page Visa des cahiers de texte =====
if (!$droitAffiche->ouvreDroits($statutItem, $titreItem, "/cahier_texte_admin/visa_ct.php"))
  $tbs_message = "Erreur lors de l'enregistrement des droits de ".$titreItem;



/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiPasswordReinitScolarite" id="GepiPasswordReinitScolarite" value="yes" <?php if (getSettingValue("GepiPasswordReinitScolarite")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiPasswordReinitScolarite' style='cursor: pointer;'> peut réinitialiser elle-même son mot de passe perdu (<em>si fonction activée</em>)</label></td>
			</tr>
		 */
$titreItem='GepiPasswordReinitScolarite';
$texteItem="peut réinitialiser elle-même son mot de passe perdu (<em>si fonction activée</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesVisuToutesEquipScol" id="GepiAccesVisuToutesEquipScol" value="yes" <?php if (getSettingValue("GepiAccesVisuToutesEquipScol")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesVisuToutesEquipScol' style='cursor: pointer;'> a accès à la Visualisation de toutes les équipes</label></td>
			</tr>
		 */
$titreItem='GepiAccesVisuToutesEquipScol';
$texteItem="a accès à la Visualisation de toutes les équipes";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<!-- Années antérieures -->
			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="AAScolTout" id="AAScolTout" value="yes" <?php if (getSettingValue("AAScolTout")=='yes') echo 'checked="checked"'; ?> /></td>
				<td style='border: 0px;'><label for='AAScolTout' style='cursor: pointer;'> a accès aux données d'années antérieures de tous les <?php echo $gepiSettings['denomination_eleves']; ?></label></td>
			</tr>
		 */
$titreItem='AAScolTout';
$texteItem="a accès aux données d'années antérieures de tous les ".$gepiSettings['denomination_eleves'];
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*
			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="AAScolResp" id="AAScolResp" value="yes" <?php if (getSettingValue("AAScolResp")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='AAScolResp' style='cursor: pointer;'> a accès aux données d'années antérieures des <?php echo $gepiSettings['denomination_eleves']; ?> des classes dont il est responsable</label></td>
			</tr>
		 */
$titreItem='AAScolResp';
$texteItem="a accès aux données d'années antérieures des ".$gepiSettings['denomination_eleves']." des classes dont il est responsable";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesModifMaPhotoScolarite" id="GepiAccesModifMaPhotoScolarite" value="yes" <?php if (getSettingValue("GepiAccesModifMaPhotoScolarite")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesModifMaPhotoScolarite' style='cursor: pointer;'> a le droit d'envoyer/modifier lui-même sa photo dans 'Gérer mon compte'
				</label></td>
			</tr>
		 */
$titreItem='GepiAccesModifMaPhotoScolarite';
$texteItem="a le droit d'envoyer/modifier lui-même sa photo dans 'Gérer mon compte'";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesTouteFicheEleveScolarite';
$texteItem="a le droit d'accéder à toutes les fiches élève";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

/*

           	<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesSaisieEctsScolarite" id="GepiAccesSaisieEctsScolarite" value="yes" <?php if (getSettingValue("GepiAccesSaisieEctsScolarite")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesSaisieEctsScolarite' style='cursor: pointer;'> peut saisir les crédits ECTS</label></td>
			</tr>
		 */
$titreItem='GepiAccesSaisieEctsScolarite';
$texteItem="peut saisir les crédits ECTS";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

           	<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesEditionDocsEctsScolarite" id="GepiAccesEditionDocsEctsScolarite" value="yes" <?php if (getSettingValue("GepiAccesEditionDocsEctsScolarite")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesEditionDocsEctsScolarite' style='cursor: pointer;'> peut éditer les relevés d'ECTS</label></td>
			</tr>
		 */
$titreItem='GepiAccesEditionDocsEctsScolarite';
$texteItem="peut éditer les relevés d'ECTS";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

      <tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesRecapitulatifEctsScolarite" id="GepiAccesRecapitulatifEctsScolarite" value="yes" <?php if (getSettingValue("GepiAccesRecapitulatifEctsScolarite")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesRecapitulatifEctsScolarite' style='cursor: pointer;'> a accès aux récapitulatifs globaux des crédits ECTS.</label></td>
			</tr>

		 */
$titreItem='GepiAccesRecapitulatifEctsScolarite';
$texteItem="a accès aux récapitulatifs globaux des crédits ECTS.";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;


if(getSettingValue('active_mod_ooo')=='y') {
  $titreItem='OOoUploadScol';
  $texteItem="a accès à l'upload de fichiers modèles OpenOffice personnels.";
  if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	$tbs_message = 'Erreur lors du chargement de '.$titreItem;
}
//=======================================================================================
// CPE
$statutItem="cpe";

// Relevés de notes
$titreItem='GepiAccesReleveCpe';
$texteItem="a accès aux relevés de notes des élèves qu'il a en responsabilité";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesReleveCpeTousEleves';
$texteItem="a accès à tous les relevés de notes de toutes les classes";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

// Bulletins
$titreItem='GepiCpeImprBul';
$texteItem="édite/imprime les bulletins périodiques des classes dont il a la charge.<br />
			(<em>Par défaut, seul un utilisateur ayant le statut scolarité peut éditer les bulletins</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiCpeImprBulSettings';
$texteItem="a accès au paramétrage de l'impression des bulletins (<em>lorsqu'il est autorisé à éditer/imprimer les bulletins</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

// CDT
$titreItem='GepiAccesCdtCpe';
$texteItem="a accès aux cahiers de textes";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesCdtCpeRestreint';
$texteItem="a accès aux cahiers de textes des ".$gepiSettings['denomination_eleves']." dont il a la responsabilité<br />
			<em>bloque l'affichage des cahiers de textes de toutes les classes</em>";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;


// Bulletins
$titreItem='autoriser_signalement_faute_app_cpe';
$texteItem="peut signaler, en période ouverte ou partiellement close, (<em>aux professeurs concernés</em>) des fautes (<em>de frappe;</em>) dans les appréciations des bulletins (<em>pour leur permettre corriger avant impression des bulletins</em>).";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;


// Avis du conseil de classe
$titreItem='GepiRubConseilCpe';
$texteItem="peut saisir les avis du conseil de classe (<em>pour les élèves qu'il a en responsabilité</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiRubConseilCpeTous';
$texteItem="peut saisir les avis du conseil de classe (<em>pour tous les élèves de toutes les classes</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='CommentairesTypesCpe';
$texteItem="peut utiliser des commentaires-types dans ses saisies d'avis du conseil de classe<br />
			(<em>sous réserve de pouvoir saisir les avis du conseil de classe</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;


// Divers
$titreItem='GepiAccesVisuToutesEquipCpe';
$texteItem="a accès à la Visualisation de toutes les équipes";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;


//Années antérieures
$titreItem='AACpeTout';
$texteItem="a accès aux données d'années antérieures de tous les ".$gepiSettings['denomination_eleves'];
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='AACpeResp';
$texteItem="a accès aux données d'années antérieures des ".$gepiSettings['denomination_eleves']." dont il est responsable";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;


// Mon compte
$titreItem='GepiPasswordReinitCpe';
$texteItem="peut réinitialiser lui-même son mot de passe perdu (<em>si fonction activée</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesModifMaPhotoCpe';
$texteItem="a le droit d'envoyer/modifier lui-même sa photo dans 'Gérer mon compte'";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;


// Photos
$titreItem='GepiAccesTouteFicheEleveCpe';
$texteItem="a le droit d'accéder à toutes les fiches élève";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='CpeAccesUploadPhotosEleves';
$texteItem="a accès à l'upload des photos des ".$gepiSettings['denomination_eleves']."<br />sous réserve d'activation du module Trombinoscopes (<em>";
if(!getSettingAOui('active_module_trombinoscopes')) {
	$texteItem.="in";
}
$texteItem.="actif</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;


// Absences
$titreItem='GepiAccesAbsTouteClasseCpe';
$texteItem="a le droit d'accéder à toutes les classes pour saisir les absences";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;


// OOo
if(getSettingValue('active_mod_ooo')=='y') {
  $titreItem='OOoUploadCpe';
  $texteItem="a accès à l'upload de fichiers modèles OpenOffice personnels.";
  if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	$tbs_message = 'Erreur lors du chargement de '.$titreItem;
}
//=======================================================================================

/*

			</table>
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">Un administrateur</td>
		<td>
			<table border='0' summary='Administrateur'>
			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAdminImprBulSettings" id="GepiAdminImprBulSettings" value="yes" <?php if (getSettingValue("GepiAdminImprBulSettings")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAdminImprBulSettings' style='cursor: pointer;'> a accès au paramétrage de l'impression des bulletins</label></td>
			</tr>
		 */
$statutItem="administrateur";
$titreItem='GepiAdminImprBulSettings';
$texteItem="a accès au paramétrage de l'impression des bulletins";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiPasswordReinitAdmin" id="GepiPasswordReinitAdmin" value="yes" <?php if (getSettingValue("GepiPasswordReinitAdmin")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiPasswordReinitAdmin' style='cursor: pointer;'> peut réinitialiser lui-même son mot de passe perdu (<em>si fonction activée</em>)</label></td>
			</tr>
		 */
$titreItem='GepiPasswordReinitAdmin';
$texteItem="peut réinitialiser lui-même son mot de passe perdu (<em>si fonction activée</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesModifMaPhotoAdministrateur" id="GepiAccesModifMaPhotoAdministrateur" value="yes" <?php if (getSettingValue("GepiAccesModifMaPhotoAdministrateur")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesModifMaPhotoAdministrateur' style='cursor: pointer;'> a le droit d'envoyer/modifier lui-même sa photo dans 'Gérer mon compte'
				</label></td>
			</tr>
			</table>
		</td>
	</tr>
		 */
$titreItem='GepiAccesModifMaPhotoAdministrateur';
$texteItem="a le droit d'envoyer/modifier lui-même sa photo dans 'Gérer mon compte'";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

	<tr>
		<td style="font-variant: small-caps;">Un <?php echo $gepiSettings['denomination_eleve']; ?></td>
		<td>
			<table border='0' summary='Elève'>
			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesReleveEleve" id="GepiAccesReleveEleve" value="yes" <?php if (getSettingValue("GepiAccesReleveEleve")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesReleveEleve' style='cursor: pointer;'> a accès à ses relevés de notes</label></td>
			</tr>
		 */
$statutItem='eleve';
$titreItem='autorise_edt_eleve';
$texteItem="a accès à son emploi du temps (ouvre également le droit aux parents)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesReleveEleve';
$texteItem="a accès à ses relevés de notes";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesOptionsReleveEleve" id="GepiAccesOptionsReleveEleve" value="yes" <?php if (getSettingValue("GepiAccesOptionsReleveEleve")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesOptionsReleveEleve' style='cursor: pointer;'> a accès aux options du relevés de notes (<em>nom court, coef, date des devoirs, ...</em>)</label></td>
			</tr>
		 */
$titreItem='GepiAccesOptionsReleveEleve';
$texteItem="a accès aux options du relevés de notes (<em>nom court, coef, date des devoirs, ...</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*


			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesCahierTexteEleve" id="GepiAccesCahierTexteEleve" value="yes" <?php if (getSettingValue("GepiAccesCahierTexteEleve")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesCahierTexteEleve' style='cursor: pointer;'> a accès à son cahier de texte</label></td>
			</tr>
		 */
$titreItem='GepiAccesCahierTexteEleve';
$texteItem="a accès à son cahier de texte";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiPasswordReinitEleve" id="GepiPasswordReinitEleve" value="yes" <?php if (getSettingValue("GepiPasswordReinitEleve")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiPasswordReinitEleve' style='cursor: pointer;'> peut réinitialiser lui-même son mot de passe perdu (<em>si fonction activée</em>)</label></td>
			</tr>
		 */
$titreItem='GepiPasswordReinitEleve';
$texteItem="peut réinitialiser lui-même son mot de passe perdu (<em>si fonction activée</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesEquipePedaEleve" id="GepiAccesEquipePedaEleve" value="yes" <?php if (getSettingValue("GepiAccesEquipePedaEleve")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesEquipePedaEleve' style='cursor: pointer;'> a accès à l'équipe pédagogique le concernant</label></td>
			</tr>
		 */
$titreItem='GepiAccesEquipePedaEleve';
$texteItem="a accès à l'équipe pédagogique le concernant";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesCpePPEmailEleve" id="GepiAccesCpePPEmailEleve" value="yes" <?php if (getSettingValue("GepiAccesCpePPEmailEleve")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesCpePPEmailEleve' style='cursor: pointer;'> a accès aux adresses email de son CPE et de son professeur principal (<em>paramètre utile seulement si le paramètre suivant est décoché</em>)</label></td>
			</tr>
		 */
$titreItem='GepiAccesCpePPEmailEleve';
$texteItem="a accès aux adresses email de son CPE et de son professeur principal (<em>paramètre utile seulement si le paramètre suivant est décoché</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesEquipePedaEmailEleve" id="GepiAccesEquipePedaEmailEleve" value="yes" <?php if (getSettingValue("GepiAccesEquipePedaEmailEleve")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesEquipePedaEmailEleve' style='cursor: pointer;'> a accès aux adresses email de l'équipe pédagogique le concernant</label></td>
			</tr>
		 */
$titreItem='GepiAccesEquipePedaEmailEleve';
$texteItem="a accès aux adresses email de l'équipe pédagogique le concernant";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;' valign='top'><input type="checkbox" name="GepiAccesBulletinSimpleEleve" id="GepiAccesBulletinSimpleEleve" value="yes" <?php if (getSettingValue("GepiAccesBulletinSimpleEleve")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /><a name='bull_simp_ele'></a></td>
				<td style='border: 0px;'>
				<label for='GepiAccesBulletinSimpleEleve' style='cursor: pointer;'> a accès à ses bulletins simplifiés</label>
				<br />
				<?php
					$acces_app_ele_resp=getSettingValue('acces_app_ele_resp');
					if($acces_app_ele_resp=="") {$acces_app_ele_resp='manuel';}
					$delais_apres_cloture=getSettingValue('delais_apres_cloture');
					if(!my_ereg("^[0-9]*$",$delais_apres_cloture)) {$delais_apres_cloture=0;}

					echo "<span style='font-size:x-small'>";
					if($acces_app_ele_resp=='manuel') {
						echo "L'accès aux appréciations est donné manuellement dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Accès aux appréciations et avis du conseil</a>.";
					}
					elseif($acces_app_ele_resp=='date') {
						echo "L'accès aux appréciations est ouvert à la date saisie dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Accès aux appréciations et avis du conseil</a>.";
					}
					elseif($acces_app_ele_resp=='periode_close') {
						echo "L'accès aux appréciations est ouvert automatiquement ";
						if($delais_apres_cloture>0) {echo $delais_apres_cloture." jours après ";}
						echo "la clôture de la période par un compte scolarité.";
					}
					echo "</span>";
					echo "<br />";
					echo "<span style='font-size:x-small'>";
					echo "Le mode d'ouverture de l'accès se paramètre en <a href='param_gen.php#mode_ouverture_acces_appreciations'  onclick=\"return confirm_abandon (this, change, '$themessage')\">Gestion générale/Configuration générale</a>";
					echo "</span>";

				?>
				</td>
			</tr>
		 */
$titreItem='GepiAccesBulletinSimpleEleve';
$texteItem="a accès à ses bulletins simplifiés<br />";
  $acces_app_ele_resp=getSettingValue('acces_app_ele_resp');
  if($acces_app_ele_resp=="") {$acces_app_ele_resp='manuel';}
  $delais_apres_cloture=getSettingValue('delais_apres_cloture');
  if(!my_ereg("^[0-9]*$",$delais_apres_cloture)) {$delais_apres_cloture=0;}
  $texteItem.="<em>";
  if($acces_app_ele_resp=='manuel') {
	$texteItem.="L'accès aux appréciations est donné manuellement dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon(this, change, '$themessage')\">Accès aux appréciations et avis du conseil</a>.<br />";
  } elseif($acces_app_ele_resp=='date') {
	$texteItem.="L'accès aux appréciations est ouvert à la date saisie dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon(this, change, '$themessage')\">Accès aux appréciations et avis du conseil</a>.<br />";
  } elseif($acces_app_ele_resp=='periode_close') {
	$texteItem.= "L'accès aux appréciations est ouvert automatiquement ";
	if($delais_apres_cloture>0) {$texteItem.=$delais_apres_cloture." jours après ";}
	$texteItem.= "la clôture de la période par un compte scolarité.";
	$texteItem.= "<br />";
  }
  $texteItem.= "Le mode d'ouverture de l'accès se paramètre en <a href='param_gen.php#mode_ouverture_acces_appreciations'  onclick=\"return confirm_abandon(this, change, '$themessage')\">Gestion générale/Configuration générale</a>";
  $texteItem.= "</em>";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;' valign='top'><input type="checkbox" name="GepiAccesGraphEleve" id="GepiAccesGraphEleve" value="yes" <?php if (getSettingValue("GepiAccesGraphEleve")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesGraphEleve' style='cursor: pointer;'> a accès à la visualisation graphique de ses résultats</label>
				<br />
				<?php

					echo "<span style='font-size:x-small'>";
					if($acces_app_ele_resp=='manuel') {
						echo "L'accès aux appréciations est donné manuellement dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Accès aux appréciations et avis du conseil</a>.";
					}
					elseif($acces_app_ele_resp=='date') {
						echo "L'accès aux appréciations est ouvert à la date saisie dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Accès aux appréciations et avis du conseil</a>.";
					}
					elseif($acces_app_ele_resp=='periode_close') {
						echo "L'accès aux appréciations est ouvert automatiquement ";
						if($delais_apres_cloture>0) {echo $delais_apres_cloture." jours après ";}
						echo "la clôture de la période par un compte scolarité.";
					}
					echo "</span>";
					echo "<br />";
					echo "<span style='font-size:x-small'>";
					echo "Le mode d'ouverture de l'accès se paramètre en <a href='param_gen.php#mode_ouverture_acces_appreciations'  onclick=\"return confirm_abandon (this, change, '$themessage')\">Gestion générale/Configuration générale</a>";
					echo "</span>";

				?>
				</td>
			</tr>
		 */
$titreItem='GepiAccesGraphEleve';
$texteItem="a accès à la visualisation graphique de ses résultats<br />";
$texteItem.= "<em>";
if($acces_app_ele_resp=='manuel') {
  $texteItem.= "L'accès aux appréciations est donné manuellement dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon(this, change, '$themessage')\">Accès aux appréciations et avis du conseil</a>.";
} elseif($acces_app_ele_resp=='date') {
  $texteItem.= "L'accès aux appréciations est ouvert à la date saisie dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon(this, change, '$themessage')\">Accès aux appréciations et avis du conseil</a>.";
} elseif($acces_app_ele_resp=='periode_close') {
  $texteItem.= "L'accès aux appréciations est ouvert automatiquement ";
  if($delais_apres_cloture>0) {$texteItem.= $delais_apres_cloture." jours après ";}
  $texteItem.= "la clôture de la période par un compte scolarité.";
}
  $texteItem.= "<br />";
  $texteItem.= "Le mode d'ouverture de l'accès se paramètre en <a href='param_gen.php#mode_ouverture_acces_appreciations'  onclick=\"return confirm_abandon(this, change, '$themessage')\">Gestion générale/Configuration générale</a>";
  $texteItem.= "</em>";

if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<!-- Années antérieures -->
			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="AAEleve" id="AAEleve" value="yes" <?php if (getSettingValue("AAEleve")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='AAEleve' style='cursor: pointer;'> a accès à ses données d'années antérieures</label></td>
			</tr>
		 */
$titreItem='AAEleve';
$texteItem="a accès à ses données d'années antérieures";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesModifMaPhotoEleve" id="GepiAccesModifMaPhotoEleve" value="yes" <?php if (getSettingValue("GepiAccesModifMaPhotoEleve")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesModifMaPhotoEleve' style='cursor: pointer;'> a le droit d'envoyer/modifier lui-même sa photo dans 'Gérer mon compte'
				<br /><em>(voir aussi le module de gestion du trombinoscope pour une gestion plus fine des droits d'acc&egrave;s)</em>
				</label></td>
			</tr>
		 */
$titreItem='GepiAccesModifMaPhotoEleve';
$texteItem="a le droit d'envoyer/modifier lui-même sa photo dans 'Gérer mon compte'
				<br /><em>(voir aussi le module de gestion du trombinoscope pour une gestion plus fine des droits d'accès)</em>";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesEleTrombiTousEleves" id="GepiAccesEleTrombiTousEleves" value="yes" <?php if (getSettingValue("GepiAccesEleTrombiTousEleves")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesEleTrombiTousEleves' style='cursor: pointer;'> a accès au trombinoscope de tous les <?php echo $gepiSettings['denomination_eleves']; ?> de l'établissement.<br />
				<em>(sous réserve que le module Trombinoscope-élève soit activé.<br />voir aussi le module de gestion du trombinoscope pour une gestion plus fine des droits d'acc&egrave;s)</em>
				</label></td>
			</tr>
		 */
$titreItem='GepiAccesEleTrombiTousEleves';
$texteItem="a accès au trombinoscope de tous les ".$gepiSettings['denomination_eleves']." de l'établissement.<br />
				<em>(sous réserve que le module Trombinoscope-élève soit activé.<br />voir aussi le module de gestion du trombinoscope pour une gestion plus fine des droits d'accès)</em>";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesEleTrombiElevesClasse" id="GepiAccesEleTrombiElevesClasse" value="yes" <?php if (getSettingValue("GepiAccesEleTrombiElevesClasse")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesEleTrombiElevesClasse' style='cursor: pointer;'> a accès au trombinoscope des <?php echo $gepiSettings['denomination_eleves']; ?> de sa classe.<br />
				<em>(sous réserve que le module Trombinoscope-élève soit activé.<br />voir aussi le module de gestion du trombinoscope pour une gestion plus fine des droits d'acc&egrave;s)</em>
				</label></td>
			</tr>
		 */
$titreItem='GepiAccesEleTrombiElevesClasse';
$texteItem="a accès au trombinoscope des ".$gepiSettings['denomination_eleves']." de sa classe.<br />
				<em>(sous réserve que le module Trombinoscope-élève soit activé.<br />
				voir aussi le module de gestion du trombinoscope pour une gestion plus fine des droits d'accès)</em>";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesEleTrombiPersonnels" id="GepiAccesEleTrombiPersonnels" value="yes" <?php if (getSettingValue("GepiAccesEleTrombiPersonnels")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesEleTrombiPersonnels' style='cursor: pointer;'> a accès au trombinoscope de tous les personnels de l'établissement.<br />
				<em>(sous réserve que le module Trombinoscope-personnels soit activé.<br />voir aussi le module de gestion du trombinoscope pour une gestion plus fine des droits d'acc&egrave;s)</em>
				</label></td>
			</tr>
		 */
$titreItem='GepiAccesEleTrombiPersonnels';
$texteItem="a accès au trombinoscope de tous les personnels de l'établissement.<br />
			<em>(sous réserve que le module Trombinoscope-personnels soit activé.<br />
			voir aussi le module de gestion du trombinoscope pour une gestion plus fine des droits d'accès)</em>";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesEleTrombiProfsClasse" id="GepiAccesEleTrombiProfsClasse" value="yes" <?php if (getSettingValue("GepiAccesEleTrombiProfsClasse")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesEleTrombiProfsClasse' style='cursor: pointer;'> a accès au trombinoscope des <?php echo $gepiSettings['denomination_professeurs']; ?> de sa classe.<br />
				<em>(sous réserve que le module Trombinoscope-personnels soit activé.<br />voir aussi le module de gestion du trombinoscope pour une gestion plus fine des droits d'acc&egrave;s)</em>
				</label></td>
			</tr>
		 */
$titreItem='GepiAccesEleTrombiProfsClasse';
$texteItem="a accès au trombinoscope des ".$gepiSettings['denomination_professeurs']." de sa classe.<br />
				<em>(sous réserve que le module Trombinoscope-personnels soit activé.<br />
				voir aussi le module de gestion du trombinoscope pour une gestion plus fine des droits d'accès)</em>";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;




$titreItem='visuEleDisc';
$texteItem="a accès dans le module Discipline aux incidents le concernant.";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;


/*

			</table>
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">Un <?php echo $gepiSettings['denomination_responsable']; ?></td>
		<td>
			<table border='0' summary='Responsable'>
			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesReleveParent" id="GepiAccesReleveParent" value="yes" <?php if (getSettingValue("GepiAccesReleveParent")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesReleveParent' style='cursor: pointer;'> a accès aux relevés de notes des <?php echo $gepiSettings['denomination_eleves']; ?> dont il est responsable</label></td>
			</tr>
		 */
$statutItem='responsable';
$titreItem='active_absences_parents';
$texteItem="a accès aux absences des ".$gepiSettings['denomination_eleves']." dont il est responsable (affichage des saisies non traitées 4 heures après la création)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesReleveParent';
$texteItem="a accès aux relevés de notes des ".$gepiSettings['denomination_eleves']." dont il est responsable";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesOptionsReleveParent" id="GepiAccesOptionsReleveParent" value="yes" <?php if (getSettingValue("GepiAccesOptionsReleveParent")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesOptionsReleveParent' style='cursor: pointer;'> a accès aux options du relevés de notes (<em>nom court, coef, date des devoirs,...</em>)</label></td>
			</tr>
		 */
$titreItem='GepiAccesOptionsReleveParent';
$texteItem="a accès aux options du relevés de notes (<em>nom court, coef, date des devoirs,...</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesCahierTexteParent" id="GepiAccesCahierTexteParent" value="yes" <?php if (getSettingValue("GepiAccesCahierTexteParent")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesCahierTexteParent' style='cursor: pointer;'> a accès au cahier de texte des <?php echo $gepiSettings['denomination_eleves']; ?> dont il est responsable</label></td>
			</tr>
		 */
$titreItem='GepiAccesCahierTexteParent';
$texteItem="a accès au cahier de texte des ".$gepiSettings['denomination_eleves']." dont il est responsable";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiPasswordReinitParent" id="GepiPasswordReinitParent" value="yes" <?php if (getSettingValue("GepiPasswordReinitParent")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiPasswordReinitParent' style='cursor: pointer;'> peut réinitialiser lui-même son mot de passe perdu (<em>si fonction activée</em>)</label></td>
			</tr>
		 */
$titreItem='GepiPasswordReinitParent';
$texteItem="peut réinitialiser lui-même son mot de passe perdu (<em>si fonction activée</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesEquipePedaParent" id="GepiAccesEquipePedaParent" value="yes" <?php if (getSettingValue("GepiAccesEquipePedaParent")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesEquipePedaParent' style='cursor: pointer;'> a accès à l'équipe pédagogique concernant les <?php echo $gepiSettings['denomination_eleves']; ?> dont il est responsable</label></td>
			</tr>
		 */
$titreItem='GepiAccesEquipePedaParent';
$texteItem="a accès à l'équipe pédagogique concernant les ".$gepiSettings['denomination_eleves']." dont il est responsable";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesCpePPEmailParent" id="GepiAccesCpePPEmailParent" value="yes" <?php if (getSettingValue("GepiAccesCpePPEmailParent")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesCpePPEmailParent' style='cursor: pointer;'> a accès aux adresses email du CPE et du professeur principal responsables des <?php echo $gepiSettings['denomination_eleves']; ?> dont il est responsable (<em>paramètre utile seulement si le paramètre suivant est décoché</em>)</label></td>
			</tr>
		 */
$titreItem='GepiAccesCpePPEmailParent';
$texteItem="a accès aux adresses email du CPE et du professeur principal responsables des ".$gepiSettings['denomination_eleves']." dont il est responsable (<em>paramètre utile seulement si le paramètre suivant est décoché</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesEquipePedaEmailParent" id="GepiAccesEquipePedaEmailParent" value="yes" <?php if (getSettingValue("GepiAccesEquipePedaEmailParent")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesEquipePedaEmailParent' style='cursor: pointer;'> a accès aux adresses email de l'équipe pédagogique concernant les <?php echo $gepiSettings['denomination_eleves']; ?> dont il est responsable</label></td>
			</tr>
		 */
$titreItem='GepiAccesEquipePedaEmailParent';
$texteItem="a accès aux adresses email de l'équipe pédagogique concernant les ".$gepiSettings['denomination_eleves']." dont il est responsable";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;' valign='top'><input type="checkbox" name="GepiAccesBulletinSimpleParent" id="GepiAccesBulletinSimpleParent" value="yes" <?php if (getSettingValue("GepiAccesBulletinSimpleParent")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesBulletinSimpleParent' style='cursor: pointer;'> a accès aux bulletins simplifiés des <?php echo $gepiSettings['denomination_eleves']; ?> dont il est responsable</label>
				<br />
				<?php

					echo "<span style='font-size:x-small'>";
					if($acces_app_ele_resp=='manuel') {
						echo "L'accès aux appréciations est donné manuellement dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Accès aux appréciations et avis du conseil</a>.";
					}
					elseif($acces_app_ele_resp=='date') {
						echo "L'accès aux appréciations est ouvert à la date saisie dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Accès aux appréciations et avis du conseil</a>.";
					}
					elseif($acces_app_ele_resp=='periode_close') {
						echo "L'accès aux appréciations est ouvert automatiquement ";
						if($delais_apres_cloture>0) {echo $delais_apres_cloture." jours après ";}
						echo "la clôture de la période par un compte scolarité.";
					}
					echo "</span>";
					echo "<br />";
					echo "<span style='font-size:x-small'>";
					echo "Le mode d'ouverture de l'accès se paramètre en <a href='param_gen.php#mode_ouverture_acces_appreciations'  onclick=\"return confirm_abandon (this, change, '$themessage')\">Gestion générale/Configuration générale</a>";
					echo "</span>";
				?>
				</td>
			</tr>
		 */
$titreItem='GepiAccesBulletinSimpleParent';
$texteItem="a accès aux bulletins simplifiés des ".$gepiSettings['denomination_eleves']." dont il est responsable<br />";

$texteItem.="<em>";
if($acces_app_ele_resp=='manuel') {
  $texteItem.= "L'accès aux appréciations est donné manuellement dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon(this, change, '$themessage')\">Accès aux appréciations et avis du conseil</a>.";
} elseif($acces_app_ele_resp=='date') {
  $texteItem.= "L'accès aux appréciations est ouvert à la date saisie dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Accès aux appréciations et avis du conseil</a>.";
} elseif($acces_app_ele_resp=='periode_close') {
  $texteItem.= "L'accès aux appréciations est ouvert automatiquement ";
  if($delais_apres_cloture>0) {$texteItem.= $delais_apres_cloture." jours après ";}
  $texteItem.= "la clôture de la période par un compte scolarité.";
}
$texteItem.="<br />";
$texteItem.= "Le mode d'ouverture de l'accès se paramètre en <a href='param_gen.php#mode_ouverture_acces_appreciations'  onclick=\"return confirm_abandon (this, change, '$themessage')\">Gestion générale/Configuration générale</a>";
$texteItem.= "</em>";

if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<tr valign='top'>
				<td style='border: 0px;' valign='top'><input type="checkbox" name="GepiAccesGraphParent" id="GepiAccesGraphParent" value="yes" <?php if (getSettingValue("GepiAccesGraphParent")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesGraphParent' style='cursor: pointer;'> a accès à la visualisation graphique des résultats des <?php echo $gepiSettings['denomination_eleves']; ?> dont il est responsable</label>
				<br />
				<?php

					echo "<span style='font-size:x-small'>";
					if($acces_app_ele_resp=='manuel') {
						echo "L'accès aux appréciations est donné manuellement dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Accès aux appréciations et avis du conseil</a>.";
					}
					elseif($acces_app_ele_resp=='date') {
						echo "L'accès aux appréciations est ouvert à la date saisie dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Accès aux appréciations et avis du conseil</a>.";
					}
					elseif($acces_app_ele_resp=='periode_close') {
						echo "L'accès aux appréciations est ouvert automatiquement ";
						if($delais_apres_cloture>0) {echo $delais_apres_cloture." jours après ";}
						echo "la clôture de la période par un compte scolarité.";
					}
					echo "</span>";
					echo "<br />";
					echo "<span style='font-size:x-small'>";
					echo "Le mode d'ouverture de l'accès se paramètre en <a href='param_gen.php#mode_ouverture_acces_appreciations'  onclick=\"return confirm_abandon (this, change, '$themessage')\">Gestion générale/Configuration générale</a>";
					echo "</span>";
				?>
			</td>
			</tr>
		 */
$titreItem='GepiAccesGraphParent';
$texteItem="a accès à la visualisation graphique des résultats des ".$gepiSettings['denomination_eleves']." dont il est responsable<br />";
$texteItem.= "<em>";
if($acces_app_ele_resp=='manuel') {
  $texteItem.= "L'accès aux appréciations est donné manuellement dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Accès aux appréciations et avis du conseil</a>.";
} elseif($acces_app_ele_resp=='date') {
  $texteItem.= "L'accès aux appréciations est ouvert à la date saisie dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Accès aux appréciations et avis du conseil</a>.";
} elseif($acces_app_ele_resp=='periode_close') {
  $texteItem.= "L'accès aux appréciations est ouvert automatiquement ";
  if($delais_apres_cloture>0) {$texteItem.= $delais_apres_cloture." jours après ";}
  $texteItem.= "la clôture de la période par un compte scolarité.";
}
$texteItem.= "<br />";
$texteItem.= "Le mode d'ouverture de l'accès se paramètre en <a href='param_gen.php#mode_ouverture_acces_appreciations'  onclick=\"return confirm_abandon (this, change, '$themessage')\">Gestion générale/Configuration générale</a>";
$texteItem.= "</em>";

if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
/*

			<!-- Années antérieures -->
			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="AAResponsable" id="AAResponsable" value="yes" <?php if (getSettingValue("AAResponsable")=='yes') echo 'checked="checked"'; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='AAResponsable' style='cursor: pointer;'> a accès aux données d'années antérieures des <?php echo $gepiSettings['denomination_eleves']; ?> dont il est responsable</label></td>
			</tr>
		 */
$titreItem='AAResponsable';
$texteItem="a accès aux données d'années antérieures des ".$gepiSettings['denomination_eleves']." dont il est responsable";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;




$titreItem='visuRespDisc';
$texteItem="a accès dans le module Discipline aux incidents concernant les enfants dont il est responsable.";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

/*

			</table>
		</td>
	</tr>
</table>
<p style="text-align:center;">
<input type="hidden" name="is_posted" value="1" />
<input type="submit" name = "OK" value="Enregistrer" style="font-variant: small-caps;" />
</p>
</form>
<?php require("../lib/footer.inc.php");?>
		 *
		 *
		 */

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
