<?php
@set_time_limit(0);
/*
* Last modification  : 15/09/2006
*
* Copyright 2001, 2006 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
};

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : Importation des matières";
require_once("../lib/header.inc");
//************** FIN EN-TETE ***************
?>
<p class=bold><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil initialisation</a></p>
<?php

echo "<center><h3 class='gepi'>Cinquième phase d'initialisation<br />Importation des associations élèves-options</h3></center>";


if (!isset($_POST["action"])) {
	//
	// On sélectionne le fichier à importer
	//	
	
	echo "<p>Vous allez effectuer la cinquième et dernière étape : elle consiste à importer le fichier <b>eleves_options.csv</b> contenant les associations des élèves et des enseignements de type 'groupe', c'est-à-dire qui ne corerespondent pas à un enseignement de classe entière.";
	echo "<p>Les champs suivants doivent être présents, dans l'ordre, et <b>séparés par un point-virgule</b> : ";
	echo "<ul><li>Login de l'élève</li>" .
			"<li>Identifiant(s) de matière (séparés par un point d'exclamation)</li>" .
			"</ul>";
	echo "<p>Remarque : vous pouvez ne spécifier qu'une seule ligne par élève, en indiquant toutes les matières suivies dans le deuxième champ en séparant les identifiants de matières avec un point d'exclamation, mais vous pouvez également avoir une ligne pour une association simple, et avoir autant de lignes que d'enseignements suivis par l'élève.</p>";
	echo "<p>Veuillez préciser le nom complet du fichier <b>eleves_options.csv</b>.";
	echo "<form enctype='multipart/form-data' action='eleves_options.php' method='post'>";
	echo "<input type='hidden' name='action' value='upload_file' />";
	echo "<p><input type=\"file\" size=\"80\" name=\"csv_file\" />";
	echo "<p><input type='submit' value='Valider' />";
	echo "</form>";

} else {
	//
	// Quelque chose a été posté
	//
	if ($_POST['action'] == "save_data") {
		//
		// On enregistre les données dans la base.
		// Le fichier a déjà été affiché, et l'utilisateur est sûr de vouloir enregistrer
		//

		$go = true;
		$i = 0;
		// Compteur d'erreurs
		$error = 0;
		// Compteur d'enregistrement
		$total = 0;
		while ($go) {
		
			$reg_login = $_POST["ligne".$i."_login"];
			$reg_options = $_POST["ligne".$i."_options"];
			
			// On nettoie et on vérifie :
			$reg_login = preg_replace("/[^0-9A-Za-z_\.\-]/","",trim($reg_login));
			$reg_login = preg_replace("/\./", "_", $reg_login);
			$reg_options = preg_replace("/[^A-Za-z0-9.\-!]/","",trim($reg_options));
			if (strlen($reg_options) > 2000) $reg_options = substr($reg_options, 0, 2000); // Juste pour éviter une tentative d'overflow...


			// Première étape : on s'assure que l'élève existe... S'il n'existe pas, on laisse tomber.
			$test = mysql_query("SELECT login FROM eleves WHERE login = '" . $reg_login . "'");
			if (mysql_num_rows($test) == 1) {
				$login_eleve = $reg_login;
				
				// Maintenant on récupère les différentes matières, et on vérifie qu'elles existent
				$reg_options = explode("!", $reg_options);
				$valid_options = array();
				foreach ($reg_options as $option) {
					$option = strtoupper($option);
					$test = mysql_result(mysql_query("SELECT count(matiere) FROM matieres WHERE matiere = '" . $option ."'"), 0);
					if ($test == 1) {
						$valid_options[] = $option;
					}
				}
				
				// On a maintenant un tableau avec les options que l'élève doit suivre.
				
				// On récupère la classe de l'élève.
				
				$test = mysql_query("SELECT DISTINCT(id_classe) FROM j_eleves_classes WHERE login = '" . $login_eleve . "'");
				
				if (mysql_num_rows($test) != 0) {
					// L'élève fait bien parti d'une classe
	
					$id_classe = mysql_result($test, 0, "id_classe");
					
					// Maintenant on a tout : les options, la classe de l'élève, et son login
					// Enfin il reste quand même un truc à récupérer : le nombre de périodes :
					
					$num_periods = mysql_result(mysql_query("SELECT count(num_periode) FROM periodes WHERE id_classe = '" . $id_classe . "'"), 0);
					
					// Bon cette fois c'est bon, on a tout. On va donc procéder de la manière suivante :
					// - on regarde s'il existe un groupe pour la classe dans la matière considérée
					// - on teste pour voir si l'élève n'est pas déjà inscrit dans ce groupe
					// - s'il ne l'est pas, on l'inscrit !
					// Simple, non ?
					
					// On procède matière par matière :
					
					foreach ($valid_options as $matiere) {
						$test = mysql_query("SELECT jgc.id_groupe FROM j_groupes_classes jgc, j_groupes_matieres jgm WHERE (" .
								"jgc.id_classe = '" . $id_classe . "' AND " .
								"jgc.id_groupe = jgm.id_groupe AND " .
								"jgm.id_matiere = '" . $matiere . "')");
						if (mysql_num_rows($test) > 0) {
							// Au moins un groupe existe, c'est bon signe
							// On passe groupe par groupe pour vérifier si l'élève est déjà inscrit ou pas
							// Si plusieurs groupes existent, l'élève sera inscrit à tous les groupes...
							for ($j=0;$j<mysql_num_rows($test);$j++) {
								// On extrait l'ID du groupe
								$group_id = mysql_result($test, $j, "id_groupe");
								// On regarde si l'élève est inscrit
								$res = mysql_result(mysql_query("SELECT count(login) FROM j_eleves_groupes WHERE (id_groupe = '" . $group_id . "' AND login = '" . $login_eleve . "')"), 0);
								if ($res == 0) {
									// L'élève ne fait pas encore parti du groupe. On l'inscrit pour les périodes de la classe
									for ($p=1;$p<=$num_periods;$p++) {
										// On enregistre
										$reg = mysql_query("INSERT INTO j_eleves_groupes SET id_groupe = '" . $group_id . "', login = '" . $login_eleve . "', periode = '" . $p . "'");
										if (!$reg) {
											$error++;
											echo mysql_error();
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
			if (!isset($_POST['ligne'.$i.'_login'])) $go = false;	
		}
		
		if ($error > 0) echo "<p><font color=red>Il y a eu " . $error . " erreurs.</font></p>";
		if ($total > 0) echo "<p>" . $total . " associations élèves-options ont été enregistrées.</p>";
		
		echo "<p><a href='index.php'>Revenir à la page précédente</a></p>";		
		
	
	} else if ($_POST['action'] == "upload_file") {
		//
		// Le fichier vient d'être envoyé et doit être traité
		// On va donc afficher le contenu du fichier tel qu'il va être enregistré dans Gepi
		// en proposant des champs de saisie pour modifier les données si on le souhaite
		//

		$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;

		// On vérifie le nom du fichier... Ce n'est pas fondamentalement indispensable, mais
		// autant forcer l'utilisateur à être rigoureux
		if(strtolower($csv_file['name']) == "eleves_options.csv") {
			
			// Le nom est ok. On ouvre le fichier
			$fp=fopen($csv_file['tmp_name'],"r");
	
			if(!$fp) {
				// Aie : on n'arrive pas à ouvrir le fichier... Pas bon.
				echo "<p>Impossible d'ouvrir le fichier CSV !</p>";
				echo "<p><a href='eleves_options.php'>Cliquer ici </a> pour recommencer !</center></p>";
			} else {
				
				// Fichier ouvert ! On attaque le traitement
				
				// On va stocker toutes les infos dans un tableau
				// Une ligne du CSV pour une entrée du tableau
				$data_tab = array();
	
				//=========================
				// On lit une ligne pour passer la ligne d'entête:
				$ligne = fgets($fp, 4096);
				//=========================
				
					$k = 0;
					while (!feof($fp)) {
						$ligne = fgets($fp, 4096);
						if(trim($ligne)!="") {

							$tabligne=explode(";",$ligne);

							// 0 : Login de l'élève
							// 1 : Identifiants de matières
							

							// On nettoie et on vérifie :
							$tabligne[0] = preg_replace("/[^0-9A-Za-z_\.\-]/","",trim($tabligne[0]));
							if (strlen($tabligne[0]) > 50) $tabligne[0] = substr($tabligne[0], 0, 50);
							$tabligne[1] = preg_replace("/[^A-Za-z0-9\.\-!]/","",trim($tabligne[1]));
							if (strlen($tabligne[1]) > 2000) $tabligne[1] = substr($tabligne[1], 0, 2000);

							
							$data_tab[$k] = array();
							
							

							$data_tab[$k]["login"] = $tabligne[0];
							$data_tab[$k]["options"] = $tabligne[1];

						}
					$k++;
					}

				fclose($fp);
				
				// Fin de l'analyse du fichier.
				// Maintenant on va afficher tout ça.
				
				echo "<form enctype='multipart/form-data' action='eleves_options.php' method='post'>";
				echo "<input type='hidden' name='action' value='save_data' />";
				echo "<table>";
				echo "<tr><td>Login de l'élève</td><td>Matières</td></tr>";
				
				for ($i=0;$i<$k-1;$i++) {
					echo "<tr>";
					echo "<td>";
					echo $data_tab[$i]["login"];
					echo "<input type='hidden' name='ligne".$i."_login' value='" . $data_tab[$i]["login"] . "'>";
					echo "</td>";
					echo "<td>";
					echo strtoupper($data_tab[$i]["options"]);
					echo "<input type='hidden' name='ligne".$i."_options' value='" . $data_tab[$i]["options"] . "'>";
					echo "</td>";
					echo "</tr>";
				}
				
				echo "</table>";
				
				echo "<input type='submit' value='Enregistrer'>";

				echo "</form>";
			}

		} else if (trim($csv_file['name'])=='') {
	
			echo "<p>Aucun fichier n'a été sélectionné !<br />";
			echo "<a href='eleves_options.php'>Cliquer ici </a> pour recommencer !</center></p>";
	
		} else {
			echo "<p>Le fichier sélectionné n'est pas valide !<br />";
			echo "<a href='eleves_options.php'>Cliquer ici </a> pour recommencer !</center></p>";
		}
	}
}
require("../lib/footer.inc.php");
?>