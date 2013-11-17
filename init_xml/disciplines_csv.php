<?php

@set_time_limit(0);
/*
* $Id$
*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
extract($_POST, EXTR_OVERWRITE);

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

include("../lib/initialisation_annee.inc.php");
$liste_tables_del = $liste_tables_del_etape_matieres;

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}


//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : Importation des matières";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
?>
<p class="bold"><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil initialisation</a></p>

<?php

echo "<center><h3 class='gepi'>Troisième phase d'initialisation<br />Importation des matières</h3></center>";

if (!isset($step1)) {
	$j=0;
	$flag=0;
	while (($j < count($liste_tables_del)) and ($flag==0)) {
		if (old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
			$flag=1;
		}
		$j++;
	}
	if ($flag != 0){
		echo "<p><b>ATTENTION ...</b><br />";
		echo "Des données concernant les matières sont actuellement présentes dans la base GEPI<br /></p>";
		echo "<p>Si vous poursuivez la procédure les données telles que notes, appréciations, ... seront effacées.</p>";
		echo "<p>Seules la table contenant les matières et la table mettant en relation les matières et les professeurs seront conservées.</p>";

		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>";
		echo add_token_field();
		echo "<input type=hidden name='step1' value='y' />";
		echo "<input type='submit' name='confirm' value='Poursuivre la procédure' />";
		echo "</form>";
		echo "</div>";
		require("../lib/footer.inc.php");
		die();
	}
}

if (!isset($is_posted)) {
	if(isset($step1)) {
		$j=0;
		while ($j < count($liste_tables_del)) {
			if (old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
				$del = @mysqli_query($GLOBALS["mysqli"], "DELETE FROM $liste_tables_del[$j]");
			}
			$j++;
		}
	}

	echo "<p><b>ATTENTION ...</b><br />Vous ne devez procéder à cette opération uniquement si la constitution des classes a été effectuée !</p>";
	echo "<p>Importation du fichier <b>F_tmt.csv</b> contenant les données relatives aux matières : veuillez préciser le nom complet du fichier <b>F_tmt.csv</b>.";
	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>";
	echo add_token_field();
	echo "<input type=hidden name='is_posted' value='yes' />";
	echo "<input type=hidden name='step1' value='y' />";
	echo "<p><input type='file' size='80' name='dbf_file' />";
	echo "<p><input type=submit value='Valider' />";
	echo "</form>";

} else {
	check_token(false);

	$dbf_file = isset($_FILES["dbf_file"]) ? $_FILES["dbf_file"] : NULL;
	if(mb_strtoupper($dbf_file['name']) == "F_TMT.CSV") {
		$fp = fopen($dbf_file['tmp_name'],"r");
		if(!$fp) {
			echo "<p>Impossible d'ouvrir le fichier dbf</p>";
			echo "<p><a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>";
		} else {
			// on constitue le tableau des champs à extraire
			$tabchamps = array("MATIMN","MATILC");

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

					//$en_tete=explode(";",$ligne);
					$nbchamps=sizeof($en_tete);
				}
				$nblignes++;
			}
			fclose ($fp);

			$cpt_tmp=0;
			for ($k = 0; $k < count($tabchamps); $k++) {
				for ($i = 0; $i < count($en_tete); $i++) {
					if (trim($en_tete[$i]) == $tabchamps[$k]) {
						$tabindice[$cpt_tmp]=$i;
						$cpt_tmp++;
					}
				}
			}
			echo "<p>Dans le tableau ci-dessous, les identifiants en rouge correspondent à des nouvelles matières dans la base GEPI. les identifiants en vert correspondent à des identifiants de matières détectés dans le fichier GEP mais déjà présents dans la base GEPI.<br /><br />Il est possible que certaines matières ci-dessous, bien que figurant dans le fichier CSV, ne soient pas utilisées dans votre établissement cette année. C'est pourquoi il vous sera proposé en fin de procédure d'initialsation, un nettoyage de la base afin de supprimer ces données inutiles.</p>";
			echo "<table class='boireaus' border=1 cellpadding=2 cellspacing=2>";
			echo "<tr><th><p class=\"small\">Identifiant de la matière</p></th><th><p class=\"small\">Nom complet</p></th></tr>";

			$alt=1;
			//=========================
			$fp=fopen($dbf_file['tmp_name'],"r");
			// On lit une ligne pour passer la ligne d'entête:
			$ligne = fgets($fp, 4096);
			//=========================
			$nb_reg_no = 0;
			for($k = 1; ($k < $nblignes+1); $k++){
				if(!feof($fp)){
					$ligne = preg_replace('/"/','',fgets($fp, 4096));
					if(trim($ligne)!=""){
						//echo "<tr><td>";
						$tabligne=explode(";",$ligne);
						for($i = 0; $i < count($tabchamps); $i++) {
							$affiche[$i] = nettoyer_caracteres_nom($tabligne[$tabindice[$i]], "an", "&_.' -", "");
							//echo "\$tabligne[".$tabindice[$i]."]=".$tabligne[$tabindice[$i]]."<br />";
							//echo "\$affiche[$i]=$affiche[$i]<br />";
						}
						//echo "</td></tr>";

						$alt=$alt*(-1);

						$verif = mysqli_query($GLOBALS["mysqli"], "select matiere, nom_complet from matieres where matiere='$affiche[0]'");
						$resverif = mysqli_num_rows($verif);
						if($resverif == 0) {
							$req = mysqli_query($GLOBALS["mysqli"], "insert into matieres set matiere='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], nettoyer_caracteres_nom(remplace_accents($affiche[0],''),"an","_-","")) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."', nom_complet='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $affiche[1]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."', priority='0',matiere_aid='n',matiere_atelier='n'");
							if(!$req) {
								$nb_reg_no++; echo ((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false));
							} else {
								echo "<tr class='lig$alt white_hover'><td><p><font color='red'>$affiche[0]</font></p></td><td><p>".htmlentities($affiche[1])."</p></td></tr>";
							}
						} else {
							$nom_complet = old_mysql_result($verif,0,'nom_complet');
							echo "<tr class='lig$alt white_hover'><td><p><font color='green'>$affiche[0]</font></p></td><td><p>".htmlentities($nom_complet)."</p></td></tr>";
						}
					}
				}
			}
			echo "</table>";

			fclose($fp);
			if ($nb_reg_no != 0) {
				echo "<p>Lors de l'enregistrement des données il y a eu $nb_reg_no erreurs. Essayez de trouvez la cause de l'erreur et recommencez la procédure avant de passer à l'étape suivante.";
			} else {
				echo "<p>L'importation des matières dans la base GEPI a été effectuée avec succès !<br />Vous pouvez procéder à la quatrième phase d'importation des professeurs.</p>";
			}
			echo "<p align='center'><a href='prof_csv.php?a=a".add_token_in_url()."'>Importation des professeurs</a></p>";
		}
	} else if (trim($dbf_file['name'])=='') {
		echo "<p>Aucun fichier n'a été sélectionné !<br />";
		echo "<a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>";

	} else {
		echo "<p>Le fichier sélectionné n'est pas valide !<br />";
		echo "<a href='".$_SERVER['PHP_SELF']."'>Cliquer ici </a> pour recommencer !</center></p>";
	}
}
echo "<p><br /></p>";
require("../lib/footer.inc.php");
?>
