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
	function compareEdtEmplacementCours($a, $b) {
		throw new PropelException("Pas encore implemente");
		return 0;
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

