<?php


/**
 * This class adds structure of 'ct_entry' table to 'gepi' DatabaseMap object.
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
class CahierTexteCompteRenduMapBuilder implements MapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.CahierTexteCompteRenduMapBuilder';

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
		$this->dbMap = Propel::getDatabaseMap(CahierTexteCompteRenduPeer::DATABASE_NAME);

		$tMap = $this->dbMap->addTable(CahierTexteCompteRenduPeer::TABLE_NAME);
		$tMap->setPhpName('CahierTexteCompteRendu');
		$tMap->setClassname('CahierTexteCompteRendu');

		$tMap->setUseIdGenerator(true);

		$tMap->addPrimaryKey('ID_CT', 'IdCt', 'INTEGER', true, null);

		$tMap->addColumn('HEURE_ENTRY', 'HeureEntry', 'TIME', true, null);

		$tMap->addColumn('DATE_CT', 'DateCt', 'INTEGER', true, null);

		$tMap->addColumn('CONTENU', 'Contenu', 'LONGVARCHAR', true, null);

		$tMap->addColumn('VISE', 'Vise', 'CHAR', true, null);

		$tMap->addColumn('VISA', 'Visa', 'CHAR', true, null);

		$tMap->addForeignKey('ID_GROUPE', 'IdGroupe', 'INTEGER', 'groupes', 'ID', true, null);

		$tMap->addForeignKey('ID_LOGIN', 'IdLogin', 'VARCHAR', 'utilisateurs', 'LOGIN', false, 32);

		$tMap->addForeignKey('ID_SEQUENCE', 'IdSequence', 'INTEGER', 'ct_sequences', 'ID', false, 5);

	} // doBuild()

} // CahierTexteCompteRenduMapBuilder
