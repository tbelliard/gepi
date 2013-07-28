<?php
/*
 *
 * Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
require_once("../lib/share-trombinoscope.inc.php");

unset($reg_login);
$reg_login = isset($_POST["reg_login"]) ? $_POST["reg_login"] : NULL;
unset($reg_nom);
$reg_nom = isset($_POST["reg_nom"]) ? $_POST["reg_nom"] : NULL;
unset($reg_prenom);
$reg_prenom = isset($_POST["reg_prenom"]) ? $_POST["reg_prenom"] : NULL;
unset($reg_email);
$reg_email = isset($_POST["reg_email"]) ? $_POST["reg_email"] : NULL;
unset($reg_sexe);
$reg_sexe = isset($_POST["reg_sexe"]) ? $_POST["reg_sexe"] : NULL;
unset($reg_no_nat);
$reg_no_nat = isset($_POST["reg_no_nat"]) ? $_POST["reg_no_nat"] : NULL;
unset($reg_no_gep);
$reg_no_gep = isset($_POST["reg_no_gep"]) ? $_POST["reg_no_gep"] : NULL;

unset($reg_mef_code);
$reg_mef_code = isset($_POST["reg_mef_code"]) ? $_POST["reg_mef_code"] : NULL;

unset($reg_auth_mode);
$reg_auth_mode = isset($_POST["reg_auth_mode"]) ? $_POST["reg_auth_mode"] : NULL;

unset($birth_year);
$birth_year = isset($_POST["birth_year"]) ? $_POST["birth_year"] : NULL;
unset($birth_month);
$birth_month = isset($_POST["birth_month"]) ? $_POST["birth_month"] : NULL;
unset($birth_day);
$birth_day = isset($_POST["birth_day"]) ? $_POST["birth_day"] : NULL;

unset($reg_tel_pers);
$reg_tel_pers = isset($_POST["reg_tel_pers"]) ? $_POST["reg_tel_pers"] : NULL;
unset($reg_tel_port);
$reg_tel_port = isset($_POST["reg_tel_port"]) ? $_POST["reg_tel_port"] : NULL;
unset($reg_tel_prof);
$reg_tel_prof = isset($_POST["reg_tel_prof"]) ? $_POST["reg_tel_prof"] : NULL;

//Gestion de la date de sortie de l'établissement
unset($date_sortie_jour);
$date_sortie_jour = isset($_POST["date_sortie_jour"]) ? $_POST["date_sortie_jour"] : "00";
unset($date_sortie_mois);
$date_sortie_mois = isset($_POST["date_sortie_mois"]) ? $_POST["date_sortie_mois"] : "00";
unset($date_sortie_annee);
$date_sortie_annee = isset($_POST["date_sortie_annee"]) ? $_POST["date_sortie_annee"] : "0000";

//Gestion de la date d'entrée dans l'établissement
unset($date_entree_jour);
$date_entree_jour = isset($_POST["date_entree_jour"]) ? $_POST["date_entree_jour"] : "00";
unset($date_entree_mois);
$date_entree_mois = isset($_POST["date_entree_mois"]) ? $_POST["date_entree_mois"] : "00";
unset($date_entree_annee);
$date_entree_annee = isset($_POST["date_entree_annee"]) ? $_POST["date_entree_annee"] : "0000";

//=========================
// AJOUT: boireaus 20071107
unset($reg_regime);
$reg_regime = isset($_POST["reg_regime"]) ? $_POST["reg_regime"] : NULL;
unset($reg_doublant);
$reg_doublant = isset($_POST["reg_doublant"]) ? $_POST["reg_doublant"] : NULL;

//echo "\$reg_regime=$reg_regime<br />";
//echo "\$reg_doublant=$reg_doublant<br />";

//=========================

//debug_var();

// Témoin pour index_call_data.php
$page_courante="modify_eleve";

//=========================
$modif_adr_pers_id=isset($_GET["modif_adr_pers_id"]) ? $_GET["modif_adr_pers_id"] : NULL;
$adr_id=isset($_GET["adr_id"]) ? $_GET["adr_id"] : NULL;
//=========================


unset($reg_resp1);
$reg_resp1 = isset($_POST["reg_resp1"]) ? $_POST["reg_resp1"] : NULL;
unset($reg_resp2);
$reg_resp2 = isset($_POST["reg_resp2"]) ? $_POST["reg_resp2"] : NULL;

unset($reg_etab);
$reg_etab = isset($_POST["reg_etab"]) ? $_POST["reg_etab"] : NULL;

unset($mode);
$mode = isset($_POST["mode"]) ? $_POST["mode"] : (isset($_GET["mode"]) ? $_GET["mode"] : NULL);
unset($order_type);
$order_type = isset($_POST["order_type"]) ? $_POST["order_type"] : (isset($_GET["order_type"]) ? $_GET["order_type"] : NULL);
unset($quelles_classes);
$quelles_classes = isset($_POST["quelles_classes"]) ? $_POST["quelles_classes"] : (isset($_GET["quelles_classes"]) ? $_GET["quelles_classes"] : NULL);
unset($eleve_login);
$eleve_login = isset($_POST["eleve_login"]) ? $_POST["eleve_login"] : (isset($_GET["eleve_login"]) ? $_GET["eleve_login"] : NULL);
//echo "\$eleve_login=$eleve_login<br />";

$definir_resp = isset($_POST["definir_resp"]) ? $_POST["definir_resp"] : (isset($_GET["definir_resp"]) ? $_GET["definir_resp"] : NULL);
if(($definir_resp!=1)&&($definir_resp!=2)){$definir_resp=NULL;}

$definir_etab = isset($_POST["definir_etab"]) ? $_POST["definir_etab"] : (isset($_GET["definir_etab"]) ? $_GET["definir_etab"] : NULL);


//=========================
// Pour l'arrivée depuis la page index.php suite à une recherche
$motif_rech=isset($_POST['motif_rech']) ? $_POST['motif_rech'] : (isset($_GET['motif_rech']) ? $_GET['motif_rech'] : NULL);
$mode_rech=isset($_POST['mode_rech']) ? $_POST['mode_rech'] : (isset($_GET['mode_rech']) ? $_GET['mode_rech'] : NULL);
if((isset($quelles_classes))&&(isset($mode_rech))&&($mode_rech=='contient')) {
	// On initialise des variables pour index_call_data.php
	if($quelles_classes=='recherche') {
		$mode_rech_nom="contient";
	}
	elseif($quelles_classes=='rech_prenom') {
		$mode_rech_prenom="contient";
	}
	elseif($quelles_classes=='rech_elenoet') {
		$mode_rech_elenoet="contient";
	}
	elseif($quelles_classes=='rech_ele_id') {
		$mode_rech_ele_id="contient";
	}
	elseif($quelles_classes=='rech_no_gep') {
		$mode_rech_no_gep="contient";
	}
}
//=========================
//echo "\$motif_rech=$motif_rech<br />";

$journal_connexions=isset($_POST['journal_connexions']) ? $_POST['journal_connexions'] : (isset($_GET['journal_connexions']) ? $_GET['journal_connexions'] : 'n');
$duree=isset($_POST['duree']) ? $_POST['duree'] : NULL;

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

if(!isset($eleve_login)) {
    header("Location: ./index.php?msg=Élève non choisi.");
    die();
}

if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
	// On récupère le RNE de l'établissement
	$rep_photos="../photos/".$_COOKIE['RNE']."/eleves/";
} else {
	$rep_photos="../photos/eleves/";
}

if(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite")) {
	// Le deuxième responsable prend l'adresse du premier
	if((isset($modif_adr_pers_id))&&(isset($adr_id))) {
		check_token();
		$sql="UPDATE resp_pers SET adr_id='$adr_id' WHERE pers_id='$modif_adr_pers_id';";
		$update=mysql_query($sql);
		if(!$update){
			$msg="Echec de la modification de l'adresse du deuxième responsable.";
		}
	}


	/*
	foreach($_POST as $post => $val){
		echo $post.' : '.$val."<br />\n";
	}

	echo "\$eleve_login=$eleve_login<br />";
	echo "\$valider_choix_resp=$valider_choix_resp<br />";
	echo "\$definir_resp=$definir_resp<br />";
	*/
	// Validation d'un choix de responsable
	if((isset($eleve_login))&&(isset($definir_resp))&&(isset($_POST['valider_choix_resp']))) {
		check_token();

		if($definir_resp==1){
			$pers_id=$reg_resp1;
		}
		else{
			$pers_id=$reg_resp2;
		}

		if($pers_id==""){
			// Recherche de l'ele_id
			$sql="SELECT ele_id FROM eleves WHERE login='$eleve_login'";
			$res_ele=mysql_query($sql);
			if(mysql_num_rows($res_ele)==0){
				$msg="Erreur: L'élève $eleve_login n'a pas l'air présent dans la table 'eleves'.";
			}
			else{
				$lig_ele=mysql_fetch_object($res_ele);

				$sql="DELETE FROM responsables2 WHERE ele_id='$lig_ele->ele_id' AND resp_legal='$definir_resp'";
				$suppr=mysql_query($sql);
				if($suppr){
					$msg="Suppression de l'association de l'élève avec le responsable $definir_resp réussie.";
				}
				else{
					$msg="Echec de la suppression l'association de l'élève avec le responsable $definir_resp.";
				}
			}
		}
		else{
			$sql="SELECT 1=1 FROM resp_pers WHERE pers_id='$pers_id'";
			$test=mysql_query($sql);

			if(mysql_num_rows($test)==0){
				$msg="Erreur: L'identifiant de responsable proposé n'existe pas.";
			}
			else{
				// Recherche de l'ele_id
				$sql="SELECT ele_id FROM eleves WHERE login='$eleve_login'";
				$res_ele=mysql_query($sql);
				if(mysql_num_rows($res_ele)==0){
					$msg="Erreur: L'élève $eleve_login n'a pas l'air présent dans la table 'eleves'.";
				}
				else{
					$lig_ele=mysql_fetch_object($res_ele);

					//$sql="SELECT 1=1 FROM responsables2 WHERE pers_id='$pers_id' AND ele_id='$lig_ele->ele_id' AND resp_legal='$definir_resp'";
					$sql="SELECT 1=1 FROM responsables2 WHERE ele_id='$lig_ele->ele_id' AND resp_legal='$definir_resp'";
					$test=mysql_query($sql);

					if(mysql_num_rows($test)==0){
						$sql="INSERT INTO responsables2 SET pers_id='$pers_id', ele_id='$lig_ele->ele_id', resp_legal='$definir_resp', pers_contact='1'";
						$insert=mysql_query($sql);
						if($insert){
							$msg="Association de l'élève avec le responsable $definir_resp réussie.";
						}
						else{
							$msg="Echec de l'association de l'élève avec le responsable $definir_resp.";
						}
					}
					else{
						$sql="UPDATE responsables2 SET pers_id='$pers_id' WHERE ele_id='$lig_ele->ele_id' AND resp_legal='$definir_resp'";
						$update=mysql_query($sql);
						if($update){
							$msg="Association de l'élève avec le responsable $definir_resp réussie.";
						}
						else{
							$msg="Echec de l'association de l'élève avec le responsable $definir_resp.";
						}
					}
				}
			}
		}
		unset($definir_resp);
	}

	//debug_var();

	// Validation d'un choix d'établissement d'origine
	if((isset($eleve_login))&&(isset($definir_etab))&&(isset($_POST['valider_choix_etab']))) {
		check_token();
	//if((isset($eleve_login))&&(isset($reg_no_gep))&&($reg_no_gep!="")&&(isset($definir_etab))&&(isset($_POST['valider_choix_etab']))) {

		$sql="SELECT elenoet FROM eleves WHERE login='$eleve_login';";
		$res_elenoet=mysql_query($sql);
		if(mysql_num_rows($res_elenoet)>0) {
			$lig_elenoet=mysql_fetch_object($res_elenoet);
			$reg_no_gep=$lig_elenoet->elenoet;
			if($reg_no_gep!="") {
				if($reg_etab==""){
					//$sql="DELETE FROM j_eleves_etablissements WHERE id_eleve='$eleve_login'";
					$sql="DELETE FROM j_eleves_etablissements WHERE id_eleve='$reg_no_gep'";
					$suppr=mysql_query($sql);
					if($suppr){
						$msg="Suppression de l'association de l'élève avec un établissement réussie.";
					}
					else{
						$msg="Echec de la suppression l'association de l'élève avec un établissement.";
					}
				}
				else{
					$sql="SELECT 1=1 FROM etablissements WHERE id='$reg_etab'";
					//echo "$sql<br />";
					$test=mysql_query($sql);

					if(mysql_num_rows($test)==0){
						$msg="Erreur: L'établissement choisi (<i>$reg_etab</i>) n'existe pas dans la table 'etablissement'.";
					}
					else{
						//$sql="SELECT 1=1 FROM j_eleves_etablissements WHERE id_eleve='$eleve_login'";
						$sql="SELECT 1=1 FROM j_eleves_etablissements WHERE id_eleve='$reg_no_gep'";
						$test=mysql_query($sql);

						if(mysql_num_rows($test)==0){
							//$sql="INSERT INTO j_eleves_etablissements SET id_eleve='$eleve_login', id_etablissement='$reg_etab'";
							$sql="INSERT INTO j_eleves_etablissements SET id_eleve='$reg_no_gep', id_etablissement='$reg_etab'";
							$insert=mysql_query($sql);
							if($insert){
								$msg="Association de l'élève avec l'établissement $reg_etab réussie.";
							}
							else{
								$msg="Echec de l'association de l'élève avec l'établissement $reg_etab.";
							}
						}
						else{
							//$sql="UPDATE j_eleves_etablissements SET id_etablissement='$reg_etab' WHERE id_eleve='$eleve_login'";
							$sql="UPDATE j_eleves_etablissements SET id_etablissement='$reg_etab' WHERE id_eleve='$reg_no_gep'";
							$update=mysql_query($sql);
							if($update){
								$msg="Association de l'élève avec l'établissement $reg_etab réussie.";
							}
							else{
								$msg="Echec de l'association de l'élève avec l'établissement $reg_etab.";
							}
						}
					}
				}
			}
		}
		unset($definir_etab);
	}


	//================================================
	// Validation de modifications dans le formulaire de nom, prénom,...
	if (isset($_POST['is_posted']) and ($_POST['is_posted'] == "1")) {
		check_token();

		// Détermination du format de la date de naissance
		$call_eleve_test = mysql_query("SELECT naissance FROM eleves WHERE 1");
		$test_eleve_naissance = @mysql_result($call_eleve_test, "0", "naissance");
		$format = mb_strlen($test_eleve_naissance);


		// Cas de la création d'un élève
		$reg_nom = trim($reg_nom);
		$reg_prenom = trim($reg_prenom);
		$reg_email = trim($reg_email);
		if ($reg_resp1 == '(vide)') $reg_resp1 = '';
		if (!preg_match ("/^[0-9]{4}$/", $birth_year)) {$birth_year = "1900";}
		if (!preg_match ("/^[0-9]{2}$/", $birth_month)) {$birth_month = "01";}
		if (!preg_match ("/^[0-9]{2}$/", $birth_day)) {$birth_day = "01";}
		if ($format == '10') {
			// YYYY-MM-DD
			$reg_naissance = $birth_year."-".$birth_month."-".$birth_day." 00:00:00";
		}
		else {
			if ($format == '8') {
				// YYYYMMDD
				$reg_naissance = $birth_year.$birth_month.$birth_day;
				settype($reg_naissance,"integer");
			} else {
				// Format inconnu
				$reg_naissance = $birth_year.$birth_month.$birth_day;
			}
		}
		
		//gestion de la date de sortie de l'élève
		//echo "date_sortie_annee=".$date_sortie_annee."<br/>";
		//echo "date_sortie_mois=".$date_sortie_mois."<br/>";
		//echo "date_sortie_jour=".$date_sortie_jour."<br/>";
		if (!preg_match ("/^[0-9]{4}$/", $date_sortie_annee)) {$date_sortie_annee = "0000";}
		if (!preg_match ("/^[0-9]{1,2}$/", $date_sortie_mois)) {$date_sortie_mois = "00";}
		if (!preg_match ("/^[0-9]{1,2}$/", $date_sortie_jour)) {$date_sortie_jour = "00";}
		//echo "date_sortie_annee=".$date_sortie_annee."<br/>";
		//echo "date_sortie_mois=".$date_sortie_mois."<br/>";
		//echo "date_sortie_jour=".$date_sortie_jour."<br/>";

		//création de la chaine au format timestamp
		$date_de_sortie_eleve = $date_sortie_annee."-".$date_sortie_mois."-".$date_sortie_jour." 00:00:00"; 
		
		//gestion de la date d'entrée de l'élève
		if (!preg_match ("/^[0-9]{4}$/", $date_entree_annee)) {$date_entree_annee = "0000";}
		if (!preg_match ("/^[0-9]{1,2}$/", $date_entree_mois)) {$date_entree_mois = "00";}
		if (!preg_match ("/^[0-9]{1,2}$/", $date_entree_jour)) {$date_entree_jour = "00";}
		//création de la chaine au format timestamp
		$date_entree_eleve = $date_entree_annee."-".$date_entree_mois."-".$date_entree_jour." 00:00:00"; 
		
		//===========================
		//AJOUT:
		if(!isset($msg)){$msg="";}
		//===========================

		$continue = 'yes';
		if (($reg_nom == '') or ($reg_prenom == '')) {
			$msg = "Les champs nom et prénom sont obligatoires.";
			$continue = 'no';
		}

		//$msg.="\$reg_login=$reg_login<br />";
		//if(isset($eleve_login)){$msg.="\$eleve_login=$eleve_login<br />";}

		// $reg_login non vide correspond à un nouvel élève.
		// On a saisi un login avant de valider
		if (($continue == 'yes') and (isset($reg_login))) {
			// CE CAS NE DOIT PLUS SE PRODUIRE PUISQUE J'AI AJOUTé UNE PAGE add_eleve.php D'APRES L'ANCIENNE modify_eleve.php
			// On doit nécessairement passer dans le else plus bas...

			//echo "\$reg_login=$reg_login<br/>";

			$msg = '';
			$ok = 'yes';
			if (preg_match("/^[a-zA-Z_]{1}[a-zA-Z0-9_]{0,11}$/", $reg_login)) {
				if ($reg_no_gep != '') {
					$test1 = mysql_query("SELECT login FROM eleves WHERE elenoet='$reg_no_gep'");
					$count1 = mysql_num_rows($test1);
					if ($count1 != "0") {
						//$msg .= "Erreur : un élève ayant le même numéro GEP existe déjà.<br />";
						$msg .= "Erreur : un élève ayant le même numéro interne Sconet (elenoet) existe déjà.<br />";
						$ok = 'no';
					}
				}

				if ($reg_no_nat != '') {
					$test2 = mysql_query("SELECT login FROM eleves WHERE no_gep='$reg_no_nat'");
					$count2 = mysql_num_rows($test2);
					if ($count2 != "0") {
						$msg .= "Erreur : un élève ayant le même numéro national existe déjà.";
						$ok = 'no';
					}
				}

				if ($ok == 'yes') {
					$test = mysql_query("SELECT login FROM eleves WHERE login='$reg_login'");
					$count = mysql_num_rows($test);
					if ($count == "0") {

						if(!isset($ele_id)){
							// GENERER UN ele_id...
							/*
							$sql="SELECT MAX(ele_id) max_ele_id FROM eleves";
							$res_ele_id_eleve=mysql_query($sql);
							$max_ele_id = mysql_result($call_resp , 0, "max_ele_id");

							$sql="SELECT MAX(ele_id) max_ele_id FROM responsables2";
							$res_ele_id_responsables2=mysql_query($sql);
							$max_ele_id2 = mysql_result($call_resp , 0, "max_ele_id");

							if($max_ele_id2>$max_ele_id){$max_ele_id=$max_ele_id2;}
							$ele_id=$max_ele_id+1;
							*/
							// PB si on fait ensuite un import sconet le pers_id risque de ne pas correspondre... de provoquer des collisions.
							// QUAND ON LES METS A LA MAIN, METTRE UN ele_id, pers_id,... négatifs?

							// PREFIXER D'UN a...

							$sql="SELECT ele_id FROM eleves WHERE ele_id LIKE 'e%' ORDER BY ele_id DESC";
							$res_ele_id_eleve=mysql_query($sql);
							if(mysql_num_rows($res_ele_id_eleve)>0){
								$tmp=0;
								$lig_ele_id_eleve=mysql_fetch_object($res_ele_id_eleve);
								$tmp=mb_substr($lig_ele_id_eleve->ele_id,1);
								$tmp++;
								$max_ele_id=$tmp;
							}
							else{
								$max_ele_id=1;
							}

							$sql="SELECT ele_id FROM responsables2 WHERE ele_id LIKE 'e%' ORDER BY ele_id DESC";
							$res_ele_id_responsables2=mysql_query($sql);
							if(mysql_num_rows($res_ele_id_responsables2)>0){
								$tmp=0;
								$lig_ele_id_responsables2=mysql_fetch_object($res_ele_id_responsables2);
								$tmp=mb_substr($lig_ele_id_responsables2->ele_id,1);
								$tmp++;
								$max_ele_id2=$tmp;
							}
							else{
								$max_ele_id2=1;
							}

							$tmp=max($max_ele_id,$max_ele_id2);
							$ele_id="e".sprintf("%09d",max($max_ele_id,$max_ele_id2));
						}

						/*
						$reg_data1 = mysql_query("INSERT INTO eleves SET
							no_gep = '".$reg_no_nat."',
							nom='".$reg_nom."',
							prenom='".$reg_prenom."',
							login='".$reg_login."',
							sexe='".$reg_sexe."',
							naissance='".$reg_naissance."',
							elenoet = '".$reg_no_gep."',
							ereno = '".$reg_resp1."',
							ele_id = '".$ele_id."'
							");
						*/
						$sql="INSERT INTO eleves SET
							no_gep = '".$reg_no_nat."',
							nom='".$reg_nom."',
							prenom='".$reg_prenom."',
							email='".$reg_email ."',
							login='".$reg_login."',
							sexe='".$reg_sexe."',
							naissance='".$reg_naissance."',
							elenoet = '".$reg_no_gep."',
							ele_id = '".$ele_id."'";
						if(isset($reg_mef_code)) {$sql.=",mef_code='".$reg_mef_code."'";}
						if(isset($reg_tel_pers)) {$sql.=",tel_pers='".$reg_tel_pers."'";}
						if(isset($reg_tel_port)) {$sql.=",tel_port='".$reg_tel_port."'";}
						if(isset($reg_tel_prof)) {$sql.=",tel_prof='".$reg_tel_prof."'";}
						$reg_data1 = mysql_query();

						// Régime:
						$reg_data3 = mysql_query("INSERT INTO j_eleves_regime SET login='$reg_login', doublant='-', regime='d/p'");
						if ((!$reg_data1) or (!$reg_data3)) {
							$msg = "Erreur lors de l'enregistrement des données";
						} elseif ($mode == "unique") {
							$mess=rawurlencode("Elève enregistré !");
							header("Location: index.php?msg=$mess");
							die();
						} elseif ($mode == "multiple") {
							$mess=rawurlencode("Elève enregistré.Vous pouvez saisir l'élève suivant.");
							header("Location: modify_eleve.php?mode=multiple&msg=$mess");
							die();
						}
					} else {
						$msg="Un élève portant le même identifiant existe déja !";
					}
				}
			} else {
				$msg="L'identifiant choisi est constitué au maximum de 12 caractères : lettres, chiffres ou \"_\" et ne doit pas commencer par un chiffre !";
			}
		} else if ($continue == 'yes') {
			// C'est une mise à jour pour un élève qui existait déjà dans la table 'eleves'.
			$sql="UPDATE eleves SET date_sortie = '$date_de_sortie_eleve', date_entree = '$date_entree_eleve', no_gep = '$reg_no_nat', nom='$reg_nom',prenom='$reg_prenom',sexe='$reg_sexe',naissance='".$reg_naissance."', ereno='".$reg_resp1."', elenoet = '".$reg_no_gep."'";

			if(isset($reg_tel_pers)) {$sql.=",tel_pers='".$reg_tel_pers."'";}
			if(isset($reg_tel_port)) {$sql.=",tel_port='".$reg_tel_port."'";}
			if(isset($reg_tel_prof)) {$sql.=",tel_prof='".$reg_tel_prof."'";}
			if(isset($reg_mef_code)) {$sql.=",mef_code='".$reg_mef_code."'";}

			$temoin_mon_compte_mais_pas_de_compte_pour_cet_eleve="n";
			$sql_test="SELECT email FROM utilisateurs WHERE login='$eleve_login' AND statut='eleve';";
			$res_email_utilisateur_ele=mysql_query($sql_test);
			if(mysql_num_rows($res_email_utilisateur_ele)==0) {
				$temoin_mon_compte_mais_pas_de_compte_pour_cet_eleve="y";
			}

			/*
			if(getSettingValue('mode_email_ele')=='mon_compte') {
				$sql_test="SELECT email FROM utilisateurs WHERE login='$eleve_login' AND statut='eleve';";
				$res_email_utilisateur_ele=mysql_query($sql_test);
				if(mysql_num_rows($res_email_utilisateur_ele)>0) {
					// Faut-il insérer un email? si l'email utilisateur est vide?
				}
				else {
					$sql.=",email='$reg_email'";
					$temoin_mon_compte_mais_pas_de_compte_pour_cet_eleve="y";
				}
			}
			else {
			*/
				$sql.=",email='$reg_email'";
			//}
			$sql.=" WHERE login='".$eleve_login."'";

			$reg_data = mysql_query($sql);
			if (!$reg_data) {
				$msg = "Erreur lors de l'enregistrement des données";
			}
			//elseif((getSettingValue('mode_email_ele')!='mon_compte')||($temoin_mon_compte_mais_pas_de_compte_pour_cet_eleve=="y")) {
			else {
				/*
				// On met à jour la table utilisateurs si un compte existe pour cet élève
				$test_login = mysql_result(mysql_query("SELECT count(login) FROM utilisateurs WHERE login = '".$eleve_login ."'"), 0);
				if ($test_login > 0) {
				*/
				if($temoin_mon_compte_mais_pas_de_compte_pour_cet_eleve=='n') {

					$res = mysql_query("UPDATE utilisateurs SET nom='".$reg_nom."', prenom='".$reg_prenom."', email='".$reg_email."', auth_mode='$reg_auth_mode' WHERE login = '".$eleve_login."'");
					//$msg.="TEMOIN test_login puis update<br />";
				}
			}

			if ($date_sortie_annee != "0000") {
				// On a une date de sortie, on met à jour la table d'agrégation
				require_once("../lib/initialisationsPropel.inc.php");
				$eleve = EleveQuery::create()->findOneByLogin($eleve_login);
				$eleve->updateAbsenceAgregationTable();//pas besoin de sauver dateSortie, c'est déjà fait en mysql ligne 492
			}


			// Corriger le compte d'utilisateur
			$sql="UPDATE utilisateurs SET nom='$reg_nom', prenom='$reg_prenom', civilite='".(($reg_sexe=='M') ? 'M.' : 'Mlle')."' WHERE login = '".$eleve_login."' AND statut='eleve';";
			$update_utilisateur=mysql_query($sql);


			if(isset($reg_doublant)){
				if ($reg_doublant!='R') {$reg_doublant = '-';}

				$call_regime = mysql_query("SELECT * FROM j_eleves_regime WHERE login='$eleve_login'");
				$nb_test_regime = mysql_num_rows($call_regime);
				if ($nb_test_regime == 0) {
					// On va se retrouver éventuellement avec un régime vide... cela peut-il poser pb?
					$reg_data = mysql_query("INSERT INTO j_eleves_regime SET login='$eleve_login', doublant='$reg_doublant';");
					if (!($reg_data)) {$reg_ok = 'no';}
				} else {
					$reg_data = mysql_query("UPDATE j_eleves_regime SET doublant = '$reg_doublant' WHERE login='$eleve_login';");
					if (!($reg_data)) {$reg_ok = 'no';}
				}
			}

			if(isset($reg_regime)){
				if (($reg_regime!='i-e')&&($reg_regime!='int.')&&($reg_regime!='ext.')&&($reg_regime!='d/p')) {
					$reg_regime='d/p';
				}

				$call_regime = mysql_query("SELECT * FROM j_eleves_regime WHERE login='$eleve_login'");
				$nb_test_regime = mysql_num_rows($call_regime);
				if ($nb_test_regime == 0) {
					$reg_data = mysql_query("INSERT INTO j_eleves_regime SET login='$eleve_login', regime='$reg_regime'");
					if (!($reg_data)) {$reg_ok = 'no';}
				} else {
					$reg_data = mysql_query("UPDATE j_eleves_regime SET regime = '$reg_regime'  WHERE login='$eleve_login'");
					if (!($reg_data)) {$reg_ok = 'no';}
				}
			}



			/*
			$call_test = mysql_query("SELECT * FROM j_eleves_etablissements WHERE id_eleve = '$eleve_login'");
			$count = mysql_num_rows($call_test);
			if ($count == "0") {
				if ($reg_etab != "(vide)") {
					$reg_data = mysql_query("INSERT INTO j_eleves_etablissements VALUES ('$eleve_login','$reg_etab')");
				}
			} else {
				if ($reg_etab != "(vide)") {
					$reg_data = mysql_query("UPDATE j_eleves_etablissements SET id_etablissement = '$reg_etab' WHERE id_eleve='$eleve_login'");
				} else {
					$reg_data = mysql_query("DELETE FROM j_eleves_etablissements WHERE id_eleve='$eleve_login'");
				}
			}
			*/

			if (!$reg_data) {
				$msg = "Erreur lors de l'enregistrement des données ! ";
			} else {
				//$msg = "Les modifications ont bien été enregistrées !";
				// MODIF POUR AFFICHER MES TEMOINS...
				$msg .= "Les modifications ont bien été enregistrées ! ";
			}


			// Envoi de la photo
			if(isset($reg_no_gep)){
				//echo "\$reg_no_gep=$reg_no_gep<br />";
				if($reg_no_gep!=""){
					if(mb_strlen(preg_replace("/[0-9]/","",$reg_no_gep))==0) {
						if(isset($_POST['suppr_filephoto'])){
							if($_POST['suppr_filephoto']=='y'){

								// Récupération du nom de la photo en tenant compte des histoires des zéro 02345.jpg ou 2345.jpg
								$photo=nom_photo($reg_no_gep);
/*
								if("$photo"!=""){
									if(unlink("../photos/eleves/$photo")){
 */
								if($photo){
									if(unlink($photo)){
										$msg.="La photo ".$photo." a été supprimée. ";
									}
									else{
										$msg.="Echec de la suppression de la photo ".$photo." ";
									}
								}
								else{
									$msg.="Echec de la suppression de la photo correspondant à $reg_no_gep (<i>non trouvée</i>) ";
								}
							}
						}

						// Contrôler qu'un seul élève a bien cet elenoet???
						$sql="SELECT 1=1 FROM eleves WHERE elenoet='$reg_no_gep'";
						$test=mysql_query($sql);
						$nb_elenoet=mysql_num_rows($test);
						if($nb_elenoet==1){
							// filephoto
							if(isset($_FILES['filephoto'])){
								$filephoto_tmp=$_FILES['filephoto']['tmp_name'];
								if($filephoto_tmp!=""){
									$filephoto_name=$_FILES['filephoto']['name'];
									$filephoto_size=$_FILES['filephoto']['size'];
									// Tester la taille max de la photo?

									if(is_uploaded_file($filephoto_tmp)){
										$dest_file=$rep_photos.encode_nom_photo($reg_no_gep).".jpg";
										//echo "\$dest_file=$dest_file<br />";
										$source_file=$filephoto_tmp;
										$res_copy=copy("$source_file" , "$dest_file");
										if($res_copy){
											$msg.="Mise en place de la photo effectuée.";
											if (getSettingValue("active_module_trombinoscopes_rd")=='y') {
												// si le redimensionnement des photos est activé on redimensionne
												if (getSettingValue("active_module_trombinoscopes_rt")!='')
													$redim_OK=redim_photo($dest_file,getSettingValue("l_resize_trombinoscopes"), getSettingValue("h_resize_trombinoscopes"),getSettingValue("active_module_trombinoscopes_rt"));
												else
													$redim_OK=redim_photo($dest_file,getSettingValue("l_resize_trombinoscopes"), getSettingValue("h_resize_trombinoscopes"));
												if (!$redim_OK) $msg .= " Echec du redimensionnement de la photo.";
											}
										}
										else{
											$msg.="Erreur lors de la mise en place de la photo.";
										}
									}
									else{
										$msg.="Erreur lors de l'upload de la photo.";
									}
								}
							}
						}
						elseif($nb_elenoet==0){
								//$msg.="Le numéro GEP de l'élève n'est pas enregistré dans la table 'eleves'.";
								$msg.="Le numéro interne Sconet (elenoet) de l'élève n'est pas enregistré dans la table 'eleves'.";
						}
						else{
							//$msg.="Le numéro GEP est commun à plusieurs élèves. C'est une anomalie.";
							$msg.="Le numéro interne Sconet (elenoet) est commun à plusieurs élèves. C'est une anomalie.";
						}
					}
					else{
						//$msg.="Le numéro GEP proposé contient des caractères non numériques.";
						$msg.="Le numéro interne Sconet (elenoet) proposé contient des caractères non numériques.";
					}
				}
			}


			$temoin_ele_id="";
			$sql="SELECT ele_id FROM eleves WHERE login='$eleve_login'";
			$res_ele_id_eleve=mysql_query($sql);
			if(mysql_num_rows($res_ele_id_eleve)==0){
				$msg.="Erreur: Le champ ele_id n'est pas présent. Votre table 'eleves' n'a pas l'air à jour.<br />";
				$temoin_ele_id="PB";
			}
			else{
				$lig_tmp=mysql_fetch_object($res_ele_id_eleve);
				$ele_id=$lig_tmp->ele_id;
			}
		}
	}

	//================================================
}
elseif(($_SESSION['statut']=="professeur")||($_SESSION['statut']=="cpe")) {
	if (isset($_POST['is_posted']) and ($_POST['is_posted'] == "1")) {
		if(!isset($msg)){$msg="";}

		//debug_var();

		// En cpe ou prof, on n'a pas accès à la modification de la fiche... donc pas de reg_no_gep
		/*
		$sql="SELECT 1=1 FROM eleves WHERE login='$eleve_login' AND elenoet='$reg_no_gep';";
		//echo "$sql<br />";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)==0) {
			tentative_intrusion("2", "Tentative d'upload par un ".$_SESSION['statut']." de la photo d'un élève ($eleve_login) pour un elenoet ($reg_no_gep) ne correspondant pas à cet élève.");
			echo "Incohérence entre le login élève et son numéro elenoet.";
			require ("../lib/footer.inc.php");
			die();
		}
		else {
		*/
		$sql="SELECT elenoet FROM eleves WHERE login='$eleve_login' AND elenoet!='';";
		//echo "$sql<br />";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)==0) {
			$msg.="L'élève n'a pas d'elenoet.<br />La mise en place de la photo n'est pas possible.<br />";
		}
		else {
			$reg_no_gep=mysql_result($test,0,"elenoet");

			// Envoi de la photo
			if((isset($reg_no_gep))&&(isset($eleve_login))) {
				if($reg_no_gep!="") {
					if(mb_strlen(preg_replace("/[0-9]/","",$reg_no_gep))==0) {
						if(($_SESSION['statut']=='professeur')&&(getSettingValue("GepiAccesGestPhotoElevesProfP")!='yes')) {
							tentative_intrusion("2", "Tentative d'upload par un professeur de la photo d'un élève ($eleve_login), sans avoir l'autorisation d'upload.");
							echo "L'upload de photo n'est pas autorisé pour les professeurs.";
							require ("../lib/footer.inc.php");
							die();
						}
						elseif(($_SESSION['statut']=='cpe')&&(getSettingValue("CpeAccesUploadPhotosEleves")!='yes')) {
							tentative_intrusion("2", "Tentative d'upload par un cpe de la photo d'un élève ($eleve_login), sans avoir l'autorisation d'upload.");
							echo "L'upload de photo n'est pas autorisé pour les cpe.";
							require ("../lib/footer.inc.php");
							die();
						}
						else {
							if(($_SESSION['statut']=='cpe')||(is_cpe($_SESSION['login'],"",$eleve_login))) {
								if(isset($_POST['suppr_filephoto'])) {
									check_token();
									if($_POST['suppr_filephoto']=='y'){

										// Récupération du nom de la photo en tenant compte des histoires des zéro 02345.jpg ou 2345.jpg
										$photo=nom_photo($reg_no_gep);
										if($photo){
											if(unlink($photo)){
												$msg.="La photo ".$photo." a été supprimée. ";
											}
											else{
												$msg.="Echec de la suppression de la photo ".$photo." ";
											}
										}
										else{
											$msg.="Echec de la suppression de la photo correspondant à $reg_no_gep (<i>non trouvée</i>) ";
										}
									}
								}

								// Contrôler qu'un seul élève a bien cet elenoet???
								$sql="SELECT 1=1 FROM eleves WHERE elenoet='$reg_no_gep'";
								$test=mysql_query($sql);
								$nb_elenoet=mysql_num_rows($test);
								if($nb_elenoet==1){
									// filephoto
									if(isset($_FILES['filephoto'])){
										check_token();
										$filephoto_tmp=$_FILES['filephoto']['tmp_name'];
										if($filephoto_tmp!=""){
											$filephoto_name=$_FILES['filephoto']['name'];
											$filephoto_size=$_FILES['filephoto']['size'];
											// Tester la taille max de la photo?

											if(is_uploaded_file($filephoto_tmp)){
												$dest_file=$rep_photos.encode_nom_photo($reg_no_gep).".jpg";
												//echo "\$dest_file=$dest_file<br />";
												$source_file=$filephoto_tmp;
												$res_copy=copy("$source_file" , "$dest_file");
												if($res_copy){
													$msg.="Mise en place de la photo effectuée.";
												}
												else{
													$msg.="Erreur lors de la mise en place de la photo.";
												}

												if (getSettingValue("active_module_trombinoscopes_rd")=='y') {
													// si le redimensionnement des photos est activé on redimenssionne
													$source = imagecreatefromjpeg($dest_file); // La photo est la source

													if (getSettingValue("active_module_trombinoscopes_rt")=='') {
														$destination = imagecreatetruecolor(getSettingValue("l_resize_trombinoscopes"), getSettingValue("h_resize_trombinoscopes"));
													} // On crée la miniature vide

													if (getSettingValue("active_module_trombinoscopes_rt")!='') {
														$destination = imagecreatetruecolor(getSettingValue("h_resize_trombinoscopes"), getSettingValue("l_resize_trombinoscopes"));
													} // On crée la miniature vide

													// Les fonctions imagesx et imagesy renvoient la largeur et la hauteur d'une image
													$largeur_source = imagesx($source);
													$hauteur_source = imagesy($source);
													$largeur_destination = imagesx($destination);
													$hauteur_destination = imagesy($destination);

													// On crée la miniature
													imagecopyresampled($destination, $source, 0, 0, 0, 0, $largeur_destination, $hauteur_destination, $largeur_source, $hauteur_source);
													if (getSettingValue("active_module_trombinoscopes_rt")!='') {
														$degrees = getSettingValue("active_module_trombinoscopes_rt");
														// $destination = imagerotate($destination,$degrees);
														$destination = ImageRotateRightAngle($destination,$degrees);
													}
													// On enregistre la miniature sous le nom "mini_couchersoleil.jpg"
													imagejpeg($destination, $dest_file,100);
												}
											}
											else{
												$msg.="Erreur lors de l'upload de la photo.";
											}
										}
									}
								}
								elseif($nb_elenoet==0){
										//$msg.="Le numéro GEP de l'élève n'est pas enregistré dans la table 'eleves'.";
										$msg.="Le numéro interne Sconet (elenoet) de l'élève n'est pas enregistré dans la table 'eleves'.";
								}
								else{
									//$msg.="Le numéro GEP est commun à plusieurs élèves. C'est une anomalie.";
									$msg.="Le numéro interne Sconet (elenoet) est commun à plusieurs élèves. C'est une anomalie.";
								}
							}
							else {
								tentative_intrusion("2", "Tentative d'upload par un prof de la photo d'un élève ($eleve_login) dont il n'est pas ".getSettingValue('gepi_prof_suivi').".");
								echo "Upload de photo non autorisé : Vous n'êtes pas ".getSettingValue('gepi_prof_suivi')." de cet élève.";
								require ("../lib/footer.inc.php");
								die();
							}
						}
					}
					else{
						//$msg.="Le numéro GEP proposé contient des caractères non numériques.";
						$msg.="Le numéro interne Sconet (elenoet) proposé contient des caractères non numériques.";
					}
				}
			}
		}
	}
}

// On appelle les informations de l'utilisateur pour les afficher :
if (isset($eleve_login)) {
    $call_eleve_info = mysql_query("SELECT * FROM eleves WHERE login='$eleve_login'");
    $eleve_nom = mysql_result($call_eleve_info, "0", "nom");
    $eleve_prenom = mysql_result($call_eleve_info, "0", "prenom");
    $eleve_email = mysql_result($call_eleve_info, "0", "email");

	if(getSettingValue('mode_email_ele')=='mon_compte') {
		$sql_test="SELECT email FROM utilisateurs WHERE login='$eleve_login' AND statut='eleve';";
		$res_email_utilisateur_ele=mysql_query($sql_test);
		if(mysql_num_rows($res_email_utilisateur_ele)>0) {
			$tmp_lig_email=mysql_fetch_object($res_email_utilisateur_ele);

			if($tmp_lig_email->email!="") {
				if($tmp_lig_email->email!=$eleve_email) {
					//check_token();
					$sql="UPDATE eleves SET email='$tmp_lig_email->email' WHERE login='$eleve_login';";
					$update=mysql_query($sql);
					if(!$update) {
						if(!isset($msg)) {$msg="";}
						$msg.="Erreur lors de la mise à jour du mail de l'élève d'après son compte d'utilisateur<br />$eleve_email -&gt; $tmp_lig_email->email<br />";
					}
					else {
						if(!isset($msg)) {$msg="";}
						$msg.="Mise à jour de l'email de $eleve_login dans la table 'eleves' d'après l'email de son compte utilisateur<br />$eleve_email -&gt; $tmp_lig_email->email<br />";
					}
				}
				$eleve_email = $tmp_lig_email->email;
			}
		}
	}

    $eleve_sexe = mysql_result($call_eleve_info, "0", "sexe");
    $eleve_naissance = mysql_result($call_eleve_info, "0", "naissance");
    if (mb_strlen($eleve_naissance) == 10) {
        // YYYY-MM-DD
        $eleve_naissance_annee = mb_substr($eleve_naissance, 0, 4);
        $eleve_naissance_mois = mb_substr($eleve_naissance, 5, 2);
        $eleve_naissance_jour = mb_substr($eleve_naissance, 8, 2);
    } elseif (mb_strlen($eleve_naissance) == 8 ) {
        // YYYYMMDD
        $eleve_naissance_annee = mb_substr($eleve_naissance, 0, 4);
        $eleve_naissance_mois = mb_substr($eleve_naissance, 4, 2);
        $eleve_naissance_jour = mb_substr($eleve_naissance, 6, 2);
    } elseif (mb_strlen($eleve_naissance) == 19 ) {
        // YYYY-MM-DD xx:xx:xx
        $eleve_naissance_annee = mb_substr($eleve_naissance, 0, 4);
        $eleve_naissance_mois = mb_substr($eleve_naissance, 5, 2);
        $eleve_naissance_jour = mb_substr($eleve_naissance, 8, 2);
    } else {
        // Format inconnu
        $eleve_naissance_annee = "??";
        $eleve_naissance_mois = "??";
        $eleve_naissance_jour = "????";
    }

    $eleve_lieu_naissance = mysql_result($call_eleve_info, "0", "lieu_naissance");

	//=======================================
	//Date de sortie de l'élève (timestamps), à zéro par défaut
	$eleve_date_de_sortie =mysql_result($call_eleve_info, "0", "date_sortie"); 

	//echo "Date de sortie de l'élève dans la base :  $eleve_date_de_sortie <br/>";
	//conversion en seconde (timestamp)
	$eleve_date_de_sortie_time=strtotime($eleve_date_de_sortie);

	if ($eleve_date_de_sortie!=0) {
		//récupération du jour, du mois et de l'année
		$eleve_date_sortie_jour=date('d', $eleve_date_de_sortie_time); 
		$eleve_date_sortie_mois=date('m', $eleve_date_de_sortie_time);
		$eleve_date_sortie_annee=date('Y', $eleve_date_de_sortie_time); 
		//echo "La date n'est pas nulle J:$eleve_date_sortie_jour   M:$eleve_date_sortie_mois   A:$eleve_date_sortie_annee";
	} else {
		$eleve_date_sortie_jour="00"; 
		$eleve_date_sortie_mois="00";
		$eleve_date_sortie_annee="0000"; 
	}
	//=======================================
	// Date d'entrée de l'élève dans l'établissement
	$eleve_date_entree =mysql_result($call_eleve_info, "0", "date_entree"); 
	$eleve_date_entree_time=strtotime($eleve_date_entree);
	if ($eleve_date_entree!=0) {
	//récupération du jour, du mois et de l'année
		$eleve_date_entree_jour=date('d', $eleve_date_entree_time); 
		$eleve_date_entree_mois=date('m', $eleve_date_entree_time);
		$eleve_date_entree_annee=date('Y', $eleve_date_entree_time); 
	} else {
		$eleve_date_entree_jour="00"; 
		$eleve_date_entree_mois="00";
		$eleve_date_entree_annee="0000"; 
	}
	//=======================================

    //$eleve_no_resp = mysql_result($call_eleve_info, "0", "ereno");
    $reg_no_nat = mysql_result($call_eleve_info, "0", "no_gep");
    $reg_no_gep = mysql_result($call_eleve_info, "0", "elenoet");
	$reg_ele_id = mysql_result($call_eleve_info, "0", "ele_id");
	$reg_mef_code = mysql_result($call_eleve_info, "0", "mef_code");

	$reg_tel_pers = mysql_result($call_eleve_info, "0", "tel_pers");
	$reg_tel_port = mysql_result($call_eleve_info, "0", "tel_port");
	$reg_tel_prof = mysql_result($call_eleve_info, "0", "tel_prof");

    //$call_etab = mysql_query("SELECT e.* FROM etablissements e, j_eleves_etablissements j WHERE (j.id_eleve='$eleve_login' and e.id = j.id_etablissement)");
    $id_etab=0;
	if($reg_no_gep!="") {
		$call_etab = mysql_query("SELECT e.* FROM etablissements e, j_eleves_etablissements j WHERE (j.id_eleve='$reg_no_gep' and e.id = j.id_etablissement)");
	    $id_etab = @mysql_result($call_etab, "0", "id");
	}

	//echo "SELECT e.* FROM etablissements e, j_eleves_etablissements j WHERE (j.id_eleve='$eleve_login' and e.id = j.id_etablissement)<br />";

	//=========================
	// AJOUT: boireaus 20071107
	$sql="SELECT * FROM j_eleves_regime WHERE login='$eleve_login';";
	//echo "$sql<br />\n";
	$res_regime=mysql_query($sql);
	if(mysql_num_rows($res_regime)>0) {
		$lig_tmp=mysql_fetch_object($res_regime);
		$reg_regime=$lig_tmp->regime;
		$reg_doublant=$lig_tmp->doublant;
	}
	else {
		$reg_regime="d/p";
		$reg_doublant="-";
	}
	//=========================


	if(!isset($ele_id)){
		$ele_id=mysql_result($call_eleve_info, "0", "ele_id");
	}

	$sql="SELECT pers_id FROM responsables2 WHERE ele_id='$ele_id' AND resp_legal='1'";
	//echo "$sql<br />\n";
	$res_resp1=mysql_query($sql);
	if(mysql_num_rows($res_resp1)>0) {
		$lig_no_resp1=mysql_fetch_object($res_resp1);
		$eleve_no_resp1=$lig_no_resp1->pers_id;
	}
	else {
		$eleve_no_resp1=0;
	}
	//echo "\$eleve_no_resp1=$eleve_no_resp1<br />\n";

	$sql="SELECT pers_id FROM responsables2 WHERE ele_id='$ele_id' AND resp_legal='2'";
	//echo "$sql<br />\n";
	$res_resp2=mysql_query($sql);
	if(mysql_num_rows($res_resp2)>0){
		$lig_no_resp2=mysql_fetch_object($res_resp2);
		$eleve_no_resp2=$lig_no_resp2->pers_id;
	}
	else {
		$eleve_no_resp2=0;
	}


} else {
    if (isset($reg_nom)) {$eleve_nom = $reg_nom;}
    if (isset($reg_prenom)) {$eleve_prenom = $reg_prenom;}
    if (isset($reg_email)) {$eleve_email = $reg_email;}
    if (isset($reg_sexe)) {$eleve_sexe = $reg_sexe;}
    if (isset($reg_no_nat)) {$reg_no_nat = $reg_no_nat;}
    if (isset($reg_no_gep)) {$reg_no_gep = $reg_no_gep;}
    if (isset($birth_year)) {$eleve_naissance_annee = $birth_year;}
    if (isset($birth_month)) {$eleve_naissance_mois = $birth_month;}
    if (isset($birth_day)) {$eleve_naissance_jour = $birth_day;}

    if (isset($reg_lieu_naissance)) {$eleve_lieu_naissance=$reg_lieu_naissance;}

    //$eleve_no_resp = 0;
    $eleve_no_resp1 = 0;
    $eleve_no_resp2 = 0;
    $id_etab = 0;

	//=========================
	// AJOUT: boireaus 20071107
	// On ne devrait pas passer par là.
	// Quand on arrive sur modify_elve.php, le login de l'élève doit exister.
	$reg_regime="d/p";
	$reg_doublant="-";
	//=========================
}


$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Gestion des élèves | Ajouter/Modifier une fiche élève";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

echo "<div align='center'>
	<div id='message_target_blank' style='color:red;'></div>
</div>\n";

/*
if ((isset($order_type)) and (isset($quelles_classes))) {
    echo "<p class=bold><a href=\"index.php?quelles_classes=$quelles_classes&amp;order_type=$order_type\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";
} else {
    echo "<p class=bold><a href=\"index.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";
}
*/

/*
// Désactivé pour permettre de renseigner un ELENOET manquant pour une conversion avec sconet
// Cela a en revanche été conservé sur la page index.php
// On ne devrait donc arriver ici lorsqu'une conversion est réclamée qu'en venant de conversion.php pour remplir un ELENOET
if(!getSettingValue('conv_new_resp_table')){
	$sql="SELECT 1=1 FROM responsables";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0){
		echo "<p>Une conversion des données élèves/responsables est requise.</p>\n";
		echo "<p>Suivez ce lien: <a href='../responsables/conversion.php'>CONVERTIR</a></p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$sql="SHOW COLUMNS FROM eleves LIKE 'ele_id'";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)==0){
		echo "<p>Une conversion des données élèves/responsables est requise.</p>\n";
		echo "<p>Suivez ce lien: <a href='../responsables/conversion.php'>CONVERTIR</a></p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	else{
		$sql="SELECT 1=1 FROM eleves WHERE ele_id=''";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0){
			echo "<p>Une conversion des données élèves/responsables est requise.</p>\n";
			echo "<p>Suivez ce lien: <a href='../responsables/conversion.php'>CONVERTIR</a></p>\n";
			require("../lib/footer.inc.php");
			die();
		}
	}
}
*/


?>
<!--form enctype="multipart/form-data" action="modify_eleve.php" method=post-->
<?php

if(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite")){
	//eleve_login=$eleve_login&amp;definir_resp=1
	if(isset($definir_resp)){
		if(!isset($valider_choix_resp)){

			echo "<p class=bold><a href=\"modify_eleve.php?eleve_login=$eleve_login\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>\n";

			echo "<p>Choix du responsable légal <b>$definir_resp</b> pour <b>".casse_mot($eleve_prenom,'majf2')." ".my_strtoupper($eleve_nom)."</b></p>\n";

			$critere_recherche=isset($_POST['critere_recherche']) ? $_POST['critere_recherche'] : "";
			$afficher_tous_les_resp=isset($_POST['afficher_tous_les_resp']) ? $_POST['afficher_tous_les_resp'] : "n";
			//$critere_recherche=preg_replace("/[^a-zA-ZÀÄÂÉÈÊËÎÏÔÖÙÛÜ½¼Ççàäâéèêëîïôöùûü_ -]/", "", $critere_recherche);
			$critere_recherche=preg_replace("/[^a-zA-Z_ -]/", "%", nettoyer_caracteres_nom($critere_recherche,"a"," _-",""));

			if($critere_recherche==""){
				$critere_recherche=mb_substr($eleve_nom,0,3);
			}

			$nb_resp=isset($_POST['nb_resp']) ? $_POST['nb_resp'] : 20;
			if(mb_strlen(preg_replace("/[0-9]/","",$nb_resp))!=0) {
				$nb_resp=20;
			}
			$num_premier_resp_rech=isset($_POST['num_premier_resp_rech']) ? $_POST['num_premier_resp_rech'] : 0;
			if(mb_strlen(preg_replace("/[0-9]/","",$num_premier_resp_rech))!=0) {
				$num_premier_resp_rech=0;
			}

			echo "<form enctype='multipart/form-data' name='form_rech' action='modify_eleve.php' method='post'>\n";
			echo add_token_field();

			echo "<input type='hidden' name='eleve_login' value='$eleve_login' />\n";
			echo "<input type='hidden' name='definir_resp' value='$definir_resp' />\n";
			echo "<p align='center'><input type='submit' name='filtrage' value='Afficher' /> les ";
			echo "<input type='text' name='nb_resp' value='$nb_resp' size='3' />\n";
			echo " responsables dont le <b>nom</b> contient: ";
			echo "<input type='text' name='critere_recherche' value='$critere_recherche' />\n";
			echo " à partir de l'enregistrement ";
			echo "<input type='text' name='num_premier_resp_rech' value='$num_premier_resp_rech' size='4' />\n";
			echo "</p>\n";


			if (isset($order_type)) echo "<input type=hidden name=order_type value=\"$order_type\" />\n";
			if (isset($quelles_classes)) echo "<input type=hidden name=quelles_classes value=\"$quelles_classes\" />\n";
			if (isset($motif_rech)) echo "<input type=hidden name=motif_rech value=\"$motif_rech\" />\n";
			if (isset($mode_rech)) echo "<input type=hidden name=mode_rech value=\"$mode_rech\" />\n";


			echo "<input type='hidden' name='afficher_tous_les_resp' id='afficher_tous_les_resp' value='n' />\n";
			echo "<p align='center'><input type='button' name='afficher_tous' value='Afficher tous les responsables' onClick=\"document.getElementById('afficher_tous_les_resp').value='y'; document.form_rech.submit();\" /></p>\n";
			echo "</form>\n";


			echo "<form enctype='multipart/form-data' action='modify_eleve.php' method='post'>\n";
			echo add_token_field();

			echo "<input type='hidden' name='eleve_login' value='$eleve_login' />\n";
			echo "<input type='hidden' name='definir_resp' value='$definir_resp' />\n";

			if($definir_resp==1){
				$pers_id=$eleve_no_resp1;
			}
			else{
				$pers_id=$eleve_no_resp2;
			}

			//$sql="SELECT DISTINCT rp.pers_id,rp.nom,rp.prenom,ra.* FROM responsables2 r, resp_adr ra, resp_pers rp WHERE r.pers_id=rp.pers_id AND rp.adr_id=ra.adr_id ORDER BY rp.nom, rp.prenom";
			//$sql="SELECT DISTINCT rp.pers_id,rp.nom,rp.prenom FROM resp_pers rp ORDER BY rp.nom, rp.prenom";
			$sql="SELECT DISTINCT rp.pers_id,rp.nom,rp.prenom FROM resp_pers rp";
			if($afficher_tous_les_resp!='y'){
				if($critere_recherche!=""){
					$sql.=" WHERE rp.nom like '%".$critere_recherche."%'";
				}
			}
			$sql.=" ORDER BY rp.nom, rp.prenom";
			if($afficher_tous_les_resp!='y'){
				$sql.=" LIMIT $num_premier_resp_rech, $nb_resp";
			}
			//echo "$sql<br />";
			$call_resp=mysql_query($sql);
			$nombreligne = mysql_num_rows($call_resp);
			// si la table des responsables est non vide :
			if ($nombreligne != 0) {
				echo "<p align='center'><input type='submit' name='valider_choix_resp' value='Enregistrer' /></p>\n";
				echo "<table align='center' class='boireaus' summary='Responsable'>\n";
				echo "<tr>\n";
				echo "<td><input type='radio' name='reg_resp".$definir_resp."' value='' onchange='changement();' /></td>\n";
				echo "<td style='font-weight:bold; text-align:center; background-color:#96C8F0;'><b>Responsable légal $definir_resp</b></td>\n";
				echo "<td style='font-weight:bold; text-align:center; background-color:#AAE6AA;'><b>Adresse</b></td>\n";
				echo "</tr>\n";

				$cpt=1;
				$alt=1;
				while($lig_resp=mysql_fetch_object($call_resp)){
					$alt=$alt*(-1);
					//if($cpt%2==0){$couleur="silver";}else{$couleur="white";}
					echo "<tr class='lig$alt white_hover'>\n";
					echo "<td><input type='radio' name='reg_resp".$definir_resp."' value='$lig_resp->pers_id' ";
					if($lig_resp->pers_id==$pers_id){
						echo "checked ";
					}
					echo "onchange='changement();' /></td>\n";
					echo "<td><a href='../responsables/modify_resp.php?pers_id=$lig_resp->pers_id&amp;quitter_la_page=y' target='_blank'>".my_strtoupper($lig_resp->nom)." ".casse_mot($lig_resp->prenom,'majf2')."</a></td>\n";
					echo "<td>";

					$sql="SELECT ra.* FROM resp_adr ra, resp_pers rp WHERE rp.pers_id='$lig_resp->pers_id' AND rp.adr_id=ra.adr_id";
					$res_adr=mysql_query($sql);
					if(mysql_num_rows($res_adr)==0){
						// L'adresse du responsable n'est pas définie:
						//echo "<font color='red'>L'adresse du responsable légal n'est pas définie</font>: <a href='../responsables/modify_resp.php?pers_id=$lig_resp->pers_id' target='_blank'>Définir l'adresse du responsable légal</a>\n";
						echo "&nbsp;";
					}
					else{
						$chaine_adr1="";
						$lig_adr=mysql_fetch_object($res_adr);
						if("$lig_adr->adr1"!=""){$chaine_adr1.="$lig_adr->adr1, ";}
						if("$lig_adr->adr2"!=""){$chaine_adr1.="$lig_adr->adr2, ";}
						if("$lig_adr->adr3"!=""){$chaine_adr1.="$lig_adr->adr3, ";}
						if("$lig_adr->adr4"!=""){$chaine_adr1.="$lig_adr->adr4, ";}
						if("$lig_adr->cp"!=""){$chaine_adr1.="$lig_adr->cp, ";}
						if("$lig_adr->commune"!=""){$chaine_adr1.="$lig_adr->commune";}
						if("$lig_adr->pays"!=""){$chaine_adr1.=" (<i>$lig_adr->pays</i>)";}
						echo $chaine_adr1;
					}

					echo "</td>\n";
					echo "</tr>\n";
					$cpt++;
				}

				echo "</table>\n";
				echo "<p align='center'><input type='submit' name='valider_choix_resp' value='Enregistrer' /></p>\n";
			}
			else{
				echo "<p>Aucun responsable n'est défini, ou aucun responsable correspond à la recherche.</p>\n";
			}

			echo "<p>Si le responsable légal ne figure pas dans la liste, vous pouvez l'ajouter à la base<br />\n";
			echo "(<i>après avoir, le cas échéant, sauvegardé cette fiche</i>)<br />\n";
			if($_SESSION['statut']=="scolarite") {
				echo "en vous rendant dans [<a href='../responsables/index.php'>Gestion des fiches responsables élèves</a>]</p>\n";
			}
			else{
				echo "en vous rendant dans [Gestion des bases-><a href='../responsables/index.php'>Gestion des responsables élèves</a>]</p>\n";
			}

			if (isset($order_type)) echo "<input type=hidden name=order_type value=\"$order_type\" />\n";
			if (isset($quelles_classes)) echo "<input type=hidden name=quelles_classes value=\"$quelles_classes\" />\n";
			if (isset($motif_rech)) echo "<input type=hidden name=motif_rech value=\"$motif_rech\" />\n";
			if (isset($mode_rech)) echo "<input type=hidden name=mode_rech value=\"$mode_rech\" />\n";


			echo "</form>\n";
		}
		else{
			// On valide l'enregistrement...
			// ... il faut le faire plus haut avant le header...
		}
		require("../lib/footer.inc.php");
		die();
	}



	//echo "\$eleve_no_resp1=$eleve_no_resp1<br />\n";



	if(isset($definir_etab)){
		if(!isset($valider_choix_etab)){
			echo "<p class=bold><a href=\"modify_eleve.php?eleve_login=$eleve_login\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

			//====================================================
			$critere_recherche=isset($_POST['critere_recherche']) ? $_POST['critere_recherche'] : (isset($_GET['critere_recherche']) ? $_GET['critere_recherche'] : "");
			$afficher_tous_les_etab=isset($_POST['afficher_tous_les_etab']) ? $_POST['afficher_tous_les_etab'] : (isset($_GET['afficher_tous_les_etab']) ? $_GET['afficher_tous_les_etab'] : "n");
			//$critere_recherche=my_ereg_replace("[^0-9a-zA-ZÀÄÂÉÈÊËÎÏÔÖÙÛÜ½¼Ççàäâéèêëîïôöùûü_ -]", "", $critere_recherche);
			$critere_recherche=preg_replace("/[^0-9a-zA-ZÀÄÂÉÈÊËÎÏÔÖÙÛÜ½¼Ççàäâéèêëîïôöùûü_ %-]/", "", preg_replace("/ /","%",$critere_recherche));
			// Saisir un espace ou % pour plusieurs portions du champ de recherche ou pour une apostrophe
			$champ_rech=isset($_POST['champ_rech']) ? $_POST['champ_rech'] : (isset($_GET['champ_rech']) ? $_GET['champ_rech'] : "nom");
			$tab_champs_recherche_autorises=array('nom','cp','ville','id');
			if(!in_array($champ_rech,$tab_champs_recherche_autorises)) {$champ_rech="nom";}

			/*
			if($critere_recherche==""){
				$critere_recherche=mb_substr($eleve_nom,0,3);
			}
			*/

			$nb_etab=isset($_POST['nb_etab']) ? $_POST['nb_etab'] : (isset($_GET['nb_etab']) ? $_GET['nb_etab'] : 20);
			if(mb_strlen(preg_replace("/[0-9]/","",$nb_etab))!=0) {
				$nb_etab=20;
			}
			$num_premier_etab_rech=isset($_POST['num_premier_etab_rech']) ? $_POST['num_premier_etab_rech'] : (isset($_GET['num_premier_etab_rech']) ? $_GET['num_premier_etab_rech'] : 0);
			if(mb_strlen(preg_replace("/[0-9]/","",$num_premier_etab_rech))!=0) {
				$num_premier_etab_rech=0;
			}

			$etab_order_by=isset($_POST['etab_order_by']) ? $_POST['etab_order_by'] : (isset($_GET['etab_order_by']) ? $_GET['etab_order_by'] : "ville,nom");
			$tab_champs_etab_order_by_autorises=array('ville,nom','id','nom','cp');
			if(!in_array($etab_order_by,$tab_champs_etab_order_by_autorises)) {$etab_order_by="ville,nom";}


			echo "<div align='center'>\n";
			echo "<div style='width:90%; border: 1px solid black;'>\n";
			echo "<!-- Formulaire de recherche/filtrage parmi les établissements -->\n";
			echo "<form enctype='multipart/form-data' name='form_rech' action='modify_eleve.php' method='post'>\n";
			echo add_token_field();

			echo "<input type='hidden' name='eleve_login' value='$eleve_login' />\n";
			echo "<input type='hidden' name='definir_etab' value='$definir_etab' />\n";
			echo "<table border='0' summary='Filtrage'>\n";
			echo "<tr>\n";
			echo "<td valign='top'>\n";
			//echo "<p align='center'>";
			echo "<input type='submit' name='filtrage' value='Afficher' /> les ";
			echo "<input type='text' name='nb_etab' value='$nb_etab' size='3' />\n";
			echo " établissements dont ";
			echo "</td>\n";
			echo "<td valign='top'>\n";

			echo "<input type='radio' name='champ_rech' id='champ_rech_nom' value='nom' ";
			if($champ_rech=="nom") {echo "checked ";}
			echo "/> <label for='champ_rech_nom' style='cursor: pointer;'>le <b>nom</b></label><br />\n";

			echo "<input type='radio' name='champ_rech' id='champ_rech_rne' value='id' ";
			if($champ_rech=="id") {echo "checked ";}
			echo "/> <label for='champ_rech_rne' style='cursor: pointer;'>le <b>RNE</b></label><br />\n";

			echo "<input type='radio' name='champ_rech' id='champ_rech_cp' value='cp' ";
			if($champ_rech=="cp") {echo "checked ";}
			echo "/> <label for='champ_rech_cp' style='cursor: pointer;'>le <b>code postal</b></label><br />\n";

			echo "<input type='radio' name='champ_rech' id='champ_rech_ville' value='ville' ";
			if($champ_rech=="ville") {echo "checked ";}
			echo "/> <label for='champ_rech_ville' style='cursor: pointer;'>la <b>ville</b></label>\n";

			echo "</td>\n";
			echo "<td valign='top'>\n";
			echo " contient: ";
			echo "<input type='text' name='critere_recherche' value='$critere_recherche' />\n";
			echo "<br />\n";
			echo "&nbsp;&nbsp;&nbsp;à partir de l'enregistrement ";
			echo "<input type='text' name='num_premier_etab_rech' value='$num_premier_etab_rech' size='4' />\n";
			//echo "</p>\n";
			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";


			if (isset($order_type)) echo "<input type=hidden name=order_type value=\"$order_type\" />\n";
			if (isset($quelles_classes)) echo "<input type=hidden name=quelles_classes value=\"$quelles_classes\" />\n";
			if (isset($motif_rech)) echo "<input type=hidden name=motif_rech value=\"$motif_rech\" />\n";
			if (isset($mode_rech)) echo "<input type=hidden name=mode_rech value=\"$mode_rech\" />\n";


			echo "<input type='hidden' name='afficher_tous_les_etab' id='afficher_tous_les_etab' value='n' />\n";
			echo "<p align='center'>";

			echo "<input type='submit' name='filtrage2' value='Afficher la sélection' /> ou ";

			echo "<input type='button' name='afficher_tous' value='Afficher tous les établissements' onClick=\"document.getElementById('afficher_tous_les_etab').value='y'; document.form_rech.submit();\" /></p>\n";
			echo "</form>\n";
			echo "</div>\n";
			echo "</div>\n";
			//====================================================


			echo "<!-- Formulaire de choix de l'établissement -->\n";
			echo "<form enctype='multipart/form-data' name='form_choix_etab' action='modify_eleve.php' method='post'>\n";
			echo add_token_field();

			echo "<p>Choix de l'établissement d'origine pour <b>".casse_mot($eleve_prenom,'majf2')." ".my_strtoupper($eleve_nom)."</b></p>\n";

			echo "<input type='hidden' name='eleve_login' value='$eleve_login' />\n";
			//echo "<input type='hidden' name='reg_no_gep' value='$reg_no_gep' />\n";
			echo "<input type='hidden' name='definir_etab' value='y' />\n";


			//$sql="SELECT * FROM etablissements ORDER BY ville,nom";
			$sql="SELECT * FROM etablissements e";
			if($afficher_tous_les_etab!='y'){
				if($critere_recherche!=""){
					$sql.=" WHERE e.$champ_rech LIKE '%".$critere_recherche."%'";
				}
			}
			$sql.=" ORDER BY $etab_order_by";
			if($afficher_tous_les_etab!='y'){
				$sql.=" LIMIT $num_premier_etab_rech, $nb_etab";
			}
			//echo "$sql<br />";

			$chaine_param_tri="";
			if(isset($eleve_login)) {$chaine_param_tri.= "&amp;eleve_login=$eleve_login";}
			if(isset($definir_etab)) {$chaine_param_tri.= "&amp;definir_etab=$definir_etab";}
			if(isset($nb_etab)) {$chaine_param_tri.= "&amp;nb_etab=$nb_etab";}
			if(isset($champ_rech)) {$chaine_param_tri.= "&amp;champ_rech=$champ_rech";}
			if(isset($critere_recherche)) {$chaine_param_tri.= "&amp;critere_recherche=$critere_recherche";}
			if(isset($num_premier_etab_rech)) {$chaine_param_tri.= "&amp;num_premier_etab_rech=$num_premier_etab_rech";}
			if(isset($order_type)) {$chaine_param_tri.= "&amp;order_type=$order_type";}
			if(isset($quelles_classes)) {$chaine_param_tri.= "&amp;quelles_classes=$quelles_classes";}
			if(isset($motif_rech)) {$chaine_param_tri.= "&amp;motif_rech=$motif_rech";}
			if (isset($mode_rech)) echo "<input type=hidden name=mode_rech value=\"$mode_rech\" />\n";
			if(isset($afficher_tous_les_etab)) {$chaine_param_tri.= "&amp;afficher_tous_les_etab=$afficher_tous_les_etab";}

			$call_etab=mysql_query($sql);
			$nombreligne = mysql_num_rows($call_etab);
			if ($nombreligne != 0) {
				echo "<p align='center'><input type='submit' name='valider_choix_etab' value='Valider' /></p>\n";
				echo "<table align='center' class='boireaus' border='1' summary='Etablissement'>\n";
				echo "<tr>\n";
				echo "<td><input type='radio' name='reg_etab' value='' /></td>\n";
				echo "<td style='font-weight:bold; text-align:center; background-color:#96C8F0;'>\n";
				echo "<a href='".$_SERVER['PHP_SELF']."?etab_order_by=id";
				echo $chaine_param_tri;
				echo "'>";
				echo "<b>RNE</b>\n";
				echo "</a>";
				echo "</td>\n";
				echo "<td style='font-weight:bold; text-align:center; background-color:#96C8F0;'>\n";
				echo "<b>Niveau</b>\n";
				echo "</td>\n";
				echo "<td style='font-weight:bold; text-align:center; background-color:#96C8F0;'>\n";
				echo "<b>Type</b>\n";
				echo "</td>\n";
				echo "<td style='font-weight:bold; text-align:center; background-color:#AAE6AA;'>\n";
				echo "<a href='".$_SERVER['PHP_SELF']."?etab_order_by=nom";
				echo $chaine_param_tri;
				echo "'>";
				echo "<b>Nom</b>\n";
				echo "</a>";
				echo "</td>\n";
				echo "<td style='font-weight:bold; text-align:center; background-color:#AAE6AA;'>\n";
				echo "<a href='".$_SERVER['PHP_SELF']."?etab_order_by=cp";
				echo $chaine_param_tri;
				echo "'>";
				echo "<b>Code postal</b>\n";
				echo "</a>";
				echo "</td>\n";
				echo "<td style='font-weight:bold; text-align:center; background-color:#AAE6AA;'>\n";
				echo "<a href='".$_SERVER['PHP_SELF']."?etab_order_by=ville,nom";
				echo $chaine_param_tri;
				echo "'>";
				echo "<b>Ville</b>\n";
				echo "</a>";
				echo "</td>\n";
				echo "</tr>\n";

				$temoin_checked="n";

				$cpt=1;
				$alt=1;
				while($lig_etab=mysql_fetch_object($call_etab)){
					//if($cpt%2==0){$couleur="silver";}else{$couleur="white";}
					$alt=$alt*(-1);
					echo "<tr class='lig$alt white_hover'>\n";
					/*
					echo "<td style='text-align:center; background-color:$couleur;'><input type='radio' name='reg_etab' value='$lig_etab->id' ";
					if($lig_etab->id==$id_etab){
						echo "checked ";
					}
					echo "onchange='changement();' /></td>";
					echo "<td style='text-align:center; background-color:$couleur;'><a href='../etablissements/modify_etab.php?id=$lig_etab->id' target='_blank'>$lig_etab->id</a></td>\n";
					echo "<td style='text-align:center; background-color:$couleur;'>$lig_etab->niveau</td>\n";
					echo "<td style='text-align:center; background-color:$couleur;'>$lig_etab->type</td>\n";
					echo "<td style='text-align:center; background-color:$couleur;'>$lig_etab->nom</td>\n";
					echo "<td style='text-align:center; background-color:$couleur;'>$lig_etab->cp</td>\n";
					echo "<td style='text-align:center; background-color:$couleur;'>$lig_etab->ville</td>\n";
					*/
					echo "<td><input type='radio' name='reg_etab' value='$lig_etab->id' ";
					if($lig_etab->id==$id_etab){
						echo "checked ";
						$temoin_checked="y";
					}
					echo "onchange='changement();' /></td>\n";
					echo "<td><a href='../etablissements/modify_etab.php?id=$lig_etab->id' target='_blank'>$lig_etab->id</a></td>\n";
					echo "<td>$lig_etab->niveau</td>\n";
					echo "<td>$lig_etab->type</td>\n";
					echo "<td>$lig_etab->nom</td>\n";
					echo "<td>$lig_etab->cp</td>\n";
					echo "<td>$lig_etab->ville</td>\n";

					echo "</tr>\n";
					$cpt++;
				}

				if(($temoin_checked=="n")&&($id_etab!=0)) {
					$sql="SELECT * FROM etablissements WHERE id='$id_etab';";
					$res_etab=mysql_query($sql);
					if(mysql_num_rows($res_etab)>0) {
						$lig_etab=mysql_fetch_object($res_etab);

						$alt=$alt*(-1);
						echo "<tr class='lig$alt white_hover'>\n";
						echo "<td><input type='radio' name='reg_etab' value='$lig_etab->id' ";
						echo "checked ";
						echo "onchange='changement();' /></td>\n";
						echo "<td><a href='../etablissements/modify_etab.php?id=$lig_etab->id' target='_blank'>$lig_etab->id</a></td>\n";
						echo "<td>$lig_etab->niveau</td>\n";
						echo "<td>$lig_etab->type</td>\n";
						echo "<td>$lig_etab->nom</td>\n";
						echo "<td>$lig_etab->cp</td>\n";
						echo "<td>$lig_etab->ville</td>\n";

						echo "</tr>\n";
					}
				}

				echo "</table>\n";
				echo "<p align='center'><input type='submit' name='valider_choix_etab' value='Valider' /></p>\n";
			}
			else{
				echo "<p>Aucun établissement n'est défini</p>\n";
			}

			echo "<p>Si un établissement ne figure pas dans la liste, vous pouvez l'ajouter à la base<br />\n";
			echo "en vous rendant dans [Gestion des bases-><a href='../etablissements/index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Gestion des établissements</a>]</p>\n";


			if (isset($order_type)) echo "<input type=hidden name=order_type value=\"$order_type\" />\n";
			if (isset($quelles_classes)) echo "<input type=hidden name=quelles_classes value=\"$quelles_classes\" />\n";
			if (isset($motif_rech)) echo "<input type=hidden name=motif_rech value=\"$motif_rech\" />\n";
			if (isset($mode_rech)) echo "<input type=hidden name=mode_rech value=\"$mode_rech\" />\n";


			echo "</form>\n";
		}
		else{
			// On valide l'enregistrement...
			// ... il faut le faire plus haut avant le header...
		}
		require("../lib/footer.inc.php");
		die();
	}
}

echo "<form enctype='multipart/form-data' name='form_choix_eleve' action='modify_eleve.php' method='post'>\n";
//echo add_token_field();
echo "<p class=bold><a href=\"index.php\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";

$num_eleve_courant=-1;
if ((isset($order_type)) and (isset($quelles_classes))) {
    //echo "<p class=bold><a href=\"index.php?quelles_classes=$quelles_classes&amp;order_type=$order_type\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>\n";

    echo " | <a href=\"index.php?quelles_classes=$quelles_classes";
	if(isset($motif_rech)){echo "&amp;motif_rech=$motif_rech";}
	if(isset($mode_rech)){echo "&amp;mode_rech=$mode_rech";}
	echo "&amp;order_type=$order_type\" onclick=\"return confirm_abandon (this, change, '$themessage')\">Retour à votre recherche</a>\n";

	include("index_call_data.php");

	echo "<input type=hidden name=order_type value=\"$order_type\" />\n";
	echo "<input type=hidden name=quelles_classes value=\"$quelles_classes\" />\n";
	if (isset($motif_rech)) echo "<input type=hidden name=motif_rech value=\"$motif_rech\" />\n";
	if (isset($mode_rech)) echo "<input type=hidden name=mode_rech value=\"$mode_rech\" />\n";

	if(isset($calldata)) {
		echo " | <select name='eleve_login' id='choix_eleve_login' onchange=\"confirm_changement_eleve(change, '$themessage');\">\n";
		$cpt_eleve=0;
		while($lig_calldata=mysql_fetch_object($calldata)) {
			echo "<option value='$lig_calldata->login'";
			if($lig_calldata->login==$eleve_login) {
				echo " selected";
				$num_eleve_courant=$cpt_eleve;
			}
			echo ">".$lig_calldata->nom." ".$lig_calldata->prenom."</option>\n";
			$cpt_eleve++;
		}
		echo "</select>\n";
	}
	elseif(isset($tab_eleve)) {
		echo " | <select name='eleve_login' id='choix_eleve_login' onchange=\"confirm_changement_eleve(change, '$themessage');\">\n";
		$cpt_eleve=0;
		for($loop=0;$loop<count($tab_eleve);$loop++) {
			echo "<option value='".$tab_eleve[$loop]['login']."'";
			if($tab_eleve[$loop]['login']==$eleve_login) {
				echo " selected";
				$num_eleve_courant=$cpt_eleve;
			}
			echo ">".$tab_eleve[$loop]['nom']." ".$tab_eleve[$loop]['prenom']."</option>\n";
			$cpt_eleve++;
		}
		echo "</select>\n";
	}
	echo "<input type='submit' id='bouton_submit_changement_eleve' value='Changer' />\n";
}
/*
else {
    echo "<p class=bold><a href=\"index.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>\n";
}
*/
echo "</p>\n";
echo "</form>\n";

echo "<script type='text/javascript'>
	// Initialisation
	change='no';

	if(document.getElementById('bouton_submit_changement_eleve')) {
		document.getElementById('bouton_submit_changement_eleve').style.display='none';
	}

	function confirm_changement_eleve(thechange, themessage)
	{
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			document.form_choix_eleve.submit();
		}
		else{
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				document.form_choix_eleve.submit();
			}
			else{
				document.getElementById('choix_eleve_login').selectedIndex=$num_eleve_courant;
			}
		}
	}
</script>\n";



echo "<form enctype='multipart/form-data' name='form_rech' action='modify_eleve.php' method='post'>\n";
echo add_token_field();

//echo "\$eleve_no_resp1=$eleve_no_resp1<br />\n";

//echo "\$eleve_login=$eleve_login<br />";

if(isset($eleve_login)) {
	//$sql="SELECT 1=1 FROM utilisateurs WHERE login='$eleve_login' AND statut='eleve';";
	$sql="SELECT auth_mode FROM utilisateurs WHERE login='$eleve_login';";
	$test_compte=mysql_query($sql);
	if(mysql_num_rows($test_compte)>0) {
		$compte_eleve_existe="y";
		$user_auth_mode=mysql_result($test_compte, 0, "auth_mode");
	}
	else {
		$compte_eleve_existe="n";
	}

	if(($compte_eleve_existe=="y")&&
		(($_SESSION['statut']=="administrateur")||
		(($_SESSION['statut']=="scolarite")&&(getSettingAOui('AccesDetailConnexionEleScolarite')))||
		(($_SESSION['statut']=="cpe")&&(getSettingAOui('AccesDetailConnexionEleCpe'))))) {
		//$journal_connexions=isset($_POST['journal_connexions']) ? $_POST['journal_connexions'] : (isset($_GET['journal_connexions']) ? $_GET['journal_connexions'] : 'n');
		//$duree=isset($_POST['duree']) ? $_POST['duree'] : NULL;
	
		echo "<div style='float:right; width:; height:;'><a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;journal_connexions=y#connexion' title='Journal des connexions'><img src='../images/icons/document.png' width='16' height='16' alt='Journal des connexions' /></a></div>\n";
	}
}

//echo "<table border='1'>\n";
echo "<table summary='Informations élève'>\n";
echo "<tr>\n";
echo "<td>\n";

echo "<table cellpadding='5' summary='Infos 1'>\n";
echo "<tr>\n";

if (isset($eleve_login)) {
	echo "<th style='text-align:left;'>Identifiant GEPI * : </th>
	<td>";

	if($_SESSION['statut']=='administrateur') {$avec_lien="y";}
	else {$avec_lien="n";}
	$lien_image_compte_utilisateur=lien_image_compte_utilisateur($eleve_login, "eleve", "_blank", $avec_lien);

	if(($compte_eleve_existe=="y")&&($_SESSION['statut']=="administrateur")) {
		echo "<a href='../utilisateurs/edit_eleve.php?critere_recherche=$eleve_nom' title=\"Accéder au compte de l'utilisateur.\">".$eleve_login;
		if($lien_image_compte_utilisateur!="") {echo " ".$lien_image_compte_utilisateur;}
		echo "</a>";
	}
	else {
		echo $eleve_login;
		if($lien_image_compte_utilisateur!="") {echo " ".$lien_image_compte_utilisateur;}
	}
	echo "<input type='hidden' name='eleve_login' size='20' ";
	if ($eleve_login) echo "value='$eleve_login'";
	echo " /></td>\n";
} else {
	echo "<th style='text-align:left;'>Identifiant GEPI * : </th>
	<td><input type='text' name='reg_login' size='20' value=\"\" onchange='changement();' /></td>\n";
}
echo "</tr>\n";

if(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite")){

	if($compte_eleve_existe=="y") {
		echo "<tr><th style='text-align:left;'>Authentification&nbsp;:</th>\n";

		echo "<td style='text-align:left;'>";
		echo "<select id='select_auth_mode' name='reg_auth_mode' onchange='changement()'>
		<option value='gepi' ";
		if ($user_auth_mode=='gepi') echo ' selected ';
		echo ">Locale (base Gepi)</option>
		<option value='ldap' ";
		if ($user_auth_mode=='ldap') echo ' selected ';
		echo ">LDAP</option>
		<option value='sso' ";
		if ($user_auth_mode=='sso') echo ' selected ';
		echo ">SSO (Cas, LCS, LemonLDAP)</option>
		</select>\n";
		echo "</td>\n";
		echo "</tr>\n";
	}

	echo "
	<tr>
		<th style='text-align:left;'>Nom * : </th>
		<td><input type='text' name='reg_nom' size='20' ";
	if (isset($eleve_nom)) {
		echo "value=\"".$eleve_nom."\"";
	}
	echo " onchange='changement();' /></td>
	</tr>
	<tr>
		<th style='text-align:left;'>Prénom * : </th>
		<td><input type='text' name='reg_prenom' size='20' ";
	if (isset($eleve_prenom)) {
		echo "value=\"".$eleve_prenom."\"";
	}
	echo " onchange='changement();' /></td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "	<th style='text-align:left;'>Email : </th>\n";
	echo "	<td>";

	/*
	if((isset($compte_eleve_existe))&&($compte_eleve_existe=="y")&&(getSettingValue('mode_email_ele')=='mon_compte')) {
		if (isset($eleve_email)) {
			echo $eleve_email;
		}
		else {
			echo "&nbsp;";
		}
	}
	else {
	*/
		echo "<input type='text' name='reg_email' size='18' ";
		if (isset($eleve_email)) {
			echo "value=\"".$eleve_email."\"";
		}
		echo " onchange='changement();' />";
	//}

	if((isset($compte_eleve_existe))&&($compte_eleve_existe=="y")&&(getSettingValue('mode_email_ele')=='mon_compte')) {
		if (isset($eleve_email)) {
			$txt_attention="ATTENTION : Le choix effectué dans 'Configuration générale' est de laisser l'utilisateur paramétrer son adresse mail dans 'Gérer mon compte'. Ne modifiez l'adresse mail que si c'est vraiment souhaitable.";
			echo " <img src='../images/icons/ico_attention.png' width='22' height='19' alt=\"$txt_attention\" title=\"$txt_attention\" />";
		}
	}

	if((isset($eleve_email))&&($eleve_email!='')) {
		$tmp_date=getdate();
		echo " <a href='mailto:".$eleve_email."?subject=GEPI&amp;body=";
		if($tmp_date['hours']>=18) {echo "Bonsoir";} else {echo "Bonjour";}
		echo ",%0d%0aCordialement.' title=\"Envoyer un courriel\">";
		echo "<img src='../images/imabulle/courrier.jpg' width='20' height='15' alt='Envoyer un courriel' border='0' />";
		echo "</a>";
	}
	echo "</td>\n";
	echo "</tr>\n";

	if(getSettingAOui('ele_tel_pers')) {
		echo "<tr>\n";
		echo "<th style='text-align:left;'>Tel personnel&nbsp;: </th>\n";
		echo "<td><input type='text' name='reg_tel_pers' size='20' ";
		if (isset($reg_tel_pers)) echo "value=\"".$reg_tel_pers."\"";
		echo " onchange='changement();' /></td>\n";
		echo "</tr>\n";
	}

	if(!getSettingAOui('ele_tel_port')) {
		// Par défaut, si on n'a pas enregistré la préférence dans Configuration générale, on affiche le tel port.
	}
	else {
		echo "<tr>\n";
		echo "<th style='text-align:left;'>Tel portable&nbsp;: </th>\n";
		echo "<td><input type='text' name='reg_tel_port' size='20' ";
		if (isset($reg_tel_port)) echo "value=\"".$reg_tel_port."\"";
		echo " onchange='changement();' /></td>\n";
		echo "</tr>\n";
	}

	if(getSettingAOui('ele_tel_prof')) {
		echo "<tr>\n";
		echo "<th style='text-align:left;'>Tel professionnel&nbsp;: </th>\n";
		echo "<td><input type='text' name='reg_tel_prof' size='20' ";
		if (isset($reg_tel_prof)) echo "value=\"".$reg_tel_prof."\"";
		echo " onchange='changement();' /></td>\n";
		echo "</tr>\n";
	}

	echo "<tr>\n";
	echo "<th style='text-align:left;'>Identifiant National : </th>\n";
    echo "<td><input type='text' name='reg_no_nat' size='20' ";
    if (isset($reg_no_nat)) echo "value=\"".$reg_no_nat."\"";
    echo " onchange='changement();' /></td>\n";

	echo "</tr>\n";

    //echo "<tr><td>Numéro GEP : </td><td><input type=text name='reg_no_gep' size=20 ";
    echo "<tr><th style='text-align:left;'>Numéro interne Sconet (<em style='font-weight:normal'>elenoet</em>) : </th><td><input type='text' name='reg_no_gep' size='20' ";
    if (isset($reg_no_gep)) echo "value=\"".$reg_no_gep."\"";
    echo " onchange='changement();' /></td>\n";
	echo "</tr>\n";
	
    echo "<tr><th style='text-align:left;'>Numéro interne Sconet (<em style='font-weight:normal'>ele_id</em>) : </th><td>";
    if (isset($reg_ele_id)) {echo $reg_ele_id;}
    echo "</td>\n";
	echo "</tr>\n";

    echo "<tr>
	<th style='text-align:left;'>MEF : </th>
	<td>
		<select name='reg_mef_code' onchange='changement();'>
			<option value=''>---</option>";
	$sql="SELECT * FROM mef ORDER BY libelle_long, libelle_edition, libelle_court;";
	$res_mef=mysql_query($sql);
	while($lig_mef=mysql_fetch_object($res_mef)) {
		echo "
			<option value='$lig_mef->mef_code'";
		if($lig_mef->mef_code==$reg_mef_code) {echo " selected";}
		echo " title='$lig_mef->mef_code|$lig_mef->libelle_court|$lig_mef->libelle_long|$lig_mef->libelle_edition'>";
		if($lig_mef->libelle_edition!="") {
			echo $lig_mef->libelle_edition;
		}
		elseif($lig_mef->libelle_long!="") {
			echo $lig_mef->libelle_long;
		}
		elseif($lig_mef->libelle_court!="") {
			echo $lig_mef->libelle_court;
		}
		else {
			echo $lig_mef->mef_code;
		}
		echo "</option>";
	}
	echo "
		</select>";
	if(acces("/mef/admin_mef.php", $_SESSION['statut'])) {
		echo " <a href='../mef/admin_mef.php' title='Gérer les MEFS' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/configure.png' width='16' height='16' ></a>";
	}
	echo "
	</td>
</tr>\n";

	//Date dentrée dans l'établissement
	echo "<tr>
	<th style='text-align:left;'>Date d'entrée dans l'établissement : <br/>(<em style='font-weight:normal'>respecter format JJ/MM/AAAA</em>)</th>
	<td>
		<div class='norme'>
			Jour  <input type='text' name='date_entree_jour' id='date_entree_jour' size='2' onchange='changement();' value=\"";
	if (isset($eleve_date_entree_jour) and ($eleve_date_entree_jour!="00") ) {echo $eleve_date_entree_jour;}
	echo "\" onKeyDown='clavier_2(this.id,event,1,31);' AutoComplete='off'  title=\"Vous pouvez modifier le jour de sortie à l'aide des flèches Up et Down du pavé de direction.\" /> 
		Mois  <input type='text' name='date_entree_mois' id='date_entree_mois' size='2' onchange='changement();' value=\"";
	if (isset($eleve_date_entree_mois) and ($eleve_date_entree_mois!="00")) {echo $eleve_date_entree_mois;}
	echo "\" onKeyDown='clavier_2(this.id,event,1,12);' AutoComplete='off'  title=\"Vous pouvez modifier le mois de naissance à l'aide des flèches Up et Down du pavé de direction.\" /> 
		Année <input type='text' name='date_entree_annee' id='date_entree_annee' size='4' onchange='changement();' value=\"";
	if (isset($eleve_date_entree_annee) and ($eleve_date_entree_annee!="0000")) {echo $eleve_date_entree_annee;}
	echo "\" onKeyDown='clavier_2(this.id,event,2000,2100);' AutoComplete='off'  title=\"Vous pouvez modifier l'année de naissance à l'aide des flèches Up et Down du pavé de direction.\" />
		<a href='javascript:date_entree_aujourdhui()' title=\"Aujourd'hui\"><img src='../images/icons/wizard.png' width='20' height='20' title=\"Aujourd'hui\" /></a>
	</td>
</tr>\n";

	//Date de sortie de l'établissement
	echo "<tr>
	<th style='text-align:left;'>Date de sortie de l'établissement : <br/>(<em style='font-weight:normal'>respecter format JJ/MM/AAAA</em>)</th>
	<td>
		<div class='norme'>
			Jour  <input type='text' name='date_sortie_jour' id='date_sortie_jour' size='2' onchange='changement();' value=\"";
	if (isset($eleve_date_sortie_jour) and ($eleve_date_sortie_jour!="00") ) {echo $eleve_date_sortie_jour;}
	echo "\" onKeyDown='clavier_2(this.id,event,1,31);' AutoComplete='off'  title=\"Vous pouvez modifier le jour de sortie à l'aide des flèches Up et Down du pavé de direction.\" /> 
		Mois  <input type='text' name='date_sortie_mois' id='date_sortie_mois' size='2' onchange='changement();' value=\"";
	if (isset($eleve_date_sortie_mois) and ($eleve_date_sortie_mois!="00")) {echo $eleve_date_sortie_mois;}
	echo "\" onKeyDown='clavier_2(this.id,event,1,12);' AutoComplete='off'  title=\"Vous pouvez modifier le mois de naissance à l'aide des flèches Up et Down du pavé de direction.\" /> 
		Année <input type='text' name='date_sortie_annee' id='date_sortie_annee' size='4' onchange='changement();' value=\"";
	if (isset($eleve_date_sortie_annee) and ($eleve_date_sortie_annee!="0000")) {echo $eleve_date_sortie_annee;}
	echo "\" onKeyDown='clavier_2(this.id,event,2000,2100);' AutoComplete='off'  title=\"Vous pouvez modifier l'année de naissance à l'aide des flèches Up et Down du pavé de direction.\" />
		<a href='javascript:date_sortie_aujourdhui()' title=\"Aujourd'hui\"><img src='../images/disabled.png' width='20' height='20' title=\"Aujourd'hui\" /></a>
		<script type='text/javascript'>
function date_entree_aujourdhui() {
	aujourdhui=new Date();
	document.getElementById('date_entree_jour').value=aujourdhui.getDate();
	document.getElementById('date_entree_mois').value=aujourdhui.getMonth()+1;
	annee=aujourdhui.getYear();
	if(annee<1000) {
		//alert(annee);
		if(annee>70) {
			annee=1900+annee;
		}
		else {
			annee=2000+annee;
		}
	}
	document.getElementById('date_entree_annee').value=annee;
	changement();
}

function date_sortie_aujourdhui() {
	aujourdhui=new Date();
	document.getElementById('date_sortie_jour').value=aujourdhui.getDate();
	document.getElementById('date_sortie_mois').value=aujourdhui.getMonth()+1;
	annee=aujourdhui.getYear();
	if(annee<1000) {
		//alert(annee);
		if(annee>70) {
			annee=1900+annee;
		}
		else {
			annee=2000+annee;
		}
	}
	document.getElementById('date_sortie_annee').value=annee;
	changement();
}
</script>
	</td>
</tr>\n";

}
else {
	echo "
	<tr>
		<th style='text-align:left;'>Nom * : </th>
		<td>";
	if (isset($eleve_nom)) {
		echo "$eleve_nom";
	}
	echo "</td>
	</tr>
	<tr>
		<th style='text-align:left;'>Prénom * : </th>
		<td>";
	if (isset($eleve_prenom)) {
		echo "$eleve_prenom";
	}
	echo "</td>
	</tr>
	<tr>
		<th style='text-align:left;'>Email : </th>
		<td>";
	if (isset($eleve_email)) {
		echo "$eleve_email";
	}
	if((isset($eleve_email))&&($eleve_email!='')) {
		$tmp_date=getdate();
		echo " <a href='mailto:".$eleve_email."?subject=GEPI&amp;body=";
		if($tmp_date['hours']>=18) {echo "Bonsoir";} else {echo "Bonjour";}
		echo ",%0d%0aCordialement.' title=\"Envoyer un courriel\">";
		echo "<img src='../images/imabulle/courrier.jpg' width='20' height='15' alt='Envoyer un courriel' border='0' />";
		echo "</a>";
	}
	echo "</td>
	</tr>
	<tr>
    <th style='text-align:left;'>Identifiant National : </th>\n";
    echo "<td>";
    if (isset($reg_no_nat)) echo "$reg_no_nat";
    echo "</td>\n";

	echo "</tr>\n";

    //echo "<tr><td>Numéro GEP : </td><td><input type=text name='reg_no_gep' size=20 ";
    echo "<tr><th style='text-align:left;'>Numéro interne Sconet (<i>elenoet</i>) : </th><td>";
    if (isset($reg_no_gep)) {
		echo "$reg_no_gep";
		if(getSettingValue("GepiAccesGestPhotoElevesProfP")=='yes'){
			// Nécessaire pour les photos:
			echo "<input type='hidden' name='reg_no_gep' size='20' value=\"".$reg_no_gep."\" />\n";
		}
	}
    echo "</td>\n";
	echo "</tr>\n";

	if ((isset($eleve_date_entree))&&($eleve_date_entree!=0)) {
		//Date d'entrée dans l'établissement
		echo "<tr><th style='text-align:left;'>Date d'entrée dans l'établissement : <br/></th>";
		echo "<td><div class='norme'>";	
		
		if ((isset($eleve_date_entree_jour)) and ($eleve_date_entree_jour!="00")) echo $eleve_date_entree_jour."/";
		if ((isset($eleve_date_entree_mois)) and ($eleve_date_entree_mois!="00")) echo $eleve_date_entree_mois."/";
		if ((isset($eleve_date_entree_annee)) and ($eleve_date_entree_annee!="00")) echo $eleve_date_entree_annee; 
		echo "</td>\n";
		echo "</tr>\n";
	}

	if ((isset($eleve_date_de_sortie))&&($eleve_date_de_sortie!=0)) {
		//Date de sortie de l'établissement
		echo "<tr><th style='text-align:left;'>Date de sortie de l'établissement : <br/></th>";
		echo "<td><div class='norme'>";	
		
		if ((isset($eleve_date_sortie_jour)) and ($eleve_date_sortie_jour!="00")) echo $eleve_date_sortie_jour."/";
		if ((isset($eleve_date_sortie_mois)) and ($eleve_date_sortie_mois!="00")) echo $eleve_date_sortie_mois."/";
		if ((isset($eleve_date_sortie_annee)) and ($eleve_date_sortie_annee!="00")) echo $eleve_date_sortie_annee; 
		echo "</td>\n";
		echo "</tr>\n";
	}
}
echo "</table>\n";

//echo "\$eleve_no_resp1=$eleve_no_resp1<br />\n";
//echo "<td>\$reg_no_gep=$reg_no_gep</td>";
if(isset($reg_no_gep)){
	// Récupération du nom de la photo en tenant compte des histoires des zéro 02345.jpg ou 2345.jpg
	$photo=nom_photo($reg_no_gep);

	echo "<td align='center'>\n";
	$temoin_photo="non";
	//echo "<td>\$photo=$photo</td>";
	if($photo){
		if(file_exists($photo)){
			$temoin_photo="oui";
			//echo "<td>\n";
			echo "<div align='center'>\n";
			// la photo sera réduite si nécessaire
			$dimphoto=dimensions_affichage_photo($photo,getSettingValue('l_max_aff_trombinoscopes'),getSettingValue('h_max_aff_trombinoscopes'));
			//echo '<img src="'.$photo.'" style="width: '.$dimphoto[0].'px; height: '.$dimphoto[1].'px; border: 0px; border-right: 3px solid #FFFFFF; float: left;" alt="" />';
			echo '<img src="'.$photo.'" style="width: '.$dimphoto[0].'px; height: '.$dimphoto[1].'px; border: 0px; border: 3px solid #FFFFFF;" alt="" />';
			//echo "</td>\n";
			//echo "<br />\n";
			echo "</div>\n";
			echo "<div style='clear:both;'></div>\n";
		}
	}

	//echo "getSettingValue(\"GepiAccesGestPhotoElevesProfP\")=".getSettingValue("GepiAccesGestPhotoElevesProfP")."<br />";
  if ((getSettingValue("active_module_trombinoscopes")=='y') and 
  (($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite")||
  (($_SESSION['statut']=='cpe')&&(getSettingValue("CpeAccesUploadPhotosEleves")=='yes'))||
  (($_SESSION['statut']=="professeur")&&(getSettingValue("GepiAccesGestPhotoElevesProfP")=='yes')&&(isset($eleve_login))&&(is_pp($_SESSION['login'],"",$eleve_login))))) {
		echo "<div align='center'>\n";
		//echo "<span id='lien_photo' style='font-size:xx-small;'>";
		echo "<div id='lien_photo' style='border: 1px solid black; padding: 5px; margin: 5px;'>";
		echo "<a href='#' onClick=\"document.getElementById('div_upload_photo').style.display='';document.getElementById('lien_photo').style.display='';return false;\">";
		if($temoin_photo=="oui"){
			//echo "Modifier le fichier photo</a>\n";
			echo "Modifier le fichier photo</a>\n";
		}
		else{
			echo "Envoyer un fichier photo</a>\n";
			//echo "Envoyer<br />un fichier<br />photo</a>\n";
		}
		//echo "</span>\n";
		echo "</div>\n";
		echo "<div id='div_upload_photo' style='display:none;'>";
		echo "<input type='file' name='filephoto' />\n";
		if("$photo"!=""){
			if(file_exists($photo)){
				echo "<br />\n";
				echo "<input type='checkbox' name='suppr_filephoto' id='suppr_filephoto' value='y' onchange='changement();' /><label for='suppr_filephoto' style='cursor:pointer;'> Supprimer la photo existante</label>\n";
			}
		}
		echo "</div>\n";
		echo "</div>\n";
	}
	echo "</td>\n";
}


// Lien vers les inscriptions à des groupes:
if(isset($eleve_login)){
	echo "<td valign='top'>\n";
	// style='border: 1px solid black; text-align:center;'

	//echo "\$reg_regime=$reg_regime<br />";
	//echo "\$reg_doublant=$reg_doublant<br />";

	if(($_SESSION['statut']=="professeur")||($_SESSION['statut']=='cpe')) {
		echo "<table border='0' summary='Infos 2'>\n";

		echo "<tr><th style='text-align:left;'>Né(e) le: </th><td>$eleve_naissance_jour/$eleve_naissance_mois/$eleve_naissance_annee</td></tr>\n";
		if ($eleve_sexe == "M") {
			echo "<tr><th style='text-align:left;'>Sexe: </th><td>Masculin</td></tr>\n";
		}
		elseif($eleve_sexe == "F"){
			echo "<tr><th style='text-align:left;'>Sexe: </th><td>Féminin</td></tr>\n";
		}

		echo "<tr><th style='text-align:left;'>Régime: </th><td>";
		if ($reg_regime == 'i-e') {
			echo "Interne-externé";
		}
		elseif ($reg_regime == 'int.') {
			echo "Interne";
		}
		elseif ($reg_regime == 'd/p') {
			echo "Demi-pensionnaire";
		}
		elseif ($reg_regime == 'ext.') {
			echo "Externe";
		}
		echo "</td></tr>\n";

		if ($reg_doublant == 'R') {echo "<tr><th style='text-align:left;'>Redoublant</th><td>Oui</td></tr>\n";}

		echo "</table>\n";

	}
	else{
		//=========================
		// AJOUT: boireaus 20071107
		echo "<table style='border-collapse: collapse; border: 1px solid black;' align='center'  summary='Régime'>\n";
		echo "<tr>\n";
		echo "<th>Régime: </th>\n";
		echo "<td style='text-align: center; border: 0px;'>I-ext<br /><input type='radio' name='reg_regime' value='i-e' ";
		if ($reg_regime == 'i-e') {echo " checked";}
		echo " onchange='changement();' /></td>\n";
		echo "<td style='text-align: center; border: 0px; border-left: 1px solid #AAAAAA;'>Int<br/><input type='radio' name='reg_regime' value='int.' ";
		if ($reg_regime == 'int.') {echo " checked";}
		echo " onchange='changement();' /></td>\n";
		echo "<td style='text-align: center; border: 0px; border-left: 1px solid #AAAAAA;'>D/P<br/><input type='radio' name='reg_regime' value='d/p' ";
		if ($reg_regime == 'd/p') {echo " checked";}
		echo " onchange='changement();' /></td>\n";
		echo "<td style='text-align: center; border: 0px; border-left: 1px solid #AAAAAA;'>Ext<br/><input type='radio' name='reg_regime' value='ext.' ";
		if ($reg_regime == 'ext.') {echo " checked";}
		echo " onchange='changement();' /></td></tr>\n";
		echo "</table>\n";

		echo "<br />\n";
		//echo "<tr><td>&nbsp;</td></tr>\n";

		echo "<table style='border-collapse: collapse; border: 1px solid black;' align='center' summary='Redoublement'>\n";
		echo "<tr>\n";
		echo "<th>Redoublant: </th>\n";
		echo "<td style='text-align: center; border: 0px;'>O<br /><input type='radio' name='reg_doublant' value='R' ";
		if ($reg_doublant == 'R') {echo " checked";}
		echo " onchange='changement();' /></td>\n";
		echo "<td style='text-align: center; border: 0px; border-left: 1px solid #AAAAAA;'>N<br /><input type='radio' name='reg_doublant' value='-' ";
		if ($reg_doublant == '-') {echo " checked";}
		echo " onchange='changement();' /></td></tr>\n";
		echo "</table>\n";

		echo "<br />\n";
		echo "<div style='border: 1px solid black; text-align:center;'>\n";
		echo "<a href='visu_eleve.php?ele_login=".$eleve_login."'>Consultation élève</a>";
		echo "</div>\n";
		echo "<br />\n";
		//=========================

		echo "<div style='border: 1px solid black; text-align:center;'>\n";
		$sql="SELECT jec.id_classe,c.classe, jec.periode FROM j_eleves_classes jec, classes c WHERE jec.login='$eleve_login' AND jec.id_classe=c.id GROUP BY jec.id_classe ORDER BY jec.periode";
		$res_grp1=mysql_query($sql);
		if(mysql_num_rows($res_grp1)==0){
			echo "L'élève n'est encore associé à aucune classe.";
		}
		else {
			while($lig_classe=mysql_fetch_object($res_grp1)){
				//echo "Enseignements suivis en <a href='../classes/eleve_options.php?login_eleve=$eleve_login&amp;id_classe=$lig_classe->id_classe' target='_blank'>$lig_classe->classe</a><br />\n";
				echo "<a href='../classes/eleve_options.php?login_eleve=$eleve_login&amp;id_classe=$lig_classe->id_classe&amp;quitter_la_page=y' target='_blank'>Enseignements suivis</a> en ".preg_replace("/ /","&nbsp;",$lig_classe->classe)."\n";
				echo "<br />\n";

				//echo "Définir/consulter <a href='../classes/classes_const.php?id_classe=$lig_classe->id_classe&amp;quitter_la_page=y' target='_blank'>le régime, le professeur principal, le CPE responsable</a> de l'élève.\n";
				//echo "<br />\n";
			}
		}
		echo "</div>\n";

		//=========================
		//$test_compte_actif=check_compte_actif($eleve_login);
		//if($test_compte_actif!=0) {
		if((isset($compte_eleve_existe))&&($compte_eleve_existe=="y")&&($_SESSION['statut']=="administrateur")) {
			echo "<div style='margin-top: 0.5em; text-align:center; border: 1px solid black;'>\n";
			echo affiche_actions_compte($eleve_login);
			echo "</div>\n";
		}
		//=========================

	}
	echo "</td>\n";
}

echo "</tr>\n";
echo "</table>\n";

//echo "\$eleve_no_resp1=$eleve_no_resp1<br />\n";

if (($reg_no_gep == '') and (isset($eleve_login))) {
   //echo "<font color=red>ATTENTION : Cet élève ne possède pas de numéro GEP. Vous ne pourrez pas importer les absences à partir des fichiers GEP pour cet élèves.</font>\n";
   echo "<font color='red'>ATTENTION : Cet élève ne possède pas de numéro interne Sconet (<i>elenoet</i>). Vous ne pourrez pas importer les absences à partir des fichiers GEP/Sconet pour cet élève.<br />Vous ne pourrez pas définir l'établissement d'origine de l'élève.<br />Cet élève ne pourra pas figurer dans le module trombinoscope.</font>\n";

	$sql="select value from setting where name='import_maj_xml_sconet'";
	$test_sconet=mysql_query($sql);
	if(mysql_num_rows($test_sconet)>0){
		$lig_tmp=mysql_fetch_object($test_sconet);
		if($lig_tmp->value=='1'){
			echo "<br />";
			echo "<font color='red'>Vous ne pourrez pas non plus effectuer les mises à jour de ses informations depuis Sconet<br />(<i>l'ELENOET et l'ELE_ID ne correspondront pas aux données de Sconet</i>).</font>\n";
		}
	}
}
//echo "\$eleve_no_resp1=$eleve_no_resp1<br />\n";

/*
if($_SESSION['statut']=="professeur") {
	if ($eleve_sexe == "M") {
		echo "<b>Sexe:</b> Masculin<br />";
	}
	elseif($eleve_sexe == "F"){
		echo "<b>Sexe:</b> Féminin<br />";
	}

	echo "<b>Né(e) le</b>: $eleve_naissance<br />\n";
}
else{
*/
if(($_SESSION['statut']!="professeur")&&($_SESSION['statut']!="cpe")) {
?>
<center>
<!--table border = '1' CELLPADDING = '5'-->
<table class='boireaus' cellpadding='5' summary='Sexe'>
<tr><td><div class='norme'><b>Sexe :</b> <br />
<?php
if (!(isset($eleve_sexe))) {$eleve_sexe="M";}
?>
<label for='reg_sexeM' style='cursor: pointer;'><input type=radio name=reg_sexe id='reg_sexeM' value=M <?php if ($eleve_sexe == "M") { echo "CHECKED" ;} ?> onchange='changement();' /> Masculin</label>
<label for='reg_sexeF' style='cursor: pointer;'><input type=radio name=reg_sexe id='reg_sexeF' value=F <?php if ($eleve_sexe == "F") { echo "CHECKED" ;} ?> onchange='changement();' /> Féminin</label>
</div></td>

<td><div class='norme'>
<b>Date de naissance (<em>respecter format 00/00/0000</em>) :</b> <br />
<?php

echo "Jour <input type='text' name='birth_day' id='birth_day' size='2' onchange='changement();' value='";
if (isset($eleve_naissance_jour)) {echo $eleve_naissance_jour;}
echo "' onKeyDown='clavier_2(this.id,event,1,31);' AutoComplete='off' title=\"Vous pouvez modifier le jour de naissance à l'aide des flèches Up et Down du pavé de direction.\" />";

echo " Mois <input type='text' name='birth_month' id='birth_month' size='2' onchange='changement();' value='";
if (isset($eleve_naissance_mois)) {echo $eleve_naissance_mois;}
echo "' onKeyDown='clavier_2(this.id,event,1,12);' AutoComplete='off' title=\"Vous pouvez modifier le mois de naissance à l'aide des flèches Up et Down du pavé de direction.\" />";

echo " Année <input type='text' name='birth_year' id='birth_year' size='2' onchange='changement();' value='";
if (isset($eleve_naissance_annee)) {echo $eleve_naissance_annee;}
echo "' onKeyDown='clavier_2(this.id,event,1970,2100);' AutoComplete='off' title=\"Vous pouvez modifier l'année de naissance à l'aide des flèches Up et Down du pavé de direction.\" />";


if(getSettingValue('ele_lieu_naissance')=='y') {
	echo "<br />\n";
	echo "<b>Lieu de naissance&nbsp;:</b> ";
	if(isset($eleve_lieu_naissance)) {echo get_commune($eleve_lieu_naissance,1);}
	else {echo "<span style='color:red'>Non défini</span>";}
	echo "\n";
}
?>
</div></td>

</tr>
</table></center>

<p><b>Remarque</b> :
<br />- Les champs * sont obligatoires.</p>
<?php
}


echo "<input type=hidden name=is_posted value=\"1\" />\n";
if (isset($order_type)) echo "<input type=hidden name=order_type value=\"$order_type\" />\n";
if (isset($quelles_classes)) echo "<input type=hidden name=quelles_classes value=\"$quelles_classes\" />\n";
if (isset($motif_rech)) echo "<input type=hidden name=motif_rech value=\"$motif_rech\" />\n";
if (isset($mode_rech)) echo "<input type=hidden name=mode_rech value=\"$mode_rech\" />\n";
if (isset($eleve_login)) echo "<input type=hidden name=eleve_login value=\"$eleve_login\" />\n";
if (isset($mode)) echo "<input type=hidden name=mode value=\"$mode\" />\n";

if($_SESSION['statut']=='professeur'){
  if ((getSettingValue("active_module_trombinoscopes")=='y') && (getSettingValue("GepiAccesGestPhotoElevesProfP")=='yes')){
		echo "<center><input type=submit value=Enregistrer /></center>\n";
	}
}
else{
	echo "<center><input type=submit value=Enregistrer /></center>\n";
}
echo "</form>\n";

//echo "\$eleve_no_resp1=$eleve_no_resp1<br />\n";


if(isset($eleve_login)){
	//$sql="SELECT rp.nom,rp.prenom,rp.pers_id,ra.* FROM responsables2 r, resp_adr ra, resp_pers rp WHERE r.resp_legal='1' AND r.pers_id=rp.pers_id AND rp.adr_id=ra.adr_id ORDER BY rp.nom, rp.prenom";
	//$sql="SELECT DISTINCT rp.pers_id,rp.nom,rp.prenom,ra.* FROM responsables2 r, resp_adr ra, resp_pers rp WHERE r.pers_id=rp.pers_id AND rp.adr_id=ra.adr_id ORDER BY rp.nom, rp.prenom";
	$sql="SELECT DISTINCT rp.pers_id,rp.nom,rp.prenom FROM resp_pers rp ORDER BY rp.nom, rp.prenom";
	$call_resp=mysql_query($sql);
	$nombreligne = mysql_num_rows($call_resp);
	// si la table des responsables est non vide :
	if ($nombreligne != 0) {

		echo "<br />\n";
		echo "<hr />\n";
		echo "<h3>Envoi des bulletins par voie postale</h3>\n";

		//echo "\$eleve_no_resp1=$eleve_no_resp1<br />\n";

		echo "<i>Si vous n'envoyez pas les bulletins scolaires par voie postale, vous pouvez ignorer cette rubrique.</i>";
		echo "<br />\n<br />\n";

		$temoin_tableau="";
		$chaine_adr1='';
		// Lorsque le $eleve_no_resp1 est non numérique (cas sans sconet), on a p000000012 et il considère que p000000012==0
		// Il faut comparer des chaines de caractères.
		//if($eleve_no_resp1==0){
		if("$eleve_no_resp1"=="0"){
			// Le responsable 1 n'est pas défini:
			echo "<p>Le responsable légal 1 n'est pas défini";
			//if($_SESSION['statut']=="professeur") {
			if(!in_array($_SESSION['statut'], array("administrateur", "scolarite"))) {
				echo ".";
			}
			else{
				echo ": <a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;definir_resp=1";
				if (isset($order_type)) {echo "&amp;order_type=$order_type";}
				if (isset($quelles_classes)) {echo "&amp;quelles_classes=$quelles_classes";}
				if (isset($motif_rech)) {echo "&amp;motif_rech=$motif_rech";}
				if (isset($mode_rech)) {echo "&amp;mode_rech=$mode_rech";}
				echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">Définir le responsable légal 1</a>";
			}
			echo "</p>\n";
		}
		else{
			$sql="SELECT nom,prenom FROM resp_pers WHERE pers_id='$eleve_no_resp1'";
			$res_resp=mysql_query($sql);
			if(mysql_num_rows($res_resp)==0){
				// Bizarre: Le responsable 1 n'est pas défini:
				echo "<p>Le responsable légal 1 n'est pas défini";
				//if($_SESSION['statut']=="professeur") {
				if(!in_array($_SESSION['statut'], array("administrateur", "scolarite"))) {
					echo ".";
				}
				else{
					echo ": <a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;definir_resp=1";
					if (isset($order_type)) {echo "&amp;order_type=$order_type";}
					if (isset($quelles_classes)) {echo "&amp;quelles_classes=$quelles_classes";}
					if (isset($motif_rech)) {echo "&amp;motif_rech=$motif_rech";}
					if (isset($mode_rech)) {echo "&amp;mode_rech=$mode_rech";}
					echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">Définir le responsable légal 1</a>";
				}
				echo "</p>\n";
			}
			else{
				$temoin_tableau="oui";
				$lig_resp=mysql_fetch_object($res_resp);
				echo "<table border='0' summary='Responsable légal 1'>\n";
				echo "<tr valign='top'>\n";
				echo "<td rowspan='2'>Le responsable légal 1 est: </td>\n";
				echo "<td>";
				//if($_SESSION['statut']=="professeur") {
				if(!in_array($_SESSION['statut'], array("administrateur", "scolarite"))) {
					echo casse_mot($lig_resp->prenom,'majf2')." ".my_strtoupper($lig_resp->nom);
				}
				else{
					//echo "<a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp1' target='_blank'>";
					//echo "<a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp1&amp;quitter_la_page=y' target='_blank' onclick=\"affiche_message_raffraichissement(); return confirm_abandon (this, change, '$themessage');\">";
					echo "<a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp1&amp;quitter_la_page=y' target='_blank' onclick=\"return confirm_abandon (this, change, '$themessage');\">";
					echo casse_mot($lig_resp->prenom,'majf2')." ".my_strtoupper($lig_resp->nom);
					echo "</a>";
				}
				echo "</td>\n";

				//if($_SESSION['statut']!="professeur") {
				if(in_array($_SESSION['statut'], array("administrateur", "scolarite"))) {
					//echo "<td><a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;definir_resp=1'>Modifier l'association</a></td>\n";
					echo "<td><a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;definir_resp=1";
					if (isset($order_type)) {echo "&amp;order_type=$order_type";}
					if (isset($quelles_classes)) {echo "&amp;quelles_classes=$quelles_classes";}
					if (isset($motif_rech)) {echo "&amp;motif_rech=$motif_rech";}
					if (isset($mode_rech)) {echo "&amp;mode_rech=$mode_rech";}
					//echo "'>Modifier le responsable</a></td>\n";
					echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">Changer de responsable</a></td>\n";
				}
				echo "</tr>\n";

				echo "<tr valign='top'>\n";
				// La 1ère colonne est dans le rowspan

				$sql="SELECT ra.* FROM resp_adr ra, resp_pers rp WHERE rp.pers_id='$eleve_no_resp1' AND rp.adr_id=ra.adr_id";
				$res_adr=mysql_query($sql);
				if(mysql_num_rows($res_adr)==0){
					// L'adresse du responsable 1 n'est pas définie:
					echo "<td colspan='2'>\n";
					//if($_SESSION['statut']=="professeur") {
					if(!in_array($_SESSION['statut'], array("administrateur", "scolarite"))) {
						echo "L'adresse du responsable légal 1 n'est pas définie.\n";
					}
					else{
						//echo "L'adresse du responsable légal 1 n'est pas définie: <a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp1#adresse' target='_blank'>Définir l'adresse du responsable légal 1</a>\n";
						//echo "L'adresse du responsable légal 1 n'est pas définie: <a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp1&amp;quitter_la_page=y#adresse' target='_blank' onclick=\"affiche_message_raffraichissement(); return confirm_abandon (this, change, '$themessage');\">Définir l'adresse du responsable légal 1</a>\n";
						echo "L'adresse du responsable légal 1 n'est pas définie: <a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp1&amp;quitter_la_page=y#adresse' target='_blank' onclick=\"return confirm_abandon (this, change, '$themessage');\">Définir l'adresse du responsable légal 1</a>\n";
					}
					echo "</td>\n";
					$adr_id_1er_resp="";
				}
				else{
					echo "<td>\n";
					$lig_adr=mysql_fetch_object($res_adr);
					$adr_id_1er_resp=$lig_adr->adr_id;
					if("$lig_adr->adr1"!=""){$chaine_adr1.="$lig_adr->adr1, ";}
					if("$lig_adr->adr2"!=""){$chaine_adr1.="$lig_adr->adr2, ";}
					if("$lig_adr->adr3"!=""){$chaine_adr1.="$lig_adr->adr3, ";}
					if("$lig_adr->adr4"!=""){$chaine_adr1.="$lig_adr->adr4, ";}
					if("$lig_adr->cp"!=""){$chaine_adr1.="$lig_adr->cp, ";}
					if("$lig_adr->commune"!=""){$chaine_adr1.="$lig_adr->commune";}
					if("$lig_adr->pays"!=""){$chaine_adr1.=" (<i>$lig_adr->pays</i>)";}
					echo $chaine_adr1;
					echo "</td>\n";
					//if($_SESSION['statut']!="professeur") {
					if(in_array($_SESSION['statut'], array("administrateur", "scolarite"))) {
						echo "<td>\n";
						//echo "<a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp1#adresse' target='_blank'>Modifier l'adresse du responsable</a>\n";
						//echo "<a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp1&amp;quitter_la_page=y#adresse' onClick='affiche_message_raffraichissement();' target='_blank'>Modifier l'adresse du responsable</a>\n";
						//echo "<a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp1&amp;quitter_la_page=y#adresse' onclick=\"affiche_message_raffraichissement(); return confirm_abandon (this, change, '$themessage');\" target='_blank'>Modifier l'adresse du responsable</a>\n";
						echo "<a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp1&amp;quitter_la_page=y#adresse' onclick=\"return confirm_abandon (this, change, '$themessage');\" target='_blank'>Modifier l'adresse du responsable</a>\n";
						echo "</td>\n";
					}
				}
				echo "</tr>\n";
				//echo "</table>\n";
			}
		}





		$chaine_adr2='';
		//if($eleve_no_resp2==0){
		if("$eleve_no_resp2"=="0"){
			// Le responsable 2 n'est pas défini:
			if($temoin_tableau=="oui"){echo "</table>\n";$temoin_tableau="non";}

			//if($_SESSION['statut']=="professeur") {
			if(!in_array($_SESSION['statut'], array("administrateur", "scolarite"))) {
				echo "<p>Le responsable légal 2 n'est pas défini: </p>\n";
 			}
			else{
				echo "<p>Le responsable légal 2 n'est pas défini: <a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;definir_resp=2";
				if (isset($order_type)) {echo "&amp;order_type=$order_type";}
				if (isset($quelles_classes)) {echo "&amp;quelles_classes=$quelles_classes";}
				if (isset($motif_rech)) {echo "&amp;motif_rech=$motif_rech";}
				if (isset($mode_rech)) {echo "&amp;mode_rech=$mode_rech";}
				echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">Définir le responsable légal 2</a></p>\n";
			}
		}
		else{
			$sql="SELECT nom,prenom FROM resp_pers WHERE pers_id='$eleve_no_resp2'";
			$res_resp=mysql_query($sql);
			if(mysql_num_rows($res_resp)==0){
				// Bizarre: Le responsable 2 n'est pas défini:
				if($temoin_tableau=="oui"){echo "</table>\n";$temoin_tableau="non";}

				//if($_SESSION['statut']=="professeur") {
				if(!in_array($_SESSION['statut'], array("administrateur", "scolarite"))) {
					echo "<p>Le responsable légal 2 n'est pas défini.</p>\n";
				}
				else{
					echo "<p>Le responsable légal 2 n'est pas défini: <a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;definir_resp=2";
					if (isset($order_type)) {echo "&amp;order_type=$order_type";}
					if (isset($quelles_classes)) {echo "&amp;quelles_classes=$quelles_classes";}
					if (isset($motif_rech)) {echo "&amp;motif_rech=$motif_rech";}
					if (isset($mode_rech)) {echo "&amp;mode_rech=$mode_rech";}
					echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">Définir le responsable légal 2</a></p>\n";
				}
			}
			else{
				$lig_resp=mysql_fetch_object($res_resp);

				if($temoin_tableau!="oui"){
					echo "<table border='0' summary='Responsable légal 2'>\n";
					$temoin_tableau="oui";
				}
				echo "<tr valign='top'>\n";
				echo "<td rowspan='2'>Le responsable légal 2 est: </td>\n";
				//if($_SESSION['statut']=="professeur") {
				if(!in_array($_SESSION['statut'], array("administrateur", "scolarite"))) {
					echo "<td>".casse_mot($lig_resp->prenom,'majf2')." ".my_strtoupper($lig_resp->nom)."</td>\n";
				}
				else{
					echo "<td><a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp2&amp;quitter_la_page=y' onclick=\"return confirm_abandon (this, change, '$themessage');\" target='_blank'>".casse_mot($lig_resp->prenom,'majf2')." ".my_strtoupper($lig_resp->nom)."</a></td>\n";

					echo "<td><a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;definir_resp=2";
					if (isset($order_type)) {echo "&amp;order_type=$order_type";}
					if (isset($quelles_classes)) {echo "&amp;quelles_classes=$quelles_classes";}
					if (isset($motif_rech)) {echo "&amp;motif_rech=$motif_rech";}
					if (isset($mode_rech)) {echo "&amp;mode_rech=$mode_rech";}
					echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">Changer de responsable</a></td>\n";
				}
				echo "</tr>\n";

				echo "<tr valign='top'>\n";
				// La 1ère colonne est dans le rowspan

				$sql="SELECT ra.* FROM resp_adr ra, resp_pers rp WHERE rp.pers_id='$eleve_no_resp2' AND rp.adr_id=ra.adr_id";
				$res_adr=mysql_query($sql);
				if(mysql_num_rows($res_adr)==0){
					// L'adresse du responsable 2 n'est pas définie:
					echo "<td colspan='2'>\n";
					//if($_SESSION['statut']=="professeur") {
					if(!in_array($_SESSION['statut'], array("administrateur", "scolarite"))) {
						echo "L'adresse du responsable légal 2 n'est pas définie.\n";
					}
					else{
						//echo "L'adresse du responsable légal 2 n'est pas définie: <a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp2#adresse' target='_blank'>Définir l'adresse du responsable légal 2</a>\n";
						//echo "L'adresse du responsable légal 2 n'est pas définie: <a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp2&amp;quitter_la_page=y#adresse' target='_blank' onclick=\"affiche_message_raffraichissement(); return confirm_abandon (this, change, '$themessage');\">Définir l'adresse du responsable légal 2</a>\n";
						echo "L'adresse du responsable légal 2 n'est pas définie: <a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp2&amp;quitter_la_page=y#adresse' target='_blank' onclick=\"return confirm_abandon (this, change, '$themessage');\">Définir l'adresse du responsable légal 2</a>\n";
					}
					echo "</td>\n";
				}
				else{
					echo "<td>\n";
					$lig_adr=mysql_fetch_object($res_adr);

					if(!isset($adr_id_1er_resp)) {$adr_id_1er_resp='';}
					if(($lig_adr->adr_id!="")&&($lig_adr->adr_id!=$adr_id_1er_resp)){
						$adr_id_2eme_resp=$lig_adr->adr_id;
						if("$lig_adr->adr1"!=""){$chaine_adr2.="$lig_adr->adr1, ";}
						if("$lig_adr->adr2"!=""){$chaine_adr2.="$lig_adr->adr2, ";}
						if("$lig_adr->adr3"!=""){$chaine_adr2.="$lig_adr->adr3, ";}
						if("$lig_adr->adr4"!=""){$chaine_adr2.="$lig_adr->adr4, ";}
						if("$lig_adr->cp"!=""){$chaine_adr2.="$lig_adr->cp, ";}
						if("$lig_adr->commune"!=""){$chaine_adr2.="$lig_adr->commune";}
						if("$lig_adr->pays"!=""){$chaine_adr2.=" (<i>$lig_adr->pays</i>)";}

						//if("$chaine_adr1"=="$chaine_adr2"){
						if(casse_mot("$chaine_adr1",'min')==casse_mot("$chaine_adr2",'min')){
							echo "$chaine_adr2<br />\n<span style='color: red;'>Les adresses sont identiques, mais sont enregistrées sous deux identifiants différents (<i>$adr_id_1er_resp et $lig_adr->adr_id</i>); vous devriez modifier l'adresse pour pointer vers le même identifiant d'adresse.</span>";
						}
						else{
							echo "$chaine_adr2";
						}
					}
					else{
						echo "Même adresse.";
					}
					echo "</td>\n";
					//if($_SESSION['statut']!="professeur") {
					if(in_array($_SESSION['statut'], array("administrateur", "scolarite"))) {
						echo "<td>\n";
						//echo "<a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp2#adresse' target='_blank'>Modifier l'adresse du responsable</a>\n";
						//echo "<a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp2&amp;quitter_la_page=y#adresse' onClick='affiche_message_raffraichissement();' target='_blank'>Modifier l'adresse du responsable</a>\n";
						//echo "<a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp2&amp;quitter_la_page=y#adresse' onclick=\"affiche_message_raffraichissement(); return confirm_abandon (this, change, '$themessage');\" target='_blank'>Modifier l'adresse du responsable</a>\n";
						echo "<a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp2&amp;quitter_la_page=y#adresse' onclick=\"return confirm_abandon (this, change, '$themessage');\" target='_blank'>Modifier l'adresse du responsable</a>\n";
						if((isset($adr_id_1er_resp))&&(isset($adr_id_2eme_resp))){
							if("$adr_id_1er_resp"!="$adr_id_2eme_resp"){
								echo "<br />";
								echo "<a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;modif_adr_pers_id=$eleve_no_resp2&amp;adr_id=$adr_id_1er_resp";
								if (isset($order_type)) {echo "&amp;order_type=$order_type";}
								if (isset($quelles_classes)) {echo "&amp;quelles_classes=$quelles_classes";}
								if (isset($motif_rech)) {echo "&amp;motif_rech=$motif_rech";}
								if (isset($mode_rech)) {echo "&amp;mode_rech=$mode_rech";}
								//echo "'>Prendre l'adresse de l'autre responsable</a>";
								echo add_token_in_url();
								echo "' onclick=\"return confirm_abandon (this, change, '$themessage');\">Prendre l'adresse de l'autre responsable</a>";
							}
						}
						echo "</td>\n";
					}
				}
				echo "</tr>\n";
				//echo "</table>\n";
			}
		}
		if($temoin_tableau=="oui"){echo "</table>\n";$temoin_tableau="non";}


		echo "<script type='text/javascript'>
	function affiche_message_raffraichissement() {
		document.getElementById('message_target_blank').innerHTML=\"Pensez à rafraichir la page après modification de l'adresse responsable.<br />Cependant, si vous avez modifié des informations dans la présente page, pensez à les enregistrer avant de recharger la page.\";
	}
</script>\n";


		if("$chaine_adr2"!=""){
			if("$chaine_adr1"!=""){
				if("$chaine_adr1"!="$chaine_adr2"){
					echo "<p><b>Les adresses des deux responsables légaux ne sont pas identiques. Par conséquent, le bulletin sera envoyé aux deux responsables légaux.</b></p>\n";
				}
				else{
					echo "<p><b>Les adresses des deux responsables légaux sont identiques. Par conséquent, le bulletin ne sera envoyé qu'à la première adresse.</b>";
					echo "</p>\n";
				}
			}
			else{
				echo "<p><b>Le bulletin ne sera envoyé qu'au deuxième responsable.</b></p>\n";
			}
		}
		else{
			if("$chaine_adr1"!=""){
				echo "<p><b>Le bulletin ne sera envoyé qu'au premier responsable.</b></p>\n";
			}
			else{
				echo "<p><b>Aucune adresse n'est renseignée. Le bulletin ne pourra pas être envoyé.</b></p>\n";
			}
		}


		//if(($eleve_no_resp1==0)||($eleve_no_resp2==0)){
		if(("$eleve_no_resp1"=="0")||("$eleve_no_resp2"=="0")){
			//if($_SESSION['statut']=="professeur") {
			if(!in_array($_SESSION['statut'], array("administrateur", "scolarite"))) {
				echo "<p>Si le responsable légal ne figure pas dans la liste, prenez contact avec l'administrateur ou avec une personne disposant du statut 'scolarité'.</p>\n";
			}
			else{
				echo "<p>Si le responsable légal ne figure pas dans la liste, vous pouvez l'ajouter à la base<br />\n";
				echo "(<i>après avoir, le cas échéant, sauvegardé cette fiche</i>)<br />\n";

				if($_SESSION['statut']=="scolarite") {
					echo "en vous rendant dans [<a href='../responsables/index.php'>Gestion des fiches responsables élèves</a>]</p>\n";
				}
				else{
					echo "en vous rendant dans [Gestion des bases-><a href='../responsables/index.php'>Gestion des responsables élèves</a>]</p>\n";
				}
			}
		}
	}
}



//if(isset($eleve_login)){
if((isset($eleve_login))&&(isset($reg_no_gep))&&($reg_no_gep!="")) {

	echo "<br />\n";
	echo "<hr />\n";

	echo "<h3>Etablissement d'origine</h3>\n";

	//$sql="SELECT * FROM j_eleves_etablissements WHERE id_eleve='$eleve_login'";
	$sql="SELECT * FROM j_eleves_etablissements WHERE id_eleve='$reg_no_gep'";
	$res_etab=mysql_query($sql);
	if(mysql_num_rows($res_etab)==0) {
		echo "<p>L'établissement d'origine de l'élève n'est pas renseigné.";
		//if($_SESSION['statut']!="professeur") {
		if(in_array($_SESSION['statut'], array("administrateur", "scolarite"))) {
			echo "<br />\n";
			echo "<a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;definir_etab=y";
			//echo "<a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;reg_no_gep=$reg_no_gep&amp;definir_etab=y";
			if (isset($order_type)) {echo "&amp;order_type=$order_type";}
			if (isset($quelles_classes)) {echo "&amp;quelles_classes=$quelles_classes";}
			if (isset($motif_rech)) {echo "&amp;motif_rech=$motif_rech";}
			if (isset($mode_rech)) {echo "&amp;mode_rech=$mode_rech";}
			echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">Renseigner l'établissement d'origine</a>";
		}
		echo "</p>\n";
	}
	else{
		$lig_etab=mysql_fetch_object($res_etab);

		if("$lig_etab->id_etablissement"==""){
			//if($_SESSION['statut']=="professeur") {
			if(!in_array($_SESSION['statut'], array("administrateur", "scolarite"))) {
				echo "<p>L'établissement d'origine de l'élève n'est pas renseigné.</p>\n";
			}
			else{
				echo "<p><a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;definir_etab=y";
				//echo "<p><a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;reg_no_gep=$reg_no_gep&amp;definir_etab=y";
				if (isset($order_type)) {echo "&amp;order_type=$order_type";}
				if (isset($quelles_classes)) {echo "&amp;quelles_classes=$quelles_classes";}
				if (isset($motif_rech)) {echo "&amp;motif_rech=$motif_rech";}
				if (isset($mode_rech)) {echo "&amp;mode_rech=$mode_rech";}
				echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">Définir l'établissement d'origine</a>";
				echo "</p>\n";
			}
		}
		else{
			$sql="SELECT * FROM etablissements WHERE id='$lig_etab->id_etablissement'";
			$res_etab2=mysql_query($sql);
			if(mysql_num_rows($res_etab2)==0) {
				echo "<p>L'association avec l'identifiant d'établissement existe (<i>$lig_etab->id_etablissement</i>), mais les informations correspondantes n'existent pas dans la table 'etablissement'.";
				//if($_SESSION['statut']!="professeur") {
				if(in_array($_SESSION['statut'], array("administrateur", "scolarite"))) {
					echo "<br />\n";

					echo "<a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;definir_etab=y";
					
					if (isset($order_type)) {echo "&amp;order_type=$order_type";}
					if (isset($quelles_classes)) {echo "&amp;quelles_classes=$quelles_classes";}
					if (isset($motif_rech)) {echo "&amp;motif_rech=$motif_rech";}
					if (isset($mode_rech)) {echo "&amp;mode_rech=$mode_rech";}

					echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">Modifier l'établissement d'origine</a>";
				}
				echo "</p>\n";
			}
			else{
				echo "<p>L'établissement d'origine de l'élève est&nbsp;:<br />\n";
				$lig_etab2=mysql_fetch_object($res_etab2);
				echo "&nbsp;&nbsp;&nbsp;";
				if($lig_etab2->niveau=="college"){
					echo "Collège";
				}
				elseif($lig_etab2->niveau=="lycee"){
					echo "Lycée";
				}
				else{
					echo casse_mot($lig_etab2->niveau,'majf2');
				}
				echo " ".$lig_etab2->type." ".$lig_etab2->nom.", ".$lig_etab2->cp.", ".$lig_etab2->ville." (<i>$lig_etab->id_etablissement</i>)";
				//if($_SESSION['statut']!="professeur") {
				if(in_array($_SESSION['statut'], array("administrateur", "scolarite"))) {
					echo "<br />\n";
					echo "<a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;definir_etab=y";
					if (isset($order_type)) {echo "&amp;order_type=$order_type";}
					if (isset($quelles_classes)) {echo "&amp;quelles_classes=$quelles_classes";}
					if (isset($motif_rech)) {echo "&amp;motif_rech=$motif_rech";}
					if (isset($mode_rech)) {echo "&amp;mode_rech=$mode_rech";}
					echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">Modifier l'établissement d'origine</a>";
				}
				echo "</p>\n";
			}
		}
	}
	echo "<p><br /></p>\n";
}

if((isset($eleve_login))&&($compte_eleve_existe=="y")&&($journal_connexions=='n')&&
		(
			($_SESSION['statut']=="administrateur")||
			(($_SESSION['statut']=="scolarite")&&(getSettingAOui('AccesDetailConnexionEleScolarite')))||
			(($_SESSION['statut']=="cpe")&&(getSettingAOui('AccesDetailConnexionEleCpe')))
		)
	) {
		echo "<hr />\n";

		echo "<p><a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;journal_connexions=y#connexion' title='Journal des connexions'>Journal des connexions</a></p>\n";
	//}
}


if((isset($eleve_login))&&($compte_eleve_existe=="y")&&($journal_connexions=='y')&&
		(
			($_SESSION['statut']=="administrateur")||
			(($_SESSION['statut']=="scolarite")&&(getSettingAOui('AccesDetailConnexionEleScolarite')))||
			(($_SESSION['statut']=="cpe")&&(getSettingAOui('AccesDetailConnexionEleCpe')))
		)
	) {
	echo "<hr />\n";
	// Journal des connexions
	echo "<a name=\"connexion\"></a>\n";
	if (isset($_POST['duree'])) {
		$duree = $_POST['duree'];
	} else {
		$duree = '7';
	}
	
	journal_connexions($eleve_login,$duree,'modify_eleve');
	echo "<p><br /></p>\n";
}


require("../lib/footer.inc.php");
?>
