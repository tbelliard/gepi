<?php



/**
 * This class defines the structure of the 'mef' table.
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
class MefTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.MefTableMap';

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
		$this->setName('mef');
		$this->setPhpName('Mef');
		$this->setClassname('Mef');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('MEF_CODE', 'MefCode', 'INTEGER', false, null, null);
		$this->addColumn('LIBELLE_COURT', 'LibelleCourt', 'VARCHAR', true, 50, null);
		$this->addColumn('LIBELLE_LONG', 'LibelleLong', 'VARCHAR', true, 300, null);
		$this->addColumn('LIBELLE_EDITION', 'LibelleEdition', 'VARCHAR', true, 300, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('Eleve', 'Eleve', RelationMap::ONE_TO_MANY, array('mef_code' => 'mef_code', ), 'SET NULL', null, 'Eleves');
	} // buildRelations()

} // MefTableMap
