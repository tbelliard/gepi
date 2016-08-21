<?php



/**
 * This class defines the structure of the 'j_eleves_groupes' table.
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
class JEleveGroupeTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.JEleveGroupeTableMap';

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
		$this->setName('j_eleves_groupes');
		$this->setPhpName('JEleveGroupe');
		$this->setClassname('JEleveGroupe');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(false);
		// columns
		$this->addForeignPrimaryKey('LOGIN', 'Login', 'VARCHAR' , 'eleves', 'LOGIN', true, 50, '');
		$this->addForeignPrimaryKey('ID_GROUPE', 'IdGroupe', 'INTEGER' , 'groupes', 'ID', true, 11, 0);
		$this->addPrimaryKey('PERIODE', 'Periode', 'INTEGER', true, 11, 0);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('Eleve', 'Eleve', RelationMap::MANY_TO_ONE, array('login' => 'login', ), 'CASCADE', null);
		$this->addRelation('Groupe', 'Groupe', RelationMap::MANY_TO_ONE, array('id_groupe' => 'id', ), 'CASCADE', null);
	} // buildRelations()

} // JEleveGroupeTableMap
