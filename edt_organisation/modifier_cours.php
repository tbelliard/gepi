<?php

/**
 * Fichier destiné à permettre la modification d'un cours
 *
 *
 * Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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
require_once("./choix_langue.php");
$titre_page = "Modifier un cours de l'emploi du temps";
$affiche_connexion = 'yes';
$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions edt
require_once("./fonctions_edt.php");
require_once("./fonctions_cours.php");
// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

// Sécurité
// INSERT INTO droits VALUES ('/edt_organisation/modifier_cours.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'Modifier un cours', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}
// Sécurité supplémentaire par rapport aux paramètres du module EdT / Calendrier
if (param_edt($_SESSION["statut"]) != "yes") {
	Die('Vous devez demander à votre administrateur l\'autorisation de voir cette page.');
}
// On vérifie que le droit soit le bon pour le profil scolarité
	$autorise = "non";
if ($_SESSION["statut"] == "administrateur") {
	$autorise = "oui";
}
elseif ($_SESSION["statut"] == "scolarite" AND $gepiSettings['scolarite_modif_cours'] == "y") {
	$autorise = "oui";
}
else {
	$autorise = "non";
	exit('Vous n\'êtes pas autorisé à modifier les cours des emplois du temps, contacter l\'administrateur de Gepi');
}


// ===== Initialisation des variables =====

$id_cours = isset($_GET["id_cours"]) ? $_GET["id_cours"] : (isset($_POST["id_cours"]) ? $_POST["id_cours"] : NULL);
$type_edt = isset($_GET["type_edt"]) ? $_GET["type_edt"] : (isset($_POST["type_edt"]) ? $_POST["type_edt"] : NULL);
$identite = isset($_GET["identite"]) ? $_GET["identite"] : (isset($_POST["identite"]) ? $_POST["identite"] : NULL);
$modifier_cours = isset($_POST["modifier_cours"]) ? $_POST["modifier_cours"] : NULL;
$enseignement = isset($_POST["enseignement"]) ? $_POST["enseignement"] : NULL;
$ch_jour_semaine = isset($_POST["ch_jour_semaine"]) ? $_POST["ch_jour_semaine"] : NULL;
$ch_heure = isset($_POST["ch_heure"]) ? $_POST["ch_heure"] : NULL;
$heure_debut = isset($_POST["heure_debut"]) ? $_POST["heure_debut"] : NULL;
$duree = isset($_POST["duree"]) ? $_POST["duree"] : NULL;
$choix_semaine = isset($_POST["choix_semaine"]) ? $_POST["choix_semaine"] : NULL;
$login_salle = isset($_POST["login_salle"]) ? $_POST["login_salle"] : NULL;
$periode_calendrier = isset($_POST["periode_calendrier"]) ? $_POST["periode_calendrier"] : NULL;
$aid = isset($_POST["aid"]) ? $_POST["aid"] : NULL;
$horaire = isset($_GET["horaire"]) ? $_GET["horaire"] : (isset($_POST["horaire"]) ? $_POST["horaire"] : NULL);
$cours = isset($_GET["cours"]) ? $_GET["cours"] : (isset($_POST["cours"]) ? $_POST["cours"] : NULL);
$period_id=isset($_GET['period_id']) ? $_GET['period_id'] : (isset($_POST['period_id']) ? $_POST['period_id'] : NULL);
$message = "";
$id_aid = "";
$analyse = explode("|", $enseignement);
if ($analyse[0] == "AID") {
    $id_aid = $analyse[1];
    $enseignement = "";
}

// Traitement des changements
if (isset($modifier_cours) AND $modifier_cours == "ok") {
	if (ProfDisponible($identite, $ch_jour_semaine, $ch_heure, $duree, $heure_debut, $choix_semaine, $id_cours, $message, $periode_calendrier)) {
        if (SalleDisponible($login_salle, $ch_jour_semaine, $ch_heure, $duree, $heure_debut, $choix_semaine, $id_cours, $message, $periode_calendrier)) {
            if (GroupeDisponible($enseignement, $id_aid, $ch_jour_semaine, $ch_heure, $duree, $heure_debut, $choix_semaine, $id_cours, $message, $periode_calendrier)) {
	            $req_modif = mysql_query("UPDATE edt_cours SET id_groupe = '$enseignement',
                    id_aid = '$id_aid',
	                id_salle = '$login_salle',
	                jour_semaine = '$ch_jour_semaine',
	                id_definie_periode = '$ch_heure',
	                duree = '$duree',
	                heuredeb_dec = '$heure_debut',
	                id_semaine = '$choix_semaine',
	                id_calendrier = '$periode_calendrier'
	                WHERE id_cours = '".$id_cours."'")
	                or die('Erreur dans la mofication du cours : '.mysql_error().'');
                    $_SESSION['edt_prof_enseignement'] = $enseignement;
                    $_SESSION['edt_prof_salle'] = $login_salle;
	        }
        }
    }
}
elseif (isset($modifier_cours) AND $modifier_cours == "non") {
	if (ProfDisponible($identite, $ch_jour_semaine, $ch_heure, $duree, $heure_debut, $choix_semaine, -1, $message, $periode_calendrier)) {
		if (SalleDisponible($login_salle, $ch_jour_semaine, $ch_heure, $duree, $heure_debut, $choix_semaine, -1, $message, $periode_calendrier)) {
            if (GroupeDisponible($enseignement, $id_aid, $ch_jour_semaine, $ch_heure, $duree, $heure_debut, $choix_semaine, -1, $message, $periode_calendrier)) {
				$nouveau_cours = mysql_query("INSERT INTO edt_cours SET id_groupe = '$enseignement',
                     id_aid = '$id_aid',
					 id_salle = '$login_salle',
					 jour_semaine = '$ch_jour_semaine',
					 id_definie_periode = '$ch_heure',
					 duree = '$duree',
					 heuredeb_dec = '$heure_debut',
					 id_semaine = '$choix_semaine',
					 id_calendrier = '$periode_calendrier',
					 login_prof = '".$identite."'")
				OR DIE('Erreur dans la création du cours : '.mysql_error());
                $_SESSION['edt_prof_enseignement'] = $enseignement;
                $_SESSION['edt_prof_salle'] = $login_salle;
			}
		}
    }
} else {
	// On ne fait rien
}

// ======== récupérer le message transmis en cas de problème lors de la création/modification du cours

$_SESSION["message"] = $message;

// CSS et js particulier à l'EdT
$javascript_specifique = "edt_organisation/script/fonctions_edt";
$style_specifique = "templates/".NameTemplateEDT()."/css/style_edt";

// +++++++++++++++ entête de Gepi +++++++++
require_once("../lib/header.inc.php");
// +++++++++++++++ entête de Gepi +++++++++

// On ajoute le menu EdT
require_once("./menu.inc.php");


?>

<!-- la page du corps de l'EdT -->

	<div id="lecorps">

<?php

    // ========================= AFFICHAGE DES MESSAGES
    
    if ($message != "") {
        echo ("<div class=\"cadreInformation\">".$message."</div>");
        $_SESSION["message"] = "";
    }
	echo '
	<p>
    <a href="./index_edt.php?visioedt=prof1&login_edt='.$identite.'&type_edt_2=prof">
	<img src="../images/icons/back.png" alt="Revenir" title="Revenir" />
    </a>
    Retour
    </p>

	';
$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
    echo ("<div class=\"fenetre\">\n");
    echo("<div class=\"contenu\">
		<div class=\"coingh\"></div>
        <div class=\"coindh\"></div>
        <div class=\"partiecentralehaut\"></div>
        <div class=\"droite\"></div>
        <div class=\"gauche\"></div>
        <div class=\"coingb\"></div>
		<div class=\"coindb\"></div>
		<div class=\"partiecentralebas\"></div>\n");
}    
// Si tout est ok, on affiche le cours reçu en GET ou POST
if ($autorise == "oui") {
	// On récupère les infos sur le cours
	if (isset($id_cours)) {
		$req_cours = mysql_query("SELECT * FROM edt_cours WHERE id_cours = '".$id_cours."'");
		$rep_cours = mysql_fetch_array($req_cours);
	} else {
		$rep_cours["jour_semaine"] = NULL;
		$rep_cours["id_definie_periode"] = NULL;
		$rep_cours["id_groupe"] = NULL;
		$rep_cours["id_aid"] = NULL;		
		$rep_cours["id_semaine"] = NULL;
		$rep_cours["id_salle"] = NULL;
		$rep_cours["id_calendrier"] = NULL;
		$rep_cours["duree"] = 2;
	}

	// On récupère les infos sur le professeur
	$rep_prof = mysql_fetch_array(mysql_query("SELECT nom, prenom FROM utilisateurs WHERE login = '".$identite."'"));

	// On insère alors le message d'erreur s'il existe
	if (isset($message)) {
		$affmessage = $message;
	}else {
		$affmessage = "";
	}
$affmessage = NULL;
	// On affiche les différents items du cours
echo '
	<fieldset>
		<legend>'.LESSON_MODIFICATION.'</legend>
		<form action="modifier_cours.php" method="post">

			<!-- <h2>'.$rep_prof["prenom"].' '.$rep_prof["nom"].' ('.$id_cours.') '.$affmessage.'</h2> -->

	<table id="edt_modif" summary="Choisir les informations du cours">
		<tr class="ligneimpaire">
			<td>
			<select name="enseignement">';

		$tab_enseignements = get_groups_for_prof($identite);
		// Si c'est un AID, on inscrit son nom
		if ($rep_cours["id_aid"] != NULL) {
			$nom_aid = mysql_fetch_array(mysql_query("SELECT nom, indice_aid FROM aid WHERE id = '".$rep_cours["id_aid"]."'"));
		    $req_nom_complet = mysql_query("SELECT nom FROM aid_config WHERE indice_aid = '".$nom_aid["indice_aid"]."'");
		    $rep_nom_complet = mysql_fetch_array($req_nom_complet);
			$aff_intro = $rep_nom_complet["nom"]." : ".$nom_aid["nom"];
		}else {
			$aff_intro = CHOOSE_LESSON;
		}
echo '
				<option value="'.$rep_cours["id_groupe"].'">'.$aff_intro.'</option>
	';


    $already_selected = false;
	for($i=0; $i<count($tab_enseignements); $i++) {

		if(isset($rep_cours["id_groupe"])){
			if($rep_cours["id_groupe"] == $tab_enseignements[$i]["id"]){
				$selected = " selected='selected'";
                $already_selected = true;
			}
			else{
                $selected="";
			}
		}
		else{
		    if(isset($_SESSION['edt_prof_enseignement'])){
			    if($_SESSION['edt_prof_enseignement'] == $tab_enseignements[$i]["id"]){
				    $selected = " selected='selected'";
                    $already_selected = true;
			    }
			    else{
                    $selected="";
			    }
		    }
		    else{
			    $selected="";
		    }
		}
	echo '
				<option value="'.$tab_enseignements[$i]["id"].'"'.$selected.'>'.$tab_enseignements[$i]["classlist_string"].' : '.$tab_enseignements[$i]["description"].'</option>
		';
	}

	// On ajoute les AID s'il y en a
	$tab_aid = renvoieAid("prof", $identite);
	for($i = 0; $i < count($tab_aid); $i++) {
		$nom_aid = mysql_fetch_array(mysql_query("SELECT nom, indice_aid FROM aid WHERE id = '".$tab_aid[$i]["id_aid"]."'"));
		$req_nom_complet = mysql_query("SELECT nom FROM aid_config WHERE indice_aid = '".$nom_aid["indice_aid"]."'");
		$rep_nom_complet = mysql_fetch_array($req_nom_complet);
        $complete_aid = "AID|".$tab_aid[$i]["id_aid"];
		if(isset($_SESSION['edt_prof_enseignement'])){
			if(($_SESSION['edt_prof_enseignement'] == $complete_aid) AND (!$already_selected)){
				$selected = " selected='selected'";
			}
			else{
                $selected="";
			}
		}
		else{
			$selected="";
		}
		echo '
				<option value="AID|'.$tab_aid[$i]["id_aid"].'"'.$selected.'>'.$rep_nom_complet["nom"].' : '.$nom_aid["nom"].'</option>
		';
	}

echo '
			</select>



			</td>
			<td>
				<select name="ch_jour_semaine">
	';

	// Dans le cas de la création d'un cours, on propose le bon jour
	if ($cours == "aucun" AND isset($horaire)) {
		// On récupère le jour et le créneau
		$jour_creneau = explode("|", $horaire);
		$jour_creer = $jour_creneau[0];
		$id_creneau_creer = $jour_creneau[1];
		$deb_creer = $jour_creneau[2];
	} else {
		$jour_creer = NULL;
		$id_creneau_creer = NULL;
		$deb_creer = NULL;
	}

	// On propose aussi le choix du jour

	$req_jour = mysql_query("SELECT id_horaire_etablissement, jour_horaire_etablissement FROM horaires_etablissement WHERE ouvert_horaire_etablissement = 1");
	$rep_jour = mysql_fetch_array($req_jour);
	$nbre = mysql_num_rows($req_jour);
	$tab_select_jour = array();

	for($a=0; $a < $nbre; $a++) {
		$tab_select_jour[$a]["id"] = mysql_result($req_jour, $a, "id_horaire_etablissement");
		$tab_select_jour[$a]["jour_sem"] = mysql_result($req_jour, $a, "jour_horaire_etablissement");

		if(isset($rep_cours["jour_semaine"]) OR isset($jour_creer)){
			if(($rep_cours["jour_semaine"] == $tab_select_jour[$a]["jour_sem"]) OR ($jour_creer == $tab_select_jour[$a]["jour_sem"])){
				$selected=" selected='selected'";
			}
			else{
				$selected = "";
			}
		}
		else{
			$selected = "";
		}
		echo '
		<option value="'.$tab_select_jour[$a]["jour_sem"].'"'.$selected.'>'.$tab_select_jour[$a]["jour_sem"].'</option>
		';
	}
echo '
				</select>
			</td>
			<td>
			<select name="ch_heure">
				<option value="rien">'.HOURS.'</option>';

	// On propose aussi le choix de l'horaire

	$req_heure = mysql_query("SELECT id_definie_periode, nom_definie_periode, heuredebut_definie_periode, heurefin_definie_periode FROM edt_creneaux WHERE type_creneaux != 'pause' ORDER BY heuredebut_definie_periode");
	$rep_heure = mysql_num_rows($req_heure);

	for($b = 0; $b < $rep_heure; $b++) {

		$tab_select_heure[$b]["id_heure"] = mysql_result($req_heure, $b, "id_definie_periode");
		$tab_select_heure[$b]["creneaux"] = mysql_result($req_heure, $b, "nom_definie_periode");
		$tab_select_heure[$b]["heure_debut"] = mysql_result($req_heure, $b, "heuredebut_definie_periode");
		$tab_select_heure[$b]["heure_fin"] = mysql_result($req_heure, $b, "heurefin_definie_periode");

		if(isset($rep_cours["id_definie_periode"]) OR isset($id_creneau_creer)){
			if(($rep_cours["id_definie_periode"] == $tab_select_heure[$b]["id_heure"]) OR ($id_creneau_creer == $tab_select_heure[$b]["id_heure"])){
				$selected=" selected='selected'";
			}
			else{
				$selected="";
			}
		}
		else{
			$selected="";
		}
		echo '
		<option value="'.$tab_select_heure[$b]["id_heure"].'"'.$selected.'>'.$tab_select_heure[$b]["creneaux"].' : '.$tab_select_heure[$b]["heure_debut"].' - '.$tab_select_heure[$b]["heure_fin"].'</option>
		';

	}
echo '
			</select>

			</td>
		</tr>
		<tr class="lignepaire">
			<td>
	';


	// On vérifie comment ce cours commence
	
	if (isset($deb_creer)) {
		// On vérifie comment ce nouveau cours commence
		if ($deb_creer == "debut") {
			$rep_cours["heuredeb_dec"] = 0;
		}
		elseif($deb_creer == "milieu"){
			$rep_cours["heuredeb_dec"] = "0.5";
		} else {
			$rep_cours["heuredeb_dec"] = NULL;
		}
	}

if (isset($rep_cours["heuredeb_dec"])) {
		if ($rep_cours["heuredeb_dec"] === 0) {
		$selected0 = " selected='selected'";
		$selected5 = '';
	}
	else if ($rep_cours["heuredeb_dec"] == "0.5") {
		$selected0 = '';
		$selected5 = " selected='selected'";
	}
	else {
		$selected0 = "";
		$selected5 = "";
	}
}else {
	$selected0 = "";
	$selected5 = "";
}

echo '
			<select name="heure_debut">
				<option value="0"'.$selected0.'>'.LESSON_START_AT_THE_BEGINNING.'</option>
				<option value="0.5"'.$selected5.'>'.LESSON_START_AT_THE_MIDDLE.'</option>
			</select>

			</td>
			<td>
	';
	// On détermine le selected de la duree
    $selected = array();
	$selected[1] = $selected[2] = $selected[3] = $selected[4] = $selected[5] = $selected[6] = $selected[7] = $selected[8] = " ";
	$selected[9] = $selected[10] = $selected[11] = $selected[12] = $selected[13] = $selected[14] = $selected[15] = $selected[16] = " ";
	if (isset($rep_cours["duree"])) {
        $index = $rep_cours["duree"];
		$selected[$index] = " selected='selected'";       
	}
    else {
		$selected[2] = " selected='selected'";
	}

echo '
			<select name="duree">
				<option value="1"'.$selected[1].'>'.HOUR1.'</option>
				<option value="2"'.$selected[2].'>'.HOUR2.'</option>
				<option value="3"'.$selected[3].'>'.HOUR3.'</option>
				<option value="4"'.$selected[4].'>'.HOUR4.'</option>
				<option value="5"'.$selected[5].'>'.HOUR5.'</option>
				<option value="6"'.$selected[6].'>'.HOUR6.'</option>
				<option value="7"'.$selected[7].'>'.HOUR7.'</option>
				<option value="8"'.$selected[8].'>'.HOUR8.'</option>
				<option value="9"'.$selected[9].'>'.HOUR9.'</option>
				<option value="10"'.$selected[10].'>'.HOUR10.'</option>
				<option value="11"'.$selected[11].'>'.HOUR11.'</option>
				<option value="12"'.$selected[12].'>'.HOUR12.'</option>
				<option value="13"'.$selected[13].'>'.HOUR13.'</option>
				<option value="14"'.$selected[14].'>'.HOUR14.'</option>
				<option value="15"'.$selected[15].'>'.HOUR15.'</option>
				<option value="16"'.$selected[16].'>'.HOUR16.'</option>
			</select>

			</td>
			<td>

			<select name="choix_semaine">
				<option value="0">'.ALL_WEEKS.'</option>
		';
		// on récupère les types de semaines

	$req_semaines = mysql_query("SELECT SQL_SMALL_RESULT DISTINCT type_edt_semaine FROM edt_semaines WHERE type_edt_semaine != '' LIMIT 5 ");
	$nbre_semaines = mysql_num_rows($req_semaines);

	for ($s=0; $s<$nbre_semaines; $s++) {
			$rep_semaines[$s]["type_edt_semaine"] = mysql_result($req_semaines, $s, "type_edt_semaine");
			if (isset($rep_cours["id_semaine"])) {
				if ($rep_cours["id_semaine"] == $rep_semaines[$s]["type_edt_semaine"]) {
					$selected = " selected='selected'";
				}
				else $selected = "";
			}
			else $selected = "";
		echo '
				<option value="'.$rep_semaines[$s]["type_edt_semaine"].'"'.$selected.'>Semaine '.$rep_semaines[$s]["type_edt_semaine"].'</option>
		';
	}

echo '
			</select>

			</td>
		</tr>
		<tr class="ligneimpaire">
			<td>

			<select  name="login_salle">
				<option value="rien">'.CLASSROOM.'</option>
	';
	// Choix de la salle
	$tab_select_salle = renvoie_liste("salle");

	for($c=0;$c<count($tab_select_salle);$c++) {
		if(isset($rep_cours["id_salle"])){
			if($rep_cours["id_salle"] == $tab_select_salle[$c]["id_salle"]){
				$selected=" selected='selected'";
			}
			else{
				$selected="";
			}
		}
		else{
			$selected="";
		}
			// On vérifie si le nom de l asalle existe vraiment
			if ($tab_select_salle[$c]["nom_salle"] == "") {
				$tab_select_salle[$c]["nom_salle"] = $tab_select_salle[$c]["numero_salle"];
			}
			if (SalleDisponible($tab_select_salle[$c]["id_salle"], $rep_cours["jour_semaine"], $rep_cours["id_definie_periode"], $rep_cours["duree"], $rep_cours["heuredeb_dec"], $rep_cours["id_semaine"], -1, $message, $rep_cours["id_calendrier"])) {
				echo "<option value='".$tab_select_salle[$c]["id_salle"]."'".$selected.">".$tab_select_salle[$c]["nom_salle"]."</option>\n";
			}
			else if ($tab_select_salle[$c]["id_salle"] == $rep_cours["id_salle"]) {
				echo "<option value='".$tab_select_salle[$c]["id_salle"]."'".$selected.">".$tab_select_salle[$c]["nom_salle"]."</option>\n";			
			}
			
			
	}
echo '
			</select>

			</td>
			<td>
    ';

	$req_calendrier = mysql_query("SELECT * FROM edt_calendrier WHERE etabferme_calendrier = '1' AND etabvacances_calendrier = '0'");
	$nbre_calendrier = mysql_num_rows($req_calendrier);
    if ($nbre_calendrier == 0) {
        echo '		<input type="hidden" name="periode_calendrier" value="0" />';

    }
    else {

        echo '
			        <select name="periode_calendrier">
				        <option value="0">'.ENTIRE_YEAR.'</option>
	        ';
		    
	    // ================================================== Choix de la période définie dans le calendrier ================================
    
        $req_id_classe = mysql_query("SELECT id_classe FROM j_groupes_classes WHERE id_groupe = '".$rep_cours['id_groupe']."' ");
        
        // ==== On récupère l'id de la classe concernée
        if ($rep_id_classe = mysql_fetch_array($req_id_classe)) {
            $id_classe = $rep_id_classe['id_classe'];
        }
        else {
            $id_classe = 0;
        }
    
	    for ($a=0; $a<$nbre_calendrier; $a++) {
		    $rep_calendrier[$a]["id_calendrier"] = mysql_result($req_calendrier, $a, "id_calendrier");
		    $rep_calendrier[$a]["nom_calendrier"] = mysql_result($req_calendrier, $a, "nom_calendrier");
		    $rep_calendrier[$a]["classe_concerne_calendrier"] = mysql_result($req_calendrier, $a, "classe_concerne_calendrier");
            $classes_concernes = explode(";", $rep_calendrier[$a]['classe_concerne_calendrier']);
            if ((in_array($id_classe, $classes_concernes) AND ($id_classe != 0)) OR ($id_classe == 0)) {
    
	            if(isset($rep_cours["id_calendrier"])){
		            if($rep_cours["id_calendrier"] == $rep_calendrier[$a]["id_calendrier"]){
			            $selected=" selected='selected'";
		            }
		            else{
			            $selected="";
		            }
	            }
	            else{
		            $selected="";
	            }
        
		        echo '
			        <option value="'.$rep_calendrier[$a]["id_calendrier"].'"'.$selected.'>'.$rep_calendrier[$a]["nom_calendrier"].'</option>
		        ';
            }
            unset($classes_concernes);
	    }
    
        echo '
			    </select>
            ';
    }
    echo '</td>';

    echo '
			<td>
		<input type="hidden" name="id_cours" value="'.$id_cours.'" />
		<input type="hidden" name="type_edt" value="'.$type_edt.'" />
		<input type="hidden" name="identite" value="'.$identite.'" />
		<input type="hidden" name="aid" value="'.$rep_cours["id_groupe"].'" />
	';
	// Cas où il s'agit de la création d'un cours
	if ($cours == "aucun" OR $modifier_cours == "non") {
		echo '		<input type="hidden" name="modifier_cours" value="non" />';
	} else {
		echo '		<input type="hidden" name="modifier_cours" value="ok" />';
	}
		echo '
		<input type="submit" name="Enregistre" value='.REGISTER.' />
		

			</td>

		</tr>

	</table>
		</form>';



echo '
	</fieldset>
	';

}// if $autorise...
else {
	die();
}
?>

	</div>

<?php
$ua = getenv("HTTP_USER_AGENT");
if (!strstr($ua, "MSIE 6.0")) {
echo "</div>";
echo "</div>";
}
// inclusion du footer
require("../lib/footer.inc.php");
?>