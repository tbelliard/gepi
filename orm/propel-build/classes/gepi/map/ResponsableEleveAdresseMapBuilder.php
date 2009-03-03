<?php


/**
 * This class adds structure of 'resp_adr' table to 'gepi' DatabaseMap object.
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
class ResponsableEleveAdresseMapBuilder implements MapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.ResponsableEleveAdresseMapBuilder';

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
		$this->dbMap = Propel::getDatabaseMap(ResponsableEleveAdressePeer::DATABASE_NAME);

		$tMap = $this->dbMap->addTable(ResponsableEleveAdressePeer::TABLE_NAME);
		$tMap->setPhpName('ResponsableEleveAdresse');
		$tMap->setClassname('ResponsableEleveAdresse');

		$tMap->setUseIdGenerator(false);

		$tMap->addPrimaryKey('ADR_ID', 'AdrId', 'VARCHAR', true, 10);

		$tMap->addColumn('ADR1', 'Adr1', 'VARCHAR', true, 100);

		$tMap->addColumn('ADR2', 'Adr2', 'VARCHAR', true, 100);

		$tMap->addColumn('ADR3', 'Adr3', 'VARCHAR', true, 100);

		$tMap->addColumn('ADR4', 'Adr4', 'VARCHAR', true, 100);

		$tMap->addColumn('CP', 'Cp', 'VARCHAR', true, 6);

		$tMap->addColumn('PAYS', 'Pays', 'VARCHAR', true, 50);

		$tMap->addColumn('COMMUNE', 'Commune', 'VARCHAR', true, 50);

	} // doBuild()

} // ResponsableEleveAdresseMapBuilder
