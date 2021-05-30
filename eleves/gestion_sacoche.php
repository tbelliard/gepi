<?php
/*
 *
 * Copyright 2001, 2020, 2021 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau, Romain Neil
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


$sql = "SELECT 1=1 FROM droits WHERE id='/eleves/gestion_sacoche.php';";
$test = mysqli_query($GLOBALS["mysqli"], $sql);
if (mysqli_num_rows($test) == 0) {
	$sql = "INSERT INTO droits SET id='/eleves/gestion_sacoche.php',
	administrateur='V',
	professeur='F',
	cpe='F',
	scolarite='F',
	eleve='F',
	responsable='F',
	secours='F',
	autre='F',
	description='Gestion identifiants SACoche',
	statut='';";
	$insert = mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//**************** EN-TETE *****************
$titre_page = "Élèves : Identifiants SACoche";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

// Etape...
$step = isset($_POST['step']) ? $_POST['step'] : (isset($_GET['step']) ? $_GET['step'] : NULL);

// On va uploader les fichiers XML dans le tempdir de l'utilisateur (administrateur, ou scolarité pour les màj Sconet)
$tempdir = get_user_temp_directory();
if (!$tempdir) {
	echo "<p style='color:red'>Il semble que le dossier temporaire de l'utilisateur " . $_SESSION['login'] . " ne soit pas défini&nbsp;!?</p>\n";
	// Il ne faut pas aller plus loin...
	// SITUATION A GERER
	require("../lib/footer.inc.php");
	die();
}


// =======================================================
// EST-CE ENCORE UTILE?
if (isset($_GET['nettoyage'])) {
	check_token(false);
	//echo "<h1 align='center'>Suppression des CSV</h1>\n";
	echo "<p class=bold><a href='";
	if (isset($_SESSION['ad_retour_imports_communs'])) {
		echo $_SESSION['ad_retour_imports_communs'];
	} else {
		echo "index.php";
	}
	echo "'> <img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
	echo "<a href='" . $_SERVER['PHP_SELF'] . "'> | Autre import</a></p>\n";
	//echo "</div>\n";

	echo "<h2>Suppression des XML</h2>\n";

	echo "<p>Si des fichiers XML existent, ils seront supprimés...</p>\n";
	$tabfich = array("users_sacoche.xml");

	for ($i = 0; $i < count($tabfich); $i++) {
		if (file_exists("../temp/" . $tempdir . "/$tabfich[$i]")) {
			echo "<p>Suppression de $tabfich[$i]... ";
			if (unlink("../temp/" . $tempdir . "/$tabfich[$i]")) {
				echo "réussie.</p>\n";
			} else {
				echo "<font color='red'>Echec!</font> Vérifiez les droits d'écriture sur le serveur.</p>\n";
			}
		}
	}

	require("../lib/footer.inc.php");
	die();
} // =======================================================
else {
	echo "<p class=bold><a href='";
	if (isset($_SESSION['ad_retour_imports_communs'])) {
		// On peut venir de l'index init_xml, de la page de conversion ou de la page de mise à jour Sconet
		echo $_SESSION['ad_retour_imports_communs'];
	} else {
		echo "index.php";
	}
	echo "'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

	if (isset($step)) {
		echo " | <a href='" . $_SERVER['PHP_SELF'] . "'>Autre import</a>";
	}

	echo " | <a href='" . $_SERVER['PHP_SELF'] . "?nettoyage=oui" . add_token_in_url() . "'>Suppression des fichiers XML existants</a>";
	echo "</p>\n";
	//echo "</div>\n";

	echo "<center><h3 class='gepi'>Import Users</h3></center>\n";

	//if(!isset($_POST['is_posted'])){
	if (!isset($step)) {

		echo "<p>Cette page permet d'importer les identifiants SACoche pour pouvoir importer dans SACoche des informations de Gepi <em>(appréciations, notes,...)</em>.</p>
			
			<form enctype='multipart/form-data' action='" . $_SERVER['PHP_SELF'] . "' id='form_envoi_xml' method='post'>
				<fieldset class='fieldset_opacite50'>
					" . add_token_field() . "
					<p>Veuillez fournir le fichier users.xml&nbsp;:<br />
					<input type=\"file\" size=\"65\" name=\"users_xml_file\" id='input_xml_file' style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\"); padding:5px; margin:5px;' /></p>
					<input type='hidden' name='step' value='0' />
					<input type='hidden' name='is_posted' value='yes' />
					<p><input type='submit' id='input_submit' value='Valider' />
					<input type='button' id='input_button' value='Valider' style='display:none;' onclick=\"check_champ_file()\" /></p>
				</fieldset>

				<script type='text/javascript'>
					document.getElementById('input_submit').style.display='none';
					document.getElementById('input_button').style.display='';

					function check_champ_file() {
						let fichier = document.getElementById('input_xml_file').value;
						//alert(fichier);
						if(fichier === '') {
							alert('Vous n\'avez pas sélectionné de fichier XML à envoyer.');
						} else {
							document.getElementById('form_envoi_xml').submit();
						}
					}
				</script>

				<p style='margin-left:3em; text-indent:-3em;'><em>NOTE&nbsp;:</em> Le fichier users.xml de SACoche peut être récupéré niveau par niveu dans SACoche suivant le cheminement que voici&bnsp;:<br />
				MENU/Import de données/Transfert de saisies depuis SACoche/<br />
				Choisir le niveau et exporter.<br />
				Dézipper pour récupérer le fichier users.xml</p>
			</form>\n";

	} else {
		check_token(false);
		$post_max_size = ini_get('post_max_size');
		$upload_max_filesize = ini_get('upload_max_filesize');
		$max_execution_time = ini_get('max_execution_time');
		$memory_limit = ini_get('memory_limit');

		if ($step == 0) {
			$xml_file = isset($_FILES["users_xml_file"]) ? $_FILES["users_xml_file"] : NULL;

			if (!is_uploaded_file($xml_file['tmp_name'])) {
				echo "<p style='color:red;'>L'upload du fichier a échoué.</p>\n";

				echo "<p>Les variables du php.ini peuvent peut-être expliquer le problème:<br />\n";
				echo "post_max_size=$post_max_size<br />\n";
				echo "upload_max_filesize=$upload_max_filesize<br />\n";
				echo "</p>\n";

				// Il ne faut pas aller plus loin...
				// SITUATION A GERER
				require("../lib/footer.inc.php");
				die();
			} else {
				if (!file_exists($xml_file['tmp_name'])) {
					echo "<p style='color:red;'>Le fichier aurait été uploadé... mais ne serait pas présent/conservé.</p>\n";

					echo "<p>Les variables du php.ini peuvent peut-être expliquer le problème:<br />\n";
					echo "post_max_size=$post_max_size<br />\n";
					echo "upload_max_filesize=$upload_max_filesize<br />\n";
					echo "et le volume de " . $xml_file['name'] . " serait<br />\n";
					echo "\$xml_file['size']=" . volume_human($xml_file['size']) . "<br />\n";
					echo "</p>\n";

					echo "<p>Il semblerait que l'absence d'extension .XML ou .ZIP puisse aussi provoquer ce genre de symptômes.<br />Dans ce cas, ajoutez l'extension et ré-essayez.</p>\n";

					// Il ne faut pas aller plus loin...
					// SITUATION A GERER
					require("../lib/footer.inc.php");
					die();
				}

				echo "<p>Le fichier a été uploadé.</p>\n";

				//$source_file=stripslashes($xml_file['tmp_name']);
				$source_file = $xml_file['tmp_name'];
				$dest_file = "../temp/" . $tempdir . "/users_sacoche.xml";
				$res_copy = copy("$source_file", "$dest_file");

				if (!$res_copy) {
					echo "<p style='color:red;'>La copie du fichier vers le dossier temporaire a échoué.<br />Vérifiez que l'utilisateur ou le groupe apache ou www-data a accès au dossier temp/$tempdir</p>\n";
					// Il ne faut pas aller plus loin...
					// SITUATION A GERER
					require("../lib/footer.inc.php");
					die();
				} else {
					echo "<p>La copie du fichier vers le dossier temporaire a réussi.</p>\n";

					libxml_use_internal_errors(true);
					$users_xml = simplexml_load_file($dest_file);
					if (!$users_xml) {
						echo "<p style='color:red;'>ECHEC du chargement du fichier avec simpleXML.</p>\n";
						echo "<p><a href='" . $_SERVER['PHP_SELF'] . "'>Téléverser un autre fichier</a></p>\n";
						require("../lib/footer.inc.php");
						die();
					}

					$nom_racine = $users_xml->getName();
					if (strtolower($nom_racine) != 'users') {
						echo "<p style='color:red;'>ERREUR: Le fichier XML fourni n'a pas l'air d'être un fichier XML Users.<br />Sa racine devrait être 'users'.<br />La racine est ici&nbsp;: " . $nom_racine . "</p>\n";
						require("../lib/footer.inc.php");
						die();
					}


					echo "<p>Analyse du fichier pour extraire les informations de la section users.<br />\n";

					/*
					<users>
						<user id="1234"
						sconet="987654_ele_id"
						elenoet="4635"
						reference="1234567AB_INE"
						nom="DUNORD"
						prenom="Edgar" />
					*/

					$tab_champs_eleve = array("id",
						"sconet",
						"elenoet",
						"reference",
						"nom",
						"prenom"
					);

					$i = 0;
					$eleves = array();
					//$objet_eleves=($users_xml->users);
					$objet_eleves = $users_xml;
					foreach ($objet_eleves->children() as $eleve) {

						$tmp_eleve = array();

						foreach ($eleve->attributes() as $key => $value) {
							//echo "$key=".$value."<br />";
							$tmp_eleve[strtolower($key)] = trim($value);
						}

						// Rechercher les élèves dont l'identifiant a changé
						if ((isset($tmp_eleve['elenoet'])) && (isset($tmp_eleve['id']))) {
							$sql = "SELECT * FROM eleves WHERE elenoet='" . $tmp_eleve['elenoet'] . "' AND id_sacoche!='" . $tmp_eleve['id'] . "';";
							$test = mysqli_query($mysqli, $sql);
							if (mysqli_num_rows($test) > 0) {
								$i++;
								//echo "<p><b>Elève $i</b><br />";
								$eleves[$i] = array();

								$eleves[$i] = $tmp_eleve;

								$lig = mysqli_fetch_assoc($test);
								$eleves[$i]['gepi'] = $lig;
							}
						}
					}

					if (count($eleves) == 0) {
						echo "<p>Aucune différence n'a été trouvée entre le XML fourni et le contenu de la table 'eleves'.<br />
							Pour les élèves du XML, <strong>la base Gepi est à jour</strong>.</p>";
					} else {
						echo "<form action='" . $_SERVER['PHP_SELF'] . "' method='post'>
	<fieldset class='fieldset_opacite50'>
		" . add_token_field() . "
		<p>Voici les élèves pour lesquels des différences existent&nbsp;:</p>
		<table class='boireaus boireaus_alt'>
			<thead>
				<tr>
					<th rowspan='2'>
						<a href=\"javascript:ToutCocher();changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' title='Tout cocher' /></a> / <a href=\"javascript:ToutDecocher();changement();\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' title='Tout décocher' /></a>
					</th>
					<th colspan='4' style='background-color:lightblue'>Contenu Gepi</th>
					<th colspan='3' style='background-color:plum'>Contenu du XML</th>
				</tr>
				<tr>
					<th style='background-color:lightblue'>Nom</th>
					<th style='background-color:lightblue'>Prénom</th>
					<th style='background-color:lightblue'>Elenoet</th>
					<th style='background-color:lightblue'>Id SACoche</th>

					<th style='background-color:plum'>Id SACoche</th>
					<th style='background-color:plum'>Nom</th>
					<th style='background-color:plum'>Prénom</th>
				</tr>
			</thead>
			<tbody>";
						$i = 0;
						foreach ($eleves as $current_eleve) {
							echo "
				<tr>
					<td><input type='checkbox' name='eleve[]' id='eleve_" . $i . "' value='" . $current_eleve['elenoet'] . "|" . $current_eleve['id'] . "' onchange=\"checkbox_change(this.id)\" /></td>
					<td><label for='eleve_" . $i . "' id='texte_eleve_" . $i . "'>" . $current_eleve['gepi']['nom'] . "</label></td>
					<td><label for='eleve_" . $i . "'>" . $current_eleve['gepi']['prenom'] . "</label></td>
					<td><label for='eleve_" . $i . "'>" . $current_eleve['gepi']['elenoet'] . "</label></td>
					<td><label for='eleve_" . $i . "'>" . $current_eleve['gepi']['id_sacoche'] . "</label></td>
					
					<td><label for='eleve_" . $i . "'>" . $current_eleve['id'] . "</label></td>
					<td><label for='eleve_" . $i . "'>" . $current_eleve['nom'] . "</label></td>
					<td><label for='eleve_" . $i . "'>" . $current_eleve['prenom'] . "</label></td>";
							$i++;
						}

						echo "
			</tbody>
		</table>
		<input type='hidden' name='step' value='1' />
		<p><input type='submit' value='Valider' /></p>
	</fieldset>
</form>

<script type='text/javascript'>
	function ToutCocher() {
		let item = document.getElementsByTagName('input');
		for(let i = 0; i < item.length; i++) {
			if(item[i].getAttribute('type') === 'checkbox') {
				item[i].checked = true;
				checkbox_change(item[i].getAttribute('id'));
			}
		}
	}

	function ToutDecocher() {
		let item=document.getElementsByTagName('input');
		for(let i = 0;i < item.length; i++) {
			if(item[i].getAttribute('type') === 'checkbox') {
				item[i].checked=false;
				checkbox_change(item[i].getAttribute('id'));
			}
		}
	}
</script>";

						echo js_checkbox_change_style("checkbox_change", 'texte_', "y");

					}

				}
			}
		} else {
			check_token(false);

			$eleve = isset($_POST['eleve']) ? $_POST['eleve'] : NULL;

			if ((!isset($eleve)) || (!is_array($eleve))) {
				echo "<p>Aucune mise à jour n'a été demandée.</p>";
			} else {
				$nb_maj = 0;
				foreach ($eleve as $current_corresp) {
					$tab = explode('|', $current_corresp);
					if ((isset($tab[1])) && (preg_match('/^[0-9]+$/', $tab[0])) && (preg_match('/^[0-9]+$/', $tab[1]))) {
						$sql = "UPDATE eleves SET id_sacoche='" . $tab[1] . "' WHERE elenoet='" . $tab[0] . "';";
						$update = mysqli_query($mysqli, $sql);
						if (!$update) {
							echo "Erreur lors de la mise à jour de l'identifiant SACoche pour l'élève n°" . $tab[0] . ".<br />";
						} else {
							$nb_maj++;
						}
					}
				}
				echo "<p>" . $nb_maj . " mise(s) à jour effectuée(s).</p>";
			}


		}


	}
}
require("../lib/footer.inc.php");
?>
