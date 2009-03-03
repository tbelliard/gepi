<?php

class UnitTestAbsenceSaise  {

	public static function getAbsenceSaisie() {
		//Creation d'une absence
		$absenceSaisie = new AbsenceSaisie();
		return $absenceSaisie;
	}

	public static function getAbsenceTraitement() {
		//Creation d'une absence
		$absenceTraitement = new AbsenceTraitement();
		$absenceTraitement->setTexteJustification('UnitTestTraitementAbsence');
		return $absenceTraitement;
	}

}
?>
