<?php
/**
 *
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

$niveau_arbo = 2;
// Initialisations files
require_once("../../lib/initialisations.inc.php");
// Les fonctions utiles
include("../lib/functions.php");
require_once("../../edt_organisation/fonctions_edt.php");
require_once("../../edt_organisation/fonctions_calendrier.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
    header("Location: ../../logout.php?auto=1");
die();
};

// Sécurité
if (!checkAccess()) {
    header("Location: ../../logout.php?auto=1");
die();
}

// Insertion du style spécifique
$style_specifique = "mod_absences/gestion/style_absences";

//+++++++++++++++++++++++++++++++++
//++++++++++HEADER+++++++++++++++++
$titre_page = "Bilan quotidien des repas.";
require_once("../../lib/header.inc.php");
//+++++++++FIN HEADER++++++++++++++

// Initialisation des variables
$date_choisie = isset($_POST["date_choisie"]) ? $_POST["date_choisie"] : (date("d/m/Y"));
$choix_date = explode("/", $date_choisie);
$date_choisie_ts = mktime(0,0,0, $choix_date[1], $choix_date[0], $choix_date[2]);

$date_voulue = $choix_date[2]."-".$choix_date[1]."-".$choix_date[0];
// On récupère le nom des créneaux



?>
<form name="autre_date" method="post" action="bilan_repas_quotidien.php">

	<a href="./voir_absences_viescolaire.php">
		<img src="../../images/icons/back.png" alt="Retour" title="Retour" class="back_link" />&nbsp;Retour
	</a> -

	<label for="dateChoisie" style="cursor: pointer;">Voir le bilan du&nbsp;</label>
	<input type="text" name="date_choisie" id="dateChoisie" style="maxlenght: 10;" size="10" value="<?php echo $date_choisie; ?>" />
	<a href="#calend" onclick="window.open('../../lib/calendrier/pop.calendrier.php?frm=autre_date&amp;ch=date_choisie','calendrier','width=350,height=170,scrollbars=0').focus();">
		<img src="../../lib/calendrier/petit_calendrier.gif" alt="" border="0" />
	</a>
	<input type="submit" name="valider" title="valider" />

</form>

	<h3 class="gepi" style="margin-bottom: 2px; margin-left: 4px;">Bilan des repas du <?php echo date_frl(date_sql($date_choisie)); ?>.</h3>
<table style="border: 1px solid black;" cellpadding="5" cellspacing="5">

	<tr>
	  <td colspan="2">
	  </td>
	</tr>
	<tr>
		<th style="border: 1px solid black; background-color: grey;">Classe</th>
		<th style="border: 1px solid black; background-color: grey; width: 300px;">Nom Pr&eacute;nom</th>
	</tr>

<?php
// ===================== Quelques variables utiles ===============
	// On détermine le jour en Français actuel
	$jour_choisi = retourneJour(date("w", $date_choisie_ts));
	$query = mysql_query("SELECT ouverture_horaire_etablissement, fermeture_horaire_etablissement FROM horaires_etablissement WHERE jour_horaire_etablissement = '".$jour_choisi."'");
	$attention = ''; // message de prévention au cas où $query ne retourne rien

	$nbre_rep = mysql_num_rows($query);
	if ($nbre_rep >= 1) {
		// Avec le résultat, on calcule les timestamps UNIX
		$req = mysql_fetch_array($query);
		$rep_deb = explode(":", $req["ouverture_horaire_etablissement"]);
		$rep_fin = explode(":", $req["fermeture_horaire_etablissement"]);
		$time_actu_deb = mktime($rep_deb[0], $rep_deb[1], 0, $choix_date[1], $choix_date[0], $choix_date[2]);
		$time_actu_fin = mktime($rep_fin[0], $rep_fin[1], 0, $choix_date[1], $choix_date[0], $choix_date[2]);
	}else{
		// Si on ne récupère rien, on donne par défaut les ts du jour actuel
		$time_actu_deb = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
		$time_actu_fin = mktime(23, 59, 0, date("m"), date("d"), date("Y"));
		// et on affiche un petit message
		$attention = "L'établissement est censé être fermé aujourd'hui.";
	}

// Affichage des noms répartis par classe
$req_classe = mysql_query("SELECT id, classe FROM classes ORDER BY classe");
$nbre = mysql_num_rows($req_classe);

for($i = 0; $i < $nbre; $i++) {
	// On récupère le nom de toutes les classes
	$rep_classe[$i]["classe"] = mysql_result($req_classe, $i, "classe");
	$rep_classe[$i]["id"] = mysql_result($req_classe, $i, "id");
	echo '
		<tr>
			<td>'.$rep_classe[$i]["classe"].'</td>
			<td colspan="2"></td>
		</tr>
		';
	// On traite alors l'affichage de tous les élèves de chaque classe
	$sql_repas = "SELECT DISTINCT eleve_id FROM absences_repas WHERE date_repas = '$date_voulue' ORDER BY eleve_id";
	//echo $sql_repas;
	$req_repas = mysql_query($sql_repas);

	$nbre_a = mysql_num_rows($req_repas);

	for($b = 0; $b < $nbre_a; $b++){
		$rep_absences[$b]["eleve_id"] = mysql_result($req_repas, $b, "eleve_id");
		$req_id_classe = mysql_fetch_array(mysql_query("SELECT id_classe FROM j_eleves_classes WHERE login = '".$rep_absences[$b]["eleve_id"]."'"));

		// On affiche l'élève en fonction de la classe à laquelle il appartient
		if ($rep_classe[$i]["id"] == $req_id_classe["id_classe"]) {
			// On récupère nom et prénom de l'élève
			$rep_nom = mysql_fetch_array(mysql_query("SELECT nom, prenom FROM eleves WHERE login = '".$rep_absences[$b]["eleve_id"]."'"));
			echo '<tr>
			<td></td>
			<td>'.$rep_nom["nom"].' '.$rep_nom["prenom"].'</td>
			';
			echo '
			</tr>';
		} else {
			echo "";
		}
	} // for $b
} // for $i

echo "Nombre de repas : $nbre_a";

?>
</table>

<h5>Impression faite le <?php echo date("d/m/Y - h:i"); ?>.</h5>
<?php
echo '<p class="red">'.$attention.'</p>'; // message d'information si le jour demandé est un jour fermé normalement
require("../../lib/footer.inc.php");
?>