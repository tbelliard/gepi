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

		$lebesgue = new UtilisateurProfessionnel();
		$lebesgue->setLogin('Lebesgue');
		$lebesgue->setStatut('professeur');
		$lebesgue->save($con);
		
		$dolto = new UtilisateurProfessionnel();
		$dolto->setLogin('Dolto');
		$dolto->setStatut('cpe');
		$dolto->save($con);
		
		$florence = new Eleve();
		$florence->setLogin('Florene Michu');
		$florence->setEleId('00112233');
		$florence->save();
		$dolto->addEleve($florence);
		$dolto->save();
		
		$classe_6A = new Classe();
		$classe_6A->setNom('6ieme A');
		$classe_6A->addEleve($florence,1);
		$classe_6A->addEleve($florence,2);
		
		$groupe_math = new Groupe();
		$groupe_math->setName('MATH6A');
		$groupe_math->addEleve($florence, 1);
		$groupe_math->addEleve($florence, 2);
		$groupe_math->addUtilisateurProfessionnel($lebesgue);
		$groupe_math->save();
		
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
