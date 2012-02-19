<?php

require_once dirname(__FILE__) . '/../../tools/helpers/orm/GepiEmptyTestBase.php';

/**
 * Test class for UtilisateurProfessionnel.
 *
 */
class PropelObjectCollectionTest extends GepiEmptyTestBase
{
    protected function setUp()
    {
        parent::setUp();
        GepiDataPopulator::populate();
    }


    /**
    * Test that PropelObjectCollection->add() prevents duplicates of objects with same primaryKey
    *
    */
   public function testAdd()
    {
        Propel::disableInstancePooling();
        $eleve1 = EleveQuery::create()->findOneByLogin('Florence Michu');
        $eleve1->setNom(rand(0,1000));
        $eleve_col = new PropelObjectCollection();
        $eleve_col->add($eleve1);
        $eleve1idem = EleveQuery::create()->findOneByLogin('Florence Michu');
        $eleve_col->add($eleve1idem);
        $this->assertEquals(1, count($eleve_col));


        $eleve1 = EleveQuery::create()->findOneByLogin('Florence Michu');
        $eleve1->setPrimaryKey('test');
        $eleve_col = new PropelObjectCollection();
        $eleve_col->add($eleve1);
        $eleve1idem = EleveQuery::create()->findOneByLogin('Florence Michu');
        $eleve_col->add($eleve1idem);
        $this->assertEquals(2, count($eleve_col));
        Propel::enableInstancePooling();

    }
}
