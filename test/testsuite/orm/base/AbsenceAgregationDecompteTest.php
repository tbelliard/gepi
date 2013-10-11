<?php

require_once dirname(__FILE__) . '/../../../tools/helpers/orm/GepiEmptyTestBase.php';
require_once dirname(__FILE__) . '/init_date.php';

/**
 * Test class for UtilisateurProfessionnel.
 *
 */
class AbsenceAgregationDecompteTest extends GepiEmptyTestBase
{
	protected function setUp()
	{
		parent::setUp();
		GepiDataPopulator::populate();
	}

	public function testPeerCheckSynchroAbsenceAgregationTable()
	{
	    AbsenceAgregationDecompteQuery::create()->deleteAll();
	    foreach (EleveQuery::create()->find() as $eleve) {
            $eleve->updateAbsenceAgregationTable();
	    }
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(null, null, 0),'Doit renvoyer true quand tout est à jour');
	    
	    //on va tester sur les date (dates plus large que la mise à jour, il va manquer des demi-journées
	    AbsenceAgregationDecompteQuery::create()->deleteAll();
	    foreach (EleveQuery::create()->find() as $eleve) {
            $eleve->updateAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-09 23:59:59'));
	    }
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-09 23:59:59'), 0));
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-10 23:59:59'), 0));
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-10 23:59:59'), 1));
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-10 23:59:59'), 10));
	    
	    //on va supprimer les marqueurs de calcul
	    AbsenceAgregationDecompteQuery::create()->deleteAll();
	    foreach (EleveQuery::create()->find() as $eleve) {
            $eleve->updateAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-14 23:59:59'));
	    }
	    AbsenceAgregationDecompteQuery::create()->filterByMarqueurFinMiseAJour()->delete();
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-09 23:59:59'), 0));
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-09 23:59:59'), 0));
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-09 23:59:59'), 1));
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-09 23:59:59'), 10));
	    
	    //on va modifier une saisie à la main
	    $tomorow = new DateTime();
	    $tomorow->modify("+1 day");
	    $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
	    $saisie_id = $florence_eleve->getAbsenceEleveSaisiesDuJour(VENDREDI20101001)->getFirst()->getId();
	    mysql_query("update a_saisies set updated_at = '".$tomorow->format('Y-m-d H:i:s')."' where id = ".$saisie_id);
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(SAMEDI20101002.' 00:00:00'),new DateTime('2012-10-09 23:59:59'), 0));
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-09 23:59:59'), 0));
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-09 23:59:59'), 1));
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-09 23:59:59'), 10));
	    mysql_query("update a_saisies set updated_at = now() where id = ".$saisie_id);

	    //on va modifier à la main une saisie, un traitement et une version de saisie
	    AbsenceAgregationDecompteQuery::create()->deleteAll();
	    foreach (EleveQuery::create()->find() as $eleve) {
            $eleve->updateAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-20 23:59:59'));
	    }
	    sleep(1);
	    $saisie_id = $florence_eleve->getAbsenceEleveSaisiesDuJour(SAMEDI20101002)->getFirst()->getId();
        mysql_query("update a_saisies set updated_at = now() where id = ".$saisie_id);
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-14 23:59:59'),0));
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-14 23:59:59'),10));
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(null, null, 0));
        mysql_query("update a_saisies set updated_at = now()-10 where id = ".$saisie_id);
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-14 23:59:59'),0));
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(null, null, 0));
        mysql_query("update a_saisies set deleted_at = now() where id = ".$saisie_id);
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-14 23:59:59'),0));
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-14 23:59:59'),10));
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(null, null, 0));
        mysql_query("update a_saisies set deleted_at = now()-10 where id = ".$saisie_id);
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-14 23:59:59'),0));
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(null, null, 0));
	    
	    $traitement_id = AbsenceEleveTraitementQuery::create()->filterByAbsenceEleveSaisie($florence_eleve->getAbsenceEleveSaisiesDuJour(SAMEDI20101002)->getFirst())->findOne()->getId();
        mysql_query("update a_traitements set updated_at = now() where id = ".$traitement_id);
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-14 23:59:59'),0));
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-14 23:59:59'),10));
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(null, null, 0));
        mysql_query("update a_traitements set updated_at = now()-10 where id = ".$traitement_id);
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-14 23:59:59'),0));
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(null, null, 0));
        mysql_query("update a_traitements set deleted_at = now() where id = ".$traitement_id);
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-14 23:59:59'),0));
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-14 23:59:59'),10));
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(null, null, 0));
        mysql_query("update a_traitements set deleted_at = now()-10 where id = ".$traitement_id);
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-14 23:59:59'),0));
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(null, null, 0));
	    
	    $saisie_version_id = AbsenceEleveSaisieVersionQuery::create()->filterByAbsenceEleveSaisie($florence_eleve->getAbsenceEleveSaisiesDuJour(VENDREDI20101001)->getFirst())->findOne()->getId();
        mysql_query("update a_saisies_version set updated_at = now() where id = ".$saisie_version_id);
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-14 23:59:59'),0));
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-14 23:59:59'),10));
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(null, null, 0));
        mysql_query("update a_saisies_version set updated_at = now()-10 where id = ".$saisie_version_id);
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-14 23:59:59'),0));
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(null, null, 0));
        mysql_query("update a_saisies_version set deleted_at = now() where id = ".$saisie_version_id);
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-14 23:59:59'),0));
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-14 23:59:59'),10));
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(null, null, 0));
        mysql_query("update a_saisies_version set deleted_at = now()-10 where id = ".$saisie_version_id);
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-14 23:59:59'),0));
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(null, null, 0));
	    
	    //on test sur un marqueur d'appel effectué, ça ne doit pas avoir d'incidence sur la table d'agrégation
	    $saisie_id = AbsenceEleveSaisieQuery::create()->filterByFinAbs('2012-10-14 09:00:00')->findOne()->getId();
        mysql_query("update a_saisies set updated_at = now() where id = ".$saisie_id);
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-14 23:59:59'),0));
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-14 23:59:59'),10));
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(null, null, 0));
        mysql_query("update a_saisies set updated_at = now()-10 where id = ".$saisie_id);
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-14 23:59:59'),0));
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(null, null, 0));
	    
	    $saisie_version_id = AbsenceEleveSaisieVersionQuery::create()->filterByAbsenceEleveSaisie(AbsenceEleveSaisieQuery::create()->filterByFinAbs('2012-10-14 09:00:00')->findOne())->findOne()->getId();
	    mysql_query("update a_saisies_version set updated_at = now() where id = ".$saisie_version_id);
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-14 23:59:59'),0));
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-14 23:59:59'),10));
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(null, null, 0));
        mysql_query("update a_saisies_version set updated_at = now()-10 where id = ".$saisie_version_id);
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-14 23:59:59'),0));
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(null, null, 0));
	    
	    $saisie = AbsenceEleveSaisieQuery::create()->filterByDebutAbs('2012-10-20 08:00:00')->findOne();
	    $traitement = $saisie->getAbsenceEleveTraitements()->getFirst();
	    mysql_query("update a_traitements set updated_at = now() where id = ".$traitement->getId());
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-20 23:59:59'),0));
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-20 23:59:59'),10));
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(null, null, 0));
	    mysql_query("update a_traitements set updated_at = now()-10 where id = ".$traitement->getId());
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-20 23:59:59'),0));
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(null, null, 0));
        
        
	    //on va tester le passage à l'heure d'été
	    AbsenceAgregationDecompteQuery::create()->deleteAll();
	    foreach (EleveQuery::create()->find() as $eleve) {
            $eleve->updateAbsenceAgregationTable(new DateTime('2013-03-31 00:00:00'),new DateTime('2013-03-31 23:59:59'));
	    }
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime('2013-03-31 00:00:00'),new DateTime('2013-03-31 23:59:59'), 1));
	    
	   
	}
	
	public function testPeerUpdateAgregationTable()
	{
	    //on va modifier à la main une saisie
	    AbsenceAgregationDecompteQuery::create()->deleteAll();
	    foreach (EleveQuery::create()->find() as $eleve) {
            $eleve->updateAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-14 23:59:59'));
	    }
	    sleep(1);
	    $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
	    $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour(SAMEDI20101002)->getFirst();
	    $saisie_id = $saisie->getId();
        mysql_query("update a_saisies set updated_at = now() where id = ".$saisie_id);
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(null, null, 0));
	    $col = new PropelCollection();
	    $col->append($saisie);
	    AbsenceAgregationDecomptePeer::updateAgregationTable($col);
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-14 23:59:59'),0));
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(null, null, 0));

	    AbsenceAgregationDecompteQuery::create()->deleteAll();
	    foreach (EleveQuery::create()->find() as $eleve) {
            $eleve->updateAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-14 23:59:59'));
	    }
	    sleep(1);
	    $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
	    $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour(VENDREDI20101001)->getFirst();
	    $saisie_id = $saisie->getId();
        mysql_query("update a_saisies set fin_abs = '".VENDREDI20101001." 08:10:00' where id = ".$saisie_id);//ça devient un retard
	    $decompte = AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByDateDemiJounee(VENDREDI20101001)->findOne();
        $this->assertTrue($decompte->getManquementObligationPresence());
        $this->assertEquals(0,$decompte->getRetards());
        $saisie->getEleve()->clearAllReferences();
        $saisie->clearAllReferences();
        $saisie->reload();
	    $col = new PropelCollection();
	    $col->append($saisie);
	    AbsenceAgregationDecomptePeer::updateAgregationTable($col);
	    $decompte = AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByDateDemiJounee(VENDREDI20101001)->findOne();
	    $this->assertFalse($decompte->getManquementObligationPresence());
        $this->assertEquals(1,$decompte->getRetards());
        $saisie->setFinAbs(VENDREDI20101001.' 09:00:00');
        $saisie->save();
        
	    AbsenceAgregationDecompteQuery::create()->deleteAll();
        AbsenceAgregationDecomptePeer::updateAgregationTable(AbsenceEleveSaisieQuery::create()->filterByPlageTemps(new DateTime(VENDREDI20101001), new DateTime('2012-10-14'))->find());
        $eleve_col = EleveQuery::create()->useAbsenceEleveSaisieQuery()->filterByPlageTemps(new DateTime(VENDREDI20101001), new DateTime('2012-10-14'))->endUse()->find();
        foreach($eleve_col as $eleve) {
            $this->assertTrue($eleve->checkSynchroAbsenceAgregationTable(new DateTime(SAMEDI20101002.' 00:00:00'),new DateTime('2012-10-09')));
        }
	}

	public function testQueryFilterByDateIntervalle()
	{
	    AbsenceAgregationDecompteQuery::create()->deleteAll();
	    foreach (EleveQuery::create()->find() as $eleve) {
            $eleve->updateAbsenceAgregationTable(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2012-10-24 23:59:59'));
	    }
	    $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
	    $this->assertEquals(4,AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByDateIntervalle(new DateTime(VENDREDI20101001),new DateTime('2012-10-07'))->count());
	    $this->assertEquals(5,AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByDateIntervalle(new DateTime(VENDREDI20101001),new DateTime('2012-10-07 12:00:00'))->count());
            $this->assertEquals(1,AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByDateIntervalle(new DateTime('2012-10-23 08:00:00'),new DateTime('2012-10-23 10:00:00'))->count()); 
            $date_debut=new DateTime('2012-10-23 14:00:00');
            $date_fin=new DateTime('2012-10-23 15:00:00');
            saveSetting('abs2_heure_demi_journee','12:30');
            $this->assertEquals(1,AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByDateIntervalle($date_debut,$date_fin)->count());
            $this->assertEquals(1,AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByDateIntervalle($date_debut,$date_fin)->count());
            saveSetting('abs2_heure_demi_journee','11:50');
            $this->assertEquals(1,AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByDateIntervalle(new DateTime('2012-10-23 14:00:00'),new DateTime('2012-10-23 15:00:00'))->count());
            $this->assertEquals(2,AbsenceAgregationDecompteQuery::create()->filterByEleve($florence_eleve)->filterByDateIntervalle(new DateTime('2012-10-23 08:00:00'),new DateTime('2012-10-23 16:00:00'))->count());
	}

	public function testQueryCountRetards()
	{
	    AbsenceAgregationDecompteQuery::create()->deleteAll();
	    foreach (EleveQuery::create()->find() as $eleve) {
            $eleve->updateAbsenceAgregationTable();
	    }
	    $this->assertEquals(6,AbsenceAgregationDecompteQuery::create()->filterByDateIntervalle(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2013-06-03 23:59:59'))->countRetards());
	    $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
	    $this->assertEquals(6,AbsenceAgregationDecompteQuery::create()->filterByDateIntervalle(new DateTime(VENDREDI20101001.' 00:00:00'),new DateTime('2013-06-03 23:59:59'))->filterByEleve($florence_eleve)->countRetards());
	}

}
