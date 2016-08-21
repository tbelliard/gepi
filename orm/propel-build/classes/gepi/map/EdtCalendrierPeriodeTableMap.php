<?php



/**
 * This class defines the structure of the 'edt_calendrier' table.
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
class EdtCalendrierPeriodeTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.EdtCalendrierPeriodeTableMap';

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
		$this->setName('edt_calendrier');
		$this->setPhpName('EdtCalendrierPeriode');
		$this->setClassname('EdtCalendrierPeriode');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(false);
		// columns
		$this->addPrimaryKey('ID_CALENDRIER', 'IdCalendrier', 'INTEGER', true, 11, null);
		$this->addColumn('CLASSE_CONCERNE_CALENDRIER', 'ClasseConcerneCalendrier', 'LONGVARCHAR', true, null, null);
		$this->addColumn('NOM_CALENDRIER', 'NomCalendrier', 'VARCHAR', true, 100, null);
		$this->addColumn('DEBUT_CALENDRIER_TS', 'DebutCalendrierTs', 'VARCHAR', true, 255, null);
		$this->addColumn('FIN_CALENDRIER_TS', 'FinCalendrierTs', 'VARCHAR', true, 255, null);
		$this->addColumn('JOURDEBUT_CALENDRIER', 'JourdebutCalendrier', 'DATE', true, 11, null);
		$this->addColumn('HEUREDEBUT_CALENDRIER', 'HeuredebutCalendrier', 'TIME', true, 11, null);
		$this->addColumn('JOURFIN_CALENDRIER', 'JourfinCalendrier', 'DATE', true, 11, null);
		$this->addColumn('HEUREFIN_CALENDRIER', 'HeurefinCalendrier', 'TIME', true, 11, null);
		$this->addColumn('NUMERO_PERIODE', 'NumeroPeriode', 'TINYINT', true, 4, null);
		$this->addColumn('ETABFERME_CALENDRIER', 'EtabfermeCalendrier', 'TINYINT', true, 4, null);
		$this->addColumn('ETABVACANCES_CALENDRIER', 'EtabvacancesCalendrier', 'TINYINT', true, 4, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
		$this->addRelation('EdtEmplacementCours', 'EdtEmplacementCours', RelationMap::ONE_TO_MANY, array('id_calendrier' => 'id_calendrier', ), 'SET NULL', null, 'EdtEmplacementCourss');
	} // buildRelations()

} // EdtCalendrierPeriodeTableMap
