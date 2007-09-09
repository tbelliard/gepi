<?php
	@set_time_limit(0);

	// Auteur: Stephane Boireau
	// Dernière modification: 17/07/2007


	// Initialisations files
	require_once("../lib/initialisations.inc.php");

	// Resume session
	$resultat_session = resumeSession();
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
	$titre_page = "Outil d'initialisation de l'année : Importation des élèves";
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

	function affiche_debug($texte){
		// Passer à 1 la variable pour générer l'affichage des infos de debug...
		$debug=0;
		if($debug==1){
			echo "<font color='green'>".$texte."</font>";
			flush();
		}
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


	// Etape...
	$step=isset($_POST['step']) ? $_POST['step'] : (isset($_GET['step']) ? $_GET['step'] : NULL);


	if(isset($_GET['ad_retour'])){
		$_SESSION['ad_retour']=$_GET['ad_retour'];
	}

	//echo "\$_SESSION['ad_retour']=".$_SESSION['ad_retour']."<br />";

	unset($remarques);
	$remarques=array();


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
		$tabfich=array("eleves.xml","nomenclature.xml");

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
		echo "<center><h3 class='gepi'>Première phase d'initialisation<br />Préparation des données élèves/classes/périodes/options</h3></center>\n";
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
			echo "<p>Cette page permet de remplir des tables temporaires avec les informations élèves.<br />\n";
			echo "</p>\n";

			echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
			echo "<p>Veuillez fournir le fichier ElevesAvecAdresses.xml (<i>ou ElevesSansAdresses.xml</i>):<br />\n";
			echo "<input type=\"file\" size=\"80\" name=\"eleves_xml_file\" /><br />\n";
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

					$source_file=stripslashes($xml_file['tmp_name']);
					$dest_file="../temp/".$tempdir."/eleves.xml";
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
						$create_table = mysql_query($sql);

						$sql="TRUNCATE TABLE temp_gep_import2;";
						$vide_table = mysql_query($sql);

						// On va lire plusieurs fois le fichier pour remplir des tables temporaires.
						/*
						$fp=fopen($dest_file,"r");
						if($fp){
							echo "<p>Lecture du fichier Elèves...<br />\n";
							//echo "<blockquote>\n";
							while(!feof($fp)){
								$ligne[]=fgets($fp,4096);
							}
							fclose($fp);
							//echo "<p>Terminé.</p>\n";
						}
						*/

						$fp=fopen($dest_file,"r");
						if($fp){
							// On commence par la section STRUCTURES pour ne récupérer que les ELE_ID d'élèves qui sont dans une classe.
							echo "<p>\n";
							echo "Analyse du fichier pour extraire les informations de la section STRUCTURES pour ne conserver que les identifiants d'élèves affectés dans une classe...<br />\n";

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

								//if(strstr($ligne[$cpt],"<STRUCTURES>")){
								if(strstr($ligne,"<STRUCTURES>")){
									echo "Début de la section STRUCTURES à la ligne <span style='color: blue;'>$cpt</span><br />\n";
									$temoin_structures++;
								}
								//if(strstr($ligne[$cpt],"</STRUCTURES>")){
								if(strstr($ligne,"</STRUCTURES>")){
									echo "Fin de la section STRUCTURES à la ligne <span style='color: blue;'>$cpt</span><br />\n";
									$temoin_structures++;
									break;
								}
								if($temoin_structures==1){
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

										/*
										// Recherche du $i de $eleves[$i] correspondant:
										$temoin_ident="non";
										for($i=0;$i<count($eleves);$i++){
											if($eleves[$i]["eleve_id"]==$tmp_eleve_id){
												$temoin_ident="oui";
												break;
											}
										}
										if($temoin_ident!="oui"){
											unset($tabtmp);
											$tabtmp=explode('"',strstr($ligne[$cpt]," ELENOET="));
											$tmp_elenoet=trim($tabtmp[1]);

											for($i=0;$i<count($eleves);$i++){
												if($eleves[$i]["elenoet"]==$tmp_elenoet){
													$temoin_ident="oui";
													break;
												}
											}
										}
										if($temoin_ident=="oui"){
										*/
											$eleves[$i]["structures"]=array();
											$j=0;
											$temoin_struct_ele=1;
										//}
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
													$eleves[$i]["structures"][$j]["$tmpmin"]=extr_valeur($ligne);
													//echo "\$eleves[$i]["structures"][$j][\"$tmpmin\"]=".$eleves[$i]["structures"][$j]["$tmpmin"]."<br />\n";
													break;
												}
											}
										}
									}
								}
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
									$res_insert=mysql_query($sql);
									if(!$res_insert){
										echo "Erreur lors de la requête $sql<br />\n";
										$nb_err++;
									}
									$id_tempo++;
								}
							}
							if($nb_err==0) {
								echo "<p>La première phase s'est passée sans erreur.</p>\n";
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
							echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=1'>Suite</a></p>\n";

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
			} // Fin du $step=0
			elseif($step==1){
				$dest_file="../temp/".$tempdir."/eleves.xml";
				$fp=fopen($dest_file,"r");
				if(!$fp){
					echo "<p>Le XML élève n'a pas l'air présent dans le dossier temporaire.<br />Auriez-vous sauté une étape???</p>\n";
					require("../lib/footer.inc.php");
					die();
				}
				else{



					$sql="CREATE TABLE IF NOT EXISTS `temp_etab_import` (
													`id` char(8) NOT NULL default '',
													`nom` char(50) NOT NULL default '',
													`niveau` char(50) NOT NULL default '',
													`type` char(50) NOT NULL default '',
													`cp` int(10) NOT NULL default '0',
													`ville` char(50) NOT NULL default '',
													PRIMARY KEY  (`id`)
													);";
					$create_table = mysql_query($sql);

					$sql="TRUNCATE TABLE temp_etab_import;";
					$vide_table = mysql_query($sql);




					// On récupère les ele_id des élèves qui sont affectés dans une classe
					$sql="SELECT ele_id FROM temp_gep_import2 ORDER BY id_tempo";
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

					echo "<p>Analyse du fichier pour extraire les informations de la section ELEVES...<br />\n";
					//echo "<blockquote>\n";

					$cpt=0;
					$eleves=array();
					$temoin_eleves=0;
					$temoin_ele=0;
					$temoin_options=0;
					$temoin_scol=0;
					//Compteur élève:
					$i=-1;

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

					// PARTIE <ELEVES>
					//while($cpt<count($ligne)){
					while(!feof($fp)){
						$ligne=fgets($fp,4096);
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
							break;
						}
						if($temoin_eleves==1){
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
											$eleves[$i]["$tmpmin"]=extr_valeur($ligne);
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
											$eleves[$i]["scolarite_an_dernier"]["$tmpmin"]=extr_valeur($ligne);
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
						}
						$cpt++;
					}
					fclose($fp);
					echo "</p>\n";
					flush();

					affiche_debug("count(\$eleves)=".count($eleves)."<br />\n");
					affiche_debug("count(\$tab_ele_id)=".count($tab_ele_id)."<br />\n");
					$stat=0;
					$nb_err=0;
					$stat_etab=0;
					$nb_err_etab=0;
					unset($tab_list_etab);
					$tab_list_etab=array();
					for($i=0;$i<count($eleves);$i++){
						// On ne traite que les élèves affectés dans une classe ($tab_ele_id)
						if(in_array($eleves[$i]['eleve_id'],$tab_ele_id)){
							/*
							if(!isset($eleves[$i]["code_sexe"])){
								$remarques[]="Le sexe de l'élève <a href='#sexe_manquant_".$i."'>".$eleves[$i]["nom"]." ".$eleves[$i]["prenom"]."</a> n'est pas renseigné dans Sconet.";
							}
							*/

							$sql="UPDATE temp_gep_import2 SET ";
							$sql.="elenoet='".$eleves[$i]['elenoet']."', ";
							if(isset($eleves[$i]['id_national'])) {$sql.="elenonat='".$eleves[$i]['id_national']."', ";}
							$sql.="elenom='".addslashes($eleves[$i]['nom'])."', ";
							$sql.="elepre='".addslashes($eleves[$i]['prenom'])."', ";
							$sql.="elesexe='".sexeMF($eleves[$i]["code_sexe"])."', ";
							$sql.="eledatnais='".$eleves[$i]['date_naiss']."', ";
							$sql.="eledoubl='".ouinon($eleves[$i]["doublement"])."', ";
							if(isset($eleves[$i]["scolarite_an_dernier"]["code_rne"])){$sql.="etocod_ep='".$eleves[$i]["scolarite_an_dernier"]["code_rne"]."', ";}
							if(isset($eleves[$i]["code_regime"])){$sql.="elereg='".$eleves[$i]["code_regime"]."', ";}
							$sql=substr($sql,0,strlen($sql)-2);
							$sql.=" WHERE ele_id='".$eleves[$i]['eleve_id']."';";
							affiche_debug("$sql<br />\n");
							$res_insert=mysql_query($sql);
							if(!$res_insert){
								echo "Erreur lors de la requête $sql<br />\n";
								$nb_err++;
								flush();
							}
							else{
								$stat++;
							}


							// Insertion des informations de l'établissement précédent dans une table temporaire:
							if(isset($eleves[$i]["scolarite_an_dernier"]["code_rne"])){
								$sql="INSERT INTO temp_etab_import SET ";
								$cpt_debut_requete=0;


								if($eleves[$i]["scolarite_an_dernier"]["code_rne"]!=""){

									// Renseigner un tableau pour indiquer que c'est un RNE déjà traité... et tester le contenu du tableau
									if(!in_array($eleves[$i]["scolarite_an_dernier"]["code_rne"],$tab_list_etab)){
										$tab_list_etab[]=$eleves[$i]["scolarite_an_dernier"]["code_rne"];

										if(isset($eleves[$i]["scolarite_an_dernier"]["code_rne"])){
											$sql.="id='".addslashes($eleves[$i]["scolarite_an_dernier"]["code_rne"])."'";
											$cpt_debut_requete++;
										}

										/*
										// NOM
										if(isset($eleves[$i]["scolarite_an_dernier"]["denom_compl"])){
											if($cpt_debut_requete>0){
												$sql.=", ";
											}
											$sql.="nom='".addslashes(maj_min_comp($eleves[$i]["scolarite_an_dernier"]["denom_compl"]))."'";
											$cpt_debut_requete++;
										}
										*/

										// NIVEAU
										$chaine="";
										if(isset($eleves[$i]["scolarite_an_dernier"]["denom_princ"])){
											if(ereg("ECOLE",$eleves[$i]["scolarite_an_dernier"]["denom_princ"])){
												$chaine="ecole";
											}
											elseif(ereg("COLLEGE",$eleves[$i]["scolarite_an_dernier"]["denom_princ"])){
												$chaine="college";
											}
											elseif(ereg("LYCEE",$eleves[$i]["scolarite_an_dernier"]["denom_princ"])){
												if(ereg("PROF",$eleves[$i]["scolarite_an_dernier"]["denom_princ"])){
													$chaine="lprof";
												}
												else{
													$chaine="lycee";
												}
											}
											else{
												$chaine="";
											}

											if($cpt_debut_requete>0){
												$sql.=", ";
											}
											$sql.="niveau='".$chaine."'";
											$cpt_debut_requete++;
										}


										// NOM
										if(isset($eleves[$i]["scolarite_an_dernier"]["denom_compl"])){
											if($cpt_debut_requete>0){
												$sql.=", ";
											}
											$nom_etab=trim(maj_min_comp($eleves[$i]["scolarite_an_dernier"]["denom_compl"]));
											if($nom_etab=="") {
												$nom_etab=ucfirst(strtolower($chaine));
											}
											//$sql.="nom='".addslashes(maj_min_comp($eleves[$i]["scolarite_an_dernier"]["denom_compl"]))."'";
											$sql.="nom='".addslashes($nom_etab)."'";
											$cpt_debut_requete++;
										}
										else{
											$sql.=", ";
											$nom_etab=ucfirst(strtolower($chaine));
											$sql.="nom='".addslashes($nom_etab)."'";
											$cpt_debut_requete++;
										}


										// TYPE
										if(isset($eleves[$i]["scolarite_an_dernier"]["denom_princ"])){
											if(ereg("PRIVE",$eleves[$i]["scolarite_an_dernier"]["denom_princ"])){
												$chaine="prive";
											}
											else{
												$chaine="public";
											}

											if($cpt_debut_requete>0){
												$sql.=", ";
											}
											$sql.="type='".$chaine."'";
											$cpt_debut_requete++;
										}

										// CODE POSTAL: Non présent dans le fichier ElevesSansAdresses.xml
										//              Ca y est, il a été ajouté.
										// Il faudrait le fichier Communes.xml ou quelque chose de ce genre.
										if(isset($eleves[$i]["scolarite_an_dernier"]["code_commune_insee"])){
											if($cpt_debut_requete>0){
												$sql.=", ";
											}
											$sql.="cp='".addslashes(maj_min_comp($eleves[$i]["scolarite_an_dernier"]["code_commune_insee"]))."'";
											$cpt_debut_requete++;
										}

										// COMMUNE
										if(isset($eleves[$i]["scolarite_an_dernier"]["ll_commune_insee"])){
											if($cpt_debut_requete>0){
												$sql.=", ";
											}
											$sql.="ville='".addslashes(maj_min_comp($eleves[$i]["scolarite_an_dernier"]["ll_commune_insee"]))."'";
											$cpt_debut_requete++;
										}

										//echo "$sql<br />";

										$res_insert_etab=mysql_query($sql);
										if(!$res_insert_etab){
											echo "Erreur lors de la requête $sql<br />\n";
											$nb_err_etab++;
											flush();
										}
										else{
											$stat_etab++;
										}
									}
								}

							}
						}
					}
					if($nb_err==0) {
						echo "<p>La deuxième phase s'est passée sans erreur.</p>\n";
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
				echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=2'>Suite</a></p>\n";

				require("../lib/footer.inc.php");
				die();
			} // Fin du $step=1
			elseif($step==2){
				$dest_file="../temp/".$tempdir."/eleves.xml";
				$fp=fopen($dest_file,"r");
				if(!$fp){
					echo "<p>Le XML élève n'a pas l'air présent dans le dossier temporaire.<br />Auriez-vous sauté une étape???</p>\n";
					require("../lib/footer.inc.php");
					die();
				}
				else{
					// On récupère les ele_id des élèves qui sont affectés dans une classe
					$sql="SELECT ele_id FROM temp_gep_import2 ORDER BY id_tempo";
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
						$ligne=fgets($fp,4096);
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

								/*
								// Recherche du $i de $eleves[$i] correspondant:
								$temoin_ident="non";
								for($i=0;$i<count($eleves);$i++){
									if($eleves[$i]["eleve_id"]==$tmp_eleve_id){
										$temoin_ident="oui";
										break;
									}
								}
								if($temoin_ident!="oui"){
									unset($tabtmp);
									$tabtmp=explode('"',strstr($ligne[$cpt]," ELENOET="));
									$tmp_elenoet=trim($tabtmp[1]);

									for($i=0;$i<count($eleves);$i++){
										if($eleves[$i]["elenoet"]==$tmp_elenoet){
											$temoin_ident="oui";
											break;
										}
									}
								}
								if($temoin_ident=="oui"){
								*/
									$eleves[$i]["options"]=array();
									$j=0;
									$temoin_opt=1;
								//}
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
								}

								$tab_champs_opt=array("NUM_OPTION","CODE_MODALITE_ELECT","CODE_MATIERE");
								if($temoin_opt_ele==1){
									for($loop=0;$loop<count($tab_champs_opt);$loop++){
										//if(strstr($ligne[$cpt],"<".$tab_champs_opt[$loop].">")){
										if(strstr($ligne,"<".$tab_champs_opt[$loop].">")){
											$tmpmin=strtolower($tab_champs_opt[$loop]);
											//$eleves[$i]["options"][$j]["$tmpmin"]=extr_valeur($ligne[$cpt]);
											$eleves[$i]["options"][$j]["$tmpmin"]=extr_valeur($ligne);
											//echo "\$eleves[$i][\"$tmpmin\"]=".$eleves[$i]["$tmpmin"]."<br />\n";
											break;
										}
									}
								}
							}
						}
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
					if($nb_err==0) {
						echo "<p>La troisième phase s'est passée sans erreur.</p>\n";
					}
					elseif($nb_err==1) {
						echo "<p>$nb_err erreur.</p>\n";
					}
					else{
						echo "<p>$nb_err erreurs</p>\n";
					}

					echo "<p>$stat option(s) ont été mises à jour dans la table 'temp_gep_import2'.</p>\n";

					//echo "<p><a href='".$_SERVER['PHP_SELF']."?etape=1&amp;step=3'>Suite</a></p>\n";
					echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=3'>Suite</a></p>\n";

					require("../lib/footer.inc.php");
					die();
				}
			}
			elseif($step==3){

				echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
				echo "<p>Les codes numériques des options doivent maintenant être traduits en leurs équivalents alphabétiques (<i>ex.: 030201 -&gt; AGL1</i>).</p>\n";
				echo "<p>Veuillez fournir le fichier Nomenclature.xml:<br />\n";
				echo "<input type=\"file\" size=\"80\" name=\"nomenclature_xml_file\" /><br />\n";
				//echo "<input type='hidden' name='etape' value='$etape' />\n";
				echo "<input type='hidden' name='step' value='4' />\n";
				echo "<input type='hidden' name='is_posted' value='yes' />\n";
				//echo "</p>\n";
				echo "<p><input type='submit' value='Valider' /></p>\n";
				echo "</form>\n";
				require("../lib/footer.inc.php");
				die();
			}
			elseif($step==4){
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

					$source_file=stripslashes($xml_file['tmp_name']);
					$dest_file="../temp/".$tempdir."/nomenclature.xml";
					$res_copy=copy("$source_file" , "$dest_file");

					if(!$res_copy){
						echo "<p style='color:red;'>La copie du fichier vers le dossier temporaire a échoué.<br />Vérifiez que l'utilisateur ou le groupe apache ou www-data a accès au dossier temp/$tempdir</p>\n";
						// Il ne faut pas aller plus loin...
						// SITUATION A GERER
						require("../lib/footer.inc.php");
						die();
					}
					else{
						// Lecture du fichier Nomenclature... pour changer les codes numériques d'options dans 'temp_gep_import2' en leur code gestion

						$dest_file="../temp/".$tempdir."/nomenclature.xml";
						$fp=fopen($dest_file,"r");
						if(!$fp){
							echo "<p>Le XML élève n'a pas l'air présent dans le dossier temporaire.<br />Auriez-vous sauté une étape???</p>\n";
							require("../lib/footer.inc.php");
							die();
						}

						/*
						echo "<p>Lecture du fichier Nomenclature...<br />\n";
						//echo "<blockquote>\n";
						while(!feof($fp)){
							$ligne[]=fgets($fp,4096);
						}
						fclose($fp);
						//echo "<p>Terminé.</p>\n";
						*/
						flush();

						echo "<p>";
						echo "Analyse du fichier pour extraire les associations CODE_MATIERE/CODE_GESTION...<br />\n";

						$matieres=array();
						$temoin_matieres=0;
						$temoin_mat=-1;

						$tab_champs_matiere=array("CODE_GESTION",
						"LIBELLE_COURT",
						"LIBELLE_LONG",
						"LIBELLE_EDITION",
						"MATIERE_ETP"
						);

						// PARTIE <MATIERES>
						// Compteur matières:
						$i=-1;
						// Compteur de lignes du fichier:
						$cpt=0;
						$fp=fopen($dest_file,"r");
						//while($cpt<count($ligne)){
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
								break;
							}
							if($temoin_matieres==1){
								//if(strstr($ligne[$cpt],"<MATIERE ")){
								if(strstr($ligne,"<MATIERE ")){
									$i++;
									$matieres[$i]=array();

									//affiche_debug("<p><b>".htmlentities($ligne[$cpt])."</b><br />\n");
									affiche_debug("<p><b>".htmlentities($ligne)."</b><br />\n");
									unset($tabtmp);
									//$tabtmp=explode('"',strstr($ligne[$cpt]," CODE_MATIERE="));
									$tabtmp=explode('"',strstr($ligne," CODE_MATIERE="));
									$matieres[$i]["code_matiere"]=trim($tabtmp[1]);
									//affiche_debug("\$matieres[$i][\"matiere_id\"]=".$matieres[$i]["matiere_id"]."<br />\n");
									affiche_debug("\$matieres[$i][\"code_matiere\"]=".$matieres[$i]["code_matiere"]."<br />\n");
									$temoin_mat=1;
								}
								//if(strstr($ligne[$cpt],"</MATIERE>")){
								if(strstr($ligne,"</MATIERE>")){
									$temoin_mat=0;
								}
								if($temoin_mat==1){
									for($loop=0;$loop<count($tab_champs_matiere);$loop++){
										//if(strstr($ligne[$cpt],"<".$tab_champs_matiere[$loop].">")){
										if(strstr($ligne,"<".$tab_champs_matiere[$loop].">")){
											$tmpmin=strtolower($tab_champs_matiere[$loop]);
											//$matieres[$i]["$tmpmin"]=extr_valeur($ligne[$cpt]);
											$matieres[$i]["$tmpmin"]=extr_valeur($ligne);
											affiche_debug("\$matieres[$i][\"$tmpmin\"]=".$matieres[$i]["$tmpmin"]."<br />\n");
											break;
										}
									}
								}
							}
							$cpt++;
						}

						$sql="SELECT * FROM temp_gep_import2";
						$res1=mysql_query($sql);

						if(mysql_num_rows($res1)==0) {
							echo "<p>La table 'temp_gep_import2' est vide.<br />Ce n'est pas normal.<br />Auriez-vous sauté des étapes???</p>\n";
							require("../lib/footer.inc.php");
							die();
						}

						$stat=0;
						$nb_err=0;
						while($lig=mysql_fetch_object($res1)){
							//echo "<p>";
							//echo "$lig->nom $lig->prenom: ";
							//echo $lig->ELENOM." ".$lig->ELEPRE.": ";
							//echo "$lig->ELEOPT1, ";
							//echo "$lig->ELEOPT2, ";
							//echo "$lig->ELEOPT3, ";

							// Témoin pour tester si une option au moins doit être corrigée:
							$temoin=0;
							$sql="UPDATE temp_gep_import2 SET ";
							for($i=1;$i<12;$i++){
								$eleopt="ELEOPT$i";
								if($lig->$eleopt!=''){
									//if($i>1){
									//	echo ", ";
									//}
									//echo $lig->$eleopt;

									$option="";
									for($k=0;$k<count($matieres);$k++) {
										if($matieres[$k]["code_matiere"]==$lig->$eleopt) {
											$option=$matieres[$k]["code_gestion"];
											break;
										}
									}

									if($option!=""){
										$sql.="$eleopt='$option', ";
										$temoin++;
									}
								}
							}

							if($temoin>0){
								$sql=substr($sql,0,strlen($sql)-2);
								$sql.=" WHERE ele_id='$lig->ELE_ID';";
								affiche_debug($sql."<br />\n");
								$res2=mysql_query($sql);
								if(!$res2){
									echo "Erreur lors de la requête $sql<br />\n";
									flush();
									$nb_err++;
								}
								else{
									$stat++;
								}
							}
							//echo "</p>\n";
						}

						if($nb_err==0) {
							echo "<p>La quatrième phase s'est passée sans erreur.</p>\n";
						}
						elseif($nb_err==1) {
							echo "<p>$nb_err erreur.</p>\n";
						}
						else{
							echo "<p>$nb_err erreurs</p>\n";
						}

						echo "<p>$stat enregistrement(s) ont été mis à jour dans la table 'temp_gep_import2'.</p>\n";

						//echo "<p><a href='".$_SERVER['PHP_SELF']."?etape=1&amp;step=5'>Suite</a></p>\n";
						echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=5'>Suite</a></p>\n";

						require("../lib/footer.inc.php");
						die();
					}
				}
			}
			elseif($step==5){

				echo "<p>Etablissements d'origine des élèves.</p>\n";



				$sql="SELECT * FROM temp_etab_import ORDER BY ville,nom";
				$res_etab=mysql_query($sql);
				if(mysql_num_rows($res_etab)==0){
					echo "<p>Aucun établissement précédent n'a été trouvé.</p>\n";

					echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=7'>Suite</a></p>\n";
				}
				else{
					echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";

					/*
					// Transféré vers /style.css
					echo "<style type='text/css'>

					table.boireaus {
						border-style: solid;
						border-width: 1px;
						border-color: black;
						border-collapse: collapse;
					}

					.boireaus th {
						border-style: solid;
						border-width: 1px;
						border-color: black;
						background-color: #fafabe;
						font-weight:bold;
						text-align:center;
					}

					.boireaus td {
						text-align:center;
						border-style: solid;
						border-width: 1px;
						border-color: black;
					}

					.boireaus .lig-1 {
						background-color: white;
					}
					.boireaus .lig1 {
						background-color: silver;
					}
					.boireaus .nouveau {
						background-color: #96c8f0;
					}
					.boireaus .modif {
						background-color: #aae6aa;
					}

					</style>\n";
					*/

					echo "<table class='boireaus'>\n";
					echo "<tr>\n";
					echo "<th>\n";
					echo "<a href='javascript:modif_case(true)'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
					echo "<a href='javascript:modif_case(false)'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
					echo "</th>\n";
					echo "<th>Statut</th>\n";
					echo "<th>RNE</th>\n";
					echo "<th>Nom</th>\n";
					echo "<th>Niveau</th>\n";
					echo "<th>Type</th>\n";
					echo "<th>Code postal</th>\n";
					echo "<th>Commune</th>\n";
					echo "</tr>\n";
					$alt=1;
					$nombre_ligne=0;
					while($lig=mysql_fetch_object($res_etab)){
						$alt=$alt*(-1);
						$sql="SELECT * FROM etablissements WHERE id='$lig->id'";
						$res_etab2=mysql_query($sql);
						if(mysql_num_rows($res_etab2)==0){
							// Nouvelle entrée

							// #AAE6AA; vert
							// #FAFABE; jaune
							// #96C8F0; bleu

							//echo "<tr style='background-color: #96C8F0;'>\n";
							echo "<tr class='lig$alt'>\n";
							echo "<td class='nouveau'>\n";
							echo "<input type='checkbox' id='case$nombre_ligne' name='rne[]' value='$lig->id' />\n";
							echo "</td>\n";

							echo "<td class='nouveau'>\n";
							echo "Nouveau\n";
							echo "</td>\n";

							echo "<td>\n";
							echo "$lig->id\n";
							echo "</td>\n";

							echo "<td>\n";
							echo "$lig->nom\n";
							echo "</td>\n";

							echo "<td>\n";
							echo "$lig->niveau\n";
							echo "</td>\n";

							echo "<td>\n";
							echo "$lig->type\n";
							echo "</td>\n";

							echo "<td>\n";
							echo "$lig->cp\n";
							echo "</td>\n";

							echo "<td>\n";
							echo "$lig->ville\n";
							echo "</td>\n";

							echo "</tr>\n";

						}
						else{
							// Entrée existante
							$lig2=mysql_fetch_object($res_etab2);

							//echo "<tr style='background-color: #AAE6AA;'>\n";
							echo "<tr class='lig$alt'>\n";
							echo "<td class='modif'>\n";
							echo "<input type='checkbox' id='case$nombre_ligne' name='rne_modif[]' value='$lig->id' />\n";
							echo "</td>\n";

							echo "<td class='modif'>\n";
							echo "Modification\n";
							echo "</td>\n";

							echo "<td>\n";
							echo "$lig->id\n";
							echo "</td>\n";

							echo "<td>\n";
							if($lig->nom!=$lig2->nom){
								echo "$lig2->nom";
								echo " <span style='color:red'>-&gt;</span> ";
							}
							echo "$lig->nom\n";
							echo "</td>\n";

							echo "<td>\n";
							if($lig->niveau!=$lig2->niveau){
								echo "$lig2->niveau";
								echo " <span style='color:red'>-&gt;</span> ";
							}
							echo "$lig->niveau\n";
							echo "</td>\n";

							echo "<td>\n";
							if($lig->type!=$lig2->type){
								echo "$lig2->type";
								echo " <span style='color:red'>-&gt;</span> ";
							}
							echo "$lig->type\n";
							echo "</td>\n";

							echo "<td>\n";
							if($lig->cp!=$lig2->cp){
								echo "$lig2->cp";
								echo " <span style='color:red'>-&gt;</span> ";
							}
							echo "$lig->cp\n";
							echo "</td>\n";

							echo "<td>\n";
							if($lig->ville!=$lig2->ville){
								echo "$lig2->ville";
								echo " <span style='color:red'>-&gt;</span> ";
							}
							echo "$lig->ville\n";
							echo "</td>\n";

							echo "</tr>\n";
						}
						$nombre_ligne++;
					}
					echo "</table>\n";

					echo "<input type='hidden' name='step' value='6' />\n";
					echo "<input type='hidden' name='is_posted' value='yes' />\n";
					echo "<p><input type='submit' value='Valider' /></p>\n";
					echo "</form>\n";


					echo "<script type='text/javascript' language='javascript'>
	function modif_case(statut){
		// statut: true ou false
		for(k=0;k<$nombre_ligne;k++){
			if(document.getElementById('case'+k)){
				document.getElementById('case'+k).checked=statut;
			}
		}
		changement();
	}
</script>\n";

				}


				require("../lib/footer.inc.php");
				die();
			}
			elseif($step==6){

				echo "<p>Etablissements d'origine des élèves.</p>\n";

				$rne=isset($_POST['rne']) ? $_POST['rne'] : NULL;
				$rne_modif=isset($_POST['rne_modif']) ? $_POST['rne_modif'] : NULL;

				$nb_err=0;
				$stat=0;
				if(isset($rne)){
					for($i=0;$i<count($rne);$i++){
						$sql="INSERT INTO etablissements SELECT id,nom,niveau,type,cp,ville FROM temp_etab_import WHERE temp_etab_import.id='".$rne[$i]."'";
						//echo "$sql<br />";
						$res=mysql_query($sql);

						if(!$res){
							echo "Erreur lors de la requête $sql<br />\n";
							flush();
							$nb_err++;
						}
						else{
							$stat++;
						}
					}
				}

				if($nb_err>0){
					echo "<p>$nb_err erreur(s) lors de l'insertion des nouveaux établissements.</p>\n";
				}
				if($stat>0){
					if($stat==1){
						echo "<p>$stat nouvel établissement ajouté.</p>\n";
					}
					else{
						echo "<p>$stat nouveaux établissements ajoutés.</p>\n";
					}
				}

				$nb_err=0;
				$stat=0;
				if(isset($rne_modif)){
					for($i=0;$i<count($rne_modif);$i++){
						$sql="DELETE FROM etablissements WHERE id='".$rne_modif[$i]."'";
						//echo "$sql<br />";
						$res=mysql_query($sql);

						$sql="INSERT INTO etablissements SELECT id,nom,niveau,type,cp,ville FROM temp_etab_import WHERE temp_etab_import.id='".$rne_modif[$i]."'";
						//echo "$sql<br />";
						$res=mysql_query($sql);

						if(!$res){
							echo "Erreur lors de la requête $sql<br />\n";
							flush();
							$nb_err++;
						}
						else{
							$stat++;
						}
					}
				}

				if($nb_err>0){
					echo "<p>$nb_err erreur(s) lors de la modification des établissements.</p>\n";
				}
				if($stat>0){
					if($stat==1){
						echo "<p>$stat modification d'établissement effectuée.</p>\n";
					}
					else{
						echo "<p>$stat modifications d'établissements effectuées.</p>\n";
					}
				}

				echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=7'>Suite</a></p>\n";

				require("../lib/footer.inc.php");
				die();
			}
			else{
				// TERMINé?
				// A LA DERNIERE ETAPE, IL FAUT SUPPRIMER LE FICHIER "../temp/".$tempdir."/eleves.xml"

				if(file_exists("../temp/".$tempdir."/eleves.xml")) {
					echo "<p>Suppression de eleves.xml... ";
					if(unlink("../temp/".$tempdir."/eleves.xml")){
						echo "réussie.</p>\n";
					}
					else{
						echo "<font color='red'>Echec!</font> Vérifiez les droits d'écriture sur le serveur.</p>\n";
					}
				}

				if(file_exists("../temp/".$tempdir."/nomenclature.xml")) {
					echo "<p>Suppression de nomenclature.xml... ";
					if(unlink("../temp/".$tempdir."/nomenclature.xml")){
						echo "réussie.</p>\n";
					}
					else{
						echo "<font color='red'>Echec!</font> Vérifiez les droits d'écriture sur le serveur.</p>\n";
					}
				}

				echo "<p>Suite: <a href='step2.php'>Classes et périodes</a></p>\n";

				require("../lib/footer.inc.php");
				die();
			}



			/*
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
			*/
		}
	}
	require("../lib/footer.inc.php");
?>