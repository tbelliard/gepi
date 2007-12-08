<?php
/*
 * $Id$
 *
 * Copyright 2001-2004 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
};

// INSERT INTO `droits` VALUES ('/responsables/maj_import2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Mise à jour Sconet', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}


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
			$prenom2.=ucfirst(strtolower($tab2[$j]));
		}
	}
	return $prenom2;
}

// Etape...
$step=isset($_POST['step']) ? $_POST['step'] : (isset($_GET['step']) ? $_GET['step'] : NULL);

$parcours_diff=isset($_POST['parcours_diff']) ? $_POST['parcours_diff'] : NULL;

$tab_ele_id=isset($_POST['tab_ele_id']) ? $_POST['tab_ele_id'] : NULL;
$tab_ele_id_diff=isset($_POST['tab_ele_id_diff']) ? $_POST['tab_ele_id_diff'] : NULL;
$nb_parcours=isset($_POST['nb_parcours']) ? $_POST['nb_parcours'] : NULL;

$tab_pers_id=isset($_POST['tab_pers_id']) ? $_POST['tab_pers_id'] : NULL;
$tab_pers_id_diff=isset($_POST['tab_pers_id_diff']) ? $_POST['tab_pers_id_diff'] : NULL;

$total_pers_diff=isset($_POST['total_pers_diff']) ? $_POST['total_pers_diff'] : NULL;

$valid_pers_id=isset($_POST['valid_pers_id']) ? $_POST['valid_pers_id'] : NULL;
$liste_pers_id=isset($_POST['liste_pers_id']) ? $_POST['liste_pers_id'] : NULL;

$tab_adr_id=isset($_POST['tab_adr_id']) ? $_POST['tab_adr_id'] : NULL;
$tab_adr_id_diff=isset($_POST['tab_adr_id_diff']) ? $_POST['tab_adr_id_diff'] : NULL;

/*
$tab_resp_id=isset($_POST['tab_resp_id']) ? $_POST['tab_resp_id'] : NULL;
$tab_resp_id_diff=isset($_POST['tab_resp_id_diff']) ? $_POST['tab_resp_id_diff'] : NULL;
*/

$tab_resp=isset($_POST['tab_resp']) ? $_POST['tab_resp'] : NULL;
$tab_resp_diff=isset($_POST['tab_resp_diff']) ? $_POST['tab_resp_diff'] : NULL;

$total_diff=isset($_POST['total_diff']) ? $_POST['total_diff'] : NULL;

$liste_assoc=isset($_POST['liste_assoc']) ? $_POST['liste_assoc'] : NULL;


$stop=isset($_POST['stop']) ? $_POST['stop'] : (isset($_GET['stop']) ? $_GET['stop'] :'n');

//$style_specifique="responsables/maj_import2";


//**************** EN-TETE *****************
$titre_page = "Mise à jour eleves/responsables";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

if(isset($step)){
	if(($step==0)||
		($step==1)||
		($step==2)||
		($step==3)||
		($step==10)||
		($step==11)||
		($step==12)||
		($step==13)||
		($step==14)||
		($step==17)
		) {
		echo "<div style='float: right; border: 1px solid black; width: 4em;'>
<form name='formstop' action='".$_SERVER['PHP_SELF']."' method='post'>
<input type='checkbox' name='stop' id='stop' value='y' onchange='stop_change()' ";
//if(isset($stop)){
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
		document.location.replace('".$_SERVER['PHP_SELF']."?step='+num";

// AJOUT A FAIRE VALEUR STOP
echo "+'&amp;stop='+stop";

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
echo "<a href=\"index.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
//echo "</p>\n";


// On fournit les fichiers CSV générés depuis les XML de SCONET...
//if (!isset($is_posted)) {
if(!isset($step)) {
	echo "</p>\n";

	//echo time()."<br />\n";

	echo "<h2>Import/mise à jour des élèves</h2>\n";

	echo "<p>Cette page est destinée à effectuer l'import des élèves et responsables d'après les modifications et ajouts effectués sur Sconet.</p>\n";

	echo "<p>Vous allez importer des fichiers d'exports XML de Sconet.<br />\nLes fichiers requis au cours de la procédure sont dans un premier temps ElevesAvecAdresses.xml, puis le fichier ResponsablesAvecAdresses.xml</p>\n";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";

	//echo "<input type=hidden name='is_posted' value='yes' />\n";
	echo "<input type=hidden name='step' value='0' />\n";
	//echo "<input type=hidden name='mode' value='1' />\n";
	echo "<p>Sélectionnez le fichier <b>ElevesAvecAdresses.xml</b> (<i>ou ElevesSansAdresses.xml</i>):<br />\n";
	echo "<input type=\"file\" size=\"80\" name=\"eleves_xml_file\" /><br />\n";

	//==============================
	// AJOUT pour tenir compte de l'automatisation ou non:
	//echo "<input type='hidden' name='stop' id='id_form_stop' value='$stop' />\n";
	echo "<input type='checkbox' name='stop' id='id_form_stop' value='y' /><label for='id_form_stop' style='cursor: pointer;'> Désactiver le mode automatique.</label></p>\n";
	//==============================

	echo "<p><input type='submit' value='Valider' /></p>\n";
	echo "</form>\n";

	echo "<p>Il est recommandé d'importer les informations élèves et de ne passer qu'ensuite à l'import des informations responsables.<br />\n";
	echo "<a href='".$_SERVER['PHP_SELF']."?is_posted=y&amp;step=9'>Passer néanmoins à la page d'importation des responsables</a></p>\n";

	echo "<p><br /></p>\n";

	echo "<p><i>NOTE:</i> Après une phase d'analyse des différences, les différences seront affichées et des cases à cocher seront proposées pour valider les modifications.</p>\n";
}
else{
	if($step>0){
		echo " | <a href=\"".$_SERVER['PHP_SELF']."\">Mise à jour Sconet</a>";
	}
	echo "</p>\n";

	//echo "\$step=$step<br />\n";

	/*
	if(($step==0)||
		($step==1)||
		($step==2)||
		($step==3)||
		($step==7)||
		($step==8)||
		($step==9)||
		($step==10)||
		($step==11)||
		($step==14)
		) {
		echo "<div style='float: right; border: 1px solid black; width: 4em;'>
<form name='formstop' action='".$_SERVER['PHP_SELF']."' method='post'>
<input type='checkbox' name='stop' id='stop' value='y' /> Stop
</form>
</div>\n";
	}
	*/

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
			echo "<h2>Import/mise à jour des élèves</h2>\n";

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
												$tmpmin=strtolower($tab_champs_struct[$loop]);
												//$eleves[$i]["structures"][$j]["$tmpmin"]=extr_valeur($ligne[$cpt]);

												//$eleves[$i]["structures"][$j]["$tmpmin"]=extr_valeur($ligne);
												// Suppression des guillemets éventuels
												$eleves[$i]["structures"][$j]["$tmpmin"]=ereg_replace('"','',extr_valeur($ligne));

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

							echo "<script type='text/javascript'>
	/*
	stop='n';
	if(document.getElementById('stop')){
		if(document.getElementById('stop').checked==true){
			stop='y';
		}
	}
	if(stop=='n'){
		setTimeout(\"document.location.replace('".$_SERVER['PHP_SELF']."?step=1')\",2000);
	}
	*/
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
						echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=1&amp;stop=y'>Suite</a></p>\n";

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
			echo "<h2>Import/mise à jour des élèves</h2>\n";

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

										// Suppression des guillemets éventuels
										//$eleves[$i]["$tmpmin"]=extr_valeur($ligne);
										$eleves[$i]["$tmpmin"]=ereg_replace('"','',extr_valeur($ligne));

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
										// Suppression des guillemets éventuels
										//$eleves[$i]["scolarite_an_dernier"]["$tmpmin"]=extr_valeur($ligne);
										$eleves[$i]["scolarite_an_dernier"]["$tmpmin"]=ereg_replace('"','',extr_valeur($ligne));
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
						/*
						if(!isset($eleves[$i]["code_sexe"])){
							$remarques[]="Le sexe de l'élève <a href='#sexe_manquant_".$i."'>".$eleves[$i]["nom"]." ".$eleves[$i]["prenom"]."</a> n'est pas renseigné dans Sconet.";
						}
						*/

						$sql="UPDATE temp_gep_import2 SET ";
						$sql.="elenoet='".$eleves[$i]['elenoet']."', ";
						if(isset($eleves[$i]['id_national'])) {$sql.="elenonat='".$eleves[$i]['id_national']."', ";}
						//$sql.="elenom='".addslashes($eleves[$i]['nom'])."', ";
						$sql.="elenom='".addslashes(strtoupper($eleves[$i]['nom']))."', ";

						//$sql.="elepre='".addslashes($eleves[$i]['prenom'])."', ";
						// On ne retient que le premier prénom:
						$tab_prenom = explode(" ",$eleves[$i]['prenom']);
						$sql.="elepre='".addslashes(maj_ini_prenom($tab_prenom[0]))."', ";

						//$sql.="elesexe='".sexeMF($eleves[$i]["code_sexe"])."', ";
						if(isset($eleves[$i]["code_sexe"])) {
							$sql.="elesexe='".sexeMF($eleves[$i]["code_sexe"])."', ";
						}
						else {
							echo "<span style='color:red'>Sexe non défini dans Sconet pour ".maj_ini_prenom($tab_prenom[0])." ".strtoupper($eleves[$i]['nom'])."</span><br />\n";
							$sql.="elesexe='M', ";
						}
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
					}
				}
				if($nb_err==0) {
					echo "<p>La deuxième phase s'est passée sans erreur.</p>\n";

					echo "<script type='text/javascript'>
	/*
	stop='n';
	if(document.getElementById('stop')){
		if(document.getElementById('stop').checked==true){
			stop='y';
		}
	}
	if(stop=='n'){
		//setTimeout(\"document.location.replace('".$_SERVER['PHP_SELF']."?step=2')\",2000);
		setTimeout(\"document.location.replace('".$_SERVER['PHP_SELF']."?step=3')\",2000);
	}
	*/
	setTimeout(\"test_stop('3')\",3000);
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

			//echo "<p><a href='".$_SERVER['PHP_SELF']."?etape=1&amp;step=2'>Suite</a></p>\n";

			// ON SAUTE L'ETAPE 2 QUI CORRESPOND AUX OPTIONS DES ELEVES... NON PRISES EN CHARGE POUR LE MOMENT.
			//echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=2'>Suite</a></p>\n";
			echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=3&amp;stop=y'>Suite</a></p>\n";

			require("../lib/footer.inc.php");
			die();

			break;
		case 2:
			echo "<h2>Import/mise à jour des élèves</h2>\n";

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

							$eleves[$i]["options"]=array();
							$j=0;
							$temoin_opt=1;
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

										// Suppression des guillemets éventuels
										//$eleves[$i]["options"][$j]["$tmpmin"]=extr_valeur($ligne);
										$eleves[$i]["options"][$j]["$tmpmin"]=ereg_replace('"','',extr_valeur($ligne));

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

					echo "<script type='text/javascript'>
	/*
	stop='n';
	if(document.getElementById('stop')){
		if(document.getElementById('stop').checked==true){
			stop='y';
		}
	}
	if(stop=='n'){
		setTimeout(\"document.location.replace('".$_SERVER['PHP_SELF']."?step=3')\",2000);
	}
	*/
	setTimeout(\"test_stop('3')\",3000);
</script>\n";
				}
				elseif($nb_err==1) {
					echo "<p>$nb_err erreur.</p>\n";
				}
				else{
					echo "<p>$nb_err erreurs</p>\n";
				}

				echo "<p>$stat option(s) ont été mises à jour dans la table 'temp_gep_import2'.</p>\n";

				//echo "<p><a href='".$_SERVER['PHP_SELF']."?etape=1&amp;step=3'>Suite</a></p>\n";
				echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=3&amp;stop=y'>Suite</a></p>\n";

				require("../lib/footer.inc.php");
				die();
			}

			break;
		case 3:
			echo "<h2>Import/mise à jour des élèves</h2>\n";

			if(file_exists("../temp/".$tempdir."/eleves.xml")) {
				echo "<p>Suppression de eleves.xml... ";
				if(unlink("../temp/".$tempdir."/eleves.xml")){
					echo "réussie.</p>\n";
				}
				else{
					echo "<font color='red'>Echec!</font> Vérifiez les droits d'écriture sur le serveur.</p>\n";
				}
			}

			if(!isset($parcours_diff)){
				$sql="TRUNCATE TABLE tempo2;";
				$res0=mysql_query($sql);

				$sql="SELECT ele_id,naissance FROM eleves";
				$res1=mysql_query($sql);
				//$nb_eleves=mysql_num_rows($res1);
				//if($nb_eleves==0){
				if(mysql_num_rows($res1)==0){
					echo "<p>La table 'eleves' est vide???<br />Avez-vous procédé à l'initialisation de l'année?</p>\n";

					// ON POURRAIT PEUT-ÊTRE PERMETTRE DE POURSUIVRE... en effectuant les étapes init_xml2/step2.php et init_xml2/step3.php

					require("../lib/footer.inc.php");
					die();
				}

				// Il faut prendre la table temp_gep_import2 comme référence pour les différences pour ne pas passer à côté des nouveaux élèves.
				$sql="SELECT ELE_ID,ELEDATNAIS FROM temp_gep_import2";
				$res2=mysql_query($sql);
				$nb_eleves=mysql_num_rows($res2);
				if($nb_eleves==0){
					echo "<p>La table 'temp_gep_import2' est vide???</p>\n";
					require("../lib/footer.inc.php");
					die();
				}

				echo "<p>Les ".$nb_eleves." élèves vont être parcourus par tranches de 20 à la recherche de différences.</p>\n";

				echo "<p>Parcours de la tranche <b>1</b>.</p>\n";
			}
			else{
				echo "<p>Parcours de la tranche <b>$parcours_diff/$nb_parcours</b>.</p>\n";
			}

			flush();

			echo "<form action='".$_SERVER['PHP_SELF']."' name='formulaire' method='post'>\n";
			//==============================
			// AJOUT pour tenir compte de l'automatisation ou non:
   echo "<input type='hidden' name='stop' id='id_form_stop' value='$stop' />\n";
			//==============================

			/*
			$cpt=0;
			$tabrq=array();
			$cpt2=-1;
			while($lig=mysql_fetch_object($res1)){
				$tab_naissance=explode("-",$lig->naissance);
				$naissance=$tab_naissance[0].$tab_naissance[1].$tab_naissance[2];
				$sql="INSERT INTO tempo2 SET col1='$lig->ele_id', col2='$naissance';";
				$insert=mysql_query($sql);
				if($cpt%20==0){
					$cpt2++;
					$tabrq[$cpt2]="e.ele_id='$lig->ele_id'";
				}
				else{
					$tabrq[$cpt2].=" OR e.ele_id='$lig->ele_id'";
				}
				$cpt++;
			}

			for($i=0;$i<count($tabrq);$i++){
				//echo "<p>\$tabrq[$i]=$tabrq[$i]</p>\n";

				$sql="SELECT e.ele_id FROM eleves e, temp_gep_import2 t, tempo2 t2
								WHERE e.ele_id=t.ELE_ID AND
										e.ele_id=t2.col1 AND
										(
											e.nom!=t.ELENOM OR
											e.prenom!=t.ELEPRE OR
											e.sexe!=t.ELESEXE OR
											t2.col2!=t.ELEDATNAIS OR
											e.no_gep!=t.ELENONAT
										)
										AND ($tabrq[$i])
										";
				//echo "$sql<br />";
				$test=mysql_query($sql);

				echo "$i: mysql_num_rows(\$test)=".mysql_num_rows($test)."<br />";
				flush();
			}
			*/

			if(!isset($parcours_diff)){
				// La date de naissance n'est pas au même format dans les tables eleves et temp_gep_import2
				// Une mise au même format est opérée dans une table intermédiaire.
				$tab_ele_id=array();

				$cpt=0;
				$chaine_nouveaux="";
				//while($lig=mysql_fetch_object($res1)){
				while($lig=mysql_fetch_object($res2)){
					//$tab_naissance=explode("-",$lig->naissance);
					//$naissance=$tab_naissance[0].$tab_naissance[1].$tab_naissance[2];
					$naissance=substr($lig->ELEDATNAIS,0,4)."-".substr($lig->ELEDATNAIS,4,2)."-".substr($lig->ELEDATNAIS,6,2);
					//$sql="INSERT INTO tempo2 SET col1='$lig->ele_id', col2='$naissance';";
					$sql="INSERT INTO tempo2 SET col1='$lig->ELE_ID', col2='$naissance';";
					$insert=mysql_query($sql);

					// Est-ce un nouvel élève?
					$sql="SELECT 1=1 FROM eleves e, temp_gep_import2 t WHERE e.ele_id=t.ELE_ID AND t.ELE_ID='$lig->ELE_ID'";
					$test=mysql_query($sql);
					if(mysql_num_rows($test)==0){
						if($cpt>0){$chaine_nouveaux.=", ";}
						$chaine_nouveaux.=$lig->ELE_ID;
						echo "<input type='hidden' name='tab_ele_id_diff[]' value='$lig->ELE_ID' />\n";
						$cpt++;
					}
					else{
						//$tab_ele_id[]=$lig->ele_id;
						$tab_ele_id[]=$lig->ELE_ID;
					}
				}

				//if($chaine_nouveaux==1){
				if($cpt==1){
					echo "<p>L'ELE_ID d'un nouvel élève a été trouvé: $chaine_nouveaux</p>\n";
				}
				//elseif($chaine_nouveaux>1){
				elseif($cpt>1){
					echo "<p>Les ELE_ID de $cpt nouveaux élèves ont été trouvés: $chaine_nouveaux</p>\n";
				}

				$nb_parcours=ceil(count($tab_ele_id)/20);
			}
			else{
				if(isset($tab_ele_id_diff)){
					if(count($tab_ele_id_diff)==1){
						echo "<p>L'ELE_ID, pour lequel une ou des différences ont déjà été repérées, est: \n";
					}
					else{
						echo "<p>Le(s) ELE_ID, pour lesquels une ou des différences ont déjà été repérées, sont: \n";
					}
					$chaine_ele_id_diff="";
					for($i=0;$i<count($tab_ele_id_diff);$i++){
						if($i>0){$chaine_ele_id_diff.=", ";}
						$chaine_ele_id_diff.=$tab_ele_id_diff[$i];
						//echo "$i: ";
						echo "<input type='hidden' name='tab_ele_id_diff[]' value='$tab_ele_id_diff[$i]' />\n";
						//echo "<br />\n";
					}
					echo $chaine_ele_id_diff;
					echo "</p>\n";
				}
			}

			echo "<input type='hidden' name='nb_parcours' value='$nb_parcours' />\n";


			// On construit la chaine des 20 ELE_ID retenus pour la requête à venir:
			$chaine="";
			//for($i=0;$i<count($tab_ele_id);$i++){
			for($i=0;$i<min(20,count($tab_ele_id));$i++){
				if($i>0){$chaine.=" OR ";}
				$chaine.="e.ele_id='$tab_ele_id[$i]'";

				// On teste s'il s'agit d'un nouvel élève:
				//$sql="SELECT 1=1 FROM";
			}

			//echo "\$chaine=$chaine<br />\n";

			// Liste des ELE_ID restant à parcourir:
			for($i=20;$i<count($tab_ele_id);$i++){
				//echo "$i: ";
				echo "<input type='hidden' name='tab_ele_id[]' value='$tab_ele_id[$i]' />\n";
				//echo "<br />\n";
			}


			/*
			$sql="SELECT e.ele_id FROM eleves e, temp_gep_import2 t, tempo2 t2
							WHERE e.ele_id=t.ELE_ID AND
									e.ele_id=t2.col1 AND
									(
										e.nom!=t.ELENOM OR
										e.prenom!=t.ELEPRE OR
										e.sexe!=t.ELESEXE OR
										t2.col2!=t.ELEDATNAIS OR
										e.no_gep!=t.ELENONAT
									)
									AND ($chaine)
									";
			*/




			/*
			$sql="SELECT e.ele_id FROM eleves e, temp_gep_import2 t, tempo2 t2
							WHERE e.ele_id=t.ELE_ID AND
									e.ele_id=t2.col1 AND
									(
										e.nom!=t.ELENOM OR
										e.prenom!=t.ELEPRE OR
										e.sexe!=t.ELESEXE OR
										e.naissance!=t2.col2 OR
										e.no_gep!=t.ELENONAT
									)
									AND ($chaine)
									";

			echo "$sql<br />\n";
			echo strftime("%H:%M:%S")."<br />\n";
			flush();

			$test=mysql_query($sql);
			echo strftime("%H:%M:%S")."<br />\n";
			flush();

			$cpt=0;
			if(mysql_num_rows($test)>0){
				echo "<p>Une ou des différences ont été trouvées dans la tranche étudiée à cette phase.";
				echo "<br />\n";
				echo "En voici le(s) ELE_ID: ";
				//$cpt=0;
				$chaine_ele_id="";
				while($lig=mysql_fetch_object($test)){
					if($cpt>0){$chaine_ele_id.=", ";}
					$chaine_ele_id.=$lig->ele_id;
					echo "<input type='hidden' name='tab_ele_id_diff[]' value='$lig->ele_id' />\n";
					//echo "<br />\n";
					// Pour le cas où on est dans la dernière tranche:
					$tab_ele_id_diff[]=$lig->ele_id;
					$cpt++;
				}
				echo $chaine_ele_id;
			}
			*/

			$cpt=0;
			for($i=0;$i<min(20,count($tab_ele_id));$i++){
				$sql="SELECT e.ele_id FROM eleves e, temp_gep_import2 t, tempo2 t2
							WHERE e.ele_id=t.ELE_ID AND
									e.ele_id=t2.col1 AND
									(
										e.nom!=t.ELENOM OR
										e.prenom!=t.ELEPRE OR
										e.sexe!=t.ELESEXE OR
										e.naissance!=t2.col2 OR
										e.no_gep!=t.ELENONAT
									)
									AND e.ele_id='$tab_ele_id[$i]';";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)>0){
					if($cpt==0){
						echo "<p>Une ou des différences ont été trouvées dans la tranche étudiée à cette phase.";
						echo "<br />\n";
						echo "En voici le(s) ELE_ID: ";
					}
					else{
						echo ", ";
					}
					$lig=mysql_fetch_object($test);
					echo "<input type='hidden' name='tab_ele_id_diff[]' value='$lig->ele_id' />\n";
					echo $lig->ele_id;
					flush();
					$cpt++;
				}
				else{
					// Inutile de tester les différences sur le régime si des différences ont déjà été repérées et que l'ELE_ID est déjà en tab_ele_id_diff[]

					$temoin_test_regime='n';

					if(!isset($tab_ele_id_diff)){
						$temoin_test_regime='y';
					}
					elseif(!in_array($tab_ele_id[$i],$tab_ele_id_diff)){
						$temoin_test_regime='y';
					}

					if($temoin_test_regime=='y'){
						$sql="SELECT jer.regime, t.elereg FROM j_eleves_regime jer, eleves e, temp_gep_import2 t
								WHERE e.ele_id='$tab_ele_id[$i]' AND
										jer.login=e.login AND
										t.ele_id=e.ele_id";
						$test=mysql_query($sql);
						if(mysql_num_rows($test)>0){
							$lig=mysql_fetch_object($test);
							$tmp_reg=traite_regime_sconet($lig->elereg);
							if("$tmp_reg"!="$lig->regime"){
								// BIZARRE CE $cpt... on n'écrit rien après la virgule...
								if($cpt==0){
									echo "<p>Une ou des différences ont été trouvées dans la tranche étudiée à cette phase.";
									echo "<br />\n";
									echo "En voici le(s) ELE_ID: ";
								}
								else{
									echo ", ";
								}
								echo $tab_ele_id[$i];
								echo "<input type='hidden' name='tab_ele_id_diff[]' value='".$tab_ele_id[$i]."' />\n";
								//echo "<br />\n";
								// Pour le cas où on est dans la dernière tranche:
								$tab_ele_id_diff[]=$tab_ele_id[$i];
								$cpt++;
							}
						}
					}
				}
			}

			/*
			for($i=0;$i<min(20,count($tab_ele_id));$i++){

				$temoin_test_regime='n';

				if(!isset($tab_ele_id_diff)){
					$temoin_test_regime='y';
				}
				elseif(!in_array($tab_ele_id[$i],$tab_ele_id_diff)){
					$temoin_test_regime='y';
				}

				if($temoin_test_regime=='y'){
					$sql="SELECT jer.regime, t.elereg FROM j_eleves_regime jer, eleves e, temp_gep_import2 t
							WHERE e.ele_id='$tab_ele_id[$i]' AND
									jer.login=e.login AND
									t.ele_id=e.ele_id";
					$test=mysql_query($sql);
					if(mysql_num_rows($test)>0){
						$lig=mysql_fetch_object($test);
						$tmp_reg=traite_regime_sconet($lig->elereg);
						if("$tmp_reg"!="$lig->regime"){
							// BIZARRE CE $cpt... on n'écrit rien après la virgule...
							if($cpt==0){
								echo "<p>Une ou des différences ont été trouvées dans la tranche étudiée à cette phase.";
								echo "<br />\n";
								echo "En voici le(s) ELE_ID: ";
							}
							else{
								echo ", ";
							}
							echo $tab_ele_id[$i];
							echo "<input type='hidden' name='tab_ele_id_diff[]' value='".$tab_ele_id[$i]."' />\n";
							//echo "<br />\n";
							// Pour le cas où on est dans la dernière tranche:
							$tab_ele_id_diff[]=$tab_ele_id[$i];
							$cpt++;
						}
					}
				}
			}
			*/



			if(!isset($parcours_diff)){$parcours_diff=1;}
			$parcours_diff++;
			//echo "<input type='hidden' name='parcours_diff' value='$parcours_diff' />\n";

			if(count($tab_ele_id)>20){
				echo "<input type='hidden' name='parcours_diff' value='$parcours_diff' />\n";

				echo "<input type='hidden' name='step' value='3' />\n";
				echo "<p><input type='submit' value='Suite' /></p>\n";

				echo "<script type='text/javascript'>
	/*
	stop='n';
	if(document.getElementById('stop')){
		if(document.getElementById('stop').checked==true){
			stop='y';
		}
	}
	//stop='y'
	if(stop=='n'){
		setTimeout(\"document.forms['formulaire'].submit();\",1000);
	}
	*/
	//test_stop2();
	setTimeout(\"test_stop2()\",3000);
</script>\n";
			}
			else{
				echo "<p>Le parcours des différences est terminé.</p>\n";

				echo "<input type='hidden' name='step' value='4' />\n";
				echo "<p><input type='submit' value='Afficher les différences' /></p>\n";

				// On vide la table dont on va se resservir:
				$sql="TRUNCATE TABLE tempo2;";
				$res0=mysql_query($sql);
			}
			//echo "<input type='hidden' name='is_posted' value='yes' />\n";
			echo "</form>\n";

			//echo "$i: mysql_num_rows(\$test)=".mysql_num_rows($test)."<br />";


			break;
		case 4:
			echo "<h2>Import/mise à jour des élèves</h2>\n";

			if(!isset($tab_ele_id_diff)){
				echo "<p>Aucune différence n'a été trouvée.</p>\n";

				echo "<p>Voulez-vous <a href='".$_SERVER['PHP_SELF']."?is_posted=y&amp;step=9'>passer à la page d'importation/mise à jour des responsables</a></p>\n";
			}
			else{
				echo "<p>".count($tab_ele_id_diff)." élève(s) restant à parcourir (<i>nouveau(x) ou modifié(s)</i>).</p>\n";
				/*
				echo "<p>Liste des différences repérées: <br />\n";
				for($i=0;$i<count($tab_ele_id_diff);$i++){
					echo "\$tab_ele_id_diff[$i]=$tab_ele_id_diff[$i]";
					//echo "<input type='text' name='tab_ele_id_diff[]' value='$tab_ele_id_diff[$i]' />\n";
					echo "<br />\n";
				}
				*/


				// ?????????????????????????????????????????????????????????????????????
				// ?????????????????????????????????????????????????????????????????????
				// ?????????????????????????????????????????????????????????????????????
				// A CE NIVEAU IL FAUDRAIT POUVOIR GERER LE CAS D'UN TROP GRAND NOMBRE DE CORRECTIONS A EFFECTUER...
				// ... LES AFFICHER PAR TRANCHES...
				// APRES VALIDATION, STOCKER DANS UNE TABLE LES ELE_ID POUR LESQUELS temp_gep_import2 DOIT ECRASER eleves ET CEUX CORRESPONDANT A DE NOUVEAUX ELEVES
				// ?????????????????????????????????????????????????????????????????????
				// ?????????????????????????????????????????????????????????????????????
				// ?????????????????????????????????????????????????????????????????????
				//$eff_tranche=min(3,count($tab_ele_id_diff));
				$eff_tranche=10;


				// Les cases validées à l'étape 4 précédente:
				$modif=isset($_POST['modif']) ? $_POST['modif'] : NULL;
				$new=isset($_POST['new']) ? $_POST['new'] : NULL;

				if(isset($modif)){
					for($i=0;$i<count($modif);$i++){
						$sql="INSERT INTO tempo2 SET col1='modif', col2='$modif[$i]'";
						$insert=mysql_query($sql);
					}
				}

				if(isset($new)){
					for($i=0;$i<count($new);$i++){
						$sql="INSERT INTO tempo2 SET col1='new', col2='$new[$i]'";
						$insert=mysql_query($sql);

						// A CE STADE OU AU SUIVANT, IL FAUDRAIT AUSSI PROPOSER D'AFFECTER LES ELEVES DANS LES CLASSES INDIQUEES... AVEC CHOIX DES PERIODES.
						// ET UNE CASE A COCHER POUR:
						// - METTRE DANS TOUS LES GROUPES OU NON
						// OU ALORS PROPOSER LE TABLEAU eleves_options.php
					}
				}

				$tab_ele_id_diff=array_unique($tab_ele_id_diff);

				/*
				if(!isset($parcours_diff)){
					$nblignes=count($tab_ele_id_diff);
				}
				*/
				$nblignes=min($eff_tranche,count($tab_ele_id_diff));



				echo "<form action='".$_SERVER['PHP_SELF']."' name='formulaire' method='post'>\n";
				//==============================
				// AJOUT pour tenir compte de l'automatisation ou non:
				echo "<input type='hidden' name='stop' id='id_form_stop' value='$stop' />\n";
				//==============================

				for($i=$eff_tranche;$i<count($tab_ele_id_diff);$i++){
					//echo "$i: ";
					// BIZARRE: Il semble que certains indices puissent ne pas être affectés???
					// Peut-être à cause du array_unique() -> certains élèves qui ont des modifs de nom, date, INE,... et de régime peuvent être comptés deux fois...
					if(isset($tab_ele_id_diff[$i])){
						echo "<input type='hidden' name='tab_ele_id_diff[]' value='$tab_ele_id_diff[$i]' />\n";
					}
					//echo "<br />\n";
				}



				echo "<p align='center'><input type=submit value='Valider' /></p>\n";
				//echo "<p align='center'><input type=submit value='Enregistrer les modifications' /></p>\n";

				//echo "<table border='1'>\n";
				//echo "<table class='majimport'>\n";
				echo "<table class='boireaus'>\n";
				//echo "<tr style='background-color: rgb(150, 200, 240);'>\n";
				echo "<tr>\n";
				//echo "<td style='text-align: center; font-weight: bold;'>Enregistrer<br />\n";
				echo "<td style='text-align: center; font-weight: bold;'>Modifier<br />\n";

				echo "<a href=\"javascript:modifcase('coche')\">";
				echo "<img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>";
				echo " / ";
				echo "<a href=\"javascript:modifcase('decoche')\">";
				echo "<img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";

				echo "</td>\n";

				echo "<td style='text-align: center; font-weight: bold;'>Statut</td>\n";
				echo "<td style='text-align: center; font-weight: bold;'>elenoet</td>\n";
				echo "<td style='text-align: center; font-weight: bold;'>ele_id</td>\n";
				echo "<td style='text-align: center; font-weight: bold;'>Nom</td>\n";
				echo "<td style='text-align: center; font-weight: bold;'>Prénom</td>\n";
				echo "<td style='text-align: center; font-weight: bold;'>Sexe</td>\n";
				echo "<td style='text-align: center; font-weight: bold;'>Naissance</td>\n";
				echo "<td style='text-align: center; font-weight: bold;'>Doublement</td>\n";
				echo "<td style='text-align: center; font-weight: bold;'>N°NAT</td>\n";
				echo "<td style='text-align: center; font-weight: bold;'>Régime</td>\n";
				echo "<td style='text-align: center; font-weight: bold;'>Classe</td>\n";
				echo "</tr>\n";
				$cpt=0;
				$cpt_modif=0;
				$cpt_new=0;
				$alt=1;
				for($k = 1; ($k < $nblignes+1); $k++){
					$temoin_modif="";
					$temoin_nouveau="";
					//if(!feof($fp)){
						//$ligne = fgets($fp, 4096);

					$w=$k-1;
					$sql="SELECT DISTINCT * FROM temp_gep_import2 WHERE ELE_ID='$tab_ele_id_diff[$w]';";
					$res1=mysql_query($sql);
					if(mysql_num_rows($res1)==0){
						echo "<tr><td>ele_id=$tab_ele_id_diff[$w] non trouvé dans 'temp_gep_import2' ???</td></tr>\n";
					}
					else{
						$lig=mysql_fetch_object($res1);
						$affiche=array();

						$affiche[0]=traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($lig->ELENOM))));
						// IL FAUDRAIT FAIRE ICI LE MEME TRAITEMENT QUE DANS /init_xml/step3.php POUR LES PRENOMS COMPOSéS ET SAISIE DE PLUSIEURS PRéNOMS...
						$affiche[1]=traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($lig->ELEPRE))));
						$affiche[2]=traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($lig->ELESEXE))));
						$affiche[3]=traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($lig->ELEDATNAIS))));
						$affiche[4]=traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($lig->ELENOET))));
						$affiche[5]=traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($lig->ELE_ID))));
						$affiche[6]=traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($lig->ELEDOUBL))));
						$affiche[7]=traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($lig->ELENONAT))));
						$affiche[8]=traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($lig->ELEREG))));
						$affiche[9]=traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($lig->DIVCOD))));

						//if(trim($ligne)!=""){
							//$tabligne=explode(";",$ligne);
							//$affiche=array();
							//for($i = 0; $i < count($tabchamps); $i++) {
							//	$affiche[$i] = traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($tabligne[$tabindice[$i]]))));
							//}

							//$sql="SELECT * FROM eleves WHERE elenoet='$affiche[4]'";
							$sql="SELECT * FROM eleves WHERE (elenoet='$affiche[4]' OR elenoet='".sprintf("%05d",$affiche[4])."')";
							$res1=mysql_query($sql);
							if(mysql_num_rows($res1)>0){
								//$sql="UPDATE eleves SET ele_id='$affiche[5]' WHERE elenoet='$affiche[4]'";

								// FAUT-IL FAIRE LES UPDATE SANS CONTRÔLE OU SIGNALER LES MODIFS SEULEMENT...
								//$sql="UPDATE eleves SET ele_id='$affiche[5]' WHERE elenoet='$affiche[4]'";

								// STOCKER DANS UN TABLEAU ET AFFICHER SEULEMENT LES MODIFS DANS UN PREMIER TEMPS
								// CASES A COCHER POUR VALIDER


								//$res_update=mysql_query($sql);
								//if(!$res_update){
								//	$erreur++;
								//}

								//$eleves[$cpt]

								$lig_ele=mysql_fetch_object($res1);
								//$tabtmp=explode("/",$affiche[3]);
								// $lig_ele->naissance!=$tabtmp[2]."-".$tabtmp[1]."-".$tabtmp[0])||


								$new_date=substr($affiche[3],0,4)."-".substr($affiche[3],4,2)."-".substr($affiche[3],6,2);

								// Des stripslashes() pour les apostrophes dans les noms
								if((stripslashes($lig_ele->nom)!=stripslashes($affiche[0]))||
								(stripslashes($lig_ele->prenom)!=stripslashes($affiche[1]))||
								($lig_ele->sexe!=$affiche[2])||
								($lig_ele->naissance!=$new_date)||
								($lig_ele->no_gep!=$affiche[7])){
									$temoin_modif='y';
									$cpt_modif++;
								}
								else{
									if($lig_ele->ele_id!=$affiche[5]){
										// GROS PROBLEME SI LES elenoet et ele_id ne sont plus des clés primaires
									}
								}

								// TESTER DANS j_eleves_regime pour doublant et regime
								//	table -> $affiche[]
								//	ext. -> 0
								//	d/p -> 2

								//	if ($reg_regime == "0") {$regime = "ext.";}
								//	if ($reg_regime == "2") {$regime = "d/p";}
								//	if ($reg_regime == "3") {$regime = "int.";}
								//	if ($reg_regime == "4") {$regime = "i-e";}


								//	R pour doublant -> O
								//	- pour doublant -> N


								$sql="SELECT * FROM j_eleves_regime WHERE (login='$lig_ele->login')";
								$res2=mysql_query($sql);
								if(mysql_num_rows($res2)>0){
									$tmp_regime="";
									$lig2=mysql_fetch_object($res2);
									//=========================
									// MODIF: boireaus 20071024
									$tmp_new_regime=traite_regime_sconet($affiche[8]);
									//switch($affiche[8]){
									/*
									switch($tmp_new_regime){
										case 0:
											$tmp_regime="ext.";
											break;
										case 2:
											$tmp_regime="d/p";
											break;
										case 3:
											$tmp_regime="int.";
											break;
										case 4:
											$tmp_regime="i-e";
											break;
									}
									*/
									$temoin_pb_regime_inhabituel="n";
									if("$tmp_new_regime"=="ERR"){
										$tmp_regime="d/p";
										$temoin_pb_regime_inhabituel="y";
									}
									else{
										$tmp_regime=$tmp_new_regime;
									}
									//=========================


									if($tmp_regime!=$lig2->regime){
										$temoin_modif='y';
										$cpt_modif++;
									}

									$tmp_doublant="";
									switch($affiche[6]){
										case "O":
											$tmp_doublant="R";
											break;
										case "N":
											$tmp_doublant="-";
											break;
									}
									if($tmp_doublant!=$lig2->doublant){
										$temoin_modif='y';
										$cpt_modif++;
									}
								}
								else{
									// Apparemment, aucune info n'est encore saisie dans j_eleves_regime
								}


								// Rechercher s'il y a un changement de classe?


							}
							else{
								$temoin_nouveau='y';
								$cpt_new++;
								// C'est un nouvel arrivant...

								// AFFICHER ET STOCKER DANS UN TABLEAU...
								// SUR VALIDATION, INSéRER DANS 'eleves' ET PAR LA SUITE AFFECTER DANS DES CLASSES POUR TELLES ET TELLES PERIODES ET COCHER LES OPTIONS POUR TELLES ET TELLES PERIODES.

								// TRANSMETTRE VIA UN FORMULAIRE POUR PROCEDER AUX AJOUTS, ET POUR LES eleves ENCHAINER AVEC LE CHOIX DE CLASSE ET D'OPTIONS
							}


							if($temoin_modif=='y'){
								//echo "<tr style='background-color:green;'>\n";
								//echo "<tr>\n";
								$alt=$alt*(-1);
								/*
								echo "<tr style='background-color:";
								if($alt==1){
									echo "silver";
								}
								else{
									echo "white";
								}
								echo ";'>\n";
								*/
								echo "<tr class='lig$alt'>\n";

								echo "<td style='text-align: center;'>";
								//echo "<input type='checkbox' id='check_".$cpt."' name='modif[]' value='$cpt' />";
								// ELE_ID:
								echo "<input type='checkbox' id='check_".$cpt."' name='modif[]' value='$affiche[5]' />";
								echo "</td>\n";

								//echo "<td style='text-align: center; background-color: lightgreen;'>Modif</td>\n";
								echo "<td class='modif'>Modif</td>\n";

								// ELENOET:
								echo "<td style='text-align: center;'>";
								echo "$affiche[4]";
								echo "<input type='hidden' name='modif_".$cpt."_elenoet' value='$affiche[4]' />\n";
								echo "</td>\n";
								// ELE_ID:
								echo "<td style='text-align: center;'>";
								echo "$affiche[5]";
								echo "<input type='hidden' name='modif_".$cpt."_eleid' value='$affiche[5]' />\n";
								echo "<input type='hidden' name='modif_".$cpt."_login' value='$lig_ele->login' />\n";
								echo "</td>\n";

								//echo "<td style='text-align: center;";
								echo "<td";
								if(stripslashes($lig_ele->nom)!=stripslashes($affiche[0])){
									//echo " background-color:lightgreen;'>";
									echo " class='modif'>";
									if($lig_ele->nom!=''){
										echo stripslashes($lig_ele->nom)." <font color='red'>-&gt;</font>\n";
									}
								}
								else{
									//echo "'>";
									echo ">";
								}
								echo stripslashes($affiche[0]);
								echo "<input type='hidden' name='modif_".$cpt."_nom' value=\"$affiche[0]\" />\n";
								echo "</td>\n";

								//echo "<td style='text-align: center;";
								echo "<td";
								if(stripslashes($lig_ele->prenom)!=stripslashes($affiche[1])){
									//echo " background-color:lightgreen;'>";
									echo " class='modif'>";
									if($lig_ele->prenom!=''){
										echo stripslashes($lig_ele->prenom)." <font color='red'>-&gt;</font>\n";
									}
								}
								else{
									//echo "'>";
									echo ">";
								}
								echo stripslashes($affiche[1]);
								echo "<input type='hidden' name='modif_".$cpt."_prenom' value=\"$affiche[1]\" />\n";
								echo "</td>\n";

								//echo "<td style='text-align: center;";
								echo "<td";
								if($lig_ele->sexe!=$affiche[2]){
									//echo " background-color:lightgreen;'>";
									echo " class='modif'>";
									if($lig_ele->sexe!=''){
										echo "$lig_ele->sexe <font color='red'>-&gt;</font>\n";
									}
								}
								else{
									//echo "'>";
									echo ">";
								}
								echo "$affiche[2]";
								echo "<input type='hidden' name='modif_".$cpt."_sexe' value='$affiche[2]' />\n";
								echo "</td>\n";

								//echo "<td style='text-align: center;";
								echo "<td";
								if($lig_ele->naissance!=$new_date){
									//echo " background-color:lightgreen;'>";
									echo " class='modif'>";
									if($lig_ele->naissance!=''){
										echo "$lig_ele->naissance <font color='red'>-&gt;</font>\n";
									}
								}
								else{
									//echo "'>";
									echo ">";
								}
								echo "$new_date";
								echo "<input type='hidden' name='modif_".$cpt."_naissance' value='$new_date' />\n";
								echo "</td>\n";

								//echo "<td style='text-align: center;'>$affiche[6]</td>\n";
								//echo "<td style='text-align: center;";
								echo "<td";
								//if($tmp_doublant!=$affiche[6]){
								if($tmp_doublant!=$lig2->doublant){
									//echo " background-color:lightgreen;'>";
									echo " class='modif'>";
									if($lig2->doublant!=''){
										echo "$lig2->doublant <font color='red'>-&gt;</font>\n";
									}
								}
								else{
									//echo "'>";
									echo ">";
								}
								//echo "$affiche[6]";
								echo "$tmp_doublant";
								echo "<input type='hidden' name='modif_".$cpt."_doublant' value='$tmp_doublant' />\n";
								echo "</td>\n";


								//echo "<td style='text-align: center;";
								echo "<td";
								if($lig_ele->no_gep!=$affiche[7]){
									//echo " background-color:lightgreen;'>";
									echo " class='modif'>";
									if($lig_ele->no_gep!=''){
										echo "$lig_ele->no_gep <font color='red'>-&gt;</font>\n";
									}
								}
								else{
									//echo "'>";
									echo ">";
								}
								echo "$affiche[7]";
								echo "<input type='hidden' name='modif_".$cpt."_nonat' value='$affiche[7]' />\n";
								echo "</td>\n";

								//echo "<td style='text-align: center;'>$affiche[8]</td>\n";
								//echo "<td style='text-align: center;";
								echo "<td";
								if($tmp_regime!=$lig2->regime){
									//echo " background-color:lightgreen;'>";
									echo " class='modif'>";
									if($lig2->regime!=''){
										echo "$lig2->regime <font color='red'>-&gt;</font>\n";
									}
								}
								else{
									//echo "'>";
									echo ">";
								}
								//echo "$affiche[8]";
								if($temoin_pb_regime_inhabituel=="y"){
									echo "<span style='color:red'>$tmp_regime</span>";
								}
								else{
									echo "$tmp_regime";
								}
								//echo " <span style='color:red'>DEBUG: ".$affiche[8]."</span> ";
								echo "<input type='hidden' name='modif_".$cpt."_regime' value=\"$tmp_regime\" />\n";
								echo "</td>\n";

								//echo "<td style='text-align: center; background-color: white;'>";
								echo "<td style='text-align: center;'>";
								echo "$affiche[9]";
								echo "</td>\n";

								echo "</tr>\n";
							}
							elseif($temoin_nouveau=='y'){
								//echo "<tr style='background-color:yellow;'>\n";
								//echo "<tr>\n";
								$alt=$alt*(-1);
								/*
								echo "<tr style='background-color:";
								if($alt==1){
									echo "silver";
								}
								else{
									echo "white";
								}
								echo ";'>\n";
								*/
								echo "<tr class='lig$alt'>\n";

								//echo "<td style='text-align: center;'><input type='checkbox' id='check_".$cpt."' name='new[]' value='$cpt' /></td>\n";
								echo "<td style='text-align: center;'><input type='checkbox' id='check_".$cpt."' name='new[]' value='$affiche[5]' /></td>\n";

								//echo "<td style='text-align: center; background-color: rgb(150, 200, 240);'>Nouveau</td>\n";
								echo "<td class='nouveau'>Nouveau</td>\n";


								echo "<td style='text-align: center;'>";
								echo "$affiche[4]";
								echo "<input type='hidden' name='new_".$cpt."_elenoet' value='$affiche[4]' />\n";
								echo "</td>\n";

								echo "<td style='text-align: center;'>";
								echo "$affiche[5]";
								echo "<input type='hidden' name='new_".$cpt."_eleid' value='$affiche[5]' />\n";
								echo "</td>\n";

								echo "<td style='text-align: center;'>";
								echo stripslashes($affiche[0]);
								echo "<input type='hidden' name='new_".$cpt."_nom' value=\"$affiche[0]\" />\n";
								echo "</td>\n";

								echo "<td style='text-align: center;'>";
								echo stripslashes($affiche[1]);
								echo "<input type='hidden' name='new_".$cpt."_prenom' value=\"$affiche[1]\" />\n";
								echo "</td>\n";

								echo "<td style='text-align: center;'>";
								echo "$affiche[2]";
								echo "<input type='hidden' name='new_".$cpt."_sexe' value='$affiche[2]' />\n";
								echo "</td>\n";

								$new_date=substr($affiche[3],0,4)."-".substr($affiche[3],4,2)."-".substr($affiche[3],6,2);
								echo "<td style='text-align: center;'>";
								echo "$new_date";
								echo "<input type='hidden' name='new_".$cpt."_naissance' value='$new_date' />\n";
								echo "</td>\n";


								$tmp_doublant="";
								switch($affiche[6]){
									case "O":
										$tmp_doublant="R";
										break;
									case "N":
										$tmp_doublant="-";
										break;
								}

								echo "<td style='text-align: center;'>";
								echo "$tmp_doublant";
								echo "<input type='hidden' name='new_".$cpt."_doublant' value='$tmp_doublant' />\n";
								echo "</td>\n";

								echo "<td style='text-align: center;'>";
								echo "$affiche[7]";
								echo "<input type='hidden' name='new_".$cpt."_nonat' value='$affiche[7]' />\n";
								echo "</td>\n";


								$tmp_regime="";
								//=========================
								// MODIF: boireaus 20071024
								$tmp_new_regime=traite_regime_sconet($affiche[8]);
								//switch($affiche[8]){
								/*
								switch($tmp_new_regime){
									case 0:
										$tmp_regime="ext.";
										break;
									case 2:
										$tmp_regime="d/p";
										break;
									case 3:
										$tmp_regime="int.";
										break;
									case 4:
										$tmp_regime="i-e";
										break;
								}
								*/
								if("$tmp_new_regime"=="ERR"){
									$tmp_regime="d/p";

									echo "<td style='text-align: center;'>\n";
									echo "<span style='color:red'>$tmp_regime</span>";
									//echo " <span style='color:red'>DEBUG: ".$affiche[8]."</span> ";
									echo "<input type='hidden' name='new_".$cpt."_regime' value='$tmp_regime' />\n";
								}
								else{
									$tmp_regime=$tmp_new_regime;

									echo "<td style='text-align: center;'>\n";
									echo "$tmp_regime";
									//echo " <span style='color:red'>DEBUG: ".$affiche[8]."</span> ";
									echo "<input type='hidden' name='new_".$cpt."_regime' value='$tmp_regime' />\n";
								}
								//=========================

								echo "</td>\n";

								echo "<td style='text-align: center;'>";
								echo "$affiche[9]";
								echo "</td>\n";

								echo "</tr>\n";
							}

							$cpt++;
						//}
					}
				}
				echo "</table>\n";
				//echo "<p>On compte $cpt_modif champs modifiés et $cpt_new nouveaux élèves.</p>\n";
				//fclose($fp);

				echo "<script type='text/javascript'>
	function modifcase(mode){
		for(i=0;i<$cpt;i++){
			if(document.getElementById('check_'+i)){
				if(mode=='coche'){
					document.getElementById('check_'+i).checked=true;
				}
				else{
					document.getElementById('check_'+i).checked=false;
				}
			}
		}
	}
</script>\n";

				//echo "<input type='hidden' name='cpt' value='$cpt' />\n";
				if(count($tab_ele_id_diff)>$eff_tranche){
					echo "<input type='hidden' name='step' value='4' />\n";
				}
				else{
					echo "<input type='hidden' name='step' value='5' />\n";
				}

				echo "<p align='center'><input type=submit value='Valider' /></p>\n";
				//echo "<p align='center'><input type=submit value='Enregistrer les modifications' /></p>\n";
				echo "</form>\n";
			}

			break;
		case 5:
			echo "<h2>Import/mise à jour des élèves</h2>\n";

			$modif=isset($_POST['modif']) ? $_POST['modif'] : NULL;
			$new=isset($_POST['new']) ? $_POST['new'] : NULL;

			// Ceux validés dans la dernière phase:
			if(isset($modif)){
				for($i=0;$i<count($modif);$i++){
					$sql="INSERT INTO tempo2 SET col1='modif', col2='$modif[$i]'";
					$insert=mysql_query($sql);
				}
			}

			if(isset($new)){
				for($i=0;$i<count($new);$i++){
					$sql="INSERT INTO tempo2 SET col1='new', col2='$new[$i]'";
					$insert=mysql_query($sql);
				}
			}
			// Si on rafraichit la page, les derniers insérés le sont à plusieurs reprises.
			// Les DISTINCT des requêtes qui suivent permettent de ne pas tenir compte des doublons.


			// CHANGEMENT DE MODE DE FONCTIONNEMENT:
			// On recherche dans tempo2 la liste des ELE_ID correspondant à modif ou new
			// Et on remplit/met à jour 'eleves' avec les enregistrements correspondants de temp_gep_import2

			$erreur=0;
			$cpt=0;
			$sql="SELECT DISTINCT t.* FROM temp_gep_import2 t, tempo2 t2 WHERE t.ELE_ID=t2.col2 AND t2.col1='modif'";
			$res_modif=mysql_query($sql);
			if(mysql_num_rows($res_modif)>0){
				echo "<p>Mise à jour des informations pour ";
				while($lig=mysql_fetch_object($res_modif)){
					//echo "Modif: $lig->ELE_ID : $lig->ELENOM $lig->ELEPRE<br />\n";

					if($cpt>0){echo ", ";}

					$naissance=substr($lig->ELEDATNAIS,0,4)."-".substr($lig->ELEDATNAIS,4,2)."-".substr($lig->ELEDATNAIS,6,2);

					/*
					switch($lig->ELEREG){
						case 0:
							$regime="ext.";
							break;
						case 2:
							$regime="d/p";
							break;
						case 3:
							$regime="int.";
							break;
						case 4:
							$regime="i-e";
							break;
					}
					*/
					$regime=traite_regime_sconet($lig->ELEREG);
					/*
					if("$regime"=="ERR"){
						$regime="d/p";
					}
					*/

					switch($lig->ELEDOUBL){
						case "O":
							$doublant="R";
							break;
						case "N":
							$doublant="-";
							break;
					}

					$sql="UPDATE eleves SET nom='".addslashes($lig->ELENOM)."',
											prenom='".addslashes($lig->ELEPRE)."',
											sexe='".$lig->ELESEXE."',
											naissance='".$naissance."',
											no_gep='".$lig->ELENONAT."'";

					// Je ne pense pas qu'on puisse corriger un ELENOET manquant...
					// Si on fait des imports avec Sconet, l'ELENOET n'est pas vide.
					// Et l'interface ne permet pas actuellement de saisir/corriger un ELE_ID
					$sql_tmp="SELECT elenoet,login FROM eleves WHERE ele_id='$lig->ELE_ID'";
					$res_tmp=mysql_query($sql_tmp);
					$lig_tmp=mysql_fetch_object($res_tmp);
					if($lig_tmp->elenoet==""){
						$sql.=", elenoet='".$lig->ELENOET."' ";
					}
					$login_eleve=$lig_tmp->login;

					$sql.="WHERE ele_id='".$lig->ELE_ID."';";
					$update=mysql_query($sql);
					if($update){
						echo "\n<span style='color:darkgreen;'>";
					}
					else{
						echo "\n<span style='color:red;'>";
						$erreur++;
					}
					//echo "$sql<br />\n";
					echo "$lig->ELEPRE $lig->ELENOM";
					echo "</span>";

					$sql="UPDATE j_eleves_regime SET doublant='$doublant'";
					if("$regime"!="ERR"){
						$sql.=", regime='$regime'";
					}
					$sql.=" WHERE (login='$login_eleve');";
					$res2=mysql_query($sql);
					if(!$res2){
						echo " <span style='color:red;'>(*)</span>";
						$erreur++;
					}

					$cpt++;
				}
				echo "</p>\n";
			}

			$cpt=0;
			$sql="SELECT DISTINCT t.* FROM temp_gep_import2 t, tempo2 t2 WHERE t.ELE_ID=t2.col2 AND t2.col1='new'";
			$res_new=mysql_query($sql);
			if(mysql_num_rows($res_new)>0){

				$sql="CREATE TABLE IF NOT EXISTS temp_ele_classe (
				`ele_id` varchar(40) NOT NULL default '',
				`divcod` varchar(40) NOT NULL default ''
				);";
				$create_table = mysql_query($sql);

				$sql="TRUNCATE TABLE temp_ele_classe;";
				$vide_table = mysql_query($sql);


				echo "<p>Ajout de ";
				while($lig=mysql_fetch_object($res_new)){
					// ON VERIFIE QU'ON N'A PAS DEJA UN ELEVE DE MEME ele_id DANS eleves
					// CELA PEUT ARRIVER SI ON JOUE AVEC F5
					$sql="SELECT 1=1 FROM eleves WHERE ele_id='$lig->ELE_ID'";
					$test=mysql_query($sql);
					if(mysql_num_rows($test)==0){
						//echo "New: $lig->ELE_ID : $lig->ELENOM $lig->ELEPRE<br />";

						if($cpt>0){echo ", ";}

						$naissance=substr($lig->ELEDATNAIS,0,4)."-".substr($lig->ELEDATNAIS,4,2)."-".substr($lig->ELEDATNAIS,6,2);

						/*
						switch($lig->ELEREG){
							case 0:
								$regime="ext.";
								break;
							case 2:
								$regime="d/p";
								break;
							case 3:
								$regime="int.";
								break;
							case 4:
								$regime="i-e";
								break;
						}
						*/
						$regime=traite_regime_sconet($lig->ELEREG);
						// Si le régime est en erreur, on impose 'd/p' comme le moins mauvais choix dans ce cas
						if("$regime"=="ERR"){
							$regime="d/p";
						}

						switch($lig->ELEDOUBL){
							case "O":
								$doublant="R";
								break;
							case "N":
								$doublant="-";
								break;
						}

						$tmp_nom=strtr($lig->ELENOM,"àâäéèêëîïôöùûüçÀÄÂÉÈÊËÎÏÔÖÙÛÜÇ","aaaeeeeiioouuucAAAEEEEIIOOUUUC");
						$tmp_prenom=strtr($lig->ELEPRE,"àâäéèêëîïôöùûüçÀÄÂÉÈÊËÎÏÔÖÙÛÜÇ","aaaeeeeiioouuucAAAEEEEIIOOUUUC");

						// Générer un login...
						$temp1 = strtoupper($tmp_nom);
						$temp1 = preg_replace('/[^0-9a-zA-Z_]/',"", $temp1);
						$temp1 = strtr($temp1, " '-", "___");
						$temp1 = substr($temp1,0,7);
						$temp2 = strtoupper($tmp_prenom);
						$temp2 = preg_replace('/[^0-9a-zA-Z_]/',"", $temp2);
						$temp2 = strtr($temp2, " '-", "___");
						$temp2 = substr($temp2,0,1);
						$login_eleve = $temp1.'_'.$temp2;

						// On teste l'unicité du login que l'on vient de créer
						$k = 2;
						$test_unicite = 'no';
						$temp = $login_eleve;
						while ($test_unicite != 'yes') {
							//$test_unicite = test_unique_e_login($login_eleve,$i);
							$test_unicite = test_unique_login($login_eleve);
							if ($test_unicite != 'yes') {
								$login_eleve = $temp.$k;
								$k++;
							}
						}

						// On ne renseigne plus l'ERENO et on n'a pas l'EMAIL dans temp_gep_import2
						$sql="INSERT INTO eleves SET login='$login_eleve',
												nom='".addslashes($lig->ELENOM)."',
												prenom='".addslashes($lig->ELEPRE)."',
												sexe='".$lig->ELESEXE."',
												naissance='".$naissance."',
												no_gep='".$lig->ELENONAT."',
												elenoet='".$lig->ELENOET."',
												ele_id='".$lig->ELE_ID."';";
						$insert=mysql_query($sql);
						if($insert){
							echo "\n<span style='color:blue;'>";
						}
						else{
							echo "\n<span style='color:red;'>";
							$erreur++;
						}
						//echo "$sql<br />\n";
						echo "$lig->ELEPRE $lig->ELENOM";
						echo "</span>";


						$sql="INSERT INTO j_eleves_regime SET doublant='$doublant',
									regime='$regime',
									login='$login_eleve';";
						$res2=mysql_query($sql);
						if(!$res2){
							echo " <span style='color:red;'>(*)</span>";
							$erreur++;
						}


						// On remplit aussi une table pour l'association avec la classe:
						// On fait le même traitement que dans step2.php
						// (dans step1.php, on a fait le même traitement que pour le remplissage de temp_gep_import2 ici)
						$classe=traitement_magic_quotes(corriger_caracteres($lig->DIVCOD));
						$sql="INSERT INTO temp_ele_classe SET ele_id='".$lig->ELE_ID."', divcod='$classe'";
						$insert=mysql_query($sql);

						$cpt++;
					}
				}
				echo "</p>\n";
			}

			echo "<p><br /></p>\n";




			if($cpt==0){
				// Pas de nouveau:
				switch($erreur){
					case 0:
						echo "<p>Passer à l'étape d'<a href='".$_SERVER['PHP_SELF']."?step=9&amp;stop=y'>import/mise à jour des personnes (<i>responsables</i>) et adresses</a>.</p>\n";
						break;

					case 1:
						echo "<p><font color='red'>Une erreur s'est produite.</font><br />\nVous devriez en chercher la cause avant de passer à l'étape d'<a href='".$_SERVER['PHP_SELF']."?step=9&amp;stop=y'>import/mise à jour des personnes (<i>responsables</i>) et adresses</a>.</p>\n";
						break;

					default:
						echo "<p><font color='red'>$erreur erreurs se sont produites.</font><br />\nVous devriez en chercher la cause avant de passer à l'étape d'<a href='".$_SERVER['PHP_SELF']."?step=9&amp;stop=y'>import/mise à jour des personnes (<i>responsables</i>) et adresses</a>.</p>\n";
						break;
				}
			}
			else{
				switch($erreur){
					case 0:
						echo "<p>Passer à l'étape d'<a href='".$_SERVER['PHP_SELF']."?step=6&amp;stop=y'>affectation des nouveaux élèves dans leurs classes</a>.</p>\n";
						break;

					case 1:
						echo "<p><font color='red'>Une erreur s'est produite.</font><br />\nVous devriez en chercher la cause avant de passer à l'étape d'<a href='".$_SERVER['PHP_SELF']."?step=6&amp;stop=y'>affectation des nouveaux élèves dans leurs classes</a>.</p>\n";
						break;

					default:
						echo "<p><font color='red'>$erreur erreurs se sont produites.</font><br />\nVous devriez en chercher la cause avant de passer à l'étape d'<a href='".$_SERVER['PHP_SELF']."?step=6&amp;stop=y'>affectation des nouveaux élèves dans leurs classes</a>.</p>\n";
						break;
				}
			}

			break;

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

// INSERER ICI: le traitement d'affectation dans les classes des nouveaux élèves...
//              ... et d'affectation dans les options?

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		case 6:
			echo "<h2>Import/mise à jour des élèves</h2>\n";

			echo "<p>Affectation des nouveaux élèves dans leurs classes:</p>\n";

			echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>\n";
			//==============================
			// AJOUT pour tenir compte de l'automatisation ou non:
			echo "<input type='hidden' name='stop' id='id_form_stop' value='$stop' />\n";
			//==============================

			// DISTINCT parce qu'on peut avoir plusieurs enregistrements d'un même élève dans 'temp_ele_classe' si on a joué avec F5.
			// ERREUR: Il faut régler le problème plus haut parce que si on insère plusieurs fois l'élève, il est plusieurs fois dans 'eleves' avec des logins différents.
			$sql="SELECT DISTINCT e.*,t.divcod FROM temp_ele_classe t,eleves e WHERE t.ele_id=e.ele_id ORDER BY e.nom,e.prenom";
			$res_ele=mysql_query($sql);

			//echo mysql_num_rows($res_ele);

			if(mysql_num_rows($res_ele)==0){
				echo "<p>Bizarre: il semble que la table 'temp_ele_classe' ne contienne aucun identifiant de nouvel élève.</p>\n";
				// FAUT-IL SAUTER A UNE AUTRE ETAPE?
			}
			else{

				$sql="SELECT DISTINCT num_periode FROM periodes ORDER BY num_periode DESC LIMIT 1";
				$res_per=mysql_query($sql);

				if(mysql_num_rows($res_per)==0){
					echo "<p>Bizarre: il semble qu'aucune période ne soit encore définie.</p>\n";
					// FAUT-IL SAUTER A UNE AUTRE ETAPE?
				}
				else{

					$lig_per=mysql_fetch_object($res_per);
					$max_per=$lig_per->num_periode;
					echo "<input type='hidden' name='maxper' value='$max_per' />\n";

					echo "<p align='center'><input type='submit' value='Valider' /></p>\n";

					//echo "<table class='majimport'>\n";
					echo "<table class='boireaus'>\n";
					echo "<tr>\n";
					echo "<th rowspan='2'>Elève</th>\n";
					echo "<th rowspan='2'>Classe</th>\n";
					echo "<th colspan='$max_per'>Périodes</th>\n";

					$chaine_coche="";
					$chaine_decoche="";
					for($i=1;$i<=$max_per;$i++){
						$chaine_coche.="modif_case($i,\"col\",true);";
						$chaine_decoche.="modif_case($i,\"col\",false);";
					}

					//echo "<th rowspan='2'>&nbsp;</th>\n";
					echo "<th rowspan='2'>\n";
					echo "<a href='javascript:$chaine_coche'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
					echo "<a href='javascript:$chaine_decoche'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
					echo "</th>\n";
					echo "</tr>\n";

					echo "<tr>\n";
					for($i=1;$i<=$max_per;$i++){
						echo "<th>\n";
						echo "Période $i\n";
						echo "<br />\n";
						echo "<a href='javascript:modif_case($i,\"col\",true)'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
						echo "<a href='javascript:modif_case($i,\"col\",false)'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
						echo "</th>\n";

						$chaine_coche.="modif_case($i,\"col\",true);";
						$chaine_decoche.="modif_case($i,\"col\",false);";
					}
					echo "</tr>\n";





					$cpt=0;
					$alt=-1;
					while($lig_ele=mysql_fetch_object($res_ele)){
						$alt=$alt*(-1);
						/*
						echo "<tr style='background-color:";
						if($alt==1){
							echo "silver";
						}
						else{
							echo "white";
						}
						echo ";'>\n";
						*/
						echo "<tr class='lig$alt'>\n";

						echo "<td>";
						echo "$lig_ele->nom $lig_ele->prenom";
						echo "<input type='hidden' name='login_eleve[$cpt]' value='".$lig_ele->login."' />\n";
						echo "</td>\n";

						$sql="SELECT c.id FROM classes c WHERE c.classe='$lig_ele->divcod';";
						$res_classe=mysql_query($sql);
						if(mysql_num_rows($res_classe)>0){
							$lig_classe=mysql_fetch_object($res_classe);

							echo "<td>";
							echo $lig_ele->divcod;
							echo "<input type='hidden' name='id_classe[$cpt]' value='$lig_classe->id' />\n";
							echo "</td>\n";

							$sql="SELECT p.num_periode FROM periodes p, classes c
													WHERE p.id_classe=c.id AND
															c.classe='$lig_ele->divcod'
													ORDER BY num_periode;";
							$res_per=mysql_query($sql);
							$cpt_periode=1;
							while($lig_per=mysql_fetch_object($res_per)){
								echo "<td>\n";
								echo "<input type='checkbox' name='periode_".$cpt."_[$cpt_periode]' id='case".$cpt."_".$cpt_periode."'  value='$cpt_periode' />\n";
								echo "</td>\n";
								$cpt_periode++;
							}
							for($i=$cpt_periode;$i<=$max_per;$i++){
								echo "<td style='background-color: darkgray;'>\n";
								echo "</td>\n";
							}
						}
						else{
							// La classe n'a pas été identifiée
							$sql="SELECT DISTINCT id,classe FROM classes ORDER BY classe";
							$res_classe=mysql_query($sql);
							echo "<td>\n";
							if(mysql_num_rows($res_classe)>0){
								echo "<select name='id_classe[$cpt]'>\n";
								echo "<option value=''>---</option>\n";
								while($lig_classe=mysql_fetch_object($res_classe)){
									echo "<option value='$lig_classe->id'>$lig_classe->classe</option>\n";
								}
								echo "</select>\n";
							}
							echo "</td>\n";

							for($i=1;$i<=$max_per;$i++){
								echo "<td style='background-color: orange;'>\n";
								echo "<input type='checkbox' name='periode_".$cpt."_[$i]' value='$i' />\n";
								echo "</td>\n";
							}
						}

						echo "<td>\n";
						echo "<a href='javascript:modif_case($cpt,\"lig\",true)'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
						echo "<a href='javascript:modif_case($cpt,\"lig\",false)'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
						echo "</td>\n";

						echo "</tr>\n";
						$cpt++;
					}
					echo "</table>\n";
				}
			}


			echo "<script type='text/javascript' language='javascript'>
	function modif_case(rang,type,statut){
		// type: col ou lig
		// rang: le numéro de la colonne ou de la ligne
		// statut: true ou false
		if(type=='col'){
			for(k=0;k<$cpt;k++){
				if(document.getElementById('case'+k+'_'+rang)){
					document.getElementById('case'+k+'_'+rang).checked=statut;
				}
			}
		}
		else{
			for(k=1;k<=$max_per;k++){
				if(document.getElementById('case'+rang+'_'+k)){
					document.getElementById('case'+rang+'_'+k).checked=statut;
				}
			}
		}
		changement();
	}
</script>\n";

			echo "<p><br /></p>\n";

			//echo "<input type='hidden' name='step' value='6_1' />\n";
			echo "<input type='hidden' name='step' value='7' />\n";
			echo "<p align='center'><input type='submit' value='Valider' /></p>\n";

			echo "</form>\n";
			break;

		//case "6_1":
		case 7:
			echo "<h2>Import/mise à jour des élèves</h2>\n";

			$login_eleve=isset($_POST['login_eleve']) ? $_POST['login_eleve'] : NULL;
			$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : NULL;
			$maxper=isset($_POST['maxper']) ? $_POST['maxper'] : NULL;

			if(!isset($login_eleve)){
				echo "<p>Vous n'avez affecté aucun élève.</p>\n";
			}
			else{
				echo "<p>\n";
				for($i=0;$i<count($login_eleve);$i++){
					$sql="SELECT nom, prenom FROM eleves WHERE login='$login_eleve[$i]'";
					//echo $sql."<br />";
					$res_ele=mysql_query($sql);
					if(mysql_num_rows($res_ele)>0){
						$lig_ele=mysql_fetch_object($res_ele);

						echo "Affectation de $lig_ele->prenom $lig_ele->nom ";

						//if(is_int($id_classe[$i])){
						if(is_numeric($id_classe[$i])){
							$tab_periode=isset($_POST['periode_'.$i.'_']) ? $_POST['periode_'.$i.'_'] : NULL;

							if(isset($tab_periode)){
								$sql="SELECT classe FROM classes WHERE id='$id_classe[$i]'";
								$test=mysql_query($sql);
								if(mysql_num_rows($test)>0){
									$lig_classe=mysql_fetch_object($test);

									echo "en $lig_classe->classe pour ";
									if(count($tab_periode)==1){
										echo "la période ";
									}
									else{
										echo "les périodes ";
									}

									$cpt_per=0;
									for($j=1;$j<=$maxper;$j++){
										if(isset($tab_periode[$j])){
											//if(is_int($tab_periode[$j])){
											if(is_numeric($tab_periode[$j])){
												$sql="SELECT 1=1 FROM periodes WHERE id_classe='$id_classe[$i]' AND num_periode='$tab_periode[$j]'";
												$test=mysql_query($sql);

												if(mysql_num_rows($test)>0){
													// VERIFICATION: Si on fait F5 pour rafraichir la page, on risque d'insérer plusieurs fois le même enregistrement.
													$sql="SELECT 1=1 FROM j_eleves_classes WHERE login='$login_eleve[$i]' AND
																						id_classe='$id_classe[$i]' AND
																						periode='$tab_periode[$j]'";
													$test=mysql_query($sql);

													if(mysql_num_rows($test)==0){
														$sql="INSERT INTO j_eleves_classes SET login='$login_eleve[$i]',
																							id_classe='$id_classe[$i]',
																							periode='$tab_periode[$j]',
																							rang='0'";
														$insert=mysql_query($sql);
													}
													if($cpt_per>0){echo ", ";}
													echo "$j";
													$cpt_per++;
												}
											}
										}
									}
								}
								else{
									echo "dans aucune classe (<i>identifiant de classe invalide</i>).";
								}
							}
							else{
								echo "dans aucune classe (<i>aucune période cochée</i>).";
							}
						}
						else{
							echo "dans aucune classe (<i>identifiant de classe invalide</i>).";
						}
						echo "<br />\n";
					}
				}
				echo "</p>\n";
			}

			echo "<p>Passer à l'étape d'<a href='".$_SERVER['PHP_SELF']."?step=8&amp;stop=y'>inscription des nouveaux élèves dans les groupes</a>.</p>\n";

			break;

		case 8:

			echo "<h2>Import/mise à jour élève</h2>\n";

			$opt_eleve=isset($_POST['opt_eleve']) ? $_POST['opt_eleve'] : NULL;
			$eleve=isset($_POST['eleve']) ? $_POST['eleve'] : NULL;

			echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>\n";
			//==============================
			// AJOUT pour tenir compte de l'automatisation ou non:
			echo "<input type='hidden' name='stop' id='id_form_stop' value='$stop' />\n";
			//==============================

			if(!isset($opt_eleve)){
				$sql="SELECT e.* FROM eleves e, temp_ele_classe t WHERE t.ele_id=e.ele_id ORDER BY e.nom,e.prenom";
				$res_ele=mysql_query($sql);

				if(mysql_num_rows($res_ele)==0){
					// CA NE DEVRAIT PAS ARRIVER

					echo "<p>Il semble qu'il n'y ait aucun élève à affecter.</p>\n";

					// METTRE LE LIEN VERS L'ETAPE SUIVANTE

					echo "</form>\n";
					require("../lib/footer.inc.php");
					die();
				}

				$lig_ele=mysql_fetch_object($res_ele);
				$nom_eleve=$lig_ele->nom;
				$prenom_eleve=$lig_ele->prenom;
				$login_eleve=$lig_ele->login;
				$ele_id=$lig_ele->ele_id;

				while($lig_ele=mysql_fetch_object($res_ele)){
					echo "<input type='hidden' name='eleve[]' value='$lig_ele->ele_id' />\n";
				}

			}
			else{
				$login_eleve=isset($_POST['login_eleve']) ? $_POST['login_eleve'] : NULL;
				$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : NULL;
				$sql="SELECT * FROM periodes WHERE id_classe='$id_classe' ORDER BY num_periode";
				$res_per=mysql_query($sql);
				$nb_periode=mysql_num_rows($res_per)+1;

				$cpe_resp=isset($_POST['cpe_resp']) ? $_POST['cpe_resp'] : NULL;

				if(isset($cpe_resp)){
					if("$cpe_resp"!=""){
						// Par précaution:
						$sql="DELETE FROM j_eleves_cpe WHERE e_login='$login_eleve' AND cpe_login='$cpe_resp'";
						$nettoyage_cpe=mysql_query($sql);

						$sql="INSERT INTO j_eleves_cpe SET e_login='$login_eleve', cpe_login='$cpe_resp'";
						$insert_cpe=mysql_query($sql);
					}
				}

				$pp_resp=isset($_POST['pp_resp']) ? $_POST['pp_resp'] : NULL;

				if(isset($pp_resp)){
					if("$pp_resp"!=""){
						// Par précaution:
						$sql="DELETE FROM j_eleves_professeurs WHERE login='$login_eleve' AND professeur='$pp_resp' AND id_classe='$id_classe';";
						// DEBUG:
						//echo "$sql<br />\n";
						$nettoyage_pp=mysql_query($sql);

						$sql="INSERT INTO j_eleves_professeurs SET login='$login_eleve', professeur='$pp_resp', id_classe='$id_classe';";
						// DEBUG:
						//echo "$sql<br />\n";
						$insert_pp=mysql_query($sql);
					}
				}
				/*
				$cpt=1;
				while($lig_per=mysql_fetch_object($res_per)){
					$nom_periode[$cpt]=$lig_per->nom_periode;
					$cpt++;
				}
				*/

				$j = 1;
				while ($j < $nb_periode) {
					$call_group = mysql_query("SELECT DISTINCT g.id, g.name FROM groupes g, j_groupes_classes jgc WHERE (g.id = jgc.id_groupe and jgc.id_classe = '" . $id_classe ."') ORDER BY jgc.priorite, g.name");
					$nombre_ligne = mysql_num_rows($call_group);
					$i=0;
					while ($i < $nombre_ligne) {
						$id_groupe = mysql_result($call_group, $i, "id");
						$nom_groupe = mysql_result($call_group, $i, "name");
						$id_group[$j] = $id_groupe."_".$j;
						$test_query = mysql_query("SELECT 1=1 FROM j_eleves_groupes WHERE (" .
								"id_groupe = '" . $id_groupe . "' and " .
								"login = '" . $login_eleve . "' and " .
								"periode = '" . $j . "')");
						$test = mysql_num_rows($test_query);
						if (isset($_POST[$id_group[$j]])) {
							if ($test == 0) {
								$req = mysql_query("INSERT INTO j_eleves_groupes SET id_groupe = '" . $id_groupe . "', login = '" . $login_eleve . "', periode = '" . $j ."'");
							}
						} else {
							$test1 = mysql_query("SELECT 1=1 FROM matieres_notes WHERE (id_groupe = '".$id_groupe."' and login = '".$login_eleve."' and periode = '$j')");
							$nb_test1 = mysql_num_rows($test1);
							$test2 = mysql_query("SELECT 1=1 FROM matieres_appreciations WHERE (id_groupe = '".$id_groupe."' and login = '".$login_eleve."' and periode = '$j')");
							$nb_test2 = mysql_num_rows($test2);
							if (($nb_test1 != 0) or ($nb_test2 != 0)) {
								$msg = $msg."--> Impossible de supprimer cette option pour l'élève $login_eleve car des moyennes ou appréciations ont déjà été rentrées pour le groupe $nom_groupe pour la période $j ! Commencez par supprimer ces données !<br />";
							} else {
								if ($test != "0")  $req = mysql_query("DELETE FROM j_eleves_groupes WHERE (login='".$login_eleve."' and id_groupe='".$id_groupe."' and periode = '".$j."')");
							}
						}
						$i++;
					}
					$j++;
				}



				if(isset($eleve)){
					$sql="SELECT e.* FROM eleves e WHERE e.ele_id='$eleve[0]'";
					$res_ele=mysql_query($sql);

					$lig_ele=mysql_fetch_object($res_ele);
					$nom_eleve=$lig_ele->nom;
					$prenom_eleve=$lig_ele->prenom;
					$login_eleve=$lig_ele->login;
					$ele_id=$lig_ele->ele_id;

					for($i=1;$i<count($eleve);$i++){
						echo "<input type='hidden' name='eleve[]' value='$eleve[$i]' />\n";
					}
				}
				else{
					echo "<p>Tous les élèves ont été parcourus.</p>\n";

					// METTRE LE LIEN VERS L'ETAPE SUIVANTE

					echo "<input type='hidden' name='step' value='9' />\n";
					echo "<p><input type='submit' value='Etape suivante: Responsables' /></p>\n";

					echo "</form>\n";
					require("../lib/footer.inc.php");
					die();
				}
			}

			echo "<input type='hidden' name='opt_eleve' value='y' />\n";

			$sql="SELECT DISTINCT c.id,c.classe FROM classes c,j_eleves_classes jec
									WHERE jec.id_classe=c.id AND
										jec.login='$login_eleve'";
			$res_classe=mysql_query($sql);

			if(mysql_num_rows($res_classe)==0){
				echo "<p>$prenom_eleve $nom_eleve n'est dans aucune classe.</p>\n";

				// PASSER AU SUIVANT...

				echo "<input type='hidden' name='step' value='8' />\n";
				echo "<p><input type='submit' value='Suite' /></p>\n";

				echo "</form>\n";
			}
			else{
				$lig_classe=mysql_fetch_object($res_classe);
				$id_classe=$lig_classe->id;

				$sql="SELECT * FROM periodes WHERE id_classe='$id_classe' ORDER BY num_periode";
				$res_per=mysql_query($sql);

				if(mysql_num_rows($res_per)==0){
					echo "<p>L'élève $prenom_eleve $ele_nom_eleve serait dans une classe sans période???</p>\n";

					// PASSER AU SUIVANT...
					echo "</form>\n";
				}
				else{

					echo "<p><b>$prenom_eleve $nom_eleve</b> (<i>$lig_classe->classe</i>)</p>\n";

					//===========================
					// A FAIRE: boireaus 20071129
					//          Ajouter l'association avec le PP et le CPE
					$sql="SELECT login, nom, prenom FROM utilisateurs WHERE statut='cpe' ORDER BY nom, prenom;";
					$res_cpe=mysql_query($sql);

					echo "<table border='0'>\n";
					if(mysql_num_rows($res_cpe)>0){
						echo "<tr><td>CPE responsable: </td><td><select name='cpe_resp'>\n";
						echo "<option value=''>---</option>\n";
						while($lig_cpe=mysql_fetch_object($res_cpe)){
							echo "<option value='$lig_cpe->login'>$lig_cpe->nom $lig_cpe->prenom</option>\n";
						}
						echo "</select>\n";
						echo "</td>\n";
						echo "</tr>\n";
					}

					$sql="SELECT DISTINCT u.login, u.nom, u.prenom FROM utilisateurs u, j_eleves_professeurs jep
										WHERE jep.id_classe='$id_classe' AND
												jep.professeur=u.login
										ORDER BY u.nom, u.prenom;";
					$res_pp=mysql_query($sql);
					if(mysql_num_rows($res_cpe)>0){
						echo "<tr><td>".ucfirst(getSettingValue('gepi_prof_suivi')).": </td><td><select name='pp_resp'>\n";
						echo "<option value=''>---</option>\n";
						while($lig_pp=mysql_fetch_object($res_pp)){
							echo "<option value='$lig_pp->login'>$lig_pp->nom $lig_pp->prenom</option>\n";
						}
						echo "</select>\n";
						echo "</td>\n";
						echo "</tr>\n";
					}
					echo "</table>\n";
					echo "<p>&nbsp;</p>\n";

					//===========================

					$nb_periode=mysql_num_rows($res_per)+1;

					$cpt=1;
					while($lig_per=mysql_fetch_object($res_per)){
						$nom_periode[$cpt]=$lig_per->nom_periode;
						$cpt++;
					}


					echo "<p>Affectation dans les groupes de l'élève $prenom_eleve $nom_eleve (<i>$lig_classe->classe</i>)</p>\n";
					echo "<p align='center'><input type='submit' value='Valider' /></p>\n";

					echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";
					echo "<input type='hidden' name='login_eleve' value='$login_eleve' />\n";


					$sql="SELECT DISTINCT g.id, g.name FROM groupes g,
															j_groupes_classes jgc
									WHERE (g.id = jgc.id_groupe AND
											jgc.id_classe = '" . $id_classe ."')
									ORDER BY jgc.priorite, g.name";
					$call_group=mysql_query($sql);
					$nombre_ligne=mysql_num_rows($call_group);

					//echo "<table border = '1' cellpadding='5' cellspacing='0'>\n";
					//echo "<table class='majimport' cellpadding='5' cellspacing='0'>\n";
					echo "<table class='boireaus' cellpadding='5' cellspacing='0'>\n";
					//echo "<tr align='center'><td><b>Matière</b></td>";
					echo "<tr align='center'><th><b>Matière</b></th>\n";

					$j = 1;
					$chaine_coche="";
					$chaine_decoche="";
					while ($j < $nb_periode) {
						//echo "<td><b>".$nom_periode[$j]."</b><br />\n";
						echo "<th><b>".$nom_periode[$j]."</b><br />\n";
						echo "<a href='javascript:modif_case($j,\"col\",true)'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
						echo "<a href='javascript:modif_case($j,\"col\",false)'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
						//echo "</td>";
						echo "</th>\n";

						$chaine_coche.="modif_case($j,\"col\",true);";
						$chaine_decoche.="modif_case($j,\"col\",false);";

						$j++;
					}
					//echo "<td>&nbsp;</td>\n";
					//echo "<th>&nbsp;</th>\n";
					echo "<th>\n";

					echo "<a href='javascript:$chaine_coche'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
					echo "<a href='javascript:$chaine_decoche'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";

					echo "</th>\n";
					echo "</tr>\n";

					$nb_erreurs=0;
					$i=0;
					$alt=-1;
					while ($i < $nombre_ligne) {
						$id_groupe = mysql_result($call_group, $i, "id");
						$nom_groupe = mysql_result($call_group, $i, "name");

						$alt=$alt*(-1);
						/*
						echo "<tr style='background-color:";
						if($alt==1){
							echo "silver";
						}
						else{
							echo "white";
						}
						echo ";'>\n";
						*/
						echo "<tr class='lig$alt'>\n";
						echo "<td>".$nom_groupe;
						echo "</td>\n";
						$j = 1;
						while ($j < $nb_periode) {
							$test=mysql_query("SELECT 1=1 FROM j_eleves_groupes WHERE (" .
									"id_groupe = '" . $id_groupe . "' and " .
									"login = '" . $login_eleve . "' and " .
									"periode = '" . $j . "')");

							$sql="SELECT * FROM j_eleves_classes WHERE login='$login_eleve' AND periode='$j' AND id_classe='$id_classe'";
							// CA NE VA PAS... SUR LES GROUPES A REGROUPEMENT, IL FAUT PRENDRE DES PRECAUTIONS...
							$res_test_class_per=mysql_query($sql);
							if(mysql_num_rows($res_test_class_per)==0){
								if (mysql_num_rows($test) == "0") {
									echo "<td>&nbsp;</td>\n";
								}
								else{
									$sql="SELECT DISTINCT id_classe FROM j_groupes_classes WHERE id_groupe='$id_groupe'";
									$res_grp=mysql_query($sql);
									$temoin="";
									while($lig_clas=mysql_fetch_object($res_grp)){
										$sql="SELECT 1=1 FROM j_eleves_classes WHERE id_classe='$lig_clas->id_classe' AND login='$login_eleve' AND periode='$j'";
										$res_test_ele=mysql_query($sql);
										if(mysql_num_rows($res_test_ele)==1){
											$sql="SELECT classe FROM classes WHERE id='$lig_clas->id_classe'";
											$res_tmp=mysql_query($sql);
											$lig_tmp=mysql_fetch_object($res_tmp);
											$clas_tmp=$lig_tmp->classe;

											$temoin=$clas_tmp;
										}
									}

									if($temoin!=""){
										echo "<td><center>".$temoin."<input type=hidden name=".$id_groupe."_".$j." value='checked' /></center></td>\n";
									}
									else{
										$msg_erreur="Cette case est validée et ne devrait pas l être. Validez le formulaire pour corriger.";
										echo "<td><center><a href='#' alt='$msg_erreur' title='$msg_erreur'><font color='red'>ERREUR</font></a></center></td>\n";
										$nb_erreurs++;
									}
								}
							}
							else{

								/*
								// Un autre test à faire:
								// Si l'élève est resté dans le groupe alors qu'il n'est plus dans cette classe pour la période
								$sql="SELECT 1=1 FROM j_eleves_classes WHERE id_classe='$id_classe' AND periode='$j' AND login='$login_eleve'";
								*/

								//=========================
								// MODIF: boireaus
								if (mysql_num_rows($test) == "0") {
									//echo "<td><center><input type=checkbox name=".$id_groupe."_".$j." /></center></td>\n";
									echo "<td><center><input type=checkbox id=case".$i."_".$j." name=".$id_groupe."_".$j." onchange='changement();' /></center></td>\n";
								} else {
									//echo "<td><center><input type=checkbox name=".$id_groupe."_".$j." CHECKED /></center></td>\n";
									echo "<td><center><input type=checkbox id=case".$i."_".$j." name=".$id_groupe."_".$j." onchange='changement();' checked /></center></td>\n";
								}
								//=========================
							}
							$j++;
						}
						//=========================
						// AJOUT: boireaus
						echo "<td>\n";
						//echo "<input type='button' name='coche_lig_$i' value='C' onClick='modif_case($i,\"lig\",true)' />/\n";
						//echo "<input type='button' name='decoche_lig_$i' value='D' onClick='modif_case($i,\"lig\",false)' />\n";
						echo "<a href='javascript:modif_case($i,\"lig\",true)'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
						echo "<a href='javascript:modif_case($i,\"lig\",false)'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
						echo "</td>\n";
						//=========================
						echo "</tr>\n";
						$i++;
					}
					echo "</table>\n";


					echo "<script type='text/javascript' language='javascript'>
	function modif_case(rang,type,statut){
		// type: col ou lig
		// rang: le numéro de la colonne ou de la ligne
		// statut: true ou false
		if(type=='col'){
			for(k=0;k<$nombre_ligne;k++){
				if(document.getElementById('case'+k+'_'+rang)){
					document.getElementById('case'+k+'_'+rang).checked=statut;
				}
			}
		}
		else{
			for(k=1;k<$nb_periode;k++){
				if(document.getElementById('case'+rang+'_'+k)){
					document.getElementById('case'+rang+'_'+k).checked=statut;
				}
			}
		}
		changement();
	}
</script>\n";

					echo "<input type='hidden' name='step' value='8' />\n";
					echo "<p align='center'><input type='submit' value='Valider' /></p>\n";
					echo "</form>\n";
				}

			}


			break;

		case 9:
			echo "<h2>Import/mise à jour des responsables</h2>\n";

			echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
			//==============================
			// AJOUT pour tenir compte de l'automatisation ou non:
			//echo "<input type='hidden' name='stop' id='id_form_stop' value='$stop' />\n";
			//==============================
			echo "<p>Veuillez fournir le fichier ResponsablesAvecAdresses.xml:<br />\n";
			echo "<input type=\"file\" size=\"80\" name=\"responsables_xml_file\" /><br />\n";
			echo "<input type='hidden' name='step' value='10' />\n";
			//echo "<input type='hidden' name='is_posted' value='yes' />\n";

			//==============================
			// AJOUT pour tenir compte de l'automatisation ou non:
			//echo "<input type='hidden' name='stop' id='id_form_stop' value='$stop' />\n";
			echo "<input type='checkbox' name='stop' id='id_form_stop' value='y' ";
			if("$stop"=="y"){echo "checked ";}
			echo "/><label for='id_form_stop' style='cursor: pointer;'> Désactiver le mode automatique.</label></p>\n";
			//==============================

			echo "<p><input type='submit' value='Valider' /></p>\n";
			echo "</form>\n";

			echo "<p><br /></p>\n";

			echo "<p><i>NOTE:</i></p>\n";
			echo "<blockquote>\n";
			echo "<p>Après une phase d'analyse des différences, les différences seront affichées et des cases à cocher seront proposées pour valider les modifications.</p>\n";
			echo "<p>Les différences concernant les personnes, puis les adresses sont recherchées.<br />Ensuite seulement, il vous est proposé de valider les modifications concernant les personnes et adresses.</p>\n";
			echo "<p>Un troisième parcours des différences est ensuite effectué pour rechercher les changements dans les associations responsables/élèves.</p>\n";
			echo "</blockquote>\n";

			require("../lib/footer.inc.php");
			die();

			break;
		case "10":
			echo "<h2>Import/mise à jour des responsables</h2>\n";

			$post_max_size=ini_get('post_max_size');
			$upload_max_filesize=ini_get('upload_max_filesize');
			$max_execution_time=ini_get('max_execution_time');
			$memory_limit=ini_get('memory_limit');


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

					//$sql="CREATE TABLE IF NOT EXISTS resp_pers (
					$sql="CREATE TABLE IF NOT EXISTS temp_resp_pers_import (
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

					$sql="TRUNCATE TABLE temp_resp_pers_import;";
					//$sql="TRUNCATE TABLE resp_pers;";
					$vide_table = mysql_query($sql);

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
							$sql="INSERT INTO temp_resp_pers_import SET ";
							//$sql="INSERT INTO resp_pers SET ";
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

						echo "<p><br /></p>\n";

						if ($nb_err != 0) {
							echo "<p>Lors de l'enregistrement des données PERSONNES, il y a eu $nb_err erreurs. Essayez de trouvez la cause de l'erreur et recommencez la procédure avant de passer à l'étape suivante.</p>\n";
						} else {
							echo "<p>L'importation des personnes (responsables) dans la base GEPI a été effectuée avec succès (".$stat." enregistrements au total).</p>\n";

							echo "<script type='text/javascript'>
	/*
	stop='n';
	if(document.getElementById('stop')){
		if(document.getElementById('stop').checked==true){
			stop='y';
		}
	}
	if(stop=='n'){
		setTimeout(\"document.location.replace('".$_SERVER['PHP_SELF']."?step=11')\",2000);
	}
	*/
	setTimeout(\"test_stop('11')\",3000);
</script>\n";
						}

						//echo "<p>$stat enregistrement(s) ont été inséré(s) dans la table 'temp_resp_pers_import'.</p>\n";
						//echo "<p>$stat enregistrement(s) ont été inséré(s) dans la table 'resp_pers'.</p>\n";

						echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=11&amp;stop=y'>Suite</a></p>\n";


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

			break;
		case "11":
			echo "<h2>Import/mise à jour des responsables</h2>\n";

			$dest_file="../temp/".$tempdir."/responsables.xml";
			$fp=fopen($dest_file,"r");
			if(!$fp){
				echo "<p>Le XML responsables n'a pas l'air présent dans le dossier temporaire.<br />Auriez-vous sauté une étape???</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
			else{

				//$sql="CREATE TABLE IF NOT EXISTS responsables2 (
				$sql="CREATE TABLE IF NOT EXISTS temp_responsables2_import (
						`ele_id` varchar(10) NOT NULL,
						`pers_id` varchar(10) NOT NULL,
						`resp_legal` varchar(1) NOT NULL,
						`pers_contact` varchar(1) NOT NULL
						);";
				$create_table = mysql_query($sql);

				$sql="TRUNCATE TABLE temp_responsables2_import;";
				//$sql="TRUNCATE TABLE responsables2;";
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
					$sql="INSERT INTO temp_responsables2_import SET ";
					//$sql="INSERT INTO responsables2 SET ";
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

				echo "<p><br /></p>\n";

				if ($nb_err!=0) {
					echo "<p>Lors de l'enregistrement des données de RESPONSABLES, il y a eu $nb_err erreurs. Essayez de trouvez la cause de l'erreur et recommencez la procédure avant de passer à l'étape suivante.</p>\n";
				}
				else {
					echo "<p>L'importation des relations eleves/responsables dans la base GEPI a été effectuée avec succès (".$stat." enregistrements au total).</p>\n";

					echo "<script type='text/javascript'>
	/*
	stop='n';
	if(document.getElementById('stop')){
		if(document.getElementById('stop').checked==true){
			stop='y';
		}
	}
	if(stop=='n'){
		setTimeout(\"document.location.replace('".$_SERVER['PHP_SELF']."?step=12')\",2000);
	}
	*/
	setTimeout(\"test_stop('12')\",3000);
</script>\n";
				}

				//echo "<p>$stat enregistrement(s) ont été inséré(s) dans la table 'temp_responsables2_import'.</p>\n";

				echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=12&amp;stop=y'>Suite</a></p>\n";

				require("../lib/footer.inc.php");
				die();
			}

			break;
		case "12":
			echo "<h2>Import/mise à jour des responsables</h2>\n";

			$dest_file="../temp/".$tempdir."/responsables.xml";
			$fp=fopen($dest_file,"r");
			if(!$fp){
				echo "<p>Le XML responsables n'a pas l'air présent dans le dossier temporaire.<br />Auriez-vous sauté une étape???</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
			else{

				//$sql="CREATE TABLE IF NOT EXISTS resp_adr (
				$sql="CREATE TABLE IF NOT EXISTS temp_resp_adr_import (
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

				$sql="TRUNCATE TABLE temp_resp_adr_import;";
				//$sql="TRUNCATE TABLE resp_adr;";
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
					$sql="INSERT INTO temp_resp_adr_import SET ";
					//$sql="INSERT INTO resp_adr SET ";
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

				echo "<p><br /></p>\n";

				if ($nb_err != 0) {
					echo "<p>Lors de l'enregistrement des données ADRESSES des responsables, il y a eu $nb_err erreurs. Essayez de trouvez la cause de l'erreur et recommencez la procédure avant de passer à l'étape suivante.</p>\n";
				} else {
					echo "<p>L'importation des adresses de responsables dans la base GEPI a été effectuée avec succès (".$stat." enregistrements au total).</p>\n";

					echo "<script type='text/javascript'>
	/*
	stop='n';
	if(document.getElementById('stop')){
		if(document.getElementById('stop').checked==true){
			stop='y';
		}
	}
	if(stop=='n'){
		setTimeout(\"document.location.replace('".$_SERVER['PHP_SELF']."?step=13')\",2000);
	}
	*/
	setTimeout(\"test_stop('13')\",3000);
</script>\n";
				}
				//echo "<p>$stat enregistrement(s) ont été mis à jour dans la table 'temp_resp_adr_import'.</p>\n";

				echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=13&amp;stop=y'>Suite</a></p>\n";

				require("../lib/footer.inc.php");
				die();
			}
			break;
		case 13:
			// On va commencer les comparaisons...
			// - resp_pers
			// - resp_adr en rappelant la liste des personnes auxquelles l'adresse est rattachée...
			//     . enchainer avec une proposition de nettoyage des adresses qui ne sont plus rattachées à personne
			// - responsables2:
			//     . Nouvelles responsabilités
			//     . Responsabilités supprimées

			echo "<h2>Import/mise à jour des responsables</h2>\n";

			if(file_exists("../temp/".$tempdir."/responsables.xml")) {
				echo "<p>Suppression du fichier responsables.xml... ";
				if(unlink("../temp/".$tempdir."/responsables.xml")){
					echo "réussie.</p>\n";
				}
				else{
					echo "<font color='red'>Echec!</font> Vérifiez les droits d'écriture sur le serveur.</p>\n";
				}
			}

			echo "<h3>Section PERSONNES</h3>\n";

			echo "<form action='".$_SERVER['PHP_SELF']."' name='formulaire' method='post'>\n";
			//==============================
			// AJOUT pour tenir compte de l'automatisation ou non:
			echo "<input type='hidden' name='stop' id='id_form_stop' value='$stop' />\n";
			//==============================

			if(!isset($parcours_diff)){
				echo "<p>On va commencer les comparaisons...</p>\n";

				$sql="select pers_id from temp_resp_pers_import;";
				$res1=mysql_query($sql);

				$nb_pers=mysql_num_rows($res1);

				echo "<p>Les ".$nb_pers." personnes responsables vont être parcourus par tranches de 20 à la recherche de différences.</p>\n";

				echo "<p>Parcours de la tranche <b>1</b>.</p>\n";

				flush();

				$tab_pers_id=array();

				$cpt=0;
				$chaine_nouveaux="";
				while($lig=mysql_fetch_object($res1)){
					/*
					$sql="SELECT 1=1 FROM resp_pers rp, responsables2 r, eleves e";
					if(){

					}
					else{
					*/
						// Est-ce une nouvelle personne responsable?
						$sql="SELECT 1=1 FROM resp_pers rp, temp_resp_pers_import t WHERE rp.pers_id=t.pers_id AND t.pers_id='$lig->pers_id'";
						$test=mysql_query($sql);
						if(mysql_num_rows($test)==0){
							// On ne va considérer comme nouveau responsable qu'une personne associée à un élève effectivement accepté dans la table 'eleves':
							$sql="SELECT 1=1 FROM temp_resp_pers_import trp,
													temp_responsables2_import tr,
													eleves e
											WHERE trp.pers_id='$lig->pers_id' AND
													trp.pers_id=tr.pers_id AND
													tr.ele_id=e.ele_id";
							$test=mysql_query($sql);
							if(mysql_num_rows($test)>0){
								if($cpt>0){$chaine_nouveaux.=", ";}
								$chaine_nouveaux.=$lig->pers_id;
								echo "<input type='hidden' name='tab_pers_id_diff[]' value='$lig->pers_id' />\n";
								$cpt++;
							}
						}
						else{
							//$tab_pers_id[]=$lig->pers_id;
							$tab_pers_id[]=$lig->pers_id;
						}
					//}
				}
				flush();

				if($chaine_nouveaux==1){
					echo "<p>L'identifiant PERS_ID d'un nouveau responsable a été trouvé: $chaine_nouveaux</p>\n";
				}
				elseif($chaine_nouveaux>1){
					echo "<p>Les identifiants PERS_ID de $cpt nouveaux responsables ont été trouvés: $chaine_nouveaux</p>\n";
				}

				$nb_parcours=ceil(count($tab_pers_id)/20);
			}
			else{
				echo "<p>Parcours de la tranche <b>$parcours_diff/$nb_parcours</b>.</p>\n";

				if(isset($tab_pers_id_diff)){
					if(count($tab_pers_id_diff)==1){
						echo "<p>L'identifiant PERS_ID pour lequel une ou des différences ont déjà été repérées, est: \n";
					}
					else{
						echo "<p>Les identifiants PERS_ID, pour lesquels une ou des différences ont déjà été repérées, sont: \n";
					}
					for($i=0;$i<count($tab_pers_id_diff);$i++){
						if($i>0){echo ", ";}
						echo $tab_pers_id_diff[$i];
						//echo "$i: ";
						echo "<input type='hidden' name='tab_pers_id_diff[]' value='$tab_pers_id_diff[$i]' />\n";
						//echo "<br />\n";
					}
					echo "</p>\n";
				}
			}

			echo "<input type='hidden' name='nb_parcours' value='$nb_parcours' />\n";


			/*
			// On construit la chaine des 20 PERS_ID retenus pour la requête à venir:
			$chaine="";
			for($i=0;$i<min(20,count($tab_pers_id));$i++){
				if($i>0){$chaine.=" OR ";}
				$chaine.="rp.pers_id='$tab_pers_id[$i]'";
			}
			*/

			//echo "\$chaine=$chaine<br />\n";

			// Liste des pers_id restant à parcourir:
			for($i=20;$i<count($tab_pers_id);$i++){
				//echo "$i: ";
				echo "<input type='hidden' name='tab_pers_id[]' value='$tab_pers_id[$i]' />\n";
				//echo "<br />\n";
			}

			/*
			$sql="SELECT rp.pers_id FROM resp_pers rp, temp_resp_pers_import t
							WHERE rp.pers_id=t.pers_id AND
									(
										rp.nom!=t.nom OR
										rp.prenom!=t.prenom OR
										rp.civilite!=t.civilite OR
										rp.tel_pers!=t.tel_pers OR
										rp.tel_port!=t.tel_port OR
										rp.tel_prof!=t.tel_prof OR
										rp.adr_id!=t.adr_id
									)
									AND ($chaine)
									";
			//echo "$sql<br />\n";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)>0){
				echo "<p>Une ou des différences ont été trouvées dans la tranche étudiée à cette phase.";
				echo "<br />\n";
				echo "En voici le(s) pers_id: ";
				$cpt=0;
				while($lig=mysql_fetch_object($test)){
					if($cpt>0){echo ", ";}
					echo $lig->pers_id;
					echo "<input type='hidden' name='tab_pers_id_diff[]' value='$lig->pers_id' />\n";
					//echo "<br />\n";
					// Pour le cas où on est dans la dernière tranche:
					$tab_pers_id_diff[]=$lig->pers_id;
					$cpt++;
				}
			}
			*/


			$cpt=0;
			for($i=0;$i<min(20,count($tab_pers_id));$i++){
				$sql="SELECT rp.pers_id FROM resp_pers rp, temp_resp_pers_import t
								WHERE rp.pers_id=t.pers_id AND
										(
											rp.nom!=t.nom OR
											rp.prenom!=t.prenom OR
											rp.civilite!=t.civilite OR
											rp.tel_pers!=t.tel_pers OR
											rp.tel_port!=t.tel_port OR
											rp.tel_prof!=t.tel_prof OR
											rp.adr_id!=t.adr_id
										)
										AND rp.pers_id='$tab_pers_id[$i]';";
				//echo "$sql<br />\n";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)>0){
					if($cpt==0){
						echo "<p>Une ou des différences ont été trouvées dans la tranche étudiée à cette phase.";
						echo "<br />\n";
						echo "En voici le(s) pers_id: ";
					}
					else{
						echo ", ";
					}
					$lig=mysql_fetch_object($test);
					echo $lig->pers_id;
					echo "<input type='hidden' name='tab_pers_id_diff[]' value='$lig->pers_id' />\n";
					//echo "<br />\n";
					// Pour le cas où on est dans la dernière tranche:
					$tab_pers_id_diff[]=$lig->pers_id;
					$cpt++;
				}
			}

			if(!isset($parcours_diff)){$parcours_diff=1;}
			$parcours_diff++;
			//echo "<input type='hidden' name='parcours_diff' value='$parcours_diff' />\n";

			if(count($tab_pers_id)>20){
				echo "<input type='hidden' name='parcours_diff' value='$parcours_diff' />\n";
				echo "<input type='hidden' name='step' value='13' />\n";
				echo "<p><input type='submit' value='Suite' /></p>\n";

				echo "<script type='text/javascript'>
	/*
	stop='n';
	if(document.getElementById('stop')){
		if(document.getElementById('stop').checked==true){
			stop='y';
		}
	}
	if(stop=='n'){
		setTimeout(\"document.forms['formulaire'].submit();\",1000);
	}
	*/
	setTimeout(\"test_stop2()\",3000);
</script>\n";
			}
			else{
				echo "<p>Le parcours des différences concernant les personnes est terminé.</p>\n";

				// On stocke dans la table tempo2 la liste des pers_id pour lesquels un changement a eu lieu:
				$sql="TRUNCATE TABLE tempo2;";
				$res0=mysql_query($sql);

				for($i=0;$i<count($tab_pers_id_diff);$i++){
					$sql="INSERT INTO tempo2 SET col1='pers_id', col2='$tab_pers_id_diff[$i]'";
					$insert=mysql_query($sql);
				}

				echo "<input type='hidden' name='step' value='14' />\n";
				//echo "<p><input type='submit' value='Afficher les différences' /></p>\n";
				echo "<p><input type='submit' value=\"Parcourir les différences d'adresses\" /></p>\n";

				echo "<script type='text/javascript'>
	/*
	stop='n';
	if(document.getElementById('stop')){
		if(document.getElementById('stop').checked==true){
			stop='y';
		}
	}
	if(stop=='n'){
		setTimeout(\"document.forms['formulaire'].submit();\",5000);
	}
	*/
	setTimeout(\"test_stop2()\",3000);
</script>\n";
			}
			//echo "<input type='hidden' name='is_posted' value='yes' />\n";
			echo "</form>\n";


			break;
		case 14:
			// DEBUG:
			//echo "step=$step<br />";

			echo "<h2>Import/mise à jour des responsables</h2>\n";

			echo "<h3>Section ADRESSES</h3>\n";

			echo "<form action='".$_SERVER['PHP_SELF']."' name='formulaire' method='post'>\n";
			//==============================
			// AJOUT pour tenir compte de l'automatisation ou non:
			echo "<input type='hidden' name='stop' id='id_form_stop' value='$stop' />\n";
			//==============================

			if(!isset($parcours_diff)){
				echo "<p>On va commencer les comparaisons...</p>\n";

				$sql="select adr_id from temp_resp_adr_import;";
				$res1=mysql_query($sql);

				$nb_adr=mysql_num_rows($res1);

				echo "<p>Les ".$nb_adr." adresses responsables vont être parcourus par tranches de 20 à la recherche de différences.</p>\n";

				echo "<p>Parcours de la tranche <b>1</b>.</p>\n";

				// On construit une barre de 100 cellules pour faire les changements de couleurs au fur et à mesure du traitement et donner une indication de progression:
				echo "<table align='center' style='border: 1px solid black;'>\n";
				echo "<tr>\n";
				for($i=0;$i<100;$i++){
					echo "<td id='td_$i' style='width:1px;'";
					echo " cellspacing='0' cellpadding='0'";
					echo "></td>\n";
				}
				echo "</tr>\n";
				echo "</table>\n";
				//die();

				//$pc=round($nb_adr/100);


				flush();

				$tab_adr_id=array();

				// +++++++++++++++++++++++++++++++++
				// +++++++++++++++++++++++++++++++++
				// +++++++++++++++++++++++++++++++++
				// A AMELIORER/MODIFIER... VOIR EN METTANT LES PARAMETRES PAR DEFAUT D'apache SI CA PASSE
				//max_execution_time = 30     ; Maximum execution time of each script, in seconds
				//memory_limit = 8M      ; Maximum amount of memory a script
				// Cela a l'air de passer avec 1016 adresses anoncées...
				// IL SEMBLE QUE CETTE PHASE SOIT TRES LONGUE
				// +++++++++++++++++++++++++++++++++
				// +++++++++++++++++++++++++++++++++
				// +++++++++++++++++++++++++++++++++



				$time1=time();
				$cpt=0;
				$chaine_nouveaux="";
				$compteur=0;
				while($lig=mysql_fetch_object($res1)){
					//$time1=time();
					// Est-ce une nouvelle adresse responsable?
					$sql="SELECT 1=1 FROM resp_adr ra WHERE ra.adr_id='$lig->adr_id'";
					$test1=mysql_query($sql);

					$sql="SELECT 1=1 FROM resp_adr ra, temp_resp_adr_import t WHERE ra.adr_id=t.adr_id AND t.adr_id='$lig->adr_id'";
					$test2=mysql_query($sql);
					if((mysql_num_rows($test1)==0)||(mysql_num_rows($test2)==0)){
						// On ne va considérer une nouvelle adresse responsable que si la personne est associée à un élève effectivement accepté dans la table 'eleves':
						$sql="SELECT 1=1 FROM temp_resp_adr_import tra,
												temp_resp_pers_import trp,
												temp_responsables2_import tr,
												eleves e
										WHERE tra.adr_id='$lig->adr_id' AND
												trp.adr_id=tra.adr_id AND
												trp.pers_id=tr.pers_id AND
												tr.ele_id=e.ele_id";
						$test=mysql_query($sql);
						if(mysql_num_rows($test)>0){
							//echo "<span style='color:blue;'> $lig->adr_id </span>";
							if($cpt>0){$chaine_nouveaux.=", ";}
							$chaine_nouveaux.=$lig->adr_id;
							echo "<input type='hidden' name='tab_adr_id_diff[]' value='$lig->adr_id' />\n";
							$cpt++;

							// Peut-être mettre:
							$tab_adr_id_diff[]=$lig->adr_id;
							// Est-ce qu'il peut arriver qu'on ne fasse pas un tour dans la boucle de formulaire...
						}
						/*
						else{
							echo "<span style='color:red;'> $lig->adr_id </span>";
						}
						*/
					}
					else{
						// Ce n'est pas une nouvelle adresse.
						// Il faut comparer l'entrée de temp_resp_adr_import avec ce qui se trouve dans resp_adr...

						//echo "<span style='color:green;'> $lig->adr_id </span>";
						//$tab_adr_id[]=$lig->adr_id;
						$tab_adr_id[]=$lig->adr_id;
					}
					/*
					$time2=time();
					$delta=$time2-$time1;
					echo $delta;
					flush();
					*/

					echo "<script type='text/javascript'>\n";
					for($i=0;$i<round(100*$compteur/$nb_adr);$i++){
						echo "document.getElementById('td_$i').style.backgroundColor='red';\n";
					}
					echo "</script>\n";

					$compteur++;
				}
				$time2=time();
				$delta=$time2-$time1;
				echo "Durée: ".$delta." secondes.<br />\n";

				flush();

				//if($chaine_nouveaux==1){
				if($cpt==1){
					echo "<p>L'identifiant ADR_ID d'une nouvelle adresse responsable a été trouvé: $chaine_nouveaux</p>\n";
				}
				//elseif($chaine_nouveaux>1){
				elseif($cpt>1){
					echo "<p>Les identifiants ADR_ID de $cpt nouvelles adresses responsables ont été trouvés: $chaine_nouveaux</p>\n";
				}

				$nb_parcours=ceil(count($tab_adr_id)/20);
			}
			else{
				echo "<p>Parcours de la tranche <b>$parcours_diff/$nb_parcours</b>.</p>\n";

				if(isset($tab_adr_id_diff)){
					if(count($tab_adr_id_diff)==1){
						echo "<p>L'identifiant ADR_ID pour lequel une ou des différences ont déjà été repérées, est: \n";
					}
					else{
						echo "<p>Les identifiants ADR_ID, pour lesquels une ou des différences ont déjà été repérées, sont: \n";
					}
					$chaine_adr_id="";
					for($i=0;$i<count($tab_adr_id_diff);$i++){
						//if($i>0){echo ", ";}
						//echo $tab_adr_id_diff[$i];
						if($i>0){$chaine_adr_id.=", ";}
						$chaine_adr_id.=$tab_adr_id_diff[$i];
						//echo "$i: ";
						echo "<input type='hidden' name='tab_adr_id_diff[]' value='$tab_adr_id_diff[$i]' />\n";
						//echo "<br />\n";
					}
					echo $chaine_adr_id;
					echo "</p>\n";
				}
			}

			echo "<input type='hidden' name='nb_parcours' value='$nb_parcours' />\n";



			/*
			// On construit la chaine des 20 adr_ID retenus pour la requête à venir:
			$info_nouvelles_adresses="";
			$chaine="";
			for($i=0;$i<min(20,count($tab_adr_id));$i++){
				if($i>0){$chaine.=" OR ";}
				$chaine.="ra.adr_id='$tab_adr_id[$i]'";

				$sql="SELECT 1=1 FROM resp_adr WHERE adr_id='$tab_adr_id[$i]';";
				// DEBUG:
				//echo "$sql<br />\n";
				$res_nouvelle_adr=mysql_query($sql);
				if(mysql_num_rows($res_nouvelle_adr)==0){
					// Cet identifiant d'adresse n'existait pas.
					// DEBUG:
					//echo "<input type='text' name='tab_adr_id_diff[]' value='".$tab_adr_id[$i]."' />\n";
					echo "<input type='hidden' name='tab_adr_id_diff[]' value='".$tab_adr_id[$i]."' />\n";
					$tab_adr_id_diff[]=$tab_adr_id[$i];
					if($info_nouvelles_adresses!=""){$info_nouvelles_adresses.=", ";}
					$info_nouvelles_adresses.=$tab_adr_id[$i];
				}
			}
			*/


			//echo "\$chaine=$chaine<br />\n";

			// Liste des adr_id restant à parcourir:
			// DEBUG:
			//echo "<p>Liste des adr_id restant à parcourir:<br />";
			for($i=20;$i<count($tab_adr_id);$i++){
				//echo "$i: ";
				// DEBUG:
				echo "<input type='hidden' name='tab_adr_id[]' value='$tab_adr_id[$i]' />\n";
				//echo "<input type='text' name='tab_adr_id[]' value='$tab_adr_id[$i]' />\n";
				//echo "<br />\n";
			}

			/*
			$sql="SELECT ra.adr_id FROM resp_adr ra, temp_resp_adr_import t
							WHERE ra.adr_id=t.adr_id AND
									(
										ra.adr1!=t.adr1 OR
										ra.adr2!=t.adr2 OR
										ra.adr3!=t.adr3 OR
										ra.adr4!=t.adr4 OR
										ra.cp!=t.cp OR
										ra.commune!=t.commune OR
										ra.pays!=t.pays
									)
									AND ($chaine)
									";
			//echo "$sql<br />\n";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)>0){
				echo "<p>Une ou des différences ont été trouvées dans la tranche étudiée à cette phase.";
				echo "<br />\n";
				echo "En voici le(s) adr_id: ";
				$cpt=0;
				$chaine_adr="";
				while($lig=mysql_fetch_object($test)){
					if($cpt>0){$chaine_adr.=", ";}
					$chaine_adr.=$lig->adr_id;
					echo "<input type='hidden' name='tab_adr_id_diff[]' value='$lig->adr_id' />\n";
					//echo "<br />\n";
					// Pour le cas où on est dans la dernière tranche:
					$tab_adr_id_diff[]=$lig->adr_id;
					$cpt++;
				}
				echo $chaine_adr;
			}
			*/


			$cpt=0;
			$info_nouvelles_adresses="";
			for($i=0;$i<min(20,count($tab_adr_id));$i++){
				$sql="SELECT 1=1 FROM resp_adr WHERE adr_id='$tab_adr_id[$i]';";
				// DEBUG:
				//echo "$sql<br />\n";
				$res_nouvelle_adr=mysql_query($sql);
				if(mysql_num_rows($res_nouvelle_adr)==0){
					// Cet identifiant d'adresse n'existait pas.
					// DEBUG:
					//echo "<input type='text' name='tab_adr_id_diff[]' value='".$tab_adr_id[$i]."' />\n";
					echo "<input type='hidden' name='tab_adr_id_diff[]' value='".$tab_adr_id[$i]."' />\n";
					$tab_adr_id_diff[]=$tab_adr_id[$i];

					if($info_nouvelles_adresses!=""){$info_nouvelles_adresses.=", ";}
					$info_nouvelles_adresses.=$tab_adr_id[$i];
				}
				else{

					$sql="SELECT ra.adr_id FROM resp_adr ra, temp_resp_adr_import t
									WHERE ra.adr_id=t.adr_id AND
											(
												ra.adr1!=t.adr1 OR
												ra.adr2!=t.adr2 OR
												ra.adr3!=t.adr3 OR
												ra.adr4!=t.adr4 OR
												ra.cp!=t.cp OR
												ra.commune!=t.commune OR
												ra.pays!=t.pays
											)
											AND ra.adr_id='$tab_adr_id[$i]';";
					//echo "$sql<br />\n";
					$test=mysql_query($sql);
					if(mysql_num_rows($test)>0){
						if($cpt==0){
							echo "<p>Une ou des différences ont été trouvées dans la tranche étudiée à cette phase.";
							echo "<br />\n";
							echo "En voici le(s) adr_id: ";
						}
						else{
							echo ", ";
						}
						$lig=mysql_fetch_object($test);
						echo $lig->adr_id;
						echo "<input type='hidden' name='tab_adr_id_diff[]' value='$lig->adr_id' />\n";
						//echo "<br />\n";
						// Pour le cas où on est dans la dernière tranche:
						$tab_adr_id_diff[]=$lig->adr_id;
						$cpt++;
						flush();
					}
				}
			}

			if($info_nouvelles_adresses!=""){
				echo "<p>Une ou des nouvelles adresses ont été trouvées.<br />\n";
				echo "En voici le(s) adr_id: ";
				echo $info_nouvelles_adresses;
				echo "</p>\n";
			}



			if(!isset($parcours_diff)){$parcours_diff=1;}
			$parcours_diff++;
			//echo "<input type='hidden' name='parcours_diff' value='$parcours_diff' />\n";

			// DEBUG:
			//echo "count(\$tab_adr_id)=".count($tab_adr_id)."<br />\n";

			if(count($tab_adr_id)>20){
				echo "<input type='hidden' name='parcours_diff' value='$parcours_diff' />\n";
				echo "<input type='hidden' name='step' value='14' />\n";
				echo "<p><input type='submit' value='Suite' /></p>\n";

				echo "<script type='text/javascript'>
	/*
	stop='n';
	if(document.getElementById('stop')){
		if(document.getElementById('stop').checked==true){
			stop='y';
		}
	}
	if(stop=='n'){
		setTimeout(\"document.forms['formulaire'].submit();\",1000);
	}
	*/
	setTimeout(\"test_stop2()\",3000);
</script>\n";

			}
			else{
				echo "<p>Le parcours des différences concernant les adresses est terminé.<br />Vous allez pouvoir contrôler les différences concernant les personnes et les adresses.</p>\n";

				/*
				// On stocke dans la table tempo2 la liste des pers_id pour lesquels un changement a eu lieu:
				//$sql="TRUNCATE TABLE tempo2;";
				//$res0=mysql_query($sql);
				// On complète la table tempo2...

				for($i=0;$i<count($tab_pers_id_diff);$i++){
					$sql="INSERT INTO tempo2 SET col1='pers_id', col2='$tab_pers_id_diff[$i]'";
					$insert=mysql_query($sql);
				}
				*/

				// DEBUG:
				//echo "count(\$tab_adr_id_diff)=".count($tab_adr_id_diff)."<br />\n";

				for($i=0;$i<count($tab_adr_id_diff);$i++){
					$sql="SELECT DISTINCT pers_id FROM resp_pers WHERE adr_id='$tab_adr_id_diff[$i]'";
					$test=mysql_query($sql);

					if(mysql_num_rows($test)>0){
						while($lig=mysql_fetch_object($test)){
							$sql="INSERT INTO tempo2 SET col1='pers_id', col2='$lig->pers_id'";
							$insert=mysql_query($sql);
						}
					}

					$sql="SELECT DISTINCT pers_id FROM temp_resp_pers_import WHERE adr_id='$tab_adr_id_diff[$i]'";
					$test=mysql_query($sql);

					if(mysql_num_rows($test)>0){
						while($lig=mysql_fetch_object($test)){
							$sql="INSERT INTO tempo2 SET col1='pers_id', col2='$lig->pers_id'";
							$insert=mysql_query($sql);
						}
					}
					// Les doublons importent peu.
					// On fait des recherches en DISTINCT par la suite.
				}

				echo "<input type='hidden' name='step' value='15' />\n";
				echo "<p><input type='submit' value='Afficher les différences' /></p>\n";

				/*
				echo "<script type='text/javascript'>
	stop='n';
	if(document.getElementById('stop')){
		if(document.getElementById('stop').checked==true){
			stop='y';
		}
	}
	if(stop=='n'){
		setTimeout(\"document.forms['formulaire'].submit();\",5000);
	}
</script>\n";
				*/
			}
			echo "</form>\n";


			break;
		case 15:
			echo "<h2>Import/mise à jour des responsables</h2>\n";


			echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>\n";
			//==============================
			// AJOUT pour tenir compte de l'automatisation ou non:
			echo "<input type='hidden' name='stop' id='id_form_stop' value='$stop' />\n";
			//==============================

			if(!isset($parcours_diff)){
				$sql="SELECT 1=1 FROM tempo2 WHERE col1='pers_id';";
				$test=mysql_query($sql);

				//echo "<p>".count($tab_pers_id_diff)." personnes...</p>\n";

				//echo "<p>".mysql_num_rows($test)." personnes/adresses modifiées requièrent votre attention.</p>\n";
				$nb_tmp_modif=mysql_num_rows($test);
				if($nb_tmp_modif==0){
					echo "<p>Aucune modification ne requiert votre attention (<i>personnes/adresses</i>).</p>\n";
				}
				elseif($nb_tmp_modif==1){
					echo "<p>Une personne/adresse modifiée requiert votre attention.</p>\n";
				}
				else{
					echo "<p>$nb_tmp_modif personnes/adresses modifiées requièrent votre attention.</p>\n";
				}

				//echo "<input type='hidden' name='total_pers_diff' value='".count($tab_pers_id_diff)."' />\n";
				echo "<input type='hidden' name='total_pers_diff' value='".mysql_num_rows($test)."' />\n";
			}
			else{
				if(isset($valid_pers_id)){
					// On modifie la valeur de col1 pour les pers_id confirmés pour ne pas les re-parcourir:
					for($i=0;$i<count($valid_pers_id);$i++){
						$sql="UPDATE tempo2 SET col1='pers_id_confirm' WHERE col2='$valid_pers_id[$i]';";
						$update=mysql_query($sql);
					}

					for($i=0;$i<count($liste_pers_id);$i++){
						if(!in_array($liste_pers_id[$i],$valid_pers_id)){
							$sql="UPDATE tempo2 SET col1='pers_id_refus' WHERE col2='$liste_pers_id[$i]';";
							$update=mysql_query($sql);
						}
					}
				}
				else{
					if(isset($liste_pers_id)){
						for($i=0;$i<count($liste_pers_id);$i++){
							$sql="UPDATE tempo2 SET col1='pers_id_refus' WHERE col2='$liste_pers_id[$i]';";
							$update=mysql_query($sql);
						}
					}
				}

				$sql="SELECT 1=1 FROM tempo2 WHERE col1='pers_id';";
				$test=mysql_query($sql);

				echo "<p>".mysql_num_rows($test)." personnes/adresses restantes sur un total de $total_pers_diff.</p>\n";
				echo "<input type='hidden' name='total_pers_diff' value='".$total_pers_diff."' />\n";
			}

			echo "<input type='hidden' name='parcours_diff' value='y' />\n";

			// Il faut encore parcourir les changements d'adresses...
			// ... et faire une première tranche de corrections?
			// Ou alors on le fait séparemment...



			$eff_tranche=20;

			$sql="SELECT DISTINCT col2 FROM tempo2 WHERE col1='pers_id' LIMIT $eff_tranche";
			$res1=mysql_query($sql);

			if(mysql_num_rows($res1)>0){

				//echo "<p align='center'><input type='submit' value='Poursuivre' /></p>\n";
				echo "<p align='center'><input type='submit' value='Valider' /></p>\n";

				// Affichage du tableau
				//echo "<table border='1'>\n";
				//echo "<table class='majimport'>\n";
				echo "<table class='boireaus'>\n";

				/*
				echo "<tr>\n";
				echo "<td style='text-align: center; font-weight: bold;'>Enregistrer<br />\n";
				echo "<a href=\"javascript:modifcase('coche')\">";
				echo "<img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>";
				echo " / ";
				echo "<a href=\"javascript:modifcase('decoche')\">";
				echo "<img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
				echo "</td>\n";

				echo "<td style='text-align:center;'>&nbsp;</td>\n";

				echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>pers_id</td>\n";
				echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Nom</td>\n";
				echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Prénom</td>\n";
				echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Civilité</td>\n";
				echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Téléphone / mel</td>\n";
				//echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Tel perso</td>\n";
				//echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Tel port</td>\n";
				//echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Tel prof</td>\n";
				//echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Mel</td>\n";

				// Pour l'adresse, on teste si l'adr_id a changé:
				// - si oui on indique le changement en piochant la nouvelle adresse dans temp_resp_adr_import2
				// - sinon on indique 'Identifiant d adresse inchangé'
				echo "<td style='text-align:center; font-weight:bold; background-color: #FAFABE;'>Adresse</td>\n";
				echo "</tr>\n";
				*/

				$ligne_entete_tableau="<tr>\n";
				//$ligne_entete_tableau.="<td style='text-align: center; font-weight: bold;'>Enregistrer<br />\n";
				$ligne_entete_tableau.="<td style='text-align: center; font-weight: bold;'>Modifier<br />\n";
				//$ligne_entete_tableau.="<th style='text-align: center; font-weight: bold;'>Enregistrer<br />\n";
				$ligne_entete_tableau.="<a href=\"javascript:modifcase('coche')\">";
				$ligne_entete_tableau.="<img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>";
				$ligne_entete_tableau.=" / ";
				$ligne_entete_tableau.="<a href=\"javascript:modifcase('decoche')\">";
				$ligne_entete_tableau.="<img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
				$ligne_entete_tableau.="</td>\n";
				//$ligne_entete_tableau.="</th>\n";

				//$ligne_entete_tableau.="<td style='text-align:center; background-color: rgb(150, 200, 240);'>Statut</td>\n";
				$ligne_entete_tableau.="<td style='text-align:center; font-weight: bold;'>Statut</td>\n";
				//$ligne_entete_tableau.="<th style='text-align:center; font-weight: bold;'>Statut</th>\n";

				$ligne_entete_tableau.="<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>pers_id</td>\n";
				$ligne_entete_tableau.="<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Nom</td>\n";
				$ligne_entete_tableau.="<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Prénom</td>\n";
				$ligne_entete_tableau.="<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Civilité</td>\n";
				$ligne_entete_tableau.="<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Téléphone / mel</td>\n";
				//$ligne_entete_tableau.="<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Tel perso</td>\n";
				//$ligne_entete_tableau.="<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Tel port</td>\n";
				//$ligne_entete_tableau.="<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Tel prof</td>\n";
				//$ligne_entete_tableau.="<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Mel</td>\n";

				// Pour l'adresse, on teste si l'adr_id a changé:
				// - si oui on indique le changement en piochant la nouvelle adresse dans temp_resp_adr_import2
				// - sinon on indique 'Identifiant d adresse inchangé'
				$ligne_entete_tableau.="<td style='text-align:center; font-weight:bold; background-color: #FAFABE;'>Adresse</td>\n";
				$ligne_entete_tableau.="</tr>\n";

				echo $ligne_entete_tableau;

				$alt=1;
				$cpt=0;
				while($lig1=mysql_fetch_object($res1)){
				//for($i=0;$i<count($pers_modif);$i++){
					//$pers_id=$pers_modif[$i];
					$pers_id=$lig1->col2;

					// Est-ce un nouveau ou une modif?
					$sql="SELECT * FROM resp_pers WHERE pers_id='$pers_id'";
					$res_pers1=mysql_query($sql);
					$nouveau=0;
					if(mysql_num_rows($res_pers1)==0){
						$nouveau=1;

						$nom1="";
						$prenom1="";
						$civilite1="";
						$tel_pers1="";
						$tel_port1="";
						$tel_prof1="";
						$mel1="";
						$adr_id1="";
					}
					else{
						$lig_pers1=mysql_fetch_object($res_pers1);

						$nom1=$lig_pers1->nom;
						$prenom1=$lig_pers1->prenom;
						$civilite1=$lig_pers1->civilite;
						$tel_pers1=$lig_pers1->tel_pers;
						$tel_port1=$lig_pers1->tel_port;
						$tel_prof1=$lig_pers1->tel_prof;
						$mel1=$lig_pers1->mel;
						$adr_id1=$lig_pers1->adr_id;
					}

					//echo "<tr>\n";
					$alt=$alt*(-1);
					/*
					echo "<tr style='background-color:";
					if($alt==1){
						echo "silver";
					}
					else{
						echo "white";
					}
					echo ";'>\n";
					*/
					echo "<tr class='lig$alt'>\n";

					//echo "<td style='text-align:center;'>";
					//echo "</td>\n";
					echo "<td style='text-align: center;'>\n";
					echo "<input type='checkbox' id='check_".$cpt."' name='valid_pers_id[]' value='$pers_id' />\n";
					echo "<input type='hidden' name='liste_pers_id[]' value='$pers_id' />\n";
					echo "</td>\n";

					if($nouveau==0){
						//echo "<td style='text-align: center; background-color: lightgreen;'>Modif</td>\n";
						echo "<td class='modif'>Modif</td>\n";
					}
					else{
						//echo "<td style='text-align: center; background-color: rgb(150, 200, 240);'>Nouveau</td>\n";
						//echo "<td class='nouveau'>Nouveau</td>\n";

						$sql="SELECT 1=1 FROM temp_resp_pers_import trp,
												temp_responsables2_import tr,
												eleves e
										WHERE trp.pers_id='$pers_id' AND
												trp.pers_id=tr.pers_id AND
												tr.ele_id=e.ele_id";
						$test=mysql_query($sql);
						if(mysql_num_rows($test)>0){
							echo "<td class='nouveau'>Nouveau</td>\n";
						}
						else{
							echo "<td style='background-color:orange;'>Nouveau</td>\n";
						}
					}

					echo "<td style='text-align:center;'>$pers_id";
					//echo "<input type='hidden' name='modif_".$cpt."_pers_id' value='$pers_id' />\n";
					//echo "<input type='text' name='modif_".$cpt."_pers_id' value='$pers_id' />\n";
					echo "</td>\n";


					$sql="SELECT * FROM temp_resp_pers_import WHERE (pers_id='$pers_id')";
					$res_pers2=mysql_query($sql);
					$lig_pers2=mysql_fetch_object($res_pers2);

					//echo "<td style='text-align:center;";
					echo "<td";
					if($nouveau==0){
						if(stripslashes($lig_pers2->nom)!=stripslashes($nom1)){
							//echo " background-color:lightgreen;'>";
							echo " class='modif'>";
							if($nom1!=''){
								echo stripslashes($nom1)." <font color='red'>-&gt;</font>\n";
							}
						}
						else{
							//echo "'>";
							echo ">";
						}
					}
					else{
						//echo "'>";
						echo ">";
					}
					echo stripslashes($lig_pers2->nom);
					echo "</td>\n";

					//echo "<td style='text-align:center;";
					echo "<td";
					if($nouveau==0){
						if(stripslashes($lig_pers2->prenom)!=stripslashes($prenom1)){
							//echo " background-color:lightgreen;'>";
							echo " class='modif'>";
							if($prenom1!=''){
								echo stripslashes($prenom1)." <font color='red'>-&gt;</font>\n";
							}
						}
						else{
							//echo "'>";
							echo ">";
						}
					}
					else{
						//echo "'>";
						echo ">";
					}
					echo stripslashes($lig_pers2->prenom);
					echo "</td>\n";


					//======================================
					//echo "<td style='text-align:center;";
					echo "<td";
					if($nouveau==0){
						//if(stripslashes($lig_pers2->civilite)!=stripslashes($civilite1)){
						if(ucfirst(strtolower(stripslashes($lig_pers2->civilite)))!=ucfirst(strtolower(stripslashes($civilite1)))){
							//echo " background-color:lightgreen;'>";
							echo " class='modif'>";
							if($civilite1!=''){
								echo stripslashes($civilite1)." <font color='red'>-&gt;</font>\n";
							}
						}
						else{
							//echo "'>";
							echo ">";
						}
					}
					else{
						//echo "'>";
						echo ">";
					}
					//echo stripslashes($lig_pers2->civilite);
					echo ucfirst(strtolower(stripslashes($lig_pers2->civilite)));
					echo "</td>\n";
					//======================================


					echo "<td style='text-align:center; padding: 2px;'>";
						//echo "<table border='1' width='100%'>\n";
						echo "<table class='majimport' width='100%'>\n";
						echo "<tr>\n";
						echo "<td style='text-align:center; font-weight:bold;'>Tel</td>\n";
						//echo "<td style='text-align:center;";
						echo "<td";
						if($nouveau==0){
							if($lig_pers2->tel_pers!=$tel_pers1) {
								if(($lig_pers2->tel_pers!='')||($tel_pers1!='')){
									//echo " background-color:lightgreen;'>";
									echo " class='modif'>";
									if($tel_pers1!=''){
										echo $tel_pers1." <font color='red'>-&gt;</font>\n";
									}
								}
								else{
									//echo "'>";
									echo ">";
								}
							}
							else{
								//echo "'>";
								echo ">";
							}
						}
						else{
							//echo "'>";
							echo ">";
						}
						echo $lig_pers2->tel_pers;
						echo "</td>\n";
						echo "</tr>\n";

						echo "<tr>\n";
						echo "<td style='text-align:center; font-weight:bold;'>TPo</td>\n";
						//echo "<td style='text-align:center;";
						echo "<td";
						if($nouveau==0){
							if($lig_pers2->tel_port!=$tel_port1) {
								if(($lig_pers2->tel_port!='')||($tel_port1!='')){
									//echo " background-color:lightgreen;'>";
									echo " class='modif'>";
									if($tel_port1!=''){
										echo $tel_port1." <font color='red'>-&gt;</font>\n";
									}
								}
								else{
									//echo "'>";
									echo ">";
								}
							}
							else{
								//echo "'>";
								echo ">";
							}
						}
						else{
							//echo "'>";
							echo ">";
						}
						echo $lig_pers2->tel_port;
						echo "</td>\n";
						echo "</tr>\n";

						echo "<tr>\n";
						echo "<td style='text-align:center; font-weight:bold;'>TPr</td>\n";
						//echo "<td style='text-align:center;";
						echo "<td";
						if($nouveau==0){
							if($lig_pers2->tel_prof!=$tel_prof1) {
								if(($lig_pers2->tel_prof!='')||($tel_prof1!='')){
									//echo " background-color:lightgreen;'>";
									echo " class='modif'>";
									if($tel_prof1!=''){
										echo $tel_prof1." <font color='red'>-&gt;</font>\n";
									}
								}
								else{
									//echo "'>";
									echo ">";
								}
							}
							else{
								//echo "'>";
								echo ">";
							}
						}
						else{
							//echo "'>";
							echo ">";
						}
						echo $lig_pers2->tel_prof;
						echo "</td>\n";
						echo "</tr>\n";

						echo "<tr>\n";
						echo "<td style='text-align:center; font-weight:bold;'>mel</td>\n";
						//echo "<td style='text-align:center;";
						echo "<td";
						if($nouveau==0){
							if($lig_pers2->mel!=$mel1) {
								if(($lig_pers2->mel!='')||($mel1!='')){
									//echo " background-color:lightgreen;'>";
									echo " class='modif'>";
									if($mel1!=''){
										echo $mel1." <font color='red'>-&gt;</font>\n";
									}
								}
								else{
									//echo "'>";
									echo ">";
								}
							}
							else{
								//echo "'>";
								echo ">";
							}
						}
						else{
							//echo "'>";
							echo ">";
						}
						echo $lig_pers2->mel;
						echo "</td>\n";
						echo "</tr>\n";
						echo "</table>\n";

						//echo "\$lig_pers2->adr_id=$lig_pers2->adr_id";
					echo "</td>\n";



					// Adresse
					//echo "<td style='text-align:center;";
					echo "<td";

					if($lig_pers2->adr_id!=""){
						$sql="SELECT * FROM temp_resp_adr_import WHERE (adr_id='".$lig_pers2->adr_id."')";
						$res_adr2=mysql_query($sql);
						if(mysql_num_rows($res_adr2)==0){
							$adr1_2="";
							$adr2_2="";
							$adr3_2="";
							$adr4_2="";
							$cp2="";
							$commune2="";
							$pays2="";
						}
						else{
							$lig_adr2=mysql_fetch_object($res_adr2);

							$adr1_2=$lig_adr2->adr1;
							$adr2_2=$lig_adr2->adr2;
							$adr3_2=$lig_adr2->adr3;
							$adr4_2=$lig_adr2->adr4;
							$cp2=$lig_adr2->cp;
							$commune2=$lig_adr2->commune;
							$pays2=$lig_adr2->pays;
						}
					}
					else{
						$adr1_2="";
						$adr2_2="";
						$adr3_2="";
						$adr4_2="";
						$cp2="";
						$commune2="";
						$pays2="";
					}

					if($nouveau==0){
						if($adr_id1!=""){
							$sql="SELECT * FROM resp_adr WHERE (adr_id='".$adr_id1."')";
							//$adr_id=$personne[$pers_id]["adr_id"];
							$res_adr1=mysql_query($sql);
							if(mysql_num_rows($res_adr1)==0){
								$adr1_1="";
								$adr2_1="";
								$adr3_1="";
								$adr4_1="";
								$cp1="";
								$commune1="";
								$pays1="";
							}
							else{
								$lig_adr1=mysql_fetch_object($res_adr1);

								$adr1_1=$lig_adr1->adr1;
								$adr2_1=$lig_adr1->adr2;
								$adr3_1=$lig_adr1->adr3;
								$adr4_1=$lig_adr1->adr4;
								$cp1=$lig_adr1->cp;
								$commune1=$lig_adr1->commune;
								$pays1=$lig_adr1->pays;
							}
						}
						else{
							$adr1_1="";
							$adr2_1="";
							$adr3_1="";
							$adr4_1="";
							$cp1="";
							$commune1="";
							$pays1="";
						}

						$chaine_adr1="";
						if(($adr1_1!="")||($adr2_1!="")||($adr3_1!="")||($adr4_1!="")||($cp1!="")||($commune1!="")||($pays1!="")){
							if($adr1_1!=""){
								$chaine_adr1.=stripslashes("$adr1_1, ");
							}
							if($adr2_1!=""){
								$chaine_adr1.=stripslashes("$adr2_1, ");
							}
							if($adr3_1!=""){
								$chaine_adr1.=stripslashes("$adr3_1, ");
							}
							if($adr4_1!=""){
								$chaine_adr1.=stripslashes("$adr4_1, ");
							}
							if($cp1!=""){
								$chaine_adr1.=stripslashes("$cp1, ");
							}
							if($commune1!=""){
								$chaine_adr1.=stripslashes("$commune1, ");
							}
							if($pays1!=""){
								$chaine_adr1.=stripslashes("$pays1");
							}
						}

						$chaine_adr2="";
						if(($adr1_2!="")||($adr2_2!="")||($adr3_2!="")||($adr4_2!="")||($cp2!="")||($commune2!="")||($pays2!="")){
							if($adr1_2!=""){
								$chaine_adr2.=stripslashes("$adr1_2, ");
							}
							if($adr2_2!=""){
								$chaine_adr2.=stripslashes("$adr2_2, ");
							}
							if($adr3_2!=""){
								$chaine_adr2.=stripslashes("$adr3_2, ");
							}
							if($adr4_2!=""){
								$chaine_adr2.=stripslashes("$adr4_2, ");
							}
							if($cp2!=""){
								$chaine_adr2.=stripslashes("$cp2, ");
							}
							if($commune2!=""){
								$chaine_adr2.=stripslashes("$commune2, ");
							}
							if($pays2!=""){
								$chaine_adr2.=stripslashes("$pays2");
							}
						}

						if($chaine_adr1!=$chaine_adr2){
							//echo " background-color:lightgreen;'>";
							echo " class='modif'>";
							echo $chaine_adr1;
							echo " <font color='red'>-&gt;</font><br />\n";
						}
						else{
							//echo "'>";
							echo ">";
						}
						echo $chaine_adr2;


						/*
						if($lig_pers2->adr_id!=$adr_id1) {
							if($adr_id1!=""){
								$sql="SELECT * FROM resp_adr WHERE (adr_id='".$adr_id1."')";
								//$adr_id=$personne[$pers_id]["adr_id"];
								$res_adr1=mysql_query($sql);
								if(mysql_num_rows($res_adr1)==0){
									$adr1_1="";
									$adr2_1="";
									$adr3_1="";
									$adr4_1="";
									$cp1="";
									$commune1="";
									$pays1="";
								}
								else{
									$lig_adr1=mysql_fetch_object($res_adr1);

									$adr1_1=$lig_adr1->adr1;
									$adr2_1=$lig_adr1->adr2;
									$adr3_1=$lig_adr1->adr3;
									$adr4_1=$lig_adr1->adr4;
									$cp1=$lig_adr1->cp;
									$commune1=$lig_adr1->commune;
									$pays1=$lig_adr1->pays;
								}
							}
							else{
								$adr1_1="";
								$adr2_1="";
								$adr3_1="";
								$adr4_1="";
								$cp1="";
								$commune1="";
								$pays1="";
							}


							echo " background-color:lightgreen;'>";
							if(($adr1_1!="")||($adr2_1!="")||($adr3_1!="")||($adr4_1!="")||($cp1!="")||($commune1!="")||($pays1!="")){
								$chaine_adr="";
								if($adr1_1!=""){
									$chaine_adr.=stripslashes("$adr1_1, ");
								}
								if($adr2_1!=""){
									$chaine_adr.=stripslashes("$adr2_1, ");
								}
								if($adr3_1!=""){
									$chaine_adr.=stripslashes("$adr3_1, ");
								}
								if($adr4_1!=""){
									$chaine_adr.=stripslashes("$adr4_1, ");
								}
								if($cp1!=""){
									$chaine_adr.=stripslashes("$cp1, ");
								}
								if($commune1!=""){
									$chaine_adr.=stripslashes("$commune1, ");
								}
								if($pays1!=""){
									$chaine_adr.=stripslashes("$pays1");
								}
								echo $chaine_adr;
								echo " <font color='red'>-&gt;</font><br />\n";
							}

							if(($adr1_2!="")||($adr2_2!="")||($adr3_2!="")||($adr4_2!="")||($cp2!="")||($commune2!="")||($pays2!="")){
								$chaine_adr="";
								if($adr1_2!=""){
									$chaine_adr.=stripslashes("$adr1_2, ");
								}
								if($adr2_2!=""){
									$chaine_adr.=stripslashes("$adr2_2, ");
								}
								if($adr3_2!=""){
									$chaine_adr.=stripslashes("$adr3_2, ");
								}
								if($adr4_2!=""){
									$chaine_adr.=stripslashes("$adr4_2, ");
								}
								if($cp2!=""){
									$chaine_adr.=stripslashes("$cp2, ");
								}
								if($commune2!=""){
									$chaine_adr.=stripslashes("$commune2, ");
								}
								if($pays2!=""){
									$chaine_adr.=stripslashes("$pays2");
								}
								echo $chaine_adr;
							}
							else{
								echo "Adresse vide";
							}
						}
						else{
							echo "'>";
							echo "Identifiant d'adresse inchangé: $adr_id1";
						}
						*/

					}
					else{
						//echo "'>";
						echo ">";
						// Indiquer l'adresse pour cette nouvelle personne responsable

						if(($adr1_2!="")||($adr2_2!="")||($adr3_2!="")||($adr4_2!="")||($cp2!="")||($commune2!="")||($pays2!="")){
							$chaine_adr="";
							if($adr1_2!=""){
								$chaine_adr.=stripslashes("$adr1_2, ");
							}
							if($adr2_2!=""){
								$chaine_adr.=stripslashes("$adr2_2, ");
							}
							if($adr3_2!=""){
								$chaine_adr.=stripslashes("$adr3_2, ");
							}
							if($adr4_2!=""){
								$chaine_adr.=stripslashes("$adr4_2, ");
							}
							if($cp2!=""){
								$chaine_adr.=stripslashes("$cp2, ");
							}
							if($commune2!=""){
								$chaine_adr.=stripslashes("$commune2, ");
							}
							if($pays2!=""){
								$chaine_adr.=stripslashes("$pays2");
							}
							echo $chaine_adr;
						}
						else{
							echo "<span color='red'>Adresse vide</span>\n";
						}
					}
					echo "</td>\n";


					echo "</tr>\n";
					$cpt++;
				}

				echo $ligne_entete_tableau;
				echo "</table>\n";

				echo "<script type='text/javascript'>
	function modifcase(mode){
		for(i=0;i<$cpt;i++){
			if(document.getElementById('check_'+i)){
				if(mode=='coche'){
					document.getElementById('check_'+i).checked=true;
				}
				else{
					document.getElementById('check_'+i).checked=false;
				}
			}
		}
	}
</script>\n";

				echo "<input type='hidden' name='step' value='15' />\n";
				//echo "<p align='center'><input type='submit' value='Poursuivre' /></p>\n";
				echo "<p align='center'><input type='submit' value='Valider' /></p>\n";
			}
			else{
				// On est à la fin on peut passer à step=12 et effectuer les changements confirmés.
				echo "<p>Toutes les différences concernant les personnes ont été parcourues.</p>\n";

				echo "<input type='hidden' name='step' value='16' />\n";
				echo "<p><input type='submit' value='Valider les modifications' /></p>\n";
			}

			//echo "<input type='hidden' name='is_posted' value='yes' />\n";
			echo "</form>\n";

			break;
		case 16:
			echo "<h2>Import/mise à jour des responsables</h2>\n";

			//echo "<p>On doit parcourir 'tempo2' en recherchant 'pers_id_confirm'.</p>\n";

			$sql="SELECT DISTINCT col2 FROM tempo2 WHERE col1='pers_id_confirm';";
			$res1=mysql_query($sql);
			if(mysql_num_rows($res1)==0){
				echo "<p>Aucune modification n'a été confirmée/demandée.</p>\n";

				// IL RESTE... les responsabilités
				echo "<p>Passer à l'étape de <a href='".$_SERVER['PHP_SELF']."?step=17&amp;stop=y'>mise à jour des responsabilités</a>.</p>\n";

			}
			else{
				$erreur=0;
				$cpt=0;
				echo "<p>Ajout ou modification de: ";
				while($lig1=mysql_fetch_object($res1)){
					$sql="SELECT DISTINCT t.* FROM temp_resp_pers_import t WHERE t.pers_id='$lig1->col2'";
					$res=mysql_query($sql);
					if(mysql_num_rows($res)>0){
						$lig=mysql_fetch_object($res);

						if($cpt>0){
							echo ", ";
						}

						$sql="SELECT 1=1 FROM resp_pers WHERE pers_id='$lig1->col2'";
						$test=mysql_query($sql);

						if(mysql_num_rows($test)==0){
							// prenom='".addslashes(ucfirst(strtolower($lig->prenom)))."',

							$sql="INSERT INTO resp_pers SET pers_id='$lig1->col2',
													nom='".addslashes(strtoupper($lig->nom))."',
													prenom='".addslashes(maj_ini_prenom($lig->prenom))."',
													civilite='".ucfirst(strtolower($lig->civilite))."',
													tel_pers='".$lig->tel_pers."',
													tel_port='".$lig->tel_port."',
													tel_prof='".$lig->tel_prof."',
													mel='".$lig->mel."',
													adr_id='".$lig->adr_id."';";
							$insert=mysql_query($sql);
							if($insert){
								echo "\n<span style='color:blue;'>";
							}
							else{
								echo "\n<span style='color:red;'>";
								$erreur++;
							}
							echo "$lig->prenom $lig->nom";
							echo "</span>";
						}
						else{
							$sql="UPDATE resp_pers SET nom='".addslashes(strtoupper($lig->nom))."',
													prenom='".addslashes(maj_ini_prenom($lig->prenom))."',
													civilite='".ucfirst(strtolower($lig->civilite))."',
													tel_pers='".$lig->tel_pers."',
													tel_port='".$lig->tel_port."',
													tel_prof='".$lig->tel_prof."',
													mel='".$lig->mel."',
													adr_id='".$lig->adr_id."'
												WHERE pers_id='$lig1->col2';";
							$update=mysql_query($sql);
							if($update){
								echo "\n<span style='color:darkgreen;'>";
							}
							else{
								echo "\n<span style='color:red;'>";
								$erreur++;
							}
							//echo "$sql<br />\n";
							echo "$lig->prenom $lig->nom";
							echo "</span>";
						}

						if($lig->adr_id!=""){
							// Ajout ou modification validée, on met à jour l'adresse aussi:
							$sql="SELECT DISTINCT t.* FROM temp_resp_adr_import t WHERE t.adr_id='$lig->adr_id'";
							$res_adr2=mysql_query($sql);
							if(mysql_num_rows($res_adr2)>0){
								$lig_adr2=mysql_fetch_object($res_adr2);

								$adr1_2=$lig_adr2->adr1;
								$adr2_2=$lig_adr2->adr2;
								$adr3_2=$lig_adr2->adr3;
								$adr4_2=$lig_adr2->adr4;
								$cp2=$lig_adr2->cp;
								$commune2=$lig_adr2->commune;
								$pays2=$lig_adr2->pays;


								$sql="SELECT DISTINCT * FROM resp_adr WHERE adr_id='$lig->adr_id'";
								$res_adr1=mysql_query($sql);
								if(mysql_num_rows($res_adr1)>0){
									$lig_adr1=mysql_fetch_object($res_adr1);

									$adr1_1=$lig_adr1->adr1;
									$adr2_1=$lig_adr1->adr2;
									$adr3_1=$lig_adr1->adr3;
									$adr4_1=$lig_adr1->adr4;
									$cp1=$lig_adr1->cp;
									$commune1=$lig_adr1->commune;
									$pays1=$lig_adr1->pays;

									$sql="UPDATE resp_adr SET adr1='".addslashes($adr1_2)."',
																adr2='".addslashes($adr2_2)."',
																adr3='".addslashes($adr3_2)."',
																adr4='".addslashes($adr4_2)."',
																cp='".addslashes($cp2)."',
																commune='".addslashes($commune2)."',
																pays='".addslashes($pays2)."'
														WHERE adr_id='$lig->adr_id'";
									$update=mysql_query($sql);
									if(!$update){
										$erreur++;
										echo "<span style='color:red;'>(*)</span>";
									}
								}
								else{
									$adr1_1="";
									$adr2_1="";
									$adr3_1="";
									$adr4_1="";
									$cp1="";
									$commune1="";
									$pays1="";

									$sql="INSERT INTO resp_adr SET adr1='".addslashes($adr1_2)."',
																adr2='".addslashes($adr2_2)."',
																adr3='".addslashes($adr3_2)."',
																adr4='".addslashes($adr4_2)."',
																cp='".addslashes($cp2)."',
																commune='".addslashes($commune2)."',
																pays='".addslashes($pays2)."',
																adr_id='$lig->adr_id'";
									$insert=mysql_query($sql);
									if(!$insert){
										$erreur++;
										echo "<span style='color:red;'>(*)</span>";
									}

								}
							}
							else{
								// FAUT-IL INSERER UNE LIGNE VIDE dans resp_adr ?

								// On ne devrait pas arriver à cette situation...

							}
						}
					}
					$cpt++;
				}

				echo "<p><br /></p>\n";

				echo "<p><b>Indication:</b> En <span style='color:blue;'>bleu</span>, les personnes ajoutées et en <span style='color:darkgreen;'>vert</span> les personnes/adresses mises à jour.<br />Les <span style='color:red;'>(*)</span> éventuellement présents signalent un souci concernant l'adresse.</p>\n";

				echo "<p><br /></p>\n";

				switch($erreur){
					case 0:
						echo "<p>Passer à l'étape de <a href='".$_SERVER['PHP_SELF']."?step=17&amp;stop=y'>mise à jour des responsabilités</a>.</p>\n";
						break;
					case 1:
						echo "<p><font color='red'>Une erreur s'est produite.</font><br />\nVous devriez en chercher la cause avant de passer à l'étape de <a href='".$_SERVER['PHP_SELF']."?step=17&amp;stop=y'>mise à jour des responsabilités</a>.</p>\n";
						break;

					default:
						echo "<p><font color='red'>$erreur erreurs se sont produites.</font><br />\nVous devriez en chercher la cause avant de passer à l'étape de <a href='".$_SERVER['PHP_SELF']."?step=17&amp;stop=y'>mise à jour des responsabilités</a>.</p>\n";
						break;
				}
			}

			break;
		case 17:
			//echo "<h2>Import/mise à jour des responsabilités</h2>\n";

			echo "<h2>Import/mise à jour des associations responsables/élèves</h2>\n";

			echo "<form action='".$_SERVER['PHP_SELF']."' name='formulaire' method='post'>\n";
			//==============================
			// AJOUT pour tenir compte de l'automatisation ou non:
			echo "<input type='hidden' name='stop' id='id_form_stop' value='$stop' />\n";
			//==============================

			$eff_tranche=20;

			if(!isset($parcours_diff)){

				echo "<p>On va commencer les comparaisons...</p>\n";

				flush();

				$sql="TRUNCATE tempo2;";
				$res0=mysql_query($sql);

				$sql="select ele_id, pers_id from temp_responsables2_import;";
				$res1=mysql_query($sql);

				if(mysql_num_rows($res1)==0){
					echo "<p style='color:red;'>Bizarre: La table 'temp_responsables2_import' est vide.<br />Auriez-vous sauté une étape?</p>\n";

					echo "<p><br /></p>\n";
					require("../lib/footer.inc.php");
					die();
				}
				else{
					$tab_resp=array();
					while($lig=mysql_fetch_object($res1)){
						// On ne va considérer un couple valide que si le responsable est une personne associée à un élève effectivement accepté dans la table 'eleves':
						$sql="SELECT 1=1 FROM temp_resp_pers_import trp,
												temp_responsables2_import tr,
												eleves e
										WHERE trp.pers_id='$lig->pers_id' AND
												trp.pers_id=tr.pers_id AND
												tr.ele_id=e.ele_id";
						$test=mysql_query($sql);
						if(mysql_num_rows($test)>0){
							$sql="INSERT INTO tempo2 SET col1='t', col2='t_".$lig->ele_id."_".$lig->pers_id."'";
							$insert=mysql_query($sql);

							$tab_resp[]="t_".$lig->ele_id."_".$lig->pers_id;
						}
					}
				}

				flush();

				/*
				if($cpt==1){
					echo "<p>L'identifiant ADR_ID d'une nouvelle adresse responsable a été trouvé: $chaine_nouveaux</p>\n";
				}
				elseif($cpt>1){
					echo "<p>Les identifiants ADR_ID de $cpt nouvelles adresses responsables ont été trouvés: $chaine_nouveaux</p>\n";
				}
				*/

				$nb_parcours=ceil(count($tab_resp)/$eff_tranche);
			}
			else{

				echo "<p>Parcours de la tranche <b>$parcours_diff/$nb_parcours</b>.</p>\n";

				if(isset($tab_resp_diff)){
					if(count($tab_resp_diff)==1){
						echo "<p>Le couple ELE_ID/PERS_ID pour lequel une ou des différences ont déjà été repérées, est: \n";
					}
					else{
						echo "<p>Les couples ELE_ID/PERS_ID, pour lesquels une ou des différences ont déjà été repérées, sont: \n";
					}
					$chaine_ele_resp="";
					for($i=0;$i<count($tab_resp_diff);$i++){
						if($i>0){$chaine_ele_resp.=", ";}
						$tab_tmp=explode("_",$tab_resp_diff[$i]);
						$chaine_ele_resp.=$tab_tmp[1]."/".$tab_tmp[2];
						//echo "$i: ";
						echo "<input type='hidden' name='tab_resp_diff[]' value='$tab_resp_diff[$i]' />\n";
						//echo "<br />\n";
					}
					echo $chaine_ele_resp;
					echo "</p>\n";
				}
			}

			echo "<input type='hidden' name='nb_parcours' value='$nb_parcours' />\n";

			//echo "count(\$tab_resp)=".count($tab_resp)."<br />";

			// On construit la chaine des $eff_tranche couples retenus pour la requête à venir:
			//$chaine="";
			$cpt=0;
			for($i=0;$i<min($eff_tranche,count($tab_resp));$i++){
				//if($i>0){$chaine.=" OR ";}

				$tab_tmp=explode("_",$tab_resp[$i]);

				//$chaine.="(t.ele_id='$tab_tmp[1]' AND t.pers_id='$tab_tmp[2]')";

				$sql="SELECT 1=1 FROM responsables2 WHERE ele_id='$tab_tmp[1]' AND pers_id='$tab_tmp[2]';";
				//echo "$sql<br />";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)==0){
					// C'est une nouvelle responsabilité
					/*
					//$sql="UPDATE tempo2 SET col1='t_new' WHERE col2='t_".$tab_tmp[1]."_".$tab_tmp[2]."'";
					$sql="UPDATE tempo2 SET col1='t_diff' WHERE col2='t_".$tab_tmp[1]."_".$tab_tmp[2]."'";
					$update=mysql_query($sql);
					*/

					echo "<input type='hidden' name='tab_resp_diff[]' value='t_".$tab_tmp[1]."_".$tab_tmp[2]."' />\n";

					// FAIRE UN echo POUR INDIQUER CES NOUVEAUX RESPONSABLES REPéRéS
					// REMPLIR UNE CHAINE ET L'AJOUTER A LA FIN DE LA LISTE AFFICHéE PLUS BAS
				}
				else{

					$sql="SELECT t.ele_id,t.pers_id FROM responsables2 r, temp_responsables2_import t
									WHERE r.pers_id=t.pers_id AND
											r.ele_id=t.ele_id AND
											(
												r.resp_legal!=t.resp_legal OR
												r.pers_contact!=t.pers_contact
											)
											AND (t.ele_id='$tab_tmp[1]' AND t.pers_id='$tab_tmp[2]')
											";
					//echo "$sql<br />\n";
					$test=mysql_query($sql);
					if(mysql_num_rows($test)>0){
						if($cpt==0){
							echo "<p>Une ou des différences ont été trouvées dans la tranche étudiée à cette phase.";
							echo "<br />\n";
							echo "En voici le(s) couple(s) ELE_ID/PERS_ID: ";
						}
						else{
							echo ", ";
						}
						$lig=mysql_fetch_object($test);

						echo $lig->ele_id."/".$lig->pers_id;
						echo "<input type='hidden' name='tab_resp_diff[]' value='t_".$lig->ele_id."_".$lig->pers_id."' />\n";
						//echo "<br />\n";
						// Pour le cas où on est dans la dernière tranche:
						$tab_resp_diff[]="t_".$lig->ele_id."_".$lig->pers_id;
						$cpt++;
					}
				}
			}

			//echo "\$chaine=$chaine<br />\n";

			// Liste des couples restant à parcourir:
			for($i=$eff_tranche;$i<count($tab_resp);$i++){
				//echo "$i: ";
				echo "<input type='hidden' name='tab_resp[]' value='$tab_resp[$i]' />\n";
				//echo "<br />\n";
			}


			/*
			$sql="SELECT t.ele_id,t.pers_id FROM responsables2 r, temp_responsables2_import t
							WHERE r.pers_id=t.pers_id AND
									r.ele_id=t.ele_id AND
									(
										r.resp_legal!=t.resp_legal OR
										r.pers_contact!=t.pers_contact
									)
									AND ($chaine)
									";
			//echo "$sql<br />\n";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)>0){
				echo "<p>Une ou des différences ont été trouvées dans la tranche étudiée à cette phase.";
				echo "<br />\n";
				echo "En voici le(s) couple(s) ELE_ID/PERS_ID: ";
				$cpt=0;
				$chaine_ele_resp="";
				while($lig=mysql_fetch_object($test)){
					if($cpt>0){$chaine_ele_resp.=", ";}
					$chaine_ele_resp.=$lig->ele_id."/".$lig->pers_id;
					echo "<input type='hidden' name='tab_resp_diff[]' value='t_".$lig->ele_id."_".$lig->pers_id."' />\n";
					//echo "<br />\n";
					// Pour le cas où on est dans la dernière tranche:
					$tab_resp_diff[]="t_".$lig->ele_id."_".$lig->pers_id;
					$cpt++;
				}
				echo $chaine_ele_resp;
			}
			*/

			if(!isset($parcours_diff)){$parcours_diff=1;}
			$parcours_diff++;
			//echo "<input type='hidden' name='parcours_diff' value='$parcours_diff' />\n";


			if(count($tab_resp)>$eff_tranche){
				echo "<input type='hidden' name='parcours_diff' value='$parcours_diff' />\n";
				echo "<input type='hidden' name='step' value='17' />\n";
				echo "<p><input type='submit' value='Suite' /></p>\n";


				echo "<script type='text/javascript'>
	/*
	stop='n';
	if(document.getElementById('stop')){
		if(document.getElementById('stop').checked==true){
			stop='y';
		}
	}
	if(stop=='n'){
		setTimeout(\"document.forms['formulaire'].submit();\",1000);
	}
	*/
	setTimeout(\"test_stop2()\",3000);
</script>\n";


			}
			else{
				echo "<p>Le parcours des différences concernant les associations élève/responsables est terminé.<br />Vous allez pouvoir contrôler les différences.</p>\n";
				//echo "<p>La première phase du parcours des différences concernant les associations élève/responsables est terminé.<br />Vous allez pouvoir passer à la deuxième phase avant de contrôler les différences.</p>\n";

				for($i=0;$i<count($tab_resp_diff);$i++){
					$sql="UPDATE tempo2 SET col1='t_diff' WHERE col2='$tab_resp_diff[$i]'";
					$update=mysql_query($sql);
				}

				echo "<input type='hidden' name='step' value='18' />\n";
				echo "<p><input type='submit' value='Afficher les différences' /></p>\n";

				echo "<script type='text/javascript'>
	/*
	stop='n';
	if(document.getElementById('stop')){
		if(document.getElementById('stop').checked==true){
			stop='y';
		}
	}
	if(stop=='n'){
		setTimeout(\"document.forms['formulaire'].submit();\",5000);
	}
	*/
	setTimeout(\"test_stop2()\",3000);
</script>\n";

			}
			echo "</form>\n";


			break;
		case 18:

			echo "<h2>Import/mise à jour des associations responsables/élèves</h2>\n";

			echo "<form action='".$_SERVER['PHP_SELF']."' name='formulaire' method='post'>\n";
			//==============================
			// AJOUT pour tenir compte de l'automatisation ou non:
   echo "<input type='hidden' name='stop' id='id_form_stop' value='$stop' />\n";
			//==============================

			if(!isset($parcours_diff)){
				$sql="SELECT 1=1 FROM tempo2 WHERE col1='t_diff';";
				$test=mysql_query($sql);

				//echo "<p>".count($tab_pers_id_diff)." personnes...</p>\n";
				$nb_associations_a_consulter=mysql_num_rows($test);

				if($nb_associations_a_consulter==0){
					echo "<p>Aucune association ELE_ID/PERS_ID ne requiert votre attention.</p>\n";
				}
				elseif($nb_associations_a_consulter==1){
					echo "<p>".$nb_associations_a_consulter." association ELE_ID/PERS_ID requiert votre attention.</p>\n";
				}
				else{
					echo "<p>".$nb_associations_a_consulter." associations ELE_ID/PERS_ID requièrent votre attention.</p>\n";
				}
				//echo "<input type='hidden' name='total_pers_diff' value='".count($tab_pers_id_diff)."' />\n";
				echo "<input type='hidden' name='total_diff' value='".$nb_associations_a_consulter."' />\n";

			}
			else{
				$modif=isset($_POST['modif']) ? $_POST['modif'] : NULL;
				$new=isset($_POST['new']) ? $_POST['new'] : NULL;

				// A VOIR: IL FAUDRAIT PEUT-ETRE VALIDER LES MODIFS DES CE NIVEAU...
				// LES TESTS POUR NE PAS AVOIR DEUX resp_legal=1 PEUVENT ETRE PERTURBéS PAR DES ENREGISTREMENTS DIFFéRéS...

				if(isset($modif)){
					// On modifie la valeur de col1 pour les ele_id/pers_id confirmés pour ne pas les re-parcourir:
					for($i=0;$i<count($modif);$i++){
						$sql="UPDATE tempo2 SET col1='t_diff_confirm' WHERE col2='$modif[$i]';";
						$update=mysql_query($sql);
					}

					if(isset($new)){
						// On modifie la valeur de col1 pour les ele_id/pers_id confirmés pour ne pas les re-parcourir:
						for($i=0;$i<count($new);$i++){
							$sql="UPDATE tempo2 SET col1='t_diff_confirm' WHERE col2='$new[$i]';";
							$update=mysql_query($sql);
						}

						for($i=0;$i<count($liste_assoc);$i++){
							if((!in_array($liste_assoc[$i],$modif))&&(!in_array($liste_assoc[$i],$new))) {
								$sql="UPDATE tempo2 SET col1='t_diff_refus' WHERE col2='$liste_assoc[$i]';";
								$update=mysql_query($sql);
							}
						}
					}
					else{
						for($i=0;$i<count($liste_assoc);$i++){
							if(!in_array($liste_assoc[$i],$modif)){
								$sql="UPDATE tempo2 SET col1='t_diff_refus' WHERE col2='$liste_assoc[$i]';";
								$update=mysql_query($sql);
							}
						}
					}
				}
				elseif(isset($new)){
					// On modifie la valeur de col1 pour les ele_id/pers_id confirmés pour ne pas les re-parcourir:
					for($i=0;$i<count($new);$i++){
						$sql="UPDATE tempo2 SET col1='t_diff_confirm' WHERE col2='$new[$i]';";
						$update=mysql_query($sql);
					}

					for($i=0;$i<count($liste_assoc);$i++){
						if(!in_array($liste_assoc[$i],$new)) {
							$sql="UPDATE tempo2 SET col1='t_diff_refus' WHERE col2='$liste_assoc[$i]';";
							$update=mysql_query($sql);
						}
					}
				}
				else{
					if(isset($liste_assoc)){
						for($i=0;$i<count($liste_assoc);$i++){
							$sql="UPDATE tempo2 SET col1='t_diff_refus' WHERE col2='$liste_assoc[$i]';";
							$update=mysql_query($sql);
						}
					}
				}

				// FAIRE LES ENREGISTREMENTS A CE NIVEAU!!!
				if(isset($modif)){
					$compteur_modifs=0;
					for($i=0;$i<count($modif);$i++){
						$tab_tmp=explode("_",$modif[$i]);
						$ele_id=$tab_tmp[1];
						$pers_id=$tab_tmp[2];

						$sql="SELECT * FROM temp_responsables2_import WHERE ele_id='$ele_id' AND pers_id='$pers_id';";
						$res1=mysql_query($sql);
						if(mysql_num_rows($res1)>0){
							$lig1=mysql_fetch_object($res1);

							$resp_legal=$lig1->resp_legal;
							$pers_contact=$lig1->pers_contact;

							$sql="SELECT * FROM responsables2 WHERE ele_id='$ele_id' AND pers_id='$pers_id';";
							$test1=mysql_query($sql);
							// Pour une modif, ce test doit toujours être vrai.
							if(mysql_num_rows($test1)>0){
								$sql="DELETE FROM responsables2 WHERE ele_id='$ele_id' AND pers_id='$pers_id';";
								$suppr=mysql_query($sql);
							}

							// Il ne peut pas y avoir 2 resp_legal 1, ni 2 resp_legal 2 pour un même élève.
							if(($resp_legal==1)||($resp_legal==2)) {
								$sql="SELECT * FROM responsables2 WHERE ele_id='$ele_id' AND resp_legal='$resp_legal';";
								$test2=mysql_query($sql);
								if(mysql_num_rows($test2)>0){
									//$lig2=mysql_fetch_object($test2);
									$sql="UPDATE responsables2 SET pers_id='$pers_id',
																	pers_contact='$pers_contact'
															WHERE ele_id='$ele_id' AND
																	resp_legal='$resp_legal';";
									$update=mysql_query($sql);
								}
								else{
									$sql="INSERT INTO responsables2 SET pers_id='$pers_id',
																	pers_contact='$pers_contact',
																	ele_id='$ele_id',
																	resp_legal='$resp_legal';";
									$update=mysql_query($sql);
								}
							}
							else{
								// Cas de resp_legal=0
								$sql="INSERT INTO responsables2 SET pers_id='$pers_id',
																pers_contact='$pers_contact',
																ele_id='$ele_id',
																resp_legal='$resp_legal';";
								$update=mysql_query($sql);
							}
						}
					}
					//===========================
					// A FAIRE: boireaus 20071115
					// Indiquer combien d'enregistrements viennent d'être effectués.
					//===========================
				}

				if(isset($new)){
					for($i=0;$i<count($new);$i++){
						$tab_tmp=explode("_",$new[$i]);
						$ele_id=$tab_tmp[1];
						$pers_id=$tab_tmp[2];

						$sql="SELECT * FROM temp_responsables2_import WHERE ele_id='$ele_id' AND pers_id='$pers_id';";
						$res1=mysql_query($sql);
						if(mysql_num_rows($res1)>0){
							$lig1=mysql_fetch_object($res1);

							$resp_legal=$lig1->resp_legal;
							$pers_contact=$lig1->pers_contact;

							$sql="SELECT * FROM responsables2 WHERE ele_id='$ele_id' AND pers_id='$pers_id';";
							$test1=mysql_query($sql);
							// Pour une 'new', ce test doit toujours être faux.
							if(mysql_num_rows($test1)>0){
								$sql="DELETE FROM responsables2 WHERE ele_id='$ele_id' AND pers_id='$pers_id';";
								$suppr=mysql_query($sql);
							}

							// Il ne peut pas y avoir 2 resp_legal 1, ni 2 resp_legal 2 pour un même élève.
							if(($resp_legal==1)||($resp_legal==2)) {
								$sql="SELECT * FROM responsables2 WHERE ele_id='$ele_id' AND resp_legal='$resp_legal';";
								$test2=mysql_query($sql);
								if(mysql_num_rows($test2)>0){
									//$lig2=mysql_fetch_object($test2);
									$sql="UPDATE responsables2 SET pers_id='$pers_id',
																	pers_contact='$pers_contact'
															WHERE ele_id='$ele_id' AND
																	resp_legal='$resp_legal';";
									$update=mysql_query($sql);
								}
								else{
									$sql="INSERT INTO responsables2 SET pers_id='$pers_id',
																	pers_contact='$pers_contact',
																	ele_id='$ele_id',
																	resp_legal='$resp_legal';";
									$update=mysql_query($sql);
								}
							}
							else{
								// Cas de resp_legal=0
								$sql="INSERT INTO responsables2 SET pers_id='$pers_id',
																pers_contact='$pers_contact',
																ele_id='$ele_id',
																resp_legal='$resp_legal';";
								$update=mysql_query($sql);
							}
						}
					}
					//===========================
					// A FAIRE: boireaus 20071115
					// Indiquer combien d'enregistrements viennent d'être effectués.
					//===========================
				}




				$sql="SELECT 1=1 FROM tempo2 WHERE col1='t_diff';";
				$test=mysql_query($sql);

				if(mysql_num_rows($test)>0){
					echo "<p>".mysql_num_rows($test)." associations restantes sur un total de $total_diff.</p>\n";
				}
				else{
					echo "<p>Toutes les associations (<i>$total_diff</i>) ont été parcourues.</p>\n";
				}
				echo "<input type='hidden' name='total_diff' value='".$total_diff."' />\n";
			}

			flush();

			echo "<input type='hidden' name='parcours_diff' value='y' />\n";

			$eff_tranche=20;

			$sql="SELECT col2 FROM tempo2 WHERE col1='t_diff' LIMIT $eff_tranche";
			$res0=mysql_query($sql);

			if(mysql_num_rows($res0)>0){

				//echo "<p align='center'><input type='submit' value='Poursuivre' /></p>\n";
				echo "<p align='center'><input type=submit value='Valider' /></p>\n";

				// Affichage du tableau

				//echo "<table border='1'>\n";
				//echo "<table class='majimport'>\n";
				echo "<table class='boireaus'>\n";
				echo "<tr>\n";

				//echo "<td style='text-align: center; font-weight: bold;' rowspan='2'>Enregistrer<br />\n";
				echo "<td style='text-align: center; font-weight: bold;' rowspan='2'>Modifier<br />\n";
				echo "<a href=\"javascript:modifcase('coche')\">";
				echo "<img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>";
				echo " / ";
				echo "<a href=\"javascript:modifcase('decoche')\">";
				echo "<img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
				echo "</td>\n";

				echo "<td rowspan='2'>&nbsp;</td>\n";

				echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);' colspan='5'>Responsable</td>\n";

				echo "<td style='text-align:center; font-weight:bold; background-color: #FAFABE;' colspan='3'>Elève</td>\n";

				//=========================
				// AJOUT: boireaus 20071129
				echo "<td style='text-align:center; font-weight:bold; background-color: red;' rowspan='2'>Suppression<br />du responsable</td>\n";
				//=========================

				echo "</tr>\n";

				echo "<tr>\n";
				echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>pers_id</td>\n";
				echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Nom</td>\n";
				echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>Prénom</td>\n";
				echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>resp_legal</td>\n";
				echo "<td style='text-align:center; font-weight:bold; background-color: rgb(150, 200, 240);'>pers_contact</td>\n";

				echo "<td style='text-align:center; font-weight:bold; background-color: #FAFABE;'>Nom</td>\n";
				echo "<td style='text-align:center; font-weight:bold; background-color: #FAFABE;'>Prénom</td>\n";
				echo "<td style='text-align:center; font-weight:bold; background-color: #FAFABE;'>ele_id</td>\n";

				echo "</tr>\n";


				$alt=1;

				$cpt=0;
				$nb_reg_no1=0;
				$nb_record1=0;
				//for($k = 1; ($k < $nblignes+1); $k++){
				while($lig0=mysql_fetch_object($res0)){
					$tab_tmp=explode("_",$lig0->col2);


					$ele_id=$tab_tmp[1];
					$pers_id=$tab_tmp[2];

					$sql="SELECT * FROM temp_responsables2_import WHERE ele_id='$ele_id' AND pers_id='$pers_id'";
					$res0b=mysql_query($sql);
					if(mysql_num_rows($res0b)==0){
						// CA NE DOIT PAS ARRIVER
						echo "<tr><td>ANOMALIE! Ce cas ne devrait pas arriver</td></tr>\n";
					}
					else{
						$lig0b=mysql_fetch_object($res0b);

						$resp_legal=$lig0b->resp_legal;
						$pers_contact=$lig0b->pers_contact;
					}


					//echo "<tr>\n";

					//$sql="SELECT * FROM responsables2 WHERE ele_id='$affiche[0]' AND pers_id='$affiche[1]'";
					$sql="SELECT * FROM responsables2 WHERE (ele_id='$ele_id' AND pers_id='$pers_id')";
					$res1=mysql_query($sql);
					if(mysql_num_rows($res1)==0){
						// L'association responsable/eleve n'existe pas encore
						$resp_new[]="$ele_id:$pers_id";


						$alt=$alt*(-1);
						/*
						echo "<tr style='background-color:";
						if($alt==1){
							echo "silver";
						}
						else{
							echo "white";
						}
						echo ";'>\n";
						*/
						echo "<tr class='lig$alt'>\n";

						$sql="SELECT nom,prenom FROM resp_pers WHERE (pers_id='$pers_id')";
						$res2=mysql_query($sql);
						if(mysql_num_rows($res2)==0){
							// Problème: On ne peut pas importer l'association sans que la personne existe.
							// Est-ce que l'étape d'import de la personne a été refusée?
							echo "<td>&nbsp;</td>\n";
							echo "<td>&nbsp;</td>\n";

							echo "<td style='background-color:red;'>&nbsp;</td>\n";
							//echo "<td colspan='5'>Aucune personne associée???</td>\n";
							echo "<td colspan='7'>Aucune personne associée???</td>\n";

							//=========================
							// AJOUT: boireaus 20071129
							//echo "<td style='text-align:center;'><input type='checkbox' name='suppr_resp[]' value='$pers_id' /></td>\n";
							//echo "<td style='text-align:center;'>&nbsp;</td>\n";
							//=========================

						}
						else{
							$lig2=mysql_fetch_object($res2);
							echo "<td style='text-align:center;'>\n";
							//echo "<input type='checkbox' id='check_".$cpt."' name='new[]' value='$cpt' />";

							// Elève(s) associé(s)
							$sql="SELECT nom,prenom FROM eleves WHERE (ele_id='$ele_id')";
							$res4=mysql_query($sql);
							if(mysql_num_rows($res4)>0){
								echo "<input type='checkbox' id='check_".$cpt."' name='new[]' value='$lig0->col2' />\n";
							}
							echo "<input type='hidden' name='liste_assoc[]' value='$lig0->col2' />\n";
							echo "</td>\n";

							//echo "<td style='text-align:center; background-color: rgb(150, 200, 240);'>Nouveau</td>\n";
							echo "<td class='nouveau'>Nouveau</td>\n";

							echo "<td style='text-align:center;'>\n";
							echo "$pers_id";
							//echo "<input type='hidden' name='new_".$cpt."_pers_id' value='$pers_id' />\n";
							echo "</td>\n";

							echo "<td style='text-align:center;'>\n";
							echo "$lig2->nom";
							//echo "<input type='hidden' name='new_".$cpt."_resp_nom' value=\"$lig2->nom\" />\n";
							echo "</td>\n";

							echo "<td style='text-align:center;'>\n";
							echo "$lig2->prenom";
							//echo "<input type='hidden' name='new_".$cpt."_resp_prenom' value=\"$lig2->prenom\" />\n";
							echo "</td>\n";

							// Existe-t-il déjà un numéro de responsable légal 1 ou 2 correspondant au nouvel arrivant?
							// Il peut y avoir en revanche plus d'un resp_legal=0

							//echo "<td style='text-align:center;";
							echo "<td";
							//$sql="SELECT 1=1 FROM responsables2 WHERE (pers_id!='$pers_id' AND ele_id='$ele_id' AND resp_legal='$resp_legal')";
							$sql="SELECT 1=1 FROM responsables2 WHERE (pers_id!='$pers_id' AND ele_id='$ele_id' AND resp_legal='$resp_legal' AND (resp_legal='1' OR resp_legal='2'))";
							$res3=mysql_query($sql);
							if(mysql_num_rows($res3)==0){
								//echo "'>\n";
								echo ">\n";
							}
							else{
								//echo " background-color: lightgreen;'>\n";
								echo " class='modif'>\n";
							}
							echo "$resp_legal";
							//echo "<input type='hidden' name='new_".$cpt."_resp_legal' value='$resp_legal' />\n";
							echo "</td>\n";

							echo "<td style='text-align:center;'>\n";
							echo "$pers_contact";
							//echo "<input type='hidden' name='new_".$cpt."_pers_contact' value='$pers_contact' />\n";
							echo "</td>\n";

							// Elève(s) associé(s)
							/*
							$sql="SELECT nom,prenom FROM eleves WHERE (ele_id='$ele_id')";
							$res4=mysql_query($sql);
							*/
							if(mysql_num_rows($res4)==0){
								echo "<td style='text-align:center; background-color:red;' colspan='3'>\n";
								echo "Aucun élève pour ele_id=$ele_id ???";
								echo "</td>\n";

								//=========================
								// AJOUT: boireaus 20071129
								//echo "<td style='text-align:center;'><input type='checkbox' name='suppr_resp[]' value='$pers_id' /></td>\n";
								//=========================
							}
							else{
								$lig4=mysql_fetch_object($res4);
								echo "<td style='text-align:center;'>\n";
								echo "$lig4->nom";
								//echo "<input type='hidden' name='new_".$cpt."_ele_nom' value=\"$lig4->nom\" />\n";
								echo "</td>\n";

								echo "<td style='text-align:center;'>\n";
								echo "$lig4->prenom";
								//echo "<input type='hidden' name='new_".$cpt."_ele_prenom' value=\"$lig4->prenom\" />\n";
								echo "</td>\n";

								echo "<td style='text-align:center;'>\n";
								echo "$ele_id";
								//echo "<input type='hidden' name='new_".$cpt."_ele_id' value='$ele_id' />\n";
								echo "</td>\n";

								//=========================
								// AJOUT: boireaus 20071129
								//echo "<td style='text-align:center;'>&nbsp;</td>\n";
								//=========================
							}
						}


						//=========================
						// AJOUT: boireaus 20071129

						// TESTER SI LE RESPONSABLE EST ASSOCIé AVEC UN ELEVE EXISTANT AU MOINS
						$sql="SELECT e.ele_id FROM eleves e, resp_pers rp, temp_responsables2_import r
										WHERE e.ele_id=r.ele_id AND
												r.pers_id=rp.pers_id AND
												rp.pers_id='$pers_id'";
						$test=mysql_query($sql);
						if(mysql_num_rows($test)>0) {
							//echo "<td style='text-align:center;'>&nbsp;</td>\n";
							echo "<td style='text-align:center;'>";
							//while($lig_tmp_test=mysql_fetch_object($test)){echo "$lig_tmp_test->ele_id - ";}
							echo "&nbsp;\n";
							echo "</td>\n";
						}
						else{
							echo "<td style='text-align:center;'><input type='checkbox' name='suppr_resp[]' value='$pers_id' /></td>\n";
						}
						//=========================


						echo "</tr>\n";
					}
					else{


						$lig1=mysql_fetch_object($res1);
						if((stripslashes($lig1->resp_legal)!=stripslashes($resp_legal))||
						(stripslashes($lig1->pers_contact)!=stripslashes($pers_contact))){
							// L'un des champs resp_legal ou pers_contact au moins a changé
							//$resp_modif[]="$affiche[0]:$affiche[1]";
							$resp_modif[]="$ele_id:$pers_id";


							$alt=$alt*(-1);
							/*
							echo "<tr style='background-color:";
							if($alt==1){
								echo "silver";
							}
							else{
								echo "white";
							}
							echo ";'>\n";
							*/
							echo "<tr class='lig$alt'>\n";

							$sql="SELECT nom,prenom FROM resp_pers WHERE (pers_id='$pers_id')";
							$res2=mysql_query($sql);
							if(mysql_num_rows($res2)==0){
								// Problème: On ne peut pas importer l'association sans que la personne existe.
								// Est-ce que l'étape d'import de la personne a été refusée?
								echo "<td>&nbsp;</td>\n";
								echo "<td>&nbsp;</td>\n";

								echo "<td style='background-color:red;'>&nbsp;</td>\n";
								echo "<td colspan='5'>Aucune personne associée???</td>\n";

								//=========================
								// AJOUT: boireaus 20071129
								//echo "<td style='text-align:center;'><input type='checkbox' name='suppr_resp[]' value='$pers_id' /></td>\n";
								//=========================
							}
							else{
								$lig2=mysql_fetch_object($res2);
								echo "<td style='text-align:center;'>\n";
								//echo "<input type='checkbox' id='check_".$cpt."' name='modif[]' value='$cpt' />";

								// Elève(s) associé(s)
								$sql="SELECT nom,prenom FROM eleves WHERE (ele_id='$ele_id')";
								$res4=mysql_query($sql);
								if(mysql_num_rows($res4)>0){
									echo "<input type='checkbox' id='check_".$cpt."' name='modif[]' value='$lig0->col2' />\n";
								}
								echo "<input type='hidden' name='liste_assoc[]' value='$lig0->col2' />\n";
								echo "</td>\n";

								//echo "<td style='text-align:center; background-color:lightgreen;'>Modif</td>\n";
								echo "<td class='modif'>Modif</td>\n";

								echo "<td style='text-align:center;'>\n";
								echo "$pers_id";
								//echo "<input type='hidden' name='modif_".$cpt."_pers_id' value='$pers_id' />\n";
								echo "</td>\n";

								echo "<td style='text-align:center;'>\n";
								echo "$lig2->nom";
								//echo "<input type='hidden' name='modif_".$cpt."_resp_nom' value=\"".addslashes($lig2->nom)."\" />\n";
								//echo "<input type='hidden' name='modif_".$cpt."_resp_nom' value=\"".$lig2->nom."\" />\n";
								echo "</td>\n";

								echo "<td style='text-align:center;'>\n";
								echo "$lig2->prenom";
								//echo "<input type='hidden' name='modif_".$cpt."_resp_prenom' value=\"".addslashes($lig2->nom)."\" />\n";
								//echo "<input type='hidden' name='modif_".$cpt."_resp_prenom' value=\"".$lig2->prenom."\" />\n";
								echo "</td>\n";

								// Existe-t-il déjà un numéro de responsable légal 1 ou 2 correspondant au nouvel arrivant?

								//echo "<td style='text-align:center;";
								echo "<td";
								//$sql="SELECT 1=1 FROM responsables2 WHERE (pers_id!='$pers_id' AND ele_id='$ele_id' AND resp_legal='$resp_legal')";
								$sql="SELECT 1=1 FROM responsables2 WHERE (pers_id!='$pers_id' AND ele_id='$ele_id' AND resp_legal='$resp_legal' AND (resp_legal='1' OR resp_legal='2'))";
								$res3=mysql_query($sql);
								if(mysql_num_rows($res3)==0){
									//echo "'>\n";
									echo ">\n";
								}
								else{
									//echo " background-color: lightgreen;'>\n";
									echo " class='modif'>\n";
								}
								echo "$resp_legal";
								//echo "<input type='hidden' name='modif_".$cpt."_resp_legal' value='$resp_legal' />\n";
								echo "</td>\n";

								echo "<td style='text-align:center;'>\n";
								echo "$pers_contact";
								//echo "<input type='hidden' name='modif_".$cpt."_pers_contact' value='$pers_contact' />\n";
								echo "</td>\n";

								// Elève(s) associé(s)
								//$sql="SELECT nom,prenom FROM eleves WHERE (ele_id='$ele_id')";
								//$res4=mysql_query($sql);
								if(mysql_num_rows($res4)==0){
									echo "<td style='text-align:center; background-color:red;' colspan='3'>\n";
									echo "Aucun élève pour ele_id=$ele_id ???";
									echo "</td>\n";

									//=========================
									// AJOUT: boireaus 20071129
									//echo "<td style='text-align:center;'><input type='checkbox' name='suppr_resp[]' value='$pers_id' /></td>\n";
									//=========================
								}
								else{
									$lig4=mysql_fetch_object($res4);
									echo "<td style='text-align:center;'>\n";
									echo "$lig4->nom";
									//echo "<input type='hidden' name='modif_".$cpt."_ele_nom' value=\"".addslashes($lig4->nom)."\" />\n";
									//echo "<input type='hidden' name='modif_".$cpt."_ele_nom' value=\"".$lig4->nom."\" />\n";
									echo "</td>\n";

									echo "<td style='text-align:center;'>\n";
									echo "$lig4->prenom";
									//echo "<input type='hidden' name='modif_".$cpt."_ele_prenom' value=\"".addslashes($lig4->prenom)."\" />\n";
									//echo "<input type='hidden' name='modif_".$cpt."_ele_prenom' value=\"".$lig4->prenom."\" />\n";
									echo "</td>\n";

									echo "<td style='text-align:center;'>\n";
									echo "$ele_id";
									//echo "<input type='hidden' name='modif_".$cpt."_ele_id' value='$ele_id' />\n";
									echo "</td>\n";

									//=========================
									// AJOUT: boireaus 20071129
									//echo "<td style='text-align:center;'>&nbsp;</td>\n";
									//=========================
								}

							}

							//=========================
							// AJOUT: boireaus 20071129

							// TESTER SI LE RESPONSABLE EST ASSOCIé AVEC UN ELEVE EXISTANT AU MOINS
							$sql="SELECT e.ele_id FROM eleves e, resp_pers rp, temp_responsables2_import r
											WHERE e.ele_id=r.ele_id AND
													r.pers_id=rp.pers_id AND
													rp.pers_id='$pers_id'";
							$test=mysql_query($sql);
							if(mysql_num_rows($test)>0) {
								//echo "<td style='text-align:center;'>&nbsp;</td>\n";
								echo "<td style='text-align:center;'>";
								//while($lig_tmp_test=mysql_fetch_object($test)){echo "$lig_tmp_test->ele_id - ";}
								echo "&nbsp;\n";
								echo "</td>\n";
							}
							else{
								echo "<td style='text-align:center;'><input type='checkbox' name='suppr_resp[]' value='$pers_id' /></td>\n";
							}
							//=========================

							echo "</tr>\n";
						}
						// Sinon, il n'est pas nécessaire de refaire l'inscription déjà présente.
					}

					//echo "</tr>\n";
					$cpt++;
				}
				echo "</table>\n";


				echo "<script type='text/javascript'>
	function modifcase(mode){
		for(i=0;i<$cpt;i++){
			if(document.getElementById('check_'+i)){
				if(mode=='coche'){
					document.getElementById('check_'+i).checked=true;
				}
				else{
					document.getElementById('check_'+i).checked=false;
				}
			}
		}
	}
</script>\n";

				echo "<input type='hidden' name='step' value='18' />\n";
				echo "<p align='center'><input type=submit value='Valider' /></p>\n";

				echo "<p><br /></p>\n";
				echo "<p><i>NOTES:</i></p>\n";
				echo "<ul>\n";
				echo "<li>La case de suppression d'un responsable n'est proposée que s'il n'est associé à aucun élève effectivement présent dans votre table 'eleves'.</li>\n";
				echo "<li>Le message 'Aucun élève pour ele_id=...' signifie que l'import fait référence à un identifiant d'élève qui n'est plus dans l'établissement ou qui était proposé à l'import des élèves et que vous n'avez pas coché.<br />Cela ne signifie pas que le responsable n'est pas associé à autre élève qui lui est bien présent dans votre table 'eleves'.</li>\n";
				echo "</ul>\n";

			}
			else{
				echo "<input type='hidden' name='step' value='19' />\n";

				//echo "<p align='center'><input type=submit value='Terminer' /></p>\n";
				echo "<p>Retour à:</p>\n";
				echo "<ul>\n";
				echo "<li><a href='../accueil.php'>l'accueil</a></li>\n";
				echo "<li><a href='index.php'>l'index Responsables</a></li>\n";
				echo "<li><a href='../eleves/index.php'>l'index Elèves</a></li>\n";
				echo "</ul>\n";
			}

			echo "</form>\n";

			break;
		case 19:
			echo "<h2>THE END ?</h2>\n";
			break;
	}
}

/*
echo "<p><i>NOTES:</i></p>\n";
echo "<ul>\n";
echo "<li>\n";
echo "<p>Les noms de fichiers fournis dans les champs de formulaires doivent coïncider avec le nom indiqué ELEVES.CSV, ADRESSES.CSV,...\n";
echo "</p>\n";
echo "</li>\n";
echo "<li>";
echo "<p>Il reste aussi à assurer l'import de l'établissement d'origine avec les fichiers etablissements.csv et eleves_etablissements.csv<br />\n";
echo "Par ailleurs, l'inscription des élèves dans telle ou telle classe, avec telle et telle option n'est pas encore assurée par cette page d'importation/mise à jour.<br />\n";
echo "(<i>il faut donc par la suite affecter les nouveaux élèves dans les classes et les inscrire dans les groupes/options/matières</i>)<br />\n";
echo "</p>\n";
echo "</li>\n";
echo "</ul>\n";
*/

echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
