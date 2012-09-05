<?php



/**
 * This class defines the structure of the 'aid_config' table.
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
class AidConfigurationTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.AidConfigurationTableMap';

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
		$this->setName('aid_config');
		$this->setPhpName('AidConfiguration');
		$this->setClassname('AidConfiguration');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(false);
		// columns
		$this->addColumn('NOM', 'Nom', 'CHAR', true, 100, '');
		$this->addColumn('NOM_COMPLET', 'NomComplet', 'CHAR', true, 100, '');
		$this->addColumn('NOTE_MAX', 'NoteMax', 'INTEGER', true, 11, 0);
		$this->addColumn('ORDER_DISPLAY1', 'OrderDisplay1', 'CHAR', true, 1, '0');
		$this->addColumn('ORDER_DISPLAY2', 'OrderDisplay2', 'INTEGER', true, 11, 0);
		$this->addColumn('TYPE_NOTE', 'TypeNote', 'CHAR', true, 5, '');
		$this->addColumn('DISPLAY_BEGIN', 'DisplayBegin', 'INTEGER', true, 11, 0);
		$this->addColumn('DISPLAY_END', 'DisplayEnd', 'INTEGER', true, 11, 0);
		$this->addColumn('MESSAGE', 'Message', 'CHAR', true, 20, '');
		$this->addColumn('DISPLAY_NOM', 'DisplayNom', 'CHAR', true, 1, '');
		$this->addPrimaryKey('INDICE_AID', 'IndiceAid', 'INTEGER', true, 11, 0);
		$this->addColumn('DISPLAY_BULLETIN', 'DisplayBulletin', 'CHAR', true, 1, 'y');
		$this->addColumn('BULL_SIMPLIFIE', 'BullSimplifie', 'CHAR', true, 1, 'y');
		$this->addColumn('OUTILS_COMPLEMENTAIRES', 'OutilsComplementaires', 'CHAR', true, 1, 'n');
		$this->addColumn('FEUILLE_PRESENCE', 'FeuillePresence', 'CHAR', true, 1, 'n');
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('AidDetails', 'AidDetails', RelationMap::ONE_TO_MANY, array('indice_aid' => 'indice_aid', ), 'CASCADE', null, 'AidDetailss');
	} // buildRelations()

} // AidConfigurationTableMap
