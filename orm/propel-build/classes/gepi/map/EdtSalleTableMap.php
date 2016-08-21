<?php



/**
 * This class defines the structure of the 'salle_cours' table.
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
class EdtSalleTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.EdtSalleTableMap';

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
		$this->setName('salle_cours');
		$this->setPhpName('EdtSalle');
		$this->setClassname('EdtSalle');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(false);
		// columns
		$this->addPrimaryKey('ID_SALLE', 'IdSalle', 'INTEGER', true, 3, null);
		$this->addColumn('NUMERO_SALLE', 'NumeroSalle', 'VARCHAR', true, 10, null);
		$this->addColumn('NOM_SALLE', 'NomSalle', 'VARCHAR', false, 50, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('EdtEmplacementCours', 'EdtEmplacementCours', RelationMap::ONE_TO_MANY, array('id_salle' => 'id_salle', ), 'SET NULL', null, 'EdtEmplacementCourss');
	} // buildRelations()

} // EdtSalleTableMap
