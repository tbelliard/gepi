<?php

class UnitTestEleve  {

	public static function getEleve() {
		//Creation d'un eleve
		$eleve = new Eleve();
		$eleve->setLogin('UnitTestEleve');
		$eleve->setEleId('TestScoId');
		return $eleve;
	}
}
?>
