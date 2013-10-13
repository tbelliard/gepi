<?php

require_once dirname(__FILE__) . '/../../../tools/helpers/orm/GepiEmptyTestBase.php';
require_once dirname(__FILE__) . '/init_date.php';

/**
 * Test class for UtilisateurProfessionnel.
 *
 */
class JTraitementSaisieTest extends GepiEmptyTestBase
{
    protected function setUp()
    {
        parent::setUp();
        GepiDataPopulator::populate();
    }

    public function testInsert()
    {
        $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
        $florence_eleve->updateAbsenceAgregationTable();
        AbsenceEleveSaisiePeer::disableAgregation();
        AbsenceEleveTraitementPeer::disableAgregation();
        $traitement = new AbsenceEleveTraitement();
        $traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->filterByNom('Infirmerie')->findOne());
        $traitement->save();
        $traitement->addAbsenceEleveSaisie($florence_eleve->getAbsenceEleveSaisiesDuJour(VENDREDI_s40j5)->getFirst());
        $traitement->save();
        $decompte = AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByDateDemiJounee(VENDREDI_s40j5)->findOne();
        $this->assertTrue($decompte->getManquementObligationPresence());
        $traitement->delete();
        $decompte->reload();
        $this->assertTrue($decompte->getManquementObligationPresence());
        
        $traitement = new AbsenceEleveTraitement();
        $traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->filterByNom('Infirmerie')->findOne());
        $traitement->save();
        AbsenceEleveSaisiePeer::enableAgregation();
        AbsenceEleveTraitementPeer::enableAgregation();
        $traitement->addAbsenceEleveSaisie($florence_eleve->getAbsenceEleveSaisiesDuJour(VENDREDI_s40j5)->getFirst());
        $traitement->save();
        $decompte->reload();
        $this->assertFalse($decompte->getManquementObligationPresence());
                
        AbsenceEleveTraitementPeer::disableAgregation();
        AbsenceEleveSaisiePeer::disableAgregation();
    }

   public function testModification()
    {
        $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
        $saisie = AbsenceEleveSaisieQuery::create()->filterByDebutAbs(DIMANCHE_s41j7.' 08:00:00')->findOne();
        $traitements = AbsenceEleveTraitementQuery::create()->useAbsenceEleveTypeQuery()->filterByNom('Infirmerie')->endUse()->find();
        $traitement_1 = $traitements->getFirst();
        $traitement_2 = $traitements->get(1);
        
        $j_traitement_saisie = $traitement_1->getJTraitementSaisieEleves()->getFirst();
        try {
            $j_traitement_saisie->setAbsenceEleveTraitement($traitement_2);
            $this->fail('Une exception doit être soulevée lors de cette modification');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
        try {
            $j_traitement_saisie->setAbsenceEleveSaisie($saisie);
            $this->fail('Une exception doit être soulevée lors de cette modification');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
            try {
            $j_traitement_saisie->setATraitementId($traitement_2->getId());
            $this->fail('Une exception doit être soulevée lors de cette modification');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
            try {
            $j_traitement_saisie->setASaisieId($saisie->getId());
            $this->fail('Une exception doit être soulevée lors de cette modification');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }
    
    public function testDelete()
    {
        $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
        $florence_eleve->updateAbsenceAgregationTable();
        AbsenceEleveSaisiePeer::enableAgregation();
        AbsenceEleveTraitementPeer::enableAgregation();
        $traitement = new AbsenceEleveTraitement();
        $traitement->setAbsenceEleveType(AbsenceEleveTypeQuery::create()->filterByNom('Infirmerie')->findOne());
        $traitement->save();
        $traitement->addAbsenceEleveSaisie($florence_eleve->getAbsenceEleveSaisiesDuJour(VENDREDI_s40j5)->getFirst());
        $traitement->save();
        $decompte = AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByDateDemiJounee(VENDREDI_s40j5)->findOne();
        $this->assertFalse($decompte->getManquementObligationPresence());
        $j_traitement_saisie = $traitement->getJTraitementSaisieEleves()->getFirst();
        $j_traitement_saisie->delete();
        $decompte->reload();
        $this->assertTrue($decompte->getManquementObligationPresence());
        
                
        AbsenceEleveTraitementPeer::disableAgregation();
        AbsenceEleveSaisiePeer::disableAgregation();
    }
}
