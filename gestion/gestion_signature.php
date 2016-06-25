<?php
/*
* Copyright 2001-2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
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
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$msg="";

$choix_acces=isset($_GET['choix_acces']) ? $_GET['choix_acces'] : NULL;
$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

if(isset($_POST['activation_desactivation'])) {
	check_token();

	if (isset($_POST['activer'])) {
		if (!saveSetting("active_fichiers_signature", $_POST['activer'])) {$msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation !";}
	}
}

//debug_var();

$sql="CREATE TABLE IF NOT EXISTS signature_droits (
id INT(11) unsigned NOT NULL auto_increment,
login VARCHAR( 255 ) NOT NULL ,
PRIMARY KEY ( id )
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

if(isset($_POST['enregistrer_choix_utilisateurs'])) {
	check_token();

	$login_user=isset($_POST['login_user']) ? $_POST['login_user'] : array();

	$tab_droits_existants=array();

	$sql="SELECT * FROM signature_droits;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			$tab_droits_existants[]=$lig->login;
		}
	}

	$nb_suppr=0;
	for($loop=0;$loop<count($tab_droits_existants);$loop++) {
		if(!in_array($tab_droits_existants[$loop], $login_user)) {
			$sql="DELETE FROM signature_droits WHERE login='".$tab_droits_existants[$loop]."';";
			$menage=mysqli_query($GLOBALS["mysqli"], $sql);
			if($menage) {
				$nb_suppr++;
			}
			else {
				$msg.="Erreur lors de la suppression du droit de signature pour ".civ_nom_prenom($tab_droits_existants[$loop])."<br />";
			}
		}
	}

	$nb_reg=0;
	for($loop=0;$loop<count($login_user);$loop++) {
		if(!in_array($login_user[$loop], $tab_droits_existants)) {
			$sql="INSERT INTO signature_droits SET login='".$login_user[$loop]."';";
			$insert=mysqli_query($GLOBALS["mysqli"], $sql);
			if($insert) {
				$nb_reg++;
			}
			else {
				$msg.="Erreur lors de l'enregistrement du droit de signature pour ".civ_nom_prenom($login_user[$loop])."<br />";
			}
		}
	}

	if($nb_suppr>0) {
		$msg.=$nb_suppr." droit(s) supprimé(s).<br />";
	}

	if($nb_reg>0) {
		$msg.=$nb_reg." droit(s) enregistré(s).<br />";
	}
}

$sql="CREATE TABLE IF NOT EXISTS signature_fichiers (
id_fichier INT(11) unsigned NOT NULL auto_increment,
fichier VARCHAR( 255 ) NOT NULL ,
login VARCHAR( 255 ) NOT NULL ,
type VARCHAR( 255 ) NOT NULL,
PRIMARY KEY ( id_fichier )
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
//echo "$sql<br />";
$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

if (isset($_POST['ajout_fichier'])) {
	check_token();

	$sign_file = isset($_FILES["sign_file"]) ? $_FILES["sign_file"] : NULL;
	$login_user = isset($_POST["login_user"]) ? $_POST["login_user"] : NULL;

	if(!isset($sign_file)) {
		$msg.= "Aucun fichier n'a été fourni.<br />";
	}
	elseif((!isset($login_user))||($login_user=="")) {
		$msg.= "Aucun utilisateur n'a été choisi.<br />";
	}
	elseif((isset($sign_file['error']))&&($sign_file['error']==4)) {
		$msg.= "Aucun fichier n'a été proposé.<br />";
	}
	elseif((!preg_match("/\.jpeg$/i", $sign_file['name']))&&(!preg_match("/\.jpg$/i", $sign_file['name']))) {
		$msg.= "Seule l'extension JPG est autorisée.<br />";
	}
	else {
		if(!check_user_temp_directory($login_user, 1)) {
			$msg.= "Le dossier temporaire de ".civ_nom_prenom($login_user)." ne peut pas être créé ou n'est pas accessible en écriture.<br />";
		}
		else {
			$dirname=get_user_temp_directory($login_user);
			if((!$dirname)||($dirname=="")) {
				$msg.= "Le dossier temporaire de ".civ_nom_prenom($login_user)." n'existe pas ou n'est pas accessible en écriture.<br />";
			}
			else {
				$tmp_dim_img=getimagesize($sign_file['tmp_name']);
				if((isset($tmp_dim_img[2]))&&($tmp_dim_img[2]==2)) {
					$dirname="../temp/".$dirname."/signature";

					if(!file_exists($dirname)) {
						mkdir($dirname);
						if ($f = @fopen("$dirname/index.html", "w")) {
							@fputs($f, '<html><head><script type="text/javascript">
	document.location.replace("../../../login.php")
</script></head></html>');
							@fclose($f);
						}
					}

					if(!file_exists($dirname)) {
						$msg.= "Il n'a pas été possible de créer un dossier 'signature' dans votre dossier temporaire.<br />";
					}
					else {
						$ok = false;
						if ($f = @fopen("$dirname/.test", "w")) {
							@fputs($f, '<'.'?php $ok = true; ?'.'>');
							@fclose($f);
							include("$dirname/.test");
						}

						//$msg.=$dirname."<br />";

						if (!$ok) {
							$msg.= "Problème d'écriture sur le répertoire temporaire de ".civ_nom_prenom($login_user).".<br />Veuillez signaler ce problème à l'administrateur du site.<br />";
						} else {
							if (file_exists($dirname."/".$sign_file['name'])) {
								@unlink($dirname."/".$sign_file['name']);
								$sql="DELETE FROM signature_fichiers WHERE fichier='".mysqli_real_escape_string($GLOBALS["mysqli"], $sign_file['name'])."' AND login='$login_user';";
								$menage=mysqli_query($GLOBALS["mysqli"], $sql);
								$msg.= "Un fichier de même nom existait pour cet utilisateur.<br />Le fichier précédent a été supprimé.<br />";
							}
							$ok = @copy($sign_file['tmp_name'], $dirname."/".$sign_file['name']);
							if (!$ok) {$ok = @move_uploaded_file($sign_file['tmp_name'], $dirname."/".$sign_file['name']);}
							if (!$ok) {
								$msg.= "Problème de transfert : le fichier n'a pas pu être transféré dans le répertoire temporaire de ".civ_nom_prenom($login_user).".<br />Veuillez signaler ce problème à l'administrateur du site<br />.";
							}
							else {
								$msg.= "Le fichier a été transféré.<br />";

								// Par précaution, pour éviter des blagues avec des scories...
								$sql="DELETE FROM signature_fichiers WHERE fichier='".mysqli_real_escape_string($GLOBALS["mysqli"], $sign_file['name'])."' AND login='$login_user';";
								$menage=mysqli_query($GLOBALS["mysqli"], $sql);

								$sql="INSERT INTO signature_fichiers SET login='$login_user', fichier='".mysqli_real_escape_string($GLOBALS["mysqli"], $sign_file['name'])."';";
								$insert=mysqli_query($GLOBALS["mysqli"], $sql);
								if (!$insert) {
									$msg.="Erreur lors de l'enregistrement dans la table 'signature_fichiers'.<br />";
								}
							}
						}
					}
				}
				else {
					$msg.= "Le type de l'image est incorrect.<br />";
				}
			}
		}
	}
}

if (isset($_POST['suppr_fichier'])) {
	check_token();

	$suppr = isset($_POST["suppr"]) ? $_POST["suppr"] : array();

	if(count($suppr)==0) {
		$msg.= "Aucun fichier à supprimer n'a été coché.<br />";
	}
	else {
		$cpt_suppr=0;
		$cpt_fich_suppr=0;
		for($loop=0;$loop<count($suppr);$loop++) {
			$sql="SELECT * FROM signature_fichiers WHERE id_fichier='".$suppr[$loop]."';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				$lig=mysqli_fetch_object($res);

				$dirname=get_user_temp_directory($lig->login);
				$fichier_courant="../temp/".$dirname."/signature/".$lig->fichier;
				if(($dirname)&&($dirname!="")&&(file_exists($fichier_courant))) {
					$menage=unlink($fichier_courant);
					if(!$menage) {
						$msg.="Erreur lors de la suppression du fichier $fichier_courant<br />";
					}
					else {
						$cpt_fich_suppr++;
					}
				}

				$sql="SELECT * FROM signature_classes WHERE id_fichier='".$suppr[$loop]."';";
				$res_sc=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_sc)>0) {
					while($lig_sc=mysqli_fetch_object($res_sc)) {
						$sql="UPDATE signature_classes WHERE SET id_fichier='-1' WHERE login='".$_SESSION['login']."' AND id_classe='".$lig_sc->id_classe."';";
						$menage2=mysqli_query($GLOBALS["mysqli"], $sql);
					}
				}

				$sql="DELETE FROM signature_fichiers WHERE id_fichier='".$suppr[$loop]."';";
				$menage=mysqli_query($GLOBALS["mysqli"], $sql);
				if($menage) {
					$cpt_suppr++;
					/*
					$sql="DELETE FROM signature_classes WHERE id_fichier='".$suppr[$loop]."';";
					$menage2=mysql_query($sql);
					*/
				}
				else {
					$msg.="Erreur lors de la suppression de l'enregistrement concernant $fichier_courant<br />";
				}
			}
		}
		if($cpt_suppr>0) {
			$msg.="$cpt_suppr enregistrement(s) supprimé(s).<br />";
		}
		if($cpt_fich_suppr>0) {
			$msg.="$cpt_fich_suppr fichier(s) supprimé(s).<br />";
		}
	}
}

$sql="CREATE TABLE IF NOT EXISTS signature_classes (
id INT(11) unsigned NOT NULL auto_increment,
login VARCHAR( 255 ) NOT NULL ,
id_classe INT( 11 ) NOT NULL ,
id_fichier INT( 11 ) NOT NULL ,
PRIMARY KEY ( id )
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

if (isset($_POST['enregistrer_choix_assoc_fichier_user_classe'])) {
	check_token();

	$droit = isset($_POST["droit"]) ? $_POST["droit"] : array();
	$id_fichier = isset($_POST["id_fichier"]) ? $_POST["id_fichier"] : array();

	$cpt_droits_suppr=0;
	$droits_anterieurs=array();
	$sql="SELECT * FROM signature_classes ORDER BY login, id_classe;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			$droits_anterieurs[$lig->id]=$lig->login."|".$lig->id_classe;

			if(!in_array($lig->login."|".$lig->id_classe, $droit)) {
				$sql="DELETE FROM signature_classes WHERE id='$lig->id';";
				$menage=mysqli_query($GLOBALS["mysqli"], $sql);
				$cpt_droits_suppr++;
			}
			else {
				// Il y a peut-être un update à faire.
			}
		}
	}

	$cpt_nouveaux_droits=0;
	$cpt_modif_fichiers=0;
	//for($loop=0;$loop<count($droit);$loop++) {
		//$tab=explode("|", $droit[$loop]);
	foreach($droit as $key => $value) {
		$tab=explode("|", $value);

		$sql="SELECT * FROM signature_classes WHERE login='".$tab[0]."' AND id_classe='".$tab[1]."';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			// Il y a peut-être un update à faire.
			$lig=mysqli_fetch_object($res);
			$id_fichier_actuel=$lig->id_fichier;

			if(!isset($id_fichier[$key])) {
				$id_fichier_a_mettre=-1;
			}
			elseif((isset($id_fichier[$key]))&&($id_fichier[$key]=="")) {
				$id_fichier_a_mettre=-1;
			}
			else {
				$id_fichier_a_mettre=$id_fichier[$key];
			}

			if($id_fichier_a_mettre!=$id_fichier_actuel) {
				$sql="UPDATE signature_classes SET id_fichier='".$id_fichier_a_mettre."' WHERE login='".$tab[0]."' AND id_classe='".$tab[1]."';";
				$update=mysqli_query($GLOBALS["mysqli"], $sql);
				if($update) {
					$cpt_modif_fichiers++;
				}
			}
		}
		else {
			// On insère un nouveau droit.
			$id_fichier_courant=-1;
			if((isset($id_fichier[$key]))&&($id_fichier[$key]!="")) {
				$id_fichier_courant=$id_fichier[$key];
			}

			$sql="INSERT INTO signature_classes SET login='".$tab[0]."', id_classe='".$tab[1]."', id_fichier='".$id_fichier_courant."';";
			$insert=mysqli_query($GLOBALS["mysqli"], $sql);
			if($insert) {
				$cpt_nouveaux_droits++;
			}
		}
	}


	if($cpt_nouveaux_droits>0) {
		$msg.="$cpt_nouveaux_droits nouvelle(s) autorisation(s) donnée(s).<br />";
	}
	if($cpt_modif_fichiers>0) {
		$msg.="$cpt_modif_fichiers association(s) fichier(s) effectuée(s).<br />";
	}
	if($cpt_droits_suppr>0) {
		$msg.="$cpt_droits_suppr autorisation(s) supprimée(s).<br />";
	}
}

/*
> Pour ma part, je pense qu'on répondra au besoin en prévoyant d'associer 
> un fichier image (signature) à chaque signataire (champ "Prénom et nom 
> du signataire des bulletins...") prévu en page 
> classes/modify_nom_class.php?id_classe=3 (et bien évidemment aussi en 
> paramétrage par lot)
> 
> Il faut ensuite ne laisser l'activation de cette fonctionnalité qu'à 
> l'admin, avec moult alertes rouges sur le danger induit, et prévoir 
> comme tu le suggères que seuls des logins bien identifiés par l'admin et 
> appartenant à un profil identifié lui aussi impriment les bulletins et 
> autres documents avec la signature (les autres utilisateurs ayant accès 
> à l’impression n'obtenant que des docs classiques, sans la signature)
> 
> donc :
> 1- activation de la fonctionnalité / admin
> 2- association (ou pas) d'une signature à chaque signataire/classe
> 3- définition par l'admin du profil (scolarité par défaut) pouvant 
> accéder à cette fonctionnalité
> 4- définition par l'admin des utilisateurs (appartenant au(x) profil(s) 
> choisi(s)) imprimant avec la signature
*/

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *********************
$titre_page = "Signature";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

echo "<p class='bold'><a href=\"../bulletin/index_admin.php\" title=\"Retour à la Gestion du module Bulletins\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";

//==============================================================================
if(!isset($mode)) {
	echo "</p>

<p style='margin-top:1em;margin-bottom:1em;'>La <strong>possibilité</strong> d'utiliser des fichiers de signature est actuellement <strong>".((getSettingValue('active_fichiers_signature')!='y') ? "in" : "")."active</strong>.</p>

<p class='bold'>Choisissez&nbsp;:</p>

<form enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\" id=\"form1\" style=\"width: 100%;\">

	<fieldset id='activation' style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\");'>
		<legend style='border: 1px solid grey; background-color: white;'>Activation</legend>

		<input type='radio' 
				name='activer' 
				id='activer_y' 
				value='y' 
				".((getSettingValue('active_fichiers_signature')=='y') ? " checked='checked'" : "")."
				onchange='changement();' />
		<label for='activer_y' style='cursor: pointer;'>
			Activer la possibilité d'utiliser des fichiers de signature
		</label>

		<br />

		<input type='radio' 
				name='activer' 
				id='activer_n' 
				value='n' 
				".((getSettingValue('active_fichiers_signature')!='y') ? " checked='checked'" : "")."
				onchange='changement();' />
		<label for='activer_n' style='cursor: pointer;'>
			Désactiver la possibilité d'utiliser des fichiers de signature
		</label>

		".add_token_field()."

		<input type='hidden' name='activation_desactivation' value='y' />
		<input type='submit' value='Valider' />

	</fieldset>
</form>

<p style='margin-top:1em;'>Ou</p>

<ol>
	<li><a href='".$_SERVER['PHP_SELF']."?mode=choix_utilisateurs'>Choisir les personnes autorisées à mettre en place et utiliser un fichier de signature pour telle ou telle classe</a></li>
	<li><a href='".$_SERVER['PHP_SELF']."?mode=choix_fichier'>Associer des fichiers de signature à tel ou tel compte</a><br />
	Vous pouvez cependant laisser les utilisateurs choisis à l'étape précédente téléverser eux-même leurs fichiers de signature.</li>
	<li><a href='".$_SERVER['PHP_SELF']."?mode=choix_assoc_fichier_user_classe'>Définir les associations classe/utilisateur<br />
	et éventuellement associer des fichiers de signature aux classes pour tel ou tel compte</a><br />
	Cette étape est indispensable ne serait-ce que pour indiquer pour quelle(s) classe(s) tel utilisateur peut utiliser un fichier de signature (<em>même si vous laissez l'utilisateur téléverser lui-même son fichier</em>).</li>
</ol>

<p style='margin-top:1em;'><em>NOTES&nbsp;:</em></p>
<ul>
	<li>
		<p>Les fichiers mis en place ne sont pas protégés contre un téléchargement abusif.<br />
		Toute personne connaissant le chemin (<em>aléatoire tout de même</em>) et le nom du fichier signature pourrait le récupérer.</p>
	</li>
	<li>
		<p>Le chemin d'un fichier mis en place peut se trouver après affichage dans une page web,... dans le cache de votre navigateur ou dans les fichiers temporaires du navigateur.<br />
		Pensez à effacer vos traces.</p>
	</li>
	<li>
		<p>On peut aussi se demander quelle garantie d'authenticité apporte une image insérée.<br />
		Un document peut être scanné et l'image réinsérée ailleurs.<br />
		Conserver un tamponnage classique des bulletins peut être une bonne chose.</p>
	</li>
</ul>\n";


	require("../lib/footer.inc.php");
	die();
}
//==============================================================================
if($mode=='choix_utilisateurs') {

	$tab_user_preselectionnes=array();
	$sql="SELECT * FROM signature_droits;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			$tab_user_preselectionnes[]=$lig->login;
		}
	}

	echo " | <a href='".$_SERVER['PHP_SELF']."'>Index signature</a></p>

<form enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\" id=\"form1\" style=\"width: 100%;\">

	<fieldset id='choix_user' style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\");'>
		<legend style='border: 1px solid grey; background-color: white;'>Choix des utilisateurs</legend>

		<p class='bold' style='margin-top:1em;margin-bottom:1em;'>Choisissez les utilisateurs autorisés à utiliser des fichiers de signature.</p>

		".liste_checkbox_utilisateurs(array('administrateur', 'scolarite', 'cpe', 'professeur'), $tab_user_preselectionnes)."

		".add_token_field()."

		<input type='hidden' name='mode' value='choix_utilisateurs' />
		<input type='hidden' name='enregistrer_choix_utilisateurs' value='y' />
		<input type='submit' value='Valider' />

	</fieldset>
</form>\n";


	require("../lib/footer.inc.php");
	die();
}
//==============================================================================
if($mode=='choix_fichier') {
	// Tableau des chemins user_temp_directory
	$tab_user_temp_dir=array();

	// Tableau des utilisateurs autorisés à utiliser un fichier de signature
	$cpt=0;
	$tab_user=array();
	$sql="SELECT * FROM signature_droits;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			$tab_user[$cpt]['login']=$lig->login;
			$tab_user[$cpt]['designation']=civ_nom_prenom($lig->login);
			$cpt++;
		}
	}
	else {
		echo " | <a href='".$_SERVER['PHP_SELF']."'>Index signature</a></p>

<p>Aucun utilisateur n'est actuellement autorisé à utiliser un fichier de signature.</p>";

		require("../lib/footer.inc.php");
		die();
	}

	/*
	$sql="CREATE TABLE IF NOT EXISTS signature_fichiers (
	id_fichier INT(11) unsigned NOT NULL auto_increment,
	fichier VARCHAR( 255 ) NOT NULL ,
	login VARCHAR( 255 ) NOT NULL ,
	type VARCHAR( 255 ) NOT NULL,
	PRIMARY KEY ( id )
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
	*/

	$cpt=0;
	$login_prec="";
	$tab_fichiers_utilisateurs=array();
	$sql="SELECT * FROM signature_fichiers ORDER BY login;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			// Vérifier si le user_temp_directory peut changer?
			if(!array_key_exists($lig->login, $tab_user_temp_dir)) {
				$tab_user_temp_dir[$lig->login]=get_user_temp_directory($lig->login);
			}

			if($lig->login!=$login_prec) {
				$cpt=0;
				$login_prec=$lig->login;
			}

			$tab_fichiers_utilisateurs[$lig->login][$cpt]['id_fichier']=$lig->id_fichier;
			$tab_fichiers_utilisateurs[$lig->login][$cpt]['fichier']=$lig->fichier;
			$tab_fichiers_utilisateurs[$lig->login][$cpt]['chemin']="../temp/".$tab_user_temp_dir[$lig->login]."/signature/".$lig->fichier;
			$cpt++;
		}
	}

	echo " | <a href='".$_SERVER['PHP_SELF']."'>Index signature</a></p>

<form enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\" id=\"form1\" style=\"width: 100%;\">

	<fieldset id='ajout_fichier' style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\");'>
		<legend style='border: 1px solid grey; background-color: white;'>Ajouter un fichier</legend>
		".add_token_field()."

		<p class='bold'>Ajouter le fichier&nbsp;: <input type=\"file\" name=\"sign_file\" onchange='changement()' /><br />
		pour&nbsp;: 
			<select name='login_user' onchange='changement()'>
				<option value=''>--- Choisissez un utilisateur ---</option>";

	for($i=0;$i<count($tab_user);$i++) {
		echo "
				<option value='".$tab_user[$i]['login']."' title=\"".$tab_user[$i]['login']."\"";
		if((isset($login_user))&&($tab_user[$i]['login']==$login_user)) {echo " selected='selected'";}
		echo ">".$tab_user[$i]['designation']."</option>";
	}

	echo "
			</select>

		<input type='hidden' name='mode' value='choix_fichier' />
		<input type='hidden' name='ajout_fichier' value='y' />
		<input type='submit' value='Valider' />
		</p>

		<p style='margin-top:1em'><em>NOTE&nbsp;:</em> Seuls les fichiers JPEG sont autorisés.</p>

	</fieldset>
</form>

<br />

<form enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\" id=\"form1\" style=\"width: 100%;\">

	<fieldset id='fichiers_des_utilisateurs' style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\");'>
		<legend style='border: 1px solid grey; background-color: white;'>Fichiers des utilisateurs</legend>

		<p class='bold'>Liste des fichiers associés aux utilisateurs&nbsp;:</p>
		<ul>";

	$cpt=0;
	for($i=0;$i<count($tab_user);$i++) {
		echo "
			<li>
				<p><span class='bold' title=\"".$tab_user[$i]['login']."\">".$tab_user[$i]['designation']."&nbsp;:</span> ";

		//if(count($tab_fichiers_utilisateurs[$tab_user[$i]['login']])>0) {
		if(isset($tab_fichiers_utilisateurs[$tab_user[$i]['login']])) {
			echo "
				<ul>";
			for($j=0;$j<count($tab_fichiers_utilisateurs[$tab_user[$i]['login']]);$j++) {
				if(file_exists($tab_fichiers_utilisateurs[$tab_user[$i]['login']][$j]['chemin'])) {
					$texte="<center><img src='".$tab_fichiers_utilisateurs[$tab_user[$i]['login']][$j]['chemin']."' width='200' /></center>";
					$tabdiv_infobulle[]=creer_div_infobulle('fichier_'.$cpt,"Fichier de signature","",$texte,"",14,0,'y','y','n','n');

					echo "
					<li title=\"Cochez la case pour supprimer ce fichier\"><input type='checkbox' name='suppr[]' id='suppr_$cpt' value='".$tab_fichiers_utilisateurs[$tab_user[$i]['login']][$j]['id_fichier']."' onchange='changement()' /><label for='suppr_$cpt' onmouseover=\"delais_afficher_div('fichier_$cpt','y',-100,20,1000,20,20);\"> ".$tab_fichiers_utilisateurs[$tab_user[$i]['login']][$j]['fichier']."</label></li>";
				}
				else {
					echo "
					<li title=\"Cochez la case pour supprimer ce fichier\"><input type='checkbox' name='suppr[]' id='suppr_$cpt' value='".$tab_fichiers_utilisateurs[$tab_user[$i]['login']][$j]['id_fichier']."' onchange='changement()' /><label for='suppr_$cpt'> ".$tab_fichiers_utilisateurs[$tab_user[$i]['login']][$j]['fichier']." <span style='color:red'>ANOMALIE : Le fichier semble absent&nbsp;???</span></label></li>";
				}
				$cpt++;
			}
			echo "
				</ul>";
		}
		else {
			echo "Aucun fichier n'est encore associé.</p>";
		}

		echo "
			</li>";
	}

	echo "
		</ul>
		".add_token_field()."

		<input type='hidden' name='mode' value='choix_fichier' />
		<input type='hidden' name='suppr_fichier' value='y' />
		<input type='submit' value='Supprimer les fichiers cochés' />

	</fieldset>
</form>\n";

	require("../lib/footer.inc.php");
	die();
}
//==============================================================================
if($mode=='choix_assoc_fichier_user_classe') {
	// Tableau des chemins user_temp_directory
	$tab_user_temp_dir=array();

	// Tableau des utilisateurs autorisés à utiliser un fichier de signature
	$cpt=0;
	$tab_user=array();
	$sql="SELECT * FROM signature_droits;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			$tab_user[$cpt]['login']=$lig->login;
			$tab_user[$cpt]['designation']=civ_nom_prenom($lig->login);
			$cpt++;
		}
	}
	else {
		echo " | <a href='".$_SERVER['PHP_SELF']."'>Index signature</a></p>

<p>Aucun utilisateur n'est actuellement autorisé à utiliser un fichier de signature.</p>";

		require("../lib/footer.inc.php");
		die();
	}

	$cpt=0;
	$login_prec="";
	$tab_fichiers=array();
	$tab_fichiers_utilisateurs=array();
	$sql="SELECT * FROM signature_fichiers ORDER BY login;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			// Vérifier si le user_temp_directory peut changer?
			if(!array_key_exists($lig->login, $tab_user_temp_dir)) {
				$tab_user_temp_dir[$lig->login]=get_user_temp_directory($lig->login);
			}

			if($lig->login!=$login_prec) {
				$cpt=0;
				$login_prec=$lig->login;
			}

			$tab_fichiers_utilisateurs[$lig->login][$cpt]['id_fichier']=$lig->id_fichier;
			$tab_fichiers_utilisateurs[$lig->login][$cpt]['fichier']=$lig->fichier;
			$tab_fichiers_utilisateurs[$lig->login][$cpt]['chemin']="../temp/".$tab_user_temp_dir[$lig->login]."/".$lig->fichier;

			$tab_fichiers[$lig->id_fichier]="../temp/".$tab_user_temp_dir[$lig->login]."/signature/".$lig->fichier;

			$cpt++;
		}
	}

	/*
	$sql="CREATE TABLE IF NOT EXISTS signature_classes (
	id INT(11) unsigned NOT NULL auto_increment,
	login VARCHAR( 255 ) NOT NULL ,
	id_classe INT( 11 ) NOT NULL ,
	id_fichier INT( 11 ) NOT NULL ,
	PRIMARY KEY ( id )
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$create_table=mysql_query($sql);
	*/

	$tab_fichiers_classes_utilisateurs=array();
	$tab_fichiers_utilisateurs_classes=array();
	$sql="SELECT * FROM signature_classes ORDER BY login, id_classe;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			$tab_fichiers_classes_utilisateurs[$lig->id_classe][$lig->login]=$lig->id_fichier;
			$tab_fichiers_utilisateurs_classes[$lig->login][$lig->id_classe]=$lig->id_fichier;
		}
	}

	/*
	echo "<pre>";
	print_r();
	echo "</pre>";
	*/

	$tab_classe=array();
	$sql="SELECT * FROM classes c ORDER BY c.classe, c.nom_complet;";
	$res_classe=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_classe)>0) {
		$cpt=0;
		while($lig_classe=mysqli_fetch_object($res_classe)) {
			$tab_classe[$cpt]['id_classe']=$lig_classe->id;
			$tab_classe[$cpt]['classe']=$lig_classe->classe;
			$cpt++;
		}
	}

	echo " | <a href='".$_SERVER['PHP_SELF']."'>Index signature</a></p>

<form enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\" id=\"form1\" style=\"width: 100%;\">

	<fieldset id='choix_fichier' style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\");'>
		<legend style='border: 1px solid grey; background-color: white;'>Fichiers par classe et utilisateur</legend>

		<p class='bold' style='margin-top:1em;margin-bottom:1em;'>Choisissez quels utilisateurs sont autorisés à téléverser des fichiers pour quelles classes<br />
		et choisissez vous-même, si vous le souhaitez, les fichiers à associer à tel utilisateur <span title=\"Chaque utilisateur peut de son côté téléverser des fichiers pour les classes pour lesquelles vous aurez donné l'autorisation.\">(*)</span> et telle classe.</p>

		<table class='boireaus boireaus_alt' summary='Tableau des associations classes/utilisateurs/fichiers de signature'>
			<tr>
				<th></th>";

	for($loop=0;$loop<count($tab_user);$loop++) {
		echo "
				<th>".$tab_user[$loop]['designation']."</th>";
	}

	echo "
			</tr>";

	$cpt=0;
	for($loop_c=0;$loop_c<count($tab_classe);$loop_c++) {
		echo "
			<tr>
				<td>".$tab_classe[$loop_c]['classe']."</td>";
		for($loop=0;$loop<count($tab_user);$loop++) {
			echo "
				<td>
					<input type='checkbox' name='droit[$cpt]' id='droit_$cpt' value='".$tab_user[$loop]['login']."|".$tab_classe[$loop_c]['id_classe']."' ";
			if(isset($tab_fichiers_classes_utilisateurs[$tab_classe[$loop_c]['id_classe']][$tab_user[$loop]['login']])) {
				echo "checked ";
			}
			echo "onchange='changement()' />";

			/*
			if((isset($tab_fichiers_classes_utilisateurs[$tab_classe[$loop_c]['id_classe']][$tab_user[$loop]['login']]))&&
			(isset($tab_fichiers_utilisateurs[$tab_user[$loop]['login']]))&&
			(in_array($tab_fichiers_classes_utilisateurs[$tab_classe[$loop_c]['id_classe']][$tab_user[$loop]['login']],$tab_fichiers_utilisateurs[$tab_user[$loop]['login']][$i]['id_fichier']))) {
			*/
			if((isset($tab_fichiers_classes_utilisateurs[$tab_classe[$loop_c]['id_classe']][$tab_user[$loop]['login']]))&&
			(isset($tab_fichiers_utilisateurs[$tab_user[$loop]['login']]))&&
			(array_key_exists($tab_fichiers_classes_utilisateurs[$tab_classe[$loop_c]['id_classe']][$tab_user[$loop]['login']],$tab_fichiers))) {
				echo "<img src='".$tab_fichiers[$tab_fichiers_classes_utilisateurs[$tab_classe[$loop_c]['id_classe']][$tab_user[$loop]['login']]]."' width='100' />";
			}

			if(isset($tab_fichiers_utilisateurs[$tab_user[$loop]['login']])) {
				echo "<br />
					<select name='id_fichier[$cpt]' id='id_fichier_$cpt' onchange='changement(); update_checkbox($cpt)'>
						<option value=''>---</option>";
				for($i=0;$i<count($tab_fichiers_utilisateurs[$tab_user[$loop]['login']]);$i++) {
					echo "
						<option value='".$tab_fichiers_utilisateurs[$tab_user[$loop]['login']][$i]['id_fichier']."'";
					if((isset($tab_fichiers_classes_utilisateurs[$tab_classe[$loop_c]['id_classe']][$tab_user[$loop]['login']]))&&($tab_fichiers_utilisateurs[$tab_user[$loop]['login']][$i]['id_fichier']==$tab_fichiers_classes_utilisateurs[$tab_classe[$loop_c]['id_classe']][$tab_user[$loop]['login']])) {
						echo " selected='selected'";
					}
					echo ">".$tab_fichiers_utilisateurs[$tab_user[$loop]['login']][$i]['fichier']."</option>";
				}
				echo "
					</select>";
			}
			echo "</td>";
			$cpt++;
		}
		echo "
			</tr>";
	}

	echo "
		</table>
		".add_token_field()."

		<input type='hidden' name='mode' value='choix_assoc_fichier_user_classe' />
		<input type='hidden' name='enregistrer_choix_assoc_fichier_user_classe' value='y' />
		<p align='center'><input type='submit' value='Valider' /></p>

		<script type='text/javascript'>
			function update_checkbox(i) {
				if((document.getElementById('id_fichier_'+i))&&
				(document.getElementById('droit_'+i))) {
					if(document.getElementById('id_fichier_'+i).selectedIndex!=0) {
						document.getElementById('droit_'+i).checked=true;
					}
				}
			}
		</script>

	</fieldset>
</form>\n";

	require("../lib/footer.inc.php");
	die();
}

/*
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
*/
?>
