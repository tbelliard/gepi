<?php



/**
 * This class defines the structure of the 'a_justifications' table.
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
class AbsenceEleveJustificationTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.AbsenceEleveJustificationTableMap';

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
		$this->setName('a_justifications');
		$this->setPhpName('AbsenceEleveJustification');
		$this->setClassname('AbsenceEleveJustification');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 11, null);
		$this->addColumn('NOM', 'Nom', 'VARCHAR', true, 250, null);
		$this->addColumn('COMMENTAIRE', 'Commentaire', 'LONGVARCHAR', false, null, null);
		$this->addColumn('SORTABLE_RANK', 'SortableRank', 'INTEGER', false, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('AbsenceEleveTraitement', 'AbsenceEleveTraitement', RelationMap::ONE_TO_MANY, array('id' => 'a_justification_id', ), 'SET NULL', null, 'AbsenceEleveTraitements');
	} // buildRelations()

	/**
	 *
	 * Gets the list of behaviors registered for this table
	 *
	 * @return array Associative array (name => parameters) of behaviors
	 */
	public function getBehaviors()
	{
		return array(
			'sortable' => array('rank_column' => 'sortable_rank', 'use_scope' => 'false', 'scope_column' => 'sortable_scope', ),
			'timestampable' => array('create_column' => 'created_at', 'update_column' => 'updated_at', ),
		);
	} // getBehaviors()

} // AbsenceEleveJustificationTableMap
