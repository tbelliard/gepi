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


//INSERT INTO droits VALUES ('/gestion/saisie_message_connexion.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Saisie de messages de connexion.', '');
// Check access
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

//================================
$titre_page = "Saisie de messages de connexion";
require_once("../lib/header.inc.php");
//================================

if (!loadSettings()) {
	die("Erreur chargement settings");
}

$sql="CREATE TABLE message_login (
id int(11) NOT NULL auto_increment,
texte text NOT NULL,
PRIMARY KEY  (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$resultat_creation_table=mysqli_query($GLOBALS["___mysqli_ston"], $sql);

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
//echo "<p class='bold'><a href='../accueil.php' onClick='self.close();return false;'>Refermer</a>";
echo "<p class='bold'><a href='gestion_connect.php#message_login'>Retour</a>";
echo " | <a href='".$_SERVER['PHP_SELF']."?import_message=y'>Importer un fichier de messages</a>";
//echo " | <a href='export_message.php'>Exporter le fichier de messages</a>";
echo "</p>\n";

$import_message=isset($_GET['import_message']) ? $_GET['import_message'] : NULL;
$valide_import_message=isset($_POST['valide_import_message']) ? $_POST['valide_import_message'] : NULL;

if(isset($import_message)) {
	echo "<h3 class='gepi'>Import d'un fichier de messages&nbsp;:</h3>\n";

	echo "<p>Le fichier doit contenir un message par ligne.</p>\n";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	echo add_token_field();
	echo "<p><input type=\"file\" size=\"80\" name=\"csv_file\" /></p>\n";
	echo "<p><input type='submit' name='valide_import_message' value='Valider' /></p>\n";
	echo "</form>\n";

	echo "<p><br /></p>\n";
	require("../lib/footer.inc.php");
	die();
}
elseif(isset($valide_import_message)) {
	check_token(false);

	$csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;

	if (trim($csv_file['name'])=='') {
		echo "<p>Aucun fichier n'a été sélectionné !<br />\n";
		echo "<a href='".$_SERVER['PHP_SELF']."?import_message=y'>Cliquer ici</a> pour recommencer !</p>\n";
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

						$sql="INSERT INTO message_login SET texte='".addslashes($ligne)."';";
						//echo "$sql<br />";
						$insert=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
						if($insert) {
							$nb_reg++;
						}
						else {
							echo "<span style='color:red;'><b>Erreur lors de l'insertion du message&nbsp;:</b> $ligne</span><br />\n";
							$temoin_erreur='y';
						}
					}
					if($nb_reg>=$nb_max_reg) {
						echo "<p style='color:red;'>On n'enregistre pas plus de $nb_max_reg messages lors d'un import.</p>";
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

$id_message=isset($_POST['id_message']) ? $_POST['id_message'] : array();
$suppr=isset($_POST['suppr']) ? $_POST['suppr'] : array();

$compteur_nb_messages=isset($_POST['compteur_nb_messages']) ? $_POST['compteur_nb_messages'] : NULL;

if(isset($compteur_nb_messages)){
	check_token();

	// Validation des saisies/modifs...
	for($i=1;$i<=$compteur_nb_messages;$i++){
		if(isset($suppr[$i])) {
			$sql="DELETE FROM message_login WHERE id='".$suppr[$i]."';";
			$resultat_suppr_message=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		}
		else {
			$nom_log = "message_".$i;
			if (isset($NON_PROTECT[$nom_log])){
				$message_courant = traitement_magic_quotes(corriger_caracteres($NON_PROTECT[$nom_log]));

				if($message_courant!=""){
					$sql="UPDATE message_login SET texte='$message_courant' WHERE id='".$id_message[$i]."';";
					//echo "sql=$sql<br />";
					$resultat_update_message=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
				}
			}
		}
	}

	$nom_log = "new_message";
	if (isset($NON_PROTECT[$nom_log])){
		$message_courant=traitement_magic_quotes(corriger_caracteres($NON_PROTECT[$nom_log]));
		if(trim($message_courant)!='') {
			$sql="INSERT INTO message_login SET texte='$message_courant';";
			//echo "sql=$sql<br />";
			$resultat_insertion_message=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		}
	}
}


// Recherche des messages déjà saisis:
$sql="SELECT * FROM message_login ORDER BY texte;";
//echo "$sql";
$resultat_messages=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
$cpt=1;
if(mysqli_num_rows($resultat_messages)!=0){
	echo "<p>Voici la liste de vos messages:</p>\n";
	echo "<blockquote>\n";
	echo "<table class='boireaus' border='1' summary='Messages'>\n";
	echo "<tr style='text-align:center;'>\n";
	echo "<th>Message</th>\n";
	echo "<th>Supprimer</th>\n";
	echo "<th>Aperçu</th>\n";
	echo "</tr>\n";

	$alt=1;
	while($ligne_message=mysqli_fetch_object($resultat_messages)){
		$alt=$alt*(-1);
		echo "<tr class='lig$alt' style='text-align:center;'>\n";

		echo "<td>\n";
		echo "<input type='hidden' name='id_message[$cpt]' value='$ligne_message->id' />\n";
		echo "<textarea name='no_anti_inject_message_".$cpt."' id='textarea_$ligne_message->id' cols='60' onchange='changement();' onblur='maj_apercu_message($ligne_message->id)'>".stripslashes($ligne_message->texte)."</textarea>";
		echo "</td>\n";

		echo "<td><input type='checkbox' name='suppr[$cpt]' value='$ligne_message->id' /></td>\n";
		echo "<td id='td_apercu_message_$ligne_message->id'>$ligne_message->texte</td>\n";
		echo "</tr>\n";
		$cpt++;
	}
	echo "</table>\n";
	echo "</blockquote>\n";
}

echo "<p>Saisie d'un nouveau message:</p>";
echo "<blockquote>\n";
//echo "<textarea name='message[$cpt]' cols='60'></textarea><br />\n";
echo "<textarea name='no_anti_inject_new_message' id='no_anti_inject_new_message' cols='60' onchange='changement()'></textarea><br />\n";

echo "<script type='text/javascript'>
	document.getElementById('no_anti_inject_new_message').focus();

	function maj_apercu_message(num) {
		if(document.getElementById('td_apercu_message_'+num)) {
			document.getElementById('td_apercu_message_'+num).innerHTML=document.getElementById('textarea_'+num).value;
		}
	}
</script>\n";

echo "<input type='hidden' name='compteur_nb_messages' value='$cpt' />\n";

echo "<center><input type='submit' name='ok' value='Valider' /></center>\n";
echo "</blockquote>\n";

echo "</form>\n";
echo "<p><br /></p>\n";

//echo "<p><i>NOTES:</i></p>\n";
//echo "<p><br /></p>\n";

require("../lib/footer.inc.php");
?>
