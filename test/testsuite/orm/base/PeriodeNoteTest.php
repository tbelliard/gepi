<?php

require_once dirname(__FILE__) . '/../../../tools/helpers/orm/GepiEmptyTestBase.php';

/**
 * Test class for UtilisateurProfessionnel.
 *
 */
class PeriodeNoteTest extends GepiEmptyTestBase
{
	protected function setUp()
	{
		parent::setUp();
		GepiDataPopulator::populate();
	}

	public function testGetDateDebut()
	{
		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		$periode_col = $florence_eleve->getPeriodeNotes();
		$this->assertEquals('2',$periode_col->count());
		$this->assertEquals('1',$periode_col->getFirst()->getNumPeriode());
		$this->assertEquals('2',$periode_col->getLast()->getNumPeriode());
		
		$periode_1 = $periode_col->getFirst();
		$this->assertEquals('2010-12-01 23:59:59',$periode_1->getDateFin('Y-m-d H:i:s'));
		$this->assertEquals('2010-08-31 00:00:00',$periode_1->getDateDebut('Y-m-d H:i:s'));
		
		$periode_2 = $periode_col->getLast();
		$this->assertEquals('2011-03-01 23:59:59',$periode_2->getDateFin('Y-m-d H:i:s'));
		$this->assertEquals('2010-12-02 00:00:00',$periode_2->getDateDebut('Y-m-d H:i:s'));
	}
	
}
