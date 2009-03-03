<?php


/**
 * This class adds structure of 'j_aid_eleves' table to 'gepi' DatabaseMap object.
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
class JAidElevesMapBuilder implements MapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.JAidElevesMapBuilder';

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
		$this->dbMap = Propel::getDatabaseMap(JAidElevesPeer::DATABASE_NAME);

		$tMap = $this->dbMap->addTable(JAidElevesPeer::TABLE_NAME);
		$tMap->setPhpName('JAidEleves');
		$tMap->setClassname('JAidEleves');

		$tMap->setUseIdGenerator(false);

		$tMap->addForeignKey('ID_AID', 'IdAid', 'VARCHAR', 'aid', 'ID', true, 100);

		$tMap->addForeignPrimaryKey('LOGIN', 'Login', 'VARCHAR' , 'eleves', 'LOGIN', true, 60);

		$tMap->addForeignPrimaryKey('INDICE_AID', 'IndiceAid', 'INTEGER' , 'aid_config', 'INDICE_AID', true, 11);

	} // doBuild()

} // JAidElevesMapBuilder
