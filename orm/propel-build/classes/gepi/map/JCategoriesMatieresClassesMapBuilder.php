<?php


/**
 * This class adds structure of 'j_matieres_categories_classes' table to 'gepi' DatabaseMap object.
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
class JCategoriesMatieresClassesMapBuilder implements MapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.JCategoriesMatieresClassesMapBuilder';

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
		$this->dbMap = Propel::getDatabaseMap(JCategoriesMatieresClassesPeer::DATABASE_NAME);

		$tMap = $this->dbMap->addTable(JCategoriesMatieresClassesPeer::TABLE_NAME);
		$tMap->setPhpName('JCategoriesMatieresClasses');
		$tMap->setClassname('JCategoriesMatieresClasses');

		$tMap->setUseIdGenerator(false);

		$tMap->addForeignPrimaryKey('CATEGORIE_ID', 'CategorieId', 'INTEGER' , 'matieres_categories', 'ID', true, 11);

		$tMap->addForeignPrimaryKey('CLASSE_ID', 'ClasseId', 'INTEGER' , 'classes', 'ID', true, 11);

		$tMap->addColumn('AFFICHE_MOYENNE', 'AfficheMoyenne', 'BOOLEAN', false, null);

		$tMap->addColumn('PRIORITY', 'Priority', 'INTEGER', true, 6);

	} // doBuild()

} // JCategoriesMatieresClassesMapBuilder
