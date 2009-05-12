<?php


/**
 * This class adds structure of 'j_groupes_classes' table to 'gepi' DatabaseMap object.
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
class JGroupesClassesMapBuilder implements MapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.JGroupesClassesMapBuilder';

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
		$this->dbMap = Propel::getDatabaseMap(JGroupesClassesPeer::DATABASE_NAME);

		$tMap = $this->dbMap->addTable(JGroupesClassesPeer::TABLE_NAME);
		$tMap->setPhpName('JGroupesClasses');
		$tMap->setClassname('JGroupesClasses');

		$tMap->setUseIdGenerator(false);

		$tMap->addForeignPrimaryKey('ID_GROUPE', 'IdGroupe', 'INTEGER' , 'groupes', 'ID', true, null);

		$tMap->addForeignPrimaryKey('ID_CLASSE', 'IdClasse', 'INTEGER' , 'classes', 'ID', true, null);

		$tMap->addColumn('PRIORITE', 'Priorite', 'SMALLINT', true, null);

		$tMap->addColumn('COEF', 'Coef', 'DECIMAL', true, null);

		$tMap->addForeignKey('CATEGORIE_ID', 'CategorieId', 'INTEGER', 'matieres_categories', 'ID', true, null);

		$tMap->addColumn('SAISIE_ECTS', 'SaisieEcts', 'BOOLEAN', false, null);

		$tMap->addColumn('VALEUR_ECTS', 'ValeurEcts', 'DECIMAL', false, null);

	} // doBuild()

} // JGroupesClassesMapBuilder
