<?php



/**
 * Skeleton subclass for performing query and update operations on the 'a_traitements' table.
 *
 * Un traitement peut gerer plusieurs saisies et consiste à definir les motifs/justifications... de ces absences saisies
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class AbsenceEleveTraitementPeer extends BaseAbsenceEleveTraitementPeer {

	/**
	 * Est-ce qu'on met à jour la table d'agrégation
	 */
	static $isAgregationEnabled = true;
	
	/**
	 * Checks whether agrégation is enabled
	 *
	 * @return boolean
	 */
	public static function isAgregationEnabled()
	{
		return self::$isAgregationEnabled;
	}
	
	/**
	 * Enables agrégation
	 */
	public static function enableAgregation()
	{
		self::$isAgregationEnabled = true;
	}
	
	/**
	 * Disables agrégation
	 */
	public static function disableAgregation()
	{
		self::$isAgregationEnabled = false;
	}
} // AbsenceEleveTraitementPeer
