<?php



/**
 * This class defines the structure of the 'edt_cours' table.
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
class EdtEmplacementCoursTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.EdtEmplacementCoursTableMap';

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
		$this->setName('edt_cours');
		$this->setPhpName('EdtEmplacementCours');
		$this->setClassname('EdtEmplacementCours');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(false);
		// columns
		$this->addPrimaryKey('ID_COURS', 'IdCours', 'INTEGER', true, 3, null);
		$this->addForeignKey('ID_GROUPE', 'IdGroupe', 'CHAR', 'groupes', 'ID', false, 10, null);
		$this->addForeignKey('ID_AID', 'IdAid', 'CHAR', 'aid', 'ID', false, 10, null);
		$this->addForeignKey('ID_SALLE', 'IdSalle', 'CHAR', 'salle_cours', 'ID_SALLE', false, 10, null);
		$this->addColumn('JOUR_SEMAINE', 'JourSemaine', 'VARCHAR', true, 10, null);
		$this->addForeignKey('ID_DEFINIE_PERIODE', 'IdDefiniePeriode', 'VARCHAR', 'edt_creneaux', 'ID_DEFINIE_PERIODE', true, 3, null);
		$this->addColumn('DUREE', 'Duree', 'VARCHAR', true, 10, '2');
		$this->addColumn('HEUREDEB_DEC', 'HeuredebDec', 'VARCHAR', true, 3, '0');
		$this->addColumn('ID_SEMAINE', 'TypeSemaine', 'VARCHAR', false, 3, '');
		$this->addForeignKey('ID_CALENDRIER', 'IdCalendrier', 'VARCHAR', 'edt_calendrier', 'ID_CALENDRIER', false, 3, null);
		$this->addColumn('MODIF_EDT', 'ModifEdt', 'VARCHAR', false, 3, null);
		$this->addForeignKey('LOGIN_PROF', 'LoginProf', 'VARCHAR', 'utilisateurs', 'LOGIN', false, 50, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('Groupe', 'Groupe', RelationMap::MANY_TO_ONE, array('id_groupe' => 'id', ), 'CASCADE', null);
    $this->addRelation('AidDetails', 'AidDetails', RelationMap::MANY_TO_ONE, array('id_aid' => 'id', ), 'CASCADE', null);
    $this->addRelation('EdtSalle', 'EdtSalle', RelationMap::MANY_TO_ONE, array('id_salle' => 'id_salle', ), 'SET NULL', null);
    $this->addRelation('EdtCreneau', 'EdtCreneau', RelationMap::MANY_TO_ONE, array('id_definie_periode' => 'id_definie_periode', ), 'CASCADE', null);
    $this->addRelation('EdtCalendrierPeriode', 'EdtCalendrierPeriode', RelationMap::MANY_TO_ONE, array('id_calendrier' => 'id_calendrier', ), 'SET NULL', null);
    $this->addRelation('UtilisateurProfessionnel', 'UtilisateurProfessionnel', RelationMap::MANY_TO_ONE, array('login_prof' => 'login', ), 'SET NULL', null);
    $this->addRelation('AbsenceEleveSaisie', 'AbsenceEleveSaisie', RelationMap::ONE_TO_MANY, array('id_cours' => 'id_edt_emplacement_cours', ), 'SET NULL', null);
	} // buildRelations()

} // EdtEmplacementCoursTableMap
