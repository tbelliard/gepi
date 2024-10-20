<?php
/**
 * Ajouter, modifier un devoir
 * 
 *
 * @copyright Copyright 2001, 2021 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
 *
 * @package Carnet_de_notes
 * @subpackage Conteneur
 * @license GNU/GPL
 * @see add_token_field()
 * @see Calendrier::get_strPopup()
 * @see check_token()
 * @see checkAccess()
 * @see corriger_caracteres()
 * @see get_group()
 * @see get_groups_for_prof()
 * @see getPref()
 * @see getSettingValue()
 * @see mise_a_jour_moyennes_conteneurs()
 * @see recherche_enfant()
 * @see Session::security_check()
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
if (getSettingValue("active_carnets_notes")!='y') {
    die("Le module n'est pas activé.");
}

// 20241019
$GLOBALS['dont_get_modalite_elect']=true;

$msg="";

isset($id_retour);
$id_retour = isset($_POST["id_retour"]) ? $_POST["id_retour"] : (isset($_GET["id_retour"]) ? $_GET["id_retour"] : NULL);
isset($id_devoir);
$id_devoir = isset($_POST["id_devoir"]) ? $_POST["id_devoir"] : (isset($_GET["id_devoir"]) ? $_GET["id_devoir"] : NULL);
isset($mode_navig);
$mode_navig = isset($_POST["mode_navig"]) ? $_POST["mode_navig"] : (isset($_GET["mode_navig"]) ? $_GET["mode_navig"] : NULL);


if ($id_devoir)  {
    $query = mysqli_query($GLOBALS["mysqli"], "SELECT id_conteneur, id_racine FROM cn_devoirs WHERE id = '$id_devoir'");
    $id_racine = old_mysql_result($query, 0, 'id_racine');
    $id_conteneur = old_mysql_result($query, 0, 'id_conteneur');
} else if ((isset($_POST['id_conteneur'])) or (isset($_GET['id_conteneur']))) {
    $id_conteneur = isset($_POST['id_conteneur']) ? $_POST['id_conteneur'] : (isset($_GET['id_conteneur']) ? $_GET['id_conteneur'] : NULL);
    $query = mysqli_query($GLOBALS["mysqli"], "SELECT id_racine FROM cn_conteneurs WHERE id = '$id_conteneur'");
    $id_racine = old_mysql_result($query, 0, 'id_racine');
} else {
    header("Location: ../logout.php?auto=1");
    die();
}

$interface_simplifiee=isset($_POST['interface_simplifiee']) ? $_POST['interface_simplifiee'] : (isset($_GET['interface_simplifiee']) ? $_GET['interface_simplifiee'] : getPref($_SESSION['login'],'add_modif_dev_simpl','n'));

/**
 * Configuration des calendriers
 */
/*
include("../lib/calendrier/calendrier.class.php");
$cal = new Calendrier("formulaire", "display_date");
$cal2 = new Calendrier("formulaire", "date_ele_resp");
*/
$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";


// On teste si le carnet de notes appartient bien à la personne connectée
if (!(Verif_prof_cahier_notes ($_SESSION['login'],$id_racine))) {
    $mess=rawurlencode("Vous tentez de pénétrer dans un carnet de notes qui ne vous appartient pas !");
    header("Location: index.php?msg=$mess");
    die();
}

$appel_cahier_notes = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM cn_cahier_notes WHERE id_cahier_notes ='$id_racine'");
$id_groupe = old_mysql_result($appel_cahier_notes, 0, 'id_groupe');

// 20210301
if(is_groupe_exclu_tel_module($id_groupe, 'cahier_notes')) {
	$mess="Groupe/enseignement invalide.<br />";
	header("Location: index.php?msg=$mess");
	die();
}

$current_group = get_group($id_groupe);
$periode_num = old_mysql_result($appel_cahier_notes, 0, 'periode');
/**
 * Gestion des périodes
 */
include "../lib/periodes.inc.php";

$acces_exceptionnel_saisie=false;
if($_SESSION['statut']=='professeur') {
	$acces_exceptionnel_saisie=acces_exceptionnel_saisie_cn_groupe_periode($id_groupe, $periode_num);
}

// On teste si la periode est vérouillée !
if (($current_group["classe"]["ver_periode"]["all"][$periode_num] <= 1)&&(!$acces_exceptionnel_saisie)) {
    $mess=rawurlencode("Vous tentez de pénétrer dans un carnet de notes dont la période est bloquée !");
    header("Location: index.php?msg=$mess");
    die();
}

$matiere_nom = $current_group["matiere"]["nom_complet"];
$matiere_nom_court = $current_group["matiere"]["matiere"];
$nom_classe = $current_group["classlist_string"];

//isset($id_devoir);
$id_devoir = isset($_POST["id_devoir"]) ? $_POST["id_devoir"] : (isset($_GET["id_devoir"]) ? $_GET["id_devoir"] : NULL);

//debug_var();

// enregistrement des données
if (isset($_POST['ok'])) {
	check_token();
	unset($tab_group);
	$tab_group=array();

	$msg="";

	$reg_ok = "yes";
	$new='no';
	if ((isset($_POST['new_devoir'])) and ($_POST['new_devoir'] == 'yes')) {
		$reg = mysqli_query($GLOBALS["mysqli"], "insert into cn_devoirs (id_racine,id_conteneur,nom_court) values ('$id_racine','$id_conteneur','nouveau')");
		$id_devoir = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
		$new='yes';
		if (!$reg) {$reg_ok = "no";}

		$creation_dev_autres_groupes=isset($_POST['creation_dev_autres_groupes']) ? $_POST['creation_dev_autres_groupes'] : 'n';
		$id_autre_groupe=isset($_POST['id_autre_groupe']) ? $_POST['id_autre_groupe'] : array();
		if(($creation_dev_autres_groupes=='y')&&(count($id_autre_groupe)>0)) {
			// Créer un tableau des id_groupe, id_cahier_notes=id_racine, id_conteneur, id_devoir

			// On récupère le nom, la description,... de l'emplacement/boite/conteneur pour pouvoir créer le même si nécessaire
			$id_emplacement=isset($_POST['id_emplacement']) ? $_POST['id_emplacement'] : $id_racine;

			$sql="SELECT * FROM cn_conteneurs WHERE id='$id_emplacement';";
			$res_infos_conteneur=mysqli_query($GLOBALS["mysqli"], $sql);
			$lig_conteneur=mysqli_fetch_object($res_infos_conteneur);
			$nom_court_conteneur=$lig_conteneur->nom_court;
			$nom_complet_conteneur=$lig_conteneur->nom_complet;
			$description_conteneur=$lig_conteneur->description;
			$mode_conteneur=$lig_conteneur->mode;
			$coef_conteneur=$lig_conteneur->coef;
			$arrondir_conteneur=$lig_conteneur->arrondir;
			$ponderation_conteneur=$lig_conteneur->ponderation;
			$display_parents_conteneur=$lig_conteneur->display_parents;
			$display_bulletin_conteneur=$lig_conteneur->display_bulletin;

			$cpt=0;
			// Boucle sur les autres enseignements sur lesquels créer le même devoir
			for($i=0;$i<count($id_autre_groupe);$i++) {
				$tmp_group=get_group($id_autre_groupe[$i]);
				// Vérifier que la période est bien ouverte en saisie pour le groupe autre choisi
				if($tmp_group["classe"]["ver_periode"]["all"][$periode_num]>=2) {

					$tmp_id_racine="";

					$sql="SELECT id_cahier_notes FROM cn_cahier_notes WHERE (id_groupe='".$tmp_group['id']."' AND periode='$periode_num');";
					//echo "$sql<br />\n";
					$res_idcn=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_idcn)==0) {
						// On crée le cahier de notes

						$tmp_nom_complet_matiere = $tmp_group["matiere"]["nom_complet"];
						$tmp_nom_court_matiere = $tmp_group["matiere"]["matiere"];
						$reg = mysqli_query($GLOBALS["mysqli"], "INSERT INTO cn_conteneurs SET id_racine='', nom_court='".traitement_magic_quotes($tmp_group["description"])."', nom_complet='". traitement_magic_quotes($tmp_nom_complet_matiere)."', description = '', mode = '2', coef = '1.0', arrondir = 's1', ponderation = '0.0', display_parents = '0', display_bulletin = '1', parent = '0'");
						if ($reg) {
							$tmp_id_racine = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
							// On renseigne le champ id_racine avec la même valeur que l'id du conteneur: c'est la racine du cahier de notes
							$reg = mysqli_query($GLOBALS["mysqli"], "UPDATE cn_conteneurs SET id_racine='$tmp_id_racine', parent = '0' WHERE id='$tmp_id_racine'");
							// On déclare le cahier de notes avec cet identifiant
							$reg = mysqli_query($GLOBALS["mysqli"], "INSERT INTO cn_cahier_notes SET id_groupe = '".$tmp_group['id']."', periode = '$periode_num', id_cahier_notes='$tmp_id_racine'");
						}
					}
					else {
						$lig_tmp=mysqli_fetch_object($res_idcn);
						$tmp_id_racine=$lig_tmp->id_cahier_notes;
					}

					if(($tmp_id_racine!="")&&(is_numeric($tmp_id_racine))) {
						// Si le conteneur/boite n'est pas à la racine, on teste s'il faut créer un conteneur/boite dans l'autre enseignement
						if($id_emplacement!=$id_racine) {
							// La même boite existe-t-elle dans cet autre enseignement?
							$sql="SELECT * FROM cn_conteneurs WHERE nom_court='".addslashes($nom_court_conteneur)."' AND id_racine='$tmp_id_racine';";
							//echo "$sql<br />\n";
							$test_conteneur=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($test_conteneur)>0) {
								$lig_tmp=mysqli_fetch_object($test_conteneur);
								$tmp_id_conteneur=$lig_tmp->id;
							}
							else {
								// Par défaut, on met le devoir à la racine si le conteneur de même nom n'existe pas
								$tmp_id_conteneur=$tmp_id_racine;

								// si la case 'creer_conteneur' a été cochée, on crée ici une boite comme celle du devoir modèle
								if((isset($_POST['creer_conteneur']))&&($_POST['creer_conteneur']=="y")) {
									$sql="INSERT INTO cn_conteneurs SET id_racine='$tmp_id_racine',
																		nom_court='".addslashes($nom_court_conteneur)."',
																		nom_complet='".addslashes($nom_complet_conteneur)."',
																		description='".addslashes($description_conteneur)."',
																		mode='".addslashes($mode_conteneur)."',
																		coef='".addslashes($coef_conteneur)."',
																		arrondir='".addslashes($arrondir_conteneur)."',
																		ponderation='".addslashes($ponderation_conteneur)."',
																		display_parents='".addslashes($display_parents_conteneur)."',
																		display_bulletin='".addslashes($display_bulletin_conteneur)."',
																		parent='$tmp_id_racine';";
									if($insert_conteneur=mysqli_query($GLOBALS["mysqli"], $sql)) {
										$tmp_id_conteneur=((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
									}
									else {
										// Sinon, le devoir sera a la racine... mais on met un avertissement
										$msg.="Le conteneur/boite pour l'enseignement ".$tmp_group["name"]." (n°".$tmp_group["id"].") en ".$tmp_group["classlist_string"]." n'a pas été créé.<br />";
									}
								}

							}
						}
						else {
							// La boite du devoir est la racine du cahier de notes
							$tmp_id_conteneur=$tmp_id_racine;
						}

						if((is_numeric($tmp_id_conteneur))&&(is_numeric($tmp_id_racine))) {
							$sql="insert into cn_devoirs (id_racine,id_conteneur,nom_court) values ('$tmp_id_racine','$tmp_id_conteneur','nouveau');";
							//echo "$sql<br />\n";
							$creation_dev=mysqli_query($GLOBALS["mysqli"], $sql);
							$tmp_id_devoir = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
	
							$tab_group[$cpt]=$tmp_group;
							$tab_group[$cpt]['id_racine']=$tmp_id_racine;
							$tab_group[$cpt]['id_conteneur']=$tmp_id_conteneur;
							$tab_group[$cpt]['id_devoir']=$tmp_id_devoir;
	
							$cpt++;
						}
						else {
							$msg.="Le devoir n'a pas pu être créé pour le conteneur '$tmp_id_conteneur' de racine '$tmp_id_racine'.<br />";
							$reg_ok="no";
						}
					}
				}
			}
		}

	}

	// Pour loguer les modifications en période close:
	$temoin_log="n";
	$chaine_log="";
	if ($current_group["classe"]["ver_periode"]["all"][$periode_num] <= 1) {
		$sql="SELECT * FROM cn_devoirs WHERE id = '$id_devoir';";
		$res_old=mysqli_query($GLOBALS["mysqli"], $sql);
		$lig_old=mysqli_fetch_object($res_old);
		$temoin_log="y";
	}

	if (isset($_POST['nom_court'])) {
		$nom_court = $_POST['nom_court'];
	} else {
		$nom_court = "Devoir ".$id_devoir;
	}
	if(($temoin_log=="y")&&($nom_court!=$lig_old->nom_court)) {
		$chaine_log.=". Modification du nom court : $nom_court -> $lig_old->nom_court\n";
	}
	$reg = mysqli_query($GLOBALS["mysqli"], "UPDATE cn_devoirs SET nom_court = '".corriger_caracteres($nom_court)."' WHERE id = '$id_devoir'");
	if (!$reg)  $reg_ok = "no";
	for($i=0;$i<count($tab_group);$i++) {
		$sql="UPDATE cn_devoirs SET nom_court = '".corriger_caracteres($nom_court)."' WHERE id = '".$tab_group[$i]['id_devoir']."';";
		//echo "$sql<br />\n";
		$reg=mysqli_query($GLOBALS["mysqli"], $sql);
	}

	if(($interface_simplifiee=="y")&&(getPref($_SESSION['login'],'add_modif_dev_nom_complet','n')=='n')&&(isset($_POST['new_devoir'])) and ($_POST['new_devoir'] == 'yes')) {
		$nom_complet = $nom_court;
	}
	elseif (isset($_POST['nom_complet'])) {
		$nom_complet = $_POST['nom_complet'];
	} else {
		$nom_complet = $nom_court;
	}
	if(($temoin_log=="y")&&($nom_complet!=$lig_old->nom_complet)) {
		$chaine_log.=". Modification du nom complet : $nom_complet -> $lig_old->nom_complet\n";
	}
	$reg = mysqli_query($GLOBALS["mysqli"], "UPDATE cn_devoirs SET nom_complet = '".corriger_caracteres($nom_complet)."' WHERE id = '$id_devoir'");
	if (!$reg)  $reg_ok = "no";
	for($i=0;$i<count($tab_group);$i++) {
		$sql="UPDATE cn_devoirs SET nom_complet = '".corriger_caracteres($nom_complet)."' WHERE id = '".$tab_group[$i]['id_devoir']."';";
		//echo "$sql<br />\n";
		$reg=mysqli_query($GLOBALS["mysqli"], $sql);
	}

	if (isset($_POST['description'])) {
		if(($temoin_log=="y")&&($_POST['description']!=$lig_old->description)) {
			$chaine_log.=". Modification de la description :\n$lig_old->description\n->\n".$_POST['description']."\n";
		}
		$reg = mysqli_query($GLOBALS["mysqli"], "UPDATE cn_devoirs SET description = '".corriger_caracteres($_POST['description'])."' WHERE id = '$id_devoir'");
		if (!$reg)  $reg_ok = "no";
	}
	for($i=0;$i<count($tab_group);$i++) {
		$sql="UPDATE cn_devoirs SET description = '".corriger_caracteres($_POST['description'])."' WHERE id = '".$tab_group[$i]['id_devoir']."';";
		//echo "$sql<br />\n";
		$reg=mysqli_query($GLOBALS["mysqli"], $sql);
	}

	if (isset($_POST['id_emplacement'])) {
		$id_emplacement = $_POST['id_emplacement'];
		if(($temoin_log=="y")&&($lig_old->id_conteneur!=$id_emplacement)) {
			$chaine_log.=". Modification du conteneur dans lequel se trouve le devoir : $lig_old->id_conteneur -> ".$id_emplacement."\n";
		}
		$reg = mysqli_query($GLOBALS["mysqli"], "UPDATE cn_devoirs SET id_conteneur = '".$id_emplacement."' WHERE id = '$id_devoir'");
		if (!$reg)  $reg_ok = "no";

		for($i=0;$i<count($tab_group);$i++) {
			$sql="UPDATE cn_devoirs SET id_conteneur = '".$tab_group[$i]['id_conteneur']."' WHERE id = '".$tab_group[$i]['id_devoir']."';";
			//echo "$sql<br />\n";
			$reg=mysqli_query($GLOBALS["mysqli"], $sql);
		}
	}

	$tmp_coef=isset($_POST['coef']) ? $_POST['coef'] : 0;
	if((preg_match("/^[0-9]*$/", $tmp_coef))||(preg_match("/^[0-9]*\.[0-9]$/", $tmp_coef))) {
		// Le coef a le bon format
		//$msg.="Le coefficient proposé $tmp_coef est valide.<br />";
	}
	elseif(preg_match("/^[0-9]*\.[0-9]*$/", $tmp_coef)) {
		$msg.="Le coefficient ne peut avoir plus d'un chiffre après la virgule. Le coefficient va être tronqué.<br />";
	}
	elseif(preg_match("/^[0-9]*,[0-9]*$/", $tmp_coef)) {
		$msg.="Correction du séparateur des décimales dans le coefficient de $tmp_coef en ";
		$tmp_coef=preg_replace("/,/", ".", $tmp_coef);
		$msg.=$tmp_coef."<br />";
	}
	else {
		$msg.="Le coefficient proposé $tmp_coef est invalide. Mise à 1.0 du coefficient.<br />";
		$tmp_coef="1.0";
	}
	if(($temoin_log=="y")&&($lig_old->coef!=$tmp_coef)) {
		$chaine_log.=". Modification du coefficient du devoir : $lig_old->coef -> ".$tmp_coef."\n";
	}
	$reg = mysqli_query($GLOBALS["mysqli"], "UPDATE cn_devoirs SET coef='".$tmp_coef."' WHERE id='$id_devoir'");
	if (!$reg)  $reg_ok = "no";
	for($i=0;$i<count($tab_group);$i++) {
		$sql="UPDATE cn_devoirs SET coef='".$tmp_coef."' WHERE id='".$tab_group[$i]['id_devoir']."';";
		//echo "$sql<br />\n";
		$reg=mysqli_query($GLOBALS["mysqli"], $sql);
	}

	$note_sur=isset($_POST['note_sur']) ? $_POST['note_sur'] : getSettingValue("referentiel_note");
	if((preg_match("/^[0-9]*$/", $note_sur))||(preg_match("/^[0-9]*\.[0-9]*$/", $note_sur))) {
		// Le note_sur a le bon format
		//$msg.="Le référentiel proposé $note_sur est valide.<br />";
	}
	elseif(preg_match("/^[0-9]*,[0-9]*$/", $note_sur)) {
		$msg.="Correction du séparateur des décimales dans le référentiel de $note_sur en ";
		$note_sur=preg_replace("/,/", ".", $note_sur);
		$msg.=$note_sur."<br />";
	}
	else {
		$msg.="Le référentiel proposé $note_sur est invalide. Mise à ".getSettingValue("referentiel_note")." du référentiel.<br />";
		$note_sur=getSettingValue("referentiel_note");
	}

	// 20140531
	$sql="SELECT 1=1 FROM cn_notes_devoirs WHERE id_devoir='$id_devoir' AND note>'$note_sur' AND statut='';";
	$test_note=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_note_sup=mysqli_num_rows($test_note);
	if($nb_note_sup>0) {
		if(($temoin_log=="y")&&($lig_old->note_sur!=$note_sur)) {
			$chaine_log.=". ERREUR : Modification impossible du référentiel de note (note_sur) du devoir : $lig_old->note_sur -> ".$note_sur." (".$nb_note_sup." note(s) supérieures à $note_sur)\n";
		}
		$msg.="ERREUR : Modification impossible du référentiel de note (note_sur) : ".$nb_note_sup." note(s) supérieures à $note_sur.<br />";
		$reg_ok = "no";
	}
	else {
		if(($temoin_log=="y")&&($lig_old->note_sur!=$note_sur)) {
			$chaine_log.=". Modification du référentiel de note (note_sur) du devoir : $lig_old->note_sur -> ".$note_sur."\n";
		}
		$reg = mysqli_query($GLOBALS["mysqli"], "UPDATE cn_devoirs SET note_sur='".$note_sur."' WHERE id='$id_devoir'");
		if (!$reg)  $reg_ok = "no";
	}

	// Création d'autres devoirs avec les mêmes paramètres... donc pas encore de note
	for($i=0;$i<count($tab_group);$i++) {
		$sql="UPDATE cn_devoirs SET note_sur='".$note_sur."' WHERE id='".$tab_group[$i]['id_devoir']."';";
		//echo "$sql<br />\n";
		$reg=mysqli_query($GLOBALS["mysqli"], $sql);
	}

	if ((isset($_POST['ramener_sur_referentiel']))&&($_POST['ramener_sur_referentiel']=="V")) {
		$ramener_sur_referentiel='V';
	} else {
		$ramener_sur_referentiel='F';
	}
	if(($temoin_log=="y")&&($lig_old->ramener_sur_referentiel!=$ramener_sur_referentiel)) {
		$chaine_log.=". Modification du paramètre ramener_sur_referentiel du devoir : $lig_old->ramener_sur_referentiel -> ".$ramener_sur_referentiel."\n";
	}
	$reg = mysqli_query($GLOBALS["mysqli"], "UPDATE cn_devoirs SET ramener_sur_referentiel = '$ramener_sur_referentiel' WHERE id = '$id_devoir'");
	if (!$reg)  $reg_ok = "no";
	for($i=0;$i<count($tab_group);$i++) {
		$sql="UPDATE cn_devoirs SET ramener_sur_referentiel='$ramener_sur_referentiel' WHERE id='".$tab_group[$i]['id_devoir']."';";
		//echo "$sql<br />\n";
		$reg=mysqli_query($GLOBALS["mysqli"], $sql);
	}

	if (isset($_POST['facultatif']) and preg_match("/^(O|N|B)$/", $_POST['facultatif'])) {
		$reg = mysqli_query($GLOBALS["mysqli"], "UPDATE cn_devoirs SET facultatif = '".$_POST['facultatif']."' WHERE id = '$id_devoir'");
		if (!$reg)  $reg_ok = "no";
		for($i=0;$i<count($tab_group);$i++) {
			$sql="UPDATE cn_devoirs SET facultatif='".$_POST['facultatif']."' WHERE id='".$tab_group[$i]['id_devoir']."';";
			//echo "$sql<br />\n";
			$reg=mysqli_query($GLOBALS["mysqli"], $sql);
		}
	}

	if (isset($_POST['display_date'])) {
		if (preg_match("#([0-9]{2})/([0-9]{2})/([0-9]{4})#", $_POST['display_date'])) {
			$annee = mb_substr($_POST['display_date'],6,4);
			$mois = mb_substr($_POST['display_date'],3,2);
			$jour = mb_substr($_POST['display_date'],0,2);
		} else {
			$annee = strftime("%Y");
			$mois = strftime("%m");
			$jour = strftime("%d");
		}

		$date = $annee."-".$mois."-".$jour." 00:00:00";

		// 20201202 : Test sur la date : Est-elle bien entre le début et la fin de l'année scolaire ?
		if(gmmktime(0, 0, 0, $mois, $jour, $annee)<getSettingValue("begin_bookings")) {
			$debut_annee=strftime("%d/%m/%Y", getSettingValue("begin_bookings"));
			$msg.="La date proposée (".$_POST['display_date'].") est antérieure au début de l'année (".$debut_annee.")<br />Modification de la date proposée en ".$debut_annee."<br />";
			$date=strftime("%Y-%m-%d 00:00:00", getSettingValue("begin_bookings"));
		}

		if(gmmktime(0, 0, 0, $mois, $jour, $annee)>getSettingValue("end_bookings")) {
			$fin_annee=strftime("%d/%m/%Y", getSettingValue("end_bookings"));
			$msg.="La date proposée (".$_POST['display_date'].") est postérieure à la fin de l'année (".$fin_annee.")<br />Modification de la date proposée en ".$fin_annee."<br />";
			$date=strftime("%Y-%m-%d 00:00:00", getSettingValue("end_bookings"));
		}

		if(($temoin_log=="y")&&($lig_old->date!=$date)) {
			$chaine_log.=". Modification de la date du devoir : ".formate_date($lig_old->date)." -> ".formate_date($date)."\n";
		}

		$reg = mysqli_query($GLOBALS["mysqli"], "UPDATE cn_devoirs SET date = '".$date."' WHERE id = '$id_devoir'");
		if (!$reg)  $reg_ok = "no";
		for($i=0;$i<count($tab_group);$i++) {
			$sql="UPDATE cn_devoirs SET date='".$date."' WHERE id='".$tab_group[$i]['id_devoir']."';";
			//echo "$sql<br />\n";
			$reg=mysqli_query($GLOBALS["mysqli"], $sql);
		}
	}

	//====================================================
	if (isset($_POST['date_ele_resp'])) {
		if (preg_match("#([0-9]{2})/([0-9]{2})/([0-9]{4})#", $_POST['date_ele_resp'])) {
			$annee = mb_substr($_POST['date_ele_resp'],6,4);
			$mois = mb_substr($_POST['date_ele_resp'],3,2);
			$jour = mb_substr($_POST['date_ele_resp'],0,2);
		} else {
			$annee = strftime("%Y");
			$mois = strftime("%m");
			$jour = strftime("%d");
		}
		$date = $annee."-".$mois."-".$jour." 00:00:00";
		if(($temoin_log=="y")&&($lig_old->date_ele_resp!=$date)) {
			$chaine_log.=". Modification de la date de visibilité élève/parent du devoir : ".formate_date($lig_old->date_ele_resp)." -> ".formate_date($date)."\n";
		}
		$reg = mysqli_query($GLOBALS["mysqli"], "UPDATE cn_devoirs SET date_ele_resp='".$date."' WHERE id = '$id_devoir'");
		if (!$reg)  $reg_ok = "no";
		for($i=0;$i<count($tab_group);$i++) {
			$sql="UPDATE cn_devoirs SET date_ele_resp='".$date."' WHERE id='".$tab_group[$i]['id_devoir']."';";
			//echo "$sql<br />\n";
			$reg=mysqli_query($GLOBALS["mysqli"], $sql);
		}
	}
	//====================================================

	if (isset($_POST['display_parents'])) {
		if($_POST['display_parents']==1) {
			$display_parents=1;
		}
		else {
			$display_parents=0;
		}
	} else {
		$display_parents=0;
	}
	if(($temoin_log=="y")&&($lig_old->display_parents!=$display_parents)) {
		$chaine_log.=". Modification de la visibilité du devoir pour les parents/élèves : $lig_old->display_parents -> ".$display_parents."\n";
	}
	$reg = mysqli_query($GLOBALS["mysqli"], "UPDATE cn_devoirs SET display_parents = '$display_parents' WHERE id = '$id_devoir'");
	if (!$reg) {$reg_ok = "no";}
	for($i=0;$i<count($tab_group);$i++) {
		$sql="UPDATE cn_devoirs SET display_parents='$display_parents' WHERE id='".$tab_group[$i]['id_devoir']."';";
		//echo "$sql<br />\n";
		$reg=mysqli_query($GLOBALS["mysqli"], $sql);
	}

	if (isset($_POST['display_parents_app'])) {
		if($_POST['display_parents_app']==1) {
			$display_parents_app=1;
		}
		else {
			$display_parents_app=0;
		}
	} else {
		$display_parents_app=0;
	}
	if(($temoin_log=="y")&&($lig_old->display_parents_app!=$display_parents_app)) {
		$chaine_log.=". Modification de la visibilité par les parents/élèves du commentaire saisi : $lig_old->display_parents_app -> ".$display_parents_app."\n";
	}
	$reg = mysqli_query($GLOBALS["mysqli"], "UPDATE cn_devoirs SET display_parents_app = '$display_parents_app' WHERE id = '$id_devoir'");
	if (!$reg) {$reg_ok = "no";}
	for($i=0;$i<count($tab_group);$i++) {
		$sql="UPDATE cn_devoirs SET display_parents_app='$display_parents_app' WHERE id='".$tab_group[$i]['id_devoir']."';";
		//echo "$sql<br />\n";
		$reg=mysqli_query($GLOBALS["mysqli"], $sql);
	}

	if ($current_group["classe"]["ver_periode"]["all"][$periode_num] <= 1) {
		if($_POST['new_devoir'] == 'yes') {
			$texte="Ajout du devoir n°$id_devoir : ".$nom_court." (".$nom_complet.") coef $tmp_coef du ".formate_date($date).".\n";
		}
		else {
			$texte="Modification du devoir n°$id_devoir : ".$nom_court." (".$nom_complet.") coef $tmp_coef du ".formate_date($date).".\n";
			$texte.=$chaine_log;
		}
		$retour=log_modifs_acces_exceptionnel_saisie_cn_groupe_periode($id_groupe, $periode_num, $texte);
	}


    //==========================================================
    // MODIF: boireaus
    //
    // Mise à jour des moyennes du conteneur et des conteneurs parent, grand-parent, etc...
    //
    $arret = 'no';
    mise_a_jour_moyennes_conteneurs($current_group, $periode_num,$id_racine,$id_conteneur,$arret);
    // La boite courante est mise à jour...
    // ... mais pas la boite destination.
    
    recherche_enfant($id_racine);
    //==========================================================

	if ($reg_ok=='yes') {
		if ($new=='yes') {$msg.="Nouvel enregistrement réussi.";}
		else {$msg.="Les modifications ont été effectuées avec succès.<br />";}
	} else {
		$msg.="Il y a eu un problème lors de l'enregistrement.<br />";
	}

    //==========================================================
    // Ajout d'un test:
    // Si on modifie un devoir alors que des notes ont été reportées sur le bulletin, il faut penser à mettre à jour la recopie vers le bulletin.
    $sql="SELECT 1=1 FROM matieres_notes WHERE periode='".$periode_num."' AND id_groupe='".$id_groupe."';";
    $test_bulletin=mysqli_query($GLOBALS["mysqli"], $sql);
    if(mysqli_num_rows($test_bulletin)>0) {
        $msg.=" ATTENTION: Des notes sont présentes sur les bulletins. Si vous avez modifié un coefficient, des notes,... pensez à mettre à jour la recopie vers les bulletins.";
    }
    //==========================================================

    //
    // retour
    //
    if ($mode_navig == 'retour_saisie') {
        header("Location: ./saisie_notes.php?id_conteneur=$id_retour&msg=$msg");
        die();
    } else if ($mode_navig == 'retour_index') {
        header("Location: ./index.php?id_racine=$id_racine&msg=$msg");
        die();
    } elseif($mode_navig == 'saisie_devoir'){
	     header("Location: ./saisie_notes.php?id_conteneur=$id_conteneur&id_devoir=$id_devoir&msg=$msg");
        die();
    }
}

//-----------------------------------------------------------------------------------

if ($id_devoir)  {
    $new_devoir = 'no';
    $appel_devoir = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM cn_devoirs WHERE (id ='$id_devoir' and id_racine='$id_racine')");
    $nom_court = old_mysql_result($appel_devoir, 0, 'nom_court');
    $nom_complet = old_mysql_result($appel_devoir, 0, 'nom_complet');
    $description = old_mysql_result($appel_devoir, 0, 'description');
    $coef = old_mysql_result($appel_devoir, 0, 'coef');
    $note_sur = old_mysql_result($appel_devoir, 0, 'note_sur');
    $ramener_sur_referentiel = old_mysql_result($appel_devoir, 0, 'ramener_sur_referentiel');
    $facultatif = old_mysql_result($appel_devoir, 0, 'facultatif');
    $display_parents = old_mysql_result($appel_devoir, 0, 'display_parents');
    $display_parents_app = old_mysql_result($appel_devoir, 0, 'display_parents_app');
    $date = old_mysql_result($appel_devoir, 0, 'date');
    $id_conteneur = old_mysql_result($appel_devoir, 0, 'id_conteneur');

    $annee = mb_substr($date,0,4);
    $mois =  mb_substr($date,5,2);
    $jour =  mb_substr($date,8,2);
    $display_date = $jour."/".$mois."/".$annee;

    $date = old_mysql_result($appel_devoir, 0, 'date_ele_resp');
    $annee = mb_substr($date,0,4);
    $mois =  mb_substr($date,5,2);
    $jour =  mb_substr($date,8,2);
    $date_ele_resp = $jour."/".$mois."/".$annee;

} else {
    $nom_court = getPref($_SESSION['login'], 'cn_default_nom_court', 'Nouvelle évaluation');
    $nom_complet = getPref($_SESSION['login'], 'cn_default_nom_complet', 'Nouvelle évaluation');
    $description = "";
    $new_devoir = 'yes';
    $coef = getPref($_SESSION['login'], 'cn_default_coef', '1.0');
    if((!preg_match("/[0-9]*/", $coef))&&(!preg_match("/[0-9]*\.[0-9]*/", $coef))) {
    	$coef="1.0";
    	savePref($_SESSION['login'], 'cn_default_coef', '1.0');
    	$msg.="Correction de la valeur par défaut du coefficient.<br />";
	}
    $note_sur = getSettingValue("referentiel_note");
    $ramener_sur_referentiel = "F";
    $display_parents = "1";
    $display_parents_app = "0";
    $facultatif = "O";
    $date = "";
    $annee = strftime("%Y");
    $mois = strftime("%m");
    $jour = strftime("%d");
    $display_date = $jour."/".$mois."/".$annee;
	$date_ele_resp=$display_date;
}

//onclick=\"return confirm_abandon (this, change, '$themessage')\"
//onchange=\"changement();\"
$themessage  = 'Des notes ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Carnet de notes - Ajout/modification d'une évaluation";
/**
 * Entête de la page
 */
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();
insere_lien_calendrier_crob("right");
?>
<form enctype="multipart/form-data" 
	  name= "form_choix_dev" 
	  action="add_modif_dev.php" 
	  method="post">
<?php echo add_token_field(); ?>
	<div class='norme'>
		<p class=bold>
<?php 
if ($mode_navig == 'retour_saisie') {
?>
			<a href='./saisie_notes.php?id_conteneur=<?php echo $id_retour; ?>' onclick="return confirm_abandon (this, change, '<?php echo $themessage; ?>')">
				<img src='../images/icons/back.png' alt='Retour' class='back_link'/>
				Retour
			</a>
<?php
} else {
?>
			<a href='index.php?id_racine=<?php echo $id_racine; ?>' onclick="return confirm_abandon (this, change, '<?php echo $themessage; ?>')">
				<img src='../images/icons/back.png' alt='Retour' class='back_link'/>
				Retour
			</a>
<?php
}

$interface_simplifiee=isset($_POST['interface_simplifiee']) ? $_POST['interface_simplifiee'] : (isset($_GET['interface_simplifiee']) ? $_GET['interface_simplifiee'] : getPref($_SESSION['login'],'add_modif_dev_simpl','n'));

//echo "<a href='".$_SERVER['PHP_SELF']."?id_conteneur=$id_conteneur";
?>
			| 
			<a href='add_modif_dev.php?id_conteneur=<?php echo $id_conteneur; ?>
<?php if(isset($mode_navig)){ ?>&amp;mode_navig=<?php echo $mode_navig; ?><?php } ?>
<?php if(isset($id_devoir)){ ?>&amp;id_devoir=<?php echo $id_devoir; ?><?php } ?>
<?php if(isset($id_retour)){ ?>&amp;id_retour=<?php echo $id_retour; ?><?php } ?>
				&amp;
<?php 
//if($interface_simplifiee!=""){
if($interface_simplifiee=="y"){ ?>interface_simplifiee=n' <?php } else { ?>interface_simplifiee=y' <?php } ?>
				onclick="return confirm_abandon (this, change, '<?php echo $themessage; ?>')">
<?php if($interface_simplifiee=="y"){ ?>Interface complète <?php } else { ?>Interface simplifiée <?php } ?>
			|
			<a href='../gestion/config_prefs.php#add_modif_dev' 
			   onclick=\"return confirm_abandon (this, change, '<?php echo $themessage; ?>')">
			   Paramétrer l'interface simplifiée
			</a>
<?php
$sql="SELECT * FROM cn_devoirs WHERE id_racine='$id_racine' ORDER BY date, nom_court, nom_complet;";
$res_cd=mysqli_query($GLOBALS["mysqli"], $sql);
$chaine="";
$index_num_devoir=0;
$cpt_dev=0;
$temoin_champ_changement_dev="n";
if(mysqli_num_rows($res_cd)>0) {
	while ($lig_cd=mysqli_fetch_object($res_cd)) {
		$chaine.="<option value='".$lig_cd->id."'";
		if(($id_devoir)&&($lig_cd->id==$id_devoir)) {
			$chaine.=" selected='selected' ";
			$index_num_devoir=$cpt_dev;
		}
		$chaine.=">".$lig_cd->nom_court;
		if(($lig_cd->nom_complet!="")&&($lig_cd->nom_court!=$lig_cd->nom_complet)) {$chaine.=" (".$lig_cd->nom_complet.")";}
		$chaine.="</option>\n";
		$cpt_dev++;
	}
	if((($id_devoir)&&($cpt_dev>1))||
	((!$id_devoir)&&($cpt_dev>0))) { 
?>
			| 
			Période <?php echo $periode_num; ?>&nbsp;: 
			<select id='id_devoir' 
					name='id_devoir' 
					onchange="confirm_changement_devoir(change, '$themessage');">
<?php if(!$id_devoir) { ?>
				<option value='' selected='selected' >---</option>
<?php } ?>
				<?php echo $chaine; ?>
			</select>
			<input type='hidden' name='id_conteneur' value="<?php echo $id_conteneur; ?>" />
			<input type='submit' id='validation_form_choix_dev' value="Changer d'évaluation" />
<?php if($interface_simplifiee=="y"){ ?>
			<input type='hidden' name='interface_simplifiee' value=\"y\" />
<?php } else { ?>
			<input type='hidden' name='interface_simplifiee' value='n' />
<?php }
		$temoin_champ_changement_dev="y";
	}
} ?>
		</p>
<?php if($temoin_champ_changement_dev=="y") { ?>
<script type='text/javascript'>
	document.getElementById('validation_form_choix_dev').style.display='none';

	// Initialisation
	change='no';

	function confirm_changement_devoir(thechange, themessage)
	{
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			document.form_choix_dev.submit();
		}
		else {
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				document.form_choix_dev.submit();
			}
			else{
				document.getElementById('id_devoir').selectedIndex=$index_num_devoir;
			}
		}
	}
</script>
<?php } ?>
	</div>
</form>

<form enctype="multipart/form-data" name= "formulaire" action="add_modif_dev.php" method="post">

<?php echo add_token_field(); ?>

	<p class='bold'> 
		Classe : <?php echo $nom_classe; ?>
		|
		Matière : <?php echo htmlspecialchars("$matiere_nom ($matiere_nom_court)"); ?>
		| 
		Période : <?php echo $nom_periode[$periode_num]; ?>
		<input type="submit" name='ok' value="Enregistrer" style="font-variant: small-caps;" />
	</p>
</div>


<h2 class='gepi'>Configuration de l'évaluation :</h2>
<?php 
require('cc_lib.php');
$explication_ramener_sur_referentiel_case_non_cochee="Ce mode de calcul n'est pas un calcul de moyenne.
Cela peut néanmoins être utile pour des notes qui correspondraient à plusieurs petits contrôles destinés à ne former qu'une seule note.
Il conviendra de placer tous ces contrôles dans un(e) ".getSettingValue('gepi_denom_boite')." particulier distinct des autres évaluations.

A noter: Il est également possible de saisir des $nom_cc 
              plutôt que d'utiliser la présente solution.
              Voir le lien $nom_cc sur la ligne de liens
              sous l'entête dans votre carnet de notes.";
$explication_ramener_sur_referentiel_case_cochee="C'est le mode normal de calcul d'une moyenne:
On fait la somme des notes et on divise par le nombre de notes.

Cette explication est un chouia plus complexe si tous les coefficients de toutes les évaluations ne sont pas égaux:
C'est la somme des (note*coef) divisée par la somme des coefficients.";

if($interface_simplifiee=="y"){
	// Récupérer les paramètres à afficher.
	// Dans un premier temps, un choix pour tous.
	// Dans le futur, permettre un paramétrage par utilisateur

	$aff_nom_court=getPref($_SESSION['login'],'add_modif_dev_nom_court','y');
	$aff_nom_complet=getPref($_SESSION['login'],'add_modif_dev_nom_complet','n');
	$aff_description=getPref($_SESSION['login'],'add_modif_dev_description','n');
	$aff_coef=getPref($_SESSION['login'],'add_modif_dev_coef','y');
	$aff_note_autre_que_referentiel=getPref($_SESSION['login'],'add_modif_dev_note_autre_que_referentiel','n');
	$aff_date=getPref($_SESSION['login'],'add_modif_dev_date','y');
	$aff_date_ele_resp=getPref($_SESSION['login'],'add_modif_dev_date_ele_resp','y');
	$aff_boite=getPref($_SESSION['login'],'add_modif_dev_boite','y');
	$aff_display_parents=getPref($_SESSION['login'],'add_modif_dev_display_parents','n');
	$aff_display_parents_app=getPref($_SESSION['login'],'add_modif_dev_display_parents_app','n');
 ?>
<div align='center'>
	<table class='boireaus boireaus_alt' border='1'>
		<caption class="invisible">Paramètres du devoir</caption>

<?php
/*if($aff_nom_court=='y'){ ?>
		<tr>
			<td style='background-color: #aae6aa; font-weight: bold;'>Nom court :</td>
			<td>
				<input type='text' 
					   name = 'nom_court' 
					   size='40' 
					   value = "<?php echo $nom_court; ?>"
					   onfocus="javascript:this.select()" 
					   onchange="changement();" />
			</td>
		</tr>
<?php } else { ?>
		<tr style='display:none;'>
			<td style='background-color: #aae6aa; font-weight: bold;'>Nom court :</td>
			<td>
				<input type='hidden' 
					   name = 'nom_court' 
					   size='40' 
					   value = "<?php $nom_court; ?>" 
					   onfocus="javascript:this.select()" 
					   onchange="changement();" />
			</td>
		</tr>
<?php } 
*/?>


		<tr<?php if($aff_nom_court!='y'){ ?> style='display:none;' <?php } ?>>
			<td style='background-color: #aae6aa; font-weight: bold;'>Nom court :</td>
			<td>
				<input type='<?php if($aff_nom_court=='y'){ ?>text<?php } else { ?>hidden<?php } ?>' 
					   name = 'nom_court' 
					   size='40' 
					   value = "<?php echo $nom_court; ?>"
					   onfocus="javascript:this.select()" 
					   onchange="changement();" />
			</td>
		</tr>




<?php 
	if($aff_nom_complet=='y'){
		echo "<tr>\n";
		echo "<td style='background-color: #aae6aa; font-weight: bold;'>Nom complet :</td>\n";
		echo "<td>\n";
		echo "<input type='text' name = 'nom_complet' size='40' value = \"".$nom_complet."\" onfocus=\"javascript:this.select()\" onchange=\"changement();\" />\n";
		echo "</td>\n";
		echo "</tr>\n";
	}
	else{
		echo "<tr style='display:none;'>\n";
		echo "<td style='background-color: #aae6aa; font-weight: bold;'>Nom complet :</td>\n";
		echo "<td>\n";
		echo "<input type='hidden' name = 'nom_complet' size='40' value = \"".$nom_complet."\" onfocus=\"javascript:this.select()\" onchange=\"changement();\" />\n";
		echo "</td>\n";
		echo "</tr>\n";
	}


	if($aff_description=='y'){
		echo "<tr>\n";
		echo "<td style='background-color: #aae6aa; font-weight: bold;'>Description :</td>\n";
		echo "<td>\n";
		echo "<textarea name='description' rows='2' cols='40' onchange=\"changement();\">".$description."</textarea>\n";
		echo "</td>\n";
		echo "</tr>\n";
	}
	else{
		echo "<tr style='display:none;'>\n";
		echo "<td style='background-color: #aae6aa; font-weight: bold;'>Description :</td>\n";
		echo "<td>\n";
		echo "<input type='hidden' name='description' value='$description' />\n";
		echo "</td>\n";
		echo "</tr>\n";
	}


	if($aff_coef=='y'){
		echo "<tr>\n";
		echo "<td style='background-color: #aae6aa; font-weight: bold;'>Coefficient :</td>\n";
		echo "<td>\n";
		echo "<input type='text' name = 'coef' id='coef' size='4' value = \"".$coef."\" onkeydown=\"clavier_2(this.id,event,0,10);\" onchange=\"changement();\" autocomplete=\"off\" title=\"Vous pouvez modifier le coefficient à l'aide des flèches Up et Down du pavé de direction.\" />\n";
		echo "</td>\n";
		echo "</tr>\n";
	}
	else{
		echo "<tr style='display:none;'>\n";
		echo "<td>Coefficient : </td>\n";
		echo "<td>\n";
		echo "<input type='hidden' name = 'coef' size='4' value = \"".$coef."\" />\n";
		echo "</td>\n";
		echo "</tr>\n";
	}
	
	
	if(getSettingValue("note_autre_que_sur_referentiel")=="V") {
			?>
<tr>
	<td style='background-color: #aae6aa; font-weight: bold;'>Note sur : </td>
	<td>
		<input type='text' 
			   name = 'note_sur' 
			   id='note_sur' 
			   size='4' 
			   value = "<?php echo $note_sur; ?>"
			   onfocus="javascript:this.select()" 
			   onkeydown="clavier_2(this.id,event,1,100);" 
			   onchange="changement();" 
			   autocomplete="off" 
			   title="Vous pouvez modifier la valeur à l'aide des flèches Up et Down du pavé de direction." />
	</td>
</tr>
<tr>
	<td style='background-color: #aae6aa; font-weight: bold; vertical-align: top;'>
		Ramener la note sur <?php echo getSettingValue("referentiel_note"); ?>
		<br />lors du calcul de la moyenne :
	</td>
	<td>
		<input type='checkbox' 
			   name='ramener_sur_referentiel' 
			   value='V' 
			   onchange="changement();"
			   <?php if ($ramener_sur_referentiel == 'V') {echo " checked ='checked' ";} ?>
			   />
		<br />
		<span style="font-size: x-small;">
			Exemple avec 3 notes : 18/20 ; 4/10 ; 1/5
			<br />
			<span title="<?php echo $explication_ramener_sur_referentiel_case_cochee ?> " style="cursor: pointer">
				Case cochée : moyenne = 18/20 + 8/20 + 4/20 = 30/60 = 10/20
			</span>
			<br />
			<span title="<?php echo $explication_ramener_sur_referentiel_case_non_cochee ?>" style="cursor: pointer">
				Case non cochée : moyenne = (18 + 4 + 1) / (20 + 10 + 5) = 23/35 &asymp; 13,1/20
			</span>
			<span style="display: block;font-size: .7em;"><br /></span>
			Exemple avec 3 notes coefficientées : 
			<span style='color:blueviolet'>18/20 coef 3</span> ; 
			<span style='color:orange'>4/10 coef 1</span> ;
			<span style='color:green'>1/5 coef 2</span>
			<br />
			<span title="<?php echo $explication_ramener_sur_referentiel_case_cochee ?> " style="cursor: pointer">
				Case cochée : moyenne
				= <span style='color:blueviolet'>(18/20)*3</span>
				+ <span style='color:red'>4/10</span>
				+ <span style='color:green'>(1/5)*2</span> 
				= <span style='color:blueviolet'>(18/20)*3</span>
				+ <span style='color:red'>8/20</span>
				+ <span style='color:green'>(4/20)*2</span>
				= 70/120 ≈ 11,67/20
			</span>
			<br />
			<span title="<?php echo $explication_ramener_sur_referentiel_case_non_cochee ?>" style="cursor: pointer">
				Case non cochée : moyenne = (
				<span style='color:blueviolet'>18*3</span>
				+ <span style='color:red'>4</span>
				+ <span style='color:green'>1*2</span>
				) / (
				<span style='color:blueviolet'>20*3</span>
				+ <span style='color:red'>10</span>
				+ <span style='color:green'>5*2</span>
				) = 60/80 = 15/20
			</span>
		</span>
	</td>
</tr>
			<?php
	} else {
		echo "<tr style='display:none;'>\n";
		echo "<td>Note sur :</td>\n";
		echo "<td>\n";
		echo "<input type='hidden' name = 'note_sur' value = '".$note_sur."' />\n";
		echo "<input type='hidden' name = 'ramener_sur_referentiel' value = '$ramener_sur_referentiel' />\n";
 		echo "</td>\n";
		echo "</tr>\n";
	}


	if($aff_date=='y'){
		echo "<tr>\n";
		echo "<td style='background-color: #aae6aa; font-weight: bold;'>Date :</td>\n";
		echo "<td>\n";
		// 20201202 : A FAIRE : Ajouter un test sur la date : Est-elle bien entre le début et la fin de l'année scolaire ?
		echo "<input type='text' name='display_date' id='display_date' size='10' value = \"".$display_date."\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" title=\"Vous pouvez modifier la date à l'aide des flèches Up et Down du pavé de direction.\" ";
		if($aff_date_ele_resp!='y'){
			echo " onchange=\"document.getElementById('date_ele_resp').value=document.getElementById('display_date').value;changement();\"";
		}
		echo "/>\n";
		/*
		echo "<a href=\"#calend\" onClick=\"".$cal->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"";
		if($aff_date_ele_resp!='y'){
			echo " onchange=\"document.getElementById('date_ele_resp').value=document.getElementById('display_date').value;changement();\"";
		}
		echo "><img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Petit calendrier\" /></a>\n";
		*/
		echo img_calendrier_js("display_date", "img_bouton_display_date");
		echo "</td>\n";
		echo "</tr>\n";
	}
	else{
		echo "<tr style='display:none;'>\n";
		echo "<td style='background-color: #aae6aa; font-weight: bold;'>Date :</td>\n";
		echo "<td>\n";
		echo "<input type='hidden' name = 'display_date' size='10' value = \"".$display_date."\" onchange=\"changement();\" />\n";
		echo "</td>\n";
		echo "</tr>\n";
	}

	if($aff_date_ele_resp=='y'){
		echo "<tr>\n";
		echo "<td style='background-color: #aae6aa; font-weight: bold;'>Date de visibilité<br />de la note pour les<br />élèves et responsables :</td>\n";
		echo "<td>\n";
		echo "<input type='text' name = 'date_ele_resp' id='date_ele_resp' size='10' value = \"".$date_ele_resp."\" onKeyDown=\"clavier_date(this.id,event);\" onchange=\"changement();\" AutoComplete=\"off\" title=\"Vous pouvez modifier la date à l'aide des flèches Up et Down du pavé de direction.\" />\n";
		//echo "<a href=\"#calend\" onClick=\"".$cal2->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Petit calendrier\" /></a>\n";
		echo img_calendrier_js("date_ele_resp", "img_bouton_date_ele_resp");
		echo "</td>\n";
		echo "</tr>\n";
	}
	else{
		echo "<tr style='display:none;'>\n";
		echo "<td style='background-color: #aae6aa; font-weight: bold;'>Date de visibilité<br />de la note pour les<br />élèves et responsables :</td>\n";
		echo "<td>\n";
		echo "<input type='hidden' name='date_ele_resp' size='10' value = \"".$date_ele_resp."\" />\n";
		echo "</td>\n";
		echo "</tr>\n";
	}


	if($aff_boite=='y'){
		echo "<tr>\n";
		echo "<td style='background-color: #aae6aa; font-weight: bold;'>Emplacement de l'évaluation :</td>\n";
		echo "<td>\n";

		echo "<select size='1' name='id_emplacement' onchange=\"changement();\">\n";
		$appel_conteneurs = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM cn_conteneurs WHERE id_racine ='$id_racine' order by nom_court");
		$nb_cont = mysqli_num_rows($appel_conteneurs);
		$i = 0;
		while ($i < $nb_cont) {
			$id_cont = old_mysql_result($appel_conteneurs, $i, 'id');
			$nom_conteneur = old_mysql_result($appel_conteneurs, $i, 'nom_court');
			echo "<option value='$id_cont' ";
			if ($id_cont == $id_conteneur) echo "selected";
			if($nom_conteneur==""){echo " >---</option>\n";}else{echo " >$nom_conteneur</option>\n";}
			$i++;
		}
		echo "</select>\n";

		echo "</td>\n";
		echo "</tr>\n";
	}
	else{
		echo "<tr style='display:none;'>\n";
		echo "<td style='background-color: #aae6aa; font-weight: bold;'>Emplacement de l'évaluation:</td>\n";
		echo "<td>\n";
		echo "<input type='hidden' name='id_emplacement' size='10' value='$id_conteneur' />\n";
		echo "</td>\n";
		echo "</tr>\n";
	}


	if($aff_display_parents=='y'){
		echo "<tr>\n";
		echo "<td style='background-color: #aae6aa; font-weight: bold;'>L'évaluation apparaît sur le relevé de notes :</td>\n";
		echo "<td><input type='checkbox' name='display_parents' value='1' onchange=\"changement();\" "; if ($display_parents == '1') {echo " checked";} echo " /></td>\n";
		echo "</tr>\n";
	} else {
		echo "<tr style='display:none;'>\n";
		echo "<td>L'évaluation apparaît sur le relevé de notes :</td>\n";
		echo "<td>\n";
		echo "<input type='hidden' name='display_parents' value='$display_parents' />\n";
		echo "</td>\n";
		echo "</tr>\n";
	}

	if($aff_display_parents_app=='y'){
		echo "<tr>\n";
		echo "<td style='background-color: #aae6aa; font-weight: bold;' title=\"Sous réserve que l'évaluation soit affichée sur le relevé de notes.\">L'appréciation de l'évaluation apparaît sur le relevé de notes :</td>\n";
		echo "<td><input type='checkbox' name='display_parents_app' value='1' onchange=\"changement();\" "; if ($display_parents_app == '1') {echo " checked";} echo " /></td>\n";
		echo "</tr>\n";
	} else {
		echo "<tr style='display:none;'>\n";
		echo "<td>L'appréciation de l'évaluation apparaît sur le relevé de notes :</td>\n";
		echo "<td>\n";
		echo "<input type='hidden' name='display_parents_app' value='$display_parents_app' />\n";
		echo "</td>\n";
		echo "</tr>\n";
	}


	echo "</table>\n";
	echo "</div>\n";
	echo "<input type='hidden' name='facultatif' value='$facultatif' />\n";
	echo "<input type='hidden' name='interface_simplifiee' value='$interface_simplifiee' />\n";

	if($aff_nom_court=='y'){
		echo "<script type='text/javascript'>
	document.formulaire.nom_court.focus();
</script>\n";
	}
}
else{
	//====================================
	// Noms et conteneur
	// =================

	echo "<table summary='Nom et conteneur du devoir'>\n";
	echo "<tr><td>Nom court : </td><td><input type='text' name = 'nom_court' size='40' value = \"".$nom_court."\" onfocus=\"javascript:this.select()\" onchange=\"changement();\" /></td></tr>\n";
	echo "<tr><td>Nom complet : </td><td><input type='text' name = 'nom_complet' size='40' value = \"".$nom_complet."\" onfocus=\"javascript:this.select()\" onchange=\"changement();\" /></td></tr>\n";
	echo "<tr><td>Description : </td><td><textarea name='description' rows='2' cols='40' onchange=\"changement();\" >".$description."</textarea></td></tr></table>\n";
	echo "<br />\n";
	echo "<table summary='Emplacement du devoir'><tr><td><h3 class='gepi'>Emplacement de l'évaluation : </h3></td>\n<td>";
	echo "<select size='1' name='id_emplacement' onchange=\"changement();\">\n";
	$appel_conteneurs = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM cn_conteneurs WHERE id_racine ='$id_racine' order by nom_court");
	$nb_cont = mysqli_num_rows($appel_conteneurs);
	$i = 0;
	while ($i < $nb_cont) {
	$id_cont = old_mysql_result($appel_conteneurs, $i, 'id');
	$nom_conteneur = old_mysql_result($appel_conteneurs, $i, 'nom_court');
	echo "<option value='$id_cont' ";
	if ($id_cont == $id_conteneur) {echo "selected='selected'";}
	if($nom_conteneur==""){echo " >---</option>\n";}else{echo " >$nom_conteneur</option>\n";}
	$i++;
	}
	echo "</select></td></tr></table>\n";

	//====================================
	// Coeff
	// =====

	echo "<h3 class='gepi'>Coefficient de l'évaluation</h3>\n";
	echo "<div style='margin-left:2em;'>\n";
	echo "<table summary='Ponderation'><tr><td>Valeur de la pondération dans le calcul de la moyenne (<em>si 0, la note de l'évaluation n'intervient pas dans le calcul de la moyenne</em>)&nbsp;: </td>";
	echo "<td><input type='text' name = 'coef' id='coef' size='4' value = \"".$coef."\" onfocus=\"javascript:this.select()\" onchange=\"changement();\" onkeydown=\"clavier_2(this.id,event,0,10);\" autocomplete=\"off\" title=\"Vous pouvez modifier le coefficient à l'aide des flèches Up et Down du pavé de direction.\" /></td></tr></table>\n";
	echo "</div>\n";

	//====================================
	// Note autre que sur 20
	// =====
	if(getSettingValue("note_autre_que_sur_referentiel")=="V") {
		?>
<h3 class='gepi'>Notation</h3>
<div style='margin-left:2em;'>
	<table summary='Referentiel'>
		<tr>
			<td>Note sur : </td>
			<td>
				<input type='text' 
					   name = 'note_sur' 
					   id='note_sur' 
					   size='4' 
					   value = "<?php echo $note_sur; ?>" 
					   onfocus="javascript:this.select()" 
					   onkeydown="clavier_2(this.id,event,1,100);" 
					   onchange="changement();" 
					   autocomplete="off" 
					   title="Vous pouvez modifier la valeur à l'aide des flèches Up et Down du pavé de direction." />
			</td>
		</tr>
		<tr>
			<td>
				Ramener la note sur <?php echo getSettingValue("referentiel_note"); ?> lors du calcul de la moyenne : 
				<br />
				<span style="font-size: x-small;">
					Exemple avec 3 notes : 18/20 ; 4/10 ; 1/5
					<br />
					<span title=\"$explication_ramener_sur_referentiel_case_cochee\">
						Case cochée : moyenne = 18/20 + 8/20 + 4/20 = 30/60 = 10/20
					</span>
					<br />
					<span title="$explication_ramener_sur_referentiel_case_non_cochee">
						Case non cochée : moyenne = (18 + 4 + 1) / (20 + 10 + 5) = 23/35 &asymp; 13,1/20
					</span>
					<span style="display: block;font-size: .7em;"><br /></span>				
					Exemple avec 3 notes coefficientées : 
					<span style='color:blueviolet'>18/20 coef 3</span> ; 
					<span style='color:red'>4/10 coef 1</span> ;
					<span style='color:green'>1/5 coef 2</span>
					<br />
					<span title="<?php echo $explication_ramener_sur_referentiel_case_cochee ?> " style="cursor: pointer">
						Case cochée : moyenne
						= <span style='color:blueviolet'>(18/20)*3</span>
						+ <span style='color:red'>4/10</span>
						+ <span style='color:green'>(1/5)*2</span> 
						= <span style='color:blueviolet'>(18/20)*3</span>
						+ <span style='color:red'>8/20</span>
						+ <span style='color:green'>(4/20)*2</span>
						= 70/120 ≈ 11,67/20
					</span>
					<br />
					<span title="<?php echo $explication_ramener_sur_referentiel_case_non_cochee ?>" style="cursor: pointer">
						Case non cochée : moyenne = (
						<span style='color:blueviolet'>18*3</span>
						+ <span style='color:red'>4</span>
						+ <span style='color:green'>1*2</span>
						) / (
						<span style='color:blueviolet'>20*3</span>
						+ <span style='color:red'>10</span>
						+ <span style='color:green'>5*2</span>
						) = 60/80 = 15/20
					</span>
					</span>
				</span>
				<br />
			</td>
			<td>
				<input type='checkbox' 
					   name='ramener_sur_referentiel' 
					   value='V' 
					   onchange="changement();" 
						   <?php if ($ramener_sur_referentiel == 'V') {echo " checked = 'checked' ";} ?>
					   />
				<br />
			</td>
		</tr>
	</table>
</div>
		<?php
	} else {
		echo "<input type='hidden' name = 'note_sur' value = '".$note_sur."' />\n";
		echo "<input type='hidden' name = 'ramener_sur_referentiel' value = '$ramener_sur_referentiel' />\n";
	}

	//====================================
	// Statut
	// ======

	echo "<a name='statut_evaluation'></a><h3 class='gepi'>Statut de l'évaluation</h3>\n";
	echo "<div style='margin-left:2em;'>\n";
	if(!in_array($facultatif, array("O", "B", "N"))) {
		echo "<p style='color:red'><strong>Anomalie&nbsp;:</strong> Aucun choix n'est effectué ci-dessous.<br />Cela risque de perturber le calcul de la moyenne du carnet de notes.</p>";
	}
	echo "<table summary='Statut du devoir'><tr><td><input type='radio' name='facultatif' id='facultatif_O' value='O' onchange=\"changement();\" ";
	if ($facultatif=='O') {echo "checked";}
	echo " /></td><td>";
	echo "<label for='facultatif_O' style='cursor: pointer;'>";
	echo "La note de l'évaluation entre dans le calcul de la moyenne.";
	echo "</label>";
	echo "</td></tr>\n<tr><td><input type='radio' name='facultatif' id='facultatif_B' value='B' onchange=\"changement();\" ";
	if ($facultatif=='B') {echo "checked";}
	echo " /></td><td>";
	echo "<label for='facultatif_B' style='cursor: pointer;'>";
	echo "Seules les notes de l'évaluation supérieures à 10 entrent dans le calcul de la moyenne.";
	echo "</label>";
	echo "</td></tr>\n<tr><td><input type='radio' name='facultatif' id='facultatif_N' value='N' onchange=\"changement();\" ";
	if ($facultatif=='N') {echo "checked";}
	echo " /></td><td>";
	echo "<label for='facultatif_N' style='cursor: pointer;'>";
	echo "La note de l'évaluation n'entre dans le calcul de la moyenne que si elle améliore la moyenne.";
	echo "</label>";
	echo "</td></tr></table></div>\n";

	//====================================
	// Date
	// ====

	echo "<a name=\"calend\"></a><h3 class='gepi'>Date de l'évaluation (<em>format jj/mm/aaaa</em>) : </h3>
	<div style='margin-left:2em;'>
	Date&nbsp;: <input type='text' name = 'display_date' id='display_date' size='10' value = \"".$display_date."\" onKeyDown=\"clavier_date(this.id,event);\" onchange=\"changement();\" AutoComplete=\"off\" title=\"Vous pouvez modifier la date à l'aide des flèches Up et Down du pavé de direction.\" />";
	//echo "<a href=\"#calend\" onClick=\"".$cal->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Petit calendrier\" /></a>\n";
		echo img_calendrier_js("display_date", "img_bouton_display_date");
	echo "<br />\n";
	echo "<b>Remarque</b> : c'est cette date qui est prise en compte pour l'édition des relevés de notes à différentes périodes de l'année.";
	echo "</div>\n";

	echo "<a name=\"calend\"></a><h3 class='gepi'>Date de visibilité de l'évaluation pour les élèves et responsables (<em>format jj/mm/aaaa</em>) : </h3>
	<div style='margin-left:2em;'>
	Date&nbsp;: <input type='text' name='date_ele_resp' id='date_ele_resp' size='10' value=\"".$date_ele_resp."\" onKeyDown=\"clavier_date(this.id,event);\" onchange=\"changement();\" AutoComplete=\"off\" title=\"Vous pouvez modifier la date à l'aide des flèches Up et Down du pavé de direction.\" />";
	//echo "<a href=\"#calend\" onClick=\"".$cal2->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Petit calendrier\" /></a>\n";
	echo img_calendrier_js("date_ele_resp", "img_bouton_date_ele_resp");
	echo "<br />\n";
	echo "<b>Remarque</b> : Cette date permet de ne rendre la note visible qu'une fois que le devoir est corrigé en classe.";
	echo "</div>\n";

	//====================================
	// Relevé de notes
	// ===============

	echo "<h3 class='gepi'>Affichage sur le relevé de notes</h3>\n";
	echo "<div style='margin-left:2em;'>\n";
	echo "<table summary='Visibilité'>\n";
	echo "<tr><td><label for='display_parents' style='cursor: pointer;'>";
	echo "Faire <b>apparaître cette évaluation</b> sur le <b>relevé de notes</b> de l'élève : ";
	echo "</label>";
	echo "</td><td><input type='checkbox' name='display_parents' id='display_parents' value='1' onchange=\"changement();\" "; if ($display_parents == 1) echo " checked"; echo " /></td></tr>\n";

	echo "<tr><td><label for='display_parents_app' style='cursor: pointer;'>";
	echo "<b>L'appréciation</b> de l'évaluation est affichable sur le <b>relevé de notes</b> de l'élève (si l'option précédente a été validée) :";
	echo "</label>";
	echo "</td><td><input type='checkbox' name='display_parents_app' id='display_parents_app' value='1' onchange=\"changement();\" "; if ($display_parents_app == 1) echo " checked"; echo " /></td></tr>\n";

  echo "</table>\n";
	echo "</div>\n";

	echo "<script type='text/javascript'>
	document.formulaire.nom_court.focus();
</script>\n";

}

if ($new_devoir=='yes') {
	echo "<input type='hidden' name='new_devoir' value='yes' />\n";

	$tab_group=get_groups_for_prof($_SESSION['login']);
	if(count($tab_group)>1) {

		if($interface_simplifiee=="y"){echo "<div align='center'>\n";}
		echo "<input type='checkbox' id='creation_dev_autres_groupes' name='creation_dev_autres_groupes' value='y' onchange=\"display_div_autres_groupes()\" onchange=\"changement();\" /><label for='creation_dev_autres_groupes'> Créer le même devoir pour d'autres enseignements.</label><br />\n";
	
		echo "<div id='div_autres_groupes'>\n";
		echo "<table class='boireaus boireaus_alt' summary='Autres enseignements'>\n";
		echo "<tr>\n";
		echo "<th rowspan='2'>";
		echo "<a href='javascript:modif_case(true)'><img src='../images/enabled.png' class='icone15' alt='Tout cocher' /></a>/\n";
		echo "<a href='javascript:modif_case(false)'><img src='../images/disabled.png' class='icone15' alt='Tout décocher' /></a>\n";
		echo "</th>\n";
		echo "<th colspan='3'>Enseignement</th>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<th>Nom</th>\n";
		echo "<th>Description</th>\n";
		echo "<th>Classe</th>\n";
		echo "</tr>\n";

		$cpt=0;
		for($i=0;$i<count($tab_group);$i++) {
			if((!isset($tab_group[$i]["visibilite"]["cahier_notes"]))||($tab_group[$i]["visibilite"]["cahier_notes"]=='y')) {
				if($tab_group[$i]['id']!=$id_groupe) {
					// Tester si la période est aussi ouverte pour le groupe... ou sinon si une seule période est ouverte en saisie?
					if(isset($tab_group[$i]["classe"]["ver_periode"]["all"][$periode_num])) {
						echo "<tr>\n";
						echo "<td>\n";
						if($tab_group[$i]["classe"]["ver_periode"]["all"][$periode_num]>=2) {
							echo "<input type='checkbox' name='id_autre_groupe[]' id='case_$cpt' value='".$tab_group[$i]['id']."' onchange=\"changement();\" />\n";
							echo "</td>\n";
							echo "<td><label for='case_$cpt'>".htmlspecialchars($tab_group[$i]['name'])."</label></td>\n";
							echo "<td><label for='case_$cpt'>".htmlspecialchars($tab_group[$i]['description'])."</label></td>\n";
							echo "<td><label for='case_$cpt'>".$tab_group[$i]['classlist_string']."</label></td>\n";
							$cpt++;
						}
						else {
							echo "<span style='color:red;'>Clos</span>";
							echo "</td>\n";
							echo "<td>".htmlspecialchars($tab_group[$i]['name'])."</td>\n";
							echo "<td>".htmlspecialchars($tab_group[$i]['description'])."</td>\n";
							echo "<td>".$tab_group[$i]['classlist_string']."</td>\n";
						}
						//echo "<td>...</td>\n";
						echo "</tr>\n";
					}
				}
			}
		}
		echo "</table>\n";
		// A METTRE AU POINT: 
		echo "<input type='checkbox' name='creer_conteneur' id='creer_conteneur' value='y' onchange=\"changement();\" /><label for='creer_conteneur'> Créer si nécessaire ";
		if(getSettingValue('gepi_denom_boite_genre')=="m") {echo "le ";} else {echo "la ";}
		echo getSettingValue('gepi_denom_boite');
		echo ".</label>\n";
		echo "</div>\n";
		if($interface_simplifiee=="y"){echo "</div>\n";}

		echo "<script type='text/javascript'>
function display_div_autres_groupes() {
	if(document.getElementById('creation_dev_autres_groupes').checked==true) {
		document.getElementById('div_autres_groupes').style.display='';
	}
	else {
		document.getElementById('div_autres_groupes').style.display='none';
	}
}
display_div_autres_groupes();

function modif_case(statut){
	for(k=0;k<$cpt;k++){
		if(document.getElementById('case_'+k)){
			document.getElementById('case_'+k).checked=statut;
		}
	}
}

</script>\n";

	}
}
echo "<input type='hidden' name='id_devoir' value='$id_devoir' />\n";
echo "<input type='hidden' name='id_conteneur' value='$id_conteneur' />\n";
echo "<input type='hidden' name='mode_navig' value='$mode_navig' />\n";
echo "<input type='hidden' name='id_retour' value='$id_retour' />\n";

echo "<div style='display:none'><input type=\"hidden\" name='ok' value=\"Enregistrer\" /></div>\n";
echo "<p style='text-align:center;'><input type=\"submit\" name='ok1' value=\"Enregistrer\" style=\"font-variant: small-caps;\" /><br/>\n";
echo "<input type=\"button\" name='ok2' value=\"Enregistrer et saisir dans la foulée\" style=\"font-variant: small-caps;\" onClick=\"document.forms['formulaire'].mode_navig.value='saisie_devoir';document.forms['formulaire'].submit();\" /></p>\n";

echo "</form>\n";
echo "<br />\n";
/**
 * Pied de page
 */
require("../lib/footer.inc.php");
?>
