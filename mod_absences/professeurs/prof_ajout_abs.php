<?php
/*
 *
 * $Id$
 *
 * Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Christian Chapel
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

// Initialisation des feuilles de style après modification pour améliorer l'accessibilité
$accessibilite="y";

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
}

if (!checkAccess()) {
    header("Location: ../../logout.php?auto=1");
    die();
}

//On vérifie si le module est activé
if (getSettingValue("active_module_absence")!='y') {
    die("Le module n'est pas activé.");
}

// ================= fonctions de sécuritée =======================
// uid de pour ne pas refaire renvoyer plusieurs fois le même formulaire
// autoriser la validation de formulaire $uid_post===$_SESSION['uid_prime']
if(empty($_SESSION['uid_prime'])) {
	$_SESSION['uid_prime']='';
}
$uid_post = isset($_GET["uid_post"]) ? $_GET["uid_post"] : (isset($_POST["uid_post"]) ? $_POST["uid_post"] : NULL);

$uid = md5(uniqid(microtime(), 1));
// on remplace les %20 par des espaces
$uid_post = my_eregi_replace('%20',' ',$uid_post);
if($uid_post===$_SESSION['uid_prime']) {
	$valide_form = 'yes';
} else {
	$valide_form = 'no';
}
$_SESSION['uid_prime'] = $uid;

// ================= fin des fonctions de sécuritée =======================
// On inclut les fonctions
require_once("fonctions_prof_abs.php");

$menuBar = isset($_GET["menuBar"]) ? $_GET["menuBar"] : NULL;
$etape = isset($_POST["etape"]) ? $_POST["etape"] : NULL;
$d_heure_absence_eleve = isset($_POST['d_heure_absence_eleve']) ? $_POST['d_heure_absence_eleve'] : NULL;
$a_heure_absence_eleve = isset($_POST["a_heure_absence_eleve"]) ? $_POST["a_heure_absence_eleve"] : NULL;
$d_heure_absence_eleve_ins = isset($_POST["d_heure_absence_eleve_ins"]) ? $_POST["d_heure_absence_eleve_ins"] : NULL;
$a_heure_absence_eleve_ins = isset($_POST["a_heure_absence_eleve_ins"]) ? $_POST["a_heure_absence_eleve_ins"] : NULL;
$heuredebut_definie_periode = isset($_POST["heuredebut_definie_periode"]) ? $_POST["heuredebut_definie_periode"] : NULL;
$heurefin_definie_periode = isset($_POST["heurefin_definie_periode"]) ? $_POST["heurefin_definie_periode"] : NULL;
$d_date_absence_eleve = isset($_POST["d_date_absence_eleve"]) ? $_POST["d_date_absence_eleve"] : date('d/m/Y');
$classe = isset($_POST["classe"]) ? $_POST["classe"] : "";
$eleve_initial = isset($_POST["eleve_initial"]) ? $_POST["eleve_initial"] :"";

//on mets le groupe dans la session, pour naviguer entre absence, cahier de texte et autres
if ($classe != "") {
    $_SESSION['id_groupe_session'] = $classe;
}

$passer_cahier_texte = isset($_POST["passer_cahier_texte"]) ? $_POST["passer_cahier_texte"] :false;
if ($passer_cahier_texte == "true") {
    header("Location: ../../cahier_texte/index.php");
}

if (!empty($d_date_absence_eleve) AND $etape=='1' AND !empty($eleve_absent)) {
	$d_date_absence_eleve = date_fr($d_date_absence_eleve);
}
if (getSettingValue("active_module_trombinoscopes")=='y') {
	$modif_photo = isset($_POST["photo"]) ? $_POST["photo"] : NULL;
	$photo = isset($_POST["photo"]) ? $_POST["photo"] : getPref($_SESSION["login"],"absences_avec_photo","");
}else{
	//$modif_photo = NULL;
	$modif_photo = "";
	//$photo = NULL;
	$photo = "";
}

// Si une classe et un élève sont définis en même temps, on réinitialise
if ($classe!="" and $eleve_initial!="") {
    $classe="";
    $eleve_initial="";
}
$etape = isset($_POST["etape"]) ? $_POST["etape"] : (isset($_GET["etape"]) ? $_GET["etape"] : 1);
$action_sql = isset($_POST["action_sql"]) ? $_POST["action_sql"] : NULL;
$id = isset($_POST["id"]) ? $_POST["id"] :"";
$saisie_absence_eleve = isset($_POST["saisie_absence_eleve"]) ? $_POST["saisie_absence_eleve"] : NULL;
$eleve_absent = isset($_POST["eleve_absent"]) ? $_POST["eleve_absent"] : NULL;
$active_absence_eleve = isset($_POST["active_absence_eleve"]) ? $_POST["active_absence_eleve"] : NULL;
$active_retard_eleve = isset($_POST["active_retard_eleve"]) ? $_POST["active_retard_eleve"] : NULL;
// Ajout Eric
$active_repas_eleve = isset($_POST["active_repas_eleve"]) ? $_POST["active_repas_eleve"] : NULL;
// Fin Ajout
$heure_retard_eleve = isset($_POST["heure_retard_eleve"]) ? $_POST["heure_retard_eleve"] : NULL;
$edt_enregistrement = isset($_POST["edt_enregistrement"]) ? $_POST["edt_enregistrement"] : NULL;
$premier_passage = isset($_POST["premier_passage"]) ? $_POST["premier_passage"] : NULL;
$passage_form = isset($_GET['passage_form']) ? $_GET['passage_form'] : (isset($_POST['passage_form']) ? $_POST['passage_form'] : NULL);

$passage_auto='';
$heure_choix = date('G:i');
$num_periode = periode_actuel($heure_choix);
$datej = date('Y-m-d');
$annee_scolaire = annee_en_cours_t($datej);

$miseajour='';
$verification = '0';
$id_absence_eleve = $id;
$total = '0';
$erreur = '0';
$nb = '0';
// On enregistre les préférences du professeur si la photo est cochée
if ($modif_photo == "avec_photo") {
	// On vérifie si la préférence existe déjà
	if (getPref($_SESSION["login"], 'absences_avec_photo', 'aucune') == 'aucune') {
		$query = mysqli_query($GLOBALS["mysqli"], "INSERT INTO preferences SET login = '".$_SESSION["login"]."', name = 'absences_avec_photo', value = 'avec_photo'");
	}elseif (getPref($_SESSION["login"], 'absences_avec_photo', 'aucune') != 'aucune') {
		$query = mysqli_query($GLOBALS["mysqli"], "UPDATE preferences SET value = '".$modif_photo."' WHERE login = '".$_SESSION["login"]."' AND name = 'absences_avec_photo'");
	}
}elseif ($modif_photo == "" AND $premier_passage == "ok") {
	if (getPref($_SESSION["login"], 'absences_avec_photo', 'aucune') != 'aucune') {
		$query = mysqli_query($GLOBALS["mysqli"], "UPDATE preferences SET value = 'n' WHERE login = '".$_SESSION["login"]."' AND name = 'absences_avec_photo'");
	}
}// fin du traitement de la préférence sur les photos

// on traite les demandes de l'utilisateur
if(($action_sql == "ajouter" or $action_sql == "modifier") and $valide_form==='yes') {
	$type_absence_eleve = isset($_POST['type_absence_eleve']) ? $_POST['type_absence_eleve'] : NULL;
	$d_date_absence_eleve_format_sql = date_sql($_POST['d_date_absence_eleve']);
	$a_date_absence_eleve_format_sql = $d_date_absence_eleve_format_sql;
	$justify_absence_eleve = "N";
	$motif_absence_eleve = "A";

	$nb_i = isset($_POST["nb_i"]) ? $_POST["nb_i"] : 1;
	$total = '0';

	while ($total < $nb_i) {
		if(!empty($heure_retard_eleve[$total])) {
			$type_absence_eleve = "R";
			$heure_retard_eleve_ins = $_POST['heure_retard_eleve'][$total];
		} else {
			$type_absence_eleve = "A";
		}
		// Identifiant de l'élève
		if(empty($_POST['active_absence_eleve'][$total])) {
			$_POST['active_absence_eleve'][$total]='';
		}
		$eleve_absent_ins = $_POST['eleve_absent'][$total];
		$active_absence_eleve_ins = $_POST['active_absence_eleve'][$total];
		if($active_absence_eleve_ins == "1" or !empty($heure_retard_eleve[$total])) {
			// on vérifie si une absences est déja définie et non justifiee  modif didier
			//requete dans la base absence eleve
			if ( $action_sql == "ajouter" ) {
				$requete = "SELECT * FROM absences_eleves
					WHERE eleve_absence_eleve='".$eleve_absent_ins."' AND
					d_date_absence_eleve <= '".$d_date_absence_eleve_format_sql."' AND
					a_date_absence_eleve >= '".$d_date_absence_eleve_format_sql."' AND
					type_absence_eleve = 'A' AND justify_absence_eleve= 'N'";
				$requete_retard = "SELECT * FROM absences_eleves
					WHERE eleve_absence_eleve='".$eleve_absent_ins."' AND
					d_date_absence_eleve = '".$d_date_absence_eleve_format_sql."' AND
					a_date_absence_eleve = '".$d_date_absence_eleve_format_sql."' AND
					type_absence_eleve = 'R' AND justify_absence_eleve= 'N'";
			}
			if ( $action_sql == "modifier" ) {
				$requete = "SELECT * FROM absences_eleves
					WHERE eleve_absence_eleve='".$eleve_absent_ins."' AND
					d_date_absence_eleve <= '".$d_date_absence_eleve_format_sql."' AND
					a_date_absence_eleve >= '".$d_date_absence_eleve_format_sql."' AND
					id_absence_eleve <> '".$id."' AND justify_absence_eleve= 'N'";
			}

			$resultat = mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
			$resultat_retard = mysqli_query($GLOBALS["mysqli"], $requete_retard) or die('Erreur SQL !'.$requete_retard.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
			$heuredebut_definie_periode_ins = $d_heure_absence_eleve;
			$heurefin_definie_periode_ins = $a_heure_absence_eleve;

			if(!isset($active_retard_eleve[$total])) {
				$active_retard_eleve[$total] = '0';
			}
			if($active_retard_eleve[$total] != '1') {
				//on prend les donnée pour les vérifier
				$miseajour = '';
				while ($data = mysqli_fetch_array($resultat)) {
					//id de la base sélectionné
					$id_abs = $data['id_absence_eleve'];
					//vérification
					if($data['d_heure_absence_eleve'] <= $heuredebut_definie_periode_ins and $data['a_heure_absence_eleve'] >= $heurefin_definie_periode_ins) {
						//on ne fait rien
					} else {
						if($data['d_heure_absence_eleve'] <= $heuredebut_definie_periode_ins and $data['a_heure_absence_eleve'] < $heurefin_definie_periode_ins) {
							//Update de Fin
							$id_abs = $data['id_absence_eleve'];
							$miseajour='fin';
							// vérification du courrier lettre de justificatif
							modif_suivi_du_courrier($id_abs, $eleve_absent_ins);
			  			}
						if($data['d_heure_absence_eleve'] >= $heuredebut_definie_periode_ins and $data['a_heure_absence_eleve'] > $heurefin_definie_periode_ins) {
							//Update de Début
                			$id_abs = $data['id_absence_eleve'];
							$miseajour='debut';
							// vérification du courrier lettre de justificatif
	  			  			modif_suivi_du_courrier($id_abs, $eleve_absent_ins);
			  			}
			  			if($data['d_heure_absence_eleve'] > $heuredebut_definie_periode_ins and $data['a_heure_absence_eleve'] < $heurefin_definie_periode_ins) {
							//Delete de l'enregistrement
	                        $req_delete = "DELETE FROM absences_eleves WHERE id_absence_eleve ='".$id_abs."'";
        	                $req_sql2 = mysqli_query($GLOBALS["mysqli"], $req_delete);
			  			}
					}
				} // fin while ($data = mysql_fetch_array($resultat))

				while ($data_retard = mysqli_fetch_array($resultat_retard)) {
					if ($data_retard['d_heure_absence_eleve'] >= $heuredebut_definie_periode_ins and $data_retard['d_heure_absence_eleve'] <= $heurefin_definie_periode_ins) {
						$id_ret = $data_retard['id_absence_eleve'];
						// supprime le retard de la base
						$req_delete = "DELETE FROM absences_eleves WHERE id_absence_eleve ='".$id_ret."'";
						$req_sql2 = mysqli_query($GLOBALS["mysqli"], $req_delete);
                	}
				}
			} // if($active_retard_eleve[$total]!='1')

			if($active_retard_eleve[$total]==='1') {
				while ($data = mysqli_fetch_array($resultat)) {
					if ($heure_retard_eleve[$total] >= $data['d_heure_absence_eleve'] and $heure_retard_eleve[$total] <= $data['a_heure_absence_eleve']) {
                    	$id_abs = $data['id_absence_eleve'];
						if($data['d_heure_absence_eleve']===$heuredebut_definie_periode_ins) {
							$req_delete = "DELETE FROM absences_eleves WHERE id_absence_eleve ='".$id_abs."'";
							$req_sql2 = mysqli_query($GLOBALS["mysqli"], $req_delete);
		    			} else {
                    		// modifie l'absences
                    		$req_modifie = "UPDATE absences_eleves SET a_heure_absence_eleve = '$heuredebut_definie_periode_ins' WHERE id_absence_eleve ='".$id_abs."'";
                    		$req_sql2 = mysqli_query($GLOBALS["mysqli"], $req_modifie);
			    		}
                	}
				}
			} // if($active_retard_eleve[$total]==='1')

			if(!empty($heure_retard_eleve[$total])) {
				$d_heure_absence_eleve_ins = $heure_retard_eleve[$total];
				$a_heure_absence_eleve_ins = '';
			} else {
				$d_heure_absence_eleve_ins = $d_heure_absence_eleve;
				$a_heure_absence_eleve_ins = $a_heure_absence_eleve;
			}

			if($erreur != 1) {
				if($miseajour==='debut' or $miseajour==='fin') {
					if($miseajour==='debut') {
						$requete="UPDATE ".$prefix_base."absences_eleves SET d_heure_absence_eleve = '$d_heure_absence_eleve_ins' WHERE id_absence_eleve = '".$id_abs."'";
					}
					if($miseajour==='fin') {
						$requete="UPDATE ".$prefix_base."absences_eleves SET a_heure_absence_eleve = '$a_heure_absence_eleve_ins' WHERE id_absence_eleve = '".$id_abs."'";
					}
					$resultat = mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
				}
				if($miseajour!='debut' and $miseajour!='fin') {
					$requete="INSERT INTO ".$prefix_base."absences_eleves (type_absence_eleve,eleve_absence_eleve,justify_absence_eleve,motif_absence_eleve,d_date_absence_eleve,a_date_absence_eleve,d_heure_absence_eleve,a_heure_absence_eleve,saisie_absence_eleve) values ('$type_absence_eleve','$eleve_absent_ins','$justify_absence_eleve','$motif_absence_eleve','$d_date_absence_eleve_format_sql','$a_date_absence_eleve_format_sql','$d_heure_absence_eleve_ins','$a_heure_absence_eleve_ins','$saisie_absence_eleve')";
					$resultat = mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
				}

				if ( $type_absence_eleve === 'A' ) {
					// connaitre l'id de l'enregistrement
					if ( $miseajour != 'debut' and $miseajour != 'fin' ) {
						$num_id = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
					}
					if ( $miseajour==='debut' or $miseajour === 'fin' ) {
						$num_id = $id_abs;
					}

					//envoie d'une lettre de justification
					$date_emis = date('Y-m-d');
					$heure_emis = date('H:i:s');
					$cpt_lettre_suivi = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."lettres_suivis WHERE quirecois_lettre_suivi = '".$eleve_absent_ins."' AND emis_date_lettre_suivi = '".$date_emis."' AND partde_lettre_suivi = 'absences_eleves'"),0);
					if( $cpt_lettre_suivi == 0 ) {
						//si aucune lettre n'a encore été demandé alors on en créer une
						$requete = "INSERT INTO ".$prefix_base."lettres_suivis (quirecois_lettre_suivi, partde_lettre_suivi, partdenum_lettre_suivi, quiemet_lettre_suivi, emis_date_lettre_suivi, emis_heure_lettre_suivi, type_lettre_suivi, statu_lettre_suivi) VALUES ('".$eleve_absent_ins."', 'absences_eleves', ',".$num_id.",', '".$_SESSION['login']."', '".$date_emis."', '".$heure_emis."', '6', 'en attente')";
						mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
					} else {
						//si une lettre a déjas été demandé alors on la modifi
						// on cherche la lettre concerné et on prend les id déjas disponible puis on y ajout le nouvelle id
						$requete_info ="SELECT * FROM ".$prefix_base."lettres_suivis  WHERE emis_date_lettre_suivi = '".$date_emis."' AND partde_lettre_suivi = 'absences_eleves'";
						$execution_info = mysqli_query($GLOBALS["mysqli"], $requete_info) or die('Erreur SQL !'.$requete_info.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
						while ( $donne_info = mysqli_fetch_array($execution_info)) {
							$id_lettre_suivi = $donne_info['id_lettre_suivi'];
							$id_deja_present = $donne_info['partdenum_lettre_suivi'];
						}
						$tableau_deja_existe = explode(',', $id_deja_present);
						if ( in_array($num_id, $tableau_deja_existe) ) {
							$id_ajout = $id_deja_present;
						} else {
							$id_ajout = $id_deja_present.$num_id.',';
						}
						$requete = "UPDATE ".$prefix_base."lettres_suivis SET partdenum_lettre_suivi = '".$id_ajout."', quiemet_lettre_suivi = '".$_SESSION['login']."', type_lettre_suivi = '6' WHERE id_lettre_suivi = '".$id_lettre_suivi."'";
						mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
					}
				}
			}
    	} // if($active_absence_eleve_ins == "1" or !empty($heure_retard_eleve[$total]))
    $total = $total + 1;
	} // while ($total < $nb_i)
}
/*======== Traitement dans la table absences_rb =============*/
if ($etape == 2 AND $classe != "toutes" AND $classe != "" AND $action_sql == "ajouter") {

		// On calcule tous les éléments dont on a besoin
	$explode_heuredeb = explode(":", $d_heure_absence_eleve);
	$explode_heurefin = explode(":", $a_heure_absence_eleve);
	$explode_date = explode("/", $d_date_absence_eleve);
	$ts_debut = mktime($explode_heuredeb[0], $explode_heuredeb[1], 0, $explode_date[1], $explode_date[0], $explode_date[2]);
	$ts_fin = mktime($explode_heurefin[0], $explode_heurefin[1], 0, $explode_date[1], $explode_date[0], $explode_date[2]);
	$ts_actu = mktime(date("H"), date("i"), 0, date("m"), date("d"), date("Y"));
	$jour_semaine = explode(" ", (date_frl(date_sql($d_date_absence_eleve))));
		// on récupère l'id du créneau de début de saisie
		// en tenantcompte toujours du réglage sur les créneaux
		if (getSettingValue("creneau_different") != 'n') {
			if (date("w") == getSettingValue("creneau_different")) {
				$req_creneau = mysqli_query($GLOBALS["mysqli"], "SELECT id_definie_periode FROM edt_creneaux_bis WHERE heuredebut_definie_periode = '".$d_heure_absence_eleve."'");
			}
			else {
			$req_creneau = mysqli_query($GLOBALS["mysqli"], "SELECT id_definie_periode FROM edt_creneaux WHERE heuredebut_definie_periode = '".$d_heure_absence_eleve."'");
			}
		}else {
			$req_creneau = mysqli_query($GLOBALS["mysqli"], "SELECT id_definie_periode FROM edt_creneaux WHERE heuredebut_definie_periode = '".$d_heure_absence_eleve."'");
		}
	$rep_creneau = mysqli_fetch_array($req_creneau);

	/* +++++++ Traitement des entrées +++++++++*/

		// Pour le cas où il y a au moins un absent
		$echo = "";
	for($a=0; $a<count($eleve_absent); $a++) {
		// On vérifie que cet élève a été coché absent
		if (isset($active_absence_eleve[$a])) {
			if ($active_absence_eleve[$a] == 1) {
				// On récupère le nom et le prénom de l'élève
				$req_noms = mysqli_query($GLOBALS["mysqli"], "SELECT nom, prenom FROM eleves WHERE login = '".$eleve_absent[$a]."'");
				$noms = mysqli_fetch_array($req_noms);

				// On vérifie que cette absence exacte n'a pas été encore saisie (login élève a la date et heure du début de l'absence
				$cherche_abs = mysqli_query($GLOBALS["mysqli"], "SELECT id FROM absences_rb WHERE eleve_id = '".$eleve_absent[$a]."' AND debut_ts = '".$ts_debut."'");
				$nbr_cherche = mysqli_num_rows($cherche_abs);
				if ($nbr_cherche == 0) {
					// On insère alors l'absence dans la base
					$saisie_sql = "INSERT INTO absences_rb (eleve_id, groupe_id, edt_id, jour_semaine, creneau_id, debut_ts, fin_ts, date_saisie, login_saisie) VALUES ('".$eleve_absent[$a]."', '".$classe."', '0', '".$jour_semaine[0]."', '".$rep_creneau["id_definie_periode"]."', '".$ts_debut."', '".$ts_fin."', '".$ts_actu."', '".$_SESSION["login"]."')";
					$insere_abs = mysqli_query($GLOBALS["mysqli"], $saisie_sql) OR DIE ('Erreur SQL !'.$saisie_sql.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));//('Impossible d\'enregistrer l\'absence de '.$eleve_absent[$a]);
					$echo .= '<p class="enregistre_bon">L\'absence de '.$noms["prenom"].' '.$noms["nom"].' est bien enregistrée !</p>';
				}else {
					$echo .='<p class="enregistre_deja">L\'absence de '.$noms["prenom"].' '.$noms["nom"].' a déjà été saisie ! </p>';
				}
			}
		}
		if (isset($active_retard_eleve[$a])) {
			if ($active_retard_eleve[$a] == 1){
				// On récupère le nom et le prénom de l'élève
				$req_noms = mysqli_query($GLOBALS["mysqli"], "SELECT nom, prenom FROM eleves WHERE login = '".$eleve_absent[$a]."'");
				$noms = mysqli_fetch_array($req_noms);

				// On vérifie que ce retard ne correspond pas à une absence ou n'a pas été encore saisie (login élève a la date et heure du début de l'absence
				$cherche_ret = mysqli_query($GLOBALS["mysqli"], "SELECT id FROM absences_rb WHERE eleve_id = '".$eleve_absent[$a]."' AND debut_ts = '".$ts_debut."'");
				$nbr_cherche = mysqli_num_rows($cherche_ret);
				if ($nbr_cherche == 0) {

					// On insère alors le retard dans la base
					$saisie_sql = "INSERT INTO absences_rb (eleve_id, retard_absence, groupe_id, edt_id, jour_semaine, creneau_id, debut_ts, fin_ts, date_saisie, login_saisie) VALUES ('".$eleve_absent[$a]."', 'R', '".$classe."', '0', '".$jour_semaine[0]."', '".$rep_creneau["id_definie_periode"]."', '".$ts_debut."', '".$ts_fin."', '".$ts_actu."', '".$_SESSION["login"]."')";

					$insere_abs = mysqli_query($GLOBALS["mysqli"], $saisie_sql) OR DIE ('Erreur SQL !'.$saisie_sql.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));//('Impossible d\'enregistrer l\'absence de '.$eleve_absent[$a]);
					$echo .= '<p class="enregistre_bon">Le retard de '.$noms["prenom"].' '.$noms["nom"].' est bien enregistré !</p>';
				}else {
					// On modifie l'absence pour un retard
					// l'absence en question est old_mysql_result($cherche_ret, 0,"id");
					$id_abs = old_mysql_result($cherche_ret, 0,"id");
					$update = mysqli_query($GLOBALS["mysqli"], "UPDATE absences_rb SET retard_absence = 'R'
															WHERE id = '".$id_abs."'");
					$echo .='<p class="enregistre_modifie">L\'absence de '.$noms["prenom"].' '.$noms["nom"].' a été modifiée en retard ! </p>';
                  
				}

			}
		}
		
		//Ajout Eric traitement des repas
		if (isset($active_repas_eleve[$a])) {
			if ($active_repas_eleve[$a] == 1){
				// On récupère le nom et le prénom de l'élève
				$req_noms = mysqli_query($GLOBALS["mysqli"], "SELECT nom, prenom FROM eleves WHERE login = '".$eleve_absent[$a]."'");
				$noms = mysqli_fetch_array($req_noms);
				$date_du_jour = date ('Y-m-d');
				// On insère alors le retard dans la base
				$saisie_sql = "INSERT INTO absences_repas (date_repas, id_groupe,eleve_id, pers_id ) VALUES ('".$date_du_jour."', '".$classe."', '".$eleve_absent[$a]."','".$_SESSION["login"]."')";
				//echo $saisie_sql;
				$insere_abs = mysqli_query($GLOBALS["mysqli"], $saisie_sql) OR DIE ('Erreur SQL !'.$saisie_sql.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));//('Impossible d\'enregistrer l\'absence de '.$eleve_absent[$a]);
				$echo .= '<p class="enregistre_bon">Le repas pour '.$noms["prenom"].' '.$noms["nom"].' est bien enregistré !</p>';
			}
		}
		//Fin Ajout Eric
		
	} // for $a

		// Le cas où il n'y a pas d'absent
	if ($echo == "") {
			// On vérifie que cet appel n'est pas déjà enregistré
		$req_verif = mysqli_query($GLOBALS["mysqli"], "SELECT id FROM absences_rb WHERE eleve_id = 'appel' AND groupe_id = '".$classe."' AND jour_semaine = '".$jour_semaine[0]."' AND creneau_id = '".$rep_creneau["id_definie_periode"]."' AND debut_ts = '".$ts_debut."' AND fin_ts = '".$ts_fin."' AND login_saisie = '".$_SESSION["login"]."'");
		$nbre_verif = mysqli_num_rows($req_verif);
		if ($nbre_verif == 0) {
			$requete_sql = "INSERT INTO absences_rb (eleve_id, groupe_id, edt_id, jour_semaine, creneau_id, debut_ts, fin_ts, date_saisie, login_saisie) VALUES ('appel', '".$classe."', '0', '".$jour_semaine[0]."', '".$rep_creneau["id_definie_periode"]."', '".$ts_debut."', '".$ts_fin."', '".$ts_actu."', '".$_SESSION["login"]."')" OR DIE ('Impossible d\'entrer la saisie');
			$saisie_appel = mysqli_query($GLOBALS["mysqli"], $requete_sql);
			$echo .= '<p class="enregistre_bon">Aucun absent - L\'appel a bien été effectué.</p>';
		}
		else {
			$echo .= '<p class="enregistre_deja">Aucun absent mais cet appel a déjà été enregistré.</p>';
		}
	}
} // if isset de départ de absences_rb et fin du traitement dans la table absences_rb


// ==================== Fin de l'action ajouter ====================

// gestion des erreurs de saisi d'entre du formulaire de demande
$msg_erreur = '';
if ( $etape == '2' AND $menuBar != "ok") {
	if ( $d_heure_absence_eleve>=$a_heure_absence_eleve) { $msg_erreur = 'Attention l\'horaire de debut doit précéder l\'horaire de fin'; $etape = ''; }
	if ( $a_heure_absence_eleve === '' ) { $msg_erreur = 'Attention il faut saisir un horaire de fin'; $etape = ''; }
	if ( $d_heure_absence_eleve === '' ) { $msg_erreur = 'Attention il faut saisir un horaire de debut'; $etape = ''; }
	if ( $d_date_absence_eleve === '' ) { $msg_erreur = 'Attention il faut saisir une date'; $etape = ''; }
}

// si l'utilisateur demande l'enregistrement dans l'emploi du temps
if($edt_enregistrement==='1') {
	//connaitre le jour de la date sélectionné
	$jour_semaine = jour_semaine($d_date_absence_eleve);
	$matiere_du_groupe = matiere_du_groupe($classe);
	$type_de_semaine = semaine_type($d_date_absence_eleve);

	$test_existance = old_mysql_result(mysqli_query($GLOBALS["mysqli"], 'SELECT count(*) FROM edt_classes WHERE prof_edt_classe = "'.$_SESSION["login"].'" AND jour_edt_classe = "'.$jour_semaine['chiffre'].'" AND semaine_edt_classe = "'.$type_de_semaine.'" AND heuredebut_edt_classe <= "'.$d_heure_absence_eleve.'" AND heurefin_edt_classe >= "'.$a_heure_absence_eleve.'"'),0);
	$test_existance_groupe = old_mysql_result(mysqli_query($GLOBALS["mysqli"], 'SELECT count(*) FROM edt_classes WHERE groupe_edt_classe = "'.$classe.'" AND prof_edt_classe = "'.$_SESSION["login"].'" AND jour_edt_classe = "'.$jour_semaine['chiffre'].'" AND semaine_edt_classe = "'.$type_de_semaine.'" AND heuredebut_edt_classe <= "'.$d_heure_absence_eleve.'" AND heurefin_edt_classe >= "'.$a_heure_absence_eleve.'"'),0);
	if ($test_existance === '0') {
		$requete="INSERT INTO ".$prefix_base."edt_classes (groupe_edt_classe,prof_edt_classe,matiere_edt_classe,semaine_edt_classe,jour_edt_classe,datedebut_edt_classe,datefin_edt_classe,heuredebut_edt_classe,heurefin_edt_classe,salle_edt_classe) values ('".$classe."','".$_SESSION["login"]."','".$matiere_du_groupe['nomcourt']."','".semaine_type($d_date_absence_eleve)."','".$jour_semaine['chiffre']."','','','".$d_heure_absence_eleve."','".$a_heure_absence_eleve."','')";
		$resultat = mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	}
	if ( $test_existance === '1' and $test_existance_groupe === '0' ) {
		$requete = 'UPDATE '.$prefix_base.'edt_classes SET groupe_edt_classe = "'.$classe.'" WHERE prof_edt_classe = "'.$_SESSION["login"].'" AND jour_edt_classe = "'.$jour_semaine['chiffre'].'" AND semaine_edt_classe = "'.$type_de_semaine.'" AND heuredebut_edt_classe <= "'.$d_heure_absence_eleve.'" AND heurefin_edt_classe >= "'.$a_heure_absence_eleve.'"';
		$resultat = mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	}
}

	$datej = date('Y-m-d');
	$annee_en_cours_t = annee_en_cours_t($datej);
	$datejour = date('d/m/Y');
	$type_de_semaine = semaine_type($datejour);

$i = 0;


	$requete_modif = "SELECT * FROM absences_eleves WHERE id_absence_eleve ='$id_absence_eleve'";
	$resultat_modif = mysqli_query($GLOBALS["mysqli"], $requete_modif) or die('Erreur SQL !'.$requete_modif.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	while ($data_modif = mysqli_fetch_array($resultat_modif)) {
		$type_absence_eleve[$i] = $data_modif['type_absence_eleve'];
		$eleve_absent[$i] = $data_modif['eleve_absence_eleve'];
		$justify_absence_eleve[$i] = $data_modif['justify_absence_eleve'];
		$info_justify_absence_eleve[$i] = $data_modif['info_justify_absence_eleve'];
		$motif_absence_eleve[$i] = $data_modif['motif_absence_eleve'];
		$d_date_absence_eleve[$i] = date_fr($data_modif['d_date_absence_eleve']);
		$a_date_absence_eleve[$i] = date_fr($data_modif['a_date_absence_eleve']);
		$heuredebut_definie_periode[$i] = $data_modif['heuredebut_definie_periode'];
		$heurefin_definie_periode[$i] = $data_modif['heurefin_definie_periode'];
			$i = $i + 1;
	}

//Configuration du calendrier
include("../../lib/calendrier/calendrier.class.php");
$cal_1 = new Calendrier("absence", "d_date_absence_eleve");

// Style spécifique
$style_specifique = "mod_absences/styles/saisie_absences";
$javascript_specifique = "mod_absences/lib/js_profs_abs";

//**************** EN-TETE *****************
$titre_page = "Saisie des absences";
require_once("../../lib/header.inc.php");
//**************** FIN EN-TETE *****************
?>

<?php
echo "
<p class='lien_retour'>
	<a href=\"../../accueil.php\">
		<img src='../../images/icons/back.png' alt='Retour' class='back_link'/>
		Retour à l'accueil
	</a>";

//++++++++++++++++ Affichage des opérations réussies ou ratées+++++ absences_rb +++++++++++++
if (isset($echo)) {
	echo $echo;
}
//++++++++++++++++ FIN de cet Affichage des opérations réussies ou ratées+++++ absences_rb ++

// Première étape
    if($passage_form != 'manuel') {
    	//horaire dans lequel nous nous trouvons actuellement
    	// en tenant compte du jour différent
		if (getSettingValue("creneau_different") != 'n') {
			if (date("w") == getSettingValue("creneau_different")) {
				$horaire = periode_heure_jourdifferent(periode_actuel_jourdifferent(date('H:i:s')));
			} else {
				$horaire = periode_heure(periode_actuel(date('H:i:s')));
			}
		}else {
			$horaire = periode_heure(periode_actuel(date('H:i:s')));
		}

		// jour de la semaine au format chiffre
		$jour_aujourdhui = jour_semaine($datej);

    	// On vérifie si la menuBarre n'a pas renvoyé une classe (nouvelle version)
    	if ((getSettingValue("utiliserMenuBarre") != "no") AND $_SESSION["statut"] == "professeur" AND $menuBar == 'ok') {
			$d_heure_absence_eleve = $horaire["debut"];
			$a_heure_absence_eleve = $horaire["fin"];
			$classe = isset($_GET["groupe"]) ? $_GET["groupe"] : NULL;
			$etape = '2';
			$passage_auto = 'oui';
		}else{
			// on vérifie si un emploi du temps pour ce prof n'est pas disponible (ancienne version)
			$sql = 'SELECT * FROM edt_classes WHERE prof_edt_classe = "'.$_SESSION["login"].'" AND jour_edt_classe = "'.$jour_aujourdhui['chiffre'].'" AND semaine_edt_classe = "'.$type_de_semaine.'" AND heuredebut_edt_classe <="'.date('H:i:s').'" AND heurefin_edt_classe >="'.date('H:i:s').'"';
			$req = mysqli_query($GLOBALS["mysqli"], $sql) or die('Erreur SQL !<br>'.$sql.'<br>'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
			$nbre = mysqli_num_rows($req);
			if ($nbre >= 1) {
				// on fait une boucle qui va faire un tour pour chaque enregistrement
				while($data = mysqli_fetch_array($req)) {
					$d_heure_absence_eleve = $data['heuredebut_edt_classe'];
					$a_heure_absence_eleve = $data['heurefin_edt_classe'];
					$classe = $data['groupe_edt_classe'];
					$etape = '2';
					$passage_auto = 'oui';
				}
			}

		}
	}

if($etape=="2" or $etape=="3") {
	echo " |
	<a href='prof_ajout_abs.php?passage_form=manuel'>
		Retour étape 1/2
	</a>";
}
if (getSettingValue("liste_absents") == "y") {
	echo "
	 |<a href=\"../lib/tableau.php?type=A&amp;pagedarriver=prof_ajout_abs\"> Visualiser les absences</a>\n";
}
echo "</p>";

if( ( $classe == 'toutes'  or ( $classe == '' and $eleve_initial == '' ) and $etape != '3' ) or $msg_erreur != '' ) {
?>
	<div class="centre_tout_moyen">
	<h2>Saisie des absences : choix du cours</h2>
<?php
	if ( $msg_erreur != '' ) {
		echo '
	<p class="erreur_saisie">
		<img src="../../images/icons/ico_attention.png" alt="ATTENTION" title="ATTENTION" />&nbsp;
		'.$msg_erreur.'
	</p>';
	}
?>
		<!-- <form method="post" action="prof_ajout_abs.php" name="absence"> -->
		<form method="post" action="prof_ajout_abs.php" id="absence" name="form_absence">
		<!--
		<span id='js_afficher' style='display:none;'><a href="javascript: document.forms['form_absence'].submit()">Afficher les élèves</a></span>
		<script type='text/javascript'>document.getElementById('js_afficher').style.display='';</script>
		-->
<?php
	if(empty($d_date_absence_eleve)) {
		$d_date_absence_eleve = date('d/m/Y');
	}
	// On vérifie si le professeur a le droit de modifier la date
	if (getSettingValue("date_phase1") == "y") {
		echo '
	<!--p class="choix_fin"-->
	<p>
		<label for="d_date_absence_eleve">Date</label>
		<input size="10" id="d_date_absence_eleve" name="d_date_absence_eleve" value="'.$d_date_absence_eleve.'" />
		<a href="#calend" onclick="'.$cal_1->get_strPopup('../../lib/calendrier/pop.calendrier_id.php', 350, 170).'">
			<img src="../../lib/calendrier/petit_calendrier.gif" alt="Calendrier" />
		</a>
	</p>
		';
	}else{
		echo '
	<h3 class="gepi">'.date_frl(date_sql($d_date_absence_eleve)).'</h3>
	<p class="erreur_saisie">
		<img src="../../images/icons/ico_attention.png" alt="ATTENTION" title="ATTENTION" />&nbsp;
		V&eacute;rifier les horaires !
	</p>
		';
	}
?>
		<p>
		<label for="d_heure_absence_eleve">De</label>
		<select id="d_heure_absence_eleve" name="d_heure_absence_eleve">

<?php // choix de l'heure de début du créneau
// on vérifie que certains jours n'ont pas les mêmes créneaux
	if (getSettingValue("creneau_different") != 'n') {
		if (date("w") == getSettingValue("creneau_different")) {
			$requete_pe = ('SELECT * FROM edt_creneaux_bis WHERE type_creneaux != "pause" ORDER BY heuredebut_definie_periode ASC');
		} else {
			$requete_pe = ('SELECT * FROM edt_creneaux WHERE type_creneaux != "pause" ORDER BY heuredebut_definie_periode ASC');
		}
	}else {
		$requete_pe = ('SELECT * FROM edt_creneaux WHERE type_creneaux != "pause" ORDER BY heuredebut_definie_periode ASC');
	}
	// et on récupère les créneaux
	$resultat_pe = mysqli_query($GLOBALS["mysqli"], $requete_pe) or die('Erreur SQL !'.$requete_pe.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	// On détermine l'affichage du selected
	if(isset($dp_absence_eleve_erreur) and $dp_absence_eleve_erreur[$i] == "") {
		$selected = ' selected="selected"';
	} else {
		$selected = '';
	}
?>
			<option value=""<?php echo $selected; ?>>pas de s&eacute;lection</option>
<?php
	while($data_pe = mysqli_fetch_array($resultat_pe)) {
		// On vérifie si on a un jour différent ou pas
		if (getSettingValue("creneau_different") != 'n' AND date("w") == getSettingValue("creneau_different")) {
			$test1 = periode_actuel_jourdifferent($heure_choix);
		}else {
			$test1 = periode_actuel($heure_choix);
		}
		if($data_pe['id_definie_periode'] == $test1) {
			$selected = ' selected="selected"';
		}else{
			$selected = '';
		}
		echo '
			<option value="'.$data_pe['heuredebut_definie_periode'].'"'.$selected.'>'.$data_pe['nom_definie_periode'].' '.heure_court($data_pe['heuredebut_definie_periode']).'</option>
			';
	}
?>
		</select>
		<label for="a_heure_absence_eleve">&nbsp;&agrave;&nbsp;</label>
		<select id="a_heure_absence_eleve" name="a_heure_absence_eleve">

<?php // choix de l'heure de fin du créneau en question (en tenant compte de la durée
	// on vérifie que certains jours n'ont pas les mêmes créneaux
	if (getSettingValue("creneau_different") != 'n') {
		if (date("w") == getSettingValue("creneau_different")) {
			$requete_pe = ('SELECT * FROM edt_creneaux_bis WHERE type_creneaux != "pause" ORDER BY heuredebut_definie_periode ASC');
		} else {
			$requete_pe = ('SELECT * FROM edt_creneaux WHERE type_creneaux != "pause" ORDER BY heuredebut_definie_periode ASC');
		}
	}else {
		$requete_pe = ('SELECT * FROM edt_creneaux WHERE type_creneaux != "pause" ORDER BY heuredebut_definie_periode ASC');
	}

	$resultat_pe = mysqli_query($GLOBALS["mysqli"], $requete_pe) or die('Erreur SQL !'.$requete_pe.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	// on détermine l'affichage du selected
	if(isset($dp_absence_eleve_erreur[$i]) and $dp_absence_eleve_erreur[$i] == "") {
		$selected = ' selected="selected"';
	} else {
		$selected = '';
	}
?>
			<option value="">pas de s&eacute;lection</option>
<?php
	while($data_pe = mysqli_fetch_array($resultat_pe)) {

		if($data_pe['id_definie_periode'] == $test1) {
			$selected = ' selected="selected"';
		}else {
			$selected = '';
		}
		echo '
			<option value="'.$data_pe['heurefin_definie_periode'].'"'.$selected.'>'.$data_pe['nom_definie_periode'].' '.heure_court($data_pe['heurefin_definie_periode']).'</option>'."\n";
	}
?>
		</select>
</p>
<!--p class="choix_fin"-->
<p>
	<label for="classe">Groupe</label>
	<select id="classe" name="classe">
<?php
	// On récupère l'ensemble des enseignements du professeur en question
	// Il restera à ajouter les AID après (voir plus loin)
	$groups = get_groups_for_prof($_SESSION["login"]);

	foreach($groups as $group) {
		if(!empty($classe) and $classe == $group["id"]) {
			$selected = ' selected="selected"';
		}else if (isset($_SESSION['id_groupe_session']) and $_SESSION['id_groupe_session'] != "" and $_SESSION['id_groupe_session'] == $group["id"])  {
			$selected = ' selected="selected"';
		} else {
			$selected = '';
		}
		echo '
		<option value="'.$group["id"].'"'.$selected.'>';

		echo $group["description"]."&nbsp;-&nbsp;(";
		$str = null;
		foreach ($group["classes"]["classes"] as $classe) {
			$str .= $classe["classe"] . ", ";
		}
		$str = mb_substr($str, 0, -2);
		echo $str . ")</option>";
	}
	// Et on ajoute les AID
	echo "\n".'<!-- les AID -->'."\n";
	$sql_aid = "SELECT id_aid FROM j_aid_utilisateurs WHERE id_utilisateur = '".$_SESSION["login"]."'";
	$req_aid = mysqli_query($GLOBALS["mysqli"], $sql_aid);
	$nbre_aid = mysqli_num_rows($req_aid);

	for($i=0; $i<$nbre_aid; $i++){
		$rep_aid[$i]["id_aid"] = old_mysql_result($req_aid, $i, "id_aid");
		$recup_nom_aid = mysqli_fetch_array(mysqli_query($GLOBALS["mysqli"], "SELECT nom FROM aid WHERE id = '".$rep_aid[$i]["id_aid"]."'"));
		echo '
		<option value="AID|'.$rep_aid[$i]["id_aid"].'">AID : '.$recup_nom_aid["nom"].'</option>';
	}
	echo "\n";
?>
	</select>
</p>
<?php
	if ( $etape == '2' and $classe == '' and $eleve_initial == '' ) {
		echo '
			<p class="erreur_rouge_jaune">
				<img src="../../images/icons/ico_attention.png" alt="ATTENTION" title="ATTENTION" />&nbsp;
				Erreur de selection, n\'oubliez pas de sélectionner une classe ou un élève
			</p>
	   ';
	}

	//echo '<p class="choix_fin">'."\n";
	echo '<p>'."\n";
	if (getSettingValue("active_module_trombinoscopes")=='y') {
		if ($photo == 'avec_photo') {
			$checkedPhoto = ' checked="checked"';
		}else{
			$checkedPhoto = '';
		}
		echo '
		<input type="checkbox" id="affPhoto" name="photo" value="avec_photo"'.$checkedPhoto.' />
		<label for="affPhoto">Avec photos</label>'."\n";
	}

	// On vérifie si l'utilisateur peut se servir de la mémorisation de ses cours
	if (getSettingValue("memorisation") == "y") {
		echo '
		<input type="checkbox" id="edtEnregistrement" name="edt_enregistrement" value="1" />
		<label for="edtEnregistrement">Mémoriser cette sélection</label>
		';
	}
	echo '</p>'."\n";
?>

	<!--p class="choix_fin"-->
	<p>
		<input value="2" name="etape" type="hidden" />
		<input type="hidden" name="premier_passage" value="ok" />
		<input value="<?php echo $passage_form; ?>" name="passage_form" type="hidden" />
		<input type="hidden" name="uid_post" value="<?php echo my_ereg_replace(' ','%20',$uid); ?>" />
		<input value="Afficher les élèves" name="Valider" type="submit" onclick="this.form.submit();this.disabled=true;this.value='En cours'" />
	</p>
	<p>
<?php // on affiche la date du jour si le professeur est autorisé à la modifier
	if (getSettingValue("date_phase1") == "y") {
		echo '
		Nous sommes le : '.date('d/m/Y').' et ';
	}
?>
		il est actuellement : <?php echo date('G:i')  ?>
	</p>
	<p class="voir_tout"><a href="./bilan_absences_professeur.php">Visualiser toutes ses saisies d'absences</a></p>
	</form>
</div>
<?php
} //if( ( $classe == 'toutes'  or ( $classe == '' and $eleve_initial == '' ) and $etape != '3' ) or $ms...
?>




<?php
// Deuxième étape
if ( $etape === '2' AND $classe != 'toutes' AND ( $classe != '' OR $eleve_initial != '' ) AND $msg_erreur === '') {

    // Ajout d'un test sur la période active
	if($classe!='') {
	    $sql = "SELECT DISTINCT num_periode FROM periodes p, j_groupes_classes jgc WHERE jgc.id_classe=p.id_classe AND jgc.id_groupe='$classe' AND p.verouiller='N' ORDER BY num_periode";
	}
	else {
	    $sql = "SELECT DISTINCT num_periode FROM periodes WHERE verouiller = 'N' ORDER BY num_periode";
	}
	//echo "$sql<br />";
    $periode_active = mysqli_query($GLOBALS["mysqli"], $sql) OR DIE('Impossible de récupérer le numéro de la période active' . $sql . '<br />--> ' . ((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    $periode = mysqli_fetch_array($periode_active);
    //echo '<pre>'; print_r($periode); echo'</pre>'; exit();
    $nbre_per = count($periode);
    $_periode = isset($periode[0]) ? $periode[0] : '1';

    // ======================== Correctif : On récupère la période actuelle si elle a été paramétrée dans l'emploi du temps

	$sql="SELECT DISTINCT id_classe FROM j_groupes_classes WHERE id_groupe='$classe';";
	$res_classes_du_groupe=mysqli_query($GLOBALS["mysqli"], $sql);
	$tab_classes_grp=array();
	while($lig_tmp=mysqli_fetch_object($res_classes_du_groupe)) {
		$tab_classes_grp[]=$lig_tmp->id_classe;
	}

    //$req_periode_courante = mysql_query("SELECT numero_periode FROM edt_calendrier WHERE
    //$sql="SELECT numero_periode FROM edt_calendrier WHERE
    $sql="SELECT numero_periode, classe_concerne_calendrier FROM edt_calendrier WHERE
                                        debut_calendrier_ts < ".date("U")." AND
                                        fin_calendrier_ts > ".date("U");
    $req_periode_courante = mysqli_query($GLOBALS["mysqli"], $sql);
    if ($rep_periode_courante = mysqli_fetch_array($req_periode_courante)) {
        if ($rep_periode_courante["numero_periode"] != 0) {

			$temoin_classe_concernee="n";
			$tab_classe_concerne_calendrier=explode(";",$rep_periode_courante["classe_concerne_calendrier"]);
			for($loop=0;$loop<count($tab_classes_grp);$loop++) {
				if(in_array($tab_classes_grp[$loop],$tab_classe_concerne_calendrier)) {
					$temoin_classe_concernee="y";
					//$periode_edt_trouvee="y";
					break;
				}
			}
	
			if($temoin_classe_concernee=="y") {
	            $_periode = $rep_periode_courante["numero_periode"];
				//$periode_edt_trouvee="y";
				//break;
			}
        }
    }
    //echo "\$_periode=".$_periode."<br/>";
    // ======================== fin de correctif

	// on vérifie que l'enseignement envoyé n'est pas une AID
	$test = explode("|", $classe);
	if ($test[0] == "AID") {
		// On récupère les infos sur l'AID
		$aid_nom = mysqli_fetch_array(mysqli_query($GLOBALS["mysqli"], "SELECT nom FROM aid WHERE id = '".$test[1]."'"));
		$current_groupe["description"] = $aid_nom["nom"];
		$current_groupe["classlist_string"] = "AID";
		//$nbre_eleves = mysql_num_rows(mysql_query("SELECT DISTINCT login FROM j_aid_eleves WHERE id_aid = '".$test[1]."'"));
		$req_logins_eleves = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT login FROM j_aid_eleves WHERE id_aid = '".$test[1]."'");
	}else{
		$current_groupe = get_group($classe);
		//$nbre_eleves = mysql_num_rows(mysql_query("SELECT DISTINCT login FROM j_eleves_groupes WHERE id_groupe = '".$classe."' AND periode = '" . $_periode . "'"));
		$req_logins_eleves = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT login FROM j_eleves_groupes WHERE id_groupe = '".$classe."' AND periode = '" . $_periode . "'");
	}

    // ================= Calculer le nbre d'élèves 
    $nbre_eleves = 0;
    while ($rep_logins_eleves = mysqli_fetch_array($req_logins_eleves)) {
        $req_periode_courante = mysqli_query($GLOBALS["mysqli"], "SELECT numero_periode FROM edt_calendrier WHERE
                                            debut_calendrier_ts < ".date("U")." AND
                                            fin_calendrier_ts > ".date("U")."
                                ");
        if ($rep_periode_courante = mysqli_fetch_array($req_periode_courante)) {
            if ($rep_periode_courante["numero_periode"] != 0) {
                $test = explode("|", $classe);
                if ( $test[0] != "AID") {
                    $req_eleve_dispo = mysqli_query($GLOBALS["mysqli"], "SELECT login FROM j_eleves_groupes WHERE
                                                    login = '".$rep_logins_eleves["login"]."' AND
                                                    id_groupe = '".$classe."' AND
                                                    periode = '".$rep_periode_courante["numero_periode"]."' 
                                                    
                                        ");
                    if (mysqli_num_rows($req_eleve_dispo) != 0) {
                        $nbre_eleves++;
                    }
                }
                else {
                    $req_eleve_dispo = mysqli_query($GLOBALS["mysqli"], "SELECT login FROM j_aid_eleves WHERE 
	                                                    id_aid = '".$test[1]."' AND
	                                                    indice_aid IN (SELECT indice_aid FROM aid_config WHERE
			                                            display_begin <= '".$rep_periode_courante["numero_periode"]."' AND
			                                            display_end >= '".$rep_periode_courante["numero_periode"]."' ) 
                                                    ");
                    if (mysqli_num_rows($req_eleve_dispo) != 0) {
                        $nbre_eleves++;
                    }
                }
            } else {
                $nbre_eleves++;
            } 
        } else {
            $nbre_eleves++;
        }
        
    }

//echo "\$nbre_eleves=$nbre_eleves<br />";

?>
	<div class="centre_tout_moyen">
		<form method="post" action="prof_ajout_abs.php" id="liste_absence_eleve">
			<!--p class="expli_page choix_fin"-->
			<p class="expli_page">
				Saisie des absences<br/>
				du <strong><?php echo date_frl(date_sql($d_date_absence_eleve)); ?></strong>
				de <strong><?php echo heure_court($d_heure_absence_eleve); ?></strong>
				à <strong><?php echo heure_court($a_heure_absence_eleve); ?></strong>
				<br/>
<?php
	echo "
				<strong>".$current_groupe["description"]."</strong>
				 (".$current_groupe["classlist_string"] .")
			</p>";

	if($passage_auto === 'oui' and $passage_form === '') {
		echo '
			<!--p class="choix_fin"-->
			<p>
				<a href="prof_ajout_abs.php?passage_form=manuel">Ceci n\'est pas la bonne liste d\'appel ?</a>
			</p>
		';
	}
	?>
			<!--p class="choix_fin"-->
			<p>
				<input value="Enregistrer" name="Valider" type="submit"  onclick="this.form.submit();this.disabled=true;this.value='En cours'" />
			</p>
			<?php
				if ($_SESSION['statut'] == 'professeur' && getSettingValue("active_cahiers_texte")=='y') {
			?>
			<!--p class="choix_fin"-->
			<p>
				<input type="hidden" name="passer_cahier_texte" id="passer_cahier_texte" value="false" />
				<input value="Enregistrer et passer au cahier de texte" name="Valider" type="submit"  onclick="document.getElementById('passer_cahier_texte').value = true; this.form.submit(); this.disabled=true; this.value='En cours'" />
			</p>
			<?php
				}
			?>

<!-- Afichage du tableau de la liste des élèves -->
<!-- Legende du tableau-->
	<?php
		//=====================================================================
		// on compte les créneaux pour savoir combien de cellules il faut créer
		if (getSettingValue("creneau_different") != 'n') {
			if (date("w") == getSettingValue("creneau_different")) {
				$sql = "SELECT nom_definie_periode FROM edt_creneaux_bis WHERE type_creneaux != 'pause' ORDER BY heuredebut_definie_periode";
			}else{
				$sql = "SELECT nom_definie_periode FROM edt_creneaux WHERE type_creneaux != 'pause' ORDER BY heuredebut_definie_periode";
			}
		}else{
			$sql = "SELECT nom_definie_periode FROM edt_creneaux WHERE type_creneaux != 'pause' ORDER BY heuredebut_definie_periode";
		}
		$req_noms = mysqli_query($GLOBALS["mysqli"], $sql) OR DIE ('Pas de créneaux disponibles.');
		$nbre_noms = mysqli_num_rows($req_noms) OR die ('Impossible de compter les créneaux.');
		$req_noms_1=$req_noms;
		$nbre_noms_1=$nbre_noms;
		//=====================================================================
		// On insère les noms des différents créneaux
		if (getSettingValue("creneau_different") != 'n') {
			if (date("w") == getSettingValue("creneau_different")) {
				$sql = "SELECT nom_definie_periode FROM edt_creneaux_bis WHERE type_creneaux != 'pause' ORDER BY heuredebut_definie_periode";
			}else{
				$sql = "SELECT nom_definie_periode FROM edt_creneaux WHERE type_creneaux != 'pause' ORDER BY heuredebut_definie_periode";
			}
		}else{
			$sql = "SELECT nom_definie_periode FROM edt_creneaux WHERE type_creneaux != 'pause' ORDER BY heuredebut_definie_periode";
		}
		$req_noms = mysqli_query($GLOBALS["mysqli"], $sql) OR DIE ('Pas de créneaux disponibles.');
		$nbre_noms = mysqli_num_rows($req_noms) OR DIE ('Impossible de compter les créneaux.');
		$req_noms_2=$req_noms;
		$nbre_noms_2=$nbre_noms;
		//=====================================================================
		if ($test[0] == "AID") {
				// On a besoin du login, nom, prenom et sexe de l'élève
			$requete_liste_eleve = "SELECT eleves.* FROM eleves, aid, j_aid_eleves WHERE eleves.login = j_aid_eleves.login AND j_aid_eleves.id_aid = aid.id AND id = '".$test[1]."' AND (eleves.date_sortie IS NULL OR eleves.date_sortie='' OR eleves.date_sortie='0000-00-00 00:00:00' OR eleves.date_sortie>'".strftime("%Y-%m-%d %H:%M:%S")."') GROUP BY eleves.login ORDER BY nom, prenom";
			$execution_liste_eleve = mysqli_query($GLOBALS["mysqli"], $requete_liste_eleve) or die('Erreur SQL AID !'.$requete_liste_eleve.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		}
		else {

			$requete_liste_eleve = "SELECT * FROM eleves, groupes, j_eleves_groupes 
		                          WHERE eleves.login=j_eleves_groupes.login
		                          AND j_eleves_groupes.id_groupe=groupes.id
		                          AND j_eleves_groupes.periode = " . $_periode . "
		                          AND id = '".$classe."'
		                          AND (eleves.date_sortie IS NULL OR eleves.date_sortie='' OR eleves.date_sortie='0000-00-00 00:00:00' OR eleves.date_sortie>'".strftime("%Y-%m-%d %H:%M:%S")."')
		                        GROUP BY eleves.login
		                        ORDER BY nom, prenom";

		    $execution_liste_eleve = mysqli_query($GLOBALS["mysqli"], $requete_liste_eleve) or die('Erreur SQL !'.$requete_liste_eleve.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		}
		//=====================================================================
		$nbre_eleves=mysqli_num_rows($execution_liste_eleve);

		echo '<p>'.$nbre_eleves.' élèves.</p>';
	?>
	<table class="tb_code_couleur" summary="Code des couleurs">
		<tr>
			<td class="td_Retard">&nbsp;R&nbsp;</td><td>&nbsp;Retard</td>
			<td class="td_Absence">&nbsp;A&nbsp;</td><td>&nbsp;Absence</td>
		</tr>
	</table>
<!-- Fin de la legende -->
<!-- <table style="text-align: left; width: 600px;" border="0" cellpadding="0" cellspacing="1"> -->
	<table class="tb_absences" summary="Liste des élèves pour l'appel. Colonne 1 : élèves, colonne 2 : absence, colonne3 : retard, colonnes suivantes : suivi de la journée par créneaux, dernière colonne : photos si actif">
		<caption class="invisible no_print">Absences</caption>
		<tbody>
			<tr class="titre_tableau_gestion" style="white-space: nowrap;">
				<th class="td_abs_eleves" style="width: 10%;">&nbsp;Hier&nbsp;</th>
				<th class="td_abs_eleves" abbr="élèves">Liste des &eacute;l&egrave;ves</th>
				<th class="td_abs_absence">Absence</th>
	<?php // on vérifie que le professeur est bien autorisé à saisir les retards
	if (getSettingValue("renseigner_retard") == "y") {
		echo'
				<th class="td_abs_retard">Retard</th>
		';
	}
	
	//ajout Eric
	if (getSettingValue("renseigner_Repas") == "y") {
		echo'
				<th class="td_abs_retard">Repas</th>
		';
	}
	//Fin Ajout Eric

	/*
	// on compte les créneaux pour savoir combien de cellules il faut créer
	if (getSettingValue("creneau_different") != 'n') {
		if (date("w") == getSettingValue("creneau_different")) {
			$sql = "SELECT nom_definie_periode FROM edt_creneaux_bis WHERE type_creneaux != 'pause' ORDER BY heuredebut_definie_periode";
		}else{
			$sql = "SELECT nom_definie_periode FROM edt_creneaux WHERE type_creneaux != 'pause' ORDER BY heuredebut_definie_periode";
		}
	}else{
		$sql = "SELECT nom_definie_periode FROM edt_creneaux WHERE type_creneaux != 'pause' ORDER BY heuredebut_definie_periode";
	}
	$req_noms = mysql_query($sql) OR DIE ('Pas de créneaux disponibles.');
	$nbre_noms = mysql_num_rows($req_noms) OR die ('Impossible de compter les créneaux.');
	*/
	$nbre_noms=$nbre_noms_1;

	echo '
				<th colspan="'.$nbre_noms.'" class="th_abs_suivi" abbr="Créneaux">Suivi sur la journ&eacute;e</th>'."\n";
?>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td></td>
	<?php // on vérifie que le professeur est bien autorisé à saisir les retards
	if (getSettingValue("renseigner_retard") == "y") {
		echo '
				<td></td>';
	}
	
	//Ajout Eric
	if (getSettingValue("renseigner_Repas") == "y") {
		echo '
				<td></td>';
	}
    // Fin Ajout Eric

	/*
	// On insère les noms des différents créneaux
	if (getSettingValue("creneau_different") != 'n') {
		if (date("w") == getSettingValue("creneau_different")) {
			$sql = "SELECT nom_definie_periode FROM edt_creneaux_bis WHERE type_creneaux != 'pause' ORDER BY heuredebut_definie_periode";
		}else{
			$sql = "SELECT nom_definie_periode FROM edt_creneaux WHERE type_creneaux != 'pause' ORDER BY heuredebut_definie_periode";
		}
	}else{
		$sql = "SELECT nom_definie_periode FROM edt_creneaux WHERE type_creneaux != 'pause' ORDER BY heuredebut_definie_periode";
	}
	$req_noms = mysql_query($sql) OR DIE ('Pas de créneaux disponibles.');
	$nbre_noms = mysql_num_rows($req_noms) OR DIE ('Impossible de compter les créneaux.');
	*/
	$nbre_noms=$nbre_noms_2;
	$req_noms=$req_noms_2;

	for($i=0; $i<$nbre_noms; $i++) {
		$rep_sql[$i]["nom_creneau"] = old_mysql_result($req_noms, $i, "nom_definie_periode");
	}
	for($a=0; $a<$nbre_noms; $a++){
		echo '
				<td class="td_nom_creneau">'.$rep_sql[$a]["nom_creneau"].'</td>';
	}
?>
			</tr>
	<?php

	/*
	if ($test[0] == "AID") {
			// On a besoin du login, nom, prenom et sexe de l'élève
		$requete_liste_eleve = "SELECT eleves.* FROM eleves, aid, j_aid_eleves WHERE eleves.login = j_aid_eleves.login AND j_aid_eleves.id_aid = aid.id AND id = '".$test[1]."' AND (eleves.date_sortie IS NULL OR eleves.date_sortie='' OR eleves.date_sortie='0000-00-00 00:00:00' OR eleves.date_sortie>'".strftime("%Y-%m-%d %H:%M:%S")."') GROUP BY eleves.login ORDER BY nom, prenom";
		$execution_liste_eleve = mysql_query($requete_liste_eleve) or die('Erreur SQL AID !'.$requete_liste_eleve.'<br />'.mysql_error());
	}
	else {

		$requete_liste_eleve = "SELECT * FROM eleves, groupes, j_eleves_groupes 
                              WHERE eleves.login=j_eleves_groupes.login
                              AND j_eleves_groupes.id_groupe=groupes.id
                              AND j_eleves_groupes.periode = " . $_periode . "
                              AND id = '".$classe."'
                              AND (eleves.date_sortie IS NULL OR eleves.date_sortie='' OR eleves.date_sortie='0000-00-00 00:00:00' OR eleves.date_sortie>'".strftime("%Y-%m-%d %H:%M:%S")."')
                            GROUP BY eleves.login
                            ORDER BY nom, prenom";

        $execution_liste_eleve = mysql_query($requete_liste_eleve) or die('Erreur SQL !'.$requete_liste_eleve.'<br />'.mysql_error());
    }
	*/

	$cpt_eleve = '0';
	$ic = '1';
	$ligne= '0';
    $test = explode("|", $classe);
    $req_periode_courante = mysqli_query($GLOBALS["mysqli"], "SELECT numero_periode FROM edt_calendrier WHERE
                                        debut_calendrier_ts < ".date("U")." AND
                                        fin_calendrier_ts > ".date("U")."
                            ");
    if ($rep_periode_courante = mysqli_fetch_array($req_periode_courante)) {
        $periode_courante = $rep_periode_courante["numero_periode"];
    }
    else {
        $periode_courante = 0;
    }

	while ($data_liste_eleve = mysqli_fetch_array($execution_liste_eleve)) {

        // =============== Filtrage supplémentaire pour masquer les élèves "désaffectés" de la période courante
        // =============== si cette période a été définie dans les périodes des edt.
        $eleve_dispo = true;
        if ($periode_courante != 0) {
            if ( $test[0] != "AID") {
                // =============== Si l'élève actuel a été supprimé de la période courante
                // =============== on bloque son affichage
                $req_eleve_dispo = mysqli_query($GLOBALS["mysqli"], "SELECT login FROM j_eleves_groupes WHERE
                                                login = '".$data_liste_eleve["login"]."' AND
                                                id_groupe = '".$classe."' AND
                                                periode = '".$periode_courante."' 
                                                
                                    ");
                if (mysqli_num_rows($req_eleve_dispo) != 0) {
                    $eleve_dispo = true;
                } else {
                    $eleve_dispo = false;
                }
            }
            else {
                // =============== Si l'AID n'est pas définie sur la période courante, on bloque l'affichage
                // =============== des élèves
                $req_eleve_dispo = mysqli_query($GLOBALS["mysqli"], "SELECT login FROM j_aid_eleves WHERE 
	                                                id_aid = '".$test[1]."' AND
	                                                indice_aid IN (SELECT indice_aid FROM aid_config WHERE
			                                        display_begin <= '".$periode_courante."' AND
			                                        display_end >= '".$periode_courante."' ) 
                                                ");
                if (mysqli_num_rows($req_eleve_dispo) != 0) {
                    $eleve_dispo = true;
                } else {
                    $eleve_dispo = false;
                }
            }
        }

		//if((isset($data_liste_eleve["date_sortie"]))&&($data_liste_eleve["date_sortie"]!='')&&($data_liste_eleve["date_sortie"]!='0000-00-00 00:00:00')&&($data_liste_eleve["date_sortie"]<=strftime("%Y-%m-%d %H:%M:%S"))) {$eleve_dispo = false;}

        // ==================== fin du filtrage
        if ($eleve_dispo) {
		$ligne= $ligne+1;
		if ($ic === '1') {
			$ic='2';
			$couleur_cellule="td_tableau_absence_1";
			$background_couleur="#E8F1F4";
			$couleur_classe="abs_ligne_impaire";
		} else {
			$couleur_cellule="td_tableau_absence_2";
			$background_couleur="#C6DCE3"; $ic='1';
			$couleur_classe="abs_ligne_paire";
		}
		echo "<tr id='ligne_".$ligne."' class='$couleur_classe' onmouseover='Element.addClassName(\"ligne_$ligne\",\"abs_ligne_survol\")' onmouseout='Element.removeClassName(\"ligne_$ligne\",\"abs_ligne_survol\")'>\n";

		// On cherche s'il y a eu des absences la veille
		$hier_00h00 = date("U") - ((date("H") * 3600) + 86400);
		$hier_23h59 = date("U") - ((date("H") * 3600) + 3600);

		$sql_hier = "SELECT * FROM absences_rb WHERE eleve_id = '" . $data_liste_eleve["login"] . "'
												AND debut_ts > '" . $hier_00h00 . "'
												AND fin_ts < '" . $hier_23h59 . "'
												AND retard_absence = 'A'
												ORDER BY debut_ts";
		$query_hier = mysqli_query($GLOBALS["mysqli"], $sql_hier) OR DIE('ERREUR dans la requête SQL ' . $sql_hier . '<br />&nbsp;&nbsp;--> ' . ((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		$compter_hier = mysqli_num_rows($query_hier);

		$color_hier = ($compter_hier >= 2) ? ' style="background-color: blue; text-align: center; color: white; font-weight: bold;"' : '';
		$aff_compter_hier = ($compter_hier >= 1) ? $compter_hier.' enr.' : '';

?>
				<td<?php echo $color_hier; ?>><?php echo $aff_compter_hier; ?></td>
				<td class='td_abs_eleves'>
					<input type="hidden" name="eleve_absent[<?php echo $cpt_eleve; ?>]" value="<?php echo $data_liste_eleve['login']; ?>" />

<?php

		// On détermine la civilité de l'élève
		if($data_liste_eleve['sexe']=="M") {
			$civile = "(M.)";
		} elseif ($data_liste_eleve['sexe']=="F") {
			$civile = "(Mlle)";
		}
		$sexe = $data_liste_eleve['sexe'];
			// On vérifie si le prof a le droit de voir la fiche de l'élève
			if ($_SESSION["statut"] == "professeur" AND getSettingValue("voir_fiche_eleve") == "n" OR getSettingValue("voir_fiche_eleve") == '') {

				echo '<span class="td_abs_eleves">'.strtoupper($data_liste_eleve['nom']).' '.ucfirst($data_liste_eleve['prenom']).'&nbsp;'.$civile.'</span>';

			}elseif($_SESSION["statut"] != "professeur" OR getSettingValue("voir_fiche_eleve") == "y"){
				echo '
				<a href="javascript:centrerpopup(\'../lib/fiche_eleve.php?select_fiche_eleve='.$data_liste_eleve['login'].'\',550,500,\'scrollbars=yes,statusbar=no,resizable=yes\');">
				'.strtoupper($data_liste_eleve['nom']).' '.ucfirst($data_liste_eleve['prenom'])
				.'</a> '.$civile.'
				';
			}

?>
				</td>
				<td class="td_abs_absence">

<?php
		$pass='0';
		$requete = "SELECT * FROM absences_eleves
			WHERE eleve_absence_eleve='".$data_liste_eleve['login']."'
			AND type_absence_eleve = 'A'
			AND ( '".date_sql($d_date_absence_eleve)."' BETWEEN d_date_absence_eleve AND a_date_absence_eleve
				OR d_date_absence_eleve BETWEEN '".date_sql($d_date_absence_eleve)."' AND '".date_sql($d_date_absence_eleve)."'
				OR a_date_absence_eleve BETWEEN '".date_sql($d_date_absence_eleve)."' AND '".date_sql($d_date_absence_eleve)."')
			AND ( '".$d_heure_absence_eleve."' BETWEEN d_heure_absence_eleve AND a_heure_absence_eleve
				AND '".$a_heure_absence_eleve."' BETWEEN d_heure_absence_eleve AND a_heure_absence_eleve
				OR (d_heure_absence_eleve BETWEEN '".$d_heure_absence_eleve."' AND '".$a_heure_absence_eleve."'
				AND a_heure_absence_eleve BETWEEN '".$d_heure_absence_eleve."' AND '".$a_heure_absence_eleve."')
				)";
		$query = mysqli_query($GLOBALS["mysqli"], $requete);
		$cpt_absences = mysqli_num_rows($query);
		if($cpt_absences != '0') {
			$pass = '1';
		}
		if ($pass === '0') {
?>
		<label for="activAb<?php echo $cpt_eleve; ?>">
		<input id="activAb<?php echo $cpt_eleve; ?>" name="active_absence_eleve[<?php echo $cpt_eleve; ?>]" value="1" type="checkbox" />
		</label>
<?php
		} else {
			if($sexe=="M") {
				 echo 'Absent';
			}
			if($sexe=="F") {
				 echo 'Absente';
			}
?>
		<input name="active_absence_eleve[<?php echo $cpt_eleve; ?>]" value="0" type="hidden" />
		<?php
		}
        $pass='0';

		echo '</td>';
//======================== début de la saisie des retards ==================================================
		// On vérifie que le professeur est autorisé à renseigner le retard
		if (getSettingValue("renseigner_retard") == "y") {
			echo '<td class="td_abs_retard">';

			$pass='0';
			$requete_retards = "SELECT count(*) FROM absences_eleves
					WHERE eleve_absence_eleve='".$data_liste_eleve['login']."'
					AND type_absence_eleve = 'R'
					AND
					( '".date_sql($d_date_absence_eleve)."' BETWEEN d_date_absence_eleve AND a_date_absence_eleve
						OR d_date_absence_eleve BETWEEN '".date_sql($d_date_absence_eleve)."' AND '".date_sql($d_date_absence_eleve)."'
						OR a_date_absence_eleve BETWEEN '".date_sql($d_date_absence_eleve)."' AND '".date_sql($d_date_absence_eleve)."'
					)AND
					( '".$d_heure_absence_eleve."' BETWEEN d_heure_absence_eleve AND a_heure_absence_eleve
						OR '".$a_heure_absence_eleve."' BETWEEN d_heure_absence_eleve AND a_heure_absence_eleve
						OR d_heure_absence_eleve BETWEEN '".$d_heure_absence_eleve."' AND '".$a_heure_absence_eleve."'
						OR a_heure_absence_eleve BETWEEN '".$d_heure_absence_eleve."' AND '".$a_heure_absence_eleve."'
					)";
			$cpt_retards = old_mysql_result(mysqli_query($GLOBALS["mysqli"], $requete_retards),0);
			if($cpt_retards != '0') {
				$pass = '1';
			}
			if ($pass === '0') {
?>
				<label for="active_retard_eleve<?php echo $cpt_eleve; ?>" class="invisible no_print">Retard</label>
				<input type="checkbox" id="active_retard_eleve<?php echo $cpt_eleve; ?>" name="active_retard_eleve[<?php echo $cpt_eleve; ?>]" value="1" onclick="getHeure(active_retard_eleve<?php echo $cpt_eleve; ?>,heure_retard_eleve<?php echo $cpt_eleve; ?>,'liste_absence_eleve')" />
				<label for="heure_retard_eleve<?php echo $cpt_eleve; ?>" class="invisible no_print">heure retard</label>
				<input type="text" id="heure_retard_eleve<?php echo $cpt_eleve; ?>" name="heure_retard_eleve[<?php echo $cpt_eleve; ?>]" size="3" maxlength="8" value="<?php echo heure_court($heuredebut_definie_periode); ?>" />
<?php
			} else {
?>
				En retard<input id="active_retard_eleve<?php echo $cpt_eleve; ?>" name="active_retard_eleve[<?php echo $cpt_eleve; ?>]" value="0" type="hidden" />
<?php
			}
		echo"\n</td>\n";
		} // if (getSettingValue("renseigner_retard") == "y")
		//echo"\n</td>\n";
//======================== fin de la saisie des retards ==================================================

//======================== début de la saisie des Repas ==================================================
		// On vérifie que le professeur est autorisé à renseigner Les Repas
		if (getSettingValue("renseigner_Repas") == "y") {
			echo '<td class="td_abs_retard">';

			$pass='0';
			$requete_retards = "SELECT count(*) FROM absences_eleves
					WHERE eleve_absence_eleve='".$data_liste_eleve['login']."'
					AND type_absence_eleve = 'R'
					AND
					( '".date_sql($d_date_absence_eleve)."' BETWEEN d_date_absence_eleve AND a_date_absence_eleve
						OR d_date_absence_eleve BETWEEN '".date_sql($d_date_absence_eleve)."' AND '".date_sql($d_date_absence_eleve)."'
						OR a_date_absence_eleve BETWEEN '".date_sql($d_date_absence_eleve)."' AND '".date_sql($d_date_absence_eleve)."'
					)AND
					( '".$d_heure_absence_eleve."' BETWEEN d_heure_absence_eleve AND a_heure_absence_eleve
						OR '".$a_heure_absence_eleve."' BETWEEN d_heure_absence_eleve AND a_heure_absence_eleve
						OR d_heure_absence_eleve BETWEEN '".$d_heure_absence_eleve."' AND '".$a_heure_absence_eleve."'
						OR a_heure_absence_eleve BETWEEN '".$d_heure_absence_eleve."' AND '".$a_heure_absence_eleve."'
					)";
			$cpt_retards = old_mysql_result(mysqli_query($GLOBALS["mysqli"], $requete_retards),0);
			if($cpt_retards != '0') {
				$pass = '1';
			}
			if ($pass === '0') {
?>
				<label for="active_repas_eleve<?php echo $cpt_eleve; ?>" class="invisible no_print">Repas</label>
				<input type="checkbox" id="active_repas_eleve<?php echo $cpt_eleve; ?>" name="active_repas_eleve[<?php echo $cpt_eleve; ?>]" value="1" " />
<?php
			} 
?>
<?php
			
		echo"\n</td>\n";
		} // if (getSettingValue("renseigner_retard") == "y")
		//echo"\n</td>\n";
//======================== fin de la saisie des retards ==================================================



// ===================== On insère le suivi sur les différents créneaux ==================================
// On construit le tableau html des créneaux avec les couleurs
		if (date("w") == getSettingValue("creneau_different")) {
			$req_creneaux = mysqli_query($GLOBALS["mysqli"], "SELECT id_definie_periode FROM edt_creneaux_bis WHERE type_creneaux != 'pause' ORDER BY heuredebut_definie_periode");
		} else {
			$req_creneaux = mysqli_query($GLOBALS["mysqli"], "SELECT id_definie_periode FROM edt_creneaux WHERE type_creneaux != 'pause' ORDER BY heuredebut_definie_periode");
		}
		$nbre_creneaux = mysqli_num_rows($req_creneaux);

		for($i=0; $i<$nbre_creneaux; $i++) {
			$rep_creneaux[$i]["id"] = old_mysql_result($req_creneaux, $i, "id_definie_periode");
		}
		// On affiche la liste des créneaux en testant chacun d'entre eux (absence ou retard)
		for($a=0; $a<$nbre_creneaux; $a++) {
			echo '
				<td'.suivi_absence($rep_creneaux[$a]["id"], $data_liste_eleve['login']).'</td>';
		}
// ===================== fin du suivi sur les différents créneaux ========================================
           // Avec ou sans photo
		if ((getSettingValue("active_module_trombinoscopes")=='y') and ($photo=="avec_photo")) {
      $nom_photo = nom_photo($data_liste_eleve['elenoet'],"eleves",2);
			//if (($nom_photo == "") or (!(file_exists($photos)))) {
			if (($nom_photo == NULL) or (!(file_exists($nom_photo)))) {
				$nom_photo = "../../mod_trombinoscopes/images/trombivide.jpg";
			}
			$valeur = redimensionne_image_petit($nom_photo);
?>
				<td>
<?php
//echo $nom_photo;
?>
					<img src="<?php echo $nom_photo; ?>" style="width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px; border: 0px" alt="" title="" />
				</td>
<?php
		}
?>
			</tr>
<?php
		$type_saisie="A";
		$cpt_eleve = $cpt_eleve + 1;
    } //if ($eleve_dispo)
	} //while ($data_liste_eleve = mysql_fetch_array($execution_liste_eleve)
// Régis j'en suis là
?>
		</tbody>
	</table>
	<p>
		<input value="0" name="etape" type="hidden" />
		<input type="hidden" name="nb_i" value="<?php echo $cpt_eleve; ?>" />
		<input type="hidden" name="type_absence_eleve" value="<?php echo $type_saisie; ?>" />
		<input type="hidden" name="saisie_absence_eleve" value="<?php echo $_SESSION['login']; ?>" />
		<input type="hidden" name="classe" value="<?php echo $classe; ?>" />
		<input type="hidden" name="action_sql" value="ajouter" />
<?php
	if (getSettingValue("active_module_trombinoscopes")=='y'){
		echo '
		<input type="hidden" name="photo" value="'.$photo.'" />'."\n";
	}
?>
		<input type="hidden" name="d_date_absence_eleve" value="<?php echo $d_date_absence_eleve; ?>" />
		<input type="hidden" name="d_heure_absence_eleve" value="<?php echo $d_heure_absence_eleve; ?>" />
		<input type="hidden" name="etape" value="2" />
		<input type="hidden" name="a_heure_absence_eleve" value="<?php echo $a_heure_absence_eleve; ?>" />
		<input type="hidden" name="uid_post" value="<?php echo my_ereg_replace(' ','%20',$uid); ?>" />
	</p>
		<div style="text-align: center; margin: 20px;">
			<input value="Enregistrer" name="Valider" type="submit"  onclick="this.form.submit();this.disabled=true;this.value='En cours'" />
		</div>
		<?php
			if ($_SESSION['statut'] == 'professeur' && getSettingValue("active_cahiers_texte")=='y') {
		?>
		<div style="text-align: center; margin: 20px;">
				<input value="Enregistrer et passer au cahier de texte" name="Valider" type="submit"  onclick="document.getElementById('passer_cahier_texte').value = true; this.form.submit(); this.disabled=true; this.value='En cours'" />
		</div>
		<?php
			}
		?>
	</form>
	<p class="info_importante">Quand vous saisissez vos absences (avec ou sans absent),
	Gepi enregistre la date et l'heure ainsi que votre identifiant.</p>

</div>
<?php
} // fin if ( $etape === '2' AND $classe != 'toutes' AND ( $classe != '' OR $el ...

require("../../lib/footer.inc.php");
?>
