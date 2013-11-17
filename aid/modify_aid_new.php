<?php

/**
 * Document destiné à constituer les AID (élèves) en partant d'un lot de classes.
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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

// Initialisation
require_once("../lib/initialisations.inc.php");

// Les fonctions de Gepi
require_once("../lib/share.inc.php");

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

//Initialisation des variables
$id_aid = isset($_GET["id_aid"]) ? $_GET["id_aid"] : (isset($_POST["id_aid"]) ? $_POST["id_aid"] : NULL);
$indice_aid = isset($_GET["indice_aid"]) ? $_GET["indice_aid"] : (isset($_POST["indice_aid"]) ? $_POST["indice_aid"] : NULL);
$aff_liste_m = (isset($_GET["classe"]) AND is_numeric($_GET["classe"])) ? $_GET["classe"] : (isset($_POST["classe"]) ? $_POST["classe"] : NULL);
$choix_aid = isset($_GET["choix_aid"]) ? $_GET["choix_aid"] : (isset($_POST["choix_aid"]) ? $_POST["choix_aid"] : NULL);
$id_eleve = isset($_GET["id_eleve"]) ? $_GET["id_eleve"] : (isset($_POST["id_eleve"]) ? $_POST["id_eleve"] : NULL);
$eleve = isset($_GET["eleve"]) ? $_GET["eleve"] : (isset($_POST["eleve"]) ? $_POST["eleve"] : NULL);
$action = isset($_GET["action"]) ? $_GET["action"] : (isset($_POST["action"]) ? $_POST["action"] : NULL);
$aff_infos_g = "";
$aff_classes_g = "";
$aff_aid_d = "";
$aff_classes_m = "";
$autoriser_inscript_multiples = sql_query1("select autoriser_inscript_multiples from aid_config where indice_aid='".$indice_aid."'");


//+++++++++++++++++ CSS AID++++++++
$style_specifique = "aid/style_aid";
//+++++++++++++++++ AJAX AID ++++++
	// En attente de fonctionnement
$utilisation_prototype = "ok";
$javascript_specifique = "aid/aid_ajax";

// Vérification du niveau de gestion des AIDs
if (NiveauGestionAid($_SESSION["login"],$indice_aid,$id_aid) <= 0) {
    header("Location: ../logout.php?auto=1");
    die();
}


//**************** EN-TETE **************************************
$titre_page = "Gestion des élèves dans les AID";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE **********************************

	//================ TRAITEMENT des entrées ===================
	if (isset($aff_liste_m) AND isset($id_aid) AND isset($id_eleve) AND isset($indice_aid)) {
		check_token(false);

		// Cas de la classe entière
		if ($id_eleve == "tous") {
			// On récupère tous les login de cette classe
			$req_login = mysqli_query($GLOBALS["mysqli"], "SELECT login FROM j_eleves_classes WHERE id_classe = '".$aff_liste_m."' ORDER BY login");
			$nbre_login = mysqli_num_rows($req_login);
			for($i=0; $i<$nbre_login; $i++){
				$rep_log_eleve[$i]["login"] = mysql_result($req_login, $i, "login");
				// On teste si cet élève n'est pas déjà membre de l'AID
        if ($autoriser_inscript_multiples == 'y')
  				$req_verif = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT login FROM j_aid_eleves WHERE indice_aid = '".$indice_aid."' AND login = '".$rep_log_eleve[$i]["login"]."'") OR die ('Erreur requête1 : '.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)).'.');
        else
  				$req_verif = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT login FROM j_aid_eleves WHERE indice_aid = '".$indice_aid."' AND id_aid = '".$id_aid."' AND login = '".$rep_log_eleve[$i]["login"]."'") OR die ('Erreur requête1 : '.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)).'.');
				$verif = mysqli_num_rows($req_verif);
				if ($verif === 0) {
					$req_ajout = mysqli_query($GLOBALS["mysqli"], "INSERT INTO j_aid_eleves SET login='".$rep_log_eleve[$i]["login"]."', id_aid='".$id_aid."', indice_aid='".$indice_aid."'");
				}else {
					// on ne fait rien
				}
			}
		}else {
		// On intègre cet élève dans la base s'il n'y est pas déjà
		// Pour l'instant on récupère son login à partir de id_eleve
		$rep_log_eleve = mysqli_fetch_array(mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT login FROM eleves WHERE id_eleve = '".$id_eleve."'"));
		// On vérifie s'il n'est pas déjà membre de cet aid
		// Par cette méthode, on ne peut enregistrer deux fois le même
		$req_ajout = mysqli_query($GLOBALS["mysqli"], "INSERT INTO j_aid_eleves SET login='".$rep_log_eleve["login"]."', id_aid='".$id_aid."', indice_aid='".$indice_aid."'");
		}// fin du else
	}

	//================= TRAITEMENT des sorties =======================
	// Attention de penser à sortir les lignes des notes et appréciations si elles existent
	if (isset($action) AND $action == "del_eleve_aid") {
		check_token(false);

		// On supprime l'élève de l'AID
		$req_suppr1 = mysqli_query($GLOBALS["mysqli"], "DELETE FROM j_aid_eleves WHERE login='".$eleve."' and id_aid = '".$id_aid."' and indice_aid='".$indice_aid."'");
		$req_suppr2 = mysqli_query($GLOBALS["mysqli"], "DELETE FROM j_aid_eleves_resp WHERE login='".$eleve."' and id_aid = '".$id_aid."' and indice_aid='".$indice_aid."'");
		//On teste ensuite si cet élève avait des appréciations / notes
		$req_test_notes = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM aid_appreciations WHERE login='".$eleve."' and id_aid = '".$id_aid."' and indice_aid='".$indice_aid."'");
		$test_notes = mysqli_num_rows($req_test_notes);
		if ($test_notes !== 0) {
			$suppr_notes = mysqli_query($GLOBALS["mysqli"], "DELETE FROM aid_appreciations WHERE login='".$eleve."' and id_aid = '".$id_aid."' and indice_aid='".$indice_aid."'");
		}
	} //if isset($action...

// Affichage du retour
	// On récupère l'indice de l'aid en question
	$aff_infos_g .= "<span class=\"aid_a\"><a href=\"modify_aid.php?flag=eleve&amp;aid_id=".$id_aid."&amp;indice_aid=".$indice_aid.add_token_in_url()."\"><img src='../images/icons/back.png' alt='Retour' class='back_link' /> Retour</a></span>";


//Affichage du nom et des précisions sur l'AID en question
	$req_aid = mysqli_query($GLOBALS["mysqli"], "SELECT nom FROM aid WHERE id = '".$id_aid."'");
	$rep_aid = mysqli_fetch_array($req_aid);
	$aff_infos_g .= "<p class=\"bold\">Liste des classes</p>\n";

// Affichage de la liste des classes par $aff_classes_g

	$req_liste_classe = mysqli_query($GLOBALS["mysqli"], "SELECT id, classe FROM classes ORDER BY classe");
	$nbre_classe = mysqli_num_rows($req_liste_classe);

	for($a=0; $a<$nbre_classe; $a++) {
		$liste_classe[$a]["id"] = mysql_result($req_liste_classe, $a, "id");
		$liste_classe[$a]["classe"] = mysql_result($req_liste_classe, $a, "classe");

		$aff_classes_g .= "<tr><td style=\"width: 196px;\"><a href=\"./modify_aid_new.php?id_aid=".$id_aid."&amp;classe=".$liste_classe[$a]["id"]."&amp;indice_aid=".$indice_aid.add_token_in_url()."\">Elèves de la ".$liste_classe[$a]["classe"]."</a></td></tr>\n";
	}

// Affichage de la liste des élèves de la classe choisie (au milieu) par $aff_classes_m

if (isset($aff_liste_m)) {

	$aff_nom_classe = mysqli_fetch_array(mysqli_query($GLOBALS["mysqli"], "SELECT classe FROM classes WHERE id = '".$aff_liste_m."'"));

	// Récupérer la liste des élèves de la classe en question
	$req_ele = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT e.login, e.id_eleve, nom, prenom, sexe
					FROM j_eleves_classes jec, eleves e
					WHERE id_classe = '".$aff_liste_m."'
					AND jec.login = e.login ORDER BY nom, prenom")
						OR DIE('Erreur dans la requête $req_ele : '.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$nbre_ele_m = mysqli_num_rows($req_ele);

	$aff_classes_m .= "
		<p class=\"red\">Classe de ".$aff_nom_classe["classe"]." : </p>

	<table class=\"aid_tableau\" summary=\"Liste des &eacute;l&egrave;ves\">
	";
		// Ligne paire, ligne impaire (inutile dans un premier temps), on s'en sert pour faire ladifférence avec une ligne vide.
			$aff_tr_css = "aid_lignepaire";
		// On ajoute un lien qui permet d'intégrer toute la classe d'un coup
		$aff_classes_m .= "
		<tr class=\"".$aff_tr_css."\">
			<td>
				<a href=\"modify_aid_new.php?classe=".$aff_liste_m."&amp;id_eleve=tous&amp;id_aid=".$id_aid."&amp;indice_aid=".$indice_aid.add_token_in_url()."\">
				<img src=\"../images/icons/add_user.png\" alt=\"Ajouter\" title=\"Ajouter\" /> Toute la classe
				</a>
			</td>
		</tr>
		<tr>
			<td>Liste des élèves
			</td>
		</tr>
						";

	for($b=0; $b<$nbre_ele_m; $b++) {
		$aff_ele_m[$b]["login"] = mysql_result($req_ele, $b, "login") OR DIE('Erreur requête liste_eleves : '.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

			$aff_ele_m[$b]["id_eleve"] = mysql_result($req_ele, $b, "id_eleve");
			$aff_ele_m[$b]["nom"] = mysql_result($req_ele, $b, "nom");
			$aff_ele_m[$b]["prenom"] = mysql_result($req_ele, $b, "prenom");
			$aff_ele_m[$b]["sexe"] = mysql_result($req_ele, $b, "sexe");

			// On vérifie que cet élève n'est pas déjà membre de l'AID
			if ($autoriser_inscript_multiples == 'y')
  			$req_verif = mysqli_query($GLOBALS["mysqli"], "SELECT login FROM j_aid_eleves WHERE login = '".$aff_ele_m[$b]["login"]."' and id_aid='$id_aid' AND indice_aid = '".$indice_aid."'");
  		else
  			$req_verif = mysqli_query($GLOBALS["mysqli"], "SELECT login FROM j_aid_eleves WHERE login = '".$aff_ele_m[$b]["login"]."' AND indice_aid = '".$indice_aid."'");
			$nbre_verif = mysqli_num_rows($req_verif);
				if ($nbre_verif >> 0) {
					$aff_classes_m .= "
					<tr class=\"aid_ligneimpaire\">
					<td></td></tr>
					";
				}
				else {
					$aff_classes_m .= "
					<tr class=\"".$aff_tr_css."\">
					<td><a href=\"modify_aid_new.php?classe=".$aff_liste_m."&amp;id_eleve=".$aff_ele_m[$b]["id_eleve"]."&amp;id_aid=".$id_aid."&amp;indice_aid=".$indice_aid.add_token_in_url()."\">
							<img src=\"../images/icons/add_user.png\" alt=\"Ajouter\" title=\"Ajouter\" /> ".$aff_ele_m[$b]["nom"]." ".$aff_ele_m[$b]["prenom"]."
							</a></td></tr>
					";
				}
	}// for $b


	$aff_classes_m .= "</table>\n";
}// if isset...

// Dans le div de droite, on affiche la liste des élèves de l'AID
		$aff_aid_d .= "<p style=\"color: brown; border: 1px solid brown; padding: 2px;\">".$rep_aid["nom"]." :</p>\n";
		// mais aussi le nom des profs de l'AID
		$req_prof = mysqli_query($GLOBALS["mysqli"], "SELECT id_utilisateur FROM j_aid_utilisateurs WHERE id_aid = '".$id_aid."' ORDER BY id_utilisateur");
		$nbre_prof = mysqli_num_rows($req_prof);
		for($p=0; $p<$nbre_prof; $p++) {
			$prof[$p]["id_utilisateur"] = mysql_result($req_prof, $p, "id_utilisateur");
			// On récupère le nom et la civilité de tous les profs
			$rep_nom = mysqli_fetch_array(mysqli_query($GLOBALS["mysqli"], "SELECT nom, civilite FROM utilisateurs WHERE login = '".$prof[$p]["id_utilisateur"]."'"));
			$aff_aid_d .= "".$rep_nom["civilite"].$rep_nom["nom"]." ";
		}

	$req_ele_aid = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT j.login, e.nom, e.prenom, c.id, c.classe
										FROM j_aid_eleves j, eleves e, j_eleves_classes jec, classes c
										WHERE j.id_aid = '".$id_aid."' AND
										j.login = e.login AND
										jec.login = j.login AND
										jec.id_classe = c.id
										ORDER BY c.classe, e.nom, e.prenom")
									OR trigger_error('Erreur sur la liste d\'élèves : '.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), E_USER_ERROR);
	$nbre = mysqli_num_rows($req_ele_aid);
		$s = "";
		if ($nbre >= 2) {
			$s = "s";
		}
		else {
			$s = "";
		}
		$aff_aid_d .= "\n<br />".$nbre." élève".$s.".<br />";

	for($d=0; $d<$nbre; $d++){
		// On récupère ses noms et prénoms, puis la classe
		$rep_ele_aid[$d]["login"] = mysql_result($req_ele_aid, $d, "login");
		$rep_ele_aid[$d]["nom"] = mysql_result($req_ele_aid, $d, "nom");
		$rep_ele_aid[$d]["prenom"] = mysql_result($req_ele_aid, $d, "prenom");
		$rep_ele_aid[$d]["classe"] = mysql_result($req_ele_aid, $d, "classe");
		$rep_ele_aid[$d]["id_classe"] = mysql_result($req_ele_aid, $d, "id");

		$aff_aid_d .= "<br />
			<a href='./modify_aid_new.php?classe=".$rep_ele_aid[$d]["id_classe"]."&amp;eleve=".$rep_ele_aid[$d]["login"]."&amp;id_aid=".$id_aid."&amp;indice_aid=".$indice_aid."&amp;action=del_eleve_aid".add_token_in_url()."'>
				<img src=\"../images/icons/delete.png\" title=\"Supprimer cet élève\" alt=\"Supprimer\" />
			</a>".$rep_ele_aid[$d]["nom"]." ".$rep_ele_aid[$d]["prenom"]." ".$rep_ele_aid[$d]["classe"]."\n";
	}

?>
<a href="#" onMouseOver="javascript:changerDisplayDiv('aid_aide');" onMouseOut="javascript:changerDisplayDiv('aid_aide');">
	<img src="../images/info.png" alt="Plus d'infos..." Title="Plus d'infos..." />
</a>
	<div id="aid_aide" style="display: none;">
	Pour acc&eacute;l&eacute;rer la proc&eacute;dure, en cliquant sur une classe,
	vous avez acc&egrave;s &agrave; la liste de ses &eacute;l&egrave;ves.<br />
	Vous pouvez int&eacute;grer tous ces &eacute;l&egrave;ves en cliquant sur [Toute la classe].<br />
	<hr />
	</div>

	<div id="aid_gauche">

<?php // Affichage des infos sur la partie gauche
	echo $aff_infos_g;
?>

		<table class="aid_tableau" summary="Liste des classes">
<?php // Afichage de la liste des classes à gauche
	echo $aff_classes_g;
?>
		</table>
	</div>

	<div id="aid_droite">

<?php // Affichage à droite
	echo $aff_aid_d;
?>

	</div>

	<div id="aid_centre">

<?php // Affichage au centre
	echo $aff_classes_m;
?>
	</div>


<?php
//require_once("../lib/footer.inc.php");
echo "</div></body></html>";
?>
