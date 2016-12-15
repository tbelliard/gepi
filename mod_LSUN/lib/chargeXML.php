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
include_once 'fonctions.php';
include_once 'chargeDonnees.php';



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
					$attributsEleve = array('id'=>'EL_'.$eleve->id_eleve,'id-be'=>$eleve->id_eleve,
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
						$msgErreur .= $enseignant->nom." ".$enseignant->prenom." n'a pas d'identifiant STS, vous devez corriger cette erreur avant de continuer. <em><a href=\"../../utilisateurs/modify_user.php?user_login=".$enseignant->login."\" target=\"_BLANK\" >Corriger</a></em><br>";
						continue;
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
		while ($elementProgramme = $listeElementsProgramme->fetch_object()){
			$noeudElementProgramme = $xml->createElement('element-programme');
			$attributsElementProgramme = array('id'=>'EP_'.$elementProgramme->id, 'libelle'=>substr(htmlspecialchars($elementProgramme->libelle),0,300));
			foreach ($attributsElementProgramme as $cle=>$valeur) {
				$attElementProgramme = $xml->createAttribute($cle);
				$attElementProgramme->value = $valeur;
				$noeudElementProgramme->appendChild($attElementProgramme);
			}
			$elementsProgramme->appendChild($noeudElementProgramme);
		}
		$donnees->appendChild($elementsProgramme);
		
		/*----- Parcours -----*/
if (FALSE) {
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

			/*----- Vie scolaire -----*/
if (FALSE) {
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
			$comVieScoCommun = $xml->createElement('commentaire', $vieScoCommun->appreciation);
			$noeudVieSco->appendChild($comVieScoCommun);
			$viesScolairesCommuns->appendChild($noeudVieSco);
		}
		
			
		$donnees->appendChild($viesScolairesCommuns);
}

			/*----- epis -----*/
if (FALSE) {
			$epis = $xml->createElement('epis');
			$listeEPICommun = getEPICommun();
			while ($epiCommun = $listeEPICommun->fetch_object()) { 
				$noeudEpiCommun = $xml->createElement('epi');
				$matieres = getMatieresEPICommun($epiCommun->id);
				$refDisciplines = "";
				foreach ($matieres as $matiere) {
					$refDisciplines .= "DI_".getMatiereOnMatiere($matiere["id_matiere"])->code_matiere.$matiere["modalite"]." ";
					
				}
				$attributsEpiCommun = array('id'=>"EPI_$epiCommun->id", 'intitule'=>"$epiCommun->intituleEpi", 'thematique'=>"$epiCommun->codeEPI", 'discipline-refs'=>"$refDisciplines");
				foreach ($attributsEpiCommun as $cle=>$valeur) {
					$attsEpiCommun = $xml->createAttribute($cle);
					$attsEpiCommun->value = $valeur;
					$noeudEpiCommun->appendChild($attsEpiCommun);
				}
				$noeudDexcriptionEpiCommun = $xml->createElement('description', $epiCommun->descriptionEpi);
				$noeudEpiCommun->appendChild($noeudDexcriptionEpiCommun);
				$epis->appendChild($noeudEpiCommun);
			}
		$donnees->appendChild($epis);
}

			/*----- epis-groupes -----*/
if (FALSE) {
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
				$CommentaireEPI = getCommentaireGroupe($episGroupe->id,$episGroupe->periode);
				$noeudEpisGroupesCommentaire = $xml->createElement('commentaire');
				if ($CommentaireEPI->num_rows) {
					$noeudEpisGroupesCommentaire->value = $CommentaireEPI->fetch_object()->appreciation;
				}
				$noeudEpisGroupes->appendChild($noeudEpisGroupesCommentaire);
				
								
				$episGroupes->appendChild($noeudEpisGroupes);
				// enseih,a,ts
				$noeudEnseigneDis = $xml->createElement('enseignants-disciplines');
				$profsEPI = getProfsEPI($episGroupe->id);
				while($prof = $profsEPI->fetch_object()) {
					$noeudProf1 = $xml->createElement('enseignant-discipline');
					$attsMat1 =  $xml->createAttribute('discipline-ref');
					//$attsMat1->value = 'DI_';
					//var_dump(getMatiereOnMatiere($prof->matiere1)->code_matiere);
					$attsMat1->value = 'DI_'.getMatiereOnMatiere($prof->matiere1)->code_matiere;
					$noeudProf1->appendChild($attsMat1);
					$attsProf1 =  $xml->createAttribute('enseignant-ref');
					$attsProf1->value = 'ENS_'.$prof->numind1;
					$noeudProf1->appendChild($attsProf1);
					
					$noeudProf2 = $xml->createElement('enseignant-discipline');
					$attsMat2 =  $xml->createAttribute('discipline-ref');
					$attsMat2->value = 'DI_'.getMatiereOnMatiere($prof->matiere2)->code_matiere;
					$noeudProf2->appendChild($attsMat2);
					$attsProf2 =  $xml->createAttribute('enseignant-ref');
					$attsProf2->value = 'ENS_'.$prof->numind2;
					$noeudProf2->appendChild($attsProf2);
					
					$noeudEnseigneDis->appendChild($noeudProf1);
					$noeudEnseigneDis->appendChild($noeudProf2);
				}
				
				$noeudEpisGroupes->appendChild($noeudEnseigneDis);
				
			}
		$donnees->appendChild($episGroupes);
}

if (FALSE) {		
			/*----- acc-persos -----*/
		$accPersos = $xml->createElement('acc-persos');
		$listeApCommuns = getAPCommun();
		while ($apCommun = $listeApCommuns->fetch_object()) {
			$noeudApCommun = $xml->createElement('acc-perso');
			$disciplines = getDisciplines($apCommun->id);
			$matieresAP = "";
			while ($matiere = $disciplines->fetch_object()) {
				$matieresAP .= "DI_".$matiere->id_enseignements.$matiere->modalite." ";
			}
			$attributsAPCommun = array('id'=>'ACC_PERSO_'.$apCommun->id , 'intitule'=>"$apCommun->intituleAP" , 'discipline-refs'=>"$matieresAP");
			foreach ($attributsAPCommun as $cle=>$valeur) {
				$attsApCommun = $xml->createAttribute($cle);
				$attsApCommun->value = $valeur;
				$noeudApCommun->appendChild($attsApCommun);
			}
			$noeudApDescription = $xml->createElement('description', $apCommun->descriptionAP);
			$noeudApCommun->appendChild($noeudApDescription);
			//descriptionAP
			$accPersos->appendChild($noeudApCommun);
		}
		$donnees->appendChild($accPersos);
}

		
			/*----- acc-persos-groupes -----*/
if (FALSE) {
		$accPersosGroupes = $xml->createElement('acc-persos-groupes');
		$listeApGroupes = getApGroupes();
		while ($apGroupe = $listeApGroupes->fetch_object()) {
			$noeudApGroupes = $xml->createElement('acc-perso-groupe');
			$attributsEpiGroupe = array('id'=>"ACC_PERSO_GROUPE_".$apGroupe->id, 'intitule'=>$apGroupe->nom, 'acc-perso-ref'=>'ACC_PERSO_'.$apGroupe->id_ap );
			foreach ($attributsEpiGroupe as $cle=>$valeur) {
				$attsEpiGroupe = $xml->createAttribute($cle);
				$attsEpiGroupe->value = $valeur;

				$noeudApGroupes->appendChild($attsEpiGroupe);
			}
			//On a que 1 commentaire de groupe dans l'export alors qu'on peut en avoir 1 par trimestre, on prend le dernier
			;
			$commentairesGroupeAp = getCommentaireGroupe($apGroupe->id);
			while ($commentaire = $commentairesGroupeAp->fetch_object()) {
				if (trim($commentaire->appreciation)) {
					$noeudComGroupeAp = $xml->createElement('commentaire',trim($commentaire->appreciation));
					$noeudApGroupes->appendChild($noeudComGroupeAp);
				}
			}
			
			
			
			$accPersosGroupes->appendChild($noeudApGroupes);
		}
		
		$donnees->appendChild($accPersosGroupes);
}
		
		/*----- Bilans périodiques -----*/
		$bilansPeriodiques = $xml->createElement('bilans-periodiques');
		
		
		$eleves = getElevesExport();
		while ($eleve = $eleves->fetch_object()) {
			$desAcquis = FALSE;
			$noeudBilanElevePeriodique = $xml->createElement('bilan-periodique');
			$respEtabElv = "RESP_".$eleve->id_resp_etab;
			
			$profResponsable = getUtilisateur($eleve->professeur)->numind;
			$profResponsable = substr(getUtilisateur($eleve->professeur)->numind,1);
			
			//if($periode->num_periode < 10) {$num_periode = "0".$periode->num_periode;} else {$num_periode = $periode->num_periode;}
			if($eleve->periode < 10) {$num_periode = "0".$eleve->periode;} else {$num_periode = $eleve->periode;}
			$attributsElevePeriode = array('prof-princ-refs'=>"ENS_".$profResponsable , 'eleve-ref'=>"EL_".$eleve->id_eleve , 'periode-ref'=>'P_'.$num_periode , 'date-conseil-classe'=>$eleve->date_conseil , 'date-scolarite'=>"$eleve->date_entree" , 'date-verrou'=>"$eleve->date_verrou" , 'responsable-etab-ref'=>"$respEtabElv" );
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
				$noeudAcquisAppreciation = $xml->createElement('appreciation' ,$acquisEleve->appreciation);
				$noeudAcquis->appendChild($noeudAcquisAppreciation);
				$listeAcquis->appendChild($noeudAcquis);
			}
			
			$noeudBilanElevePeriodique->appendChild($listeAcquis);
			
			$listeEpisEleve = $xml->createElement('epis-eleve');
			// non obligatoire
		
			$listeAccPersosEleve = $xml->createElement('acc-persos-eleve');
			// non obligatoire
			
			$listeParcoursEleve = $xml->createElement('liste-parcours');
			// non obligatoire
			
			$modalitesAccompagnement = $xml->createElement('modalites-accompagnement');
			// non obligatoire
			
			$retourAvisElv=getAppConseil($eleve->login , $eleve->periode);
			if ($retourAvisElv->num_rows) {
				$avisElv = $retourAvisElv->fetch_object()->avis;
				$avisConseil = $avisElv;
				$acquisConseils = $xml->createElement('acquis-conseils', $avisConseil);
				$noeudBilanElevePeriodique->appendChild($acquisConseils);
			}
			
			
			
			$retardEleve = getRetardsEleve($eleve->login , $eleve->periode)->fetch_object();
			$vieScolaire = $xml->createElement('vie-scolaire');
			$retardsJustifies = $retardEleve->nb_absences - $retardEleve->non_justifie;
			$attributsVieScolaire = array('nb-retards'=>$retardEleve->nb_retards , 'nb-abs-justifiees'=>$retardsJustifies, 'nb-abs-injustifiees'=>$retardEleve->non_justifie);
			
			foreach ($attributsVieScolaire as $cle=>$valeur) {
				$attsVieSco= $xml->createAttribute($cle);
				$attsVieSco->value = $valeur;
				$vieScolaire->appendChild($attsVieSco);
			}
			$comVieSco = $xml->createElement('commentaire', $retardEleve->appreciation);
			//$vieScolaire->appendChild($comVieSco);
			// non obligatoire
			$noeudBilanElevePeriodique->appendChild($vieScolaire);
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			$socle = $xml->createElement('socle');
			// non obligatoire
			
			$responsables = $xml->createElement('responsables');
			// non obligatoire
			
			
			
			
			
			
			if ($desAcquis) {$bilansPeriodiques->appendChild($noeudBilanElevePeriodique);}
		}	
		$donnees->appendChild($bilansPeriodiques);
		
	$items->appendChild($donnees);