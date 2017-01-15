<?php
/*
 *
 * Copyright 2001-2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

//extract($_GET, EXTR_OVERWRITE);
//extract($_POST, EXTR_OVERWRITE);

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

// INSERT INTO `droits` VALUES ('/responsables/corrige_ele_id.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Correction des ELE_ID d apres Sconet', '');

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}


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
		flush();
	}
}

function maj_ini_prenom($prenom){
	$prenom2="";
	$tab1=explode("-",$prenom);
	for($i=0;$i<count($tab1);$i++){
		if($i>0){
			$prenom2.="-";
		}
		$tab2=explode(" ",$tab1[$i]);
		for($j=0;$j<count($tab2);$j++){
			if($j>0){
				$prenom2.=" ";
			}
			$prenom2.=ucfirst(mb_strtolower($tab2[$j]));
		}
	}
	return $prenom2;
}

// Etape...
$step=isset($_POST['step']) ? $_POST['step'] : (isset($_GET['step']) ? $_GET['step'] : NULL);
$stop=isset($_POST['stop']) ? $_POST['stop'] : (isset($_GET['stop']) ? $_GET['stop'] :'n');

$mysql_collate=getSettingValue("mysql_collate") ? getSettingValue("mysql_collate") : "";
$chaine_mysql_collate="";
if($mysql_collate!="") {$chaine_mysql_collate="COLLATE $mysql_collate";}

//**************** EN-TETE *****************
$titre_page = "Correction des ELE_ID d'apres Sconet";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();


if(isset($step)){
	if(($step==0)||
		($step==1)
		) {
		echo "<div style='float: right; border: 1px solid black; width: 4em;'>
<form name='formstop' action='".$_SERVER['PHP_SELF']."' method='post'>
<input type='checkbox' name='stop' id='stop' value='y' onchange='stop_change()' ";

if($stop=='y'){
	echo "checked ";
}
echo "/> <a href='#' onmouseover=\"afficher_div('div_stop','y',10,20);\">Stop</a>
</form>\n";
		echo "</div>\n";

		echo creer_div_infobulle("div_stop","","","Ce bouton permet s'il est coché d'interrompre les passages automatiques à la page suivante","",12,0,"n","n","y","n");

		echo "<script type='text/javascript'>
	temporisation_chargement='ok';
	cacher_div('div_stop');
</script>\n";


		echo "<script type='text/javascript'>
function stop_change(){
	stop='n';
	if(document.getElementById('stop')){
		if(document.getElementById('stop').checked==true){
			stop='y';
		}
	}
	if(document.getElementById('id_form_stop')){
		document.getElementById('id_form_stop').value=stop;
	}
}

function test_stop(num){
	stop='n';
	if(document.getElementById('stop')){
		if(document.getElementById('stop').checked==true){
			stop='y';
		}
	}
	//document.getElementById('id_form_stop').value=stop;
	if(stop=='n'){
		//setTimeout(\"document.location.replace('".$_SERVER['PHP_SELF']."?step=1')\",2000);
		document.location.replace('".$_SERVER['PHP_SELF']."?step='+num+'".add_token_in_url(false)."'";

// AJOUT A FAIRE VALEUR STOP
echo "+'&stop='+stop";

echo ");
	}
}

function test_stop2(){
	stop='n';
	if(document.getElementById('stop')){
		if(document.getElementById('stop').checked==true){
			stop='y';
		}
	}
	document.getElementById('id_form_stop').value=stop;
	if(stop=='n'){
		//setTimeout(\"document.forms['formulaire'].submit();\",1000);
		document.forms['formulaire'].submit();
	}
}
</script>\n";

	}
}


echo "<p class=bold>";
echo "<a href=\"maj_import.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
//echo "</p>\n";


if(!isset($step)) {
	echo "</p>\n";

	//echo time()."<br />\n";

	echo "<h2>Fichier ElevesSansAdresses.xml</h2>\n";

	echo "<p>Cette page est destinée à corriger les champs ELE_ID de la table 'eleves' et de la table 'responsables2' d'après le fichier ElevesSansAdresses.xml de Sconet.<br />La correction n'est possible que si les ELENOET sont corrects.</p>\n";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	echo add_token_field();
	echo "<input type=hidden name='step' value='0' />\n";
	echo "<p>Sélectionnez le fichier <b>ElevesAvecAdresses.xml</b> (<i>ou ElevesSansAdresses.xml</i>):<br />\n";
	echo "<input type=\"file\" size=\"80\" name=\"eleves_xml_file\" /><br />\n";

	//==============================
	// AJOUT pour tenir compte de l'automatisation ou non:
	//echo "<input type='hidden' name='stop' id='id_form_stop' value='$stop' />\n";
	echo "<input type='checkbox' name='stop' id='id_form_stop' value='y' /><label for='id_form_stop' style='cursor: pointer;'> Désactiver le mode automatique.</label></p>\n";
	//==============================

	echo "<p><input type='submit' value='Valider' /></p>\n";
	echo "</form>\n";

	echo "<p><br /></p>\n";
	echo "<p><i>NOTE:</i> Après une phase d'analyse, les corrections seront proposées.</p>\n";
}
else{
	/*
	if($step>0){
		echo " | <a href=\"".$_SERVER['PHP_SELF']."\">Correction des ELE_ID d'apres Sconet</a>";
	}
	*/
	echo "</p>\n";

	check_token();

	// On va uploader les fichiers XML dans le tempdir de l'utilisateur (administrateur, ou scolarité pour les màj Sconet)
	$tempdir=get_user_temp_directory();
	if(!$tempdir){
		echo "<p style='color:red'>Il semble que le dossier temporaire de l'utilisateur ".$_SESSION['login']." ne soit pas défini!?</p>\n";
		// Il ne faut pas aller plus loin...
		// SITUATION A GERER
	}

	$post_max_size=ini_get('post_max_size');
	$upload_max_filesize=ini_get('upload_max_filesize');
	$max_execution_time=ini_get('max_execution_time');
	$memory_limit=ini_get('memory_limit');

	//if(!isset($_POST['step'])){
	switch($step){
		case 0:
			// Affichage des informations élèves
			echo "<h2>Analyse du XML élèves</h2>\n";

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

				//$source_file=stripslashes($xml_file['tmp_name']);
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
					$extension_fichier_emis=mb_strtolower(strrchr($fichier_emis,"."));
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
							echo "<p style='color:red;'>Erreur : La taille du fichier extrait (<i>".$list_file_zip[0]['size']." octets</i>) dépasse la limite paramétrée (<i>".lien_valeur_unzipped_max_filesize()."</i>).</p>\n";
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

					$sql="DROP TABLE IF EXISTS temp_gep_import2;";
					//echo "$sql<br />";
					$suppr_table = mysqli_query($GLOBALS["mysqli"], $sql);

					$sql="CREATE TABLE IF NOT EXISTS temp_gep_import2 (
					`ID_TEMPO` varchar(40) NOT NULL default '',
					`LOGIN` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELENOM` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELEPRE` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELESEXE` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELEDATNAIS` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELENOET` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELE_ID` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELEDOUBL` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELENONAT` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELEREG` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`DIVCOD` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ETOCOD_EP` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELEOPT1` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELEOPT2` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELEOPT3` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELEOPT4` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELEOPT5` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELEOPT6` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELEOPT7` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELEOPT8` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELEOPT9` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELEOPT10` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELEOPT11` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`ELEOPT12` varchar(40) $chaine_mysql_collate NOT NULL default '',
					`LIEU_NAISSANCE` varchar(50) $chaine_mysql_collate NOT NULL default ''
					) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
					$create_table = mysqli_query($GLOBALS["mysqli"], $sql);

					$sql="TRUNCATE TABLE temp_gep_import2;";
					$vide_table = mysqli_query($GLOBALS["mysqli"], $sql);

					// On va stocker dans cette deuxième table les correspondances ELE_ID/ELENOET à appliquer sur les tables 'eleves' et 'responsables2'
					$sql="TRUNCATE TABLE tempo2;";
					$vide_table = mysqli_query($GLOBALS["mysqli"], $sql);

					$fp=fopen($dest_file,"r");
					if($fp){
						// On commence par la section STRUCTURES pour ne récupérer que les ELE_ID d'élèves qui sont dans une classe.
						echo "<p>\n";
						echo "Analyse du fichier pour extraire les associations ELENOET/ELE_ID d'élèves affectés dans une classe...<br />\n";

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

									//echo "<p><b>".htmlspecialchars($ligne[$cpt])."</b><br />\n";
									unset($tabtmp);
									//$tabtmp=explode('"',strstr($ligne[$cpt]," ELEVE_ID="));
									$tabtmp=explode('"',strstr($ligne," ELEVE_ID="));
									//$tmp_eleve_id=trim($tabtmp[1]);
									$eleves[$i]['eleve_id']=trim($tabtmp[1]);

									$eleves[$i]["structures"]=array();
									$j=0;
									$temoin_struct_ele=1;
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
												$tmpmin=mb_strtolower($tab_champs_struct[$loop]);
												//$eleves[$i]["structures"][$j]["$tmpmin"]=extr_valeur($ligne[$cpt]);

												//$eleves[$i]["structures"][$j]["$tmpmin"]=extr_valeur($ligne);
												// Suppression des guillemets éventuels
												$eleves[$i]["structures"][$j]["$tmpmin"]=my_ereg_replace('"','',extr_valeur($ligne));

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
								$res_insert=mysqli_query($GLOBALS["mysqli"], $sql);
								if(!$res_insert){
									echo "Erreur lors de la requête $sql<br />\n";
									$nb_err++;
								}
								$id_tempo++;
							}
						}
						if($nb_err==0) {
							echo "<p>La première phase s'est passée sans erreur.</p>\n";

							echo "<script type='text/javascript'>
	setTimeout(\"test_stop('1')\",3000);
</script>\n";
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
						echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=1&amp;stop=y".add_token_in_url()."'>Suite</a></p>\n";

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
			break;


		case 1:
			echo "<h2>Suite de l'analyse du XML</h2>\n";

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
				$res_ele_id=mysqli_query($GLOBALS["mysqli"], $sql);
				affiche_debug("count(\$res_ele_id)=".count($res_ele_id)."<br />");

				unset($tab_ele_id);
				$tab_ele_id=array();
				$cpt=0;
				// Pourquoi est-ce que cela ne fonctionne pas en mysql_fetch_object()???
				// TROUVé: C'EST SENSIBLE à LA CASSE: IL FAUDRAIT $lig->ELE_ID
				//while($lig=mysql_fetch_object($res_ele_id)){
				while($lig=mysqli_fetch_array($res_ele_id)){
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

				echo "<p>";
				echo "Analyse du fichier pour extraire les informations de la section ELEVES...<br />\n";
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
				"LL_COMMUNE_INSEE"
				);

				// PARTIE <ELEVES>
				//while($cpt<count($ligne)){
				while(!feof($fp)){
					$ligne=fgets($fp,4096);
					//echo "<p>".htmlspecialchars($ligne[$cpt])."<br />\n";
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

							//echo "<p><b>".htmlspecialchars($ligne[$cpt])."</b><br />\n";
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
										$tmpmin=mb_strtolower($tab_champs_eleve[$loop]);
										//$eleves[$i]["$tmpmin"]=extr_valeur($ligne[$cpt]);

										// Suppression des guillemets éventuels
										//$eleves[$i]["$tmpmin"]=extr_valeur($ligne);
										$eleves[$i]["$tmpmin"]=my_ereg_replace('"','',extr_valeur($ligne));

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
										$tmpmin=mb_strtolower($tab_champs_scol_an_dernier[$loop]);
										//$eleves[$i]["scolarite_an_dernier"]["$tmpmin"]=extr_valeur($ligne[$cpt]);
										// Suppression des guillemets éventuels
										//$eleves[$i]["scolarite_an_dernier"]["$tmpmin"]=extr_valeur($ligne);
										$eleves[$i]["scolarite_an_dernier"]["$tmpmin"]=my_ereg_replace('"','',extr_valeur($ligne));
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

				//===========================
				// A FAIRE: boireaus 20071115
				// Insérer ici un tableau comme dans la partie ADRESSES pour simuler une barre de progression
				//===========================

				$stat=0;
				$nb_err=0;
				for($i=0;$i<count($eleves);$i++){
					if(in_array($eleves[$i]['eleve_id'],$tab_ele_id)){

						$sql="UPDATE temp_gep_import2 SET ";
						$sql.="elenoet='".$eleves[$i]['elenoet']."', ";
						if(isset($eleves[$i]['id_national'])) {$sql.="elenonat='".$eleves[$i]['id_national']."', ";}
						$sql.="elenom='".addslashes(mb_strtoupper($eleves[$i]['nom']))."', ";

						// On ne retient que le premier prénom:
						$tab_prenom = explode(" ",$eleves[$i]['prenom']);
						$sql.="elepre='".addslashes(maj_ini_prenom($tab_prenom[0]))."', ";

						if(isset($eleves[$i]["code_sexe"])) {
							$sql.="elesexe='".sexeMF($eleves[$i]["code_sexe"])."', ";
						}
						else {
							echo "<span style='color:red'>Sexe non défini dans Sconet pour ".maj_ini_prenom($tab_prenom[0])." ".mb_strtoupper($eleves[$i]['nom'])."</span><br />\n";
							$sql.="elesexe='M', ";
						}
						$sql.="eledatnais='".$eleves[$i]['date_naiss']."', ";
						$sql.="eledoubl='".ouinon($eleves[$i]["doublement"])."', ";
						if(isset($eleves[$i]["scolarite_an_dernier"]["code_rne"])){$sql.="etocod_ep='".$eleves[$i]["scolarite_an_dernier"]["code_rne"]."', ";}
						if(isset($eleves[$i]["code_regime"])){$sql.="elereg='".$eleves[$i]["code_regime"]."', ";}
						$sql=mb_substr($sql,0,mb_strlen($sql)-2);
						$sql.=" WHERE ele_id='".$eleves[$i]['eleve_id']."';";
						affiche_debug("$sql<br />\n");
						$res_insert=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$res_insert){
							echo "Erreur lors de la requête $sql<br />\n";
							$nb_err++;
							flush();
						}
						else{
							$stat++;
						}
					}
				}
				if($nb_err==0) {
					echo "<p>La deuxième phase s'est passée sans erreur.</p>\n";

					echo "<script type='text/javascript'>
	setTimeout(\"test_stop('2')\",3000);
</script>\n";
				}
				elseif($nb_err==1) {
					echo "<p>$nb_err erreur.</p>\n";
				}
				else{
					echo "<p>$nb_err erreurs</p>\n";
				}
			}

			echo "<p>$stat enregistrement(s) ont été mis à jour dans la table 'temp_gep_import2'.</p>\n";

			echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=2&amp;stop=y".add_token_in_url()."'>Suite</a></p>\n";

			require("../lib/footer.inc.php");
			die();

			break;
		case 2:
			echo "<h2>Recherche des correspondances ELENOET</h2>\n";

			// SUPPRIMER LE XML...
			if(file_exists("../temp/".$tempdir."/eleves.xml")) {
				echo "<p>Suppression de eleves.xml... ";
				if(unlink("../temp/".$tempdir."/eleves.xml")){
					echo "réussie.</p>\n";
				}
				else{
					echo "<font color='red'>Echec!</font> Vérifiez les droits d'écriture sur le serveur.</p>\n";
				}
			}

			if(isset($_POST['is_posted'])) {
				$ele_id=isset($_POST['ele_id']) ? $_POST['ele_id'] : NULL;
				$old_ele_id=isset($_POST['old_ele_id']) ? $_POST['old_ele_id'] : NULL;
				$elenoet=isset($_POST['elenoet']) ? $_POST['elenoet'] : NULL;
				$nb_ele_id=isset($_POST['nb_ele_id']) ? $_POST['nb_ele_id'] : 0;
				$cpt=0;
				if(isset($ele_id)) {
					//for($i=0;$i<count($ele_id);$i++){
					for($i=0;$i<$nb_ele_id;$i++){
						//$sql="DELETE FROM temp_gep_import2 WHERE elenoet='$elenoet[$i]';";
						$sql="DELETE FROM temp_gep_import2 WHERE elenoet='$elenoet[$i]' OR elenoet='".sprintf("%05d",$elenoet[$i])."';";
						affiche_debug("$sql<br />\n");
						$nettoyage=mysqli_query($GLOBALS["mysqli"], $sql);

						if($elenoet[$i]!='') {
							if((isset($ele_id[$i]))&&($ele_id[$i]!='')) {
								//$sql="INSERT INTO tempo2 SET col1='$ele_id[$i]', col2='$elenoet[$i]';";
								// Ou on fait les corrections dès maintenant.
								//$sql="UPDATE eleves SET ele_id='$ele_id[$i]' WHERE elenoet='$elenoet[$i]';";
								$sql="UPDATE eleves SET ele_id='$ele_id[$i]' WHERE elenoet='$elenoet[$i]' OR elenoet='".sprintf("%05d",$elenoet[$i])."';";
								affiche_debug("$sql<br />\n");
								//echo "$sql<br />\n";
								/*
								$fsql=fopen("/tmp/fich_debug.sql","a+");
								fwrite($fsql,"$sql\n");
								fclose($fsql);
								*/
								$correction1=mysqli_query($GLOBALS["mysqli"], $sql);

								if($old_ele_id[$i]!='') {
									$sql="UPDATE responsables2 SET ele_id='$ele_id[$i]' WHERE ele_id='$old_ele_id[$i]';";
									affiche_debug("$sql<br />\n");
									//echo "$sql<br />\n";
									$correction2=mysqli_query($GLOBALS["mysqli"], $sql);
								}
								$cpt++;
							}
						}
					}
					echo "<p>$cpt correction(s) effectuée(s).</p>\n";
				}
			}

			echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
			echo add_token_field();
			echo "<input type='hidden' name='is_posted' value='yes' />\n";
			echo "<input type='hidden' name='step' value='2' />\n";


			//$sql="SELECT e.login,e.nom,e.prenom,e.naissance,e.sexe,e.elenoet, e.ele_id AS old_ele_id,t.ele_id FROM eleves e, temp_gep_import2 t WHERE e.elenoet=t.elenoet AND e.ele_id!=t.ele_id ORDER BY e.nom, e.prenom LIMIT 20;";
			$sql="SELECT e.login,e.nom,e.prenom,e.naissance,e.sexe,e.elenoet, e.ele_id AS old_ele_id,t.ele_id FROM eleves e, temp_gep_import2 t WHERE LPAD(e.elenoet,5,'0')=LPAD(t.elenoet,5,'0') AND e.ele_id!=t.ele_id ORDER BY e.nom, e.prenom LIMIT 20;";
			affiche_debug("$sql<br />\n");
			if(!$res=mysqli_query($GLOBALS["mysqli"], $sql)) {
				echo "<p>Une <span style='color:red;'>erreur</span> s'est produite sur la requête&nbsp;:<br /><span style='color:green;'>".$sql."</span><br />\n";
				//Illegal mix of collations
				if(my_eregi("Illegal mix of collations",mysqli_error($GLOBALS["mysqli"]))) {
					//echo "<span style='color:red'>".mysql_error()."</span>\n";
					echo "Il semble qu'il y ait un problème de 'collation' entre les champs 'eleves.ele_id' et 'temp_gep_import2.ele_id'&nbsp;:<br />\n";
					echo "<span style='color:red'>".mysqli_error($GLOBALS["mysqli"])."</span><br />\n";
					echo "Il faudrait supprimer la table 'temp_gep_import2', renseigner la valeur de 'mysql_collate' dans la table 'setting' en mettant la même collation que pour votre champ 'eleves.ele_id'.<br />\n";
					echo "Si par exemple, le champ 'eleves.ele_id' a pour collation 'latin1_general_ci', il faudrait exécuter une requête du type <span style='color:green;'>INSERT INTO setting SET name='mysql_collate', value='latin1_general_ci';</span> ou si la valeur existe déjà <span style='color:green;'>UPDATE setting SET value='latin1_general_ci' WHERE name='mysql_collate';</span><br />\n";
				}
				echo "</p>\n";
			}
			else {
				if(mysqli_num_rows($res)==0){
					if(isset($_POST['is_posted'])) {
						echo "<p>Il n'y a plus d'ELENOET pour lequel effectuer une correction.</p>\n";

						$sql="SELECT * FROM eleves WHERE ele_id LIKE 'e%' OR ele_id LIKE '';";
						$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);

						if(mysqli_num_rows($res_ele)==0){
							echo "<p>Terminé.</p>\n";
						}
						else {
							echo "<p>Il reste un ou des élèves non rattachés à Sconet.<br />
							<a href='".$_SERVER['PHP_SELF']."?step=3&amp;stop=y".add_token_in_url()."'>Tenter une reconnaissance sur les Nom, prénom et date de naissance</a>.</p>\n";
						}

					}
					else{
						echo "<p>Aucun ELENOET trouvé pour effectuer une correction.</p>\n";

						$sql="SELECT * FROM eleves WHERE ele_id LIKE 'e%' OR ele_id LIKE '';";
						$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);

						if(mysqli_num_rows($res_ele)==0){
							echo "<p>Terminé.</p>\n";
						}
						else {
							echo "<p>Il reste un ou des élèves non rattachés à Sconet.<br />
							<a href='".$_SERVER['PHP_SELF']."?step=3&amp;stop=y".add_token_in_url()."'>Tenter une reconnaissance sur les Nom, prénom et date de naissance</a>.</p>\n";
						}
					}
				}
				else{
					echo "<p>Cocher les lignes pour lesquelles corriger l'ELE_ID dans la table 'eleves' (<i>et éventuellement dans la table 'responsables2'</i>) d'après la proposition en dernière colonne.</p>\n";
					echo "<table class='boireaus'>\n";
					echo "<tr>\n";
					echo "<th>";
					echo "<a href=\"javascript:modifcase('coche')\">";
					echo "<img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>";
					echo " / ";
					echo "<a href=\"javascript:modifcase('decoche')\">";
					echo "<img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
					echo "</th>\n";
					echo "<th>Login</th>\n";
					echo "<th>Nom</th>\n";
					echo "<th>Prénom</th>\n";
					echo "<th>Sexe</th>\n";
					echo "<th>Naissance</th>\n";
					echo "<th>ELENOET</th>\n";
					echo "<td style='font-weight:bold; text-align:center; background-color:plum;'>ELE_ID</td>\n";
					echo "</tr>\n";
					$alt=1;
					$cpt=0;
					while($lig=mysqli_fetch_object($res)){
						$alt=$alt*(-1);
						echo "<tr class='lig$alt'>\n";
						echo "<td>";
						echo "<input type='checkbox' name='ele_id[$cpt]' id='ele_id_$cpt' value='$lig->ele_id' />\n";
						echo "<input type='hidden' name='elenoet[$cpt]' value='$lig->elenoet' />\n";
						echo "<input type='hidden' name='old_ele_id[$cpt]' value='$lig->old_ele_id' />\n";
						echo "</td>\n";
						echo "<td>$lig->login</td>\n";
						echo "<td>$lig->nom</td>\n";
						echo "<td>$lig->prenom</td>\n";
						echo "<td>$lig->sexe</td>\n";
						echo "<td>$lig->naissance</td>\n";
						echo "<td>$lig->elenoet</td>\n";
						echo "<td>$lig->ele_id</td>\n";
						echo "</tr>\n";
						$cpt++;
					}
					echo "</table>\n";
					echo "<input type='hidden' name='nb_ele_id' value='$cpt' />\n";
					echo "<input type='submit' name='validation' value='Valider' />\n";
				}
			}
			echo "</form>\n";

			if(isset($cpt)) {
				echo "<script type='text/javascript'>
	function modifcase(mode){
		for(i=0;i<$cpt;i++){
			if(document.getElementById('ele_id_'+i)){
				if(mode=='coche'){
					document.getElementById('ele_id_'+i).checked=true;
				}
				else{
					document.getElementById('ele_id_'+i).checked=false;
				}
			}
		}
	}
</script>\n";
			}

			break;
		case 3:
			echo "<h2>Recherche des correspondances NOM/PRENOM/NAISSANCE</h2>\n";

			if(isset($_POST['is_posted'])) {
				$ele_id=isset($_POST['ele_id']) ? $_POST['ele_id'] : NULL;
				$old_ele_id=isset($_POST['old_ele_id']) ? $_POST['old_ele_id'] : NULL;
				$elenoet=isset($_POST['elenoet']) ? $_POST['elenoet'] : NULL;
				$naissance=isset($_POST['naissance']) ? $_POST['naissance'] : NULL;
				$nb_ele_id=isset($_POST['nb_ele_id']) ? $_POST['nb_ele_id'] : 0;
				$cpt=0;
				if(isset($ele_id)) {
					//for($i=0;$i<count($ele_id);$i++){
					for($i=0;$i<$nb_ele_id;$i++){
						//$sql="DELETE FROM temp_gep_import2 WHERE elenoet='$elenoet[$i]';";
						$sql="DELETE FROM temp_gep_import2 WHERE elenoet='$elenoet[$i]' OR elenoet='".sprintf("%05d",$elenoet[$i])."';";
						affiche_debug("$sql<br />\n");
						$nettoyage=mysqli_query($GLOBALS["mysqli"], $sql);

						if($elenoet[$i]!='') {
							if((isset($ele_id[$i]))&&($ele_id[$i]!='')) {
								//$sql="INSERT INTO tempo2 SET col1='$ele_id[$i]', col2='$elenoet[$i]';";
								// Ou on fait les corrections dès maintenant.
								//$sql="UPDATE eleves SET ele_id='$ele_id[$i]' WHERE elenoet='$elenoet[$i]';";
								$sql="UPDATE eleves SET ele_id='$ele_id[$i]', elenoet='$elenoet[$i]', naissance='$naissance[$i]' WHERE ele_id='$old_ele_id[$i]';";
								affiche_debug("$sql<br />\n");
								//echo "$sql<br />\n";
								/*
								$fsql=fopen("/tmp/fich_debug.sql","a+");
								fwrite($fsql,"$sql\n");
								fclose($fsql);
								*/
								$correction1=mysqli_query($GLOBALS["mysqli"], $sql);

								if($old_ele_id[$i]!='') {
									$sql="UPDATE responsables2 SET ele_id='$ele_id[$i]' WHERE ele_id='$old_ele_id[$i]';";
									affiche_debug("$sql<br />\n");
									//echo "$sql<br />\n";
									$correction2=mysqli_query($GLOBALS["mysqli"], $sql);
								}
								$cpt++;
							}
						}
					}
					echo "<p>$cpt correction(s) effectuée(s).</p>\n";
				}
			}

			echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
			echo add_token_field();
			echo "<input type='hidden' name='is_posted' value='yes' />\n";
			echo "<input type='hidden' name='step' value='3' />\n";

			$sql="SELECT e.login,e.nom,e.prenom,e.naissance,e.sexe, e.naissance, e.elenoet AS old_elenoet, e.ele_id AS old_ele_id, t.ele_id, t.elenoet, t.divcod, t.eledatnais FROM eleves e, temp_gep_import2 t WHERE e.ele_id LIKE 'e%' AND e.nom=t.elenom AND e.prenom=t.elepre ORDER BY e.nom, e.prenom LIMIT 20;";
			affiche_debug("$sql<br />\n");
			if(!$res=mysqli_query($GLOBALS["mysqli"], $sql)) {
				echo "<p>Une <span style='color:red;'>erreur</span> s'est produite sur la requête&nbsp;:<br /><span style='color:green;'>".$sql."</span><br />\n";
				//Illegal mix of collations
				if(my_eregi("Illegal mix of collations",mysqli_error($GLOBALS["mysqli"]))) {
					//echo "<span style='color:red'>".mysql_error()."</span>\n";
					echo "Il semble qu'il y ait un problème de 'collation' sur les champs nom et prénom des tables 'eleves' et 'temp_gep_import2'&nbsp;:<br />\n";
					echo "<span style='color:red'>".mysqli_error($GLOBALS["mysqli"])."</span><br />\n";
					echo "Il faudrait supprimer la table 'temp_gep_import2', renseigner la valeur de 'mysql_collate' dans la table 'setting' en mettant la même collation que pour votre champ 'eleves.ele_id'.<br />\n";
					echo "Si par exemple, le champ 'eleves.ele_id' a pour collation 'latin1_general_ci', il faudrait exécuter une requête du type <span style='color:green;'>INSERT INTO setting SET name='mysql_collate', value='latin1_general_ci';</span> ou si la valeur existe déjà <span style='color:green;'>UPDATE setting SET value='latin1_general_ci' WHERE name='mysql_collate';</span><br />\n";
				}
				echo "</p>\n";
			}
			else {
				if(mysqli_num_rows($res)==0){
					if(isset($_POST['is_posted'])) {
						echo "<p>Il n'y a plus de correspondance nom/prénom trouvée pour laquelle effectuer une correction.</p>\n";
						echo "<p>Terminé.</p>\n";
					}
					else{
						echo "<p>Aucune correspondance nom/prénom trouvée pour effectuer une correction.</p>\n";
					}
				}
				else{
					echo "<p>Cocher les lignes pour lesquelles corriger l'ELE_ID, l'ELENOET et la date de naissance dans la table 'eleves' (<i>et éventuellement dans la table 'responsables2'</i>) d'après la proposition en dernière colonne.</p>\n";
					echo "<table class='boireaus'>\n";
					echo "<tr>
	<th rowspan='2'>
		<a href=\"javascript:modifcase('coche')\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>
		 / 
		<a href=\"javascript:modifcase('decoche')\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>
	</th>
	<th colspan='5'>Table 'eleves'<br />
	(<em>contenu actuel de la base</em>)</th>
	<th colspan='3'>Table 'temp_gep_import2'<br />
	(<em>correspondant au fichier XML fourni</em>)</th>
</tr>";
					echo "<tr>\n";
					echo "<th>Login</th>\n";
					echo "<th>Nom</th>\n";
					echo "<th>Prénom</th>\n";
					echo "<th>Sexe</th>\n";
					echo "<th>Naissance</th>\n";
					echo "<th>Naissance</th>\n";
					echo "<th>ELENOET</th>\n";
					echo "<td style='font-weight:bold; text-align:center; background-color:plum;'>ELE_ID</td>\n";
					echo "</tr>\n";
					$alt=1;
					$cpt=0;
					while($lig=mysqli_fetch_object($res)){
						$alt=$alt*(-1);
						echo "<tr class='lig$alt'>\n";
						echo "<td>";
						echo "<input type='checkbox' name='ele_id[$cpt]' id='ele_id_$cpt' value='$lig->ele_id' />\n";
						echo "<input type='hidden' name='elenoet[$cpt]' value='$lig->elenoet' />\n";
						$naissance=mb_substr($lig->eledatnais,0,4)."-".mb_substr($lig->eledatnais,4,2)."-".mb_substr($lig->eledatnais,6,2);
						echo "<input type='hidden' name='naissance[$cpt]' value='$naissance' />\n";
						echo "<input type='hidden' name='old_ele_id[$cpt]' value='$lig->old_ele_id' />\n";
						echo "</td>\n";
						echo "<td>$lig->login</td>\n";
						echo "<td>$lig->nom</td>\n";
						echo "<td>$lig->prenom</td>\n";
						echo "<td>$lig->sexe</td>\n";
						echo "<td>$lig->naissance</td>\n";
						echo "<td>$naissance</td>\n";
						echo "<td>$lig->elenoet</td>\n";
						echo "<td>$lig->ele_id</td>\n";
						echo "</tr>\n";
						$cpt++;
					}
					echo "</table>\n";
					echo "<input type='hidden' name='nb_ele_id' value='$cpt' />\n";
					echo "<input type='submit' name='validation' value='Valider' />\n";
				}
			}
			echo "</form>\n";

			if(isset($cpt)) {
				echo "<script type='text/javascript'>
	function modifcase(mode){
		for(i=0;i<$cpt;i++){
			if(document.getElementById('ele_id_'+i)){
				if(mode=='coche'){
					document.getElementById('ele_id_'+i).checked=true;
				}
				else{
					document.getElementById('ele_id_'+i).checked=false;
				}
			}
		}
	}
</script>\n";
			}

			break;
	}
}

echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
