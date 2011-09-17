<?php



/**
 * This class defines the structure of the 'a_saisies' table.
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
class AbsenceEleveSaisieTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.AbsenceEleveSaisieTableMap';

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
		$this->setName('a_saisies');
		$this->setPhpName('AbsenceEleveSaisie');
		$this->setClassname('AbsenceEleveSaisie');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 11, null);
		$this->addForeignKey('UTILISATEUR_ID', 'UtilisateurId', 'VARCHAR', 'utilisateurs', 'LOGIN', false, 100, null);
		$this->addForeignKey('ELEVE_ID', 'EleveId', 'INTEGER', 'eleves', 'ID_ELEVE', false, 11, null);
		$this->addColumn('COMMENTAIRE', 'Commentaire', 'LONGVARCHAR', false, null, null);
		$this->addColumn('DEBUT_ABS', 'DebutAbs', 'TIMESTAMP', false, null, null);
		$this->addColumn('FIN_ABS', 'FinAbs', 'TIMESTAMP', false, null, null);
		$this->addForeignKey('ID_EDT_CRENEAU', 'IdEdtCreneau', 'INTEGER', 'edt_creneaux', 'ID_DEFINIE_PERIODE', false, 12, null);
		$this->addForeignKey('ID_EDT_EMPLACEMENT_COURS', 'IdEdtEmplacementCours', 'INTEGER', 'edt_cours', 'ID_COURS', false, 12, null);
		$this->addForeignKey('ID_GROUPE', 'IdGroupe', 'INTEGER', 'groupes', 'ID', false, null, null);
		$this->addForeignKey('ID_CLASSE', 'IdClasse', 'INTEGER', 'classes', 'ID', false, null, null);
		$this->addForeignKey('ID_AID', 'IdAid', 'INTEGER', 'aid', 'ID', false, null, null);
		$this->addColumn('ID_S_INCIDENTS', 'IdSIncidents', 'INTEGER', false, null, null);
		$this->addForeignKey('ID_LIEU', 'IdLieu', 'INTEGER', 'a_lieux', 'ID', false, 11, null);
		$this->addColumn('DELETED_BY', 'DeletedBy', 'VARCHAR', false, 100, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('DELETED_AT', 'DeletedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('VERSION', 'Version', 'INTEGER', false, null, 0);
		$this->addColumn('VERSION_CREATED_AT', 'VersionCreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('VERSION_CREATED_BY', 'VersionCreatedBy', 'VARCHAR', false, 100, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('UtilisateurProfessionnel', 'UtilisateurProfessionnel', RelationMap::MANY_TO_ONE, array('utilisateur_id' => 'login', ), null, null);
		$this->addRelation('Eleve', 'Eleve', RelationMap::MANY_TO_ONE, array('eleve_id' => 'id_eleve', ), 'CASCADE', null);
		$this->addRelation('EdtCreneau', 'EdtCreneau', RelationMap::MANY_TO_ONE, array('id_edt_creneau' => 'id_definie_periode', ), 'SET NULL', null);
		$this->addRelation('EdtEmplacementCours', 'EdtEmplacementCours', RelationMap::MANY_TO_ONE, array('id_edt_emplacement_cours' => 'id_cours', ), 'SET NULL', null);
		$this->addRelation('Groupe', 'Groupe', RelationMap::MANY_TO_ONE, array('id_groupe' => 'id', ), 'SET NULL', null);
		$this->addRelation('Classe', 'Classe', RelationMap::MANY_TO_ONE, array('id_classe' => 'id', ), 'SET NULL', null);
		$this->addRelation('AidDetails', 'AidDetails', RelationMap::MANY_TO_ONE, array('id_aid' => 'id', ), 'SET NULL', null);
		$this->addRelation('AbsenceEleveLieu', 'AbsenceEleveLieu', RelationMap::MANY_TO_ONE, array('id_lieu' => 'id', ), 'SET NULL', null);
		$this->addRelation('JTraitementSaisieEleve', 'JTraitementSaisieEleve', RelationMap::ONE_TO_MANY, array('id' => 'a_saisie_id', ), 'CASCADE', null, 'JTraitementSaisieEleves');
		$this->addRelation('AbsenceEleveSaisieVersion', 'AbsenceEleveSaisieVersion', RelationMap::ONE_TO_MANY, array('id' => 'id', ), 'CASCADE', null, 'AbsenceEleveSaisieVersions');
		$this->addRelation('AbsenceEleveTraitement', 'AbsenceEleveTraitement', RelationMap::MANY_TO_MANY, array(), 'CASCADE', null, 'AbsenceEleveTraitements');
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
			'versionable' => array('version_column' => 'version', 'version_table' => '', 'log_created_at' => 'true', 'log_created_by' => 'true', 'log_comment' => 'false', 'version_created_at_column' => 'version_created_at', 'version_created_by_column' => 'version_created_by', 'version_comment_column' => 'version_comment', ),
		);
	} // getBehaviors()

} // AbsenceEleveSaisieTableMap
