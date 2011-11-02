<?php
/**
 *
 * @version $Id: enregistrement_saisie_eleve.php 6607 2011-03-03 14:10:16Z crob $
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

if ($utilisateur->getStatut()!="cpe" && $utilisateur->getStatut()!="scolarite" && $utilisateur->getStatut()!="autre") {
    die("acces interdit");
}

//récupération des paramètres de la requète
$id_creneau = isset($_POST["id_creneau"]) ? $_POST["id_creneau"] :(isset($_GET["id_creneau"]) ? $_GET["id_creneau"] :NULL);
$id_cours = isset($_POST["id_cours"]) ? $_POST["id_cours"] :(isset($_GET["id_cours"]) ? $_GET["id_cours"] :NULL);
$type_absence = isset($_POST["type_absence"]) ? $_POST["type_absence"] :NULL;
$commentaire = isset($_POST["commentaire"]) ? $_POST["commentaire"] :NULL;
$total_eleves = isset($_POST["total_eleves"]) ? $_POST["total_eleves"] :(isset($_GET["total_eleves"]) ? $_GET["total_eleves"] :0);
$multisaisie = isset($_POST["multisaisie"]) ? $_POST["multisaisie"] :NULL;

$message_enregistrement = "";

//initialisation des variable
if ($id_creneau != null && $id_creneau != -1) {
    $creneau = EdtCreneauQuery::create()->findPk($id_creneau);
    if ($creneau == null) {
	$message_enregistrement .= "Probleme avec le parametre id_creneau<br/>";
	$id_creneau = null;
    }
} else {
    $creneau = null;
}

if ($id_cours != null && $id_cours != -1) {
    $current_cours = EdtEmplacementCoursQuery::create()->findPk($id_cours);
    if ($current_cours != null) {
    } else {
	$message_enregistrement .= "Probleme avec le parametre id_cours<br/>";
	$id_cours = null;
    }
} else {
   $current_cours = null;
}

$type = null;
if ($type_absence != null && $type_absence != -1) {
    $type = AbsenceEleveTypeQuery::create()->findPk($type_absence);
    if ($type != null) {
	if (!$type->isStatutAutorise($utilisateur->getStatut())) {
	    $message_enregistrement .= "Type d'absence non autorisé pour ce statut : ".$_POST['type_absence']."<br/>";
	    $type = null;
	}
    } else {
	$message_enregistrement .= "Probleme avec l'id du type d'absence : ".$_POST['type_absence']."<br/>";
    }
}

//on determine la liste des saisies
$saisie_col_modele = new PropelObjectCollection();
if ($current_cours != null) {
    //$message_enregistrement .= "test";//on se base sur les cours et les semaines
    $col = EdtSemaineQuery::create()->find();
    foreach ($col as $semaine) {
	if (isset ($_POST['semaine_'.$semaine->getPrimaryKey()])) {
	        if ($current_cours->getTypeSemaine() != '' && $current_cours->getTypeSemaine() != '0' && $current_cours->getTypeSemaine() != $semaine->getTypeEdtSemaine()) {
		    $message_enregistrement .= "Probleme avec la semaine : " .$semaine->getNumEdtSemaine()." ".$semaine->getTypeEdtSemaine();
		    $message_enregistrement .= ", le type ne correspond pas au cours.";
		} else {
		    $saisie = new AbsenceEleveSaisie();
		    $saisie->setUtilisateurProfessionnel($utilisateur);
		    $saisie->setCommentaire($commentaire);

		    $date_debut = $current_cours->getDate($semaine->getNumEdtSemaine());
		    $date_debut->setTime($current_cours->getHeureDebut('H'), $current_cours->getHeureDebut('i'));
		    $saisie->setDebutAbs($date_debut);

		    $date_fin = clone $date_debut;
		    $heure_fin = $current_cours->getHeureFin();
		    $date_fin->setTime($current_cours->getHeureFin('H'), $current_cours->getHeureFin('i'));
		    $saisie->setFinAbs($date_fin);

		    $saisie_col_modele->append($saisie);
		}
	}
    }
} else {
    try {
	$date_debut = new DateTime(str_replace("/",".",$_POST['date_absence_eleve_debut_saisir_eleve']));
    } catch (Exception $x) {
	$message_enregistrement .= "Mauvais format de date.<br/>";
    }
    try {
	$date_fin = new DateTime(str_replace("/",".",$_POST['date_absence_eleve_fin_saisir_eleve']));
    } catch (Exception $x) {
	$message_enregistrement .= "Mauvais format de date.<br/>";
    }

    if ($creneau != null) {
	$multisaisie = 'y'; //on fait une saisie par jour
	$heure_debut = $creneau->getHeuredebutDefiniePeriode(null);
	$heure_fin = $creneau->getHeurefinDefiniePeriode(null);
    } else {
	try {
	    $heure_debut = new DateTime($_POST['heure_debut_absence_eleve']);
	} catch (Exception $x) {
	    $message_enregistrement .= "Mauvais format d'heure.<br/>";
	}
	try {
	    $heure_fin = new DateTime($_POST['heure_fin_absence_eleve']);
	} catch (Exception $x) {
	    $message_enregistrement .= "Mauvais format d'heure.<br/>";
	}
    }

    if ($date_debut->format('U') > $date_fin->format('U')) {
	$message_enregistrement .= "La date de debut d'absence ne peut pas être postérieure à la date de fin.<br/>";
    }

    if ($message_enregistrement == "") {
	if ($multisaisie == 'y') {
	//on va creer une saisie par jour
	    $date_compteur = $date_debut;
	    $compteur = 0;
	    while (!($date_compteur->format('U') > $date_fin->format('U')) && $compteur < 50) { //maximum 50 saisies simultanées
		$compteur = $compteur + 1;
		$date_debut_saisie = clone $date_compteur;
		$date_debut_saisie->setTime($heure_debut->format('H'), $heure_debut->format('i'));
		$date_fin_saisie = clone $date_compteur;
		$date_fin_saisie->setTime($heure_fin->format('H'), $heure_fin->format('i'));

		$saisie = new AbsenceEleveSaisie();
		$saisie->setUtilisateurProfessionnel($utilisateur);
		$saisie->setCommentaire($commentaire);

		$saisie->setDebutAbs($date_debut_saisie);
		$saisie->setFinAbs($date_fin_saisie);
		if ($creneau != null) {
		     $saisie->setEdtCreneau($creneau);
		}
		$saisie_col_modele->append($saisie);
		$date_compteur->modify("+1 day");
	    }
	} else {
	    $date_debut_saisie = clone $date_debut;
	    $date_debut_saisie->setTime($heure_debut->format('H'), $heure_debut->format('i'));
	    $date_fin_saisie = clone $date_fin;
	    $date_fin_saisie->setTime($heure_fin->format('H'), $heure_fin->format('i'));

	    $saisie = new AbsenceEleveSaisie();
	    $saisie->setUtilisateurProfessionnel($utilisateur);
	    $saisie->setCommentaire($commentaire);

	    $saisie->setDebutAbs($date_debut_saisie);
	    $saisie->setFinAbs($date_fin_saisie);
	    if ($creneau != null) {
		 $saisie->setEdtCreneau($creneau);
	    }
	    $saisie_col_modele->append($saisie);
	}
    }
}

for($i=0; $i<$total_eleves; $i++) {

    //$id_eleve = $_POST['id_eleve_absent'][$i];

    //on test si l'eleve est enregistré absent
    if (!isset($_POST['active_absence_eleve'][$i])) {
	continue;
    }
    
    $eleve = EleveQuery::create()->findPk($_POST['active_absence_eleve'][$i]);
    if ($eleve == null) {
	$message_enregistrement .= "Probleme avec l'id eleve : ".$_POST['id_eleve_absent'][$i]."<br/>";
	continue;
    }

    foreach ($saisie_col_modele as $saisie_modele) {

	$saisie = clone $saisie_modele;
	$saisie->setEleveId($eleve->getIdEleve());

	if ($type != null) {
	    $traitement = new AbsenceEleveTraitement();
	    $traitement->addAbsenceEleveSaisie($saisie);
	    $traitement->setAbsenceEleveType($type);
	    $traitement->setUtilisateurProfessionnel($utilisateur);
	    if ($type->getTypeSaisie() == "DISCIPLINE" && getSettingValue("active_mod_discipline")=='y') {
		//on affiche un lien pour saisir le module discipline
		$saisie_discipline = true;
	    }
	}

	if ($saisie->validate()) {
	    $saisie->save();
	    if (isset($traitement)) {
		$traitement->save();
	    }
	    $message_enregistrement .= "<a href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."'>Saisie enregistrée pour l'eleve : ".$eleve->getNom()."</a>";
	    if (isset($saisie_discipline) && $saisie_discipline == true) {
		$message_enregistrement .= " &nbsp;<a href='../mod_discipline/saisie_incident_abs2.php?id_absence_eleve_saisie=".
		    $saisie->getId()."&return_url=no_return".add_token_in_url()."'>Saisir un incident disciplinaire pour l'élève : ".$eleve->getNom()."</a>";
	    }
	    $message_enregistrement .= "<br/>";
	} else {
	    $message_erreur_eleve[$eleve->getIdEleve()] = '';
	    foreach ($saisie->getValidationFailures() as $erreurs) {
		$message_erreur_eleve[$eleve->getIdEleve()] .= $erreurs;
		$no_br = true;
		if ($no_br) {
		    $no_br = false;
		} else {
		    $message_erreur_eleve[$eleve->getIdEleve()] .= '<br/>';
		}
	    }
	}
    }
}

include("saisir_eleve.php");
?>