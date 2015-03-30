<?php
/*
*
* Copyright 2001, 2015 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

$step=isset($_POST['step']) ? $_POST['step'] : (isset($_GET['step']) ? $_GET['step'] : NULL);
$num_fich=isset($_POST['num_fich']) ? $_POST['num_fich'] : (isset($_GET['num_fich']) ? $_GET['num_fich'] : NULL);
$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

if((isset($mode))&&($mode=='telech')&&(isset($num_fich))) {
	check_token();

	$num_fich_test=preg_replace("/[^0-9_]/", "", $num_fich);
	if($num_fich_test!=$num_fich) {
		$msg="Le numéro de fichier transmis est invalide&nbsp;: $num_fich<br />";
		unset($step);
	}
	else {
		$tempdir=get_user_temp_directory();
		if(!$tempdir){
			$msg="Il semble que le dossier temporaire de l'utilisateur ".$_SESSION['login']." ne soit pas défini!?<br />";
			unset($step);
		}
		else {
			$nom_fichier="export_groupes_".$num_fich.".xml";
			header('Content-type: text/html');
			header('Content-Disposition: attachment; filename="'.$nom_fichier.'"');
			$contenu=readfile("../temp/".$tempdir."/export_groupes_".$num_fich.".xml");
			echo $contenu;
			die();

			//unset($step);
			//unset($num_fich);
		}
	}
}

//**************** EN-TETE *****************
$titre_page = "Export XML Groupes";
require_once("../lib/header.inc.php");
//************** FIN EN-TETE *****************

//debug_var();

$ajout_ariane="";
if(isset($step)) {
	$ajout_ariane=" | <a href='".$_SERVER['PHP_SELF']."'>Effectuer un autre export</a>";
}

echo "<p class='bold'><a href='../classes/index.php'>Retour vers Classes et enseignements</a></p>

<h2>Export des groupes au format XML pour Sconet</h2>";

/*
// Boucler sur les élèves pour extraire leurs groupes
$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec, classes c WHERE e.login=jec.login AND jec.id_classe=c.id ORDER BY c.classe, e.nom, e.prenom;";
// On n'aura pas assurément la bonne liste d'élèves en terme d'order dans le cas des élèves changeant de classe.
// Pour les élèves changeant de classe, on va avoir trop de groupes.
// Ou alors faire une extraction pour tel numéro de période ou la dernière période

// Mieux vaut la dernière période
*/

$tranche=100;

if(!isset($step)) {
	echo "<p>Sconet peut recevoir un fichier XML des affectations élèves dans les groupes de Gepi.<br />
La présente page permet de générer cet export en parcourant les élèves par tranches de $tranche.</p>
<p><a href='".$_SERVER['PHP_SELF']."?step=1".add_token_in_url()."'>Effectuer l'export XML</a></p>";
}
elseif($step==1) {
	check_token(false);

	$sql="TRUNCATE tempo2;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="INSERT INTO tempo2 (SELECT DISTINCT e.login, e.login FROM eleves e, j_eleves_classes jec, classes c WHERE e.login=jec.login AND jec.id_classe=c.id ORDER BY c.classe, e.nom, e.prenom);";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);

	$tempdir=get_user_temp_directory();
	if(!$tempdir){
		echo "<p style='color:red'>Il semble que le dossier temporaire de l'utilisateur ".$_SESSION['login']." ne soit pas défini!?</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$num_fich=strftime("%Y%m%d%H%M%S")."_".rand();

	// Numéro d'envoi à enregistrer dans setting
	$num_envoi=getSettingValue("NumExportGroupesSconet");
	if($num_envoi=="") {
		$num_envoi=1;
	}
	else {
		$num_envoi++;
	}
	saveSetting("NumExportGroupesSconet", $num_envoi);

	$f=fopen("../temp/".$tempdir."/export_groupes_".$num_fich.".xml", "w+");
	fwrite($f, "<?xml version='1.0' encoding='UTF-8'?>
<IMPORT_ELEVES VERSION=\"1.3\">
	<PARAMETRES>
		<UAJ>".getSettingValue('GepiSchoolRne')."</UAJ>
		<ANNEE_SCOLAIRE>".mb_substr(getSettingValue('gepiYear'), 0, 4)."</ANNEE_SCOLAIRE>
		<DATE_IMPORT>".strftime("%d/%m/%Y")."</DATE_IMPORT>
		<NUM_ENVOI>50218</NUM_ENVOI>
		<LOGICIEL>GEPI</LOGICIEL>
	</PARAMETRES>
	<DONNEES>
		<ELEVES>
		");
	fclose($f);

	echo "<p>Préparation initiale effectuée.</p>
<p><a href='".$_SERVER['PHP_SELF']."?step=2&num_fich=$num_fich".add_token_in_url()."'>Suite</a></p>";

}
else {
	// Filtrage de num_fich
	$num_fich_test=preg_replace("/[^0-9_]/", "", $num_fich);
	if($num_fich_test!=$num_fich) {
		echo "<p style='color:red'>Le numéro de fichier transmis est invalide&nbsp;: $num_fich</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	// Parcourir par tranches la table tempo2

	$tempdir=get_user_temp_directory();
	if(!$tempdir){
		echo "<p style='color:red'>Il semble que le dossier temporaire de l'utilisateur ".$_SESSION['login']." ne soit pas défini!?</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$debut_annee=strftime("%Y-%m-%d", getSettingValue('begin_bookings'));
	$fin_annee=strftime("%Y-%m-%d", getSettingValue('end_bookings'));

	//echo "<p>Fichier : export_groupes_".$num_fich.".xml</p>";
	$f=fopen("../temp/".$tempdir."/export_groupes_".$num_fich.".xml", "a+");

	$sql="SELECT COUNT(col1) AS eff FROM tempo2;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	$lig=mysqli_fetch_object($res);
	if($lig->eff>0) {
		echo "<p>$lig->eff élève(s) restent à extraire.</p><p>Liste des élèves extraits dans cette tranche&nbsp;: ";

		$sql="SELECT col1 AS login FROM tempo2 LIMIT $tranche;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			$cpt=0;
			while($lig=mysqli_fetch_object($res)) {

				$sql="SELECT e.ele_id, e.nom, e.prenom, e.naissance FROM eleves e WHERE login='$lig->login';";
				//echo "$sql<br />";
				$res2=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res2)==0) {
					echo "</p><p><span style='color:red'>ERREUR&nbsp;: $lig->login non trouvé dans la table 'eleves'.</span></p>";
				}
				else {
					if($cpt>0) {
						echo ", ";
					}

					$lig2=mysqli_fetch_object($res2);

					$chaine_eleve="<ELEVE>
			<ELEVE_ID>$lig2->ele_id</ELEVE_ID>
			<NOM>$lig2->nom</NOM>
			<PRENOM>$lig2->prenom</PRENOM>
			<DATE_NAISS>$lig2->naissance</DATE_NAISS>
			<GROUPES>";

					// Récupérer la dernière classe/période de l'élève
					$sql="SELECT id_classe, periode FROM j_eleves_classes WHERE login='$lig->login' ORDER BY periode DESC LIMIT 1;";
					$res_clas=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_clas)==0) {
						// On ne devrait pas passer là avec la façon dont tempo2 est remplie
						echo "<span style='color:orange' title=\"Inscrit dans aucune classe\">$lig2->nom $lig2->prenom</span>";
					}
					else {
						$lig_clas=mysqli_fetch_object($res_clas);

						// Récupérer la liste des groupes de l'élève
						$sql="SELECT jeg.id_groupe FROM j_eleves_groupes jeg WHERE login='".$lig->login."' AND periode='".$lig_clas->periode."';";
						$res_grp=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res_grp)==0) {
							// On ne devrait pas passer là avec la façon dont tempo2 est remplie
							echo "<span style='color:plum' title=\"Inscrit dans aucun enseignement\">$lig2->nom $lig2->prenom</span>";
						}
						else {
							echo "$lig2->nom $lig2->prenom";
							while($lig_grp=mysqli_fetch_object($res_grp)) {
								$info_grp=$lig_grp->id_groupe;

								$chaine_eleve.="				<GROUPE>
					<CODE_GROUPE>$info_grp</CODE_GROUPE>
					<DATE_DEBUT_GROUPE>$debut_annee</DATE_DEBUT_GROUPE>
					<DATE_FIN_GROUPE>$fin_annee</DATE_FIN_GROUPE>
				</GROUPE>";
							}
						}

						$chaine_eleve.="			</GROUPES>
		</ELEVE>
		";

						fwrite($f, $chaine_eleve);

						$cpt++;
					}
				}

				$sql="DELETE FROM tempo2 WHERE col1='$lig->login';";
				$del=mysqli_query($GLOBALS["mysqli"], $sql);
			}

			$step++;
			echo "<p id='p_suite'><a href='".$_SERVER['PHP_SELF']."?step=$step&num_fich=$num_fich".add_token_in_url()."'>Suite</a></p>
<p id='p_suite2' style='display:none'></p>";

			echo "<script type='text/javascript'>
	document.getElementById('p_suite').style.display='none';
	document.getElementById('p_suite2').style.display='';

	function suite(cpt) {
		if(cpt>0) {
			document.getElementById('p_suite2').innerHTML='Suite dans <strong>'+cpt+'</strong>s.';
			cpt--;
			setTimeout('suite('+cpt+')', 1000);
		}
		else {
			document.getElementById('p_suite2').innerHTML='Suite dans <strong>'+cpt+'</strong>s.';
			document.location.replace('".$_SERVER['PHP_SELF']."?step=$step&num_fich=$num_fich".add_token_in_url()."');
		}
	}

	suite(5);
</script>";

		}
	}
	else {
		fwrite($f, "</ELEVES>"
		   . "</DONNEES>"
		   . "</IMPORT_ELEVES>");

		echo "<p>Parcours terminé.</p>
<!--p><a href='../temp/".$tempdir."/export_groupes_".$num_fich.".xml' target='_blank'>Récupérer le fichier XML</a> (<em>effectuer un clic-droit/enregistrer la cible</em>)</p-->
<p><a href='".$_SERVER['PHP_SELF']."?num_fich=$num_fich&amp;mode=telech".add_token_in_url()."' target='_blank'>Récupérer le fichier XML</a></p>";
		// En l'état: clic-droit/enregistrer la cible.

	}
	fclose($f);
}


require("../lib/footer.inc.php");
?>
