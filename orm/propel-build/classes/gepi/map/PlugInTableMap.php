<?php



/**
 * This class defines the structure of the 'plugins' table.
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
class PlugInTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.PlugInTableMap';

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
		$this->setName('plugins');
		$this->setPhpName('PlugIn');
		$this->setClassname('PlugIn');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 11, null);
		$this->addColumn('NOM', 'Nom', 'VARCHAR', true, 100, null);
		$this->addColumn('REPERTOIRE', 'Repertoire', 'VARCHAR', true, 255, null);
		$this->addColumn('DESCRIPTION', 'Description', 'CLOB', true, null, null);
		$this->addColumn('OUVERT', 'Ouvert', 'CHAR', true, 1, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('PlugInAutorisation', 'PlugInAutorisation', RelationMap::ONE_TO_MANY, array('id' => 'plugin_id', ), 'CASCADE', null, 'PlugInAutorisations');
		$this->addRelation('PlugInMiseEnOeuvreMenu', 'PlugInMiseEnOeuvreMenu', RelationMap::ONE_TO_MANY, array('id' => 'plugin_id', ), 'CASCADE', null, 'PlugInMiseEnOeuvreMenus');
	} // buildRelations()

} // PlugInTableMap
