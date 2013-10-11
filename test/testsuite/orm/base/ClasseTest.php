<?php

require_once dirname(__FILE__) . '/../../../tools/helpers/orm/GepiEmptyTestBase.php';

/**
 * Test class for UtilisateurProfessionnel.
 *
 */
class ClasseTest extends GepiEmptyTestBase
{
	protected function setUp()
	{
		parent::setUp();
		GepiDataPopulator::populate();
	}

	public function testGetPeriodeNote()
	{
		$sixieme_A = ClasseQuery::create()->findOneByNom('6ieme A');
		$periode_col = $sixieme_A->getPeriodeNotes();
		$this->assertEquals('2',$periode_col->count());
		$this->assertEquals('1',$periode_col->getFirst()->getNumPeriode());
		$this->assertEquals('2',$periode_col->getLast()->getNumPeriode());
				
		$periode = $sixieme_A->getPeriodeNote('2011-12-01');
		$this->assertEquals('2',$periode->getNumPeriode(),'à la date du 2011-12-01, il doit y avoir la derniére période par défaut');
		
		$periode = $sixieme_A->getPeriodeNote('2012-10-02');
		$this->assertNotNull($periode,'à la date du 2012-10-02, il ne doit y avoir la première période d assignée, donc on doit retourner null');
                $this->assertEquals('1',$periode->getNumPeriode());

		$periode = $sixieme_A->getPeriodeNote('2012-12-11');
		$this->assertNotNull($periode,'à la date du 2012-12-11, il ne doit y avoir la deuxième période d assignée, donc on doit retourner null');
                $this->assertEquals('2',$periode->getNumPeriode());

		$periode = $sixieme_A->getPeriodeNote('2013-05-11');
		$this->assertEquals('2',$periode->getNumPeriode(),'à la date du 2013-05-11, il doit y avoir la derniére période par défaut');
                
                //on rajoute une autre période, dont la date de fin est non renseignée
                $periode_6A_3 = new PeriodeNote();
                $periode_6A_3->setClasse($sixieme_A);
                $periode_6A_3->setNumPeriode(3);
                $periode_6A_3->setVerouiller('O');
                $periode_6A_3->setNomPeriode('troisième trimestre');
                $periode_6A_3->save();

		$periode = $sixieme_A->getPeriodeNote('2013-05-11');
		$this->assertNotNull($periode,'à la date du 2013-05-11, il ne doit y avoir la troisième période d assignée meme si elle n est pas terminée');
                $this->assertEquals('3',$periode->getNumPeriode());
	}
	
}
