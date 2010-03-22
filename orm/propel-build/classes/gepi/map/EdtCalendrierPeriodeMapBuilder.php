<?php


/**
 * This class adds structure of 'edt_calendrier' table to 'gepi' DatabaseMap object.
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
class EdtCalendrierPeriodeMapBuilder implements MapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.EdtCalendrierPeriodeMapBuilder';

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
		$this->dbMap = Propel::getDatabaseMap(EdtCalendrierPeriodePeer::DATABASE_NAME);

		$tMap = $this->dbMap->addTable(EdtCalendrierPeriodePeer::TABLE_NAME);
		$tMap->setPhpName('EdtCalendrierPeriode');
		$tMap->setClassname('EdtCalendrierPeriode');

		$tMap->setUseIdGenerator(false);

		$tMap->addPrimaryKey('ID_CALENDRIER', 'IdCalendrier', 'INTEGER', true, 11);

		$tMap->addColumn('CLASSE_CONCERNE_CALENDRIER', 'ClasseConcerneCalendrier', 'LONGVARCHAR', true, null);

		$tMap->addColumn('NOM_CALENDRIER', 'NomCalendrier', 'VARCHAR', true, 100);

		$tMap->addColumn('DEBUT_CALENDRIER_TS', 'DebutCalendrierTs', 'VARCHAR', true, 11);

		$tMap->addColumn('FIN_CALENDRIER_TS', 'FinCalendrierTs', 'VARCHAR', true, 11);

		$tMap->addColumn('JOURDEBUT_CALENDRIER', 'JourdebutCalendrier', 'DATE', true, 11);

		$tMap->addColumn('HEUREDEBUT_CALENDRIER', 'HeuredebutCalendrier', 'TIME', true, 11);

		$tMap->addColumn('JOURFIN_CALENDRIER', 'JourfinCalendrier', 'DATE', true, 11);

		$tMap->addColumn('HEUREFIN_CALENDRIER', 'HeurefinCalendrier', 'TIME', true, 11);

		$tMap->addColumn('NUMERO_PERIODE', 'NumeroPeriode', 'TINYINT', true, 4);

		$tMap->addColumn('ETABFERME_CALENDRIER', 'EtabfermeCalendrier', 'TINYINT', true, 4);

		$tMap->addColumn('ETABVACANCES_CALENDRIER', 'EtabvacancesCalendrier', 'TINYINT', true, 4);

	} // doBuild()

} // EdtCalendrierPeriodeMapBuilder
