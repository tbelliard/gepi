<?php



/**
 * This class defines the structure of the 'a_types_statut' table.
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
class AbsenceEleveTypeStatutAutoriseTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.AbsenceEleveTypeStatutAutoriseTableMap';

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
		$this->setName('a_types_statut');
		$this->setPhpName('AbsenceEleveTypeStatutAutorise');
		$this->setClassname('AbsenceEleveTypeStatutAutorise');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 11, null);
		$this->addForeignKey('ID_A_TYPE', 'IdAType', 'INTEGER', 'a_types', 'ID', true, 11, null);
		$this->addColumn('STATUT', 'Statut', 'VARCHAR', true, 20, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('AbsenceEleveType', 'AbsenceEleveType', RelationMap::MANY_TO_ONE, array('id_a_type' => 'id', ), 'CASCADE', null);
	} // buildRelations()

} // AbsenceEleveTypeStatutAutoriseTableMap
