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
	
	public function testGetSousResponsabiliteEtablissement()
	{
	    $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
	    
	    $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-02')->getFirst();
	    $traitement = $saisie->getAbsenceEleveTraitements()->getFirst();
	    $this->assertFalse($traitement->getSousResponsabiliteEtablissement());
	    
		saveSetting('abs2_saisie_par_defaut_sous_responsabilite_etab','n');
	    $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-14')->getFirst();
	    $traitement = $saisie->getAbsenceEleveTraitements()->getFirst();
	    $this->assertFalse($traitement->getSousResponsabiliteEtablissement());
		saveSetting('abs2_saisie_par_defaut_sous_responsabilite_etab','y');
	    $this->assertTrue($traitement->getSousResponsabiliteEtablissement());
		saveSetting('abs2_saisie_par_defaut_sous_responsabilite_etab','n');
	    
	}

	public function testQuery()
	{
        $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
        
        $traitements = AbsenceEleveTraitementQuery::create()->useJTraitementSaisieEleveQuery()->useAbsenceEleveSaisieQuery()
                                                    	    ->filterByEleve($florence_eleve)
                                                    	    ->endUse()->endUse()->find();
        $this->assertEquals(16,$traitements->count(),'le total des traitements de Florence est 16');
                                    	    
        saveSetting('abs2_saisie_par_defaut_sans_manquement','n');
        $traitements = AbsenceEleveTraitementQuery::create()->filterByManquementObligationPresence(true)
                                                    	    ->useJTraitementSaisieEleveQuery()->useAbsenceEleveSaisieQuery()
                                                    	    ->filterByEleve($florence_eleve)
                                                    	    ->endUse()->endUse()->find();
        $this->assertEquals(9,$traitements->count());
        $traitements = AbsenceEleveTraitementQuery::create()->filterByManquementObligationPresence(false)
                                                    	    ->useJTraitementSaisieEleveQuery()->useAbsenceEleveSaisieQuery()
                                                    	    ->filterByEleve($florence_eleve)
                                                    	    ->endUse()->endUse()->find();
//        foreach ($traitements as $traitement) {
//            echo "\n".$traitement->getDescription();
//        }
	    $this->assertEquals(7,$traitements->count());
        
        saveSetting('abs2_saisie_par_defaut_sans_manquement','y');
        $traitements = AbsenceEleveTraitementQuery::create()->filterByManquementObligationPresence(true)
                                                    	    ->useJTraitementSaisieEleveQuery()->useAbsenceEleveSaisieQuery()
                                                    	    ->filterByEleve($florence_eleve)
                                                    	    ->endUse()->endUse()->find();
        $this->assertEquals(8,$traitements->count());
        $traitements = AbsenceEleveTraitementQuery::create()->filterByManquementObligationPresence(false)
                                                    	    ->useJTraitementSaisieEleveQuery()->useAbsenceEleveSaisieQuery()
                                                    	    ->filterByEleve($florence_eleve)
                                                    	    ->endUse()->endUse()->find();
        $this->assertEquals(8,$traitements->count());
        
       saveSetting('abs2_saisie_par_defaut_sans_manquement','n');
	}
}
