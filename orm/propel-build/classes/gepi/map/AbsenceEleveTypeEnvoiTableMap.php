<?php


/**
 * This class defines the structure of the 'a_type_envois' table.
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
class AbsenceEleveTypeEnvoiTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.AbsenceEleveTypeEnvoiTableMap';

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
		$this->setName('a_type_envois');
		$this->setPhpName('AbsenceEleveTypeEnvoi');
		$this->setClassname('AbsenceEleveTypeEnvoi');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 11, null);
		$this->addColumn('NOM', 'Nom', 'VARCHAR', true, 100, null);
		$this->addColumn('ORDRE_AFFICHAGE', 'OrdreAffichage', 'INTEGER', true, 4, null);
		$this->addColumn('CONTENU', 'Contenu', 'CLOB', true, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('AbsenceEleveEnvoi', 'AbsenceEleveEnvoi', RelationMap::ONE_TO_MANY, array('id' => 'id_type_envoi', ), 'SET NULL', null);
	} // buildRelations()

} // AbsenceEleveTypeEnvoiTableMap
