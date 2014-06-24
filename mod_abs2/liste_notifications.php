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

if (isset($_POST["generer_notifications_par_lot"])) {
    include('generer_notifications_par_lot.php');
    die();
}

include('include_requetes_filtre_de_recherche.php');

$page_number = isset($_POST["page_number"]) ? $_POST["page_number"] :(isset($_GET["page_number"]) ? $_GET["page_number"] :(isset($_SESSION["page_number"]) ? $_SESSION["page_number"] : NULL));
if (!is_numeric($page_number) || $reinit_filtre == 'y') {
    $page_number = 1;
}

include('include_pagination.php');

//==============================================
$style_specifique[] = "mod_abs2/lib/abs_style";
$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";
$titre_page = "Les absences";
$utilisation_jsdivdrag = "non";
$_SESSION['cacher_header'] = "y";
$dojo = true;
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

include('menu_abs2.inc.php');

echo "<div class='css-panes' style='background-color:#c7e3ec;' id='containDiv' style='overflow : none; float : left; margin-top : -1px; border-width : 1px;'>\n";


$query = AbsenceEleveNotificationQuery::create();
if (isFiltreRechercheParam('filter_notification_id')) {
    $query->filterById(getFiltreRechercheParam('filter_notification_id'));
}
if (isFiltreRechercheParam('filter_utilisateur')) {
    $query->useUtilisateurProfessionnelQuery()->filterByNom('%'.getFiltreRechercheParam('filter_utilisateur').'%', Criteria::LIKE)->endUse();
}
if (isFiltreRechercheParam('filter_eleve')) {
    $query->useAbsenceEleveTraitementQuery()->useJTraitementSaisieEleveQuery()->useAbsenceEleveSaisieQuery()->useEleveQuery()
    ->filterByNomOrPrenomLike(getFiltreRechercheParam('filter_eleve'))->endUse()->endUse()->endUse()->endUse();
}
if (isFiltreRechercheParam('filter_type_notification')) {
    $query->filterByTypeNotification(getFiltreRechercheParam('filter_type_notification'));
}
if (isFiltreRechercheParam('filter_statut_notification')) {
	if (getFiltreRechercheParam('filter_statut_notification') != 'SANS') {
		$query->filterByStatutEnvoi(getFiltreRechercheParam('filter_statut_notification'));
	}
}
if (isFiltreRechercheParam('filter_date_creation_notification_debut_plage')) {
    echo 'auiauiaui';
    $date_creation_notification_debut_plage = new DateTime(str_replace("/",".",getFiltreRechercheParam('filter_date_creation_notification_debut_plage')));
    $query->filterByCreatedAt($date_creation_notification_debut_plage, Criteria::GREATER_EQUAL);
}
if (isFiltreRechercheParam('filter_date_creation_notification_fin_plage')) {
    $date_creation_notification_fin_plage = new DateTime(str_replace("/",".",getFiltreRechercheParam('filter_date_creation_notification_fin_plage')));
    $query->filterByCreatedAt($date_creation_notification_fin_plage, Criteria::LESS_EQUAL);
}
if (isFiltreRechercheParam('filter_date_modification')) {
    $query->where('AbsenceEleveNotification.CreatedAt != AbsenceEleveNotification.UpdatedAt');
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
    $query->useAbsenceEleveTraitementQuery()->useJTraitementSaisieEleveQuery()->useAbsenceEleveSaisieQuery()->useEleveQuery()->orderBy('Nom', Criteria::ASC)->endUse()->endUse()->endUse()->endUse();
} else if ($order == "des_eleve") {
    $query->useAbsenceEleveTraitementQuery()->useJTraitementSaisieEleveQuery()->useAbsenceEleveSaisieQuery()->useEleveQuery()->orderBy('Nom', Criteria::DESC)->endUse()->endUse()->endUse()->endUse();
} else if ($order == "asc_saisie") {
    $query->useAbsenceEleveTraitementQuery()->useJTraitementSaisieEleveQuery()->useAbsenceEleveSaisieQuery()->orderBy('DebutAbs', Criteria::ASC)->endUse()->endUse()->endUse();
} else if ($order == "des_saisie") {
    $query->useAbsenceEleveTraitementQuery()->useJTraitementSaisieEleveQuery()->useAbsenceEleveSaisieQuery()->orderBy('FinAbs', Criteria::DESC)->endUse()->endUse()->endUse();    
} else if ($order == "asc_traitement") {
    $query->useAbsenceEleveTraitementQuery()->orderBy('UpdatedAt', Criteria::ASC)->endUse();
} else if ($order == "des_traitement") {
    $query->useAbsenceEleveTraitementQuery()->orderBy('UpdatedAt', Criteria::DESC)->endUse();
} else if ($order == "asc_type") {
    $query->orderBy('TypeNotification', Criteria::ASC);
} else if ($order == "des_type") {
    $query->orderBy('TypeNotification', Criteria::DESC);
} else if ($order == "asc_statut") {
    $query->orderBy('StatutEnvoi', Criteria::ASC);
} else if ($order == "des_statut") {
    $query->orderBy('StatutEnvoi', Criteria::DESC);
} else if ($order == "asc_date_creation") {
    $query->orderBy('CreatedAt', Criteria::ASC);
} else if ($order == "des_date_creation") {
    $query->orderBy('CreatedAt', Criteria::DESC);
} else if ($order == "asc_date_modification") {
    $query->orderBy('UpdatedAt', Criteria::ASC);
} else if ($order == "des_date_modification") {
    $query->orderBy('UpdatedAt', Criteria::DESC);
}

$query->distinct();
$notifications_col = $query->paginate($page_number, $item_per_page);

$nb_pages = (floor($notifications_col->getNbResults() / $item_per_page) + 1);
if ($page_number > $nb_pages) {
    $page_number = $nb_pages;
}

echo '<form method="post" action="liste_notifications.php" name="liste_notifications" id="liste_notifications">';
if ($notifications_col->haveToPaginate()) {
    echo "Page ";
    echo '<input type="submit" name="page_deplacement" value="-"/>';
    echo '<input type="text" name="page_number" size="1" value="'.$page_number.'"/>';
    echo '<input type="submit" name="page_deplacement" value="+"/> ';
    echo "sur ".$nb_pages." page(s) ";
    echo "| ";
}
echo "Voir ";
echo '<input type="text" name="item_per_page" size="1" value="'.$item_per_page.'"/>';
echo "par page&nbsp;&nbsp;&nbsp;";
?>    <div id="action_bouton" dojoType="dijit.form.DropDownButton" style="display: inline">
	<span>Action</span>
	<div dojoType="dijit.Menu" style="display: inline">
	    <button type="submit" dojoType="dijit.MenuItem" onClick="document.liste_notifications.submit();">
		Rechercher
	    </button>
	    <button type="submit" name="reinit_filtre" value="y" dojoType="dijit.MenuItem" onClick="
		//Create an input type dynamically.
		var element = document.createElement('input');
		element.setAttribute('type', 'hidden');
		element.setAttribute('name', 'reinit_filtre');
		element.setAttribute('value', 'y');
		document.liste_notifications.appendChild(element);
		document.liste_notifications.submit();
				">
		Réinitialiser les filtres
	    </button>
	    <button type="submit" name="generer_notifications_par_lot" value="y" dojoType="dijit.MenuItem" onClick="
		//Create an input type dynamically.
		var element = document.createElement('input');
		element.setAttribute('type', 'hidden');
		element.setAttribute('name', 'generer_notifications_par_lot');
		element.setAttribute('value', 'y');
		document.liste_notifications.appendChild(element);
		document.liste_notifications.action = 'generer_notifications_par_lot.php';
		document.liste_notifications.submit();
				">
		Generer par lot les notifications sélectionnées
	    </button>
	</div>
    </div>
<script language="javascript">
   //on cache les boutons pas très jolis en attendant le parsing dojo
   dojo.byId("action_bouton").hide();
</script>
<?php
echo '<table id="table_liste_absents" class="tb_absences" style="border-spacing:0; width:100%;">';

echo '<thead>';
echo '<tr>';

//en tete selection
echo '<th>';
echo '<div id="select_shortcut_buttons_container"/>';
echo '</th>';

//en tete filtre id
echo '<th>';
echo '<input type="hidden" name="order" value="'.$order.'" />'; 
echo '<span style="white-space: nowrap;"> ';
echo 'N°';
echo '<input type="image" src="../images/up.png" title="monter" style="vertical-align: middle;width:15px; height:15px; ';
if ($order == "asc_id") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_id" onclick="this.form.order.value = this.value"/>';
echo '<input type="image" src="../images/down.png" title="descendre" style="vertical-align: middle;width:15px; height:15px; ';
if ($order == "des_id") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_id" onclick="this.form.order.value = this.value"/>';
echo '</span>';
echo '<input type="text" name="filter_notification_id" value="'.getFiltreRechercheParam('filter_notification_id').'" size="3"/>';
echo '</th>';

//en tete filtre utilisateur
echo '<th>';
echo '<span style="white-space: nowrap;"> ';
echo '<input type="image" src="../images/up.png" title="monter" style="vertical-align: middle; width:15px; height:15px; ';
if ($order == "asc_utilisateur") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_utilisateur" onclick="this.form.order.value = this.value"/>';
echo '<input type="image" src="../images/down.png" title="descendre" style="vertical-align: middle;width:15px; height:15px; ';
if ($order == "des_utilisateur") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_utilisateur" onclick="this.form.order.value = this.value"/>';
echo '</span><br />';
echo 'Utilisateur';
echo '<br /><input type="text" name="filter_utilisateur" value="'.getFiltreRechercheParam('filter_utilisateur').'" size="12"/>';
echo '</th>';

//en tete filtre eleve
echo '<th>';
echo '<span style="white-space: nowrap;"> ';
echo 'Eleve';
echo '<input type="image" src="../images/up.png" title="monter" style="vertical-align: middle; width:15px; height:15px; ';
if ($order == "asc_eleve") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_eleve" onclick="this.form.order.value = this.value"/>';
echo '<input type="image" src="../images/down.png" title="descendre" style="vertical-align: middle;width:15px; height:15px; ';
if ($order == "des_eleve") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_eleve" onclick="this.form.order.value = this.value"/>';
echo '</span>';
echo '<br /><input type="text" name="filter_eleve" value="'.getFiltreRechercheParam('filter_eleve').'" size="8"/>';
echo '</th>';

//en tete filtre saisies
echo '<th>';
echo '<span style="white-space: nowrap;"> ';
echo '<input type="image" src="../images/up.png" title="monter" style="vertical-align: middle; width:15px; height:15px; ';
if ($order == "asc_saisie") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_saisie" onclick="this.form.order.value = this.value"/>';
echo '<input type="image" src="../images/down.png" title="descendre" style="vertical-align: middle;width:15px; height:15px; ';
if ($order == "des_saisie") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_saisie" onclick="this.form.order.value = this.value"/>';
echo '</span><br/>';
echo 'Saisies';
echo '</th>';

//en tete filtre traitement
echo '<th>';
echo '<span style="white-space: nowrap;"> ';
//echo '<nobr>';
echo '<input type="image" src="../images/up.png" title="monter" style="vertical-align: middle; width:15px; height:15px; ';
if ($order == "asc_traitement") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_traitement" onclick="this.form.order.value = this.value"/>';
echo '<input type="image" src="../images/down.png" title="descendre" style="vertical-align: middle;width:15px; height:15px; ';
if ($order == "des_traitement") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_traitement" onclick="this.form.order.value = this.value"/>';
echo '</span><br/>';
echo 'Traitement';
//echo '</nobr>';
echo '</th>';

//en tete filtre type de notification
echo '<th>';
echo '<span style="white-space: nowrap;"> ';
echo '<input type="image" src="../images/up.png" title="monter" style="vertical-align: middle; width:15px; height:15px; ';
if ($order == "asc_type") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_type" onclick="this.form.order.value = this.value"/>';
echo '<input type="image" src="../images/down.png" title="descendre" style="vertical-align: middle;width:15px; height:15px; ';
if ($order == "des_type") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_type" onclick="this.form.order.value = this.value"/>';
echo '</span>';
echo '<br />';
echo 'type de notification';
echo '<br />';
echo ("<select name=\"filter_type_notification\" onchange='submit()'>");
echo "<option value=''></option>\n";
foreach (AbsenceEleveNotificationPeer::getValueSet(AbsenceEleveNotificationPeer::TYPE_NOTIFICATION) as $type) {
    echo "<option value='$type'";
    if (getFiltreRechercheParam('filter_type_notification') === $type) {
	echo 'selected';
    }
    echo ">".$type."</option>\n";
}
echo "</select>";
echo '</th>';


//en tete filtre statut de notification
echo '<th>';
echo '<span style="white-space: nowrap;"> ';
//echo '<nobr>';
echo 'statut';
echo '<input type="image" src="../images/up.png" title="monter" style="vertical-align: middle; width:15px; height:15px; ';
if ($order == "asc_statut") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_statut" onclick="this.form.order.value = this.value"/>';
echo '<input type="image" src="../images/down.png" title="descendre" style="vertical-align: middle;width:15px; height:15px; ';
if ($order == "des_statut") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_statut" onclick="this.form.order.value = this.value"/>';
echo '</span>';
//echo '</nobr>';
echo '<br />';
echo ("<select name=\"filter_statut_notification\" onchange='submit()'>");
echo "<option value=''></option>\n";
foreach (AbsenceEleveNotificationPeer::getValueSet(AbsenceEleveNotificationPeer::STATUT_ENVOI) as $status) {
    echo "<option value='$status'";
    if (getFiltreRechercheParam('filter_statut_notification') === $status) {
	echo 'selected';
    }
    echo ">".$status."</option>\n";
}
echo "</select>";
echo '</th>';


//en tete filtre date creation
echo '<th>';
echo '<span style="white-space: nowrap;"> ';
//echo '<nobr>';
echo 'Date creation';
echo '<input type="image" src="../images/up.png" title="monter" style="vertical-align: middle; width:15px; height:15px; ';
if ($order == "asc_date_creation") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_date_creation" onclick="this.form.order.value = this.value"/>';
echo '<input type="image" src="../images/down.png" title="descendre" style="vertical-align: middle;width:15px; height:15px; ';
if ($order == "des_date_creation") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_date_creation" onclick="this.form.order.value = this.value"/>';
echo '</span>';
//echo '</nobr>';
echo '<br />';
echo '<span style="white-space: nowrap;"> ';
//echo '<nobr>';
echo 'Entre : <input size="13" id="filter_date_creation_notification_debut_plage" name="filter_date_creation_notification_debut_plage" value="';
if (isFiltreRechercheParam('filter_date_creation_notification_debut_plage')) {echo getFiltreRechercheParam('filter_date_creation_notification_debut_plage');}
echo '" onKeyDown="clavier_date2(this.id,event);" AutoComplete="off" />&nbsp;';
echo '<img id="trigger_filter_date_creation_notification_debut_plage" src="../images/icons/calendrier.gif" alt="" />';
echo '</span>';
//echo '</nobr>';
echo '
<script type="text/javascript">
    Calendar.setup({
	inputField     :    "filter_date_creation_notification_debut_plage",     // id of the input field
	ifFormat       :    "%d/%m/%Y %H:%M",      // format of the input field
	button         :    "trigger_filter_date_creation_notification_debut_plage",  // trigger for the calendar (button ID)
	align          :    "Tl",           // alignment (defaults to "Bl")
	singleClick    :    true,
	showsTime	:   true
    });
</script>';
echo '<br />';
echo '<span style="white-space: nowrap;"> ';
//echo '<nobr>';
echo 'Et : <input size="13" id="filter_date_creation_notification_fin_plage" name="filter_date_creation_notification_fin_plage" value="';
if (isFiltreRechercheParam('filter_date_creation_notification_fin_plage')) {echo getFiltreRechercheParam('filter_date_creation_notification_fin_plage');}
echo '" onKeyDown="clavier_date2(this.id,event);" AutoComplete="off" />&nbsp;';
echo '<img id="trigger_filter_date_creation_notification_fin_plage" src="../images/icons/calendrier.gif" alt="" />';
echo '</span>';
//echo '</nobr>';
echo '
<script type="text/javascript">
    Calendar.setup({
	inputField     :    "filter_date_creation_notification_fin_plage",     // id of the input field
	ifFormat       :    "%d/%m/%Y %H:%M",      // format of the input field
	button         :    "trigger_filter_date_creation_notification_fin_plage",  // trigger for the calendar (button ID)
	align          :    "Tl",           // alignment (defaults to "Bl")
	singleClick    :    true,
	showsTime	:   true
    });
</script>';
echo '</th>';

//en tete filtre date modification
echo '<th>';
echo '<span style="white-space: nowrap;"> ';
//echo '<nobr>';
echo '';
echo '<input type="image" src="../images/up.png" title="monter" style="vertical-align: middle; width:15px; height:15px; ';
if ($order == "asc_date_modification") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="asc_date_modification" onclick="this.form.order.value = this.value"/>';
echo '<input type="image" src="../images/down.png" title="descendre" style="vertical-align: middle;width:15px; height:15px; ';
if ($order == "des_date_modification") {echo "border-style: solid; border-color: red;";} else {echo "border-style: solid; border-color: silver;";}
echo 'border-width:1px;" alt="" name="order" value="des_date_modification" onclick="this.form.order.value = this.value"/>';
echo '</span><br/>';
//echo '</nobr> ';
echo '<span style="white-space: nowrap;"> ';
//echo '<nobr>';
echo '<input type="checkbox" value="y" name="filter_date_modification" onchange="submit()"';
if (getFiltreRechercheParam('filter_date_modification') == 'y') {echo "checked";}
echo '/> modifié';
echo '</span>';
//echo '</nobr>';
echo '</th>';

//en tete commentaire
echo '<th>';
echo 'com.';
echo '</th>';

echo '</tr>';
echo '</thead>';

//echo '<tbody>';
$results = $notifications_col->getResults();

if(count($results)) {
echo '<tbody>';

foreach ($results as $notification) {
    //$notification = new AbsenceEleveNotification();
    if ($results->getPosition() %2 == '1') {
	    $background_couleur="rgb(220, 220, 220);";
    } else {
	    $background_couleur="rgb(210, 220, 230);";
    }

    echo "<tr style='background-color :$background_couleur'>\n";

    echo '<td>';
    echo '<input name="select_notification[]" select_shortcut="true" value="'.$notification->getPrimaryKey().'" type="checkbox" notif_status="'.$notification->getStatutEnvoi().'"/>';
    echo '</td>';

    //donnees id
    echo '<td>';
    echo "<a href='visu_notification.php?id_notification=".$notification->getPrimaryKey()."' style='display: block; height: 100%;'> ";
    echo $notification->getId();
    echo "</a>";
    echo '</td>';

    //donnees utilisateur
    echo '<td>';
    echo "<a href='visu_notification.php?id_notification=".$notification->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'> ";
    if ($notification->getUtilisateurProfessionnel() != null) {
	echo $notification->getUtilisateurProfessionnel()->getCivilite().' '.$notification->getUtilisateurProfessionnel()->getNom();
    }
    echo "</a>";
    echo '</td>';

    //donnees eleve
    echo '<td>';
    $eleve_col = new PropelObjectCollection();
    if ($notification->getAbsenceEleveTraitement() != null) {
	foreach ($notification->getAbsenceEleveTraitement()->getAbsenceEleveSaisies() as $saisie) {
	    if ($saisie->getEleve() != null) {
		$eleve_col->add($saisie->getEleve());
	    }
	}
    }
    foreach ($eleve_col as $eleve) {
	echo "<table style='border-spacing:0px; border-style : none; margin : 0px; padding : 0px; width : 100%'>";
	echo "<tr style='border-spacing:0px; border-style : none; margin : 0px; padding : 0px;'>";
	echo "<td style='border-spacing:0px; border-style : none; margin : 0px; padding : 0px;'>";
	echo "<a href='liste_notifications.php?filter_eleve=".$eleve->getNom()."' style='display: block; height: 100%;'> ";
	echo ($eleve->getCivilite().' '.$eleve->getNom().' '.$eleve->getPrenom());
	echo "</a>";
	if ($utilisateur->getAccesFicheEleve($eleve)) {
	    //echo "<a href='../eleves/visu_eleve.php?ele_login=".$eleve->getLogin()."' target='_blank'>";
	    echo "<a href='../eleves/visu_eleve.php?ele_login=".$eleve->getLogin()."&amp;onglet=responsables&amp;quitter_la_page=y' target='_blank' >";
	    echo ' (voir fiche)';
	    echo "</a>";
	}
	echo "</td>";
 	if ((getSettingValue("active_module_trombinoscopes")=='y')) {
	    echo "<td style='border-spacing:0px; border-style : none; margin : 0px; padding : 0px; font-size:100%;'>";
	    echo "<a href='liste_notifications.php?filter_eleve=".$eleve->getNom()."' style='display: block; height: 100%;'> ";
	    $nom_photo = $eleve->getNomPhoto(1);
	    $photos = $nom_photo;
	    //if (($nom_photo != "") && (file_exists($photos))) {
	    if (($nom_photo != NULL) && (file_exists($photos))) {
		$valeur = redimensionne_image_petit($photos);
		echo ' <img src="'.$photos.'" style ="align:right; width:'.$valeur[0].'px; height:'.$valeur[1].'px;" alt="" title="" /> ';
	    }
	    echo "</a>";
	    echo "</td";
	}
	echo "</tr></table>";
    }
    echo '</td>';

    //donnees saisies
    echo '<td>';
    if ($notification->getAbsenceEleveTraitement() != null && !$notification->getAbsenceEleveTraitement()->getAbsenceEleveSaisies()->isEmpty()) {
	echo "<table style='border-spacing:0px; border-style : none; margin : 0px; padding : 0px; font-size:100%; width:100%'>";
	foreach ($notification->getAbsenceEleveTraitement()->getAbsenceEleveSaisies() as $saisie) {
	    echo "<tr style='border-spacing:0px; border-style : solid; border-size : 1px; margin : 0px; padding : 0px; font-size:100%;'>";
	    echo "<td style='border-spacing:0px; border-style : solid; border-size : 1px; çargin : 0px; padding-top : 3px; font-size:100%;'>";
	    echo "<a href='visu_saisie.php?id_saisie=".$saisie->getPrimaryKey()."' style='display: block; height: 100%;'>\n";
	    echo $saisie->getDescription();
	    echo "</a>";
	    echo "</td>";
	    echo "</tr>";
	}
	echo "</table>";
    }
    echo '</td>';

    echo '<td>';
    if ($notification->getAbsenceEleveTraitement() != null) {
	echo '<div>';
	echo "<a href='visu_traitement.php?id_traitement=".$notification->getAbsenceEleveTraitement()->getPrimaryKey()."' style='display: block; height: 100%;'> ";
	echo $notification->getAbsenceEleveTraitement()->getDescription();
	echo "</a></div>";
    }
    echo '</td>';

    echo '<td>';
	echo '<span style="white-space: nowrap;"> ';
//  echo '<td><nobr>';
    echo "<a href='visu_notification.php?id_notification=".$notification->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'>\n";
    echo $notification->getTypeNotification();
    echo "</a>";
    echo "</span>";
    echo '</td>';
//  echo '</nobr></td>';


    echo '<td>';
	echo '<span style="white-space: nowrap;"> ';
//  echo '<td><nobr>';
    echo "<a href='visu_notification.php?id_notification=".$notification->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'>\n";
    if ($notification->getStatutEnvoi() != ''){
	echo $notification->getStatutEnvoi();
    }
    echo "</a>";

    echo "</span>";
    echo '</td>';
//  echo '</nobr></td>';


    echo '<td>';
	echo '<span style="white-space: nowrap;"> ';
//  echo '<td><nobr>';
    echo "<a href='visu_notification.php?id_notification=".$notification->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'>\n";
    echo (strftime("%a %d/%m/%Y %H:%M", $notification->getCreatedAt('U')));
    echo "</a>";
        echo "</span>";
    echo '</td>';
//  echo '</nobr></td>';

    echo '<td>';
    echo "<a href='visu_notification.php?id_notification=".$notification->getPrimaryKey()."' style='color: #330033'>\n";
    echo (strftime("%a %d/%m/%Y %H:%M", $notification->getUpdatedAt('U')));
    echo "</a>";
    echo '</td>';

    echo '<td>';
    echo "<a href='visu_notification.php?id_notification=".$notification->getPrimaryKey()."' style='display: block; height: 100%; color: #330033'>\n";
    echo ($notification->getCommentaire());
    echo "&nbsp;";
    echo "</a>";
    echo '</td>';

    echo '</tr>';
}

echo '</tbody>';
}else{
  echo '<tbody>';
  // il faut au moins une case vide pour ne pas avoir d'erreur W3C
    echo '<tr>';
    echo '<td>';

    echo '</td>';
    echo '</tr>';
  echo '</tbody>';
}

echo '</table>';

echo '</form>';

echo "</div>\n";

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
            label: "Selectionner tous",
            onClick: function() {
		var query_string = \'input[type=checkbox][name="select_notification[]"]\';
		dojo.query(query_string).attr(\'checked\', true);
	    }
        });
        menu.addChild(menuItem0);
	
        var menuItem1 = new dijit.MenuItem({
            label: "Selectionner pret à envoyer",
            onClick: function() {
		var query_string = \'input[type=checkbox][name="select_notification[]"]\';
		dojo.query(query_string).attr(\'checked\', false);
		query_string = \'input[type=checkbox][notif_status="'.AbsenceEleveNotificationPeer::STATUT_ENVOI_PRET_A_ENVOYER.'"][name="select_notification[]"]\';
		dojo.query(query_string).attr(\'checked\', true);
	    }
        });
        menu.addChild(menuItem1);

        var menuItem2 = new dijit.MenuItem({
            label: "Selectionner aucun",
            onClick: function() {
		var query_string = \'input[type=checkbox][name="select_notification[]"]\';
		dojo.query(query_string).attr(\'checked\', false);
	    }
        });
        menu.addChild(menuItem2);

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

?>
