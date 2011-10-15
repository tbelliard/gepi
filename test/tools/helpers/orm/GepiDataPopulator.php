<?php

class GepiDataPopulator
{

	public static function populate($con = null)
	{
		if($con === null) {
			$con = Propel::getConnection();
		}			
		
		$con->beginTransaction();

		// Add utilisateur records
		// ---------------------

		$lebesgue_prof = new UtilisateurProfessionnel();
		$lebesgue_prof->setLogin('Lebesgue');
		$lebesgue_prof->setStatut('professeur');
		$lebesgue_prof->setPreferenceValeur('glace_parfum','chocolat');
		$lebesgue_prof->save($con);
		
		$newton_prof = new UtilisateurProfessionnel();
		$newton_prof->setLogin('Newton');
		$newton_prof->setStatut('professeur');
		$newton_prof->save($con);
		
		$curie_prof = new UtilisateurProfessionnel();
		$curie_prof->setLogin('Curie');
		$curie_prof->setStatut('professeur');
		$curie_prof->save($con);
		
		$dolto_cpe = new UtilisateurProfessionnel();
		$dolto_cpe->setLogin('Dolto');
		$dolto_cpe->setStatut('cpe');
		$dolto_cpe->save($con);
		
		$aubert_scola = new UtilisateurProfessionnel();
		$aubert_scola->setLogin('Aubert');
		$aubert_scola->setStatut('scolarite');
		$aubert_scola->save($con);
		
		$florence_eleve = new Eleve();
		$florence_eleve->setLogin('Florence Michu');
		$florence_eleve->setEleId('00112233');
		$florence_eleve->save();
		$dolto_cpe->addEleve($florence_eleve);
		$dolto_cpe->save();
		$newton_prof->addEleve($florence_eleve);
		$newton_prof->save();
		
		$nicolas_eleve = new Eleve();
		$nicolas_eleve->setLogin('Nicolas Dupont');
		$nicolas_eleve->setEleId('00112234');
		$nicolas_eleve->save();
		
		$michel_eleve = new Eleve();
		$michel_eleve->setLogin('Michel Martin');
		$michel_eleve->setEleId('00112235');
		$michel_eleve->save();
		
		$classe_6A = new Classe();
		$classe_6A->setNom('6ieme A');
		$classe_6A->save();
		$periode_6A_1 = new PeriodeNote();
		$periode_6A_1->setClasse($classe_6A);
		$periode_6A_1->setNumPeriode(1);
		$periode_6A_1->setVerouiller('O');
		$periode_6A_1->setNomPeriode('premier trimestre');
		$periode_6A_1->setDateFin('2010-12-01 00:00:00');
		$periode_6A_1->save();
		$periode_6A_2 = new PeriodeNote();
		$periode_6A_2->setClasse($classe_6A);
		$periode_6A_2->setNumPeriode(2);
		$periode_6A_2->setVerouiller('N');
		$periode_6A_2->setNomPeriode('deuxième trimestre');
		$periode_6A_2->setDateFin('2011-03-01 23:59:59');
		$periode_6A_2->save();
		
		$classe_6A->addEleve($florence_eleve,1);//florence est dans la 6A pour les deux premiers trimestres et dans la 6B pour les deux suivants
		$classe_6A->addEleve($florence_eleve,2);
		$aubert_scola->addClasse($classe_6A);
		$aubert_scola->save();
		
		$classe_6B = new Classe();
		$classe_6B->setNom('6ieme B');
		$classe_6B->save();
		$periode_6B_2 = new PeriodeNote();
		$periode_6B_2->setClasse($classe_6B);
		$periode_6B_2->setNumPeriode(2);
		$periode_6B_2->setVerouiller('O');
		$periode_6B_2->setNomPeriode('deuxième trimestre');
		$periode_6B_2->setDateFin('2011-03-01 23:59:59');
		$periode_6B_2->save();
		$periode_6B_3 = new PeriodeNote();
		$periode_6B_3->setClasse($classe_6B);
		$periode_6B_3->setNumPeriode(3);
		$periode_6B_3->setVerouiller('O');
		$periode_6B_3->setNomPeriode('troisième trimestre');
		$periode_6B_3->setDateFin('2011-07-01 23:59:59');
		$periode_6B_3->save();
		$classe_6B->addEleve($nicolas_eleve,1);
		$classe_6B->addEleve($nicolas_eleve,2);
		$classe_6B->addEleve($florence_eleve,3);
		
		$groupe_math = new Groupe();
		$groupe_math->setName('MATH6A');
		$groupe_math->addEleve($florence_eleve, 1);
		$groupe_math->addEleve($florence_eleve, 2);
		$groupe_math->addEleve($florence_eleve, 3);
		$groupe_math->addUtilisateurProfessionnel($lebesgue_prof);
		$groupe_math->addClasse($classe_6A);
		$groupe_math->addClasse($classe_6B);
		$groupe_math->save();
		
		$aid_1 = new AidDetails();
		$aid_1->setNom('aid 1');
		$aid_1->setId('1ai');
		$aid_1->addEleve($florence_eleve);
		$aid_1->addUtilisateurProfessionnel($newton_prof);
		$aid_1->save();
		
        //on va peupler les absences 2
        include_once(dirname(__FILE__).'/../../../../mod_abs2/admin/function.php');
        ajoutMotifsParDefaut();
        ajoutLieuxParDefaut();
        ajoutJustificationsParDefaut();
        ajoutTypesParDefaut();
        
        $saisie = new AbsenceEleveSaisie();
        $saisie->setEleve($florence_eleve);
        $saisie->setUtilisateurProfessionnel($lebesgue_prof);
        $saisie->setDebutAbs('2010-10-01 08:00:00');//le 2010-10-01 est un vendredi
        $saisie->setFinAbs('2010-10-01 09:00:00');
        $saisie->save();
        
        
		$con->commit();
	}


	public static function depopulate($con = null)
	{
		$peerClasses = array(
        	'UtilisateurProfessionnelPeer',
        	'ElevePeer',
        	'ClassePeer',
        	'GroupePeer',
        	'AidDetailsPeer',
        	'AbsenceEleveSaisiePeer',
        	'AbsenceEleveTraitementPeer',
        	'AbsenceEleveNotificationPeer',
        	'AbsenceEleveTypeStatutAutorisePeer',
		    'AbsenceEleveLieuPeer',
		    'AbsenceEleveTypePeer',
		    'AbsenceEleveJustificationPeer',
			'AbsenceEleveMotifPeer',
		);
		// free the memory from existing objects
		foreach ($peerClasses as $peerClass) {
			// $peerClass::$instances crashes on PHP 5.2, see http://www.propelorm.org/ticket/1388
			$r = new ReflectionClass($peerClass);
			$p = $r->getProperty('instances');
			foreach ($p->getValue() as $o) {
				$o->clearAllReferences();
			}
		}
		// delete records from the database
		if($con === null) {
			$con = Propel::getConnection();
		}
		$con->beginTransaction();
		foreach ($peerClasses as $peerClass) {
			// $peerClass::doDeleteAll() crashes on PHP 5.2, see http://www.propelorm.org/ticket/1388
			call_user_func(array($peerClass, 'doDeleteAll'), $con);
		}
		$con->commit();
	}

}
