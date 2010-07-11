<?php



/**
 * Skeleton subclass for representing a row from the 'a_traitements' table.
 *
 * Un traitement peut gerer plusieurs saisies et consiste Ã  definir les motifs/justifications... de ces absences saisies
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class AbsenceEleveTraitement extends BaseAbsenceEleveTraitement {

	/**
	 *
	 * Renvoi une description intelligible du traitement
	 *
	 * @return     String description
	 *
	 */
	public function getDescriptionCourte() {
	    $desc = '';
	    $desc .= strftime("%a %d %b %Y", $this->getUpdatedAt('U'));
	    if ($this->getAbsenceEleveType() != null) {
		$desc .= "; type : ".$this->getAbsenceEleveType()->getNom();
	    }
	    if ($this->getAbsenceEleveMotif() != null) {
		$desc .= "; motif : ".$this->getAbsenceEleveMotif()->getNom();
	    }
	    if ($this->getAbsenceEleveJustification() != null) {
		$desc .= "; justification : ".$this->getAbsenceEleveJustification()->getNom();
	    }
	    if ($this->getCommentaire() != null && $this->getCommentaire() != '') {
		$desc .= "; Commentaire : ".$this->getCommentaire();
	    }
	    return $desc;
	}

	/**
	 *
	 * Renvoi true / false suivant que le traitement est modifiable ou pas
	 *
	 * @return     String description
	 *
	 */
	public function getModifiable() {

	    //modifiable uniquement si aucune notifications n'a été envoyé
	    foreach ($this->getAbsenceEleveNotifications() as $notification) {
		if ($notification->getStatutEnvoi() != AbsenceEleveNotification::$STATUT_INITIAL) {
		    return false;
		}
	    }
	    return true;
	}

	/**
	 *
	 * Renvoi la liste de tout les responsables légaux des saisies associees a ce traitement
	 *
	 * @return     PropelObjectCollection collection d'objets de la classe ResponsableInformation
	 *
	 */
	public function getResponsablesInformationsSaisies() {
	    $resp_col = new PropelObjectCollection();
	    $resp_col->setModel('ResponsableInformation');
	    foreach ($this->getAbsenceEleveSaisies() as $saisie) {
		foreach ($saisie->getEleve()->getResponsableInformations() as $responsable_information) {
		    $resp_col->add($responsable_information);
		}
	    }
	    return $resp_col;
	}
} // AbsenceEleveTraitement
