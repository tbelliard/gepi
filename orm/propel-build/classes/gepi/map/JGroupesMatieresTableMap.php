<?php



/**
 * This class defines the structure of the 'j_groupes_matieres' table.
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
class JGroupesMatieresTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.JGroupesMatieresTableMap';

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
		$this->setName('j_groupes_matieres');
		$this->setPhpName('JGroupesMatieres');
		$this->setClassname('JGroupesMatieres');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(false);
		// columns
		$this->addForeignPrimaryKey('ID_GROUPE', 'IdGroupe', 'INTEGER' , 'groupes', 'ID', true, null, null);
		$this->addForeignPrimaryKey('ID_MATIERE', 'IdMatiere', 'VARCHAR' , 'matieres', 'MATIERE', true, 255, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('Groupe', 'Groupe', RelationMap::MANY_TO_ONE, array('id_groupe' => 'id', ), 'CASCADE', null);
    $this->addRelation('Matiere', 'Matiere', RelationMap::MANY_TO_ONE, array('id_matiere' => 'matiere', ), 'CASCADE', null);
	} // buildRelations()

} // JGroupesMatieresTableMap
