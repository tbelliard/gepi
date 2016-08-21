<?php



/**
 * Skeleton subclass for representing a row from the 'a_saisies_version' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class AbsenceEleveSaisieVersion extends BaseAbsenceEleveSaisieVersion {
	
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
	
} // AbsenceEleveSaisieVersion
