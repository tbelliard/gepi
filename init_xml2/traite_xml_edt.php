<?php
	@set_time_limit(0);
	/*
	*
	* Copyright 2001, 2015 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

	$sql="SELECT 1=1 FROM droits WHERE id='/init_xml2/traite_xml_edt.php';";
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)==0) {
	$sql="INSERT INTO droits SET id='/init_xml2/traite_xml_edt.php',
	administrateur='V',
	professeur='F',
	cpe='F',
	scolarite='F',
	eleve='F',
	responsable='F',
	secours='F',
	autre='F',
	description='Import des enseignements via un Export XML EDT',
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

	$_SESSION['init_xml_groupes']="xml_edt";

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

	/*
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
	function enregistre_corresp_EDT_classe_matiere_GEPI_id_groupe($id_groupe, $nom_groupe_edt, $mat_code_edt) {
		$sql="SELECT * FROM edt_corresp2 WHERE id_groupe='".$id_groupe."' AND nom_groupe_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $nom_groupe_edt)."';";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			$sql="INSERT INTO edt_corresp2 SET id_groupe='".$id_groupe."', nom_groupe_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $nom_groupe_edt)."', mat_code_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $mat_code_edt)."';;";
			$insert=mysqli_query($GLOBALS["mysqli"], $sql);
		}
	}
	*/

	//**************** EN-TETE *****************
	$titre_page = "Outil d'initialisation de l'année : Importation XML EDT";
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
		echo "<h2>Suppression du XML</h2>\n";
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

		echo "<p>Si le XML EDT est présent, il sera supprimé...</p>\n";
		$tabfich=array("export_edt.xml");

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
		echo "<center><h3 class='gepi'>Première phase de l'import XML EDT</h3></center>\n";
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
		echo " | <a href='".$_SERVER['PHP_SELF']."?nettoyage=oui".add_token_in_url()."'>Suppression d'un XML existant</a>";
		echo "</p>\n";
		//echo "</div>\n";

		//if(!isset($_POST['is_posted'])) {
		if(!isset($step)) {
			echo "<p class='bold'>Upload du fichier d'export EXP_COURS.xml d'EDT.</p>\n";

			echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
			echo "<fieldset class='fieldset_opacite50'>\n";
			echo "<p>Veuillez fournir le fichier d'export EXP_COURS.xml&nbsp;:<br />\n";
			echo "<input type=\"file\" size=\"65\" name=\"xml_file\" style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\"); padding:5px; margin:5px;' /><br />\n";
			echo "<input type='hidden' name='step' value='0' />\n";
			echo "<input type='hidden' name='is_posted' value='yes' />\n";
			echo add_token_field();
			echo "<p><input type='submit' value='Valider' /></p>\n";
			echo "</fieldset>\n";
			echo "</form>\n";

			echo "<p><i>Remarques</i>&nbsp;:</p>\n";
			echo "<ul>\n";
			echo "<li>Dans EDT, la démarche est <span style='color:red'>A PRECISER</span></li>\n";
			echo "</ul>\n";
		}
		else {
			$post_max_size=ini_get('post_max_size');
			$upload_max_filesize=ini_get('upload_max_filesize');
			$max_execution_time=ini_get('max_execution_time');
			$memory_limit=ini_get('memory_limit');

			if($step==0) {
				$xml_file = isset($_FILES["xml_file"]) ? $_FILES["xml_file"] : NULL;

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
				else {
					if(!file_exists($xml_file['tmp_name'])) {
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
					$dest_file="../temp/".$tempdir."/export_edt.xml";
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



						//echo "<p>Veuillez maintenant compléter les correspondances utiles entre EDT et GEPI&nbsp;:</p>\n";

						$sql="TRUNCATE TABLE edt_lignes;";
						$vide_table = mysqli_query($GLOBALS["mysqli"], $sql);

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

						echo "<p><a href='".$_SERVER['PHP_SELF']."?action=rapprochements&step=1".add_token_in_url()."'>Effectuer les rapprochements</a></p>";

					}
				}
			}
			elseif($step==1) {


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
					<table class='boireaus boireaus_alt'>";
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
		Les cases ont été pré-cochées/détectées en recherchant la chaine correspondant au nom de classe dans le nom de groupe.<br />
		Prenez le temps de compléter/corriger si nécessaire.<br />
		Ou <a href='#' onclick=\"import_edt_decocher_groupes();return false;\">Tout décocher</a> si vous préférez.<br />
		<!--
		(<em>En cas de doute sur les classes associées, commencer par effectuer l'association des matières en décochant tout dans cette section... et valider en bas de page.<br />
		Revenez ensuite aux rapprochements.<br />
		Les associations non encore effectuées seront re-proposées, mais les matières reconnues permettront d'identifier plus facilement les classes en cliquant sur les icones <img src='../images/icons/chercher.png' class='icone16' alt='Chercher' /></em>).
		--></p>
		<table class='boireaus boireaus_alt'>";

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
					<!--div style='float:right; width:16px;'>
						<a href='#' onclick=\"import_edt_chercher_groupe(".$ligne[$loop]['id'].");return false;\"><img src='../images/icons/chercher.png' class='icone16' alt='Chercher' /></a-->
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
			<input type='hidden' name='step' value='2' />
			<input type='hidden' name='is_posted' value='y' />
			<input type='submit' id='input_submit' value='Valider' />
		</p>

		".js_checkbox_change_style('checkbox_change', 'texte_', "y")."
	</fieldset>
</form>

<!--p style='color:red'>Dans le tableau des rapprochements de groupe, utiliser les infos matière et prof associés à l'enregistrement de edt_lignes pour afficher une aide au choix des classes (liste des groupes de la matière,...)</p-->

<p style='color:red'>A FAIRE : Pouvoir afficher à titre de contrôle... et pouvoir supprimer des associations enregistrées.</p>";

			}
			elseif($step==2) {

				if(!isset($_POST['is_posted'])) {
					echo "<p style='color:red'>ERREUR&nbsp;: Une partie des variables n'as pas été POSTée.<br />Vous avez probablement un module PHP qui limite le nombre de variables transmises (<i>suhosin?</i>)</p>\n";
					require("../lib/footer.inc.php");
					die();
				}

				//debug_var();

				// matiere
				$corresp_matiere_a_enregistrer=isset($_POST['corresp_matiere_a_enregistrer']) ? $_POST['corresp_matiere_a_enregistrer'] : NULL;
				if(isset($corresp_matiere_a_enregistrer)) {
					$nb_reg=0;
					$nb_del=0;
					foreach($corresp_matiere_a_enregistrer as $id_ligne => $nom_gepi) {
						$sql="SELECT * FROM edt_lignes WHERE id='$id_ligne';";
						$res=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res)>0) {
							$lig=mysqli_fetch_object($res);

							if($nom_gepi=="") {
								$sql="DELETE FROM edt_corresp WHERE champ='matiere' AND nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->mat_code)."';";
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
					//echo "<p>1</p>";
					$nb_reg=0;
					$nb_del=0;
					foreach($corresp_salle_a_enregistrer as $id_ligne => $nom_gepi) {
						$sql="SELECT * FROM edt_lignes WHERE id='$id_ligne';";
						//echo "$sql<br />";
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
						if(isset($_POST['corresp_groupe_a_enregistrer_'.$lig->id])) {
							$current_ligne_grp=$_POST['corresp_groupe_a_enregistrer_'.$lig->id];

							$chaine_classes="|";
							for($loop=0;$loop<count($current_ligne_grp);$loop++) {
								$chaine_classes.=$current_ligne_grp[$loop]."|";
							}

							$sql="SELECT * FROM edt_corresp WHERE champ='groupe' AND nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->classe)."';";
							$res2=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res2)>0) {
								$lig2=mysqli_fetch_object($res2);
								echo "<span style='color:red'>$lig->classe était préalablement associée à $lig2->nom_gepi</span><br />";

								$sql="UPDATE edt_corresp SET champ='groupe', nom_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig->classe)."', nom_gepi='$chaine_classes' WHERE id='$lig2->id';";
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


				/*
				$classe_udt=isset($_POST['classe_udt']) ? $_POST['classe_udt'] : array();
				$classe=isset($_POST['classe']) ? $_POST['classe'] : array();
				//$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : array();

				$matiere_udt=isset($_POST['matiere_udt']) ? $_POST['matiere_udt'] : array();
				$matiere=isset($_POST['matiere']) ? $_POST['matiere'] : array();

				$prof_udt=isset($_POST['prof_udt']) ? $_POST['prof_udt'] : array();
				$login_prof=isset($_POST['login_prof']) ? $_POST['login_prof'] : array();
				*/

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


				$enseignements_deja_traites=array();
				$sql="SELECT * FROM edt_lignes ORDER BY numero;";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res)==0) {
					echo "<p>Aucun enregistrement n'a été trouvé???</p>";
					require("../lib/footer.inc.php");
					die();
				}

				while($tab=mysqli_fetch_assoc($res)) {
/*
<Cours numero="1">
<NUMERO>1</NUMERO>
<DUREE>1h00</DUREE>
<FREQUENCE>H</FREQUENCE>
<MAT_CODE>AGL1</MAT_CODE>
<MAT_LIBELLE>ANGLAIS LV1</MAT_LIBELLE>
<PROF_NOM>COURSIER-FRIMONT</PROF_NOM>
<PROF_PRENOM>ARIANE</PROF_PRENOM>
<CLASSE>6B</CLASSE>
<SALLE>24</SALLE>
<ALTERNANCE>H</ALTERNANCE>
<MODALITE>CG</MODALITE>
<CO-ENS.>N</CO-ENS.>
<POND.>1</POND.>
<JOUR>lundi</JOUR>
<H.DEBUT>08h00</H.DEBUT>
<EFFECTIF>21</EFFECTIF>
</Cours>
*/

					$current_nom_regroupement_edt=preg_replace("/\[/", "", preg_replace("/\]/", "", $tab['classe']));

					$edt_cours_id_groupe="";
					$edt_cours_id_salle="";
					$edt_cours_jour_semaine="";
					$edt_cours_id_definie_periode="";
					$edt_cours_duree="";
					$edt_cours_heuredeb_dec="";
					$edt_cours_id_semaine="";
					$edt_cours_login_prof="";

					if($tab['classe']=='') {
						echo "<p style='color:red; margin-top:1em;'>Le cours n°".$tab['numero']." n'est associé à aucune classe dans EDT.<br />Il se peut qu'il s'agisse de l'emploi du temps d'un(e) surveillant(e) en permanence,...<br />Ce cas n'est pas géré.</p>";
						echo "<pre style='color:red'>";
						print_r($tab);
						echo "</pre>";
					}
					else {
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
						$groupes=get_corresp_edt("groupe", $tab['classe']);
						$salle=get_corresp_edt("salle", $tab['salle']);
						$prof=get_corresp_edt("prof", $tab['prof_nom']." ".$tab['prof_prenom']);

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

						// Lors de l'initialisation, il n'y a pas encore de groupe enregistré... ??
						// Si... si 
						$chaine_classes="";
						$groupes_aff="";
						$tmp_nom_groupe="";
						if(preg_match("/^\[.*\]$/", $tab['classe'])) {
							$tmp_nom_groupe=preg_replace("/^\[/", "", preg_replace("/\]$/", "", $tab['classe']));
							$groupes_aff=$tmp_nom_groupe;
						}
						$tmp_tab=explode("|", $groupes);
						for($loop=0;$loop<count($tmp_tab);$loop++) {
							if($tmp_tab[$loop]!="") {
								if(!isset($tab_classe[$tmp_tab[$loop]])) {
									$tmp_current_classe=get_nom_classe($tmp_tab[$loop]);
									$tab_classe[$tmp_tab[$loop]]=$tmp_current_classe;
									if($chaine_classes!="") {
										$chaine_classes.="_";
									}
									$chaine_classes.=$tmp_current_classe;
								}
								if($groupes_aff!="") {$groupes_aff.=", ";}
								$groupes_aff.=$tab_classe[$tmp_tab[$loop]];
							}
						}
						echo "<p style='margin-top:1em;'>Cours n°".$tab['numero']."<br />";
						echo "matiere=$matiere<br />";
						echo "classe=$classe<br />";
						echo "classe_aff=$classe_aff<br />";
						echo "groupes=$groupes<br />";
						echo "groupes_aff=$groupes_aff<br />";
						echo "prof=$prof<br />";

						if(($matiere!="")&&($prof!="")) {

							$edt_cours_login_prof=$prof;
							$prof_aff="";
							if($prof!="") {
								if(!isset($tab_prof[$prof])) {
									$tab_prof[$prof]=civ_nom_prenom($prof);
								}
								$prof_aff=$tab_prof[$prof];
							}
							echo "prof_aff=$prof_aff<br />";

							$reg_matiere=$matiere;
							$matiere_nom_complet=get_valeur_champ('matieres', "matiere='$matiere'", "nom_complet");
							$matiere_categorie_id=get_valeur_champ('matieres', "matiere='$matiere'", "categorie_id");

							if($matiere_nom_complet=="") {
								echo "<span style='color:red'>Matière non trouvée dans la table 'matieres'... on n'enregistre pas.</span><br />";
							}
							else {
								$reg_nom_groupe=$matiere;
								$reg_nom_complet=$matiere_nom_complet;
								$reg_categorie=$matiere_categorie_id;

								$chaine_id_classe="";
								$reg_clazz=array();
								if($classe!="") {
									$reg_clazz[]=$classe;
									$chaine_id_classe=$classe;
								}
								else {
									// C'est un regroupement (en principe).
									$temp=explode("|", trim($groupes));
									sort($temp);
									for($loop=0;$loop<count($temp);$loop++) {
										if($temp[$loop]!="") {
											$reg_clazz[]=$temp[$loop];
											$chaine_id_classe.=",".$temp[$loop];
										}
									}
									//$tmp_nom_groupe=preg_replace("/^\[/", "", preg_replace("/\]$/", "", $tab['classe']));
									if($tmp_nom_groupe!="") {
										if(!preg_match("/$matiere/i", $tmp_nom_groupe)) {
											$reg_nom_groupe=$matiere."_".$tmp_nom_groupe;
										}
										else {
											$reg_nom_groupe=$tmp_nom_groupe;
										}

										$reg_nom_complet=$matiere_nom_complet." (".$tmp_nom_groupe.")";
									}

									/*
									if($chaine_classes!="") {
										// Plusieurs classes
										$reg_nom_complet=$matiere_nom_complet."_".$chaine_classes;
									}
									*/
								}

								if(in_array($reg_nom_groupe."|".$reg_nom_complet."|".$reg_matiere."|".$chaine_id_classe, $enseignements_deja_traites)) {
									echo "<span style='color:orange'>Le cours n°".$tab['numero']." correspond à un enseignement déjà traité.</span><br />\n";
								}
								else {
									$create=create_group($reg_nom_groupe, $reg_nom_complet, $reg_matiere, $reg_clazz, $reg_categorie);
									if(!$create) {
										echo "<span style='color:red'>Erreur lors de la création du groupe.</span><br />";
									}
									else {
										$enseignements_deja_traites[]=$reg_nom_groupe."|".$reg_nom_complet."|".$reg_matiere."|".$chaine_id_classe;


										$sql="INSERT INTO edt_corresp2 SET id_groupe='$create', mat_code_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $tab['mat_code'])."', nom_groupe_edt='".mysqli_real_escape_string($GLOBALS["mysqli"], $tab['classe'])."';";
										$insert=mysqli_query($GLOBALS["mysqli"], $sql);
										if($insert) {
											echo "Enregistrement de l'association groupe dans edt_corresp2.<br />";
										}
										else {
											echo "<span style='color:red'>Erreur lors de l'enregistrement de l'association groupe dans edt_corresp2.</span><br />";
										}




										$reg_professeurs=array();
										$reg_professeurs[]=$prof;
										// Et si il y a plusieurs profs associés? ça se présente comment?

										$sql="SELECT 1=1 FROM j_professeurs_matieres WHERE id_professeur='$prof' AND id_matiere='$matiere';";
										$test=mysqli_query($GLOBALS["mysqli"], $sql);
										if(mysqli_num_rows($test)==0) {

											$sql="SELECT ordre_matieres FROM j_professeurs_matieres WHERE id_professeur='$prof' ORDER BY ordre_matieres DESC LIMIT 1;";
											$res_max_ordre_matiere=mysqli_query($GLOBALS["mysqli"], $sql);
											if(mysqli_num_rows($res_max_ordre_matiere)==0) {
												$ordre_mat=1;
											}
											else {
												$lig_ordre_mat=mysqli_fetch_object($res_max_ordre_matiere);
												$ordre_mat=$lig_ordre_mat->ordre_matieres+1;
											}

											$sql="INSERT INTO j_professeurs_matieres SET id_professeur='$prof', id_matiere='$matiere', ordre_matieres='$ordre_mat';";
											$insert=mysqli_query($GLOBALS["mysqli"], $sql);
											if(!$insert) {echo "<br /><b>ERREUR</b> lors de l'association du professeur $prof avec la matière $matiere<br />\n";}
											else {echo " (<i>association du professeur avec la matière $matiere</i>)<br />";}
										}



										$tab_eleves_groupe_toutes_periodes=array();
										$reg_eleves=array();
										$current_group=get_group($create);
										foreach ($current_group["periodes"] as $period) {
											$reg_eleves[$period['num_periode']]=array();

											$cpt_clas=0;
											$sql="";
											foreach($reg_clazz as $tmp_id_classe){
												if($cpt_clas>0) {$sql.=" UNION ";}
												$sql.="(SELECT jec.login FROM j_eleves_classes jec, eleves e, classes c WHERE id_classe='$tmp_id_classe' AND periode='".$period['num_periode']."' AND jec.login=e.login AND jec.id_classe=c.id ORDER BY e.nom, e.prenom)";
												$cpt_clas++;
											}
											//$sql.=" ORDER BY c.classe, e.nom, e.prenom;";
											//echo "$sql<br />";
											$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
											$nb_ele=mysqli_num_rows($res_ele);
											if($nb_ele>0){
												$cpt_ele=1;
												while($lig_ele=mysqli_fetch_object($res_ele)) {
													$reg_eleves[$period['num_periode']][]=$lig_ele->login;
													//echo $lig_ele->login."<br />";

													if(!in_array($lig_ele->login, $tab_eleves_groupe_toutes_periodes)) {
														$tab_eleves_groupe_toutes_periodes[]=$lig_ele->login;
													}

													$cpt_ele++;
												}
											}
										}


										$code_modalite_elect_eleves=array();
										for($loop=0;$loop<count($tab_eleves_groupe_toutes_periodes);$loop++) {
											//$sql="SELECT code_modalite_elect FROM sconet_ele_options seo, eleves e WHERE seo.ele_id=e.ele_id AND e.login='".$tab_eleves_groupe_toutes_periodes[$loop]."' AND seo.code_matiere='".$current_group["matiere"]["code_matiere"]."';";
											//$sql="SELECT code_modalite_elect FROM sconet_ele_options seo, eleves e, matieres m WHERE seo.ele_id=e.ele_id AND e.login='".$tab_eleves_groupe_toutes_periodes[$loop]."' AND seo.code_matiere=m.code_matiere AND m.matiere='".$mat."';";
											$sql="SELECT code_modalite_elect FROM sconet_ele_options seo, eleves e, matieres m WHERE seo.ele_id=e.ele_id AND e.login='".$tab_eleves_groupe_toutes_periodes[$loop]."' AND seo.code_matiere=m.code_matiere AND m.matiere='".$matiere."';";
											$res_cme=mysqli_query($GLOBALS["mysqli"], $sql);
											if(mysqli_num_rows($res_cme)>0) {
												$lig_cme=mysqli_fetch_object($res_cme);
												$code_modalite_elect_eleves[$lig_cme->code_modalite_elect]["eleves"][]=$tab_eleves_groupe_toutes_periodes[$loop];
											}
										}


										if ((count($reg_professeurs) == 0)&&(count($reg_eleves) == 0)) {
											echo "<span style='color:red'>Groupe sans élève ni professeur.</span><br />";
										} else {
											$update_grp=update_group($create, $reg_nom_groupe, $reg_nom_complet, $reg_matiere, $reg_clazz, $reg_professeurs, $reg_eleves, $code_modalite_elect_eleves);
											if(!$update_grp) {
												echo "<span style='color:red'>Erreur lors de l'inscription des professeurs et élèves dans le groupe.</span><br />";
											}
										}

									}
								}
							}
						}
					}
				}


				/*
				echo "<p style='color:red'>On ne va pas plus loin pour le moment.</p>";
				require("../lib/footer.inc.php");
				die();
				//++++++++++++++++++++++++++++++++++++++++++++++++++++++
				*/

				echo "<center><p><a href='init_options.php?a=a".add_token_in_url()."'>Prise en compte des options des élèves</a></p></center>\n";

				//echo "<center><p><a href='init_pp.php?a=a".add_token_in_url()."'>Import des professeurs principaux</a><br />Il est probable que cette information n'était pas dans le fichier de STS, l'import des professeurs principaux risque de ne rien donner... mais qui ne tente rien...</p></center>\n";

			}
		}
	}

	require("../lib/footer.inc.php");
?>
