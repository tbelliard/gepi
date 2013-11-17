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

$sql="SELECT 1=1 FROM droits WHERE id='/mod_genese_classes/import_options.php';";
$test=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_genese_classes/import_options.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Genèse des classes: Import options depuis CSV',
statut='';";
$insert=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
}

//======================================================================================
// Section checkAccess() à décommenter en prenant soin d'ajouter le droit correspondant:
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
//======================================================================================

$projet=isset($_POST['projet']) ? $_POST['projet'] : (isset($_GET['projet']) ? $_GET['projet'] : NULL);


//**************** EN-TETE *****************
$titre_page = "Genèse classe: Import CSV des options";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc.php");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

//debug_var();

if((!isset($projet))||($projet=="")) {
	echo "<p class='bold'><a href='index.php'>Retour</a></p>\n";

	echo "<p style='color:red'>ERREUR: Le projet n'est pas choisi.</p>\n";
	require("../lib/footer.inc.php");
	die();
}

//echo "<div class='noprint'>\n";
echo "<p class='bold'><a href='index.php?projet=$projet'>Retour</a> | <a href='".$_SERVER['PHP_SELF']."?projet=$projet'>Autre import</a>";
//echo "</div>\n";

$afficher_listes=isset($_POST['afficher_listes']) ? $_POST['afficher_listes'] : (isset($_GET['afficher_listes']) ? $_GET['afficher_listes'] : NULL);

$action=isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : NULL);

if($action=="upload_file") {
	echo "</p>\n";

	echo "<h2>Projet $projet</h2>\n";

	$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;

	// Le nom est ok. On ouvre le fichier
	$fp=fopen($csv_file['tmp_name'],"r");

	if(!$fp) {
		// Aie : on n'arrive pas à ouvrir le fichier... Pas bon.
		echo "<p>Impossible d'ouvrir le fichier CSV !</p>\n";
		echo "<p><a href='".$_SERVER['PHP_SELF']."?projet=$projet'>Cliquer ici</a> pour recommencer !</p>\n";

		require("../lib/footer.inc.php");
		die();
	}
	else {

		$sql="DELETE FROM gc_eleves_options WHERE projet='$projet';";
		$del=mysqli_query($GLOBALS["___mysqli_ston"], $sql);

		$tab_non_option=array('NOM','PRENOM','SEXE','NAISSANCE','LOGIN','ELENOET','ELE_ID','INE','EMAIL','CLASSE');

		// Lecture de la ligne d'entête du CSV
		$ligne=trim(fgets($fp, 4096));
		$tabligne_entete=explode(";",$ligne);

		$tab_options=array();

		$tabligne_entete_inverse=array();
		for($i=0;$i<count($tabligne_entete);$i++) {
			$tabligne_entete_inverse["$tabligne_entete[$i]"]=$i;

			if(!in_array($tabligne_entete[$i],$tab_non_option)) {


				// VERIFIER AUSSI SI L'OPTION PRéSUMéE EST DANS gc_options
				$sql="SELECT 1=1 FROM gc_options WHERE projet='$projet' AND opt='".$tabligne_entete[$i]."';";
				$test=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
				if(mysqli_num_rows($test)>0) {
					if(!in_array($tabligne_entete[$i],$tab_options)) {
						$tab_options[]=$tabligne_entete[$i];
					}
				}
			}
		}

		$cle="";
		if(in_array('LOGIN',$tabligne_entete)) {
			$cle='login';
		}
		elseif(in_array('ELENOET',$tabligne_entete)) {
			$cle='elenoet';
		}
		elseif(in_array('ELE_ID',$tabligne_entete)) {
			$cle='ele_id';
		}
		elseif(in_array('INE',$tabligne_entete)) {
			$cle='no_gep';
		}

		if($cle=="") {
			echo "<p style='color:red'>ERREUR: Le fichier ne contient aucune des clés LOGIN, ELENOET, ELE_ID ou INE.</p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		echo "<table class='boireaus' border='1' summary='Options importées'>\n";
		echo "<tr><th>Login</th><th>Options</th></tr>\n";
		$val_login_precedent="";
		$alt=1;
		$cpt=0;
		$nat_num = array();
		while (!feof($fp)) {
			$ligne = fgets($fp, 4096);
			if(trim($ligne)!="") {
				$tabligne=explode(";",$ligne);

				if($cle=='no_gep') {
					$valeur_cle=$tabligne[$tabligne_entete_inverse['INE']];
				}
				else {
					$valeur_cle=$tabligne[$tabligne_entete_inverse[strtoupper($cle)]];
				}

				$val_login="";
				// Si la clé n'est pas LOGIN, il faut récupérer le login d'après la table eleves... A FAIRE
				if($cle=="") {
					$sql="SELECT 1=1 FROM eleves WHERE login='".$valeur_cle."';";
					$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
					if(mysqli_num_rows($res)==1) {
						$val_login=$valeur_cle;
					}
					elseif(mysqli_num_rows($res)==0) {
						echo "<tr><td colspan='2'>\n";
						echo "<span style='color:red'>Aucun enregistrement n'a été trouvé dans la table 'eleves' pour le login correspondant à la ligne '<span style='color:blue'>$ligne</span>'</span><br />\n";
						echo "</td></tr>\n";
					}
					else {
						echo "<tr><td colspan='2'>\n";
						echo "<span style='color:red'>Plusieurs enregistrement ont été trouvés dans la table 'eleves' pour le login '<span style='color:blue'>$valeur_cle</span>' correspondant à la ligne '<span style='color:blue'>$ligne</span>'.C'est une grosse anomalie.</span><br />\n";
						echo "</td></tr>\n";
					}
				}
				else {
					//$sql="SELECT login FROM eleves WHERE ".strtolower($tabligne_entete_inverse["$cle"])."='".$valeur_cle."';";
					$sql="SELECT login FROM eleves WHERE ".$cle."='".$valeur_cle."';";
					//$sql="SELECT login FROM eleves WHERE ".$cle."='".$valeur_cle."' AND (e.date_sortie IS NULL OR e.date_sortie NOT LIKE '20%');";
					$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
					if(mysqli_num_rows($res)==1) {
						$lig_tmp=mysqli_fetch_object($res);
						$val_login=$lig_tmp->login;
					}
					elseif(mysqli_num_rows($res)==0) {
						echo "<tr><td colspan='2'>\n";
						echo "<span style='color:red'>Aucun enregistrement n'a été trouvé dans la table 'eleves' pour le login correspondant à la ligne '<span style='color:blue'>$ligne</span>'</span><br />\n";
						echo "</td></tr>\n";
					}
					else {
						echo "<tr><td colspan='2'>\n";
						echo "<span style='color:red'>Plusieurs valeurs ont été trouvées dans la table 'eleves' pour le login correspondant à la ligne '<span style='color:blue'>$ligne</span>'</span><br />\n";
						echo "</td></tr>\n";
					}
				}

				if($val_login!="") {
					if($val_login!=$val_login_precedent) {
						if($cpt>0) {
							echo "</td></tr>\n";
						}
						$alt=$alt*(-1);
						echo "<tr class='lig$alt'><td style='text-align:left;'><b>$val_login</b>&nbsp;:</td><td style='text-align:left;'>\n";
					}
					$chaine_opt_eleve="";
					for($i=0;$i<count($tab_options);$i++) {
						if($tabligne[$tabligne_entete_inverse["$tab_options[$i]"]]==1) {

							echo $tab_options[$i]." ";
							$chaine_opt_eleve.="|".$tab_options[$i];
							//$sql="INSERT INTO gc_eleves_options SET projet='$projet', login='$val_login', opt='".$tab_options[$i]."';";
							//echo "$sql<br />\n";
							//$res=mysql_query($sql);
						}
					}
					if($chaine_opt_eleve!="") {
						$chaine_opt_eleve.="|";
						$sql="INSERT INTO gc_eleves_options SET projet='$projet', login='$val_login', liste_opt='".$chaine_opt_eleve."';";
						//echo "$sql<br />\n";
						$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
					}
				}

				$val_login_precedent=$val_login;
				$cpt++;
			}
		}
		echo "</td></tr>\n";
		echo "</table>\n";
		echo "<p>Import achevé.</p>\n";
	}
}
else {
	echo "</p>\n";

	echo "<h2>Projet $projet</h2>\n";

	echo "<p>Veuillez fournir un fichier CSV au format... approprié... pour importer les options futures des élèves.</p>\n";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	echo "<input type='hidden' name='action' value='upload_file' />\n";
	echo "<input type='hidden' name='projet' value='$projet' />\n";
	echo "<p><input type=\"file\" size=\"80\" name=\"csv_file\" />\n";
	echo "<p><input type='submit' value='Valider' />\n";
	echo "</form>\n";

	echo "<p><i>NOTES&nbsp;:</i></p>\n";
	echo "<ul>\n";
	echo "<li><p>Les options préalablement saisies pour ce projet seront perdues.<p></li>\n";
	echo "<li><p>Le format du CSV pourra être par exemple&nbsp;:<br />NOM;PRENOM;NAISSANCE;ELENOET;CLASSE;AGL1;AGL2;ALL1;ALL2;ATHLE;DECP3;ESP2;LATIN;Redoublement;Depart<br />Dans cet exemple, ELENOET sera la clé pour identifier l'élève.<br />Les autres clés valides sont LOGIN, ELE_ID, INE.<br />Les noms des colonnes doivent coïncider avec les noms de matières dans Gepi.</p><p>Le plus simple pour obtenir ce fichier consiste à suivre les étapes dans l'ordre.<br />Lors de l'étape 2 'Lister les options actuelles des élèves', un fichier CSV au bon format est généré.</p></li>\n";
	echo "</ul>\n";
}


require("../lib/footer.inc.php");
?>
