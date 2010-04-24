<?php

class UnitTestAbsenceSaise  {

	public static function getAbsenceSaisie() {
		//Creation d'une absence
		$absenceSaisie = new AbsenceEleveSaisie();
		return $absenceSaisie;
	}

	public static function getAbsenceTraitement() {
		//Creation d'une absence
		$absenceTraitement = new AbsenceEleveTraitement();
		$absenceTraitement->setCommentaire('UnitTestTraitementAbsence');
		return $absenceTraitement;
	}

}
?>
