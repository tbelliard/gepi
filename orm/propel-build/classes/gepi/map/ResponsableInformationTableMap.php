<?php



/**
 * This class defines the structure of the 'responsables2' table.
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
class ResponsableInformationTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.ResponsableInformationTableMap';

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
		$this->setName('responsables2');
		$this->setPhpName('ResponsableInformation');
		$this->setClassname('ResponsableInformation');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(false);
		// columns
		$this->addForeignPrimaryKey('ELE_ID', 'EleId', 'VARCHAR' , 'eleves', 'ELE_ID', true, 10, null);
		$this->addForeignKey('PERS_ID', 'PersId', 'VARCHAR', 'resp_pers', 'PERS_ID', true, 10, null);
		$this->addPrimaryKey('RESP_LEGAL', 'RespLegal', 'VARCHAR', true, 1, null);
		$this->addColumn('PERS_CONTACT', 'PersContact', 'VARCHAR', true, 1, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('Eleve', 'Eleve', RelationMap::MANY_TO_ONE, array('ele_id' => 'ele_id', ), 'CASCADE', null);
		$this->addRelation('ResponsableEleve', 'ResponsableEleve', RelationMap::MANY_TO_ONE, array('pers_id' => 'pers_id', ), 'CASCADE', null);
	} // buildRelations()

} // ResponsableInformationTableMap
