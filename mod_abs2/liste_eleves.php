<?php
/**
 *
 *
 * Copyright 2012 Josselin Jacquard
 *
 * This file and the mod_abs2 module is distributed under GPL version 3, or
 * (at your option) any later version.
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

include('include_requetes_filtre_de_recherche.php');

include('include_pagination.php');

$affichage = isset($_POST["affichage"]) ? $_POST["affichage"] :(isset($_GET["affichage"]) ? $_GET["affichage"] : NULL);
$menu = isset($_POST["menu"]) ? $_POST["menu"] :(isset($_GET["menu"]) ? $_GET["menu"] : Null);
$imprime = isset($_POST["imprime"]) ? $_POST["imprime"] :(isset($_GET["imprime"]) ? $_GET["imprime"] : Null);

//==============================================
$style_specifique[] = "mod_abs2/lib/abs_style";
$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";
if(!$menu){
   $titre_page = "Liste de élèves"; 
}
$utilisation_jsdivdrag = "non";
$_SESSION['cacher_header'] = "y";

$query = EleveQuery::create();
//$query->joinWith('Eleve.AbsenceEleveSaisie', Criteria::LEFT_JOIN);
if (isFiltreRechercheParam('filter_eleve')) {
    $query->filterByNomOrPrenomLike(getFiltreRechercheParam('filter_eleve'));
}
if (isFiltreRechercheParam('filter_classe')) {
    $query->leftJoin('Eleve.JEleveClasse');
    $query->where('JEleveClasse.IdClasse = ?', getFiltreRechercheParam('filter_classe'));
}
if (isFiltreRechercheParam('filter_regime')) {
    $query->useEleveRegimeDoublantQuery()->filterByRegime(getFiltreRechercheParam('filter_regime'))->endUse();
}
if (isFiltreRechercheParam('filter_date_debut_saisie_debut_plage')) {
    $date_debut_saisie_debut_plage = new DateTime(str_replace("/",".",getFiltreRechercheParam('filter_date_debut_saisie_debut_plage')));
    $query->useAbsenceEleveSaisieQuery()->filterByDebutAbs($date_debut_saisie_debut_plage, Criteria::GREATER_EQUAL)->endUse();
}
if (isFiltreRechercheParam('filter_date_debut_saisie_fin_plage')) {
    $date_debut_saisie_fin_plage = new DateTime(str_replace("/",".",getFiltreRechercheParam('filter_date_debut_saisie_fin_plage')));
    $query->useAbsenceEleveSaisieQuery()->filterByDebutAbs($date_debut_saisie_fin_plage, Criteria::LESS_EQUAL)->endUse();
}
if (isFiltreRechercheParam('filter_date_fin_saisie_debut_plage')) {
    $date_fin_absence_debut_plage = new DateTime(str_replace("/",".",getFiltreRechercheParam('filter_date_fin_saisie_debut_plage')));
    $query->useAbsenceEleveSaisieQuery()->filterByFinAbs($date_fin_absence_debut_plage, Criteria::GREATER_EQUAL)->endUse();
}
if (isFiltreRechercheParam('filter_date_fin_saisie_fin_plage')) {
    $date_fin_absence_fin_plage = new DateTime(str_replace("/",".",getFiltreRechercheParam('filter_date_fin_saisie_fin_plage')));
    $query->useAbsenceEleveSaisieQuery()->filterByFinAbs($date_fin_absence_fin_plage, Criteria::LESS_EQUAL)->endUse();
}
if (isFiltreRechercheParam('filter_type')) {
    if (getFiltreRechercheParam('filter_type') == 'SANS') {
        $query->useAbsenceEleveSaisieQuery()->useJTraitementSaisieEleveQuery()->useAbsenceEleveTraitementQuery()->filterByATypeId(null)->endUse()->endUse()->endUse();
    } else {
        $query->useAbsenceEleveSaisieQuery()->useJTraitementSaisieEleveQuery()->useAbsenceEleveTraitementQuery()->filterByATypeId(getFiltreRechercheParam('filter_type'))->endUse()->endUse()->endUse();
    }
}
if (isFiltreRechercheParam('filter_manqement_obligation')) {
    if (getFiltreRechercheParam('filter_manqement_obligation')=='y') {
        //on commence par filter certain élèves
        $query_clone = clone $query;
        $array_eleve_id = $query_clone->distinct()->select('Id')->find();
        
        $saisie_manque_col = AbsenceEleveSaisieQuery::create()->where('AbsenceEleveSaisie.EleveId IN ?', $array_eleve_id)->filterByManquementObligationPresence()->select('Id')->find()->toKeyValue('Id', 'Id');
        $query->useAbsenceEleveSaisieQuery()->filterById($saisie_manque_col)->endUse();
    }
    unset($saisie_manque_col);
}
if (isFiltreRechercheParam('filter_motif')) {
    if (getFiltreRechercheParam('filter_motif') == 'SANS') {
        $query->useAbsenceEleveSaisieQuery()->useJTraitementSaisieEleveQuery()->useAbsenceEleveTraitementQuery('b', 'left join')->filterByAMotifId(null)->endUse()->endUse()->endUse();
    } else {
        $query->useAbsenceEleveSaisieQuery()->useJTraitementSaisieEleveQuery()->useAbsenceEleveTraitementQuery()->filterByAMotifId(getFiltreRechercheParam('filter_motif'))->endUse()->endUse()->endUse();
    }
}
if (isFiltreRechercheParam('filter_justification')) {
    if (getFiltreRechercheParam('filter_justification') == 'SANS') {
        //on commence par filter certain élèves
        $query_clone = clone $query;
        $array_eleve_id = $query_clone->distinct()->select('Id')->find();
        
        //on filtre les saisies pour trouver celles qui ne sont pas justifiées
        $absences_saisie_query1 = new AbsenceEleveSaisieQuery();
        $absences_saisie_query1->where('AbsenceEleveSaisie.EleveId IN ?', $array_eleve_id)->useJTraitementSaisieEleveQuery('ab', 'left join')->useAbsenceEleveTraitementQuery('ad', 'left join')->endUse()->endUse()
                ->groupBy('Id')->withColumn('count(ad.a_justification_id)', 'nbJustif');
        $absences_saisie_query = new AbsenceEleveSaisieQuery();
        $absences_saisie_query->addSelectQuery($absences_saisie_query1, 'justif')->where('justif.nbJustif = 0')->where('justif.EleveId IN ?', $array_eleve_id);
        $absences_saisie_query->distinct()->select('Id');
        $array_absence_id = $absences_saisie_query->find();
        
        //on filtre la requete principale avec les saisies précédentes
        $query->useAbsenceEleveSaisieQuery()->where('AbsenceEleveSaisie.Id IN ?', $array_absence_id)->endUse();
    } else {
        $query->useAbsenceEleveSaisieQuery()->useJTraitementSaisieEleveQuery()->useAbsenceEleveTraitementQuery()->filterByAJustificationId(getFiltreRechercheParam('filter_justification'))->endUse()->endUse()->endUse();
    }
}

if (getFiltreRechercheParam('order') == "asc_id") {
    $query->orderBy('Id', Criteria::ASC);
} else if (getFiltreRechercheParam('order') == "des_id") {
    $query->orderBy('Id', Criteria::DESC);
} else if (getFiltreRechercheParam('order') == "asc_nom") {
    $query->orderBy('Nom', Criteria::ASC);
    $query->orderBy('Prenom', Criteria::ASC);
} else if (getFiltreRechercheParam('order') == "des_nom") {
    $query->orderBy('Nom', Criteria::DESC);
    $query->orderBy('Prenom', Criteria::DESC);
} else if (getFiltreRechercheParam('order') == "asc_classe") {
    $query->useJEleveClasseQuery()->useClasseQuery()->orderBy('NomComplet', Criteria::ASC)->endUse()->endUse();
} else if (getFiltreRechercheParam('order') == "des_classe") {
    $query->useJEleveClasseQuery()->useClasseQuery()->orderBy('NomComplet', Criteria::DESC)->endUse()->endUse();
} else if (getFiltreRechercheParam('order') == "asc_regime") {
    $query->useEleveRegimeDoublantQuery()->orderByRegime(Criteria::ASC)->endUse();
} else if (getFiltreRechercheParam('order') == "des_regime") {
    $query->useEleveRegimeDoublantQuery()->orderByRegime(Criteria::DESC)->endUse();
} else if (getFiltreRechercheParam('order') == "asc_type") {
    $query->orderBy('ATypeId', Criteria::ASC);
} else if (getFiltreRechercheParam('order') == "des_type") {
    $query->orderBy('ATypeId', Criteria::DESC);
} else if (getFiltreRechercheParam('order') == "asc_motif") {
    $query->orderBy('AMotifId', Criteria::ASC);
} else if (getFiltreRechercheParam('order') == "des_motif") {
    $query->orderBy('AMotifId', Criteria::DESC);
} else if (getFiltreRechercheParam('order') == "asc_justification") {
    $query->orderBy('AJustificationId', Criteria::ASC);
} else if (getFiltreRechercheParam('order') == "des_justification") {
    $query->orderBy('AJustificationId', Criteria::DESC);
} else if (getFiltreRechercheParam('order') == "asc_date_creation") {
    $query->orderBy('CreatedAt', Criteria::ASC);
} else if (getFiltreRechercheParam('order') == "des_date_creation") {
    $query->orderBy('CreatedAt', Criteria::DESC);
} else if (getFiltreRechercheParam('order') == "asc_date_modification") {
    $query->orderBy('UpdatedAt', Criteria::ASC);
} else if (getFiltreRechercheParam('order') == "des_date_modification") {
    $query->orderBy('UpdatedAt', Criteria::DESC);
} else if (getFiltreRechercheParam('order') == "asc_notification") {
    $query->leftJoinAbsenceEleveNotification()->orderBy('AbsenceEleveNotification.StatutEnvoi', Criteria::ASC);
} else if (getFiltreRechercheParam('order') == "des_notification") {
    $query->leftJoinAbsenceEleveNotification()->orderBy('AbsenceEleveNotification.StatutEnvoi', Criteria::DESC);
}

$query->distinct();
$reuse_later_query = clone $query;

$eleves_col = $query->paginate($page_number, $item_per_page);
$nb_pages = (floor($eleves_col->getNbResults() / $item_per_page) + 1);
if ($page_number > $nb_pages) {
    $page_number = $nb_pages;
}
$results = $eleves_col->getResults();

require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

if(!$menu){
    include('menu_abs2.inc.php');
    include('menu_bilans.inc.php');
}

echo "<div class='css-panes' id='containDiv' style=''>\n";

echo '<form method="post" action="liste_eleves.php" id="liste_eleves">';
echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
  echo "<p>";
  
if ($eleves_col->haveToPaginate()) {
    echo "Page ";
    echo '<input type="submit" name="page_deplacement" value="-"/>';
    echo '<input type="text" name="page_number" size="1" value="'.$page_number.'"/>';
    echo '<input type="submit" name="page_deplacement" value="+"/> ';
    echo "sur ".$nb_pages." page(s) ";
    echo "| ";
}
echo "Voir ";
echo '<input type="text" name="item_per_page" size="1" value="'.$item_per_page.'"/>';
echo "par page|  Nombre d'élèves : ";
echo $eleves_col->count();

echo "&nbsp;&nbsp;&nbsp;";
echo '<button type="submit">Rechercher</button>';
echo '<button type="submit" name="reinit_filtre" value="y" >Reinitialiser les filtres</button> ';
echo "</p>";

echo '<table id="table_liste_absents" class="tb_absences" style="border-spacing:0; width:100%">';

echo '<thead>';
echo '<tr>';

$order = getFiltreRechercheParam('order');
//en tete filtre id
echo '<th>';
//echo '<nobr>';
echo '<input type="hidden" name="order" value="'.$order.'" />'; 
echo '<span style="white-space: nowrap;"> ';
echo '<input type="image" src="../images/up.png" title="monter" style="vertical-align: middle;width:15px; height:15px; ';
if ($order == "asc_id") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_id" onclick="this.form.order.value = this.value"/>';
echo '<input type="image" src="../images/down.png" title="descendre" style="vertical-align: middle;width:15px; height:15px; ';
if ($order == "des_id") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_id" onclick="this.form.order.value = this.value"/>';
//echo '</nobr> ';
echo '</span>';
echo '<br/> ';
echo 'N°';
echo '<input type="text" name="filter_eleve_id" value="'.getFiltreRechercheParam('filter_eleve_id').'" size="3"/>';
echo '</th>';

//en tete filtre nom prenom
echo '<th>';
//echo '<nobr>';
echo '<span style="white-space: nowrap;"> ';
echo 'Élève';
echo '<input type="image" src="../images/up.png" title="monter" style="vertical-align: middle;width:15px; height:15px; ';
if ($order == "asc_nom") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_nom" onclick="this.form.order.value = this.value"/>';
echo '<input type="image" src="../images/down.png" title="descendre" style="vertical-align: middle;width:15px; height:15px; ';
if ($order == "des_nom") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_nom" onclick="this.form.order.value = this.value"/>';
//echo '</nobr>';
echo '</span>';
echo '<br /><input type="text" name="filter_eleve" value="'.getFiltreRechercheParam('filter_eleve').'" size="8"/>';
echo '</th>';

//en tete filtre classe
echo '<th>';
echo '<span style="white-space: nowrap;"> ';
//echo '<nobr>';
echo 'Classe';
echo '<input type="image" src="../images/up.png" title="monter" style="vertical-align: middle;width:15px; height:15px; ';
if ($order == "asc_classe") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_classe" onclick="this.form.order.value = this.value"/>';
echo '<input type="image" src="../images/down.png" title="descendre" style="vertical-align: middle;width:15px; height:15px; ';
if ($order == "des_classe") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_classe" onclick="this.form.order.value = this.value"/>';
echo '</span>';
//echo '</nobr>';
echo '<br />';
echo ("<select name=\"filter_classe\" onchange='submit()'>");
echo "<option value=''></option>\n";
foreach (ClasseQuery::create()->orderByNom()->orderByNomComplet()->distinct()->find() as $classe) {
	echo "<option value='".$classe->getId()."'";
	if (getFiltreRechercheParam('filter_classe') === (string) $classe->getId()) echo " selected='selected' ";
	echo ">";
	echo $classe->getNom();
	echo "</option>\n";
}
echo "</select>";
echo '</th>';

//en tete filtre qualité demi-pension
echo '<th>';
echo '<span style="white-space: nowrap;"> ';
//echo '<nobr>';
echo 'Régime';
echo '<input type="image" src="../images/up.png" title="monter" style="vertical-align: middle;width:15px; height:15px; ';
if ($order == "asc_regime") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_regime" onclick="this.form.order.value = this.value"/>';
echo '<input type="image" src="../images/down.png" title="descendre" style="vertical-align: middle;width:15px; height:15px; ';
if ($order == "des_regime") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_regime" onclick="this.form.order.value = this.value"/>';
echo '</span>';
//echo '</nobr>';
echo '<br />';
echo ("<select name=\"filter_regime\" onchange='submit()'>");
echo "<option value=''></option>\n";
echo "<option value='d/p'";
if (getFiltreRechercheParam('filter_regime') == 'd/p') echo " selected='selected' ";
echo ">d/p</option>\n";
echo "<option value='ext.'";
if (getFiltreRechercheParam('filter_regime') == 'ext.') echo " selected='selected' ";
echo ">ext.</option>\n";
echo "<option value='int.'";
if (getFiltreRechercheParam('filter_regime') == 'int.') echo " selected='selected' ";
echo ">int.</option>\n";
echo "<option value='i-e'";
if (getFiltreRechercheParam('filter_regime') == 'i-e') echo " selected='selected' ";
echo ">i-e</option>\n";
echo "</select>";
echo '</th>';

//en tete filtre date debut
echo '<th>';
//echo '<nobr>';
echo '<span style="white-space: nowrap;"> ';
echo 'Date début saisie';
echo '</span>';
echo '<br />';
//echo '<nobr>';
echo '<span style="white-space: nowrap;"> ';
echo 'Entre : <input size="13" id="filter_date_debut_saisie_debut_plage" name="filter_date_debut_saisie_debut_plage" value="';
if (isFiltreRechercheParam('filter_date_debut_saisie_debut_plage')) {echo getFiltreRechercheParam('filter_date_debut_saisie_debut_plage');}
echo '" />&nbsp;';
echo '<img id="trigger_filter_date_debut_saisie_debut_plage" src="../images/icons/calendrier.gif" alt="" />';
//echo '</nobr>';
echo '</span>';
echo '
<script type="text/javascript">
    Calendar.setup({
	inputField     :    "filter_date_debut_saisie_debut_plage",     // id of the input field
	ifFormat       :    "%d/%m/%Y %H:%M",      // format of the input field
	button         :    "trigger_filter_date_debut_saisie_debut_plage",  // trigger for the calendar (button ID)
	align          :    "Tl",           // alignment (defaults to "Bl")
	singleClick    :    true,
	showsTime	:   true
    });
</script>';
echo '<br />';
//echo '<nobr>';
echo '<span style="white-space: nowrap;"> ';
echo 'Et : <input size="13" id="filter_date_debut_saisie_fin_plage" name="filter_date_debut_saisie_fin_plage" value="';
if (isFiltreRechercheParam('filter_date_debut_saisie_fin_plage')) {echo getFiltreRechercheParam('filter_date_debut_saisie_fin_plage');}
echo '" />&nbsp;';
echo '<img id="trigger_filter_date_debut_saisie_fin_plage" src="../images/icons/calendrier.gif" alt="" />';
//echo '</nobr>';
echo '</span>';
echo '
<script type="text/javascript">
    Calendar.setup({
	inputField     :    "filter_date_debut_saisie_fin_plage",     // id of the input field
	ifFormat       :    "%d/%m/%Y %H:%M",      // format of the input field
	button         :    "trigger_filter_date_debut_saisie_fin_plage",  // trigger for the calendar (button ID)
	align          :    "Tl",           // alignment (defaults to "Bl")
	singleClick    :    true,
	showsTime	:   true
    });
</script>';
echo '</th>';

//en tete filtre date fin
echo '<th>';
//echo '<nobr>';
echo '<span style="white-space: nowrap;"> ';
echo 'Date fin saisie';
echo '</span>';
echo '<br />';
//echo '<nobr>';
echo '<span style="white-space: nowrap;"> ';
echo 'Entre : <input size="13" id="filter_date_fin_saisie_debut_plage" name="filter_date_fin_saisie_debut_plage" value="';
if (isFiltreRechercheParam('filter_date_fin_saisie_debut_plage')) {echo getFiltreRechercheParam('filter_date_fin_saisie_debut_plage');}
echo '" />&nbsp;';
echo '<img id="trigger_filter_date_fin_saisie_debut_plage" src="../images/icons/calendrier.gif" alt="" />';
//echo '</nobr>';
echo '</span>';
echo '
<script type="text/javascript">
    Calendar.setup({
	inputField     :    "filter_date_fin_saisie_debut_plage",     // id of the input field
	ifFormat       :    "%d/%m/%Y %H:%M",      // format of the input field
	button         :    "trigger_filter_date_fin_saisie_debut_plage",  // trigger for the calendar (button ID)
	align          :    "Tl",           // alignment (defaults to "Bl")
	singleClick    :    true,
	showsTime	:   true
    });
</script>';
echo '<br />';
//echo '<nobr>';
echo '<span style="white-space: nowrap;"> ';
echo 'Et : <input size="13" id="filter_date_fin_saisie_fin_plage" name="filter_date_fin_saisie_fin_plage" value="';
if (isFiltreRechercheParam('filter_date_fin_saisie_fin_plage')) {echo getFiltreRechercheParam('filter_date_fin_saisie_fin_plage');}
echo '" />&nbsp;';
echo '<img id="trigger_filter_date_fin_saisie_fin_plage" src="../images/icons/calendrier.gif" alt="" />';
//echo '</nobr>';
echo '</span>';
echo '
<script type="text/javascript">
    Calendar.setup({
	inputField     :    "filter_date_fin_saisie_fin_plage",     // id of the input field
	ifFormat       :    "%d/%m/%Y %H:%M",      // format of the input field
	button         :    "trigger_filter_date_fin_saisie_fin_plage",  // trigger for the calendar (button ID)
	align          :    "Tl",           // alignment (defaults to "Bl")
	singleClick    :    true,
	showsTime	:   true
    });
</script>';
echo '</th>';

//en tete type d'absence
echo '<th>';
echo '<span style="white-space: nowrap;"> ';
echo 'Type';
echo '</span>';
echo '<br />';
echo ("<select name=\"filter_type\" onchange='submit()'>");
echo "<option value=''></option>\n";
echo "<option value='SANS'";
if (getFiltreRechercheParam('filter_type') == 'SANS') echo " selected='selected' ";
echo ">SANS TYPE</option>\n";
foreach (AbsenceEleveTypeQuery::create()->orderBySortableRank()->find() as $type) {
	echo "<option value='".$type->getId()."'";
	if (getFiltreRechercheParam('filter_type') === (string) $type->getId()) echo " selected='selected' ";
	echo ">";
	echo $type->getNom();
	echo "</option>\n";
}
echo "</select>";
echo '</th>';

//en tete filtre manqement_obligation
echo '<th>';
echo ("<select name=\"filter_manqement_obligation\" onchange='submit()'>");
echo "<option value=''";
if (!isFiltreRechercheParam('filter_manqement_obligation')) {echo "selected='selected'";}
echo "></option>\n";
echo "<option value='y' ";
if (getFiltreRechercheParam('filter_manqement_obligation') == 'y') {echo "selected='selected'";}
echo ">oui</option>\n";
echo "</select>";
echo '<br/>Manquement obligation présence';
echo '</th>';

//en tete justification d'absence
echo '<th>';
echo 'Justification';
echo '<br />';
echo ("<select name=\"filter_justification\" onchange='submit()'>");
echo "<option value=''></option>\n";
echo "<option value='SANS'";
if (getFiltreRechercheParam('filter_justification') == 'SANS') echo " selected='selected' ";
echo ">";
echo 'SANS JUSTIFICATION';
echo "</option>\n";
foreach (AbsenceEleveJustificationQuery::create()->orderByRank()->find() as $justification) {
	echo "<option value='".$justification->getId()."'";
	if (getFiltreRechercheParam('filter_justification') === (string) $justification->getId()) echo " selected='selected' ";
	echo ">";
	echo $justification->getNom();
	echo "</option>\n";
}
echo "</select>";
echo '</th>';

//en tete motif d'absence
echo '<th>';
echo 'Motif';
echo '<br />';
echo ("<select name=\"filter_motif\" onchange='submit()'>");
echo "<option value=''></option>\n";
echo "<option value='SANS'";
if (getFiltreRechercheParam('filter_motif') == 'SANS') echo " selected='selected' ";
echo ">";
echo 'SANS MOTIF';
echo "</option>\n";
foreach (AbsenceEleveMotifQuery::create()->orderByRank()->find() as $motif) {
	echo "<option value='".$motif->getId()."'";
	if (getFiltreRechercheParam('filter_motif') === (string) $motif->getId()) echo " selected='selected' ";
	echo ">";
	echo $motif->getNom();
	echo "</option>\n";
}
echo "</select>";
echo '</th>';

echo '</tr>';
echo '</thead>';

echo '<tbody>';

foreach ($results as $eleve) {
    //$traitement = new AbsenceEleveTraitement();
    if ($results->isOdd()) {
	    $background_couleur="rgb(220, 220, 220);";
    } else {
	    $background_couleur="rgb(210, 220, 230);";
    }

    echo "<tr style='background-color :$background_couleur'>\n";

    //donnees id
    echo '<td>';
    echo $eleve->getId();
    echo '</td>';

    //nom eleve
    echo '<td>';
    echo "<a href='liste_eleves.php?filter_eleve=".$eleve->getNom()."&order=asc_eleve' style='display: block; height: 100%;'> ";
    echo ($eleve->getCivilite().' '.$eleve->getNom().' '.$eleve->getPrenom());
    echo "</a>";
    if ($utilisateur->getAccesFicheEleve($eleve)) {
        echo "<a href='../eleves/visu_eleve.php?ele_login=".$eleve->getLogin()."&amp;onglet=responsables&amp;quitter_la_page=y' target='_blank'>";
        //echo "<a href='../eleves/visu_eleve.php?ele_login=".$eleve->getLogin()."' >";
        echo ' (voir fiche)';
        echo "</a>";
    }
    echo "</td>";
    
    //donnees classe
    echo '<td>';
    echo $eleve->getClasseNom();
    echo '</td>';

    //donnees régime
    echo '<td>';
    $reg = $eleve->getEleveRegimeDoublant();
    if ($reg != null) echo $reg->getRegime();
    echo '</td>';

    //date saisie
    echo '<td colspan=2>';
    $query_eleve_hydration = clone $reuse_later_query;
    $query_eleve_hydration->filterById($eleve->getId());
    $query_eleve_hydration->joinWith('Eleve.AbsenceEleveSaisie', 'LEFT JOIN')->joinWith('AbsenceEleveSaisie.JTraitementSaisieEleve', 'LEFT JOIN')->joinWith('JTraitementSaisieEleve.AbsenceEleveTraitement', 'LEFT JOIN');
    $query_eleve_hydration->useAbsenceEleveSaisieQuery()->filterByDeletedAt(null)->endUse();
    $eleve_saisie_hydrated = $query_eleve_hydration->find()->getFirst();
    echo $eleve_saisie_hydrated->getAbsenceEleveSaisies()->count();
    echo " saisie";
    if ($eleve_saisie_hydrated->getAbsenceEleveSaisies()->count() > 1) {
        echo "s";
    }
    echo '</td>';

    $type_col = new PropelObjectCollection();
    $justif_col = new PropelObjectCollection();
    $motif_col = new PropelObjectCollection();
    $manque = false;
    foreach ($eleve_saisie_hydrated->getAbsenceEleveSaisies() as $saisie) {
        foreach ($saisie->getAbsenceEleveTraitements() as $traitement) {
            $type_col->add($traitement->getAbsenceEleveType());
            $justif_col->add($traitement->getAbsenceEleveJustification());
            $motif_col->add($traitement->getAbsenceEleveMotif());
        }
        $manque = $manque || $saisie->getManquementObligationPresence();
    }

    //donnees type
    echo '<td>';
    foreach ($type_col as $type) {
        if ($type == null) continue;
        echo $type->getNom();
        if (!$type_col->isLast()) {
            echo ', ';
        }
    }
    echo '</td>';

    echo '<td>';
    if ($manque) {
	echo 'oui';
    } else {
	echo 'non';
    }
    echo '</td>';

    //donnees justif
    echo '<td>';
    foreach ($justif_col as $justif) {
        if ($justif == null) continue;
        echo $justif->getNom();
        if (!$justif_col->isLast()) {
            echo ', ';
        }
    }
    echo "</a>";
    
    //donnees motif
    echo '<td>';
    foreach ($motif_col as $motif) {
        if ($motif == null) continue;
        echo $motif->getNom();
        if (!$motif_col->isLast()) {
            echo ', ';
        }
    }
    echo "</a>";
    
    echo '</tr>';
}

echo '</tbody>';
//echo '</tbody>';

echo '</table>';

echo '</form>';

echo "</div>\n";

require_once("../lib/footer.inc.php");


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
