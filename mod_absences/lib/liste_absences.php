<?php
/*
 * Copyright 2001, 2002 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Christian Chapel
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

$niveau_arbo = 2;
// Initialisations files
require_once("../../lib/initialisations.inc.php");
//mes fonctions
include("../lib/functions.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../../logout.php?auto=1");
    die();
};


if (!checkAccess()) {
    header("Location: ../../logout.php?auto=1");
    die();
}

//On vérifie si le module est activé
if (getSettingValue("active_module_absence")!='y') {
    die("Le module n'est pas activé.");
}

if($_GET['type'] == "D") $titre = "Liste des dispenses";
if($_GET['type'] == "A") $titre = "Liste des absences";
if($_GET['type'] == "I") $titre = "Liste des passages à l'infirmerie";
if($_GET['type'] == "R") $titre = "Liste des retards";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
    <title><?php echo $titre; ?></title>
    <link rel="stylesheet" href="../styles/mod_absences.css" type="text/css" />
    </head>
<body>
<?php
if (!isset($_GET['id_eleve']))
  {
      die("<p><b>Paramètres invalides !</b></p>\n</body>\n</html>");
  }
?>
<div class="centre">
<?php echo $titre; ?>
</div>
<?php
//affiche s'il y a quelque chose pour l'élève
$cpt_eleves = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve='".$_GET['id_eleve']."' and type_absence_eleve = '".$_GET['type']."'"),0);
if ($cpt_eleves!=0) {
?>
    <div style="width: 240px; margin: auto;">
    <table class="tableau_liste">
    <thead>
<?php
    $requete_dispense ="SELECT * FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve='".$_GET['id_eleve']."' and type_absence_eleve = '".$_GET['type']."' ORDER BY d_date_absence_eleve";
    $execution_dispense = mysql_query($requete_dispense);
    while ($donnee_dispense = mysql_fetch_array($execution_dispense))
    { //modif couleur selon motif didier
    if ($donnee_dispense['justify_absence_eleve'] == 'O') {$style = ' style="color: green;"';}
    if ($donnee_dispense['justify_absence_eleve'] =='T') {$style = ' style="color: orange;"';}
	if ($donnee_dispense['justify_absence_eleve'] == 'N') {$style = ' style="color: red;"';}
	?>
    <tr>
		<td>
		<?php
		if($donnee_dispense['d_date_absence_eleve'] != $donnee_dispense['a_date_absence_eleve']) {
			?>du<?php
		} else {
			?>le<?php
		}
		echo " <b>".date_frc($donnee_dispense['d_date_absence_eleve'])."</b>";
		if($donnee_dispense['d_date_absence_eleve'] != $donnee_dispense['a_date_absence_eleve']) {
			?><br />au <?php
			echo date_frc($donnee_dispense['a_date_absence_eleve']);
		}
		if($_GET['type'] == "D") {
			//modif couleur selon motif didier?><br /><?php echo "<span . $style . >".$donnee_dispense['info_absence_eleve']."</span>";
		}
		if($_GET['type'] == "I" or $_GET['type'] == "A") {
			?><br /><?php //modif couleur selon motif didier
			echo "de ".$donnee_dispense['d_heure_absence_eleve']." à ".$donnee_dispense['a_heure_absence_eleve']."<br /> <span . $style . >".motif_de($donnee_dispense['motif_absence_eleve'])."</span>";
		}
		if($_GET['type'] == "R" ) {
			?><br /><?php //modif couleur selon motif didier
			echo "à ".$donnee_dispense['d_heure_absence_eleve']."<br /> <span . $style . >".motif_de($donnee_dispense['motif_absence_eleve'])."</span>";
		} //modif didier motif absence?>
		<br />&nbsp;
		</td>
    </tr>
<?php } ?>
     </thead>
     </table>
     <br /><img src="./view_artichow_absences_petit.php?type_1=<?php echo $_GET['type']; ?>&amp;classe_1=&amp;eleve_1=<?php echo $_GET['id_eleve']; ?>" alt="graphiques" />
     </div>
<?php } else { ?><br /><div class="centre">il n'y a pas de <?php
               if($_GET['type'] == "D") { ?>dispense<?php }
               if($_GET['type'] == "A") { ?>d'absence<?php }
               if($_GET['type'] == "I") { ?>de passage à l'infirmerie<?php }
               if($_GET['type'] == "R") { ?>de retard<?php }  ?></div><?php
         } ?>
      <br /><div class="centre"><a href="javascript:window.close();">Fermer la fenetre</a></div>
</body>
</html>
