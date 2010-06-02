<?php



/**
 * This class defines the structure of the 'plugins_menus' table.
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
class PlugInMiseEnOeuvreMenuTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.PlugInMiseEnOeuvreMenuTableMap';

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
		$this->setName('plugins_menus');
		$this->setPhpName('PlugInMiseEnOeuvreMenu');
		$this->setClassname('PlugInMiseEnOeuvreMenu');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 11, null);
		$this->addForeignKey('PLUGIN_ID', 'PluginId', 'INTEGER', 'plugins', 'ID', true, 11, null);
		$this->addColumn('USER_STATUT', 'UserStatut', 'VARCHAR', true, 50, null);
		$this->addColumn('TITRE_ITEM', 'TitreItem', 'VARCHAR', true, 255, null);
		$this->addColumn('LIEN_ITEM', 'LienItem', 'VARCHAR', true, 255, null);
		$this->addColumn('DESCRIPTION_ITEM', 'DescriptionItem', 'VARCHAR', true, 255, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('PlugIn', 'PlugIn', RelationMap::MANY_TO_ONE, array('plugin_id' => 'id', ), 'CASCADE', null);
	} // buildRelations()

} // PlugInMiseEnOeuvreMenuTableMap
