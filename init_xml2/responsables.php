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
	$titre_page = "Outil d'initialisation de l'année : Importation des responsables des élèves";
	require_once("../lib/header.inc.php");
	//**************** FIN EN-TETE *****************

	require_once("init_xml_lib.php");

	// Etape...
	$step=isset($_POST['step']) ? $_POST['step'] : (isset($_GET['step']) ? $_GET['step'] : NULL);

	$verif_tables_non_vides=isset($_POST['verif_tables_non_vides']) ? $_POST['verif_tables_non_vides'] : NULL;

	// Passer à 'y' pour afficher les requêtes
	$debug_resp='n';

	if(isset($_GET['ad_retour'])){
		$_SESSION['ad_retour']=$_GET['ad_retour'];
	}
	//echo "\$_SESSION['ad_retour']=".$_SESSION['ad_retour']."<br />";

	include("../lib/initialisation_annee.inc.php");
	$liste_tables_del = $liste_tables_del_etape_resp;


	// On va uploader les fichiers XML dans le tempdir de l'utilisateur (administrateur, ou scolarité pour les màj Sconet)
	$tempdir=get_user_temp_directory();
	if(!$tempdir){
		echo "<p style='color:red'>Il semble que le dossier temporaire de l'utilisateur ".$_SESSION['login']." ne soit pas défini!?</p>\n";
		// Il ne faut pas aller plus loin...
		// SITUATION A GERER
	}


	// =======================================================
	// EST-CE ENCORE UTILE?
	if(isset($_GET['nettoyage'])) {
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
	else {
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
		echo " | <a href='".$_SERVER['PHP_SELF']."?nettoyage=oui".add_token_in_url()."'>Suppression des fichiers XML existants</a>";
		echo "</p>\n";
		//echo "</div>\n";

		//debug_var();

		//if(!isset($_POST['is_posted'])){
		if(!isset($step)) {
			if(!isset($verif_tables_non_vides)) {
				$j=0;
				$flag=0;
				$chaine_tables="";
				while (($j < count($liste_tables_del)) and ($flag==0)) {
					if (old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
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
					echo "Des données concernant les responsables sont actuellement présentes dans la base GEPI<br /></p>\n";
					echo "<p>Si vous poursuivez la procédure ces données seront effacées.</p>\n";

					echo "<p>Les tables vidées seront&nbsp;: $chaine_tables</p>\n";

					echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
					echo add_token_field();
					echo "<input type=hidden name='verif_tables_non_vides' value='y' />\n";
					echo "<input type='submit' name='confirm' value='Poursuivre la procédure' />\n";
					echo "</form>\n";

					$sql="SELECT 1=1 FROM utilisateurs WHERE statut='responsable';";
					if($debug_resp=='y') {echo "<span style='color:green;'>$sql</span><br />";}
					$test=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($test)>0) {
						$sql="SELECT 1=1 FROM tempo_utilisateurs WHERE statut='responsable';";
						if($debug_resp=='y') {echo "<span style='color:green;'>$sql</span><br />";}
						$test=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($test)==0) {
							echo "<p style='color:red'>Il existe un ou des comptes responsables de l'année passée, et vous n'avez pas mis ces comptes en réserve pour imposer le même login/mot de passe cette année.<br />Est-ce bien un choix délibéré ou un oubli de votre part?<br />Pour conserver ces login/mot de de passe de façon à ne pas devoir re-distribuer ces informations (<em>et éviter de perturber ces utilisateurs</em>), vous pouvez procéder à la mise en réserve avant d'initialiser l'année dans la page <a href='../gestion/changement_d_annee.php'>Changement d'année</a> (<em>vous y trouverez aussi la possibilité de conserver les comptes élèves (s'ils n'ont pas déjà été supprimés) et bien d'autres actions à ne pas oublier avant l'initialisation</em>).</p>\n";
						}
					}

					echo "<p><br /></p>\n";
					require("../lib/footer.inc.php");
					die();
				}
			}
	
			if(isset($verif_tables_non_vides)) {
				check_token(false);
				$j=0;
				while ($j < count($liste_tables_del)) {
					if (old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
						$sql="DELETE FROM $liste_tables_del[$j];";
						if($debug_resp=='y') {echo "<span style='color:green;'>$sql</span><br />";}
						$del=@mysqli_query($GLOBALS["mysqli"], $sql);
					}
					$j++;
				}

				// Ménage infos_actions:
				$sql="SELECT * FROM infos_actions WHERE titre LIKE 'Nouveau responsable%';";
				//echo "$sql<br />";
				$test = mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($test)>0) {
					echo "<br />";
					echo "Suppression d'anciens messages en page d'accueil invitant à créer de nouveaux comptes responsables.";
					$sql="DELETE FROM infos_actions WHERE titre LIKE 'Nouveau responsable%';";
					$del = mysqli_query($GLOBALS["mysqli"], $sql);
				}

				// Suppression des comptes de responsables:
				$sql="DELETE FROM utilisateurs WHERE statut='responsable';";
				//echo "$sql<br />";
				if($debug_resp=='y') {echo "<span style='color:green;'>$sql</span><br />";}
				$del=mysqli_query($GLOBALS["mysqli"], $sql);
			}

			echo "<p><b>ATTENTION ...</b><br />Vous ne devez procéder à cette opération uniquement si la constitution des classes a été effectuée !</p>\n";

			echo "<form enctype='multipart/form-data' id='form_envoi_xml' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
			echo "<fieldset style='border: 1px solid grey;";
			echo "background-image: url(\"../images/background/opacite50.png\"); ";
			echo "'>\n";
			echo add_token_field();
			echo "<p>Veuillez fournir le fichier ResponsablesAvecAdresses.xml&nbsp;:<br />\n";
			echo "<input type=\"file\" size=\"65\" name=\"responsables_xml_file\" id='input_xml_file' style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\"); padding:5px; margin:5px;' /><br />\n";
			if ($gepiSettings['unzipped_max_filesize']>=0) {
				echo "<p style=\"font-size:small; color: red;\"><em>REMARQUE&nbsp;:</em> Vous pouvez fournir à Gepi le fichier compressé issu directement de SCONET (<em>Ex&nbsp;: ResponsablesAvecAdresses.zip</em>).</p>";
			}
			echo "<input type='hidden' name='step' value='0' />\n";
			echo "<input type='hidden' name='is_posted' value='yes' />\n";
			//echo "<p><input type='submit' value='Valider' /></p>\n";
			echo "<p><input type='submit' id='input_submit' value='Valider' />
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
</script>\n";
			echo "</form>\n";
		}
		else {
			check_token(false);
			$post_max_size=ini_get('post_max_size');
			$upload_max_filesize=ini_get('upload_max_filesize');
			$max_execution_time=ini_get('max_execution_time');
			$memory_limit=ini_get('memory_limit');


			if($step==0){
				$xml_file = isset($_FILES["responsables_xml_file"]) ? $_FILES["responsables_xml_file"] : NULL;
				/*
				echo "<pre>";
				print_r($xml_file);
				echo "</pre>";
				*/
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
								PRIMARY KEY  (`pers_id`)
								) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
						$create_table = mysqli_query($GLOBALS["mysqli"], $sql);

						//$sql="TRUNCATE TABLE temp_resp_pers_import;";
						$sql="TRUNCATE TABLE resp_pers;";
						$vide_table = mysqli_query($GLOBALS["mysqli"], $sql);

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

						$resp_xml=simplexml_load_file($dest_file);
						if(!$resp_xml) {
							echo "<p style='color:red;'>ECHEC du chargement du fichier avec simpleXML.</p>\n";
							require("../lib/footer.inc.php");
							die();
						}

						$nom_racine=$resp_xml->getName();
						if(my_strtoupper($nom_racine)!='BEE_RESPONSABLES') {
							echo "<p style='color:red;'>ERREUR: Le fichier XML fourni n'a pas l'air d'être un fichier XML Responsables.<br />Sa racine devrait être 'BEE_RESPONSABLES'.</p><p><a href='".$_SERVER['PHP_SELF']."'>Retour au choix du fichier.</a></p>\n";
							require("../lib/footer.inc.php");
							die();
						}


						/*
						<PARAMETRES>
							<UAJ>TEL_RNE</UAJ>
							<ANNEE_SCOLAIRE>2011</ANNEE_SCOLAIRE>
							<DATE_EXPORT>22/05/2012</DATE_EXPORT>
							<HORODATAGE>22/05/2012 08:09:38</HORODATAGE>
						</PARAMETRES>
						<DONNEES>
							...
						*/
						$xml_uaj="";
						$xml_horodatage="";
						$objet_parametres=($resp_xml->PARAMETRES);
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
						$texte_maj_sconet="<br /><p><strong>Fichier XML élève</strong>";
						if($xml_uaj!="") {$texte_maj_sconet.=" ($xml_uaj)";}
						if($xml_horodatage!="") {$texte_maj_sconet.=" du $xml_horodatage";}
						$texte_maj_sconet.="</p>";
						echo $texte_maj_sconet;
						//enregistre_log_maj_sconet($texte_maj_sconet);

						echo "<p>Analyse du fichier pour extraire les informations de la section PERSONNES...<br />\n";

						$personnes=array();

						$tab_champs_personne=array("NOM",
						"NOM_USAGE",
						"NOM_DE_FAMILLE",
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
							//echo("<p><b>Personne</b><br />");

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

						$nb_err=0;
						$stat=0;
						$i=0;
						$nb_utilisateurs_responsables_restaures=0;
						while($i<count($personnes)){

							// Pour tenir compte de la modif Sconet de l'été 2016
							if(isset($personnes[$i]["nom_usage"])) {
								$personnes[$i]["nom"]=$personnes[$i]["nom_usage"];
							}
							elseif(isset($personnes[$i]["nom_de_famille"])) {
								$personnes[$i]["nom"]=$personnes[$i]["nom_de_famille"];
							}

							//$sql="INSERT INTO temp_resp_pers_import SET ";
							$sql="INSERT INTO resp_pers SET ";
							$sql.="pers_id='".$personnes[$i]["personne_id"]."', ";
							$sql.="nom='".mysqli_real_escape_string($GLOBALS["mysqli"], $personnes[$i]["nom"])."', ";
							$sql.="prenom='".mysqli_real_escape_string($GLOBALS["mysqli"], $personnes[$i]["prenom"])."', ";
							if(isset($personnes[$i]["lc_civilite"])){
								$sql.="civilite='".casse_mot($personnes[$i]["lc_civilite"],'majf2')."', ";
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
								$sql.="mel='".mysqli_real_escape_string($GLOBALS["mysqli"], $personnes[$i]["mel"])."', ";
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
							$res_insert=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$res_insert){
								echo "<span style='color:red'>Erreur lors de la requête $sql</span><br />\n";
								flush();
								$nb_err++;
							}
							else{

								$sql="SELECT * FROM tempo_utilisateurs WHERE identifiant1='".$personnes[$i]["personne_id"]."' AND statut='responsable';";
								if($debug_resp=='y') {echo "<span style='color:green;'>$sql</span><br />";}
								$res_tmp_u=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($res_tmp_u)>0) {
									$lig_tmp_u=mysqli_fetch_object($res_tmp_u);

									$sql="SELECT statut FROM utilisateurs WHERE login='".$lig_tmp_u->login."';";
									$test_u=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($test_u)>0) {
										$lig_test_u=mysqli_fetch_object($test_u);
										if($lig_test_u->statut!='responsable') {
											echo "<span style='color:red;'>ANOMALIE&nbsp;:</span> Un compte d'utilisateur <b>$lig_test_u->statut</b> existait pour le login <b>$lig_tmp_u->login</b> mis en réserve pour ".$personnes[$i]["nom"]." ".$personnes[$i]["prenom"]."&nbsp;:<br /><span style='color:red;'>$sql</span><br />";
										}
										else {
											echo "<span style='color:red;'>ATTENTION&nbsp;:</span> Un compte d'utilisateur <b>$lig_test_u->statut</b> existe déjà pour le login <b>$lig_tmp_u->login</b>.<br />Est-ce le même que le responsable nouvellement créé&nbsp;???<br />Un Nettoyage des tables serait peut-être bienvenu.<br /><span style='color:red;'>$sql</span><br />";
										}
									}
									else {
										// On vérifie si le login existe déjà:
										$test_unicite = test_unique_login($lig_tmp_u->login, "y");
										if ($test_unicite != 'yes') {
											echo "<span style='color:red;'>ATTENTION&nbsp;:</span> Un compte d'utilisateur existe déjà pour le login <b>$lig_tmp_u->login</b> mis en réserve pour ".$personnes[$i]["nom"]." ".$personnes[$i]["prenom"].".<br />";
										}
										else {
											$sql="INSERT INTO utilisateurs SET login='".$lig_tmp_u->login."', nom='".mysqli_real_escape_string($GLOBALS["mysqli"], $personnes[$i]["nom"])."', prenom='".mysqli_real_escape_string($GLOBALS["mysqli"], $personnes[$i]["prenom"])."', ";
											if(isset($personnes[$i]["lc_civilite"])){
												$sql.="civilite='".casse_mot($personnes[$i]["lc_civilite"],'majf2')."', ";
											}
											$sql.="password='".$lig_tmp_u->password."', salt='".$lig_tmp_u->salt."', email='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig_tmp_u->email)."', statut='responsable', etat='inactif', change_mdp='n', auth_mode='".$lig_tmp_u->auth_mode."';";
											if($debug_resp=='y') {echo "<span style='color:green;'>$sql</span><br />";}
											$insert_u=mysqli_query($GLOBALS["mysqli"], $sql);
											if(!$insert_u) {
												echo "<span style='color:red;'>Erreur</span> lors de la création du compte utilisateur pour ".$personnes[$i]["nom"]." ".$personnes[$i]["prenom"]."&nbsp;:<br /><span style='color:red;'>$sql</span><br />";
											}
											else {
												$nb_utilisateurs_responsables_restaures++;

												$sql="UPDATE resp_pers SET login='".$lig_tmp_u->login."' WHERE pers_id='".$personnes[$i]["personne_id"]."';";
												if($debug_resp=='y') {echo "<span style='color:green;'>$sql</span><br />";}
												$update_rp=mysqli_query($GLOBALS["mysqli"], $sql);
	
												$sql="UPDATE tempo_utilisateurs SET temoin='recree' WHERE identifiant1='".$personnes[$i]["personne_id"]."' AND statut='responsable';";
												if($debug_resp=='y') {echo "<span style='color:green;'>$sql</span><br />";}
												$update_tmp_u=mysqli_query($GLOBALS["mysqli"], $sql);
											}
										}
									}
								}

								$stat++;
							}

							$i++;
						}

						if ($nb_err != 0) {
							echo "<p>Lors de l'enregistrement des données PERSONNES, il y a eu $nb_err erreurs. Essayez de trouvez la cause de l'erreur et recommencez la procédure avant de passer à l'étape suivante.</p>\n";
						} else {
							echo "<p>L'importation des personnes (<em>responsables</em>) dans la base GEPI a été effectuée avec succès (<em>".$stat." enregistrements au total</em>).</p>\n";
						}

						if($nb_utilisateurs_responsables_restaures>0) {
							echo "<p>$nb_utilisateurs_responsables_restaures compte(s) d'utilisateur(s) responsable(s) a(ont) été restauré(s) (<em>avec leur(s) mot(s) de passe</em>), mais ils sont actuellement inactifs.<br />Lorsque vous voudrez rouvrir l'accès responsable, vous devrez activer les comptes responsables dans <a href='../utilisateurs/edit_responsable.php' target='_blank'>Gestion des bases/Comptes utilisateurs/Responsables</a>.</p>\n";
						}

						//echo "<p>$stat enregistrement(s) ont été inséré(s) dans la table 'temp_resp_pers_import'.</p>\n";
						//echo "<p>$stat enregistrement(s) ont été inséré(s) dans la table 'resp_pers'.</p>\n";

						echo "<br /><p align='center'><a href='".$_SERVER['PHP_SELF']."?step=1".add_token_in_url()."'>Suite</a></p>\n";

						require("../lib/footer.inc.php");
						die();
					}
				}
			} // Fin du $step=0
			elseif($step==1) {
				check_token(false);

				$dest_file="../temp/".$tempdir."/responsables.xml";

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

				//$sql="CREATE TABLE IF NOT EXISTS temp_responsables2_import (
				$sql="CREATE TABLE IF NOT EXISTS responsables2 (
						`ele_id` varchar(10) NOT NULL,
						`pers_id` varchar(10) NOT NULL,
						`resp_legal` varchar(1) NOT NULL,
						`pers_contact` varchar(1) NOT NULL
						) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
				$create_table = mysqli_query($GLOBALS["mysqli"], $sql);

				//$sql="TRUNCATE TABLE temp_responsables2_import;";
				$sql="TRUNCATE TABLE responsables2;";
				$vide_table = mysqli_query($GLOBALS["mysqli"], $sql);

				flush();

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
					//echo("<p><b>Personne</b><br />");

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

				$nb_err=0;
				$stat=0;
				$i=0;
				while($i<count($responsables)){
					// VERIFICATION: Il arrive que Sconet contienne des anomalies
					$sql="SELECT 1=1 FROM responsables2 WHERE ";
					$sql.="ele_id='".$responsables[$i]["eleve_id"]."' AND ";
					$sql.="resp_legal='".$responsables[$i]["resp_legal"]."' AND ";
					$sql.="(resp_legal='1' OR resp_legal='2');";
					affiche_debug("$sql<br />\n");
					$res_test=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_test)==0){
						//$sql="INSERT INTO temp_responsables2_import SET ";
						$sql="INSERT INTO responsables2 SET ";
						$sql.="ele_id='".$responsables[$i]["eleve_id"]."', ";
						$sql.="pers_id='".$responsables[$i]["personne_id"]."', ";
						$sql.="resp_legal='".$responsables[$i]["resp_legal"]."', ";
						$sql.="pers_contact='".$responsables[$i]["pers_contact"]."';";
						affiche_debug("$sql<br />\n");
						$res_insert=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$res_insert){
							echo "<span style='color:red'>Erreur lors de la requête $sql</span><br />\n";
							flush();
							$nb_err++;
						}
						else{
							$stat++;
						}
					}
					else {
						$sql="SELECT nom, prenom FROM eleves WHERE ele_id='".$responsables[$i]["eleve_id"]."';";
						affiche_debug("$sql<br />\n");
						$res_ele_anomalie=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res_ele_anomalie)>0){
							$lig_ele_anomalie=mysqli_fetch_object($res_ele_anomalie);
							echo "<p><b style='color:red;'>Anomalie sconet:</b> Plusieurs responsables légaux n°<b>".$responsables[$i]["resp_legal"]."</b> sont déclarés pour l'élève ".$lig_ele_anomalie->prenom." ".$lig_ele_anomalie->nom."<br />Seule la première responsabilité a été enregistrée.<br />Vous devriez faire le ménage dans Sconet et faire une mise à jour par la suite.</p>\n";

							$nb_err++;
						}
						else {

							$sql="SELECT ELENOM, ELEPRE, DIVCOD FROM temp_gep_import2 WHERE ELE_ID='".$responsables[$i]["eleve_id"]."';";
							affiche_debug("$sql<br />\n");
							$res_ele_anomalie=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res_ele_anomalie)>0){
								// Si l'élève associé n'est ni dans 'eleves', ni dans 'temp_gep_import2', on ne s'en occupe pas.

								$sql="SELECT civilite,nom,prenom FROM temp_resp_pers_import WHERE pers_id='".$responsables[$i]["personne_id"]."';";
								$res_resp_anomalie=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($res_resp_anomalie)>0){
									$lig_resp_anomalie=mysqli_fetch_object($res_resp_anomalie);

									echo "<p><b style='color:red;'>Anomalie sconet:</b> Plusieurs responsables légaux n°<b>".$responsables[$i]["resp_legal"]."</b> sont déclarés pour l'élève ".$lig_ele_anomalie->ELEPRE." ".$lig_ele_anomalie->ELENOM." (<em>".$lig_ele_anomalie->DIVCOD."</em>).<br />L'un d'eux est: ".$lig_resp_anomalie->civilite." ".$lig_resp_anomalie->nom." ".$lig_resp_anomalie->prenom."</p>\n";
								}
								else {
									echo "<p><b style='color:red;'>Anomalie sconet:</b> Plusieurs responsables légaux n°<b>".$responsables[$i]["resp_legal"]."</b> sont déclarés pour l'élève ".$lig_ele_anomalie->ELEPRE." ".$lig_ele_anomalie->ELENOM." (<em>".$lig_ele_anomalie->DIVCOD."</em>)<br />L'élève n'a semble-t-il pas été ajouté à la table 'eleves'.<br />Par ailleurs, la personne responsable semble inexistante, mais l'association avec l'identifiant de responsable n°".$responsables[$i]["personne_id"]." existe dans le XML fourni???<br />L'anomalie n'est pas grave pour Gepi; par contre il serait bon de corriger dans Sconet.</p>\n";
								}

								$nb_err++;
							}
						}
						//$nb_err++;
					}

					$i++;
				}

				$sql="SELECT r.pers_id,r.ele_id FROM responsables2 r LEFT JOIN eleves e ON e.ele_id=r.ele_id WHERE e.ele_id is NULL;";
				$test=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($test)>0){
					echo "<p>Suppression de responsabilités sans élève.\n";
					flush();
					$cpt_nett=0;
					while($lig_nett=mysqli_fetch_object($test)){
						//if($cpt_nett>0){echo ", ";}
						//echo "<a href='modify_resp.php?pers_id=$lig_nett->pers_id' target='_blank'>".$lig_nett->pers_id."</a>";
						$sql="DELETE FROM responsables2 WHERE pers_id='$lig_nett->pers_id' AND ele_id='$lig_nett->ele_id';";
						$nettoyage=mysqli_query($GLOBALS["mysqli"], $sql);
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
					echo "<p>L'importation des relations eleves/responsables dans la base GEPI a été effectuée avec succès (<em>".$stat." enregistrements au total</em>).</p>\n";
				}

				//echo "<p>$stat enregistrement(s) ont été inséré(s) dans la table 'temp_responsables2_import'.</p>\n";

				echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=2".add_token_in_url()."'>Suite</a></p>\n";

				require("../lib/footer.inc.php");
				die();
			} // Fin du $step=1
			elseif($step==2){
				check_token(false);
				$dest_file="../temp/".$tempdir."/responsables.xml";

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

				$sql="CREATE TABLE IF NOT EXISTS resp_adr (
						`adr_id` varchar(10) NOT NULL,
						`adr1` varchar(100) NOT NULL,
						`adr2` varchar(100) NOT NULL,
						`adr3` varchar(100) NOT NULL,
						`adr4` varchar(100) NOT NULL,
						`cp` varchar(6) NOT NULL,
						`pays` varchar(50) NOT NULL,
						`commune` varchar(50) NOT NULL,
						PRIMARY KEY  (`adr_id`)
						) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
				$create_table = mysqli_query($GLOBALS["mysqli"], $sql);

				//$sql="TRUNCATE TABLE temp_resp_adr_import;";
				$sql="TRUNCATE TABLE resp_adr;";
				$vide_table = mysqli_query($GLOBALS["mysqli"], $sql);

				flush();


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
					//echo("<p><b>Adresse</b><br />");

					$i++;
					$adresses[$i]=array();

					foreach($adresse->attributes() as $key => $value) {
						// <ADRESSE ADRESSE_ID="228114">
						$adresses[$i][my_strtolower($key)]=trim($value);
					}

					foreach($adresse->children() as $key => $value) {
						if(in_array(my_strtoupper($key),$tab_champs_adresse)) {
							//$adresses[$i][my_strtolower($key)]=nettoyer_caracteres_nom(preg_replace('/"/',' ',preg_replace("/'$/","",preg_replace("/^'/"," ",$value))), "an", " .'-", " ");
							$adresses[$i][my_strtolower($key)]=nettoyer_caracteres_nom(preg_replace("/'$/","",preg_replace("/^'/"," ",$value)), "an", " .'-", " ");
						}
					}

					if($debug_import=='y') {
						echo "<pre style='color:green;'><b>Tableau \$adresses[$i]&nbsp;:</b>";
						print_r($adresses[$i]);
						echo "</pre>";
					}
				}

				$nb_err=0;
				$stat=0;
				$i=0;
				while($i<count($adresses)){
					//$sql="INSERT INTO temp_resp_adr_import SET ";
					$sql="INSERT INTO resp_adr SET ";
					$sql.="adr_id='".$adresses[$i]["adresse_id"]."', ";
					if(isset($adresses[$i]["ligne1_adresse"])){
						$sql.="adr1='".mysqli_real_escape_string($GLOBALS["mysqli"], $adresses[$i]["ligne1_adresse"])."', ";
					}
					if(isset($adresses[$i]["ligne2_adresse"])){
						$sql.="adr2='".mysqli_real_escape_string($GLOBALS["mysqli"], $adresses[$i]["ligne2_adresse"])."', ";
					}
					if(isset($adresses[$i]["ligne3_adresse"])){
						$sql.="adr3='".mysqli_real_escape_string($GLOBALS["mysqli"], $adresses[$i]["ligne3_adresse"])."', ";
					}
					if(isset($adresses[$i]["ligne4_adresse"])){
						$sql.="adr4='".mysqli_real_escape_string($GLOBALS["mysqli"], $adresses[$i]["ligne4_adresse"])."', ";
					}
					if(isset($adresses[$i]["code_postal"])){
						$sql.="cp='".$adresses[$i]["code_postal"]."', ";
					}
					if(isset($adresses[$i]["ll_pays"])){
						$sql.="pays='".mysqli_real_escape_string($GLOBALS["mysqli"], $adresses[$i]["ll_pays"])."', ";
					}
					if(isset($adresses[$i]["libelle_postal"])){
						$sql.="commune='".mysqli_real_escape_string($GLOBALS["mysqli"], $adresses[$i]["libelle_postal"])."', ";
					} elseif(isset($adresses[$i]["commune_etrangere"])) {
						$sql.="commune='".mysqli_real_escape_string($GLOBALS["mysqli"], $adresses[$i]["commune_etrangere"])."', ";
					}
					$sql=mb_substr($sql,0,mb_strlen($sql)-2);
					$sql.=";";
					affiche_debug("$sql<br />\n");
					$res_insert=mysqli_query($GLOBALS["mysqli"], $sql);
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

				if ($nb_err != 0) {
					echo "<p>Lors de l'enregistrement des données ADRESSES des responsables, il y a eu $nb_err erreurs. Essayez de trouvez la cause de l'erreur et recommencez la procédure avant de passer à l'étape suivante.</p>\n";
				} else {
					echo "<p>L'importation des adresses de responsables dans la base GEPI a été effectuée avec succès (<em>".$stat." enregistrements au total</em>).</p>\n";
				}
				//echo "<p>$stat enregistrement(s) ont été mis à jour dans la table 'temp_resp_adr_import'.</p>\n";

				echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=3".add_token_in_url()."'>Suite</a></p>\n";

				require("../lib/footer.inc.php");
				die();
			}
			else{
				check_token(false);
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

				echo "<center><p><a href='matieres.php'>Procéder à la troisième phase</a>.</p></center>\n";

				require("../lib/footer.inc.php");
				die();
			}
		}
	}

	require("../lib/footer.inc.php");
?>
