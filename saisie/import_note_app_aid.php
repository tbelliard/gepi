<?php
/*
 *
 * Copyright 2001, 2016 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

//extract($_GET, EXTR_OVERWRITE);
//extract($_POST, EXTR_OVERWRITE);

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

$indice_aid = isset($_POST['indice_aid']) ? $_POST['indice_aid'] : (isset($_GET['indice_aid']) ? $_GET['indice_aid'] : NULL);
$aid_id = isset($_POST['aid_id']) ? $_POST['aid_id'] : (isset($_GET['aid_id']) ? $_GET['aid_id'] : NULL);
$en_tete = isset($_POST['en_tete']) ? $_POST['en_tete'] : (isset($_GET['en_tete']) ? $_GET['en_tete'] : NULL);

if(is_numeric($indice_aid) && $indice_aid > 0 && is_numeric($aid_id) && $aid_id > 0) {
	if ($_SESSION['statut'] != "secours") {
		if(!acces_saisie_aid($_SESSION['login'], $indice_aid, $aid_id)) {
			$mess=rawurlencode("Vous n&aposêtes pas professeur de cet AID !");
			header("Location: ../accueil.php?msg=$mess");
			die();
		}
	}

	$tab_aid=get_tab_aid($aid_id);
}
else {
	$mess=rawurlencode("AID non identifié !");
	header("Location: ../accueil.php?msg=$mess");
	die();
}

$periode_num = isset($_POST['periode_num']) ? $_POST['periode_num'] : (isset($_GET['periode_num']) ? $_GET['periode_num'] : NULL);
if (!is_numeric($periode_num)) {$periode_num = 0;}

$is_posted = isset($_POST['is_posted']) ? $_POST['is_posted'] : NULL;

//include "../lib/periodes.inc.php";

//**************** EN-TETE *****************
$titre_page = "AID notes et appréciations | Importation";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

// $long_max : doit être plus grand que la plus grande ligne trouvée dans le fichier CSV
$long_max = 8000;

echo "<p class='bold'><a href='saisie_aid.php?indice_aid=$indice_aid'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil saisie</a>";
//====================================
if($_SESSION['statut']=='professeur') {

	/*
	// A FAIRE: Récupérer la liste des AID de la catégorie courante
	$tab_groups = get_groups_for_prof($_SESSION["login"],"classe puis matière");
	//$tab_groups = get_groups_for_prof($_SESSION["login"]);

	if(!empty($tab_groups)) {
		$id_grp_prec=0;
		$id_grp_suiv=0;
		$temoin_tmp=0;
		//foreach($tab_groups as $tmp_group) {
		for($loop=0;$loop<count($tab_groups);$loop++) {
			if($tab_groups[$loop]['id']==$id_groupe){
				$temoin_tmp=1;
				if(isset($tab_groups[$loop+1])){
					$id_grp_suiv=$tab_groups[$loop+1]['id'];
				}
				else{
					$id_grp_suiv=0;
				}
			}
			if($temoin_tmp==0){
				$id_grp_prec=$tab_groups[$loop]['id'];
			}
		}
		// =================================

		if(isset($id_grp_prec)){
			if($id_grp_prec!=0){
				echo " | <a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_grp_prec&amp;periode_num=$periode_num";
				echo "'>Enseignement précédent</a>";
			}
		}
		if(isset($id_grp_suiv)){
			if($id_grp_suiv!=0){
				echo " | <a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_grp_suiv&amp;periode_num=$periode_num";
				echo "'>Enseignement suivant</a>";
				}
		}
	}
	*/
	// =================================
}
//====================================
echo "</p>\n";

echo "<p><span class = 'grand'>Première phase d'importation des notes et/ou appréciations </span>";
echo "<p class = 'bold'>AID&nbsp;: " . htmlspecialchars($tab_aid["nom_aid"]) ." (" . $tab_aid["classlist_string"] . ") (" . htmlspecialchars($tab_aid["nom_general_complet"]) . ")</p>\n";

$lignes_radio_periode="";
$tout_verrouille=true;
for($loop_per=1;$loop_per<=$tab_aid['maxper'];$loop_per++) {
	if(($tab_aid["classe"]["ver_periode"]['all'][$loop_per]>=2)||
	(($tab_aid["classe"]["ver_periode"]['all'][$loop_per]!=0)&&($_SESSION['statut']=='secours'))) {
		$tout_verrouille=false;
		//break;
		if($lignes_radio_periode=="") {
			$lignes_radio_periode.="<label for='periode_num_$loop_per' id='texte_periode_num_$loop_per'>Période $loop_per </label><input type='radio' name='periode_num' id='periode_num_$loop_per' value='$loop_per' checked /><br />";
		}
		else {
			$lignes_radio_periode.="<label for='periode_num_$loop_per' id='texte_periode_num_$loop_per'>Période $loop_per </label><input type='radio' name='periode_num' id='periode_num_$loop_per' value='$loop_per' /><br />";
		}
	}
}

if($tout_verrouille) {
	echo "<p style='color:red'>Toutes les périodes sont closes pour les classes associées à cet AID.<br />Les saisies ne sont plus possibles.</p>";
	require("../lib/footer.inc.php");
	die();
}

if (!isset($is_posted)) {

	$csv_file="";
	echo "<form enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method='post' name='formulaire'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<p>Fichier CSV à importer : <input type='file' name='csv_file' /> <input type='submit' value='Ouvrir' /></p>
		<p>Si le fichier à importer comporte une première ligne d'en-tête (non vide) à ignorer, cocher la case ci-contre&nbsp;
		<input type='checkbox' name='en_tete' value='yes' checked /></p>
		<input type='hidden' name=is_posted value = 1 />
		<input type='hidden' name='aid_id' value='" . $aid_id . "' />
		<input type='hidden' name='indice_aid' value='" . $indice_aid . "' />
		<p>
		$lignes_radio_periode
		</p>
	</fieldset>
</form>

<p>Vous avez décidé d'importer directement un fichier de moyennes et/ou d'appréciations. Le fichier d'importation doit être au format csv <em>(séparateur : point-virgule)</em> et doit contenir les trois champs suivants&nbsp;:</br />
<br />
--&gt; <B>IDENTIFIANT</B> : L'identifiant GEPI de l'élève (<b>voir les explications plus bas</b>).<br />
<br />
--&gt; <B>NOTE</B> : note entre 0 et 20 avec le point ou la virgule comme symbole décimal.<br />Autres codes possibles <em>(sans les guillemets)</em>&nbsp;: \"<b>abs</b>\" pour \"absent\", \"<b>disp</b>\" pour \"dispensé\", \"<b>-</b>\" pour absence de note.<br />Si ce champ est vide, Il n'y aura pas modification de la note déjà enregistrée dans GEPI pour l'élève en question.<br />
Laisser la colonne vide si il n'y a pas de notes pour cet AID <em>(mais la colonne doit être présente dans le fichier)</em>.<br />
<br />
--&gt; <B>Appréciation</B> : le texte de l'appréciation de l'élève.<br />Si ce champ est vide, Il n'y aura pas modification de l'appréciation enregistrée dans GEPI pour l'élève en question.<br />&nbsp;
</p>
<p>Pour constituer le fichier d'importation vous avez besoin de connaître l'identifiant <b>GEPI</b> de chaque élève. Vous pouvez télécharger:</p>
<ul>
	<li>le fichier élèves <em>(identifiant GEPI (login), sans nom et prénom)</em> en <a href='import_class_csv.php?id_groupe=$id_groupe&amp;periode_num=$periode_num&amp;champs=3&amp;ligne_entete=y&amp;mode=Id_Note_App'><b>cliquant ici</b></a></li>
	<li>ou bien le fichier élèves <em>(nom - prénom - identifiant GEPI)</em> en <a href='import_class_csv.php?id_groupe=$id_groupe&amp;periode_num=$periode_num&amp;champs=5&amp;ligne_entete=y&amp;mode=Nom_Prenom_Id_Note_App'><b>cliquant ici</b></a><br />(<i>ce deuxième fichier n'est pas directement adapté à l'import<br />(il faudra en supprimer les colonnes Nom et Prénom avant import)</i>)</li>
</ul>
<p>Une fois téléchargé, utilisez votre tableur habituel pour ouvrir ce fichier en précisant que le type de fichier est csv avec point-virgule comme séparateur.</p>\n";

}
elseif(!isset($_POST['valider_import'])) {
	check_token(false);

	$non_def = 'no';
	$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;
	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method=post >
	<input type='hidden' name='valider_import' value='y' />
	".add_token_field();
	if($csv_file['tmp_name'] != "") {
		echo "<p><b>Attention</b>, les données ne sont pas encore enregistrées dans la base GEPI. Vous devez confirmer l'importation (bouton en bas de la page) !</p>";

		$fp = @fopen($csv_file['tmp_name'], "r");
		if(!$fp) {
			echo "Impossible d'ouvrir le fichier CSV";
		} else {
			$row = 0;
			echo "<table class='boireaus'>\n<tr>\n<th><p class='bold'>IDENTIFIANT</p></th>\n<th><p class='bold'>Nom</p></th>\n<th><p class='bold'>Prénom</p></th>\n<th><p class='bold'>Note</p></th>\n<th><p class='bold'>Appréciation</p></th>\n</tr>\n";
			$valid = 1;
			$alt=1;
			while(!feof($fp)) {
				if (isset($en_tete)) {
					$data = fgetcsv ($fp, $long_max, ";");
					unset($en_tete);
				}
				$data = fgetcsv ($fp, $long_max, ";");
				$num = count ($data);
				// On commence par repérer les lignes qui comportent 2 ou 3 champs tous vides de façon à ne pas les retenir
				if (($num == 2) or ($num == 3)) {
					$champs_vides = 'yes';
					for ($c=0; $c<$num; $c++) {
						if ($data[$c] != '') {
							$champs_vides = 'no';
						}
					}
				}
				// On ne retient que les lignes qui comportent 2 ou 3 champs dont au moins un est non vide
				if ((($num == 3) or ($num == 2)) and ($champs_vides == 'no')) {
					$alt=$alt*(-1);
					$row++;
					echo "<tr class='lig$alt'>\n";
					for ($c=0; $c<$num; $c++) {
						$col3 = '';
						$reg_app = '';
						$data_app = '';
						switch ($c) {
							case 0:
								//login
								$reg_login = "reg_".$row."_login";
								$reg_statut = "reg_".$row."_statut";
								$call_login = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM eleves WHERE login='" . $data[$c] . "'");
								$test = @mysqli_num_rows($call_login);
								if ($test != 0) {
									$nom_eleve = @old_mysql_result($call_login, 0, "nom");
									$prenom_eleve = @old_mysql_result($call_login, 0, "prenom");

									//
									// Si l'élève ne suit pas la matière
									//
									if (in_array($data[$c], $tab_aid["eleves"][$periode_num]["list"]))  {
										echo "<td><p>$data[$c]</p></td>\n";
									} else {
										echo "<td><p><font color = red>* $data[$c] ??? *</font></p></td>\n";
										$valid = 0;
									}
									echo "<td><p>$nom_eleve</p></td>\n";
									//echo "<td><p>$prenom_eleve</p></td>";
									echo "<td><p>$prenom_eleve</p>";
									$data_login = urlencode($data[$c]);
									echo "<input type='hidden' name='$reg_login' value=\"$data_login\" />";
									echo "</td>\n";
								} else {
									echo "<td><font color = red>???</font></td>\n";
									echo "<td><font color = red>???</font></td>\n";
									echo "<td><font color = red>???</font></td>\n";
									echo "<td><font color = red>???</font></td>\n";
									$valid = 0;
								}
								break;
							case 1:
								// Note
								if (preg_match ("/^[0-9\.\,]{1,}$/", $data[$c])) {
									$data[$c] = str_replace(",", ".", "$data[$c]");
									$test_num = settype($data[$c],"double");
									if ($test_num) {
										if (($data[$c] >= 0) and ($data[$c] <= 20)) {
											//echo "<td><p>$data[$c]</p></td>";
											echo "<td><p>$data[$c]</p>";
											$reg_note = "reg_".$row."_note";
											echo "<input type='hidden' name='$reg_note' value=\"$data[$c]\" />";
											echo "</td>\n";
										} else {
											echo "<td><font color = red>???</font></td>\n";
											$valid = 0;
										}
									} else {
										echo "<td><font color = red>???</font></td>\n";
										$valid = 0;
									}
								} else {
									$tempo = my_strtolower($data[$c]);
									if (($tempo == "disp") or ($tempo == "abs") or ($tempo == "-")) {
										//echo "<td><p>$data[$c]</p></td>";
										echo "<td><p>$data[$c]</p>\n";
										$reg_note = "reg_".$row."_note";
										echo "<input type='hidden' name='$reg_note' value=\"$data[$c]\" />";
										echo "</td>\n";
									} else if ($data[$c] == "") {
										//echo "<td><p><font color = green>ND</font></p></td>";
										echo "<td><p><font color = green>ND</font></p>";
										$reg_note = "reg_".$row."_note";
										echo "<input type='hidden' name='$reg_note' value='' />";
										echo "</td>\n";
										$non_def = 'yes';
									} else {
										echo "<td><font color = red>???</font></td>\n";
										$valid = 0;
									}
								}
								break;
							case 2:
								// Appréciation
								$non_def='';
								if ($data[$c] == "") {
									$col3 = "<font color = green>ND</font>";
									$non_def = 'yes';
									$data_app = '';
								} else {
									// =====================================================
									// L'export CSV généré par le fichier ODS remplace les ; par des |POINT-VIRGULE|
									// pour ne pas provoquer de problème avec le séparateur ; du CSV
									// AJOUT: boireaus
									//echo "<td>\$data[$c]=$data[$c]</td>";
									//$data[$c]=my_ereg_replace("|POINT-VIRGULE|",";",$data[$c]);
									//$data[$c]=my_ereg_replace("\|POINT-VIRGULE\|",";",$data[$c]);
									$data[$c]=trim(str_replace("|POINT-VIRGULE|",";",$data[$c]));
									// =====================================================
									//$col3 = $data[$c];
									$col3 = ensure_utf8($data[$c]);
									//$data_app = urlencode($data[$c]);
									$data_app = urlencode($col3);
								}
								$reg_app = "reg_".$row."_app";
								//                            echo "<INPUT TYPE=HIDDEN name='$reg_app' value = $data_app>";
								echo "<td><p>$col3</p>";
								if($non_def!='yes') {
									echo "<input type='hidden' name='$reg_app' value=\"$data_app\" />";
								}
								//echo "</td>\n</tr>\n";
								echo "</td>\n";
								break;
						}
					}
					//echo "<td><p>$col3</p>"</td></tr>";
					/*
					echo "<td><p>$col3</p>";
					echo "<INPUT TYPE=HIDDEN name='$reg_app' value = $data_app />";
					echo "</td>\n</tr>\n";
					*/
					echo "</tr>\n";
					// fin de la condition "if ($num == 3)"
				}

			// fin de la boucle "while(!feof($fp))"
			}
			fclose($fp);
			echo "</table>\n";
			echo "<p>Première phase de l'importation : $row entrées trouvées, prêtes à être importées !</p>\n";
			if ($row > 0) {
				if ($valid == '1') {
					echo "<input type='hidden' name='nb_row' value=\"$row\" />\n";
					echo "<input type='hidden' name='aid_id' value='" . $aid_id . "' />
					<input type='hidden' name='indice_aid' value='" . $indice_aid . "' />";
					echo "<input type='hidden' name='periode_num' value=\"$periode_num\" />\n";
					echo "<input type='hidden' name='is_posted' value=\"1\" />\n";
					echo "<input type='hidden' name='valider_import' value=\"y\" />\n";
					echo "<input type='submit' value='Enregistrer les données' />\n";
					echo "</form>\n";
					?>
					<script type="text/javascript" language="javascript">
					<!--
					alert("Attention, les données ne sont pas encore enregistrées dans la base GEPI. Vous devez confirmer l'importation (bouton en bas de la page) !");
					//-->
					</script>
					<?php
				} else {
					echo "<p class='bold'>AVERTISSEMENT : Les symboles <font color=red>???</font> signifient que le champ en question n'est pas valide. L'opération d'importation des données ne peut continuer normalement.<br />Veuillez corriger le fichier à importer <br /></p>\n";
					echo "</form>\n";
				}
				if ($non_def == 'yes') {
					echo "<p class='bold'>Les symboles <font color=green>ND</font> signifient que le champ en question sera ignoré. Il n'y aura donc pas modification de la donnée existante dans la base de GEPI.<br /></p>\n";
				}
			} else {
				echo "<p>L'importation a échoué !</p>\n";
			}
		}
		// suite de la condition "if($csv_file != "none")"
	} else {
		echo "<p>Aucun fichier n'a été sélectionné !</p>\n";
		// fin de la condition "if($csv_file != "none")"
	}
}
else {
	$nb_row=$_POST['nb_row'];

	// On vérifie:
	if($periode_num<$tab_aid["display_begin"]) {
		echo "<p style='color:red'>La période choisie $periode_num est antérieure à la première période de cet AID.</p>";
		require("../lib/footer.inc.php");
		die();
	}
	elseif($periode_num<$tab_aid["display_begin"]) {
		echo "<p style='color:red'>La période choisie $periode_num est postérieure à la dernière période de cet AID.</p>";
		require("../lib/footer.inc.php");
		die();
	}

	//debug_var();

	echo "<p>";
	$modif = 'no';
	$nb_row++;
	for ($row=1; $row<$nb_row; $row++) {
		$enregistrement_note = 'yes';
		if(isset($_POST["reg_".$row."_login"])) {
			$reg_login = urldecode($_POST["reg_".$row."_login"]);
		} else {
			$reg_login = '';
		}

		if(isset($_POST["reg_".$row."_note"])) {
			$reg_note = urldecode($_POST["reg_".$row."_note"]);
		}
		else {
			$reg_note = '';
		}

		if(isset($_POST["reg_".$row."_app"])) {
			$reg_app = urldecode($_POST["reg_".$row."_app"]);
			$reg_app = traitement_magic_quotes(corriger_caracteres($reg_app));
		}
		else {
			$reg_app = '';
		}

		$temoin_periode_close="n";

		$call_login = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM eleves WHERE login='$reg_login';");
		$test = mysqli_num_rows($call_login);
		if ($test != 0) {
			//
			// Si l'élève ne suit pas l'enseignement, échec
			//
			if (in_array($reg_login, $tab_aid["eleves"][$periode_num]["list"]))  {
				$eleve_id_classe = $tab_aid["classes"]["classes"][$tab_aid["eleves"][$periode_num]["users"][$reg_login]["classe"]]["id"];
				if (($tab_aid["classe"]["ver_periode"][$eleve_id_classe][$periode_num]=="N")||
				($acces_exceptionnel_saisie)||
				(($tab_aid["classe"]["ver_periode"][$eleve_id_classe][$periode_num]!="O")&&($_SESSION['statut']=='secours'))) {

					$sql_note="";
					if(($tab_aid["type_note"]=="every")||
					(($tab_aid["type_note"]=='last')&&($periode_num==$tab_aid["display_end"]))) {
						// A REVOIR : Si on a un AID avec des classes à 2 et 3 périodes, on risque de ne pas enregistrer dans le cas type_note=last les notes de la classe à 2 périodes
						$reg_note_min = my_strtolower($reg_note);
						if (preg_match ("/^[0-9\.\,]{1,}$/", $reg_note)) {
							$reg_note = str_replace(",", ".", "$reg_note");
							//$test_num = settype($reg_note,"double");
							if (($reg_note >= 0) and ($reg_note <= 20)) {
								$elev_statut = '';
								$sql_note=", note='$reg_note', statut='' ";
							} else {
								$reg_note = '0';
								$elev_statut = '-';
								$sql_note=", note='0', statut='-' ";
							}
						} elseif ($reg_note_min == '-') {
							$reg_note = '0';
							$elev_statut = '-';
							$sql_note=", note='0', statut='-' ";
						} elseif ($reg_note_min == "disp") {
							$reg_note = '0';
							$elev_statut = 'disp';
							$sql_note=", note='0', statut='disp' ";
						} elseif ($reg_note_min == "abs") {
							$reg_note = '0';
							$elev_statut = 'abs';
							$sql_note=", note='0', statut='abs' ";
						} elseif ($reg_note == "") {
							$enregistrement_note = 'no';
						} else {
							$reg_note = '0';
							$elev_statut = '-';
							$sql_note=", note='0', statut='-' ";
						}
					}

					if ($reg_app != "") {
						$sql="SELECT * FROM aid_appreciations WHERE (login='$reg_login' AND periode='$periode_num' and id_aid = '$aid_id' and indice_aid='$indice_aid');";
						$test_eleve_app_query = mysqli_query($GLOBALS["mysqli"], $sql);
						$test = mysqli_num_rows($test_eleve_app_query);
						if ($test != 0) {
							$sql="UPDATE aid_appreciations SET appreciation = '" . $reg_app . "'".$sql_note." WHERE login='$reg_login' AND periode='$periode_num' AND id_aid = '$aid_id' AND indice_aid='$indice_aid';";
						} else {
							$sql="INSERT INTO aid_appreciations SET login='$reg_login', periode='$periode_num', id_aid = '$aid_id', indice_aid='$indice_aid', appreciation = '" . $reg_app . "'".$sql_note;
						}
						$reg_data2 = mysqli_query($GLOBALS["mysqli"], $sql);
					} else {
						$reg_data2 = 'ok';
					}
				}
				else {
					$temoin_periode_close="y";
				}
			}
		}
		if($temoin_periode_close=="y") {
			echo "<font color='red'>La période est close pour l'utilisateur $reg_login !</font><br />\n";
		}
		else {
			if (!$reg_data2) {
				echo "<font color='red'>Erreur lors de la modification de données de l'utilisateur $reg_login !</font><br />\n";
			} else {
				echo "Les données de l'utilisateur $reg_login ont été modifiées avec succès !<br />\n";
			}
		}
	}

	echo "</p>\n";
	echo "<p><a href='saisie_aid.php?indice_aid=$indice_aid&aid_id=$aid_id'>Accéder à la page de saisie des notes/appréciations AID pour vérification</a>";
}
require("../lib/footer.inc.php");
?>
