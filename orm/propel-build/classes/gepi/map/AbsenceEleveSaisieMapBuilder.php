<?php


/**
 * This class adds structure of 'a_saisies' table to 'gepi' DatabaseMap object.
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
class AbsenceEleveSaisieMapBuilder implements MapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.AbsenceEleveSaisieMapBuilder';

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
		$this->dbMap = Propel::getDatabaseMap(AbsenceEleveSaisiePeer::DATABASE_NAME);

		$tMap = $this->dbMap->addTable(AbsenceEleveSaisiePeer::TABLE_NAME);
		$tMap->setPhpName('AbsenceEleveSaisie');
		$tMap->setClassname('AbsenceEleveSaisie');

		$tMap->setUseIdGenerator(true);

		$tMap->addPrimaryKey('ID', 'Id', 'INTEGER', true, 11);

		$tMap->addForeignKey('UTILISATEUR_ID', 'UtilisateurId', 'VARCHAR', 'utilisateurs', 'LOGIN', false, 100);

		$tMap->addForeignKey('ELEVE_ID', 'EleveId', 'INTEGER', 'eleves', 'ID_ELEVE', false, 4);

		$tMap->addColumn('COMMENTAIRE', 'Commentaire', 'LONGVARCHAR', false, null);

		$tMap->addColumn('DEBUT_ABS', 'DebutAbs', 'TIME', false, null);

		$tMap->addColumn('FIN_ABS', 'FinAbs', 'TIME', false, null);

		$tMap->addForeignKey('ID_EDT_CRENEAU', 'IdEdtCreneau', 'INTEGER', 'edt_creneaux', 'ID_DEFINIE_PERIODE', false, 12);

		$tMap->addForeignKey('ID_EDT_EMPLACEMENT_COURS', 'IdEdtEmplacementCours', 'INTEGER', 'edt_cours', 'ID_COURS', false, 12);

	} // doBuild()

} // AbsenceEleveSaisieMapBuilder
