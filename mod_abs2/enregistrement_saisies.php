<?php
/**
 *
 * @version $Id$
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Eric Lebrun, Stephane Boireau, Julien Jocal
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

if ($utilisateur->getStatut()=="professeur" &&  getSettingValue("active_module_absence_professeur")!='y') {
    die("Le module n'est pas activé.");
}

//récupération des paramètres de la requète
$id_groupe = isset($_POST["id_groupe"]) ? $_POST["id_groupe"] :(isset($_GET["id_groupe"]) ? $_GET["id_groupe"] :NULL);
$id_classe = isset($_POST["id_classe"]) ? $_POST["id_classe"] :(isset($_GET["id_classe"]) ? $_GET["id_classe"] :NULL);
$id_aid = isset($_POST["id_aid"]) ? $_POST["id_aid"] :(isset($_GET["id_aid"]) ? $_GET["id_aid"] :NULL);
$id_creneau = isset($_POST["id_creneau"]) ? $_POST["id_creneau"] :(isset($_GET["id_creneau"]) ? $_GET["id_creneau"] :NULL);
$id_cours = isset($_POST["id_cours"]) ? $_POST["id_cours"] :(isset($_GET["id_cours"]) ? $_GET["id_cours"] :NULL);
$d_date_absence_eleve = isset($_POST["d_date_absence_eleve"]) ? $_POST["d_date_absence_eleve"] :NULL;
$total_eleves = isset($_POST["total_eleves"]) ? $_POST["total_eleves"] :(isset($_GET["total_eleves"]) ? $_GET["total_eleves"] :0);

//initialisation des variable
$message_enregistrement = '';
//if ($d_date_absence_eleve == null) {
//    $message_enregistrement = 'Erreur, la date ne peut etre nulle.</br>';
//    include("saisie_absences.php");
//    die();
//} else {
//    $date_time_absence_eleve = new DateTime(str_replace("/",".",$d_date_absence_eleve));
//}
if ($id_groupe != null) {
    if (GroupeQuery::create()->findPk($id_groupe) == null) {
	$message_enregistrement .= "Probleme avec le parametre id_groupe<br/>";
	$id_groupe = null;
    }
}
if ($id_classe != null) {
    if (ClasseQuery::create()->findPk($id_classe) == null) {
	$message_enregistrement .= "Probleme avec le parametre id_classe<br/>";
	$id_classe = null;
    }
}
if ($id_aid != null) {
    if (AidDetailsQuery::create()->findPk($id_aid) == null) {
	$message_enregistrement .= "Probleme avec le parametre id_aid<br/>";
	$id_aid = null;
    }
}
if ($id_creneau != null) {
    if (EdtCreneauQuery::create()->findPk($id_creneau) == null) {
	$message_enregistrement .= "Probleme avec le parametre id_creneau<br/>";
	$id_creneau = null;
    }
}

if ($id_cours != null) {
    $current_cours = EdtEmplacementCoursQuery::create()->findPk($id_cours);
    if ($current_cours != null) {
	$id_creneau = $current_cours->getIdDefiniePeriode();
	$id_groupe = $current_cours->getIdGroupe();
	$id_aid = $current_cours->getIdAid();
    } else {
	$message_enregistrement .= "Probleme avec le parametre id_cours<br/>";
	$id_cours = null;
    }
}

for($i=0; $i<$total_eleves; $i++) {

    //on test si l'eleve est enregistré absent
    if (!isset($_POST['active_absence_eleve'][$i]) &&
	!(isset($_POST['commentaire_absence_eleve'][$i]) && $_POST['commentaire_absence_eleve'][$i] != null) &&
	!(isset($_POST['type_absence_eleve'][$i]) && $_POST['type_absence_eleve'][$i] != -1) ) {
	continue;
    }
    //il faut au moins un eleve
    $eleve = EleveQuery::create()->findPk($_POST['id_eleve_absent'][$i]);
    if ($eleve == null) {
	$message_enregistrement .= "Probleme avec l'id eleve : ".$_POST['id_eleve_absent'][$i]."<br/>";
	continue;
    }

    $saisie = new AbsenceEleveSaisie();
    $saisie->setEleveId($_POST['id_eleve_absent'][$i]);
    $saisie->setIdEdtCreneau($id_creneau);
    $saisie->setIdEdtEmplacementCours($id_cours);
    $saisie->setIdGroupe($id_groupe);
    $saisie->setIdClasse($id_classe);
    $saisie->setIdAid($id_aid);
    $saisie->setCommentaire($_POST['commentaire_absence_eleve'][$i]);

    $date_debut = new DateTime(str_replace("/",".",$_POST['date_debut_absence_eleve'][$i]));
    $heure_debut = new DateTime($_POST['heure_debut_absence_eleve'][$i]);
    $date_debut->setTime($heure_debut->format('H'), $heure_debut->format('i'));
    $saisie->setDebutAbs($date_debut);

    $date_fin = new DateTime(str_replace("/",".",$_POST['date_fin_absence_eleve'][$i]));
    $heure_fin = new DateTime($_POST['heure_fin_absence_eleve'][$i]);
    $date_fin->setTime($heure_fin->format('H'), $heure_fin->format('i'));
    $saisie->setFinAbs($date_fin);

    $saisie->setUtilisateurId($utilisateur->getPrimaryKey());

    if (isset($_POST['type_absence_eleve'][$i]) && $_POST['type_absence_eleve'][$i] != -1) {
	$type = AbsenceEleveTypeQuery::create()->findPk($_POST['type_absence_eleve'][$i]);
	if ($type != null) {
	    if ($type->isStatutAutorise($utilisateur->getStatut())) {
		//on va creer un traitement avec le type d'absence associé
		$traitement = new AbsenceEleveTraitement();
		$saisie->save();
		$traitement->addAbsenceEleveSaisie($saisie);
		$traitement->setAbsenceEleveType($type);
		$traitement->setUtilisateurProfessionnel($utilisateur);
		$traitement->save();
	    } else {
		$message_enregistrement .= "Type d'absence non autorisé pour ce statut : ".$_POST['type_absence_eleve'][$i]."<br/>";
	    }
	} else {
	    $message_enregistrement .= "Probleme avec l'id du type d'absence : ".$_POST['type_absence_eleve'][$i]."<br/>";
	}
    }

    $saisie->save();
    $message_enregistrement .= "Saisie enregistrée pour l'eleve : ".$eleve->getNom()."<br/>";
}
if (!isset($saisie) || $saisie == null) {
    //il n'y aucune saisie d'effectuer, on va enregistrer une saisie pour marquer le fait que l'appel a été effectué
    //on test si l'eleve est enregistré absent
    if (($id_groupe != null || $id_classe != null || $id_aid != null) && ($id_creneau != null) && ($d_date_absence_eleve != null)) {
	$saisie = new AbsenceEleveSaisie();

	$saisie->setIdEdtCreneau($id_creneau);
	$saisie->setIdEdtEmplacementCours($id_cours);
	$saisie->setIdGroupe($id_groupe);
	$saisie->setIdClasse($id_classe);
	$saisie->setIdAid($id_aid);
	$saisie->setUtilisateurId($utilisateur->getPrimaryKey());

	$d_date_absence_eleve = new DateTime(str_replace("/",".",$d_date_absence_eleve));
	$creneau = EdtCreneauQuery::create()->findPk($id_creneau);
	$d_date_absence_eleve->setTime($creneau->getHeuredebutDefiniePeriode('H'), $creneau->getHeuredebutDefiniePeriode('i'));
	$saisie->setDebutAbs($d_date_absence_eleve);
	$date_fin = clone $d_date_absence_eleve;
	$date_fin->setTime($creneau->getHeurefinDefiniePeriode('H'), $creneau->getHeurefinDefiniePeriode('i'));
	$saisie->setFinAbs($date_fin);
	
	$message_enregistrement .= "Saisie enregistrée.<br/>";

	$saisie->save();
    }
}

include("saisie_absences.php");
?>