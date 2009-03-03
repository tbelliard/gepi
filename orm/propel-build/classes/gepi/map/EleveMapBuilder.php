<?php


/**
 * This class adds structure of 'eleves' table to 'gepi' DatabaseMap object.
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
class EleveMapBuilder implements MapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.EleveMapBuilder';

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
		$this->dbMap = Propel::getDatabaseMap(ElevePeer::DATABASE_NAME);

		$tMap = $this->dbMap->addTable(ElevePeer::TABLE_NAME);
		$tMap->setPhpName('Eleve');
		$tMap->setClassname('Eleve');

		$tMap->setUseIdGenerator(true);

		$tMap->addColumn('NO_GEP', 'NoGep', 'VARCHAR', true, 50);

		$tMap->addColumn('LOGIN', 'Login', 'VARCHAR', true, 50);

		$tMap->addColumn('NOM', 'Nom', 'VARCHAR', true, 50);

		$tMap->addColumn('PRENOM', 'Prenom', 'VARCHAR', true, 50);

		$tMap->addColumn('SEXE', 'Sexe', 'VARCHAR', true, 1);

		$tMap->addColumn('NAISSANCE', 'Naissance', 'DATE', true, null);

		$tMap->addColumn('LIEU_NAISSANCE', 'LieuNaissance', 'VARCHAR', true, 50);

		$tMap->addColumn('ELENOET', 'Elenoet', 'VARCHAR', true, 50);

		$tMap->addColumn('ERENO', 'Ereno', 'VARCHAR', true, 50);

		$tMap->addColumn('ELE_ID', 'EleId', 'VARCHAR', true, 10);

		$tMap->addColumn('EMAIL', 'Email', 'VARCHAR', true, 255);

		$tMap->addPrimaryKey('ID_ELEVE', 'IdEleve', 'INTEGER', true, 11);

	} // doBuild()

} // EleveMapBuilder
