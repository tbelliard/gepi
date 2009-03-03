<?php


/**
 * This class adds structure of 'aid_config' table to 'gepi' DatabaseMap object.
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
class AidConfigurationMapBuilder implements MapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.AidConfigurationMapBuilder';

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
		$this->dbMap = Propel::getDatabaseMap(AidConfigurationPeer::DATABASE_NAME);

		$tMap = $this->dbMap->addTable(AidConfigurationPeer::TABLE_NAME);
		$tMap->setPhpName('AidConfiguration');
		$tMap->setClassname('AidConfiguration');

		$tMap->setUseIdGenerator(false);

		$tMap->addColumn('NOM', 'Nom', 'CHAR', true, 100);

		$tMap->addColumn('NOM_COMPLET', 'NomComplet', 'CHAR', true, 100);

		$tMap->addColumn('NOTE_MAX', 'NoteMax', 'INTEGER', true, 11);

		$tMap->addColumn('ORDER_DISPLAY1', 'OrderDisplay1', 'CHAR', true, 1);

		$tMap->addColumn('ORDER_DISPLAY2', 'OrderDisplay2', 'INTEGER', true, 11);

		$tMap->addColumn('TYPE_NOTE', 'TypeNote', 'CHAR', true, 5);

		$tMap->addColumn('DISPLAY_BEGIN', 'DisplayBegin', 'INTEGER', true, 11);

		$tMap->addColumn('DISPLAY_END', 'DisplayEnd', 'INTEGER', true, 11);

		$tMap->addColumn('MESSAGE', 'Message', 'CHAR', true, 20);

		$tMap->addColumn('DISPLAY_NOM', 'DisplayNom', 'CHAR', true, 1);

		$tMap->addPrimaryKey('INDICE_AID', 'IndiceAid', 'INTEGER', true, 11);

		$tMap->addColumn('DISPLAY_BULLETIN', 'DisplayBulletin', 'CHAR', true, 1);

		$tMap->addColumn('BULL_SIMPLIFIE', 'BullSimplifie', 'CHAR', true, 1);

		$tMap->addColumn('OUTILS_COMPLEMENTAIRES', 'OutilsComplementaires', 'CHAR', true, 1);

		$tMap->addColumn('FEUILLE_PRESENCE', 'FeuillePresence', 'CHAR', true, 1);

	} // doBuild()

} // AidConfigurationMapBuilder
