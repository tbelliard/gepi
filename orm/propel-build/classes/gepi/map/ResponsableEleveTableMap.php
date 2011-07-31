<?php



/**
 * This class defines the structure of the 'resp_pers' table.
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
class ResponsableEleveTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.ResponsableEleveTableMap';

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
		$this->setName('resp_pers');
		$this->setPhpName('ResponsableEleve');
		$this->setClassname('ResponsableEleve');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(false);
		// columns
		$this->addPrimaryKey('PERS_ID', 'PersId', 'VARCHAR', true, 10, null);
		$this->addColumn('LOGIN', 'Login', 'VARCHAR', true, 50, null);
		$this->addColumn('NOM', 'Nom', 'VARCHAR', true, 30, null);
		$this->addColumn('PRENOM', 'Prenom', 'VARCHAR', true, 30, null);
		$this->addColumn('CIVILITE', 'Civilite', 'VARCHAR', true, 5, null);
		$this->addColumn('TEL_PERS', 'TelPers', 'VARCHAR', true, 255, null);
		$this->addColumn('TEL_PORT', 'TelPort', 'VARCHAR', true, 255, null);
		$this->addColumn('TEL_PROF', 'TelProf', 'VARCHAR', true, 255, null);
		$this->addColumn('MEL', 'Mel', 'VARCHAR', true, 100, null);
		$this->addForeignKey('ADR_ID', 'AdrId', 'VARCHAR', 'resp_adr', 'ADR_ID', false, 10, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('ResponsableEleveAdresse', 'ResponsableEleveAdresse', RelationMap::MANY_TO_ONE, array('adr_id' => 'adr_id', ), 'SET NULL', null);
		$this->addRelation('ResponsableInformation', 'ResponsableInformation', RelationMap::ONE_TO_MANY, array('pers_id' => 'pers_id', ), 'CASCADE', null, 'ResponsableInformations');
		$this->addRelation('JNotificationResponsableEleve', 'JNotificationResponsableEleve', RelationMap::ONE_TO_MANY, array('pers_id' => 'pers_id', ), 'CASCADE', null, 'JNotificationResponsableEleves');
		$this->addRelation('AbsenceEleveNotification', 'AbsenceEleveNotification', RelationMap::MANY_TO_MANY, array(), 'CASCADE', null, 'AbsenceEleveNotifications');
	} // buildRelations()

} // ResponsableEleveTableMap
