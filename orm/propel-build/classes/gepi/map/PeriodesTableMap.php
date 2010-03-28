<?php


/**
 * This class defines the structure of the 'periodes' table.
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
class PeriodesTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.PeriodesTableMap';

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
		$this->setName('periodes');
		$this->setPhpName('Periodes');
		$this->setClassname('Periodes');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(false);
		// columns
		$this->addPrimaryKey('NOM_PERIODE', 'NomPeriode', 'VARCHAR', true, 10, null);
		$this->addPrimaryKey('NUM_PERIODE', 'NumPeriode', 'INTEGER', true, 10, null);
		$this->addColumn('VEROUILLER', 'Verouiller', 'VARCHAR', true, 1, 'O');
		$this->addForeignPrimaryKey('ID_CLASSE', 'IdClasse', 'INTEGER' , 'classes', 'ID', true, 10, null);
		$this->addColumn('DATE_VERROUILLAGE', 'DateVerrouillage', 'TIME', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('Classe', 'Classe', RelationMap::MANY_TO_ONE, array('id_classe' => 'id', ), 'CASCADE', null);
	} // buildRelations()

} // PeriodesTableMap
