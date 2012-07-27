<?php
/*
* Copyright 2001-2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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


$sql="SELECT 1=1 FROM droits WHERE id='/gestion/gestion_signature.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/gestion/gestion_signature.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Gestion signature',
statut='';";
$insert=mysql_query($sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$msg="";

$choix_acces=isset($_GET['choix_acces']) ? $_GET['choix_acces'] : NULL;

$dirname = "../backup/".getSettingValue("backup_directory");

if (isset($_POST['valid_sign'])) {
	check_token();

	$sign_file = isset($_FILES["sign_file"]) ? $_FILES["sign_file"] : NULL;

	if((!preg_match("/\.jpeg$/i", $sign_file['name']))&&(!preg_match("/\.jpg$/i", $sign_file['name']))) {
		$msg = "Seule l'extension JPG est autorisée.";
	}
	else {
		$tmp_dim_img=getimagesize($sign_file['tmp_name']);
		if((isset($tmp_dim_img[2]))&&($tmp_dim_img[2]==2)) {
			$ok = false;
			if ($f = @fopen("$dirname/.test", "w")) {
				@fputs($f, '<'.'?php $ok = true; ?'.'>');
				@fclose($f);
				include("$dirname/.test");
			}

			if (!$ok) {
				$msg = "Problème d'écriture sur le répertoire BACKUP.<br />Veuillez signaler ce problème à l'administrateur du site.<br />";
			} else {
				$old = getSettingValue("fichier_signature");

				if (file_exists($dirname."/".$old)) {@unlink($dirname."/".$old);}
				if (file_exists($dirname."/".$sign_file['name'])) {@unlink($dirname."/".$sign_file['name']);}
				$ok = @copy($sign_file['tmp_name'], $dirname."/".$sign_file['name']);
				if (!$ok) {$ok = @move_uploaded_file($sign_file['tmp_name'], $dirname."/".$sign_file['name']);}
				if (!$ok) {
					$msg = "Problème de transfert : le fichier n'a pas pu être transféré dans le répertoire BACKUP.<br />Veuillez signaler ce problème à l'administrateur du site<br />.";
				}
				else {
					$msg = "Le fichier a été transféré.";
				}

				if (!saveSetting("fichier_signature", $sign_file['name'])) {
					$msg .= "Erreur lors de l'enregistrement dans la table setting !";
				}

			}
		}
		else {
			$msg = "Le type de l'image est incorrect.<br />";
		}
	}
}

$sql="CREATE TABLE IF NOT EXISTS droits_acces_fichiers (
id INT(11) unsigned NOT NULL auto_increment,
fichier VARCHAR( 255 ) NOT NULL ,
identite VARCHAR( 255 ) NOT NULL ,
type VARCHAR( 255 ) NOT NULL,
PRIMARY KEY ( id )
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$create_table=mysql_query($sql);

if (isset($_POST['valid_choix_acces_sign'])) {
	check_token();

	$sql="DELETE FROM droits_acces_fichiers WHERE fichier='signature_img';";
	$menage=mysql_query($sql);

	$cpt=0;
	$statut_autorise=isset($_POST['statut_autorise']) ? $_POST['statut_autorise'] : array();
	for($loop=0;$loop<count($statut_autorise);$loop++) {
		$sql="INSERT INTO droits_acces_fichiers SET fichier='signature_img', type='statut', identite='".$statut_autorise[$loop]."';";
		$insert=mysql_query($sql);
		if(!$insert) {
			$msg.="Erreur : $sql<br />";
		}
		else {
			$cpt++;
		}
	}
	if($cpt>0) {
		$msg.="$cpt statut(s) autorisé(s).<br />";
	}

	$cpt=0;
	$compte_autorise=isset($_POST['compte_autorise']) ? $_POST['compte_autorise'] : array();
	for($loop=0;$loop<count($compte_autorise);$loop++) {
		$sql="INSERT INTO droits_acces_fichiers SET fichier='signature_img', type='individu', identite='".$compte_autorise[$loop]."';";
		$insert=mysql_query($sql);
		if(!$insert) {
			$msg.="Erreur : $sql<br />";
		}
		else {
			$cpt++;
		}
	}
	if($cpt>0) {
		$msg.="$cpt compte(s) autorisé(s).<br />";
	}
}



//**************** EN-TETE *********************
$titre_page = "Signature";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

echo "<p class=bold><a href=\"param_gen.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";

$nom_fichier_signature = getSettingValue("fichier_signature");

$sql="SELECT * FROM droits_acces_fichiers WHERE fichier='signature_img' ORDER BY type, identite;";
$res=mysql_query($sql);

if(!isset($choix_acces)) {
	if(($nom_fichier_signature!="")&&(file_exists($dirname."/".$nom_fichier_signature))) {
		echo " | <a href='".$_SERVER['PHP_SELF']."?choix_acces=y'>Choisir les comptes ou statuts autorisés</a>";
	}
	echo "</p>\n";

	echo "<form enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\" id=\"form1\" style=\"width: 100%;\">\n";
	echo add_token_field();

	echo "<p class='bold'>Fichier JPEG de signature/cachet de l'établissement.</p>\n";

	echo "<p>Vous pouvez mettre en place un fichier de signature/cachet et choisir quels statuts ou utilisateurs pourront insérer le fichier dans un document Gepi (<em>actuellement, seuls les bulletins PDF sont concernés</em>).</p>\n";

	if(($nom_fichier_signature!="")&&(file_exists($dirname."/".$nom_fichier_signature))) {
		echo "<br />\n";

		// Si un .htaccess est en place dans backup, on n'atteind pas l'image sans fournir compte/mdp
		$url_fich_sign="../temp/".get_user_temp_directory()."/".md5(microtime()).$nom_fichier_signature;
		copy($dirname."/".$nom_fichier_signature, $url_fich_sign);
		// La copie sera supprimée à la déconnexion

		//$info_image=redim_img($dirname."/".$nom_fichier_signature, 200, 200);
		//echo "<p>Un fichier de signature est actuellement en place&nbsp;:<br /><img src='".$dirname."/".$nom_fichier_signature."' width='".$info_image[0]."' height='".$info_image[1]."' /></p>\n";
		$info_image=redim_img($url_fich_sign, 200, 200);
		echo "<p>Un fichier de signature est actuellement en place&nbsp;:<br /><img src='".$url_fich_sign."' width='".$info_image[0]."' height='".$info_image[1]."' /></p>\n";

		echo "<p class='bold'>Comptes et statuts autorisés à insérer la signature/cachet&nbsp:</p>\n";
		if(mysql_num_rows($res)==0) {
			echo "<p style='margin-left: 3em;'>";
			echo "Aucun statut ni utilisateur n'est autorisé à insérer le fichier dans un document Gepi.";
		}
		else {
			$nb_statuts=0;
			$nb_comptes=0;
			while($lig=mysql_fetch_object($res)) {
				if($lig->type=='statut') {
					if($nb_statuts==0) {
						echo "<p style='margin-left: 3em;'>";
						echo "<strong>Statut(s)&nbsp;:</strong> ";
					}
					else {
						echo ", ";
					}
					$nb_statuts++;
				}

				if($lig->type=='individu') {
					if($nb_comptes==0) {
						echo "<p style='margin-left: 3em;'>";
						echo "<strong>Compte(s)&nbsp;:</strong> ";
					}
					else {
						echo ", ";
					}
					$nb_comptes++;
				}

				echo $lig->identite;
			}
		}
		echo "</p>\n";
		echo "<br />\n";
	}

	echo "<p><strong>Mettre en place un fichier de signature&nbsp;:</strong><br />\n";
	echo "Fichier&nbsp;: <input type=\"file\" name=\"sign_file\" onchange='changement()' />\n";
	echo "<input type=\"submit\" name=\"valid_sign\" value=\"Enregistrer\" /></p>\n";

	if(($nom_fichier_signature!="")&&(file_exists($dirname."/".$nom_fichier_signature))) {
		echo "<p>ou supprimer le fichier&nbsp;: <input type=\"submit\" name=\"suppr_sign\" value=\"Supprimer le fichier signature\" />\n";
	}
	echo "</form>\n";

	//echo "<hr />\n";
	//echo "<p><a href='".$_SERVER['PHP_SELF']."?choix_acces=y'>Choisir les comptes ou statuts autorisés</a></p>\n";
}
else {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir une image</a>";
	echo "</p>\n";

	echo "<p class='bold'>Choix des comptes autorisés à utiliser la signature.</p>\n";

	$tab_droits=array();
	$tab_droits['statut']=array();
	$tab_droits['individu']=array();
	$sql="SELECT * FROM droits_acces_fichiers WHERE fichier='signature_img';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		while($lig=mysql_fetch_object($res)) {
			$tab_droits[$lig->type][]=$lig->identite;
		}
	}

	echo "<form enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\" id=\"form1\" style=\"width: 100%;\">\n";
	echo add_token_field();
	echo "<div style='float:left; width: 200px;'>\n";
	echo "<p class='bold'>Statuts</p>\n";
	$tab_statuts=array('administrateur', 'scolarite', 'cpe', 'professeur');
	for($loop=0;$loop<count($tab_statuts);$loop++) {
		echo "<input type='checkbox' name='statut_autorise[]' id='statut_autorise_$loop' value='".$tab_statuts[$loop]."' ";
		if(in_array($tab_statuts[$loop], $tab_droits['statut'])) {echo "checked ";}
		echo "/><label for='statut_autorise_$loop'>".$tab_statuts[$loop]."</label><br />\n";
	}

	echo "<input type=\"submit\" name=\"valid_choix_acces_sign\" value=\"Enregistrer\" /></p>\n";

	echo "</div>\n";

	$cpt=0;
	for($loop=0;$loop<count($tab_statuts);$loop++) {
		$sql="SELECT login, nom, prenom, civilite FROM utilisateurs WHERE statut='".$tab_statuts[$loop]."' AND etat='actif' ORDER BY nom, prenom;";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {
			echo "<div style='float:left; width: 200px;'>\n";
			echo "<p class='bold'>".casse_mot($tab_statuts[$loop], 'majf2')."</p>\n";
			while($lig=mysql_fetch_object($res)) {
				echo "<input type='checkbox' name='compte_autorise[]' id='compte_autorise_$cpt' value='".$lig->login."' ";
				if(in_array($lig->login, $tab_droits['individu'])) {echo "checked ";}
				echo "/><label for='compte_autorise_$cpt'>".casse_mot($lig->nom, 'maj')." ".casse_mot($lig->prenom, 'majf2')."</label><br />\n";
				$cpt++;
			}
			echo "</div>\n";
		}
	}
	echo "<input type=\"hidden\" name=\"choix_acces\" value=\"hidden\" />\n";
	//echo "<input type=\"submit\" name=\"valid_choix_acces_sign\" value=\"Enregistrer\" /></p>\n";
	echo "</form>\n";
}

echo "<div style='clear:both;'></div>\n";

echo "<p><em>NOTES&nbsp;:</em></p>\n";
echo "<ul>\n";
echo "<li><p>Le fichier mis en place n'est protégé contre un téléchargement abusif que si un .htaccess est en place dans le dossier de backup (<em>voir la rubrique <a href='accueil_sauve.php'>Sauvegarde et restauration</a></em>).";

if(($nom_fichier_signature!="")&&(file_exists($dirname."/".$nom_fichier_signature))) {
	echo "<br />";
	echo "Si le .htaccess est en place et pris en compte par le serveur, vous devriez vous voir réclamer un couple compte/mot de passe pour télécharger l'image&nbsp;: <a href='".$dirname."/".$nom_fichier_signature."' target='_blank'>Tester la protection</a>.<br />";
	echo "Si l'accès n'est pas protégé, toute personne connaissant le chemin (<em>aléatoire tout de même</em>) et le nom du fichier signature pourrait le récupérer.";
}
echo "</p></li>\n";

echo "<li><p>Le fichier mis en place peut se trouver après affichage dans une page web,... dans le cache de votre navigateur ou dans les fichiers temporaires du navigateur.<br />Pensez à effacer vos traces.</p></li>\n";

echo "<li><p>On peut aussi se demander quelle garantie d'authenticité apporte une image insérée.<br />Un document peut être scanné et l'image réinsérée ailleurs.<br />Conserver un tamponnage classique des bulletins peut être une bonne chose.</p></li>\n";
echo "</ul>\n";
require("../lib/footer.inc.php");
?>
