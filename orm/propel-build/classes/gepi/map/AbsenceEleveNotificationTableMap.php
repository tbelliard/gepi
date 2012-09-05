<?php



/**
 * This class defines the structure of the 'a_notifications' table.
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
class AbsenceEleveNotificationTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.AbsenceEleveNotificationTableMap';

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
		$this->setName('a_notifications');
		$this->setPhpName('AbsenceEleveNotification');
		$this->setClassname('AbsenceEleveNotification');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 11, null);
		$this->addForeignKey('UTILISATEUR_ID', 'UtilisateurId', 'VARCHAR', 'utilisateurs', 'LOGIN', false, 100, null);
		$this->addForeignKey('A_TRAITEMENT_ID', 'ATraitementId', 'INTEGER', 'a_traitements', 'ID', true, 12, null);
		$this->addColumn('TYPE_NOTIFICATION', 'TypeNotification', 'ENUM', false, null, null);
		$this->getColumn('TYPE_NOTIFICATION', false)->setValueSet(array (
  0 => 'courrier',
  1 => 'email',
  2 => 'sms',
  3 => 'communication telephonique',
));
		$this->addColumn('EMAIL', 'Email', 'VARCHAR', false, 100, null);
		$this->addColumn('TELEPHONE', 'Telephone', 'VARCHAR', false, 100, null);
		$this->addForeignKey('ADR_ID', 'AdresseId', 'VARCHAR', 'resp_adr', 'ADR_ID', false, 10, null);
		$this->addColumn('COMMENTAIRE', 'Commentaire', 'LONGVARCHAR', false, null, null);
		$this->addColumn('STATUT_ENVOI', 'StatutEnvoi', 'ENUM', false, null, 'etat initial');
		$this->getColumn('STATUT_ENVOI', false)->setValueSet(array (
  0 => 'etat initial',
  1 => 'en cours',
  2 => 'echec',
  3 => 'succes',
  4 => 'succes avec accuse de reception',
  5 => 'pret a envoyer',
));
		$this->addColumn('DATE_ENVOI', 'DateEnvoi', 'TIMESTAMP', false, null, null);
		$this->addColumn('ERREUR_MESSAGE_ENVOI', 'ErreurMessageEnvoi', 'LONGVARCHAR', false, null, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('UtilisateurProfessionnel', 'UtilisateurProfessionnel', RelationMap::MANY_TO_ONE, array('utilisateur_id' => 'login', ), 'SET NULL', null);
		$this->addRelation('AbsenceEleveTraitement', 'AbsenceEleveTraitement', RelationMap::MANY_TO_ONE, array('a_traitement_id' => 'id', ), 'CASCADE', null);
		$this->addRelation('Adresse', 'Adresse', RelationMap::MANY_TO_ONE, array('adr_id' => 'adr_id', ), 'SET NULL', null);
		$this->addRelation('JNotificationResponsableEleve', 'JNotificationResponsableEleve', RelationMap::ONE_TO_MANY, array('id' => 'a_notification_id', ), 'CASCADE', null, 'JNotificationResponsableEleves');
		$this->addRelation('ResponsableEleve', 'ResponsableEleve', RelationMap::MANY_TO_MANY, array(), 'CASCADE', null, 'ResponsableEleves');
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
		);
	} // getBehaviors()

} // AbsenceEleveNotificationTableMap
