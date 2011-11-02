<?php



/**
 * This class defines the structure of the 'j_aid_eleves' table.
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
class JAidElevesTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.JAidElevesTableMap';

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
		$this->setName('j_aid_eleves');
		$this->setPhpName('JAidEleves');
		$this->setClassname('JAidEleves');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(false);
		// columns
		$this->addForeignPrimaryKey('ID_AID', 'IdAid', 'VARCHAR' , 'aid', 'ID', true, 100, null);
		$this->addForeignPrimaryKey('LOGIN', 'Login', 'VARCHAR' , 'eleves', 'LOGIN', true, 60, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('AidDetails', 'AidDetails', RelationMap::MANY_TO_ONE, array('id_aid' => 'id', ), 'CASCADE', null);
		$this->addRelation('Eleve', 'Eleve', RelationMap::MANY_TO_ONE, array('login' => 'login', ), 'CASCADE', null);
	} // buildRelations()

} // JAidElevesTableMap
