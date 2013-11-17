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

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : Importation des matières";
require_once("../lib/header.inc.php");
//************** FIN EN-TETE ***************

$en_tete=isset($_POST['en_tete']) ? $_POST['en_tete'] : "no";

?>
<p class=bold><a href="index.php#eleves_options"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil initialisation</a></p>
<?php

echo "<center><h3 class='gepi'>Septième phase d'initialisation<br />Importation des associations élèves-options</h3></center>\n";


if (!isset($_POST["action"])) {
	//
	// On sélectionne le fichier à importer
	//

	echo "<p>Vous allez effectuer la septième et dernière étape : elle consiste à importer le fichier <b>g_eleves_options.csv</b> contenant les associations des élèves et des enseignements de type 'groupe', c'est-à-dire qui ne corerespondent pas à un enseignement de classe entière.\n";
	echo "<p style='margin-left:6em; text-indent:-6em;'><em>Remarque&nbsp;:</em> cette opération n'efface pas les classes. Elle ne fait qu'une mise à jour, le cas échéant, de la liste des matières.\n";
	echo "<p>Les champs suivants doivent être présents, dans l'ordre, et <b>séparés par un point-virgule</b> : \n";
	echo "<ul><li>Identifiant (<em>interne</em>) de l'élève</li>\n" .
			"<li>Identifiant(s) de matière (<em>séparés par un point d'exclamation</em>)</li>\n" .
			"</ul>\n";
	echo "<p style='margin-left:6em; text-indent:-6em;'><em>Remarque&nbsp;:</em> vous pouvez ne spécifier qu'une seule ligne par élève, en indiquant toutes les matières suivies dans le deuxième champ en séparant les identifiants de matières avec un point d'exclamation, mais vous pouvez également avoir une ligne pour une association simple, et avoir autant de lignes que d'enseignements suivis par l'élève.</p>\n";
	echo "<p>Veuillez préciser le nom complet du fichier <b>g_eleves_options.csv</b>.\n";

	echo "<form enctype='multipart/form-data' action='eleves_options.php' method='post'>\n";
	echo add_token_field();
	echo "<input type='hidden' name='action' value='upload_file' />\n";
	echo "<p><input type=\"file\" size=\"80\" name=\"csv_file\" /></p>\n";

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

		$sql="SELECT * FROM tempo2;";
		$res_temp=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_temp)==0) {
			echo "<p style='color:red'>ERREUR&nbsp;: Aucune matière n'a été trouvée&nbsp;???</p>\n";
			echo "<p><br /></p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		echo "<p><em>On remplit/complète la table 'j_eleves_groupes'&nbsp;:</em> ";

		$i = 0;
		// Compteur d'erreurs
		$error = 0;
		// Compteur d'enregistrement
		$total = 0;
		while ($lig=mysqli_fetch_object($res_temp)) {
			$reg_id_int = $lig->col1;
			$reg_options = $lig->col2;

			// On nettoie et on vérifie :
			$reg_id_int = preg_replace("/[^0-9]/","",trim($reg_id_int));
			if (mb_strlen($reg_id_int) > 50) $reg_id_int = mb_substr($reg_id_int, 0, 50);
			$reg_options = preg_replace("/[^A-Za-z0-9._\-!]/","",trim($reg_options));
			if (mb_strlen($reg_options) > 2000) $reg_options = mb_substr($reg_options, 0, 2000); // Juste pour éviter une tentative d'overflow...


			// Première étape : on s'assure que l'élève existe et on récupère son login... S'il n'existe pas, on laisse tomber.
			$test = mysqli_query($GLOBALS["mysqli"], "SELECT login FROM eleves WHERE elenoet = '" . $reg_id_int . "'");
			if (mysqli_num_rows($test) == 1) {
				$login_eleve = mysql_result($test, 0, "login");

				// Maintenant on récupère les différentes matières, et on vérifie qu'elles existent
				$reg_options = explode("!", $reg_options);
				$valid_options = array();
				foreach ($reg_options as $option) {
					$test = mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(matiere) FROM matieres WHERE matiere = '" . $option ."'"), 0);
					if ($test == 1) {
						$valid_options[] = $option;
					}
				}

				// On a maintenant un tableau avec les options que l'élève doit suivre.

				// On récupère la classe de l'élève.

				$test = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT(id_classe) FROM j_eleves_classes WHERE login = '" . $login_eleve . "'");

				if (mysqli_num_rows($test) != 0) {
					// L'élève fait bien parti d'une classe

					$id_classe = mysql_result($test, 0, "id_classe");

					// Maintenant on a tout : les options, la classe de l'élève, et son login
					// Enfin il reste quand même un truc à récupérer : le nombre de périodes :

					$num_periods = mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(num_periode) FROM periodes WHERE id_classe = '" . $id_classe . "'"), 0);

					// Bon cette fois c'est bon, on a tout. On va donc procéder de la manière suivante :
					// - on regarde s'il existe un groupe pour la classe dans la matière considérée
					// - on teste pour voir si l'élève n'est pas déjà inscrit dans ce groupe
					// - s'il ne l'est pas, on l'inscrit !
					// Simple, non ?

					// On procède matière par matière :

					foreach ($valid_options as $matiere) {
						$test = mysqli_query($GLOBALS["mysqli"], "SELECT jgc.id_groupe FROM j_groupes_classes jgc, j_groupes_matieres jgm WHERE (" .
								"jgc.id_classe = '" . $id_classe . "' AND " .
								"jgc.id_groupe = jgm.id_groupe AND " .
								"jgm.id_matiere = '" . $matiere . "')");
						if (mysqli_num_rows($test) > 0) {
							// Au moins un groupe existe, c'est bon signe
							// On passe groupe par groupe pour vérifier si l'élève est déjà inscrit ou pas
							for ($j=0;$j<mysqli_num_rows($test);$j++) {
								// On extrait l'ID du groupe
								$group_id = mysql_result($test, $j, "id_groupe");
								// On regarde si l'élève est inscrit
								$res = mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(login) FROM j_eleves_groupes WHERE (id_groupe = '" . $group_id . "' AND login = '" . $login_eleve . "')"), 0);
								if ($res == 0) {
									// L'élève ne fait pas encore parti du groupe. On l'inscrit pour les périodes de la classe
									for ($p=1;$p<=$num_periods;$p++) {
										// On enregistre
										$reg = mysqli_query($GLOBALS["mysqli"], "INSERT INTO j_eleves_groupes SET id_groupe = '" . $group_id . "', login = '" . $login_eleve . "', periode = '" . $p . "'");
										if (!$reg) {
											$error++;
											echo "<span style='color:red'>".((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)).'<span><br />';
										} else {
											if ($p == 1) $total++;
										}
									}
								}
							}
						}
					}
				}
			}

			$i++;
		}

		if ($error > 0) {echo "<p><font color='red'>Il y a eu " . $error . " erreurs.</font></p>\n";}
		if ($total > 0) {echo "<p>" . $total . " associations élèves-options ont été enregistrées.</p>\n";}
		if(($error==0)&&($total==0)) {echo "<p>Aucune association élève-option n'a été enregistrée???</p>\n";}

		echo "<p><a href='index.php#eleves_options'>Revenir à la page précédente</a></p>\n";


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
		if(my_strtolower($csv_file['name']) == "g_eleves_options.csv") {

			// Le nom est ok. On ouvre le fichier
			$fp=fopen($csv_file['tmp_name'],"r");

			if(!$fp) {
				// Aie : on n'arrive pas à ouvrir le fichier... Pas bon.
				echo "<p>Impossible d'ouvrir le fichier CSV !</p>";
				echo "<p><a href='eleves_options.php'>Cliquer ici </a> pour recommencer !</center></p>\n";
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

						// 0 : ID interne de l'élève
						// 1 : Identifiants de matières


						// On nettoie et on vérifie :
						$tabligne[0] = preg_replace("/[^0-9]/","",trim($tabligne[0]));
						if (mb_strlen($tabligne[0]) > 50) $tabligne[0] = mb_substr($tabligne[0], 0, 50);

						if(!isset($tabligne[1])) {$tabligne[1]="";}
						$tabligne[1] = preg_replace("/[^A-Za-z0-9._\-!]/","",trim($tabligne[1]));
						if (mb_strlen($tabligne[1]) > 2000) $tabligne[1] = mb_substr($tabligne[1], 0, 2000);

						$data_tab[$k] = array();

						$data_tab[$k]["id_int"] = $tabligne[0];
						$data_tab[$k]["options"] = $tabligne[1];

						$k++;
					}
				}

				fclose($fp);

				// Fin de l'analyse du fichier.
				// Maintenant on va afficher tout ça.

				$sql="TRUNCATE TABLE tempo2;";
				$vide_table = mysqli_query($GLOBALS["mysqli"], $sql);

				$nb_error=0;

				echo "<form enctype='multipart/form-data' action='eleves_options.php' method='post'>\n";
				echo add_token_field();
				echo "<input type='hidden' name='action' value='save_data' />\n";
				echo "<table class='boireaus' summary='Elèves/options'>\n";
				echo "<tr><th>ID interne de l'élève</th><th>Matières</th></tr>\n";

				$alt=1;
				for ($i=0;$i<$k;$i++) {
					if(isset($data_tab[$i]["id_int"])) {
						$alt=$alt*(-1);
						echo "<tr class='lig$alt'>\n";
						echo "<td>\n";
						$sql="INSERT INTO tempo2 SET col1='".$data_tab[$i]["id_int"]."',
						col2='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $data_tab[$i]["options"]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."';";
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
						echo $data_tab[$i]["options"];
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
			echo "<a href='eleves_options.php'>Cliquer ici </a> pour recommencer !</p>\n";

		} else {
			echo "<p>Le fichier sélectionné n'est pas valide !<br />\n";
			echo "<a href='eleves_options.php'>Cliquer ici </a> pour recommencer !</p>\n";
		}
	}
}
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
