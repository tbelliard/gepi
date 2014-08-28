<?php
/*
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

//======================================================================================

$sql="SELECT 1=1 FROM droits WHERE id='/groupes/correction_inscriptions_grp_csv.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/groupes/correction_inscriptions_grp_csv.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Correction des inscriptions dans des groupes d après un CSV',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

//======================================================================================
// Section checkAccess() à décommenter en prenant soin d'ajouter le droit correspondant:
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
//======================================================================================

//$projet=isset($_POST['projet']) ? $_POST['projet'] : (isset($_GET['projet']) ? $_GET['projet'] : NULL);
//$is_posted=isset($_POST['is_posted']) ? $_POST['is_posted'] : NULL;
$action=isset($_POST['action']) ? $_POST['action'] : NULL;
$col_login=isset($_POST['col_login']) ? $_POST['col_login'] : NULL;
$col_elenoet=isset($_POST['col_elenoet']) ? $_POST['col_elenoet'] : NULL;
$col_option=isset($_POST['col_option']) ? $_POST['col_option'] : NULL;


//**************** EN-TETE *****************
$titre_page = "Correction membres groupes";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc.php");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

//debug_var();

if(!isset($action)) {
	echo "<p class='bold'><a href='../classes/index.php'>Retour à la Gestion des classes</a></p>\n";

	echo "<h2>Choix du CSV</h2>\n";

	echo "<p>Veuillez fournir un fichier CSV au format... approprié... pour corriger les inscriptions dans les groupes existants.</p>\n";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	echo "<input type='hidden' name='action' value='upload_file' />\n";
	//echo "<input type='hidden' name='is_posted' value='1' />\n";
	echo "<p><input type=\"file\" size=\"80\" name=\"csv_file\" />\n";
	echo "<p><input type='submit' value='Valider' />\n";
	echo "</form>\n";

	echo "<p style='margin-top:1em;'><i>NOTES&nbsp;:</i></p>
<ul>
	<li>
		<p>La présente page est destinée à effectuer des inscriptions/désinscriptions d'élèves de groupes existants en fournissant un CSV des inscriptions.<br />
		Le format de CSV envisagé correspond à ce qui est produit par le module Genèse des classes à savoir quelque chose comme&nbsp;:<br />
		NOM;PRENOM;NAISSANCE;ELENOET;CLASSE;AGL1;AGL2;ALL1;ALL2;ATHLE;DECP3;ESP2;LATIN;Redoublement;Depart<br />
		Dans cet exemple, ELENOET sera la clé pour identifier l'élève.<br />
		Les autres clés valides sont LOGIN pour le moment et dans le futur ELE_ID, INE.<br />
		Les noms des colonnes doivent coïncider avec les noms de matières dans Gepi.<br />
		Il est proposé après l'envoi du fichier de choisir les colonnes à prendre en compte.</p>
	</li>
	<li>
		<p>Un élève sera considéré comme suivant la matière si la colonne correspondante est non vide.</p>
	</li>
	<li>
		<p>Si un élève suit telle matière, il sera inscrit dans <strong>tous</strong> les enseignements de la matière (<em>tous les enseignements associés à la classe de l'élève tout de même</em>).</p>
	</li>
</ul>\n";

	require("../lib/footer.inc.php");
	die();
}

//================================================

if((isset($action))&&($action=="upload_file")) {
	echo "<p class='bold'><a href='".$_SERVER['PHP_SELF']."'>Retour</a></p>\n";

	echo "<h2>Envoi du CSV</h2>\n";

	$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;

	if(!is_uploaded_file($csv_file['tmp_name'])) {
		echo "<p style='color:red;'>L'upload du fichier a échoué.</p>\n";

		echo "<p>Les variables du php.ini peuvent peut-être expliquer le problème:<br />\n";
		echo "post_max_size=$post_max_size<br />\n";
		echo "upload_max_filesize=$upload_max_filesize<br />\n";
		echo "</p>\n";

		require("../lib/footer.inc.php");
		die();
	}

	if(!file_exists($csv_file['tmp_name'])){
		echo "<p style='color:red;'>Le fichier aurait été uploadé... mais ne serait pas présent/conservé.</p>\n";

		echo "<p>Les variables du php.ini peuvent peut-être expliquer le problème:<br />\n";
		echo "post_max_size=$post_max_size<br />\n";
		echo "upload_max_filesize=$upload_max_filesize<br />\n";
		echo "et le volume de ".$xml_file['name']." serait<br />\n";
		echo "\$xml_file['size']=".volume_human($xml_file['size'])."<br />\n";
		echo "</p>\n";

		require("../lib/footer.inc.php");
		die();
	}

	echo "<p>Le fichier a été uploadé.</p>\n";

	$tempdir=get_user_temp_directory();
	$source_file=$csv_file['tmp_name'];
	$dest_file="../temp/".$tempdir."/correction_inscriptions_grp.csv";
	$res_copy=copy("$source_file" , "$dest_file");

	// Le nom est ok. On ouvre le fichier
	$fp=fopen($dest_file,"r");

	if(!$fp) {
		// Aie : on n'arrive pas à ouvrir le fichier... Pas bon.
		echo "<p>Impossible d'ouvrir le fichier CSV !</p>\n";
		echo "<p><a href='".$_SERVER['PHP_SELF']."'>Cliquer ici</a> pour recommencer !</p>\n";

		require("../lib/footer.inc.php");
		die();
	}

	// Lecture de la ligne d'entête du CSV
	$ligne=trim(fgets($fp, 4096));
	$tabligne_entete=explode(";",$ligne);

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>
<p>Vous devez choisir une colonne pour l'identifiant de l'élève parmi LOGIN ou ELENOET.<br />
Vous devez également cocher les colonnes d'options à traiter.<br />
Les élèves seront inscrits/désinscrits des groupes des matières choisies dans les enseignements des classes où les élèves sont inscrits.</p>
<input type='submit' value='Valider' />
<input type='hidden' name='action' value='inscriptions_desinscriptions' />
<table class='boireaus boireaus_alt'>
	<thead>
		<tr>
			<th rowspan='2'>Identifiant</th>
			<th>Login</th>
			<th><input type='radio' name='col_login' value='' checked /></th>";
	for($loop=0;$loop<count($tabligne_entete);$loop++) {
		echo "
			<th><input type='radio' name='col_login' value='$loop' /></th>";
	}
	echo "
		</tr>
		<tr>
			<th>Elenoet</th>
			<th><input type='radio' name='col_elenoet' value='' checked /></th>";
	for($loop=0;$loop<count($tabligne_entete);$loop++) {
		echo "
			<th><input type='radio' name='col_elenoet' value='$loop' /></th>";
	}
	echo "
		</tr>
		<tr>
			<th colspan='3'>Colonnes options à prendre en compte</th>";
	for($loop=0;$loop<count($tabligne_entete);$loop++) {
		echo "
			<th><input type='checkbox' name='col_option[]' value='$loop' /></th>";
	}
	echo "
		</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan='3'>Ligne d'entête</td>";
	for($loop=0;$loop<count($tabligne_entete);$loop++) {
		echo "
			<td>".$tabligne_entete[$loop]."</td>";
	}
	echo "
		</tr>";

	$cpt=1;
	while (!feof($fp)) {
		$ligne = fgets($fp, 4096);
		if(trim($ligne)!="") {
			$tabligne=explode(";",$ligne);
			echo "
		<tr>
			<td colspan='3'>Ligne $cpt</td>";
			for($loop=0;$loop<count($tabligne);$loop++) {
				echo "
			<td>".$tabligne[$loop]."</td>";
			}
			echo "
		</tr>";
			$cpt++;
		}
	}
	echo "
	</tbody>
</table>
<input type='submit' value='Valider' />
</form>";


	require("../lib/footer.inc.php");
	die();

}

//================================================

if((isset($action))&&($action=="inscriptions_desinscriptions")) {
	echo "<p class='bold'><a href='".$_SERVER['PHP_SELF']."'>Retour</a></p>\n";

	echo "<h2>Inscriptions désinscriptions d'après le CSV</h2>\n";

	if(($col_login=="")&&($col_elenoet=="")) {
		echo "<p style='color:red'>Vous n'avez pas choisi de colonne identifiant LOGIN ou ELENOET.</p>";

		require("../lib/footer.inc.php");
		die();
	}

	$tempdir=get_user_temp_directory();
	$dest_file="../temp/".$tempdir."/correction_inscriptions_grp.csv";

	$fp=fopen($dest_file,"r");

	if(!$fp) {
		// Aie : on n'arrive pas à ouvrir le fichier... Pas bon.
		echo "<p>Impossible d'ouvrir le fichier CSV !</p>\n";
		echo "<p><a href='".$_SERVER['PHP_SELF']."'>Cliquer ici</a> pour recommencer !</p>\n";

		require("../lib/footer.inc.php");
		die();
	}

	$ligne=trim(fgets($fp, 4096));
	echo "<p>Ligne d'entête&nbsp;:<br />$ligne</p>";
	$tabligne_entete=explode(";",$ligne);

	if($col_elenoet!="") {
		echo "<p>Identifiant choisi&nbsp;: ELENOET<br />La colonne identifiant choisie est la n°".$col_elenoet."&nbsp;: ".$tabligne_entete[$col_elenoet]."</p>";
		$col_identifiant=$col_elenoet;
		$nature_identifiant='elenoet';
	}
	else {
		echo "<p>Identifiant choisi&nbsp;: LOGIN<br />La colonne identifiant choisie est la n°".$col_login."&nbsp;: ".$tabligne_entete[$col_login]."</p>";
		$col_identifiant=$col_login;
		$nature_identifiant='login';
	}

	echo "<p>Les colonnes à traiter sont les colonnes&nbsp;:<br />";
	for($loop=0;$loop<count($col_option);$loop++) {
		echo $col_option[$loop]."&nbsp;: ".$tabligne_entete[$col_option[$loop]]."<br />";
	}
	echo "</p>";

	echo "<p>Les inscriptions vont apparaitre en vert et les désinscriptions en rouge.<br />Si aucune inscription verte ou rouge n'apparait, c'est que les inscriptions sont conformes au fichier CSV.</p>";
	$cpt=1;
	$tab_classe=array();
	while (!feof($fp)) {
		$ligne = fgets($fp, 4096);
		if(trim($ligne)!="") {
			$tabligne=explode(";",$ligne);
			if($tabligne[$col_identifiant]!="") {
				// Recherche du login associé:
				$sql="SELECT login, nom, prenom FROM eleves WHERE $nature_identifiant='".$tabligne[$col_identifiant]."';";
				$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_ele)==0) {
					echo "<p style='color:red'>Élève non trouvé pour l'identifiant ".$tabligne[$col_identifiant].".<br />".$ligne."</p>";
				}
				elseif(mysqli_num_rows($res_ele)>1) {
					echo "<p style='color:red'>ANOMALIE&nbsp;: Plus d'un élève semble correspondre à l'identifiant proposé.<br />".$ligne."</p>";
				}
				else {
					$lig_ele=mysqli_fetch_object($res_ele);

					// Recherche des classes de l'élève:
					$sql="SELECT DISTINCT id_classe FROM j_eleves_classes WHERE login='".$lig_ele->login."';";
					$res_clas=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_clas)==0) {
						echo "<p style='color:red'>Élève $lig_ele->nom $lig_ele->prenom inscrit dans aucune classe.<br />".$ligne."</p>";
					}
					else {
						echo $lig_ele->nom." ".$lig_ele->prenom."&nbsp;: ";
						while($lig_clas=mysqli_fetch_object($res_clas)) {
							if(!isset($tab_classe[$lig_clas->id_classe])) {
								$tab_classe[$lig_clas->id_classe]=get_nom_classe($lig_clas->id_classe);
							}
							echo "(".$tab_classe[$lig_clas->id_classe].") ";
							$nb_per=get_period_number($lig_clas->id_classe);
							//echo "(".$nb_per.") ";
							for($loop=0;$loop<count($col_option);$loop++) {
								// Pour chaque classe, recherche des groupes de l'option indiquée
								$sql="SELECT DISTINCT jgc.id_groupe FROM j_groupes_classes jgc, 
														j_groupes_matieres jgm 
													WHERE jgc.id_classe='".$lig_clas->id_classe."' AND 
														jgc.id_groupe=jgm.id_groupe AND 
														jgm.id_matiere='".$tabligne_entete[$col_option[$loop]]."';";
								//echo "$sql<br />";
								$res_grp=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($res_grp)>0) {
									while($lig_grp=mysqli_fetch_object($res_grp)) {
										$sql="SELECT 1=1 FROM j_eleves_groupes WHERE login='".$lig_ele->login."' AND id_groupe='".$lig_grp->id_groupe."';";
										//echo "$sql<br />";
										$res_ele_grp=mysqli_query($GLOBALS["mysqli"], $sql);
										if((mysqli_num_rows($res_ele_grp)==0)&&($tabligne[$col_option[$loop]]!="")) {
											for($loop_per=1;$loop_per<$nb_per+1;$loop_per++) {
												$sql="INSERT INTO j_eleves_groupes SET login='".$lig_ele->login."', id_groupe='".$lig_grp->id_groupe."', periode='$loop_per';";
												//echo "$sql<br />";
												$insert=mysqli_query($GLOBALS["mysqli"], $sql);
												if(!$insert) {
													echo "<span style='color:red'>ERREUR</span> ";
												}
											}
											echo "<span style='color:green' title=\"Inscription de l'élève dans les groupes de ".$tabligne_entete[$col_option[$loop]]."\">".$tabligne_entete[$col_option[$loop]]."</span> ";
										}
										elseif((mysqli_num_rows($res_ele_grp)>0)&&($tabligne[$col_option[$loop]]=="")) {
											for($loop_per=1;$loop_per<$nb_per+1;$loop_per++) {
												if (test_before_eleve_removal($lig_ele->login, $lig_grp->id_groupe, $loop_per)) {
													$sql="DELETE FROM j_eleves_groupes WHERE login='".$lig_ele->login."' AND id_groupe='".$lig_grp->id_groupe."' AND periode='$loop_per';";
													//echo "$sql<br />";
													$del=mysqli_query($GLOBALS["mysqli"], $sql);
													if(!$del) {
														echo "<span style='color:red'>ERREUR</span> ";
													}
													echo "<span style='color:red' title=\"Désinscription de l'élève des groupes de ".$tabligne_entete[$col_option[$loop]]."\">".$tabligne_entete[$col_option[$loop]]."</span> ";
												}
												else {
													echo "<span style='color:plum' title=\"Désinscription impossible de l'élève des groupes de ".$tabligne_entete[$col_option[$loop]]." en période $loop_per.\">".$tabligne_entete[$col_option[$loop]]." (bulletins non vides P$loop_per)</span> ";
												}
											}
										}
									}
								}
								else {
									if($tabligne[$col_option[$loop]]!="") {
										echo "<span style='color:orange' title=\"L'élève est inscrit(e) en ".$tabligne_entete[$col_option[$loop]].", mais aucun groupe n'existe pour la classe.
Vous devriez créer un groupe de ".$tabligne_entete[$col_option[$loop]]." en ".$tab_classe[$lig_clas->id_classe]." et refaire l'import du CSV.\">Aucun groupe de ".$tabligne_entete[$col_option[$loop]]." trouvé</span> ";
									}
								}
							}
						}
						echo "<br />";
					}
				}
			}
			$cpt++;
		}
	}


	require("../lib/footer.inc.php");
	die();
}

require("../lib/footer.inc.php");
?>
