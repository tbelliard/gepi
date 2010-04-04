<?php
/**
 * Description of EdtEmplacementCoursHelper
 *  Classe qui implemente des methodes statiques pour géré un groupe ou un tableau de groupe
 *
 * @author joss
 */
class EdtEmplacementCoursHelper {

 	/**
	 * Compare deux edtEmplacementCours par ordre chronologique
	 *
	 * @param      EdtEmplacementCours $groupeA Le premier EdtEmplacementCours a coparer
	 * @param      EdtEmplacementCours $groupeB Le deuxieme EdtEmplacementCours a comparer
	 * @return     int un entier, qui sera inférieur, égal ou supérieur à zéro suivant que le premier argument est considéré comme plus petit, égal ou plus grand que le second argument.
	 */
	public static function compareEdtEmplacementCours($a, $b) {
		if ($a ==null || $b == null){
		    throw new PropelException("Objet null pour la comparaison.");
		}
		
		// On traduit le nom du jour
		$semaine_declaration["dimanche"] = 1;
		$semaine_declaration["lundi"] = 2;
		$semaine_declaration["mardi"] = 3;
		$semaine_declaration["mercredi"] = 4;
		$semaine_declaration["jeudi"] = 5;
		$semaine_declaration["vendredi"] = 6;
		$semaine_declaration["samedi"] = 7;

		if ($semaine_declaration[$a->getJourSemaine()] != $semaine_declaration[$b->getJourSemaine()]) {
		    $result = ($semaine_declaration[$a->getJourSemaine()] - $semaine_declaration[$b->getJourSemaine()]);
		} elseif ($a->getEdtCreneau()->getIdDefiniePeriode() != $b->getEdtCreneau()->getIdDefiniePeriode())  {
		    $start = strtotime($a->getEdtCreneau()->getHeuredebutDefiniePeriode());
		    $end = strtotime($b->getEdtCreneau()->getHeuredebutDefiniePeriode());
		    $result = ($start-$end);
		} elseif ($a->getHeuredebDec() != $b->getHeuredebDec())  {
		    $result = ($a->getHeuredebDec() - $b->getHeuredebDec());
		} else  {
		    $result = ($a->getDuree() - $b->getDuree());
		}
		return $result;
	}

 	/**
	 *
	 * Classe un tableau de groupe par ordre alphabétique de leur nom (avec les noms de classes d'eleves associée)
	 *
	 * @param      PropelObjectCollection $edtEmplacementCours La collection d'emplacement de cours
	 * @return     PropelObjectCollection $edtEmplacementCours Un collection ordonnés d'emplacement de cours
	 * @throws     PropelException - si les types d'entrées ne sont pas bon.
	 */
	public static function orderChronologically(PropelObjectCollection $edtEmplacementCours) {
		$edtEmplacementCours->uasort(array("EdtEmplacementCoursHelper", "compareEdtEmplacementCours"));
		return $edtEmplacementCours;
	}
}
?>

