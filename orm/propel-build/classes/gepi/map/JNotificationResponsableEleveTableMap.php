<?php



/**
 * This class defines the structure of the 'j_notifications_resp_pers' table.
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
class JNotificationResponsableEleveTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.JNotificationResponsableEleveTableMap';

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
		$this->setName('j_notifications_resp_pers');
		$this->setPhpName('JNotificationResponsableEleve');
		$this->setClassname('JNotificationResponsableEleve');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(false);
		// columns
		$this->addForeignPrimaryKey('A_NOTIFICATION_ID', 'ANotificationId', 'INTEGER' , 'a_notifications', 'ID', true, 12, null);
		$this->addForeignPrimaryKey('PERS_ID', 'PersId', 'VARCHAR' , 'resp_pers', 'PERS_ID', true, 10, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('AbsenceEleveNotification', 'AbsenceEleveNotification', RelationMap::MANY_TO_ONE, array('a_notification_id' => 'id', ), 'CASCADE', null);
		$this->addRelation('ResponsableEleve', 'ResponsableEleve', RelationMap::MANY_TO_ONE, array('pers_id' => 'pers_id', ), 'CASCADE', null);
	} // buildRelations()

} // JNotificationResponsableEleveTableMap
