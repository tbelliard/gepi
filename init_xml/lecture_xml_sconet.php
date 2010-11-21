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

//**************** EN-TETE *****************
$titre_page = "XML de SCONET: Génération de CSV";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

function extr_valeur($lig){
	unset($tabtmp);
	$tabtmp=explode(">",my_ereg_replace("<",">",$lig));
	return trim($tabtmp[2]);
}

?>
<!--!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>Lecture du XML Emploi du temps de Sts-web et génération de CSV</title>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-15" />
	<meta name="author" content="Stephane Boireau, A.S. RUE de Bernay/Pont-Audemer" />
	<link type="text/css" rel="stylesheet" href="../style.css" />
</head>
<body-->
	<div class="content">
		<?php
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
						/*
						echo "<p>Cette page génère des fichiers CSV:</p>\n";
						echo "<ul>\n";
							echo "<li>\n";
								echo "<p><b>Pour SambaEdu3:</b></p>\n";
								echo "<ul>\n";
								echo "<li>f_wind.txt</li>\n";
								echo "<li>f_div.txt</li>\n";
								echo "<li>f_men.txt</li>\n";
								echo "</ul>\n";
							echo "</li>\n";
							echo "<li>\n";
								echo "<p><b>Pour Gepi:</b></p>\n";
								echo "<ul>\n";
								echo "<li>f_wind.csv</li>\n";
								echo "<li>f_div.csv</li>\n";
								echo "<li>f_men.csv</li>\n";
								echo "<li>f_gpd.csv</li>\n";
								echo "<li>f_tmt.csv</li>\n";
								echo "</ul>\n";
							echo "</li>\n";
						echo "</ul>\n";
						echo "<p>Il faut lui fournir un Export XML réalisé depuis l'application STS-web.<br />Demandez gentiment à votre secrétaire d'accéder à STS-web et d'effectuer 'Mise à jour/Exports/Emplois du temps'.</p>\n";
						*/
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
						/*
						echo "<p> Pour GEPI:<br />\n";
						echo "<input type=\"radio\" name=\"mdp\" value=\"alea\" checked> Générer un mot de passe aléatoire pour chaque professeur.<br />\n";
						echo "<input type=\"radio\" name=\"mdp\" value=\"date\"> Utiliser plutôt la date de naissance au format 'aaaammjj' comme mot de passe initial (<i>il devra être modifié au premier login</i>).</p>\n";
						echo "<input type='hidden' name='is_posted' value='yes'>\n";
						//echo "</p>\n";
						*/
						echo "<p><input type='submit' value='Valider' /></p>\n";
						echo "</form>\n";
					}
					else {
						$post_max_size=ini_get('post_max_size');
						$upload_max_filesize=ini_get('upload_max_filesize');
						$max_execution_time=ini_get('max_execution_time');
						$memory_limit=ini_get('memory_limit');

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


						if($etape==1){
							$xml_file = isset($_FILES["eleves_xml_file"]) ? $_FILES["eleves_xml_file"] : NULL;
							$fp=fopen($xml_file['tmp_name'],"r");
							if($fp){
								echo "<h3>Lecture du fichier Elèves...</h3>\n";
								echo "<blockquote>\n";
								while(!feof($fp)){
									$ligne[]=fgets($fp,4096);
								}
								fclose($fp);
								echo "<p>Terminé.</p>\n";

								echo "<p>Aller à la section <a href='#csv'>CSV</a>.<br />\n";

								echo "Si vous patientez, des liens directs seront proposés (<i>dans un cadre jaune</i>) pour télécharger les fichiers.<br />Si la page finit son chargement sans générer de cadre jaune, il se peut que la configuration de PHP donne un temps de traitement trop court";
								if($max_execution_time!=0){
									echo " (<i>".$max_execution_time."s sur votre serveur</i>)";
								}
								else{
									echo " (<i>consultez la valeur de la variable 'max_execution_time' dans votre 'php.ini'</i>)";
								}
								echo " ou une charge maximale trop réduite";
								if("$memory_limit"!="0"){
									echo " (<i>".$memory_limit." sur votre serveur</i>)\n";
								}
								else{
									echo " (<i>consultez la valeur de la variable 'memory_limit' dans votre 'php.ini'</i>)";
								}
								echo ".</p>\n";
								echo "</blockquote>\n";


								echo "<h3>Analyse du fichier pour extraire les informations élèves...</h3>\n";
								echo "<blockquote>\n";

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

								/*
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
								"LL_COMMUNE_INSEE"
								);
								*/

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
								while($cpt<count($ligne)){
									//echo "<p>".htmlentities($ligne[$cpt])."<br />\n";
									if(strstr($ligne[$cpt],"<ELEVES>")){
										echo "Début de la section ELEVES à la ligne <span style='color: blue;'>$cpt</span><br />\n";
										$temoin_eleves++;
									}
									if(strstr($ligne[$cpt],"</ELEVES>")){
										echo "Fin de la section ELEVES à la ligne <span style='color: blue;'>$cpt</span><br />\n";
										$temoin_eleves++;
										break;
									}
									if($temoin_eleves==1){
										if(strstr($ligne[$cpt],"<ELEVE ")){
											$i++;
											$eleves[$i]=array();
											$eleves[$i]["scolarite_an_dernier"]=array();

											//echo "<p><b>".htmlentities($ligne[$cpt])."</b><br />\n";
											unset($tabtmp);
											$tabtmp=explode('"',strstr($ligne[$cpt]," ELEVE_ID="));
											$eleves[$i]["eleve_id"]=trim($tabtmp[1]);
											//echo "\$eleves[$i][\"eleve_id\"]=".$eleves[$i]["eleve_id"]."<br />\n";

											unset($tabtmp);
											$tabtmp=explode('"',strstr($ligne[$cpt]," ELENOET="));
											$eleves[$i]["elenoet"]=trim($tabtmp[1]);
											//echo "\$eleves[$i][\"elenoet\"]=".$eleves[$i]["elenoet"]."<br />\n";
											$temoin_ele=1;
										}
										if(strstr($ligne[$cpt],"</ELEVE>")){
											$temoin_ele=0;
										}
										if($temoin_ele==1){
											if(strstr($ligne[$cpt],"<SCOLARITE_AN_DERNIER>")){
												$temoin_scol=1;
											}
											if(strstr($ligne[$cpt],"</SCOLARITE_AN_DERNIER>")){
												$temoin_scol=0;
											}

											if($temoin_scol==0){
												for($loop=0;$loop<count($tab_champs_eleve);$loop++){
													if(strstr($ligne[$cpt],"<".$tab_champs_eleve[$loop].">")){
														$tmpmin=strtolower($tab_champs_eleve[$loop]);
														$eleves[$i]["$tmpmin"]=extr_valeur($ligne[$cpt]);
														//echo "\$eleves[$i][\"$tmpmin\"]=".$eleves[$i]["$tmpmin"]."<br />\n";
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
													if(strstr($ligne[$cpt],"<".$tab_champs_scol_an_dernier[$loop].">")){
														//echo "$i - ";
														$tmpmin=strtolower($tab_champs_scol_an_dernier[$loop]);
														$eleves[$i]["scolarite_an_dernier"]["$tmpmin"]=extr_valeur($ligne[$cpt]);
														//echo "\$eleves[$i][\"scolarite_an_dernier\"][\"$tmpmin\"]=".$eleves[$i]["scolarite_an_dernier"]["$tmpmin"]."<br />\n";
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


								/*

								for($i=0;$i<count($eleves);$i++){
									echo "\$eleves[$i][\"nom\"]=".$eleves[$i]["nom"]."<br />\n";
									echo "\$eleves[$i][\"scolarite_an_dernier\"][\"code_rne\"]=".$eleves[$i]["scolarite_an_dernier"]["code_rne"]."<br />\n";
								}
								*/


								// PARTIE <OPTIONS>
								$temoin_opt="";
								$temoin_opt_ele="";
								while($cpt<count($ligne)){
									if(strstr($ligne[$cpt],"<OPTIONS>")){
										echo "Début de la section OPTIONS à la ligne <span style='color: blue;'>$cpt</span><br />\n";
										$temoin_options++;
									}
									if(strstr($ligne[$cpt],"</OPTIONS>")){
										echo "Fin de la section OPTIONS à la ligne <span style='color: blue;'>$cpt</span><br />\n";
										$temoin_options++;
										break;
									}
									if($temoin_options==1){
										if(strstr($ligne[$cpt],"<OPTION ")){

											//echo "<p><b>".htmlentities($ligne[$cpt])."</b><br />\n";
											unset($tabtmp);
											$tabtmp=explode('"',strstr($ligne[$cpt]," ELEVE_ID="));
											$tmp_eleve_id=trim($tabtmp[1]);

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
												$eleves[$i]["options"]=array();
												$j=0;
												$temoin_opt=1;
											}
										}
										if(strstr($ligne[$cpt],"</OPTION>")){
											$temoin_opt=0;
										}
										if($temoin_opt==1){
										//if(($temoin_opt==1)&&($temoin_ident=="oui")){
											if(strstr($ligne[$cpt],"<OPTIONS_ELEVE>")){
												$eleves[$i]["options"][$j]=array();
												$temoin_opt_ele=1;
											}
											if(strstr($ligne[$cpt],"</OPTIONS_ELEVE>")){
												$j++;
												$temoin_opt_ele=0;
											}

											$tab_champs_opt=array("NUM_OPTION","CODE_MODALITE_ELECT","CODE_MATIERE");
											if($temoin_opt_ele==1){
												for($loop=0;$loop<count($tab_champs_opt);$loop++){
													if(strstr($ligne[$cpt],"<".$tab_champs_opt[$loop].">")){
														$tmpmin=strtolower($tab_champs_opt[$loop]);
														$eleves[$i]["options"][$j]["$tmpmin"]=extr_valeur($ligne[$cpt]);
														//echo "\$eleves[$i][\"$tmpmin\"]=".$eleves[$i]["$tmpmin"]."<br />\n";
														break;
													}
												}
											}
										}
									}
									$cpt++;
								}


								// PARTIE <STRUCTURES>
								$temoin_structures=0;
								$temoin_struct_ele=-1;
								$temoin_struct=-1;
								while($cpt<count($ligne)){
									if(strstr($ligne[$cpt],"<STRUCTURES>")){
										echo "Début de la section STRUCTURES à la ligne <span style='color: blue;'>$cpt</span><br />\n";
										$temoin_structures++;
									}
									if(strstr($ligne[$cpt],"</STRUCTURES>")){
										echo "Fin de la section STRUCTURES à la ligne <span style='color: blue;'>$cpt</span><br />\n";
										$temoin_structures++;
										break;
									}
									if($temoin_structures==1){
										if(strstr($ligne[$cpt],"<STRUCTURES_ELEVE ")){

											//echo "<p><b>".htmlentities($ligne[$cpt])."</b><br />\n";
											unset($tabtmp);
											$tabtmp=explode('"',strstr($ligne[$cpt]," ELEVE_ID="));
											$tmp_eleve_id=trim($tabtmp[1]);

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
												$eleves[$i]["structures"]=array();
												$j=0;
												$temoin_struct_ele=1;
											}
										}
										if(strstr($ligne[$cpt],"</STRUCTURES_ELEVE>")){
											$temoin_struct_ele=0;
										}
										if($temoin_struct_ele==1){
											if(strstr($ligne[$cpt],"<STRUCTURE>")){
												$eleves[$i]["structures"][$j]=array();
												$temoin_struct=1;
											}
											if(strstr($ligne[$cpt],"</STRUCTURE>")){
												$j++;
												$temoin_struct=0;
											}

											$tab_champs_struct=array("CODE_STRUCTURE","TYPE_STRUCTURE");
											if($temoin_struct==1){
												for($loop=0;$loop<count($tab_champs_struct);$loop++){
													if(strstr($ligne[$cpt],"<".$tab_champs_struct[$loop].">")){
														$tmpmin=strtolower($tab_champs_struct[$loop]);
														$eleves[$i]["structures"][$j]["$tmpmin"]=extr_valeur($ligne[$cpt]);
														//echo "\$eleves[$i]["structures"][$j][\"$tmpmin\"]=".$eleves[$i]["structures"][$j]["$tmpmin"]."<br />\n";
														break;
													}
												}
											}
										}
									}
									$cpt++;
								}

								echo "<p>Terminé.</p>\n";
								echo "</blockquote>\n";

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

							}
							else{
								echo "<p><span style='color:red'>ERREUR!</span> Le fichier Elèves n'a pas pu être ouvert.<br />\n";
								echo "Contrôlez si la taille du fichier XML ne dépasse pas la taille maximale autorisée par votre serveur: ".$upload_max_filesize."<br />\n";
								echo "<a href='".$_SERVER['PHP_SELF']."'>Retour</a>.</p>\n";
							}



							$xml_file = isset($_FILES["nomenclature_xml_file"]) ? $_FILES["nomenclature_xml_file"] : NULL;
							$fp=fopen($xml_file['tmp_name'],"r");
							if($fp){
								echo "<h3>Lecture du fichier Nomenclature...</h3>\n";
								echo "<blockquote>\n";
								while(!feof($fp)){
									$ligne[]=fgets($fp,4096);
								}
								fclose($fp);
								echo "<p>Terminé.</p>\n";
								echo "</blockquote>\n";

								echo "<h3>Analyse du fichier pour extraire les informations de Nomenclature...</h3>\n";
								echo "<blockquote>\n";

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
								while($cpt<count($ligne)){
									//echo htmlentities($ligne[$cpt])."<br />\n";

									if(strstr($ligne[$cpt],"<MATIERES>")){
										echo "Début de la section MATIERES à la ligne <span style='color: blue;'>$cpt</span><br />\n";
										$temoin_matieres++;
									}
									if(strstr($ligne[$cpt],"</MATIERES>")){
										echo "Fin de la section MATIERES à la ligne <span style='color: blue;'>$cpt</span><br />\n";
										$temoin_matieres++;
										break;
									}
									if($temoin_matieres==1){
										if(strstr($ligne[$cpt],"<MATIERE ")){
											$i++;
											$matieres[$i]=array();

											//echo "<p><b>".htmlentities($ligne[$cpt])."</b><br />\n";
											unset($tabtmp);
											$tabtmp=explode('"',strstr($ligne[$cpt]," CODE_MATIERE="));
											$matieres[$i]["code_matiere"]=trim($tabtmp[1]);
											//echo "\$matieres[$i][\"matiere_id\"]=".$matieres[$i]["matiere_id"]."<br />\n";
											$temoin_mat=1;
										}
										if(strstr($ligne[$cpt],"</MATIERE>")){
											$temoin_mat=0;
										}
										if($temoin_mat==1){
											for($loop=0;$loop<count($tab_champs_matiere);$loop++){
												if(strstr($ligne[$cpt],"<".$tab_champs_matiere[$loop].">")){
													$tmpmin=strtolower($tab_champs_matiere[$loop]);
													$matieres[$i]["$tmpmin"]=extr_valeur($ligne[$cpt]);
													//echo "\$matieres[$i][\"$tmpmin\"]=".$matieres[$i]["$tmpmin"]."<br />\n";
													break;
												}
											}
										}
									}
									$cpt++;
								}
								echo "<p>Terminé.</p>\n";
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
										$tmpmin=strtolower($tab_champs_matiere[$j]);
										echo "<td>".$matieres[$i]["$tmpmin"]."</td>\n";
									}
									echo "</tr>\n";
									$i++;
								}
								echo "</table>\n";
								echo "</blockquote>\n";
							}
							else{
								echo "<p><span style='color:red'>ERREUR!</span> Le fichier Nomenclature.xml n'a pas pu être ouvert.<br />\n";
								echo "Contrôlez si la taille du fichier XML ne dépasse pas la taille maximale autorisée par votre serveur: ".$upload_max_filesize."<br />\n";
								echo "<a href='".$_SERVER['PHP_SELF']."'>Retour</a>.</p>\n";
							}

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
										$chaine=substr($chaine,0,strlen($chaine)-1);
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

							/*
							for($i=0;$i<count($eleves);$i++){
								echo "\$eleves[$i][\"nom\"]=".$eleves[$i]["nom"]."<br />\n";
								echo "\$eleves[$i][\"scolarite_an_dernier\"][\"code_rne\"]=".$eleves[$i]["scolarite_an_dernier"]["code_rne"]."<br />\n";
							}
							*/

							/*
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
								"LL_COMMUNE_INSEE"
								);
							*/

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
												/*
												for($j=0;$j<count($tab_champs_scol_an_dernier);$j++){
													$tmpmin=strtolower($tab_champs_scol_an_dernier[$j]);
													echo "<td>";
													if(isset($eleves[$i]["scolarite_an_dernier"]["$tmpmin"])){
														echo $eleves[$i]["scolarite_an_dernier"]["$tmpmin"];
														$chaine.=$eleves[$i]["scolarite_an_dernier"]["$tmpmin"];
													}
													else{
														echo "&nbsp;";
													}
													echo "</td>\n";

													//$chaine.=$eleves[$i]["scolarite_an_dernier"]["$tmpmin"].";";
													$chaine.=";";
												}
												*/

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
													/*
													switch($eleves[$i]["scolarite_an_dernier"]["denom_princ"]){
														case :
															echo "";
															$chaine.="";
															break;
														case :
															echo "";
															$chaine.="";
															break;
														case :
															echo "";
															$chaine.="";
															break;
													}
													*/
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
											$chaine=substr($chaine,0,strlen($chaine)-1);
											fwrite($fich,$chaine."\n");
										}
									}
								//}
								$i++;
							}

							/*
							//fwrite($fich,"CODE_RNE;SIGLE;DENOM_PRINC;DENOM_COMPL;LIGNE1_ADRESSE;LIGNE2_ADRESSE;LIGNE3_ADRESSE;LIGNE4_ADRESSE;BOITE_POSTALE;MEL;TELEPHONE;LL_COMMUNE_INSEE\n");
							fwrite($fich,"CODE_STRUCTURE;CODE_RNE;SIGLE;DENOM_PRINC;DENOM_COMPL;LIGNE1_ADRESSE;LIGNE2_ADRESSE;LIGNE3_ADRESSE;LIGNE4_ADRESSE;BOITE_POSTALE;MEL;TELEPHONE;LL_COMMUNE_INSEE\n");

							echo "<table border='1'>\n";
							echo "<tr>\n";
							for($i=0;$i<count($tab_champs_scol_an_dernier);$i++){
								echo "<th>$tab_champs_scol_an_dernier[$i]</th>\n";
							}
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
												for($j=0;$j<count($tab_champs_scol_an_dernier);$j++){
													$tmpmin=strtolower($tab_champs_scol_an_dernier[$j]);
													echo "<td>";
													if(isset($eleves[$i]["scolarite_an_dernier"]["$tmpmin"])){
														echo $eleves[$i]["scolarite_an_dernier"]["$tmpmin"];
														$chaine.=$eleves[$i]["scolarite_an_dernier"]["$tmpmin"];
													}
													else{
														echo "&nbsp;";
													}
													echo "</td>\n";

													//$chaine.=$eleves[$i]["scolarite_an_dernier"]["$tmpmin"].";";
													$chaine.=";";
												}
												echo "</tr>\n";
											}
											$chaine=substr($chaine,0,strlen($chaine)-1);
											fwrite($fich,$chaine."\n");
										}
									}
								//}
								$i++;
							}
							*/

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
						else{
							$xml_file = isset($_FILES["responsables_xml_file"]) ? $_FILES["responsables_xml_file"] : NULL;
							$fp=fopen($xml_file['tmp_name'],"r");
							if($fp){
								echo "<h3>Lecture du fichier Responsables...</h3>\n";
								echo "<blockquote>\n";
								while(!feof($fp)){
									$ligne[]=fgets($fp,4096);
								}
								fclose($fp);
								echo "<p>Terminé.</p>\n";

								echo "<p>Aller à la section <a href='#csv'>CSV</a>.<br />\n";

								echo "Si vous patientez, des liens directs seront proposés (<i>dans un cadre jaune</i>) pour télécharger les fichiers.<br />Si la page finit son chargement sans générer de cadre jaune, il se peut que la configuration de PHP donne un temps de traitement trop court";
								if($max_execution_time!=0){
									echo " (<i>".$max_execution_time."s sur votre serveur</i>)";
								}
								else{
									echo " (<i>consultez la valeur de la variable 'max_execution_time' dans votre 'php.ini'</i>)";
								}
								echo " ou une charge maximale trop réduite";
								if("$memory_limit"!="0"){
									echo " (<i>".$memory_limit." sur votre serveur</i>)\n";
								}
								else{
									echo " (<i>consultez la valeur de la variable 'memory_limit' dans votre 'php.ini'</i>)";
								}
								echo ".</p>\n";
								echo "</blockquote>\n";



								echo "<h3>Analyse du fichier pour extraire les informations Responsables...</h3>\n";
								echo "<blockquote>\n";

								$personnes=array();
								$responsables=array();
								$adresses=array();

								$temoin_personnes=0;
								$temoin_responsables=0;
								$temoin_adresses=0;
								$temoin_pers=0;
								$temoin_resp=0;
								$temoin_addr=-1;

								/*
								$tab_champs_personne=array("NOM",
								"PRENOM",
								"TEL_PERSONNEL",
								"TEL_PORTABLE",
								"TEL_PROFESSIONNEL",
								"MEL",
								"ACCEPTE_SMS",
								"ADRESSE_ID",
								"CODE_PROFESSION",
								"COMMUNICATION_ADRESSE"
								);
								*/

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

								$tab_champs_responsable=array("ELEVE_ID",
								"PERSONNE_ID",
								"RESP_LEGAL",
								"CODE_PARENTE",
								"RESP_FINANCIER",
								"PERS_PAIMENT",
								"PERS_CONTACT"
								);

								$tab_champs_adresse=array("LIGNE1_ADRESSE",
								"LIGNE2_ADRESSE",
								"LIGNE3_ADRESSE",
								"LIGNE4_ADRESSE",
								"CODE_POSTAL",
								"LL_PAYS",
								"CODE_DEPARTEMENT",
								"LIBELLE_POSTAL"
								);

								// PARTIE <PERSONNES>
								// Compteur personnes:
								$i=-1;
								// Compteur de lignes du fichier:
								$cpt=0;
								while($cpt<count($ligne)){
									//echo htmlentities($ligne[$cpt])."<br />\n";

									if(strstr($ligne[$cpt],"<PERSONNES>")){
										echo "Début de la section PERSONNES à la ligne <span style='color: blue;'>$cpt</span><br />\n";
										$temoin_personnes++;
									}
									if(strstr($ligne[$cpt],"</PERSONNES>")){
										echo "Fin de la section PERSONNES à la ligne <span style='color: blue;'>$cpt</span><br />\n";
										$temoin_personnes++;
										break;
									}
									if($temoin_personnes==1){
										if(strstr($ligne[$cpt],"<PERSONNE ")){
											$i++;
											$personnes[$i]=array();

											//echo "<p><b>".htmlentities($ligne[$cpt])."</b><br />\n";
											unset($tabtmp);
											$tabtmp=explode('"',strstr($ligne[$cpt]," PERSONNE_ID="));
											$personnes[$i]["personne_id"]=trim($tabtmp[1]);
											//echo "\$personnes[$i][\"personne_id\"]=".$personnes[$i]["personne_id"]."<br />\n";
											$temoin_pers=1;
										}
										if(strstr($ligne[$cpt],"</PERSONNE>")){
											$temoin_pers=0;
										}
										if($temoin_pers==1){
											for($loop=0;$loop<count($tab_champs_personne);$loop++){
												if(strstr($ligne[$cpt],"<".$tab_champs_personne[$loop].">")){
													$tmpmin=strtolower($tab_champs_personne[$loop]);
													$personnes[$i]["$tmpmin"]=extr_valeur($ligne[$cpt]);
													//echo "\$personnes[$i][\"$tmpmin\"]=".$personnes[$i]["$tmpmin"]."<br />\n";
													break;
												}
											}
										}
									}
									$cpt++;
								}




								// PARTIE <RESPONSABLES>
								// Compteur responsables:
								$i=-1;
								// Compteur de lignes du fichier:
								$cpt=0;
								while($cpt<count($ligne)){
									//echo htmlentities($ligne[$cpt])."<br />\n";

									if(strstr($ligne[$cpt],"<RESPONSABLES>")){
										echo "Début de la section RESPONSABLES à la ligne <span style='color: blue;'>$cpt</span><br />\n";
										$temoin_responsables++;
									}
									if(strstr($ligne[$cpt],"</RESPONSABLES>")){
										echo "Fin de la section RESPONSABLES à la ligne <span style='color: blue;'>$cpt</span><br />\n";
										$temoin_responsables++;
										break;
									}
									if($temoin_responsables==1){
										if(strstr($ligne[$cpt],"<RESPONSABLE_ELEVE>")){
											$i++;
											$responsables[$i]=array();
											$temoin_resp=1;
										}
										if(strstr($ligne[$cpt],"</RESPONSABLE_ELEVE>")){
											$temoin_resp=0;
										}
										if($temoin_resp==1){
											for($loop=0;$loop<count($tab_champs_responsable);$loop++){
												if(strstr($ligne[$cpt],"<".$tab_champs_responsable[$loop].">")){
													$tmpmin=strtolower($tab_champs_responsable[$loop]);
													$responsables[$i]["$tmpmin"]=extr_valeur($ligne[$cpt]);
													//echo "\$responsables[$i][\"$tmpmin\"]=".$responsables[$i]["$tmpmin"]."<br />\n";
													break;
												}
											}
										}
									}
									$cpt++;
								}





								// PARTIE <ADRESSES>
								// Compteur adresses:
								$i=-1;
								$temoin_adr=-1;
								// Compteur de lignes du fichier:
								$cpt=0;
								while($cpt<count($ligne)){
									//echo htmlentities($ligne[$cpt])."<br />\n";

									if(strstr($ligne[$cpt],"<ADRESSES>")){
										echo "Début de la section ADRESSES à la ligne <span style='color: blue;'>$cpt</span><br />\n";
										$temoin_adresses++;
									}
									if(strstr($ligne[$cpt],"</ADRESSES>")){
										echo "Fin de la section ADRESSES à la ligne <span style='color: blue;'>$cpt</span><br />\n";
										$temoin_adresses++;
										break;
									}
									if($temoin_adresses==1){
										if(strstr($ligne[$cpt],"<ADRESSE ")){
											$i++;
											$adresses[$i]=array();

											//echo "<p><b>".htmlentities($ligne[$cpt])."</b><br />\n";
											unset($tabtmp);
											$tabtmp=explode('"',strstr($ligne[$cpt]," ADRESSE_ID="));
											$adresses[$i]["adresse_id"]=trim($tabtmp[1]);
											$temoin_adr=1;
										}
										if(strstr($ligne[$cpt],"</ADRESSE>")){
											$temoin_adr=0;
										}

										if($temoin_adr==1){
											for($loop=0;$loop<count($tab_champs_adresse);$loop++){
												if(strstr($ligne[$cpt],"<".$tab_champs_adresse[$loop].">")){
													$tmpmin=strtolower($tab_champs_adresse[$loop]);
													$adresses[$i]["$tmpmin"]=extr_valeur($ligne[$cpt]);
													//echo "\$adresses[$i][\"$tmpmin\"]=".$adresses[$i]["$tmpmin"]."<br />\n";
													break;
												}
											}
										}
									}
									$cpt++;
								}



								echo "<p>Terminé.</p>\n";
								echo "</blockquote>\n";

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
										$tmpmin=strtolower($tab_champs_personne[$j]);
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
										$tmpmin=strtolower($tab_champs_responsable[$j]);
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
										$tmpmin=strtolower($tab_champs_adresse[$j]);
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
							}
							else{
								echo "<p><span style='color:red'>ERREUR!</span> Le fichier Responsables n'a pas pu être ouvert.<br />\n";
								echo "Contrôlez si la taille du fichier XML ne dépasse pas la taille maximale autorisée par votre serveur: ".$upload_max_filesize."<br />\n";
								echo "<a href='".$_SERVER['PHP_SELF']."'>Retour</a>.</p>\n";
							}




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