<?php


/**
 * This class adds structure of 'j_eleves_professeurs' table to 'gepi' DatabaseMap object.
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
class JEleveProfesseurPrincipalMapBuilder implements MapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.JEleveProfesseurPrincipalMapBuilder';

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
		$this->dbMap = Propel::getDatabaseMap(JEleveProfesseurPrincipalPeer::DATABASE_NAME);

		$tMap = $this->dbMap->addTable(JEleveProfesseurPrincipalPeer::TABLE_NAME);
		$tMap->setPhpName('JEleveProfesseurPrincipal');
		$tMap->setClassname('JEleveProfesseurPrincipal');

		$tMap->setUseIdGenerator(false);

		$tMap->addForeignPrimaryKey('LOGIN', 'Login', 'VARCHAR' , 'eleves', 'LOGIN', true, 50);

		$tMap->addForeignPrimaryKey('PROFESSEUR', 'Professeur', 'VARCHAR' , 'utilisateurs', 'LOGIN', true, 50);

		$tMap->addForeignPrimaryKey('ID_CLASSE', 'IdClasse', 'INTEGER' , 'classes', 'ID', true, 11);

	} // doBuild()

} // JEleveProfesseurPrincipalMapBuilder
