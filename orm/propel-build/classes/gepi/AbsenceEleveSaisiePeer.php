<?php



/**
 * Skeleton subclass for performing query and update operations on the 'a_saisies' table.
 *
 * Chaque saisie d'absence doit faire l'objet d'une ligne dans la table a_saisies. Une saisie peut etre : une plage horaire longue durÃ©e (plusieurs jours), dÃ©fini avec les champs debut_abs et fin_abs. Un creneau horaire, le jour etant precisÃ© dans debut_abs. Un cours de l'emploi du temps, le jours du cours etant precisÃ© dans debut_abs.
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class AbsenceEleveSaisiePeer extends BaseAbsenceEleveSaisiePeer {

	/**
	 * Validates all modified columns of given AbsenceEleveTraitement object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      AbsenceEleveTraitement $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate($obj, $cols = null)
	{
	    $failureMap = array();
	    if (($retval = parent::doValidate($obj, $cols)) !== true) {
		    $failureMap = array_merge($failureMap, $retval);
	    }


	    //validation maison
	    //on exclus mutuellement un id_classe, et id_groupe et un id_aid
	    $id_relation = 0;
	    if ($obj->getAidDetails() !== null) {
		$id_relation = $id_relation + 1;
	    }
	    if ($obj->getClasse() !== null) {
		$id_relation = $id_relation + 1;
	    }
	    if ($obj->getGroupe() !== null) {
		$id_relation = $id_relation + 1;
	    }
	    if ($id_relation > 1) {
		$failureMap[AbsenceEleveSaisiePeer::ID] = "Il ne peut y avoir un groupe, une classe et une aid simultanéments pécisé.<br/>";
	    }

	    if ($obj->getEleveId() !== null) {
		if ($obj->getEleve() == null) {
		    $failureMap[AbsenceEleveSaisiePeer::ELEVE_ID] = "L'id de l'eleve est incorrect.<br/>";
		}
	    }

	    if ($obj->getEdtEmplacementCours() !== null) {
		//si on saisie un cours, alors le creneau et la classe doive etre
		if ($obj->getIdClasse() !== null) {
		    $failureMap[AbsenceEleveSaisiePeer::ID] = "Si un cours est renseigne la classe doit etre nul.<br/>";
		}
		if ($obj->getIdEdtCreneau() !== null && $obj->getEdtEmplacementCours()->getIdDefiniePeriode() != $obj->getIdEdtCreneau()) {
		    $failureMap[AbsenceEleveSaisiePeer::ID] = "Si un cours est renseigne le creneau doit lui correspondre.<br/>";
		}
		if ($obj->getIdGroupe() === null && $obj->getIdAid() === null) {
		    $failureMap[AbsenceEleveSaisiePeer::ID] = "Si un cours est renseigne alors le groupe ou l'aid doivent etre saisies.<br/>";
		}
		if ($obj->getIdGroupe() != null && $obj->getEdtEmplacementCours()->getIdGroupe() != $obj->getIdGroupe()) {
		    $failureMap[AbsenceEleveSaisiePeer::ID] = "Si un cours est renseigne alors le groupe doit etre celui du cours.<br/>";
		}
		if ($obj->getIdAid() != null && $obj->getEdtEmplacementCours()->getIdAid() != $obj->getIdAid()) {
		    $failureMap[AbsenceEleveSaisiePeer::ID] = "Si un cours est renseigne alors l'aid doit etre celle du cours.<br/>";
		}
	    }

	    //si il y a un eleve, on verifie qu'il appartient bien au groupe, à la classe ou à l'aid précisé
	    if ($obj->getAidDetails() != null && $obj->getEleve() != null) {
		$criteria = new Criteria();
		$criteria->add(JAidElevesPeer::LOGIN, $obj->getEleve()->getLogin());
		if ($obj->getAidDetails()->countJAidElevess($criteria) == 0) {
		    $failureMap[AbsenceEleveSaisiePeer::ELEVE_ID] = "L'eleve n'appartient pas à l'aid selectionné : ".$obj->getAidDetails()->getNom()."<br/>";
		}
	    }

	    //si il y a un eleve, on verifie qu'il appartient bien au groupe, à la classe ou à l'aid précisé
	    if ($obj->getGroupe() != null && $obj->getEleve() != null) {
		$criteria = new Criteria();
		$criteria->add(JEleveGroupePeer::LOGIN, $obj->getEleve()->getLogin());
		if ($obj->getGroupe()->countJEleveGroupes($criteria) == 0) {
		    $failureMap[AbsenceEleveSaisiePeer::ELEVE_ID] = "L'eleve n'appartient pas au groupe selectionné.<br/>";
		}
	    }

	    //si il y a un eleve, on verifie qu'il appartient bien au groupe, à la classe ou à l'aid précisé
	    if ($obj->getClasse() != null && $obj->getEleve() != null) {
		$criteria = new Criteria();
		$criteria->add(JEleveClassePeer::LOGIN, $obj->getEleve()->getLogin());
		if ($obj->getClasse()->countJEleveClasses($criteria) == 0) {
		    $failureMap[AbsenceEleveSaisiePeer::ELEVE_ID] = "L'eleve n'appartient pas à la classe selectionnée.<br/>";
		}
	    }

	    if ($obj->getUtilisateurId() === null) {
		$failureMap[AbsenceEleveSaisiePeer::UTILISATEUR_ID] .= "Il faut preciser l'utilisateur qui rentre la saisie.<br/>";
	    }

	    if ($obj->getDebutAbs() == null) {
		$failureMap[AbsenceEleveSaisiePeer::DEBUT_ABS] .= "La date de debut d'absence ne doit pas etre nulle.<br/>";
	    }

	    if ($obj->getFinAbs() == null) {
		 $failureMap[AbsenceEleveSaisiePeer::FIN_ABS] .= "La date de fin d'absence ne doit pas etre nulle.<br/>";
	    }

	    if ($obj->getDebutAbs('U') >= $obj->getFinAbs('U')) {
		$failureMap[AbsenceEleveSaisiePeer::FIN_ABS] = "La date de debut d'absence doit etre strictement anterieure à la date de fin.<br/>";
	    }

//	    if ($obj->getEdtEmplacementCours() != null) {
//		//un emplacement de cours est saisie, il faut verifier que les heure de debut et de fin d'absences sont coherent avec l'emplacement de cours.
//		if ($obj->getDebutAbs("Hi") < $obj->getEdtEmplacementCours()->getHeureDebut("Hi")) {
//		    $failureMap[AbsenceEleveSaisiePeer::FIN_ABS] = "L'heure de debut d'absence ne peut pas etre anterieure au cours.<br/>";
//		} elseif ($obj->getFinAbs("Hi") > $obj->getEdtEmplacementCours()->getHeureFin("Hi")) {
//		    $failureMap[AbsenceEleveSaisiePeer::FIN_ABS] = "L'heure de fin d'absence ne peut pas etre posterieure au cours.<br/>";
//		} elseif (($obj->getFinAbs("U") - $obj->getDebutAbs("U")) > ($obj->getEdtEmplacementCours()->getHeureFin("U") - $obj->getEdtEmplacementCours()->getHeureDebut("U"))) {
//		    $failureMap[AbsenceEleveSaisiePeer::FIN_ABS] = "La durée de l'absence ne peut pas etre superieure à la durée du cours (verifier les date).<br/>";
//		}
//	    }

	    return (!empty($failureMap) ? $failureMap : true);

	}


} // AbsenceEleveSaisiePeer
