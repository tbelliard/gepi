<?php
	@set_time_limit(0);


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

	//**************** EN-TETE *****************
	$titre_page = "Outil d'initialisation de l'année : Importation des matières";
	require_once("../lib/header.inc.php");
	//**************** FIN EN-TETE *****************

	require_once("init_xml_lib.php");

		// Etape...
	$step=isset($_POST['step']) ? $_POST['step'] : (isset($_GET['step']) ? $_GET['step'] : NULL);

	$verif_tables_non_vides=isset($_POST['verif_tables_non_vides']) ? $_POST['verif_tables_non_vides'] : NULL;

	if(isset($_GET['ad_retour'])){
		$_SESSION['ad_retour']=$_GET['ad_retour'];
	}
	//echo "\$_SESSION['ad_retour']=".$_SESSION['ad_retour']."<br />";

	include("../lib/initialisation_annee.inc.php");
	$liste_tables_del = $liste_tables_del_etape_matieres;


	// On va uploader les fichiers XML dans le tempdir de l'utilisateur (administrateur, ou scolarité pour les màj Sconet)
	$tempdir=get_user_temp_directory();
	if(!$tempdir){
		echo "<p style='color:red'>Il semble que le dossier temporaire de l'utilisateur ".$_SESSION['login']." ne soit pas défini!?</p>\n";
		// Il ne faut pas aller plus loin...
		// SITUATION A GERER
	}


	// =======================================================
	// EST-CE ENCORE UTILE?
	if(isset($_GET['nettoyage'])){
		check_token(false);

		//echo "<h1 align='center'>Suppression des CSV</h1>\n";
		echo "<h2>Suppression des XML</h2>\n";
		echo "<p class=bold><a href='";
		if(isset($_SESSION['ad_retour'])){
			echo $_SESSION['ad_retour'];
		}
		else{
			echo "index.php";
		}
		echo "'> <img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
		echo "<a href='".$_SERVER['PHP_SELF']."'> | Autre import</a></p>\n";
		//echo "</div>\n";

		echo "<p>Si des fichiers XML existent, ils seront supprimés...</p>\n";
		//$tabfich=array("f_ele.csv","f_ere.csv");
		$tabfich=array("sts.xml","nomenclature.xml");

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
	else{
		echo "<center><h3 class='gepi'>Importation des matières</h3></center>\n";
		//echo "<h2>Préparation des données élèves/classes/périodes/options</h2>\n";
		echo "<p class=bold><a href='";
		if(isset($_SESSION['ad_retour'])){
			// On peut venir de l'index init_xml, de la page de conversion ou de la page de mise à jour Sconet
			echo $_SESSION['ad_retour'];
		}
		else{
			echo "index.php";
		}
		echo "'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

		//echo " | <a href='".$_SERVER['PHP_SELF']."'>Autre import</a>";
		echo " | <a href='".$_SERVER['PHP_SELF']."?nettoyage=oui".add_token_in_url()."'>Suppression des fichiers XML existants</a>";
		echo "</p>\n";
		//echo "</div>\n";

		//if(!isset($_POST['is_posted'])){
		if(!isset($step)){

			if(!isset($verif_tables_non_vides)) {
				$j=0;
				$flag=0;
				$chaine_tables="";
				while (($j < count($liste_tables_del)) and ($flag==0)) {
					$sql="SELECT 1=1 FROM $liste_tables_del[$j];";
					//echo "$sql<br />";
					$test_del=mysql_query($sql);
					if(mysql_num_rows($test_del)>0) {
						if (mysql_result($test_del,0)!=0) {
							$flag=1;
						}
					}
					$j++;
				}
				for($loop=0;$loop<count($liste_tables_del);$loop++) {
					if($chaine_tables!="") {$chaine_tables.=", ";}
					$chaine_tables.="'".$liste_tables_del[$loop]."'";
				}

				if ($flag != 0){
					echo "<p><b>ATTENTION ...</b><br />\n";
					echo "Des données concernant les matières sont actuellement présentes dans la base GEPI<br /></p>\n";
					echo "<p>Si vous poursuivez la procédure les données telles que notes, appréciations, ... seront effacées.</p>\n";
					echo "<p>Seules la table contenant les matières et la table mettant en relation les matières et les professeurs seront conservées.</p>\n";

					echo "<p>Les tables vidées seront&nbsp;: $chaine_tables</p>\n";

					echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
					echo add_token_field();
					echo "<input type=hidden name='verif_tables_non_vides' value='y' />\n";
					echo "<input type='submit' name='confirm' value='Poursuivre la procédure' />\n";
					echo "</form>\n";
					echo "</div>\n";
					echo "</body>\n";
					echo "</html>\n";
					die();
				}
			}


			if(isset($verif_tables_non_vides)) {
				check_token(false);

				$j=0;
				while ($j < count($liste_tables_del)) {
					if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
						$del = @mysql_query("DELETE FROM $liste_tables_del[$j]");
					}
					$j++;
				}
			}

			echo "<p><b>ATTENTION ...</b><br />Vous ne devez procéder à cette opération que si la constitution des classes a été effectuée !</p>\n";

			echo "<p>Cette page permet d'uploader un fichier qui servira à remplir les tables de GEPI avec les informations professeurs, matières,...</p>\n";

			echo "<p>Il faut lui fournir un Export XML réalisé depuis l'application STS-web.<br />Demandez gentiment à votre secrétaire d'accéder à STS-web et d'effectuer 'Mise à jour/Exports/Emplois du temps'.</p>\n";

			echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' id='form_envoi_xml' method='post'>\n";
			echo add_token_field();
			echo "<p>Veuillez fournir le fichier XML <b>sts_emp_<i>RNE</i>_<i>ANNEE</i>.xml</b>&nbsp;: \n";
			echo "<p><input type=\"file\" size=\"65\" name=\"xml_file\" id='input_xml_file' />\n";
			echo "<p><input type=\"hidden\" name=\"step\" value=\"0\" />\n";
			echo "<input type='hidden' name='is_posted' value='yes' />\n";
			echo "</p>\n";


			echo "<input type='hidden' name='is_posted' value='yes' />\n";

			//echo "<p><input type='submit' value='Valider' /></p>\n";

			echo "<p><input type='submit' id='input_submit' value='Valider' />
<input type='button' id='input_button' value='Valider' style='display:none;' onclick=\"check_champ_file()\" /></p>

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
</script>\n";

			echo "</form>\n";
		}
		else{
			check_token(false);

			$post_max_size=ini_get('post_max_size');
			$upload_max_filesize=ini_get('upload_max_filesize');
			$max_execution_time=ini_get('max_execution_time');
			$memory_limit=ini_get('memory_limit');

			if($step==0){
				$xml_file=isset($_FILES["xml_file"]) ? $_FILES["xml_file"] : NULL;

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

						echo "<p>Il semblerait que l'absence d'extension .XML puisse aussi provoquer ce genre de symptômes.<br />Dans ce cas, ajoutez l'extension et ré-essayez.</p>\n";

						// Il ne faut pas aller plus loin...
						// SITUATION A GERER
						require("../lib/footer.inc.php");
						die();
					}

					echo "<p>Le fichier a été uploadé.</p>\n";


					//$source_file=stripslashes($xml_file['tmp_name']);
					$source_file=$xml_file['tmp_name'];
					$dest_file="../temp/".$tempdir."/sts.xml";
					$res_copy=copy("$source_file" , "$dest_file");

					if(!$res_copy){
						echo "<p style='color:red;'>La copie du fichier vers le dossier temporaire a échoué.<br />Vérifiez que l'utilisateur ou le groupe apache ou www-data a accès au dossier temp/$tempdir</p>\n";
						// Il ne faut pas aller plus loin...
						// SITUATION A GERER
						require("../lib/footer.inc.php");
						die();
					}
					else{
						echo "<p>La copie du fichier vers le dossier temporaire a réussi.</p>\n";

						// Table destinée à stocker l'association code/code_gestion utilisée dans d'autres parties de l'initialisation
						$sql="CREATE TABLE IF NOT EXISTS temp_matieres_import (
								code varchar(40) NOT NULL default '',
								code_gestion varchar(40) NOT NULL default '',
								libelle_court varchar(40) NOT NULL default '',
								libelle_long varchar(255) NOT NULL default '',
								libelle_edition varchar(255) NOT NULL default ''
								) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
						$create_table = mysql_query($sql);

						$sql="TRUNCATE TABLE temp_matieres_import;";
						$vide_table = mysql_query($sql);

						flush();

						$sts_xml=simplexml_load_file($dest_file);
						if(!$sts_xml) {
							echo "<p style='color:red;'>ECHEC du chargement du fichier avec simpleXML.</p>\n";
							require("../lib/footer.inc.php");
							die();
						}
		
						$nom_racine=$sts_xml->getName();
						if(my_strtoupper($nom_racine)!='STS_EDT') {
							echo "<p style='color:red;'><b>ERREUR&nbsp;:</b> Le fichier XML fourni n'a pas l'air d'être un fichier XML STS_EMP_&lt;RNE&gt;_&lt;ANNEE&gt;.<br />Sa racine devrait être 'STS_EDT'.</p>\n";

							if(my_strtoupper($nom_racine)=='EDT_STS') {
								echo "<p style='color:red;'>Vous vous êtes trompé d'export.<br />Vous avez probablement utilisé un export de votre logiciel EDT d'Index Education, au lieu de l'export XML provenant de STS.</p>\n";
							}

							require("../lib/footer.inc.php");
							die();
						}

						// On commence par la section MATIERES.
						echo "Analyse du fichier pour extraire les informations de la section MATIERES...<br />\n";

						$tab_champs_matiere=array("CODE_GESTION",
						"LIBELLE_COURT",
						"LIBELLE_LONG",
						"LIBELLE_EDITION");

						$matiere=array();
						// Compteur matieres:
						$i=0;
				
						foreach($sts_xml->NOMENCLATURES->MATIERES->children() as $objet_matiere) {
				
							foreach($objet_matiere->attributes() as $key => $value) {
								// <MATIERE CODE="090100">
								$matiere[$i][my_strtolower($key)]=trim($value);
							}
				
							// Champs de la matière
							foreach($objet_matiere->children() as $key => $value) {
								if(in_array(my_strtoupper($key),$tab_champs_matiere)) {
									if(my_strtoupper($key)=='CODE_GESTION') {
										$matiere[$i][my_strtolower($key)]=nettoyer_caracteres_nom(remplace_accents($value),"an","&_. -","");
									}
									elseif(my_strtoupper($key)=='LIBELLE_COURT') {
										$matiere[$i][my_strtolower($key)]=trim(preg_replace("/'/"," ",preg_replace('/"/',' ',nettoyer_caracteres_nom($value, "an", " .'_&-", ""))));
									}
									else {
										$matiere[$i][my_strtolower($key)]=trim(preg_replace('/"/',' ',nettoyer_caracteres_nom($value, "an", " .'_&-", "")));
									}
								}
							}

							if($debug_import=='y') {
								echo "<pre style='color:green;'><b>Tableau \$adresses[$i]&nbsp;:</b>";
								print_r($matiere[$i]);
								echo "</pre>";
							}
				
							$i++;
						}

						$i=0;
						$nb_err=0;
						$stat=0;
						while($i<count($matiere)){
							//$sql="INSERT INTO temp_resp_pers_import SET ";
							$sql="INSERT INTO temp_matieres_import SET ";
							$sql.="code='".$matiere[$i]["code"]."', ";
							$sql.="code_gestion='".mysql_real_escape_string($matiere[$i]["code_gestion"])."', ";
							$sql.="libelle_court='".mysql_real_escape_string($matiere[$i]["libelle_court"])."', ";
							$sql.="libelle_long='".mysql_real_escape_string($matiere[$i]["libelle_long"])."', ";
							$sql.="libelle_edition='".mysql_real_escape_string($matiere[$i]["libelle_edition"])."';";
							affiche_debug("$sql<br />\n");
							$res_insert=mysql_query($sql);
							if(!$res_insert){
								echo "<span style='color:red'>Erreur lors de la requête $sql</span><br />\n";
								flush();
								$nb_err++;
							}
							else{
								$stat++;
							}

							$i++;
						}



						echo "<p>Dans le tableau ci-dessous, les identifiants en rouge correspondent à des nouvelles matières dans la base GEPI. les identifiants en vert correspondent à des identifiants de matières détectés dans le fichier GEP mais déjà présents dans la base GEPI.<br /><br />Il est possible que certaines matières ci-dessous, bien que figurant dans le fichier CSV, ne soient pas utilisées dans votre établissement cette année. C'est pourquoi il vous sera proposé en fin de procédure d'initialisation, un nettoyage de la base afin de supprimer ces données inutiles.</p>\n";

						echo "<table border='1' class='boireaus' cellpadding='2' cellspacing='2' summary='Tableau des matières'>\n";

						echo "<tr><th><p class=\"small\">Identifiant de la matière</p></th><th><p class=\"small\">Nom complet</p></th></tr>\n";

						$i=0;
						//$nb_err=0;
						$nb_reg_no=0;
						//$stat=0;

						$alt=1;
						while($i<count($matiere)){
							$sql="select matiere, nom_complet from matieres where matiere='".$matiere[$i]['code_gestion']."';";
							$verif=mysql_query($sql);
							$resverif = mysql_num_rows($verif);
							if($resverif==0) {
								$sql="insert into matieres set matiere='".mysql_real_escape_string($matiere[$i]['code_gestion'])."', nom_complet='".mysql_real_escape_string($matiere[$i]['libelle_court'])."', priority='0',matiere_aid='n',matiere_atelier='n';";
								$req=mysql_query($sql);
								if(!$req) {
									$nb_reg_no++;
									echo "<span style='color:red'>".mysql_error()."</span><br />\n";
								}
								else {
									$alt=$alt*(-1);
									echo "<tr class='lig$alt'>\n";
									echo "<td><p><font color='red'>".$matiere[$i]['code_gestion']."</font></p></td><td><p>".htmlspecialchars($matiere[$i]['libelle_court'])."</p></td></tr>\n";
								}
							} else {
								$nom_complet = mysql_result($verif,0,'nom_complet');
								$alt=$alt*(-1);
								echo "<tr class='lig$alt'>\n";
								echo "<td><p><font color='green'>".$matiere[$i]['code_gestion']."</font></p></td><td><p>".htmlspecialchars($nom_complet)."</p></td></tr>\n";
							}

							$i++;
						}

						echo "</table>\n";




						// Importation des MEF
						$divisions=array();
						$tab_mef_code=array();
						$i=0;
						foreach($sts_xml->DONNEES->STRUCTURE->DIVISIONS->children() as $objet_division) {
							$divisions[$i]=array();
					
							foreach($objet_division->attributes() as $key => $value) {
								if(my_strtoupper($key)=='CODE') {
									$divisions[$i]['code']=preg_replace("/'/","",preg_replace('/"/','',trim($value)));
									//echo "<p>\$divisions[$i]['code']=".$divisions[$i]['code']."<br />";
									break;
								}
							}

							// Champs de la division
							foreach($objet_division->MEFS_APPARTENANCE->children() as $mef_appartenance) {
								foreach($mef_appartenance->attributes() as $key => $value) {
									// Normalement, on ne devrait faire qu'un tour:
									$divisions[$i]["mef_code"][]=trim($value);
									$tab_mef_code[]=trim($value);
									//echo "\$divisions[$i][\"mef_code\"][]=trim(traite_utf8($value))<br />";
								}
							}
							$i++;
						}

						/*
						// Il peut y avoir plusieurs MEF associées à une classe (3EME et 3EME BILANGUE par exemple) et chaque élève est associé à un(e) des ces MEFS
						for($i=0;$i<count($divisions);$i++) {
							if(isset($divisions[$i]["mef_code"][0])) {
								$sql="UPDATE eleves SET mef_code='".$divisions[$i]["mef_code"][0]."' WHERE login IN (SELECT j.login FROM j_eleves_classes j, classes c WHERE j.id_classe=c.id AND c.classe='".mysql_real_escape_string($divisions[$i]["code"])."');";
								//echo "$sql<br />";
								$update_mef=mysql_query($sql);
							}
						}
						*/

						$tab_champs_mef=array("LIBELLE_COURT",
						"LIBELLE_LONG",
						"LIBELLE_EDITION");

						$mefs=array();
						$i=0;
						foreach($sts_xml->NOMENCLATURES->MEFS->children() as $objet_mef) {
							$mefs[$i]=array();
					
							foreach($objet_mef->attributes() as $key => $value) {
								if(my_strtoupper($key)=='CODE') {
									$mefs[$i]['code']=preg_replace('/"/','',preg_replace("/'/","",trim($value)));
									break;
								}
							}

							if(in_array($mefs[$i]['code'],$tab_mef_code)) {
								// Champs MEF
								foreach($objet_mef->children() as $key => $value) {
									if(in_array(my_strtoupper($key),$tab_champs_mef)) {
										$mefs[$i][my_strtolower($key)]=trim(preg_replace('/"/','',preg_replace("/'/","",nettoyer_caracteres_nom($value, "an", " .'_&-", ""))));
									}
								}
								$i++;
							}
						}

						for($i=0;$i<count($mefs);$i++) {
							$sql="SELECT 1=1 FROM mef WHERE mef_code='".$mefs[$i]['code']."';";
							$test=mysql_query($sql);
							if(mysql_num_rows($test)>0) {
								$sql="UPDATE mef SET ";
								if(isset($mefs[$i]["libelle_court"])) {
									$sql.=" libelle_court='".mysql_real_escape_string($mefs[$i]["libelle_court"])."',";
								}
								//elseif(isset($mefs[$i]["libelle_long"])) {$sql.=" libelle_court='".$mefs[$i]["libelle_long"]."',";}
								else {
									$sql.=" libelle_court='',";
								}
								if(isset($mefs[$i]["libelle_long"])) {$sql.=" libelle_long='".mysql_real_escape_string($mefs[$i]["libelle_long"])."',";}
								if(isset($mefs[$i]["libelle_edition"])) {$sql.=" libelle_edition='".mysql_real_escape_string($mefs[$i]["libelle_edition"])."',";}
								$sql.=" mef_code='".$mefs[$i]["code"]."' WHERE mef_code='".$mefs[$i]["code"]."';";
								//echo "$sql<br />";
								$update_mef=mysql_query($sql);
							}
							else{
								$sql="INSERT INTO mef SET ";
								//if(isset($mefs[$i]["libelle_court"])) {$sql.=" libelle_court='".$mefs[$i]["libelle_court"]."',";} elseif(isset($mefs[$i]["libelle_long"])) {$sql.=" libelle_court='".$mefs[$i]["libelle_long"]."',";}
								if(isset($mefs[$i]["libelle_court"])) {
									$sql.=" libelle_court='".mysql_real_escape_string($mefs[$i]["libelle_court"])."',";
								}
								//elseif(isset($mefs[$i]["libelle_long"])) {$sql.=" libelle_court='".$mefs[$i]["libelle_long"]."',";}
								else {
									$sql.=" libelle_court='',";
								}
								if(isset($mefs[$i]["libelle_long"])) {$sql.=" libelle_long='".mysql_real_escape_string($mefs[$i]["libelle_long"])."',";}
								if(isset($mefs[$i]["libelle_edition"])) {$sql.=" libelle_edition='".mysql_real_escape_string($mefs[$i]["libelle_edition"])."',";}
								$sql.=" mef_code='".$mefs[$i]["code"]."';";
								//echo "$sql<br />";
								$insert=mysql_query($sql);
							}
						}


						if ($nb_reg_no != 0) {
							echo "<p>Lors de l'enregistrement des données il y a eu $nb_reg_no erreurs. Essayez de trouvez la cause de l'erreur et recommencez la procédure avant de passer à l'étape suivante.";
						} else {
							echo "<p>L'importation des matières dans la base GEPI a été effectuée avec succès !<br />Vous pouvez procéder à la quatrième phase d'importation des professeurs.</p>";
						}

						//echo "<center><p><a href='prof_csv.php'>Importation des professeurs</a></p></center>";
						//echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=1'>Importation des professeurs</a></p>\n";
						echo "<p align='center'><a href='professeurs.php'>Importation des professeurs</a></p>\n";
						echo "<p><br /></p>\n";

						require("../lib/footer.inc.php");
						die();
					}
				}
			}
		}
	}
	require("../lib/footer.inc.php");
?>
