<?php



/**
 * This class defines the structure of the 'ct_devoirs_entry' table.
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
class CahierTexteTravailAFaireTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.CahierTexteTravailAFaireTableMap';

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
		$this->setName('ct_devoirs_entry');
		$this->setPhpName('CahierTexteTravailAFaire');
		$this->setClassname('CahierTexteTravailAFaire');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID_CT', 'IdCt', 'INTEGER', true, null, null);
		$this->addColumn('DATE_CT', 'DateCt', 'INTEGER', true, null, 0);
		$this->addColumn('CONTENU', 'Contenu', 'LONGVARCHAR', true, null, null);
		$this->addColumn('VISE', 'Vise', 'CHAR', true, null, 'n');
		$this->addForeignKey('ID_GROUPE', 'IdGroupe', 'INTEGER', 'groupes', 'ID', true, null, null);
		$this->addForeignKey('ID_LOGIN', 'IdLogin', 'VARCHAR', 'utilisateurs', 'LOGIN', false, 32, null);
		$this->addForeignKey('ID_SEQUENCE', 'IdSequence', 'INTEGER', 'ct_sequences', 'ID', false, 5, 0);
		$this->addColumn('DATE_VISIBILITE_ELEVE', 'DateVisibiliteEleve', 'TIMESTAMP', true, null, 'now()');
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('Groupe', 'Groupe', RelationMap::MANY_TO_ONE, array('id_groupe' => 'id', ), 'CASCADE', null);
		$this->addRelation('UtilisateurProfessionnel', 'UtilisateurProfessionnel', RelationMap::MANY_TO_ONE, array('id_login' => 'login', ), 'SET NULL', null);
		$this->addRelation('CahierTexteSequence', 'CahierTexteSequence', RelationMap::MANY_TO_ONE, array('id_sequence' => 'id', ), 'SET NULL', null);
		$this->addRelation('CahierTexteTravailAFaireFichierJoint', 'CahierTexteTravailAFaireFichierJoint', RelationMap::ONE_TO_MANY, array('id_ct' => 'id_ct_devoir', ), 'CASCADE', null, 'CahierTexteTravailAFaireFichierJoints');
	} // buildRelations()

} // CahierTexteTravailAFaireTableMap
