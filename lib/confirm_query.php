<?php
/*
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
require_once("../lib/initialisations.inc.php");

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

function AfficheNiveauGraviteRequete($_texte,$_niveau){
    if ($_niveau<="1") {
        return $_texte;
    } else {
        return "<span style=\"color: red; font-weight: bold;\">".$_texte."</span>";
    }
}

// Controle attaques CSRF
check_token();

// Initialisation
$liste_cible = isset($_POST["liste_cible"]) ? $_POST["liste_cible"] :(isset($_GET["liste_cible"]) ? $_GET["liste_cible"] :NULL);
$liste_cible2 = isset($_POST["liste_cible2"]) ? $_POST["liste_cible2"] :(isset($_GET["liste_cible2"]) ? $_GET["liste_cible2"] :NULL);
$liste_cible3 = isset($_POST["liste_cible3"]) ? $_POST["liste_cible3"] :(isset($_GET["liste_cible3"]) ? $_GET["liste_cible3"] :NULL);
$action = isset($_POST["action"]) ? $_POST["action"] :(isset($_GET["action"]) ? $_GET["action"] :NULL);
$cible1 = isset($_POST["cible1"]) ? $_POST["cible1"] :NULL;
$cible2 = isset($_POST["cible2"]) ? $_POST["cible2"] :NULL;
$cible3 = isset($_POST["cible3"]) ? $_POST["cible3"] :NULL;
$k = isset($_POST["k"]) ? $_POST["k"] :(isset($_GET["k"]) ? $_GET["k"] :NULL);

//if (!PeutEffectuerActionSuppression($_SESSION["login"],$action,$liste_cible1,$liste_cible2,$liste_cible3)) {
if (!PeutEffectuerActionSuppression($_SESSION["login"],$action,$liste_cible,$liste_cible2,$liste_cible3)) {
    header("Location: ../logout.php?auto=1");
    die();
}

//debug_var();

if(isset($_GET['chemin_retour'])) {
	$_SESSION['chemin_retour']=$_GET['chemin_retour'];
}

if (isset($liste_cible)) $tab_cible1 = explode(";", $liste_cible);
if (isset($liste_cible2)) $tab_cible2 = explode(";", $liste_cible2);
if (isset($liste_cible3)) $tab_cible3 = explode(";", $liste_cible3);
if (isset($tab_cible1)) {$nb_cible1 = count($tab_cible1);} else {$nb_cible1 = 0;}
if (!isset($k)) {$k = 0;}
if (($k < $nb_cible1) and ($tab_cible1[$k] != '')){
    $cible1=$tab_cible1[$k];
    if (isset($tab_cible2)) {$cible2=$tab_cible2[$k];} else { $cible2='';}
    if (isset($tab_cible3)) {$cible3=$tab_cible3[$k];} else { $cible3='';}

    switch ($action) {

    // Suppression d'un prof d'une aid
    case "del_prof_aid":
    $nombre_req = 2;
    $mess[0] = "Table de jointure aid/utilisateurs";
    $test_nb[0] = "SELECT * FROM j_aid_utilisateurs WHERE id_utilisateur='$cible1' and id_aid = '$cible2' and indice_aid='$cible3'";
    $req[0] = "DELETE FROM j_aid_utilisateurs WHERE id_utilisateur='$cible1' and id_aid = '$cible2' and indice_aid='$cible3'";
    $mess[1] = "Table de jointure aid/utilisateurs pouvant modifier les fiches projets";
    $test_nb[1] = "SELECT * FROM j_aidcateg_utilisateurs WHERE id_utilisateur='$cible1' and indice_aid='$cible3'";
    $req[1] = "DELETE FROM j_aidcateg_utilisateurs WHERE id_utilisateur='$cible1' and indice_aid='$cible3'";
    break;

    // Suppression d'un gestionnaire d'une aid
    case "del_gest_aid":
    $nombre_req = 1;
    $mess[0] = "Table de jointure aid/utilisateurs";
    $test_nb[0] = "SELECT * FROM j_aid_utilisateurs_gest WHERE id_utilisateur='$cible1' and id_aid = '$cible2' and indice_aid='$cible3'";
    $req[0] = "DELETE FROM j_aid_utilisateurs_gest WHERE id_utilisateur='$cible1' and id_aid = '$cible2' and indice_aid='$cible3'";
    break;

    // Suppression d'un élève d'une aid
    case "del_eleve_aid":
    $nombre_req = 3;
    $mess[0] = AfficheNiveauGraviteRequete("Supression de l'élève de la table de correspondance aid<->eleves",1);
    $test_nb[0] = "SELECT * FROM j_aid_eleves WHERE login='$cible1' and id_aid = '$cible2' and indice_aid='$cible3'";
    $req[0] = "DELETE FROM j_aid_eleves WHERE login='$cible1' and id_aid = '$cible2' and indice_aid='$cible3'";
    $mess[1] = AfficheNiveauGraviteRequete("Supression de l'élève de la table de correspondance aid<->eleves responsable",1);
    $test_nb[1] = "SELECT * FROM j_aid_eleves_resp WHERE login='$cible1' and id_aid = '$cible2' and indice_aid='$cible3'";
    $req[1] = "DELETE FROM j_aid_eleves_resp WHERE login='$cible1' and id_aid = '$cible2' and indice_aid='$cible3'";
    $mess[2] = AfficheNiveauGraviteRequete("Suppression d'enregistrements de la table des appréciations aid",2);
    $test_nb[2] = "SELECT * FROM aid_appreciations WHERE login='$cible1' and id_aid = '$cible2' and indice_aid='$cible3'";
    $req[2] = "DELETE FROM aid_appreciations WHERE login='$cible1' and id_aid = '$cible2' and indice_aid='$cible3'";
    break;

    // Suppression d'un type d'AID
    case "del_type_aid":
    $nombre_req = 1;
    $mess[0] = "Tables des AID";
    $test_nb[0] = "SELECT * FROM aid_config WHERE indice_aid='$cible1'";
    $req[0] = "DELETE FROM aid_config WHERE indice_aid='$cible1'";
    break;


    // Suppression d'un établissement
    case "del_etab":
    $nombre_req = 2;
    $mess[0] = "Table des établissements";
    $test_nb[0] = "SELECT * from etablissements WHERE id='$cible1'";
    $req[0] = "DELETE from etablissements WHERE id='$cible1'";

    $mess[1] = "Table de jointure élève/établissements";
    $test_nb[1] = "SELECT * FROM j_eleves_etablissements WHERE id_etablissement='$cible1'";
    $req[1] = "DELETE from j_eleves_etablissements WHERE id_etablissement='$cible1'";
    break;

    case "del_resp":
    $nombre_req = 2;
    $mess[0] = "Table des responsables";
    $test_nb[0] = "SELECT * from responsables WHERE ereno='$cible1'";
    $req[0] = "DELETE from responsables WHERE ereno='$cible1'";

    $mess[1] = "Mise à jour de la table eleves";
    $test_nb[1] = "SELECT * FROM eleves WHERE ereno='$cible1'";
    $req[1] = "UPDATE eleves SET ereno='' WHERE ereno='$cible1'";

    break;


    case "del_utilisateur":
    // Suppression d'un utilisateur
    $message = "<font color=red>Attention : la suppression d'un utilisateur est irréversible !!!
    <br />Dans la plupart des cas, il est préférable de rendre un utilisateur inactif, plutôt que de le supprimer de la base.
    <br />Une telle suppression ne devrait pas avoir lieu en cours d'année. Si c'est le cas, cela peut entraîner la présence de données orphelines dans la base (moyennes et appréciations d'élèves, ...). Etes-vous sûr de vouloir continuer ?
    </font>";

    $mess[0] = "Table des utilisateurs :";
    $test_nb[0] = "SELECT * FROM utilisateurs WHERE login='$cible1'";
    $req[0] = "DELETE FROM utilisateurs WHERE login='$cible1'";

    $mess[1] = "Table de jointure élèves/".getSettingValue("gepi_prof_suivi")." :";
    $test_nb[1] = "SELECT * FROM j_eleves_professeurs WHERE professeur='$cible1'";
    $req[1] = "DELETE FROM j_eleves_professeurs WHERE professeur='$cible1'";

    $mess[2] = "Table de jointure Aid/professeurs responsables :";
    $test_nb[2] = "SELECT * FROM j_aid_utilisateurs WHERE id_utilisateur='$cible1'";
    $req[2] = "DELETE FROM j_aid_utilisateurs WHERE id_utilisateur='$cible1';";

    $mess[3] = "Table de définition groupe/professeur :";
    $test_nb[3] = "SELECT * FROM j_groupes_professeurs WHERE login='$cible1'";
    $req[3] = "DELETE FROM j_groupes_professeurs WHERE login='$cible1';";

    $nombre_req = 4;

    $mess[] = "Table de correspondance SSO :";
    $test_nb[] = "SELECT * FROM sso_table_correspondance WHERE login_gepi='$cible1'";
    $req[] = "DELETE FROM sso_table_correspondance WHERE login_gepi='$cible1';";
  	$nombre_req++;

    $mess[] = "Table de définition cpe/élève :";
    $test_nb[] = "SELECT * FROM j_eleves_cpe WHERE cpe_login='$cible1'";
    $req[] = "DELETE FROM j_eleves_cpe WHERE cpe_login='$cible1';";
  	$nombre_req++;

    $mess[] = "Table de définition scolarité/classe :";
    $test_nb[] = "SELECT * FROM j_scol_classes WHERE login='$cible1'";
    $req[] = "DELETE FROM j_scol_classes WHERE login='$cible1';";
	  $nombre_req++;

    $mess[] = "Table de jointure du module Inscription :";
    $test_nb[] = "SELECT * FROM inscription_j_login_items WHERE login='$cible1'";
    $req[] = "DELETE FROM inscription_j_login_items WHERE login='$cible1';";
	$nombre_req++;

	  $mess[] = "Table de jointure aid/utilisateurs pouvant modifier les fiches projets";
    $test_nb[] = "SELECT * FROM j_aidcateg_utilisateurs WHERE id_utilisateur='$cible1'";
    $req[] = "DELETE FROM j_aidcateg_utilisateurs WHERE id_utilisateur='$cible1'";
    $nombre_req++;

    $mess[] = "Table de jointure Aid/utilisateurs gestionnaires :";
    $test_nb[] = "SELECT * FROM j_aid_utilisateurs_gest WHERE id_utilisateur='$cible1'";
    $req[] = "DELETE FROM j_aid_utilisateurs_gest WHERE id_utilisateur='$cible1';";
    $nombre_req++;

    $mess[] = "Table des modèles de grilles PDF :";
    $test_nb[] = "select * from modeles_grilles_pdf_valeurs mv, modeles_grilles_pdf m where m.id_modele=mv.id_modele and m.login='$cible1'";
    $req[] = "delete from modeles_grilles_pdf_valeurs where id_modele in (select id_modele from modeles_grilles_pdf where login='$cible1');";
    $nombre_req++;

    $mess[] = "Table des valeurs des modèles de grilles PDF :";
    $test_nb[] = "select * from modeles_grilles_pdf where login='$cible1'";
    $req[] = "delete from modeles_grilles_pdf WHERE login='$cible1';";
    $nombre_req++;


	$test_existence=mysql_query("SHOW TABLES LIKE 'observatoire_config';");
	if(mysql_num_rows($test_existence)>0){
		$mess[] = "Table des responsables de l'observatoire :";
		$test_nb[] = "SELECT * FROM observatoire_config WHERE content='$cible1'";
		$req[] = "DELETE FROM observatoire_config WHERE content='$cible1';";

		$nombre_req++;
	}

	$test_existence=mysql_query("SHOW TABLES LIKE 'observatoire_j_resp_champ';");
	if(mysql_num_rows($test_existence)>0){
		$mess[] = "Table des responsables de champs particuliers de l'observatoire :";
		$test_nb[] = "SELECT * FROM observatoire_j_resp_champ WHERE login='$cible1'";
		$req[] = "DELETE FROM observatoire_j_resp_champ WHERE login='$cible1';";

		$nombre_req++;
	}

    break;

    // Suppression d'une matière
    case "del_matiere":

    $mess[0] = "Table des matières :";
    $test_nb[0] = "SELECT * from matieres WHERE matiere='$cible1'";
    $req[0] = "DELETE from matieres WHERE matiere='$cible1'";

    $mess[] = "Table de définition matière/groupe, groupes associés, notes et évaluations " .
    		"<br/><br/>><- ATTENTION! -><<br/> Beaucoup de données importantes risquent d'être affectées par cette suppression " .
    		"si vous validez la procédure. Le nombre d'enregistrements mentionné correspond au nombre de " .
    		"groupes qui vont être supprimés par héritage de la suppression de la matière, et pas au nombre " .
    		"réel de données qui seront supprimées.' :";
    $test_nb[] = "SELECT * FROM j_groupes_matieres WHERE id_matiere='$cible1'";
    $req[] = "DELETE FROM j_groupes_matieres jgm, " .
    		"groupes g, " .
    		"j_groupes_classes jgc, " .
    		"j_eleves_groupes jeg, " .
    		"j_groupes_professeurs jgp, " .
    		"cn_cahier_notes cn, " .
    		"matieres_appreciations ma, " .
    		"matieres_notes mn " .
    		"WHERE (" .
    		"jgm.id_matiere = '$cible1' and " .
    		"g.id = jgm.id_groupe and " .
    		"jgc.id_groupe = jgm.id_groupe and " .
    		"jeg.id_groupe = jgm.id_groupe and " .
    		"jgp.id_groupe = jgm.id_groupe and " .
    		"cn.id_groupe = jgm.id_groupe and " .
    		"ma.id_groupe = jgm.id_groupe and " .
    		"mn.id_groupe = jgm.id_groupe)";

    $mess[] = "Table de jointure professeurs/matières :";
    $test_nb[] = "SELECT * FROM j_professeurs_matieres WHERE id_matiere='$cible1'";
    $req[] = "DELETE FROM j_professeurs_matieres WHERE id_matiere='$cible1'";

    $mess[] = "Matière principale des fiches projets :";
    $test_nb[] = "SELECT * FROM aid WHERE matiere1='$cible1'";
    $req[] = "UPDATE aid SET matiere1 = '' WHERE matiere1='$cible1'";

    $mess[] = "Matière secondaire des fiches projets :";
    $test_nb[] = "SELECT * FROM aid WHERE matiere2='$cible1'";
    $req[] = "UPDATE aid SET matiere2 = '' WHERE matiere2='$cible1'";

    $nombre_req = 5;

	$test_existence=mysql_query("SHOW TABLES LIKE 'observatoire';");
	if(mysql_num_rows($test_existence)>0){
		$mess[] = "Table des niveaux (observatoire) :";
		$test_nb[] = "SELECT * FROM observatoire WHERE matiere ='$cible1'";
		$req[] = "DELETE FROM observatoire WHERE matiere ='$cible1'";

		$nombre_req++;
	}

	$test_existence=mysql_query("SHOW TABLES LIKE 'observatoire_comment';");
	if(mysql_num_rows($test_existence)>0){
		$mess[] = "Table des commentaires (observatoire) :";
		$test_nb[] = "SELECT * FROM observatoire_comment WHERE matiere ='$cible1'";
		$req[] = "DELETE FROM observatoire_comment WHERE matiere ='$cible1'";

		$nombre_req++;
	}

    break;

    case "del_eleve":
    //Suppression d'un élève
    $mess[0] = "Table des élèves";
    $test_nb[0] = "SELECT * FROM eleves WHERE login='$cible1'";
    $req[0] = "DELETE FROM eleves WHERE login='$cible1'";

    $mess[1] = "Table des moyennes";
    $test_nb[1] = "SELECT * FROM matieres_notes WHERE login='$cible1'";
    $req[1] = "DELETE FROM matieres_notes WHERE login='$cible1'";

    $mess[2] = "Table des groupes suivis";
    $test_nb[2] = "SELECT * FROM j_eleves_groupes WHERE login='$cible1'";
    $req[2] = "DELETE FROM j_eleves_groupes WHERE login='$cible1'";

    $mess[3] = "Table de jointure classe/élève";
    $test_nb[3] = "SELECT * FROM j_eleves_classes WHERE login='$cible1'";
    $req[3] = "DELETE FROM j_eleves_classes WHERE login='$cible1'";

    $mess[4] = "Table des appréciations AID";
    $test_nb[4] = "SELECT * FROM aid_appreciations WHERE login='$cible1'";
    $req[4] = "DELETE FROM aid_appreciations WHERE login='$cible1'";

    $mess[5] = "Table de jointure élève\Aid suivie";
    $test_nb[5] = "SELECT * FROM j_aid_eleves WHERE login='$cible1'";
    $req[5] = "DELETE FROM j_aid_eleves WHERE login='$cible1'";

    $mess[6] = "Table des appréciations";
    $test_nb[6] = "SELECT * FROM matieres_appreciations WHERE login='$cible1'";
    $req[6] = "DELETE FROM matieres_appreciations WHERE login='$cible1'";

    $mess[7] = "Table de jointure élève/".getSettingValue("gepi_prof_suivi")."";
    $test_nb[7] = "SELECT * FROM j_eleves_professeurs WHERE login='$cible1'";
    $req[7] = "DELETE FROM j_eleves_professeurs WHERE login='$cible1'";

    $mess[8] = "Table des avis des conseils de classe";
    $test_nb[8] = "SELECT * FROM avis_conseil_classe WHERE login='$cible1'";
    $req[8] = "DELETE FROM avis_conseil_classe WHERE login='$cible1'";

    $mess[9] = "Table de jointure élève/établissement";
    //$test_nb[9] = "SELECT * FROM j_eleves_etablissements WHERE id_eleve ='$cible1'";
    //$req[9] = "DELETE FROM j_eleves_etablissements WHERE id_eleve ='$cible1'";
    $test_nb[9] = "SELECT * FROM j_eleves_etablissements WHERE id_eleve ='$cible2'";
    $req[9] = "DELETE FROM j_eleves_etablissements WHERE id_eleve ='$cible2'";

    $mess[10] = "Table des notes du carnet de notes";
    $test_nb[10] = "SELECT * FROM cn_notes_devoirs WHERE login='$cible1'";
    $req[10] = "DELETE FROM cn_notes_devoirs WHERE login='$cible1';";

    $mess[11] = "Table des moyennes du carnet de notes";
    $test_nb[11] = "SELECT * FROM cn_notes_conteneurs WHERE login='$cible1'";
    $req[11] = "DELETE FROM cn_notes_conteneurs WHERE login='$cible1';";

    $mess[12] = "Table de jointure aid/eleves responsable";
    $test_nb[12] = "SELECT * FROM j_aid_eleves_resp WHERE login='$cible1'";
    $req[12] = "DELETE FROM j_aid_eleves_resp WHERE login='$cible1'";

    $mess[13] = "Table de utilisateurs";
    $test_nb[13] = "SELECT * FROM utilisateurs WHERE (login='$cible1' AND statut='eleve')";
    $req[13] = "DELETE FROM utilisateurs WHERE (login='$cible1' AND statut='eleve')";

    $nombre_req = 14;

	$test_existence=mysql_query("SHOW TABLES LIKE 'observatoire';");
	if(mysql_num_rows($test_existence)>0){
		$mess[] = "Table des niveaux (observatoire) :";
		$test_nb[] = "SELECT * FROM observatoire WHERE login ='$cible1'";
		$req[] = "DELETE FROM observatoire WHERE login ='$cible1'";

		$nombre_req++;
	}

	$test_existence=mysql_query("SHOW TABLES LIKE 'observatoire_suivi';");
	if(mysql_num_rows($test_existence)>0){
		$mess[] = "Table du suivi (observatoire) :";
		$test_nb[] = "SELECT * FROM observatoire_suivi WHERE login ='$cible1'";
		$req[] = "DELETE FROM observatoire_suivi WHERE login ='$cible1'";

		$nombre_req++;
	}

	$test_existence=mysql_query("SHOW TABLES LIKE 'observatoire_comment';");
	if(mysql_num_rows($test_existence)>0){
		$mess[] = "Table des commentaires (observatoire) :";
		$test_nb[] = "SELECT * FROM observatoire_comment WHERE login ='$cible1'";
		$req[] = "DELETE FROM observatoire_comment WHERE login ='$cible1'";

		$nombre_req++;
	}

    $mess[] = "Table des absences";
    $test_nb[] = "SELECT * FROM absences WHERE login ='$cible1'";
    $req[] = "DELETE FROM absences WHERE login ='$cible1'";

	$nombre_req++;

    $mess[] = "Table de jointure élève/régime";
    $test_nb[] = "SELECT * FROM j_eleves_regime WHERE login ='$cible1'";
    $req[] = "DELETE FROM j_eleves_regime WHERE login ='$cible1'";
	$nombre_req++;

	$test_existence=mysql_query("SHOW TABLES LIKE 'j_signalement';");
	if(mysql_num_rows($test_existence)>0){
		$mess[] = "Table des signalements d'erreurs d'affectation";
		$test_nb[] = "SELECT * FROM j_signalement WHERE login='$cible1'";
		$req[] = "DELETE FROM j_signalement WHERE login='$cible1'";

		$nombre_req++;
	}

	$mess[] = "Table jointure élève/cpe :";
	$test_nb[] = "SELECT * FROM j_eleves_cpe WHERE e_login ='$cible1'";
	$req[] = "DELETE FROM j_eleves_cpe WHERE e_login ='$cible1'";
	$nombre_req++;

    break;

    case "del_classe":
    //Suppression d'une classe
    $mess[0] = "Table des classes :";
    $test_nb[0] = "SELECT * FROM classes WHERE id='$cible1'";
    $req[0] = "DELETE FROM classes WHERE id='$cible1'";

    $mess[1] = "Table de définition classe/groupe :";
    $test_nb[1] = "SELECT * FROM j_groupes_classes WHERE id_classe='$cible1'";
    $req[1] = "DELETE FROM j_groupes_classes WHERE id_classe='$cible1'";

    $mess[2] = "Table de gestion des périodes de la classe :";
    $test_nb[2] = "SELECT * FROM periodes WHERE id_classe='$cible1'";
    $req[2] = "DELETE FROM periodes WHERE id_classe='$cible1'";

    $mess[3] = "Table de jointure classe/élève :";
    $test_nb[3] = "SELECT * FROM j_eleves_classes WHERE id_classe ='$cible1'";
    $req[3] = "DELETE FROM j_eleves_classes WHERE id_classe ='$cible1'";

    $mess[4] = "Table de jointure élève/".getSettingValue("gepi_prof_suivi")."/classe :";
    $test_nb[4] = "SELECT * FROM j_eleves_professeurs WHERE id_classe ='$cible1'";
    $req[4] = "DELETE FROM j_eleves_professeurs WHERE id_classe ='$cible1'";

    $nombre_req = 5;

	$test_existence=mysql_query("SHOW TABLES LIKE 'periodes_observatoire';");
	if(mysql_num_rows($test_existence)>0){
		$mess[] = "Table des périodes de l'observatoire :";
		$test_nb[] = "SELECT * FROM periodes_observatoire WHERE id_classe ='$cible1'";
		$req[] = "DELETE FROM periodes_observatoire WHERE id_classe ='$cible1'";

		$nombre_req++;
	}

    break;
    case "del_aid":
    $mess[0] = "Table des Aid : ";
    $test_nb[0] = "SELECT * FROM aid WHERE (id='$cible1' and indice_aid='$cible3')";
    $req[0] = "DELETE FROM aid WHERE (id='$cible1' and indice_aid='$cible3')";

    $mess[1] = "Table de jointure elèves/Aid :";
    $test_nb[1] = "SELECT * FROM j_aid_eleves WHERE (id_aid='$cible1' and indice_aid='$cible3')";
    $req[1] = "DELETE FROM j_aid_eleves WHERE (id_aid='$cible1' and indice_aid='$cible3')";

    $mess[2] = "Table de jointure Aid/professeur :";
    $test_nb[2] = "SELECT * FROM j_aid_utilisateurs WHERE (id_aid='$cible1' and indice_aid='$cible3')";
    $req[2] = "DELETE FROM j_aid_utilisateurs WHERE (id_aid='$cible1' and indice_aid='$cible3')";

    $mess[3] = "Table des appréciations des AID : ";
    $test_nb[3] = "SELECT * FROM aid_appreciations WHERE (id_aid='$cible1' and indice_aid='$cible3')";
    $req[3] = "DELETE FROM aid_appreciations WHERE (id_aid='$cible1' and indice_aid='$cible3')";

    $mess[4] = "Table de jointure aid/utilisateurs pouvant modifier les fiches projets";
    $test_nb[4] = "SELECT * FROM j_aidcateg_utilisateurs WHERE indice_aid='$cible3'";
    $req[4] = "DELETE FROM j_aidcateg_utilisateurs WHERE indice_aid='$cible3'";

    $mess[5] = "Table de jointure aid/eleves responsable";
    $test_nb[5] = "SELECT * FROM j_aid_eleves_resp WHERE id_aid = '$cible1' and indice_aid='$cible3'";
    $req[5] = "DELETE FROM j_aid_eleves_resp WHERE id_aid = '$cible1' and indice_aid='$cible3'";

    $mess[6] = "Table de jointure Aid/gestionnaire :";
    $test_nb[6] = "SELECT * FROM j_aid_utilisateurs_gest WHERE (id_aid='$cible1' and indice_aid='$cible3')";
    $req[6] = "DELETE FROM j_aid_utilisateurs_gest WHERE (id_aid='$cible1' and indice_aid='$cible3')";

    $nombre_req = 7;

    break;
    case "retire_eleve":
    // Retrait d'un élève d'une classe

    $mess[0] = "Table de jointure classe/élève :";
    $test_nb[0] = "SELECT * FROM j_eleves_classes WHERE (login='$cible1' and periode = '$cible2')";
    $req[0] = "DELETE FROM j_eleves_classes WHERE (login='$cible1' and periode = '$cible2')";

    $nombre_req = 1;
    // Suppression de l'association élève-prof de suivi (seulement si l'élève est totalement dissocié d'une classe)
    if (mysql_num_rows(mysql_query("SELECT * FROM j_eleves_classes WHERE (login='$cible1' and id_classe = '$cible3')"))=='1') {
        $mess[] = "Table de jointure élève/".getSettingValue("gepi_prof_suivi")." : ";
        $test_nb[] = "SELECT * FROM j_eleves_professeurs WHERE (login='$cible1' and id_classe='$cible3')";
        $req[] = "DELETE FROM j_eleves_professeurs WHERE (login='$cible1' and id_classe='$cible3')";
        $nombre_req++;
    }
    // Suppression de l'association élève-cpe (seulement si l'élève est totalement dissocié d'une classe)
    if (mysql_num_rows(mysql_query("SELECT * FROM j_eleves_classes WHERE (login='$cible1' and id_classe = '$cible3')"))=='1') {
        $mess[] = "Table de jointure élève/cpe : ";
        $test_nb[] = "SELECT * FROM j_eleves_cpe WHERE (e_login='$cible1')";
        $req[] = "DELETE FROM j_eleves_cpe WHERE (e_login='$cible1')";
        $nombre_req++;
    }
    // Suppression des associations élève-groupe
    if (mysql_num_rows(mysql_query("SELECT * FROM j_eleves_groupes WHERE (login='$cible1' and periode = '$cible2')"))>='1') {
        $mess[] = "Table de jointure élève/groupes (l'élève sera supprimé de tous les enseignements pour la période considérée) : ";
        $test_nb[] = "SELECT * FROM j_eleves_groupes WHERE (login='$cible1' and periode='$cible2')";
        $req[] = "DELETE FROM j_eleves_groupes WHERE (login='$cible1' and periode='$cible2')";
        $nombre_req++;
    }


    break;

    }

    if (!isset($_POST['is_posted'])) {
        //**************** EN-TETE *****************
        $titre_page = "page de confirmation";
        require_once("../lib/header.inc.php");
        //**************** FIN EN-TETE *****************
        ?><form action="confirm_query.php" method="post" enctype="application/x-www-form-urlencoded"><?php

		//=====================
		// Sécurité: 20101118
		//echo "<input type='hidden' name='csrf_alea' value='".$csrf_alea."' />\n";
		echo add_token_field();
		//=====================

        echo "<p class='grand'>Confirmation de la suppression : ";
        echo "<input type='submit' name='confirm' value='Oui' /> ";
        echo "<input type='submit' name='confirm' value='Non' /></p>";
        if (isset($message)) echo "<p>$message</p>";
        echo "<p>Requête(s) à effectuer : <br /><br />";
        for ($c=0; $c<$nombre_req; $c++) {
            $call = @mysql_query($test_nb[$c]);
			//echo "\$test_nb[$c]=".$test_nb[$c]."<br />";
            if($call) {
				$nb_lignes = mysql_num_rows($call);
				if ($nb_lignes != 0) {
					echo "<span class='bold'>$mess[$c] : </span><br />";
					echo "<span class='small'>--> $req[$c] ($nb_lignes "; if ($nb_lignes == 1) { echo "enregistrement"; } else { echo "enregistrements";} echo ")</span><br /><br />";
				}
			}
        }
        echo "</p>";
        ?>
        <input type="hidden" name="action" value="<?php echo $action; ?>" />
        <input type="hidden" name="cible1" value="<?php echo $cible1; ?>" />
        <input type="hidden" name="cible2" value="<?php echo $cible2; ?>" />
        <input type="hidden" name="cible3" value="<?php echo $cible3; ?>" />
        <?php if (isset($liste_cible)) { ?>
            <input type="hidden" name="liste_cible" value="<?php echo $liste_cible; ?>" />
        <?php }
        if (isset($liste_cible2)) { ?>
            <input type="hidden" name="liste_cible2" value="<?php echo $liste_cible2; ?>" />
        <?php }
        if (isset($liste_cible3)) { ?>
            <input type="hidden" name="liste_cible3" value="<?php echo $liste_cible3; ?>" />
        <?php } ?>
        <input type="hidden" name="k" value="<?php echo $k; ?>" />
        <input type=hidden name="is_posted" value="1" />
        <input type="submit" name="confirm" value="Oui" />
        <input type="submit" name="confirm" value="Non" />
        </form>
        <?php
    } else {
        $succes = 'yes';
        if ($_POST['confirm'] == "Oui") {
            for ($c=0; $c<$nombre_req; $c++) {
                $call = @mysql_query($test_nb[$c]);
				if($call) {
					$nb_lignes = mysql_num_rows($call);
					if ($nb_lignes != 0) {
						$tab_action = explode(";", $req[$c]);
						$nbligne = count($tab_action);
						for ($i = 0; $i < $nbligne; $i++) {
							$do = mysql_query($tab_action[$i]);
						}
					}
				}
            }
            //Vérification
            for ($c=0; $c<$nombre_req; $c++) {
                //$call = mysql_query($test_nb[$c]);
                $call = @mysql_query($test_nb[$c]);
				if($call) {
					$nb_lignes = mysql_num_rows($call);
					if ($nb_lignes != 0) {
						$succes = 'no';
						//**************** EN-TETE *****************
						$titre_page = "page de confirmation";
						require_once("../lib/header.inc.php");
						//**************** FIN EN-TETE *****************

						echo "<p><span class='bold'>$mess[$c] : </span><br />";
						echo "Il y a eu un problème lors de l'exécution de la requête :<br />";
						echo "$req[$c]<br />";
						echo "Il reste $nb_lignes "; if ($nb_lignes == 1) { echo "enregistrement"; } else { echo "enregistrements";} echo " à supprimer !<br /></p>";
					}
				}
            }
            $k++;
        } else {
            $k++;
        }

        if ($succes == 'no') {
            echo "<p><a href='confirm_query.php?cible1=$cible1&amp;cible2=$cible2&amp;cible3=$cible3&amp;action=$action&amp;k=$k&amp;liste_cible=$liste_cible";
            if (isset($liste_cible2)) echo "&amp;liste_cible2=$liste_cible2";
            if (isset($liste_cible3)) echo "&amp;liste_cible3=$liste_cible3";
			//===========================
			// Sécurité: 20101118
			//echo "&amp;csrf_alea=".$csrf_alea;
			echo add_token_in_url();
			//===========================
            echo "'>Suite</a></p>";
            die();
        } else {
            $page ="Location: confirm_query.php?cible1=$cible1&cible2=$cible2&cible3=$cible3&action=$action&k=$k&liste_cible=$liste_cible";
            if (isset($liste_cible2)) $page .= "&liste_cible2=$liste_cible2";
            if (isset($liste_cible3)) $page .= "&liste_cible3=$liste_cible3";
			//===========================
			// Sécurité: 20101118
			//$page.="&csrf_alea=".$csrf_alea;
			$page.=add_token_in_url(false);
			//===========================
            header($page);
            die();
        }
    }
} else {
    $temp = $_SESSION['chemin_retour'];
    unset($_SESSION['chemin_retour']);
    header("Location: ".$temp."");
}

?>
</body>
</html>
