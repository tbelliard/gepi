<?php
/**
 *
 * @version $Id$
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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

// Initialisations files
require_once("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");
//mes fonctions
include("../edt_organisation/fonctions_calendrier.php");
// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
	die();
};

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

// Initialisation des variables
$date_absence_eleve = isset($_POST["date_absence_eleve"]) ? $_POST["date_absence_eleve"] :(isset($_GET["date_absence_eleve"]) ? $_GET["date_absence_eleve"] :(isset($_SESSION["date_absence_eleve"]) ? $_SESSION["date_absence_eleve"] : NULL));
if ($date_absence_eleve != null) {$_SESSION["date_absence_eleve"] = $date_absence_eleve;}

if ($date_absence_eleve != null) {
    $dt_date_absence_eleve = new DateTime(str_replace("/",".",$date_absence_eleve));
} else {
    $dt_date_absence_eleve = new DateTime('now');
}

$style_specifique[] = "edt_organisation/style_edt";
$style_specifique[] = "templates/DefaultEDT/css/small_edt";
$style_specifique[] = "mod_abs2/lib/abs_style";
$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";
//$javascript_specifique[] = "mod_abs2/lib/include";
$javascript_specifique[] = "edt_organisation/script/fonctions_edt";
//**************** EN-TETE *****************
$titre_page = "Bilan du jour.";
require_once("../lib/header.inc");
include('menu_abs2.inc.php');
include('menu_bilans.inc.php');
?>
<div id="contain_div" class="css-panes">
<form name="choix_date" action="<?php $_SERVER['PHP_SELF']?>" method="post">
<h2>Les saisies du
    <input size="8" id="date_absence_eleve_1" name="date_absence_eleve" value="<?php echo $dt_date_absence_eleve->format('d/m/Y')?>" />
    <script type="text/javascript">
	Calendar.setup({
	    inputField     :    "date_absence_eleve_1",     // id of the input field
	    ifFormat       :    "%d/%m/%Y",      // format of the input field
	    button         :    "date_absence_eleve_1",  // trigger for the calendar (button ID)
	    align          :    "Bl",           // alignment (defaults to "Bl")
	    singleClick    :    true
	});
    </script>
    <button type="submit">Changer</button>

<table style="border: 1px solid black;" cellpadding="5" cellspacing="5">
<?php $creneau_col = EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime();?>
	<tr>
		<th style="border: 1px solid black; background-color: grey;">Classe</th>
		<th style="border: 1px solid black; background-color: grey; min-width: 300px; max-width: 500px;">Nom Pr&eacute;nom</th>
<?php
		//afficher les créneaux
		foreach(EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime() as $creneau){
			echo "<th style=\"border: 1px solid black; background-color: grey;\">".$creneau->getNomDefiniePeriode()."</th>\n";
		}
?>
	</tr>
<?php
$dt_debut = $dt_date_absence_eleve;
$dt_debut->setTime(0,0,0);
$dt_fin = clone $dt_date_absence_eleve;
$dt_fin->setTime(23,59,59);
$classe_col = ClasseQuery::create()->orderByNom()->find();
foreach($classe_col as $classe) {
	echo '
		<tr>
			<td>'.$classe->getNom().'</td>
			<td colspan="'.($creneau_col->count() + 1).'"></td>
		</tr>
		';
	$eleve_col = EleveQuery::create()
		->orderByNom()
		->filterByUtilisateurProfessionnel($utilisateur)
		->useAbsenceEleveSaisieQuery()->filterByPlageTemps($dt_debut, $dt_fin)->endUse()
		->useJEleveClasseQuery()->filterByIdClasse($classe->getId())->endUse()
		->distinct()->find();
	foreach($eleve_col as $eleve){
			echo '<tr>
			<td></td>
			<td>'.$eleve->getNom().' '.$eleve->getPrenom();
			echo "<a href='../eleves/visu_eleve.php?ele_login=".$eleve->getLogin()."' >";
			echo ' (voir&nbsp;fiche)';
			echo "</a>";
			echo '</td>';
			// On traite alors pour chaque créneau
			foreach($creneau_col as $creneau) {
			    $abs_col = $eleve->getAbsenceEleveSaisiesDuCreneau($creneau, $dt_date_absence_eleve);
			    if ($abs_col->isEmpty()){
				echo '<td></td>';
			    } else {
				foreach($abs_col as $abs) {
				    $red = false;
				    if (!$abs->getResponsabiliteEtablissement()) {
					echo '<td style="background-color:red"></td>';
					$red = true;
					break;
				    }
				}
				if (!$red) {
				    echo '<td style="backgroud-color:green"></td>';
				}
			    }
			}
			echo '</tr>';
	}
}
?>
</table>

<h5>Impression faite le <?php echo date("d/m/Y - h:i"); ?>.</h5>
<?php
require("../lib/footer.inc.php");
?>