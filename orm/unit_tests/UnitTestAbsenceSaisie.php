<?php

class UnitTestAbsenceSaise  {

	public static function getAbsenceSaisie() {
		//Creation d'une absence
		$absenceSaisie = new AbsenceEleveSaisie();
                $absenceSaisie->setDebutAbs(new DateTime('2010-01-01 00:00'));
                $absenceSaisie->setFinAbs(new DateTime('2010-01-01 12:00'));
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
