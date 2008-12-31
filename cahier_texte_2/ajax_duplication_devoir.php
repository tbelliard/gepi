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
if (isset($_GET['traite_anti_inject']) OR isset($_POST['traite_anti_inject'])) $traite_anti_inject = "yes";

// Dans le cas ou on poste une notice ou un devoir, pas de traitement anti_inject
// Pour ne pas interférer avec fckeditor
$traite_anti_inject = 'no';

require_once("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");

$utilisateur = $_SESSION['utilisateur'];
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

//récupération des paramètres de la requète
$id_ct = isset($_POST["id_ct"]) ? $_POST["id_ct"] :(isset($_GET["id_ct"]) ? $_GET["id_ct"] :NULL);
$date_duplication = isset($_POST["date_duplication"]) ? $_POST["date_duplication"] :(isset($_GET["date_duplication"]) ? $_GET["date_duplication"] :NULL);
$id_groupe = isset($_POST["id_groupe"]) ? $_POST["id_groupe"] :(isset($_GET["id_groupe"]) ? $_GET["id_groupe"] :NULL);

$ctTravailAFaire = CtTravailAFairePeer::retrieveByPK($id_ct);
if ($ctTravailAFaire == null) {
	echo ("Erreur : pas de devoir trouv�.");
	die();
}
$groupe = GroupePeer::retrieveByPK($id_groupe);
if ($groupe == null) {
	echo("Pas de groupe sp�cifi�");
	die;
}

$nouveauCtTravailAFaire = new CtTravailAFaire();
$deepcopy = 1;
$nouveauCtTravailAFaire = $ctTravailAFaire->copy($deepcopy);
$nouveauCtTravailAFaire->setGroupe($groupe);
$nouveauCtTravailAFaire->setDateCt($date_duplication);

$nouveauCtTravailAFaire->save();
$utilisateur->clearAllReferences();
echo("Duplication effectu�e");
?>