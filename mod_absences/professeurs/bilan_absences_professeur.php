<?php

/**
 * @version $Id$
 *
 * destiné à permettre de visionner le bilan de la journée des absences heure par heure et cours par cours
 * en ordonnant le classement des élèves par classe et par ordre alphabétique.
 *
 * @copyright 2008
 */
$niveau_arbo = 2;
// Initialisations files
require_once("../../lib/initialisations.inc.php");
//mes fonctions
include("../lib/functions.php");
// ainsi que les fonctions de l'EdT pour la gestion des créneaux
require_once("../../edt_organisation/fonctions_calendrier.php");
require_once("../../edt_organisation/fonctions_edt.php");

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
$style_specifique = "mod_absences/styles/bilan_absences";

//+++++++++++++++++++++++++++++++++
//++++++++++HEADER+++++++++++++++++
$titre_page = "Bilan des absences.";
require_once("../../lib/header.inc.php");
//+++++++++FIN HEADER++++++++++++++

// Initialisation des variables
$date_choisie_deb = isset($_POST["date_choisie_deb"]) ? $_POST["date_choisie_deb"] : (date("d/m/Y"));
$date_choisie = isset($_POST["date_choisie"]) ? $_POST["date_choisie"] : (date("d/m/Y"));
$choix_date_deb = explode("/", $date_choisie_deb);
$choix_date_fin = explode("/", $date_choisie);
$date_choisie_deb_ts = mktime(0,0,0, $choix_date_deb[1], $choix_date_deb[0], $choix_date_deb[2]);
$date_choisie_fin_ts = mktime(23,59,0, $choix_date_fin[1], $choix_date_fin[0], $choix_date_fin[2]);

// On récupère le nom des créneaux

$creneaux = retourne_creneaux();

// Fonctions des absences
	function suivi_absence_prof($date_choisie_deb, $eleve_id, $date_choisie){
		// On récupère les horaires de début du créneau en question et on les transforme en timestamp UNIX
			$choix_date = explode("/", $date_choisie);
			$date_choisie_ts = mktime(0,0,0, $choix_date[1], $choix_date[0], $choix_date[2]);
		if (date("w", $date_choisie_ts) == getSettingValue("creneau_different")) {
			$req_sql = mysql_query("SELECT heuredebut_definie_periode, heurefin_definie_periode FROM edt_creneaux_bis WHERE id_definie_periode = '".$creneau_id."'");
		}
		else {
			$req_sql = mysql_query("SELECT heuredebut_definie_periode, heurefin_definie_periode FROM edt_creneaux WHERE id_definie_periode = '".$creneau_id."'");
		}
		$rep_sql = mysql_fetch_array($req_sql);
		$heuredeb = explode(":", $rep_sql["heuredebut_definie_periode"]);
		$heurefin = explode(":", $rep_sql["heurefin_definie_periode"]);
		$d_date = explode("/", $d_date_absence_eleve);
		$ts_heuredeb = mktime($heuredeb[0], $heuredeb[1], 0, $choix_date[1], $choix_date[0], $choix_date[2]);
		$ts_heurefin = mktime($heurefin[0], $heurefin[1], 0, $choix_date[1], $choix_date[0], $choix_date[2]);
		// On teste si l'élève était absent ou en retard le cours du créneau (on ne teste que le début du créneau)
		$req = mysql_query("SELECT id, retard_absence FROM absences_rb WHERE eleve_id = '".$eleve_id."' AND debut_ts = '".$ts_heuredeb."'");
		$rep = mysql_fetch_array($req);
			// S'il est marqué absent A -> fond rouge
		if ($rep["retard_absence"] == "A") {
			$retour = "<td style=\"border: 1px solid black; background-color: #ffd4d4; color: red;\"><b>A</b></td>";
		}
			// S'il est marqué en retard R -> fond vert
		else if ($rep["retard_absence"] == "R") {
			$retour = "<td style=\"border: 1px solid black; background-color: #d7ffd4; color: green;\"><b>R</b></td>";
		} else {
			$retour = "<td style=\"border: 1px solid black;\"></td>";
		}
		return $retour;
	}

?>
	<form name="autre_date" method="post" action="bilan_absences_professeur.php">
<table summary="Mise en page du haut (d&eacute;sol&eacute;)">
	<tr>
		<td>
			<a href="prof_ajout_abs.php">Retour</a>
		 </td>
		<td> - D&eacute;but :
		</td>
		<td>
		<input type="text" name="date_choisie_deb" maxlenght="10" size="10" value="<?php echo $date_choisie_deb; ?>" />
		<a href="#calend" onclick="window.open('../../lib/calendrier/pop.calendrier.php?frm=autre_date&amp;ch=date_choisie_deb','calendrier','width=350,height=170,scrollbars=0').focus();">
		<img src="../../lib/calendrier/petit_calendrier.gif" alt="" border="0" /></a>
		</td>
		<td> - Fin :
		</td>
		<td>
		<input type="text" name="date_choisie" maxlenght="10" size="10" value="<?php echo $date_choisie; ?>" />
		<a href="#calend" onclick="window.open('../../lib/calendrier/pop.calendrier.php?frm=autre_date&amp;ch=date_choisie','calendrier','width=350,height=170,scrollbars=0').focus();">
		<img src="../../lib/calendrier/petit_calendrier.gif" alt="" border="0" /></a>
		</td>
		<td>
		<input type="submit" name="valider" title="valider" />
		</td>
	</tr>
</table>
	</form>
<p>Bilan des absences que vous avez saisies.</p>
<table summary="Bilan des absences saisies par le professeur" style="border: 1px solid black;" cellpadding="5" cellspacing="5">

	<tr>
	<td>
<!--
		<table border="0" cellspacing="0" cellpadding="1">
		<tr>
		<td style="background-color: #d7ffd4; width: 15px; color: green;"><b>R</b></td><td> Retard</td><td></td>
		<td style="background-color: #ffd4d4; width: 15px; color: red;"><b>A</b></td><td> Absence</td>
		</tr>
		</table>
-->
	</td>
	</tr>

	<tr>
		<th style="border: 1px solid black; background-color: grey;">Classe</th>
		<th style="border: 1px solid black; background-color: grey; width: 300px;">Nom Pr&eacute;nom</th>
		<th style="width: 500px; border: 1px solid black; background-color: grey;">Dates des absences saisies</th>
	</tr>


<?php
	// Quelques variables utiles
	$jour_choisi = retourneJour(date("w"));
	$query = mysql_query("SELECT ouverture_horaire_etablissement, fermeture_horaire_etablissement FROM horaires_etablissement WHERE jour_horaire_etablissement = '".$jour_choisi."'");

	$nbre_rep = mysql_num_rows($query);
	if ($nbre_rep >= 1) {
		// Avec le résultat, on calcule les timestamps UNIX
		$req = mysql_fetch_array($query);
		$rep_deb = explode(":", $req["ouverture_horaire_etablissement"]);
		$rep_fin = explode(":", $req["fermeture_horaire_etablissement"]);
		$time_actu_deb = mktime($rep_deb[0], $rep_deb[1], 0, $choix_date_deb[1], $choix_date_deb[0], $choix_date_deb[2]);
		$time_actu_fin = mktime($rep_fin[0], $rep_fin[1], 0, $choix_date_fin[1], $choix_date_fin[0], $choix_date_fin[2]);
	}


// Affichage des noms répartis par classe
$req_classe = mysql_query("SELECT id, classe FROM classes ORDER BY classe");
$nbre = mysql_num_rows($req_classe);

for($i=0; $i<$nbre; $i++){
	// On récupère le nom de toutes les classes
	$rep_classe[$i]["classe"] = mysql_result($req_classe, $i, "classe");
	$rep_classe[$i]["id"] = mysql_result($req_classe, $i, "id");
	echo '
		<tr>
			<td><a href="bilan_absences_classe.php?id_classe='.$rep_classe[$i]["id"].'">'.$rep_classe[$i]["classe"].'</a></td>
			<td colspan="2"></td>
		</tr>
		';
	// On traite alors l'affichage de tous les élèves de chaque classe
	$req_absences = mysql_query("SELECT DISTINCT eleve_id FROM absences_rb WHERE eleve_id != 'appel' AND date_saisie >= '".$date_choisie_deb_ts."' AND date_saisie <= '".$date_choisie_fin_ts."' AND retard_absence = 'A' AND login_saisie = '".$_SESSION["login"]."' ORDER BY eleve_id");
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
			<td>
			';
			// On affiche toutes les dates où l'élève a été absent
			$req_eleve = mysql_query("SELECT debut_ts FROM absences_rb WHERE eleve_id = '".$rep_absences[$b]["eleve_id"]."' AND debut_ts >= '".$date_choisie_deb_ts."' AND fin_ts <= '".$date_choisie_fin_ts."' AND login_saisie = '".$_SESSION["login"]."' AND retard_absence = 'A'");
			$nbre_absences = mysql_num_rows($req_eleve);

			for($ab=0; $ab<$nbre_absences; $ab++) {
				$rep_abs[$ab]["debut_ts"] = mysql_result($req_eleve, $ab, "debut_ts");
				$aff_abs[$ab] = date("d/m/Y", $rep_abs[$ab]["debut_ts"]);
				echo
				$aff_abs[$ab].' -
				';
			}

	echo '
			</td>
		</tr>
		';
		} //if $req_id_classe["classe"]...
		else {
		echo "";
		}

	} // for $b
} // for $i
?>
</table>

<h5>Impression faite le <?php echo date("d/m/Y - h:i"); ?>.</h5>
<?php
require("../../lib/footer.inc.php");
?>