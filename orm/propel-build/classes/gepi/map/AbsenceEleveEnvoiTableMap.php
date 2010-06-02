<?php



/**
 * This class defines the structure of the 'a_envois' table.
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
class AbsenceEleveEnvoiTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.AbsenceEleveEnvoiTableMap';

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
		$this->setName('a_envois');
		$this->setPhpName('AbsenceEleveEnvoi');
		$this->setClassname('AbsenceEleveEnvoi');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 11, null);
		$this->addForeignKey('UTILISATEUR_ID', 'UtilisateurId', 'VARCHAR', 'utilisateurs', 'LOGIN', false, 100, '-1');
		$this->addForeignKey('ID_TYPE_ENVOI', 'IdTypeEnvoi', 'INTEGER', 'a_type_envois', 'ID', true, 4, -1);
		$this->addColumn('COMMENTAIRE', 'Commentaire', 'LONGVARCHAR', false, null, null);
		$this->addColumn('STATUT_ENVOI', 'StatutEnvoi', 'VARCHAR', false, 20, '0');
		$this->addColumn('DATE_ENVOI', 'DateEnvoi', 'TIMESTAMP', false, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('UtilisateurProfessionnel', 'UtilisateurProfessionnel', RelationMap::MANY_TO_ONE, array('utilisateur_id' => 'login', ), 'SET NULL', null);
    $this->addRelation('AbsenceEleveTypeEnvoi', 'AbsenceEleveTypeEnvoi', RelationMap::MANY_TO_ONE, array('id_type_envoi' => 'id', ), 'SET NULL', null);
    $this->addRelation('JTraitementEnvoiEleve', 'JTraitementEnvoiEleve', RelationMap::ONE_TO_MANY, array('id' => 'a_envoi_id', ), 'CASCADE', null);
    $this->addRelation('AbsenceEleveTraitement', 'AbsenceEleveTraitement', RelationMap::MANY_TO_MANY, array(), 'CASCADE', null);
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
			'timestampable' => array('create_column' => 'created_at', 'update_column' => 'updated_at', ),
		);
	} // getBehaviors()

} // AbsenceEleveEnvoiTableMap
