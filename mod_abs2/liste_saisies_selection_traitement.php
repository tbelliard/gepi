<?php
/**
 *
 *
 * Copyright 2010 Josselin Jacquard
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
 * but WIthOUT ANY WARRANTY; without even the implied warranty of
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

if (isset($_POST["creation_traitement"]) || isset($_POST["ajout_traitement"])) {
    include('creation_traitement.php');
}

if (isset($_POST["suppression_saisies"])) {
    AbsenceEleveSaisiePeer::disableVersioning();
    $saisieCol = AbsenceEleveSaisieQuery::create()->filterByPrimaryKeys($_POST["select_saisie"])->find();
    foreach($saisieCol as $saisie) {
    	$saisie->delete();
    }
	AbsenceEleveSaisiePeer::enableVersioning();
} else if (isset($_POST["restauration_saisies"])) {
    AbsenceEleveSaisiePeer::disableVersioning();
    $saisieCol = AbsenceEleveSaisieQuery::create()->includeDeleted()->filterByPrimaryKeys($_POST["select_saisie"])->find();
    foreach($saisieCol as $saisie) {
    	$saisie->unDelete();
    }
    AbsenceEleveSaisiePeer::enableVersioning();
}

include('include_requetes_filtre_de_recherche.php');

include('include_pagination.php');

$menu = isset($_POST["menu"]) ? $_POST["menu"] :(isset($_GET["menu"]) ? $_GET["menu"] : NULL);
//==============================================
$style_specifique[] = "mod_abs2/lib/abs_style";
$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";
$javascript_specifique[] = "mod_abs2/lib/include";
if(!$menu){
    $titre_page = "Les absences";
}
$utilisation_jsdivdrag = "non";
$_SESSION['cacher_header'] = "y";
$dojo = true;
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

if(!$menu){
   include('menu_abs2.inc.php'); 
}

echo "<div class='css-panes' style='background-color:#cae7cb;' id='containDiv' style='overflow : none; float : left; margin-top : -1px; border-width : 1px;'>\n";


$query = AbsenceEleveSaisieQuery::create();
if(isset($_GET['saisies'])){
    $saisies=unserialize($_GET['saisies']);
    //on reinitialise les filtres au besoin
    $_SESSION['filtre_recherche'] = Array();
    $_SESSION['filtre_recherche']['order'] = 'des_id';
    $query->filterById($saisies);
}
//$query->leftJoin('AbsenceEleveSaisie.JTraitementSaisieEleve')->leftJoin('JTraitementSaisieEleve.AbsenceEleveTraitement')->with('AbsenceEleveTraitement');
if (isFiltreRechercheParam('filter_saisie_id')) {
    $query->filterById(getFiltreRechercheParam('filter_saisie_id'));    
}
if (isFiltreRechercheParam('filter_utilisateur')) {
    $query->useUtilisateurProfessionnelQuery()->filterByNom('%'.getFiltreRechercheParam('filter_utilisateur').'%', Criteria::LIKE)->endUse();
}
if (isFiltreRechercheParam('filter_eleve')) {
    $query->useEleveQuery()->filterByNomOrPrenomLike(getFiltreRechercheParam('filter_eleve'))->endUse();
}
if (isFiltreRechercheParam('filter_marqueur_appel')) {
    $query->filterByEleveId(null);
}
if (isFiltreRechercheParam('filter_classe')) {
    $query->leftJoin('AbsenceEleveSaisie.Eleve');
    $query->leftJoin('Eleve.JEleveClasse');
    $query->condition('cond1', 'JEleveClasse.IdClasse = ?', getFiltreRechercheParam('filter_classe'));
    $query->condition('cond2', 'AbsenceEleveSaisie.IdClasse = ?', getFiltreRechercheParam('filter_classe'));
    $query->where(array('cond1', 'cond2'), 'or');
}
if (isFiltreRechercheParam('filter_groupe')) {
    $query->leftJoin('AbsenceEleveSaisie.Eleve');
    $query->leftJoin('Eleve.JEleveGroupe');
    $query->condition('cond1', 'JEleveGroupe.IdGroupe = ?', getFiltreRechercheParam('filter_groupe'));
    $query->condition('cond2', 'AbsenceEleveSaisie.IdGroupe = ?', getFiltreRechercheParam('filter_groupe'));
    $query->where(array('cond1', 'cond2'), 'or');
}
if (isFiltreRechercheParam('filter_aid')) {
    $query->leftJoin('AbsenceEleveSaisie.Eleve');
    $query->leftJoin('Eleve.JAidEleves');
    $query->condition('cond1', 'JAidEleves.IdAid = ?', getFiltreRechercheParam('filter_aid'));
    $query->condition('cond2', 'AbsenceEleveSaisie.IdAid = ?', getFiltreRechercheParam('filter_aid'));
    $query->where(array('cond1', 'cond2'), 'or');
}
if (isFiltreRechercheParam('filter_date_debut_saisie_debut_plage')) {
    $date_debut_saisie_debut_plage = new DateTime(str_replace("/",".",getFiltreRechercheParam('filter_date_debut_saisie_debut_plage')));
    $query->filterByDebutAbs($date_debut_saisie_debut_plage, Criteria::GREATER_EQUAL);
}
if (isFiltreRechercheParam('filter_date_debut_saisie_fin_plage')) {
    $date_debut_saisie_fin_plage = new DateTime(str_replace("/",".",getFiltreRechercheParam('filter_date_debut_saisie_fin_plage')));
    $query->filterByDebutAbs($date_debut_saisie_fin_plage, Criteria::LESS_EQUAL);
}
if (isFiltreRechercheParam('filter_date_fin_saisie_debut_plage')) {
    $date_fin_absence_debut_plage = new DateTime(str_replace("/",".",getFiltreRechercheParam('filter_date_fin_saisie_debut_plage')));
    $query->filterByFinAbs($date_fin_absence_debut_plage, Criteria::GREATER_EQUAL);
}
if (isFiltreRechercheParam('filter_date_fin_saisie_fin_plage')) {
    $date_fin_absence_fin_plage = new DateTime(str_replace("/",".",getFiltreRechercheParam('filter_date_fin_saisie_fin_plage')));
    $query->filterByFinAbs($date_fin_absence_fin_plage, Criteria::LESS_EQUAL);
}
if (isFiltreRechercheParam('filter_creneau')) {
    $query->filterByIdEdtCreneau(getFiltreRechercheParam('filter_creneau'));
}
if (isFiltreRechercheParam('filter_cours')) {
    $query->filterByIdEdtEmplacementCours(getFiltreRechercheParam('filter_cours'));
}
if (isFiltreRechercheParam('filter_date_creation_saisie_debut_plage')) {
    $date_creation_saisie_debut_plage = new DateTime(str_replace("/",".",getFiltreRechercheParam('filter_date_creation_saisie_debut_plage')));
    $query->filterByCreatedAt($date_creation_saisie_debut_plage, Criteria::GREATER_EQUAL);
}
if (isFiltreRechercheParam('filter_date_creation_saisie_fin_plage')) {
    $date_creation_saisie_fin_plage = new DateTime(str_replace("/",".",getFiltreRechercheParam('filter_date_creation_saisie_fin_plage')));
    $query->filterByCreatedAt($date_creation_saisie_fin_plage, Criteria::LESS_EQUAL);
}
if (isFiltreRechercheParam('filter_date_modification')) {
    $query->where('AbsenceEleveSaisie.CreatedAt != AbsenceEleveSaisie.UpdatedAt');
}
if (isFiltreRechercheParam('filter_date_traitement_absence_debut_plage')) {
    $date_traitement_absence_debut_plage = new DateTime(str_replace("/",".",getFiltreRechercheParam('filter_date_traitement_absence_debut_plage')));
    $query->leftJoin('AbsenceEleveSaisie.JTraitementSaisieEleve')->leftJoin('JTraitementSaisieEleve.AbsenceEleveTraitement')->where('AbsenceEleveTraitement.UpdatedAt >= ?', $date_traitement_absence_debut_plage);
}
if (isFiltreRechercheParam('filter_date_traitement_absence_fin_plage')) {
    $date_traitement_absence_fin_plage = new DateTime(str_replace("/",".",getFiltreRechercheParam('filter_date_traitement_absence_fin_plage')));
    $query->leftJoin('AbsenceEleveSaisie.JTraitementSaisieEleve')->leftJoin('JTraitementSaisieEleve.AbsenceEleveTraitement');
    $query->condition('trait1', 'AbsenceEleveTraitement.UpdatedAt <= ?', $date_traitement_absence_fin_plage);
    $query->condition('trait2', 'AbsenceEleveTraitement.UpdatedAt IS NULL');
    $query->where(array('trait1', 'trait2'), 'or');
}
if (isFiltreRechercheParam('filter_discipline')) {
    $query->filterByIdSIncidents(null, Criteria::NOT_EQUAL);
    $query->filterByIdSIncidents(-1, Criteria::NOT_EQUAL);
}
if (isFiltreRechercheParam('filter_type')) {
    if (getFiltreRechercheParam('filter_type') == 'SANS') {
	$query->groupById()
	    ->useJTraitementSaisieEleveQuery('', Criteria::LEFT_JOIN)
	    ->useAbsenceEleveTraitementQuery('', Criteria::LEFT_JOIN)
	    ->endUse()->endUse()
	    ->withColumn('group_concat(a_traitements.A_TYPE_ID)', 'a_types_id_concat');
	$criteria = new Criteria();
	$c = $criteria->getNewCriterion('a_types_id_concat', null, Criteria::ISNULL);
	$query->addHaving($c);
    } else {
	$query->useJTraitementSaisieEleveQuery()->useAbsenceEleveTraitementQuery()->filterByATypeId(getFiltreRechercheParam('filter_type'))->endUse()->endUse();
    }
}
if (isFiltreRechercheParam('filter_manqement_obligation')) {
    $query->filterByManquementObligationPresence(getFiltreRechercheParam('filter_manqement_obligation')=='y');
}
if (getFiltreRechercheParam('filter_saisies_supprimees')=='y') {
	$query->includeDeleted()->filterByDeletedAt(null,Criteria::ISNOTNULL);
	if (isFiltreRechercheParam('filter_date_suppression_saisie_debut_plage')) {
	    $date_suppression_saisie_debut_plage = new DateTime(str_replace("/",".",getFiltreRechercheParam('filter_date_suppression_saisie_debut_plage')));
	    $query->filterByDeletedAt($date_suppression_saisie_debut_plage, Criteria::GREATER_EQUAL);
	}
	if (isFiltreRechercheParam('filter_date_suppression_saisie_fin_plage')) {
	    $date_suppression_saisie_fin_plage = new DateTime(str_replace("/",".",getFiltreRechercheParam('filter_date_suppression_saisie_fin_plage')));
	    $query->filterByDeletedAt($date_suppression_saisie_fin_plage, Criteria::LESS_EQUAL);
	}
}

//on va filtrer sur les saisies possiblement rattachées à un traitement
$recherche_saisie_a_rattacher = getFiltreRechercheParam('filter_recherche_saisie_a_rattacher');
//récupération des paramètres de la requète
$id_traitement = isset($_POST["id_traitement"]) ? $_POST["id_traitement"] :(isset($_GET["id_traitement"]) ? $_GET["id_traitement"] :(isset($_SESSION["id_traitement"]) ? $_SESSION["id_traitement"] : NULL));
if (isset($id_traitement) && $id_traitement != null) $_SESSION['id_traitement'] = $id_traitement;
$traitement = AbsenceEleveTraitementQuery::create()->findPk($id_traitement);
if ($recherche_saisie_a_rattacher == 'oui' && $traitement != null) {
    $date_debut = null;
    $date_fin = null;
    $id_eleve_array = null;
    $id_saisie_array = null;
    foreach ($traitement->getAbsenceEleveSaisies() as $saisie) {//$saisie = new AbsenceEleveSaisie();
	if ($date_debut == null || $saisie->getDebutAbs('U') < $date_debut->format('U')) {
	    $date_debut = clone $saisie->getDebutAbs(null);
	}
	if ($date_fin == null || $saisie->getFinAbs('U') > $date_fin->format('U')) {
	    $date_fin = clone $saisie->getFinAbs(null);
	}
	$id_eleve_array[] = $saisie->getEleveId();
	$id_saisie_array[] = $saisie->getId();
    }
    if ($date_debut != null) date_date_set($date_debut, $date_debut->format('Y'), $date_debut->format('m'), $date_debut->format('d') - 1);
    if ($date_fin != null) date_date_set($date_fin, $date_fin->format('Y'), $date_fin->format('m'), $date_fin->format('d') + 1);
    $query->filterByPlageTemps($date_debut, $date_fin)->filterByEleveId($id_eleve_array)->filterById($id_saisie_array, Criteria::NOT_IN);
}


$order = getFiltreRechercheParam('order');
if ($order == "asc_id") {
    $query->orderBy('Id', Criteria::ASC);
} else if ($order == "des_id") {
    $query->orderBy('Id', Criteria::DESC);
} else if ($order == "asc_utilisateur") {
    $query->useUtilisateurProfessionnelQuery()->orderBy('Nom', Criteria::ASC)->endUse();
} else if ($order == "des_utilisateur") {
    $query->useUtilisateurProfessionnelQuery()->orderBy('Nom', Criteria::DESC)->endUse();
} else if ($order == "asc_eleve") {
    $query->useEleveQuery()->orderBy('Nom', Criteria::ASC)->orderBy('Prenom', Criteria::ASC)->endUse();
} else if ($order == "des_eleve") {
    $query->useEleveQuery()->orderBy('Nom', Criteria::DESC)->orderBy('Prenom', Criteria::DESC)->endUse();
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
} else if (getFiltreRechercheParam('filter_saisies_supprimees') == 'y') {
    if ($order == "asc_date_suppression") {
		$query->orderBy('DeletedAt', Criteria::ASC);
	} else if ($order == "des_date_suppression") {
		$query->orderBy('DeletedAt', Criteria::DESC);
	}
} else {
	$query->orderBy('Id', Criteria::DESC);
}

$query->distinct();
$query_count_eleve = clone $query;
$total_eleve = $query_count_eleve->groupByEleveId()->count();

$saisies_col = $query->paginate($page_number, $item_per_page);

$nb_pages = (floor($saisies_col->getNbResults() / $item_per_page) + 1);
if ($page_number > $nb_pages) {
    $page_number = $nb_pages;
}

if (isset($message_erreur_traitement)) {
    echo $message_erreur_traitement;
}

echo '<form method="post" action="liste_saisies_selection_traitement.php" name="liste_saisies" id="liste_saisies">';
 echo '<input type="hidden" name="menu" value="'.$menu.'"/>';
?>    <div id="action_bouton" dojoType="dijit.form.DropDownButton" style="display: inline">
		<span>Action</span>
	<div dojoType="dijit.Menu" style="display: inline">
	    <button type="submit" dojoType="dijit.MenuItem" onClick="document.liste_saisies.submit();">
		Rechercher
	    </button>
	    <button type="submit" name="reinit_filtre" value="y" dojoType="dijit.MenuItem" onClick="
		//Create an input type dynamically.
		var element = document.createElement('input');
		element.setAttribute('type', 'hidden');
		element.setAttribute('name', 'reinit_filtre');
		element.setAttribute('value', 'y');
		document.liste_saisies.appendChild(element);
		document.liste_saisies.submit();
				">
		Réinitialiser les filtres
	    </button>
	    <button type="submit" name="creation_traitement" value="yes" dojoType="dijit.MenuItem" onClick="
		//Create an input type dynamically.
		var element = document.createElement('input');
		element.setAttribute('type', 'hidden');
		element.setAttribute('name', 'creation_traitement');
		element.setAttribute('value', 'yes');
		document.liste_saisies.appendChild(element);
		document.liste_saisies.submit();"
		<?php
                if (getFiltreRechercheParam('filter_saisies_supprimees')=='y') echo'disabled'
                ?> >
		Creer un nouveau traitement
	    </button>
	    <?php if ($traitement != null) { ?>
	    <button type="submit" name="creation_traitement" value="yes" dojoType="dijit.MenuItem" onClick="
		//Create an input type dynamically.
		var element = document.createElement('input');
		element.setAttribute('type', 'hidden');
		element.setAttribute('name', 'ajout_traitement');
		element.setAttribute('value', 'yes');
		document.liste_saisies.appendChild(element);
		var element = document.createElement('input');
		element.setAttribute('type', 'hidden');
		element.setAttribute('name', 'id_traitement');
		element.setAttribute('value', '<?php echo $id_traitement ?>');
		document.liste_saisies.appendChild(element);
		document.liste_saisies.submit();
				">
		Ajouter les saisies au traitement 
		<?php 
	    $desc = $traitement->getDescription();
	    if (mb_strlen($desc)>300) {
	    	echo mb_substr($desc,0,300).' ... ';
	    } else {
	    	echo $desc;
	    }
		?>
	    </button>
	    <?php } 
	    if (getFiltreRechercheParam('filter_saisies_supprimees') != 'y') {
	    ?>
	    <button type="submit" name="suppression_saisies" value="yes" dojoType="dijit.MenuItem" onClick="
		//Create an input type dynamically.
		var element = document.createElement('input');
		element.setAttribute('type', 'hidden');
		element.setAttribute('name', 'suppression_saisies');
		element.setAttribute('value', 'yes');
		document.liste_saisies.appendChild(element);
		document.liste_saisies.submit();
				">
		Supprimer la selection
	    </button>
	    <?php } else { ?>
	    <button type="submit" name="restauration_saisies" value="yes" dojoType="dijit.MenuItem" onClick="
		//Create an input type dynamically.
		var element = document.createElement('input');
		element.setAttribute('type', 'hidden');
		element.setAttribute('name', 'restauration_saisies');
		element.setAttribute('value', 'yes');
		document.liste_saisies.appendChild(element);
		document.liste_saisies.submit();
				">
		Restaurer la selection
	    </button>
	    <?php } ?>
	</div>
    </div>
<script language="javascript">
   //on cache les boutons pas très jolis en attendant le parsing dojo
   dojo.byId("action_bouton").hide();
</script>
<?php
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
echo "par page |  Nbre de saisies : ";
echo $saisies_col->count();
echo " | Nbre d'élèves : ".$total_eleve;
echo ' | <input type="checkbox" name="filter_saisies_supprimees"  onchange="submit()" value="y"';
if (getFiltreRechercheParam('filter_saisies_supprimees') == 'y') {echo "checked='checked'";}
echo '/>';
if (getFiltreRechercheParam('filter_saisies_supprimees') == 'y') {echo '<font color="red">';}
echo 'Voir les saisies supprimées';
if (getFiltreRechercheParam('filter_saisies_supprimees') == 'y') {echo '</font>';}
if (getFiltreRechercheParam('filter_recherche_saisie_a_rattacher') == 'oui' && $traitement != null) {
    echo " | filtre actif : recherche de saisies a rattacher au traitement n° ";
    echo "<a href='./visu_traitement.php?id_traitement=".$traitement->getId()."";
    if($menu){
                echo"&menu=false";
            }
    echo "'>".$traitement->getId()."</a>";
}
echo '</p><p>';
if (isset($message_erreur_traitement)) {
    echo $message_erreur_traitement;
}
echo '</p>';
echo '<table id="table_liste_absents" class="tb_absences" style="border-spacing:0; width:100%; font-size:88%">';
echo '<thead>';
echo '<tr>';

echo '<th>';
echo '<div id="select_shortcut_buttons_container"/>';
echo '</th>';

//en tete filtre id
echo '<th>';
//echo '<nobr>';
echo '<input type="hidden" name="order" value="'.$order.'" />'; 
echo '<span style="white-space: nowrap;"> ';
echo '<input type="image" src="../images/up.png" title="monter" style="width:15px; height:15px;vertical-align: middle;';
if ($order == "asc_id") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_id" onclick="this.form.order.value = this.value"/>';
echo '<input type="image" src="../images/down.png" title="descendre" style="width:15px; height:15px;vertical-align: middle;';
if ($order == "des_id") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_id" onclick="this.form.order.value = this.value"/>';
//echo '</nobr> ';
echo '</span>';
echo '<br/> ';
echo 'N°';
echo '<input type="text" name="filter_saisie_id" value="'.getFiltreRechercheParam('filter_saisie_id').'" size="3"/>';
echo '</th>';

if (getFiltreRechercheParam('filter_saisies_supprimees') == 'y') {
	//en tete filtre date suppression
	echo '<th>';
	//echo '<nobr>';
	echo '<span style="white-space: nowrap;"> ';
	echo 'Date suppression';
	echo '<input type="image" src="../images/up.png" title="monter" style="width:15px; height:15px;vertical-align: middle;';
	if ($order == "asc_date_suppression") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
	echo 'border-width:1px;" alt="" name="order" value="asc_date_suppression"/ onclick="this.form.order.value = this.value">';
	echo '<input type="image" src="../images/down.png" title="descendre" style="width:15px; height:15px;vertical-align: middle;' ;
	if ($order == "des_date_suppression") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
	echo 'border-width:1px;" alt="" name="order" value="des_date_suppression"/ onclick="this.form.order.value = this.value">';
	//echo '</nobr>';
	echo '</span>';
	echo '<br />';
	//echo '<nobr>';
	echo '<span style="white-space: nowrap;"> ';
	echo 'Entre : <input size="13" id="filter_date_suppression_saisie_debut_plage" name="filter_date_suppression_saisie_debut_plage" value="';
	if (isFiltreRechercheParam('filter_date_suppression_saisie_debut_plage')) {echo getFiltreRechercheParam('filter_date_suppression_saisie_debut_plage');}
	echo '" />&nbsp;';
	echo '<img id="trigger_filter_date_suppression_saisie_debut_plage" src="../images/icons/calendrier.gif" alt="" />';
	//echo '</nobr>';
	echo '</span>';
	echo '
	<script type="text/javascript">
	    Calendar.setup({
		inputField     :    "filter_date_suppression_saisie_debut_plage",     // id of the input field
		ifFormat       :    "%d/%m/%Y %H:%M",      // format of the input field
		button         :    "trigger_filter_date_suppression_saisie_debut_plage",  // trigger for the calendar (button ID)
		align          :    "Tl",           // alignment (defaults to "Bl")
		singleClick    :    true,
		showsTime	:   true
	    });
	</script>';
	echo '<br />';
	//echo '<nobr>';
	echo '<span style="white-space: nowrap;"> ';
	echo 'Et : <input size="13" id="filter_date_suppression_saisie_fin_plage" name="filter_date_suppression_saisie_fin_plage" value="';
	if (isFiltreRechercheParam('filter_date_suppression_saisie_fin_plage')) {echo getFiltreRechercheParam('filter_date_suppression_saisie_fin_plage');}
	echo '" />&nbsp;';
	echo '<img id="trigger_filter_date_suppression_saisie_fin_plage" src="../images/icons/calendrier.gif" alt="" />';
	//echo '</nobr>';
	echo '</span>';
	echo '
	<script type="text/javascript">
	    Calendar.setup({
		inputField     :    "filter_date_suppression_saisie_fin_plage",     // id of the input field
		ifFormat       :    "%d/%m/%Y %H:%M",      // format of the input field
		button         :    "trigger_filter_date_suppression_saisie_fin_plage",  // trigger for the calendar (button ID)
		align          :    "Tl",           // alignment (defaults to "Bl")
		singleClick    :    true,
		showsTime	:   true
	    });
	</script>';
	echo '</th>';
}

//en tete filtre utilisateur
echo '<th>';
//echo '<nobr>';
echo '<span style="white-space: nowrap;"> ';
echo '<input type="image" src="../images/up.png"  title="monter" style="width:15px; height:15px;vertical-align: middle;';
if ($order == "asc_utilisateur") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_utilisateur" onclick="this.form.order.value = this.value"/>';
echo '<input type="image" src="../images/down.png" title="descendre" style="width:15px; height:15px;vertical-align: middle;';
if ($order == "des_utilisateur") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_utilisateur" onclick="this.form.order.value = this.value"/>';
//echo '</nobr>';
echo '</span>';
echo '<br />';
echo 'Utilisateur';
echo '<br /><input type="text" name="filter_utilisateur" value="'.getFiltreRechercheParam('filter_utilisateur').'" size="11"/>';
echo '</th>';

//en tete filtre eleve
echo '<th>';
//echo '<nobr>';
echo '<span style="white-space: nowrap;"> ';
echo '<input type="image" src="../images/up.png" title="monter" style="width:15px; height:15px;vertical-align: middle;';
if ($order == "asc_eleve") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_eleve" onclick="this.form.order.value = this.value"/>';
echo '<input type="image" src="../images/down.png" title="descendre" style="width:15px; height:15px;vertical-align: middle;';
if ($order == "des_eleve") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_eleve" onclick="this.form.order.value = this.value"/>';
//echo '</nobr>';
echo '</span>';
echo 'Élève';
echo '<input type="hidden" value="y" name="filter_checkbox_posted"/>';echo '<br /><input type="text" name="filter_eleve" value="'.getFiltreRechercheParam('filter_eleve').'" size="11"/>';
echo '<br /><nobr><input type="checkbox" name="filter_marqueur_appel"  onchange="submit()" value="y"';
if (getFiltreRechercheParam('filter_marqueur_appel') == 'y') {echo "checked='checked'";}
echo '/>Marque d\'appel</nobr>';
echo '</th>';

//en tete filtre classe
echo '<th>';
//echo '<nobr>';
echo '<span style="white-space: nowrap"> ';
echo 'Classe';
echo '<input type="image" src="../images/up.png" title="monter" style="width:15px; height:15px;vertical-align: middle;';
if ($order == "asc_classe") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_classe"/ onclick="this.form.order.value = this.value">';
echo '<input type="image" src="../images/down.png" title="descendre" style="width:15px; height:15px;vertical-align: middle;';
if ($order == "des_classe") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_classe"/ onclick="this.form.order.value = this.value">';
//echo '</nobr>';
echo '</span>';
echo '<br />';
echo ("<select name=\"filter_classe\" onchange='submit()'>");
echo "<option value=''></option>\n";
foreach (ClasseQuery::create()->orderByNom()->orderByNomComplet()->find() as $classe) {
	echo "<option value='".$classe->getId()."'";
	if (getFiltreRechercheParam('filter_classe') === (string) $classe->getId()) echo " SELECTED ";
	echo ">";
	echo $classe->getNom();
	echo "</option>\n";
}
echo "</select>";
echo '</th>';

//en tete filtre groupe
echo '<th>';
//echo '<nobr>';
echo '<span style="white-space: nowrap;"> ';
echo 'Groupe';
echo '<input type="image" src="../images/up.png" title="monter" style="width:15px; height:15px;vertical-align: middle;';
if ($order == "asc_groupe") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_groupe"/ onclick="this.form.order.value = this.value">';
echo '<input type="image" src="../images/down.png" title="descendre" style="width:15px; height:15px;vertical-align: middle;';
if ($order == "des_groupe") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_groupe"/ onclick="this.form.order.value = this.value">';
//echo '</nobr>';
echo '</span>';
echo '<br />';
echo ("<select name=\"filter_groupe\" onchange='submit()'>");
echo "<option value=''></option>\n";
foreach (GroupeQuery::create()->orderByName()->useJGroupesClassesQuery()->useClasseQuery()->orderByNom()->endUse()->endUse()
                                ->leftJoinWith('Groupe.JGroupesClasses')
                                ->leftJoinWith('JGroupesClasses.Classe')
                                ->find()  as $group) {
	echo "<option value='".$group->getId()."'";
	if (getFiltreRechercheParam('filter_groupe') === (string) $group->getId()) echo " SELECTED ";
	echo ">";
	echo $group->getNameAvecClasses();
	echo "</option>\n";
}
echo "</select>";
echo '</th>';

//en tete filtre aid
echo '<th>';
//echo '<nobr>';
echo '<span style="white-space: nowrap;"> ';
echo 'AID';
echo '<input type="image" src="../images/up.png" title="monter" style="width:15px; height:15px;vertical-align: middle;';
if ($order == "asc_aid") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_aid"/ onclick="this.form.order.value = this.value">';
echo '<input type="image" src="../images/down.png" title="descendre" style="width:15px; height:15px;vertical-align: middle;';
if ($order == "des_aid") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_aid"/ onclick="this.form.order.value = this.value">';
//echo '</nobr>';
echo '</span>';
echo '<br />';
echo ("<select name=\"filter_aid\" onchange='submit()'>");
echo "<option value=''></option>\n";
//$temp_collection->add(AidDetailsQuery::create()->useJAidElevesQuery()->useEleveQuery()->useJEleveCpeQuery()->filterByUtilisateurProfessionnel($utilisateur)->endUse()->endUse()->endUse()->find());
foreach (AidDetailsQuery::create()->find() as $aid) {
	echo "<option value='".$aid->getId()."'";
	if (getFiltreRechercheParam('filter_aid') === (string) $aid->getId()) echo " SELECTED ";
	echo ">";
	echo $aid->getNom();
	echo "</option>\n";
}
echo "</select>";
echo '</th>';

//en tete filtre creneaux
echo '<th>';
//echo '<nobr>';
echo '<span style="white-space: nowrap;"> ';
echo 'Créneau';
echo '<input type="image" src="../images/up.png" title="monter" style="width:15px; height:15px;vertical-align: middle;';
if ($order == "asc_creneau") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_creneau"/ onclick="this.form.order.value = this.value">';
echo '<input type="image" src="../images/down.png" title="descendre" style="width:15px; height:15px;vertical-align: middle;';
if ($order == "des_creneau") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_creneau"/ onclick="this.form.order.value = this.value">';
//echo '</nobr>';
echo '</span>';
echo '<br />';
echo ("<select name=\"filter_creneau\" onchange='submit()'>");
echo "<option value=''></option>\n";
foreach (EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime() as $edt_creneau) {
	echo "<option value='".$edt_creneau->getIdDefiniePeriode()."'";
	if (getFiltreRechercheParam('filter_creneau') === (string) $edt_creneau->getIdDefiniePeriode()) echo " SELECTED ";
	echo ">";
	echo $edt_creneau->getDescription();
	echo "</option>\n";
}
echo "</select>";
echo '</th>';

//en tete filtre date debut
echo '<th>';
//echo '<nobr>';
echo '<span style="white-space: nowrap;"> ';
echo 'Date début';
echo '<input type="image" src="../images/up.png" title="monter" style="width:15px; height:15px;vertical-align: middle;';
if ($order == "asc_date_debut") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_date_debut"/ onclick="this.form.order.value = this.value">';
echo '<input type="image" src="../images/down.png" title="descendre" style="width:15px; height:15px;vertical-align: middle;';
if ($order == "des_date_debut") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_date_debut"/ onclick="this.form.order.value = this.value">';
//echo '</nobr>';
echo '</span>';
echo '<br />';
//echo '<nobr>';
echo '<span style="white-space: nowrap;"> ';
echo 'Entre : <input size="13" id="filter_date_debut_saisie_debut_plage" name="filter_date_debut_saisie_debut_plage" value="';
if (isFiltreRechercheParam('filter_date_debut_saisie_debut_plage')) {echo getFiltreRechercheParam('filter_date_debut_saisie_debut_plage');}
echo '" />&nbsp;';
echo '<img id="trigger_filter_date_debut_saisie_debut_plage" src="../images/icons/calendrier.gif" alt=""/>';
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
echo 'Date fin';
echo '<input type="image" src="../images/up.png" title="monter" style="width:15px; height:15px;vertical-align: middle;';
if ($order == "asc_date_fin") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_date_fin"/ onclick="this.form.order.value = this.value">';
echo '<input type="image" src="../images/down.png" title="descendre" style="width:15px; height:15px;vertical-align: middle;';
if ($order == "des_date_fin") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_date_fin"/ onclick="this.form.order.value = this.value">';
//echo '</nobr>';
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

//en tete filtre emplacement de cours
echo '<th>';
//echo '<nobr>';
echo 'Cours';
//echo '</nobr>';
echo '</th>';

//en tete type d'absence
echo '<th>';
//echo '<nobr>';
echo 'Type';
//echo '</nobr>';
echo '<br />';
echo ("<select name=\"filter_type\" onchange='submit()'>");
echo "<option value=''></option>\n";
echo "<option value='SANS'";
if (getFiltreRechercheParam('filter_type') == 'SANS') echo " selected='selected' ";
echo ">SANS TYPE</option>\n";
foreach (AbsenceEleveTypeQuery::create()->orderBySortableRank()->find() as $type) {
	echo "<option value='".$type->getId()."'";
	if (getFiltreRechercheParam('filter_type') === (string) $type->getId()) echo " SELECTED ";
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
echo "<option value='n' ";
if (getFiltreRechercheParam('filter_manqement_obligation') == 'n') {echo "selected='selected'";}
echo ">non</option>\n";
echo "</select>";
echo '<br/>Manquement obligation présence';
echo '</th>';

//en tete filtre sous_responsabilite_etablissement
echo '<th>';
//echo '<input type="checkbox" value="y" name="filter_sous_responsabilite_etablissement" onchange="submit()"';
//if (isFiltreRechercheParam('filter_sous_responsabilite_etablissement') && getFiltreRechercheParam('filter_sous_responsabilite_etablissement') == 'y') {echo "checked='checked'";}
//echo '/><br/>sous resp. etab.';
echo 'Sous resp. étab.';
echo '</th>';

//en tete filtre date traitement
echo '<th>';
//echo '<nobr>';
echo '<span style="white-space: nowrap;"> ';
echo 'Date traitement';
echo '<input type="image" src="../images/up.png" title="monter" style="width:15px; height:15px;vertical-align: middle;';
if ($order == "asc_date_traitement") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_date_traitement"/ onclick="this.form.order.value = this.value">';
echo '<input type="image" src="../images/down.png" title="descendre" style="width:15px; height:15px;vertical-align: middle;';
if ($order == "des_date_traitement") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_date_traitement"/ onclick="this.form.order.value = this.value">';
//echo '</nobr>';
echo '</span>';
echo '<br />';
//echo '<nobr>';
echo '<span style="white-space: nowrap;"> ';
echo 'Entre : <input size="13" id="filter_date_traitement_absence_debut_plage" name="filter_date_traitement_absence_debut_plage" value="';
if (isFiltreRechercheParam('filter_date_traitement_absence_debut_plage')) {echo getFiltreRechercheParam('filter_date_traitement_absence_debut_plage');}
echo '" />&nbsp;';
echo '<img id="trigger_filter_date_traitement_absence_debut_plage" src="../images/icons/calendrier.gif" alt="" />';
//echo '</nobr>';
echo '</span>';
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
echo '<br />';
//echo '<nobr>';
echo '<span style="white-space: nowrap;"> ';
echo 'Et : <input size="13" id="filter_date_traitement_absence_fin_plage" name="filter_date_traitement_absence_fin_plage" value="';
if (isFiltreRechercheParam('filter_date_traitement_absence_fin_plage')) {echo getFiltreRechercheParam('filter_date_traitement_absence_fin_plage');}
echo '" />&nbsp;';
echo '<img id="trigger_filter_date_traitement_absence_fin_plage" src="../images/icons/calendrier.gif" alt="" />';
//echo '</nobr>';
echo '</span>';
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
echo '</th>';

//en tete conflit
echo '<th>';
echo 'Conflit';
echo '</th>';

//en tete filtre date creation
echo '<th>';
//echo '<nobr>';
echo '<span style="white-space: nowrap;"> ';
echo 'Date création';
echo '<input type="image" src="../images/up.png" title="monter" style="width:15px; height:15px;vertical-align: middle;';
if ($order == "asc_date_creation") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_date_creation"/ onclick="this.form.order.value = this.value">';
echo '<input type="image" src="../images/down.png" title="descendre" style="width:15px; height:15px;vertical-align: middle;' ;
if ($order == "des_date_creation") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_date_creation"/ onclick="this.form.order.value = this.value">';
//echo '</nobr>';
echo '</span>';
echo '<br />';
//echo '<nobr>';
echo '<span style="white-space: nowrap;"> ';
echo 'Entre : <input size="13" id="filter_date_creation_saisie_debut_plage" name="filter_date_creation_saisie_debut_plage" value="';
if (isFiltreRechercheParam('filter_date_creation_saisie_debut_plage')) {echo getFiltreRechercheParam('filter_date_creation_saisie_debut_plage');}
echo '" />&nbsp;';
echo '<img id="trigger_filter_date_creation_saisie_debut_plage" src="../images/icons/calendrier.gif" alt="" />';
//echo '</nobr>';
echo '</span>';
echo '
<script type="text/javascript">
    Calendar.setup({
	inputField     :    "filter_date_creation_saisie_debut_plage",     // id of the input field
	ifFormat       :    "%d/%m/%Y %H:%M",      // format of the input field
	button         :    "trigger_filter_date_creation_saisie_debut_plage",  // trigger for the calendar (button ID)
	align          :    "Tl",           // alignment (defaults to "Bl")
	singleClick    :    true,
	showsTime	:   true
    });
</script>';
echo '<br />';
//echo '<nobr>';
echo '<span style="white-space: nowrap;"> ';
echo 'Et : <input size="13" id="filter_date_creation_saisie_fin_plage" name="filter_date_creation_saisie_fin_plage" value="';
if (isFiltreRechercheParam('filter_date_creation_saisie_fin_plage')) {echo getFiltreRechercheParam('filter_date_creation_saisie_fin_plage');}
echo '" />&nbsp;';
echo '<img id="trigger_filter_date_creation_saisie_fin_plage" src="../images/icons/calendrier.gif" alt="" />';
//echo '</nobr>';
echo '</span>';
echo '
<script type="text/javascript">
    Calendar.setup({
	inputField     :    "filter_date_creation_saisie_fin_plage",     // id of the input field
	ifFormat       :    "%d/%m/%Y %H:%M",      // format of the input field
	button         :    "trigger_filter_date_creation_saisie_fin_plage",  // trigger for the calendar (button ID)
	align          :    "Tl",           // alignment (defaults to "Bl")
	singleClick    :    true,
	showsTime	:   true
    });
</script>';
echo '</th>';

//en tete filtre date modification
echo '<th>';
//echo '<nobr>';
echo '<span style="white-space: nowrap;"> ';
echo '';
echo '<input type="image" src="../images/up.png"  title="monter" style="width:15px; height:15px; vertical-align: middle;';
if ($order == "asc_date_modification") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_date_modification"/ onclick="this.form.order.value = this.value">';
echo '<input type="image" src="../images/down.png" title="descendre" style="width:15px; height:15px; vertical-align: middle;';
if ($order == "des_date_modification") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_date_modification"/ onclick="this.form.order.value = this.value">';
echo '<br/> ';
echo '</span>';
echo '<span style="white-space: nowrap;"> ';
echo '<input type="checkbox" value="y" name="filter_date_modification" onchange="submit()"';
if (isFiltreRechercheParam('filter_date_modification') && getFiltreRechercheParam('filter_date_modification') == 'y') {echo "checked='checked'";}
echo '/></span><br/> Modifié';
echo '</th>';

//en tete commentaire
echo '<th>';
echo 'Com.';
echo '</th>';

//en tete disciplinne
echo '<th>';
//echo '<nobr>';
echo '<span style="white-space: nowrap;"> ';
echo '<input type="image" src="../images/up.png" title="monter" style="width:15px; height:15px; vertical-align: middle;';
if ($order == "asc_dis") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_dis"/ onclick="this.form.order.value = this.value">';
echo '<input type="image" src="../images/down.png" title="descendre" style="width:15px; height:15px; vertical-align: middle;';
if ($order == "des_dis") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_dis"/ onclick="this.form.order.value = this.value">';
echo '</span>';
echo '<br/>';
//echo '<nobr>';
echo '<span style="white-space: nowrap;"> ';
echo '<input type="checkbox" value="y" name="filter_discipline" onchange="submit()"';
if (isFiltreRechercheParam('filter_discipline') && getFiltreRechercheParam('filter_discipline') == 'y') {echo "checked='checked'";}
echo '/></span><br/>Incident';
echo '</th>';

echo '</tr>';
echo '</thead>';

echo '<tbody>';
$results = $saisies_col->getResults();
if ($recherche_saisie_a_rattacher == 'oui' && $traitement != null) {
    if($results->count()==0){
        echo"<p class='red'>Aucune saisie (de + ou - 24 heures) à rattacher au traitement : ";
        echo "<a href='visu_traitement.php?id_traitement=".$traitement->getId()."";
        if($menu){
                echo"&menu=false";
            } 
        echo"'> ";
	    echo $traitement->getDescription();
	    echo "</a>";
    }
}
$hier='';
$numero_couleur=1;
foreach ($results as $saisie) {
    $aujourdhui=strftime("%d/%m/%Y", $saisie->getDebutAbs('U'));    
    if (!isFiltreRechercheParam('filter_eleve')) {
        $numero_couleur = $results->getPosition();
    } else {
        if ($aujourdhui !== $hier)
            $numero_couleur++;
    }
    if ($numero_couleur %2 == '1') {
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

    echo '<td>';
    echo "<a href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."' style='display: block; height: 100%;'> ";
    echo $saisie->getId();
    echo "</a>";
    echo '</td>';

	if (getFiltreRechercheParam('filter_saisies_supprimees') == 'y') {
	    echo '</td>';
	    echo '<td>';
	    echo "<a href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'>\n";
	    echo (strftime("%a %d/%m/%Y %H:%M", $saisie->getDeletedAt('U')));
    	$suppr_utilisateur = UtilisateurProfessionnelQuery::create()->findPK($saisie->getDeletedBy());
    	if ($suppr_utilisateur != null) {
    		echo ' par '.  $suppr_utilisateur->getCivilite().' '.$suppr_utilisateur->getNom().' '.mb_substr($suppr_utilisateur->getPrenom(), 0, 1).'.';;
    	}
	    echo "</a>";
	    echo '</td>';
	}

    echo '<td>';
//    echo "<a href='liste_saisies_selection_traitement.php?filter_utilisateur=".$saisie->getUtilisateurProfessionnel()->getNom()."' style='display: block; height: 100%; color: #330033'> ";
    if ($saisie->getUtilisateurProfessionnel() != null) {
    echo "<a href='liste_saisies_selection_traitement.php?filter_utilisateur=".$saisie->getUtilisateurProfessionnel()->getNom()."' style='display: block; height: 100%; color: #330033'> ";
	echo $saisie->getUtilisateurProfessionnel()->getCivilite().' '.$saisie->getUtilisateurProfessionnel()->getNom();
    echo "</a>";
    }
 //   echo "</a>";
    echo '</td>';

    echo '<td>';
    if ($saisie->getEleve() != null) {
	echo "<table style='border-spacing:0px; border-style : none; margin : 0px; padding : 0px; font-size:100%; width:100%;'>";
	echo "<tr style='border-spacing:0px; border-style : none; margin : 0px; padding : 0px; font-size:100%;'>";
	echo "<td style='border-spacing:0px; border-style : none; margin : 0px; padding : 0px; font-size:100%;'>";
	echo "<a href='liste_saisies_selection_traitement.php?filter_eleve=".$saisie->getEleve()->getNom()."&order=asc_eleve' style='display: block; height: 100%;'> ";
	echo ($saisie->getEleve()->getCivilite().' '.$saisie->getEleve()->getNom().' '.$saisie->getEleve()->getPrenom());
	echo "</a>";
	if ($utilisateur->getAccesFicheEleve($saisie->getEleve())) {
	    //echo "<a href='../eleves/visu_eleve.php?ele_login=".$saisie->getEleve()->getLogin()."' target='_blank'>";
	    echo "<a href='../eleves/visu_eleve.php?ele_login=".$saisie->getEleve()->getLogin()."&amp;onglet=responsables&amp;quitter_la_page=y' target='_blank' >";
	    echo ' (voir fiche)';
	    echo "</a>";
	}
	echo "</td>";
	echo "<td style='border-spacing:0px; border-style : none; margin : 0px; padding : 0px; font-size:100%;'>";
//	echo "<a href='liste_saisies_selection_traitement.php?filter_eleve=".$saisie->getEleve()->getNom()."' style='display: block; height: 100%;'> ";
 	if ((getSettingValue("active_module_trombinoscopes")=='y') && $saisie->getEleve() != null) {

	echo "<a href='liste_saisies_selection_traitement.php?filter_eleve=".$saisie->getEleve()->getNom()."&order=asc_eleve' style='display: block; height: 100%;'> ";
	$nom_photo = $saisie->getEleve()->getNomPhoto(1);
	    $photos = $nom_photo;
	    //if (($nom_photo != "") && (file_exists($photos))) {
	    if (($nom_photo != NULL) && (file_exists($photos))) {
		$valeur = redimensionne_image_petit($photos);
		echo ' <img src="'.$photos.'" style ="width:'.$valeur[0].'px; height:'.$valeur[1].'px;align:right" alt="" title="" /> ';
	    }
	echo "</a>";
	}
//	echo "</a>";
	echo "</td></tr></table>";
    } else {
	echo "Marqueur d'appel effectué";
    }
    echo '</td>';

    echo '<td>';
    if ($saisie->getClasse() != null) {
	echo "<a href='liste_saisies_selection_traitement.php?filter_classe=".$saisie->getClasse()->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'> ";
	echo $saisie->getClasse()->getNom();
    echo "</a>";
    } else {
	echo "&nbsp;";
    }
    echo '</td>';

    echo '<td>';
    if ($saisie->getGroupe() != null) {
	echo "<a href='liste_saisies_selection_traitement.php?filter_groupe=".$saisie->getGroupe()->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'> ";
	echo $saisie->getGroupe()->getNameAvecClasses();
    echo "</a>";
    } else {
	echo "&nbsp;";
    }
    echo '</td>';

    echo '<td>';
    if ($saisie->getAidDetails() != null) {
	echo "<a href='liste_saisies_selection_traitement.php?filter_aid=".$saisie->getAidDetails()->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'>\n";
	//$groupe = new Groupe();
	echo $saisie->getAidDetails()->getNom();
    } else {
	echo "<a href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'>\n";
	echo "&nbsp;";
    }
    echo "</a>";
    echo '</td>';

    echo '<td>';
//    echo "<a href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'>\n";
    if ($saisie->getEdtCreneau() != null) {
	//$groupe = new Groupe();
    echo "<a href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'>\n";
	echo $saisie->getEdtCreneau()->getDescription();
    echo "</a>";
    } else {
	echo "&nbsp;";
    }
    echo '</td>';

    echo '<td>';
    echo "<a href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'>\n";
    echo (strftime("%a %d/%m/%Y %H:%M", $saisie->getDebutAbs('U')));
    echo "</a>";
    echo '</td>';

    echo '<td>';
    echo "<a href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'>\n";
    echo (strftime("%a %d/%m/%Y %H:%M", $saisie->getFinAbs('U')));
    echo "</a>";
    echo '</td>';

    echo '<td>';
//    echo "<a href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'>\n";
    //echo '<nobr>';
    if ($saisie->getEdtEmplacementCours() != null) {
	//$groupe = new Groupe();
    echo "<a href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'>\n";
    
	echo $saisie->getEdtEmplacementCours()->getDescription();
    echo "</a>";
    } else {
	echo "&nbsp;";
    }
    //echo '</nobr>';
    echo '</td>';

    echo '<td>';
    echo "<a href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'>\n";
    foreach ($saisie->getAbsenceEleveTraitements() as $traitement) {
	 if ($traitement->getAbsenceEleveType() != null) {
	    echo $traitement->getAbsenceEleveType()->getNom();
	    if (!$saisie->getAbsenceEleveTraitements()->isLast())
	    echo ';<br/>';
	 }
    }
    if ($saisie->getAbsenceEleveTraitements()->isEmpty()) {
	echo "&nbsp;";
    }
    echo "</a>";
    echo '</td>';

    echo '<td>';
    if ($saisie->getManquementObligationPresence()) {
	echo 'oui';
    } else {
	echo 'non';
    }
    echo '</td>';

    echo '<td>';
    if ($saisie->getSousResponsabiliteEtablissement()) {
	echo 'oui';
    } else {
	echo 'non';
    }
    echo '</td>';

    echo '<td>';
    foreach ($saisie->getAbsenceEleveTraitements() as $traitement) {
	echo "<table width='100%'><tr><td>";
	echo "<a href='visu_traitement.php?id_traitement=".$traitement->getPrimaryKey()."' style='display: block; height: 100%;'> ";
    $desc = $traitement->getDescription();
    if (mb_strlen($desc)>300) {
    	echo mb_substr($desc,0,300).' ... ';
    } else {
    	echo $desc;
    }
	echo "</a>";
	echo "</td></tr></table>";
    }
    if ($saisie->getAbsenceEleveTraitements()->isEmpty()) {
	echo "&nbsp;";
    }
    echo '</td>';

    echo '<td>';
    $saisies_conflit = $saisie->getSaisiesContradictoiresManquementObligation();
    foreach ($saisies_conflit as $saisie_conflit) {
	echo "<a href='visu_saisie.php?id_saisie=".$saisie_conflit->getPrimaryKey()."' style=''> ";
	echo $saisie_conflit->getId();
	echo "</a>";
	if (!$saisies_conflit->isLast()) {
	    echo ' - ';
	}
    }
   	echo '</td>';
    echo '<td>';
    echo "<a href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'>\n";
	$all_version = $saisie->getAllVersions()->getFirst();
	if ($all_version != null) {
		$created_at = $saisie->getAllVersions()->getFirst()->getVersionCreatedAt('U');
    	echo (strftime("%a %d/%m/%Y %H:%M", $created_at));
	}else{
        $created_at = $saisie->getVersionCreatedAt('U');
        echo (strftime("%a %d/%m/%Y %H:%M", $created_at)); 
	}
    echo "</a>";
    echo '</td>';

    echo '<td>';
    echo "<a href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'>\n";
    if ($created_at != $saisie->getVersionCreatedAt('U')) {
    echo "<a href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'>\n";
   
	echo (strftime("%a %d/%m/%Y %H:%M", $saisie->getVersionCreatedAt('U')));
    echo "</a>";
    } else {
	echo "&nbsp;";
    }
    echo '</td>';

    echo '<td>';
    echo "<a href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'>\n";
    echo ($saisie->getCommentaire());
    echo "&nbsp;";
    echo "</a>";
    echo '</td>';

    echo '<td>';
    if ($saisie->getIdSIncidents() !== null) {
	echo "<a href='../mod_discipline/saisie_incident.php?id_incident=".
	$saisie->getIdSIncidents()."&step=2&return_url=no_return'>Visualiser l'incident </a>";
    }
    echo '</td>';

    echo '</tr>';
    $hier=$aujourdhui;
}

echo '</tbody>';
//echo '</tbody>';

echo '</table>';
echo '<p>';
if (isset($message_erreur_traitement)) {
    echo $message_erreur_traitement;
}
echo '</p>';
echo '</form>';
echo '</div>';

$javascript_footer_texte_specifique = '<script type="text/javascript">
    dojo.require("dijit.form.Button");
    dojo.require("dijit.Menu");
    dojo.require("dijit.form.Form");
    dojo.require("dijit.form.CheckBox");
    dojo.require("dijit.form.DateTextBox");

    dojo.addOnLoad(function() {
        var menu = new dijit.Menu({
            style: "display: none;"
        });

        var menuItem0 = new dijit.MenuItem({
            label: "Sélectionner tous",
            onClick: function() {
			SetAllCheckBoxes(\'liste_saisies\', \'select_saisie[]\', \'\', true);
	    }
        });
        menu.addChild(menuItem0);
	
        var menuItem1 = new dijit.MenuItem({
            label: "aucun",
            onClick: function() {
			SetAllCheckBoxes(\'liste_saisies\', \'select_saisie[]\', \'\', false);
        }
        });
        menu.addChild(menuItem1);

        var menuItem2 = new dijit.MenuItem({
            label: "non traitées",
            onClick: function() {
			SetAllCheckBoxes(\'liste_saisies\', \'select_saisie[]\', \'\', false);
			SetAllCheckBoxes(\'liste_saisies\', \'select_saisie[]\', \'saisie_vierge\', true);			
	    }
        });
        menu.addChild(menuItem2);

        var menuItem3 = new dijit.MenuItem({
            label: "non notifiées",
            onClick: function() {
			SetAllCheckBoxes(\'liste_saisies\', \'select_saisie[]\', \'\', true);
			SetAllCheckBoxes(\'liste_saisies\', \'select_saisie[]\', \'saisie_notifie\', false);	    }
        });
        menu.addChild(menuItem3);

        var button = new dijit.form.DropDownButton({
            label: "",
            name: "programmatic2",
            dropDown: menu,
            id: "progButton"
        });
        dojo.byId("select_shortcut_buttons_container").appendChild(button.domNode);

	//affichage des boutons d action
	dojo.query(\'[widgetid=action_bouton]\').style({ visibility:"visible" }).style({ display:"" });
    });
</script>';

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
