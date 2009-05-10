<?php


/**
 * This class adds structure of 'classes' table to 'gepi' DatabaseMap object.
 *
 *
 *
 * These statically-built map classes are used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    gepi.map
 */
class ClasseMapBuilder implements MapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.ClasseMapBuilder';

	/**
	 * The database map.
	 */
	private $dbMap;

	/**
	 * Tells us if this DatabaseMapBuilder is built so that we
	 * don't have to re-build it every time.
	 *
	 * @return     boolean true if this DatabaseMapBuilder is built, false otherwise.
	 */
	public function isBuilt()
	{
		return ($this->dbMap !== null);
	}

	/**
	 * Gets the databasemap this map builder built.
	 *
	 * @return     the databasemap
	 */
	public function getDatabaseMap()
	{
		return $this->dbMap;
	}

	/**
	 * The doBuild() method builds the DatabaseMap
	 *
	 * @return     void
	 * @throws     PropelException
	 */
	public function doBuild()
	{
		$this->dbMap = Propel::getDatabaseMap(ClassePeer::DATABASE_NAME);

		$tMap = $this->dbMap->addTable(ClassePeer::TABLE_NAME);
		$tMap->setPhpName('Classe');
		$tMap->setClassname('Classe');

		$tMap->setUseIdGenerator(true);

		$tMap->addPrimaryKey('ID', 'Id', 'INTEGER', true, null);

		$tMap->addColumn('CLASSE', 'Classe', 'VARCHAR', true, 100);

		$tMap->addColumn('NOM_COMPLET', 'NomComplet', 'VARCHAR', true, 100);

		$tMap->addColumn('SUIVI_PAR', 'SuiviPar', 'VARCHAR', true, 50);

		$tMap->addColumn('FORMULE', 'Formule', 'VARCHAR', true, 100);

		$tMap->addColumn('FORMAT_NOM', 'FormatNom', 'VARCHAR', true, 5);

		$tMap->addColumn('DISPLAY_RANG', 'DisplayRang', 'CHAR', true, 1);

		$tMap->addColumn('DISPLAY_ADDRESS', 'DisplayAddress', 'CHAR', true, 1);

		$tMap->addColumn('DISPLAY_COEF', 'DisplayCoef', 'CHAR', true, 1);

		$tMap->addColumn('DISPLAY_MAT_CAT', 'DisplayMatCat', 'CHAR', true, 1);

		$tMap->addColumn('DISPLAY_NBDEV', 'DisplayNbdev', 'CHAR', true, 1);

		$tMap->addColumn('DISPLAY_MOY_GEN', 'DisplayMoyGen', 'CHAR', true, 1);

		$tMap->addColumn('MODELE_BULLETIN_PDF', 'ModeleBulletinPdf', 'VARCHAR', false, 255);

		$tMap->addColumn('RN_NOMDEV', 'RnNomdev', 'CHAR', true, null);

		$tMap->addColumn('RN_TOUTCOEFDEV', 'RnToutcoefdev', 'CHAR', true, null);

		$tMap->addColumn('RN_COEFDEV_SI_DIFF', 'RnCoefdevSiDiff', 'CHAR', true, null);

		$tMap->addColumn('RN_DATEDEV', 'RnDatedev', 'CHAR', true, 1);

		$tMap->addColumn('RN_SIGN_CHEFETAB', 'RnSignChefetab', 'CHAR', true, 1);

		$tMap->addColumn('RN_SIGN_PP', 'RnSignPp', 'CHAR', true, 1);

		$tMap->addColumn('RN_SIGN_RESP', 'RnSignResp', 'CHAR', true, 1);

		$tMap->addColumn('RN_SIGN_NBLIG', 'RnSignNblig', 'INTEGER', true, null);

		$tMap->addColumn('RN_FORMULE', 'RnFormule', 'LONGVARCHAR', true, null);

		$tMap->addColumn('ECTS_TYPE_FORMATION', 'EctsTypeFormation', 'VARCHAR', false, 255);

		$tMap->addColumn('ECTS_PARCOURS', 'EctsParcours', 'VARCHAR', false, 255);

		$tMap->addColumn('ECTS_CODE_PARCOURS', 'EctsCodeParcours', 'VARCHAR', false, 255);

		$tMap->addColumn('ECTS_DOMAINES_ETUDE', 'EctsDomainesEtude', 'VARCHAR', false, 255);

		$tMap->addColumn('ECTS_FONCTION_SIGNATAIRE_ATTESTATION', 'EctsFonctionSignataireAttestation', 'VARCHAR', false, 255);

	} // doBuild()

} // ClasseMapBuilder
