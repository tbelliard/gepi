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


$niveau_arbo = "2";

// Initialisations files
include("../../lib/initialisationsPropel.inc.php");
require_once("../../lib/initialisations.inc.php");



// si l'appel se fait avec passage de paramètre alors test du token
if ((function_exists("check_token")) && ((count($_POST)<>0) || (count($_GET)<>0))) check_token();

/*
//recherche de l'utilisateur avec propel
$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../../logout.php?auto=1");
	die();
}
 * 
 */

/*==========================================================================
 *             On charge les données
 ==========================================================================*/
include_once 'fonctions.php';
include_once 'chargeDonnees.php';





header('Content-Type: application/xml');

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
				$attResponsableEtabLibelle->value = $responsable->civilite." ".$responsable->nom." ".$responsable->prenom;
				$noeudResponsableEtab->appendChild($attResponsableEtabLibelle);
				$responsablesEtab->appendChild($noeudResponsableEtab);
			}
			/*
				$noeudsResponsableEtab = array('id'=>$idResponsable,'libelle'=>$libelle);
				
				foreach ($noeudsResponsableEtab as $cle=>$valeur) {
					
					$attResponsableEtab->value = $valeur;
					$noeudResponsableEtab->appendChild($attResponsableEtab);
				}
			 * 
			 */
			
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
					$attributsDiscipline = array('id'=>'DI_'.$discipline->code_matiere.$discipline->election,'code'=>$discipline->code_matiere,
						'modalite-election'=>$discipline->election,'libelle'=>htmlspecialchars($discipline->nom_complet));
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
					//if($enseignant->id < 10) {$id_enseignant = "0".$enseignant->id;} else {$id_enseignant = $enseignant->id;}
					//on ne conserve que les chiffres pour id-sts
					preg_match_all('#[0-9]+#',$enseignant->numind,$extract);
					$idSts = $extract[0][0];
					$attributsEnseignant = array('id'=>'ENS_'.$idSts, 'type'=>$enseignant->type, 'id-sts'=>$idSts,
						'civilite'=>$enseignant->civilite, 'nom'=>$enseignant->nom, 'prenom'=>$enseignant->prenom);
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
		
			/*----- Vie scolaire -----*/
			$viesScolairesCommuns = $xml->createElement('vies-scolaires-communs');
		$donnees->appendChild($viesScolairesCommuns);
		
			/*----- epis -----*/
			$epis = $xml->createElement('epis');
		$donnees->appendChild($epis);
		
			/*----- epis-groupes -----*/
			$episGroupes = $xml->createElement('epis-groupes');
		$donnees->appendChild($episGroupes);
		
			/*----- acc-persos -----*/
			$accPersos = $xml->createElement('acc-persos');
		$donnees->appendChild($accPersos);
		
			/*----- acc-persos-groupes -----*/
			$accPersosGroupes = $xml->createElement('acc-persos-groupes');
		$donnees->appendChild($accPersosGroupes);
		
			/*----- Bilans périodiques -----*/
			$bilansPeriodiques = $xml->createElement('bilans-periodiques');
		$donnees->appendChild($bilansPeriodiques);
		
	$items->appendChild($donnees);
echo $xml->saveXML();



