<?php



/**
 * This class defines the structure of the 'edt_semaines' table.
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
class EdtSemaineTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.EdtSemaineTableMap';

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
		$this->setName('edt_semaines');
		$this->setPhpName('EdtSemaine');
		$this->setClassname('EdtSemaine');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(false);
		// columns
		$this->addPrimaryKey('ID_EDT_SEMAINE', 'IdEdtSemaine', 'INTEGER', true, 10, null);
		$this->addColumn('NUM_EDT_SEMAINE', 'NumEdtSemaine', 'INTEGER', true, 10, null);
		$this->addColumn('TYPE_EDT_SEMAINE', 'TypeEdtSemaine', 'VARCHAR', false, 10, null);
		$this->addColumn('NUM_SEMAINES_ETAB', 'NumSemainesEtab', 'INTEGER', false, 10, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // EdtSemaineTableMap
