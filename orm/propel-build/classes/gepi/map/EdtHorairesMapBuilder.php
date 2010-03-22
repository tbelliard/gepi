<?php


/**
 * This class adds structure of 'horaires_etablissement' table to 'gepi' DatabaseMap object.
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
class EdtHorairesMapBuilder implements MapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.EdtHorairesMapBuilder';

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
		$this->dbMap = Propel::getDatabaseMap(EdtHorairesPeer::DATABASE_NAME);

		$tMap = $this->dbMap->addTable(EdtHorairesPeer::TABLE_NAME);
		$tMap->setPhpName('EdtHoraires');
		$tMap->setClassname('EdtHoraires');

		$tMap->setUseIdGenerator(true);

		$tMap->addPrimaryKey('ID_HORAIRE_ETABLISSEMENT', 'IdHoraireEtablissement', 'INTEGER', true, 11);

		$tMap->addColumn('DATE_HORAIRE_ETABLISSEMENT', 'DateHoraireEtablissement', 'DATE', false, null);

		$tMap->addColumn('JOUR_HORAIRE_ETABLISSEMENT', 'JourHoraireEtablissement', 'VARCHAR', true, 15);

		$tMap->addColumn('OUVERTURE_HORAIRE_ETABLISSEMENT', 'OuvertureHoraireEtablissement', 'TIME', true, null);

		$tMap->addColumn('FERMETURE_HORAIRE_ETABLISSEMENT', 'FermetureHoraireEtablissement', 'TIME', true, null);

		$tMap->addColumn('PAUSE_HORAIRE_ETABLISSEMENT', 'PauseHoraireEtablissement', 'TIME', true, 15);

		$tMap->addColumn('OUVERT_HORAIRE_ETABLISSEMENT', 'OuvertHoraireEtablissement', 'BOOLEAN', true, null);

	} // doBuild()

} // EdtHorairesMapBuilder
