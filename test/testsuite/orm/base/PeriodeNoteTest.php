<?php

require_once dirname(__FILE__) . '/../../../tools/helpers/orm/GepiEmptyTestBase.php';
require_once dirname(__FILE__) . '/init_date.php';

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
		$this->assertEquals('3',$periode_col->count());
		$this->assertEquals('1',$periode_col->getFirst()->getNumPeriode());
		$this->assertEquals('3',$periode_col->getLast()->getNumPeriode());
		
		$periode_1 = $periode_col->getFirst();
		$this->assertEquals(SAMEDI_s48j6.' 23:59:59',$periode_1->getDateFin('Y-m-d H:i:s'));
		$this->assertEquals(VENDREDI_s35j5.' 00:00:00',$periode_1->getDateDebut('Y-m-d H:i:s'));
		
		$periode_3 = $periode_col->getLast();
		$this->assertEquals(LUNDI_a1_s27j1.' 23:59:59',$periode_3->getDateFin('Y-m-d H:i:s'));
		$this->assertEquals(SAMEDI_a1_s9j6.' 00:00:00',$periode_3->getDateDebut('Y-m-d H:i:s'));

	}
	
}
