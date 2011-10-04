<?php



/**
 * This class defines the structure of the 'j_eleves_professeurs' table.
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
class JEleveProfesseurPrincipalTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.JEleveProfesseurPrincipalTableMap';

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
		$this->setName('j_eleves_professeurs');
		$this->setPhpName('JEleveProfesseurPrincipal');
		$this->setClassname('JEleveProfesseurPrincipal');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(false);
		// columns
		$this->addForeignPrimaryKey('LOGIN', 'Login', 'VARCHAR' , 'eleves', 'LOGIN', true, 50, null);
		$this->addForeignPrimaryKey('PROFESSEUR', 'Professeur', 'VARCHAR' , 'utilisateurs', 'LOGIN', true, 50, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('Eleve', 'Eleve', RelationMap::MANY_TO_ONE, array('login' => 'login', ), 'CASCADE', null);
		$this->addRelation('UtilisateurProfessionnel', 'UtilisateurProfessionnel', RelationMap::MANY_TO_ONE, array('professeur' => 'login', ), 'CASCADE', null);
	} // buildRelations()

} // JEleveProfesseurPrincipalTableMap
