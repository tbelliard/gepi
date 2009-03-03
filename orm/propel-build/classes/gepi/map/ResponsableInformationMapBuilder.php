<?php


/**
 * This class adds structure of 'responsables2' table to 'gepi' DatabaseMap object.
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
class ResponsableInformationMapBuilder implements MapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.ResponsableInformationMapBuilder';

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
		$this->dbMap = Propel::getDatabaseMap(ResponsableInformationPeer::DATABASE_NAME);

		$tMap = $this->dbMap->addTable(ResponsableInformationPeer::TABLE_NAME);
		$tMap->setPhpName('ResponsableInformation');
		$tMap->setClassname('ResponsableInformation');

		$tMap->setUseIdGenerator(false);

		$tMap->addForeignPrimaryKey('ELE_ID', 'EleId', 'VARCHAR' , 'eleves', 'ELE_ID', true, 10);

		$tMap->addForeignKey('PERS_ID', 'PersId', 'VARCHAR', 'resp_pers', 'PERS_ID', true, 10);

		$tMap->addPrimaryKey('RESP_LEGAL', 'RespLegal', 'VARCHAR', true, 1);

		$tMap->addColumn('PERS_CONTACT', 'PersContact', 'VARCHAR', true, 1);

	} // doBuild()

} // ResponsableInformationMapBuilder
