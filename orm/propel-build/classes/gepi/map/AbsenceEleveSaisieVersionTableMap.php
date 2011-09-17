<?php



/**
 * This class defines the structure of the 'a_saisies_version' table.
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
class AbsenceEleveSaisieVersionTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.AbsenceEleveSaisieVersionTableMap';

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
		$this->setName('a_saisies_version');
		$this->setPhpName('AbsenceEleveSaisieVersion');
		$this->setClassname('AbsenceEleveSaisieVersion');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(false);
		// columns
		$this->addForeignPrimaryKey('ID', 'Id', 'INTEGER' , 'a_saisies', 'ID', true, 11, null);
		$this->addColumn('UTILISATEUR_ID', 'UtilisateurId', 'VARCHAR', false, 100, null);
		$this->addColumn('ELEVE_ID', 'EleveId', 'INTEGER', false, 11, null);
		$this->addColumn('COMMENTAIRE', 'Commentaire', 'LONGVARCHAR', false, null, null);
		$this->addColumn('DEBUT_ABS', 'DebutAbs', 'TIMESTAMP', false, null, null);
		$this->addColumn('FIN_ABS', 'FinAbs', 'TIMESTAMP', false, null, null);
		$this->addColumn('ID_EDT_CRENEAU', 'IdEdtCreneau', 'INTEGER', false, 12, null);
		$this->addColumn('ID_EDT_EMPLACEMENT_COURS', 'IdEdtEmplacementCours', 'INTEGER', false, 12, null);
		$this->addColumn('ID_GROUPE', 'IdGroupe', 'INTEGER', false, null, null);
		$this->addColumn('ID_CLASSE', 'IdClasse', 'INTEGER', false, null, null);
		$this->addColumn('ID_AID', 'IdAid', 'INTEGER', false, null, null);
		$this->addColumn('ID_S_INCIDENTS', 'IdSIncidents', 'INTEGER', false, null, null);
		$this->addColumn('ID_LIEU', 'IdLieu', 'INTEGER', false, 11, null);
		$this->addColumn('DELETED_BY', 'DeletedBy', 'VARCHAR', false, 100, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('DELETED_AT', 'DeletedAt', 'TIMESTAMP', false, null, null);
		$this->addPrimaryKey('VERSION', 'Version', 'INTEGER', false, null, 0);
		$this->addColumn('VERSION_CREATED_AT', 'VersionCreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('VERSION_CREATED_BY', 'VersionCreatedBy', 'VARCHAR', false, 100, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('AbsenceEleveSaisie', 'AbsenceEleveSaisie', RelationMap::MANY_TO_ONE, array('id' => 'id', ), 'CASCADE', null);
	} // buildRelations()

} // AbsenceEleveSaisieVersionTableMap
