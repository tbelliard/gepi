<?php

require_once dirname(__FILE__) . '/../../../tools/helpers/orm/GepiEmptyTestBase.php';

/**
 * Test class for UtilisateurProfessionnel.
 *
 */
class EleveTest extends GepiEmptyTestBase
{
	protected function setUp()
	{
		parent::setUp();
		GepiDataPopulator::populate();
	}

	public function testGetPeriodeNote()
	{
		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		$periode_col = $florence_eleve->getPeriodeNotes();
		$this->assertEquals('3',$periode_col->count());
		$this->assertEquals('1',$periode_col->getFirst()->getNumPeriode());
		$this->assertEquals('3',$periode_col->getLast()->getNumPeriode());
				
		$periode = $florence_eleve->getPeriodeNote();
		$this->assertNotNull($periode,'à la date en cours, il ne doit y avoir aucune période d assigné, donc on doit retourner la dernière période');
		$this->assertEquals('3',$periode->getNumPeriode());
		
		$periode_2 = $florence_eleve->getPeriodeNoteOuverte();
		$this->assertNotNull($periode_2,'La période de note ouverte de florence ne doit pas être nulle');
		$this->assertEquals('2',$periode_2->getNumPeriode());
		
		//on va fermer la période
		//$periode = new PeriodeNote();
		$periode_2->setVerouiller('O');
		$periode_2->save();
		$florence_eleve->reload();
		$periode_col = $florence_eleve->getPeriodeNotes();
		$this->assertEquals('3',$periode_col->count());
		$this->assertNull($florence_eleve->getPeriodeNoteOuverte(),'Après verrouillage la période ouverte de note de florence doit être nulle');
		
		$periode = $florence_eleve->getPeriodeNote(new DateTime('2010-10-01'));
		$this->assertNotNull($periode);
		$this->assertEquals('1',$periode->getNumPeriode());
		
		$periode = $florence_eleve->getPeriodeNote(new DateTime('2010-12-05'));
		$this->assertNotNull($periode);
		$this->assertEquals('2',$periode->getNumPeriode());
		
		$michel_eleve = EleveQuery::create()->findOneByLogin('Michel Martin');
		$this->assertEquals(0,$michel_eleve->getPeriodeNotes()->count());
		
		$periode = $michel_eleve->getPeriodeNote(new DateTime('2010-12-05'));
		$this->assertNull($periode);
		
		$periode = $michel_eleve->getPeriodeNote();
		$this->assertNull($periode);
	}
	
	public function testGetClasse()
	{
		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		$classe = $florence_eleve->getClasse(1);//on récupère la classe pour la période 1
		$this->assertNotNull($classe,'La classe de florence ne doit pas être nulle pour la période 1');
		$this->assertEquals('6ieme A',$classe->getNom());

		$classe = $florence_eleve->getClasse(5);//on récupère la classe pour la période 1
		$this->assertNull($classe,'La classe de florence doit pas être nulle pour la période 5');

		$classe = $florence_eleve->getClasse(new DateTime('2010-10-01'));
		$this->assertNotNull($classe,'La classe de florence ne doit pas être nulle pour la date 2010-10-01 (période 1)');
		$this->assertEquals('6ieme A',$classe->getNom());
		
		$classe = $florence_eleve->getClasse(new DateTime('2005-01-01'));
		$this->assertNull($classe,'La classe de florence doit être nulle pour la date 2005-01-01');

		$classe = $florence_eleve->getClasse(3);
		$this->assertNotNull($classe,'La classe de florence ne doit pas être nulle pour la période 3');
		$this->assertEquals('6ieme B',$classe->getNom());

		$classe = $florence_eleve->getClasse();
		$this->assertNotNull($classe,'Si il n y a aucune période en cours, la classe de florence doit être la dernière classe affecté');
		$this->assertEquals('6ieme B',$classe->getNom());
		
		$michel_eleve = EleveQuery::create()->findOneByLogin('Michel Martin');
		$classe = $michel_eleve->getClasse();
		$this->assertNull($classe);
		
		$classes = $florence_eleve->getClasses(5);
		$this->assertEquals(0,$classes->count(),'Les classes de florence sont vides pour la période 5');
		$this->assertEmpty($classes->getPrimaryKeys());

	}

	public function testGetGroupes() {
		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		$groupes = $florence_eleve->getGroupes(1);//on récupère les groupes pour la période 1
		$this->assertNotNull($groupes,'La collection des groupes ne doit jamais retourner null');
		$this->assertEquals(1,$groupes->count());

		$groupes = $florence_eleve->getGroupes(5);//on récupère la classe pour la période 1
		$this->assertEquals(0,$groupes->count(),'Les groupes de florence sont vides pour la période 5');
		$this->assertEmpty($groupes->getPrimaryKeys());

		$groupes = $florence_eleve->getGroupes(new DateTime('2010-10-01'));
		$this->assertNotNull($groupes,'La collection des groupes ne doit jamais retourner null');
		$this->assertEquals(1,$groupes->count(),'La collection des groupes de florence doit comporter un groupe pour la date 2010-10-01 (période 1)');
		
		$groupes = $florence_eleve->getGroupes(new DateTime('2005-01-01'));
		$this->assertEquals(0,$groupes->count(),'La collection des groupes de florence doit être vide pour la date 2005-01-01');

		$groupes = $florence_eleve->getGroupes();
		$this->assertEquals(1,$groupes->count(),'Si il n y a aucune période en cours, les groupes de florence sont les groupes de la dernière période');
		
		$michel_eleve = EleveQuery::create()->findOneByLogin('Michel Martin');
		$groupes = $michel_eleve->getGroupes();
		$this->assertEquals(0,$groupes->count(),'La collection des groupes de Michel doit être vide pour la date courante (aucune période d assignée pour michel');
	}

	public function testGetAbsenceEleveSaisiesDuJour() {
		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		$saisies = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-01');
		$this->assertEquals(1,$saisies->count());
		
		$saisies = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-02');
		$this->assertEquals(1,$saisies->count());
								
		$saisies = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-03');
		$this->assertEquals(1,$saisies->count());
								
		$saisies = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-04');
		$this->assertEquals(1,$saisies->count());
		
		$saisies = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-05');
		$saisie = $saisies->getFirst();
		
		$saisies = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-06');
		$this->assertEquals(1,$saisies->count());
	}

	public function testIsEleveSorti() {
		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		$this->assertFalse($florence_eleve->isEleveSorti());
		
		$michel_eleve = EleveQuery::create()->findOneByLogin('Michel Martin');
		$this->assertTrue($michel_eleve->isEleveSorti());
		
	}

	public function testGetAbsColDecompteDemiJournee() {
		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		$saisie_col = $florence_eleve->getAbsColDecompteDemiJournee();
		$this->assertEquals(19,$saisie_col->count());
		$saisie_col = $florence_eleve->getAbsColDecompteDemiJournee(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-01 23:59:59'));
		$this->assertEquals(1,$saisie_col->count());
		$saisie_col = $florence_eleve->getAbsColDecompteDemiJournee(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-14 23:59:59'));
		$this->assertEquals(17,$saisie_col->count());
		
		$michel_eleve = EleveQuery::create()->findOneByLogin('Michel Martin');
		$saisie_col = $michel_eleve->getAbsColDecompteDemiJournee();
		$this->assertEquals(0,$saisie_col->count());
				
	}
	
	public function testGetDemiJourneesAbsenceParCollection() {
		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		$saisie_col = $florence_eleve->getAbsColDecompteDemiJournee(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-01 23:59:59'));
		$this->assertEquals(1,$saisie_col->count());
		$demi_j_col = $florence_eleve->getDemiJourneesAbsenceParCollection($saisie_col);
		$this->assertEquals(1,$demi_j_col->count());
		$this->assertEquals("2010-10-01 00:00:00",$demi_j_col->getFirst()->format("Y-m-d H:i:s"));

		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		$saisie_col = $florence_eleve->getAbsColDecompteDemiJournee(new DateTime('2010-10-02 00:00:00'),new DateTime('2010-10-02 23:59:59'));
		$this->assertEquals(1,$saisie_col->count());
		$demi_j_col = $florence_eleve->getDemiJourneesAbsenceParCollection($saisie_col);
		$this->assertEquals(0,$demi_j_col->count());

		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		$saisie_col = $florence_eleve->getAbsColDecompteDemiJournee(new DateTime('2010-10-04 00:00:00'),new DateTime('2010-10-04 23:59:59'));
		$this->assertEquals(1,$saisie_col->count());
		$this->assertTrue($saisie_col->getFirst()->getManquementObligationPresence());
		saveSetting('abs2_retard_critere_duree',20);
		$saisie_col->getFirst()->reload();
		$demi_j_col = $florence_eleve->getDemiJourneesAbsenceParCollection($saisie_col);
		$this->assertEquals(1,$demi_j_col->count());
		saveSetting('abs2_retard_critere_duree',30);
		$saisie_col->getFirst()->reload();
		$demi_j_col = $florence_eleve->getDemiJourneesAbsenceParCollection($saisie_col);
		$this->assertEquals(0,$demi_j_col->count());
				
		$this->assertEquals(4,$florence_eleve->getDemiJourneesAbsenceParPeriode(1)->count());
	}
	
	public function testGetDemiJourneesNonJustifieesAbsenceParCollection() {
		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		$saisie_col = $florence_eleve->getAbsColDecompteDemiJournee(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-01 23:59:59'));
		$this->assertEquals(1,$saisie_col->count());
		$demi_j_col = $florence_eleve->getDemiJourneesNonJustifieesAbsenceParCollection($saisie_col);
		$this->assertEquals(1,$demi_j_col->count());
		$this->assertEquals("2010-10-01 00:00:00",$demi_j_col->getFirst()->format("Y-m-d H:i:s"));

		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		$saisie_col = $florence_eleve->getAbsColDecompteDemiJournee(new DateTime('2010-10-02 00:00:00'),new DateTime('2010-10-02 23:59:59'));
		$this->assertEquals(1,$saisie_col->count());
		$demi_j_col = $florence_eleve->getDemiJourneesNonJustifieesAbsenceParCollection($saisie_col);
		$this->assertEquals(0,$demi_j_col->count());

		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		$saisie_col = $florence_eleve->getAbsColDecompteDemiJournee(new DateTime('2010-10-04 00:00:00'),new DateTime('2010-10-04 23:59:59'));
		$this->assertEquals(1,$saisie_col->count());
		$this->assertTrue($saisie_col->getFirst()->getManquementObligationPresence());
		saveSetting('abs2_retard_critere_duree',20);
		$saisie_col->getFirst()->reload();
		$demi_j_col = $florence_eleve->getDemiJourneesNonJustifieesAbsenceParCollection($saisie_col);
		$this->assertEquals(0,$demi_j_col->count());
		saveSetting('abs2_retard_critere_duree',30);
		$saisie_col->getFirst()->reload();
		$demi_j_col = $florence_eleve->getDemiJourneesNonJustifieesAbsenceParCollection($saisie_col);
		$this->assertEquals(0,$demi_j_col->count());
				
		$this->assertEquals(3,$florence_eleve->getDemiJourneesNonJustifieesAbsenceParPeriode(1)->count());
	}

	public function testGetRetards() {
		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		$saisie_col = $florence_eleve->getRetards(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-01 23:59:59'));
		$this->assertEquals(0,$saisie_col->count());

		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		$saisie_col = $florence_eleve->getRetards(new DateTime('2010-10-02 00:00:00'),new DateTime('2010-10-02 23:59:59'));
		$this->assertEquals(0,$saisie_col->count());

		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		$saisie_col = $florence_eleve->getAbsColDecompteDemiJournee(new DateTime('2010-10-04 00:00:00'),new DateTime('2010-10-04 23:59:59'));
		$this->assertEquals(1,$saisie_col->count());
		$this->assertTrue($saisie_col->getFirst()->getManquementObligationPresence());
		saveSetting('abs2_retard_critere_duree',20);
		$saisie_col->getFirst()->reload();
		$retard_col = $florence_eleve->getRetards(new DateTime('2010-10-04 00:00:00'),new DateTime('2010-10-04 23:59:59'));
		$this->assertEquals(0,$retard_col->count());
		saveSetting('abs2_retard_critere_duree',30);
		$saisie_col->getFirst()->reload();
		$retard_col = $florence_eleve->getRetards(new DateTime('2010-10-04 00:00:00'),new DateTime('2010-10-04 23:59:59'));
		$this->assertEquals(1,$retard_col->count());
		
		$this->assertEquals(6,$florence_eleve->getRetardsParPeriode(1)->count());
	}

	public function testGetAbsenceEleveSaisiesManquementObligationPresence() {
		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		$saisie_col = $florence_eleve->getAbsenceEleveSaisiesManquementObligationPresence(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-01 23:59:59'));
		$this->assertEquals(1,$saisie_col->count());

		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		$saisie_col = $florence_eleve->getAbsenceEleveSaisiesManquementObligationPresence(new DateTime('2010-10-02 00:00:00'),new DateTime('2010-10-02 23:59:59'));
		$this->assertEquals(1,$saisie_col->count());

		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		$saisie_col = $florence_eleve->getAbsenceEleveSaisiesManquementObligationPresence(new DateTime('2010-10-04 00:00:00'),new DateTime('2010-10-04 23:59:59'));
		$this->assertEquals(1,$saisie_col->count());
		$this->assertTrue($saisie_col->getFirst()->getManquementObligationPresence());
		saveSetting('abs2_retard_critere_duree',20);
		$saisie_col->getFirst()->reload();
		$manguement_col = $florence_eleve->getAbsenceEleveSaisiesManquementObligationPresence(new DateTime('2010-10-04 00:00:00'),new DateTime('2010-10-04 23:59:59'));
		$this->assertEquals(1,$manguement_col->count());
		saveSetting('abs2_retard_critere_duree',30);
		$saisie_col->getFirst()->reload();
		$manguement_col = $florence_eleve->getAbsenceEleveSaisiesManquementObligationPresence(new DateTime('2010-10-04 00:00:00'),new DateTime('2010-10-04 23:59:59'));
		$this->assertEquals(1,$manguement_col->count());
				
	}
	
	public function testUpdateAbsenceAgregationTable() {
	    //on purge les decompte pour florence
	    $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
	    AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->delete();
	    $florence_eleve->updateAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-05 23:59:59'));
	    $this->assertEquals(11,AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->count());
	    
	    $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
	    AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->delete();
	    $florence_eleve->updateAbsenceAgregationTable();
	    $this->assertEquals(30,AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->count());
	    
	    $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
	    AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->delete();
	    $florence_eleve->updateAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-15 23:59:59'));
	    $this->assertEquals(31,AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->count());
	    $this->assertEquals(4, AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByManquementObligationPresence(true)->count());
	    $this->assertEquals(1, AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByManquementObligationPresence(true)->filterByNonJustifiee(false)->count());
	    $this->assertEquals(4, AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByNbRetards(1)->count());
	    $this->assertEquals(1, AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByNbRetards(2)->count());
	    $this->assertEquals(4, AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByNbRetardsNonJustifies(1)->count());
	    $demi_journee =  AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByManquementObligationPresence(true)->filterByNonJustifiee(false)->findOne();
	    $this->assertEquals('2010-10-14 00:00:00', $demi_journee->getDateDemiJounee('Y-m-d H:i:s'));
	}
	
	public function testCheckSynchroAbsenceAgregationTable() {
	    //on purge les decompte pour florence
	    $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
	    AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->delete();
	    $this->assertFalse($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-05 23:59:59')));
	    $florence_eleve->updateAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-05 23:59:59'));
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-05 23:59:59')));
	    
	    $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
	    AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->delete();
	    $florence_eleve->updateAbsenceAgregationTable();
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable());
	    	    
	    $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
	    AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->delete();
	    $florence_eleve->updateAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-15 23:59:59'));
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-15 23:59:59')));
	    
	    AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByManquementObligationPresence(true)->filterByNonJustifiee(false)->delete();
	    $this->assertFalse($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-15 23:59:59')));
	    $florence_eleve->updateAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-15 23:59:59'));
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-15 23:59:59')));

	    AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByManquementObligationPresence(true)->filterByNonJustifiee(false)->delete();
	    $this->assertFalse($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-15 23:59:59')));
	    $florence_eleve->updateAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-15 23:59:59'));
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-15 23:59:59')));
	}
}
