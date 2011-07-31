<?php



/**
 * This class defines the structure of the 'resp_adr' table.
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
class ResponsableEleveAdresseTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.ResponsableEleveAdresseTableMap';

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
		$this->setName('resp_adr');
		$this->setPhpName('ResponsableEleveAdresse');
		$this->setClassname('ResponsableEleveAdresse');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(false);
		// columns
		$this->addPrimaryKey('ADR_ID', 'AdrId', 'VARCHAR', true, 10, null);
		$this->addColumn('ADR1', 'Adr1', 'VARCHAR', true, 100, null);
		$this->addColumn('ADR2', 'Adr2', 'VARCHAR', true, 100, null);
		$this->addColumn('ADR3', 'Adr3', 'VARCHAR', true, 100, null);
		$this->addColumn('ADR4', 'Adr4', 'VARCHAR', true, 100, null);
		$this->addColumn('CP', 'Cp', 'VARCHAR', true, 6, null);
		$this->addColumn('PAYS', 'Pays', 'VARCHAR', true, 50, null);
		$this->addColumn('COMMUNE', 'Commune', 'VARCHAR', true, 50, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('ResponsableEleve', 'ResponsableEleve', RelationMap::ONE_TO_MANY, array('adr_id' => 'adr_id', ), 'SET NULL', null, 'ResponsableEleves');
		$this->addRelation('AbsenceEleveNotification', 'AbsenceEleveNotification', RelationMap::ONE_TO_MANY, array('adr_id' => 'adr_id', ), 'SET NULL', null, 'AbsenceEleveNotifications');
	} // buildRelations()

} // ResponsableEleveAdresseTableMap
