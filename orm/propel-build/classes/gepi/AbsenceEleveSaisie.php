<?php



/**
 * Skeleton subclass for representing a row from the 'a_saisies' table.
 *
 * Chaque saisie d'absence doit faire l'objet d'une ligne dans la table a_saisies. Une saisie peut etre : une plage horaire longue durée (plusieurs jours), défini avec les champs debut_abs et fin_abs. Un creneau horaire, le jour etant precisé dans debut_abs. Un cours de l'emploi du temps, le jours du cours etant precisé dans debut_abs.
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class AbsenceEleveSaisie extends BaseAbsenceEleveSaisie {

	/**
	 * @var        bool to store aggregation of sousResponsabiliteEtablissement value
	 */
	protected $sousResponsabiliteEtablissement;
    
	/**
	 * @var        bool to store aggregation of manquementObligationPresence value
	 */
	protected $manquementObligationPresence;

	/**
	 * @var        bool to store the manquementObligationPresenceSpecifie_NON_PRECISE state
	 */
	protected $manquementObligationPresenceSpecifie_NON_PRECISE;

	/**
	 * @var        bool to store the justifie state
	 */
	protected $justifiee;

	/**
	 * @var        bool to store aggregation of retard value
	 */
	protected $retard;

	/**
	 * @var        collection to store aggregation of saisie contradictoires
	 */
	protected $collectionSaisiesContradictoiresManquementObligation;

	/**
	 * @var        boolean to store if saisie is contradictoire
	 */
	protected $boolSaisiesContradictoiresManquementObligation;

	/**
	 *
	 * Renvoi une description intelligible du traitement
	 *
	 * @return     String description
	 *
	 */
	public function getDescription() {
	    $desc = '';
	    if ($this->getEleve() != null) {
		$desc .= $this->getEleve()->getCivilite().' '.$this->getEleve()->getNom().' '.$this->getEleve()->getPrenom();
	    }
	    $desc .= ' '.$this->getDateDescription();
	    $desc .= ' ';
	    if ($this->getClasse() != null) {
		$desc .= "; classe : ".$this->getClasse()->getNomComplet();
	    }
	    if ($this->getGroupe() != null) {
		$desc .= "; groupe : ".$this->getGroupe()->getName();
	    }
	    if ($this->getAidDetails() != null) {
		$desc .= "; aid : ".$this->getAidDetails()->getNom();
	    }
//	    if ($this->getNotifiee() != null) { //desactive pour ameliorer les performances
//		$desc .= "; notifiée";
//	    }
	    if ($this->getCommentaire() != null && $this->getCommentaire() != '') {
		$desc .= "; ".$this->getCommentaire();
	    }
	    return $desc;
	}

	/**
	 *
	 * Renvoi une liste intelligible des types associes
	 *
	 * @return     String description
	 *
	 */
	public function getTypesDescription() {
	    $traitement_col = $this->getAbsenceEleveTraitements();
	    $result = '';
	    $besoin_echo_type = true;
	    $besoin_echo_virgule = false;
	    foreach ($traitement_col as $bou_traitement) {
		if ($bou_traitement->getAbsenceEleveType() != null) {
		    if ($besoin_echo_type) {
			$result .= 'type : ';
			$besoin_echo_type = false;
		    }
		    if ($besoin_echo_virgule) {
			$result .=  ', ';
			$besoin_echo_virgule = false;
		    }
		    $result .= $bou_traitement->getAbsenceEleveType()->getNom();
		    $besoin_echo_virgule = true;
		}
	    }
	    return $result;
	}

    /**
	 *
	 * Renvoi une liste des types associes ou non traitée sinon
	 *
	 * @return     String description
	 *
	 */
	public function getTypesTraitements() {
        $traitement_col = $this->getAbsenceEleveTraitements();
        $result = '';
        $besoin_echo_virgule = false;
        foreach ($traitement_col as $bou_traitement) {
            if ($bou_traitement->getAbsenceEleveType() != null) {
                if ($besoin_echo_virgule) {
                    $result .= ', ';
                    $besoin_echo_virgule = false;
                }
                $result .= $bou_traitement->getAbsenceEleveType()->getNom();
                $besoin_echo_virgule = true;
            }
        }
        if ($result == '') $result = 'Non traitée';
        return $result;
    }
	/**
	 *
	 * Renvoi true ou false en fonction des types associé
	 *
	 * @return     boolean
	 *
	 */
	public function hasTypeSaisieDiscipline() {
	    $traitements = $this->getAbsenceEleveTraitements();
	    foreach ($traitements as $traitement) {
		if ($traitement->getAbsenceEleveType() != null &&
		    $traitement->getAbsenceEleveType()->getTypeSaisie() == 'DISCIPLINE') {
		    return true;
		}
	    }
	    return false;
	}
    /**
	 *
	 * Renvoi true ou false si un type est defini ou non
	 *
	 * @return     boolean
	 *
	 */
	public function hasTypeSaisie() {
	    $traitements = $this->getAbsenceEleveTraitements();
	    foreach ($traitements as $traitement) {
		if ($traitement->getAbsenceEleveType() != null ) {
		    return true;
		}
	    }
	    return false;
	}
    /**
	 *
	 * Renvoi true ou false si le lieu est rattaché a cette saisie ou non
	 * @param      $id_lieu id du lieu à tester
	 * @return     boolean
	 *
	 */
    public function hasLieuSaisie($id_lieu) {

        if (!$this->getTraitee() && $id_lieu == null) {
            return true;
        }
        $traitements = $this->getAbsenceEleveTraitements();
        foreach ($traitements as $traitement) {
            if ($traitement->getAbsenceEleveType() == null && $id_lieu == null) {
                return true;
            }
            if ($traitement->getAbsenceEleveType() != null && $traitement->getAbsenceEleveType()->getIdLieu() == $id_lieu) {
                return true;
            }
        }
        return false;
    }
    /**
	 *
	 * Renvoi true ou false si le type  est du type erreur de saisie
	 *
	 * @return     boolean
	 *
	 */
    public function hasTypeLikeErreurSaisie() {

        $traitements = $this->getAbsenceEleveTraitements();
        foreach ($traitements as $traitement) {
            if ($traitement->getAbsenceEleveType() != null && $traitement->getAbsenceEleveType()->getSousResponsabiliteEtablissement() == AbsenceEleveType::$SOUS_RESP_ETAB_NON_PRECISE
                    && $traitement->getAbsenceEleveType()->getManquementObligationPresence() == AbsenceEleveType::$MANQU_OBLIG_PRESE_NON_PRECISE) {
                return true;
            }
        }
        return false;
    }
	/**
	 *
	 * Renvoi une chaine de caractere compréhensible concernant les dates de debut et de fin
	 *
	 * @return     string
	 *
	 */
	public function getDateDescription() {
	    $message = '';
	    if ($this->getDebutAbs('d/m/Y') == $this->getFinAbs('d/m/Y')) {
		$message .= 'Le ';
		$message .= (strftime("%a %d/%m/%Y", $this->getDebutAbs('U')));
		$message .= ' de ';
		$message .= $this->getDebutAbs('H:i');
		$message .= ' a ';
		$message .= $this->getFinAbs('H:i');

	    } else {
		$message .= 'Du ';
		$message .= (strftime("%a %d/%m/%Y %H:%M", $this->getDebutAbs('U')));
		$message .= ' au ';
		$message .= (strftime("%a %d/%m/%Y %H:%M", $this->getFinAbs('U')));
	    }
	    return $message;
	}

	/**
	 *
	 * Renvoi true ou false en fonction de la saisie. Ceci concerne le décompte des bulletins
	 *
	 * @return     boolean
	 *
	 */
	public function getRetard() {
	    if (!isset($this->retard) || $this->retard === null) {
		$retard = false;
		$nb_min = getSettingValue("abs2_retard_critere_duree");
		if ($nb_min == null
			|| $nb_min == '') {
		    $nb_min = 30;
		} if (($this->getFinAbs('U') - $this->getDebutAbs('U')) < 60*$nb_min) {
		    $retard = true;
		} else {
		    //on va regarder si il y a un retard dans les types
		    foreach ($this->getAbsenceEleveTraitements() as $traitement) {
			if ($traitement->getAbsenceEleveType() != null) {
			    if ($traitement->getAbsenceEleveType()->getRetardBulletin() == AbsenceEleveType::$RETARD_BULLETIN_VRAI) {
				$retard = true;
				break;
			    }
			}
		    }
		}
		$this->retard = $retard;
	    }
	    return $this->retard;
	}

	/**
	 *
	 * Renvoi true ou false si l'eleve etait sous la responsabilite de l'etablissement (infirmerie ou autre)
	 * une saisie qui n'est pas sous la responsabilite de l'etablissement sere comptee dans le bulletin
	 * une saisie qui est sous la responsabilite de l'etablissement ne sera pas comptee dans le bulletin
	 *
	 * @return     boolean
	 *
	 */
	public function getSousResponsabiliteEtablissement() {
	    if (!isset($this->sousResponsabiliteEtablissement) || $this->sousResponsabiliteEtablissement === null) {
		$type_sans = false;
		$type_avec = false;
		$type_non_precise = false;
		foreach ($this->getAbsenceEleveTraitements() as $traitement) {
		    if ($traitement->getAbsenceEleveType() != null) {
			if ($traitement->getAbsenceEleveType()->getSousResponsabiliteEtablissement() == AbsenceEleveType::$SOUS_RESP_ETAB_VRAI) {
			    $type_avec = true;
			} elseif ($traitement->getAbsenceEleveType()->getSousResponsabiliteEtablissement() == AbsenceEleveType::$SOUS_RESP_ETAB_FAUX) {
			    $type_sans = true;
			} else if ($traitement->getAbsenceEleveType()->getSousResponsabiliteEtablissement() == AbsenceEleveType::$SOUS_RESP_ETAB_NON_PRECISE) {
			    $type_non_precise = true;
			}
		    }
		}
		if ($type_avec == false && $type_sans == false && $type_non_precise == false) {
		    //on a aucune information on renvoit le reglage adequat
		    $sousResponsabiliteEtablissement = (getSettingValue("abs2_saisie_par_defaut_sous_responsabilite_etab")=='y');
		} else if ($type_avec == true && $type_sans == true) {
		    //on a les deux types, on renvoi le reglage adequat
		    $sousResponsabiliteEtablissement = (getSettingValue("abs2_saisie_multi_type_sous_responsabilite_etab")=='y');
		} else if ($type_avec == false && $type_sans == true) {
		    $sousResponsabiliteEtablissement = false;
		} else if ($type_avec == true && $type_sans == false) {
		    $sousResponsabiliteEtablissement = true;
		} else {//c'est le dernier cas : ($type_avec == false && $type_sans == false && $type_non_precise == true)
		    //si on a un type de responsabilite specifie a non_precise (comme le type 'erreur de saisie'),
		    //on renvoi une resp etab (sinon l'utilisateur aurait specifier un type $MANQU_OBLIG_PRESE_VRAI)
		    $sousResponsabiliteEtablissement = true;
		}
		$this->sousResponsabiliteEtablissement = $sousResponsabiliteEtablissement;
	    }
	    return $this->sousResponsabiliteEtablissement;
	}

	/**
	 *
	 *
	 * Renvoi true ou false si l'eleve est en manque de ses obligation de presence
	 * une saisie qui n'est pas un manquement ne sera pas comptee dans le bulletin
	 * une saisie qui est un manquement sera comptee dans le bulletin
	 * Cette propriété est calculé par l'intermediaire des types de traitement
	 *
	 * @return     boolean
	 *
	 */
	public function getManquementObligationPresence() {
	    if (!isset($this->manquementObligationPresence) || $this->manquementObligationPresence === null) {
		$type_sans = false;
		$type_avec = false;
		$type_non_precise = false;
		foreach ($this->getAbsenceEleveTraitements() as $traitement) {
		    if ($traitement->getAbsenceEleveType() != null) {
			if ($traitement->getAbsenceEleveType()->getManquementObligationPresence() == AbsenceEleveType::$MANQU_OBLIG_PRESE_VRAI) {
			    $type_avec = true;
			} else if ($traitement->getAbsenceEleveType()->getManquementObligationPresence() == AbsenceEleveType::$MANQU_OBLIG_PRESE_FAUX) {
			    $type_sans = true;
			} else if ($traitement->getAbsenceEleveType()->getManquementObligationPresence() == AbsenceEleveType::$MANQU_OBLIG_PRESE_NON_PRECISE) {
			    $type_non_precise = true;
			}
		    }
		}

		if ($type_avec == false && $type_sans == false && $type_non_precise == false) {
		    //on a aucune information on renvoit le reglage adequat
		    $manquementObligationPresence = (getSettingValue("abs2_saisie_par_defaut_sans_manquement")!='y');
		} else if ($type_avec == true && $type_sans == true) {
		    //on a les deux types, on renvoi le reglage adequat
		    $manquementObligationPresence = (getSettingValue("abs2_saisie_multi_type_sans_manquement")!='y');
		} else if ($type_avec == false && $type_sans == true) {
		    $manquementObligationPresence = false;
		} else if ($type_avec == true && $type_sans == false) {
		    $manquementObligationPresence = true;
		} else {//c'est le dernier cas : ($type_avec == false && $type_sans == false && $type_non_precise == true)
		    //si on a un type de manquement specifie a non_precise (comme le type 'erreur de saisie'),
		    //on renvoi un non manquement (sinon l'utilisateur aurait specifier un type $MANQU_OBLIG_PRESE_VRAI)
		    $manquementObligationPresence = false;
		}
		$this->manquementObligationPresence = $manquementObligationPresence;
	    }
	    return $this->manquementObligationPresence;
	}

	/**
	 *
	 *
	 * Renvoi true ou false si la saisie a un type de manquement spécifié a 'non précisé'
	 * Cette propriété est calculé par l'intermediaire des types de traitement
	 *
	 * @return     boolean
	 *
	 */
	public function getManquementObligationPresenceSpecifie_NON_PRECISE() {
	    if (!isset($this->manquementObligationPresenceSpecifie_NON_PRECISE) || $this->manquementObligationPresenceSpecifie_NON_PRECISE === null) {
		$type_non_precise = false;
		foreach ($this->getAbsenceEleveTraitements() as $traitement) {
		    if ($traitement->getAbsenceEleveType() != null) {
			if ($traitement->getAbsenceEleveType()->getManquementObligationPresence() == AbsenceEleveType::$MANQU_OBLIG_PRESE_VRAI
				|| $traitement->getAbsenceEleveType()->getManquementObligationPresence() == AbsenceEleveType::$MANQU_OBLIG_PRESE_FAUX) {
			    $type_non_precise = false;
			    break;
			} else if ($traitement->getAbsenceEleveType()->getManquementObligationPresence() == AbsenceEleveType::$MANQU_OBLIG_PRESE_NON_PRECISE) {
			    $type_non_precise = true;
			}
		    }
		}
		$this->manquementObligationPresenceSpecifie_NON_PRECISE = $type_non_precise;
	    }
	    return $this->manquementObligationPresenceSpecifie_NON_PRECISE;
	}

	/**
	 *
	 * Renvoi 'oui' ou 'non' si l'eleve manque une obligation de presence
	 * Ajoute pour les modele tbs
	 *
	 * @return     string
	 *
	 */
	public function getManquementObligationPresenceDescription() {
	    if ($this->getManquementObligationPresence()) {
		return 'oui';
	    } else {
		return 'non';
	    }
	}

	/**
	 *
	 * Renvoi 'oui' ou 'non' si l'eleve etait sous la responsabilite de l'etablissement (infirmerie ou autre)
	 * une saisie qui n'est pas sous la responsabilite de l'etablissement sere comptee dans le bulletin
	 * une saisie qui est sous la responsabilite de l'etablissement ne sera pas comptee dans le bulletin
	 * Ajoute pour les modele tbs
	 *
	 * @return     string
	 *
	 */
	public function getSousResponsabiliteEtablissementDescription() {
	    if ($this->getSousResponsabiliteEtablissement()) {
		return 'oui';
	    } else {
		return 'non';
	    }
	}

  /**
	 *
	 * Renvoi le motif s'il existe ou Null sinon
	 *
	 * @return     string
	 *
	 */
    public function getMotif() {
        $motif = Null;
        $besoin_echo_virgule = false;
        if ($this->getTraitee()) {
            foreach ($this->getAbsenceEleveTraitements() as $traitement) {
                if ($traitement->getAbsenceEleveMotif() != null) {
                    if ($besoin_echo_virgule) {
                        $motif.= ', ';                        
                    }                   
                    $motif.=$traitement->getAbsenceEleveMotif()->getNom();
                    $besoin_echo_virgule = true;
                }
            }
        }
        return ($motif);
    }

    /**
     *
     * Renvoi la justification si elle existe ou Null sinon
     *
     * @return     string
     *
     */
    public function getJustification() {
        $justification = Null;
        $besoin_echo_virgule = false;
        if ($this->getJustifiee()) {
            foreach ($this->getAbsenceEleveTraitements() as $traitement) {
                if ($traitement->getAbsenceEleveJustification() != null) {
                    if ($besoin_echo_virgule) {
                        $justification.= ', ';
                    }
                    $justification.=$traitement->getAbsenceEleveJustification()->getNom();
                    $besoin_echo_virgule = true;
                }
            }
        }
        return ($justification);
    }

	/**
	 *
	 * Renvoi true ou false en fonction des justifications apporte
	 *
	 * @return     boolean
	 *
	 */
	public function getJustifiee() {
	    if (!isset($this->justifiee) || $this->justifiee === null) {
		$justifiee_sans = false;
		$justifiee_avec = false;
		foreach ($this->getAbsenceEleveTraitements() as $traitement) {
			if ($traitement->getAbsenceEleveJustification() != null) {
			    $justifiee_avec = true;
			} else {
			    $justifiee_sans = true;
			}
			if ($justifiee_avec && $justifiee_sans) {
			    break;
			}
		}

		if ($justifiee_avec && $justifiee_sans ) {
		    //on a aucune information on renvoit le reglage adequat
		    $this->justifiee = (getSettingValue("abs2_saisie_multi_type_non_justifiee")!='y');
		} else if ($justifiee_avec) {
		    $this->justifiee =  true;
		} else {
		    $this->justifiee =  false;
		}
	    }
	    return $this->justifiee;
	}

	/**
	 *
	 * Renvoi le nom du groupe (avec les classe) ou une chaine vide
	 * utilise pour les template tbs
	 * @return     string
	 *
	 */
	public function getGroupeNameAvecClasses() {
	    if ($this->getGroupe() != null) {
		return $this->getGroupe()->getNameAvecClasses();
	    } else {
		return '';
	    }
	}
	/**
	 *
	 * Renvoi le nom du groupe ou une chaine vide
	 * utilise pour les template tbs
	 * @return     string
	 *
	 */
	public function getGroupeName() {
	    if ($this->getGroupe() != null) {
		return $this->getGroupe()->getName();
	    } else {
		return '';
	    }
	}

	/**
	 *
	 * Renvoi true si un traitement est associe a la saisie
	 *
	 * @return     boolean
	 *
	 */
	public function getTraitee() {
	    return ($this->getAbsenceEleveTraitements()->count() != 0);
	}

	/**
	 *
	 * Renvoi true si une notification a ete recue par la famille
	 *
	 * @return     boolean
	 *
	 */
	public function getNotifiee() {
	    foreach ($this->getAbsenceEleveTraitements() as $traitement) {
		foreach ($traitement->getAbsenceEleveNotifications() as $notification) {
		    if ($notification->getStatutEnvoi() == AbsenceEleveNotification::$STATUT_SUCCES || $notification->getStatutEnvoi() == AbsenceEleveNotification::$STATUT_SUCCES_AR) {
			return true;
		    }
		}
	    }
	    return false;
	}

	/**
	 * Gets a collection of AbsenceEleveTraitement objects related by a many-to-many relationship
	 * to the current object by way of the j_traitements_saisies cross-reference table.
	 *
	 * ajout d'un join pour recuperer les types en meme temps que les traitements
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this AbsenceEleveSaisie is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria Optional query object to filter the query
	 * @param      PropelPDO $con Optional connection object
	 *
	 * @return     PropelCollection|array AbsenceEleveTraitement[] List of AbsenceEleveTraitement objects
	 */
	public function getAbsenceEleveTraitements($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collAbsenceEleveTraitements || null !== $criteria) {
			if ($this->isNew() && null === $this->collAbsenceEleveTraitements) {
				// return empty collection
				$this->initAbsenceEleveTraitements();
			} else {
				if ($this->collJTraitementSaisieEleves === null || null !== $criteria) {
				    if (null !== $criteria) {
					return AbsenceEleveTraitementQuery::create(null, $criteria)
						->filterByAbsenceEleveSaisie($this)
						->find($con);
				    } else {
					//on utilise du sql directement pour optimiser la requete
					//WARNING WARNING WARNING WARNING
					//si le modele change ca va bugger, il faut utiliser la requete AbsenceEleveTraitementQuery en dessous
					//le sql a ete generer en activant les logs propel et en recuperant le sql de la requete ci-dessous
					$sql = "SELECT /* comment_getAbsenceEleveTraitements */ a_traitements.ID, a_traitements.UTILISATEUR_ID, a_traitements.A_TYPE_ID, a_traitements.A_MOTIF_ID, a_traitements.A_JUSTIFICATION_ID, a_traitements.COMMENTAIRE, a_traitements.MODIFIE_PAR_UTILISATEUR_ID, a_traitements.CREATED_AT, a_traitements.UPDATED_AT, a_types.ID, a_types.NOM, a_types.JUSTIFICATION_EXIGIBLE, a_types.SOUS_RESPONSABILITE_ETABLISSEMENT, a_types.MANQUEMENT_OBLIGATION_PRESENCE, a_types.RETARD_BULLETIN, a_types.TYPE_SAISIE, a_types.COMMENTAIRE, a_types.ID_LIEU, a_types.SORTABLE_RANK, a_notifications.ID, a_notifications.UTILISATEUR_ID, a_notifications.A_TRAITEMENT_ID, a_notifications.TYPE_NOTIFICATION, a_notifications.EMAIL, a_notifications.TELEPHONE, a_notifications.ADR_ID, a_notifications.COMMENTAIRE, a_notifications.STATUT_ENVOI, a_notifications.DATE_ENVOI, a_notifications.ERREUR_MESSAGE_ENVOI, a_notifications.CREATED_AT, a_notifications.UPDATED_AT, a_justifications.ID, a_justifications.NOM, a_justifications.COMMENTAIRE, a_justifications.SORTABLE_RANK FROM `a_traitements` INNER JOIN j_traitements_saisies ON (a_traitements.ID=j_traitements_saisies.A_TRAITEMENT_ID) LEFT JOIN a_types ON (a_traitements.A_TYPE_ID=a_types.ID) LEFT JOIN a_notifications ON (a_traitements.ID=a_notifications.A_TRAITEMENT_ID) LEFT JOIN a_justifications ON (a_traitements.A_JUSTIFICATION_ID=a_justifications.ID) WHERE j_traitements_saisies.A_SAISIE_ID='".$this->getId()."'";
					$con = Propel::getConnection(AbsenceEleveTraitementPeer::DATABASE_NAME, Propel::CONNECTION_READ);
					$stmt = $con->prepare($sql);
					$stmt->execute();

					$this->collAbsenceEleveTraitements = AbsenceEleveSaisie::getTraitementFormatter()->format($stmt);


//					$this->collAbsenceEleveTraitements = AbsenceEleveTraitementQuery::create()
//						->setComment('comment_getAbsenceEleveTraitements')
//						->useJTraitementSaisieEleveQuery()->filterByASaisieId($this->getId())->endUse()
//						->leftJoinWith('AbsenceEleveType')
//						->leftJoinWith('AbsenceEleveNotification')
//						->leftJoinWith('AbsenceEleveJustification')
//						->find();

//					foreach ($this->collAbsenceEleveTraitements as $traitement) {
//					    echo $this->getId().'sql $traitement->isTypeHydrated() : '.$traitement->isTypeHydrated().'<br/>';
//					    echo $this->getId().'sql $traitement->isNotificationHydrated() : '.$traitement->isNotificationHydrated().'<br/>';
//					    echo $this->getId().'sql $traitement->isJustificationHydrated() : '.$traitement->isJustificationHydrated().'<br/>';
//					}
				    }
				} else {
				    $this->collAbsenceEleveTraitements = new PropelObjectCollection();
				    $this->collAbsenceEleveTraitements->setModel('AbsenceEleveTraitement');
				    foreach ($this->collJTraitementSaisieEleves as $jTraitementSaisieEleve) {
					if ($jTraitementSaisieEleve->getAbsenceEleveTraitement() !== null) {
					    $this->collAbsenceEleveTraitements->append($jTraitementSaisieEleve->getAbsenceEleveTraitement());
					}
				    }
//				    foreach ($this->collAbsenceEleveTraitements as $traitement) {
//					echo $this->getId().'collJ $traitement->isTypeHydrated() : '.$traitement->isTypeHydrated().'<br/>';
//					echo $this->getId().'collJ $traitement->isNotificationHydrated() : '.$traitement->isNotificationHydrated().'<br/>';
//					echo $this->getId().'collJ $traitement->isJustificationHydrated() : '.$traitement->isJustificationHydrated().'<br/>';
//				    }
				}
			}
		}
		return $this->collAbsenceEleveTraitements;
	}

	/**
	 * PropelFormatter pour la requete sql directe
	 */
	private static $traitementFormatter;

	/**
	 * PropelFormatter pour la requete sql directe
	 *
	 * @return     PropelFormatter pour le requete getGroupe
	 */
	private static function getTraitementFormatter() {
	    if (AbsenceEleveSaisie::$traitementFormatter === null) {
		    $formatter = new PropelObjectFormatter();
		    $formatter->setDbName(AbsenceEleveTraitementPeer::DATABASE_NAME);
		    $formatter->setClass('AbsenceEleveTraitement');
		    $formatter->setPeer('AbsenceEleveTraitementPeer');
		    $formatter->setAsColumns(array());
		    $formatter->setHasLimit(false);

		    $typeTableMap = Propel::getDatabaseMap(AbsenceEleveTraitementPeer::DATABASE_NAME)->getTableByPhpName('AbsenceEleveTraitement');
		    $width = array();
		    // create a ModelJoin object for this join
		    $typeJoin = new ModelJoin();
		    $typeJoin->setJoinType(Criteria::LEFT_JOIN);
		    $typeRelation = $typeTableMap->getRelation('AbsenceEleveType');
		    $typeJoin->setRelationMap($typeRelation);
		    $width["AbsenceEleveType"] = $typeJoin;

		    $notificationJoin = new ModelJoin();
		    $notificationJoin->setJoinType(Criteria::LEFT_JOIN);
		    $notificationRelation = $typeTableMap->getRelation('AbsenceEleveNotification');
		    $notificationJoin->setRelationMap($notificationRelation);
		    $width["AbsenceEleveNotification"] = $notificationJoin;

		    $justificationJoin = new ModelJoin();
		    $justificationJoin->setJoinType(Criteria::LEFT_JOIN);
		    $justificationRelation = $typeTableMap->getRelation('AbsenceEleveJustification');
		    $justificationJoin->setRelationMap($justificationRelation);
		    $width["AbsenceEleveJustification"] = $justificationJoin;

		    $formatter->setWith($width);
		    AbsenceEleveSaisie::$traitementFormatter = $formatter;
	    }
	    return AbsenceEleveSaisie::$traitementFormatter;
	}

	/**
	 * This function performs the validation work for complex object models.
	 *
	 * ADDED : on ne verifie pas les objets lies car c'est exponentiel
	 *
	 * In addition to checking the current object, all related objects will
	 * also be validated.  If all pass then <code>true</code> is returned; otherwise
	 * an aggreagated array of ValidationFailed objects will be returned.
	 *
	 * @param      array $columns Array of column names to validate.
	 * @return     mixed <code>true</code> if all validations pass; array of <code>ValidationFailed</code> objets otherwise.
	 */
	protected function doValidate($columns = null)
	{
		if (!$this->alreadyInValidation) {
			$this->alreadyInValidation = true;
			$retval = null;

			$failureMap = array();

			if (($retval = AbsenceEleveSaisiePeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collJTraitementSaisieEleves !== null) {
					foreach ($this->collJTraitementSaisieEleves as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}


			$this->alreadyInValidation = false;
		}

		return (!empty($failureMap) ? $failureMap : true);
	}

	/**
	 *
	 * Renvoi un liste de saisies qui sont en contradiction avec celle la concernant le manquement des obligations de presence (apparition dans le bulletin)
	 *
	 * @param boolean $retourne_booleen la fonction retourne vrai ou faux selon qu'il y ai des saisies contradictoires au lieu de retourner une collection
	 *
	 * @return mixed boolean or PropelObjectCollection AbsenceEleveSaisie[]
	 *
	 */
	public function getSaisiesContradictoiresManquementObligation($retourne_booleen = false) {
	    //if (isset($this->boolSaisiesContradictoiresManquementObligation)) {echo 'mis en cache';} else {echo 'pas de cache';}
	    if (($retourne_booleen && (!isset($this->boolSaisiesContradictoiresManquementObligation) || $this->boolSaisiesContradictoiresManquementObligation === null))
		|| (!$retourne_booleen && (!isset($this->collectionSaisiesContradictoiresManquementObligation) || $this->collectionSaisiesContradictoiresManquementObligation === null))) {
		$result = new PropelObjectCollection();
		$result->setModel('AbsenceEleveSaisie');
		if ($this->getEleve() === null) {
		    $this->boolSaisiesContradictoiresManquementObligation = false;
		    $this->collectionSaisiesContradictoiresManquementObligation = $result;
		} else {

		    //on regarde les saisies sur cet eleve
		    $eleve = $this->getEleve();
		    $manque = $this->getManquementObligationPresence();
		    foreach ($eleve->getAbsenceEleveSaisiesFilterByDate($this->getDebutAbs(null), $this->getFinAbs(null)) as $saisie) {
			if ($manque !== $saisie->getManquementObligationPresence()) {
			    if ($retourne_booleen) {
				$this->boolSaisiesContradictoiresManquementObligation = true;
				return true;
			    }
			    $result->append($saisie);
			}
		    }

		    if ($manque == true) {
			//on recupere les saisies de marquage d'absence (donc sans eleves) qui se chevauchent avec celle-la
			//optimisation : utiliser la requete pour stocker ca
			if (isset($_REQUEST['query_AbsenceEleveSaisieQuery_getSaisiesContradictoires_'.$this->getDebutAbs('U').'_'.$this->getFinAbs('U')])
				&& $_REQUEST['query_AbsenceEleveSaisieQuery_getSaisiesContradictoires_'.$this->getDebutAbs('U').'_'.$this->getFinAbs('U')] != null) {
			    $saisie_col = $_REQUEST['query_AbsenceEleveSaisieQuery_getSaisiesContradictoires_'.$this->getDebutAbs('U').'_'.$this->getFinAbs('U')];
			} else {
			    $query = AbsenceEleveSaisieQuery::create();
			    $query->filterByPlageTemps($this->getDebutAbs(null), $this->getFinAbs(null))
				->add(AbsenceEleveSaisiePeer::ELEVE_ID, NULL)
                                ->addOr(AbsenceEleveSaisiePeer::ELEVE_ID, $this->getEleveId())
                                ;
			    $saisie_col = $query->find();
			    $_REQUEST['query_AbsenceEleveSaisieQuery_getSaisiesContradictoires_'.$this->getDebutAbs('U').'_'.$this->getFinAbs('U')] = $saisie_col;
			}

			//on va filtrer pour supprimer de la liste les aid, classe ou groupe qui serait le meme que cette saisie la
			$temp_saisie_col = new PropelObjectCollection();
			$temp_saisie_col->setModel('AbsenceEleveSaisie');
			if ($this->getClasse() != null) {
			    foreach ($saisie_col as $saisie) {
				if ($saisie->getIdClasse() != $this->getIdClasse()) {
				    $temp_saisie_col->append($saisie);
				}
			    }
			} elseif ($this->getGroupe() != null) {
			    foreach ($saisie_col as $saisie) {
				if ($saisie->getIdGroupe() != $this->getIdGroupe()) {
				    $temp_saisie_col->append($saisie);
				}
			    }
			} elseif ($this->getAidDetails() != null) {
			    foreach ($saisie_col as $saisie) {
				if ($saisie->getIdAid() != $this->getIdAid()) {
				    $temp_saisie_col->append($saisie);
				}
			    }
			} else {
			    foreach ($saisie_col as $saisie) {
				if ($saisie->getId() != $this->getId()) {
				    $temp_saisie_col->append($saisie);
				}
			    }
			}
			$saisie_col = $temp_saisie_col;

			//on regarde si un groupe, classe ou aid auquel appartient cet eleve a été saisi et pour lequel l'eleve en question n'a pas ete saisi (c'est donc que l'eleve est present)
			//on va utiliser comme periode pour determiner les classes et groupes la periode correspondant au debut de l'absence
			$periode = $eleve->getPeriodeNote($this->getDebutAbs(null));

			//on recupere la liste des classes de l'eleve et on regarde si il y a eu des saisies pour ces classes
			$classes = $eleve->getClasses($periode);
			$saisie_col_classe_id_array = $saisie_col->toKeyValue('PrimaryKey','IdClasse');
			$saisie_col_array_copy = $saisie_col->getArrayCopy('Id');
			foreach ($classes as $classe) {
			    $keys = array_keys($saisie_col_classe_id_array, $classe->getId());
			    if (!empty($keys)) {
				//on a des saisies pour cette classe
				//est-ce que l'eleve a bien été saisi absent ?
				$temp_col = new PropelObjectCollection();
				$bool_eleve_saisi = false;
				foreach ($keys as $key) {
				    $saisie_temp = $saisie_col_array_copy[$key];
				    if ($saisie_temp->getEleveId() === null) {
					$temp_col->append($saisie_temp);
				    }
				    if ($this->getEleveId() == $saisie_temp->getEleveId()) {
					$bool_eleve_saisi = true;
				    }
				}
				if (!$bool_eleve_saisi) {
				    //l'eleve n'a pas ete saisi, c'est contradictoire
				    if ($retourne_booleen) {
					$this->boolSaisiesContradictoiresManquementObligation = true;
					return true;
				    }
				    $result->addCollection($temp_col);
				}
			    }
			}

			//on recupere la liste des groupes de l'eleve et on regarde si il y a eu des saisies pour ces groupes
			$groupes = $eleve->getGroupes($periode);
			$saisie_col_groupe_id_array = $saisie_col->toKeyValue('PrimaryKey','IdGroupe');
			foreach ($groupes as $groupe) {
			    $keys = array_keys($saisie_col_groupe_id_array, $groupe->getId());
			    if (!empty($keys)) {
				//on a des saisies pour cette groupe
				//est-ce que l'eleve a bien été saisi absent ?
				$temp_col = new PropelObjectCollection();
				$bool_eleve_saisi = false;
				foreach ($keys as $key) {
				    $saisie_temp = $saisie_col_array_copy[$key];
				    if ($saisie_temp->getEleveId() === null) {
					$temp_col->append($saisie_temp);
				    }
				    if ($this->getEleveId() == $saisie_temp->getEleveId()) {
					$bool_eleve_saisi = true;
				    }
				}
				if (!$bool_eleve_saisi) {
				    //l'eleve n'a pas ete saisi, c'est contradictoire
				    if ($retourne_booleen) {
					$this->boolSaisiesContradictoiresManquementObligation = true;
					return true;
				    }
				    $result->addCollection($temp_col);
				}
			    }
			}

			//on recupere la liste des aids de l'eleve et on regarde si il y a eu des saisies pour ces aids
			$aids = $eleve->getAidDetailss($periode);
			$saisie_col_aid_id_array = $saisie_col->toKeyValue('PrimaryKey','IdAid');
			foreach ($aids as $aid) {
			    $keys = array_keys($saisie_col_aid_id_array, $aid->getId());
			    if (!empty($keys)) {
				//on a des saisies pour cette aid
				//est-ce que l'eleve a bien été saisi absent ?
				$temp_col = new PropelObjectCollection();
				$bool_eleve_saisi = false;
				foreach ($keys as $key) {
				    $saisie_temp = $saisie_col_array_copy[$key];
				    if ($saisie_temp->getEleveId() === null) {
					$temp_col->append($saisie_temp);
				    }
				    if ($this->getEleveId() == $saisie_temp->getEleveId()) {
					$bool_eleve_saisi = true;
				    }
				}
				if (!$bool_eleve_saisi) {
				    //l'eleve n'a pas ete saisi, c'est contradictoire
				    if ($retourne_booleen) {
					$this->boolSaisiesContradictoiresManquementObligation = true;
					return true;
				    }
				    $result->addCollection($temp_col);
				}
			    }
			}
		    }
		    $this->boolSaisiesContradictoiresManquementObligation = !$result->isEmpty();
		    $this->collectionSaisiesContradictoiresManquementObligation = $result;
		}
	    }
	    if ($retourne_booleen) {return $this->boolSaisiesContradictoiresManquementObligation;}
	    return $this->collectionSaisiesContradictoiresManquementObligation;
	}

	/**
	 *
	 * Renvoi true/false selon qu'il y a des saisies contradictoires
	 *
	 * @return PropelObjectCollection AbsenceEleveSaisie[]
	 *
	 */
	public function isSaisiesContradictoiresManquementObligation() {
	    return $this->getSaisiesContradictoiresManquementObligation(true);
	}

	/**
	 * Ajout manuel : renseignement automatique de l'utilisateur qui a créé ou modifié la saisie
	 * Persists this object to the database.
	 *
	 * If the object is new, it inserts it; otherwise an update is performed.
	 * All modified related objects will also be persisted in the doSave()
	 * method.  This method wraps all precipitate database operations in a
	 * single transaction.
	 *
	 * @param      PropelPDO $con
	 * @return     int The number of rows affected by this insert/update and any referring fk objects' save() operations.
	 * @throws     PropelException
	 * @see        doSave()
	 */
	public function save(PropelPDO $con = null)
	{
	    if ($this->isNew()) {
		if ($this->getUtilisateurId() == null) {
		    $utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
		    if ($utilisateur != null) {
			$this->setUtilisateurProfessionnel($utilisateur);
		    }
		}
	    } else {
		$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
		if ($utilisateur != null) {
		    $this->setModifieParUtilisateur($utilisateur);
		}
	    }
	    return parent::save($con);
	}

	/**
	 *
	 * Renvoi le lieu de l'absence ou le lieu de plus petit rang des types d'absence associé.
	 *
	 * @return     AbsenceEleveLieu
	 *
	 */
	public function  getAbsenceEleveLieuEtendu(PropelPDO $con = null) {
            $lieu = parent::getAbsenceEleveLieu($con);
            if ($lieu != null) {
                return $lieu;
            } else {
                //parcourir les types associés et retourner le lieu de plus petit rang
                throw new PropelException("non implémenté");
            }
        }

     /**
	 *
	 * Renvoi true/false selon que l'élève est sorti ou non de l'établissement
	 *
	 * @return Boolean
	 *
	 */
    public function isSaisieEleveSorti($date_debut_test) {

        $eleve = $this->getEleve();
        if (!is_null($eleve)) {
            $date_sortie_eleve = $eleve->getDateSortie('U');            
            if (is_null($date_sortie_eleve) || $date_sortie_eleve == 0) {
                return false;
            } else {
                if ($date_debut_test->format('U') > $date_sortie_eleve) {
                    return(true);
                } else {
                    return(false);
                }
            }
        }else{
            return(false);
        }
    }

} // AbsenceEleveSaisie
