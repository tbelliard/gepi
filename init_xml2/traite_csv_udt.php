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

	$sql="SELECT 1=1 FROM droits WHERE id='/init_xml2/traite_csv_udt.php';";
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)==0) {
	$sql="INSERT INTO droits SET id='/init_xml2/traite_csv_udt.php',
	administrateur='V',
	professeur='F',
	cpe='F',
	scolarite='F',
	eleve='F',
	responsable='F',
	secours='F',
	autre='F',
	description='Import des enseignements via un Export CSV UDT',
	statut='';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);
	}


	if (!checkAccess()) {
		header("Location: ../logout.php?auto=1");
		die();
	}

	check_token();

	function get_nom_complet_from_matiere($mat) {
		$sql="SELECT nom_complet FROM matieres WHERE matiere='$mat';";
		$res_mat=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_mat)>0) {
			$lig_mat=mysqli_fetch_object($res_mat);
			return $lig_mat->nom_complet;
		}
	}

	//**************** EN-TETE *****************
	$titre_page = "Outil d'initialisation de l'année : Importation CSV UDT";
	require_once("../lib/header.inc.php");
	//**************** FIN EN-TETE *****************
	include("init_xml_lib.php");

	//debug_var();

	/*
	function affiche_debug($texte) {
		// Passer à 1 la variable pour générer l'affichage des infos de debug...
		$debug=0;
		if($debug==1) {
			echo "<font color='green'>".$texte."</font>";
			flush();
		}
	}
	*/

	// Etape...
	$step=isset($_POST['step']) ? $_POST['step'] : (isset($_GET['step']) ? $_GET['step'] : NULL);

	if(isset($_GET['ad_retour'])) {
		$_SESSION['ad_retour']=$_GET['ad_retour'];
	}
	//echo "\$_SESSION['ad_retour']=".$_SESSION['ad_retour']."<br />";

	$mysql_collate=getSettingValue("mysql_collate") ? getSettingValue("mysql_collate") : "";
	$chaine_mysql_collate="";
	if($mysql_collate!="") {$chaine_mysql_collate="COLLATE $mysql_collate";}

	//unset($remarques);
	//$remarques=array();


	// On va uploader le CSV dans le tempdir de l'utilisateur (administrateur)
	$tempdir=get_user_temp_directory();
	if(!$tempdir) {
		echo "<p style='color:red'>Il semble que le dossier temporaire de l'utilisateur ".$_SESSION['login']." ne soit pas défini!?</p>\n";
		// Il ne faut pas aller plus loin...
		// SITUATION A GERER
	}


	// =======================================================
	// EST-CE ENCORE UTILE?
	if(isset($_GET['nettoyage'])) {
		//echo "<h1 align='center'>Suppression des CSV</h1>\n";
		echo "<h2>Suppression du CSV</h2>\n";
		echo "<p class=bold><a href='";
		if(isset($_SESSION['ad_retour'])) {
			echo $_SESSION['ad_retour'];
		}
		else {
			echo "index.php";
		}
		echo "'> <img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
		echo " | <a href='".$_SERVER['PHP_SELF']."'>Autre import</a></p>\n";
		//echo "</div>\n";

		echo "<p>Si le CSV d'UDT est présent, il sera supprimé...</p>\n";
		//$tabfich=array("f_ele.csv","f_ere.csv");
		$tabfich=array("export_udt.csv");

		for($i=0;$i<count($tabfich);$i++) {
			if(file_exists("../temp/".$tempdir."/$tabfich[$i]")) {
				echo "<p>Suppression de $tabfich[$i]... ";
				if(unlink("../temp/".$tempdir."/$tabfich[$i]")) {
					echo "réussie.</p>\n";
				}
				else {
					echo "<font color='red'>Echec!</font> Vérifiez les droits d'écriture sur le serveur.</p>\n";
				}
			}
		}

		require("../lib/footer.inc.php");
		die();
	}
	// =======================================================
	else {
		echo "<center><h3 class='gepi'>Première phase de l'import CSV UDT</h3></center>\n";
		//echo "<h2>Préparation des données élèves/classes/périodes/options</h2>\n";
		echo "<p class=bold><a href='";
		if(isset($_SESSION['ad_retour'])) {
			// On peut venir de l'index init_xml, de la page de conversion ou de la page de mise à jour Sconet
			echo $_SESSION['ad_retour'];
		}
		else {
			echo "index.php";
		}
		echo "'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

		//echo " | <a href='".$_SERVER['PHP_SELF']."'>Autre import</a>";
		echo " | <a href='".$_SERVER['PHP_SELF']."?nettoyage=oui".add_token_in_url()."'>Suppression d'un CSV existant</a>";
		echo "</p>\n";
		//echo "</div>\n";

		//if(!isset($_POST['is_posted'])) {
		if(!isset($step)) {
			echo "<p class='bold'>Upload du fichier d'export d'UDT.</p>\n";

			echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
			echo "<fieldset style='border: 1px solid grey;";
			echo "background-image: url(\"../images/background/opacite50.png\"); ";
			echo "'>\n";
			echo "<p>Veuillez fournir le fichier d'export CSV d'UDT&nbsp;:<br />\n";
			echo "<input type=\"file\" size=\"65\" name=\"csv_file\" style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\"); padding:5px; margin:5px;' /><br />\n";
			echo "<input type='hidden' name='step' value='0' />\n";
			echo "<input type='hidden' name='is_posted' value='yes' />\n";
			echo add_token_field();
			echo "<p><input type='submit' value='Valider' /></p>\n";
			echo "</fieldset>\n";
			echo "</form>\n";

			echo "<p><i>Remarques</i>&nbsp;:</p>\n";
			echo "<ul>\n";
			echo "<li>Dans UDT, la démarche est menu Recherche/Emploi du temps, valider la recherche, puis cliquer sur Exporter.</li>\n";
			echo "<li>Si l'export généré est au format TXT (<i>séparateur tabulation</i>), se rendre dans le menu Outils/Préférences pour choisir CSV plutôt que TXT pour l'export.</li>\n";
			echo "<li>Les champs du CSV sont&nbsp;:<br />Jour;Heure;Div;Matière;Professeur;Salle;Groupe;Regroup;Eff;Mo;Freq;Aire;</li>\n";
			//echo "<li><span style='color:red'>A FAIRE:</span> Quand un prof n'est pas identifié, trouver les matières associées dans le CSV.</li>\n";
			echo "<li>Si vous disposez d'un export <b>STS_EMP_&lt;RNE&gt;_&lt;ANNEE&gt;</b> après remontée de l'emploi du temps vers STS, il vaut mieux effectuer l'<a href='prof_disc_classe_csv.php?a=a".add_token_in_url()."'>initialisation des enseignements</a> à partir de ce fichier.</li>\n";
			echo "</ul>\n";
		}
		else {
			$post_max_size=ini_get('post_max_size');
			$upload_max_filesize=ini_get('upload_max_filesize');
			$max_execution_time=ini_get('max_execution_time');
			$memory_limit=ini_get('memory_limit');

			if($step==0) {
				$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;

				if(!is_uploaded_file($csv_file['tmp_name'])) {
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
				else {
					if(!file_exists($csv_file['tmp_name'])) {
						echo "<p style='color:red;'>Le fichier aurait été uploadé... mais ne serait pas présent/conservé.</p>\n";

						echo "<p>Les variables du php.ini peuvent peut-être expliquer le problème:<br />\n";
						echo "post_max_size=$post_max_size<br />\n";
						echo "upload_max_filesize=$upload_max_filesize<br />\n";
						echo "et le volume de ".$csv_file['name']." serait<br />\n";
						echo "\$csv_file['size']=".volume_human($csv_file['size'])."<br />\n";
						echo "</p>\n";
						// Il ne faut pas aller plus loin...
						// SITUATION A GERER
						require("../lib/footer.inc.php");
						die();
					}

					echo "<p>Le fichier a été uploadé.</p>\n";

					//$source_file=stripslashes($csv_file['tmp_name']);
					$source_file=$csv_file['tmp_name'];
					$dest_file="../temp/".$tempdir."/export_udt.csv";
					$res_copy=copy("$source_file" , "$dest_file");

					if(!$res_copy) {
						echo "<p style='color:red;'>La copie du fichier vers le dossier temporaire a échoué.<br />Vérifiez que l'utilisateur ou le groupe apache ou www-data a accès au dossier temp/$tempdir</p>\n";
						// Il ne faut pas aller plus loin...
						// SITUATION A GERER
						require("../lib/footer.inc.php");
						die();
					}
					else {
						echo "<p>La copie du fichier vers le dossier temporaire a réussi.</p>\n";

						echo "<p>Veuillez maintenant compléter les correspondances utiles entre UDT et GEPI&nbsp;:</p>\n";

						//$sql="DROP TABLE IF EXISTS udt_corresp;";
						//$suppr_table = mysql_query($sql);

						$sql="CREATE TABLE IF NOT EXISTS udt_corresp (
						champ varchar(255) $chaine_mysql_collate NOT NULL default '',
						nom_udt varchar(255) $chaine_mysql_collate NOT NULL default '',
						nom_gepi varchar(255) $chaine_mysql_collate NOT NULL default ''
						) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
						$create_table = mysqli_query($GLOBALS["mysqli"], $sql);

						//$sql="TRUNCATE TABLE udt_corresp;";
						//$vide_table = mysql_query($sql);




						//$sql="DROP TABLE IF EXISTS udt_lignes;";
						//$suppr_table = mysql_query($sql);

						$sql="CREATE TABLE IF NOT EXISTS udt_lignes (
						id INT(11) unsigned NOT NULL auto_increment,
						division varchar(255) $chaine_mysql_collate NOT NULL default '',
						matiere varchar(255) $chaine_mysql_collate NOT NULL default '',
						prof varchar(255) $chaine_mysql_collate NOT NULL default '',
						groupe varchar(255) $chaine_mysql_collate NOT NULL default '',
						regroup varchar(255) $chaine_mysql_collate NOT NULL default '',
						mo varchar(255) $chaine_mysql_collate NOT NULL default '', 
						PRIMARY KEY id (id)
						) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
						//echo "$sql<br />";
						$create_table = mysqli_query($GLOBALS["mysqli"], $sql);

						$sql="TRUNCATE TABLE udt_lignes;";
						$vide_table = mysqli_query($GLOBALS["mysqli"], $sql);



						//$tabchamps = array("Jour", "Heure", "Div", "Matière", "Professeur", "Salle", "Groupe", "Regroup", "Eff", "Mo", "Freq", "Aire");
						$tabchamps = array("Div", "Matière", "Professeur", "Groupe", "Regroup", "Mo");

						// Lire ligne 1 et la mettre dans $temp
						$fp=fopen($dest_file,"r");
						if(!$fp) {
							echo "<p>ERREUR lors de la tentative d'ouverture du fichier $dest_file</p>\n";
							require("../lib/footer.inc.php");
							die();
						}

						// Lecture de la ligne 1 et la mettre dans $temp
						$temp=fgets($fp,4096);
						$en_tete=explode(";",$temp);

						$tabindice=array();
						// On range dans tabindice les indices des champs retenus
						for ($k = 0; $k < count($tabchamps); $k++) {
							for ($i = 0; $i < count($en_tete); $i++) {
								if (remplace_accents($en_tete[$i]) == remplace_accents($tabchamps[$k])) {
									$tabindice[] = $i;
								}
							}
						}

						if(count($tabindice)!=count($tabchamps)) {
							echo "<p style='color:red'>ERREUR&nbsp;: ".count($tabindice)." champs sur un total de ".count($tabchamps)." ont été identifiés.<br />Les champs sont habituellement&nbsp;:<br />Jour;Heure;Div;Matière;Professeur;Salle;Groupe;Regroup;Eff;Mo;Freq;Aire<br />Sur ces champs, seuls Div, Matière, Professeur, Groupe, Regroup, Mo sont requis</p><p>On suppose pour la suite que les champs sont<br />Jour;Heure;Div;Matière;Professeur;Salle;Groupe;Regroup;Eff;Mo;Freq;Aire<br />Si ce n'est pas le cas, ajoutez à la main la ligne d'entête dans votre fichier et <a href='".$_SERVER['PHP_SELF']."'>refaites cette étape</a>.</p>";
							flush();
							$tabindice=array(2,3,4,6,7,9);
						}

						// Lire le fichier
						$ligne=array();
						while(!feof($fp)){
							$ligne[]=fgets($fp,4096);
						}
						fclose($fp);

						// Lister les classes, matières et profs
						$udt_div=array();
						$udt_matiere=array();
						$udt_prof=array();
						for($i=0;$i<count($ligne);$i++) {
							//echo "\$ligne[$i]=$ligne[$i]<br />";
							if($ligne[$i]!='') {
								$tab=explode(";",$ligne[$i]);
								if(($tab[$tabindice[0]]!='')&&(!in_array($tab[$tabindice[0]],$udt_div))) {$udt_div[]=$tab[$tabindice[0]];}
								if(($tab[$tabindice[1]]!='')&&(!in_array($tab[$tabindice[1]],$udt_matiere))) {$udt_matiere[]=$tab[$tabindice[1]];}
								if(($tab[$tabindice[2]]!='')&&(!in_array($tab[$tabindice[2]],$udt_prof))) {$udt_prof[]=$tab[$tabindice[2]];}
	
								$div=preg_replace("/[^a-zA-Z0-9_. -]/", "", $tab[$tabindice[0]]);
								$matiere=preg_replace("/[^a-zA-Z0-9_. -]/","", $tab[$tabindice[1]]);
								$professeur=preg_replace("/[^a-zA-Z_. -]/","", $tab[$tabindice[2]]);
								$groupe=preg_replace("/[^a-zA-Z0-9_. -]/","", $tab[$tabindice[3]]);
								$regroup=preg_replace("/[^a-zA-Z0-9_. -]/","", $tab[$tabindice[4]]);
								$mo=my_strtoupper($tab[$tabindice[5]]);
	
								//if($mo=="MO") {
								if($regroup!="") {
									$sql="SELECT 1=1 FROM udt_lignes WHERE division='$div' AND matiere='$matiere' AND prof='$professeur' AND groupe='$groupe' AND regroup='$regroup' AND mo='$mo';";
								}
								else {
									$sql="SELECT 1=1 FROM udt_lignes WHERE division='$div' AND matiere='$matiere' AND prof='$professeur';";
								}
								$test=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($test)==0) {
									// Inscrire les correspondances avec noms corrigés
									$sql="INSERT INTO udt_lignes SET division='$div', matiere='$matiere', prof='$professeur', groupe='$groupe', regroup='$regroup', mo='$mo';";
									//echo "$sql<br />\n";
									$insert=mysqli_query($GLOBALS["mysqli"], $sql);
									if(!$insert) {echo "Erreur sur $sql<br />\n";}
								}
								// Plus loin corriger les $udt_div[] de la même façon avant d'insérer les correspondances
							}
						}
						sort($udt_div);
						sort($udt_matiere);
						sort($udt_prof);

						$sql="SELECT id,classe, nom_complet FROM classes ORDER BY classe;";
						$res=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res)==0) {
							echo "<p>ERREUR&nbsp;: Il n'existe aucune classe dans la base.</p>\n";
							require("../lib/footer.inc.php");
							die();
						}

						while($lig=mysqli_fetch_object($res)) {
							$tab_id_classe[]=$lig->id;
							$tab_classe[]=$lig->classe;
							$tab_classe_nom_complet[]=$lig->nom_complet;
						}

						echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
						echo "<input type='hidden' name='step' value='1' />\n";
						echo add_token_field();

						// Identifier les classes
						echo "<p><b>Classes</b>&nbsp;:</p>\n";
						echo "<blockquote>\n";
						echo "<table class='boireaus' summary='Correspondances de classes'>\n";
						echo "<tr>\n";
						echo "<th>Classe dans UDT</th>\n";
						echo "<th>Classe dans Gepi</th>\n";
						echo "</tr>\n";
						$alt=1;
						for($i=0;$i<count($udt_div);$i++) {
							$alt=$alt*(-1);
							echo "<tr class='lig$alt'>\n";
							echo "<td>\n";
							echo "<input type='hidden' name='classe_udt[]' value='$udt_div[$i]' />\n";
							echo "$udt_div[$i]&nbsp;: ";
							echo "</td>\n";
							echo "<td>\n";
							echo "<select name='classe[]'>\n";
							//echo "<select name='id_classe[]'>\n";
							echo "<option value=''>---</option>\n";
							for($j=0;$j<count($tab_classe);$j++) {
								echo "<option value='$tab_classe[$j]'";
								//echo "<option value='$tab_id_classe[$j]'";
								if(my_strtolower($tab_classe[$j])==my_strtolower($udt_div[$i])) {echo " selected='true'";}
								elseif(preg_replace("/ /","",$tab_classe[$j])==preg_replace("/ /","",$udt_div[$i])) {echo " selected='true'";}
								echo ">$tab_classe[$j]</option>\n";
							}
							echo "</select>\n";
							echo "</td>\n";
							echo "</tr>\n";
						}
						echo "</table>\n";
						echo "</blockquote>\n";

						$sql="SELECT matiere, nom_complet FROM matieres ORDER BY matiere;";
						$res=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res)==0) {
							echo "<p>ERREUR&nbsp;: Il n'existe aucune matière dans la base.</p>\n";
							require("../lib/footer.inc.php");
							die();
						}

						while($lig=mysqli_fetch_object($res)) {
							$tab_matiere[]=$lig->matiere;
							$tab_matiere_nom_complet[]=$lig->nom_complet;
						}

						// Les noms avec des PRIME ne sont pas détectés... on se retrouve avec ATPquot pour ATP'
						$tab_udt_matiere=array();
						$sql="SELECT * FROM udt_corresp WHERE champ='matiere' ORDER BY nom_udt;";
						$res=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res)>0) {
							while($lig=mysqli_fetch_object($res)) {
								$tab_udt_matiere[$lig->nom_gepi]=$lig->nom_udt;
								//echo "\$tab_udt_matiere[$lig->nom_gepi]=$lig->nom_udt<br />\n";
							}
						}

						// Identifier les matières
						echo "<p><b>Matières</b>&nbsp;:</p>\n";
						echo "<blockquote>\n";
						echo "<table class='boireaus' summary='Correspondances de matières'>\n";
						echo "<tr>\n";
						echo "<th>Matière dans UDT</th>\n";
						echo "<th>Matière dans Gepi</th>\n";
						echo "<th>Info</th>\n";
						echo "</tr>\n";
						$alt=1;
						for($i=0;$i<count($udt_matiere);$i++) {
							$alt=$alt*(-1);
							echo "<tr class='lig$alt'>\n";
							echo "<td>\n";
							echo "<input type='hidden' name='matiere_udt[]' value='$udt_matiere[$i]' />\n";
							echo "$udt_matiere[$i]&nbsp;: ";
							echo "</td>\n";
							echo "<td>\n";
							echo "<select name='matiere[]'>\n";
							echo "<option value=''>---</option>\n";
							$matiere_trouvee="n";
							for($j=0;$j<count($tab_matiere);$j++) {
								echo "<option value='$tab_matiere[$j]'";
								if((my_strtolower($tab_matiere[$j])==my_strtolower($udt_matiere[$i]))||
								((isset($tab_udt_matiere[$tab_matiere[$j]]))&&(my_strtolower($tab_udt_matiere[$tab_matiere[$j]])==my_strtolower($udt_matiere[$i])))) {
									echo " selected='true'";
									$matiere_trouvee="y";
								}
								echo ">$tab_matiere[$j]</option>\n";
							}
							echo "</select>\n";
							echo "</td>\n";
							echo "<td>\n";
							if($matiere_trouvee=="n") {
								$sql="SELECT * FROM udt_lignes WHERE matiere='$udt_matiere[$i]' ORDER BY division, prof;";
								//echo "$sql<br />\n";
								$res_info=mysqli_query($GLOBALS["mysqli"], $sql);
								if(($res_info)&&(mysqli_num_rows($res_info)>0)) {
									echo "<table align='center' class='boireaus' summary='Information sur la matière $udt_matiere[$i]'>\n";
									$info_alt=1;
									while($lig_info=mysqli_fetch_object($res_info)) {
										$info_alt=$info_alt*(-1);
										echo "<tr class='lig$info_alt'>\n";
										echo "<td>$lig_info->division</td>\n";
										echo "<td>$lig_info->matiere</td>\n";
										echo "<td>$lig_info->prof</td>\n";
										echo "<td>$lig_info->groupe</td>\n";
										echo "<td>$lig_info->regroup</td>\n";
										echo "<td>$lig_info->mo</td>\n";
										echo "</tr>\n";
									}
									echo "</table>\n";
								}
								else{
									echo "???";
								}
							}
							else{
								echo "&nbsp;";
							}
							echo "</td>\n";
							echo "</tr>\n";
						}
						echo "</table>\n";
						echo "</blockquote>\n";

						$tab_udt_prof=array();
						$sql="SELECT * FROM udt_corresp WHERE champ='prof' ORDER BY nom_udt;";
						$res=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res)>0) {
							while($lig=mysqli_fetch_object($res)) {
								$tab_udt_prof[$lig->nom_gepi]=$lig->nom_udt;
								//echo "\$tab_udt_matiere[$lig->nom_gepi]=$lig->nom_udt<br />\n";
							}
						}

						$sql="SELECT login, nom, prenom FROM utilisateurs WHERE statut='professeur' AND etat='actif' ORDER BY nom,prenom;";
						$res=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res)==0) {
							echo "<p>ERREUR&nbsp;: Il n'existe aucun professeur dans la base.</p>\n";
							require("../lib/footer.inc.php");
							die();
						}

						while($lig=mysqli_fetch_object($res)) {
							$tab_login_prof[]=$lig->login;
							$tab_nom_prof[]=my_strtoupper($lig->nom);
							$tab_prenom_prof[]=my_strtoupper($lig->prenom);
						}

						// Identifier les profs
						echo "<p><b>Professeurs</b>&nbsp;:</p>\n";
						echo "<blockquote>\n";
						echo "<table class='boireaus' summary='Correspondances de professeurs'>\n";
						echo "<tr>\n";
						echo "<th>Professeur dans UDT</th>\n";
						echo "<th>Professeur dans Gepi</th>\n";
						echo "<th>Info</th>\n";
						echo "</tr>\n";
						$alt=1;
						for($i=0;$i<count($udt_prof);$i++) {
							$alt=$alt*(-1);
							echo "<tr class='lig$alt'>\n";
							echo "<td>\n";
							echo "<input type='hidden' name='prof_udt[]' value='$udt_prof[$i]' />\n";
							echo "$udt_prof[$i]&nbsp;: ";

							$tmp_prof=explode(" ",$udt_prof[$i]);
							$udt_nom_suppose=$tmp_prof[0];
							$udt_prenom_suppose="";
							if(isset($tmp_prof[1])) {$udt_prenom_suppose=$tmp_prof[1];}
							

							echo "</td>\n";
							echo "<td>\n";
							echo "<select name='login_prof[]'>\n";
							echo "<option value=''>---</option>\n";
							$prof_trouve="n";
							for($j=0;$j<count($tab_login_prof);$j++) {
								echo "<option value='$tab_login_prof[$j]'";

								if((isset($tab_udt_prof[$tab_login_prof[$j]]))&&(my_strtolower($tab_udt_prof[$tab_login_prof[$j]])==my_strtolower($udt_prof[$i]))) {
									echo " selected='true'";
									$prof_trouve="y";
								}
								else {
									if($tab_nom_prof[$j]==my_strtoupper($udt_nom_suppose)) {
										if($tab_prenom_prof[$j]==my_strtoupper($udt_prenom_suppose)) {
											echo " selected='true'";
											$prof_trouve="y";
										}
									}
								}
								echo ">$tab_nom_prof[$j] ".casse_mot($tab_prenom_prof[$j],'majf2')."</option>\n";
							}
							echo "</select>\n";
							echo "</td>\n";
							echo "<td>\n";
							if($prof_trouve=="n") {
								$sql="SELECT * FROM udt_lignes WHERE prof='$udt_prof[$i]' ORDER BY division;";
								$res_info=mysqli_query($GLOBALS["mysqli"], $sql);
								if(($res_info)&&(mysqli_num_rows($res_info)>0)) {
									echo "<table class='boireaus' summary='Information sur le professeur $udt_prof[$i]'>\n";
									$info_alt=1;
									while($lig_info=mysqli_fetch_object($res_info)) {
										$info_alt=$info_alt*(-1);
										echo "<tr class='lig$info_alt'>\n";
										echo "<td>$lig_info->division</td>\n";
										echo "<td>$lig_info->matiere</td>\n";
										echo "<td>$lig_info->prof</td>\n";
										echo "<td>$lig_info->groupe</td>\n";
										echo "<td>$lig_info->regroup</td>\n";
										echo "<td>$lig_info->mo</td>\n";
										echo "</tr>\n";
									}
									echo "</table>\n";
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
						}
						echo "</table>\n";
						echo "</blockquote>\n";

						echo "<input type='hidden' name='is_posted' value='yes' />\n";
						echo "<p style='text-align:center;'><input type='submit' value='Valider' /></p>\n";
						echo "</form>\n";

						//echo "<p style='color:red'>A FAIRE: Pouvoir n'afficher que les comptes actifs</p>\n";
						echo "<p style='color:red'>Si vous ne souhaitez pas créer de groupes de VIE DE CLASSE, REMEDIATION, SOUTIEN,... parce qu'aucune note ne doit être saisie dans ces 'matières', il suffit de ne faire correspondre aucune matière GEPI avec la matière UDT.</p>\n";
					}
				}
			}
			elseif($step==1) {

				if(!isset($_POST['is_posted'])) {
					echo "<p style='color:red'>ERREUR&nbsp;: Une partie des variables n'as pas été POSTée.<br />Vous avez probablement un module PHP qui limite le nombre de variables transmises (<i>suhosin?</i>)</p>\n";
					require("../lib/footer.inc.php");
					die();
				}

				$classe_udt=isset($_POST['classe_udt']) ? $_POST['classe_udt'] : array();
				$classe=isset($_POST['classe']) ? $_POST['classe'] : array();
				//$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : array();

				$matiere_udt=isset($_POST['matiere_udt']) ? $_POST['matiere_udt'] : array();
				$matiere=isset($_POST['matiere']) ? $_POST['matiere'] : array();

				$prof_udt=isset($_POST['prof_udt']) ? $_POST['prof_udt'] : array();
				$login_prof=isset($_POST['login_prof']) ? $_POST['login_prof'] : array();

				echo "<p>Suppression des enseignements, associations élèves/enseignements, classes/enseignements et professeurs/enseignements.</p>\n";
				$del = @mysqli_query($GLOBALS["mysqli"], "DELETE FROM groupes;");
				$del = @mysqli_query($GLOBALS["mysqli"], "DELETE FROM j_eleves_groupes;");
				$del = @mysqli_query($GLOBALS["mysqli"], "DELETE FROM j_groupes_classes;");
				$del = @mysqli_query($GLOBALS["mysqli"], "DELETE FROM j_groupes_professeurs;");
				$del = @mysqli_query($GLOBALS["mysqli"], "DELETE FROM j_groupes_matieres;");
				$del = @mysqli_query($GLOBALS["mysqli"], "DELETE FROM j_signalement;");
				$del = @mysqli_query($GLOBALS["mysqli"], "DELETE FROM j_groupes_visibilite;");
				$del = @mysqli_query($GLOBALS["mysqli"], "DELETE FROM acces_cdt_groupes;");
				// On conserve les associations profs/matières
				//$del = @mysql_query("DELETE FROM j_professeurs_matieres;");

/*
mar aoû 31 22:11:47 steph@hpcrob:~/2010_01_02/hameau
$ grep SVT udt_20100826.csv |egrep "(3 B1|3 B2)"
Mardi;11H00;3 B2;SVT;FERMANEL CHRISTINE;32;;;29;CG;sb;;
Mercredi;10H00;3 B2;SVT;FERMANEL CHRISTINE;32;;;29;CG;;;
Jeudi;08H00;3 B1;SVT;KERAUDREN DELPHINE;32;;;28;CG;;;
Vendredi;09H00;3 B1;SVT;KERAUDREN DELPHINE;32;;;28;CG;sa;;
mar aoû 31 22:12:01 steph@hpcrob:~/2010_01_02/hameau
$                                                          
*/
				$sql="TRUNCATE TABLE udt_corresp;";
				$vide_table=mysqli_query($GLOBALS["mysqli"], $sql);

				for($i=0;$i<count($classe_udt);$i++) {
					if($classe[$i]!="") {
						$sql="INSERT INTO udt_corresp SET champ='classe', nom_udt='".my_ereg_replace("[^a-zA-Z0-9_. -]", "",$classe_udt[$i])."', nom_gepi='$classe[$i]';";
						$res=mysqli_query($GLOBALS["mysqli"], $sql);
					}
				}

				for($i=0;$i<count($matiere_udt);$i++) {
					if($matiere[$i]!="") {
						$sql="INSERT INTO udt_corresp SET champ='matiere', nom_udt='".my_ereg_replace("[^a-zA-Z0-9_. -]","", $matiere_udt[$i])."', nom_gepi='$matiere[$i]';";
						$res=mysqli_query($GLOBALS["mysqli"], $sql);
					}
				}

				/*
				echo "<table><tr><td valign='top'>\n";
				foreach($prof_udt as $key => $value) {
				echo "\$prof_udt[$key]=$value<br />\n";
				}
				echo "</td><td valign='top'>\n";
				foreach($login_prof as $key => $value) {
				echo "\$login_prof[$key]=$value<br />\n";
				}
				echo "</td></tr></table>\n";
				*/

				for($i=0;$i<count($prof_udt);$i++) {
					if($login_prof[$i]!="") {
					//if((isset($login_prof[$i]))&&($login_prof[$i]!="")) {
						$sql="INSERT INTO udt_corresp SET champ='prof', nom_udt='".my_ereg_replace("[^a-zA-Z_. -]","", $prof_udt[$i])."', nom_gepi='$login_prof[$i]';";
						//echo "$sql<br />\n";
						$res=mysqli_query($GLOBALS["mysqli"], $sql);
					}
				}

				$nom_classe=array();
				$sql="SELECT id,classe FROM classes;";
				$res_classes=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_classes)==0) {
					echo "<p style='color:red'>Aucune classe n'existe encore.</p>\n";
					require("../lib/footer.inc.php");
					die();
				}
				while($lig_classe=mysqli_fetch_object($res_classes)) {
					$nom_classe[$lig_classe->id]=$lig_classe->classe;
				}

				$nom_prenom_prof=array();
				$sql="SELECT login, civilite, nom, prenom FROM utilisateurs WHERE statut='professeur';";
				$res_profs=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_profs)==0) {
					echo "<p style='color:red'>Aucun prof n'existe encore.</p>\n";
					require("../lib/footer.inc.php");
					die();
				}
				while($lig_profs=mysqli_fetch_object($res_profs)) {
					$nom_prenom_prof[$lig_profs->login]=$lig_profs->civilite." ".my_strtoupper($lig_profs->nom)." ".casse_mot($lig_profs->prenom,'majf2');
				}

				$lignes_deja_traitees=array();
				//$sql="SELECT * FROM udt_lignes;";
				$sql="SELECT * FROM udt_lignes ORDER BY regroup, division, matiere;";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res)>0) {
					while($lig=mysqli_fetch_object($res)) {
						if(in_array($lig->id,$lignes_deja_traitees)) {
							/*echo "<p style='color:red'><b>Ligne $lig->id déjà traitée.</b><br />\n";
							echo "division=$lig->division<br />\n";
							echo "matiere=$lig->matiere<br />\n";
							echo "prof=$lig->prof<br />\n";
							echo "groupe=$lig->groupe<br />\n";
							echo "regroup=$lig->regroup<br />\n";
							echo "mo=$lig->mo<br />\n";
							*/
							echo "<p>L'association $lig->division/$lig->matiere/$lig->prof";
							if($lig->groupe!="") {echo " (<i>$lig->groupe</i>)";}
							if($lig->regroup!="") {echo " (<i>$lig->regroup</i>)";}
							echo " est déjà traitée.";
							echo "<hr />\n";
						}
						else {
							/*
							echo "<p>\n";
							echo "id=$lig->id<br />\n";
							echo "division=$lig->division<br />\n";
							echo "matiere=$lig->matiere<br />\n";
							echo "prof=$lig->prof<br />\n";
							echo "groupe=$lig->groupe<br />\n";
							echo "regroup=$lig->regroup<br />\n";
							echo "mo=$lig->mo<br />\n";
							*/

							if(!isset($tab_ele[$lig->division])) {
								$tab_ele[$lig->division]=array();
	
								//$sql="SELECT DISTINCT jec.login, c.classe FROM j_eleves_classes jec, classes c, udt_corresp uc WHERE jec.id_classe=c.id AND c.classe=uc.nom_gepi AND uc.champ='classe' AND classe_udt='$lig' ORDER BY jec.login;";
								//$sql="SELECT DISTINCT jec.login FROM j_eleves_classes jec, classes c, udt_corresp uc WHERE jec.id_classe=c.id AND c.classe=uc.nom_gepi AND uc.champ='classe' AND uc.nom_udt='$lig->division' ORDER BY jec.login;";
								$sql="SELECT DISTINCT jec.login, c.id FROM j_eleves_classes jec, classes c, udt_corresp uc WHERE jec.id_classe=c.id AND c.classe=uc.nom_gepi AND uc.champ='classe' AND uc.nom_udt='$lig->division' ORDER BY jec.login;";
								//echo "$sql<br />\n";
								$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
								while($lig_ele=mysqli_fetch_object($res_ele)) {
									//$tab_ele[$lig->division][]=$lig_ele->login;
									$tab_ele[$lig_ele->id][]=$lig_ele->login;
								}
							}
							//echo "</p>\n";
	
							if(!isset($tab_per[$lig->division])) {
								//$sql="SELECT MAX(num_periode) AS max_per FROM periodes p, classes c, udt_corresp uc WHERE c.classe=uc.nom_gepi AND uc.nom_udt='$lig->division' AND c.id=p.id_classe;";
								//$sql="SELECT MAX(num_periode) AS max_per, c.id FROM periodes p, classes c, udt_corresp uc WHERE c.classe=uc.nom_gepi AND uc.nom_udt='$lig->division' AND c.id=p.id_classe GROUP BY c.id;";
								$sql="SELECT num_periode AS max_per, c.id FROM periodes p, classes c, udt_corresp uc WHERE c.classe=uc.nom_gepi AND uc.nom_udt='$lig->division' AND c.id=p.id_classe ORDER BY num_periode DESC LIMIT 1;";
								//echo "$sql<br />\n";
								$periode_query=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($periode_query)>0) {
									$lig_per=mysqli_fetch_object($periode_query);
									//$tab_per[$lig->division]=$lig_per->max_per;
									//echo "\$tab_per[$lig->division]=$lig_per->max_per<br />\n";
									$tab_per[$lig_per->id]=$lig_per->max_per;
									//echo "\$tab_per[$lig_per->id]=$lig_per->max_per<br />\n";
								}
							}


	// Si c'est un regroupement, il va y avoir plusieurs classes associées
	// Il faudrait alors les récupérer... et ne pas traiter à nouveau les lignes correspondantes de udt_lignes quand on va les atteindre...
							$tab_clas=array();
							if($lig->regroup!="") {
								//$sql="SELECT c.id FROM classes c, udt_corresp uc, udt_lignes ul WHERE ul.regroup='$lig->regroup' AND uc.nom_udt=ul.division AND uc.nom_gepi=c.classe AND uc.champ='classe';";
								// Est-ce qu'un même regroupement peut correspondre à plusieurs matières? Sans doute...
								$sql="SELECT DISTINCT c.id FROM classes c, udt_corresp uc, udt_lignes ul WHERE ul.regroup='$lig->regroup' AND ul.matiere='$lig->matiere' AND uc.nom_udt=ul.division AND uc.nom_gepi=c.classe AND uc.champ='classe';";
								//echo "<span style='color:green'>$sql</span><br />\n";
								$res_clas=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($res_clas)>0) {
									while($lig_clas=mysqli_fetch_object($res_clas)) {
										$tab_clas[]=$lig_clas->id;
										//echo "\$tab_clas[]=$lig_clas->id<br />\n";
									}
								}

								$sql="SELECT ul.id FROM udt_lignes ul WHERE ul.regroup='$lig->regroup' AND ul.matiere='$lig->matiere';";
								$res_lig=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($res_lig)>0) {
									while($lig_lig=mysqli_fetch_object($res_lig)) {
										$lignes_deja_traitees[]=$lig_lig->id;
									}
								}
							}
							else {
								$id_classe="";
								$sql="SELECT DISTINCT c.id FROM classes c, udt_corresp uc WHERE uc.nom_udt='$lig->division' AND uc.nom_gepi=c.classe AND uc.champ='classe';";
								//echo "$sql<br />\n";
								$res_clas=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($res_clas)==0) {
									echo "<p>Aucune classe n'est associée à $lig->division dans Gepi.</p>\n";
								}
								elseif(mysqli_num_rows($res_clas)>1) {
									echo "<p>ANOMALIE: Plus d'une classe est associée à $lig->division dans Gepi.</p>\n";
								}
								else {
									$lig_clas=mysqli_fetch_object($res_clas);
									$id_classe=$lig_clas->id;
									//echo "\$id_classe=$id_classe<br />\n";
	
									$tab_clas=array($id_classe);
								}
							}
	
							$mat="";
							$sql="SELECT DISTINCT m.matiere, m.nom_complet FROM matieres m, udt_corresp uc WHERE uc.nom_udt='$lig->matiere' AND uc.nom_gepi=m.matiere AND uc.champ='matiere';";
							//echo "$sql<br />\n";
							$res_mat=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res_mat)==0) {
								echo "<p>Aucune matière n'est associée dans Gepi à la matière $lig->matiere d'UDT.</p>\n";
							}
							elseif(mysqli_num_rows($res_mat)>1) {
								echo "<p>ANOMALIE: Plus d'une matière est associée dans Gepi à la matière $lig->matiere d'UDT.</p>\n";
							}
							else {
								$lig_mat=mysqli_fetch_object($res_mat);
								$mat=$lig_mat->matiere;
								$mat_nom_complet=$lig_mat->nom_complet;
								//echo "\$mat=$mat<br />\n";
								//echo "\$mat_nom_complet=$mat_nom_complet<br />\n";

								if($lig->regroup=="") {
									$nom_grp=$mat;
									$descr_grp=get_nom_complet_from_matiere($mat);
								}
								else {
									$nom_grp=$mat."_".$lig->regroup;
									//$descr_grp=get_nom_complet_from_matiere($mat)." ($lig->regroup ($lig->groupe))";
									$descr_grp=get_nom_complet_from_matiere($mat)." ($lig->groupe)";
								}

								if(count($tab_clas)==0) {
									echo "<p clas='color:red'>Aucune classe n'est associée au groupe de $mat.</p>\n";
								}
								else {
									//echo "<b>Récapitulatif avant création du groupe&nbsp;:</b>\n";
									echo "<p>Création du groupe $nom_grp ($descr_grp) associé à la matière $mat ";
									$cpt_clas=0;
									echo "(<i>";
									foreach($tab_clas as $key => $value) {
										if($cpt_clas>0) {echo ", ";}
										//echo "\$tab_clas[$key]=$value<br />\n";
										//echo get_class_from_id($value);
										echo $nom_classe[$value];
										$cpt_clas++;
									}
									echo "</i>)&nbsp;: ";

									if($id_groupe=create_group($nom_grp, $descr_grp, $mat, $tab_clas)) {
										//echo "<span style='color:blue;'>Création du groupe $nom_grp ($descr_grp) avec l'id_groupe $id_groupe</span><br />\n";
										echo "SUCCES&nbsp;: Groupe créé avec l'id_groupe $id_groupe<br />\n";
	
										// Récupérer les profs
										$tab_prof=array();
										/*
										// CA NE FONCTIONNE PAS
										// J'AI
	
	mysql> select * from udt_lignes where matiere='IDARH';
	+-----+----------+---------+-----------------+--------+----------+----+
	| id  | division | matiere | prof            | groupe | regroup  | mo |
	+-----+----------+---------+-----------------+--------+----------+----+
	| 151 | 5 A1     | IDARH   | ORELLANA NICOLE | IDARH1 | IDARH1   | CG |
	| 157 | 5 A2     | IDARH   | ORELLANA NICOLE | IDARH1 | IDARH1   | CG |
	| 163 | 5 B1     | IDARH   | ORELLANA NICOLE | IDARH1 | IDARH1   | CG |
	| 169 | 5 B2     | IDARH   | ORELLANA NICOLE | IDARH1 | IDARH1   | CG |
	| 175 | 5 C      | IDARH   | ORELLANA NICOLE | IDARH1 | IDARH1   | CG |
	| 296 | 5 A1     | IDARH   | CAUSSE JOSIANE  | IDARH  | 5IDARH-1 | CG |
	| 303 | 5 A2     | IDARH   | CAUSSE JOSIANE  | IDARH  | 5IDARH-1 | CG |
	| 309 | 5 B1     | IDARH   | CAUSSE JOSIANE  | IDARH  | 5IDARH-1 | CG |
	| 315 | 5 B2     | IDARH   | CAUSSE JOSIANE  | IDARH  | 5IDARH-1 | CG |
	| 321 | 5 C      | IDARH   | CAUSSE JOSIANE  | IDARH  | 5IDARH-1 | CG |
	+-----+----------+---------+-----------------+--------+----------+----+
	10 rows in set (0.00 sec)
	
	mysql>
										// Les noms de groupes et regroup ne coïncident pas.
										// Si on considère qu'on a un seul enseignement d'une matière par classe, on va fusionner les groupes d'AGL1 (alors que plusieurs profs se répartissent les élèves de plusieurs classes)
	
										if($lig->regroup=="") {
											// Ce n'est pas un groupe/regroupement, on ne teste pas si plusieurs profs sont associés
											// S'il y a plusieurs enseignements de MATHS avec plusieurs profs sans groupes, on considère que tout les élèves sont dans chacun des groupes
											// Il faudra fusionner après coup si ce n'est pas le cas
	
											$sql="SELECT DISTINCT uc.nom_gepi FROM udt_corresp uc WHERE uc.nom_udt='$lig->prof' AND uc.champ='prof';";
											echo "<span style='color:plum'>$sql</span><br />\n";
											$res_prof=mysql_query($sql);
											if(mysql_num_rows($res_prof)>0) {
												// On ne fait qu'un tour dans la boucle
												while($lig_prof=mysql_fetch_object($res_prof)) {
													$tab_prof[]=$lig_prof->nom_gepi;
													echo "\$tab_prof[]=$lig_prof->nom_gepi<br />\n";
		
													// Contrôler l'association avec la matière ou bien le update_group() le fait?
													$sql="SELECT 1=1 FROM j_professeurs_matieres WHERE id_professeur='$lig_prof->nom_gepi' AND id_matiere='$mat';";
													$test=mysql_query($sql);
													if(mysql_num_rows($test)==0) {
														$sql="INSERT INTO j_professeurs_matieres SET id_professeur='$lig_prof->nom_gepi', id_matiere='$mat';";
														$insert=mysql_query($sql);
														if(!$insert) {echo "<b>ERREUR</b> lors de l'association du professeur $lig_prof->nom_gepi avec la matière $mat<br />\n";}
													}
												}
											}
	
										}
										else {
											// Dans le cas d'un groupe/regroupement, on contrôle s'il y a plusieurs profs associés
	
											$sql="SELECT DISTINCT uc.nom_gepi FROM udt_corresp uc WHERE uc.nom_udt='$lig->prof' AND uc.champ='prof';";
											echo "<span style='color:plum'>$sql</span><br />\n";
											$res_prof=mysql_query($sql);
											if(mysql_num_rows($res_prof)>0) {
												while($lig_prof=mysql_fetch_object($res_prof)) {
													$tab_prof[]=$lig_prof->nom_gepi;
													echo "\$tab_prof[]=$lig_prof->nom_gepi<br />\n";
		
													// Contrôler l'association avec la matière ou bien le update_group() le fait?
													$sql="SELECT 1=1 FROM j_professeurs_matieres WHERE id_professeur='$lig_prof->nom_gepi' AND id_matiere='$mat';";
													$test=mysql_query($sql);
													if(mysql_num_rows($test)==0) {
														$sql="INSERT INTO j_professeurs_matieres SET id_professeur='$lig_prof->nom_gepi', id_matiere='$mat';";
														$insert=mysql_query($sql);
														if(!$insert) {echo "<b>ERREUR</b> lors de l'association du professeur $lig_prof->nom_gepi avec la matière $mat<br />\n";}
													}
												}
											}
	
										}
										*/
	
										$sql="SELECT DISTINCT uc.nom_gepi FROM udt_corresp uc WHERE uc.nom_udt='$lig->prof' AND uc.champ='prof';";
										//echo "<span style='color:plum'>$sql</span><br />\n";
										$res_prof=mysqli_query($GLOBALS["mysqli"], $sql);
										if(mysqli_num_rows($res_prof)>0) {
											echo "Association des professeurs au groupe&nbsp;: ";
											$cpt_prof=0;
											while($lig_prof=mysqli_fetch_object($res_prof)) {
												if($cpt_prof>0) {echo ", ";}

												$tab_prof[]=$lig_prof->nom_gepi;
												//echo "\$tab_prof[]=$lig_prof->nom_gepi<br />\n";
												echo $nom_prenom_prof[$lig_prof->nom_gepi];

												// Contrôler l'association avec la matière ou bien le update_group() le fait?
												$sql="SELECT 1=1 FROM j_professeurs_matieres WHERE id_professeur='$lig_prof->nom_gepi' AND id_matiere='$mat';";
												$test=mysqli_query($GLOBALS["mysqli"], $sql);
												if(mysqli_num_rows($test)==0) {

													$sql="SELECT ordre_matieres FROM j_professeurs_matieres WHERE id_professeur='$lig_prof->nom_gepi' ORDER BY ordre_matieres DESC LIMIT 1;";
													$res_max_ordre_matiere=mysqli_query($GLOBALS["mysqli"], $sql);
													if(mysqli_num_rows($res_max_ordre_matiere)==0) {
														$ordre_mat=1;
													}
													else {
														$lig_ordre_mat=mysqli_fetch_object($res_max_ordre_matiere);
														$ordre_mat=$lig_ordre_mat->ordre_matieres+1;
													}

													$sql="INSERT INTO j_professeurs_matieres SET id_professeur='$lig_prof->nom_gepi', id_matiere='$mat', ordre_matieres='$ordre_mat';";
													$insert=mysqli_query($GLOBALS["mysqli"], $sql);
													if(!$insert) {echo "<br /><b>ERREUR</b> lors de l'association du professeur $lig_prof->nom_gepi avec la matière $mat<br />\n";}
													else {echo " (<i>association du professeur avec la matière $mat</i>)";}
												}
												$cpt_prof++;
											}
										}
										else {
											echo "<span style='color:red'>Aucun professeur n'est associé au groupe.</span>\n";
										}
										echo "<br />\n";
	
	
	
										$temoin_aucun_eleve_dans_l_enseignement="y";
										if($lig->groupe!="") {
											for($loop=0;$loop<count($tab_clas);$loop++) {
												foreach($tab_ele[$tab_clas[$loop]] as $key => $value) {
													$sql="SELECT 1=1 FROM temp_gep_import2 WHERE (ELEOPT1='$mat' OR ELEOPT2='$mat' OR ELEOPT3='$mat' OR ELEOPT4='$mat' OR ELEOPT5='$mat' OR ELEOPT6='$mat' OR ELEOPT7='$mat' OR ELEOPT8='$mat' OR ELEOPT9='$mat' OR ELEOPT10='$mat' OR ELEOPT11='$mat' OR ELEOPT12='$mat') AND LOGIN='$value';";
													$test=mysqli_query($GLOBALS["mysqli"], $sql);
													if(mysqli_num_rows($test)>0) {
														$temoin_aucun_eleve_dans_l_enseignement="n";
														break;
													}
												}
											}
										}
	
	
	
										$reg_eleves=array();
										for($loop=0;$loop<count($tab_clas);$loop++) {
											//for($loopp=0;$loopp<count($tab_ele[$tab_clas[$loop]]);$loopp++) {
											foreach($tab_ele[$tab_clas[$loop]] as $key => $value) {
												//echo "\$tab_ele[\$tab_clas[$loop]][$key]=\$tab_ele[$tab_clas[$loop]][$key]=$value<br />\n";
	
												// PROBLEME: Les cours qui se font à la fois en classe entière et en groupe se retrouvent avec un effectif de zéro... d'où l'ajout du test sur aucun élève dans le groupe...
												if($temoin_aucun_eleve_dans_l_enseignement=="y") {
													$temoin_enseignement_suivi="y";
												}
												else {
													$temoin_enseignement_suivi="n";
													if($lig->groupe=="") {
														$temoin_enseignement_suivi="y";
													}
													else {
														$sql="SELECT 1=1 FROM temp_gep_import2 WHERE (ELEOPT1='$mat' OR ELEOPT2='$mat' OR ELEOPT3='$mat' OR ELEOPT4='$mat' OR ELEOPT5='$mat' OR ELEOPT6='$mat' OR ELEOPT7='$mat' OR ELEOPT8='$mat' OR ELEOPT9='$mat' OR ELEOPT10='$mat' OR ELEOPT11='$mat' OR ELEOPT12='$mat') AND LOGIN='$value';";
														$test=mysqli_query($GLOBALS["mysqli"], $sql);
														if(mysqli_num_rows($test)>0) {
															$temoin_enseignement_suivi="y";
														}
													}
												}
	
												if($temoin_enseignement_suivi=="y") {
													for($n_per=1;$n_per<=$tab_per[$tab_clas[$loop]];$n_per++) {$reg_eleves[$n_per][]=$value;}
													if($debug_import=="y") {echo "<span style='color:green'>$value suit l'enseignement n°$id_groupe de $mat</span><br />";}
												}
												elseif($debug_import=="y") {echo "<span style='color:red'>$value ne suit pas l'enseignement n°$id_groupe de $mat</span><br />";}
											}
										}
	
										//$create = update_group($id_groupe, $id_matiere[$i], $mat_nom_complet, $id_matiere[$i], $reg_clazz, $reg_professeurs, $reg_eleves);
										$update_group=update_group($id_groupe, $nom_grp, $descr_grp, $mat, $tab_clas, $tab_prof, $reg_eleves);
										if(!$update_group) {
											echo "<span style='color:red;'>ERREUR lors de l'association des professeur(s) et élève(s) avec le groupe.</span><br />\n";
										}
									}
									else {
										echo "<span style='color:red;'>ERREUR lors de la création du groupe pour...</span><br />\n";
									}

								}
							}

							echo "<hr />\n";
						}
					}
				}

				echo "<center><p><a href='init_pp.php?a=a".add_token_in_url()."'>Import des professeurs principaux</a><br />Il est probable que cette information n'était pas dans le fichier de STS, l'import des professeurs principaux risque de ne rien donner... mais qui ne tente rien...</p></center>\n";

			}
		}
	}

	require("../lib/footer.inc.php");
?>
