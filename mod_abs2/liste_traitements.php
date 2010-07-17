<?php
/**
 *
 * @version $Id$
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Eric Lebrun, Stephane Boireau, Julien Jocal
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

// Initialisation des feuilles de style après modification pour améliorer l'accessibilité
$accessibilite="y";

// Initialisations files
require_once("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");
// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

//recherche de l'utilisateur avec propel
$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

//On vérifie si le module est activé
if (getSettingValue("active_module_absence")!='2') {
    die("Le module n'est pas activé.");
}

if ($utilisateur->getStatut()!="cpe" && $utilisateur->getStatut()!="scolarite") {
    die("acces interdit");
}

//récupération des paramètres de la requète
$order = isset($_POST["order"]) ? $_POST["order"] :(isset($_GET["order"]) ? $_GET["order"] :(isset($_SESSION["order"]) ? $_SESSION["order"] : NULL));

$filter_id = isset($_POST["filter_id"]) ? $_POST["filter_id"] :(isset($_GET["filter_id"]) ? $_GET["filter_id"] :(isset($_SESSION["filter_id"]) ? $_SESSION["filter_id"] : NULL));
$filter_utilisateur = isset($_POST["filter_utilisateur"]) ? $_POST["filter_utilisateur"] :(isset($_GET["filter_utilisateur"]) ? $_GET["filter_utilisateur"] :(isset($_SESSION["filter_utilisateur"]) ? $_SESSION["filter_utilisateur"] : NULL));
$filter_eleve = isset($_POST["filter_eleve"]) ? $_POST["filter_eleve"] :(isset($_GET["filter_eleve"]) ? $_GET["filter_eleve"] :(isset($_SESSION["filter_eleve"]) ? $_SESSION["filter_eleve"] : NULL));
$filter_classe = isset($_POST["filter_classe"]) ? $_POST["filter_classe"] :(isset($_GET["filter_classe"]) ? $_GET["filter_classe"] :(isset($_SESSION["filter_classe"]) ? $_SESSION["filter_classe"] : NULL));
$filter_groupe = isset($_POST["filter_groupe"]) ? $_POST["filter_groupe"] :(isset($_GET["filter_groupe"]) ? $_GET["filter_groupe"] :(isset($_SESSION["filter_groupe"]) ? $_SESSION["filter_groupe"] : NULL));
$filter_aid = isset($_POST["filter_aid"]) ? $_POST["filter_aid"] :(isset($_GET["filter_aid"]) ? $_GET["filter_aid"] :(isset($_SESSION["filter_aid"]) ? $_SESSION["filter_aid"] : NULL));
$filter_type = isset($_POST["filter_type"]) ? $_POST["filter_type"] :(isset($_GET["filter_type"]) ? $_GET["filter_type"] :(isset($_SESSION["filter_type"]) ? $_SESSION["filter_type"] : NULL));
$filter_justification = isset($_POST["filter_justification"]) ? $_POST["filter_justification"] :(isset($_GET["filter_justification"]) ? $_GET["filter_justification"] :(isset($_SESSION["filter_justification"]) ? $_SESSION["filter_justification"] : NULL));
$filter_date_debut_absence_fin_plage = isset($_POST["filter_date_debut_absence_fin_plage"]) ? $_POST["filter_date_debut_absence_fin_plage"] :(isset($_GET["filter_date_debut_absence_fin_plage"]) ? $_GET["filter_date_debut_absence_fin_plage"] :(isset($_SESSION["filter_date_debut_absence_fin_plage"]) ? $_SESSION["filter_date_debut_absence_fin_plage"] : NULL));
$filter_date_fin_absence_debut_plage = isset($_POST["filter_date_fin_absence_debut_plage"]) ? $_POST["filter_date_fin_absence_debut_plage"] :(isset($_GET["filter_date_fin_absence_debut_plage"]) ? $_GET["filter_date_fin_absence_debut_plage"] :(isset($_SESSION["filter_date_fin_absence_debut_plage"]) ? $_SESSION["filter_date_fin_absence_debut_plage"] : NULL));
$filter_date_fin_absence_fin_plage = isset($_POST["filter_date_fin_absence_fin_plage"]) ? $_POST["filter_date_fin_absence_fin_plage"] :(isset($_GET["filter_date_fin_absence_fin_plage"]) ? $_GET["filter_date_fin_absence_fin_plage"] :(isset($_SESSION["filter_date_fin_absence_fin_plage"]) ? $_SESSION["filter_date_fin_absence_fin_plage"] : NULL));
$filter_creneau = isset($_POST["filter_creneau"]) ? $_POST["filter_creneau"] :(isset($_GET["filter_creneau"]) ? $_GET["filter_creneau"] :(isset($_SESSION["filter_creneau"]) ? $_SESSION["filter_creneau"] : NULL));
$filter_cours = isset($_POST["filter_cours"]) ? $_POST["filter_cours"] :(isset($_GET["filter_cours"]) ? $_GET["filter_cours"] :(isset($_SESSION["filter_cours"]) ? $_SESSION["filter_cours"] : NULL));
$filter_date_creation_traitement_debut_plage = isset($_POST["filter_date_creation_traitement_debut_plage"]) ? $_POST["filter_date_creation_traitement_debut_plage"] :(isset($_GET["filter_date_creation_traitement_debut_plage"]) ? $_GET["filter_date_creation_traitement_debut_plage"] :(isset($_SESSION["filter_date_creation_traitement_debut_plage"]) ? $_SESSION["filter_date_creation_traitement_debut_plage"] : NULL));
$filter_date_creation_traitement_fin_plage = isset($_POST["filter_date_creation_traitement_fin_plage"]) ? $_POST["filter_date_creation_traitement_fin_plage"] :(isset($_GET["filter_date_creation_traitement_fin_plage"]) ? $_GET["filter_date_creation_traitement_fin_plage"] :(isset($_SESSION["filter_date_creation_traitement_fin_plage"]) ? $_SESSION["filter_date_creation_traitement_fin_plage"] : NULL));
if (isset($_POST["filter_date_modification"])) {
    $filter_date_modification = $_POST["filter_date_modification"];
} elseif (isset($_GET["filter_date_modification"])) {
    $filter_date_modification = $_GET["filter_date_modification"];
} elseif (isset($_POST["filter_id"]) || isset($_GET["filter_id"])) {
    $filter_date_modification = '';
} elseif (isset($_SESSION["filter_date_modification"])) {
    $filter_date_modification = $_SESSION["filter_date_modification"];
} else {
    $filter_date_modification = null;
}
$filter_date_traitement_absence_debut_plage = isset($_POST["filter_date_traitement_absence_debut_plage"]) ? $_POST["filter_date_traitement_absence_debut_plage"] :(isset($_GET["filter_date_traitement_absence_debut_plage"]) ? $_GET["filter_date_traitement_absence_debut_plage"] :(isset($_SESSION["filter_date_traitement_absence_debut_plage"]) ? $_SESSION["filter_date_traitement_absence_debut_plage"] : NULL));
$filter_date_traitement_absence_fin_plage = isset($_POST["filter_date_traitement_absence_fin_plage"]) ? $_POST["filter_date_traitement_absence_fin_plage"] :(isset($_GET["filter_date_traitement_absence_fin_plage"]) ? $_GET["filter_date_traitement_absence_fin_plage"] :(isset($_SESSION["filter_date_traitement_absence_fin_plage"]) ? $_SESSION["filter_date_traitement_absence_fin_plage"] : NULL));
if (isset($_POST["filter_discipline"])) {
    $filter_discipline = $_POST["filter_discipline"];
} elseif (isset($_GET["filter_discipline"])) {
    $filter_discipline = $_GET["filter_discipline"];
} elseif (isset($_POST["filter_id"]) || isset($_GET["filter_id"])) {
    $filter_discipline = '';
} elseif (isset($_SESSION["filter_discipline"])) {
    $filter_discipline = $_SESSION["filter_discipline"];
} else {
    $filter_discipline = null;
}

$reinit_filtre = isset($_POST["reinit_filtre"]) ? $_POST["reinit_filtre"] :(isset($_GET["reinit_filtre"]) ? $_GET["reinit_filtre"] :NULL);
if ($reinit_filtre == 'y') {
    $filter_id = NULL;
    $filter_utilisateur = NULL;
    $filter_eleve = NULL;
    $filter_classe = NULL;
    $filter_groupe = NULL;
    $filter_aid = NULL;
    $filter_type = NULL;
    $filter_justification = NULL;
    $filter_date_debut_absence_fin_plage = NULL;
    $filter_date_fin_absence_debut_plage = NULL;
    $filter_date_fin_absence_fin_plage = NULL;
    $filter_creneau = NULL;
    $filter_cours = NULL;
    $filter_date_creation_traitement_debut_plage = NULL;
    $filter_date_creation_traitement_fin_plage = NULL;
    $filter_date_modification = NULL;
    $filter_date_traitement_absence_debut_plage = NULL;
    $filter_date_traitement_absence_fin_plage = NULL;
    $filter_discipline = NULL;

    $order = NULL;
}

if ($order == null) {
    $order = 'des_id';
}
//on va mettre en session tout les parametres de la requete, pour la navigation par onglet
if (isset($order) && $order != null) $_SESSION['order'] = $order;

if (isset($filter_id) && $filter_id != null) $_SESSION['filter_id'] = $filter_id;
if (isset($filter_eleve) && $filter_eleve != null) $_SESSION['filter_eleve'] = $filter_eleve;
if (isset($filter_classe) && $filter_classe != null) $_SESSION['filter_classe'] = $filter_classe;
if (isset($filter_groupe) && $filter_groupe != null) $_SESSION['filter_groupe'] = $filter_groupe;
if (isset($filter_aid) && $filter_aid != null) $_SESSION['filter_aid'] = $filter_aid;
if (isset($filter_type) && $filter_type != null) $_SESSION['filter_type'] = $filter_type;
if (isset($filter_justification) && $filter_justification != null) $_SESSION['filter_justification'] = $filter_justification;
if (isset($filter_date_debut_absence_fin_plage) && $filter_date_debut_absence_fin_plage != null) $_SESSION['filter_date_debut_absence_fin_plage'] = $filter_date_debut_absence_fin_plage;
if (isset($filter_date_fin_absence_debut_plage) && $filter_date_fin_absence_debut_plage != null) $_SESSION['filter_date_fin_absence_debut_plage'] = $filter_date_fin_absence_debut_plage;
if (isset($filter_date_fin_absence_fin_plage) && $filter_date_fin_absence_fin_plage != null) $_SESSION['filter_date_fin_absence_fin_plage'] = $filter_date_fin_absence_fin_plage;
if (isset($filter_creneau) && $filter_creneau != null) $_SESSION['filter_creneau'] = $filter_creneau;
if (isset($filter_cours) && $filter_cours != null) $_SESSION['filter_cours'] = $filter_cours;
if (isset($filter_date_creation_traitement_debut_plage) && $filter_date_creation_traitement_debut_plage != null) $_SESSION['filter_date_creation_traitement_debut_plage'] = $filter_date_creation_traitement_debut_plage;
if (isset($filter_date_creation_traitement_fin_plage) && $filter_date_creation_traitement_fin_plage != null) $_SESSION['filter_date_creation_traitement_fin_plage'] = $filter_date_creation_traitement_fin_plage;
if (isset($filter_date_modification) && $filter_date_modification != null) $_SESSION['filter_date_modification'] = $filter_date_modification;
if (isset($filter_date_traitement_absence_debut_plage) && $filter_date_traitement_absence_debut_plage != null) $_SESSION['filter_date_traitement_absence_debut_plage'] = $filter_date_traitement_absence_debut_plage;
if (isset($filter_date_traitement_absence_fin_plage) && $filter_date_traitement_absence_fin_plage != null) $_SESSION['filter_date_traitement_absence_fin_plage'] = $filter_date_traitement_absence_fin_plage;
if (isset($filter_discipline) && $filter_discipline != null) $_SESSION['filter_discipline'] = $filter_discipline;

$page_number = isset($_POST["page_number"]) ? $_POST["page_number"] :(isset($_GET["page_number"]) ? $_GET["page_number"] :(isset($_SESSION["page_number"]) ? $_SESSION["page_number"] : NULL));
if (!is_numeric($page_number) || $reinit_filtre == 'y') {
    $page_number = 1;
}

$page_deplacement = isset($_POST["page_deplacement"]) ? $_POST["page_deplacement"] :(isset($_GET["page_deplacement"]) ? $_GET["page_deplacement"] :NULL);
if ($page_deplacement == "+") {
    $page_number = $page_number + 1;
} else if ($page_deplacement == "-") {
    $page_number = $page_number - 1;
}
if ($page_number < 1) {
    $page_number = 1;
}
if (isset($page_number) && $page_number != null) $_SESSION['page_number'] = $page_number;
//if (isset($page_deplacement) && $page_deplacement != null) $_SESSION['page_deplacement'] = $page_deplacement;

$item_per_page = isset($_POST["item_per_page"]) ? $_POST["item_per_page"] :(isset($_GET["item_per_page"]) ? $_GET["item_per_page"] :(isset($_SESSION["item_per_page"]) ? $_SESSION["item_per_page"] : NULL));
if (!is_numeric($item_per_page)) {
    $item_per_page = 14;
}
if ($item_per_page < 1) {
    $item_per_page = 1;
}
if (isset($item_per_page) && $item_per_page != null) $_SESSION['item_per_page'] = $item_per_page;

//==============================================
$style_specifique[] = "mod_abs2/lib/abs_style";
$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";
$titre_page = "Les absences";
$utilisation_jsdivdrag = "non";
$_SESSION['cacher_header'] = "y";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

//===========================

echo "<div id='aidmenu' style='display: none;'>test</div>\n";

include('menu_abs2.inc.php');
//===========================
echo "<div class='css-panes' id='containDiv' style='overflow : none; float : left; margin-top : -1px; border-width : 1px;'>\n";


$query = AbsenceEleveTraitementQuery::create();
if ($filter_id != null && $filter_id != '') {
    $query->filterById($filter_id);
}
if ($filter_utilisateur != null && $filter_utilisateur != '') {
    $query->useUtilisateurProfessionnelQuery()->filterByNom('%'.$filter_utilisateur.'%', Criteria::LIKE)->endUse();
}
if ($filter_eleve != null && $filter_eleve != '') {
    $query->useJTraitementSaisieEleveQuery()->useAbsenceEleveSaisieQuery()->useEleveQuery()
	    ->filterByNomOrPrenomLike($filter_eleve)
	    ->endUse()->endUse()->endUse();
}
if ($filter_classe != null && $filter_classe != '-1') {
    $query->useJTraitementSaisieEleveQuery()->endUse();
    $query->leftJoin('JTraitementSaisieEleve.AbsenceEleveSaisie');
    $query->leftJoin('AbsenceEleveSaisie.Eleve');
    $query->leftJoin('Eleve.JEleveClasse');
    $query->condition('cond1', 'JEleveClasse.IdClasse = ?', $filter_classe);
    $query->condition('cond2', 'AbsenceEleveSaisie.IdClasse = ?', $filter_classe);
    $query->where(array('cond1', 'cond2'), 'or');
}
if ($filter_groupe != null && $filter_groupe != '-1') {
    $query->useJTraitementSaisieEleveQuery()->endUse();
    $query->leftJoin('JTraitementSaisieEleve.AbsenceEleveSaisie');
    $query->leftJoin('AbsenceEleveSaisie.Eleve');
    $query->leftJoin('Eleve.JEleveGroupe');
    $query->condition('cond1', 'JEleveGroupe.IdGroupe = ?', $filter_groupe);
    $query->condition('cond2', 'AbsenceEleveSaisie.IdGroupe = ?', $filter_groupe);
    $query->where(array('cond1', 'cond2'), 'or');
}
if ($filter_aid != null && $filter_aid != '-1') {
    $query->useJTraitementSaisieEleveQuery()->endUse();
    $query->leftJoin('JTraitementSaisieEleve.AbsenceEleveSaisie');
    $query->leftJoin('AbsenceEleveSaisie.Eleve');
    $query->leftJoin('Eleve.JAidEleves');
    $query->condition('cond1', 'JAidEleves.IdAid = ?', $filter_aid);
    $query->condition('cond2', 'AbsenceEleveSaisie.IdAid = ?', $filter_aid);
    $query->where(array('cond1', 'cond2'), 'or');
}
if ($filter_type != null && $filter_type != '-1') {
    $query->filterByATypeId($filter_type);
}
if ($filter_justification != null && $filter_justification != '-1') {
    $query->filterByAJustificationId($filter_justification);
}
if ($filter_date_creation_traitement_debut_plage != null && $filter_date_creation_traitement_debut_plage != '-1') {
    $date_creation_traitement_debut_plage = new DateTime(str_replace("/",".",$filter_date_creation_traitement_debut_plage));
    $query->filterByCreatedAt($date_creation_traitement_debut_plage, Criteria::GREATER_EQUAL);
}
if ($filter_date_creation_traitement_fin_plage != null && $filter_date_creation_traitement_fin_plage != '-1') {
    $date_creation_traitement_fin_plage = new DateTime(str_replace("/",".",$filter_date_creation_traitement_fin_plage));
    $query->filterByCreatedAt($date_creation_traitement_fin_plage, Criteria::LESS_EQUAL);
}
if ($filter_date_modification != null && $filter_date_modification == 'y') {
    $query->where('AbsenceEleveTraitement.CreatedAt != AbsenceEleveTraitement.UpdatedAt');
}

if ($order == "asc_id") {
    $query->orderBy('Id', Criteria::ASC);
} else if ($order == "des_id") {
    $query->orderBy('Id', Criteria::DESC);
} else if ($order == "asc_utilisateur") {
    $query->useUtilisateurProfessionnelQuery()->orderBy('Nom', Criteria::ASC)->endUse();
} else if ($order == "des_utilisateur") {
    $query->useUtilisateurProfessionnelQuery()->orderBy('Nom', Criteria::DESC)->endUse();
} else if ($order == "asc_eleve") {
    $query->useJTraitementSaisieEleveQuery()->useAbsenceEleveSaisieQuery()->useEleveQuery()->orderBy('Nom', Criteria::ASC)->endUse()->endUse()->endUse();
} else if ($order == "des_eleve") {
    $query->useJTraitementSaisieEleveQuery()->useAbsenceEleveSaisieQuery()->useEleveQuery()->orderBy('Nom', Criteria::DESC)->endUse()->endUse()->endUse();
} else if ($order == "asc_classe") {
    $query->useJTraitementSaisieEleveQuery()->useAbsenceEleveSaisieQuery()->useClasseQuery()->orderBy('NomComplet', Criteria::ASC)->endUse()->endUse()->endUse();
} else if ($order == "des_classe") {
    $query->useJTraitementSaisieEleveQuery()->useAbsenceEleveSaisieQuery()->useClasseQuery()->orderBy('NomComplet', Criteria::DESC)->endUse()->endUse()->endUse();
} else if ($order == "asc_groupe") {
    $query->useJTraitementSaisieEleveQuery()->useAbsenceEleveSaisieQuery()->useGroupeQuery()->orderBy('Name', Criteria::ASC)->endUse()->endUse()->endUse();
} else if ($order == "des_groupe") {
    $query->useJTraitementSaisieEleveQuery()->useAbsenceEleveSaisieQuery()->useGroupeQuery()->orderBy('Name', Criteria::DESC)->endUse()->endUse()->endUse();
} else if ($order == "asc_aid") {
    $query->useAidDetailsQuery()->orderBy('Nom', Criteria::ASC)->endUse();
} else if ($order == "des_aid") {
    $query->useAidDetailsQuery()->orderBy('Nom', Criteria::DESC)->endUse();
} else if ($order == "asc_type") {
    $query->orderBy('ATypeId', Criteria::ASC);
} else if ($order == "des_type") {
    $query->orderBy('ATypeId', Criteria::DESC);
} else if ($order == "asc_justification") {
    $query->orderBy('AJustificationId', Criteria::ASC);
} else if ($order == "des_justification") {
    $query->orderBy('AJustificationId', Criteria::DESC);
} else if ($order == "asc_date_debut") {
    $query->orderBy('DebutAbs', Criteria::ASC);
} else if ($order == "des_date_debut") {
    $query->orderBy('DebutAbs', Criteria::DESC);
} else if ($order == "asc_date_fin") {
    $query->orderBy('FinAbs', Criteria::ASC);
} else if ($order == "des_date_fin") {
    $query->orderBy('FinAbs', Criteria::DESC);
} else if ($order == "asc_creneau") {
    $query->useEdtCreneauQuery()->orderBy('HeuredebutDefiniePeriode', Criteria::ASC)->endUse();
} else if ($order == "des_creneau") {
    $query->useEdtCreneauQuery()->orderBy('HeuredebutDefiniePeriode', Criteria::DESC)->endUse();
} else if ($order == "asc_date_creation") {
    $query->orderBy('CreatedAt', Criteria::ASC);
} else if ($order == "des_date_creation") {
    $query->orderBy('CreatedAt', Criteria::DESC);
} else if ($order == "asc_date_modification") {
    $query->orderBy('UpdatedAt', Criteria::ASC);
} else if ($order == "des_date_modification") {
    $query->orderBy('UpdatedAt', Criteria::DESC);
} else if ($order == "des_date_modification") {
    $query->orderBy('UpdatedAt', Criteria::DESC);
}

$query->distinct();
$traitements_col = $query->paginate($page_number, $item_per_page);

$nb_pages = (floor($traitements_col->getNbResults() / $item_per_page) + 1);
if ($page_number > $nb_pages) {
    $page_number = $nb_pages;
}

echo '<form method="post" action="liste_traitements.php" id="liste_traitements">';

if ($traitements_col->haveToPaginate()) {
    echo "Page ";
    echo '<input type="submit" name="page_deplacement" value="-"/>';
    echo '<input type="text" name="page_number" size="1" value="'.$page_number.'"/>';
    echo '<input type="submit" name="page_deplacement" value="+"/> ';
    echo "sur ".$nb_pages." page(s) ";
    echo "| ";
}
echo "Voir ";
echo '<input type="text" name="item_per_page" size="1" value="'.$item_per_page.'"/>';
echo "par page|  Nombre d'enregistrements : ";
echo $traitements_col->count();

echo "&nbsp;&nbsp;&nbsp;";
echo '<button type="submit" name="reinit_filtre" value="y"/>Reinitialiser les filtres</button> ';
echo '<button type="submit">Rechercher</button>';

echo '<table id="table_liste_absents" class="tb_absences" style="border-spacing:0;">';

echo '<THEAD>';
echo '<TR>';

//en tete filtre id
echo '<TH>';
echo '<nobr>';
echo 'N°';
echo '<input type="image" src="../images/up.png" width="15" height="15" title="monter" style="vertical-align: middle;';
if ($order == "asc_id") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_id"/>';
echo '<input type="image" src="../images/down.png" width="15" height="15" title="monter" style="vertical-align: middle;';
if ($order == "des_id") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_id"/>';
echo '</nobr> ';
echo '<input type="text" name="filter_id" value="'.$filter_id.'" size="3"/>';
echo '</TH>';

//en tete filtre utilisateur
echo '<TH>';
echo '<nobr>';
echo 'Utilisateur';
echo '<input type="image" src="../images/up.png" width="15" height="15" title="monter" style="vertical-align: middle;';
if ($order == "asc_utilisateur") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_utilisateur"/>';
echo '<input type="image" src="../images/down.png" width="15" height="15" title="monter" style="vertical-align: middle;';
if ($order == "des_utilisateur") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_utilisateur"/>';
echo '</nobr>';
echo '<br><input type="text" name="filter_utilisateur" value="'.$filter_utilisateur.'" size="12"/>';
echo '</TH>';

//en tete filtre eleve
echo '<TH>';
echo '<nobr>';
echo 'Eleve';
echo '<input type="image" src="../images/up.png" width="15" height="15" title="monter" style="vertical-align: middle;';
if ($order == "asc_eleve") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_eleve"/>';
echo '<input type="image" src="../images/down.png" width="15" height="15" title="monter" style="vertical-align: middle;';
if ($order == "des_eleve") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_eleve"/>';
echo '</nobr>';
echo '<br><input type="text" name="filter_eleve" value="'.$filter_eleve.'" size="8"/>';
echo '</TH>';

//en tete filtre saisies
echo '<TH>';
echo '<nobr>';
echo 'Saisies';
echo '<input type="image" src="../images/up.png" width="15" height="15" title="monter" style="vertical-align: middle;';
if ($order == "asc_eleve") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_eleve"/>';
echo '<input type="image" src="../images/down.png" width="15" height="15" title="monter" style="vertical-align: middle;';
if ($order == "des_eleve") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_eleve"/>';
echo '</nobr>';
echo '</TH>';

//en tete filtre classe
echo '<TH>';
echo '<nobr>';
echo 'Classe';
echo '<input type="image" src="../images/up.png" width="15" height="15" title="monter" style="vertical-align: middle;';
if ($order == "asc_classe") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_classe"/>';
echo '<input type="image" src="../images/down.png" width="15" height="15" title="monter" style="vertical-align: middle;';
if ($order == "des_classe") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_classe"/>';
echo '</nobr>';
echo '<br>';
echo ("<select name=\"filter_classe\" onchange='submit()'>");
echo "<option value='-1'></option>\n";
foreach (ClasseQuery::create()->distinct()->find() as $classe) {
	echo "<option value='".$classe->getId()."'";
	if ($filter_classe == $classe->getId()) echo " SELECTED ";
	echo ">";
	echo $classe->getNomComplet();
	echo "</option>\n";
}
echo "</select>";
echo '</TH>';

//en tete type d'absence
echo '<TH>';
echo '<nobr>';
echo 'type';
echo '<input type="image" src="../images/up.png" width="15" height="15" title="monter" style="vertical-align: middle;';
if ($order == "asc_type") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_type"/>';
echo '<input type="image" src="../images/down.png" width="15" height="15" title="monter" style="vertical-align: middle;';
if ($order == "des_type") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_type"/>';
echo '</nobr>';
echo '<br>';
echo ("<select name=\"filter_type\" onchange='submit()'>");
echo "<option value='-1'></option>\n";
foreach (AbsenceEleveTypeQuery::create()->find() as $type) {
	echo "<option value='".$type->getId()."'";
	if ($filter_type == $type->getId()) echo " SELECTED ";
	echo ">";
	echo $type->getNom();
	echo "</option>\n";
}
echo "</select>";
echo '</TH>';

//en tete justification d'absence
echo '<TH>';
echo '<nobr>';
echo 'justification';
echo '<input type="image" src="../images/up.png" width="15" height="15" title="monter" style="vertical-align: middle;';
if ($order == "asc_justification") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_justification"/>';
echo '<input type="image" src="../images/down.png" width="15" height="15" title="monter" style="vertical-align: middle;';
if ($order == "des_justification") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_justification"/>';
echo '</nobr>';
echo '<br>';
echo ("<select name=\"filter_justification\" onchange='submit()'>");
echo "<option value='-1'></option>\n";
foreach (AbsenceEleveJustificationQuery::create()->find() as $justification) {
	echo "<option value='".$justification->getId()."'";
	if ($filter_justification == $justification->getId()) echo " SELECTED ";
	echo ">";
	echo $justification->getNom();
	echo "</option>\n";
}
echo "</select>";
echo '</TH>';

//en tete notification d'absence
echo '<TH>';
echo '<nobr>';
echo '&nbsp;notification&nbsp;';
echo '<input type="image" src="../images/up.png" width="15" height="15" title="monter" style="vertical-align: middle;';
if ($order == "asc_notification") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_notification"/>';
echo '<input type="image" src="../images/down.png" width="15" height="15" title="monter" style="vertical-align: middle;';
if ($order == "des_notification") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_notification"/>';
echo '</nobr>';
echo '<br>';
echo '</TH>';

//en tete filtre date creation
echo '<TH>';
echo '<nobr>';
echo 'Date creation';
echo '<input type="image" src="../images/up.png" width="15" height="15" title="monter" style="vertical-align: middle;';
if ($order == "asc_date_creation") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_date_creation"/>';
echo '<input type="image" src="../images/down.png" width="15" height="15" title="monter" style="vertical-align: middle;';
if ($order == "des_date_creation") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_date_creation"/>';
echo '</nobr>';
echo '<br>';
echo '<nobr>';
echo 'Entre : <input size="13" id="filter_date_creation_traitement_debut_plage" name="filter_date_creation_traitement_debut_plage" value="';
if ($filter_date_creation_traitement_debut_plage != null) {echo $filter_date_creation_traitement_debut_plage;}
echo '" />&nbsp;';
echo '<img id="trigger_filter_date_creation_traitement_debut_plage" src="../images/icons/calendrier.gif"/>';
echo '</nobr>';
echo '
<script type="text/javascript">
    Calendar.setup({
	inputField     :    "filter_date_creation_traitement_debut_plage",     // id of the input field
	ifFormat       :    "%d/%m/%Y %H:%M",      // format of the input field
	button         :    "trigger_filter_date_creation_traitement_debut_plage",  // trigger for the calendar (button ID)
	align          :    "Tl",           // alignment (defaults to "Bl")
	singleClick    :    true,
	showsTime	:   true
    });
</script>';
echo '<br>';
echo '<nobr>';
echo 'Et : <input size="13" id="filter_date_creation_traitement_fin_plage" name="filter_date_creation_traitement_fin_plage" value="';
if ($filter_date_creation_traitement_fin_plage != null) {echo $filter_date_creation_traitement_fin_plage;}
echo '" />&nbsp;';
echo '<img id="trigger_filter_date_creation_traitement_fin_plage" src="../images/icons/calendrier.gif"/>';
echo '</nobr>';
echo '
<script type="text/javascript">
    Calendar.setup({
	inputField     :    "filter_date_creation_traitement_fin_plage",     // id of the input field
	ifFormat       :    "%d/%m/%Y %H:%M",      // format of the input field
	button         :    "trigger_filter_date_creation_traitement_fin_plage",  // trigger for the calendar (button ID)
	align          :    "Tl",           // alignment (defaults to "Bl")
	singleClick    :    true,
	showsTime	:   true
    });
</script>';
echo '</TH>';

//en tete filtre date modification
echo '<TH>';
echo '<nobr>';
echo '';
echo '<input type="image" src="../images/up.png" width="15" height="15" title="monter" style="vertical-align: middle;';
if ($order == "asc_date_modification") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_date_modification"/>';
echo '<input type="image" src="../images/down.png" width="15" height="15" title="monter" style="vertical-align: middle;';
if ($order == "des_date_modification") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_date_modification"/>';
echo '</nobr> ';
echo '<nobr>';
echo '<INPUT TYPE="CHECKBOX" value="y" NAME="filter_date_modification" onchange="submit()"';
if ($filter_date_modification != null && $filter_date_modification == 'y') {echo "checked";}
echo '> modifié';
echo '</nobr>';
echo '</TH>';

//en tete commentaire
echo '<TH>';
echo 'commentaire';
echo '</TH>';

echo '</TR>';
echo '</THEAD>';

echo '<TBODY>';
$results = $traitements_col->getResults();
foreach ($results as $traitement) {
    //$traitement = new AbsenceEleveTraitement();
    if ($results->getPosition() %2 == '1') {
	    $background_couleur="rgb(220, 220, 220);";
    } else {
	    $background_couleur="rgb(210, 220, 230);";
    }

    echo "<tr style='background-color :$background_couleur'>\n";

    //donnees id
    echo '<TD>';
    echo "<a href='visu_traitement.php?id_traitement=".$traitement->getPrimaryKey()."' style='display: block; height: 100%;'> ";
    echo $traitement->getId();
    echo "</a>";
    echo '</TD>';

    //donnees utilisateur
    echo '<TD>';
    echo "<a href='visu_traitement.php?id_traitement=".$traitement->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'> ";
    if ($traitement->getUtilisateurProfessionnel() != null) {
	echo $traitement->getUtilisateurProfessionnel()->getCivilite().' '.$traitement->getUtilisateurProfessionnel()->getNom();
    }
    echo "</a>";
    echo '</TD>';

    //donnees eleve
    echo '<TD>';
    $eleve_col = new PropelObjectCollection();
    foreach ($traitement->getAbsenceEleveSaisies() as $saisie) {
	if ($saisie->getEleve() != null) {
	    $eleve_col->add($saisie->getEleve());
	}
    }
    foreach ($eleve_col as $eleve) {
	echo "<table style='border-spacing:0px; border-style : none; margin : 0px; padding : 0px; font-size:100%;'>";
	echo "<tr style='border-spacing:0px; border-style : none; margin : 0px; padding : 0px; font-size:100%;'>";
	echo "<td style='border-spacing:0px; border-style : none; margin : 0px; padding : 0px; font-size:100%;'>";
	echo "<a href='liste_traitements.php?filter_eleve=".$saisie->getEleve()->getNom()."' style='display: block; height: 100%;'> ";
	echo ($eleve->getCivilite().' '.$eleve->getNom().' '.$eleve->getPrenom());
	echo "</a>";
	if ($utilisateur->getAccesFicheEleve($saisie->getEleve())) {
	    echo "<a href='../eleves/visu_eleve.php?ele_login=".$eleve->getLogin()."' target='_blank'>";
	    echo ' (voir fiche)';
	    echo "</a>";
	}
	echo "</td>";
	echo "<td style='border-spacing:0px; border-style : none; margin : 0px; padding : 0px; font-size:100%;'>";
	echo "<a href='liste_traitements.php?filter_eleve=".$saisie->getEleve()->getNom()."' style='display: block; height: 100%;'> ";
 	if ((getSettingValue("active_module_trombinoscopes")=='y')) {
	    $nom_photo = $eleve->getNomPhoto(1);
	    $photos = "../photos/eleves/".$nom_photo;
	    if (($nom_photo != "") && (file_exists($photos))) {
		$valeur = redimensionne_image_petit($photos);
		echo ' <img src="'.$photos.'" align="right" width="'.$valeur[0].'px" height="'.$valeur[1].'px" alt="" title="" /> ';
	    }
	}
	echo "</a>";
	echo "</td></tr></table>";
    }
    echo '</TD>';

    //donnees saisies
    echo '<TD>';
    if (!$traitement->getAbsenceEleveSaisies()->isEmpty()) {
	echo "<table style='border-spacing:0px; border-style : none; margin : 0px; padding : 0px; font-size:100%; width: 220px;'>";
    }
    foreach ($traitement->getAbsenceEleveSaisies() as $saisie) {
	echo "<tr style='border-spacing:0px; border-style : solid; border-size : 1px; margin : 0px; padding : 0px; font-size:100%;'>";
	echo "<td style='border-spacing:0px; border-style : solid; border-size : 1px; çargin : 0px; padding-top : 3px; font-size:88%;'>";
	echo "<a href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."' style='display: block; height: 100%;'>\n";
	echo $saisie->getDescription();
	echo "</a>";
	echo "</td>";
	echo "</tr>";
    }
    if (!$traitement->getAbsenceEleveSaisies()->isEmpty()) {
	echo "</table>";
    }
    echo '</TD>';

    //donnees classe
    echo '<TD>';
    echo "<a href='visu_traitement.php?id_traitement=".$traitement->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'> ";
    $classe_col = new PropelObjectCollection();
    foreach ($traitement->getAbsenceEleveSaisies() as $saisie) {
	if ($saisie->getClasse() != null) {
	    $classe_col->add($saisie->getClasse());
	}
    }
    foreach ($classe_col as $classe) {
	echo $classe->getNomComplet();
    }
    if ($classe_col->isEmpty() != null) {
	echo "&nbsp;";
    }
    echo "</a>";
    echo '</TD>';

    //donnees type
    echo '<TD><nobr>';
    echo "<a href='visu_traitement.php?id_traitement=".$traitement->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'>\n";
    if ($traitement->getAbsenceEleveType() != null) {
	echo $traitement->getAbsenceEleveType()->getNom();
    } else {
	echo "&nbsp;";
    }
    echo "</a>";
    echo '</nobr></TD>';

    //donnees justification
    echo '<TD><nobr>';
    echo "<a href='visu_traitement.php?id_traitement=".$traitement->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'>\n";
    if ($traitement->getAbsenceEleveJustification() != null) {
	echo $traitement->getAbsenceEleveJustification()->getNom();
    } else {
	echo "&nbsp;";
    }
    echo "</a>";
    echo '</nobr></TD>';

    //donnees notification
    echo '<TD>';
    echo "<a href='visu_traitement.php?id_traitement=".$traitement->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'> ";
    echo "<table style='border-spacing:0px; border-style : none; margin : 0px; padding : 0px; font-size:100%; width: 200px;'>";
    foreach ($traitement->getAbsenceEleveNotifications() as $notification) {
	echo "<tr style='border-spacing:0px; border-style : solid; border-size : 1px; margin : 0px; padding : 0px; font-size:100%;'>";
	echo "<td style='border-spacing:0px; border-style : solid; border-size : 1px; çargin : 0px; padding-top : 3px; font-size:88%;'>";
	echo "<a href='visu_notification.php?id_notification=".$notification->getPrimaryKey()."' style='display: block; height: 100%;'>\n";
	echo $notification->getDescription();
	echo "</a>";
	echo "</td>";
	echo "</tr>";
    }
    echo "</table>";
    echo "</a>";
    echo '</TD>';

    echo '<TD><nobr>';
    echo "<a href='visu_traitement.php?id_traitement=".$traitement->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'>\n";
    echo (strftime("%a %d %b %Y %H:%M", $traitement->getCreatedAt('U')));
    echo "</a>";
    echo '</nobr></TD>';

    echo '<TD>';
    echo "<a href='visu_traitement.php?id_traitement=".$traitement->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'>\n";
    echo (strftime("%a %d %b %Y %H:%M", $traitement->getUpdatedAt('U')));
    echo "</a>";
    echo '</TD>';

    echo '<TD>';
    echo "<a href='visu_traitement.php?id_traitement=".$traitement->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'>\n";
    echo ($traitement->getCommentaire());
    echo "&nbsp;";
    echo "</a>";
    echo '</TD>';

    echo '</TR>';
}

echo '</TBODY>';
echo '</TBODY>';

echo '</table>';

//fonction redimensionne les photos petit format
function redimensionne_image_petit($photo)
 {
    // prendre les informations sur l'image
    $info_image = getimagesize($photo);
    // largeur et hauteur de l'image d'origine
    $largeur = $info_image[0];
    $hauteur = $info_image[1];
    // largeur et/ou hauteur maximum à afficher
             $taille_max_largeur = 35;
             $taille_max_hauteur = 35;

    // calcule le ratio de redimensionnement
     $ratio_l = $largeur / $taille_max_largeur;
     $ratio_h = $hauteur / $taille_max_hauteur;
     $ratio = ($ratio_l > $ratio_h)?$ratio_l:$ratio_h;

    // définit largeur et hauteur pour la nouvelle image
     $nouvelle_largeur = $largeur / $ratio;
     $nouvelle_hauteur = $hauteur / $ratio;

   // on renvoit la largeur et la hauteur
    return array($nouvelle_largeur, $nouvelle_hauteur);
 }
?>