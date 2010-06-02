<?php



/**
 * This class defines the structure of the 'ct_sequences' table.
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
class CahierTexteSequenceTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.CahierTexteSequenceTableMap';

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
		$this->setName('ct_sequences');
		$this->setPhpName('CahierTexteSequence');
		$this->setClassname('CahierTexteSequence');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('TITRE', 'Titre', 'VARCHAR', true, 255, null);
		$this->addColumn('DESCRIPTION', 'Description', 'CLOB', true, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('CahierTexteCompteRendu', 'CahierTexteCompteRendu', RelationMap::ONE_TO_MANY, array('id' => 'id_sequence', ), 'SET NULL', null);
    $this->addRelation('CahierTexteTravailAFaire', 'CahierTexteTravailAFaire', RelationMap::ONE_TO_MANY, array('id' => 'id_sequence', ), 'SET NULL', null);
    $this->addRelation('CahierTexteNoticePrivee', 'CahierTexteNoticePrivee', RelationMap::ONE_TO_MANY, array('id' => 'id_sequence', ), 'SET NULL', null);
	} // buildRelations()

} // CahierTexteSequenceTableMap
