<?php


/**
 * This class adds structure of 'salle_cours' table to 'gepi' DatabaseMap object.
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
class EdtSalleMapBuilder implements MapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.EdtSalleMapBuilder';

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
		$this->dbMap = Propel::getDatabaseMap(EdtSallePeer::DATABASE_NAME);

		$tMap = $this->dbMap->addTable(EdtSallePeer::TABLE_NAME);
		$tMap->setPhpName('EdtSalle');
		$tMap->setClassname('EdtSalle');

		$tMap->setUseIdGenerator(false);

		$tMap->addPrimaryKey('ID_SALLE', 'IdSalle', 'INTEGER', true, 3);

		$tMap->addColumn('NUMERO_SALLE', 'NumeroSalle', 'VARCHAR', true, 10);

		$tMap->addColumn('NOM_SALLE', 'NomSalle', 'VARCHAR', true, 50);

	} // doBuild()

} // EdtSalleMapBuilder
