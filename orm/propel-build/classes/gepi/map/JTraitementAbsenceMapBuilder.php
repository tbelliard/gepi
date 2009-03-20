<?php


/**
 * This class adds structure of 'j_traitement_absence' table to 'gepi' DatabaseMap object.
 *
 *
 *
 * These statically-built map classes are used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    gepi.map
 */
class JTraitementAbsenceMapBuilder implements MapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.JTraitementAbsenceMapBuilder';

	/**
	 * The database map.
	 */
	private $dbMap;

	/**
	 * Tells us if this DatabaseMapBuilder is built so that we
	 * don't have to re-build it every time.
	 *
	 * @return     boolean true if this DatabaseMapBuilder is built, false otherwise.
	 */
	public function isBuilt()
	{
		return ($this->dbMap !== null);
	}

	/**
	 * Gets the databasemap this map builder built.
	 *
	 * @return     the databasemap
	 */
	public function getDatabaseMap()
	{
		return $this->dbMap;
	}

	/**
	 * The doBuild() method builds the DatabaseMap
	 *
	 * @return     void
	 * @throws     PropelException
	 */
	public function doBuild()
	{
		$this->dbMap = Propel::getDatabaseMap(JTraitementAbsencePeer::DATABASE_NAME);

		$tMap = $this->dbMap->addTable(JTraitementAbsencePeer::TABLE_NAME);
		$tMap->setPhpName('JTraitementAbsence');
		$tMap->setClassname('JTraitementAbsence');

		$tMap->setUseIdGenerator(false);

		$tMap->addForeignPrimaryKey('A_ABSENCE_ID', 'AAbsenceId', 'INTEGER' , 'a_absences', 'ID', true, 12);

		$tMap->addForeignPrimaryKey('A_TRAITEMENT_ID', 'ATraitementId', 'INTEGER' , 'a_traitements', 'ID', true, 12);

	} // doBuild()

} // JTraitementAbsenceMapBuilder
