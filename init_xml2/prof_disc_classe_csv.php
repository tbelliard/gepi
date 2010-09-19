<?php
@set_time_limit(0);
/*
* $Id$
*
* Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

//===========================================

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
/*
if($_SESSION['statut']!='administrateur') {
	header("Location: ../logout.php?auto=1");
	die();
}
*/
//===========================================

$step1=isset($_POST['step1']) ? $_POST['step1'] : (isset($_GET['step1']) ? $_GET['step1'] : NULL);
$suite=isset($_GET['suite']) ? $_GET['suite'] : NULL;

//=====================================
// AJOUT: boireaus
//$debug=1;
$debug=0;

if(isset($_GET['debug'])){
	if($_GET['debug']=="1"){
		$debug=1;
	}
	else{
		$debug=0;
	}
}

function affiche_debug($texte){
	global $debug;
	if($debug==1){
		echo "<font color='green'>$texte</font>\n";
		flush();
	}
}

//$debug=1;
//=====================================


//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : Importation des relations professeurs/classes/matières";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<p class="bold"><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil initialisation</a></p>
<?php

// On vérifie si l'extension d_base est active
//verif_active_dbase();

echo "<center><h3 class='gepi'>Cinquième phase d'initialisation" .
		"<br />Affectation des matières à chaque professeur," .
		"<br />Affectation des professeurs dans chaque classe," .
		"<br />Importation des options suivies par les élèves" .
		"</h3></center>";

echo "<h3 class='gepi'>Première étape : affectation des matières à chaque professeur et affectation des professeurs dans chaque classe.</h3>";

if (!isset($step1)) {
	$test = mysql_result(mysql_query("SELECT count(*) FROM j_groupes_professeurs"),0);
	if ($test != 0) {
		echo "<p><b>ATTENTION ...</b><br />";
		echo "Des données concernant l'affectation de professeurs dans des classes sont actuellement présentes dans la base GEPI<br /></p>";
		echo "<p>Si vous poursuivez la procédure ces données seront effacées.</p>";
		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>";
		echo "<input type=hidden name='step1' value='y' />";
		echo "<input type='submit' name='confirm' value='Poursuivre la procédure' />";
		echo "</form>";
		die();
	}
}

$tempdir=get_user_temp_directory();
if(!$tempdir){
	echo "<p style='color:red'>Il semble que le dossier temporaire de l'utilisateur ".$_SESSION['login']." ne soit pas défini!?</p>\n";
	// Il ne faut pas aller plus loin...
	// SITUATION A GERER
}

//if (!isset($is_posted)) {
if (!isset($suite)) {

	$del = @mysql_query("DELETE FROM j_groupes_professeurs");
	$del = @mysql_query("DELETE FROM j_professeurs_matieres");

	/*
	echo "<p>Importation des fichiers <b>F_men.csv</b> et <b>F_gpd.csv</b> contenant les données de relations entre professeurs, matière et classes.";
	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method=post>";
	echo "<p>Veuillez préciser le nom complet du fichier <b>F_men.csv</b>.";
	echo "<p><input type='file' size='80' name='dbf_file' />";
	echo "<p>Veuillez préciser le nom complet du fichier <b>F_gpd.csv</b>.";
	echo "<p><input type='file' size='80' name='dbf_file2' />";
	echo "<input type='hidden' name='is_posted' value='yes' />";
	echo "<input type='hidden' name='step1' value='y' />";
	echo "<p><input type='submit' value='Valider' />";
	echo "</form>";
	*/

	$dest_file="../temp/".$tempdir."/sts.xml";
	$fp=fopen($dest_file,"r");
	if(!$fp){
		echo "<p>Le XML STS Emploi du temps n'a pas l'air présent dans le dossier temporaire.<br />Auriez-vous sauté une étape???</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	/*
	echo "<p>Lecture du fichier STS Emploi du temps...<br />\n";
	while(!feof($fp)){
		$ligne[]=fgets($fp,4096);
	}
	fclose($fp);
	*/
	flush();



	echo "Analyse du fichier pour extraire les informations de la section DIVISIONS...<br />\n";

	$cpt=0;
	$temoin_structure=0;
	$temoin_groupes=0;
	$temoin_divisions=0;
	$temoin_div=-1;
	$temoin_services=-1;
	$temoin_disc=-1;
	$temoin_enseignants=-1;
	$temoin_grp=-1;
	$temoin_div_appart=-1;

	//==================
	// Ajout 20081001: pour les groupes
	$temoin_service="";
	//==================

	//while($cpt<count($ligne)){
	while(!feof($fp)){
		$ligne=fgets($fp,4096);
		// On va récupérer les divisions et associations profs/matières...
		//if(strstr($ligne[$cpt],"<STRUCTURE>")){
		if(strstr($ligne,"<STRUCTURE>")){
			echo "Début de la section STRUCTURE à la ligne <span style='color: blue;'>$cpt</span><br />\n";
			flush();
			$temoin_structure++;
		}
		//if(strstr($ligne[$cpt],"</STRUCTURE>")){
		if(strstr($ligne,"</STRUCTURE>")){
			echo "Fin de la section STRUCTURE à la ligne <span style='color: blue;'>$cpt</span><br />\n";
			flush();
			$temoin_structure++;
		}
		if($temoin_structure==1){
			//if(strstr($ligne[$cpt],"<DIVISIONS>")){
			if(strstr($ligne,"<DIVISIONS>")){
				echo "Début de la section DIVISIONS à la ligne <span style='color: blue;'>$cpt</span><br />\n";
				$temoin_divisions++;
				$divisions=array();
				$i=0;
			}
			//if(strstr($ligne[$cpt],"</DIVISIONS>")){
			if(strstr($ligne,"</DIVISIONS>")){
				echo "Fin de la section DIVISIONS à la ligne <span style='color: blue;'>$cpt</span><br />\n";
				$temoin_divisions++;
			}
			if($temoin_divisions==1){
				/*
				if(strstr($ligne[$cpt],"<DIVISION CODE=")){
					$temoin_div=1;
					unset($tabtmp);
					$tabtmp=explode('"',$ligne[$cpt]);
					$divisions[$i]["code"]=trim($tabtmp[1]);
				}
				*/
				//if(strstr($ligne[$cpt],"<DIVISION ")){
				if(strstr($ligne,"<DIVISION ")){
					$temoin_div=1;
					unset($tabtmp);
					//$tabtmp=explode('"',strstr($ligne[$cpt]," CODE="));
					$tabtmp=explode('"',strstr($ligne," CODE="));
					$divisions[$i]["code"]=trim($tabtmp[1]);
				}
				//if(strstr($ligne[$cpt],"</DIVISION>")){
				if(strstr($ligne,"</DIVISION>")){
					$temoin_div=0;
					$i++;
				}

				if($temoin_div==1){
					//if(strstr($ligne[$cpt],"<SERVICES>")){
					if(strstr($ligne,"<SERVICES>")){
						$temoin_services=1;
						$j=0;
					}
					//if(strstr($ligne[$cpt],"</SERVICES>")){
					if(strstr($ligne,"</SERVICES>")){
						$temoin_services=0;
					}

					if($temoin_services==1){
						/*
						if(strstr($ligne[$cpt],"<SERVICE CODE_MATIERE=")){
							$temoin_disc=1;
							unset($tabtmp);
							$tabtmp=explode('"',$ligne[$cpt]);
							$divisions[$i]["services"][$j]["code_matiere"]=trim($tabtmp[1]);
						}
						*/
						//if(strstr($ligne[$cpt],"<SERVICE ")){
						if(strstr($ligne,"<SERVICE ")){
							$temoin_disc=1;
							unset($tabtmp);
							//$tabtmp=explode('"',strstr($ligne[$cpt]," CODE_MATIERE="));
							$tabtmp=explode('"',strstr($ligne," CODE_MATIERE="));
							$divisions[$i]["services"][$j]["code_matiere"]=trim($tabtmp[1]);
						}
						//if(strstr($ligne[$cpt],"</SERVICE>")){
						if(strstr($ligne,"</SERVICE>")){
							$temoin_disc=0;
							$j++;
						}

						if($temoin_disc==1){
							//if(strstr($ligne[$cpt],"<ENSEIGNANTS>")){
							if(strstr($ligne,"<ENSEIGNANTS>")){
								$temoin_enseignants=1;
								$divisions[$i]["services"][$j]["enseignants"]=array();
								$k=0;
							}
							//if(strstr($ligne[$cpt],"</ENSEIGNANTS>")){
							if(strstr($ligne,"</ENSEIGNANTS>")){
								$temoin_enseignants=0;
							}
							if($temoin_enseignants==1){
								/*
								if(strstr($ligne[$cpt],"<ENSEIGNANT ID=")){
									//$temoin_ens=1;
									unset($tabtmp);
									$tabtmp=explode('"',$ligne[$cpt]);
									$divisions[$i]["services"][$j]["enseignants"][$k]["id"]=trim($tabtmp[1]);
								}
								*/
								//if(strstr($ligne[$cpt],"<ENSEIGNANT ")){
								if(strstr($ligne,"<ENSEIGNANT ")){
									//$temoin_ens=1;
									unset($tabtmp);
									//$tabtmp=explode('"',strstr($ligne[$cpt]," ID="));
									$tabtmp=explode('"',strstr($ligne," ID="));
									$divisions[$i]["services"][$j]["enseignants"][$k]["id"]=trim($tabtmp[1]);
								}
								//if(strstr($ligne[$cpt],"</ENSEIGNANT>")){
								if(strstr($ligne,"</ENSEIGNANT>")){
									//$temoin_ens=0;
									$k++;
								}
							}
						}
					}
				}
			}





			//echo "Analyse du fichier pour extraire les informations de la section GROUPES...<br />\n";

			//if(strstr($ligne[$cpt],"<GROUPES>")){
			if(strstr($ligne,"<GROUPES>")){
				echo "Début de la section GROUPES à la ligne <span style='color: blue;'>$cpt</span><br />\n";
				flush();
				$temoin_groupes++;
				$groupes=array();
				$i=0;
			}
			//if(strstr($ligne[$cpt],"</GROUPES>")){
			if(strstr($ligne,"</GROUPES>")){
				echo "Fin de la section GROUPES à la ligne <span style='color: blue;'>$cpt</span><br />\n";
				flush();
				$temoin_groupes++;
			}
			if($temoin_groupes==1){
				/*
				if(strstr($ligne[$cpt],"<GROUPE CODE=")){
					$temoin_grp=1;
					unset($tabtmp);
					$tabtmp=explode('"',$ligne[$cpt]);
					$groupes[$i]=array();
					$groupes[$i]["code"]=trim($tabtmp[1]);
					$j=0;
					$m=0;
				}
				*/
				//if(strstr($ligne[$cpt],"<GROUPE ")){
				if(strstr($ligne,"<GROUPE ")){
					$temoin_grp=1;
					unset($tabtmp);
					//$tabtmp=explode('"',strstr($ligne[$cpt]," CODE="));
					$tabtmp=explode('"',strstr($ligne," CODE="));
					$groupes[$i]=array();
					$groupes[$i]["code"]=trim($tabtmp[1]);

					//echo "<p>GROUPE: ".$groupes[$i]["code"]."<br />";

					//==================
					// Ajout 20081001:
					$groupes[$i]['grp']=array();
					$i_grp=0;
					//==================

					$j=0;
					$m=0;
				}
				//if(strstr($ligne[$cpt],"</GROUPE>")){
				if(strstr($ligne,"</GROUPE>")){
					$temoin_grp=0;
					$i++;

					//echo "<br /></p>\n";
				}

				if($temoin_grp==1){
					//if(strstr($ligne[$cpt],"<LIBELLE_LONG>")){
					if(strstr($ligne,"<LIBELLE_LONG>")){
						// IL ARRIVE QUE L'ON AIT <LIBELLE_LONG/>... faut-il alors remplir le libelle_long avec $groupes[$i]["code"] ??
						unset($tabtmp);
						//$tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
						$tabtmp=explode(">",my_ereg_replace("<",">",$ligne));

						// Suppression des guillemets éventuels
						//$groupes[$i]["libelle_long"]=trim($tabtmp[2]);
						$groupes[$i]["libelle_long"]=my_ereg_replace('"','',trim($tabtmp[2]));
					}
					elseif(strstr($ligne,"<LIBELLE_LONG/>")){
						$groupes[$i]["libelle_long"]=$groupes[$i]["code"];
					}

					//if(strstr($ligne[$cpt],"<DIVISIONS_APPARTENANCE>")){
					if(strstr($ligne,"<DIVISIONS_APPARTENANCE>")){
						$temoin_div_appart=1;
					}
					//if(strstr($ligne[$cpt],"</DIVISIONS_APPARTENANCE>")){
					if(strstr($ligne,"</DIVISIONS_APPARTENANCE>")){
						$temoin_div_appart=0;
					}

					if($temoin_div_appart==1){
						/*
						if(strstr($ligne[$cpt],"<DIVISION_APPARTENANCE CODE=")){
							unset($tabtmp);
							$tabtmp=explode('"',$ligne[$cpt]);
							$groupes[$i]["divisions"][$j]["code"]=trim($tabtmp[1]);
							$j++;
						}
						*/
						//if(strstr($ligne[$cpt],"<DIVISION_APPARTENANCE ")){
						if(strstr($ligne,"<DIVISION_APPARTENANCE ")){
							unset($tabtmp);
							//$tabtmp=explode('"',strstr($ligne[$cpt]," CODE="));
							$tabtmp=explode('"',strstr($ligne," CODE="));
							$groupes[$i]["divisions"][$j]["code"]=trim($tabtmp[1]);
							$j++;
						}
					}


					//<SERVICE CODE_MATIERE="020100" CODE_MOD_COURS="CG">
					/*
					if(strstr($ligne[$cpt],"<SERVICE CODE_MATIERE=")){
						unset($tabtmp);
						$tabtmp=explode('"',$ligne[$cpt]);
						$groupes[$i]["code_matiere"]=trim($tabtmp[1]);
					}
					*/
					//if(strstr($ligne[$cpt],"<SERVICE ")){
					if(strstr($ligne,"<SERVICE ")){
						unset($tabtmp);
						//$tabtmp=explode('"',strstr($ligne[$cpt]," CODE_MATIERE="));

						//==================
						// MODIF 20081001:
						//$tabtmp=explode('"',strstr($ligne," CODE_MATIERE="));
						//$groupes[$i]["code_matiere"]=trim($tabtmp[1]);

						$temoin_service=1;
						$tabtmp=explode('"',strstr($ligne," CODE_MATIERE="));
						$groupes[$i]['grp'][$i_grp]["code_matiere"]=trim($tabtmp[1]);

						$groupes[$i]['grp'][$i_grp]["enseignant"]=array();

						$m=0;
						//==================

					}

					//==================
					// AJOUT 20081001:
					if(strstr($ligne,"</SERVICE>")){
						$temoin_service=0;
						$i_grp++;
					}
					//==================

					//<ENSEIGNANT TYPE="epp" ID="31762">
					// Améliorer la récup de l'attribut ID...
					// ...découper en un tableau avec ' '
					// et rechercher quel champ du tableau commence par ID=

					//<ENSEIGNANT ID="11508" TYPE="epp">

					//if(strstr($ligne[$cpt],"<ENSEIGNANT TYPE=")){
					/*
					if(strstr($ligne[$cpt],"<ENSEIGNANT ID=")){
						unset($tabtmp);
						$tabtmp=explode('"',$ligne[$cpt]);
						//$groupes[$i]["enseignant"][$m]["id"]=$tabtmp[3];
						$groupes[$i]["enseignant"][$m]["id"]=trim($tabtmp[1]);
						$m++;
					}
					*/
					//if(strstr($ligne[$cpt],"<ENSEIGNANT ")){

					//==================
					// AJOUT 20081001:
					if($temoin_service==1) {
					//==================
						if(strstr($ligne,"<ENSEIGNANT ")){
							unset($tabtmp);
							//$tabtmp=explode('"',strstr($ligne[$cpt]," ID="));
							$tabtmp=explode('"',strstr($ligne," ID="));
							//$groupes[$i]["enseignant"][$m]["id"]=$tabtmp[3];

							//==================
							// MODIF 20081001:
							//$groupes[$i]["enseignant"][$m]["id"]=trim($tabtmp[1]);

							$groupes[$i]['grp'][$i_grp]["enseignant"][$m]["id"]=trim($tabtmp[1]);

							//echo "\$groupes[$i]['grp'][$i_grp]['enseignant'][$m]['id']=".$groupes[$i]['grp'][$i_grp]["enseignant"][$m]["id"]."<br />";
							//==================

							$m++;
						}
					//==================
					// AJOUT 20081001:
					}
					//==================
				}
			}
		}
		$cpt++;
	}
	fclose($fp);


	//==================
	// MODIF 20081001:
	/*
	$tabfich=array("f_men.csv","f_gpd.csv");
	for($i=0;$i<count($tabfich);$i++){
		if(file_exists("../temp/$tempdir/$tabfich[$i]")){
			echo "<p>Suppression du précédent $tabfich[$i]... ";
			if(unlink("../temp/$tempdir/$tabfich[$i]")){
				echo "réussie.</p>\n";
			}
			else{
				echo "<font color='red'>Echec!</font> Vérifiez les droits d'écriture sur le serveur.</p>\n";
			}
		}
	}
	*/
	//==================


	// On récupère les correspondances code/code_gestion sur les matières.
	$sql="SELECT code,code_gestion FROM temp_matieres_import";
	$res_mat=mysql_query($sql);
	if(mysql_num_rows($res_mat)==0){
		echo "<p>La table 'temp_matieres_import' est vide.<br />Auriez-vous sauté des étapes???</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	$cpt=0;
	while($lig_mat=mysql_fetch_object($res_mat)){
		$matiere[$cpt]=array();
		$matiere[$cpt]["code"]=$lig_mat->code;
		$matiere[$cpt]["code_gestion"]=$lig_mat->code_gestion;
		//echo "$lig_mat->code;$lig_mat->code_gestion<br />";
		$cpt++;
	}

	function get_code_gestion_from_code($id_mat) {
		global $matiere;
		for($m=0;$m<count($matiere);$m++){
			if($matiere[$m]["code"]==$id_mat){
				return $matiere[$m]["code_gestion"];
				break;
			}
		}
	}

	function get_nom_complet_from_matiere($mat) {
		$sql="SELECT nom_complet FROM matieres WHERE matiere='$mat';";
		$res_mat=mysql_query($sql);
		if(mysql_num_rows($res_mat)>0) {
			$lig_mat=mysql_fetch_object($res_mat);
			return $lig_mat->nom_complet;
		}
	}

	echo "<hr />\n";

	echo "<p>A cette étape, les élèves vont être affectés dans tous les groupes.<br />Ce n'est qu'à l'étape suivante que les options vont être prises en compte pour élaguer les groupes.</p>\n";
	// A REVOIR... à moins que cette page soit déjà longue en traitement...

	$temoin_div_sans_services=0;
	$temoin_service_sans_enseignant=0;
	echo "<h3>Création des groupes classe entière</h3>\n";
	for ($i=0;$i<count($divisions);$i++) {
		$classe=$divisions[$i]['code'];

		echo "<p class='bold'>Classe de $classe</p>\n";
		echo "<blockquote>\n";

		$sql="SELECT id FROM classes WHERE classe='$classe';";
		$res_clas=mysql_query($sql);
		$nb_clas=mysql_num_rows($res_clas);
		if($nb_clas>1) {
			echo "<p style='color:red;'>ANOMALIE: ".$nb_clas." classes ont le même nom: ".$classe."<br />Les groupes ne peuvent pas être importés pour ces classes.</p>\n";
		}
		elseif($nb_clas==1) {

			$lig_clas=mysql_fetch_object($res_clas);
			$id_classe=$lig_clas->id;

			// Récupération des élèves de la classe
			$sql="SELECT DISTINCT login FROM j_eleves_classes WHERE id_classe='$id_classe' ORDER BY login;";
			$res_ele=mysql_query($sql);
			$tab_ele=array();
			if(mysql_num_rows($res_ele)>0) {
				while($lig_ele=mysql_fetch_object($res_ele)) {
					$tab_ele[]=$lig_ele->login;
				}
			}

			// Récupération des périodes de la classe
			$tab_per=array();
			$periode_query=mysql_query("SELECT * FROM periodes WHERE id_classe='$id_classe' ORDER BY num_periode");
			$nb_periode=mysql_num_rows($periode_query)+1;
			$j="1";
			while ($j<$nb_periode) {
				$tab_per[]=$j;
				$j++;
			}

			$tab_clas=array($id_classe);

			if(!isset($divisions[$i]['services'])) {
				echo "<p style='color:red;'>Aucun service n'est défini pour cette classe.<br />L'emploi du temps a-t-il été remonté vers STS?</p>\n";
				$temoin_div_sans_services++;
			}
			else {
				for($j=0;$j<count($divisions[$i]['services']);$j++) {
					$id_mat=$divisions[$i]['services'][$j]['code_matiere'];
					$mat=get_code_gestion_from_code($id_mat);
	
					$nom_grp=$mat;
					$descr_grp=get_nom_complet_from_matiere($mat);
	
					// Créer le groupe:                   groupes
					// L'associer à la classe:            j_groupes_classes
					// L'associer à la matière:           j_groupes_matieres
					echo "<p>Création du groupe $descr_grp (<i>$nom_grp</i>) en $classe: ";
					if($id_groupe=create_group($nom_grp, $descr_grp, $mat, $tab_clas)) {
						echo "<span style='color:green;'>$id_groupe</span>";
						//echo "<br />\n";
						echo "<blockquote>\n";
	
						echo "Professeur(s): ";
						if(!isset($divisions[$i]['services'][$j]['enseignants'])) {
							echo "<p style='color:red;'>Aucun enseignant n'est associé à ce service.<br />L'emploi du temps a-t-il été correctement renseigné lors de la remontée vers STS?</p>\n";
							$temoin_service_sans_enseignant++;
						}
						else {
	
							for($k=0;$k<count($divisions[$i]['services'][$j]['enseignants']);$k++) {
		
								$sql="select col1 from tempo2 where col2='P".$divisions[$i]['services'][$j]['enseignants'][$k]['id']."';";
								$res_prof=mysql_query($sql);
								$login_prof=@mysql_result($res_prof, 0, 'col1');
		
								if ($login_prof!='') {
									// Associer le groupe au prof:    j_groupes_professeurs
									$sql="SELECT 1=1 FROM j_groupes_professeurs WHERE id_groupe='$id_groupe' AND login='$login_prof';";
									$res_grp_prof=mysql_query($sql);
									if(mysql_num_rows($res_grp_prof)==0) {
										$sql="INSERT INTO j_groupes_professeurs SET id_groupe='$id_groupe', login='$login_prof';";
										if($insert=mysql_query($sql)) {
											echo "<span style='color:green;'>";
										}
										else {
											echo "<span style='color:red;'>";
										}
										echo "$login_prof</span>";
									}
		
									// Associer le prof à la matière: j_professeurs_matieres
									$sql="SELECT 1=1 FROM j_professeurs_matieres WHERE id_matiere='$mat' AND id_professeur='$login_prof';";
									$res_prof_mat=mysql_query($sql);
									echo " (";
									if(mysql_num_rows($res_prof_mat)==0) {
										$sql="INSERT INTO j_professeurs_matieres SET id_matiere='$mat', id_professeur='$login_prof';";
										if($insert=mysql_query($sql)) {
											echo "<span style='color:green;'>";
										}
										else {
											echo "<span style='color:red;'>";
										}
									}
									else {
										echo "<span style='color:black;'>";
									}
									echo "$mat</span>)";
		
								}
								//else {echo "prof inconnu";}
							}
						}
						echo "<br />\n";
	
						// Mettre tous les élèves dans le groupe pour toutes les périodes: j_eleves_groupes
						echo "Association des élèves:<br />";
						echo "<blockquote>\n";
						for($k=0;$k<count($tab_ele);$k++) {
							if($k>0) {echo " - ";}
							echo $tab_ele[$k]." (";
							for($l=0;$l<count($tab_per);$l++) {
								if($l>0) {echo "-";}
								$sql="SELECT 1=1 FROM j_eleves_groupes WHERE id_groupe='$id_groupe' AND login='".$tab_ele[$k]."' AND periode='".$tab_per[$l]."';";
								$res_ele_grp=mysql_query($sql);
								if(mysql_num_rows($res_ele_grp)==0) {
									$sql="INSERT INTO j_eleves_groupes SET id_groupe='$id_groupe', login='".$tab_ele[$k]."', periode='".$tab_per[$l]."';";
									if($insert=mysql_query($sql)) {
										echo "<span style='color:green;'>";
									}
									else {
										//echo "$sql<br />\n";
										echo "<span style='color:red;'>";
									}
								}
								else {
									echo "<span style='color:black;'>";
								}
								echo $tab_per[$l]."</span>";
							}
							echo ")";
						}
						echo "</blockquote>\n";
	
						echo "</blockquote>\n";
					}
					else {
						echo "<span style='color:red;'>ERREUR</span>";
					}
					echo "</p>\n";
				}
			}
		}
		echo "</blockquote>\n";
	}

	echo "<h3>Création des groupes</h3>\n";
	// Traiter les groupes ensuite
	if(!isset($groupes)) {
		echo "<p>Aucun groupe n'est défini.</p>\n";		
	}
	else {
		for ($i=0;$i<count($groupes);$i++) {
			$code_groupe=$groupes[$i]['code'];
			$libelle_groupe=$groupes[$i]['libelle_long'];
	
			$tab_clas=array();
			$tab_ele=array();
			$tab_per_clas=array();
	
			$list_classe="";
	
			$cpt_clas=0;
	
			for ($ii=0;$ii<count($groupes[$i]['divisions']);$ii++) {
				$classe=$groupes[$i]['divisions'][$ii]['code'];
				$sql="SELECT id FROM classes WHERE classe='$classe';";
				$res_clas=mysql_query($sql);
				$nb_clas=mysql_num_rows($res_clas);
				if($nb_clas==1) {
					$lig_clas=mysql_fetch_object($res_clas);
					$id_classe=$lig_clas->id;
	
					// Récupération des périodes de la classe
					//$tab_per=array();
					$periode_query=mysql_query("SELECT MAX(num_periode) FROM periodes WHERE id_classe='$id_classe';");
					if(mysql_num_rows($periode_query)>0) {
						$tab_per_clas[]=mysql_result($periode_query,0);
	
						$tab_test=array_unique($tab_per_clas);
						if(count($tab_test)==1) {
							// Récupération des élèves de la classe
							$sql="SELECT DISTINCT login FROM j_eleves_classes WHERE id_classe='$id_classe' ORDER BY login;";
							$res_ele=mysql_query($sql);
							if(mysql_num_rows($res_ele)>0) {
								while($lig_ele=mysql_fetch_object($res_ele)) {
									$tab_ele[]=$lig_ele->login;
								}
							}
	
							//$tab_clas[]=$classe;
							//$tab_id_clas[]=$id_classe;
							$tab_clas[]=$id_classe;
	
							if($list_classe!="") {$list_classe.=", ";}
							$list_classe.=$classe;
						}
						else {
							echo "<p style='color:red'>PB: Des classes n'ayant pas le même nombre de périodes sont associées dans un même groupe.</p>\n";
						}
					}
				}
			}
	
	
	
			for($i_grp=0;$i_grp<count($groupes[$i]['grp']);$i_grp++) {
				$id_mat=$groupes[$i]['grp'][$i_grp]['code_matiere'];
				$mat=get_code_gestion_from_code($id_mat);
	
				$nom_grp=$mat;
				$descr_grp=get_nom_complet_from_matiere($mat)." (".$code_groupe.")";
	
				echo "<p>Création du groupe $descr_grp (<i>$nom_grp</i>) en $list_classe";
				echo " (<i style='font-size:x-small;'>nom sts: ".$code_groupe."</i>)";
				echo ": ";
				if($id_groupe=create_group($nom_grp, $descr_grp, $mat, $tab_clas)) {
					echo "<span style='color:green;'>$id_groupe</span>";
					//echo "<br />\n";
					echo "<blockquote>\n";
	
					echo "Professeur(s): ";
					for($k=0;$k<count($groupes[$i]['grp'][$i_grp]['enseignant']);$k++) {
						if($k>0) {echo ", ";}
	
						$sql="select col1 from tempo2 where col2='P".$groupes[$i]['grp'][$i_grp]['enseignant'][$k]['id']."';";
						$res_prof=mysql_query($sql);
						$login_prof=@mysql_result($res_prof, 0, 'col1');
	
						if ($login_prof!='') {
							// Associer le groupe au prof:    j_groupes_professeurs
							$sql="SELECT 1=1 FROM j_groupes_professeurs WHERE id_groupe='$id_groupe' AND login='$login_prof';";
							$res_grp_prof=mysql_query($sql);
							if(mysql_num_rows($res_grp_prof)==0) {
								$sql="INSERT INTO j_groupes_professeurs SET id_groupe='$id_groupe', login='$login_prof';";
								if($insert=mysql_query($sql)) {
									echo "<span style='color:green;'>";
								}
								else {
									echo "<span style='color:red;'>";
								}
								echo "$login_prof</span>";
							}
	
							// Associer le prof à la matière: j_professeurs_matieres
							$sql="SELECT 1=1 FROM j_professeurs_matieres WHERE id_matiere='$mat' AND id_professeur='$login_prof';";
							$res_prof_mat=mysql_query($sql);
							echo " (";
							if(mysql_num_rows($res_prof_mat)==0) {
								$sql="INSERT INTO j_professeurs_matieres SET id_matiere='$mat', id_professeur='$login_prof';";
								if($insert=mysql_query($sql)) {
									echo "<span style='color:green;'>";
								}
								else {
									echo "<span style='color:red;'>";
								}
							}
							else {
								echo "<span style='color:black;'>";
							}
							echo "$mat</span>)";
	
						}
						//else {echo "prof inconnu";}
					}
					echo "<br />\n";
	
	
					// Mettre tous les élèves dans le groupe pour toutes les périodes: j_eleves_groupes
					echo "Association des élèves:<br />";
					echo "<blockquote>\n";
					for($k=0;$k<count($tab_ele);$k++) {
						if($k>0) {echo " - ";}
						echo $tab_ele[$k]." (";
						for($l=0;$l<count($tab_per);$l++) {
							if($l>0) {echo "-";}
							$sql="SELECT 1=1 FROM j_eleves_groupes WHERE id_groupe='$id_groupe' AND login='".$tab_ele[$k]."' AND periode='".$tab_per[$l]."';";
							$res_ele_grp=mysql_query($sql);
							if(mysql_num_rows($res_ele_grp)==0) {
								$sql="INSERT INTO j_eleves_groupes SET id_groupe='$id_groupe', login='".$tab_ele[$k]."', periode='".$tab_per[$l]."';";
								if($insert=mysql_query($sql)) {
									echo "<span style='color:green;'>";
								}
								else {
									//echo "$sql<br />\n";
									echo "<span style='color:red;'>";
								}
							}
							else {
								echo "<span style='color:black;'>";
							}
							echo $tab_per[$l]."</span>";
						}
						echo ")";
					}
					echo "</blockquote>\n";
	
					echo "</blockquote>\n";
	
	
				}
	
			}
		}
	}



	/*
	if ($nb_reg_no != 0) {
		echo "<p>Lors de l'enregistrement des données il n'y a eu $nb_reg_no erreurs. Essayez de trouvez la cause de l'erreur et recommencez la procédure avant de passer à l'étape suivante.";
	} else {
	*/

	if($temoin_div_sans_services==0) {
		echo "<p>L'importation des relations professeurs/matières et professeurs/classes dans la base GEPI a été effectuée avec succès !<br />Vous pouvez procéder à l'étape suivante d'importation des options suivies par les élèves.</p>";
	}
	elseif($temoin_div_sans_services==1) {
		echo "<p style='color:red;'>$temoin_div_sans_services division n'a pas de services déclarés.<br />Le fichier STS fourni n'est peut-être pas complet.<br />Cela arrive notamment quand l'emploi du temps n'a pas été remonté vers STS.<br />Vous devriez contrôler cela avant de procéder à l'étape d'importation des options suivies par les élèves.</p>\n";
	}
	else {
		echo "<p style='color:red;'>$temoin_div_sans_services divisions n'ont pas de services déclarés.<br />Le fichier STS fourni n'est peut-être pas complet.<br />Cela arrive notamment quand l'emploi du temps n'a pas été remonté vers STS.<br />Vous devriez contrôler cela avant de procéder à l'étape d'importation des options suivies par les élèves.</p>\n";
	}

	if($temoin_service_sans_enseignant==1) {
		echo "<p style='color:red;'>$temoin_service_sans_enseignant enseignement (<i>service</i>) a été déclaré sans enseignant associé.<br />Les élèves pratiquent-ils l'auto-formation en autonomie ou le STS est-il mal renseigné?</p>\n";
	}
	elseif($temoin_service_sans_enseignant>1) {
		echo "<p style='color:red;'>$temoin_service_sans_enseignant enseignements (<i>services</i>) ont été déclarés sans enseignant associé.<br />Les élèves pratiquent-ils l'auto-formation en autonomie ou le STS est-il mal renseigné?</p>\n";
	}

	//}
	echo "<p align='center'><a href='init_options.php'>Importer les options suivies par les élèves</a></p>\n";
	echo "<p><br /></p>\n";



	require("../lib/footer.inc.php");
	die();

	//========================================================================
	//========================================================================
	//========================================================================




	// On génère le f_men.csv
	$fich=fopen("../temp/$tempdir/f_men.csv","w+");
	if(!$fich){
		echo "<p>Erreur lors de la création du fichier 'f_men.csv'</p>\n";
		echo "<p>Il n'est pas possible de poursuivre.<br />Contrôlez les droits/propriétaires sur votre dossier temporaire, l'espace disponible,... et retentez l'opération.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$chaine="MATIMN;NUMIND;ELSTCO";
	if($fich){
		fwrite($fich,html_entity_decode_all_version($chaine)."\n");
	}
	affiche_debug($chaine."<br />\n");
	for($i=0;$i<count($divisions);$i++){
		//$divisions[$i]["services"][$j]["code_matiere"]
		$classe=$divisions[$i]["code"];
		for($j=0;$j<count($divisions[$i]["services"]);$j++){
			$mat="";
			for($m=0;$m<count($matiere);$m++){
				if($matiere[$m]["code"]==$divisions[$i]["services"][$j]["code_matiere"]){
					$mat=$matiere[$m]["code_gestion"];
				}
			}
			if($mat!=""){
				if(isset($divisions[$i]["services"][$j]["enseignants"])) {
					for($k=0;$k<count($divisions[$i]["services"][$j]["enseignants"]);$k++){
						$chaine=$mat.";P".$divisions[$i]["services"][$j]["enseignants"][$k]["id"].";".$classe;
						if($fich){
							fwrite($fich,html_entity_decode_all_version($chaine)."\n");
						}
						affiche_debug($chaine."<br />\n");
					}
				}
			}
		}
	}

	for($i=0;$i<count($groupes);$i++){
		$grocod=$groupes[$i]["code"];
		affiche_debug("<p>Groupe $i: \$grocod=$grocod<br />\n");
		for($m=0;$m<count($matiere);$m++){
			if(isset($groupes[$i]["code_matiere"])){
				affiche_debug("\$matiere[$m][\"code\"]=".$matiere[$m]["code"]." et \$groupes[$i][\"code_matiere\"]=".$groupes[$i]["code_matiere"]."<br />\n");
				if($matiere[$m]["code"]==$groupes[$i]["code_matiere"]){
					//$matimn=$programme[$k]["code_matiere"];
					$matimn=$matiere[$m]["code_gestion"];
					affiche_debug("<b>Trouvé: matière n°$m: \$matimn=$matimn</b><br />\n");
					break;
				}
			}
		}
		//$groupes[$i]["enseignant"][$m]["id"]
		//$groupes[$i]["divisions"][$j]["code"]
		if(isset($matimn)) {
			if($matimn!=""){
				for($j=0;$j<count($groupes[$i]["divisions"]);$j++){
					$elstco=$groupes[$i]["divisions"][$j]["code"];
					affiche_debug("\$elstco=$elstco<br />\n");
					if(isset($groupes[$i]["enseignant"])){
						if(count($groupes[$i]["enseignant"])==0){
							$chaine="$matimn;;$elstco";
							if($fich){
								fwrite($fich,html_entity_decode_all_version($chaine)."\n");
							}
							affiche_debug($chaine."<br />\n");
						}
						else{
							for($m=0;$m<count($groupes[$i]["enseignant"]);$m++){
								$numind=$groupes[$i]["enseignant"][$m]["id"];
								//echo "$matimn;P$numind;$elstco<br />\n";
								$chaine="$matimn;P$numind;$elstco";
								if($fich){
									fwrite($fich,html_entity_decode_all_version($chaine)."\n");
								}
								affiche_debug($chaine."<br />\n");
							}
						}
					}
					else{
						$chaine="$matimn;;$elstco";
						if($fich){
							fwrite($fich,html_entity_decode_all_version($chaine)."\n");
						}
						affiche_debug($chaine."<br />\n");
					}
				}
			}
		}
	}
	fclose($fich);


	// On génère le f_gpd.csv
	$fich=fopen("../temp/$tempdir/f_gpd.csv","w+");
	if(!$fich){
		echo "<p>Erreur lors de la création du fichier 'f_gpd.csv'</p>\n";
		echo "<p>Il n'est pas possible de poursuivre.<br />Contrôlez les droits/propriétaires sur votre dossier temporaire, l'espace disponible,... et retentez l'opération.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$chaine="GROCOD;DIVCOD";
	if($fich){
		fwrite($fich,html_entity_decode_all_version($chaine)."\n");
	}
	affiche_debug($chaine."<br />\n");

	for($i=0;$i<count($groupes);$i++){
		$grocod=$groupes[$i]["code"];
		for($j=0;$j<count($groupes[$i]["divisions"]);$j++){
			$chaine=$grocod.";".$groupes[$i]["divisions"][$j]["code"];
			if($fich){
				fwrite($fich,html_entity_decode_all_version($chaine)."\n");
			}
			affiche_debug($chaine."<br />\n");
		}
	}
	fclose($fich);

	echo "<p>Deux fichiers CSV temporaires ont été générés.<br />Le traitement va pouvoir commencer.</p>\n";
	echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step1=y&amp;suite=y'>Procéder au traitement</a></p>\n";
	echo "<p><br /></p>\n";

}
else {

	//$dbf_file = isset($_FILES["dbf_file"]) ? $_FILES["dbf_file"] : NULL;
	//$dbf_file2 = isset($_FILES["dbf_file2"]) ? $_FILES["dbf_file2"] : NULL;

	//if ((strtoupper($dbf_file['name']) == "F_MEN.DBF") or (strtoupper($dbf_file2['name']) == "F_GPD.DBF")) {

	//if ((strtoupper($dbf_file['name']) == "F_MEN.CSV") or (strtoupper($dbf_file2['name']) == "F_GPD.CSV")) {

		//$fp = @dbase_open($dbf_file['tmp_name'], 0);
		//$fp2 = @dbase_open($dbf_file2['tmp_name'], 0);
		//$fp = fopen($dbf_file['tmp_name'],"r");
		//$fp2 = fopen($dbf_file2['tmp_name'],"r");

		$fp = fopen("../temp/$tempdir/f_men.csv","r");
		$fp2 = fopen("../temp/$tempdir/f_gpd.csv","r");

		if (!$fp) {
			//echo "<p>Impossible d'ouvrir le fichier F_MEN.DBF !</p>";
			//@dbase_close($fp2);
			echo "<p>Impossible d'ouvrir le fichier F_MEN.CSV !</p>";
			fclose($fp2);
			echo "<a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>";
		} else if (!$fp2) {
			//echo "<p>Impossible d'ouvrir le fichier F_GPD.DBF !</p>";
			//@dbase_close($fp);
			echo "<p>Impossible d'ouvrir le fichier F_GPD.CSV !</p>";
			fclose($fp);
			echo "<a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>";
		} else {
			// on constitue le tableau des champs à extraire dans $fp2
			$tabchamps2 = array("GROCOD","DIVCOD");
			//$nblignes2 = dbase_numrecords($fp2); //number of rows

			unset($en_tete);
			$nblignes2=0;
			while (!feof($fp2)) {
				$ligne = fgets($fp2, 4096);
				if($nblignes2==0){
					// Quand on enregistre en CSV des fichiers DBF de GEP avec OpenOffice, les champs sont renommés avec l'ajout de ',...' en fin de nom de champ.
					// On ne retient pas ces ajouts pour $en_tete
					$temp=explode(";",$ligne);
					for($i=0;$i<sizeof($temp);$i++){
						$temp2=explode(",",$temp[$i]);
						$en_tete[$i]=$temp2[0];
					}

					//$en_tete=explode(";",$ligne);
					$nbchamps=sizeof($en_tete);
				}
				$nblignes2++;
			}
			fclose($fp2);
			/*
			if (@dbase_get_record_with_names($fp2,1)) {
				$temp = @dbase_get_record_with_names($fp2,1);
			} else {
				echo "<p>Le fichier F_GPD.DBF sélectionné n'est pas valide !<br />";
				echo "<a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>";
				die();
			}
			$nb = 0;
			foreach($temp as $key => $val){
				$en_tete[$nb] = "$key";
				affiche_debug("\$en_tete[$nb]=$en_tete[$nb]<br />\n");
				$nb++;
			}
			affiche_debug("==========================<br />\n");
			*/
			// On range dans tabindice les indices des champs retenus
			// On repère l'indice des colonnes GROCOD et DIVCOD
			$cpt_tmp=0;
			for ($k = 0; $k < count($tabchamps2); $k++) {
				for ($i = 0; $i < count($en_tete); $i++) {
					//if ($en_tete[$i] == $tabchamps2[$k]) {
					if (trim($en_tete[$i]) == $tabchamps2[$k]) {
						//$tabindice2[] = $i;
						//affiche_debug("\$tabindice2[]=$i<br />\n");
						$tabindice2[$cpt_tmp] = $i;
						affiche_debug("\$tabindice2[$cpt_tmp]=$i<br />\n");
						$cpt_tmp++;
					}
				}
			}
			affiche_debug("==========================<br />\n");
			//=========================
			//$fp2=fopen($dbf_file2['tmp_name'],"r");
			$fp2 = fopen("../temp/$tempdir/f_gpd.csv","r");
			// On lit une ligne pour passer la ligne d'entête:
			$ligne = fgets($fp2, 4096);
			//=========================
			for($k = 1; ($k < $nblignes2+1); $k++){
				// Pour chaque ligne du fichier F_GPD, on récupère dans $affiche[0] le GROCOD et dans $affiche[1] le DIVCOD
				//$ligne = dbase_get_record($fp2,$k);
				if(!feof($fp2)){
					$ligne = fgets($fp2, 4096);
					if(trim($ligne)!=""){
						$tabligne=explode(";",$ligne);
						for($i = 0; $i < count($tabchamps2); $i++) {
							//$affiche[$i] = dbase_filter(trim($ligne[$tabindice2[$i]]));
							$affiche[$i] = dbase_filter(trim($tabligne[$tabindice2[$i]]));
							affiche_debug("\$affiche[$i]=$affiche[$i]<br />\n");
						}
						$tab_groupe[$affiche[0]] = $affiche[1];
						affiche_debug("\$tab_groupe[\$affiche[0]]=\$tab_groupe[$affiche[0]]=".$tab_groupe[$affiche[0]]."<br />\n");
						//=======================================================
						// AJOUT: boireaus
						$tab_groupe2[$affiche[0]][] = $affiche[1];
						affiche_debug("\$tab_groupe2[\$affiche[0]][]=\$tab_groupe2[$affiche[0]][]=".$affiche[1]."<br />\n");
						//=======================================================
					}
				}
			}
			//dbase_close($fp2);
			fclose($fp2);
			// Jusque là, on s'est arrangé pour renseigner un tableau du type:
			// $tab_groupe[GROCOD] = DIVCOD;
			// Du coup, on ne récupère qu'une seule des classes... la dernière de la liste des classes/membres du groupe.
			// Corrigé avec le tab_groupe2
			affiche_debug("=======================================================<br />\n");
			affiche_debug("On a fini l'épluchage du fichier F_GPD<br />\n");
			affiche_debug("=======================================================<br />\n");
			unset($en_tete2);

			// on range les classes existantes dans un tableau:
			$req = mysql_query("select id, classe from classes");
			$nb_classes = mysql_num_rows($req);
			$n = 0;

			// on constitue le tableau des champs à extraire
			$tabchamps = array("MATIMN","NUMIND","ELSTCO");
			//$nblignes = dbase_numrecords($fp); //number of rows
			$nblignes=0;
			while (!feof($fp)) {
				$ligne = fgets($fp, 4096);
				if($nblignes==0){
					// Quand on enregistre en CSV des fichiers DBF de GEP avec OpenOffice, les champs sont renommés avec l'ajout de ',...' en fin de nom de champ.
					// On ne retient pas ces ajouts pour $en_tete
					$temp=explode(";",$ligne);
					//echo "\$ligne=".$ligne."<br />\n";
					//echo "sizeof(\$temp)=".sizeof($temp)."<br />\n";
					for($i=0;$i<sizeof($temp);$i++){
						$temp2=explode(",",$temp[$i]);
						//$en_tete[$i]=$temp2[0];
						//affiche_debug("\$en_tete[$i]=".$en_tete[$i]."<br />\n");
						$en_tete2[$i]=$temp2[0];
						affiche_debug("\$en_tete2[$i]=".$en_tete2[$i]."<br />\n");
					}
					$nbchamps=sizeof($en_tete2);
					affiche_debug("\$nbchamps=".$nbchamps."<br />\n");
					for($i=0;$i<sizeof($en_tete2);$i++){
						affiche_debug("\$en_tete2[$i]=".$en_tete2[$i]."<br />\n");
					}
				}
				$nblignes++;
			}
			fclose ($fp);

			/*
			if (@dbase_get_record_with_names($fp,1)) {
				$temp = @dbase_get_record_with_names($fp,1);
			} else {
				echo "<p>Le fichier sélectionné n'est pas valide !<br />";
				echo "<a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>";
				die();
			}

			$nb = 0;
			foreach($temp as $key => $val){
				$en_tete[$nb] = "$key";
				affiche_debug("\$en_tete[$nb]=$en_tete[$nb]<br />\n");
				$nb++;
			}
			affiche_debug("==========================<br />\n");
			*/
			// On range dans tabindice les indices des champs retenus
			affiche_debug("count(\$tabchamps)=".count($tabchamps)."<br />\n");
			//affiche_debug("count(\$en_tete)=".count($en_tete)."<br />\n");
			affiche_debug("count(\$en_tete2)=".count($en_tete2)."<br />\n");
			/*
			for ($k = 0; $k < count($tabchamps); $k++) {
				//for ($i = 0; $i < count($en_tete); $i++) {
				for ($i = 0; $i < count($en_tete2); $i++) {
					//echo "\$en_tete2[$i]=".$en_tete2[$i]." et \$tabchamps[$k]=".$tabchamps[$k]."<br />\n";
					//if ($en_tete2[$i] == $tabchamps[$k]) {
					if (trim($en_tete2[$i]) == $tabchamps[$k]) {
						$tabindice[] = $i;
						affiche_debug("\$tabindice[]=$i<br />\n");
					}
				}
			}
			*/
			$cpt_tmp=0;
			for ($k = 0; $k < count($tabchamps); $k++) {
				for ($i = 0; $i < count($en_tete2); $i++) {
					if (trim($en_tete2[$i]) == $tabchamps[$k]) {
						$tabindice[$cpt_tmp]=$i;
						affiche_debug("\$tabindice[$cpt_tmp]=$i<br />\n");
						$cpt_tmp++;
					}
				}
			}
			affiche_debug("==========================<br />\n");
			affiche_debug("==========================<br />\n");

			//=========================
			//$fp=fopen($dbf_file['tmp_name'],"r");
			$fp = fopen("../temp/$tempdir/f_men.csv","r");
			// On lit une ligne pour passer la ligne d'entête:
			$ligne = fgets($fp, 4096);
			//=========================
			$nb_reg_no = 0;
			for($k = 1; ($k < $nblignes+1); $k++){
				//$ligne = dbase_get_record($fp,$k);
				if(!feof($fp)){
					$ligne = fgets($fp, 4096);
					if(trim($ligne)!=""){
						$tabligne=explode(";",$ligne);
						for($i = 0; $i < count($tabchamps); $i++) {
							//$affiche[$i] = dbase_filter(trim($ligne[$tabindice[$i]]));
							//affiche_debug("\$affiche[$i]=dbase_filter(trim(\$ligne[$tabindice[$i]]))=$affiche[$i]<br />\n");
							$affiche[$i] = dbase_filter(trim($tabligne[$tabindice[$i]]));
							affiche_debug("\$affiche[$i]=dbase_filter(trim(\$tabligne[".$tabindice[$i]."]))=".$affiche[$i]."<br />\n");
						}
						affiche_debug("==========================<br />\n");
						$req = mysql_query("select col1 from tempo2 where col2 = '$affiche[1]'");
						affiche_debug("On recherche si un prof assure le cours correspondant au groupe: select col1 from tempo2 where col2 = '$affiche[1]'<br />\n");
						$login_prof = @mysql_result($req, 0, 'col1');

						// A REVOIR... IL FAUDRAIT PEUT-ETRE CREER QUAND MEME LE GROUPE POUR L'ASSOCIATION groupe/matiere/classe même si il n'y a pas encore de prof (dans le F_MEN)
						if ($login_prof != '') {
							// On relie les profs aux matières
							affiche_debug("Un (au moins) prof trouvé: $login_prof<br />\n");
							$verif = mysql_query("select id_professeur from j_professeurs_matieres where (id_matiere='$affiche[0]' and id_professeur='$login_prof')");
							affiche_debug("select id_professeur from j_professeurs_matieres where (id_matiere='$affiche[0]' and id_professeur='$login_prof')<br />\n");
							$resverif = mysql_num_rows($verif);
							if($resverif == 0) {
								// On arrive jusque là.
								$req = mysql_query("insert into j_professeurs_matieres set id_matiere='$affiche[0]', id_professeur='$login_prof', ordre_matieres=''");
								affiche_debug("insert into j_professeurs_matieres set id_matiere='$affiche[0]', id_professeur='$login_prof', ordre_matieres=''<br />\n");
								//echo "Ajout de la correspondance prof/matière suivante: $login_prof/$affiche[0]<br />\n";
								echo "<p>Ajout de la correspondance prof/matière suivante: $login_prof/$affiche[0]<br />\n";
								if(!$req) $nb_reg_no++;
							}

							// On relie prof, matières et classes dans un nouveau groupe de Gepi

							// On vide le tableau de la liste des classes associées au groupe:
							unset($tabtmp);

							$test = mysql_query("select id from classes where classe='$affiche[2]'");
							// On initialise le tableau pour que par défaut il contienne $affiche[2] au cas où ce serait une classe...
							$tabtmp[0]=$affiche[2];
							affiche_debug("select id from classes where classe='$affiche[2]'<br />\n");
							$nb_test = mysql_num_rows($test) ;
							if ($nb_test == 0) {
								// dans ce cas, $affiche[2] désigne un groupe
								// on convertit le groupe en classe
					/*
								$affiche[2] = $tab_groupe[$affiche[2]];
								echo "\$affiche[2] = \$tab_groupe[\$affiche[2]] = \$tab_groupe[$affiche[2]] = $affiche[2];<br />\n";
								$test = mysql_query("select id from classes where classe='$affiche[2]'");
								echo "select id from classes where classe='$affiche[2]'<br />\n";
					*/
								// MODIF: boireaus
								// On modifie/remplit le tableau $tabtmp avec la liste des classes associées au groupe.
								for($i=0;$i<count($tab_groupe2[$affiche[2]]);$i++){
									$tabtmp[$i]=$tab_groupe2[$affiche[2]][$i];
									affiche_debug("\$tabtmp[$i]=$tabtmp[$i]<br />\n");
								}
							}
							// On boucle sur la liste des classes:
							// On initialise un témoin pour ne pas recréer le groupe pour la deuxième, troisième,... classe:
							$temoin_groupe_deja_cree="non";
							for($i=0;$i<count($tabtmp);$i++){
								$test = mysql_query("select id from classes where classe='$tabtmp[$i]'");

								$id_classe = @mysql_result($test,0,'id');
								affiche_debug("select id from classes where classe='$tabtmp[$i]' donne \$id_classe=$id_classe<br />\n");

								if ($id_classe != '') {
									$sql="SELECT classe FROM classes WHERE id='$id_classe'";
									$res_classe_tmp=mysql_query($sql);
									$lig_classe_tmp=mysql_fetch_object($res_classe_tmp);
									$classe=$lig_classe_tmp->classe;

									//echo "<p>\n";

									$verif = mysql_query("select g.id from " .
											"groupes g, j_groupes_matieres jgm, j_groupes_professeurs jgp, j_groupes_classes jgc " .
											"where (" .
											"g.id = jgm.id_groupe and " .
											"jgm.id_matiere='$affiche[0]' and " .
											"jgm.id_groupe = jgp.id_groupe and " .
											"jgp.login = '$login_prof' and " .
											"jgp.id_groupe = jgc.id_groupe and " .
											"jgc.id_classe='$id_classe')");
									affiche_debug("select g.id from " .
											"groupes g, j_groupes_matieres jgm, j_groupes_professeurs jgp, j_groupes_classes jgc " .
											"where (" .
											"g.id = jgm.id_groupe and " .
											"jgm.id_matiere='$affiche[0]' and " .
											"jgm.id_groupe = jgp.id_groupe and " .
											"jgp.login = '$login_prof' and " .
											"jgp.id_groupe = jgc.id_groupe and " .
											"jgc.id_classe='$id_classe')<br />\n");
									$resverif = mysql_num_rows($verif);
									if($resverif == 0) {

										// Avant d'enregistrer, il faut quand même vérifier si le groupe existe déjà ou pas
										// ... pour cette classe...
										$verif2 = mysql_query("select g.id from " .
											"groupes g, j_groupes_matieres jgm, j_groupes_classes jgc " .
											"where (" .
											"g.id = jgm.id_groupe and " .
											"jgm.id_matiere='$affiche[0]' and " .
											"jgm.id_groupe = jgc.id_groupe and " .
											"jgc.id_classe='$id_classe')");
										affiche_debug("select g.id from " .
											"groupes g, j_groupes_matieres jgm, j_groupes_classes jgc " .
											"where (" .
											"g.id = jgm.id_groupe and " .
											"jgm.id_matiere='$affiche[0]' and " .
											"jgm.id_groupe = jgc.id_groupe and " .
											"jgc.id_classe='$id_classe')<br />\n");
										$resverif2 = mysql_num_rows($verif2);

										if ($resverif2 == 0) {
											affiche_debug("Le groupe n'existe pas encore pour la classe \$id_classe=$id_classe<br />\n");

											// ordre d'affichage par défaut :
											$priority = sql_query("select priority from matieres where matiere='".$affiche[0]."'");
											if ($priority == "-1") $priority = "0";

											$matiere_nom = mysql_result(mysql_query("SELECT nom_complet FROM matieres WHERE matiere = '" . $affiche[0] . "'"), 0);
											if($temoin_groupe_deja_cree=="non"){
												$res = mysql_query("insert into groupes set name = '" . $affiche[0] . "', description = '" . $matiere_nom . "', recalcul_rang = 'y'");
												affiche_debug("insert into groupes set name = '" . $affiche[0] . "', description = '" . $matiere_nom . "', recalcul_rang = 'y'<br />\n");
												$group_id = mysql_insert_id();
												$temoin_groupe_deja_cree=$group_id;

												echo "<p>\n";
												//echo "Création d'un groupe pour la matière $affiche[0], \n";
												echo "Création d'un groupe (n°$group_id) pour la matière $affiche[0], \n";


												$res2 = mysql_query("insert into j_groupes_matieres set id_groupe = '" . $group_id . "', id_matiere = '" . $affiche[0] . "'");
												affiche_debug("insert into j_groupes_matieres set id_groupe = '" . $group_id . "', id_matiere = '" . $affiche[0] . "'<br />\n");

												$res4 = mysql_query("insert into j_groupes_professeurs set id_groupe = '" . $group_id . "', login ='" . $login_prof . "'");
												affiche_debug("insert into j_groupes_professeurs set id_groupe = '" . $group_id . "', login ='" . $login_prof . "'<br />\n");
												echo "le professeur $login_prof\n";
											}
											else{
												$group_id=$temoin_groupe_deja_cree;
												affiche_debug("Groupe déjà créé avec \$group_id=$group_id<br />");
											}


											$res3 = mysql_query("insert into j_groupes_classes set id_groupe = '" . $group_id . "', id_classe = '" . $id_classe . "', priorite = '" . $priority . "', coef = '0'");
											affiche_debug("insert into j_groupes_classes set id_groupe = '" . $group_id . "', id_classe = '" . $id_classe . "', priorite = '" . $priority . "', coef = '0'<br />\n");

/*
											$sql="SELECT classe FROM classes WHERE id='$id_classe'";
											$res_classe_tmp=mysql_query($sql);
											$lig_classe_tmp=mysql_fetch_object($res_classe_tmp);
											echo " et la classe $lig_classe_tmp->classe.<br />\n";
*/
											echo " et la classe $classe.<br />\n";

											//$res4 = mysql_query("insert into j_groupes_professeurs set id_groupe = '" . $group_id . "', login ='" . $login_prof . "'");
											//echo "insert into j_groupes_professeurs set id_groupe = '" . $group_id . "', login ='" . $login_prof . "'<br />\n";

											// On ajoute tous les élèves de la classe considérée aux groupes. On enlèvera ceux qui ne suivent pas les enseignements
											// à la prochaine étape

											$get_eleves = mysql_query("SELECT distinct(login) FROM j_eleves_classes WHERE id_classe = '" . $id_classe . "'");
											$nb_eleves = mysql_num_rows($get_eleves);
											affiche_debug("\$nb_eleves=$nb_eleves<br />\n");
											$nb_per = mysql_result(mysql_query("SELECT count(*) FROM periodes WHERE id_classe = '" . $id_classe . "'"), 0);
											affiche_debug("\$nb_per=$nb_per<br />\n");

											// DEBUG :: echo "<br/>Classe : " . $id_classe . "<br/>Nb el. : " . $nb_eleves . "<br/>Nb per.: " . $nb_per . "<br/><br/>";
											if($nb_eleves>0){
												echo "Ajout à ce groupe des élèves suivants: ";
												for ($m=0;$m<$nb_eleves;$m++) {
													$e_login = mysql_result($get_eleves, $m, "login");
													for ($n=1;$n<=$nb_per;$n++) {
														$insert_e = mysql_query("INSERT into j_eleves_groupes SET id_groupe = '" . $group_id . "', login = '" . $e_login . "', periode = '" . $n . "'");
														//affiche_debug("INSERT into j_eleves_groupes SET id_groupe = '" . $group_id . "', login = '" . $e_login . "', periode = '" . $n . "'<br />\n");
														affiche_debug("<br />\nINSERT into j_eleves_groupes SET id_groupe = '" . $group_id . "', login = '" . $e_login . "', periode = '" . $n . "'\n");
													}
													if($m==0){
														echo "$e_login";
													}
													else{
														echo ", $e_login";
													}
												}
												echo "<br />\n";
											}
											else{
												echo "Aucun élève dans ce groupe???<br />\n";
											}

										} else {
											// Si on est là, c'est que le groupe existe déjà, mais que le professeur que l'on
											// est en train de traiter n'est pas encore associé au groupe
											// C'est le cas de deux professeurs pour un même groupe/classe dans une matière.
											affiche_debug("Le groupe existe déjà pour la classe \$id_classe=$id_classe, on ajoute le professeur $login_prof au groupe:<br />\n");
											$group_id = mysql_result($verif2, 0);
											$res = mysql_query("insert into j_groupes_professeurs set id_groupe = '" . $group_id . "', login ='" . $login_prof . "'");
											affiche_debug("insert into j_groupes_professeurs set id_groupe = '" . $group_id . "', login ='" . $login_prof . "'<br />\n");
											echo "Ajout de $login_prof à un groupe existant (<i>plus d'un professeur pour ce groupe</i>).<br />\n";
											//echo "Ajout de $login_prof à un groupe existant.<br />\n";
										}
									}
									//echo "</p>\n";
								}
							}
						}
					}
					affiche_debug("===================================================<br />\n");
				}
			}
			//dbase_close($fp);
			fclose($fp);


            /*
			if ($nb_reg_no != 0) {
				echo "<p>Lors de l'enregistrement des données il n'y a eu $nb_reg_no erreurs. Essayez de trouvez la cause de l'erreur et recommencez la procédure avant de passer à l'étape suivante.";
			} else {
				echo "<p>L'importation des relations professeurs/matières et professeurs/classes dans la base GEPI a été effectuée avec succès !<br />Vous pouvez procéder à l'étape suivante d'importation des options suivies par les élèves.</p>";

			}
            */

            echo "<p>Contrôlez dans la page si vous n'avez pas d'erreur (<i>signalée en rouge le cas échéant</i>), puis vous pouvez procéder à l'étape suivante d'importation des options suivies par les élèves.</p>";

			echo "<center><p><a href='init_options.php'>Importer les options suivies par les élèves</a></p></center>";
			echo "<p><br /></p>\n";
		}
	/*
	} else if ((trim($dbf_file['name'])=='') or (trim($dbf_file2['name'])=='')) {
		echo "<p>Veuillez préciser les fichiers !<br />";
		echo "<a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>";

	} else {
		echo "<p>Fichier(s) sélectionné(s) non valide(s) !<br />";
		echo "<a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>";
	}
	*/
}
require("../lib/footer.inc.php");
?>