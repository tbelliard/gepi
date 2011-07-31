<?php



/**
 * This class defines the structure of the 'matieres_categories' table.
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
class CategorieMatiereTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.CategorieMatiereTableMap';

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
		$this->setName('matieres_categories');
		$this->setPhpName('CategorieMatiere');
		$this->setClassname('CategorieMatiere');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 11, null);
		$this->addColumn('NOM_COURT', 'NomCourt', 'VARCHAR', true, 255, null);
		$this->addColumn('NOM_COMPLET', 'NomComplet', 'VARCHAR', true, 255, null);
		$this->addColumn('PRIORITY', 'Priority', 'INTEGER', true, 6, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('JGroupesClasses', 'JGroupesClasses', RelationMap::ONE_TO_MANY, array('id' => 'categorie_id', ), null, null, 'JGroupesClassess');
		$this->addRelation('Matiere', 'Matiere', RelationMap::ONE_TO_MANY, array('id' => 'categorie_id', ), null, null, 'Matieres');
		$this->addRelation('JCategoriesMatieresClasses', 'JCategoriesMatieresClasses', RelationMap::ONE_TO_MANY, array('id' => 'categorie_id', ), 'CASCADE', null, 'JCategoriesMatieresClassess');
		$this->addRelation('Classe', 'Classe', RelationMap::MANY_TO_MANY, array(), 'CASCADE', null, 'Classes');
	} // buildRelations()

} // CategorieMatiereTableMap
