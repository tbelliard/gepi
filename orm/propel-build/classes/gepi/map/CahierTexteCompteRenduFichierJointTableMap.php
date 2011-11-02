<?php



/**
 * This class defines the structure of the 'ct_documents' table.
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
class CahierTexteCompteRenduFichierJointTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.CahierTexteCompteRenduFichierJointTableMap';

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
		$this->setName('ct_documents');
		$this->setPhpName('CahierTexteCompteRenduFichierJoint');
		$this->setClassname('CahierTexteCompteRenduFichierJoint');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addForeignKey('ID_CT', 'IdCt', 'INTEGER', 'ct_entry', 'ID_CT', true, null, 0);
		$this->addColumn('TITRE', 'Titre', 'VARCHAR', true, 255, null);
		$this->addColumn('TAILLE', 'Taille', 'INTEGER', true, null, 0);
		$this->addColumn('EMPLACEMENT', 'Emplacement', 'VARCHAR', true, 255, null);
		$this->addColumn('VISIBLE_ELEVE_PARENT', 'VisibleEleveParent', 'BOOLEAN', false, null, true);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('CahierTexteCompteRendu', 'CahierTexteCompteRendu', RelationMap::MANY_TO_ONE, array('id_ct' => 'id_ct', ), 'CASCADE', null);
	} // buildRelations()

} // CahierTexteCompteRenduFichierJointTableMap
