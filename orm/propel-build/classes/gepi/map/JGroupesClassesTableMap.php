<?php



/**
 * This class defines the structure of the 'j_groupes_classes' table.
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
class JGroupesClassesTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.JGroupesClassesTableMap';

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
		$this->setName('j_groupes_classes');
		$this->setPhpName('JGroupesClasses');
		$this->setClassname('JGroupesClasses');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(false);
		// columns
		$this->addForeignPrimaryKey('ID_GROUPE', 'IdGroupe', 'INTEGER' , 'groupes', 'ID', true, null, null);
		$this->addForeignPrimaryKey('ID_CLASSE', 'IdClasse', 'INTEGER' , 'classes', 'ID', true, null, null);
		$this->addColumn('PRIORITE', 'Priorite', 'SMALLINT', true, null, null);
		$this->addColumn('COEF', 'Coef', 'DECIMAL', true, null, null);
		$this->addForeignKey('CATEGORIE_ID', 'CategorieId', 'INTEGER', 'matieres_categories', 'ID', true, null, null);
		$this->addColumn('SAISIE_ECTS', 'SaisieEcts', 'BOOLEAN', false, null, false);
		$this->addColumn('VALEUR_ECTS', 'ValeurEcts', 'DECIMAL', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('Groupe', 'Groupe', RelationMap::MANY_TO_ONE, array('id_groupe' => 'id', ), 'CASCADE', null);
    $this->addRelation('Classe', 'Classe', RelationMap::MANY_TO_ONE, array('id_classe' => 'id', ), 'CASCADE', null);
    $this->addRelation('CategorieMatiere', 'CategorieMatiere', RelationMap::MANY_TO_ONE, array('categorie_id' => 'id', ), null, null);
	} // buildRelations()

} // JGroupesClassesTableMap
