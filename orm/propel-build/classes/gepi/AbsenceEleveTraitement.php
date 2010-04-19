<?php


/**
 * Skeleton subclass for representing a row from the 'a_traitements' table.
 *
 * Un traitement peut gerer plusieurs saisies et consiste à definir les motifs/justifications... de ces absences saisies
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

} // AbsenceEleveTraitement
