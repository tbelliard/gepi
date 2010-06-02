<?php



/**
 * This class defines the structure of the 'j_traitements_envois' table.
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
class JTraitementEnvoiEleveTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.JTraitementEnvoiEleveTableMap';

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
		$this->setName('j_traitements_envois');
		$this->setPhpName('JTraitementEnvoiEleve');
		$this->setClassname('JTraitementEnvoiEleve');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(false);
		// columns
		$this->addForeignPrimaryKey('A_ENVOI_ID', 'AEnvoiId', 'INTEGER' , 'a_envois', 'ID', true, 12, null);
		$this->addForeignPrimaryKey('A_TRAITEMENT_ID', 'ATraitementId', 'INTEGER' , 'a_traitements', 'ID', true, 12, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('AbsenceEleveEnvoi', 'AbsenceEleveEnvoi', RelationMap::MANY_TO_ONE, array('a_envoi_id' => 'id', ), 'CASCADE', null);
    $this->addRelation('AbsenceEleveTraitement', 'AbsenceEleveTraitement', RelationMap::MANY_TO_ONE, array('a_traitement_id' => 'id', ), 'CASCADE', null);
	} // buildRelations()

} // JTraitementEnvoiEleveTableMap
