<?php



/**
 * This class defines the structure of the 'j_eleves_regime' table.
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
class EleveRegimeDoublantTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.EleveRegimeDoublantTableMap';

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
		$this->setName('j_eleves_regime');
		$this->setPhpName('EleveRegimeDoublant');
		$this->setClassname('EleveRegimeDoublant');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(false);
		// columns
		$this->addForeignPrimaryKey('LOGIN', 'Login', 'VARCHAR' , 'eleves', 'LOGIN', true, 50, null);
		$this->addColumn('DOUBLANT', 'Doublant', 'CHAR', true, 1, null);
		$this->addColumn('REGIME', 'Regime', 'CHAR', true, 5, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('Eleve', 'Eleve', RelationMap::MANY_TO_ONE, array('login' => 'login', ), 'CASCADE', null);
	} // buildRelations()

} // EleveRegimeDoublantTableMap
