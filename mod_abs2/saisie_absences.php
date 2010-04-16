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
include("../lib/initialisationsPropel.inc.php");
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

if ($utilisateur->getStatut()=="professeur" &&  getSettingValue("active_module_absence_professeur")!='y') {
    die("Le module n'est pas activé.");
}

//récupération des paramètres de la requète
$id_groupe = isset($_POST["id_groupe"]) ? $_POST["id_groupe"] :(isset($_GET["id_groupe"]) ? $_GET["id_groupe"] :NULL);
$id_creneau = isset($_POST["id_creneau"]) ? $_POST["id_creneau"] :(isset($_GET["id_creneau"]) ? $_GET["id_creneau"] :NULL);
$id_cours = isset($_POST["id_cours"]) ? $_POST["id_cours"] :(isset($_GET["id_cours"]) ? $_GET["id_cours"] :NULL);
$type_selection = isset($_POST["type_selection"]) ? $_POST["type_selection"] :(isset($_GET["type_selection"]) ? $_GET["type_selection"] :NULL);
$d_date_absence_eleve = isset($_POST["d_date_absence_eleve"]) ? $_POST["d_date_absence_eleve"] : date('d/m/Y');

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

// Etiquettes des onglets:
$onglet_abs='saisie';
include('menu_abs2.inc.php');
//===========================
echo "<div class='css-panes' id='containDiv'>\n";


//on affiche une selection avec les groupes
if (!$utilisateur->getGroupes()->isEmpty()) {
    echo "<form action=\"./saisie_absences.php\" method=\"post\" style=\"width: 100%;\">\n";
    echo '<input type="hidden" name="type_selection" value="id_groupe"/>';
     echo ("<select name=\"id_groupe\">");
    if ($id_groupe == null) {
	$cours = $utilisateur->getEdtEmplacementCours();
	if ($cours != null) {
	    $id_groupe = $cours->getIdGroupe();
	} else {
	    $id_groupe =  $_SESSION['id_groupe_session'];
	}
    }
    echo "<option value='-1'>choisissez un groupe</option>\n";
    foreach ($utilisateur->getGroupes() as $group) {
	    echo "<option value='".$group->getId()."'";
	    if ($id_groupe == $group->getId()) echo " SELECTED ";
	    echo ">";
	    echo $group->getDescription() . "&nbsp;-&nbsp;(";
	    $str = null;
	    foreach ($group->getClasses() as $classe) {
		    $str .= $classe->getClasse() . ", ";
	    }
	    $str = substr($str, 0, -2);
	    echo $str . ")&nbsp;\n";
	    echo "</option>\n";
    }
    echo "</select>&nbsp;";
    echo ("<select name=\"id_creneau\">");
    $edt_creneau_col = EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime();
    if ($id_creneau == null) {
	$current_creneau = EdtCreneauPeer::retrieveEdtCreneauActuel();
	if ($current_creneau != null) {
	    $id_creneau = $current_creneau->getIdDefiniePeriode();
	}
    }
    echo "<option value='-1'>choisissez un creneau</option>\n";
    foreach ($edt_creneau_col as $edt_creneau) {
	//$edt_creneau = new EdtCreneau();
	    echo "<option value='".$edt_creneau->getIdDefiniePeriode()."'";
	    if ($id_creneau == $edt_creneau->getIdDefiniePeriode()) echo " SELECTED ";
	    echo ">";
	    echo $edt_creneau->getNomDefiniePeriode() . "&nbsp;&nbsp;";
	    echo $edt_creneau->getHeuredebutDefiniePeriode("H:i") . "&nbsp;-&nbsp;";
	    echo $edt_creneau->getHeurefinDefiniePeriode("H:i");
	    echo "</option>\n";
    }
    echo "</select>&nbsp;";
    if(empty($d_date_absence_eleve)) {
	    $d_date_absence_eleve = date('d/m/Y');
    }
    echo '<input size="10" id="d_date_absence_eleve" name="d_date_absence_eleve" value="'.$d_date_absence_eleve.'" />';
    echo '<input id="f_trigger_c" name="f_trigger_c" type="button" value="..."/>';
    echo '
    <script type="text/javascript">
	Calendar.setup({
	    inputField     :    "d_date_absence_eleve",     // id of the input field
	    ifFormat       :    "%d/%m/%Y",      // format of the input field
	    button         :    "f_trigger_c",  // trigger for the calendar (button ID)
	    align          :    "Tl",           // alignment (defaults to "Bl")
	    singleClick    :    true
	});
    </script>';
    echo '<button type="submit">Afficher les eleves</button>';
    echo "</form><br/>";
}


//on affiche une selection avec les cours
$edt_cours_col = $utilisateur->getEdtEmplacementCourssPeriodeCalendrierActuelle();
if (!$edt_cours_col->isEmpty()) {
    echo "<form action=\"./saisie_absences.php\" method=\"post\" style=\"width: 100%;\">\n";
    echo '<input type="hidden" name="type_selection" value="edt_cours"/>';
    echo ("<select name=\"id_cours\">");

    echo "<option value='-1'>choisissez un cours</option>\n";
    foreach ($edt_cours_col as $edt_cours) {
	    echo "<option value='".$edt_cours->getIdCours()."'";
	    if ($id_cours == $edt_cours->getIdCours()) echo " SELECTED ";
	    echo ">";
	    if ($edt_cours->getGroupe() != null) {
		echo $edt_cours->getGroupe()->getNameAvecClasses() . "&nbsp;&nbsp;";
	    }
	    if ($edt_cours->getAidDetails() != null) {
		echo "Aid : ".$edt_cours->getAidDetails()->getNom() . "&nbsp;&nbsp;";
	    }
	    echo $edt_cours->getJourSemaine() . "&nbsp;&nbsp;";
	    echo $edt_cours->getHeureDebut("H:i") . "&nbsp;&nbsp;";
	    echo $edt_cours->getHeureFin("H:i") . "&nbsp;-&nbsp;";
	    if ($edt_cours->getTypeSemaine() != NULL && $edt_cours->getTypeSemaine() != '') {
		echo " semaine : ".$edt_cours->getTypeSemaine();
	    }
	    echo "</option>\n";
    }
    echo "</select>&nbsp;";
    if(empty($d_date_absence_eleve)) {
	    $d_date_absence_eleve = date('d/m/Y');
    }
    echo '<input size="10" id="d_date_absence_eleve_2" name="d_date_absence_eleve" value="'.$d_date_absence_eleve.'" />';
    echo '<input id="f_trigger_c_2" name="f_trigger_c_2" type="button" value="..."/>';
    echo '
    <script type="text/javascript">
	Calendar.setup({
	    inputField     :    "d_date_absence_eleve_2",     // id of the input field
	    ifFormat       :    "%d/%m/%Y",      // format of the input field
	    button         :    "f_trigger_c_2",  // trigger for the calendar (button ID)
	    align          :    "Tl",           // alignment (defaults to "Bl")
	    singleClick    :    true
	});
    </script>';
    echo '<button type="submit">Afficher les eleves</button>';
    echo "</form><br/>";
}

echo "</div>\n";


//afichage des eleves
//on utilise en priorite
$eleve_col = new PropelCollection();
if ($type_selection == "id_groupe") {
    $groupe = GroupeQuery::create()->filterByPrimaryKey($id_groupe)->findOne();
    if ($groupe != null) {
	$eleve_col = $groupe->getEleves();
    }
}

$dt = new DateTime(str_replace("/",".",$d_date_absence_eleve));

//afichage des eleves
?>	<div class="centre_tout_moyen">
		<form method="post" action="prof_ajout_abs.php" id="liste_absence_eleve">
			<p class="expli_page choix_fin">
				Saisie des absences<br/>du <strong><?php echo strftime  ('%A %d %B %G',  $dt->format('U')); ?></strong>
				<br/></p>
			<p class="choix_fin">
				<input value="Enregistrer" name="Valider" type="submit"  onclick="this.form.submit();this.disabled=true;this.value='En cours'" />
			</p>
			<p class="choix_fin">
				<input type="hidden" name="passer_cahier_texte" id="passer_cahier_texte" value="false" />
				<input value="Enregistrer et passer au cahier de texte" name="Valider" type="submit"  onclick="document.getElementById('passer_cahier_texte').value = true; this.form.submit(); this.disabled=true; this.value='En cours'" />
			</p>

<!-- Afichage du tableau de la liste des élèves -->
<!-- Legende du tableau-->
	<?php echo '<p>'.$nbre_eleves.' élèves.</p>'; ?>
	<table class="tb_code_couleur" summary="Code des couleurs">
		<tr>
			<td class="td_Retard">&nbsp;R&nbsp;</td><td>&nbsp;Retard</td>
			<td class="td_Absence">&nbsp;A&nbsp;</td><td>&nbsp;Absence</td>
		</tr>
	</table>
<!-- Fin de la legende -->
<!-- <table style="text-align: left; width: 600px;" border="0" cellpadding="0" cellspacing="1"> -->
	<table class="tb_absences" summary="Liste des élèves pour l'appel. Colonne 1 : élèves, colonne 2 : absence, colonne3 : retard, colonnes suivantes : suivi de la journée par créneaux, dernière colonne : photos si actif">
		<caption class="invisible no_print">Absences</caption>
		<tbody>
			<tr class="titre_tableau_gestion" style="white-space: nowrap;">
				<th class="td_abs_eleves" style="width: 10%;">&nbsp;Hier&nbsp;</th>
				<th class="td_abs_eleves" abbr="élèves">Liste des &eacute;l&egrave;ves</th>
				<th class="td_abs_absence">Absence</th>
	<?php

	// on compte les créneaux pour savoir combien de cellules il faut créer
	$liste_creneaux = EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime();
	echo '
				<th colspan="'.$liste_creneaux->count().'" class="th_abs_suivi" abbr="Créneaux">Suivi sur la journ&eacute;e</th>'."\n";
?>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<?php foreach($liste_creneaux as $edt_creneau){
					echo '		<td class="td_nom_creneau">'.$edt_creneau->getNomDefiniePeriode().'</td>';
				}?>
			</tr>

<?php
require_once("../lib/footer.inc.php");
?>