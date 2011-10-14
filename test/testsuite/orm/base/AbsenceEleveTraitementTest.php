<?php

require_once dirname(__FILE__) . '/../../../tools/helpers/orm/GepiEmptyTestBase.php';

/**
 * Test class for UtilisateurProfessionnel.
 *
 */
class AbsenceEleveTraitementTest extends GepiEmptyTestBase
{
	protected function setUp()
	{
		parent::setUp();
		GepiDataPopulator::populate();
	}

	public function testGetResponsablesInformationsSaisies()
	{
	    $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
	    $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-02')->getFirst();
	    $traitement = $saisie->getAbsenceEleveTraitements()->getFirst();
	    $this->assertEquals('Mere',$traitement->getResponsablesInformationsSaisies()->getFirst()->getResponsableEleve()->getPrenom());
	}
	
	public function testGetManquementObligationPresence()
	{
	    $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
	    
	    $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-02')->getFirst();
	    $traitement = $saisie->getAbsenceEleveTraitements()->getFirst();
	    $this->assertTrue($traitement->getManquementObligationPresence());
	    
		saveSetting('abs2_saisie_par_defaut_sans_manquement','n');
	    $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-14')->getFirst();
	    $traitement = $saisie->getAbsenceEleveTraitements()->getFirst();
	    $this->assertTrue($traitement->getManquementObligationPresence());
		saveSetting('abs2_saisie_par_defaut_sans_manquement','y');
	    $this->assertFalse($traitement->getManquementObligationPresence());
		saveSetting('abs2_saisie_par_defaut_sans_manquement','n');
	    
	}
	
}
