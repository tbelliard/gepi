<?php


/**
 * This class adds structure of 'a_types_statut' table to 'gepi' DatabaseMap object.
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
class AbsenceEleveTypeStatutMapBuilder implements MapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.AbsenceEleveTypeStatutMapBuilder';

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
		$this->dbMap = Propel::getDatabaseMap(AbsenceEleveTypeStatutPeer::DATABASE_NAME);

		$tMap = $this->dbMap->addTable(AbsenceEleveTypeStatutPeer::TABLE_NAME);
		$tMap->setPhpName('AbsenceEleveTypeStatut');
		$tMap->setClassname('AbsenceEleveTypeStatut');

		$tMap->setUseIdGenerator(true);

		$tMap->addPrimaryKey('ID', 'Id', 'INTEGER', true, 11);

		$tMap->addForeignKey('ID_A_TYPE', 'IdAType', 'INTEGER', 'a_types', 'ID', true, 11);

		$tMap->addColumn('STATUT', 'Statut', 'VARCHAR', true, 20);

	} // doBuild()

} // AbsenceEleveTypeStatutMapBuilder
