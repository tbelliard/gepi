<?php
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

extract($_GET, EXTR_OVERWRITE);
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

//**************** EN-TETE *****************
$titre_page = "Etablissements | Importation d'un fichier csv";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
// $long_max : doit être plus grand que la plus grande ligne trouvée dans le fichier CSV
$long_max = 8000;

echo "<p class='bold'><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link' /> Retour</a></>\n";;

if (!isset($is_posted)) {
	echo "<p><span class = 'grand'>Première phase d'importation des établissements </span></p>\n";
	echo "<hr />\n";
	echo "<p>Choisir un fichier csv parmi ceux disponibles actuellement dans la distribution GEPI : <br />\n";
	echo "<form enctype=\"multipart/form-data\" action=\"import_etab_csv.php\" method=post name=\"formulaire\">\n";

	echo add_token_field();

	$handle=opendir('./bases');
	echo "<select name=\"csv_file\" size=\"1\">\n";
	$file_tab = array();
	while ($file = readdir($handle)) {
	if (($file != '.') and ($file != '..'))
		// On met le fichier dans un tableau, histoire de pouvoir classer tout ça
		$files_tab[] = $file;
	}
	sort($files_tab);
	foreach ($files_tab as $file) {
	echo "<option>".$file."</option>\n";
	}
	echo "</select>\n";
	closedir($handle);
	echo "<input type='submit' value='Valider' />\n";
	echo "<input type='hidden' name='is_posted' value='1' />\n";
	echo "<input type='hidden' name='choix' value=\"gepi\" />\n";
	echo "</form>\n";

	echo "<br /><br /><hr />\n";
/*
	echo "<p>Choisir un autre fichier de votre choix :<br />
	<form enctype=\"multipart/form-data\" action=\"import_etab_csv.php\" method=\"post\" name=\"formulaire\">\n";
*/
	echo "<p>Choisir un autre fichier de votre choix :<br />
	<form enctype=\"multipart/form-data\" action=\"import_etab_csv.php\" method=\"post\" name=\"formulaire2\">\n";
	echo add_token_field();

	$csv_file = "";
	echo "<input type='file' name=\"csv_file\" />\n";
	echo "<input type='submit' value='Valider' />\n";
	?>
	<p><label for='en_tete' style='cursor: pointer;'>Si le fichier à importer comporte une première ligne d'en-tête (<i>non vide</i>) à ignorer, cocher la case ci-contre&nbsp;
	<input type='checkbox' name="en_tete" id="en_tete" value="yes" /></label></p>
	<input type='hidden' name='is_posted' value='1' />
	<input type='hidden' name='choix' value="autre" />

	</FORM>
	<?php
	echo "<p>Le fichier d'importation peut-être constitué à l'aide d'un tableur à partir des informations contenues dans le fichier \"NMETABC.TXT\" qui se trouve dans GEP.";
	echo "<br />Il doit être au format csv (séparateur : point-virgule) et doit contenir les six champs suivants :<br />\n";
	echo "--> <B>Le N° RNE de l'établissement</B><br />\n";
	echo "--> <B>Le nom de l'établissement</B><br />\n";
	echo "--> <B>Le type :</B>\n<ul>\n";
	foreach ($type_etablissement as $type_etab => $nom_etablissement) {
		if ($nom_etablissement != "") echo "<li>\"<b>".$type_etab."</b>\" (pour les établissements de type \"".$nom_etablissement."\")</li>\n";
	}
	echo "</ul>\nSeules ces possibilités sont autorisées (attention à respecter la casse).<br /><br />\n";
	echo "--> <B>Le type  \"public\" ou \"prive\". Seules ces deux possibilités sont autorisées.</B><br />\n";
	echo "--> <B>Le code postal de la ville.</B><br />\n";
	echo "--> <B>La ville.</B>\n";

} else if (isset($is_posted ) and ($is_posted==1 )) {
	check_token(false);

	echo "<p><span class = 'grand'>Deuxième phase d'importation des établissements </span></p>\n";
	$table_etab=array();
	if ($_POST['choix'] == 'gepi') {
		$fp = @fopen("./bases/".$_POST['csv_file'], "r");
	} else {
	$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;
	if($csv_file['tmp_name'] == "") {
		echo "<p>Aucun fichier n'a été sélectionné !</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	$fp = @fopen($csv_file['tmp_name'], "r");
	}

	echo "<form enctype='multipart/form-data' action='import_etab_csv.php' method='post'>\n";
	echo "<p><b>Attention</b>, les données ne sont pas encore enregistrées dans la base GEPI. Vous devez confirmer l'importation (bouton en bas de la page) !</p>\n";
	if(!$fp) {
		echo "Impossible d'ouvrir le fichier CSV";
	} else {
		// Nombre total de lignes lues
		$row = 0;
		// Nombre total de lignes insérées dans la base
		$ind = 0;
		echo "<table class='boireaus'><tr>
		<th><p class='bold'>N° RNE</p></th>
		<th><p class='bold'>Nom de l'établissement</p></th>
		<th><p class='bold'>Type lycée/collège/école/...</p></th>
		<th><p class='bold'>Type public/privé</p></th>
		<th><p class='bold'>Code postal</p></th>
		<th><p class='bold'>Ville</p></th></tr>\n";
		$alt=1;
		while(!feof($fp)) {
			if (isset($en_tete)) {
				$data = fgetcsv ($fp, $long_max, ";");
				unset($en_tete);
			}
			else{
				$alt=$alt*(-1);
			}
			$data = fgetcsv ($fp, $long_max, ";");
			$num = count ($data);
			if ($num == 6)  {
				$reg_rne = '';
				$reg_nom = '';
				$reg_type2 = '';
				$reg_type1 = '';
				$reg_cp = '';
				$reg_ville = '';
				$row++;
				echo "<tr class='lig$alt white_hover'>\n";
				for ($c=0; $c<$num; $c++) {
					switch ($c) {
					case 0:
						//RNE
						$call_rne = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM etablissements WHERE id='$data[$c]'");
						$test = @mysqli_num_rows($call_rne);
						$couleur = 'black';
						if ($test != 0) {
							$couleur = 'red';
							$reg_ligne='no';
						}
						//echo "<td><p><b><font color = ".$couleur.">".$data[$c]."</font></p></b></td>\n";
						echo "<td><p><b><font color = ".$couleur.">".$data[$c]."</font></b></p></td>\n";
						$reg_rne=$data[$c];
						break;
					case 1:
						// Nom
						if ($data[$c] == "") {
						$col = "<b><font color='red'>Non défini</font></b>\n";
							$reg_ligne='no';
						} else {
							$reg_nom = traitement_magic_quotes(corriger_caracteres($data[$c]));
							$col = $data[$c];
						}
						echo "<td>$col</td>\n";
						break;
					case 2:
						// Type lycée/collège
						$tempo = $data[$c];
						$valid='no';
						foreach ($type_etablissement as $type_etabli => $nom_etablissement) {
							if ($tempo == $type_etabli) {
								$tempo = $nom_etablissement;
								$reg_type1 = $type_etabli;
								$valid='yes';

							}
						}
						if ($valid=='yes') {
							echo "<td><p>$tempo</p></td>\n";
						} else {
							echo "<td><b><font color='red'>Non défini</font></b></td>\n";
							$reg_ligne='no';
						}
						break;
					case 3:
						// Type public/privé
						$tempo = my_strtolower($data[$c]);
						$valid='yes';
						switch($tempo) {
							case "public":
							$reg_type2 = "public";
							break;
							case "prive":
							$reg_type2 = "prive";
							break;
							$valid = 'no';
						}
						if ($valid=='yes') {
							echo "<td><p>$tempo</p></td>\n";
						} else {
							echo "<td><b><font color='red'>Non défini</font></b></td>\n";
							$reg_ligne='no';
						}
						break;
					case 4:
						// Code postal
						if (preg_match ("/^[0-9]{1,5}$/", $data[$c])) {
							echo "<td><p>$data[$c]</p></td>\n";
							$reg_cp=$data[$c];
						} else {
							echo "<td><b><font color='red'>Non défini</font></b></td>\n";
							$reg_ligne='no';
						}
						break;
					case 5:
						// Ville
					if ($data[$c] == "") {
							$col = "<b><font color='red'>Non défini</font></b>\n";
							$reg_ligne='no';
							$reg_ville = '';
						} else {
							$col = $data[$c];
							$reg_ville = traitement_magic_quotes(corriger_caracteres($data[$c]))    ;
						}
						echo "<td>$col</td></tr>\n";
						break;
					}
				}
				if (isset($reg_ligne)) {
					unset($reg_ligne);
				} else {
					$table_etab[$ind][] = $reg_rne;
					$table_etab[$ind][] = $reg_nom;
					$table_etab[$ind][] = $reg_type1;
					$table_etab[$ind][] = $reg_type2;
					$table_etab[$ind][] = $reg_cp;
					$table_etab[$ind][] = $reg_ville;
					$ind++;
				}
			}
			// fin de la boucle "while(!feof($fp))"
		}
		fclose($fp);
		echo "</table>\n";
		echo "<p>Première phase de l'importation : <b>$row entrées détectées</b> !</p>\n";
		if ($row > 0) {
			$table_etab=serialize($table_etab);
			$_SESSION['table_etab']=$table_etab;
			echo "<p class='bold'>AVERTISSEMENT : </p>
			<ul><li>Les N° RNE qui apparaissent en rouge correspondent à des établissements déjà présents dans la base.
			Les lignes correspondantes seront ignorées lors de la phase finale d'importation.</li>
			<li>Les intitulés \"<font color=red>Non défini</font>\" signifient que le champ en question n'est pas valide.
			La ligne correspondante sera ignorée lors de la phase finale d'importation.</li>
			</ul>\n";
			if ($ind != 0) {
				if ($ind == 1) {
					echo "<center><p><b>".$ind." ligne est prête à être enregistrée.</b></p>\n";
				}
				else{
					echo "<center><p><b>".$ind." lignes sont prêtes à être enregistrées.</b></p>\n";
				}
				echo "<input type='submit' value='Enregistrer les données' /></center>\n";
				echo "<input type='hidden' name='is_posted' value='2' />\n";
			} else {
				echo "<center><p><b>Il n'y a aucun établissement à entrer dans la base.</p></center>\n";
			}

			echo add_token_field();

			echo "</form>\n";
		} else {
			echo "<p>L'importation a échoué !</p>\n";
		}
	}
} else {
	echo "<p><span class = 'grand'>Troisième phase d'importation des établissements </span></p>\n";
	if (!isset($_SESSION['table_etab'])) {
		echo "<center><p class='grand'>Opération non conforme.</p></center></body></html>\n";
		die();
	}

	check_token(false);

	$table_etab=unserialize($_SESSION['table_etab']);
	$pb = 'no';
	for ($c=0; $c<count ($table_etab); $c++) {
		$couleur[$c] = '';
		$sql = mysqli_query($GLOBALS["mysqli"], "INSERT INTO etablissements SET
		id='".$table_etab[$c][0]."',
		nom='".$table_etab[$c][1]."',
		niveau='".$table_etab[$c][2]."',
		type='".$table_etab[$c][3]."',
		cp='".$table_etab[$c][4]."',
		ville='".$table_etab[$c][5]."'
		");
		if (!$sql) {
			$couleur[$c] = 'red';
			$pb = 'yes';
		}
	}
	If ($pb == 'yes') {
		echo "<p>Il y a eu un ou plusieurs problèmes lors de l'enregistrement.
		Les lignes en rouge indiquent les enregistrements défectueux.</p>\n";
	} else {
		echo "<p class='bold'>".count ($table_etab)." établissements ont été insérés avec succès dans la base.</p>\n";
	}

	echo "<table class='boireaus' cellpadding=\"2\" cellspacing=\"2\">\n";
	$alt=1;
	for ($c=0; $c<count ($table_etab); $c++) {
		$alt=$alt*(-1);
		if($couleur[$c]!=''){
			echo "<tr bgColor=\"".$couleur[$c]."\">\n";
		}
		else{
			echo "<tr class='lig$alt white_hover'>\n";
		}

		for ($j=0; $j<count($table_etab[$c]); $j++) {
			// Pour l'affichage final, on enlève les caractère \ qu'on a rajouté avec traitement_magic_quotes plus haut
			echo "<td>".StripSlashes($table_etab[$c][$j])."</td>\n";
		}
		echo "</tr>\n";
	}
	echo "</table>\n";
	unset($_SESSION['table_etab']);

}
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
