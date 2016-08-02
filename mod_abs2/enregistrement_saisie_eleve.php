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

if ($utilisateur->getStatut()!="cpe" && $utilisateur->getStatut()!="scolarite" && $utilisateur->getStatut()!="autre") {
    die("acces interdit");
}

//debug_var();

//récupération des paramètres de la requète
$id_creneau = isset($_POST["id_creneau"]) ? $_POST["id_creneau"] :(isset($_GET["id_creneau"]) ? $_GET["id_creneau"] :NULL);
$id_cours = isset($_POST["id_cours"]) ? $_POST["id_cours"] :(isset($_GET["id_cours"]) ? $_GET["id_cours"] :NULL);
$type_absence = isset($_POST["type_absence"]) ? $_POST["type_absence"] :NULL;
$commentaire = isset($_POST["commentaire"]) ? $_POST["commentaire"] :NULL;
$total_eleves = isset($_POST["total_eleves"]) ? $_POST["total_eleves"] :(isset($_GET["total_eleves"]) ? $_GET["total_eleves"] :0);
$multisaisie = isset($_POST["multisaisie"]) ? $_POST["multisaisie"] :NULL;

$message_enregistrement = "";

$temoin_erreur_saisie="";

//initialisation des variable
if ($id_creneau != null && $id_creneau != -1) {
    $creneau = EdtCreneauQuery::create()->findPk($id_creneau);
    if ($creneau == null) {
	$message_enregistrement .= "Probleme avec le parametre id_creneau<br/>";
	$temoin_erreur_saisie="y";
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
	$temoin_erreur_saisie="y";
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
	    $temoin_erreur_saisie="y";
	    $type = null;
	}
    } else {
	$message_enregistrement .= "Probleme avec l'id du type d'absence : ".$_POST['type_absence']."<br/>";
	$temoin_erreur_saisie="y";
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
		    $temoin_erreur_saisie="y";
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
	$temoin_erreur_saisie="y";
    }
    try {
	$date_fin = new DateTime(str_replace("/",".",$_POST['date_absence_eleve_fin_saisir_eleve']));
    } catch (Exception $x) {
	$message_enregistrement .= "Mauvais format de date.<br/>";
	$temoin_erreur_saisie="y";
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
	    $temoin_erreur_saisie="y";
	}
	try {
	    $heure_fin = new DateTime($_POST['heure_fin_absence_eleve']);
	} catch (Exception $x) {
	    $message_enregistrement .= "Mauvais format d'heure.<br/>";
	    $temoin_erreur_saisie="y";
	}
    }

    if ($date_debut->format('U') > $date_fin->format('U')) {
	$message_enregistrement .= "La date de debut d'absence ne peut pas être postérieure à la date de fin.<br/>";
	$temoin_erreur_saisie="y";
    }

    if ($message_enregistrement == "") {
	if ($multisaisie == 'y') {
	//on va creer une saisie par jour

		$tab_jour_ouvres=get_tab_jour_ouverture_etab();
		$tab_jour_ouvres_US=get_tab_jour_ouverture_etab_US();
		/*
		echo "tab_jour_ouvres<pre>";
		print_r($tab_jour_ouvres);
		echo "</pre>";

		echo "tab_jour_ouvres_US<pre>";
		print_r($tab_jour_ouvres_US);
		echo "</pre>";
		*/
		$tab_jours_vacances=get_tab_jours_vacances();

		$date_compteur = $date_debut;
		$compteur = 0;
		while (!($date_compteur->format('U') > $date_fin->format('U')) && $compteur < 50) { //maximum 50 saisies simultanées

			// 20160624: Ajouter un test sur le fait que le jour est ouvré ou non... dans des vacances ou non... pb si vacances pour certaines classes seulement.
			//echo "\$date_compteur->format('Ymd')=".$date_compteur->format('Ymd')." ";
			//echo "\$date_compteur->format('U')=".$date_compteur->format('U')." ";
			//echo "\$date_compteur->format('j')=".$date_compteur->format('j')." ".$date_compteur->format('l');

			$compteur = $compteur + 1;
			if(((in_array(mb_strtolower($date_compteur->format('l')), $tab_jour_ouvres))||
			(in_array(mb_strtolower($date_compteur->format('l')), $tab_jour_ouvres_US)))&&
			(!in_array($date_compteur->format('Ymd'), $tab_jours_vacances))) {
				//echo " jour ouvré";
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
			}
			//echo "<br />";
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

$ts_debut_annee=getSettingValue('begin_bookings');
$ts_fin_annee=getSettingValue('end_bookings');
foreach ($saisie_col_modele as $saisie_modele) {
	if(($saisie_modele->getDebutAbs('U')<$ts_debut_annee)||($saisie_modele->getDebutAbs('U')>$ts_fin_annee)) {
		$message_enregistrement .= "<span style='color:red'>Date de début hors année scolaire (<em>du ".strftime("%d/%m/%Y", $ts_debut_annee)." au ".strftime("%d/%m/%Y", $ts_fin_annee)."</em>).<br />Veillez à corriger pour ne pas inquiéter les familles, ni fausser les totaux.</span><br/>";
		$temoin_erreur_saisie="y";
		break;
	}
	if(($saisie_modele->getFinAbs('U')<$ts_debut_annee)||($saisie_modele->getFinAbs('U')>$ts_fin_annee)) {
		$message_enregistrement .= "<span style='color:red'>Date de fin hors année scolaire (<em>du ".strftime("%d/%m/%Y", $ts_debut_annee)." au ".strftime("%d/%m/%Y", $ts_fin_annee)."</em>).<br />Veillez à corriger pour ne pas inquiéter les familles, ni fausser les totaux.</span><br/>";
		$temoin_erreur_saisie="y";
		break;
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
	$temoin_erreur_saisie="y";
	continue;
    }

    foreach ($saisie_col_modele as $saisie_modele) {

	$saisie = clone $saisie_modele;
	$saisie->setEleveId($eleve->getId());

	if ($type != null) {
	    $traitement = new AbsenceEleveTraitement();
	    $traitement->addAbsenceEleveSaisie($saisie);
	    $traitement->setAbsenceEleveType($type);
	    $traitement->setUtilisateurProfessionnel($utilisateur);
	    if ($type->getModeInterface() == "DISCIPLINE" && getSettingValue("active_mod_discipline")=='y') {
		//on affiche un lien pour saisir le module discipline
		$saisie_discipline = true;
	    }
	}

	if ($saisie->validate()) {
	    $saisie->save();

	    $lien_message_enregistrement="<a href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."'>Saisie enregistrée pour l'eleve : ".$eleve->getNom()."</a>";

	    if (isset($traitement)) {
			$modif_lien_message_enregistrement="n";
			if((isset($_POST['id_motif']))&&($_POST['id_motif']!='')&&($_POST['id_motif']!='-1')) {
				$traitement->setAbsenceEleveMotif(AbsenceEleveMotifQuery::create()->findPk($_POST["id_motif"]));
				$modif_lien_message_enregistrement="y";
			}
			if((isset($_POST['id_justification']))&&($_POST['id_justification']!='')&&($_POST['id_justification']!='-1')) {
				$traitement->setAbsenceEleveJustification(AbsenceEleveJustificationQuery::create()->findPk($_POST["id_justification"]));
				$modif_lien_message_enregistrement="y";
			}
			$traitement->save();

			if($modif_lien_message_enregistrement=="y") {
			    $lien_message_enregistrement="<a href='visu_traitement.php?id_traitement=".$traitement->getId()."&id_saisie_appel=".$saisie->getPrimaryKey()."'>Saisie et traitement enregistrés pour l'eleve : ".$eleve->getNom()."</a>";
			}

			// Apporter des précisions sur le traitement:
			$info_traitement=get_infos_traitement_abs2($traitement->getId());
			if($info_traitement!="") {
				$lien_message_enregistrement.=" <span style='font-size:x-small'>(".$info_traitement.")</span>";
			}

			// 20160722: Chercher les saisies pouvant être rattachées
			if((isset($_POST['rattacher_saisies_si_possible']))&&($_POST['rattacher_saisies_si_possible']=="y")) {

				$query = AbsenceEleveSaisieQuery::create();

				$tmp_date_debut = null;
				$tmp_date_fin = null;
				$tmp_id_eleve_array = null;
				//$tmp_id_eleve_array[] = $eleve->getId();
				$tmp_id_saisie_array = null;
				foreach ($traitement->getAbsenceEleveSaisies() as $tmp_saisie) {
					if ($tmp_date_debut == null || $tmp_saisie->getDebutAbs('U') < $tmp_date_debut->format('U')) {
						$tmp_date_debut = clone $tmp_saisie->getDebutAbs(null);
					}
					if ($tmp_date_fin == null || $tmp_saisie->getFinAbs('U') > $tmp_date_fin->format('U')) {
						$tmp_date_fin = clone $tmp_saisie->getFinAbs(null);
					}
					$tmp_id_eleve_array[] = $tmp_saisie->getEleveId();
					$tmp_id_saisie_array[] = $tmp_saisie->getId();
				}
				if ($tmp_date_debut != null) date_date_set($tmp_date_debut, $tmp_date_debut->format('Y'), $tmp_date_debut->format('m'), $tmp_date_debut->format('d') - 1);
				if ($tmp_date_fin != null) date_date_set($tmp_date_fin, $tmp_date_fin->format('Y'), $tmp_date_fin->format('m'), $tmp_date_fin->format('d') + 1);
				$query->filterByPlageTemps($tmp_date_debut, $tmp_date_fin)->filterByEleveId($tmp_id_eleve_array)->filterById($tmp_id_saisie_array, Criteria::NOT_IN);

				$query->distinct();

				$tmp_page_number=1;
				$tmp_item_per_page=1000;
				$tmp_saisies_col = $query->paginate($tmp_page_number, $tmp_item_per_page);
				$nb_saisies_rattachees=0;
				$nb_saisies_conflit=0;
				$liste_saisies_conflit="";

				$results = $tmp_saisies_col->getResults();
				if($results->count()!=0) {
					foreach ($results as $tmp_saisie) {

						$saisies_conflit = $tmp_saisie->getSaisiesContradictoiresManquementObligation();
						if($saisies_conflit->isEmpty()) {
							if (!$traitement->getAbsenceEleveSaisies()->contains($tmp_saisie)) {
								$traitement->addAbsenceEleveSaisie($tmp_saisie);
								$nb_saisies_rattachees++;
							}
						}
						else {
							/*
							foreach ($saisies_conflit as $current_saisie_conflit) {
								$liste_saisies_conflit.="<a href='../mod_abs2/visu_saisie.php?id_saisie=".$current_saisie_conflit->getPrimaryKey()."' style='' title=\"Voir la saisie n°".$current_saisie_conflit->getId()."\">";
								// target='_blank'
								$liste_saisies_conflit.=$current_saisie_conflit->getId();
								$liste_saisies_conflit.="</a>";
								if (!$saisies_conflit->isLast()) {
									$liste_saisies_conflit.=' - ';
								}
							}
							*/

							$nb_saisies_conflit++;
						}
					}

					if (($nb_saisies_rattachees>0)&&(!$traitement->getAbsenceEleveSaisies()->isEmpty())) {
						$traitement->save();

						$lien_message_enregistrement.=" <em style='font-size:x-small; color:darkviolet' title=\"$nb_saisies_rattachees saisie(s) a(ont) été rattachée(s).\">(".$nb_saisies_rattachees.")</em>";
						if($nb_saisies_conflit>0) {
							$lien_message_enregistrement.=" <em style='font-size:x-small; color:red' title=\"Conflit détecté sur $nb_saisies_conflit saisie(s).\nLa ou les saisies correspondantes n'ont pas été rattachées.\">(".$nb_saisies_conflit.")</em>";
						}
					}
					elseif($nb_saisies_conflit>0) {
						$lien_message_enregistrement.=" <em style='font-size:x-small; color:red' title=\"Conflit détecté sur $nb_saisies_conflit saisie(s).\nLa ou les saisies correspondantes n'ont pas été rattachées.\">(".$nb_saisies_conflit;
						//$lien_message_enregistrement.=" <span style='font-size:xx-small;'>(".$liste_saisies_conflit.")</span>";
						$lien_message_enregistrement.=")</em>";
					}
				}

			}
	    }
	    else {
			// Apporter des précisions sur la saisie:
			$info_saisie=get_infos_saisie_abs2($saisie->getId());
			if($info_saisie!="") {
				$lien_message_enregistrement.=" <span style='font-size:x-small'>(".$info_saisie.")</span>";
			}
			// 20160624: Chercher les saisies pouvant être rattachées?
	    }

	    $message_enregistrement .= $lien_message_enregistrement;

	    if (isset($saisie_discipline) && $saisie_discipline == true) {
		$message_enregistrement .= " &nbsp;<a href='../mod_discipline/saisie_incident_abs2.php?id_absence_eleve_saisie=".
		    $saisie->getId()."&return_url=no_return".add_token_in_url()."'>Saisir un incident disciplinaire pour l'élève : ".$eleve->getNom()."</a>";
	    }
	    $message_enregistrement .= "<br/>";
	} else {
	    $message_erreur_eleve[$eleve->getId()] = '';
        foreach ($saisie->getValidationFailures() as $failure) {
    		$message_erreur_eleve[$eleve->getId()] .= $failure->getMessage();
    		$no_br = true;
    		if ($no_br) {
    		    $no_br = false;
    		} else {
    		    $message_erreur_eleve[$eleve->getId()] .= '<br/>';
    		}
	    }
	}
    }
}

include("saisir_eleve.php");
?>
