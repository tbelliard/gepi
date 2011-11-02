<?php

class UnitTestResponsableEleve  {

	public static function getResponsableInformation() {
		//Creation d'une fiche ResponsableInformation
		$responsableInformation = new ResponsableInformation();
		$responsableInformation->setPersContact('0');
		return $responsableInformation;
	}

	public static function getResponsableEleve() {
		//Creation d'une fiche ResponsableInformation
		$responsableEleve = new ResponsableEleve();
		$responsableEleve->setPersId('TestPersId');
		return $responsableEleve;
	}

	public static function getResponsableEleveAdresse() {
		//Creation d'une fiche ResponsableInformation
		$responsableEleveAdresse = new ResponsableEleveAdresse();
		$responsableEleveAdresse->setAdrId('TestAdrId');
		return $responsableEleveAdresse;
	}
}

?>
