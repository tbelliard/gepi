<?php

/**
 * @version $Id$
 *
 * Fichier destiné à visionner le bilan de la journée des absences heure par heure et cours par cours
 * en ordonnant le classement des élèves par classe et par ordre alphabétique.
 *
 * @copyright 2007
 */

$niveau_arbo = 2;
// Initialisations files
require_once("../../lib/initialisations.inc.php");
// Les fonctions utiles
include("../lib/functions.php");
require_once("../../edt_organisation/fonctions_edt.php");
require_once("../../edt_organisation/fonctions_calendrier.php");

// Resume session
$resultat_session = resumeSession();
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
$titre_page = "Bilan quotidien des absences.";
require_once("../../lib/header.inc");
//+++++++++FIN HEADER++++++++++++++

// Initialisation des variables
$date_choisie = isset($_POST["date_choisie"]) ? $_POST["date_choisie"] : (date("d/m/Y"));
$choix_date = explode("/", $date_choisie);
$date_choisie_ts = mktime(0,0,0, $choix_date[1], $choix_date[0], $choix_date[2]);

// On récupère le nom des créneaux

$creneaux = retourne_creneaux();

// Fonctions des absences
	function suivi_absence($creneau_id, $eleve_id, $date_choisie){
		// On récupère les horaires de début du créneau en question et on les transforme en timestamp UNIX
			$choix_date = explode("/", $date_choisie);
			$date_choisie_ts = mktime(0,0,0, $choix_date[1], $choix_date[0], $choix_date[2]);
		if (date("w", $date_choisie_ts) == getSettingValue("creneau_different")) {
			$req_sql = mysql_query("SELECT heuredebut_definie_periode, heurefin_definie_periode FROM absences_creneaux_bis WHERE id_definie_periode = '".$creneau_id."'");
		}
		else {
			$req_sql = mysql_query("SELECT heuredebut_definie_periode, heurefin_definie_periode FROM absences_creneaux WHERE id_definie_periode = '".$creneau_id."'");
		}
		$rep_sql = mysql_fetch_array($req_sql);
		$heuredeb = explode(":", $rep_sql["heuredebut_definie_periode"]);
		$heurefin = explode(":", $rep_sql["heurefin_definie_periode"]);
		//$d_date = explode("/", $d_date_absence_eleve);
		$ts_heuredeb = mktime($heuredeb[0], $heuredeb[1], 0, $choix_date[1], $choix_date[0], $choix_date[2]);
		$ts_heurefin = mktime($heurefin[0], $heurefin[1], 0, $choix_date[1], $choix_date[0], $choix_date[2]);
		// On teste si l'élève était absent ou en retard le cours du créneau (on ne teste que le début du créneau)
		$req = mysql_query("SELECT id, retard_absence FROM absences_rb WHERE eleve_id = '".$eleve_id."' AND debut_ts = '".$ts_heuredeb."'");
		$rep = mysql_fetch_array($req);
			// S'il est marqué absent A -> fond rouge
		if ($rep["retard_absence"] == "A") {
			return "<td style=\"border: 1px solid black; background-color: #ffd4d4; color: red;\"><b>A</b></td>";
		}
			// S'il est marqué en retard R -> fond vert
		else if ($rep["retard_absence"] == "R") {
			return "<td style=\"border: 1px solid black; background-color: #d7ffd4; color: green;\"><b>R</b></td>";
		}
		else
		return "<td style=\"border: 1px solid black;\"></td>";
	}

?>
	<form name="autre_date" method="post" action="bilan_absences_quotidien.php">
<table>
	<tr>
		<td>
			<h2>Bilan des absences du <?php echo date_frl(date_sql($date_choisie)); ?>.</h2>
		</td>
		<td> -
			<a href="./voir_absences_viescolaire.php">Retour</a>
		</td>
		<td> - Modifier la date
		</td>
		<td>
		<input type="text" name="date_choisie" style="maxlenght: 10;" size="10" value="<?php echo $date_choisie; ?>" />
		<a href="#calend" onclick="window.open('../../lib/calendrier/pop.calendrier.php?frm=autre_date&amp;ch=date_choisie','calendrier','width=350,height=170,scrollbars=0').focus();">
		<img src="../../lib/calendrier/petit_calendrier.gif" alt="" border="0" /></a>
		</td>
		<td>
		<input type="submit" name="valider" title="valider" />
		</td>
	</tr>
</table>
	</form>
<table style="border: 1px solid black;" cellpadding="5" cellspacing="5">

	<tr>
	<td colspan="<?php echo(count($creneaux) + 2); ?>">
		<table border="0" cellspacing="0" cellpadding="1">
		<tr>
		<td style="background-color: #d7ffd4; width: 15px; color: green;"><b>R</b></td><td> Retard</td><td></td>
		<td style="background-color: #ffd4d4; width: 15px; color: red;"><b>A</b></td><td> Absence</td>
		</tr>
		</table>
	</td>
	</tr>

	<tr>
		<th style="border: 1px solid black; background-color: grey;">Classe</th>
		<th style="border: 1px solid black; background-color: grey; width: 300px;">Nom Pr&eacute;nom</th>
<?php //afficher les créneaux
			$i=0;
		while($i<count($creneaux)){
			echo "<th style=\"border: 1px solid black; background-color: grey;\">".$creneaux[$i]."</th>\n";
			$i++;
		}
?>
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

for($i=0; $i<$nbre; $i++) {
	// On récupère le nom de toutes les classes
	$rep_classe[$i]["classe"] = mysql_result($req_classe, $i, "classe");
	$rep_classe[$i]["id"] = mysql_result($req_classe, $i, "id");
	echo '
		<tr>
			<td><a href="bilan_absences_classe.php?id_classe='.$rep_classe[$i]["id"].'">'.$rep_classe[$i]["classe"].'</a></td>
			<td colspan="'.(count($creneaux) + 1).'"></td>
		</tr>
		';
	// On traite alors l'affichage de tous les élèves de chaque classe
	$req_absences = mysql_query("SELECT DISTINCT eleve_id FROM absences_rb WHERE eleve_id != 'appel' AND debut_ts >= '".$time_actu_deb."' AND fin_ts <= '".$time_actu_fin."' ORDER BY eleve_id");
	$nbre_a = mysql_num_rows($req_absences);

	for($b=0; $b<$nbre_a; $b++){
		$rep_absences[$b]["eleve_id"] = mysql_result($req_absences, $b, "eleve_id");
		$req_id_classe = mysql_fetch_array(mysql_query("SELECT id_classe FROM j_eleves_classes WHERE login = '".$rep_absences[$b]["eleve_id"]."'"));

		// On affiche l'élève en fonction de la classe à laquelle il appartient
		if ($rep_classe[$i]["id"] == $req_id_classe["id_classe"]) {
			// On récupère nom et prénom de l'élève
			$rep_nom = mysql_fetch_array(mysql_query("SELECT nom, prenom FROM eleves WHERE login = '".$rep_absences[$b]["eleve_id"]."'"));
			echo '<tr>
			<td></td>
			<td>'.$rep_nom["nom"].' '.$rep_nom["prenom"].'</td>
			';
			// On traite alors pour chaque créneau
			if (getSettingValue("creneau_different") != 'n') {
				if (date("w") == getSettingValue("creneau_different")) {
					$req_creneaux = mysql_query("SELECT id_definie_periode FROM absences_creneaux_bis WHERE type_creneaux != 'pause'");
				}else {
					$req_creneaux = mysql_query("SELECT id_definie_periode FROM absences_creneaux WHERE type_creneaux != 'pause'");
				}
			}else {
				$req_creneaux = mysql_query("SELECT id_definie_periode FROM absences_creneaux WHERE type_creneaux != 'pause'");
			}
			$nbre_creneaux = mysql_num_rows($req_creneaux);
			for($a=0; $a<$nbre_creneaux; $a++){
				$id_creneaux[$a]["id"] = mysql_result($req_creneaux, $a, "id_definie_periode");
				echo '
				'.suivi_absence($id_creneaux[$a]["id"], $rep_absences[$b]["eleve_id"], $date_choisie);
			} // for $a

			echo '
			</tr>';
		} else {
			echo "";
		}
	} // for $b
} // for $i
?>
</table>

<h5>Impression faite le <?php echo date("d/m/Y - h:i"); ?>.</h5>
<?php
echo '<p class="red">'.$attention.'</p>'; // message d'information si le jour demandé est un jour fermé normalement
require("../../lib/footer.inc.php");
?>