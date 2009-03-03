<?php


/**
 * This class adds structure of 'j_aid_utilisateurs' table to 'gepi' DatabaseMap object.
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
class JAidUtilisateursProfessionnelsMapBuilder implements MapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.JAidUtilisateursProfessionnelsMapBuilder';

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
		$this->dbMap = Propel::getDatabaseMap(JAidUtilisateursProfessionnelsPeer::DATABASE_NAME);

		$tMap = $this->dbMap->addTable(JAidUtilisateursProfessionnelsPeer::TABLE_NAME);
		$tMap->setPhpName('JAidUtilisateursProfessionnels');
		$tMap->setClassname('JAidUtilisateursProfessionnels');

		$tMap->setUseIdGenerator(false);

		$tMap->addForeignPrimaryKey('ID_AID', 'IdAid', 'VARCHAR' , 'aid', 'ID', true, 100);

		$tMap->addForeignKey('ID_UTILISATEUR', 'IdUtilisateur', 'VARCHAR', 'utilisateurs', 'LOGIN', true, 100);

		$tMap->addForeignPrimaryKey('INDICE_AID', 'IndiceAid', 'INTEGER' , 'aid_config', 'INDICE_AID', true, 11);

	} // doBuild()

} // JAidUtilisateursProfessionnelsMapBuilder
