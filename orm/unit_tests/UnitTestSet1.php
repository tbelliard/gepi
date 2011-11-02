<?php
/*
 * $Id: index.php 2356 2008-09-05 14:02:27Z jjocal $
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Gabriel Fischer
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GEPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GEPI; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

// On désamorce une tentative de contournement du traitement anti-injection lorsque register_globals=on
if (isset($_GET['traite_anti_inject']) || isset($_POST['traite_anti_inject'])) $traite_anti_inject = "yes";

$niveau_arbo = "2";
// Initialisations files
include("../../lib/initialisationsPropel.inc.php");
require_once("../../lib/initialisations.inc.php");
Propel::init('../propel-build/conf/gepi-conf_debug.php');
include('UnitTestUtilisateurProfessionnel.php');
include('UnitTestEleve.php');
include('UnitTestGroupe.php');
include('UnitTestClasse.php');
include('UnitTestResponsableEleve.php');
//include("../propel/logger/STACKLogger.php");
$logger = new StackLogger();
Propel::setLogger($logger);


// On met le header en petit par défaut
$_SESSION['cacher_header'] = "y";
//**************** EN-TETE *****************

//require_once("../../lib/header.inc");
//**************** FIN EN-TETE *************
purgeDonneesTest($logger);


//Creation d'un utilisateur
$utilisateurProfessionnel = new UtilisateurProfessionnel();
$utilisateurProfessionnel = UnitTestUtilisateurProfessionnel::getUtilisateurProfessionnel();
$utilisateurProfessionnel->save();
$newUtilisateurProfessionnel = UtilisateurProfessionnelPeer::retrieveByPK($utilisateurProfessionnel->getLogin());
echo ($logger->getDisplay());
if ($newUtilisateurProfessionnel == null) {
	echo('test creation utilisateur professionnel a <font color="red">echoue</font> <br><br/>.');
} else {
	echo('test creation utilisateur professionnel a reussi avec comme retour l\'id : ' . $newUtilisateurProfessionnel->getLogin() . '<br/><br/>');
}

//Creation d'un eleve
$eleve = UnitTestEleve::getEleve();
$eleve->save();
$newEleve = ElevePeer::retrieveByPK($eleve->getIdEleve());
echo ($logger->getDisplay());
if ($newEleve == null) {
	echo('test creation eleve a <font color="red">echoue</font> <br><br/>');
} else {
	echo('test creation eleve a reussi avec comme retour l\'id : ' . $eleve->getIdEleve() . '<br/><br/>');
}

//Creation d'un groupe
$groupe = new Groupe();
$groupe->getName();
$groupe = UnitTestGroupe::getGroupe();
$groupe->save();
$newGroupe = GroupePeer::retrieveByPK($groupe->getId());
echo ($logger->getDisplay());
if ($newGroupe == null) {
	echo('test creation groupe a <font color="red">echoue</font> <br><br/>');
} else {
	echo('test creation groupe a reussi avec comme retour l\'id : ' . $groupe->getId() . '<br/><br/>');
}

//Creation d'une classe
$classe = UnitTestClasse::getClasse();
$classe->save();
$newClasse = ClassePeer::retrieveByPK($classe->getId());
echo ($logger->getDisplay());
if ($newClasse == null) {
	echo('test creation classe a <font color="red">echoue</font> <br><br/>');
} else {
	echo('test creation classe a reussi avec comme retour l\'id : ' . $classe->getId() . '<br/><br/>');
}

//ajout d'une periode ouverte et d'un periode fermée à une classe
$periode_fermee = new PeriodeNote();
$periode_fermee->setNumPeriode(1);
$periode_fermee->setVerouiller('O');
$periode_fermee->setNomPeriode('1 Unit test');
$newClasse->addPeriodeNote($periode_fermee);
$periode_fermee->save();
echo ($logger->getDisplay());
$periode_ouverte = new PeriodeNote();
$periode_ouverte->setNumPeriode(2);
$periode_ouverte->setVerouiller('N');
$periode_ouverte->setNomPeriode('2 Unit test');
$newClasse->addPeriodeNote($periode_ouverte);
$periode_ouverte->save();
echo ($logger->getDisplay());

$periode_ouverte = $newClasse->getPeriodeNoteOuverte();
echo ($logger->getDisplay());
if ($periode_ouverte == null || $periode_ouverte->getNumPeriode() != 2) {
	echo('test ajout periode a <font color="red">echoue</font> <br><br/>');
} else {
	echo('test ajout periode a reussi<br/><br/>');
}


//ajout de eleve au professeur en tant que cpe
//$utilisateurProfessionnel = new UtilisateurProfessionnel();
$utilisateurProfessionnel->setStatut('cpe');
$utilisateurProfessionnel->addEleve($eleve);
$newEleve1 = $utilisateurProfessionnel->getEleves();
$newEleve11 = $newEleve1[0];
if ($newEleve1 == null || $newEleve11->getIdEleve() != $eleve->getIdEleve()) {
	echo ($logger->getDisplay());
	echo('test ajout de eleve au professeur en tant que cpe a <font color="red">echoue</font> <br><br/>');
} else {
	echo ($logger->getDisplay());
	echo('test ajout de eleve au professeur en tant que cpe a reussi <br><br/>');
}

//ajout de eleve au groupe periode 1 et 2
$groupe->addEleve($eleve, 1);
$newEleve2 = $groupe->getEleves(1);
$newEleve3 = $newEleve2[0];
echo ($logger->getDisplay());
if ($newEleve3 == null) {
	echo('test 1 ajout de eleve au groupe a <font color="red">echoue</font> au premier test<br><br/>');
} else {
	echo('test 1 ajout de eleve au groupe a reussi <br><br/>');
	$newGroupe2 = $newEleve3->getGroupes(1);
	echo ($logger->getDisplay());
	if ($newGroupe2[0] != null && $newGroupe2[0]->getId() == $groupe->getId()) {
		echo ($logger->getDisplay());
		echo('test 2 ajout de eleve au groupe a reussi <br><br/>');
	} else {
		echo ($logger->getDisplay());
		echo('test 2 ajout de eleve au groupe a <font color="red">echoue</font> <br><br/>');
	}
}

//ajout de eleve a la classe groupe periode 2
$classe->addEleve($eleve, 2);
$newEleve2 = $classe->getEleves(2);
$newEleve3 = $newEleve2[0];
echo ($logger->getDisplay());
if ($newEleve3 == null) {
	echo('test 1 ajout de eleve a la classe a <font color="red">echoue</font> <br><br/>');
} else {
	echo('test 1 ajout de eleve a la classe a reussi <br><br/>');
	$newClasse2 = $newEleve3->getClasses(2);
	if ($newClasse2[0] != null && $newClasse2[0]->getId() == $classe->getId()) {
		echo ($logger->getDisplay());
		echo('test 2 ajout de eleve a la classe a reussi <br><br/>');
	} else {
		echo ($logger->getDisplay());
		echo('test 2 ajout de eleve a la classe a <font color="red">echoue</font> <br><br/>');
	}
}

$periodeNoteOuverte = $newEleve3->getPeriodeNoteOuverte();
if ($periodeNoteOuverte == null) {
	echo('test recuperation de periode actuelle a partir d\'un eleve a <font color="red">echoue</font> <br><br/>');
} else {
	if ($periodeNoteOuverte->getNumPeriode() == 2) {
		echo ($logger->getDisplay());
		echo('test recuperation de periode actuelle a partir d\'un eleve a reussi <br><br/>');
	} else {
		echo ($logger->getDisplay());
		echo('test recuperation de periode actuelle a partir d\'un eleve a <font color="red">echoue</font> <br><br/>');
	}
}

//Creation d'un responsable pour l'eleve
$respInformation = UnitTestResponsableEleve::getResponsableInformation();
//$respInformation = new ResponsableInformation();
$respPers = UnitTestResponsableEleve::getResponsableEleve();
//$respPers = new ResponsableEleve();
$respAdr = UnitTestResponsableEleve::getResponsableEleveAdresse();

$respInformation->setEleve($eleve);
$respInformation->setResponsableEleve($respPers);
$respPers->setResponsableEleveAdresse($respAdr);
$respInformation->save();
//$eleve = new Eleve();
$respInfos = $eleve->getResponsableInformations();
if ($respInfos[0] == null) {
	echo ($logger->getDisplay());
	echo('test ajout des responsables information a l eleve a <font color="red">echoue</font> <br><br/>');
} else {
	$respInformation = $respInfos[0];
	if ($respInformation->getResponsableEleve()->getPersId() != UnitTestResponsableEleve::getResponsableEleve()->getPersId()) {
		echo ($logger->getDisplay());
		echo('test ajout des responsables information a l eleve a <font color="red">echoue</font> <br><br/>');
	} else {
		$respEleve = $respInformation->getResponsableEleve();
		if ($respEleve->getResponsableEleveAdresse()->getAdrId() != UnitTestResponsableEleve::getResponsableEleveAdresse()->getAdrId()) {
			echo ($logger->getDisplay());
			echo('test ajout des responsables information a l eleve a <font color="red">echoue</font> <br><br/>');
		} else {
			echo ($logger->getDisplay());
			echo('test ajout ajout des responsables information a l eleve a reussi <br><br/>');
		}
	}
}

echo ($logger->getDisplay());

purgeDonneesTest($logger);
Propel::setLogger(null);

function purgeDonneesTest($logger) {
	echo "Purge des données<br/><br/>";
	//purge de l'utilisateur
	echo "<br/>Purge de l'utilisateur : <br/>";
	$utilisateurProfessionnel = UtilisateurProfessionnelPeer::retrieveByPK(UnitTestUtilisateurProfessionnel::getUtilisateurProfessionnel()->getLogin());
	if ($utilisateurProfessionnel != null)	{
		$utilisateurProfessionnel->delete();
	}
	echo ($logger->getDisplay());

	//purge du groupe
	echo "<br/>Purge du groupe : <br/>";
	$criteria = new Criteria();
	$criteria->add(GroupePeer::NAME, UnitTestGroupe::getGroupe()->getName());
	$groupe = GroupePeer::doSelectOne($criteria);
	if ($groupe != null) {
		$groupe->delete();
	}
	echo ($logger->getDisplay());

	//purge de la classe
	echo "<br/>Purge de la classe :<br/>";
	$criteria = new Criteria();
	$criteria->add(ClassePeer::CLASSE, UnitTestClasse::getClasse()->getNom());
	$classe = ClassePeer::doSelectOne($criteria);
	if ($classe != null) {
		$classe->delete();
	}
	echo ($logger->getDisplay());

	//purge de l'eleve
	echo "<br/>Purge de l'eleve<br/>";
	$criteria = new Criteria();
	$criteria->add(ElevePeer::LOGIN, UnitTestEleve::getEleve()->getLogin());
	$eleve = ElevePeer::doSelectOne($criteria);
	if ($eleve != null) {
		$eleve->delete();
	}
	echo ($logger->getDisplay());

	//purge du responsable legal et de son adresse
	echo "<br/>Purge du responsable legal et de son adresse<br/>";
	$criteria = new Criteria();
	$criteria->add(ResponsableElevePeer::PERS_ID, UnitTestResponsableEleve::getResponsableEleve()->getPersId());
	$responsableEleve = ResponsableElevePeer::doSelectOne($criteria);
	if ($responsableEleve != null) {
		$responsableEleveAdresse = $responsableEleve->getResponsableEleveAdresse();
		if ($responsableEleveAdresse != null) {
			$responsableEleveAdresse->delete();
		}
		$responsableEleve->delete();
	}
	$criteria = new Criteria();
	$criteria->add(ResponsableEleveAdressePeer::ADR_ID, UnitTestResponsableEleve::getResponsableEleveAdresse()->getAdrId());
	$responsableEleveAdresse = ResponsableEleveAdressePeer::doSelectOne($criteria);
	if ($responsableEleveAdresse != null) {
		$responsableEleveAdresse->delete();
	}

	echo ($logger->getDisplay());

	echo "<br/>Fin Purge des données<br/><br/>";
}

?>
