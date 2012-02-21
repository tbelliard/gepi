<?php

require_once dirname(__FILE__) . '/../../tools/helpers/orm/GepiEmptyTestBase.php';

/**
 * Test class for UtilisateurProfessionnel.
 *
 */
class PropelCollectionTest extends GepiEmptyTestBase
{
    protected function setUp()
    {
        parent::setUp();
        GepiDataPopulator::populate();
    }


    /**
    * Test that PropelCollection->add() prevents duplicates of objects strictly identical
    *
    */
   public function testAdd()
    {
        Propel::disableInstancePooling();
        $eleve1 = EleveQuery::create()->findOneByLogin('Florence Michu');
        $eleve1->setNom('test_different');
        $eleve_col = new PropelCollection();
        $eleve_col->add($eleve1);
        $eleve1idem = EleveQuery::create()->findOneByLogin('Florence Michu');
        $this->assertFalse($eleve_col->contains($eleve1idem));
        $eleve_col->add($eleve1idem);
        $this->assertEquals(2, count($eleve_col));
        Propel::enableInstancePooling();


        $eleve1 = EleveQuery::create()->findOneByLogin('Florence Michu');
        $eleve_col = new PropelCollection();
        $eleve_col->add($eleve1);
        $eleve_col->add($eleve1);
        $this->assertEquals(1, count($eleve_col));
    }

    /**
    * test a propel bug corrected in commit f23575d of propel github repo
    *
    */
   public function testInstancePooling() {
        ElevePeer::clearInstancePool();
        foreach (AbsenceEleveSaisieQuery::create()->useEleveQuery()->filterByLogin('Florence Michu')->endUse()->find() as $saisie) {
                $eleve_col[] = $saisie->getEleve();
        }
        $this->assertTrue($eleve_col[0] === $eleve_col[1]);
    }
}
