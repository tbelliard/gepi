<?php

require_once dirname(__FILE__) . '/../../../tools/helpers/orm/GepiEmptyTestBase.php';

/**
 * Test class for UtilisateurProfessionnel.
 *
 */
class AbsenceEleveSaisieTest extends GepiEmptyTestBase
{
	protected function setUp()
	{
		parent::setUp();
		GepiDataPopulator::populate();
	}

	public function testSave()
	{
		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		$lebesgue_prof = UtilisateurProfessionnelQuery::create()->findOneByLogin('Lebesgue');
		$saisie = new AbsenceEleveSaisie();
        $saisie->setEleve($florence_eleve);
        $saisie->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie->setDebutAbs('2005-10-01 08:00:00');
        try {
            $saisie->save();
            $this->fail('Une exception doit être soulevée lors de cette sauvegarde');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
	}
	
	public function testHasTypeSaisie()
	{
		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-01')->getFirst();
		$this->assertFalse($saisie->hasTypeSaisie());
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-02')->getFirst();
		$this->assertFalse($saisie->hasTypeSaisie());
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-03')->getFirst();
		$this->assertTrue($saisie->hasTypeSaisie());
	}

	public function testHasTypeSaisieDiscipline()
	{
		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-01')->getFirst();
		$this->assertFalse($saisie->hasTypeSaisieDiscipline());
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-02')->getFirst();
		$this->assertFalse($saisie->hasTypeSaisieDiscipline());
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-03')->getFirst();
		$this->assertTrue($saisie->hasTypeSaisieDiscipline());
	}

	public function testGetTraitee()
	{
		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-01')->getFirst();
		$this->assertFalse($saisie->getTraitee());
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-02')->getFirst();
		$this->assertTrue($saisie->getTraitee());
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-03')->getFirst();
		$this->assertTrue($saisie->getTraitee());
	}

	public function testHasLieuSaisie()
	{
	    $id_lieu = AbsenceEleveLieuQuery::create()->filterByNom("Etablissement")->findOne()->getId();
	    
		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-01')->getFirst();
		$this->assertTrue($saisie->hasLieuSaisie(null));
		$this->assertFalse($saisie->hasLieuSaisie($id_lieu));
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-02')->getFirst();
		$this->assertTrue($saisie->hasLieuSaisie(null));
		$this->assertFalse($saisie->hasLieuSaisie($id_lieu));
				
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-03')->getFirst();
		$this->assertFalse($saisie->hasLieuSaisie(null));
		$this->assertTrue($saisie->hasLieuSaisie($id_lieu));
				
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-04')->getFirst();
		$this->assertTrue($saisie->hasLieuSaisie(null));
		$this->assertTrue($saisie->hasLieuSaisie($id_lieu));
	}

	public function testHasTypeLikeErreurSaisie()
	{
	    $id_lieu = AbsenceEleveLieuQuery::create()->filterByNom("Etablissement")->findOne()->getId();
	    
		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-01')->getFirst();
		$this->assertFalse($saisie->hasTypeLikeErreurSaisie());
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-02')->getFirst();
		$this->assertFalse($saisie->hasTypeLikeErreurSaisie());
						
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-03')->getFirst();
		$this->assertFalse($saisie->hasTypeLikeErreurSaisie());
						
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-04')->getFirst();
		$this->assertTrue($saisie->hasTypeLikeErreurSaisie());
	}

	public function testGetManquementObligationPresence()
	{
	    $id_lieu = AbsenceEleveLieuQuery::create()->filterByNom("Etablissement")->findOne()->getId();
	    
		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-01')->getFirst();
		$this->assertTrue($saisie->getManquementObligationPresence());
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-02')->getFirst();
		$this->assertTrue($saisie->getManquementObligationPresence());
								
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-03')->getFirst();
		$this->assertFalse($saisie->getRetard());
								
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-04')->getFirst();
		$this->assertTrue($saisie->getManquementObligationPresence());
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-06')->getFirst();
		$this->assertTrue($saisie->getManquementObligationPresence());
		
	}
	
	public function testGetRetard()
	{
	    $id_lieu = AbsenceEleveLieuQuery::create()->filterByNom("Etablissement")->findOne()->getId();
	    
		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-01')->getFirst();
		$this->assertFalse($saisie->getRetard());
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-02')->getFirst();
		$this->assertFalse($saisie->getRetard());
								
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-03')->getFirst();
		$this->assertFalse($saisie->getRetard());
								
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-04')->getFirst();
		$this->assertTrue($saisie->getRetard());
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-05')->getFirst();
		$this->assertTrue($saisie->getRetard());
		$saisie->reload();
		saveSetting('abs2_retard_critere_duree',20);
		$this->assertFalse($saisie->getRetard());
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-06')->getFirst();
		$this->assertTrue($saisie->getRetard());
		
	}

}
