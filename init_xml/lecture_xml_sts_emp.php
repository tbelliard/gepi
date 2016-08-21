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

if(isset($_GET['ad_retour'])){
	$_SESSION['ad_retour']=$_GET['ad_retour'];
}

//**************** EN-TETE *****************
$titre_page = "XML de STS: Génération de CSV";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

echo "<p class=bold><a href='";
if(isset($_SESSION['ad_retour'])){
	echo $_SESSION['ad_retour'];
}
else{
	echo "index.php";
}
echo "'> <img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

require_once("../init_xml2/init_xml_lib.php");

//================================================
// Fonction de génération de mot de passe récupérée sur TotallyPHP
// Aucune mention de licence pour ce script...

/*
* The letter l (lowercase L) and the number 1
* have been removed, as they can be mistaken
* for each other.
*/

function createRandomPassword() {
	$chars = "abcdefghijkmnopqrstuvwxyz023456789";
	srand((double)microtime()*1000000);
	$i = 0;
	$pass = '' ;

	//while ($i <= 7) {
	while ($i <= 5) {
		$num = rand() % 33;
		$tmp = mb_substr($chars, $num, 1);
		$pass = $pass . $tmp;
		$i++;
	}

	return $pass;
}
//================================================

$tempdir=get_user_temp_directory();
if(!$tempdir){
	echo "<p style='color:red'>Il semble que le dossier temporaire de l'utilisateur ".$_SESSION['login']." ne soit pas défini!?</p>\n";
	// Il ne faut pas aller plus loin...
	// SITUATION A GERER
}

?>
	<div class="content">
		<?php

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


			if(isset($_GET['nettoyage'])) {
				check_token(false);

				//echo "<h1 align='center'>Suppression des CSV</h1>\n";
				echo "<h2 align='center'>Suppression des CSV</h2>\n";
				echo "<p>Si des fichiers CSV existent, ils seront supprimés...</p>\n";
				$tabfich=array("f_wind.csv","f_men.csv","f_gpd.csv","f_div.csv","f_tmt.csv","profs.html");
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

				require("../lib/footer.inc.php");
				die();
			}


			//echo "<h1 align='center'>Lecture du XML Emploi du temps de Sts-web et génération de CSV</h1>\n";
			echo "<h2 align='center'>Lecture du XML Emploi du temps de Sts-web et génération de CSV</h2>\n";
			if(!isset($_POST['is_posted'])) {
				//echo "<p>Cette page permet de remplir des tableaux PHP avec les informations professeurs, matières,... mais pas encore les liaisons profs/matières/classes.<br />Elle génère des fichiers CSV permettant un import des comptes profs pour GEPI.</p>\n";
				echo "<p>Cette page permet de remplir des tables temporaires avec les informations professeurs, matières,...<br />Elle génère des fichiers CSV permettant un import des comptes profs pour GEPI.</p>\n";
				echo "<p>Il faut lui fournir un Export XML réalisé depuis l'application STS-web.<br />Demandez gentiment à votre secrétaire d'accéder à STS-web et d'effectuer 'Mise à jour/Exports/Emplois du temps'.</p>\n";
				echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
				echo add_token_field();
				echo "<p>Veuillez fournir le fichier XML: \n";
				echo "<p><input type=\"file\" size=\"80\" name=\"xml_file\" />\n";
				echo "<input type='hidden' name='is_posted' value='yes' />\n";
				echo "</p>\n";
				echo "<p><input type=\"radio\" name=\"mdp\" id='mdp_alea' value=\"alea\" checked /> <label for='mdp_alea' style='cursor: pointer;'>Générer un mot de passe aléatoire pour chaque professeur.</label><br />\n";
				echo "<input type=\"radio\" name=\"mdp\" id='mdp_date' value=\"date\" /> <label for='mdp_date' style='cursor: pointer;'>Utiliser plutôt la date de naissance au format 'aaaammjj' comme mot de passe initial (<i>il devra être modifié au premier login</i>).</label></p>\n";
				echo "<input type='hidden' name='is_posted' value='yes' />\n";
				//echo "</p>\n";
				echo "<p><input type='submit' value='Valider' /></p>\n";
				echo "</form>\n";
			}
			else {
				check_token(false);

				$post_max_size=ini_get('post_max_size');
				$upload_max_filesize=ini_get('upload_max_filesize');
				$max_execution_time=ini_get('max_execution_time');
				$memory_limit=ini_get('memory_limit');

				$temoin_au_moins_un_prof_princ="";



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

					echo "<p>La copie du fichier vers le dossier temporaire a réussi.</p>\n";

					echo "<p>Aller à la <a href='#gepi'>section GEPI</a><br />Si vous patientez, des liens directs seront proposés (<i>dans un cadre jaune</i>) pour télécharger les fichiers.<br />Si la page finit son chargement sans générer de cadre jaune, il se peut que la configuration de PHP donne un temps de traitement trop court";

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

					$temoin_au_moins_une_matiere="";
					$temoin_au_moins_un_prof="";

					echo "<h2>Matières</h2>\n";
					echo "<blockquote>\n";
					echo "<h3>Analyse du fichier pour extraire les matières...</h3>\n";
					echo "<blockquote>\n";


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
							//$matiere[$i][my_strtolower($key)]=trim(traite_utf8($value));
							$matiere[$i][my_strtolower($key)]=trim($value);
						}
			
						// Champs de la matière
						foreach($objet_matiere->children() as $key => $value) {
							if(in_array(my_strtoupper($key),$tab_champs_matiere)) {
								if(my_strtoupper($key)=='CODE_GESTION') {
									$matiere[$i][my_strtolower($key)]=trim(preg_replace("/[^a-zA-Z0-9&_. -]/","",nettoyer_caracteres_nom(remplace_accents($value),"an", "&_. -", "")));
								}
								elseif(my_strtoupper($key)=='LIBELLE_COURT') {
									$matiere[$i][my_strtolower($key)]=trim(preg_replace("/'/"," ",preg_replace('/"/',' ',nettoyer_caracteres_nom($value,"an", "&_. -", ""))));
								}
								else {
									$matiere[$i][my_strtolower($key)]=trim(preg_replace("/'/"," ",preg_replace('/"/',' ',nettoyer_caracteres_nom($value,"an", "&_. -", ""))));
								}
							}
						}

						if($debug_import=='y') {
							echo "<pre style='color:green;'><b>Tableau \$adresses[$i]&nbsp;:</b>";
							print_r($adresses[$i]);
							echo "</pre>";
						}
			
						$i++;
					}

					echo "<p>Terminé.</p>\n";
					echo "</blockquote>\n";

					echo "<h3>Affichage des données MATIERES extraites:</h3>\n";
					echo "<blockquote>\n";
					echo "<table border='1'>\n";
					echo "<tr>\n";
					echo "<th style='color: blue;'>&nbsp;</th>\n";
					echo "<th>Code</th>\n";
					echo "<th>Code_gestion</th>\n";
					echo "<th>Libelle_court</th>\n";
					echo "<th>Libelle_long</th>\n";
					echo "<th>Libelle_edition</th>\n";
					echo "</tr>\n";
					$cpt=0;
					while($cpt<count($matiere)){
						echo "<tr>\n";
						echo "<td style='color: blue;'>$cpt</td>\n";
						echo "<td>".$matiere[$cpt]["code"]."</td>\n";
						echo "<td>".$matiere[$cpt]["code_gestion"]."</td>\n";
						echo "<td>".$matiere[$cpt]["libelle_court"]."</td>\n";
						echo "<td>".$matiere[$cpt]["libelle_long"]."</td>\n";
						echo "<td>".$matiere[$cpt]["libelle_edition"]."</td>\n";
						echo "</tr>\n";
						$cpt++;
					}
					echo "</table>\n";
					echo "</blockquote>\n";
					echo "</blockquote>\n";



					echo "<h2>Personnels</h2>\n";
					echo "<blockquote>\n";
					echo "<h3>Analyse du fichier pour extraire les professeurs,...</h3>\n";
					echo "<blockquote>\n";

					$prof=array();
					$i=0;
				
					$tab_champs_personnels=array("NOM_USAGE",
					"NOM_PATRONYMIQUE",
					"PRENOM",
					"SEXE",
					"CIVILITE",
					"DATE_NAISSANCE",
					"GRADE",
					"FONCTION");
				
					$prof=array();
					$i=0;

					$sts_xml=simplexml_load_file($dest_file);
					foreach($sts_xml->DONNEES->INDIVIDUS->children() as $individu) {
						$prof[$i]=array();
				
						//echo "<span style='color:orange'>\$individu->NOM_USAGE=".$individu->NOM_USAGE."</span><br />";
				
						foreach($individu->attributes() as $key => $value) {
							// <INDIVIDU ID="4189" TYPE="epp">
							//$prof[$i][my_strtolower($key)]=trim(traite_utf8($value));
							$prof[$i][my_strtolower($key)]=trim($value);
						}
				
						// Champs de l'individu
						foreach($individu->children() as $key => $value) {
							if(in_array(my_strtoupper($key),$tab_champs_personnels)) {
								if(my_strtoupper($key)=='SEXE') {
									$prof[$i]["sexe"]=trim(preg_replace("/[^1-2]/","",$value));
								}
								elseif(my_strtoupper($key)=='CIVILITE') {
									$prof[$i]["civilite"]=trim(preg_replace("/[^1-3]/","",$value));
								}
								elseif((my_strtoupper($key)=='NOM_USAGE')||
								(my_strtoupper($key)=='NOM_PATRONYMIQUE')||
								(my_strtoupper($key)=='NOM_USAGE')) {
									//$prof[$i][my_strtolower($key)]=trim(preg_replace("/[^A-Za-z -]/","",traite_utf8($value)));
									$prof[$i][my_strtolower($key)]=trim(preg_replace("/[^A-Za-z -]/","",remplace_accents($value)));
								}
								elseif(my_strtoupper($key)=='PRENOM') {
									//$prof[$i][my_strtolower($key)]=trim(preg_replace("/[^A-Za-zÆæ¼½".$liste_caracteres_accentues." -]/","",traite_utf8($value)));
									$prof[$i][my_strtolower($key)]=trim(preg_replace('/"/','',preg_replace("/'/","",nettoyer_caracteres_nom($value,"a"," '_-",""))));
								}
								elseif(my_strtoupper($key)=='DATE_NAISSANCE') {
									//$prof[$i][my_strtolower($key)]=trim(preg_replace("/[^0-9-]/","",traite_utf8($value)));
									$prof[$i][my_strtolower($key)]=trim(preg_replace("/[^0-9-]/","",$value));
								}
								elseif((my_strtoupper($key)=='GRADE')||
									(my_strtoupper($key)=='FONCTION')) {
									//$prof[$i][my_strtolower($key)]=trim(preg_replace('/"/','',traite_utf8($value)));
									$prof[$i][my_strtolower($key)]=trim(preg_replace('/"/','',preg_replace("/'/"," ",$value)));
								}
								else {
									$prof[$i][my_strtolower($key)]=trim($value);
								}
								//echo "\$prof[$i][".strtolower($key)."]=".$prof[$i][strtolower($key)]."<br />";
							}
						}
				
						$prof[$i]["prof_princ"]=array();
						if(isset($individu->PROFS_PRINC)) {
						//if($temoin_prof_princ>0) {
							$j=0;
							foreach($individu->PROFS_PRINC->children() as $prof_princ) {
								//$prof[$i]["prof_princ"]=array();
								foreach($prof_princ->children() as $key => $value) {
									//$prof[$i]["prof_princ"][$j][my_strtolower($key)]=trim(traite_utf8(preg_replace('/"/',"",$value)));
									$prof[$i]["prof_princ"][$j][my_strtolower($key)]=trim(preg_replace('/"/',"",$value));
									$temoin_au_moins_un_prof_princ="oui";
								}
								$j++;
							}
						}
				
						//if($temoin_discipline>0) {
						if(isset($individu->DISCIPLINES)) {
							$j=0;
							foreach($individu->DISCIPLINES->children() as $discipline) {
								foreach($discipline->attributes() as $key => $value) {
									if(my_strtoupper($key)=='CODE') {
										//$prof[$i]["disciplines"][$j]["code"]=trim(traite_utf8(preg_replace('/"/',"",$value)));
										$prof[$i]["disciplines"][$j]["code"]=trim(preg_replace('/"/',"",$value));
										break;
									}
								}
				
								foreach($discipline->children() as $key => $value) {
									//$prof[$i]["disciplines"][$j][my_strtolower($key)]=trim(traite_utf8(preg_replace('/"/',"",$value)));
									$prof[$i]["disciplines"][$j][my_strtolower($key)]=trim(preg_replace('/"/',"",$value));
								}
								$j++;
							}
						}
				
						if($debug_import=='y') {
							echo "<pre style='color:green;'><b>Tableau \$prof[$i]&nbsp;:</b>";
							print_r($prof[$i]);
							echo "</pre>";
						}
				
						$i++;
					}


					$divisions=array();
					$i=0;
					//$sts_xml=simplexml_load_file($dest_file);
					foreach($sts_xml->DONNEES->STRUCTURE->DIVISIONS->children() as $division) {
						$divisions[$i]=array();
				
						foreach($division->attributes() as $key => $value) {
							// $divisions[$i]["code"]
							$divisions[$i][my_strtolower($key)]=trim($value);
							//echo "\$divisions[$i][my_strtolower($key)]=".$divisions[$i][my_strtolower($key)]."<br />";
						}

						$j=0;
						foreach($division->SERVICES->children() as $service) {
							foreach($service->attributes() as $key => $value) {
								//<SERVICE CODE_MATIERE="030201" CODE_MOD_COURS="CG">
								$divisions[$i]["services"][$j][my_strtolower($key)]=trim($value);
								//echo "&nbsp;&nbsp;&nbsp;\$divisions[$i][services][$j][my_strtolower($key)]=".$divisions[$i]["services"][$j][my_strtolower($key)]."<br />";
								//$j++;
							}

							$divisions[$i]["services"][$j]["enseignants"]=array();
							$k=0;
							foreach($service->ENSEIGNANTS->children() as $enseignant) {
								//<ENSEIGNANT ID="20048" TYPE="epp">
								foreach($enseignant->attributes() as $key => $value) {
									$divisions[$i]["services"][$j]["enseignants"][$k][my_strtolower($key)]=trim($value);
									//echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\$divisions[$i][services][$j][enseignants][$k][my_strtolower($key)]=".$divisions[$i]["services"][$j]["enseignants"][$k][my_strtolower($key)]."<br />";
								}
								$k++;
							}
							$j++;
						}
						$i++;
					}



					$groupes=array();
					$i=0;
					$sts_xml=simplexml_load_file($dest_file);
					foreach($sts_xml->DONNEES->STRUCTURE->GROUPES->children() as $groupe) {
						$cpt_prof=0;
						$groupes[$i]=array();
				
						foreach($groupe->attributes() as $key => $value) {
							//<GROUPE CODE="3 AVSPRO">
							// $groupes[$i]["code"]
							$groupes[$i][my_strtolower($key)]=trim($value);
						}

						foreach($groupe->children() as $key => $value) {
							if(my_strtoupper($key)=='LIBELLE_LONG') {
								$groupes[$i]["libelle_long"]=trim($value);
							}
						}

						$j=0;
						foreach($groupe->DIVISIONS_APPARTENANCE->children() as $division_appartenance) {
							foreach($division_appartenance->attributes() as $key => $value) {
								//<DIVISION_APPARTENANCE CODE="6 B">
								$groupes[$i]["divisions"][$j][my_strtolower($key)]=trim($value);
							}
							$j++;
						}

						$j=0;
						foreach($groupe->SERVICES->children() as $service) {
							//<SERVICE CODE_MATIERE="020100" CODE_MOD_COURS="CG">

							//foreach($service->children() as $key => $value) {
							foreach($service->attributes() as $key => $value) {
								if(my_strtoupper($key)=='CODE_MATIERE') {
									$groupes[$i]["code_matiere"]=trim($value);
								}
							}

							$groupes[$i]["services"][$j]["enseignants"]=array();
							$k=0;
							foreach($service->ENSEIGNANTS->children() as $enseignant) {
								//<ENSEIGNANT ID="20048" TYPE="epp">
								foreach($enseignant->attributes() as $key => $value) {
									$groupes[$i]["services"][$j]["enseignants"][$k][my_strtolower($key)]=trim($value);
									if(my_strtoupper($key)=='ID') {
										$groupes[$i]["enseignant"][$cpt_prof][my_strtolower($key)]=trim($value);
										$cpt_prof++;
									}
								}
								$k++;
							}
							$j++;
						}

						$i++;
					}


					//$cpt++;

					echo "<p>Terminé.</p>\n";
					echo "</blockquote>\n";



					echo "<h3>Affichage des données PROFS,... extraites:</h3>\n";
					echo "<blockquote>\n";
					echo "<table border='1'>\n";
					echo "<tr>\n";
					echo "<th style='color: blue;'>&nbsp;</th>\n";
					echo "<th>Id</th>\n";
					echo "<th>Type</th>\n";
					echo "<th>Sexe</th>\n";
					echo "<th>Civilite</th>\n";
					echo "<th>Nom_usage</th>\n";
					echo "<th>Nom_patronymique</th>\n";
					echo "<th>Prenom</th>\n";
					echo "<th>Date_naissance</th>\n";
					echo "<th>Grade</th>\n";
					echo "<th>Fonction</th>\n";
					echo "<th>Disciplines</th>\n";
					echo "</tr>\n";
					$cpt=0;
					while($cpt<count($prof)){
						echo "<tr>\n";
						echo "<td style='color: blue;'>$cpt</td>\n";
						echo "<td>".$prof[$cpt]["id"]."</td>\n";
						echo "<td>".$prof[$cpt]["type"]."</td>\n";
						echo "<td>".$prof[$cpt]["sexe"]."</td>\n";
						echo "<td>".$prof[$cpt]["civilite"]."</td>\n";
						echo "<td>".$prof[$cpt]["nom_usage"]."</td>\n";
						echo "<td>".$prof[$cpt]["nom_patronymique"]."</td>\n";
						echo "<td>".$prof[$cpt]["prenom"]."</td>\n";
						echo "<td>".$prof[$cpt]["date_naissance"]."</td>\n";
						echo "<td>".$prof[$cpt]["grade"]."</td>\n";
						echo "<td>".$prof[$cpt]["fonction"]."</td>\n";

						echo "<td align='center'>\n";

						if($prof[$cpt]["fonction"]=="ENS"){
							echo "<table border='1'>\n";
							echo "<tr>\n";
							echo "<th>Code</th>\n";
							echo "<th>Libelle_court</th>\n";
							echo "<th>Nb_heures</th>\n";
							echo "</tr>\n";
							for($j=0;$j<count($prof[$cpt]["disciplines"]);$j++){
								echo "<tr>\n";
								echo "<td>".$prof[$cpt]["disciplines"][$j]["code"]."</td>\n";
								echo "<td>".$prof[$cpt]["disciplines"][$j]["libelle_court"]."</td>\n";
								echo "<td>".$prof[$cpt]["disciplines"][$j]["nb_heures"]."</td>\n";
								echo "</tr>\n";
							}
							echo "</table>\n";
						}

						echo "</td>\n";
						echo "</tr>\n";
						$cpt++;
					}
					echo "</table>\n";
					echo "</blockquote>\n";

					echo "<p style='color:red;'><b>A faire</b>: un fichier profs pour GEPI...</p>\n";



					// Affichage des infos Enseignements et divisions:
					echo "<a name='divisions'></a><h3>Affichage des divisions</h3>\n";
					echo "<blockquote>\n";
					for($i=0;$i<count($divisions);$i++){
						//echo "<p>\$divisions[$i][\"code\"]=".$divisions[$i]["code"]."<br />\n";
						echo "<h4>Classe de ".$divisions[$i]["code"]."</h4>\n";
						echo "<ul>\n";
						for($j=0;$j<count($divisions[$i]["services"]);$j++){
							//echo "\$divisions[$i][\"services\"][$j][\"code_matiere\"]=".$divisions[$i]["services"][$j]["code_matiere"]."<br />\n";
							echo "<li>\n";
							for($m=0;$m<count($matiere);$m++){
								if($matiere[$m]["code"]==$divisions[$i]["services"][$j]["code_matiere"]){
									//echo "\$matiere[$m][\"code_gestion\"]=".$matiere[$m]["code_gestion"]."<br />\n";
									echo "Matière: ".$matiere[$m]["code_gestion"]."<br />\n";
									$temoin_au_moins_une_matiere="oui";
								}
							}
							echo "<ul>\n";
							for($k=0;$k<count($divisions[$i]["services"][$j]["enseignants"]);$k++){
							//$divisions[$i]["services"][$j]["enseignants"][$k]["id"]
								for($m=0;$m<count($prof);$m++){
									if($prof[$m]["id"]==$divisions[$i]["services"][$j]["enseignants"][$k]["id"]){
										//echo $prof[$m]["nom_usage"]." ".$prof[$m]["prenom"]."|";
										echo "<li>\n";
										echo "Enseignant: ".$prof[$m]["nom_usage"]." ".$prof[$m]["prenom"];
										echo "</li>\n";
										$temoin_au_moins_un_prof="oui";
									}
								}
							}
							echo "</ul>\n";
							//echo "<br />\n";
							echo "</li>\n";
						}
						echo "</ul>\n";
						//echo "</p>\n";
					}
					echo "</blockquote>\n";
					echo "</blockquote>\n";




					echo "<h2>Suppression des CSV existants</h2>\n";
					echo "<blockquote>\n";
					echo "<p>Si des fichiers CSV ont déjà été générés, on va commencer par les supprimer avant d'en générer de nouveaux...</p>\n";
					$tabfich=array("f_wind.csv","f_men.csv","f_gpd.csv","f_div.csv","f_tmt.csv","profs.html");
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
					echo "<p>Terminé.</p>\n";
					echo "</blockquote>\n";



					echo "<a name='gepi'></a>\n";

					echo "<a name='f_wind_gepi'></a><h2>Génération du CSV (F_WIND.CSV) des profs pour GEPI</h2>\n";
					echo "<blockquote>\n";
					$cpt=0;
					$fich=fopen("../backup/$dirname/csv/f_wind.csv","w+");
					$chaine="AINOMU;AIPREN;AICIVI;NUMIND;FONCCO;INDNNI";
					if($fich){
						//fwrite($fich,$chaine."\n");
						fwrite($fich,html_entity_decode($chaine)."\n");
					}
					echo $chaine."<br />\n";

					if($_POST['mdp']=="alea"){
						$fich2=fopen("../backup/$dirname/csv/profs.html","w+");
						fwrite($fich2,"<?php
@set_time_limit(0);

// Initialisations files
require_once('../lib/initialisations.inc.php');

// Resume session
\$resultat_session = \$session_gepi->security_check();
if (\$resultat_session == 'c') {
header('Location: ../utilisateurs/mon_compte.php?change_mdp=yes');
die();
} else if (\$resultat_session == '0') {
header('Location: ../logout.php?auto=1');
die();
};

if (!checkAccess()) {
header('Location: ../logout.php?auto=1');
die();
}
?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN' 'http://www.w3.org/TR/html4/loose.dtd'>
<html>
<head>
	<title>Fichier profs</title>
	<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
	<meta name='author' content='Stephane Boireau, A.S. RUE de Bernay/Pont-Audemer' />
	<link type='text/css' rel='stylesheet' href='../../style.css' />
</head>
<body>
<h1 align='center'>Fichier des mots de passe initiaux des professeurs</h1>
<table border='1'>
<tr>
<th>Nom</th>
<th>Prénom</th>
<th>Civilité</th>
<th>Mot de passe</th>
</tr>\n");
					}

					while($cpt<count($prof)){
						if($prof[$cpt]["fonction"]=="ENS"){

							if($prof[$cpt]["sexe"]=="1"){
								$civi="M.";
							}
							else{
								$civi="MM";
							}

							switch($prof[$cpt]["civilite"]){
								case 1:
									$civi="M.";
									break;
								case 2:
									$civi="MM";
									break;
								case 3:
									$civi="ML";
									break;
							}

							if($_POST['mdp']=="alea"){
								$mdp=createRandomPassword();
							}
							else{
								$date=str_replace("-","",$prof[$cpt]["date_naissance"]);
								$mdp=$date;
							}
							//echo $prof[$cpt]["nom_usage"].";".$prof[$cpt]["prenom"].";".$civi.";"."P".$prof[$cpt]["id"].";"."ENS".";".$date."<br />\n";
							$chaine=$prof[$cpt]["nom_usage"].";".$prof[$cpt]["prenom"].";".$civi.";"."P".$prof[$cpt]["id"].";"."ENS".";".$mdp;
							if($fich){
								fwrite($fich,html_entity_decode($chaine)."\n");
							}
							if($_POST['mdp']=="alea"){
								fwrite($fich2,"<tr>
<td>".$prof[$cpt]["nom_usage"]."</td>
<td>".$prof[$cpt]["prenom"]."</td>
<td>$civi</td>
<td>$mdp</td>
</tr>\n");
							}
							echo $chaine."<br />\n";
						}
						$cpt++;
					}
					fclose($fich);
					if($_POST['mdp']=="alea"){
						fwrite($fich2,"</table>
<p>Imprimez cette page, puis supprimez-la en procédant au nettoyage comme indiqué à la page précédente.</p>
</body>
</html>\n");
						fclose($fich2);
					}
					echo "</blockquote>\n";



					echo "<a name='f_men_gepi'></a><h2>Génération d'un CSV du F_MEN pour GEPI</h2>\n";
					echo "<blockquote>\n";
					if(($temoin_au_moins_une_matiere=="")||($temoin_au_moins_un_prof=="")){
						echo "<p>Votre fichier ne comporte pas suffisamment d'informations pour générer ce CSV.<br />Il faut que les emplois du temps soient remontés vers STS pour que le fichier XML permette de générer ce CSV.</p>\n";
					}
					else{
						$fich=fopen("../backup/$dirname/csv/f_men.csv","w+");
						$chaine="MATIMN;NUMIND;ELSTCO";
						if($fich){
							fwrite($fich,html_entity_decode($chaine)."\n");
						}
						echo $chaine."<br />\n";
						for($i=0;$i<count($divisions);$i++){
							//$divisions[$i]["services"][$j]["code_matiere"]
							$classe=$divisions[$i]["code"];
							//echo "\$classe=$classe<br />";
							for($j=0;$j<count($divisions[$i]["services"]);$j++){
								$mat="";
								for($m=0;$m<count($matiere);$m++){
									if($matiere[$m]["code"]==$divisions[$i]["services"][$j]["code_matiere"]){
										$mat=$matiere[$m]["code_gestion"];
									}
								}
								//echo "\$mat=$mat<br />";
								if($mat!=""){
									for($k=0;$k<count($divisions[$i]["services"][$j]["enseignants"]);$k++){
										//echo $mat."|".$classe."|P".$divisions[$i]["services"][$j]["enseignants"][$k]["id"]."<br />\n";
										//echo $mat.";P".$divisions[$i]["services"][$j]["enseignants"][$k]["id"].";".$classe."<br />\n";
										$chaine=$mat.";P".$divisions[$i]["services"][$j]["enseignants"][$k]["id"].";".$classe;
										if($fich){
											fwrite($fich,html_entity_decode($chaine)."\n");
										}
										echo $chaine."<br />\n";
									}
								}
							}
						}

						//echo "<hr width='200' />\n";
						for($i=0;$i<count($groupes);$i++){
							$grocod=$groupes[$i]["code"];
							//echo "<p>Groupe $i: \$grocod=$grocod<br />\n";
							for($m=0;$m<count($matiere);$m++){
								//echo "\$matiere[$m][\"code\"]=".$matiere[$m]["code"]." et \$groupes[$i][\"code_matiere\"]=".$groupes[$i]["code_matiere"]."<br />\n";
								if($matiere[$m]["code"]==$groupes[$i]["code_matiere"]){
									//$matimn=$programme[$k]["code_matiere"];
									$matimn=$matiere[$m]["code_gestion"];
									//echo "<b>Trouvé: matière n°$m: \$matimn=$matimn</b><br />\n";
								}
							}
							//$groupes[$i]["enseignant"][$m]["id"]
							//$groupes[$i]["divisions"][$j]["code"]
							if($matimn!=""){
								for($j=0;$j<count($groupes[$i]["divisions"]);$j++){
									$elstco=$groupes[$i]["divisions"][$j]["code"];
									//echo "\$elstco=$elstco<br />\n";
									if(count($groupes[$i]["enseignant"])==0){
										$chaine="$matimn;;$elstco";
										if($fich){
											fwrite($fich,html_entity_decode($chaine)."\n");
										}
										echo $chaine."<br />\n";
									}
									else{
										for($m=0;$m<count($groupes[$i]["enseignant"]);$m++){
											$numind=$groupes[$i]["enseignant"][$m]["id"];
											//echo "$matimn;P$numind;$elstco<br />\n";
											$chaine="$matimn;P$numind;$elstco";
											if($fich){
												fwrite($fich,html_entity_decode($chaine)."\n");
											}
											echo $chaine."<br />\n";
										}
									}
									//echo $grocod.";".$groupes[$i]["divisions"][$j]["code"]."<br />\n";
								}
							}
						}
						fclose($fich);
					}
					echo "<p>Je ne sais pas trop pour le préfixe P.<br />Il n'est pas dans le fichier XML, mais est utilisé par SE3...<br />Et par contre, sur les F_WIND.DBF générés par AutoSco, il y a un préfixe E.</p>";
					echo "</blockquote>\n";













					echo "<a name='f_gpd_gepi'></a><h2>Génération d'un CSV du F_GPD pour GEPI</h2>\n";
					echo "<blockquote>\n";
					//echo "GROCOD;DIVCOD<br />\n";
					$fich=fopen("../backup/$dirname/csv/f_gpd.csv","w+");
					$chaine="GROCOD;DIVCOD";
					if($fich){
						fwrite($fich,html_entity_decode($chaine)."\n");
					}
					echo $chaine."<br />\n";

					for($i=0;$i<count($groupes);$i++){
						//$divisions[$i]["services"][$j]["code_matiere"]
						$grocod=$groupes[$i]["code"];
						for($j=0;$j<count($groupes[$i]["divisions"]);$j++){
							//echo $grocod.";".$groupes[$i]["divisions"][$j]["code"]."<br />\n";
							$chaine=$grocod.";".$groupes[$i]["divisions"][$j]["code"];
							if($fich){
								fwrite($fich,html_entity_decode($chaine)."\n");
							}
							echo $chaine."<br />\n";
						}
					}
					fclose($fich);
					echo "</blockquote>\n";



					echo "<a name='f_tmt_gepi'></a><h2>Génération d'un CSV du F_TMT pour GEPI</h2>\n";
					echo "<blockquote>\n";
					//echo "MATIMN;MATILC<br />\n";
					$fich=fopen("../backup/$dirname/csv/f_tmt.csv","w+");
					$chaine="MATIMN;MATILC";
					if($fich){
						fwrite($fich,html_entity_decode($chaine)."\n");
					}
					echo $chaine."<br />\n";
					for($i=0;$i<count($matiere);$i++){
						//echo $matiere[$i]["code_gestion"].";".$matiere[$i]["libelle_court"]."<br />\n";
						$chaine=$matiere[$i]["code_gestion"].";".$matiere[$i]["libelle_court"];
						if($fich){
							fwrite($fich,html_entity_decode($chaine)."\n");
						}
						echo $chaine."<br />\n";
					}
					fclose($fich);
					echo "</blockquote>\n";



					echo "<a name='f_div_gepi'></a><h2>Génération d'un CSV du F_DIV pour GEPI</h2>\n";
					echo "<blockquote>\n";
					$fich=fopen("../backup/$dirname/csv/f_div.csv","w+");
					$chaine="DIVCOD;NUMIND";
					if($fich){
						fwrite($fich,html_entity_decode($chaine)."\n");
					}
					echo $chaine."<br />\n";
					for($i=0;$i<count($divisions);$i++){
						$numind_pp="";
						for($m=0;$m<count($prof);$m++){
							for($n=0;$n<count($prof[$m]["prof_princ"]);$n++){
								if($prof[$m]["prof_princ"][$n]["code_structure"]==$divisions[$i]["code"]){
									$numind_pp="P".$prof[$m]["id"];
								}
							}
						}
						//echo $divisions[$i]["code"].";".$divisions[$i]["code"].";".$numind_pp."<br />\n";
						$chaine=$divisions[$i]["code"].";".$numind_pp;
						if($fich){
							fwrite($fich,html_entity_decode($chaine)."\n");
						}
						echo $chaine."<br />\n";
					}
					fclose($fich);
					echo "<p>Ce CSV est destiné à renseigner les Professeurs Principaux...</p>\n";
					echo "</blockquote>\n";

					echo "<div id='boxInfo' style='position:absolute; top: 70px; left: 300px; width: 300px; background: yellow; border: 1px solid black; padding-left: 5px; padding-right: 5px; padding-top: 0;'  onmousedown=\"dragStart(event, 'boxInfo')\">\n";

					echo "<h4 style='margin:0; padding:0; text-align:center;'>GEPI</h4>\n";
					//echo "<p style='margin-top: 0;'>Effectuez un Clic-droit/Enregistrer la cible du lien sous... pour chacun des fichiers ci-dessous.</p>\n";
					echo "<p style='margin-top: 0;'>Récupérez les CSV suivants (<i>pas par clic-droit</i>).</p>\n";
					echo "<table border='0'>\n";

					echo "<tr><td>Fichier Profs:</td><td><a href='save_csv.php?fileid=0'>f_wind.csv</a></td></tr>\n";
					echo "<tr><td>Fichier Classes/matières/profs:</td><td><a href='save_csv.php?fileid=1'>f_men.csv</a></td></tr>\n";
					echo "<tr><td>Fichier Groupes/classes:</td><td><a href='save_csv.php?fileid=2'>f_gpd.csv</a></td></tr>\n";
					echo "<tr><td>Fichier Matières:</td><td><a href='save_csv.php?fileid=3'>f_tmt.csv</a></td></tr>\n";
					echo "<tr><td>Fichier Profs principaux:</td><td><a href='save_csv.php?fileid=4'>f_div.csv</a></td></tr>\n";

					echo "</table>\n";
					if($_POST['mdp']=="alea"){
						echo "<p>Voici également une <a href='../backup/$dirname/csv/profs.html' target='_blank'>page des mots de passe initiaux des professeurs</a> à imprimer avant de procéder au nettoyage ci-dessous.</p>\n";
					}
					echo "<p>Pour supprimer les fichiers après récupération: <a href='".$_SERVER['PHP_SELF']."?nettoyage=oui".add_token_in_url()."'>Nettoyage</a></p>\n";
					echo "</div>\n";
				}
/*
				else{
					echo "<p><span style='color:red'>ERREUR!</span> Le fichier XML n'a pas pu être ouvert.<br />\n";
					echo "Contrôlez si la taille du fichier XML ne dépasse pas la taille maximale autorisée par votre serveur: ".$upload_max_filesize."<br />\n";
					echo "<a href='".$_SERVER['PHP_SELF']."'>Retour</a>.</p>\n";
				}
*/
			}

		?>
		<p>Retour à l'<a href="index.php">index</a></p>
	</div>
<!--/body>
</html-->
<?php require("../lib/footer.inc.php");?>
