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
include('UnitTestUtilisateurProfessionnel.php');
include('UnitTestEleve.php');
include('UnitTestAbsenceSaisie.php');
Propel::init('../propel-build/conf/gepi-conf_debug.php');
$logger = new StackLogger();
//Propel::setLogger($logger);


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
	echo('test creation utilisateur professionnel a reussi <br><br/>');
}

//Creation d'un eleve
$eleve = UnitTestEleve::getEleve();
$eleve->save();
$newEleve = ElevePeer::retrieveByPK($eleve->getIdEleve());
echo ($logger->getDisplay());
if ($newEleve == null) {
	echo('test creation eleve a <font color="red">echoue</font> <br><br/>');
} else {
	echo('test creation eleve a reussi <br><br/>');
}

//creation d'une saisie d'absence
$absenceSaisie = UnitTestAbsenceSaise::getAbsenceSaisie();
//$newEleve->addAbsenceEleveSaisie($absenceSaisie);
$newUtilisateurProfessionnel->addAbsenceEleveSaisie($absenceSaisie);
$absenceSaisie->save();
echo ($logger->getDisplay());
echo('saisie absence cree<br/><br/>');
//$absenceSaisie = new AbsenceEleveSaisie();
$eleve_saisie = $absenceSaisie->getEleve();
if ($eleve_saisie == null) {
	echo('test creation de saisie sans eleve a reussie <br><br/>');
} else {
	echo('test creation de saisie sans eleve a <font color="red">echoue</font> <br><br/>');
}
$absenceSaisie->setEleve($newEleve);
$absenceSaisie->save();

//creation d'un traitement d'absence
$absenceTraitement = UnitTestAbsenceSaise::getAbsenceTraitement();
$absenceSaisie->addAbsenceEleveTraitement($absenceTraitement);
$newUtilisateurProfessionnel->addAbsenceEleveTraitement($absenceTraitement);
$absenceTraitement->save();
echo ($logger->getDisplay());
echo('traitement absence cree<br/><br/>');

$absenceSaisies = $newUtilisateurProfessionnel->getAbsenceEleveSaisies();
$absenceSaisie = $absenceSaisies[0];
if ($absenceSaisie == null) {
	echo ($logger->getDisplay());
	echo('test recuperation absence saisie eleve a partir d\'un professeur a <font color="red">echoue</font> <br><br/>');
} else {
	$absenceTraitements = $absenceSaisie->getAbsenceEleveTraitements();
	$absenceTraitement = $absenceTraitements[0];
	if ($absenceTraitement == null) {
		echo ($logger->getDisplay());
		echo('test recuperation absence traitement a partir d\'un professeur a <font color="red">echoue</font> <br><br/>');
	} else {
		echo ($logger->getDisplay());
		echo('test recuperation absence saisie et absence traitement a partir d\'un professeur a reussi <br><br/>');
	}
}

$absenceSaisies = $newEleve->getAbsenceEleveSaisies();
$absenceSaisie = $absenceSaisies[0];
if ($absenceSaisie == null) {
	echo ($logger->getDisplay());
	echo('test 1 recuperation absence saisie eleve a partir d\'un eleve a <font color="red">echoue</font> <br><br/>');
} else {
	echo('test 1 recuperation absence saisie eleve a partir d\'un eleve a reussi <br><br/>');
	$absenceTraitements = $absenceSaisie->getAbsenceEleveTraitements();
	$absenceTraitement = $absenceTraitements[0];
	if ($absenceTraitement == null) {
		echo ($logger->getDisplay());
		echo('test 2 recuperation absence traitement a partir d\'un eleve a <font color="red">echoue</font> <br><br/>');
	} else {
		echo ($logger->getDisplay());
		echo('test 2 recuperation absence saisie et absence traitement a partir d\'un eleve a reussi <br><br/>');
	}
}

$absenceSaisie2 = UnitTestAbsenceSaise::getAbsenceSaisie();
$absenceSaisie2->setDebutAbs(new DateTime('2010-01-01 13:00'));
$absenceSaisie2->setFinAbs(new DateTime('2010-01-01 13:01'));
$absenceSaisie2->setEleve($newEleve);
$absenceSaisie2->save();//normalement cette saisie va ètre comptée en retard et non en manquementObligationPresence

$absenceSaisies = $newEleve->getAbsenceEleveSaisiesManquementObligationPresence();
if ($absenceSaisies->count() != 2) {
	echo ($logger->getDisplay());
	echo('test 1 recuperation absence saisie eleve manquement obligation a partir d\'un eleve a <font color="red">echoue</font> <br><br/>');
} else {
	echo('test 1 recuperation absence saisie eleve manquement obligation a partir d\'un eleve a reussi<br><br/>');
}


purgeDonneesTest($logger);
Propel::setLogger(null);

function purgeDonneesTest($logger) {
	echo "Purge des données<br/><br/>";
	//purge de l'utilisateur
	echo "<br/>Purge de l'utilisateur : <br/>";
	$utilisateurProfessionnel = UtilisateurProfessionnelPeer::retrieveByPK(UnitTestUtilisateurProfessionnel::getUtilisateurProfessionnel()->getLogin());
	if ($utilisateurProfessionnel != null)	{
		foreach ($utilisateurProfessionnel->getAbsenceEleveTraitements() as $traitement) {
		    $traitement->delete();
		}
		$utilisateurProfessionnel->delete();
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

	echo "<br/>Fin Purge des données<br/><br/>";
}

?>
