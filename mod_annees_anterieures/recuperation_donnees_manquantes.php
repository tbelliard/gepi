<?php
/*
 *
 * Copyright 2001, 2016 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
include("../lib/initialisationsPropel.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

$sql="SELECT 1=1 FROM droits WHERE id='/mod_annees_anterieures/recuperation_donnees_manquantes.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_annees_anterieures/recuperation_donnees_manquantes.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Ajax: Acces aux appreciations et avis des bulletins',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

require_once("./fonctions_annees_anterieures.inc.php");

// Si le module n'est pas activé...
if(!getSettingAOui('active_annees_anterieures')) {
	header("Location: ../logout.php?auto=1");
	die();
}

$msg="";

$step=isset($_POST['step']) ? $_POST['step'] : (isset($_GET['step']) ? $_GET['step'] : NULL);

$debug_import="n";

//**************** EN-TETE *****************
$titre_page = "Récupération de données manquantes";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

echo "<div class='norme'>
	<p class='bold'>
		<a href='./index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>
	</p>
</div>\n";

/*
- Récup NUMIND et TYPE d'après sts_emp_RNE_ANNEE
- Remplissage des champs NUMIND et TYPE dans la table archivage_disciplines ?
*/

$sql="SELECT DISTINCT u.login, u.nom, u.prenom, u.numind, u.type FROM utilisateurs u, j_groupes_professeurs jgp WHERE jgp.login=u.login AND u.statut='professeur' AND (numind='' OR type='') ORDER BY u.nom, u.prenom;";
//echo "$sql<br />";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0){
	echo "<p>Tous les professeurs ont leurs NUMIND et TYPE renseignés.</p>";
	require("../lib/footer.inc.php");
	die();
}
else {
	if(!isset($step)) {
		// style='color:red; margin-top:1em; margin-left:7.5em; text-indent:-7.5em;'
		echo "<p>".mysqli_num_rows($test)." professeur(s) ont leur NUMIND <em>(identifiant STS)</em> ou leur TYPE <em>(\"Emploi Poste Personnel\" ou \"Local\")</em> non renseigné.<br />
Cela posera problème dans le cas où vous souhaiteriez faire remonter les données dans le <strong>Livret Scolaire Lycée</strong> ou dans le <strong>Livret Scolaire Collège</strong> <em>(LSUN)</em>.<br />
Il est recommandé de procéder à l'association professeur/NUMIND avant d'archiver l'année.</p>

<table class='boireaus boireaus_alt'>
	<tr>
		<th>Professeur</th>
		<th>Matières enseignées</th>
		<th>Numind</th>
		<th>Type</th>
		<th>Modifier</th>
	</tr>";
		while($lig=mysqli_fetch_object($test)) {
			echo "
	<tr onmouseover=\"this.style.backgroundColor='white'\" onmouseout=\"this.style.backgroundColor=''\">
		<td style='text-align:left;'>".casse_mot($lig->nom, "maj")." ".casse_mot($lig->prenom, "majf2")."</td>
		<td>".get_chaine_matieres_prof($lig->login)."</td>
		<td>".$lig->numind."</td>
		<td>".$lig->type."</td>
		<td><a href='../utilisateurs/modify_user.php?user_login=".$lig->login."' target='_blank'><img src='../images/edit16.png' class='icone16' alt='Edit' /></a></td>
	</tr>";

		}
		echo "
</table>

<p style='margin-top:1em;'>Les informations manquantes sont probablement dans le fichier <strong>Emplois du temps de STS</strong> <em>(sts_emp_RNE_ANNEE.xml)</em></p>
<p>Veuillez fournir un Export XML réalisé depuis l'application STS-web.<br />
Demandez gentiment à votre secrétaire d'accéder à STS-web et d'effectuer 'Mise à jour/Exports/Emplois du temps'.</p>

<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' id='form_envoi_xml' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<p>Veuillez fournir le fichier XML <b>sts_emp_<i>RNE</i>_<i>ANNEE</i>.xml</b>&nbsp;: </p>
		<p><input type=\"file\" size=\"65\" name=\"xml_file\" id='input_xml_file' style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\"); padding:5px; margin:5px;' />
		<input type=\"hidden\" name=\"step\" value=\"0\" />
		<input type='hidden' name='is_posted' value='yes' />
		<p><input type='submit' id='input_submit' value='Valider' />
		<input type='button' id='input_button' value='Valider' style='display:none;' onclick=\"check_champ_file()\" /></p>
	</fieldset>

	<script type='text/javascript'>
		document.getElementById('input_submit').style.display='none';
		document.getElementById('input_button').style.display='';

		function check_champ_file() {
			fichier=document.getElementById('input_xml_file').value;
			//alert(fichier);
			if(fichier=='') {
				alert('Vous n\'avez pas sélectionné de fichier XML à envoyer.');
			}
			else {
				document.getElementById('form_envoi_xml').submit();
			}
		}
	</script>
</form>\n";

		require("../lib/footer.inc.php");
		die();
	}
	else {

		check_token(false);

		$tempdir=get_user_temp_directory();
		if(!$tempdir){
			echo "<p style='color:red'>Il semble que le dossier temporaire de l'utilisateur ".$_SESSION['login']." ne soit pas défini!?</p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		$post_max_size=ini_get('post_max_size');
		$upload_max_filesize=ini_get('upload_max_filesize');
		$max_execution_time=ini_get('max_execution_time');
		$memory_limit=ini_get('memory_limit');

		if($step==0) {
			$xml_file=isset($_FILES["xml_file"]) ? $_FILES["xml_file"] : NULL;

			if(!is_uploaded_file($xml_file['tmp_name'])) {
				echo "<p style='color:red;'>L'upload du fichier a échoué.</p>\n";

				echo "<p>Les variables du php.ini peuvent peut-être expliquer le problème:<br />\n";
				echo "post_max_size=$post_max_size<br />\n";
				echo "upload_max_filesize=$upload_max_filesize<br />\n";
				echo "</p>\n";

				require("../lib/footer.inc.php");
				die();
			}
			else{
				if(!file_exists($xml_file['tmp_name'])){
					echo "<p style='color:red;'>Le fichier aurait été uploadé... mais ne serait pas présent/conservé.</p>\n";

					echo "<p>Les variables du php.ini peuvent peut-être expliquer le problème:<br />\n";
					echo "post_max_size=$post_max_size<br />\n";
					echo "upload_max_filesize=$upload_max_filesize<br />\n";
					echo "et le volume de ".$xml_file['name']." serait<br />\n";
					echo "\$xml_file['size']=".volume_human($xml_file['size'])."<br />\n";
					echo "</p>\n";

					echo "<p>Il semblerait que l'absence d'extension .XML puisse aussi provoquer ce genre de symptômes.<br />Dans ce cas, ajoutez l'extension et ré-essayez.</p>\n";

					require("../lib/footer.inc.php");
					die();
				}

				echo "<p>Le fichier a été uploadé.</p>\n";
				$source_file=$xml_file['tmp_name'];
				$dest_file="../temp/".$tempdir."/sts.xml";
				$res_copy=copy("$source_file" , "$dest_file");

				if(!$res_copy) {
					echo "<p style='color:red;'>La copie du fichier vers le dossier temporaire a échoué.<br />Vérifiez que l'utilisateur ou le groupe apache ou www-data a accès au dossier temp/$tempdir</p>\n";
					require("../lib/footer.inc.php");
					die();
				}
				else {
					echo "<p>La copie du fichier vers le dossier temporaire a réussi.</p>\n";

					$dest_file="../temp/".$tempdir."/sts.xml";

					$sts_xml=simplexml_load_file($dest_file);
					if(!$sts_xml) {
						echo "<p style='color:red;'>ECHEC du chargement du fichier avec simpleXML.</p>\n";
						require("../lib/footer.inc.php");
						die();
					}

					$nom_racine=$sts_xml->getName();
					if(my_strtoupper($nom_racine)!='STS_EDT') {
						echo "<p style='color:red;'>ERREUR: Le fichier XML fourni n'a pas l'air d'être un fichier XML STS_EMP_&lt;RNE&gt;_&lt;ANNEE&gt;.<br />Sa racine devrait être 'STS_EDT'.</p>\n";
						require("../lib/footer.inc.php");
						die();
					}

					echo "<p>";
					echo "Analyse du fichier pour extraire les informations de la section INDIVIDUS...<br />\n";

					$prof=array();
					$i=0;

					$tab_champs_personnels=array("NOM_USAGE",
					"NOM_PATRONYMIQUE",
					"PRENOM",
					"SEXE",
					"CIVILITE",
					"DATE_NAISSANCE",
					"GRADE",
					"FONCTION");

					$prof=array();
					$i=0;

					foreach($sts_xml->DONNEES->INDIVIDUS->children() as $individu) {
						$prof[$i]=array();

						//echo "<span style='color:orange'>\$individu->NOM_USAGE=".$individu->NOM_USAGE."</span><br />";

						foreach($individu->attributes() as $key => $value) {
							// <INDIVIDU ID="4189" TYPE="epp">
							$prof[$i][my_strtolower($key)]=trim($value);
						}

						// Champs de l'individu
						foreach($individu->children() as $key => $value) {
							if(in_array(my_strtoupper($key),$tab_champs_personnels)) {
								if(my_strtoupper($key)=='SEXE') {
									$prof[$i]["sexe"]=trim(preg_replace("/[^1-2]/","",$value));
								}
								elseif(my_strtoupper($key)=='CIVILITE') {
									$prof[$i]["civilite"]=trim(preg_replace("/[^1-3]/","",$value));
								}
								elseif((my_strtoupper($key)=='NOM_USAGE')||
								(my_strtoupper($key)=='NOM_PATRONYMIQUE')||
								(my_strtoupper($key)=='NOM_USAGE')) {
									$prof[$i][my_strtolower($key)]=trim(preg_replace("/[^A-Za-z -]/","",remplace_accents($value)));
								}
								elseif(my_strtoupper($key)=='PRENOM') {
									$prof[$i][my_strtolower($key)]=trim(preg_replace('/"/','',preg_replace("/'/","",nettoyer_caracteres_nom($value,"a"," -",""))));
								}
								elseif(my_strtoupper($key)=='DATE_NAISSANCE') {
									$prof[$i][my_strtolower($key)]=trim(preg_replace("/[^0-9-]/","",$value));
								}
								elseif((my_strtoupper($key)=='GRADE')||
									(my_strtoupper($key)=='FONCTION')) {
									$prof[$i][my_strtolower($key)]=trim(preg_replace('/"/','',preg_replace("/'/"," ",$value)));
								}
								else {
									$prof[$i][my_strtolower($key)]=trim($value);
								}
								//echo "\$prof[$i][".strtolower($key)."]=".$prof[$i][strtolower($key)]."<br />";
							}
						}

						if(isset($individu->PROFS_PRINC)) {
							$j=0;
							foreach($individu->PROFS_PRINC->children() as $prof_princ) {
								//$prof[$i]["prof_princ"]=array();
								foreach($prof_princ->children() as $key => $value) {
									$prof[$i]["prof_princ"][$j][my_strtolower($key)]=trim(preg_replace('/"/',"",$value));
									$temoin_au_moins_un_prof_princ="oui";
								}
								$j++;
							}
						}

						if(isset($individu->DISCIPLINES)) {
							$j=0;
							foreach($individu->DISCIPLINES->children() as $discipline) {
								foreach($discipline->attributes() as $key => $value) {
									if(my_strtoupper($key)=='CODE') {
										$prof[$i]["disciplines"][$j]["code"]=trim(preg_replace('/"/',"",$value));
										break;
									}
								}

								foreach($discipline->children() as $key => $value) {
									$prof[$i]["disciplines"][$j][my_strtolower($key)]=trim(preg_replace('/"/',"",$value));
								}
								$j++;
							}
						}

						if($debug_import=='y') {
							echo "<pre style='color:green;'><b>Tableau \$prof[$i]&nbsp;:</b>";
							print_r($prof[$i]);
							echo "</pre>";
						}

						$i++;
					}

					if(count($prof)==0) {
						echo "<p style='color:red'>Aucun professeur n'a été trouvé dans votre fichier.</p>\n";
						require("../lib/footer.inc.php");
						die();
					}
					else {
						$tab_prof_gepi=array();
						$tab_numind_prof_gepi=array();
						$sql="SELECT DISTINCT u.login, u.nom, u.prenom, u.numind, u.type FROM utilisateurs u, j_groupes_professeurs jgp WHERE jgp.login=u.login AND u.statut='professeur' ORDER BY u.nom, u.prenom;";
						//echo "$sql<br />";
						$test=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($test)>0){
							$cpt=0;
							while($lig=mysqli_fetch_assoc($test)) {
								$nom_prenom=casse_mot($lig['nom'], "maj")." ".casse_mot($lig['prenom'], "majf2");
								$tab_prof_gepi[$cpt]=$lig;
								$tab_prof_gepi[$cpt]['nom_prenom']=$nom_prenom;
								$tab_numind_prof_gepi[$lig['numind']]['cpt']=$cpt;
								$tab_numind_prof_gepi[$lig['numind']]['type']=$lig['type'];
								$tab_numind_prof_gepi[$lig['numind']]['nom_prenom']=$nom_prenom;
								$cpt++;
							}
						}

/*
echo "tab_prof_gepi<pre>";
print_r($tab_prof_gepi);
echo "</pre>";

echo "tab_numind_prof_gepi<pre>";
print_r($tab_numind_prof_gepi);
echo "</pre>";

echo "prof<pre>";
print_r($prof);
echo "</pre>";
*/

						echo "
<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type=\"hidden\" name=\"step\" value=\"1\" />
		<input type='hidden' name='is_posted' value='yes' />
		<p>Ne sont affichés ci-dessous que les professeurs du fichiers XML non, trouvés dans Gepi ou pour lesquels des données semblent manquantes dans Gepi.<br />
		Si les nom et prénom coïncident entre Gepi et le fichier XML, l'identification devrait être automatique.<br />
		Sinon, effectuez les associations manuellement.</p>
		<table class='boireaus boireaus_alt'>
			<tr>
				<th colspan='4'>Fichier XML</th>
				<th>Base GEPI</th>
			</tr>
			<tr>
				<th>Professeur</th>
				<th>Matières enseignées</th>
				<th>Numind</th>
				<th>Type</th>
				<th>Choix</th>
			</tr>";

						for($loop=0;$loop<count($prof);$loop++) {
							$afficher="y";

							if(array_key_exists("P".$prof[$loop]['id'], $tab_numind_prof_gepi)) {
								// Vérifier si le type est manquant ou différent

								$type=$tab_numind_prof_gepi["P".$prof[$loop]['id']]['type'];
								if($type==$prof[$loop]['type']) {
									//Si le type est identique
									$afficher="n";
								}
							}
							else {
								//
							}


							if($afficher=="y") {
								$liste_matieres_enseignes="";

								if(isset($prof[$loop]['disciplines'])) {
									for($loop2=0;$loop2<count($prof[$loop]['disciplines']);$loop2++) {
										if($liste_matieres_enseignes!="") {
											$liste_matieres_enseignes.=", ";
										}
										if(isset($prof[$loop]['disciplines'][$loop2]['libelle_court'])) {
											$liste_matieres_enseignes.=$prof[$loop]['disciplines'][$loop2]['libelle_court'];
										}
									}
								}

								echo "
			<tr onmouseover=\"this.style.backgroundColor='white'\" onmouseout=\"this.style.backgroundColor=''\">
				<td style='text-align:left;'>".casse_mot($prof[$loop]['nom_usage'], "maj")." ".casse_mot($prof[$loop]['prenom'], "majf2")."</td>
				<td>".$liste_matieres_enseignes."</td>
				<td>".$prof[$loop]['id']."</td>
				<td>".$prof[$loop]['type']."</td>
				<td>
					<select name='login_gepi[".$prof[$loop]['id']."]' id='login_gepi_".$prof[$loop]['id']."'>
						<option value=''>---</option>";
								for($loop2=0;$loop2<count($tab_prof_gepi);$loop2++) {
									$selected="";
									if("P".$prof[$loop]['id']==$tab_prof_gepi[$loop2]['numind']) {
										$selected=" selected";
									}
									elseif(casse_mot($prof[$loop]['nom_usage'], "maj")." ".casse_mot($prof[$loop]['prenom'], "majf2")==$tab_prof_gepi[$loop2]['nom_prenom']) {
										$selected=" selected";
									}
									echo "
						<option value='".$tab_prof_gepi[$loop2]['login']."'".$selected.">".$tab_prof_gepi[$loop2]['nom_prenom']."</option>";
								}
								echo "
					</select>
				</td>
			</tr>";
							}
						}
						echo "
		</table>
		<p><input type='submit' id='input_submit' value='Valider' /></p>
	</fieldset>
</form>";


					}
				}
			}
		}
		else {
			// Valider les imports
			check_token(false);

			$login_gepi=isset($_POST['login_gepi']) ? $_POST['login_gepi'] : NULL;

			if(!isset($login_gepi)) {
				echo "<p>Aucun rapprochement n'a été validé.<br /><a href='".$_SERVER['PHP_SELF']."'>Retour</a></p>\n";
				require("../lib/footer.inc.php");
				die();
			}

			//debug_var();

			echo "<p>Prise en compte des validations...</p>\n";

			$tempdir=get_user_temp_directory();
			if(!$tempdir){
				echo "<p style='color:red'>Il semble que le dossier temporaire de l'utilisateur ".$_SESSION['login']." ne soit pas défini!?</p>\n";
				require("../lib/footer.inc.php");
				die();
			}

			$dest_file="../temp/".$tempdir."/sts.xml";

			$sts_xml=simplexml_load_file($dest_file);
			if(!$sts_xml) {
				echo "<p style='color:red;'>ECHEC du chargement du fichier avec simpleXML.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}

			$nom_racine=$sts_xml->getName();
			if(my_strtoupper($nom_racine)!='STS_EDT') {
				echo "<p style='color:red;'>ERREUR: Le fichier XML fourni n'a pas l'air d'être un fichier XML STS_EMP_&lt;RNE&gt;_&lt;ANNEE&gt;.<br />Sa racine devrait être 'STS_EDT'.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}

			echo "<p>Analyse du fichier pour extraire les informations de la section INDIVIDUS...<br />\n";

			$prof=array();
			$i=0;

			$tab_champs_personnels=array("NOM_USAGE",
			"NOM_PATRONYMIQUE",
			"PRENOM",
			"SEXE",
			"CIVILITE",
			"DATE_NAISSANCE",
			"GRADE",
			"FONCTION");

			$prof=array();
			$prof2=array();
			$i=0;

			foreach($sts_xml->DONNEES->INDIVIDUS->children() as $individu) {
				$prof[$i]=array();

				//echo "<span style='color:orange'>\$individu->NOM_USAGE=".$individu->NOM_USAGE."</span><br />";

				foreach($individu->attributes() as $key => $value) {
					// <INDIVIDU ID="4189" TYPE="epp">
					$prof[$i][my_strtolower($key)]=trim($value);
					if(my_strtolower($key)=="id") {
						$prof2[trim($value)]=$i;
					}
				}

				// Champs de l'individu
				foreach($individu->children() as $key => $value) {
					if(in_array(my_strtoupper($key),$tab_champs_personnels)) {
						if(my_strtoupper($key)=='SEXE') {
							$prof[$i]["sexe"]=trim(preg_replace("/[^1-2]/","",$value));
						}
						elseif(my_strtoupper($key)=='CIVILITE') {
							$prof[$i]["civilite"]=trim(preg_replace("/[^1-3]/","",$value));
						}
						elseif((my_strtoupper($key)=='NOM_USAGE')||
						(my_strtoupper($key)=='NOM_PATRONYMIQUE')||
						(my_strtoupper($key)=='NOM_USAGE')) {
							$prof[$i][my_strtolower($key)]=trim(preg_replace("/[^A-Za-z -]/","",remplace_accents($value)));
						}
						elseif(my_strtoupper($key)=='PRENOM') {
							$prof[$i][my_strtolower($key)]=trim(preg_replace('/"/','',preg_replace("/'/","",nettoyer_caracteres_nom($value,"a"," -",""))));
						}
						elseif(my_strtoupper($key)=='DATE_NAISSANCE') {
							$prof[$i][my_strtolower($key)]=trim(preg_replace("/[^0-9-]/","",$value));
						}
						elseif((my_strtoupper($key)=='GRADE')||
							(my_strtoupper($key)=='FONCTION')) {
							$prof[$i][my_strtolower($key)]=trim(preg_replace('/"/','',preg_replace("/'/"," ",$value)));
						}
						else {
							$prof[$i][my_strtolower($key)]=trim($value);
						}
						//echo "\$prof[$i][".strtolower($key)."]=".$prof[$i][strtolower($key)]."<br />";
					}
				}

				if(isset($individu->PROFS_PRINC)) {
					$j=0;
					foreach($individu->PROFS_PRINC->children() as $prof_princ) {
						//$prof[$i]["prof_princ"]=array();
						foreach($prof_princ->children() as $key => $value) {
							$prof[$i]["prof_princ"][$j][my_strtolower($key)]=trim(preg_replace('/"/',"",$value));
							$temoin_au_moins_un_prof_princ="oui";
						}
						$j++;
					}
				}

				if(isset($individu->DISCIPLINES)) {
					$j=0;
					foreach($individu->DISCIPLINES->children() as $discipline) {
						foreach($discipline->attributes() as $key => $value) {
							if(my_strtoupper($key)=='CODE') {
								$prof[$i]["disciplines"][$j]["code"]=trim(preg_replace('/"/',"",$value));
								break;
							}
						}

						foreach($discipline->children() as $key => $value) {
							$prof[$i]["disciplines"][$j][my_strtolower($key)]=trim(preg_replace('/"/',"",$value));
						}
						$j++;
					}
				}

				if($debug_import=='y') {
					echo "<pre style='color:green;'><b>Tableau \$prof[$i]&nbsp;:</b>";
					print_r($prof[$i]);
					echo "</pre>";
				}

				$i++;
			}

			echo "<p>Traitement des rapprochements demandés...<br />\n";
			$nb_reg=0;
			foreach($login_gepi as $id_prof => $current_login) {
				if($current_login!="") {
					if(isset($prof2[$id_prof])) {
						$i=$prof2[$id_prof];
						$sql="UPDATE utilisateurs SET numind='P".$id_prof."', type='".$prof[$i]['type']."' WHERE login='".$current_login."';";
						//echo "$sql<br />";
						$update=mysqli_query($mysqli, $sql);
						if(!$update) {
							echo "<span style='color:red;'>ERREUR lors de la requête&nbsp;: <br />$sql</span><br />\n";
						}
						else {
							$nb_reg++;
						}
					}
				}
			}
			if($nb_reg>0) {
				echo $nb_reg." correction(s) effectuée(s).<br />";
			}
			echo "<p><a href='".$_SERVER['PHP_SELF']."'>Retour</a></p>\n";
		}
	}
}

require("../lib/footer.inc.php");
?>
