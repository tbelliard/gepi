<?php
/**
 *
 *
 * Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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

$titre_page = "Emploi du temps";
$affiche_connexion = 'yes';
$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions edt
require_once('./choix_langue.php');
require_once("./fonctions_edt.php");            // --- fonctions de base communes à tous les emplois du temps
require_once("./fonctions_edt_eleve.php");      // --- edt eleve
require_once("./fonctions_calendrier.php");
require_once("./fonctions_affichage.php");
require_once("./req_database.php");


$type_edt_2 = "eleve";
$period_id=isset($_GET['period_id']) ? $_GET['period_id'] : (isset($_POST['period_id']) ? $_POST['period_id'] : NULL);


// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

// Sécurité
if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}

// Sécurité supplémentaire par rapport aux paramètres du module EdT / Calendrier
if (param_edt($_SESSION["statut"]) != "yes") {
	Die(ASK_AUTHORIZATION_TO_ADMIN);
}

// =============== Traitement des données ====================//
if (isset($_SESSION["login"])) {

	$autorise = 'non'; // par principe, rien n'est autorisé ;)
	$aff_nom_edt = ''; // l'edt est vide

	if ($_SESSION['statut'] == "eleve") {

		$aff_nom_edt = renvoie_nom_long(($_SESSION["login"]), "eleve");
		$autorise = 'oui';
		$liens_autres_enfants = "";

	}elseif($_SESSION['statut'] == "responsable"){

		$tab_tmp_ele = get_enfants_from_resp_login($_SESSION['login'], 'simple', 'y');
		$nbre_enfants_brut = count($tab_tmp_ele);
		$liens_autres_enfants = "";
		// On vérifie que le login demandé est autorisé pour ce responsable

		for($a = 0 ; $a < $nbre_enfants_brut ; $a++){
			if ($tab_tmp_ele[$a] == $_GET["login_edt"]) {
				$autorise = 'oui';
				$aff_nom_edt = $tab_tmp_ele[$a + 1];
                $login_edt = $tab_tmp_ele[$a];
			}
			if ($nbre_enfants_brut > 2) {

				if ($a % 2 != 1) {

					// On propose un lien vers tous les enfants de ce responsable
					$liens_autres_enfants .= '
					 -- <a href="'.$_SERVER['PHP_SELF'].'?login_edt=' . $tab_tmp_ele[$a] . '">
					Voir celui de ' . $tab_tmp_ele[$a + 1] . '</a>';

				}

			}

		}

	}

	if ($autorise == 'oui') {

		$aff_nom_edt = '<span style="font-weight: bold;">L\'emploi du temps de '.$aff_nom_edt."</span>\n";

	}else{

		DIE('Vous ne pouvez pas voir l\'emploi du temps de cet utilisateur.');

	}
}

// CSS et js particulier à l'EdT
$javascript_specifique = "edt_organisation/script/fonctions_edt";
$ua = getenv("HTTP_USER_AGENT");
if (strstr($ua, "MSIE 6.0")) {
	$style_specifique = "templates/".NameTemplateEDT()."/css/style_edt_ie6";
}
else {
	$style_specifique = "templates/".NameTemplateEDT()."/css/style_edt";
}
    //=========================== GESTION DES PERIODES
    
    if (PeriodesExistent()) {
        if ($period_id != NULL) {
            $_SESSION['period_id'] = $period_id;
        }
        if (!isset($_SESSION['period_id'])) {
            $_SESSION['period_id'] = ReturnIdPeriod(date("U"));
        }
        if (!PeriodExistsInDB($_SESSION['period_id'])) {
            $_SESSION['period_id'] = ReturnFirstIdPeriod();    
        }
        $DisplayPeriodBar = true;
    }
    else {
        $DisplayPeriodBar = false;
        $_SESSION['period_id'] = 0;
    }

    //=========================== CONSTRUCTION DES EDT

    if ($_SESSION['statut'] == "eleve")
    {
        $tab_data = ConstruireEDTEleve($_SESSION['login'], $_SESSION['period_id'] );
        $entetes = ConstruireEnteteEDT();
        $creneaux = ConstruireCreneauxEDT();
        $DisplayEDT = true;
        $login_edt = $_SESSION['login'];
    }
    else if ($_SESSION['statut'] == "responsable")
    {
        $tab_data = ConstruireEDTEleve($login_edt, $_SESSION['period_id'] );
        $entetes = ConstruireEnteteEDT();
        $creneaux = ConstruireCreneauxEDT();
        $DisplayEDT = true;
    }
    else {
        $DisplayEDT = false;
    }
// =============================================================================
//
//                                  VUE
//		
// =============================================================================

// ============== Le header ==========
require_once("../lib/header.inc.php");
// ===================================
?>

<div id="edteleve">

	<p class="bold">
		<a href="../accueil.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a></p>

    <p class="cadreInformation">
	    <?php echo $aff_nom_edt . $liens_autres_enfants; ?>
    </p>

    <?php



// ========================= AFFICHAGE DE LA BAR DE COMMUTATION DES PERIODES

    if ($DisplayPeriodBar) {
        AfficheBarCommutateurPeriodesEleve();
    }

    AfficheImprimante(true); 

?>
</div>
    <?php


// ========================= AFFICHAGE DES EMPLOIS DU TEMPS

    if ($DisplayEDT) {
        AfficherEDT($tab_data, $entetes, $creneaux, "eleve", $login_edt, $_SESSION['period_id']);
    }

    require_once("../lib/footer.inc.php"); 

?>
