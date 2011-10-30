<?php



/**
 * This class defines the structure of the 'eleves' table.
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
class EleveTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.EleveTableMap';

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
		$this->setName('eleves');
		$this->setPhpName('Eleve');
		$this->setClassname('Eleve');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(true);
		// columns
		$this->addColumn('NO_GEP', 'NoGep', 'VARCHAR', true, 50, null);
		$this->addColumn('LOGIN', 'Login', 'VARCHAR', true, 50, null);
		$this->addColumn('NOM', 'Nom', 'VARCHAR', true, 50, null);
		$this->addColumn('PRENOM', 'Prenom', 'VARCHAR', true, 50, null);
		$this->addColumn('SEXE', 'Sexe', 'VARCHAR', true, 1, null);
		$this->addColumn('NAISSANCE', 'Naissance', 'DATE', true, null, null);
		$this->addColumn('LIEU_NAISSANCE', 'LieuNaissance', 'VARCHAR', true, 50, '');
		$this->addColumn('ELENOET', 'Elenoet', 'VARCHAR', true, 50, null);
		$this->addColumn('ERENO', 'Ereno', 'VARCHAR', true, 50, null);
		$this->addColumn('ELE_ID', 'EleId', 'VARCHAR', true, 10, '');
		$this->addColumn('EMAIL', 'Email', 'VARCHAR', true, 255, '');
		$this->addPrimaryKey('ID_ELEVE', 'Id', 'INTEGER', true, 11, null);
		$this->addColumn('DATE_SORTIE', 'DateSortie', 'TIMESTAMP', false, null, null);
		$this->addForeignKey('MEF_CODE', 'MefCode', 'INTEGER', 'mef', 'MEF_CODE', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('Mef', 'Mef', RelationMap::MANY_TO_ONE, array('mef_code' => 'mef_code', ), 'SET NULL', null);
		$this->addRelation('JEleveClasse', 'JEleveClasse', RelationMap::ONE_TO_MANY, array('login' => 'login', ), 'CASCADE', null, 'JEleveClasses');
		$this->addRelation('JEleveCpe', 'JEleveCpe', RelationMap::ONE_TO_MANY, array('login' => 'e_login', ), 'CASCADE', null, 'JEleveCpes');
		$this->addRelation('JEleveGroupe', 'JEleveGroupe', RelationMap::ONE_TO_MANY, array('login' => 'login', ), 'CASCADE', null, 'JEleveGroupes');
		$this->addRelation('JEleveProfesseurPrincipal', 'JEleveProfesseurPrincipal', RelationMap::ONE_TO_MANY, array('login' => 'login', ), 'CASCADE', null, 'JEleveProfesseurPrincipals');
		$this->addRelation('EleveRegimeDoublant', 'EleveRegimeDoublant', RelationMap::ONE_TO_ONE, array('login' => 'login', ), 'CASCADE', null);
		$this->addRelation('ResponsableInformation', 'ResponsableInformation', RelationMap::ONE_TO_MANY, array('ele_id' => 'ele_id', ), 'CASCADE', null, 'ResponsableInformations');
		$this->addRelation('JEleveAncienEtablissement', 'JEleveAncienEtablissement', RelationMap::ONE_TO_MANY, array('id_eleve' => 'id_eleve', ), 'CASCADE', null, 'JEleveAncienEtablissements');
		$this->addRelation('JAidEleves', 'JAidEleves', RelationMap::ONE_TO_MANY, array('login' => 'login', ), 'CASCADE', null, 'JAidElevess');
		$this->addRelation('AbsenceEleveSaisie', 'AbsenceEleveSaisie', RelationMap::ONE_TO_MANY, array('id_eleve' => 'eleve_id', ), 'CASCADE', null, 'AbsenceEleveSaisies');
		$this->addRelation('AbsenceAgregationDecompte', 'AbsenceAgregationDecompte', RelationMap::ONE_TO_MANY, array('id_eleve' => 'eleve_id', ), 'CASCADE', null, 'AbsenceAgregationDecomptes');
		$this->addRelation('CreditEcts', 'CreditEcts', RelationMap::ONE_TO_MANY, array('id_eleve' => 'id_eleve', ), 'CASCADE', null, 'CreditEctss');
		$this->addRelation('CreditEctsGlobal', 'CreditEctsGlobal', RelationMap::ONE_TO_MANY, array('id_eleve' => 'id_eleve', ), 'CASCADE', null, 'CreditEctsGlobals');
		$this->addRelation('ArchiveEcts', 'ArchiveEcts', RelationMap::ONE_TO_MANY, array('no_gep' => 'ine', ), 'CASCADE', null, 'ArchiveEctss');
		$this->addRelation('AncienEtablissement', 'AncienEtablissement', RelationMap::MANY_TO_MANY, array(), 'CASCADE', null, 'AncienEtablissements');
		$this->addRelation('AidDetails', 'AidDetails', RelationMap::MANY_TO_MANY, array(), 'CASCADE', null, 'AidDetailss');
	} // buildRelations()

} // EleveTableMap
