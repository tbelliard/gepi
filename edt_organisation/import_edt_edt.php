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

// Resume session

$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}


$sql="SELECT 1=1 FROM droits WHERE id='/edt_organisation/import_edt_edt.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/edt_organisation/import_edt_edt.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Import des EDT depuis un XML EDT',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$msg="";
$action=isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : "");

function get_corresp_edt($type, $nom) {
	$retour="";
	$sql="SELECT nom_gepi FROM edt_corresp WHERE champ='$type' AND nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $nom)."';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$lig=mysqli_fetch_object($res);
		$retour=$lig->nom_gepi;
	}
	return $retour;
}

function get_id_groupe_from_tab_ligne($tab) {
	$retour="";

	if((isset($tab['classe']))&&(isset($tab['prof_nom']))&&(isset($tab['prof_prenom']))&&(isset($tab['mat_code']))) {
		$chaine_nom_edt=$tab['classe']."|".$tab['prof_nom']."|".$tab['prof_prenom']."|".$tab['mat_code'];
		$retour=get_corresp_edt('choix_id_groupe', $chaine_nom_edt);
	}
	return $retour;
}

// Fonction utilisée pour renseigner edt_corresp2 avec les correspondances id_groupe, nom de regroupement EDT
// La table edt_corresp2 est utilisée dans groupes/maj_inscript_ele_d_apres_edt.php
function enregistre_corresp_EDT_classe_matiere_GEPI_id_groupe($id_groupe, $nom_groupe_edt, $mat_code_edt, $ecraser="n") {
	global $debug_import_edt;

	$sql="SELECT * FROM edt_corresp2 WHERE id_groupe='".$id_groupe."' AND nom_groupe_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $nom_groupe_edt)."';";
	if($debug_import_edt=="y") {
		echo htmlentities($sql)."<br />\n";
	}
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {

		if($ecraser=="n") {
			$sql="SELECT * FROM edt_corresp2 WHERE id_groupe='".$id_groupe."' AND nom_groupe_edt!='".mysqli_real_escape_string($GLOBALS["mysqli"], $nom_groupe_edt)."';";
			if($debug_import_edt=="y") {
				echo htmlentities($sql)."<br />\n";
			}
			$test=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test)>0) {
				$lig=mysqli_fetch_object($test);
				if($debug_import_edt=="y") {
					echo "Groupe n°$id_groupe déjà associé à ".$lig->nom_groupe_edt."<br />\n";
				}
			}
			else {
				$sql="INSERT INTO edt_corresp2 SET id_groupe='".$id_groupe."', nom_groupe_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $nom_groupe_edt)."', mat_code_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $mat_code_edt)."';";
				if($debug_import_edt=="y") {
					echo htmlentities($sql)."<br />\n";
				}
				$insert=mysqli_query($GLOBALS["mysqli"], $sql);
			}
		}
		else {
			$sql="UPDATE edt_corresp2 SET nom_groupe_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $nom_groupe_edt)."', mat_code_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $mat_code_edt)."' WHERE id_groupe='".$id_groupe."';";
			if($debug_import_edt=="y") {
				echo htmlentities($sql)."<br />\n";
			}
			$insert=mysqli_query($GLOBALS["mysqli"], $sql);
		}
	}
	if($debug_import_edt=="y") {
		echo "==============================<br />\n";
	}
}
// A FAIRE: Pouvoir afficher les correspondances enregistrées dans edt_corresp2
// REMARQUE: On peut avoir plusieurs noms de regroupements EDT associés à un enseignement.
//           Cas un peu bizarre de la mise à jour partielle du nom de groupe [3ALL1GR.1]
//           en [GR_3C3D_BIL] sur une partie seulement des cours d'ALL1 et AGL1 bilangues du EXP_COURS.xml

$sql="CREATE TABLE IF NOT EXISTS edt_corresp (
id int(11) NOT NULL AUTO_INCREMENT,
champ VARCHAR(100) NOT NULL DEFAULT '',
nom_edt VARCHAR(255) NOT NULL DEFAULT '',
nom_gepi VARCHAR(255) NOT NULL DEFAULT '',
PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

$sql="CREATE TABLE IF NOT EXISTS edt_lignes (
id int(11) NOT NULL AUTO_INCREMENT,
numero varchar(255) NOT NULL default '',
classe varchar(255) NOT NULL default '',
mat_code varchar(255) NOT NULL default '',
mat_libelle varchar(255) NOT NULL default '',
prof_nom varchar(255) NOT NULL default '',
prof_prenom varchar(255) NOT NULL default '',
salle varchar(255) NOT NULL default '',
jour varchar(255) NOT NULL default '',
h_debut varchar(255) NOT NULL default '',
duree varchar(255) NOT NULL default '',
frequence varchar(10) NOT NULL default '',
alternance varchar(10) NOT NULL default '',
effectif varchar(255) NOT NULL default '',
modalite varchar(255) NOT NULL default '',
co_ens varchar(255) NOT NULL default '',
pond varchar(255) NOT NULL default '',
traitement varchar(100) NOT NULL default '',
details_cours VARCHAR(255) NOT NULL DEFAULT '',
PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

// ALTER TABLE edt_lignes ADD traitement VARCHAR( 100 ) NOT NULL DEFAULT '' AFTER pond;
// ALTER TABLE edt_lignes ADD id_groupe INT( 11 ) NOT NULL DEFAULT '0' AFTER traitement;
// ALTER TABLE edt_lignes CHANGE id_groupe details_cours VARCHAR(255) NOT NULL DEFAULT '';


$sql="CREATE TABLE IF NOT EXISTS edt_corresp2 (
id int(11) NOT NULL AUTO_INCREMENT,
id_groupe int(11) NOT NULL,
mat_code_edt VARCHAR(255) NOT NULL DEFAULT '',
nom_groupe_edt VARCHAR(255) NOT NULL DEFAULT '',
PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

if(isset($_GET['afficher_details_groupe_gepi'])) {
	if((isset($_GET['id_groupe']))&&(preg_match("/[0-9]{1,}/", $_GET['id_groupe']))) {
		$info_grp=get_info_grp($_GET['id_groupe']);
		$tab_ele=get_eleves_from_groupe($_GET['id_groupe']);

		if((isset($tab_ele['users']))&&(count($tab_ele['users'])>0)) {
			echo "<div style='margin:0.5em;'>";
			echo "<p class='bold'>$info_grp <a href='../groupes/edit_group.php?id_groupe=".$_GET['id_groupe']."' target='_blank' title=\"Voir le groupe/enseignement dans un nouvel onglet.\"><img src='../images/edit16.png' class='icone16' alt='Éditer' /></a></p><p>";
			foreach($tab_ele['users'] as $current_login => $current_ele) {
				echo $current_ele['nom']." ".$current_ele['prenom']."<br />";
			}
			echo "<span class='bold'>Effectif&nbsp;: ".count($tab_ele['users'])."</span>";
			echo "</p>";
			echo "</div>";
		}
		else {
			echo "<p class='bold'>$info_grp</p><p>Aucun élève dans le groupe ou groupe non associé au prof&nbsp;?</p>";
		}
	}
	else {
		echo "<p style='color:red'>Identifiant de groupe invalide.</p>";
	}

	die();
}

if(($action=="editer_corresp")&&(isset($_GET['vider']))) {
	check_token();

	$tab_champs=array("matiere", "classe", "groupe", "salle", "jour", "prof", "h_debut", "frequence");
	if(in_array($_GET['vider'], $tab_champs)) {
		$sql="DELETE FROM edt_corresp WHERE champ='".$_GET['vider']."';";
		$del=mysqli_query($GLOBALS["mysqli"], $sql);
		$msg.="Correspondances '".$_GET['vider']."' supprimées.<br />";
	}
}

if(($action=="editer_corresp")&&(isset($_POST['suppr']))) {
	check_token();

	$cpt_suppr=0;
	$suppr=$_POST['suppr'];
	for($loop=0;$loop<count($suppr);$loop++) {
		$sql="DELETE FROM edt_corresp WHERE id='".$suppr[$loop]."';";
		$del=mysqli_query($GLOBALS["mysqli"], $sql);
		if($del) {
			$cpt_suppr++;
		}
		else {
			$msg.="Erreur lors de la suppression de l'association n°".$suppr[$loop].".<br />";
		}
	}
	$msg.=$cpt_suppr." correspondance(s) supprimée(s).<br />";
}

if((isset($_GET['rechercher_groupes_possibles']))&&(isset($_GET['num']))) {
	if(preg_match("/^[0-9]{1,}/", $_GET['num'])) {

		$sql="SELECT * FROM edt_lignes WHERE id='".$_GET['num']."';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			echo "<p>Aucun enregistrement pour la ligne n°".$_GET['num']."</p>";
		}
		else {
			$ligne=mysqli_fetch_assoc($res);

			$matiere=get_corresp_edt("matiere", $ligne['mat_code']);
			/*
			$prof=get_corresp_edt("prof", $ligne['prof_nom']." ".$ligne['prof_prenom']);
			$classe=get_corresp_edt("classe", $ligne['classe']);
			$salle=get_corresp_edt("salle", $ligne['salle']);
			$jour=get_corresp_edt("jour", $ligne['jour']);
			$h_debut=get_corresp_edt("h_debut", $ligne['h_debut']);
			$duree=get_corresp_edt("duree", $ligne['duree']);
			$frequence=get_corresp_edt("frequence", $ligne['frequence']);
			*/
			$prof=$ligne['prof_nom']." ".$ligne['prof_prenom'];
			$classe=$ligne['classe'];
			$salle=$ligne['salle'];
			$jour=$ligne['jour'];
			$h_debut=$ligne['h_debut'];
			$duree=$ligne['duree'];
			$frequence=$ligne['frequence'];

			$login_prof=get_corresp_edt("prof", $ligne['prof_nom']." ".$ligne['prof_prenom']);

			echo "<p style='margin:0.5em;' class='fieldset_opacite50'>Cours&nbsp;: $classe<br />
			Matière : $matiere<br />
			Professeur : $prof<br />
			Salle : $salle<br />
			Le $jour à $h_debut pour une durée de $duree en semaine $frequence.</p>";

			$sql = "SELECT DISTINCT jgm.id_groupe
						FROM j_groupes_matieres jgm, j_groupes_classes jgc, classes c, groupes g
						WHERE (" .
						"jgm.id_matiere='".$matiere."'
						AND jgm.id_groupe=g.id
						AND jgm.id_groupe=jgc.id_groupe
						AND jgc.id_classe=c.id) ".
						"ORDER BY c.classe, g.name, g.description;" ;
			$res_grp=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_grp)==0) {
				echo "<p>Aucun groupe associé à la matière $matiere.</p>";
			}
			else {
				if($login_prof!="") {
					$grp_prof=array();
					$sql = "SELECT jgp.id_groupe
						FROM j_groupes_professeurs jgp
						WHERE login = '" . $login_prof . "';" ;
					$res_prof=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_prof)>0) {
						while($lig_prof=mysqli_fetch_object($res_prof)) {
							$grp_prof[]=$lig_prof->id_groupe;
						}
					}
				}

				echo "<p>Groupes associés à la matière $matiere&nbsp;:</p>
				<ul>";
				while($lig_grp=mysqli_fetch_object($res_grp)) {
					if(in_array($lig_grp->id_groupe, $grp_prof)) {
						echo "<li style='color:blue;'>".get_info_grp($lig_grp->id_groupe)."</li>";
					}
					else {
						echo "<li>".get_info_grp($lig_grp->id_groupe)."</li>";
					}
				}
				echo "</ul>";
			}
		}
	}
	else {
		echo "Numéro d'enregistrement invalide.";
	}
	die();
}

$sql="SELECT 1=1 FROM edt_corresp;";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
$nb_reg_edt_corresp=mysqli_num_rows($test);

$sql="SELECT 1=1 FROM edt_lignes;";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
$nb_reg_edt_lignes=mysqli_num_rows($test);

$javascript_specifique[] = "lib/tablekit";
$utilisation_tablekit="ok";
//**************** EN-TETE *****************
$titre_page = "Import EDT EDT";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
//debug_var();

$debug_import_edt="n";

echo "<p class='bold'><a href='index_edt.php'> <img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
if($nb_reg_edt_corresp>0) {echo " | <a href='".$_SERVER['PHP_SELF']."?action=editer_corresp'>Editer les correspondances enregistrées</a> ";}
if($nb_reg_edt_lignes>0) {echo " | <a href='".$_SERVER['PHP_SELF']."?action=rapprochements'>Effectuer les rapprochements d'après le dernier XML envoyé</a>";}
if($nb_reg_edt_lignes>0) {echo " | <a href='".$_SERVER['PHP_SELF']."?action=remplir_edt_cours".add_token_in_url()."'>Remplir l'EDT d'après le dernier XML envoyé et d'après les rapprochements effectués</a>";}

if($action!="") {echo " | <a href='".$_SERVER['PHP_SELF']."'> Autre import </a>";}

$edt_edt_last_upload=getSettingValue("edt_edt_last_upload");
if($edt_edt_last_upload!="") {
	echo " <em title=\"Le dernier fichier EXP_COURS.xml uploadé/déposé sur le serveur date du ".strftime("%d/%m/%Y à %H:%M:%S", $edt_edt_last_upload).".\">(dernier upload ".strftime("%d/%m/%Y à %H:%M:%S", $edt_edt_last_upload).")</em>";
}
echo "</p>

<h2>Import des EDT depuis un XML d'EDT</h2>";

// On va uploader les fichiers XML dans le tempdir de l'utilisateur (administrateur, ou scolarité pour les màj Sconet)
$tempdir=get_user_temp_directory();
if(!$tempdir){
	echo "<p style='color:red'>Il semble que le dossier temporaire de l'utilisateur ".$_SESSION['login']." ne soit pas défini!?</p>\n";
	require("../lib/footer.inc.php");
	die();
}

if($action=="") {
	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' id='form_envoi_xml' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<p>
			Veuillez fournir l'export EXP_COURS.xml d'EDT&nbsp;:<br />
			<input type=\"file\" size=\"65\" name=\"xml_file\" id='input_xml_file' class='fieldset_opacite50' style='padding:5px; margin:5px;' /><br />
			<input type='hidden' name='action' value='upload' />
		</p>
		<p>
			<input type='submit' id='input_submit' value='Valider' />
			<input type='button' id='input_button' value='Valider' style='display:none;' onclick=\"check_champ_file()\" />
		</p>
	</fieldset>

	<p style='margin-top:1em;'><em>NOTES&nbsp;:</em></p>
	<ul>
		<li>
			Le fichier d'export EDT doit être obtenu en veillant à sélectionner toutes les classes<br />
			<em>(on obtient assez facilement l'export d'une classe seulement)</em><br />
			via le menu <strong>Fichier/Autres/Export texte</strong>.<br />
			Dans la fenêtre obtenue, sélectionner un export <strong>Cours</strong>.<br />
			Pour le type, sélectionner <strong>XML</strong>.<br />
			En bas à droite, cocher <strong>Nom complet des groupes et des parties</strong> et enfin cliquer sur <strong>Exporter</strong>.
		</li>
		<li>
			Le remplissage de l'emploi du temps s'effectue en trois phases&nbsp;:<br />
			<ol>
				<li>Upload d'un fichier EXP_COURS.xml</li>
				<li>Rapprochement des noms EDT et des noms Gepi, des associations groupes, jours, salles,...</li>
				<li>Le remplissage proprement dit.<br />
				Une partie des cours est identifiée automatiquement.<br />
				Les indéterminés nécessitent un choix <em>(généralement quand il y a plusieurs groupes d'une même matière etque le rapprochement n'a pas été fait (voir la liste des noms de regroupements EDT associés au groupe en éditant le groupe dans <strong>Gestion des classes/Gestion des enseignements/Tel_enseignement</strong>))</em>.<br />
				Lorsqu'un premier import a déjà été effectué, il est possible de re-faire en un clic les mêmes choix de résolution d'indétermination que lors de l'import précédent.</li>
			</ol>
		</li>
	</ul>

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
</form>";
	require("../lib/footer.inc.php");
	die();
}
elseif($action=="upload") {
	check_token(false);
	$post_max_size=ini_get('post_max_size');
	$upload_max_filesize=ini_get('upload_max_filesize');
	$max_execution_time=ini_get('max_execution_time');
	$memory_limit=ini_get('memory_limit');

	$xml_file = isset($_FILES["xml_file"]) ? $_FILES["xml_file"] : NULL;

	if(!is_uploaded_file($xml_file['tmp_name'])) {
		echo "<p style='color:red;'>L'upload du fichier a échoué.</p>\n";

		echo "<p>Les variables du php.ini peuvent peut-être expliquer le problème:<br />\n";
		echo "post_max_size=$post_max_size<br />\n";
		echo "upload_max_filesize=$upload_max_filesize<br />\n";
		echo "</p>\n";

		require("../lib/footer.inc.php");
		die();
	}

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
	$dest_file="../temp/".$tempdir."/edt_cours.xml";
	$res_copy=copy("$source_file" , "$dest_file");

	if(!$res_copy){
		echo "<p style='color:red;'>La copie du fichier vers le dossier temporaire a échoué.<br />Vérifiez que l'utilisateur ou le groupe apache ou www-data a accès au dossier temp/$tempdir</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	echo "<p>La copie du fichier vers le dossier temporaire a réussi.</p>\n";

	$cours_xml=simplexml_load_file($dest_file);
	if(!$cours_xml) {
		echo "<p style='color:red;'>ECHEC du chargement du fichier avec simpleXML.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$nom_racine=$cours_xml->getName();
	if(my_strtoupper($nom_racine)!='TABLE') {
		echo "<p style='color:red;'>ERREUR: Le fichier XML fourni n'a pas l'air d'être un fichier XML EXP_COURS.<br />Sa racine devrait être 'TABLE'.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	/*
	<TABLE nom="Cours">
		...
		<Cours numero="24">
			<NUMERO>24</NUMERO>
			<DUREE>1h00</DUREE>
			<FREQUENCE>H</FREQUENCE>
			<MAT_CODE>MATHS</MAT_CODE>
			<MAT_LIBELLE>MATHEMATIQUES</MAT_LIBELLE>
			<PROF_NOM>BOIREAU</PROF_NOM>
			<PROF_PRENOM>STEPHANE</PROF_PRENOM>
			<CLASSE>4B</CLASSE>
			<SALLE>14</SALLE>
			<ALTERNANCE>H</ALTERNANCE>
			<MODALITE>CG</MODALITE>
			<CO-ENS.>N</CO-ENS.>
			<POND.>1</POND.>
			<JOUR>lundi</JOUR>
			<H.DEBUT>  09h00</H.DEBUT>
			<EFFECTIF>29</EFFECTIF>
		</Cours>
		...
	*/

	$tab_champs=array("NUMERO",
				"DUREE",
				"FREQUENCE",
				"MAT_CODE",
				"MAT_LIBELLE",
				"PROF_NOM",
				"PROF_PRENOM",
				"CLASSE",
				"SALLE",
				"ALTERNANCE",
				"MODALITE",
				"CO-ENS.",
				"POND.",
				"JOUR",
				"H.DEBUT",
				"EFFECTIF");

	for($loop=0;$loop<count($tab_champs);$loop++) {
		$tab_champs2[$tab_champs[$loop]]=casse_mot($tab_champs[$loop], "min");
	}
	$tab_champs2["CO-ENS."]="co_ens";
	$tab_champs2["POND."]="pond";
	$tab_champs2["H.DEBUT"]="h_debut";

	$sql="TRUNCATE edt_lignes;";
	$menage=mysqli_query($GLOBALS["mysqli"], $sql);

	$cpt=0;
	$tab_cours=array();
	foreach ($cours_xml->children() as $key => $cur_cours) {
		if($key=='Cours') {
			/*
			echo "<p>$key</p>";
			echo "<pre>";
			print_r($cur_cours);
			echo "</pre>";
			*/
			foreach ($cur_cours->children() as $key2 => $value2) {
				if(in_array($key2, $tab_champs)) {
					//$tab_cours[$cpt][$key2]=(string)$value2;
					$champ_courant=$tab_champs2[$key2];
					//$tab_cours[$cpt]["$champ_courant"]=(string)$value2;
					$tab_cours[$cpt]["$champ_courant"]=trim($value2);
					//echo "$key2:$value2<br />";
				}
			}
			/*
			echo "<p>\$tab_cours[$cpt]</p>";
			echo "<pre>";
			print_r($tab_cours[$cpt]);
			echo "</pre>";
			*/

			// Enregistrer la ligne dans edt_lignes
			$sql="INSERT INTO edt_lignes SET ";
			$sql_ajout="";
			for($loop=0;$loop<count($tab_champs);$loop++) {
				$champ_courant=$tab_champs2[$tab_champs[$loop]];
				//echo "Test champ ".$tab_champs[$loop]."<br />";
				if(isset($tab_cours[$cpt][$champ_courant])) {
					if($sql_ajout!="") {$sql_ajout.=",";}
					$sql_ajout.=" ".$champ_courant."='".mysqli_real_escape_string($GLOBALS["mysqli"], $tab_cours[$cpt][$champ_courant])."'";
				}
			}
			//echo "\$sql_ajout=$sql_ajout<br />";

			if($sql_ajout!="") {
				$sql.=$sql_ajout;
				//echo "\$sql=$sql<br />";
				$insert=mysqli_query($GLOBALS["mysqli"], $sql);
			}
			flush();

			$cpt++;
		}
	}

	saveSetting("edt_edt_last_upload", time());

	echo "<p><a href='".$_SERVER['PHP_SELF']."?action=rapprochements'>Effectuer les rapprochements</a></p>";

	require("../lib/footer.inc.php");
	die();
}
elseif($action=="rapprochements") {


	$sql="SELECT * FROM edt_lignes ORDER BY numero;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p>Aucun enregistrement n'a été trouvé.</p>";
		require("../lib/footer.inc.php");
		die();
	}

	$cpt=0;
	$ligne=array();
	while($ligne[$cpt]=mysqli_fetch_assoc($res)) {
		$cpt++;
	}

	$cpt=0;
	$tab_mat=array();
	$sql="SELECT matiere, nom_complet FROM matieres ORDER BY matiere;";
	$res_mat=mysqli_query($GLOBALS["mysqli"], $sql);
	while($tab_mat[$cpt]=mysqli_fetch_assoc($res_mat)) {
		$cpt++;
	}

	echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<p>La ou les correspondances de matières EDT/GEPI suivantes ne sont pas encore enregistrées.</p>
		<table class='boireaus boireaus_alt' title=\"En survolant le nom EDT sur la gauche, vous aurez des détails sur un cours mentionnant cette matière.\">";
	$tab_corresp_a_faire=array();
	$tab_corresp_a_faire['matiere']=array();
	$tab_corresp_a_faire['prof']=array();
	$tab_corresp_a_faire['classe']=array();
	$tab_corresp_a_faire['groupe']=array();
	$tab_corresp_a_faire['salle']=array();
	$tab_corresp_a_faire['jour']=array();
	$tab_corresp_a_faire['h_debut']=array();
	$tab_corresp_a_faire['frequence']=array();

	for($loop=0;$loop<count($ligne);$loop++) {
		$current_mat_code_edt=$ligne[$loop]['mat_code'];
		if($current_mat_code_edt!="") {
			$matiere=get_corresp_edt("matiere", $current_mat_code_edt);
			if(($matiere=="")&&(!in_array($current_mat_code_edt, $tab_corresp_a_faire['matiere']))) {

/*
<Cours numero="240">
<NUMERO>220</NUMERO>
<DUREE>1h00</DUREE>
<FREQUENCE>Q1</FREQUENCE>
<MAT_CODE>REM-FR</MAT_CODE>
<MAT_LIBELLE>REMEDIATION FR</MAT_LIBELLE>
<PROF_NOM>TOESCA</PROF_NOM>
<PROF_PRENOM>VERONIQUE</PROF_PRENOM>
<CLASSE>6C</CLASSE>
<SALLE>35</SALLE>
<ALTERNANCE>Q1</ALTERNANCE>
<MODALITE>CG</MODALITE>
<CO-ENS.>N</CO-ENS.>
<POND.>1</POND.>
<JOUR>mardi</JOUR>
<H.DEBUT>  13h30</H.DEBUT>
<EFFECTIF>21</EFFECTIF>
</Cours>
*/

				echo "
			<tr>
				<td title=\"Matière     : ".$ligne[$loop]['mat_code']." (".$ligne[$loop]['mat_libelle'].")
Professeur  : ".$ligne[$loop]['prof_nom']." ".$ligne[$loop]['prof_prenom']."
Classe      : ".$ligne[$loop]['classe']."
Salle       : ".$ligne[$loop]['salle']."
Jour        : ".$ligne[$loop]['jour']."
Heure début : ".$ligne[$loop]['h_debut']."\">".$current_mat_code_edt."&nbsp;: </td>
				<td>
					<select name=\"corresp_matiere_a_enregistrer[".$ligne[$loop]['id']."]\">
						<option value=''>---</option>";

				for($loop2=0;$loop2<count($tab_mat);$loop2++) {
					if($tab_mat[$loop2]['matiere']!="") {
						$selected="";
						if($tab_mat[$loop2]['matiere']==$current_mat_code_edt) {
							$selected=" selected";
						}
						echo "
						<option value='".$tab_mat[$loop2]['matiere']."'$selected>".$tab_mat[$loop2]['matiere']." (".$tab_mat[$loop2]['nom_complet'].")</option>";
					}
				}
				echo "
					</select>
				</td>
			</tr>";

				$tab_corresp_a_faire['matiere'][]=$current_mat_code_edt;

			}
		}
	}
	echo "</table>";


	$cpt=0;
	$tab_prof=array();
	$sql="SELECT login, civilite, nom, prenom, etat FROM utilisateurs WHERE statut='professeur' ORDER BY nom, prenom;";
	$res_prof=mysqli_query($GLOBALS["mysqli"], $sql);
	while($tab_prof[$cpt]=mysqli_fetch_assoc($res_prof)) {
		$cpt++;
	}

	echo "<br />
		<p>La ou les correspondances d'identités de professeurs EDT/GEPI suivantes ne sont pas encore enregistrées.</p>
		<table class='boireaus boireaus_alt'>";

	for($loop=0;$loop<count($ligne);$loop++) {
		if(($ligne[$loop]['prof_nom']!="")||($ligne[$loop]['prof_prenom']!="")) {
			$prof=get_corresp_edt("prof", $ligne[$loop]['prof_nom']." ".$ligne[$loop]['prof_prenom']);
			if($prof=="") {
				if(!in_array($ligne[$loop]['prof_nom']." ".$ligne[$loop]['prof_prenom'], $tab_corresp_a_faire['prof'])) {


								if(preg_match("/,/", $ligne[$loop]['prof_nom'])) {
									// Plusieurs profs sont désignés
									echo "
			<tr>
				<td>".$ligne[$loop]['prof_nom']." ".$ligne[$loop]['prof_prenom']."&nbsp;: </td>
				<td>
					<input type='hidden' name=\"corresp_prof_a_enregistrer[".$ligne[$loop]['id']."]\" value='___PLUSIEURS_PROFS___' />
					Plusieurs profs&nbsp;:<br />";

									$tmp_tab_nom=explode(",", $ligne[$loop]['prof_nom']);
									$tmp_tab_prenom=explode(",", $ligne[$loop]['prof_prenom']);
									for($loop_prof=0;$loop_prof<count($tmp_tab_nom);$loop_prof++) {
										if(trim($tmp_tab_nom[$loop_prof])!="") {
											echo $tmp_tab_nom[$loop_prof];
											if(isset($tmp_tab_prenom[$loop_prof])) {
												echo " ".$tmp_tab_prenom[$loop_prof];
											}
											echo "&nbsp;: ";
											echo "
					<select name=\"corresp_prof_a_enregistrer_".$ligne[$loop]['id']."[]\">
						<option value=''>---</option>";
											for($loop2=0;$loop2<count($tab_prof);$loop2++) {
												$selected="";
												if(casse_mot($tab_prof[$loop2]['nom']." ".$tab_prof[$loop2]['prenom'], "maj")==casse_mot($ligne[$loop]['prof_nom']." ".$ligne[$loop]['prof_prenom'], "maj")) {
													$selected=" selected";
												}
												echo "
						<option value='".$tab_prof[$loop2]['login']."'$selected>".$tab_prof[$loop2]['nom']." ".$tab_prof[$loop2]['prenom']."</option>";
											}
											echo "
					</select><br />";
										}
									}
									echo "
				</td>
			</tr>";
								}
								else {
									echo "
			<tr>
				<td>".$ligne[$loop]['prof_nom']." ".$ligne[$loop]['prof_prenom']."&nbsp;: </td>
				<td>
					<select name=\"corresp_prof_a_enregistrer[".$ligne[$loop]['id']."]\">
						<option value=''>---</option>";
									for($loop2=0;$loop2<count($tab_prof);$loop2++) {
										$selected="";
										if(casse_mot($tab_prof[$loop2]['nom']." ".$tab_prof[$loop2]['prenom'], "maj")==casse_mot($ligne[$loop]['prof_nom']." ".$ligne[$loop]['prof_prenom'], "maj")) {
											$selected=" selected";
										}
										echo "
						<option value='".$tab_prof[$loop2]['login']."'$selected>".$tab_prof[$loop2]['nom']." ".$tab_prof[$loop2]['prenom']."</option>";
									}
									echo "
					</select>
				</td>
			</tr>";
								}


					/*
					echo "
			<tr>
				<td>".$ligne[$loop]['prof_nom']." ".$ligne[$loop]['prof_prenom']."&nbsp;: </td>
				<td>
					<select name=\"corresp_prof_a_enregistrer[".$ligne[$loop]['id']."]\">
						<option value=''>---</option>";
					for($loop2=0;$loop2<count($tab_prof);$loop2++) {
						$selected="";
						if(casse_mot($tab_prof[$loop2]['nom']." ".$tab_prof[$loop2]['prenom'], "maj")==casse_mot($ligne[$loop]['prof_nom']." ".$ligne[$loop]['prof_prenom'], "maj")) {
							$selected=" selected";
						}
						echo "
						<option value='".$tab_prof[$loop2]['login']."'$selected>".$tab_prof[$loop2]['nom']." ".$tab_prof[$loop2]['prenom']."</option>";
					}
					echo "
					</select>
				</td>
			</tr>";
					*/

					$tab_corresp_a_faire['prof'][]=$ligne[$loop]['prof_nom']." ".$ligne[$loop]['prof_prenom'];
				}
			}
		}
	}

	echo "</table>";

	$cpt=0;
	$tab_classe=array();
	$sql="SELECT id, classe, nom_complet FROM classes ORDER BY classe, nom_complet;";
	$res_classe=mysqli_query($GLOBALS["mysqli"], $sql);
	while($tab_classe[$cpt]=mysqli_fetch_assoc($res_classe)) {
		$cpt++;
	}
/*
echo "<pre>";
print_r($tab_classe);
echo "</pre>";
*/
	echo "<br />
		<p>La ou les correspondances de <strong>classes</strong> EDT/GEPI suivantes ne sont pas encore enregistrées.<br />
		Ne renseignez que les lignes correspondant à des classes, pas à des groupes.<br />
		(<em>il est normal que vous conserviez ici des lignes non associées (tous les groupes)</em>)</p>
		<table class='boireaus boireaus_alt'>";

	for($loop=0;$loop<count($ligne);$loop++) {
		if($ligne[$loop]['classe']!="") {
			$classe=get_corresp_edt("classe", $ligne[$loop]['classe']);
			if($classe=="") {
				if(!in_array($ligne[$loop]['classe'], $tab_corresp_a_faire['classe'])) {
					echo "
			<tr>
				<td>".$ligne[$loop]['classe']."&nbsp;: </td>
				<td>
					<select name=\"corresp_classe_a_enregistrer[".$ligne[$loop]['id']."]\">
						<option value=''>---</option>";
					for($loop2=0;$loop2<count($tab_classe);$loop2++) {
						if(isset($tab_classe[$loop2]['id'])) {
							$selected="";
							if((casse_mot($tab_classe[$loop2]['classe'], "maj")==casse_mot($ligne[$loop]['classe'], "maj"))||
							(casse_mot(preg_replace("/ /","",$tab_classe[$loop2]['classe']), "maj")==casse_mot(preg_replace("/ /","",$ligne[$loop]['classe']), "maj"))) {
								$selected=" selected";
							}
							echo "
						<option value='".$tab_classe[$loop2]['id']."'$selected>".$tab_classe[$loop2]['classe']." (".$tab_classe[$loop2]['nom_complet'].")</option>";
						}
					}
					echo "
					</select>
				</td>
			</tr>";

					$tab_corresp_a_faire['classe'][]=$ligne[$loop]['classe'];
				}
			}
		}
	}

	echo "</table>";

	$texte_infobulle="<div id='div_infos_groupes2'></div>";
	$tabdiv_infobulle[]=creer_div_infobulle("div_infos_groupes","Groupes possibles","",$texte_infobulle,"",40,0,'y','y','n','n');

	echo "<br />
		<p>La ou les correspondances de <strong>groupes</strong> EDT/GEPI suivantes ne sont pas encore enregistrées.<br />
		<a href='#' onclick=\"import_edt_decocher_groupes();return false;\">Tout décocher</a><br />
		(<em>En cas de doute sur les classes associées, commencer par effectuer l'association des matières en décochant tout dans cette section... et valider en bas de page.<br />
		Revenez ensuite aux rapprochements.<br />
		Les associations non encore effectuées seront re-proposées, mais les matières reconnues permettront d'identifier plus facilement les classes en cliquant sur les icones <img src='../images/icons/chercher.png' class='icone16' alt='Chercher' /></em>).</p>
		<table class='boireaus boireaus_alt' title=\"En survolant le nom EDT sur la gauche, vous aurez des détails sur un cours mentionnant ce groupe.\">";

	$cpt=0;
	for($loop=0;$loop<count($ligne);$loop++) {
		if($ligne[$loop]['classe']!="") {
			$classe=get_corresp_edt("groupe", $ligne[$loop]['classe']);
			if($classe=="") {
				if(!in_array($ligne[$loop]['classe'], $tab_corresp_a_faire['groupe'])) {
					echo "
			<tr>
				<td title=\"Classe      : ".$ligne[$loop]['classe']."
Matière     : ".$ligne[$loop]['mat_code']." (".$ligne[$loop]['mat_libelle'].")
Professeur  : ".$ligne[$loop]['prof_nom']." ".$ligne[$loop]['prof_prenom']."
Salle       : ".$ligne[$loop]['salle']."
Jour        : ".$ligne[$loop]['jour']."
Heure début : ".$ligne[$loop]['h_debut']."\">
					<div style='float:right; width:16px;'>
						<a href='#' onclick=\"import_edt_chercher_groupe(".$ligne[$loop]['id'].");return false;\"><img src='../images/icons/chercher.png' class='icone16' alt='Chercher' /></a>
					</div>

					".$ligne[$loop]['classe']."&nbsp;: 
				</td>
				<td>";
					for($loop2=0;$loop2<count($tab_classe);$loop2++) {
						if(isset($tab_classe[$loop2]['id'])) {
							$checked="";
							$style_tmp="";
							if((preg_match("/".$tab_classe[$loop2]['classe']."/", $ligne[$loop]['classe']))||
							(preg_match("/".preg_replace("/ /","",$tab_classe[$loop2]['classe'])."/", $ligne[$loop]['classe']))) {
								$checked=" checked";
								$style_tmp=" style='font-weight:bold;'";
							}
							echo "<input type='checkbox' name='corresp_groupe_a_enregistrer_".$ligne[$loop]['id']."[]' id='grp_classe_".$cpt."' value='".$tab_classe[$loop2]['id']."' onchange=\"checkbox_change('grp_classe_".$cpt."')\" $checked/><label for='grp_classe_".$cpt."' id='texte_grp_classe_".$cpt."'$style_tmp>".$tab_classe[$loop2]['classe']."</label> - ";
							$cpt++;
						}
					}
					echo "
				</td>
			</tr>";

					$tab_corresp_a_faire['groupe'][]=$ligne[$loop]['classe'];
				}
			}
		}
	}

	echo "</table>

<script type='text/javascript'>
	function import_edt_chercher_groupe(num) {
		new Ajax.Updater($('div_infos_groupes2'),'".$_SERVER['PHP_SELF']."?rechercher_groupes_possibles=y&num='+num,{method: 'get'});
		afficher_div('div_infos_groupes','y',10,10);
	}

	function import_edt_decocher_groupes() {
		input=document.getElementsByTagName('input');
		for(i=0;i<input.length;i++) {
			type=input[i].getAttribute('type');
			if(type=='checkbox') {
				id=input[i].getAttribute('id');
				if(id.substr(0,11)=='grp_classe_') {
					//document.getElementById('texte_'+id).style.color='red';
					document.getElementById(id).checked=false;
					checkbox_change(id);
				}
			}
		}
	}
</script>";


	$cpt=0;
	$tab_salle=array();
	$sql="SELECT * FROM salle_cours ORDER BY numero_salle, nom_salle;";
	$res_salle=mysqli_query($GLOBALS["mysqli"], $sql);
	while($tab_salle[$cpt]=mysqli_fetch_assoc($res_salle)) {
		$cpt++;
	}

	/*
	echo "<pre>";
	print_r($tab_salle);
	echo "</pre>";
	*/

	echo "<br />
		<p>La ou les correspondances de <strong>salles</strong> EDT/GEPI suivantes ne sont pas encore enregistrées.</p>
		<table class='boireaus boireaus_alt'>";

	for($loop=0;$loop<count($ligne);$loop++) {
		if($ligne[$loop]['salle']!="") {
			$salle=get_corresp_edt("salle", $ligne[$loop]['salle']);
			if($salle=="") {
				if(!in_array($ligne[$loop]['salle'], $tab_corresp_a_faire['salle'])) {
					echo "
			<tr>
				<td>".$ligne[$loop]['salle']."&nbsp;: </td>
				<td>";

					$current_edt_salle=trim($ligne[$loop]['salle']);

					echo "
					<select name=\"corresp_salle_a_enregistrer[".$ligne[$loop]['id']."]\">
						<option value=''>---</option>";
					for($loop2=0;$loop2<count($tab_salle);$loop2++) {
						if(isset($tab_salle[$loop2]['id_salle'])) {
							$selected="";

							$current_numero_salle=$tab_salle[$loop2]['numero_salle'];
							if("$current_numero_salle"=="$current_edt_salle") {
								$selected=" selected";
							}
							$current_nom_salle=$tab_salle[$loop2]['nom_salle'];
							if("$current_nom_salle"=="$current_edt_salle") {
								$selected=" selected";
							}

							echo "
						<option value='".$tab_salle[$loop2]['id_salle']."' $selected>".$tab_salle[$loop2]['numero_salle'];
							/*
							echo "
						<option value='".$tab_salle[$loop2]['numero_salle']."'$selected>".$tab_salle[$loop2]['numero_salle'];
							*/
							if($tab_salle[$loop2]['nom_salle']!="") {
								echo " (".$tab_salle[$loop2]['nom_salle'].")";
							}
							echo "</option>";
						}
					}
					echo "
						<option value='___SALLE_A_CREER___'>Créer la salle</option>
					</select>
				</td>
			</tr>";

					$tab_corresp_a_faire['salle'][]=$ligne[$loop]['salle'];
				}
			}
		}
	}

	echo "</table>";

	$tab_jour=array("lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi", "dimanche");

	echo "<br />
		<p>La ou les correspondances de <strong>jours</strong> EDT/GEPI suivantes ne sont pas encore enregistrées.</p>
		<table class='boireaus boireaus_alt'>";

	for($loop=0;$loop<count($ligne);$loop++) {
		if($ligne[$loop]['jour']!="") {
			$jour=get_corresp_edt("jour", $ligne[$loop]['jour']);
			if($jour=="") {
				if(!in_array($ligne[$loop]['jour'], $tab_corresp_a_faire['jour'])) {
					echo "
			<tr>
				<td>".$ligne[$loop]['jour']."&nbsp;: </td>
				<td>
					<select name=\"corresp_jour_a_enregistrer[".$ligne[$loop]['id']."]\">
						<option value=''>---</option>";
					for($loop2=0;$loop2<count($tab_jour);$loop2++) {
						$selected="";

						if(casse_mot($tab_jour[$loop2], "min")==casse_mot($ligne[$loop]['jour'], "min")) {
							$selected=" selected";
						}

						echo "
						<option value='".$tab_jour[$loop2]."'$selected>".$tab_jour[$loop2]."</option>\n";
					}
					echo "
					</select>
				</td>
			</tr>";

					$tab_corresp_a_faire['jour'][]=$ligne[$loop]['jour'];
				}
			}
		}
	}

	echo "</table>";


	$tab_creneaux=get_heures_debut_fin_creneaux();

	echo "<br />
		<p>La ou les correspondances d'<strong>horaires de début de cours</strong> EDT/GEPI suivantes ne sont pas encore enregistrées.</p>
		<table class='boireaus boireaus_alt'>";

	for($loop=0;$loop<count($ligne);$loop++) {
		if($ligne[$loop]['h_debut']!="") {
			$h_debut=get_corresp_edt("h_debut", $ligne[$loop]['h_debut']);
			if($h_debut=="") {
				if(!in_array($ligne[$loop]['h_debut'], $tab_corresp_a_faire['h_debut'])) {
					echo "
			<tr>
				<td>".$ligne[$loop]['h_debut']."&nbsp;: </td>
				<td>
					<select name=\"corresp_h_debut_a_enregistrer[".$ligne[$loop]['id']."]\">
						<option value=''>---</option>";
					foreach($tab_creneaux as $id_creneau => $current_creneau) {
						$selected="";

						echo "
						<option value='".$id_creneau."'$selected>".$current_creneau['nom_creneau']." (".$current_creneau['debut_court']."-".$current_creneau['fin_court'].")</option>\n";
					}
					echo "
					</select>
				</td>
				<td>
					<input type='checkbox' name='corresp_h_debut_demi_creneau[".$ligne[$loop]['id']."]' value='y' />Demi-créneau
				</td>
			</tr>";

					$tab_corresp_a_faire['h_debut'][]=$ligne[$loop]['h_debut'];
				}
			}
		}
	}

	echo "</table>";




	$cpt=0;
	$tab_semaine=array();
	$sql="SELECT DISTINCT type_edt_semaine FROM edt_semaines;";
	$res_semaine=mysqli_query($GLOBALS["mysqli"], $sql);
	while($tab_semaine[$cpt]=mysqli_fetch_assoc($res_semaine)) {
		$cpt++;
	}

	echo "<br />
		<p>La ou les correspondances de <strong>types de semaines</strong> EDT/GEPI suivantes ne sont pas encore enregistrées.</p>
		<table class='boireaus boireaus_alt'>";

	for($loop=0;$loop<count($ligne);$loop++) {
		if($ligne[$loop]['frequence']!="") {
			$frequence=get_corresp_edt("frequence", $ligne[$loop]['frequence']);
			if($frequence=="") {
				if(!in_array($ligne[$loop]['frequence'], $tab_corresp_a_faire['frequence'])) {
					echo "
			<tr>
				<td>".$ligne[$loop]['frequence']."&nbsp;: </td>
				<td>
					<select name=\"corresp_frequence_a_enregistrer[".$ligne[$loop]['id']."]\">
						<option value=''>---</option>";
					for($loop2=0;$loop2<count($tab_semaine);$loop2++) {
						if(trim($tab_semaine[$loop2]['type_edt_semaine'])!="") {
							$selected="";

							echo "
						<option value='".$tab_semaine[$loop2]['type_edt_semaine']."'$selected>".$tab_semaine[$loop2]['type_edt_semaine']."</option>\n";
						}
					}
					echo "
					</select>
				</td>
			</tr>";

					$tab_corresp_a_faire['frequence'][]=$ligne[$loop]['frequence'];
				}
			}
		}
	}

	echo "</table>";



	echo "
		<p>
			<input type='hidden' name='action' value='enregistrer_rapprochements' />
			<input type='submit' id='input_submit' value='Valider' />
		</p>

		".js_checkbox_change_style('checkbox_change', 'texte_', "y")."
	</fieldset>
</form>

<p style='color:red'>Dans le tableau des rapprochements de groupe, utiliser les infos matière et prof associés à l'enregistrement de edt_lignes pour afficher une aide au choix des classes (liste des groupes de la matière,...)</p>";


	require("../lib/footer.inc.php");
	die();
}
elseif($action=="enregistrer_rapprochements") {

	// matiere
	$corresp_matiere_a_enregistrer=isset($_POST['corresp_matiere_a_enregistrer']) ? $_POST['corresp_matiere_a_enregistrer'] : NULL;
	if(isset($corresp_matiere_a_enregistrer)) {
		$nb_reg=0;
		$nb_del=0;
		foreach($corresp_matiere_a_enregistrer as $id_ligne => $nom_gepi) {
			$sql="SELECT * FROM edt_lignes WHERE id='$id_ligne';";
			//echo "$sql<br />";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				$lig=mysqli_fetch_object($res);

				/*
				echo "<pre>";
				print_r($lig);
				echo "</pre>";
				*/
				if($nom_gepi=="") {
					$sql="DELETE FROM edt_corresp WHERE champ='matiere' AND nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->mat_code)."';";
					//echo "$sql<br />";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$del) {
						echo "<span style='color:red'>Erreur : $sql</span><br />";
					}
					else {
						$nb_del++;
					}
				}
				else {
					$sql="SELECT * FROM edt_corresp WHERE champ='matiere' AND nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->mat_code)."';";
					$res2=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res2)>0) {
						$lig2=mysqli_fetch_object($res2);
						echo "<span style='color:red'>$lig->mat_code était préalablement associée à $lig2->nom_gepi</span><br />";

						$sql="UPDATE edt_corresp SET champ='matiere', nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->mat_code)."', nom_gepi='$nom_gepi' WHERE id='$lig2->id';";
						//echo "$sql<br />";
						$update=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$update) {
							echo "<span style='color:red'>Erreur : $sql</span><br />";
						}
						else {
							$nb_reg++;
						}
					}
					else {
						$sql="INSERT INTO edt_corresp SET champ='matiere', nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->mat_code)."', nom_gepi='$nom_gepi';";
						//echo "$sql<br />";
						$insert=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$insert) {
							echo "<span style='color:red'>Erreur : $sql</span><br />";
						}
						else {
							$nb_reg++;
						}
					}
				}
			}
			else {
				echo "<span style='color:red'>Enregistrement non trouvé pour la ligne n°$id_ligne correspondant à $nom_gepi</span><br />";
			}
		}
		echo "$nb_reg associations matières effectuées.<br />";
	}

	// prof
	$corresp_prof_a_enregistrer=isset($_POST['corresp_prof_a_enregistrer']) ? $_POST['corresp_prof_a_enregistrer'] : NULL;
	if(isset($corresp_prof_a_enregistrer)) {
		$nb_reg=0;
		$nb_del=0;
		foreach($corresp_prof_a_enregistrer as $id_ligne => $nom_gepi) {
			$sql="SELECT * FROM edt_lignes WHERE id='$id_ligne';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				$lig=mysqli_fetch_object($res);

				if($nom_gepi=="") {
					$sql="DELETE FROM edt_corresp WHERE champ='prof' AND nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->prof_nom." ".$lig->prof_prenom)."';";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$del) {
						echo "<span style='color:red'>Erreur : $sql</span><br />";
					}
					else {
						$nb_del++;
					}
				}
				elseif($nom_gepi=="___PLUSIEURS_PROFS___") {
					$tmp_tab_corresp_prof_a_enregistrer=isset($_POST["corresp_prof_a_enregistrer_".$id_ligne]) ? $_POST["corresp_prof_a_enregistrer_".$id_ligne] : array();
					$chaine_profs="";
					for($loop_prof=0;$loop_prof<count($tmp_tab_corresp_prof_a_enregistrer);$loop_prof++) {
						if($tmp_tab_corresp_prof_a_enregistrer[$loop_prof]!="") {
							if($chaine_profs!="") {
								$chaine_profs.="|";
							}
							$chaine_profs.=$tmp_tab_corresp_prof_a_enregistrer[$loop_prof];
						}
					}
					$nom_gepi=$chaine_profs;

					if($nom_gepi=="") {
						$sql="DELETE FROM edt_corresp WHERE champ='prof' AND nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->prof_nom." ".$lig->prof_prenom)."';";
						$del=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$del) {
							echo "<span style='color:red'>Erreur : $sql</span><br />";
						}
						else {
							$nb_del++;
						}
					}
					else {

						$sql="SELECT * FROM edt_corresp WHERE champ='prof' AND nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->prof_nom." ".$lig->prof_prenom)."';";
						$res2=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res2)>0) {
							$lig2=mysqli_fetch_object($res2);
							echo "<span style='color:red'>$lig->prof_nom $lig->prof_prenom était préalablement associée à $lig2->nom_gepi</span><br />";

							$sql="UPDATE edt_corresp SET champ='prof', nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->prof_nom." ".$lig->prof_prenom)."', nom_gepi='$nom_gepi' WHERE id='$lig2->id';";
							$update=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$update) {
								echo "<span style='color:red'>Erreur : $sql</span><br />";
							}
							else {
								$nb_reg++;
							}
						}
						else {
							$sql="INSERT INTO edt_corresp SET champ='prof', nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->prof_nom." ".$lig->prof_prenom)."', nom_gepi='$nom_gepi';";
							$insert=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$insert) {
								echo "<span style='color:red'>Erreur : $sql</span><br />";
							}
							else {
								$nb_reg++;
							}
						}
					}

				}
				else {
					$sql="SELECT * FROM edt_corresp WHERE champ='prof' AND nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->prof_nom." ".$lig->prof_prenom)."';";
					$res2=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res2)>0) {
						$lig2=mysqli_fetch_object($res2);
						echo "<span style='color:red'>$lig->prof_nom $lig->prof_prenom était préalablement associée à $lig2->nom_gepi</span><br />";

						$sql="UPDATE edt_corresp SET champ='prof', nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->prof_nom." ".$lig->prof_prenom)."', nom_gepi='$nom_gepi' WHERE id='$lig2->id';";
						$update=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$update) {
							echo "<span style='color:red'>Erreur : $sql</span><br />";
						}
						else {
							$nb_reg++;
						}
					}
					else {
						$sql="INSERT INTO edt_corresp SET champ='prof', nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->prof_nom." ".$lig->prof_prenom)."', nom_gepi='$nom_gepi';";
						$insert=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$insert) {
							echo "<span style='color:red'>Erreur : $sql</span><br />";
						}
						else {
							$nb_reg++;
						}
					}
				}
			}
			else {
				echo "<span style='color:red'>Enregistrement non trouvé pour la ligne n°$id_ligne correspondant à $nom_gepi</span><br />";
			}
		}
		echo "$nb_reg associations professeurs effectuées.<br />";
	}

	// classe
	$corresp_classe_a_enregistrer=isset($_POST['corresp_classe_a_enregistrer']) ? $_POST['corresp_classe_a_enregistrer'] : NULL;
	if(isset($corresp_classe_a_enregistrer)) {
		$nb_reg=0;
		$nb_del=0;
		foreach($corresp_classe_a_enregistrer as $id_ligne => $nom_gepi) {
			$sql="SELECT * FROM edt_lignes WHERE id='$id_ligne';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				$lig=mysqli_fetch_object($res);

				if($nom_gepi=="") {
					$sql="DELETE FROM edt_corresp WHERE champ='classe' AND nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->classe)."';";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$del) {
						echo "<span style='color:red'>Erreur : $sql</span><br />";
					}
					else {
						$nb_del++;
					}
				}
				else {
					$sql="SELECT * FROM edt_corresp WHERE champ='classe' AND nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->classe)."';";
					$res2=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res2)>0) {
						$lig2=mysqli_fetch_object($res2);
						echo "<span style='color:red'>$lig->classe était préalablement associée à $lig2->nom_gepi</span><br />";

						$sql="UPDATE edt_corresp SET champ='classe', nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->classe)."', nom_gepi='$nom_gepi' WHERE id='$lig2->id';";
						$update=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$update) {
							echo "<span style='color:red'>Erreur : $sql</span><br />";
						}
						else {
							$nb_reg++;
						}
					}
					else {
						$sql="INSERT INTO edt_corresp SET champ='classe', nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->classe)."', nom_gepi='$nom_gepi';";
						$insert=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$insert) {
							echo "<span style='color:red'>Erreur : $sql</span><br />";
						}
						else {
							$nb_reg++;
						}
					}
				}
			}
			else {
				echo "<span style='color:red'>Enregistrement non trouvé pour la ligne n°$id_ligne correspondant à $nom_gepi</span><br />";
			}
		}
		echo "$nb_reg associations classes effectuées.<br />";
	}

	// salle
	$tab_salle_cours=get_tab_salle_cours();
	$corresp_salle_a_enregistrer=isset($_POST['corresp_salle_a_enregistrer']) ? $_POST['corresp_salle_a_enregistrer'] : NULL;
	if(isset($corresp_salle_a_enregistrer)) {
		$nb_reg=0;
		$nb_del=0;
		foreach($corresp_salle_a_enregistrer as $id_ligne => $nom_gepi) {
			$sql="SELECT * FROM edt_lignes WHERE id='$id_ligne';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				$lig=mysqli_fetch_object($res);

				if($nom_gepi=="") {
					$sql="DELETE FROM edt_corresp WHERE champ='salle' AND nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->salle)."';";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$del) {
						echo "<span style='color:red'>Erreur : $sql</span><br />";
					}
					else {
						$nb_del++;
					}
				}
				else {
					$temoin_erreur="n";
					if($nom_gepi=="___SALLE_A_CREER___") {
						$nom_gepi=remplace_accents($lig->salle, "all");
						$sql="SELECT 1=1 FROM salle_cours WHERE numero_salle='".$nom_gepi."';";
						$test=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($test)==0) {
							$sql="INSERT INTO salle_cours SET numero_salle='".$nom_gepi."', nom_salle='".$nom_gepi."'";
							//echo "$sql<br />";
							$insert=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$insert) {
								echo "<span style='color:red'>Erreur : $sql</span><br />";
								$temoin_erreur="y";
							}
							else {
								$tab_salle_cours=get_tab_salle_cours();
							}
						}
					}

					if($temoin_erreur=="n") {
						$sql="SELECT * FROM edt_corresp WHERE champ='salle' AND nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->salle)."';";
						$res2=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res2)>0) {
							$lig2=mysqli_fetch_object($res2);
							echo "<span style='color:red'>$lig->salle était préalablement associée à ".$tab_salle_cours['indice'][$lig2->nom_gepi]['numero_salle']." (".$tab_salle_cours['indice'][$lig2->nom_gepi]['nom_salle'].")</span><br />";

							$sql="UPDATE edt_corresp SET champ='salle', nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->salle)."', nom_gepi='$nom_gepi' WHERE id='$lig2->id';";
							$update=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$update) {
								echo "<span style='color:red'>Erreur : $sql</span><br />";
							}
							else {
								$nb_reg++;
							}
						}
						else {
							$sql="INSERT INTO edt_corresp SET champ='salle', nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->salle)."', nom_gepi='$nom_gepi';";
							$insert=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$insert) {
								echo "<span style='color:red'>Erreur : $sql</span><br />";
							}
							else {
								$nb_reg++;
							}
						}
					}
				}
			}
			else {
				echo "<span style='color:red'>Enregistrement non trouvé pour la ligne n°$id_ligne correspondant à $nom_gepi</span><br />";
			}
		}
		echo "$nb_reg associations salles effectuées.<br />";
	}

	// jour
	$corresp_jour_a_enregistrer=isset($_POST['corresp_jour_a_enregistrer']) ? $_POST['corresp_jour_a_enregistrer'] : NULL;
	if(isset($corresp_jour_a_enregistrer)) {
		$nb_reg=0;
		$nb_del=0;
		foreach($corresp_jour_a_enregistrer as $id_ligne => $nom_gepi) {
			$sql="SELECT * FROM edt_lignes WHERE id='$id_ligne';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				$lig=mysqli_fetch_object($res);

				if($nom_gepi=="") {
					$sql="DELETE FROM edt_corresp WHERE champ='jour' AND nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->jour)."';";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$del) {
						echo "<span style='color:red'>Erreur : $sql</span><br />";
					}
					else {
						$nb_del++;
					}
				}
				else {
					$sql="SELECT * FROM edt_corresp WHERE champ='jour' AND nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->jour)."';";
					$res2=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res2)>0) {
						$lig2=mysqli_fetch_object($res2);
						echo "<span style='color:red'>$lig->jour était préalablement associée à $lig2->nom_gepi</span><br />";

						$sql="UPDATE edt_corresp SET champ='jour', nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->jour)."', nom_gepi='$nom_gepi' WHERE id='$lig2->id';";
						$update=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$update) {
							echo "<span style='color:red'>Erreur : $sql</span><br />";
						}
						else {
							$nb_reg++;
						}
					}
					else {
						$sql="INSERT INTO edt_corresp SET champ='jour', nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->jour)."', nom_gepi='$nom_gepi';";
						$insert=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$insert) {
							echo "<span style='color:red'>Erreur : $sql</span><br />";
						}
						else {
							$nb_reg++;
						}
					}
				}
			}
			else {
				echo "<span style='color:red'>Enregistrement non trouvé pour la ligne n°$id_ligne correspondant à $nom_gepi</span><br />";
			}
		}
		echo "$nb_reg associations jours effectuées.<br />";
	}

	// groupes
	$nb_reg=0;
	$sql="SELECT * FROM edt_lignes;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			/*
			echo "<pre>";
			print_r($lig);
			echo "</pre>";
			*/
			if(isset($_POST['corresp_groupe_a_enregistrer_'.$lig->id])) {
				$current_ligne_grp=$_POST['corresp_groupe_a_enregistrer_'.$lig->id];

				$chaine_classes="|";
				for($loop=0;$loop<count($current_ligne_grp);$loop++) {
					$chaine_classes.=$current_ligne_grp[$loop]."|";
				}

				$sql="SELECT * FROM edt_corresp WHERE champ='groupe' AND nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->classe)."';";
				//echo "$sql<br />";
				$res2=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res2)>0) {
					$lig2=mysqli_fetch_object($res2);
					echo "<span style='color:red'>$lig->classe était préalablement associée à $lig2->nom_gepi</span><br />";

					$sql="UPDATE edt_corresp SET champ='groupe', nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->classe)."', nom_gepi='$chaine_classes' WHERE id='$lig2->id';";
					//echo "$sql<br />";
					$update=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$update) {
						echo "<span style='color:red'>Erreur : $sql</span><br />";
					}
					else {
						$nb_reg++;
					}
				}
				else {
					$sql="INSERT INTO edt_corresp SET champ='groupe', nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->classe)."', nom_gepi='$chaine_classes';";
					//echo "$sql<br />";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$insert) {
						echo "<span style='color:red'>Erreur : $sql</span><br />";
					}
					else {
						$nb_reg++;
					}
				}
			}
		}
		echo "$nb_reg associations classes/groupes effectuées.<br />";
	}


	// h_debut
	$corresp_h_debut_a_enregistrer=isset($_POST['corresp_h_debut_a_enregistrer']) ? $_POST['corresp_h_debut_a_enregistrer'] : NULL;
	$corresp_h_debut_demi_creneau=isset($_POST['corresp_h_debut_demi_creneau']) ? $_POST['corresp_h_debut_demi_creneau'] : array();
	if(isset($corresp_h_debut_a_enregistrer)) {
		$nb_reg=0;
		$nb_del=0;
		foreach($corresp_h_debut_a_enregistrer as $id_ligne => $nom_gepi) {
			$sql="SELECT * FROM edt_lignes WHERE id='$id_ligne';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				$lig=mysqli_fetch_object($res);

				if($nom_gepi=="") {
					$sql="DELETE FROM edt_corresp WHERE champ='h_debut' AND nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->h_debut)."';";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$del) {
						echo "<span style='color:red'>Erreur : $sql</span><br />";
					}
					else {
						$nb_del++;
					}
				}
				else {
					if(isset($corresp_h_debut_demi_creneau[$id_ligne])) {
						$nom_gepi.="|0.5";
					}
					else {
						$nom_gepi.="|0";
					}

					$sql="SELECT * FROM edt_corresp WHERE champ='h_debut' AND nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->h_debut)."';";
					$res2=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res2)>0) {
						$lig2=mysqli_fetch_object($res2);
						echo "<span style='color:red'>$lig->h_debut était préalablement associée à $lig2->nom_gepi</span><br />";

						$sql="UPDATE edt_corresp SET champ='h_debut', nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->h_debut)."', nom_gepi='$nom_gepi' WHERE id='$lig2->id';";
						$update=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$update) {
							echo "<span style='color:red'>Erreur : $sql</span><br />";
						}
						else {
							$nb_reg++;
						}
					}
					else {
						$sql="INSERT INTO edt_corresp SET champ='h_debut', nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->h_debut)."', nom_gepi='$nom_gepi';";
						$insert=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$insert) {
							echo "<span style='color:red'>Erreur : $sql</span><br />";
						}
						else {
							$nb_reg++;
						}
					}
				}
			}
			else {
				echo "<span style='color:red'>Enregistrement non trouvé pour la ligne n°$id_ligne correspondant à $nom_gepi</span><br />";
			}
		}
		echo "$nb_reg associations d'heure de début de cours effectuées.<br />";
	}





	// frequence
	$corresp_frequence_a_enregistrer=isset($_POST['corresp_frequence_a_enregistrer']) ? $_POST['corresp_frequence_a_enregistrer'] : NULL;
	if(isset($corresp_frequence_a_enregistrer)) {
		$nb_reg=0;
		$nb_del=0;
		foreach($corresp_frequence_a_enregistrer as $id_ligne => $nom_gepi) {
			$sql="SELECT * FROM edt_lignes WHERE id='$id_ligne';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				$lig=mysqli_fetch_object($res);

				if($nom_gepi=="") {
					$sql="DELETE FROM edt_corresp WHERE champ='frequence' AND nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->frequence)."';";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$del) {
						echo "<span style='color:red'>Erreur : $sql</span><br />";
					}
					else {
						$nb_del++;
					}
				}
				else {
					$sql="SELECT * FROM edt_corresp WHERE champ='frequence' AND nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->frequence)."';";
					$res2=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res2)>0) {
						$lig2=mysqli_fetch_object($res2);
						echo "<span style='color:red'>$lig->frequence était préalablement associée à $lig2->nom_gepi</span><br />";

						$sql="UPDATE edt_corresp SET champ='frequence', nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->frequence)."', nom_gepi='$nom_gepi' WHERE id='$lig2->id';";
						$update=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$update) {
							echo "<span style='color:red'>Erreur : $sql</span><br />";
						}
						else {
							$nb_reg++;
						}
					}
					else {
						$sql="INSERT INTO edt_corresp SET champ='frequence', nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->frequence)."', nom_gepi='$nom_gepi';";
						$insert=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$insert) {
							echo "<span style='color:red'>Erreur : $sql</span><br />";
						}
						else {
							$nb_reg++;
						}
					}
				}
			}
			else {
				echo "<span style='color:red'>Enregistrement non trouvé pour la ligne n°$id_ligne correspondant à $nom_gepi</span><br />";
			}
		}
		echo "$nb_reg associations de semaines A/B effectuées.<br />";
	}

	echo "<p>L'étape suivante consiste à effectuer le remplissage de l'emploi du temps en suivant le lien <strong>Remplir l'EDT</strong> ci-dessus sous le bandeau d'entête.</p>";

	require("../lib/footer.inc.php");
	die();
}
elseif($action=="remplir_edt_cours") {
	check_token(false);

	$sql="SELECT * FROM edt_lignes WHERE traitement='cours_identifie_cree';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_cours_crees=mysqli_num_rows($res);

	$sql="SELECT * FROM edt_lignes WHERE traitement='choix_effectue';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_cours_choix_effectue=mysqli_num_rows($res);

	//if(($nb_cours_crees==0)&&($nb_cours_choix_effectue==0)) {
	if($nb_cours_choix_effectue==0) {
		echo "<p>Vous allez effectuer le remplissage des cours d'après les rapprochements effectués en commençant par vider la table 'edt_cours' (<em>les cours créés préalablement dans l'emploi du temps (à la main ou par import) seront supprimés&nbsp;;<br />Cela ne supprime pas les enseignements et tout ce qui y est associé&nbsp;;<br />Seul le contenu de l'emploi du temps sera d'abord vidé</em>).<br />
Une large partie des enseignements devrait être identifiée et la création de cours effectuée sans intervention de votre part.<br />
Les indéterminés vous seront proposés.</p>

<p><a href='".$_SERVER['PHP_SELF']."?action=valider_remplir_edt_cours".add_token_in_url()."'>Vider puis remplir l'emploi du temps</a></p>";
	}
	else {

		echo "<p>Vous allez effectuer le remplissage des cours d'après les rapprochements effectués en commençant par vider la table 'edt_cours' (<em>les cours créés préalablement dans l'emploi du temps (à la main ou par import) seront supprimés&nbsp;;<br />Cela ne supprime pas les enseignements et tout ce qui y est associé&nbsp;;<br />Seul le contenu de l'emploi du temps sera d'abord vidé</em>).<br />
Une large partie des enseignements devrait être identifiée et la création de cours effectuée sans intervention de votre part.<br />
Les indéterminés vous seront proposés.</p>

<p>Des cours ont été créés préalablement et des choix effectués pour les indéterminés.<br />
Vous pouvez choisir&nbsp;:</p>
<ul>
	<li>
		<p>Refaire l'identification des groupes,...<br />
		<a href='".$_SERVER['PHP_SELF']."?action=valider_remplir_edt_cours".add_token_in_url()."'>Vider puis remplir l'emploi du temps</a></p>
	</li>
	<li>
		<p>Recréer les cours d'après les associations enregistrées et les choix préalablement effectués pour ne vous proposer que les choix non tranchés auparavant.<br />
		<a href='".$_SERVER['PHP_SELF']."?action=valider_remplir_edt_cours&amp;utiliser_choix_prec=y".add_token_in_url()."'>Vider l'emploi du temps, recréer les cours déjà identifiés, puis proposer les indéterminés.</a></p>
	</li>
";


	}

	require("../lib/footer.inc.php");
	die();
}
elseif($action=="valider_remplir_edt_cours") {
	check_token(false);

	//$sql="TRUNCATE TABLE edt_cours;";
	$sql="DELETE FROM edt_cours;";
	// Le TRUNCATE fait repartir l'auto_increment à 1... et on risque de ré-attribuer un id_cours si on fait plusieurs remplissages dans l'année.
	// Et on aura alors des choses bizarres par exemple dans mod_abs2 avec a_saisies.id_edt_emplacement_cours=edt_cours.id_cours
	$res=mysqli_query($GLOBALS["mysqli"], $sql);

	$utiliser_choix_prec=isset($_GET['utiliser_choix_prec']) ? "y" : "n";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<div id='div_info_affichage_masquage' style='float:right; width:6em; text-align:center; background:green'>
			<span style='color:red'>Patience...</span>
			<br />
			<img src='../images/spinner.gif' class='icone16' />
		</div>";
	if($utiliser_choix_prec=="n") {
		echo "
		<p><a href='#' onclick='import_edt_afficher_masquer_enregistrements();return false;'>Afficher/masquer les enregistrements effectués.</a><br />
	Par défaut, seuls les enseignements nécessitant un choix seront affichés.</p>";
	}

	echo "<p id='p_cocher_choix_prec' style='display:none;'><a href='javascript:cocher_choix_prec()'>Cocher les choix comme lors du précédent import</a>.</p>";
	echo "<p id='p_lien_choix_prec_deja_fait' style='display:none;'><a href=\"javascript:afficher_masquer_lignes_choix_fait_lors_precedent_import('')\">Afficher</a>/<a href=\"javascript:afficher_masquer_lignes_choix_fait_lors_precedent_import('none')\">masquer</a> ces lignes pour lesquelles l'indétermination a été tranchée lors du précédent import<br /><em>(veillez à cocher ces lignes avec le lien ci-dessus avant de les masquer)</em>.</p>";

	flush();

	$tab_creneaux=get_tab_creneaux();
	$tab_classe=array();
	$tab_prof=array();

	// Identification des cours/groupes

	$nb_cours_enregistres=0;
	$cpt_non_trouve=0;
	$cpt_indecis=0;
	$tab_salle_cours=get_tab_salle_cours();

	$sql_edt_lignes="SELECT * FROM edt_lignes;";
	if($utiliser_choix_prec=="y") {
		// Commencer par recréer les cours identifiés

		//$chaine_details_cours=$edt_cours_id_groupe."|".$edt_cours_id_salle."|".$edt_cours_jour_semaine."|".$edt_cours_id_definie_periode."|".$edt_cours_duree."|".$edt_cours_heuredeb_dec."|".$edt_cours_id_semaine."|".$edt_cours_login_prof;
		$sql_edt_lignes="SELECT * FROM edt_lignes WHERE traitement!='';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql_edt_lignes);
		if(mysqli_num_rows($res)>0) {
			while($tab=mysqli_fetch_assoc($res)) {
/*
echo "<pre>";
print_r($tab);
echo "</pre>";
*/
				$tab2=explode("|", $tab['details_cours']);
/*
echo "<pre>";
print_r($tab2);
echo "</pre>";
*/
				$edt_cours_id_groupe=$tab2[0];
				$edt_cours_id_salle=$tab2[1];
				$edt_cours_jour_semaine=$tab2[2];
				$edt_cours_id_definie_periode=$tab2[3];
				$edt_cours_duree=$tab2[4];
				$edt_cours_heuredeb_dec=$tab2[5];
				$edt_cours_id_semaine=$tab2[6];
				$edt_cours_login_prof=$tab2[7];

				$sql="INSERT INTO edt_cours SET id_groupe='".$edt_cours_id_groupe."',
										id_salle='".$edt_cours_id_salle."',
										jour_semaine='".$edt_cours_jour_semaine."',
										id_definie_periode='".$edt_cours_id_definie_periode."',
										duree='".$edt_cours_duree."',
										heuredeb_dec='".$edt_cours_heuredeb_dec."',
										id_semaine='".$edt_cours_id_semaine."',
										login_prof='".$edt_cours_login_prof."';";
				if($debug_import_edt=="y") {
					echo "$sql<br />";
				}
				$insert=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$insert) {
					echo "<span style='color:red'>Erreur lors de la création du cours : $sql</span><br />";
				}
				else {
					enregistre_corresp_EDT_classe_matiere_GEPI_id_groupe($edt_cours_id_groupe, $tab['classe'], $tab['mat_code']);

					$nb_cours_enregistres++;
				}

			}
		}

		echo "<p>$nb_cours_enregistres cours enregistré(s) <em>(ré-insérés d'après le précédent traitement)</em>.</p>";

		$sql_edt_lignes="SELECT * FROM edt_lignes WHERE traitement='';";
	}

	function afficher_infos_cours_xml_EDT($tab) {
		$retour="";
		/*
		$tab[id]=1
		$tab[numero]=1
		$tab[classe]=6B
		$tab[mat_code]=AGL1
		$tab[mat_libelle]=ANGLAIS LV1
		$tab[prof_nom]=COURSIER-FRIMONT
		$tab[prof_prenom]=ARIANE
		$tab[salle]=24
		$tab[jour]=lundi
		$tab[h_debut]=08h00
		$tab[duree]=1h00
		$tab[frequence]=H
		$tab[alternance]=H
		$tab[effectif]=21
		$tab[modalite]=CG
		$tab[co_ens]=N
		$tab[pond]=1
		$tab[traitement]=
		$tab[details_cours]=
		*/

		$retour="<table class='boireaus boireaus_alt' title=\"Cours n°".$tab['numero']." du fichier XML.\n(identifiant ".$tab['id']." dans la table 'edt_lignes').\">
	<tr>
		<td>Classe</td>
		<td>".htmlentities($tab['classe'])."</td>
	</tr>
	<tr>
		<td>Matière</td>
		<td>".$tab['mat_code']."<br /><span style='font-size:x-small'>(<em>".$tab['mat_libelle']."</em>)</span></td>
	</tr>
	<tr>
		<td>Professeur</td>
		<td>".$tab['prof_nom']." ".$tab['prof_prenom']."</td>
	</tr>
	<tr>
		<td>Jour</td>
		<td>";
		if($tab['jour']!="") {
			$retour.=$tab['jour'];
		}
		else {
			$retour.="<span style='color:red'>Jour non défini???<br />Pas d'enregistrement possible</span>";
		}
		$retour.="</td>
	</tr>
	<tr>
		<td>Heure</td>
		<td>".$tab['h_debut']."</td>
	</tr>
	<tr>
		<td>Durée</td>
		<td>".$tab['duree']."</td>
	</tr>
	<tr>
		<td>Alternance</td>
		<td>".$tab['alternance']."</td>
	</tr>
	<tr>
		<td>Salle</td>
		<td>".$tab['salle']."</td>
	</tr>
	<tr>
		<td>Effectif</td>
		<td>".$tab['effectif']."</td>
	</tr>
</table>\n";

		return $retour;
	}

	$tab_grp_associes_precedent_import=array();
	$tab_identifiants_precedent_import=array();
	$tab_edt_lignes_precedent_import=array();

	$res=mysqli_query($GLOBALS["mysqli"], $sql_edt_lignes);
	if(mysqli_num_rows($res)>0) {
		while($tab=mysqli_fetch_assoc($res)) {

			$current_nom_regroupement_edt=preg_replace("/\[/", "", preg_replace("/\]/", "", $tab['classe']));

			$lignes_ce_cours="";

			$edt_cours_id_groupe="";
			$edt_cours_id_salle="";
			$edt_cours_jour_semaine="";
			$edt_cours_id_definie_periode="";
			$edt_cours_duree="";
			$edt_cours_heuredeb_dec="";
			$edt_cours_id_semaine="";
			$edt_cours_login_prof="";


			$lignes_ce_cours.="<table class='boireaus boireaus_alt'>
	<tr>
		<th>Enregistrement dans EDT</th>
		<th>Informations trouvées d'après les rapprochements effectués</th>
		<th title=\"Dans le cas où le nom de regroupement EDT a précédemment été associé à un des enseignements Gepi lors d'un précédent import, les informations correspondantes seront affichées ici.\">Choix précédent</th>
	</tr>
	<tr>
		<td>";
			if($tab['classe']=='') {
				$lignes_ce_cours.="<p>Ce cours n'est associé à aucune classe dans EDT.<br />Il se peut qu'il s'agisse de l'emploi du temps d'un(e) surveillant(e) en permanence,...<br />Ce cas n'est pas géré.</p>";
			}
			/*
			$lignes_ce_cours.="<pre>";
			//print_r($tab);
			foreach($tab as $key => $value) {
				$lignes_ce_cours.="\$tab[$key]=$value<br />";
			}
			$lignes_ce_cours.="</pre>";
			*/
			$lignes_ce_cours.=afficher_infos_cours_xml_EDT($tab);

			$lignes_ce_cours.="
		</td>
		<td>";
			/*
			Array
			(
			    [id] => 1
			    [numero] => 1
			    [classe] => 6B
			    [mat_code] => AGL1
			    [mat_libelle] => ANGLAIS LV1
			    [prof_nom] => COURSIER-FRIMONT
			    [prof_prenom] => ARIANE
			    [salle] => 24
			    [jour] => lundi
			    [h_debut] => 08h00
			    [duree] => 1h00
			    [frequence] => H
			    [alternance] => H
			    [effectif] => 21
			    [modalite] => CG
			    [co_ens] => N
			    [pond] => 1
			)
			*/

			$id_ligne=$tab['id'];

			$matiere=get_corresp_edt("matiere", $tab['mat_code']);
			$classe=get_corresp_edt("classe", $tab['classe']);
			if($debug_import_edt=="y") {
				$lignes_ce_cours.="\$tab['classe']=".$tab['classe']."<br />";
				$lignes_ce_cours.="classe=$classe<br />";
			}
			$groupes=get_corresp_edt("groupe", $tab['classe']);
			$salle=get_corresp_edt("salle", $tab['salle']);

			//20170907
			//$prof=get_corresp_edt("prof", $tab['prof_nom']." ".$tab['prof_prenom']);
			$tmp_prof=get_corresp_edt("prof", $tab['prof_nom']." ".$tab['prof_prenom']);
			$edt_cours_login_prof=$tmp_prof;
			if(preg_match("/|/", $tmp_prof)) {
				$prof=array();
				$tmp_tab=explode("|",$tmp_prof);
				for($loop=0;$loop<count($tmp_tab);$loop++) {
					if(trim($tmp_tab[$loop])!="") {
						$prof[]=$tmp_tab[$loop];
					}
				}
			}
			else {
				$prof=$tmp_prof;
			}

			$jour=get_corresp_edt("jour", $tab['jour']);
			$h_debut=get_corresp_edt("h_debut", $tab['h_debut']);
			$frequence=get_corresp_edt("frequence", $tab['frequence']);

			$classe_aff=$classe;
			if(preg_match("/^[0-9]{1,}$/", $classe)) {
				if(!isset($tab_classe[$classe])) {
					$tab_classe[$classe]=get_nom_classe($classe);
				}
				$classe_aff=$tab_classe[$classe];
			}

			$groupes_aff="";
			$tmp_tab=explode("|", $groupes);
			for($loop=0;$loop<count($tmp_tab);$loop++) {
				if($tmp_tab[$loop]!="") {
					if(!isset($tab_classe[$tmp_tab[$loop]])) {
						$tab_classe[$tmp_tab[$loop]]=get_nom_classe($tmp_tab[$loop]);
					}
					if($groupes_aff!="") {$groupes_aff.=", ";}
					//$groupes_aff.=$tab_classe[$tmp_tab[$loop]];
					$groupes_aff.="<a href='../groupes/edit_class.php?id_classe=".$tmp_tab[$loop]."' title=\"Voir les enseignements de cette classe dans un nouvel onglet\" target='_blank'>".$tab_classe[$tmp_tab[$loop]].'</a>';
				}
			}

			if($debug_import_edt=="y") {
				$lignes_ce_cours.="matiere=$matiere<br />";
				$lignes_ce_cours.="classe=$classe<br />";
				$lignes_ce_cours.="groupes=$groupes<br />";
				//$lignes_ce_cours.="salle=";
			}
			$salle_aff="";
			if(isset($tab_salle_cours['indice'][$salle])) {
				//$lignes_ce_cours.=$tab_salle_cours['indice'][$salle]['numero_salle'];
				//if($tab_salle_cours['indice'][$salle]['nom_salle']!="") {$lignes_ce_cours.=" (".$tab_salle_cours['indice'][$salle]['nom_salle'].")";}

				$salle_aff.=$tab_salle_cours['indice'][$salle]['numero_salle'];
				if($tab_salle_cours['indice'][$salle]['nom_salle']!="") {$salle_aff.=" (".$tab_salle_cours['indice'][$salle]['nom_salle'].")";}

				$edt_cours_id_salle=$salle;
			}

			//$lignes_ce_cours.="<br />";
			//$lignes_ce_cours.="prof=$prof<br />";
			//$edt_cours_login_prof=$prof;
			$prof_aff="";
			/*
			if($prof!="") {
				if(!isset($tab_prof[$prof])) {
					$tab_prof[$prof]=civ_nom_prenom($prof);
				}
				$prof_aff=$tab_prof[$prof];
			}
			*/
			$temoin_prof="";
			if(is_array($prof)) {
				for($loop_prof=0;$loop_prof<count($prof);$loop_prof++) {
					//echo "prof[$loop_prof]=".$prof[$loop_prof]."<br />";
					if($prof[$loop_prof]!="") {
						$temoin_prof="OK";
						if($prof_aff!="") {
							$prof_aff.=", ";
						}
						if(!isset($tab_prof[$prof[$loop_prof]])) {
							$tab_prof[$prof[$loop_prof]]=civ_nom_prenom($prof[$loop_prof]);
						}
						$prof_aff.=$tab_prof[$prof[$loop_prof]];
					}
				}
			}
			else {
				if(!isset($tab_prof[$prof])) {
					$tab_prof[$prof]=civ_nom_prenom($prof);
				}
				$prof_aff=$tab_prof[$prof];
				//echo "prof=$prof<br />";
				if($prof!="") {$temoin_prof="OK";}
			}

			//$lignes_ce_cours.="jour=$jour<br />";
			$edt_cours_jour_semaine=$jour;

			$heure_aff="";
			//$lignes_ce_cours.="h_debut=";
			$tab_h_debut=explode("|", $h_debut);
			if(isset($tab_h_debut[1])) {
				$id_creneau=$tab_h_debut[0];
				$demi_creneau=$tab_h_debut[1];
				if(isset($tab_creneaux['indice'][$id_creneau])) {
					$edt_cours_id_definie_periode=$id_creneau;

					if($demi_creneau==0) {
						//$lignes_ce_cours.=$tab_creneaux['indice'][$id_creneau]['debut_court'];
						//$lignes_ce_cours.=" <span style='font-size:small'>(début du créneau ".$tab_creneaux['indice'][$id_creneau]['nom_definie_periode']." (".$tab_creneaux['indice'][$id_creneau]['debut_court']."-".$tab_creneaux['indice'][$id_creneau]['fin_court']."))</span>";

						$heure_aff.=$tab_creneaux['indice'][$id_creneau]['debut_court'];
						$heure_aff.=" <span style='font-size:small'>(début du créneau ".$tab_creneaux['indice'][$id_creneau]['nom_definie_periode']." (".$tab_creneaux['indice'][$id_creneau]['debut_court']."-".$tab_creneaux['indice'][$id_creneau]['fin_court']."))</span>";

						$edt_cours_heuredeb_dec=0;
					}
					else {
						//$lignes_ce_cours.=$tab_creneaux['indice'][$id_creneau]['debut_court']."*";
						//$lignes_ce_cours.=" <span style='font-size:small'>(milieu du créneau ".$tab_creneaux['indice'][$id_creneau]['nom_definie_periode']." (".$tab_creneaux['indice'][$id_creneau]['debut_court']."-".$tab_creneaux['indice'][$id_creneau]['fin_court']."))</span>";

						$heure_aff.=$tab_creneaux['indice'][$id_creneau]['debut_court']."*";
						$heure_aff.=" <span style='font-size:small'>(milieu du créneau ".$tab_creneaux['indice'][$id_creneau]['nom_definie_periode']." (".$tab_creneaux['indice'][$id_creneau]['debut_court']."-".$tab_creneaux['indice'][$id_creneau]['fin_court']."))</span>";

						$edt_cours_heuredeb_dec=0.5;
					}
				}
			}
			//$lignes_ce_cours.="<br />";
			//$lignes_ce_cours.="frequence=$frequence<br />";
			$edt_cours_id_semaine=$frequence;
			if($edt_cours_id_semaine=="") {
				$edt_cours_id_semaine=0;
			}

			//$lignes_ce_cours.="duree=".$tab['duree']."<br />";
			$tab_duree=explode("h", casse_mot($tab['duree'], "min"));
			if(isset($tab_duree[1])) {
				// 20160907
				/*
				$edt_cours_duree=$tab_duree[0];
				if($tab_duree[1]==30) {
					$edt_cours_duree+=0.5;
				}
				$edt_cours_duree=2*$edt_cours_duree;
				*/

				$tmp_duree=$tab_duree[0]+($tab_duree[1]/60);
				$edt_cours_duree=round($tmp_duree*2);
			}
			//$lignes_ce_cours.=$edt_cours_duree." demi-heures<br />";


			// 20141101
			if(!isset($tab_grp_associes_precedent_import['"'.$current_nom_regroupement_edt.'"'])) {
				$tab_grp_associes_precedent_import['"'.$current_nom_regroupement_edt.'"']['lignes']="";
				$tab_grp_associes_precedent_import['"'.$current_nom_regroupement_edt.'"']['id_groupe']=array();

				//20170908
				//$sql="SELECT * FROM edt_corresp2 ec2, groupes g WHERE nom_groupe_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $tab['classe'])."' AND ec2.id_groupe=g.id;";
				/*
				$sql="SELECT * FROM edt_corresp2 ec2, 
								groupes g 
							WHERE (nom_groupe_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $tab['classe'])."' OR 
							nom_groupe_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], trim(preg_replace("/\[.*\]/", "", $tab['classe'])))."') AND 
								ec2.id_groupe=g.id;";
				*/
				// Le preg_replace nettoye trop: 
				// [4 ARTS2]<4 B> <SIECLE> 4 ARTS2, [4 ARTS2]<4 C> <SIECLE> 4 ARTS2, [4 ARTS2]<4 D> <SIECLE> 4 ARTS2
				// devient
				// <4 D> <SIECLE> 4 ARTS2
				// SELECT * FROM edt_corresp2 ec2, groupes g WHERE (nom_groupe_edt='[4 ARTS2]<4 B> <SIECLE> 4 ARTS2, [4 ARTS2]<4 C> <SIECLE> 4 ARTS2, [4 ARTS2]<4 D> <SIECLE> 4 ARTS2' OR nom_groupe_edt='<4 D> <SIECLE> 4 ARTS2') AND ec2.id_groupe=g.id;

				$sql="SELECT * FROM edt_corresp2 ec2, 
								groupes g 
							WHERE (nom_groupe_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $tab['classe'])."' OR 
							nom_groupe_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], trim(preg_replace("/\[[^\[\]]*\]/", "", $tab['classe'])))."') AND 
								ec2.id_groupe=g.id;";


				//$lignes_ce_cours.="DEBUG : ".htmlentities($sql)."<br />";
				$res_choix_prec=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_choix_prec)>0) {
					// 20150204 : A FAIRE DANS CE CAS : Proposer l'association avec le groupe choisi dans la liste.
					$tab_grp_associes_precedent_import['"'.$current_nom_regroupement_edt.'"']['lignes'].="Lors d'un précédent import de l'emploi du temps, le regroupement EDT ".$tab['classe']." a été associés aux groupes Gepi suivants&nbsp;:<br />";

					while($lig_choix_prec=mysqli_fetch_object($res_choix_prec)) {
						//$tab_grp_associes_precedent_import['"'.$current_nom_regroupement_edt.'"']['lignes'].=get_info_grp($lig_choix_prec->id_groupe)."<br />";
						$tab_grp_associes_precedent_import['"'.$current_nom_regroupement_edt.'"']['lignes'].=$lig_choix_prec->name." <em style='font-size:small'>(".$lig_choix_prec->description.") (<span title='Groupe n°".$lig_choix_prec->id_groupe."'><a href='../groupes/edit_group.php?id_groupe=".$lig_choix_prec->id_groupe."' target='_blank' title=\"Voir le groupe dans un nouvel onglet\">".$lig_choix_prec->id_groupe."</a></span>)</em><br />";

						$tab_grp_associes_precedent_import['"'.$current_nom_regroupement_edt.'"']['id_groupe'][]=$lig_choix_prec->id_groupe;
					}
				}
				
			}


			$lignes_ce_cours.="<table class='boireaus boireaus_alt'>
	<tr>
		<td>Classe</td>
		<td>$classe_aff $groupes_aff</td>
	</tr>
	<!--tr>
		<td>Classes</td>
		<td>$groupes_aff</td>
	</tr-->
	<tr>
		<td>Matière</td>
		<td>$matiere</td>
	</tr>
	<tr>
		<td>Professeur</td>
		<td>$prof_aff</td>
	</tr>
	<tr>
		<td>Jour</td>
		<td>$jour</td>
	</tr>
	<tr>
		<td>Heure</td>
		<td>$heure_aff</td>
	</tr>
	<tr>
		<td>Durée</td>
		<td>$edt_cours_duree demi-heures</td>
	</tr>
	<tr>
		<td>Fréquence</td>
		<td>$frequence</td>
	</tr>
	<tr>
		<td>Salle</td>
		<td>$salle_aff</td>
	</tr>
</table>";


			$lignes_ce_cours.="
		</td>
		<td>
			".$tab_grp_associes_precedent_import['"'.$current_nom_regroupement_edt.'"']['lignes']."
		</td>
	</tr>
</table>";

/*

mysql> select * from edt_cours limit 5;
+----------+-----------+----------+--------------+--------------------+-------+--------------+------------+---------------+-----------+------------+--------+
| id_cours | id_groupe | id_salle | jour_semaine | id_definie_periode | duree | heuredeb_dec | id_semaine | id_calendrier | modif_edt | login_prof | id_aid |
+----------+-----------+----------+--------------+--------------------+-------+--------------+------------+---------------+-----------+------------+--------+
|     1350 | 3222      | 11       | lundi        | 1                  | 2     | 0            | 0          | 0             | 0         | coursiea   |        |
|     1351 | 3235      | inc      | lundi        | 1                  | 2     | 0            | 0          | 0             | 0         | quemenec   |        |
|     1352 | 3235      | inc      | lundi        | 2                  | 2     | 0            | 0          | 0             | 0         | quemenec   |        |
|     1353 | 3258      | inc      | lundi        | 1                  | 2     | 0            | 0          | 0             | 0         | PRUNIERA   |        |
|     1354 | 3258      | inc      | lundi        | 2                  | 2     | 0            | 0          | 0             | 0         | PRUNIERA   |        |
+----------+-----------+----------+--------------+--------------------+-------+--------------+------------+---------------+-----------+------------+--------+
5 rows in set (0.00 sec)

mysql> select distinct heuredeb_dec from edt_cours limit 5;
+--------------+
| heuredeb_dec |
+--------------+
| 0            |
+--------------+
1 row in set (0.00 sec)

mysql> select distinct heuredeb_dec from edt_cours limit 5;
+--------------+
| heuredeb_dec |
+--------------+
| 0            |
+--------------+
1 row in set (0.01 sec)

mysql> select distinct duree from edt_cours limit 5;
+-------+
| duree |
+-------+
| 2     |
| 1     |
+-------+
2 rows in set (0.00 sec)

mysql> select * from edt_cours where duree='1' limit 5;
+----------+-----------+----------+--------------+--------------------+-------+--------------+------------+---------------+-----------+------------+--------+
| id_cours | id_groupe | id_salle | jour_semaine | id_definie_periode | duree | heuredeb_dec | id_semaine | id_calendrier | modif_edt | login_prof | id_aid |
+----------+-----------+----------+--------------+--------------------+-------+--------------+------------+---------------+-----------+------------+--------+
|     2058 | 3119      | 4        | vendredi     | 7                  | 1     | 0            | 0          | 0             | 0         | BOIREAUS   |        |
+----------+-----------+----------+--------------+--------------------+-------+--------------+------------+---------------+-----------+------------+--------+
1 row in set (0.00 sec)

mysql> select * from edt_cours where duree='1' limit 5;
+----------+-----------+----------+--------------+--------------------+-------+--------------+------------+---------------+-----------+------------+--------+
| id_cours | id_groupe | id_salle | jour_semaine | id_definie_periode | duree | heuredeb_dec | id_semaine | id_calendrier | modif_edt | login_prof | id_aid |
+----------+-----------+----------+--------------+--------------------+-------+--------------+------------+---------------+-----------+------------+--------+
|     2058 | 3119      | 4        | vendredi     | 7                  | 1     | 0.5          | 0          | 0             | 0         | BOIREAUS   |        |
+----------+-----------+----------+--------------+--------------------+-------+--------------+------------+---------------+-----------+------------+--------+
1 row in set (0.01 sec)

mysql> 
*/
			$choix_a_faire="n";
			if((is_array($prof))||($prof!="")) {

				// 20170907: Remplir un tableau tmp_tab_prof
				if(is_array($prof)) {
					$tmp_tab_prof=$prof;
				}
				else {
					$tmp_tab_prof=array($prof);
				}
				/*
				foreach($tmp_tab_prof as $key => $current_prof_login) {
					// DEPLACER ICI CE QUI SUIT EN REMPLACANT $prof par $current_prof_login

// NON : On cherche à identifier le groupe
// S'il n'y a qu'un prof, c'est la procédure existante, s'il y en a plusieurs, il faudrait essayer de matcher le groupe qui a ces différents profs



				}
				*/
				// On va se contenter du premier prof du groupe à ce stade
				$current_prof_login=$tmp_tab_prof[0];

				if($classe!="") {
					$tmp_tab_id_groupe_proposes=array();

					$sql="SELECT DISTINCT jgc.id_groupe FROM j_groupes_classes jgc, 
											j_groupes_professeurs jgp, 
											j_groupes_matieres jgm
										WHERE jgc.id_groupe=jgp.id_groupe AND 
											jgp.id_groupe=jgm.id_groupe AND 
											jgc.id_classe='$classe' AND 
											jgm.id_matiere='$matiere' AND 
											jgp.login='$current_prof_login';";
					if($debug_import_edt=="y") {$lignes_ce_cours.="$sql<br />";}
					$res_grp=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_grp)==1) {
						$lig=mysqli_fetch_object($res_grp);
						//$current_group=get_group($lig->id_groupe);
						$lignes_ce_cours.="<span style='color:blue'>".get_info_grp($lig->id_groupe)."</span><br />";
						$edt_cours_id_groupe=$lig->id_groupe;
					}
					elseif(mysqli_num_rows($res_grp)>1) {

						$lignes_ce_cours.="<p style='color:black;'>Plusieurs groupes semblent pouvoir convenir&nbsp;:<br />";
						$lignes_ce_cours.="<input type='radio' name='grp_enregistrer_rapprochement[".$tab['id']."]' id='grp_enregistrer_rapprochement_".$tab['id']."_aucun0' value='' onchange=\"change_style_radio(this.name)\" checked /><label for='grp_enregistrer_rapprochement_".$tab['id']."_aucun0' id='texte_grp_enregistrer_rapprochement_".$tab['id']."_aucun0'>---</label><br />";

						while($lig=mysqli_fetch_object($res_grp)) {

							$temoin_choix_precedent="";
							if(in_array($lig->id_groupe, $tab_grp_associes_precedent_import['"'.$current_nom_regroupement_edt.'"']['id_groupe'])) {
								$temoin_choix_precedent=" <img src='../images/icons/flag_green.png' class='icone16' title=\"Choix effectué lors d'un précédent import.\"/ >";
								$tab_identifiants_precedent_import[]="grp_enregistrer_rapprochement_".$tab['id']."_".$lig->id_groupe;
								$tab_edt_lignes_precedent_import[]=$cpt_indecis+1;
							}

							$lignes_ce_cours.="<input type='radio' name='grp_enregistrer_rapprochement[".$tab['id']."]' id='grp_enregistrer_rapprochement_".$tab['id']."_".$lig->id_groupe."' value='".$lig->id_groupe."' onchange=\"change_style_radio(this.name)\"><label for='grp_enregistrer_rapprochement_".$tab['id']."_".$lig->id_groupe."' id='texte_grp_enregistrer_rapprochement_".$tab['id']."_".$lig->id_groupe."'>".get_info_grp($lig->id_groupe)."</label> <a href='#' onclick=\"afficher_details_groupe(".$lig->id_groupe."); return false;\" title=\"Afficher la liste et l'effectif des élèves inscrits dans ce groupe.\"><img src='../images/icons/chercher.png' class='icone16' alt='Voir' /></a>$temoin_choix_precedent<br />";

							$tmp_tab_id_groupe_proposes[]=$lig->id_groupe;
						}

						$lignes_ce_cours.="</p>";
						$cpt_indecis++;
						$choix_a_faire="y";

					}
					else {

						// A FAIRE: Reessayer sans filtrer sur le prof
						//$lignes_ce_cours.="A FAIRE: Reessayer sans filtrer sur le prof<br />";

						if($debug_import_edt=="y") {$lignes_ce_cours.="DEBUG : ECHEC<br />";}

						// 20150204
						if(count($tab_grp_associes_precedent_import['"'.$current_nom_regroupement_edt.'"']['lignes'])>0) {
							//$lignes_ce_cours.="PLOP<br />";

							$lignes_ce_cours.="<p style='color:black;'>Vous pouvez choisir parmi les groupes précédemment associés au regroupement EDT&nbsp;:<br />";

							$lignes_ce_cours.="<input type='radio' name='grp_enregistrer_rapprochement[".$tab['id']."]' id='grp_enregistrer_rapprochement_".$tab['id']."_aucun' value='' onchange=\"change_style_radio(this.name)\" checked /><label for='grp_enregistrer_rapprochement_".$tab['id']."_aucun' id='texte_grp_enregistrer_rapprochement_".$tab['id']."_aucun'>---</label><br />";
							for($loop=0;$loop<count($tab_grp_associes_precedent_import['"'.$current_nom_regroupement_edt.'"']['id_groupe']);$loop++) {
								//$lignes_ce_cours.="<span style='color:red'>".get_info_grp($tab_grp_candidat[$loop])."</span><br />";
								$lignes_ce_cours.="<input type='radio' name='grp_enregistrer_rapprochement[".$tab['id']."]' id='grp_enregistrer_rapprochement_".$tab['id']."_".$tab_grp_associes_precedent_import['"'.$current_nom_regroupement_edt.'"']['id_groupe'][$loop]."' value='".$tab_grp_associes_precedent_import['"'.$current_nom_regroupement_edt.'"']['id_groupe'][$loop]."' onchange=\"change_style_radio(this.name)\"";

								$tmp_tab_id_groupe_proposes[]=$tab_grp_associes_precedent_import['"'.$current_nom_regroupement_edt.'"']['id_groupe'][$loop];
								/*
								if($tab['details_cours']!="") {
									$tmp_tab=explode("|", $tab['details_cours']);
									$tmp_id_groupe=$tmp_tab[0];
									if($tmp_id_groupe==$tab_grp_candidat[$loop]) {
										$lignes_ce_cours.=" selected";
									}
								}
								else {
									$id_groupe_choix_import_xml_precedent=get_id_groupe_from_tab_ligne($tab);
									if(($id_groupe_choix_import_xml_precedent!="")&&($tab_grp_candidat[$loop]==$id_groupe_choix_import_xml_precedent)) {
										$lignes_ce_cours.=" selected";
									}
								}
								*/

								$temoin_choix_precedent="";
								/*
								if(in_array($tab_grp_associes_precedent_import['"'.$current_nom_regroupement_edt.'"']['id_groupe'][$loop], $tab_grp_associes_precedent_import['"'.$current_nom_regroupement_edt.'"']['id_groupe'])) {
									$temoin_choix_precedent=" <img src='../images/icons/flag_green.png' class='icone16' title=\"Choix effectué lors d'un précédent import.\"/ >";
									$tab_identifiants_precedent_import[]="grp_enregistrer_rapprochement_".$tab['id']."_".$tab_grp_associes_precedent_import['"'.$current_nom_regroupement_edt.'"']['id_groupe'][$loop];
									$tab_edt_lignes_precedent_import[]=$cpt_indecis+1;
								}
								*/

								$lignes_ce_cours.="><label for='grp_enregistrer_rapprochement_".$tab['id']."_".$tab_grp_associes_precedent_import['"'.$current_nom_regroupement_edt.'"']['id_groupe'][$loop]."' id='texte_grp_enregistrer_rapprochement_".$tab['id']."_".$tab_grp_associes_precedent_import['"'.$current_nom_regroupement_edt.'"']['id_groupe'][$loop]."'>".get_info_grp($tab_grp_associes_precedent_import['"'.$current_nom_regroupement_edt.'"']['id_groupe'][$loop])."</label> <a href='#' onclick=\"afficher_details_groupe(".$tab_grp_associes_precedent_import['"'.$current_nom_regroupement_edt.'"']['id_groupe'][$loop]."); return false;\" title=\"Afficher la liste et l'effectif des élèves inscrits dans ce groupe.\"><img src='../images/icons/chercher.png' class='icone16' alt='Voir' /></a>$temoin_choix_precedent<br />";
							}

							$lignes_ce_cours.="</p>";
							$cpt_indecis++;
							$choix_a_faire="y";
						}
						/*
						else {
							$cpt_non_trouve++;
						}
						*/


						// Essayer de jouer sur la classe et la matière
						$cpt_grp_classe_matiere=0;
						
						// 20150909
						// PROBLEME : On enregistre un EDT prof... avec $edt_cours_login_prof
						//            Si, on met un groupe avec un autre prof, on va remplir l'EDT de $edt_cours_login_prof et pas celui de l'autre prof associé au groupe.
						//            On fait un test à l'enregistrement et on gère... en espérant qu'il n'y a pas plusieurs profs associés à un groupe
						$chaine_groupes_associes_a_classe_et_matiere="";
						$sql="SELECT DISTINCT jgc.id_groupe FROM j_groupes_classes jgc, 
												j_groupes_matieres jgm
											WHERE jgc.id_groupe=jgm.id_groupe AND 
												jgc.id_classe='$classe' AND 
												jgm.id_matiere='$matiere';";
						if($debug_import_edt=="y") {$lignes_ce_cours.="$sql<br />";}
						$res_grp=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res_grp)>0) {
							while($lig=mysqli_fetch_object($res_grp)) {
								if(!in_array($lig->id_groupe, $tmp_tab_id_groupe_proposes)) {
									$chaine_groupes_associes_a_classe_et_matiere.="<input type='radio' name='grp_enregistrer_rapprochement[".$tab['id']."]' id='grp_enregistrer_rapprochement_".$tab['id']."_".$lig->id_groupe."' value='".$lig->id_groupe."'><label for='grp_enregistrer_rapprochement_".$tab['id']."_".$lig->id_groupe."' id='texte_grp_enregistrer_rapprochement_".$tab['id']."_".$lig->id_groupe."' onchange=\"change_style_radio(this.name)\">".get_info_grp($lig->id_groupe)."</label> <a href='#' onclick=\"afficher_details_groupe(".$lig->id_groupe."); return false;\" title=\"Afficher la liste et l'effectif des élèves inscrits dans ce groupe.\"><img src='../images/icons/chercher.png' class='icone16' alt='Voir' /></a><br />";
									$cpt_grp_classe_matiere++;
								}
							}

							if($chaine_groupes_associes_a_classe_et_matiere!="") {
								$lignes_ce_cours.="<p style='color:black;'>Vous pouvez choisir parmi les groupes associés à cette classe et cette matière, <strong style='color:red'>mais <span title=\"Sur des enseignements à plusieurs profs, l'identification peut aussi ne pas être automatique.\">(peut-être)</span> avec un autre professeur</strong>&nbsp;:<br />";
								$lignes_ce_cours.="<input type='radio' name='grp_enregistrer_rapprochement[".$tab['id']."]' id='grp_enregistrer_rapprochement_".$tab['id']."_aucun2' value='' onchange=\"change_style_radio(this.name)\" checked /><label for='grp_enregistrer_rapprochement_".$tab['id']."_aucun2' id='texte_grp_enregistrer_rapprochement_".$tab['id']."_aucun2'>---</label><br />";
								$lignes_ce_cours.=$chaine_groupes_associes_a_classe_et_matiere;
							}
						}
						

						// Si $choix_a_faire=="n" et pas de groupe trouvé pour classe+matière, alors $cpt_non_trouve++;
						if(($choix_a_faire=="n")&&($cpt_grp_classe_matiere==0)) {
							$cpt_non_trouve++;
						}

					}
				}
				elseif("$groupes"!="") {
					$tmp_tab=explode("|", preg_replace("/^\|/", "", preg_replace("/\|$/", "", $groupes)));
					/*
					echo "\$tmp_tab<pre>";
					print_r($tmp_tab);
					echo "</pre>";
					*/
					$chaine_sql_id_classe="";
					for($loop=0;$loop<count($tmp_tab);$loop++) {
						if($loop>0) {$chaine_sql_id_classe.=" OR ";}
						$chaine_sql_id_classe.=" jgc.id_classe='".$tmp_tab[$loop]."' ";
					}
					if($chaine_sql_id_classe!="") {
						$chaine_sql_id_classe="(".$chaine_sql_id_classe.") AND ";
					}

					/*
					$sql="SELECT DISTINCT jgc.id_groupe FROM j_groupes_classes jgc, 
											j_groupes_professeurs jgp, 
											j_groupes_matieres jgm
										WHERE jgc.id_groupe=jgp.id_groupe AND 
											jgp.id_groupe=jgm.id_groupe AND 
											jgc.id_classe='".$tmp_tab[0]."' AND 
											jgm.id_matiere='$matiere' AND 
											jgp.login='$prof';";
					*/
					$sql="SELECT DISTINCT jgc.id_groupe FROM j_groupes_classes jgc, 
											j_groupes_professeurs jgp, 
											j_groupes_matieres jgm
										WHERE jgc.id_groupe=jgp.id_groupe AND 
											jgp.id_groupe=jgm.id_groupe AND 
											".$chaine_sql_id_classe." 
											jgm.id_matiere='$matiere' AND 
											jgp.login='$current_prof_login';";
					if($debug_import_edt=="y") {$lignes_ce_cours.="$sql<br />";}
					$res_grp=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_grp)==1) {
						$lig=mysqli_fetch_object($res_grp);
						$lignes_ce_cours.="<span style='color:blue'>".get_info_grp($lig->id_groupe)."</span><br />";
						$edt_cours_id_groupe=$lig->id_groupe;
					}
					elseif(mysqli_num_rows($res_grp)>1) {

						if(count($tmp_tab)>1) {
							// Plusieurs classes associées à ce cours dans l'EDT
							$tab_grp_candidat=array();
							while($lig=mysqli_fetch_object($res_grp)) {
								/*
								//$current_group=get_group($lig->id_groupe);
								$lignes_ce_cours.="<span style='color:blue'>".get_info_grp($lig->id_groupe)."</span><br />";
								*/
								for($loop=1;$loop<count($tmp_tab);$loop++) {
									$sql="SELECT DISTINCT jgc.id_groupe FROM j_groupes_classes jgc 
														WHERE jgc.id_groupe='$lig->id_groupe' AND 
															jgc.id_classe='".$tmp_tab[$loop]."';";
									if($debug_import_edt=="y") {$lignes_ce_cours.="$sql<br />";}
									$test_grp=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($test_grp)>0) {
										if(!in_array($lig->id_groupe, $tab_grp_candidat)) {
											$tab_grp_candidat[]=$lig->id_groupe;
										}
									}
								}
							}

							if(count($tab_grp_candidat)==1) {
								$lignes_ce_cours.="<span style='color:blue'>".get_info_grp($tab_grp_candidat[0])."</span><br />";
								$edt_cours_id_groupe=$tab_grp_candidat[0];
							}
							elseif(count($tab_grp_candidat)>1) {
								if($debug_import_edt=="y") {
									$lignes_ce_cours.="<pre>";
									//print_r($tab_grp_candidat);
									foreach($tab_grp_candidat as $key => $value) {
										$lignes_ce_cours.="\$tab_grp_candidat[$key]=$value<br />";
									}
									$lignes_ce_cours.="</pre>";
								}

								$lignes_ce_cours.="<p style='color:black;'>Plusieurs groupes trouvés<br />";
								$lignes_ce_cours.="<input type='radio' name='grp_enregistrer_rapprochement[".$tab['id']."]' id='grp_enregistrer_rapprochement_".$tab['id']."_aucun' value='' onchange=\"change_style_radio(this.name)\" checked /><label for='grp_enregistrer_rapprochement_".$tab['id']."_aucun' id='texte_grp_enregistrer_rapprochement_".$tab['id']."_aucun'>---</label><br />";
								for($loop=0;$loop<count($tab_grp_candidat);$loop++) {
									//$lignes_ce_cours.="<span style='color:red'>".get_info_grp($tab_grp_candidat[$loop])."</span><br />";
									$lignes_ce_cours.="<input type='radio' name='grp_enregistrer_rapprochement[".$tab['id']."]' id='grp_enregistrer_rapprochement_".$tab['id']."_".$tab_grp_candidat[$loop]."' value='".$tab_grp_candidat[$loop]."' onchange=\"change_style_radio(this.name)\"";
									if($tab['details_cours']!="") {
										$tmp_tab=explode("|", $tab['details_cours']);
										$tmp_id_groupe=$tmp_tab[0];
										if($tmp_id_groupe==$tab_grp_candidat[$loop]) {
											$lignes_ce_cours.=" selected";
										}
									}
									else {
										$id_groupe_choix_import_xml_precedent=get_id_groupe_from_tab_ligne($tab);
										if(($id_groupe_choix_import_xml_precedent!="")&&($tab_grp_candidat[$loop]==$id_groupe_choix_import_xml_precedent)) {
											$lignes_ce_cours.=" selected";
										}
									}

									$temoin_choix_precedent="";
									if(in_array($tab_grp_candidat[$loop], $tab_grp_associes_precedent_import['"'.$current_nom_regroupement_edt.'"']['id_groupe'])) {
										$temoin_choix_precedent=" <img src='../images/icons/flag_green.png' class='icone16' title=\"Choix effectué lors d'un précédent import.\"/ >";
										$tab_identifiants_precedent_import[]="grp_enregistrer_rapprochement_".$tab['id']."_".$tab_grp_candidat[$loop];
										$tab_edt_lignes_precedent_import[]=$cpt_indecis+1;
									}
									$lignes_ce_cours.="><label for='grp_enregistrer_rapprochement_".$tab['id']."_".$tab_grp_candidat[$loop]."' id='texte_grp_enregistrer_rapprochement_".$tab['id']."_".$tab_grp_candidat[$loop]."'>".get_info_grp($tab_grp_candidat[$loop])."</label> <a href='#' onclick=\"afficher_details_groupe($tab_grp_candidat[$loop]); return false;\" title=\"Afficher la liste et l'effectif des élèves inscrits dans ce groupe.\"><img src='../images/icons/chercher.png' class='icone16' alt='Voir' /></a>$temoin_choix_precedent<br />";
								}
								$lignes_ce_cours.="</p>";
								$cpt_indecis++;
								$choix_a_faire="y";
							}
							else {

// A FAIRE: Reessayer sans filtrer sur le prof
// A FAIRE: ou proposer les enseignements du prof si c'est une erreur d'association matière

								if($debug_import_edt=="y") {$lignes_ce_cours.="DEBUG : ECHEC<br />";}
								$cpt_non_trouve++;
							}
						}
						else {
							// Une seule classe: il faut proposer le choix, en indiquant les effectifs,...

							$lignes_ce_cours.="<p style='color:black;'>Plusieurs groupes trouvés<br />";
							$lignes_ce_cours.="<input type='radio' name='grp_enregistrer_rapprochement[".$tab['id']."]' id='grp_enregistrer_rapprochement_".$tab['id']."_aucun' value='' onchange=\"change_style_radio(this.name)\" checked><label for='grp_enregistrer_rapprochement_".$tab['id']."_aucun' id='texte_grp_enregistrer_rapprochement_".$tab['id']."_aucun'>---</label><br />";
							while($lig=mysqli_fetch_object($res_grp)) {
								//$lignes_ce_cours.="<span style='color:red'>".get_info_grp($lig->id_groupe)."</span><br />";
								$lignes_ce_cours.="<input type='radio' name='grp_enregistrer_rapprochement[".$tab['id']."]' id='grp_enregistrer_rapprochement_".$tab['id']."_".$lig->id_groupe."' value='".$lig->id_groupe."' onchange=\"change_style_radio(this.name)\"";
								if($tab['details_cours']!="") {
									$tmp_tab=explode("|", $tab['details_cours']);
									$tmp_id_groupe=$tmp_tab[0];
									if($tmp_id_groupe==$lig->id_groupe) {
										$lignes_ce_cours.=" selected";
									}
								}
								else {
									$id_groupe_choix_import_xml_precedent=get_id_groupe_from_tab_ligne($tab);
									if(($id_groupe_choix_import_xml_precedent!="")&&($lig->id_groupe==$id_groupe_choix_import_xml_precedent)) {
										$lignes_ce_cours.=" selected";
									}
								}
								$temoin_choix_precedent="";
								if(in_array($lig->id_groupe, $tab_grp_associes_precedent_import['"'.$current_nom_regroupement_edt.'"']['id_groupe'])) {
									$temoin_choix_precedent=" <img src='../images/icons/flag_green.png' class='icone16' title=\"Choix effectué lors d'un précédent import.\"/ >";
									$tab_identifiants_precedent_import[]="grp_enregistrer_rapprochement_".$tab['id']."_".$lig->id_groupe;
									$tab_edt_lignes_precedent_import[]=$cpt_indecis+1;
								}
								$lignes_ce_cours.="><label for='grp_enregistrer_rapprochement_".$tab['id']."_".$lig->id_groupe."' id='texte_grp_enregistrer_rapprochement_".$tab['id']."_".$lig->id_groupe."'>".get_info_grp($lig->id_groupe)."</label> <a href='#' onclick=\"afficher_details_groupe($lig->id_groupe); return false;\" title=\"Afficher la liste et l'effectif des élèves inscrits dans ce groupe.\"><img src='../images/icons/chercher.png' class='icone16' alt='Voir' /></a>$temoin_choix_precedent<br />";
							}
							$lignes_ce_cours.="</p>";

							$cpt_indecis++;
							$choix_a_faire="y";

						}
					}
					else {
						//$lignes_ce_cours.="A FAIRE: Reessayer sans filtrer sur le prof2<br />";

						$cpt_grp_classe_matiere=0;
						/*$sql="SELECT DISTINCT jgc.id_groupe FROM j_groupes_classes jgc, 
												j_groupes_matieres jgm
											WHERE jgc.id_groupe=jgm.id_groupe AND 
												jgc.id_classe='".$tmp_tab[0]."' AND 
												jgm.id_matiere='$matiere';";
						*/
						$sql="SELECT DISTINCT jgc.id_groupe FROM j_groupes_classes jgc, 
												j_groupes_matieres jgm
											WHERE jgc.id_groupe=jgm.id_groupe AND 
												".$chaine_sql_id_classe."
												jgm.id_matiere='$matiere';";
						if($debug_import_edt=="y") {$lignes_ce_cours.="$sql<br />";}
						$res_grp_sans_filtrage_prof=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res_grp_sans_filtrage_prof)>0) {
							/*
							$current_classe_0=get_nom_classe($tmp_tab[0]);
							$lignes_ce_cours.="Il existe un ou des groupes associés à la classe ".$current_classe_0.", mais pas avec le professeur ".civ_nom_prenom($prof)."<br />";
							while($lig_grp_sans_filtrage_prof=mysqli_fetch_object($res_grp_sans_filtrage_prof)) {
								$lignes_ce_cours.=get_info_grp($lig_grp_sans_filtrage_prof->id_groupe)."<br />";
							}
							$lignes_ce_cours.="Il se peut aussi que le groupe ".$tab['classe']." ait été associé par erreur à la classe de $current_classe_0.<br />Dans ce cas, vous devriez supprimer cette association et refaire une étape de rapprochements.<br />";
							*/
							$chaine_groupes_associes_a_classe_et_matiere="";
							while($lig=mysqli_fetch_object($res_grp_sans_filtrage_prof)) {
								$chaine_groupes_associes_a_classe_et_matiere.="<input type='radio' name='grp_enregistrer_rapprochement[".$tab['id']."]' id='grp_enregistrer_rapprochement_".$tab['id']."_".$lig->id_groupe."' value='".$lig->id_groupe."' onchange=\"change_style_radio(this.name)\"><label for='grp_enregistrer_rapprochement_".$tab['id']."_".$lig->id_groupe."' id='texte_grp_enregistrer_rapprochement_".$tab['id']."_".$lig->id_groupe."'>".get_info_grp($lig->id_groupe)."</label> <a href='#' onclick=\"afficher_details_groupe(".$lig->id_groupe."); return false;\" title=\"Afficher la liste et l'effectif des élèves inscrits dans ce groupe.\"><img src='../images/icons/chercher.png' class='icone16' alt='Voir' /></a><br />";
							}

							if($chaine_groupes_associes_a_classe_et_matiere!="") {
								$lignes_ce_cours.="<p style='color:black;'>Vous pouvez choisir parmi les groupes associés à cette classe et cette matière, <strong style='color:red'>mais <span title=\"Sur des enseignements à plusieurs profs, l'identification peut aussi ne pas être automatique.\">(peut-être)</span> avec un autre professeur</strong>&nbsp;:<br />";
								$lignes_ce_cours.="<input type='radio' name='grp_enregistrer_rapprochement[".$tab['id']."]' id='grp_enregistrer_rapprochement_".$tab['id']."_aucun2' value='' onchange=\"change_style_radio(this.name)\" checked /><label for='grp_enregistrer_rapprochement_".$tab['id']."_aucun2' id='texte_grp_enregistrer_rapprochement_".$tab['id']."_aucun2'>---</label><br />";
								$lignes_ce_cours.=$chaine_groupes_associes_a_classe_et_matiere;
							}

						}
						if($debug_import_edt=="y") {$lignes_ce_cours.="DEBUG : ECHEC<br />";}
						if($cpt_grp_classe_matiere==0) {
							$cpt_non_trouve++;
						}
					}
				}
				else {
					if($debug_import_edt=="y") {$lignes_ce_cours.="DEBUG : ECHEC<br />";}
					$cpt_non_trouve++;
				}
			}
			else {
				// Cas de la vie de classe pas attribuée à un prof bien souvent
				// Pas de prof associé au cours

				if($classe!="") {
					$sql="SELECT DISTINCT jgc.id_groupe FROM j_groupes_classes jgc, 
											j_groupes_matieres jgm
										WHERE jgc.id_groupe=jgm.id_groupe AND 
											jgc.id_classe='$classe' AND 
											jgm.id_matiere='$matiere';";
					if($debug_import_edt=="y") {$lignes_ce_cours.="$sql<br />";}
					$res_grp=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_grp)>0) {
						$lig=mysqli_fetch_object($res_grp);
						//$current_group=get_group($lig->id_groupe);
						$lignes_ce_cours.="<span style='color:blue'>".get_info_grp($lig->id_groupe)."</span><br />";
						$edt_cours_id_groupe=$lig->id_groupe;
					}
					else {
						if($debug_import_edt=="y") {$lignes_ce_cours.="DEBUG : ECHEC<br />";}
						$cpt_non_trouve++;
					}
				}
				elseif("$groupes"!="") {
					$tmp_tab=explode("|", preg_replace("/^\|/", "", preg_replace("/\|$/", "", $groupes)));
					/*
					echo "\$tmp_tab<pre>";
					print_r($tmp_tab);
					echo "</pre>";
					*/

					$sql="SELECT DISTINCT jgc.id_groupe FROM j_groupes_classes jgc, 
											j_groupes_matieres jgm
										WHERE jgc.id_groupe=jgm.id_groupe AND 
											jgc.id_classe='".$tmp_tab[0]."' AND 
											jgm.id_matiere='$matiere';";
					if($debug_import_edt=="y") {$lignes_ce_cours.="$sql<br />";}
					$res_grp=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_grp)==1) {
						$lig=mysqli_fetch_object($res_grp);
						$lignes_ce_cours.="<span style='color:blue'>".get_info_grp($lig->id_groupe)."</span><br />";
						$edt_cours_id_groupe=$lig->id_groupe;
					}
					elseif(mysqli_num_rows($res_grp)>1) {

						if(count($tmp_tab)>1) {
							// Plusieurs classes associées à ce cours dans l'EDT
							$tab_grp_candidat=array();
							while($lig=mysqli_fetch_object($res_grp)) {
								/*
								//$current_group=get_group($lig->id_groupe);
								$lignes_ce_cours.="<span style='color:blue'>".get_info_grp($lig->id_groupe)."</span><br />";
								*/
								for($loop=1;$loop<count($tmp_tab);$loop++) {
									$sql="SELECT DISTINCT jgc.id_groupe FROM j_groupes_classes jgc 
														WHERE jgc.id_groupe='$lig->id_groupe' AND 
															jgc.id_classe='".$tmp_tab[$loop]."';";
									$lignes_ce_cours.="$sql<br />";
									$test_grp=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($test_grp)>0) {
										$tab_grp_candidat[]=$lig->id_groupe;
									}
								}
							}

							if(count($tab_grp_candidat)==1) {
								$lignes_ce_cours.="<span style='color:blue'>".get_info_grp($tab_grp_candidat[0])."</span><br />";
								$edt_cours_id_groupe=$tab_grp_candidat[0];
							}
							elseif(count($tab_grp_candidat)>1) {
								$lignes_ce_cours.="<p style='color:black;'>Plusieurs groupes trouvés<br />";
								$lignes_ce_cours.="<pre>";
								//print_r($tab_grp_candidat);
								foreach($tab_grp_candidat as $key => $value) {
									$lignes_ce_cours.="\$tab_grp_candidat[$key]=$value<br />";
								}
								$lignes_ce_cours.="</pre>";
								$lignes_ce_cours.="<input type='radio' name='grp_enregistrer_rapprochement[".$tab['id']."]' id='grp_enregistrer_rapprochement_".$tab['id']."_aucun' value='' onchange=\"change_style_radio(this.name)\" checked><label for='grp_enregistrer_rapprochement_".$tab['id']."_aucun' id='texte_grp_enregistrer_rapprochement_".$tab['id']."_aucun'>---</label><br />";
								for($loop=0;$loop<count($tab_grp_candidat);$loop++) {
									//$lignes_ce_cours.="<span style='color:red'>".get_info_grp($tab_grp_candidat[$loop])."</span><br />";
									$lignes_ce_cours.="<input type='radio' name='grp_enregistrer_rapprochement[".$tab['id']."]' id='grp_enregistrer_rapprochement_".$tab['id']."_".$tab_grp_candidat[$loop]."' value='".$tab_grp_candidat[$loop]."' onchange=\"change_style_radio(this.name)\"";
									if($tab['details_cours']!="") {
										$tmp_tab=explode("|", $tab['details_cours']);
										$tmp_id_groupe=$tmp_tab[0];
										if($tmp_id_groupe==$tab_grp_candidat[$loop]) {
											$lignes_ce_cours.=" selected";
										}
									}
									else {
										$id_groupe_choix_import_xml_precedent=get_id_groupe_from_tab_ligne($tab);
										if(($id_groupe_choix_import_xml_precedent!="")&&($tab_grp_candidat[$loop]==$id_groupe_choix_import_xml_precedent)) {
											$lignes_ce_cours.=" selected";
										}
									}
									$temoin_choix_precedent="";
									if(in_array($tab_grp_candidat[$loop], $tab_grp_associes_precedent_import['"'.$current_nom_regroupement_edt.'"']['id_groupe'])) {
										$temoin_choix_precedent=" <img src='../images/icons/flag_green.png' class='icone16' title=\"Choix effectué lors d'un précédent import.\"/ >";
										$tab_identifiants_precedent_import[]="grp_enregistrer_rapprochement_".$tab['id']."_".$tab_grp_candidat[$loop];
										$tab_edt_lignes_precedent_import[]=$cpt_indecis+1;
									}
									$lignes_ce_cours.="><label for='grp_enregistrer_rapprochement_".$tab['id']."_".$tab_grp_candidat[$loop]."' id='texte_grp_enregistrer_rapprochement_".$tab['id']."_".$tab_grp_candidat[$loop]."'>".get_info_grp($tab_grp_candidat[$loop])."</label> <a href='#' onclick=\"afficher_details_groupe($tab_grp_candidat[$loop]); return false;\" title=\"Afficher la liste et l'effectif des élèves inscrits dans ce groupe.\"><img src='../images/icons/chercher.png' class='icone16' alt='Voir' /></a>$temoin_choix_precedent<br />";
								}
								$lignes_ce_cours.="</p>";
								$cpt_indecis++;
								$choix_a_faire="y";
							}
							else {
								if($debug_import_edt=="y") {$lignes_ce_cours.="DEBUG : ECHEC<br />";}
								$cpt_non_trouve++;
							}
						}
						else {
							// Une seule classe: il faut proposer le choix, en indiquant les effectifs,...

							$lignes_ce_cours.="<p style='color:black;'>Plusieurs groupes trouvés<br />";
							$lignes_ce_cours.="<input type='radio' name='grp_enregistrer_rapprochement[".$tab['id']."]' id='grp_enregistrer_rapprochement_".$tab['id']."_aucun' value='' onchange=\"change_style_radio(this.name)\" checked><label for='grp_enregistrer_rapprochement_".$tab['id']."_aucun' id='texte_grp_enregistrer_rapprochement_".$tab['id']."_aucun'>---</label><br />";
							while($lig=mysqli_fetch_object($res_grp)) {
								//$lignes_ce_cours.="<span style='color:red'>".get_info_grp($lig->id_groupe)."</span><br />";
								$lignes_ce_cours.="<input type='radio' name='grp_enregistrer_rapprochement[".$tab['id']."]' id='grp_enregistrer_rapprochement_".$tab['id']."_".$lig->id_groupe."' value='".$lig->id_groupe."' onchange=\"change_style_radio(this.name)\"";
								if($tab['details_cours']!="") {
									$tmp_tab=explode("|", $tab['details_cours']);
									$tmp_id_groupe=$tmp_tab[0];
									if($tmp_id_groupe==$lig->id_groupe) {
										$lignes_ce_cours.=" selected";
									}
								}
								else {
									$id_groupe_choix_import_xml_precedent=get_id_groupe_from_tab_ligne($tab);
									if(($id_groupe_choix_import_xml_precedent!="")&&($lig->id_groupe==$id_groupe_choix_import_xml_precedent)) {
										$lignes_ce_cours.=" selected";
									}
								}
								$temoin_choix_precedent="";
								if(in_array($lig->id_groupe, $tab_grp_associes_precedent_import['"'.$current_nom_regroupement_edt.'"']['id_groupe'])) {
									$temoin_choix_precedent=" <img src='../images/icons/flag_green.png' class='icone16' title=\"Choix effectué lors d'un précédent import.\"/ >";
									$tab_identifiants_precedent_import[]="grp_enregistrer_rapprochement_".$tab['id']."_".$lig->id_groupe;
									$tab_edt_lignes_precedent_import[]=$cpt_indecis+1;
								}
								$lignes_ce_cours.="><label for='grp_enregistrer_rapprochement_".$tab['id']."_".$lig->id_groupe."' id='texte_grp_enregistrer_rapprochement_".$tab['id']."_".$lig->id_groupe."'>".get_info_grp($lig->id_groupe)."</label> <a href='#' onclick=\"afficher_details_groupe($lig->id_groupe); return false;\" title=\"Afficher la liste et l'effectif des élèves inscrits dans ce groupe.\"><img src='../images/icons/chercher.png' class='icone16' alt='Voir' /></a>$temoin_choix_precedent<br />";
							}
							$lignes_ce_cours.="</p>";
							$cpt_indecis++;
							$choix_a_faire="y";
						}
					}
					else {
						if($debug_import_edt=="y") {$lignes_ce_cours.="DEBUG : ECHEC<br />";}
						$cpt_non_trouve++;
					}
				}
				else {
					if($debug_import_edt=="y") {$lignes_ce_cours.="DEBUG : ECHEC<br />";}
					$cpt_non_trouve++;
				}
			}

			if($edt_cours_id_groupe!="") {

				/*
				echo "\$tab['id']=".$tab['id']."<br />";
				echo "\$tab['classe']=".$tab['classe']."<br />";
				echo "\$tab['mat_code']=".$tab['mat_code']."<br />";
				echo "\$edt_cours_id_groupe=$edt_cours_id_groupe<br />";
				*/

				//20170908
				if((is_array($prof))||($prof!="")) {

					// Remplir un tableau tmp_tab_prof
					if(is_array($prof)) {
						$tmp_tab_prof=$prof;
					}
					else {
						$tmp_tab_prof=array($prof);
					}
				}
				else {
					// Cours sans prof?
					$tmp_tab_prof[0]="";
				}

				$temoin_erreur_insert_edt_cours=0;
				for($loop_prof=0;$loop_prof<count($tmp_tab_prof);$loop_prof++) {
					// On va peut-être avoir une blague sur l'EDT de salle avec deux profs pour la même salle
					$sql="INSERT INTO edt_cours SET id_groupe='".$edt_cours_id_groupe."',
											id_salle='".$edt_cours_id_salle."',
											jour_semaine='".$edt_cours_jour_semaine."',
											id_definie_periode='".$edt_cours_id_definie_periode."',
											duree='".$edt_cours_duree."',
											heuredeb_dec='".$edt_cours_heuredeb_dec."',
											id_semaine='".$edt_cours_id_semaine."',
											login_prof='".$tmp_tab_prof[$loop_prof]."';";
					if($debug_import_edt=="y") {
						echo "$sql<br />";
					}
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$insert) {
						echo "<div style='color:red'>".$lignes_ce_cours."</div>";
						echo "<span style='color:red'>Erreur lors de la création du cours : $sql</span><br />";
						echo "<hr />";
						$temoin_erreur_insert_edt_cours++;
					}
				}

				if($temoin_erreur_insert_edt_cours>0) {
					echo "<div style='color:green' id='div_cours_enregistre_".$nb_cours_enregistres."'>".$lignes_ce_cours."<hr /></div>";
					$nb_cours_enregistres++;

					$chaine_details_cours=$edt_cours_id_groupe."|".$edt_cours_id_salle."|".$edt_cours_jour_semaine."|".$edt_cours_id_definie_periode."|".$edt_cours_duree."|".$edt_cours_heuredeb_dec."|".$edt_cours_id_semaine."|".$edt_cours_login_prof;
					$sql="UPDATE edt_lignes SET traitement='cours_identifie_cree', details_cours='".$chaine_details_cours."' WHERE id='$id_ligne';";
					$update=mysqli_query($GLOBALS["mysqli"], $sql);

					enregistre_corresp_EDT_classe_matiere_GEPI_id_groupe($edt_cours_id_groupe, $tab['classe'], $tab['mat_code']);

				}
/*


mysql> select * from edt_cours where duree='1' limit 5;
+----------+-----------+----------+--------------+--------------------+-------+--------------+------------+---------------+-----------+------------+--------+
| id_cours | id_groupe | id_salle | jour_semaine | id_definie_periode | duree | heuredeb_dec | id_semaine | id_calendrier | modif_edt | login_prof | id_aid |
+----------+-----------+----------+--------------+--------------------+-------+--------------+------------+---------------+-----------+------------+--------+
|     2058 | 3119      | 4        | vendredi     | 7                  | 1     | 0.5          | 0          | 0             | 0         | BOIREAUS   |        |
+----------+-----------+----------+--------------+--------------------+-------+--------------+------------+---------------+-----------+------------+--------+

*/
			}
			elseif($choix_a_faire=="y") {
				// Choix à faire...
				echo "<div style='color:darkorange;' id='div_choix_a_faire_".$cpt_indecis."'>".$lignes_ce_cours;
				//echo "</div>";
				echo "<hr />";
				echo "</div>";
			}
			else {
				// Pas trouvé du tout.
				echo "<div style='color:red'>".$lignes_ce_cours."</div>";
				echo "<hr />";
			}

		}
	}

	$texte_infobulle="<div id='div_detail_groupe_corps_bis'>Patience...</div>";
	//<div id='div_detail_groupe_titre2'>Groupe Gepi</div>
	$tabdiv_infobulle[]=creer_div_infobulle("div_detail_groupe","Groupe Gepi","",$texte_infobulle,"",20,0,'y','y','n','n');

	$lignes_js_choix_prec="";
	if(count($tab_identifiants_precedent_import)>0) {
		echo "<p><a href='javascript:cocher_choix_prec()'>Cocher les choix comme lors du précédent import</a>.</p>";
		$lignes_js_choix_prec.="function cocher_choix_prec() {
	var tab_choix_prec=new Array(";
		for($loop=0;$loop<count($tab_identifiants_precedent_import);$loop++) {
			if($loop>0) {
				$lignes_js_choix_prec.=", ";
			}
			$lignes_js_choix_prec.="'".$tab_identifiants_precedent_import[$loop]."'";
		}
		$lignes_js_choix_prec.=");
	for(j=0;j<tab_choix_prec.length;j++) {
		document.getElementById(tab_choix_prec[j]).checked=true;
		// 20160926
		id_change_style_radio(tab_choix_prec[j]);
	}

	//change_style_radio();
}

if(document.getElementById('p_cocher_choix_prec')) {
	document.getElementById('p_cocher_choix_prec').style.display='';
}
";
		// DEBUG
		//echo "<p>\$lignes_js_choix_prec=$lignes_js_choix_prec</p>";
	}

	//div_choix_a_faire_
	$lignes_js_choix_prec_deja_fait="";
	if(count($tab_edt_lignes_precedent_import)>0) {
		$lignes_js_choix_prec_deja_fait="
	function afficher_masquer_lignes_choix_fait_lors_precedent_import(mode) {";
		for($loop=0;$loop<count($tab_edt_lignes_precedent_import);$loop++) {
			$lignes_js_choix_prec_deja_fait.="
		if(document.getElementById('div_choix_a_faire_".$tab_edt_lignes_precedent_import[$loop]."')) {
			document.getElementById('div_choix_a_faire_".$tab_edt_lignes_precedent_import[$loop]."').style.display=mode;
		}";
		}
		$lignes_js_choix_prec_deja_fait.="
	}

	if(document.getElementById('p_lien_choix_prec_deja_fait')) {
		document.getElementById('p_lien_choix_prec_deja_fait').style.display='';
	}";
	}

	echo "<script type='text/javascript'>

	".js_change_style_radio2($nom_js_func="change_style_radio", "n", "y", 'checkbox_change', 'texte_')."

	$lignes_js_choix_prec
	$lignes_js_choix_prec_deja_fait

	var etat_affichage_enregistrements='y';

	function import_edt_afficher_masquer_enregistrements() {
		if(document.getElementById('div_info_affichage_masquage')) {
			//document.getElementById('div_info_affichage_masquage').innerHTML=\"<img src='../images/spinner.gif' class='icone16' /><span style='color:red'> Patience...</span>\";
			document.getElementById('div_info_affichage_masquage').style.display='';
		}

		div=document.getElementsByTagName('div');
		for(i=0;i<div.length;i++) {
			if(div[i].getAttribute('id')) {
				id=div[i].getAttribute('id');
				//if(i<30) {alert(id)}
				if(id.substr(0,21)=='div_cours_enregistre_') {
					if(etat_affichage_enregistrements=='n') {
						document.getElementById(id).style.display='';
					}
					else {
						document.getElementById(id).style.display='none';
					}
				}
			}
		}

		if(etat_affichage_enregistrements=='n') {
			etat_affichage_enregistrements='y';
		}
		else {
			etat_affichage_enregistrements='n';
		}

		if(document.getElementById('div_info_affichage_masquage')) {
			document.getElementById('div_info_affichage_masquage').style.display='none';
		}
	}

	import_edt_afficher_masquer_enregistrements();

	function afficher_details_groupe(id_groupe) {
		new Ajax.Updater($('div_detail_groupe_corps_bis'),'".$_SERVER['PHP_SELF']."?afficher_details_groupe_gepi=y&id_groupe='+id_groupe,{method: 'get'});
		//document.getElementById('div_detail_groupe_titre').innerHTML='Groupe n°'+id_groupe;
		afficher_div('div_detail_groupe','y',10,10);
	}

	change_style_radio();

</script>";

	echo $nb_cours_enregistres." cours déjà enregistrés.<br />";

	if(($cpt_indecis>0)||($cpt_non_trouve>0)) {
		echo "
		<p><input type='hidden' name='action' value='enregistrer_cours_indecis' />
		<p><input type='submit' value='Valider' /></p>";
	}
	echo "
	</fieldset>
</form>";

	echo "<p>".$cpt_non_trouve." identifications en échec.<br />";
	echo $cpt_indecis." identifications nécessitant un choix du groupe.<br />Les tableaux ci-dessus doivent vous permettre de régler les indéterminées.</p><p><br /></p>";

	require("../lib/footer.inc.php");
	die();
}
elseif($action=="editer_corresp") {

	$tab_salle_cours=get_tab_salle_cours();
	$tab_creneaux=get_tab_creneaux();

	$cpt_suppr=0;
	$tab_champs=array("matiere", "classe", "groupe", "salle", "jour", "prof", "h_debut", "frequence");
	for($loop=0;$loop<count($tab_champs);$loop++) {
		$sql="SELECT * FROM edt_corresp WHERE champ='".$tab_champs[$loop]."' ORDER BY nom_edt;";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		$nb_reg_edt_corresp=mysqli_num_rows($test);

		echo "
<h3>".ucfirst($tab_champs[$loop])."</h3>
<div style='margin-left:3em;'>
	<p>".$nb_reg_edt_corresp." correspondances '".$tab_champs[$loop]."' enregistrées.<br />";
		if($nb_reg_edt_corresp>0) {
			echo "
	<a href='".$_SERVER['PHP_SELF']."?action=editer_corresp&amp;vider=".$tab_champs[$loop]."".add_token_in_url()."'>Supprimer toutes les correspondances enregistrées pour le champ '".$tab_champs[$loop]."'</a></p>

	<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>
		<fieldset class='fieldset_opacite50'>
			".add_token_field()."

			<table class='boireaus boireaus_alt resizable sortable'>
				<tr>
					<th class='text' title='Trier suivant cette colonne'>EDT</th>
					<th class='text' title='Trier suivant cette colonne'>GEPI</th>
					<th class='text' title='Trier suivant cette colonne'>Supprimer</th>
				</tr>";
			while($lig=mysqli_fetch_object($test)) {
				echo "
				<tr>
					<td><label for='suppr_$cpt_suppr' >$lig->nom_edt</label></td>
					<td><label for='suppr_$cpt_suppr' >";
				if($tab_champs[$loop]=='classe') {
					echo get_nom_classe($lig->nom_gepi);
				}
				elseif($tab_champs[$loop]=='groupe') {
					//echo $lig->nom_gepi;
					$tmp_tab=explode("|",$lig->nom_gepi);
					for($loop2=0;$loop2<count($tmp_tab);$loop2++) {
						if($tmp_tab[$loop2]!="") {
							echo get_nom_classe($tmp_tab[$loop2])." - ";
						}
					}
				}
				elseif($tab_champs[$loop]=='salle') {
					if(isset($tab_salle_cours['indice'][$lig->nom_gepi]['designation_complete'])) {
						echo $tab_salle_cours['indice'][$lig->nom_gepi]['designation_complete'];
					}
					else {
						echo "Salle d'identifiant ".$lig->nom_gepi." inconnu";
					}
				}
				elseif($tab_champs[$loop]=='prof') {
					echo civ_nom_prenom($lig->nom_gepi);
				}
				elseif($tab_champs[$loop]=='h_debut') {
					$tab_h_debut=explode("|", $lig->nom_gepi);
					if(isset($tab_h_debut[1])) {
						$id_creneau=$tab_h_debut[0];
						echo $tab_creneaux['indice'][$id_creneau]['debut_court'];
						if($tab_h_debut[1]==0) {
							echo " <span style='font-size:small'>(début du créneau ".$tab_creneaux['indice'][$id_creneau]['nom_definie_periode']." (".$tab_creneaux['indice'][$id_creneau]['debut_court']."-".$tab_creneaux['indice'][$id_creneau]['fin_court']."))</span>";
						}
						else {
							echo " <span style='font-size:small'>(milieu du créneau ".$tab_creneaux['indice'][$id_creneau]['nom_definie_periode']." (".$tab_creneaux['indice'][$id_creneau]['debut_court']."-".$tab_creneaux['indice'][$id_creneau]['fin_court']."))</span>";
						}
					}
					else {
						echo "<span style='color:red'>".$lig->nom_gepi."</span>";
					}
				}
				else {
					echo $lig->nom_gepi;
				}
				echo "</label></td>
					<td><input type='checkbox' name='suppr[]' id='suppr_$cpt_suppr' value='$lig->id' /></td>
				</tr>";
				$cpt_suppr++;
			}
			echo "
			</table>
			<p><input type='hidden' name='action' value='editer_corresp' />
			<p><input type='submit' value='Valider' /></p>
		</fieldset>
	</form>";

		}
		echo "
</div>";
	}

	require("../lib/footer.inc.php");
	die();
}
elseif(isset($_POST['grp_enregistrer_rapprochement'])) {
	check_token(false);

	echo "<p>Un ou des enregistrements de rapprochements ont été validés.</p>";

	/*
$_POST[grp_enregistrer_rapprochement]['47']=	3282
$_POST[grp_enregistrer_rapprochement]['59']=	3344
$_POST[grp_enregistrer_rapprochement]['66']=	3296
$_POST[grp_enregistrer_rapprochement]['71']=	3280
$_POST[grp_enregistrer_rapprochement]['76']=	3333
$_POST[grp_enregistrer_rapprochement]['98']=	3307
$_POST[grp_enregistrer_rapprochement]['99']=	3293
$_POST[grp_enregistrer_rapprochement]['103']=	3360
$_POST[grp_enregistrer_rapprochement]['104']=	3359
	*/

	$grp_enregistrer_rapprochement=$_POST['grp_enregistrer_rapprochement'];

	$sql="SELECT * FROM edt_lignes;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p>Aucune ligne n'est enregistrée dans 'edt_lignes'.</p>";
	}
	else {
		$nb_cours_enregistres=0;

		$tab_creneaux=get_tab_creneaux();
		/*
		echo "<pre>";
		print_r($tab_creneaux);
		echo "</pre>";
		*/
		$tab_salle_cours=get_tab_salle_cours();

		while($tab=mysqli_fetch_assoc($res)) {
			$id_ligne=$tab['id'];
			if(isset($grp_enregistrer_rapprochement[$id_ligne])) {
				if($grp_enregistrer_rapprochement[$id_ligne]!='') {
					$edt_cours_id_groupe=$grp_enregistrer_rapprochement[$id_ligne];
					// A FAIRE : Vérifier que cet id_groupe existe, mais ce devrait être le cas

					$edt_cours_id_salle="";
					$edt_cours_jour_semaine="";
					$edt_cours_id_definie_periode="";
					$edt_cours_duree="";
					$edt_cours_heuredeb_dec="";
					$edt_cours_id_semaine="";
					$edt_cours_login_prof="";

					if($debug_import_edt=="y") {
						echo "<pre>";
						print_r($tab);
						echo "</pre>";
					}

					$matiere=get_corresp_edt("matiere", $tab['mat_code']);
					$classe=get_corresp_edt("classe", $tab['classe']);
					$groupes=get_corresp_edt("groupe", $tab['classe']);
					$salle=get_corresp_edt("salle", $tab['salle']);

					//$prof=get_corresp_edt("prof", $tab['prof_nom']." ".$tab['prof_prenom']);
					//20170908
					$tmp_prof=get_corresp_edt("prof", $tab['prof_nom']." ".$tab['prof_prenom']);
					$edt_cours_login_prof=$tmp_prof;
					if(preg_match("/|/", $tmp_prof)) {
						$prof=array();
						$tmp_tab=explode("|",$tmp_prof);
						for($loop=0;$loop<count($tmp_tab);$loop++) {
							if(trim($tmp_tab[$loop])!="") {
								$prof[]=$tmp_tab[$loop];
							}
						}
					}
					else {
						$prof=$tmp_prof;
					}


					$jour=get_corresp_edt("jour", $tab['jour']);
					$h_debut=get_corresp_edt("h_debut", $tab['h_debut']);
					$frequence=get_corresp_edt("frequence", $tab['frequence']);

					if(isset($tab_salle_cours['indice'][$salle])) {
						$edt_cours_id_salle=$salle;
					}

					//$edt_cours_login_prof=$prof;
					$edt_cours_jour_semaine=$jour;

					$tab_h_debut=explode("|", $h_debut);
					/*
					echo "<pre>";
					print_r($tab_h_debut);
					echo "</pre>";
					*/
					if(isset($tab_h_debut[1])) {
						$id_creneau=$tab_h_debut[0];
						$demi_creneau=$tab_h_debut[1];
						if(isset($tab_creneaux['indice'][$id_creneau])) {
							$edt_cours_id_definie_periode=$id_creneau;

							if($demi_creneau==0) {
								$edt_cours_heuredeb_dec=0;
							}
							else {
								$edt_cours_heuredeb_dec=0.5;
							}
						}
					}

					$edt_cours_id_semaine=$frequence;
					if($edt_cours_id_semaine=="") {
						$edt_cours_id_semaine=0;
					}

					$tab_duree=explode("h", casse_mot($tab['duree'], "min"));
					if(isset($tab_duree[1])) {
						// 20160907
						/*
						echo "<pre>";
						print_r($tab);
						echo "</pre>";
						*/
						//echo "\$tab['duree']=".$tab['duree']."<br />";

						$tmp_duree=$tab_duree[0]+($tab_duree[1]/60);
						//echo "\$tmp_duree=$tmp_duree<br />";
						/*
						$edt_cours_duree=$tab_duree[0];
						if($tab_duree[1]==30) {
							$edt_cours_duree+=0.5;
						}
						$edt_cours_duree=2*$edt_cours_duree;
						*/

						$edt_cours_duree=round($tmp_duree*2);
						//echo "\$edt_cours_duree=$edt_cours_duree<br />";
					}
					/*
					elseif(preg_match("^[0-9]*$", $tab['duree'])) {
						$edt_cours_duree=2*$tab['duree'];
					}
					*/

					if($edt_cours_jour_semaine=="") {
						echo "<pre>";
						print_r($tab);
						echo "</pre>";
						echo "<span style='color:red'>Le jour n'a pas été identifié.</span><br />";
					}
					elseif($edt_cours_id_definie_periode=="") {
						echo "<pre>";
						print_r($tab);
						echo "</pre>";
						echo "<span style='color:red'>Le créneau n'a pas été identifié.</span><br />";
					}
					elseif($edt_cours_duree=="") {
						echo "<pre>";
						print_r($tab);
						echo "</pre>";
						echo "<span style='color:red'>La durée n'a pas été identifiée.</span><br />";
					}
					elseif("$edt_cours_heuredeb_dec"=="") {
						echo "<pre>";
						print_r($tab);
						echo "</pre>";
						echo "<span style='color:red'>L'heure de début du cours n'a pas été identifiée.</span><br />";
					}
					else {

						if($edt_cours_login_prof!="") {
							$tmp_tab_login_prof=explode("|", $edt_cours_login_prof);
							foreach($tmp_tab_login_prof as $key => $current_login_prof) {
								//$sql="SELECT login FROM j_groupes_professeurs WHERE id_groupe='".$edt_cours_id_groupe."' AND login='".$edt_cours_login_prof."';";
								$sql="SELECT login FROM j_groupes_professeurs WHERE id_groupe='".$edt_cours_id_groupe."' AND login='".$current_login_prof."';";
								//echo "$sql<br />";
								$test_prof_grp=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($test_prof_grp)==0) {
									//echo "<span style='color:red'>Le groupe n°<a href='../groupes/edit_group.php?id_groupe=".$edt_cours_id_groupe."' target='_blank'>".$edt_cours_id_groupe."</a> (".get_info_grp($edt_cours_id_groupe).") n'est pas associé au professeur ".civ_nom_prenom($edt_cours_login_prof)." mentionné dans l'EDT.</span><br />";
									echo "<span style='color:red'>Le groupe n°<a href='../groupes/edit_group.php?id_groupe=".$edt_cours_id_groupe."' target='_blank'>".$edt_cours_id_groupe."</a> (".get_info_grp($edt_cours_id_groupe).") n'est pas associé au professeur ".civ_nom_prenom($current_login_prof)." mentionné dans l'EDT.</span><br />";
									$sql="SELECT login FROM j_groupes_professeurs WHERE id_groupe='".$edt_cours_id_groupe."';";
									$test_prof_grp=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($test_prof_grp)==1) {
										$lig_prof_grp=mysqli_fetch_object($test_prof_grp);
										$edt_cours_login_prof=$lig_prof_grp->login;
										echo "Enregistrement du cours pour ".civ_nom_prenom($edt_cours_login_prof)." (<em>qui pour sa part est associé au groupe n°<a href='../groupes/edit_group.php?id_groupe=".$edt_cours_id_groupe."' target='_blank'>".$edt_cours_id_groupe."</a></em>).<br />";
										break;
									}
									elseif(mysqli_num_rows($test_prof_grp)>1) {
										$lig_prof_grp=mysqli_fetch_object($test_prof_grp);
										$edt_cours_login_prof=$lig_prof_grp->login;
										echo "Enregistrement du cours pour ".civ_nom_prenom($edt_cours_login_prof)." (<em>premier des ".mysqli_num_rows($test_prof_grp)." professeurs associés au groupe n°<a href='../groupes/edit_group.php?id_groupe=".$edt_cours_id_groupe."' target='_blank'>".$edt_cours_id_groupe."</a></em>).<br />";
										break;
									}
								}
							}
						}

						// Vérification
						$enregistrer_ce_cours="y";
						if($edt_cours_login_prof!="") {
							$tmp_tab_login_prof=explode("|", $edt_cours_login_prof);
							foreach($tmp_tab_login_prof as $key => $current_login_prof) {
								// Il faudrait tester qu'il n'y a pas d'intersection de créneaux->A FAIRE
								// et tester si on essaye de saisir un cours semaine A ou B alors qu'un cours toutes semaines existe->FAIT
								$sql="SELECT * FROM edt_cours WHERE jour_semaine='".$edt_cours_jour_semaine."' AND 
													id_definie_periode='".$edt_cours_id_definie_periode."' AND 
													heuredeb_dec='".$edt_cours_heuredeb_dec."' AND 
													(id_semaine='".$edt_cours_id_semaine."' OR id_semaine='' OR id_semaine='0') AND 
													login_prof='".$current_login_prof."';";
								//echo "$sql<br />";
								if($debug_import_edt=="y") {
									echo "$sql<br />";
								}
								$test=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($test)>0) {
									$lig_cours=mysqli_fetch_object($test);
									echo "<span style='color:red'>".civ_nom_prenom($current_login_prof)." a déjà cours (".get_info_grp($lig_cours->id_groupe).") le ".$edt_cours_jour_semaine." en ".$tab_creneaux['indice'][$edt_cours_id_definie_periode]['nom_definie_periode']." <a href='../edt/index2.php?login_prof=".$current_login_prof."&id_classe=&type_affichage=prof&login_eleve=&affichage=semaine&mode=afficher_edt&afficher_sem_AB=y".add_token_in_url()."' target='_blank'><img src='../images/icons/edt_semAB.png' class='icone16' alt='EDT seul' /></a></span><br />";
									//num_semaine_annee=37|2015&
									$enregistrer_ce_cours="n";
								}
							}
						}

						if($enregistrer_ce_cours=="y") {
							$tmp_tab_login_prof=explode("|", $edt_cours_login_prof);
							$temoin_erreur_pour_un_prof=0;
							foreach($tmp_tab_login_prof as $key => $current_login_prof) {
								$sql="INSERT INTO edt_cours SET id_groupe='".$edt_cours_id_groupe."',
													id_salle='".$edt_cours_id_salle."',
													jour_semaine='".$edt_cours_jour_semaine."',
													id_definie_periode='".$edt_cours_id_definie_periode."',
													duree='".$edt_cours_duree."',
													heuredeb_dec='".$edt_cours_heuredeb_dec."',
													id_semaine='".$edt_cours_id_semaine."',
													login_prof='".$current_login_prof."';";
								//echo "$sql<br />";
								if($debug_import_edt=="y") {
									echo "$sql<br />";
								}
								$insert=mysqli_query($GLOBALS["mysqli"], $sql);
								if(!$insert) {
									echo "<span style='color:red'>Erreur lors de la création du cours : $sql</span><br />";
									$temoin_erreur_pour_un_prof++;
								}
							}

							if($temoin_erreur_pour_un_prof==0) {
								$chaine_details_cours=$edt_cours_id_groupe."|".$edt_cours_id_salle."|".$edt_cours_jour_semaine."|".$edt_cours_id_definie_periode."|".$edt_cours_duree."|".$edt_cours_heuredeb_dec."|".$edt_cours_id_semaine."|".$edt_cours_login_prof;
								$sql="UPDATE edt_lignes SET traitement='choix_effectue', details_cours='".$chaine_details_cours."' WHERE id='$id_ligne';";
								$update=mysqli_query($GLOBALS["mysqli"], $sql);

								$chaine_nom_edt=$tab['classe']."|".$tab['prof_nom']."|".$tab['prof_prenom']."|".$tab['mat_code'];
								$sql="SELECT * FROM edt_corresp WHERE champ='choix_id_groupe' AND nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $chaine_nom_edt)."'";
								//echo "$sql<br />";
								$test=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($test)==0) {
									$sql="INSERT INTO edt_corresp SET traitement='choix_id_groupe', nom_gepi='".$edt_cours_id_groupe."', nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $chaine_nom_edt)."';";
									//echo "$sql<br />";
									$insert=mysqli_query($GLOBALS["mysqli"], $sql);
								}
								else {
									$lig_edt_corresp=mysqli_fetch_object($test);
									$sql="UPDATE edt_corresp SET traitement='choix_id_groupe', nom_gepi='".$edt_cours_id_groupe."' WHERE id='".$lig->id."';";
									//echo "$sql<br />";
									$update=mysqli_query($GLOBALS["mysqli"], $sql);

									//enregistre_corresp_EDT_classe_matiere_GEPI_id_groupe($edt_cours_id_groupe, $tab['classe'], $tab['mat_code'], "y");
								}
								enregistre_corresp_EDT_classe_matiere_GEPI_id_groupe($edt_cours_id_groupe, $tab['classe'], $tab['mat_code'], "y");

								$nb_cours_enregistres++;
							}
						}
					}
				}
			}
		}

		echo $nb_cours_enregistres." cours enregistrés.<br />";
	}

}
else {
	echo "plop";
}

require("../lib/footer.inc.php");
?>
