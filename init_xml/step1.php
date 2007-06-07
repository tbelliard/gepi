<?php
@set_time_limit(0);
/*
* Last modification  : 09/03/2005
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


if (!checkAccess()) {
header("Location: ../logout.php?auto=1");
die();
}
//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : Importation des élèves - Etape 1";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<p class=bold><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil initialisation</a></p>
<?php

// On vérifie si l'extension d_base est active
//verif_active_dbase();

echo "<center><h3 class='gepi'>Première phase d'initialisation<br />Importation des élèves,  constitution des classes et affectation des élèves dans les classes</h3></center>\n";


if (!isset($is_posted)) {
	echo "<p>Vous allez effectuer la première étape : elle consiste à importer le fichier <b>ELEVES.CSV</b> (<i>généré à partir des exports XML de Sconet</i>) contenant toutes les données dans une table temporaire de la base de données de <b>GEPI</b>.";
	echo "<p>Veuillez préciser le nom complet du fichier <b>ELEVES.CSV</b>.";
	echo "<form enctype='multipart/form-data' action='step1.php' method=post>\n";
	echo "<input type=hidden name='is_posted' value='yes' />\n";
	echo "<p><input type=\"file\" size=\"80\" name=\"csv_file\" /></p>\n";
	echo "<p><input type=submit value='Valider' /></p>\n";
	echo "</form>\n";
} else {
	$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;
	if(strtoupper($csv_file['name']) == "ELEVES.CSV"){
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
			);";
			$create_table = mysql_query($sql);

			$del = @mysql_query("DELETE FROM temp_gep_import2");
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


			/*
			// On range dans un tableau les en-têtes des champs
			if (@dbase_get_record_with_names($fp,1)) {
				$temp = @dbase_get_record_with_names($fp,1);
			} else {
				echo "<p>Le fichier sélectionné n'est pas valide !<br />";
				echo "<a href='step1.php'>Cliquer ici </a> pour recommencer !</center></p>";
				die();
			}

			$nb = 0;
			foreach($temp as $key => $val){
				$en_tete[$nb] = "$key";
				$nb++;
			}
			*/


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
				//$ligne = dbase_get_record($fp,$k);
				if(!feof($fp)){
					$ligne = fgets($fp, 4096);
					if(trim($ligne)!=""){
						$tabligne=explode(";",$ligne);
						//$query = "INSERT INTO temp_gep_import2 VALUES ('$k',''";
						$query = "INSERT INTO temp_gep_import2 SET ID_TEMPO='$k'";
						for($i = 0; $i < count($tabchamps); $i++) {
							//$query = $query.",";

							$ind = $tabindice[$i];
							//$affiche = dbase_filter(trim($ligne[$ind]));
							//$affiche = dbase_filter(trim($tabligne[$ind]));
							// On vire en plus les apostrophes dans les noms,...
							$affiche = ereg_replace("'"," ",dbase_filter(trim($tabligne[$ind])));
							//$query = $query."\"".$affiche."\"";
							if($tabchamps[$ind]!=''){
								$query = $query.",";
								$query = $query."$tabchamps[$ind]='".$affiche."'";
							}
							if (($en_tete[$ind] == 'DIVCOD') and ($affiche == '')) {$enregistre = "no";}
						}
						//$query = $query.")";
						//echo "$query<br />";
						if ($enregistre == "yes") {
							$register = mysql_query($query);
							if (!$register) {
								echo "<p class=\"small\"><font color='red'>Analyse de la ligne $k : erreur lors de l'enregistrement !</font></p>";
								$nb_reg_no++;
							} else {
								$nb_reg_ok++;
					//                        echo ".";
							}
						} else {
				//                    echo ".";
						}
					}
				}
			}

			//dbase_close($fp);
			fclose($fp);
			if ($nb_reg_no != 0) {
				echo "<p>Lors de l'enregistrement des données il y a eu $nb_reg_no erreurs, vous ne pouvez pas procéder à la suite de l'initialisation. Trouvez la cause de l'erreur et recommencez la procédure, après avoir vidé la table temporaire.";
			}
			else {
				echo "<p>Les $nblignes lignes du fichier ELEVES.CSV ont été analysées.<br />$nb_reg_ok lignes de données correspondant à des élèves de l'année en cours ont été enregistrées dans une table temporaire.<br />Il n'y a pas eu d'erreurs, vous pouvez procéder à l'étape suivante.</p>";
				echo "<center><p><a href='step2.php'>Accéder à l'étape 2</a></p></center>";
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