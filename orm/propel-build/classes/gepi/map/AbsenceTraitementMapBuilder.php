<?php


/**
 * This class adds structure of 'a_traitements' table to 'gepi' DatabaseMap object.
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
class AbsenceTraitementMapBuilder implements MapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.AbsenceTraitementMapBuilder';

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
		$this->dbMap = Propel::getDatabaseMap(AbsenceTraitementPeer::DATABASE_NAME);

		$tMap = $this->dbMap->addTable(AbsenceTraitementPeer::TABLE_NAME);
		$tMap->setPhpName('AbsenceTraitement');
		$tMap->setClassname('AbsenceTraitement');

		$tMap->setUseIdGenerator(true);

		$tMap->addPrimaryKey('ID', 'Id', 'INTEGER', true, 11);

		$tMap->addForeignKey('UTILISATEUR_ID', 'UtilisateurId', 'VARCHAR', 'utilisateurs', 'LOGIN', false, 100);

		$tMap->addColumn('CREATED_ON', 'CreatedOn', 'INTEGER', true, 13);

		$tMap->addColumn('UPDATED_ON', 'UpdatedOn', 'INTEGER', true, 13);

		$tMap->addForeignKey('A_TYPE_ID', 'ATypeId', 'INTEGER', 'a_types', 'ID', false, 4);

		$tMap->addForeignKey('A_MOTIF_ID', 'AMotifId', 'INTEGER', 'a_motifs', 'ID', false, 4);

		$tMap->addForeignKey('A_JUSTIFICATION_ID', 'AJustificationId', 'INTEGER', 'a_justifications', 'ID', false, 4);

		$tMap->addColumn('TEXTE_JUSTIFICATION', 'TexteJustification', 'VARCHAR', true, 250);

		$tMap->addForeignKey('A_ACTION_ID', 'AActionId', 'INTEGER', 'a_actions', 'ID', false, 4);

	} // doBuild()

} // AbsenceTraitementMapBuilder
