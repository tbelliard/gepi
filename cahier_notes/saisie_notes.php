<?php
/**
 * saisie des Notes
 *
 * Copyright 2001, 2021 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun; Stephane Boireau, Romain Neil
 *
 * @license GNU/GPL
 * @package Carnet_de_notes
 * @subpackage saisie
 * @see add_token_field()
 * @see check_token()
 * @see checkAccess()
 * @see corriger_caracteres()
 * @see creer_div_infobulle()
 * @see formate_date()
 * @see getSettingValue()
 * @see get_group()
 * @see get_groups_for_prof()
 * @see getPref()
 * @see html_entity_decode()
 * @see javascript_tab_stat()
 * @see mise_a_jour_moyennes_conteneurs()
 * @see nom_photo()
 * @see recherche_enfant()
 * @see Session::security_check()
 * @see sous_conteneurs()
 * @see traitement_magic_quotes()
 * @see Verif_prof_cahier_notes()
 */

/*
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

/**
 * Fichiers d'initialisation
 */
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

//On vérifie si le module est activé
if (getSettingValue("active_carnets_notes") != 'y') {
	die("Le module n'est pas activé.");
}

$msg = "";

// 20120509
$delta_modif_note = 0.5;

unset($id_devoir);
$id_devoir = isset($_POST["id_devoir"]) ? $_POST["id_devoir"] : (isset($_GET["id_devoir"]) ? $_GET["id_devoir"] : NULL);
//if($id_devoir=='') {$id_devoir=NULL;}

unset($affiche_message);
$affiche_message = isset($_POST["affiche_message"]) ? $_POST["affiche_message"] : (isset($_GET["affiche_message"]) ? $_GET["affiche_message"] : NULL);

$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : (isset($_POST['order_by']) ? $_POST["order_by"] : getPref($_SESSION['login'], 'cn_order_by', 'classe'));


if ($id_devoir) {
	$sql = "SELECT * FROM cn_devoirs WHERE id ='$id_devoir';";
	//echo "$sql<br />";
	$appel_devoir = mysqli_query($GLOBALS["mysqli"], $sql);
	$obj_devoir = $appel_devoir->fetch_object();
	$nom_devoir = $obj_devoir->nom_court;
	$ramener_sur_referentiel_dev_choisi = $obj_devoir->ramener_sur_referentiel;
	$note_sur_dev_choisi = $obj_devoir->note_sur;
	// 20151024
	$date_devoir = $obj_devoir->date;

	$sql = "SELECT id_conteneur, id_racine FROM cn_devoirs WHERE id = '$id_devoir';";
	$query = mysqli_query($GLOBALS["mysqli"], $sql);
	$obj_cn = $query->fetch_object();
	$id_racine = $obj_cn->id_racine;
	$id_conteneur = $obj_cn->id_conteneur;
} else if ((isset($_POST['id_conteneur'])) or (isset($_GET['id_conteneur']))) {
	$id_conteneur = isset($_POST['id_conteneur']) ? $_POST['id_conteneur'] : (isset($_GET['id_conteneur']) ? $_GET['id_conteneur'] : NULL);
	$query = mysqli_query($GLOBALS["mysqli"], "SELECT id_racine FROM cn_conteneurs WHERE id = '$id_conteneur'");
	if (mysqli_num_rows($query) == 0) {
		$msg = "Le conteneur numero $id_conteneur n'existe pas. C'est une anomalie.";
		header("Location: index.php?msg=$msg");
		die();
	} else {
		$obj_cn = $query->fetch_object();
		$id_racine = $obj_cn->id_racine;
	}
} else {
	//debug_var();

	if (($_SESSION['statut'] == 'professeur') && (isset($_GET['id_groupe']) && (isset($_GET['periode_num'])))) {
		$id_groupe = $_GET['id_groupe'];
		$periode_num = $_GET['periode_num'];

		if (is_numeric($id_groupe) && $id_groupe > 0) {
			if (is_groupe_exclu_tel_module($id_groupe, 'cahier_notes')) {
				header("Location: ../accueil.php?msg=Groupe/Enseignement invalide");
				die();
			}

			$current_group = get_group($id_groupe);

			// Avec des classes qui n'ont pas le même nombre de période, on peut arriver avec un periode_num impossible pour un id_groupe
			while ((!isset($current_group["classe"]["ver_periode"]["all"][$periode_num])) && ($periode_num > 0)) {
				$periode_num--;
			}
			if ($periode_num < 1) {
				$mess = rawurlencode("ERREUR: Aucune période n'a été trouvée pour le groupe choisi !");
				header("Location: index.php?msg=$mess");
				die();
			}

			$login_prof = $_SESSION['login'];
			$appel_cahier_notes = mysqli_query($GLOBALS["mysqli"], "SELECT id_cahier_notes FROM cn_cahier_notes WHERE (id_groupe='$id_groupe' and periode='$periode_num')");
			$nb_cahier_note = mysqli_num_rows($appel_cahier_notes);
			if ($nb_cahier_note == 0) {
				$nom_complet_matiere = $current_group["matiere"]["nom_complet"];
				$nom_court_matiere = $current_group["matiere"]["matiere"];
				$reg = mysqli_query($GLOBALS["mysqli"], "INSERT INTO cn_conteneurs SET id_racine='', nom_court='" . traitement_magic_quotes($current_group["description"]) . "', nom_complet='" . traitement_magic_quotes($nom_complet_matiere) . "', description = '', mode = '2', coef = '1.0', arrondir = 's1', ponderation = '0.0', display_parents = '0', display_bulletin = '1', parent = '0'");
				if ($reg) {
					$id_racine = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
					$reg = mysqli_query($GLOBALS["mysqli"], "UPDATE cn_conteneurs SET id_racine='$id_racine', parent = '0' WHERE id='$id_racine'");
					$reg = mysqli_query($GLOBALS["mysqli"], "INSERT INTO cn_cahier_notes SET id_groupe = '$id_groupe', periode = '$periode_num', id_cahier_notes='$id_racine'");
				}
			} else {
				$obj_cn = $appel_cahier_notes->fetch_object();
				$id_racine = $obj_cn->id_cahier_notes;
			}
			$id_conteneur = $id_racine;
		} else {
			header("Location: ../logout.php?auto=1");
			die();
		}
	} else {
		header("Location: ../logout.php?auto=1");
		die();
	}
}

//20150529
if (isset($_GET['export_csv'])) {
	check_token();

	$nom_fic = $current_group["name"];
	$nom_fic .= "_" . $current_group["description"];
	$nom_fic .= "_" . $current_group["classlist_string"];

	// 20221023 : Insérer le nom du contrôle
	if ((isset($_GET['tmp_id_devoir'])) && (preg_match('/^[0-9]{1,}$/', $_GET['tmp_id_devoir'])) && ($_GET['tmp_id_devoir'] != 0)) {
		$nom_devoir=get_valeur_champ("cn_devoirs", "id='" . $_GET['tmp_id_devoir'] . "'", "nom_court");
		$nom_fic .= '_'.remplace_accents($nom_devoir, 'all');
	}

	$nom_fic .= "_periode_" . $periode_num;
	$nom_fic .= "_" . date("Ymd");
	$nom_fic = remplace_accents($nom_fic, "all");

	$csv = "";

	$header_pdf = array();
	$header_pdf = unserialize($_SESSION['header_pdf']);
	for ($loop = 0; $loop < count($header_pdf); $loop++) {
		$csv .= $header_pdf[$loop] . ";";
	}
	$csv .= "\r\n";

	// tableau des données
	$data_pdf = array();
	$data_pdf = unserialize($_SESSION['data_pdf']);
	for ($loop = 0; $loop < count($data_pdf); $loop++) {
		for ($loop2 = 0; $loop2 < count($data_pdf[$loop]); $loop2++) {
			$csv .= $data_pdf[$loop][$loop2] . ";";
		}
		$csv .= "\r\n";
	}

	/*
	echo "<pre>";
	echo $csv;
	echo "</pre>";
	*/

	send_file_download_headers('text/x-csv', $nom_fic . '.csv');
	echo echo_csv_encoded($csv);
	die();
}

//Initialisation pour le pdf
$w_pdf = array();
$w1 = "i"; //largeur de la première colonne
$w1b = "d"; //largeur de la colonne "classe" si présente
$w2 = "n"; // largeur des colonnes "notes"
$w3 = "c"; // largeur des colonnes "commentaires"
$header_pdf = array();
$data_pdf = array();

$appel_conteneur = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM cn_conteneurs WHERE id ='$id_conteneur'");
$obj_cn = $appel_conteneur->fetch_object();
$nom_conteneur = $obj_cn->nom_court;
$mode = $obj_cn->mode;
$arrondir = $obj_cn->arrondir;
$ponderation = $obj_cn->ponderation;
$display_bulletin = $obj_cn->display_bulletin;

// On teste si le carnet de notes appartient bien à la personne connectée
if (!(Verif_prof_cahier_notes($_SESSION['login'], $id_racine))) {
	$mess = rawurlencode("Vous tentez de pénétrer dans un carnet de notes qui ne vous appartient pas !");
	header("Location: index.php?msg=$mess");
	die();
}

//
// On dispose donc pour la suite des trois variables :
// id_racine
// id_conteneur
// id_devoir

$appel_cahier_notes = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM cn_cahier_notes WHERE id_cahier_notes ='$id_racine'");
$obj_ccn = $appel_cahier_notes->fetch_object();
$id_groupe = $obj_ccn->id_groupe;
$current_group = get_group($id_groupe);
$id_classe = $current_group["classes"]["list"][0];
$periode_num = $obj_ccn->periode;
if (count($current_group["classes"]["list"]) > 1) {
	$multiclasses = true;
} else {
	$multiclasses = false;
	$order_by = "nom";
}
/**
 * Gestion des périodes
 */
include "../lib/periodes.inc.php";

$acces_exceptionnel_saisie = false;
if ($_SESSION['statut'] == 'professeur') {
	$acces_exceptionnel_saisie = acces_exceptionnel_saisie_cn_groupe_periode($id_groupe, $periode_num);
}
/*
// Pour gérer les classes avec des nombres de périodes différents
while((!isset($current_group["classe"]["ver_periode"]["all"][$periode_num]))&&($periode_num>0)) {
	$periode_num--;
}
if($periode_num<1) {
	$mess=rawurlencode("ERREUR: Aucune période n'a été trouvée pour le groupe choisi !");
	header("Location: index.php?msg=$mess");
	die();
}
*/

// On teste si la periode est vérouillée !
if (($current_group["classe"]["ver_periode"]["all"][$periode_num] <= 1) and (isset($id_devoir)) and ($id_devoir != '')) {
	if (!$acces_exceptionnel_saisie) {
		$mess = rawurlencode("Vous tentez de pénétrer dans un carnet de notes dont la période est bloquée !");
		header("Location: index.php?msg=$mess");
		die();
	}
}


$matiere_nom = $current_group["matiere"]["nom_complet"];
$matiere_nom_court = $current_group["matiere"]["matiere"];
$nom_classe = $current_group["classlist_string"];


//$periode_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM periodes WHERE id_classe = '$id_classe' ORDER BY num_periode");
//$nom_periode = old_mysql_result($periode_query, $periode_num-1, "nom_periode");
$periode_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM periodes WHERE id_classe = '$id_classe' AND num_periode='$periode_num' ORDER BY num_periode");
$obj_periode = $periode_query->fetch_object();
$nom_periode = $obj_periode->nom_periode;

//
// Détermination des sous-conteneurs
//
$nom_sous_cont = array();
$id_sous_cont = array();
$coef_sous_cont = array();
$ponderation_sous_cont = array();
$display_bulletin_sous_cont = array();
$nb_sous_cont = 0;
if ($mode == 1) {
	// on s'intéresse à tous les conteneurs fils, petit-fils, ...
	sous_conteneurs($id_conteneur, $nb_sous_cont, $nom_sous_cont, $coef_sous_cont, $id_sous_cont, $display_bulletin_sous_cont, 'all', $ponderation_sous_cont);
} else {
	// On s'intéresse uniquement au conteneurs fils
	sous_conteneurs($id_conteneur, $nb_sous_cont, $nom_sous_cont, $coef_sous_cont, $id_sous_cont, $display_bulletin_sous_cont, '', $ponderation_sous_cont);
}

//debug_var();
//-------------------------------------------------------------------------------------------------------------------
if (isset($_POST['notes'])) {
	check_token();
	$temp = $_POST['notes'] . " 1";
	$temp = my_ereg_replace("\\\\r", "\r", $temp);
	$temp = my_ereg_replace("\\\\n", "\n", $temp);
	$longueur = mb_strlen($temp);
	$i = 0;
	$fin_note = 'yes';
	$indice = $_POST['debut_import'] - 2;
	$tempo = '';
	if (!isset($note_sur_dev_choisi)) {
		$note_sur_dev_choisi = 20;
	}
	while (($i < $longueur) and ($indice < $_POST['fin_import'])) {
		$car = mb_substr($temp, $i, 1);
		if (my_ereg('^[0-9.,a-zA-Z-]{1}$', $car)) {
			if (($fin_note == 'yes') or ($i == $longueur - 1)) {
				$fin_note = 'no';
				if (is_numeric($tempo)) {
					if ($tempo <= $note_sur_dev_choisi) {
						$note_import[$indice] = $tempo;
						$indice++;
					} else {
						$note_import[$indice] = "0";
						$indice++;
					}
				} else {
					$note_import[$indice] = $tempo;
					$indice++;
				}
				$tempo = '';
			}
			$tempo = $tempo . $car;
		} else {
			$fin_note = 'yes';
		}
		$i++;
	}
}

//debug_var();
//-------------------------------------------------------------------------------------------------------------------
if (isset($_POST['import_sacoche'])) {
	//check_token();
	//@TODO check referer

	$note_import = array();
	$i = 0;
	if (!isset($note_sur_dev_choisi)) {
		$note_sur_dev_choisi = 20;
	}
	do {
		$note_import_sacoche[$_POST['log_eleve'][$i]] = round($_POST['note_eleve'][$i] * $note_sur_dev_choisi / 100);
		$i++;
	} while ($i < $_POST['indice_max_log_eleve']);
}

if (isset($_POST['appreciations'])) {
	check_token();

	$temp = $_POST['appreciations'] . " 1";
	// Sous Linux, on n'envoie que des \n
	if (preg_match("/\\\\r/", $temp)) {
		// Cas Window$ et Mac
		$temp = my_ereg_replace("\\\\r", "`", $temp);
		$temp = my_ereg_replace("\\\\n", "", $temp);
	} elseif (preg_match("/\\\\n/", $temp)) {
		// Cas Linux
		$temp = my_ereg_replace("\\\\n", "`", $temp);
	}
	$temp = unslashes($temp);
	$longueur = mb_strlen($temp);
	$i = 0;
	$fin_app = 'yes';
	$indice = $_POST['debut_import'] - 2;
	$tempo = "";
	while (($i < $longueur) and ($indice < $_POST['fin_import'])) {
		$car = mb_substr($temp, $i, 1);
		if (!my_ereg("^[`]{1}$", $car)) {
			if (($fin_app == 'yes') or ($i == $longueur - 1)) {
				$fin_app = 'no';
				$appreciations_import[$indice] = $tempo;
				$indice++;
				$tempo = '';
			}
			$tempo = $tempo . $car;
		} else {
			$fin_app = 'yes';
		}
		$i++;
	}
}

if (isset($_POST['is_posted'])) {
	check_token();

	if (!isset($msg)) {
		$msg = "";
	}

	$tab_precision = array('s1', 's5', 'se', 'p1', 'p5', 'pe');
	$cn_precision = isset($_POST['cn_precision']) ? $_POST['cn_precision'] : "";
	if (in_array($cn_precision, $tab_precision)) {
		savePref($_SESSION['login'], 'cn_precision', $cn_precision);
	} else {
		unset($cn_precision);
	}

	$cn_mode_saisie = isset($_POST['cn_mode_saisie']) ? $_POST['cn_mode_saisie'] : 0;
	if (in_array($cn_mode_saisie, array(0, 1, 2))) {
		savePref($_SESSION['login'], 'cn_mode_saisie', $cn_mode_saisie);
	} else {
		unset($cn_mode_saisie);
	}

	$log_eleve = $_POST['log_eleve'];
	$note_eleve = $_POST['note_eleve'];
	$comment_eleve = $_POST['comment_eleve'];

	$indice_max_log_eleve = $_POST['indice_max_log_eleve'];

	// 20150424
	$modif_note_sur = isset($_POST['modif_note_sur']) ? $_POST['modif_note_sur'] : NULL;
	if ((isset($modif_note_sur)) && ($modif_note_sur != "")) {
		if ((preg_match("/^[0-9]{1,}$/", $modif_note_sur)) || (preg_match("/^[0-9]{1,}.[0-9]{1,}$/", $modif_note_sur))) {

			// Contrôler que la période est ouverte pour au moins un élève...

			if (($current_group["classe"]["ver_periode"]["all"][$periode_num] >= 2) || ($acces_exceptionnel_saisie)) {
				$sql = "UPDATE cn_devoirs SET note_sur='$modif_note_sur' WHERE id='$id_devoir';";
				$update_note_sur = mysqli_query($GLOBALS["mysqli"], $sql);
				if (!$update_note_sur) {
					$msg .= "ERREUR lors de la modification du référentiel de l'évaluation.<br />";
				} else {
					$msg .= "Modification du référentiel de l'évaluation (<em>maintenant sur $modif_note_sur</em>).<br />";
				}
			}
		} else {
			$msg .= "ERREUR : Le référentiel choisi est invalide : '$modif_note_sur'.<br />";
		}
	}

	$appel_note_sur = mysqli_query($GLOBALS["mysqli"], "SELECT note_sur FROM cn_devoirs WHERE id = '$id_devoir'");
	$obj_note_sur = $appel_note_sur->fetch_object();
	$note_sur_verif = $obj_note_sur->note_sur;

	for ($i = 0; $i < $indice_max_log_eleve; $i++) {
		if (isset($log_eleve[$i])) {
			// La période est-elle ouverte?
			$reg_eleve_login = $log_eleve[$i];
			if (isset($current_group["eleves"][$periode_num]["users"][$reg_eleve_login]["classe"])) {
				$id_classe = $current_group["eleves"][$periode_num]["users"][$reg_eleve_login]["classe"];
				if (($current_group["classe"]["ver_periode"][$id_classe][$periode_num] == "N") || ($acces_exceptionnel_saisie)) {
					$note = $note_eleve[$i];
					$elev_statut = '';

					$comment = $comment_eleve[$i];
					$comment = trim(suppression_sauts_de_lignes_surnumeraires($comment));

					if (($note == 'disp') || ($note == 'd')) {
						$note = '0';
						$elev_statut = 'disp';
					} else if (($note == 'abs') || ($note == 'a')) {
						$note = '0';
						$elev_statut = 'abs';
					} else if (($note == '-') || ($note == 'n')) {
						$note = '0';
						$elev_statut = '-';
					} else if (my_ereg("^[0-9\.\,]{1,}$", $note)) {
						$note = str_replace(",", ".", "$note");
						/*
						$appel_note_sur = mysql_query("SELECT NOTE_SUR FROM cn_devoirs WHERE id = '$id_devoir'");
						$note_sur_verif = old_mysql_result($appel_note_sur,0 ,'note_sur');
						*/
						if (($note < 0) or ($note > $note_sur_verif)) {
							$note = '';
							$elev_statut = 'v';
						}
					} else {
						$note = '';
						$elev_statut = 'v';
					}

					$test_eleve_note_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM cn_notes_devoirs WHERE (login='$reg_eleve_login' AND id_devoir = '$id_devoir')");
					$test = mysqli_num_rows($test_eleve_note_query);
					if ($test != "0") {
						if ($current_group["classe"]["ver_periode"][$id_classe][$periode_num] != "N") {
							// On récupère la note précédente de l'élève
							$lig_old_note_ele = mysqli_fetch_object($test_eleve_note_query);

							if (($lig_old_note_ele->note != $note) || ($lig_old_note_ele->statut != $elev_statut)) {
								$texte = "Modification de note au devoir n°$id_devoir pour " . get_nom_prenom_eleve($reg_eleve_login, 'avec_classe') . " : ";
								if (($lig_old_note_ele->statut != "")) {
									$texte .= $lig_old_note_ele->statut . " -> ";
								} else {
									$texte .= $lig_old_note_ele->note . " -> ";
								}
								if ($elev_statut != "") {
									if ($elev_statut == "v") {
										$texte .= "(vide)";
									} else {
										$texte .= $elev_statut;
									}
								} else {
									$texte .= $note;
								}
								$texte .= ".";
								$retour = log_modifs_acces_exceptionnel_saisie_cn_groupe_periode($id_groupe, $periode_num, $texte);
							}
						}

						$sql = "UPDATE cn_notes_devoirs SET comment='" . $comment . "', note='$note',statut='$elev_statut' WHERE (login='" . $reg_eleve_login . "' AND id_devoir='" . $id_devoir . "');";
						//echo "$sql<br />";
						$register = mysqli_query($GLOBALS["mysqli"], $sql);

					} else {
						$sql = "INSERT INTO cn_notes_devoirs SET login='" . $reg_eleve_login . "', id_devoir='" . $id_devoir . "',note='" . $note . "',statut='" . $elev_statut . "',comment='" . $comment . "';";
						$register = mysqli_query($GLOBALS["mysqli"], $sql);

						if ($current_group["classe"]["ver_periode"][$id_classe][$periode_num] != "N") {
							$texte = "Saisie de note au devoir n°$id_devoir pour " . get_nom_prenom_eleve($reg_eleve_login, 'avec_classe') . " : ";
							if (($elev_statut != "")) {
								if ($elev_statut == "v") {
									$texte .= "(vide)";
								} else {
									$texte .= $elev_statut;
								}
							} else {
								$texte .= $note;
							}
							$texte .= ".\n";
							$retour = log_modifs_acces_exceptionnel_saisie_cn_groupe_periode($id_groupe, $periode_num, $texte);
						}
					}

				}
			}
		}
	}

	// Mise à jour des moyennes du conteneur et des conteneurs parent, grand-parent, etc...

	$arret = 'no';
	mise_a_jour_moyennes_conteneurs($current_group, $periode_num, $id_racine, $id_conteneur, $arret);

	//==========================================================
	// Ajout d'un test:
	// Si on modifie un devoir alors que des notes ont été reportées sur le bulletin, il faut penser à mettre à jour la recopie vers le bulletin.
	$sql = "SELECT 1=1 FROM matieres_notes WHERE periode='" . $periode_num . "' AND id_groupe='" . $id_groupe . "';";
	$test_bulletin = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($test_bulletin) > 0) {
		$msg .= " ATTENTION: Des notes sont présentes sur le bulletin.<br />Si vous avez modifié ou ajouté des notes, pensez à mettre à jour la recopie vers le bulletin.<br />";
	}
	//==========================================================

	$affiche_message = 'yes';
}

if ((isset($_GET['recalculer'])) && (isset($id_conteneur)) && (isset($periode_num)) && (isset($current_group))) {
	check_token();

	if ((isset($id_conteneur)) && (($current_group["classe"]["ver_periode"]["all"][$periode_num] >= 2) || ($acces_exceptionnel_saisie))) {

		recherche_enfant($id_conteneur);

		$msg .= "Recalcul des moyennes du carnet de notes effectué.<br />";
	}
}
if (isset($_POST['import_sacoche'])) {
	$message_enregistrement = "Vos notes ne sont pas encore enregistrées, veuillez les vérifier et cliquer sur le bouton d'enregistrement";
} else {
	$message_enregistrement = "Les modifications ont été enregistrées !";
}

if (($message_enregistrement != '') && (isset($affiche_message)) && ($affiche_message == 'yes')) {
	$message_enregistrement .= " (" . strftime("%d/%m/%Y à %H:%M:%S") . ")";
	$msg .= $message_enregistrement;
}

require('cc_lib.php');

$themessage = 'Des notes ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';

$message_cnil_commentaires = "* En conformité avec la CNIL, le professeur s'engage à ne faire figurer dans le carnet de notes que des notes et commentaires portés à la connaissance de l'élève (<em>note et commentaire portés sur la copie, ...</em>).<br />";
$message_cnil_commentaires .= "<br />";
$message_cnil_commentaires .= "Veillez donc à respecter les préconisations suivantes&nbsp;:<br />";
$message_cnil_commentaires .= "<strong>Règle n° 1 :</strong> Avoir à l'esprit, quand on renseigne ces zones commentaires, que la personne qui est concernée peut exercer son droit d'accès et lire ces commentaires !<br />";
$message_cnil_commentaires .= "<strong>Règle n° 2 :</strong> Rédiger des commentaires purement objectifs et jamais excessifs ou insultants.<br />";
$message_cnil_commentaires .= "<br />";
$message_cnil_commentaires .= "Pour plus de détails, consultez <a href='http://www.cnil.fr/la-cnil/actualite/article/article/zones-bloc-note-et-commentaires-les-bons-reflexes-pour-ne-pas-deraper/' target='_blank'>l'article de la CNIL</a>?<br /><br />";

$javascript_specifique[] = "lib/tablekit";
$utilisation_tablekit = "ok";
//**************** EN-TETE *****************
$titre_page = "Saisie des notes";
/**
 * Entête de la page
 */
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
//debug_var();

unset($_SESSION['chemin_retour']);

//===============================================
$tabdiv_infobulle[] = creer_div_infobulle('div_explication_cnil', "Commentaire", "", $message_cnil_commentaires, "", 30, 0, 'y', 'y', 'n', 'n');
// Paramètres concernant le délai avant affichage d'une infobulle via delais_afficher_div()
// Hauteur de la bande testée pour la position de la souris:
$hauteur_survol_infobulle = 20;
// Largeur de la bande testée pour la position de la souris:
$largeur_survol_infobulle = 100;
// Délais en ms avant affichage:
$delais_affichage_infobulle = 500;
//===============================================

?>
<script type="text/javascript" language=javascript>
	chargement = false;
</script>

<?php
if ($id_conteneur == $id_racine) {
	if ($nom_conteneur == "") {
		$titre = $current_group['description'] . " (" . $nom_periode . ")";
	} else {
		$titre = $nom_conteneur . " (" . $nom_periode . ")";
	}
} else {
	$titre = casse_mot(getSettingValue("gepi_denom_boite"), 'majf2') . " : " . $nom_conteneur . " (" . $nom_periode . ")";
}

//$titre_pdf = urlencode(utf8_decode($titre));
$titre_pdf = urlencode($titre);
if ($id_devoir != 0) {
	$titre .= " - SAISIE";
} else {
	$titre .= " - VISUALISATION";
}

echo "<script type=\"text/javascript\" language=\"javascript\">";
if (isset($_POST['debut_import'])) {
	$temp = $_POST['debut_import'] - 1;
	if ((isset($note_import[$temp])) and ($note_import[$temp] != '')) {
		echo "change = 'yes';";
	} else {
		echo "change = 'no';";
	}
} else {
	echo "change = 'no';";
}
echo "</script>";


// Détermination du nombre de devoirs à afficher
$appel_dev = mysqli_query($GLOBALS["mysqli"], "select * from cn_devoirs where (id_conteneur='$id_conteneur' and id_racine='$id_racine') order by date");
$nb_dev = mysqli_num_rows($appel_dev);

// Détermination des noms et identificateurs des devoirs
$j = 0;
//while ($j < $nb_dev) {
while ($obj_dev = $appel_dev->fetch_object()) {
	$nom_dev[$j] = $obj_dev->nom_court;
	$description_dev[$j] = $obj_dev->description;
	$id_dev[$j] = $obj_dev->id;
	$coef[$j] = $obj_dev->coef;
	$note_sur[$j] = $obj_dev->note_sur;
	$ramener_sur_referentiel[$j] = $obj_dev->ramener_sur_referentiel;
	$facultatif[$j] = $obj_dev->facultatif;
	$display_parents[$j] = $obj_dev->display_parents;
	$date_visibilite_ele_resp[$j] = $obj_dev->date_ele_resp;
	// 20151024
	$date_dev[$j] = $obj_dev->date;
	$date = $obj_dev->date;
	$annee = mb_substr($date, 0, 4);
	$mois = mb_substr($date, 5, 2);
	$jour = mb_substr($date, 8, 2);
	$display_date[$j] = $jour . "/" . $mois . "/" . $annee;
	$j++;
}

echo "<form enctype=\"multipart/form-data\" name= \"form1\" action=\"saisie_notes.php\" method=\"get\">\n";
insere_lien_calendrier_crob("right");
echo "<p class='bold'>\n";
echo "<a href=\"../accueil.php\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil </a>|";
echo "<a href='index.php";
if (isset($id_devoir)) {
	echo "?id_groupe=no_group";
}
echo "'  onclick=\"return confirm_abandon (this, change, '$themessage')\"> Mes enseignements </a>|";


if (($_SESSION['statut'] == 'professeur') || ($_SESSION['statut'] == 'secours')) {
	if ($_SESSION['statut'] == 'professeur') {
		$login_prof_groupe_courant = $_SESSION["login"];
	} else {
		$tmp_current_group = get_group($id_groupe);

		$login_prof_groupe_courant = $tmp_current_group["profs"]["list"][0];
	}

	$tab_groups = get_groups_for_prof($login_prof_groupe_courant, "classe puis matière");

	if (!empty($tab_groups)) {

		$chaine_options_classes = "";

		$num_groupe = -1;
		$nb_groupes_suivies = count($tab_groups);
		$id_grp_prec = 0;
		$id_grp_suiv = 0;
		$temoin_tmp = 0;
		$tab_groupes_utiles = array();
		for ($loop = 0; $loop < count($tab_groups); $loop++) {
			if (!is_groupe_exclu_tel_module($tab_groups[$loop]['id'], 'cahier_notes')) {
				if ((!isset($tab_groups[$loop]["visibilite"]["cahier_notes"])) || ($tab_groups[$loop]["visibilite"]["cahier_notes"] == 'y')) {
					// On ne retient que les groupes qui ont un nombre de périodes au moins égal à la période sélectionnée
					if ($tab_groups[$loop]["nb_periode"] >= $periode_num) {
						$tab_groupes_utiles[] = $tab_groups[$loop];
					}
				}
			}
		}
		for ($loop = 0; $loop < count($tab_groupes_utiles); $loop++) {
			if ($tab_groupes_utiles[$loop]['id'] == $id_groupe) {
				$num_groupe = $loop;

				$chaine_options_classes .= "<option value='" . $tab_groupes_utiles[$loop]['id'] . "' selected='true'>" . $tab_groupes_utiles[$loop]['description'] . " (" . $tab_groupes_utiles[$loop]['classlist_string'] . ")</option>\n";

				$temoin_tmp = 1;
				if (isset($tab_groupes_utiles[$loop + 1])) {
					$id_grp_suiv = $tab_groupes_utiles[$loop + 1]['id'];
				} else {
					$id_grp_suiv = 0;
				}
			} else {
				$chaine_options_classes .= "<option value='" . $tab_groupes_utiles[$loop]['id'] . "'>" . $tab_groupes_utiles[$loop]['description'] . " (" . $tab_groupes_utiles[$loop]['classlist_string'] . ")</option>\n";
			}

			if ($temoin_tmp == 0) {
				$id_grp_prec = $tab_groupes_utiles[$loop]['id'];
			}
		}

		if (($chaine_options_classes != "") && ($nb_groupes_suivies > 1)) {

			echo "<script type='text/javascript'>
	// Initialisation
	change='no';

	function confirm_changement_classe(thechange, themessage)
	{
		//alert('thechange='+thechange+' '+document.getElementById('id_groupe').selectedIndex+' '+document.getElementById('id_groupe').options[document.getElementById('id_groupe').selectedIndex].value);
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			document.form1.submit();
		}
		else {
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				document.form1.submit();
			}
			else{
				document.getElementById('id_groupe').selectedIndex=$num_groupe;
			}
		}
	}
</script>\n";

			echo "<input type='hidden' name='periode_num' value='$periode_num' />\n";
			echo "Période $periode_num&nbsp;:";
			if ((isset($id_grp_prec)) && ($id_grp_prec != 0)) {
				//arrow-left.png
				echo " <a href='" . $_SERVER['PHP_SELF'] . "?id_groupe=$id_grp_prec&amp;periode_num=$periode_num' title='Groupe précédent' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' width='16' height='16' alt='Groupe précédent' /></a>\n";
			}
			echo "<select name='id_groupe' id='id_groupe' onchange=\"confirm_changement_classe(change, '$themessage');\">\n";
			echo $chaine_options_classes;
			echo "</select>\n";
			if ((isset($id_grp_suiv)) && ($id_grp_suiv != 0)) {
				//arrow-right.png
				echo "<a href='" . $_SERVER['PHP_SELF'] . "?id_groupe=$id_grp_suiv&amp;periode_num=$periode_num' title='Groupe suivant' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/forward.png' width='16' height='16' alt='Groupe suivant' /></a>\n";
			}
			echo " | \n";
		}

	}
}

// Recuperer la liste des cahiers de notes
$sql = "SELECT * FROM cn_cahier_notes ccn where id_groupe='$id_groupe' ORDER BY periode;";
$res_cn = mysqli_query($GLOBALS["mysqli"], $sql);
if (mysqli_num_rows($res_cn) > 1) {
	// On ne propose pas de champ SELECT pour un seul canier de notes
	$max_per = 0;
	$chaine_options_periodes = "";
	while ($lig_cn = mysqli_fetch_object($res_cn)) {
		$chaine_options_periodes .= "<option value='$lig_cn->id_cahier_notes'";
		if ($lig_cn->periode == $periode_num) {
			$chaine_options_periodes .= " selected='true'";
		}
		$chaine_options_periodes .= ">$lig_cn->periode</option>\n";

		if ($lig_cn->periode > $max_per) {
			$max_per = $lig_cn->periode;
		}
	}

	$index_num_periode = $periode_num - 1;

	echo "<script type='text/javascript'>
	// Initialisation
	change='no';

	function confirm_changement_periode(thechange, themessage)
	{
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			document.getElementById('form1b_id_conteneur').value=document.getElementById('id_conteneur').options[document.getElementById('id_conteneur').selectedIndex].value;
			document.form1b.submit();
		}
		else{
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				document.getElementById('form1b_id_conteneur').value=document.getElementById('id_conteneur').options[document.getElementById('id_conteneur').selectedIndex].value;
				document.form1b.submit();
			}
			else{
				document.getElementById('id_conteneur').selectedIndex=$index_num_periode;
			}
		}
	}
</script>\n";

	echo "<span title='Accéder au cahier de notes de la période (ne sont proposées que les périodes pour lesquelles le cahier de notes a été initialisé)'>Période</span>";
	if ($periode_num > 1) {
		$periode_prec = $periode_num - 1;
		echo " <a href='" . $_SERVER['PHP_SELF'] . "?id_groupe=$id_groupe&amp;periode_num=$periode_prec' title='Période précédente'><img src='../images/icons/back.png' width='16' height='16' alt='Période précédente' /></a>\n";
	}
	echo "<select name='tmp_id_conteneur' id='id_conteneur' onchange=\"confirm_changement_periode(change, '$themessage');\">\n";
	echo $chaine_options_periodes;
	echo "</select>";
	if ($periode_num < $max_per) {
		$periode_suiv = $periode_num + 1;
		echo "<a href='" . $_SERVER['PHP_SELF'] . "?id_groupe=$id_groupe&amp;periode_num=$periode_suiv' title='Période suivante'><img src='../images/icons/forward.png' width='16' height='16' alt='Période suivante' /></a> \n";
	}
	echo " | \n";

}

echo "<a href=\"index.php?id_racine=$id_racine\" onclick=\"return confirm_abandon (this, change, '$themessage')\"> Mes évaluations</a> |";

// On dé-cache le champ si javascript est actif
echo "<span id='span_chgt_dev' style='display: none;'>\n";
$sql = "select * from cn_devoirs where id_conteneur='$id_conteneur' order by date;";
$res_devoirs = mysqli_query($GLOBALS["mysqli"], $sql);
if (mysqli_num_rows($res_devoirs) > 1) {

	$chaine_options_devoirs = "<option value=''>Choix éval.</option>";
	$num_devoir = 0;
	$cpt_dev = 0;
	while ($lig_dev = mysqli_fetch_object($res_devoirs)) {
		$chaine_options_devoirs .= "<option value='$lig_dev->id'";
		if ($lig_dev->id == $id_devoir) {
			$chaine_options_devoirs .= " selected='true'";
			$num_devoir = $cpt_dev;
		}
		$chaine_options_devoirs .= ">$lig_dev->nom_court (" . formate_date($lig_dev->date) . ")</option>\n";
		$cpt_dev++;
	}
	// Seulement avec javascript... parce qu'on a plusieurs submit dans ce formulaire là...
	echo "<select name='select_id_devoir' id='select_id_devoir' onchange=\"confirm_changement_devoir(change, '$themessage');\">\n";
	echo $chaine_options_devoirs;
	echo "</select>";
	echo " | \n";
}
echo "</span>\n";

if ((isset($id_devoir)) && ($id_devoir != 0)) {
	echo "<a href=\"saisie_notes.php?id_conteneur=$id_racine\" onclick=\"return confirm_abandon (this, change, '$themessage')\" title=\"Visualisation du Carnet de Notes\"> Visu.CN </a>|";

	// Ca ne fonctionne pas: On ne récupère que le dernier devoir consulté,... parce qu'imprime_pdf.php récupère ce qui est mis en $_SESSION['data_pdf']
	//echo "<a href=\"../fpdf/imprime_pdf.php?titre=$titre_pdf&amp;id_groupe=$id_groupe&amp;periode_num=$periode_num&amp;nom_pdf_en_detail=oui\" onclick=\"return confirm_abandon (this, change, '$themessage')\" title=\"Export PDF du Carnet de Notes\"> CN PDF </a>|";
}

if (($current_group["classe"]["ver_periode"]["all"][$periode_num] >= 2) || ($acces_exceptionnel_saisie)) {
	if (getSettingAOui('GepiPeutCreerBoitesProf')) {
		echo "<a href='add_modif_conteneur.php?id_racine=$id_racine&amp;mode_navig=retour_saisie&amp;id_retour=$id_conteneur' onclick=\"return confirm_abandon (this, change,'$themessage')\"> Créer un";
		if (getSettingValue("gepi_denom_boite_genre") == 'f') {
			echo "e";
		}
		echo " " . htmlspecialchars(my_strtolower(getSettingValue("gepi_denom_boite"))) . " </a>|";
	}

	echo "<a href='add_modif_dev.php?id_conteneur=$id_racine&amp;mode_navig=retour_saisie&amp;id_retour=$id_conteneur' onclick=\"return confirm_abandon (this, change,'$themessage')\"> Créer une évaluation </a>|";
}

echo "<a href=\"../fpdf/imprime_pdf.php?titre=$titre_pdf&amp;id_groupe=$id_groupe&amp;periode_num=$periode_num&amp;nom_pdf_en_detail=oui\" onclick=\"return VerifChargement()\" target=\"_blank\" ";
if ((isset($id_devoir)) && ($id_devoir != 0)) {
	echo "title=\"Impression des notes de l'évaluation au format PDF\"";
} else {
	echo "title=\"Impression du Carnet de Notes au format PDF\"";
}
echo "> Imprimer au format PDF </a>|";

echo "<a href=\"" . $_SERVER['PHP_SELF'] . "?export_csv=y&amp;id_groupe=$id_groupe&amp;periode_num=$periode_num";
// 20221023
if ((isset($id_devoir)) && ($id_devoir != 0)) {
	echo "&amp;tmp_id_devoir=$id_devoir";
}
echo add_token_in_url() . "\" target=\"_blank\" onclick=\"return VerifChargement()\"> Exporter en CSV </a>| ";

if (acces_modif_liste_eleves_grp_groupes($id_groupe)) {
	echo "<a href='../groupes/grp_groupes_edit_eleves.php?id_groupe=$id_groupe' title=\"Si la liste des élèves du groupe affiché n'est pas correcte, vous êtes autorisé à modifier la liste.\">Modifier le groupe <img src='../images/icons/edit_user.png' class='icone16' title=\"Modifier.\" /></a>";
} else {
	echo "<a href=\"../groupes/signalement_eleves.php?id_groupe=$id_groupe&amp;chemin_retour=../cahier_notes/saisie_notes.php?id_conteneur=$id_conteneur\" title=\"Si certains élèves sont affectés à tort dans cet enseignement, ou si il vous manque certains élèves, vous pouvez dans cette page signaler l'erreur à l'administrateur Gepi.\"> Signaler des erreurs d'affectation <img src='../images/icons/ico_attention.png' class='icone16' alt='Erreur' /></a>";
}

echo "|<a href=\"index_cc.php?id_racine=$id_racine\"> " . ucfirst($nom_cc) . "</a>";

echo "</p>\n";
echo "</form>\n";

if ((isset($current_group["classes"]["list"])) && (count($current_group["classes"]["list"]) == 1)) {
	echo "<div style='float:right; width:30em; font-size:x-small;'>" . affiche_tableau_resp_classe($current_group["classes"]["list"][0]) . "</div>";
}

if ((isset($current_group['eleves'][$periode_num]['list'])) && (count($current_group['eleves'][$periode_num]['list']) == 0)) {
	echo "<p style='color:red'>Il n'y a aucun élève dans cet enseignement sur la période " . $periode_num . ".</p>";
	require("../lib/footer.inc.php");
	die();
}

if (isset($num_devoir)) {
	echo "<script type='text/javascript' language='JavaScript'>
	if(document.getElementById('span_chgt_dev')) {document.getElementById('span_chgt_dev').style.display='';}

	function confirm_changement_devoir(thechange, themessage) {
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			document.location.href=\"" . $_SERVER['PHP_SELF'] . "?id_conteneur=$id_conteneur&id_devoir=\"+document.getElementById('select_id_devoir').options[document.getElementById('select_id_devoir').selectedIndex].value;
		}
		else {
			var is_confirmed = confirm(themessage);
			if(is_confirmed) {
				document.location.href=\"" . $_SERVER['PHP_SELF'] . "?id_conteneur=$id_conteneur&id_devoir=\"+document.getElementById('select_id_devoir').options[document.getElementById('select_id_devoir').selectedIndex].value;
			}
			else {
				document.getElementById('select_id_devoir').selectedIndex=$num_devoir;
			}
		}
	}

</script>\n";
}

// Affichage ou non les colonnes "commentaires"
// Affichage ou non de tous les devoirs
if (isset($_POST['ok'])) {
	if (isset($_POST['affiche_comment'])) {
		$_SESSION['affiche_comment'] = 'no';
	} else {
		$_SESSION['affiche_comment'] = 'yes';
	}
	if (isset($_POST['affiche_tous'])) {
		$_SESSION['affiche_tous'] = 'yes';
	} else {
		$_SESSION['affiche_tous'] = 'no';
	}

}
if (!isset($_SESSION['affiche_comment'])) {
	$_SESSION['affiche_comment'] = 'yes';
}
if (!isset($_SESSION['affiche_tous'])) {
	$_SESSION['affiche_tous'] = 'no';
}
$nb_dev_sous_cont = 0;

// Premier formulaire pour masquer ou non les colonnes "commentaires" non vides des évaluations verrouillées
if ($id_devoir == 0) {
	echo "<form enctype=\"multipart/form-data\" action=\"saisie_notes.php\" method=post name=\"form1b\">\n";
	echo "<fieldset style=\"padding-top: 0px; padding-bottom: 0px;  margin-left: 0px; margin-right: 100px;\">\n";
	echo "<table summary='Paramètres'><tr><td><label for='affiche_comment'>Masquer les colonnes \"commentaires\" non vides (mode visualisation uniquement) :</label>
	</td><td><input type=\"checkbox\" name=\"affiche_comment\" id='affiche_comment' ";
	if ($_SESSION['affiche_comment'] != 'yes') echo "checked";
	echo " /></td><td><input type=\"submit\" name=\"ok\" value=\"OK\" /></td></tr>\n";
	$nb_dev_sous_cont = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "select d.id from cn_devoirs d, cn_conteneurs c where (d.id_conteneur = c.id and c.parent='$id_conteneur')"));
	if ($nb_dev_sous_cont != 0) {
		echo "<tr><td><label for='affiche_tous'>Afficher les évaluations des \"sous-" . htmlspecialchars(my_strtolower(getSettingValue("gepi_denom_boite"))) . "s\" : </label></td><td><input type=\"checkbox\" id='affiche_tous' name=\"affiche_tous\"  ";
		if ($_SESSION['affiche_tous'] == 'yes') {
			echo "checked";
		}
		echo " /></td><td></td></tr>\n";
	}
	echo "</table></fieldset>\n";
	echo "<input type='hidden' name='id_conteneur' id='form1b_id_conteneur' value=\"" . $id_conteneur . "\" />\n";
	echo "<input type='hidden' name='id_devoir' value=\"" . $id_devoir . "\" />\n";
	echo "</form>\n";
} else {
	// Formulaire destiné à permettre via javascript de passer à une autre période... on accède alors au mode Visualisation parce que le $id_devoir n'existe pas dans une autre période.
	echo "<form enctype=\"multipart/form-data\" action=\"saisie_notes.php\" method=post name=\"form1b\">\n";
	echo "<input type='hidden' name='id_conteneur' id='form1b_id_conteneur' value=\"" . $id_conteneur . "\" />\n";
	//echo "<input type='hidden' name='id_devoir' value=\"".$id_devoir."\" />\n";
	echo "</form>\n";
}
// Fin du premier formulaire

// Construction de la variable $detail qui affiche dans un pop-up le mode de calcul de la moyenne
$detail = "Mode de calcul de la moyenne :\\n";
$detail = $detail . "La moyenne s\\'effectue sur les colonnes repérées par les cellules de couleur violette.\\n";
if (($nb_dev_sous_cont != 0) and ($_SESSION['affiche_tous'] == 'no'))
	$detail = $detail . "ATTENTION : cliquez sur \'Afficher les évaluations des sous-" . htmlspecialchars(my_strtolower(getSettingValue("gepi_denom_boite"))) . "s\' pour faire apparaître toutes les évaluations qui interviennent dans la moyenne.\\n";
if ($arrondir == 's1') $detail = $detail . "La moyenne est arrondie au dixième de point supérieur.\\n";
if ($arrondir == 's5') $detail = $detail . "La moyenne est arrondie au demi-point supérieur.\\n";
if ($arrondir == 'se') $detail = $detail . "La moyenne est arrondie au point entier supérieur.\\n";
if ($arrondir == 'p1') $detail = $detail . "La moyenne est arrondie au dixième de point le plus proche.\\n";
if ($arrondir == 'p5') $detail = $detail . "La moyenne est arrondie au demi-point le plus proche.\\n";
if ($arrondir == 'pe') $detail = $detail . "La moyenne est arrondie au point entier le plus proche.\\n";
if ($ponderation != 0) $detail = $detail . "Pondération : " . $ponderation . " (s\\'ajoute au coefficient de la meilleur note de chaque élève).\\n";

// Titre
echo "<h2 class='gepi'>" . htmlspecialchars($titre);

// 20210302
$is_groupe_exclu_module_cn = is_groupe_exclu_tel_module($current_group['id'], 'cahier_notes');
if ($is_groupe_exclu_module_cn) {
	echo " <img src='../images/icons/ico_attention.png' class='icone16' title='Le carnet de notes est désactivé pour au moins une des classes associées à cet enseignement. Les notes saisies ne sont pas visibles des élèves et parents.' />";
	$acces_cn_prof_url_cn_officiel = getSettingValue('acces_cn_prof_url_cn_officiel');
	if ($acces_cn_prof_url_cn_officiel != '') {
		echo "<a href='" . $acces_cn_prof_url_cn_officiel . "' target='_blank' title=\"Accéder à l'application officielle de saisie des résultats aux évaluations : $acces_cn_prof_url_cn_officiel.\"><img src='../images/lien.png' class='icone16' /></a>";
	}
}

echo "</h2>\n";
if (($nb_dev == 0) and ($nb_sous_cont == 0)) {

	echo "<p class=cn>";
	if (getSettingValue("gepi_denom_boite_genre") == 'f') {
		echo "La ";
	} else {
		echo "Le ";
	}
	echo htmlspecialchars(my_strtolower(getSettingValue("gepi_denom_boite"))) . " $nom_conteneur ne contient aucune évaluation.<br /><a href='add_modif_dev.php?id_conteneur=$id_racine&amp;mode_navig=retour_saisie&amp;id_retour=$id_conteneur' onclick=\"return confirm_abandon (this, change,'$themessage')\"><img src='../images/icons/add.png' class='icone16' /> Créer une évaluation</a></p>\n";

	/**
	 * Pied de page
	 */
	require("../lib/footer.inc.php");
	die();
}

// Début du deuxième formulaire
echo "<form enctype=\"multipart/form-data\" action=\"saisie_notes.php\" method=post id=\"form2\">\n";
if ($id_devoir != 0) {
	echo add_token_field();
	if ((isset($current_group['eleves'][$periode_num]['list'])) && (count($current_group['eleves'][$periode_num]['list']) > 0)) {
		echo "<center><input type='submit' value='Enregistrer' /></center>\n";
	}
	echo "<input type='hidden' name='modif_note_sur' id='modif_note_sur' value='' />\n";
}

// Couleurs utilisées
$couleur_devoirs = '#AAE6AA';
$couleur_moy_cont = '#96C8F0';
$couleur_moy_sous_cont = '#FAFABE';
$couleur_calcul_moy = '#AAAAE6';
$note_sur_verif = 20;
if ($id_devoir != 0) {
	$appel_note_sur = mysqli_query($GLOBALS["mysqli"], "SELECT note_sur FROM cn_devoirs WHERE id = '$id_devoir'");
	$obj_note_sur = $appel_note_sur->fetch_object();
	$note_sur_verif = $obj_note_sur->note_sur;
	echo "<p class='cn'>Taper une note de 0 à " . $note_sur_verif . " pour chaque élève, ou à défaut le code 'a' pour 'absent', le code 'd' pour 'dispensé', le code '-' ou 'n' pour absence de note.</p>\n";
	echo "<p class='cn'>Vous pouvez également <b>importer directement vos notes par \"copier/coller\"</b> à partir d'un tableur ou d'une autre application : voir <a href='#import_notes_tableur'>tout en bas de cette page</a>.</p>\n";

}
echo "<p class=cn><b>Enseignement : " . $current_group['description'] . " (" . $current_group["classlist_string"] . ")";
echo "</b></p>\n";

echo "
<script type='text/javascript' language='JavaScript'>

// 20220129
// Avec clavier_note() dans lib/functions.js on exploite les frappes UP/DOWN, PageUp/PageDown pour passer au champ note précédent/suivant,...
// on corrige les saisies &->1, é->2,... (fautes de frappe, Shift oublié pour saisir un chiffre,...)
// on gère les cn_mode_saisie pour selon le mode:
// - passer au champ suivant quand on a saisi 125 pour 12.5
// - passer au champ suivant quand un chiffre après la virgule a été saisi
// ...
// Avec verifcol() ici, on teste sur onchange les valeurs saisies, on corrige les . en ,
// on remplace 'a' par 'abs',...
// et on colorise en rouge si la valeur saisie dépasse la valeur note_sur, en vert sinon.
function verifcol(num_id){
	document.getElementById('n'+num_id).value=document.getElementById('n'+num_id).value.toLowerCase();
	if(document.getElementById('n'+num_id).value=='a'){
		document.getElementById('n'+num_id).value='abs';
	}
	if(document.getElementById('n'+num_id).value=='d'){
		document.getElementById('n'+num_id).value='disp';
	}
	if(document.getElementById('n'+num_id).value=='n'){
		document.getElementById('n'+num_id).value='-';
	}

	note=document.getElementById('n'+num_id).value;

	if((note!='-')&&(note!='disp')&&(note!='abs')&&(note!='')){
		note=note.replace(',','.');

		//if((note.search(/^[0-9.]+$/)!=-1)&&(note.lastIndexOf('.')==note.indexOf('.',0))){

		if(((note.search(/^[0-9.]+$/)!=-1)&&(note.lastIndexOf('.')==note.indexOf('.',0)))||
		((note.search(/^[0-9,]+$/)!=-1)&&(note.lastIndexOf(',')==note.indexOf(',',0)))){
			if((note>" . $note_sur_verif . ")||(note<0)){
				couleur='red';
			}
			else{
				couleur='$couleur_devoirs';
			}
		}
		else{
			couleur='red';
		}
	}
	else{
		couleur='$couleur_devoirs';
	}
	eval('document.getElementById(\'td_'+num_id+'\').style.background=couleur');
}
</script>
";

$i = 0;
while ($i < $nb_dev) {
	$nocomment[$i] = 'yes';
	$i++;
}

// Tableau destiner à stocker l'id du champ de saisie de note (n$num_id) correspondant à l'élève $i
$indice_ele_saisie = array();

$i = 0;
$num_id = 10;
$current_displayed_line = 0;

// On commence par mettre la liste dans l'ordre souhaité
if ($order_by != "classe") {
	$liste_eleves = $current_group["eleves"][$periode_num]["users"];
} else {
	// Ici, on tri par classe
	// On va juste créer une liste des élèves pour chaque classe
	$tab_classes = array();
	foreach ($current_group["classes"]["list"] as $classe_id) {
		$tab_classes[$classe_id] = array();
	}
	// On passe maintenant élève par élève et on les met dans la bonne liste selon leur classe
	foreach ($current_group["eleves"][$periode_num]["list"] as $e_login) {
		$classe = $current_group["eleves"][$periode_num]["users"][$e_login]["classe"];
		$tab_classes[$classe][$e_login] = $current_group["eleves"][$periode_num]["users"][$e_login];
	}
	// On met tout ça à la suite
	$liste_eleves = array();
	foreach ($current_group["classes"]["list"] as $classe_id) {
		$liste_eleves = array_merge($liste_eleves, $tab_classes[$classe_id]);
	}
}

// 20151024
if (($id_devoir != 0) && (isset($date_devoir))) {
	$tmp_liste_eleves = $liste_eleves;
	unset($liste_eleves);
	foreach ($tmp_liste_eleves as $eleve) {
		//if((!isset($eleve["date_sortie"]))||($eleve["date_sortie"]=="0000-00-00 00:00:00")||($eleve["date_sortie"]>$date_devoir)) {
		if (((isset($eleve["date_sortie"])) && ($eleve["date_sortie"] != "0000-00-00 00:00:00") && ($eleve["date_sortie"] < $date_devoir)) ||
			((isset($eleve["date_entree"])) && (($eleve["date_entree"] > $date_devoir)))) {
			// On n'ajoute pas
		} else {
			$liste_eleves[$eleve['login']] = $tmp_liste_eleves[$eleve['login']];
		}
	}
}

$prev_classe = null;

$tab_graph = array();

$edt_et_abs2 = false;
if ((acces('/edt/index2.php', $_SESSION['statut'])) && (getSettingValue('active_module_absence') == '2')) {
	$edt_et_abs2 = true;
}

if (getSettingAOui('active_annees_anterieures')) {
	$tab_ele_aa = array();
	$sql = "SELECT DISTINCT jeg.login FROM j_eleves_groupes jeg, 
							eleves e, 
							archivage_disciplines ad 
						WHERE ad.matiere LIKE '" . $current_group["matiere"]["nom_complet"] . "' AND 
							ad.INE=e.no_gep AND 
							e.login=jeg.login AND 
							jeg.id_groupe='" . $id_groupe . "' AND 
							jeg.periode='" . $periode_num . "';";
	$res_aa = mysqli_query($mysqli, $sql);
	if (mysqli_num_rows($res_aa) > 0) {
		while ($lig_aa = mysqli_fetch_object($res_aa)) {
			$tab_ele_aa[] = $lig_aa->login;
		}
	}
}
/*
echo "<pre>";
print_r($liste_eleves);
echo "</pre>";
*/
if (!isset($liste_eleves)) {
	echo "<p style='color:red'>Il n'y a aucun élève dans cet enseignement sur la période " . $periode_num . ".</p>";
	require("../lib/footer.inc.php");
	die();
}

foreach ($liste_eleves as $eleve) {
	/*
	echo "<pre>";
	print_r($eleve);
	echo "</pre>";
	*/
	$eleve_login[$i] = $eleve["login"];
	$eleve_nom[$i] = $eleve["nom"];
	$eleve_prenom[$i] = $eleve["prenom"];
	$eleve_sexe[$i] = $eleve["sexe"];
	$eleve_classe[$i] = $current_group["classes"]["classes"][$eleve["classe"]]["classe"];
	$eleve_id_classe[$i] = $current_group["classes"]["classes"][$eleve["classe"]]["id"];
	$somme_coef = 0;

	$k = 0;
	while ($k < $nb_dev) {
		$date_dev_formatee = formate_date($date_dev[$k]);

		$sql = "SELECT * FROM cn_notes_devoirs WHERE (login='$eleve_login[$i]' AND id_devoir='$id_dev[$k]')";
		$note_query = mysqli_query($GLOBALS["mysqli"], $sql);

		if ((isset($eleve["sexe"])) && ($eleve["sexe"] == 'F')) {
			$suffixe_accord = 'e';
		} else {
			$suffixe_accord = '';
		}

		// 20151024: Pour des explications
		$eleve_title = "";
		if ($note_query) {
			if (mysqli_num_rows($note_query) > 0) {
				$obj_note_query = $note_query->fetch_object();
				$eleve_statut = $obj_note_query->statut;
				$eleve_note = $obj_note_query->note;
				$eleve_comment = $obj_note_query->comment;
			} else {
				$eleve_statut = "";
				$eleve_note = "";
				$eleve_comment = "";

				// 20151024
				//echo "$sql<br />";
				//if((isset($eleve["date_sortie"]))&&($eleve["date_sortie"]!="0000-00-00 00:00:00")&&($eleve["date_sortie"]<$date_dev[$k])) {

				if ((isset($eleve["date_sortie"])) && ($eleve["date_sortie"] != "0000-00-00 00:00:00") && ($eleve["date_sortie"] < $date_dev[$k])) {
					$eleve_title = "Élève sorti" . $suffixe_accord . " de l'établissement (" . formate_date($eleve["date_sortie"]) . ") avant la date du devoir (" . formate_date($date_dev[$k]) . ").";
				} elseif ((isset($eleve["date_entree"])) && (($eleve["date_entree"] > $date_dev[$k]))) {
					$eleve_title = "Élève entré" . $suffixe_accord . " dans l'établissement (" . formate_date($eleve["date_entree"]) . ") après la date du devoir (" . formate_date($date_dev[$k]) . ").";
				}
			}
		} else {
			$eleve_statut = "";
			$eleve_note = "";
			$eleve_comment = "";

			// 20151024
			if ((isset($eleve["date_sortie"])) && ($eleve["date_sortie"] != "0000-00-00 00:00:00") && ($eleve["date_sortie"] < $date_dev[$k])) {
				$eleve_title = "Élève sorti" . $suffixe_accord . " de l'établissement (" . formate_date($eleve["date_sortie"]) . ") avant la date du devoir (" . formate_date($date_dev[$k]) . ").";
			} elseif ((isset($eleve["date_entree"])) && ($eleve["date_entree"] > $date_dev[$k])) {
				$eleve_title = "Élève entré" . $suffixe_accord . " dans l'établissement (" . formate_date($eleve["date_entree"]) . ") après la date du devoir (" . formate_date($date_dev[$k]) . ").";
			}
		}
		if ($eleve_comment != '') {
			$nocomment[$k] = 'no';
		}
		$eleve_login_note = $eleve_login[$i] . "_note";
		$eleve_login_comment = $eleve_login[$i] . "_comment";
		if ($id_dev[$k] != $id_devoir) {
			$mess_note[$i][$k] = '';
			$mess_note[$i][$k] = $mess_note[$i][$k] . "<td class='cn'";
			// 20151024
			if ($eleve_title == "") {
				$mess_note[$i][$k] .= " bgcolor='$couleur_devoirs'";
			} else {
				$mess_note[$i][$k] .= " bgcolor='orange' title=\"" . $eleve_title . "\"";
			}
			$mess_note[$i][$k] .= "><center><b>";
			if (($eleve_statut != '') and ($eleve_statut != 'v')) {
				$mess_note[$i][$k] = $mess_note[$i][$k] . $eleve_statut;
				$mess_note_pdf[$i][$k] = $eleve_statut;
			} else if ($eleve_statut == 'v') {
				$mess_note[$i][$k] = $mess_note[$i][$k] . "&nbsp;";
				$mess_note_pdf[$i][$k] = "";
			} else {
				if ($eleve_note != '') {
					$mess_note[$i][$k] = $mess_note[$i][$k] . number_format($eleve_note, 1, ',', ' ');
					$mess_note_pdf[$i][$k] = number_format($eleve_note, 1, ',', ' ');

					$tab_graph[$k][] = number_format($eleve_note, 1, '.', ' ');
				} else {
					$mess_note[$i][$k] = $mess_note[$i][$k] . "&nbsp;";
					$mess_note_pdf[$i][$k] = "";
				}
			}
			$mess_note[$i][$k] = $mess_note[$i][$k] . "</b></center></td>\n";
			if ($eleve_comment != '') {
				$mess_comment[$i][$k] = "<td class='cn'";
				// 20151024
				if ($eleve_title != "") {
					$mess_comment[$i][$k] .= " bgcolor='orange' title=\"" . $eleve_title . "\"";
				}
				$mess_comment[$i][$k] .= ">" . $eleve_comment . "</td>\n";
				$mess_comment_pdf[$i][$k] = ($eleve_comment);

			} else {
				$mess_comment[$i][$k] = "<td class='cn'";
				// 20151024
				if ($eleve_title != "") {
					$mess_comment[$i][$k] .= " bgcolor='orange' title=\"" . $eleve_title . "\"";
				}
				$mess_comment[$i][$k] .= ">&nbsp;</td>\n";
				$mess_comment_pdf[$i][$k] = "";
			}
		} else {
			$mess_note[$i][$k] = "<td class='cn' id='td_$num_id' style='text-align:center; background-color:$couleur_devoirs;'>\n";

			$mess_note[$i][$k] .= "<input type='hidden' name='log_eleve[$i]' id='log_eleve_$i' value='$eleve_login[$i]' />\n";

			if (($current_group["classe"]["ver_periode"][$eleve_id_classe[$i]][$periode_num] == "N") || ($acces_exceptionnel_saisie)) {

				$indice_ele_saisie[$i] = $num_id;
				//$mess_note[$i][$k] .= "<input id=\"n".$num_id."\" onKeyDown=\"clavier(this.id,event);\" type=\"text\" size=\"4\" name=\"note_eleve[$i]\" ";
				$mess_note[$i][$k] .= "<input id=\"n" . $num_id . "\" onKeyUp=\"clavier_note(this.id,event);\" type=\"text\" size=\"4\" name=\"note_eleve[$i]\" ";
				$mess_note[$i][$k] .= "autocomplete='off' ";
				$mess_note[$i][$k] .= "value=\"";
			}

			if ((isset($note_import[$current_displayed_line])) and ($note_import[$current_displayed_line] != '')) {
				$mess_note[$i][$k] = $mess_note[$i][$k] . $note_import[$current_displayed_line];
				$mess_note_pdf[$i][$k] = $note_import[$current_displayed_line];
			} else if (isset($note_import_sacoche[$eleve["login"]])) {
				$mess_note[$i][$k] .= $note_import_sacoche[$eleve["login"]];
				$mess_note_pdf[$i][$k] = $note_import_sacoche[$eleve["login"]];
			} else {
				if (($eleve_statut != '') and ($eleve_statut != 'v')) {
					$mess_note[$i][$k] = $mess_note[$i][$k] . $eleve_statut;
					$mess_note_pdf[$i][$k] = $eleve_statut;
				} else if ($eleve_statut == 'v') {
					$mess_note_pdf[$i][$k] = "";
				} else {
					$mess_note[$i][$k] = $mess_note[$i][$k] . $eleve_note;

					if ($eleve_note == "") {
						// Ca ne devrait pas arriver... si: quand le devoir est créé, mais qu'aucune note n'est saisie, ni enregistrement encore effectué.
						// Le simple fait de cliquer sur Enregistrer remplit la table cn_notes_devoirs avec eleve_note='0.0' et eleve_statut='v' et on n'a plus l'erreur
						$mess_note_pdf[$i][$k] = $eleve_note;
					} else {
						if ((preg_match("/^[0-9]*.[0-9]*$/", $eleve_note)) ||
							(preg_match("/^[0-9]*,[0-9]*$/", $eleve_note)) ||
							(preg_match("/^[0-9]*$/", $eleve_note))) {
							$mess_note_pdf[$i][$k] = number_format($eleve_note, 1, ',', ' ');

							$tab_graph[$k][] = number_format($eleve_note, 1, '.', ' ');
						} else {
							echo "<p style='color:red;'>BIZARRE: \$eleve_login[$i]=$eleve_login[$i] \$i=$i et \$k=$j<br />\$eleve_statut=$eleve_statut<br />\$eleve_note=$eleve_note<br />\n";
						}
					}
				}
			}
			if (($current_group["classe"]["ver_periode"][$eleve_id_classe[$i]][$periode_num] == "N") || ($acces_exceptionnel_saisie)) {
				$mess_note[$i][$k] = $mess_note[$i][$k] . "\" onfocus=\"javascript:this.select()";

				if (getSettingValue("gepi_pmv") != "n") {
					$sql = "SELECT elenoet FROM eleves WHERE login='$eleve_login[$i]';";
					$res_ele = mysqli_query($GLOBALS["mysqli"], $sql);
					if (mysqli_num_rows($res_ele) > 0) {
						$lig_ele = mysqli_fetch_object($res_ele);
						if (nom_photo($lig_ele->elenoet)) {
							$mess_note[$i][$k] .= ";affiche_photo('" . nom_photo($lig_ele->elenoet) . "','" . addslashes(my_strtoupper($eleve_nom[$i]) . " " . casse_mot($eleve_prenom[$i], 'majf2')) . "')";
						} else {
							$mess_note[$i][$k] .= ";document.getElementById('div_photo_eleve').innerHTML='';";
						}
					} else {
						$mess_note[$i][$k] .= ";document.getElementById('div_photo_eleve').innerHTML='';";
					}
				}
				$mess_note[$i][$k] .= "\" onchange=\"verifcol($num_id);calcul_moy_med();changement();\" />";

				// 20120509
				$mess_note[$i][$k] .= "<span id='modif_note_$i' style='display:none'><a href='#' onclick=\"modif_note($num_id, $delta_modif_note);return false;\"><img src='../images/icons/add.png' width='16' height='16' alt='Augmenter la note de $delta_modif_note' title='Augmenter la note de $delta_modif_note' /></a><a href='#' onclick=\"modif_note($num_id, -$delta_modif_note);return false;\"><img src='../images/icons/remove.png' width='16' height='16' alt='Diminuer la note de $delta_modif_note' title='Diminuer la note de $delta_modif_note' /></a></span>";
			}
			$mess_note[$i][$k] .= "</td>\n";
			$mess_comment[$i][$k] = "<td class='cn' bgcolor='$couleur_devoirs'>";
			if (($current_group["classe"]["ver_periode"][$eleve_id_classe[$i]][$periode_num] == "N") || ($acces_exceptionnel_saisie)) {
				if ((isset($appreciations_import[$current_displayed_line])) and ($appreciations_import[$current_displayed_line] != '')) {
					$eleve_comment = $appreciations_import[$current_displayed_line];
				}
				$mess_comment[$i][$k] .= "<textarea id=\"n1" . $num_id . "\" onKeyDown=\"clavier(this.id,event);\" name='comment_eleve[$i]' rows=1 cols=60 class='wrap' onchange=\"changement()\"";

				if (getSettingValue("gepi_pmv") != "n") {
					$mess_comment[$i][$k] .= " onfocus=\"";
					$sql = "SELECT elenoet FROM eleves WHERE login='$eleve_login[$i]';";
					$res_ele = mysqli_query($GLOBALS["mysqli"], $sql);
					if (mysqli_num_rows($res_ele) > 0) {
						$lig_ele = mysqli_fetch_object($res_ele);
						if (nom_photo($lig_ele->elenoet)) {
							$mess_comment[$i][$k] .= ";affiche_photo('" . nom_photo($lig_ele->elenoet) . "','" . addslashes(my_strtoupper($eleve_nom[$i]) . " " . casse_mot($eleve_prenom[$i], 'majf2')) . "')";
						} else {
							$mess_comment[$i][$k] .= ";document.getElementById('div_photo_eleve').innerHTML='';";
						}
					} else {
						$mess_comment[$i][$k] .= ";document.getElementById('div_photo_eleve').innerHTML='';";
					}
					$mess_comment[$i][$k] .= "\"";
				}
				$mess_comment[$i][$k] .= ">" . $eleve_comment . "</textarea>";

				// 20181022
				//$mess_comment[$i][$k] .=affiche_tableau_annee_anterieure_ele_matiere($eleve_login[$i], $current_group['matiere']['nom_complet']);
				$mess_comment[$i][$k] .= " <a href='../cahier_notes/saisie_notes.php?id_groupe=" . $id_groupe . "&amp;periode_num=" . $periode_num . "' onclick=\"affiche_div_notes_cn(" . $current_group['id'] . ", '" . $eleve_login[$i] . "', '" . str_replace('"', " ", str_replace("'", " ", $eleve_nom[$i] . " " . $eleve_prenom[$i])) . "');return false;\" target='_blank' title=\"Visualiser en infobulle les notes du carnet de notes.\"><img src='../images/icons/cn_16.png' class='icone16' alt='Visualiser' /></a>";

				if ((getSettingAOui('active_annees_anterieures')) && (in_array($eleve_login[$i], $tab_ele_aa))) {
					$mess_comment[$i][$k] .= " <a href='../mod_annees_anterieures/consultation_annee_anterieure.php?id_classe=" . $eleve["id_classe"] . "&logineleve=" . $eleve_login[$i] . "' onclick=\"affiche_div_aa_ele_matiere('" . $eleve_login[$i] . "', '" . str_replace('"', "%", str_replace("'", "%", $current_group['matiere']['nom_complet'])) . "', '" . str_replace('"', " ", str_replace("'", " ", $eleve_nom[$i] . " " . $eleve_prenom[$i])) . "');return false;\" target='_blank' title=\"Visualiser en infobulle les moyennes et appréciations des années antérieures.\"><img src='../images/icons/annees_anterieures.png' class='icone16' alt='AA' /></a>";
				}

				// 20180225
				if ($edt_et_abs2) {
					$mess_comment[$i][$k] .= " <a href='$gepiPath/edt/index2.php?affichage=semaine&type_affichage=eleve&login_eleve=" . $eleve_login[$i] . "&affichage_complementaire_sur_edt=absences2&display_date=" . $date_dev_formatee . "' target='_blank' title=\"Affichage des absences sur un EDT version 2\"><img src='$gepiPath/images/icons/edt2_abs2.png' width='24' height='24' alt='EDT2' /></a>";
				}

				$mess_comment[$i][$k] .= "</td>\n";
			} else {
				$mess_comment[$i][$k] .= $eleve_comment . "</td>\n";
			}
			$mess_comment_pdf[$i][$k] = ($eleve_comment);
			$num_id++;
		}
		$k++;
	}
	$current_displayed_line++;
	$i++;
}
// 20120509
$max_indice_eleve = $i;

// 20210316
/*
if(isset($tab_graph)) {
echo "<pre>";
print_r($tab_graph);
echo "</pre>";
}
*/
//========================================
// 20181022

$titre_infobulle = "Notes <span id='span_titre_notes_ele'></span>";
$texte_infobulle = "<div id='div_notes_cn'></div>";
$tabdiv_infobulle[] = creer_div_infobulle('div_infobulle_notes_cn', $titre_infobulle, "", $texte_infobulle, "", 30, 0, 'y', 'y', 'n', 'n');

$titre_infobulle = "Années antérieures <span id='span_titre_aa_ele_matiere'></span>";
$texte_infobulle = "<div id='div_aa_ele_matiere'></div>";
$tabdiv_infobulle[] = creer_div_infobulle('div_infobulle_aa_ele_matiere', $titre_infobulle, "", $texte_infobulle, "", 50, 0, 'y', 'y', 'n', 'n');
echo "<script type='text/javascript'>
	function affiche_div_notes_cn(id_groupe, login_ele, designation_ele) {

		document.getElementById('span_titre_notes_ele').innerHTML=designation_ele;

		new Ajax.Updater($('div_notes_cn'),'../lib/ajax_action.php?mode=notes_ele_grp_per&ele_login='+login_ele+'&id_groupe='+id_groupe,{method: 'get'});

		afficher_div('div_infobulle_notes_cn', 'y', 10, 10);
	}

	function affiche_div_aa_ele_matiere(login_ele, matiere, designation_ele) {

		document.getElementById('span_titre_aa_ele_matiere').innerHTML=designation_ele;

		new Ajax.Updater($('div_aa_ele_matiere'),'../lib/ajax_action.php?mode=notes_ele_aa_matiere&ele_login='+login_ele+'&matiere='+matiere,{method: 'get'});

		afficher_div('div_infobulle_aa_ele_matiere', 'y', 10, 10);
	}
</script>";
//========================================


// Affichage du tableau

echo "<table class='boireaus' cellspacing='2' cellpadding='1' summary=\"Tableau de notes\">\n";

// Première ligne

// on calcule le nombre de colonnes à scinder
if ($id_devoir == 0) {
	// Mode consultation
	$nb_colspan = $nb_dev;
	$i = 0;
	while ($i < $nb_dev) {
		if ((($nocomment[$i] != 'yes') and ($_SESSION['affiche_comment'] == 'yes')) or ($id_dev[$i] == $id_devoir)) {
			$nb_colspan++;
		}
		$i++;
	}
} else {
	// En mode saisie, on n'affiche que le devoir à saisir
	$nb_colspan = 2;
}

// Affichage première ligne

echo "<tr><th class='cn'>&nbsp;</th>\n";
if ($multiclasses) {
	echo "<th class='cn'>&nbsp;</th>\n";
}
echo "\n";
if ($nb_dev != 0) {
	if ($nom_conteneur != "") {
		echo "<th class='cn' colspan='$nb_colspan' valign='top'>\n";
		echo "<div style='float:right; width:16;'><a href='javascript:affichage_quartiles();'><img src='../images/icons/histogramme.png' width='16' height='16' alt='Afficher les quartiles' title='Afficher les quartiles' /></a></div>\n";

		echo "$nom_conteneur\n";

		if ($ponderation != '0.0') {
			$message_ponderation = "La meilleure note de la " . getSettingValue("gepi_denom_boite") . " est pondérée d un coefficient $ponderation";
			echo " <img src='../images/icons/flag.png' width='17' height='18' alt=\"$message_ponderation\" title=\"$message_ponderation\" />";
		}
		echo "</th>\n";
	} else {
		echo "<th class='cn' colspan='$nb_colspan' valign='top'><center>&nbsp;</center></th>\n";
	}
}

// on affiche les sous-conteneurs et les devoirs des sous-conteneurs si on n'est pas en mode saisie ($id_devoir == 0)
if ($id_devoir == 0) {
	$i = 0;
	while ($i < $nb_sous_cont) {
		// on affiche les devoirs des sous-conteneurs si l'utilisateur a fait le choix de tout afficher
		if (($_SESSION['affiche_tous'] == 'yes') and ($id_devoir == 0)) {
			$query_nb_dev = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM cn_devoirs where (id_conteneur='$id_sous_cont[$i]' and id_racine='$id_racine') order by date");
			$nb_dev_s_cont[$i] = mysqli_num_rows($query_nb_dev);
			$m = 0;
			//while ($m < $nb_dev_s_cont[$i]) {
			while ($obj_nb_dev = $query_nb_dev->fetch_object()) {
				$id_s_dev[$i][$m] = $obj_nb_dev->id;
				$nom_sous_dev[$i][$m] = $obj_nb_dev->nom_court;
				$coef_s_dev[$i][$m] = $obj_nb_dev->coef;
				$note_sur_s_dev[$i][$m] = $obj_nb_dev->note_sur;
				$ramener_sur_referentiel_s_dev[$i][$m] = $obj_nb_dev->ramener_sur_referentiel;
				$fac_s_dev[$i][$m] = $obj_nb_dev->facultatif;
				$date = $obj_nb_dev->date;
				$annee = mb_substr($date, 0, 4);
				$mois = mb_substr($date, 5, 2);
				$jour = mb_substr($date, 8, 2);
				$display_date_s_dev[$i][$m] = $jour . "/" . $mois . "/" . $annee;

				$m++;
			}
			if ($nom_sous_cont[$i] != "") {
				$cellule_nom_sous_cont = $nom_sous_cont[$i];
			} else {
				$cellule_nom_sous_cont = "&nbsp;";
			}
			if ($nb_dev_s_cont[$i] != 0) {
				echo "<th class=cn colspan='$nb_dev_s_cont[$i]' valign='top'><center>$cellule_nom_sous_cont</center></th>\n";
			}
		}
		if ($nom_sous_cont[$i] != "") {
			echo "<th class='cn' valign='top'><center><b>$nom_sous_cont[$i]</b><br />\n";
		} else {
			echo "<th class='cn' valign='top'><center><b>&nbsp;</b><br />\n";
		}
		if (($current_group["classe"]["ver_periode"]["all"][$periode_num] >= 3) || ($acces_exceptionnel_saisie)) {
			if (getSettingAOui('GepiPeutCreerBoitesProf')) {
				echo "<a href=\"./add_modif_conteneur.php?mode_navig=retour_saisie&amp;id_conteneur=$id_sous_cont[$i]&amp;id_retour=$id_conteneur\"  onclick=\"return confirm_abandon (this, change,'$themessage')\">Configuration</a><br />\n";
			}
		}

		echo "<a href=\"./saisie_notes.php?id_conteneur=$id_sous_cont[$i]\"  onclick=\"return confirm_abandon (this, change,'$themessage')\">Visualisation</a>\n";
		if ($display_bulletin_sous_cont[$i] == '1') {
			echo "<br /><font color='red'>Aff.&nbsp;bull.</font>\n";
		}
		echo "</center></th>\n";
		$i++;
	}
}
// En mode saisie, on n'affiche que le devoir à saisir
if ((($current_group["classe"]["ver_periode"]["all"][$periode_num] >= 2) || ($acces_exceptionnel_saisie)) and ($id_devoir == 0)) {
	if ($nom_conteneur != "") {
		echo "<th class='cn' valign='top'><center><b>$nom_conteneur</b>";

		if ($ponderation != '0.0') {
			$message_ponderation = "La meilleure note de la " . getSettingValue("gepi_denom_boite") . " est pondérée d un coefficient $ponderation";
			echo " <img src='../images/icons/flag.png' width='17' height='18' alt=\"$message_ponderation\" title=\"$message_ponderation\" />";
		}
		echo "<br />";
	} else {
		echo "<th class='cn' valign='top'><center><b>&nbsp;</b><br />";
	}

	if (getSettingAOui('GepiPeutCreerBoitesProf')) {
		echo "<a href=\"./add_modif_conteneur.php?mode_navig=retour_saisie&amp;id_conteneur=$id_conteneur&amp;id_retour=$id_conteneur\"  onclick=\"return confirm_abandon (this, change,'$themessage')\">Configuration</a><br />";
	}
	echo "<br /><font color='red'>Aff.&nbsp;bull.</font></center></th>\n";
} else {
	echo "<th class='cn' valign='top'>&nbsp;</th>\n";
}

echo "</tr>\n";

// Deuxième ligne
echo "<tr>\n";
echo "<td class=cn valign='top'>";
//echo "&nbsp;";
echo "<p id='p_ramener_sur_N' style='display:none'><a href='#' onclick=\"afficher_div('div_ramener_sur_N','y',20,20); return false;\" target=\'_blank\'>Ramener sur N</a>";
// 20120509
//if(getSettingAOui('cn_increment_notes')) {
echo " . <a href='#' onclick=\"affichage_modif_note();return false;\" title='Incrémenter/décrémenter les notes de $delta_modif_note'>+/-</a>";
//}
echo " . <a href='#' onclick=\"afficher_div('div_facilite_saisie','y',20,20); return false;\" target=\'_blank\' title=\"Afficher la fenêtre de choix du mode de saisie.\"><img src='../images/icons/wizard.png' class='icone16' alt='Facilité de saisie' /></a>";
echo "</p>";
echo "<input type='hidden' name='cn_precision' id='cn_precision' value='' />\n";
echo "</td>\n";
$header_pdf[] = "Evaluation :";
if ($multiclasses) {
	$header_pdf[] = "";
}
$w_pdf[] = $w1;
if ($multiclasses) {
	echo "<td class='cn'>&nbsp;</td>\n";
}
if ($multiclasses) {
	$w_pdf[] = $w2;
}

echo "<!-- Liste des évaluations à la racine -->\n";
$i = 0;
$indice_devoir_saisi = -1;
while ($i < $nb_dev) {
	// En mode saisie, on n'affiche que le devoir à saisir
	if (($id_devoir == 0) or ($id_dev[$i] == $id_devoir)) {
		$indice_devoir_saisi = $i;
		if ($coef[$i] != 0) {
			$tmp = " bgcolor = $couleur_calcul_moy ";
		} else {
			$tmp = '';
		}
		$header_pdf[] = ($nom_dev[$i] . " (" . $display_date[$i] . ")");
		$w_pdf[] = $w2;
		if (($current_group["classe"]["ver_periode"]["all"][$periode_num] >= 2) || ($acces_exceptionnel_saisie)) {
			echo "<td class=cn" . $tmp . " valign='top'><center><b><a href=\"./add_modif_dev.php?mode_navig=retour_saisie&amp;id_retour=$id_conteneur&amp;id_devoir=$id_dev[$i]\"  onclick=\"return confirm_abandon (this, change,'$themessage')\" title=\"" . ($description_dev[$i] != '' ? str_replace('"', "'", $description_dev[$i]) . "\n\n" : "") . "Cliquer pour modifier les paramètres de cette évaluation (nom, coefficient, date, date de visibilité,...)\">$nom_dev[$i]</a></b><br /><font size=-2>(<em title=\"Date de l'évaluation\">$display_date[$i]</em>)</font>\n";
			echo "<span id='span_visibilite_" . $id_dev[$i] . "'>";
			if ($display_parents[$i] != 0) {
				/*
				echo " <img src='../images/icons/visible.png' width='19' height='16' title='Evaluation visible sur le relevé de notes.
Visible à compter du ".formate_date($date_visibilite_ele_resp[$i])." pour les parents et élèves.' alt='Evaluation visible sur le relevé de notes' />\n";
				*/

				echo "<a href='index.php?id_groupe=$id_groupe&amp;id_racine=$id_racine&amp;id_dev=" . $id_dev[$i] . "&amp;mode=change_visibilite_dev&amp;visible=n" . add_token_in_url() . "' onclick=\"change_visibilite_dev(" . $id_dev[$i] . ",'n');return false;\"><img src='../images/icons/visible.png' width='19' height='16' title='Evaluation du " . $display_date[$i] . " visible sur le relevé de notes.
Visible à compter du " . formate_date($date_visibilite_ele_resp[$i]) . " pour les parents et élèves.

Cliquez pour ne pas faire apparaître cette note sur le relevé de notes.' alt='Evaluation visible sur le relevé de notes' /></a>";

			} else {
				/*
				echo " <img src='../images/icons/invisible.png' width='19' height='16' title='Evaluation non visible sur le relevé de notes' alt='Evaluation non visible sur le relevé de notes' />\n";
				*/
				echo " <a href='index.php?id_groupe=$id_groupe&amp;id_racine=$id_racine&amp;id_dev=" . $id_dev[$i] . "&amp;mode=change_visibilite_dev&amp;visible=y" . add_token_in_url() . "' onclick=\"change_visibilite_dev(" . $id_dev[$i] . ",'y');return false;\"><img src='../images/icons/invisible.png' width='19' height='16' title='Evaluation non visible sur le relevé de notes.
					
Cliquez pour faire apparaître cette note sur le relevé de notes.' alt='Evaluation non visible sur le relevé de notes' /></a>\n";
			}
			echo "</span>";
			echo "</center></td>\n";
		} else {
			echo "<td class=cn" . $tmp . " valign='top' title=\"" . ($description_dev[$i] != '' ? str_replace('"', "'", $description_dev[$i]) : "") . "\"><center><b>" . $nom_dev[$i] . "</b><br /><font size=-2>($display_date[$i])</font>\n";
			if ($display_parents[$i] != 0) {
				echo " <img src='../images/icons/visible.png' width='19' height='16' title='Evaluation visible sur le relevé de notes' alt='Evaluation visible sur le relevé de notes.
Visible à compter du " . formate_date($date_visibilite_ele_resp[$i]) . " pour les parents et élèves.' />\n";
			} else {
				echo " <img src='../images/icons/invisible.png' width='19' height='16' title='Evaluation non visible sur le relevé de notes' alt='Evaluation non visible sur le relevé de notes' />\n";
			}
			echo "</center></td>\n";
		}

		if ((($nocomment[$i] != 'yes') and ($_SESSION['affiche_comment'] == 'yes')) or ($id_dev[$i] == $id_devoir)) {
			//echo "<td class=cn  valign='top'><center><span title=\"$message_cnil_commentaires\">Commentaire&nbsp;*</span>\n";
			echo "<td class=cn  valign='top'><center><a href='#' onclick=\"afficher_div('div_explication_cnil','y',10,-40);return false;\" onmouseover=\"delais_afficher_div('div_explication_cnil','y',10,-40, $delais_affichage_infobulle, $largeur_survol_infobulle, $hauteur_survol_infobulle);\">Commentaire&nbsp;*</a>\n";

			echo "</center></td>\n";
			$header_pdf[] = "Commentaire";
			$w_pdf[] = $w3;
		}
	}
	$i++;
}

// on affiche les sous-conteneurs et les devoirs des sous-conteneurs si on n'est pas en mode saisie ($id_devoir == 0)
if ($id_devoir == 0) {
	$i = 0;
	while ($i < $nb_sous_cont) {
		$tmp = '';
		if ($_SESSION['affiche_tous'] == 'yes') {
			echo "<!-- Liste des évaluations dans les conteneurs/boites -->\n";
			$m = 0;
			while ($m < $nb_dev_s_cont[$i]) {
				$tmp = '';
				if (($mode == 1) and ($coef_s_dev[$i][$m] != 0)) $tmp = " bgcolor = $couleur_calcul_moy ";
				$header_pdf[] = ($nom_sous_dev[$i][$m] . " (" . $display_date_s_dev[$i][$m] . ")");
				$w_pdf[] = $w2;
				if (($current_group["classe"]["ver_periode"]["all"][$periode_num] >= 2) || ($acces_exceptionnel_saisie)) {
					echo "<td class=cn" . $tmp . " valign='top' style='background-color:plum'><center><b><a href=\"./add_modif_dev.php?mode_navig=retour_saisie&amp;id_retour=$id_conteneur&amp;id_devoir=" . $id_s_dev[$i][$m] . "\"  onclick=\"return confirm_abandon (this, change,'$themessage')\">" . $nom_sous_dev[$i][$m] . "</a></b><br /><font size=-2>(" . $display_date_s_dev[$i][$m] . ")</font></center></td>\n";
				} else {
					echo "<td class=cn" . $tmp . " valign='top' style='background-color:plum'><center><b>" . $nom_sous_dev[$i][$m] . "</b><br /><font size=-2>(" . $display_date_s_dev[$i][$m] . ")</font></center></td>\n";
				}
				$m++;
			}
			$tmp = '';
			if (($mode == 2) and ($coef_sous_cont[$i] != 0)) $tmp = " bgcolor = $couleur_calcul_moy ";
		}
		echo "<td class=cn" . $tmp . " valign='top'><center>Moyenne</center></td>\n";
		$header_pdf[] = ("Moyenne : " . $nom_sous_cont[$i]);
		$w_pdf[] = $w2;
		$i++;
	}
}
// En mode saisie, on n'affiche que le devoir à saisir
if ($id_devoir == 0) {
	echo "<th class='cn' valign='top'><center><b>Moyenne</b>\n";
	if ((isset($id_conteneur)) && (($current_group["classe"]["ver_periode"]["all"][$periode_num] >= 2) || ($acces_exceptionnel_saisie))) {
		echo "<br /><a href='" . $_SERVER['PHP_SELF'] . "?id_conteneur=$id_conteneur&amp;recalculer=y" . add_token_in_url() . "'>Recalculer</a>";
	}
	echo "</center></th>\n";
	$header_pdf[] = "Moyenne";
	$w_pdf[] = $w2;
}
echo "</tr>";

//
// Troisième ligne
//
echo "<tr><td class=cn valign='top'>&nbsp;</td>";
if ($multiclasses) {
	echo "<td class='cn'>&nbsp;</td>";
}
echo "\n";
echo "<!-- Liens de saisie pour les évaluations à la racine si la période est ouverte  -->\n";
$i = 0;
while ($i < $nb_dev) {
	// En mode saisie, on n'affiche que le devoir à saisir
	if (($id_devoir == 0) or ($id_dev[$i] == $id_devoir)) {
		if ($id_dev[$i] == $id_devoir) {
			echo "<td class=cn valign='top'><center><a href=\"saisie_notes.php?id_conteneur=$id_conteneur&amp;id_devoir=0\" onclick=\"return confirm_abandon (this, change,'$themessage')\" title=\"Visualiser le conteneur $nom_conteneur\">Visualiser</a>";

			$sql = "SELECT * FROM cc_dev WHERE id_cn_dev='$id_dev[$i]';";
			$res_cc_dev = mysqli_query($GLOBALS["mysqli"], $sql);
			if (mysqli_num_rows($res_cc_dev) > 0) {
				$lig_cc_dev = mysqli_fetch_object($res_cc_dev);
				echo "<br /><a href='index_cc.php?id_racine=" . $id_racine . "' title=\"Voir l'évaluation cumul associée $lig_cc_dev->nom_court ($lig_cc_dev->nom_complet)\">EvCum</a>";
			}

			echo "</center></td>\n";
			echo "<td class=cn valign='top'>&nbsp;</td>\n";
		} else {
			if (($current_group["classe"]["ver_periode"]["all"][$periode_num] >= 2) || ($acces_exceptionnel_saisie)) {
				echo "<td class=cn valign='top'><center><a href=\"saisie_notes.php?id_conteneur=$id_conteneur&amp;id_devoir=$id_dev[$i]\" onclick=\"return confirm_abandon (this, change,'$themessage')\">saisir</a>";

				$sql = "SELECT * FROM cc_dev WHERE id_cn_dev='$id_dev[$i]';";
				$res_cc_dev = mysqli_query($GLOBALS["mysqli"], $sql);
				if (mysqli_num_rows($res_cc_dev) > 0) {
					$lig_cc_dev = mysqli_fetch_object($res_cc_dev);
					echo "<br /><a href='index_cc.php?id_racine=" . $id_racine . "' title=\"Voir l'évaluation cumul associée $lig_cc_dev->nom_court ($lig_cc_dev->nom_complet)\">EvCum</a>";
				}
				echo "</center></td>\n";
			} else {
				echo "<td class=cn valign='top'>&nbsp;</td>\n";
			}
			if (($nocomment[$i] != 'yes') and ($_SESSION['affiche_comment'] == 'yes')) {
				echo "<td class=cn valign='top'>&nbsp;</td>\n";
			}
		}
	}
	$i++;
}
// on affiche les sous-conteneurs et les devoirs des sous-conteneurs si on n'est pas en mode saisie ($id_devoir == 0)
if ($id_devoir == 0) {
	$i = 0;
	while ($i < $nb_sous_cont) {
		if ($_SESSION['affiche_tous'] == 'yes') {
			echo "<!-- Liens de saisie pour les évaluations dans les conteneurs/boites -->\n";
			$m = 0;
			while ($m < $nb_dev_s_cont[$i]) {
				if (($current_group["classe"]["ver_periode"]["all"][$periode_num] >= 2) || ($acces_exceptionnel_saisie)) {
					echo "<td class=cn valign='top'><center><a href=\"saisie_notes.php?id_conteneur=" . $id_sous_cont[$i] . "&amp;id_devoir=" . $id_s_dev[$i][$m] . "\" onclick=\"return confirm_abandon (this, change,'$themessage')\">saisir</a></center></td>\n";
				} else {
					// La période est close et il n'y a pas d'accès exceptionnel ouvert
					echo "<td class=cn valign='top'>&nbsp;</td>\n";
				}
				$m++;
			}
		}
		echo "<td class=cn valign='top'><center>&nbsp;</center></td>\n";
		$i++;
	}
}
// En mode saisie, on n'affiche que le devoir à saisir
if ($id_devoir == 0) {
	echo "<td class='cn' valign='top'>&nbsp;</td>\n";
}
echo "</tr>";

// quatrième ligne

echo "<tr><td class='cn' valign='top'><b>" .
	"<a href='saisie_notes.php?id_conteneur=" . $id_conteneur . "&amp;id_devoir=" . $id_devoir . "&amp;order_by=nom' onclick=\"return confirm_abandon (this, change,'$themessage')\">Nom Prénom</a></b></td>";
if ($multiclasses) {
	echo "<td><a href='saisie_notes.php?id_conteneur=" . $id_conteneur . "&amp;id_devoir=" . $id_devoir . "&amp;order_by=classe' onclick=\"return confirm_abandon (this, change,'$themessage')\">Classe</a></td>";
}
echo "\n";

if (getSettingValue("note_autre_que_sur_referentiel") == "V") {
	$data_pdf[0][] = ("Nom Prénom /Note sur (coef)");
} else {
	$data_pdf[0][] = ("Nom Prénom \ (coef)");
}

if ($multiclasses) {
	$data_pdf[0][] = "";
}
$i = 0;
while ($i < $nb_dev) {
	// En mode saisie, on n'affiche que le devoir à saisir
	if (($id_devoir == 0) or ($id_dev[$i] == $id_devoir)) {
		echo "<td class='cn' valign='top'>";
		if (getSettingValue("note_autre_que_sur_referentiel") == "V" || $note_sur[$i] != getSettingValue("referentiel_note")) {
			$data_pdf[0][] = "/" . $note_sur[$i] . " (" . number_format($coef[$i], 1, ',', ' ') . ")";
			if ($ramener_sur_referentiel[$i] != 'V') {
				echo "<font size=-2>Note sur " . $note_sur[$i] . "<br />";
			} else {
				$tabdiv_infobulle[] = creer_div_infobulle('ramenersurReferentiel_' . $i, "", "", "La note est ramenée sur " . getSettingValue("referentiel_note") . " pour le calcul de la moyenne", "", 15, 0, 'n', 'n', 'n', 'n');
				echo "<a href='#' onmouseover=\"delais_afficher_div('ramenersurReferentiel_$i','y',-150,20,1500,10,10);\"";
				echo " onmouseout=\"cacher_div('ramenersurReferentiel_" . $i . "');\"";
				echo ">";

				echo "<font size=-2>Note sur " . $note_sur[$i];
				echo "</a><br />";
			}
		} else {
			$data_pdf[0][] = "(" . number_format($coef[$i], 1, ',', ' ') . ")";
		}
		echo "coef : " . number_format($coef[$i], 1, ',', ' ');
		if (($facultatif[$i] == 'B') or ($facultatif[$i] == 'N')) {
			echo "<br />Bonus";
		}
		echo "</center>";

		echo "<div style='float:right; width:16px;'><a href='copie_dev.php?id_devoir=" . $id_dev[$i] . "' onclick=\"return confirm_abandon (this, change,'$themessage')\" title=\"Copier le devoir et les notes vers une autre période ou un autre enseignement (Les notes ne sont copiées que si les élèves sont les mêmes).\"><img src='../images/icons/copy-16.png' width='16' height='16' /></a></div>";

		echo "<div style='float:right; width:16px;'><a href='#' onclick=\"afficher_dev_infobulle(" . $id_dev[$i] . ");return false;\" title=\"Afficher les notes en infobulle... avec possibilité de tri.\"><img src='../images/icons/chercher.png' class='icone16' alt='Afficher' /></a></div>";

		echo "</td>\n";
		if ($id_dev[$i] == $id_devoir) {
			echo "<td class='cn' valign='top'>&nbsp;</td>\n";
			$data_pdf[0][] = "";
		} else {
			if (($nocomment[$i] != 'yes') and ($_SESSION['affiche_comment'] == 'yes')) {
				echo "<td class='cn' valign='top'>&nbsp;</td>\n";
				$data_pdf[0][] = "";
			}
		}
	}
	$i++;
}

// on affiche les sous-conteneurs et les devoirs des sous-conteneurs si on n'est pas en mode saisie ($id_devoir == 0)
if ($id_devoir == 0) {
	$i = 0;
	while ($i < $nb_sous_cont) {
		if ($_SESSION['affiche_tous'] == 'yes') {
			$m = 0;
			while ($m < $nb_dev_s_cont[$i]) {
				echo "<td class='cn' valign='top'>";
				if (getSettingValue("note_autre_que_sur_referentiel") == "V" || $note_sur_s_dev[$i][$m] != getSettingValue("referentiel_note")) {
					$data_pdf[0][] = "/" . $note_sur_s_dev[$i][$m] . " (" . number_format($coef_s_dev[$i][$m], 1, ',', ' ') . ")";
					if ($ramener_sur_referentiel_s_dev[$i][$m] != 'V') {
						echo "<font size=-2>Note sur " . $note_sur_s_dev[$i][$m] . "<br />";
					} else {
						$tabdiv_infobulle[] = creer_div_infobulle("ramenersurReferentiel_s_dev_" . $i . "_" . $m, "", "", "La note est ramenée sur " . getSettingValue("referentiel_note") . " pour le calcul de la moyenne", "", 15, 0, 'n', 'n', 'n', 'n');
						echo "<a href='#' onmouseover=\"delais_afficher_div('ramenersurReferentiel_s_dev_" . $i . "_" . $m . "','y',-150,20,1500,10,10);\"";
						echo " onmouseout=\"cacher_div('ramenersurReferentiel_s_dev_" . $i . "_" . $m . "');\"";
						echo ">";
						echo "<font size=-2>Note sur " . $note_sur_s_dev[$i][$m];
						echo "</a><br />";
					}
				} else {
					$data_pdf[0][] = "(" . number_format($coef_s_dev[$i][$m], 1, ',', ' ') . ")";
				}


				echo "<center>coef : " . number_format($coef_s_dev[$i][$m], 1, ',', ' ');
				if (($fac_s_dev[$i][$m] == 'B') or ($fac_s_dev[$i][$m] == 'N')) echo "<br />Bonus";
				echo "</center></td>\n";
				$m++;
			}
		}
		if ($mode == 2) {
			echo "<td class='cn' valign='top'><center>coef : " . number_format($coef_sous_cont[$i], 1, ',', ' ') . "</center></td>\n";
			$data_pdf[0][] = number_format($coef_sous_cont[$i], 1, ',', ' ');
		} else {
			echo "<td class='cn' valign='top'><center>&nbsp;</center></td>\n";
			$data_pdf[0][] = "";
		}
		$i++;
	}
}

// En mode saisie, on n'affiche que le devoir à saisir
if ($id_devoir == 0) {
	echo "<td class='cn' valign='top'><center><a href=\"javascript:alert('" . $detail . "')\">Informations</a></center></td>\n";
	$data_pdf[0][] = "";
}

echo "</tr>\n";

$graphe_largeurTotale = 200;
$graphe_hauteurTotale = 150;
$graphe_titre = "Répartition des notes";
$graphe_taille_police = 3;
$graphe_epaisseur_traits = 2;
$graphe_nb_tranches = 5;

$n_dev_0 = 0;
$n_dev_fin = $nb_dev;
if ($id_devoir > 0) {
	for ($k = 0; $k < $nb_dev; $k++) {
		if ($id_dev[$k] == $id_devoir) {
			$n_dev_0 = $k;
			$n_dev_fin = $k + 1;
			break;
		}
	}
	echo "<tr>\n";
	echo "<td>Répartition des notes";
	echo "</td>\n";
	if ($multiclasses) {
		echo "<td>&nbsp;</td>\n";
	}
} elseif ($nb_sous_cont == 0) {
	echo "<tr>\n";
	echo "<td>Répartition des notes";
	echo "</td>\n";
	if ($multiclasses) {
		echo "<td>&nbsp;</td>\n";
	}
}

if (($id_devoir > 0) || ($nb_sous_cont == 0)) {
	for ($k = $n_dev_0; $k < $n_dev_fin; $k++) {

		echo "<td>";
		if (isset($tab_graph[$k])) {
			$graphe_serie = "";
			for ($l = 0; $l < count($tab_graph[$k]); $l++) {
				if ($l > 0) {
					$graphe_serie .= "|";
				}
				$graphe_serie .= $tab_graph[$k][$l];
			}
			$titre = $nom_dev[$k];

			$texte = "<div align='center'><object data='../lib/graphe_svg.php?";
			$texte .= "serie=$graphe_serie";
			$texte .= "&amp;note_sur_serie=$note_sur[$k]";
			$texte .= "&amp;nb_tranches=$graphe_nb_tranches";
			$texte .= "&amp;titre=$graphe_titre";
			$texte .= "&amp;v_legend1=Notes";
			$texte .= "&amp;v_legend2=Effectif";
			$texte .= "&amp;largeurTotale=$graphe_largeurTotale";
			$texte .= "&amp;hauteurTotale=$graphe_hauteurTotale";
			$texte .= "&amp;taille_police=$graphe_taille_police";
			$texte .= "&amp;epaisseur_traits=$graphe_epaisseur_traits";
			$texte .= "'";
			$texte .= " width='$graphe_largeurTotale' height='$graphe_hauteurTotale'";
			$texte .= " type=\"image/svg+xml\"></object></div>\n";

			$tabdiv_infobulle[] = creer_div_infobulle('repartition_notes_' . $k, $titre, "", $texte, "", 14, 0, 'y', 'y', 'n', 'n');

			echo " <a href='#' onmouseover=\"delais_afficher_div('repartition_notes_$k','y',-100,20,1500,10,10);\"";
			echo " onclick=\"alterner_affichage_div('repartition_notes_$k','y',-100,20);return false;\"";
			echo " title=\"Afficher l'histogramme de répartition des notes.\">";
			echo "<img src='../images/icons/histogramme.png' alt='Répartition des notes' />";
			echo "</a>";

			echo " <a href='#' onclick=\"afficher_dev_infobulle(" . $id_dev[$k] . ");return false;\" title=\"Afficher les notes en infobulle... avec possibilité de tri.\"><img src='../images/icons/chercher.png' class='icone16' alt='Afficher' /></a>";
		} else {
			echo "&nbsp;";
		}
		echo "</td>\n";
		if (($nocomment[$k] == 'no') && ($_SESSION['affiche_comment'] == 'yes')) {
			if ($id_devoir > 0) {
				echo "<td><a href='javascript:vider_commentaires()'><img src='../images/icons/trash.png' width='16' height='16' title='Vider les commentaires' alt='Vider les commentaires' /></a></td>\n";
			} else {
				echo "<td>&nbsp;</td>\n";
			}
		}
	}
	if ($id_devoir == 0) {
		// Colonne Moyenne de l'élève
		echo "<td>";
		echo " <a href='#' onmouseover=\"delais_afficher_div('repartition_notes_moyenne','y',-100,20,1500,10,10);\"";
		echo " onclick=\"alterner_affichage_div('repartition_notes_moyenne','y',-100,20);return false;\"";
		echo ">";
		echo "<img src='../images/icons/histogramme.png' alt='Répartition des notes' />";
		echo "</a>";
		echo "</td>\n";
	}
	echo "</tr>\n";
}
//========================

// Pour permettre d'afficher moyenne, médiane, quartiles,... en mode Visualisation du carnet de notes
$chaine_input_moy = "";

// 20141103
$tab_ele_dev = array();

//
// Affichage des lignes "elèves"
//
$alt = 1;
$i = 0;
$pointer = 0;
$tot_data_pdf = 1;
$tab_ele_notes = array();
// 20160222
$tab_note_sur = array();
// 20151024
//$nombre_lignes = count($current_group["eleves"][$periode_num]["list"]);
$nombre_lignes = count($liste_eleves);
while ($i < $nombre_lignes) {
	$pointer++;
	$tot_data_pdf++;
	$data_pdf[$pointer][] = ($eleve_nom[$i] . " " . $eleve_prenom[$i]);
	if ($multiclasses) {
		$data_pdf[$pointer][] = ($eleve_classe[$i]);
	}
	$alt = $alt * (-1);
	echo "<tr class='lig$alt white_hover'>\n";
	if ($eleve_classe[$i] != $prev_classe && $prev_classe != null && $order_by == "classe") {
		echo "<td class=cn style='border-top: 2px solid blue; text-align:left;'>\n";

		echo "<a href='../eleves/visu_eleve.php?ele_login=" . $eleve_login[$i] . "' target='_blank' title='Consulter la fiche élève'>$eleve_nom[$i] $eleve_prenom[$i]</a>\n";

		$tab_ele_notes[$i][] = "";
		echo "</td>";
		if ($multiclasses) {
			echo "<td style='border-top: 2px solid blue;'>$eleve_classe[$i]</td>";
			$tab_ele_notes[$i][] = "";
		}
		echo "\n";
	} else {
		echo "<td class=cn style='text-align:left;'>\n";

		if ($id_devoir != 0) {
			echo "<div style='float:right; width:16;'><a href=\"javascript:";
			if (isset($indice_ele_saisie[$i])) {
				echo "if(document.getElementById('n'+" . $indice_ele_saisie[$i] . ")) {document.getElementById('n'+" . $indice_ele_saisie[$i] . ").focus()};";
				echo "affiche_div_photo();";
			} else {
				echo "affiche_div_photo();";
			}
			echo "\"><img src='../mod_trombinoscopes/images/" . (($eleve_sexe[$i] == "F") ? "photo_f" : "photo_g") . ".png' class='icone16' alt='Afficher la photo élève' title='Afficher la photo élève' /></a></div>\n";
		}

		echo "<a href='../eleves/visu_eleve.php?ele_login=" . $eleve_login[$i] . "' target='_blank' title='Consulter la fiche élève'>$eleve_nom[$i] $eleve_prenom[$i]</a>\n";
		$tab_ele_notes[$i][] = "";
		echo "</td>";
		if ($multiclasses) {
			echo "<td>$eleve_classe[$i]</td>";
			$tab_ele_notes[$i][] = "";
		}
		echo "\n";
	}
	$prev_classe = $eleve_classe[$i];
	$k = 0;
	while ($k < $nb_dev) {
		// En mode saisie, on n'affiche que le devoir à saisir
		if (($id_devoir == 0) or ($id_dev[$k] == $id_devoir)) {
			echo $mess_note[$i][$k];
			$tab_ele_notes[$i][] = $mess_note_pdf[$i][$k];

			// 20160221: Récupérer l'indice courant avec count($tab_ele_notes[$i]);
			$tab_note_sur[count($tab_ele_notes[$i]) - 1] = $note_sur[$k];

			$data_pdf[$pointer][] = $mess_note_pdf[$i][$k];
			if ((($nocomment[$k] != 'yes') and ($_SESSION['affiche_comment'] == 'yes')) or ($id_dev[$k] == $id_devoir)) {
				echo $mess_comment[$i][$k];
				$data_pdf[$pointer][] = ($mess_comment_pdf[$i][$k]);
				$tab_ele_notes[$i][] = "";
			}

			// 20141103
			$tab_ele_dev[$id_dev[$k]]['nom_dev'] = $nom_dev[$k];
			$tab_ele_dev[$id_dev[$k]]['eleve'][$eleve_login[$i]]['nom_prenom'] = $eleve_nom[$i] . " " . $eleve_prenom[$i];
			$tab_ele_dev[$id_dev[$k]]['eleve'][$eleve_login[$i]]['note'] = $mess_note_pdf[$i][$k];
		}
		$k++;
	}

	// Affichage de la moyenne de tous les sous-conteneurs

	// on affiche les sous-conteneurs et les devoirs des sous-conteneurs si on n'est pas en mode saisie ($id_devoir == 0)
	if ($id_devoir == 0) {
		$k = 0;
		while ($k < $nb_sous_cont) {
			if ($_SESSION['affiche_tous'] == 'yes') {
				$m = 0;
				while ($m < $nb_dev_s_cont[$k]) {
					$temp = $id_s_dev[$k][$m];
					$note_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM cn_notes_devoirs WHERE (login='$eleve_login[$i]' AND id_devoir='$temp')");
					if ($note_query && $note_query->num_rows) {
						$obj_note_query = $note_query->fetch_object();
						$eleve_statut = $obj_note_query->statut;
						$eleve_note = $obj_note_query->note;
						if (($eleve_statut != '') and ($eleve_statut != 'v')) {
							$tmp = $eleve_statut;
							$data_pdf[$pointer][] = $eleve_statut;

							$tab_ele_notes[$i][] = $eleve_statut;
						} else if ($eleve_statut == 'v') {
							$tmp = "&nbsp;";
							$data_pdf[$pointer][] = "";

							$tab_ele_notes[$i][] = "";
						} else {
							if ($eleve_note != '') {
								$tmp = number_format($eleve_note, 1, ',', ' ');
								$data_pdf[$pointer][] = number_format($eleve_note, 1, ',', ' ');

								$tab_ele_notes[$i][] = $eleve_note;
							} else {
								$tmp = "&nbsp;";
								$data_pdf[$pointer][] = "";

								$tab_ele_notes[$i][] = "";
							}
						}
					} else {
						$eleve_statut = "";
						$eleve_note = "";
						$tmp = "&nbsp;";
						$data_pdf[$pointer][] = "";

						$tab_ele_notes[$i][] = "";
					}
					//$tab_ele_notes[$i][]=$eleve_note;

					echo "<td class='cn' bgcolor='$couleur_devoirs'><center><b>$tmp</b></center></td>\n";

					$m++;
				}
			}

			$moyenne_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM cn_notes_conteneurs WHERE (login='$eleve_login[$i]' AND id_conteneur='$id_sous_cont[$k]')");

			if ($moyenne_query && $moyenne_query->num_rows) {
				$obj_moyenne_query = $moyenne_query->fetch_object();
				$statut_moy = $obj_moyenne_query->statut;
				if ($statut_moy == 'y') {
					$moy = $obj_moyenne_query->note;
					$moy = number_format($moy, 1, ',', ' ');
					$data_pdf[$pointer][] = $moy;
					$tab_ele_notes[$i][] = $moy;
				} else {
					$tab_ele_notes[$i][] = '';
					$moy = '&nbsp;';
					$data_pdf[$pointer][] = "";
				}
			} else {
				$tab_ele_notes[$i][] = '';
				$statut_moy = "";
				$moy = '&nbsp;';
				$data_pdf[$pointer][] = "";
			}
			echo "<td class='cn' bgcolor='$couleur_moy_sous_cont'><center>$moy</center></td>\n";
			$k++;
		}
	}

	// affichage des moyennes du conteneur (moyennes des élèves sur le conteneur choisi (éventuellement le conteneur racine))

	// En mode saisie, on n'affiche que le devoir à saisir
	if ($id_devoir == 0) {
		$moyenne_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM cn_notes_conteneurs WHERE (login='$eleve_login[$i]' AND id_conteneur='$id_conteneur')");

		if ($moyenne_query && $moyenne_query->num_rows) {
			$obj_moyenne_query = $moyenne_query->fetch_object();
			$statut_moy = $obj_moyenne_query->statut;
			if ($statut_moy == 'y') {
				$moy = $obj_moyenne_query->note;
				$moy = number_format($moy, 1, ',', ' ');
				$data_pdf[$pointer][] = $moy;
				$tab_ele_notes[$i][] = $moy;

				$tab_graph_moy[] = number_format(strtr($moy, ",", "."), 1, '.', ' ');

			} else {
				$tab_ele_notes[$i][] = '';
				$moy = '&nbsp;';
				$data_pdf[$pointer][] = "";
			}
		} else {
			$tab_ele_notes[$i][] = '';
			$statut_moy = "";
			$moy = '&nbsp;';
			$data_pdf[$pointer][] = "";
		}

		echo "<td class='cn' bgcolor='$couleur_moy_cont'><center><b>$moy</b></center></td>\n";
		if ($moy == '&nbsp;') {
			$chaine_input_moy .= "<input type='hidden' name='n$i' id='n$i' value='' />\n";
		} else {
			$chaine_input_moy .= "<input type='hidden' name='n$i' id='n$i' value='$moy' />\n";
		}
	}
	echo "</tr>\n";

	$i++;
}
$nombre_lignes = $i;

// Génération de l'infobulle pour $tab_graph_moy[]
if ($id_devoir == 0) {
	$graphe_serie = "";
	if (isset($tab_graph_moy)) {
		for ($l = 0; $l < count($tab_graph_moy); $l++) {
			if ($l > 0) {
				$graphe_serie .= "|";
			}
			$graphe_serie .= $tab_graph_moy[$l];
		}
	}

	$titre = "Moyenne";

	$texte = "<div align='center'><object data='../lib/graphe_svg.php?";
	$texte .= "serie=$graphe_serie";
	$texte .= "&amp;note_sur_serie=20";
	$texte .= "&amp;nb_tranches=5";
	$texte .= "&amp;titre=$graphe_titre";
	$texte .= "&amp;v_legend1=Notes";
	$texte .= "&amp;v_legend2=Effectif";
	$texte .= "&amp;largeurTotale=$graphe_largeurTotale";
	$texte .= "&amp;hauteurTotale=$graphe_hauteurTotale";
	$texte .= "&amp;taille_police=$graphe_taille_police";
	$texte .= "&amp;epaisseur_traits=$graphe_epaisseur_traits";
	$texte .= "'";
	$texte .= " width='$graphe_largeurTotale' height='$graphe_hauteurTotale'";
	$texte .= " type=\"image/svg+xml\"></object></div>\n";

	$tabdiv_infobulle[] = creer_div_infobulle('repartition_notes_moyenne', $titre, "", $texte, "", 14, 0, 'y', 'y', 'n', 'n');
}

// Dernière ligne

echo "<tr>";
if ($multiclasses) {
	echo "<td class=cn colspan=2>";
} else {
	echo "<td class=cn>";
}

echo "<input type='hidden' name='indice_max_log_eleve' value='$i' />\n";
$indice_max_log_eleve = $i;

$w_pdf[] = $w2;
if ($id_devoir == 0) {
	$data_pdf[$tot_data_pdf][] = "Moyennes";
	echo "<b>Moyennes :</b></td>\n";
} else {
	$data_pdf[$tot_data_pdf][] = "Moyenne";
	echo "<b>Moyenne :</b></td>\n";
}

if ($multiclasses) {
	$data_pdf[$tot_data_pdf][] = "";
}
$k = '0';
while ($k < $nb_dev) {
	// En mode saisie, on n'affiche que le devoir à saisir
	if (($id_devoir == 0) or ($id_dev[$k] == $id_devoir)) {
		$call_moyenne = mysqli_query($GLOBALS["mysqli"], "SELECT round(avg(n.note),1) moyenne FROM cn_notes_devoirs n, j_eleves_groupes j WHERE (
		j.id_groupe='$id_groupe' AND
		j.periode = '$periode_num' AND
		j.login = n.login AND
		n.statut='' AND
		n.id_devoir='$id_dev[$k]'
		)");
		$obj_moy_courante = $call_moyenne->fetch_object();
		$moyenne[$k] = $obj_moy_courante->moyenne;
		if ($moyenne[$k] != '') {
			echo "<td class='cn'><center><b>" . number_format($moyenne[$k], 1, ',', ' ') . "</b></center></td>\n";
			$data_pdf[$tot_data_pdf][] = number_format($moyenne[$k], 1, ',', ' ');

		} else {
			echo "<td class='cn'>&nbsp;</td></td>\n";
			$data_pdf[$tot_data_pdf][] = "";
		}
		if ((($nocomment[$k] != 'yes') and ($_SESSION['affiche_comment'] == 'yes')) or ($id_dev[$k] == $id_devoir)) {
			echo "<td class='cn'>&nbsp;</td>\n";
			$data_pdf[$tot_data_pdf][] = "";
		}
	}
	$k++;
}

// Moyenne des moyennes des sous-conteneurs
//
// on affiche les sous-conteneurs et les devoirs des sous-conteneurs si on n'est pas en mode saisie ($id_devoir == 0)
if ($id_devoir == 0) {
	$k = 0;
	while ($k < $nb_sous_cont) {
		if ($_SESSION['affiche_tous'] == 'yes') {
			$m = 0;
			while ($m < $nb_dev_s_cont[$k]) {
				$temp = $id_s_dev[$k][$m];
				$call_moy = mysqli_query($GLOBALS["mysqli"], "SELECT round(avg(n.note),1) moyenne FROM cn_notes_devoirs n, j_eleves_groupes j WHERE (
				j.id_groupe='$id_groupe' AND
				j.periode = '$periode_num' AND
				j.login = n.login AND
				n.statut='' AND
				n.id_devoir='$temp'
				)");
				$obj_moy = $call_moy->fetch_object();
				$moy_s_dev = $obj_moy->moyenne;
				if ($moy_s_dev != '') {
					echo "<td class='cn'><center><b>" . number_format($moy_s_dev, 1, ',', ' ') . "</b></center></td>\n";
					$data_pdf[$tot_data_pdf][] = number_format($moy_s_dev, 1, ',', ' ');
				} else {
					echo "<td class='cn'>&nbsp;</td>\n";
					$data_pdf[$tot_data_pdf][] = "";
				}
				$m++;
			}
		}
		$call_moy_moy = mysqli_query($GLOBALS["mysqli"], "SELECT round(avg(n.note),1) moyenne FROM cn_notes_conteneurs n, j_eleves_groupes j WHERE (
		j.id_groupe='$id_groupe' AND
		j.login = n.login AND
		j.periode = '$periode_num' AND
		n.statut='y' AND
		n.id_conteneur='$id_sous_cont[$k]'
		)");
		$obj_moy_moy = $call_moy_moy->fetch_object();
		$moy_moy = $obj_moy_moy->moyenne;
		if ($moy_moy != '') {
			echo "<td class='cn'><center><b>" . number_format($moy_moy, 1, ',', ' ') . "</b></center></td>\n";
			$data_pdf[$tot_data_pdf][] = number_format($moy_moy, 1, ',', ' ');
		} else {
			echo "<td class='cn'>&nbsp;</td>\n";
			$data_pdf[$tot_data_pdf][] = "";
		}
		$k++;
	}
}

// Moyenne des moyennes du conteneur
//
// on affiche les sous-conteneurs et les devoirs des sous-conteneurs si on n'est pas en mode saisie ($id_devoir == 0)
if ($id_devoir == 0) {

	$call_moy_moy = mysqli_query($GLOBALS["mysqli"], "SELECT round(avg(n.note),1) moyenne FROM cn_notes_conteneurs n, j_eleves_groupes j WHERE (
	j.id_groupe='$id_groupe' AND
	j.login = n.login AND
	j.periode = '$periode_num' AND
	n.statut='y' AND
	n.id_conteneur='$id_conteneur'
	)");
	$obj_moy_moy = $call_moy_moy->fetch_object();
	$moy_moy = $obj_moy_moy->moyenne;
	if ($moy_moy != '') {
		echo "<td class='cn'><center><b>" . number_format($moy_moy, 1, ',', ' ') . "</b></center></td>\n";
		$data_pdf[$tot_data_pdf][] = number_format($moy_moy, 1, ',', ' ');
	} else {
		echo "<td class='cn'>&nbsp;</td>\n";
		$data_pdf[$tot_data_pdf][] = "";
	}
}
echo "</tr>\n";

//echo "<pre>".print_r($tab_ele_notes)."</pre>";
$indice_premiere_col_note = 1;
if ($multiclasses) {
	$indice_premiere_col_note = 2;
}
$tab_col_note = array();
for ($i = 0; $i < count($tab_ele_notes); $i++) {
	for ($j = 0; $j < count($tab_ele_notes[$i]); $j++) {
		$tab_col_note[$j][$i] = $tab_ele_notes[$i][$j];
	}
}
/*
echo "<div style='float:left;width:300px;'>";
echo "\$note_sur<pre>";
print_r($note_sur);
echo "</pre>";
echo "</div>";

echo "<div style='float:left;width:300px;'>";
echo "\$tab_col_note<pre>";
print_r($tab_col_note);
echo "</pre>";
echo "</div>";

echo "<div style='float:left;width:300px;'>";
echo "\$tab_note_sur<pre>";
print_r($tab_note_sur);
echo "</pre>";
echo "</div>";

echo "<div style='clear:both;'></div>";
*/
$tmp_tab_note_sur = array();
for ($i = 0; $i < count($tab_col_note); $i++) {
	// 20160220
	if (isset($tab_note_sur[$i])) {
		$tab_m[$i] = calcule_moy_mediane_quartiles($tab_col_note[$i], $tab_note_sur[$i]);
		if (!in_array($tab_note_sur[$i], $tmp_tab_note_sur)) {
			$tmp_tab_note_sur[] = $tab_note_sur[$i];
		}
	} else {
		$tab_m[$i] = calcule_moy_mediane_quartiles($tab_col_note[$i]);
	}
}

if (getPref($_SESSION['login'], 'cn_avec_min_max', 'y') == 'y') {
	$tot_data_pdf++;
	$data_pdf[$tot_data_pdf][] = 'Min.:';
	echo "<tr>\n";
	echo "<td class='cn bold'><b>Min.&nbsp;:</b></td>\n";
	if ($multiclasses) {
		echo "<td class='cn bold'>&nbsp;</td>\n";
		$data_pdf[$tot_data_pdf][] = '';
	}
	for ($i = $indice_premiere_col_note; $i < count($tab_m); $i++) {
		echo "<td class='cn bold'>" . $tab_m[$i]['min'] . "</td>\n";
		$data_pdf[$tot_data_pdf][] = $tab_m[$i]['min'];
	}
	echo "</tr>\n";

	$tot_data_pdf++;
	$data_pdf[$tot_data_pdf][] = 'Max.:';
	echo "<tr>\n";
	echo "<td class='cn bold'><b>Max.&nbsp;:</b></td>\n";
	if ($multiclasses) {
		echo "<td class='cn bold'>&nbsp;</td>\n";
		$data_pdf[$tot_data_pdf][] = '';
	}
	for ($i = $indice_premiere_col_note; $i < count($tab_m); $i++) {
		echo "<td class='cn bold'>" . $tab_m[$i]['max'] . "</td>\n";
		$data_pdf[$tot_data_pdf][] = $tab_m[$i]['max'];
	}
	echo "</tr>\n";
}

if (getPref($_SESSION['login'], 'cn_avec_mediane_q1_q3', 'y') == 'y') {
	$tot_data_pdf++;
	echo "<tr>\n";
	if ($id_devoir == 0) {
		$data_pdf[$tot_data_pdf][] = 'Médianes :';
		echo "<td class='cn bold'><b>Médianes&nbsp;:</b></td>\n";
	} else {
		$data_pdf[$tot_data_pdf][] = 'Médiane :';
		echo "<td class='cn bold'><b>Médiane&nbsp;:</b></td>\n";
	}
	if ($multiclasses) {
		echo "<td class='cn bold'>&nbsp;</td>\n";
		$data_pdf[$tot_data_pdf][] = '';
	}
	for ($i = $indice_premiere_col_note; $i < count($tab_m); $i++) {
		echo "<td class='cn bold'>" . $tab_m[$i]['mediane'] . "</td>\n";
		$data_pdf[$tot_data_pdf][] = $tab_m[$i]['mediane'];
	}
	echo "</tr>\n";

	$tot_data_pdf++;
	$data_pdf[$tot_data_pdf][] = '1er quartile :';
	echo "<tr>\n";
	echo "<td class='cn bold'><b>1er quartile&nbsp;:</b></td>\n";
	if ($multiclasses) {
		echo "<td class='cn bold'>&nbsp;</td>\n";
		$data_pdf[$tot_data_pdf][] = '';
	}
	for ($i = $indice_premiere_col_note; $i < count($tab_m); $i++) {
		echo "<td class='cn bold'>" . $tab_m[$i]['q1'] . "</td>\n";
		$data_pdf[$tot_data_pdf][] = $tab_m[$i]['q1'];
	}
	echo "</tr>\n";

	$tot_data_pdf++;
	$data_pdf[$tot_data_pdf][] = '3è quartile :';
	echo "<tr>\n";
	echo "<td class='cn bold'><b>3è quartile&nbsp;:</b></td>\n";
	if ($multiclasses) {
		echo "<td class='cn bold'>&nbsp;</td>\n";
		$data_pdf[$tot_data_pdf][] = '';
	}
	for ($i = $indice_premiere_col_note; $i < count($tab_m); $i++) {
		echo "<td class='cn bold'>" . $tab_m[$i]['q3'] . "</td>\n";
		$data_pdf[$tot_data_pdf][] = $tab_m[$i]['q3'];
	}
	echo "</tr>\n";
}

$temoin_remarque_nb_notes_sup_10_autre_referentiel = 0;
if (getPref($_SESSION['login'], 'cn_avec_sup10', 'y') == 'y') {
	$tot_data_pdf++;
	echo "<tr>\n";

	if (count($tab_note_sur) == 1) {
		$data_pdf[$tot_data_pdf][] = 'Nb.notes≥' . ($tmp_tab_note_sur[0] / 2) . ' :';
		echo "<td class='cn bold'><b>Nb.notes&ge;" . ($tmp_tab_note_sur[0] / 2) . "&nbsp;:</b></td>\n";
	} elseif (count($tmp_tab_note_sur) > 1) {
		$data_pdf[$tot_data_pdf][] = 'Nb.notes≥10 (*) :';
		echo "<td class='cn bold' title=\"Nombre de notes supérieures ou égales à la note moitié du référentiel.\nCe sera le plus souvent 10, mais pour un référentiel sur 30, on comptera les notes supérieures ou égales à 15.\"><b>Nb.notes&ge;10 (**)&nbsp;:</b></td>\n";
		$temoin_remarque_nb_notes_sup_10_autre_referentiel++;
	} else {
		$data_pdf[$tot_data_pdf][] = 'Nb.notes≥10 :';
		echo "<td class='cn bold'><b>Nb.notes&ge;10&nbsp;:</b></td>\n";
	}
	if ($multiclasses) {
		echo "<td class='cn bold'>&nbsp;</td>\n";
		$data_pdf[$tot_data_pdf][] = '';
	}

	for ($i = $indice_premiere_col_note; $i < count($tab_m); $i++) {
		echo "<td class='cn bold'>" . $tab_m[$i]['supegal10'] . "</td>\n";
		$data_pdf[$tot_data_pdf][] = $tab_m[$i]['supegal10'];
	}
	echo "</tr>\n";

	$tot_data_pdf++;
	echo "<tr>\n";
	if (count($tab_note_sur) == 1) {
		$data_pdf[$tot_data_pdf][] = 'Nb.notes<' . ($tmp_tab_note_sur[0] / 2) . ' :';
		echo "<td class='cn bold'><b>Nb.notes&lt;" . ($tmp_tab_note_sur[0] / 2) . "&nbsp;:</b></td>\n";
	} elseif (count($tmp_tab_note_sur) > 1) {
		$data_pdf[$tot_data_pdf][] = 'Nb.notes<10 (*) :';
		echo "<td class='cn bold' title=\"Nombre de notes inférieures à la note moitié du référentiel.\nCe sera le plus souvent 10, mais pour un référentiel sur 30, on comptera les notes inférieures à 15.\"><b>Nb.notes&lt;10 (**)&nbsp;:</b></td>\n";
		$temoin_remarque_nb_notes_sup_10_autre_referentiel++;
	} else {
		$data_pdf[$tot_data_pdf][] = 'Nb.notes<10 :';
		echo "<td class='cn bold'><b>Nb.notes&lt;10&nbsp;:</b></td>\n";
	}
	if ($multiclasses) {
		echo "<td class='cn bold'>&nbsp;</td>\n";
		$data_pdf[$tot_data_pdf][] = '';
	}
	for ($i = $indice_premiere_col_note; $i < count($tab_m); $i++) {
		echo "<td class='cn bold'>" . $tab_m[$i]['inf10'] . "</td>\n";
		$data_pdf[$tot_data_pdf][] = $tab_m[$i]['inf10'];
	}
	echo "</tr>\n";
}
echo "</table>\n";

$cn_mode_saisie = getPref($_SESSION['login'], 'cn_mode_saisie', 0);
echo "<input type='hidden' name='cn_mode_saisie' id='cn_mode_saisie' value='" . $cn_mode_saisie . "' />";

if ((isset($id_devoir)) && ($id_devoir != 0)) {
	echo "<div id='div_q_p' style='position: fixed; top: 220px; right: 200px; text-align:center;'>\n";
	echo "<div id='div_quartiles' style='text-align:center; display:none;'>\n";

	// 20210502 : DEBUG
	/*
	if(isset($note_sur)) {
		if(is_array($note_sur)) {
			echo "\$note_sur= array()<br />";
			echo "<pre>";
			print_r($note_sur);
			echo "</pre>";
		}
		else {
			echo "\$note_sur=$note_sur<br />";
		}
	}
	if(isset($note_sur_dev_choisi)) {
		echo "\$note_sur_dev_choisi=$note_sur_dev_choisi<br />";
	}
	*/

	javascript_tab_stat('tab_stat_', $num_id);
	echo "</div>\n";

	echo "<br />\n";

	echo "<div id='div_photo_eleve' style='text-align:center; display:none;'></div>\n";
	echo "</div>\n";

	echo "<script type='text/javascript'>
	function affiche_div_photo() {
		if(document.getElementById('div_photo_eleve').style.display=='none') {
			document.getElementById('div_photo_eleve').style.display='';
		}
		else {
			document.getElementById('div_photo_eleve').style.display='none';
		}
	}

	function affiche_photo(photo,nom_prenom) {
 		document.getElementById('div_photo_eleve').innerHTML='<img src=\"'+photo+'\" width=\"150\" alt=\"Photo\" /><br />'+nom_prenom;
	}

	function vider_commentaires() {
		if(confirm('Êtes-vous sûr de vouloir vider les commentaires ?')) {
			for(i=110;i<" . (100 + $num_id) . ";i++) {
				if(document.getElementById('n'+i)) {
					document.getElementById('n'+i).value='';
				}
			}
		}
	}
</script>\n";
}
//===================================

echo "
<script type='text/javascript'>
	function change_visibilite_dev(id_dev, visible) {

		new Ajax.Updater($('span_visibilite_'+id_dev),'index.php?id_groupe=$id_groupe&id_racine=$id_racine&id_dev='+id_dev+'&mode=change_visibilite_dev&visible='+visible+'&mode_js=y&" . add_token_in_url(false) . "',{method: 'get'});

	}
</script>";

// Préparation du pdf
$header_pdf = serialize($header_pdf);
$_SESSION['header_pdf'] = $header_pdf;

$w_pdf = serialize($w_pdf);
$_SESSION['w_pdf'] = $w_pdf;

$data_pdf = serialize($data_pdf);
$_SESSION['data_pdf'] = $data_pdf;

if ($id_devoir) echo "<input type='hidden' name='is_posted' value=\"yes\" />\n";

?>

<input type="hidden" name="id_conteneur" value="<?php echo "$id_conteneur"; ?>"/>
<input type="hidden" name="id_devoir" value="<?php echo "$id_devoir"; ?>"/>
<?php
if ($id_devoir != 0) {
	if ((isset($current_group['eleves'][$periode_num]['list'])) && (count($current_group['eleves'][$periode_num]['list']) > 0)) {
		echo "<br /><center><div id=\"fixe\"><input type='submit' value='Enregistrer' /></div></center>\n";
	}
}
?>
</form>
<?php

// Affichage des quartiles flottants

if ((!isset($id_devoir)) || ($id_devoir == '') || ($id_devoir == '0')) {
	echo "<form action='" . $_SERVER['PHP_SELF'] . "' method='post' name='form_calcul_mediane'>\n";
	echo $chaine_input_moy;
	echo "</form>\n";

	echo "<div id='div_q_p' style='position: fixed; top: 220px; right: 200px; text-align:center;'>\n";
	echo "<div id='div_quartiles' style='text-align:center; display:none;'>\n";


	// 20210502 : DEBUG
	/*
	echo "id_devoir=$id_devoir<br />";
	if(isset($note_sur)) {
		if(is_array($note_sur)) {
			echo "\$note_sur= array()<br />";
			echo "<pre>";
			print_r($note_sur);
			echo "</pre>";
		}
		else {
			echo "\$note_sur=$note_sur<br />";
		}
	}
	if(isset($note_sur_dev_choisi)) {
		echo "\$note_sur_dev_choisi=$note_sur_dev_choisi<br />";
	}
	*/

	javascript_tab_stat('tab_stat_', $nombre_lignes);

	echo "</div>\n";
	echo "</div>\n";
}

if ($id_devoir) {

	$chaine_indices = "";
	for ($i = 0; $i < $nombre_lignes; $i++) {
		if (isset($indice_ele_saisie[$i])) {
			if ($chaine_indices != "") {
				$chaine_indices .= ",";
			}
			$chaine_indices .= $indice_ele_saisie[$i];
		}
	}

	//echo "<p id='p_tri'></p>\n";
	echo "<p id='p_liens_javascript' style='display:none;'><span id='p_tri'></span>\n";
	echo "<script type='text/javascript'>
	function affiche_lien_tri() {
		const tab_indices = new Array($chaine_indices);

		let chaine1 = '';
		let chaine2 = '';
		for(let i = 0; i < $nombre_lignes; i++) {
			//num=eval(10+i);
			let num = tab_indices[i];

			if(document.getElementById('n'+num)) {
				if(chaine1 !== '') {
					chaine1=chaine1+'|';
					chaine2=chaine2+'|';
				}

				chaine1 = chaine1+document.getElementById('log_eleve_'+i).value;
				chaine2 = chaine2+document.getElementById('n'+num).value;
			}
		}
		
		document.getElementById('p_tri').innerHTML='<a href=\'affiche_tri.php?titre=Notes&chaine1='+chaine1+'&chaine2='+chaine2+'\' onclick=\"effectuer_tri(); afficher_div(\'div_tri\',\'y\',-150,20); return false;\" target=\'_blank\'>Afficher les notes triées</a>';
	}

	function effectuer_tri() {
		let tab_indices = [$chaine_indices]

		let chaine1 = '';
		let chaine2 = '';
		for(let i = 0; i < $nombre_lignes; i++) {
			let num = tab_indices[i];

			if(document.getElementById('n'+num)) {
				if(chaine1 !== '') {
					chaine1 = chaine1+'|';
					chaine2 = chaine2+'|';
				}

				chaine1=chaine1+document.getElementById('log_eleve_'+i).value;
				chaine2=chaine2+document.getElementById('n'+num).value;
			}
		}

		new Ajax.Updater($('notes_triees'),'affiche_tri.php?titre=Notes&chaine1='+chaine1+'&chaine2='+chaine2+'" . add_token_in_url(false) . "',{method: 'get'});
	}

	affiche_lien_tri();

	// On affiche le paragraphe avec les liens javascript via javascript pour ne pas les proposer si javascript est désactivé:
	document.getElementById('p_liens_javascript').style.display='';
</script>\n";
	$titre_infobulle = "Notes triées";
	$texte_infobulle = "<div id='notes_triees'></div>";
	$tabdiv_infobulle[] = creer_div_infobulle('div_tri', $titre_infobulle, "", $texte_infobulle, "", 30, 0, 'y', 'y', 'n', 'n');

	//=====================================================

	echo " - ";

	//=====================================================
	// Ramener une note sur 20 (ou autre)
	$cn_precision = getPref($_SESSION['login'], 'cn_precision', 's5');

	$titre_infobulle = "Ramener sur N";
	$texte_infobulle = "<p>Vous avez des notes sur 37 ou un autre nombre pas très parlant pour les élèves et les parents et vous souhaitez le ramener sur 20 (<em>ou autre</em>) pour plus d'accessibilité dans le carnet de notes.</p>
<div align='center'>
<table class='boireaus'>
<tr class='lig1'><td>Total du barême&nbsp;</td><td><input type='text' name='total_bareme' id='total_bareme' value='30' size='3' onkeydown='clavier_2(this.id,event,1,100);' autocomplete='off' /></td><td></td></tr>
<tr class='lig-1'><td>Ramener sur&nbsp;</td><td><input type='text' name='ramener_sur_N' id='ramener_sur_N' value='20' size='3' onkeydown='clavier_2(this.id,event,1,100);' autocomplete='off' /></td><td></td></tr>
<tr class='lig1'><td rowspan='6'>Arrondir&nbsp;</td><td><input type='radio' name='precision' id='precision_s1' value='s1' ";
	if ($cn_precision == 's1') {
		$texte_infobulle .= "checked ";
	}
	$texte_infobulle .= "/></td><td><label for='precision_s1' style='cursor: pointer;'>au dixième de point supérieur</label></td></tr>
<tr class='lig-1'><td><input type='radio' name='precision' id='precision_s5' value='s5' ";
	if ($cn_precision == 's5') {
		$texte_infobulle .= "checked ";
	}
	$texte_infobulle .= "/></td><td><label for='precision_s5' style='cursor: pointer;'>au demi-point supérieur</label></td></tr>
<tr class='lig1'><td><input type='radio' name='precision' id='precision_se' value='se' ";
	if ($cn_precision == 'se') {
		$texte_infobulle .= "checked ";
	}
	$texte_infobulle .= "/></td><td><label for='precision_se' style='cursor: pointer;'>au point entier supérieur</label></td></tr>
<tr class='lig-1'><td><input type='radio' name='precision' id='precision_p1' value='p1' ";
	if ($cn_precision == 'p1') {
		$texte_infobulle .= "checked ";
	}
	$texte_infobulle .= "/></td><td><label for='precision_p1' style='cursor: pointer;'>au dixième de point le plus proche</label></td></tr>
<tr class='lig1'><td><input type='radio' name='precision' id='precision_p5' value='p5' ";
	if ($cn_precision == 'p5') {
		$texte_infobulle .= "checked ";
	}
	$texte_infobulle .= "/></td><td><label for='precision_p5' style='cursor: pointer;'>au demi-point le plus proche</label></td></tr>
<tr class='lig-1'><td><input type='radio' name='precision' id='precision_pe' value='pe' ";
	if ($cn_precision == 'pe') {
		$texte_infobulle .= "checked ";
	}
	$texte_infobulle .= "/></td><td><label for='precision_pe' style='cursor: pointer;'>au point entier le plus proche</label></td></tr>
</table>
<p><input type='button' name='valider_ramener_sur_N' value='Valider' onclick='effectuer_ramener_sur_N()' /></p>
<!--20150424-->
<p>L'évaluation est actuellement <strong>configurée/notée sur " . $note_sur[$indice_devoir_saisi] . "</strong><br />
<input type='checkbox' name='modif_eval_ramener_sur' id='modif_eval_ramener_sur' value='y' onchange=\"checkbox_change(this.id)\" /><label for='modif_eval_ramener_sur' id='texte_modif_eval_ramener_sur'>Modifier cette valeur pour prendre la valeur de <strong>ramener_sur</strong> ci_dessus</label></p>
</div>";
	$tabdiv_infobulle[] = creer_div_infobulle('div_ramener_sur_N', $titre_infobulle, "", $texte_infobulle, "", 32, 0, 'y', 'y', 'n', 'n');
	echo "<span id='p_ramener_sur_N2' style='display:none'><a href='#' onclick=\"afficher_div('div_ramener_sur_N','y',20,20); return false;\" target=\'_blank\'>Ramener sur N</a></span>";

	//=====================================================
	// Facilité de saisie
	//$cn_mode_saisie=getPref($_SESSION['login'], 'cn_mode_saisie', 0);

	$titre_infobulle = "Facilités de saisie";
	$texte_infobulle = "<p>Vous pouvez choisir parmi plusieurs modes de saisie des notes&nbsp;:</p>

<p style='text-indent:-2em; margin-left:2em;'>
	<input type='radio' name='cn_mode_saisie' id='cn_mode_saisie_0' value='0' onchange=\"changement(); checkbox_change('cn_mode_saisie_0');checkbox_change('cn_mode_saisie_1');checkbox_change('cn_mode_saisie_2');\" " . ($cn_mode_saisie == 0 ? "checked " : "") . "/>
	<label for='cn_mode_saisie_0' id='texte_cn_mode_saisie_0'" . ($cn_mode_saisie == 0 ? " style='font-weight:bold'" : "") . ">On passe au champ suivant <em>(en dessous)</em> en pressant la flèche vers le bas.</label>
</p>

<p style='text-indent:-2em; margin-left:2em;'>
	<input type='radio' name='cn_mode_saisie' id='cn_mode_saisie_1' value='1' onchange=\"changement(); checkbox_change('cn_mode_saisie_0');checkbox_change('cn_mode_saisie_1');checkbox_change('cn_mode_saisie_2');\" " . ($cn_mode_saisie == 1 ? "checked " : "") . "/>
	<label for='cn_mode_saisie_1' id='texte_cn_mode_saisie_1'" . ($cn_mode_saisie == 1 ? " style='font-weight:bold'" : "") . ">On passe au champ suivant <em>(en dessous)</em> en pressant la flèche vers le bas ou en tapant un nombre à un chiffre après la virgule.<br />
	<em>Exemple&nbsp;:</em> Si vous tapez <strong>12.5</strong> ou <strong>8.0</strong>, vous passerez à la note suivante.</label>
</p>
<p style='text-indent:-2em; margin-left:2em;'>
	<input type='radio' name='cn_mode_saisie' id='cn_mode_saisie_2' value='2' onchange=\"changement(); checkbox_change('cn_mode_saisie_0');checkbox_change('cn_mode_saisie_1');checkbox_change('cn_mode_saisie_2');\" " . ($cn_mode_saisie == 2 ? "checked " : "") . "/>
	<label for='cn_mode_saisie_2' id='texte_cn_mode_saisie_2'" . ($cn_mode_saisie == 2 ? " style='font-weight:bold'" : "") . ">On passe au champ suivant <em>(en dessous)</em> en pressant la flèche vers le bas ou en tapant un nombre à trois chiffres pour le nombre de dixièmes de la note.<br />
	<em>Exemple&nbsp;:</em> Pour 12.5, vous pouvez taper <strong>125</strong>, la note sera corrigée en 12.5 et le pointeur passera à la note suivante.<br />
	Pour 8/20, il faudra taper <strong>080</strong></label>
<p>
<p><input type='button' name='valider_choix_mode_saisie' value='Valider' onclick='effectuer_choix_mode_saisie()' /></p>

<p style='margin-top:1em; text-indent:-3.8em; margin-left:3.8em;'><em>NOTE&nbsp;:</em> Dans tous les cas, on passe à la note suivante <em>(en dessous)</em> ou précédente en pressant les flèches Bas/Haut du pavé de direction.<br />
	Et on passe à la note suivante lorsqu'on presse <strong>a</strong> pour absent, <strong>d</strong> pour dispensé ou <strong>-</strong> pour non noté.</p>";

	$tabdiv_infobulle[] = creer_div_infobulle('div_facilite_saisie', $titre_infobulle, "", $texte_infobulle, "", 45, 0, 'y', 'y', 'n', 'n');

	echo "<span id='p_facilite_saisie_2' style='display:none'> - <a href='#' onclick=\"afficher_div('div_facilite_saisie','y',20,20); return false;\" target=\'_blank\'>Modes de saisie <img src='../images/icons/wizard.png' class='icone16' alt='Facilité de saisie' /></a></span>";

	//=====================================================

	echo "<script type='text/javascript'>
	" . js_checkbox_change_style() . "

	function effectuer_choix_mode_saisie() {
		if(document.getElementById('cn_mode_saisie')) {
			if(document.getElementById('cn_mode_saisie_0').checked) {
				document.getElementById('cn_mode_saisie').value=0;
			}
			if(document.getElementById('cn_mode_saisie_1').checked) {
				document.getElementById('cn_mode_saisie').value=1;
			}
			if(document.getElementById('cn_mode_saisie_2').checked) {
				document.getElementById('cn_mode_saisie').value=2;
			}
		}
		cacher_div('div_facilite_saisie');
	}

	function effectuer_ramener_sur_N() {
		if((document.getElementById('ramener_sur_N')) && (document.getElementById('ramener_sur_N').value !== '')&&(document.getElementById('total_bareme'))&&(document.getElementById('total_bareme').value!='')) {

			let precision;
			let ramener_sur_N = document.getElementById('ramener_sur_N').value;
			let total_bareme = document.getElementById('total_bareme').value;

			ramener_sur_N=ramener_sur_N.replace(',', '.');
			total_bareme=total_bareme.replace(',', '.');

			//precision=document.getElementById('precision').value;
			if(document.getElementById('precision_s1').checked==true) {
				precision='s1'
			} else {
				if(document.getElementById('precision_s5').checked === true) {
					precision='s5'
				} else {
					if(document.getElementById('precision_se').checked === true) {
						precision='se'
					} else {
						if(document.getElementById('precision_p1').checked === true) {
							precision='p1'
						} else {
							if(document.getElementById('precision_p5').checked === true) {
								precision='p5'
							} else {
								if(document.getElementById('precision_pe').checked===true) {
									precision='pe'
								} else {
									precision='p5'
								}
							}
						}
					}
				}
			}

			if(document.getElementById('cn_precision')) {
				document.getElementById('cn_precision').value=precision;
			}

			if((((total_bareme.search(/^[0-9.]+$/)!==-1)&&(total_bareme.lastIndexOf('.')===total_bareme.indexOf('.',0)))||
			((total_bareme.search(/^[0-9,]+$/)!==-1)&&(total_bareme.lastIndexOf(',')===total_bareme.indexOf(',',0))))&&
			(((ramener_sur_N.search(/^[0-9.]+$/)!==-1)&&(ramener_sur_N.lastIndexOf('.')===ramener_sur_N.indexOf('.',0)))||
			((ramener_sur_N.search(/^[0-9,]+$/)!==-1)&&(ramener_sur_N.lastIndexOf(',')===ramener_sur_N.indexOf(',',0))))) {
				let tab_indices = [$chaine_indices]
				for(let i = 0; i < $nombre_lignes; i++) {
					let num = tab_indices[i];
					if(document.getElementById('n'+num)) {
						if(document.getElementById('n'+num).value !== '') {
							let note = document.getElementById('n'+num).value;
							note = note.replace(',', '.');

							if(((note.search(/^[0-9.]+$/) !== -1) && (note.lastIndexOf('.') === note.indexOf('.',0)))||
							((note.search(/^[0-9,]+$/) !== -1) && (note.lastIndexOf(',') === note.indexOf(',',0)))){
								note_modifiee=note*ramener_sur_N/total_bareme;
								//document.getElementById('n'+num).value=note_modifiee;

								if(precision === 'p5') {
									document.getElementById('n'+num).value=Math.round(2*note_modifiee)/2;
								} else {
									if(precision === 'p1') {
										document.getElementById('n'+num).value=Math.round(10*note_modifiee)/10;
									} else {
										if(precision === 'pe') {
											document.getElementById('n'+num).value=Math.round(note_modifiee);
										} else {
											if(precision === 's5') {
												document.getElementById('n'+num).value=Math.ceil(2*note_modifiee)/2;
											} else {
												if(precision === 's1') {
													document.getElementById('n'+num).value=Math.ceil(10*note_modifiee)/10;
												} else {
													if(precision === 'se') {
														document.getElementById('n'+num).value=Math.ceil(note_modifiee);
													} else {
														document.getElementById('n'+num).value=note_modifiee;
													}
												}
											}
										}
									}
								}

								if((note_modifiee>" . $note_sur_verif . ") || (note_modifiee<0)){
									couleur = 'red';
								} else{
									couleur = '$couleur_devoirs';
								}
								eval('document.getElementById(\'td_'+num+'\').style.background=couleur');

								if(document.getElementById('n1'+num)) {
									if(document.getElementById('n1'+num).value !== '') {
										document.getElementById('n1'+num).value = document.getElementById('n1'+num).value + ' ('+note+'/'+total_bareme+')';
									} else {
										document.getElementById('n1'+num).value = '('+note+'/'+total_bareme+')';
									}
								}
							}
						}
					}
				}

				calcul_moy_med();

				if(document.getElementById('modif_eval_ramener_sur').checked === true) {
					document.getElementById('modif_note_sur').value=ramener_sur_N;
					document.getElementById('form2').submit();
				} else {
					alert('Opération terminée.');
				}
				cacher_div('div_ramener_sur_N');
			} else {
				alert('Valeur proposée invalide.');
			}
		} else {
			alert('Valeur proposée invalide.');
		}
	}

	document.getElementById('p_ramener_sur_N').style.display='';
	document.getElementById('p_ramener_sur_N2').style.display='';
	document.getElementById('p_facilite_saisie_2').style.display='';
	
	function recopier_notes_vers_textarea() {
		if(document.getElementById('textarea_notes')) {
			//document.getElementById('textarea_notes').value='';
			liste_notes='';

			var arr = document.getElementsByTagName('input');
			for(j=0;j<$indice_max_log_eleve;j++) {
				if(j>0) {liste_notes=liste_notes+'\\n';}

				for(var i = 0; i < arr.length; i++) {
					if(arr[i].name == 'note_eleve['+j+']') {
						note_eleve=arr[i].value;

						liste_notes=liste_notes+note_eleve;
					}
				}
			}

			document.getElementById('textarea_notes').value=liste_notes;
		}
	}
	
	// 20120509
	function modif_note(num, delta) {
		let reg_virgule=new RegExp('[,]', 'g');

		if(document.getElementById('n'+num)) {
			let note = document.getElementById('n'+num).value;
			if(((note.search(/^[0-9.]+$/) !== -1)&&(note.lastIndexOf('.') === note.indexOf('.',0)))||
			((note.search(/^[0-9,]+$/) !== -1)&&(note.lastIndexOf(',') === note.indexOf(',',0)))){
				let note_modifiee = eval(note.replace(reg_virgule, '.'))+eval(delta);
				if((note_modifiee >= 0)&&(note_modifiee <= $note_sur_verif)) {
					document.getElementById('n'+num).value = note_modifiee;
					changement();
				}
			}
		}
	}

	function affichage_modif_note() {
		for(let i = 0; i < $max_indice_eleve; i++) {
			if(document.getElementById('modif_note_'+i)) {
				document.getElementById('modif_note_'+i).style.display='';
			}
		}
	}
</script>\n";
	//=====================================================

	echo " - ";

	//=====================================================

	echo "<a href='javascript:recopier_notes_vers_textarea()'>Recopier les notes vers le Textarea ci-dessous</a>";

	echo "</p>\n";
	//=====================================================


	echo "<fieldset style=\"padding-top: 8px; padding-bottom: 8px;  margin-left: 8px; margin-right: 100px;\">\n";
	echo "<form enctype=\"multipart/form-data\" action=\"saisie_notes.php\" method='post' id='form_import_notes'>\n";
	echo add_token_field();
	echo "<a name='import_notes_tableur'></a>";
	echo "<h3 class='gepi'>Importation directe des notes par copier/coller à partir d'un tableur</h3>\n";
	echo "<table summary=\"Tableau d'import\"><tr>\n";
	echo "<td>De la ligne : ";
	echo "<SELECT name='debut_import' size='1'>\n";
	$k = 1;
	while ($k < $current_displayed_line + 1) {
		echo "<option value='$k'>$k</option>\n";
		$k++;
	}
	echo "</select>\n";

	echo "<br /> à la ligne : \n";
	echo "<SELECT name='fin_import' size='1'>\n";
	$k = 1;
	while ($k < $current_displayed_line + 1) {
		echo "<option value='$k'";
		if ($k == $current_displayed_line) echo " SELECTED ";
		echo ">$k</option>\n";
		$k++;
	}
	echo "</select>\n";
	echo "</td><td>\n";
	echo "Coller ci-dessous les données à importer : <br />\n";
	if (isset($_POST['notes'])) {
		$notes = preg_replace("/\\\\n/", "\n", preg_replace("/\\\\r/", "\r", $_POST['notes']));
	} else {
		$notes = '';
	}
	//echo "<textarea name='notes' rows='3' cols='40' wrap='virtual'>$notes</textarea>\n";
	echo "<textarea name='notes' id='textarea_notes' rows='3' cols='40' class='wrap'>$notes</textarea>\n";
	echo "</td></tr></table>\n";

	echo "<input type='hidden' name='id_conteneur' value='$id_conteneur' />\n";
	echo "<input type='hidden' name='id_devoir' value='$id_devoir' />\n";

	echo "<input type='hidden' name='order_by' value='$order_by' />\n";

	echo "<center><input type='submit' value='Importer'  onclick=\"return confirm_abandon (this, change, '$themessage')\" /></center>\n";
	echo "<p><b>Remarque importante :</b> l'importation ne prend en compte que les élèves dont le nom est affiché ci-dessus !<br />Soyez donc vigilant à ne coller que les notes de ces élèves, dans le bon ordre.</p>\n";
	echo "</form></fieldset>\n";

	if (isset($_POST['notes'])) {
		echo "<script type=\"text/javascript\" language=\"javascript\">
		<!--
		alert(\"Attention, les notes importées ne sont pas encore enregistrées dans la base GEPI. Vous devez confirmer l'importation (bouton 'Enregistrer') !\");
		changement();
		//-->
		</script>\n";
	}

	if (isset($_POST['appreciations'])) {
		echo "<script type=\"text/javascript\" language=\"javascript\">
  	<!--
  	alert(\"Attention, les appréciations importées ne sont pas encore enregistrées dans la base GEPI. Vous devez confirmer l'importation (bouton 'Enregistrer') !\");
  	changement();
  	//-->
  	</script>\n";
	}
}

if ($id_devoir) {
	echo "<fieldset style=\"padding-top: 8px; padding-bottom: 8px;  margin-left: 8px; margin-right: 100px;\">\n";
	echo "<form enctype=\"multipart/form-data\" action=\"saisie_notes.php\" method='post' id='form_import_app'>\n";
	echo add_token_field();
	echo "<h3 class='gepi'>Importation directe des appréciations par copier/coller à partir d'un tableur</h3>\n";
	echo "<table summary=\"Tableau d'import\"><tr>\n";
	echo "<td>De la ligne : ";
	echo "<SELECT name='debut_import' size='1'>\n";
	$k = 1;
	while ($k < $current_displayed_line + 1) {
		echo "<option value='$k'>$k</option>\n";
		$k++;
	}
	echo "</select>\n";

	echo "<br /> à la ligne : \n";
	echo "<SELECT name='fin_import' size='1'>\n";
	$k = 1;
	while ($k < $current_displayed_line + 1) {
		echo "<option value='$k'";
		if ($k == $current_displayed_line) echo " SELECTED ";
		echo ">$k</option>\n";
		$k++;
	}
	echo "</select>\n";
	echo "</td><td>\n";
	echo "Coller ci-dessous les données à importer&nbsp;: <br />\n";
	if (isset($_POST['appreciations'])) {
		$appreciations = preg_replace("/\\\\n/", "\n", preg_replace("/\\\\r/", "\r", $_POST['appreciations']));
	} else {
		$appreciations = '';
	}
	echo "<textarea name='appreciations' rows='3' cols='40' class='wrap'>$appreciations</textarea>\n";
	echo "</td></tr></table>\n";
	echo "<input type='hidden' name='id_conteneur' value='$id_conteneur' />\n";
	echo "<input type='hidden' name='id_devoir' value='$id_devoir' />\n";
	echo "<input type='hidden' name='order_by' value='$order_by' />\n";
	echo "<center><input type='submit' value='Importer'  onclick=\"return confirm_abandon (this, change, '$themessage')\" /></center>\n";
	echo "<p><b>Remarque importante :</b> l'importation ne prend en compte que les élèves dont le nom est affiché ci-dessus !<br />Soyez donc vigilant à ne coller que les appréciations de ces élèves, dans le bon ordre.</p>\n";
	echo "</form></fieldset>\n";
}

// Pour qu'un professeur puisse avoir une préférence d'affichage par défaut ou non des quartiles:
$aff_quartiles_par_defaut = getPref($_SESSION['login'], 'aff_quartiles_cn', "n");
$aff_photo_cn_par_defaut = getPref($_SESSION['login'], 'aff_photo_cn', "n");

echo "<br />";
echo "<p><em>NOTES&nbsp;:</em></p>
<ul>
	<li style='margin-bottom:1em;'><p>La page de saisie de notes dans le carnet de notes permet de choisir des <a href='#' onclick=\"afficher_div('div_facilite_saisie','y',20,20); return false;\" target=\'_blank\'>Modes de saisie <img src='../images/icons/wizard.png' class='icone16' alt='Facilité de saisie' /></a>.<br />
	Selon vos préférences, vous opterez pour le mode historique de passage d'un champ note au suivant, ou vous opterez pour une des automatisations proposées.<br />
	Cliquez sur le lien <strong>Modes de saisie</strong> ci-dessus pour plus de précisions.</p></li>
	<li style='margin-bottom:1em;'><p>$message_cnil_commentaires</p></li>
	" . (((isset($temoin_remarque_nb_notes_sup_10_autre_referentiel)) && ($temoin_remarque_nb_notes_sup_10_autre_referentiel > 0)) ? "<li style='margin-bottom:1em;'><p>** Le nombre de notes supérieures ou égales à la note moitié du référentiel est affiché.<br />
	Pour les notes sur 20, ce sera le nombre de notes supérieures ou égales (<em >respectivement inférieures</em>) à 10.<br />
	Pour d'autres référentiels, par exemple sur 30, on comptera les notes supérieures ou égales à 15.</p></li>" : "") . "
</ul>";

// 20141103
foreach ($tab_ele_dev as $current_id_dev => $current_tab_notes) {
	$titre_infobulle = "Devoir n°" . $current_id_dev . " : " . $current_tab_notes['nom_dev'];

	$texte_infobulle = "<div align='center'>
<table class='boireaus boireaus_alt resizable sortable'>
	<thead>
		<tr>
			<th class='text' title=\"Cliquez pour trier\">Nom prénom</th>
			<th class='number' title=\"Cliquez pour trier\">Note</th>
		</tr>
	</thead>
	<tbody>";

	foreach ($current_tab_notes['eleve'] as $current_login_ele => $tmp_tab) {
		//number_format($tmp_tab['note'], 1, ".")
		if (preg_match("/^[0-9]{1,}[,]{0,1}[0-9]{0,2}$/", $tmp_tab['note'])) {
			$current_note = preg_replace("/,/", ".", $tmp_tab['note']);
		} elseif ($tmp_tab['note'] == "") {
			$current_note = "-";
		} else {
			$current_note = $tmp_tab['note'];
		}
		$texte_infobulle .= "
	<tr>
		<td>" . $tmp_tab['nom_prenom'] . "</td>
		<td>" . $current_note . "</td>
	</tr>";
	}

	$texte_infobulle .= "
	</tbody>
</table>
</div>";

	$tabdiv_infobulle[] = creer_div_infobulle('div_tel_devoir_' . $current_id_dev, $titre_infobulle, "", $texte_infobulle, "", 20, 0, 'y', 'y', 'n', 'n');
}
echo "<script type='text/javascript' language='javascript'>

</script>";

?>

<script type="text/javascript" language="javascript">
	chargement = true;

	// La vérification ci-dessous est effectuée après le remplacement des notes supérieures à 20 par des zéros.
	// Ces éventuelles erreurs de frappe ne sauteront pas aux yeux.
	for (i = 10; i <<?php echo $num_id; ?>; i++) {
		eval("verifcol(" + i + ")");
	}

	// On donne le focus à la première cellule lors du chargement de la page:
	if (document.getElementById('n10')) {
		document.getElementById('n10').focus();
	}

	function affichage_quartiles() {
		if (document.getElementById('div_quartiles').style.display == 'none') {
			document.getElementById('div_quartiles').style.display = '';
		} else {
			document.getElementById('div_quartiles').style.display = 'none';
		}
	}
	<?php
	if ($aff_quartiles_par_defaut == 'y') {
		echo "affichage_quartiles();\n";
	}
	if ((isset($id_devoir)) && ($id_devoir != NULL) && ($aff_photo_cn_par_defaut == 'y')) {
		echo "affiche_div_photo();\n";
	}
	?>

	function afficher_dev_infobulle(id_dev) {
		afficher_div('div_tel_devoir_' + id_dev, 'y', 10, 10);
	}

</script>
<?php
/**
 * Pied de page
 */
require("../lib/footer.inc.php");
?>
