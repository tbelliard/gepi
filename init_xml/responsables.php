<?php
@set_time_limit(0);
/*
* Last modification  : 07/05/2005
*
* Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
die();
} else if ($resultat_session == '0') {
header("Location: ../logout.php?auto=1");
die();
};

$liste_tables_del = array(
//"absences",
//"aid",
//"aid_appreciations",
//"aid_config",
//"avis_conseil_classe",
//"classes",
//"droits",
//"eleves",
// ==================================
// On vide l'ancienne table responsables pour ne pas conserver des infos d'années antérieures:
"responsables",

"responsables2",
"resp_pers",
"resp_adr",
// ==================================
//"etablissements",
//"j_aid_eleves",
//"j_aid_utilisateurs",
//"j_eleves_classes",
//"j_eleves_etablissements",
//"j_eleves_professeurs",
//"j_eleves_regime",
//"j_professeurs_matieres",
//"log",
//"matieres",
//"matieres_appreciations",
//"matieres_notes",
//"observatoire",
//"observatoire_comment",
//"observatoire_config",
//"observatoire_niveaux",
//"observatoire_j_resp_champ",
//"observatoire_suivi",
//"periodes",
//"periodes_observatoire",
"tempo2",
//"temp_gep_import",
"tempo",
//"utilisateurs",
//"cn_cahier_notes",
//"cn_conteneurs",
//"cn_devoirs",
//"cn_notes_conteneurs",
//"cn_notes_devoirs",
//"setting"
);



if (!checkAccess()) {
header("Location: ../logout.php?auto=1");
die();
}

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : Importation des responsables des élèves";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<p class=bold><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil initialisation</a></p>

<?php

// On vérifie si l'extension d_base est active
//verif_active_dbase();

echo "<center><h3 class='gepi'>Deuxième phase d'initialisation<br />Importation des responsables</h3></center>\n";

//if(isset($step1)) {
if(!isset($step1)) {
	$j=0;
	$flag=0;
	while (($j < count($liste_tables_del)) and ($flag==0)) {
		if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
			$flag=1;
		}
		$j++;
	}
	if ($flag != 0){
		echo "<p><b>ATTENTION ...</b><br />\n";
		echo "Des données concernant les responsables sont actuellement présentes dans la base GEPI<br /></p>\n";
		echo "<p>Si vous poursuivez la procédure ces données seront effacées.</p>\n";
		echo "<form enctype='multipart/form-data' action='responsables.php' method=post>\n";
		echo "<input type=hidden name='step1' value='y' />\n";
		echo "<input type='submit' name='confirm' value='Poursuivre la procédure' />\n";
		echo "</form>\n";
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
	);";
	$res_create_table1=mysql_query($sql);

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
	);";
	$res_create_table2=mysql_query($sql);

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
	);";
	$res_create_table3=mysql_query($sql);


	$j=0;
	while ($j < count($liste_tables_del)) {
		if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
		$del = @mysql_query("DELETE FROM $liste_tables_del[$j]");
		}
		$j++;
	}

	echo "<p><b>ATTENTION ...</b><br />Vous ne devez procéder à cette opération uniquement si la constitution des classes a été effectuée !</p>";
	//echo "<p>Importation des fichiers <b>PERSONNES.CSV</b>, <b>RESPONSABLES.CSV</b> et <b>ADRESSES.CSV</b> contenant les données relatives aux responsables : veuillez préciser le nom complet du fichier <b>F_ere.dbf</b>.\n";
	echo "<p>Importation des fichiers <b>PERSONNES.CSV</b>, <b>RESPONSABLES.CSV</b> et <b>ADRESSES.CSV</b> contenant les données relatives aux responsables.\n";
	echo "<form enctype='multipart/form-data' action='responsables.php' method=post>\n";
	echo "<input type=hidden name='is_posted' value='yes' />\n";
	//echo "<input type=hidden name='step1' value='y' />\n";
	echo "<p>Sélectionnez le fichier <b>PERSONNES.CSV</b>:<br /><input type='file' size='80' name='pers_file' />\n";
	echo "<p>Sélectionnez le fichier <b>RESPONSABLES.CSV</b>:<br /><input type='file' size='80' name='resp_file' />\n";
	echo "<p>Sélectionnez le fichier <b>ADRESSES.CSV</b>:<br /><input type='file' size='80' name='adr_file' />\n";
	echo "<p><input type=submit value='Valider' />\n";
	echo "</form>\n";

} else {
	$nb_reg_no1=-1;
	$nb_reg_no2=-1;
	$nb_reg_no3=-1;

	$csv_file = isset($_FILES["pers_file"]) ? $_FILES["pers_file"] : NULL;
	//echo strtoupper($csv_file['name'])."<br />";
	if(strtoupper($csv_file['name']) == "PERSONNES.CSV") {
		$fp=fopen($csv_file['tmp_name'],"r");
		if(!$fp){
			echo "<p>Impossible d'ouvrir le fichier PERSONNES.CSV.</p>\n";
			echo "<p><a href='responsables.php'>Cliquer ici </a> pour recommencer !</center></p>\n";
		}
		else{
			// on constitue le tableau des champs à extraire
			//$tabchamps=array("pers_id","nom","prenom","tel_pers","tel_port","tel_prof","mel","adr_id");
			$tabchamps=array("pers_id","nom","prenom","civilite","tel_pers","tel_port","tel_prof","mel","adr_id");
			//echo "\$tabchamps=array(\"pers_id\",\"nom\",\"prenom\",\"civilite\",\"tel_pers\",\"tel_port\",\"tel_prof\",\"mel\",\"adr_id\");<br />\n";

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

			// On range dans tabindice les indices des champs retenus
			/*
			for ($k = 0; $k < count($tabchamps); $k++) {
				for ($i = 0; $i < count($en_tete); $i++) {
					if (trim($en_tete[$i]) == $tabchamps[$k]) {
						$tabindice[] = $i;
					}
				}
			}
			*/
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
				//$ligne = dbase_get_record($fp,$k);
				if(!feof($fp)){
					$ligne = fgets($fp, 4096);
					if(trim($ligne)!=""){
						$tabligne=explode(";",$ligne);
						for($i = 0; $i < count($tabchamps); $i++) {
							//$ind = $tabindice[$i];
							$affiche[$i] = traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($tabligne[$tabindice[$i]]))));
						}
						$req = mysql_query("insert into resp_pers set
									pers_id = '$affiche[0]',
									nom = '$affiche[1]',
									prenom = '$affiche[2]',
									civilite = '".ucfirst(strtolower($affiche[3]))."',
									tel_pers = '$affiche[4]',
									tel_port = '$affiche[5]',
									tel_prof = '$affiche[6]',
									mel = '$affiche[7]',
									adr_id = '$affiche[8]'
									");
						if(!$req) {
							$nb_reg_no3++;
							echo mysql_error();
						} else {
							$nb_record3++;
						}
					}
				}
			}
			//dbase_close($fp);
			fclose($fp);

			if ($nb_reg_no3 != 0) {
				echo "<p>Lors de l'enregistrement des données de PERSONNES.CSV, il y a eu $nb_reg_no3 erreurs. Essayez de trouvez la cause de l'erreur et recommencez la procédure avant de passer à l'étape suivante.</p>\n";
			} else {
				echo "<p>L'importation des personnes (responsables) dans la base GEPI a été effectuée avec succès (".$nb_record3." enregistrements au total).</p>\n";
				/*
				echo "<br />Vous pouvez à présent retourner à l'accueil et effectuer toutes les autres opérations d'initialisation manuellement ou bien procéder à la troixième phase d'importation des matières et de définition des options suivies par les élèves.</p>\n";
				echo "<center><p><a href='../accueil.php'>Retourner à l'accueil</a></p></center>\n";
				echo "<center><p><a href='disciplines_csv.php'>Procéder à la troisième phase</a>.</p></center>\n";
				*/
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
	//echo strtoupper($csv_file['name'])."<br />";
	if(strtoupper($csv_file['name']) == "RESPONSABLES.CSV") {
		$fp=fopen($csv_file['tmp_name'],"r");
		if(!$fp){
			echo "<p>Impossible d'ouvrir le fichier RESPONSABLES.CSV.</p>";
			echo "<p><a href='responsables.php'>Cliquer ici </a> pour recommencer !</center></p>";
		}
		else{
			// on constitue le tableau des champs à extraire
			$tabchamps=array("ele_id","pers_id","resp_legal","pers_contact");
			//echo "\$tabchamps=array(\"ele_id\",\"pers_id\",\"resp_legal\",\"pers_contact\");<br />\n";

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

			// On range dans tabindice les indices des champs retenus
			/*
			for ($k = 0; $k < count($tabchamps); $k++) {
				for ($i = 0; $i < count($en_tete); $i++) {
					if (trim($en_tete[$i]) == $tabchamps[$k]) {
						$tabindice[] = $i;
					}
				}
			}
			*/
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
				//$ligne = dbase_get_record($fp,$k);
				if(!feof($fp)){
					$ligne = fgets($fp, 4096);
					if(trim($ligne)!=""){
						$tabligne=explode(";",$ligne);
						for($i = 0; $i < count($tabchamps); $i++) {
							//$ind = $tabindice[$i];
							$affiche[$i] = traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($tabligne[$tabindice[$i]]))));
						}
						$req = mysql_query("insert into responsables2 set
									ele_id = '$affiche[0]',
									pers_id = '$affiche[1]',
									resp_legal = '$affiche[2]',
									pers_contact = '$affiche[3]'
									");
						if(!$req) {
							$nb_reg_no1++;
							echo mysql_error();
						} else {
							$nb_record1++;
						}
					}
				}
			}
			//dbase_close($fp);
			fclose($fp);

			if ($nb_reg_no1 != 0) {
				echo "<p>Lors de l'enregistrement des données de RESPONSABLES.CSV, il y a eu $nb_reg_no1 erreurs. Essayez de trouvez la cause de l'erreur et recommencez la procédure avant de passer à l'étape suivante.</p>\n";
			}
			else {
				echo "<p>L'importation des relations eleves/responsables dans la base GEPI a été effectuée avec succès (".$nb_record1." enregistrements au total).</p>\n";
				/*
				echo "<br />Vous pouvez à présent retourner à l'accueil et effectuer toutes les autres opérations d'initialisation manuellement ou bien procéder à la troixième phase d'importation des matières et de définition des options suivies par les élèves.</p>\n";
				echo "<center><p><a href='../accueil.php'>Retourner à l'accueil</a></p></center>\n";
				echo "<center><p><a href='disciplines_csv.php'>Procéder à la troisième phase</a>.</p></center>\n";
				*/
			}

		}
	} else if (trim($csv_file['name'])=='') {
		echo "<p>Aucun fichier RESPONSABLES.CSV n'a été sélectionné !<br />\n";
		//echo "<a href='disciplines.php'>Cliquer ici </a> pour recommencer !</center></p>";
		echo "<a href='responsables.php'>Cliquer ici </a> pour recommencer !</center></p>\n";

	} else {
		echo "<p>Le fichier sélectionné n'est pas valide !<br />\n";
		//echo "<a href='disciplines.php'>Cliquer ici </a> pour recommencer !</center></p>";
		echo "<a href='responsables.php'>Cliquer ici </a> pour recommencer !</center></p>\n";
	}



	$csv_file = isset($_FILES["adr_file"]) ? $_FILES["adr_file"] : NULL;
	//echo strtoupper($csv_file['name'])."<br />";
	if(strtoupper($csv_file['name']) == "ADRESSES.CSV") {
		$fp=fopen($csv_file['tmp_name'],"r");
		if(!$fp){
			echo "<p>Impossible d'ouvrir le fichier ADRESSES.CSV.</p>";
			echo "<p><a href='responsables.php'>Cliquer ici </a> pour recommencer !</center></p>";
		}
		else{
			// on constitue le tableau des champs à extraire
			$tabchamps=array("adr_id","adr1","adr2","adr3","adr4","cp","pays","commune");
			//echo "\$tabchamps=array(\"adr_id\",\"adr1\",\"adr2\",\"adr3\",\"adr4\",\"cp\",\"pays\",\"commune\");<br />\n";

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

			// On range dans tabindice les indices des champs retenus
			/*
			for ($k = 0; $k < count($tabchamps); $k++) {
				for ($i = 0; $i < count($en_tete); $i++) {
					if (trim($en_tete[$i]) == $tabchamps[$k]) {
						$tabindice[] = $i;
					}
				}
			}
			*/
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
				//$ligne = dbase_get_record($fp,$k);
				if(!feof($fp)){
					$ligne = fgets($fp, 4096);
					if(trim($ligne)!=""){
						$tabligne=explode(";",$ligne);
						for($i = 0; $i < count($tabchamps); $i++) {
							//$ind = $tabindice[$i];
							$affiche[$i] = traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($tabligne[$tabindice[$i]]))));
						}
						$req = mysql_query("insert into resp_adr set
									adr_id = '$affiche[0]',
									adr1 = '$affiche[1]',
									adr2 = '$affiche[2]',
									adr3 = '$affiche[3]',
									adr4 = '$affiche[4]',
									cp = '$affiche[5]',
									pays = '$affiche[6]',
									commune = '$affiche[7]'
									");
						if(!$req) {
							$nb_reg_no2++;
							echo mysql_error();
						} else {
							$nb_record2++;
						}
					}
				}
			}
			//dbase_close($fp);
			fclose($fp);

			if ($nb_reg_no2 != 0) {
				echo "<p>Lors de l'enregistrement des données de ADRESSES.CSV, il y a eu $nb_reg_no2 erreurs. Essayez de trouvez la cause de l'erreur et recommencez la procédure avant de passer à l'étape suivante.</p>\n";
			} else {
				echo "<p>L'importation des adresses de responsables dans la base GEPI a été effectuée avec succès (".$nb_record2." enregistrements au total).</p>\n";
				/*
				echo "<br />Vous pouvez à présent retourner à l'accueil et effectuer toutes les autres opérations d'initialisation manuellement ou bien procéder à la troixième phase d'importation des matières et de définition des options suivies par les élèves.</p>\n";
				echo "<center><p><a href='../accueil.php'>Retourner à l'accueil</a></p></center>\n";
				echo "<center><p><a href='disciplines_csv.php'>Procéder à la troisième phase</a>.</p></center>\n";
				*/
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