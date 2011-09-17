<?php



/**
 * This class defines the structure of the 'a_traitements' table.
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
class AbsenceEleveTraitementTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.AbsenceEleveTraitementTableMap';

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
		$this->setName('a_traitements');
		$this->setPhpName('AbsenceEleveTraitement');
		$this->setClassname('AbsenceEleveTraitement');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 11, null);
		$this->addForeignKey('UTILISATEUR_ID', 'UtilisateurId', 'VARCHAR', 'utilisateurs', 'LOGIN', false, 100, null);
		$this->addForeignKey('A_TYPE_ID', 'ATypeId', 'INTEGER', 'a_types', 'ID', false, 4, null);
		$this->addForeignKey('A_MOTIF_ID', 'AMotifId', 'INTEGER', 'a_motifs', 'ID', false, 4, null);
		$this->addForeignKey('A_JUSTIFICATION_ID', 'AJustificationId', 'INTEGER', 'a_justifications', 'ID', false, 4, null);
		$this->addColumn('COMMENTAIRE', 'Commentaire', 'LONGVARCHAR', false, null, null);
		$this->addForeignKey('MODIFIE_PAR_UTILISATEUR_ID', 'ModifieParUtilisateurId', 'VARCHAR', 'utilisateurs', 'LOGIN', false, 100, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('DELETED_AT', 'DeletedAt', 'TIMESTAMP', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('UtilisateurProfessionnel', 'UtilisateurProfessionnel', RelationMap::MANY_TO_ONE, array('utilisateur_id' => 'login', ), null, null);
		$this->addRelation('AbsenceEleveType', 'AbsenceEleveType', RelationMap::MANY_TO_ONE, array('a_type_id' => 'id', ), 'SET NULL', null);
		$this->addRelation('AbsenceEleveMotif', 'AbsenceEleveMotif', RelationMap::MANY_TO_ONE, array('a_motif_id' => 'id', ), 'SET NULL', null);
		$this->addRelation('AbsenceEleveJustification', 'AbsenceEleveJustification', RelationMap::MANY_TO_ONE, array('a_justification_id' => 'id', ), 'SET NULL', null);
		$this->addRelation('ModifieParUtilisateur', 'UtilisateurProfessionnel', RelationMap::MANY_TO_ONE, array('modifie_par_utilisateur_id' => 'login', ), null, null);
		$this->addRelation('JTraitementSaisieEleve', 'JTraitementSaisieEleve', RelationMap::ONE_TO_MANY, array('id' => 'a_traitement_id', ), 'CASCADE', null, 'JTraitementSaisieEleves');
		$this->addRelation('AbsenceEleveNotification', 'AbsenceEleveNotification', RelationMap::ONE_TO_MANY, array('id' => 'a_traitement_id', ), 'CASCADE', null, 'AbsenceEleveNotifications');
		$this->addRelation('AbsenceEleveSaisie', 'AbsenceEleveSaisie', RelationMap::MANY_TO_MANY, array(), 'CASCADE', null, 'AbsenceEleveSaisies');
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
			'soft_delete' => array('deleted_column' => 'deleted_at', ),
		);
	} // getBehaviors()

} // AbsenceEleveTraitementTableMap
