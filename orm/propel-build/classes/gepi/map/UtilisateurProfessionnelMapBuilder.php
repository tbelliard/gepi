<?php


/**
 * This class adds structure of 'utilisateurs' table to 'gepi' DatabaseMap object.
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
class UtilisateurProfessionnelMapBuilder implements MapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.UtilisateurProfessionnelMapBuilder';

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
		$this->dbMap = Propel::getDatabaseMap(UtilisateurProfessionnelPeer::DATABASE_NAME);

		$tMap = $this->dbMap->addTable(UtilisateurProfessionnelPeer::TABLE_NAME);
		$tMap->setPhpName('UtilisateurProfessionnel');
		$tMap->setClassname('UtilisateurProfessionnel');

		$tMap->setUseIdGenerator(false);

		$tMap->addPrimaryKey('LOGIN', 'Login', 'VARCHAR', true, 50);

		$tMap->addColumn('NOM', 'Nom', 'VARCHAR', true, 50);

		$tMap->addColumn('PRENOM', 'Prenom', 'VARCHAR', true, 50);

		$tMap->addColumn('CIVILITE', 'Civilite', 'VARCHAR', true, 5);

		$tMap->addColumn('PASSWORD', 'Password', 'CHAR', true, 32);

		$tMap->addColumn('EMAIL', 'Email', 'VARCHAR', true, 50);

		$tMap->addColumn('SHOW_EMAIL', 'ShowEmail', 'VARCHAR', true, 50);

		$tMap->addColumn('STATUT', 'Statut', 'VARCHAR', true, 20);

		$tMap->addColumn('ETAT', 'Etat', 'VARCHAR', true, 20);

		$tMap->addColumn('CHANGE_MDP', 'ChangeMdp', 'CHAR', true, 1);

		$tMap->addColumn('DATE_VERROUILLAGE', 'DateVerrouillage', 'TIME', true, null);

		$tMap->addColumn('PASSWORD_TICKET', 'PasswordTicket', 'VARCHAR', true, 255);

		$tMap->addColumn('TICKET_EXPIRATION', 'TicketExpiration', 'TIME', true, null);

		$tMap->addColumn('NIVEAU_ALERTE', 'NiveauAlerte', 'SMALLINT', true, null);

		$tMap->addColumn('OBSERVATION_SECURITE', 'ObservationSecurite', 'TINYINT', true, null);

		$tMap->addColumn('TEMP_DIR', 'TempDir', 'VARCHAR', true, 255);

		$tMap->addColumn('NUMIND', 'Numind', 'VARCHAR', true, 255);

		$tMap->addColumn('AUTH_MODE', 'AuthMode', 'VARCHAR', true, 255);

	} // doBuild()

} // UtilisateurProfessionnelMapBuilder
