<?php


/**
 * This class adds structure of 'j_eleves_etablissements' table to 'gepi' DatabaseMap object.
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
class JEleveAncienEtablissementMapBuilder implements MapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.JEleveAncienEtablissementMapBuilder';

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
		$this->dbMap = Propel::getDatabaseMap(JEleveAncienEtablissementPeer::DATABASE_NAME);

		$tMap = $this->dbMap->addTable(JEleveAncienEtablissementPeer::TABLE_NAME);
		$tMap->setPhpName('JEleveAncienEtablissement');
		$tMap->setClassname('JEleveAncienEtablissement');

		$tMap->setUseIdGenerator(false);

		$tMap->addForeignPrimaryKey('ID_ELEVE', 'IdEleve', 'VARCHAR' , 'eleves', 'ID_ELEVE', true, 50);

		$tMap->addForeignPrimaryKey('ID_ETABLISSEMENT', 'IdEtablissement', 'VARCHAR' , 'etablissements', 'ID', true, 8);

	} // doBuild()

} // JEleveAncienEtablissementMapBuilder
