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
		//pas implémentée
		//le modele n'est pas clair : dans la table j_eleves_professeurs (pour les profs principaux) on peut associer des classes ET des élèves, ce qui n'est pas consitant.
		//a voir avec la méthode testGetEleveStatutProfesseurPrincipal(), en fonction du modele choisi (association pp<->eleve ou pp<->classe
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
	
}
