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


/*==========================================================================
 *             On charge les données
 ==========================================================================*/
$msgErreur = "";
include_once 'lib/fonctions.php';
include_once 'chargeDonnees.php';

$_AP = 1;
$_EPI = 2;
$_PARCOURS = 3;

//++++++++++++++++++++++++++++++++++
// Initialisations
$msg_erreur_remplissage="";
$liste_creation_auto_element_programme="";

//$gepiYear=getSettingValue("gepiYear");
//$millesime=preg_replace("/[^0-9]{1,}[0-9]*/","",$gepiYear);
$date_creation=strftime("%Y-%m-%d");

$longueur_limite_lignes_adresse["ligne1"]=50;
$longueur_limite_lignes_adresse["ligne2"]=50;
$longueur_limite_lignes_adresse["ligne3"]=50;
$longueur_limite_lignes_adresse["ligne4"]=50;
$longueur_limite_lignes_adresse["code-postal"]=10;
$longueur_limite_lignes_adresse["commune"]=100;

$tab_erreur_adr=array();
//++++++++++++++++++++++++++++++++++

$xml = new DOMDocument('1.0', 'utf-8');

$xml->preserveWhiteSpace = false;
$xml->formatOutput = true;

$items = $xml->createElementNS('urn:fr:edu:scolarite:lsun:bilans:import','lsun-bilans');
	
$xml->appendChild($items);

	$items->setAttributeNS(
		'http://www.w3.org/2001/XMLSchema-instance', // xmlns namespace URI
		'xsi:schemaLocation',
		'urn:fr:edu:scolarite:lsun:bilans:import import-bilan-complet.xsd'
	);

	$items->setAttribute(
		'schemaVersion',
		'2.0'
	);
		/*----- Entête -----*/
		$entete = $xml->createElement('entete');
			$etablissement= getSettingValue('gepiSchoolRne');
			$noeudsEntete = array("editeur"=>'Contributeurs GEPI','application'=>'GEPI','etablissement'=>$etablissement);
			foreach ($noeudsEntete as $cle=>$valeur) {
				$noeudEntete = $xml->createElement($cle,$valeur);
				$entete->appendChild($noeudEntete);
			}
			
	$items->appendChild($entete);
	
	/*----- Données -----*/
	$donnees = $xml->createElement('donnees');
	$items->appendChild($donnees);
		
		/*----- Responsables-etab -----*/
		$responsablesEtab = $xml->createElement('responsables-etab');
		// TODO: il faudra gérer un tableau
		while ($responsable = $listeResponsables->fetch_object()){
				$noeudResponsableEtab = $xml->createElement('responsable-etab');
				$attResponsableEtabId= $xml->createAttribute('id');
				$attResponsableEtabId->value = "RESP_".$responsable->id;
				$noeudResponsableEtab->appendChild($attResponsableEtabId);
				$attResponsableEtabLibelle= $xml->createAttribute('libelle');
				$attResponsableEtabLibelle->value = $responsable->suivi_par;
				$noeudResponsableEtab->appendChild($attResponsableEtabLibelle);
				$responsablesEtab->appendChild($noeudResponsableEtab);
			}
			
		$donnees->appendChild($responsablesEtab);
		
		/*----- Élèves -----*/
		$eleves = $xml->createElement('eleves');
		while ($eleve = $listeEleves->fetch_object()){
				$noeudEleve = $xml->createElement('eleve');
					$attributsEleve = array('id'=>'EL_'.$eleve->id_eleve,'id-be'=>$eleve->ele_id,
						'nom'=>substr($eleve->nom,0,100),
						'prenom'=>substr($eleve->prenom,0,100),
						'code-division'=>substr($eleve->classe,0,8));
					foreach ($attributsEleve as $cle=>$valeur) {
						$attEleve = $xml->createAttribute($cle);
						$attEleve->value = $valeur;
						$noeudEleve->appendChild($attEleve);
					}
				$eleves->appendChild($noeudEleve);
			}
		$donnees->appendChild($eleves);
		
		/*----- Périodes -----*/
		$periodes = $xml->createElement('periodes');
		while ($periode = $listePeriodes->fetch_object()){
				$noeudPeriode = $xml->createElement('periode');
					if($periode->num_periode < 10) {$num_periode = "0".$periode->num_periode;} else {$num_periode = $periode->num_periode;}
					$attributsPeriode = array('id'=>'P_'.$num_periode,'millesime'=>$millesime,
						'indice'=>$periode->num_periode,'nb-periodes'=>$listePeriodes->num_rows);
					foreach ($attributsPeriode as $cle=>$valeur) {
						$attPeriode = $xml->createAttribute($cle);
						$attPeriode->value = $valeur;
						$noeudPeriode->appendChild($attPeriode);
					}
				$periodes->appendChild($noeudPeriode);
			}
		$donnees->appendChild($periodes);
		
		/*----- Disciplines -----*/
		$disciplines = $xml->createElement('disciplines');
		while ($discipline = $listeDisciplines->fetch_object()){
				$noeudDiscipline = $xml->createElement('discipline');
					//if($discipline->id < 10) {$id_discipline = "0".$discipline->id;} else {$id_discipline = $discipline->id;}
				$codesAutorises = array('S', 'O', 'F', 'L', 'R', 'X');
				if (!in_array($discipline->code_modalite_elect, $codesAutorises)) {
					$msgErreur .= "La matière $discipline->nom_complet a pour modalité $discipline->code_modalite_elect. Cette modalité n'est pas autorisée. <a href='../gestion/gerer_modalites_election_enseignements.php' target='_blank'>Corriger</a> / <a href='../utilisateurs/modif_par_lots.php#update_xml_sts' target='_BLANK' >mettre à jour d'après le XML STS</a>.<br />";
				}
				if($discipline->code_matiere=="") {
					$msgErreur .= "La matière ".$discipline->nom_complet." a un code vide <em>(non rattaché à une <strong>nomenclature</strong>)</em>. Le XML ne va pas être valide. <a href='../matieres/modify_matiere.php?current_matiere=".$discipline->id_matiere."' target='_blank'>Corriger</a>.<br />";
				}
				$attributsDiscipline = array('id'=>'DI_'.$discipline->code_matiere.$discipline->code_modalite_elect,'code'=>$discipline->code_matiere,
					'modalite-election'=>$discipline->code_modalite_elect,'libelle'=>htmlspecialchars($discipline->nom_complet));
				foreach ($attributsDiscipline as $cle=>$valeur) {
					$attDiscipline = $xml->createAttribute($cle);
					$attDiscipline->value = $valeur;
					$noeudDiscipline->appendChild($attDiscipline);
				}
				$disciplines->appendChild($noeudDiscipline);
			}
		$donnees->appendChild($disciplines);
		
		/*----- Enseignants -----*/
		$enseignants = $xml->createElement('enseignants');
		while ($enseignant = $listeEnseignants->fetch_object()){
				$noeudEnseignant = $xml->createElement('enseignant');
						
					//on ne conserve que les chiffres pour id-sts
					if (!$enseignant->numind) {
						$msgErreur .= $enseignant->nom." ".$enseignant->prenom." n'a pas d'identifiant STS, vous devez corriger cette erreur avant de continuer&nbsp;: <em><a href=\"../utilisateurs/modify_user.php?user_login=".$enseignant->login."\" target=\"_BLANK\" >Corriger</a></em><br />";
						continue;
					}
					if (!$enseignant->nom ) {
						$msgErreur .= "L'enseignant $enseignant->numind n'a pas de nom, vous devez corriger cette erreur.<br />";
					}
					if (!$enseignant->prenom) {
						$msgErreur .= "L'enseignant $enseignant->nom ($enseignant->numind) n'a pas de nom, vous devez corriger cette erreur.<br />";
					}
					preg_match_all('#[0-9]+#',$enseignant->numind,$extract);
					$idSts = $extract[0][0];
					$type = $enseignant->type ? $enseignant->type : "local";
					$civilite = $enseignant->civilite == "Mme" ? 'MME' : 'M' ;
					$attributsEnseignant = array('id'=>'ENS_'.$idSts, 'type'=>$type, 'id-sts'=>$idSts,
						'civilite'=>$civilite, 'nom'=>$enseignant->nom, 'prenom'=>$enseignant->prenom);
					foreach ($attributsEnseignant as $cle=>$valeur) {
						$attEnseignant = $xml->createAttribute($cle);
						$attEnseignant->value = $valeur;
						$noeudEnseignant->appendChild($attEnseignant);
					}
				$enseignants->appendChild($noeudEnseignant);
			}
		$donnees->appendChild($enseignants);
		
		/*----- Éléments du programme -----*/
		$elementsProgramme = $xml->createElement('elements-programme');
		// on crée un élément de programme "Pas d'élément de programme saisi pour la période".
		$noeudPasEP = $xml->createElement('element-programme');
		$attributsElementProgramme = array('id'=>'EP_0000', 'libelle'=>"Pas d'élément de programme saisi pour la période");
			foreach ($attributsElementProgramme as $cle=>$valeur) {
				$attElementProgramme = $xml->createAttribute($cle);
				$attElementProgramme->value = $valeur;
				$noeudPasEP->appendChild($attElementProgramme);
			}
			$elementsProgramme->appendChild($noeudPasEP);
		while ($elementProgramme = $listeElementsProgramme->fetch_object()){
			$noeudElementProgramme = $xml->createElement('element-programme');
			$elePro = trim($elementProgramme->libelle) ? substr(htmlspecialchars($elementProgramme->libelle),0,300) : "-";
			$attributsElementProgramme = array('id'=>'EP_'.$elementProgramme->id, 'libelle'=>$elePro);
			foreach ($attributsElementProgramme as $cle=>$valeur) {
				$attElementProgramme = $xml->createAttribute($cle);
				$attElementProgramme->value = $valeur;
				$noeudElementProgramme->appendChild($attElementProgramme);
			}
			$elementsProgramme->appendChild($noeudElementProgramme);
		}
		$donnees->appendChild($elementsProgramme);
		
		/*----- Parcours -----*/
if (getSettingValue("LSU_Parcours") != "n") {
	if ($listeParcoursCommuns->num_rows) {
		$parcoursCommuns = $xml->createElement('parcours-communs');
		while ($parcoursCommun = $listeParcoursCommuns->fetch_object()){
				$noeudParcoursCommun= $xml->createElement('parcours-commun');
					if($parcoursCommun->periode < 10) {$num_periode = "0".$parcoursCommun->periode;} else {$num_periode = $parcoursCommun->periode;}
					$parcoursClasse = getClasses($parcoursCommun->classe)->fetch_object()->classe;
					$attributsParcoursCommun = array('periode-ref'=>'P_'.$num_periode, 'code-division'=>substr(htmlspecialchars($parcoursClasse),0,8));
					foreach ($attributsParcoursCommun as $cle=>$valeur) {
						$attParcoursCommun = $xml->createAttribute($cle);
						$attParcoursCommun->value = $valeur;
						$noeudParcoursCommun->appendChild($attParcoursCommun);
					}
					
					$listeParcours = getParcoursCommuns(NULL, $parcoursCommun->classe, $parcoursCommun->periode);
					//var_dump($listeParcours);
					while ($parcours = $listeParcours->fetch_object()){
						//echo $parcours->description.'<br>';
						$noeudParcours = $xml->createElement('parcours',$parcours->description);
						$attributsParcours = array('code'=>$parcours->codeParcours);
						foreach ($attributsParcours as $cle=>$valeur) {
							$attParcours = $xml->createAttribute($cle);
							$attParcours->value = $valeur;
							$noeudParcours->appendChild($attParcours);
						}
						$noeudParcoursCommun->appendChild($noeudParcours);
					}
					
				$parcoursCommuns->appendChild($noeudParcoursCommun);
				
			}
		$donnees->appendChild($parcoursCommuns);
	}
}	

			/*----- Vie scolaire -----*/

if ($listeVieScoCommun->num_rows) {
		$viesScolairesCommuns = $xml->createElement('vies-scolaires-communs');
		while ($vieScoCommun = $listeVieScoCommun->fetch_object()) {
			$noeudVieSco =  $xml->createElement('vie-scolaire-commun');
			if($vieScoCommun->periode < 10) {$num_periode = "0".$vieScoCommun->periode;} else {$num_periode = $vieScoCommun->periode;}
			$attributsVieSco = array('periode-ref'=>'P_'."$num_periode" , 'code-division'=>"$vieScoCommun->classe");
			foreach ($attributsVieSco as $cle=>$valeur) {
				$attVieSco = $xml->createAttribute($cle);
				$attVieSco->value = $valeur;
				$noeudVieSco->appendChild($attVieSco);
			}
			$tmp_chaine=nettoye_texte_vers_chaine($vieScoCommun->appreciation);
			$comVieScoCommun = $xml->createElement('commentaire', substr(trim($tmp_chaine),0,600));
			$noeudVieSco->appendChild($comVieScoCommun);
			$viesScolairesCommuns->appendChild($noeudVieSco);
		}
		
			
		$donnees->appendChild($viesScolairesCommuns);
}

			/*----- epis -----*/
if (getSettingValue("LSU_traite_EPI") != "n") {
			$epis = $xml->createElement('epis');
			$listeEPICommun = getEPICommun();
			while ($epiCommun = $listeEPICommun->fetch_object()) { 
				$noeudEpiCommun = $xml->createElement('epi');
				$matieres = getMatieresEPICommun($epiCommun->id);
				$refDisciplines = "";
				foreach ($matieres as $matiere) {
					$ref = "DI_".getMatiereOnMatiere($matiere["id_matiere"])->code_matiere.$matiere["modalite"];
					assureDisciplinePresente($ref);
					$refDisciplines .= $ref." ";
	
	
				}
				$attributsEpiCommun = array('id'=>"EPI_$epiCommun->id", 'intitule'=>"$epiCommun->intituleEpi", 'thematique'=>"$epiCommun->codeEPI", 'discipline-refs'=>"$refDisciplines");
				foreach ($attributsEpiCommun as $cle=>$valeur) {
					$attsEpiCommun = $xml->createAttribute($cle);
					$attsEpiCommun->value = $valeur;
					$noeudEpiCommun->appendChild($attsEpiCommun);
				}
				$noeudDexcriptionEpiCommun = $xml->createElement('description', nettoye_texte_vers_chaine($epiCommun->descriptionEpi));
				$noeudEpiCommun->appendChild($noeudDexcriptionEpiCommun);
				$epis->appendChild($noeudEpiCommun);
			}
		$donnees->appendChild($epis);
//}

			/*----- epis-groupes -----*/
//if (FALSE) {
			$episGroupes = $xml->createElement('epis-groupes');
			$listeEpisGroupes = getEpisGroupes();
			while ($episGroupe = $listeEpisGroupes->fetch_object()) { 
				$noeudEpisGroupes = $xml->createElement('epi-groupe');
				//id="EPI_GROUPE_02"
				$attributsEpiGroupe = array('id'=>"EPI_GROUPE_".$episGroupe->id, 'intitule'=>$episGroupe->nom, 'epi-ref'=>'EPI_'.$episGroupe->id_epi );
				foreach ($attributsEpiGroupe as $cle=>$valeur) {
					$attsEpiGroupe = $xml->createAttribute($cle);
					$attsEpiGroupe->value = $valeur;
					
					$noeudEpisGroupes->appendChild($attsEpiGroupe);
				}
				
				// Commentaire → Résumé + appréciation du groupe
				$CommentaireEPI1 = trim(getResumeAid($episGroupe->id));
				$commentairesGroupe = getCommentaireGroupe($episGroupe->id,$episGroupe->periode);
				if ($commentairesGroupe->num_rows) {
					//echo "coucou ".trim($commentairesGroupe->fetch_object()->appreciation)."<br>";
					$CommentaireEPI1 .= " ".trim($commentairesGroupe->fetch_object()->appreciation);
					
				}
				$tmp_chaine=nettoye_texte_vers_chaine($CommentaireEPI1);
				$CommentaireEPI = substr($tmp_chaine, 0, 600);
				//echo $CommentaireEPI;
				if ($CommentaireEPI) {
					$noeudEpisGroupesCommentaire = $xml->createElement('commentaire',$CommentaireEPI);
					$noeudEpisGroupes->appendChild($noeudEpisGroupesCommentaire);
				}
				
								
				$episGroupes->appendChild($noeudEpisGroupes);
				// enseignants
				$noeudEnseigneDis = $xml->createElement('enseignants-disciplines');

				//$modaliteEns = getModaliteGroupeAP($episGroupe->id);
				$modaliteEns = getModaliteGroupe($episGroupe->id);
				
				if ($modaliteEns->num_rows) {
					while ($ensModalite = $modaliteEns->fetch_object()) {
						$noeudProf = $xml->createElement('enseignant-discipline');
						$attsMat =  $xml->createAttribute('discipline-ref');
						$attsMat->value = 'DI_'.$ensModalite->code_matiere.$ensModalite->modalite;
						$noeudProf->appendChild($attsMat);
						$prof = substr($ensModalite->numind,1);
						$attsProf =  $xml->createAttribute('enseignant-ref');
						$attsProf->value = 'ENS_'.$prof;
						$noeudProf->appendChild($attsProf);
						$noeudEnseigneDis->appendChild($noeudProf);
					}
				}
				
				$noeudEpisGroupes->appendChild($noeudEnseigneDis);
				
			}
		$donnees->appendChild($episGroupes);
}

if (getSettingValue("LSU_traite_AP") != "n") {
			/*----- acc-persos -----*/
	$listeApCommuns = getAPCommun();
	if ($listeApCommuns->num_rows) {
		
		$accPersos = $xml->createElement('acc-persos');
		
		while ($apCommun = $listeApCommuns->fetch_object()) {
			$noeudApCommun = $xml->createElement('acc-perso');
			$disciplines = getDisciplines($apCommun->id);
			$matieresAP = "";
			while ($matiere = $disciplines->fetch_object()) {
				$matieresAP .= "DI_".$matiere->id_enseignements.$matiere->modalite." ";
			}
			$attributsAPCommun = array('id'=>'ACC_PERSO_'.$apCommun->id , 'intitule'=>  substr(trim($apCommun->intituleAP), 0, 150) , 'discipline-refs'=>"$matieresAP");
			foreach ($attributsAPCommun as $cle=>$valeur) {
				$attsApCommun = $xml->createAttribute($cle);
				$attsApCommun->value = $valeur;
				$noeudApCommun->appendChild($attsApCommun);
			}
			if (substr(trim($apCommun->descriptionAP),0,600)) {
				$noeudApDescription = $xml->createElement('description', substr(trim($apCommun->descriptionAP),0,600));
				$noeudApCommun->appendChild($noeudApDescription);
			}
			
			//descriptionAP
			$accPersos->appendChild($noeudApCommun);
		}
		$donnees->appendChild($accPersos);
	//}

		
			/*----- acc-persos-groupes -----*/
	
		$accPersosGroupes = $xml->createElement('acc-persos-groupes');
		$listeApGroupes = getApGroupes();
		while ($apGroupe = $listeApGroupes->fetch_object()) {
			$noeudApGroupes = $xml->createElement('acc-perso-groupe');
			$attributsEpiGroupe = array('id'=>"ACC_PERSO_GROUPE_".$apGroupe->id, 'intitule'=>$apGroupe->nom, 'acc-perso-ref'=>'ACC_PERSO_'.$apGroupe->id_ap );
			//echo " - AP ".$apGroupe->id. " ";
			foreach ($attributsEpiGroupe as $cle=>$valeur) {
				$attsEpiGroupe = $xml->createAttribute($cle);
				$attsEpiGroupe->value = $valeur;

				$noeudApGroupes->appendChild($attsEpiGroupe);
			}
			//On a que 1 commentaire de groupe dans l'export alors qu'on peut en avoir 1 par trimestre, on prend le dernier
			
			$commentairesGroupeAp = getCommentaireGroupe($apGroupe->id);
			while ($commentaire = $commentairesGroupeAp->fetch_object()) {
				if (trim($commentaire->appreciation)) {
					$tmp_chaine=nettoye_texte_vers_chaine($commentaire->appreciation);
					$noeudComGroupeAp = $xml->createElement('commentaire',substr(trim($tmp_chaine),0,600));
					$noeudApGroupes->appendChild($noeudComGroupeAp);
				}
			}
			
			// on ajoute les enseignants
			//print_r($apGroupe);
			//echo '<br>11<br>';
			$profMatiere = getModaliteGroupeAP($apGroupe->id);
			//print_r($profMatiere);
			//echo '<br><br>';
			if ($profMatiere->num_rows) {
				$noeudProfs = $xml->createElement('enseignants-disciplines');
			//print_r($profMatiere);
				while ($ensModalite = $profMatiere->fetch_object()) {
					$noeudProf = $xml->createElement('enseignant-discipline');
					$attsMat =  $xml->createAttribute('discipline-ref');
					//$matiere = getMatiereOnMatiere($ensModalite->matiere);
					$attsMat->value = 'DI_'.$ensModalite->code_matiere.$ensModalite->modalite;
					$noeudProf->appendChild($attsMat);
					$prof = substr(getUtilisateur($ensModalite->login)->numind,1);
					$attsProf =  $xml->createAttribute('enseignant-ref');
					$attsProf->value = 'ENS_'.$prof;
					$noeudProf->appendChild($attsProf);
					$noeudProfs->appendChild($noeudProf);
				}
				$noeudApGroupes->appendChild($noeudProfs);
				
			$accPersosGroupes->appendChild($noeudApGroupes);
			}
		}
		
		$donnees->appendChild($accPersosGroupes);	
	}
}

		// Initialisation des tableau des cycles et MEF associés
		$tab_cycle=array();
		$tab_domaines=array("CPD_FRA", "CPD_ETR", "CPD_SCI", "CPD_ART", "MET_APP", "FRM_CIT", "SYS_NAT", "REP_MND");

		/*----- Bilans périodiques -----*/
		$bilansPeriodiques = $xml->createElement('bilans-periodiques');
		
		
		$eleves = getElevesExport();
		while ($eleve = $eleves->fetch_object()) {
			$desAcquis = FALSE;
			$noeudBilanElevePeriodique = $xml->createElement('bilan-periodique');
			$respEtabElv = "RESP_".$eleve->id_resp_etab;
			
			//$profResponsable = getUtilisateur($eleve->professeur)->numind;
			$profResponsable = substr(getUtilisateur($eleve->professeur)->numind,1);
			
			if($eleve->periode < 10) {$num_periode = "0".$eleve->periode;} else {$num_periode = $eleve->periode;}
			$datecolarite = dateScolarite($eleve->login, $eleve->periode);
			$attributsElevePeriode = array('prof-princ-refs'=>"ENS_".$profResponsable , 'eleve-ref'=>"EL_".$eleve->id_eleve , 'periode-ref'=>'P_'.$num_periode , 'date-conseil-classe'=>$eleve->date_conseil , 'date-scolarite'=>"$datecolarite" , 'date-verrou'=>"$eleve->date_verrou" , 'responsable-etab-ref'=>"$respEtabElv" );
			foreach ($attributsElevePeriode as $cle=>$valeur) {
				$attsElevePeriode = $xml->createAttribute($cle);
				$attsElevePeriode->value = $valeur;

				$noeudBilanElevePeriodique->appendChild($attsElevePeriode);
			}
			
			$listeAcquis = $xml->createElement('liste-acquis');
			$acquisEleves = getAcquisEleve($eleve->login, $eleve->periode);
			// <acquis discipline-ref="DI_030602" enseignant-refs="ENS_0123456789ABE" element-programme-refs="EP_05" moyenne-eleve="18/20" moyenne-structure="15/20">
			// <appreciation>Appréciation pour la matière espagnol</appreciation>
			// matieres_notes - matiere_element_programme - matieres_appreciations
			while ($acquisEleve = $acquisEleves->fetch_object()) {
				$desAcquis = TRUE;
				$noeudAcquis = $xml->createElement('acquis');
				$matiere = $acquisEleve->code_matiere;
				$moyenne = getMoyenne($acquisEleve->id_groupe);
				$modalite = getModalite($acquisEleve->id_groupe, $eleve->login, $acquisEleve->mef_code, $acquisEleve->code_matiere);
				$matiere = "DI_".$acquisEleve->code_matiere.$modalite;
				
				$donneesProfs = getProfGroupe ($acquisEleve->id_groupe);
				$prof = "";
				while ($profMatiere = $donneesProfs->fetch_object()) {
					$prof .= "ENS_".$profMatiere->numind." ";
				}
				
				$elementsProgramme = getEPeleve ($eleve->login, $acquisEleve->id_groupe,$eleve->periode );
				$elementProgramme = "";
				while ($elemProgramme = $elementsProgramme->fetch_object()) {
					$elementProgramme .= "EP_".$elemProgramme->idEP." ";
					//TODO VÉRIFIER que l'élément de programme existe
				}
				if (!$elementProgramme) {
					$elementProgramme = "EP_0000";
					$absenceEP = true;
					if(!isset($liste_absenceEP)) {
						$liste_absenceEP="";
					}
					$liste_absenceEP.="<span style='color:red'>".get_nom_prenom_eleve($eleve->login)." n'a pas d'élément de programme en ".$acquisEleve->id_matiere." en période ".$eleve->periode.".</span><br />";
					//$msgErreur .= get_nom_prenom_eleve($eleve->login)." n'a pas d'élément de programme en $acquisEleve->id_matiere. Vérifiez si c'est une erreur ou volontaire<br>";
					//$msgErreur .= $eleve->login." n'a pas d'élément de programme en $matiere, votre fichier n'est pas valide.<br>";
				}
				$attributsAcquis = array('discipline-ref'=>$matiere , 'enseignant-refs'=>$prof, 'element-programme-refs'=>$elementProgramme, 'moyenne-structure'=>$moyenne."/20");
				
				$note = $acquisEleve->note;
				if (intval($note)) {
					$attributsAcquis['moyenne-eleve'] = $note."/20";
				} else {
					if (getStatutNote($eleve->login,$acquisEleve->id_groupe,$eleve->periode)) {
						$attributsAcquis['eleve-non-note'] = "1";
					} else {
						$attributsAcquis['moyenne-eleve'] = $note."/200";
					}
				}
				
				
				foreach ($attributsAcquis as $cle=>$valeur) {
					$attsAcquis= $xml->createAttribute($cle);
					$attsAcquis->value = $valeur;
					$noeudAcquis->appendChild($attsAcquis);
					
				}
				$tmp_chaine=nettoye_texte_vers_chaine($acquisEleve->appreciation);
				$noeudAcquisAppreciation = $xml->createElement('appreciation' ,substr(trim($tmp_chaine),0,600));
				$noeudAcquis->appendChild($noeudAcquisAppreciation);
				$listeAcquis->appendChild($noeudAcquis);
			}
			
			$noeudBilanElevePeriodique->appendChild($listeAcquis);
			
			if ((getSettingValue("LSU_traite_EPI") != "n") && (getSettingValue("LSU_traite_EPI_Elv") != "n")) {
				// non obligatoire
				$episEleve = getAidEleve($eleve->login, $_EPI, $eleve->periode);
				if ($episEleve->num_rows) {
					//echo "<br /><p>$eleve->login EPI: $_EPI en période $eleve->periode<br />";
					//var_dump($episEleve);
					$listeEpisEleve = $xml->createElement('epis-eleve');
					$existeEpi = false;
					while ($epiEleve = $episEleve->fetch_object()) {
						/*
						echo "<pre>";
						print_r($epiEleve);
						echo "</pre>";
						*/
						//on verifie que le groupe est bien déclaré
						if (!verifieGroupeEPI($epiEleve->id_aid)) {
							$msgErreur .= "<p class='rouge'>".$eleve->nom." ".$eleve->prenom." a un EPI qui n'est pas déclaré en administrateur, il ne sera pas exporté (AID $epiEleve->indice_aid).</p>";
						} else {
							$noeudEpiEleve = $xml->createElement('epi-eleve');
							$attsEpisEleve = $xml->createAttribute('epi-groupe-ref');
							$attsEpisEleve->value = "EPI_GROUPE_".$epiEleve->id_aid;
							$noeudEpiEleve->appendChild($attsEpisEleve);
							$existeEpi = TRUE;
							$commentaireEpiElv = getCommentaireAidElv($eleve->login, $epiEleve->id_aid, $eleve->periode);
							if ($commentaireEpiElv->num_rows) {
								$tmp_chaine=nettoye_texte_vers_chaine($commentaireEpiElv->fetch_object()->appreciation);
								$comm = substr(trim($tmp_chaine),0,600);
								if ($comm) {
									$noeudComEpiEleve = $xml->createElement('commentaire', $comm);
									$noeudEpiEleve->appendChild($noeudComEpiEleve);
								}
							}
							$listeEpisEleve->appendChild($noeudEpiEleve);
							
						}
						
					}
					if ($existeEpi) {
						$noeudBilanElevePeriodique->appendChild($listeEpisEleve);
					}
					
				}
			}
			
			if ((getSettingValue("LSU_traite_AP") != "n") && (getSettingValue("LSU_traite_AP_Elv") != "n")) {
				// non obligatoire
				$apEleve = getAidEleve($eleve->login, $_AP, $eleve->periode);
				if ($apEleve->num_rows) {
					$listeAccPersosEleve = $xml->createElement('acc-persos-eleve');
					while ($accPersosEleve = $apEleve->fetch_object()) {
						$noeudAPEleve = $xml->createElement('acc-perso-eleve');
						$attsAPEleve = $xml->createAttribute('acc-perso-groupe-ref');
						$attsAPEleve->value = "ACC_PERSO_GROUPE_".$accPersosEleve->id_aid;
						$noeudAPEleve->appendChild($attsAPEleve);
						
						$commentaireAPEleve = getCommentaireAidElv($eleve->login, $accPersosEleve->id_aid, $eleve->periode);
						if ($commentaireAPEleve->num_rows) {
							$tmp_chaine=nettoye_texte_vers_chaine($commentaireAPEleve->fetch_object()->appreciation);
							$comm = substr(trim($tmp_chaine),0,600);
							if ($comm) {
								$noeudComApEleve = $xml->createElement('commentaire', $comm);
								$noeudAPEleve->appendChild($noeudComApEleve);
							}
						}
						$listeAccPersosEleve->appendChild($noeudAPEleve);
					}
					$noeudBilanElevePeriodique->appendChild($listeAccPersosEleve);
				}
			}
			
			if ((getSettingValue("LSU_Parcours") != "n") && (getSettingValue("LSU_ParcoursElv") != "n")) {
				// non obligatoire
				$parcoursEleve = getAidEleve($eleve->login, $_PARCOURS, $eleve->periode);
				if ($parcoursEleve->num_rows) {
					$listeParcoursEleve = $xml->createElement('liste-parcours');
					$creeParcours = FALSE;
					while ($parcoursElv = $parcoursEleve->fetch_object()) {
						$typeParcoursEleve = getCodeParcours($parcoursElv->id_aid);
						if ($typeParcoursEleve->num_rows) {
							$typeParcoursEleve = $typeParcoursEleve->fetch_object();
							
							$commentaireEleve = getCommentaireEleveParcours($eleve->login,$parcoursElv->id_aid, $eleve->periode);//
							if ($commentaireEleve->num_rows) {
								$tmp_chaine=nettoye_texte_vers_chaine($commentaireEleve->fetch_object()->appreciation);
								$commentaireEleve = substr(trim($tmp_chaine),0,600);
								if (strlen($commentaireEleve)) {
									$noeudParcoursEleve = $xml->createElement('parcours',$commentaireEleve);
									$attsParcoursEleve = $xml->createAttribute('code');
									$attsParcoursEleve->value = $typeParcoursEleve->codeParcours;
									$noeudParcoursEleve->appendChild($attsParcoursEleve);
									$listeParcoursEleve->appendChild($noeudParcoursEleve);
									$creeParcours = TRUE;
								}
								
							}							
						}
					}
					if ($creeParcours) {
						$noeudBilanElevePeriodique->appendChild($listeParcoursEleve);
					}
					
				}
			}
			
			$modalitesAccompagnement = $xml->createElement('modalites-accompagnement');
			// non obligatoire
			
			$retourAvisElv=getAppConseil($eleve->login , $eleve->periode);
			if ($retourAvisElv->num_rows) {
				$avisElv = $retourAvisElv->fetch_object()->avis;
				$avisConseil = $avisElv;
				$acquisConseils = $xml->createElement('acquis-conseils', $avisConseil);
				$noeudBilanElevePeriodique->appendChild($acquisConseils);
			}
			
			
			
			//$retardEleve = getRetardsEleve($eleve->login , $eleve->periode)->fetch_object();
			$retardEleve = getAbsencesEleve($eleve->login , $eleve->periode);
			$vieScolaire = $xml->createElement('vie-scolaire');
			$retardsJustifies = $retardEleve['absences'] - $retardEleve['nj'];
			//$attributsVieScolaire = array('nb-retards'=>$retardEleve->nb_retards , 'nb-abs-justifiees'=>$retardsJustifies, 'nb-abs-injustifiees'=>$retardEleve->non_justifie);
			$attributsVieScolaire = array('nb-retards'=>$retardEleve['retards'] , 'nb-abs-justifiees'=>$retardsJustifies, 'nb-abs-injustifiees'=>$retardEleve['nj']);
			
			foreach ($attributsVieScolaire as $cle=>$valeur) {
				$attsVieSco= $xml->createAttribute($cle);
				$attsVieSco->value = $valeur;
				$vieScolaire->appendChild($attsVieSco);
			}
			if (trim($retardEleve['appreciation']) && getSettingValue("LSU_commentaire_vie_sco")) {
				// non obligatoire
				$tmp_chaine=nettoye_texte_vers_chaine($retardEleve['appreciation']);
				$comVieSco = $xml->createElement('commentaire', substr(trim($tmp_chaine),0,600));
				$vieScolaire->appendChild($comVieSco);
			}
			
			
			$noeudBilanElevePeriodique->appendChild($vieScolaire);
			
			
			$socle = $xml->createElement('socle');
			// non obligatoire
			if (getSettingValue("LSU_Donnees_socle") == "y") {

				$mef_code_ele=$eleve->mef_code;
				if(!isset($tab_cycle[$mef_code_ele])) {
					$tmp_tab_cycle_niveau=calcule_cycle_et_niveau($mef_code_ele, "", "");
					$cycle_eleve_courant=$tmp_tab_cycle_niveau["mef_cycle"];
					$niveau_eleve_courant=$tmp_tab_cycle_niveau["mef_niveau"];
					$tab_cycle[$mef_code_ele]=$cycle_eleve_courant;
				}

				$tab_positionnement_trouve=array();
				$sql="SELECT DISTINCT sec.* FROM socle_eleves_composantes sec, eleves e WHERE sec.ine=e.no_gep AND e.ele_id='".$eleve->ele_id."' AND sec.cycle='".$tab_cycle[$mef_code_ele]."';";
				$res_ele_socle=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_ele_socle)>0) {
					while($lig_ele_socle=mysqli_fetch_object($res_ele_socle)) {

						if((in_array($lig_ele_socle->code_composante, $tab_domaines))&&(!in_array($lig_ele_socle->code_composante, $tab_positionnement_trouve))) {
							$tab_positionnement_trouve[]=$lig_ele_socle->code_composante;
						}

						$socleElv=$xml->createElement('domaine');

						$attsSocle=$xml->createAttribute('code');
						$attsSocle->value=$lig_ele_socle->code_composante;
						$socleElv->appendChild($attsSocle);

						$attsSocle=$xml->createAttribute('positionnement');
						$attsSocle->value=$lig_ele_socle->niveau_maitrise;
						$socleElv->appendChild($attsSocle);

						$socle->appendChild($socleElv);
					}

					if(count($tab_domaines)==count($tab_positionnement_trouve)) {
						$noeudBilanElevePeriodique->appendChild($socle);
					}
					elseif(isset($socle)) {
						unset($socle);
					}
				}
			}

			if (getSettingValue("LSU_Donnees_responsables") != "n") {
				$noeudResponsables = $xml->createElement('responsables');
				// non obligatoire
				$responsablesEleve = getResponsableEleve($eleve->ele_id);
				while ($responsable = $responsablesEleve->fetch_object()) {
					//echo $responsable->pers_id.' '.$responsable->civilite.' '.$responsable->nom.' '.$responsable->prenom.' '.$responsable->resp_legal.' ';
					//echo $responsable->adr1.' '.$responsable->adr2.' '.$responsable->adr3.' '.$responsable->adr4.' '.$responsable->cp.' '.$responsable->pays.' '.$responsable->commune;
					//echo "<br>";
					$legal1 = $responsable->resp_legal == 1 ? 1 : 0;
					$legal2 = $responsable->resp_legal == 2 ? 1 : 0;
					$respElv = $xml->createElement('responsable');
					$attributsResponsable = array('civilite'=>$responsable->civilite , 'nom'=>$responsable->nom, 'prenom'=>$responsable->prenom, 'legal1'=>$legal1, 'legal2'=>$legal2);
					foreach ($attributsResponsable as $cle=>$valeur) {
						$attsResp = $xml->createAttribute($cle);
						$attsResp->value = $valeur;
						$respElv->appendChild($attsResp);
						
					}
					
					if (trim($responsable->adr1) && $responsable->cp && $responsable->commune) {
						$noeudAdresse = $xml->createElement('adresse');
						$responsableAdr1 = trim($responsable->adr1) ? trim($responsable->adr1) : "-";
						$attributsAdresse = array('ligne1'=>$responsableAdr1, 'code-postal'=>$responsable->cp, 'commune'=>$responsable->commune);
						if (trim($responsable->adr2) != "") {$attributsAdresse['ligne2'] = $responsable->adr2;}
						if (trim($responsable->adr3) != "") {$attributsAdresse['ligne3'] = $responsable->adr3;}
						if (trim($responsable->adr4) != "") {$attributsAdresse['ligne4'] = $responsable->adr4;}
						foreach ($attributsAdresse as $cle=>$valeur) {
							if (!$valeur) {continue ;}

							if((isset($longueur_limite_lignes_adresse[$cle]))&&(mb_strlen($valeur)>$longueur_limite_lignes_adresse[$cle])&&(!in_array("longueur_adresse_resp_".$responsable->pers_id, $tab_erreur_adr))) {
								$msg_erreur_remplissage.="La ".$cle." de l'adresse postale de <a href='../responsables/modify_resp.php?pers_id=".$responsable->pers_id."' target='_blank'>".$responsable->civilite." ".$responsable->nom." ".$responsable->prenom."</a> dépasse ".$longueur_limite_lignes_adresse[$cle]." caractères.<br />";
								$tab_erreur_adr[]="longueur_adresse_resp_".$responsable->pers_id;
							}
							$attAdresse = $xml->createAttribute($cle);
							$attAdresse->value = $valeur;
							$noeudAdresse->appendChild($attAdresse);
						}
						$respElv->appendChild($noeudAdresse);
					}
					
					$noeudResponsables->appendChild($respElv);
				}
				//echo "<br>";
				
				
				
				if ($responsablesEleve->num_rows) {
					$noeudBilanElevePeriodique->appendChild($noeudResponsables);
				}
				
			}
			
			
			
			
			if ($desAcquis) {$bilansPeriodiques->appendChild($noeudBilanElevePeriodique);}
		}	
		$donnees->appendChild($bilansPeriodiques);


	/*
		// Bilans de fin de cycle

        <bilans-cycle>
            <bilan-cycle eleve-ref="EL_01" cycle="3" millesime="2015" date-creation="2016-06-21" date-verrou="2016-06-23T06:00:00" responsable-etab-ref="RESP_01" prof-princ-refs="ENS_0123456789ABC">
                <socle>
                   <domaine code="CPD_FRA" positionnement="1"/>
                   <domaine code="CPD_ETR" positionnement="0"/>
                   <domaine code="CPD_SCI" positionnement="3"/>
                   <domaine code="CPD_ART" positionnement="4"/>
                   <domaine code="MET_APP" positionnement="1"/>
                   <domaine code="FRM_CIT" positionnement="2"/>
                   <domaine code="SYS_NAT" positionnement="3"/>
                   <domaine code="REP_MND" positionnement="4"/>
                </socle>
                <synthese>Synthèse des acquis scolaires pour Victor</synthese>
                <enseignement-complement code="LCA" positionnement="2" />
                <responsables>
                    <responsable civilite="M" nom="NOM RESPONSABLE 1" prenom="Prénom responsable 1" lien-parente="PERE" legal1="true" legal2="false">
                        <adresse ligne1="68" ligne2="GALERIE DE L'ARLEQUIN" ligne3="GRENOBLE" ligne4="BP 241" code-postal="38036" commune="GRENOBLE Cedex 2"/>
                    </responsable>
                </responsables>
            </bilan-cycle>
            <bilan-cycle eleve-ref="EL_02" cycle="3" millesime="2015" date-creation="2016-06-22" date-verrou="2016-06-24T06:00:00" responsable-etab-ref="RESP_01" prof-princ-refs="ENS_0123456789ABC">
                <socle>
                   <domaine code="CPD_FRA" positionnement="4"/>
                   <domaine code="CPD_ETR" positionnement="3"/>
                   <domaine code="CPD_SCI" positionnement="2"/>
                   <domaine code="CPD_ART" positionnement="1"/>
                   <domaine code="MET_APP" positionnement="4"/>
                   <domaine code="FRM_CIT" positionnement="3"/>
                   <domaine code="SYS_NAT" positionnement="2"/>
                   <domaine code="REP_MND" positionnement="1"/>
                </socle>
                <synthese>Synthèse des acquis scolaires pour Aline</synthese>
                <enseignement-complement code="AUC" />
            </bilan-cycle>
        </bilans-cycle>

	*/

		if (getSettingValue("LSU_Donnees_BilanFinCycle") == "y") {
			$bilansFinCycle = $xml->createElement('bilans-cycle');

			$on_a_des_BilansFinCycle=FALSE;

			// Problème : On récupère chaque élève autant de fois qu'on a de période
			//$eleves = getElevesExport();
			$eleves = getElevesExportCycle();

			while ($eleve = $eleves->fetch_object()) {

				$mef_code_ele=$eleve->mef_code;
				if(!isset($tab_cycle[$mef_code_ele])) {
					$tmp_tab_cycle_niveau=calcule_cycle_et_niveau($mef_code_ele, "", "");
					$cycle_eleve_courant=$tmp_tab_cycle_niveau["mef_cycle"];
					$niveau_eleve_courant=$tmp_tab_cycle_niveau["mef_niveau"];
					$tab_cycle[$mef_code_ele]=$cycle_eleve_courant;
				}

				$cycle_eleve_courant=$tmp_tab_cycle_niveau["mef_cycle"];
				$niveau_eleve_courant=$tmp_tab_cycle_niveau["mef_niveau"];

				if($tab_cycle[$mef_code_ele]=="") {
					$msg_erreur_remplissage.="Cycle courant de ".$eleve->nom." ".$eleve->prenom." en classe de ".get_chaine_liste_noms_classes_from_ele_login($eleve->login)." non identifié.<br />";
					//$generer_bilan_pour_cet_eleve=false;
				}

				if((($cycle_eleve_courant==3)&&($niveau_eleve_courant==6))||
				(($cycle_eleve_courant==4)&&($niveau_eleve_courant==3))) {
					$on_a_un_BilanFinCycle_pour_cet_eleve=FALSE;
					$generer_bilan_pour_cet_eleve=true;

					$noeudBilanEleveFinCycle = $xml->createElement('bilan-cycle');

					// Récupérer la dernière classe de l'élève... même si ce n'est pas dans la liste de classes à exporter?
					// Ou ajouter un test pour ne retenir que les élèves qui sont dans la classe en dernière période?
					$sql="SELECT jec.id_classe, 
								c.classe, 
								c.suivi_par, 
								jec.periode, 
								DATE_FORMAT(p.date_verrouillage, '%Y-%m-%d') AS date_verrou
							FROM j_eleves_classes jec, 
								classes c, 
								periodes p 
							WHERE jec.periode=p.num_periode AND 
								jec.id_classe=p.id_classe AND 
								jec.id_classe=c.id AND 
								jec.login='".$eleve->login."' 
							ORDER BY periode DESC LIMIT 1;";
					//echo "$sql<br />";
					$res_class=mysqli_query($mysqli, $sql);
					$lig_clas=mysqli_fetch_object($res_class);

					$date_verrou=$lig_clas->date_verrou;

					$sql="SELECT lr.id AS id_resp_etab FROM lsun_responsables AS lr WHERE lr.login='".$lig_clas->suivi_par."';";
					//echo "$sql<br />";
					$res_lr=mysqli_query($mysqli, $sql);
					if(mysqli_num_rows($res_lr)>0) {
						$lig_lr=mysqli_fetch_object($res_lr);

						$respEtabElv = "RESP_".$lig_lr->id_resp_etab;
					}
					else {
						$msg_erreur_remplissage.="Le principal/adjoint chargé suivi de la classe de ".$lig_clas->classe." n'est pas défini.<br />";
						$generer_bilan_pour_cet_eleve=false;
					}

					$sql="SELECT jep.professeur 
							FROM j_eleves_professeurs jep 
							WHERE jep.id_classe='".$lig_clas->id_classe."' AND 
								jep.login='".$eleve->login."';";
					//echo "$sql<br />";
					$res_profprinc=mysqli_query($mysqli, $sql);
					if(mysqli_num_rows($res_profprinc)>0) {
						$lig_profprinc=mysqli_fetch_object($res_profprinc);
						$profResponsable = substr(getUtilisateur($lig_profprinc->professeur)->numind,1);
					}
					else {
						$msg_erreur_remplissage.="Le professeur principal chargé suivi de ".$eleve->nom." ".$eleve->prenom." en classe de ".$lig_clas->classe." n'est pas défini.<br />";
						$generer_bilan_pour_cet_eleve=false;
					}

					/*
							<bilan-cycle 
								eleve-ref="EL_02" 
								cycle="3" 
								millesime="2015" 
								date-creation="2016-06-22" 
								date-verrou="2016-06-24T06:00:00" 
								responsable-etab-ref="RESP_01" 
								prof-princ-refs="ENS_0123456789ABC">
					*/

					if($generer_bilan_pour_cet_eleve) {
						$attributsEleveCycle = array('eleve-ref'=>"EL_".$eleve->id_eleve , 
											'cycle'=>$tab_cycle[$mef_code_ele], 
											'millesime'=>$millesime , 
											'date-creation'=>$date_creation , 
											'date-verrou'=>"$date_verrou" , 
											'responsable-etab-ref'=>"$respEtabElv", 
											'prof-princ-refs'=>"ENS_".$profResponsable );
						foreach ($attributsEleveCycle as $cle=>$valeur) {
							$attsEleveCycle = $xml->createAttribute($cle);
							$attsEleveCycle->value = $valeur;

							$noeudBilanEleveFinCycle->appendChild($attsEleveCycle);
						}

						$socle = $xml->createElement('socle');

// A VERIFIER : Il peut y avoir un domaine avec AB (absent)?

						// Récupération des positionnements dans les domaines du socle
						$tab_positionnement_trouve=array();
						$sql="SELECT DISTINCT sec.* FROM socle_eleves_composantes sec, eleves e WHERE sec.ine=e.no_gep AND e.ele_id='".$eleve->ele_id."' AND sec.cycle='".$tab_cycle[$mef_code_ele]."' AND niveau_maitrise!='' AND niveau_maitrise!='0';";
						//echo "$sql<br />";
						$res_ele_socle=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res_ele_socle)>0) {
							while($lig_ele_socle=mysqli_fetch_object($res_ele_socle)) {
								if((in_array($lig_ele_socle->code_composante, $tab_domaines))&&(!in_array($lig_ele_socle->code_composante, $tab_positionnement_trouve))) {
									$tab_positionnement_trouve[]=$lig_ele_socle->code_composante;
								}

								$socleElv=$xml->createElement('domaine');

								$attsSocle=$xml->createAttribute('code');
								$attsSocle->value=$lig_ele_socle->code_composante;
								$socleElv->appendChild($attsSocle);

								$attsSocle=$xml->createAttribute('positionnement');
								$attsSocle->value=$lig_ele_socle->niveau_maitrise;
								$socleElv->appendChild($attsSocle);

								$socle->appendChild($socleElv);
							}
						}

						// DEBUG:
						//echo "count(\$tab_domaines)=".count($tab_domaines)."<br />count(\$tab_positionnement_trouve)=".count($tab_positionnement_trouve)."<br />";
						// A-t-on les 8 postionnements?
						if(count($tab_domaines)==count($tab_positionnement_trouve)) {
							$noeudBilanEleveFinCycle->appendChild($socle);

							// On vérifie aussi qu'on a une synthèse:
							$sql="SELECT DISTINCT sec.* FROM socle_eleves_syntheses sec, eleves e WHERE sec.ine=e.no_gep AND e.ele_id='".$eleve->ele_id."' AND sec.cycle='".$tab_cycle[$mef_code_ele]."' AND sec.synthese!='';";
							//echo "$sql<br />";
							$res_ele_synthese=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res_ele_synthese)>0) {
								$lig_ele_synthese=mysqli_fetch_object($res_ele_synthese);

								$synthese=$xml->createElement('synthese' ,$lig_ele_synthese->synthese);

								$noeudBilanEleveFinCycle->appendChild($synthese);

								// DEBUG:
								//echo "On a un bilan pour cet élève.<br />";
								// On a à la fois les 8 domaines du socle et la synthèse
								$on_a_un_BilanFinCycle_pour_cet_eleve=TRUE;
							}

						}
						elseif(isset($socle)) {
							unset($socle);
						}

						if ($on_a_un_BilanFinCycle_pour_cet_eleve) {

							// Il faut aussi les Enseignements de complément
							/*
							    <enseignement-complement code="LCA" positionnement="2" />
							ou si on n'a pas d'enseignement de complément à remonter:
							    <enseignement-complement code="AUC" />
							*/
							$sql="SELECT seec.*, jgec.code FROM j_groupes_enseignements_complement jgec, 
										socle_eleves_enseignements_complements seec 
									WHERE jgec.id_groupe=seec.id_groupe AND 
										(seec.positionnement='1' OR seec.positionnement='2') AND 
										seec.ine='".$eleve->no_gep."';";
							//echo "$sql<br />";
							$res_seec=mysqli_query($mysqli, $sql);
							if(mysqli_num_rows($res_seec)>0) {
								while($lig_seec=mysqli_fetch_object($res_seec)) {
									$noeudEnseignementComplement=$xml->createElement('enseignement-complement');

									// Ca ne devrait pas arriver
									if($lig_seec->code=="AUC") {
										$attributsEnseignementComplement = array('code'=>"AUC");
									}
									else {
										$attributsEnseignementComplement = array('code'=>$lig_seec->code , 'positionnement'=>$lig_seec->positionnement);
									}
									foreach ($attributsEnseignementComplement as $cle=>$valeur) {
										$attsEC = $xml->createAttribute($cle);
										$attsEC->value = $valeur;
										$noeudEnseignementComplement->appendChild($attsEC);
									}
									$noeudBilanEleveFinCycle->appendChild($noeudEnseignementComplement);
								}
							}
							else {
								$noeudEnseignementComplement=$xml->createElement('enseignement-complement');

								$attributsEnseignementComplement = array('code'=>"AUC");
								foreach ($attributsEnseignementComplement as $cle=>$valeur) {
									$attsEC = $xml->createAttribute($cle);
									$attsEC->value = $valeur;
									$noeudEnseignementComplement->appendChild($attsEC);
								}
								$noeudBilanEleveFinCycle->appendChild($noeudEnseignementComplement);
							}

							if (getSettingValue("LSU_Donnees_responsables") != "n") {
								$noeudResponsables = $xml->createElement('responsables');
								// non obligatoire
								$responsablesEleve = getResponsableEleve($eleve->ele_id);
								while ($responsable = $responsablesEleve->fetch_object()) {
									//echo $responsable->pers_id.' '.$responsable->civilite.' '.$responsable->nom.' '.$responsable->prenom.' '.$responsable->resp_legal.' ';
									//echo $responsable->adr1.' '.$responsable->adr2.' '.$responsable->adr3.' '.$responsable->adr4.' '.$responsable->cp.' '.$responsable->pays.' '.$responsable->commune;
									//echo "<br>";
									$legal1 = $responsable->resp_legal == 1 ? 1 : 0;
									$legal2 = $responsable->resp_legal == 2 ? 1 : 0;
									$respElv = $xml->createElement('responsable');
									$attributsResponsable = array('civilite'=>$responsable->civilite , 'nom'=>$responsable->nom, 'prenom'=>$responsable->prenom, 'legal1'=>$legal1, 'legal2'=>$legal2);
									foreach ($attributsResponsable as $cle=>$valeur) {
										$attsResp = $xml->createAttribute($cle);
										$attsResp->value = $valeur;
										$respElv->appendChild($attsResp);
						
									}
					
									if (trim($responsable->adr1) && $responsable->cp && $responsable->commune) {
										$noeudAdresse = $xml->createElement('adresse');
										$responsableAdr1 = trim($responsable->adr1) ? trim($responsable->adr1) : "-";
										$attributsAdresse = array('ligne1'=>$responsableAdr1, 'code-postal'=>$responsable->cp, 'commune'=>$responsable->commune);
										if (trim($responsable->adr2) != "") {$attributsAdresse['ligne2'] = $responsable->adr2;}
										if (trim($responsable->adr3) != "") {$attributsAdresse['ligne3'] = $responsable->adr3;}
										if (trim($responsable->adr4) != "") {$attributsAdresse['ligne4'] = $responsable->adr4;}
										foreach ($attributsAdresse as $cle=>$valeur) {
											if (!$valeur) {continue ;}
											$attAdresse = $xml->createAttribute($cle);
											$attAdresse->value = $valeur;
											$noeudAdresse->appendChild($attAdresse);
										}
										$respElv->appendChild($noeudAdresse);
									}
					
									$noeudResponsables->appendChild($respElv);
								}
								//echo "<br>";

								if ($responsablesEleve->num_rows) {
									$noeudBilanEleveFinCycle->appendChild($noeudResponsables);
								}
							}

							$bilansFinCycle->appendChild($noeudBilanEleveFinCycle);
							$on_a_des_BilansFinCycle=TRUE;
						}
					}
				}
			}

			if ($on_a_des_BilansFinCycle) {
				$donnees->appendChild($bilansFinCycle);
			}
		}


