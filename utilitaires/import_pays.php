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
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

//debug_var();

echo "<p class='bold'>";
echo "<a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

if(!isset($is_posted)) {
	echo "</p>\n";

	echo "<p>Cette page est destinée à importer les correspondances identifiant_pays/nom_pays d'après un fichier CSV.</p>\n";
	
	echo "<p>Veuillez préciser le nom complet du fichier <b>CSV</b> à importer.";
	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='POST'>\n";
	echo "<input type='hidden' name='is_posted' value='yes' />\n";
	echo "<p><input type=\"file\" size=\"80\" name=\"csv_file\" /></p>\n";
	echo "<p><input type=submit value='Valider' /></p>\n";
	echo "</form>\n";
	
	echo "<p><br /</p>\n";
	
	
	echo "<p><i>NOTE</i>&nbsp;:</p>\n";
	echo "<p style='margin-left:3em;'>Le format du fichier CSV est le suivant&nbsp;:<br /><b>code_pays;nom_pays</b><br />Le fichier proposé à l'import est <a href='https://www.sylogix.org/attachments/113/pays.csv.zip'>pays.csv.zip</a>.<br />Il provient d'une extraction du fichier Geographique.xml de Sconet légerement retouché/complété.</p>\n";
}
else {
	echo " | <a href=\"".$_SERVER['PHP_SELF']."\">Import des pays</a>";
	echo "</p>\n";

	if(!isset($_POST['valide_insertion_pays'])) {
		$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;
		
		if (trim($csv_file['name'])=='') {
			echo "<p>Aucun fichier n'a été sélectionné !<br />\n";
			echo "<a href='".$_SERVER['PHP_SELF']."'>Cliquer ici</a> pour recommencer !</center></p>";
		}
		else {
		
			$fp=fopen($csv_file['tmp_name'],"r");
		
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
					$en_tete[$i]=my_ereg_replace('"','',$temp[$i]);
				}
				$nbchamps=sizeof($en_tete);
				fclose($fp);
		
				// On range dans tabindice les indices des champs retenus
				$temoin=0;
				for($k=0;$k<count($tabchamps);$k++){
					for($i=0;$i<count($en_tete);$i++){
						if(strtolower(trim($en_tete[$i]))==strtolower($tabchamps[$k])) {
							$tabindice[$k]=$i;
							//echo "\$tabindice[$k]=$tabindice[$k]<br />";
							$temoin++;
						}
					}
				}
		
				if($temoin!=count($tabchamps)){
					echo "<p><b>ERREUR:</b> La ligne d'entête du fichier n'est pas conforme à ce qui est attendu.</p>\n";
					echo "<p><a href='".$_SERVER['PHP_SELF']."'>Cliquer ici</a> pour recommencer !</center></p>\n";
					require("../lib/footer.inc.php");
					die();
				}
		
				$fp=fopen($csv_file['tmp_name'],"r");
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
	
						$tabligne=explode(";",my_ereg_replace('"','',$ligne));
	
						$code_pays[]=my_ereg_replace("[^0-9]","",corriger_caracteres($tabligne[$tabindice[0]]));
						$nom_pays[]=my_ereg_replace("[^a-zA-Z0-9ÀÄÂÉÈÊËÎÏÔÖÙÛÜÇçàäâéèêëîïôöùûü_. ()'-]","",corriger_caracteres(html_entity_decode_all_version($tabligne[$tabindice[1]])));
	
					}
				}
				fclose($fp);
	
	
				echo "<div align='center'>\n";
				echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
				echo "<input type='hidden' name='is_posted' value='y' />\n";
				echo "<input type='hidden' name='valide_insertion_pays' value='y' />\n";
				echo "<p align=\"center\"><input type=submit value=\"Importer\" /></p>\n";

				$tab_code_pays_connus=array();
				$tab_nom_pays_connus=array();
				$sql="SELECT * FROM pays;";
				$res_pays=mysql_query($sql);
				if(mysql_num_rows($res_pays)>0) {
					while($lig=mysql_fetch_object($res_pays)) {
						$tab_code_pays_connus[]=$lig->code_pays;
						$tab_nom_pays_connus[$lig->code_pays]=$lig->nom_pays;
						echo "\$tab_nom_pays_connus[$lig->code_pays]=".$tab_nom_pays_connus[$lig->code_pays]."<br />";
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
			}
		}
	}
	else {
		$code_pays=isset($_POST['code_pays']) ? $_POST['code_pays'] : NULL;
		$nom_pays=isset($_POST['nom_pays']) ? $_POST['nom_pays'] : NULL;
		$nb_pays=isset($_POST['nb_pays']) ? $_POST['nb_pays'] : NULL;

		$tab_code_pays_connus=array();
		$sql="SELECT code_pays FROM pays;";
		$res_pays=mysql_query($sql);
		if(mysql_num_rows($res_pays)>0) {
			while($lig=mysql_fetch_object($res_pays)) {
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
						$sql="UPDATE pays SET nom_pays='".addslashes(my_ereg_replace("[^a-zA-Z0-9ÀÄÂÉÈÊËÎÏÔÖÙÛÜÇçàäâéèêëîïôöùûü_. ()'-]","",corriger_caracteres(html_entity_decode_all_version($nom_pays[$i]))))."' WHERE code_pays='$code_pays[$i]';";
					}
					else {
						$sql="INSERT INTO pays SET nom_pays='".addslashes(my_ereg_replace("[^a-zA-Z0-9ÀÄÂÉÈÊËÎÏÔÖÙÛÜÇçàäâéèêëîïôöùûü_. ()'-]","",corriger_caracteres(html_entity_decode_all_version($nom_pays[$i]))))."', code_pays='$code_pays[$i]';";
					}
					//echo "$sql<br />";
					$res=mysql_query($sql);
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
