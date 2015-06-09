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
$type_selection = isset($_POST["type_selection"]) ? $_POST["type_selection"] :(isset($_GET["type_selection"]) ? $_GET["type_selection"] : NULL);
$date_absence_eleve = isset($_POST["date_absence_eleve"]) ? $_POST["date_absence_eleve"] :NULL;
$total_eleves = isset($_POST["total_eleves"]) ? $_POST["total_eleves"] :(isset($_GET["total_eleves"]) ? $_GET["total_eleves"] :0);
$heure_debut_appel = isset($_POST["heure_debut_appel"]) ? $_POST["heure_debut_appel"] :(isset($_GET["heure_debut_appel"]) ? $_GET["heure_debut_appel"] :NULL);
$heure_fin_appel = isset($_POST["heure_fin_appel"]) ? $_POST["heure_fin_appel"] :(isset($_GET["heure_fin_appel"]) ? $_GET["heure_fin_appel"] :NULL);

$message_enregistrement = "";

//initialisation des variables
$current_cours = null;
$current_classe = null;
$current_groupe = null;
$current_aid = null;
$current_creneau = null;

if ($type_selection == 'id_cours') {
    if (getSettingValue("autorise_edt_tous") != 'y') {
	$message_enregistrement .= "<span style='color :red'>Erreur : un cours a été spécifié mais le module edt est désactivé</span><br/>";
    }else {
	$current_cours = EdtEmplacementCoursQuery::create()->findPk($id_cours);
	if ($current_cours != null) {
	    //$id_creneau = $current_cours->getIdDefiniePeriode();
	    $current_creneau = $current_cours->getEdtCreneau();
	    $current_groupe = $current_cours->getGroupe();
	    $current_aid = $current_cours->getAidDetails();
	} else {
	    $message_enregistrement .= "<span style='color :red'>Erreur : Probleme avec le parametre id_cours</span><br/>";
	}
    }
} else {
    $current_creneau = EdtCreneauQuery::create()->findPk($id_creneau);
    if ($current_creneau == null) {
	$message_enregistrement .= "<span style='color :red'>Erreur : Probleme avec le parametre id_creneau</span><br/>";
    }
}

if ($type_selection == 'id_groupe') {
    $current_groupe = GroupeQuery::create()->findPk($id_groupe);
    if ($current_groupe == null) {
	$message_enregistrement .= "<span style='color :red'>Erreur : Probleme avec le parametre id_groupe</span><br/>";
    }
} elseif ($type_selection == 'id_classe') {
    $current_classe = ClasseQuery::create()->findPk($id_classe);
    if ($current_classe == null) {
	$message_enregistrement .= "<span style='color :red'>Erreur : Probleme avec le parametre id_classe</span><br/>";
    }
} elseif ($type_selection == 'id_aid') {
    $current_aid = AidDetailsQuery::create()->findPk($id_aid);
    if ($current_aid == null) {
	$message_enregistrement .= "<span style='color :red'>Erreur : Probleme avec le parametre id_aid</span><br/>";
    }
}

$id_groupe = null;
$id_classe = null;
$id_aid = null;
$id_creneau = null;
$id_cours = null;
if ($current_groupe != null) {$id_groupe = $current_groupe->getId();}
if ($current_classe != null) {$id_classe = $current_classe->getId();}
if ($current_aid != null) {$id_aid = $current_aid->getId();}
if ($current_creneau != null) {$id_creneau = $current_creneau->getIdDefiniePeriode();}
if ($current_cours != null) {$id_cours = $current_cours->getIdCours();}

//test autorisation de saisie du professeur
if ($current_cours == null && $utilisateur->getStatut() == 'professeur' && getSettingValue("abs2_saisie_prof_hors_cours")!='y') {
    //on autorise uniquement les saisies de classe dont le prof est pp
    if ($current_classe != null && $utilisateur->getClasses()->contains($current_classe)) {
	//ok
    } else {
	$message_enregistrement .= "<span style='color :red'>Erreur : Il faut obligatoirement saisir un cours de l'emploi du temps</span><br/>";
    }
}


if ($date_absence_eleve != null) {
    try {
	$dt_date_absence_eleve = new DateTime(str_replace("/",".",$date_absence_eleve));
    } catch (Exception $x) {
	$message_enregistrement .= "<span style='color :red'>Erreur : Mauvais format de date d'absence.</span><br/>";
    }
} else {
    $message_enregistrement .= "<span style='color :red'>Erreur : La date d'absence doit etre précisée.</span><br/>";
}
if ($message_enregistrement != '') {
	//il y a une erreur, on arrete la saisie
	include("saisir_groupe.php");
	die();
}
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
if ($heure_fin_appel != null) {
    try {
	$heure_fin_appel = new DateTime($heure_fin_appel);
	$dt_date_fin_appel = clone $dt_date_absence_eleve;
	$dt_date_fin_appel->setTime($heure_fin_appel->format('H'), $heure_fin_appel->format('i'));
    } catch (Exception $x) {
	$message_enregistrement .= "<span style='color :red'>Erreur : Mauvais format d'heure de fin de saisie.</span><br/>";
    }
} else {
    $message_enregistrement .= "<span style='color :red'>Erreur : heure de fin de saisie non precisée.</span><br/>";
}

if ($id_groupe == null && $id_classe == null && $id_aid == null) {
  	$message_enregistrement .= '<span style="color :red">Erreur : Il faut au moins une classe, une aid, un groupe pour faire un appel.</span><br/>';
}
if ($id_creneau == null && $id_cours == null) {
  	$message_enregistrement .= '<span style="color :red">Erreur : Il faut au moins un creneau ou un cours  pour faire un appel.</span><br/>';
}

if ($message_enregistrement != '') {
	//il y a une erreur, on arrete la saisie
	include("saisir_groupe.php");
	die();
}

//on enregistre le marqueur d'appel (saisie sans eleve)
$message_erreur = '';
$saisie = new AbsenceEleveSaisie();

$saisie->setIdEdtCreneau($id_creneau);
$saisie->setIdEdtEmplacementCours($id_cours);
$saisie->setIdGroupe($id_groupe);
$saisie->setIdClasse($id_classe);
$saisie->setIdAid($id_aid);
$saisie->setUtilisateurId($utilisateur->getPrimaryKey());

//on verifie si l'utilisateur a le droit de saisir cela
$message_erreur .= verif_debut_fin_saisie($dt_date_debut_appel, $dt_date_fin_appel, $utilisateur, $current_cours);
//on vérifie en prime que l'appel est bien fait pendant l'heure en cours.
if ($utilisateur->getStatut() == 'professeur' && getSettingValue("abs2_saisie_prof_decale_journee")!='y' && getSettingValue("abs2_saisie_prof_decale")!='y') {
    $now = new DateTime('now');
    if ($dt_date_debut_appel->format('U') > $now->format('U') || $dt_date_fin_appel->format('U') < $now->format('U')) {
	$message_erreur .= "Appel non autorisé en dehors des heures de cours concernées.<br/>";
    }
}
$saisie->setDebutAbs($dt_date_debut_appel);
$saisie->setFinAbs($dt_date_fin_appel);

$chaine_son_alerte="
<audio id='id_erreur_sound' preload='auto' autobuffer autoplay>
	<!--source src='../sounds/verre_brise.wav' /-->
	<source src='../sounds/default_alarm.wav' />
</audio>\n";

if ($message_erreur != '') {
    $message_enregistrement .= '<span style="color :red">Erreur sur l\'enregistrement du marqueur d\'appel : '.$message_erreur.'</span>';

	if(getSettingAOui("abs2_jouer_sound_erreur")) {
		$message_enregistrement .= $chaine_son_alerte;
	}

    //on arrete la saisie
    include("saisir_groupe.php");
    die();
} else if ($saisie->validate()) {
    $saisie->save();
    $message_enregistrement .= "<a href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."'>Marqueur d'appel enregistré</a><br/>";
} else {
    //on arrete la saisie
    $message_enregistrement .= '<span style="color :red">Erreur sur l\'enregistrement du marqueur d\'appel : '.format_verif_failures($saisie).'</span>';

	if(getSettingAOui("abs2_jouer_sound_erreur")) {
		$message_enregistrement .= $chaine_son_alerte;
	}

    include("saisir_groupe.php");
    die();
}



for($i=0; $i<$total_eleves; $i++) {

    if (!(isset($_POST['id_eleve_absent'][$i]))) {
	continue;
    }
    $id_eleve = $_POST['id_eleve_absent'][$i];
	
	// on teste si une case est cochée
	if (isset ($_POST['check'][$i]) && $_POST['check'][$i]) {
		$_POST['active_absence_eleve'][$i] = TRUE;
		$_POST['type_absence_eleve'][$i] = $_POST['check'][$i];
	}

    //on teste si l'eleve est coché absent
    if (!isset($_POST['active_absence_eleve'][$i])
	&& !(isset($_POST['commentaire_absence_eleve'][$i]) && $_POST['commentaire_absence_eleve'][$i] != null)
	&& !(isset($_POST['type_absence_eleve'][$i]) && $_POST['type_absence_eleve'][$i] != -1)
	    ) {
	continue;
    }
    
    //on cherche l'eleve
    $eleve = EleveQuery::create()->findPk($_POST['id_eleve_absent'][$i]);
    if ($eleve == null) {
	$message_enregistrement .= "Probleme avec l'id eleve : ".$_POST['id_eleve_absent'][$i]."<br/>";
	continue;
    }

    $message_erreur_eleve[$id_eleve] = "";

    $saisie = new AbsenceEleveSaisie();
    $saisie->setEleveId($eleve->getId());
    $saisie->setIdEdtCreneau($id_creneau);
    $saisie->setIdEdtEmplacementCours($id_cours);
    $saisie->setIdGroupe($id_groupe);
    $saisie->setIdClasse($id_classe);
    $saisie->setIdAid($id_aid);
    $saisie->setCommentaire($_POST['commentaire_absence_eleve'][$i]);

    try {
	$date_debut = new DateTime(str_replace("/",".",$_POST['date_debut_absence_eleve'][$i]));
    } catch (Exception $x) {
	$message_erreur_eleve[$id_eleve] .= "Mauvais format de date.<br/>";
	continue;
    }
    try {
	$heure_debut = new DateTime($_POST['heure_debut_absence_eleve'][$i]);
    } catch (Exception $x) {
	$message_erreur_eleve[$id_eleve] .= "Mauvais format d'heure.<br/>";
	continue;
    }
    $date_debut->setTime($heure_debut->format('H'), $heure_debut->format('i'));
    $saisie->setDebutAbs($date_debut);

    try {
	$date_fin = new DateTime(str_replace("/",".",$_POST['date_fin_absence_eleve'][$i]));
    } catch (Exception $x) {
	$message_erreur_eleve[$id_eleve] .= "Mauvais format de date.<br/>";
	continue;
    }
    try {
	$heure_fin = new DateTime($_POST['heure_fin_absence_eleve'][$i]);
    } catch (Exception $x) {
	$message_erreur_eleve[$id_eleve] .= "Mauvais format d'heure.<br/>";
	continue;
    }
    $date_fin->setTime($heure_fin->format('H'), $heure_fin->format('i'));
    $saisie->setFinAbs($date_fin);
    $message_erreur_eleve[$id_eleve] .= verif_debut_fin_saisie($date_debut, $date_fin, $utilisateur, $current_cours);

    $saisie->setUtilisateurId($utilisateur->getPrimaryKey());

    $saisie_discipline = false;



	// 20150404
	// Il faudrait pouvoir tester ici si la saisie peut et doit être rattachée à un traitement existant
	// Pb: si un prof saisit une absence... puis s'il s'agit d'un retard... et qu'il faut ensuite un passage à l'infirmerie, il va être délicat de se baser sur le fait que la saisie est sur le même créneau (englobée?)...

	// Si on teste juste qu'une saisie englobe date_debut_saisie_engloblante<=date_debut_saisie et date_fin_saisie_engloblante>date_fin_saisie
	// ou date_debut_saisie_engloblante<date_debut_saisie et date_fin_saisie_engloblante>=date_fin_saisie
	// est-ce qu'on ne va pas rater des infos dans le cas d'un élève qui arrive avant sa date prévue de retour?




	$info_type_saisie="";
    if (isset($_POST['type_absence_eleve'][$i]) && $_POST['type_absence_eleve'][$i] != -1) {
	$type = AbsenceEleveTypeQuery::create()->findPk($_POST['type_absence_eleve'][$i]);
	if ($type != null) {
		$info_type_saisie=$type->getNom();
		/*
		echo "<pre>";
		print_r($type);
		echo "</pre>";
		*/
	    if ($type->isStatutAutorise($utilisateur->getStatut())) {
		//on va creer un traitement avec le type d'absence associé
		$traitement = new AbsenceEleveTraitement();
		$traitement->addAbsenceEleveSaisie($saisie);
		$traitement->setAbsenceEleveType($type);
		$traitement->setUtilisateurProfessionnel($utilisateur);
		$saisie_discipline = ($type->getModeInterface() == "DISCIPLINE" && getSettingValue("active_mod_discipline")=='y');
	    } else {
		$message_erreur_eleve[$id_eleve] .= "Type d'absence non autorisé pour ce statut : ".$_POST['type_absence_eleve'][$i]."<br/>";
	    }
	} else {
	    $message_erreur_eleve[$id_eleve] .= "Probleme avec l'id du type d'absence : ".$_POST['type_absence_eleve'][$i]."<br/>";
	}
    }

    if ($message_erreur_eleve[$id_eleve] != '') {
	//il y a des erreurs, on evite l'enregistrement
    } else {
	if ($saisie->validate()) {
	    $eleve->getAbsenceEleveSaisies();
	    $eleve->addAbsenceEleveSaisie($saisie);
	    $saisie->save();
	    if (isset($traitement)) {
		$traitement->save();
	    }
	    $message_enregistrement .= "<a href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."'";
	    if($info_type_saisie!="") {
		    $message_enregistrement .= " title='".$info_type_saisie."'";
		}
	    $message_enregistrement .= ">Saisie enregistrée pour l'élève : ".$eleve->getNom().' '.$eleve->getPrenom()."</a>";
	    if (isset($saisie_discipline) && $saisie_discipline == true) {
		$message_enregistrement .= " &nbsp;<a href='../mod_discipline/saisie_incident_abs2.php?id_absence_eleve_saisie=".
		    $saisie->getId()."&return_url=no_return".add_token_in_url()."'>Saisir un incident disciplinaire pour l'élève : ".$eleve->getNom().' '.$eleve->getPrenom()."</a>";
	    }

		if(getSettingAOui('active_mod_alerte')) {
			// Icone du module Alertes
			//$icone_deposer_alerte="no_mail.png";
			$icone_deposer_alerte="module_alerte32.png";
			$message_enregistrement .= " <a href='../mod_alerte/form_message.php?sujet=[".$eleve->getClasse()->getNom()."] ".$eleve->getNom().' '.$eleve->getPrenom()."' title=\"Déposer un message d'alerte à propos de cet élève dans le module Alertes.\" target=\"_blank\"><img src='$gepiPath/images/icons/$icone_deposer_alerte' class='icone16' alt='Dispositif Alertes' /></a>";
		}

	$abs2_rattachement_auto_saisies_englobees=getSettingValue("abs2_rattachement_auto_saisies_englobees");
	if($abs2_rattachement_auto_saisies_englobees=="y") {
		//$acces_visu_traitement=acces("/mod_abs2/visu_traitement.php", $_SESSION['statut']);
		$acces_visu_traitement=false;
		if((acces("/mod_abs2/visu_traitement.php", $_SESSION['statut']))&&(in_array($_SESSION['statut'], array('cpe', 'scolarite', 'administrateur')))) {
			$acces_visu_traitement=true;
		}

		$debut_saisie=strftime("%Y-%m-%d %H:%M:%S", $saisie->getDebutAbs('U'));
		$fin_saisie=strftime("%Y-%m-%d %H:%M:%S", $saisie->getFinAbs('U'));
		// Recherche d'une saisie/traitement englobant la saisie courante
		$sql="SELECT a_s.*, at.id AS id_traitement FROM a_saisies a_s, 
					j_traitements_saisies jts, 
					a_traitements at 
				WHERE a_s.eleve_id='".$saisie->getEleve()->getId()."' AND 
					a_s.deleted_at IS NULL AND 
					at.deleted_at IS NULL AND 
					a_s.id=jts.a_saisie_id AND 
					at.id=jts.a_traitement_id AND 
					((a_s.debut_abs<='".$debut_saisie."' AND a_s.fin_abs>'".$fin_saisie."') OR (a_s.debut_abs<'".$debut_saisie."' AND a_s.fin_abs>='".$fin_saisie."')) AND 
					a_s.id!='".$saisie->getPrimaryKey()."';";
		//$message_enregistrement .= "Test de rattachement pour ".$saisie->getEleve()->getLogin().":<br />$sql<br/>";
		$res=mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($res)==1) {
			$lig_saisie_conteneur=mysqli_fetch_object($res);
			// Pour afficher des infos:
			$saisie_conteneur=AbsenceEleveSaisieQuery::create()->includeDeleted()->findPk($lig_saisie_conteneur->id);
			$message_enregistrement .= " (<em><a href='visu_saisie.php?id_saisie=".$lig_saisie_conteneur->id."' target='_blank' title=\"Saisie englobée par la saisie n°".$lig_saisie_conteneur->id." (du ".$saisie_conteneur->getDebutAbs('d/m/y H:i')." au ".$saisie_conteneur->getFinAbs('d/m/y H:i').")\">saisie englobée</a>";

			$sql="SELECT 1=1 FROM j_traitements_saisies WHERE a_saisie_id='".$saisie->getPrimaryKey()."' AND a_traitement_id='".$lig_saisie_conteneur->id_traitement."';";
			$res=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($res)==0) {
				$sql="INSERT INTO j_traitements_saisies SET  a_saisie_id='".$saisie->getPrimaryKey()."', a_traitement_id='".$lig_saisie_conteneur->id_traitement."';";
				//$message_enregistrement .= "$sql<br/>";
				$insert=mysqli_query($mysqli, $sql);
				if($insert) {
					if($acces_visu_traitement) {
						$message_enregistrement.=" (<a href='visu_traitement.php?id_traitement=".$lig_saisie_conteneur->id_traitement."' title=\"Saisie rattachée au traitement n°".$lig_saisie_conteneur->id_traitement."\" target='_blank'>saisie rattachée</a>)";
					}
					else {
						$message_enregistrement.=" (<span title=\"Saisie rattachée au traitement n°".$lig_saisie_conteneur->id_traitement."\" target='_blank'>saisie rattachée</span>)";
					}
				}
				else {
					$message_enregistrement.=" <span style='color:red'>(erreur lors du rattachement de la saisie)</span>";
				}
			}
			$message_enregistrement.="</em>)";
		}
	}
	$message_enregistrement .= "<br/>";

/*
$traitement = AbsenceEleveTraitementQuery::create()->findPk($id_traitement);
if ($recherche_saisie_a_rattacher == 'oui' && $traitement != null) {

    $traitement_recherche_saisie_a_rattacher=$traitement;

    $date_debut = null;
    $date_fin = null;
    $id_eleve_array = null;
    $id_saisie_array = null;
    foreach ($traitement->getAbsenceEleveSaisies() as $saisie) {//$saisie = new AbsenceEleveSaisie();
	if ($date_debut == null || $saisie->getDebutAbs('U') < $date_debut->format('U')) {
	    $date_debut = clone $saisie->getDebutAbs(null);
	}
	if ($date_fin == null || $saisie->getFinAbs('U') > $date_fin->format('U')) {
	    $date_fin = clone $saisie->getFinAbs(null);
	}
	$id_eleve_array[] = $saisie->getEleveId();
	$id_saisie_array[] = $saisie->getId();
    }
    if ($date_debut != null) date_date_set($date_debut, $date_debut->format('Y'), $date_debut->format('m'), $date_debut->format('d') - 1);
    if ($date_fin != null) date_date_set($date_fin, $date_fin->format('Y'), $date_fin->format('m'), $date_fin->format('d') + 1);
    $query->filterByPlageTemps($date_debut, $date_fin)->filterByEleveId($id_eleve_array)->filterById($id_saisie_array, Criteria::NOT_IN);

$query->distinct();

}
*/




	} else {
	    $message_erreur_eleve[$id_eleve] .= format_verif_failures($saisie);
	}
    }

    if ($message_erreur_eleve[$id_eleve] != '') {
	$message_enregistrement .= '<span style="color :red">Erreur pour l\'enregistrement de l\'élève '.$eleve->getNom().' '.$eleve->getPrenom().' : </span>'.$message_erreur_eleve[$id_eleve];
    }
}

include("saisir_groupe.php");

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
?>
