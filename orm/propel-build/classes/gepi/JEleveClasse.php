<?php



/**
 * Skeleton subclass for representing a row from the 'j_eleves_classes' table.
 *
 * Table de jointure entre les eleves et leur classe en fonction de la periode
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class JEleveClasse extends BaseJEleveClasse {

      	/**
	 *
	 * Retourne la periode de note associée
	 *
	 *
	 * @return PeriodeNote $periode_note
	 */
	public function getPeriodeNote() {
	    return PeriodeNoteQuery::create()->filterByIdClasse($this->getIdClasse())->filterByNumPeriode($this->getPeriode())->findOne();
	}

	public function isClasseHydrated() {
	    return $this->aClasse != null;
	}

	
} // JEleveClasse
