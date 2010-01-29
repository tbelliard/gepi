<?php

/**
 * Fichier voir_edt.php pour visionner les différents EdT (classes ou professeurs)
 *
 * @version $Id$
 *
 * Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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

// Définir dés le début le type d'EdT qu'on veut voir (prof, classe, salle)

//===========================
// AJOUT: boireaus
$visioedt=isset($_GET['visioedt']) ? $_GET['visioedt'] : (isset($_POST['visioedt']) ? $_POST['visioedt'] : NULL);
$login_edt=isset($_GET['login_edt']) ? $_GET['login_edt'] : (isset($_POST['login_edt']) ? $_POST['login_edt'] : NULL);
$classe=isset($_GET['classe']) ? $_GET['classe'] : (isset($_POST['classe']) ? $_POST['classe'] : NULL);
$salle=isset($_GET['salle']) ? $_GET['salle'] : (isset($_POST['salle']) ? $_POST['salle'] : NULL);
$supprimer_cours = isset($_GET["supprimer_cours"]) ? $_GET["supprimer_cours"] : NULL;
$identite = isset($_GET["identite"]) ? $_GET["identite"] : NULL;
$message = isset($_SESSION["message"]) ? $_SESSION["message"] : "";
$type_edt_2 = isset($_GET["type_edt_2"]) ? $_GET["type_edt_2"] : (isset($_POST["type_edt_2"]) ? $_POST["type_edt_2"] : NULL);
$period_id=isset($_GET['period_id']) ? $_GET['period_id'] : (isset($_POST['period_id']) ? $_POST['period_id'] : NULL);
$bascule_edt=isset($_GET['bascule_edt']) ? $_GET['bascule_edt'] : (isset($_POST['bascule_edt']) ? $_POST['bascule_edt'] : NULL);
//===========================

if ($visioedt == 'prof1') $type_edt = $login_edt;
elseif ($visioedt == 'classe1') $type_edt = $classe;
elseif ($visioedt == 'salle1') $type_edt = $salle;

// ========================= AFFICHAGE DES MESSAGES

if ($message != "") {
    echo ("<div class=\"cadreInformation\">".$message."</div>");
    $_SESSION["message"] = "";
}
if ($bascule_edt != NULL) {
    $_SESSION['bascule_edt'] = $bascule_edt;
}
if (!isset($_SESSION['bascule_edt'])) {
    $_SESSION['bascule_edt'] = 'periode';
}
if ($_SESSION['bascule_edt'] == 'periode') {
    //=========================== GESTION DES PERIODES
    if (PeriodesExistent()) {
        if ($period_id != NULL) {
            $_SESSION['period_id'] = $period_id;
        }
        if (!isset($_SESSION['period_id'])) {
            $_SESSION['period_id'] = ReturnIdPeriod(date("U"));
        }
        if (!PeriodExistsInDB($_SESSION['period_id'])) {
            $_SESSION['period_id'] = ReturnFirstIdPeriod();    
        }
        AfficheBarCommutateurPeriodes($login_edt, $visioedt, $type_edt_2);
    }
    else {
        $_SESSION['period_id'] = 0;
    }
}

//=========================== AFFICHAGE DES MENUS DEROULANTS DE SELECTION

$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
    echo ("<div class=\"fenetre\">\n");
    echo("<div class=\"contenu\">
		<div class=\"coingh\"></div>
        <div class=\"coindh\"></div>
        <div class=\"partiecentralehaut\"></div>
        <div class=\"droite\"></div>
        <div class=\"gauche\"></div>
        <div class=\"coingb\"></div>
		<div class=\"coindb\"></div>
		<div class=\"partiecentralebas\"></div>\n");
}        
echo "<span class=\"legende\">".TITLE_VOIR_EDT."</span>\n";

if (isset($visioedt) AND $visioedt == "prof1") {
	require_once("./voir_edt_prof.php");
}

elseif (isset($visioedt) AND $visioedt == "salle1") {
	require_once("./voir_edt_salle.php");
}

elseif (isset($visioedt) AND $visioedt == "classe1") {
	require_once("./voir_edt_classe.php");
}

if ((isset($visioedt)) AND isset($login_edt) AND $visioedt == "prof1") {
	$aff_nom_edt = renvoie_nom_long(($login_edt), "prof");
}
elseif ((isset($visioedt)) AND isset($login_edt) AND $visioedt == "salle1") {
	$aff_nom_edt = renvoie_nom_long(($login_edt), "salle");
}
elseif ((isset($visioedt)) AND isset($login_edt) AND $visioedt == "classe1") {
	$aff_nom_edt = renvoie_nom_long(($login_edt), "classe");
}

$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
echo "</div>";
echo "</div>";
}
// =============================================================================
//
//                Affichage des emplois du temps (version 2)
//		
//                  pour les edt élèves, voir les fichiers voir_edt_eleve.php et 
//                  edt_eleve.php
// =============================================================================

if(isset($aff_nom_edt)){

    $type_edt = isset($_GET["type_edt_2"]) ? $_GET["type_edt_2"] : (isset($_POST["type_edt_2"]) ? $_POST["type_edt_2"] : NULL);
    if ($type_edt == "prof")
    {
        $tab_data = ConstruireEDTProf($type_edt, $login_edt, $_SESSION['period_id']);
        $entetes = ConstruireEnteteEDT();
        $creneaux = ConstruireCreneauxEDT();
        AfficheImprimante(true);
        //AfficheBascule(true, $login_edt, $visioedt, $type_edt_2);
        AfficherEDT($tab_data, $entetes, $creneaux, $type_edt, $login_edt, $_SESSION['period_id']);
    }
    else if ($type_edt == "classe")
    {
        $tab_data = ConstruireEDTClasse($type_edt, $login_edt, $_SESSION['period_id']);
        $entetes = ConstruireEnteteEDT();
        $creneaux = ConstruireCreneauxEDT();
        AfficheImprimante(true);
        //AfficheBascule(true, $login_edt, $visioedt, $type_edt_2);
        AfficherEDT($tab_data, $entetes, $creneaux, $type_edt, $login_edt, $_SESSION['period_id']);

    }
    else if ($type_edt == "salle")
    {
        $tab_data = ConstruireEDTSalle($type_edt, $login_edt , $_SESSION['period_id']);
        $entetes = ConstruireEnteteEDT();
        $creneaux = ConstruireCreneauxEDT();
        AfficheImprimante(true);
        //AfficheBascule(true, $login_edt, $visioedt, $type_edt_2);
        AfficherEDT($tab_data, $entetes, $creneaux, $type_edt, $login_edt, $_SESSION['period_id']);

    }

}
?>