<?php


/**
 * This class adds structure of 'etablissements' table to 'gepi' DatabaseMap object.
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
class AncienEtablissementMapBuilder implements MapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.AncienEtablissementMapBuilder';

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
		$this->dbMap = Propel::getDatabaseMap(AncienEtablissementPeer::DATABASE_NAME);

		$tMap = $this->dbMap->addTable(AncienEtablissementPeer::TABLE_NAME);
		$tMap->setPhpName('AncienEtablissement');
		$tMap->setClassname('AncienEtablissement');

		$tMap->setUseIdGenerator(true);

		$tMap->addPrimaryKey('ID', 'Id', 'INTEGER', true, 8);

		$tMap->addColumn('NOM', 'Nom', 'VARCHAR', true, 50);

		$tMap->addColumn('NIVEAU', 'Niveau', 'VARCHAR', true, 50);

		$tMap->addColumn('TYPE', 'Type', 'VARCHAR', true, 50);

		$tMap->addColumn('CP', 'Cp', 'INTEGER', true, 10);

		$tMap->addColumn('VILLE', 'Ville', 'VARCHAR', true, 50);

	} // doBuild()

} // AncienEtablissementMapBuilder
