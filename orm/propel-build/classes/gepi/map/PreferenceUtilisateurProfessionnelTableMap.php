<?php



/**
 * This class defines the structure of the 'preferences' table.
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
class PreferenceUtilisateurProfessionnelTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.PreferenceUtilisateurProfessionnelTableMap';

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
		$this->setName('preferences');
		$this->setPhpName('PreferenceUtilisateurProfessionnel');
		$this->setClassname('PreferenceUtilisateurProfessionnel');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('NAME', 'Name', 'VARCHAR', true, 50, null);
		$this->addColumn('VALUE', 'Value', 'LONGVARCHAR', true, null, null);
		$this->addForeignPrimaryKey('LOGIN', 'Login', 'VARCHAR' , 'utilisateurs', 'LOGIN', true, 50, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('UtilisateurProfessionnel', 'UtilisateurProfessionnel', RelationMap::MANY_TO_ONE, array('login' => 'login', ), 'CASCADE', null);
	} // buildRelations()

} // PreferenceUtilisateurProfessionnelTableMap
