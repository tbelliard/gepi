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
	};

	if (!checkAccess()) {
		header("Location: ../logout.php?auto=1");
		die();
	}

	//**************** EN-TETE *****************
	$titre_page = "Outil d'initialisation de l'année : Importation des matières";
	require_once("../lib/header.inc");
	//**************** FIN EN-TETE *****************

	function extr_valeur($lig){
		unset($tabtmp);
		$tabtmp=explode(">",my_ereg_replace("<",">",$lig));
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
		}
	}

		// Etape...
	$step=isset($_POST['step']) ? $_POST['step'] : (isset($_GET['step']) ? $_GET['step'] : NULL);

	$verif_tables_non_vides=isset($_POST['verif_tables_non_vides']) ? $_POST['verif_tables_non_vides'] : NULL;

	if(isset($_GET['ad_retour'])){
		$_SESSION['ad_retour']=$_GET['ad_retour'];
	}
	//echo "\$_SESSION['ad_retour']=".$_SESSION['ad_retour']."<br />";

	$liste_tables_del = array(
//"absences",
//"aid",
//"aid_appreciations",
//"aid_config",
//"avis_conseil_classe",
//"classes",
//"droits",
//"eleves",
//"responsables",
//"etablissements",
"groupes",
//"j_aid_eleves",
//"j_aid_utilisateurs",
//"j_eleves_classes",
//"j_eleves_etablissements",
"j_eleves_groupes",
"j_groupes_matieres",
"j_groupes_professeurs",
"j_groupes_classes",
"eleves_groupes_settings",
//"j_eleves_professeurs",
//"j_eleves_regime",
//"j_professeurs_matieres",
//"log",
//"matieres",
"matieres_appreciations",
"matieres_notes",
"matieres_appreciations_grp",
"matieres_appreciations_tempo",
//"observatoire",
//"observatoire_comment",
//"observatoire_config",
//"observatoire_niveaux",
//"observatoire_j_resp_champ",
//"observatoire_suivi",
//"periodes",
//"periodes_observatoire",
"tempo2",
//"temp_gep_import",
"tempo",
//"utilisateurs",
"cn_cahier_notes",
"cn_conteneurs",
"cn_devoirs",
"cn_notes_conteneurs",
"cn_notes_devoirs",
"ct_devoirs_entry",
"ct_documents",
"ct_entry",
"ct_devoirs_documents",
"ct_private_entry",
"ct_sequences",
//"setting"
);




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
		echo " | <a href='".$_SERVER['PHP_SELF']."?nettoyage=oui'>Suppression des fichiers XML existants</a>";
		echo "</p>\n";
		//echo "</div>\n";

		//if(!isset($_POST['is_posted'])){
		if(!isset($step)){

			if(!isset($verif_tables_non_vides)) {
				$j=0;
				$flag=0;
				$chaine_tables="";
				while (($j < count($liste_tables_del)) and ($flag==0)) {
					if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
						$flag=1;
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
					echo "<input type=hidden name='verif_tables_non_vides' value='y' />\n";
					echo "<input type='submit' name='confirm' value='Poursuivre la procédure' />\n";
					echo "</form>\n";
					echo "</div>\n";
					echo "</body>\n";
					echo "</html>\n";
					die();
				}
			}


			$j=0;
			while ($j < count($liste_tables_del)) {
				if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
					$del = @mysql_query("DELETE FROM $liste_tables_del[$j]");
				}
				$j++;
			}


			echo "<p><b>ATTENTION ...</b><br />Vous ne devez procéder à cette opération que si la constitution des classes a été effectuée !</p>\n";

			echo "<p>Cette page permet d'uploader un fichier qui servira à remplir les tables de GEPI avec les informations professeurs, matières,...</p>\n";

			echo "<p>Il faut lui fournir un Export XML réalisé depuis l'application STS-web.<br />Demandez gentiment à votre secrétaire d'accéder à STS-web et d'effectuer 'Mise à jour/Exports/Emplois du temps'.</p>\n";

			echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
			echo "<p>Veuillez fournir le fichier XML <b>sts_emp_<i>RNE</i>_<i>ANNEE</i>.xml</b>&nbsp;: \n";
			echo "<p><input type=\"file\" size=\"65\" name=\"xml_file\" />\n";
			echo "<p><input type=\"hidden\" name=\"step\" value=\"0\" />\n";
			echo "<input type='hidden' name='is_posted' value='yes' />\n";
			echo "</p>\n";


			echo "<input type='hidden' name='is_posted' value='yes' />\n";

			echo "<p><input type='submit' value='Valider' /></p>\n";
			echo "</form>\n";
		}
		else{
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
								);";
						$create_table = mysql_query($sql);

						$sql="TRUNCATE TABLE temp_matieres_import;";
						$vide_table = mysql_query($sql);



						/*
						// On va lire plusieurs fois le fichier pour remplir des tables temporaires.
						$fp=fopen($dest_file,"r");
						if($fp){
							echo "<p>Lecture du fichier STS Emploi du temps...<br />\n";
							//echo "<blockquote>\n";
							while(!feof($fp)){
								$ligne[]=fgets($fp,4096);
							}
							fclose($fp);
							//echo "<p>Terminé.</p>\n";
						}
						*/
						flush();



						// On commence par la section MATIERES.
						echo "Analyse du fichier pour extraire les informations de la section MATIERES...<br />\n";

						$cpt=0;
						$temoin_matieres=0;
						$matiere=array();
						$i=0;
						$temoin_mat=0;
						//while($cpt<count($ligne)){
						$fp=fopen($dest_file,"r");
						if($fp){
							while(!feof($fp)){
								$ligne=fgets($fp,4096);

								//echo htmlentities($ligne[$cpt])."<br />\n";
								//if(strstr($ligne[$cpt],"<MATIERES>")){
								if(strstr($ligne,"<MATIERES>")){
									echo "Début de la section MATIERES à la ligne <span style='color: blue;'>$cpt</span><br />\n";
									flush();
									$temoin_matieres++;
								}
								//if(strstr($ligne[$cpt],"</MATIERES>")){
								if(strstr($ligne,"</MATIERES>")){
									echo "Fin de la section MATIERES à la ligne <span style='color: blue;'>$cpt</span><br />\n";
									flush();
									$temoin_matieres++;
								}
								if($temoin_matieres==1){
									// On analyse maintenant matière par matière:
									/*
									if(strstr($ligne[$cpt],"<MATIERE CODE=")){
										$matiere[$i]=array();
										unset($tabtmp);
										//$tabtmp=explode("=",my_ereg_replace(">","",my_ereg_replace("<","",$ligne[$cpt])));
										$tabtmp=explode('"',$ligne[$cpt]);
										$matiere[$i]["code"]=trim($tabtmp[1]);
										$temoin_mat=1;
									}
									*/
									//if(strstr($ligne[$cpt],"<MATIERE ")){
									if(strstr($ligne,"<MATIERE ")){
										$matiere[$i]=array();
										unset($tabtmp);
										//$tabtmp=explode("=",my_ereg_replace(">","",my_ereg_replace("<","",$ligne[$cpt])));
										//$tabtmp=explode('"',strstr($ligne[$cpt]," CODE="));
										$tabtmp=explode('"',strstr($ligne," CODE="));
										$matiere[$i]["code"]=trim($tabtmp[1]);
										$temoin_mat=1;
									}
									//if(strstr($ligne[$cpt],"</MATIERE>")){
									if(strstr($ligne,"</MATIERE>")){
										$temoin_mat=0;
										$i++;
									}
									if($temoin_mat==1){
										//if(strstr($ligne[$cpt],"<CODE_GESTION>")){
										if(strstr($ligne,"<CODE_GESTION>")){
											unset($tabtmp);
											//$tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
											$tabtmp=explode(">",my_ereg_replace("<",">",$ligne));
											//$matiere[$i]["code_gestion"]=$tabtmp[2];
											$matiere[$i]["code_gestion"]=trim(my_ereg_replace("[^a-zA-Z0-9&_. -]","",html_entity_decode_all_version($tabtmp[2])));
										}
										//if(strstr($ligne[$cpt],"<LIBELLE_COURT>")){
										if(strstr($ligne,"<LIBELLE_COURT>")){
											unset($tabtmp);
											//$tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
											$tabtmp=explode(">",my_ereg_replace("<",">",$ligne));
											//$matiere[$i]["libelle_court"]=$tabtmp[2];
											$matiere[$i]["libelle_court"]=trim(my_ereg_replace("[^a-zA-Z0-9ÀÄÂÉÈÊËÎÏÔÖÙÛÜÇçàäâéèêëîïôöùûü&_. -]","",html_entity_decode_all_version($tabtmp[2])));
										}
										//if(strstr($ligne[$cpt],"<LIBELLE_LONG>")){
										if(strstr($ligne,"<LIBELLE_LONG>")){
											unset($tabtmp);
											//$tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
											$tabtmp=explode(">",my_ereg_replace("<",">",$ligne));
											//$matiere[$i]["libelle_long"]=trim($tabtmp[2]);

											// Suppression des guillemets éventuels
											//$matiere[$i]["libelle_long"]=traitement_magic_quotes(corriger_caracteres(trim($tabtmp[2])));
											$matiere[$i]["libelle_long"]=traitement_magic_quotes(corriger_caracteres(trim(my_ereg_replace('"','',$tabtmp[2]))));
										}
										//if(strstr($ligne[$cpt],"<LIBELLE_EDITION>")){
										if(strstr($ligne,"<LIBELLE_EDITION>")){
											unset($tabtmp);
											//$tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
											$tabtmp=explode(">",my_ereg_replace("<",">",$ligne));
											//$matiere[$i]["libelle_edition"]=trim($tabtmp[2]);

											// Suppression des guillemets éventuels
											//$matiere[$i]["libelle_edition"]=traitement_magic_quotes(corriger_caracteres(trim($tabtmp[2])));
											$matiere[$i]["libelle_edition"]=traitement_magic_quotes(corriger_caracteres(trim(my_ereg_replace('"','',$tabtmp[2]))));
										}
									}
								}
								$cpt++;
							}
							fclose($fp);


							$i=0;
							$nb_err=0;
							$stat=0;
							while($i<count($matiere)){
								//$sql="INSERT INTO temp_resp_pers_import SET ";
								$sql="INSERT INTO temp_matieres_import SET ";
								$sql.="code='".$matiere[$i]["code"]."', ";
								$sql.="code_gestion='".$matiere[$i]["code_gestion"]."', ";
								$sql.="libelle_court='".$matiere[$i]["libelle_court"]."', ";
								$sql.="libelle_long='".$matiere[$i]["libelle_long"]."', ";
								$sql.="libelle_edition='".$matiere[$i]["libelle_edition"]."';";
								affiche_debug("$sql<br />\n");
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



							echo "<p>Dans le tableau ci-dessous, les identifiants en rouge correspondent à des nouvelles matières dans la base GEPI. les identifiants en vert correspondent à des identifiants de matières détectés dans le fichier GEP mais déjà présents dans la base GEPI.<br /><br />Il est possible que certaines matières ci-dessous, bien que figurant dans le fichier CSV, ne soient pas utilisées dans votre établissement cette année. C'est pourquoi il vous sera proposé en fin de procédure d'initialsation, un nettoyage de la base afin de supprimer ces données inutiles.</p>\n";

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
									$sql="insert into matieres set matiere='".$matiere[$i]['code_gestion']."', nom_complet='".$matiere[$i]['libelle_court']."', priority='0',matiere_aid='n',matiere_atelier='n';";
									$req=mysql_query($sql);
									if(!$req) {
										$nb_reg_no++;
										echo mysql_error();
									}
									else {
										$alt=$alt*(-1);
										echo "<tr class='lig$alt'>\n";
										echo "<td><p><font color='red'>".$matiere[$i]['code_gestion']."</font></p></td><td><p>".htmlentities($matiere[$i]['libelle_court'])."</p></td></tr>\n";
									}
								} else {
									$nom_complet = mysql_result($verif,0,'nom_complet');
									$alt=$alt*(-1);
									echo "<tr class='lig$alt'>\n";
									echo "<td><p><font color='green'>".$matiere[$i]['code_gestion']."</font></p></td><td><p>".htmlentities($nom_complet)."</p></td></tr>\n";
								}

								$i++;
							}

							echo "</table>\n";

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
						else{
							echo "<p>ERREUR: Il n'a pas été possible d'ouvrir le fichier en lecture.</p>\n";

							require("../lib/footer.inc.php");
							die();
						}
					}
				}
			}
		}
	}
	require("../lib/footer.inc.php");
?>