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
		saveSetting('abs2_retard_critere_duree',30);
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
		saveSetting('abs2_retard_critere_duree',30);
		$this->assertTrue($saisie->getRetard());
		$saisie->reload();
		saveSetting('abs2_retard_critere_duree',20);
		$this->assertFalse($saisie->getRetard());
		saveSetting('abs2_retard_critere_duree',30);
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-06')->getFirst();
		$this->assertTrue($saisie->getRetard());
		
	}

	public function testGetSousResponsabiliteEtablissement()
	{
		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-01')->getFirst();
		saveSetting('abs2_saisie_par_defaut_sous_responsabilite_etab','y');
		$this->assertTrue($saisie->getSousResponsabiliteEtablissement());
		saveSetting('abs2_saisie_par_defaut_sous_responsabilite_etab','n');
		$saisie->reload();
		$this->assertFalse($saisie->getSousResponsabiliteEtablissement());
		saveSetting('abs2_saisie_par_defaut_sous_responsabilite_etab','y');
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-02')->getFirst();
		$this->assertFalse($saisie->getSousResponsabiliteEtablissement());
								
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-03')->getFirst();
		$this->assertTrue($saisie->getSousResponsabiliteEtablissement());
								
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-04')->getFirst();
		saveSetting('abs2_saisie_multi_type_sous_responsabilite_etab','y');
		$this->assertTrue($saisie->getSousResponsabiliteEtablissement());
		saveSetting('abs2_saisie_multi_type_sous_responsabilite_etab','n');
		$saisie->reload();
		$this->assertFalse($saisie->getSousResponsabiliteEtablissement());
		saveSetting('abs2_saisie_multi_type_sous_responsabilite_etab','y');
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-05')->getFirst();
		$this->assertTrue($saisie->getSousResponsabiliteEtablissement());
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-06')->getFirst();
		$this->assertFalse($saisie->getSousResponsabiliteEtablissement());
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-07')->getFirst();
		$this->assertTrue($saisie->getSousResponsabiliteEtablissement());
	}
	
	public function testGetManquementObligationPresenceSpecifie_NON_PRECISE()
	{
		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-01')->getFirst();
		$this->assertFalse($saisie->getManquementObligationPresenceSpecifie_NON_PRECISE());
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-02')->getFirst();
		$this->assertFalse($saisie->getManquementObligationPresenceSpecifie_NON_PRECISE());
								
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-03')->getFirst();
		$this->assertFalse($saisie->getManquementObligationPresenceSpecifie_NON_PRECISE());
								
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-04')->getFirst();
		$this->assertFalse($saisie->getManquementObligationPresenceSpecifie_NON_PRECISE());
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-05')->getFirst();
		$this->assertFalse($saisie->getManquementObligationPresenceSpecifie_NON_PRECISE());
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-06')->getFirst();
		$this->assertFalse($saisie->getManquementObligationPresenceSpecifie_NON_PRECISE());
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-07')->getFirst();
		$this->assertTrue($saisie->getManquementObligationPresenceSpecifie_NON_PRECISE());
	}

	public function testGetJustifiee()
	{
		saveSetting('abs2_saisie_multi_type_non_justifiee','n');
		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-01')->getFirst();
		$this->assertFalse($saisie->getJustifiee());
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-02')->getFirst();
		$this->assertTrue($saisie->getJustifiee());
								
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-03')->getFirst();
		$this->assertFalse($saisie->getJustifiee());
								
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-04')->getFirst();
		$this->assertTrue($saisie->getJustifiee());
		saveSetting('abs2_saisie_multi_type_non_justifiee','y');
		$saisie->reload();
		$this->assertFalse($saisie->getJustifiee());
		saveSetting('abs2_saisie_multi_type_non_justifiee','n');
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-05')->getFirst();
		$this->assertFalse($saisie->getJustifiee());
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-06')->getFirst();
		$this->assertFalse($saisie->getJustifiee());
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-07')->getFirst();
		$this->assertFalse($saisie->getJustifiee());
	}

	public function testGetNotifiee()
	{
		saveSetting('abs2_saisie_multi_type_non_justifiee','n');
		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-01')->getFirst();
		$this->assertFalse($saisie->getNotifiee());
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-02')->getFirst();
		$this->assertFalse($saisie->getNotifiee());
								
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-03')->getFirst();
		$this->assertFalse($saisie->getNotifiee());
								
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-04')->getFirst();
		$this->assertTrue($saisie->getNotifiee());
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-05')->getFirst();
		$this->assertFalse($saisie->getNotifiee());
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-06')->getFirst();
		$this->assertFalse($saisie->getNotifiee());
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-07')->getFirst();
		$this->assertFalse($saisie->getNotifiee());
	}

	public function testGetNotificationEnCours()
	{
		saveSetting('abs2_saisie_multi_type_non_justifiee','n');
		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-01')->getFirst();
		$this->assertFalse($saisie->getNotificationEnCours());
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-02')->getFirst();
		$this->assertTrue($saisie->getNotificationEnCours());
								
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-03')->getFirst();
		$this->assertFalse($saisie->getNotificationEnCours());
								
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-04')->getFirst();
		$this->assertFalse($saisie->getNotificationEnCours());
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-05')->getFirst();
		$this->assertFalse($saisie->getNotificationEnCours());
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-06')->getFirst();
		$this->assertFalse($saisie->getNotificationEnCours());
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-07')->getFirst();
		$this->assertFalse($saisie->getNotificationEnCours());
	}

	public function testGetAbsenceEleveTraitements()
	{
		saveSetting('abs2_saisie_multi_type_non_justifiee','n');
		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-01')->getFirst();
		$this->assertEquals(0,$saisie->getAbsenceEleveTraitements()->count());
        $traitement = new AbsenceEleveTraitement();
        $traitement->save();
        $saisie->addAbsenceEleveTraitement($traitement);
        $this->assertEquals(1,$saisie->getAbsenceEleveTraitements()->count());
        $traitement->delete();
        
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-02')->getFirst();
		$this->assertEquals(1,$saisie->getAbsenceEleveTraitements()->count());
										
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-03')->getFirst();
		$this->assertEquals(1,$saisie->getAbsenceEleveTraitements()->count());
										
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-04')->getFirst();
		$this->assertEquals(3,$saisie->getAbsenceEleveTraitements()->count());
				
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-05')->getFirst();
		$this->assertEquals(0,$saisie->getAbsenceEleveTraitements()->count());
				
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-06')->getFirst();
		$this->assertEquals(3,$saisie->getAbsenceEleveTraitements()->count());
				
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-07')->getFirst();
		$this->assertEquals(1,$saisie->getAbsenceEleveTraitements()->count());
	}
	
	public function testGetSaisiesContradictoiresManquementObligation()
	{
		saveSetting('abs2_saisie_multi_type_non_justifiee','n');
		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-01')->getFirst();
		$this->assertEquals(0,$saisie->getSaisiesContradictoiresManquementObligation()->count());
        
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-08')->getFirst();
		$this->assertEquals(0,$saisie->getSaisiesContradictoiresManquementObligation()->count());
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-09')->getFirst();
		$this->assertEquals(1,$saisie->getSaisiesContradictoiresManquementObligation()->count());

		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-10')->getFirst();
		$this->assertEquals(1,$saisie->getSaisiesContradictoiresManquementObligation()->count());
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-11')->getFirst();
		$this->assertEquals(1,$saisie->getSaisiesContradictoiresManquementObligation()->count());
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-12')->getFirst();
		$this->assertEquals(1,$saisie->getSaisiesContradictoiresManquementObligation()->count());
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-13')->getFirst();
		$this->assertEquals(0,$saisie->getSaisiesContradictoiresManquementObligation()->count());
	}

	public function testIsSaisiesContradictoiresManquementObligation()
	{
		saveSetting('abs2_saisie_multi_type_non_justifiee','n');
		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-01')->getFirst();
		$this->assertFalse($saisie->isSaisiesContradictoiresManquementObligation());
        
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-08')->getFirst();
		$this->assertFalse($saisie->isSaisiesContradictoiresManquementObligation());
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-09')->getFirst();
		$this->assertTrue($saisie->isSaisiesContradictoiresManquementObligation());

		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-10')->getFirst();
		$this->assertTrue($saisie->isSaisiesContradictoiresManquementObligation());
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-11')->getFirst();
		$this->assertTrue($saisie->isSaisiesContradictoiresManquementObligation());
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-12')->getFirst();
		$this->assertTrue($saisie->isSaisiesContradictoiresManquementObligation());
		
		$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-13')->getFirst();
		$this->assertFalse($saisie->isSaisiesContradictoiresManquementObligation());
	}
}
