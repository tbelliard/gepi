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
		$responsableEleve->setAdresseId('TestPersId');
		return $responsableEleve;
	}

	public static function getAdresse() {
		//Creation d'une fiche ResponsableInformation
		$Adresse = new Adresse();
		$Adresse->setAdresseId('TestAdrId');
		return $Adresse;
	}
}

?>
