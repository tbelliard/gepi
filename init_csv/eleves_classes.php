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

$liste_tables_del = array("j_eleves_classes");

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : Importation des matières";
require_once("../lib/header.inc.php");
//************** FIN EN-TETE ***************

$en_tete=isset($_POST['en_tete']) ? $_POST['en_tete'] : "no";

?>
<p class="bold"><a href="index.php#eleves_classes"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil initialisation</a></p>
<?php

echo "<center><h3 class='gepi'>Cinquième phase d'initialisation<br />Importation des associations élèves-classes</h3></center>";


if (!isset($_POST["action"])) {
	//
	// On sélectionne le fichier à importer
	//

	echo "<p>Vous allez effectuer la cinquième étape : elle consiste à importer le fichier <b>g_eleves_classes.csv</b> contenant les données relatives aux disciplines.</p>\n";
	echo "<p>Remarque : cette opération n'efface par les classes. Elle ne fait qu'une mise à jour, le cas échéant, de la liste des matières.</p>\n";
	echo "<p>Les champs suivants doivent être présents, dans l'ordre, et <b>séparés par un point-virgule</b> : </p>\n";
	echo "<ul><li>Identifiant (<em>interne</em>) de l'élève</li>\n" .
			"<li>Identifiant court de la classe (<em>ex: 1S2</em>)</li>\n" .
			"</ul>\n";
	echo "<p>Veuillez préciser le nom complet du fichier <b>g_eleves_classes.csv</b>.</p>\n";
	echo "<form enctype='multipart/form-data' action='eleves_classes.php' method='post'>\n";
	echo add_token_field();
	echo "<input type='hidden' name='action' value='upload_file' />\n";
	echo "<p><input type=\"file\" size=\"80\" name=\"csv_file\" /></p>\n";

    echo "<p><label for='en_tete' style='cursor:pointer;'>Si le fichier à importer comporte une première ligne d'en-tête (non vide) à ignorer, <br />cocher la case ci-contre</label>&nbsp;<input type='checkbox' name='en_tete' id='en_tete' value='yes' checked /></p>\n";

	echo "<p><input type='submit' value='Valider' /></p>\n";
	echo "</form>\n";

} else {
	//
	// Quelque chose a été posté
	//
	if ($_POST['action'] == "save_data") {
		check_token(false);
		//
		// On enregistre les données dans la base.
		// Le fichier a déjà été affiché, et l'utilisateur est sûr de vouloir enregistrer
		//

		echo "<p><em>On vide d'abord les tables suivantes&nbsp;:</em> ";
		$j=0;
		$k=0;
		while ($j < count($liste_tables_del)) {
			$sql="SHOW TABLES LIKE '".$liste_tables_del[$j]."';";
			//echo "$sql<br />";
			$test = sql_query1($sql);
			if ($test != -1) {
				if($k>0) {echo ", ";}
				$sql="SELECT 1=1 FROM $liste_tables_del[$j];";
				$res_test_tab=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_test_tab)>0) {
					$sql="DELETE FROM $liste_tables_del[$j];";
					$del = @mysqli_query($GLOBALS["mysqli"], $sql);
					echo "<b>".$liste_tables_del[$j]."</b>";
					echo " (".mysqli_num_rows($res_test_tab).")";
				}
				else {
					echo $liste_tables_del[$j];
				}
				$k++;
			}
			$j++;
		}

		$sql="SELECT * FROM tempo2;";
		$res_temp=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_temp)==0) {
			echo "<p style='color:red'>ERREUR&nbsp;: Aucune association élève/classe n'a été trouvée&nbsp;???</p>\n";
			echo "<p><br /></p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		echo "<br />\n";
		echo "<p><em>On remplit les tables 'classes', 'periodes' et 'j_eleves_classes'&nbsp;:</em> ";

		$i = 0;
		// Compteur d'erreurs
		$error = 0;
		// Compteur d'enregistrement
		$total = 0;
		while ($lig=mysqli_fetch_object($res_temp)) {
			$reg_id_int = $lig->col1;
			$reg_classe = $lig->col2;

			// On nettoie et on vérifie :
			$reg_id_int = preg_replace("/[^0-9]/","",trim($reg_id_int));
			if (mb_strlen($reg_id_int) > 50) $reg_id_int = mb_substr($reg_id_int, 0, 50);

			$reg_classe = preg_replace("/[^A-Za-z0-9._ \-]/","",trim($reg_classe));
			//$reg_classe=nettoyer_caracteres_nom($reg_classe, "an", " _-", "");
			if (mb_strlen($reg_classe) > 100) $reg_classe = mb_substr($reg_classe, 0, 100);


			if(($reg_id_int!='')&&($reg_classe!='')){
				// Première étape : on s'assure que l'élève existe et on récupère son login... S'il n'existe pas, on laisse tomber.
				$sql="SELECT login FROM eleves WHERE elenoet = '" . $reg_id_int . "'";
				//echo "$sql<br />";
				$test = mysqli_query($GLOBALS["mysqli"], $sql);
				if (mysqli_num_rows($test) == 1) {
					$login_eleve = mysql_result($test, 0, "login");

					// Maintenant que tout est propre et que l'élève existe, on fait un test sur la table pour voir si la classe existe

					$sql="SELECT id FROM classes WHERE classe = '" . ((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $reg_classe) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")) . "'";
					//echo "$sql<br />";
					$test = mysqli_query($GLOBALS["mysqli"], $sql);

					if (mysqli_num_rows($test) == 0) {
						// Test négatif : aucune classe avec ce nom court... on créé !

						$sql="INSERT INTO classes SET " .
							"classe = '" . ((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $reg_classe) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")) . "', " .
							"nom_complet = '" . ((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $reg_classe) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")) . "', " .
							"format_nom = 'np', " .
							"display_rang = 'n', " .
							"display_address = 'n', " .
							"display_coef = 'y'";
						//echo "$sql<br />";
						$insert1 = mysqli_query($GLOBALS["mysqli"], $sql);
						// On récupère l'ID de la classe nouvelle créée, pour enregistrer les périodes
						$classe_id = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);

						for ($p=1;$p<4;$p++) {
							if ($p == 1) {$v = "O";}
							else {$v = "N";}
							$sql="INSERT INTO periodes SET " .
									"nom_periode = 'Période ".$p . "', " .
									"num_periode = '" . $p . "', " .
									"verouiller = '" . $v . "', " .
									"id_classe = '" . $classe_id . "', ".
									"date_verrouillage='0000-00-00 00:00:00'";
							//echo "$sql<br />";
							$insert2 = mysqli_query($GLOBALS["mysqli"], $sql);
						}
						$num_periods = 3;

					} else {
						// La classe existe
						// On récupère son ID
						$classe_id = mysql_result($test, 0, "id");
						$num_periods = mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(num_periode) FROM periodes WHERE id_classe = '" . $classe_id . "'"), 0);
					}

					// Maintenant qu'on a l'ID de la classe et le nombre de périodes, on enregistre l'association

					for ($p=1;$p<=$num_periods;$p++) {
						$sql="INSERT INTO j_eleves_classes SET login = '" . $login_eleve . "', " .
																	"id_classe = '" . $classe_id . "', " .
																	"periode = '" . $p . "'";
						//echo "$sql<br />";
						$insert = mysqli_query($GLOBALS["mysqli"], $sql);
					}

					if (!$insert) {
						$error++;
						echo "<span style='color:red'>".((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."</span><br />\n";
					} else {
						$total++;
					}

				}
			}

			$i++;
			//if (!isset($_POST['ligne'.$i.'_id_int'])) $go = false;
		}

		$sql="update periodes set date_verrouillage='0000-00-00 00:00:00';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if($res) {
			echo "Réinitialisation des dates de verrouillage de périodes effectuée.<br />";
		}
		else {
			echo "Erreur lors de la réinitialisation des dates de verrouillage de périodes.<br />";
		}

		if ($error > 0) echo "<p><font color='red'>Il y a eu " . $error . " erreurs.</font></p>\n";
		if ($total > 0) echo "<p>" . $total . " associations eleves-classes ont été enregistrées.</p>\n";

		echo "<p><a href='index.php#eleves_classes'>Revenir à la page précédente</a></p>\n";


	} else if ($_POST['action'] == "upload_file") {
		check_token(false);
		//
		// Le fichier vient d'être envoyé et doit être traité
		// On va donc afficher le contenu du fichier tel qu'il va être enregistré dans Gepi
		// en proposant des champs de saisie pour modifier les données si on le souhaite
		//

		$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;

		// On vérifie le nom du fichier... Ce n'est pas fondamentalement indispensable, mais
		// autant forcer l'utilisateur à être rigoureux
		if(my_strtolower($csv_file['name']) == "g_eleves_classes.csv") {

			// Le nom est ok. On ouvre le fichier
			$fp=fopen($csv_file['tmp_name'],"r");

			if(!$fp) {
				// Aie : on n'arrive pas à ouvrir le fichier... Pas bon.
				echo "<p>Impossible d'ouvrir le fichier CSV !</p>\n";
				echo "<p><a href='eleves_classes.php'>Cliquer ici </a> pour recommencer !</p>\n";
			} else {

				// Fichier ouvert ! On attaque le traitement

				// On va stocker toutes les infos dans un tableau
				// Une ligne du CSV pour une entrée du tableau
				$data_tab = array();

				//=========================
				// On lit une ligne pour passer la ligne d'entête:
				if($en_tete=="yes") {
					$ligne = fgets($fp, 4096);
				}
				//=========================

				$k = 0;
				while (!feof($fp)) {
					$ligne = trim(fgets($fp, 4096));
					if($ligne!="") {

						$tabligne=explode(";",$ligne);

						// 0 : ID interne de l'élève
						// 1 : nom court de la classe

						// On nettoie et on vérifie :
						$tabligne[0] = preg_replace("/[^0-9]/","",trim($tabligne[0]));
						if (mb_strlen($tabligne[0]) > 50) $tabligne[0] = mb_substr($tabligne[0], 0, 50);

						//$tabligne[1] = preg_replace("/[^A-Za-z0-9 .\-éèüëïäê]/","",trim($tabligne[1]));
						$tabligne[1]=preg_replace("/[^A-Za-z0-9._ \-]/","",trim(remplace_accents($tabligne[1])));
						//$tabligne[1]=nettoyer_caracteres_nom($tabligne[1], "an", " _-", "");
						if (mb_strlen($tabligne[1]) > 100) $tabligne[1] = mb_substr($tabligne[1], 0, 100);

						$data_tab[$k] = array();

						$data_tab[$k]["id_int"] = $tabligne[0];
						$data_tab[$k]["classe"] = $tabligne[1];

						$k++;

					}
				}

				fclose($fp);

				// Fin de l'analyse du fichier.
				// Maintenant on va afficher tout ça.

				$sql="TRUNCATE TABLE tempo2;";
				$vide_table = mysqli_query($GLOBALS["mysqli"], $sql);

				$nb_error=0;

				echo "<form enctype='multipart/form-data' action='eleves_classes.php' method='post'>\n";
				echo add_token_field();
				echo "<input type='hidden' name='action' value='save_data' />\n";
				echo "<table border='1' class='boireaus' summary='Tableau élèves/classes'>\n";
				echo "<tr><th>ID interne de l'élève</th><th>Classe</th></tr>\n";

				$alt=1;
				for ($i=0;$i<$k;$i++) {
					if(isset($data_tab[$i]["id_int"])) {
						$alt=$alt*(-1);
						echo "<tr class='lig$alt'>\n";
						echo "<td>\n";
						$sql="INSERT INTO tempo2 SET col1='".$data_tab[$i]["id_int"]."',
						col2='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $data_tab[$i]["classe"]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."';";
						$insert=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$insert) {
							echo "<span style='color:red'>";
							echo $data_tab[$i]["id_int"];
	 						echo "</span>";
							$nb_error++;
						}
						else {
							echo $data_tab[$i]["id_int"];
						}
						echo "</td>\n";
						echo "<td>\n";
						echo $data_tab[$i]["classe"];
						echo "</td>\n";
						echo "</tr>\n";
					}
				}

				echo "</table>\n";

				if($nb_error>0) {
					echo "<p><span style='color:red'>$nb_error erreur(s) détectée(s) lors de la préparation.</span></p>\n";
				}

				echo "<p><input type='submit' value='Enregistrer' /></p>\n";

				echo "</form>\n";
			}

		} else if (trim($csv_file['name'])=='') {

			echo "<p>Aucun fichier n'a été sélectionné !<br />\n";
			echo "<a href='eleves_classes.php'>Cliquer ici </a> pour recommencer !</p>\n";

		} else {
			echo "<p>Le fichier sélectionné n'est pas valide !<br />\n";
			echo "<a href='eleves_classes.php'>Cliquer ici </a> pour recommencer !</p>\n";
		}
	}
}
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
