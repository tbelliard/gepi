<?php

/**
 *
 *
 * Copyright 2010-2012 Josselin Jacquard, Régis Bouguin
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
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


// Initialisations files
require_once("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");

// On donne les droits sur la page en attendant qu'ils soient dans la base
$query = mysqli_query($GLOBALS["mysqli"], "INSERT INTO droits VALUES ('/mod_abs2/enregistrement_saisie_journee.php', 'F', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Saisie des absences', '') 
	ON DUPLICATE KEY UPDATE id = '/mod_abs2/enregistrement_saisie_journee.php'");

if (!$query) {
	echo "INSERT INTO droits VALUES ('/mod_abs2/enregistrement_saisie_journee.php', 'F', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Saisie des absences', '')
	ON DUPLICATE KEY UPDATE id = '/mod_abs2/enregistrement_saisie_journee.php'
	<br />";
	die ("Erreur lors de l'insertion des droits");
}

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


function verif_debut_fin_saisie(DateTime $dt_date_debut_appel, DateTime $dt_date_fin_appel, UtilisateurProfessionnel $utilisateur, $edt_cours) {
    $message_enregistrement = '';
    if ($dt_date_debut_appel === null) {
	$message_enregistrement .= "Le début de saisie doit etre précisée.<br/>";
	return $message_enregistrement;
    }
    if ($dt_date_fin_appel === null) {
	$message_enregistrement .= "La fin de saisie doit etre précisée.<br/>";
	return $message_enregistrement;
    }
    ////on verifie si l'utilisateur a le droit de saisir cela
    if ($utilisateur->getStatut() == 'professeur') {
	if ($dt_date_debut_appel->format('d/m/Y') != $dt_date_fin_appel->format('d/m/Y')) {
	    $message_enregistrement .= "Saisie sur plusieurs jours non autorisée.<br/>";
	}
	//verification des autorisation de saisies decaleer
	if (getSettingValue("abs2_saisie_prof_decale")!='y') {
	    $now = new DateTime('now');
	    if ($dt_date_debut_appel->format('d/m/Y') != $now->format('d/m/Y') || $dt_date_fin_appel->format('d/m/Y') != $now->format('d/m/Y')) {
		$message_enregistrement .= "Saisie non autorisée autre que pour la journée courante.<br/>";
	    }
	}
	if ($edt_cours != null && $utilisateur->getStatut() == 'professeur' && getSettingValue("abs2_saisie_prof_hors_cours")!='y') {
	    //on verifie que le saisie ne deborde pas du cours
	    if ($edt_cours->getHeureDebut('Hi') > $dt_date_debut_appel->format('Hi')) {
		$message_enregistrement .= "L'heure de début de saisie ne peut pas être antérieure au cours.<br/>";
	    }
	    if ($edt_cours->getHeureFin('Hi') < $dt_date_fin_appel->format('Hi')) {
		$message_enregistrement .= "L'heure de fin de saisie ne peut pas être postérieure au cours.<br/>";
	    }
	}
    }
    if ($dt_date_debut_appel->format('U') > $dt_date_fin_appel->format('U')) {
	$message_enregistrement .= "L'heure de fin de saisie ne peut etre anterieure à l'heure de début.<br/>";
    }
    return $message_enregistrement;
}

function format_verif_failures($saisie) {
	$message = '';
	$no_br = true;
	foreach ($saisie->getValidationFailures() as $failure) {
	    $message .= $failure->getMessage();
	    if ($no_br) {
		$no_br = false;
	    } else {
		$message .= '<br/>';
	    }
	}
	return $message;
}


// echo date('H:i:s').'<br />';


$message_enregistrement = "";

/*
 * Données communes à toutes les saisies
 * $id_groupe
 * $id_classe
 * $id_aid
 * $type_selection
 * $total_eleves
 * $type = AbsenceEleveTypeQuery::create()->findPk($_POST['type_absence_eleve']);
 * $date_fin = new DateTime(str_replace("/",".",$_POST['date_fin_absence_eleve']));
 * $date_debut = new DateTime(str_replace("/",".",$_POST['date_debut_absence_eleve']));
 * $idUtilisateur = $utilisateur->getPrimaryKey();
 * 
 */

//récupération des paramètres de la requête

$id_groupe = isset($_POST["id_groupe"]) ? $_POST["id_groupe"] :(isset($_GET["id_groupe"]) ? $_GET["id_groupe"] :NULL);
$id_classe = isset($_POST["id_classe"]) ? $_POST["id_classe"] :(isset($_GET["id_classe"]) ? $_GET["id_classe"] :NULL);
$id_aid = isset($_POST["id_aid"]) ? $_POST["id_aid"] :(isset($_GET["id_aid"]) ? $_GET["id_aid"] :NULL);
$type_selection = isset($_POST["type_selection"]) ? $_POST["type_selection"] :(isset($_GET["type_selection"]) ? $_GET["type_selection"] : NULL);
$total_eleves = isset($_POST["total_eleves"]) ? $_POST["total_eleves"] :(isset($_GET["total_eleves"]) ? $_GET["total_eleves"] :0);
$type_absence_eleve = isset($_POST["type_absence_eleve"]) ? $_POST["type_absence_eleve"] : -1;
$type = AbsenceEleveTypeQuery::create()->findPk($type_absence_eleve);
$date_absence_eleve = isset($_POST["date_absence_eleve"]) ? $_POST["date_absence_eleve"] :NULL;
$date_fin = $date_debut = new DateTime(str_replace("/",".",$date_absence_eleve));
$idUtilisateur = $utilisateur->getPrimaryKey();

// initialisation des variables
$heure_debut_appel = 0;
$heure_fin_appel = 0;
$id_cours = 0;
$id_creneau = 0;
$current_cours = null;
$current_classe = null;
$current_groupe = null;
$current_aid = null;
$current_creneau = null;

if ($type_selection == 'id_groupe') {
    $current_groupe = GroupeQuery::create()->findPk($id_groupe);
    if ($current_groupe == null) {
	$message_enregistrement .= "<span style='color :red'>Erreur : Problème avec le paramètre id_groupe ".$id_groupe."</span><br/>";
    }
} elseif ($type_selection == 'id_classe') {
    $current_classe = ClasseQuery::create()->findPk($id_classe);
    if ($current_classe == null) {
	$message_enregistrement .= "<span style='color :red'>Erreur : Problème avec le paramètre id_classe</span><br/>";
    }
} elseif ($type_selection == 'id_aid') {
    $current_aid = AidDetailsQuery::create()->findPk($id_aid);
    if ($current_aid == null) {
	$message_enregistrement .= "<span style='color :red'>Erreur : Problème avec le paramètre id_aid</span><br/>";
    }
}

$id_groupe = null;
$id_classe = null;
$id_aid = null;
if ($current_groupe != null) {$id_groupe = $current_groupe->getId();}
if ($current_classe != null) {$id_classe = $current_classe->getId();}
if ($current_aid != null) {$id_aid = $current_aid->getId();}


// ligne 141 enregistrement_saisie_groupe.php

if ($date_absence_eleve != null) {
    try {
	$dt_date_absence_eleve = new DateTime(str_replace("/",".",$date_absence_eleve));
    } catch (Exception $x) {
	$message_enregistrement .= "<span class='rouge'>Erreur : Mauvais format de date d'absence.</span><br/>";
    }
} else {
    $message_enregistrement .= "<span class='rouge'>Erreur : La date d'absence doit etre précisée.</span><br/>";
}


if ($id_groupe == null && $id_classe == null && $id_aid == null) {
  	$message_enregistrement .= '<span style="color :red">Erreur : Il faut au moins une classe, une aid, un groupe pour faire un appel.</span><br/>';
}

if ($message_enregistrement != '') {
	//il y a une erreur, on arrete la saisie
	include("saisir_groupe.php");
	die();
}


/*
 * Données propres à chaque saisies
 * objet $eleve = EleveQuery::create()->findPk($_POST['id_eleve_absent'][$i]);
 * $_POST['commentaire_absence_eleve'][$i]
 * $heure_debut = new DateTime($_POST['heure_debut_absence_eleve'][$i]);
 * $heure_fin = new DateTime($_POST['heure_fin_absence_eleve'][$i]);
 * $id_creneau
 * $id_cours
 * 
 * 
 */
//$type_selection = 'id_cours';
$message_enregistrement = '';
$message_erreur = '';
$saisiesAbs = isset ($_POST['active_absence_eleve']) ? $_POST['active_absence_eleve'] : array();
$message_erreur_eleve = array();
$message_reussite = '';

if(!isset($_POST['temoin_dernier_champ_formulaire_transmis'])) {
	$message_enregistrement="<span style='color:red'>Il semble qu'une partie des champs de formulaire n'ait pas été transmise.<br />Y aurait-il des limitations au nombre de variables transmises sur votre serveur.</span><br />";
}

// On stocke les valeurs pour les récupérer après la boucle sur les élèves
$last_id_aid= $id_classe;
$last_id_aid= $id_aid;

if ('cours' == $_SESSION['creneau_cours_eleve']) {
	$id_classe = NULL;
	$id_aid = NULL;
}

$dernierEleve = '';
// $cpt=0;
foreach ($saisiesAbs as $key=>$indice) {
	
	
	set_time_limit(20);
	
	$id_eleve_absent = isset ($_POST['id_eleve_absent'][$key]) ? $_POST['id_eleve_absent'][$key] :  NULL;
	
	//on cherche l'élève
	if (!isset ($eleve) || ($eleve->getId() != $id_eleve_absent)) {
		unset ($eleve);
		$eleve = EleveQuery::create()->findPk($id_eleve_absent);		
	}
	
	
	$id_creneau = isset ($_POST['id_creneau'][$key]) ? $_POST['id_creneau'][$key] :  NULL;
	$id_cours = isset ($_POST['id_cours'][$key]) ? $_POST['id_cours'][$key] :  NULL;
	$id_groupe = isset ($_POST['id_groupe_el'][$key]) ? $_POST['id_groupe_el'][$key] :  NULL;
	
	if ($type_selection == 'id_cours') {
		if (getSettingValue("autorise_edt_tous") != 'y') {
		$message_erreur .= "<span style='color :red'>Erreur : un cours a été spécifié mais le module edt est désactivé</span><br/>";
		}else {
		$current_cours = EdtEmplacementCoursQuery::create()->findPk($id_cours);
		if ($current_cours != null) {
			$current_creneau = $current_cours->getEdtCreneau();
			$current_groupe = $current_cours->getGroupe();
			$current_aid = $current_cours->getAidDetails();
		} else {
			$message_erreur .= "<span style='color :red'>Erreur : Problème avec le paramètre id_cours".$id_cours."</span><br/>";
		}
		}
	} else {
		
		if ('cours' != $_SESSION['creneau_cours_eleve']) {
			$current_creneau = EdtCreneauQuery::create()->findPk($id_creneau);
			if ($current_creneau == null) {
				$message_erreur .= "<span style='color :red'>Erreur : Problème avec le paramètre id_creneau ".$id_creneau."</span><br/>";
			}
		
		}
	}
	
	if ($message_erreur != '') {
		$message_enregistrement = $message_erreur;
		//on arrete la saisie
		include("saisir_groupe.php");
		die();
	}	
	
	
	
	
	
    if ($eleve == null) {
		$message_enregistrement .= "Probleme avec l'id eleve : ".$_POST['id_eleve_absent'][$i]."<br/>";
		continue;
    }
	
	$commentaire='';
	
	
	$heure_debut_appel = isset($_POST["heure_debut_appel"][$key]) ? $_POST["heure_debut_appel"][$key] : NULL;
	if ($heure_debut_appel != null) {
		try {
		$heure_debut_appel = new DateTime($heure_debut_appel);
		$dt_date_debut_appel = clone $dt_date_absence_eleve;
		$dt_date_debut_appel->setTime($heure_debut_appel->format('H'), $heure_debut_appel->format('i'));
		} catch (Exception $x) {
		$message_enregistrement .= "<span style='color :red'>Erreur : Mauvais format d'heure de debut de saisie.</span><br/>";
		}
	} else {
		$message_enregistrement .= "<span style='color :red'>Erreur : heure de debut de saisie non precisée.</span><br/>";
	}

	
	$heure_fin_appel = isset($_POST["heure_fin_appel"][$key]) ? $_POST["heure_fin_appel"][$key] : NULL;	
	if ($heure_fin_appel != null) {
		try {
		$heure_fin_appel = new DateTime($heure_fin_appel);
		$dt_date_fin_appel = clone $dt_date_absence_eleve;
		$dt_date_fin_appel->setTime($heure_fin_appel->format('H'), $heure_fin_appel->format('i'));
		} catch (Exception $x) {
		$message_enregistrement .= "<span class='rouge'>Erreur : Mauvais format d'heure de fin de saisie.</span><br/>";
		}
	} else {
		$message_enregistrement .= "<span class='rouge'>Erreur : heure de fin de saisie non precisée.</span><br/>";
	}

	if ($id_creneau == null && $id_cours == null) {
		$message_enregistrement .= '<span class="rouge">Erreur : Il faut au moins un creneau ou un cours pour faire un appel.</span><br/>';
	}

	if ($message_enregistrement != '') {
		//il y a une erreur, on arrete la saisie
		include("saisir_groupe.php");
		die();
	}
	
	//on verifie si l'utilisateur a le droit de saisir cela
	$message_erreur .= verif_debut_fin_saisie($dt_date_debut_appel, $dt_date_fin_appel, $utilisateur, $current_cours);

	if ($message_erreur != '') {
		$message_enregistrement .= '<span class="rouge">Erreur sur l\'enregistrement : '.$message_erreur.'</span>';
		//on arrete la saisie
		include("saisir_groupe.php");
		die();
	}
	
	// $cpt++;
	// echo $cpt.' - '.date('H:m:s').'<br />';
	// on crée la saisie
	unset ($saisie);
	$saisie = new AbsenceEleveSaisie();
    $saisie->setEleveId($eleve->getId());
    $saisie->setIdEdtCreneau($id_creneau);
    $saisie->setIdEdtEmplacementCours($id_cours);
    $saisie->setIdGroupe($id_groupe);
    $saisie->setIdClasse($id_classe);
    $saisie->setIdAid($id_aid);
    $saisie->setCommentaire($commentaire);
    $saisie->setDebutAbs($dt_date_debut_appel);
    $saisie->setFinAbs($dt_date_fin_appel);
	$saisie->setUtilisateurId($utilisateur->getPrimaryKey());	
	
	
    if (isset ($message_erreur_eleve[$key]) && $message_erreur_eleve[$key] != '') {
	//il y a des erreurs, on evite l'enregistrement
    } else {
		// if ($saisie->validate()) {
		if (TRUE) {
			$eleve->getAbsenceEleveSaisies();
			$eleve->addAbsenceEleveSaisie($saisie);
			$saisie->save();
			$message_reussite .= "<a href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."'>Saisie enregistrée pour l'élève : ".$eleve->getNom().' '.$eleve->getPrenom().' → '.$heure_debut_appel->format('H:i')."</a>";
			if (isset($saisie_discipline) && $saisie_discipline == true) {
				$message_enregistrement .= " &nbsp;<a href='../mod_discipline/saisie_incident_abs2.php?id_absence_eleve_saisie=".
				$saisie->getId()."&return_url=no_return".add_token_in_url()."'>Saisir un incident disciplinaire pour l'élève : ".$eleve->getNom().' '.$eleve->getPrenom()."</a>";
			}
			$message_reussite .= "<br/>";
		} else {
			$message_erreur_eleve[$key] .= format_verif_failures($saisie);
		}
    }

    if (isset ($message_erreur_eleve[$key]) && $message_erreur_eleve[$key] != '') {
		$message_enregistrement .= '<span class="rouge" >Erreur pour l\'enregistrement de l\'élève '.$eleve->getNom().' '.$eleve->getPrenom().' : </span>'.$message_erreur_eleve[$key];
    }
	
	
	$message_erreur_eleve[$key] = '';
    if (isset($type_absence_eleve) && $type_absence_eleve != -1) {
			// on crée un traitement ou on met à jour le précédent
		if (isset ($_POST['multi_traitement'][$eleve->getId()]) || $dernierEleve == '' || $dernierEleve != $eleve->getId()) {
			$type = AbsenceEleveTypeQuery::create()->findPk($type_absence_eleve);
			if ($type != null) {
				if ($type->isStatutAutorise($utilisateur->getStatut())) {
					//on va creer un traitement avec le type d'absence associé
					unset ($traitement);
					$traitement = new AbsenceEleveTraitement();
					$traitement->addAbsenceEleveSaisie($saisie);
					$traitement->setAbsenceEleveType($type);
					$traitement->setUtilisateurProfessionnel($utilisateur);
					if ($type->getModeInterface() == "DISCIPLINE" && getSettingValue("active_mod_discipline")=='y') {
						//on affiche un lien pour saisir le module discipline
						$saisie_discipline = true;
					}
					$dernierEleve = $eleve->getId();
				} else {
					$message_erreur_eleve[$key] .= "Type d'absence non autorisé pour ce statut : ".$type_absence_eleve."<br/>";
				}
			} else {
				$message_erreur_eleve[$key] .= "Probleme avec l'id du type d'absence : ".$type_absence_eleve."<br/>";
			}
			
			// on crée une notification si demandé
			/* */
			if (isset($_POST['Valider']) && 'SavePrint' == $_POST['Valider']) {
				//echo 'Notification ';
				unset ($notification);
				$notification = new AbsenceEleveNotification();
				$notification->setUtilisateurProfessionnel($utilisateur);
				$notification->setAbsenceEleveTraitement($traitement);

				//on met le type courrier par défaut
				$notification->setTypeNotification(AbsenceEleveNotificationPeer::TYPE_NOTIFICATION_COURRIER);

				$result = $notification->preremplirResponsables();
				if ($result) $notification->setStatutEnvoi(AbsenceEleveNotificationPeer::STATUT_ENVOI_PRET_A_ENVOYER);

				// on ajoute le motif au besoin
				if (isset ($_POST["type_motif_eleve"])) {
					$traitement->setAbsenceEleveMotif(AbsenceEleveMotifQuery::create()->findPk($_POST["type_motif_eleve"]));
				}
				$traitement->save();
				$notification->save();
			}
			
			 /* */
		} else {
			
			$traitement->addAbsenceEleveSaisie($saisie);
			// on ajoute le motif au besoin
			if (isset ($_POST["type_motif_eleve"])) {
				$traitement->setAbsenceEleveMotif(AbsenceEleveMotifQuery::create()->findPk($_POST["type_motif_eleve"]));
				$traitement->save();
			}
			
		}
				
    }
	
	

	
}

		

// On récupère les valeurs
$id_classe = $last_id_aid;
$id_aid = $last_id_aid;

$message_enregistrement .=	$message_reussite;

// echo date('H:i:s').'<br />';
//  $affiche_debug=debug_var();

include("saisir_groupe.php");
	




?>
