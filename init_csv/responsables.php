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
$liste_tables_del = $liste_tables_del_etape_resp;

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : Importation des élèves - Etape 1";
require_once("../lib/header.inc.php");
//************** FIN EN-TETE ***************

$en_tete=isset($_POST['en_tete']) ? $_POST['en_tete'] : "no";

//debug_var();

// Passer à 'y' pour afficher les requêtes
$debug_resp='n';

?>
<p class="bold"><a href="index.php#responsables"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil initialisation</a></p>
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
	echo add_token_field();
	echo "<input type='hidden' name='action' value='upload_file' />\n";
	echo "<p><input type=\"file\" size=\"80\" name=\"csv_file\" />\n";
    echo "<p><label for='en_tete' style='cursor:pointer;'>Si le fichier à importer comporte une première ligne d'en-tête (non vide) à ignorer, <br />cocher la case ci-contre</label>&nbsp;<input type='checkbox' name='en_tete' id='en_tete' value='yes' checked /></p>\n";
	echo "<p><input type='submit' value='Valider' />\n";
	echo "</form>\n";

	$sql="SELECT 1=1 FROM utilisateurs WHERE statut='responsable';";
	if($debug_resp=='y') {echo "<span style='color:green;'>$sql</span><br />";}
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)>0) {
		$sql="SELECT 1=1 FROM tempo_utilisateurs WHERE statut='responsable';";
		if($debug_resp=='y') {echo "<span style='color:green;'>$sql</span><br />";}
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)==0) {
			echo "<p style='color:red'>Il existe un ou des comptes responsables de l'année passée, et vous n'avez pas mis ces comptes en réserve pour imposer le même login/mot de passe cette année.<br />Est-ce bien un choix délibéré ou un oubli de votre part?<br />Pour conserver ces login/mot de de passe de façon à ne pas devoir re-distribuer ces informations (<em>et éviter de perturber ces utilisateurs</em>), vous pouvez procéder à la mise en réserve avant d'initialiser l'année dans la page <a href='../gestion/changement_d_annee.php'>Changement d'année</a> (<em>vous y trouverez aussi la possibilité de conserver les comptes élèves (s'ils n'ont pas déjà été supprimés) et bien d'autres actions à ne pas oublier avant l'initialisation</em>).</p>\n";
		}
	}

	echo "<p><i>NOTE:</i> Les champs marqués d'un <b>(*)</b> doivent être non vides.</p>\n";
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

		// Première étape : on vide les tables

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

		// Suppression des comptes de responsables:
		echo "<br />\n";
		echo "<p><em>On supprime les anciens comptes responsables...</em> ";
		$sql="DELETE FROM utilisateurs WHERE statut='responsable';";
		$del=mysqli_query($GLOBALS["mysqli"], $sql);

		$sql="SELECT * FROM temp_responsables;";
		$res_temp=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_temp)==0) {
			echo "<p style='color:red'>ERREUR&nbsp;: Aucun responsable n'a été trouvé&nbsp;???</p>\n";
			echo "<p><br /></p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		echo "<br />\n";
		echo "<p><em>On remplit la table 'responsables'&nbsp;:</em> ";

		$i = 0;
		// Compteur d'erreurs
		$error = 0;
		// Compteur d'enregistrement
		$total = 0;
		while ($lig=mysqli_fetch_object($res_temp)) {
			$reg_id_eleve = $lig->elenoet;
			$reg_nom = $lig->nom;
			$reg_prenom = $lig->prenom;
			$reg_civilite = $lig->civilite;
			$reg_adresse1 = $lig->adresse1;
			$reg_adresse2 = $lig->adresse2;
			$reg_code_postal = $lig->code_postal;
			$reg_commune = $lig->commune;

			// On nettoie et on vérifie :
			$reg_id_eleve = preg_replace("/[^0-9]/","",trim($reg_id_eleve));

			$reg_nom=my_strtoupper(nettoyer_caracteres_nom($reg_nom, "a", " '_-",""));
			if (mb_strlen($reg_nom) > 50) $reg_nom = mb_substr($reg_nom, 0, 50);

			$reg_prenom=nettoyer_caracteres_nom($reg_prenom, "a", " '_-","");
			if (mb_strlen($reg_prenom) > 50) $reg_prenom = mb_substr($reg_prenom, 0, 50);

			if ($reg_civilite != "M." AND $reg_civilite != "MME" AND $reg_civilite != "MLLE") { $reg_civilite = "";}

			$reg_adresse1=preg_replace("/'/",' ',$reg_adresse1);
			$reg_adresse1=nettoyer_caracteres_nom($reg_adresse1, "an", " ,'_-","");
			if (mb_strlen($reg_adresse1) > 50) $reg_adresse1 = mb_substr($reg_adresse1, 0, 50);

			$reg_adresse2=preg_replace("/'/",' ',$reg_adresse2);
			$reg_adresse2=nettoyer_caracteres_nom($reg_adresse2, "an", " ,'_-","");
			if (mb_strlen($reg_adresse2) > 50) $reg_adresse2 = mb_substr($reg_adresse2, 0, 50);

			$reg_code_postal = preg_replace("/[^0-9]/","",trim($reg_code_postal));
			if (mb_strlen($reg_code_postal) > 6) $reg_code_postal = mb_substr($reg_code_postal, 0, 6);

			$reg_commune=preg_replace("/'/",' ',$reg_commune);
			$reg_commune=nettoyer_caracteres_nom($reg_commune, "an", " ,'_-","");
			if (mb_strlen($reg_commune) > 50) $reg_commune = mb_substr($reg_commune, 0, 50);


			// On vérifie que l'élève existe
			$test = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(login) FROM eleves WHERE elenoet = '" . $reg_id_eleve . "'"), 0);

			if($reg_id_eleve==""){
				echo "<p style='color:red'>Erreur : L'identifiant élève est vide pour $reg_prenom $reg_nom</p>\n";
			}
			else{
				if($reg_nom==""){
					echo "<p style='color:red'>Erreur : Le nom du responsable est vide pour l'élève $reg_id_eleve</p>\n";
				}
				else{
					if ($test == 0 OR !$test) {
						// Test négatif : aucun élève avec cet ID... On envoie un message d'erreur.
						echo "<p style='color:red'>Erreur : l'élève avec l'identifiant interne " . $reg_id_eleve . " n'existe pas dans Gepi.</p>\n";
					} else {
						// Test positif : on peut donc enregistrer les données de responsable.

						// On regarde si une entrée existe déjà pour l'élève en question
						$test = mysqli_query($GLOBALS["mysqli"], "SELECT ereno, nom1, nom2 FROM responsables WHERE ereno = '" . $reg_id_eleve . "'");
						$insert = null;

						if (mysqli_num_rows($test) == 0) {
							// Aucune entrée n'existe. On enregistre le responsable comme premier responsable

							$sql="INSERT INTO responsables SET " .
								"ereno = '" . $reg_id_eleve . "', " .
								"nom1 = '" . mysqli_real_escape_string($GLOBALS["mysqli"], $reg_nom) . "', " .
								"prenom1 = '" . mysqli_real_escape_string($GLOBALS["mysqli"], $reg_prenom) . "', " .
								"adr1 = '" . mysqli_real_escape_string($GLOBALS["mysqli"], $reg_adresse1) . "', " .
								"adr1_comp = '" . mysqli_real_escape_string($GLOBALS["mysqli"], $reg_adresse2) . "', " .
								"commune1 = '" . mysqli_real_escape_string($GLOBALS["mysqli"], $reg_commune) . "', " .
								"cp1 = '" . $reg_code_postal . "'";
							$insert = mysqli_query($GLOBALS["mysqli"], $sql);

						} else {
							// Une entrée existe
							// On regarde si le responsable 1 a déjà été saisi
							if (old_mysql_result($test, 0, "nom1") == "") {
								$sql="UPDATE responsables SET " .
									"nom1 = '" . mysqli_real_escape_string($GLOBALS["mysqli"], $reg_nom) . "', " .
									"prenom1 = '" . mysqli_real_escape_string($GLOBALS["mysqli"], $reg_prenom) . "', " .
									"adr1 = '" . mysqli_real_escape_string($GLOBALS["mysqli"], $reg_adresse1) . "', " .
									"adr1_comp = '" . mysqli_real_escape_string($GLOBALS["mysqli"], $reg_adresse2) . "', " .
									"commune1 = '" . mysqli_real_escape_string($GLOBALS["mysqli"], $reg_commune) . "', " .
									"cp1 = '" . $reg_code_postal . "' " .
									"WHERE " .
									"ereno = '" . $reg_id_eleve . "'";
								$insert = mysqli_query($GLOBALS["mysqli"], $sql);

							} else if (old_mysql_result($test, 0, "nom2") == "") {
								$sql="UPDATE responsables SET " .
									"nom2 = '" . mysqli_real_escape_string($GLOBALS["mysqli"], $reg_nom) . "', " .
									"prenom2 = '" . mysqli_real_escape_string($GLOBALS["mysqli"], $reg_prenom) . "', " .
									"adr2 = '" . mysqli_real_escape_string($GLOBALS["mysqli"], $reg_adresse1) . "', " .
									"adr2_comp = '" . mysqli_real_escape_string($GLOBALS["mysqli"], $reg_adresse2) . "', " .
									"commune2 = '" . mysqli_real_escape_string($GLOBALS["mysqli"], $reg_commune) . "', " .
									"cp2 = '" . $reg_code_postal . "' " .
									"WHERE " .
									"ereno = '" . $reg_id_eleve . "'";
								$insert = mysqli_query($GLOBALS["mysqli"], $sql);

							} else {
								// Erreur ! Les deux responsables ont déjà été saisis...
								echo "<p style='color:red'>Erreur pour " . $reg_prenom . " " . $reg_nom . " ! Les deux responsables ont déjà été saisis.</p>\n";
							}
						}

						if ($insert == false) {
							$error++;
							$erreur_mysql=mysqli_error($GLOBALS["mysqli"]);
							if($erreur_mysql!=""){echo "<p style='color:red'>".$erreur_mysql."</p>\n";}
							//echo "<p>$sql</p>\n";
						} else {
							$total++;
						}
					}
				}
			}

			$i++;
		}

		if ($error > 0) echo "<p style='color:red'>Il y a eu " . $error . " erreurs.</p>\n";
		if ($total > 0) echo "<p>" . $total . " responsables ont été enregistrés.</p>\n";

		echo "<p><a href='index.php#responsables'>Revenir à la page précédente</a></p>\n";

		// On sauvegarde le témoin du fait qu'il va falloir convertir pour remplir les nouvelles tables responsables:
		saveSetting("conv_new_resp_table", 0);

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
		if(my_strtolower($csv_file['name']) == "g_responsables.csv") {

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

						$tabligne[1]=my_strtoupper(nettoyer_caracteres_nom($tabligne[1], "a", " _-",""));
						$tabligne[1]=preg_replace("/'/"," ",$tabligne[1]);
						if (mb_strlen($tabligne[1]) > 50) $tabligne[1] = mb_substr($tabligne[1], 0, 50);

						$tabligne[2]=nettoyer_caracteres_nom($tabligne[2], "a", " _-","");
						$tabligne[2]=preg_replace("/'/"," ",$tabligne[2]);
						if (mb_strlen($tabligne[2]) > 50) $tabligne[2] = mb_substr($tabligne[2], 0, 50);

						if ($tabligne[3] != "M." AND $tabligne[3] != "MME" AND $tabligne[3] != "MLLE") { $tabligne[3] = "";}

						$tabligne[4]=nettoyer_caracteres_nom($tabligne[4], "an", " ,'_-","");
						$tabligne[4]=preg_replace("/'/",' ',$tabligne[4]);
						if (mb_strlen($tabligne[4]) > 50) $tabligne[4] = mb_substr($tabligne[4], 0, 50);

						$tabligne[5]=nettoyer_caracteres_nom($tabligne[5], "an", " ,'_-","");
						$tabligne[5]=preg_replace("/'/",' ',$tabligne[5]);
						if (mb_strlen($tabligne[5]) > 50) $tabligne[5] = mb_substr($tabligne[5], 0, 50);

						$tabligne[6] = preg_replace("/[^0-9]/","",trim($tabligne[6]));
						if (mb_strlen($tabligne[6]) > 6) $tabligne[6] = mb_substr($tabligne[6], 0, 6);

						$tabligne[7]=nettoyer_caracteres_nom($tabligne[7], "an", " ,'_-","");
						$tabligne[7]=preg_replace("/'/",' ',$tabligne[7]);
						if (mb_strlen($tabligne[7]) > 50) $tabligne[7] = mb_substr($tabligne[7], 0, 50);

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
				}

				fclose($fp);

				// Fin de l'analyse du fichier.
				// Maintenant on va afficher tout ça.

				$sql="CREATE TABLE IF NOT EXISTS temp_responsables (
				id int(11) NOT NULL auto_increment,
				elenoet varchar(50) NOT NULL default '', 
				nom varchar(50) NOT NULL default '', 
				prenom varchar(50) NOT NULL default '', 
				civilite varchar(50) NOT NULL default '', 
				adresse1 varchar(100) NOT NULL default '', 
				adresse2 varchar(100) NOT NULL default '', 
				code_postal varchar(6) NOT NULL default '', 
				commune varchar(50) NOT NULL default '',
				PRIMARY KEY  (id)
				);";
				$create_table = mysqli_query($GLOBALS["mysqli"], $sql);

				$sql="TRUNCATE TABLE temp_responsables;";
				$vide_table = mysqli_query($GLOBALS["mysqli"], $sql);


				$nb_error=0;

				echo "<form enctype='multipart/form-data' action='responsables.php' method='post'>\n";
				echo add_token_field();
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

					$sql="INSERT INTO temp_responsables SET elenoet='".mysqli_real_escape_string($GLOBALS["mysqli"], $data_tab[$i]["id_eleve"])."',
					nom='".mysqli_real_escape_string($GLOBALS["mysqli"], $data_tab[$i]["nom"])."',
					prenom='".mysqli_real_escape_string($GLOBALS["mysqli"], $data_tab[$i]["prenom"])."',
					civilite='".mysqli_real_escape_string($GLOBALS["mysqli"], $data_tab[$i]["civilite"])."',
					adresse1='".mysqli_real_escape_string($GLOBALS["mysqli"], $data_tab[$i]["adresse1"])."',
					adresse2='".mysqli_real_escape_string($GLOBALS["mysqli"], $data_tab[$i]["adresse2"])."',
					commune='".mysqli_real_escape_string($GLOBALS["mysqli"], $data_tab[$i]["commune"])."',
					code_postal='".mysqli_real_escape_string($GLOBALS["mysqli"], $data_tab[$i]["code_postal"])."';";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$insert) {
						echo "<span style='color:red'>";
						echo $data_tab[$i]["id_eleve"];
 						echo "</span>";
						$nb_error++;
					}
					else {
						echo $data_tab[$i]["id_eleve"];
					}
					echo "</td>\n";
					echo "<td";
					if($data_tab[$i]["id_eleve"]==""){
						echo " style='color:red;'";
					}
					echo ">\n";
					echo $data_tab[$i]["nom"];
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["prenom"];
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["civilite"];
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["adresse1"];
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["adresse2"];
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["code_postal"];
					echo "</td>\n";
					echo "<td>\n";
					echo $data_tab[$i]["commune"];
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
