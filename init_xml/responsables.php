<?php
@set_time_limit(0);
/*
* $Id$
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
extract($_POST, EXTR_OVERWRITE);

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

include("../lib/initialisation_annee.inc.php");
$liste_tables_del = $liste_tables_del_etape_resp;

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

// Passer à 'y' pour afficher les requêtes
$debug_resp='n';

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : Importation des responsables des élèves";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
?>
<p class="bold"><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil initialisation</a></p>

<?php

echo "<center><h3 class='gepi'>Deuxième phase d'initialisation<br />Importation des responsables</h3></center>\n";

//if(isset($step1)) {
if(!isset($step1)) {
	$j=0;
	$flag=0;
	while (($j < count($liste_tables_del)) and ($flag==0)) {
		if (old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
			$flag=1;
		}
		$j++;
	}
	if ($flag != 0){
		echo "<p><b>ATTENTION ...</b><br />\n";
		echo "Des données concernant les responsables sont actuellement présentes dans la base GEPI<br /></p>\n";
		echo "<p>Si vous poursuivez la procédure ces données seront effacées.</p>\n";
		echo "<form enctype='multipart/form-data' action='responsables.php' method=post>\n";
		echo add_token_field();
		echo "<input type=hidden name='verif_tables_non_vides' value='y' />\n";
		echo "<input type=hidden name='step1' value='y' />\n";
		echo "<input type='submit' name='confirm' value='Poursuivre la procédure' />\n";
		echo "</form>\n";

		$sql="SELECT 1=1 FROM utilisateurs WHERE statut='responsable';";
		if($debug_resp=='y') {echo "<span style='color:green;'>$sql</span><br />";}
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)>0) {
			$sql="SELECT 1=1 FROM tempo_utilisateurs WHERE statut='responsable';";
			if($debug_resp=='y') {echo "<span style='color:green;'>$sql</span><br />";}
			$test=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test)==0) {
				echo "<p style='color:red'>Il existe un ou des comptes responsables de l'année passée, et vous n'avez pas mis ces comptes en réserve pour imposer le même login/mot de passe cette année.<br />Est-ce bien un choix délibéré ou un oubli de votre part?<br />Pour conserver ces login/mot de de passe de façon à ne pas devoir re-distribuer ces informations (<em>et éviter de perturber ces utilisateurs</em>), vous pouvez procéder à la mise en réserve avant d'initialiser l'année dans la page <a href='../gestion/changement_d_annee.php'>Changement d'année</a> (<em>vous y trouverez aussi la possibilité de conserver les comptes élèves (s'ils n'ont pas déjà été supprimés) et bien d'autres actions à ne pas oublier avant l'initialisation</em>).</p>\n";
			}
		}

		echo "<p><br /></p>\n";
		require("../lib/footer.inc.php");
		die();
	}
}

if (!isset($is_posted)) {

	$sql="CREATE TABLE IF NOT EXISTS `responsables2` (
	`ele_id` VARCHAR( 10 ) NOT NULL ,
	`pers_id` VARCHAR( 10 ) NOT NULL ,
	`resp_legal` VARCHAR( 1 ) NOT NULL ,
	`pers_contact` VARCHAR( 1 ) NOT NULL
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$res_create_table1=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="CREATE TABLE IF NOT EXISTS `resp_adr` (
	`adr_id` VARCHAR( 10 ) NOT NULL ,
	`adr1` VARCHAR( 100 ) NOT NULL ,
	`adr2` VARCHAR( 100 ) NOT NULL ,
	`adr3` VARCHAR( 100 ) NOT NULL ,
	`adr4` VARCHAR( 100 ) NOT NULL ,
	`cp` VARCHAR( 6 ) NOT NULL ,
	`pays` VARCHAR( 50 ) NOT NULL ,
	`commune` VARCHAR( 50 ) NOT NULL ,
	PRIMARY KEY ( `adr_id` )
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$res_create_table2=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="CREATE TABLE IF NOT EXISTS `resp_pers` (
	`pers_id` int(11) NOT NULL,
	`login` varchar(50) NOT NULL,
	`nom` varchar(30) NOT NULL,
	`prenom` varchar(30) NOT NULL,
	`civilite` varchar(5) NOT NULL,
	`tel_pers` varchar(255) NOT NULL,
	`tel_port` varchar(255) NOT NULL,
	`tel_prof` varchar(255) NOT NULL,
	`mel` varchar(100) NOT NULL,
	`adr_id` varchar(10) NOT NULL,
	PRIMARY KEY  (`pers_id`)
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$res_create_table3=mysqli_query($GLOBALS["mysqli"], $sql);


	if(isset($verif_tables_non_vides)) {
		check_token(false);
		$j=0;
		while ($j < count($liste_tables_del)) {
			if (old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
			$del = @mysqli_query($GLOBALS["mysqli"], "DELETE FROM $liste_tables_del[$j]");
			}
			$j++;
		}

		// Suppression des comptes de responsables:
		$sql="DELETE FROM utilisateurs WHERE statut='responsable';";
		$del=mysqli_query($GLOBALS["mysqli"], $sql);
	}

	echo "<p><b>ATTENTION ...</b><br />Vous ne devez procéder à cette opération uniquement si la constitution des classes a été effectuée !</p>";
	//echo "<p>Importation des fichiers <b>PERSONNES.CSV</b>, <b>RESPONSABLES.CSV</b> et <b>ADRESSES.CSV</b> contenant les données relatives aux responsables : veuillez préciser le nom complet du fichier <b>F_ere.dbf</b>.\n";
	echo "<p>Importation des fichiers <b>PERSONNES.CSV</b>, <b>RESPONSABLES.CSV</b> et <b>ADRESSES.CSV</b> contenant les données relatives aux responsables.\n";
	echo "<form enctype='multipart/form-data' action='responsables.php' method=post>\n";
	echo add_token_field();
	echo "<input type=hidden name='is_posted' value='yes' />\n";
	//echo "<input type=hidden name='step1' value='y' />\n";
	echo "<p>Sélectionnez le fichier <b>PERSONNES.CSV</b>:<br /><input type='file' size='80' name='pers_file' />\n";
	echo "<p>Sélectionnez le fichier <b>RESPONSABLES.CSV</b>:<br /><input type='file' size='80' name='resp_file' />\n";
	echo "<p>Sélectionnez le fichier <b>ADRESSES.CSV</b>:<br /><input type='file' size='80' name='adr_file' />\n";
	echo "<p><input type=submit value='Valider' />\n";
	echo "</form>\n";

} else {
	check_token(false);
	$nb_reg_no1=-1;
	$nb_reg_no2=-1;
	$nb_reg_no3=-1;

	$csv_file = isset($_FILES["pers_file"]) ? $_FILES["pers_file"] : NULL;
	if(mb_strtoupper($csv_file['name']) == "PERSONNES.CSV") {
		$fp=fopen($csv_file['tmp_name'],"r");
		if(!$fp){
			echo "<p>Impossible d'ouvrir le fichier PERSONNES.CSV.</p>\n";
			echo "<p><a href='responsables.php'>Cliquer ici </a> pour recommencer !</center></p>\n";
		}
		else{
			// on constitue le tableau des champs à extraire
			$tabchamps=array("pers_id","nom","prenom","civilite","tel_pers","tel_port","tel_prof","mel","adr_id");

			$nblignes=0;
			while (!feof($fp)) {
				$ligne = fgets($fp, 4096);
				if($nblignes==0){
					// Quand on enregistre en CSV des fichiers DBF de GEP avec OpenOffice, les champs sont renommés avec l'ajout de ',...' en fin de nom de champ.
					// On ne retient pas ces ajouts pour $en_tete
					$temp=explode(";",$ligne);
					unset($en_tete);
					for($i=0;$i<sizeof($temp);$i++){
						$temp2=explode(",",$temp[$i]);
						$en_tete[$i]=$temp2[0];
						//echo "\$en_tete[$i]=$temp2[0];<br />\n";
					}

					$nbchamps=sizeof($en_tete);
				}
				$nblignes++;
			}
			fclose ($fp);

			unset($tabindice);
			$cpt_tmp=0;
			for ($k = 0; $k < count($tabchamps); $k++) {
				for ($i = 0; $i < count($en_tete); $i++) {
					if (trim($en_tete[$i]) == $tabchamps[$k]) {
						$tabindice[$cpt_tmp]=$i;
						//echo "\$tabindice[$cpt_tmp]=$i<br />\n";
						$cpt_tmp++;
					}
				}
			}

			//=========================
			$fp=fopen($csv_file['tmp_name'],"r");
			// On lit une ligne pour passer la ligne d'entête:
			$ligne = fgets($fp, 4096);
			//=========================
			$nb_reg_no3=0;
			$nb_record3=0;
			for($k = 1; ($k < $nblignes+1); $k++){
				if(!feof($fp)){
					$ligne = preg_replace('/"/','',fgets($fp, 4096));
					if(trim($ligne)!=""){
						$tabligne=explode(";",$ligne);
						for($i = 0; $i < count($tabchamps); $i++) {
							//$ind = $tabindice[$i];
							$affiche[$i] = trim(preg_replace("/'/"," ",nettoyer_caracteres_nom($tabligne[$tabindice[$i]], "an", " '_-", "")));
						}
						$sql="insert into resp_pers set
									pers_id = '".preg_replace("/[^0-9]/","",$affiche[0])."',
									nom = '".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $affiche[1]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."',
									prenom = '".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $affiche[2]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."',
									civilite = '".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], casse_mot($affiche[3],'majf2')) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."',
									tel_pers = '".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $affiche[4]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."',
									tel_port = '".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $affiche[5]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."',
									tel_prof = '".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $affiche[6]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."',
									mel = '".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $affiche[7]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."',
									adr_id = '".preg_replace("/[^0-9]/","",$affiche[8])."'
									";
						$req = mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$req) {
							echo "<span style='color:red'>".mysqli_error($GLOBALS["mysqli"])."</span> sur <span style='color:red'>".$sql."</span><br />\n";
							$nb_reg_no3++;
						} else {
							$nb_record3++;

							$sql="SELECT * FROM tempo_utilisateurs WHERE identifiant1='".$affiche[0]."';";
							if($debug_resp=='y') {echo "<span style='color:green;'>$sql</span><br />";}
							$res_tmp_u=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res_tmp_u)>0) {
								$lig_tmp_u=mysqli_fetch_object($res_tmp_u);

								$sql="SELECT statut FROM utilisateurs WHERE login='".$lig_tmp_u->login."';";
								$test_u=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($test_u)>0) {
									$lig_test_u=mysqli_fetch_object($test_u);
									if($lig_test_u->statut!='responsable') {
										echo "<span style='color:red;'>ANOMALIE&nbsp;:</span> Un compte d'uilisateur <b>$lig_test_u->statut</b> existait pour le login <b>$lig_tmp_u->login</b> mis en réserve pour ".$personnes[$i]["nom"]." ".$personnes[$i]["prenom"]."&nbsp;:<br /><span style='color:red;'>$sql</span><br />";
									}
								}
								else {

									$sql="INSERT INTO utilisateurs SET login='".$lig_tmp_u->login."', nom='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $affiche[1]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."', prenom='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $affiche[2]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."', ";
									if(isset($affiche[3])){
										$sql.="civilite='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], casse_mot($affiche[3],'majf2')) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."', ";
									}
									$sql.="password='".$lig_tmp_u->password."', salt='".$lig_tmp_u->salt."', email='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $lig_tmp_u->email) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."', statut='responsable', etat='inactif', change_mdp='n', auth_mode='".$lig_tmp_u->auth_mode."';";
									if($debug_resp=='y') {echo "<span style='color:green;'>$sql</span><br />";}
									$insert_u=mysqli_query($GLOBALS["mysqli"], $sql);
									if(!$insert_u) {
										echo "Erreur lors de la création du compte utilisateur pour ".$affiche[1]." ".$affiche[2].".<br />";
									}
									else {
										$sql="UPDATE resp_pers SET login='".$lig_tmp_u->login."' WHERE pers_id='".$affiche[0]."';";
										if($debug_resp=='y') {echo "<span style='color:green;'>$sql</span><br />";}
										$update_rp=mysqli_query($GLOBALS["mysqli"], $sql);
	
										$sql="UPDATE tempo_utilisateurs SET temoin='recree' WHERE identifiant1='".$affiche[0]."';";
										if($debug_resp=='y') {echo "<span style='color:green;'>$sql</span><br />";}
										$update_tmp_u=mysqli_query($GLOBALS["mysqli"], $sql);
									}
								}
							}

						}
					}
				}
			}
			fclose($fp);

			if ($nb_reg_no3 != 0) {
				echo "<p>Lors de l'enregistrement des données de PERSONNES.CSV, il y a eu <span style='color:red'>$nb_reg_no3 erreurs</span>. Essayez de trouvez la cause de l'erreur et recommencez la procédure avant de passer à l'étape suivante.</p>\n";
			} else {
				echo "<p>L'importation des personnes (<em>responsables</em>) dans la base GEPI a été effectuée avec succès (<em>".$nb_record3." enregistrements au total</em>).</p>\n";
			}

		}
	} else if (trim($csv_file['name'])=='') {
		echo "<p>Aucun fichier PERSONNES.CSV n'a été sélectionné !<br />\n";
		//echo "<a href='disciplines.php'>Cliquer ici </a> pour recommencer !</center></p>";
		echo "<a href='responsables.php'>Cliquer ici </a> pour recommencer !</center></p>\n";

	} else {
		echo "<p>Le fichier sélectionné n'est pas valide !<br />\n";
		//echo "<a href='disciplines.php'>Cliquer ici </a> pour recommencer !</center></p>";
		echo "<a href='responsables.php'>Cliquer ici </a> pour recommencer !</center></p>\n";
	}




	$csv_file = isset($_FILES["resp_file"]) ? $_FILES["resp_file"] : NULL;
	if(mb_strtoupper($csv_file['name']) == "RESPONSABLES.CSV") {
		$fp=fopen($csv_file['tmp_name'],"r");
		if(!$fp){
			echo "<p>Impossible d'ouvrir le fichier RESPONSABLES.CSV.</p>";
			echo "<p><a href='responsables.php'>Cliquer ici </a> pour recommencer !</center></p>";
		}
		else{
			// on constitue le tableau des champs à extraire
			$tabchamps=array("ele_id","pers_id","resp_legal","pers_contact");

			$nblignes=0;
			while (!feof($fp)) {
				$ligne = fgets($fp, 4096);
				if($nblignes==0){
					// Quand on enregistre en CSV des fichiers DBF de GEP avec OpenOffice, les champs sont renommés avec l'ajout de ',...' en fin de nom de champ.
					// On ne retient pas ces ajouts pour $en_tete
					$temp=explode(";",$ligne);
					unset($en_tete);
					for($i=0;$i<sizeof($temp);$i++){
						$temp2=explode(",",$temp[$i]);
						$en_tete[$i]=$temp2[0];
						//echo "\$en_tete[$i]=$temp2[0];<br />\n";
					}

					$nbchamps=sizeof($en_tete);
				}
				$nblignes++;
			}
			fclose ($fp);

			unset($tabindice);
			$cpt_tmp=0;
			for ($k = 0; $k < count($tabchamps); $k++) {
				for ($i = 0; $i < count($en_tete); $i++) {
					if (trim($en_tete[$i]) == $tabchamps[$k]) {
						$tabindice[$cpt_tmp]=$i;
						//echo "\$tabindice[$cpt_tmp]=$i<br />\n";
						$cpt_tmp++;
					}
				}
			}

			//=========================
			$fp=fopen($csv_file['tmp_name'],"r");
			// On lit une ligne pour passer la ligne d'entête:
			$ligne = fgets($fp, 4096);
			//=========================
			$nb_reg_no1=0;
			$nb_record1=0;
			for($k = 1; ($k < $nblignes+1); $k++){
				if(!feof($fp)){
					$ligne = preg_replace('/"/','',fgets($fp, 4096));
					if(trim($ligne)!=""){
						$tabligne=explode(";",$ligne);
						for($i = 0; $i < count($tabchamps); $i++) {
							//$ind = $tabindice[$i];
							$affiche[$i] = nettoyer_caracteres_nom($tabligne[$tabindice[$i]], "an", "", "");
						}
						$sql="insert into responsables2 set
									ele_id = '$affiche[0]',
									pers_id = '$affiche[1]',
									resp_legal = '$affiche[2]',
									pers_contact = '$affiche[3]'
									";
						$req = mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$req) {
							$nb_reg_no1++;
							echo "<span style='color:red'>".mysqli_error($GLOBALS["mysqli"])."</span> sur <span style='color:red'>".$sql."</span><br />\n";
						} else {
							$nb_record1++;
						}
					}
				}
			}
			fclose($fp);



			$sql="SELECT r.pers_id,r.ele_id FROM responsables2 r LEFT JOIN eleves e ON e.ele_id=r.ele_id WHERE e.ele_id is NULL;";
			$test=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test)>0){
				echo "<p>Suppression de responsabilités sans élève.\n";
				flush();
				$cpt_nett=0;
				while($lig_nett=mysqli_fetch_object($test)){
					//if($cpt_nett>0){echo ", ";}
					//echo "<a href='modify_resp.php?pers_id=$lig_nett->pers_id' target='_blank'>".$lig_nett->pers_id."</a>";
					$sql="DELETE FROM responsables2 WHERE pers_id='$lig_nett->pers_id' AND ele_id='$lig_nett->ele_id';";
					$nettoyage=mysqli_query($GLOBALS["mysqli"], $sql);
					//flush();
					$cpt_nett++;
				}
				//echo ".</p>\n";
				echo "<br />$cpt_nett associations aberrantes supprimées.</p>\n";
			}



			if ($nb_reg_no1 != 0) {
				echo "<p>Lors de l'enregistrement des données de RESPONSABLES.CSV, il y a eu <span style='color:red'>$nb_reg_no1 erreurs</span>. Essayez de trouvez la cause de l'erreur et recommencez la procédure avant de passer à l'étape suivante.</p>\n";
			}
			else {
				echo "<p>L'importation des relations eleves/responsables dans la base GEPI a été effectuée avec succès (<em>".$nb_record1." enregistrements au total</em>).</p>\n";
			}

		}
	} else if (trim($csv_file['name'])=='') {
		echo "<p>Aucun fichier RESPONSABLES.CSV n'a été sélectionné !<br />\n";
		echo "<a href='responsables.php'>Cliquer ici </a> pour recommencer !</center></p>\n";

	} else {
		echo "<p>Le fichier sélectionné n'est pas valide !<br />\n";
		echo "<a href='responsables.php'>Cliquer ici </a> pour recommencer !</center></p>\n";
	}



	$csv_file = isset($_FILES["adr_file"]) ? $_FILES["adr_file"] : NULL;
	if(mb_strtoupper($csv_file['name']) == "ADRESSES.CSV") {
		$fp=fopen($csv_file['tmp_name'],"r");
		if(!$fp){
			echo "<p>Impossible d'ouvrir le fichier ADRESSES.CSV.</p>";
			echo "<p><a href='responsables.php'>Cliquer ici </a> pour recommencer !</center></p>";
		}
		else{
			// on constitue le tableau des champs à extraire
			$tabchamps=array("adr_id","adr1","adr2","adr3","adr4","cp","pays","commune");

			$nblignes=0;
			while (!feof($fp)) {
				$ligne = fgets($fp, 4096);
				if($nblignes==0){
					// Quand on enregistre en CSV des fichiers DBF de GEP avec OpenOffice, les champs sont renommés avec l'ajout de ',...' en fin de nom de champ.
					// On ne retient pas ces ajouts pour $en_tete
					$temp=explode(";",$ligne);
					unset($en_tete);
					for($i=0;$i<sizeof($temp);$i++){
						$temp2=explode(",",$temp[$i]);
						$en_tete[$i]=$temp2[0];
						//echo "\$en_tete[$i]=$temp2[0];<br />\n";
					}

					$nbchamps=sizeof($en_tete);
				}
				$nblignes++;
			}
			fclose ($fp);

			unset($tabindice);
			$cpt_tmp=0;
			for ($k = 0; $k < count($tabchamps); $k++) {
				for ($i = 0; $i < count($en_tete); $i++) {
					if (trim($en_tete[$i]) == $tabchamps[$k]) {
						$tabindice[$cpt_tmp]=$i;
						//echo "\$tabindice[$cpt_tmp]=$i<br />\n";
						$cpt_tmp++;
					}
				}
			}

			//=========================
			$fp=fopen($csv_file['tmp_name'],"r");
			// On lit une ligne pour passer la ligne d'entête:
			$ligne = fgets($fp, 4096);
			//=========================
			$nb_reg_no2=0;
			$nb_record2=0;
			for($k = 1; ($k < $nblignes+1); $k++){
				if(!feof($fp)){
					$ligne = preg_replace('/"/','',fgets($fp, 4096));
					if(trim($ligne)!=""){
						$tabligne=explode(";",$ligne);
						for($i = 0; $i < count($tabchamps); $i++) {
							//$ind = $tabindice[$i];
							$affiche[$i] = preg_replace("/'$/","",preg_replace("/^'/"," ", nettoyer_caracteres_nom($tabligne[$tabindice[$i]],"an", " .'-", " ")));
						}
						$sql="insert into resp_adr set
									adr_id = '".preg_replace("/[^0-9]/","",$affiche[0])."',
									adr1 = '".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $affiche[1]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."',
									adr2 = '".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $affiche[2]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."',
									adr3 = '".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $affiche[3]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."',
									adr4 = '".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $affiche[4]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."',
									cp = '".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $affiche[5]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."',
									pays = '".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $affiche[6]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."',
									commune = '".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $affiche[7]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."'
									";
						$req = mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$req) {
							$nb_reg_no2++;
							echo "<span style='color:red'>".mysqli_error($GLOBALS["mysqli"])."</span> sur <span style='color:red'>".$sql."</span><br />\n";
						} else {
							$nb_record2++;
						}
					}
				}
			}
			fclose($fp);

			if ($nb_reg_no2 != 0) {
				echo "<p>Lors de l'enregistrement des données de ADRESSES.CSV, il y a eu <span style='color:red'>$nb_reg_no2 erreurs</span>. Essayez de trouvez la cause de l'erreur et recommencez la procédure avant de passer à l'étape suivante.</p>\n";
			} else {
				echo "<p>L'importation des adresses de responsables dans la base GEPI a été effectuée avec succès (<em>".$nb_record2." enregistrements au total</em>).</p>\n";
			}

		}
	} else if (trim($csv_file['name'])=='') {
		echo "<p>Aucun fichier ADRESSES.CSV n'a été sélectionné !<br />\n";
		//echo "<a href='disciplines.php'>Cliquer ici </a> pour recommencer !</center></p>";
		echo "<a href='responsables.php'>Cliquer ici </a> pour recommencer !</center></p>\n";

	} else {
		echo "<p>Le fichier sélectionné n'est pas valide !<br />\n";
		//echo "<a href='disciplines.php'>Cliquer ici </a> pour recommencer !</center></p>";
		echo "<a href='responsables.php'>Cliquer ici </a> pour recommencer !</center></p>\n";
	}


	if(($nb_reg_no1==0)&&($nb_reg_no2==0)&&($nb_reg_no3==0)){
		echo "<p>Vous pouvez à présent retourner à l'accueil et effectuer toutes les autres opérations d'initialisation manuellement ou bien procéder à la troisième phase d'importation des matières et de définition des options suivies par les élèves.</p>\n";
		echo "<center><p><a href='../accueil.php'>Retourner à l'accueil</a></p></center>\n";
		echo "<center><p><a href='disciplines_csv.php'>Procéder à la troisième phase</a>.</p></center>\n";
	}
}
require("../lib/footer.inc.php");
?>
