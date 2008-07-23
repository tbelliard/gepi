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

$liste_tables_del = array(
//"absences",
//"absences_gep",
//"aid",
//"aid_appreciations",
//"aid_config",
//"avis_conseil_classe",
//"classes",
//"droits",
//"eleves",
"responsables",
"responsables2",
"resp_pers",
"resp_adr"
//"etablissements",
//"j_aid_eleves",
//"j_aid_utilisateurs",
//"j_eleves_classes",
//"j_eleves_etablissements",
//"j_eleves_professeurs",
//"j_eleves_regime",
//"j_eleves_groupes",
//"j_professeurs_matieres",
//"log",
//"matieres",
//"matieres_appreciations",
//"matieres_notes",
//"periodes",
//"tempo2",
//"temp_gep_import",
//"tempo",
//"utilisateurs",
//"cn_cahier_notes",
//"cn_conteneurs",
//"cn_devoirs",
//"cn_notes_conteneurs",
//"cn_notes_devoirs",
//"setting"
);




//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : Importation des élèves - Etape 1";
require_once("../lib/header.inc");
//************** FIN EN-TETE ***************

$en_tete=isset($_POST['en_tete']) ? $_POST['en_tete'] : "no";

?>
<p class=bold><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil initialisation</a></p>
<?php

echo "<center><h3 class='gepi'>Première phase d'initialisation<br />Importation des responsables d'élèves</h3></center>";


if (!isset($_POST["action"])) {
	//
	// On sélectionne le fichier à importer
	//

	echo "<p>Vous allez effectuer la deuxième étape : elle consiste à importer le fichier <b>g_responsables.csv</b> contenant les données élèves.</p>\n";
	echo "<p>Les champs suivants doivent être présents, dans l'ordre, et <b>séparés par un point-virgule</b> :</p>\n";
	echo "<ul><li>Identifiant élève interne à l'établissement (n°, et non login) <b>(*)</b></li>\n" .
			"<li>Nom du responsable <b>(*)</b></li>\n" .
			"<li>Prénom</li>\n" .
			"<li>Civilité</li>\n" .
			"<li>Ligne 1 adresse</li>\n" .
			"<li>Ligne 2 adresse</li>\n" .
			"<li>Code postal</li>\n" .
			"<li>Commune</li>\n" .
			"</ul>\n";
	echo "<p>Veuillez préciser le nom complet du fichier <b>g_responsables.csv</b>.</p>\n";
	echo "<form enctype='multipart/form-data' action='responsables.php' method='post'>\n";
	echo "<input type='hidden' name='action' value='upload_file' />\n";
	echo "<p><input type=\"file\" size=\"80\" name=\"csv_file\" />\n";
    echo "<p><label for='en_tete' style='cursor:pointer;'>Si le fichier à importer comporte une première ligne d'en-tête (non vide) à ignorer, <br />cocher la case ci-contre</label>&nbsp;<input type='checkbox' name='en_tete' id='en_tete' value='yes' checked /></p>\n";
	echo "<p><input type='submit' value='Valider' />\n";
	echo "</form>\n";

	echo "<p><i>NOTE:</i> Les champs marqués d'un <b>(*)</b> doivent être non vides.</p>\n";
} else {
	//
	// Quelque chose a été posté
	//
	if ($_POST['action'] == "save_data") {
		//
		// On enregistre les données dans la base.
		// Le fichier a déjà été affiché, et l'utilisateur est sûr de vouloir enregistrer
		//

		// Première étape : on vide les tables

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

			$reg_id_eleve = $_POST["ligne".$i."_id_eleve"];
			$reg_nom = $_POST["ligne".$i."_nom"];
			$reg_prenom = $_POST["ligne".$i."_prenom"];
			$reg_civilite = $_POST["ligne".$i."_civilite"];
			$reg_adresse1 = $_POST["ligne".$i."_adresse1"];
			$reg_adresse2 = $_POST["ligne".$i."_adresse2"];
			$reg_code_postal = $_POST["ligne".$i."_code_postal"];
			$reg_commune = $_POST["ligne".$i."_commune"];

			// On nettoie et on vérifie :
			$reg_id_eleve = preg_replace("/[^0-9]/","",trim($reg_id_eleve));

			//$reg_nom = preg_replace("/[^A-Za-z .\-]/","",trim(strtoupper($reg_nom)));
			$reg_nom = ereg_replace("Æ","AE",ereg_replace("æ","ae",ereg_replace("¼","OE",ereg_replace("½","oe",preg_replace("/[^A-Za-z .\-àâäéèêëîïôöùûüçÀÄÂÉÈÊËÎÏÔÖÙÛÜÇ]/","",trim(strtoupper($reg_nom)))))));
			if (strlen($reg_nom) > 50) $reg_nom = substr($reg_nom, 0, 50);
			//$reg_prenom = preg_replace("/[^A-Za-z .\-éèüëïäê]/","",trim($reg_prenom));
			$reg_prenom = ereg_replace("Æ","AE",ereg_replace("æ","ae",ereg_replace("¼","OE",ereg_replace("½","oe",preg_replace("/[^A-Za-z .\-àâäéèêëîïôöùûüçÀÄÂÉÈÊËÎÏÔÖÙÛÜÇ]/","",trim($reg_prenom))))));

			// ÂÄÀÁÃÄÅÇÊËÈÉÎÏÌÍÑÔÖÒÓÕ¦ÛÜÙÚÝ¾´áàâäãåçéèêëîïìíñôöðòóõ¨ûüùúýÿ¸

			if (strlen($reg_prenom) > 50) $reg_prenom = substr($reg_prenom, 0, 50);

			//if ($reg_civilite != "M." AND $reg_civilite != "MME" AND $reg_civilite != "MLLE") $reg_civilite = "M.";
			if ($reg_civilite != "M." AND $reg_civilite != "MME" AND $reg_civilite != "MLLE") { $reg_civilite = "";}

			//$reg_adresse1 = preg_replace("/[^A-Za-z0-9 .\-éèüëïäê]/","",trim($reg_adresse1));
			$reg_adresse1 = ereg_replace("Æ","AE",ereg_replace("æ","ae",ereg_replace("¼","OE",ereg_replace("½","oe",preg_replace("/[^A-Za-z0-9 .\-àâäéèêëîïôöùûüçÀÄÂÉÈÊËÎÏÔÖÙÛÜÇ]/","",trim(strtr($reg_adresse1,"'"," ")))))));

			if (strlen($reg_adresse1) > 50) $reg_adresse1 = substr($reg_adresse1, 0, 50);

			//$reg_adresse2 = preg_replace("/[^A-Za-z0-9 .\-éèüëïäê]/","",trim($reg_adresse2));
			$reg_adresse2 = ereg_replace("Æ","AE",ereg_replace("æ","ae",ereg_replace("¼","OE",ereg_replace("½","oe",preg_replace("/[^A-Za-z0-9 .\-àâäéèêëîïôöùûüçÀÄÂÉÈÊËÎÏÔÖÙÛÜÇ]/","",trim(strtr($reg_adresse2,"'"," ")))))));
			if (strlen($reg_adresse2) > 50) $reg_adresse2 = substr($reg_adresse2, 0, 50);

			$reg_code_postal = preg_replace("/[^0-9]/","",trim($reg_code_postal));
			if (strlen($reg_code_postal) > 6) $reg_code_postal = substr($reg_code_postal, 0, 6);

			//$reg_commune = preg_replace("/[^A-Za-z0-9 .\-éèüëïäê]/","",trim($reg_commune));
			$reg_commune = ereg_replace("Æ","AE",ereg_replace("æ","ae",ereg_replace("¼","OE",ereg_replace("½","oe",preg_replace("/[^A-Za-z0-9 .\-àâäéèêëîïôöùûüçÀÄÂÉÈÊËÎÏÔÖÙÛÜÇ]/","",trim(strtr($reg_commune,"'"," ")))))));
			if (strlen($reg_commune) > 50) $reg_commune = substr($reg_commune, 0, 50);

			// On vérifie que l'élève existe
			$test = mysql_result(mysql_query("SELECT count(login) FROM eleves WHERE elenoet = '" . $reg_id_eleve . "'"), 0);

			if($reg_id_eleve==""){
				echo "<p>Erreur : L'identifiant élève est vide pour $reg_prenom $reg_nom</p>\n";
			}
			else{
				if($reg_nom==""){
					echo "<p>Erreur : Le nom du responsable est vide pour l'élève $reg_id_eleve</p>\n";
				}
				else{
					if ($test == 0 OR !$test) {
						// Test négatif : aucun élève avec cet ID... On envoie un message d'erreur.
						echo "<p>Erreur : l'élève avec l'identifiant interne " . $reg_id_eleve . " n'existe pas dans Gepi.</p>\n";
					} else {
						// Test positif : on peut donc enregistrer les données de responsable.

						// On regarde si une entrée existe déjà pour l'élève en question
						$test = mysql_query("SELECT ereno, nom1, nom2 FROM responsables WHERE ereno = '" . $reg_id_eleve . "'");
						$insert = null;

						if (mysql_num_rows($test) == 0) {
							// Aucune entrée n'existe. On enregistre le responsable comme premier responsable

							$sql="INSERT INTO responsables SET " .
								"ereno = '" . $reg_id_eleve . "', " .
								"nom1 = '" . $reg_nom . "', " .
								"prenom1 = '" . $reg_prenom . "', " .
								"adr1 = '" . $reg_adresse1 . "', " .
								"adr1_comp = '" . $reg_adresse2 . "', " .
								"commune1 = '" . $reg_commune . "', " .
								"cp1 = '" . $reg_code_postal . "'";
							$insert = mysql_query($sql);

						} else {
							// Une entrée existe
							// On regarde si le responsable 1 a déjà été saisi
							if (mysql_result($test, 0, "nom1") == "") {
								$sql="UPDATE responsables SET " .
									"nom1 = '" . $reg_nom . "', " .
									"prenom1 = '" . $reg_prenom . "', " .
									"adr1 = '" . $reg_adresse1 . "', " .
									"adr1_comp = '" . $reg_adresse2 . "', " .
									"commune1 = '" . $reg_commune . "', " .
									"cp1 = '" . $reg_code_postal . "' " .
									"WHERE " .
									"ereno = '" . $reg_id_eleve . "'";
								$insert = mysql_query($sql);

							} else if (mysql_result($test, 0, "nom2") == "") {
								$sql="UPDATE responsables SET " .
									"nom2 = '" . $reg_nom . "', " .
									"prenom2 = '" . $reg_prenom . "', " .
									"adr2 = '" . $reg_adresse1 . "', " .
									"adr2_comp = '" . $reg_adresse2 . "', " .
									"commune2 = '" . $reg_commune . "', " .
									"cp2 = '" . $reg_code_postal . "' " .
									"WHERE " .
									"ereno = '" . $reg_id_eleve . "'";
								$insert = mysql_query($sql);

							} else {
								// Erreur ! Les deux responsables ont déjà été saisis...
								echo "<p>Erreur pour " . $reg_prenom . " " . $reg_nom . " ! Les deux responsables ont déjà été saisis.</p>\n";
							}


						}

						if ($insert == false) {
							$error++;
							$erreur_mysql=mysql_error();
							if($erreur_mysql!=""){echo "<p><font color='red'>".$erreur_mysql."</font></p>\n";}
							//echo "<p>$sql</p>\n";
						} else {
							$total++;
						}
					}
				}
			}

			$i++;
			if (!isset($_POST['ligne'.$i.'_nom'])) $go = false;
		}

		if ($error > 0) echo "<p><font color='red'>Il y a eu " . $error . " erreurs.</font></p>\n";
		if ($total > 0) echo "<p>" . $total . " responsables ont été enregistrés.</p>\n";

		echo "<p><a href='index.php'>Revenir à la page précédente</a></p>\n";

		// On sauvegarde le témoin du fait qu'il va falloir convertir pour remplir les nouvelles tables responsables:
		saveSetting("conv_new_resp_table", 0);

	} else if ($_POST['action'] == "upload_file") {
		//
		// Le fichier vient d'être envoyé et doit être traité
		// On va donc afficher le contenu du fichier tel qu'il va être enregistré dans Gepi
		// en proposant des champs de saisie pour modifier les données si on le souhaite
		//

		$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;

		// On vérifie le nom du fichier... Ce n'est pas fondamentalement indispensable, mais
		// autant forcer l'utilisateur à être rigoureux
		if(strtolower($csv_file['name']) == "g_responsables.csv") {

			// Le nom est ok. On ouvre le fichier
			$fp=fopen($csv_file['tmp_name'],"r");

			if(!$fp) {
				// Aie : on n'arrive pas à ouvrir le fichier... Pas bon.
				echo "<p>Impossible d'ouvrir le fichier CSV !</p>\n";
				echo "<p><a href='responsables.php'>Cliquer ici </a> pour recommencer !</p>\n";
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

						// 0 : Identifiant interne élève
						// 1 : Nom
						// 2 : Prénom
						// 3 : Civilité
						// 4 : Ligne 1 adresse
						// 5 : Ligne 2 adresse
						// 6 : Code postal
						// 7 : Commune

							// On nettoie et on vérifie :
						$tabligne[0] = preg_replace("/[^0-9]/","",trim($tabligne[0]));

						//$tabligne[1] = preg_replace("/[^A-Za-z .\-]/","",trim(strtoupper($tabligne[1])));
						$tabligne[1] = ereg_replace("Æ","AE",ereg_replace("æ","ae",ereg_replace("¼","OE",ereg_replace("½","oe",preg_replace("/[^A-Za-z .\-àâäéèêëîïôöùûüçÀÄÂÉÈÊËÎÏÔÖÙÛÜÇ]/","",trim(strtoupper($tabligne[1])))))));
						if (strlen($tabligne[1]) > 50) $tabligne[1] = substr($tabligne[1], 0, 50);

						//$tabligne[2] = preg_replace("/[^A-Za-z .\-éèüëïäê]/","",trim($tabligne[2]));
						$tabligne[2] = ereg_replace("Æ","AE",ereg_replace("æ","ae",ereg_replace("¼","OE",ereg_replace("½","oe",preg_replace("/[^A-Za-z .\-àâäéèêëîïôöùûüçÀÄÂÉÈÊËÎÏÔÖÙÛÜÇ]/","",trim($tabligne[2]))))));
						if (strlen($tabligne[2]) > 50) $tabligne[2] = substr($tabligne[2], 0, 50);

						//if ($tabligne[3] != "M." AND $tabligne[3] != "MME" AND $tabligne[3] != "MLLE") $tabligne[3] = "M.";
						if ($tabligne[3] != "M." AND $tabligne[3] != "MME" AND $tabligne[3] != "MLLE") { $tabligne[3] = "";}

						//$tabligne[4] = preg_replace("/[^A-Za-z0-9 .\-éèüëïäê]/","",trim($tabligne[4]));
						$tabligne[4] = ereg_replace("Æ","AE",ereg_replace("æ","ae",ereg_replace("¼","OE",ereg_replace("½","oe",preg_replace("/[^A-Za-z0-9 .\-àâäéèêëîïôöùûüçÀÄÂÉÈÊËÎÏÔÖÙÛÜÇ]/","",trim(strtr($tabligne[4],"'"," ")))))));
						if (strlen($tabligne[4]) > 50) $tabligne[4] = substr($tabligne[4], 0, 50);

						//$tabligne[5] = preg_replace("/[^A-Za-z0-9 .\-éèüëïäê]/","",trim($tabligne[5]));
						$tabligne[5] = ereg_replace("Æ","AE",ereg_replace("æ","ae",ereg_replace("¼","OE",ereg_replace("½","oe",preg_replace("/[^A-Za-z0-9 .\-àâäéèêëîïôöùûüçÀÄÂÉÈÊËÎÏÔÖÙÛÜÇ]/","",trim(strtr($tabligne[5],"'"," ")))))));
						if (strlen($tabligne[5]) > 50) $tabligne[5] = substr($tabligne[5], 0, 50);

						$tabligne[6] = preg_replace("/[^0-9]/","",trim($tabligne[6]));
						if (strlen($tabligne[6]) > 6) $tabligne[6] = substr($tabligne[6], 0, 6);

						//$tabligne[7] = preg_replace("/[^A-Za-z0-9 .\-éèüëïäê]/","",trim($tabligne[7]));
						$tabligne[7] = ereg_replace("Æ","AE",ereg_replace("æ","ae",ereg_replace("¼","OE",ereg_replace("½","oe",preg_replace("/[^A-Za-z0-9 .\-àâäéèêëîïôöùûüçÀÄÂÉÈÊËÎÏÔÖÙÛÜÇ]/","",trim(strtr($tabligne[7],"'"," ")))))));
						if (strlen($tabligne[7]) > 50) $tabligne[7] = substr($tabligne[7], 0, 50);


						$data_tab[$k] = array();


						$data_tab[$k]["id_eleve"] = $tabligne[0];
						$data_tab[$k]["nom"] = $tabligne[1];
						$data_tab[$k]["prenom"] = $tabligne[2];
						$data_tab[$k]["civilite"] = $tabligne[3];
						$data_tab[$k]["adresse1"] = $tabligne[4];
						$data_tab[$k]["adresse2"] = $tabligne[5];
						$data_tab[$k]["code_postal"] = $tabligne[6];
						$data_tab[$k]["commune"] = $tabligne[7];

						$k++;
					}
					//$k++;
				}

				fclose($fp);

				// Fin de l'analyse du fichier.
				// Maintenant on va afficher tout ça.

				echo "<form enctype='multipart/form-data' action='responsables.php' method='post'>\n";
				echo "<input type='hidden' name='action' value='save_data' />\n";
				echo "<table class='boireaus' summary='Tableau des responsables'>\n";
				echo "<tr><th>ID élève</th><th>Nom</th><th>Prénom</th><th>Civilité</th><th>Ligne 1 adresse</th><th>Ligne 2 adresse</th><th>Code postal</th><th>Commune</th></tr>\n";

				$alt=1;
				for ($i=0;$i<$k-1;$i++) {
					$alt=$alt*(-1);
                    echo "<tr class='lig$alt'>\n";
					echo "<td";
					if($data_tab[$i]["id_eleve"]==""){
						echo " style='color:red;'";
					}
					echo ">\n";
					echo $data_tab[$i]["id_eleve"];
					echo "<input type='hidden' name='ligne".$i."_id_eleve' value='" . $data_tab[$i]["id_eleve"] . "' />\n";
					echo "</td>\n";
					echo "<td";
					if($data_tab[$i]["id_eleve"]==""){
						echo " style='color:red;'";
					}
					echo ">\n";
					echo $data_tab[$i]["nom"];
					echo "<input type='hidden' name='ligne".$i."_nom' value='" . $data_tab[$i]["nom"] . "' />\n";
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["prenom"];
					echo "<input type='hidden' name='ligne".$i."_prenom' value='" . $data_tab[$i]["prenom"] . "' />\n";
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["civilite"];
					echo "<input type='hidden' name='ligne".$i."_civilite' value='" . $data_tab[$i]["civilite"] . "' />\n";
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["adresse1"];
					echo "<input type='hidden' name='ligne".$i."_adresse1' value='" . $data_tab[$i]["adresse1"] . "' />\n";
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["adresse2"];
					echo "<input type='hidden' name='ligne".$i."_adresse2' value='" . $data_tab[$i]["adresse2"] . "' />\n";
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["code_postal"];
					echo "<input type='hidden' name='ligne".$i."_code_postal' value='" . $data_tab[$i]["code_postal"] . "' />\n";
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["commune"];
					echo "<input type='hidden' name='ligne".$i."_commune' value='" . $data_tab[$i]["commune"] . "' />\n";
					echo "</td>\n";
					echo "</tr>\n";
				}

				echo "</table>\n";

				echo "<input type='submit' value='Enregistrer' />\n";

				echo "</form>\n";
			}

		} else if (trim($csv_file['name'])=='') {

			echo "<p>Aucun fichier n'a été sélectionné !<br />\n";
			echo "<a href='responsables.php'>Cliquer ici </a> pour recommencer !</p>\n";

		} else {
			echo "<p>Le fichier sélectionné n'est pas valide !<br />\n";
			echo "<a href='responsables.php'>Cliquer ici </a> pour recommencer !</p>\n";
		}
	}
}
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>