<?php



/**
 * This class defines the structure of the 'edt_creneaux' table.
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
class EdtCreneauTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.EdtCreneauTableMap';

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
		$this->setName('edt_creneaux');
		$this->setPhpName('EdtCreneau');
		$this->setClassname('EdtCreneau');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID_DEFINIE_PERIODE', 'IdDefiniePeriode', 'INTEGER', true, 11, null);
		$this->addColumn('NOM_DEFINIE_PERIODE', 'NomDefiniePeriode', 'VARCHAR', true, 50, null);
		$this->addColumn('HEUREDEBUT_DEFINIE_PERIODE', 'HeuredebutDefiniePeriode', 'TIME', true, null, null);
		$this->addColumn('HEUREFIN_DEFINIE_PERIODE', 'HeurefinDefiniePeriode', 'TIME', true, null, null);
		$this->addColumn('SUIVI_DEFINIE_PERIODE', 'SuiviDefiniePeriode', 'INTEGER', false, 2, 9);
		$this->addColumn('TYPE_CRENEAUX', 'TypeCreneaux', 'VARCHAR', false, 15, 'cours');
		$this->addColumn('JOUR_CRENEAU', 'JourCreneau', 'VARCHAR', false, 20, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('AbsenceEleveSaisie', 'AbsenceEleveSaisie', RelationMap::ONE_TO_MANY, array('id_definie_periode' => 'id_edt_creneau', ), 'SET NULL', null);
    $this->addRelation('EdtEmplacementCours', 'EdtEmplacementCours', RelationMap::ONE_TO_MANY, array('id_definie_periode' => 'id_definie_periode', ), 'CASCADE', null);
	} // buildRelations()

} // EdtCreneauTableMap
