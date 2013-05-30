<?php



/**
 * Skeleton subclass for performing query and update operations on the 'a_agregation_decompte' table.
 *
 * Table d'agregation des decomptes de demi journees d'absence et de retard
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class AbsenceAgregationDecomptePeer extends BaseAbsenceAgregationDecomptePeer {
	/**
	 *
	 * Vérifie que l'ensemble de la table d'agrégation est à jours, pour tous les élèves.
	 * Corrige automatiquement la table pour un certain nombres d'élèves (précisé par $reparation_nbr),
	 * si plus d'élève que ce nombre sont en échec on renvoie faux
	 *
	 * @param      DateTime $dateDebut date de début pour la prise en compte du test
	 * @param      DateTime $dateFin date de fin pour la prise en compte du test
	 * @param      int $reparation_nbr nomble d'elve qu'on va mettre à jour avant de renvoyer faux
	 * @return		Boolean
	 *
	 */
	public static function checkSynchroAbsenceAgregationTable(DateTime $dateDebut = null, DateTime $dateFin = null, $reparation_nbr = 50) {
		$debug = false;
		if ($debug) {
			print_r('AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable() called<br/>');
		}
		
		//on initialise les date clone qui seront manipulés dans l'algoritme, c'est nécessaire pour ne pas modifier les dates passées en paramêtre.
		$dateDebutClone = null;
		$dateFinClone = null;
		
		if ($dateDebut != null) {
			if ($debug) {
				print_r('Date début '.$dateDebut->format('Y-m-d').'<br/>');
			}
			$dateDebutClone = clone $dateDebut;
			$dateDebutClone->setTime(0,0);
		}
		if ($dateFin != null) {
			if ($debug) {
				print_r('Date fin '.$dateFin->format('Y-m-d').'<br/>');
			}
			$dateFinClone = clone $dateFin;
			$dateFinClone->setTime(23,59);
		}
		
		//on va vérifier que tout les marqueurs de fin des calculs de mise à jour sont bien présents pour tout les élèves
		$query = '
			SELECT distinct eleves.ID_ELEVE
			FROM `eleves` 
			LEFT JOIN (
				SELECT distinct ELEVE_ID
				FROM `a_agregation_decompte`
				WHERE date_demi_jounee = \'0001-01-01 00:00:00\') as a_agregation_decompte_selection
			ON (eleves.ID_ELEVE=a_agregation_decompte_selection.ELEVE_ID)
			WHERE a_agregation_decompte_selection.ELEVE_ID IS NULL';
		$result = mysql_query($query);
		$num_rows = mysql_num_rows($result);
		if ($num_rows>0 && $num_rows < $reparation_nbr) {
			if ($debug) {
				print_r('Il manque des marqueurs de fin de calcul<br/>');
			}
			//on va corriger la table pour ces élèves là
			while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
				$eleve = EleveQuery::create()->findOneById($row[0]);
				if ($debug) {
					print_r('recalcul pour l eleve '.$eleve->getId().'<br/>');
				}
				$eleve->checkAndUpdateSynchroAbsenceAgregationTable($dateDebutClone,$dateFinClone);
			}
			//après avoir corrigé on relance le test
			return(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable($dateDebutClone, $dateFinClone));
		} elseif ($num_rows>0) {
			if ($debug) {
				print_r('retourne faux : Il manque trop de marqueurs de fin de calcul<br/>');
			}
			return false;
		}
		
		//conditions sql sur les dates
		$date_saisies_selection = ' 1=1 ';
		$date_saisies_version_selection = ' 1=1 ';
		$date_agregation_selection = ' 1=1 ';
		if ($dateDebutClone != null) {
			$date_saisies_selection .= ' and a_saisies.fin_abs >= "'.$dateDebutClone->format('Y-m-d H:i:s').'" ';
			$date_saisies_version_selection .= ' and a_saisies_version.fin_abs >= "'.$dateDebutClone->format('Y-m-d H:i:s').'" ';
			$date_agregation_selection .= ' and a_agregation_decompte.DATE_DEMI_JOUNEE >= "'.$dateDebutClone->format('Y-m-d H:i:s').'" ';
		}
		if ($dateFinClone != null) {
			$date_saisies_selection .= ' and a_saisies.debut_abs <= "'.$dateFinClone->format('Y-m-d H:i:s').'" ';
			$date_saisies_version_selection .= ' and a_saisies_version.debut_abs <= "'.$dateFinClone->format('Y-m-d H:i:s').'" ';
			$date_agregation_selection .= ' and a_agregation_decompte.DATE_DEMI_JOUNEE <= "'.$dateFinClone->format('Y-m-d H:i:s').'" ';
		}
				
		//on va vérifier que tout les élèves ont bien le bon nombres entrées dans la table d'agrégation pour cette période
		if ($dateFinClone != null && $dateDebutClone != null) {
    		$query = '
    			SELECT eleves.ID_ELEVE, count(eleves.ID_ELEVE) as count_entrees
    			FROM `eleves` 
    			LEFT JOIN (
    				SELECT ELEVE_ID
    				FROM `a_agregation_decompte`
    				WHERE '.$date_agregation_selection.') as a_agregation_decompte_selection
    			ON (eleves.ID_ELEVE=a_agregation_decompte_selection.ELEVE_ID)
    			group by eleves.ID_ELEVE';
    		$result = mysql_query($query);
    		$wrong_eleve = array();
    		$nbre_demi_journees=(int)(($dateFinClone->format('U')+3700-$dateDebutClone->format('U'))/(3600*12));
    		while($row = mysql_fetch_array($result)){
    			if ($row[1]!=$nbre_demi_journees) {
    				if ($debug) {
    					print_r('Il manque des entrees pour l eleve '.$row[0].'<br/>');
    				}
    				$wrong_eleve[]=$row[0];
    			}
    		}
    		if (count($wrong_eleve) > 0 && count($wrong_eleve) < $reparation_nbr) {
    			//on va corriger la table pour ces élèves là
    			foreach($wrong_eleve as $idEleve) {
    				$eleve = EleveQuery::create()->findOneById($idEleve);
    				if ($debug) {
    					print_r('recalcul pour l eleve '.$eleve->getId().'<br/>');
    				}
    				$eleve->checkAndUpdateSynchroAbsenceAgregationTable($dateDebutClone,$dateFinClone);
    			}
    			//après avoir corrigé on relance le test
    			return(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable($dateDebutClone, $dateFinClone));
    		} elseif (!empty($wrong_eleve) > 0) {
    			if ($debug) {
    				print_r('retourne faux : Il manque des saisies sur '.count($wrong_eleve).' eleves<br/>');
    			}
    			return false;
    		}
		}
		
		
		// est-ce que la date updated_at de mise à jour de la table est bien postérieure aux date de modification des saisies et autres entrées
		$query = 'select union_date, updated_at, now() as now
		
		FROM
			(SELECT max(updated_at) as updated_at
			FROM a_agregation_decompte WHERE '.$date_agregation_selection.'	
			) as updated_at_select

		LEFT JOIN (
			(SELECT union_date from 
				(	SELECT GREATEST(IFNULL(max(updated_at),CAST(0 as DATETIME)),IFNULL(max(deleted_at),CAST(0 as DATETIME))) as union_date FROM a_saisies WHERE eleve_id is not null and '.$date_saisies_selection.'
				UNION ALL
					SELECT GREATEST(IFNULL(max(a_saisies_version.updated_at),CAST(0 as DATETIME)),IFNULL(max(a_saisies_version.deleted_at),CAST(0 as DATETIME))) as union_date FROM a_saisies_version WHERE a_saisies_version.eleve_id is not null and '.$date_saisies_version_selection.'
				UNION ALL
					SELECT GREATEST(IFNULL(max(a_traitements.updated_at),CAST(0 as DATETIME)),IFNULL(max(a_traitements.deleted_at),CAST(0 as DATETIME))) as union_date FROM a_traitements join j_traitements_saisies on a_traitements.id = j_traitements_saisies.a_traitement_id join a_saisies on a_saisies.id = j_traitements_saisies.a_saisie_id WHERE a_saisies.eleve_id is not null and '.$date_saisies_selection.'
				
				ORDER BY union_date DESC LIMIT 1
				) AS union_date_union_all_select
			) AS union_date_select
		) ON 1=1;';
			
		$result_query = mysql_query($query);
		if ($result_query === false) {
			if ($debug) {
				echo $query;
			}
			echo 'Erreur sur la requete : '.mysql_error().'<br/>';
			return false;
		}
		$row = mysql_fetch_array($result_query);
		mysql_free_result($result_query);
		if ($debug) {
		    print_r($row);
		}
		if ($row['updated_at'] && $row['updated_at']  > $row['now']) {
			if ($debug) {
				print_r('faux : Date de mise a jour des agregation ne peut pas etre dans le futur<br/>');
			}
			return false;
		} else if ($row['union_date'] && $row['union_date']  > $row['now']) {
			if ($debug) {
				print_r('faux : Date de mise a jour des saisie ou traitements ne peut pas etre dans le futur<br/>');
			}
			return false;
		} else if ($row['union_date'] && (!$row['updated_at'] || $row['union_date'] > $row['updated_at'])){//si on a pas de updated_at dans la table d'agrégation, ou si la date de mise à jour des saisies est postérieure à updated_at ou 
			if ($debug) {
				print_r('retourne faux : Les date de mise a jour de la table sont trop anciennes<br/>');
			}
			return false;
		} else {
			if ($debug) {
				print_r('retourne vrai<br/>');
			}
			return true;//on ne vérifie pas le nombre d'entrée car les dates ne sont pas précisée
		}
	}
	
	/**
	 *
	 * Purge l'ensemble des décomptes pour les saisies précisées et met la table à jour
	 * 
	 * @param      ArrayObject $saisie_col
	 *
	 */
	public static function updateAgregationTable(ArrayObject $saisie_col) {
		$eleveEtDate = Array();
		foreach($saisie_col as $saisie) {
		    $saisie->clearAllReferences();
			if (!isset($eleveEtDate[$saisie->getEleveId()])) {
				$eleveArray = Array('dateDebut' => null,'dateFin' => null, 'eleve' => $saisie->getEleve());
			} else {
				$eleveArray = $eleveEtDate[$saisie->getEleveId()];
			}
			if ($eleveArray['dateDebut'] == null || $saisie->getDebutAbs(null) < $eleveArray['dateDebut']) {
			    $eleveArray['dateDebut'] = clone $saisie->getDebutAbs(null);
			}
			if ($eleveArray['dateFin'] == null || $saisie->getFinAbs(null) > $eleveArray['dateFin']) {
			    $eleveArray['dateFin'] = clone $saisie->getFinAbs(null);
			}
			$eleveEtDate[$saisie->getEleveId()] = $eleveArray;
		}
		foreach ($eleveEtDate as $id => $array_eleve) {
			if ($array_eleve['eleve'] != null) {
			    $array_eleve['eleve']->clearAbsenceEleveSaisiesParJour();
				$array_eleve['eleve']->updateAbsenceAgregationTable($array_eleve['dateDebut'],$array_eleve['dateFin']);
			}
		}
	}
	
} // AbsenceAgregationDecomptePeer
