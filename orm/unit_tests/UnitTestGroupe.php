<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UnitTestGroupe
 *
 * @author joss
 */
class UnitTestGroupe {
	public static function getGroupe() {
		//Creation d'un groupe
		$groupe = new Groupe();
		$groupe->setName('UnitTestGroupe');
		return $groupe;
	}
}
?>
