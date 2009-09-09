<?php
/*
 * $Id$
 *
 * Copyright 2001-2004 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// INSERT INTO `droits` VALUES ('/responsables/maj_import2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Mise à jour Sconet', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

$ele_lieu_naissance=getSettingValue("ele_lieu_naissance") ? getSettingValue("ele_lieu_naissance") : "n";

function extr_valeur($lig){
	unset($tabtmp);
	//$tabtmp=explode(">",my_ereg_replace("<",">",$lig));
	$tabtmp=explode(">",strtr($lig,"<",">"));
	return trim($tabtmp[2]);
}

function ouinon($nombre){
	if($nombre==1){return "O";}elseif($nombre==0){return "N";}else{return "";}
}
function sexeMF($nombre){
	//if($nombre==2){return "F";}else{return "M";}
	if($nombre==2){return "F";}elseif($nombre==1){return "M";}else{return "";}
}

function affiche_debug($texte){
	// Passer à 1 la variable pour générer l'affichage des infos de debug...
	$debug=0;
	if($debug==1){
		echo "<font color='green'>".$texte."</font>";
		flush();
	}
}

// Initialisation du répertoire actuel de sauvegarde
$dirname = getSettingValue("backup_directory");

function info_debug($texte,$mode=0) {
	global $step;
	global $dirname;

	$debug=0;
	if($debug==1) {
		if($mode==1) {
			$fich_debug=fopen("../backup/".$dirname."/debug_maj_import2.txt","w+");
			fwrite($fich_debug,"$step;$texte;".time()."\n");
			fclose($fich_debug);
		}
		elseif($mode==2) {
			echo "<p><a href='../backup/".$dirname."/debug_maj_import2.txt' target='_blank'>Fichier debug</a></p>";
		}
		else {
			//$fich_debug=fopen("/tmp/debug_maj_import2.txt","a+");
			$fich_debug=fopen("../backup/".$dirname."/debug_maj_import2.txt","a+");
			fwrite($fich_debug,"$step;$texte;".time()."\n");
			fclose($fich_debug);
		}
	}
}

function maj_ini_prenom($prenom){
	$prenom2="";
	$tab1=explode("-",$prenom);
	for($i=0;$i<count($tab1);$i++){
		if($i>0){
			$prenom2.="-";
		}
		$tab2=explode(" ",$tab1[$i]);
		for($j=0;$j<count($tab2);$j++){
			if($j>0){
				$prenom2.=" ";
			}
			$prenom2.=ucfirst(strtolower($tab2[$j]));
		}
	}
	return $prenom2;
}



function get_commune($code_commune_insee,$mode){
	$retour="";

	$sql="SELECT * FROM communes WHERE code_commune_insee='$code_commune_insee';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$lig=mysql_fetch_object($res);
		if($mode==0) {
			$retour=$lig->commune;
		}
		else {
			$retour=$lig->commune." (<i>".$lig->departement."</i>)";
		}
	}
	return $retour;
}


// Etape...
$step=isset($_POST['step']) ? $_POST['step'] : (isset($_GET['step']) ? $_GET['step'] : NULL);

$parcours_diff=isset($_POST['parcours_diff']) ? $_POST['parcours_diff'] : NULL;

$tab_ele_id=isset($_POST['tab_ele_id']) ? $_POST['tab_ele_id'] : NULL;
$tab_ele_id_diff=isset($_POST['tab_ele_id_diff']) ? $_POST['tab_ele_id_diff'] : NULL;
$nb_parcours=isset($_POST['nb_parcours']) ? $_POST['nb_parcours'] : NULL;

$tab_pers_id=isset($_POST['tab_pers_id']) ? $_POST['tab_pers_id'] : NULL;
$tab_pers_id_diff=isset($_POST['tab_pers_id_diff']) ? $_POST['tab_pers_id_diff'] : NULL;

$total_pers_diff=isset($_POST['total_pers_diff']) ? $_POST['total_pers_diff'] : NULL;

$valid_pers_id=isset($_POST['valid_pers_id']) ? $_POST['valid_pers_id'] : NULL;
$liste_pers_id=isset($_POST['liste_pers_id']) ? $_POST['liste_pers_id'] : NULL;

$tab_adr_id=isset($_POST['tab_adr_id']) ? $_POST['tab_adr_id'] : NULL;
$tab_adr_id_diff=isset($_POST['tab_adr_id_diff']) ? $_POST['tab_adr_id_diff'] : NULL;

/*
$tab_resp_id=isset($_POST['tab_resp_id']) ? $_POST['tab_resp_id'] : NULL;
$tab_resp_id_diff=isset($_POST['tab_resp_id_diff']) ? $_POST['tab_resp_id_diff'] : NULL;
*/

$tab_resp=isset($_POST['tab_resp']) ? $_POST['tab_resp'] : NULL;
$tab_resp_diff=isset($_POST['tab_resp_diff']) ? $_POST['tab_resp_diff'] : NULL;

$total_diff=isset($_POST['total_diff']) ? $_POST['total_diff'] : NULL;

$liste_assoc=isset($_POST['liste_assoc']) ? $_POST['liste_assoc'] : NULL;

$ne_pas_proposer_resp_sans_eleve=isset($_POST['ne_pas_proposer_resp_sans_eleve']) ? $_POST['ne_pas_proposer_resp_sans_eleve'] : (isset($_GET['ne_pas_proposer_resp_sans_eleve']) ? $_GET['ne_pas_proposer_resp_sans_eleve'] : (isset($_SESSION['ne_pas_proposer_resp_sans_eleve']) ? $_SESSION['ne_pas_proposer_resp_sans_eleve'] : "si"));

$stop=isset($_POST['stop']) ? $_POST['stop'] : (isset($_GET['stop']) ? $_GET['stop'] :'n');

//$style_specifique="responsables/maj_import2";

$gepiSchoolRne=getSettingValue("gepiSchoolRne") ? getSettingValue("gepiSchoolRne") : "";

$mysql_collate=getSettingValue("mysql_collate") ? getSettingValue("mysql_collate") : "";
$chaine_mysql_collate="";
if($mysql_collate!="") {$chaine_mysql_collate="COLLATE $mysql_collate";}

//**************** EN-TETE *****************
$titre_page = "Mise à jour eleves/responsables";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

//debug_var();

if(isset($step)) {
	if(($step==0)||
		($step=="0b")||
		($step==1)||
		($step==2)||
		($step==3)||
		($step==10)||
		($step==11)||
		($step==12)||
		($step==13)||
		($step==14)||
		($step==18)
		) {
//		($step==17)

		echo "<div style='float: right; border: 1px solid black; width: 4em;'>
<form name='formstop' action='".$_SERVER['PHP_SELF']."' method='post'>
<input type='checkbox' name='stop' id='stop' value='y' onchange='stop_change()' ";
//if(isset($stop)){
if($stop=='y'){
	echo "checked ";
}
echo "/> <a href='#' onmouseover=\"afficher_div('div_stop','y',10,20);\">Stop</a>
</form>\n";
		echo "</div>\n";

		echo creer_div_infobulle("div_stop","","","Ce bouton permet s'il est coché d'interrompre les passages automatiques à la page suivante","",12,0,"n","n","y","n");

		echo "<script type='text/javascript'>
	temporisation_chargement='ok';
	cacher_div('div_stop');
</script>\n";


							echo "<script type='text/javascript'>
function stop_change(){
	stop='n';
	if(document.getElementById('stop')){
		if(document.getElementById('stop').checked==true){
			stop='y';
		}
	}
	if(document.getElementById('id_form_stop')){
		document.getElementById('id_form_stop').value=stop;
	}
}

function test_stop(num){
	stop='n';
	if(document.getElementById('stop')){
		if(document.getElementById('stop').checked==true){
			stop='y';
		}
	}
	//document.getElementById('id_form_stop').value=stop;
	if(stop=='n'){
		//setTimeout(\"document.location.replace('".$_SERVER['PHP_SELF']."?step=1')\",2000);
		//document.location.replace('".$_SERVER['PHP_SELF']."?step='+num+'&amp;stop='+stop);
		document.location.replace('".$_SERVER['PHP_SELF']."?step='+num+'&stop='+stop);
	}
}

function test_stop_bis(num,cpt_saut_lignes){
//function test_stop_bis(num,cpt){
	stop='n';
	if(document.getElementById('stop')){
		if(document.getElementById('stop').checked==true){
			stop='y';
		}
	}
	//document.getElementById('id_form_stop').value=stop;
	if(stop=='n'){
		//stop='y';
		//document.location.replace('".$_SERVER['PHP_SELF']."?step='+num+'&amp;stop='+stop+'&amp;cpt_saut_lignes='+cpt_saut_lignes);
		//document.location.replace('".$_SERVER['PHP_SELF']."?step='+num+'&amp;stop='+stop+'&amp;cpt_saut_lignes='+cpt);
		//document.location.replace('".$_SERVER['PHP_SELF']."?cpt_saut_lignes='+cpt+'&amp;step='+num+'&amp;stop='+stop);
		//document.location.replace('".$_SERVER['PHP_SELF']."?cpt_saut_lignes='+cpt+'&step='+num+'&stop='+stop);
		document.location.replace('".$_SERVER['PHP_SELF']."?cpt_saut_lignes='+cpt_saut_lignes+'&step='+num+'&stop='+stop);
	}
}

function test_stop2(){
	stop='n';
	if(document.getElementById('stop')){
		if(document.getElementById('stop').checked==true){
			stop='y';
		}
	}
	document.getElementById('id_form_stop').value=stop;
	if(stop=='n'){
		//setTimeout(\"document.forms['formulaire'].submit();\",1000);
		document.forms['formulaire'].submit();
	}
}


function test_stop_suite(num){
	stop='n';
	if(document.getElementById('stop')){
		if(document.getElementById('stop').checked==true){
			stop='y';
		}
	}

	//document.location.replace('".$_SERVER['PHP_SELF']."?step='+num+'&amp;stop='+stop);
	document.location.replace('".$_SERVER['PHP_SELF']."?step='+num+'&stop='+stop);
}

function test_stop_suite_bis(num,cpt_saut_lignes){
	stop='n';
	if(document.getElementById('stop')){
		if(document.getElementById('stop').checked==true){
			stop='y';
		}
	}

	//document.location.replace('".$_SERVER['PHP_SELF']."?step='+num+'&amp;stop='+stop+'&amp;cpt_saut_lignes='+cpt_saut_lignes);
	document.location.replace('".$_SERVER['PHP_SELF']."?step='+num+'&stop='+stop+'&cpt_saut_lignes='+cpt_saut_lignes);
}

</script>\n";

	}
}

echo "<p class='bold'>";
echo "<a href=\"index.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
//echo "</p>\n";


// On fournit les fichiers CSV générés depuis les XML de SCONET...
//if (!isset($is_posted)) {
if(!isset($step)) {
	echo "</p>\n";

	//echo time()."<br />\n";

	echo "<h2>Import/mise à jour des élèves</h2>\n";

	echo "<p>Cette page est destinée à effectuer l'import des élèves et responsables d'après les modifications et ajouts effectués sur Sconet.</p>\n";

	echo "<p>Vous allez importer des fichiers d'exports XML de Sconet.<br />\nLes fichiers requis au cours de la procédure sont dans un premier temps ElevesAvecAdresses.xml, puis le fichier ResponsablesAvecAdresses.xml</p>\n";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";

	//echo "<input type=hidden name='is_posted' value='yes' />\n";
	echo "<input type=hidden name='step' value='0' />\n";
	//echo "<input type=hidden name='mode' value='1' />\n";
	echo "<p>Sélectionnez le fichier <b>ElevesAvecAdresses.xml</b> (<i>ou ElevesSansAdresses.xml</i>):<br />\n";
	echo "<input type=\"file\" size=\"80\" name=\"eleves_xml_file\" /><br />\n";
	if ($gepiSettings['unzipped_max_filesize']>=0) {
		echo "<p style=\"color: red;\">REMARQUE : Vous pouvez fournir à Gepi le fichier compressé issu directement de SCONET. (Ex : ElevesAvecAdresses.zip)</p>";
	}
	//==============================
	// AJOUT pour tenir compte de l'automatisation ou non:
	//echo "<input type='hidden' name='stop' id='id_form_stop' value='$stop' />\n";
	echo "<input type='checkbox' name='stop' id='id_form_stop' value='y' /><label for='id_form_stop' style='cursor: pointer;'> Désactiver le mode automatique.</label>\n";
	//==============================

	echo "<p><input type='submit' value='Valider' /></p>\n";
	echo "</form>\n";

	echo "<p>Il est recommandé d'importer les informations élèves et de ne passer qu'ensuite à l'import des informations responsables.<br />\n";
	echo "<a href='".$_SERVER['PHP_SELF']."?is_posted=y&amp;step=9'>Passer néanmoins à la page d'importation des responsables</a></p>\n";

	echo "<p><br /></p>\n";

	echo "<p><i>NOTE:</i> Après une phase d'analyse des différences, les différences seront affichées et des cases à cocher seront proposées pour valider les modifications.</p>\n";

	// Pour afficher le lien vers le fichier de debug.
	info_debug("",2);
}
else{
	if($step>0){
		echo " | <a href=\"".$_SERVER['PHP_SELF']."\">Mise à jour Sconet</a>";
	}
	echo "</p>\n";

	//echo "\$step=$step<br />\n";

	/*
	if(($step==0)||
		($step==1)||
		($step==2)||
		($step==3)||
		($step==7)||
		($step==8)||
		($step==9)||
		($step==10)||
		($step==11)||
		($step==14)
		) {
		echo "<div style='float: right; border: 1px solid black; width: 4em;'>
<form name='formstop' action='".$_SERVER['PHP_SELF']."' method='post'>
<input type='checkbox' name='stop' id='stop' value='y' /> Stop
</form>
</div>\n";
	}
	*/

	// On va uploader les fichiers XML dans le tempdir de l'utilisateur (administrateur, ou scolarité pour les màj Sconet)
	$tempdir=get_user_temp_directory();
	if(!$tempdir){
		echo "<p style='color:red'>Il semble que le dossier temporaire de l'utilisateur ".$_SESSION['login']." ne soit pas défini!?</p>\n";
		// Il ne faut pas aller plus loin...
		// SITUATION A GERER
	}

	$post_max_size=ini_get('post_max_size');
	$upload_max_filesize=ini_get('upload_max_filesize');
	$max_execution_time=ini_get('max_execution_time');
	$memory_limit=ini_get('memory_limit');

	//if(!isset($_POST['step'])){
	switch($step){
		case "0":
			// Affichage des informations élèves
			echo "<h2>Import/mise à jour des élèves</h2>\n";

			$xml_file = isset($_FILES["eleves_xml_file"]) ? $_FILES["eleves_xml_file"] : NULL;

			if(!is_uploaded_file($xml_file['tmp_name'])) {
				echo "<p style='color:red;'>L'upload du fichier a échoué.</p>\n";

				echo "<p>Les variables du php.ini peuvent peut-être expliquer le problème:<br />\n";
				echo "post_max_size=$post_max_size<br />\n";
				echo "upload_max_filesize=$upload_max_filesize<br />\n";
				echo "</p>\n";

				// Il ne faut pas aller plus loin...
				// SITUATION A GERER
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
					// Il ne faut pas aller plus loin...
					// SITUATION A GERER
					require("../lib/footer.inc.php");
					die();
				}

				echo "<p>Le fichier a été uploadé.</p>\n";

				/*
				echo "\$xml_file['tmp_name']=".$xml_file['tmp_name']."<br />\n";
				echo "\$tempdir=".$tempdir."<br />\n";

				echo "<p>Les variables du php.ini peuvent peut-être expliquer le problème:<br />\n";
				echo "post_max_size=$post_max_size<br />\n";
				echo "upload_max_filesize=$upload_max_filesize<br />\n";
				echo "\$xml_file['size']=".volume_human($xml_file['size'])."<br />\n";
				echo "</p>\n";
				*/

				//$source_file=stripslashes($xml_file['tmp_name']);
				$source_file=$xml_file['tmp_name'];
				$dest_file="../temp/".$tempdir."/eleves.xml";
				$res_copy=copy("$source_file" , "$dest_file");

				//===============================================================
				// ajout prise en compte des fichiers ZIP: Marc Leygnac

				$unzipped_max_filesize=getSettingValue('unzipped_max_filesize')*1024*1024;
				// $unzipped_max_filesize = 0    pas de limite de taille pour les fichiers extraits
				// $unzipped_max_filesize < 0    extraction zip désactivée
				if($unzipped_max_filesize>=0) {
					$fichier_emis=$xml_file['name'];
					$extension_fichier_emis=strtolower(strrchr($fichier_emis,"."));
					if (($extension_fichier_emis==".zip")||($xml_file['type']=="application/zip"))
						{
						require_once('../lib/pclzip.lib.php');
						$archive = new PclZip($dest_file);

						if (($list_file_zip = $archive->listContent()) == 0) {
							echo "<p style='color:red;'>Erreur : ".$archive->errorInfo(true)."</p>\n";
							require("../lib/footer.inc.php");
							die();
						}

						if(sizeof($list_file_zip)!=1) {
							echo "<p style='color:red;'>Erreur : L'archive contient plus d'un fichier.</p>\n";
							require("../lib/footer.inc.php");
							die();
						}

						/*
						echo "<p>\$list_file_zip[0]['filename']=".$list_file_zip[0]['filename']."<br />\n";
						echo "\$list_file_zip[0]['size']=".$list_file_zip[0]['size']."<br />\n";
						echo "\$list_file_zip[0]['compressed_size']=".$list_file_zip[0]['compressed_size']."</p>\n";
						*/
						//echo "<p>\$unzipped_max_filesize=".$unzipped_max_filesize."</p>\n";

						if(($list_file_zip[0]['size']>$unzipped_max_filesize)&&($unzipped_max_filesize>0)) {
							echo "<p style='color:red;'>Erreur : La taille du fichier extrait (<i>".$list_file_zip[0]['size']." octets</i>) dépasse la limite paramétrée (<i>$unzipped_max_filesize octets</i>).</p>\n";
							require("../lib/footer.inc.php");
							die();
						}

						$res_extract=$archive->extract(PCLZIP_OPT_PATH, "../temp/".$tempdir);
						if ($res_extract != 0) {
							echo "<p>Le fichier uploadé a été dézippé.</p>\n";
							$fichier_extrait=$res_extract[0]['filename'];
							unlink("$dest_file"); // Pour Wamp...
							$res_copy=rename("$fichier_extrait" , "$dest_file");
						}
						else {
							echo "<p style='color:red'>Echec de l'extraction de l'archive ZIP.</p>\n";
							require("../lib/footer.inc.php");
							die();
						}
					}
				}
				//fin  ajout prise en compte des fichiers ZIP
				//===============================================================

				if(!$res_copy){
					echo "<p style='color:red;'>La copie du fichier vers le dossier temporaire a échoué.<br />Vérifiez que l'utilisateur ou le groupe apache ou www-data a accès au dossier temp/$tempdir</p>\n";
					// Il ne faut pas aller plus loin...
					// SITUATION A GERER
					require("../lib/footer.inc.php");
					die();
				}
				else{
					echo "<p>La copie du fichier vers le dossier temporaire a réussi.</p>\n";

					info_debug("Mise à jour sconet",1);

					/*
					$sql="CREATE TABLE IF NOT EXISTS `temp_gep_import2` (
					`ID_TEMPO` varchar(40) NOT NULL default '',
					`LOGIN` varchar(40) NOT NULL default '',
					`ELENOM` varchar(40) NOT NULL default '',
					`ELEPRE` varchar(40) NOT NULL default '',
					`ELESEXE` varchar(40) NOT NULL default '',
					`ELEDATNAIS` varchar(40) NOT NULL default '',
					`ELENOET` varchar(40) NOT NULL default '',
					`ELE_ID` varchar(40) NOT NULL default '',
					`ELEDOUBL` varchar(40) NOT NULL default '',
					`ELENONAT` varchar(40) NOT NULL default '',
					`ELEREG` varchar(40) NOT NULL default '',
					`DIVCOD` varchar(40) NOT NULL default '',
					`ETOCOD_EP` varchar(40) NOT NULL default '',
					`ELEOPT1` varchar(40) NOT NULL default '',
					`ELEOPT2` varchar(40) NOT NULL default '',
					`ELEOPT3` varchar(40) NOT NULL default '',
					`ELEOPT4` varchar(40) NOT NULL default '',
					`ELEOPT5` varchar(40) NOT NULL default '',
					`ELEOPT6` varchar(40) NOT NULL default '',
					`ELEOPT7` varchar(40) NOT NULL default '',
					`ELEOPT8` varchar(40) NOT NULL default '',
					`ELEOPT9` varchar(40) NOT NULL default '',
					`ELEOPT10` varchar(40) NOT NULL default '',
					`ELEOPT11` varchar(40) NOT NULL default '',
					`ELEOPT12` varchar(40) NOT NULL default ''
					);";
					*/

					$sql="DROP TABLE IF EXISTS temp_gep_import2;";
					info_debug($sql);
					$suppr_table = mysql_query($sql);

					$sql="CREATE TABLE IF NOT EXISTS `temp_gep_import2` (
					`ID_TEMPO` varchar(40) NOT NULL default '',
					`LOGIN` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELENOM` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELEPRE` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELESEXE` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELEDATNAIS` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELENOET` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELE_ID` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELEDOUBL` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELENONAT` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELEREG` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`DIVCOD` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ETOCOD_EP` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELEOPT1` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELEOPT2` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELEOPT3` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELEOPT4` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELEOPT5` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELEOPT6` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELEOPT7` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELEOPT8` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELEOPT9` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELEOPT10` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELEOPT11` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELEOPT12` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`LIEU_NAISSANCE` varchar(50) $chaine_mysql_collate NOT NULL default ''
					);";
					info_debug($sql);
					$create_table = mysql_query($sql);

					$sql="TRUNCATE TABLE temp_gep_import2;";
					info_debug($sql);
					$vide_table = mysql_query($sql);

					//echo "<p style='color:red;'>DEBUG \$tempdir=$tempdir</p>";

					$fp=fopen($dest_file,"r");
					if($fp){
						// On commence par la section STRUCTURES pour ne récupérer que les ELE_ID d'élèves qui sont dans une classe.
						echo "<p>\n";
						//echo "Analyse du fichier pour extraire les informations de la section STRUCTURES pour ne conserver que les identifiants d'élèves affectés dans une classe...<br />\n";
						echo "Découpage du fichier ElevesSansAdresses suivant les sections ELEVES, OPTIONS et STRUCTURES.<br />\n";

						// PARTIE <STRUCTURES>
						$cpt=0;
						$eleves=array();
						$temoin_structures=0;
						$temoin_struct_ele=-1;
						$temoin_struct=-1;
						$i=-1;
						//while($cpt<count($ligne)){
						while(!feof($fp)){
							$ligne=fgets($fp,4096);

							if(strstr($ligne,"<ELEVES>")){
								echo "Début de la section ELEVES à la ligne <span style='color: blue;'>$cpt</span><br />\n";
								//$temoin_eleves++;
								$fich=fopen("../temp/".$tempdir."/section_eleves.xml","w+");
							}
							elseif(strstr($ligne,"</ELEVES>")){
								echo "Fin de la section ELEVES à la ligne <span style='color: blue;'>$cpt</span><br />\n";
								fclose($fich);
								unset($fich);
								//break;
							}
							elseif(strstr($ligne,"<OPTIONS>")){
								echo "Début de la section OPTIONS à la ligne <span style='color: blue;'>$cpt</span><br />\n";
								//$temoin_options++;
								$fich=fopen("../temp/".$tempdir."/section_options.xml","w+");
							}
							elseif(strstr($ligne,"</OPTIONS>")){
								echo "Fin de la section OPTIONS à la ligne <span style='color: blue;'>$cpt</span><br />\n";
								//$temoin_options++;
								fclose($fich);
								unset($fich);
								//break;
							}
							elseif(strstr($ligne,"<STRUCTURES>")){
								echo "Début de la section STRUCTURES à la ligne <span style='color: blue;'>$cpt</span><br />\n";
								//$temoin_structures++;
								$fich=fopen("../temp/".$tempdir."/section_structures.xml","w+");
							}
							elseif(strstr($ligne,"</STRUCTURES>")){
								echo "Fin de la section STRUCTURES à la ligne <span style='color: blue;'>$cpt</span><br />\n";
								//$temoin_structures++;
								fclose($fich);
								unset($fich);
								break;
							}
							elseif(isset($fich)) {
								fwrite($fich,$ligne);
							}

							$cpt++;
						}
						fclose($fp);







						//if($nb_err==0) {
							//echo "<p>La première phase s'est passée sans erreur.</p>\n";

							echo "<script type='text/javascript'>
	setTimeout(\"test_stop('0b')\",3000);
</script>\n";
						/*
						}
						elseif($nb_err==1) {
							echo "<p>$nb_err erreur.</p>\n";
						}
						else{
							echo "<p>$nb_err erreurs</p>\n";
						}

						$stat=$id_tempo-1-$nb_err;
						echo "<p>$stat associations identifiant élève/classe ont été inséré(s) dans la table 'temp_gep_import2'.</p>\n";
						*/
						//echo "<p><a href='".$_SERVER['PHP_SELF']."?etape=1&amp;step=1'>Suite</a></p>\n";
						//echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=1&amp;stop=$stop'>Suite</a></p>\n";
						//echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=1&amp;stop=y' onClick=\"test_stop_suite('1'); return false;\">Suite</a></p>\n";
						echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=0b&amp;stop=y' onClick=\"test_stop_suite('0b'); return false;\">Suite</a></p>\n";

						require("../lib/footer.inc.php");
						die();






					}
					else{
						echo "<p>ERREUR: Il n'a pas été possible d'ouvrir le fichier en lecture...</p>\n";

						require("../lib/footer.inc.php");
						die();
					}
				}
			}
			break;
		case "0b":

			echo "<h2>Import/mise à jour des élèves</h2>\n";


					$fp=fopen("../temp/".$tempdir."/section_structures.xml","r");
					if($fp){

						echo "<p>\n";
						echo "Analyse de la section STRUCTURES pour ne conserver que les identifiants d'élèves affectés dans une classe...<br />\n";

						$cpt=0;
						$eleves=array();
						$temoin_structures=0;
						$temoin_struct_ele=-1;
						$temoin_struct=-1;
						$i=-1;
						//while($cpt<count($ligne)){
						while(!feof($fp)){
							$ligne=fgets($fp,4096);

							//if($temoin_structures==1){
								//if(strstr($ligne[$cpt],"<STRUCTURES_ELEVE ")){
								if(strstr($ligne,"<STRUCTURES_ELEVE ")){
									$i++;
									$eleves[$i]=array();

									//echo "<p><b>".htmlentities($ligne[$cpt])."</b><br />\n";
									unset($tabtmp);
									//$tabtmp=explode('"',strstr($ligne[$cpt]," ELEVE_ID="));
									$tabtmp=explode('"',strstr($ligne," ELEVE_ID="));
									//$tmp_eleve_id=trim($tabtmp[1]);
									$eleves[$i]['eleve_id']=trim($tabtmp[1]);

									$eleves[$i]["structures"]=array();
									$j=0;
									$temoin_struct_ele=1;
								}
								//if(strstr($ligne[$cpt],"</STRUCTURES_ELEVE>")){
								if(strstr($ligne,"</STRUCTURES_ELEVE>")){
									$temoin_struct_ele=0;
								}
								if($temoin_struct_ele==1){
									//if(strstr($ligne[$cpt],"<STRUCTURE>")){
									if(strstr($ligne,"<STRUCTURE>")){
										$eleves[$i]["structures"][$j]=array();
										$temoin_struct=1;
									}
									//if(strstr($ligne[$cpt],"</STRUCTURE>")){
									if(strstr($ligne,"</STRUCTURE>")){
										$j++;
										$temoin_struct=0;
									}

									$tab_champs_struct=array("CODE_STRUCTURE","TYPE_STRUCTURE");
									if($temoin_struct==1){
										for($loop=0;$loop<count($tab_champs_struct);$loop++){
											//if(strstr($ligne[$cpt],"<".$tab_champs_struct[$loop].">")){
											if(strstr($ligne,"<".$tab_champs_struct[$loop].">")){
												$tmpmin=strtolower($tab_champs_struct[$loop]);
												//$eleves[$i]["structures"][$j]["$tmpmin"]=extr_valeur($ligne[$cpt]);

												//$eleves[$i]["structures"][$j]["$tmpmin"]=extr_valeur($ligne);
												// Suppression des guillemets éventuels
												//$eleves[$i]["structures"][$j]["$tmpmin"]=my_ereg_replace('"','',extr_valeur($ligne));
												$eleves[$i]["structures"][$j]["$tmpmin"]=preg_replace('/"/','',extr_valeur($ligne));

												//echo "\$eleves[$i]["structures"][$j][\"$tmpmin\"]=".$eleves[$i]["structures"][$j]["$tmpmin"]."<br />\n";
												break;
											}
										}
									}
								}
							//}
							$cpt++;
						}
						fclose($fp);

						$nb_err=0;
						// $cpt: Identifiant id_tempo
						$id_tempo=1;
						for($i=0;$i<count($eleves);$i++){

							$temoin_div_trouvee="";
							if(isset($eleves[$i]["structures"])){
								if(count($eleves[$i]["structures"])>0){
									for($j=0;$j<count($eleves[$i]["structures"]);$j++){
										if($eleves[$i]["structures"][$j]["type_structure"]=="D"){
											$temoin_div_trouvee="oui";
											break;
										}
									}
									if($temoin_div_trouvee!=""){
										$eleves[$i]["classe"]=$eleves[$i]["structures"][$j]["code_structure"];
									}
								}
							}

							if($temoin_div_trouvee=='oui'){
								$sql="INSERT INTO temp_gep_import2 SET id_tempo='$id_tempo', ";
								$sql.="ele_id='".$eleves[$i]['eleve_id']."', ";
								$sql.="divcod='".$eleves[$i]['classe']."';";
								//echo "$sql<br />\n";
								info_debug($sql);
								$res_insert=mysql_query($sql);
								if(!$res_insert){
									echo "Erreur lors de la requête $sql<br />\n";
									$nb_err++;
								}
								$id_tempo++;
							}
						}
						if($nb_err==0) {
							echo "<p style='bold'>La première phase s'est passée sans erreur.</p>\n";

							echo "<script type='text/javascript'>
	setTimeout(\"test_stop('1')\",3000);
</script>\n";
						}
						elseif($nb_err==1) {
							echo "<p>$nb_err erreur.</p>\n";
						}
						else{
							echo "<p>$nb_err erreurs</p>\n";
						}

						$stat=$id_tempo-1-$nb_err;
						echo "<p>$stat associations identifiant élève/classe ont été inséré(s) dans la table 'temp_gep_import2'.</p>\n";

						//echo "<p><a href='".$_SERVER['PHP_SELF']."?etape=1&amp;step=1'>Suite</a></p>\n";
						//echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=1&amp;stop=$stop'>Suite</a></p>\n";
						echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=1&amp;stop=y' onClick=\"test_stop_suite('1'); return false;\">Suite</a></p>\n";

						require("../lib/footer.inc.php");
						die();
					}
					else{
						echo "<p>ERREUR: Il n'a pas été possible d'ouvrir le fichier en lecture...</p>\n";

						require("../lib/footer.inc.php");
						die();
					}
				//}
			//}
			break;
		case "1":
			echo "<h2>Import/mise à jour des élèves</h2>\n";

			info_debug("==============================================");
			info_debug("=============== Phase step $step =================");


			// 20090722
			$cpt_saut_lignes=isset($_POST['cpt_saut_lignes']) ? $_POST['cpt_saut_lignes'] : (isset($_GET['cpt_saut_lignes']) ? $_GET['cpt_saut_lignes'] : 0);
			if($cpt_saut_lignes==0) {
				$sql="TRUNCATE TABLE tempo2;";
				info_debug($sql);
				$res0=mysql_query($sql);
			}
			$cpt_saut_lignes_ini=$cpt_saut_lignes;


			//$dest_file="../temp/".$tempdir."/eleves.xml";
			$dest_file="../temp/".$tempdir."/section_eleves.xml";
			$fp=fopen($dest_file,"r");
			if(!$fp){
				echo "<p>Le XML élève n'a pas l'air présent dans le dossier temporaire.<br />Auriez-vous sauté une étape???</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
			else{
				// On récupère les ele_id des élèves qui sont affectés dans une classe
				$sql="SELECT ele_id FROM temp_gep_import2 ORDER BY id_tempo";
				info_debug($sql);
				$res_ele_id=mysql_query($sql);
				affiche_debug("count(\$res_ele_id)=".count($res_ele_id)."<br />");

				unset($tab_ele_id);
				$tab_ele_id=array();
				$cpt=0;
				// Pourquoi est-ce que cela ne fonctionne pas en mysql_fetch_object()???
				// TROUVé: C'EST SENSIBLE à LA CASSE: IL FAUDRAIT $lig->ELE_ID
				//while($lig=mysql_fetch_object($res_ele_id)){
				while($lig=mysql_fetch_array($res_ele_id)){
					//$tab_ele_id[$cpt]="$lig->ele_id";
					$tab_ele_id[$cpt]=$lig[0];
					affiche_debug("\$tab_ele_id[$cpt]=$tab_ele_id[$cpt]<br />");
					$cpt++;
				}

				/*
				echo "<p>Lecture du fichier Elèves...<br />\n";
				//echo "<blockquote>\n";
				while(!feof($fp)){
					$ligne[]=fgets($fp,4096);
				}
				fclose($fp);
				//echo "<p>Terminé.</p>\n";
				*/

				echo "<p>";
				//echo "Analyse du fichier pour extraire les informations de la section ELEVES...<br />\n";
				echo "Traitement de la section ELEVES...<br />\n";
				//echo "<blockquote>\n";

				$cpt=0;
				$eleves=array();
				$temoin_eleves=0;
				$temoin_ele=0;
				$temoin_options=0;
				$temoin_scol=0;
				//Compteur élève:
				$i=-1;

				// 20090722
				$the_end="";
				$saut_effectue="n";

				/*
				$tab_champs_eleve=array("ID_NATIONAL",
				"ELENOET",
				"NOM",
				"PRENOM",
				"DATE_NAISS",
				"DOUBLEMENT",
				"DATE_SORTIE",
				"CODE_REGIME",
				"DATE_ENTREE",
				"CODE_MOTIF_SORTIE",
				"CODE_SEXE",
				"DATE_SORTIE"
				);
				*/

				$tab_champs_eleve=array("ID_NATIONAL",
				"ELENOET",
				"NOM",
				"PRENOM",
				"DATE_NAISS",
				"DOUBLEMENT",
				"DATE_SORTIE",
				"CODE_REGIME",
				"DATE_ENTREE",
				"CODE_MOTIF_SORTIE",
				"CODE_SEXE",
				"CODE_COMMUNE_INSEE_NAISS"
				);


				$tab_champs_scol_an_dernier=array("CODE_STRUCTURE",
				"CODE_RNE",
				"SIGLE",
				"DENOM_PRINC",
				"DENOM_COMPL",
				"LIGNE1_ADRESSE",
				"LIGNE2_ADRESSE",
				"LIGNE3_ADRESSE",
				"LIGNE4_ADRESSE",
				"BOITE_POSTALE",
				"MEL",
				"TELEPHONE",
				"LL_COMMUNE_INSEE"
				);


				// PARTIE <ELEVES>
				//while($cpt<count($ligne)){
				while(!feof($fp)){
				//while((!feof($fp))||($i>200)) {

					// Sauter les lignes
					if($saut_effectue=="n") {
						echo "Saut de <b>$cpt_saut_lignes</b> lignes dans le fichier section_eleves.xml<br />";
						for($loop=0;$loop<$cpt_saut_lignes;$loop++) {$ligne=fgets($fp,4096);}
						$saut_effectue="y";
					}
					$ligne=fgets($fp,4096);

					$cpt_saut_lignes++;

					/*
					//echo "<p>".htmlentities($ligne[$cpt])."<br />\n";
					//if(strstr($ligne[$cpt],"<ELEVES>")){
					if(strstr($ligne,"<ELEVES>")){
						echo "Début de la section ELEVES à la ligne <span style='color: blue;'>$cpt</span><br />\n";
						flush();
						$temoin_eleves++;
					}
					//if(strstr($ligne[$cpt],"</ELEVES>")){
					if(strstr($ligne,"</ELEVES>")){
						echo "Fin de la section ELEVES à la ligne <span style='color: blue;'>$cpt</span><br />\n";
						flush();
						$temoin_eleves++;

						// 20090722
						$the_end="y";

						break;
					}
					if($temoin_eleves==1){
					*/
						//if(strstr($ligne[$cpt],"<ELEVE ")){
						if(strstr($ligne,"<ELEVE ")){
							$i++;
							$eleves[$i]=array();
							$eleves[$i]["scolarite_an_dernier"]=array();

							//echo "<p><b>".htmlentities($ligne[$cpt])."</b><br />\n";
							unset($tabtmp);
							//$tabtmp=explode('"',strstr($ligne[$cpt]," ELEVE_ID="));
							$tabtmp=explode('"',strstr($ligne," ELEVE_ID="));
							$eleves[$i]["eleve_id"]=trim($tabtmp[1]);
							affiche_debug("\$eleves[$i][\"eleve_id\"]=".$eleves[$i]["eleve_id"]."<br />\n");

							unset($tabtmp);
							//$tabtmp=explode('"',strstr($ligne[$cpt]," ELENOET="));
							$tabtmp=explode('"',strstr($ligne," ELENOET="));
							$eleves[$i]["elenoet"]=trim($tabtmp[1]);
							//echo "\$eleves[$i][\"elenoet\"]=".$eleves[$i]["elenoet"]."<br />\n";
							$temoin_ele=1;
						}
						//if(strstr($ligne[$cpt],"</ELEVE>")){
						if(strstr($ligne,"</ELEVE>")){
							$temoin_ele=0;

							// 20090722
							if($i>200) {$the_end="n";break;}
						}
						if($temoin_ele==1){
							//if(strstr($ligne[$cpt],"<SCOLARITE_AN_DERNIER>")){
							if(strstr($ligne,"<SCOLARITE_AN_DERNIER>")){
								$temoin_scol=1;
							}
							//if(strstr($ligne[$cpt],"</SCOLARITE_AN_DERNIER>")){
							if(strstr($ligne,"</SCOLARITE_AN_DERNIER>")){
								$temoin_scol=0;
							}

							if($temoin_scol==0){
								for($loop=0;$loop<count($tab_champs_eleve);$loop++){
									//if(strstr($ligne[$cpt],"<".$tab_champs_eleve[$loop].">")){
									if(strstr($ligne,"<".$tab_champs_eleve[$loop].">")){
										$tmpmin=strtolower($tab_champs_eleve[$loop]);
										//$eleves[$i]["$tmpmin"]=extr_valeur($ligne[$cpt]);

										// Suppression des guillemets éventuels
										//$eleves[$i]["$tmpmin"]=extr_valeur($ligne);
										//$eleves[$i]["$tmpmin"]=my_ereg_replace('"','',extr_valeur($ligne));
										$eleves[$i]["$tmpmin"]=preg_replace('/"/','',extr_valeur($ligne));

										affiche_debug("\$eleves[$i][\"$tmpmin\"]=".$eleves[$i]["$tmpmin"]."<br />\n");
										break;
									}
								}
								if(isset($eleves[$i]["date_naiss"])){
									// A AMELIORER:
									// On passe plusieurs fois dans la boucle (autant de fois qu'il y a de lignes pour l'élève en cours après le repérage de la date...)
									//echo $eleves[$i]["date_naiss"]."<br />\n";
									unset($naissance);
									$naissance=explode("/",$eleves[$i]["date_naiss"]);
									//$eleve_naissance_annee=$naissance[2];
									//$eleve_naissance_mois=$naissance[1];
									//$eleve_naissance_jour=$naissance[0];
									if(isset($naissance[2])){
										$eleve_naissance_annee=$naissance[2];
									}
									else{
										$eleve_naissance_annee="";
									}
									if(isset($naissance[1])){
										$eleve_naissance_mois=$naissance[1];
									}
									else{
										$eleve_naissance_mois="";
									}
									if(isset($naissance[0])){
										$eleve_naissance_jour=$naissance[0];
									}
									else{
										$eleve_naissance_jour="";
									}

									$eleves[$i]["date_naiss"]=$eleve_naissance_annee.$eleve_naissance_mois.$eleve_naissance_jour;
								}
							}
							else{
								//echo "$i - ";
								//$eleves[$i]["scolarite_an_dernier"]=array();
								for($loop=0;$loop<count($tab_champs_scol_an_dernier);$loop++){
									//if(strstr($ligne[$cpt],"<".$tab_champs_scol_an_dernier[$loop].">")){
									if(strstr($ligne,"<".$tab_champs_scol_an_dernier[$loop].">")){
										//echo "$i - ";
										$tmpmin=strtolower($tab_champs_scol_an_dernier[$loop]);
										//$eleves[$i]["scolarite_an_dernier"]["$tmpmin"]=extr_valeur($ligne[$cpt]);
										// Suppression des guillemets éventuels
										//$eleves[$i]["scolarite_an_dernier"]["$tmpmin"]=extr_valeur($ligne);
										//$eleves[$i]["scolarite_an_dernier"]["$tmpmin"]=my_ereg_replace('"','',extr_valeur($ligne));
										$eleves[$i]["scolarite_an_dernier"]["$tmpmin"]=preg_replace('/"/','',extr_valeur($ligne));
										affiche_debug( "\$eleves[$i][\"scolarite_an_dernier\"][\"$tmpmin\"]=".$eleves[$i]["scolarite_an_dernier"]["$tmpmin"]."<br />\n");
										break;
									}
								}
							}
							/*
							if(strstr($ligne[$cpt],"<ID_NATIONAL>")){
								$eleves[$i]["id_national"]=extr_valeur($ligne[$cpt]);
							}
							if(strstr($ligne[$cpt],"<ELENOET>")){
								$eleves[$i]["elenoet"]=extr_valeur($ligne[$cpt]);
							}
							*/
						}
					//}
					$cpt++;
				}
				fclose($fp);
				echo "</p>\n";
				flush();

				affiche_debug("count(\$eleves)=".count($eleves)."<br />\n");
				affiche_debug("count(\$tab_ele_id)=".count($tab_ele_id)."<br />\n");

				//===========================
				// A FAIRE: boireaus 20071115
				// Insérer ici un tableau comme dans la partie ADRESSES pour simuler une barre de progression
				//===========================

				$stat=0;
				$nb_err=0;
				for($i=0;$i<count($eleves);$i++){
					if(in_array($eleves[$i]['eleve_id'],$tab_ele_id)){
						/*
						if(!isset($eleves[$i]["code_sexe"])){
							$remarques[]="Le sexe de l'élève <a href='#sexe_manquant_".$i."'>".$eleves[$i]["nom"]." ".$eleves[$i]["prenom"]."</a> n'est pas renseigné dans Sconet.";
						}
						*/

						$temoin_date_sortie="n";
						if(isset($eleves[$i]['date_sortie'])) {
							echo $eleves[$i]['prenom']." ".$eleves[$i]['nom']." a quitté l'établissement le ".$eleves[$i]['date_sortie']."<br />\n";

							$tmp_tab_date=explode("/",$eleves[$i]['date_sortie']);
							if(checkdate($tmp_tab_date[1],$tmp_tab_date[0],$tmp_tab_date[2])) {
								$timestamp_sortie=mktime(0,0,0,$tmp_tab_date[1],$tmp_tab_date[0],$tmp_tab_date[2]);
								$timestamp_instant=time();
								if($timestamp_instant>$timestamp_sortie){
									$temoin_date_sortie="y";
								}
							}
						}

						if($temoin_date_sortie=="y") {
							$sql="DELETE FROM temp_gep_import2 WHERE ele_id='".$eleves[$i]['eleve_id']."';";
							info_debug($sql);
							$nettoyage=mysql_query($sql);

							$sql="INSERT INTO tempo2 SET col1='ele_id_eleve_parti', col2='".$eleves[$i]['eleve_id']."';";
							info_debug($sql);
							$insert=mysql_query($sql);
						}
						else {
							$sql="UPDATE temp_gep_import2 SET ";
							$sql.="elenoet='".$eleves[$i]['elenoet']."', ";
							if(isset($eleves[$i]['id_national'])) {$sql.="elenonat='".$eleves[$i]['id_national']."', ";}
							//$sql.="elenom='".addslashes($eleves[$i]['nom'])."', ";
							$sql.="elenom='".addslashes(strtoupper($eleves[$i]['nom']))."', ";

							//$sql.="elepre='".addslashes($eleves[$i]['prenom'])."', ";
							// On ne retient que le premier prénom:
							$tab_prenom = explode(" ",$eleves[$i]['prenom']);
							$sql.="elepre='".addslashes(maj_ini_prenom($tab_prenom[0]))."', ";

							//$sql.="elesexe='".sexeMF($eleves[$i]["code_sexe"])."', ";
							if(isset($eleves[$i]["code_sexe"])) {
								$sql.="elesexe='".sexeMF($eleves[$i]["code_sexe"])."', ";
							}
							else {
								echo "<span style='color:red'>Sexe non défini dans Sconet pour ".maj_ini_prenom($tab_prenom[0])." ".strtoupper($eleves[$i]['nom'])."</span><br />\n";
								$sql.="elesexe='M', ";
							}
							$sql.="eledatnais='".$eleves[$i]['date_naiss']."', ";
							$sql.="eledoubl='".ouinon($eleves[$i]["doublement"])."', ";
							if(isset($eleves[$i]["scolarite_an_dernier"]["code_rne"])){$sql.="etocod_ep='".$eleves[$i]["scolarite_an_dernier"]["code_rne"]."', ";}
							if(isset($eleves[$i]["code_regime"])){$sql.="elereg='".$eleves[$i]["code_regime"]."', ";}

							if(isset($eleves[$i]["code_commune_insee_naiss"])){$sql.="lieu_naissance='".$eleves[$i]["code_commune_insee_naiss"]."', ";}

							$sql=substr($sql,0,strlen($sql)-2);
							$sql.=" WHERE ele_id='".$eleves[$i]['eleve_id']."';";
							affiche_debug("$sql<br />\n");
							info_debug($sql);
							$res_insert=mysql_query($sql);
							if(!$res_insert){
								echo "Erreur lors de la requête $sql<br />\n";
								$nb_err++;
								flush();
							}
							else{
								$stat++;
							}
						}
					}
					/*
					else{
						echo $eleves[$i]['prenom']." ".$eleves[$i]['nom']." n'est pas dans \$tab_ele_id donc pas dans une classe...<br />";
						// On devrait supprimer l'élève de la table là, non?
					}
					*/
				}


				if($the_end=="n") {
					$suite="1";
				}
				else {
					$suite="2";
				}

				if($nb_err==0) {

					if($the_end=="n") {
						echo "<p>Parcours d'une tranche de la deuxième phase (<i><b>$cpt_saut_lignes_ini</b> -&gt; <b>$cpt_saut_lignes</b></i>)...</p>\n";

						echo "<script type='text/javascript'>
	setTimeout(\"test_stop_bis('$suite','$cpt_saut_lignes')\",1000);
</script>\n";
					}
					else {
						echo "<p style='bold'>La deuxième phase s'est passée sans erreur.</p>\n";

						echo "<script type='text/javascript'>
	setTimeout(\"test_stop('$suite')\",1000);
</script>\n";
					}
				}
				elseif($nb_err==1) {
					echo "<p>$nb_err erreur.</p>\n";
				}
				else{
					echo "<p>$nb_err erreurs</p>\n";
				}
			}

			echo "<p>$stat enregistrement(s) ont été mis à jour dans la table 'temp_gep_import2'.</p>\n";

			//echo "<p><a href='".$_SERVER['PHP_SELF']."?etape=1&amp;step=2'>Suite</a></p>\n";

			// ON SAUTE L'ETAPE 2 QUI CORRESPOND AUX OPTIONS DES ELEVES... NON PRISES EN CHARGE POUR LE MOMENT.
			//echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=2'>Suite</a></p>\n";
			//echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=3&amp;stop=$stop'>Suite</a></p>\n";
			//echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=3&amp;stop=$stop' onClick=\"test_stop_suite('3'); return false;\">Suite</a></p>\n";
			//echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=2&amp;stop=$stop' onClick=\"test_stop_suite('2'); return false;\">Suite</a></p>\n";

			if($the_end=="n") {
				echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=1&amp;cpt_saut_lignes=$cpt_saut_lignes&amp;stop=$stop' onClick=\"test_stop_suite_bis('$suite','$cpt_saut_lignes'); return false;\">Suite</a></p>\n";
			}
			else {
				echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=2&amp;stop=$stop' onClick=\"test_stop_suite('2'); return false;\">Suite</a></p>\n";
			}

			require("../lib/footer.inc.php");
			die();

			break;
		case "2":
			echo "<h2>Import/mise à jour des élèves</h2>\n";

			info_debug("==============================================");
			info_debug("=============== Phase step $step =================");

			// CETTE PHASE D'ANALYSE DES OPTIONS EST A REVOIR: Il faudrait le fichier Nomenclature pour pouvoir proposer les bonnes options lors de l'inscription de nouveaux élèves (ou stocker dans une table les correspondances de codes/matières).
			//
			// Par contre, on y fait quand même des tests pour les élèves partis... ne pas squizzer ça si on supprime l'étape

			//$dest_file="../temp/".$tempdir."/eleves.xml";
			$dest_file="../temp/".$tempdir."/section_options.xml";
			$fp=fopen($dest_file,"r");
			if(!$fp){
				echo "<p>Le XML élève n'a pas l'air présent dans le dossier temporaire.<br />Auriez-vous sauté une étape???</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
			else{


				// 20090722
				$cpt_saut_lignes=isset($_POST['cpt_saut_lignes']) ? $_POST['cpt_saut_lignes'] : (isset($_GET['cpt_saut_lignes']) ? $_GET['cpt_saut_lignes'] : 0);
				$the_end="";
				$saut_effectue="n";
				$cpt_saut_lignes_ini=$cpt_saut_lignes;


				// On récupère les ele_id des élèves qui sont affectés dans une classe
				$sql="SELECT ele_id FROM temp_gep_import2 ORDER BY id_tempo";
				info_debug($sql);
				$res_ele_id=mysql_query($sql);
				//echo "count(\$res_ele_id)=".count($res_ele_id)."<br />";

				unset($tab_ele_id);
				$tab_ele_id=array();
				$cpt=0;
				// Pourquoi est-ce que cela ne fonctionne pas en mysql_fetch_object()???
				// TROUVé: C'EST SENSIBLE à LA CASSE: IL FAUDRAIT $lig->ELE_ID
				//while($lig=mysql_fetch_object($res_ele_id)){
				while($lig=mysql_fetch_array($res_ele_id)){
					//$tab_ele_id[$cpt]="$lig->ele_id";
					$tab_ele_id[$cpt]=$lig[0];
					affiche_debug("\$tab_ele_id[$cpt]=$tab_ele_id[$cpt]<br />");
					$cpt++;
				}

				/*
				echo "<p>Lecture du fichier Elèves...<br />\n";
				//echo "<blockquote>\n";
				while(!feof($fp)){
					$ligne[]=fgets($fp,4096);
				}
				fclose($fp);
				//echo "<p>Terminé.</p>\n";
				*/
				flush();

				echo "<p>";
				echo "Analyse du fichier pour extraire les informations de la section OPTIONS...<br />\n";
				//echo "<blockquote>\n";

				// PARTIE <OPTIONS>
				$cpt=0;
				$eleves=array();
				$i=-1;
				$temoin_options=0;
				$temoin_opt="";
				$temoin_opt_ele="";
				$cpt=0;
				//while($cpt<count($ligne)){
				while(!feof($fp)){

					// Sauter les lignes
					if($saut_effectue=="n") {
						for($loop=0;$loop<$cpt_saut_lignes;$loop++) {$ligne=fgets($fp,4096);}
						$saut_effectue="y";
					}
					$ligne=fgets($fp,4096);

					$cpt_saut_lignes++;

					/*
					//if(strstr($ligne[$cpt],"<OPTIONS>")){
					if(strstr($ligne,"<OPTIONS>")){
						echo "Début de la section OPTIONS à la ligne <span style='color: blue;'>$cpt</span><br />\n";
						flush();
						$temoin_options++;
					}
					//if(strstr($ligne[$cpt],"</OPTIONS>")){
					if(strstr($ligne,"</OPTIONS>")){
						echo "Fin de la section OPTIONS à la ligne <span style='color: blue;'>$cpt</span><br />\n";
						flush();
						$temoin_options++;
						break;
					}
					if($temoin_options==1){
					*/
						//if(strstr($ligne[$cpt],"<OPTION ")){
						if(strstr($ligne,"<OPTION ")){
							$i++;
							$eleves[$i]=array();

							//echo "<p><b>".htmlentities($ligne[$cpt])."</b><br />\n";
							unset($tabtmp);
							//$tabtmp=explode('"',strstr($ligne[$cpt]," ELEVE_ID="));
							$tabtmp=explode('"',strstr($ligne," ELEVE_ID="));
							//$tmp_eleve_id=trim($tabtmp[1]);
							$eleves[$i]['eleve_id']=trim($tabtmp[1]);

							$eleves[$i]["options"]=array();
							$j=0;
							$temoin_opt=1;
						}
						//if(strstr($ligne[$cpt],"</OPTION>")){
						if(strstr($ligne,"</OPTION>")){
							$temoin_opt=0;
						}
						if($temoin_opt==1){
						//if(($temoin_opt==1)&&($temoin_ident=="oui")){
							//if(strstr($ligne[$cpt],"<OPTIONS_ELEVE>")){
							if(strstr($ligne,"<OPTIONS_ELEVE>")){
								$eleves[$i]["options"][$j]=array();
								$temoin_opt_ele=1;
							}
							//if(strstr($ligne[$cpt],"</OPTIONS_ELEVE>")){
							if(strstr($ligne,"</OPTIONS_ELEVE>")){
								$j++;
								$temoin_opt_ele=0;


								// 20090722
								if($i>200) {$the_end="n";break;}

							}

							$tab_champs_opt=array("NUM_OPTION","CODE_MODALITE_ELECT","CODE_MATIERE");
							if($temoin_opt_ele==1){
								for($loop=0;$loop<count($tab_champs_opt);$loop++){
									//if(strstr($ligne[$cpt],"<".$tab_champs_opt[$loop].">")){
									if(strstr($ligne,"<".$tab_champs_opt[$loop].">")){
										$tmpmin=strtolower($tab_champs_opt[$loop]);
										//$eleves[$i]["options"][$j]["$tmpmin"]=extr_valeur($ligne[$cpt]);

										// Suppression des guillemets éventuels
										//$eleves[$i]["options"][$j]["$tmpmin"]=extr_valeur($ligne);
										//$eleves[$i]["options"][$j]["$tmpmin"]=my_ereg_replace('"','',extr_valeur($ligne));
										$eleves[$i]["options"][$j]["$tmpmin"]=my_ereg_replace('/"/','',extr_valeur($ligne));

										//echo "\$eleves[$i][\"$tmpmin\"]=".$eleves[$i]["$tmpmin"]."<br />\n";
										break;
									}
								}
							}
						}
					//}
					$cpt++;
				}
				fclose($fp);


				// Insertion des codes numériques d'options
				$nb_err=0;
				$stat=0;
				for($i=0;$i<count($eleves);$i++){
					if(in_array($eleves[$i]['eleve_id'],$tab_ele_id)){
						for($j=0;$j<count($eleves[$i]["options"]);$j++){
							$k=$j+1;
							$sql="UPDATE temp_gep_import2 SET ";
							$sql.="eleopt$k='".$eleves[$i]["options"][$j]['code_matiere']."'";
							$sql.=" WHERE ele_id='".$eleves[$i]['eleve_id']."';";
							affiche_debug("$sql<br />\n");
							info_debug($sql);
							$res_update=mysql_query($sql);
							if(!$res_update){
								echo "Erreur lors de la requête $sql<br />\n";
								flush();
								$nb_err++;
							}
							else{
								$stat++;
							}
						}
					}
				}


				if($the_end=="n") {
					$suite="2";
				}
				else {
					//$sql="SELECT 1=1 FROM tempo2 WHERE col1='ele_id_eleve_parti';";
					$sql="SELECT 1=1 FROM tempo2 WHERE col1='ele_id_eleve_parti' LIMIT 1;";
					info_debug($sql);
					$test=mysql_query($sql);
					if(mysql_num_rows($test)==0) {$suite="3";} else {$suite="2b";}
				}


				if($nb_err==0) {
					if($the_end=="n") {
						echo "<p>Parcours d'une tranche de la troisième phase (<i><b>$cpt_saut_lignes_ini</b> -&gt; <b>$cpt_saut_lignes</b></i>)...</p>\n";
						echo "<script type='text/javascript'>
	setTimeout(\"test_stop_bis('$suite','$cpt_saut_lignes')\",1000);
</script>\n";
					}
					else {
						echo "<p style='bold'>La troisième phase s'est passée sans erreur.</p>\n";

						echo "<script type='text/javascript'>
	setTimeout(\"test_stop('$suite')\",3000);
</script>\n";
					}
				}
				elseif($nb_err==1) {
					echo "<p>$nb_err erreur.</p>\n";
				}
				else{
					echo "<p>$nb_err erreurs</p>\n";
				}

				echo "<p>$stat option(s) ont été mises à jour dans la table 'temp_gep_import2'.</p>\n";

				//echo "<p><a href='".$_SERVER['PHP_SELF']."?etape=1&amp;step=3'>Suite</a></p>\n";
				//echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=3&amp;stop=$stop'>Suite</a></p>\n";

				if($the_end=="n") {
					echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=$suite&amp;stop=$stop&amp;cpt_saut_lignes=$cpt_saut_lignes' onClick=\"test_stop_suite_bis('$suite','$cpt_saut_lignes'); return false;\">Suite</a></p>\n";
				}
				else {
					echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=$suite&amp;stop=$stop' onClick=\"test_stop_suite('$suite'); return false;\">Suite</a></p>\n";
				}

				require("../lib/footer.inc.php");
				die();
			}

			break;


		case "2b":
			echo "<h2>Import/mise à jour des élèves</h2>\n";

			info_debug("==============================================");
			info_debug("=============== Phase step $step =================");

			echo "<p class='bold'>Contrôle des départs d'élèves...</p>\n";

			echo "<form action='".$_SERVER['PHP_SELF']."' name='formulaire' method='post'>\n";
			//==============================
			// AJOUT pour tenir compte de l'automatisation ou non:
			echo "<input type='hidden' name='stop' id='id_form_stop' value='$stop' />\n";
			//==============================

			echo "<p>Les élèves notés dans Sconet comme ayant quitté l'établissement peuvent être désinscrits des classes et enseignements sur les périodes futures. On recherche ci-dessous les périodes sur lesquelles les élèves n'ont pas de note ni quoi que ce soit sur le bulletin.</p>\n";

			echo "<p>Cochez les périodes pour lesquelles vous souhaitez désinscrire le ou les élèves qui ont quitté l'établissement et validez en bas de page pour passer à la suite.</p>\n";

			echo "<p>";
			echo "<a href=\"javascript:modifcase('coche')\">";
			echo "Cocher tous les élèves qu'il est possible de désinscrire</a>";
			echo " / ";
			echo "<a href=\"javascript:modifcase('decoche')\">";
			echo "Tout décocher</a></p>\n";

			$sql="SELECT col2 FROM tempo2 WHERE col1='ele_id_eleve_parti';";
			info_debug($sql);
			$res=mysql_query($sql);
			$cpt=0;
			while($lig=mysql_fetch_object($res)) {
				$ele_id=$lig->col2;
				$sql="SELECT * FROM eleves WHERE ele_id='$ele_id';";
				info_debug($sql);
				$res_ele=mysql_query($sql);
				if(mysql_num_rows($res_ele)>0) {
					$lig_ele=mysql_fetch_object($res_ele);

					echo "<p>".strtoupper($lig_ele->nom)." ".ucfirst(strtolower($lig_ele->prenom))."</p>\n";
					echo "<blockquote>\n";
					// On cherche les périodes pour lesquelles l'élève n'a pas de notes ni d'appréciations ni dans le carnet de notes ni sur le bulletin.
					$sql="SELECT DISTINCT jec.id_classe, c.classe, jec.periode FROM j_eleves_classes jec, classes c WHERE jec.id_classe=c.id AND jec.login='$lig_ele->login' ORDER BY periode,classe;";
					info_debug($sql);
					$res_class=mysql_query($sql);
					if(mysql_num_rows($res_class)==0){
						echo "Il n'est inscrit dans aucune classe.";
					}
					else {
						$alt=1;
						echo "<table class='boireaus' summary='Elève n°$ele_id'>\n";
						echo "<tr class='lig$alt'>\n";
						echo "<th>Classe</th>\n";
						echo "<th>Période</th>\n";
						echo "<th>Carnet de notes</th>\n";
						echo "<th>Notes sur le bulletin</th>\n";
						echo "<th>Appréciations sur le bulletin</th>\n";
						echo "<th>Avis du conseil de classe</th>\n";
						echo "<th>\n";
						echo "Désinscrire\n";
						echo "</th>\n";
						echo "</tr>\n";

						while($lig_clas=mysql_fetch_object($res_class)){
							$temoin_periode="y";

							$alt=$alt*(-1);
							echo "<tr class='lig$alt'>\n";
							echo "<td>$lig_clas->classe</td>\n";
							echo "<td>$lig_clas->periode</td>\n";
							echo "<td>\n";
							$sql="SELECT 1=1 FROM cn_cahier_notes ccn, 
													cn_conteneurs cc, 
													cn_devoirs cd, 
													cn_notes_devoirs cnd WHERE
												ccn.periode='$lig_clas->periode' AND
												ccn.id_cahier_notes=cc.id_racine AND
												cc.id=cd.id_conteneur AND
												cd.id=cnd.id_devoir AND
												cnd.login='$lig_ele->login';";
							info_debug($sql);
							$test1=mysql_query($sql);
							$nb_notes=mysql_num_rows($test1);
							if($nb_notes==0) {
								echo "<span style='color:green;'>Vide</span>";
							}
							else {
								echo "<span style='color:red;'>$nb_notes notes</span>";
								$temoin_periode="n";
							}
							echo "</td>\n";
	
							echo "<td>\n";
							$sql="SELECT 1=1 FROM matieres_notes WHERE periode='$lig_clas->periode' AND login='$lig_ele->login';";
							info_debug($sql);
							$test2=mysql_query($sql);
							$nb_notes_bull=mysql_num_rows($test2);
							if($nb_notes_bull==0) {
								echo "<span style='color:green;'>Vide</span>";
							}
							else {
								echo "<span style='color:red;'>$nb_notes_bull notes</span>";
								$temoin_periode="n";
							}
							echo "</td>\n";
	
							echo "<td>\n";
							$sql="SELECT 1=1 FROM matieres_appreciations WHERE periode='$lig_clas->periode' AND login='$lig_ele->login';";
							info_debug($sql);
							$test3=mysql_query($sql);
							$nb_app_bull=mysql_num_rows($test3);
							if($nb_app_bull==0) {
								echo "<span style='color:green;'>Vide</span>";
							}
							else {
								echo "<span style='color:red;'>$nb_app_bull appréciations</span>";
								$temoin_periode="n";
							}
							echo "</td>\n";

							echo "<td>\n";
							$sql="SELECT 1=1 FROM avis_conseil_classe WHERE periode='$lig_clas->periode' AND login='$lig_ele->login';";
							info_debug($sql);
							$test4=mysql_query($sql);
							$nb_avis=mysql_num_rows($test4);
							if($nb_avis==0) {
								echo "<span style='color:green;'>Vide</span>";
							}
							else {
								echo "<span style='color:red;'>$nb_avis avis</span>";
								$temoin_periode="n";
							}
							echo "</td>\n";

							echo "<td>\n";
							if($temoin_periode=='y') {
								// On propose de désinscrire des classes et des groupes
								echo "<input type='checkbox' name='desinscription[]' id='desinscription_$cpt' value=\"$lig_ele->login|$lig_clas->periode\" />\n";
							}
							else {
								echo "&nbsp;";
							}
							echo "</td>\n";

							echo "</tr>\n";

							$cpt++;

						}
						echo "</table>\n";

					}
					echo "</blockquote>\n";
	
				}
			}

			echo "<input type='hidden' name='step' value='2c' />\n";
			echo "<p><input type='submit' value='Valider' /></p>\n";

			echo "</form>\n";

			echo "<script type='text/javascript'>
	function modifcase(mode){
		for(i=0;i<$cpt;i++){
			if(document.getElementById('desinscription_'+i)){
				if(mode=='coche'){
					document.getElementById('desinscription_'+i).checked=true;
				}
				else{
					document.getElementById('desinscription_'+i).checked=false;
				}
			}
		}
	}
</script>\n";

			echo "<p><i>NOTES&nbsp;:</i></p>\n";
			echo "<blockquote>\n";
			echo "<p>Les élèves notés dans Sconet comme ayant quitté l'établissement peuvent être désinscrits des classes et enseignements sur les périodes futures.<br />On recherche ci-dessus les périodes sur lesquelles les élèves n'ont pas de note ni quoi que ce soit sur le bulletin.</p>\n";
			echo "</blockquote>\n";

			//echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=3&amp;stop=$stop' onClick=\"test_stop_suite('3'); return false;\">Suite</a></p>\n";

			break;

		case "2c":

			echo "<h2>Import/mise à jour des élèves</h2>\n";

			info_debug("==============================================");
			info_debug("=============== Phase step $step =================");

			// On vide la table dont on va se resservir plus tard:
			$sql="TRUNCATE TABLE tempo2;";
			info_debug($sql);
			$res0=mysql_query($sql);


			if(!isset($_POST['desinscription'])) {
				echo "<p>Aucune désinscription n'a été validée.</p>\n";
			}
			else {
				$desinscription=$_POST['desinscription'];
				echo "<p>";
				for($i=0;$i<count($desinscription);$i++) {
					$tab=explode("|",$desinscription[$i]);
					$ele_login=$tab[0];
					$periode=$tab[1];

					$sql="SELECT * FROM eleves WHERE login='$ele_login';";
					info_debug($sql);
					$res_ele=mysql_query($sql);
					$lig_ele=mysql_fetch_object($res_ele);

					echo "Désinscription des classes et des enseignements de ".strtoupper($lig_ele->nom)." ".ucfirst(strtolower($lig_ele->prenom))." pour la période $periode: ";

					$sql="DELETE FROM j_eleves_groupes WHERE login='$ele_login' AND periode='$periode';";
					info_debug($sql);
					if(!mysql_query($sql)) {
						echo "<span style='color:red;'>ERREUR lors de la désinscription des enseignements</span>";
					}
					else {
						$sql="DELETE FROM j_eleves_classes WHERE login='$ele_login' AND periode='$periode';";
						info_debug($sql);
						if(!mysql_query($sql)) {
							echo "<span style='color:red;'>ERREUR lors de la désinscription de la classe</span>";
						}
						else {
							echo "<span style='color:green;'>OK</span>";
						}
					}
					echo "<br />\n";
				}
				echo "</p>\n";
			}

			echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=3&amp;stop=$stop' onClick=\"test_stop_suite('3'); return false;\">Suite</a></p>\n";

			break;

		case "3":
			echo "<h2>Import/mise à jour des élèves</h2>\n";

			info_debug("==============================================");
			info_debug("=============== Phase step $step =================");

			if(file_exists("../temp/".$tempdir."/eleves.xml")) {
				echo "<p>Suppression de eleves.xml... ";
				if(unlink("../temp/".$tempdir."/eleves.xml")){
					echo "réussie.<br />\n";
				}
				else{
					echo "<font color='red'>Echec!</font> Vérifiez les droits d'écriture sur le serveur.<br />\n";
				}

				echo "Suppression de section_eleves.xml... ";
				if(unlink("../temp/".$tempdir."/section_eleves.xml")){
					echo "réussie.<br />\n";
				}
				else{
					echo "<font color='red'>Echec!</font> Vérifiez les droits d'écriture sur le serveur.<br />\n";
				}

				echo "Suppression de section_options.xml... ";
				if(unlink("../temp/".$tempdir."/section_options.xml")){
					echo "réussie.<br />\n";
				}
				else{
					echo "<font color='red'>Echec!</font> Vérifiez les droits d'écriture sur le serveur.<br />\n";
				}

				echo "Suppression de section_structures.xml... ";
				if(unlink("../temp/".$tempdir."/section_structures.xml")){
					echo "réussie.</p>\n";
				}
				else{
					echo "<font color='red'>Echec!</font> Vérifiez les droits d'écriture sur le serveur.</p>\n";
				}
			}

			if(!isset($parcours_diff)){
				$sql="TRUNCATE TABLE tempo2;";
				info_debug($sql);
				$res0=mysql_query($sql);

				$sql="SELECT ele_id,naissance FROM eleves";
				info_debug($sql);
				$res1=mysql_query($sql);
				//$nb_eleves=mysql_num_rows($res1);
				//if($nb_eleves==0){
				if(mysql_num_rows($res1)==0){
					echo "<p>La table 'eleves' est vide???<br />Avez-vous procédé à l'initialisation de l'année?</p>\n";

					// ON POURRAIT PEUT-ÊTRE PERMETTRE DE POURSUIVRE... en effectuant les étapes init_xml2/step2.php et init_xml2/step3.php

					require("../lib/footer.inc.php");
					die();
				}

				// Il faut prendre la table temp_gep_import2 comme référence pour les différences pour ne pas passer à côté des nouveaux élèves.
				$sql="SELECT ELE_ID,ELEDATNAIS FROM temp_gep_import2";
				info_debug($sql);
				$res2=mysql_query($sql);
				$nb_eleves=mysql_num_rows($res2);
				if($nb_eleves==0){
					echo "<p>La table 'temp_gep_import2' est vide???</p>\n";
					require("../lib/footer.inc.php");
					die();
				}

				echo "<p>Les ".$nb_eleves." élèves vont être parcourus par tranches de 20 à la recherche de différences.</p>\n";

				echo "<p>Parcours de la tranche <b>1</b>.</p>\n";
			}
			else{
				echo "<p>Parcours de la tranche <b>$parcours_diff/$nb_parcours</b>.</p>\n";
			}

			flush();

			echo "<form action='".$_SERVER['PHP_SELF']."' name='formulaire' method='post'>\n";
			//==============================
			// AJOUT pour tenir compte de l'automatisation ou non:
			echo "<input type='hidden' name='stop' id='id_form_stop' value='$stop' />\n";
			//==============================

			/*
			$cpt=0;
			$tabrq=array();
			$cpt2=-1;
			while($lig=mysql_fetch_object($res1)){
				$tab_naissance=explode("-",$lig->naissance);
				$naissance=$tab_naissance[0].$tab_naissance[1].$tab_naissance[2];
				$sql="INSERT INTO tempo2 SET col1='$lig->ele_id', col2='$naissance';";
				$insert=mysql_query($sql);
				if($cpt%20==0){
					$cpt2++;
					$tabrq[$cpt2]="e.ele_id='$lig->ele_id'";
				}
				else{
					$tabrq[$cpt2].=" OR e.ele_id='$lig->ele_id'";
				}
				$cpt++;
			}

			for($i=0;$i<count($tabrq);$i++){
				//echo "<p>\$tabrq[$i]=$tabrq[$i]</p>\n";

				$sql="SELECT e.ele_id FROM eleves e, temp_gep_import2 t, tempo2 t2
								WHERE e.ele_id=t.ELE_ID AND
										e.ele_id=t2.col1 AND
										(
											e.nom!=t.ELENOM OR
											e.prenom!=t.ELEPRE OR
											e.sexe!=t.ELESEXE OR
											t2.col2!=t.ELEDATNAIS OR
											e.no_gep!=t.ELENONAT
										)
										AND ($tabrq[$i])
										";
				//echo "$sql<br />";
				$test=mysql_query($sql);

				echo "$i: mysql_num_rows(\$test)=".mysql_num_rows($test)."<br />";
				flush();
			}
			*/

			if(!isset($parcours_diff)){
				// La date de naissance n'est pas au même format dans les tables eleves et temp_gep_import2
				// Une mise au même format est opérée dans une table intermédiaire.
				$tab_ele_id=array();

				$cpt=0;
				$chaine_nouveaux="";
				//while($lig=mysql_fetch_object($res1)){
				while($lig=mysql_fetch_object($res2)){
					//$tab_naissance=explode("-",$lig->naissance);
					//$naissance=$tab_naissance[0].$tab_naissance[1].$tab_naissance[2];
					$naissance=substr($lig->ELEDATNAIS,0,4)."-".substr($lig->ELEDATNAIS,4,2)."-".substr($lig->ELEDATNAIS,6,2);
					//$sql="INSERT INTO tempo2 SET col1='$lig->ele_id', col2='$naissance';";
					$sql="INSERT INTO tempo2 SET col1='$lig->ELE_ID', col2='$naissance';";
					info_debug($sql);
					$insert=mysql_query($sql);

					// Est-ce un nouvel élève?
					$sql="SELECT 1=1 FROM eleves e, temp_gep_import2 t WHERE e.ele_id=t.ELE_ID AND t.ELE_ID='$lig->ELE_ID'";
					//echo "$sql<br />\n";
					info_debug($sql);
					$test=mysql_query($sql);
					if(mysql_num_rows($test)==0){
						if($cpt>0){$chaine_nouveaux.=", ";}
						$chaine_nouveaux.=$lig->ELE_ID;
						echo "<input type='hidden' name='tab_ele_id_diff[]' value='$lig->ELE_ID' />\n";
						$cpt++;
					}
					else{
						//$tab_ele_id[]=$lig->ele_id;
						$tab_ele_id[]=$lig->ELE_ID;
					}
				}

				//if($chaine_nouveaux==1){
				if($cpt==1){
					echo "<p>L'ELE_ID d'un nouvel élève a été trouvé: $chaine_nouveaux</p>\n";
				}
				//elseif($chaine_nouveaux>1){
				elseif($cpt>1){
					echo "<p>Les ELE_ID de $cpt nouveaux élèves ont été trouvés: $chaine_nouveaux</p>\n";
				}

				$nb_parcours=ceil(count($tab_ele_id)/20);
			}
			else{
				if(isset($tab_ele_id_diff)){
					if(count($tab_ele_id_diff)==1){
						echo "<p>L'ELE_ID, pour lequel une ou des différences ont déjà été repérées, est: \n";
					}
					else{
						echo "<p>Le(s) ELE_ID, pour lesquels une ou des différences ont déjà été repérées, sont: \n";
					}
					$chaine_ele_id_diff="";
					for($i=0;$i<count($tab_ele_id_diff);$i++){
						if($i>0){$chaine_ele_id_diff.=", ";}
						$chaine_ele_id_diff.=$tab_ele_id_diff[$i];
						//echo "$i: ";
						echo "<input type='hidden' name='tab_ele_id_diff[]' value='$tab_ele_id_diff[$i]' />\n";
						//echo "<br />\n";
					}
					echo $chaine_ele_id_diff;
					echo "</p>\n";
				}
			}

			echo "<input type='hidden' name='nb_parcours' value='$nb_parcours' />\n";


			// On construit la chaine des 20 ELE_ID retenus pour la requête à venir:
			$chaine="";
			//for($i=0;$i<count($tab_ele_id);$i++){
			for($i=0;$i<min(20,count($tab_ele_id));$i++){
				if($i>0){$chaine.=" OR ";}
				$chaine.="e.ele_id='$tab_ele_id[$i]'";

				// On teste s'il s'agit d'un nouvel élève:
				//$sql="SELECT 1=1 FROM";
			}

			//echo "\$chaine=$chaine<br />\n";

			// Liste des ELE_ID restant à parcourir:
			for($i=20;$i<count($tab_ele_id);$i++){
				//echo "$i: ";
				echo "<input type='hidden' name='tab_ele_id[]' value='$tab_ele_id[$i]' />\n";
				//echo "<br />\n";
			}


			/*
			$sql="SELECT e.ele_id FROM eleves e, temp_gep_import2 t, tempo2 t2
							WHERE e.ele_id=t.ELE_ID AND
									e.ele_id=t2.col1 AND
									(
										e.nom!=t.ELENOM OR
										e.prenom!=t.ELEPRE OR
										e.sexe!=t.ELESEXE OR
										t2.col2!=t.ELEDATNAIS OR
										e.no_gep!=t.ELENONAT
									)
									AND ($chaine)
									";
			*/




			/*
			$sql="SELECT e.ele_id FROM eleves e, temp_gep_import2 t, tempo2 t2
							WHERE e.ele_id=t.ELE_ID AND
									e.ele_id=t2.col1 AND
									(
										e.nom!=t.ELENOM OR
										e.prenom!=t.ELEPRE OR
										e.sexe!=t.ELESEXE OR
										e.naissance!=t2.col2 OR
										e.no_gep!=t.ELENONAT
									)
									AND ($chaine)
									";

			echo "$sql<br />\n";
			echo strftime("%H:%M:%S")."<br />\n";
			flush();

			$test=mysql_query($sql);
			echo strftime("%H:%M:%S")."<br />\n";
			flush();

			$cpt=0;
			if(mysql_num_rows($test)>0){
				echo "<p>Une ou des différences ont été trouvées dans la tranche étudiée à cette phase.";
				echo "<br />\n";
				echo "En voici le(s) ELE_ID: ";
				//$cpt=0;
				$chaine_ele_id="";
				while($lig=mysql_fetch_object($test)){
					if($cpt>0){$chaine_ele_id.=", ";}
					$chaine_ele_id.=$lig->ele_id;
					echo "<input type='hidden' name='tab_ele_id_diff[]' value='$lig->ele_id' />\n";
					//echo "<br />\n";
					// Pour le cas où on est dans la dernière tranche:
					$tab_ele_id_diff[]=$lig->ele_id;
					$cpt++;
				}
				echo $chaine_ele_id;
			}
			*/

			$cpt=0;
			for($i=0;$i<min(20,count($tab_ele_id));$i++){

				// AJOUTER SELON LA VALEUR DE ele_lieu_naissance
				// LE TEST DE DIFFERENCE SUR lieu_naissance
				if($ele_lieu_naissance=="y") {
					$sql="SELECT e.ele_id FROM eleves e, temp_gep_import2 t, tempo2 t2
							WHERE e.ele_id=t.ELE_ID AND
									e.ele_id=t2.col1 AND
									(
										e.nom!=t.ELENOM OR
										e.prenom!=t.ELEPRE OR
										e.sexe!=t.ELESEXE OR
										e.naissance!=t2.col2 OR
										e.lieu_naissance!=t.LIEU_NAISSANCE OR
										e.no_gep!=t.ELENONAT
									)
									AND e.ele_id='$tab_ele_id[$i]';";
				}
				else {
					$sql="SELECT e.ele_id FROM eleves e, temp_gep_import2 t, tempo2 t2
							WHERE e.ele_id=t.ELE_ID AND
									e.ele_id=t2.col1 AND
									(
										e.nom!=t.ELENOM OR
										e.prenom!=t.ELEPRE OR
										e.sexe!=t.ELESEXE OR
										e.naissance!=t2.col2 OR
										e.no_gep!=t.ELENONAT
									)
									AND e.ele_id='$tab_ele_id[$i]';";
				}
				//echo "$sql<br />";
				//if($tab_ele_id[$i]=='305034') {echo "$sql<br />";}
				//$reserve_sql=$sql;
				info_debug($sql);
				$test=mysql_query($sql);

				$temoin_chgt_ancien_etab="n";
				if ($gepiSchoolRne!="") {
					// Ancien établissement précédemment enregistré
					$sql="SELECT id_etablissement FROM j_eleves_etablissements jee, eleves e WHERE jee.id_eleve=e.elenoet AND e.elenoet!='' AND e.ele_id='".$tab_ele_id[$i]."';";
					info_debug($sql);
					//echo "$sql<br />";
					$test_ee=mysql_query($sql);
					if(mysql_num_rows($test_ee)>0) {
						$lig_ee=mysql_fetch_object($test_ee);
						$rne_ancien_etab=$lig_ee->id_etablissement;
					}
					else {
						$rne_ancien_etab="";
					}

					// Test de modification de l'ancien établissement
					$sql="SELECT ETOCOD_EP FROM temp_gep_import2 t WHERE t.ELE_ID='".$tab_ele_id[$i]."' AND t.ETOCOD_EP!='';";
					info_debug($sql);
					//echo "$sql<br />";
					$test_nouvel_ancien_etb=mysql_query($sql);
					if(mysql_num_rows($test_nouvel_ancien_etb)>0) {
						$lig_nee=mysql_fetch_object($test_nouvel_ancien_etb);
						$rne_ancien_etab2=$lig_nee->ETOCOD_EP;
					}
					else {
						$rne_ancien_etab2="";
					}

					if((strtolower($rne_ancien_etab)!=strtolower($rne_ancien_etab2))&&(strtolower($rne_ancien_etab2)!=strtolower($gepiSchoolRne))) {
						$temoin_chgt_ancien_etab="y";
						//echo "\$temoin_chgt_ancien_etab=$temoin_chgt_ancien_etab<br />";
					}
				}

				//if(mysql_num_rows($test)>0) {
				if((mysql_num_rows($test)>0)||($temoin_chgt_ancien_etab=="y")) {
					if($cpt==0){
						echo "<p>Une ou des différences ont été trouvées dans la tranche étudiée à cette phase.";
						echo "<br />\n";
						echo "En voici le(s) ELE_ID: ";
					}
					else{
						echo ", ";
					}
					// $lig->ele_id n'est pas affecté dans le cas où on n'a repéré qu'un changement dans l'établissement précédent.
					//if(mysql_num_rows($test)>0) {$lig=mysql_fetch_object($test);}
					//echo "<input type='hidden' id='c' name='tab_ele_id_diff[]' value='$lig->ele_id' />\n";
					//echo $lig->ele_id;
					echo "<input type='hidden' name='tab_ele_id_diff[]' value='".$tab_ele_id[$i]."' />\n";
					echo $tab_ele_id[$i];

					//echo "$reserve_sql<br/>";
					//echo "\$temoin_chgt_ancien_etab=$temoin_chgt_ancien_etab<br />";
					flush();
					$cpt++;
				}
				else{
					// Inutile de tester les différences sur le régime si des différences ont déjà été repérées et que l'ELE_ID est déjà en tab_ele_id_diff[]

					$temoin_test_regime='n';

					if(!isset($tab_ele_id_diff)){
						$temoin_test_regime='y';
					}
					elseif(!in_array($tab_ele_id[$i],$tab_ele_id_diff)){
						$temoin_test_regime='y';
					}

					if($temoin_test_regime=='y'){
						$sql="SELECT jer.regime, t.elereg FROM j_eleves_regime jer, eleves e, temp_gep_import2 t
								WHERE e.ele_id='$tab_ele_id[$i]' AND
										jer.login=e.login AND
										t.ele_id=e.ele_id";
						//=============
						//DEBUG
						if($tab_ele_id[$i]=='782611') {echo "$sql<br />";}
						//=============
						//echo "$sql<br />";
						info_debug($sql);
						$test=mysql_query($sql);
						if(mysql_num_rows($test)>0){
							$lig=mysql_fetch_object($test);
							$tmp_reg=traite_regime_sconet($lig->elereg);
							if("$tmp_reg"!="$lig->regime"){
								// BIZARRE CE $cpt... on n'écrit rien après la virgule...
								if($cpt==0){
									echo "<p>Une ou des différences ont été trouvées dans la tranche étudiée à cette phase.";
									echo "<br />\n";
									echo "En voici le(s) ELE_ID: ";
								}
								else{
									echo ", ";
								}

								echo $tab_ele_id[$i];
								echo "<input type='hidden' name='tab_ele_id_diff[]' value='".$tab_ele_id[$i]."' />\n";
								//echo "<br />\n";
								// Pour le cas où on est dans la dernière tranche:
								$tab_ele_id_diff[]=$tab_ele_id[$i];
								$cpt++;
							}
						}




						$temoin_test_doublant='n';

						if(!isset($tab_ele_id_diff)){
							$temoin_test_doublant='y';
						}
						elseif(!in_array($tab_ele_id[$i],$tab_ele_id_diff)){
							$temoin_test_doublant='y';
						}

						if($temoin_test_doublant=='y'){
							$sql="SELECT 1=1 FROM j_eleves_regime jer, eleves e, temp_gep_import2 t
									WHERE e.ele_id='$tab_ele_id[$i]' AND
											jer.login=e.login AND
											t.ele_id=e.ele_id AND
											((jer.doublant='-' AND t.ELEDOUBL='O') OR (jer.doublant!='-' AND t.ELEDOUBL='N'));";
							info_debug($sql);
							//echo "$sql<br />";
							$test=mysql_query($sql);
							if(mysql_num_rows($test)>0){
								if($cpt==0){
									echo "<p>Une ou des différences ont été trouvées dans la tranche étudiée à cette phase.";
									echo "<br />\n";
									echo "En voici le(s) ELE_ID: ";
								}
								else{
									echo ", ";
								}

								echo $tab_ele_id[$i];
								echo "<input type='hidden' name='tab_ele_id_diff[]' value='".$tab_ele_id[$i]."' />\n";
								//echo "<br />\n";
								// Pour le cas où on est dans la dernière tranche:
								$tab_ele_id_diff[]=$tab_ele_id[$i];
								$cpt++;
							}
						}

					}
				}
			}

			/*
			for($i=0;$i<min(20,count($tab_ele_id));$i++){

				$temoin_test_regime='n';

				if(!isset($tab_ele_id_diff)){
					$temoin_test_regime='y';
				}
				elseif(!in_array($tab_ele_id[$i],$tab_ele_id_diff)){
					$temoin_test_regime='y';
				}

				if($temoin_test_regime=='y'){
					$sql="SELECT jer.regime, t.elereg FROM j_eleves_regime jer, eleves e, temp_gep_import2 t
							WHERE e.ele_id='$tab_ele_id[$i]' AND
									jer.login=e.login AND
									t.ele_id=e.ele_id";
					$test=mysql_query($sql);
					if(mysql_num_rows($test)>0){
						$lig=mysql_fetch_object($test);
						$tmp_reg=traite_regime_sconet($lig->elereg);
						if("$tmp_reg"!="$lig->regime"){
							// BIZARRE CE $cpt... on n'écrit rien après la virgule...
							if($cpt==0){
								echo "<p>Une ou des différences ont été trouvées dans la tranche étudiée à cette phase.";
								echo "<br />\n";
								echo "En voici le(s) ELE_ID: ";
							}
							else{
								echo ", ";
							}
							echo $tab_ele_id[$i];
							echo "<input type='hidden' name='tab_ele_id_diff[]' value='".$tab_ele_id[$i]."' />\n";
							//echo "<br />\n";
							// Pour le cas où on est dans la dernière tranche:
							$tab_ele_id_diff[]=$tab_ele_id[$i];
							$cpt++;
						}
					}
				}
			}
			*/



			if(!isset($parcours_diff)){$parcours_diff=1;}
			$parcours_diff++;
			//echo "<input type='hidden' name='parcours_diff' value='$parcours_diff' />\n";

			if(count($tab_ele_id)>20){
				echo "<input type='hidden' name='parcours_diff' value='$parcours_diff' />\n";

				echo "<input type='hidden' name='step' value='3' />\n";
				echo "<p><input type='submit' value='Suite' /></p>\n";

				echo "<script type='text/javascript'>
	/*
	stop='n';
	if(document.getElementById('stop')){
		if(document.getElementById('stop').checked==true){
			stop='y';
		}
	}
	//stop='y'
	if(stop=='n'){
		setTimeout(\"document.forms['formulaire'].submit();\",1000);
	}
	*/
	//test_stop2();
	setTimeout(\"test_stop2()\",3000);
</script>\n";
			}
			else{
				echo "<p>Le parcours des différences est terminé.</p>\n";

				echo "<input type='hidden' name='step' value='4' />\n";
				echo "<p>Parcourir les différences par tranches de <input type='text' name='eff_tranche' value='10' size='3' /><br />\n";
				echo "<input type='submit' value='Afficher les différences' /></p>\n";

				// On vide la table dont on va se resservir:
				$sql="TRUNCATE TABLE tempo2;";
				info_debug($sql);
				$res0=mysql_query($sql);
			}
			//echo "<input type='hidden' name='is_posted' value='yes' />\n";
			echo "</form>\n";

			//echo "$i: mysql_num_rows(\$test)=".mysql_num_rows($test)."<br />";


			break;
		case "4":
			echo "<h2>Import/mise à jour des élèves</h2>\n";

			$eff_tranche=isset($_POST['eff_tranche']) ? $_POST['eff_tranche'] : 10;
			if(my_ereg("[^0-9]",$eff_tranche)) {$eff_tranche=10;}

			info_debug("==============================================");
			info_debug("=============== Phase step $step =================");

			if(!isset($tab_ele_id_diff)){
				echo "<p>Aucune différence n'a été trouvée.</p>\n";

				echo "<p>Voulez-vous <a href='".$_SERVER['PHP_SELF']."?is_posted=y&amp;step=9'>passer à la page d'importation/mise à jour des responsables</a></p>\n";
			}
			else{
				echo "<p>".count($tab_ele_id_diff)." élève(s) restant à parcourir (<i>nouveau(x) ou modifié(s)</i>).</p>\n";
				/*
				echo "<p>Liste des différences repérées: <br />\n";
				for($i=0;$i<count($tab_ele_id_diff);$i++){
					echo "\$tab_ele_id_diff[$i]=$tab_ele_id_diff[$i]";
					//echo "<input type='text' name='tab_ele_id_diff[]' value='$tab_ele_id_diff[$i]' />\n";
					echo "<br />\n";
				}
				*/


				// ?????????????????????????????????????????????????????????????????????
				// ?????????????????????????????????????????????????????????????????????
				// ?????????????????????????????????????????????????????????????????????
				// A CE NIVEAU IL FAUDRAIT POUVOIR GERER LE CAS D'UN TROP GRAND NOMBRE DE CORRECTIONS A EFFECTUER...
				// ... LES AFFICHER PAR TRANCHES...
				// APRES VALIDATION, STOCKER DANS UNE TABLE LES ELE_ID POUR LESQUELS temp_gep_import2 DOIT ECRASER eleves ET CEUX CORRESPONDANT A DE NOUVEAUX ELEVES
				// ?????????????????????????????????????????????????????????????????????
				// ?????????????????????????????????????????????????????????????????????
				// ?????????????????????????????????????????????????????????????????????
				//$eff_tranche=min(3,count($tab_ele_id_diff));

				//$eff_tranche=10;


				// Les cases validées à l'étape 4 précédente:
				$modif=isset($_POST['modif']) ? $_POST['modif'] : NULL;
				$new=isset($_POST['new']) ? $_POST['new'] : NULL;

				if(isset($modif)){
					for($i=0;$i<count($modif);$i++){
						$sql="INSERT INTO tempo2 SET col1='modif', col2='$modif[$i]'";
						info_debug($sql);
						$insert=mysql_query($sql);
					}
				}

				if(isset($new)){
					for($i=0;$i<count($new);$i++){
						$sql="INSERT INTO tempo2 SET col1='new', col2='$new[$i]'";
						info_debug($sql);
						$insert=mysql_query($sql);

						// A CE STADE OU AU SUIVANT, IL FAUDRAIT AUSSI PROPOSER D'AFFECTER LES ELEVES DANS LES CLASSES INDIQUEES... AVEC CHOIX DES PERIODES.
						// ET UNE CASE A COCHER POUR:
						// - METTRE DANS TOUS LES GROUPES OU NON
						// OU ALORS PROPOSER LE TABLEAU eleves_options.php
					}
				}

				// Dédoublonnage
				//for($loop=0;$loop<count($tab_ele_id_diff);$loop++) {echo "\$tab_ele_id_diff[$loop]=$tab_ele_id_diff[$loop]<br />";}
				$tab_ele_id_diff=array_unique($tab_ele_id_diff);
				//echo "<p>Après array_unique():<br />";
				//for($loop=0;$loop<count($tab_ele_id_diff);$loop++) {echo "\$tab_ele_id_diff[$loop]=$tab_ele_id_diff[$loop]<br />";}

				/*
				if(!isset($parcours_diff)){
					$nblignes=count($tab_ele_id_diff);
				}
				*/
				$nblignes=min($eff_tranche,count($tab_ele_id_diff));
				//echo "\$nblignes=$nblignes<br />";


				echo "<form action='".$_SERVER['PHP_SELF']."' name='formulaire' method='post'>\n";
				//==============================
				// AJOUT pour tenir compte de l'automatisation ou non:
				echo "<input type='hidden' name='stop' id='id_form_stop' value='$stop' />\n";
				//==============================
				echo "<input type='hidden' name='eff_tranche' value='$eff_tranche' />\n";

				for($i=$eff_tranche;$i<count($tab_ele_id_diff);$i++){
					//echo "$i: ";
					// BIZARRE: Il semble que certains indices puissent ne pas être affectés???
					// Peut-être à cause du array_unique() -> certains élèves qui ont des modifs de nom, date, INE,... et de régime peuvent être comptés deux fois...
					if(isset($tab_ele_id_diff[$i])){
						echo "<input type='hidden' name='tab_ele_id_diff[]' value='$tab_ele_id_diff[$i]' />\n";
					}
					//echo "<br />\n";
				}



				echo "<p align='center'><input type=submit value='Valider' /></p>\n";
				//echo "<p align='center'><input type=submit value='Enregistrer les modifications' /></p>\n";

				//echo "<table border='1'>\n";
				//echo "<table class='majimport'>\n";
				echo "<table class='boireaus'>\n";
				//echo "<tr style='background-color: rgb(150, 200, 240);'>\n";
				echo "<tr>\n";
				//echo "<td style='text-align: center; font-weight: bold;'>Enregistrer<br />\n";
				echo "<th>Modifier<br />\n";

				echo "<a href=\"javascript:modifcase('coche')\">";
				echo "<img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>";
				echo " / ";
				echo "<a href=\"javascript:modifcase('decoche')\">";
				echo "<img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";

				echo "</th>\n";

				echo "<th>Statut</th>\n";
				echo "<th>elenoet</th>\n";
				echo "<th>ele_id</th>\n";
				echo "<th>Nom</th>\n";
				echo "<th>Prénom</th>\n";
				echo "<th>Sexe</th>\n";
				echo "<th>Naissance</th>\n";
				echo "<th>Doublement</th>\n";
				echo "<th>N°NAT</th>\n";
				echo "<th>Régime</th>\n";
				echo "<th>Classe</th>\n";
				echo "<th>Etablissement d'origine</th>\n";
				echo "</tr>\n";
				$cpt=0;
				$cpt_modif=0;
				$cpt_new=0;
				$alt=1;
				for($k = 1; ($k < $nblignes+1); $k++){
					$temoin_modif="";
					$temoin_nouveau="";
					//if(!feof($fp)){
						//$ligne = fgets($fp, 4096);

					$w=$k-1;
					$sql="SELECT DISTINCT * FROM temp_gep_import2 WHERE ELE_ID='$tab_ele_id_diff[$w]';";
					info_debug($sql);
					//echo "<tr><td colspan='13'>$sql</td></tr>\n";
					$res1=mysql_query($sql);
					if(mysql_num_rows($res1)==0){
						echo "<tr><td colspan='13' style='text-align:left;'>ele_id=\$tab_ele_id_diff[$w]='$tab_ele_id_diff[$w]' non trouvé dans 'temp_gep_import2' ???</td></tr>\n";
					}
					else{
						$lig=mysql_fetch_object($res1);
						$affiche=array();

						$affiche[0]=traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($lig->ELENOM))));
						// IL FAUDRAIT FAIRE ICI LE MEME TRAITEMENT QUE DANS /init_xml/step3.php POUR LES PRENOMS COMPOSéS ET SAISIE DE PLUSIEURS PRéNOMS...
						$affiche[1]=traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($lig->ELEPRE))));
						$affiche[2]=traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($lig->ELESEXE))));
						$affiche[3]=traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($lig->ELEDATNAIS))));
						$affiche[4]=traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($lig->ELENOET))));
						$affiche[5]=traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($lig->ELE_ID))));
						$affiche[6]=traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($lig->ELEDOUBL))));
						$affiche[7]=traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($lig->ELENONAT))));
						$affiche[8]=traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($lig->ELEREG))));
						$affiche[9]=traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($lig->DIVCOD))));

						$affiche[10]=traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($lig->ETOCOD_EP))));

						if($ele_lieu_naissance=="y") {
							$affiche[11]=traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($lig->LIEU_NAISSANCE))));
						}

						//if(trim($ligne)!=""){
							//$tabligne=explode(";",$ligne);
							//$affiche=array();
							//for($i = 0; $i < count($tabchamps); $i++) {
							//	$affiche[$i] = traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($tabligne[$tabindice[$i]]))));
							//}

							//$sql="SELECT * FROM eleves WHERE elenoet='$affiche[4]'";
							$sql="SELECT * FROM eleves WHERE (elenoet='$affiche[4]' OR elenoet='".sprintf("%05d",$affiche[4])."')";
							info_debug($sql);
							//echo "<tr><td colspan='13'>$sql</td></tr>\n";
							$res1=mysql_query($sql);
							if(mysql_num_rows($res1)>0){
								//$sql="UPDATE eleves SET ele_id='$affiche[5]' WHERE elenoet='$affiche[4]'";

								// FAUT-IL FAIRE LES UPDATE SANS CONTRÔLE OU SIGNALER LES MODIFS SEULEMENT...
								//$sql="UPDATE eleves SET ele_id='$affiche[5]' WHERE elenoet='$affiche[4]'";

								// STOCKER DANS UN TABLEAU ET AFFICHER SEULEMENT LES MODIFS DANS UN PREMIER TEMPS
								// CASES A COCHER POUR VALIDER


								//$res_update=mysql_query($sql);
								//if(!$res_update){
								//	$erreur++;
								//}

								//$eleves[$cpt]

								$lig_ele=mysql_fetch_object($res1);
								//$tabtmp=explode("/",$affiche[3]);
								// $lig_ele->naissance!=$tabtmp[2]."-".$tabtmp[1]."-".$tabtmp[0])||


								$new_date=substr($affiche[3],0,4)."-".substr($affiche[3],4,2)."-".substr($affiche[3],6,2);

								// Des stripslashes() pour les apostrophes dans les noms
								if($ele_lieu_naissance=="y") {
									if((stripslashes($lig_ele->nom)!=stripslashes($affiche[0]))||
									(stripslashes($lig_ele->prenom)!=stripslashes($affiche[1]))||
									($lig_ele->sexe!=$affiche[2])||
									($lig_ele->naissance!=$new_date)||
									($lig_ele->lieu_naissance!=$affiche[11])||
									($lig_ele->no_gep!=$affiche[7])){
										$temoin_modif='y';
										$cpt_modif++;
									}
									else{
										if($lig_ele->ele_id!=$affiche[5]){
											// GROS PROBLEME SI LES elenoet et ele_id ne sont plus des clés primaires
										}
									}
								}
								else {
									if((stripslashes($lig_ele->nom)!=stripslashes($affiche[0]))||
									(stripslashes($lig_ele->prenom)!=stripslashes($affiche[1]))||
									($lig_ele->sexe!=$affiche[2])||
									($lig_ele->naissance!=$new_date)||
									($lig_ele->no_gep!=$affiche[7])){
										$temoin_modif='y';
										$cpt_modif++;
									}
									else{
										if($lig_ele->ele_id!=$affiche[5]){
											// GROS PROBLEME SI LES elenoet et ele_id ne sont plus des clés primaires
										}
									}
								}
								// TESTER DANS j_eleves_regime pour doublant et regime
								//	table -> $affiche[]
								//	ext. -> 0
								//	d/p -> 2

								//	if ($reg_regime == "0") {$regime = "ext.";}
								//	if ($reg_regime == "2") {$regime = "d/p";}
								//	if ($reg_regime == "3") {$regime = "int.";}
								//	if ($reg_regime == "4") {$regime = "i-e";}


								//	R pour doublant -> O
								//	- pour doublant -> N


								$sql="SELECT * FROM j_eleves_regime WHERE (login='$lig_ele->login')";
								info_debug($sql);
								$res2=mysql_query($sql);
								if(mysql_num_rows($res2)>0){
									$tmp_regime="";
									$lig2=mysql_fetch_object($res2);
									//=========================
									// MODIF: boireaus 20071024
									$tmp_new_regime=traite_regime_sconet($affiche[8]);
									//switch($affiche[8]){
									/*
									switch($tmp_new_regime){
										case 0:
											$tmp_regime="ext.";
											break;
										case 2:
											$tmp_regime="d/p";
											break;
										case 3:
											$tmp_regime="int.";
											break;
										case 4:
											$tmp_regime="i-e";
											break;
									}
									*/
									$temoin_pb_regime_inhabituel="n";
									if("$tmp_new_regime"=="ERR"){
										$tmp_regime="d/p";
										$temoin_pb_regime_inhabituel="y";
									}
									else{
										$tmp_regime=$tmp_new_regime;
									}
									//=========================


									if($tmp_regime!=$lig2->regime){
										$temoin_modif='y';
										$cpt_modif++;
									}

									$tmp_doublant="";
									switch($affiche[6]){
										case "O":
											$tmp_doublant="R";
											break;
										case "N":
											$tmp_doublant="-";
											break;
									}
									if($tmp_doublant!=$lig2->doublant){
										$temoin_modif='y';
										$cpt_modif++;
									}
								}
								else{
									// Apparemment, aucune info n'est encore saisie dans j_eleves_regime
								}


								// Rechercher s'il y a un changement de classe?


								// Rechercher s'il y a un changement dans l'établissement d'origine
								$sql="SELECT id_etablissement FROM j_eleves_etablissements jee WHERE jee.id_eleve='$lig_ele->elenoet';";
								info_debug($sql);
								$res_ee=mysql_query($sql);
								if(mysql_num_rows($res_ee)>0) {
									$lig_ee=mysql_fetch_object($res_ee);
									$rne_etab_prec=$lig_ee->id_etablissement;
								}
								else {
									$rne_etab_prec="";
								}

								if(strtolower($affiche[10])!=strtolower($gepiSchoolRne)) {
									if(strtolower($affiche[10])!=strtolower($rne_etab_prec)) {
										$temoin_modif='y';
										$cpt_modif++;
									}
								}
							}
							else{
								$temoin_nouveau='y';
								$cpt_new++;
								// C'est un nouvel arrivant...

								// AFFICHER ET STOCKER DANS UN TABLEAU...
								// SUR VALIDATION, INSéRER DANS 'eleves' ET PAR LA SUITE AFFECTER DANS DES CLASSES POUR TELLES ET TELLES PERIODES ET COCHER LES OPTIONS POUR TELLES ET TELLES PERIODES.

								// TRANSMETTRE VIA UN FORMULAIRE POUR PROCEDER AUX AJOUTS, ET POUR LES eleves ENCHAINER AVEC LE CHOIX DE CLASSE ET D'OPTIONS
							}

							//echo "<tr><td>$k</td><td>\$temoin_modif=$temoin_modif</td><td>\$temoin_nouveau=$temoin_nouveau</td></tr>";

							if($temoin_modif=='y'){
								//echo "<tr style='background-color:green;'>\n";
								//echo "<tr>\n";
								$alt=$alt*(-1);
								/*
								echo "<tr style='background-color:";
								if($alt==1){
									echo "silver";
								}
								else{
									echo "white";
								}
								echo ";'>\n";
								*/
								echo "<tr class='lig$alt'>\n";

								echo "<td style='text-align: center;'>";
								//echo "<input type='checkbox' id='check_".$cpt."' name='modif[]' value='$cpt' />";
								// ELE_ID:
								echo "<input type='checkbox' id='check_".$cpt."' name='modif[]' value='$affiche[5]' />";
								echo "</td>\n";

								//echo "<td style='text-align: center; background-color: lightgreen;'>Modif</td>\n";
								echo "<td class='modif'>Modif</td>\n";

								// ELENOET:
								echo "<td style='text-align: center;'>";
								echo "$affiche[4]";
								echo "<input type='hidden' name='modif_".$cpt."_elenoet' value='$affiche[4]' />\n";
								echo "</td>\n";
								// ELE_ID:
								echo "<td style='text-align: center;'>";
								echo "$affiche[5]";
								echo "<input type='hidden' name='modif_".$cpt."_eleid' value='$affiche[5]' />\n";
								echo "<input type='hidden' name='modif_".$cpt."_login' value='$lig_ele->login' />\n";
								echo "</td>\n";

								//echo "<td style='text-align: center;";
								echo "<td";
								if(stripslashes($lig_ele->nom)!=stripslashes($affiche[0])){
									//echo " background-color:lightgreen;'>";
									echo " class='modif'>";
									if($lig_ele->nom!=''){
										echo stripslashes($lig_ele->nom)." <font color='red'>-&gt;</font>\n";
									}
								}
								else{
									//echo "'>";
									echo ">";
								}
								echo stripslashes($affiche[0]);
								echo "<input type='hidden' name='modif_".$cpt."_nom' value=\"$affiche[0]\" />\n";
								echo "</td>\n";

								//echo "<td style='text-align: center;";
								echo "<td";
								if(stripslashes($lig_ele->prenom)!=stripslashes($affiche[1])){
									//echo " background-color:lightgreen;'>";
									echo " class='modif'>";
									if($lig_ele->prenom!=''){
										echo stripslashes($lig_ele->prenom)." <font color='red'>-&gt;</font>\n";
									}
								}
								else{
									//echo "'>";
									echo ">";
								}
								echo stripslashes($affiche[1]);
								echo "<input type='hidden' name='modif_".$cpt."_prenom' value=\"$affiche[1]\" />\n";
								echo "</td>\n";

								//echo "<td style='text-align: center;";
								echo "<td";
								if($lig_ele->sexe!=$affiche[2]){
									//echo " background-color:lightgreen;'>";
									echo " class='modif'>";
									if($lig_ele->sexe!=''){
										echo "$lig_ele->sexe <font color='red'>-&gt;</font>\n";
									}
								}
								else{
									//echo "'>";
									echo ">";
								}
								echo "$affiche[2]";
								echo "<input type='hidden' name='modif_".$cpt."_sexe' value='$affiche[2]' />\n";
								echo "</td>\n";

								//echo "<td style='text-align: center;";


								if($ele_lieu_naissance=="y") {
									echo "<td";
									if(($lig_ele->naissance!=$new_date)||($lig_ele->lieu_naissance!=$affiche[11])) {
										//echo " background-color:lightgreen;'>";
										echo " class='modif'>";
										if(($lig_ele->naissance!='')||($lig_ele->lieu_naissance!='')) {
											if($lig_ele->naissance!='') {
												echo "$lig_ele->naissance ";
											}
											if($lig_ele->lieu_naissance!='') {
												echo "à ".get_commune($lig_ele->lieu_naissance,1)." ";
											}
											echo "<font color='red'>-&gt;</font>\n";
										}
									}
									else{
										//echo "'>";
										echo ">";
									}
									echo "$new_date";
									if($affiche[11]!="") {echo " à ".get_commune($affiche[11],1);}
									echo "<input type='hidden' name='modif_".$cpt."_naissance' value='$new_date' />\n";
									echo "<input type='hidden' name='modif_".$cpt."_lieu_naissance' value='".$affiche[11]."' />\n";
									echo "</td>\n";
								}
								else {
									echo "<td";
									if($lig_ele->naissance!=$new_date){
										//echo " background-color:lightgreen;'>";
										echo " class='modif'>";
										if($lig_ele->naissance!=''){
											echo "$lig_ele->naissance <font color='red'>-&gt;</font>\n";
										}
									}
									else{
										//echo "'>";
										echo ">";
									}
									echo "$new_date";
									echo "<input type='hidden' name='modif_".$cpt."_naissance' value='$new_date' />\n";
									echo "</td>\n";
								}

								//echo "<td style='text-align: center;'>$affiche[6]</td>\n";
								//echo "<td style='text-align: center;";
								echo "<td";
								//if($tmp_doublant!=$affiche[6]){
								if($tmp_doublant!=$lig2->doublant){
									//echo " background-color:lightgreen;'>";
									echo " class='modif'>";
									if($lig2->doublant!=''){
										echo "$lig2->doublant <font color='red'>-&gt;</font>\n";
									}
								}
								else{
									//echo "'>";
									echo ">";
								}
								//echo "$affiche[6]";
								echo "$tmp_doublant";
								echo "<input type='hidden' name='modif_".$cpt."_doublant' value='$tmp_doublant' />\n";
								echo "</td>\n";


								//echo "<td style='text-align: center;";
								echo "<td";
								if($lig_ele->no_gep!=$affiche[7]){
									//echo " background-color:lightgreen;'>";
									echo " class='modif'>";
									if($lig_ele->no_gep!=''){
										echo "$lig_ele->no_gep <font color='red'>-&gt;</font>\n";
									}
								}
								else{
									//echo "'>";
									echo ">";
								}
								echo "$affiche[7]";
								echo "<input type='hidden' name='modif_".$cpt."_nonat' value='$affiche[7]' />\n";
								echo "</td>\n";

								//echo "<td style='text-align: center;'>$affiche[8]</td>\n";
								//echo "<td style='text-align: center;";
								echo "<td";
								if($tmp_regime!=$lig2->regime){
									//echo " background-color:lightgreen;'>";
									echo " class='modif'>";
									if($lig2->regime!=''){
										echo "$lig2->regime <font color='red'>-&gt;</font>\n";
									}
								}
								else{
									//echo "'>";
									echo ">";
								}
								//echo "$affiche[8]";
								if($temoin_pb_regime_inhabituel=="y"){
									echo "<span style='color:red'>$tmp_regime</span>";
								}
								else{
									echo "$tmp_regime";
								}
								//echo " <span style='color:red'>DEBUG: ".$affiche[8]."</span> ";
								echo "<input type='hidden' name='modif_".$cpt."_regime' value=\"$tmp_regime\" />\n";
								echo "</td>\n";

								//echo "<td style='text-align: center; background-color: white;'>";
								echo "<td style='text-align: center;'>";
								echo "$affiche[9]";
								echo "</td>\n";




								$sql="SELECT id_etablissement FROM j_eleves_etablissements WHERE id_eleve='$lig_ele->elenoet';";
								info_debug($sql);
								$res_ee=mysql_query($sql);
								if(mysql_num_rows($res_ee)) {
									$lig_ee=mysql_fetch_object($res_ee);
									$rne_ancien_etab=$lig_ee->id_etablissement;
								}
								else {
									$rne_ancien_etab="";
								}

								if(strtolower($affiche[10])!=strtolower($gepiSchoolRne)) {
									echo "<td";
									if($rne_ancien_etab!=$affiche[10]){
										echo " class='modif'>";
										if($rne_ancien_etab!=''){
											echo "$rne_ancien_etab <font color='red'>-&gt;</font>\n";
										}
									}
									else{
										echo ">";
									}
									echo "$affiche[10]";
									echo "<input type='hidden' name='modif_".$cpt."_id_etab' value='$affiche[10]' />\n";
									echo "</td>\n";
								}
								else {
									echo "<td>";
									//echo "$affiche[10]";
									//echo "<input type='hidden' name='modif_".$cpt."_id_etab' value='$affiche[10]' />\n";
									echo "&nbsp;";
									//echo "<input type='hidden' name='modif_".$cpt."_id_etab' value='' />\n";
									echo "</td>\n";
								}


								echo "</tr>\n";
							}
							elseif($temoin_nouveau=='y'){
								//echo "<tr style='background-color:yellow;'>\n";
								//echo "<tr>\n";
								$alt=$alt*(-1);
								/*
								echo "<tr style='background-color:";
								if($alt==1){
									echo "silver";
								}
								else{
									echo "white";
								}
								echo ";'>\n";
								*/
								echo "<tr class='lig$alt'>\n";

								//echo "<td style='text-align: center;'><input type='checkbox' id='check_".$cpt."' name='new[]' value='$cpt' /></td>\n";
								echo "<td style='text-align: center;'><input type='checkbox' id='check_".$cpt."' name='new[]' value='$affiche[5]' /></td>\n";

								//echo "<td style='text-align: center; background-color: rgb(150, 200, 240);'>Nouveau</td>\n";
								echo "<td class='nouveau'>Nouveau</td>\n";


								echo "<td style='text-align: center;'>";
								echo "$affiche[4]";
								echo "<input type='hidden' name='new_".$cpt."_elenoet' value='$affiche[4]' />\n";
								echo "</td>\n";

								echo "<td style='text-align: center;'>";
								echo "$affiche[5]";
								echo "<input type='hidden' name='new_".$cpt."_eleid' value='$affiche[5]' />\n";
								echo "</td>\n";

								echo "<td style='text-align: center;'>";
								echo stripslashes($affiche[0]);
								echo "<input type='hidden' name='new_".$cpt."_nom' value=\"$affiche[0]\" />\n";
								echo "</td>\n";

								echo "<td style='text-align: center;'>";
								echo stripslashes($affiche[1]);
								echo "<input type='hidden' name='new_".$cpt."_prenom' value=\"$affiche[1]\" />\n";
								echo "</td>\n";

								echo "<td style='text-align: center;'>";
								echo "$affiche[2]";
								echo "<input type='hidden' name='new_".$cpt."_sexe' value='$affiche[2]' />\n";
								echo "</td>\n";

								$new_date=substr($affiche[3],0,4)."-".substr($affiche[3],4,2)."-".substr($affiche[3],6,2);
								echo "<td style='text-align: center;'>";
								echo "$new_date";
								if($ele_lieu_naissance=="y") {
									echo " à ".get_commune($affiche[11],1);
									echo "<input type='hidden' name='new_".$cpt."_lieu_naissance' value='".$affiche[11]."' />\n";
								}
								echo "<input type='hidden' name='new_".$cpt."_naissance' value='$new_date' />\n";
								echo "</td>\n";


								$tmp_doublant="";
								switch($affiche[6]){
									case "O":
										$tmp_doublant="R";
										break;
									case "N":
										$tmp_doublant="-";
										break;
								}

								echo "<td style='text-align: center;'>";
								echo "$tmp_doublant";
								echo "<input type='hidden' name='new_".$cpt."_doublant' value='$tmp_doublant' />\n";
								echo "</td>\n";

								echo "<td style='text-align: center;'>";
								echo "$affiche[7]";
								echo "<input type='hidden' name='new_".$cpt."_nonat' value='$affiche[7]' />\n";
								echo "</td>\n";


								$tmp_regime="";
								//=========================
								// MODIF: boireaus 20071024
								$tmp_new_regime=traite_regime_sconet($affiche[8]);
								//switch($affiche[8]){
								/*
								switch($tmp_new_regime){
									case 0:
										$tmp_regime="ext.";
										break;
									case 2:
										$tmp_regime="d/p";
										break;
									case 3:
										$tmp_regime="int.";
										break;
									case 4:
										$tmp_regime="i-e";
										break;
								}
								*/
								if("$tmp_new_regime"=="ERR"){
									$tmp_regime="d/p";

									echo "<td style='text-align: center;'>\n";
									echo "<span style='color:red'>$tmp_regime</span>";
									//echo " <span style='color:red'>DEBUG: ".$affiche[8]."</span> ";
									echo "<input type='hidden' name='new_".$cpt."_regime' value='$tmp_regime' />\n";
								}
								else{
									$tmp_regime=$tmp_new_regime;

									echo "<td style='text-align: center;'>\n";
									echo "$tmp_regime";
									//echo " <span style='color:red'>DEBUG: ".$affiche[8]."</span> ";
									echo "<input type='hidden' name='new_".$cpt."_regime' value='$tmp_regime' />\n";
								}
								//=========================

								echo "</td>\n";

								echo "<td style='text-align: center;'>";
								echo "$affiche[9]";
								echo "</td>\n";


								echo "<td style='text-align: center;'>";
								if(strtolower($affiche[10])!=strtolower($gepiSchoolRne)) {
									echo "$affiche[10]";
									echo "<input type='hidden' name='new_".$cpt."_id_etab' value='$affiche[10]' />\n";
								}
								else {
									echo "&nbsp;";
									//echo "<input type='hidden' name='new_".$cpt."_id_etab' value='' />\n";
								}
								echo "</td>\n";


								echo "</tr>\n";
							}

							$cpt++;
						//}
					}
				}
				echo "</table>\n";
				//echo "<p>On compte $cpt_modif champs modifiés et $cpt_new nouveaux élèves.</p>\n";
				//fclose($fp);

				echo "<script type='text/javascript'>
	function modifcase(mode){
		for(i=0;i<$cpt;i++){
			if(document.getElementById('check_'+i)){
				if(mode=='coche'){
					document.getElementById('check_'+i).checked=true;
				}
				else{
					document.getElementById('check_'+i).checked=false;
				}
			}
		}
	}
</script>\n";

				//echo "<input type='hidden' name='cpt' value='$cpt' />\n";
				if(count($tab_ele_id_diff)>$eff_tranche){
					echo "<input type='hidden' name='step' value='4' />\n";
				}
				else{
					echo "<input type='hidden' name='step' value='5' />\n";
				}

				echo "<p align='center'><input type=submit value='Valider' /></p>\n";
				//echo "<p align='center'><input type=submit value='Enregistrer les modifications' /></p>\n";
				echo "</form>\n";
			}

			break;
		case "5":
			echo "<h2>Import/mise à jour des élèves</h2>\n";

			info_debug("==============================================");
			info_debug("=============== Phase step $step =================");

			$modif=isset($_POST['modif']) ? $_POST['modif'] : NULL;
			$new=isset($_POST['new']) ? $_POST['new'] : NULL;

			// Ceux validés dans la dernière phase:
			if(isset($modif)){
				for($i=0;$i<count($modif);$i++){
					$sql="INSERT INTO tempo2 SET col1='modif', col2='$modif[$i]'";
					info_debug($sql);
					$insert=mysql_query($sql);
				}
			}

			if(isset($new)){
				for($i=0;$i<count($new);$i++){
					$sql="INSERT INTO tempo2 SET col1='new', col2='$new[$i]'";
					info_debug($sql);
					$insert=mysql_query($sql);
				}
			}
			// Si on rafraichit la page, les derniers insérés le sont à plusieurs reprises.
			// Les DISTINCT des requêtes qui suivent permettent de ne pas tenir compte des doublons.


			// CHANGEMENT DE MODE DE FONCTIONNEMENT:
			// On recherche dans tempo2 la liste des ELE_ID correspondant à modif ou new
			// Et on remplit/met à jour 'eleves' avec les enregistrements correspondants de temp_gep_import2

			$erreur=0;
			$cpt=0;
			$sql="SELECT DISTINCT t.* FROM temp_gep_import2 t, tempo2 t2 WHERE t.ELE_ID=t2.col2 AND t2.col1='modif'";
			info_debug($sql);
			$res_modif=mysql_query($sql);
			if(mysql_num_rows($res_modif)>0){
				echo "<p>Mise à jour des informations pour ";
				while($lig=mysql_fetch_object($res_modif)){
					//echo "Modif: $lig->ELE_ID : $lig->ELENOM $lig->ELEPRE<br />\n";

					if($cpt>0){echo ", ";}

					$naissance=substr($lig->ELEDATNAIS,0,4)."-".substr($lig->ELEDATNAIS,4,2)."-".substr($lig->ELEDATNAIS,6,2);

					/*
					switch($lig->ELEREG){
						case 0:
							$regime="ext.";
							break;
						case 2:
							$regime="d/p";
							break;
						case 3:
							$regime="int.";
							break;
						case 4:
							$regime="i-e";
							break;
					}
					*/
					$regime=traite_regime_sconet($lig->ELEREG);
					/*
					if("$regime"=="ERR"){
						$regime="d/p";
					}
					*/

					switch($lig->ELEDOUBL){
						case "O":
							$doublant="R";
							break;
						case "N":
							$doublant="-";
							break;
					}

					$sql="UPDATE eleves SET nom='".addslashes($lig->ELENOM)."',
											prenom='".addslashes($lig->ELEPRE)."',
											sexe='".$lig->ELESEXE."',
											naissance='".$naissance."',
											no_gep='".$lig->ELENONAT."'";

					if($ele_lieu_naissance=="y") {
						$sql.=", lieu_naissance='".$lig->LIEU_NAISSANCE."'";
					}

					// Je ne pense pas qu'on puisse corriger un ELENOET manquant...
					// Si on fait des imports avec Sconet, l'ELENOET n'est pas vide.
					// Et l'interface ne permet pas actuellement de saisir/corriger un ELE_ID
					$sql_tmp="SELECT elenoet,login FROM eleves WHERE ele_id='$lig->ELE_ID';";
					info_debug($sql);
					//echo "$sql_tmp<br />";
					$res_tmp=mysql_query($sql_tmp);
					if(mysql_num_rows($res_tmp)>0) {
						// L'élève a été trouvé dans la table 'eleves' d'après son ELE_ID
						// L'ELE_ID était correctement renseigné
						$lig_tmp=mysql_fetch_object($res_tmp);
						if($lig_tmp->elenoet==""){
							$sql.=", elenoet='".$lig->ELENOET."'";
						}
						$login_eleve=$lig_tmp->login;

						$sql.=" WHERE ele_id='".$lig->ELE_ID."';";
						//echo "============<br />";
						//echo "$sql<br />";
						info_debug($sql);
						$update=mysql_query($sql);
						if($update){
							echo "\n<span style='color:darkgreen;'>";
						}
						else{
							echo "\n<span style='color:red;'>";
							$erreur++;
						}
						//echo "$sql<br />\n";
						echo "$lig->ELEPRE $lig->ELENOM";
						echo "</span>";

						$sql="UPDATE j_eleves_regime SET doublant='$doublant'";
						if("$regime"!="ERR"){
							$sql.=", regime='$regime'";
						}
						$sql.=" WHERE (login='$login_eleve');";
						info_debug($sql);
						$res2=mysql_query($sql);
						if(!$res2){
							echo " <span style='color:red;'>(*)</span>";
							$erreur++;
						}
					}
					else {
						// L'élève n'a pas été trouvé dans la table 'eleves' d'après son ELE_ID
						// L'ELE_ID n'est pas correctement renseigné dans 'eleves'
						// La reconnaissance de 'modif' a dû se faire sur l'ELENOET
						$sql_tmp="SELECT ele_id,login FROM eleves WHERE elenoet='$lig->ELENOET';";
						//echo "$sql_tmp<br />";
						info_debug($sql);
						$res_tmp=mysql_query($sql_tmp);
						if(mysql_num_rows($res_tmp)>0) {
							$lig_tmp=mysql_fetch_object($res_tmp);
							/*
							if($lig_tmp->elenoet==""){
								$sql.=", elenoet='".$lig->ELENOET."'";
							}
							*/
							$old_ele_id=$lig_tmp->ele_id;
							$sql.=", ele_id='".$lig->ELE_ID."'";

							$login_eleve=$lig_tmp->login;

							$sql.=" WHERE elenoet='".$lig->ELENOET."';";
							//echo "============<br />";
							//echo "$sql<br />";
							info_debug($sql);
							$update=mysql_query($sql);
							if($update){
								echo "\n<span style='color:darkgreen;'>";
							}
							else{
								echo "\n<span style='color:red;'>";
								$erreur++;
							}
							//echo "$sql<br />\n";
							echo "$lig->ELEPRE $lig->ELENOM";
							echo "</span>";

							$sql="UPDATE j_eleves_regime SET doublant='$doublant'";
							if("$regime"!="ERR"){
								$sql.=", regime='$regime'";
							}
							$sql.=" WHERE (login='$login_eleve');";
							info_debug($sql);
							$res2=mysql_query($sql);
							if(!$res2){
								echo " <span style='color:red;'>(*)</span>";
								$erreur++;
							}

							$sql="UPDATE responsables2 SET ele_id='$lig->ELE_ID' WHERE ele_id='$old_ele_id';";
							info_debug($sql);
							$correction2=mysql_query($sql);
							if(!$correction2){
								echo " <span style='color:plum;'>(*)</span>";
								$erreur++;
							}

						}
						else {
							// On ne devrait pas arriver là.
							// Si la reconnaissance de modif a été réalisée, c'est qu'on avait une correspondance soit sur l'ELE_ID soit sur l'ELENOET
							echo "\n<span style='color:purple;'>";
							$erreur++;
							echo "$lig->ELEPRE $lig->ELENOM";
							echo "</span>";
						}
					}

					if(strtolower($lig->ETOCOD_EP)!=strtolower($gepiSchoolRne)) {
						$sql="SELECT 1=1 FROM j_eleves_etablissements WHERE id_eleve='$lig->ELENOET';";
						info_debug($sql);
						$test_ee=mysql_query($sql);
						if(mysql_num_rows($test_ee)>0) {
							if($lig->ETOCOD_EP!="") {
								$sql="UPDATE j_eleves_etablissements SET id_etablissement='$lig->ETOCOD_EP' WHERE id_eleve='$lig->ELENOET';";
								info_debug($sql);
								$update_ee=mysql_query($sql);
							}
							else {
								$sql="DELETE FROM j_eleves_etablissements WHERE id_eleve='$lig->ELENOET';";
								info_debug($sql);
								$del_ee=mysql_query($sql);
							}
						}
						else {
							$sql="INSERT INTO j_eleves_etablissements SET id_eleve='$lig->ELENOET', id_etablissement='$lig->ETOCOD_EP';";
							info_debug($sql);
							$insert_ee=mysql_query($sql);
						}
					}

					$cpt++;
				}
				echo "</p>\n";
			}

			$cpt=0;
			$sql="SELECT DISTINCT t.* FROM temp_gep_import2 t, tempo2 t2 WHERE t.ELE_ID=t2.col2 AND t2.col1='new'";
			info_debug($sql);
			$res_new=mysql_query($sql);
			if(mysql_num_rows($res_new)>0){

				$sql="CREATE TABLE IF NOT EXISTS temp_ele_classe (
				`ele_id` varchar(40) $chaine_mysql_collate NOT NULL default '',
				`divcod` varchar(40) $chaine_mysql_collate NOT NULL default ''
				);";
				info_debug($sql);
				$create_table = mysql_query($sql);

				$sql="TRUNCATE TABLE temp_ele_classe;";
				info_debug($sql);
				$vide_table = mysql_query($sql);


				echo "<p>Ajout de ";
				while($lig=mysql_fetch_object($res_new)){
					// ON VERIFIE QU'ON N'A PAS DEJA UN ELEVE DE MEME ele_id DANS eleves
					// CELA PEUT ARRIVER SI ON JOUE AVEC F5
					$sql="SELECT 1=1 FROM eleves WHERE ele_id='$lig->ELE_ID'";
					info_debug($sql);
					$test=mysql_query($sql);
					if(mysql_num_rows($test)==0){
						//echo "New: $lig->ELE_ID : $lig->ELENOM $lig->ELEPRE<br />";

						if($cpt>0){echo ", ";}

						$naissance=substr($lig->ELEDATNAIS,0,4)."-".substr($lig->ELEDATNAIS,4,2)."-".substr($lig->ELEDATNAIS,6,2);

						/*
						switch($lig->ELEREG){
							case 0:
								$regime="ext.";
								break;
							case 2:
								$regime="d/p";
								break;
							case 3:
								$regime="int.";
								break;
							case 4:
								$regime="i-e";
								break;
						}
						*/
						$regime=traite_regime_sconet($lig->ELEREG);
						// Si le régime est en erreur, on impose 'd/p' comme le moins mauvais choix dans ce cas
						if("$regime"=="ERR"){
							$regime="d/p";
						}

						switch($lig->ELEDOUBL){
							case "O":
								$doublant="R";
								break;
							case "N":
								$doublant="-";
								break;
						}

						$tmp_nom=strtr($lig->ELENOM,"àâäéèêëîïôöùûüçÀÄÂÉÈÊËÎÏÔÖÙÛÜÇ","aaaeeeeiioouuucAAAEEEEIIOOUUUC");
						$tmp_prenom=strtr($lig->ELEPRE,"àâäéèêëîïôöùûüçÀÄÂÉÈÊËÎÏÔÖÙÛÜÇ","aaaeeeeiioouuucAAAEEEEIIOOUUUC");

						// Générer un login...
						$temp1 = strtoupper($tmp_nom);
						$temp1 = preg_replace('/[^0-9a-zA-Z_]/',"", $temp1);
						$temp1 = strtr($temp1, " '-", "___");
						$temp1 = substr($temp1,0,7);
						$temp2 = strtoupper($tmp_prenom);
						$temp2 = preg_replace('/[^0-9a-zA-Z_]/',"", $temp2);
						$temp2 = strtr($temp2, " '-", "___");
						$temp2 = substr($temp2,0,1);
						$login_eleve = $temp1.'_'.$temp2;

						// On teste l'unicité du login que l'on vient de créer
						$k = 2;
						$test_unicite = 'no';
						$temp = $login_eleve;
						while ($test_unicite != 'yes') {
							//$test_unicite = test_unique_e_login($login_eleve,$i);
							$test_unicite = test_unique_login($login_eleve);
							if ($test_unicite != 'yes') {
								$login_eleve = $temp.$k;
								$k++;
							}
						}

						// On ne renseigne plus l'ERENO et on n'a pas l'EMAIL dans temp_gep_import2
						$sql="INSERT INTO eleves SET login='$login_eleve',
												nom='".addslashes($lig->ELENOM)."',
												prenom='".addslashes($lig->ELEPRE)."',
												sexe='".$lig->ELESEXE."',
												naissance='".$naissance."',
												no_gep='".$lig->ELENONAT."',
												elenoet='".$lig->ELENOET."',
												ele_id='".$lig->ELE_ID."'";
						if($ele_lieu_naissance=="y") {
							$sql.=", lieu_naissance='".$lig->LIEU_NAISSANCE."'";
						}
						$sql.=";";
						info_debug($sql);
						$insert=mysql_query($sql);
						if($insert){
							echo "\n<span style='color:blue;'>";
						}
						else{
							echo "\n<span style='color:red;'>";
							$erreur++;
						}
						//echo "$sql<br />\n";
						echo "$lig->ELEPRE $lig->ELENOM";
						echo "</span>";


						$sql="INSERT INTO j_eleves_regime SET doublant='$doublant',
									regime='$regime',
									login='$login_eleve';";
						info_debug($sql);
						$res2=mysql_query($sql);
						if(!$res2){
							echo " <span style='color:red;'>(*)</span>";
							$erreur++;
						}


						if(strtolower($lig->ETOCOD_EP)!=strtolower($gepiSchoolRne)) {
							$sql="SELECT 1=1 FROM j_eleves_etablissements WHERE id_eleve='$lig->ELENOET';";
							info_debug($sql);
							$test_ee=mysql_query($sql);
							if(mysql_num_rows($test_ee)>0) {
								if($lig->ETOCOD_EP!="") {
									$sql="UPDATE j_eleves_etablissements SET id_etablissement='$lig->ETOCOD_EP' WHERE id_eleve='$lig->ELENOET';";
									info_debug($sql);
									$update_ee=mysql_query($sql);
								}
								else {
									$sql="DELETE FROM j_eleves_etablissements WHERE id_eleve='$lig->ELENOET';";
									info_debug($sql);
									$del_ee=mysql_query($sql);
								}
							}
							else {
								$sql="INSERT INTO j_eleves_etablissements SET id_eleve='$lig->ELENOET', id_etablissement='$lig->ETOCOD_EP';";
								info_debug($sql);
								$insert_ee=mysql_query($sql);
							}
						}


						// On remplit aussi une table pour l'association avec la classe:
						// On fait le même traitement que dans step2.php
						// (dans step1.php, on a fait le même traitement que pour le remplissage de temp_gep_import2 ici)
						$classe=traitement_magic_quotes(corriger_caracteres($lig->DIVCOD));
						$sql="INSERT INTO temp_ele_classe SET ele_id='".$lig->ELE_ID."', divcod='$classe'";
						info_debug($sql);
						$insert=mysql_query($sql);

						$cpt++;
					}
				}
				echo "</p>\n";
			}

			echo "<p><br /></p>\n";




			if($cpt==0){
				// Pas de nouveau:
				switch($erreur){
					case 0:
						echo "<p>Passer à l'étape d'<a href='".$_SERVER['PHP_SELF']."?step=9&amp;stop=$stop'>import/mise à jour des personnes (<i>responsables</i>) et adresses</a>.</p>\n";
						break;

					case 1:
						echo "<p><font color='red'>Une erreur s'est produite.</font><br />\nVous devriez en chercher la cause avant de passer à l'étape d'<a href='".$_SERVER['PHP_SELF']."?step=9&amp;stop=$stop'>import/mise à jour des personnes (<i>responsables</i>) et adresses</a>.</p>\n";
						break;

					default:
						echo "<p><font color='red'>$erreur erreurs se sont produites.</font><br />\nVous devriez en chercher la cause avant de passer à l'étape d'<a href='".$_SERVER['PHP_SELF']."?step=9&amp;stop=$stop'>import/mise à jour des personnes (<i>responsables</i>) et adresses</a>.</p>\n";
						break;
				}
			}
			else{
				switch($erreur){
					case 0:
						echo "<p>Passer à l'étape d'<a href='".$_SERVER['PHP_SELF']."?step=6&amp;stop=$stop'>affectation des nouveaux élèves dans leurs classes</a>.</p>\n";
						break;

					case 1:
						echo "<p><font color='red'>Une erreur s'est produite.</font><br />\nVous devriez en chercher la cause avant de passer à l'étape d'<a href='".$_SERVER['PHP_SELF']."?step=6&amp;stop=$stop'>affectation des nouveaux élèves dans leurs classes</a>.</p>\n";
						break;

					default:
						echo "<p><font color='red'>$erreur erreurs se sont produites.</font><br />\nVous devriez en chercher la cause avant de passer à l'étape d'<a href='".$_SERVER['PHP_SELF']."?step=6&amp;stop=$stop'>affectation des nouveaux élèves dans leurs classes</a>.</p>\n";
						break;
				}
			}

			break;

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

// INSERER ICI: le traitement d'affectation dans les classes des nouveaux élèves...
//              ... et d'affectation dans les options?

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		case "6":
			echo "<h2>Import/mise à jour des élèves</h2>\n";

			info_debug("==============================================");
			info_debug("=============== Phase step $step =================");

			echo "<p>Affectation des nouveaux élèves dans leurs classes:</p>\n";

			echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>\n";
			//==============================
			// AJOUT pour tenir compte de l'automatisation ou non:
			echo "<input type='hidden' name='stop' id='id_form_stop' value='$stop' />\n";
			//==============================

			// DISTINCT parce qu'on peut avoir plusieurs enregistrements d'un même élève dans 'temp_ele_classe' si on a joué avec F5.
			// ERREUR: Il faut régler le problème plus haut parce que si on insère plusieurs fois l'élève, il est plusieurs fois dans 'eleves' avec des logins différents.
			$sql="SELECT DISTINCT e.*,t.divcod FROM temp_ele_classe t,eleves e WHERE t.ele_id=e.ele_id ORDER BY e.nom,e.prenom";
			info_debug($sql);
			$res_ele=mysql_query($sql);

			//echo mysql_num_rows($res_ele);

			if(mysql_num_rows($res_ele)==0){
				echo "<p>Bizarre: il semble que la table 'temp_ele_classe' ne contienne aucun identifiant de nouvel élève.</p>\n";
				// FAUT-IL SAUTER A UNE AUTRE ETAPE?
			}
			else{

				$sql="SELECT DISTINCT num_periode FROM periodes ORDER BY num_periode DESC LIMIT 1";
				info_debug($sql);
				$res_per=mysql_query($sql);

				if(mysql_num_rows($res_per)==0){
					echo "<p>Bizarre: il semble qu'aucune période ne soit encore définie.</p>\n";
					// FAUT-IL SAUTER A UNE AUTRE ETAPE?
				}
				else{

					$lig_per=mysql_fetch_object($res_per);
					$max_per=$lig_per->num_periode;
					echo "<input type='hidden' name='maxper' value='$max_per' />\n";

					echo "<p align='center'><input type='submit' value='Valider' /></p>\n";

					//echo "<table class='majimport'>\n";
					echo "<table class='boireaus'>\n";
					echo "<tr>\n";
					echo "<th rowspan='2'>Elève</th>\n";
					echo "<th rowspan='2'>Classe</th>\n";
					echo "<th colspan='$max_per'>Périodes</th>\n";

					$chaine_coche="";
					$chaine_decoche="";
					for($i=1;$i<=$max_per;$i++){
						$chaine_coche.="modif_case($i,\"col\",true);";
						$chaine_decoche.="modif_case($i,\"col\",false);";
					}

					//echo "<th rowspan='2'>&nbsp;</th>\n";
					echo "<th rowspan='2'>\n";
					echo "<a href='javascript:$chaine_coche'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
					echo "<a href='javascript:$chaine_decoche'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
					echo "</th>\n";
					echo "</tr>\n";

					echo "<tr>\n";
					for($i=1;$i<=$max_per;$i++){
						echo "<th>\n";
						echo "Période $i\n";
						echo "<br />\n";
						echo "<a href='javascript:modif_case($i,\"col\",true)'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
						echo "<a href='javascript:modif_case($i,\"col\",false)'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
						echo "</th>\n";

						$chaine_coche.="modif_case($i,\"col\",true);";
						$chaine_decoche.="modif_case($i,\"col\",false);";
					}
					echo "</tr>\n";





					$cpt=0;
					$alt=-1;
					while($lig_ele=mysql_fetch_object($res_ele)){
						$alt=$alt*(-1);
						/*
						echo "<tr style='background-color:";
						if($alt==1){
							echo "silver";
						}
						else{
							echo "white";
						}
						echo ";'>\n";
						*/
						echo "<tr class='lig$alt'>\n";

						echo "<td>";
						echo "$lig_ele->nom $lig_ele->prenom";
						echo "<input type='hidden' name='login_eleve[$cpt]' value='".$lig_ele->login."' />\n";
						echo "</td>\n";

						$sql="SELECT c.id FROM classes c WHERE c.classe='$lig_ele->divcod';";
						info_debug($sql);
						$res_classe=mysql_query($sql);
						if(mysql_num_rows($res_classe)>0){
							$lig_classe=mysql_fetch_object($res_classe);

							echo "<td>";
							echo $lig_ele->divcod;
							echo "<input type='hidden' name='id_classe[$cpt]' value='$lig_classe->id' />\n";
							echo "</td>\n";

							$sql="SELECT p.num_periode FROM periodes p, classes c
													WHERE p.id_classe=c.id AND
															c.classe='$lig_ele->divcod'
													ORDER BY num_periode;";
							info_debug($sql);
							$res_per=mysql_query($sql);
							$cpt_periode=1;
							while($lig_per=mysql_fetch_object($res_per)){
								echo "<td>\n";
								echo "<input type='checkbox' name='periode_".$cpt."_[$cpt_periode]' id='case".$cpt."_".$cpt_periode."'  value='$cpt_periode' />\n";
								echo "</td>\n";
								$cpt_periode++;
							}
							for($i=$cpt_periode;$i<=$max_per;$i++){
								echo "<td style='background-color: darkgray;'>\n";
								echo "</td>\n";
							}
						}
						else{
							// La classe n'a pas été identifiée
							$sql="SELECT DISTINCT id,classe FROM classes ORDER BY classe";
							info_debug($sql);
							$res_classe=mysql_query($sql);
							echo "<td>\n";
							if(mysql_num_rows($res_classe)>0){
								echo "<select name='id_classe[$cpt]'>\n";
								echo "<option value=''>---</option>\n";
								while($lig_classe=mysql_fetch_object($res_classe)){
									echo "<option value='$lig_classe->id'>$lig_classe->classe</option>\n";
								}
								echo "</select>\n";
							}
							echo "</td>\n";

							for($i=1;$i<=$max_per;$i++){
								echo "<td style='background-color: orange;'>\n";
								echo "<input type='checkbox' name='periode_".$cpt."_[$i]' value='$i' />\n";
								echo "</td>\n";
							}
						}

						echo "<td>\n";
						echo "<a href='javascript:modif_case($cpt,\"lig\",true)'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
						echo "<a href='javascript:modif_case($cpt,\"lig\",false)'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
						echo "</td>\n";

						echo "</tr>\n";
						$cpt++;
					}
					echo "</table>\n";
				}
			}


			echo "<script type='text/javascript' language='javascript'>
	function modif_case(rang,type,statut){
		// type: col ou lig
		// rang: le numéro de la colonne ou de la ligne
		// statut: true ou false
		if(type=='col'){
			for(k=0;k<$cpt;k++){
				if(document.getElementById('case'+k+'_'+rang)){
					document.getElementById('case'+k+'_'+rang).checked=statut;
				}
			}
		}
		else{
			for(k=1;k<=$max_per;k++){
				if(document.getElementById('case'+rang+'_'+k)){
					document.getElementById('case'+rang+'_'+k).checked=statut;
				}
			}
		}
		changement();
	}
</script>\n";

			echo "<p><br /></p>\n";

			//echo "<input type='hidden' name='step' value='6_1' />\n";
			echo "<input type='hidden' name='step' value='7' />\n";
			echo "<p align='center'><input type='submit' value='Valider' /></p>\n";

			echo "</form>\n";
			break;

		//case "6_1":
		case "7":
			echo "<h2>Import/mise à jour des élèves</h2>\n";

			info_debug("==============================================");
			info_debug("=============== Phase step $step =================");

			$login_eleve=isset($_POST['login_eleve']) ? $_POST['login_eleve'] : NULL;
			$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : NULL;
			$maxper=isset($_POST['maxper']) ? $_POST['maxper'] : NULL;

			if(!isset($login_eleve)){
				echo "<p>Vous n'avez affecté aucun élève.</p>\n";
			}
			else{
				echo "<p>\n";
				for($i=0;$i<count($login_eleve);$i++){
					$sql="SELECT nom, prenom FROM eleves WHERE login='$login_eleve[$i]'";
					//echo $sql."<br />";
					info_debug($sql);
					$res_ele=mysql_query($sql);
					if(mysql_num_rows($res_ele)>0){
						$lig_ele=mysql_fetch_object($res_ele);

						echo "Affectation de $lig_ele->prenom $lig_ele->nom ";

						//if(is_int($id_classe[$i])){
						if(is_numeric($id_classe[$i])){
							$tab_periode=isset($_POST['periode_'.$i.'_']) ? $_POST['periode_'.$i.'_'] : NULL;

							if(isset($tab_periode)){
								$sql="SELECT classe FROM classes WHERE id='$id_classe[$i]'";
								info_debug($sql);
								$test=mysql_query($sql);
								if(mysql_num_rows($test)>0){
									$lig_classe=mysql_fetch_object($test);

									echo "en $lig_classe->classe pour ";
									if(count($tab_periode)==1){
										echo "la période ";
									}
									else{
										echo "les périodes ";
									}

									$cpt_per=0;
									for($j=1;$j<=$maxper;$j++){
										if(isset($tab_periode[$j])){
											//if(is_int($tab_periode[$j])){
											if(is_numeric($tab_periode[$j])){
												$sql="SELECT 1=1 FROM periodes WHERE id_classe='$id_classe[$i]' AND num_periode='$tab_periode[$j]'";
												info_debug($sql);
												$test=mysql_query($sql);

												if(mysql_num_rows($test)>0){
													// VERIFICATION: Si on fait F5 pour rafraichir la page, on risque d'insérer plusieurs fois le même enregistrement.
													$sql="SELECT 1=1 FROM j_eleves_classes WHERE login='$login_eleve[$i]' AND
																						id_classe='$id_classe[$i]' AND
																						periode='$tab_periode[$j]'";
													info_debug($sql);
													$test=mysql_query($sql);

													if(mysql_num_rows($test)==0){
														$sql="INSERT INTO j_eleves_classes SET login='$login_eleve[$i]',
																							id_classe='$id_classe[$i]',
																							periode='$tab_periode[$j]',
																							rang='0'";
														info_debug($sql);
														$insert=mysql_query($sql);
													}
													if($cpt_per>0){echo ", ";}
													echo "$j";
													$cpt_per++;
												}
											}
										}
									}
								}
								else{
									echo "dans aucune classe (<i>identifiant de classe invalide</i>).";
								}
							}
							else{
								echo "dans aucune classe (<i>aucune période cochée</i>).";
							}
						}
						else{
							echo "dans aucune classe (<i>identifiant de classe invalide</i>).";
						}
						echo "<br />\n";
					}
				}
				echo "</p>\n";
			}

			echo "<p>Passer à l'étape d'<a href='".$_SERVER['PHP_SELF']."?step=8&amp;stop=$stop'>inscription des nouveaux élèves dans les groupes</a>.</p>\n";

			break;

		case "8":

			echo "<h2>Import/mise à jour des élèves</h2>\n";

			info_debug("==============================================");
			info_debug("=============== Phase step $step =================");

			$opt_eleve=isset($_POST['opt_eleve']) ? $_POST['opt_eleve'] : NULL;
			$eleve=isset($_POST['eleve']) ? $_POST['eleve'] : NULL;

			echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>\n";
			//==============================
			// AJOUT pour tenir compte de l'automatisation ou non:
			echo "<input type='hidden' name='stop' id='id_form_stop' value='$stop' />\n";
			//==============================

			if(!isset($opt_eleve)){
				$sql="SELECT e.* FROM eleves e, temp_ele_classe t WHERE t.ele_id=e.ele_id ORDER BY e.nom,e.prenom";
				info_debug($sql);
				$res_ele=mysql_query($sql);

				if(mysql_num_rows($res_ele)==0){
					// CA NE DEVRAIT PAS ARRIVER

					echo "<p>Il semble qu'il n'y ait aucun élève à affecter.</p>\n";

					// METTRE LE LIEN VERS L'ETAPE SUIVANTE

					echo "</form>\n";
					require("../lib/footer.inc.php");
					die();
				}

				$lig_ele=mysql_fetch_object($res_ele);
				$nom_eleve=$lig_ele->nom;
				$prenom_eleve=$lig_ele->prenom;
				$login_eleve=$lig_ele->login;
				$ele_id=$lig_ele->ele_id;

				while($lig_ele=mysql_fetch_object($res_ele)){
					echo "<input type='hidden' name='eleve[]' value='$lig_ele->ele_id' />\n";
				}

			}
			else{
				$login_eleve=isset($_POST['login_eleve']) ? $_POST['login_eleve'] : NULL;
				$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : NULL;
				$sql="SELECT * FROM periodes WHERE id_classe='$id_classe' ORDER BY num_periode";
				info_debug($sql);
				$res_per=mysql_query($sql);
				$nb_periode=mysql_num_rows($res_per)+1;

				$cpe_resp=isset($_POST['cpe_resp']) ? $_POST['cpe_resp'] : NULL;

				if(isset($cpe_resp)){
					if("$cpe_resp"!=""){
						// Par précaution:
						$sql="DELETE FROM j_eleves_cpe WHERE e_login='$login_eleve' AND cpe_login='$cpe_resp'";
						info_debug($sql);
						$nettoyage_cpe=mysql_query($sql);

						$sql="INSERT INTO j_eleves_cpe SET e_login='$login_eleve', cpe_login='$cpe_resp'";
						info_debug($sql);
						$insert_cpe=mysql_query($sql);
					}
				}

				$pp_resp=isset($_POST['pp_resp']) ? $_POST['pp_resp'] : NULL;

				if(isset($pp_resp)){
					if("$pp_resp"!=""){
						// Par précaution:
						$sql="DELETE FROM j_eleves_professeurs WHERE login='$login_eleve' AND professeur='$pp_resp' AND id_classe='$id_classe';";
						// DEBUG:
						//echo "$sql<br />\n";
						info_debug($sql);
						$nettoyage_pp=mysql_query($sql);

						$sql="INSERT INTO j_eleves_professeurs SET login='$login_eleve', professeur='$pp_resp', id_classe='$id_classe';";
						// DEBUG:
						//echo "$sql<br />\n";
						info_debug($sql);
						$insert_pp=mysql_query($sql);
					}
				}
				/*
				$cpt=1;
				while($lig_per=mysql_fetch_object($res_per)){
					$nom_periode[$cpt]=$lig_per->nom_periode;
					$cpt++;
				}
				*/

				$j = 1;
				while ($j < $nb_periode) {
					$call_group = mysql_query("SELECT DISTINCT g.id, g.name FROM groupes g, j_groupes_classes jgc WHERE (g.id = jgc.id_groupe and jgc.id_classe = '" . $id_classe ."') ORDER BY jgc.priorite, g.name");
					$nombre_ligne = mysql_num_rows($call_group);
					$i=0;
					while ($i < $nombre_ligne) {
						$id_groupe = mysql_result($call_group, $i, "id");
						$nom_groupe = mysql_result($call_group, $i, "name");
						$id_group[$j] = $id_groupe."_".$j;
						$test_query = mysql_query("SELECT 1=1 FROM j_eleves_groupes WHERE (" .
								"id_groupe = '" . $id_groupe . "' and " .
								"login = '" . $login_eleve . "' and " .
								"periode = '" . $j . "')");
						$test = mysql_num_rows($test_query);
						if (isset($_POST[$id_group[$j]])) {
							if ($test == 0) {
								$req = mysql_query("INSERT INTO j_eleves_groupes SET id_groupe = '" . $id_groupe . "', login = '" . $login_eleve . "', periode = '" . $j ."'");
							}
						} else {
							$test1 = mysql_query("SELECT 1=1 FROM matieres_notes WHERE (id_groupe = '".$id_groupe."' and login = '".$login_eleve."' and periode = '$j')");
							$nb_test1 = mysql_num_rows($test1);
							$test2 = mysql_query("SELECT 1=1 FROM matieres_appreciations WHERE (id_groupe = '".$id_groupe."' and login = '".$login_eleve."' and periode = '$j')");
							$nb_test2 = mysql_num_rows($test2);
							if (($nb_test1 != 0) or ($nb_test2 != 0)) {
								$msg = $msg."--> Impossible de supprimer cette option pour l'élève $login_eleve car des moyennes ou appréciations ont déjà été rentrées pour le groupe $nom_groupe pour la période $j ! Commencez par supprimer ces données !<br />";
							} else {
								if ($test != "0")  $req = mysql_query("DELETE FROM j_eleves_groupes WHERE (login='".$login_eleve."' and id_groupe='".$id_groupe."' and periode = '".$j."')");
							}
						}
						$i++;
					}
					$j++;
				}



				if(isset($eleve)){
					$sql="SELECT e.* FROM eleves e WHERE e.ele_id='$eleve[0]'";
					info_debug($sql);
					$res_ele=mysql_query($sql);

					$lig_ele=mysql_fetch_object($res_ele);
					$nom_eleve=$lig_ele->nom;
					$prenom_eleve=$lig_ele->prenom;
					$login_eleve=$lig_ele->login;
					$ele_id=$lig_ele->ele_id;

					for($i=1;$i<count($eleve);$i++){
						echo "<input type='hidden' name='eleve[]' value='$eleve[$i]' />\n";
					}
				}
				else{
					echo "<p>Tous les élèves ont été parcourus.</p>\n";

					// METTRE LE LIEN VERS L'ETAPE SUIVANTE

					echo "<input type='hidden' name='step' value='9' />\n";
					echo "<p><input type='submit' value='Etape suivante: Responsables' /></p>\n";

					echo "</form>\n";
					require("../lib/footer.inc.php");
					die();
				}
			}

			echo "<input type='hidden' name='opt_eleve' value='y' />\n";

			$sql="SELECT DISTINCT c.id,c.classe FROM classes c,j_eleves_classes jec
									WHERE jec.id_classe=c.id AND
										jec.login='$login_eleve'";
			info_debug($sql);
			$res_classe=mysql_query($sql);

			if(mysql_num_rows($res_classe)==0){
				echo "<p>$prenom_eleve $nom_eleve n'est dans aucune classe.</p>\n";

				// PASSER AU SUIVANT...

				echo "<input type='hidden' name='step' value='8' />\n";
				echo "<p><input type='submit' value='Suite' /></p>\n";

				echo "</form>\n";
			}
			else{
				$lig_classe=mysql_fetch_object($res_classe);
				$id_classe=$lig_classe->id;

				$sql="SELECT * FROM periodes WHERE id_classe='$id_classe' ORDER BY num_periode";
				info_debug($sql);
				$res_per=mysql_query($sql);

				if(mysql_num_rows($res_per)==0){
					echo "<p>L'élève $prenom_eleve $ele_nom_eleve serait dans une classe sans période???</p>\n";

					// PASSER AU SUIVANT...
					echo "</form>\n";
				}
				else{

					echo "<p><b>$prenom_eleve $nom_eleve</b> (<i>$lig_classe->classe</i>)</p>\n";

					//===========================
					// A FAIRE: boireaus 20071129
					//          Ajouter l'association avec le PP et le CPE
					$sql="SELECT login, nom, prenom FROM utilisateurs WHERE statut='cpe' ORDER BY nom, prenom;";
					info_debug($sql);
					$res_cpe=mysql_query($sql);

					echo "<table border='0'>\n";
					if(mysql_num_rows($res_cpe)>0){
						echo "<tr><td>CPE responsable: </td><td><select name='cpe_resp'>\n";
						echo "<option value=''>---</option>\n";
						while($lig_cpe=mysql_fetch_object($res_cpe)){
							echo "<option value='$lig_cpe->login'";
							if(mysql_num_rows($res_cpe)==1) {echo " selected";}
							echo ">$lig_cpe->nom $lig_cpe->prenom</option>\n";
						}
						echo "</select>\n";
						echo "</td>\n";
						echo "</tr>\n";
					}

					$sql="SELECT DISTINCT u.login, u.nom, u.prenom FROM utilisateurs u, j_eleves_professeurs jep
										WHERE jep.id_classe='$id_classe' AND
												jep.professeur=u.login
										ORDER BY u.nom, u.prenom;";
					info_debug($sql);
					$res_pp=mysql_query($sql);
					if(mysql_num_rows($res_pp)>0){
						echo "<tr><td>".ucfirst(getSettingValue('gepi_prof_suivi')).": </td><td><select name='pp_resp'>\n";
						echo "<option value=''>---</option>\n";
						while($lig_pp=mysql_fetch_object($res_pp)){
							echo "<option value='$lig_pp->login'";
							if(mysql_num_rows($res_pp)==1) {echo " selected";}
							echo ">$lig_pp->nom $lig_pp->prenom</option>\n";
						}
						echo "</select>\n";
						echo "</td>\n";
						echo "</tr>\n";
					}
					echo "</table>\n";
					echo "<p>&nbsp;</p>\n";

					//===========================

					$nb_periode=mysql_num_rows($res_per)+1;

					$cpt=1;
					while($lig_per=mysql_fetch_object($res_per)){
						$nom_periode[$cpt]=$lig_per->nom_periode;
						$cpt++;
					}


					echo "<p>Affectation dans les groupes de l'élève $prenom_eleve $nom_eleve (<i>$lig_classe->classe</i>)</p>\n";
					echo "<p align='center'><input type='submit' value='Valider' /></p>\n";

					echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";
					echo "<input type='hidden' name='login_eleve' value='$login_eleve' />\n";


					$sql="SELECT DISTINCT g.id, g.name FROM groupes g,
															j_groupes_classes jgc
									WHERE (g.id = jgc.id_groupe AND
											jgc.id_classe = '" . $id_classe ."')
									ORDER BY jgc.priorite, g.name";
					info_debug($sql);
					$call_group=mysql_query($sql);
					$nombre_ligne=mysql_num_rows($call_group);

					//echo "<table border = '1' cellpadding='5' cellspacing='0'>\n";
					//echo "<table class='majimport' cellpadding='5' cellspacing='0'>\n";
					echo "<table class='boireaus' cellpadding='5' cellspacing='0'>\n";
					//echo "<tr align='center'><td><b>Matière</b></td>";
					echo "<tr align='center'><th><b>Matière</b></th>\n";

					$j = 1;
					$chaine_coche="";
					$chaine_decoche="";
					while ($j < $nb_periode) {
						//echo "<td><b>".$nom_periode[$j]."</b><br />\n";
						echo "<th><b>".$nom_periode[$j]."</b><br />\n";
						echo "<a href='javascript:modif_case($j,\"col\",true)'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
						echo "<a href='javascript:modif_case($j,\"col\",false)'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
						//echo "</td>";
						echo "</th>\n";

						$chaine_coche.="modif_case($j,\"col\",true);";
						$chaine_decoche.="modif_case($j,\"col\",false);";

						$j++;
					}
					//echo "<td>&nbsp;</td>\n";
					//echo "<th>&nbsp;</th>\n";
					echo "<th>\n";

					echo "<a href='javascript:$chaine_coche'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
					echo "<a href='javascript:$chaine_decoche'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";

					echo "</th>\n";
					echo "</tr>\n";

					$nb_erreurs=0;
					$i=0;
					$alt=-1;
					while ($i < $nombre_ligne) {
						$id_groupe = mysql_result($call_group, $i, "id");
						$nom_groupe = mysql_result($call_group, $i, "name");

						$alt=$alt*(-1);
						/*
						echo "<tr style='background-color:";
						if($alt==1){
							echo "silver";
						}
						else{
							echo "white";
						}
						echo ";'>\n";
						*/
						echo "<tr class='lig$alt'>\n";
						echo "<td>".$nom_groupe;
						echo "</td>\n";
						$j = 1;
						while ($j < $nb_periode) {
							$test=mysql_query("SELECT 1=1 FROM j_eleves_groupes WHERE (" .
									"id_groupe = '" . $id_groupe . "' and " .
									"login = '" . $login_eleve . "' and " .
									"periode = '" . $j . "')");

							$sql="SELECT * FROM j_eleves_classes WHERE login='$login_eleve' AND periode='$j' AND id_classe='$id_classe'";
							// CA NE VA PAS... SUR LES GROUPES A REGROUPEMENT, IL FAUT PRENDRE DES PRECAUTIONS...
							info_debug($sql);
							$res_test_class_per=mysql_query($sql);
							if(mysql_num_rows($res_test_class_per)==0){
								if (mysql_num_rows($test) == "0") {
									echo "<td>&nbsp;</td>\n";
								}
								else{
									$sql="SELECT DISTINCT id_classe FROM j_groupes_classes WHERE id_groupe='$id_groupe'";
									info_debug($sql);
									$res_grp=mysql_query($sql);
									$temoin="";
									while($lig_clas=mysql_fetch_object($res_grp)){
										$sql="SELECT 1=1 FROM j_eleves_classes WHERE id_classe='$lig_clas->id_classe' AND login='$login_eleve' AND periode='$j'";
										info_debug($sql);
										$res_test_ele=mysql_query($sql);
										if(mysql_num_rows($res_test_ele)==1){
											$sql="SELECT classe FROM classes WHERE id='$lig_clas->id_classe'";
											info_debug($sql);
											$res_tmp=mysql_query($sql);
											$lig_tmp=mysql_fetch_object($res_tmp);
											$clas_tmp=$lig_tmp->classe;

											$temoin=$clas_tmp;
										}
									}

									if($temoin!=""){
										echo "<td><center>".$temoin."<input type=hidden name=".$id_groupe."_".$j." value='checked' /></center></td>\n";
									}
									else{
										$msg_erreur="Cette case est validée et ne devrait pas l être. Validez le formulaire pour corriger.";
										echo "<td><center><a href='#' alt='$msg_erreur' title='$msg_erreur'><font color='red'>ERREUR</font></a></center></td>\n";
										$nb_erreurs++;
									}
								}
							}
							else{

								/*
								// Un autre test à faire:
								// Si l'élève est resté dans le groupe alors qu'il n'est plus dans cette classe pour la période
								$sql="SELECT 1=1 FROM j_eleves_classes WHERE id_classe='$id_classe' AND periode='$j' AND login='$login_eleve'";
								*/

								//=========================
								// MODIF: boireaus
								if (mysql_num_rows($test) == "0") {
									//echo "<td><center><input type=checkbox name=".$id_groupe."_".$j." /></center></td>\n";
									echo "<td><center><input type=checkbox id=case".$i."_".$j." name=".$id_groupe."_".$j." onchange='changement();' /></center></td>\n";
								} else {
									//echo "<td><center><input type=checkbox name=".$id_groupe."_".$j." CHECKED /></center></td>\n";
									echo "<td><center><input type=checkbox id=case".$i."_".$j." name=".$id_groupe."_".$j." onchange='changement();' checked /></center></td>\n";
								}
								//=========================
							}
							$j++;
						}
						//=========================
						// AJOUT: boireaus
						echo "<td>\n";
						//echo "<input type='button' name='coche_lig_$i' value='C' onClick='modif_case($i,\"lig\",true)' />/\n";
						//echo "<input type='button' name='decoche_lig_$i' value='D' onClick='modif_case($i,\"lig\",false)' />\n";
						echo "<a href='javascript:modif_case($i,\"lig\",true)'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
						echo "<a href='javascript:modif_case($i,\"lig\",false)'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
						echo "</td>\n";
						//=========================
						echo "</tr>\n";
						$i++;
					}
					echo "</table>\n";


					echo "<script type='text/javascript' language='javascript'>
	function modif_case(rang,type,statut){
		// type: col ou lig
		// rang: le numéro de la colonne ou de la ligne
		// statut: true ou false
		if(type=='col'){
			for(k=0;k<$nombre_ligne;k++){
				if(document.getElementById('case'+k+'_'+rang)){
					document.getElementById('case'+k+'_'+rang).checked=statut;
				}
			}
		}
		else{
			for(k=1;k<$nb_periode;k++){
				if(document.getElementById('case'+rang+'_'+k)){
					document.getElementById('case'+rang+'_'+k).checked=statut;
				}
			}
		}
		changement();
	}
</script>\n";

					echo "<input type='hidden' name='step' value='8' />\n";
					echo "<p align='center'><input type='submit' value='Valider' /></p>\n";
					echo "</form>\n";
				}

			}


			break;

		case "9":
			echo "<h2>Import/mise à jour des responsables</h2>\n";

			info_debug("==============================================");
			info_debug("=============== Phase step $step =================");

			echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
			//==============================
			// AJOUT pour tenir compte de l'automatisation ou non:
			//echo "<input type='hidden' name='stop' id='id_form_stop' value='$stop' />\n";
			//==============================
			echo "<p>Veuillez fournir le fichier ResponsablesAvecAdresses.xml:<br />\n";
			echo "<input type=\"file\" size=\"80\" name=\"responsables_xml_file\" /><br />\n";
			echo "<input type='hidden' name='step' value='10' />\n";
			//echo "<input type='hidden' name='is_posted' value='yes' />\n";
	        if ($gepiSettings['unzipped_max_filesize']>=0) {
	        	echo "<p style=\"color: red;\">REMARQUE : Vous pouvez fournir à Gepi le fichier compressé issu directement de SCONET. (Ex : ResponsablesAvecAdresses.zip)</p>";
			}

			echo "<input type='checkbox' name='ne_pas_proposer_resp_sans_eleve' id='ne_pas_proposer_resp_sans_eleve' value='non' checked />\n";
			//$ne_pas_proposer_resp_sans_eleve
			echo "<label for='ne_pas_proposer_resp_sans_eleve' style='cursor: pointer;'> Ne pas proposer d'ajouter les responsables non associés à des élèves.</label><br />(<i>de telles entrées peuvent subsister en très grand nombre dans Sconet</i>)<br />\n";

			//==============================
			// AJOUT pour tenir compte de l'automatisation ou non:
			//echo "<input type='hidden' name='stop' id='id_form_stop' value='$stop' />\n";
			echo "<input type='checkbox' name='stop' id='id_form_stop' value='y' ";
			if("$stop"=="y"){echo "checked ";}
			echo "/><label for='id_form_stop' style='cursor: pointer;'> Désactiver le mode automatique.</label>";
			//echo "</p>\n";
			//==============================

			echo "<p><input type='submit' value='Valider' /></p>\n";
			echo "</form>\n";

			echo "<p><br /></p>\n";

			echo "<p><i>NOTE:</i></p>\n";
			echo "<blockquote>\n";
			echo "<p>Après une phase d'analyse des différences, les différences seront affichées et des cases à cocher seront proposées pour valider les modifications.</p>\n";
			echo "<p>Les différences concernant les personnes, puis les adresses sont recherchées.<br />Ensuite seulement, il vous est proposé de valider les modifications concernant les personnes et adresses.</p>\n";
			echo "<p>Un troisième parcours des différences est ensuite effectué pour rechercher les changements dans les associations responsables/élèves.</p>\n";
			echo "</blockquote>\n";

			require("../lib/footer.inc.php");
			die();

			break;
		case "10":
			echo "<h2>Import/mise à jour des responsables</h2>\n";

			info_debug("==============================================");
			info_debug("=============== Phase step $step =================");

			$_SESSION['ne_pas_proposer_resp_sans_eleve']=$ne_pas_proposer_resp_sans_eleve;

			$post_max_size=ini_get('post_max_size');
			$upload_max_filesize=ini_get('upload_max_filesize');
			$max_execution_time=ini_get('max_execution_time');
			$memory_limit=ini_get('memory_limit');


			$xml_file = isset($_FILES["responsables_xml_file"]) ? $_FILES["responsables_xml_file"] : NULL;
			if(!is_uploaded_file($xml_file['tmp_name'])) {
				echo "<p style='color:red;'>L'upload du fichier a échoué.</p>\n";

				echo "<p>Les variables du php.ini peuvent peut-être expliquer le problème:<br />\n";
				echo "post_max_size=$post_max_size<br />\n";
				echo "upload_max_filesize=$upload_max_filesize<br />\n";
				echo "</p>\n";

				// Il ne faut pas aller plus loin...
				// SITUATION A GERER
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
					// Il ne faut pas aller plus loin...
					// SITUATION A GERER
					require("../lib/footer.inc.php");
					die();
				}

				echo "<p>Le fichier a été uploadé.</p>\n";

				//$source_file=stripslashes($xml_file['tmp_name']);
				$source_file=$xml_file['tmp_name'];
				$dest_file="../temp/".$tempdir."/responsables.xml";
				$res_copy=copy("$source_file" , "$dest_file");

				//===============================================================
				// ajout prise en compte des fichiers ZIP: Marc Leygnac

				$unzipped_max_filesize=getSettingValue('unzipped_max_filesize')*1024*1024;
				// $unzipped_max_filesize = 0    pas de limite de taille pour les fichiers extraits
				// $unzipped_max_filesize < 0    extraction zip désactivée
				if($unzipped_max_filesize>=0) {
					$fichier_emis=$xml_file['name'];
					$extension_fichier_emis=strtolower(strrchr($fichier_emis,"."));
					if (($extension_fichier_emis==".zip")||($xml_file['type']=="application/zip"))
						{
						require_once('../lib/pclzip.lib.php');
						$archive = new PclZip($dest_file);

						if (($list_file_zip = $archive->listContent()) == 0) {
							echo "<p style='color:red;'>Erreur : ".$archive->errorInfo(true)."</p>\n";
							require("../lib/footer.inc.php");
							die();
						}

						if(sizeof($list_file_zip)!=1) {
							echo "<p style='color:red;'>Erreur : L'archive contient plus d'un fichier.</p>\n";
							require("../lib/footer.inc.php");
							die();
						}

						/*
						echo "<p>\$list_file_zip[0]['filename']=".$list_file_zip[0]['filename']."<br />\n";
						echo "\$list_file_zip[0]['size']=".$list_file_zip[0]['size']."<br />\n";
						echo "\$list_file_zip[0]['compressed_size']=".$list_file_zip[0]['compressed_size']."</p>\n";
						*/
						//echo "<p>\$unzipped_max_filesize=".$unzipped_max_filesize."</p>\n";

						if(($list_file_zip[0]['size']>$unzipped_max_filesize)&&($unzipped_max_filesize>0)) {
							echo "<p style='color:red;'>Erreur : La taille du fichier extrait (<i>".$list_file_zip[0]['size']." octets</i>) dépasse la limite paramétrée (<i>$unzipped_max_filesize octets</i>).</p>\n";
							require("../lib/footer.inc.php");
							die();
						}

						$res_extract=$archive->extract(PCLZIP_OPT_PATH, "../temp/".$tempdir);
						if ($res_extract != 0) {
							echo "<p>Le fichier uploadé a été dézippé.</p>\n";
							$fichier_extrait=$res_extract[0]['filename'];
							unlink("$dest_file"); // Pour Wamp...
							$res_copy=rename("$fichier_extrait" , "$dest_file");
						}
						else {
							echo "<p style='color:red'>Echec de l'extraction de l'archive ZIP.</p>\n";
							require("../lib/footer.inc.php");
							die();
						}
					}
				}
				//fin  ajout prise en compte des fichiers ZIP
				//===============================================================

				if(!$res_copy){
					echo "<p style='color:red;'>La copie du fichier vers le dossier temporaire a échoué.<br />Vérifiez que l'utilisateur ou le groupe apache ou www-data a accès au dossier temp/$tempdir</p>\n";
					// Il ne faut pas aller plus loin...
					// SITUATION A GERER
					require("../lib/footer.inc.php");
					die();
				}
				else{
					echo "<p>La copie du fichier vers le dossier temporaire a réussi.</p>\n";

					//$sql="CREATE TABLE IF NOT EXISTS resp_pers (
					/*
					$sql="CREATE TABLE IF NOT EXISTS temp_resp_pers_import (
							`pers_id` varchar(10) NOT NULL,
							`login` varchar(50) NOT NULL,
							`nom` varchar(30) NOT NULL,
							`prenom` varchar(30) NOT NULL,
							`civilite` varchar(5) NOT NULL,
							`tel_pers` varchar(255) NOT NULL,
							`tel_port` varchar(255) NOT NULL,
							`tel_prof` varchar(255) NOT NULL,
							`mel` varchar(100) NOT NULL,
							`adr_id` varchar(10) NOT NULL,
						PRIMARY KEY  (`pers_id`));";
					*/
					$sql="DROP TABLE IF EXISTS temp_resp_pers_import;";
					info_debug($sql);
					$nettoyage = mysql_query($sql);

					$sql="CREATE TABLE IF NOT EXISTS temp_resp_pers_import (
							`pers_id` varchar(10) $chaine_mysql_collate NOT NULL,
							`login` varchar(50) $chaine_mysql_collate NOT NULL,
							`nom` varchar(30) $chaine_mysql_collate NOT NULL,
							`prenom` varchar(30) $chaine_mysql_collate NOT NULL,
							`civilite` varchar(5) $chaine_mysql_collate NOT NULL,
							`tel_pers` varchar(255) $chaine_mysql_collate NOT NULL,
							`tel_port` varchar(255) $chaine_mysql_collate NOT NULL,
							`tel_prof` varchar(255) $chaine_mysql_collate NOT NULL,
							`mel` varchar(100) $chaine_mysql_collate NOT NULL,
							`adr_id` varchar(10) $chaine_mysql_collate NOT NULL,
							`statut` varchar(100) $chaine_mysql_collate NOT NULL,
						PRIMARY KEY  (`pers_id`));";
					info_debug($sql);
					$create_table = mysql_query($sql);

					$sql="TRUNCATE TABLE temp_resp_pers_import;";
					//$sql="TRUNCATE TABLE resp_pers;";
					info_debug($sql);
					$vide_table = mysql_query($sql);

					flush();

					echo "<p>Analyse du fichier pour extraire les informations de la section PERSONNES...<br />\n";

					$personnes=array();
					$temoin_personnes=0;
					$temoin_pers=0;

					$tab_champs_personne=array("NOM",
					"PRENOM",
					"LC_CIVILITE",
					"TEL_PERSONNEL",
					"TEL_PORTABLE",
					"TEL_PROFESSIONNEL",
					"MEL",
					"ACCEPTE_SMS",
					"ADRESSE_ID",
					"CODE_PROFESSION",
					"COMMUNICATION_ADRESSE"
					);

					// PARTIE <PERSONNES>
					// Compteur personnes:
					$i=-1;
					// Compteur de lignes du fichier:
					$cpt=0;
					//while($cpt<count($ligne)){
					$fp=fopen($dest_file,"r");
					if($fp){
						while(!feof($fp)){
							$ligne=fgets($fp,4096);

							//echo htmlentities($ligne[$cpt])."<br />\n";

							//if(strstr($ligne[$cpt],"<PERSONNES>")){
							if(strstr($ligne,"<PERSONNES>")){
								echo "Début de la section PERSONNES à la ligne <span style='color: blue;'>$cpt</span><br />\n";
								flush();
								$temoin_personnes++;
							}
							//if(strstr($ligne[$cpt],"</PERSONNES>")){
							if(strstr($ligne,"</PERSONNES>")){
								echo "Fin de la section PERSONNES à la ligne <span style='color: blue;'>$cpt</span><br />\n";
								flush();
								$temoin_personnes++;
								break;
							}
							if($temoin_personnes==1){
								//if(strstr($ligne[$cpt],"<PERSONNE ")){
								if(strstr($ligne,"<PERSONNE ")){
									$i++;
									$personnes[$i]=array();

									//echo "<p><b>".htmlentities($ligne[$cpt])."</b><br />\n";
									unset($tabtmp);
									//$tabtmp=explode('"',strstr($ligne[$cpt]," PERSONNE_ID="));
									$tabtmp=explode('"',strstr($ligne," PERSONNE_ID="));
									//$personnes[$i]["personne_id"]=trim($tabtmp[1]);
									$personnes[$i]["personne_id"]=traitement_magic_quotes(corriger_caracteres(trim($tabtmp[1])));
									affiche_debug("\$personnes[$i][\"personne_id\"]=".$personnes[$i]["personne_id"]."<br />\n");
									$temoin_pers=1;
								}
								//if(strstr($ligne[$cpt],"</PERSONNE>")){
								if(strstr($ligne,"</PERSONNE>")){
									$temoin_pers=0;
								}
								if($temoin_pers==1){
									for($loop=0;$loop<count($tab_champs_personne);$loop++){
										//if(strstr($ligne[$cpt],"<".$tab_champs_personne[$loop].">")){
										if(strstr($ligne,"<".$tab_champs_personne[$loop].">")){
											$tmpmin=strtolower($tab_champs_personne[$loop]);
											//$personnes[$i]["$tmpmin"]=extr_valeur($ligne[$cpt]);
											//$personnes[$i]["$tmpmin"]=traitement_magic_quotes(corriger_caracteres(extr_valeur($ligne[$cpt])));

											// Suppression des guillemets éventuels
											//$personnes[$i]["$tmpmin"]=traitement_magic_quotes(corriger_caracteres(extr_valeur($ligne)));
											//$personnes[$i]["$tmpmin"]=traitement_magic_quotes(corriger_caracteres(my_ereg_replace('"','',extr_valeur($ligne))));
											$personnes[$i]["$tmpmin"]=traitement_magic_quotes(corriger_caracteres(preg_replace('/"/','',extr_valeur($ligne))));

											affiche_debug("\$personnes[$i][\"$tmpmin\"]=".$personnes[$i]["$tmpmin"]."<br />\n");
											break;
										}
									}
								}
							}
							$cpt++;
						}
						fclose($fp);

						//traitement_magic_quotes(corriger_caracteres())
						$nb_err=0;
						$stat=0;
						$i=0;
						while($i<count($personnes)){
							$sql="INSERT INTO temp_resp_pers_import SET ";
							//$sql="INSERT INTO resp_pers SET ";
							$sql.="pers_id='".$personnes[$i]["personne_id"]."', ";
							$sql.="nom='".$personnes[$i]["nom"]."', ";
							$sql.="prenom='".$personnes[$i]["prenom"]."', ";
							if(isset($personnes[$i]["lc_civilite"])){
								$sql.="civilite='".ucfirst(strtolower($personnes[$i]["lc_civilite"]))."', ";
							}
							if(isset($personnes[$i]["tel_personnel"])){
								$sql.="tel_pers='".$personnes[$i]["tel_personnel"]."', ";
							}
							if(isset($personnes[$i]["tel_portable"])){
								$sql.="tel_port='".$personnes[$i]["tel_portable"]."', ";
							}
							if(isset($personnes[$i]["tel_professionnel"])){
								$sql.="tel_prof='".$personnes[$i]["tel_professionnel"]."', ";
							}
							if(isset($personnes[$i]["mel"])){
								$sql.="mel='".$personnes[$i]["mel"]."', ";
							}
							if(isset($personnes[$i]["adresse_id"])){
								$sql.="adr_id='".$personnes[$i]["adresse_id"]."';";
							}
							else{
								$sql.="adr_id='';";
								// IL FAUDRAIT PEUT-ETRE REMPLIR UN TABLEAU
								// POUR SIGNALER QUE CE RESPONSABLE RISQUE DE POSER PB...
								// ... CEPENDANT, CEUX QUE J'AI REPéRéS ETAIENT resp_legal=0
								// ILS NE DEVRAIENT PAS ETRE DESTINATAIRES DE BULLETINS,...
							}
							affiche_debug("$sql<br />\n");
							info_debug($sql);
							$res_insert=mysql_query($sql);
							if(!$res_insert){
								echo "Erreur lors de la requête $sql<br />\n";
								flush();
								$nb_err++;
							}
							else{
								$stat++;
							}

							$i++;
						}

						/*
						if($nb_err==0) {
							echo "<p>La première phase s'est passée sans erreur.</p>\n";
						}
						elseif($nb_err==1) {
							echo "<p>$nb_err erreur.</p>\n";
						}
						else{
							echo "<p>$nb_err erreurs</p>\n";
						}
						*/

						echo "<p><br /></p>\n";

						if ($nb_err != 0) {
							echo "<p>Lors de l'enregistrement des données PERSONNES, il y a eu $nb_err erreurs. Essayez de trouvez la cause de l'erreur et recommencez la procédure avant de passer à l'étape suivante.</p>\n";
						} else {
							echo "<p>L'importation des personnes (responsables) dans la base GEPI a été effectuée avec succès (".$stat." enregistrements au total).</p>\n";

							echo "<script type='text/javascript'>
	/*
	stop='n';
	if(document.getElementById('stop')){
		if(document.getElementById('stop').checked==true){
			stop='y';
		}
	}
	if(stop=='n'){
		setTimeout(\"document.location.replace('".$_SERVER['PHP_SELF']."?step=11')\",2000);
	}
	*/
	setTimeout(\"test_stop('11')\",3000);
</script>\n";
						}

						//echo "<p>$stat enregistrement(s) ont été inséré(s) dans la table 'temp_resp_pers_import'.</p>\n";
						//echo "<p>$stat enregistrement(s) ont été inséré(s) dans la table 'resp_pers'.</p>\n";

						//echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=11&amp;stop=$stop'>Suite</a></p>\n";
						echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=11&amp;stop=$stop' onClick=\"test_stop_suite('11'); return false;\">Suite</a></p>\n";

						//echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=11&amp;stop=y&amp;ne_pas_proposer_resp_sans_eleve=$ne_pas_proposer_resp_sans_eleve'>Suite</a></p>\n";

						require("../lib/footer.inc.php");
						die();
					}
					else{
						echo "<p>ERREUR: Il n'a pas été possible d'ouvrir le fichier en lecture.</p>\n";

						require("../lib/footer.inc.php");
						die();
					}
				}
			}

			break;
		case "11":
			echo "<h2>Import/mise à jour des responsables</h2>\n";

			info_debug("==============================================");
			info_debug("=============== Phase step $step =================");

			$dest_file="../temp/".$tempdir."/responsables.xml";
			$fp=fopen($dest_file,"r");
			if(!$fp){
				echo "<p>Le XML responsables n'a pas l'air présent dans le dossier temporaire.<br />Auriez-vous sauté une étape???</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
			else{

				//$sql="CREATE TABLE IF NOT EXISTS responsables2 (
				$sql="CREATE TABLE IF NOT EXISTS temp_responsables2_import (
						`ele_id` varchar(10) $chaine_mysql_collate NOT NULL,
						`pers_id` varchar(10) $chaine_mysql_collate NOT NULL,
						`resp_legal` varchar(1) $chaine_mysql_collate NOT NULL,
						`pers_contact` varchar(1) $chaine_mysql_collate NOT NULL
						);";
				info_debug($sql);
				$create_table = mysql_query($sql);

				$sql="TRUNCATE TABLE temp_responsables2_import;";
				//$sql="TRUNCATE TABLE responsables2;";
				info_debug($sql);
				$vide_table = mysql_query($sql);

				/*
				echo "<p>Lecture du fichier Responsables...<br />\n";
				while(!feof($fp)){
					$ligne[]=fgets($fp,4096);
				}
				fclose($fp);
				*/
				flush();

				echo "<p>";
				echo "Analyse du fichier pour extraire les informations de la section RESPONSABLES...<br />\n";

				$responsables=array();
				$temoin_responsables=0;
				$temoin_resp=0;

				$tab_champs_responsable=array("ELEVE_ID",
				"PERSONNE_ID",
				"RESP_LEGAL",
				"CODE_PARENTE",
				"RESP_FINANCIER",
				"PERS_PAIMENT",
				"PERS_CONTACT"
				);

				// PARTIE <RESPONSABLES>
				// Compteur responsables:
				$i=-1;
				// Compteur de lignes du fichier:
				$cpt=0;
				//while($cpt<count($ligne)){
				while(!feof($fp)){
					$ligne=fgets($fp,4096);
					//echo htmlentities($ligne[$cpt])."<br />\n";

					//if(strstr($ligne[$cpt],"<RESPONSABLES>")){
					if(strstr($ligne,"<RESPONSABLES>")){
						echo "Début de la section RESPONSABLES à la ligne <span style='color: blue;'>$cpt</span><br />\n";
						flush();
						$temoin_responsables++;
					}
					//if(strstr($ligne[$cpt],"</RESPONSABLES>")){
					if(strstr($ligne,"</RESPONSABLES>")){
						echo "Fin de la section RESPONSABLES à la ligne <span style='color: blue;'>$cpt</span><br />\n";
						flush();
						$temoin_responsables++;
						break;
					}
					if($temoin_responsables==1){
						//if(strstr($ligne[$cpt],"<RESPONSABLE_ELEVE>")){
						if(strstr($ligne,"<RESPONSABLE_ELEVE>")){
							$i++;
							$responsables[$i]=array();
							$temoin_resp=1;
						}
						//if(strstr($ligne[$cpt],"</RESPONSABLE_ELEVE>")){
						if(strstr($ligne,"</RESPONSABLE_ELEVE>")){
							$temoin_resp=0;
						}
						if($temoin_resp==1){
							for($loop=0;$loop<count($tab_champs_responsable);$loop++){
								//if(strstr($ligne[$cpt],"<".$tab_champs_responsable[$loop].">")){
								if(strstr($ligne,"<".$tab_champs_responsable[$loop].">")){
									$tmpmin=strtolower($tab_champs_responsable[$loop]);
									//$responsables[$i]["$tmpmin"]=extr_valeur($ligne[$cpt]);
									//$responsables[$i]["$tmpmin"]=traitement_magic_quotes(corriger_caracteres(extr_valeur($ligne[$cpt])));

									// Suppression des guillemets éventuels (il ne devrait pas y en avoir là)
									//$responsables[$i]["$tmpmin"]=traitement_magic_quotes(corriger_caracteres(extr_valeur($ligne)));
									//$responsables[$i]["$tmpmin"]=traitement_magic_quotes(corriger_caracteres(my_ereg_replace('"','',extr_valeur($ligne))));
									$responsables[$i]["$tmpmin"]=traitement_magic_quotes(corriger_caracteres(my_ereg_replace('/"/','',extr_valeur($ligne))));

									affiche_debug("\$responsables[$i][\"$tmpmin\"]=".$responsables[$i]["$tmpmin"]."<br />\n");
									break;
								}
							}
						}
					}
					$cpt++;
				}
				fclose($fp);


				$nb_err=0;
				$stat=0;
				$i=0;
				while($i<count($responsables)){
					$sql="INSERT INTO temp_responsables2_import SET ";
					//$sql="INSERT INTO responsables2 SET ";
					$sql.="ele_id='".$responsables[$i]["eleve_id"]."', ";
					$sql.="pers_id='".$responsables[$i]["personne_id"]."', ";
					$sql.="resp_legal='".$responsables[$i]["resp_legal"]."', ";
					$sql.="pers_contact='".$responsables[$i]["pers_contact"]."';";
					affiche_debug("$sql<br />\n");
					info_debug($sql);
					$res_insert=mysql_query($sql);
					if(!$res_insert){
						echo "Erreur lors de la requête $sql<br />\n";
						flush();
						$nb_err++;
					}
					else{
						$stat++;
					}

					$i++;
				}

				echo "<p><br /></p>\n";

				if ($nb_err!=0) {
					echo "<p>Lors de l'enregistrement des données de RESPONSABLES, il y a eu $nb_err erreurs. Essayez de trouvez la cause de l'erreur et recommencez la procédure avant de passer à l'étape suivante.</p>\n";
				}
				else {
					echo "<p>L'importation des relations eleves/responsables dans la base GEPI a été effectuée avec succès (".$stat." enregistrements au total).</p>\n";

					echo "<script type='text/javascript'>
	/*
	stop='n';
	if(document.getElementById('stop')){
		if(document.getElementById('stop').checked==true){
			stop='y';
		}
	}
	if(stop=='n'){
		setTimeout(\"document.location.replace('".$_SERVER['PHP_SELF']."?step=12')\",2000);
	}
	*/
	setTimeout(\"test_stop('12')\",3000);
</script>\n";
				}

				//echo "<p>$stat enregistrement(s) ont été inséré(s) dans la table 'temp_responsables2_import'.</p>\n";

				//echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=12&amp;stop=$stop'>Suite</a></p>\n";
				echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=12&amp;stop=$stop' onClick=\"test_stop_suite('12'); return false;\">Suite</a></p>\n";

				require("../lib/footer.inc.php");
				die();
			}

			break;
		case "12":
			echo "<h2>Import/mise à jour des responsables</h2>\n";

			info_debug("==============================================");
			info_debug("=============== Phase step $step =================");

			$dest_file="../temp/".$tempdir."/responsables.xml";
			$fp=fopen($dest_file,"r");
			if(!$fp){
				echo "<p>Le XML responsables n'a pas l'air présent dans le dossier temporaire.<br />Auriez-vous sauté une étape???</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
			else{

				//$sql="CREATE TABLE IF NOT EXISTS resp_adr (
				/*
				$sql="CREATE TABLE IF NOT EXISTS temp_resp_adr_import (
						`adr_id` varchar(10) NOT NULL,
						`adr1` varchar(100) NOT NULL,
						`adr2` varchar(100) NOT NULL,
						`adr3` varchar(100) NOT NULL,
						`adr4` varchar(100) NOT NULL,
						`cp` varchar(6) NOT NULL,
						`pays` varchar(50) NOT NULL,
						`commune` varchar(50) NOT NULL,
					PRIMARY KEY  (`adr_id`));";
				$create_table = mysql_query($sql);
				*/

				$sql="DROP TABLE IF EXISTS temp_resp_adr_import;";
				info_debug($sql);
				$nettoyage = mysql_query($sql);

				$sql="CREATE TABLE IF NOT EXISTS temp_resp_adr_import (
						`adr_id` varchar(10) $chaine_mysql_collate NOT NULL,
						`adr1` varchar(100) $chaine_mysql_collate NOT NULL,
						`adr2` varchar(100) $chaine_mysql_collate NOT NULL,
						`adr3` varchar(100) $chaine_mysql_collate NOT NULL,
						`adr4` varchar(100) $chaine_mysql_collate NOT NULL,
						`cp` varchar(6) NOT $chaine_mysql_collate NULL,
						`pays` varchar(50) $chaine_mysql_collate NOT NULL,
						`commune` varchar(50) $chaine_mysql_collate NOT NULL,
						`statut` varchar(100) $chaine_mysql_collate NOT NULL,
					PRIMARY KEY  (`adr_id`));";
				info_debug($sql);
				$create_table = mysql_query($sql);

				$sql="TRUNCATE TABLE temp_resp_adr_import;";
				//$sql="TRUNCATE TABLE resp_adr;";
				info_debug($sql);
				$vide_table = mysql_query($sql);

				/*
				echo "<p>Lecture du fichier Responsables...<br />\n";
				while(!feof($fp)){
					$ligne[]=fgets($fp,4096);
				}
				fclose($fp);
				*/
				flush();


				echo "Analyse du fichier pour extraire les informations de la section ADRESSES...<br />\n";

				$adresses=array();
				$temoin_adresses=0;
				$temoin_addr=-1;

				$tab_champs_adresse=array("LIGNE1_ADRESSE",
				"LIGNE2_ADRESSE",
				"LIGNE3_ADRESSE",
				"LIGNE4_ADRESSE",
				"CODE_POSTAL",
				"LL_PAYS",
				"CODE_DEPARTEMENT",
				"LIBELLE_POSTAL",
				"COMMUNE_ETRANGERE"
				);


				// PARTIE <ADRESSES>
				// Compteur adresses:
				$i=-1;
				$temoin_adr=-1;
				// Compteur de lignes du fichier:
				$cpt=0;
				//while($cpt<count($ligne)){
				while(!feof($fp)){
					$ligne=fgets($fp,4096);
					//echo htmlentities($ligne[$cpt])."<br />\n";

					//if(strstr($ligne[$cpt],"<ADRESSES>")){
					if(strstr($ligne,"<ADRESSES>")){
						echo "Début de la section ADRESSES à la ligne <span style='color: blue;'>$cpt</span><br />\n";
						flush();
						$temoin_adresses++;
					}
					//if(strstr($ligne[$cpt],"</ADRESSES>")){
					if(strstr($ligne,"</ADRESSES>")){
						echo "Fin de la section ADRESSES à la ligne <span style='color: blue;'>$cpt</span><br />\n";
						flush();
						$temoin_adresses++;
						break;
					}
					if($temoin_adresses==1){
						//if(strstr($ligne[$cpt],"<ADRESSE ")){
						if(strstr($ligne,"<ADRESSE ")){
							$i++;
							$adresses[$i]=array();

							//echo "<p><b>".htmlentities($ligne[$cpt])."</b><br />\n";
							unset($tabtmp);
							//$tabtmp=explode('"',strstr($ligne[$cpt]," ADRESSE_ID="));
							$tabtmp=explode('"',strstr($ligne," ADRESSE_ID="));
							//$adresses[$i]["adresse_id"]=trim($tabtmp[1]);
							$adresses[$i]["adresse_id"]=traitement_magic_quotes(corriger_caracteres(trim($tabtmp[1])));
							$temoin_adr=1;
						}
						//if(strstr($ligne[$cpt],"</ADRESSE>")){
						if(strstr($ligne,"</ADRESSE>")){
							$temoin_adr=0;
						}

						if($temoin_adr==1){
							for($loop=0;$loop<count($tab_champs_adresse);$loop++){
								//if(strstr($ligne[$cpt],"<".$tab_champs_adresse[$loop].">")){
								if(strstr($ligne,"<".$tab_champs_adresse[$loop].">")){
									$tmpmin=strtolower($tab_champs_adresse[$loop]);
									//$adresses[$i]["$tmpmin"]=extr_valeur($ligne[$cpt]);
									//$adresses[$i]["$tmpmin"]=traitement_magic_quotes(corriger_caracteres(extr_valeur($ligne[$cpt])));

									// Suppression des guillemets éventuels
									//$adresses[$i]["$tmpmin"]=traitement_magic_quotes(corriger_caracteres(extr_valeur($ligne)));
									//$adresses[$i]["$tmpmin"]=traitement_magic_quotes(corriger_caracteres(my_ereg_replace('"','',extr_valeur($ligne))));
									$adresses[$i]["$tmpmin"]=traitement_magic_quotes(corriger_caracteres(preg_replace('/"/','',extr_valeur($ligne))));

									//echo "\$adresses[$i][\"$tmpmin\"]=".$adresses[$i]["$tmpmin"]."<br />\n";
									break;
								}
							}
						}
					}
					$cpt++;
				}
				fclose($fp);



				$nb_err=0;
				$stat=0;
				$i=0;
				while($i<count($adresses)){
					$sql="INSERT INTO temp_resp_adr_import SET ";
					//$sql="INSERT INTO resp_adr SET ";
					$sql.="adr_id='".$adresses[$i]["adresse_id"]."', ";
					if(isset($adresses[$i]["ligne1_adresse"])){
						$sql.="adr1='".$adresses[$i]["ligne1_adresse"]."', ";
					}
					if(isset($adresses[$i]["ligne2_adresse"])){
						$sql.="adr2='".$adresses[$i]["ligne2_adresse"]."', ";
					}
					if(isset($adresses[$i]["ligne3_adresse"])){
						$sql.="adr3='".$adresses[$i]["ligne3_adresse"]."', ";
					}
					if(isset($adresses[$i]["ligne4_adresse"])){
						$sql.="adr4='".$adresses[$i]["ligne4_adresse"]."', ";
					}
					if(isset($adresses[$i]["code_postal"])){
						$sql.="cp='".$adresses[$i]["code_postal"]."', ";
					}
					if(isset($adresses[$i]["ll_pays"])){
						$sql.="pays='".$adresses[$i]["ll_pays"]."', ";
					}
					if(isset($adresses[$i]["libelle_postal"])){
						$sql.="commune='".$adresses[$i]["libelle_postal"]."', ";
					} elseif(isset($adresses[$i]["commune_etrangere"])) {
						$sql.="commune='".$adresses[$i]["commune_etrangere"]."', ";
					}
					$sql=substr($sql,0,strlen($sql)-2);
					$sql.=";";
					affiche_debug("$sql<br />\n");
					info_debug($sql);
					$res_insert=mysql_query($sql);
					if(!$res_insert){
						echo "Erreur lors de la requête $sql<br />\n";
						flush();
						$nb_err++;
					}
					else{
						$stat++;
					}

					$i++;
				}

				echo "<p><br /></p>\n";

				if ($nb_err != 0) {
					echo "<p>Lors de l'enregistrement des données ADRESSES des responsables, il y a eu $nb_err erreurs. Essayez de trouvez la cause de l'erreur et recommencez la procédure avant de passer à l'étape suivante.</p>\n";
				} else {
					echo "<p>L'importation des adresses de responsables dans la base GEPI a été effectuée avec succès (".$stat." enregistrements au total).</p>\n";

					echo "<script type='text/javascript'>
	/*
	stop='n';
	if(document.getElementById('stop')){
		if(document.getElementById('stop').checked==true){
			stop='y';
		}
	}
	if(stop=='n'){
		setTimeout(\"document.location.replace('".$_SERVER['PHP_SELF']."?step=13')\",2000);
	}
	*/
	setTimeout(\"test_stop('13')\",3000);
</script>\n";
				}
				//echo "<p>$stat enregistrement(s) ont été mis à jour dans la table 'temp_resp_adr_import'.</p>\n";

				//echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=13&amp;stop=$stop'>Suite</a></p>\n";
				echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=13&amp;stop=$stop' onClick=\"test_stop_suite('13'); return false;\">Suite</a></p>\n";

				require("../lib/footer.inc.php");
				die();
			}
			break;
		case "13":
			// On va commencer les comparaisons...
			// - resp_pers
			// - resp_adr en rappelant la liste des personnes auxquelles l'adresse est rattachée...
			//     . enchainer avec une proposition de nettoyage des adresses qui ne sont plus rattachées à personne
			// - responsables2:
			//     . Nouvelles responsabilités
			//     . Responsabilités supprimées

			echo "<h2>Import/mise à jour des responsables</h2>\n";

			info_debug("==============================================");
			info_debug("=============== Phase step $step =================");

			if(file_exists("../temp/".$tempdir."/responsables.xml")) {
				echo "<p>Suppression du fichier responsables.xml... ";
				if(unlink("../temp/".$tempdir."/responsables.xml")){
					echo "réussie.</p>\n";
				}
				else{
					echo "<font color='red'>Echec!</font> Vérifiez les droits d'écriture sur le serveur.</p>\n";
				}
			}

			echo "<h3>Section PERSONNES</h3>\n";

			echo "<form action='".$_SERVER['PHP_SELF']."' name='formulaire' method='post'>\n";
			//==============================
			// AJOUT pour tenir compte de l'automatisation ou non:
			echo "<input type='hidden' name='stop' id='id_form_stop' value='$stop' />\n";
			//==============================


			if(!isset($parcours_diff)){
				info_debug("==================================================");
				info_debug("Avant parcours_diff PERSONNES");
				echo "<p>On va commencer les comparaisons...</p>\n";

				$sql="SELECT COUNT(pers_id) AS nb_pers FROM temp_resp_pers_import;";
				info_debug($sql);
				$res0=mysql_query($sql);
				$lig=mysql_fetch_object($res0);

				$nb_pers=$lig->nb_pers;

				echo "<p>Les ".$nb_pers." personnes responsables vont être parcourus par tranches de 20 à la recherche de différences.</p>\n";

				$nb_parcours=ceil($nb_pers/20);
			}
			$num_tranche=isset($_POST['num_tranche']) ? $_POST['num_tranche'] : 1;
			echo "<input type='hidden' name='nb_parcours' value='$nb_parcours' />\n";



			//echo "<input type='hidden' name='num_tranche'value='$num_tranche' />\n";

			//echo "<p>Parcours de la tranche <b>$num_tranche/$nb_parcours</b>.</p>\n";
			//flush();

			$sql="SELECT pers_id FROM temp_resp_pers_import WHERE statut='' LIMIT 20;";
			//echo "$sql<br />";
			info_debug($sql);
			$res1=mysql_query($sql);
			//echo "mysql_num_rows(\$res1)=".mysql_num_rows($res1)."<br />";

			if(mysql_num_rows($res1)==0) {
				// On a terminé le parcours
				echo "<p>Le parcours des différences concernant les personnes est terminé.</p>\n";
				info_debug("parcours_diff personnes terminé");

				// On stocke dans la table tempo2 la liste des pers_id pour lesquels un changement a eu lieu:
				$sql="TRUNCATE TABLE tempo2;";
				info_debug($sql);
				$res0=mysql_query($sql);

				//=======================================================
				// STOCKAGE DES pers_id DISPARUS DE temp_resp_pers_import
				$sql="insert into tempo2 SELECT rp.pers_id,rp.pers_id FROM resp_pers rp WHERE rp.pers_id NOT IN (SELECT pers_id FROM temp_resp_pers_import);";
				info_debug($sql);
				$insert=mysql_query($sql);
				$sql="UPDATE tempo2 SET col1='pers_id_disparu';";
				info_debug($sql);
				$update=mysql_query($sql);
				//=======================================================

				$sql="SELECT pers_id FROM temp_resp_pers_import WHERE statut='nouveau' OR statut='modif';";
				//echo "$sql<br />";
				info_debug($sql);
				$res2=mysql_query($sql);
				if(mysql_num_rows($res2)>0) {
					while($lig2=mysql_fetch_object($res2)) {
						$sql="INSERT INTO tempo2 SET col1='pers_id', col2='$lig2->pers_id'";
						info_debug($sql);
						$insert=mysql_query($sql);
					}
				}

				info_debug("fin du remplissage de tempo2");

				echo "<input type='hidden' name='step' value='14' />\n";
				//echo "<p><input type='submit' value='Afficher les différences' /></p>\n";
				echo "<p><input type='submit' value=\"Parcourir les différences d'adresses\" /></p>\n";

				echo "<script type='text/javascript'>
	/*
	stop='n';
	if(document.getElementById('stop')){
		if(document.getElementById('stop').checked==true){
			stop='y';
		}
	}
	if(stop=='n'){
		setTimeout(\"document.forms['formulaire'].submit();\",5000);
	}
	*/
	setTimeout(\"test_stop2()\",3000);
</script>\n";

				info_debug("==================================================");
			}
			else {
				info_debug("========================");
				info_debug("parcours de la tranche $num_tranche/$nb_parcours");
				echo "<p>Parcours de la tranche <b>$num_tranche/$nb_parcours</b>.</p>\n";

				echo "<input type='hidden' name='num_tranche'value='$num_tranche' />\n";

				// Afficher les différences déjà trouvées...
				$sql="SELECT COUNT(pers_id) AS nb_nouveau FROM temp_resp_pers_import WHERE statut='nouveau';";
				info_debug($sql);
				$res0=mysql_query($sql);
				$lig=mysql_fetch_object($res0);
				$nb_nouveau=$lig->nb_nouveau;
				if($nb_nouveau!=0) {echo "<p>$nb_nouveau nouveau(x) trouvé(s) auparavant.</p>\n";}

				$sql="SELECT COUNT(pers_id) AS nb_modif FROM temp_resp_pers_import WHERE statut='modif';";
				info_debug($sql);
				$res0=mysql_query($sql);
				$lig=mysql_fetch_object($res0);
				$nb_modif=$lig->nb_modif;
				if($nb_modif!=0) {echo "<p>$nb_modif modification(s) trouvée(s) auparavant.</p>\n";}

				flush();


				echo "<p>Recherche des différences sur la tranche parcourue: ";

				$cpt=0;
				//$chaine_nouveaux="";
				while($lig=mysql_fetch_object($res1)){
					$sql="SELECT 1=1 FROM resp_pers rp, temp_resp_pers_import t WHERE rp.pers_id=t.pers_id AND t.pers_id='$lig->pers_id'";
					info_debug($sql);
					$test=mysql_query($sql);
					info_debug("Test diff $lig->pers_id");
					if(mysql_num_rows($test)==0){
						// On ne va considérer comme nouveau responsable qu'une personne associée à un élève effectivement accepté dans la table 'eleves':
						info_debug("$lig->pers_id semble être un nouveau");
						$sql="SELECT 1=1 FROM temp_resp_pers_import trp,
												temp_responsables2_import tr,
												eleves e
										WHERE trp.pers_id='$lig->pers_id' AND
												trp.pers_id=tr.pers_id AND
												tr.ele_id=e.ele_id";
						//echo "$sql<br />";
						info_debug($sql);
						$test=mysql_query($sql);
						if(mysql_num_rows($test)>0){
							info_debug("$lig->pers_id est bien un nouveau");
							if($cpt>0){
								//$chaine_nouveaux.=", ";
								echo ", ";
							}
							//$chaine_nouveaux.=$lig->pers_id;
							echo "<span style='color:blue;'>".$lig->pers_id."</span>";
							//echo "<input type='hidden' name='tab_pers_id_diff[]' value='$lig->pers_id' />\n";
							$sql="UPDATE temp_resp_pers_import SET statut='nouveau' WHERE pers_id='$lig->pers_id';";
							info_debug($sql);
							//echo "$sql<br />";
							$update=mysql_query($sql);
							$cpt++;
						}
						else {
							info_debug("$lig->pers_id n'est associé à personne");
							// Ce 'nouveau' responsable n'est associé à aucun élève de 'eleves'...
							// Pour ne pas laisser le statut vide (signe qu'on n'a pas encore testé ce pers_id):
							$sql="UPDATE temp_resp_pers_import SET statut='-' WHERE pers_id='$lig->pers_id';";
							info_debug($sql);
							$update=mysql_query($sql);
						}
					}
					else{
						info_debug("$lig->pers_id est déjà dans resp_pers");
						//$tab_pers_id[]=$lig->pers_id;
						//$sql="SELECT rp.pers_id FROM resp_pers rp, temp_resp_pers_import t
						$sql="SELECT 1=1 FROM resp_pers rp, temp_resp_pers_import t
										WHERE rp.pers_id=t.pers_id AND
												(
													rp.nom!=t.nom OR
													rp.prenom!=t.prenom OR
													rp.civilite!=t.civilite OR
													rp.tel_pers!=t.tel_pers OR
													rp.tel_port!=t.tel_port OR
													rp.tel_prof!=t.tel_prof OR
													rp.mel!=t.mel OR
													rp.adr_id!=t.adr_id
												)
												AND rp.pers_id='".$lig->pers_id."';";
						//echo "$sql<br />\n";
						info_debug($sql);
						$test=mysql_query($sql);
						if(mysql_num_rows($test)>0){
							info_debug("... avec une diff au moins dans resp_pers");
							if($cpt>0) {
								echo ", ";
							}

							echo "<span style='color:green;'>".$lig->pers_id."</span>";
							//echo "<input type='hidden' name='tab_pers_id_diff[]' value='$lig->pers_id' />\n";
							$sql="UPDATE temp_resp_pers_import SET statut='modif' WHERE pers_id='$lig->pers_id';";
							info_debug($sql);
							$update=mysql_query($sql);
							$cpt++;
						}
						else {
							info_debug("... sans diff dans resp_pers");
							// Pour ne pas laisser le statut vide (signe qu'on n'a pas encore testé ce pers_id):
							$sql="UPDATE temp_resp_pers_import SET statut='-' WHERE pers_id='$lig->pers_id';";
							info_debug($sql);
							$update=mysql_query($sql);
						}
					}
				}

				$num_tranche++;
				echo "<input type='hidden' name='num_tranche'value='$num_tranche' />\n";

				echo "<input type='hidden' name='parcours_diff' value='$parcours_diff' />\n";
				echo "<input type='hidden' name='step' value='13' />\n";
				echo "<p><input type='submit' value='Suite' /></p>\n";

				echo "<script type='text/javascript'>
	/*
	stop='n';
	if(document.getElementById('stop')){
		if(document.getElementById('stop').checked==true){
			stop='y';
		}
	}
	if(stop=='n'){
		setTimeout(\"document.forms['formulaire'].submit();\",1000);
	}
	*/
	setTimeout(\"test_stop2()\",3000);
</script>\n";

			}


			echo "</form>\n";


			break;

		/*
		// INSERER A CE NIVEAU DES TESTS SUPPLEMENTAIRES
		case "13b":
			// 20090331

			// Remplir une table temporaire avec les membres de resp_pers et chercher s'ils sont toujours dans temp_resp_pers
			// S'ils n'y sont pas, les noter comme 'suppr' ou 'disparu' dans 
			//$sql="UPDATE temp_resp_pers_import SET statut='disparu' WHERE pers_id='$lig->pers_id';";

			//Boucle sur $cpt avec
			//$sql="SELECT pers_id FROM resp_pers LIMIT $cpt,20";
			// Et remplir une table temporaire... puis passer en revue la table temporaire

			// Ou:
			// INSERT INTO tempo3 SELECT pers_id FROM resp_pers;
			// Ou s'il faut plusieurs champs dans tempo3:
			// INSERT INTO tempo3 SELECT pers_id,autre_champ FROM resp_pers;
			// Et si le pers_id n'est pas dans temp_resp_pers, inscrire dans tempo2 pers_id,$pers_id et quand on ne trouve pas le pers_id par la sutie dans temp_resp_pers, c'est qu'on a une suppression... ou stocker plus précisément l'info ailleurs
			// Conserver les infos dans la table tempo3 (vider au fur et à mesure la table tempo3 quand le pers_id est dans temp_resp_pers

			echo "<form action='".$_SERVER['PHP_SELF']."' name='formulaire' method='post'>\n";
			//==============================
			// AJOUT pour tenir compte de l'automatisation ou non:
			echo "<input type='hidden' name='stop' id='id_form_stop' value='$stop' />\n";
			//==============================

			if(!isset($_POST['cpt'])) {
				//$sql="INSERT INTO tempo3 SELECT pers_id,autre_champ FROM resp_pers;";
				$cpt=0;
			}
			else {
				$cpt=$_POST['cpt'];
				//$sql="SELECT "
			}

			$sql="SELECT pers_id FROM resp_pers LIMIT $cpt,100";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)>0) {
				while($lig=mysql_fetch_object($res)) {


				}
			}
			else {
				// FIN DU PARCOURS
			}




			$cpt+=100;
			echo "<input type='hidden' name='cpt' value='$cpt' />\n";

			echo "</form>\n";
			break;
		*/


		case "14":
			// DEBUG:
			//echo "step=$step<br />";
			//debug_var();

			echo "<h2>Import/mise à jour des responsables</h2>\n";

			info_debug("==============================================");
			info_debug("=============== Phase step $step =================");

			echo "<h3>Section ADRESSES</h3>\n";

			echo "<form action='".$_SERVER['PHP_SELF']."' name='formulaire' method='post'>\n";
			//==============================
			// AJOUT pour tenir compte de l'automatisation ou non:
			echo "<input type='hidden' name='stop' id='id_form_stop' value='$stop' />\n";
			//==============================



			if(!isset($parcours_diff)){
				info_debug("=======================================================");
				info_debug("Avant parcours_diff ADRESSES");
				echo "<p>On va commencer les comparaisons...</p>\n";

				$sql="SELECT COUNT(adr_id) AS nb_adr FROM temp_resp_adr_import;";
				info_debug($sql);
				$res0=mysql_query($sql);
				$lig=mysql_fetch_object($res0);

				$nb_adr=$lig->nb_adr;

				echo "<p>Les ".$nb_adr." adresses de personnes responsables vont être parcourues par tranches de 20 à la recherche de différences.</p>\n";

				$nb_parcours=ceil($nb_adr/20);
			}
			$num_tranche=isset($_POST['num_tranche']) ? $_POST['num_tranche'] : 1;
			echo "<input type='hidden' name='nb_parcours' value='$nb_parcours' />\n";



			//echo "<input type='hidden' name='num_tranche'value='$num_tranche' />\n";

			//echo "<p>Parcours de la tranche <b>$num_tranche/$nb_parcours</b>.</p>\n";
			//flush();


			$sql="SELECT DISTINCT adr_id FROM temp_resp_adr_import WHERE statut='' LIMIT 20;";
			info_debug($sql);
			//echo "$sql<br />";
			$res1=mysql_query($sql);
			//echo "mysql_num_rows(\$res1)=".mysql_num_rows($res1)."<br />";

			if(mysql_num_rows($res1)==0) {
				info_debug("Fin parcours_diff adresses");
				// On a terminé le parcours
				echo "<p>Le parcours des différences concernant les personnes est terminé.</p>\n";

				flush();

				$sql="SELECT adr_id FROM temp_resp_adr_import WHERE statut='nouveau' OR statut='modif';";
				info_debug($sql);
				//echo "$sql<br />";
				$res2=mysql_query($sql);
				if(mysql_num_rows($res2)>0) {
					info_debug(mysql_num_rows($res2)." nouvelles adresses ou modifs...");
					while($lig2=mysql_fetch_object($res2)) {

						$sql="SELECT DISTINCT pers_id FROM resp_pers WHERE adr_id='".$lig2->adr_id."';";
						info_debug($sql);
						$test=mysql_query($sql);

						if(mysql_num_rows($test)>0){
							while($lig3=mysql_fetch_object($test)){
								$sql="INSERT INTO tempo2 SET col1='pers_id', col2='".$lig3->pers_id."';";
								info_debug($sql);
								$insert=mysql_query($sql);
								info_debug("Modif adresse $lig2->adr_id pour resp_pers.pers_id=$lig3->pers_id");
							}
						}
						else{
							$sql="SELECT DISTINCT pers_id FROM temp_resp_pers_import WHERE adr_id='".$lig2->adr_id."';";
							info_debug($sql);
							$test=mysql_query($sql);

							if(mysql_num_rows($test)>0){
								while($lig3=mysql_fetch_object($test)){
									$sql="INSERT INTO tempo2 SET col1='pers_id', col2='$lig3->pers_id'";
									info_debug($sql);
									$insert=mysql_query($sql);
									info_debug("Nouvelle adresse $lig2->adr_id pour temp_resp_pers_import.pers_id=$lig3->pers_id");
								}
							}
							// Les doublons importent peu.
							// On fait des recherches en DISTINCT par la suite.
						}
					}
				}



				if($ne_pas_proposer_resp_sans_eleve=="si"){
					//echo "<input type='hidden' name='step' value='15' />\n";
					echo "<input type='hidden' name='step' value='16' />\n";
					echo "<p><input type='submit' value='Afficher les différences' /></p>\n";
				}
				else{
					//echo "<input type='hidden' name='step' value='a15' />\n";
					$sql="SELECT 1=1 FROM tempo2 WHERE col1='pers_id_disparu' LIMIT 1;";
					info_debug($sql);
					$test=mysql_query($sql);
					if (mysql_num_rows($test)>0) {
						echo "<input type='hidden' name='step' value='14b' />\n";
					}
					else {
						echo "<input type='hidden' name='step' value='15' />\n";
					}
					echo "<p><input type='submit' value='Effectuer un nettoyage avant affichage des différences' /></p>\n";
				}

				echo "<script type='text/javascript'>
	/*
	stop='n';
	if(document.getElementById('stop')){
		if(document.getElementById('stop').checked==true){
			stop='y';
		}
	}
	if(stop=='n'){
		setTimeout(\"document.forms['formulaire'].submit();\",5000);
	}
	*/
	setTimeout(\"test_stop2()\",3000);
</script>\n";


				info_debug("==================================================");
			}
			else {
				info_debug("========================");
				echo "<p>Parcours de la tranche <b>$num_tranche/$nb_parcours</b>.</p>\n";
				info_debug("Parcours de la tranche $num_tranche/$nb_parcours");

				echo "<input type='hidden' name='num_tranche'value='$num_tranche' />\n";

				// Afficher les différences déjà trouvées...
				$sql="SELECT COUNT(adr_id) AS nb_nouveau FROM temp_resp_adr_import WHERE statut='nouveau';";
				info_debug($sql);
				$res0=mysql_query($sql);
				$lig=mysql_fetch_object($res0);
				$nb_nouveau=$lig->nb_nouveau;
				if($nb_nouveau!=0) {echo "<p>$nb_nouveau nouveau(x) trouvé(s) auparavant.</p>\n";}

				$sql="SELECT COUNT(adr_id) AS nb_modif FROM temp_resp_adr_import WHERE statut='modif';";
				info_debug($sql);
				$res0=mysql_query($sql);
				$lig=mysql_fetch_object($res0);
				$nb_modif=$lig->nb_modif;
				if($nb_modif!=0) {echo "<p>$nb_modif modification(s) trouvée(s) auparavant.</p>\n";}

				flush();


				echo "<p>Recherche des différences sur la tranche parcourue: ";

				$cpt=0;
				while($lig=mysql_fetch_object($res1)){
					//$time1=time();
					// Est-ce une nouvelle adresse responsable?
					$sql="SELECT 1=1 FROM resp_adr ra WHERE ra.adr_id='$lig->adr_id'";
					info_debug($sql);
					$test1=mysql_query($sql);

					if(mysql_num_rows($test1)==0){
						// L'adresse est nouvelle, mais on n'a pas vérifié à ce stade si elle est bien associée à une personne
						if($cpt>0){
							echo ", ";
						}
						echo "<span style='color:blue;'>".$lig->adr_id."</span>";
						$sql="UPDATE temp_resp_adr_import SET statut='nouveau' WHERE adr_id='$lig->adr_id';";
						//echo "$sql<br />";
						info_debug($sql);
						$update=mysql_query($sql);

						info_debug("Nouvelle adresse adr_id=$lig->adr_id");

						$cpt++;
					}
					else {
						$debug_time=time();
						$sql="SELECT ra.adr_id FROM resp_adr ra, temp_resp_adr_import t
										WHERE ra.adr_id=t.adr_id AND
												(
													ra.adr1!=t.adr1 OR
													ra.adr2!=t.adr2 OR
													ra.adr3!=t.adr3 OR
													ra.adr4!=t.adr4 OR
													ra.cp!=t.cp OR
													ra.commune!=t.commune OR
													ra.pays!=t.pays
												)
												AND ra.adr_id='".$lig->adr_id."';";
						//echo "$sql<br />\n";
						info_debug($sql);
						$test=mysql_query($sql);
						$diff_debug_time=time()-$debug_time;
						info_debug("Test modif adr_id=$lig->adr_id (durée: $diff_debug_time)");
						if(mysql_num_rows($test)>0){
							if($cpt>0){
								echo ", ";
							}
							echo "<span style='color:green;'>".$lig->adr_id."</span>";
							$sql="UPDATE temp_resp_adr_import SET statut='modif' WHERE adr_id='$lig->adr_id';";
							info_debug($sql);
							//echo "$sql<br />";
							$update=mysql_query($sql);
							info_debug("Adresse modifiée adr_id=$lig->adr_id");
							$cpt++;
						}
						else {
							// Pas de différence sur l'adresse
							// Pour ne pas laisser le statut vide (signe qu'on n'a pas encore testé ce pers_id):
							$sql="UPDATE temp_resp_adr_import SET statut='-' WHERE adr_id='$lig->adr_id';";
							info_debug($sql);
							$update=mysql_query($sql);
							info_debug("Adresse adr_id=$lig->adr_id inchangée.");
						}
					}
					flush();
				}

				$num_tranche++;
				echo "<input type='hidden' name='num_tranche'value='$num_tranche' />\n";

				echo "<input type='hidden' name='parcours_diff' value='$parcours_diff' />\n";
				echo "<input type='hidden' name='step' value='14' />\n";
				echo "<p><input type='submit' value='Suite' /></p>\n";

				echo "<script type='text/javascript'>
	/*
	stop='n';
	if(document.getElementById('stop')){
		if(document.getElementById('stop').checked==true){
			stop='y';
		}
	}
	if(stop=='n'){
		setTimeout(\"document.forms['formulaire'].submit();\",1000);
	}
	*/
	setTimeout(\"test_stop2()\",3000);
</script>\n";
			}

			echo "</form>\n";



			break;


		// 20090331
		// INSERER LA LE CONTROLE DES col1=pers_id_disparu DANS tempo2
		case "14b":
			// A l'étape précédente passer à 14b s'il y a des col1=pers_id_disparu  et passer à 15 sinon
			echo "<h2>Import/mise à jour des responsables</h2>\n";

			info_debug("==============================================");
			info_debug("=============== Phase step $step =================");

			echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>\n";
			//==============================
			// AJOUT pour tenir compte de l'automatisation ou non:
			echo "<input type='hidden' name='stop' id='id_form_stop' value='$stop' />\n";
			//echo "<input type='hidden' name='step' value='15' />\n";
			echo "<input type='hidden' name='step' value='14c' />\n";
			//==============================

			$sql="SELECT col2 FROM tempo2 WHERE col1='pers_id_disparu';";
			info_debug($sql);
			$test=mysql_query($sql);
			$nb_disparus=mysql_num_rows($test);

			echo "<p>$nb_disparus responsables présents dans votre table 'resp_pers' ne sont plus présents dans Sconet.<br />Vous allez devoir décider si vous souhaitez conserver ces responsables ou si vous voulez les supprimer de votre base.</p>\n";

			echo "<table class='boireaus' summary='Tableau des responsables disparus de Sconet'>\n";

			$ligne_entete_tableau="<tr>\n";
			$ligne_entete_tableau.="<td style='text-align: center; font-weight: bold;'>Supprimer<br />\n";

			$ligne_entete_tableau.="<a href=\"javascript:modifcase('coche')\">";
			$ligne_entete_tableau.="<img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>";
			$ligne_entete_tableau.=" / ";
			$ligne_entete_tableau.="<a href=\"javascript:modifcase('decoche')\">";
			$ligne_entete_tableau.="<img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
			$ligne_entete_tableau.="</td>\n";

			$ligne_entete_tableau.="<td style='text-align:center; font-weight: bold;'>Statut</td>\n";

			$ligne_entete_tableau.="<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>pers_id</td>\n";
			$ligne_entete_tableau.="<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Nom</td>\n";
			$ligne_entete_tableau.="<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Prénom</td>\n";
			$ligne_entete_tableau.="<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Civilité</td>\n";

			$ligne_entete_tableau.="<td style='text-align:center; font-weight:bold; background-color: #FAFABE;'>Responsable de</td>\n";
			$ligne_entete_tableau.="</tr>\n";

			// Entête du tableau:
			echo $ligne_entete_tableau;

			$alt=1;
			$cpt=0;
			//echo "mysql_num_rows(\$test)=".mysql_num_rows($test)."<br />";
			while($lig1=mysql_fetch_object($test)){
				$pers_id=$lig1->col2;

				$sql="SELECT * FROM resp_pers WHERE pers_id='$pers_id'";
				info_debug($sql);
				$res_pers1=mysql_query($sql);
				if(mysql_num_rows($res_pers1)==0){
					// CA NE DEVRAIT PAS ARRIVER
					echo "<tr style='color:red;'><td colspan='7'>Anomalie: Aucun responsable ne correspond à pers_id=$pers_id</td></tr>\n";
				}
				else{
					$lig_pers1=mysql_fetch_object($res_pers1);

					$nom1=$lig_pers1->nom;
					$prenom1=$lig_pers1->prenom;
					$civilite1=$lig_pers1->civilite;

					$adr_id1=$lig_pers1->adr_id;

					$alt=$alt*(-1);
					$ligne_parent="<tr class='lig$alt'>\n";
	
					$ligne_parent.="<td style='text-align: center;'>\n";
					$ligne_parent.="<input type='checkbox' id='check_".$cpt."' name='valid_pers_id[]' value='$pers_id' />\n";
					$ligne_parent.="<input type='hidden' name='liste_pers_id[]' value='$pers_id' />\n";
					$ligne_parent.="</td>\n";

					$ligne_parent.="<td>Disparu</td>\n";
	
					$ligne_parent.="<td style='text-align:center;'><a href='modify_resp.php?pers_id=$pers_id' target='_blank'>$pers_id</a>";
					$ligne_parent.="</td>\n";
		
					$ligne_parent.="<td>";
					$ligne_parent.=stripslashes($nom1);
					$ligne_parent.="</td>\n";
	
					$ligne_parent.="<td>";
					$ligne_parent.=stripslashes($prenom1);
					$ligne_parent.="</td>\n";

					$ligne_parent.="<td>";
					$ligne_parent.=ucfirst($civilite1);
					$ligne_parent.="</td>\n";

					$ligne_parent.="<td>\n";
					$sql="SELECT e.login, e.nom, e.prenom, r.resp_legal FROM eleves e, responsables2 r WHERE r.pers_id='$pers_id' AND r.ele_id=e.ele_id ORDER BY e.prenom;";
					info_debug($sql);
					//$ligne_parent.="$sql<br />";
					$res_ele=mysql_query($sql);
					if(mysql_num_rows($res_ele)==0) {
						//$ligne_parent.="&nbsp;\n";
						$ligne_parent.="<span style='color:red;'>X</span>\n";
					}
					else {
						$cpt_tmp=0;
						while($lig2=mysql_fetch_object($res_ele)){
							if($cpt_tmp>0) {$ligne_parent.="<br />\n";}
							$tmp_classes=get_class_from_ele_login($lig2->login);
							if(isset($tmp_classes['liste'])) {
								$info_classe=$tmp_classes['liste'];
							}
							else {
								$info_classe='Aucune classe';
							}
							if($lig2->resp_legal==0) {$ligne_parent.="<span style='font-size:x-small;'>";}
							$ligne_parent.="$lig2->nom $lig2->prenom (".$info_classe.")";
							if($lig2->resp_legal==0) {$ligne_parent.="</span>";}
							$cpt_tmp++;
						}
					}
					$ligne_parent.="</td>\n";
	
					$ligne_parent.="</tr>\n";

					echo $ligne_parent;

				}

				$cpt++;
			}

			echo $ligne_entete_tableau;

			echo "</table>\n";

			echo "<script type='text/javascript'>
	function modifcase(mode){
		for(i=0;i<$cpt;i++){
			if(document.getElementById('check_'+i)){
				if(mode=='coche'){
					document.getElementById('check_'+i).checked=true;
				}
				else{
					document.getElementById('check_'+i).checked=false;
				}
			}
		}
	}
</script>\n";

			echo "<p><input type='submit' value='Supprimer les personnes cochées et passer à la suite' /></p>\n";

			echo "</form>\n";
			break;

		case "14c":
			// 20090401
			// EFFECTUER LES SUPPRESSIONS COCHEES EN SUPRIMANT DANS resp_pers et responsables2

			//debug_var();

			info_debug("==============================================");
			info_debug("=============== Phase step $step =================");

			$valid_pers_id=isset($_POST['valid_pers_id']) ? $_POST['valid_pers_id'] : NULL;

			if(is_array($valid_pers_id)) {

				for($i=0;$i<count($valid_pers_id);$i++) {
					$sql="SELECT nom, prenom, civilite FROM resp_pers WHERE pers_id='".$valid_pers_id[$i]."';";
					info_debug($sql);
					$res=mysql_query($sql);
					if(mysql_num_rows($res)==0) {
						echo "<p style='color:red;'>Le responsable n°".$valid_pers_id[$i]." n'existe pas.</p>\n";
					}
					else {
						$lig=mysql_fetch_object($res);
						echo "<p>Suppression du responsable n°".$valid_pers_id[$i].": $lig->civilite ".strtoupper($lig->nom)." ".ucfirst(strtolower($lig->prenom)).":<br />\n";
						// Supprimer les responsabilités
						echo "Suppression des responsabilités: ";
						$sql="DELETE FROM responsables2 WHERE pers_id='".$valid_pers_id[$i]."';";
						info_debug($sql);
						//echo "$sql<br />\n";
						if(mysql_query($sql)) {echo "<span style='color:green;'>OK</span>";} else {echo "<span style='color:red;'>ERREUR</span>";}

						echo "<br />\n";

						// Supprimer la personne
						echo "Suppression de la personne de la base: ";
						$sql="DELETE FROM resp_pers WHERE pers_id='".$valid_pers_id[$i]."';";
						info_debug($sql);
						//echo "$sql<br />\n";
						if(mysql_query($sql)) {echo "<span style='color:green;'>OK</span>";} else {echo "<span style='color:red;'>ERREUR</span>";}

						echo "</p>\n";
					}
				}

			}
			else {
				echo "<p>Aucune suppression n'a été répercutée dans la base.</p>\n";
			}

			echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>\n";
			//==============================
			// AJOUT pour tenir compte de l'automatisation ou non:
			echo "<input type='hidden' name='stop' id='id_form_stop' value='$stop' />\n";
			//echo "<input type='hidden' name='step' value='15' />\n";
			echo "<input type='hidden' name='step' value='15' />\n";
			//==============================
			echo "<p><input type='submit' value='Suite' /></p>\n";
			echo "</form>\n";

			break;

		case "15":
			echo "<h2>Import/mise à jour des responsables</h2>\n";

			info_debug("==============================================");
			info_debug("=============== Phase step $step =================");

			echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>\n";
			//==============================
			// AJOUT pour tenir compte de l'automatisation ou non:
			echo "<input type='hidden' name='stop' id='id_form_stop' value='$stop' />\n";
			//echo "<input type='hidden' name='step' value='15' />\n";
			echo "<input type='hidden' name='step' value='16' />\n";
			//==============================

			$sql="SELECT col2 FROM tempo2 WHERE col1='pers_id';";
			info_debug($sql);
			$test=mysql_query($sql);

			//echo "<p>mysql_num_rows(\$test)=".mysql_num_rows($test)."</p>\n";
			echo "<p>Les ".mysql_num_rows($test)." personnes vont être contrôlées pour s'assurer qu'elles sont bien associées à des élèves.</p>\n";

			echo "<p>Suppression des responsables fantômes de la table temporaire: ";
			echo "<span style='font-size:xx-small;'>";
			$cpt=0;
			while($lig=mysql_fetch_object($test)){
				//$sql="SELECT 1=1 FROM temp_resp_pers_import trp,
				$debug_time=time();
				$sql="SELECT trp.nom,trp.prenom FROM temp_resp_pers_import trp,
										temp_responsables2_import tr,
										eleves e
								WHERE trp.pers_id='$lig->col2' AND
										trp.pers_id=tr.pers_id AND
										tr.ele_id=e.ele_id";
				info_debug($sql);
				$test2=mysql_query($sql);
				$diff_debug_time=time()-$debug_time;
				info_debug("Contrôle de pers_id=$lig->col2 (durée: $diff_debug_time)");

				if(mysql_num_rows($test2)==0){
					if($cpt>0){echo ", ";}
					//$liste_resp_sans_eleve.="'$pers_id'";
					echo $lig->col2;

					//echo " (<span style='font-size:xx-small;'>$cpt</span>)";

					$sql="DELETE FROM tempo2 WHERE col1='pers_id' AND col2='$lig->col2';";
					info_debug($sql);
					$suppr=mysql_query($sql);

					// On supprime aussi les entrées dans la table temporaire jointure ele_id/pers_id
					$sql="DELETE FROM temp_responsables2_import WHERE pers_id='$lig->col2';";
					info_debug($sql);
					$suppr=mysql_query($sql);

					$cpt++;
					flush();
				}
			}
			echo "</span>\n";
			echo "</p>\n";

			echo "<p>$cpt fantôme(s) supprimé(s) de la table temporaire.</p>\n";

			$sql="SELECT DISTINCT col2 FROM tempo2 WHERE col1='pers_id';";
			info_debug($sql);
			//echo "$sql<br />";
			$test=mysql_query($sql);
			$nb_tmp_modif=mysql_num_rows($test);
			echo "<p>Parcourir les différences par tranches de <input type='text' name='eff_tranche' value='20' size='3' /> sur un total de $nb_tmp_modif.<br />\n";

			echo "<input type='submit' value='Afficher les différences' /></p>\n";

			echo "<p><input type='checkbox' name='ne_pas_proposer_redoublonnage_adresse' id='ne_pas_proposer_redoublonnage_adresse' value='y' checked='true' /><label for='ne_pas_proposer_redoublonnage_adresse' style='cursor:pointer;'> Ne pas proposer de rétablir des doublons d'adresses identiques avec identifiant différent pour des parents qui conservent la même adresse.</label></p>\n";

			echo "</form>\n";

			break;

		//case 15:
		case "16":
			echo "<h2>Import/mise à jour des responsables</h2>\n";

			info_debug("==============================================");
			info_debug("=============== Phase step $step =================");

			$eff_tranche=isset($_POST['eff_tranche']) ? $_POST['eff_tranche'] : 20;
			if(my_ereg("[^0-9]",$eff_tranche)) {$eff_tranche=20;}

			$ne_pas_proposer_redoublonnage_adresse=isset($_POST['ne_pas_proposer_redoublonnage_adresse']) ? $_POST['ne_pas_proposer_redoublonnage_adresse'] : "n";

			echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>\n";
			//==============================
			// AJOUT pour tenir compte de l'automatisation ou non:
			echo "<input type='hidden' name='stop' id='id_form_stop' value='$stop' />\n";
			//==============================
			echo "<input type='hidden' name='ne_pas_proposer_redoublonnage_adresse' value='$ne_pas_proposer_redoublonnage_adresse' />\n";
			echo "<input type='hidden' name='eff_tranche' value='$eff_tranche' />\n";

			if(!isset($parcours_diff)){
				info_debug("========================================================");
				//$sql="SELECT 1=1 FROM tempo2 WHERE col1='pers_id';";
				$sql="SELECT DISTINCT col2 FROM tempo2 WHERE col1='pers_id';";
				info_debug($sql);
				//echo "$sql<br />";
				$test=mysql_query($sql);
				//echo "mysql_num_rows(\$test)=".mysql_num_rows($test)."<br />";

				//echo "<p>".count($tab_pers_id_diff)." personnes...</p>\n";

				//echo "<p>".mysql_num_rows($test)." personnes/adresses modifiées requièrent votre attention.</p>\n";
				$nb_tmp_modif=mysql_num_rows($test);
				if($nb_tmp_modif==0){
					echo "<p>Aucune modification ne requiert votre attention (<i>personnes/adresses</i>).</p>\n";
				}
				elseif($nb_tmp_modif==1){
					echo "<p>Une personne/adresse modifiée requiert votre attention.</p>\n";
				}
				else{
					echo "<p>$nb_tmp_modif personnes/adresses modifiées requièrent votre attention.</p>\n";
				}

				$sql="SELECT DISTINCT col2 FROM tempo2 WHERE col1='pers_id';";
				info_debug($sql);
				//echo "$sql<br />";
				$test2=mysql_query($sql);
				//echo "mysql_num_rows(\$test2)=".mysql_num_rows($test2)."<br />";

				//echo "<input type='hidden' name='total_pers_diff' value='".count($tab_pers_id_diff)."' />\n";
				echo "<input type='hidden' name='total_pers_diff' value='".mysql_num_rows($test)."' />\n";
			}
			else{
				info_debug("========================");
				info_debug("Enregistrement des validations/refus de la tranche...");
				if(isset($valid_pers_id)){
					// On modifie la valeur de col1 pour les pers_id confirmés pour ne pas les re-parcourir:
					for($i=0;$i<count($valid_pers_id);$i++){
						$sql="UPDATE tempo2 SET col1='pers_id_confirm' WHERE col2='$valid_pers_id[$i]';";
						info_debug($sql);
						$update=mysql_query($sql);
					}

					for($i=0;$i<count($liste_pers_id);$i++){
						if(!in_array($liste_pers_id[$i],$valid_pers_id)){
							$sql="UPDATE tempo2 SET col1='pers_id_refus' WHERE col2='$liste_pers_id[$i]';";
							info_debug($sql);
							$update=mysql_query($sql);
						}
					}
				}
				else{
					if(isset($liste_pers_id)){
						for($i=0;$i<count($liste_pers_id);$i++){
							$sql="UPDATE tempo2 SET col1='pers_id_refus' WHERE col2='$liste_pers_id[$i]';";
							info_debug($sql);
							$update=mysql_query($sql);
						}
					}
				}
				info_debug("... fin de l'enregistrement des validations/refus de la tranche.");

				//$sql="SELECT 1=1 FROM tempo2 WHERE col1='pers_id';";
				$sql="SELECT DISTINCT col2 FROM tempo2 WHERE col1='pers_id';";
				info_debug($sql);
				$test=mysql_query($sql);

				echo "<p>".mysql_num_rows($test)." personnes/adresses restantes sur un total de $total_pers_diff.</p>\n";
				echo "<input type='hidden' name='total_pers_diff' value='".$total_pers_diff."' />\n";
			}

			echo "<input type='hidden' name='parcours_diff' value='y' />\n";

			// Il faut encore parcourir les changements d'adresses...
			// ... et faire une première tranche de corrections?
			// Ou alors on le fait séparemment...



			//$eff_tranche=20;

			$sql="SELECT DISTINCT col2 FROM tempo2 WHERE col1='pers_id' LIMIT $eff_tranche";
			info_debug($sql);
			$res1=mysql_query($sql);

			if(mysql_num_rows($res1)>0){

				//echo "<p align='center'><input type='submit' value='Poursuivre' /></p>\n";
				echo "<p align='center'><input type='submit' value='Valider' /></p>\n";

				// Affichage du tableau
				//echo "<table border='1'>\n";
				//echo "<table class='majimport'>\n";
				echo "<table class='boireaus'>\n";

				/*
				echo "<tr>\n";
				echo "<td style='text-align: center; font-weight: bold;'>Enregistrer<br />\n";
				echo "<a href=\"javascript:modifcase('coche')\">";
				echo "<img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>";
				echo " / ";
				echo "<a href=\"javascript:modifcase('decoche')\">";
				echo "<img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
				echo "</td>\n";

				echo "<td style='text-align:center;'>&nbsp;</td>\n";

				echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>pers_id</td>\n";
				echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Nom</td>\n";
				echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Prénom</td>\n";
				echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Civilité</td>\n";
				echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Téléphone / mel</td>\n";
				//echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Tel perso</td>\n";
				//echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Tel port</td>\n";
				//echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Tel prof</td>\n";
				//echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Mel</td>\n";

				// Pour l'adresse, on teste si l'adr_id a changé:
				// - si oui on indique le changement en piochant la nouvelle adresse dans temp_resp_adr_import2
				// - sinon on indique 'Identifiant d adresse inchangé'
				echo "<td style='text-align:center; font-weight:bold; background-color: #FAFABE;'>Adresse</td>\n";
				echo "</tr>\n";
				*/

				$ligne_entete_tableau="<tr>\n";
				//$ligne_entete_tableau.="<td style='text-align: center; font-weight: bold;'>Enregistrer<br />\n";
				$ligne_entete_tableau.="<td style='text-align: center; font-weight: bold;'>Modifier<br />\n";
				//$ligne_entete_tableau.="<th style='text-align: center; font-weight: bold;'>Enregistrer<br />\n";

				$ligne_entete_tableau.="<a href=\"javascript:modifcase('coche')\">";
				$ligne_entete_tableau.="<img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>";
				$ligne_entete_tableau.=" / ";
				$ligne_entete_tableau.="<a href=\"javascript:modifcase('decoche')\">";
				$ligne_entete_tableau.="<img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
				$ligne_entete_tableau.="</td>\n";
				//$ligne_entete_tableau.="</th>\n";

				//$ligne_entete_tableau.="<td style='text-align:center; background-color: rgb(150, 200, 240);'>Statut</td>\n";
				$ligne_entete_tableau.="<td style='text-align:center; font-weight: bold;'>Statut</td>\n";
				//$ligne_entete_tableau.="<th style='text-align:center; font-weight: bold;'>Statut</th>\n";

				$ligne_entete_tableau.="<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>pers_id</td>\n";
				$ligne_entete_tableau.="<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Nom</td>\n";
				$ligne_entete_tableau.="<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Prénom</td>\n";
				$ligne_entete_tableau.="<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Civilité</td>\n";
				$ligne_entete_tableau.="<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Téléphone / mel</td>\n";
				//$ligne_entete_tableau.="<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Tel perso</td>\n";
				//$ligne_entete_tableau.="<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Tel port</td>\n";
				//$ligne_entete_tableau.="<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Tel prof</td>\n";
				//$ligne_entete_tableau.="<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Mel</td>\n";

				// Pour l'adresse, on teste si l'adr_id a changé:
				// - si oui on indique le changement en piochant la nouvelle adresse dans temp_resp_adr_import2
				// - sinon on indique 'Identifiant d adresse inchangé'
				$ligne_entete_tableau.="<td style='text-align:center; font-weight:bold; background-color: #FAFABE;'>Adresse</td>\n";
				$ligne_entete_tableau.="</tr>\n";

				$texte_infobulle="<center>La personne n'est associée à aucun élève.</center>";
				$tabdiv_infobulle[]=creer_div_infobulle('nouveau_resp_sans_eleve',"","",$texte_infobulle,"",14,0,'y','y','n','n');

				$liste_resp_sans_eleve="";

				// Entête du tableau:
				echo $ligne_entete_tableau;

				$nb_chgt_adresse_inapproprie_non_affiche=0;

				$alt=1;
				$cpt=0;
				while($lig1=mysql_fetch_object($res1)){
				//for($i=0;$i<count($pers_modif);$i++){
					//$pers_id=$pers_modif[$i];

					// Témoin pour permettre de ne pas afficher la ligne si les adresses de deux responsables associés sont identiques mais avec des adr_id différents dans Sconet alors que la correction (fusion des adr_id) a été effectuée dans Gepi.
					$temoin_chgt_adresse_inapproprie="n";
					// Témoin d'une différence autre que celle ci-dessus
					$temoin_diff_autre="n";
					// Ligne à afficher ou non:
					$ligne_parent="";

					$pers_id=$lig1->col2;

					// Est-ce un nouveau ou une modif?
					$sql="SELECT * FROM resp_pers WHERE pers_id='$pers_id'";
					info_debug($sql);
					$res_pers1=mysql_query($sql);
					$nouveau=0;
					if(mysql_num_rows($res_pers1)==0){
						$nouveau=1;

						$nom1="";
						$prenom1="";
						$civilite1="";
						$tel_pers1="";
						$tel_port1="";
						$tel_prof1="";
						$mel1="";
						$adr_id1="";
					}
					else{
						$lig_pers1=mysql_fetch_object($res_pers1);

						$nom1=$lig_pers1->nom;
						$prenom1=$lig_pers1->prenom;
						$civilite1=$lig_pers1->civilite;
						$tel_pers1=$lig_pers1->tel_pers;
						$tel_port1=$lig_pers1->tel_port;
						$tel_prof1=$lig_pers1->tel_prof;
						$mel1=$lig_pers1->mel;
						$adr_id1=$lig_pers1->adr_id;
					}

					$alt=$alt*(-1);
					$ligne_parent.="<tr class='lig$alt'>\n";

					$ligne_parent.="<td style='text-align: center;'>\n";
					$ligne_parent.="<input type='checkbox' id='check_".$cpt."' name='valid_pers_id[]' value='$pers_id' />\n";
					$ligne_parent.="<input type='hidden' name='liste_pers_id[]' value='$pers_id' />\n";
					$ligne_parent.="</td>\n";

					if($nouveau==0){
						$ligne_parent.="<td class='modif'>Modif</td>\n";
					}
					else{
						$sql="SELECT 1=1 FROM temp_resp_pers_import trp,
												temp_responsables2_import tr,
												eleves e
										WHERE trp.pers_id='$pers_id' AND
												trp.pers_id=tr.pers_id AND
												tr.ele_id=e.ele_id";
						info_debug($sql);
						$test=mysql_query($sql);
						if(mysql_num_rows($test)>0){
							$ligne_parent.="<td class='nouveau'>Nouveau</td>\n";
						}
						else{
							if($liste_resp_sans_eleve!=""){$liste_resp_sans_eleve.=",";}
							//$liste_resp_sans_eleve.="'$pers_id'";
							$liste_resp_sans_eleve.="'$cpt'";
							$ligne_parent.="<td style='background-color:orange;'>";
							$ligne_parent.="<a href='#' onmouseover=\"afficher_div('nouveau_resp_sans_eleve','y',-20,20);\"";
							$ligne_parent.=" onmouseout=\"cacher_div('nouveau_resp_sans_eleve')\" onclick=\"return false;\"";
							$ligne_parent.=">";
							$ligne_parent.="Nouveau<br />(*)";
							$ligne_parent.="</a>";
							$ligne_parent.="</td>\n";
						}
					}

					$ligne_parent.="<td style='text-align:center;'><a href='modify_resp.php?pers_id=$pers_id' target='_blank'>$pers_id</a>";
					//$ligne_parent.="<input type='hidden' name='modif_".$cpt."_pers_id' value='$pers_id' />\n";
					//$ligne_parent.="<input type='text' name='modif_".$cpt."_pers_id' value='$pers_id' />\n";
					$ligne_parent.="</td>\n";


					$sql="SELECT * FROM temp_resp_pers_import WHERE (pers_id='$pers_id')";
					info_debug($sql);
					$res_pers2=mysql_query($sql);
					$lig_pers2=mysql_fetch_object($res_pers2);

					$ligne_parent.="<td";
					if($nouveau==0){
						if(stripslashes($lig_pers2->nom)!=stripslashes($nom1)){
							$ligne_parent.=" class='modif'>";
							if($nom1!=''){
								$ligne_parent.=stripslashes($nom1)." <font color='red'>-&gt;</font>\n";
							}

							$temoin_diff_autre="y";
						}
						else{
							$ligne_parent.=">";
						}
					}
					else{
						$ligne_parent.=">";
					}
					$ligne_parent.=stripslashes($lig_pers2->nom);
					$ligne_parent.="</td>\n";

					$ligne_parent.="<td";
					if($nouveau==0){
						if(stripslashes($lig_pers2->prenom)!=stripslashes($prenom1)){
							$ligne_parent.=" class='modif'>";
							if($prenom1!=''){
								$ligne_parent.=stripslashes($prenom1)." <font color='red'>-&gt;</font>\n";
							}

							$temoin_diff_autre="y";
						}
						else{
							$ligne_parent.=">";
						}
					}
					else{
						$ligne_parent.=">";
					}
					$ligne_parent.=stripslashes($lig_pers2->prenom);
					$ligne_parent.="</td>\n";


					//======================================
					$ligne_parent.="<td";
					if($nouveau==0){
						if(ucfirst(strtolower(stripslashes($lig_pers2->civilite)))!=ucfirst(strtolower(stripslashes($civilite1)))){
							$ligne_parent.=" class='modif'>";
							if($civilite1!=''){
								$ligne_parent.=stripslashes($civilite1)." <font color='red'>-&gt;</font>\n";
							}

							$temoin_diff_autre="y";
						}
						else{
							$ligne_parent.=">";
						}
					}
					else{
						$ligne_parent.=">";
					}
					$ligne_parent.=ucfirst(strtolower(stripslashes($lig_pers2->civilite)));
					$ligne_parent.="</td>\n";
					//======================================


					$ligne_parent.="<td style='text-align:center; padding: 2px;'>";
						$ligne_parent.="<table class='majimport' width='100%'>\n";
						$ligne_parent.="<tr>\n";
						$ligne_parent.="<td style='text-align:center; font-weight:bold;'>Tel</td>\n";
						$ligne_parent.="<td";
						if($nouveau==0){
							if($lig_pers2->tel_pers!=$tel_pers1) {
								if(($lig_pers2->tel_pers!='')||($tel_pers1!='')){
									$ligne_parent.=" class='modif'>";
									if($tel_pers1!=''){
										$ligne_parent.=$tel_pers1." <font color='red'>-&gt;</font>\n";
									}

									$temoin_diff_autre="y";
								}
								else{
									$ligne_parent.=">";
								}
							}
							else{
								$ligne_parent.=">";
							}
						}
						else{
							$ligne_parent.=">";
						}
						$ligne_parent.=$lig_pers2->tel_pers;
						$ligne_parent.="</td>\n";
						$ligne_parent.="</tr>\n";

						$ligne_parent.="<tr>\n";
						$ligne_parent.="<td style='text-align:center; font-weight:bold;'>TPo</td>\n";
						$ligne_parent.="<td";
						if($nouveau==0){
							if($lig_pers2->tel_port!=$tel_port1) {
								if(($lig_pers2->tel_port!='')||($tel_port1!='')){
									$ligne_parent.=" class='modif'>";
									if($tel_port1!=''){
										$ligne_parent.=$tel_port1." <font color='red'>-&gt;</font>\n";
									}

									$temoin_diff_autre="y";
								}
								else{
									$ligne_parent.=">";
								}
							}
							else{
								$ligne_parent.=">";
							}
						}
						else{
							$ligne_parent.=">";
						}
						$ligne_parent.=$lig_pers2->tel_port;
						$ligne_parent.="</td>\n";
						$ligne_parent.="</tr>\n";

						$ligne_parent.="<tr>\n";
						$ligne_parent.="<td style='text-align:center; font-weight:bold;'>TPr</td>\n";
						$ligne_parent.="<td";
						if($nouveau==0){
							if($lig_pers2->tel_prof!=$tel_prof1) {
								if(($lig_pers2->tel_prof!='')||($tel_prof1!='')){
									$ligne_parent.=" class='modif'>";
									if($tel_prof1!=''){
										$ligne_parent.=$tel_prof1." <font color='red'>-&gt;</font>\n";
									}

									$temoin_diff_autre="y";
								}
								else{
									$ligne_parent.=">";
								}
							}
							else{
								$ligne_parent.=">";
							}
						}
						else{
							$ligne_parent.=">";
						}
						$ligne_parent.=$lig_pers2->tel_prof;
						$ligne_parent.="</td>\n";
						$ligne_parent.="</tr>\n";

						$ligne_parent.="<tr>\n";
						$ligne_parent.="<td style='text-align:center; font-weight:bold;'>mel</td>\n";
						$ligne_parent.="<td";
						if($nouveau==0){
							if($lig_pers2->mel!=$mel1) {
								if(($lig_pers2->mel!='')||($mel1!='')){
									$ligne_parent.=" class='modif'>";
									if($mel1!=''){
										$ligne_parent.=$mel1." <font color='red'>-&gt;</font>\n";
									}

									$temoin_diff_autre="y";
								}
								else{
									//$ligne_parent.="'>";
									$ligne_parent.=">";
								}
							}
							else{
								//$ligne_parent.="'>";
								$ligne_parent.=">";
							}
						}
						else{
							//$ligne_parent.="'>";
							$ligne_parent.=">";
						}
						$ligne_parent.=$lig_pers2->mel;
						$ligne_parent.="</td>\n";
						$ligne_parent.="</tr>\n";
						$ligne_parent.="</table>\n";

						//$ligne_parent.="\$lig_pers2->adr_id=$lig_pers2->adr_id";
					$ligne_parent.="</td>\n";



					// Adresse
					$ligne_parent.="<td";

					if($lig_pers2->adr_id!=""){
						$sql="SELECT * FROM temp_resp_adr_import WHERE (adr_id='".$lig_pers2->adr_id."')";
						info_debug($sql);
						$res_adr2=mysql_query($sql);
						if(mysql_num_rows($res_adr2)==0){
							$adr1_2="";
							$adr2_2="";
							$adr3_2="";
							$adr4_2="";
							$cp2="";
							$commune2="";
							$pays2="";
						}
						else{
							$lig_adr2=mysql_fetch_object($res_adr2);

							$adr1_2=$lig_adr2->adr1;
							$adr2_2=$lig_adr2->adr2;
							$adr3_2=$lig_adr2->adr3;
							$adr4_2=$lig_adr2->adr4;
							$cp2=$lig_adr2->cp;
							$commune2=$lig_adr2->commune;
							$pays2=$lig_adr2->pays;
						}
					}
					else{
						$adr1_2="";
						$adr2_2="";
						$adr3_2="";
						$adr4_2="";
						$cp2="";
						$commune2="";
						$pays2="";
					}

					if($nouveau==0){
						if($adr_id1!=""){
							$sql="SELECT * FROM resp_adr WHERE (adr_id='".$adr_id1."')";
							info_debug($sql);
							//$adr_id=$personne[$pers_id]["adr_id"];
							$res_adr1=mysql_query($sql);
							if(mysql_num_rows($res_adr1)==0){
								$adr1_1="";
								$adr2_1="";
								$adr3_1="";
								$adr4_1="";
								$cp1="";
								$commune1="";
								$pays1="";
							}
							else{
								$lig_adr1=mysql_fetch_object($res_adr1);

								$adr1_1=$lig_adr1->adr1;
								$adr2_1=$lig_adr1->adr2;
								$adr3_1=$lig_adr1->adr3;
								$adr4_1=$lig_adr1->adr4;
								$cp1=$lig_adr1->cp;
								$commune1=$lig_adr1->commune;
								$pays1=$lig_adr1->pays;
							}
						}
						else{
							$adr1_1="";
							$adr2_1="";
							$adr3_1="";
							$adr4_1="";
							$cp1="";
							$commune1="";
							$pays1="";
						}

						$chaine_adr1="";
						if(($adr1_1!="")||($adr2_1!="")||($adr3_1!="")||($adr4_1!="")||($cp1!="")||($commune1!="")||($pays1!="")){
							if($adr1_1!=""){
								$chaine_adr1.=stripslashes("$adr1_1, ");
							}
							if($adr2_1!=""){
								$chaine_adr1.=stripslashes("$adr2_1, ");
							}
							if($adr3_1!=""){
								$chaine_adr1.=stripslashes("$adr3_1, ");
							}
							if($adr4_1!=""){
								$chaine_adr1.=stripslashes("$adr4_1, ");
							}
							if($cp1!=""){
								$chaine_adr1.=stripslashes("$cp1, ");
							}
							if($commune1!=""){
								$chaine_adr1.=stripslashes("$commune1, ");
							}
							if($pays1!=""){
								$chaine_adr1.=stripslashes("$pays1");
							}
						}

						$chaine_adr2="";
						if(($adr1_2!="")||($adr2_2!="")||($adr3_2!="")||($adr4_2!="")||($cp2!="")||($commune2!="")||($pays2!="")){
							if($adr1_2!=""){
								$chaine_adr2.=stripslashes("$adr1_2, ");
							}
							if($adr2_2!=""){
								$chaine_adr2.=stripslashes("$adr2_2, ");
							}
							if($adr3_2!=""){
								$chaine_adr2.=stripslashes("$adr3_2, ");
							}
							if($adr4_2!=""){
								$chaine_adr2.=stripslashes("$adr4_2, ");
							}
							if($cp2!=""){
								$chaine_adr2.=stripslashes("$cp2, ");
							}
							if($commune2!=""){
								$chaine_adr2.=stripslashes("$commune2, ");
							}
							if($pays2!=""){
								$chaine_adr2.=stripslashes("$pays2");
							}
						}

						if($chaine_adr1!=$chaine_adr2){
							$ligne_parent.=" class='modif'>";
							$ligne_parent.=$chaine_adr1;
							$ligne_parent.=" <font color='red'>-&gt;</font><br />\n";

							$temoin_diff_autre="y";
						}
						elseif(($adr_id1!="")&&($lig_pers2->adr_id!="")&&($adr_id1!=$lig_pers2->adr_id)) {
							$ligne_parent.=" class='modif'>";

							// Mettre une infobulle pour détailler la situation:
							$titre="Modification adresse";
							$texte="<div style='text-align:center; font-size:small;'>\n";
							$texte.="<b>Adresse actuelle:</b><br />\n";

							$sql="SELECT * FROM resp_pers WHERE adr_id='$adr_id1' AND pers_id!='$pers_id';";
							info_debug($sql);
							$test_adr_id=mysql_query($sql);
							if(mysql_num_rows($test_adr_id)>0) {
								$lig_autre_resp_adr_partagee=mysql_fetch_object($test_adr_id);
								//$texte.="$civilite1 $nom1 $prenom1 partageait l'adresse suivante avec $lig_autre_resp_adr_partagee->civilite $lig_autre_resp_adr_partagee->nom $lig_autre_resp_adr_partagee->prenom:<br />\n";
								$infos_adresse="Partagée avec $lig_autre_resp_adr_partagee->civilite $lig_autre_resp_adr_partagee->nom $lig_autre_resp_adr_partagee->prenom";

								$temoin_chgt_adresse_inapproprie="y";
							}
							else {
								//$texte.="$civilite1 $nom1 $prenom1 avait l'adresse:<br />\n";
								$infos_adresse="Adresse non partagée";
							}
							$texte.="<table class='boireaus' border='1'>
<tr>
	<th>Adr_id</th>
	<th>Adr1</th>
	<th>Adr2</th>
	<th>Adr3</th>
	<th>Adr4</th>
	<th>CP</th>
	<th>Commune</th>
	<th>Pays</th>
	<th>Infos</th>
</tr>
<tr>
	<td>$lig_pers1->adr_id</td>
	<td>$adr1_1</td>
	<td>$adr2_1</td>
	<td>$adr3_1</td>
	<td>$adr4_1</td>
	<td>$cp1</td>
	<td>$commune1</td>
	<td>$pays1</td>
	<td>$infos_adresse</td>
</tr>
</table>";
							$texte.="<br />\n";
							$texte.="<b>Nouvelle adresse:</b><br />\n";

							$sql="SELECT * FROM temp_resp_pers_import WHERE adr_id='$lig_pers2->adr_id' AND pers_id!='$pers_id';";
							info_debug($sql);
							$test_adr_id=mysql_query($sql);
							if(mysql_num_rows($test_adr_id)>0) {
								$lig_autre_resp_adr_partagee=mysql_fetch_object($test_adr_id);
								//$texte.="$civilite1 $nom1 $prenom1 partageait l'adresse suivante avec $lig_autre_resp_adr_partagee->civilite $lig_autre_resp_adr_partagee->nom $lig_autre_resp_adr_partagee->prenom:<br />\n";
								$infos_adresse="Partagée avec $lig_autre_resp_adr_partagee->civilite $lig_autre_resp_adr_partagee->nom $lig_autre_resp_adr_partagee->prenom";

								$temoin_chgt_adresse_inapproprie="y";
							}
							else {
								//$texte.="$civilite1 $nom1 $prenom1 avait l'adresse:<br />\n";
								$infos_adresse="Adresse non partagée";
							}
							$texte.="<table class='boireaus' border='1'>
<tr>
	<th>Adr_id</th>
	<th>Adr1</th>
	<th>Adr2</th>
	<th>Adr3</th>
	<th>Adr4</th>
	<th>CP</th>
	<th>Commune</th>
	<th>Pays</th>
	<th>Infos</th>
</tr>
<tr>
	<td>$lig_pers2->adr_id</td>
	<td>$adr1_2</td>
	<td>$adr2_2</td>
	<td>$adr3_2</td>
	<td>$adr4_2</td>
	<td>$cp2</td>
	<td>$commune2</td>
	<td>$pays2</td>
	<td>$infos_adresse</td>
</tr>
</table>";

							$texte.="</div>\n";

							$tabdiv_infobulle[]=creer_div_infobulle('chgt_adr_'.$cpt,$titre,"",$texte,"",40,0,'y','y','n','n');

							$ligne_parent.="<a href='#' onmouseover=\"afficher_div('chgt_adr_".$cpt."','y',-20,20);\">";
							$ligne_parent.="<img src='../images/info.png' width='29' height='29'  align='middle' border='0' alt='Information' title='Information' />";
							$ligne_parent.="</a> ";

						}
						else {
							$ligne_parent.=">";
						}
						$ligne_parent.=$chaine_adr2;

					}
					else{
						//$ligne_parent.="'>";
						$ligne_parent.=">";
						// Indiquer l'adresse pour cette nouvelle personne responsable

						if(($adr1_2!="")||($adr2_2!="")||($adr3_2!="")||($adr4_2!="")||($cp2!="")||($commune2!="")||($pays2!="")){
							$chaine_adr="";
							if($adr1_2!=""){
								$chaine_adr.=stripslashes("$adr1_2, ");
							}
							if($adr2_2!=""){
								$chaine_adr.=stripslashes("$adr2_2, ");
							}
							if($adr3_2!=""){
								$chaine_adr.=stripslashes("$adr3_2, ");
							}
							if($adr4_2!=""){
								$chaine_adr.=stripslashes("$adr4_2, ");
							}
							if($cp2!=""){
								$chaine_adr.=stripslashes("$cp2, ");
							}
							if($commune2!=""){
								$chaine_adr.=stripslashes("$commune2, ");
							}
							if($pays2!=""){
								$chaine_adr.=stripslashes("$pays2");
							}
							$ligne_parent.=$chaine_adr;
						}
						else{
							$ligne_parent.="<span color='red'>Adresse vide</span>\n";
						}
					}
					$ligne_parent.="</td>\n";


					$ligne_parent.="</tr>\n";


					if($ne_pas_proposer_redoublonnage_adresse=="n") {
						// Si on n'a pas demandé à ne pas afficher les situations de redoublonnage, on affiche la ligne
						echo $ligne_parent;
					}
					else {
						if($temoin_chgt_adresse_inapproprie=="n") {
							// S'il n'y a pas de redoublonnage d'adresse, on affiche la ligne
							echo $ligne_parent;
						}
						elseif($temoin_diff_autre=="y") {
							// Même si un redoublonnage d'adresse est repéré, on affiche la ligne s'il y a d'autres différences
							echo $ligne_parent;
						}
						else {

							echo "<tr style='display:none;'><td colspan='8'>Avant...";
							//echo "<tr><td colspan='8'>Avant...";
							//echo "<input type='hidden' name='valid_pers_id[]' value='$pers_id' />\n";
							echo "<input type='hidden' name='liste_pers_id[]' value='$pers_id' />\n";
							echo "</td></tr>\n";
							//echo $ligne_parent;
							//echo "<tr style='display:none;'><td colspan='8'>... après</td></tr>\n";
							//echo "<tr><td colspan='8'>... après</td></tr>\n";

							$nb_chgt_adresse_inapproprie_non_affiche++;
						}
					}

					$cpt++;
				}

				echo $ligne_entete_tableau;
				echo "</table>\n";

				if($liste_resp_sans_eleve!=""){
					echo "<p>Une ou des personnes apparaissent comme nouvelles, mais ne sont associées à aucun élève (<i>ni dans l'actuelle table 'responsables2', ni dans la table temporaire 'temp_responsables2_import'</i>).<br />Pour ne cocher que les responsables réellement associés à des élèves, cliquez ici: <a href=\"javascript:modifcase2()\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher intelligemment' /></a></p>\n";
				}

				if($nb_chgt_adresse_inapproprie_non_affiche==1) {
					echo "<p>$nb_chgt_adresse_inapproprie_non_affiche personne à cette étape n'a pas été proposée pour un re-doublonnage d'adresse.</p>\n";
				}
				elseif($nb_chgt_adresse_inapproprie_non_affiche>1) {
					echo "<p>$nb_chgt_adresse_inapproprie_non_affiche personnes à cette étape n'ont pas été proposées pour un re-doublonnage d'adresse.</p>\n";
				}

				echo "<script type='text/javascript'>
	function modifcase(mode){
		for(i=0;i<$cpt;i++){
			if(document.getElementById('check_'+i)){
				if(mode=='coche'){
					document.getElementById('check_'+i).checked=true;
				}
				else{
					document.getElementById('check_'+i).checked=false;
				}
			}
		}
	}
";

				if($liste_resp_sans_eleve!=""){
					echo "	function modifcase2(){
		modifcase('coche');

		fauxresp=new Array($liste_resp_sans_eleve);

		for(i=0;i<fauxresp.length;i++){
			if(document.getElementById('check_'+fauxresp[i])){
				document.getElementById('check_'+fauxresp[i]).checked=false;
			}
		}
	}
";
				}

				echo "</script>\n";

				//echo "<input type='hidden' name='step' value='15' />\n";
				echo "<input type='hidden' name='step' value='16' />\n";
				//echo "<p align='center'><input type='submit' value='Poursuivre' /></p>\n";
				echo "<p align='center'><input type='submit' value='Valider' /></p>\n";
			}
			else{
				// On est à la fin on peut passer à step=12 et effectuer les changements confirmés.
				echo "<p>Toutes les différences concernant les personnes ont été parcourues.</p>\n";

				//echo "<input type='hidden' name='step' value='16' />\n";
				echo "<input type='hidden' name='step' value='17' />\n";
				echo "<p><input type='submit' value='Valider les modifications' /></p>\n";
			}

			//echo "<input type='hidden' name='is_posted' value='yes' />\n";
			echo "</form>\n";

			break;
		//case 16:
		case "17":
			echo "<h2>Import/mise à jour des responsables</h2>\n";

			info_debug("==============================================");
			info_debug("=============== Phase step $step =================");

			//echo "<p>On doit parcourir 'tempo2' en recherchant 'pers_id_confirm'.</p>\n";

			$sql="SELECT DISTINCT col2 FROM tempo2 WHERE col1='pers_id_confirm';";
			info_debug($sql);
			$res1=mysql_query($sql);
			if(mysql_num_rows($res1)==0){
				echo "<p>Aucune modification n'a été confirmée/demandée.</p>\n";

				// IL RESTE... les responsabilités
				//echo "<p>Passer à l'étape de <a href='".$_SERVER['PHP_SELF']."?step=17&amp;stop=$stop'>mise à jour des responsabilités</a>.</p>\n";
				echo "<p>Passer à l'étape de <a href='".$_SERVER['PHP_SELF']."?step=18&amp;stop=$stop'>mise à jour des responsabilités</a>.</p>\n";

			}
			else{
				$erreur=0;
				$cpt=0;
				echo "<p>Ajout ou modification de: ";
				while($lig1=mysql_fetch_object($res1)){
					$sql="SELECT DISTINCT t.* FROM temp_resp_pers_import t WHERE t.pers_id='$lig1->col2'";
					info_debug($sql);
					$res=mysql_query($sql);
					if(mysql_num_rows($res)>0){
						$lig=mysql_fetch_object($res);

						if($cpt>0){
							echo ", ";
						}

						$sql="SELECT 1=1 FROM resp_pers WHERE pers_id='$lig1->col2'";
					info_debug($sql);
						$test=mysql_query($sql);

						if(mysql_num_rows($test)==0){
							// prenom='".addslashes(ucfirst(strtolower($lig->prenom)))."',

							$sql="INSERT INTO resp_pers SET pers_id='$lig1->col2',
													nom='".addslashes(strtoupper($lig->nom))."',
													prenom='".addslashes(maj_ini_prenom($lig->prenom))."',
													civilite='".ucfirst(strtolower($lig->civilite))."',
													tel_pers='".$lig->tel_pers."',
													tel_port='".$lig->tel_port."',
													tel_prof='".$lig->tel_prof."',
													mel='".$lig->mel."',
													adr_id='".$lig->adr_id."';";
							info_debug($sql);
							$insert=mysql_query($sql);
							if($insert){
								echo "\n<span style='color:blue;'>";
							}
							else{
								echo "\n<span style='color:red;'>";
								$erreur++;
							}
							echo "$lig->prenom $lig->nom";
							echo "</span>";
						}
						else{
							$sql="UPDATE resp_pers SET nom='".addslashes(strtoupper($lig->nom))."',
													prenom='".addslashes(maj_ini_prenom($lig->prenom))."',
													civilite='".ucfirst(strtolower($lig->civilite))."',
													tel_pers='".$lig->tel_pers."',
													tel_port='".$lig->tel_port."',
													tel_prof='".$lig->tel_prof."',
													mel='".$lig->mel."',
													adr_id='".$lig->adr_id."'
												WHERE pers_id='$lig1->col2';";
							info_debug($sql);
							$update=mysql_query($sql);
							if($update){
								echo "\n<span style='color:darkgreen;'>";
							}
							else{
								echo "\n<span style='color:red;'>";
								$erreur++;
							}
							//echo "$sql<br />\n";
							echo "$lig->prenom $lig->nom";
							echo "</span>";
						}

						if($lig->adr_id!=""){
							// Ajout ou modification validée, on met à jour l'adresse aussi:
							$sql="SELECT DISTINCT t.* FROM temp_resp_adr_import t WHERE t.adr_id='$lig->adr_id'";
							info_debug($sql);
							$res_adr2=mysql_query($sql);
							if(mysql_num_rows($res_adr2)>0){
								$lig_adr2=mysql_fetch_object($res_adr2);

								$adr1_2=$lig_adr2->adr1;
								$adr2_2=$lig_adr2->adr2;
								$adr3_2=$lig_adr2->adr3;
								$adr4_2=$lig_adr2->adr4;
								$cp2=$lig_adr2->cp;
								$commune2=$lig_adr2->commune;
								$pays2=$lig_adr2->pays;


								$sql="SELECT DISTINCT * FROM resp_adr WHERE adr_id='$lig->adr_id'";
								info_debug($sql);
								$res_adr1=mysql_query($sql);
								if(mysql_num_rows($res_adr1)>0){
									$lig_adr1=mysql_fetch_object($res_adr1);

									$adr1_1=$lig_adr1->adr1;
									$adr2_1=$lig_adr1->adr2;
									$adr3_1=$lig_adr1->adr3;
									$adr4_1=$lig_adr1->adr4;
									$cp1=$lig_adr1->cp;
									$commune1=$lig_adr1->commune;
									$pays1=$lig_adr1->pays;

									$sql="UPDATE resp_adr SET adr1='".addslashes($adr1_2)."',
																adr2='".addslashes($adr2_2)."',
																adr3='".addslashes($adr3_2)."',
																adr4='".addslashes($adr4_2)."',
																cp='".addslashes($cp2)."',
																commune='".addslashes($commune2)."',
																pays='".addslashes($pays2)."'
														WHERE adr_id='$lig->adr_id'";
									info_debug($sql);
									$update=mysql_query($sql);
									if(!$update){
										$erreur++;
										echo "<span style='color:red;'>(*)</span>";
									}
								}
								else{
									$adr1_1="";
									$adr2_1="";
									$adr3_1="";
									$adr4_1="";
									$cp1="";
									$commune1="";
									$pays1="";

									$sql="INSERT INTO resp_adr SET adr1='".addslashes($adr1_2)."',
																adr2='".addslashes($adr2_2)."',
																adr3='".addslashes($adr3_2)."',
																adr4='".addslashes($adr4_2)."',
																cp='".addslashes($cp2)."',
																commune='".addslashes($commune2)."',
																pays='".addslashes($pays2)."',
																adr_id='$lig->adr_id'";
									info_debug($sql);
									$insert=mysql_query($sql);
									if(!$insert){
										$erreur++;
										echo "<span style='color:red;'>(*)</span>";
									}

								}
							}
							else{
								// FAUT-IL INSERER UNE LIGNE VIDE dans resp_adr ?

								// On ne devrait pas arriver à cette situation...

							}
						}
					}
					$cpt++;
				}

				echo "<p><br /></p>\n";

				echo "<p><b>Indication:</b> En <span style='color:blue;'>bleu</span>, les personnes ajoutées et en <span style='color:darkgreen;'>vert</span> les personnes/adresses mises à jour.<br />Les <span style='color:red;'>(*)</span> éventuellement présents signalent un souci concernant l'adresse.</p>\n";

				echo "<p><br /></p>\n";

				switch($erreur){
					case 0:
						//echo "<p>Passer à l'étape de <a href='".$_SERVER['PHP_SELF']."?step=17&amp;stop=y'>mise à jour des responsabilités</a>.</p>\n";
						echo "<p>Passer à l'étape de <a href='".$_SERVER['PHP_SELF']."?step=18&amp;stop=$stop'>mise à jour des responsabilités</a>.</p>\n";
						break;
					case 1:
						//echo "<p><font color='red'>Une erreur s'est produite.</font><br />\nVous devriez en chercher la cause avant de passer à l'étape de <a href='".$_SERVER['PHP_SELF']."?step=17&amp;stop=y'>mise à jour des responsabilités</a>.</p>\n";
						echo "<p><font color='red'>Une erreur s'est produite.</font><br />\nVous devriez en chercher la cause avant de passer à l'étape de <a href='".$_SERVER['PHP_SELF']."?step=18&amp;stop=$stop'>mise à jour des responsabilités</a>.</p>\n";
						break;

					default:
						//echo "<p><font color='red'>$erreur erreurs se sont produites.</font><br />\nVous devriez en chercher la cause avant de passer à l'étape de <a href='".$_SERVER['PHP_SELF']."?step=17&amp;stop=y'>mise à jour des responsabilités</a>.</p>\n";
						echo "<p><font color='red'>$erreur erreurs se sont produites.</font><br />\nVous devriez en chercher la cause avant de passer à l'étape de <a href='".$_SERVER['PHP_SELF']."?step=18&amp;stop=$stop'>mise à jour des responsabilités</a>.</p>\n";
						break;
				}
			}

			break;
		//case 17:
		case "18":
			//echo "<h2>Import/mise à jour des responsabilités</h2>\n";

			info_debug("==============================================");
			info_debug("=============== Phase step $step =================");

			echo "<h2>Import/mise à jour des associations responsables/élèves</h2>\n";

			echo "<form action='".$_SERVER['PHP_SELF']."' name='formulaire' method='post'>\n";
			//==============================
			// AJOUT pour tenir compte de l'automatisation ou non:
			echo "<input type='hidden' name='stop' id='id_form_stop' value='$stop' />\n";
			//==============================

			$eff_tranche=20;

			if(!isset($parcours_diff)){

				echo "<p>On va commencer les comparaisons...</p>\n";

				flush();

				$sql="TRUNCATE tempo2;";
				info_debug($sql);
				$res0=mysql_query($sql);

				$sql="select ele_id, pers_id from temp_responsables2_import;";
				info_debug($sql);
				$res1=mysql_query($sql);

				if(mysql_num_rows($res1)==0){
					echo "<p style='color:red;'>Bizarre: La table 'temp_responsables2_import' est vide.<br />Auriez-vous sauté une étape?</p>\n";

					echo "<p><br /></p>\n";
					require("../lib/footer.inc.php");
					die();
				}
				else{
					$tab_resp=array();
					while($lig=mysql_fetch_object($res1)){
						// On ne va considérer un couple valide que si le responsable est une personne associée à un élève effectivement accepté dans la table 'eleves':
						/*
						$sql="SELECT 1=1 FROM temp_resp_pers_import trp,
												temp_responsables2_import tr,
												eleves e
										WHERE trp.pers_id='$lig->pers_id' AND
												trp.pers_id=tr.pers_id AND
												tr.ele_id=e.ele_id";
						*/
						$sql="SELECT 1=1 FROM temp_resp_pers_import trp,
												temp_responsables2_import tr,
												eleves e
										WHERE trp.pers_id='$lig->pers_id' AND
												trp.pers_id=tr.pers_id AND
												tr.ele_id=e.ele_id AND
												e.ele_id='$lig->ele_id'";
						info_debug($sql);
						$test=mysql_query($sql);
						if(mysql_num_rows($test)>0){
							$sql="INSERT INTO tempo2 SET col1='t', col2='t_".$lig->ele_id."_".$lig->pers_id."'";
							info_debug($sql);
							$insert=mysql_query($sql);

							$tab_resp[]="t_".$lig->ele_id."_".$lig->pers_id;
						}
					}
				}

				flush();

				/*
				if($cpt==1){
					echo "<p>L'identifiant ADR_ID d'une nouvelle adresse responsable a été trouvé: $chaine_nouveaux</p>\n";
				}
				elseif($cpt>1){
					echo "<p>Les identifiants ADR_ID de $cpt nouvelles adresses responsables ont été trouvés: $chaine_nouveaux</p>\n";
				}
				*/

				$nb_parcours=ceil(count($tab_resp)/$eff_tranche);
			}
			else{

				echo "<p>Parcours de la tranche <b>$parcours_diff/$nb_parcours</b>.</p>\n";

				if(isset($tab_resp_diff)){
					if(count($tab_resp_diff)==1){
						echo "<p>Le couple ELE_ID/PERS_ID pour lequel une ou des différences ont déjà été repérées, est: \n";
					}
					else{
						echo "<p>Les couples ELE_ID/PERS_ID, pour lesquels une ou des différences ont déjà été repérées, sont: \n";
					}
					$chaine_ele_resp="";
					for($i=0;$i<count($tab_resp_diff);$i++){
						if($i>0){$chaine_ele_resp.=", ";}
						$tab_tmp=explode("_",$tab_resp_diff[$i]);
						$chaine_ele_resp.=$tab_tmp[1]."/".$tab_tmp[2];
						//echo "$i: ";
						echo "<input type='hidden' name='tab_resp_diff[]' value='$tab_resp_diff[$i]' />\n";
						//echo "<br />\n";
					}
					echo $chaine_ele_resp;
					echo "</p>\n";
				}
			}

			echo "<input type='hidden' name='nb_parcours' value='$nb_parcours' />\n";

			//echo "count(\$tab_resp)=".count($tab_resp)."<br />";

			// On construit la chaine des $eff_tranche couples retenus pour la requête à venir:
			//$chaine="";
			$cpt=0;
			for($i=0;$i<min($eff_tranche,count($tab_resp));$i++){
				//if($i>0){$chaine.=" OR ";}

				$tab_tmp=explode("_",$tab_resp[$i]);

				//$chaine.="(t.ele_id='$tab_tmp[1]' AND t.pers_id='$tab_tmp[2]')";

				$sql="SELECT 1=1 FROM responsables2 WHERE ele_id='$tab_tmp[1]' AND pers_id='$tab_tmp[2]';";
				info_debug($sql);
				//echo "$sql<br />";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)==0){
					// C'est une nouvelle responsabilité
					/*
					//$sql="UPDATE tempo2 SET col1='t_new' WHERE col2='t_".$tab_tmp[1]."_".$tab_tmp[2]."'";
					$sql="UPDATE tempo2 SET col1='t_diff' WHERE col2='t_".$tab_tmp[1]."_".$tab_tmp[2]."'";
					$update=mysql_query($sql);
					*/




					if($cpt==0){
						echo "<p>Une ou des différences ont été trouvées dans la tranche étudiée à cette phase.";
						echo "<br />\n";
						echo "En voici le(s) couple(s) ELE_ID/PERS_ID: ";
					}
					else{
						echo ", ";
					}
					echo "<span style='color:red;'>".$tab_tmp[1]."/".$tab_tmp[2]."</span>";

					$cpt++;




					echo "<input type='hidden' name='tab_resp_diff[]' value='t_".$tab_tmp[1]."_".$tab_tmp[2]."' />\n";

					// FAIRE UN echo POUR INDIQUER CES NOUVEAUX RESPONSABLES REPéRéS
					// REMPLIR UNE CHAINE ET L'AJOUTER A LA FIN DE LA LISTE AFFICHéE PLUS BAS
				}
				else{

					$sql="SELECT t.ele_id,t.pers_id FROM responsables2 r, temp_responsables2_import t
									WHERE r.pers_id=t.pers_id AND
											r.ele_id=t.ele_id AND
											(
												r.resp_legal!=t.resp_legal OR
												r.pers_contact!=t.pers_contact
											)
											AND (t.ele_id='$tab_tmp[1]' AND t.pers_id='$tab_tmp[2]')
											";
					info_debug($sql);
					//echo "$sql<br />\n";
					$test=mysql_query($sql);
					if(mysql_num_rows($test)>0){
						if($cpt==0){
							echo "<p>Une ou des différences ont été trouvées dans la tranche étudiée à cette phase.";
							echo "<br />\n";
							echo "En voici le(s) couple(s) ELE_ID/PERS_ID: ";
						}
						else{
							echo ", ";
						}
						$lig=mysql_fetch_object($test);

						echo $lig->ele_id."/".$lig->pers_id;
						echo "<input type='hidden' name='tab_resp_diff[]' value='t_".$lig->ele_id."_".$lig->pers_id."' />\n";
						//echo "<br />\n";
						// Pour le cas où on est dans la dernière tranche:
						$tab_resp_diff[]="t_".$lig->ele_id."_".$lig->pers_id;
						$cpt++;
					}
				}
			}

			//echo "\$chaine=$chaine<br />\n";

			// Liste des couples restant à parcourir:
			for($i=$eff_tranche;$i<count($tab_resp);$i++){
				//echo "$i: ";
				echo "<input type='hidden' name='tab_resp[]' value='$tab_resp[$i]' />\n";
				//echo "<br />\n";
			}


			/*
			$sql="SELECT t.ele_id,t.pers_id FROM responsables2 r, temp_responsables2_import t
							WHERE r.pers_id=t.pers_id AND
									r.ele_id=t.ele_id AND
									(
										r.resp_legal!=t.resp_legal OR
										r.pers_contact!=t.pers_contact
									)
									AND ($chaine)
									";
			//echo "$sql<br />\n";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)>0){
				echo "<p>Une ou des différences ont été trouvées dans la tranche étudiée à cette phase.";
				echo "<br />\n";
				echo "En voici le(s) couple(s) ELE_ID/PERS_ID: ";
				$cpt=0;
				$chaine_ele_resp="";
				while($lig=mysql_fetch_object($test)){
					if($cpt>0){$chaine_ele_resp.=", ";}
					$chaine_ele_resp.=$lig->ele_id."/".$lig->pers_id;
					echo "<input type='hidden' name='tab_resp_diff[]' value='t_".$lig->ele_id."_".$lig->pers_id."' />\n";
					//echo "<br />\n";
					// Pour le cas où on est dans la dernière tranche:
					$tab_resp_diff[]="t_".$lig->ele_id."_".$lig->pers_id;
					$cpt++;
				}
				echo $chaine_ele_resp;
			}
			*/

			if(!isset($parcours_diff)){$parcours_diff=1;}
			$parcours_diff++;
			//echo "<input type='hidden' name='parcours_diff' value='$parcours_diff' />\n";

			//=====================
			// DEBUG
			//echo "count(\$tab_resp)=".count($tab_resp)." et \$eff_tranche=$eff_tranche<br />";
			//=====================

			//if(count($tab_resp)>$eff_tranche){
			if((count($tab_resp)>$eff_tranche)||($cpt>0)) {
				echo "<input type='hidden' name='parcours_diff' value='$parcours_diff' />\n";
				//echo "<input type='hidden' name='step' value='17' />\n";
				echo "<input type='hidden' name='step' value='18' />\n";
				echo "<p><input type='submit' value='Suite' /></p>\n";


				echo "<script type='text/javascript'>
	/*
	stop='n';
	if(document.getElementById('stop')){
		if(document.getElementById('stop').checked==true){
			stop='y';
		}
	}
	if(stop=='n'){
		setTimeout(\"document.forms['formulaire'].submit();\",1000);
	}
	*/
	setTimeout(\"test_stop2()\",3000);
</script>\n";


			}
			else{
				echo "<p>Le parcours des différences concernant les associations élèves/responsables est terminé.<br />Vous allez pouvoir contrôler les différences.</p>\n";
				//echo "<p>La première phase du parcours des différences concernant les associations élève/responsables est terminé.<br />Vous allez pouvoir passer à la deuxième phase avant de contrôler les différences.</p>\n";


				for($i=0;$i<count($tab_resp_diff);$i++){
					$sql="UPDATE tempo2 SET col1='t_diff' WHERE col2='$tab_resp_diff[$i]'";
					info_debug($sql);
					$update=mysql_query($sql);
				}

				//echo "<input type='hidden' name='step' value='18' />\n";
				echo "<input type='hidden' name='step' value='19' />\n";

				$sql="SELECT 1=1 FROM tempo2 WHERE col1='t_diff';";
				info_debug($sql);
				$test=mysql_query($sql);
				$nb_associations_a_consulter=mysql_num_rows($test);
				echo "<p>Parcourir les différences par tranches de <input type='text' name='eff_tranche' value='20' size='3' /> sur un total de $nb_associations_a_consulter.<br />\n";

				echo "Ne pas proposer de supprimer des responsables non associés à des élèves <input type='checkbox' name='suppr_resp_non_assoc' value='n' /><br />\n";
				echo "<input type='submit' value='Afficher les différences' /></p>\n";
/*
				echo "<script type='text/javascript'>
	setTimeout(\"test_stop2()\",3000);
</script>\n";
*/
			}
			echo "</form>\n";


			break;
		//case 18:
		case "19":

			//debug_var();

			echo "<h2>Import/mise à jour des associations responsables/élèves</h2>\n";

			info_debug("==============================================");
			info_debug("=============== Phase step $step =================");

			$eff_tranche=isset($_POST['eff_tranche']) ? $_POST['eff_tranche'] : 20;
			if(my_ereg("[^0-9]",$eff_tranche)) {$eff_tranche=20;}

			$suppr_resp_non_assoc=isset($_POST['suppr_resp_non_assoc']) ? $_POST['suppr_resp_non_assoc'] : 'y';
			

			echo "<form action='".$_SERVER['PHP_SELF']."' name='formulaire' method='post'>\n";
			//==============================
			// AJOUT pour tenir compte de l'automatisation ou non:
			echo "<input type='hidden' name='stop' id='id_form_stop' value='$stop' />\n";
			//==============================
			echo "<input type='hidden' name='eff_tranche' value='$eff_tranche' />\n";
			echo "<input type='hidden' name='suppr_resp_non_assoc' value='$suppr_resp_non_assoc' />\n";

			echo "<input type='hidden' name='temoin_phase_19' value='19' />\n";
			//if(!isset($parcours_diff)){
			if((!isset($parcours_diff))||(!isset($_POST['temoin_phase_19']))) {
				$sql="SELECT 1=1 FROM tempo2 WHERE col1='t_diff';";
				info_debug($sql);
				$test=mysql_query($sql);

				//echo "<p>".count($tab_pers_id_diff)." personnes...</p>\n";
				$nb_associations_a_consulter=mysql_num_rows($test);

				if($nb_associations_a_consulter==0){
					echo "<p>Aucune association ELE_ID/PERS_ID ne requiert votre attention.</p>\n";
				}
				elseif($nb_associations_a_consulter==1){
					echo "<p>".$nb_associations_a_consulter." association ELE_ID/PERS_ID requiert votre attention.</p>\n";
				}
				else{
					echo "<p>".$nb_associations_a_consulter." associations ELE_ID/PERS_ID requièrent votre attention.</p>\n";
				}
				//echo "<input type='hidden' name='total_pers_diff' value='".count($tab_pers_id_diff)."' />\n";
				echo "<input type='hidden' name='total_diff' value='".$nb_associations_a_consulter."' />\n";

			}
			else{
				$modif=isset($_POST['modif']) ? $_POST['modif'] : NULL;
				$new=isset($_POST['new']) ? $_POST['new'] : NULL;

				// A VOIR: IL FAUDRAIT PEUT-ETRE VALIDER LES MODIFS DèS CE NIVEAU...
				// LES TESTS POUR NE PAS AVOIR DEUX resp_legal=1 PEUVENT ETRE PERTURBéS PAR DES ENREGISTREMENTS DIFFéRéS...

				$suppr_resp=isset($_POST['suppr_resp']) ? $_POST['suppr_resp'] : NULL;
				if(isset($suppr_resp)){
					// On modifie la valeur de col1 pour les ele_id/pers_id supprimés pour ne pas les re-parcourir:
					for($i=0;$i<count($suppr_resp);$i++){
						$sql="UPDATE tempo2 SET col1='t_diff_suppr' WHERE col2='$suppr_resp[$i]';";
						info_debug($sql);
						$update=mysql_query($sql);
					}

					$sql="DELETE FROM responsables2 WHERE WHERE pers_id='$suppr_resp[$i]';";
					info_debug($sql);
					$nettoyage=mysql_query($sql);
				}

				if(isset($modif)){
					// On modifie la valeur de col1 pour les ele_id/pers_id confirmés pour ne pas les re-parcourir:
					for($i=0;$i<count($modif);$i++){
						$sql="UPDATE tempo2 SET col1='t_diff_confirm' WHERE col2='$modif[$i]';";
						info_debug($sql);
						$update=mysql_query($sql);
					}

					if(isset($new)){
						// On modifie la valeur de col1 pour les ele_id/pers_id confirmés pour ne pas les re-parcourir:
						for($i=0;$i<count($new);$i++){
							$sql="UPDATE tempo2 SET col1='t_diff_confirm' WHERE col2='$new[$i]';";
							info_debug($sql);
							$update=mysql_query($sql);
						}

						for($i=0;$i<count($liste_assoc);$i++){
							if((!in_array($liste_assoc[$i],$modif))&&(!in_array($liste_assoc[$i],$new))) {
								$sql="UPDATE tempo2 SET col1='t_diff_refus' WHERE col2='$liste_assoc[$i]';";
								info_debug($sql);
								$update=mysql_query($sql);
							}
						}
					}
					else{
						for($i=0;$i<count($liste_assoc);$i++){
							if(!in_array($liste_assoc[$i],$modif)){
								$sql="UPDATE tempo2 SET col1='t_diff_refus' WHERE col2='$liste_assoc[$i]';";
								info_debug($sql);
								$update=mysql_query($sql);
							}
						}
					}
				}
				elseif(isset($new)){
					// On modifie la valeur de col1 pour les ele_id/pers_id confirmés pour ne pas les re-parcourir:
					for($i=0;$i<count($new);$i++){
						$sql="UPDATE tempo2 SET col1='t_diff_confirm' WHERE col2='$new[$i]';";
						info_debug($sql);
						$update=mysql_query($sql);
					}

					for($i=0;$i<count($liste_assoc);$i++){
						if(!in_array($liste_assoc[$i],$new)) {
							$sql="UPDATE tempo2 SET col1='t_diff_refus' WHERE col2='$liste_assoc[$i]';";
							info_debug($sql);
							$update=mysql_query($sql);
						}
					}
				}
				else{
					if(isset($liste_assoc)){
						for($i=0;$i<count($liste_assoc);$i++){
							$sql="UPDATE tempo2 SET col1='t_diff_refus' WHERE col2='$liste_assoc[$i]';";
							info_debug($sql);
							$update=mysql_query($sql);
						}
					}
				}

				// FAIRE LES ENREGISTREMENTS A CE NIVEAU!!!
				if(isset($modif)){
					$compteur_modifs=0;
					for($i=0;$i<count($modif);$i++){
						$tab_tmp=explode("_",$modif[$i]);
						$ele_id=$tab_tmp[1];
						$pers_id=$tab_tmp[2];

						$sql="SELECT * FROM temp_responsables2_import WHERE ele_id='$ele_id' AND pers_id='$pers_id';";
						info_debug($sql);
						$res1=mysql_query($sql);
						if(mysql_num_rows($res1)>0){
							$lig1=mysql_fetch_object($res1);

							$resp_legal=$lig1->resp_legal;
							$pers_contact=$lig1->pers_contact;

							$sql="SELECT * FROM responsables2 WHERE ele_id='$ele_id' AND pers_id='$pers_id';";
							info_debug($sql);
							$test1=mysql_query($sql);
							// Pour une modif, ce test doit toujours être vrai.
							if(mysql_num_rows($test1)>0){
								$sql="DELETE FROM responsables2 WHERE ele_id='$ele_id' AND pers_id='$pers_id';";
								info_debug($sql);
								$suppr=mysql_query($sql);
							}

							// Il ne peut pas y avoir 2 resp_legal 1, ni 2 resp_legal 2 pour un même élève.
							if(($resp_legal==1)||($resp_legal==2)) {
								$sql="SELECT * FROM responsables2 WHERE ele_id='$ele_id' AND resp_legal='$resp_legal';";
								info_debug($sql);
								$test2=mysql_query($sql);

								/*
								if(mysql_num_rows($test2)>0){
									//$lig2=mysql_fetch_object($test2);
									$sql="UPDATE responsables2 SET pers_id='$pers_id',
																	pers_contact='$pers_contact'
															WHERE ele_id='$ele_id' AND
																	resp_legal='$resp_legal';";
									$update=mysql_query($sql);
								}
								else{
									$sql="INSERT INTO responsables2 SET pers_id='$pers_id',
																	pers_contact='$pers_contact',
																	ele_id='$ele_id',
																	resp_legal='$resp_legal';";
									$update=mysql_query($sql);
								}
								*/

								if(mysql_num_rows($test2)>0){
									$sql="DELETE FROM responsables2 WHERE ele_id='$ele_id' AND
																	resp_legal='$resp_legal';";
									info_debug($sql);
									$delete=mysql_query($sql);
								}

								$sql="INSERT INTO responsables2 SET pers_id='$pers_id',
																pers_contact='$pers_contact',
																ele_id='$ele_id',
																resp_legal='$resp_legal';";
								info_debug($sql);
								$insert=mysql_query($sql);
							}
							else{
								// Cas de resp_legal=0
								$sql="INSERT INTO responsables2 SET pers_id='$pers_id',
																pers_contact='$pers_contact',
																ele_id='$ele_id',
																resp_legal='$resp_legal';";
								info_debug($sql);
								$insert=mysql_query($sql);
							}
						}
					}
					//===========================
					// A FAIRE: boireaus 20071115
					// Indiquer combien d'enregistrements viennent d'être effectués.
					//===========================
				}

				if(isset($new)){
					for($i=0;$i<count($new);$i++){
						$tab_tmp=explode("_",$new[$i]);
						$ele_id=$tab_tmp[1];
						$pers_id=$tab_tmp[2];

						$sql="SELECT * FROM temp_responsables2_import WHERE ele_id='$ele_id' AND pers_id='$pers_id';";
						info_debug($sql);
						$res1=mysql_query($sql);
						if(mysql_num_rows($res1)>0){
							$lig1=mysql_fetch_object($res1);

							$resp_legal=$lig1->resp_legal;
							$pers_contact=$lig1->pers_contact;

							$sql="SELECT * FROM responsables2 WHERE ele_id='$ele_id' AND pers_id='$pers_id';";
							info_debug($sql);
							$test1=mysql_query($sql);
							// Pour une 'new', ce test doit toujours être faux.
							if(mysql_num_rows($test1)>0){
								$sql="DELETE FROM responsables2 WHERE ele_id='$ele_id' AND pers_id='$pers_id';";
								info_debug($sql);
								$suppr=mysql_query($sql);
							}

							// Il ne peut pas y avoir 2 resp_legal 1, ni 2 resp_legal 2 pour un même élève.
							if(($resp_legal==1)||($resp_legal==2)) {
								$sql="SELECT * FROM responsables2 WHERE ele_id='$ele_id' AND resp_legal='$resp_legal';";
								info_debug($sql);
								$test2=mysql_query($sql);
								/*
								if(mysql_num_rows($test2)>0){
									//$lig2=mysql_fetch_object($test2);
									$sql="UPDATE responsables2 SET pers_id='$pers_id',
																	pers_contact='$pers_contact'
															WHERE ele_id='$ele_id' AND
																	resp_legal='$resp_legal';";
									$update=mysql_query($sql);
								}
								else{
									$sql="INSERT INTO responsables2 SET pers_id='$pers_id',
																	pers_contact='$pers_contact',
																	ele_id='$ele_id',
																	resp_legal='$resp_legal';";
									$update=mysql_query($sql);
								}
								*/

								if(mysql_num_rows($test2)>0){
									$sql="DELETE FROM responsables2 WHERE ele_id='$ele_id' AND
																	resp_legal='$resp_legal';";
									info_debug($sql);
									$delete=mysql_query($sql);
								}

								$sql="INSERT INTO responsables2 SET pers_id='$pers_id',
																pers_contact='$pers_contact',
																ele_id='$ele_id',
																resp_legal='$resp_legal';";
								info_debug($sql);
								$insert=mysql_query($sql);

							}
							else{
								// Cas de resp_legal=0
								$sql="INSERT INTO responsables2 SET pers_id='$pers_id',
																pers_contact='$pers_contact',
																ele_id='$ele_id',
																resp_legal='$resp_legal';";
								info_debug($sql);
								$update=mysql_query($sql);
							}
						}
					}
					//===========================
					// A FAIRE: boireaus 20071115
					// Indiquer combien d'enregistrements viennent d'être effectués.
					//===========================
				}




				$sql="SELECT 1=1 FROM tempo2 WHERE col1='t_diff';";
				info_debug($sql);
				$test=mysql_query($sql);

				if(mysql_num_rows($test)>0){
					echo "<p>".mysql_num_rows($test)." associations restantes sur un total de $total_diff.</p>\n";
				}
				else{
					echo "<p>Toutes les associations (<i>$total_diff</i>) ont été parcourues.</p>\n";
				}
				echo "<input type='hidden' name='total_diff' value='".$total_diff."' />\n";
			}

			flush();

			echo "<input type='hidden' name='parcours_diff' value='y' />\n";

			//$eff_tranche=20;

			$sql="SELECT col2 FROM tempo2 WHERE col1='t_diff' LIMIT $eff_tranche";
			info_debug($sql);
			//echo "$sql<br />";
			$res0=mysql_query($sql);

			if(mysql_num_rows($res0)>0){

				//echo "<p align='center'><input type='submit' value='Poursuivre' /></p>\n";
				echo "<p align='center'><input type=submit value='Valider' /></p>\n";

				// Affichage du tableau

				//echo "<table border='1'>\n";
				//echo "<table class='majimport'>\n";
				echo "<table class='boireaus'>\n";
				echo "<tr>\n";

				//echo "<td style='text-align: center; font-weight: bold;' rowspan='2'>Enregistrer<br />\n";
				echo "<td style='text-align: center; font-weight: bold;' rowspan='2'>Modifier<br />\n";
				echo "<a href=\"javascript:modifcase('coche')\">";
				echo "<img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>";
				echo " / ";
				echo "<a href=\"javascript:modifcase('decoche')\">";
				echo "<img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
				echo "</td>\n";

				echo "<td rowspan='2'>&nbsp;</td>\n";

				echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);' colspan='5'>Responsable</td>\n";

				echo "<td style='text-align:center; font-weight:bold; background-color: #FAFABE;' colspan='3'>Elève</td>\n";

				//=========================
				// AJOUT: boireaus 20071129
				echo "<td style='text-align:center; font-weight:bold; background-color: red;' rowspan='2'>Suppression<br />du responsable</td>\n";
				//=========================

				echo "</tr>\n";

				echo "<tr>\n";
				echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>pers_id</td>\n";
				echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Nom</td>\n";
				echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Prénom</td>\n";
				echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>resp_legal</td>\n";
				echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>pers_contact</td>\n";

				echo "<td style='text-align:center; font-weight:bold; background-color: #FAFABE;'>Nom</td>\n";
				echo "<td style='text-align:center; font-weight:bold; background-color: #FAFABE;'>Prénom</td>\n";
				echo "<td style='text-align:center; font-weight:bold; background-color: #FAFABE;'>ele_id</td>\n";

				echo "</tr>\n";

				$cpt_nb_lig_tab=0;

				$alt=1;

				$cpt=0;
				$nb_reg_no1=0;
				$nb_record1=0;
				//for($k = 1; ($k < $nblignes+1); $k++){
				while($lig0=mysql_fetch_object($res0)){
					$tab_tmp=explode("_",$lig0->col2);

					$temoin_suppr_resp="n";
					$ligne_courante="";

					$ele_id=$tab_tmp[1];
					$pers_id=$tab_tmp[2];

					$sql="SELECT * FROM temp_responsables2_import WHERE ele_id='$ele_id' AND pers_id='$pers_id'";
					info_debug($sql);
					//echo "$sql<br />";
					$res0b=mysql_query($sql);
					if(mysql_num_rows($res0b)==0){
						// CA NE DOIT PAS ARRIVER
						echo "<tr><td>ANOMALIE! Ce cas ne devrait pas arriver</td></tr>\n";
					}
					else{
						$lig0b=mysql_fetch_object($res0b);

						$resp_legal=$lig0b->resp_legal;
						$pers_contact=$lig0b->pers_contact;
					}


					//echo "<tr>\n";

					//$sql="SELECT * FROM responsables2 WHERE ele_id='$affiche[0]' AND pers_id='$affiche[1]'";
					$sql="SELECT * FROM responsables2 WHERE (ele_id='$ele_id' AND pers_id='$pers_id')";
					info_debug($sql);
					//echo "$sql<br />";
					$res1=mysql_query($sql);
					if(mysql_num_rows($res1)==0){
						// L'association responsable/eleve n'existe pas encore
						$resp_new[]="$ele_id:$pers_id";


						$alt=$alt*(-1);
						/*
						echo "<tr style='background-color:";
						if($alt==1){
							echo "silver";
						}
						else{
							echo "white";
						}
						echo ";'>\n";
						*/
						$ligne_courante.="<tr class='lig$alt'>\n";

						$sql="SELECT nom,prenom FROM resp_pers WHERE (pers_id='$pers_id')";
						info_debug($sql);
						$res2=mysql_query($sql);
						if(mysql_num_rows($res2)==0){
							// Problème: On ne peut pas importer l'association sans que la personne existe.
							// Est-ce que l'étape d'import de la personne a été refusée?
							$ligne_courante.="<td>&nbsp;</td>\n";
							$ligne_courante.="<td>&nbsp;</td>\n";

							$ligne_courante.="<td style='background-color:red;'>&nbsp;</td>\n";
							//$ligne_courante.="<td colspan='5'>Aucune personne associée???</td>\n";
							$ligne_courante.="<td colspan='7'>Aucune personne associée???</td>\n";

							//=========================
							// AJOUT: boireaus 20071129
							//$ligne_courante.="<td style='text-align:center;'><input type='checkbox' name='suppr_resp[]' value='$pers_id' /></td>\n";
							//$ligne_courante.="<td style='text-align:center;'>&nbsp;</td>\n";
							//=========================

						}
						else{
							$lig2=mysql_fetch_object($res2);
							$ligne_courante.="<td style='text-align:center;'>\n";
							//$ligne_courante.="<input type='checkbox' id='check_".$cpt."' name='new[]' value='$cpt' />";

							// Elève(s) associé(s)
							$sql="SELECT nom,prenom FROM eleves WHERE (ele_id='$ele_id')";
							info_debug($sql);
							$res4=mysql_query($sql);
							if(mysql_num_rows($res4)>0){
								$ligne_courante.="<input type='checkbox' id='check_".$cpt."' name='new[]' value='$lig0->col2' />\n";
							}
							$ligne_courante.="<input type='hidden' name='liste_assoc[]' value='$lig0->col2' />\n";
							$ligne_courante.="</td>\n";

							//$ligne_courante.="<td style='text-align:center; background-color: rgb(150, 200, 240);'>Nouveau</td>\n";
							$ligne_courante.="<td class='nouveau'>Nouveau</td>\n";

							$ligne_courante.="<td style='text-align:center;'>\n";
							$ligne_courante.="$pers_id";
							//$ligne_courante.="<input type='hidden' name='new_".$cpt."_pers_id' value='$pers_id' />\n";
							$ligne_courante.="</td>\n";

							$ligne_courante.="<td style='text-align:center;'>\n";
							$ligne_courante.="$lig2->nom";
							//$ligne_courante.="<input type='hidden' name='new_".$cpt."_resp_nom' value=\"$lig2->nom\" />\n";
							$ligne_courante.="</td>\n";

							$ligne_courante.="<td style='text-align:center;'>\n";
							$ligne_courante.="$lig2->prenom";
							//$ligne_courante.="<input type='hidden' name='new_".$cpt."_resp_prenom' value=\"$lig2->prenom\" />\n";
							$ligne_courante.="</td>\n";

							// Existe-t-il déjà un numéro de responsable légal 1 ou 2 correspondant au nouvel arrivant?
							// Il peut y avoir en revanche plus d'un resp_legal=0

							//$ligne_courante.="<td style='text-align:center;";
							$ligne_courante.="<td";
							//$sql="SELECT 1=1 FROM responsables2 WHERE (pers_id!='$pers_id' AND ele_id='$ele_id' AND resp_legal='$resp_legal')";
							$sql="SELECT 1=1 FROM responsables2 WHERE (pers_id!='$pers_id' AND ele_id='$ele_id' AND resp_legal='$resp_legal' AND (resp_legal='1' OR resp_legal='2'))";
							info_debug($sql);
							$res3=mysql_query($sql);
							if(mysql_num_rows($res3)==0){
								//$ligne_courante.="'>\n";
								$ligne_courante.=">\n";
							}
							else{
								//$ligne_courante.=" background-color: lightgreen;'>\n";
								$ligne_courante.=" class='modif'>\n";
							}
							$ligne_courante.="$resp_legal";
							//$ligne_courante.="<input type='hidden' name='new_".$cpt."_resp_legal' value='$resp_legal' />\n";
							$ligne_courante.="</td>\n";

							$ligne_courante.="<td style='text-align:center;'>\n";
							$ligne_courante.="$pers_contact";
							//$ligne_courante.="<input type='hidden' name='new_".$cpt."_pers_contact' value='$pers_contact' />\n";
							$ligne_courante.="</td>\n";

							// Elève(s) associé(s)
							/*
							$sql="SELECT nom,prenom FROM eleves WHERE (ele_id='$ele_id')";
							$res4=mysql_query($sql);
							*/
							if(mysql_num_rows($res4)==0){
								$ligne_courante.="<td style='text-align:center; background-color:red;' colspan='3'>\n";
								$ligne_courante.="Aucun élève pour ele_id=$ele_id ???";
								$ligne_courante.="</td>\n";

								//=========================
								// AJOUT: boireaus 20071129
								//$ligne_courante.="<td style='text-align:center;'><input type='checkbox' name='suppr_resp[]' value='$pers_id' /></td>\n";
								//=========================
							}
							else{
								$lig4=mysql_fetch_object($res4);
								$ligne_courante.="<td style='text-align:center;'>\n";
								$ligne_courante.="$lig4->nom";
								//$ligne_courante.="<input type='hidden' name='new_".$cpt."_ele_nom' value=\"$lig4->nom\" />\n";
								$ligne_courante.="</td>\n";

								$ligne_courante.="<td style='text-align:center;'>\n";
								$ligne_courante.="$lig4->prenom";
								//$ligne_courante.="<input type='hidden' name='new_".$cpt."_ele_prenom' value=\"$lig4->prenom\" />\n";
								$ligne_courante.="</td>\n";

								$ligne_courante.="<td style='text-align:center;'>\n";
								$ligne_courante.="$ele_id";
								//$ligne_courante.="<input type='hidden' name='new_".$cpt."_ele_id' value='$ele_id' />\n";
								$ligne_courante.="</td>\n";

								//=========================
								// AJOUT: boireaus 20071129
								//$ligne_courante.="<td style='text-align:center;'>&nbsp;</td>\n";
								//=========================
							}
						}


						//=========================
						// AJOUT: boireaus 20071129

						// TESTER SI LE RESPONSABLE EST ASSOCIé AVEC UN ELEVE EXISTANT AU MOINS
						$sql="SELECT e.ele_id FROM eleves e, resp_pers rp, temp_responsables2_import r
										WHERE e.ele_id=r.ele_id AND
												r.pers_id=rp.pers_id AND
												rp.pers_id='$pers_id'";
						info_debug($sql);
						$test=mysql_query($sql);
						if(mysql_num_rows($test)>0) {
							//$ligne_courante.="<td style='text-align:center;'>&nbsp;</td>\n";
							$ligne_courante.="<td style='text-align:center;'>";
							//while($lig_tmp_test=mysql_fetch_object($test)){$ligne_courante.="$lig_tmp_test->ele_id - ";}
							$ligne_courante.="&nbsp;\n";
							$ligne_courante.="</td>\n";
						}
						else{
							$ligne_courante.="<td style='text-align:center;'><input type='checkbox' name='suppr_resp[]' value='$pers_id' /></td>\n";
							$temoin_suppr_resp="y";
						}
						//=========================


						$ligne_courante.="</tr>\n";
						$cpt_nb_lig_tab++;
					}
					else{


						$lig1=mysql_fetch_object($res1);
						if((stripslashes($lig1->resp_legal)!=stripslashes($resp_legal))||
						(stripslashes($lig1->pers_contact)!=stripslashes($pers_contact))){
							//$ligne_courante.="temoin<br />";
							// L'un des champs resp_legal ou pers_contact au moins a changé
							//$resp_modif[]="$affiche[0]:$affiche[1]";
							$resp_modif[]="$ele_id:$pers_id";


							$alt=$alt*(-1);
							/*
							$ligne_courante.="<tr style='background-color:";
							if($alt==1){
								$ligne_courante.="silver";
							}
							else{
								$ligne_courante.="white";
							}
							$ligne_courante.=";'>\n";
							*/
							$ligne_courante.="<tr class='lig$alt'>\n";

							$sql="SELECT nom,prenom FROM resp_pers WHERE (pers_id='$pers_id')";
							info_debug($sql);
							$res2=mysql_query($sql);
							if(mysql_num_rows($res2)==0){
								// Problème: On ne peut pas importer l'association sans que la personne existe.
								// Est-ce que l'étape d'import de la personne a été refusée?
								$ligne_courante.="<td>&nbsp;</td>\n";
								$ligne_courante.="<td>&nbsp;</td>\n";

								$ligne_courante.="<td style='background-color:red;'>&nbsp;</td>\n";
								$ligne_courante.="<td colspan='5'>Aucune personne associée???</td>\n";

								//=========================
								// AJOUT: boireaus 20071129
								//$ligne_courante.="<td style='text-align:center;'><input type='checkbox' name='suppr_resp[]' value='$pers_id' /></td>\n";
								//=========================
							}
							else{
								$lig2=mysql_fetch_object($res2);
								$ligne_courante.="<td style='text-align:center;'>\n";
								//$ligne_courante.="<input type='checkbox' id='check_".$cpt."' name='modif[]' value='$cpt' />";

								// Elève(s) associé(s)
								$sql="SELECT nom,prenom FROM eleves WHERE (ele_id='$ele_id')";
								info_debug($sql);
								$res4=mysql_query($sql);
								if(mysql_num_rows($res4)>0){
									$ligne_courante.="<input type='checkbox' id='check_".$cpt."' name='modif[]' value='$lig0->col2' />\n";
								}
								$ligne_courante.="<input type='hidden' name='liste_assoc[]' value='$lig0->col2' />\n";
								$ligne_courante.="</td>\n";

								//$ligne_courante.="<td style='text-align:center; background-color:lightgreen;'>Modif</td>\n";
								$ligne_courante.="<td class='modif'>Modif</td>\n";

								$ligne_courante.="<td style='text-align:center;'>\n";
								$ligne_courante.="$pers_id";
								//$ligne_courante.="<input type='hidden' name='modif_".$cpt."_pers_id' value='$pers_id' />\n";
								$ligne_courante.="</td>\n";

								$ligne_courante.="<td style='text-align:center;'>\n";
								$ligne_courante.="$lig2->nom";
								//$ligne_courante.="<input type='hidden' name='modif_".$cpt."_resp_nom' value=\"".addslashes($lig2->nom)."\" />\n";
								//$ligne_courante.="<input type='hidden' name='modif_".$cpt."_resp_nom' value=\"".$lig2->nom."\" />\n";
								$ligne_courante.="</td>\n";

								$ligne_courante.="<td style='text-align:center;'>\n";
								$ligne_courante.="$lig2->prenom";
								//$ligne_courante.="<input type='hidden' name='modif_".$cpt."_resp_prenom' value=\"".addslashes($lig2->nom)."\" />\n";
								//$ligne_courante.="<input type='hidden' name='modif_".$cpt."_resp_prenom' value=\"".$lig2->prenom."\" />\n";
								$ligne_courante.="</td>\n";

								// Existe-t-il déjà un numéro de responsable légal 1 ou 2 correspondant au nouvel arrivant?

								//$ligne_courante.="<td style='text-align:center;";
								$ligne_courante.="<td";
								//$sql="SELECT 1=1 FROM responsables2 WHERE (pers_id!='$pers_id' AND ele_id='$ele_id' AND resp_legal='$resp_legal')";
								$sql="SELECT 1=1 FROM responsables2 WHERE (pers_id!='$pers_id' AND ele_id='$ele_id' AND resp_legal='$resp_legal' AND (resp_legal='1' OR resp_legal='2'))";
								info_debug($sql);
								$res3=mysql_query($sql);
								if(mysql_num_rows($res3)==0){
									//$ligne_courante.="'>\n";
									$ligne_courante.=">\n";
								}
								else{
									//$ligne_courante.=" background-color: lightgreen;'>\n";
									$ligne_courante.=" class='modif'>\n";
								}
								$ligne_courante.="$resp_legal";
								//$ligne_courante.="<input type='hidden' name='modif_".$cpt."_resp_legal' value='$resp_legal' />\n";
								$ligne_courante.="</td>\n";

								$ligne_courante.="<td style='text-align:center;'>\n";
								$ligne_courante.="$pers_contact";
								//$ligne_courante.="<input type='hidden' name='modif_".$cpt."_pers_contact' value='$pers_contact' />\n";
								$ligne_courante.="</td>\n";

								// Elève(s) associé(s)
								//$sql="SELECT nom,prenom FROM eleves WHERE (ele_id='$ele_id')";
								//$res4=mysql_query($sql);
								if(mysql_num_rows($res4)==0){
									$ligne_courante.="<td style='text-align:center; background-color:red;' colspan='3'>\n";
									$ligne_courante.="Aucun élève pour ele_id=$ele_id ???";
									$ligne_courante.="</td>\n";

									//=========================
									// AJOUT: boireaus 20071129
									//$ligne_courante.="<td style='text-align:center;'><input type='checkbox' name='suppr_resp[]' value='$pers_id' /></td>\n";
									//=========================
								}
								else{
									$lig4=mysql_fetch_object($res4);
									$ligne_courante.="<td style='text-align:center;'>\n";
									$ligne_courante.="$lig4->nom";
									//$ligne_courante.="<input type='hidden' name='modif_".$cpt."_ele_nom' value=\"".addslashes($lig4->nom)."\" />\n";
									//$ligne_courante.="<input type='hidden' name='modif_".$cpt."_ele_nom' value=\"".$lig4->nom."\" />\n";
									$ligne_courante.="</td>\n";

									$ligne_courante.="<td style='text-align:center;'>\n";
									$ligne_courante.="$lig4->prenom";
									//$ligne_courante.="<input type='hidden' name='modif_".$cpt."_ele_prenom' value=\"".addslashes($lig4->prenom)."\" />\n";
									//$ligne_courante.="<input type='hidden' name='modif_".$cpt."_ele_prenom' value=\"".$lig4->prenom."\" />\n";
									$ligne_courante.="</td>\n";

									$ligne_courante.="<td style='text-align:center;'>\n";
									$ligne_courante.="$ele_id";
									//$ligne_courante.="<input type='hidden' name='modif_".$cpt."_ele_id' value='$ele_id' />\n";
									$ligne_courante.="</td>\n";

									//=========================
									// AJOUT: boireaus 20071129
									//$ligne_courante.="<td style='text-align:center;'>&nbsp;</td>\n";
									//=========================
								}

							}

							//=========================
							// AJOUT: boireaus 20071129

							// TESTER SI LE RESPONSABLE EST ASSOCIé AVEC UN ELEVE EXISTANT AU MOINS
							$sql="SELECT e.ele_id FROM eleves e, resp_pers rp, temp_responsables2_import r
											WHERE e.ele_id=r.ele_id AND
													r.pers_id=rp.pers_id AND
													rp.pers_id='$pers_id'";
							info_debug($sql);
							$test=mysql_query($sql);
							if(mysql_num_rows($test)>0) {
								//$ligne_courante.="<td style='text-align:center;'>&nbsp;</td>\n";
								$ligne_courante.="<td style='text-align:center;'>";
								//while($lig_tmp_test=mysql_fetch_object($test)){$ligne_courante.="$lig_tmp_test->ele_id - ";}
								$ligne_courante.="&nbsp;\n";
								$ligne_courante.="</td>\n";
							}
							else{
								$ligne_courante.="<td style='text-align:center;'><input type='checkbox' name='suppr_resp[]' value='$pers_id' /></td>\n";
								$temoin_suppr_resp="y";
							}
							//=========================

							$ligne_courante.="</tr>\n";
							$cpt_nb_lig_tab++;
						}
						// Sinon, il n'est pas nécessaire de refaire l'inscription déjà présente.
						else {
							$sql="UPDATE tempo2 SET col1='t_diff_pas_modif' WHERE col2='t_".$ele_id."_".$pers_id."'";
							info_debug($sql);
							$update=mysql_query($sql);
						}
					}

					if($suppr_resp_non_assoc="y") {
						echo $ligne_courante;
					}
					elseif(($temoin_suppr_resp="n")&&($suppr_resp_non_assoc="n")) {
						echo $ligne_courante;
					}

					//echo "</tr>\n";
					$cpt++;
				}
				echo "</table>\n";

				echo "<script type='text/javascript'>
	function modifcase(mode){
		for(i=0;i<$cpt;i++){
			if(document.getElementById('check_'+i)){
				if(mode=='coche'){
					document.getElementById('check_'+i).checked=true;
				}
				else{
					document.getElementById('check_'+i).checked=false;
				}
			}
		}
	}
</script>\n";

				//echo "<input type='hidden' name='step' value='18' />\n";
				echo "<input type='hidden' name='step' value='19' />\n";

				if($cpt_nb_lig_tab==0) {
					echo "<p>Aucune ligne de différence n'est proposée après contrôle.</p>\n";
				}
				else {
					echo "<p align='center'><input type=submit value='Valider' /></p>\n";
				}

				echo "<p><br /></p>\n";
				echo "<p><i>NOTES:</i></p>\n";
				echo "<ul>\n";
				echo "<li>La case de suppression d'un responsable n'est proposée que s'il n'est associé à aucun élève effectivement présent dans votre table 'eleves'.</li>\n";
				echo "<li>Le message 'Aucun élève pour ele_id=...' signifie que l'import fait référence à un identifiant d'élève qui n'est plus dans l'établissement ou qui était proposé à l'import des élèves et que vous n'avez pas coché.<br />Cela ne signifie pas que le responsable n'est pas associé à autre élève qui lui est bien présent dans votre table 'eleves'.<br />Rien n'est inséré dans la table 'responsables2' pour ces lignes.</li>\n";
				echo "</ul>\n";

			}
			else{
				//echo "<input type='hidden' name='step' value='19' />\n";
				echo "<input type='hidden' name='step' value='20' />\n";
/*
				echo "<p>Nettoyage des tables temporaires: ";
				unset($liste_tab_del);
				$liste_tab_del=array("temp_ele_classe", "temp_gep_import2", "temp_resp_adr_import", "temp_resp_pers_import", "temp_responsables2_import", "tempo2");
				$j=0;
				for($i=0;$i<count($liste_tab_del);$i++){
					if($liste_tab_del[$i]!=""){
						if($j>0){echo ", ";}
						echo $liste_tab_del[$i];
						$sql="TRUNCATE TABLE $liste_tab_del[$i];";
						$nettoyage=mysql_query($sql);
						$j++;
					}
				}
				echo "</p>\n";
*/

				$sql="SELECT r.pers_id,r.ele_id FROM responsables2 r LEFT JOIN eleves e ON e.ele_id=r.ele_id WHERE e.ele_id is NULL;";
				info_debug($sql);
				$test=mysql_query($sql);
				if(mysql_num_rows($test)>0){
					echo "<p>Suppression de responsabilités sans élève.<br />Voici la liste des identifiants de responsables qui étaient associés à des élèves inexistants: \n";
					$cpt_nett=0;
					while($lig_nett=mysql_fetch_object($test)){
						if($cpt_nett>0){echo ", ";}
						echo "<a href='modify_resp.php?pers_id=$lig_nett->pers_id' target='_blank'>".$lig_nett->pers_id."</a>";
						$sql="DELETE FROM responsables2 WHERE pers_id='$lig_nett->pers_id' AND ele_id='$lig_nett->ele_id';";
						info_debug($sql);
						$nettoyage=mysql_query($sql);
						flush();
						$cpt_nett++;
					}
					echo ".</p>\n";
					echo "<p>$cpt_nett associations aberrantes supprimées.</p>\n";
				}


				//echo "<p align='center'><input type=submit value='Terminer' /></p>\n";
				echo "<p>Retour à:</p>\n";
				echo "<ul>\n";
				echo "<li><a href='../accueil.php'>l'accueil</a></li>\n";
				echo "<li><a href='index.php'>l'index Responsables</a></li>\n";
				echo "<li><a href='../eleves/index.php'>l'index Elèves</a></li>\n";
				echo "</ul>\n";
			}

			echo "</form>\n";

			break;
		//case 19:
		case "20":

			info_debug("==============================================");
			info_debug("=============== Phase step $step =================");

			echo "A FAIRE: Lister les associations de responsables2 qui ne sont plus dans temp_resp...<br />Il faudrait faire de même à la fin des recherches sur resp_pers, resp_adr et eleves.";

			break;
		case "21":
			echo "<h2>THE END ?</h2>\n";
			break;
	}
}

/*
echo "<p><i>NOTES:</i></p>\n";
echo "<ul>\n";
echo "<li>\n";
echo "<p>Les noms de fichiers fournis dans les champs de formulaires doivent coïncider avec le nom indiqué ELEVES.CSV, ADRESSES.CSV,...\n";
echo "</p>\n";
echo "</li>\n";
echo "<li>";
echo "<p>Il reste aussi à assurer l'import de l'établissement d'origine avec les fichiers etablissements.csv et eleves_etablissements.csv<br />\n";
echo "Par ailleurs, l'inscription des élèves dans telle ou telle classe, avec telle et telle option n'est pas encore assurée par cette page d'importation/mise à jour.<br />\n";
echo "(<i>il faut donc par la suite affecter les nouveaux élèves dans les classes et les inscrire dans les groupes/options/matières</i>)<br />\n";
echo "</p>\n";
echo "</li>\n";
echo "</ul>\n";
*/

echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
