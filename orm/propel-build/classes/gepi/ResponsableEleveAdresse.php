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

} // ResponsableEleveAdresse
