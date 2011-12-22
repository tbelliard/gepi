<?php

require_once dirname(__FILE__) . '/../../tools/helpers/orm/GepiEmptyTestBase.php';
require_once dirname(__FILE__) . '/../../../orm/helpers/AbsencesEleveSaisieHelper.php';

/**
 * Test class for UtilisateurProfessionnel.
 *
 */
class AbsenceEleveSaisieHelperTest extends GepiEmptyTestBase
{
	protected function setUp()
	{
		parent::setUp();
		GepiDataPopulator::populate();
	}

	public function testCompte_demi_journee()
	{
		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		$saisie_col = AbsenceEleveSaisieQuery::create()->orderByDebutAbs(Criteria::DESC);
        try {
            AbsencesEleveSaisieHelper::compte_demi_journee($saisie_col);
            $this->fail('Une exception doit être soulevée lors de cette sauvegarde');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
        
        $this->assertTrue(AbsencesEleveSaisieHelper::compte_demi_journee(new PropelCollection())->isEmpty());
        
        $saisie_col = new PropelCollection();
        
		$saisie = new AbsenceEleveSaisie();
        $saisie->setDebutAbs('2010-11-01 08:00:00');
        $saisie->setFinAbs('2010-11-01 13:00:00');
        $saisie_col->append($saisie);
		saveSetting('abs2_heure_demi_journee','11:50');
        $this->assertEquals(1,AbsencesEleveSaisieHelper::compte_demi_journee($saisie_col)->count());
        
		saveSetting('abs2_heure_demi_journee','11:10');
        $this->assertEquals(2,AbsencesEleveSaisieHelper::compte_demi_journee($saisie_col)->count());
		saveSetting('abs2_heure_demi_journee','11:50');
        
        $saisie = new AbsenceEleveSaisie();
        $saisie->setDebutAbs('2010-11-01 09:00:00');
        $saisie->setFinAbs('2010-11-01 17:00:00');
        $saisie_col->append($saisie);
        $this->assertEquals(2,AbsencesEleveSaisieHelper::compte_demi_journee($saisie_col)->count());

		$saisie = new AbsenceEleveSaisie();
        $saisie->setDebutAbs('2010-11-01 09:00:00');
        $saisie->setFinAbs('2010-11-01 17:00:00');
        $saisie_col->append($saisie);
        $this->assertEquals(2,AbsencesEleveSaisieHelper::compte_demi_journee($saisie_col)->count());

		$saisie = new AbsenceEleveSaisie();
        $saisie->setDebutAbs('2010-11-02 10:00:00');
        $saisie->setFinAbs('2010-11-02 10:50:00');
        $saisie_col->append($saisie);
        $this->assertEquals(3,AbsencesEleveSaisieHelper::compte_demi_journee($saisie_col)->count());

		$saisie = new AbsenceEleveSaisie();
        $saisie->setDebutAbs('2010-11-02 11:00:00');
        $saisie->setFinAbs('2010-11-02 13:30:00');
        $saisie_col->append($saisie);
        $this->assertEquals(3,AbsencesEleveSaisieHelper::compte_demi_journee($saisie_col)->count());
        
		$saisie = new AbsenceEleveSaisie();
        $saisie->setDebutAbs('2010-11-02 11:00:00');
        $saisie->setFinAbs('2010-11-02 17:30:00');
        $saisie_col->append($saisie);
        $this->assertEquals(4,AbsencesEleveSaisieHelper::compte_demi_journee($saisie_col)->count());

		$saisie = new AbsenceEleveSaisie();
        $saisie->setDebutAbs('2010-11-02 11:00:00');
        $saisie->setFinAbs('2010-11-03 10:30:00');
        $saisie_col->append($saisie);
        $this->assertEquals(5,AbsencesEleveSaisieHelper::compte_demi_journee($saisie_col)->count());

		$saisie = new AbsenceEleveSaisie();
        $saisie->setDebutAbs('2010-11-02 11:00:00');
        $saisie->setFinAbs('2010-11-03 17:30:00');//c'est un mercredi am donc on le compte pas, le total resta à 5
        $saisie_col->append($saisie);
        $this->assertEquals(5,AbsencesEleveSaisieHelper::compte_demi_journee($saisie_col)->count());
        
		$saisie = new AbsenceEleveSaisie();
        $saisie->setDebutAbs('2010-11-03 16:00:00');
        $saisie->setFinAbs('2010-11-03 17:30:00');
        $saisie_col->append($saisie);
        $this->assertEquals(5,AbsencesEleveSaisieHelper::compte_demi_journee($saisie_col)->count());
        
		$saisie = new AbsenceEleveSaisie();
        $saisie->setDebutAbs('2010-11-03 16:00:00');
        $saisie->setFinAbs('2010-11-04 02:00:00');
        $saisie_col->append($saisie);
        $this->assertEquals(5,AbsencesEleveSaisieHelper::compte_demi_journee($saisie_col)->count());
        
		$saisie = new AbsenceEleveSaisie();
        $saisie->setDebutAbs('2010-11-03 16:00:00');
        $saisie->setFinAbs('2010-11-04 09:00:00');
        $saisie_col->append($saisie);
        $this->assertEquals(6,AbsencesEleveSaisieHelper::compte_demi_journee($saisie_col)->count());
        
		$saisie = new AbsenceEleveSaisie();
        $saisie->setDebutAbs('2010-11-04 15:00:00');
        $saisie->setFinAbs('2010-11-04 16:00:00');
        $saisie_col->append($saisie);
        $this->assertEquals(7,AbsencesEleveSaisieHelper::compte_demi_journee($saisie_col)->count());
        
        $saisie_col = new PropelCollection();
        $saisie = new AbsenceEleveSaisie();
        $saisie->setDebutAbs('2010-11-05 14:00:00');
        $saisie->setFinAbs('2010-11-05 15:00:00');
        $saisie_col->append($saisie);
            saveSetting('abs2_heure_demi_journee','12:50');
        $this->assertEquals('12:00',AbsencesEleveSaisieHelper::compte_demi_journee($saisie_col)->getFirst()->format('H:i'));
            saveSetting('abs2_heure_demi_journee','11:50'); 

		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		$lebesgue_prof = UtilisateurProfessionnelQuery::create()->findOneByLogin('Lebesgue');
        $saisie = new AbsenceEleveSaisie();
        $saisie->setDebutAbs('2010-10-19 08:00:00');
        $saisie->setFinAbs('2010-10-19 16:30:00');
        $saisie->setEleve($florence_eleve);
        $saisie->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie->save();
        $saisie_col = new PropelCollection();
        $saisie_col->append($saisie);
        $this->assertEquals(2,AbsencesEleveSaisieHelper::compte_demi_journee($saisie_col)->count());

        //on va tester les effets de borne d'intervalle pour la bascule de midi
        saveSetting('abs2_heure_demi_journee','11:55');
        $saisie_col = new PropelCollection();
        $saisie = new AbsenceEleveSaisie();
        $saisie->setDebutAbs('2010-11-05 11:55:00');//cette saisie va compter pour une après midi
        $saisie->setFinAbs('2010-11-05 12:25:00');
        $saisie_col->append($saisie);
        $this->assertEquals('12:00',AbsencesEleveSaisieHelper::compte_demi_journee($saisie_col)->getFirst()->format('H:i'));
        $saisie_col = new PropelCollection();
        $saisie = new AbsenceEleveSaisie();
        $saisie->setDebutAbs('2010-11-05 11:50:00');
        $saisie->setFinAbs('2010-11-05 12:25:00');
        $saisie_col->append($saisie);
        $this->assertEquals('00:00',AbsencesEleveSaisieHelper::compte_demi_journee($saisie_col)->getFirst()->format('H:i'));
	}
	
}
