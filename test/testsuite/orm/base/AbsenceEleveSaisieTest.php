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

        AbsenceEleveSaisiePeer::disableAgregation();
        $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
        $lebesgue_prof = UtilisateurProfessionnelQuery::create()->findOneByLogin('Lebesgue');
        $saisie = new AbsenceEleveSaisie();
        $saisie->setEleve($florence_eleve);
        $saisie->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie->setDebutAbs('2012-09-18 08:00:00');
        $saisie->setFinAbs('2012-09-18 09:00:00');
        $saisie->save();
        $decompte = AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByDateDemiJounee('2012-09-18')->findOne();
        $this->assertTrue($decompte == null || $decompte->getManquementObligationPresence() == false);
         
        AbsenceEleveSaisiePeer::enableAgregation();
        $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
        $lebesgue_prof = UtilisateurProfessionnelQuery::create()->findOneByLogin('Lebesgue');
        $saisie = new AbsenceEleveSaisie();
        $saisie->setEleve($florence_eleve);
        $saisie->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie->setDebutAbs('2012-09-19 08:00:00');
        $saisie->setFinAbs('2012-09-19 09:00:00');
        $saisie->save();
        $decompte = AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByDateDemiJounee('2012-09-19')->findOne();
        $this->assertTrue($decompte->getManquementObligationPresence());
        $saisie->setFinAbs('2012-09-19 08:10:00');
        $saisie->save();
        $decompte = AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByDateDemiJounee('2012-09-19')->findOne();
        $this->assertFalse($decompte->getManquementObligationPresence());
        AbsenceEleveSaisiePeer::disableAgregation();
    }

    public function testHasModeInterface()
    {
        $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-05')->getFirst();
        $this->assertFalse($saisie->hasModeInterface());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-06')->getFirst();
        $this->assertFalse($saisie->hasModeInterface());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-07')->getFirst();
        $this->assertTrue($saisie->hasModeInterface());
    }

    public function testHasModeInterfaceDiscipline()
    {
        $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-05')->getFirst();
        $this->assertFalse($saisie->hasModeInterfaceDiscipline());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-06')->getFirst();
        $this->assertFalse($saisie->hasModeInterfaceDiscipline());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-07')->getFirst();
        $this->assertTrue($saisie->hasModeInterfaceDiscipline());
    }

    public function testGetTraitee()
    {
        $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-05')->getFirst();
        $this->assertFalse($saisie->getTraitee());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-06')->getFirst();
        $this->assertTrue($saisie->getTraitee());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-07')->getFirst();
        $this->assertTrue($saisie->getTraitee());
    }

    public function testHasLieuSaisie()
    {
        $id_lieu = AbsenceEleveLieuQuery::create()->filterByNom("Etablissement")->findOne()->getId();
        $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-05')->getFirst();
        $this->assertTrue($saisie->hasLieuSaisie(null));
        $this->assertFalse($saisie->hasLieuSaisie($id_lieu));

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-06')->getFirst();
        $this->assertTrue($saisie->hasLieuSaisie(null));
        $this->assertFalse($saisie->hasLieuSaisie($id_lieu));

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-07')->getFirst();
        $this->assertFalse($saisie->hasLieuSaisie(null));
        $this->assertTrue($saisie->hasLieuSaisie($id_lieu));

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-08')->getFirst();
        $this->assertTrue($saisie->hasLieuSaisie(null));
        $this->assertTrue($saisie->hasLieuSaisie($id_lieu));
    }

    public function testHasTypeLikeErreurSaisie()
    {
        $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-05')->getFirst();
        $this->assertFalse($saisie->hasTypeLikeErreurSaisie());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-06')->getFirst();
        $this->assertFalse($saisie->hasTypeLikeErreurSaisie());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-07')->getFirst();
        $this->assertFalse($saisie->hasTypeLikeErreurSaisie());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-08')->getFirst();
        $this->assertTrue($saisie->hasTypeLikeErreurSaisie());
    }

    public function testGetManquementObligationPresence()
    {
        $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');

        saveSetting('abs2_saisie_par_defaut_sans_manquement','n');
        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-05')->getFirst();
        $this->assertTrue($saisie->getManquementObligationPresence());
        saveSetting('abs2_saisie_par_defaut_sans_manquement','y');
        $saisie->clearAllReferences();
        $this->assertFalse($saisie->getManquementObligationPresence());
        saveSetting('abs2_saisie_par_defaut_sans_manquement','n');
        $saisie->clearAllReferences();

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-06')->getFirst();
        $this->assertTrue($saisie->getManquementObligationPresence());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-07')->getFirst();
        $this->assertFalse($saisie->getRetard());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-08')->getFirst();
        $this->assertTrue($saisie->getManquementObligationPresence());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-10')->getFirst();
        $this->assertTrue($saisie->getManquementObligationPresence());

    }

    public function testGetRetard()
    {
        saveSetting('abs2_retard_critere_duree',30);
        $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-05')->getFirst();
        $this->assertFalse($saisie->getRetard());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-06')->getFirst();
        $this->assertFalse($saisie->getRetard());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-07')->getFirst();
        $this->assertFalse($saisie->getRetard());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-08')->getFirst();
        $this->assertTrue($saisie->getRetard());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-09')->getFirst();
        saveSetting('abs2_retard_critere_duree',30);
        $this->assertTrue($saisie->getRetard());
        $saisie->clearAllReferences();
        saveSetting('abs2_retard_critere_duree',20);
        $this->assertFalse($saisie->getRetard());
        saveSetting('abs2_retard_critere_duree',30);

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-10')->getFirst();//sur cette saisie on a plusieurs traitement, on privilégie le retard
        $this->assertTrue($saisie->getRetard());

    }

    public function testGetSousResponsabiliteEtablissement()
    {
        $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-05')->getFirst();
        saveSetting('abs2_saisie_par_defaut_sous_responsabilite_etab','n');
        $this->assertFalse($saisie->getSousResponsabiliteEtablissement());
        saveSetting('abs2_saisie_par_defaut_sous_responsabilite_etab','y');
        $saisie->clearAllReferences();
        $this->assertTrue($saisie->getSousResponsabiliteEtablissement());
        saveSetting('abs2_saisie_par_defaut_sous_responsabilite_etab','n');

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-06')->getFirst();
        $saisie->clearAllReferences();
        $this->assertFalse($saisie->getSousResponsabiliteEtablissement());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-07')->getFirst();
        $saisie->clearAllReferences();
        $this->assertTrue($saisie->getSousResponsabiliteEtablissement());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-08')->getFirst();
        $saisie->clearAllReferences();
        saveSetting('abs2_saisie_multi_type_sous_responsabilite_etab','n');
        $this->assertFalse($saisie->getSousResponsabiliteEtablissement());
        saveSetting('abs2_saisie_multi_type_sous_responsabilite_etab','y');
        $saisie->clearAllReferences();
        $this->assertTrue($saisie->getSousResponsabiliteEtablissement());
        saveSetting('abs2_saisie_multi_type_sous_responsabilite_etab','n');

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-09')->getFirst();
        $saisie->clearAllReferences();
        $this->assertFalse($saisie->getSousResponsabiliteEtablissement());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-10')->getFirst();
        $saisie->clearAllReferences();
        $this->assertFalse($saisie->getSousResponsabiliteEtablissement());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-10')->getFirst();
        $saisie->clearAllReferences();
        $this->assertFalse($saisie->getSousResponsabiliteEtablissement());
    }

    public function testGetManquementObligationPresenceSpecifie_NON_PRECISE()
    {
        $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-05')->getFirst();
        $this->assertFalse($saisie->getManquementObligationPresenceSpecifie_NON_PRECISE());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-06')->getFirst();
        $this->assertFalse($saisie->getManquementObligationPresenceSpecifie_NON_PRECISE());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-07')->getFirst();
        $this->assertFalse($saisie->getManquementObligationPresenceSpecifie_NON_PRECISE());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-08')->getFirst();
        $this->assertFalse($saisie->getManquementObligationPresenceSpecifie_NON_PRECISE());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-09')->getFirst();
        $this->assertFalse($saisie->getManquementObligationPresenceSpecifie_NON_PRECISE());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-10')->getFirst();
        $this->assertFalse($saisie->getManquementObligationPresenceSpecifie_NON_PRECISE());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-11')->getFirst();
        $this->assertTrue($saisie->getManquementObligationPresenceSpecifie_NON_PRECISE());
    }

    public function testGetJustifiee()
    {
        saveSetting('abs2_saisie_multi_type_non_justifiee','n');
        $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-05')->getFirst();
        $this->assertFalse($saisie->getJustifiee());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-06')->getFirst();
        $this->assertTrue($saisie->getJustifiee());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-07')->getFirst();
        $this->assertFalse($saisie->getJustifiee());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-08')->getFirst();
        $this->assertTrue($saisie->getJustifiee());
        saveSetting('abs2_saisie_multi_type_non_justifiee','y');
        $saisie->clearAllReferences();
        $this->assertFalse($saisie->getJustifiee());
        saveSetting('abs2_saisie_multi_type_non_justifiee','n');

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-09')->getFirst();
        $this->assertTrue($saisie->getJustifiee());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-10')->getFirst();
        $this->assertFalse($saisie->getJustifiee());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-10')->getFirst();
        $this->assertFalse($saisie->getJustifiee());
    }

    public function testGetNotifiee()
    {
        $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-05')->getFirst();
        $this->assertFalse($saisie->getNotifiee());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-06')->getFirst();
        $this->assertFalse($saisie->getNotifiee());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-07')->getFirst();
        $this->assertFalse($saisie->getNotifiee());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-08')->getFirst();
        $this->assertTrue($saisie->getNotifiee());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-09')->getFirst();
        $this->assertFalse($saisie->getNotifiee());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-10')->getFirst();
        $this->assertFalse($saisie->getNotifiee());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-10')->getFirst();
        $this->assertFalse($saisie->getNotifiee());
    }

    public function testGetNotificationEnCours()
    {
        $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-05')->getFirst();
        $this->assertFalse($saisie->getNotificationEnCours());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-06')->getFirst();
        $this->assertTrue($saisie->getNotificationEnCours());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-07')->getFirst();
        $this->assertFalse($saisie->getNotificationEnCours());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-08')->getFirst();
        $this->assertFalse($saisie->getNotificationEnCours());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-09')->getFirst();
        $this->assertFalse($saisie->getNotificationEnCours());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-10')->getFirst();
        $this->assertFalse($saisie->getNotificationEnCours());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-10')->getFirst();
        $this->assertFalse($saisie->getNotificationEnCours());
    }

    public function testGetNotifieeEnglobante()
    {
        $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-05')->getFirst();
        $this->assertFalse($saisie->getNotifieeEnglobante());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-06')->getFirst();
        $this->assertFalse($saisie->getNotifieeEnglobante());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-07')->getFirst();
        $this->assertFalse($saisie->getNotifieeEnglobante());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-08')->getFirst();
        $this->assertTrue($saisie->getNotifieeEnglobante());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-09')->getFirst();
        $this->assertFalse($saisie->getNotifieeEnglobante());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-10')->getFirst();
        $this->assertFalse($saisie->getNotifieeEnglobante());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-10')->getFirst();
        $this->assertFalse($saisie->getNotifieeEnglobante());
        
        $saisie = AbsenceEleveSaisieQuery::create()->filterByFinAbs('2013-06-10 08:10:00')->findOne();
        $this->assertTrue($saisie->getNotifieeEnglobante());
        
    }

    public function testGetNotificationEnCoursEnglobante()
    {
        $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-05')->getFirst();
        $this->assertFalse($saisie->getNotificationEnCoursEnglobante());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-06')->getFirst();
        $this->assertTrue($saisie->getNotificationEnCoursEnglobante());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-07')->getFirst();
        $this->assertFalse($saisie->getNotificationEnCoursEnglobante());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-08')->getFirst();
        $this->assertFalse($saisie->getNotificationEnCoursEnglobante());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-09')->getFirst();
        $this->assertFalse($saisie->getNotificationEnCoursEnglobante());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-10')->getFirst();
        $this->assertFalse($saisie->getNotificationEnCoursEnglobante());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-10')->getFirst();
        $this->assertFalse($saisie->getNotificationEnCoursEnglobante());
        
        $saisie = AbsenceEleveSaisieQuery::create()->filterByFinAbs('2013-06-06 08:10:00')->findOne();
        $this->assertTrue($saisie->getNotificationEnCoursEnglobante());
    }
    
    public function testGetAbsenceEleveTraitements()
    {
        $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-05')->getFirst();
        $this->assertEquals(0,$saisie->getAbsenceEleveTraitements()->count());
        $traitement = new AbsenceEleveTraitement();
        $traitement->save();
        $saisie->addAbsenceEleveTraitement($traitement);
        $this->assertEquals(1,$saisie->getAbsenceEleveTraitements()->count());
        $traitement->delete();

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-06')->getFirst();
        $this->assertEquals(1,$saisie->getAbsenceEleveTraitements()->count());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-07')->getFirst();
        $this->assertEquals(1,$saisie->getAbsenceEleveTraitements()->count());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-08')->getFirst();
        $this->assertEquals(3,$saisie->getAbsenceEleveTraitements()->count());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-09')->getFirst();
        $this->assertEquals(1,$saisie->getAbsenceEleveTraitements()->count());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-10')->getFirst();
        $this->assertEquals(3,$saisie->getAbsenceEleveTraitements()->count());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-11')->getFirst();
        $this->assertEquals(1,$saisie->getAbsenceEleveTraitements()->count());
    }

    //@TODO test more this function
    public function testGetSaisiesContradictoiresManquementObligation()
    {
        $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-05')->getFirst();
        $this->assertEquals(0,$saisie->getSaisiesContradictoiresManquementObligation()->count());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-12')->getFirst();
        $this->assertEquals(0,$saisie->getSaisiesContradictoiresManquementObligation()->count());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-13')->getFirst();
        $this->assertEquals(1,$saisie->getSaisiesContradictoiresManquementObligation()->count());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-14')->getFirst();
        $this->assertEquals(1,$saisie->getSaisiesContradictoiresManquementObligation()->count());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-15')->getFirst();
        $this->assertEquals(1,$saisie->getSaisiesContradictoiresManquementObligation()->count());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-16')->getFirst();
        $this->assertEquals(1,$saisie->getSaisiesContradictoiresManquementObligation()->count());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-17')->getFirst();
        $this->assertEquals(0,$saisie->getSaisiesContradictoiresManquementObligation()->count());
    }

    public function testIsSaisiesContradictoiresManquementObligation()
    {
        $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-05')->getFirst();
        $this->assertFalse($saisie->isSaisiesContradictoiresManquementObligation());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-12')->getFirst();
        $this->assertFalse($saisie->isSaisiesContradictoiresManquementObligation());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-13')->getFirst();
        $this->assertTrue($saisie->isSaisiesContradictoiresManquementObligation());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-14')->getFirst();
        $this->assertTrue($saisie->isSaisiesContradictoiresManquementObligation());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-15')->getFirst();
        $this->assertTrue($saisie->isSaisiesContradictoiresManquementObligation());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-16')->getFirst();
        $this->assertTrue($saisie->isSaisiesContradictoiresManquementObligation());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-17')->getFirst();
        $this->assertFalse($saisie->isSaisiesContradictoiresManquementObligation());
    }

    public function testDelete()
    {
        $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-05')->getFirst();
        //on va vérifier que le delete change bien le update_ad
        $old_updated_at = $saisie->getUpdatedAt('U');
        sleep(1);
        $saisie->delete();
        $this->assertGreaterThan($old_updated_at, $saisie->getUpdatedAt('U'), 'le delete doit changer le updated_ad');
        $saisie = AbsenceEleveSaisieQuery::create()->filterByEleve($florence_eleve)->filterByDebutAbs('2012-10-05 08:00:00')->findOne();
        $this->assertNull($saisie);
        $saisie = AbsenceEleveSaisieQuery::create()->includeDeleted()->filterByEleve($florence_eleve)->filterByDebutAbs('2012-10-05 08:00:00')->findOne();
        $this->assertNotNull($saisie);

        AbsenceEleveSaisiePeer::enableAgregation();
        $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
        $lebesgue_prof = UtilisateurProfessionnelQuery::create()->findOneByLogin('Lebesgue');
        $saisie = new AbsenceEleveSaisie();
        $saisie->setEleve($florence_eleve);
        $saisie->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie->setDebutAbs('2012-09-20 08:00:00');
        $saisie->setFinAbs('2012-09-20 09:00:00');
        $saisie->save();
        $decompte = AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByDateDemiJounee('2012-09-20')->findOne();
        $this->assertTrue($decompte->getManquementObligationPresence());
        $saisie->getEleve()->clearAllReferences();
        $saisie->delete();
        $decompte = AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByDateDemiJounee('2012-09-20')->findOne();
        $this->assertFalse($decompte->getManquementObligationPresence());
    }

    public function testToVersion()
    {
        $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-06')->getFirst();
        //$saisie = new AbsenceEleveSaisie();
        $saisie->setFinAbs('2012-10-06 11:00');
        $saisie->save();
        $old_updated_at = $saisie->getUpdatedAt('U');
        sleep(1);
        $saisie->toVersion(1);
        $this->assertGreaterThan($old_updated_at, $saisie->getUpdatedAt('U'), 'le toVersion doit changer le update_ad');
    }

    public function testUpdateSynchroAbsenceAgregationTable()
    {
        AbsenceAgregationDecompteQuery::create()->deleteAll();
        foreach (EleveQuery::create()->find() as $eleve) {
            $eleve->updateAbsenceAgregationTable(new DateTime('2012-10-05 00:00:00'),new DateTime('2012-10-14 23:59:59'));
        }
        sleep(1);
        $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-05')->getFirst();
        $saisie_id = $saisie->getId();
        mysql_query("update a_saisies set fin_abs = '2012-10-05 08:10:00' where id = ".$saisie_id);//ça devient un retard
        $decompte = AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByDateDemiJounee('2012-10-05')->findOne();
        $this->assertTrue($decompte->getManquementObligationPresence());
        $this->assertEquals(0,$decompte->getRetards());
        $saisie->reload();
        $saisie->clearAllReferences();
        $saisie->getEleve()->clearAllReferences();
        $saisie->updateSynchroAbsenceAgregationTable();//c'est mis à jour
        $decompte = AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByDateDemiJounee('2012-10-05')->findOne();
        $this->assertFalse($decompte->getManquementObligationPresence());
        $this->assertEquals(1,$decompte->getRetards());
        $saisie->setFinAbs('2012-10-05 09:00:00');
        $saisie->save();
    }

    public function testCheckAndUpdateSynchroAbsenceAgregationTable()
    {
        AbsenceAgregationDecompteQuery::create()->deleteAll();
        foreach (EleveQuery::create()->find() as $eleve) {
            $eleve->updateAbsenceAgregationTable(new DateTime('2012-10-05 00:00:00'),new DateTime('2012-10-14 23:59:59'));
        }
        sleep(1);
        $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-05')->getFirst();
        $saisie_id = $saisie->getId();
        mysql_query("update a_saisies set fin_abs = '2012-10-05 08:10:00' where id = ".$saisie_id);//ça devient un retard
        $decompte = AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByDateDemiJounee('2012-10-05')->findOne();
        $this->assertTrue($decompte->getManquementObligationPresence());
        $this->assertEquals(0,$decompte->getRetards());
        $saisie->reload();
        $saisie->clearAllReferences();
        $saisie->getEleve()->clearAllReferences();
        $saisie->checkAndUpdateSynchroAbsenceAgregationTable();//ça n'est pas mis à jour car le test est true
        $decompte = AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByDateDemiJounee('2012-10-05')->findOne();
        $this->assertTrue($decompte->getManquementObligationPresence());
        $this->assertEquals(0,$decompte->getRetards());
        mysql_query("update a_saisies set updated_at = now() where id = ".$saisie_id);
        $saisie->reload();
        $saisie->clearAllReferences();
        $saisie->getEleve()->clearAllReferences();
        $saisie->checkAndUpdateSynchroAbsenceAgregationTable();//c'est mis à jour car le test est false
        $decompte = AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByDateDemiJounee('2012-10-05')->findOne();
        $this->assertFalse($decompte->getManquementObligationPresence());
        $this->assertEquals(1,$decompte->getRetards());
        $saisie->setFinAbs('2012-10-05 09:00:00');
        $saisie->save();
    }

    public function testgetAbsenceEleveSaisiesEnglobantes()
    {
        $saisie = AbsenceEleveSaisieQuery::create()->filterByDebutAbs('2013-05-30 08:00:00')->findOne();
        $saisie_englobante = $saisie->getAbsenceEleveSaisiesEnglobantes();
        $this->assertEquals($saisie_englobante->count(),1);
    }

    public function testGetJustifieeEnglobante()
    {
        saveSetting('abs2_saisie_multi_type_non_justifiee','n');
        $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-05')->getFirst();
        $this->assertFalse($saisie->getJustifieeEnglobante());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-06')->getFirst();
        $this->assertTrue($saisie->getJustifieeEnglobante());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-07')->getFirst();
        $this->assertFalse($saisie->getJustifieeEnglobante());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-08')->getFirst();
        $this->assertTrue($saisie->getJustifieeEnglobante());
        saveSetting('abs2_saisie_multi_type_non_justifiee','y');
        $saisie->clearAllReferences();
        $this->assertFalse($saisie->getJustifieeEnglobante());
        saveSetting('abs2_saisie_multi_type_non_justifiee','n');

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-09')->getFirst();
        $this->assertTrue($saisie->getJustifieeEnglobante());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-10')->getFirst();
        $this->assertFalse($saisie->getJustifieeEnglobante());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-10')->getFirst();
        $this->assertFalse($saisie->getJustifieeEnglobante());

        $saisies = $florence_eleve->getAbsenceEleveSaisiesDuJour('2013-06-04');
        $this->assertTrue($saisies->getCurrent()->getJustifieeEnglobante());
        $this->assertTrue($saisies->getNext()->getJustifieeEnglobante());

        $saisies = $florence_eleve->getAbsenceEleveSaisiesDuJour('2013-06-05');
        $this->assertTrue($saisies->getCurrent()->getJustifieeEnglobante());
        $this->assertTrue($saisies->getNext()->getJustifieeEnglobante());

        $saisie = new AbsenceEleveSaisie();
        $this->assertFalse($saisie->getJustifieeEnglobante());
    }

    public function testgetManquementObligationPresenceEnglobante()
    {
        saveSetting('abs2_saisie_multi_type_sans_manquement','n');
        $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-05')->getFirst();
        $this->assertTrue($saisie->getManquementObligationPresenceEnglobante());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-06')->getFirst();
        $this->assertTrue($saisie->getManquementObligationPresenceEnglobante());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-07')->getFirst();
        $this->assertFalse($saisie->getManquementObligationPresenceEnglobante());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-08')->getFirst();
        $this->assertTrue($saisie->getManquementObligationPresenceEnglobante());
        saveSetting('abs2_saisie_multi_type_sans_manquement','y');
        $saisie->clearAllReferences();
        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-08')->getFirst();
        $this->assertFalse($saisie->getManquementObligationPresenceEnglobante());
        saveSetting('abs2_saisie_multi_type_sans_manquement','n');

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-09')->getFirst();
        $this->assertTrue($saisie->getManquementObligationPresenceEnglobante());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-10')->getFirst();
        $this->assertTrue($saisie->getManquementObligationPresenceEnglobante());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-11')->getFirst();
        $this->assertFalse($saisie->getManquementObligationPresenceEnglobante());

        $saisie = AbsenceEleveSaisieQuery::create()->filterByFinAbs('2013-05-29 08:10:00')->findOne();
        $this->assertFalse($saisie->getManquementObligationPresenceEnglobante());
        
        $saisie = AbsenceEleveSaisieQuery::create()->filterByDebutAbs('2013-05-31 08:00:00')->find();
        $this->assertTrue($saisie->getFirst()->getManquementObligationPresenceEnglobante());
        $this->assertTrue($saisie->getNext()->getManquementObligationPresenceEnglobante());
        
        $saisies = $florence_eleve->getAbsenceEleveSaisiesDuJour('2013-06-04');
        $this->assertTrue($saisies->getCurrent()->getManquementObligationPresenceEnglobante());
        $this->assertTrue($saisies->getNext()->getManquementObligationPresenceEnglobante());

        $saisies = $florence_eleve->getAbsenceEleveSaisiesDuJour('2013-06-05');
        $this->assertTrue($saisies->getCurrent()->getManquementObligationPresenceEnglobante());
        $this->assertTrue($saisies->getNext()->getManquementObligationPresenceEnglobante());

        $saisies = $florence_eleve->getAbsenceEleveSaisiesDuJour('2013-06-08');
        $this->assertTrue($saisies->getCurrent()->getManquementObligationPresenceEnglobante());
        $this->assertTrue($saisies->getNext()->getManquementObligationPresenceEnglobante());
        saveSetting('abs2_saisie_multi_type_sans_manquement','y');
        $saisies->getFirst()->clearAllReferences();
        $this->assertFalse($saisies->getFirst()->getManquementObligationPresenceEnglobante());
        saveSetting('abs2_saisie_multi_type_sans_manquement','n');

        $saisie = AbsenceEleveSaisieQuery::create()->filterByFinAbs('2013-06-09 09:00:00')->findOne();
        $this->assertTrue($saisie->getManquementObligationPresenceEnglobante());
        saveSetting('abs2_saisie_multi_type_sans_manquement','y');
        $saisie->clearAllReferences();
        $this->assertFalse($saisie->getManquementObligationPresenceEnglobante());
        saveSetting('abs2_saisie_multi_type_sans_manquement','n');

        $saisie = new AbsenceEleveSaisie();
        $this->assertTrue($saisie->getManquementObligationPresenceEnglobante());
    }

    public function testRetardEnglobante()
    {
        saveSetting('abs2_retard_critere_duree',30);
        $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-05')->getFirst();
        $this->assertFalse($saisie->getRetardEnglobante());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-06')->getFirst();
        $this->assertFalse($saisie->getRetardEnglobante());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-07')->getFirst();
        $this->assertFalse($saisie->getRetardEnglobante());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-08')->getFirst();
        $this->assertTrue($saisie->getRetardEnglobante());

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-09')->getFirst();
        saveSetting('abs2_retard_critere_duree',30);
        $this->assertTrue($saisie->getRetardEnglobante());
        $saisie->clearAllReferences();
        saveSetting('abs2_retard_critere_duree',20);
        $this->assertFalse($saisie->getRetardEnglobante());
        saveSetting('abs2_retard_critere_duree',30);

        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-10')->getFirst();//sur cette saisie on a plusieurs traitement, on privilégie le retard
        $this->assertTrue($saisie->getRetardEnglobante());
 
        $saisie = AbsenceEleveSaisieQuery::create()->filterByFinAbs('2012-10-12 08:10:00')->findOne();
        $this->assertFalse($saisie->getRetardEnglobante());

        $saisie = AbsenceEleveSaisieQuery::create()->filterByFinAbs('2012-10-14 08:10:00')->findOne();
        $this->assertTrue($saisie->getRetardEnglobante());

        $saisie = AbsenceEleveSaisieQuery::create()->filterByFinAbs('2012-10-15 08:10:00')->findOne();
        $this->assertTrue($saisie->getRetardEnglobante());

        $saisie = AbsenceEleveSaisieQuery::create()->filterByFinAbs('2012-10-19 08:10:00')->findOne();
        $this->assertFalse($saisie->getRetardEnglobante());

        $saisies = AbsenceEleveSaisieQuery::create()->filterByFinAbs('2012-10-22 09:00:00')->find();
        $this->assertTrue($saisies->getFirst()->getRetardEnglobante());
        $this->assertTrue($saisies->getNext()->getRetardEnglobante());
        
        $saisie = AbsenceEleveSaisieQuery::create()->filterByFinAbs('2013-05-28 09:10:00')->findOne();
        $this->assertFalse($saisie->getRetardEnglobante());

        $saisie = AbsenceEleveSaisieQuery::create()->filterByFinAbs('2013-05-29 08:10:00')->findOne();
        $this->assertTrue($saisie->getRetard());
        $this->assertFalse($saisie->getRetardEnglobante());

        $saisie = AbsenceEleveSaisieQuery::create()->filterByFinAbs('2013-06-06 08:10:00')->findOne();
        $this->assertTrue($saisie->getRetardEnglobante());

        $saisie = AbsenceEleveSaisieQuery::create()->filterByFinAbs('2013-06-10 08:10:00')->findOne();
        $this->assertFalse($saisie->getRetardEnglobante());
    }


}
