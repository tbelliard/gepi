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
		$groupe_math->addUtilisateurProfessionnel($lebesgue_prof);
		$groupe_math->addClasse($classe_6A);
		$groupe_math->save();
		
		$aid_1 = new AidDetails();
		$aid_1->setNom('aid 1');
		$aid_1->setId('1ai');
		$aid_1->addEleve($florence_eleve);
		$aid_1->addUtilisateurProfessionnel($newton_prof);
		$aid_1->save();
		
		//		$rowling = new Author();
//		$rowling->setFirstName("J.K.");
//		$rowling->setLastName("Rowling");
//		// no save()
//
//		$stephenson = new Author();
//		$stephenson->setFirstName("Neal");
//		$stephenson->setLastName("Stephenson");
//		$stephenson->save($con);
//		$stephenson_id = $stephenson->getId();
//
//		$byron = new Author();
//		$byron->setFirstName("George");
//		$byron->setLastName("Byron");
//		$byron->save($con);
//		$byron_id = $byron->getId();
//
//		$grass = new Author();
//		$grass->setFirstName("Gunter");
//		$grass->setLastName("Grass");
//		$grass->save($con);
//		$grass_id = $grass->getId();
//
//		$phoenix = new Book();
//		$phoenix->setTitle("Harry Potter and the Order of the Phoenix");
//		$phoenix->setISBN("043935806X");
//		$phoenix->setAuthor($rowling);
//		$phoenix->setPublisher($scholastic);
//		$phoenix->setPrice(10.99);
//		$phoenix->save($con);
//		$phoenix_id = $phoenix->getId();
//
//		$qs = new Book();
//		$qs->setISBN("0380977427");
//		$qs->setTitle("Quicksilver");
//		$qs->setPrice(11.99);
//		$qs->setAuthor($stephenson);
//		$qs->setPublisher($morrow);
//		$qs->save($con);
//		$qs_id = $qs->getId();
//
//		$dj = new Book();
//		$dj->setISBN("0140422161");
//		$dj->setTitle("Don Juan");
//		$dj->setPrice(12.99);
//		$dj->setAuthor($byron);
//		$dj->setPublisher($penguin);
//		$dj->save($con);
//		$dj_id = $dj->getId();
//
//		$td = new Book();
//		$td->setISBN("067972575X");
//		$td->setTitle("The Tin Drum");
//		$td->setPrice(13.99);
//		$td->setAuthor($grass);
//		$td->setPublisher($vintage);
//		$td->save($con);
//		$td_id = $td->getId();
//
//		$r1 = new Review();
//		$r1->setBook($phoenix);
//		$r1->setReviewedBy("Washington Post");
//		$r1->setRecommended(true);
//		$r1->setReviewDate(time());
//		$r1->save($con);
//		$r1_id = $r1->getId();
//
//		$r2 = new Review();
//		$r2->setBook($phoenix);
//		$r2->setReviewedBy("New York Times");
//		$r2->setRecommended(false);
//		$r2->setReviewDate(time());
//		$r2->save($con);
//		$r2_id = $r2->getId();
//
//		$blob_path = _LOB_SAMPLE_FILE_PATH . '/tin_drum.gif';
//		$clob_path =  _LOB_SAMPLE_FILE_PATH . '/tin_drum.txt';
//
//		$m1 = new Media();
//		$m1->setBook($td);
//		$m1->setCoverImage(file_get_contents($blob_path));
//		// CLOB is broken in PDO OCI, see http://pecl.php.net/bugs/bug.php?id=7943
//		if (get_class(Propel::getDB()) != "DBOracle") {
//			$m1->setExcerpt(file_get_contents($clob_path));
//		}
//		$m1->save($con);
//
//		// Add book list records
//		// ---------------------
//		// (this is for many-to-many tests)
//
//		$blc1 = new BookClubList();
//		$blc1->setGroupLeader("Crazyleggs");
//		$blc1->setTheme("Happiness");
//
//		$brel1 = new BookListRel();
//		$brel1->setBook($phoenix);
//
//		$brel2 = new BookListRel();
//		$brel2->setBook($dj);
//
//		$blc1->addBookListRel($brel1);
//		$blc1->addBookListRel($brel2);
//
//		$blc1->save();
//
//		$bemp1 = new BookstoreEmployee();
//		$bemp1->setName("John");
//		$bemp1->setJobTitle("Manager");
//
//		$bemp2 = new BookstoreEmployee();
//		$bemp2->setName("Pieter");
//		$bemp2->setJobTitle("Clerk");
//		$bemp2->setSupervisor($bemp1);
//		$bemp2->save($con);
//
//		$role = new AcctAccessRole();
//		$role->setName("Admin");
//
//		$bempacct = new BookstoreEmployeeAccount();
//		$bempacct->setBookstoreEmployee($bemp1);
//		$bempacct->setAcctAccessRole($role);
//		$bempacct->setLogin("john");
//		$bempacct->setPassword("johnp4ss");
//		$bempacct->save($con);
//
//		// Add bookstores
//
//		$store = new Bookstore();
//		$store->setStoreName("Amazon");
//		$store->setPopulationServed(5000000000); // world population
//		$store->setTotalBooks(300);
//		$store->save($con);
//
//		$store = new Bookstore();
//		$store->setStoreName("Local Store");
//		$store->setPopulationServed(20);
//		$store->setTotalBooks(500000);
//		$store->save($con);
//
//		$summary = new BookSummary();
//		$summary->setSummarizedBook($phoenix);
//		$summary->setSummary("Harry Potter does some amazing magic!");
//		$summary->save();

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
		//'Peer',
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
