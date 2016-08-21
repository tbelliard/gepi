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

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$debug_ele="n";

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : Importation des élèves - Etape 1";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
?>
<p class="bold"><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil initialisation</a></p>
<?php

// On vérifie si l'extension d_base est active
//verif_active_dbase();

echo "<center><h3 class='gepi'>Première phase d'initialisation<br />Importation des élèves, constitution des classes et affectation des élèves dans les classes</h3></center>\n";


if (!isset($is_posted)) {
	echo "<p>Vous allez effectuer la première étape&nbsp;: elle consiste à importer le fichier <b>ELEVES.CSV</b> (<em>généré à partir des exports XML de Sconet</em>) contenant toutes les données dans une table temporaire de la base de données de <b>GEPI</b>.";
	echo "<p>Veuillez préciser le nom complet du fichier <b>ELEVES.CSV</b>.";
	echo "<form enctype='multipart/form-data' action='step1.php' method=post>\n";
	echo add_token_field();
	echo "<input type=hidden name='is_posted' value='yes' />\n";
	echo "<p><input type=\"file\" size=\"80\" name=\"csv_file\" /></p>\n";
	echo "<p><input type=submit value='Valider' /></p>\n";
	echo "</form>\n";

	$sql="SELECT 1=1 FROM utilisateurs WHERE statut='eleve';";
	if($debug_ele=='y') {echo "<span style='color:green;'>$sql</span><br />";}
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)>0) {
		$sql="SELECT 1=1 FROM tempo_utilisateurs WHERE statut='eleve';";
		if($debug_ele=='y') {echo "<span style='color:green;'>$sql</span><br />";}
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)==0) {
			echo "<p style='color:red'>Il existe un ou des comptes élèves de l'année passée, et vous n'avez pas mis ces comptes en réserve pour imposer le même login/mot de passe cette année.<br />Est-ce bien un choix délibéré ou un oubli de votre part?<br />Pour conserver ces login/mot de de passe de façon à ne pas devoir re-distribuer ces informations (<em>et éviter de perturber ces utilisateurs</em>), vous pouvez procéder à la mise en réserve avant d'initialiser l'année dans la page <a href='../gestion/changement_d_annee.php'>Changement d'année</a> (<em>vous y trouverez aussi la possibilité de conserver les comptes parents et bien d'autres actions à ne pas oublier avant l'initialisation</em>).</p>\n";
		}
	}

} else {
	check_token(false);
	$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;
	if(mb_strtoupper($csv_file['name']) == "ELEVES.CSV"){
		//$fp = dbase_open($csv_file['tmp_name'], 0);
		$fp=fopen($csv_file['tmp_name'],"r");

		if(!$fp){
			echo "<p>Impossible d'ouvrir le fichier CSV !</p>";
			echo "<p><a href='step1.php'>Cliquer ici </a> pour recommencer !</center></p>\n";
		}
		else{
			$sql="CREATE TABLE IF NOT EXISTS `temp_gep_import2` (
			`ID_TEMPO` varchar(40) NOT NULL default '',
			`LOGIN` varchar(40) NOT NULL default '',
			`ELENOM` varchar(40) NOT NULL default '',
			`ELEPRE` varchar(40) NOT NULL default '',
			`ELESEXE` varchar(40) NOT NULL default '',
			`ELEDATNAIS` varchar(40) NOT NULL default '',
			`ELENOET` varchar(40) NOT NULL default '',
			`ELE_ID` varchar(40) NOT NULL default '',
			`ELEDOUBL` varchar(40) NOT NULL default '',
			`ELENONAT` varchar(40) NOT NULL default '',
			`ELEREG` varchar(40) NOT NULL default '',
			`DIVCOD` varchar(40) NOT NULL default '',
			`ETOCOD_EP` varchar(40) NOT NULL default '',
			`ELEOPT1` varchar(40) NOT NULL default '',
			`ELEOPT2` varchar(40) NOT NULL default '',
			`ELEOPT3` varchar(40) NOT NULL default '',
			`ELEOPT4` varchar(40) NOT NULL default '',
			`ELEOPT5` varchar(40) NOT NULL default '',
			`ELEOPT6` varchar(40) NOT NULL default '',
			`ELEOPT7` varchar(40) NOT NULL default '',
			`ELEOPT8` varchar(40) NOT NULL default '',
			`ELEOPT9` varchar(40) NOT NULL default '',
			`ELEOPT10` varchar(40) NOT NULL default '',
			`ELEOPT11` varchar(40) NOT NULL default '',
			`ELEOPT12` varchar(40) NOT NULL default ''
			) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
			$create_table = mysqli_query($GLOBALS["mysqli"], $sql);

			$del = @mysqli_query($GLOBALS["mysqli"], "DELETE FROM temp_gep_import2");
			// on constitue le tableau des champs à extraire
			$tabchamps = array("ELENOM","ELEPRE","ELESEXE","ELEDATNAIS","ELENOET","ELE_ID","ELEDOUBL","ELENONAT","ELEREG","DIVCOD","ETOCOD_EP", "ELEOPT1", "ELEOPT2", "ELEOPT3", "ELEOPT4", "ELEOPT5", "ELEOPT6", "ELEOPT7", "ELEOPT8", "ELEOPT9", "ELEOPT10", "ELEOPT11", "ELEOPT12");

			//$nblignes = dbase_numrecords($fp); //number of rows
			//$nbchamps = dbase_numfields($fp); //number of fields

			$nblignes=0;
			while (!feof($fp)) {
				$ligne = fgets($fp, 4096);
				if($nblignes==0){
					// Quand on enregistre en CSV des fichiers DBF de GEP avec OpenOffice, les champs sont renommés avec l'ajout de ',...' en fin de nom de champ.
					// On ne retient pas ces ajouts pour $en_tete
					$temp=explode(";",$ligne);
					for($i=0;$i<sizeof($temp);$i++){
						$temp2=explode(",",$temp[$i]);
						$en_tete[$i]=$temp2[0];
					}

					//$en_tete=explode(";",$ligne);
					$nbchamps=sizeof($en_tete);
					//echo "\$nbchamps=$nbchamps<br />\n";
					/*
					for($i=0;$i<$nbchamps;$i++){
						echo "\$en_tete[$i]=$en_tete[$i]<br />\n";
					}
					*/
				}
				$nblignes++;
			}
			fclose ($fp);

			$cpt_tmp=0;
			for ($k = 0; $k < count($tabchamps); $k++) {
				for ($i = 0; $i < count($en_tete); $i++) {
					if (trim($en_tete[$i]) == $tabchamps[$k]) {
						$tabindice[$cpt_tmp]=$i;
						$cpt_tmp++;
					}
				}
			}

			//=========================
			$fp=fopen($csv_file['tmp_name'],"r");
			// On lit une ligne pour passer la ligne d'entête:
			$ligne = fgets($fp, 4096);
			//=========================
			$nb_reg_ok = 0;
			$nb_reg_no = 0;
			for($k = 1; ($k < $nblignes+1); $k++){
				$enregistre = "yes";
				if(!feof($fp)){
					$ligne = preg_replace('/"/','',fgets($fp, 4096));
					if(trim($ligne)!=""){
						$tabligne=explode(";",$ligne);
						//$query = "INSERT INTO temp_gep_import2 VALUES ('$k',''";
						$query = "INSERT INTO temp_gep_import2 SET ID_TEMPO='$k'";
						for($i = 0; $i < count($tabchamps); $i++) {
							//$query = $query.",";

							$ind = $tabindice[$i];
							// On vire en plus les apostrophes dans les noms,...
							$affiche = trim(preg_replace("/'/"," ",nettoyer_caracteres_nom($tabligne[$ind], "an", " '_-", "")));
							if($tabchamps[$ind]!=''){
								$query = $query.",";
								$query = $query."$tabchamps[$ind]='".mysqli_real_escape_string($GLOBALS["mysqli"], $affiche)."'";
							}
							if (($en_tete[$ind] == 'DIVCOD') and ($affiche == '')) {$enregistre = "no";}
						}
						if ($enregistre == "yes") {
							$register = mysqli_query($GLOBALS["mysqli"], $query);
							if (!$register) {
								echo "<p class=\"small\"><font color='red'>Analyse de la ligne $k : erreur lors de l'enregistrement !</font></p>";
								$nb_reg_no++;
							} else {
								$nb_reg_ok++;
							}
						} else {
							//echo ".";
						}
					}
				}
			}

			fclose($fp);
			if ($nb_reg_no != 0) {
				echo "<p>Lors de l'enregistrement des données il y a eu $nb_reg_no erreurs, vous ne pouvez pas procéder à la suite de l'initialisation. Trouvez la cause de l'erreur et recommencez la procédure, après avoir vidé la table temporaire.";
			}
			else {
				echo "<p>Les $nblignes lignes du fichier ELEVES.CSV ont été analysées.<br />$nb_reg_ok lignes de données correspondant à des élèves de l'année en cours ont été enregistrées dans une table temporaire.<br />Il n'y a pas eu d'erreurs, vous pouvez procéder à l'étape suivante.</p>";
				echo "<center><p><a href='step2.php?a=a".add_token_in_url()."'>Accéder à l'étape 2</a></p></center>";
			}
		}
	}
	else if (trim($csv_file['name'])=='') {

		echo "<p>Aucun fichier n'a été sélectionné !<br />";
		echo "<a href='step1.php'>Cliquer ici </a> pour recommencer !</center></p>";

	}
	else {
		echo "<p>Le fichier sélectionné n'est pas valide !<br />";
		echo "<a href='step1.php'>Cliquer ici </a> pour recommencer !</center></p>";
	}
}
require("../lib/footer.inc.php");
?>
