<?php
@set_time_limit(0);
/*
* $Id$
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

$liste_tables_del = array(
//absences
//absences_creneaux
//absences_eleves
//absences_gep
//absences_motifs
//aid
//aid_appreciations
//aid_config
//avis_conseil_classe
//classes
//cn_cahier_notes
//cn_conteneurs
//cn_devoirs
//cn_notes_conteneurs
//cn_notes_devoirs
//ct_devoirs_entry
//ct_documents
//ct_entry
//ct_types_documents
//droits
//eleves
//eleves_groupes_settings
//etablissements
//groupes
//j_aid_eleves
//j_aid_utilisateurs
"j_eleves_classes",
//j_eleves_cpe
//j_eleves_etablissements
//j_eleves_groupes
//j_eleves_professeurs
//j_eleves_regime
//j_groupes_classes
//j_groupes_matieres
//j_groupes_professeurs
//j_professeurs_matieres
//log
//matieres
//matieres_appreciations
//matieres_notes
//messages
//periodes
//responsables
//setting
//suivi_eleve_cpe
//tempo
//tempo2
//temp_gep_import
//utilisateurs
);

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : Importation des matières";
require_once("../lib/header.inc");
//************** FIN EN-TETE ***************

$en_tete=isset($_POST['en_tete']) ? $_POST['en_tete'] : "no";

?>
<p class=bold><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil initialisation</a></p>
<?php

echo "<center><h3 class='gepi'>Cinquième phase d'initialisation<br />Importation des associations élèves-classes</h3></center>";


if (!isset($_POST["action"])) {
	//
	// On sélectionne le fichier à importer
	//

	echo "<p>Vous allez effectuer la cinquième étape : elle consiste à importer le fichier <b>g_eleves_classes.csv</b> contenant les données relatives aux disciplines.</p>\n";
	echo "<p>Remarque : cette opération n'efface par les classes. Elle ne fait qu'une mise à jour, le cas échéant, de la liste des matières.</p>\n";
	echo "<p>Les champs suivants doivent être présents, dans l'ordre, et <b>séparés par un point-virgule</b> : </p>\n";
	echo "<ul><li>Identifiant (interne) de l'élève</li>\n" .
			"<li>Identifiant court de la classe (ex: 1S2)</li>\n" .
			"</ul>\n";
	echo "<p>Veuillez préciser le nom complet du fichier <b>g_eleves_classes.csv</b>.</p>\n";
	echo "<form enctype='multipart/form-data' action='eleves_classes.php' method='post'>\n";
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
		//
		// On enregistre les données dans la base.
		// Le fichier a déjà été affiché, et l'utilisateur est sûr de vouloir enregistrer
		//

		$j=0;
		while ($j < count($liste_tables_del)) {
			if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
				$del = @mysql_query("DELETE FROM $liste_tables_del[$j]");
			}
			$j++;
		}


		$go = true;
		$i = 0;
		// Compteur d'erreurs
		$error = 0;
		// Compteur d'enregistrement
		$total = 0;
		while ($go) {

			$reg_id_int = $_POST["ligne".$i."_id_int"];
			$reg_classe = $_POST["ligne".$i."_classe"];

			// On nettoie et on vérifie :
			$reg_id_int = preg_replace("/[^0-9]/","",trim($reg_id_int));
			if (strlen($reg_id_int) > 50) $reg_id_int = substr($reg_id_int, 0, 50);
			$reg_classe = preg_replace("/[^A-Za-z0-9.\-]/","",trim($reg_classe));
			if (strlen($reg_classe) > 100) $reg_classe = substr($reg_classe, 0, 100);


			if(($reg_id_int!='')&&($reg_classe!='')){
				// Première étape : on s'assure que l'élève existe et on récupère son login... S'il n'existe pas, on laisse tomber.
				$test = mysql_query("SELECT login FROM eleves WHERE elenoet = '" . $reg_id_int . "'");
				if (mysql_num_rows($test) == 1) {
					$login_eleve = mysql_result($test, 0, "login");

					// Maintenant que tout est propre et que l'élève existe, on fait un test sur la table pour voir si la classe existe

					$test = mysql_query("SELECT id FROM classes WHERE classe = '" . $reg_classe . "'");

					if (mysql_num_rows($test) == 0) {
						// Test négatif : aucune classe avec ce nom court... on créé !

						$insert1 = mysql_query("INSERT INTO classes SET " .
								"classe = '" . $reg_classe . "', " .
								"nom_complet = '" . $reg_classe . "', " .
								"format_nom = 'np', " .
								"display_rang = 'n', " .
								"display_address = 'n', " .
								"display_coef = 'y'");
						// On récupère l'ID de la classe nouvelle créée, pour enregistrer les périodes
						$classe_id = mysql_insert_id();

						for ($p=1;$p<4;$p++) {
							if ($p == 1) $v = "O";
								else $v = "N";
							$insert2 = mysql_query("INSERT INTO periodes SET " .
									"nom_periode = 'Période ".$p . "', " .
									"num_periode = '" . $p . "', " .
									"verouiller = '" . $v . "', " .
									"id_classe = '" . $classe_id . "'");
						}
						$num_periods = 3;

					} else {
						// La classe existe
						// On récupère son ID
						$classe_id = mysql_result($test, 0, "id");
						$num_periods = mysql_result(mysql_query("SELECT count(num_periode) FROM periodes WHERE id_classe = '" . $classe_id . "'"), 0);
					}

					// Maintenant qu'on a l'ID de la classe et le nombre de périodes, on enregistre l'association

					for ($p=1;$p<=$num_periods;$p++) {
						$insert = mysql_query("INSERT INTO j_eleves_classes SET login = '" . $login_eleve . "', " .
																	"id_classe = '" . $classe_id . "', " .
																	"periode = '" . $p . "'");
					}

					if (!$insert) {
						$error++;
						echo mysql_error();
					} else {
						$total++;
					}

				}
			}

			$i++;
			if (!isset($_POST['ligne'.$i.'_id_int'])) $go = false;
		}

		if ($error > 0) echo "<p><font color='red'>Il y a eu " . $error . " erreurs.</font></p>\n";
		if ($total > 0) echo "<p>" . $total . " associations eleves-classes ont été enregistrés.</p>\n";

		echo "<p><a href='index.php'>Revenir à la page précédente</a></p>\n";


	} else if ($_POST['action'] == "upload_file") {
		//
		// Le fichier vient d'être envoyé et doit être traité
		// On va donc afficher le contenu du fichier tel qu'il va être enregistré dans Gepi
		// en proposant des champs de saisie pour modifier les données si on le souhaite
		//

		$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;

		// On vérifie le nom du fichier... Ce n'est pas fondamentalement indispensable, mais
		// autant forcer l'utilisateur à être rigoureux
		if(strtolower($csv_file['name']) == "g_eleves_classes.csv") {

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
					$ligne = fgets($fp, 4096);
					if(trim($ligne)!="") {

						$tabligne=explode(";",$ligne);

						// 0 : ID interne de l'élève
						// 1 : nom court de la classe


						// On nettoie et on vérifie :
						$tabligne[0] = preg_replace("/[^0-9]/","",trim($tabligne[0]));
						if (strlen($tabligne[0]) > 50) $tabligne[0] = substr($tabligne[0], 0, 50);
						$tabligne[1] = preg_replace("/[^A-Za-z0-9 .\-éèüëïäê]/","",trim($tabligne[1]));
						if (strlen($tabligne[1]) > 100) $tabligne[1] = substr($tabligne[1], 0, 100);


						$data_tab[$k] = array();



						$data_tab[$k]["id_int"] = $tabligne[0];
						$data_tab[$k]["classe"] = $tabligne[1];

						$k++;

					}
					//$k++;
				}

				fclose($fp);

				// Fin de l'analyse du fichier.
				// Maintenant on va afficher tout ça.

				echo "<form enctype='multipart/form-data' action='eleves_classes.php' method='post'>\n";
				echo "<input type='hidden' name='action' value='save_data' />\n";
				echo "<table border='1' class='boireaus' summary='Tableau élèves/classes'>\n";
				echo "<tr><th>ID interne de l'élève</th><th>Classe</th></tr>\n";

				$alt=1;
				for ($i=0;$i<$k-1;$i++) {
					$alt=$alt*(-1);
                    echo "<tr class='lig$alt'>\n";
					echo "<td>\n";
					echo $data_tab[$i]["id_int"];
					echo "<input type='hidden' name='ligne".$i."_id_int' value='" . $data_tab[$i]["id_int"] . "' />\n";
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["classe"];
					echo "<input type='hidden' name='ligne".$i."_classe' value='" . $data_tab[$i]["classe"] . "' />\n";
					echo "</td>\n";
					echo "</tr>\n";
				}

				echo "</table>\n";

				echo "<input type='submit' value='Enregistrer' />\n";

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