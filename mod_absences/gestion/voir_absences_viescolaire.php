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

$niveau_arbo = 2;
// Initialisations files
require_once("../../lib/initialisations.inc.php");
//mes fonctions
include("../lib/functions.php");
include("../../edt_organisation/fonctions_calendrier.php");
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

// Initialisation des variables
$choix_creneau = isset($_POST["choix_creneau"]) ? $_POST["choix_creneau"] : (isset($_GET["choix_creneau"]) ? $_GET["choix_creneau"] : retourneCreneau());
$vers_absence = isset($_GET["vers_absence"]) ? $_GET["vers_absence"] : NULL;
$vers_retard = isset($_GET["vers_retard"]) ? $_GET["vers_retard"] : NULL;
$aff_nbre_abs = NULL;
$dp = $ext = $int = 0;

//======Quelques variables utiles===========
$date_jour = date("d/m/Y");
$date_mysql = date("Y-m-d");
$heure_mysql = date("H:i:s");

// ++== Traitements des données == ++
	$test = phpversion();
		$version = mb_substr($test, 0, 1);
	if ($version != 5) {
		// rien à faire
	}else{
		require_once("../../class_php/edt_cours.class.php");
	}
// =============================================================
//
//      Ancienne fonction (désactivée car remplacée par AJAX)
//
// =============================================================

function afficherCoursClasse($d, $c){
	// On teste php pour voir si c'est en php5 ou pas
	//global $version;
	//if ($version != 5) {
	//	return '';
	//}
	//$rep = '';
	//$cours = new edtAfficher();
	//$cours->sem = 0;
	//$cours->hauteur_creneau = 70;
	//$cours->type_edt = 'classe';
	//$jour = $cours->aujourdhui();
	//$rep .= $cours->entete_creneaux('noms');
	//$rep .= $cours->afficher_cours_jour($jour, $d);
	return '<p>construction</p>';
}


function IdClasse($nom_classe) {
    $result = 0;
    $sql = "SELECT id FROM classes WHERE classe = '".$nom_classe."' ";
    $req = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
    if ($req) {
        if ($rep = mysqli_fetch_array($req)) {
            $result = $rep['id'];
        }
    }
    return $result;
}


$style_specifique[0] = "edt_organisation/style_edt";
$style_specifique[1] = "templates/DefaultEDT/css/small_edt";
$javascript_specifique = "edt_organisation/script/fonctions_edt";
//**************** EN-TETE *****************
$titre_page = "Les absents du jour.";
require_once("../../lib/header.inc.php");
//************** FIN EN-TETE ***************
//debug_var();
	// Traitement du passage entre absence et retard

	if ($_SESSION["statut"] == "cpe") {
		if (isset($vers_absence)) {
			$cgt_RA = mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE absences_rb SET retard_absence = 'A' WHERE id = '".$vers_absence."'");
		}
		else if (isset($vers_retard)) {
			$cgt_RA = mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE absences_rb SET retard_absence = 'R' WHERE id = '".$vers_retard."'");
			$query_recup = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM absences_rb WHERE id = '".$vers_retard."'");
			$donnees = mysqli_fetch_array($query_recup);
			$date_recherchee = explode(" ", date("Y-m-d H:i:s", $donnees["debut_ts"]));
			// On bascule aussi celle de la table absences_eleves
			$sql_abs_eleve = "UPDATE absences_eleves SET type_absence_eleve = 'R'
														WHERE eleve_absence_eleve = '" . $donnees["eleve_id"] . "'
														AND justify_absence_eleve = 'N'
														AND d_date_absence_eleve = '" . $date_recherchee[0] . "'
														AND d_heure_absence_eleve = '" . $date_recherchee[1] . "'";
			// On force alors la mise à jour et on évite l'apparition d'un message d'erreur si le update ne donne rien
			$query_abs_eleve = @mysqli_query($GLOBALS["___mysqli_ston"], $sql_abs_eleve);
			// Il faut alors regarder s'il existe un courrier déjà lancé pour cette absence
			// le cas échéant, il faut le détruire
			$sql_lettre = "SELECT ls.id_lettre_suivi, ls.partdenum_lettre_suivi FROM lettres_suivis ls
												WHERE ls.emis_date_lettre_suivi = '" . $date_recherchee[0] . "'
												AND ls.quirecois_lettre_suivi = '" . $donnees["eleve_id"] . "'
												AND ls.statu_lettre_suivi = 'en attente'
												AND ls.envoye_date_lettre_suivi = '0000-00-00'"; // ce dernier point permet d'éviter de détruire des lettres déjà envoyées
			$query_lettre = mysqli_query($GLOBALS["___mysqli_ston"], $sql_lettre) OR DIE('Impossible de détruire la lettre déjà émise <br /> -->' . $sql_lettre . '<br /><p style="color: red;">' . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) . '</p>');
			$test = mysqli_num_rows($query_lettre);

			if ($test === 1) {
				// GEPI n'a trouvé qu'une seule réponse, on peut donc l'effacer
				$lettre_a_effacer = mysql_result($query_lettre, 0,"id_lettre_suivi");
				$delete = mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM lettres_suivis WHERE id_lettre_suivi = '" . $lettre_a_effacer . "'");
			}elseif($test > 1){
				$message_erreur_lettre_a_effacer = 'Il y a des lettres qui correspondent à ce retard mais aucune n\'a été détruite.';
			}
		}
	}


// Choix de la bonne table
if (date("w") == getSettingValue("creneau_different")){
	$table_ab = 'edt_creneaux_bis';
}else{
	$table_ab = 'edt_creneaux';
}


	// Préparation de la requête quand un créneau est choisi
$aff_aid_absences = "";
$nbre_rep = "";
if (isset($choix_creneau)) {
	if (is_numeric($choix_creneau)) {
		// On vient d'envoyer l'id du créneau qu'il faut donc récupérer
		$creneaux = mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT heuredebut_definie_periode, heurefin_definie_periode FROM ".$table_ab." WHERE id_definie_periode = '".$choix_creneau."'"));
		$explode1 = explode(":", $creneaux["heuredebut_definie_periode"]);
		$explode2 = explode(":", $creneaux["heurefin_definie_periode"]);
		$ex_horaire[0] = $explode1[0];
		$ex_horaire[1] = $explode1[1];
		$ex_horaire[3] = $explode2[0];
		$ex_horaire[4] = $explode2[1];

	}else{

		// On transforme les horaires du créneau en timestamp UNIX sur la date du jour
		$ex_horaire = explode(":", $choix_creneau);

	}

	$abs_deb_ts = mktime($ex_horaire[0], $ex_horaire[1], 0, date("m"), date("d"), date("Y"));
	$abs_fin_ts = mktime($ex_horaire[3], $ex_horaire[4], 0, date("m"), date("d"), date("Y"));

	// Cette requête permet de suivre une absence d'une durée supérieure à un seul créneau
	$sql = "SELECT DISTINCT id, eleve_id, retard_absence, groupe_id, login_saisie
					FROM absences_rb
					WHERE
					(
		      			(
						debut_ts BETWEEN '" . $abs_deb_ts . "' AND '" . $abs_fin_ts . "'
						AND fin_ts BETWEEN '" . $abs_deb_ts . "' AND '" . $abs_fin_ts . "'
       		  			)
       		  			OR
       		  			(
						'" . $abs_deb_ts . "' BETWEEN debut_ts AND fin_ts
						OR '" . $abs_fin_ts . "' BETWEEN debut_ts AND fin_ts
       		  			)
       		  			AND debut_ts != '" . $abs_fin_ts . "'
         	  			AND fin_ts != '" . $abs_deb_ts . "'
         	  		)
			  		ORDER BY eleve_id";

	$req = mysqli_query($GLOBALS["___mysqli_ston"], $sql) OR trigger_error('Impossible de lister les absents.', E_USER_ERROR);
	//$rep_absences = mysql_fetch_array($req);
	$nbre_rep = mysqli_num_rows($req);


	for($a=0; $a < $nbre_rep; $a++){
		$rep_absences[$a]["id_abs"] = mysql_result($req, $a, "id");
		$rep_absences[$a]["eleve_id"] = mysql_result($req, $a, "eleve_id");
		$rep_absences[$a]["retard_absence"] = mysql_result($req, $a, "retard_absence");
		$rep_absences[$a]["groupe_id"] = mysql_result($req, $a, "groupe_id");
		$rep_absences[$a]["login_saisie"] = mysql_result($req, $a, "login_saisie");
	}
} // if (isset($choix_creneau))

/*==============AFFICHAGE PAGE=============*/
// On récupère la liste des classes de l'établissement
$query = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id, classe FROM classes ORDER BY classe");
$nbre_classe = mysqli_num_rows($query);
$td_classe = array();
$aff_classe = array();
	// On passe le tout à la moulinette :
	for($i = 0; $i < $nbre_classe; $i++){
		$reponse[$i]["classe"] = mysql_result($query, $i, "classe");

		$td_classe[$i] = '';
                $td_classe1[$i] = '';
		$aff_classe[$i] = $reponse[$i]["classe"];
	}

$nbre_abs_cren = 0; // On initialise le nombre d'absents du créneau
// On détermine la période active :
$sql = "SELECT DISTINCT num_periode FROM periodes WHERE verouiller = 'N' ORDER BY num_periode";
$periode_active = mysqli_query($GLOBALS["___mysqli_ston"], $sql) OR DIE('Impossible de récupérer le numéro de la période active' . $sql . '<br />--> ' . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
$periode = mysqli_fetch_array($periode_active);

$nbre_per = count($periode);
$_periode = isset($periode[0]) ? $periode[0] : '1';

// On mouline alors sur la liste des enregistrements des absences
for($i = 0; $i < $nbre_rep; $i++) {
	//$req_prof = mysql_fetch_array(mysql_query("SELECT login_saisie FROM absences_rb WHERE id = '".$rep_absences[$i]["id_abs"]."'")) or die ('erreur 1a : '.mysql_error());
	$rep_prof = mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT nom, prenom FROM utilisateurs WHERE login = '".$rep_absences[$i]["login_saisie"]."'")) or die ('erreur 1b : '.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	if ($rep_absences[$i]["eleve_id"] != "appel") {
		$nbre_abs_cren++; // on incrémente le nombre d'absents du créneau
		$rep_nom = mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT nom, prenom, regime FROM eleves e, j_eleves_regime jer
								WHERE e.login = jer.login
								AND e.login = '".$rep_absences[$i]["eleve_id"]."'"));
			// traitement du régime pour dissocier les 3 états
			if ($rep_nom["regime"] == "d/p") {
				$dp++;
			}elseif($rep_nom["regime"] == "ext."){
				$ext++;
			}elseif($rep_nom["regime"] == "int."){
				$int++;
			}

		$req_classe = mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id_classe FROM j_eleves_classes WHERE login = '".$rep_absences[$i]["eleve_id"]."' AND periode = '".$_periode."'"));
		$rep_classe = mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT classe FROM classes WHERE id = '".$req_classe[0]."'"));
        if ($rep_classe === false) {
            $rep_classe = array();
        }
                // On compte aussi le nombre de classes concernées par cet enseignement
                $query_classe1 = (mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id_classe FROM j_groupes_classes WHERE id_groupe = '".$rep_absences[$i]["groupe_id"]."'"));
                $req_classe1 = array();
                while ($row = mysqli_fetch_array($query_classe1,  MYSQLI_NUM)) {
                  $req_classe1[] = $row[0];
                }
                
                // On explose pour vérifier qu'il ne s'agit pas d'une aid
		$verif_aid = explode("|", $rep_absences[$i]["groupe_id"]);
                if ($verif_aid[0] == "AID"){
                  $rep_aid = mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT nom FROM aid WHERE id = '".$verif_aid[1]."'")) or die ('erreur 1c : '.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
                  // On construit alors l'affichage de cette info qui doit permettre à la vie scolaire de savoir
                  // quand un prof a fait l'appel alors qu'il est avec un aid
                  $aff_aid_absences .= "".$rep_prof["nom"]." ".$rep_prof["prenom"]." a fait l'appel avec le groupe ".$rep_aid["nom"]."<br />";
                }elseif(count($req_classe1) >= 2){
                  // Dans le cas où cet enseignement regroupe plusieurs classes, on marque sur les autres classes qu'elles sont en groupe
                  $queryb_classe1 = (mysqli_query($GLOBALS["___mysqli_ston"], "SELECT classe FROM classes WHERE id IN ('".implode("','", $req_classe1)."')"));
                  $rep_classe1 = array();
                  while ($row1 = mysqli_fetch_array($queryb_classe1,  MYSQLI_NUM)) {
                    $rep_classe1[] = $row1[0];
                  }
                }
	}
	else if ($rep_absences[$i]["eleve_id"] == "appel") {
		// On vide les variables inutiles
		$rep_nom["nom"] = "";
		$rep_nom["prenom"] = "";
		// On explose poour vérifier qu'il ne s'agit pas d'une aid
		$verif_aid = explode("|", $rep_absences[$i]["groupe_id"]);
		if ($verif_aid[0] == "AID") {
			$rep_aid = mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT nom FROM aid WHERE id = '".$verif_aid[1]."'")) or die ('erreur 1c : '.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
			// On construit alors l'affichage de cette info qui doit permettre à la vie scolaire de savoir
			// quand un prof a fait l'appel alors qu'il est avec un aid
			$aff_aid_absences .= "".$rep_prof["nom"]." ".$rep_prof["prenom"]." a fait l'appel avec le groupe ".$rep_aid["nom"]."<br />";
			$rep_classe = array();$rep_classe[0] = "";
		} else {
			$query_classe = (mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id_classe FROM j_groupes_classes WHERE id_groupe = '".$rep_absences[$i]["groupe_id"]."'"));
                        $req_classe = array();
                        while ($row = mysqli_fetch_array($query_classe,  MYSQLI_NUM)) {
                          $req_classe[] = $row[0];
                        }
			$queryb_classe = (mysqli_query($GLOBALS["___mysqli_ston"], "SELECT classe FROM classes WHERE id IN ('".implode("','", $req_classe)."')"));
                        $rep_classe = array();
                        while ($row1 = mysqli_fetch_array($queryb_classe,  MYSQLI_NUM)) {
                          $rep_classe[] = $row1[0];
                        }

		}
	}

	// On vérifie l'état de la saisie absence ou retard ou sans absent (signifier que l'appel a bien été effectué
	if ($rep_absences[$i]["eleve_id"] == "appel") {
		// On récupère le nom de la matière
		$nom_prof = remplace_accents($rep_prof["nom"], 'all');
		$rep_matiere = mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT description FROM groupes WHERE id = '".$rep_absences[$i]["groupe_id"]."'"));
		$etat = "<span style=\"color: brown; font-style: bold;\" onmouseover=\"changementDisplay('".$nom_prof.$i."', '');\" onmouseout=\"changementDisplay('".$nom_prof.$i."', '');\">
					L'appel a bien été effectué en ".$rep_matiere["description"].".</span>
					<div id=\"".$nom_prof.$i."\" class=\"abs_appear\" style=\"display: none;\">Par ".$rep_prof["nom"]." ".$rep_prof["prenom"]."</div>";
		$modif = "";
		$modif_f = "";
	} else if ($rep_absences[$i]["retard_absence"] == "R") {
		$etat = " (retard)";
		$modif = "<a href=\"./voir_absences_viescolaire.php?vers_absence=".$rep_absences[$i]["id_abs"]."&amp;choix_creneau=".$choix_creneau."\" title=\"En retard\" style=\"color: green;\">";
		$modif_f = "</a>";
	} else {
		$etat = "";
		$modif = "<a href=\"./voir_absences_viescolaire.php?vers_retard=".$rep_absences[$i]["id_abs"]."&amp;choix_creneau=".$choix_creneau."\" title=\"Absent, par ".$rep_prof["nom"]." ".$rep_prof["prenom"]."\"><b>";
		$modif_f = "</b></a>";
	}

	// Seul le CPE peut modifier une absence vers retard et vice-versa
	if ($_SESSION["statut"] != "cpe") {
		$modif = "";
		$modif_f = "";
	}

	// On lance la moulinette pour afficher la liste des absents pour chaque classe
        $nbre_classe_ce_groupe = count($req_classe);
	for($c = 0; $c < $nbre_classe; $c++){
          // on vérifie d'abord que les enseignements qui regroupent plusieurs classes sont bien marqués si une seule de ces classes a des absents
          $verif_groupes = isset($req_classe1) ? count($req_classe1) : 0;
          for ($b = 0 ; $b < $verif_groupes ; ++$b){
            if (isset($rep_classe1) AND array_key_exists($b, $rep_classe1) AND $rep_classe1[$b] == $aff_classe[$c]) {
              $td_classe1[$c] = '<span>Classe en groupes avec '.implode(', ', $rep_classe1).'</span><br />';
            }
          }
          for ($a = 0 ; $a < $nbre_classe_ce_groupe ; ++$a){
            if (array_key_exists($a, $rep_classe) AND $rep_classe[$a] == $aff_classe[$c]) {
              $td_classe[$c] .= '<span title="Par '.$rep_prof["nom"].' '.$rep_prof["prenom"].'">'.$modif.$rep_nom["nom"]." ".$rep_nom["prenom"].$etat.$modif_f."</span><br />\n";
            }
          }
	}
} // for

// On annonce le nombre total d'absents sur le créneau
	$aff_nbre_abs = "Il y a ".$nbre_abs_cren." absents sur ce créneau";

?>
<h2><a href="../../accueil.php"><img src="../../images/icons/back.png" alt="Retour" title="Retour" class="back_link" />&nbsp;Retour</a> -
	Les absents du <?php echo $date_jour; ?> rangés par classe et par ordre alphabétique - <a href="./bilan_absences_quotidien.php">Bilan de la journ&eacute;e</a></h2>

<form name="choix_du_creneau" action="voir_absences_viescolaire.php" method="post">
	<p>Vous devez choisir un cr&eacute;neau pour visionner les absents
	<select name="choix_creneau" onchange='document.choix_du_creneau.submit();'>
		<option value="rien">Choix du cr&eacute;neau</option>
<?php
		// test sur le jour pour voir les créneaux
	if (date("w") == getSettingValue("creneau_different")) {
		$req_creneaux = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id_definie_periode, nom_definie_periode, heuredebut_definie_periode, heurefin_definie_periode FROM edt_creneaux_bis WHERE type_creneaux != 'pause' ORDER BY heuredebut_definie_periode");
	}
	else {
		$req_creneaux = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id_definie_periode, nom_definie_periode, heuredebut_definie_periode, heurefin_definie_periode FROM edt_creneaux WHERE type_creneaux != 'pause' ORDER BY heuredebut_definie_periode");
	}
	$nbre_creneaux = mysqli_num_rows($req_creneaux);
	$aff_creneaux_sans_select = NULL;
	for($a=0; $a<$nbre_creneaux; $a++) {
		$aff_creneaux[$a]["nom"] = mysql_result($req_creneaux, $a, "nom_definie_periode");
		$aff_creneaux[$a]["id"] = mysql_result($req_creneaux, $a, "id_definie_periode");
		$aff_creneaux[$a]["heure_debut"] = mysql_result($req_creneaux, $a, "heuredebut_definie_periode");
		$aff_creneaux[$a]["heure_fin"] = mysql_result($req_creneaux, $a, "heurefin_definie_periode");

		//echo '
		//<option value="'.$aff_creneaux[$a]["heure_debut"].':'.$aff_creneaux[$a]["heure_fin"].'">'.$aff_creneaux[$a]["nom"].'</option>
		//';
		if ($aff_creneaux[$a]["id"] == $choix_creneau) {
			$selected = ' selected="selected"';
			$color_selected = 'style="color: red; font-weight: bold;"';
		}else{
			$selected = '';
			$color_selected = '';
		}
		echo '
		<option value="'.$aff_creneaux[$a]["id"].'"'.$selected.'>'.$aff_creneaux[$a]["nom"].'</option>
		';
		// Ajout des liens directs sans le select
		$aff_creneaux_sans_select .= '<a href="' . $_SERVER["PHP_SELF"] . '?choix_creneau='.$aff_creneaux[$a]["id"].'"'.$color_selected.'>'.$aff_creneaux[$a]["nom"].'</a>&nbsp;-&nbsp;';
	}
?>
	</select>
	&nbsp;-&nbsp;<span style="cursor: pointer; color: blue;" onclick="changementDisplay('id4_aide', '');">Aide sommaire</span>
	</p>
		<div id="id4_aide" class="abs_appear" style="display: none; margin-left: 400px;">
	Pour voir le nom du prof qui a fait l'appel quand il n'y a pas d'absent, il suffit de passer la souris sur le texte de droite.<br />
	Pour voir l'emploi du temps de la classe, il suffit de cliquer sur le nom de la classe.</div>
</form>

<?php
if (isset($choix_creneau)) {
	//$aff_horaires = explode(":", $choix_creneau);
	echo ' Voir les absences de <span style="color: blue;">'.$explode1[0].':'.$explode1[1].'</span> à <span style="color: blue;">'.$explode2[0].':'.$explode2[1].'</span>.';
}
?>
&nbsp;&nbsp;<span style="border: 1px solid grey;"><?php echo $aff_creneaux_sans_select; ?></span></p>
<br />
<!-- Affichage des réponses-->
<table class="tab_edt" summary="Liste des absents r&eacute;partie par classe">
	<tr style="background-color: white;">
		<td><?php echo $aff_nbre_abs; ?></td>
		<td><?php echo $dp.'&nbsp;d/p'; ?></td>
		<td><?php echo $ext.'&nbsp;ext.'; ?></td>
		<td><?php echo $int.'&nbsp;int.'; ?></td>
	</tr>
	<tr>
		<td>Les groupes</td>
		<td colspan="3"><?php echo $aff_aid_absences; ?></td>
		<!--<td></td>
		<td></td>-->
	</tr>
<?php
// On affiche la liste des classes
for($a = 0; $a < $nbre_classe; $a++){
	// On détermine si sur deux colonnes, le compte tombe juste
	$calc = $nbre_classe / 2;
	$modulo = $nbre_classe % 2;
	$num_id = 'id'.remplace_accents($aff_classe[$a], 'all');
    $id_classe = IdClasse($aff_classe[$a]);
	echo '
	<tr>
		<td>
			<h4 style="color: red;"><a href="#" onclick="AfficheEdtClasseDuJour(\''.$id_classe.'\',\''.$num_id.'\', '.$niveau_arbo.'); return false;">'.$aff_classe[$a].'</a></h4>
			<div id="'.$num_id.'" style="display: none; position: absolute; background-color: white; -moz-border-radius: 10px; padding: 10px;">
			'.afficherCoursClasse($aff_classe[$a], $choix_creneau).'</div>
		</td>
		<td style="width: 250px;">'.$td_classe1[$a].$td_classe[$a].'</td>';
	if ($a == ($nbre_classe - 1) AND $modulo == 1) {
		// c'est qu'on est arrivé à la dernière ligne et que le nombre de classes est impair
		echo '
		<td></td>
		<td style="width: 250px;"></td>
		</tr>';
	}else{
		$a++; // on passe à la colonne suivante
		$num_id = 'id'.remplace_accents($aff_classe[$a], 'all');
        $id_classe = IdClasse($aff_classe[$a]);
		echo '
			<td>
				<h4 style="color: red;"><a href="#" onclick="AfficheEdtClasseDuJour(\''.$id_classe.'\',\''.$num_id.'\','.$niveau_arbo.'); return false;">'.$aff_classe[$a].'</a></h4>
				<div id="'.$num_id.'" style="display: none; position: absolute; background-color: white; -moz-border-radius: 10px; padding: 10px;">
				'.afficherCoursClasse($aff_classe[$a], $choix_creneau).'</div>
			</td>
			<td style="width: 250px;">'.$td_classe1[$a].$td_classe[$a].'</td>
		</tr>';
	}
}
?>

</table>

<h2>En cliquant sur un &eacute;l&egrave;ve, vous le changez d'&eacute;tat (de absent &agrave; retard ou inversement).</h2>
<p>Attention, cette action est uniquement disponible depuis un compte CPE ou vie scolaire.</p>

<br/>

<?php
require("../../lib/footer.inc.php");
?>