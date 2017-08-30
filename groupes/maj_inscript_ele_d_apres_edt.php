<?php
/*
*
* Copyright 2001, 2016 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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


$sql="SELECT 1=1 FROM droits WHERE id='/groupes/maj_inscript_ele_d_apres_edt.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/groupes/maj_inscript_ele_d_apres_edt.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Import des inscriptions élèves depuis un XML EDT',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}


$sql="CREATE TABLE IF NOT EXISTS edt_eleves_lignes (
id int(11) NOT NULL AUTO_INCREMENT,
nom varchar(255) NOT NULL default '',
prenom varchar(255) NOT NULL default '',
date_naiss varchar(255) NOT NULL default '',
sexe varchar(255) NOT NULL default '',
n_national varchar(255) NOT NULL default '',
classe varchar(255) NOT NULL default '',
groupes varchar(255) NOT NULL default '',
option_1 varchar(255) NOT NULL default '',
option_2 varchar(255) NOT NULL default '',
option_3 varchar(255) NOT NULL default '',
option_4 varchar(255) NOT NULL default '',
option_5 varchar(255) NOT NULL default '',
option_6 varchar(255) NOT NULL default '',
option_7 varchar(255) NOT NULL default '',
option_8 varchar(255) NOT NULL default '',
option_9 varchar(255) NOT NULL default '',
option_10 varchar(255) NOT NULL default '',
option_11 varchar(255) NOT NULL default '',
option_12 varchar(255) NOT NULL default '',
PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

$sql="CREATE TABLE IF NOT EXISTS edt_tempo (
id int(11) NOT NULL AUTO_INCREMENT,
col1 varchar(255) NOT NULL default '',
col2 varchar(255) NOT NULL default '',
PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

$acces_prepa_conseil_edit_limite="y";
if(!acces('/prepa_conseil/edit_limite.php', $_SESSION['statut'])) {
	$acces_prepa_conseil_edit_limite="n";
}

$debug_import_edt="n";
$avec_colonnes_detail_groupes_eleves="y"; // Le paramètre $debug_import_edt ci-dessus affecté à 'y' force l'affichage des colonnes groupes/classes EDT.

$msg="";
$action=isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : "");
$num_periode=isset($_POST['num_periode']) ? $_POST['num_periode'] : (isset($_GET['num_periode']) ? $_GET['num_periode'] : NULL);
$id_groupe=isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);

if(isset($_POST['MajInscriptEleEdtEnvoiMail'])) {
	check_token();
	if(!saveSetting('MajInscriptEleEdtEnvoiMail', $_POST['MajInscriptEleEdtEnvoiMail'])) {
		$msg="Erreur lors de l'enregistrement du paramètre 'MajInscriptEleEdtEnvoiMail' (".strftime("%d/%m/%Y à %H:%M:%S").").<br />";
	}
	else {
		$msg="Enregistrement du paramètre 'MajInscriptEleEdtEnvoiMail' effectué (".strftime("%d/%m/%Y à %H:%M:%S").").<br />";
	}
}

if((isset($_POST['mode']))&&($_POST['mode']=="cherche_eleve")) {
	$nom_ele=isset($_POST['nom_ele']) ? $_POST['nom_ele'] : NULL;
	$prenom_ele=isset($_POST['prenom_ele']) ? $_POST['prenom_ele'] : NULL;

	if((isset($nom_ele))&&(isset($prenom_ele))) {
		//$sql="SELECT * FROM eleves WHERE nom LIKE '%".mysqli_real_escape_string($GLOBALS['mysqli'], $nom_ele)."%' AND prenom LIKE '%".mysqli_real_escape_string($GLOBALS['mysqli'], $prenom_ele)."%' ORDER BY nom, prenom;";
		$sql="SELECT * FROM eleves WHERE nom LIKE '%".preg_replace("/[^A-Za-z]/u", "%", $nom_ele)."%' AND prenom LIKE '%".preg_replace("/[^A-Za-z]/u", "%", $prenom_ele)."%' ORDER BY nom, prenom;";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			echo "<p style='color:red'>Aucun élève trouvé.</p>";
		}
		else {
			echo "<table class='boireaus boireaus_alt'>
	<tr>
		<th>Nom</th>
		<th>Prénom</th>
		<th>Naissance</th>
		<th>INE</th>
		<th>Classes</th>
	</tr>";
			while($lig=mysqli_fetch_object($res)){
				echo "
	<tr style='text-align: center;'>
		<td>".$lig->nom."</td>
		<td>".$lig->prenom."</td>
		<td>".formate_date($lig->naissance)."</td>
		<td>";
				if($lig->no_gep!=""){
					//echo '<a href=\'#\' onClick=\"document.getElementById(document.getElementById(\'ine_recherche\').value).value=\''.$lig->no_gep.'\';cacher_div(\'div_search\');return false;\">'.$lig->no_gep.'</a>';
					echo '<a href=\'#\' onClick="corrige_ine(\''.$lig->no_gep.'\');return false;">'.$lig->no_gep.'</a>';
				}
				else{
					echo "<span style='color:red'>Non renseigné</span>";
				}
				echo "</td>
		<td>".get_chaine_liste_noms_classes_from_ele_login($lig->login)."</td>
	</tr>";
			}
			echo "</table>";
		}
	}
	else {
		echo "<p style='color:red'>Nom ou prénom non transmis.</p>";
	}

	die();
}

// 20151021
if($action=="corrige_ine_eleve") {
	check_token();

	//debug_var();

	$edt_eleves_lignes_id=isset($_POST['edt_eleves_lignes_id']) ? $_POST['edt_eleves_lignes_id'] : NULL;
	$ine_corrige=isset($_POST['ine_corrige']) ? $_POST['ine_corrige'] : NULL;
	$nom_eleve=isset($_POST['nom_eleve']) ? $_POST['nom_eleve'] : array();
	$prenom_eleve=isset($_POST['prenom_eleve']) ? $_POST['prenom_eleve'] : array();

	if(!isset($edt_eleves_lignes_id)) {
		$msg.="Aucun identifiant élève de la table 'edt_eleves_lignes' n'a été transmis.<br />";
	}
	elseif(!isset($ine_corrige)) {
		$msg.="Le tableau des INE potentiellement corrigés n'a pas été transmis.<br />";
	}
	else {
		$msg.="Prise en compte des corrections d'INE&nbsp;: <br />";
		for($loop=0;$loop<count($edt_eleves_lignes_id);$loop++) {
			if((isset($ine_corrige[$loop]))&&($ine_corrige[$loop]!="")) {
				$msg.="Mise à jour de l'INE pour ".$prenom_eleve[$loop]." ".$nom_eleve[$loop]." (".$ine_corrige[$loop].")&nbsp;: ";
				$sql="UPDATE edt_eleves_lignes SET n_national='".$ine_corrige[$loop]."' WHERE id='".$edt_eleves_lignes_id[$loop]."';";
				//$msg.="$sql<br />";
				$update=mysqli_query($GLOBALS["mysqli"], $sql);
				if($update) {
					$msg.="<span style='color:green'>OK</span>";
				}
				else {
					$msg.="<span style='color:red'>ECHEC</span>";
				}
				$msg.="<br />";
			}
		}
	}

	$action="comparer";
}

if((isset($_POST['valider_ec3']))&&(isset($id_groupe))&&(isset($_POST['id_nom_edt']))) {
	check_token();

	if($_POST['id_nom_edt']=="") {
		$sql="DELETE FROM edt_corresp2 WHERE id_groupe='$id_groupe';";
		$del=mysqli_query($GLOBALS["mysqli"], $sql);
		$msg="Association supprimée.<br />";

		if((isset($_POST['mode_js']))&&($_POST['mode_js']='y')) {
			echo "Aucun regroupement EDT n'est associé à ce groupe Gepi";
			die();
		}
	}
	else {
		$sql="SELECT * FROM edt_corresp WHERE id='".$_POST['id_nom_edt']."';";
		$res_edt=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_edt)==0) {
			$msg="L'identifiant nom_edt choisi ".$_POST['id_nom_edt']." n'existe pas.<br />";

			if((isset($_POST['mode_js']))&&($_POST['mode_js']='y')) {
				echo "Regroupement EDT associé&nbsp;: <span style='color:red'>L'identifiant nom_edt choisi ".htmlentities($_POST['id_nom_edt'])." n'existe pas.</span>";
				die();
			}
		}
		else {
			$lig_edt=mysqli_fetch_object($res_edt);

			$sql="DELETE FROM edt_corresp2 WHERE id_groupe='$id_groupe';";
			$del=mysqli_query($GLOBALS["mysqli"], $sql);

			// Problème? On ne saisit pas le nom de matière EDT
			$sql="INSERT INTO edt_corresp2 SET id_groupe='$id_groupe', nom_groupe_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig_edt->nom_edt)."';";
			$insert=mysqli_query($GLOBALS["mysqli"], $sql);
			if($insert) {
				$msg="Association enregistrée.<br />";

				if((isset($_POST['mode_js']))&&($_POST['mode_js']='y')) {
					echo "Regroupement EDT associé&nbsp;: ".htmlentities($lig_edt->nom_edt);
					die();
				}
			}
			else {
				$msg="Erreur lors de l'enregistrement de l'association.<br />";

				if((isset($_POST['mode_js']))&&($_POST['mode_js']='y')) {
					echo "Regroupement EDT associé&nbsp;: <span style='color:red'>ERREUR</span>";
					die();
				}
			}

			//$sql="SELECT * FROM edt_corresp2 WHERE id_groupe='$id_groupe';";
			//$res_grp=mysqli_query($GLOBALS["mysqli"], $sql);
			//if(mysqli_num_rows($res_grp)>0) {
			//	$sql="UPDATE SELECT * FROM edt_corresp2 WHERE id_groupe='$id_groupe';";
			//	$res_grp=mysqli_query($GLOBALS["mysqli"], $sql);
			//}
			//else {

			//}
		}
	}
}

if((isset($_GET['div_suppr_assoc_regroupement']))&&(isset($_GET['id_groupe']))&&(preg_match("/^[0-9]{1,}$/", $_GET['id_groupe']))&&(isset($_GET['id_edt_tempo']))&&(preg_match("/^[0-9]{1,}$/", $_GET['id_edt_tempo']))) {
	$sql="SELECT * FROM edt_corresp2 ec2, edt_tempo et WHERE ec2.nom_groupe_edt=et.col1 AND et.id='".$_GET['id_edt_tempo']."' AND ec2.id_groupe='".$_GET['id_groupe']."';";
	//echo "$sql<br />";
	$res_grp=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_grp)==0) {
		echo "<p style='color:red'>L'identifiant de groupe Gepi et celui EDT ne correspondent pas.</p>";
	}
	else {

		$sql="UPDATE edt_lignes SET traitement='', details_cours='' WHERE details_cours LIKE '".$_GET['id_groupe']."|%';";
		$mise_a_blanc=mysqli_query($GLOBALS["mysqli"], $sql);

		$sql="DELETE FROM edt_corresp2 WHERE nom_groupe_edt IN (SELECT col1 FROM edt_tempo WHERE id='".$_GET['id_edt_tempo']."') AND id_groupe='".$_GET['id_groupe']."';";
		//echo "$sql<br />";
		$suppr=mysqli_query($GLOBALS["mysqli"], $sql);
		if($suppr) {
			echo "<span style='color:green'>Association supprimée.</span>";
		}
		else {
			echo "<span style='color:red'>Erreur lors de la suppression de l'association.</span>";
		}
	}
	die();
}

if(($action=="editer_ec2")&&(isset($_POST['suppr_assoc']))&&($_POST['suppr_assoc']=="y")&&(isset($_POST['suppr']))) {
	check_token();

	$msg="";

	$nb_suppr=0;
	$suppr=$_POST['suppr'];
	for($loop=0;$loop<count($suppr);$loop++) {
		$sql="SELECT * FROM edt_corresp2 ec2 WHERE ec2.id='".$suppr[$loop]."';";
		//echo "$sql<br />";
		$res_grp=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_grp)==0) {
			$msg.="Association n°".$suppr[$loop]." non trouvée.<br />";
		}
		else {
			$lig_grp=mysqli_fetch_object($res_grp);

			$sql="UPDATE edt_lignes SET traitement='', details_cours='' WHERE details_cours LIKE '".$lig_grp->id_groupe."|%';";
			$mise_a_blanc=mysqli_query($GLOBALS["mysqli"], $sql);

			$sql="DELETE FROM edt_corresp2 WHERE id='".$suppr[$loop]."';";
			//echo "$sql<br />";
			$del=mysqli_query($GLOBALS["mysqli"], $sql);
			if($del) {
				$nb_suppr++;
			}
			else {
				$msg.="Erreur lors de la suppression de l'association n°".$suppr[$loop].".<br />";
			}
		}
	}

	$msg.=$nb_suppr." association(s) supprimée(s).<br />";
}

if(($action=="editer_ec2")&&(isset($_POST['suppr_assoc']))&&($_POST['suppr_assoc']=="y2")&&(isset($_POST['suppr']))) {
	check_token();

	$msg="";

	$nb_suppr=0;
	$suppr=$_POST['suppr'];
	for($loop=0;$loop<count($suppr);$loop++) {
		$sql="SELECT * FROM edt_corresp2 ec2 WHERE ec2.id='".$suppr[$loop]."';";
		//echo "$sql<br />";
		$res_grp=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_grp)==0) {
			$msg.="Association n°".$suppr[$loop]." non trouvée.<br />";
		}
		else {
			$lig_grp=mysqli_fetch_object($res_grp);

			$sql="UPDATE edt_lignes SET traitement='', details_cours='' WHERE details_cours LIKE '".$lig_grp->id_groupe."|%';";
			$mise_a_blanc=mysqli_query($GLOBALS["mysqli"], $sql);

			$sql="DELETE FROM edt_corresp2 WHERE id='".$suppr[$loop]."';";
			//echo "$sql<br />";
			$del=mysqli_query($GLOBALS["mysqli"], $sql);
			if($del) {
				$nb_suppr++;
			}
			else {
				$msg.="Erreur lors de la suppression de l'association n°".$suppr[$loop].".<br />";
			}
		}
	}

	$msg.=$nb_suppr." association(s) supprimée(s).<br />";
}

if((isset($_GET['maj_composition_groupe']))&&(isset($_GET['id_groupe']))&&(preg_match("/^[0-9]{1,}$/", $_GET['id_groupe']))&&(isset($_GET['id_edt_tempo']))&&(preg_match("/^[0-9]{1,}$/", $_GET['id_edt_tempo']))&&(isset($_GET['num_periode']))&&(preg_match("/^[0-9]{1,}$/", $_GET['num_periode']))) {

	//echo "plop";

	$ts0=time();
	//echo "ts0=$ts0<br />";

	// Test pour vérifier que l'on a bien une correspondance
	$sql="SELECT 1=1 FROM edt_corresp2 ec2, edt_tempo et WHERE ec2.nom_groupe_edt=et.col1 AND et.id='".$_GET['id_edt_tempo']."' AND ec2.id_groupe='".$_GET['id_groupe']."';";
	//echo "$sql<br />";
	$res_grp=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_grp)==0) {
		echo "<p style='color:red'>L'identifiant de groupe Gepi et celui EDT ne correspondent pas.</p>";
	}
	else {
		$ts=time();
		//echo "<br />Temps écoulé ".($ts-$ts0)."<br />";

		$tab_corresp_classe=array();
		$sql="select * from edt_corresp where champ='classe';";
		$res_corresp_classe=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_corresp_classe)>0) {
			while($lig_corresp_classe=mysqli_fetch_object($res_corresp_classe)) {
				$tab_corresp_classe[]=$lig_corresp_classe->nom_edt;
			}
		}

		echo "<p style='color:green'>L'identifiant de groupe Gepi et celui EDT correspondent.</p>";

		// Problème: On peut avoir plusieurs regroupements EDT associés à un id_groupe selon ce qui a été sélectionné dans l'import EDT
		//$sql="SELECT * FROM edt_corresp2 WHERE id_groupe='".$_GET['id_groupe']."';";
		//$sql="SELECT * FROM edt_corresp2 WHERE id_groupe='".$_GET['id_groupe']."' AND nom_groupe_edt LIKE '[%]';";
		//$sql="SELECT * FROM edt_corresp2 WHERE id_groupe='".$_GET['id_groupe']."' AND nom_groupe_edt LIKE '[%]' AND nom_groupe_edt IN (SELECT col1 FROM edt_tempo);";
		$sql="SELECT * FROM edt_corresp2 WHERE id_groupe='".$_GET['id_groupe']."' AND (nom_groupe_edt LIKE '[%]' OR nom_groupe_edt LIKE '%<%>%') AND nom_groupe_edt IN (SELECT col1 FROM edt_tempo);";
		// Si on a plusieurs enregistrements, ça ne convient pas.
		//echo "$sql<br />";
		$res_grp=mysqli_query($GLOBALS["mysqli"], $sql);
		$ts=time();
		//echo "<br />Temps écoulé ".($ts-$ts0)."<br />";
		if(mysqli_num_rows($res_grp)==0) {
			echo "<p style='color:red'>Aucun regroupement EDT n'est associé au groupe Gepi n°".$_GET['id_groupe'].".</p>";
		}
		elseif(mysqli_num_rows($res_grp)>1) {
			echo "<p style='color:red'>Plusieurs regroupements EDT sont associés au groupe Gepi n°".$_GET['id_groupe'].".<br />Vous devez commencer par supprimer la ou les associations en trop.<br />Les regroupements associés sont&nbsp;:<br />";
			while($lig_grp=mysqli_fetch_object($res_grp)) {
				$sql="SELECT id FROM edt_tempo WHERE col1='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig_grp->nom_groupe_edt)."';";
				//echo "$sql<br />";
				$res_id=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_id)>0) {
					$lig_id=mysqli_fetch_object($res_id);
					echo "<a href='#regroupement_".$lig_id->id."' title=\"Voir le regroupement (plus haut ou plus bas) dans la page.\" onclick=\"afficher_tous_div_regroup('');return true;\">$lig_grp->nom_groupe_edt</a><br />";
				}
				else {
					echo "$lig_grp->nom_groupe_edt<br />";
				}
				//$current_nom_groupe=preg_replace("/\[/", "", preg_replace("/\]/", "", $lig_grp->nom_groupe_edt));
				//echo "\$current_nom_groupe=$current_nom_groupe<br />";
			}
			echo "</p>";
		}
		else {
			$tab_info_grp=array();
			$tab_ele_grp=array();

			echo "<p style='margin-left:3em;text-indent:-3em;'>Le ou les groupes suivants sont associés à ce nom de regroupement d'élèves EDT&nbsp;:<br />";

			$lig_grp=mysqli_fetch_object($res_grp);

			//echo "\$lig_grp->nom_groupe_edt=$lig_grp->nom_groupe_edt<br />";
			//$current_nom_groupe=preg_replace("/\[/", "", preg_replace("/\]/", "", $lig_grp->nom_groupe_edt));
			//echo "\$current_nom_groupe=$current_nom_groupe<br />";

			$nom_groupe_edt=$lig_grp->nom_groupe_edt;
			$nom_groupe_edt_clean=preg_replace("/\[/", "", preg_replace("/\]/", "", $lig_grp->nom_groupe_edt));

			//$chaine_nom_groupe="";

			//$sql="";
			$sql="SELECT nom,prenom,date_naiss,sexe,n_national,groupes FROM edt_eleves_lignes 
				WHERE (";
			$tmp_tab_nom_groupe=explode(",", $lig_grp->nom_groupe_edt);
			$loop2=0;
			for($loop=0;$loop<count($tmp_tab_nom_groupe);$loop++) {
				$current_nom_groupe=trim(preg_replace("/\[/", "", preg_replace("/\]/", "", $tmp_tab_nom_groupe[$loop])));
				if(!in_array($current_nom_groupe, $tab_corresp_classe)) {
					if($loop2>0) {
						$sql.=" OR ";
					}
					$sql.="classe like '$current_nom_groupe' OR 
						classe like '$current_nom_groupe, %' OR 
						classe like '%, $current_nom_groupe, %' OR 
						classe like '%, $current_nom_groupe'";
					$sql.=" OR groupes like '$current_nom_groupe' OR 
						groupes like '$current_nom_groupe, %' OR 
						groupes like '%, $current_nom_groupe, %' OR 
						groupes like '%, $current_nom_groupe'";
					$loop2++;
				}
			}
			$sql.=");";
			if($debug_import_edt=="y") {
				echo "$sql<br />";
			}

			$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);

			$ts=time();
			//echo "<br />Temps écoulé ".($ts-$ts0)."<br />";

			if(mysqli_num_rows($res_ele)==0) {
				echo "<p style='color:plum'>Le regroupement EDT est vide.</p>";
				// Faut-il vider le groupe Gepi?
			}
			else {

				// Le groupe Gepi actuel:
				$current_group=get_group($_GET['id_groupe']);

				$reg_nom_groupe = $current_group["name"];
				$reg_nom_complet = $current_group["description"];
				$reg_matiere = $current_group["matiere"]["matiere"];
				$reg_clazz = $current_group["classes"]["list"];
				$reg_professeurs = (array)$current_group["profs"]["list"];

				// Mettre à jour le $reg_eleves
				$old_reg_eleves = array();
				$reg_eleves = array();
				foreach ($current_group["periodes"] as $period) {
					if($period["num_periode"]!=""){
						if($period["num_periode"]!=$num_periode) {
							$reg_eleves[$period["num_periode"]] = $current_group["eleves"][$period["num_periode"]]["list"];
						}
						$old_reg_eleves[$period["num_periode"]] = $current_group["eleves"][$period["num_periode"]]["list"];
					}
				}


				$texte_mail="Le groupe ".$reg_nom_groupe." ($reg_nom_complet) en ".$current_group["classlist_string"]." a été mis à jour d'après EDT pour la période n°$num_periode.\nLes élèves inscrits étaient:\n";
				for($loop=0;$loop<count($old_reg_eleves[$num_periode]);$loop++) {
					$current_login_ele=$old_reg_eleves[$num_periode][$loop];
					$texte_mail.=get_nom_prenom_eleve($current_login_ele)."\n";
				}
				$texte_mail.="Effectif ".count($old_reg_eleves[$num_periode])."\n";


				$reserves_sur_maj=0;
				echo "<p style='color:blue'>Les élèves du regroupement EDT sont&nbsp;: ";
				$cpt_ele=0;
				while($lig_ele=mysqli_fetch_object($res_ele)) {
					if($cpt_ele>0) {
						echo ", ";
						echo "<br />";
					}
					echo $lig_ele->nom." ".$lig_ele->prenom." (".$lig_ele->date_naiss.") (".$lig_ele->n_national.")";

// Si $lig_ele->n_national est vide, il faut tenter d'identifier autrement l'élève (nom, prénom, date de naissance).
					if($lig_ele->n_national!="") {
						$sql="SELECT login, date_sortie FROM eleves WHERE no_gep='".$lig_ele->n_national."';";
					}
					else {
						$sql="SELECT login, date_sortie FROM eleves WHERE nom='".$lig_ele->nom."' AND prenom='".$lig_ele->prenom."';";
					}
					//echo "$sql<br />";
					$res_nn=mysqli_query($GLOBALS["mysqli"], $sql);

					$ts=time();
					//echo "<br />Temps écoulé ".($ts-$ts0)."<br />";

					if(mysqli_num_rows($res_nn)==0) {
						echo " <span style='color:red'>INE ".$lig_ele->n_national." non trouvé dans la table 'eleves'</span>";
						$reserves_sur_maj++;
					}
					elseif(mysqli_num_rows($res_nn)==1) {
						$lig_nn=mysqli_fetch_object($res_nn);

						$tab_ele_regroupement_edt['login'][]=$lig_nn->login;
						$tab_ele_regroupement_edt['date_sortie'][]=$lig_nn->date_sortie;
						$tab_ele_regroupement_edt['nom'][]=$lig_ele->nom;
						$tab_ele_regroupement_edt['prenom'][]=$lig_ele->prenom;
						$tab_ele_regroupement_edt['date_naiss'][]=$lig_ele->date_naiss;
						$tab_ele_regroupement_edt['n_national'][]=$lig_ele->n_national;

						//echo "<br />\$lig_nn->login=$lig_nn->login<br />";
						//echo "<br />\$num_periode=$num_periode<br />";
						$id_classe="";
						$classe="";
						$tmp_tab=get_class_periode_from_ele_login($lig_nn->login);

						//echo "<pre>";
						//print_r($tmp_tab);
						//echo "</pre>";

						if(isset($tmp_tab['periode'][$num_periode]['id_classe'])) {
							$id_classe=$tmp_tab['periode'][$num_periode]['id_classe'];

							$inscrire="y";
							// Contrôler que le $id_classe est bien associé au groupe
							if(!in_array($id_classe, $reg_clazz)) {
								// Il faudrait vérifier que le nombre de périodes de la classe est le même que pour les classes déjà inscrites.
								$sql="SELECT MAX(num_periode) AS maxper FROM periodes WHERE id_classe='$id_classe';";
								$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($res_per)==0) {
									$inscrire="n";
									echo "<br /><span style='color:red'>La classe de l'élève ".$lig_ele->nom." ".$lig_ele->prenom." n'a pas de période (<em>l'élève ne peut pas être ajouté au groupe Gepi</em>)</span>";
									$texte_mail.="Ajout de ".$lig_ele->nom." ".$lig_ele->prenom." impossible (élève non trouvé).\n";
									$reserves_sur_maj++;
								}
								else {
									$lig_per=mysqli_fetch_object($res_per);
									if($lig_per->maxper==count($current_group["periodes"])) {
										$reg_clazz[]=$id_classe;
									}
									else {
										$inscrire="n";
										echo "<br /><span style='color:red'>La classe de l'élève ".$lig_ele->nom." ".$lig_ele->prenom." n'a pas le même nombre de périodes que les autres classes du groupe (<em>l'élève ne peut pas être ajouté au groupe Gepi</em>)</span>";
										$texte_mail.="Ajout de ".$lig_ele->nom." ".$lig_ele->prenom." impossible (nombre de périodes incompatible).\n";
										$reserves_sur_maj++;
									}
								}
							}
							if($inscrire=="y") {
								$reg_eleves[$num_periode][]=$lig_nn->login;
							}
						}
						else {
							echo "<br /><span style='color:red'>L'élève ".$lig_ele->nom." ".$lig_ele->prenom." n'est dans aucune classe (<em>il ne peut pas être ajouté au groupe Gepi</em>)</span>";
							$texte_mail.="Ajout de ".$lig_ele->nom." ".$lig_ele->prenom." impossible (élève inscrit dans aucune classe).\n";
							$reserves_sur_maj++;
						}

						$tab_ele_regroupement_edt['id_classe'][]=$id_classe;
						if(isset($tmp_tab['periode'][$num_periode]['classe'])) {$classe=$tmp_tab['periode'][$num_periode]['classe'];}
						$tab_ele_regroupement_edt['classe'][]=$classe;
					}
					else {
						echo " <span style='color:red'>Plusieurs élèves trouvés dans la table 'eleves' pour cet INE (???)</span>";
						$reserves_sur_maj++;
					}
					$cpt_ele++;
				}

				echo "</p>";

				for($loop=0;$loop<count($old_reg_eleves[$num_periode]);$loop++) {
					if(!in_array($old_reg_eleves[$num_periode][$loop], $reg_eleves[$num_periode])) {
						// Un élève est supprimé du groupe Gepi
						// On vérifie que la suppression est possible.

						$temoin_bull_ou_cn_non_vide=0;
						$current_login_ele=$old_reg_eleves[$num_periode][$loop];
						$current_id_groupe=$_GET['id_groupe'];

						$temoin="";
						if (!test_before_eleve_removal($current_login_ele, $current_id_groupe, $num_periode)) {
							if(($acces_prepa_conseil_edit_limite=="y")&&($current_ele['classe']!="")) {
								$temoin.="<a href='../prepa_conseil/edit_limite.php?choix_edit=2&login_eleve=".$current_login_ele."&id_classe=".$current_ele['classe']."&periode1=".$num_periode."&periode2=".$num_periode."' target='_blank'>";
								$temoin.="<img src='../images/icons/bulletin_16.png' width='16' height='16' title='Bulletin non vide' alt='Bulletin non vide' />";
								$temoin.="</a>";
							}
							else {
								$temoin.="<img src='../images/icons/bulletin_16.png' width='16' height='16' title='Bulletin non vide' alt='Bulletin non vide' />";
							}
							$temoin_bull_ou_cn_non_vide++;
						}

						$nb_notes_cn=nb_notes_ele_dans_tel_enseignement($current_login_ele, $current_id_groupe, $num_periode);
						if($nb_notes_cn>0) {
							$temoin.="<img src='../images/icons/cn_16.png' width='16' height='16' title='Carnet de notes non vide: $nb_notes_cn notes' alt='Carnet de notes non vide: $nb_notes_cn notes' />";
							$temoin_bull_ou_cn_non_vide++;
						}

						if($temoin_bull_ou_cn_non_vide>0) {
							$current_nom_prenom_eleve=get_nom_prenom_eleve($current_login_ele);
							echo "<br /><span style='color:red'>Désinscription de l'élève ".$current_nom_prenom_eleve." impossible&nbsp;: $temoin</span>";
							$texte_mail.="Désinscription de ".$current_nom_prenom_eleve." impossible (carnet de notes ou bulletin non vide).\n";
							$reg_eleves[$num_periode][]=$current_login_ele;
							$reserves_sur_maj++;
						}

					}
				}

				$ts=time();
				//echo "<br />Temps écoulé ".($ts-$ts0)."<br />";

				// 20160420: On ne modifie pas les modalités:
				$code_modalite_elect_eleves=$current_group["modalites"];

				echo "<p class='bold' style='margin-top:1em;'>Mise à jour du groupe en période $num_periode&nbsp;: ";
				$update = update_group($_GET['id_groupe'], $reg_nom_groupe, $reg_nom_complet, $reg_matiere, $reg_clazz, $reg_professeurs, $reg_eleves, $code_modalite_elect_eleves);
				if($update) {
					echo "<span style='color:green'>SUCCES</span>";
					if($reserves_sur_maj>0) {echo "<span style='color:red'> avec les réserves mentionnées plus haut</span>.";}

					$texte_mail.="\nNouvelle composition du groupe en période $num_periode:\n";
					for($loop=0;$loop<count($reg_eleves[$num_periode]);$loop++) {
						$current_login_ele=$reg_eleves[$num_periode][$loop];
						$texte_mail.=get_nom_prenom_eleve($current_login_ele)."\n";
					}
					$texte_mail.="Effectif ".count($reg_eleves[$num_periode])."\n";


					$texte_mail="Bonjour(soir),\n\n".$texte_mail."\nCordialement.\n-- \n".$_SESSION['prenom']." ".$_SESSION['nom'];

					$envoi_mail_actif=getSettingValue('envoi_mail_actif');
					// 20160908
					if(getSettingANon("MajInscriptEleEdtEnvoiMail")) {
						$envoi_mail_actif="n";
					}
					if(($envoi_mail_actif!='n')&&($envoi_mail_actif!='y')) {
						$envoi_mail_actif='y'; // Passer à 'n' pour faire des tests hors ligne... la phase d'envoi de mail peut sinon ensabler.
					}
					if($envoi_mail_actif=='y') {

						$ajout_header="";
						if(($_SESSION['email']!="")&&(check_mail($_SESSION['email']))) {
							$ajout_header.="Cc: ".$_SESSION['prenom']." ".$_SESSION['nom']." <".$_SESSION['email'].">\r\n";
							$tab_param_mail['cc']=$_SESSION['email'];
							$tab_param_mail['cc_name']=$_SESSION['prenom']." ".$_SESSION['nom'];
						}

						$destinataire_mail="";
						$gepiAdminAdress=getSettingValue("gepiAdminAdress");
						if(($gepiAdminAdress!="")&&(check_mail($gepiAdminAdress))) {
							$destinataire_mail=$gepiAdminAdress;
							$tab_param_mail['destinataire'][]=$destinataire_mail;
						}
						for($loop=0;$loop<count($current_group['profs']['list']);$loop++) {
							$mail_user=get_mail_user($current_group['profs']['list'][$loop]);
							if(($mail_user!="")&&(check_mail($mail_user))) {
								if($destinataire_mail!="") {$destinataire_mail.=",";}
								$destinataire_mail.=$mail_user;
								$tab_param_mail['destinataire'][]=$mail_user;
							}
						}

						if($destinataire_mail!="") {
							$sujet_mail="[GEPI] Modification des affectations élèves dans un de vos enseignements";
							$envoi = envoi_mail($sujet_mail, $texte_mail, $destinataire_mail, $ajout_header, "plain", $tab_param_mail);
						}
					}

				} else {echo "<span style='color:red'>ECHEC</span>";}
				echo "</p>";

			}
		}

	}
	die();
}

$avec_js_et_css_edt="y";

$javascript_specifique[] = "lib/tablekit";
$utilisation_tablekit="ok";
//**************** EN-TETE *****************
$titre_page = "EDT : Inscriptions Eleves";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
//debug_var();

echo "<p class='bold'><a href='../classes/index.php'> <img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
/*
if($nb_reg_edt_corresp>0) {echo " | <a href='".$_SERVER['PHP_SELF']."?action=editer_corresp'>Editer les correspondances enregistrées</a> ";}
if($nb_reg_edt_lignes>0) {echo " | <a href='".$_SERVER['PHP_SELF']."?action=rapprochements'>Effectuer les rapprochements d'après le dernier XML envoyé</a>";}
if($nb_reg_edt_lignes>0) {echo " | <a href='".$_SERVER['PHP_SELF']."?action=remplir_edt_cours".add_token_in_url()."'>Remplir l'EDT d'après le dernier XML envoyé et d'après les rapprochements effectués</a>";}
*/
if($action!="") {echo " | <a href='".$_SERVER['PHP_SELF']."'> Autre import </a>";}

$sql="SELECT 1=1 FROM edt_eleves_lignes LIMIT 5;";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)>0) {
	echo " | <a href='".$_SERVER['PHP_SELF']."?action=comparer'> Effectuer les comparaisons d'après le dernier EDT_ELEVES.xml importé </a>";
}

$sql="SELECT 1=1 FROM edt_corresp2 LIMIT 5;";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)>0) {
	echo " | <a href='".$_SERVER['PHP_SELF']."?action=editer_ec2'> Contrôler les associations groupe Gepi/nom de regroupement EDT </a>";
}

echo " | <a href='../edt_organisation/import_edt_edt.php'> Importer l'emploi du temps </a>";
echo "</p>

<h2>Import des inscriptions élèves dans les enseignements depuis un XML d'EDT</h2>";

// On va uploader les fichiers XML dans le tempdir de l'utilisateur (administrateur, ou scolarité pour les màj Sconet)
$tempdir=get_user_temp_directory();
if(!$tempdir){
	echo "<p style='color:red'>Il semble que le dossier temporaire de l'utilisateur ".$_SESSION['login']." ne soit pas défini!?</p>\n";
	require("../lib/footer.inc.php");
	die();
}

if($action=="") {
	// 20160908 : Ajouter un formulaire pour activer/désactiver l'envoi de mail
	if(getSettingANon("MajInscriptEleEdtEnvoiMail")) {
		$checked_envoi_y="";
		$checked_envoi_n=" checked";
	}
	else {
		$checked_envoi_y=" checked";
		$checked_envoi_n="";
	}
	echo "
<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' id='form_param_mail' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<p>
			Lors de la modification des membres élèves d'un groupe, un mail peut être envoyé au professeur <em>(si son mail est renseigné)</em>.<br />
			<input type=\"radio\" name=\"MajInscriptEleEdtEnvoiMail\" id='MajInscriptEleEdtEnvoiMail_y' value='y'".$checked_envoi_y." onchange=\"checkbox_change('MajInscriptEleEdtEnvoiMail_y');checkbox_change('MajInscriptEleEdtEnvoiMail_n');\" /><label for='MajInscriptEleEdtEnvoiMail_y' id='texte_MajInscriptEleEdtEnvoiMail_y'> Envoyer un mail lors de la modification.</label><br />
			<input type=\"radio\" name=\"MajInscriptEleEdtEnvoiMail\" id='MajInscriptEleEdtEnvoiMail_n' value='n'".$checked_envoi_n." onchange=\"checkbox_change('MajInscriptEleEdtEnvoiMail_y');checkbox_change('MajInscriptEleEdtEnvoiMail_n');\" /><label for='MajInscriptEleEdtEnvoiMail_n' id='texte_MajInscriptEleEdtEnvoiMail_n'> Ne pas envoyer un mail lors de la modification.</label>
		</p>
		<p>
			<input type='submit' value='Valider' />
		</p>
	</fieldset>
</form>
<br />
<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' id='form_envoi_xml' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<p>
			Veuillez fournir l'export EXP_ELEVE.xml d'EDT&nbsp;:<br />
			<input type=\"file\" size=\"65\" name=\"xml_file\" id='input_xml_file' class='fieldset_opacite50' style='padding:5px; margin:5px;' /><br />";
	if ($gepiSettings['unzipped_max_filesize']>=0) {
		echo "<p style=\"font-size:small; color: red;\"><em>REMARQUE&nbsp;:</em> Vous pouvez fournir à Gepi le fichier compressé en ZIP.</p>";
	}
	echo "
			<input type='hidden' name='action' value='upload' /><br />
			<input type='radio' name='fichier_xml_complet' id='fichier_xml_complet_y' value='y' onchange=\"checkbox_change('fichier_xml_complet_y');checkbox_change('fichier_xml_complet_n');\" checked /><label for='fichier_xml_complet_y' id='texte_fichier_xml_complet_y' title=\"Dans ce cas, on commence par vider les lignes des groupes EDT des élèves sauvegardées, avant de re-remplir.\">Fichier XML complet</label><br />
			<input type='radio' name='fichier_xml_complet' id='fichier_xml_complet_n' value='n' onchange=\"checkbox_change('fichier_xml_complet_y');checkbox_change('fichier_xml_complet_n');\" /><label for='fichier_xml_complet_n' id='texte_fichier_xml_complet_n' title=\"Dans ce cas, on ne vide pas les lignes des groupes EDT des élèves sauvegardées.\nOn ne fait qu'ajouter les nouveaux élèves et mettre à jour les lignes correspondant aux élèves présents dans le XML fourni.\">Fichier XML partiel <em>(une classe, quelques classes ou quelques élèves)</em></label>
		</p>
		<p>
			<input type='submit' id='input_submit' value='Valider' />
			<input type='button' id='input_button' value='Valider' style='display:none;' onclick=\"check_champ_file()\" />
		</p>

		<p style='text-indent:-4em; margin-left:4em; margin-top:1em;'><em>NOTE&nbsp;:</em> Le fichier EXP_ELEVE.xml importé va être traité en identifiant les élèves par leur numéro INE (<em>N_NATIONAL dans le fichier XML et no_gep dans Gepi</em>).<br />Les groupes d'élèves EDT auxquels appartient chaque élève sont donnés dans la liste CLASSE où les groupes sont séparés par une virgule (<em>le premier groupe est généralement la classe de l'élève et les suivants désignent des groupes d'élèves</em>).<br />
		Lors de l'import du EXP_Cours.xml d'EDT, les associations groupe_Gepi/nom_de_groupe_EDT ont été enregistrées.<br />
		C'est ce qui va permettre de rechercher les différences.</p>
	</fieldset>

	<script type='text/javascript'>
		document.getElementById('input_submit').style.display='none';
		document.getElementById('input_button').style.display='';

		function check_champ_file() {
			fichier=document.getElementById('input_xml_file').value;
			//alert(fichier);
			if(fichier=='') {
				alert('Vous n\'avez pas sélectionné de fichier XML à envoyer.');
			}
			else {
				document.getElementById('form_envoi_xml').submit();
			}
		}
		".js_checkbox_change_style()."
		checkbox_change('fichier_xml_complet_y');
		checkbox_change('fichier_xml_complet_n');
		checkbox_change('MajInscriptEleEdtEnvoiMail_y');
		checkbox_change('MajInscriptEleEdtEnvoiMail_n');
	</script>
</form>";
	require("../lib/footer.inc.php");
	die();
}
elseif($action=="upload") {
	$fichier_xml_complet=isset($_POST['fichier_xml_complet']) ? $_POST['fichier_xml_complet'] : "y";

	check_token(false);
	$post_max_size=ini_get('post_max_size');
	$upload_max_filesize=ini_get('upload_max_filesize');
	$max_execution_time=ini_get('max_execution_time');
	$memory_limit=ini_get('memory_limit');

	$xml_file = isset($_FILES["xml_file"]) ? $_FILES["xml_file"] : NULL;

	if(!is_uploaded_file($xml_file['tmp_name'])) {
		echo "<p style='color:red;'>L'upload du fichier a échoué.</p>\n";

		echo "<p>Les variables du php.ini peuvent peut-être expliquer le problème:<br />\n";
		echo "post_max_size=$post_max_size<br />\n";
		echo "upload_max_filesize=$upload_max_filesize<br />\n";
		echo "</p>\n";

		require("../lib/footer.inc.php");
		die();
	}

	if(!file_exists($xml_file['tmp_name'])){
		echo "<p style='color:red;'>Le fichier aurait été uploadé... mais ne serait pas présent/conservé.</p>\n";

		echo "<p>Les variables du php.ini peuvent peut-être expliquer le problème:<br />\n";
		echo "post_max_size=$post_max_size<br />\n";
		echo "upload_max_filesize=$upload_max_filesize<br />\n";
		echo "et le volume de ".$xml_file['name']." serait<br />\n";
		echo "\$xml_file['size']=".volume_human($xml_file['size'])."<br />\n";
		echo "</p>\n";

		echo "<p>Il semblerait que l'absence d'extension .XML puisse aussi provoquer ce genre de symptômes.<br />Dans ce cas, ajoutez l'extension et ré-essayez.</p>\n";

		require("../lib/footer.inc.php");
		die();
	}

	echo "<p>Le fichier a été uploadé.</p>\n";

	$source_file=$xml_file['tmp_name'];
	$dest_file="../temp/".$tempdir."/edt_eleves.xml";
	$res_copy=copy("$source_file" , "$dest_file");

	$unzipped_max_filesize=getSettingValue('unzipped_max_filesize')*1024*1024;
	// $unzipped_max_filesize = 0    pas de limite de taille pour les fichiers extraits
	// $unzipped_max_filesize < 0    extraction zip désactivée
	if($unzipped_max_filesize>=0) {
		$fichier_emis=$xml_file['name'];
		$extension_fichier_emis=my_strtolower(mb_strrchr($fichier_emis,"."));
		if (($extension_fichier_emis==".zip")||($xml_file['type']=="application/zip"))
			{
			require_once('../lib/pclzip.lib.php');
			$archive = new PclZip($dest_file);

			if (($list_file_zip = $archive->listContent()) == 0) {
				echo "<p style='color:red;'>Erreur : ".$archive->errorInfo(true)."</p>\n";
				require("../lib/footer.inc.php");
				die();
			}

			if(sizeof($list_file_zip)!=1) {
				echo "<p style='color:red;'>Erreur : L'archive contient plus d'un fichier.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}

			//echo "<p>\$list_file_zip[0]['filename']=".$list_file_zip[0]['filename']."<br />\n";
			//echo "\$list_file_zip[0]['size']=".$list_file_zip[0]['size']."<br />\n";
			//echo "\$list_file_zip[0]['compressed_size']=".$list_file_zip[0]['compressed_size']."</p>\n";

			//echo "<p>\$unzipped_max_filesize=".$unzipped_max_filesize."</p>\n";

			if(($list_file_zip[0]['size']>$unzipped_max_filesize)&&($unzipped_max_filesize>0)) {
				echo "<p style='color:red;'>Erreur : La taille du fichier extrait (<em>".$list_file_zip[0]['size']." octets</em>) dépasse la limite paramétrée <em>(".lien_valeur_unzipped_max_filesize().")</em>.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}

			//unlink("$dest_file"); // Pour Wamp...
			$res_extract=$archive->extract(PCLZIP_OPT_PATH, "../temp/".$tempdir);
			if ($res_extract != 0) {
				echo "<p>Le fichier uploadé a été dézippé.</p>\n";
				$fichier_extrait=$res_extract[0]['filename'];
				unlink("$dest_file"); // Pour Wamp...
				$res_copy=rename("$fichier_extrait" , "$dest_file");
			}
			else {
				echo "<p style='color:red'>Echec de l'extraction de l'archive ZIP.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
		}
	}

	if(!$res_copy){
		echo "<p style='color:red;'>La copie du fichier vers le dossier temporaire a échoué.<br />Vérifiez que l'utilisateur ou le groupe apache ou www-data a accès au dossier temp/$tempdir</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	echo "<p>La copie du fichier vers le dossier temporaire a réussi.</p>\n";

	echo "<p>Ré-écriture d'un XML élagué.</p>\n";
	echo "<span style='font-size:x-small'>";

	$f=fopen($dest_file, "r");

	$dest_file2="../temp/".$tempdir."/edt_eleves_elague.xml";
	$f2=fopen($dest_file2, "w+");
	fwrite($f2, '﻿<?xml version="1.0" encoding="UTF-8" standalone="yes"?><TABLE nom="Elèves">'."\n");

	$cpt=0;
	$nb_ele=0;
	while(!feof($f)) {
		$ligne = ensure_utf8(fgets($f, 4096));

		//echo "$cpt : ".htmlentities($ligne)."<br />";

		if(trim($ligne)!="") {

			if((preg_match("/^<Eleves /i", $ligne))||
				(preg_match("/^<NOM>/i", $ligne))||
				(preg_match("/^<PRENOM>/i", $ligne))||
				(preg_match("/^<DATE_NAISS>/i", $ligne))||
				(preg_match("/^<N_NATIONAL>/i", $ligne))||
				(preg_match("/^<CLASSE>/i", $ligne))||
				(preg_match("/^<GROUPES>/i", $ligne))||
				(preg_match("|^</Eleves>|i", $ligne))||
				(preg_match("|^</TABLE>|i", $ligne))) {
				fwrite($f2, $ligne);
				echo ". ";
				flush();

				if(preg_match("/^<NOM>/i", $ligne)) {
					$nb_ele++;
				}
			}
		}
		$cpt++;
	}
	echo "</span>";
	fclose($f);
	fclose($f2);
	$dest_file=$dest_file2;

	echo "<p style='margin-top:1em;'><strong style='color:red'>$nb_ele</strong> élève(s) trouvés dans ce fichier.</p>
	<p style='margin-top:1em;margin-left:7em;text-indent:-7em;'><strong>ATTENTION&nbsp;:</strong> Si ce n'est pas l'effectif de l'établissement, vous avez peut-être oublié de sélectionner des classes pour réaliser l'export EXP_ELEVE.xml.<br />
	Si une classe n'est pas sélectionnée, les enseignements/groupes multi-classe qui font intervenir cette classe pourraient être amputés de membres de cette classe.<br />
	<em>Exemple&nbsp;:</em> Si vous avez un groupe AGL1_4B_4C faisant intervenir des élèves de 4B et 4C, mais que seule la 4B a été sélectionnée, il vous sera proposé par la suite de désincrire du groupe les élèves de 4C puisque non trouvés dans le fichier XML parmi les membres du regroupement EDT associé à AGL1_4B_4C.</p>";

	$eleves_xml=simplexml_load_file($dest_file);
	if(!$eleves_xml) {
		echo "<p style='color:red;'>ECHEC du chargement du fichier avec simpleXML.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	/*
	<TABLE nom="Elèves">
	...
	<Eleves numero="2">
	<NUMERO>2</NUMERO>
	<CIVILITE/>
	<NOM>XXXXXXXX</NOM>
	<PRENOM>XXXXXXX</PRENOM>
	<DATE_NAISS>XX/XX/XXXX</DATE_NAISS>
	<LIEU_NAISS>BERNAY</LIEU_NAISS>
	<PAYS_NAISS>FRANCE</PAYS_NAISS>
	<NATIONALITE>FRANCE</NATIONALITE>
	<SEXE>F</SEXE>
	<N_NATIONAL>XXXXXXXX</N_NATIONAL>
	...
	<CLASSE>3D, &lt;3D&gt; &lt;Dédoublement&gt; 3D GR2, &lt;3D&gt; &lt;LATIN&gt; LAT, &lt;3D&gt; &lt;BILANGUE&gt; BIL, &lt;3D&gt; &lt;DP3&gt; NON-DP3</CLASSE>
	<GROUPES>3ALL1GR.1, 3D GR2, 3LATINGR.1, GR_3C3D_BIL</GROUPES>
	<AP_CLASSE>4 D</AP_CLASSE>
	<REDOUBLANT>N</REDOUBLANT>
	<AP_REDOUBLANT/>
	<FORMATION>3EME BILANGUES</FORMATION>
	<AP_FORMATION>4EME BILANGUES</AP_FORMATION>
	<REGIME>DEMI-PENSIONNAIRE DANS L'ETABLISSEMENT</REGIME>
	<AP_REGIME/>
	<OPTION_1>ANGLAIS LV1</OPTION_1>
	<OPTION_2>ALLEMAND LV2</OPTION_2>
	<OPTION_3>LATIN</OPTION_3>
	<OPTION_4/>
	<OPTION_5/>
	*/

	$tab_champs=array("NUMERO",
				"NOM",
				"PRENOM",
				"DATE_NAISS",
				"SEXE",
				"N_NATIONAL",
				"CLASSE",
				"GROUPES",
				"OPTION_1",
				"OPTION_2",
				"OPTION_3",
				"OPTION_4",
				"OPTION_5",
				"OPTION_6",
				"OPTION_7",
				"OPTION_8",
				"OPTION_9",
				"OPTION_10",
				"OPTION_11",
				"OPTION_12");

	for($loop=0;$loop<count($tab_champs);$loop++) {
		$tab_champs2[$tab_champs[$loop]]=casse_mot($tab_champs[$loop], "min");
	}

	if($fichier_xml_complet=="y") {
		$sql="TRUNCATE edt_eleves_lignes;";
		$menage=mysqli_query($GLOBALS["mysqli"], $sql);
	}

	echo "<p class='bold' style='margin-top:1em;'>Parcours du XML.</p>\n";
	echo "<span style='font-size:x-small'>";

	$cpt=0;
	$tab_ele=array();
	//$tab_classes_trouvees=array();
	foreach ($eleves_xml->children() as $key => $cur_eleve) {
		if($key=='Eleves') {
			/*
			echo "<p>$key</p>";
			echo "<pre>";
			print_r($cur_eleve);
			echo "</pre>";
			*/
			foreach ($cur_eleve->children() as $key2 => $value2) {
				if(in_array($key2, $tab_champs)) {
					$champ_courant=$tab_champs2[$key2];
					$tab_ele[$cpt]["$champ_courant"]=trim($value2);
					//echo "$key2:$value2<br />";
				}
			}
			/*
			echo "<p>\$tab_ele[$cpt]</p>";
			echo "<pre>";
			print_r($tab_ele[$cpt]);
			echo "</pre>";
			*/

			if($fichier_xml_complet=="n") {
				if(isset($tab_ele[$cpt]["n_national"])) {
					$sql="DELETE FROM edt_eleves_lignes WHERE n_national='".$tab_ele[$cpt]["n_national"]."';";
					//echo "$sql<br />";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$del) {
						echo "<span style='color:red'>$sql</span><br />";
					}
				}
			}

			// Enregistrer la ligne dans edt_lignes
			$sql="INSERT INTO edt_eleves_lignes SET ";
			$sql_ajout="";
			for($loop=0;$loop<count($tab_champs);$loop++) {
				$champ_courant=$tab_champs2[$tab_champs[$loop]];
				//echo "Test champ ".$tab_champs[$loop]."<br />";
				if(isset($tab_ele[$cpt][$champ_courant])) {
					if($sql_ajout!="") {$sql_ajout.=",";}
					$sql_ajout.=" ".$champ_courant."='".mysqli_real_escape_string($GLOBALS["mysqli"], $tab_ele[$cpt][$champ_courant])."'";
				}
			}
			//echo "\$sql_ajout=$sql_ajout<br />";

			if($sql_ajout!="") {
				$sql.=$sql_ajout.";";
				//echo "$sql<br />";
				$insert=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$insert) {
					echo "<span style='color:red'>$sql</span><br />";
				}
				else {
					//echo "<span style='color:green'>$sql</span><br />";
					//$tab_ele[$cpt]["id"]=mysqli_insert_id($GLOBALS["mysqli"], $insert);
					$tab_ele[$cpt]["id"]=mysqli_insert_id($GLOBALS["mysqli"]);
				}
			}
			echo ". ";
			flush();

			$cpt++;
		}
	}
	echo "</span>";

	echo "<p>$cpt élève(s) trouvés dans ce fichier.</p>";

	echo creer_div_infobulle("div_search","Formulaire de recherche dans la table 'eleves'","","<p>Saisir une portion du nom à rechercher...</p>
<form name='recherche' action='".$_SERVER['PHP_SELF']."' method='post'>
<input type='hidden' name='ine_recherche' id='ine_recherche' value='' />
<table border='0' summary='Recherche'>
	<tr>
		<th>Nom: </th>
		<td><input type='text' name='nom_ele' id='nom_ele' value='' onBlur='get_eleves(this.form)' /></td>
		<td rowspan='2'><input type='button' name='chercher' value='Chercher' onClick='get_eleves(this.form)' /></td>
	</tr>
	<tr>
		<th>Prénom: </th>
		<td><input type='text' name='prenom_ele' id='prenom_ele' value='' onBlur='get_eleves(this.form)' /></td>
	</tr>
</table>
</form>

<div id='div_resultat' style='margin: 1px;'></div>

","",32,0,"y","y","n","n");

	$cpt=0;
	$chaine_ine_pb="";
	//$tab_ine_pb=array();
	$tab_ine_pb_id=array();

	//$sql="SELECT DISTINCT id, nom, prenom, n_national, date_naiss, classe FROM edt_eleves_lignes WHERE n_national NOT IN (SELECT no_gep FROM eleves);";
	$sql="SELECT DISTINCT id, nom, prenom, n_national, date_naiss, classe FROM edt_eleves_lignes WHERE classe!='' AND n_national NOT IN (SELECT no_gep FROM eleves);";
	$res_ine_non_trouve=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_ine_non_trouve)>0) {

		$chaine_ine_pb.="<p>Voici la liste des élèves concernés&nbsp;:</p>
		<table class='boireaus boireaus_alt'>
			<tr>
				<th>Nom</th>
				<th>Prénom</th>
				<th>Naissance</th>
				<th>INE (EDT)</th>
				<th>Classe</th>
				<th>INE corrigé</th>
				<th>Chercher</th>
			</tr>";
		$cpt=0;
		while($lig=mysqli_fetch_object($res_ine_non_trouve)) {
			$chaine_ine_pb.="
			<tr>
				<td>
					$lig->nom
					<input type='hidden' name='nom_eleve[$cpt]' id='nom_eleve_$cpt' value=\"".$lig->nom."\" />
					<input type='hidden' name='edt_eleves_lignes_id[$cpt]' id='edt_eleves_lignes_id_$cpt' value=\"".$lig->id."\" />
				</td>
				<td>
					$lig->prenom
					<input type='hidden' name='prenom_eleve[$cpt]' id='prenom_eleve_$cpt' value=\"".$lig->prenom."\" />
				</td>
				<td>
					$lig->date_naiss
				</td>
				<td>
					$lig->n_national
				</td>
				<td>
					$lig->classe
				</td>
				<td>
					<input type='text' name='ine_corrige[$cpt]' id='ine_corrige_$cpt' value='' onchange=\"changement()\" />
				</td>
				<td>
					<a href='#' onClick=\"prepare_form_recherche_ele_ine($cpt);return false;\">
						<img src='../images/icons/chercher.png' width='16' height='16' alt='Chercher' />
					</a>
				</td>
			</tr>";
			/*
			if($lig->n_national!="") {
				$tab_ine_pb[]=$lig->n_national;
			}
			*/
			$tab_ine_pb_id[]=$lig->id;
			$cpt++;
		}
		$chaine_ine_pb.="
		</table>";
	}

	$chaine_ine_pb_complement="";
	// Cas GALLIER, GALLIER-GENTES inscrite deux fois dans Sconet et le principal adjoint a conservé dans sa base EDT la mauvaise élève (celle qui dans Sconet a une date de sortie renseignée)
	$sql="SELECT DISTINCT id, nom, prenom, n_national, date_naiss, classe FROM edt_eleves_lignes WHERE classe!='' AND n_national NOT IN (SELECT no_gep FROM eleves e, j_eleves_classes jec WHERE e.login=jec.login);";
	$res_ine_non_trouve=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_ine_non_trouve)>0) {
		while($lig=mysqli_fetch_object($res_ine_non_trouve)) {
			//if(!in_array($lig->n_national, $tab_ine_pb)) {
			if(!in_array($lig->id, $tab_ine_pb_id)) {
				$chaine_ine_pb_complement.="
			<tr>
				<td>
					$lig->nom
					<input type='hidden' name='nom_eleve[$cpt]' id='nom_eleve_$cpt' value=\"".$lig->nom."\" />
					<input type='hidden' name='edt_eleves_lignes_id[$cpt]' id='edt_eleves_lignes_id_$cpt' value=\"".$lig->id."\" />
				</td>
				<td>
					$lig->prenom
					<input type='hidden' name='prenom_eleve[$cpt]' id='prenom_eleve_$cpt' value=\"".$lig->prenom."\" />
				</td>
				<td>
					$lig->date_naiss
				</td>
				<td>
					$lig->n_national
				</td>
				<td>
					".preg_replace("/, /", ",<br />", htmlentities($lig->classe))."
				</td>
				<td>
					<input type='text' name='ine_corrige[$cpt]' id='ine_corrige_$cpt' value='' onchange=\"changement()\" />
				</td>
				<td>
					<a href='#' onClick=\"prepare_form_recherche_ele_ine($cpt);return false;\">
						<img src='../images/icons/chercher.png' width='16' height='16' alt='Chercher' />
					</a>
				</td>
			</tr>";
				/*
				if($lig->n_national!="") {
					$tab_ine_pb[]=$lig->n_national;
				}
				*/
				$cpt++;
			}
		}

		if($chaine_ine_pb_complement!="") {
			$chaine_ine_pb.="<p>Un ou des élèves du fichiers EDT correspondent à des élèves qui ne sont pas/plus dans des classes dans Gepi.<br />Certains rapprochements sont peut-être nécessaires.<br />Sinon, il faut (re)mettre l'élève dans la classe dans Gepi.</p>
		<p>Voici la liste des élèves concernés&nbsp;:</p>
		<table class='boireaus boireaus_alt'>
			<tr>
				<th>Nom</th>
				<th>Prénom</th>
				<th>Naissance</th>
				<th>INE (EDT)</th>
				<th>Classe</th>
				<th>INE corrigé</th>
				<th>Chercher</th>
			</tr>".$chaine_ine_pb_complement."
		</table>";
		}
	}

	if($cpt>0) {
		echo "
<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' id='form_correction_ine' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='action' value='corrige_ine_eleve' />

		<p style='color:red'>Un ou des INE du XML importé n'ont pas été trouvés dans la table 'eleves'.<br />
		Cela peut arriver dans plusieurs cas&nbsp;:</p>
		<ul>
			<li>Vous avez mis à jour la liste des élèves dans EDT sans effectuer le même ajout d'élève dans Gepi (<em>ou sans prendre en compte un export XML de Sconet pour procéder à une mise à jour non manuelle</em>).</li>
			<li>La liste des élèves dans EDT n'est pas à jour (<em>INE non renseigné dans EDT, ou mauvais INE (l'INE peut changer dans les premiers jours de l'arrivée d'un élève dans l'établissement par exemple dans le cas où il vient d'une autre académie)</em>).</li>
		</ul>";

		echo $chaine_ine_pb;

		echo "<p><input type='submit' value=\"Prendre en compte les corrections pour les INE non vides\" /></p>
		<p>Il est recommandé de faire le rapprochement avant rechercher les modifications d'appartenance aux groupes/enseignements.</p>
	</fieldset>
</form>

<script type='text/javascript'>
	function prepare_form_recherche_ele_ine(cpt) {
		document.getElementById('nom_ele').value=document.getElementById('nom_eleve_'+cpt).value;
		document.getElementById('prenom_ele').value=document.getElementById('prenom_eleve_'+cpt).value;
		document.getElementById('ine_recherche').value='ine_corrige_'+cpt;

		// On fait le nettoyage pour ne pas laisser les traces d'une précédente requête:
		document.getElementById('div_resultat').innerHTML='';
		afficher_div('div_search','y',-500,0);
	}

	function get_eleves(f) {
		nom_ele=document.getElementById('nom_ele').value;
		prenom_ele=document.getElementById('prenom_ele').value;
		new Ajax.Updater($('div_resultat'),'".$_SERVER['PHP_SELF']."',{method: 'post',
		parameters: {
			nom_ele: nom_ele,
			prenom_ele: prenom_ele,
			mode: 'cherche_eleve'
		}});
	}
	function corrige_ine(ine) {
		document.getElementById(document.getElementById('ine_recherche').value).value=ine;
		cacher_div('div_search');
	}
</script>";
	}

	if(count($tab_ele)>0) {
		if($fichier_xml_complet=="n") {

			/*
			echo "<pre>";
			print_r($tab_ele);
			echo "</pre>";
			*/

			echo "
<p style='margin-top:1em;'><span class='bold'>Pour quel élève souhaitez-vous corriger les appartenances à tels groupes d'après les groupes/regroupements EDT&nbsp;?</span><br />Cliquez sur le nom de la classe correspondante.</p>
<table class='boireaus boireaus_alt'>
	<thead>
		<tr>
			<th>INE</th>
			<th>Élève</th>
			<th>Classes</th>
			<th colspan=''>Regroupements EDT</th>
		</tr>
	</thead>
	<tbody>";

			for($loop=0;$loop<count($tab_ele);$loop++) {
				if($tab_ele[$loop]["n_national"]=="") {
					echo "
		<tr>
			<td style='color:red'>INE vide</td>
			<td>".$tab_ele[$loop]["nom"]." ".$tab_ele[$loop]["prenom"]."</td>
			<td style='color:red'>Élève non identifié dans la base</td>
			<td>".htmlentities($tab_ele[$loop]["classe"])."</td>
		</tr>";
				}
				else {
					$sql="SELECT * FROM eleves where no_gep='".$tab_ele[$loop]["n_national"]."';";
					$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_ele)==0) {
						echo "
		<tr>
			<td>".$tab_ele[$loop]["n_national"]."</td>
			<td>".$tab_ele[$loop]["nom"]." ".$tab_ele[$loop]["prenom"]."</td>
			<td style='color:red'>Élève non identifié dans la base</td>
			<td>".htmlentities($tab_ele[$loop]["classe"])."</td>
		</tr>";
					}
					else {
						$lig_ele=mysqli_fetch_object($res_ele);

						echo "
		<tr>
			<td>".$tab_ele[$loop]["n_national"]."</td>
			<td>".$tab_ele[$loop]["nom"]." ".$tab_ele[$loop]["prenom"]."</td>
			<td>";
						$tmp_tab_clas=get_class_periode_from_ele_login($lig_ele->login);
						if(!isset($tmp_tab_clas['classe'])) {
							echo "<img src='../images/disabled.png' class='icone16' alt='Aucune' title='Aucune' />";
						}
						else {
							foreach($tmp_tab_clas['classe'] as $current_id_classe => $current_classe) {
								echo "<a href='../classes/eleve_options.php?id_classe=".$current_id_classe."&login_eleve=".$lig_ele->login."&grp_edt=y&quitter_la_page=y' target='_blank' title=\"Modifier les inscriptions pour cet élève.\">".$current_classe['classe']."</a> ";
							}
						}
						echo "</td>
			<td>".htmlentities($tab_ele[$loop]["classe"])."</td>
		</tr>";

					}
				}
			}
			echo "
</table>
<p>Vous pouvez aussi opter pour une comparaison globale, mais il est préférable de procéder à ce genre de comparaison avec un export XML complet.</p>";
		}
	}
	echo "<p><a href='".$_SERVER['PHP_SELF']."?action=comparer'>Rechercher les modifications d'appartenance aux groupes/enseignements</a>.</p>";


	echo "<!--p style='color:red'><em>A FAIRE&nbsp;:</em> Repérer les élèves avec N_NATIONAL vide... qu'il va falloir rapprocher.<br />
	Rechercher les élèves non matchés entre 'edt_eleves_lignes.n_national' et 'eleves.no_gep'.</p-->";

	echo "<p>&nbsp;<br />&nbsp;</p>";

	require("../lib/footer.inc.php");
	die();
}
elseif($action=="comparer") {

	echo "<!--p style='color:red'><em>A FAIRE&nbsp;:</em> Commencer par Repérer les élèves avec N_NATIONAL vide... qu'il va falloir rapprocher.<br />
	Rechercher les élèves non matchés entre 'edt_eleves_lignes.n_national' et 'eleves.no_gep'.</p-->";

	if(!isset($num_periode)) {
		echo "<h2>Choix de la période</h2>";

		echo affiche_tableau_periodes_et_date_fin_classes();

		$sql="SELECT DISTINCT num_periode FROM periodes p, classes c WHERE c.id=p.id_classe ORDER BY num_periode;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			echo "<p style='color:red'>Aucune période n'est encore définie.</p>";
		}
		else {
			echo "<p style='margin-top:1em;margin-left:3em;text-indent:-3em;'>Rechercher les différences d'affectation des élèves sur&nbsp;:<br />";
			while($lig=mysqli_fetch_object($res)) {
				echo "la <a href='".$_SERVER['PHP_SELF']."?action=comparer&amp;num_periode=".$lig->num_periode."'>période ".$lig->num_periode."</a><br />";
			}
			echo "</p>";
		}
		require("../lib/footer.inc.php");
		die();
	}

	echo "<h2>Différences EDT/Gepi sur la période $num_periode</h2>";

	if((getSettingAOui('autorise_edt_tous'))||
		((getSettingAOui('autorise_edt_admin'))&&($_SESSION['statut']=='administrateur'))) {

		$titre_infobulle="EDT de <span id='id_ligne_titre_infobulle_edt'></span>";
		$texte_infobulle="";
		$tabdiv_infobulle[]=creer_div_infobulle('edt_prof',$titre_infobulle,"",$texte_infobulle,"",40,0,'y','y','n','n');

//https://127.0.0.1/steph/gepi_git_trunk/edt_organisation/index_edt.php?login_edt=boireaus&type_edt_2=prof&no_entete=y&no_menu=y&lien_refermer=y

		function affiche_lien_edt_prof($login_prof, $info_prof) {
			return " <a href='../edt_organisation/index_edt.php?login_edt=".$login_prof."&amp;type_edt_2=prof&amp;no_entete=y&amp;no_menu=y&amp;lien_refermer=y' onclick=\"affiche_edt_prof_en_infobulle('$login_prof', '".addslashes($info_prof)."');return false;\" title=\"Emploi du temps de ".$info_prof."\" target='_blank'><img src='../images/icons/edt.png' class='icone16' alt='EDT' /></a>";
		}

		$titre_infobulle="EDT de la classe de <span id='span_id_nom_classe'></span>";
		$texte_infobulle="";
		$tabdiv_infobulle[]=creer_div_infobulle('edt_classe',$titre_infobulle,"",$texte_infobulle,"",40,0,'y','y','n','n');

		echo "
<style type='text/css'>
	.lecorps {
		margin-left:0px;
	}
</style>

<script type='text/javascript'>
	function affiche_edt_classe_en_infobulle(id_classe, classe) {
		document.getElementById('span_id_nom_classe').innerHTML=classe;

		new Ajax.Updater($('edt_classe_contenu_corps'),'../edt_organisation/index_edt.php?login_edt='+id_classe+'&type_edt_2=classe&visioedt=classe1&no_entete=y&no_menu=y&mode_infobulle=y',{method: 'get'});
		afficher_div('edt_classe','y',-20,20);
	}

	function affiche_edt_prof_en_infobulle(login_prof, info_prof) {
		document.getElementById('id_ligne_titre_infobulle_edt').innerHTML=info_prof;

		new Ajax.Updater($('edt_prof_contenu_corps'),'../edt_organisation/index_edt.php?login_edt='+login_prof+'&type_edt_2=prof&no_entete=y&no_menu=y&mode_infobulle=y',{method: 'get'});
		afficher_div('edt_prof','y',-20,20);
	}
</script>\n";
	}
	else {
		function affiche_lien_edt_prof($login_prof, $info_prof) {
			return "";
		}
	}

	//$sql="SELECT DISTINCT nom_edt FROM edt_corresp WHERE champ='groupe';";
	$sql="SELECT DISTINCT nom_groupe_edt FROM edt_corresp2 ORDER BY nom_groupe_edt;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p>Aucun enregistrement n'a été trouvé dans 'edt_corresp2'.</p>";
		require("../lib/footer.inc.php");
		die();
	}

	$tab_corresp_classe=array();
	$sql="select * from edt_corresp where champ='classe';";
	$res_corresp_classe=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_corresp_classe)>0) {
		while($lig_corresp_classe=mysqli_fetch_object($res_corresp_classe)) {
			$tab_corresp_classe[]=$lig_corresp_classe->nom_edt;
		}
	}

	// 20160504
	$tab_eleves_sortis=array();
	$sql="SELECT DISTINCT login FROM eleves WHERE date_sortie IS NOT NULL AND date_sortie NOT LIKE '0000-00-00%' AND date_sortie<='".strftime("%Y-%m-%d %H:%M:%S")."';";
	//echo "$sql<br />";
	$res_sortis=mysqli_query($GLOBALS["mysqli"], $sql);
	while($lig_sorti=mysqli_fetch_object($res_sortis)) {
		$tab_eleves_sortis[]=$lig_sorti->login;
	}
	/*
	echo "\$tab_eleves_sortis<pre>";
	print_r($tab_eleves_sortis);
	echo "</pre>";
	*/

	$sql="TRUNCATE edt_tempo;";
	$menage=mysqli_query($GLOBALS["mysqli"], $sql);

	echo "
<p id='p_affichage_masquage_div_regroupements' style='margin-top:1em; margin-bottom:1em; display:none;'><a href=\"javascript:afficher_tous_div_regroup('')\">Afficher tous les regroupements</a><br />
<a href=\"javascript:afficher_tous_div_regroup('none')\">N'afficher que les regroupements avec différence trouvée</a></p>";

	$tab_nom_classe=array();
	$cpt_regroupement=0;
	$chaine_js_div="";
	while($lig=mysqli_fetch_object($res)) {
		$sql="INSERT INTO edt_tempo set col1='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->nom_groupe_edt)."';";
		if($debug_import_edt=="y") {
			echo "$sql<br />";
		}
		$insert=mysqli_query($GLOBALS["mysqli"], $sql);
		$current_id_temp=mysqli_insert_id($GLOBALS["mysqli"]);

		$temoin_differences=0;
		echo "<div id='div_regroupement_$cpt_regroupement'>";
		echo "<a name='regroupement_$current_id_temp'></a>";
		echo "<p class='bold'>Regroupement d'élèves ".$lig->nom_groupe_edt."</p>";
		$sql="SELECT DISTINCT id_groupe FROM edt_corresp2 WHERE nom_groupe_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->nom_groupe_edt)."';";
		if($debug_import_edt=="y") {
			echo "$sql<br />";
		}
		$res_grp=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_grp)==0) {
			echo "<p>Aucun groupe n'est associé au nom EDT ".$lig->nom_groupe_edt.".</p>";
		}
		else {
			$tab_info_grp=array();
			$tab_ele_grp=array();
			$tab_prof_grp=array();

			echo "<p style='margin-left:3em;text-indent:-3em;'>Le ou les groupes suivants sont associés à ce nom de regroupement d'élèves EDT&nbsp;:<br />";
			while($lig_grp=mysqli_fetch_object($res_grp)) {
				$tab_info_grp[$lig_grp->id_groupe]=get_info_grp($lig_grp->id_groupe);
				echo $tab_info_grp[$lig_grp->id_groupe]."<br />";
				$tmp_tab=get_eleves_from_groupe($lig_grp->id_groupe, $num_periode);
				if(isset($tab_ele_grp[$lig_grp->id_groupe]['list'])) {
					$tab_ele_grp[$lig_grp->id_groupe]['list']=$tmp_tab['list'];
					$tab_ele_grp[$lig_grp->id_groupe]['users']=$tmp_tab['users'];
				}
				$tab_prof_grp[$lig_grp->id_groupe]=get_profs_for_group($lig_grp->id_groupe);
			}
			echo "</p>";

			$nom_groupe_edt=$lig->nom_groupe_edt;
			$nom_groupe_edt_clean=preg_replace("/\[/", "", preg_replace("/\]/", "", $lig->nom_groupe_edt));

			//$chaine_nom_groupe="";
			/*
			$current_nom_groupe=preg_replace("/\[/", "", preg_replace("/\]/", "", $lig->nom_groupe_edt));

			$sql="SELECT nom,prenom,date_naiss,sexe,n_national,groupes FROM edt_eleves_lignes 
				WHERE (groupes like '$current_nom_groupe' OR 
					groupes like '$current_nom_groupe, %' OR 
					groupes like '%, $current_nom_groupe, %' OR 
					groupes like '%, $current_nom_groupe');";
			*/

			//$sql="";
			$sql="SELECT id,nom,prenom,date_naiss,sexe,n_national,groupes FROM edt_eleves_lignes 
				WHERE (";
			$tmp_tab_nom_groupe=explode(",", $lig->nom_groupe_edt);
			$loop2=0;
			for($loop=0;$loop<count($tmp_tab_nom_groupe);$loop++) {
				$current_nom_groupe=trim(preg_replace("/\[/", "", preg_replace("/\]/", "", $tmp_tab_nom_groupe[$loop])));
				if(!in_array($current_nom_groupe, $tab_corresp_classe)) {
					if($loop2>0) {
						//$chaine_nom_groupe.=", ";
						//$sql.=" UNION ";
						$sql.=" OR ";
					}
					/*
					$sql="(SELECT nom,prenom,date_naiss,sexe,n_national,groupes FROM edt_eleves_lignes 
						WHERE (groupes like '$current_nom_groupe' OR 
							groupes like '$current_nom_groupe, %' OR 
							groupes like '%, $current_nom_groupe, %' OR 
							groupes like '%, $current_nom_groupe'))";
					*/
					/*
					$sql.="groupes like '$current_nom_groupe' OR 
						groupes like '$current_nom_groupe, %' OR 
						groupes like '%, $current_nom_groupe, %' OR 
						groupes like '%, $current_nom_groupe'";
					*/
					$sql.="classe like '$current_nom_groupe' OR 
						classe like '$current_nom_groupe, %' OR 
						classe like '%, $current_nom_groupe, %' OR 
						classe like '%, $current_nom_groupe'";
					$sql.=" OR groupes like '$current_nom_groupe' OR 
						groupes like '$current_nom_groupe, %' OR 
						groupes like '%, $current_nom_groupe, %' OR 
						groupes like '%, $current_nom_groupe'";
					$loop2++;
				}
			}
			$sql.=");";
			if($debug_import_edt=="y") {
				echo "$sql<br />";
			}
			if($loop2>0) {
				$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_ele)==0) {
					//echo "<p>Aucun élève n'a dans ses groupes le nom EDT ".$current_nom_groupe.".</p>";
					//echo "<p>Aucun élève n'a dans ses groupes le nom EDT ".$chaine_nom_groupe.".</p>";
					echo "<p>Aucun élève n'a dans ses groupes le nom EDT ".$nom_groupe_edt.".</p>";
				}
				else {
					$tab_ele_regroupement_edt=array();
					echo "<p style='margin-left:3em;text-indent:-3em;'>Le ou les élèves suivants ont dans leurs groupes le nom EDT ".$current_nom_groupe."&nbsp;:<br />";
					$cpt_ele=0;
					while($lig_ele=mysqli_fetch_object($res_ele)) {
						echo $lig_ele->nom." ".$lig_ele->prenom." (".$lig_ele->date_naiss.") (".$lig_ele->n_national.")";

	// Si $lig_ele->n_national est vide, il faut tenter d'identifier autrement l'élève (nom, prénom, date de naissance).

						if($lig_ele->n_national!="") {
							$sql="SELECT login, date_sortie FROM eleves WHERE no_gep='".$lig_ele->n_national."';";
						}
						else {
							$sql="SELECT login, date_sortie FROM eleves WHERE nom='".$lig_ele->nom."' AND prenom='".$lig_ele->prenom."';";
						}
						//echo "$sql<br />";
						$res_nn=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res_nn)==0) {
							echo " <span style='color:red'>INE non trouvé dans la table 'eleves'</span>";
						}
						elseif(mysqli_num_rows($res_nn)==1) {
							$lig_nn=mysqli_fetch_object($res_nn);

							$tab_ele_regroupement_edt['id_edt_eleves_lignes'][]=$lig_ele->id;
							$tab_ele_regroupement_edt['login'][]=$lig_nn->login;
							$tab_ele_regroupement_edt['date_sortie'][]=$lig_nn->date_sortie;
							$tab_ele_regroupement_edt['nom'][]=$lig_ele->nom;
							$tab_ele_regroupement_edt['prenom'][]=$lig_ele->prenom;
							$tab_ele_regroupement_edt['date_naiss'][]=$lig_ele->date_naiss;
							$tab_ele_regroupement_edt['n_national'][]=$lig_ele->n_national;

							/*
							$classes="";
							$tmp_tab=get_class_from_ele_login($lig_nn->login);
							if(isset($tmp_tab['liste_nbsp'])) {$classes=$tmp_tab['liste_nbsp'];}
							*/
							$id_classe="";
							$classe="";
							$tmp_tab=get_class_periode_from_ele_login($lig_nn->login);
							if(isset($tmp_tab['periode'][$num_periode]['id_classe'])) {$id_classe=$tmp_tab['periode'][$num_periode]['id_classe'];}
							$tab_ele_regroupement_edt['id_classe'][]=$id_classe;
							if(isset($tmp_tab['periode'][$num_periode]['classe'])) {$classe=$tmp_tab['periode'][$num_periode]['classe'];}
							$tab_ele_regroupement_edt['classe'][]=$classe;
						}
						else {
							echo " <span style='color:red'>Plusieurs élèves trouvés dans la table 'eleves' pour cet INE (???)</span>";
						}
						echo "<br />";

						$cpt_ele++;
					}
					echo "</p>";
					echo "<p class='bold'>Effectif : $cpt_ele</p>";

					foreach($tab_ele_grp as $current_id_groupe => $current_tab_ele) {
						$tab_test_association_grp_classe=array();

						$diff=array_diff($current_tab_ele['list'], $tab_ele_regroupement_edt['login']);
						$diff2=array_diff($tab_ele_regroupement_edt['login'], $current_tab_ele['list']);

						$au_moins_une_vraie_diff=0;
						foreach($diff as $current_diff) {
							if(!in_array($current_diff, $tab_eleves_sortis)) {
								//echo "$current_diff<br />";
								$au_moins_une_vraie_diff++;
								break;
							}
						}
						foreach($diff2 as $current_diff) {
							if(!in_array($current_diff, $tab_eleves_sortis)) {
								//echo "$current_diff<br />";
								$au_moins_une_vraie_diff++;
								break;
							}
						}

						//echo "\$au_moins_une_vraie_diff=$au_moins_une_vraie_diff<br />";

						if(((count($diff)==0)&&(count($diff2)==0))||($au_moins_une_vraie_diff==0)) {
							echo "<p style='color:blue; margin-top:1em;'>Le groupe ".$tab_info_grp[$current_id_groupe]." est à jour.</p>";
						}
						else {

							/*
							echo "diff<pre>";
							print_r($diff);
							echo "</pre>";

							echo "diff2<pre>";
							print_r($diff2);
							echo "</pre>";
							*/
							$temoin_bull_ou_cn_non_vide=0;

							$temoin_differences++;
							echo "<p style='color:red; margin-top:1em;'>Différences pour <a href='../groupes/edit_group.php?id_groupe=$current_id_groupe' target='_blank' title=\"Voir le groupe dans un nouvel onglet.\">".$tab_info_grp[$current_id_groupe]."</a> <a href='../groupes/edit_eleves.php?id_groupe=$current_id_groupe' title=\"Voir/modifier les élèves inscrits dans ce groupe.\" target='_blank'><img src='../images/group16.png' class='icone16' alt='Élèves' /></a></p>";
							/*
							echo "<pre>";
							print_r($diff);
							echo "
							</pre>";
							echo "<pre>";
							print_r($diff2);
							echo "
							</pre>";
							*/

							//==================================
							$details_lignes="";
							$sql="SELECT * FROM edt_lignes WHERE classe='".mysqli_real_escape_string($GLOBALS["mysqli"], $nom_groupe_edt)."' ORDER BY mat_code, prof_nom, prof_prenom, jour, h_debut;";
							//$details_lignes.="<br />$sql<br />";
							$res_edt_lig=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res_edt_lig)>0) {
								$details_lignes.="<table id='table_lignes_edt_".$current_id_temp."_".$current_id_groupe."' class='boireaus boireaus_alt' title=\"Lignes correspondant à ce regroupement EDT dans le dernier fichier EDT_COURS.xml importé\" style='display:none'>
		<tr>
			<th>Prof</th>
			<th>Matière</th>
			<th>Jour</th>
			<th>Heure</th>
			<th>Alternance</th>".(($debug_import_edt=="y") ? "
			<th>Debug</th>" : "")."
		</tr>";
								while($lig_edt_lig=mysqli_fetch_object($res_edt_lig)) {
									$details_lignes.="
		<tr>
			<td>$lig_edt_lig->prof_nom $lig_edt_lig->prof_prenom</td>
			<td>$lig_edt_lig->mat_code</td>
			<td>$lig_edt_lig->jour</td>
			<td>$lig_edt_lig->h_debut</td>
			<td>$lig_edt_lig->alternance</td>".(($debug_import_edt=="y") ? "
			<td>".htmlentities($lig_edt_lig->classe)."</td>" : "")."
		</tr>";
								}
								$details_lignes.="
	</table>";
							}
							//==================================

							echo "<table class='boireaus boireaus_alt'>
		<thead>
			<tr>
				<th>Regroupement EDT<br />
					<!--a href='#' onclick='return false' onmouseover=\"affiche_tableau_lignes_edt('table_lignes_edt_".$current_id_temp."_".$current_id_groupe."', '')\" onmouseout=\"affiche_tableau_lignes_edt('table_lignes_edt_".$current_id_temp."_".$current_id_groupe."', 'none')\">".htmlentities($current_nom_groupe)."</a-->

					<a name='regroupement_".$current_id_temp."_groupe_".$current_id_groupe."'></a>

					<!--a href='#regroupement_".$current_id_temp."_groupe_".$current_id_groupe."' onclick=\"alterne_affichage_tableau_lignes_edt('table_lignes_edt_".$current_id_temp."_".$current_id_groupe."');return false;\" title=\"Afficher/masquer les lignes correspondant à ce regroupement EDT dans le dernier fichier EDT_COURS.xml importé.\">".$current_nom_groupe."</a-->

					<a href='#regroupement_".$current_id_temp."_groupe_".$current_id_groupe."' onclick=\"alterne_affichage_tableau_lignes_edt('table_lignes_edt_".$current_id_temp."_".$current_id_groupe."');return false;\" title=\"Afficher/masquer les lignes correspondant à ce regroupement EDT dans le dernier fichier EDT_COURS.xml importé.\">".htmlentities($nom_groupe_edt_clean)."</a>

					".$details_lignes."</th>

				<th>Enseignement Gepi<br />".$tab_info_grp[$current_id_groupe];
							if(isset($tab_prof_grp[$current_id_groupe]["users"])) {
								echo "<br />";
								$cpt_prof=0;
								foreach($tab_prof_grp[$current_id_groupe]["users"] as $tab_prof){
									if($cpt_prof>0){echo ", ";}
									echo casse_mot($tab_prof['prenom'],'majf2')." ".my_strtoupper($tab_prof['nom']);

									echo affiche_lien_edt_prof($tab_prof["login"], $tab_prof["prenom"]." ".$tab_prof["nom"]);
								}
							}
							echo "</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td style='vertical-align:top;'>
					<table class='boireaus boireaus_alt2 resizable sortable'>
						<thead>
						<tr>
							<th class='text' title='Trier suivant cette colonne'></th>
							<th class='text' title='Trier suivant cette colonne'>Nom</th>
							<th class='text' title='Trier suivant cette colonne'>Prénom</th>
							<th class='text' title='Trier suivant cette colonne'>INE</th>
							<th class='text' title='Trier suivant cette colonne'>Classe</th>".((($debug_import_edt=="y")||($avec_colonnes_detail_groupes_eleves=="y")) ? "
							<th title=\"Les indications de cette colonne correspondent aux regroupements désignés comme des classes dans le XML EDT.\nLe terme classe n'est pas forcément idéal.\">Classe(s) EDT</th>
							<th title=\"Les indications de cette colonne correspondent aux regroupements désignés comme des groupes dans le XML EDT.\">Groupe(s) EDT</th>" : "")."
						</tr>
						</thead>
						<tbody>";
							for($loop=0;$loop<count($tab_ele_regroupement_edt['login']);$loop++) {
								$temoin="";
								if(in_array($tab_ele_regroupement_edt['login'][$loop], $diff2)) {
									$temoin="<img src='../images/icons/ico_attention.png' class='icone16' alt='Attention' />";
								
									if((isset($tab_ele_regroupement_edt['date_sortie'][$loop]))&&($tab_ele_regroupement_edt['date_sortie'][$loop]!="")) {
										$temoin.="<img src='../images/icons/retour_sso.png' class='icone16' alt='Départ' ";
										$temoin.="title=\"L'élève a quitté l'établissement le ".formate_date($tab_ele_regroupement_edt['date_sortie'][$loop])."\" ";
										$temoin.="/>";
									}
								
	/*
	echo "<pre>";
	print_r($tab_ele_regroupement_edt);
	echo "</pre>";
	*/
								}
								echo "
						<tr>
							<td>".$temoin."</td>
							<td><a href='../eleves/visu_eleve.php?ele_login=".$tab_ele_regroupement_edt['login'][$loop]."' target='_blank' title=\"Voir la fiche/classeur élève avec ses onglets.\">".$tab_ele_regroupement_edt['nom'][$loop]."</a></td>
							<td>".$tab_ele_regroupement_edt['prenom'][$loop]."</td>
							<td><a href='../eleves/modify_eleve.php?eleve_login=".$tab_ele_regroupement_edt['login'][$loop]."' target='_blank' title=\"Voir/modifier la fiche de cet(te) élève.\">".$tab_ele_regroupement_edt['n_national'][$loop]."</a></td>
							<td>".$tab_ele_regroupement_edt['classe'][$loop];
								if($tab_ele_regroupement_edt['id_classe'][$loop]!="") {
									if(!isset($tab_test_association_grp_classe[$tab_ele_regroupement_edt['id_classe'][$loop]])) {
										$sql="SELECT 1=1 FROM j_groupes_classes WHERE id_groupe='$current_id_groupe' AND id_classe='".$tab_ele_regroupement_edt['id_classe'][$loop]."';";
										//echo "$sql<br />";
										$test_grp_clas=mysqli_query($GLOBALS["mysqli"], $sql);
										if(mysqli_num_rows($test_grp_clas)==0) {
											$tab_test_association_grp_classe[$tab_ele_regroupement_edt['id_classe'][$loop]]=" <img src='../images/icons/flag2.gif' class='icone16' alt='ATTENTION' title=\"Cette classe n'est pas associée au groupe Gepi.\" />";
										}
										else {
											$tab_test_association_grp_classe[$tab_ele_regroupement_edt['id_classe'][$loop]]="";
										}
									}
									echo $tab_test_association_grp_classe[$tab_ele_regroupement_edt['id_classe'][$loop]];
								}
								echo "</td>".((($debug_import_edt=="y")||($avec_colonnes_detail_groupes_eleves=="y")) ? "
							<td>".preg_replace("/, /",",<br />\n", htmlentities(get_valeur_champ('edt_eleves_lignes', "id='".$tab_ele_regroupement_edt['id_edt_eleves_lignes'][$loop]."'", "classe")))."</td>
							<td>".preg_replace("/, /",",<br />\n", htmlentities(get_valeur_champ('edt_eleves_lignes', "id='".$tab_ele_regroupement_edt['id_edt_eleves_lignes'][$loop]."'", "groupes")))."</td>" : "")."
						</tr>";
							}
							echo "
						</tbody>
					</table>
					<p>Effectif&nbsp;: ".count($tab_ele_regroupement_edt['login'])."</p>
				</td>
				<td style='vertical-align:top;'>
					<div id='div_suppr_assoc_".$current_id_groupe."_".$current_id_temp."' style='float:right; width:10em;'>
						<a href='#' onclick=\"suppr_assoc_regroupement_edt($current_id_groupe, $current_id_temp);return false;\" title=\"Si l'association entre le regroupement d'élèves EDT et le groupe Gepi vous semble erroné, vous pouvez supprimer l'association.\nLors de la prochaine mise à jour de l'emploi du temps dans Gepi d'après le EXP_COURS.xml de EDT, une nouvelle association vous sera proposée.\" target='_blank'>Supprimer l'association</a>
					</div>

					<table class='boireaus boireaus_alt2 resizable sortable' width='100%'>
						<thead>
						<tr>
							<th class='text' title='Trier suivant cette colonne'></th>
							<th class='text' title='Trier suivant cette colonne'>Nom</th>
							<th class='text' title='Trier suivant cette colonne'>Prénom</th>
							<th class='text' title='Trier suivant cette colonne'>INE</th>
							<th class='text' title='Trier suivant cette colonne'>Classe</th>".((($debug_import_edt=="y")||($avec_colonnes_detail_groupes_eleves=="y")) ? "
							<th title=\"Les indications de cette colonne correspondent aux regroupements désignés comme des classes dans le XML EDT.\nLe terme classe n'est pas forcément idéal.\">Classe(s) EDT</th>
							<th title=\"Les indications de cette colonne correspondent aux regroupements désignés comme des groupes dans le XML EDT.\">Groupe(s) EDT</th>" : "")."
						</tr>
						</thead>
						<tbody>";
							foreach($current_tab_ele['users'] as $current_login_ele => $current_ele) {
								$temoin="";
								if(in_array($current_login_ele, $diff)) {
									$temoin="<img src='../images/icons/ico_attention.png' class='icone16' alt='Attention' ";
									/*
									if((isset($current_ele['date_sortie']))&&($current_ele['date_sortie']!="")) {
										$temoin.="title=\"L'élève a quitté l'établissement le ".$current_ele['date_sortie']."\" ";
									}
									*/
									$temoin.="/>";
	/*
	echo "<pre>";
	print_r($current_ele);
	echo "</pre>";
	*/
									if (!test_before_eleve_removal($current_login_ele, $current_id_groupe, $num_periode)) {
										if(($acces_prepa_conseil_edit_limite=="y")&&($current_ele['classe']!="")) {
											$temoin.="<a href='../prepa_conseil/edit_limite.php?choix_edit=2&login_eleve=".$current_login_ele."&id_classe=".$current_ele['classe']."&periode1=".$num_periode."&periode2=".$num_periode."' target='_blank'>";
											$temoin.="<img src='../images/icons/bulletin_16.png' width='16' height='16' title='Bulletin non vide' alt='Bulletin non vide' />";
											$temoin.="</a>";
										}
										else {
											$temoin.="<img src='../images/icons/bulletin_16.png' width='16' height='16' title='Bulletin non vide' alt='Bulletin non vide' />";
										}
										$temoin_bull_ou_cn_non_vide++;
									}

									$nb_notes_cn=nb_notes_ele_dans_tel_enseignement($current_login_ele, $current_id_groupe, $num_periode);
									if($nb_notes_cn>0) {
										$temoin.="<img src='../images/icons/cn_16.png' width='16' height='16' title='Carnet de notes non vide: $nb_notes_cn notes' alt='Carnet de notes non vide: $nb_notes_cn notes' />";
										$temoin_bull_ou_cn_non_vide++;
									}

								}
								echo "
						<tr>
							<td>".$temoin."</td>
							<td><a href='../eleves/visu_eleve.php?ele_login=".$current_ele['login']."' target='_blank' title=\"Voir la fiche/classeur élève avec ses onglets.\">".$current_ele['nom']."</a></td>
							<td>".$current_ele['prenom']."</td>
							<td><a href='../eleves/modify_eleve.php?eleve_login=".$current_login_ele."' target='_blank' title=\"Voir/modifier la fiche de cet(te) élève.\">".$current_ele['no_gep']."</a></td>
							<td>";

								if (($debug_import_edt=="y")||($avec_colonnes_detail_groupes_eleves=="y")) {
									$debug_edt_eleves_lignes_classe="";
									$debug_edt_eleves_lignes_groupes="";
									$sql="SELECT eel.* FROM edt_eleves_lignes eel, eleves e WHERE eel.n_national=e.no_gep AND e.no_gep!='' AND e.login='".$current_login_ele."';";
									$res_debug_ele=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($res_debug_ele)>0) {
										$lig_debug_ele=mysqli_fetch_object($res_debug_ele);
										$debug_edt_eleves_lignes_classe=$lig_debug_ele->classe;
										$debug_edt_eleves_lignes_groupes=$lig_debug_ele->groupes;
									}
									else {
										$sql="SELECT eel.* FROM edt_eleves_lignes eel, eleves e WHERE eel.nom=e.nom AND eel.prenom=e.prenom AND e.login='".$current_login_ele."';";
										//$debug_edt_eleves_lignes_classe.=$sql."<br />";
										$res_debug_ele=mysqli_query($GLOBALS["mysqli"], $sql);
										if(mysqli_num_rows($res_debug_ele)==1) {
											$lig_debug_ele=mysqli_fetch_object($res_debug_ele);
											$debug_edt_eleves_lignes_classe=$lig_debug_ele->classe;
											$debug_edt_eleves_lignes_groupes=$lig_debug_ele->groupes;
										}
									}
								}

								if(!isset($tab_nom_classe[$current_ele['classe']])) {
									$tab_nom_classe[$current_ele['classe']]=get_nom_classe($current_ele['classe']);
								}
								echo $tab_nom_classe[$current_ele['classe']];
								echo "</td>".((($debug_import_edt=="y")||($avec_colonnes_detail_groupes_eleves=="y")) ? "
							<td>".preg_replace("/, /",",<br />\n", htmlentities($debug_edt_eleves_lignes_classe))."</td>
							<td>".preg_replace("/, /",",<br />\n", htmlentities($debug_edt_eleves_lignes_groupes))."</td>" : "")."
						</tr>";
							}
							echo "
						</tbody>
					</table>
					<p>Effectif&nbsp;: ".count($current_tab_ele['users'])."</p>

					<!--p style='color:red'>AJOUTER UN LIEN ou CHECKBOX pour mettre à jour l'enseignement Gepi d'après le regroupement EDT (id_tempo=$current_id_temp et id_groupe=$current_id_groupe)<br />
					Si le bulletin ou le carnet de notes ne sont pas vides, ne pas permettre la désinscription.</p-->

					<p>";
							$sql="SELECT MAX(p.num_periode) AS maxper FROM j_groupes_classes jgc, periodes p WHERE p.id_classe=jgc.id_classe AND jgc.id_groupe='$current_id_groupe';";
							$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res_per)==0) {
								$maxper=$num_periode;
							}
							else {
								$lig_per=mysqli_fetch_object($res_per);
								$maxper=$lig_per->maxper;
							}

							for($loop_per=$num_periode;$loop_per<=$maxper;$loop_per++) {
								echo "
						<a href='#' target=\"_blank\" onclick=\"maj_composition_groupe($current_id_groupe, $current_id_temp, $loop_per);return false;\">Mettre à jour en période $loop_per</a><br />";
							}
							echo "
					</p>
					<div id='div_rapport_maj_groupe_".$current_id_groupe."_".$current_id_temp."'>
						Groupe n°$current_id_groupe
					</div>

				</td>
			</tr>
		</tbody>
	</table>";

						}
					}
				}
			}
		}

		echo "<hr />";
		echo "</div>";
		if($temoin_differences==0) {
			if($chaine_js_div!="") {
				$chaine_js_div.=", ";
			}
			$chaine_js_div.="'$cpt_regroupement'";
		}
		$cpt_regroupement++;
	}

	if($chaine_js_div!="") {
		echo "
<p><a href=\"javascript:afficher_tous_div_regroup('')\">Afficher tous les regroupements</a><br />
<a href=\"javascript:afficher_tous_div_regroup('none')\">N'afficher que les regroupements avec différence trouvée</a></p>
<script type='text/javascript'>
	var tab_div_sans_diff=new Array($chaine_js_div);
	function afficher_tous_div_regroup(display) {
		for(i=0;i<tab_div_sans_diff.length;i++) {
			if(document.getElementById('div_regroupement_'+tab_div_sans_diff[i])) {
				document.getElementById('div_regroupement_'+tab_div_sans_diff[i]).style.display=display;
			}
		}
	}
	afficher_tous_div_regroup('none');
	document.getElementById('p_affichage_masquage_div_regroupements').style.display='';

	function maj_composition_groupe(id_groupe, id_edt_tempo, num_periode) {
		//alert(id_groupe+','+id_edt_tempo+','+num_periode);
		document.getElementById('div_rapport_maj_groupe_'+id_groupe+'_'+id_edt_tempo).innerHTML=\"<img src='../images/spinner.gif' class='icone16' alt='Patientez...' /> Patience...\";
		new Ajax.Updater($('div_rapport_maj_groupe_'+id_groupe+'_'+id_edt_tempo),'".$_SERVER['PHP_SELF']."?maj_composition_groupe=y&id_groupe='+id_groupe+'&id_edt_tempo='+id_edt_tempo+'&num_periode='+num_periode,{method: 'get'});
	}

	function suppr_assoc_regroupement_edt(id_groupe, id_edt_tempo) {
		document.getElementById('div_suppr_assoc_'+id_groupe+'_'+id_edt_tempo).innerHTML=\"<img src='../images/spinner.gif' class='icone16' alt='Patientez...' /> Patience...\";
		new Ajax.Updater($('div_suppr_assoc_'+id_groupe+'_'+id_edt_tempo),'".$_SERVER['PHP_SELF']."?div_suppr_assoc_regroupement=y&id_groupe='+id_groupe+'&id_edt_tempo='+id_edt_tempo,{method: 'get'});
	}

	function affiche_tableau_lignes_edt(id, display) {
		if(document.getElementById(id)) {
			document.getElementById(id).style.display=display;
		}
	}

	function alterne_affichage_tableau_lignes_edt(id) {
		if(document.getElementById(id)) {
			if(document.getElementById(id).style.display=='') {
				document.getElementById(id).style.display='none';
			}
			else {
				document.getElementById(id).style.display='';
			}
		}
	}

</script>";
	}

	require("../lib/footer.inc.php");
	die();
}
elseif($action=="editer_ec2") {

	echo "<p class='bold'>Voici les associations groupe Gepi/nom de regroupement EDT<br />
Deux formulaires vont être proposés&nbsp;:<br />
Le <a href='#editer_ec2_2eme_form' title=\"Aller au 2ème formulaire.\">deuxième</a> permet de contrôler qu'un groupe Gepi n'est pas associé à plusieurs regroupements EDT <em>(et de corriger si nécessaire)</em>.</p>";

	// Ménage
	$sql="DELETE FROM edt_corresp2 WHERE id_groupe NOT IN (SELECT id FROM groupes);";
	$del=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="SELECT DISTINCT nom_groupe_edt FROM edt_corresp2 ORDER BY nom_groupe_edt;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p style='color:red'>Aucune association n'est enregistrée.</p>";
	}
	else {
		echo "
<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' id='form_envoi_xml' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='action' value='editer_ec2' />
		<input type='hidden' name='suppr_assoc' value='y' />
		<table class='boireaus boireaus_alt resizable sortable'>
			<thead>
				<tr>
					<th rowspan='2' class='text'>Nom de<br />regroupement EDT</th>
					<th colspan='4'>Groupes Gepi associés</th>
				</tr>
				<tr>
					<th>
						Supprimer<br />l'association<br />
						<a href=\"javascript:tout_cocher();changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' title='Tout cocher' /></a> / <a href=\"javascript:tout_decocher();changement();\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' title='Tout décocher' /></a>
					</th>
					<th class='text'>Matière EDT</th>
					<th class='text'>Groupe Gepi</th>
					<th class='nosort'>Éditer</th>
				</tr>
			</thead>
			<tbody>";
		$cpt=0;
		while($lig=mysqli_fetch_object($res)) {
			$sql="SELECT * FROM edt_corresp2 WHERE nom_groupe_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->nom_groupe_edt)."' ORDER BY mat_code_edt;";
			$res2=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res2)>0) {
				while($lig2=mysqli_fetch_object($res2)) {
					echo "
				<tr class='white_hover'>
					<td><label for='suppr_$cpt' id='texte_suppr_$cpt'>".htmlentities($lig->nom_groupe_edt)."</label></td>
					<td><input type='checkbox' name='suppr[]' id='suppr_$cpt' value='".$lig2->id."' onchange=\"checkbox_change('suppr_$cpt')\" /></td>
					<td><label for='suppr_$cpt'>$lig2->mat_code_edt</label></td>
					<td style='text-align:left'><a href='../groupes/edit_group.php?id_groupe=".$lig2->id_groupe."' title=\"Voir l'enseignement Gepi dans un nouvel onglet\" target='_blank'>".get_info_grp($lig2->id_groupe)."</a><a name='id_groupe_".$lig2->id_groupe."'></a></td>
					<td><a href='".$_SERVER['PHP_SELF']."?id_groupe=".$lig2->id_groupe."&amp;action=editer_ec3' title=\"Modifier l'association\"><img src='../images/edit16.png' class='icone16' alt='Editer' /></a></td>
				</tr>";
					$cpt++;
				}
			}
		}
		echo "
			</tbody>
		</table>
		<p>
			<input type='submit' id='input_submit' value='Supprimer les associations cochées' />
		</p>

		<p style='margin-left:4em; text-indent:-4em; margin-top:1em;'><em>NOTES&nbsp;:</em> Seuls les enseignements Gepi, pour lesquels une association avec un regroupement EDT existe, sont proposés pour la mise à jour des affectations d'élèves.<br />
		Supprimez les associations erronées.</p>
	</fieldset>
</form>

<script type='text/javascript'>
".js_checkbox_change_style('checkbox_change', 'texte_', "n", 1, '', 'red')."

	function tout_cocher() {
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('suppr_'+i)) {
				document.getElementById('suppr_'+i).checked=true;
				checkbox_change('suppr_'+i);
			}
		}
	}

	function tout_decocher() {
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('suppr_'+i)) {
				document.getElementById('suppr_'+i).checked=false;
				checkbox_change('suppr_'+i);
			}
		}
	}
</script>";
	}

	//=================================================================

	echo "<a name='editer_ec2_2eme_form'></a>";
	$sql="SELECT DISTINCT ec.id_groupe FROM classes c, 
							j_groupes_classes jgc, 
							edt_corresp2 ec 
						WHERE c.id=jgc.id_classe AND 
							jgc.id_groupe=ec.id_groupe 
						ORDER BY c.classe, ec.mat_code_edt;";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<br /><p style='color:red'>Aucune association n'est enregistrée.</p>";
	}
	else {
		$tab_assoc_a_1=array();

		echo "<br />
<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' id='form_envoi_xml2' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='action' value='editer_ec2' />
		<input type='hidden' name='suppr_assoc' value='y2' />

		<p id='lien_afficher_masquer_assoc_a_1'><a href='#' onclick=\"masquer_lignes_1_assoc('');return false;\">Afficher</a>/<a href='#' onclick=\"masquer_lignes_1_assoc('none');return false;\">Masquer</a> les lignes pour lesquelles il n'y a qu'un seul regroupement EDT associé à un groupe Gepi.</p>

		<table class='boireaus boireaus_alt resizable sortable'>
			<thead>
				<tr>
					<!--
					 colspan='4' 
					 rowspan='2' 
					-->
					<th colspan='6'>Groupe Gepi</th>

					<th class='text' rowspan='2'>Nom de<br />regroupement EDT</th>
					<th class='text' rowspan='2'>Matière EDT</th>

					<th rowspan='2'>Suppr<br />
						<a href=\"javascript:tout_cocher2();changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' title='Tout cocher' /></a> / <a href=\"javascript:tout_decocher2();changement();\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' title='Tout décocher' /></a>
					</th>
					<th rowspan='2'>Éditer</th>
				</tr>

				<tr>
					<th class='number'>Id</th>
					<th class='text'>Nom</th>
					<th class='text'>Descr.</th>
					<th class='text'>Matière</th>
					<th class='text'>Classes</th>
					<th class='text'>Profs</th>
				</tr>

			</thead>
			<tbody>";
		$cpt=0;
		while($lig=mysqli_fetch_object($res)) {
			$sql="SELECT * FROM edt_corresp2 WHERE id_groupe='".$lig->id_groupe."' ORDER BY nom_groupe_edt, mat_code_edt;";
			$res2=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res2)>0) {
				$current_group=get_group($lig->id_groupe, array("matieres", "classes", "profs"));

				$lig2=mysqli_fetch_object($res2);
				$rowspan="";
				if(mysqli_num_rows($res2)>1) {
					$rowspan=" rowspan='".mysqli_num_rows($res2)."'";
				}
				else {
					$tab_assoc_a_1[]=$cpt;
				}
				echo "
				<tr class='white_hover' id='tr_form2_".$cpt."'>
					<td style='text-align:left'".$rowspan."><span style='display:none'>".$lig->id_groupe."</span><a href='../groupes/edit_group.php?id_groupe=".$lig->id_groupe."' title=\"Voir l'enseignement Gepi dans un nouvel onglet\" target='_blank'>".get_info_grp($lig->id_groupe)."</a><a name='id_groupe_".$lig->id_groupe."'></a></td>
					<td".$rowspan.">".$current_group['name']."</td>
					<td".$rowspan.">".$current_group['description']."</td>
					<td".$rowspan.">".$current_group['matiere']['matiere']."</td>
					<td".$rowspan.">".$current_group['classlist_string']."</td>
					<td".$rowspan.">".$current_group['profs']['proflist_string']."</td>

					<td><label for='suppr2_$cpt' id='texte_suppr2_$cpt'>".htmlentities($lig2->nom_groupe_edt)."</label></td>
					<td><label for='suppr2_$cpt'>$lig2->mat_code_edt</label></td>

					<td><input type='checkbox' name='suppr[]' id='suppr2_$cpt' value='".$lig2->id."' onchange=\"checkbox_change('suppr2_$cpt')\" /></td>

					<td".$rowspan."><a href='".$_SERVER['PHP_SELF']."?id_groupe=".$lig2->id_groupe."&amp;action=editer_ec3' title=\"Modifier l'association\"><img src='../images/edit16.png' class='icone16' alt='Editer' /></a></td>
				</tr>";

				$cpt++;
				while($lig2=mysqli_fetch_object($res2)) {

					echo "
				<tr class='white_hover'>
					<td><label for='suppr2_$cpt' id='texte_suppr2_$cpt'>".htmlentities($lig2->nom_groupe_edt)."</label></td>
					<td><label for='suppr2_$cpt'>$lig2->mat_code_edt</label></td>

					<td><input type='checkbox' name='suppr[]' id='suppr2_$cpt' value='".$lig2->id."' onchange=\"checkbox_change('suppr2_$cpt')\" /></td>
				</tr>";

					$cpt++;
				}
			}
		}
		echo "
			</tbody>
		</table>
		<p>
			<input type='submit' id='input_submit' value='Supprimer les associations cochées' />
		</p>

		<p style='margin-left:4em; text-indent:-4em; margin-top:1em;'><em>NOTES&nbsp;:</em> Seuls les enseignements Gepi, pour lesquels une association avec un regroupement EDT existe, sont proposés pour la mise à jour des affectations d'élèves.<br />
		Supprimez les associations erronées.</p>
	</fieldset>
</form>

<script type='text/javascript'>";

		if(count($tab_assoc_a_1)>0) {
			echo "
	document.getElementById('lien_afficher_masquer_assoc_a_1').style.display='';

	function masquer_lignes_1_assoc(mode) {";
			for($loop=0;$loop<count($tab_assoc_a_1);$loop++) {
				echo "
			if(document.getElementById('tr_form2_".$tab_assoc_a_1[$loop]."')) {
				document.getElementById('tr_form2_".$tab_assoc_a_1[$loop]."').style.display=mode;
			}";
			}
			echo "
	}";
		}

		echo "
	function tout_cocher2() {
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('suppr2_'+i)) {
				document.getElementById('suppr2_'+i).checked=true;
				checkbox_change('suppr2_'+i);
			}
		}
	}

	function tout_decocher2() {
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('suppr2_'+i)) {
				document.getElementById('suppr2_'+i).checked=false;
				checkbox_change('suppr2_'+i);
			}
		}
	}
</script>";
	}

	//=================================================================

	require("../lib/footer.inc.php");
	die();
}
elseif((isset($id_groupe))&&($action=="editer_ec3")) {

	$current_group=get_group($id_groupe);

	echo "<p class='bold'>Regroupement EDT associé à ".get_info_grp($id_groupe)." <a href='edit_group.php?id_groupe=$id_groupe' title=\"Voir/modifier l'enseignement.\"><img src='../images/edit16.png' class='icone16' alt='Editer' /></a></p>";

	$sql="SELECT * FROM edt_corresp WHERE champ='groupe' ORDER BY nom_edt;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p style='color:red'>Aucune association n'est enregistrée.</p>";
	}
	else {
		$tab_assoc=array();
		$sql="SELECT * FROM edt_corresp2 WHERE id_groupe='$id_groupe' ORDER BY nom_groupe_edt;";
		$res2=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res2)>0) {
			while($lig2=mysqli_fetch_object($res2)) {
				$tab_assoc[]=$lig2->nom_groupe_edt;
			}
		}
		if(count($tab_assoc)>1) {
			echo "<p style='color:red; text-indent:-6em; margin-left:6em;'>ANOMALIE&nbsp;: Le groupe/enseignement Gepi est associé à ".count($tab_assoc)." regroupements EDT (<em>";
			for($loop=0;$loop<count($tab_assoc);$loop++) {
				if($loop>0) {
					echo ", ";
				}
				echo $tab_assoc[$loop];
			}
			echo "</em>).<br />Il ne devrait y en avoir qu'un.<br />Choisissez ci-dessous le bon et validez.</p>";
		}

		$lignes_options="
				<option value=''>---</option>";
		while($lig=mysqli_fetch_object($res)) {
			$selected="";
			if(in_array($lig->nom_edt, $tab_assoc)) {
				$selected=" selected";
			}
			$lignes_options.="
				<option value='$lig->id'$selected>$lig->nom_edt</option>";
		}

		echo "
<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' id='form_envoi_xml' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='id_groupe' value='$id_groupe' />
		<input type='hidden' name='action' value='editer_ec3' />
		<input type='hidden' name='valider_ec3' value='y' />
		<p>
			Regroupement EDT à associer&nbsp;: 
			<select name='id_nom_edt'>$lignes_options
			</select>
			 <input type='submit' value='Valider' />
		</p>
	</fieldset>
</form>";
	}

	require("../lib/footer.inc.php");
	die();
}
else {
	echo "plop";
}

require("../lib/footer.inc.php");
?>
