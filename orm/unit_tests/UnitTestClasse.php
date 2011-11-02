<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UnitTestClasse
 *
 * @author joss
 */
class UnitTestClasse {
	public static function getClasse() {
		//Creation d'un groupe
		$classe = new Classe();
		$classe->setNom('UnitTestClasse');
		return $classe;
	}
}
?>
