<?php


/**
 * This class adds structure of 'resp_pers' table to 'gepi' DatabaseMap object.
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
class ResponsableEleveMapBuilder implements MapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.ResponsableEleveMapBuilder';

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
		$this->dbMap = Propel::getDatabaseMap(ResponsableElevePeer::DATABASE_NAME);

		$tMap = $this->dbMap->addTable(ResponsableElevePeer::TABLE_NAME);
		$tMap->setPhpName('ResponsableEleve');
		$tMap->setClassname('ResponsableEleve');

		$tMap->setUseIdGenerator(false);

		$tMap->addPrimaryKey('PERS_ID', 'PersId', 'VARCHAR', true, 10);

		$tMap->addColumn('LOGIN', 'Login', 'VARCHAR', true, 50);

		$tMap->addColumn('NOM', 'Nom', 'VARCHAR', true, 30);

		$tMap->addColumn('PRENOM', 'Prenom', 'VARCHAR', true, 30);

		$tMap->addColumn('CIVILITE', 'Civilite', 'VARCHAR', true, 5);

		$tMap->addColumn('TEL_PERS', 'TelPers', 'VARCHAR', true, 255);

		$tMap->addColumn('TEL_PORT', 'TelPort', 'VARCHAR', true, 255);

		$tMap->addColumn('TEL_PROF', 'TelProf', 'VARCHAR', true, 255);

		$tMap->addColumn('MEL', 'Mel', 'VARCHAR', true, 100);

		$tMap->addForeignKey('ADR_ID', 'AdrId', 'VARCHAR', 'resp_adr', 'ADR_ID', false, 10);

	} // doBuild()

} // ResponsableEleveMapBuilder
