<?php


/**
 * This class adds structure of 'edt_creneaux' table to 'gepi' DatabaseMap object.
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
class EdtCreneauMapBuilder implements MapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.EdtCreneauMapBuilder';

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
		$this->dbMap = Propel::getDatabaseMap(EdtCreneauPeer::DATABASE_NAME);

		$tMap = $this->dbMap->addTable(EdtCreneauPeer::TABLE_NAME);
		$tMap->setPhpName('EdtCreneau');
		$tMap->setClassname('EdtCreneau');

		$tMap->setUseIdGenerator(true);

		$tMap->addPrimaryKey('ID_DEFINIE_PERIODE', 'IdDefiniePeriode', 'INTEGER', true, 11);

		$tMap->addColumn('NOM_DEFINIE_PERIODE', 'NomDefiniePeriode', 'VARCHAR', true, 50);

		$tMap->addColumn('HEUREDEBUT_DEFINIE_PERIODE', 'HeuredebutDefiniePeriode', 'TIME', true, null);

		$tMap->addColumn('HEUREFIN_DEFINIE_PERIODE', 'HeurefinDefiniePeriode', 'TIME', true, null);

		$tMap->addColumn('SUIVI_DEFINIE_PERIODE', 'SuiviDefiniePeriode', 'INTEGER', false, 2);

		$tMap->addColumn('TYPE_CRENEAU', 'TypeCreneau', 'VARCHAR', false, 15);

		$tMap->addColumn('JOUR_CRENEAU', 'JourCreneau', 'VARCHAR', true, 20);

	} // doBuild()

} // EdtCreneauMapBuilder
