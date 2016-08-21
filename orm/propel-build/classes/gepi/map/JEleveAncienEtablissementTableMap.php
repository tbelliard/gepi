<?php



/**
 * This class defines the structure of the 'j_eleves_etablissements' table.
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
class JEleveAncienEtablissementTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.JEleveAncienEtablissementTableMap';

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
		$this->setName('j_eleves_etablissements');
		$this->setPhpName('JEleveAncienEtablissement');
		$this->setClassname('JEleveAncienEtablissement');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(false);
		$this->setIsCrossRef(true);
		// columns
		$this->addForeignPrimaryKey('ID_ELEVE', 'IdEleve', 'VARCHAR' , 'eleves', 'ID_ELEVE', true, 50, null);
		$this->addForeignPrimaryKey('ID_ETABLISSEMENT', 'IdEtablissement', 'VARCHAR' , 'etablissements', 'ID', true, 8, '');
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('Eleve', 'Eleve', RelationMap::MANY_TO_ONE, array('id_eleve' => 'id_eleve', ), 'CASCADE', null);
		$this->addRelation('AncienEtablissement', 'AncienEtablissement', RelationMap::MANY_TO_ONE, array('id_etablissement' => 'id', ), 'CASCADE', null);
	} // buildRelations()

} // JEleveAncienEtablissementTableMap
