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
	$titre_page = "Outil d'initialisation de l'année : Importation des responsables des élèves";
	require_once("../lib/header.inc");
	//**************** FIN EN-TETE *****************

	function extr_valeur($lig){
		unset($tabtmp);
		$tabtmp=explode(">",ereg_replace("<",">",$lig));
		return trim($tabtmp[2]);
	}

	function ouinon($nombre){
		if($nombre==1){return "O";}elseif($nombre==0){return "N";}else{return "";}
	}
	function sexeMF($nombre){
		//if($nombre==2){return "F";}else{return "M";}
		if($nombre==2){return "F";}elseif($nombre==1){return "M";}else{return "";}
	}

	function maj_min_comp($chaine){
		$tmp_tab1=explode(" ",$chaine);
		$new_chaine="";
		for($i=0;$i<count($tmp_tab1);$i++){
			$tmp_tab2=explode("-",$tmp_tab1[$i]);
			$new_chaine.=ucfirst(strtolower($tmp_tab2[0]));
			for($j=1;$j<count($tmp_tab2);$j++){
				$new_chaine.="-".ucfirst(strtolower($tmp_tab2[$j]));
			}
			$new_chaine.=" ";
		}
		$new_chaine=trim($new_chaine);
		return $new_chaine;
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
// ==================================
// On vide l'ancienne table responsables pour ne pas conserver des infos d'années antérieures:
"responsables",

"responsables2",
"resp_pers",
"resp_adr",
// ==================================
//"etablissements",
//"j_aid_eleves",
//"j_aid_utilisateurs",
//"j_eleves_classes",
//"j_eleves_etablissements",
//"j_eleves_professeurs",
//"j_eleves_regime",
//"j_professeurs_matieres",
//"log",
//"matieres",
//"matieres_appreciations",
//"matieres_notes",
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
//"cn_cahier_notes",
//"cn_conteneurs",
//"cn_devoirs",
//"cn_notes_conteneurs",
//"cn_notes_devoirs",
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
		$tabfich=array("responsables.xml");

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
		echo "<center><h3 class='gepi'>Deuxième phase d'initialisation<br />Importation des responsables</h3></center>\n";
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
				while (($j < count($liste_tables_del)) and ($flag==0)) {
					if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
						$flag=1;
					}
					$j++;
				}
				if ($flag != 0){
					echo "<p><b>ATTENTION ...</b><br />\n";
					echo "Des données concernant les responsables sont actuellement présentes dans la base GEPI<br /></p>\n";
					echo "<p>Si vous poursuivez la procédure ces données seront effacées.</p>\n";
					echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
					echo "<input type=hidden name='verif_tables_non_vides' value='y' />\n";
					echo "<input type='submit' name='confirm' value='Poursuivre la procédure' />\n";
					echo "</form>\n";
					require("../lib/footer.inc.php");
					die();
				}
			}

			//echo "<p>Cette page permet de remplir des tables temporaires avec les informations responsables.<br />\n";
			//echo "</p>\n";

			$j=0;
			while ($j < count($liste_tables_del)) {
				if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
					$del = @mysql_query("DELETE FROM $liste_tables_del[$j]");
				}
				$j++;
			}

			// Suppression des comptes de responsables:
			$sql="DELETE FROM utilisateurs WHERE statut='responsable';";
			$del=mysql_query($sql);

			echo "<p><b>ATTENTION ...</b><br />Vous ne devez procéder à cette opération uniquement si la constitution des classes a été effectuée !</p>\n";

			echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
			echo "<p>Veuillez fournir le fichier ResponsablesAvecAdresses.xml:<br />\n";
			echo "<input type=\"file\" size=\"65\" name=\"responsables_xml_file\" /><br />\n";
			echo "<input type='hidden' name='step' value='0' />\n";
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

					if(!$res_copy){
						echo "<p style='color:red;'>La copie du fichier vers le dossier temporaire a échoué.<br />Vérifiez que l'utilisateur ou le groupe apache ou www-data a accès au dossier temp/$tempdir</p>\n";
						// Il ne faut pas aller plus loin...
						// SITUATION A GERER
						require("../lib/footer.inc.php");
						die();
					}
					else{
						echo "<p>La copie du fichier vers le dossier temporaire a réussi.</p>\n";

						//$sql="CREATE TABLE IF NOT EXISTS temp_resp_pers_import (
						$sql="CREATE TABLE IF NOT EXISTS resp_pers (
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
						$create_table = mysql_query($sql);

						//$sql="TRUNCATE TABLE temp_resp_pers_import;";
						$sql="TRUNCATE TABLE resp_pers;";
						$vide_table = mysql_query($sql);

						/*
						// On va lire plusieurs fois le fichier pour remplir des tables temporaires.
						$fp=fopen($dest_file,"r");
						if($fp){
							echo "<p>Lecture du fichier Responsables...<br />\n";
							//echo "<blockquote>\n";
							while(!feof($fp)){
								$ligne[]=fgets($fp,4096);
							}
							fclose($fp);
							//echo "<p>Terminé.</p>\n";
						}
						*/
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
												$personnes[$i]["$tmpmin"]=traitement_magic_quotes(corriger_caracteres(ereg_replace('"','',extr_valeur($ligne))));

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
								//$sql="INSERT INTO temp_resp_pers_import SET ";
								$sql="INSERT INTO resp_pers SET ";
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

							if ($nb_err != 0) {
								echo "<p>Lors de l'enregistrement des données PERSONNES, il y a eu $nb_err erreurs. Essayez de trouvez la cause de l'erreur et recommencez la procédure avant de passer à l'étape suivante.</p>\n";
							} else {
								echo "<p>L'importation des personnes (responsables) dans la base GEPI a été effectuée avec succès (".$stat." enregistrements au total).</p>\n";
							}

							//echo "<p>$stat enregistrement(s) ont été inséré(s) dans la table 'temp_resp_pers_import'.</p>\n";
							//echo "<p>$stat enregistrement(s) ont été inséré(s) dans la table 'resp_pers'.</p>\n";

							echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=1'>Suite</a></p>\n";

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
			} // Fin du $step=0
			elseif($step==1){
				$dest_file="../temp/".$tempdir."/responsables.xml";
				$fp=fopen($dest_file,"r");
				if(!$fp){
					echo "<p>Le XML responsables n'a pas l'air présent dans le dossier temporaire.<br />Auriez-vous sauté une étape???</p>\n";
					require("../lib/footer.inc.php");
					die();
				}
				else{

					//$sql="CREATE TABLE IF NOT EXISTS temp_responsables2_import (
					$sql="CREATE TABLE IF NOT EXISTS responsables2 (
							`ele_id` varchar(10) NOT NULL,
							`pers_id` varchar(10) NOT NULL,
							`resp_legal` varchar(1) NOT NULL,
							`pers_contact` varchar(1) NOT NULL
							);";
					$create_table = mysql_query($sql);

					//$sql="TRUNCATE TABLE temp_responsables2_import;";
					$sql="TRUNCATE TABLE responsables2;";
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
										$responsables[$i]["$tmpmin"]=traitement_magic_quotes(corriger_caracteres(ereg_replace('"','',extr_valeur($ligne))));

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
						//$sql="INSERT INTO temp_responsables2_import SET ";
						$sql="INSERT INTO responsables2 SET ";
						$sql.="ele_id='".$responsables[$i]["eleve_id"]."', ";
						$sql.="pers_id='".$responsables[$i]["personne_id"]."', ";
						$sql.="resp_legal='".$responsables[$i]["resp_legal"]."', ";
						$sql.="pers_contact='".$responsables[$i]["pers_contact"]."';";
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

					/*
					if($nb_err==0) {
						echo "<p>La deuxième phase s'est passée sans erreur.</p>\n";
					}
					elseif($nb_err==1) {
						echo "<p>$nb_err erreur.</p>\n";
					}
					else{
						echo "<p>$nb_err erreurs</p>\n";
					}
					*/



					$sql="SELECT r.pers_id,r.ele_id FROM responsables2 r LEFT JOIN eleves e ON e.ele_id=r.ele_id WHERE e.ele_id is NULL;";
					$test=mysql_query($sql);
					if(mysql_num_rows($test)>0){
						echo "<p>Suppression de responsabilités sans élève.\n";
						flush();
						$cpt_nett=0;
						while($lig_nett=mysql_fetch_object($test)){
							//if($cpt_nett>0){echo ", ";}
							//echo "<a href='modify_resp.php?pers_id=$lig_nett->pers_id' target='_blank'>".$lig_nett->pers_id."</a>";
							$sql="DELETE FROM responsables2 WHERE pers_id='$lig_nett->pers_id' AND ele_id='$lig_nett->ele_id';";
							$nettoyage=mysql_query($sql);
							//flush();
							$cpt_nett++;
						}
						//echo ".</p>\n";
						echo "<br />$cpt_nett associations aberrantes supprimées.</p>\n";
					}




					if ($nb_err!=0) {
						echo "<p>Lors de l'enregistrement des données de RESPONSABLES, il y a eu $nb_err erreurs. Essayez de trouvez la cause de l'erreur et recommencez la procédure avant de passer à l'étape suivante.</p>\n";
					}
					else {
						echo "<p>L'importation des relations eleves/responsables dans la base GEPI a été effectuée avec succès (".$stat." enregistrements au total).</p>\n";
					}

					//echo "<p>$stat enregistrement(s) ont été inséré(s) dans la table 'temp_responsables2_import'.</p>\n";

					echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=2'>Suite</a></p>\n";

					require("../lib/footer.inc.php");
					die();
				}
			} // Fin du $step=1
			elseif($step==2){
				$dest_file="../temp/".$tempdir."/responsables.xml";
				$fp=fopen($dest_file,"r");
				if(!$fp){
					echo "<p>Le XML responsables n'a pas l'air présent dans le dossier temporaire.<br />Auriez-vous sauté une étape???</p>\n";
					require("../lib/footer.inc.php");
					die();
				}
				else{

					//$sql="CREATE TABLE IF NOT EXISTS temp_resp_adr_import (
					$sql="CREATE TABLE IF NOT EXISTS resp_adr (
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

					//$sql="TRUNCATE TABLE temp_resp_adr_import;";
					$sql="TRUNCATE TABLE resp_adr;";
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
					"LIBELLE_POSTAL"
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
										$adresses[$i]["$tmpmin"]=traitement_magic_quotes(corriger_caracteres(ereg_replace('"','',extr_valeur($ligne))));

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
						//$sql="INSERT INTO temp_resp_adr_import SET ";
						$sql="INSERT INTO resp_adr SET ";
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
						}
						$sql=substr($sql,0,strlen($sql)-2);
						$sql.=";";
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

					/*
					if($nb_err==0) {
						echo "<p>La troisième phase s'est passée sans erreur.</p>\n";
					}
					elseif($nb_err==1) {
						echo "<p>$nb_err erreur.</p>\n";
					}
					else{
						echo "<p>$nb_err erreurs</p>\n";
					}
					*/

					if ($nb_err != 0) {
						echo "<p>Lors de l'enregistrement des données ADRESSES des responsables, il y a eu $nb_err erreurs. Essayez de trouvez la cause de l'erreur et recommencez la procédure avant de passer à l'étape suivante.</p>\n";
					} else {
						echo "<p>L'importation des adresses de responsables dans la base GEPI a été effectuée avec succès (".$stat." enregistrements au total).</p>\n";
					}
					//echo "<p>$stat enregistrement(s) ont été mis à jour dans la table 'temp_resp_adr_import'.</p>\n";

					echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=3'>Suite</a></p>\n";

					require("../lib/footer.inc.php");
					die();
				}
			}
			else{
				// TERMINé?
				// A LA DERNIERE ETAPE, IL FAUT SUPPRIMER LE FICHIER "../temp/".$tempdir."/responsables.xml"

				if(file_exists("../temp/".$tempdir."/responsables.xml")) {
					echo "<p>Suppression de responsables.xml... ";
					if(unlink("../temp/".$tempdir."/responsables.xml")){
						echo "réussie.</p>\n";
					}
					else{
						echo "<font color='red'>Echec!</font> Vérifiez les droits d'écriture sur le serveur.</p>\n";
					}
				}

				/*
				if(($nb_reg_no1==0)&&($nb_reg_no2==0)&&($nb_reg_no3==0)){
					echo "<p>Vous pouvez à présent retourner à l'accueil et effectuer toutes les autres opérations d'initialisation manuellement ou bien procéder à la troixième phase d'importation des matières et de définition des options suivies par les élèves.</p>\n";
					echo "<center><p><a href='../accueil.php'>Retourner à l'accueil</a></p></center>\n";
					echo "<center><p><a href='disciplines_csv.php'>Procéder à la troisième phase</a>.</p></center>\n";
				}
				*/

				//echo "<center><p><a href='../accueil.php'>Retourner à l'accueil</a></p></center>\n";
				echo "<center><p><a href='matieres.php'>Procéder à la troisième phase</a>.</p></center>\n";

				require("../lib/footer.inc.php");
				die();
			}
		}
	}

	require("../lib/footer.inc.php");
?>
