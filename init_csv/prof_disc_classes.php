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

include("../lib/initialisation_annee.inc.php");
$liste_tables_del = $liste_tables_del_etape_matieres;

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : Importation des matières";
require_once("../lib/header.inc.php");
//************** FIN EN-TETE ***************

$en_tete=isset($_POST['en_tete']) ? $_POST['en_tete'] : "no";

?>
<p class="bold"><a href="index.php#prof_disc_classes"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil initialisation</a></p>
<?php

echo "<center><h3 class='gepi'>Sixième phase d'initialisation<br />Importation des associations profs-matières-classes (enseignements)</h3></center>\n";


if (!isset($_POST["action"])) {
	//
	// On sélectionne le fichier à importer
	//

	echo "<p>Vous allez effectuer la sixième étape : elle consiste à importer le fichier <b>g_prof_disc_classes.csv</b> contenant les données relatives aux enseignements.</p>\n";
	echo "<p><b>ATTENTION !</b> Avec cette opération, vous effacez tous les groupes d'enseignement qui avaient été définis l'année dernière. Ils seront écrasés par ceux que vous allez importer avec la procédure courante.</p>\n";
	echo "<p>Les champs suivants doivent être présents, dans l'ordre, et <b>séparés par un point-virgule</b> : </p>\n";
	echo "<ul><li>Login du professeur</li>\n" .
			"<li>Nom court de la matière</li>\n" .
			"<li>Le ou les identifiant(s) de classe (<em>séparés par un point d'exclamation ; ex : 1S1!1S2</em>)</li>\n" .
			"<li>Type d'enseignement (<em>CG pour enseignement général suivi par toute la classe, OPT pour un enseignement optionnel</em>)</li>\n" .
			"</ul>\n";
	echo "<p>Exemple de ligne pour un enseignement général :<br />\n" .
			"&nbsp;&nbsp;&nbsp;DUPONT.JEAN;MATHS;1S1;CG<br />\n" .
			"Exemple de ligne pour un enseignement optionnel avec des élèves de plusieurs classes :<br />\n" .
			"&nbsp;&nbsp;&nbsp;DURANT.PATRICE;ANGL2;1S1!1S2!1S3;OPT</p>\n";
	echo "<p>Veuillez préciser le nom complet du fichier <b>g_prof_disc_classes.csv</b>.</p>\n";
	echo "<form enctype='multipart/form-data' action='prof_disc_classes.php' method='post'>\n";
	echo add_token_field();
	echo "<input type='hidden' name='action' value='upload_file' />\n";
	echo "<p><input type=\"file\" size=\"80\" name=\"csv_file\" />\n";

	echo "<p><label for='en_tete' style='cursor:pointer;'>Si le fichier à importer comporte une première ligne d'en-tête (<em>non vide</em>) à ignorer, <br />cocher la case ci-contre</label>&nbsp;<input type='checkbox' name='en_tete' id='en_tete' value='yes' checked /></p>\n";

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
				$res_test_tab=mysql_query($sql);
				if(mysql_num_rows($res_test_tab)>0) {
					$sql="DELETE FROM $liste_tables_del[$j];";
					$del = @mysql_query($sql);
					echo "<b>".$liste_tables_del[$j]."</b>";
					echo " (".mysql_num_rows($res_test_tab).")";
				}
				else {
					echo $liste_tables_del[$j];
				}
				$k++;
			}
			$j++;
		}

		$sql="SELECT * FROM tempo4;";
		$res_tempo4=mysql_query($sql);
		if(mysql_num_rows($res_tempo4)==0) {
			echo "<p style='color:red'>ERREUR&nbsp;: Aucune association professeur/matière/classe/type n'a été trouvée&nbsp;???</p>\n";
			echo "<p><br /></p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		echo "<br />\n";
		echo "<p><em>On remplit les tables 'matieres', 'groupes', 'j_groupes_matieres', 'j_groupes_professeurs', 'j_groupes_classes' et 'j_eleves_groupes'&nbsp;:</em> ";

		$i = 0;
		// Compteur d'erreurs
		$error = 0;
		// Compteur d'enregistrement
		$total = 0;
		while ($lig=mysql_fetch_object($res_tempo4)) {
			$reg_prof = $lig->col1;
			$reg_matiere = $lig->col2;
			$reg_classes = $lig->col3;
			$reg_type = $lig->col4;

			// On nettoie et on vérifie :
			$reg_prof = preg_replace("/[^A-Za-z0-9._]/","",trim(my_strtoupper($reg_prof)));
			if (mb_strlen($reg_prof) > 50) $reg_prof = mb_substr($reg_prof, 0, 50);

			$reg_matiere = preg_replace("/[^A-Za-z0-9._\-]/","",trim(my_strtoupper($reg_matiere)));
			if (mb_strlen($reg_matiere) > 50) $reg_matiere = mb_substr($reg_matiere, 0, 50);

			$reg_classes = preg_replace("/[^A-Za-z0-9._ \-!]/","",trim($reg_classes));
			if (mb_strlen($reg_classes) > 2000) $reg_classes = mb_substr($reg_classes, 0, 2000); // C'est juste pour éviter une tentative d'overflow...

			$reg_type = preg_replace("/[^A-Za-z]/","",trim(my_strtoupper($reg_type)));
			if ($reg_type != "CG" AND $reg_type != "OPT") $reg_type = "";


			// Première étape : on s'assure que le prof existe. S'il n'existe pas, on laisse tomber.
			$test = mysql_result(mysql_query("SELECT count(login) FROM utilisateurs WHERE login = '" . $reg_prof . "'"),0);
			if ($test == 1) {

				// Le prof existe. cool. Maintenant on récupère la matière.
				$test = mysql_query("SELECT nom_complet FROM matieres WHERE matiere = '" . $reg_matiere . "'");

				if (mysql_num_rows($test) == 1) {
					// La matière existe
					// On récupère le nom complet de la matière
					$reg_matiere_complet = mysql_result($test, 0, "nom_complet");

					// Maintenant on en arrive aux classes
					// On récupère un tableau :
					$reg_classes = explode("!", $reg_classes);

					// On détermine le type de groupe
					if (count($reg_classes) > 1) {
						// On force le type "OPT" s'il y a plusieurs classes
						$reg_type = "OPT";
					} else {
						if ($reg_type == "") {
							// Si on n'a qu'une seule classe et que rien n'est spécifié, on a par défaut
							// un cours général
							$reg_type = "CG";
						}
					}

					// Si on arrive ici, c'est que normalement tout est bon.
					// On va quand même s'assurer qu'on a des classes valides.

					$valid_classes = array();
					foreach ($reg_classes as $classe) {
						$test = mysql_query("SELECT id FROM classes WHERE classe = '" . $classe . "'");
						if (mysql_num_rows($test) == 1) $valid_classes[] = mysql_result($test, 0, "id");
					}

					if (count($valid_classes) > 0) {
						// C'est bon, on a au moins une classe valide. On peut créer le groupe !

						$new_group = mysql_query("INSERT INTO groupes SET name = '" . $reg_matiere . "', description = '" . html_entity_decode($reg_matiere_complet) . "'");
						$group_id = mysql_insert_id();
						if (!$new_group) {
							echo "<span style='color:red'>".mysql_error().'<span><br />';
						}
						// Le groupe est créé. On associe la matière.
						$res = mysql_query("INSERT INTO j_groupes_matieres SET id_groupe = '".$group_id."', id_matiere = '" . $reg_matiere . "'");
						if (!$res) {
							echo "<span style='color:red'>".mysql_error().'<span><br />';
						}
						// On associe le prof
						$res = mysql_query("INSERT INTO j_groupes_professeurs SET id_groupe = '" . $group_id . "', login = '" . $reg_prof . "'");
						if (!$res) {
							echo "<span style='color:red'>".mysql_error().'<span><br />';
						}
						// On associe la matière au prof
						$res = mysql_query("INSERT INTO j_professeurs_matieres SET id_professeur = '" . $reg_prof . "', id_matiere = '" . $reg_matiere . "'");
						// On associe le groupe aux classes (ou à la classe)
						foreach ($valid_classes as $classe_id) {
							$res = mysql_query("INSERT INTO j_groupes_classes SET id_groupe = '" . $group_id . "', id_classe = '" . $classe_id ."'");
							if (!$res) {
								echo "<span style='color:red'>".mysql_error().'<span><br />';
							}
						}

						// Si le type est à "CG", on associe les élèves de la classe au groupe
						if ($reg_type == "CG") {

							// On récupère le nombre de périodes pour la classe
							$periods = mysql_result(mysql_query("SELECT count(num_periode) FROM periodes WHERE id_classe = '" . $valid_classes[0] . "'"), 0);
							$get_eleves = mysql_query("SELECT DISTINCT(login) FROM j_eleves_classes WHERE id_classe = '" . $valid_classes[0] . "'");
							$nb = mysql_num_rows($get_eleves);
							for ($e=0;$e<$nb;$e++) {
								$current_eleve = mysql_result($get_eleves, $e, "login");
								for ($p=1;$p<=$periods;$p++) {
									$res = mysql_query("INSERT INTO j_eleves_groupes SET login = '" . $current_eleve . "', id_groupe = '" . $group_id . "', periode = '" . $p . "'");
									if (!$res) {
										echo "<span style='color:red'>".mysql_error().'<span><br />';
									}
								}
							}
						}

						if (!$new_group) {
							$error++;
						} else {
							$total++;
						}
					} // -> Fin du test si on a au moins une classe valide
				} // -> Fin du test où la matière existe

			} // -> Fin du test où le prof existe

			$i++;
		}

		echo "<p>Opération terminée.</p>\n";
		if ($error > 0) echo "<p style='color:red'>Il y a eu " . $error . " erreurs.</p>\n";
		if ($total > 0) echo "<p>" . $total . " groupes ont été enregistrés.</p>\n";

		echo "<p><a href='index.php#prof_disc_classes'>Revenir à la page précédente</a></p>\n";


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
		if(my_strtolower($csv_file['name']) == "g_prof_disc_classes.csv") {

			// Le nom est ok. On ouvre le fichier
			$fp=fopen($csv_file['tmp_name'],"r");

			if(!$fp) {
				// Aie : on n'arrive pas à ouvrir le fichier... Pas bon.
				echo "<p>Impossible d'ouvrir le fichier CSV !</p>\n";
				echo "<p><a href='prof_disc_classes.php'>Cliquer ici </a> pour recommencer !</p>\n";
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
					$ligne = fgets($fp, 4096);
					if(trim($ligne)!="") {

						$tabligne=explode(";",$ligne);

						// 0 : Login du prof
						// 1 : nom court de la matière
						// 2 : identifiant(s) de là (des) classe(s) (Format : 1S1!1S2!1S3)
						// 3 : type de groupe (CG || OPT)


						// On nettoie et on vérifie :
						$tabligne[0] = preg_replace("/[^A-Za-z0-9._]/","",trim(my_strtoupper($tabligne[0])));
						if (mb_strlen($tabligne[0]) > 50) $tabligne[0] = mb_substr($tabligne[0], 0, 50);
			
						$tabligne[1] = preg_replace("/[^A-Za-z0-9._\-]/","",trim(my_strtoupper($tabligne[1])));
						if (mb_strlen($tabligne[1]) > 50) $tabligne[1] = mb_substr($tabligne[1], 0, 50);
			
						$tabligne[2] = preg_replace("/[^A-Za-z0-9._ \-!]/","",trim($tabligne[2]));
						if (mb_strlen($tabligne[2]) > 2000) $tabligne[2] = mb_substr($tabligne[2], 0, 2000);
			
						$tabligne[3] = preg_replace("/[^A-Za-z]/","",trim(my_strtoupper($tabligne[3])));
						if ($tabligne[3] != "CG" AND $tabligne[3] != "OPT") $tabligne[3] = "";

						$data_tab[$k] = array();

						$data_tab[$k]["prof"] = $tabligne[0];
						$data_tab[$k]["matiere"] = $tabligne[1];
						$data_tab[$k]["classes"] = $tabligne[2];
						$data_tab[$k]["type"] = $tabligne[3];
					}
					$k++;
				}

				fclose($fp);

				// Fin de l'analyse du fichier.
				// Maintenant on va afficher tout ça.

				$nb_error=0;

				$sql="CREATE TABLE IF NOT EXISTS tempo4 ( col1 varchar(100) NOT NULL default '', col2 varchar(100) NOT NULL default '', col3 varchar(100) NOT NULL default '', col4 varchar(100) NOT NULL default '');";
				$res_tempo4=mysql_query($sql);

				$sql="TRUNCATE tempo4;";
				$res_tempo4=mysql_query($sql);

				echo "<form enctype='multipart/form-data' action='prof_disc_classes.php' method='post'>\n";
				echo add_token_field();
				echo "<input type='hidden' name='action' value='save_data' />\n";
				echo "<table border='1' class='boireaus' summary='Prof/matière/classe/type'>\n";
				echo "<tr><th>Login prof</th><th>Matière</th><th>Classe(s)</th><th>Type</th></tr>\n";

				$alt=1;
				for ($i=0;$i<$k-1;$i++) {
					$alt=$alt*(-1);
					echo "<tr class='lig$alt'>\n";
					echo "<td>\n";
					$sql="INSERT INTO tempo4 SET col1='".mysql_real_escape_string($data_tab[$i]["prof"])."',
					col2='".mysql_real_escape_string($data_tab[$i]["matiere"])."',
					col3='".mysql_real_escape_string($data_tab[$i]["classes"])."',
					col4='".mysql_real_escape_string($data_tab[$i]["type"])."';";
					$insert=mysql_query($sql);
					if(!$insert) {
						echo "<span style='color:red'>";
						echo $data_tab[$i]["prof"];
 						echo "</span>";
						$nb_error++;
					}
					else {
						echo $data_tab[$i]["prof"];
					}
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["matiere"];
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["classes"];
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["type"];
					echo "</td>\n";
					echo "</tr>\n";
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
			echo "<a href='prof_disc_classes.php'>Cliquer ici </a> pour recommencer !</p>\n";

		} else {
			echo "<p>Le fichier sélectionné n'est pas valide !<br />\n";
			echo "<a href='prof_disc_classes.php'>Cliquer ici </a> pour recommencer !</p>\n";
		}
	}
}
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
