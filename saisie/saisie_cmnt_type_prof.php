<?php
/*
 * $Id: saisie_cmnt_type_prof.php 5969 2010-11-23 18:39:40Z crob $
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

// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

// Initialisations files
require_once("../lib/initialisations.inc.php");

// Resume session
//$resultat_session = resumeSession();
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
	die();
}
//include("../fckeditor/fckeditor.php") ;

// Check access
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

// On n'autorise que les profs à accéder à cette page
if($_SESSION['statut']!='professeur') {
    header("Location: ../logout.php?auto=1");
    die();
}

//================================
$titre_page = "Saisie de commentaires-types";
require_once("../lib/header.inc");
//================================

if (!loadSettings()) {
	die("Erreur chargement settings");
}

$sql="CREATE TABLE commentaires_types_profs (
id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
login VARCHAR( 255 ) NOT NULL ,
app TEXT NOT NULL
);";
$resultat_creation_table=mysql_query($sql);

?>

<?php

//echo "<p class='bold'><a href='../accueil.php'>Retour</a></p>\n";

echo "<script type='text/javascript'>
	function refresh_opener() {
		ad=window.opener.location.href;
		//alert(ad);
		window.opener.location.href=ad;
	}
</script>\n";

//echo "<p class='bold'><a href='../accueil.php' onClick='refresh_opener();self.close();return false;'>Refermer</a>";
echo "<p class='bold'><a href='../accueil.php' onClick='self.close();return false;'>Refermer</a>";
echo " | <a href='".$_SERVER['PHP_SELF']."?import_cmnt=y'>Importer un fichier d'appréciations</a>";
echo " | <a href='export_cmnt_type_prof.php'>Exporter le fichier d'appréciations</a>";
echo "</p>\n";

$import_cmnt=isset($_GET['import_cmnt']) ? $_GET['import_cmnt'] : NULL;
$valide_import_cmnt=isset($_POST['valide_import_cmnt']) ? $_POST['valide_import_cmnt'] : NULL;

if(isset($import_cmnt)) {
	echo "<h3 class='gepi'>Import d'un fichier d'appréciations:</h3>\n";

	echo "<p>Le fichier doit contenir un commentaire-type par ligne.</p>\n";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	echo add_token_field();
	echo "<p><input type=\"file\" size=\"80\" name=\"csv_file\" /></p>\n";
	echo "<p><input type='submit' name='valide_import_cmnt' value='Valider' /></p>\n";
	echo "</form>\n";

	echo "<p><br /></p>\n";
	require("../lib/footer.inc.php");
	die();
}
elseif(isset($valide_import_cmnt)) {
	check_token(false);

	$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;

	if (trim($csv_file['name'])=='') {
		echo "<p>Aucun fichier n'a été sélectionné !<br />\n";
		echo "<a href='".$_SERVER['PHP_SELF']."?import_cmnt=y'>Cliquer ici</a> pour recommencer !</p>\n";
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
				echo "<p><a href='".$_SERVER['PHP_SELF']."?id_racine=$id_racine'>Cliquer ici</a> pour recommencer !</center></p>\n";
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

						$sql="SELECT 1=1 FROM commentaires_types_profs WHERE login='".$_SESSION['login']."' AND app='".addslashes($ligne)."';";
						//echo "$sql<br />";
						$test=mysql_query($sql);
						if(mysql_num_rows($test)==0) {
							$sql="INSERT INTO commentaires_types_profs SET login='".$_SESSION['login']."', app='".addslashes($ligne)."';";
							//echo "$sql<br />";
							$insert=mysql_query($sql);
							if($insert) {
								$nb_reg++;
							}
							else {
								echo "<span style='color:red;'><b>Erreur lors de l'insertion de l'appréciation:</b> $ligne</span><br />\n";
								$temoin_erreur='y';
							}
						}
					}
					if($nb_reg>=$nb_max_reg) {
						echo "<p style='color:red;'>On n'enregistre pas plus de $nb_max_reg appréciations lors d'un import.</p>";
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

$compteur_nb_commentaires=isset($_POST['compteur_nb_commentaires']) ? $_POST['compteur_nb_commentaires'] : NULL;

if(isset($compteur_nb_commentaires)) {
	check_token(false);
	// Nettoyage des commentaires déjà saisis pour cette classe et ces périodes:
	$sql="DELETE FROM commentaires_types_profs WHERE login='".$_SESSION['login']."';";
	//echo "sql=$sql<br />";
	$resultat_nettoyage=mysql_query($sql);

	// Validation des saisies/modifs...
	for($i=1;$i<=$compteur_nb_commentaires;$i++){
		if(!isset($suppr[$i])) {
			$nom_log = "commentaire_".$i;
			if (isset($NON_PROTECT[$nom_log])){
				$commentaire_courant = traitement_magic_quotes(corriger_caracteres($NON_PROTECT[$nom_log]));

				if($commentaire_courant!=""){
					$sql="INSERT INTO commentaires_types_profs SET login='".$_SESSION['login']."', app='$commentaire_courant';";
					//echo "sql=$sql<br />";
					$resultat_insertion_commentaire=mysql_query($sql);
				}
			}
		}
	}
}


// Recherche des commentaires déjà saisis:
$sql="SELECT DISTINCT app,id FROM commentaires_types_profs WHERE login='".$_SESSION['login']."' ORDER BY app;";
//echo "$sql";
$resultat_commentaires=mysql_query($sql);
$cpt=1;
if(mysql_num_rows($resultat_commentaires)!=0){
	echo "<p>Voici la liste de vos commentaires-types:</p>\n";
	echo "<blockquote>\n";
	echo "<table class='boireaus' border='1' summary='Appréciations-types saisies'>\n";
	echo "<tr style='text-align:center;'>\n";
	echo "<th>Commentaire</th>\n";
	echo "<th>Supprimer</th>\n";
	echo "</tr>\n";

	$precedent_commentaire="";

	//$cpt=1;
	$alt=1;
	while($ligne_commentaire=mysql_fetch_object($resultat_commentaires)){
		if("$ligne_commentaire->app"!="$precedent_commentaire"){
			$alt=$alt*(-1);
			echo "<tr class='lig$alt' style='text-align:center;'>\n";

			echo "<td>";
			//echo "<textarea name='commentaire[$cpt]' cols='60'>".stripslashes($ligne_commentaire->commentaire)."</textarea>";
			echo "<textarea name='no_anti_inject_commentaire_".$cpt."' cols='60' onchange='changement()'>".stripslashes($ligne_commentaire->app)."</textarea>";
			echo "</td>\n";

			echo "<td><input type='checkbox' name='suppr[$cpt]' value='$ligne_commentaire->id' /></td>\n";
			echo "</tr>\n";
			$cpt++;
			$precedent_commentaire="$ligne_commentaire->app";
		}
	}
	echo "</table>\n";
	echo "</blockquote>\n";
}

echo "<p>Saisie d'un nouveau commentaire:</p>";
echo "<blockquote>\n";
//echo "<textarea name='commentaire[$cpt]' cols='60'></textarea><br />\n";
echo "<textarea name='no_anti_inject_commentaire_".$cpt."' id='no_anti_inject_commentaire_".$cpt."' cols='60' onchange='changement()'></textarea><br />\n";

echo "<script type='text/javascript'>
	document.getElementById('no_anti_inject_commentaire_".$cpt."').focus();
</script>\n";

echo "<input type='hidden' name='compteur_nb_commentaires' value='$cpt' />\n";

echo "<center><input type='submit' name='ok' value='Valider' /></center>\n";
echo "</blockquote>\n";

echo "</form>\n";
echo "<p><br /></p>\n";

echo "<p><i>NOTES:</i></p>\n";
echo "<ul>
	<li>Les appréciations-types apparaissent dans la page de saisie des appréciations pour un enseignement lors du survol de l'icone qui vous a mené à cette page.</li>
	<li>Il est possible d'utiliser la chaine de caractères '_PRENOM_' dans un commentaire-type.<br />
	Lors de l'insertion, la chaine sera remplacée par le prénom de l'élève.</li>
	<li>Le genre n'est pas encore géré:<br />
	Le genre de l'élève dans des phrases comme 'Il doit se mettre au travail' ou 'Elle doit se mettre au travail' doit encore être corrigé à la main.<br />
	Il doit être possible de résoudre ce problème de la même façon que pour le prénom de l'élève en ajoutant des champs de formulaires cachés...</li>
	<li>Après modification des appréciations-types, il faut mettre à jour la page de saisie des appréciations pour que les modifications effectuées ici soient prises en compte.<br />Prenez soin de sauvegarder les appréciations éventuellement saisies avant de recharger la page.</li>
</ul>\n";
echo "<p><br /></p>\n";

require("../lib/footer.inc.php");
?>
