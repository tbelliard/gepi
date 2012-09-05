<?php



/**
 * This class defines the structure of the 'plugins_autorisations' table.
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
class PlugInAutorisationTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.PlugInAutorisationTableMap';

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
		$this->setName('plugins_autorisations');
		$this->setPhpName('PlugInAutorisation');
		$this->setClassname('PlugInAutorisation');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 11, null);
		$this->addForeignKey('PLUGIN_ID', 'PluginId', 'INTEGER', 'plugins', 'ID', true, 11, null);
		$this->addColumn('FICHIER', 'Fichier', 'VARCHAR', true, 100, null);
		$this->addColumn('USER_STATUT', 'UserStatut', 'VARCHAR', true, 50, null);
		$this->addColumn('AUTH', 'Auth', 'CHAR', true, 1, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('PlugIn', 'PlugIn', RelationMap::MANY_TO_ONE, array('plugin_id' => 'id', ), 'CASCADE', null);
	} // buildRelations()

} // PlugInAutorisationTableMap
