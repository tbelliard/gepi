<?php

require_once dirname(__FILE__) . '/../../../tools/helpers/orm/GepiEmptyTestBase.php';
require_once dirname(__FILE__) . '/init_date.php';

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
		$florence_eleve->clearAllReferences();
		$periode_col = $florence_eleve->getPeriodeNotes();
		$this->assertEquals('3',$periode_col->count());
		$this->assertNull($florence_eleve->getPeriodeNoteOuverte(),'Après verrouillage la période ouverte de note de florence doit être nulle');
		
		$periode = $florence_eleve->getPeriodeNote(new DateTime(VENDREDI_s40j5));
		$this->assertNotNull($periode);
		$this->assertEquals('1',$periode->getNumPeriode());
		
		$periode = $florence_eleve->getPeriodeNote(new DateTime(DIMANCHE_s49j7));
		$this->assertNotNull($periode);
		$this->assertEquals('2',$periode->getNumPeriode());
		
		$michel_eleve = EleveQuery::create()->findOneByLogin('Michel Martin');
		$this->assertEquals(0,$michel_eleve->getPeriodeNotes()->count());
		
		$periode = $michel_eleve->getPeriodeNote(new DateTime(DIMANCHE_s49j7));
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
		$this->assertNull($classe,'La classe de florence doit être nulle pour la période 5');

		$classe = $florence_eleve->getClasse(new DateTime(VENDREDI_s40j5));
		$this->assertNotNull($classe,'La classe de florence ne doit pas être nulle pour la date '.VENDREDI_s40j5.' (période 1)');
		$this->assertEquals('6ieme A',$classe->getNom());
		
		$classe = $florence_eleve->getClasse(new DateTime(FIN_ANNEE_5a));
		$this->assertNull($classe,'La classe de florence doit être nulle pour la date '.FIN_ANNEE_5a);

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
		$this->assertEquals(0,count($classes->getPrimaryKeys()));

	}

	public function testGetGroupes() {
		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		$groupes = $florence_eleve->getGroupes(1);//on récupère les groupes pour la période 1
		$this->assertNotNull($groupes,'La collection des groupes ne doit jamais retourner null');
		$this->assertEquals(1,$groupes->count());

		$groupes = $florence_eleve->getGroupes(5);//on récupère la classe pour la période 1
		$this->assertEquals(0,$groupes->count(),'Les groupes de florence sont vides pour la période 5');
		$this->assertEquals(0,count($groupes->getPrimaryKeys()));

		$groupes = $florence_eleve->getGroupes(new DateTime(VENDREDI_s40j5));
		$this->assertNotNull($groupes,'La collection des groupes ne doit jamais retourner null');
		$this->assertEquals(1,$groupes->count(),'La collection des groupes de florence doit comporter un groupe pour la date '.VENDREDI_s40j5.' (période 1)');
		
		$groupes = $florence_eleve->getGroupes(new DateTime(FIN_ANNEE_5a));
		$this->assertEquals(0,$groupes->count(),'La collection des groupes de florence doit être vide pour la date '.FIN_ANNEE_5a);

		$groupes = $florence_eleve->getGroupes();
		$this->assertEquals(1,$groupes->count(),'Si il n y a aucune période en cours, les groupes de florence sont les groupes de la dernière période');
		
		$michel_eleve = EleveQuery::create()->findOneByLogin('Michel Martin');
		$groupes = $michel_eleve->getGroupes();
		$this->assertEquals(0,$groupes->count(),'La collection des groupes de Michel doit être vide pour la date courante (aucune période d assignée pour michel');
	}

	public function testGetAbsenceEleveSaisiesDuJour() {
		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		$saisies = $florence_eleve->getAbsenceEleveSaisiesDuJour(VENDREDI_s40j5);
		$this->assertEquals(1,$saisies->count());
		
		$saisies = $florence_eleve->getAbsenceEleveSaisiesDuJour(SAMEDI_s40j6);
		$this->assertEquals(1,$saisies->count());
								
		$saisies = $florence_eleve->getAbsenceEleveSaisiesDuJour(DIMANCHE_s40j7);
		$this->assertEquals(1,$saisies->count());
								
		$saisies = $florence_eleve->getAbsenceEleveSaisiesDuJour(LUNDI_s41j1);
		$this->assertEquals(1,$saisies->count());
		
		$saisies = $florence_eleve->getAbsenceEleveSaisiesDuJour(MARDI_s41j2);
		$saisie = $saisies->getFirst();
		
		$saisies = $florence_eleve->getAbsenceEleveSaisiesDuJour(MERCREDI_s41j3);
		$this->assertEquals(1,$saisies->count());
	}

	public function testIsEleveSorti() {
		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		$this->assertFalse($florence_eleve->isEleveSorti());
		
		$michel_eleve = EleveQuery::create()->findOneByLogin('Michel Martin');
		$this->assertTrue($michel_eleve->isEleveSorti());
		
	}

	public function testgetAbsenceEleveSaisiesParDate() {
		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		$saisie_col = $florence_eleve->getAbsenceEleveSaisiesParDate(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s40j5.' 23:59:59'));
		$this->assertEquals(1,$saisie_col->count());
		$saisie_col = $florence_eleve->getAbsenceEleveSaisiesParDate(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(JEUDI_s42j4.' 23:59:59'));
		$this->assertEquals(17,$saisie_col->count());
		
		$michel_eleve = EleveQuery::create()->findOneByLogin('Michel Martin');
		$saisie_col = $michel_eleve->getAbsenceEleveSaisiesParDate();
		$this->assertEquals(0,$saisie_col->count());
				
		$saisie_col = $florence_eleve->getAbsenceEleveSaisiesParDate(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s40j5.' 23:59:59'));
		$this->assertEquals(1,$saisie_col->count());
		$saisie_col = $florence_eleve->getAbsenceEleveSaisiesParDate(new DateTime(SAMEDI_s40j6.' 00:00:00'),new DateTime(SAMEDI_s40j6.' 23:59:59'));
		$this->assertEquals(1,$saisie_col->count());
		$saisie_col = $florence_eleve->getAbsenceEleveSaisiesParDate(new DateTime(LUNDI_s41j1.' 00:00:00'),new DateTime(LUNDI_s41j1.' 23:59:59'));
		$this->assertEquals(1,$saisie_col->count());
		$this->assertTrue($saisie_col->getFirst()->getManquementObligationPresence());
		$saisie_col = $florence_eleve->getAbsenceEleveSaisiesParDate(new DateTime(DIMANCHE_s43j7.' 00:00:00'),new DateTime(DIMANCHE_s45j7.' 23:59:59'));
		$this->assertEquals(1,$saisie_col->count());
		$this->assertTrue($saisie_col->getFirst()->getManquementObligationPresence());
	}
	
	public function testGetDemiJourneesAbsence() {
		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		$demi_j_col = $florence_eleve->getDemiJourneesAbsence(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s40j5.' 23:59:59'));
		$this->assertEquals(1,$demi_j_col->count());
		$this->assertEquals(VENDREDI_s40j5." 00:00:00",$demi_j_col->getFirst()->format("Y-m-d H:i:s"));

		$demi_j_col = $florence_eleve->getDemiJourneesAbsence(new DateTime(SAMEDI_s40j6.' 00:00:00'),new DateTime(SAMEDI_s40j6.' 23:59:59'));
		$this->assertEquals(0,$demi_j_col->count());

		$saisie_col = $florence_eleve->getAbsenceEleveSaisiesParDate(new DateTime(LUNDI_s41j1.' 00:00:00'),new DateTime(LUNDI_s41j1.' 23:59:59'));
		$this->assertEquals(1,$saisie_col->count());
		$this->assertTrue($saisie_col->getFirst()->getManquementObligationPresence());
		saveSetting('abs2_retard_critere_duree',20);
		$saisie_col->getFirst()->clearAllReferences();
		$demi_j_col = $florence_eleve->getDemiJourneesAbsence(new DateTime(LUNDI_s41j1.' 00:00:00'),new DateTime(LUNDI_s41j1.' 23:59:59'));
		$this->assertEquals(1,$demi_j_col->count());
		saveSetting('abs2_retard_critere_duree',30);
		$saisie_col->getFirst()->clearAllReferences();
		$demi_j_col = $florence_eleve->getDemiJourneesAbsence(new DateTime(LUNDI_s41j1.' 00:00:00'),new DateTime(LUNDI_s41j1.' 23:59:59'));
		$this->assertEquals(0,$demi_j_col->count());
		
		$demi_j_col = $florence_eleve->getDemiJourneesAbsence(new DateTime(MARDI_s43j2.' 00:00:00'),new DateTime(MARDI_s43j2.' 23:59:59'));
		$this->assertEquals(2,$demi_j_col->count());
		
		# Absence 20 du jeudi semaine 44 au mardi semaine 45 1 seule saisie
		$demi_j_col = $florence_eleve->getDemiJourneesAbsence(new DateTime(DIMANCHE_s43j7.' 00:00:00'),new DateTime(DIMANCHE_s45j7.' 23:59:59'));
		$this->assertEquals(8,$demi_j_col->count());
		# La première semaine on ne doit avoir que 4 demi-journées d'absences
		$demi_j_col = $florence_eleve->getDemiJourneesAbsence(new DateTime(DIMANCHE_s43j7.' 00:00:00'),new DateTime(SAMEDI_s44j6.' 23:59:59'));
		$this->assertEquals(4,$demi_j_col->count());	# période bornée -> 4 demi-journées
		# La deuxième semaine on ne doit avoir que 4 demi-journées d'absences
		$demi_j_col = $florence_eleve->getDemiJourneesAbsence(new DateTime(DIMANCHE_s44j7.' 00:00:00'),new DateTime(DIMANCHE_s45j7.' 23:59:59'));
		$this->assertEquals(4,$demi_j_col->count());

        #test de saisies englobant d'autres saisies
		$demi_j_col = $florence_eleve->getDemiJourneesAbsence(new DateTime(MARDI_a1_s22j2.' 00:00:00'),new DateTime(MARDI_a1_s22j2.' 23:59:59'));
		$this->assertEquals(0,$demi_j_col->count());

                $this->assertEquals(1,$florence_eleve->getDemiJourneesAbsence(new DateTime(JEUDI_1a_s35j4.' 00:00:00'),new DateTime(DIMANCHE_s40j7.' 23:59:59'))->count());
                $this->assertEquals(0,$florence_eleve->getDemiJourneesAbsence(new DateTime(LUNDI_s41j1.' 00:00:00'),new DateTime(MERCREDI_s41j3.' 23:59:59'))->count());
                $this->assertEquals(1,$florence_eleve->getDemiJourneesAbsence(new DateTime(JEUDI_s41j4.' 00:00:00'),new DateTime(SAMEDI_s41j6.' 23:59:59'))->count());
                $this->assertEquals(0,$florence_eleve->getDemiJourneesAbsence(new DateTime(DIMANCHE_s41j7.' 00:00:00'),new DateTime(MARDI_s42j2.' 23:59:59'))->count());
                $this->assertEquals(1,$florence_eleve->getDemiJourneesAbsence(new DateTime(MERCREDI_s42j3.' 00:00:00'),new DateTime(JEUDI_s42j4.' 23:59:59'))->count());
                $this->assertEquals(1,$florence_eleve->getDemiJourneesAbsence(new DateTime(VENDREDI_s42j5.' 00:00:00'),new DateTime(SAMEDI_s42j6.' 23:59:59'))->count());
                $this->assertEquals(0,$florence_eleve->getDemiJourneesAbsence(new DateTime(DIMANCHE_s42j7.' 00:00:00'),new DateTime(DIMANCHE_s42j7.' 23:59:59'))->count());
                $this->assertEquals(0,$florence_eleve->getDemiJourneesAbsence(new DateTime(LUNDI_s43j1.' 00:00:00'),new DateTime(LUNDI_s43j1.' 23:59:59'))->count());
                $this->assertEquals(2,$florence_eleve->getDemiJourneesAbsence(new DateTime(MARDI_s43j2.' 00:00:00'),new DateTime(MERCREDI_s43j3.' 23:59:59'))->count());
                $this->assertEquals(8,$florence_eleve->getDemiJourneesAbsence(new DateTime(JEUDI_s43j4.' 00:00:00'),new DateTime(JEUDI_s49j4.' 00:00:00'))->count());
                $this->assertEquals(14,$florence_eleve->getDemiJourneesAbsenceParPeriode(1)->count());

		$demi_j_col = $florence_eleve->getDemiJourneesAbsence(new DateTime(JEUDI_a1_s22j4.' 00:00:00'),new DateTime(JEUDI_a1_s22j4.' 23:59:59'));
		$this->assertEquals(0,$demi_j_col->count());

                saveSetting('abs2_saisie_multi_type_sans_manquement','y');
                $demi_j_col = $florence_eleve->getDemiJourneesAbsence(new DateTime(VENDREDI_a1_s22j5.' 00:00:00'),new DateTime(VENDREDI_a1_s22j5.' 23:59:59'));
                $this->assertEquals(0,$demi_j_col->count());
                saveSetting('abs2_saisie_multi_type_sans_manquement','n');
                $saisies = $florence_eleve->getAbsenceEleveSaisiesDuJour(VENDREDI_a1_s22j5.'');
                $saisies->getFirst()->clearAllReferences();
                $saisies->getNext()->clearAllReferences();
                $demi_j_col = $florence_eleve->getDemiJourneesAbsence(new DateTime(VENDREDI_a1_s22j5.' 00:00:00'),new DateTime(VENDREDI_a1_s22j5.' 23:59:59'));
                $this->assertEquals(1,$demi_j_col->count());

                $demi_j_col = $florence_eleve->getDemiJourneesAbsence(new DateTime(LUNDI_a1_s23j1.' 00:00:00'),new DateTime(LUNDI_a1_s23j1.' 23:59:59'));
                $this->assertEquals(1,$demi_j_col->count());
	}
	
	public function testGetDemiJourneesNonJustifieesAbsence() {
                $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		$demi_j_col = $florence_eleve->getDemiJourneesNonJustifieesAbsence(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s40j5.' 23:59:59'));
		$this->assertEquals(1,$demi_j_col->count());
		$this->assertEquals(VENDREDI_s40j5." 00:00:00",$demi_j_col->getFirst()->format("Y-m-d H:i:s"));

		$demi_j_col = $florence_eleve->getDemiJourneesNonJustifieesAbsence(new DateTime(SAMEDI_s40j6.' 00:00:00'),new DateTime(SAMEDI_s40j6.' 23:59:59'));
		$this->assertEquals(0,$demi_j_col->count());

		$demi_j_col = $florence_eleve->getDemiJourneesNonJustifieesAbsence(new DateTime(MARDI_a1_s22j2.' 00:00:00'),new DateTime(MARDI_a1_s22j2.' 23:59:59'));
		$this->assertEquals(0,$demi_j_col->count());

                $this->assertEquals(1,$florence_eleve->getDemiJourneesNonJustifieesAbsence(new DateTime(JEUDI_1a_s35j4.' 00:00:00'),new DateTime(DIMANCHE_s40j7.' 23:59:59'))->count());
                $this->assertEquals(0,$florence_eleve->getDemiJourneesNonJustifieesAbsence(new DateTime(LUNDI_s41j1.' 00:00:00'),new DateTime(MERCREDI_s41j3.' 23:59:59'))->count());
                $this->assertEquals(1,$florence_eleve->getDemiJourneesNonJustifieesAbsence(new DateTime(JEUDI_s41j4.' 00:00:00'),new DateTime(SAMEDI_s41j6.' 23:59:59'))->count());
                $this->assertEquals(0,$florence_eleve->getDemiJourneesNonJustifieesAbsence(new DateTime(DIMANCHE_s41j7.' 00:00:00'),new DateTime(MARDI_s42j2.' 23:59:59'))->count());
                $this->assertEquals(0,$florence_eleve->getDemiJourneesNonJustifieesAbsence(new DateTime(MERCREDI_s42j3.' 00:00:00'),new DateTime(JEUDI_s42j4.' 23:59:59'))->count());
                $this->assertEquals(1,$florence_eleve->getDemiJourneesNonJustifieesAbsence(new DateTime(VENDREDI_s42j5.' 00:00:00'),new DateTime(SAMEDI_s42j6.' 23:59:59'))->count());
                $this->assertEquals(0,$florence_eleve->getDemiJourneesNonJustifieesAbsence(new DateTime(DIMANCHE_s42j7.' 00:00:00'),new DateTime(DIMANCHE_s42j7.' 23:59:59'))->count());
                $this->assertEquals(0,$florence_eleve->getDemiJourneesNonJustifieesAbsence(new DateTime(LUNDI_s43j1.' 00:00:00'),new DateTime(LUNDI_s43j1.' 23:59:59'))->count());
                $this->assertEquals(2,$florence_eleve->getDemiJourneesNonJustifieesAbsence(new DateTime(MARDI_s43j2.' 00:00:00'),new DateTime(MERCREDI_s43j3.' 23:59:59'))->count());
                $this->assertEquals(8,$florence_eleve->getDemiJourneesNonJustifieesAbsence(new DateTime(JEUDI_s43j4.' 00:00:00'),new DateTime(JEUDI_s49j4.' 00:00:00'))->count());
                $this->assertEquals(13,$florence_eleve->getDemiJourneesNonJustifieesAbsenceParPeriode(1)->count());

		$demi_j_col = $florence_eleve->getDemiJourneesAbsence(new DateTime(JEUDI_a1_s22j4.' 00:00:00'),new DateTime(JEUDI_a1_s22j4.' 23:59:59'));
		$this->assertEquals(0,$demi_j_col->count());

                saveSetting('abs2_saisie_multi_type_sans_manquement','y');
                $demi_j_col = $florence_eleve->getDemiJourneesAbsence(new DateTime(VENDREDI_a1_s22j5.' 00:00:00'),new DateTime(VENDREDI_a1_s22j5.' 23:59:59'));
                $this->assertEquals(0,$demi_j_col->count());
                saveSetting('abs2_saisie_multi_type_sans_manquement','n');
                $saisies = $florence_eleve->getAbsenceEleveSaisiesDuJour(VENDREDI_a1_s22j5.'');
                $saisies->getFirst()->clearAllReferences();
                $saisies->getNext()->clearAllReferences();
                $demi_j_col = $florence_eleve->getDemiJourneesAbsence(new DateTime(VENDREDI_a1_s22j5.' 00:00:00'),new DateTime(VENDREDI_a1_s22j5.' 23:59:59'));
                $this->assertEquals(1,$demi_j_col->count());

                $demi_j_col = $florence_eleve->getDemiJourneesNonJustifieesAbsence(new DateTime(MARDI_a1_s23j2.' 00:00:00'),new DateTime(MARDI_a1_s23j2.' 23:59:59'));
                $this->assertEquals(0,$demi_j_col->count());

                saveSetting('abs2_saisie_multi_type_non_justifiee','n');
                $demi_j_col = $florence_eleve->getDemiJourneesNonJustifieesAbsence(new DateTime(MERCREDI_a1_s23j3.' 00:00:00'),new DateTime(MERCREDI_a1_s23j3.' 23:59:59'));
                $this->assertEquals(0,$demi_j_col->count());
                saveSetting('abs2_saisie_multi_type_non_justifiee','y');
                $saisies = $florence_eleve->getAbsenceEleveSaisiesDuJour(MERCREDI_a1_s23j3.'');
                $saisies->getFirst()->clearAllReferences();
                $saisies->getNext()->clearAllReferences();
                $demi_j_col = $florence_eleve->getDemiJourneesNonJustifieesAbsence(new DateTime(MERCREDI_a1_s23j3.' 00:00:00'),new DateTime(MERCREDI_a1_s23j3.' 23:59:59'));
                $this->assertEquals(1,$demi_j_col->count());
                saveSetting('abs2_saisie_multi_type_non_justifiee','n');//n par défaut
	}

	public function testGetRetards() {
		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');

                $saisie_col = $florence_eleve->getRetards(new DateTime(SAMEDI_s40j6.' 00:00:00'),new DateTime(SAMEDI_s40j6.' 23:59:59'));
		$this->assertEquals(0,$saisie_col->count());

		$saisie_col = $florence_eleve->getAbsenceEleveSaisiesParDate(new DateTime(LUNDI_s41j1.' 00:00:00'),new DateTime(LUNDI_s41j1.' 23:59:59'));
		saveSetting('abs2_retard_critere_duree',20);
		$saisie_col->getFirst()->clearAllReferences();
		$retard_col = $florence_eleve->getRetards(new DateTime(LUNDI_s41j1.' 00:00:00'),new DateTime(LUNDI_s41j1.' 23:59:59'));
		$this->assertEquals(0,$retard_col->count());
		saveSetting('abs2_retard_critere_duree',30);
		$saisie_col->getFirst()->clearAllReferences();
		$retard_col = $florence_eleve->getRetards(new DateTime(LUNDI_s41j1.' 00:00:00'),new DateTime(LUNDI_s41j1.' 23:59:59'));
		$this->assertEquals(1,$retard_col->count());
                
                $this->assertEquals(0,$florence_eleve->getRetards(new DateTime(JEUDI_1a_s35j4.' 00:00:00'),new DateTime(DIMANCHE_s40j7.' 23:59:59'))->count());
                $this->assertEquals(4,$florence_eleve->getRetards(new DateTime(LUNDI_s41j1.' 00:00:00'),new DateTime(MERCREDI_s41j3.' 23:59:59'))->count());
                $this->assertEquals(0,$florence_eleve->getRetards(new DateTime(JEUDI_s41j4.' 00:00:00'),new DateTime(SAMEDI_s41j6.' 23:59:59'))->count());
                $this->assertEquals(2,$florence_eleve->getRetards(new DateTime(DIMANCHE_s41j7.' 00:00:00'),new DateTime(MARDI_s42j2.' 23:59:59'))->count());
                $this->assertEquals(0,$florence_eleve->getRetards(new DateTime(MERCREDI_s42j3.' 00:00:00'),new DateTime(JEUDI_s42j4.' 23:59:59'))->count());
                $this->assertEquals(0,$florence_eleve->getRetards(new DateTime(VENDREDI_s42j5.' 00:00:00'),new DateTime(SAMEDI_s42j6.' 23:59:59'))->count());
                $this->assertEquals(0,$florence_eleve->getRetards(new DateTime(DIMANCHE_s42j7.' 00:00:00'),new DateTime(DIMANCHE_s42j7.' 23:59:59'))->count());
                $this->assertEquals(0,$florence_eleve->getRetards(new DateTime(LUNDI_s43j1.' 00:00:00'),new DateTime(LUNDI_s43j1.' 23:59:59'))->count());
                $this->assertEquals(0,$florence_eleve->getRetards(new DateTime(MARDI_s43j2.' 00:00:00'),new DateTime(MERCREDI_s43j3.' 23:59:59'))->count());
                $this->assertEquals(0,$florence_eleve->getRetards(new DateTime(JEUDI_s43j4.' 00:00:00'),new DateTime(JEUDI_s49j4.' 00:00:00'))->count());
                
                $this->assertEquals(6,$florence_eleve->getRetardsParPeriode(1)->count());
		
		//Retard saisi alors que l'élève a quitté l'établissement
		saveSetting('abs2_retard_critere_duree',30);
		$retard_col = $florence_eleve->getRetards(new DateTime(LUNDI_s41j1.' 00:00:00'),new DateTime(LUNDI_s41j1.' 23:59:59'));
		$this->assertEquals(1,$retard_col->count());
		$florence_eleve->setDateSortie(strtotime(LUNDI_s41j1.' 00:00:00'));	# On sort l'élève
		$retard_col = $florence_eleve->getRetards(new DateTime(LUNDI_s41j1.' 00:00:00'),new DateTime(LUNDI_s41j1.' 23:59:59'));
		$this->assertEquals(0,$retard_col->count());
		
		$retard_col = $florence_eleve->getRetards(new DateTime(LUNDI_s43j1.' 00:00:00'),new DateTime(LUNDI_s43j1.' 23:59:59'));
		$this->assertEquals(0,$retard_col->count());
                $florence_eleve->setDateSortie(null);

                //test de saisies englobant d'autres saisies
                $retard_col = $florence_eleve->getRetards(new DateTime(MERCREDI_a1_s22j3.' 00:00:00'),new DateTime(MERCREDI_a1_s22j3.' 23:59:59'));
		$this->assertEquals(0,$retard_col->count());

                $retard_col = $florence_eleve->getRetards(new DateTime(MERCREDI_a1_s23j3.' 00:00:00'),new DateTime(JEUDI_a1_s23j4.' 23:59:59'));
		$this->assertEquals(1,$retard_col->count());

                $retard_col = $florence_eleve->getRetards(new DateTime(JEUDI_a1_s23j4.' 00:00:00'),new DateTime(VENDREDI_a1_s23j5.' 23:59:59'));
		$this->assertEquals(2,$retard_col->count());

}

	public function testgetAbsenceEleveSaisiesDecompteDemiJournees() {
		$florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		$saisie_col = $florence_eleve->getAbsenceEleveSaisiesDecompteDemiJournees(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s40j5.' 23:59:59'));
		$this->assertEquals(1,$saisie_col->count());

		$saisie_col = $florence_eleve->getAbsenceEleveSaisiesDecompteDemiJournees(new DateTime(SAMEDI_s40j6.' 00:00:00'),new DateTime(SAMEDI_s40j6.' 23:59:59'));
		$this->assertEquals(1,$saisie_col->count());

		saveSetting('abs2_retard_critere_duree',20);
		$saisie_col->getFirst()->clearAllReferences();
		$manguement_col = $florence_eleve->getAbsenceEleveSaisiesDecompteDemiJournees(new DateTime(LUNDI_s41j1.' 00:00:00'),new DateTime(LUNDI_s41j1.' 23:59:59'));
		$this->assertEquals(1,$manguement_col->count());
		saveSetting('abs2_retard_critere_duree',30);
		$manguement_col->getFirst()->clearAllReferences();
		$manguement_col = $florence_eleve->getAbsenceEleveSaisiesDecompteDemiJournees(new DateTime(LUNDI_s41j1.' 00:00:00'),new DateTime(LUNDI_s41j1.' 23:59:59'));
		$this->assertEquals(0,$manguement_col->count());
				
	}
	
	public function testUpdateAbsenceAgregationTable() {
	    //on purge les decompte pour florence
	    $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
	    AbsenceAgregationDecompteQuery::create()->deleteAll();
	    
	    $florence_eleve->updateAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(MARDI_s41j2.' 23:59:59'));
	    $this->assertEquals(11,AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->count());
	    
	    AbsenceAgregationDecompteQuery::create()->deleteAll();
	    $florence_eleve->updateAbsenceAgregationTable(new DateTime(SAMEDI_s35j6.' 00:00:00'),new DateTime(DIMANCHEs35j7.' 23:59:59'));//ce test ne se terminait pas
	    $this->assertEquals(5,AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->count());
	    
	    AbsenceAgregationDecompteQuery::create()->deleteAll();
	    $florence_eleve->updateAbsenceAgregationTable(null,new DateTime(MARDI_s41j2.' 23:59:59'));
	    $this->assertEquals(11,AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->count());

	    
	    AbsenceAgregationDecompteQuery::create()->deleteAll();
	    $florence_eleve->updateAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(MARDI_s41j2.' 23:59:59'));
	    $this->assertEquals(11,AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->count());
	    
	    AbsenceAgregationDecompteQuery::create()->deleteAll();
	    $florence_eleve->updateAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59'));
	    $this->assertEquals(31,AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->count());
	    $this->assertEquals(4, AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByManquementObligationPresence(true)->count());
	    $this->assertEquals(1, AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByManquementObligationPresence(true)->filterByNonJustifiee(false)->count());
	    $this->assertEquals(4, AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByRetards(1)->count());
	    $this->assertEquals(1, AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByRetards(2)->count());
	    $this->assertEquals(4, AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByRetardsNonJustifies(1)->count());
	    $demi_journee =  AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByManquementObligationPresence(true)->filterByNonJustifiee(false)->findOne();
	    $this->assertEquals(JEUDI_s42j4.' 00:00:00', $demi_journee->getDateDemiJounee('Y-m-d H:i:s'));

	    AbsenceAgregationDecompteQuery::create()->deleteAll();
	    $florence_eleve->updateAbsenceAgregationTable(new DateTime(SAMEDI_s35j6.' 00:00:00'),new DateTime(DIMANCHEs35j7.' 23:59:59'));
	    $this->assertEquals(5,AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->count());
	    $this->assertEquals(0, AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByManquementObligationPresence(true)->count());
	    $this->assertEquals(5, AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByRetards(0)->count());
	    AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->delete();
            $saisie = new AbsenceEleveSaisie();//on va vérifier que la mise à jour de la table d'agrégation est bien limité à 3 ans dans le passé et dans le futur
            $saisie->setEleve($florence_eleve);
            $saisie->setUtilisateurProfessionnel(UtilisateurProfessionnelQuery::create()->findOneByLogin('Dolto'));
            $before = new DateTime();
            $before->modify('-4 years');
            $saisie->setDebutAbs($before);
            $after = new DateTime();
            $after->modify('+4 years');
            $saisie->setFinAbs($after);
            $saisie->save();
            $now = new DateTime();
	    $florence_eleve->updateAbsenceAgregationTable();
            $col = AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->orderByDateDemiJounee()->find();
            $this->assertTrue($now->format('U') - $col->get(1)->getDateDemiJounee('U') < 3600*24*365*3 + 3600*24);
            $this->assertTrue($now->format('U') - $col->get(1)->getDateDemiJounee('U') > 3600*24*365*3 - 3600*24);
            $this->assertTrue($col->getLast()->getDateDemiJounee('U') - $now->format('U') < 3600*24*365*3 + 3600*24);
            $this->assertTrue($col->getLast()->getDateDemiJounee('U') - $now->format('U') > 3600*24*365*3 - 3600*24);
	    
        try {
            $florence_eleve->updateAbsenceAgregationTable(new DateTime('1980-09-01 00:00:00'),null);
            $this->fail('Une exception doit être soulevée lors de cette demande car les dates sont trop larges');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
        try {
            $florence_eleve->updateAbsenceAgregationTable(new DateTime('2100-09-01 00:00:00'),null);
            $this->fail('Une exception doit être soulevée lors de cette demande car les dates sont trop larges');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
        try {
            $florence_eleve->updateAbsenceAgregationTable(null,new DateTime('2100-09-01 00:00:00'));
            $this->fail('Une exception doit être soulevée lors de cette demande car les dates sont trop larges');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
        try {
            $florence_eleve->updateAbsenceAgregationTable(null,new DateTime('1980-09-01 00:00:00'));
            $this->fail('Une exception doit être soulevée lors de cette demande car les dates sont trop larges');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
        
        
	}
	
	public function testCheckSynchroAbsenceAgregationTable() {
	    //on purge les decompte pour florence
	    $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
	    AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->delete();
	    $this->assertFalse($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(MARDI_s41j2.' 23:59:59')));
	    $florence_eleve->updateAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(MARDI_s41j2.' 23:59:59'));
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(MARDI_s41j2.' 23:59:59')));
	    
	    AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->delete();
	    $florence_eleve->updateAbsenceAgregationTable();
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable());
	    	    
	    AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->delete();
	    $florence_eleve->updateAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59'));
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59')));
	    
	    AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByManquementObligationPresence(true)->filterByNonJustifiee(false)->delete();
	    $this->assertFalse($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59')));
	    $florence_eleve->updateAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59'));
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59')));

	    AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByManquementObligationPresence(true)->filterByNonJustifiee(false)->delete();
	    $this->assertFalse($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59')));
	    $florence_eleve->updateAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59'));
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59')));
	    
	    //on va modifier une saisie à la main
	    $tomorow = new DateTime('now');
	    $tomorow->modify("+1 day");
        mysqli_query($GLOBALS["mysqli"], "update a_saisies set updated_at = '".$tomorow->format('Y-m-d H:i:s')."' where id = ".$florence_eleve->getAbsenceEleveSaisiesDuJour(VENDREDI_s40j5)->getFirst()->getId());
	    $this->assertFalse($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59')));
	    $this->assertFalse($florence_eleve->checkSynchroAbsenceAgregationTable());
        mysqli_query($GLOBALS["mysqli"], "update a_saisies set updated_at = now() where id = ".$florence_eleve->getAbsenceEleveSaisiesDuJour(VENDREDI_s40j5)->getFirst()->getId());
	    $florence_eleve->updateAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59'));
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59')));
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable());
	    
	    //on va modifier une version de saisie à la main
	    $tomorow = new DateTime();
	    $tomorow->modify("+1 day");
        mysqli_query($GLOBALS["mysqli"], "update a_saisies_version set updated_at = '".$tomorow->format('Y-m-d H:i:s')."' where eleve_id = ".$florence_eleve->getId());
	    $this->assertFalse($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59')));
	    $this->assertFalse($florence_eleve->checkSynchroAbsenceAgregationTable());
        mysqli_query($GLOBALS["mysqli"], "update a_saisies_version set updated_at = now() where eleve_id = ".$florence_eleve->getId());
	    $florence_eleve->updateAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59'));
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59')));
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable());
	    
	    //on va modifier un traitement à la main
	    $tomorow = new DateTime();
	    $tomorow->modify("+1 day");
	    $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour(SAMEDI_s40j6)->getFirst();
	    $traitement_id = AbsenceEleveTraitementQuery::create()->filterByAbsenceEleveSaisie($saisie)->findOne()->getId();
        mysqli_query($GLOBALS["mysqli"], "update a_traitements set updated_at = '".$tomorow->format('Y-m-d H:i:s')."' where id = ".$traitement_id);
	    $this->assertFalse($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59')));
	    $this->assertFalse($florence_eleve->checkSynchroAbsenceAgregationTable());
        mysqli_query($GLOBALS["mysqli"], "update a_traitements set updated_at = now() where id = ".$traitement_id);
	    $florence_eleve->updateAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59'));
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59')));
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable());
	    
	    
	    //on va modifier à la main une saisie
	    sleep(1);
	    $saisie_id = $florence_eleve->getAbsenceEleveSaisiesDuJour(VENDREDI_s40j5)->getFirst()->getId();
        mysqli_query($GLOBALS["mysqli"], "update a_saisies set updated_at = now() where id = ".$saisie_id);
	    $this->assertFalse($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59')));
	    $this->assertFalse($florence_eleve->checkSynchroAbsenceAgregationTable());
        mysqli_query($GLOBALS["mysqli"], "update a_saisies set updated_at = now()-10 where id = ".$saisie_id);
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(DIMANCHE_s41j7.' 23:59:59')));
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable());
        mysqli_query($GLOBALS["mysqli"], "update a_saisies set deleted_at = now() where id = ".$saisie_id);
	    $this->assertFalse($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59')));
	    $this->assertFalse($florence_eleve->checkSynchroAbsenceAgregationTable());
        mysqli_query($GLOBALS["mysqli"], "update a_saisies set deleted_at = now()-10 where id = ".$saisie_id);
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(DIMANCHE_s41j7.' 23:59:59')));
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable());
	    
	    $traitement_id = AbsenceEleveTraitementQuery::create()->filterByAbsenceEleveSaisie($saisie)->findOne()->getId();
        mysqli_query($GLOBALS["mysqli"], "update a_traitements set updated_at = now() where id = ".$traitement_id);
	    $this->assertFalse($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59')));
	    $this->assertFalse($florence_eleve->checkSynchroAbsenceAgregationTable());
        mysqli_query($GLOBALS["mysqli"], "update a_traitements set updated_at = now()-10 where id = ".$traitement_id);
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(DIMANCHE_s41j7.' 23:59:59')));
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable());
        mysqli_query($GLOBALS["mysqli"], "update a_traitements set deleted_at = now() where id = ".$traitement_id);
	    $this->assertFalse($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59')));
	    $this->assertFalse($florence_eleve->checkSynchroAbsenceAgregationTable());
        mysqli_query($GLOBALS["mysqli"], "update a_traitements set deleted_at = now()-10 where id = ".$traitement_id);
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(DIMANCHE_s41j7.' 23:59:59')));
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable());
	    
	    $saisie_version_id = AbsenceEleveSaisieVersionQuery::create()->filterByAbsenceEleveSaisie($florence_eleve->getAbsenceEleveSaisiesDuJour(VENDREDI_s40j5)->getFirst())->findOne()->getId();
        mysqli_query($GLOBALS["mysqli"], "update a_saisies_version set updated_at = now() where id = ".$saisie_version_id);
	    $this->assertFalse($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59')));
	    $this->assertFalse($florence_eleve->checkSynchroAbsenceAgregationTable());
        mysqli_query($GLOBALS["mysqli"], "update a_saisies_version set updated_at = now()-10 where id = ".$saisie_version_id);
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(DIMANCHE_s41j7.' 23:59:59')));
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable());
        mysqli_query($GLOBALS["mysqli"], "update a_saisies_version set deleted_at = now() where id = ".$saisie_version_id);
	    $this->assertFalse($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59')));
	    $this->assertFalse($florence_eleve->checkSynchroAbsenceAgregationTable());
        mysqli_query($GLOBALS["mysqli"], "update a_saisies_version set deleted_at = now()-10 where id = ".$saisie_version_id);
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(DIMANCHE_s41j7.' 23:59:59')));
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable());
	    
	}

	public function testThinCheckAndUpdateSynchroAbsenceAgregationTable() {
	    //on purge les decompte pour florence
	    $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
	    AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->delete();
	    $this->assertFalse($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(MARDI_s41j2.' 23:59:59')));
	    $florence_eleve->thinCheckAndUpdateSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(MARDI_s41j2.' 23:59:59'));
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(MARDI_s41j2.' 23:59:59')));
	    
	    AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->delete();
	    $florence_eleve->thinCheckAndUpdateSynchroAbsenceAgregationTable();
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable());
	    	    
	    AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->delete();
	    $florence_eleve->thinCheckAndUpdateSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59'));
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59')));
	    
	    AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByManquementObligationPresence(true)->filterByNonJustifiee(false)->delete();
	    $this->assertFalse($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59')));
	    $florence_eleve->thinCheckAndUpdateSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59'));
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59')));

	    AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByManquementObligationPresence(true)->filterByNonJustifiee(false)->delete();
	    $this->assertFalse($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59')));
	    $florence_eleve->thinCheckAndUpdateSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59'));
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59')));
	    
	    //on va modifier une saisie à la main
	    $tomorow = new DateTime();
	    $tomorow->modify("+1 day");
        mysqli_query($GLOBALS["mysqli"], "update a_saisies set updated_at = '".$tomorow->format('Y-m-d H:i:s')."' where id = ".$florence_eleve->getAbsenceEleveSaisiesDuJour(VENDREDI_s40j5)->getFirst()->getId());
	    $this->assertFalse($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59')));
	    $this->assertFalse($florence_eleve->checkSynchroAbsenceAgregationTable());
        mysqli_query($GLOBALS["mysqli"], "update a_saisies set updated_at = now() where id = ".$florence_eleve->getAbsenceEleveSaisiesDuJour(VENDREDI_s40j5)->getFirst()->getId());
	    $florence_eleve->thinCheckAndUpdateSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59'));
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59')));
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable());
	    
	    //on va modifier une version de saisie à la main
	    $tomorow = new DateTime();
	    $tomorow->modify("+1 day");
        mysqli_query($GLOBALS["mysqli"], "update a_saisies_version set updated_at = '".$tomorow->format('Y-m-d H:i:s')."' where eleve_id = ".$florence_eleve->getId());
	    $this->assertFalse($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59')));
	    $this->assertFalse($florence_eleve->checkSynchroAbsenceAgregationTable());
        mysqli_query($GLOBALS["mysqli"], "update a_saisies_version set updated_at = now() where eleve_id = ".$florence_eleve->getId());
	    $florence_eleve->thinCheckAndUpdateSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59'));
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59')));
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable());
	    
	    //on va modifier un traitement à la main
	    $tomorow = new DateTime();
	    $tomorow->modify("+1 day");
	    $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour(SAMEDI_s40j6)->getFirst();
	    $traitement_id = AbsenceEleveTraitementQuery::create()->filterByAbsenceEleveSaisie($saisie)->findOne()->getId();
        mysqli_query($GLOBALS["mysqli"], "update a_traitements set updated_at = '".$tomorow->format('Y-m-d H:i:s')."' where id = ".$traitement_id);
	    $this->assertFalse($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59')));
	    $this->assertFalse($florence_eleve->checkSynchroAbsenceAgregationTable());
        mysqli_query($GLOBALS["mysqli"], "update a_traitements set updated_at = now() where id = ".$traitement_id);
	    $florence_eleve->thinCheckAndUpdateSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59'));
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59')));
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable());
	    
	    //on va modifier à la main une saisie
	    sleep(1);
	    $traitement_id = AbsenceEleveTraitementQuery::create()->filterByAbsenceEleveSaisie($saisie)->findOne()->getId();
        mysqli_query($GLOBALS["mysqli"], "update a_traitements set updated_at = now() where id = ".$traitement_id);
	    $this->assertFalse($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59')));
	    $this->assertFalse($florence_eleve->checkSynchroAbsenceAgregationTable());
	    $florence_eleve->thinCheckAndUpdateSynchroAbsenceAgregationTable();
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(DIMANCHE_s41j7.' 23:59:59')));
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable());
	    
	}
	
	public function testCheckAndUpdateSynchroAbsenceAgregationTable() {
	    //on purge les decompte pour florence
	    $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
	    AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->delete();
	    $this->assertFalse($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(MARDI_s41j2.' 23:59:59')));
	    $florence_eleve->checkAndUpdateSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(MARDI_s41j2.' 23:59:59'));
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(MARDI_s41j2.' 23:59:59')));
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable(null, new DateTime(VENDREDI_s40j5.' 00:00:00')));
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(MERCREDI_s41j3.' 00:00:00'), null));
	    
	    //on va modifier à la main une saisie
	    sleep(1);
        mysqli_query($GLOBALS["mysqli"], "update a_saisies set updated_at = now() where id = ".$saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour(VENDREDI_s40j5)->getFirst()->getId());
	    $this->assertFalse($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI_s40j5.' 00:00:00'),new DateTime(VENDREDI_s42j5.' 23:59:59')));
	    $this->assertFalse($florence_eleve->checkSynchroAbsenceAgregationTable());
	    $florence_eleve->checkAndUpdateSynchroAbsenceAgregationTable(new DateTime(LUNDI_s41j1.' 00:00:00'),new DateTime(MARDI_s41j2.' 23:59:59'));
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable(new DateTime(JEUDI_s40j4.' 00:00:00'),new DateTime(DIMANCHE_s41j7.' 23:59:59')));
	    $this->assertTrue($florence_eleve->checkSynchroAbsenceAgregationTable());
	    	    
	}
	
	public function testSortieEleve() {		
		# Absence 21 du LUNDI_a1_s22j1 Sortir l'élève du collège et vérifier qu'aucune absence n'est retournée
	    $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
		# table d'agrégation
	    AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->delete();
	    $florence_eleve->thinCheckAndUpdateSynchroAbsenceAgregationTable();
		
		$saisie_col = $florence_eleve->getAbsenceEleveSaisiesParDate(new DateTime(LUNDI_a1_s22j1.' 00:00:00'),new DateTime(LUNDI_a1_s22j1.' 23:59:59'));
		$this->assertEquals(1,$saisie_col->count());
		$this->assertTrue($saisie_col->getFirst()->getManquementObligationPresence());
		$demi_j_col = $florence_eleve->getDemiJourneesAbsence(new DateTime(LUNDI_a1_s22j1.' 00:00:00'),new DateTime(LUNDI_a1_s22j1.' 23:59:59'));
		$this->assertEquals(2,$demi_j_col->count());	# L'élève est inscrit -> 2 absences
		# table d'agrégation
		$nbAbs = AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)
				->filterByDateIntervalle(new DateTime(LUNDI_a1_s22j1.' 00:00:00'),new DateTime(LUNDI_a1_s22j1.' 23:59:59'))
				->filterByManquementObligationPresence(true);	
	    $this->assertEquals(2,$nbAbs->count());
		
	    $florence_eleve->setDateSortie(strtotime(LUNDI_time_a1_s22j1.' 00:00:00'));	# On sort l'élève
		
		$demi_j_col = $florence_eleve->getDemiJourneesAbsence(new DateTime(LUNDI_a1_s22j1.' 00:00:00'),new DateTime(LUNDI_a1_s22j1.' 23:59:59'));
		$this->assertEquals(0,$demi_j_col->count());	# L'élève n'est plus dans l'établissement -> 0 absence
		$this->assertEquals(0,$florence_eleve->getDemiJourneesAbsenceParPeriode(3)->count());
		$demi_j_col = $florence_eleve->getDemiJourneesNonJustifieesAbsence(new DateTime(LUNDI_a1_s22j1.' 00:00:00'),new DateTime(LUNDI_a1_s22j1.' 23:59:59'));
		$this->assertEquals(0,$demi_j_col->count());
		# table d'agrégation
		$nbAbs = AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)
				->filterByDateIntervalle(new DateTime(LUNDI_a1_s22j1.' 00:00:00'),new DateTime(LUNDI_a1_s22j1.' 23:59:59'))
				->filterByManquementObligationPresence(true);	
	    $this->assertEquals(0,$nbAbs->count());
		
	}

        public function testEquals() {
            Propel::disableInstancePooling();
            $eleve1 = EleveQuery::create()->findOneByLogin('Florence Michu');
            usleep(1);
            $eleve1idem = EleveQuery::create()->findOneByLogin('Florence Michu');
            $this->assertEquals($eleve1, $eleve1idem);
            Propel::enableInstancePooling();
        }
}
