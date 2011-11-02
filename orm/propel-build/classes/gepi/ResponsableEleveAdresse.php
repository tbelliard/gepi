<?php



/**
 * Skeleton subclass for representing a row from the 'resp_adr' table.
 *
 * Table de jointure entre les responsables legaux et leur adresse
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class ResponsableEleveAdresse extends BaseResponsableEleveAdresse {

 	/**
	 *
	 * Renvoi la description de la liste des responsables habitant a cette adresse
	 *
	 *
	 * @return     String
	 *
	 */
	public function getDescriptionHabitant() {
	    $result = '';
	    foreach ($this->getResponsableEleves() as $responsableEleve) {
		//$responsableEleve = new ResponsableEleve();
		$result .= $responsableEleve->getCivilite().' '.strtoupper($responsableEleve->getNom()).' '.$responsableEleve->getPrenom();
		if (!$this->getResponsableEleves()->isLast()) {
		    $result .= ', ';
		}
	    }
	    return $result;
	}

 	/**
	 *
	 * Renvoi la description de la liste des responsables habitant a cette adresse
	 *
	 *
	 * @return     String
	 *
	 */
	public function getDescriptionSurUneLigne() {
	    $result = '';
	    if ($this->getAdr1() != null && $this->getAdr1() != '') {
		$result .= $this->getAdr1();
	    }
	    if ($this->getAdr2() != null && $this->getAdr2() != '') {
		if ($result != '' && substr($result, -2) != ', ') {$result .= ', ';}
		$result .= $this->getAdr2();
	    }
	    if ($this->getAdr3() != null && $this->getAdr3() != '') {
		if ($result != '' && substr($result, -2) != ', ') {$result .= ', ';}
		$result .= $this->getAdr3();
	    }
	    if ($this->getAdr4() != null && $this->getAdr4() != '') {
		if ($result != '' && substr($result, -2) != ', ') {$result .= ', ';}
		$result .= $this->getAdr4();
	    }
	    if ($result != '' && substr($result, -2) != ', ') {$result .= ', ';}
	    $result .= $this->getCp().' '.$this->getCommune();
	    if ($this->getPays() != null && $this->getPays() != '') {
		if ($result != '' && substr($result, -2) != ', ') {$result .= ', ';}
		$result .= $this->getPays();
	    }

	    return $result;
	}

} // ResponsableEleveAdresse
