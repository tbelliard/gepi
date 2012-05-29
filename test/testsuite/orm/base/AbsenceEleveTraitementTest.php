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
         
        saveSetting('abs2_saisie_par_defaut_sans_manquement','n');
        $traitements = AbsenceEleveTraitementQuery::create()->filterByManquementObligationPresence(true)
        ->useJTraitementSaisieEleveQuery()->useAbsenceEleveSaisieQuery()
        ->filterByEleve($florence_eleve)->filterByPlageTemps(new DateTime('2010-10-01'),new DateTime('2010-10-17 23:59:59'))
        ->endUse()->endUse()->find();
        $this->assertEquals(12,$traitements->count());
        $traitements = AbsenceEleveTraitementQuery::create()->filterByManquementObligationPresence(false)
        ->useJTraitementSaisieEleveQuery()->useAbsenceEleveSaisieQuery()
        ->filterByEleve($florence_eleve)->filterByPlageTemps(new DateTime('2010-10-01'),new DateTime('2010-10-17 23:59:59'))
        ->endUse()->endUse()->find();
        $this->assertEquals(7,$traitements->count());

        saveSetting('abs2_saisie_par_defaut_sans_manquement','y');
        $traitements = AbsenceEleveTraitementQuery::create()->filterByManquementObligationPresence(true)
        ->useJTraitementSaisieEleveQuery()->useAbsenceEleveSaisieQuery()
        ->filterByEleve($florence_eleve)->filterByPlageTemps(new DateTime('2010-10-01'),new DateTime('2010-10-17 23:59:59'))
        ->endUse()->endUse()->find();
        $this->assertEquals(10,$traitements->count());

        saveSetting('abs2_saisie_par_defaut_sans_manquement','n');
    }

    public function testUpdateAgregationTable()
    {
        AbsenceAgregationDecompteQuery::create()->deleteAll();
        $traitement = AbsenceEleveTraitementQuery::create()->useJTraitementSaisieEleveQuery()->useAbsenceEleveSaisieQuery()->filterByDebutAbs('2010-10-17 14:00:00')->endUse()->endUse()->findOne();
        $traitement->updateAgregationTable();
        $this->assertEquals(3,AbsenceAgregationDecompteQuery::create()->count());
    }

    public function testSave()
    {
        global $_SESSION;
        $_SESSION['login'] = 'Lebesgue';
         
        $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
         
        $saisie_3 = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-02')->getFirst();
        $traitement = new AbsenceEleveTraitement();
        $traitement->addAbsenceEleveSaisie($saisie_3);
        $traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->filterByNom('Exclusion de cours')->findOne());
        $traitement->save();
        $this->assertEquals('Lebesgue',$traitement->getUtilisateurId());
        $traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->filterByNom('Absence scolaire')->findOne());
        $traitement->save();
        $this->assertEquals('Lebesgue',$traitement->getModifieParUtilisateurId());

        AbsenceEleveTraitementPeer::disableAgregation();
        $traitement = AbsenceEleveTraitementQuery::create()->useJTraitementSaisieEleveQuery()
        ->useAbsenceEleveSaisieQuery()->filterByDebutAbs('2010-10-14 08:00:00')
        ->endUse()->endUse()->findOne();
        $traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->filterByNom('Infirmerie')->findOne());
        $traitement->save();
        $decompte = AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByDateDemiJounee('2010-10-14')->findOne();
        $this->assertTrue($decompte == null || $decompte->getManquementObligationPresence() == true);
         
        AbsenceEleveTraitementPeer::enableAgregation();
        $traitement->save();
        $decompte = AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByDateDemiJounee('2010-10-14')->findOne();
        $this->assertFalse($decompte->getManquementObligationPresence());
        $traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->filterByNom('Absence scolaire')->findOne());
        $traitement->save();
        $decompte = AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByDateDemiJounee('2010-10-14')->findOne();
        $this->assertTrue($decompte->getManquementObligationPresence());
        AbsenceEleveTraitementPeer::disableAgregation();
    }
    
    public function testDelete()
    {
        $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');

        $traitement = AbsenceEleveTraitementQuery::create()->useJTraitementSaisieEleveQuery()
        ->useAbsenceEleveSaisieQuery()->filterByDebutAbs('2010-10-14 08:00:00')
        ->endUse()->endUse()->findOne();
        //on va vérifier que le delete change bien le update_ad
        $old_updated_at = $traitement->getUpdatedAt('U');
        $traitement->delete();
        $traitement = AbsenceEleveTraitementQuery::create()->useJTraitementSaisieEleveQuery()
        ->useAbsenceEleveSaisieQuery()->filterByDebutAbs('2010-10-14 08:00:00')
        ->endUse()->endUse()->findOne();
        $this->assertNull($traitement);
        $traitement = AbsenceEleveTraitementQuery::create()->includeDeleted()->useJTraitementSaisieEleveQuery()
        ->useAbsenceEleveSaisieQuery()->filterByDebutAbs('2010-10-14 08:00:00')
        ->endUse()->endUse()->findOne();
        $this->assertNotNull($traitement);
        $traitement->unDelete();

        AbsenceEleveTraitementPeer::enableAgregation();
        $traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->filterByNom('Infirmerie')->findOne());
        $traitement->save();
        $decompte = AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByDateDemiJounee('2010-10-14')->findOne();
        $this->assertFalse($decompte->getManquementObligationPresence());
        $traitement->delete();
        $decompte->reload();
        $this->assertTrue($decompte->getManquementObligationPresence());
        AbsenceEleveTraitementPeer::disableAgregation();
    }
}
