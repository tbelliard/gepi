<?php

@set_time_limit(0);
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
extract($_POST, EXTR_OVERWRITE);

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
};

$liste_tables_del = array(
"j_eleves_professeurs"
);

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

// Page bourrinée... la gestion du token n'est pas faite... et ne sera faite que si quelqu'un utilise encore ce mode d'initialisation et le manifeste sur la liste de diffusion gepi-users
check_token();

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : Importation des professeurs principaux";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
?>
<p class=bold><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil initialisation</a></p>

<?php

// On vérifie si l'extension d_base est active
//verif_active_dbase();

echo "<center><h3 class='gepi'>Sixième phase<br />Importation des professeurs principaux</h3></center>";

if (!isset($step1)) {
	$j=0;
	$flag=0;
	while (($j < count($liste_tables_del)) and ($flag==0)) {
		if (mysql_result(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
			$flag=1;
		}
		$j++;
	}
	if ($flag != 0){
		echo "<p><b>ATTENTION ...</b><br />";
		echo "Des professeurs principaux sont actuellement définis dans la base GEPI<br /></p>";
		echo "<p>Si vous poursuivez la procédure ces données seront supprimées et remplacées par celles de votre fichier F_DIV.CSV</p>";

		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>";
		echo "<input type=hidden name='step1' value='y' />";
		echo "<input type='submit' name='confirm' value='Poursuivre la procédure' />";
		echo "</form>";
		die();
	}
}

if (!isset($is_posted)) {
	$j=0;
	while ($j < count($liste_tables_del)) {
		if (mysql_result(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
			$del = @mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM $liste_tables_del[$j]");
		}
		$j++;
	}

	echo "<p><b>ATTENTION ...</b><br />Vous ne devez procéder à cette opération uniquement si la constitution des classes a été effectuée et si les professeurs ont été importés !</p>";
	echo "<p>Importation du fichier <b>F_div.csv</b> contenant les associations classe/professeur principal  : veuillez préciser le nom complet du fichier <b>F_div.csv</b>.";
	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>";
	echo "<input type=hidden name='is_posted' value='yes' />";
	echo "<input type=hidden name='step1' value='y' />";
	echo "<p><input type='file' size='80' name='dbf_file' />";
	echo "<p><input type=submit value='Valider' />";
	echo "</form>";

} else {
	$dbf_file = isset($_FILES["dbf_file"]) ? $_FILES["dbf_file"] : NULL;
	//if(strtoupper($dbf_file['name']) == "F_TMT.DBF") {
	if(strtoupper($dbf_file['name']) == "F_DIV.CSV") {
		//$fp = dbase_open($dbf_file['tmp_name'], 0);
		$fp = fopen($dbf_file['tmp_name'],"r");
		if(!$fp) {
			echo "<p>Impossible d'ouvrir le fichier CSV</p>";
			echo "<p><a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>";
		} else {
			// on constitue le tableau des champs à extraire
			//$tabchamps = array("MATIMN","MATILC");
			$tabchamps = array("DIVCOD","NUMIND");

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
				}
				$nblignes++;
			}
			fclose ($fp);
/*
			if (@dbase_get_record_with_names($fp,1)) {
				$temp = @dbase_get_record_with_names($fp,1);
			} else {
				echo "<p>Le fichier sélectionné n'est pas valide !<br />";
				echo "<a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>";
				die();
			}

			$nb = 0;
			foreach($temp as $key => $val){
				$en_tete[$nb] = "$key";
				$nb++;
			}
*/
			// On range dans tabindice les indices des champs retenus
			for ($k = 0; $k < count($tabchamps); $k++) {
				for ($i = 0; $i < count($en_tete); $i++) {
					//if ($en_tete[$i] == $tabchamps[$k]) {
					if (trim($en_tete[$i]) == $tabchamps[$k]) {
						$tabindice[] = $i;
					}
				}
			}
/*
			echo "<p>Dans le tableau ci-dessous, les identifiants en rouge correspondent à des nouvelles matières dans la base GEPI. les identifiants en vert correspondent à des identifiants de matières détectés dans le fichier GEP mais déjà présents dans la base GEPI.<br /><br />Il est possible que certaines matières ci-dessous, bien que figurant dans le fichier GEP, ne soient pas utilisées dans votre établissement cette année. C'est pourquoi il vous sera proposé en fin de procédure d'initialsation, un nettoyage de la base afin de supprimer ces données inutiles.</p>";
*/
			echo "<table border=1 cellpadding=2 cellspacing=2>\n";
			echo "<tr><th><p class=\"small\">Classe</p></th><th><p class=\"small\">Professeur principal</p></th></tr>\n";


			//=========================
			$fp=fopen($dbf_file['tmp_name'],"r");
			// On lit une ligne pour passer la ligne d'entête:
			$ligne = fgets($fp, 4096);
			//=========================
			$nb_reg_no = 0;
			for($k = 1; ($k < $nblignes+1); $k++){
				//$ligne = dbase_get_record($fp,$k);
				if(!feof($fp)){
					$ligne = fgets($fp, 4096);
					if(trim($ligne)!=""){
						$tabligne=explode(";",$ligne);
						$temoin_erreur="non";
						for($i = 0; $i < count($tabchamps); $i++) {
							//$affiche[$i] = traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($ligne[$tabindice[$i]]))));
							$affiche[$i] = traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($tabligne[$tabindice[$i]]))));
							//echo "<tr><td colspan='2'>|\$affiche[$i]|=|$affiche[$i]|</td></tr>";
							if($affiche[$i]==""){
								$temoin_erreur="oui";
							}
						}
						if($temoin_erreur!="oui"){
							$sql="SELECT id FROM classes WHERE classe='$affiche[0]'";
							$res_classe=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
							if(mysqli_num_rows($res_classe)==1){
								$lig_classe=mysqli_fetch_object($res_classe);
								$id_classe=$lig_classe->id;

								/*
								$sql="SELECT num_periode FROM periodes WHERE id_classe='$id_classe' ORDER BY num_periode"
								$res_periodes=mysql_query($sql);
								unset($tab_num_periode);
								$tab_num_periode=array();
								while($lig_periode=mysql_fetch_object($res_periodes)){
									$tab_num_periode[]=$lig_periode->num_periode;
								}
								*/
								$sql="SELECT col1 FROM tempo2 WHERE col2='$affiche[1]'";
								$res_prof=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
								$lig_prof=mysqli_fetch_object($res_prof);

								//$sql="SELECT login,periode FROM j_eleves_classes WHERE id_classe='$id_classe' ORDER BY login,periode"
								$sql="SELECT login FROM j_eleves_classes WHERE id_classe='$id_classe' ORDER BY login";
								$res_eleve=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
								while($lig_eleve=mysqli_fetch_object($res_eleve)){
									$sql="INSERT INTO j_eleves_professeurs VALUES('$lig_eleve->login','$lig_prof->col1','$id_classe')";
									$res_prof_eleve=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
								}
								echo "<tr><td>$affiche[0]</td><td>$lig_prof->col1</td></tr>\n";
							}
						}
					}
				}
			}
			echo "</table>";
			//dbase_close($fp);
			fclose($fp);
			if ($nb_reg_no != 0) {
				echo "<p>Lors de l'enregistrement des données il y a eu $nb_reg_no erreurs. Essayez de trouvez la cause de l'erreur et recommencez la procédure avant de passer à l'étape suivante.";
			} else {
				echo "<p>L'importation des professeurs principaux dans la base GEPI a été effectuée avec succès !</p>";

				echo "<p>Avant de procéder à un nettoyage des tables pour supprimer les données inutiles, vous devriez effectuer une <a href='../gestion/accueil_sauve.php?action=dump'>sauvegarde</a><br />\n";
				echo "Après cette sauvegarde, effectuez le nettoyage en repassant par 'Gestion générale/Initialisation des données à partir de fichiers DBF et XML/Procéder à la septième phase'.<br />\n";
				echo "Si les données sont effectivement inutiles, c'est terminé.<br />\n";
				echo "Sinon, vous pourrez restaurer votre sauvegarde et vous aurez pu noter les associations profs/matières/classes manquantes... à effectuer par la suite manuellement dans 'Gestion des bases'.</p>";

				echo "<p>Vous pouvez procéder à l'étape suivante de nettoyage des tables GEPI.</p>\n";
				echo "<center><p><a href='clean_tables.php'>Suppression des données inutiles</a></p></center>\n";
			}
		}
	} else if (trim($dbf_file['name'])=='') {
		echo "<p>Aucun fichier n'a été sélectionné !<br />";
		echo "<a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>";

	} else {
		echo "<p>Le fichier sélectionné n'est pas valide !<br />";
		echo "<a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>";
	}
}
require("../lib/footer.inc.php");
?>