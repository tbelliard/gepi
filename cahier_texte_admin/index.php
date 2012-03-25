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

/**
 * Gestion des cahiers de textes
 * 
 * @param $_POST['activer'] activation/désactivation
 * @param $_POST['version'] numero de version du cahier de texte
 * @param $_POST['cahiers_texte_login_pub'] identifiant accès public
 * @param $_POST['cahiers_texte_passwd_pub'] mot de passe accès public
 * @param $_POST['begin_month'],$_POST['begin_day'],$_POST['begin_year'] Date de début du cahier de texte
 * @param $_POST['end_month'],$_POST['end_day'],$_POST['end_year'] Date de fin du cahier de texte
 * @param $_POST['cahier_texte_acces_public'] Accès public du cahier de texte
 * @param $_POST['visa_cdt_inter_modif_notices_visees'] Interdiction de modifier les notices visées
 * @param $_POST['delai_devoirs'] Délai de visualisation des devoirs
 * @param $_POST['is_posted']
 *
 */

$accessibilite="y";
$titre_page = "Gestion des cahiers de textes";
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

/******************************************************************
 *    Enregistrement des variables passées en $_POST si besoin
 ******************************************************************/
$msg = "";
if (isset($_POST['is_posted'])) {
	check_token();
	//debug_var();
	if (isset($_POST['activer'])) {
		if (!saveSetting("active_cahiers_texte", $_POST['activer'])) $msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation !";
	}
	
	if (isset($_POST['version'])) {
		if (!saveSetting("GepiCahierTexteVersion", $_POST['version'])) $msg = "Erreur lors de l'enregistrement du numero de version du cahier de texte !";
	}
	
	if (isset($_POST['cahiers_texte_login_pub'])) {
		$mdp = $_POST['cahiers_texte_passwd_pub'];
		$user_ct = $_POST['cahiers_texte_login_pub'];
	
		if ((trim($mdp)=='') and (trim($user_ct) !='')) {
			$_POST['cahiers_texte_login_pub'] = '';
			$msg .= "Vous devez choisir un mot de passe.";
		}
		if ((trim($mdp) !='')and (trim($user_ct) == '')) {
			$_POST['cahiers_texte_passwd_pub'] = '';
			$msg .= "Vous devez choisir un identifiant.";
		}
	
		if (!saveSetting("cahiers_texte_passwd_pub", $_POST['cahiers_texte_passwd_pub']))
				$msg .= "Erreur lors de l'enregistrement du mot de passe !";
	//    include_once( '../lib/class.htaccess.php' );
		if (!saveSetting("cahiers_texte_login_pub", $_POST['cahiers_texte_login_pub']))
				$msg .= "Erreur lors de l'enregistrement du login !";
	
	}
	
	if (isset($_POST['begin_day']) and isset($_POST['begin_month']) and isset($_POST['begin_year'])) {
		$begin_bookings = mktime(0,0,0,$_POST['begin_month'],$_POST['begin_day'],$_POST['begin_year']);
		if (!saveSetting("begin_bookings", $begin_bookings))
				$msg .= "Erreur lors de l'enregistrement de begin_bookings !";
	}
	if (isset($_POST['end_day']) and isset($_POST['end_month']) and isset($_POST['end_year'])) {
		$end_bookings = mktime(0,0,0,$_POST['end_month'],$_POST['end_day'],$_POST['end_year']);
		if (!saveSetting("end_bookings", $end_bookings))
				$msg .= "Erreur lors de l'enregistrement de end_bookings !";
	}
	
	if (isset($_POST['cahier_texte_acces_public'])) {
		if ($_POST['cahier_texte_acces_public'] == "yes") {
			$temp = "yes";
		} else {
			$temp = "no";
			}
		if (!saveSetting("cahier_texte_acces_public", $temp)) {
			$msg .= "Erreur lors de l'enregistrement de cahier_texte_acces_public !";
		}
	}
	
	//ajout Eric visa CDT
	if (isset($_POST['visa_cdt_inter_modif_notices_visees'])) {
		if ($_POST['visa_cdt_inter_modif_notices_visees'] == "yes") {
			$temp = "yes";
		} else {
			$temp = "no";
			}
		if (!saveSetting("visa_cdt_inter_modif_notices_visees", $temp)) {
			$msg .= "Erreur lors de l'enregistrement de visa_cdt_inter_modif_notices_visees !";
		}
	}
	//Fin ajout Eric
	
	if (isset($_POST['delai_devoirs'])) {
		if (!saveSetting("delai_devoirs", $_POST['delai_devoirs']))
				$msg .= "Erreur lors de l'enregistrement du délai de visualisation des devoirs";
	}


	if (isset($_POST['is_posted'])) {
		if (isset($_POST['cdt_possibilite_masquer_pj'])) {
			if (!saveSetting("cdt_possibilite_masquer_pj", "y")) {
				$msg .= "Erreur lors de l'enregistrement de l'autorisation de masquer des documents joints.<br />";
			}
		}
		elseif (!saveSetting("cdt_possibilite_masquer_pj", "n")) {
			$msg .= "Erreur lors de l'enregistrement de l'interdiction de masquer des documents joints.<br />";
		}
	}


	if (isset($_POST['is_posted']) && ($msg=="") ) {
		$msg = "Les modifications ont été enregistrées !";
		$post_reussi=TRUE;
	}
	
	if (isset($_POST['cdt_autoriser_modif_multiprof'])) {
		if ($_POST['cdt_autoriser_modif_multiprof'] == "yes") {
			$temp = "yes";
		} else {
			$temp = "no";
		}
		if (!saveSetting("cdt_autoriser_modif_multiprof", $temp)) {
			$msg .= "Erreur lors de l'enregistrement de cdt_autoriser_modif_multiprof !";
		}
	}
}

// on demande une validation si on quitte sans enregistrer les changements
$messageEnregistrer="Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?";

/****************************************************************
                     HAUT DE PAGE
****************************************************************/

// ====== Inclusion des balises head et du bandeau =====
include_once("../lib/header_template.inc.php");

/****************************************************************
			FIN HAUT DE PAGE
****************************************************************/

if (!suivi_ariane($_SERVER['PHP_SELF'],$titre_page))
		echo "erreur lors de la création du fil d'ariane";

/****************************************************************
			CONSTRUCTION DE LA PAGE
****************************************************************/


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


$nom_gabarit = '../templates/'.$_SESSION['rep_gabarits'].'/cahier_texte_admin/index_template.php';

$tbs_last_connection=""; // On n'affiche pas les dernières connexions
include($nom_gabarit);

// ------ on vide les tableaux -----
unset($menuAffiche);



?>
