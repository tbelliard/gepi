<?php
/*
 *
 * Copyright 2001, 2017 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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


$sql="SELECT 1=1 FROM droits WHERE id='/init_xml2/import_communs_xml.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/init_xml2/import_communs_xml.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Import XML Communs Sconet',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

	if (!checkAccess()) {
		header("Location: ../logout.php?auto=1");
		die();
	}

	//**************** EN-TETE *****************
	$titre_page = "Outil d'initialisation de l'année : Import Communs";
	require_once("../lib/header.inc.php");
	//**************** FIN EN-TETE *****************

	require_once("init_xml_lib.php");

	// Etape...
	$step=isset($_POST['step']) ? $_POST['step'] : (isset($_GET['step']) ? $_GET['step'] : NULL);

	if(isset($_GET['ad_retour_imports_communs'])){
		$_SESSION['ad_retour_imports_communs']=$_GET['ad_retour_imports_communs'];
	}

	$mysql_collate=getSettingValue("mysql_collate") ? getSettingValue("mysql_collate") : "";
	$chaine_mysql_collate="";
	if($mysql_collate!="") {$chaine_mysql_collate="COLLATE $mysql_collate";}

	//echo "\$_SESSION['ad_retour_imports_communs']=".$_SESSION['ad_retour_imports_communs']."<br />";

	unset($remarques);
	$remarques=array();


	// On va uploader les fichiers XML dans le tempdir de l'utilisateur (administrateur, ou scolarité pour les màj Sconet)
	$tempdir=get_user_temp_directory();
	if(!$tempdir){
		echo "<p style='color:red'>Il semble que le dossier temporaire de l'utilisateur ".$_SESSION['login']." ne soit pas défini&nbsp;!?</p>\n";
		// Il ne faut pas aller plus loin...
		// SITUATION A GERER
		require("../lib/footer.inc.php");
		die();
	}


	// =======================================================
	// EST-CE ENCORE UTILE?
	if(isset($_GET['nettoyage'])) {
		check_token(false);
		//echo "<h1 align='center'>Suppression des CSV</h1>\n";
		echo "<p class=bold><a href='";
		if(isset($_SESSION['ad_retour_imports_communs'])) {
			echo $_SESSION['ad_retour_imports_communs'];
		}
		else{
			echo "index.php";
		}
		echo "'> <img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
		echo "<a href='".$_SERVER['PHP_SELF']."'> | Autre import</a></p>\n";
		//echo "</div>\n";

		echo "<h2>Suppression des XML</h2>\n";

		echo "<p>Si des fichiers XML existent, ils seront supprimés...</p>\n";
		$tabfich=array("communs.xml");

		for($i=0;$i<count($tabfich);$i++){
			if(file_exists("../temp/".$tempdir."/$tabfich[$i]")) {
				echo "<p>Suppression de $tabfich[$i]... ";
				if(unlink("../temp/".$tempdir."/$tabfich[$i]")){
					echo "réussie.</p>\n";
				}
				else{
					echo "<font color='red'>Echec!</font> Vérifiez les droits d'écriture sur le serveur.</p>\n";
				}
			}
		}

		require("../lib/footer.inc.php");
		die();
	}
	// =======================================================
	else {
		echo "<p class=bold><a href='";
		if(isset($_SESSION['ad_retour_imports_communs'])){
			// On peut venir de l'index init_xml, de la page de conversion ou de la page de mise à jour Sconet
			echo $_SESSION['ad_retour_imports_communs'];
		}
		else{
			echo "index.php";
		}
		echo "'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

		if(isset($step)) {
			echo " | <a href='".$_SERVER['PHP_SELF']."'>Autre import</a>";
		}

		echo " | <a href='".$_SERVER['PHP_SELF']."?nettoyage=oui".add_token_in_url()."'>Suppression des fichiers XML existants</a>";
		echo "</p>\n";
		//echo "</div>\n";

		echo "<center><h3 class='gepi'>Import Communs</h3></center>\n";

		//if(!isset($_POST['is_posted'])){
		if(!isset($step)) {
			echo "<p>Cette page permet de remplir/mettre à jour les date de début/fin d'année, le nom du chef d'établissement,...<br />
			Le mauvais renseignement de certaines de ces informations peut se révéler bloquant sur certains exports comme l'export LSU.<br />
			Une date de début d'année non conforme à ce qui est dans Sconet, se solde par des erreurs lors de l'import LSU.<br />
			Le présent import permet d'éviter cela.</p>
			
			<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' id='form_envoi_xml' method='post'>
				<fieldset class='fieldset_opacite50'>
					".add_token_field()."
					<p>Veuillez fournir le fichier Communs.xml".(($gepiSettings['unzipped_max_filesize']>=0) ? " (<em>ou ExportCommuns.zip</em>)" : "")."&nbsp;:<br />
					<input type=\"file\" size=\"65\" name=\"communs_xml_file\" id='input_xml_file' style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\"); padding:5px; margin:5px;' /></p>
					<input type='hidden' name='step' value='0' />
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

		}
		else {
			check_token(false);
			$post_max_size=ini_get('post_max_size');
			$upload_max_filesize=ini_get('upload_max_filesize');
			$max_execution_time=ini_get('max_execution_time');
			$memory_limit=ini_get('memory_limit');

			if($step==0) {
				$xml_file = isset($_FILES["communs_xml_file"]) ? $_FILES["communs_xml_file"] : NULL;

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

						echo "<p>Il semblerait que l'absence d'extension .XML ou .ZIP puisse aussi provoquer ce genre de symptômes.<br />Dans ce cas, ajoutez l'extension et ré-essayez.</p>\n";

						// Il ne faut pas aller plus loin...
						// SITUATION A GERER
						require("../lib/footer.inc.php");
						die();
					}

					echo "<p>Le fichier a été uploadé.</p>\n";

					//$source_file=stripslashes($xml_file['tmp_name']);
					$source_file=$xml_file['tmp_name'];
					$dest_file="../temp/".$tempdir."/communs.xml";
					$res_copy=copy("$source_file" , "$dest_file");

					//===============================================================
					// ajout prise en compte des fichiers ZIP: Marc Leygnac

					$unzipped_max_filesize=getSettingValue('unzipped_max_filesize')*1024*1024;
					// $unzipped_max_filesize = 0    pas de limite de taille pour les fichiers extraits
					// $unzipped_max_filesize < 0    extraction zip désactivée
					if($unzipped_max_filesize>=0) {
						$fichier_emis=$xml_file['name'];
						$extension_fichier_emis=my_strtolower(mb_strrchr($fichier_emis,"."));
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
								echo "<p style='color:red;'>Erreur : La taille du fichier extrait (<em>".$list_file_zip[0]['size']." octets</em>) dépasse la limite paramétrée (<em>".lien_valeur_unzipped_max_filesize()."</em>).</p>\n";
								require("../lib/footer.inc.php");
								die();
							}

							//unlink("$dest_file"); // Pour Wamp...
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
					else {
						echo "<p>La copie du fichier vers le dossier temporaire a réussi.</p>\n";

						libxml_use_internal_errors(true);
						$communs_xml=simplexml_load_file($dest_file);
						if(!$communs_xml) {
							echo "<p style='color:red;'>ECHEC du chargement du fichier avec simpleXML.</p>\n";
							echo "<p><a href='".$_SERVER['PHP_SELF']."'>Téléverser un autre fichier</a></p>\n";
							require("../lib/footer.inc.php");
							die();
						}

						$nom_racine=$communs_xml->getName();
						if(my_strtoupper($nom_racine)!='BEE_COMMUN') {
							echo "<p style='color:red;'>ERREUR: Le fichier XML fourni n'a pas l'air d'être un fichier XML Communs.<br />Sa racine devrait être 'BEE_COMMUN'.</p>\n";
							require("../lib/footer.inc.php");
							die();
						}

						//	<PARAMETRES>
						//		<UAJ>TEL_RNE</UAJ>
						//		<ANNEE_SCOLAIRE>2011</ANNEE_SCOLAIRE>
						//		<DATE_EXPORT>22/05/2012</DATE_EXPORT>
						//		<HORODATAGE>22/05/2012 08:09:38</HORODATAGE>
						//	</PARAMETRES>
						//	<DONNEES>

						$xml_uaj="";
						$xml_horodatage="";
						$objet_parametres=($communs_xml->PARAMETRES);
						foreach ($objet_parametres->children() as $key => $value) {
							if($key=='ANNEE_SCOLAIRE') {
								//$annee_scolaire=$value;
								if(!preg_match("/^$value/", getSettingValue('gepiYear'))) {
									echo "<br /><p style='text-indent: -7.5em; margin-left: 7.5em;'><strong style='color:red'>ATTENTION&nbsp;:</strong> L'année scolaire du fichier XML (<em>$value</em>) ne semble pas correspondre à l'année scolaire paramétrée dans Gepi (<em>".getSettingValue('gepiYear')."</em>).<br />Auriez-vous récupéré un XML de l'année précédente ou de l'année prochaine (<em>il arrive que l'on bascule dans Sconet en juin ou courant septembre</em>)&nbsp;?</p><br />\n";
									//$nb_err++;
								}
							}
							elseif($key=='HORODATAGE') {
								$xml_horodatage=$value;
							}
							elseif($key=='UAJ') {
								$xml_uaj=$value;
							}
						}

						//saveSetting('ts_maj_sconet', strftime("%Y-%m-%d %H:%M:%S"));
						$texte_maj_sconet="<br /><p><strong>Fichier XML Communs</strong>";
						if($xml_uaj!="") {$texte_maj_sconet.=" ($xml_uaj)";}
						if($xml_horodatage!="") {$texte_maj_sconet.=" du $xml_horodatage";}
						$texte_maj_sconet.="</p>";
						echo $texte_maj_sconet;
						//enregistre_log_maj_sconet($texte_maj_sconet);


						echo "<p>Analyse du fichier pour extraire les informations de la section UAJ.<br />\n";

						$tab_champs_uaj=array("DENOM_PRINC",
										"NOM_RESP",
										"QUAL_RESP",
										"LIGNE1_ADRESSE",
										"LIGNE2_ADRESSE",
										"LIGNE3_ADRESSE",
										"LIGNE4_ADRESSE",
										"BOITE_POST",
										"COMMUNE_ETRANGERE",
										"TELEPHONE",
										"TELECOPIE",
										"MEL_1",
										"CODE_POSTAL",
										"CODE_COMMUNE_INSEE",
										"CODE_PAYS");
						$tab_uaj=array();

						$i=-1;
						$objet_uaj=($communs_xml->DONNEES->UAJ);
						foreach ($objet_uaj->children() as $key => $value) {
							//echo("$key=".$value."<br />");
							if(in_array(my_strtoupper($key),$tab_champs_uaj)) {
								$tab_uaj[my_strtolower($key)]=preg_replace('/"/','',trim($value));
							}
						}
						echo "</p>\n";


						echo "<p>Analyse du fichier pour extraire les informations de la section ANNEE_SCOLAIRE.<br />\n";
						$tab_champs_annee_scolaire=array("DATE_DEBUT_ELEVE",
										"DATE_FIN_ELEVE");
						$tab_annee=array();

						$i=-1;
						$objet_annee=($communs_xml->DONNEES->ANNEE_SCOLAIRE);
						foreach ($objet_annee->children() as $key => $value) {
							//echo("$key=".$value."<br />");
							if(in_array(my_strtoupper($key),$tab_champs_annee_scolaire)) {
								$tab_annee[my_strtolower($key)]=preg_replace('/"/','',trim($value));
							}
						}
						echo "</p>\n";

						echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<p>Sélectionnez les champs à modifier d'après le contenu du XML.</p>
		<table class='boireaus boireaus_alt'>
			<thead>
				<tr>
					<th>
						<a href=\"javascript:ToutCocher();changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' title='Tout cocher' /></a> / <a href=\"javascript:ToutDecocher();changement();\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' title='Tout décocher' /></a>
					</th>
					<th>Description</th>
					<th>Valeur du XML</th>
					<th>Valeur enregistrée dans Gepi</th>
				</tr>
			</thead>
			<tbody>";
						if(isset($tab_uaj['denom_princ'])) {
							echo "
				<tr>
					<td><input type='checkbox' name='denom_princ' id='denom_princ' value='y' onchange=\"checkbox_change(this.id)\" /></td>
					<td><label for='denom_princ' id='texte_denom_princ'>Nom de l'établissement</label></td>
					<td>".$tab_uaj['denom_princ']."</td>
					<td>".getSettingValue('gepiSchoolName')."</td>
				</tr>";
						}

						// Adresse
						$ligne1_adresse="";
						if((isset($tab_uaj['ligne1_adresse']))&&($tab_uaj['ligne1_adresse']!="")) {
							$ligne1_adresse=$tab_uaj['ligne1_adresse'];
						}

						$ligne2_adresse="";
						if((isset($tab_uaj['ligne2_adresse']))&&($tab_uaj['ligne2_adresse']!="")) {
							$ligne2_adresse=$tab_uaj['ligne2_adresse'];
						}
						if((isset($tab_uaj['ligne3_adresse']))&&($tab_uaj['ligne3_adresse']!="")) {
							if(($ligne2_adresse!="")&&(!preg_match("/, $/", $ligne2_adresse))) {$ligne2_adresse.=", ";}
							$ligne2_adresse.=$tab_uaj['ligne3_adresse'];
						}
						if((isset($tab_uaj['ligne4_adresse']))&&($tab_uaj['ligne4_adresse']!="")) {
							if(($ligne2_adresse!="")&&(!preg_match("/, $/", $ligne2_adresse))) {$ligne2_adresse.=", ";}
							$ligne2_adresse.=$tab_uaj['ligne4_adresse'];
						}
						if((isset($tab_uaj['boite_post']))&&($tab_uaj['boite_post']!="")) {
							if(($ligne2_adresse!="")&&(!preg_match("/, $/", $ligne2_adresse))) {$ligne2_adresse.=", ";}
							$ligne2_adresse.="BP ".$tab_uaj['boite_post'];
						}

						if($ligne1_adresse!="") {
							echo "
				<tr>
					<td><input type='checkbox' name='ligne1_adresse' id='ligne1_adresse' value='y' onchange=\"checkbox_change(this.id)\" /></td>
					<td><label for='ligne1_adresse' id='texte_ligne1_adresse'>Ligne 1 adresse</label></td>
					<td>".$ligne1_adresse."</td>
					<td>".getSettingValue('gepiSchoolAdress1')."</td>
				</tr>";
						}

						if($ligne2_adresse!="") {
							echo "
				<tr>
					<td><input type='checkbox' name='ligne2_adresse' id='ligne2_adresse' value='y' onchange=\"checkbox_change(this.id)\" /></td>
					<td><label for='ligne2_adresse' id='texte_ligne2_adresse'>Ligne 2 adresse</label></td>
					<td>".$ligne2_adresse."</td>
					<td>".getSettingValue('gepiSchoolAdress2')."</td>
				</tr>";
						}

						if(isset($tab_uaj['code_postal'])) {
							echo "
				<tr>
					<td><input type='checkbox' name='code_postal' id='code_postal' value='y' onchange=\"checkbox_change(this.id)\" /></td>
					<td><label for='code_postal' id='texte_code_postal'>Code postal</label></td>
					<td>".$tab_uaj['code_postal']."</td>
					<td>".getSettingValue('gepiSchoolZipCode')."</td>
				</tr>";
						}

						if((isset($tab_uaj['commune_etrangere']))&&($tab_uaj['commune_etrangere']!="")) {
							echo "
				<tr>
					<td><input type='checkbox' name='commune_etrangere' id='commune_etrangere' value='y' onchange=\"checkbox_change(this.id)\" /></td>
					<td><label for='commune_etrangere' id='texte_commune_etrangere'>Commune</label></td>
					<td>".$tab_uaj['commune_etrangere']."</td>
					<td>".getSettingValue('gepiSchoolCity')."</td>
				</tr>";
						}
						if((isset($tab_uaj['code_commune_insee']))&&($tab_uaj['code_commune_insee']!="")) {
							echo "
				<tr>
					<td><input type='checkbox' name='code_commune_insee' id='code_commune_insee' value='y' onchange=\"checkbox_change(this.id)\" /></td>
					<td><label for='code_commune_insee' id='texte_code_commune_insee'>Commune</label></td>
					<td>".get_commune($tab_uaj['code_commune_insee'])."</td>
					<td>".getSettingValue('gepiSchoolCity')."</td>
				</tr>";
						}


						if((isset($tab_uaj['code_pays']))&&($tab_uaj['code_pays']!="")&&(get_pays($tab_uaj['code_pays'])!='PAYS INCONNU')) {
							echo "
				<tr>
					<td><input type='checkbox' name='code_pays' id='code_pays' value='y' onchange=\"checkbox_change(this.id)\" /></td>
					<td><label for='code_pays' id='texte_code_pays'>Pays</label></td>
					<td>".get_pays($tab_uaj['code_pays'])."</td>
					<td>".getSettingValue('gepiSchoolPays')."</td>
				</tr>";
						}


						if(isset($tab_uaj['telephone'])) {
							echo "
				<tr>
					<td><input type='checkbox' name='telephone' id='telephone' value='y' onchange=\"checkbox_change(this.id)\" /></td>
					<td><label for='telephone' id='texte_telephone'>Téléphone</label></td>
					<td>".$tab_uaj['telephone']."</td>
					<td>".getSettingValue('gepiSchoolTel')."</td>
				</tr>";
						}
						if(isset($tab_uaj['telecopie'])) {
							echo "
				<tr>
					<td><input type='checkbox' name='telecopie' id='telecopie' value='y' onchange=\"checkbox_change(this.id)\" /></td>
					<td><label for='telecopie' id='texte_telecopie'>Télécopie (fax)</label></td>
					<td>".$tab_uaj['telecopie']."</td>
					<td>".getSettingValue('gepiSchoolFax')."</td>
				</tr>";
						}
						if(isset($tab_uaj['mel_1'])) {
							echo "
				<tr>
					<td><input type='checkbox' name='mel_1' id='mel_1' value='y' onchange=\"checkbox_change(this.id)\" /></td>
					<td><label for='mel_1' id='texte_mel_1'>Email de l'établissement</label></td>
					<td>".$tab_uaj['mel_1']."</td>
					<td>".getSettingValue('gepiSchoolEmail')."</td>
				</tr>";
						}


						// Chef d'établissement
						if(isset($tab_uaj['qual_resp'])) {
							echo "
				<tr>
					<td><input type='checkbox' name='qual_resp' id='qual_resp' value='y' onchange=\"checkbox_change(this.id)\" /></td>
					<td><label for='qual_resp' id='texte_qual_resp'>Fonction du chef d'établissement/<br />(administrateur du site)</label></td>
					<td>".$tab_uaj['qual_resp']."</td>
					<td>".getSettingValue('gepiAdminFonction')."</td>
				</tr>";
						}

						if(isset($tab_uaj['nom_resp'])) {
							$tmp_tab=explode(' ', $tab_uaj['nom_resp']);
							if(isset($tmp_tab[1])) {
								echo "
				<tr>
					<td><input type='checkbox' name='nom_resp' id='nom_resp' value='y' onchange=\"checkbox_change(this.id)\" /></td>
					<td><label for='nom_resp' id='texte_nom_resp'>Nom du chef d'établissement</label></td>
					<td>".$tmp_tab[1]."</td>
					<td>".getSettingValue('gepiAdminNom')."</td>
				</tr>";

								echo "
				<tr>
					<td><input type='checkbox' name='prenom_resp' id='prenom_resp' value='y' onchange=\"checkbox_change(this.id)\" /></td>
					<td><label for='prenom_resp' id='texte_prenom_resp'>Prénom du chef d'établissement</label></td>
					<td>".$tmp_tab[0]."</td>
					<td>".getSettingValue('gepiAdminPrenom')."</td>
				</tr>";
							}
						}



						if(isset($tab_annee['date_debut_eleve'])) {
							$date_debut_annee="";
							if(getSettingValue('begin_bookings')!='') {
								$date_debut_annee=strftime("%d/%m/%Y", getSettingValue('begin_bookings'));
							}
							echo "
				<tr>
					<td><input type='checkbox' name='date_debut_eleve' id='date_debut_eleve' value='y' onchange=\"checkbox_change(this.id)\" /></td>
					<td><label for='date_debut_eleve' id='texte_date_debut_eleve'>Date de début d'année pour les élèves</label></td>
					<td>".$tab_annee['date_debut_eleve']."</td>
					<td>".$date_debut_annee."</td>
				</tr>";
						}
						if(isset($tab_annee['date_fin_eleve'])) {
							$date_fin_annee="";
							if(getSettingValue('end_bookings')!='') {
								$date_fin_annee=strftime("%d/%m/%Y", getSettingValue('end_bookings'));
							}
							echo "
				<tr>
					<td><input type='checkbox' name='date_fin_eleve' id='date_fin_eleve' value='y' onchange=\"checkbox_change(this.id)\" /></td>
					<td><label for='date_fin_eleve' id='texte_date_fin_eleve'>Date de fin d'année pour les élèves</label></td>
					<td>".$tab_annee['date_fin_eleve']."</td>
					<td>".$date_fin_annee."</td>
				</tr>";
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
		item=document.getElementsByTagName('input');
		for(i=0;i<item.length;i++) {
			if(item[i].getAttribute('type')=='checkbox') {
				item[i].checked=true;
				checkbox_change(item[i].getAttribute('id'));
			}
		}
	}

	function ToutDecocher() {
		item=document.getElementsByTagName('input');
		for(i=0;i<item.length;i++) {
			if(item[i].getAttribute('type')=='checkbox') {
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
			elseif($step==1) {
				//debug_var();

				$dest_file="../temp/".$tempdir."/communs.xml";

				libxml_use_internal_errors(true);
				$communs_xml=simplexml_load_file($dest_file);
				if(!$communs_xml) {
					echo "<p style='color:red;'>ECHEC du chargement du fichier avec simpleXML.</p>\n";
					echo "<p><a href='".$_SERVER['PHP_SELF']."'>Téléverser un autre fichier</a></p>\n";
					require("../lib/footer.inc.php");
					die();
				}

				$nom_racine=$communs_xml->getName();
				if(my_strtoupper($nom_racine)!='BEE_COMMUN') {
					echo "<p style='color:red;'>ERREUR: Le fichier XML fourni n'a pas l'air d'être un fichier XML Communs.<br />Sa racine devrait être 'BEE_COMMUN'.</p>\n";
					require("../lib/footer.inc.php");
					die();
				}

				//	<PARAMETRES>
				//		<UAJ>TEL_RNE</UAJ>
				//		<ANNEE_SCOLAIRE>2011</ANNEE_SCOLAIRE>
				//		<DATE_EXPORT>22/05/2012</DATE_EXPORT>
				//		<HORODATAGE>22/05/2012 08:09:38</HORODATAGE>
				//	</PARAMETRES>
				//	<DONNEES>

				$xml_uaj="";
				$xml_horodatage="";
				$objet_parametres=($communs_xml->PARAMETRES);
				foreach ($objet_parametres->children() as $key => $value) {
					if($key=='ANNEE_SCOLAIRE') {
						//$annee_scolaire=$value;
						if(!preg_match("/^$value/", getSettingValue('gepiYear'))) {
							echo "<br /><p style='text-indent: -7.5em; margin-left: 7.5em;'><strong style='color:red'>ATTENTION&nbsp;:</strong> L'année scolaire du fichier XML (<em>$value</em>) ne semble pas correspondre à l'année scolaire paramétrée dans Gepi (<em>".getSettingValue('gepiYear')."</em>).<br />Auriez-vous récupéré un XML de l'année précédente ou de l'année prochaine (<em>il arrive que l'on bascule dans Sconet en juin ou courant septembre</em>)&nbsp;?</p><br />\n";
							//$nb_err++;
						}
					}
					elseif($key=='HORODATAGE') {
						$xml_horodatage=$value;
					}
					elseif($key=='UAJ') {
						$xml_uaj=$value;
					}
				}

				//saveSetting('ts_maj_sconet', strftime("%Y-%m-%d %H:%M:%S"));
				$texte_maj_sconet="<br /><p><strong>Fichier XML Communs</strong>";
				if($xml_uaj!="") {$texte_maj_sconet.=" ($xml_uaj)";}
				if($xml_horodatage!="") {$texte_maj_sconet.=" du $xml_horodatage";}
				$texte_maj_sconet.="</p>";
				echo $texte_maj_sconet;
				//enregistre_log_maj_sconet($texte_maj_sconet);


				echo "<p>Analyse du fichier pour extraire les informations de la section UAJ.<br />\n";

				$tab_champs_uaj=array("DENOM_PRINC",
								"NOM_RESP",
								"QUAL_RESP",
								"LIGNE1_ADRESSE",
								"LIGNE2_ADRESSE",
								"LIGNE3_ADRESSE",
								"LIGNE4_ADRESSE",
								"BOITE_POST",
								"COMMUNE_ETRANGERE",
								"TELEPHONE",
								"TELECOPIE",
								"MEL_1",
								"CODE_POSTAL",
								"CODE_COMMUNE_INSEE",
								"CODE_PAYS");
				$tab_uaj=array();

				$i=-1;
				$objet_uaj=($communs_xml->DONNEES->UAJ);
				foreach ($objet_uaj->children() as $key => $value) {
					//echo("$key=".$value."<br />");
					if(in_array(my_strtoupper($key),$tab_champs_uaj)) {
						$tab_uaj[my_strtolower($key)]=preg_replace('/"/','',trim($value));
					}
				}
				echo "</p>\n";


				echo "<p>Analyse du fichier pour extraire les informations de la section ANNEE_SCOLAIRE.<br />\n";
				$tab_champs_annee_scolaire=array("DATE_DEBUT_ELEVE",
								"DATE_FIN_ELEVE");
				$tab_annee=array();

				$i=-1;
				$objet_annee=($communs_xml->DONNEES->ANNEE_SCOLAIRE);
				foreach ($objet_annee->children() as $key => $value) {
					//echo("$key=".$value."<br />");
					if(in_array(my_strtoupper($key),$tab_champs_annee_scolaire)) {
						$tab_annee[my_strtolower($key)]=preg_replace('/"/','',trim($value));
					}
				}
				echo "</p>\n";

				echo "<p style='margin-top:1em;'>";
				if((isset($_POST['denom_princ']))&&(isset($tab_uaj['denom_princ']))) {
					echo "Enregistrement du nom de l'établissement&nbsp;: ";
					if(saveSetting('gepiSchoolName', $tab_uaj['denom_princ'])) {
						echo "<span style='color:green'>".$tab_uaj['denom_princ']."</span>";
					}
					else {
						echo "<span style='color:red'>".$tab_uaj['denom_princ']."</span>";
					}
					echo "<br />";
				}

				// Adresse
				$ligne1_adresse="";
				if((isset($tab_uaj['ligne1_adresse']))&&($tab_uaj['ligne1_adresse']!="")) {
					$ligne1_adresse=$tab_uaj['ligne1_adresse'];
				}

				$ligne2_adresse="";
				if((isset($tab_uaj['ligne2_adresse']))&&($tab_uaj['ligne2_adresse']!="")) {
					$ligne2_adresse=$tab_uaj['ligne2_adresse'];
				}
				if((isset($tab_uaj['ligne3_adresse']))&&($tab_uaj['ligne3_adresse']!="")) {
					if(($ligne2_adresse!="")&&(!preg_match("/, $/", $ligne2_adresse))) {$ligne2_adresse.=", ";}
					$ligne2_adresse.=$tab_uaj['ligne3_adresse'];
				}
				if((isset($tab_uaj['ligne4_adresse']))&&($tab_uaj['ligne4_adresse']!="")) {
					if(($ligne2_adresse!="")&&(!preg_match("/, $/", $ligne2_adresse))) {$ligne2_adresse.=", ";}
					$ligne2_adresse.=$tab_uaj['ligne4_adresse'];
				}
				if((isset($tab_uaj['boite_post']))&&($tab_uaj['boite_post']!="")) {
					if(($ligne2_adresse!="")&&(!preg_match("/, $/", $ligne2_adresse))) {$ligne2_adresse.=", ";}
					$ligne2_adresse.="BP ".$tab_uaj['boite_post'];
				}

				if((isset($_POST['ligne1_adresse']))&&($ligne1_adresse!="")) {
					echo "Enregistrement de la ligne 1 de l'adresse établissement&nbsp;: ";
					if(saveSetting('gepiSchoolAdress1', $ligne1_adresse)) {
						echo "<span style='color:green'>".$ligne1_adresse."</span>";
					}
					else {
						echo "<span style='color:red'>".$ligne1_adresse."</span>";
					}
					echo "<br />";
				}

				if((isset($_POST['ligne2_adresse']))&&($ligne2_adresse!="")) {
					echo "Enregistrement de la ligne 2 de l'adresse établissement&nbsp;: ";
					if(saveSetting('gepiSchoolAdress2', $ligne2_adresse)) {
						echo "<span style='color:green'>".$ligne2_adresse."</span>";
					}
					else {
						echo "<span style='color:red'>".$ligne2_adresse."</span>";
					}
					echo "<br />";
				}

				if((isset($_POST['code_postal']))&&(isset($tab_uaj['code_postal']))) {
					echo "Enregistrement du code postal de l'établissement&nbsp;: ";
					if(saveSetting('gepiSchoolZipCode', $tab_uaj['code_postal'])) {
						echo "<span style='color:green'>".$tab_uaj['code_postal']."</span>";
					}
					else {
						echo "<span style='color:red'>".$tab_uaj['code_postal']."</span>";
					}
					echo "<br />";
				}

				if((isset($_POST['commune_etrangere']))&&(isset($tab_uaj['commune_etrangere']))&&($tab_uaj['commune_etrangere']!="")) {
					echo "Enregistrement de la commune de l'établissement&nbsp;: ";
					if(saveSetting('gepiSchoolCity', $tab_uaj['commune_etrangere'])) {
						echo "<span style='color:green'>".$tab_uaj['commune_etrangere']."</span>";
					}
					else {
						echo "<span style='color:red'>".$tab_uaj['commune_etrangere']."</span>";
					}
					echo "<br />";
				}

				if((isset($_POST['code_commune_insee']))&&(isset($tab_uaj['code_commune_insee']))&&($tab_uaj['code_commune_insee']!="")) {
					echo "Enregistrement de la commune de l'établissement&nbsp;: ";
					$commune=get_commune($tab_uaj['code_commune_insee']);
					if(saveSetting('gepiSchoolCity', $commune)) {
						echo "<span style='color:green'>".$commune."</span>";
					}
					else {
						echo "<span style='color:red'>".$commune."</span>";
					}
					echo "<br />";
				}

				if((isset($_POST['code_pays']))&&(isset($tab_uaj['code_pays']))&&($tab_uaj['code_pays']!="")&&(get_pays($tab_uaj['code_pays'])!='PAYS INCONNU')) {
					echo "Enregistrement du pays de l'établissement&nbsp;: ";
					$pays=get_pays($tab_uaj['code_pays']);
					if(saveSetting('gepiSchoolPays', $pays)) {
						echo "<span style='color:green'>".$pays."</span>";
					}
					else {
						echo "<span style='color:red'>".$pays."</span>";
					}
					echo "<br />";
				}

				if((isset($_POST['telephone']))&&(isset($tab_uaj['telephone']))) {
					echo "Enregistrement du numéro de téléphone de l'établissement&nbsp;: ";
					if(saveSetting('gepiSchoolTel', $tab_uaj['telephone'])) {
						echo "<span style='color:green'>".$tab_uaj['telephone']."</span>";
					}
					else {
						echo "<span style='color:red'>".$tab_uaj['telephone']."</span>";
					}
					echo "<br />";
				}

				if((isset($_POST['telecopie']))&&(isset($tab_uaj['telecopie']))) {
					echo "Enregistrement du numéro de télécopie de l'établissement&nbsp;: ";
					if(saveSetting('gepiSchoolFax', $tab_uaj['telecopie'])) {
						echo "<span style='color:green'>".$tab_uaj['telecopie']."</span>";
					}
					else {
						echo "<span style='color:red'>".$tab_uaj['telecopie']."</span>";
					}
					echo "<br />";
				}

				if((isset($_POST['mel_1']))&&(isset($tab_uaj['mel_1']))) {
					echo "Enregistrement de l'email de l'établissement&nbsp;: ";
					if(saveSetting('gepiSchoolEmail', $tab_uaj['mel_1'])) {
						echo "<span style='color:green'>".$tab_uaj['mel_1']."</span>";
					}
					else {
						echo "<span style='color:red'>".$tab_uaj['mel_1']."</span>";
					}
					echo "<br />";
				}


				// Chef d'établissement
				if((isset($_POST['qual_resp']))&&(isset($tab_uaj['qual_resp']))) {
					echo "Enregistrement de la qualité du chef d'établissement&nbsp;: ";
					if(saveSetting('gepiAdminFonction', $tab_uaj['qual_resp'])) {
						echo "<span style='color:green'>".$tab_uaj['qual_resp']."</span>";
					}
					else {
						echo "<span style='color:red'>".$tab_uaj['qual_resp']."</span>";
					}
					echo "<br />";
				}

				if((isset($_POST['nom_resp']))&&(isset($tab_uaj['nom_resp']))) {
					$tmp_tab=explode(' ', $tab_uaj['nom_resp']);
					if(isset($tmp_tab[1])) {
						echo "Enregistrement du nom du chef d'établissement&nbsp;: ";
						if(saveSetting('gepiAdminNom', $tmp_tab[1])) {
							echo "<span style='color:green'>".$tmp_tab[1]."</span>";
						}
						else {
							echo "<span style='color:red'>".$tmp_tab[1]."</span>";
						}
						echo "<br />";
					}
				}

				if((isset($_POST['prenom_resp']))&&(isset($tab_uaj['nom_resp']))) {
					$tmp_tab=explode(' ', $tab_uaj['nom_resp']);
					if(isset($tmp_tab[0])) {
						echo "Enregistrement du prénom du chef d'établissement&nbsp;: ";
						if(saveSetting('gepiAdminPrenom', $tmp_tab[0])) {
							echo "<span style='color:green'>".$tmp_tab[0]."</span>";
						}
						else {
							echo "<span style='color:red'>".$tmp_tab[0]."</span>";
						}
						echo "<br />";
					}
				}


				if((isset($_POST['date_debut_eleve']))&&(isset($tab_annee['date_debut_eleve']))) {
					if(!preg_match('#^[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}$#', $tab_annee['date_debut_eleve'])) {
						echo "<span style='color:red'>Format de la date de début invalide&nbsp;: ".$tab_annee['date_debut_eleve']."</span><br />";
					}
					else {
						$tmp_tab=explode('/', $tab_annee['date_debut_eleve']);
						$begin_bookings = mktime(0,0,0,$tmp_tab[1], $tmp_tab[0], $tmp_tab[2]);
						echo "Enregistrement de la date de début d'année pour les élèves&nbsp;: ";
						if(saveSetting('begin_bookings', $begin_bookings)) {
							echo "<span style='color:green'>".$tab_annee['date_debut_eleve']."</span>";
						}
						else {
							echo "<span style='color:red'>".$tab_annee['date_debut_eleve']."</span>";
						}
						echo "<br />";
					}
				}
				if((isset($_POST['date_fin_eleve']))&&(isset($tab_annee['date_fin_eleve']))) {
					if(!preg_match('#^[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}$#', $tab_annee['date_fin_eleve'])) {
						echo "<span style='color:red'>Format de la date de fin invalide&nbsp;: ".$tab_annee['date_fin_eleve']."</span><br />";
					}
					else {
						$tmp_tab=explode('/', $tab_annee['date_fin_eleve']);
						$end_bookings = mktime(0,0,0,$tmp_tab[1], $tmp_tab[0], $tmp_tab[2]);
						echo "Enregistrement de la date de din d'année&nbsp;: ";
						if(saveSetting('end_bookings', $end_bookings)) {
							echo "<span style='color:green'>".$tab_annee['date_fin_eleve']."</span>";
						}
						else {
							echo "<span style='color:red'>".$tab_annee['date_fin_eleve']."</span>";
						}
						echo "<br />";
					}
				}
				echo "</p><p style='margin-top:1em;'>Terminé.</p>";


			}
			else {
				echo "<p style='color:red'>Anomalie&nbsp;: Vous ne devriez pas arriver là.</p>";
			}
		}
	}
	require("../lib/footer.inc.php");
?>
