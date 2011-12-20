<?php



/**
 * This class defines the structure of the 'j_matieres_categories_classes' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.gepi.map
 */
class JCategoriesMatieresClassesTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.JCategoriesMatieresClassesTableMap';

	/**
	 * Initialize the table attributes, columns and validators
	 * Relations are not initialized by this method since they are lazy loaded
	 *
	 * @return     void
	 * @throws     PropelException
	 */
	public function initialize()
	{
		// attributes
		$this->setName('j_matieres_categories_classes');
		$this->setPhpName('JCategoriesMatieresClasses');
		$this->setClassname('JCategoriesMatieresClasses');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(false);
		$this->setIsCrossRef(true);
		// columns
		$this->addForeignPrimaryKey('CATEGORIE_ID', 'CategorieId', 'INTEGER' , 'matieres_categories', 'ID', true, 11, null);
		$this->addForeignPrimaryKey('CLASSE_ID', 'ClasseId', 'INTEGER' , 'classes', 'ID', true, 11, null);
		$this->addColumn('AFFICHE_MOYENNE', 'AfficheMoyenne', 'BOOLEAN', false, 1, false);
		$this->addColumn('PRIORITY', 'Priority', 'INTEGER', true, 6, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('CategorieMatiere', 'CategorieMatiere', RelationMap::MANY_TO_ONE, array('categorie_id' => 'id', ), 'CASCADE', null);
		$this->addRelation('Classe', 'Classe', RelationMap::MANY_TO_ONE, array('classe_id' => 'id', ), 'CASCADE', null);
	} // buildRelations()

} // JCategoriesMatieresClassesTableMap
