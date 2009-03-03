<?php


/**
 * This class adds structure of 'aid' table to 'gepi' DatabaseMap object.
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
class AidDetailsMapBuilder implements MapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.AidDetailsMapBuilder';

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
		$this->dbMap = Propel::getDatabaseMap(AidDetailsPeer::DATABASE_NAME);

		$tMap = $this->dbMap->addTable(AidDetailsPeer::TABLE_NAME);
		$tMap->setPhpName('AidDetails');
		$tMap->setClassname('AidDetails');

		$tMap->setUseIdGenerator(true);

		$tMap->addPrimaryKey('ID', 'Id', 'VARCHAR', true, 100);

		$tMap->addColumn('NOM', 'Nom', 'VARCHAR', true, 100);

		$tMap->addColumn('NUMERO', 'Numero', 'VARCHAR', true, 8);

		$tMap->addForeignKey('INDICE_AID', 'IndiceAid', 'INTEGER', 'aid_config', 'INDICE_AID', true, 11);

		$tMap->addColumn('PERSO1', 'Perso1', 'VARCHAR', true, 255);

		$tMap->addColumn('PERSO2', 'Perso2', 'VARCHAR', true, 255);

		$tMap->addColumn('PERSO3', 'Perso3', 'VARCHAR', true, 255);

		$tMap->addColumn('PRODUCTIONS', 'Productions', 'VARCHAR', true, 100);

		$tMap->addColumn('RESUME', 'Resume', 'LONGVARCHAR', true, null);

		$tMap->addColumn('FAMILLE', 'Famille', 'SMALLINT', true, 6);

		$tMap->addColumn('MOTS_CLES', 'MotsCles', 'VARCHAR', true, 255);

		$tMap->addColumn('ADRESSE1', 'Adresse1', 'VARCHAR', true, 255);

		$tMap->addColumn('ADRESSE2', 'Adresse2', 'VARCHAR', true, 255);

		$tMap->addColumn('PUBLIC_DESTINATAIRE', 'PublicDestinataire', 'VARCHAR', true, 50);

		$tMap->addColumn('CONTACTS', 'Contacts', 'LONGVARCHAR', true, null);

		$tMap->addColumn('DIVERS', 'Divers', 'LONGVARCHAR', true, null);

		$tMap->addColumn('MATIERE1', 'Matiere1', 'VARCHAR', true, 100);

		$tMap->addColumn('MATIERE2', 'Matiere2', 'VARCHAR', true, 100);

		$tMap->addColumn('ELEVE_PEUT_MODIFIER', 'ElevePeutModifier', 'CHAR', true, 1);

		$tMap->addColumn('PROF_PEUT_MODIFIER', 'ProfPeutModifier', 'CHAR', true, 1);

		$tMap->addColumn('CPE_PEUT_MODIFIER', 'CpePeutModifier', 'CHAR', true, 1);

		$tMap->addColumn('FICHE_PUBLIQUE', 'FichePublique', 'CHAR', true, 1);

		$tMap->addColumn('AFFICHE_ADRESSE1', 'AfficheAdresse1', 'CHAR', true, 1);

		$tMap->addColumn('EN_CONSTRUCTION', 'EnConstruction', 'CHAR', true, 1);

	} // doBuild()

} // AidDetailsMapBuilder
