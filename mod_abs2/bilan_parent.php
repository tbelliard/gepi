<?php
/**
 *
 * @version $Id: bilan_du_jour.php 5114 2010-08-26 15:29:50Z crob $
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
};

// mise à jour des droits dans la table droits
$sql="INSERT INTO `droits` ( `id` , `administrateur` , `professeur` , `cpe` , `scolarite` , `eleve` , `responsable` , `secours` , `autre` , `description` , `statut` )
VALUES ('/mod_abs2/bilan_parent.php', 'F', 'F', 'F', 'F', 'F', 'V', 'F', 'F', 'Affichage parents des absences de leurs enfants', '')
ON DUPLICATE KEY UPDATE `responsable` = 'V'";

$result = mysql_query($sql);


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

if ($utilisateur->getStatut()!="responsable") {
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


// Initialisation toutes absences

$dt_fin_toutes = isset($_POST["date_fin_toute_absence"]) ? new DateTime(str_replace("/",".",$_POST["date_fin_toute_absence"])) : date_create() ;

if ($dt_fin_toutes->format('m')>=8){
  $date_debut_toutes=$dt_fin_toutes->format('Y')."-08-01";
} else {
  $date_debut_toutes=($dt_fin_toutes->format('Y')-1)."-08-01";
}
$dt_debut_toutes = isset($_POST["date_debut_toute_absence"]) ? new DateTime(str_replace("/",".",$_POST["date_debut_toute_absence"])) : date_create($date_debut_toutes);

if ($dt_fin_toutes < $dt_debut_toutes){
  $dt = $dt_fin_toutes;
  $dt_fin_toutes = $dt_debut_toutes;
  $dt_debut_toutes = $dt;
}

$dt_debut_toutes->setTime(0,0,0);
$dt_fin_toutes->setTime(23,59,59);

//$date_interval= new DateInterval("P1D");
$date_traite="";
// Fin Initialisation toutes absences


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
$titre_page = "Absences";
require_once("../lib/header.inc");
include('menu_abs2.inc.php');
include('menu_bilans.inc.php');
?>
<div id="contain_div" class="css-panes">
<form id="choix_date" action="<?php $_SERVER['PHP_SELF']?>" method="post">
<h2>
  <label for="date_absence_eleve_1">Les saisies du</label>
    <input size="8" id="date_absence_eleve_1" name="date_absence_eleve" onchange="document.getElementById('choix_date').submit()" value="<?php echo $dt_date_absence_eleve->format('d/m/Y')?>" />
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
  </h2>
</form>

<!-- Absences du jour -->
<table style="border: 1px solid black;" cellpadding="5" cellspacing="5" title="Les absences du jour">
<?php $creneau_col = EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime();?>
	<tr>
		<th style="border: 1px solid black; background-color: gray; min-width: 300px; max-width: 500px;">Nom Pr&eacute;nom</th>
<?php
		//afficher les créneaux
		foreach(EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime() as $creneau){
?>
		<th style="border: 1px solid black; background-color: gray;">
		  <?php echo $creneau->getNomDefiniePeriode(); ?>
		</th>
<?php
		}
?>
	</tr>
<?php
$dt_debut = $dt_date_absence_eleve;
$dt_debut->setTime(0,0,0);
$dt_fin = clone $dt_date_absence_eleve;
$dt_fin->setTime(23,59,59);
?>
	<tr>
	  <td colspan="<?php echo ($creneau_col->count() + 1);?>"></td>
	</tr>

<?php
	$eleve_query = EleveQuery::create()
		->orderByNom()
		->useResponsableInformationQuery()->useResponsableEleveQuery()->filterByLogin($_SESSION['login'])->endUse()->endUse()
		->distinct();
	
	$eleve_col = $eleve_query->find();
	foreach($eleve_col as $eleve){
	  $affiche = false;
?>
	<tr>
	  <td>
<?php
	  echo $eleve->getNom().' '.$eleve->getPrenom();
?>
	  </td>
<?php
	  // On traite alors pour chaque créneau
	  foreach($creneau_col as $creneau) {
		$abs_col = $eleve->getAbsenceEleveSaisiesDuCreneau($creneau, $dt_date_absence_eleve);
		if ($abs_col->isEmpty()){
?>
	  <td> </td>
<?php
		} else {
		  foreach($abs_col as $abs) {
			if ($abs->getManquementObligationPresence()) {
			  if ($abs->getJustifiee()){
				if ($abs->getRetard()){
?>
	  <td style="background:aqua;">RJ</td>
<?php
				}else {
?>
	  <td style="background-color:blue;">J</td>
<?php
				}
			  } else {
				if ($abs->getRetard()){
?>
	  <td style="background:fuchsia;">RNJ</td>
<?php
				}else {
?>
	  <td style="background-color:red;">NJ</td>
<?php
				}
			  }
			} else if ($abs->getRetard()){
?>
	  <td style="background-color:lime;">R</td>
<?php
			} else {
?>
	  <td style="background-color:green;">C</td>
<?php
			}
			break;
		  }

		}
	  }
?>
	</tr>
<?php
	}
?>
</table>

  <p>
	<span style="background-color:red;">&nbsp;&nbsp;NJ&nbsp;&nbsp;</span>
	Manquement aux obligations scolaires : absence non justifiée
  </p>
  <p>
	<span style="background-color:fuchsia;">&nbsp;RNJ&nbsp;</span>
	Manquement aux obligations scolaires : Retard non justifiée
  </p>
  <p>
	<span style="background-color:blue;">&nbsp;&nbsp;&nbsp;J&nbsp;&nbsp;&nbsp;&nbsp;</span>
	Manquement aux obligations scolaires : absence justifiée
  </p>
  <p>
	<span style="background-color:aqua;">&nbsp;&nbsp;RJ&nbsp;&nbsp;</span>
	Manquement aux obligations scolaires : Retard justifiée
  </p>
  <p>
	<span style="background-color:green;">&nbsp;&nbsp;&nbsp;C&nbsp;&nbsp;&nbsp;</span>
	Absence cadre scolaire (Infirmerie, sortie scolaire...)
  </p>
  <p>
	<span style="background-color:lime;">&nbsp;&nbsp;&nbsp;R&nbsp;&nbsp;&nbsp;</span>
	Retard intercours
  </p>


<!-- Absences totales -->



<form id="toutes_abs" action="<?php $_SERVER['PHP_SELF']?>" method="post">
<h2>
  <label for="date_debut_toute_absence">Les absences entre le</label>
    <input size="8" id="date_debut_toute_absence" name="date_debut_toute_absence" onchange="document.getElementById('toutes_abs').submit()" value="<?php echo $dt_debut_toutes->format('d/m/Y')?>" />
    <script type="text/javascript">
	Calendar.setup({
	    inputField     :    "date_debut_toute_absence",     // id of the input field
	    ifFormat       :    "%d/%m/%Y",      // format of the input field
	    button         :    "date_debut_toute_absence",  // trigger for the calendar (button ID)
	    align          :    "Bl",           // alignment (defaults to "Bl")
	    singleClick    :    true
	});
    </script>
	<label for="date_fin_toute_absence"> et le</label>
	<input size="8" id="date_fin_toute_absence" name="date_fin_toute_absence" onchange="document.getElementById('toutes_abs').submit()" value="<?php echo $dt_fin_toutes->format('d/m/Y')?>" />
    <script type="text/javascript">
	Calendar.setup({
	    inputField     :    "date_fin_toute_absence",     // id of the input field
	    ifFormat       :    "%d/%m/%Y",      // format of the input field
	    button         :    "date_fin_toute_absence",  // trigger for the calendar (button ID)
	    align          :    "Bl",           // alignment (defaults to "Bl")
	    singleClick    :    true
	});
    </script>
    <button type="submit">Changer</button>
</h2>
</form>
<?php


	$eleve_query = EleveQuery::create()
		->orderByNom()
		->useResponsableInformationQuery()->useResponsableEleveQuery()->filterByLogin($_SESSION['login'])->endUse()->endUse()
		->useAbsenceEleveSaisieQuery()->filterByPlageTemps($dt_debut_toutes, $dt_fin_toutes)->endUse()
		->distinct();

	$eleve_col = $eleve_query->find();


	foreach($eleve_col as $eleve){

?>

<br />
<table style="border: 1px solid black;" cellpadding="5" cellspacing="5" title="Toutes les absences de <?php echo $eleve->getNom().' '.$eleve->getPrenom();?>">
<?php $creneau_col = EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime();?>
	<tr>
		<th style="border: 1px solid black; background-color: gray; min-width: 300px; max-width: 500px;"><?php echo $eleve->getNom().' '.$eleve->getPrenom();?></th>
<?php
		//afficher les créneaux
		foreach(EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime() as $creneau){
?>
		<th style="border: 1px solid black; background-color: gray;">
<?php

			echo $creneau->getNomDefiniePeriode();
?>
		  </th>
<?php
		}
?>
	</tr>
	<tr>
	  <td colspan="<?php echo ($creneau_col->count() + 1);?>"></td>
	</tr>
<?php
		unset ($date_actuelle);

		$date_actuelle=clone $dt_fin_toutes ;

		while ($date_actuelle >= $dt_debut_toutes) {
			foreach($eleve->getAbsenceEleveSaisiesDuJour($date_actuelle) as $abs) {
			if ($date_traite==$date_actuelle->format('d/m/Y')){
			  break;
			} else {
			  $date_traite=$date_actuelle->format('d/m/Y');
			}
?>
	<tr>
	  <td style="text-align:center;"><?php echo $date_actuelle->format('d/m/Y'); ?></td>
<?php
	  foreach($creneau_col as $creneau) {
		$abs_col = $eleve->getAbsenceEleveSaisiesDuCreneau($creneau, $abs->getDebutAbs());
		if ($abs_col->isEmpty()){
?>
	  <td></td>
<?php

		} else {
		  foreach($abs_col as $abs) {
			if ($abs->getManquementObligationPresence()) {
?>
<?php
			  if ($abs->getJustifiee()){
				if ($abs->getRetard()){
				  ?>
				  <td style="background:aqua;">RJ</td>
				  <?php
				}else {
?>
	  <td style="background-color:blue;">
		J
<?php
				}
			  }else{
				if ($abs->getRetard()){
				  ?>
				  <td style="background:fuchsia;">RNJ
				  <?php
				}else {
?>
	  <td style="background-color:red;">
		NJ
<?php
				}
			  }
			  break;
			} else {
			  if ($abs->getRetard()){
?>
	  <td style="background-color:lime;">
		R
<?php
			  } else {
?>
	  <td style="background-color:green;">
		C
<?php
			  }
			}
		  }
?>
	  </td>
<?php
		}
	  }
?>
	</tr>
<?php
	}
  //$date_actuelle = date_add($date_actuelle , $date_interval);

date_date_set($date_actuelle, $date_actuelle->format('Y'),$date_actuelle->format('m'), $date_actuelle->format('d')-1);

  }

?>
</table>
<?php
}






?>



  <p class="bold small">Impression faite le <?php echo date("d/m/Y - H:i"); ?>.</p>
</div>
<?php

require("../lib/footer.inc.php");
?>