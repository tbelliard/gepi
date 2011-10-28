<?php

require_once dirname(__FILE__) . '/../../../tools/helpers/orm/GepiEmptyTestBase.php';

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
            $eleve->updateAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-05 23:59:59'));
	    }
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-05 23:59:59'), 0));
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-06 23:59:59'), 0));
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-06 23:59:59'), 1));
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-06 23:59:59'), 10));
	    
	    //on va supprimer les marqueurs de calcul
	    AbsenceAgregationDecompteQuery::create()->deleteAll();
	    foreach (EleveQuery::create()->find() as $eleve) {
            $eleve->updateAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-10 23:59:59'));
	    }
	    AbsenceAgregationDecompteQuery::create()->filterByMarqueurFinMiseAJour()->delete();
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-05 23:59:59'), 0));
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-05 23:59:59'), 0));
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-05 23:59:59'), 1));
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-05 23:59:59'), 10));
	    
	    //on va modifier une saisie à la main
	    $tomorow = new DateTime();
	    $tomorow->modify("+1 day");
	    $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
	    $saisie_id = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-01')->getFirst()->getId();
	    mysql_query("update a_saisies set updated_at = '".$tomorow->format('Y-m-d H:i:s')."' where id = ".$saisie_id);
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime('2010-10-02 00:00:00'),new DateTime('2010-10-05 23:59:59'), 0));
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-05 23:59:59'), 0));
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-05 23:59:59'), 1));
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-05 23:59:59'), 10));
	    mysql_query("update a_saisies set updated_at = now() where id = ".$saisie_id);

	    //on va modifier à la main une saisie, un traitement et une version de saisie
	    AbsenceAgregationDecompteQuery::create()->deleteAll();
	    foreach (EleveQuery::create()->find() as $eleve) {
            $eleve->updateAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-10 23:59:59'));
	    }
	    sleep(1);
	    $saisie_id = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-02')->getFirst()->getId();
        mysql_query("update a_saisies set updated_at = now() where id = ".$saisie_id);
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-10 23:59:59'),0));
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-10 23:59:59'),10));
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(null, null, 0));
        mysql_query("update a_saisies set updated_at = now()-10 where id = ".$saisie_id);
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-10 23:59:59'),0));
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(null, null, 0));
	    
	    $traitement_id = AbsenceEleveTraitementQuery::create()->filterByAbsenceEleveSaisie($florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-02')->getFirst())->findOne()->getId();
        mysql_query("update a_traitements set updated_at = now() where id = ".$traitement_id);
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-10 23:59:59'),0));
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-10 23:59:59'),10));
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(null, null, 0));
        mysql_query("update a_traitements set updated_at = now()-10 where id = ".$traitement_id);
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-10 23:59:59'),0));
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(null, null, 0));
        
	    $saisie_version_id = AbsenceEleveSaisieVersionQuery::create()->filterByAbsenceEleveSaisie($florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-01')->getFirst())->findOne()->getId();
        mysql_query("update a_saisies_version set updated_at = now() where id = ".$saisie_version_id);
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-10 23:59:59'),0));
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-10 23:59:59'),10));
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(null, null, 0));
        mysql_query("update a_saisies_version set updated_at = now()-10 where id = ".$saisie_version_id);
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-10 23:59:59'),0));
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(null, null, 0));
	    
	    //on test sur un marqueur d'appel effectué, ça ne doit pas avoir d'incidence sur la table d'agrégation
	    $saisie_id = AbsenceEleveSaisieQuery::create()->filterByFinAbs('2010-10-10 09:00:00')->findOne()->getId();
        mysql_query("update a_saisies set updated_at = now() where id = ".$saisie_id);
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-10 23:59:59'),0));
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-10 23:59:59'),10));
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(null, null, 0));
        mysql_query("update a_saisies set updated_at = now()-10 where id = ".$saisie_id);
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-10 23:59:59'),0));
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(null, null, 0));
	    
	    $saisie_version_id = AbsenceEleveSaisieVersionQuery::create()->filterByAbsenceEleveSaisie(AbsenceEleveSaisieQuery::create()->filterByFinAbs('2010-10-10 09:00:00')->findOne())->findOne()->getId();
	    mysql_query("update a_saisies_version set updated_at = now() where id = ".$saisie_version_id);
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-10 23:59:59'),0));
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-10 23:59:59'),10));
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(null, null, 0));
        mysql_query("update a_saisies_version set updated_at = now()-10 where id = ".$saisie_version_id);
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-10 23:59:59'),0));
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(null, null, 0));
	}
	
	public function testPeerUpdateAgregationTable()
	{
	    //on va modifier à la main une saisie
	    AbsenceAgregationDecompteQuery::create()->deleteAll();
	    foreach (EleveQuery::create()->find() as $eleve) {
            $eleve->updateAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-10 23:59:59'));
	    }
	    sleep(1);
	    $florence_eleve = EleveQuery::create()->findOneByLogin('Florence Michu');
	    $saisie = $florence_eleve->getAbsenceEleveSaisiesDuJour('2010-10-02')->getFirst();
	    $saisie_id = $saisie->getId();
        mysql_query("update a_saisies set updated_at = now() where id = ".$saisie_id);
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-10 23:59:59'),0));
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-10 23:59:59'),10));
	    $this->assertFalse(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(null, null, 0));
	    
	    $col = new PropelCollection();
	    $col->append($saisie);
	    AbsenceAgregationDecomptePeer::updateAgregationTable($col);
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(new DateTime('2010-10-01 00:00:00'),new DateTime('2010-10-10 23:59:59'),0));
	    $this->assertTrue(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable(null, null, 0));
	}

	public function testQueryFilterByDateIntervalle()
	{
	}

	public function testQueryCountRetards()
	{
	}

}
