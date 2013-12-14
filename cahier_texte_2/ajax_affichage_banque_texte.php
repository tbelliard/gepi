<?php
/*
 *
 * Copyright 2009-2012 Josselin Jacquard, Stephane Boireau
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
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

header('Content-Type: text/html; charset=utf-8');

// On désamorce une tentative de contournement du traitement anti-injection lorsque register_globals=on
if (isset($_GET['traite_anti_inject']) OR isset($_POST['traite_anti_inject'])) $traite_anti_inject = "yes";

// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

require_once("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");
//echo("Debug Locale : ".setLocale(LC_TIME,0));

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}


$sql="SELECT 1=1 FROM droits WHERE id='/cahier_texte_2/ajax_affichage_banque_texte.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/cahier_texte_2/ajax_affichage_banque_texte.php',
administrateur='F',
professeur='V',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='CDT2: Banque de textes à insérer',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}


if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}


//On vérifie si le module est activé
if (getSettingValue("active_cahiers_texte")!='y') {
	die("Le module n'est pas activé.");
}

$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

$sql="CREATE TABLE banque_cdt (
id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
login VARCHAR( 255 ) NOT NULL ,
app TEXT NOT NULL
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$resultat_creation_table=mysqli_query($GLOBALS["mysqli"], $sql);

$editer_banque=isset($_POST['editer_banque']) ? $_POST['editer_banque'] : (isset($_GET['editer_banque']) ? $_GET['editer_banque'] : "n");
if($editer_banque=='y') {
	//================================
	$titre_page = "Saisie de textes-types";
	require_once("../lib/header.inc.php");
	//================================

	echo "<p class='bold'><a href='../accueil.php' onClick='self.close();return false;'>Refermer</a>";
	echo " | <a href='".$_SERVER['PHP_SELF']."?editer_banque=y&amp;import_txt=y'>Importer un fichier de textes-types</a>";
	//echo " | <a href='export_txt_type_prof.php'>Exporter le fichier d'appréciations</a>";
	echo "</p>\n";

	$import_txt=isset($_GET['import_txt']) ? $_GET['import_txt'] : NULL;
	$valide_import_txt=isset($_POST['valide_import_txt']) ? $_POST['valide_import_txt'] : NULL;

	if(isset($import_txt)) {
		echo "<h3 class='gepi'>Import d'un fichier de textes-types&nbsp;:</h3>\n";

		echo "<p>Le fichier doit contenir un texte-type par ligne.</p>\n";

		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
		echo add_token_field();
		echo "<input type=\"hidden\" name=\"editer_banque\" value=\"y\" />\n";
		echo "<p><input type=\"file\" size=\"80\" name=\"csv_file\" /></p>\n";
		echo "<p><input type='submit' name='valide_import_txt' value='Valider' /></p>\n";
		echo "</form>\n";

		echo "<p><br /></p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	elseif(isset($valide_import_txt)) {
		check_token(false);

		$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;

		if (trim($csv_file['name'])=='') {
			echo "<p>Aucun fichier n'a été sélectionné !<br />\n";
			echo "<a href='".$_SERVER['PHP_SELF']."?editer_banque=y&amp;import_txt=y'>Cliquer ici</a> pour recommencer !</p>\n";
		}
		else{

			//echo "mime_content_type(".$csv_file['tmp_name'].")=".mime_content_type($csv_file['tmp_name'])."<br />";
			//die();
			if(mime_content_type($csv_file['tmp_name'])!="text/plain") {
				echo "<p style='color:red;'>Le type du fichier ne convient pas: ".mime_content_type($csv_file['tmp_name'])."<br />\n";
				echo "Vous devez fournir un fichier TXT (<i>type bloc-notes</i>), pas un fichier traitement de texte ou quoi que ce soit d'autre.</p>\n";
			}
			else {
				$fp=fopen($csv_file['tmp_name'],"r");

				if(!$fp){
					echo "<p>Impossible d'ouvrir le fichier CSV !</p>\n";
					echo "<a href='".$_SERVER['PHP_SELF']."?editer_banque=y&amp;import_txt=y'>Cliquer ici</a> pour recommencer !</p>\n";
				}
				else{

					$fp=fopen($csv_file['tmp_name'],"r");

					$nb_max_reg=100;

					$nb_reg=0;
					$temoin_erreur='n';

					while(!feof($fp)){
						$ligne = fgets($fp, 4096);
						if(trim($ligne)!="") {
							$ligne=trim($ligne);

							$sql="SELECT 1=1 FROM banque_cdt WHERE login='".$_SESSION['login']."' AND app='".addslashes($ligne)."';";
							//echo "$sql<br />";
							$test=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($test)==0) {
								$sql="INSERT INTO banque_cdt SET login='".$_SESSION['login']."', app='".addslashes($ligne)."';";
								//echo "$sql<br />";
								$insert=mysqli_query($GLOBALS["mysqli"], $sql);
								if($insert) {
									$nb_reg++;
								}
								else {
									echo "<span style='color:red;'><b>Erreur lors de l'insertion du texte-type&nbsp;:</b> $ligne</span><br />\n";
									$temoin_erreur='y';
								}
							}
						}
						if($nb_reg>=$nb_max_reg) {
							echo "<p style='color:red;'>On n'enregistre pas plus de $nb_max_reg textes-types lors d'un import.</p>";
							break;
						}
					}
					fclose($fp);

					if(($nb_reg>0)&&($temoin_erreur=='n')) {echo "<span style='color:red;'>Import effectué.</span><br />";}
				}
			}
		}
	}

	echo "<form name='formulaire' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	echo add_token_field();

	$suppr=isset($_POST['suppr']) ? $_POST['suppr'] : "";

	$compteur_nb_txt=isset($_POST['compteur_nb_txt']) ? $_POST['compteur_nb_txt'] : NULL;

	if(isset($compteur_nb_txt)) {
		check_token(false);
		// Nettoyage des txt déjà saisis pour cette classe et ces périodes:
		$sql="DELETE FROM banque_cdt WHERE login='".$_SESSION['login']."';";
		//echo "sql=$sql<br />";
		$resultat_nettoyage=mysqli_query($GLOBALS["mysqli"], $sql);

		// Validation des saisies/modifs...
		for($i=1;$i<=$compteur_nb_txt;$i++){
			if(!isset($suppr[$i])) {
				$nom_log = "txt_".$i;
				if (isset($NON_PROTECT[$nom_log])){
					$txt_courant = traitement_magic_quotes(corriger_caracteres($NON_PROTECT[$nom_log]));

					if($txt_courant!=""){
						$sql="INSERT INTO banque_cdt SET login='".$_SESSION['login']."', app='$txt_courant';";
						//echo "sql=$sql<br />";
						$resultat_insertion_txt=mysqli_query($GLOBALS["mysqli"], $sql);
					}
				}
			}
		}
	}


	// Recherche des txt déjà saisis:
	$sql="SELECT DISTINCT app,id FROM banque_cdt WHERE login='".$_SESSION['login']."' ORDER BY app;";
	//echo "$sql";
	$resultat_txt=mysqli_query($GLOBALS["mysqli"], $sql);
	$cpt=1;
	if(mysqli_num_rows($resultat_txt)!=0){
		echo "<p>Voici la liste de vos textes-types&nbsp;:</p>\n";
		echo "<blockquote>\n";
		echo "<table class='boireaus' border='1' summary='Textes-types saisis'>\n";
		echo "<tr style='text-align:center;'>\n";
		echo "<th>Texte-type</th>\n";
		echo "<th>Supprimer</th>\n";
		echo "</tr>\n";

		$precedent_txt="";

		//$cpt=1;
		$alt=1;
		while($ligne_txt=mysqli_fetch_object($resultat_txt)){
			if("$ligne_txt->app"!="$precedent_txt"){
				$alt=$alt*(-1);
				echo "<tr class='lig$alt' style='text-align:center;'>\n";

				echo "<td>";
				echo "<textarea name='no_anti_inject_txt_".$cpt."' cols='60' onchange='changement()'>".$ligne_txt->app."</textarea>";
				echo "</td>\n";

				echo "<td><input type='checkbox' name='suppr[$cpt]' value='$ligne_txt->id' /></td>\n";
				echo "</tr>\n";
				$cpt++;
				$precedent_txt="$ligne_txt->app";
			}
		}
		echo "</table>\n";
		echo "</blockquote>\n";
	}

	echo "<p>Saisie d'un nouveau texte-type&nbsp;:</p>";
	echo "<blockquote>\n";
	//echo "<textarea name='txt[$cpt]' cols='60'></textarea><br />\n";
	echo "<textarea name='no_anti_inject_txt_".$cpt."' id='no_anti_inject_txt_".$cpt."' cols='60' onchange='changement()'></textarea><br />\n";

	echo "<script type='text/javascript'>
		document.getElementById('no_anti_inject_txt_".$cpt."').focus();
	</script>\n";

	echo "<input type='hidden' name='compteur_nb_txt' value='$cpt' />\n";
	echo "<input type='hidden' name='editer_banque' value='y' />\n";

	echo "<center><input type='submit' name='ok' value='Valider' /></center>\n";
	echo "</blockquote>\n";

	echo "</form>\n";
	echo "<p><br /></p>\n";

	echo "<p><em>NOTES&nbsp;:</em></p>
<ul>
	<li>Vous pouvez trouver utile de disposer de chaines du style de&nbsp;:<br />
	\"<span style='color:green'>Exercice&nbsp;&nbsp;page&nbsp;</span>\",<br />
	\"<span style='color:green'>Correction de l'e</span>\",<br />
	\"<span style='color:green'>Correction des e</span>\",<br />
	...</li>
	<li>Les retours à la ligne ne sont pas supportés (<em>peut-être dans une prochaine version de Gepi</em>).</li>
</ul>\n";

	require("../lib/footer.inc.php");
	die();
}

echo "<div style='float:right; width:16px;'><a href='".$_SERVER['PHP_SELF']."?editer_banque=y' title='Editer les textes-types' target='_blank'><img src='../images/edit16.png' width='16' height='16' alt='Editer les textes-types' /></a></div>\n";

$tab_txt=array();
/*
$tab_txt[]='Exercice  page ';
$tab_txt[]='Correction de l\\\'exercice  page ';
$tab_txt[]='\\\widehat{}';
$tab_txt[]='\\\frac{}{}';
$tab_txt[]='\\\sqrt{}';
$tab_txt[]='\\\newline';
$tab_txt[]='\\\phantom{}';
*/
$sql="SELECT DISTINCT app,id FROM banque_cdt WHERE login='".$_SESSION['login']."' ORDER BY app;";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)==0) {
	echo "<p>Aucun texte-type n'est encore saisi.</p>\n";
	echo "<p><a href='".$_SERVER['PHP_SELF']."?editer_banque=y' title='Editer les textes-types' target='_blank'>Editer les textes-types</a></p>\n";
}
else {
	echo "<p>Cliquez sur le texte à insérer&nbsp;:</p>\n";
	echo "<table class='boireaus'>\n";
	$alt=1;
	while($lig=mysqli_fetch_object($res)) {
	//for($loop=0;$loop<count($tab_txt);$loop++) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover'>\n";
		echo "<td style='text-align:left;'>\n";
		//echo "<a href=\"javascript:insere_texte_dans_ckeditor('".$tab_txt[$loop]."')\">".stripslashes($tab_txt[$loop])."</a><br />\n";
		echo "<a href=\"javascript:insere_texte_type_dans_ckeditor('texte_type_".$lig->id."')\">".$lig->app."</a><br />\n";
		echo "<div id='texte_type_".$lig->id."' style='display:none'>$lig->app</div>\n";
		echo "</td>\n";
		echo "</tr>\n";
	}
	echo "</table>\n";
}
?>
