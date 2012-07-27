<?php



/**
 * This class defines the structure of the 'horaires_etablissement' table.
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
class EdtHorairesEtablissementTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'gepi.map.EdtHorairesEtablissementTableMap';

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
		$this->setName('horaires_etablissement');
		$this->setPhpName('EdtHorairesEtablissement');
		$this->setClassname('EdtHorairesEtablissement');
		$this->setPackage('gepi');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID_HORAIRE_ETABLISSEMENT', 'IdHoraireEtablissement', 'INTEGER', true, 11, null);
		$this->addColumn('DATE_HORAIRE_ETABLISSEMENT', 'DateHoraireEtablissement', 'DATE', false, null, null);
		$this->addColumn('JOUR_HORAIRE_ETABLISSEMENT', 'JourHoraireEtablissement', 'VARCHAR', true, 15, null);
		$this->addColumn('OUVERTURE_HORAIRE_ETABLISSEMENT', 'OuvertureHoraireEtablissement', 'TIME', true, null, null);
		$this->addColumn('FERMETURE_HORAIRE_ETABLISSEMENT', 'FermetureHoraireEtablissement', 'TIME', true, null, null);
		$this->addColumn('PAUSE_HORAIRE_ETABLISSEMENT', 'PauseHoraireEtablissement', 'TIME', false, null, null);
		$this->addColumn('OUVERT_HORAIRE_ETABLISSEMENT', 'OuvertHoraireEtablissement', 'BOOLEAN', true, 1, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // EdtHorairesEtablissementTableMap
