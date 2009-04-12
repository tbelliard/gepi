<?php


/**
 * This class adds structure of 'archivage_ects' table to 'gepi' DatabaseMap object.
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
class ArchiveEctsMapBuilder implements MapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.ArchiveEctsMapBuilder';

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
		$this->dbMap = Propel::getDatabaseMap(ArchiveEctsPeer::DATABASE_NAME);

		$tMap = $this->dbMap->addTable(ArchiveEctsPeer::TABLE_NAME);
		$tMap->setPhpName('ArchiveEcts');
		$tMap->setClassname('ArchiveEcts');

		$tMap->setUseIdGenerator(true);

		$tMap->addPrimaryKey('ID', 'Id', 'INTEGER', true, 11);

		$tMap->addColumn('ANNEE', 'Annee', 'VARCHAR', true, 255);

		$tMap->addForeignPrimaryKey('INE', 'Ine', 'VARCHAR' , 'eleves', 'NO_GEP', true, 255);

		$tMap->addColumn('CLASSE', 'Classe', 'VARCHAR', true, 255);

		$tMap->addPrimaryKey('NUM_PERIODE', 'NumPeriode', 'INTEGER', true, 11);

		$tMap->addColumn('NOM_PERIODE', 'NomPeriode', 'VARCHAR', true, 255);

		$tMap->addPrimaryKey('SPECIAL', 'Special', 'VARCHAR', true, 255);

		$tMap->addColumn('MATIERE', 'Matiere', 'VARCHAR', false, 255);

		$tMap->addColumn('PROFS', 'Profs', 'VARCHAR', false, 255);

		$tMap->addColumn('VALEUR', 'Valeur', 'DECIMAL', true, null);

		$tMap->addColumn('MENTION', 'Mention', 'VARCHAR', true, 255);

	} // doBuild()

} // ArchiveEctsMapBuilder
