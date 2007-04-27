<?php
/*
 * Last modification  : 04/10/2006
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

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);

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

// A FAIRE:
// ALTER TABLE `eleves` ADD `ele_id` VARCHAR( 10 ) NOT NULL ;


//**************** EN-TETE *****************
$titre_page = "Mise à jour eleves/responsables";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
echo "<p class=bold>";
echo "<a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
echo "</p>\n";

// Solution de conversion d'une part...
// ... et proposer d'autre part une mise à jour par import Sconet

$sql="SELECT value FROM setting WHERE name='conv_new_resp_table'";
$test=mysql_query($sql);
if($test){
	$ligtmp=mysql_fetch_object($test);
	if($ligtmp->value>0){
		echo "<p>La mise à jour a déjà été effectuée.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	else{
		$temoin=1;
	}
}
else{
	$temoin=1;
}

if($temoin==1){
	$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

	// Ajout, si nécessaire, du champ 'ele_id' à la table 'eleves':
	$sql="SHOW FIELDS FROM eleves";
	$test=mysql_query($sql);
	$temoin_ele_id="";
	while($tabtmp=mysql_fetch_array($test)){
		if(strtolower($tabtmp[0])=="ele_id"){
			$temoin_ele_id="oui";
		}
	}
	if($temoin_ele_id==""){
		$sql="ALTER TABLE `eleves` ADD `ele_id` VARCHAR( 10 ) NOT NULL ;";
		$res_ele_id=mysql_query($sql);
	}


	$sql="CREATE TABLE IF NOT EXISTS `responsables2` (
	`ele_id` varchar(10) NOT NULL,
	`pers_id` varchar(10) NOT NULL,
	`resp_legal` varchar(1) NOT NULL,
	`pers_contact` varchar(1) NOT NULL
	);";
	$res_create=mysql_query($sql);

	$sql="CREATE TABLE IF NOT EXISTS `resp_adr` (
	`adr_id` varchar(10) NOT NULL,
	`adr1` varchar(100) NOT NULL,
	`adr2` varchar(100) NOT NULL,
	`adr3` varchar(100) NOT NULL,
	`adr4` varchar(100) NOT NULL,
	`cp` varchar(6) NOT NULL,
	`pays` varchar(50) NOT NULL,
	`commune` varchar(50) NOT NULL,
	PRIMARY KEY  (`adr_id`)
	);";
	$res_create=mysql_query($sql);

	$sql="CREATE TABLE IF NOT EXISTS `resp_pers` (
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
	);";
	$res_create=mysql_query($sql);


	if(!isset($mode)){
		echo "<p>Vous pouvez effectuer la mise à jour des tables:</p>\n";
		echo "<ul>";
		echo "<li><a href='".$_SERVER['PHP_SELF']."?mode=1'>avec SCONET</a>: Dans ce cas, il faut fournir des fichiers CSV générés <a href='../init_xml/lecture_xml_sconet.php?ad_retour=".$_SERVER['PHP_SELF']."'>ici</a> depuis des fichiers XML extraits de SCONET.<br />\nSi vous effectuez ce choix, vous pourrez par la suite effectuer de nouveaux imports pour insérer les élèves/responsables arrivés en cours d'année.<br />\nCe choix implique que le champ ELENOET de la table 'eleves' soit correctement rempli avec des valeurs correspondant à celles de l'ancien F_ELE.DBF<br />\nLe contenu de la table 'responsables' est ignoré et les nouvelles tables responsables sont remplies d'après les CSV fournis.</li>\n";
		echo "<li><a href='".$_SERVER['PHP_SELF']."?mode=2'>sans SCONET</a>: Dans ce cas, on ne fait que la conversion des tables.<br />\nVous devrez dans ce cas gérer les futures nouvelles inscriptions à la main (<i>ou par des imports CSV</i>).<br />\nIci, c'est la liaison ERENO de vos tables 'eleves' et 'responsables' qui est utilisée pour assurer la migration vers les nouvelles tables.<br />\nCe mode ne permet pas de mises à jour en cours d'année.</li>\n";
		echo "</ul>";

		$sql="SELECT * FROM eleves WHERE elenoet=''";
		$res1=mysql_query($sql);
		if(mysql_num_rows($res1)>0){
			echo "<p>Les élèves suivants n'ont pas leur ELENOET renseigné.<br />Ils ne seront donc pas identifiés/associés par la suite avec les élèves inscrits dans Sconet.<br />Si vous envisagez le mode avec Sconet (<i>recommandé quand c'est possible</i>), vous devriez commencer par rechercher les ELENOET manquants et les renseigner dans la Gestion des élèves.</p>\n";
			echo "<table border='1'>\n";
			echo "<tr>\n";
			echo "<td style='font-weight:bold; text-align:center;'>Login</td>\n";
			echo "<td style='font-weight:bold; text-align:center;'>Nom</td>\n";
			echo "<td style='font-weight:bold; text-align:center;'>Prenom</td>\n";
			echo "<td style='font-weight:bold; text-align:center;'>Naissance</td>\n";
			echo "</tr>\n";
			while($lig1=mysql_fetch_object($res1)){
				echo "<tr>\n";
				echo "<td>$lig1->login</td>\n";
				echo "<td>$lig1->nom</td>\n";
				echo "<td>$lig1->prenom</td>\n";
				echo "<td>$lig1->naissance</td>\n";
				echo "</tr>\n";
			}
			echo "</table>\n";
		}

	}
	elseif($mode==2){

		if(!isset($confirmer)){
			echo "<p><b>ATTENTION:</b> Le mode sans SCONET ne permet pas de mises à jour en cours d'année.<br />\nCela signifie que les corrections effectuées sur votre logiciel de gestion des élèves et responsables (<i>changements d'adresses, corrections,...</i>) ne pourront pas être automatiquement importées dans GEPI.<br />Vous aurez donc une double-saisie à effectuer pour gérer ces mises à jour.<br />\nLe mode avec SCONET, lui, permettrait d'importer les corrections en cours d'année.</p>\n";
			echo "<p>Ce choix est irréversible.<br />\nEtes-vous sûr que vous ne souhaitez pas utiliser l'import avec SCONET?</p>\n";
			echo "<p><a href='".$_SERVER['PHP_SELF']."?mode=2&amp;confirmer=oui'>OUI</a> ou <a href='".$_SERVER['PHP_SELF']."'>NON</a></p>\n";
		}
		else{
			$erreur=0;
			$sql="SELECT * FROM eleves ORDER BY nom,prenom";
			$res1=mysql_query($sql);
			if(mysql_num_rows($res1)>0){
				// On vide les tables avant traitement (au cas où il aurait fallu s'y prendre à deux fois)
				$sql="TRUNCATE TABLE resp_adr";
				$res_truncate=mysql_query($sql);
				$sql="TRUNCATE TABLE resp_pers";
				$res_truncate=mysql_query($sql);
				$sql="TRUNCATE TABLE responsables2";
				$res_truncate=mysql_query($sql);

				while($lig1=mysql_fetch_object($res1)){
					//if($lig1->ele_id==''){
					unset($ele_id);
					if(!isset($lig1->ele_id)){
						$ele_id="";
					}
					else{
						$ele_id=$lig1->ele_id;
					}

					if($ele_id==''){
						//echo "<p>On va générer un ele_id pour $lig1->nom $lig1->prenom ($lig1->elenoet)<br />\n";
						// Recherche du plus grand ele_id:
						$sql="SELECT ele_id FROM eleves WHERE ele_id LIKE 'e%' ORDER BY ele_id DESC";
						//echo "$sql<br />\n";
						$res_ele_id_eleve=mysql_query($sql);
						if(mysql_num_rows($res_ele_id_eleve)>0){
							$tmp=0;
							$lig_ele_id_eleve=mysql_fetch_object($res_ele_id_eleve);
							$tmp=substr($lig_ele_id_eleve->ele_id,1);
							$tmp++;
							$max_ele_id=$tmp;
						}
						else{
							$max_ele_id=1;
						}

						$sql="SELECT ele_id FROM responsables2 WHERE ele_id LIKE 'e%' ORDER BY ele_id DESC";
						//echo "$sql<br />\n";
						$res_ele_id_responsables2=mysql_query($sql);
						if(mysql_num_rows($res_ele_id_responsables2)>0){
							$tmp=0;
							$lig_ele_id_responsables2=mysql_fetch_object($res_ele_id_responsables2);
							$tmp=substr($lig_ele_id_responsables2->ele_id,1);
							$tmp++;
							$max_ele_id2=$tmp;
						}
						else{
							$max_ele_id2=1;
						}

						$tmp=max($max_ele_id,$max_ele_id2);
						$ele_id="e".sprintf("%09d",max($max_ele_id,$max_ele_id2));

						//$sql="UPDATE eleves SET ele_id='$ele_id' WHERE elenoet='$lig1->elenoet'";
						$sql="UPDATE eleves SET ele_id='$ele_id' WHERE login='$lig1->login'";
						//echo "$sql<br />\n";
						$res_update=mysql_query($sql);
						if(!$res_update){
							//echo "<font color='red'>Erreur</font> lors de la définition de l'ele_id $ele_id pour $lig1->nom $lig1->prenom ($lig1->elenoet).<br />\n";
							echo "<font color='red'>Erreur</font> lors de la définition de l'ele_id $ele_id pour $lig1->nom $lig1->prenom ($lig1->login).<br />\n";
							$erreur++;
						}
						else{
							//echo "<p>Définition de l'ele_id $ele_id pour $lig1->nom $lig1->prenom ($lig1->elenoet).<br />\n";
							echo "<p>Définition de l'ele_id $ele_id pour $lig1->nom $lig1->prenom ($lig1->login).<br />\n";
						}
					}
					else{
						//echo "<p>$lig1->nom $lig1->prenom ($lig1->elenoet) dispose déjà d'un ele_id<br />\n";
						$ele_id=$lig1->ele_id;
					}
					/*
					echo "<p>nom=$lig1->nom<br />\n";
					echo "prenom=$lig1->prenom<br />\n";
					echo "elenoet=$lig1->elenoet<br />\n";
					echo "ereno=$lig1->ereno<br />\n";
					echo "ele_id=$ele_id</p>\n";
					*/

					if($lig1->ereno!=''){
						$sql="SELECT * FROM responsables WHERE ereno='$lig1->ereno'";
						//echo "$sql<br />\n";
						$res2=mysql_query($sql);
						if(mysql_num_rows($res2)>0){
							while($lig2=mysql_fetch_object($res2)){
								// Est-ce que cet ereno a déjà fait l'objet d'une insertion dans les nouvelles tables?
								// Recherche des pers_id ou recherche du plus grand pers_id affecté.

								$sql="SELECT r2.* FROM responsables2 r2, responsables r, eleves e WHERE r2.ele_id=e.ele_id AND r.ereno=e.ereno AND e.ereno='$lig1->ereno'";
								//echo "$sql<br />\n";
								$test=mysql_query($sql);
								if(mysql_num_rows($test)>0){
									// Le couple de responsables correspondant à $lig1->ereno est déjà dans les nouvelles tables.
									while($ligtmp=mysql_fetch_object($test)){
										//$sql="SELECT 1=1 FROM responsables2 WHERE pers_id='$ligtmp->pers_id' AND ele_id='$lig1->ele_id'";
										$sql="SELECT 1=1 FROM responsables2 WHERE pers_id='$ligtmp->pers_id' AND ele_id='$ele_id'";
										//echo "$sql<br />\n";
										$test2=mysql_query($sql);
										if(mysql_num_rows($test2)==0){
											// L'élève courant n'est pas encore inscrit...
											//$sql="INSERT INTO responsables2 SET ele_id='$lig1->ele_id', pers_id='$ligtmp->pers_id', resp_legal='$ligtmp->resp_legal', pers_contact='$ligtmp->pers_contact'";
											$sql="INSERT INTO responsables2 SET ele_id='$ele_id', pers_id='$ligtmp->pers_id', resp_legal='$ligtmp->resp_legal', pers_contact='$ligtmp->pers_contact'";
											//echo "$sql<br />\n";
											$res_insert=mysql_query($sql);
											if(!$res_insert){
												echo "<font color='red'>Erreur</font> lors de l'insertion de l'association avec le responsable ($ligtmp->resp_legal) $ligtmp->pers_id<br />\n";
												$erreur++;
											}
											else{
												echo "Insertion de l'association avec le responsable ($ligtmp->resp_legal) $ligtmp->pers_id <br />\n";
											}
										}
									}
								}
								else{
									// Le couple n'a pas encore été inscrit dans les nouvelles tables.

									// Recherche du plus grand pers_id:
									$sql="SELECT pers_id FROM resp_pers WHERE pers_id LIKE 'p%' ORDER BY pers_id DESC";
									//echo "$sql<br />\n";
									$restmp=mysql_query($sql);
									if(mysql_num_rows($restmp)==0){
										$nb1=1;
									}
									else{
										$ligtmp=mysql_fetch_object($restmp);
										$nb1=substr($ligtmp->pers_id,1);
										$nb1++;
									}
									$pers_id="p".sprintf("%09d",$nb1);

									// Recherche du plus grand adr_id:
									$sql="SELECT adr_id FROM resp_adr WHERE adr_id LIKE 'a%' ORDER BY adr_id DESC";
									//echo "$sql<br />\n";
									$restmp=mysql_query($sql);
									if(mysql_num_rows($restmp)==0){
										$nb2=1;
									}
									else{
										$ligtmp=mysql_fetch_object($restmp);
										$nb2=substr($ligtmp->adr_id,1);
										$nb2++;
									}
									$adr_id="a".sprintf("%09d",$nb2);




									if($lig2->nom1!=''){
										$sql="INSERT INTO responsables2 SET pers_id='$pers_id', ele_id='$ele_id', resp_legal='1', pers_contact='1'";
										//echo "$sql<br />\n";
										$res_insert1=mysql_query($sql);
										if(!$res_insert1){
											echo "<font color='red'>Erreur</font> lors de l'insertion de l'association de l'élève $ele_id avec le responsable (1) $pers_id<br />\n";
											$erreur++;
										}
										else{
											echo "Insertion de l'association de l'élève $ele_id avec le responsable (1) $pers_id<br />\n";
										}

										//$sql="INSERT INTO resp_pers SET pers_id='$pers_id', nom='$lig2->nom1',prenom='$lig2->prenom1',adr_id='$adr_id'";
										$sql="INSERT INTO resp_pers SET pers_id='$pers_id', nom='".addslashes($lig2->nom1)."',prenom='".addslashes($lig2->prenom1)."',adr_id='$adr_id'";
										//echo "$sql<br />\n";
										$res_insert2=mysql_query($sql);
										if(!$res_insert2){
											echo "<font color='red'>Erreur</font> lors de l'insertion du responsable ($pers_id): $lig2->nom1 $lig2->prenom1 (avec le n° adresse $adr_id).<br />\n";
											$erreur++;
										}
										else{
											echo "Insertion du responsable ($pers_id): $lig2->nom1 $lig2->prenom1 (avec le n° adresse $adr_id).<br />\n";
										}

										//$sql="INSERT INTO resp_adr SET adr1='$lig2->adr1',adr2='$lig2->adr1_comp',cp='$lig2->cp1',commune='$lig2->commune1',adr_id='$adr_id'";
										$sql="INSERT INTO resp_adr SET adr1='".addslashes($lig2->adr1)."',adr2='".addslashes($lig2->adr1_comp)."',cp='$lig2->cp1',commune='".addslashes($lig2->commune1)."',adr_id='$adr_id'";
										//echo "$sql<br />\n";
										$res_insert3=mysql_query($sql);
										if(!$res_insert3){
											echo "<font color='red'>Erreur</font> lors de l'insertion de l'adresse $lig2->adr1, $lig2->adr1_comp, $lig2->cp1, $lig2->commune1 avec le n° adresse $adr_id.<br />\n";
											$erreur++;
										}
										else{
											echo "Insertion de l'adresse $lig2->adr1, $lig2->adr1_comp, $lig2->cp1, $lig2->commune1 avec le n° adresse $adr_id.<br />\n";
										}
									}


									if($lig2->nom2!=''){
										// Pour le deuxième responsable:
										$nb1++;
										$pers_id="p".sprintf("%09d",$nb1);

										if(($lig2->adr2!=$lig2->adr1)||($lig2->adr2_comp!=$lig2->adr1_comp)||($lig2->cp2!=$lig2->cp1)||($lig2->commune2!=$lig2->commune1)){
											if(($lig2->adr2!='')||($lig2->adr2_comp!='')||($lig2->cp2!='')||($lig2->commune2!='')){
												$nb2++;
												$adr_id="a".sprintf("%09d",$nb2);

												echo "Le deuxième responsable n'a pas la même adresse.<br />\n";

												//$sql="INSERT INTO resp_adr SET adr1='$lig2->adr2',adr2='$lig2->adr2_comp',cp='$lig2->cp2',commune='$lig2->commune2',adr_id='$adr_id'";
												$sql="INSERT INTO resp_adr SET adr1='".addslashes($lig2->adr2)."',adr2='".addslashes($lig2->adr2_comp)."',cp='$lig2->cp2',commune='".addslashes($lig2->commune2)."',adr_id='$adr_id'";
												//echo "$sql<br />\n";
												$res_insert3=mysql_query($sql);
												if(!$res_insert3){
													echo "<font color='red'>Erreur</font> lors de l'insertion de l'adresse $lig2->adr2, $lig2->adr2_comp, $lig2->cp2, $lig2->commune2 avec le n° adresse $adr_id.<br />\n";
													$erreur++;
												}
												else{
													echo "Insertion de l'adresse $lig2->adr2, $lig2->adr2_comp, $lig2->cp2, $lig2->commune2 avec le n° adresse $adr_id.<br />\n";
												}
											}
										}

										$sql="INSERT INTO responsables2 SET pers_id='$pers_id', ele_id='$ele_id', resp_legal='2', pers_contact='1'";
										//echo "$sql<br />\n";
										$res_insert1=mysql_query($sql);
										if(!$res_insert1){
											echo "<font color='red'>Erreur</font> de l'insertion de l'association de l'élève $ele_id avec le responsable (2) $pers_id<br />\n";
											$erreur++;
										}
										else{
											echo "Insertion de l'association de l'élève $ele_id avec le responsable (2) $pers_id<br />\n";
										}

										//$sql="INSERT INTO resp_pers SET pers_id='$pers_id', nom='$lig2->nom2',prenom='$lig2->prenom2',adr_id='$adr_id'";
										$sql="INSERT INTO resp_pers SET pers_id='$pers_id', nom='".addslashes($lig2->nom2)."',prenom='".addslashes($lig2->prenom2)."',adr_id='$adr_id'";
										//echo "$sql<br />\n";
										$res_insert2=mysql_query($sql);
										if(!$res_insert2){
											echo "<font color='red'>Erreur</font> de l'insertion du responsable ($pers_id): $lig2->nom2 $lig2->prenom2 (avec le n° adresse $adr_id).<br />\n";
											$erreur++;
										}
										else{
											echo "Insertion du responsable ($pers_id): $lig2->nom2 $lig2->prenom2 (avec le n° adresse $adr_id).<br />\n";
										}
									}
								}
							}
						}
					}
				}

				if($erreur==0){
					echo "<p>L'opération s'est correctement déroulée.</p>\n";
					echo "<center><p><a href='../accueil.php'>Retourner à l'accueil</a></p></center>\n";

					// On renseigne le témoin de mise à jour effectuée:
					saveSetting("conv_new_resp_table", 1);
					saveSetting("import_maj_xml_sconet", 0);
				}
				else{
					echo "<p>Des erreurs se sont produites.</p>\n";
				}
			}
			else{
				echo "<p>Il semble que la table 'eleves' soit vide.</p>\n";
			}
		}
	}
	elseif($mode==1){
		// On fournit les fichiers CSV générés depuis les XML de SCONET...
		if (!isset($is_posted)) {
			echo "<p>Vous allez importer les fichiers <b>CSV</b> (<i><a href='../init_xml/lecture_xml_sconet.php?ad_retour=".$_SERVER['PHP_SELF']."'>générés</a> à partir des exports XML de Sconet</i>).</p>\n";
			echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method=post>\n";
			echo "<input type=hidden name='is_posted' value='yes' />\n";
			echo "<input type=hidden name='mode' value='1' />\n";
			echo "<p>Sélectionnez le fichier <b>ELEVES.CSV</b>:<br /><input type=\"file\" size=\"80\" name=\"ele_file\" /></p>\n";
			echo "<p>Et les fichiers de responsables:</p>\n";
			echo "<p>Sélectionnez le fichier <b>PERSONNES.CSV</b>:<br /><input type='file' size='80' name='pers_file' />\n";
			echo "<p>Sélectionnez le fichier <b>RESPONSABLES.CSV</b>:<br /><input type='file' size='80' name='resp_file' />\n";
			echo "<p>Sélectionnez le fichier <b>ADRESSES.CSV</b>:<br /><input type='file' size='80' name='adr_file' />\n";
			echo "<p><input type=submit value='Valider' /></p>\n";
			echo "</form>\n";
		}
		else{
			unset($tab_elenoet_non_trouves);
			$tab_elenoet_non_trouves=array();

			$csv_file = isset($_FILES["ele_file"]) ? $_FILES["ele_file"] : NULL;
			if(strtoupper($csv_file['name']) == "ELEVES.CSV"){
				//$fp = dbase_open($csv_file['tmp_name'], 0);
				$fp=fopen($csv_file['tmp_name'],"r");

				if(!$fp){
					echo "<p>Impossible d'ouvrir le fichier ELEVES.CSV !</p>";
					echo "<p><a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>\n";
				}
				else{
					echo "<p>Dans un premier temps, on renseigne le nouveau champ 'ele_id' dans la table 'eleves'.</p>\n";

					$tabchamps = array("ELENOET","ELE_ID");

					$erreur=0;

					$nblignes=0;
					while(!feof($fp)) {
						$ligne=fgets($fp, 4096);
						if($nblignes==0){
							// Quand on enregistre en CSV des fichiers DBF de GEP avec OpenOffice, les champs sont renommés avec l'ajout de ',...' en fin de nom de champ.
							// On ne retient pas ces ajouts pour $en_tete
							$temp=explode(";",$ligne);
							for($i=0;$i<sizeof($temp);$i++){
								$temp2=explode(",",$temp[$i]);
								$en_tete[$i]=$temp2[0];
							}

							$nbchamps=sizeof($en_tete);
						}
						$nblignes++;
					}
					fclose ($fp);

					// On range dans tabindice les indices des champs retenus
					unset($tabindice);
					for ($k = 0; $k < count($tabchamps); $k++) {
						for ($i = 0; $i < count($en_tete); $i++) {
							if (trim($en_tete[$i]) == $tabchamps[$k]) {
								$tabindice[] = $i;
								//echo "Champ $tabchamps[$k] trouvé à l'indice $i<br />";
							}
						}
					}

					//=========================
					$fp=fopen($csv_file['tmp_name'],"r");
					// On lit une ligne pour passer la ligne d'entête:
					$ligne = fgets($fp, 4096);
					for($k = 1; ($k < $nblignes+1); $k++){
						if(!feof($fp)){
							$ligne = fgets($fp, 4096);
							if(trim($ligne)!=""){
								$tabligne=explode(";",$ligne);
								$affiche=array();
								for($i = 0; $i < count($tabchamps); $i++) {
									$affiche[$i] = traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($tabligne[$tabindice[$i]]))));
									//echo "A l'indice $i, on a \$tabligne[\$tabindice[$i]]=\$tabligne[$tabindice[$i]]=".$tabligne[$tabindice[$i]]."<br />";
								}

								//$affiche[0]=sprintf("%05d",$affiche[0]);
								//if(strlen($affiche[0])){
								//}

								//$sql="SELECT * FROM eleves WHERE elenoet='$affiche[0]'";
								$sql="SELECT * FROM eleves WHERE elenoet='$affiche[0]' OR elenoet='".sprintf("%05d",$affiche[0])."'";
								//echo "$sql<br />\n";
								$res1=mysql_query($sql);
								if(mysql_num_rows($res1)>0){
									//$sql="UPDATE eleves SET ele_id='$affiche[1]' WHERE elenoet='$affiche[0]'";
									$sql="UPDATE eleves SET ele_id='$affiche[1]' WHERE elenoet='$affiche[0]' OR elenoet='".sprintf("%05d",$affiche[0])."'";
									//echo "$sql<br />\n";
									$res_update=mysql_query($sql);
									if(!$res_update){
										$erreur++;
										echo "<font color='red'>Erreur</font> lors du renseignement de l'ele_id avec la valeur $affiche[1] pour l'élève d'ELENOET $affiche[0]<br />\n";
									}
									else{
										echo "Renseignement de l'ele_id avec la valeur $affiche[1] pour l'élève d'ELENOET $affiche[0]<br />\n";
									}
								}
								else{
									echo "<font color='red'>Aucun élève avec l'ELENOET $affiche[0] n'a été trouvé dans votre table 'eleves'; il pourra être créé lors d'un import ultérieur.</font>\n";
									$tab_elenoet_non_trouves[]=$affiche[0];
								}
							}
						}
					}
					fclose($fp);
				}
			}

			// Et la partie responsables:
			// C'est la copie de la page /init_xml/responsables.php
			$nb_reg_no1=-1;
			$nb_reg_no2=-1;
			$nb_reg_no3=-1;

			$csv_file = isset($_FILES["pers_file"]) ? $_FILES["pers_file"] : NULL;
			//echo strtoupper($csv_file['name'])."<br />";
			if(strtoupper($csv_file['name']) == "PERSONNES.CSV") {
				$fp=fopen($csv_file['tmp_name'],"r");
				if(!$fp){
					echo "<p>Impossible d'ouvrir le fichier PERSONNES.CSV.</p>\n";
					echo "<p><a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>\n";
				}
				else{
					echo "<p>Lecture du PERSONNES.CSV pour renseigner la nouvelle table 'resp_pers' avec les nom, prénom, téléphone,... des responsables.</p>";

					// On vide la table avant traitement (au cas où il aurait fallu s'y prendre à deux fois)
					$sql="TRUNCATE TABLE resp_pers";
					$res_truncate=mysql_query($sql);

					// on constitue le tableau des champs à extraire
					$tabchamps=array("pers_id","nom","prenom","tel_pers","tel_port","tel_prof","mel","adr_id");

					$nblignes=0;
					while (!feof($fp)) {
						$ligne = fgets($fp, 4096);
						if($nblignes==0){
							// Quand on enregistre en CSV des fichiers DBF de GEP avec OpenOffice, les champs sont renommés avec l'ajout de ',...' en fin de nom de champ.
							// On ne retient pas ces ajouts pour $en_tete
							$temp=explode(";",$ligne);
							for($i=0;$i<sizeof($temp);$i++){
								$temp2=explode(",",$temp[$i]);
								$en_tete[$i]=$temp2[0];
							}

							$nbchamps=sizeof($en_tete);
						}
						$nblignes++;
					}
					fclose ($fp);

					// On range dans tabindice les indices des champs retenus
					unset($tabindice);
					for ($k = 0; $k < count($tabchamps); $k++) {
						for ($i = 0; $i < count($en_tete); $i++) {
							if (trim($en_tete[$i]) == $tabchamps[$k]) {
								$tabindice[] = $i;
								//echo "Champ $tabchamps[$k] trouvé à l'indice $i<br />";
							}
						}
					}

					//=========================
					$fp=fopen($csv_file['tmp_name'],"r");
					// On lit une ligne pour passer la ligne d'entête:
					$ligne = fgets($fp, 4096);
					//=========================
					$nb_reg_no3=0;
					$nb_record3=0;
					for($k = 1; ($k < $nblignes+1); $k++){
						//$ligne = dbase_get_record($fp,$k);
						if(!feof($fp)){
							$ligne = fgets($fp, 4096);
							if(trim($ligne)!=""){
								$tabligne=explode(";",$ligne);
								for($i = 0; $i < count($tabchamps); $i++) {
									//$ind = $tabindice[$i];
									$affiche[$i] = traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($tabligne[$tabindice[$i]]))));
									//echo "A l'indice $i, on a \$tabligne[\$tabindice[$i]]=\$tabligne[$tabindice[$i]]=".$tabligne[$tabindice[$i]]."<br />";
								}
								$sql="insert into resp_pers set
											pers_id = '$affiche[0]',
											nom = '$affiche[1]',
											prenom = '$affiche[2]',
											tel_pers = '$affiche[3]',
											tel_port = '$affiche[4]',
											tel_prof = '$affiche[5]',
											mel = '$affiche[6]',
											adr_id = '$affiche[7]'
											";
								//echo "$sql<br />\n";
								$req = mysql_query($sql);
								if(!$req) {
									$nb_reg_no3++;
									echo mysql_error();
									echo "<font color='red'>Erreur</font> lors de l'insertion du responsable ($affiche[0]) $affiche[1] $affiche[2] avec les numéros de téléphone $affiche[3], $affiche[4], $affiche[5], le mel $affiche[6] et le numéro d'adresse $affiche[7].<br />\n";
								} else {
									$nb_record3++;
									echo "Insertion du responsable ($affiche[0]) $affiche[1] $affiche[2] avec les numéros de téléphone $affiche[3], $affiche[4], $affiche[5], le mel $affiche[6] et le numéro d'adresse $affiche[7].<br />\n";
								}
							}
						}
					}
					//dbase_close($fp);
					fclose($fp);

					if ($nb_reg_no3 != 0) {
						echo "<p>Lors de l'enregistrement des données de PERSONNES.CSV, il y a eu $nb_reg_no3 erreurs. Essayez de trouvez la cause de l'erreur.</p>\n";
					} else {
						echo "<p>L'importation des personnes (responsables) dans la base GEPI a été effectuée avec succès (".$nb_record3." enregistrements au total).</p>\n";
					}

				}
			} else if (trim($csv_file['name'])=='') {
				echo "<p>Aucun fichier PERSONNES.CSV n'a été sélectionné !<br />\n";
				//echo "<a href='disciplines.php'>Cliquer ici </a> pour recommencer !</center></p>";
				echo "<a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>\n";

			} else {
				echo "<p>Le fichier sélectionné n'est pas valide !<br />\n";
				//echo "<a href='disciplines.php'>Cliquer ici </a> pour recommencer !</center></p>";
				echo "<a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>\n";
			}




			$csv_file = isset($_FILES["resp_file"]) ? $_FILES["resp_file"] : NULL;
			//echo strtoupper($csv_file['name'])."<br />";
			if(strtoupper($csv_file['name']) == "RESPONSABLES.CSV") {
				$fp=fopen($csv_file['tmp_name'],"r");
				if(!$fp){
					echo "<p>Impossible d'ouvrir le fichier RESPONSABLES.CSV.</p>";
					echo "<p><a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>";
				}
				else{
					echo "<p>Lecture du fichier RESPONSABLES.CSV pour renseigner les associations eleves/responsables.</p>\n";

					// On vide la table avant traitement (au cas où il aurait fallu s'y prendre à deux fois)
					$sql="TRUNCATE TABLE responsables2";
					$res_truncate=mysql_query($sql);

					// on constitue le tableau des champs à extraire
					$tabchamps=array("ele_id","pers_id","resp_legal","pers_contact");

					$nblignes=0;
					while (!feof($fp)) {
						$ligne = fgets($fp, 4096);
						if($nblignes==0){
							// Quand on enregistre en CSV des fichiers DBF de GEP avec OpenOffice, les champs sont renommés avec l'ajout de ',...' en fin de nom de champ.
							// On ne retient pas ces ajouts pour $en_tete
							$temp=explode(";",$ligne);
							for($i=0;$i<sizeof($temp);$i++){
								$temp2=explode(",",$temp[$i]);
								$en_tete[$i]=$temp2[0];
							}

							$nbchamps=sizeof($en_tete);
						}
						$nblignes++;
					}
					fclose ($fp);

					// On range dans tabindice les indices des champs retenus
					unset($tabindice);
					for ($k = 0; $k < count($tabchamps); $k++) {
						for ($i = 0; $i < count($en_tete); $i++) {
							if (trim($en_tete[$i]) == $tabchamps[$k]) {
								$tabindice[] = $i;
								//echo "Champ $tabchamps[$k] trouvé à l'indice $i<br />";
							}
						}
					}

					//=========================
					$fp=fopen($csv_file['tmp_name'],"r");
					// On lit une ligne pour passer la ligne d'entête:
					$ligne = fgets($fp, 4096);
					//=========================
					$nb_reg_no1=0;
					$nb_record1=0;
					for($k = 1; ($k < $nblignes+1); $k++){
						//$ligne = dbase_get_record($fp,$k);
						if(!feof($fp)){
							$ligne = fgets($fp, 4096);
							if(trim($ligne)!=""){
								$tabligne=explode(";",$ligne);
								for($i = 0; $i < count($tabchamps); $i++) {
									//$ind = $tabindice[$i];
									$affiche[$i] = traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($tabligne[$tabindice[$i]]))));
								}
								$sql="insert into responsables2 set
											ele_id = '$affiche[0]',
											pers_id = '$affiche[1]',
											resp_legal = '$affiche[2]',
											pers_contact = '$affiche[3]'
											";
								//echo "$sql<br />\n";
								$req = mysql_query($sql);
								if(!$req) {
									$nb_reg_no1++;
									echo mysql_error();
									echo "<font color='red'>Erreur</font> lors de l'insertion de l'association élève $affiche[0] et responsable ($affiche[2]) $affiche[1].<br />\n";
								} else {
									$nb_record1++;
									echo "Insertion de l'association élève $affiche[0] et responsable ($affiche[2]) $affiche[1].<br />\n";
								}
							}
						}
					}
					//dbase_close($fp);
					fclose($fp);

					if ($nb_reg_no1 != 0) {
						echo "<p>Lors de l'enregistrement des données de RESPONSABLES.CSV, il y a eu $nb_reg_no1 erreurs. Essayez de trouvez la cause de l'erreur.</p>\n";
					}
					else {
						echo "<p>L'importation des relations eleves/responsables dans la base GEPI a été effectuée avec succès (".$nb_record1." enregistrements au total).</p>\n";
					}

				}
			} else if (trim($csv_file['name'])=='') {
				echo "<p>Aucun fichier RESPONSABLES.CSV n'a été sélectionné !<br />\n";
				//echo "<a href='disciplines.php'>Cliquer ici </a> pour recommencer !</center></p>";
				echo "<a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>\n";

			} else {
				echo "<p>Le fichier sélectionné n'est pas valide !<br />\n";
				//echo "<a href='disciplines.php'>Cliquer ici </a> pour recommencer !</center></p>";
				echo "<a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>\n";
			}



			$csv_file = isset($_FILES["adr_file"]) ? $_FILES["adr_file"] : NULL;
			//echo strtoupper($csv_file['name'])."<br />";
			if(strtoupper($csv_file['name']) == "ADRESSES.CSV") {
				$fp=fopen($csv_file['tmp_name'],"r");
				if(!$fp){
					echo "<p>Impossible d'ouvrir le fichier ADRESSES.CSV.</p>";
					echo "<p><a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>";
				}
				else{
					echo "<p>Lecture du fichier ADRESSES.CSV</p>";

					// On vide la table avant traitement (au cas où il aurait fallu s'y prendre à deux fois)
					$sql="TRUNCATE TABLE resp_adr";
					$res_truncate=mysql_query($sql);

					// on constitue le tableau des champs à extraire
					$tabchamps=array("adr_id","adr1","adr2","adr3","adr4","cp","pays","commune");

					$nblignes=0;
					while (!feof($fp)) {
						$ligne = fgets($fp, 4096);
						if($nblignes==0){
							// Quand on enregistre en CSV des fichiers DBF de GEP avec OpenOffice, les champs sont renommés avec l'ajout de ',...' en fin de nom de champ.
							// On ne retient pas ces ajouts pour $en_tete
							$temp=explode(";",$ligne);
							for($i=0;$i<sizeof($temp);$i++){
								$temp2=explode(",",$temp[$i]);
								$en_tete[$i]=$temp2[0];
							}

							$nbchamps=sizeof($en_tete);
						}
						$nblignes++;
					}
					fclose ($fp);

					// On range dans tabindice les indices des champs retenus
					unset($tabindice);
					for ($k = 0; $k < count($tabchamps); $k++) {
						for ($i = 0; $i < count($en_tete); $i++) {
							if (trim($en_tete[$i]) == $tabchamps[$k]) {
								$tabindice[] = $i;
								//echo "Champ $tabchamps[$k] trouvé à l'indice $i<br />";
							}
						}
					}

					//=========================
					$fp=fopen($csv_file['tmp_name'],"r");
					// On lit une ligne pour passer la ligne d'entête:
					$ligne = fgets($fp, 4096);
					//=========================
					$nb_reg_no2=0;
					$nb_record2=0;
					for($k = 1; ($k < $nblignes+1); $k++){
						//$ligne = dbase_get_record($fp,$k);
						if(!feof($fp)){
							$ligne = fgets($fp, 4096);
							if(trim($ligne)!=""){
								$tabligne=explode(";",$ligne);
								for($i = 0; $i < count($tabchamps); $i++) {
									//$ind = $tabindice[$i];
									$affiche[$i] = traitement_magic_quotes(corriger_caracteres(dbase_filter(trim($tabligne[$tabindice[$i]]))));
								}
								$sql="insert into resp_adr set
											adr_id = '$affiche[0]',
											adr1 = '$affiche[1]',
											adr2 = '$affiche[2]',
											adr3 = '$affiche[3]',
											adr4 = '$affiche[4]',
											cp = '$affiche[5]',
											pays = '$affiche[6]',
											commune = '$affiche[7]'
											";
								//echo "$sql<br />\n";
								$req = mysql_query($sql);
								if(!$req) {
									$nb_reg_no2++;
									echo mysql_error();
									echo "<font color='red'>Erreur</font> lors de l'insertion de l'adresse $affiche[1], $affiche[2], $affiche[3], $affiche[4], $affiche[5], $affiche[7], ($affiche[6]), avec le numéro $affiche[0].<br />\n";
								} else {
									$nb_record2++;
									echo "Insertion de l'adresse $affiche[1], $affiche[2], $affiche[3], $affiche[4], $affiche[5], $affiche[7], ($affiche[6]), avec le numéro $affiche[0].<br />\n";
								}
							}
						}
					}
					//dbase_close($fp);
					fclose($fp);

					if ($nb_reg_no2 != 0) {
						echo "<p>Lors de l'enregistrement des données de ADRESSES.CSV, il y a eu $nb_reg_no2 erreurs. Essayez de trouvez la cause de l'erreur.</p>\n";
					} else {
						echo "<p>L'importation des adresses de responsables dans la base GEPI a été effectuée avec succès (".$nb_record2." enregistrements au total).</p>\n";
					}

					if(count($tab_elenoet_non_trouves)>0){
						echo "<h2>ATTENTION</h2>\n";
						echo "<p>Récapitulatif des ELENOET non trouvés dans votre table 'eleves':<br />\n";
						echo "$tab_elenoet_non_trouves[0]";
						for($i=0;$i<count($tab_elenoet_non_trouves);$i++){
							echo ", $tab_elenoet_non_trouves[$i]";
						}
						echo "</p>\n";
					}

				}
			} else if (trim($csv_file['name'])=='') {
				echo "<p>Aucun fichier ADRESSES.CSV n'a été sélectionné !<br />\n";
				//echo "<a href='disciplines.php'>Cliquer ici </a> pour recommencer !</center></p>";
				echo "<a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>\n";

			} else {
				echo "<p>Le fichier sélectionné n'est pas valide !<br />\n";
				//echo "<a href='disciplines.php'>Cliquer ici </a> pour recommencer !</center></p>";
				echo "<a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>\n";
			}


			if(($nb_reg_no1==0)&&($nb_reg_no2==0)&&($nb_reg_no3==0)&&($erreur==0)){
				echo "<p>L'opération s'est correctement déroulée.</p>\n";
				echo "<center><p><a href='../accueil.php'>Retourner à l'accueil</a></p></center>\n";

				// On renseigne le témoin de mise à jour effectuée:
				saveSetting("conv_new_resp_table", 1);
				saveSetting("import_maj_xml_sconet", 1);
			}
			else{
				echo "<p>Des erreurs se sont produites.</p>\n";
			}
		}
	}
}
require("../lib/footer.inc.php");
?>
