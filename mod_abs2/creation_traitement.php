<?php
/**
 *
 *
 * Copyright 2010 Josselin Jacquard
 *
 * This file and the mod_abs2 module is distributed under GPL version 3, or
 * (at your option) any later version.
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

// Initialisations files
require_once("../lib/initialisationsPropel.inc.php");
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

//recherche de l'utilisateur avec propel
$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

//On vérifie si le module est activé
if (getSettingValue("active_module_absence")!='2') {
    die("Le module n'est pas activé.");
}

if ($utilisateur->getStatut()!="cpe" && $utilisateur->getStatut()!="scolarite") {
    die("acces interdit");
}

$menu = isset($_POST["menu"]) ? $_POST["menu"] :(isset($_GET["menu"]) ? $_GET["menu"] : Null);

$nb = 100;
if (isset($_POST["nb_checkbox"])) {
    $nb = $_POST["nb_checkbox"];
} else if (isset($_POST["item_per_page"])) {
    $nb = $_POST["item_per_page"];
}
if ( isset($_POST["creation_traitement"]) && $_POST["creation_traitement"] == 'yes') {
    $traitement = new AbsenceEleveTraitement();
    $traitement->setUtilisateurProfessionnel($utilisateur);
    for($i=0; $i<$nb; $i++) {
	if (isset($_POST["select_saisie"][$i])) {
	    $traitement->addAbsenceEleveSaisie(AbsenceEleveSaisieQuery::create()->findPk($_POST["select_saisie"][$i]));
	}
    }
    if ($traitement->getAbsenceEleveSaisies()->isEmpty()) {
	$message_erreur_traitement = ' Erreur : aucune saisie sélectionnée';
    } else {
	$traitement->save();
        $url='./visu_traitement.php?id_traitement='.$traitement->getId().'';
        if($menu){
            $url.='&menu=false';
        }
	header("Location:".$url);
        die;
    }
} else if ( isset($_POST["ajout_traitement"]) && $_POST["ajout_traitement"] == 'yes') {
    $id_traitement = isset($_POST["id_traitement"]) ? $_POST["id_traitement"] :(isset($_GET["id_traitement"]) ? $_GET["id_traitement"] :(isset($_SESSION["id_traitement"]) ? $_SESSION["id_traitement"] : NULL));
    $traitement = AbsenceEleveTraitementQuery::create()->findPk($id_traitement);
    if ($traitement == null) {
	$message_erreur_traitement = ' Erreur : aucun traitement trouvé';
    } else {
	for($i=0; $i<$nb; $i++) {
	    if (isset($_POST["select_saisie"][$i])) {
		$saisie = AbsenceEleveSaisieQuery::create()->findPk($_POST["select_saisie"][$i]);
		if (!$traitement->getAbsenceEleveSaisies()->contains($saisie)) {
		    $traitement->addAbsenceEleveSaisie($saisie);
		}
	    }
	}
	if ($traitement->getAbsenceEleveSaisies()->isEmpty()) {
	    $message_erreur_traitement = ' Erreur : aucune saisie sélectionnée';
	} else {
	    $traitement->save();
            $url='./visu_traitement.php?id_traitement='.$traitement->getId().'';
            if($menu){
                $url.='&menu=false';
            }
            header("Location:".$url);
            die;
	}
    }
}
?>