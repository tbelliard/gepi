<?php



/**
 * This class defines the structure of the 'classes' table.
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
class ClasseTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.ClasseTableMap';

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
		$this->setName('classes');
		$this->setPhpName('Classe');
		$this->setClassname('Classe');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('CLASSE', 'Nom', 'VARCHAR', true, 100, null);
		$this->addColumn('NOM_COMPLET', 'NomComplet', 'VARCHAR', true, 100, null);
		$this->addColumn('SUIVI_PAR', 'SuiviPar', 'VARCHAR', true, 50, null);
		$this->addColumn('FORMULE', 'Formule', 'VARCHAR', true, 100, null);
		$this->addColumn('FORMAT_NOM', 'FormatNom', 'VARCHAR', true, 5, null);
		$this->addColumn('DISPLAY_RANG', 'DisplayRang', 'CHAR', true, 1, 'n');
		$this->addColumn('DISPLAY_ADDRESS', 'DisplayAddress', 'CHAR', true, 1, 'n');
		$this->addColumn('DISPLAY_COEF', 'DisplayCoef', 'CHAR', true, 1, 'y');
		$this->addColumn('DISPLAY_MAT_CAT', 'DisplayMatCat', 'CHAR', true, 1, 'n');
		$this->addColumn('DISPLAY_NBDEV', 'DisplayNbdev', 'CHAR', true, 1, 'n');
		$this->addColumn('DISPLAY_MOY_GEN', 'DisplayMoyGen', 'CHAR', true, 1, 'y');
		$this->addColumn('MODELE_BULLETIN_PDF', 'ModeleBulletinPdf', 'VARCHAR', false, 255, null);
		$this->addColumn('RN_NOMDEV', 'RnNomdev', 'CHAR', true, null, 'n');
		$this->addColumn('RN_TOUTCOEFDEV', 'RnToutcoefdev', 'CHAR', true, null, 'n');
		$this->addColumn('RN_COEFDEV_SI_DIFF', 'RnCoefdevSiDiff', 'CHAR', true, null, 'n');
		$this->addColumn('RN_DATEDEV', 'RnDatedev', 'CHAR', true, 1, 'n');
		$this->addColumn('RN_SIGN_CHEFETAB', 'RnSignChefetab', 'CHAR', true, 1, 'n');
		$this->addColumn('RN_SIGN_PP', 'RnSignPp', 'CHAR', true, 1, 'n');
		$this->addColumn('RN_SIGN_RESP', 'RnSignResp', 'CHAR', true, 1, 'n');
		$this->addColumn('RN_SIGN_NBLIG', 'RnSignNblig', 'INTEGER', true, null, 3);
		$this->addColumn('RN_FORMULE', 'RnFormule', 'LONGVARCHAR', true, null, null);
		$this->addColumn('ECTS_TYPE_FORMATION', 'EctsTypeFormation', 'VARCHAR', false, 255, null);
		$this->addColumn('ECTS_PARCOURS', 'EctsParcours', 'VARCHAR', false, 255, null);
		$this->addColumn('ECTS_CODE_PARCOURS', 'EctsCodeParcours', 'VARCHAR', false, 255, null);
		$this->addColumn('ECTS_DOMAINES_ETUDE', 'EctsDomainesEtude', 'VARCHAR', false, 255, null);
		$this->addColumn('ECTS_FONCTION_SIGNATAIRE_ATTESTATION', 'EctsFonctionSignataireAttestation', 'VARCHAR', false, 255, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('PeriodeNote', 'PeriodeNote', RelationMap::ONE_TO_MANY, array('id' => 'id_classe', ), 'CASCADE', null, 'PeriodeNotes');
		$this->addRelation('JScolClasses', 'JScolClasses', RelationMap::ONE_TO_MANY, array('id' => 'id_classe', ), 'CASCADE', null, 'JScolClassess');
		$this->addRelation('JGroupesClasses', 'JGroupesClasses', RelationMap::ONE_TO_MANY, array('id' => 'id_classe', ), 'CASCADE', null, 'JGroupesClassess');
		$this->addRelation('JEleveClasse', 'JEleveClasse', RelationMap::ONE_TO_MANY, array('id' => 'id_classe', ), 'CASCADE', null, 'JEleveClasses');
		$this->addRelation('JEleveProfesseurPrincipal', 'JEleveProfesseurPrincipal', RelationMap::ONE_TO_MANY, array('id' => 'id_classe', ), 'CASCADE', null, 'JEleveProfesseurPrincipals');
		$this->addRelation('AbsenceEleveSaisie', 'AbsenceEleveSaisie', RelationMap::ONE_TO_MANY, array('id' => 'id_classe', ), 'SET NULL', null, 'AbsenceEleveSaisies');
		$this->addRelation('JCategoriesMatieresClasses', 'JCategoriesMatieresClasses', RelationMap::ONE_TO_MANY, array('id' => 'classe_id', ), 'CASCADE', null, 'JCategoriesMatieresClassess');
		$this->addRelation('CategorieMatiere', 'CategorieMatiere', RelationMap::MANY_TO_MANY, array(), 'CASCADE', null, 'CategorieMatieres');
	} // buildRelations()

} // ClasseTableMap
