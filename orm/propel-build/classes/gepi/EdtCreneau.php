<?php


/**
 * Skeleton subclass for representing a row from the 'edt_creneaux' table.
 *
 * Table contenant les creneaux de chaque journee (M1, M2...S1, S2...)
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class EdtCreneau extends BaseEdtCreneau {

	/**
	 *
	 * Renvoi le creneau suivant du type donné
	 *
	 * @return     EdtCreneau EdtCreneau
	 *
	 */
	public function getNextEdtCreneau($type_creneau = null) {
		throw new PropelException("Pas encore implemente");
		return new EdtCreneau();
	}

	/**
	 *
	 * Renvoi le creneau precedent du type donné
	 *
	 * @return     EdtCreneau EdtCreneau
	 *
	 */
	public function getPrevEdtCreneau($type_creneau = null) {
		throw new PropelException("Pas encore implemente");
		return new EdtCreneau();
	}

	/**
	 *
	 * Renvoi la liste de cours associes a un professeur sur ce creneau
	 *
	 * @return     PropelObjectCollection EdtEmplacementCours
	 *
	 */
	public function getEdtEmplacementCours($utilisateur_professionnel_id) {
		throw new PropelException("Pas encore implemente");
		return new PropelObjectCollection();
	}

} // EdtCreneau
