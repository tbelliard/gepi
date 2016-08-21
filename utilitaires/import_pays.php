<?php
/*
 * $Id$
 *
 * Copyright 2001-2016 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

// INSERT INTO `droits` VALUES ('/utilitaires/import_pays.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Import des pays', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

$is_posted=isset($_POST['is_posted']) ? $_POST['is_posted'] : NULL;

/*
$msg="";
if(isset($_POST['valide_insertion_pays']) {
	$code_pays=isset($_POST['code_pays']) ? $_POST['code_pays'] : NULL;
	$nom_pays=isset($_POST['nom_pays']) ? $_POST['nom_pays'] : NULL;


}
*/

//**************** EN-TETE *****************
$titre_page = "Import des pays";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

echo "<p class='bold'>";
echo "<a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a>";
if(acces("/eleves/import_communes.php", $_SESSION['statut'])) {
	echo " | <a href=\"../eleves/import_communes.php\">Import des communes</a>";
}

if(!isset($is_posted)) {
	echo "</p>\n";

	echo "<p>Cette page est destinée à importer les correspondances identifiant_pays/nom_pays d'après un fichier CSV.</p>\n";
	
	//echo "<p>Veuillez préciser le nom complet du fichier <b>CSV</b> à importer.";
	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='POST'>\n";
	echo "<input type='hidden' name='is_posted' value='yes' />\n";

	if ($gepiSettings['unzipped_max_filesize']>=0) {
		echo "<p>Sélectionnez le fichier <b>pays.csv.zip</b>&nbsp;:<br />\n";
	}
	else {
		echo "<p>Veuillez dézipper le fichier (<i>évitez de l'ouvrir/modifier/enregistrer avec un tableur</i>) et fournissez le fichier <b>pays.csv</b>&nbsp;:<br />\n";
	}

	echo add_token_field();

	echo "<input type=\"file\" size=\"80\" name=\"csv_file\" /></p>\n";
	echo "<p><input type=submit value='Valider' /></p>\n";
	echo "</form>\n";
	
	echo "<p><br /</p>\n";
	
	
	echo "<p><i>NOTE</i>&nbsp;:</p>\n";
	echo "<p style='margin-left:3em;'>Le format du fichier CSV est le suivant&nbsp;:<br /><b>code_pays;nom_pays</b><br />Le fichier proposé à l'import est <a href='https://www.sylogix.org/attachments/122/pays.csv.zip'>pays.csv.zip</a>.<br />Il provient d'une extraction du fichier Geographique.xml de Sconet légerement retouché/complété.</p>\n";
}
else {
	echo " | <a href=\"".$_SERVER['PHP_SELF']."\">Import des pays</a>";
	echo "</p>\n";

	check_token(false);

	if(!isset($_POST['valide_insertion_pays'])) {
		$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;
		
		if (trim($csv_file['name'])=='') {
			echo "<p>Aucun fichier n'a été sélectionné !<br />\n";
			echo "<a href='".$_SERVER['PHP_SELF']."'>Cliquer ici</a> pour recommencer !</center></p>";
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

			$tempdir=get_user_temp_directory();
			if(!$tempdir) {
				echo "<p style='color:red'>Il semble que le dossier temporaire de l'utilisateur ".$_SESSION['login']." ne soit pas défini!?</p>\n";
				// Il ne faut pas aller plus loin...
				// SITUATION A GERER
				require("../lib/footer.inc.php");
				die();
			}

			$post_max_size=ini_get('post_max_size');
			$upload_max_filesize=ini_get('upload_max_filesize');
			$max_execution_time=ini_get('max_execution_time');
			$memory_limit=ini_get('memory_limit');

			$source_file=$csv_file['tmp_name'];
			$dest_file="../temp/".$tempdir."/pays.csv";

			$unzipped_max_filesize=getSettingValue('unzipped_max_filesize')*1024*1024;
			if($unzipped_max_filesize>=0) {
				$fichier_emis=$csv_file['name'];
				$extension_fichier_emis=mb_strtolower(strrchr($fichier_emis,"."));
				if (($extension_fichier_emis==".zip")||($csv_file['type']=="application/zip"))
					{

					$dest_zip_file="../temp/".$tempdir."/pays.csv.zip";
					$res_copy=copy("$source_file" , "$dest_zip_file");

					require_once('../lib/pclzip.lib.php');
					//$archive = new PclZip($dest_file);
					$archive = new PclZip($dest_zip_file);

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
						echo "<p style='color:red;'>Erreur : La taille du fichier extrait (<i>".$list_file_zip[0]['size']." octets</i>) dépasse la limite paramétrée (<i>$unzipped_max_filesize octets</i>).</p>\n";
						require("../lib/footer.inc.php");
						die();
					}

					$res_extract=$archive->extract(PCLZIP_OPT_PATH, "../temp/".$tempdir);
					if ($res_extract != 0) {
						echo "<p>Le fichier uploadé a été dézippé.</p>\n";
						$fichier_extrait=$res_extract[0]['filename'];
						//echo "Fichier extrait: ".$fichier_extrait."<br />";
						//unlink("$dest_file"); // Pour Wamp...
						$res_copy=rename("$fichier_extrait" , "$dest_file");
					}
					else {
						echo "<p style='color:red'>Echec de l'extraction de l'archive ZIP.</p>\n";
						require("../lib/footer.inc.php");
						die();
					}
				}
				else {
					$res_copy=copy("$source_file" , "$dest_file");
				}
			}
			else {
				$res_copy=copy("$source_file" , "$dest_file");
			}

			//echo "\$dest_file=$dest_file<br />";

			/*
			$handle = fopen ($dest_file, "r");
			$contents = fread ($handle, filesize ($dest_file));
			fclose ($handle);
			echo "<pre>$contents</pre>";
			*/

			//$fp=fopen($csv_file['tmp_name'],"r");
			$fp=fopen($dest_file,"r");

			if(!$fp){
				echo "<p>Impossible d'ouvrir le fichier CSV !</p>\n";
				echo "<p><a href='".$_SERVER['PHP_SELF']."'>Cliquer ici</a> pour recommencer !</center></p>\n";
			}
			else {

				// on constitue le tableau des champs à extraire
				$tabchamps=array("code_pays", "nom_pays");
		
				$ligne=fgets($fp, 4096);
				$temp=explode(";",$ligne);
				for($i=0;$i<sizeof($temp);$i++){
					$en_tete[$i]=preg_replace('/"/','',$temp[$i]);
				}
				$nbchamps=sizeof($en_tete);
				fclose($fp);
		
				// On range dans tabindice les indices des champs retenus
				$temoin=0;
				for($k=0;$k<count($tabchamps);$k++){
					for($i=0;$i<count($en_tete);$i++){
						if(mb_strtolower(trim($en_tete[$i]))==mb_strtolower($tabchamps[$k])) {
							$tabindice[$k]=$i;
							//echo "\$tabindice[$k]=$tabindice[$k]<br />";
							$temoin++;
						}
					}
				}
		
				if($temoin!=count($tabchamps)) {
					echo "<p><b>ERREUR:</b> La ligne d'entête du fichier n'est pas conforme à ce qui est attendu.</p>\n";
					echo "<p><a href='".$_SERVER['PHP_SELF']."'>Cliquer ici</a> pour recommencer !</center></p>\n";
					require("../lib/footer.inc.php");
					die();
				}

				//$fp=fopen($csv_file['tmp_name'],"r");
				$fp=fopen($dest_file,"r");
				// On lit une ligne pour passer la ligne d'entête:
				$ligne = fgets($fp, 4096);
				//=========================
				unset($code_pays);
				$code_pays=array();
				unset($nom_pays);
				$nom_pays=array();

				//$info_erreur="";
		
				// Initialisation:
				$code_pays=array();
				$nom_pays=array();
	
				while(!feof($fp)){
					$ligne = fgets($fp, 4096);
					if(trim($ligne)!=""){
						$ligne=trim($ligne);
						//echo "<p>ligne=$ligne<br />\n";
	
						$tabligne=explode(";",preg_replace('/"/','',$ligne));
	
						$code_pays[]=preg_replace("/[^0-9]/","",corriger_caracteres($tabligne[$tabindice[0]]));
						//$nom_pays[]=my_ereg_replace("[^a-zA-Z0-9ÀÄÂÉÈÊËÎÏÔÖÙÛÜÇçàäâéèêëîïôöùûü_. ()'-]","",corriger_caracteres(html_entity_decode(my_ereg_replace("&#039;","'",$tabligne[$tabindice[1]]))));
						//echo $tabligne[$tabindice[1]]." -&gt; ".my_ereg_replace("[^a-zA-Z0-9ÀÄÂÉÈÊËÎÏÔÖÙÛÜÇçàäâéèêëîïôöùûü_. ()'-]","",corriger_caracteres(html_entity_decode(my_ereg_replace("&#039;","'",$tabligne[$tabindice[1]]))))."</p>";


						$nom_pays[]=preg_replace("/[^a-zA-Z0-9ÀÄÂÉÈÊËÎÏÔÖÙÛÜÇçàäâéèêëîïôöùûü_. ()'-]/","",corriger_caracteres(html_entity_decode(preg_replace("/&#039;/","'",$tabligne[$tabindice[1]]))));
						//echo $tabligne[$tabindice[1]]." -&gt; ".preg_replace("/[^a-zA-Z0-9ÀÄÂÉÈÊËÎÏÔÖÙÛÜÇçàäâéèêëîïôöùûü_. ()'-]/","",corriger_caracteres(html_entity_decode(my_ereg_replace("&#039;","'",$tabligne[$tabindice[1]]))))."</p>";
					}
				}
				fclose($fp);
	
	
				echo "<div align='center'>\n";
				echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
				echo "<input type='hidden' name='is_posted' value='y' />\n";
				echo "<input type='hidden' name='valide_insertion_pays' value='y' />\n";
				echo "<p align=\"center\"><input type=submit value=\"Importer\" /></p>\n";

				echo add_token_field();

				$tab_code_pays_connus=array();
				$tab_nom_pays_connus=array();
				$sql="SELECT * FROM pays;";
				$res_pays=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_pays)>0) {
					while($lig=mysqli_fetch_object($res_pays)) {
						$tab_code_pays_connus[]=$lig->code_pays;
						$tab_nom_pays_connus[$lig->code_pays]=$lig->nom_pays;
						//echo "\$tab_nom_pays_connus[$lig->code_pays]=".$tab_nom_pays_connus[$lig->code_pays]."<br />";
					}
				}

				$nb_pays=0;
				echo "<table class='boireaus' summary='Pays'>\n";
				echo "<tr>\n";
				echo "<th>Code pays</th>\n";
				echo "<th>Nom pays</th>\n";
				echo "<th>Information</th>\n";
				echo "</tr>\n";
	
				$alt=1;
				for($i=0;$i<count($code_pays);$i++) {
					if(($code_pays[$i]!="")&&($nom_pays[$i]!="")) {
						$alt=$alt*(-1);
						echo "<tr class='lig$alt white_hover'>\n";

						if(in_array($code_pays[$i],$tab_code_pays_connus)) {
							if($nom_pays[$i]==$tab_nom_pays_connus[$code_pays[$i]]) {
								echo "<td>\n";
								echo $code_pays[$i];
								echo "</td>\n";
			
								echo "<td>\n";
								echo $nom_pays[$i];
								echo "</td>\n";
		
								echo "<td>\n";
								echo "Pays déjà enregistré";
								echo "</td>\n";
							}
							else {
								echo "<td>\n";
								echo "<input type='hidden' name='code_pays[$i]' value=\"".$code_pays[$i]."\" />\n";
								echo $code_pays[$i];
								echo "</td>\n";
			
								echo "<td>\n";
								echo "<input type='hidden' name='nom_pays[$i]' value=\"".$nom_pays[$i]."\" />\n";
								echo $nom_pays[$i];
								echo "</td>\n";
		
								echo "<td>\n";
								echo "Pays enregistré sous un autre nom&nbsp;: ".$tab_nom_pays_connus[$code_pays[$i]];
								echo "</td>\n";
							}
						}
						else {
							echo "<td>\n";
							echo "<input type='hidden' name='code_pays[$i]' value=\"".$code_pays[$i]."\" />\n";
							echo $code_pays[$i];
							echo "</td>\n";
		
							echo "<td>\n";
							echo "<input type='hidden' name='nom_pays[$i]' value=\"".$nom_pays[$i]."\" />\n";
							echo $nom_pays[$i];
							echo "</td>\n";
	
							echo "<td>\n";
							echo "Nouveau";
							echo "</td>\n";
						}
	
						echo "<tr>\n";
						$nb_pays++;
					}
				}
				echo "</tr>\n";
		
				echo "</table>\n";

				echo "<input type='hidden' name='nb_pays' value='$nb_pays' />\n";

				echo "<p align=\"center\"><input type=submit value=\"Importer\" /></p>\n";
				echo "</form>\n";
		
				echo "</div>\n";
				//================

				//unlink($dest_file);
			}
		}
	}
	else {
		$code_pays=isset($_POST['code_pays']) ? $_POST['code_pays'] : NULL;
		$nom_pays=isset($_POST['nom_pays']) ? $_POST['nom_pays'] : NULL;
		$nb_pays=isset($_POST['nb_pays']) ? $_POST['nb_pays'] : NULL;

		$tab_code_pays_connus=array();
		$sql="SELECT code_pays FROM pays;";
		$res_pays=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_pays)>0) {
			while($lig=mysqli_fetch_object($res_pays)) {
				$tab_code_pays_connus[]=$lig->code_pays;
			}
		}

		$nb_err=0;
		$nb_reg=0;
		if(!isset($code_pays)) {
			echo "<p>Aucun pays n'a été proposé à l'enregistrement.</p>\n";
		}
		else {
			//for($i=0;$i<count($code_pays);$i++) {
			for($i=0;$i<$nb_pays;$i++) {
				if(isset($code_pays[$i])) {
					if(in_array($code_pays[$i],$tab_code_pays_connus)) {
						$sql="UPDATE pays SET nom_pays='".addslashes(preg_replace("/[^a-zA-Z0-9ÀÄÂÉÈÊËÎÏÔÖÙÛÜÇçàäâéèêëîïôöùûü_. ()'-]/","",corriger_caracteres(html_entity_decode(preg_replace("/&#039;/","'",$nom_pays[$i])))))."' WHERE code_pays='$code_pays[$i]';";
					}
					else {
						$sql="INSERT INTO pays SET nom_pays='".addslashes(preg_replace("/[^a-zA-Z0-9ÀÄÂÉÈÊËÎÏÔÖÙÛÜÇçàäâéèêëîïôöùûü_. ()'-]/","",corriger_caracteres(html_entity_decode(preg_replace("/&#039;/","'",$nom_pays[$i])))))."', code_pays='$code_pays[$i]';";
					}
					//echo "$sql<br />";
					$res=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$res) {
						$nb_err++;
					}
					else {
						$nb_reg++;
					}
				}
			}

			echo "<p>";
			if($nb_reg==0) {
				echo "Aucun enregistrement n'a été effectué.<br />\n";
			}
			elseif($nb_reg==1) {
				echo "Un enregistrement a été effectué.<br />\n";
			}
			else {
				echo "$nb_reg enregistrements ont été effectués.<br />\n";
			}

			if($nb_err==1) {
				echo "Une erreur a eu lieu lors d'un enregistrement.";
			}
			elseif($nb_err>1) {
				echo "$nb_err erreurs ont eu lieu.";
			}
		}
	}
}


echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
