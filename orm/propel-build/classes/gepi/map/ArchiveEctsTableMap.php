<?php



/**
 * This class defines the structure of the 'archivage_ects' table.
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
class ArchiveEctsTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.ArchiveEctsTableMap';

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
		$this->setName('archivage_ects');
		$this->setPhpName('ArchiveEcts');
		$this->setClassname('ArchiveEcts');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 11, null);
		$this->addColumn('ANNEE', 'Annee', 'VARCHAR', true, 255, null);
		$this->addForeignPrimaryKey('INE', 'Ine', 'VARCHAR' , 'eleves', 'NO_GEP', true, 255, null);
		$this->addColumn('CLASSE', 'Classe', 'VARCHAR', true, 255, null);
		$this->addPrimaryKey('NUM_PERIODE', 'NumPeriode', 'INTEGER', true, 11, null);
		$this->addColumn('NOM_PERIODE', 'NomPeriode', 'VARCHAR', true, 255, null);
		$this->addPrimaryKey('SPECIAL', 'Special', 'VARCHAR', true, 255, null);
		$this->addColumn('MATIERE', 'Matiere', 'VARCHAR', false, 255, null);
		$this->addColumn('PROFS', 'Profs', 'VARCHAR', false, 255, null);
		$this->addColumn('VALEUR', 'Valeur', 'DECIMAL', true, null, null);
		$this->addColumn('MENTION', 'Mention', 'VARCHAR', true, 255, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('Eleve', 'Eleve', RelationMap::MANY_TO_ONE, array('ine' => 'no_gep', ), 'CASCADE', null);
	} // buildRelations()

} // ArchiveEctsTableMap
