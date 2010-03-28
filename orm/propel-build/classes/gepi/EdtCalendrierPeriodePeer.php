<?php


/**
 * Skeleton subclass for performing query and update operations on the 'edt_calendrier' table.
 *
 * Liste des periodes datees de l'annee courante(pour definir par exemple les trimestres)
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class EdtCalendrierPeriodePeer extends BaseEdtCalendrierPeriodePeer {

 	/**
	 * Retrourne la periode actuelle, ou null si aucun periode n'est trouvé pour le jours actuel
	 *
	 * @return     int un entier, qui sera inférieur, égal ou supérieur à zéro suivant que le premier argument est considéré comme plus petit, égal ou plus grand que le second argument.
	 */
	function retrieveEdtCalendrierPeriodeActuelle() {
		throw new PropelException("Pas encore implemente");
		return new EdtCalendrierPeriode();
	}


} // EdtCalendrierPeriodePeer
