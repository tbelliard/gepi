<?php

/**
 * Fichier de Julien Jocal pour la Vie Scolaire du collège de Sauveterre-de-Guyenne
 * destiné à permettre de visionner les absences heure par heure et cours par cours
 * en ordonnant le classement des élèves par classe et par ordre alphabétique.
 *
 * @version $Id$
 * @copyright 2007
 */
$niveau_arbo = 2;
// Initialisations files
require_once("../../lib/initialisations.inc.php");
//mes fonctions
include("../lib/functions.php");

// Resume session
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
header("Location: ../../utilisateurs/mon_compte.php?change_mdp=yes");
die();
} else if ($resultat_session == '0') {
    header("Location: ../../logout.php?auto=1");
die();
};
// Il faudra remettre cette sécurité
// INSERT INTO droits VALUES ('/mod_absences/gestion/voir_absences_viescolaire.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'Visionner les absences du jour', '');
if (!checkAccess()) {
    header("Location: ../../logout.php?auto=1");
die();
}

// Initialisation des variables
$choix_creneau = isset($_POST["choix_creneau"]) ? $_POST["choix_creneau"] : (isset($_GET["choix_creneau"]) ? $_GET["choix_creneau"] : NULL);
$vers_absence = isset($_GET["vers_absence"]) ? $_GET["vers_absence"] : NULL;
$vers_retard = isset($_GET["vers_retard"]) ? $_GET["vers_retard"] : NULL;


//======Quelques variables utiles===========
$date_jour = date("d/m/Y");
$date_mysql = date("Y-m-d");
$heure_mysql = date("H:i:s");

//**************** EN-TETE *****************
$titre_page = "Les absents du collège.";
require_once("../../lib/header.inc");
//************** FIN EN-TETE ***************
// style_edt en insertion
echo '
<link href="../../edt_organisation/style_edt.css" rel="stylesheet" type="text/css" />
';

	// Traitement du passage entre absence et retard
	// Pour le collège j'ai ajouté OR $_SESSION["login"] == "VISCO" mais il faudra l'enlever
	if ($_SESSION["statut"] == "cpe") {
		if (isset($vers_absence)) {
			$cgt_RA = mysql_query("UPDATE absences_rb SET retard_absence = 'A' WHERE id = '".$vers_absence."'");
		}
		else if (isset($vers_retard)) {
			$cgt_RA = mysql_query("UPDATE absences_rb SET retard_absence = 'R' WHERE id = '".$vers_retard."'");
		}
	}




	// Préparation de la requête quand un créneau est choisi
$aff_aid_absences = "Les groupes :";
if (isset($choix_creneau)) {
		// On transforme les horaires du créneau en timestamp UNIX sur la date du jour
		$ex_horaire = explode(":", $choix_creneau);
		$abs_deb_ts = mktime($ex_horaire[0], $ex_horaire[1], 0, date("m"), date("d"), date("Y"));
		$abs_fin_ts = mktime($ex_horaire[3], $ex_horaire[4], 0, date("m"), date("d"), date("Y"));
	$sql = "SELECT DISTINCT id, eleve_id, retard_absence, groupe_id FROM absences_rb WHERE (debut_ts = '".$abs_deb_ts."' OR fin_ts = '".$abs_fin_ts."') ORDER BY eleve_id";
	$req = mysql_query($sql) OR DIE('Impossible de lister les absents.');
	//$rep_absences = mysql_fetch_array($req);
	$nbre_rep = mysql_num_rows($req);
	for($a=0; $a<$nbre_rep; $a++){
		$rep_absences[$a]["id_abs"] = mysql_result($req, $a, "id");
		$rep_absences[$a]["eleve_id"] = mysql_result($req, $a, "eleve_id");
		$rep_absences[$a]["retard_absence"] = mysql_result($req, $a, "retard_absence");
		$rep_absences[$a]["groupe_id"] = mysql_result($req, $a, "groupe_id");
	}
} // if (isset($choix_creneau))


/*==============AFFICHAGE PAGE=============*/

// On le fait à la main, mais il faudra voir pour automatiser tout ça.
$td_classe61 = "<h2 style=\"color: red;\">6EME1</h2>";
$td_classe62 = "<h2 style=\"color: red;\">6EME2</h2>";
$td_classe63 = "<h2 style=\"color: red;\">6EME3</h2>";
$td_classe64 = "<h2 style=\"color: red;\">6EME4</h2>";
$td_classe65 = "<h2 style=\"color: red;\">6EME5</h2>";
$td_classe51 = "<h2 style=\"color: red;\">5EME1</h2>";
$td_classe52 = "<h2 style=\"color: red;\">5EME2</h2>";
$td_classe53 = "<h2 style=\"color: red;\">5EME3</h2>";
$td_classe54 = "<h2 style=\"color: red;\">5EME4</h2>";
$td_classe41 = "<h2 style=\"color: red;\">4EME1</h2>";
$td_classe42 = "<h2 style=\"color: red;\">4EME2</h2>";
$td_classe43 = "<h2 style=\"color: red;\">4EME3</h2>";
$td_classe44 = "<h2 style=\"color: red;\">4EME4</h2>";
$td_classe31 = "<h2 style=\"color: red;\">3EME1</h2>";
$td_classe32 = "<h2 style=\"color: red;\">3EME2</h2>";
$td_classe33 = "<h2 style=\"color: red;\">3EME3</h2>";
$td_classe34 = "<h2 style=\"color: red;\">3EME4</h2>";
$td_classeupi = "<h2 style=\"color: red;\">UPI</h2>";

for($i=0; $i<$nbre_rep; $i++) {
	if ($rep_absences[$i]["eleve_id"] != "appel") {
		$rep_nom = mysql_fetch_array(mysql_query("SELECT nom, prenom FROM eleves WHERE login = '".$rep_absences[$i]["eleve_id"]."'"));
		$req_classe = mysql_fetch_array(mysql_query("SELECT id_classe FROM j_eleves_classes WHERE login = '".$rep_absences[$i]["eleve_id"]."'"));
		$rep_classe = mysql_fetch_array(mysql_query("SELECT classe FROM classes WHERE id = '".$req_classe[0]."'"));
	}
	else if ($rep_absences[$i]["eleve_id"] == "appel") {
		// On vide les variables inutiles
		$rep_nom["nom"] = "";
		$rep_nom["prenom"] = "";
		// On explose poour vérifier qu'il ne s'agit pas d'un aid
		$verif_aid = explode(":", $rep_absences[$i]["groupe_id"]);
		if ($verif_aid[0] == "AID") {
			$rep_aid = mysql_fetch_array(mysql_query("SELECT nom FROM aid WHERE id = '".$verif_aid[1]."'")) or die ('erreur 1c : '.mysql_error());
			$req_prof = mysql_fetch_array(mysql_query("SELECT login_saisie FROM absences_rb WHERE id = '".$rep_absences[$i]["id_abs"]."'")) or die ('erreur 1a : '.mysql_error());
			$rep_prof = mysql_fetch_array(mysql_query("SELECT nom, prenom FROM utilisateurs WHERE login = '".$req_prof["login_saisie"]."'")) or die ('erreur 1b : '.mysql_error());
			// On construit alors l'affichage de cette info qui doit permettre à la vie scolaire de savoir
			// quand un prof a fait l'appel alors qu'il est avec un aid
			$aff_aid_absences .= "".$rep_prof["nom"]." ".$rep_prof["prenom"]." a fait l'appel avec le groupe ".$rep_aid["nom"]."<br />";
			$rep_classe[0] = "";
		} else {
			$req_classe = mysql_fetch_array(mysql_query("SELECT id_classe FROM j_groupes_classes WHERE id_groupe = '".$rep_absences[$i]["groupe_id"]."'"));
			$rep_classe = mysql_fetch_array(mysql_query("SELECT classe FROM classes WHERE id = '".$req_classe[0]."'"));
		}
	}

	// On vérifie l'état de la saisie absence ou retard ou sans absent (signifier que l'appel a bien été effectué
		if ($rep_absences[$i]["eleve_id"] == "appel") {
				// On récupère le nom de la matière
			$rep_matiere = mysql_fetch_array(mysql_query("SELECT description FROM groupes WHERE id = '".$rep_absences[$i]["groupe_id"]."'"));
			$etat = "<span style=\"color: brown; font-style: bold;\">L'appel a bien été effectué par ".$rep_matiere["description"].".</span>";
			$modif = "";
			$modif_f = "";
		}
		else if ($rep_absences[$i]["retard_absence"] == "R") {
			$etat = " (retard)";
			$modif = "<a href=\"./voir_absences_viescolaire.php?vers_absence=".$rep_absences[$i]["id_abs"]."&choix_creneau=".$choix_creneau."\" title=\"En retard\" style=\"color: green;\">";
			$modif_f = "</a>";
		}
		else {
			$etat = "";
			$modif = "<a href=\"./voir_absences_viescolaire.php?vers_retard=".$rep_absences[$i]["id_abs"]."&choix_creneau=".$choix_creneau."\" title=\"Absent\"><b>";
			$modif_f = "</a></b>";
		}

	// Seul le CPE peut modifier une absence vers retard et vice-versa
		if ($_SESSION["statut"] != "cpe") {
			$modif = "";
			$modif_f = "";
		}


		if ($rep_classe[0] == "6EME1") {
			$td_classe61 .= $modif.$rep_nom["nom"]." ".$rep_nom["prenom"].$etat.$modif_f."<br />\n";
		}
		else if ($rep_classe[0] == "6EME2") {
			$td_classe62 .= $modif.$rep_nom["nom"]." ".$rep_nom["prenom"].$etat.$modif_f."<br />\n";
		}
		else if ($rep_classe[0] == "6EME3") {
			$td_classe63 .= $modif.$rep_nom["nom"]." ".$rep_nom["prenom"].$etat.$modif_f."<br />\n";
		}
		else if ($rep_classe[0] == "6EME4") {
			$td_classe64 .= $modif.$rep_nom["nom"]." ".$rep_nom["prenom"].$etat.$modif_f."<br />\n";
		}
		else if ($rep_classe[0] == "6EME5") {
			$td_classe65 .= $modif.$rep_nom["nom"]." ".$rep_nom["prenom"].$etat.$modif_f."<br />\n";
		}
		else if ($rep_classe[0] == "5EME1") {
			$td_classe51 .= $modif.$rep_nom["nom"]." ".$rep_nom["prenom"].$etat.$modif_f."<br />\n";
		}
		else if ($rep_classe[0] == "5EME2") {
			$td_classe52 .= $modif.$rep_nom["nom"]." ".$rep_nom["prenom"].$etat.$modif_f."<br />\n";
		}
		else if ($rep_classe[0] == "5EME3") {
			$td_classe53 .= $modif.$rep_nom["nom"]." ".$rep_nom["prenom"].$etat.$modif_f."<br />\n";
		}
		else if ($rep_classe[0] == "5EME4") {
			$td_classe54 .= $modif.$rep_nom["nom"]." ".$rep_nom["prenom"].$etat.$modif_f."<br />\n";
		}
		else if ($rep_classe[0] == "4EME1") {
			$td_classe41 .= $modif.$rep_nom["nom"]." ".$rep_nom["prenom"].$etat.$modif_f."<br />\n";
		}
		else if ($rep_classe[0] == "4EME2") {
			$td_classe42 .= $modif.$rep_nom["nom"]." ".$rep_nom["prenom"].$etat.$modif_f."<br />\n";
		}
		else if ($rep_classe[0] == "4EME3") {
			$td_classe43 .= $modif.$rep_nom["nom"]." ".$rep_nom["prenom"].$etat.$modif_f."<br />";
		}
		else if ($rep_classe[0] == "4EME4") {
			$td_classe44 .= $modif.$rep_nom["nom"]." ".$rep_nom["prenom"].$etat.$modif_f."<br />";
		}
		else if ($rep_classe[0] == "3EME1") {
			$td_classe31 .= $modif.$rep_nom["nom"]." ".$rep_nom["prenom"].$etat.$modif_f."<br />";
		}
		else if ($rep_classe[0] == "3EME2") {
			$td_classe32 .= $modif.$rep_nom["nom"]." ".$rep_nom["prenom"].$etat.$modif_f."<br />";
		}
		else if ($rep_classe[0] == "3EME3") {
			$td_classe33 .= $modif.$rep_nom["nom"]." ".$rep_nom["prenom"].$etat.$modif_f."<br />";
		}
		else if ($rep_classe[0] == "3EME4") {
			$td_classe34 .= $modif.$rep_nom["nom"]." ".$rep_nom["prenom"].$etat.$modif_f."<br />";
		}
		else if ($rep_classe[0] == "UPI") {
			$td_classeupi .= $modif.$rep_nom["nom"]." ".$rep_nom["prenom"].$etat.$modif_f."<br />";
		}
} // for


?>
	<h2>Les absents du <?php echo $date_jour; ?> rangés par classe et par ordre alphabétique.</h2>
	<h3><a href="./bilan_absences_quotidien.php">Bilan de la journ&eacute;e</a></h3>

<h3>Vous devez choisir un cr&eacute;neau pour visionner les absents :</h3>
<form name="choix_du_creneau" action="voir_absences_viescolaire.php" method="post">
	<select name="choix_creneau" onchange='document.choix_du_creneau.submit();'>
		<option value="rien">Choix du cr&eacute;neau</option>
<?php
		// test sur le jour pour voir les créneaux du mercredi
	if (date("w") == 3) {
		$req_creneaux = mysql_query("SELECT nom_definie_periode, heuredebut_definie_periode, heurefin_definie_periode FROM absences_creneaux_bis WHERE type_creneaux != 'pause' ORDER BY heuredebut_definie_periode");
	}
	else {
		$req_creneaux = mysql_query("SELECT nom_definie_periode, heuredebut_definie_periode, heurefin_definie_periode FROM absences_creneaux WHERE type_creneaux != 'pause' ORDER BY heuredebut_definie_periode");
	}
	//$rep_creneau = mysql_fetch_array($req_creneaux);
	$nbre_creneaux = mysql_num_rows($req_creneaux);
	for($a=0; $a<$nbre_creneaux; $a++) {
		$aff_creneaux[$a]["nom"] = mysql_result($req_creneaux, $a, "nom_definie_periode");
		$aff_creneaux[$a]["heure_debut"] = mysql_result($req_creneaux, $a, "heuredebut_definie_periode");
		$aff_creneaux[$a]["heure_fin"] = mysql_result($req_creneaux, $a, "heurefin_definie_periode");

		echo '
		<option value="'.$aff_creneaux[$a]["heure_debut"].':'.$aff_creneaux[$a]["heure_fin"].'">'.$aff_creneaux[$a]["nom"].'</option>
		';
	}
?>
	</select>
<?php
if (isset($choix_creneau)) {
	$aff_horaires = explode(":", $choix_creneau);
	echo ' Voir les absences de <span style="color: blue;">'.$aff_horaires[0].':'.$aff_horaires[1].'</span> à <span style="color: blue;">'.$aff_horaires[3].':'.$aff_horaires[4].'</span>.';
}
?>
</form>
<br />
<!-- Affichage des réponses-->
<table class="tab_edt">
	<tr>
		<td colspan="5"><?php echo $aff_aid_absences; ?></td>
	</tr>
	<tr>
		<td><?php echo $td_classe61; ?></td>
		<td><?php echo $td_classe62; ?></td>
		<td><?php echo $td_classe63; ?></td>
		<td><?php echo $td_classe64; ?></td>
		<td><?php echo $td_classe65; ?></td>
	</tr>
	<tr>
		<td><?php echo $td_classe51; ?></td>
		<td><?php echo $td_classe52; ?></td>
		<td><?php echo $td_classe53; ?></td>
		<td><?php echo $td_classe54; ?></td>
		<td></td>
	</tr>
	<tr>
		<td><?php echo $td_classe41; ?></td>
		<td><?php echo $td_classe42; ?></td>
		<td><?php echo $td_classe43; ?></td>
		<td><?php echo $td_classe44; ?></td>
		<td></td>
	</tr>
	<tr>
		<td><?php echo $td_classe31; ?></td>
		<td><?php echo $td_classe32; ?></td>
		<td><?php echo $td_classe33; ?></td>
		<td><?php echo $td_classe34; ?></td>
		<td><?php echo $td_classeupi; ?></td>
	</tr>

</table>

<h2>En cliquant sur un &eacute;l&egrave;ve, vous le changer d'&eacute;tat (de absent &agrave; retard ou inversement).</h2>
<p>Attention, cette action est uniquement disponible depuis un compte CPE ou vie scolaire.</p>

<br/>

<?php
require("../../lib/footer.inc.php");
?>