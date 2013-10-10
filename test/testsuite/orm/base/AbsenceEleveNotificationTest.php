<?php

require_once dirname(__FILE__) . '/../../../tools/helpers/orm/GepiEmptyTestBase.php';

/**
 * Test class for UtilisateurProfessionnel.
 *
 */
class AbsenceEleveNotificationTest extends GepiEmptyTestBase
{
    protected function setUp()
    {
        parent::setUp();
        GepiDataPopulator::populate();
    }

    public function testPreremplirResponsables()
    {
        $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
        $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2012-10-06')->getFirst();
        $traitement = $saisie->getAbsenceEleveTraitements()->getFirst();
        $notification = $traitement->getAbsenceEleveNotifications()->getFirst();
        $this->assertEquals($notification->getEmail(), null);
        $this->assertEquals($notification->getResponsableEleves()->count(), 0);
        $result = $notification->preremplirResponsables();
        $this->assertTrue($result);
        $this->assertEquals($notification->getEmail(), 'mail@test');
        $this->assertEquals($notification->getResponsableEleves()->count(), 2);


        //on va changer le responsables 2 de florence
        foreach ($florence_eleve->getResponsableInformations() as $resp_info) {
            if ($resp_info->getNiveauResponsabilite() == '2') {
                $resp = $resp_info->getResponsableEleve();
                $resp->setAdresseId(null);
                $resp->save();
            }
        }
        $empty_col= new PropelObjectCollection();
        $notification->setResponsableEleves($empty_col);
        $this->assertEquals($notification->getResponsableEleves()->count(), 0);
        $result = $notification->preremplirResponsables();
        $this->assertTrue($result);
        $this->assertEquals($notification->getEmail(), 'mail@test');
        $this->assertEquals($notification->getResponsableEleves()->count(), 1);

        //on va rajouter un responsable 1
        $responsable = new ResponsableEleve();
        $responsable->setCivilite('M.');
        $responsable->setNom('Michu');
        $responsable->setMel('mail@test');
        $responsable->setPrenom('Mere');
        $responsable->setResponsableEleveId('id 5');
        $responsable->save();
        $responsable_info = new ResponsableInformation();
        $responsable_info->setEleve($florence_eleve);
        $responsable_info->setNiveauResponsabilite(1);
        $responsable_info->setResponsableEleve($responsable);
        $responsable_info->save();
        $empty_col= new PropelObjectCollection();
        $notification->setResponsableEleves($empty_col);
        $this->assertEquals($notification->getResponsableEleves()->count(), 0);
        $result = $notification->preremplirResponsables();
        $this->assertFalse($result);
        $this->assertEquals($notification->getEmail(), 'mail@test');
        $this->assertEquals($notification->getResponsableEleves()->count(), 0);
    }
}
