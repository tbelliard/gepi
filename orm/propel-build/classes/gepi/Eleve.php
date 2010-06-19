<?php



/**
 * Skeleton subclass for representing a row from the 'eleves' table.
 *
 * Liste des eleves de l'etablissement
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class Eleve extends BaseEleve {

	/**
	 *
	 * Renvoi sous forme d'un tableau la liste des classes d'un eleves.
	 * Manually added for N:M relationship
	 *
	 * @periode integer numero de la periode ou objet periodeNote
	 * @return     PropelObjectCollection Classes[]
	 *
	 */
    // ERREUR ?? Il ne peut y avoir qu'une seule classe pour un élève pour une période !!
	public function getClasses($periode) {
		require_once("helpers/PeriodeNoteHelper.php");
		$periode_num = PeriodeNoteHelper::getNumPeriode($periode);
		$classes = new PropelObjectCollection();
		$criteria = new Criteria();
		$criteria->add(JEleveClassePeer::PERIODE,$periode_num);
		foreach($this->getJEleveClassesJoinClasse($criteria) as $ref) {
		    if ($ref->getClasse() != NULL) {
			$classes->append($ref->getClasse());
		    }
		}
		return $classes;
	}

    // La méthode ci-dessous, au singulier, corrige le problème ci-dessus.
	public function getClasse($periode) {
		require_once("helpers/PeriodeNoteHelper.php");
		$periode_num = PeriodeNoteHelper::getNumPeriode($periode);
		$c = new Criteria();
		$c->add(JEleveClassePeer::PERIODE,$periode_num);
		$jec = $this->getJEleveClassesJoinClasse($c);
		if ($jec->isEmpty()) {
		    return null;
		} else {
		    return $jec->getFirst()->getClasse();
		}
	}

	/**
	 *
	 * Renvoi sous forme d'un tableau la liste des groupes d'un eleve pour une période donnée.
	 * Manually added for N:M relationship
	 *
	 * @periode integer numero de la periode
	 * @return     PropelObjectCollection Groupes[]
	 *
	 */
	public function getGroupes($periode = null) {
		if ($periode == null) {
		    $periode = $this->getPeriodeNoteOuverte();
		}
		require_once("helpers/PeriodeNoteHelper.php");
		$periode_num = PeriodeNoteHelper::getNumPeriode($periode);
		$groupes = new PropelObjectCollection();
		$c = new Criteria();
		if ($periode != null) {
		    $c->add(JEleveGroupePeer::PERIODE,$periode_num);
		}
		
		foreach($this->getJEleveGroupesJoinGroupe($c) as $ref) {
			if ($ref->getGroupe() != NULL) {
			    //ajout de l'eleve seulement si il n'y est pas deja
			    if (!$groupes->contains($ref->getGroupe())) {
				$groupes->append($ref->getGroupe());
			    }
			}
		}
		return $groupes;
	}

    public function getGroupesByCategories($periode) {
        // On commence par récupérer tous les groupes
        $groupes = $this->getGroupes($periode);
        // Ensuite, il nous faut les catégories. Pour ça, on passe par les classes.
        $classe = $this->getClasse($periode);
        $categories = array();
        $c = new Criteria();
        $c->add(JCategoriesMatieresClassesPeer::CLASSE_ID,$classe->getId());
        $c->addAscendingOrderByColumn(JCategoriesMatieresClassesPeer::PRIORITY);
        foreach(JCategoriesMatieresClassesPeer::doSelect($c) as $j) {
            $cat = $j->getCategorieMatiere();
            $categories[$cat->getId()] = array(0 => $cat, 1 => array());
        }
        // Maintenant, on mets tout ça ensemble
        foreach($groupes as $groupe) {
            $cat = $groupe->getCategorieMatiere($classe);
            $categories[$cat->getId()][1][] = $groupe;
        }
        // On renvoie un table multi-dimensionnel, qui contient les catégories
        // dans le bon ordre, et les groupes sous chaque catégorie.
        return $categories;
    }

    	/**
	 *
	 * Renvoi sous forme d'un tableau la liste des groupes d'un élève pour une période donnée
     * en limitant aux groupes pour lesquels une saisie ECTS est prévue.

	 * Manually added for N:M relationship
	 *
	 * @periode integer numero de la periode
	 * @return     PropelObjectCollection Groupes[]
	 *
	 */
	public function getEctsGroupes($periode) {
		$con = Propel::getConnection(ElevePeer::DATABASE_NAME, Propel::CONNECTION_READ);

        $sql = "SELECT groupes.* FROM groupes, j_eleves_classes jec, j_groupes_classes jgc, j_eleves_groupes jeg
                    WHERE (groupes.id = jgc.id_groupe AND jgc.id_groupe = jeg.id_groupe AND jgc.id_classe = jec.id_classe AND jgc.saisie_ects = TRUE AND jec.login = jeg.login AND jec.periode = jeg.periode AND jeg.periode = '".$periode."' AND jeg.login = '".$this->getLogin()."') ORDER BY jgc.priorite";
        $stmt = $con->prepare($sql);
        $stmt->execute();
        $groupes = GroupePeer::populateObjects($stmt);
		return $groupes;
	}

    public function getEctsGroupesByCategories($periode) {
        // On commence par récupérer tous les groupes
        $groupes = $this->getGroupes($periode);
        // Ensuite, il nous faut les catégories. Pour ça, on passe par les classes.
        $classe = $this->getClasse($periode);
        $categories = array();
        $c = new Criteria();
        $c->add(JCategoriesMatieresClassesPeer::CLASSE_ID,$classe->getId());
        $c->addAscendingOrderByColumn(JCategoriesMatieresClassesPeer::PRIORITY);
        foreach(JCategoriesMatieresClassesPeer::doSelect($c) as $j) {
            $cat = $j->getCategorieMatiere();
            $categories[$cat->getId()] = array(0 => $cat, 1 => array());
        }
        // Maintenant, on mets tout ça ensemble
        foreach($groupes as $groupe) {
            if ($groupe->allowsEctsCredits($classe->getId())) {
                $cat = $groupe->getCategorieMatiere($classe->getId());
                $categories[$cat->getId()][1][$groupe->getId()] = $groupe;
            }
        }

        foreach($categories as $cat) {
            if (count($cat[1]) == 0) {
                $id = $cat[0]->getId();
                unset($categories[$id]);
            }
        }

        // On renvoie un table multi-dimensionnel, qui contient les catégories
        // dans le bon ordre, et les groupes sous chaque catégorie.
        return $categories;
    }


	/**
	 * Manually added for N:M relationship
	 *
	 * @periode integer numero de la periode
	 * @return     EctsCredit
	 *
	 */
	public function getEctsCredit($periode,$id_groupe) {
		$criteria = new Criteria();
		$criteria->add(CreditEctsPeer::NUM_PERIODE,$periode);
		$criteria->add(CreditEctsPeer::ID_GROUPE,$id_groupe);
		$v = $this->getCreditEctss($criteria);
		if ($v->isEmpty()) {
		    return null;
		} else {
		    return $v->getFirst();
		}
	}

	public function deleteEctsCredit($periode,$id_groupe) {
        $credit = $this->getEctsCredit($periode, $id_groupe);
        return $credit == null ? true : $credit->delete();
	}

  // On remet à zéro le crédit ECTS concerné
  // Cela signifie simplement que l'on efface la valeur et la mention 'officielle'
  // pour ainsi ne conserver que l'éventuelle pré-saisie du prof
	public function resetEctsCredit($periode,$id_groupe) {
        $credit = $this->getEctsCredit($periode, $id_groupe);
        if ($credit) {
          $credit->setValeur(null);
          $credit->setMention(null);
        }
        return $credit == null ? true : $credit->save();
	}

	public function getArchivedEctsCredits($annee,$periode) {
		$criteria = new Criteria();
		$criteria->add(ArchiveEctsPeer::NUM_PERIODE,$periode);
        $criteria->add(ArchiveEctsPeer::ANNEE,$annee);
        $v = $this->getArchiveEctss($criteria);
        $result = array();
        if (!empty($v)) {
            foreach($v as $credit){
                $result[$credit->getId()] = $credit;
            }
        }
        return $result;
	}

	public function getArchivedEctsCredit($annee,$periode,$matiere) {
		$criteria = new Criteria();
		$criteria->add(ArchiveEctsPeer::NUM_PERIODE,$periode);
        $criteria->add(ArchiveEctsPeer::ANNEE,$annee);
        $criteria->add(ArchiveEctsPeer::MATIERE,$matiere);
        $v = $this->getArchiveEctss($criteria);
		if ($v->isEmpty()) {
		    return null;
		} else {
		    return $v->getFirst();
		}
	}

	public function getCreditEctsGlobal() {
        $v = $this->getCreditEctsGlobals();
		if ($v->isEmpty()) {
		    return null;
		} else {
		    return $v->getFirst();
		}
	}

    public function getEctsAnneesPrecedentes() {
        $c = new Criteria();
        $c->addAscendingOrderByColumn(ArchiveEctsPeer::ANNEE);
        $c->addAscendingOrderByColumn(ArchiveEctsPeer::NUM_PERIODE);
        $archives = $this->getArchiveEctss($c);
        $annees = array();
        foreach ($archives as $a) {
            if (array_key_exists($a->getAnnee(), $annees)) {
                // Le tableau avec l'année existe déjà.
                // On regarde si c'est le cas pour la période.
                if (!array_key_exists($a->getNumPeriode(), $annees[$a->getAnnee()]['periodes'])) {
                    $annees[$a->getAnnee()]['periodes'][$a->getNumPeriode()] = $a->getNomPeriode();
                }
            } else {
                $annees[$a->getAnnee()] = array('annee' => $a->getAnnee(), 'periodes' => array($a->getNumPeriode() => $a->getNomPeriode()));
            }
        }
        return $annees;
    }
    /**
	 * Enregistre les crédits ECTS pour une période et un groupe
	 */
	public function setEctsCredit($periode,$id_groupe,$valeur_ects,$mention_ects,$mention_prof = null) {
        $credit = $this->getEctsCredit($periode,$id_groupe);
        if ($credit == null) {
            $credit = new CreditEcts();
            $credit->setEleve($this);
            $credit->setIdGroupe($id_groupe);
            $credit->setNumPeriode($periode);
        }
        // Si on enregistre une pré-saisie, alors on n'enregistre que ça, sans toucher au reste.
        if ($mention_prof) {
          $credit->setMentionProf($mention_prof);
        } else {
          $credit->setValeur($valeur_ects);
          $credit->setMention($mention_ects);
        }
        return $credit->save();
	}

	public function setCreditEctsGlobal($mention_ects) {
        $credit = $this->getCreditEctsGlobal();
        if ($credit == null) {
            $credit = new CreditEctsGlobal();
            $credit->setEleve($this);
        }
        $credit->setMention($mention_ects);
        return $credit->save();
	}

	/**
	 *
	 * Retourne l'emplacement de cours pour la periode de note donnée.
	 *
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return EdtEmplacementCours l'emplacement de cours actuel ou null si pas de cours actuellement
	 */
	public function getEdtEmplacementCours($v){

	    $edtCoursCol = $this->getEdtEmplacementCourssPeriodeCalendrierActuelle($v);

	    require_once("helpers/EdtEmplacementCoursHelper.php");
	    return EdtEmplacementCoursHelper::getEdtEmplacementCoursActuel($edtCoursCol, $v);
	}

	/**
	 *
	 * Retourne tous les emplacements de cours pour la periode précisée du calendrier.
	 * On recupere aussi les emplacements dont la periode n'est pas definie ou vaut 0.
	 *
	 * @return PropelObjectCollection EdtEmplacementCours une collection d'emplacement de cours ordonnée chronologiquement
	 */
	public function getEdtEmplacementCourssPeriodeCalendrierActuelle($v = 'now'){
	    $groupe = new Groupe();
	    $colGroupeId = $this->getGroupes($this->getPeriodeNoteOuverte($v))->getPrimaryKeys();

	    $query = EdtEmplacementCoursQuery::create()->filterByIdGroupe($colGroupeId)
		    ->filterByIdCalendrier(0)
		    ->addOr(EdtEmplacementCoursPeer::ID_CALENDRIER, NULL);

	    if ($v instanceof EdtCalendrierPeriode) {
		$query->addOr(EdtEmplacementCoursPeer::ID_CALENDRIER, $v->getIdCalendrier());
	    } else {
		$periodeCalendrier = EdtCalendrierPeriodePeer::retrieveEdtCalendrierPeriodeActuelle($v);
		if ($periodeCalendrier != null) {
		       $query->addOr(EdtEmplacementCoursPeer::ID_CALENDRIER, $periodeCalendrier->getIdCalendrier());
		}
	    }

	    $edtCoursCol = $query->find();
	    require_once("helpers/EdtEmplacementCoursHelper.php");
	    EdtEmplacementCoursHelper::orderChronologically($edtCoursCol);

	    return $edtCoursCol;
	}

  	/**
	 *
	 * Retourne une periode de note pour laquelle l'eleve est affectée à une classe dont la periode est ouverte
	 *
	 * @return PeriodeNote objet periode ou null si pas de periode ouverte
	 */
	public function getPeriodeNoteOuverte($v = 'now') {
		foreach ($this->getJEleveClassesJoinClasse() as $jclasse) {
		    $periode = $jclasse->getClasse()->getPeriodeNoteOuverte($v);
		    if ($periode != null && $periode->getNumPeriode() == $jclasse->getPeriode()) {
			//on a une periode ouverte et l'eleve est inscrit dans cete classe pour cette periode, on va considerer que c'est la periode actuelle
			return $periode;
		    }
		}
		return null;
	}

  	/**
	 *
	 * Retourne une liste d'absence du jour
	 *
	 * @return PropelCollection AbsenceEleveSaisie[]
	 */
	public function getAbsenceSaisiesDuJour($v = 'now') {
	    // we treat '' as NULL for temporal objects because DateTime('') == DateTime('now')
	    // -- which is unexpected, to say the least.
	    //$dt = new DateTime();
	    if ($v === null || $v === '') {
		    $dt = null;
	    } elseif ($v instanceof DateTime) {
		    $dt = $v;
	    } else {
		    // some string/numeric value passed; we normalize that so that we can
		    // validate it.
		    try {
			    if (is_numeric($v)) { // if it's a unix timestamp
				    $dt = new DateTime('@'.$v, new DateTimeZone('UTC'));
				    // We have to explicitly specify and then change the time zone because of a
				    // DateTime bug: http://bugs.php.net/bug.php?id=43003
				    $dt->setTimeZone(new DateTimeZone(date_default_timezone_get()));
			    } else {
				    $dt = new DateTime($v);
			    }
		    } catch (Exception $x) {
			    throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
		    }
	    }
	    $dt->setTime(0,0,0);
	    $criteria = new Criteria();
	    $criteria->add(AbsenceEleveSaisiePeer::FIN_ABS, $dt, Criteria::GREATER_EQUAL);
	    $dt_fin = clone $dt;
	    $dt_fin->setTime(23,59,59);
	    $criteria->add(AbsenceEleveSaisiePeer::DEBUT_ABS, $dt_fin, Criteria::LESS_EQUAL);
	    $col =  $this->getAbsenceEleveSaisies($criteria);
	    return $col;
	}

  	/**
	 *
	 * Retourne une liste d'absence pour le creneau et le jour donné.
	 *
	 * @return PropelColection AbsenceEleveSaisie[]
	 */
	public function getAbsenceSaisiesDuCreneau($edtcreneau = null, $v = 'now') {
	    if ($edtcreneau == null) {
		$edtcreneau = EdtCreneauPeer::retrieveEdtCreneauActuel($v);
	    }
	    
	    if (!($edtcreneau instanceof EdtCreneau)) {
		throw new PropelException('Le premier argument doit etre de la classe EdtCreneau');
	    }
	    
	    // we treat '' as NULL for temporal objects because DateTime('') == DateTime('now')
	    // -- which is unexpected, to say the least.
	    //$dt = new DateTime();
	    if ($v === null || $v === '') {
		    $dt = null;
	    } elseif ($v instanceof DateTime) {
		    $dt = $v;
	    } else {
		    // some string/numeric value passed; we normalize that so that we can
		    // validate it.
		    try {
			    if (is_numeric($v)) { // if it's a unix timestamp
				    $dt = new DateTime('@'.$v, new DateTimeZone('UTC'));
				    // We have to explicitly specify and then change the time zone because of a
				    // DateTime bug: http://bugs.php.net/bug.php?id=43003
				    $dt->setTimeZone(new DateTimeZone(date_default_timezone_get()));
			    } else {
				    $dt = new DateTime($v);
			    }
		    } catch (Exception $x) {
			    throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
		    }
	    }


	    $query = AbsenceEleveSaisieQuery::create();
	    $query->filterByEleveId($this->getIdEleve());

	    $dt->setTime($edtcreneau->getHeuredebutDefiniePeriode('H'), $edtcreneau->getHeuredebutDefiniePeriode('i'), 0);
	    $query->filterByFinAbs($dt, Criteria::GREATER_THAN);
	    
	    $dt_fin_creneau = clone $dt;
	    $dt_fin_creneau->setTime($edtcreneau->getHeurefinDefiniePeriode('H'), $edtcreneau->getHeurefinDefiniePeriode('i'), 0);
	    $query->filterByDebutAbs($dt_fin_creneau, Criteria::LESS_THAN);

//	    $query->leftJoin('AbsenceEleveSaisie.EdtEmplacementCours');
//	    $query->condition('cond1', 'AbsenceEleveSaisie.IdEdtCreneau IS NULL');
//	    $query->condition('cond2', 'AbsenceEleveSaisie.IdEdtCreneau = ?', $edtcreneau->getIdDefiniePeriode());
//	    $query->where(array('cond1', 'cond2'), 'or');

//	    $result = new PropelObjectCollection();
//	    foreach ($query->find() as $saisie) {
//		if ($saisie->getEdtEmplacementCours() == null ||
//		    ($saisie->getEdtEmplacementCours()->getHeureDebut() < $edtcreneau->getHeurefinDefiniePeriode() &&
//		    $saisie->getEdtEmplacementCours()->getHeureFin() > $edtcreneau->getHeuredebutDefiniePeriode()) ) {
//		    $result->append($saisie);
//		    }
//	    }
	    return $query->find();
	}

	/*
	Renvoie le nom de la photo de l'élève
	Renvoie une chaine vide si :
	- le module trombinoscope n'est pas activé
	- ou bien la photo n'existe pas.

	$_elenoet_ou_loginc : selon les cas, soir l'elenoet de l'élève ou bien lelogin du professeur
	$repertoire : "eleves"
	$arbo : niveau d'aborescence (1 ou 2).
	*/
	public function getNomPhoto($arbo=1) {
		if ($arbo==2) {$chemin = "../";} else {$chemin = "";}
		$repertoire = "eleves";
		if (getSettingValue("active_module_trombinoscopes")!='y') {
			return "";
			die();
		}
		// Cas des élèves
		// En multisite, le login est préférable à l'ELENOET
		if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
		    $_elenoet_ou_login = $this->getElenoet();
		} else {
		    $_elenoet_ou_login = $this->getLogin();
		}

		$photo= null;
		if($_elenoet_ou_login!='') {
			if(file_exists($chemin."../photos/eleves/".$this->getLogin().".jpg")) {
				$photo="$_elenoet_ou_login.jpg";
			}
			else {
				if(file_exists($chemin."../photos/eleves/".sprintf("%05d",$_elenoet_ou_login).".jpg")) {
					$photo=sprintf("%05d",$this->getLogin()).".jpg";
				} else {
					for($i=0;$i<5;$i++){
						if(substr($this->getLogin(),$i,1)=="0"){
							$test_photo=substr($this->getLogin(),$i+1);
							//if(file_exists($chemin."../photos/eleves/".$test_photo.".jpg")){
							if(($test_photo!='')&&(file_exists($chemin."../photos/eleves/".$test_photo.".jpg"))) {
								$photo=$test_photo.".jpg";
								break;
							}
						}
					}
				}
			}
		}
		
		return $photo;
	}

	/**
	 * renvoi la civilite 
	 * M. ou Mlle
	 * @return     string
	 */
	public function getCivilite()
	{
		if($this->getSexe()=="M") {
			return "M.";
		} elseif ($this->getSexe()=="F") {
			return "Mlle";
		}
		return "";
	}

  	/**
	 *
	 * Retourne une liste d'absence pour la période donnée
	 *
	 * @param      mixed $periode numeric or PeriodeNote value.
	 *
	 * @return PropelObjectCollection AbsenceEleveSaisie[]
	 */
	public function getAbsenceSaisiesPeriode($periode = null) {
	    $periode_obj = new PeriodeNote();
	    if ($periode === null) {
		$periode = $this->getPeriodeNoteOuverte();
	    }
	    if (is_numeric($periode)) {
		    $periode_obj = PeriodeNoteQuery::create()->filterByClasse($this->getClasse($periode))->filterByNumPeriode($periode);
	    } else if (! $periode instanceof PeriodeNote) {
		    throw new PropelException('Argument $periode doit etre de type numerique ou une instance de PeriodeNote.');
	    } else {
		    $periode_obj = $periode;
	    }

	    if ($periode_obj == null) {
		return null;
	    }
	    $date_debut = $periode_obj->getDateDebut(null);
	    if ($date_debut  == null)  {
		return null;
	    }
	    

	    $query =  AbsenceEleveSaisieQuery::create();
	    $query->filterByEleve($this);
	    $query->filterByFinAbs($date_debut, Criteria::GREATER_EQUAL);
	    $date_fin = $periode_obj->getDateFin(null);
	    if ($date_fin != null) {
		$query->filterByDebutAbs($date_fin, Criteria::LESS_EQUAL);
	    }

	    return $query->find();
	    
	}

	
  	/**
	 *
	 * Retourne le nombre de 1/2 journées d'absence
	 *
	 * @param      mixed $periode numeric or PeriodeNote value.
	 *
	 * @return int $nombre_absence
	 */
	public function getNbreDemiJourneeAbsence($date_debut, $date_fin = null) {
	    $query =  AbsenceEleveSaisieQuery::create();
	    $query->filterByEleve($this);
	    $query->filterByFinAbs($date_debut, Criteria::GREATER_EQUAL);
	    if ($date_fin != null) {
		$query->filterByDebutAbs($date_fin, Criteria::LESS_EQUAL);
	    }
	    $query->orderByDebutAbs(Criteria::ASC);

	    $abs_saisie_col = $query->find();
	    //echo $abs_saisie_col->count();

	    if ($abs_saisie_col->isEmpty()) {
		return 0;
	    }
	    
	    $date_compteur = clone $date_debut;
	    $date_compteur->setTime(0,0);
	    if ($date_fin != null) {
		$date_fin_iteration = clone $date_fin;
	    } else {
		$date_fin_iteration = new DateTime('now');
	    }

	    $abs_saisie_col->getFirst();
	    $semaine_declaration = array("dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi");
	    $horaire_col = EdtHorairesEtablissementQuery::create()->find();
	    $horaire_tab = $horaire_col->getArrayCopy('JourHoraireEtablissement');
	    $total = 0;
	    
	    foreach($abs_saisie_col as $saisie) {
		if ($date_compteur > $date_fin_iteration) {
		    break;
		}
		if ($saisie->getRetard() || $saisie->getResponsabiliteEtablissement()) {
		    continue;
		}
		if ($date_compteur < $saisie->getDebutAbs(null)) {
		    $date_compteur = clone $saisie->getDebutAbs(null);
		}
		if ($date_compteur->format('H') < 12) {
		    $date_compteur->setTime(0, 0);
		} else {
		    $date_compteur->setTime(12, 30);//on calle la demi journée a 12h30
		}
		$max = 0;
		while ($date_compteur < $saisie->getFinAbs(null) && $date_compteur < $date_fin_iteration && $max < 200) {
		    //est-ce un jour de la semaine ouvert ?
		    $jour_semaine = $semaine_declaration[$date_compteur->format("w")];
		    $horaire = null;
		    if (isset($horaire_tab[$jour_semaine])) {
			$horaire = $horaire_tab[$jour_semaine];
		    }
		    if ($horaire == null || $date_compteur->format('Hi') >= $horaire->getFermetureHoraireEtablissement('Hi')) {
			//fermé
			$date_compteur = new DateTime('@'.($date_compteur->format('U') + 43200));//86400 correspond a 24 heures
			$date_compteur->setTimeZone($date_debut->getTimeZone());
			continue;
		    }

		    //ouvert
		    $date_compteur_suivante = new DateTime('@'.($date_compteur->format('U') + 43200));//86400 correspond a 24 heures
		    $date_compteur_suivante->setTimeZone($date_compteur->getTimeZone());
		    if ($date_compteur_suivante->format('H') < 12) {
			$date_compteur_suivante->setTime(0, 0);
		    } else {
			$date_compteur_suivante->setTime(12, 30);
		    }
		    if ($saisie->getDebutAbs('U') < $date_compteur_suivante->format('U') && $saisie->getFinAbs('U') > $date_compteur->format('U')) {
			$total = $total + 1;
		    }
		    $date_compteur = $date_compteur_suivante;
		}
	    }
	    return $total;

	}

  	/**
	 *
	 * Retourne la liste de période de notes
	 *
	 *
	 * @return PropelObjectCollection PeriodeNote[]
	 */
	public function getPeriodesNotes() {
	    $periodeNotes = new PropelObjectCollection();
	    $periodeNotes = PeriodeNoteQuery::create()->useClasseQuery()->useJEleveClasseQuery()->filterByEleve($this)->endUse()->endUse()->distinct()->find();
	    $periodeNotes->uasort(array("PeriodeNote", "comparePeriodeNote"));
	    return $periodeNotes;
	}
} // Eleve
