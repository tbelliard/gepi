<?php


/**
 * This class adds structure of 'edt_cours' table to 'gepi' DatabaseMap object.
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
class EdtEmplacementCoursMapBuilder implements MapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.EdtEmplacementCoursMapBuilder';

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
		$this->dbMap = Propel::getDatabaseMap(EdtEmplacementCoursPeer::DATABASE_NAME);

		$tMap = $this->dbMap->addTable(EdtEmplacementCoursPeer::TABLE_NAME);
		$tMap->setPhpName('EdtEmplacementCours');
		$tMap->setClassname('EdtEmplacementCours');

		$tMap->setUseIdGenerator(false);

		$tMap->addPrimaryKey('ID_COURS', 'IdCours', 'INTEGER', true, 3);

		$tMap->addForeignKey('ID_GROUPE', 'IdGroupe', 'INTEGER', 'groupes', 'ID', false, 10);

		$tMap->addForeignKey('ID_AID', 'IdAid', 'INTEGER', 'aid', 'ID', true, 10);

		$tMap->addForeignKey('ID_SALLE', 'IdSalle', 'INTEGER', 'salle_cours', 'ID_SALLE', true, 10);

		$tMap->addColumn('JOUR_SEMAINE', 'JourSemaine', 'VARCHAR', true, 10);

		$tMap->addForeignKey('ID_DEFINIE_PERIODE', 'IdDefiniePeriode', 'VARCHAR', 'edt_creneaux', 'ID_DEFINIE_PERIODE', true, 3);

		$tMap->addColumn('DUREE', 'Duree', 'VARCHAR', true, 10);

		$tMap->addColumn('HEUREDEB_DEC', 'HeuredebDec', 'VARCHAR', true, 3);

		$tMap->addColumn('ID_SEMAINE', 'IdSemaine', 'VARCHAR', true, 3);

		$tMap->addForeignKey('ID_CALENDRIER', 'IdCalendrier', 'VARCHAR', 'edt_calendrier', 'ID_CALENDRIER', true, 3);

		$tMap->addColumn('MODIF_EDT', 'ModifEdt', 'VARCHAR', true, 3);

		$tMap->addForeignKey('LOGIN_PROF', 'LoginProf', 'VARCHAR', 'utilisateurs', 'LOGIN', true, 50);

	} // doBuild()

} // EdtEmplacementCoursMapBuilder
