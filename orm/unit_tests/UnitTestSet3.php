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
$logger = new StackLogger();
Propel::setLogger($logger);

include('UnitTestUtilisateurProfessionnel.php');
include('UnitTestEleve.php');
include('UnitTestGroupe.php');
include('UnitTestClasse.php');


// On met le header en petit par défaut
$_SESSION['cacher_header'] = "y";
//**************** EN-TETE *****************

//require_once("../../lib/header.inc");
//**************** FIN EN-TETE *************
purgeDonneesTest($logger);

//recuperation d'un creneau
$time = mktime(0, 0, 0);
$creneau = EdtCreneauPeer::retrieveEdtCreneauActuel($time);
echo ($logger->getDisplay());
if ($creneau != null) {
	echo('test recuperation d\'un creneau à minuit a <font color="red">echoue</font> <br><br/> : ');
	echo('creneau retourné : '.$creneau->getNomDefiniePeriode());
	echo(' alors qu\'on ne devrait pas avoir de retour en toute logique');
} else {
	echo('test recuperation d\'un creneau à minuit a reussi : pas de retour <br><br/>');
}

$time = mktime(8, 40, 0);
$creneau = EdtCreneauPeer::retrieveEdtCreneauActuel($time);
echo ($logger->getDisplay());
if ($creneau == null) {
	echo('test recuperation d\'un creneau à 8h40 a <font color="red">echoue</font> : ');
	echo('pas de retour <br><br/>');
} else {
	echo('test recuperation d\'un creneau à 8h40 a reussi : '.$creneau->getNomDefiniePeriode().'<br><br/>');
}

$creneau = EdtCreneauPeer::retrieveEdtCreneauActuel('8:40');
echo ($logger->getDisplay());
if ($creneau == null) {
	echo('test recuperation d\'un creneau à 8h40 a <font color="red">echoue</font> : ');
	echo('pas de retour <br><br/>');
} else {
	echo('test recuperation d\'un creneau à 8h40 a reussi : '.$creneau->getNomDefiniePeriode().'<br><br/>');
}


//Creation d'un utilisateur
$utilisateurProfessionnel = new UtilisateurProfessionnel();
$utilisateurProfessionnel = UnitTestUtilisateurProfessionnel::getUtilisateurProfessionnel();
$utilisateurProfessionnel->save();
$newUtilisateurProfessionnel = UtilisateurProfessionnelPeer::retrieveByPK($utilisateurProfessionnel->getLogin());
$logger->getDisplay();
if ($newUtilisateurProfessionnel == null) {
	echo('test creation utilisateur professionnel a <font color="red">echoue</font> <br><br/>.');
} else {
	echo('test creation utilisateur professionnel a reussi avec comme retour l\'id : ' . $newUtilisateurProfessionnel->getLogin() . '<br/><br/>');
}

//Creation d'un groupe
$groupe = new Groupe();
$groupe->getName();
$groupe = UnitTestGroupe::getGroupe();
$groupe->save();
$newGroupe = GroupePeer::retrieveByPK($groupe->getId());
$logger->getDisplay();
if ($newGroupe == null) {
	echo('test creation groupe a <font color="red">echoue</font> <br><br/>');
} else {
	echo('test creation groupe a reussi avec comme retour l\'id : ' . $groupe->getId() . '<br/><br/>');
}

//ajout du groupe au professeur
$newUtilisateurProfessionnel->addGroupe($groupe);
$newUtilisateurProfessionnel->save();
$newGroupes = $newUtilisateurProfessionnel->getGroupes();
echo ($logger->getDisplay());
if ($newGroupes->count() != 1) {
	echo('test ajout groupe au professeur a <font color="red">echoue</font> <br><br/>');
} else {
	echo('test ajout groupe au professeur a reussi<br/><br/>');
}

$edtCours = new EdtEmplacementCours();
$edtCours->setGroupe($groupe);
$edtCours->setHeuredebDec('0.5');
$edtCours->setUtilisateurProfessionnel($newUtilisateurProfessionnel);
$edtCours->setEdtCreneau(EdtCreneauPeer::retrieveFirstEdtCreneau());
$edtCours->setJourSemaine('lundi');
$edtCours->setTypeSemaine('');
$edtCours->save();
echo ($logger->getDisplay());
echo('emplacement de cours ajouté.<br>');
echo('Debut du cours : '.$edtCours->getHeureDebut('H:i').'<br>');
echo('Fin du cours : '.$edtCours->getHeureFin('H:i').'<br><br/>');

$colEdtCours = $newUtilisateurProfessionnel->getEdtEmplacementCourss();
echo ($logger->getDisplay());
if ($colEdtCours->count() != 1) {
	echo('test recuperation emplacement de cours du professeur a <font color="red">echoue</font> <br><br/>');
} else {
	echo('test recuperation emplacement de cours du professeur a reussi<br/><br/>');
}

$colEdtCours = $newUtilisateurProfessionnel->getEdtEmplacementCourssPeriodeCalendrierActuelle();
echo ($logger->getDisplay());
if ($colEdtCours->count() != 1) {
	echo('test recuperation emplacement de cours du professeur a <font color="red">echoue</font> <br><br/>');
} else {
	echo('test recuperation emplacement de cours du professeur a reussi<br/><br/>');
}

$edtCours2 = new EdtEmplacementCours();
$edtCours2->setGroupe($groupe);
$edtCours2->setUtilisateurProfessionnel($newUtilisateurProfessionnel);
$edtCours2->setEdtCreneau($creneau->getNextEdtCreneau());//on prend le creneau precent pour ce cours
$edtCours2->setJourSemaine('lundi');
$edtCours2->setTypeSemaine('');
$edtCours2->setDuree(5);
//$edtCours2->setTypeSemaine('');
$edtCours2->save();
echo ($logger->getDisplay());
echo('emplacement de cours ajouté.<br>');
echo('Debut du cours : '.$edtCours2->getHeureDebut('H:i').'<br>');
echo('Fin du cours : '.$edtCours2->getHeureFin('H:i').'<br><br/>');
echo ($logger->getDisplay());
echo ("<br>");

//on prend une date le lundi matin à 9h40
$now = date('Y-m-d H:i',strtotime("next Monday 9:40"));
$edtCoursTest = $groupe->getEdtEmplacementCours($now);
echo ($logger->getDisplay());
if ($edtCoursTest != null && $edtCoursTest->getIdDefiniePeriode() == $edtCours2->getIdDefiniePeriode()) {
    echo('test recuperation emplacement de cours d\'un groupe a reussi<br/><br/>');
} else {
    echo('test recuperation emplacement de cours d\'un groupe a <font color="red">echoue</font> <br><br/>');
}


$colEdtCours = $newUtilisateurProfessionnel->getEdtEmplacementCourss();
echo ($logger->getDisplay());
if ($colEdtCours->count() != 2) {
	echo('test recuperation emplacement de cours du professeur a <font color="red">echoue</font> <br><br/>');
} else {
	echo('test recuperation emplacement de cours du professeur a reussi<br/><br/>');
}

$colEdtCours = $newUtilisateurProfessionnel->getEdtEmplacementCourssPeriodeCalendrierActuelle();
echo ($logger->getDisplay());
if ($colEdtCours->count() != 2) {
	echo('test recuperation emplacement de cours du professeur a <font color="red">echoue</font> <br><br/>');
} else {
    if ($colEdtCours->getFirst()->getIdDefiniePeriode() == $edtCours2->getIdDefiniePeriode()) {
	    echo('test recuperation emplacement de cours ordonné chronologiquement a <font color="red">echoue</font> <br><br/>');
    } else {
	    echo('test recuperation emplacement de cours du professeur a reussi<br/><br/>');
    }
}

//on prend une date le lundi matin à 8h40
$now = date('Y-m-d H:i',strtotime("next Monday 8:40"));
$edtCoursTest = $newUtilisateurProfessionnel->getEdtEmplacementCours($now);
echo ($logger->getDisplay());
if ($edtCoursTest != null && $edtCoursTest->getIdDefiniePeriode() == $edtCours->getIdDefiniePeriode()) {
    echo('test recuperation emplacement de cours du professeur a reussi<br/><br/>');
} else {
    echo('test recuperation emplacement de cours actuel a <font color="red">echoue</font> <br><br/>');
}

//on prend une date le lundi matin à 9h40
$now = date('Y-m-d H:i',strtotime("next Monday 9:40"));
$edtCoursTest = $newUtilisateurProfessionnel->getEdtEmplacementCours($now);
echo ($logger->getDisplay());
if ($edtCoursTest != null && $edtCoursTest->getIdDefiniePeriode() == $edtCours2->getIdDefiniePeriode()) {
    echo('test recuperation emplacement de cours du professeur a reussi<br/><br/>');
} else {
    echo('test recuperation emplacement de cours actuel a <font color="red">echoue</font> <br><br/>');
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

//Creation d'une classe
$classe = new Classe();
$classe->getClasse();
$classe = UnitTestClasse::getClasse();
$classe->save();
$newClasse = ClassePeer::retrieveByPK($classe->getId());
echo ($logger->getDisplay());
if ($newClasse == null) {
	echo('test creation classe a <font color="red">echoue</font> <br/><br/>');
} else {
	echo('test creation classe a reussi avec comme retour l\'id : ' . $classe->getId() . '<br/><br/>');
}

//ajout d'une periode ouverte et d'un periode fermée à une classe
$periode_fermee = new PeriodeNote();
$periode_fermee->setNumPeriode(1);
$periode_fermee->setVerouiller('O');
$periode_fermee->setNomPeriode('1 Unit test');
$periode_fermee->setDateFin('01/01/1980');
$classe->addPeriodeNote($periode_fermee);
$periode_fermee->save();
echo '<br/>ajout d\'une periode fermee a la classe<br/>';
echo ($logger->getDisplay());
$periode_ouverte = new PeriodeNote();
$periode_ouverte->setNumPeriode(2);
$periode_ouverte->setVerouiller('N');
$periode_ouverte->setNomPeriode('2 Unit test');
$classe->addPeriodeNote($periode_ouverte);
$periode_ouverte->save();
echo '<br/>ajout d\'une periode ouverte a la classe<br/>';
echo ($logger->getDisplay());

echo '<br/>ajout d\'un eleve a la classe pour la periode 1<br/>';
$classe->addEleve($eleve, 1);
echo ($logger->getDisplay());

echo '<br/>ajout d\'un eleve au groupe pour la periode 1<br/>';
$groupe->addEleve($eleve, 1);
$groupe->save();
echo ($logger->getDisplay());

echo("<br/>");
$now = new DateTime('now');
$periode = $eleve->getPeriodeNote($now);
echo ($logger->getDisplay());
if ($periode === null) {
    echo('test 1 recuperation de la periode d\'un eleve a reussi<br/><br/>');
} else {
    echo('test 1 recuperation de la periode d\'un eleve  a <font color="red">echoue</font> <br><br/>');
}

echo("<br/>");
$edtEmplacementCol = $eleve->getEdtEmplacementCourssPeriodeCalendrierActuelle();
echo ($logger->getDisplay());
if ($edtEmplacementCol->count() == 2) {
    echo('test 1 recuperation emplacement de cours d\'un eleve a reussi<br/><br/>');
} else {
    echo('test 1 recuperation emplacement de cours d\'un eleve  a <font color="red">echoue</font> <br><br/>');
}


echo '<br/>ajout d\'un eleve au groupe et a la classe pour la periode 2<br/>';
$classe->addEleve($eleve, 2);
$groupe->addEleve($eleve, 2);
$groupe->save();
echo ($logger->getDisplay());

echo("<br/>");
$now = new DateTime('now');
$periode = $eleve->getPeriodeNote($now);
echo ($logger->getDisplay());
if ($periode != null && $periode->getNomPeriode() == '2 Unit test') {
    echo('test 2 recuperation de la periode d\'un eleve a reussi<br/><br/>');
} else {
    echo('test 2 recuperation de la periode d\'un eleve  a <font color="red">echoue</font> <br><br/>');
}

echo("<br/>");
$edtEmplacementCol = $eleve->getEdtEmplacementCourssPeriodeCalendrierActuelle();
echo ($logger->getDisplay());
if ($edtEmplacementCol->count() == 2) {
    echo('test 2 recuperation emplacement de cours d\'un eleve a reussi<br/><br/>');
} else {
    echo('test 2 recuperation emplacement de cours d\'un eleve  a <font color="red">echoue</font> <br><br/>');
}

//on prend une date le lundi matin à 9h40
$now = date('Y-m-d H:i',strtotime("next Monday 9:40"));
$edtCoursTest = $eleve->getEdtEmplacementCours($now);
echo ($logger->getDisplay());
if ($edtCoursTest != null && $edtCoursTest->getIdDefiniePeriode() == $edtCours2->getIdDefiniePeriode()) {
    echo('test 3 recuperation emplacement de cours d\'un eleve a reussi<br/><br/>');
} else {
    echo('test 3 recuperation emplacement de cours d\'un eleve  a <font color="red">echoue</font> <br><br/>');
}

echo("<br/>");

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
	$logger->getDisplay();

	//purge du groupe
	echo "<br/>Purge du groupe : <br/>";
	$criteria = new Criteria();
	$criteria->add(GroupePeer::NAME, UnitTestGroupe::getGroupe()->getName());
	$groupe = GroupePeer::doSelectOne($criteria);
	if ($groupe != null) {
		$groupe->delete();
	}
	$logger->getDisplay();

	//purge de la classe
	echo "<br/>Purge de la classe :<br/>";
	$criteria = new Criteria();
	$criteria->add(ClassePeer::CLASSE, UnitTestClasse::getClasse()->getClasse());
	$classe = ClassePeer::doSelectOne($criteria);
	if ($classe != null) {
		$classe->delete();
	}
	$logger->getDisplay();

	//purge de l'eleve
	echo "<br/>Purge de l'eleve<br/>";
	$criteria = new Criteria();
	$criteria->add(ElevePeer::LOGIN, UnitTestEleve::getEleve()->getLogin());
	$eleve = ElevePeer::doSelectOne($criteria);
	if ($eleve != null) {
		$eleve->delete();
	}
	echo ($logger->getDisplay());

echo "<br/>Fin Purge des données<br/><br/>";
}

?>
