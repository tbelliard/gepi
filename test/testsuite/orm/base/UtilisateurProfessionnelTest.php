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
	
	public function testGetGroupesStatutScolarite()
	{
		$aubert = UtilisateurProfessionnelQuery::create()->findOneByLogin('Aubert');
		$groupes = $aubert->getGroupes();
		$this->assertEquals(1,$groupes->count());
		$this->assertEquals('MATH6A',$groupes->getFirst()->getName());
	}
	
	public function testGetClassesStatutScolarite()
	{
		$aubert = UtilisateurProfessionnelQuery::create()->findOneByLogin('Aubert');
		$classes = $aubert->getClasses();
		$this->assertEquals(1,$classes->count());
		$this->assertEquals('6ieme A',$classes->getFirst()->getNom());
	}
	
	public function testGetClassesStatutProfesseur()
	{
		$newton = UtilisateurProfessionnelQuery::create()->findOneByLogin('Newton');
		$classes = $newton->getClasses();
		$this->assertEquals(2,$classes->count());
		
		$lebesgue = UtilisateurProfessionnelQuery::create()->findOneByLogin('Lebesgue');
		$classes = $lebesgue->getClasses();//Lebesque n'a aucun eleve en tant que professeur principal
		$this->assertEquals(0,$classes->count());
	}
	
	public function testGetClassesStatutCpe()
	{
		$dolto = UtilisateurProfessionnelQuery::create()->findOneByLogin('Dolto');
		$classes = $dolto->getClasses();
		$this->assertEquals(2,$classes->count());
		$this->assertEquals('6ieme A',$classes->getFirst()->getNom());
	}
	
	public function testGetPreferenceValeur()
	{
		$lebesgue = UtilisateurProfessionnelQuery::create()->findOneByLogin('Lebesgue');
		$value = $lebesgue->getPreferenceValeur('glace_parfum');
		$this->assertEquals('chocolat',$value);
	}
	
	public function testGetEleveStatutProfesseurPrincipal()
	{
		$newton = UtilisateurProfessionnelQuery::create()->findOneByLogin('Newton');
		$eleves = $newton->getEleves();
		$this->assertEquals(1,$eleves->count());
		
		$lebesgue = UtilisateurProfessionnelQuery::create()->findOneByLogin('Lebesgue');
		$eleves = $lebesgue->getEleves();//Lebesque n'a aucun eleve en tant que professeur principal
		$this->assertEquals(0,$eleves->count());
	}
	
	public function testGetEleveStatutCpe()
	{
		$dolto = UtilisateurProfessionnelQuery::create()->findOneByLogin('Dolto');
		$eleves = $dolto->getEleves();
		$this->assertEquals(1,$eleves->count());
	}
	
	public function testGetAidDetailssStatutScolarite()
	{
		$aubert = UtilisateurProfessionnelQuery::create()->findOneByLogin('Aubert');
		$aid_detailss = $aubert->getAidDetailss();
		$this->assertEquals(1,$aid_detailss->count());
		$this->assertEquals('aid 1',$aid_detailss->getFirst()->getNom());
	}
	
	public function testGetAidDetailssStatutProfesseur()
	{
		$newton = UtilisateurProfessionnelQuery::create()->findOneByLogin('Newton');
		$aid_detailss = $newton->getAidDetailss();
		$this->assertEquals(1,$aid_detailss->count());
		
		$lebesgue = UtilisateurProfessionnelQuery::create()->findOneByLogin('Lebesgue');
		$aid_detailss = $lebesgue->getAidDetailss();
		$this->assertEquals(0,$aid_detailss->count());
	}
	
	public function testGetAidDetailssStatutCpe()
	{
		$dolto = UtilisateurProfessionnelQuery::create()->findOneByLogin('Dolto');
		$aid_detailss = $dolto->getAidDetailss();
		$this->assertEquals(1,$aid_detailss->count());
	}
	
}
