<?php



/**
 * This class defines the structure of the 'utilisateurs' table.
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
class UtilisateurProfessionnelTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.UtilisateurProfessionnelTableMap';

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
		$this->setName('utilisateurs');
		$this->setPhpName('UtilisateurProfessionnel');
		$this->setClassname('UtilisateurProfessionnel');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(false);
		// columns
		$this->addPrimaryKey('LOGIN', 'Login', 'VARCHAR', true, 50, null);
		$this->addColumn('NOM', 'Nom', 'VARCHAR', true, 50, null);
		$this->addColumn('PRENOM', 'Prenom', 'VARCHAR', true, 50, null);
		$this->addColumn('CIVILITE', 'Civilite', 'VARCHAR', true, 5, null);
		$this->addColumn('PASSWORD', 'Password', 'CHAR', true, 128, null);
		$this->addColumn('SALT', 'Salt', 'CHAR', false, 128, null);
		$this->addColumn('EMAIL', 'Email', 'VARCHAR', true, 50, null);
		$this->addColumn('SHOW_EMAIL', 'ShowEmail', 'VARCHAR', true, 50, 'no');
		$this->addColumn('STATUT', 'Statut', 'VARCHAR', true, 20, null);
		$this->addColumn('ETAT', 'Etat', 'VARCHAR', true, 20, null);
		$this->addColumn('CHANGE_MDP', 'ChangeMdp', 'CHAR', true, 1, 'n');
		$this->addColumn('DATE_VERROUILLAGE', 'DateVerrouillage', 'DATE', true, null, '2006-01-01 00:00:00');
		$this->addColumn('PASSWORD_TICKET', 'PasswordTicket', 'VARCHAR', true, 255, null);
		$this->addColumn('TICKET_EXPIRATION', 'TicketExpiration', 'DATE', true, null, null);
		$this->addColumn('NIVEAU_ALERTE', 'NiveauAlerte', 'SMALLINT', true, null, 0);
		$this->addColumn('OBSERVATION_SECURITE', 'ObservationSecurite', 'TINYINT', true, null, 0);
		$this->addColumn('TEMP_DIR', 'TempDir', 'VARCHAR', true, 255, null);
		$this->addColumn('NUMIND', 'Numind', 'VARCHAR', true, 255, null);
		$this->addColumn('AUTH_MODE', 'AuthMode', 'VARCHAR', true, 255, 'gepi');
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('JGroupesProfesseurs', 'JGroupesProfesseurs', RelationMap::ONE_TO_MANY, array('login' => 'login', ), 'CASCADE', null, 'JGroupesProfesseurss');
		$this->addRelation('JScolClasses', 'JScolClasses', RelationMap::ONE_TO_MANY, array('login' => 'login', ), 'CASCADE', null, 'JScolClassess');
		$this->addRelation('CahierTexteCompteRendu', 'CahierTexteCompteRendu', RelationMap::ONE_TO_MANY, array('login' => 'id_login', ), 'SET NULL', null, 'CahierTexteCompteRendus');
		$this->addRelation('CahierTexteTravailAFaire', 'CahierTexteTravailAFaire', RelationMap::ONE_TO_MANY, array('login' => 'id_login', ), 'SET NULL', null, 'CahierTexteTravailAFaires');
		$this->addRelation('CahierTexteNoticePrivee', 'CahierTexteNoticePrivee', RelationMap::ONE_TO_MANY, array('login' => 'id_login', ), 'SET NULL', null, 'CahierTexteNoticePrivees');
		$this->addRelation('JEleveCpe', 'JEleveCpe', RelationMap::ONE_TO_MANY, array('login' => 'cpe_login', ), 'CASCADE', null, 'JEleveCpes');
		$this->addRelation('JEleveProfesseurPrincipal', 'JEleveProfesseurPrincipal', RelationMap::ONE_TO_MANY, array('login' => 'professeur', ), 'CASCADE', null, 'JEleveProfesseurPrincipals');
		$this->addRelation('JAidUtilisateursProfessionnels', 'JAidUtilisateursProfessionnels', RelationMap::ONE_TO_MANY, array('login' => 'id_utilisateur', ), 'CASCADE', null, 'JAidUtilisateursProfessionnelss');
		$this->addRelation('AbsenceEleveSaisie', 'AbsenceEleveSaisie', RelationMap::ONE_TO_MANY, array('login' => 'utilisateur_id', ), null, null, 'AbsenceEleveSaisies');
		$this->addRelation('AbsenceEleveTraitement', 'AbsenceEleveTraitement', RelationMap::ONE_TO_MANY, array('login' => 'utilisateur_id', ), null, null, 'AbsenceEleveTraitements');
		$this->addRelation('ModifiedAbsenceEleveTraitement', 'AbsenceEleveTraitement', RelationMap::ONE_TO_MANY, array('login' => 'modifie_par_utilisateur_id', ), null, null, 'ModifiedAbsenceEleveTraitements');
		$this->addRelation('AbsenceEleveNotification', 'AbsenceEleveNotification', RelationMap::ONE_TO_MANY, array('login' => 'utilisateur_id', ), 'SET NULL', null, 'AbsenceEleveNotifications');
		$this->addRelation('JProfesseursMatieres', 'JProfesseursMatieres', RelationMap::ONE_TO_MANY, array('login' => 'id_professeur', ), null, null, 'JProfesseursMatieress');
		$this->addRelation('PreferenceUtilisateurProfessionnel', 'PreferenceUtilisateurProfessionnel', RelationMap::ONE_TO_MANY, array('login' => 'login', ), 'CASCADE', null, 'PreferenceUtilisateurProfessionnels');
		$this->addRelation('EdtEmplacementCours', 'EdtEmplacementCours', RelationMap::ONE_TO_MANY, array('login' => 'login_prof', ), 'SET NULL', null, 'EdtEmplacementCourss');
		$this->addRelation('Groupe', 'Groupe', RelationMap::MANY_TO_MANY, array(), 'SET NULL', null, 'Groupes');
		$this->addRelation('AidDetails', 'AidDetails', RelationMap::MANY_TO_MANY, array(), 'SET NULL', null, 'AidDetailss');
		$this->addRelation('Matiere', 'Matiere', RelationMap::MANY_TO_MANY, array(), 'SET NULL', null, 'Matieres');
	} // buildRelations()

} // UtilisateurProfessionnelTableMap
