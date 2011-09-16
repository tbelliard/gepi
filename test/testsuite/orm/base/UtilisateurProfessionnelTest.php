<?php

require_once dirname(__FILE__) . '/../../../tools/helpers/orm/GepiEmptyTestBase.php';

/**
 * Test class for UtilisateurProfessionnel.
 *
 */
class UtilisateurProfessionnelTest extends GepiEmptyTestBase
{
	protected function setUp()
	{
		parent::setUp();
		GepiDataPopulator::populate();
	}

	public function testGetGroupesStatutProfesseur()
	{
		$lebesgue = UtilisateurProfessionnelQuery::create()->findOneByLogin('Lebesgue');
		$groupes = $lebesgue->getGroupes();
		$this->assertEquals(1,$groupes->count());
		$this->assertEquals('MATH6A',$groupes->getFirst()->getName());
	}
	
	public function testGetGroupesStatutCpe()
	{
		$dolto = UtilisateurProfessionnelQuery::create()->findOneByLogin('Dolto');
		$groupes = $dolto->getGroupes();
		$this->assertEquals(1,$groupes->count());
		$this->assertEquals('MATH6A',$groupes->getFirst()->getName());
	}
	
}
