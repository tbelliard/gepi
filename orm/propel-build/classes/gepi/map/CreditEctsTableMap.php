<?php



/**
 * This class defines the structure of the 'ects_credits' table.
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
class CreditEctsTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.CreditEctsTableMap';

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
		$this->setName('ects_credits');
		$this->setPhpName('CreditEcts');
		$this->setClassname('CreditEcts');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 11, null);
		$this->addForeignPrimaryKey('ID_ELEVE', 'IdEleve', 'INTEGER' , 'eleves', 'ID_ELEVE', true, 11, null);
		$this->addPrimaryKey('NUM_PERIODE', 'NumPeriode', 'INTEGER', true, 11, null);
		$this->addForeignPrimaryKey('ID_GROUPE', 'IdGroupe', 'INTEGER' , 'groupes', 'ID', true, 11, null);
		$this->addColumn('VALEUR', 'Valeur', 'DECIMAL', false, null, null);
		$this->addColumn('MENTION', 'Mention', 'VARCHAR', false, 255, null);
		$this->addColumn('MENTION_PROF', 'MentionProf', 'VARCHAR', false, 255, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('Eleve', 'Eleve', RelationMap::MANY_TO_ONE, array('id_eleve' => 'id_eleve', ), 'CASCADE', null);
		$this->addRelation('Groupe', 'Groupe', RelationMap::MANY_TO_ONE, array('id_groupe' => 'id', ), 'CASCADE', null);
	} // buildRelations()

} // CreditEctsTableMap
