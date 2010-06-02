<?php



/**
 * This class defines the structure of the 'j_eleves_cpe' table.
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
class JEleveCpeTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.JEleveCpeTableMap';

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
		$this->setName('j_eleves_cpe');
		$this->setPhpName('JEleveCpe');
		$this->setClassname('JEleveCpe');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(false);
		// columns
		$this->addForeignPrimaryKey('E_LOGIN', 'ELogin', 'VARCHAR' , 'eleves', 'LOGIN', true, 50, '');
		$this->addForeignPrimaryKey('CPE_LOGIN', 'CpeLogin', 'VARCHAR' , 'utilisateurs', 'LOGIN', true, 50, '');
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('Eleve', 'Eleve', RelationMap::MANY_TO_ONE, array('e_login' => 'login', ), 'CASCADE', null);
    $this->addRelation('UtilisateurProfessionnel', 'UtilisateurProfessionnel', RelationMap::MANY_TO_ONE, array('cpe_login' => 'login', ), 'CASCADE', null);
	} // buildRelations()

} // JEleveCpeTableMap
