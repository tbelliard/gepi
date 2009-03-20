<?php


/**
 * This class adds structure of 'a_absences' table to 'gepi' DatabaseMap object.
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
class AbsenceAbsenceMapBuilder implements MapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.AbsenceAbsenceMapBuilder';

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
		$this->dbMap = Propel::getDatabaseMap(AbsenceAbsencePeer::DATABASE_NAME);

		$tMap = $this->dbMap->addTable(AbsenceAbsencePeer::TABLE_NAME);
		$tMap->setPhpName('AbsenceAbsence');
		$tMap->setClassname('AbsenceAbsence');

		$tMap->setUseIdGenerator(true);

		$tMap->addPrimaryKey('ID', 'Id', 'INTEGER', true, 11);

		$tMap->addForeignKey('UTILISATEUR_ID', 'UtilisateurId', 'VARCHAR', 'utilisateurs', 'LOGIN', false, 100);

		$tMap->addColumn('CREATED_ON', 'CreatedOn', 'INTEGER', true, 13);

		$tMap->addColumn('UPDATED_ON', 'UpdatedOn', 'INTEGER', true, 13);

		$tMap->addColumn('DEBUT_ABS', 'DebutAbs', 'INTEGER', true, 12);

		$tMap->addColumn('FIN_ABS', 'FinAbs', 'INTEGER', true, 12);

	} // doBuild()

} // AbsenceAbsenceMapBuilder
