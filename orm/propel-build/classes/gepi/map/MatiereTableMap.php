<?php



/**
 * This class defines the structure of the 'matieres' table.
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
class MatiereTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.MatiereTableMap';

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
		$this->setName('matieres');
		$this->setPhpName('Matiere');
		$this->setClassname('Matiere');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(false);
		// columns
		$this->addPrimaryKey('MATIERE', 'Matiere', 'VARCHAR', true, 255, null);
		$this->addColumn('NOM_COMPLET', 'NomComplet', 'VARCHAR', true, 200, null);
		$this->addColumn('PRIORITY', 'Priority', 'INTEGER', true, 6, 0);
		$this->addColumn('MATIERE_AID', 'MatiereAid', 'VARCHAR', false, 1, 'n');
		$this->addColumn('MATIERE_ATELIER', 'MatiereAtelier', 'VARCHAR', false, 1, 'n');
		$this->addForeignKey('CATEGORIE_ID', 'CategorieId', 'INTEGER', 'matieres_categories', 'ID', true, 11, 1);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('CategorieMatiere', 'CategorieMatiere', RelationMap::MANY_TO_ONE, array('categorie_id' => 'id', ), null, null);
    $this->addRelation('JGroupesMatieres', 'JGroupesMatieres', RelationMap::ONE_TO_MANY, array('matiere' => 'id_matiere', ), 'CASCADE', null);
    $this->addRelation('JProfesseursMatieres', 'JProfesseursMatieres', RelationMap::ONE_TO_MANY, array('matiere' => 'id_matiere', ), null, null);
    $this->addRelation('Groupe', 'Groupe', RelationMap::MANY_TO_MANY, array(), null, null);
    $this->addRelation('Professeur', 'UtilisateurProfessionnel', RelationMap::MANY_TO_MANY, array(), null, null);
	} // buildRelations()

} // MatiereTableMap
