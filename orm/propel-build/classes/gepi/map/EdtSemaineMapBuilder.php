<?php


/**
 * This class adds structure of 'edt_semaines' table to 'gepi' DatabaseMap object.
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
class EdtSemaineMapBuilder implements MapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.EdtSemaineMapBuilder';

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
		$this->dbMap = Propel::getDatabaseMap(EdtSemainePeer::DATABASE_NAME);

		$tMap = $this->dbMap->addTable(EdtSemainePeer::TABLE_NAME);
		$tMap->setPhpName('EdtSemaine');
		$tMap->setClassname('EdtSemaine');

		$tMap->setUseIdGenerator(false);

		$tMap->addPrimaryKey('ID_EDT_SEMAINE', 'IdEdtSemaine', 'INTEGER', true, 11);

		$tMap->addColumn('NUM_EDT_SEMAINE', 'NumEdtSemaine', 'INTEGER', true, 11);

		$tMap->addColumn('TYPE_EDT_SEMAINE', 'TypeEdtSemaine', 'VARCHAR', true, 10);

		$tMap->addColumn('NUM_EDT_SEMAINE', 'NumEdtSemaine', 'INTEGER', true, 11);

	} // doBuild()

} // EdtSemaineMapBuilder
