<?php


/**
 * This class adds structure of 'plugins_autorisations' table to 'gepi' DatabaseMap object.
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
class PlugInAutorisationMapBuilder implements MapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.PlugInAutorisationMapBuilder';

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
		$this->dbMap = Propel::getDatabaseMap(PlugInAutorisationPeer::DATABASE_NAME);

		$tMap = $this->dbMap->addTable(PlugInAutorisationPeer::TABLE_NAME);
		$tMap->setPhpName('PlugInAutorisation');
		$tMap->setClassname('PlugInAutorisation');

		$tMap->setUseIdGenerator(true);

		$tMap->addPrimaryKey('ID', 'Id', 'INTEGER', true, 11);

		$tMap->addForeignKey('PLUGIN_ID', 'PluginId', 'INTEGER', 'plugins', 'ID', true, 11);

		$tMap->addColumn('FICHIER', 'Fichier', 'VARCHAR', true, 100);

		$tMap->addColumn('USER_STATUT', 'UserStatut', 'VARCHAR', true, 50);

		$tMap->addColumn('AUTH', 'Auth', 'CHAR', true, 1);

	} // doBuild()

} // PlugInAutorisationMapBuilder
