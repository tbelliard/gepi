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

if (isset($_POST["creation_traitement"]) || isset($_POST["ajout_saisie_traitement"])) {
    include('creation_traitement.php');
    die;
}

//récupération des paramètres de la requète
$order = isset($_POST["order"]) ? $_POST["order"] :(isset($_GET["order"]) ? $_GET["order"] :(isset($_SESSION["order"]) ? $_SESSION["order"] : NULL));

$filter_id = isset($_POST["filter_id"]) ? $_POST["filter_id"] :(isset($_GET["filter_id"]) ? $_GET["filter_id"] :(isset($_SESSION["filter_id"]) ? $_SESSION["filter_id"] : NULL));
$filter_utilisateur = isset($_POST["filter_utilisateur"]) ? $_POST["filter_utilisateur"] :(isset($_GET["filter_utilisateur"]) ? $_GET["filter_utilisateur"] :(isset($_SESSION["filter_utilisateur"]) ? $_SESSION["filter_utilisateur"] : NULL));
$filter_eleve = isset($_POST["filter_eleve"]) ? $_POST["filter_eleve"] :(isset($_GET["filter_eleve"]) ? $_GET["filter_eleve"] :(isset($_SESSION["filter_eleve"]) ? $_SESSION["filter_eleve"] : NULL));
$filter_classe = isset($_POST["filter_classe"]) ? $_POST["filter_classe"] :(isset($_GET["filter_classe"]) ? $_GET["filter_classe"] :(isset($_SESSION["filter_classe"]) ? $_SESSION["filter_classe"] : NULL));
$filter_groupe = isset($_POST["filter_groupe"]) ? $_POST["filter_groupe"] :(isset($_GET["filter_groupe"]) ? $_GET["filter_groupe"] :(isset($_SESSION["filter_groupe"]) ? $_SESSION["filter_groupe"] : NULL));
$filter_aid = isset($_POST["filter_aid"]) ? $_POST["filter_aid"] :(isset($_GET["filter_aid"]) ? $_GET["filter_aid"] :(isset($_SESSION["filter_aid"]) ? $_SESSION["filter_aid"] : NULL));
$filter_date_debut_absence_debut_plage = isset($_POST["filter_date_debut_absence_debut_plage"]) ? $_POST["filter_date_debut_absence_debut_plage"] :(isset($_GET["filter_date_debut_absence_debut_plage"]) ? $_GET["filter_date_debut_absence_debut_plage"] :(isset($_SESSION["filter_date_debut_absence_debut_plage"]) ? $_SESSION["filter_date_debut_absence_debut_plage"] : NULL));
$filter_date_debut_absence_fin_plage = isset($_POST["filter_date_debut_absence_fin_plage"]) ? $_POST["filter_date_debut_absence_fin_plage"] :(isset($_GET["filter_date_debut_absence_fin_plage"]) ? $_GET["filter_date_debut_absence_fin_plage"] :(isset($_SESSION["filter_date_debut_absence_fin_plage"]) ? $_SESSION["filter_date_debut_absence_fin_plage"] : NULL));
$filter_date_fin_absence_debut_plage = isset($_POST["filter_date_fin_absence_debut_plage"]) ? $_POST["filter_date_fin_absence_debut_plage"] :(isset($_GET["filter_date_fin_absence_debut_plage"]) ? $_GET["filter_date_fin_absence_debut_plage"] :(isset($_SESSION["filter_date_fin_absence_debut_plage"]) ? $_SESSION["filter_date_fin_absence_debut_plage"] : NULL));
$filter_date_fin_absence_fin_plage = isset($_POST["filter_date_fin_absence_fin_plage"]) ? $_POST["filter_date_fin_absence_fin_plage"] :(isset($_GET["filter_date_fin_absence_fin_plage"]) ? $_GET["filter_date_fin_absence_fin_plage"] :(isset($_SESSION["filter_date_fin_absence_fin_plage"]) ? $_SESSION["filter_date_fin_absence_fin_plage"] : NULL));
$filter_creneau = isset($_POST["filter_creneau"]) ? $_POST["filter_creneau"] :(isset($_GET["filter_creneau"]) ? $_GET["filter_creneau"] :(isset($_SESSION["filter_creneau"]) ? $_SESSION["filter_creneau"] : NULL));
$filter_cours = isset($_POST["filter_cours"]) ? $_POST["filter_cours"] :(isset($_GET["filter_cours"]) ? $_GET["filter_cours"] :(isset($_SESSION["filter_cours"]) ? $_SESSION["filter_cours"] : NULL));
$filter_date_creation_absence_debut_plage = isset($_POST["filter_date_creation_absence_debut_plage"]) ? $_POST["filter_date_creation_absence_debut_plage"] :(isset($_GET["filter_date_creation_absence_debut_plage"]) ? $_GET["filter_date_creation_absence_debut_plage"] :(isset($_SESSION["filter_date_creation_absence_debut_plage"]) ? $_SESSION["filter_date_creation_absence_debut_plage"] : NULL));
$filter_date_creation_absence_fin_plage = isset($_POST["filter_date_creation_absence_fin_plage"]) ? $_POST["filter_date_creation_absence_fin_plage"] :(isset($_GET["filter_date_creation_absence_fin_plage"]) ? $_GET["filter_date_creation_absence_fin_plage"] :(isset($_SESSION["filter_date_creation_absence_fin_plage"]) ? $_SESSION["filter_date_creation_absence_fin_plage"] : NULL));
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
$filter_type = isset($_POST["filter_type"]) ? $_POST["filter_type"] :(isset($_GET["filter_type"]) ? $_GET["filter_type"] :(isset($_SESSION["filter_type"]) ? $_SESSION["filter_type"] : NULL));

$reinit_filtre = isset($_POST["reinit_filtre"]) ? $_POST["reinit_filtre"] :(isset($_GET["reinit_filtre"]) ? $_GET["reinit_filtre"] :NULL);
if ($reinit_filtre == 'y') {
    $filter_id = NULL;
    $filter_utilisateur = NULL;
    $filter_eleve = NULL;
    $filter_classe = NULL;
    $filter_groupe = NULL;
    $filter_aid = NULL;
    $filter_date_debut_absence_debut_plage = NULL;
    $filter_date_debut_absence_fin_plage = NULL;
    $filter_date_fin_absence_debut_plage = NULL;
    $filter_date_fin_absence_fin_plage = NULL;
    $filter_creneau = NULL;
    $filter_cours = NULL;
    $filter_date_creation_absence_debut_plage = NULL;
    $filter_date_creation_absence_fin_plage = NULL;
    $filter_date_modification = NULL;
    $filter_date_traitement_absence_debut_plage = NULL;
    $filter_date_traitement_absence_fin_plage = NULL;
    $filter_discipline = NULL;
    $filter_type = NULL;

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
if (isset($filter_date_debut_absence_debut_plage) && $filter_date_debut_absence_debut_plage != null) $_SESSION['filter_date_debut_absence_debut_plage'] = $filter_date_debut_absence_debut_plage;
if (isset($filter_date_debut_absence_fin_plage) && $filter_date_debut_absence_fin_plage != null) $_SESSION['filter_date_debut_absence_fin_plage'] = $filter_date_debut_absence_fin_plage;
if (isset($filter_date_fin_absence_debut_plage) && $filter_date_fin_absence_debut_plage != null) $_SESSION['filter_date_fin_absence_debut_plage'] = $filter_date_fin_absence_debut_plage;
if (isset($filter_date_fin_absence_fin_plage) && $filter_date_fin_absence_fin_plage != null) $_SESSION['filter_date_fin_absence_fin_plage'] = $filter_date_fin_absence_fin_plage;
if (isset($filter_creneau) && $filter_creneau != null) $_SESSION['filter_creneau'] = $filter_creneau;
if (isset($filter_cours) && $filter_cours != null) $_SESSION['filter_cours'] = $filter_cours;
if (isset($filter_date_creation_absence_debut_plage) && $filter_date_creation_absence_debut_plage != null) $_SESSION['filter_date_creation_absence_debut_plage'] = $filter_date_creation_absence_debut_plage;
if (isset($filter_date_creation_absence_fin_plage) && $filter_date_creation_absence_fin_plage != null) $_SESSION['filter_date_creation_absence_fin_plage'] = $filter_date_creation_absence_fin_plage;
if (isset($filter_date_modification) && $filter_date_modification != null) $_SESSION['filter_date_modification'] = $filter_date_modification;
if (isset($filter_date_traitement_absence_debut_plage) && $filter_date_traitement_absence_debut_plage != null) $_SESSION['filter_date_traitement_absence_debut_plage'] = $filter_date_traitement_absence_debut_plage;
if (isset($filter_date_traitement_absence_fin_plage) && $filter_date_traitement_absence_fin_plage != null) $_SESSION['filter_date_traitement_absence_fin_plage'] = $filter_date_traitement_absence_fin_plage;
if (isset($filter_discipline) && $filter_discipline != null) $_SESSION['filter_discipline'] = $filter_discipline;
if (isset($filter_type) && $filter_type != null) $_SESSION['filter_type'] = $filter_type;

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
$javascript_specifique[] = "mod_abs2/lib/include";
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


$query = AbsenceEleveSaisieQuery::create();
//$query->leftJoin('AbsenceEleveSaisie.JTraitementSaisieEleve')->leftJoin('JTraitementSaisieEleve.AbsenceEleveTraitement')->with('AbsenceEleveTraitement');
if ($filter_id != null && $filter_id != '') {
    $query->filterById($filter_id);
}
if ($filter_utilisateur != null && $filter_utilisateur != '') {
    $query->useUtilisateurProfessionnelQuery()->filterByNom('%'.$filter_utilisateur.'%', Criteria::LIKE)->endUse();
}
if ($filter_eleve != null && $filter_eleve != '') {
    $query->useEleveQuery()->filterByNomOrPrenomLike($filter_eleve)->endUse();
}
if ($filter_classe != null && $filter_classe != '-1') {
    $query->leftJoin('AbsenceEleveSaisie.Eleve');
    $query->leftJoin('Eleve.JEleveClasse');
    $query->condition('cond1', 'JEleveClasse.IdClasse = ?', $filter_classe);
    $query->condition('cond2', 'AbsenceEleveSaisie.IdClasse = ?', $filter_classe);
    $query->where(array('cond1', 'cond2'), 'or');
}
if ($filter_groupe != null && $filter_groupe != '-1') {
    $query->leftJoin('AbsenceEleveSaisie.Eleve');
    $query->leftJoin('Eleve.JEleveGroupe');
    $query->condition('cond1', 'JEleveGroupe.IdGroupe = ?', $filter_groupe);
    $query->condition('cond2', 'AbsenceEleveSaisie.IdGroupe = ?', $filter_groupe);
    $query->where(array('cond1', 'cond2'), 'or');
}
if ($filter_aid != null && $filter_aid != '-1') {
    $query->leftJoin('AbsenceEleveSaisie.Eleve');
    $query->leftJoin('Eleve.JAidEleves');
    $query->condition('cond1', 'JAidEleves.IdAid = ?', $filter_aid);
    $query->condition('cond2', 'AbsenceEleveSaisie.IdAid = ?', $filter_aid);
    $query->where(array('cond1', 'cond2'), 'or');
}
if ($filter_date_debut_absence_debut_plage != null && $filter_date_debut_absence_debut_plage != '-1') {
    $date_debut_absence_debut_plage = new DateTime(str_replace("/",".",$filter_date_debut_absence_debut_plage));
    $query->filterByDebutAbs($date_debut_absence_debut_plage, Criteria::GREATER_EQUAL);
}
if ($filter_date_debut_absence_fin_plage != null && $filter_date_debut_absence_fin_plage != '-1') {
    $date_debut_absence_fin_plage = new DateTime(str_replace("/",".",$filter_date_debut_absence_fin_plage));
    $query->filterByDebutAbs($date_debut_absence_fin_plage, Criteria::LESS_EQUAL);
}
if ($filter_date_fin_absence_debut_plage != null && $filter_date_fin_absence_debut_plage != '-1') {
    $date_fin_absence_debut_plage = new DateTime(str_replace("/",".",$filter_date_fin_absence_debut_plage));
    $query->filterByFinAbs($date_fin_absence_debut_plage, Criteria::GREATER_EQUAL);
}
if ($filter_date_fin_absence_fin_plage != null && $filter_date_fin_absence_fin_plage != '-1') {
    $date_fin_absence_fin_plage = new DateTime(str_replace("/",".",$filter_date_fin_absence_fin_plage));
    $query->filterByFinAbs($date_fin_absence_fin_plage, Criteria::LESS_EQUAL);
}
if ($filter_creneau != null && $filter_creneau != '-1') {
    $query->filterByIdEdtCreneau($filter_creneau);
}
if ($filter_cours != null && $filter_cours != '-1') {
    $query->filterByIdEdtEmplacementCours($filter_cours);
}
if ($filter_date_creation_absence_debut_plage != null && $filter_date_creation_absence_debut_plage != '-1') {
    $date_creation_absence_debut_plage = new DateTime(str_replace("/",".",$filter_date_creation_absence_debut_plage));
    $query->filterByCreatedAt($date_creation_absence_debut_plage, Criteria::GREATER_EQUAL);
}
if ($filter_date_creation_absence_fin_plage != null && $filter_date_creation_absence_fin_plage != '-1') {
    $date_creation_absence_fin_plage = new DateTime(str_replace("/",".",$filter_date_creation_absence_fin_plage));
    $query->filterByCreatedAt($date_creation_absence_fin_plage, Criteria::LESS_EQUAL);
}
if ($filter_date_modification != null && $filter_date_modification == 'y') {
    $query->where('AbsenceEleveSaisie.CreatedAt != AbsenceEleveSaisie.UpdatedAt');
}
if ($filter_date_traitement_absence_debut_plage != null && $filter_date_traitement_absence_debut_plage != '-1') {
    $date_traitement_absence_debut_plage = new DateTime(str_replace("/",".",$filter_date_traitement_absence_debut_plage));
    $query->leftJoin('AbsenceEleveSaisie.JTraitementSaisieEleve')->leftJoin('JTraitementSaisieEleve.AbsenceEleveTraitement')->where('AbsenceEleveTraitement.UpdatedAt >= ?', $date_traitement_absence_debut_plage);
}
if ($filter_date_traitement_absence_fin_plage != null && $filter_date_traitement_absence_fin_plage != '-1') {
    $date_traitement_absence_fin_plage = new DateTime(str_replace("/",".",$filter_date_traitement_absence_fin_plage));
    $query->leftJoin('AbsenceEleveSaisie.JTraitementSaisieEleve')->leftJoin('JTraitementSaisieEleve.AbsenceEleveTraitement');
    $query->condition('trait1', 'AbsenceEleveTraitement.UpdatedAt <= ?', $date_traitement_absence_fin_plage);
    $query->condition('trait2', 'AbsenceEleveTraitement.UpdatedAt IS NULL');
    $query->where(array('trait1', 'trait2'), 'or');
}
if ($filter_discipline != null && $filter_discipline == 'y') {
    $query->filterByIdSIncidents(null, Criteria::NOT_EQUAL);
    $query->filterByIdSIncidents(-1, Criteria::NOT_EQUAL);
}
if ($filter_type != null && $filter_type != '-1') {
    $query->useJTraitementSaisieEleveQuery()->useAbsenceEleveTraitementQuery()->filterByATypeId($filter_type)->endUse()->endUse();
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
    $query->useEleveQuery()->orderBy('Nom', Criteria::ASC)->endUse();
} else if ($order == "des_eleve") {
    $query->useEleveQuery()->orderBy('Prenom', Criteria::DESC)->endUse();
} else if ($order == "asc_classe") {
    $query->useClasseQuery()->orderBy('NomComplet', Criteria::ASC)->endUse();
} else if ($order == "des_classe") {
    $query->useClasseQuery()->orderBy('NomComplet', Criteria::DESC)->endUse();
} else if ($order == "asc_groupe") {
    $query->useGroupeQuery()->orderBy('Name', Criteria::ASC)->endUse();
} else if ($order == "des_groupe") {
    $query->useGroupeQuery()->orderBy('Name', Criteria::DESC)->endUse();
} else if ($order == "asc_aid") {
    $query->useAidDetailsQuery()->orderBy('Nom', Criteria::ASC)->endUse();
} else if ($order == "des_aid") {
    $query->useAidDetailsQuery()->orderBy('Nom', Criteria::DESC)->endUse();
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
} else if ($order == "asc_date_traitement") {
    $query->leftJoin('AbsenceEleveSaisie.JTraitementSaisieEleve')->leftJoin('JTraitementSaisieEleve.AbsenceEleveTraitement')->orderBy('AbsenceEleveTraitement.UpdatedAt', Criteria::ASC);
} else if ($order == "des_date_traitement") {
    $query->leftJoin('AbsenceEleveSaisie.JTraitementSaisieEleve')->leftJoin('JTraitementSaisieEleve.AbsenceEleveTraitement')->orderBy('AbsenceEleveTraitement.UpdatedAt', Criteria::DESC);
} else if ($order == "asc_dis") {
    $query->orderBy('IdSIncidents', Criteria::ASC);
} else if ($order == "des_dis") {
    $query->orderBy('IdSIncidents', Criteria::DESC);
}

$query->distinct();
$saisies_col = $query->paginate($page_number, $item_per_page);

$nb_pages = (floor($saisies_col->getNbResults() / $item_per_page) + 1);
if ($page_number > $nb_pages) {
    $page_number = $nb_pages;
}

echo '<form method="post" action="liste_saisies_selection_traitement.php" id="liste_saisies">';

if ($saisies_col->haveToPaginate()) {
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
echo $saisies_col->count();

echo "&nbsp;&nbsp;&nbsp;";
echo '<button type="submit" name="reinit_filtre" value="y"/>Reinitialiser les filtres</button> ';
echo '<button type="submit">Rechercher</button>';
echo '<br/>';
echo '<button type="submit" name="creation_traitement" value="creation_traitement">Creer un traitement</button>';

$id_traitement = isset($_POST["id_traitement"]) ? $_POST["id_traitement"] :(isset($_GET["id_traitement"]) ? $_GET["id_traitement"] :(isset($_SESSION["id_traitement"]) ? $_SESSION["id_traitement"] : NULL));
if ($id_traitement != null && AbsenceEleveTraitementQuery::create()->findPk($id_traitement) != null) {
    $traitement = AbsenceEleveTraitementQuery::create()->findPk($id_traitement);
    echo '<button type="submit" name="ajout_saisie_traitement" value="ajout_saisie_traitement">Ajouter les saisies au traitement n° '.$id_traitement.' ('.$traitement->getDescription().')</button>';
    echo '<input type="hidden" name="id_traitement" value="'.$id_traitement.'"/>';
}
if (isset($message_erreur_traitement)) {
    echo $message_erreur_traitement;
}

echo '<br/>';
echo 'Sélectionner: ';
echo '<a href="" onClick="SetAllCheckBoxes(\'liste_saisies\', \'select_saisie[]\', \'\', true); return false;">Tous</a>, ';
echo '<a href="" onClick="SetAllCheckBoxes(\'liste_saisies\', \'select_saisie[]\', \'\', false); return false;">Aucun</a>, ';
echo '<a href="" onClick="SetAllCheckBoxes(\'liste_saisies\', \'select_saisie[]\', \'\', false);
    SetAllCheckBoxes(\'liste_saisies\', \'select_saisie[]\', \'saisie_vierge\', true);
    return false;">Non traités</a>, ';
echo '<a href="" onClick="SetAllCheckBoxes(\'liste_saisies\', \'select_saisie[]\', \'\', true);
    SetAllCheckBoxes(\'liste_saisies\', \'select_saisie[]\', \'saisie_notifie\', false);
    return false;">Non notifiés</a>';

echo '<table id="table_liste_absents" class="tb_absences" style="border-spacing:0;">';

echo '<THEAD>';
echo '<TR>';

echo '<TH>';
echo '</TH>';

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
foreach ($utilisateur->getClasses() as $classe) {
	echo "<option value='".$classe->getId()."'";
	if ($filter_classe == $classe->getId()) echo " SELECTED ";
	echo ">";
	echo $classe->getNomComplet();
	echo "</option>\n";
}
echo "</select>";
echo '</TH>';

//en tete filtre groupe
echo '<TH>';
echo '<nobr>';
echo 'Groupe';
echo '<input type="image" src="../images/up.png" width="15" height="15" title="monter" style="vertical-align: middle;';
if ($order == "asc_groupe") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_groupe"/>';
echo '<input type="image" src="../images/down.png" width="15" height="15" title="monter" style="vertical-align: middle;';
if ($order == "des_groupe") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_groupe"/>';
echo '</nobr>';
echo '<br>';
echo ("<select name=\"filter_groupe\" onchange='submit()'>");
echo "<option value='-1'></option>\n";
foreach ($utilisateur->getGroupes()  as $group) {
	echo "<option value='".$group->getId()."'";
	if ($filter_groupe == $group->getId()) echo " SELECTED ";
	echo ">";
	echo $group->getNameAvecClasses();
	echo "</option>\n";
}
echo "</select>";
echo '</TH>';

//en tete filtre aid
echo '<TH>';
echo '<nobr>';
echo 'AID';
echo '<input type="image" src="../images/up.png" width="15" height="15" title="monter" style="vertical-align: middle;';
if ($order == "asc_aid") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_aid"/>';
echo '<input type="image" src="../images/down.png" width="15" height="15" title="monter" style="vertical-align: middle;';
if ($order == "des_aid") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_aid"/>';
echo '</nobr>';
echo '<br>';
echo ("<select name=\"filter_aid\" onchange='submit()'>");
echo "<option value='-1'></option>\n";
$temp_collection = $utilisateur->getAidDetailss();
//$temp_collection->add(AidDetailsQuery::create()->useJAidElevesQuery()->useEleveQuery()->useJEleveCpeQuery()->filterByUtilisateurProfessionnel($utilisateur)->endUse()->endUse()->endUse()->find());
foreach ($temp_collection as $aid) {
	echo "<option value='".$aid->getId()."'";
	if ($filter_aid == $aid->getId()) echo " SELECTED ";
	echo ">";
	echo $aid->getNom();
	echo "</option>\n";
}
echo "</select>";
echo '</TH>';

//en tete filtre creneaux
echo '<TH>';
echo '<nobr>';
echo 'Creneau';
echo '<input type="image" src="../images/up.png" width="15" height="15" title="monter" style="vertical-align: middle;';
if ($order == "asc_creneau") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_creneau"/>';
echo '<input type="image" src="../images/down.png" width="15" height="15" title="monter" style="vertical-align: middle;';
if ($order == "des_creneau") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_creneau"/>';
echo '</nobr>';
echo '<br>';
echo ("<select name=\"filter_creneau\" onchange='submit()'>");
echo "<option value='-1'></option>\n";
foreach (EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime() as $edt_creneau) {
	echo "<option value='".$edt_creneau->getIdDefiniePeriode()."'";
	if ($filter_creneau == $edt_creneau->getIdDefiniePeriode()) echo " SELECTED ";
	echo ">";
	echo $edt_creneau->getDescription();
	echo "</option>\n";
}
echo "</select>";
echo '</TH>';

//en tete filtre date debut
echo '<TH>';
echo '<nobr>';
echo 'Date debut';
echo '<input type="image" src="../images/up.png" width="15" height="15" title="monter" style="vertical-align: middle;';
if ($order == "asc_date_debut") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_date_debut"/>';
echo '<input type="image" src="../images/down.png" width="15" height="15" title="monter" style="vertical-align: middle;';
if ($order == "des_date_debut") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_date_debut"/>';
echo '</nobr>';
echo '<br>';
echo '<nobr>';
echo 'Entre : <input size="13" id="filter_date_debut_absence_debut_plage" name="filter_date_debut_absence_debut_plage" value="';
if ($filter_date_debut_absence_debut_plage != null) {echo $filter_date_debut_absence_debut_plage;}
echo '" />&nbsp;';
echo '<img id="trigger_filter_date_debut_absence_debut_plage" src="../images/icons/calendrier.gif"/>';
echo '</nobr>';
echo '
<script type="text/javascript">
    Calendar.setup({
	inputField     :    "filter_date_debut_absence_debut_plage",     // id of the input field
	ifFormat       :    "%d/%m/%Y %H:%M",      // format of the input field
	button         :    "trigger_filter_date_debut_absence_debut_plage",  // trigger for the calendar (button ID)
	align          :    "Tl",           // alignment (defaults to "Bl")
	singleClick    :    true,
	showsTime	:   true
    });
</script>';
echo '<br>';
echo '<nobr>';
echo 'Et : <input size="13" id="filter_date_debut_absence_fin_plage" name="filter_date_debut_absence_fin_plage" value="';
if ($filter_date_debut_absence_fin_plage != null) {echo $filter_date_debut_absence_fin_plage;}
echo '" />&nbsp;';
echo '<img id="trigger_filter_date_debut_absence_fin_plage" src="../images/icons/calendrier.gif"/>';
echo '</nobr>';
echo '
<script type="text/javascript">
    Calendar.setup({
	inputField     :    "filter_date_debut_absence_fin_plage",     // id of the input field
	ifFormat       :    "%d/%m/%Y %H:%M",      // format of the input field
	button         :    "trigger_filter_date_debut_absence_fin_plage",  // trigger for the calendar (button ID)
	align          :    "Tl",           // alignment (defaults to "Bl")
	singleClick    :    true,
	showsTime	:   true
    });
</script>';
echo '</TH>';

//en tete filtre date fin
echo '<TH>';
echo '<nobr>';
echo 'Date fin';
echo '<input type="image" src="../images/up.png" width="15" height="15" title="monter" style="vertical-align: middle;';
if ($order == "asc_date_fin") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_date_fin"/>';
echo '<input type="image" src="../images/down.png" width="15" height="15" title="monter" style="vertical-align: middle;';
if ($order == "des_date_fin") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_date_fin"/>';
echo '</nobr>';
echo '<br>';
echo '<nobr>';
echo 'Entre : <input size="13" id="filter_date_fin_absence_debut_plage" name="filter_date_fin_absence_debut_plage" value="';
if ($filter_date_fin_absence_debut_plage != null) {echo $filter_date_fin_absence_debut_plage;}
echo '" />&nbsp;';
echo '<img id="trigger_filter_date_fin_absence_debut_plage" src="../images/icons/calendrier.gif"/>';
echo '</nobr>';
echo '
<script type="text/javascript">
    Calendar.setup({
	inputField     :    "filter_date_fin_absence_debut_plage",     // id of the input field
	ifFormat       :    "%d/%m/%Y %H:%M",      // format of the input field
	button         :    "trigger_filter_date_fin_absence_debut_plage",  // trigger for the calendar (button ID)
	align          :    "Tl",           // alignment (defaults to "Bl")
	singleClick    :    true,
	showsTime	:   true
    });
</script>';
echo '<br>';
echo '<nobr>';
echo 'Et : <input size="13" id="filter_date_fin_absence_fin_plage" name="filter_date_debut_absence_fin_plage" value="';
if ($filter_date_fin_absence_fin_plage != null) {echo $filter_date_fin_absence_fin_plage;}
echo '" />&nbsp;';
echo '<img id="trigger_filter_date_fin_absence_fin_plage" src="../images/icons/calendrier.gif"/>';
echo '</nobr>';
echo '
<script type="text/javascript">
    Calendar.setup({
	inputField     :    "filter_date_fin_absence_fin_plage",     // id of the input field
	ifFormat       :    "%d/%m/%Y %H:%M",      // format of the input field
	button         :    "trigger_filter_date_fin_absence_fin_plage",  // trigger for the calendar (button ID)
	align          :    "Tl",           // alignment (defaults to "Bl")
	singleClick    :    true,
	showsTime	:   true
    });
</script>';
echo '</TH>';

//en tete filtre emplacement de cours
echo '<TH>';
echo '<nobr>';
echo 'Cours';
echo '</nobr>';
echo '</TH>';

//en tete type d'absence
echo '<TH>';
echo '<nobr>';
echo 'type';
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

//en tete filtre date traitement
echo '<TH>';
echo '<nobr>';
echo 'Date traitement';
echo '<input type="image" src="../images/up.png" width="15" height="15" title="monter" style="vertical-align: middle;';
if ($order == "asc_date_traitement") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_date_traitement"/>';
echo '<input type="image" src="../images/down.png" width="15" height="15" title="monter" style="vertical-align: middle;';
if ($order == "des_date_traitement") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_date_traitement"/>';
echo '</nobr>';
echo '<br>';
echo '<nobr>';
echo 'Entre : <input size="13" id="filter_date_traitement_absence_debut_plage" name="filter_date_traitement_absence_debut_plage" value="';
if ($filter_date_traitement_absence_debut_plage != null) {echo $filter_date_traitement_absence_debut_plage;}
echo '" />&nbsp;';
echo '<img id="trigger_filter_date_traitement_absence_debut_plage" src="../images/icons/calendrier.gif"/>';
echo '</nobr>';
echo '
<script type="text/javascript">
    Calendar.setup({
	inputField     :    "filter_date_traitement_absence_debut_plage",     // id of the input field
	ifFormat       :    "%d/%m/%Y %H:%M",      // format of the input field
	button         :    "trigger_filter_date_traitement_absence_debut_plage",  // trigger for the calendar (button ID)
	align          :    "Tl",           // alignment (defaults to "Bl")
	singleClick    :    true,
	showsTime	:   true
    });
</script>';
echo '<br>';
echo '<nobr>';
echo 'Et : <input size="13" id="filter_date_traitement_absence_fin_plage" name="filter_date_traitement_absence_fin_plage" value="';
if ($filter_date_traitement_absence_fin_plage != null) {echo $filter_date_traitement_absence_fin_plage;}
echo '" />&nbsp;';
echo '<img id="trigger_filter_date_traitement_absence_fin_plage" src="../images/icons/calendrier.gif"/>';
echo '</nobr>';
echo '
<script type="text/javascript">
    Calendar.setup({
	inputField     :    "filter_date_traitement_absence_fin_plage",     // id of the input field
	ifFormat       :    "%d/%m/%Y %H:%M",      // format of the input field
	button         :    "trigger_filter_date_traitement_absence_fin_plage",  // trigger for the calendar (button ID)
	align          :    "Tl",           // alignment (defaults to "Bl")
	singleClick    :    true,
	showsTime	:   true
    });
</script>';
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
echo 'Entre : <input size="13" id="filter_date_creation_absence_debut_plage" name="filter_date_creation_absence_debut_plage" value="';
if ($filter_date_creation_absence_debut_plage != null) {echo $filter_date_creation_absence_debut_plage;}
echo '" />&nbsp;';
echo '<img id="trigger_filter_date_creation_absence_debut_plage" src="../images/icons/calendrier.gif"/>';
echo '</nobr>';
echo '
<script type="text/javascript">
    Calendar.setup({
	inputField     :    "filter_date_creation_absence_debut_plage",     // id of the input field
	ifFormat       :    "%d/%m/%Y %H:%M",      // format of the input field
	button         :    "trigger_filter_date_creation_absence_debut_plage",  // trigger for the calendar (button ID)
	align          :    "Tl",           // alignment (defaults to "Bl")
	singleClick    :    true,
	showsTime	:   true
    });
</script>';
echo '<br>';
echo '<nobr>';
echo 'Et : <input size="13" id="filter_date_creation_absence_fin_plage" name="filter_date_creation_absence_fin_plage" value="';
if ($filter_date_creation_absence_fin_plage != null) {echo $filter_date_creation_absence_fin_plage;}
echo '" />&nbsp;';
echo '<img id="trigger_filter_date_creation_absence_fin_plage" src="../images/icons/calendrier.gif"/>';
echo '</nobr>';
echo '
<script type="text/javascript">
    Calendar.setup({
	inputField     :    "filter_date_creation_absence_fin_plage",     // id of the input field
	ifFormat       :    "%d/%m/%Y %H:%M",      // format of the input field
	button         :    "trigger_filter_date_creation_absence_fin_plage",  // trigger for the calendar (button ID)
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

//en tete disciplinne
echo '<TH>';
echo '<nobr>';
echo '<input type="image" src="../images/up.png" width="15" height="15" title="monter" style="vertical-align: middle;';
if ($order == "asc_dis") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_dis"/>';
echo '<input type="image" src="../images/down.png" width="15" height="15" title="monter" style="vertical-align: middle;';
if ($order == "des_dis") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_dis"/>';
echo '</nobr> ';
echo '<nobr>';
echo '<INPUT TYPE="CHECKBOX" value="y" NAME="filter_discipline" onchange="submit()"';
if ($filter_discipline != null && $filter_discipline == 'y') {echo "checked";}
echo '> Rapport<br/>d\'incident';
echo '</nobr>';
echo '</TH>';

echo '</TR>';
echo '</THEAD>';

echo '<TBODY>';
$results = $saisies_col->getResults();
foreach ($results as $saisie) {

    if ($results->getPosition() %2 == '1') {
	    $background_couleur="rgb(220, 220, 220);";
    } else {
	    $background_couleur="rgb(210, 220, 230);";
    }

    echo "<tr style='background-color :$background_couleur'>\n";

    if ($saisie->getNotifiee()) {
	$prop = 'saisie_notifie';
    } elseif ($saisie->getTraitee()) {
	$prop = 'saisie_traite';
    } else {
	$prop = 'saisie_vierge';
    }
    echo '<td><input name="select_saisie[]" value="'.$saisie->getPrimaryKey().'" type="checkbox" id="'.$prop.'_'.$results->getPosition().'"/></td>';

    echo '<TD>';
    echo "<a href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."' style='display: block; height: 100%;'> ";
    echo $saisie->getId();
    echo "</a>";
    echo '</TD>';

    echo '<TD>';
    echo "<a href='liste_saisies_selection_traitement.php?filter_utilisateur=".$saisie->getUtilisateurProfessionnel()->getNom()."' style='display: block; height: 100%; color: #330033'> ";
    if ($saisie->getUtilisateurProfessionnel() != null) {
	echo $saisie->getUtilisateurProfessionnel()->getCivilite().' '.$saisie->getUtilisateurProfessionnel()->getNom();
    }
    echo "</a>";
    echo '</TD>';

    echo '<TD>';
    if ($saisie->getEleve() != null) {
	echo "<table style='border-spacing:0px; border-style : none; margin : 0px; padding : 0px; font-size:100%;'>";
	echo "<tr style='border-spacing:0px; border-style : none; margin : 0px; padding : 0px; font-size:100%;'>";
	echo "<td style='border-spacing:0px; border-style : none; margin : 0px; padding : 0px; font-size:100%;'>";
	echo "<a href='liste_saisies_selection_traitement.php?filter_eleve=".$saisie->getEleve()->getNom()."' style='display: block; height: 100%;'> ";
	echo ($saisie->getEleve()->getCivilite().' '.$saisie->getEleve()->getNom().' '.$saisie->getEleve()->getPrenom());
	echo "</a>";
	if ($utilisateur->getEleves()->contains($saisie->getEleve()) && ($utilisateur->getStatut() != 'professeur' || (getSettingValue("voir_fiche_eleve") == "y"))) {
	    echo "<a href='../eleves/visu_eleve.php?ele_login=".$saisie->getEleve()->getLogin()."&amp;onglet=absences' target='_blank'>";
	    echo ' (voir fiche)';
	    echo "</a>";
	}
	echo "</td>";
	echo "<td style='border-spacing:0px; border-style : none; margin : 0px; padding : 0px; font-size:100%;'>";
	echo "<a href='liste_saisies_selection_traitement.php?filter_eleve=".$saisie->getEleve()->getNom()."' style='display: block; height: 100%;'> ";
 	if ((getSettingValue("active_module_trombinoscopes")=='y') && $saisie->getEleve() != null) {
	    $nom_photo = $saisie->getEleve()->getNomPhoto(1);
	    $photos = "../photos/eleves/".$nom_photo;
	    if (($nom_photo != "") && (file_exists($photos))) {
		$valeur = redimensionne_image_petit($photos);
		echo ' <img src="'.$photos.'" align="right" width="'.$valeur[0].'px" height="'.$valeur[1].'px" alt="" title="" /> ';
	    }
	}
	echo "</a>";
	echo "</td></tr></table>";
    } else {
	echo "Aucun élève absent";
    }
    echo '</TD>';

    echo '<TD>';
    if ($saisie->getClasse() != null) {
	echo "<a href='liste_saisies_selection_traitement.php?filter_classe=".$saisie->getClasse()->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'> ";
	//$classe = new Classe();
	echo $classe->getNomComplet();
    } else {
	echo "&nbsp;";
    }
    echo "</a>";
    echo '</TD>';

    echo '<TD>';
    if ($saisie->getGroupe() != null) {
	echo "<a href='liste_saisies_selection_traitement.php?filter_groupe=".$saisie->getGroupe()->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'> ";
	//$groupe = new Groupe();
	echo $saisie->getGroupe()->getNameAvecClasses();
    } else {
	echo "&nbsp;";
    }
    echo "</a>";
    echo '</TD>';

    echo '<TD>';
    if ($saisie->getAidDetails() != null) {
	echo "<a href='liste_saisies_selection_traitement.php?filter_aid=".$saisie->getAidDetails()->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'>\n";
	//$groupe = new Groupe();
	echo $saisie->getAidDetails()->getNom();
    } else {
	echo "<a href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'>\n";
	echo "&nbsp;";
    }
    echo "</a>";
    echo '</TD>';

    echo '<TD>';
    echo "<a href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'>\n";
    if ($saisie->getEdtCreneau() != null) {
	//$groupe = new Groupe();
	echo $saisie->getEdtCreneau()->getDescription();
    } else {
	echo "&nbsp;";
    }
    echo '</TD>';

    echo '<TD><nobr>';
    echo "<a href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'>\n";
    echo (strftime("%a %d %b %Y %H:%M", $saisie->getDebutAbs('U')));
    echo "</a>";
    echo '</nobr></TD>';

    echo '<TD><nobr>';
    echo "<a href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'>\n";
    echo (strftime("%a %d %b %Y %H:%M", $saisie->getFinAbs('U')));
    echo "</a>";
    echo '</nobr></TD>';

    echo '<TD>';
    echo "<a href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'>\n";
    echo '<nobr>';
    if ($saisie->getEdtEmplacementCours() != null) {
	//$groupe = new Groupe();
	echo $saisie->getEdtEmplacementCours()->getDescription();
    } else {
	echo "&nbsp;";
    }
    echo '</nobr>';
    echo "</a>";
    echo '</TD>';

    echo '<TD>';
    echo "<a href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'>\n";
    foreach ($saisie->getAbsenceEleveTraitements() as $traitement) {
	 if ($traitement->getAbsenceEleveType() != null) {
	    echo $traitement->getAbsenceEleveType()->getNom();
	    echo '<br/>';
	 }
    }
    if ($saisie->getAbsenceEleveTraitements()->isEmpty()) {
	echo "&nbsp;";
    }
    echo "</a>";
    echo '</TD>';

    echo '<TD>';
    foreach ($saisie->getAbsenceEleveTraitements() as $traitement) {
	echo "<table><tr><td>";
	echo "<a href='visu_traitement.php?id_traitement=".$traitement->getPrimaryKey()."' style='display: block; height: 100%;'> ";
	echo $traitement->getDescription();
	echo "</a></div>";
	echo "</td></tr></table>";
    }
    if ($saisie->getAbsenceEleveTraitements()->isEmpty()) {
	echo "&nbsp;";
    }
    echo '</TD>';

    echo '<TD><nobr>';
    echo "<a href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'>\n";
    echo (strftime("%a %d %b %Y %H:%M", $saisie->getCreatedAt('U')));
    echo "</a>";
    echo '</nobr></TD>';

    echo '<TD>';
    echo "<a href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'>\n";
    if ($saisie->getCreatedAt() != $saisie->getUpdatedAt()) {
	echo (strftime("%a %d %b %Y %H:%M", $saisie->getUpdatedAt('U')));
    } else {
	echo "&nbsp;";
    }
    echo "</a>";
    echo '</TD>';

    echo '<TD>';
    echo "<a href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'>\n";
    echo ($saisie->getCommentaire());
    echo "&nbsp;";
    echo "</a>";
    echo '</TD>';

    echo '<TD>';
    if ($saisie->getIdSIncidents() !== null) {
	echo "<a href='../mod_discipline/saisie_incident.php?id_incident=".
	$saisie->getIdSIncidents()."&step=2&return_url=no_return'>Visualiser l'incident </a>";
    }
    echo '</td>';

    echo '</TR>';


}

echo '</TBODY>';
echo '</TBODY>';

echo '</table>';
echo 'Sélectionner: ';
echo '<a href="" onClick="SetAllCheckBoxes(\'liste_saisies\', \'select_saisie[]\', \'\', true); return false;">Tous</a>, ';
echo '<a href="" onClick="SetAllCheckBoxes(\'liste_saisies\', \'select_saisie[]\', \'\', false); return false;">Aucun</a>, ';
echo '<a href="" onClick="SetAllCheckBoxes(\'liste_saisies\', \'select_saisie[]\', \'\', false);
    SetAllCheckBoxes(\'liste_saisies\', \'select_saisie[]\', \'saisie_vierge\', true);
    return false;">Non traités</a>, ';
echo '<a href="" onClick="SetAllCheckBoxes(\'liste_saisies\', \'select_saisie[]\', \'\', true);
    SetAllCheckBoxes(\'liste_saisies\', \'select_saisie[]\', \'saisie_notifie\', false);
    return false;">Non notifiés</a>';

echo '<br/>';

echo '<button type="submit" name="creation_traitement" value="creation_traitement">Creer un traitement</button>';

$id_traitement = isset($_POST["id_traitement"]) ? $_POST["id_traitement"] :(isset($_GET["id_traitement"]) ? $_GET["id_traitement"] :(isset($_SESSION["id_traitement"]) ? $_SESSION["id_traitement"] : NULL));
if ($id_traitement != null && AbsenceEleveTraitementQuery::create()->findPk($id_traitement) != null) {
    $traitement = AbsenceEleveTraitementQuery::create()->findPk($id_traitement);
    echo '<button type="submit" name="ajout_saisie_traitement" value="ajout_saisie_traitement">Ajouter les saisies au traitement n° '.$id_traitement.' ('.$traitement->getDescription().')</button>';
    echo '<input type="hidden" name="id_traitement" value="'.$id_traitement.'"/>';
}
if (isset($message_erreur_traitement)) {
    echo $message_erreur_traitement;
}
echo '</form>';

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