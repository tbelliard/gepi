<?php
@set_time_limit(0);

// $Id$

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

$debug_import="n";

//**************** EN-TETE *****************
$titre_page = "XML de SCONET: Génération de CSV";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

function extr_valeur($lig){
	unset($tabtmp);
	$tabtmp=explode(">",preg_replace("/</",">",$lig));
	return trim($tabtmp[2]);
}

?>
	<div class="content">
		<?php

			// On va uploader les fichiers XML dans le tempdir de l'utilisateur (administrateur, ou scolarité pour les màj Sconet)
			$tempdir=get_user_temp_directory();
			if(!$tempdir){
				echo "<p style='color:red'>Il semble que le dossier temporaire de l'utilisateur ".$_SESSION['login']." ne soit pas défini!?</p>\n";
				// Il ne faut pas aller plus loin...
				// SITUATION A GERER
			}

			// Pour importer séparemment les ElevesAvecAdresses.xml, Nomenclature.xml et d'autre part le Responsables.xml,
			// une variable:
			$etape=isset($_POST['etape']) ? $_POST['etape'] : (isset($_GET['etape']) ? $_GET['etape'] : NULL);
			// Il y a un problème de volume des données transférées si on envoye tout d'un coup.


			if(isset($_GET['ad_retour'])){
				$_SESSION['ad_retour']=$_GET['ad_retour'];
			}

			//echo "\$_SESSION['ad_retour']=".$_SESSION['ad_retour']."<br />";

			unset($remarques);
			$remarques=array();


			// Initialisation du répertoire actuel de sauvegarde
			$dirname = getSettingValue("backup_directory");
			//$dirname="tmp";

			if(!file_exists("../backup/$dirname/csv")){
				//if(!mkdir("../backup/$dirname/csv","0770")){
				if(!mkdir("../backup/$dirname/csv")){
					echo "<p style='color:red;'>Erreur! Le dossier csv n'a pas pu être créé.</p>\n";
					echo "<p>Retour à l'<a href='index.php'>index</a></p>\n";
					echo "</div></body></html>\n";
					die();
				}
			}

			if(!file_exists("../backup/$dirname/csv/index.html")){
				$fich=fopen("../backup/$dirname/csv/index.html","w+");
				fwrite($fich,'<script type="text/javascript" language="JavaScript">
    document.location.replace("../../../login.php")
</script>');
				fclose($fich);
			}

			if(isset($_GET['nettoyage'])){
				check_token(false);

				//echo "<h1 align='center'>Suppression des CSV</h1>\n";
				echo "<h2 align='center'>Suppression des CSV</h2>\n";
				echo "<p class=bold><a href='";
				if(isset($_SESSION['ad_retour'])){
					echo $_SESSION['ad_retour'];
				}
				else{
					echo "index.php";
				}
				echo "'> <img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
				echo "<a href='".$_SERVER['PHP_SELF']."'> | Autre import</a></p>\n";

				echo "<p>Si des fichiers CSV existent, ils seront supprimés...</p>\n";
				//$tabfich=array("f_ele.csv","f_ere.csv");
				$tabfich=array("eleves.csv","etablissements.csv","eleve_etablissement.csv","adresses.csv","personnes.csv","responsables.csv");
				for($i=0;$i<count($tabfich);$i++){
					if(file_exists("../backup/$dirname/csv/$tabfich[$i]")){
						echo "<p>Suppression de $tabfich[$i]... ";
						if(unlink("../backup/$dirname/csv/$tabfich[$i]")){
							echo "réussie.</p>\n";
						}
						else{
							echo "<font color='red'>Echec!</font> Vérifiez les droits d'écriture sur le serveur.</p>\n";
						}
					}
				}
			}
			else{
				//echo "<h1 align='center'>Lecture des XML de Sconet et génération de CSV</h1>\n";
				echo "<h2 align='center'>Lecture des XML de Sconet et génération de CSV</h2>\n";
				//echo "<p><a href='index.php'>Retour</a>|\n";
				echo "<p class=bold><a href='";
				if(isset($_SESSION['ad_retour'])){
					echo $_SESSION['ad_retour'];
				}
				else{
					echo "index.php";
				}
				echo "'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

				if(!isset($etape)){
					echo "</p>\n";
					echo "<p>Pour éviter des problèmes de taille maximale des upload, les extractions se font en deux étapes.</p>";
					echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
					echo add_token_field();
					echo "<input type='radio' name='etape' value='1' id='etape_eleves' checked /> <label for='etape_eleves'>Etape 1: Elèves</label><br />\n";
					echo "<input type='radio' name='etape' value='2' id='etape_resp' /> <label for='etape_resp'>Etape 2: Responsables</label><br />\n";

					echo "<p><input type='submit' value='Valider' /></p>\n";

					echo "<p>Les fichiers réclamés ici doivent être récupérés depuis Sconet.<br />Demandez gentiment à votre secrétaire de se rendre dans 'Sconet/Accès Base élèves mode normal/Exploitation/Exports standard/Exports XML génériques' pour récupérer les fichiers ElevesAvecAdresses.xml, Nomenclature.xml et ResponsablesAvecAdresses.xml.</p>\n";
					echo "</form>\n";
				}
				else {
					echo "<a href='".$_SERVER['PHP_SELF']."'> | Autre import</a></p>\n";

					check_token(false);

					if(!isset($_POST['is_posted'])) {
						//echo "<p>Cette page permet de remplir des tableaux PHP avec les informations élèves, responsables,...<br />\n";
						echo "<p>Cette page permet de remplir des tables temporaires avec les informations élèves, responsables,...<br />\n";
						echo "</p>\n";

						echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
						echo add_token_field();

						if($etape==1){
							echo "<p>Veuillez fournir le fichier ElevesAvecAdresses.xml (<i>ou ElevesSansAdresses.xml</i>):<br />\n";
							echo "<input type=\"file\" size=\"80\" name=\"eleves_xml_file\" /><br />\n";
							echo "Veuillez fournir le fichier Nomenclature.xml:<br />\n";
							echo "<input type=\"file\" size=\"80\" name=\"nomenclature_xml_file\" /><br />\n";
						}
						else{
							echo "<p>Veuillez fournir le fichier ResponsablesAvecAdresses.xml:<br />\n";
							echo "<input type=\"file\" size=\"80\" name=\"responsables_xml_file\" /><br />\n";
						}
						echo "<input type='hidden' name='etape' value='$etape' />\n";
						echo "<input type='hidden' name='is_posted' value='yes' />\n";
						echo "</p>\n";

						echo "<p><input type='submit' value='Valider' /></p>\n";
						echo "</form>\n";
					}
					else {
						$post_max_size=ini_get('post_max_size');
						$upload_max_filesize=ini_get('upload_max_filesize');
						$max_execution_time=ini_get('max_execution_time');
						$memory_limit=ini_get('memory_limit');

						if($etape==1) {
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
										echo "<p style='color:red;'>Erreur : La taille du fichier extrait (<em>".$list_file_zip[0]['size']." octets</em>) dépasse la limite paramétrée (<em>$unzipped_max_filesize octets</em>).</p>\n";
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
	
							echo "<p>La copie du fichier vers le dossier temporaire a réussi.</p>\n";
	
							$ele_xml=simplexml_load_file($dest_file);
							if(!$ele_xml) {
								echo "<p style='color:red;'>ECHEC du chargement du fichier avec simpleXML.</p>\n";
								require("../lib/footer.inc.php");
								die();
							}
	
							$nom_racine=$ele_xml->getName();
							if(my_strtoupper($nom_racine)!='BEE_ELEVES') {
								echo "<p style='color:red;'>ERREUR: Le fichier XML fourni n'a pas l'air d'être un fichier XML Elèves.<br />Sa racine devrait être 'BEE_ELEVES'.</p>\n";
								require("../lib/footer.inc.php");
								die();
							}
	
							echo "<p>\n";
							echo "Analyse du fichier pour extraire les informations de la section STRUCTURES pour ne conserver que les identifiants d'élèves affectés dans une classe...<br />\n";
	
							$tab_champs_struct=array("CODE_STRUCTURE","TYPE_STRUCTURE");
							$tab_ele_id=array();
	
							$i=-1;
							$objet_structures=($ele_xml->DONNEES->STRUCTURES);
							foreach ($objet_structures->children() as $structures_eleve) {
								//echo("<p><b>Structure</b><br />");
						
								$chaine_structures_eleve="STRUCTURES_ELEVE";
								foreach($structures_eleve->attributes() as $key => $value) {
									//echo("$key=".$value."<br />");
	
									if(my_strtoupper($key)=='ELEVE_ID') {
										// On teste si l'ELEVE_ID existe déjà: ça ne devrait pas arriver
										if(in_array($value,$tab_ele_id)) {
											echo "<b style='color:red;'>ANOMALIE&nbsp;:</b> Il semble qu'il y a plusieurs sections STRUCTURES_ELEVE pour l'ELEVE_ID '$value'.<br />";
										}
										else {
											$i++;
											$eleves[$i]=array();
	
											$eleves[$i]['eleve_id']=$value;
	
											$eleves[$i]["structures"]=array();
											$j=0;
											foreach($structures_eleve->children() as $structure) {
												$eleves[$i]["structures"][$j]=array();
												foreach($structure->children() as $key => $value) {
													if(in_array(my_strtoupper($key),$tab_champs_struct)) {
														$eleves[$i]["structures"][$j][my_strtolower($key)]=preg_replace("/'/","",preg_replace('/"/','',trim($value)));
													}
												}
												$j++;
											}
				
											if($debug_import=='y') {
												echo "<pre style='color:green;'><b>Tableau \$eleves[$i]&nbsp;:</b>";
												print_r($eleves[$i]);
												echo "</pre>";
											}
										}
									}
								}
							}
	
							if(!isset($eleves)) {
								echo "<p style='color:red'>Les classes d'affectation des élèves ne sont pas définies dans le fichier XML.<br />Votre secrétaire n'a pas encore remonté cette information dans Sconet... ou bien la remontée n'est pas encore prise en compte dans les XML.<br />Pendant un temps, la saisie n'était prise en compte dans les XML que le lendemain de la saisie.</p>\n";
								require("../lib/footer.inc.php");
								die();
							}
	
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
												//break;
	
												$eleves[$i]["classe"]=$eleves[$i]["structures"][$j]["code_structure"];
											}
											/*
											elseif($eleves[$i]["structures"][$j]["type_structure"]=="G") {
											// STOCKER LES GROUPES?
												$sql="INSERT INTO temp_grp SET ele_id='".$eleves[$i]['eleve_id']."', nom_grp='".mysql_real_escape_string($eleves[$i]["structures"][$j]["code_structure"])."';";
												$insert_assoc_grp=mysql_query($sql);
											}
											*/
										}
									}
								}
							}
	
							//===============================================================================
	
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
							"CODE_COMMUNE_INSEE",
							"LL_COMMUNE_INSEE"
							);
			
							$avec_scolarite_an_dernier="y";
			
							$objet_eleves=($ele_xml->DONNEES->ELEVES);
							foreach ($objet_eleves->children() as $eleve) {
								//$i++;

								//$eleves[$i]=array();

								$tmp_eleve_id="";
								foreach($eleve->attributes() as $key => $value) {
									//$eleves[$i][my_strtolower($key)]=trim($value);
									if(my_strtolower($key)=='eleve_id') {
										$tmp_eleve_id=trim($value);
									}
								}

								if($tmp_eleve_id!="") {
									// Recherche du $i de $eleves[$i] correspondant:
									$temoin_ident="non";
									for($i=0;$i<count($eleves);$i++){
										if($eleves[$i]["eleve_id"]==$tmp_eleve_id){
											$temoin_ident="oui";
											break;
										}
									}
									if($temoin_ident=="oui"){
		
										foreach($eleve->children() as $key => $value) {
											if(in_array(my_strtoupper($key),$tab_champs_eleve)) {
												//$eleves[$i][my_strtolower($key)]=preg_replace('/"/','',trim($value));
												//$eleves[$i][my_strtolower($key)]=preg_replace('/"/','',preg_replace("/'$/","",preg_replace("/^'/","",trim($value))));
												$eleves[$i][my_strtolower($key)]=preg_replace('/"/','',preg_replace("/'/","",trim($value)));
											}
					
											if(($avec_scolarite_an_dernier=='y')&&(my_strtoupper($key)=='SCOLARITE_AN_DERNIER')) {
												$eleves[$i]["scolarite_an_dernier"]=array();
								
												foreach($eleve->SCOLARITE_AN_DERNIER->children() as $key2 => $value2) {
													if(in_array(my_strtoupper($key2),$tab_champs_scol_an_dernier)) {
														$eleves[$i]["scolarite_an_dernier"][my_strtolower($key2)]=preg_replace('/"/','',trim($value2));
													}
												}
											}
										}
					
										if(isset($eleves[$i]["date_naiss"])){
											unset($naissance);
											$naissance=explode("/",$eleves[$i]["date_naiss"]);
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
					
										if($debug_import=='y') {
											echo "<pre style='color:green;'><b>Tableau \$eleves[$i]&nbsp;:</b>";
											print_r($eleves[$i]);
											echo "</pre>";
										}
									}
								}
							}
	
							//===============================================================================
	
							echo "<p>";
							echo "Analyse du fichier pour extraire les informations de la section OPTIONS...<br />\n";
							//echo "<blockquote>\n";
	
							$tab_champs_opt=array("NUM_OPTION","CODE_MODALITE_ELECT","CODE_MATIERE");
			
							//$i=-1;
			
							// PARTIE <OPTIONS>
							$objet_options=($ele_xml->DONNEES->OPTIONS);
							foreach ($objet_options->children() as $option) {
								// $option est un <OPTION ELEVE_ID="145778" ELENOET="2643">
								//$i++;
								//$eleves[$i]=array();
								$tmp_eleve_id="";
								foreach($option->attributes() as $key => $value) {
									//$eleves[$i][my_strtolower($key)]=trim($value);
									if(my_strtolower($key)=='eleve_id') {
										$tmp_eleve_id=trim($value);
									}
								}

								if($tmp_eleve_id!="") {
									// Recherche du $i de $eleves[$i] correspondant:
									$temoin_ident="non";
									for($i=0;$i<count($eleves);$i++){
										if($eleves[$i]["eleve_id"]==$tmp_eleve_id){
											$temoin_ident="oui";
											break;
										}
									}
									if($temoin_ident=="oui"){
										$eleves[$i]["options"]=array();
										$j=0;
										// $option fait référence à un élève
										// Les enfants sont des OPTIONS_ELEVE
										foreach($option->children() as $options_eleve) {
											foreach($options_eleve->children() as $key => $value) {
												// Les enfants indiquent NUM_OPTION, CODE_MODALITE_ELECT, CODE_MATIERE
												if(in_array(my_strtoupper($key),$tab_champs_opt)) {
													$eleves[$i]["options"][$j][my_strtolower($key)]=preg_replace('/"/','',trim($value));
												}
											}
											$j++;
										}
							
										if($debug_import=='y') {
											echo "<pre style='color:green;'><b>Tableau \$eleves[$i]&nbsp;:</b>";
											print_r($eleves[$i]);
											echo "</pre>";
										}
									}
								}
							}

							//===============================================================================
	
	
							$xml_file = isset($_FILES["nomenclature_xml_file"]) ? $_FILES["nomenclature_xml_file"] : NULL;
			
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
							$dest_file="../temp/".$tempdir."/nomenclature.xml";
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
		
									if(($list_file_zip[0]['size']>$unzipped_max_filesize)&&($unzipped_max_filesize>0)) {
										echo "<p style='color:red;'>Erreur : La taille du fichier extrait (<em>".$list_file_zip[0]['size']." octets</em>) dépasse la limite paramétrée (<em>$unzipped_max_filesize octets</em>).</p>\n";
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
	
							// Lecture du fichier Nomenclature... pour changer les codes numériques d'options dans 'temp_gep_import2' en leur code gestion
	
							$dest_file="../temp/".$tempdir."/nomenclature.xml";
	
							$nomenclature_xml=simplexml_load_file($dest_file);
							if(!$nomenclature_xml) {
								echo "<p style='color:red;'>ECHEC du chargement du fichier avec simpleXML.</p>\n";
								require("../lib/footer.inc.php");
								die();
							}
		
							$nom_racine=$nomenclature_xml->getName();
							if(my_strtoupper($nom_racine)!='BEE_NOMENCLATURES') {
								echo "<p style='color:red;'>ERREUR: Le fichier XML fourni n'a pas l'air d'être un fichier XML Nomenclatures.<br />Sa racine devrait être 'BEE_NOMENCLATURES'.</p>\n";
								require("../lib/footer.inc.php");
								die();
							}
	
							$tab_champs_matiere=array("CODE_GESTION",
							"LIBELLE_COURT",
							"LIBELLE_LONG",
							"LIBELLE_EDITION",
							"MATIERE_ETP"
							);
	
							echo "<p>";
							echo "Analyse du fichier pour extraire les associations CODE_MATIERE/CODE_GESTION...<br />\n";
		
							$matieres=array();
							$i=-1;
	
							$objet_matieres=($nomenclature_xml->DONNEES->MATIERES);
							foreach ($objet_matieres->children() as $matiere) {
								$i++;
								$matieres[$i]=array();
						
								foreach($matiere->attributes() as $key => $value) {
									// <MATIERE CODE_MATIERE="001400">
									$matieres[$i][my_strtolower($key)]=trim($value);
								}
		
								foreach($matiere->children() as $key => $value) {
									if(in_array(my_strtoupper($key),$tab_champs_matiere)) {
										$matieres[$i][my_strtolower($key)]=preg_replace('/"/','',trim($value));
									}
								}
							}
	
							//===============================================================================
	
							echo '
	<script type="text/javascript">//<![CDATA[
	
	//*****************************************************************************
	// Do not remove this notice.
	//
	// Copyright 2001 by Mike Hall.
	// See http://www.brainjar.com for terms of use.
	//*****************************************************************************
	
	// Determine browser and version.
	
	function Browser() {
	
	var ua, s, i;
	
	this.isIE    = false;
	this.isNS    = false;
	this.version = null;
	
	ua = navigator.userAgent;
	
	s = "MSIE";
	if ((i = ua.indexOf(s)) >= 0) {
		this.isIE = true;
		this.version = parseFloat(ua.substr(i + s.length));
		return;
	}
	
	s = "Netscape6/";
	if ((i = ua.indexOf(s)) >= 0) {
		this.isNS = true;
		this.version = parseFloat(ua.substr(i + s.length));
		return;
	}
	
	// Treat any other "Gecko" browser as NS 6.1.
	
	s = "Gecko";
	if ((i = ua.indexOf(s)) >= 0) {
		this.isNS = true;
		this.version = 6.1;
		return;
	}
	}
	
	var browser = new Browser();
	
	// Global object to hold drag information.
	
	var dragObj = new Object();
	dragObj.zIndex = 0;
	
	function dragStart(event, id) {
	
	var el;
	var x, y;
	
	// If an element id was given, find it. Otherwise use the element being
	// clicked on.
	
	if (id)
		dragObj.elNode = document.getElementById(id);
	else {
		if (browser.isIE)
		dragObj.elNode = window.event.srcElement;
		if (browser.isNS)
		dragObj.elNode = event.target;
	
		// If this is a text node, use its parent element.
	
		if (dragObj.elNode.nodeType == 3)
		dragObj.elNode = dragObj.elNode.parentNode;
	}
	
	// Get cursor position with respect to the page.
	
	if (browser.isIE) {
		x = window.event.clientX + document.documentElement.scrollLeft
		+ document.body.scrollLeft;
		y = window.event.clientY + document.documentElement.scrollTop
		+ document.body.scrollTop;
	}
	if (browser.isNS) {
		x = event.clientX + window.scrollX;
		y = event.clientY + window.scrollY;
	}
	
	// Save starting positions of cursor and element.
	
	dragObj.cursorStartX = x;
	dragObj.cursorStartY = y;
	dragObj.elStartLeft  = parseInt(dragObj.elNode.style.left, 10);
	dragObj.elStartTop   = parseInt(dragObj.elNode.style.top,  10);
	
	if (isNaN(dragObj.elStartLeft)) dragObj.elStartLeft = 0;
	if (isNaN(dragObj.elStartTop))  dragObj.elStartTop  = 0;
	
	// Update element s z-index.
	
	dragObj.elNode.style.zIndex = ++dragObj.zIndex;
	
	// Capture mousemove and mouseup events on the page.
	
	if (browser.isIE) {
		document.attachEvent("onmousemove", dragGo);
		document.attachEvent("onmouseup",   dragStop);
		window.event.cancelBubble = true;
		window.event.returnValue = false;
	}
	if (browser.isNS) {
		document.addEventListener("mousemove", dragGo,   true);
		document.addEventListener("mouseup",   dragStop, true);
		event.preventDefault();
	}
	}
	
	function dragGo(event) {
	
	var x, y;
	
	// Get cursor position with respect to the page.
	
	if (browser.isIE) {
		x = window.event.clientX + document.documentElement.scrollLeft
		+ document.body.scrollLeft;
		y = window.event.clientY + document.documentElement.scrollTop
		+ document.body.scrollTop;
	}
	if (browser.isNS) {
		x = event.clientX + window.scrollX;
		y = event.clientY + window.scrollY;
	}
	
	// Move drag element by the same amount the cursor has moved.
	
	dragObj.elNode.style.left = (dragObj.elStartLeft + x - dragObj.cursorStartX) + "px";
	dragObj.elNode.style.top  = (dragObj.elStartTop  + y - dragObj.cursorStartY) + "px";
	
	if (browser.isIE) {
		window.event.cancelBubble = true;
		window.event.returnValue = false;
	}
	if (browser.isNS)
		event.preventDefault();
	}
	
	function dragStop(event) {
	
	// Stop capturing mousemove and mouseup events.
	
	if (browser.isIE) {
		document.detachEvent("onmousemove", dragGo);
		document.detachEvent("onmouseup",   dragStop);
	}
	if (browser.isNS) {
		document.removeEventListener("mousemove", dragGo,   true);
		document.removeEventListener("mouseup",   dragStop, true);
	}
	}
	
	//]]></script>
	';
	
							echo "<h3>Affichage (d'une partie) des données ELEVES extraites:</h3>\n";
							echo "<blockquote>\n";
							echo "<table border='1'>\n";
							echo "<tr>\n";
							//echo "<th style='color: blue;'>&nbsp;</th>\n";
							echo "<th>Elenoet</th>\n";
							echo "<th>Nom</th>\n";
							echo "<th>Prénom</th>\n";
							echo "<th>Sexe</th>\n";
							echo "<th>Date de naissance</th>\n";
							echo "<th>Division</th>\n";
							echo "</tr>\n";
							$i=0;
							while($i<count($eleves)){
								echo "<tr>\n";
								//echo "<td style='color: blue;'>$cpt</td>\n";
								//echo "<td style='color: blue;'>&nbsp;</td>\n";
								echo "<td>".$eleves[$i]["elenoet"]."</td>\n";
								echo "<td>".$eleves[$i]["nom"]."</td>\n";
								echo "<td>".$eleves[$i]["prenom"]."</td>\n";
								if(isset($eleves[$i]["code_sexe"])){
									echo "<td>".$eleves[$i]["code_sexe"]."</td>\n";
								}
								else{
									echo "<td style='background-color:red'>1<a name='sexe_manquant_".$i."'></a></td>\n";
									//$remarques[]="Le sexe de l'élève <a href='#sexe_manquant_".$i."'>".$eleves[$i]["nom"]." ".$eleves[$i]["prenom"]."</a> n'est pas renseigné dans Sconet.";
								}
								echo "<td>".$eleves[$i]["date_naiss"]."</td>\n";
								echo "<td>";
								//if(isset($eleves[$i]["structures"][0]["code_structure"])){echo $eleves[$i]["structures"][0]["code_structure"];}else{echo "&nbsp;";}
								$temoin_div_trouvee="";
								if(isset($eleves[$i]["structures"])){
									if(count($eleves[$i]["structures"])>0){
										for($j=0;$j<count($eleves[$i]["structures"]);$j++){
											if($eleves[$i]["structures"][$j]["type_structure"]=="D"){
												$temoin_div_trouvee="oui";
												break;
											}
										}
										if($temoin_div_trouvee==""){
											echo "&nbsp;";
										}
										else{
											echo $eleves[$i]["structures"][$j]["code_structure"];
											$eleves[$i]["classe"]=$eleves[$i]["structures"][$j]["code_structure"];
		
											if(!isset($eleves[$i]["code_sexe"])){
												$remarques[]="Le sexe de l'élève <a href='#sexe_manquant_".$i."'>".$eleves[$i]["nom"]." ".$eleves[$i]["prenom"]."</a> n'est pas renseigné dans Sconet.";
											}
										}
									}
									else{
										echo "&nbsp;";
									}
								}
								else{
									echo "&nbsp;";
								}
								echo "</td>\n";
								echo "</tr>\n";
								$i++;
							}
							echo "</table>\n";
							echo "</blockquote>\n";
	
	
							echo "<h3>Affichage des données MATIERES extraites:</h3>\n";
							echo "<blockquote>\n";
							echo "<table border='1'>\n";
							echo "<tr>\n";
							for($i=0;$i<count($tab_champs_matiere);$i++){
								echo "<th>$tab_champs_matiere[$i]</th>\n";
							}
							echo "</tr>\n";
							$i=0;
							while($i<count($matieres)){
								echo "<tr>\n";
								for($j=0;$j<count($tab_champs_matiere);$j++){
									$tmpmin=mb_strtolower($tab_champs_matiere[$j]);
									echo "<td>".$matieres[$i]["$tmpmin"]."</td>\n";
								}
								echo "</tr>\n";
								$i++;
							}
							echo "</table>\n";
							echo "</blockquote>\n";
	
	
							function ouinon($nombre){
								if($nombre==1){return "O";}elseif($nombre==0){return "N";}else{return "";}
							}
							function sexeMF($nombre){
								//if($nombre==2){return "F";}else{return "M";}
								if($nombre==2){return "F";}elseif($nombre==1){return "M";}else{return "";}
							}
	
							// Génération d'un eleves.csv
							//echo "<h3><a name='csv'></a>Génération d'un fichier F_ELE.CSV</h3>\n";
							echo "<h3><a name='csv'></a>Génération d'un fichier ELEVES.CSV</h3>\n";
							echo "<blockquote>\n";
							echo "<p>A la place de l'ERENO, je mets l'ELEVE_ID (<i>ce n'est pas l'équivalent, mais c'est lui qui est utilisé pour le lien entre le ElevesAvecAdresses.xml et le Responsables.xml</i>).</p>\n";
	
							$fich=fopen("../backup/$dirname/csv/eleves.csv","w+");
							fwrite($fich,"ELENOM;ELEPRE;ELESEXE;ELEDATNAIS;ELENOET;ELE_ID;ELEDOUBL;ELENONAT;ELEREG;DIVCOD;ETOCOD_EP;ELEOPT1;ELEOPT2;ELEOPT3;ELEOPT4;ELEOPT5;ELEOPT6;ELEOPT7;ELEOPT8;ELEOPT9;ELEOPT10;ELEOPT11;ELEOPT12\n");
	
							echo "<table border='1'>\n";
							echo "<tr>\n";
							//echo "<th>Id_tempo</th>\n";
							//echo "<th>Login</th>\n";
							echo "<th>Elenom</th>\n";
							echo "<th>Elepre</th>\n";
							echo "<th>Elesexe</th>\n";
							echo "<th>Eledatnais</th>\n";
							echo "<th>Elenoet</th>\n";
							echo "<th>Ereno/eleve_id</th>\n";
							echo "<th>Eledoubl</th>\n";
							echo "<th>Elenonat</th>\n";
							echo "<th>Elereg</th>\n";
							echo "<th>Divcod</th>\n";
							echo "<th>Etocod_ep</th>\n";
							echo "<th>Eleopt1</th>\n";
							echo "<th>Eleopt2</th>\n";
							echo "<th>Eleopt3</th>\n";
							echo "<th>Eleopt4</th>\n";
							echo "<th>Eleopt5</th>\n";
							echo "<th>Eleopt6</th>\n";
							echo "<th>Eleopt7</th>\n";
							echo "<th>Eleopt8</th>\n";
							echo "<th>Eleopt9</th>\n";
							echo "<th>Eleopt10</th>\n";
							echo "<th>Eleopt11</th>\n";
							echo "<th>Eleopt12</th>\n";
							echo "</tr>\n";
							$i=0;
							while($i<count($eleves)){
								if(isset($eleves[$i]["structures"][0]["code_structure"])){
									if($eleves[$i]["structures"][0]["code_structure"]!=""){
										echo "<tr>\n";
										//echo "<td></td>\n";
										//echo "<td></td>\n";
										echo "<td>".$eleves[$i]["nom"]."</td>\n";
										echo "<td>".$eleves[$i]["prenom"]."</td>\n";
										if(isset($eleves[$i]["code_sexe"])){
											echo "<td>".sexeMF($eleves[$i]["code_sexe"])."</td>\n";
										}
										else{
											echo "<td style='background-color:red;'>M</td>\n";
										}
										echo "<td>".$eleves[$i]["date_naiss"]."</td>\n";
										echo "<td>".$eleves[$i]["elenoet"]."</td>\n";
										echo "<td>".$eleves[$i]["eleve_id"]."</td>\n";
										echo "<td>".ouinon($eleves[$i]["doublement"])."</td>\n";
										echo "<td>";
										if(isset($eleves[$i]["id_national"])){
											echo $eleves[$i]["id_national"];
										}
										else{echo "&nbsp;";}
										echo "</td>\n";
										echo "<td>";
										if(isset($eleves[$i]["code_regime"])){
											echo $eleves[$i]["code_regime"];
										}
										else{echo "&nbsp;";}
										echo "</td>\n";
										echo "<td>";
										/*
										if(isset($eleves[$i]["structures"][0]["code_structure"])){
											echo $eleves[$i]["structures"][0]["code_structure"];
										}
										*/
										if(isset($eleves[$i]["classe"])){
											echo $eleves[$i]["classe"];
										}
										else{echo "&nbsp;";}
										echo "</td>\n";
	
										echo "<td>";
										if(isset($eleves[$i]["scolarite_an_dernier"]["code_rne"])){
											echo $eleves[$i]["scolarite_an_dernier"]["code_rne"];
										}
										else{echo "&nbsp;";}
										echo "</td>\n";
	
										/*
										$chaine=$eleves[$i]["nom"].";".
										$eleves[$i]["prenom"].";".
										sexeMF($eleves[$i]["code_sexe"]).";".
										$eleves[$i]["date_naiss"].";".
										$eleves[$i]["elenoet"].";".
										$eleves[$i]["eleve_id"].";".
										ouinon($eleves[$i]["doublement"]).";".
										$eleves[$i]["id_national"].";".
										$eleves[$i]["code_regime"].";".
										$eleves[$i]["structures"][0]["code_structure"].";".
										$eleves[$i]["scolarite_an_dernier"]["code_rne"].";";
										*/
	
										$chaine=$eleves[$i]["nom"].";".
										$eleves[$i]["prenom"].";";
										if(isset($eleves[$i]["code_sexe"])){
											$chaine.=sexeMF($eleves[$i]["code_sexe"]).";";
										}
										else{
											$chaine.="M;";
										}
										$chaine.=$eleves[$i]["date_naiss"].";".
										$eleves[$i]["elenoet"].";".
										$eleves[$i]["eleve_id"].";".
										ouinon($eleves[$i]["doublement"]).";";
										if(isset($eleves[$i]["id_national"])){$chaine.=$eleves[$i]["id_national"];}
										$chaine.=";";
										if(isset($eleves[$i]["code_regime"])){$chaine.=$eleves[$i]["code_regime"];}
										$chaine.=";";
										//if(isset($eleves[$i]["structures"][0]["code_structure"])){$chaine.=$eleves[$i]["structures"][0]["code_structure"];}
	
										if(isset($eleves[$i]["classe"])){
											$chaine.=$eleves[$i]["classe"];
										}
	
										$chaine.=";";
										if(isset($eleves[$i]["scolarite_an_dernier"]["code_rne"])){$chaine.=$eleves[$i]["scolarite_an_dernier"]["code_rne"];}
										$chaine.=";";
	
	
										for($j=0;$j<count($eleves[$i]["options"]);$j++){
											//$tab_champs_opt=array("NUM_OPTION","CODE_MODALITE_ELECT","CODE_MATIERE");
											//$eleves[$i]["options"][$j]["$tmpmin"]=extr_valeur($ligne[$cpt]);
											$eleopt="";
											for($k=0;$k<count($matieres);$k++){
												if($matieres[$k]["code_matiere"]==$eleves[$i]["options"][$j]["code_matiere"]){
													$eleopt=$matieres[$k]["code_gestion"];
													break;
												}
											}
											echo "<td>".$eleopt."</td>\n";
											$chaine.=$eleopt.";";
										}
										for($m=$j;$m<12;$m++){
											echo "<td>&nbsp;</td>\n";
											$chaine.=";";
										}
										echo "</tr>\n";
										$chaine=mb_substr($chaine,0,mb_strlen($chaine)-1);
										fwrite($fich,$chaine."\n");
									}
								}
								$i++;
							}
							echo "</table>\n";
							fclose($fich);
							echo "<p><a href='save_csv.php?fileid=5'>eleves.csv</a></p>\n";
							echo "</blockquote>\n";
	
	
							// Génération d'un etablissements.csv
							echo "<h3>Génération d'un fichier etablissements.csv</h3>\n";
							echo "<blockquote>\n";

							function maj_min_comp($chaine){
								$tmp_tab1=explode(" ",$chaine);
								$new_chaine="";
								for($i=0;$i<count($tmp_tab1);$i++){
									$tmp_tab2=explode("-",$tmp_tab1[$i]);
									$new_chaine.=ucfirst(mb_strtolower($tmp_tab2[0]));
									for($j=1;$j<count($tmp_tab2);$j++){
										$new_chaine.="-".ucfirst(mb_strtolower($tmp_tab2[$j]));
									}
									$new_chaine.=" ";
								}
								$new_chaine=trim($new_chaine);
								return $new_chaine;
							}
	
	
							$fich=fopen("../backup/$dirname/csv/etablissements.csv","w+");
	
							//fwrite($fich,"CODE_RNE;DENOM_COMPL;niveau;type;code_postal;LL_COMMUNE_INSEE\n");
							fwrite($fich,"CODE_RNE;DENOM_COMPL;niveau;type;CODE_COMMUNE_INSEE;LL_COMMUNE_INSEE\n");
							// RNE, Nom étab, ecole/college/lycee, public/prive, CP, ville
	
							echo "<table border='1'>\n";
							echo "<tr>\n";
							/*
							for($i=0;$i<count($tab_champs_scol_an_dernier);$i++){
								echo "<th>$tab_champs_scol_an_dernier[$i]</th>\n";
							}
							*/
							echo "<th>RNE</th>\n";
							echo "<th>Nom</th>\n";
							echo "<th>Niveau</th>\n";
							echo "<th>Type</th>\n";
							echo "<th>Code postal</th>\n";
							echo "<th>Commune</th>\n";
							echo "</tr>\n";
							$i=0;
							while($i<count($eleves)){
								// Ligne commentée pour ne pas exclure des établissements parce qu'un élève y est passé et a quitté le notre.
								//if($eleves[$i]["structures"][0]["code_structure"]!=""){
									$temoin_tmp="";
									$chaine="";
									for($k=0;$k<$i;$k++){
										if((isset($eleves[$k]["scolarite_an_dernier"]["code_rne"]))&&(isset($eleves[$i]["scolarite_an_dernier"]["code_rne"]))){
											if($eleves[$k]["scolarite_an_dernier"]["code_rne"]==$eleves[$i]["scolarite_an_dernier"]["code_rne"]){$temoin_tmp="oui";}
										}
									}
									if($temoin_tmp!="oui"){
										if(isset($eleves[$i]["scolarite_an_dernier"]["code_rne"])){
											if($eleves[$i]["scolarite_an_dernier"]["code_rne"]!=""){
												echo "<tr>\n";
												//$chaine="";
												//echo "<td>$i: ".$eleves[$i]["nom"]."</td>\n";
												
												// RNE
												echo "<td>";
												if(isset($eleves[$i]["scolarite_an_dernier"]["code_rne"])){
													echo $eleves[$i]["scolarite_an_dernier"]["code_rne"];
													$chaine.=$eleves[$i]["scolarite_an_dernier"]["code_rne"];
												}
												else{
													echo "&nbsp;";
												}
												echo "</td>\n";
												$chaine.=";";
	
												// NOM
												echo "<td>";
												if(isset($eleves[$i]["scolarite_an_dernier"]["denom_compl"])){
													echo maj_min_comp($eleves[$i]["scolarite_an_dernier"]["denom_compl"]);
													$chaine.=maj_min_comp($eleves[$i]["scolarite_an_dernier"]["denom_compl"]);
												}
												else{
													echo "&nbsp;";
												}
												echo "</td>\n";
												$chaine.=";";
	
												// NIVEAU
												echo "<td>";
												if(isset($eleves[$i]["scolarite_an_dernier"]["denom_princ"])){
													if(my_ereg("ECOLE",$eleves[$i]["scolarite_an_dernier"]["denom_princ"])){
														echo "ecole";
														$chaine.="ecole";
													}
													elseif(my_ereg("COLLEGE",$eleves[$i]["scolarite_an_dernier"]["denom_princ"])){
														echo "college";
														$chaine.="college";
													}
													elseif(my_ereg("LYCEE",$eleves[$i]["scolarite_an_dernier"]["denom_princ"])){
														if(my_ereg("PROF",$eleves[$i]["scolarite_an_dernier"]["denom_princ"])){
															echo "lprof";
															$chaine.="lprof";
														}
														else{
															echo "lycee";
															$chaine.="lycee";
														}
													}
													else{
														echo "&nbsp;";
														$chaine.="";
													}
												}
												else{
													echo "&nbsp;";
												}
												echo "</td>\n";
												$chaine.=";";
	
												// TYPE
												echo "<td>";
												if(isset($eleves[$i]["scolarite_an_dernier"]["denom_princ"])){
													if(my_ereg("PRIVE",$eleves[$i]["scolarite_an_dernier"]["denom_princ"])){
														echo "prive";
														$chaine.="prive";
													}
													else{
														echo "public";
														$chaine.="public";
													}
												}
												else{
													echo "&nbsp;";
												}
												echo "</td>\n";
												$chaine.=";";
	
												// CODE POSTAL: Non présent dans le fichier ElevesSansAdresses.xml
												//              Ca y est, il a été ajouté.
												// Il faudrait le fichier Communes.xml ou quelque chose de ce genre.
												echo "<td>";
												// ERREUR: Le code_commune_insee est différent du code postal
												/*
												if(isset($eleves[$i]["scolarite_an_dernier"]["code_commune_insee"])){
													echo $eleves[$i]["scolarite_an_dernier"]["code_commune_insee"];
													$chaine.=$eleves[$i]["scolarite_an_dernier"]["code_commune_insee"];
												}
												else{
												*/
													echo "&nbsp;";
												//}
												echo "</td>\n";
												$chaine.=";";
	
												// COMMUNE
												echo "<td>";
												if(isset($eleves[$i]["scolarite_an_dernier"]["ll_commune_insee"])){
													echo maj_min_comp($eleves[$i]["scolarite_an_dernier"]["ll_commune_insee"]);
													$chaine.=maj_min_comp($eleves[$i]["scolarite_an_dernier"]["ll_commune_insee"]);
												}
												else{
													echo "&nbsp;";
												}
												echo "</td>\n";
												$chaine.=";";
	
												echo "</tr>\n";
											}
											$chaine=mb_substr($chaine,0,strlen($chaine)-1);
											fwrite($fich,$chaine."\n");
										}
									}
								//}
								$i++;
							}
	
							echo "</table>\n";
							fclose($fich);
							echo "<p><a href='save_csv.php?fileid=9'>etablissements.csv</a></p>\n";
							echo "</blockquote>\n";
	
	
							// Génération d'un etablissements.csv
							echo "<h3>Génération d'un fichier eleve_etablissement.csv</h3>\n";
							echo "<blockquote>\n";
	
							$fich=fopen("../backup/$dirname/csv/eleve_etablissement.csv","w+");
							fwrite($fich,"ELENOET;CODE_RNE\n");
	
							echo "<table border='1'>\n";
							echo "<tr>\n";
							echo "<th>ELENOET</th>\n";
							echo "<th>CODE_RNE</th>\n";
							echo "</tr>\n";
							$i=0;
							while($i<count($eleves)){
								if(isset($eleves[$i]["structures"][0]["code_structure"])){
									if($eleves[$i]["structures"][0]["code_structure"]!=""){
										if(isset($eleves[$i]["scolarite_an_dernier"]["code_rne"])){
											if(($eleves[$i]["elenoet"]!="")&&($eleves[$i]["scolarite_an_dernier"]["code_rne"]!="")){
												echo "<tr>\n";
												echo "<td>".$eleves[$i]["elenoet"]."</td>\n";
												echo "<td>".$eleves[$i]["scolarite_an_dernier"]["code_rne"]."</td>\n";
												echo "</tr>\n";
												$chaine=$eleves[$i]["elenoet"].";".$eleves[$i]["scolarite_an_dernier"]["code_rne"];
												fwrite($fich,$chaine."\n");
											}
										}
									}
								}
								$i++;
							}
							echo "</table>\n";
							fclose($fich);
							echo "<p><a href='save_csv.php?fileid=10'>eleve_etablissement.csv</a></p>\n";
							echo "</blockquote>\n";
	
							//echo "<div style='position:absolute; top: 70px; left: 300px; width: 350px; background: yellow; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-top: 0; '>\n";
							echo "<div id='boxInfo' style='position:absolute; top: 70px; left: 300px; width: 400px; background: yellow; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-top: 0;'  onmousedown=\"dragStart(event, 'boxInfo')\">\n";
							echo "<h4 style='margin:0; padding:0; text-align:center;'>GEPI</h4>\n";
							//echo "<p style='margin-top: 0;'>Effectuez un Clic-droit/Enregistrer la cible du lien sous... pour chacun des fichiers ci-dessous.</p>\n";
							echo "<p style='margin-top: 0;'>Récupérez les CSV suivants (<i>pas par clic-droit</i>).</p>\n";
							echo "<table border='0'>\n";
							echo "<tr><td>Fichier Elèves:</td><td><a href='save_csv.php?fileid=5'>eleves.csv</a></td></tr>\n";
							echo "<tr><td>Fichier Etablissements:</td><td><a href='save_csv.php?fileid=9'>etablissements.csv</a></td></tr>\n";
							echo "<tr><td>Fichier Elève/Etablissement:</td><td><a href='save_csv.php?fileid=10'>eleve_etablissement.csv</a></td></tr>\n";
							echo "</table>\n";
							echo "<p>Pour supprimer les fichiers après récupération: <a href='".$_SERVER['PHP_SELF']."?nettoyage=oui".add_token_in_url()."'>Nettoyage</a></p>\n";
	
							if(count($remarques)>0){
								echo "<p><b>Attention:</b> Des anomalies ont été relevées.<br />Suivez ce lien pour en <a href='#remarques'>consulter le détail</a></p>";
							}
	
							echo "</div>\n";
						}
						else {
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
		
									if(($list_file_zip[0]['size']>$unzipped_max_filesize)&&($unzipped_max_filesize>0)) {
										echo "<p style='color:red;'>Erreur : La taille du fichier extrait (<em>".$list_file_zip[0]['size']." octets</em>) dépasse la limite paramétrée (<em>$unzipped_max_filesize octets</em>).</p>\n";
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

							echo "<p>La copie du fichier vers le dossier temporaire a réussi.</p>\n";

							$resp_xml=simplexml_load_file($dest_file);
							if(!$resp_xml) {
								echo "<p style='color:red;'>ECHEC du chargement du fichier avec simpleXML.</p>\n";
								require("../lib/footer.inc.php");
								die();
							}
	
							$nom_racine=$resp_xml->getName();
							if(my_strtoupper($nom_racine)!='BEE_RESPONSABLES') {
								echo "<p style='color:red;'>ERREUR: Le fichier XML fourni n'a pas l'air d'être un fichier XML Responsables.<br />Sa racine devrait être 'BEE_RESPONSABLES'.</p>\n";
								require("../lib/footer.inc.php");
								die();
							}
	
							echo "<p>Analyse du fichier pour extraire les informations de la section PERSONNES...<br />\n";
	
							$personnes=array();
	
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
	
							$objet_personnes=($resp_xml->DONNEES->PERSONNES);
							foreach ($objet_personnes->children() as $personne) {
	
								$i++;
								$personnes[$i]=array();
	
								foreach($personne->attributes() as $key => $value) {
									// <PERSONNE PERSONNE_ID="294435">
									//$personnes[$i][my_strtolower($key)]=trim($value);
									$personnes[$i][my_strtolower($key)]=trim(nettoyer_caracteres_nom($value, "an", " .@'-", ""));
								}
	
								foreach($personne->children() as $key => $value) {
									if(in_array(my_strtoupper($key),$tab_champs_personne)) {
										$personnes[$i][my_strtolower($key)]=nettoyer_caracteres_nom(preg_replace('/"/',' ',preg_replace("/'$/","",preg_replace("/^'/"," ",$value))), "an", " .@'_-", "");
									}
								}
	
								if($debug_import=='y') {
									echo "<pre style='color:green;'><b>Tableau \$personnes[$i]&nbsp;:</b>";
									print_r($personnes[$i]);
									echo "</pre>";
								}
							}

							//=========================================================================

							echo "<p>";
							echo "Analyse du fichier pour extraire les informations de la section RESPONSABLES...<br />\n";
			
							$responsables=array();
			
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
			
							$objet_resp=($resp_xml->DONNEES->RESPONSABLES);
							foreach ($objet_resp->children() as $responsable_eleve) {
			
								$i++;
								$responsables[$i]=array();
			
								foreach($responsable_eleve->children() as $key => $value) {
									if(in_array(my_strtoupper($key),$tab_champs_responsable)) {
										//$responsables[$i][my_strtolower($key)]=nettoyer_caracteres_nom(preg_replace('/"/',' ',preg_replace("/'/"," ",$value)), "an", " .@'-", "");
										$responsables[$i][my_strtolower($key)]=preg_replace('/[^0-9]/', '', $value);
									}
								}
			
								if($debug_import=='y') {
									echo "<pre style='color:green;'><b>Tableau \$responsables[$i]&nbsp;:</b>";
									print_r($responsables[$i]);
									echo "</pre>";
								}
							}

							//=========================================================================

							echo "Analyse du fichier pour extraire les informations de la section ADRESSES...<br />\n";
			
							$adresses=array();
			
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
			
							$objet_adresses=($resp_xml->DONNEES->ADRESSES);
							foreach ($objet_adresses->children() as $adresse) {
			
								$i++;
								$adresses[$i]=array();
			
								foreach($adresse->attributes() as $key => $value) {
									// <ADRESSE ADRESSE_ID="228114">
									$adresses[$i][my_strtolower($key)]=trim($value);
								}
			
								foreach($adresse->children() as $key => $value) {
									if(in_array(my_strtoupper($key),$tab_champs_adresse)) {
										$adresses[$i][my_strtolower($key)]=nettoyer_caracteres_nom(preg_replace('/"/',' ',preg_replace("/'/"," ",$value)), "an", " .'-", " ");
									}
								}
			
								if($debug_import=='y') {
									echo "<pre style='color:green;'><b>Tableau \$adresses[$i]&nbsp;:</b>";
									print_r($adresses[$i]);
									echo "</pre>";
								}
							}


							echo "<h3>Affichage des données Personnes extraites:</h3>\n";
							echo "<blockquote>\n";
							echo "<table border='1'>\n";
							echo "<tr>\n";
							for($i=0;$i<count($tab_champs_personne);$i++){
								echo "<th>$tab_champs_personne[$i]</th>\n";
							}
							echo "</tr>\n";
							$i=0;
							while($i<count($personnes)){
								echo "<tr>\n";
								for($j=0;$j<count($tab_champs_personne);$j++){
									$tmpmin=mb_strtolower($tab_champs_personne[$j]);
									echo "<td>";
									if(isset($personnes[$i]["$tmpmin"])){
										echo $personnes[$i]["$tmpmin"];
									}
									else{echo "&nbsp;";}
									echo "</td>\n";
								}
								echo "</tr>\n";
								$i++;
							}
							echo "</table>\n";
							echo "</blockquote>\n";


							echo "<h3>Affichage des données Responsables extraites:</h3>\n";
							echo "<blockquote>\n";
							echo "<table border='1'>\n";
							echo "<tr>\n";
							for($i=0;$i<count($tab_champs_responsable);$i++){
								echo "<th>$tab_champs_responsable[$i]</th>\n";
							}
							echo "</tr>\n";
							$i=0;
							while($i<count($responsables)){
								echo "<tr>\n";
								for($j=0;$j<count($tab_champs_responsable);$j++){
									$tmpmin=mb_strtolower($tab_champs_responsable[$j]);
									echo "<td>";
									if(isset($responsables[$i]["$tmpmin"])){
										echo $responsables[$i]["$tmpmin"];
									}
									else{echo "&nbsp;";}
									echo "</td>\n";
								}
								echo "</tr>\n";
								$i++;
							}
							echo "</table>\n";
							echo "</blockquote>\n";



							echo "<h3>Affichage des données Adresses extraites:</h3>\n";
							echo "<blockquote>\n";
							echo "<table border='1'>\n";
							echo "<tr>\n";
							for($i=0;$i<count($tab_champs_adresse);$i++){
								echo "<th>$tab_champs_adresse[$i]</th>\n";
							}
							echo "</tr>\n";
							$i=0;
							while($i<count($adresses)){
								echo "<tr>\n";
								for($j=0;$j<count($tab_champs_adresse);$j++){
									$tmpmin=mb_strtolower($tab_champs_adresse[$j]);
									echo "<td>";
									if(isset($adresses[$i]["$tmpmin"])){
										echo $adresses[$i]["$tmpmin"];
									}
									else{echo "&nbsp;";}
									echo "</td>\n";
								}
								echo "</tr>\n";
								$i++;
							}
							echo "</table>\n";
							echo "</blockquote>\n";

							//============================================================

							echo "<h3><a name='csv'></a>Génération de trois fichiers CSV</h3>\n";
							echo "<blockquote>\n";

							echo "<p>Personnes:</p>\n";
							$fich=fopen("../backup/$dirname/csv/personnes.csv","w+");
							//fwrite($fich,"pers_id;nom;prenom;tel_pers;tel_port;tel_prof;mel;adr_id\n");
							fwrite($fich,"pers_id;nom;prenom;civilite;tel_pers;tel_port;tel_prof;mel;adr_id\n");
							echo "<table border='1'>\n";
							echo "<tr>\n";
							echo "<th>Identifiant</th>\n";
							echo "<th>Nom</th>\n";
							echo "<th>Prenom</th>\n";
							echo "<th>Civilité</th>\n";
							echo "<th>Tel_personnel</th>\n";
							echo "<th>Tel_portable</th>\n";
							echo "<th>Tel_professionnel</th>\n";
							echo "<th>Mel</th>\n";
							//echo "<th>Accepte_sms</th>\n";
							echo "<th>Adresse_id</th>\n";
							//echo "<th>Code_profession</th>\n";
							//echo "<th>Communication_adresse</th>\n";
							echo "</tr>\n";
							//$tabtmppersonnes=array("personne_id","nom","prenom","tel_personnel","tel_portable","tel_professionnel","mel","adresse_id");
							$tabtmppersonnes=array("personne_id","nom","prenom","lc_civilite","tel_personnel","tel_portable","tel_professionnel","mel","adresse_id");
							$i=0;
							while($i<count($personnes)){
								/*
								echo "<tr>\n";
								echo "<td>".$personnes[$i]["personne_id"]."</td>\n";
								echo "<td>".$personnes[$i]["nom"]."</td>\n";
								echo "<td>".$personnes[$i]["prenom"]."</td>\n";
								echo "<td>".$personnes[$i]["tel_personnel"]."</td>\n";
								echo "<td>".$personnes[$i]["tel_portable"]."</td>\n";
								echo "<td>".$personnes[$i]["tel_professionnel"]."</td>\n";
								echo "<td>".$personnes[$i]["mel"]."</td>\n";
								//echo "<td>".$personnes[$i]["accepte_sms"]."</td>\n";
								echo "<td>".$personnes[$i]["adresse_id"]."</td>\n";
								//echo "<td>".$personnes[$i]["communication_adresse"]."</td>\n";
								echo "</tr>\n";
								fwrite($fich,$personnes[$i]["personne_id"].";".
									$personnes[$i]["nom"].";".
									$personnes[$i]["prenom"].";".
									$personnes[$i]["tel_personnel"].";".
									$personnes[$i]["tel_portable"].";".
									$personnes[$i]["tel_professionnel"].";".
									$personnes[$i]["mel"].";".
									$personnes[$i]["adresse_id"]."\n");
								*/

								echo "<tr>\n";
								echo "<td>";
								$cptloop=0;
								if(isset($personnes[$i][$tabtmppersonnes[$cptloop]])){
									echo $personnes[$i][$tabtmppersonnes[$cptloop]];
									fwrite($fich,$personnes[$i][$tabtmppersonnes[$cptloop]]);
								}
								else{echo "&nbsp;";}
								echo "</td>\n";
								for($cptloop=1;$cptloop<count($tabtmppersonnes);$cptloop++){
									echo "<td>";
									if(isset($personnes[$i][$tabtmppersonnes[$cptloop]])){
										echo $personnes[$i][$tabtmppersonnes[$cptloop]];
										fwrite($fich,";".$personnes[$i][$tabtmppersonnes[$cptloop]]);
									}
									else{
										echo "&nbsp;";
										fwrite($fich,";");
									}
									echo "</td>\n";
								}
								fwrite($fich,"\n");
								echo "</tr>\n";

								$i++;
							}
							echo "</table>\n";
							fclose($fich);
							echo "<p><a href='save_csv.php?fileid=6'>personnes.csv</a></p>\n";

							echo "<p>Responsables:</p>\n";
							$fich=fopen("../backup/$dirname/csv/responsables.csv","w+");
							fwrite($fich,"ele_id;pers_id;resp_legal;pers_contact\n");
							echo "<table border='1'>\n";
							echo "<tr>\n";
							echo "<th>Eleve_id</th>\n";
							echo "<th>Personne_id</th>\n";
							echo "<th>Resp_legal</th>\n";
							echo "<th>Pers_contact</th>\n";
							echo "</tr>\n";
							$i=0;
							while($i<count($responsables)){
								echo "<tr>\n";
								echo "<td>".$responsables[$i]["eleve_id"]."</td>\n";
								echo "<td>".$responsables[$i]["personne_id"]."</td>\n";
								echo "<td>".$responsables[$i]["resp_legal"]."</td>\n";
								echo "<td>".$responsables[$i]["pers_contact"]."</td>\n";
								echo "</tr>\n";
								fwrite($fich,$responsables[$i]["eleve_id"].";".
									$responsables[$i]["personne_id"].";".
									$responsables[$i]["resp_legal"].";".
									$responsables[$i]["pers_contact"]."\n");
								$i++;
							}
							echo "</table>\n";
							fclose($fich);
							echo "<p><a href='save_csv.php?fileid=7'>responsables.csv</a></p>\n";


							echo "<p>Adresses:</p>\n";
							$fich=fopen("../backup/$dirname/csv/adresses.csv","w+");
							fwrite($fich,"adr_id;adr1;adr2;adr3;adr4;cp;pays;commune\n");
							echo "<table border='1'>\n";
							echo "<tr>\n";
							echo "<th>Identifiant adresse</th>\n";
							echo "<th>Ligne1_adresse</th>\n";
							echo "<th>Ligne2_adresse</th>\n";
							echo "<th>Ligne3_adresse</th>\n";
							echo "<th>Ligne4_adresse</th>\n";
							echo "<th>Code_postal</th>\n";
							echo "<th>Ll_pays</th>\n";
							//echo "<th>Code_departement</th>\n";
							echo "<th>Libelle_postal</th>\n";
							echo "</tr>\n";

							$tabtmpadresses=array("adresse_id","ligne1_adresse","ligne2_adresse","ligne3_adresse","ligne4_adresse","code_postal","ll_pays","libelle_postal");

							$i=0;
							while($i<count($adresses)){
								/*
								echo "<tr>\n";
								echo "<td>".$adresses[$i]["adresse_id"]."</td>\n";
								echo "<td>".$adresses[$i]["ligne1_adresse"]."</td>\n";
								echo "<td>".$adresses[$i]["ligne2_adresse"]."</td>\n";
								echo "<td>".$adresses[$i]["ligne3_adresse"]."</td>\n";
								echo "<td>".$adresses[$i]["ligne4_adresse"]."</td>\n";
								echo "<td>".$adresses[$i]["code_postal"]."</td>\n";
								echo "<td>".$adresses[$i]["ll_pays"]."</td>\n";
								//echo "<td>".$adresses[$i]["code_departement"]."</td>\n";
								echo "<td>".$adresses[$i]["libelle_postal"]."</td>\n";
								echo "</tr>\n";
								fwrite($fich,$adresses[$i]["adresse_id"].";".
									$adresses[$i]["ligne1_adresse"].";".
									$adresses[$i]["ligne2_adresse"].";".
									$adresses[$i]["ligne3_adresse"].";".
									$adresses[$i]["ligne4_adresse"].";".
									$adresses[$i]["code_postal"].";".
									$adresses[$i]["ll_pays"].";".
									$adresses[$i]["libelle_postal"]."\n");
								*/

								echo "<tr>\n";
								echo "<td>";
								$cptloop=0;
								if(isset($adresses[$i][$tabtmpadresses[$cptloop]])){
									echo $adresses[$i][$tabtmpadresses[$cptloop]];
									fwrite($fich,$adresses[$i][$tabtmpadresses[$cptloop]]);
								}
								else{echo "&nbsp;";}
								echo "</td>\n";
								for($cptloop=1;$cptloop<count($tabtmpadresses);$cptloop++){
									echo "<td>";
									if(isset($adresses[$i][$tabtmpadresses[$cptloop]])){
										echo $adresses[$i][$tabtmpadresses[$cptloop]];
										fwrite($fich,";".$adresses[$i][$tabtmpadresses[$cptloop]]);
									}
									else{
										echo "&nbsp;";
										fwrite($fich,";");
									}
									echo "</td>\n";
								}
								fwrite($fich,"\n");
								echo "</tr>\n";


								$i++;
							}
							echo "</table>\n";
							fclose($fich);
							echo "<p><a href='save_csv.php?fileid=8'>adresses.csv</a></p>\n";
							echo "</blockquote>\n";









							//echo "<div style='position:absolute; top: 70px; left: 300px; width: 300px; background: yellow; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-top: 0; '>\n";
							echo "<div id='boxInfo' style='position:absolute; top: 70px; left: 300px; width: 300px; background: yellow; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-top: 0;'  onmousedown=\"dragStart(event, 'boxInfo')\">\n";

							echo "<h4 style='margin:0; padding:0; text-align:center;'>GEPI</h4>\n";
							//echo "<p style='margin-top: 0;'>Effectuez un Clic-droit/Enregistrer la cible du lien sous... pour chacun des fichiers ci-dessous.</p>\n";
							echo "<p style='margin-top: 0;'>Récupérez les CSV suivants (<i>pas par clic-droit</i>).</p>\n";
							echo "<table border='0'>\n";
							echo "<tr><td>Fichier Personnes Responsables:</td><td><a href='save_csv.php?fileid=6'>personnes.csv</a></td></tr>\n";
							echo "<tr><td>Fichier Responsables:</td><td><a href='save_csv.php?fileid=7'>responsables.csv</a></td></tr>\n";
							echo "<tr><td>Fichier Adresses:</td><td><a href='save_csv.php?fileid=8'>adresses.csv</a></td></tr>\n";
							echo "</table>\n";
							echo "<p>Pour supprimer les fichiers après récupération: <a href='".$_SERVER['PHP_SELF']."?nettoyage=oui".add_token_in_url()."'>Nettoyage</a></p>\n";
							if(count($remarques)>0){
								echo "<p><b>Attention:</b> Des anomalies ont été relevées.<br />Suivez ce lien pour en <a href='#remarques'>consulter le détail</a></p>";
							}
							echo "</div>\n";

						}


						if(count($remarques)>0){
							echo "<a name='remarques'></a><h3>Remarques</h3>\n";
							if(count($remarques)==1){
								echo "<p>Une anomalie a été notée lors du parcours de vos fichiers:</p>\n";
							}
							else{
								echo "<p>Des anomalies ont été notées lors du parcours de vos fichiers:</p>\n";
							}
							echo "<ul>\n";
							for($i=0;$i<count($remarques);$i++){
								echo "<li>".$remarques[$i]."</li>\n";
							}
							echo "</ul>\n";
						}

					}
				}
			}
		?>
		<!--p>Retour à l'<a href="index.php">index</a></p-->
	</div>
<?php require("../lib/footer.inc.php");?>
