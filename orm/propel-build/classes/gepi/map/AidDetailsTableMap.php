<?php



/**
 * This class defines the structure of the 'aid' table.
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
class AidDetailsTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.AidDetailsTableMap';

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
		$this->setName('aid');
		$this->setPhpName('AidDetails');
		$this->setClassname('AidDetails');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(false);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'VARCHAR', true, 100, null);
		$this->addColumn('NOM', 'Nom', 'VARCHAR', true, 100, '');
		$this->addColumn('NUMERO', 'Numero', 'VARCHAR', true, 8, '0');
		$this->addForeignKey('INDICE_AID', 'IndiceAid', 'INTEGER', 'aid_config', 'INDICE_AID', true, 11, 0);
		$this->addColumn('PERSO1', 'Perso1', 'VARCHAR', true, 255, null);
		$this->addColumn('PERSO2', 'Perso2', 'VARCHAR', true, 255, null);
		$this->addColumn('PERSO3', 'Perso3', 'VARCHAR', true, 255, null);
		$this->addColumn('PRODUCTIONS', 'Productions', 'VARCHAR', true, 100, null);
		$this->addColumn('RESUME', 'Resume', 'LONGVARCHAR', true, null, null);
		$this->addColumn('FAMILLE', 'Famille', 'SMALLINT', true, 6, null);
		$this->addColumn('MOTS_CLES', 'MotsCles', 'VARCHAR', true, 255, null);
		$this->addColumn('ADRESSE1', 'Adresse1', 'VARCHAR', true, 255, null);
		$this->addColumn('ADRESSE2', 'Adresse2', 'VARCHAR', true, 255, null);
		$this->addColumn('PUBLIC_DESTINATAIRE', 'PublicDestinataire', 'VARCHAR', true, 50, null);
		$this->addColumn('CONTACTS', 'Contacts', 'LONGVARCHAR', true, null, null);
		$this->addColumn('DIVERS', 'Divers', 'LONGVARCHAR', true, null, null);
		$this->addColumn('MATIERE1', 'Matiere1', 'VARCHAR', true, 100, null);
		$this->addColumn('MATIERE2', 'Matiere2', 'VARCHAR', true, 100, null);
		$this->addColumn('ELEVE_PEUT_MODIFIER', 'ElevePeutModifier', 'CHAR', true, 1, 'n');
		$this->addColumn('PROF_PEUT_MODIFIER', 'ProfPeutModifier', 'CHAR', true, 1, 'n');
		$this->addColumn('CPE_PEUT_MODIFIER', 'CpePeutModifier', 'CHAR', true, 1, 'n');
		$this->addColumn('FICHE_PUBLIQUE', 'FichePublique', 'CHAR', true, 1, 'n');
		$this->addColumn('AFFICHE_ADRESSE1', 'AfficheAdresse1', 'CHAR', true, 1, 'n');
		$this->addColumn('EN_CONSTRUCTION', 'EnConstruction', 'CHAR', true, 1, 'n');
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('AidConfiguration', 'AidConfiguration', RelationMap::MANY_TO_ONE, array('indice_aid' => 'indice_aid', ), 'CASCADE', null);
		$this->addRelation('JAidUtilisateursProfessionnels', 'JAidUtilisateursProfessionnels', RelationMap::ONE_TO_MANY, array('id' => 'id_aid', ), 'CASCADE', null, 'JAidUtilisateursProfessionnelss');
		$this->addRelation('JAidEleves', 'JAidEleves', RelationMap::ONE_TO_MANY, array('id' => 'id_aid', ), 'CASCADE', null, 'JAidElevess');
		$this->addRelation('AbsenceEleveSaisie', 'AbsenceEleveSaisie', RelationMap::ONE_TO_MANY, array('id' => 'id_aid', ), 'SET NULL', null, 'AbsenceEleveSaisies');
		$this->addRelation('EdtEmplacementCours', 'EdtEmplacementCours', RelationMap::ONE_TO_MANY, array('id' => 'id_aid', ), 'CASCADE', null, 'EdtEmplacementCourss');
		$this->addRelation('UtilisateurProfessionnel', 'UtilisateurProfessionnel', RelationMap::MANY_TO_MANY, array(), 'CASCADE', null, 'UtilisateurProfessionnels');
		$this->addRelation('Eleve', 'Eleve', RelationMap::MANY_TO_MANY, array(), 'CASCADE', null, 'Eleves');
	} // buildRelations()

} // AidDetailsTableMap
