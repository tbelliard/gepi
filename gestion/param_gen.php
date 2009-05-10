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
};
// Check access

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$msg = '';
if (isset($_POST['sup_logo'])) {
	$dest = '../images/';
	$ok = false;
	if ($f = @fopen("$dest/.test", "w")) {
		@fputs($f, '<'.'?php $ok = true; ?'.'>');
		@fclose($f);
		include("$dest/.test");
	}
	if (!$ok) {
		$msg = "Problème d'écriture sur le répertoire. Veuillez signaler ce problème à l'administrateur du site";
	} else {
		$old = getSettingValue("logo_etab");
		if (($old != '') and (file_exists($dest.$old))) unlink($dest.$old);
		$msg = "Le logo a été supprimé.";
		if (!saveSetting("logo_etab", '')) $msg .= "Erreur lors de l'enregistrement dans la table setting !";

	}

}
if (isset($_POST['valid_logo'])) {
	$doc_file = isset($_FILES["doc_file"]) ? $_FILES["doc_file"] : NULL;
	if (ereg("\.([^.]+)$", $doc_file['name'], $match)) {
		$ext = strtolower($match[1]);
		if ($ext!='jpg' and $ext!='png'and $ext!='gif') {
		//if ($ext!='jpg' and $ext!='jpeg' and $ext!='png'and $ext!='gif') {
			$msg = "les seules extensions autorisées sont gif, png et jpg";
		} else {
			$dest = '../images/';
			$ok = false;
			if ($f = @fopen("$dest/.test", "w")) {
				@fputs($f, '<'.'?php $ok = true; ?'.'>');
				@fclose($f);
				include("$dest/.test");
			}
			if (!$ok) {
				$msg = "Problème d'écriture sur le répertoire IMAGES. Veuillez signaler ce problème à l'administrateur du site";
			} else {
				$old = getSettingValue("logo_etab");
				if (file_exists($dest.$old)) @unlink($dest.$old);
				if (file_exists($dest.$doc_file)) @unlink($dest.$doc_file);
				$ok = @copy($doc_file['tmp_name'], $dest.$doc_file['name']);
				if (!$ok) $ok = @move_uploaded_file($doc_file['tmp_name'], $dest.$doc_file['name']);
				if (!$ok) {
					$msg = "Problème de transfert : le fichier n'a pas pu être transféré sur le répertoire IMAGES. Veuillez signaler ce problème à l'administrateur du site";
				} else {
					$msg = "Le fichier a été transféré.";
				}
				if (!saveSetting("logo_etab", $doc_file['name'])) {
				$msg .= "Erreur lors de l'enregistrement dans la table setting !";
				}

			}
		}
	} else {
		$msg = "Le fichier sélectionné n'est pas valide !";
	}
}
// Max session length
if (isset($_POST['sessionMaxLength'])) {
	if (!(ereg ("^[0-9]{1,}$", $_POST['sessionMaxLength'])) || $_POST['sessionMaxLength'] < 1) {
		$_POST['sessionMaxLength'] = 30;
	}
	if (!saveSetting("sessionMaxLength", $_POST['sessionMaxLength'])) {
		$msg .= "Erreur lors de l'enregistrement da durée max d'inactivité !";
	}
}
if (isset($_POST['gepiSchoolRne'])) {
	if (!saveSetting("gepiSchoolRne", $_POST['gepiSchoolRne'])) {
		$msg .= "Erreur lors de l'enregistrement du numéro RNE de l'établissement !";
	}
}
if (isset($_POST['gepiYear'])) {
	if (!saveSetting("gepiYear", $_POST['gepiYear'])) {
		$msg .= "Erreur lors de l'enregistrement de l'année scolaire !";
	}
}
if (isset($_POST['gepiSchoolName'])) {
	if (!saveSetting("gepiSchoolName", $_POST['gepiSchoolName'])) {
		$msg .= "Erreur lors de l'enregistrement du nom de l'établissement !";
	}
}
if (isset($_POST['gepiSchoolAdress1'])) {
	if (!saveSetting("gepiSchoolAdress1", $_POST['gepiSchoolAdress1'])) {
		$msg .= "Erreur lors de l'enregistrement de l'adresse !";
	}
}
if (isset($_POST['gepiSchoolAdress2'])) {
	if (!saveSetting("gepiSchoolAdress2", $_POST['gepiSchoolAdress2'])) {
		$msg .= "Erreur lors de l'enregistrement de l'adresse !";
	}
}
if (isset($_POST['gepiSchoolZipCode'])) {
	if (!saveSetting("gepiSchoolZipCode", $_POST['gepiSchoolZipCode'])) {
		$msg .= "Erreur lors de l'enregistrement du code postal !";
	}
}
if (isset($_POST['gepiSchoolCity'])) {
	if (!saveSetting("gepiSchoolCity", $_POST['gepiSchoolCity'])) {
		$msg .= "Erreur lors de l'enregistrement de la ville !";
	}
}
if (isset($_POST['gepiSchoolPays'])) {
	if (!saveSetting("gepiSchoolPays", $_POST['gepiSchoolPays'])) {
		$msg .= "Erreur lors de l'enregistrement du pays !";
	}
}
if (isset($_POST['gepiSchoolAcademie'])) {
	if (!saveSetting("gepiSchoolAcademie", $_POST['gepiSchoolAcademie'])) {
		$msg .= "Erreur lors de l'enregistrement de l'académie !";
	}
}
if (isset($_POST['gepiSchoolTel'])) {
	if (!saveSetting("gepiSchoolTel", $_POST['gepiSchoolTel'])) {
		$msg .= "Erreur lors de l'enregistrement du numéro de téléphone !";
	}
}
if (isset($_POST['gepiSchoolFax'])) {
	if (!saveSetting("gepiSchoolFax", $_POST['gepiSchoolFax'])) {
		$msg .= "Erreur lors de l'enregistrement du numéro de fax !";
	}
}
if (isset($_POST['gepiSchoolEmail'])) {
	if (!saveSetting("gepiSchoolEmail", $_POST['gepiSchoolEmail'])) {
		$msg .= "Erreur lors de l'adresse électronique !";
	}
}
if (isset($_POST['gepiAdminNom'])) {
	if (!saveSetting("gepiAdminNom", $_POST['gepiAdminNom'])) {
		$msg .= "Erreur lors de l'enregistrement du nom de l'administrateur !";
	}
}
if (isset($_POST['gepiAdminPrenom'])) {
	if (!saveSetting("gepiAdminPrenom", $_POST['gepiAdminPrenom'])) {
		$msg .= "Erreur lors de l'enregistrement du prénom de l'administrateur !";
	}
}
if (isset($_POST['gepiAdminFonction'])) {
	if (!saveSetting("gepiAdminFonction", $_POST['gepiAdminFonction'])) {
		$msg .= "Erreur lors de l'enregistrement de la fonction de l'administrateur !";
	}
}

if (isset($_POST['gepiAdminAdress'])) {
	if (!saveSetting("gepiAdminAdress", $_POST['gepiAdminAdress'])) {
		$msg .= "Erreur lors de l'enregistrement de l'adresse email !";
	}
}


if (isset($_POST['is_posted'])) {
	if ($_POST['is_posted']=='1') {
		if (isset($_POST['gepiAdminAdressPageLogin'])) {
			if (!saveSetting("gepiAdminAdressPageLogin", 'y')) {
				$msg .= "Erreur lors de l'enregistrement de l'affichage de adresse email sur la page de login !";
			}
		}
		else{
			if (!saveSetting("gepiAdminAdressPageLogin", 'n')) {
				$msg .= "Erreur lors de l'enregistrement du non-affichage de adresse email sur la page de login !";
			}
		}
	}
}

if (isset($_POST['is_posted'])) {
	if ($_POST['is_posted']=='1') {
		if (isset($_POST['gepiAdminAdressFormHidden'])) {
			if (!saveSetting("gepiAdminAdressFormHidden", 'n')) {
				$msg .= "Erreur lors de l'enregistrement de l'affichage de adresse email dans le formulaire [Contacter l'administrateur] !";
			}
		}
		else{
			if (!saveSetting("gepiAdminAdressFormHidden", 'y')) {
				$msg .= "Erreur lors de l'enregistrement du non-affichage de adresse email dans le formulaire [Contacter l'administrateur] !";
			}
		}
	}
}


if (isset($_POST['longmin_pwd'])) {
	if (!saveSetting("longmin_pwd", $_POST['longmin_pwd'])) {
		$msg .= "Erreur lors de l'enregistrement de la longueur minimale du mot de passe !";
	}
}

if (isset($_POST['mode_generation_pwd_majmin'])) {
	if (!saveSetting("mode_generation_pwd_majmin", $_POST['mode_generation_pwd_majmin'])) {
		$msg .= "Erreur lors de l'enregistrement du paramètre Min/Maj sur les mots de passe !";
	}
}

if (isset($_POST['is_posted'])) {
	if (isset($_POST['mode_generation_pwd_excl'])) {
		if (!saveSetting("mode_generation_pwd_excl", $_POST['mode_generation_pwd_excl'])) {
			$msg .= "Erreur lors de l'enregistrement du paramètre d'exclusion des caractères prêtant à confusion sur les mots de passe !";
		}
	}
	else{
		if (!saveSetting("mode_generation_pwd_excl", 'n')) {
			$msg .= "Erreur lors de l'enregistrement du paramètre d'exclusion des caractères prêtant à confusion sur les mots de passe !";
		}
	}

	//===============================================================
	// Traitement des problemes de points d'interrogation à la place des accents
	if (isset($_POST['mode_utf8_bulletins_pdf'])) {
		if (!saveSetting("mode_utf8_bulletins_pdf", $_POST['mode_utf8_bulletins_pdf'])) {
			$msg .= "Erreur lors de l'enregistrement du paramètre mode_utf8_bulletins_pdf !";
		}
	}
	else{
		if (!saveSetting("mode_utf8_bulletins_pdf", 'n')) {
			$msg .= "Erreur lors de l'enregistrement du paramètre mode_utf8_bulletins_pdf !";
		}
	}
	/*
	if (isset($_POST['mode_utf8_listes_pdf'])) {
		if (!saveSetting("mode_utf8_listes_pdf", $_POST['mode_utf8_listes_pdf'])) {
			$msg .= "Erreur lors de l'enregistrement du paramètre mode_utf8_listes_pdf !";
		}
	}
	else{
		if (!saveSetting("mode_utf8_listes_pdf", 'n')) {
			$msg .= "Erreur lors de l'enregistrement du paramètre mode_utf8_listes_pdf !";
		}
	}
	*/
	if (isset($_POST['mode_utf8_visu_notes_pdf'])) {
		if (!saveSetting("mode_utf8_visu_notes_pdf", $_POST['mode_utf8_visu_notes_pdf'])) {
			$msg .= "Erreur lors de l'enregistrement du paramètre mode_utf8_visu_notes_pdf !";
		}
	}
	else{
		if (!saveSetting("mode_utf8_visu_notes_pdf", 'n')) {
			$msg .= "Erreur lors de l'enregistrement du paramètre mode_utf8_visu_notes_pdf !";
		}
	}
	/*
	if (isset($_POST['mode_utf8_releves_pdf'])) {
		if (!saveSetting("mode_utf8_releves_pdf", $_POST['mode_utf8_releves_pdf'])) {
			$msg .= "Erreur lors de l'enregistrement du paramètre mode_utf8_releves_pdf !";
		}
	}
	else{
		if (!saveSetting("mode_utf8_releves_pdf", 'n')) {
			$msg .= "Erreur lors de l'enregistrement du paramètre mode_utf8_releves_pdf !";
		}
	}
	*/
	//===============================================================
}

// Dénomination du professeur de suivi
if (isset($_POST['gepi_prof_suivi'])) {
	if (!saveSetting("gepi_prof_suivi", $_POST['gepi_prof_suivi'])) {
		$msg .= "Erreur lors de l'enregistrement de gepi_prof_suivi !";
	}
}

// Dénomination des professeurs
if (isset($_POST['denomination_professeur'])) {
	if (!saveSetting("denomination_professeur", $_POST['denomination_professeur'])) {
		$msg .= "Erreur lors de l'enregistrement de denomination_professeur !";
	}
}
if (isset($_POST['denomination_professeurs'])) {
	if (!saveSetting("denomination_professeurs", $_POST['denomination_professeurs'])) {
		$msg .= "Erreur lors de l'enregistrement de denomination_professeurs !";
	}
}

// Dénomination des responsables légaux
if (isset($_POST['denomination_responsable'])) {
	if (!saveSetting("denomination_responsable", $_POST['denomination_responsable'])) {
		$msg .= "Erreur lors de l'enregistrement de denomination_responsable !";
	}
}
if (isset($_POST['denomination_responsables'])) {
	if (!saveSetting("denomination_responsables", $_POST['denomination_responsables'])) {
		$msg .= "Erreur lors de l'enregistrement de denomination_responsables !";
	}
}

// Dénomination des élèves
if (isset($_POST['denomination_eleve'])) {
	if (!saveSetting("denomination_eleve", $_POST['denomination_eleve'])) {
		$msg .= "Erreur lors de l'enregistrement de denomination_eleve !";
	}
}
if (isset($_POST['denomination_eleves'])) {
	if (!saveSetting("denomination_eleves", $_POST['denomination_eleves'])) {
		$msg .= "Erreur lors de l'enregistrement de denomination_eleves !";
	}
}
// Initialiser à 'Boite'
if (isset($_POST['gepi_denom_boite'])) {
	if (!saveSetting("gepi_denom_boite", $_POST['gepi_denom_boite'])) {
		$msg .= "Erreur lors de l'enregistrement de gepi_denom_boite !";
	}
}
if (isset($_POST['gepi_denom_boite_genre'])) {
	if (!saveSetting("gepi_denom_boite_genre", $_POST['gepi_denom_boite_genre'])) {
		$msg .= "Erreur lors de l'enregistrement de gepi_denom_boite_genre !";
	}
}

if (isset($_POST['gepi_stylesheet'])) {
	if (!saveSetting("gepi_stylesheet", $_POST['gepi_stylesheet'])) {
		$msg .= "Erreur lors de l'enregistrement de l'année scolaire !";
	}
}

if (isset($_POST['num_enregistrement_cnil'])) {
	if (!saveSetting("num_enregistrement_cnil", $_POST['num_enregistrement_cnil'])) {
		$msg .= "Erreur lors de l'enregistrement du numéro d'enregistrement à la CNIL !";
	}
}

if (isset($_POST['mode_generation_login'])) {
	if (!saveSetting("mode_generation_login", $_POST['mode_generation_login'])) {
		$msg .= "Erreur lors de l'enregistrement du mode de génération des logins !";
	}
	// On en profite pour mettre à jour la variable $longmax_login -> settings : longmax_login
			$nbre_carac = 12;
		if ($_POST['mode_generation_login'] == 'name8' OR $_POST['mode_generation_login'] == 'fname8' OR $_POST['mode_generation_login'] == 'namef8') {
			$nbre_carac = 8;
		}
		elseif ($_POST['mode_generation_login'] == 'fname19' OR $_POST['mode_generation_login'] == 'firstdotname19') {
			$nbre_carac = 19;
		}
		elseif ($_POST['mode_generation_login'] == 'firstdotname') {
			$nbre_carac = 30;
		}
		else {
			$nbre_carac = 12;
		}
	$req = "UPDATE setting SET value = '".$nbre_carac."' WHERE name = 'longmax_login'";
	$modif_maxlong = mysql_query($req);
}


if (isset($_POST['unzipped_max_filesize'])) {
	$unzipped_max_filesize=$_POST['unzipped_max_filesize'];
	if(substr($unzipped_max_filesize,0,1)=="-") {$unzipped_max_filesize=-1;}
	elseif(strlen(ereg_replace("[0-9]","",$unzipped_max_filesize))!=0) {
		$unzipped_max_filesize=10;
		$msg .= "Caractères invalides pour le paramètre unzipped_max_filesize<br />Initialisation à 10 Mo !";
	}

	if (!saveSetting("unzipped_max_filesize", $unzipped_max_filesize)) {
		$msg .= "Erreur lors de l'enregistrement du paramètre unzipped_max_filesize !";
	}
}


if (isset($_POST['gepi_pmv'])) {
	if (!saveSetting("gepi_pmv", $_POST['gepi_pmv'])) {
		$msg .= "Erreur lors de l'enregistrement de gepi_pmv !";
	}
}

if (isset($_POST['delais_apres_cloture'])) {
	$delais_apres_cloture=$_POST['delais_apres_cloture'];
	if (!(ereg ("^[0-9]{1,}$", $delais_apres_cloture)) || $delais_apres_cloture < 0) {
		//$delais_apres_cloture=0;
		$msg .= "Erreur lors de l'enregistrement de delais_apres_cloture !";
	}
	else {
		if (!saveSetting("delais_apres_cloture", $delais_apres_cloture)) {
			$msg .= "Erreur lors de l'enregistrement de delais_apres_cloture !";
		}
	}
}



/*
if(isset($_POST['is_posted'])){
	if (isset($_POST['export_cn_ods'])) {
		//if (!saveSetting("export_cn_ods", $_POST['export_cn_ods'])) {
		if (!saveSetting("export_cn_ods", 'y')) {
			$msg .= "Erreur lors de l'enregistrement de l'autorisation de l'export au format ODS !";
		}
	}
	else{
		if (!saveSetting("export_cn_ods", 'n')) {
			$msg .= "Erreur lors de l'enregistrement de l'interdiction de l'export au format ODS !";
		}
	}
}
*/

// Load settings
if (!loadSettings()) {
	die("Erreur chargement settings");
}
if (isset($_POST['is_posted']) and ($msg=='')) $msg = "Les modifications ont été enregistrées !";

// End standart header
$titre_page = "Paramètres généraux";
require_once("../lib/header.inc");
?>
<p class=bold><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>
<form action="param_gen.php" method="post" name="form1" style="width: 100%;">
<table style="width: 100%; border: 0;" cellpadding="5" cellspacing="5" summary='Paramètres'>
	<tr>
		<td style="width: 60%;font-variant: small-caps;">
		Année scolaire :
		</td>
		<td><input type="text" name="gepiYear" size="20" value="<?php echo(getSettingValue("gepiYear")); ?>" />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Numéro RNE de l'établissement :
		</td>
		<td><input type="text" name="gepiSchoolRne" size="8" value="<?php echo(getSettingValue("gepiSchoolRne")); ?>" />
		</td>
	</tr>

	<tr>
		<td style="font-variant: small-caps;">
		Nom de l'établissement :
		</td>
		<td><input type="text" name="gepiSchoolName" size="20" value="<?php echo(getSettingValue("gepiSchoolName")); ?>" />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Adresse de l'établissement :
		</td>
		<td><input type="text" name="gepiSchoolAdress1" size="40" value="<?php echo(getSettingValue("gepiSchoolAdress1")); ?>" /><br />
		<input type="text" name="gepiSchoolAdress2" size="40" value="<?php echo(getSettingValue("gepiSchoolAdress2")); ?>" />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Code postal :
		</td>
		<td><input type="text" name="gepiSchoolZipCode" size="20" value="<?php echo(getSettingValue("gepiSchoolZipCode")); ?>" />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Ville :
		</td>
		<td><input type="text" name="gepiSchoolCity" size="20" value="<?php echo(getSettingValue("gepiSchoolCity")); ?>" />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Pays :<br />
		(<span style='font-style:italic;font-size:x-small'>Le pays est utilisé pour comparer avec celui des responsables dans les blocs adresse des courriers adressés aux responsables</span>)
		</td>
		<td><input type="text" name="gepiSchoolPays" size="20" value="<?php echo(getSettingValue("gepiSchoolPays")); ?>" />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Académie :<br />
		(<span style='font-style:italic;font-size:x-small'>utilisé pour certains documents officiels</span>)
		</td>
		<td><input type="text" name="gepiSchoolAcademie" size="20" value="<?php echo(getSettingValue("gepiSchoolAcademie")); ?>" />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Téléphone établissement :
		</td>
		<td><input type="text" name="gepiSchoolTel" size="20" value="<?php echo(getSettingValue("gepiSchoolTel")); ?>" />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Fax établissement :
		</td>
		<td><input type="text" name="gepiSchoolFax" size="20" value="<?php echo(getSettingValue("gepiSchoolFax")); ?>" />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		E-mail établissement :
		</td>
		<td><input type="text" name="gepiSchoolEmail" size="20" value="<?php echo(getSettingValue("gepiSchoolEmail")); ?>" />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Nom de l'administrateur du site :
		</td>
		<td><input type="text" name="gepiAdminNom" size="20" value="<?php echo(getSettingValue("gepiAdminNom")); ?>" />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Prénom de l'administrateur du site :
		</td>
		<td><input type="text" name="gepiAdminPrenom" size="20" value="<?php echo(getSettingValue("gepiAdminPrenom")); ?>" />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Fonction de l'administrateur du site :
		</td>
		<td><input type="text" name="gepiAdminFonction" size="20" value="<?php echo(getSettingValue("gepiAdminFonction")); ?>" />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Email de l'administrateur du site :
		</td>
		<td><input type="text" name="gepiAdminAdress" size="20" value="<?php echo(getSettingValue("gepiAdminAdress")); ?>" />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		<label for='gepiAdminAdressPageLogin' style='cursor: pointer;'>Faire apparaitre le lien [Contacter l'administrateur] sur la page de login :</label>
		</td>
		<td>
		<input type="checkbox" id='gepiAdminAdressPageLogin' name="gepiAdminAdressPageLogin" value="y"
		<?php
			if(getSettingValue("gepiAdminAdressPageLogin")!='n'){echo " checked";}
		?>
		/>
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		<label for='gepiAdminAdressFormHidden' style='cursor: pointer;'>Faire apparaitre l'adresse de l'administrateur dans le formulaire [Contacter l'administrateur] :</label>
		</td>
		<td>
		<input type="checkbox" name="gepiAdminAdressFormHidden" id="gepiAdminAdressFormHidden" value="n"
		<?php
			if(getSettingValue("gepiAdminAdressFormHidden")!='y'){echo " checked";}
		?>
		/>
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Durée maximum d'inactivité : <br />
		<span class='small'>(Durée d'inactivité, en minutes, au bout de laquelle un utilisateur est automatiquement déconnecté de Gepi.) Attention, la variable session.maxlifetime dans le fichier php.ini est réglée à <?php echo(ini_get("session.gc_maxlifetime")); ?> secondes, soit un maximum de <?php echo(ini_get("session.gc_maxlifetime")/60); ?> minutes pour la session.</span>
		</td>
		<td><input type="text" name="sessionMaxLength" size="20" value="<?php echo(getSettingValue("sessionMaxLength")); ?>" />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Longueur minimale du mot de passe :</td>
		<td><input type="text" name="longmin_pwd" size="20" value="<?php echo(getSettingValue("longmin_pwd")); ?>" />
		</td>
	</tr>
        <?php if (isset($use_custom_denominations) && $use_custom_denominations) {
          ?>
	<tr>
		<td style="font-variant: small-caps;">
		Dénomination des professeurs :</td>
		<td>Sing. :<input type="text" name="denomination_professeur" size="20" value="<?php echo(getSettingValue("denomination_professeur")); ?>" />
		<br/>Pluriel :<input type="text" name="denomination_professeurs" size="20" value="<?php echo(getSettingValue("denomination_professeurs")); ?>" />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Dénomination des élèves :</td>
		<td>Sing. :<input type="text" name="denomination_eleve" size="20" value="<?php echo(getSettingValue("denomination_eleve")); ?>" />
		<br/>Pluriel :<input type="text" name="denomination_eleves" size="20" value="<?php echo(getSettingValue("denomination_eleves")); ?>" />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Dénomination des responsables légaux :</td>
		<td>Sing. :<input type="text" name="denomination_responsable" size="20" value="<?php echo(getSettingValue("denomination_responsable")); ?>" />
		<br/>Pluriel :<input type="text" name="denomination_responsables" size="20" value="<?php echo(getSettingValue("denomination_responsables")); ?>" />
		</td>
	</tr>
        <?php } ?>
	<tr>
		<td style="font-variant: small-caps;">
		Dénomination du professeur chargé du suivi des élèves :</td>
		<td><input type="text" name="gepi_prof_suivi" size="20" value="<?php echo(getSettingValue("gepi_prof_suivi")); ?>" />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;" valign='top'>
		Désignation des boites/conteneurs/emplacements/sous-matières :</td>
		<td>
		<input type="text" name="gepi_denom_boite" size="20" value="<?php echo(getSettingValue("gepi_denom_boite")); ?>" /><br />
		<table summary='Genre'><tr valign='top'><td>Genre:</td><td>
		<input type="radio" name="gepi_denom_boite_genre" id="gepi_denom_boite_genre_m" value="m" <?php if(getSettingValue("gepi_denom_boite_genre")=="m"){echo 'checked';} ?> /> <label for='gepi_denom_boite_genre_m' style='cursor: pointer;'>Masculin</label><br />
		<input type="radio" name="gepi_denom_boite_genre" id="gepi_denom_boite_genre_f" value="f" <?php if(getSettingValue("gepi_denom_boite_genre")=="f"){echo 'checked';} ?> /> <label for='gepi_denom_boite_genre_f' style='cursor: pointer;'>Féminin</label><br />
		</td></tr></table>
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Mode de génération automatique des logins :</td>
	<td>
	<select name='mode_generation_login'>
			<option value='name8'<?php if (getSettingValue("mode_generation_login")=='name8') echo " SELECTED"; ?>> nom (tronqué à 8 caractères)</option>
			<option value='fname8'<?php if (getSettingValue("mode_generation_login")=='fname8') echo " SELECTED"; ?>> pnom (tronqué à 8 caractères)</option>
			<option value='fname19'<?php if (getSettingValue("mode_generation_login")=='fname19') echo " SELECTED"; ?>> pnom (tronqué à 19 caractères)</option>
			<option value='firstdotname'<?php if (getSettingValue("mode_generation_login")=='firstdotname') echo " SELECTED"; ?>> prenom.nom</option>
			<option value='firstdotname19'<?php if (getSettingValue("mode_generation_login")=='firstdotname19') echo " SELECTED"; ?>> prenom.nom (tronqué à 19 caractères)</option>
			<option value='namef8'<?php if (getSettingValue("mode_generation_login")=='namef8') echo " SELECTED"; ?>> nomp (tronqué à 8 caractères)</option>
	</select>
	</td>
	</tr>


	<tr>
		<td style="font-variant: small-caps;" valign='top'>
		Mode de génération des mots de passe :<br />(<i style='font-size:small;'>Jeu de caractères à utiliser en plus des caractères numériques</i>)</td>
	<td valign='top'>
		<input type="radio" name="mode_generation_pwd_majmin" id="mode_generation_pwd_majmin_y" value="y" <?php if((getSettingValue("mode_generation_pwd_majmin")=="y")||(getSettingValue("mode_generation_pwd_majmin")=="")) {echo 'checked';} ?> /> <label for='mode_generation_pwd_majmin_y' style='cursor: pointer;'>Majuscules et minuscules</label><br />
		<input type="radio" name="mode_generation_pwd_majmin" id="mode_generation_pwd_majmin_n" value="n" <?php if(getSettingValue("mode_generation_pwd_majmin")=="n"){echo 'checked';} ?> /> <label for='mode_generation_pwd_majmin_n' style='cursor: pointer;'>Minuscules seulement</label><br />

		<table border='0' summary='Pass'>
		<tr>
		<td valign='top'>
		<input type="checkbox" name="mode_generation_pwd_excl" id="mode_generation_pwd_excl" value="y" <?php if(getSettingValue("mode_generation_pwd_excl")=="y") {echo 'checked';} ?> />
		</td>
		<td valign='top'> <label for='mode_generation_pwd_excl' style='cursor: pointer;'>Exclure les caractères prêtant à confusion (<i>i, 1, l, I, 0, O, o</i>)</label><br />
		</td>
		</tr>
		</table>
	</td>
	</tr>


	<!-- Traitement des problemes de points d'interrogation à la place des accents -->
<?php
/*
	// Apparemment, ce n'est pas utile...
	<tr>
		<td style="font-variant: small-caps;" valign='top'>
		<label for='mode_utf8_releves_pdf' style='cursor: pointer;'>Traitement UTF8 des caractères accentués des relevés de notes PDF&nbsp;:</label>
		</td>
		<td>
		<input type="checkbox" id='mode_utf8_releves_pdf' name="mode_utf8_releves_pdf" value="y"
		<?php
			if(getSettingValue("mode_utf8_releves_pdf")=='y'){echo " checked";}
		?>
		/>
		</td>
	</tr>
*/
?>
	<tr>
		<td style="font-variant: small-caps;" valign='top'>
		<label for='mode_utf8_visu_notes_pdf' style='cursor: pointer;'>Traitement UTF8 des caractères accentués dans la visualisation des notes du carnet de notes&nbsp;:</label>
		</td>
		<td>
		<input type="checkbox" id='mode_utf8_visu_notes_pdf' name="mode_utf8_visu_notes_pdf" value="y"
		<?php
			if(getSettingValue("mode_utf8_visu_notes_pdf")=='y'){echo " checked";}
		?>
		/>
		</td>
	</tr>
<?php
/*
	// Apparemment, ce n'est pas utile...
	<tr>
		<td style="font-variant: small-caps;" valign='top'>
		<label for='mode_utf8_listes_pdf' style='cursor: pointer;'>Traitement UTF8 des caractères accentués des listes PDF&nbsp;:</label>
		</td>
		<td>
		<input type="checkbox" id='mode_utf8_listes_pdf' name="mode_utf8_listes_pdf" value="y"
		<?php
			if(getSettingValue("mode_utf8_listes_pdf")=='y'){echo " checked";}
		?>
		/>
		</td>
	</tr>
*/
?>
	<tr>
		<td style="font-variant: small-caps;" valign='top'>
		<label for='mode_utf8_bulletins_pdf' style='cursor: pointer;'>Traitement UTF8 des caractères accentués des bulletins PDF&nbsp;:</label>
		</td>
		<td>
		<input type="checkbox" id='mode_utf8_bulletins_pdf' name="mode_utf8_bulletins_pdf" value="y"
		<?php
			if(getSettingValue("mode_utf8_bulletins_pdf")=='y'){echo " checked";}
		?>
		/>
		</td>
	</tr>

	<tr>
		<td style="font-variant: small-caps;">
		Feuille de style à utiliser :</td>
	<td>
	<select name='gepi_stylesheet'>
			<option value='style'<?php if (getSettingValue("gepi_stylesheet")=='style') echo " SELECTED"; ?>> Nouveau design</option>
			<option value='style_old'<?php if (getSettingValue("gepi_stylesheet")=='style_old') echo " SELECTED"; ?>> Design proche des anciennes versions (1.4.*)</option>
	</select>
	</td>
	</tr>
	<?php
/*
		echo "<tr>\n";
		if(file_exists("../lib/ss_zip.class.php")){
			echo "<td style='font-variant: small-caps;'>Permettre l'export des carnets de notes au format ODS :<br />(<i>si les professeurs ne font pas le ménage après génération des exports,<br />ces fichiers peuvent prendre de la place sur le serveur</i>)</td>\n";
			echo "<td><input type='checkbox' name='export_cn_ods' value='y'";
			if(getSettingValue('export_cn_ods')=='y'){
				echo ' checked';
			}
			echo " />";
			echo "</td>\n";
		}
		else{
			echo "<td style='font-variant: small-caps;'>En mettant en place la bibliothèque 'ss_zip_.class.php' dans le dossier '/lib/', vous pouvez générer des fichiers tableur ODS pour permettre des saisies hors ligne, la conservation de données,...<br />Voir <a href='http://smiledsoft.com/demos/phpzip/' style=''>http://smiledsoft.com/demos/phpzip/</a><br />Une version limitée est disponible gratuitement.</td>\n";
			echo "<td>&nbsp;</td>\n";

			// Comme la bibliothèque n'est pas présente, on force la valeur à 'n':
			$svg_param=saveSetting("export_cn_ods", 'n');
		}
		echo "</tr>\n";
*/
	?>
	<?php
		echo "<tr>\n";
		if(file_exists("../lib/pclzip.lib.php")){
			echo "<td style='font-variant: small-caps;'>Taille maximale extraite des fichiers dézippés:<br />
(<i style='font-size:small;'>Un fichier dézippé peut prendre énormément de place.<br />
Par prudence, il convient de fixer une limite à la taille d'un fichier extrait.<br />
En mettant zéro, vous ne fixez aucune limite.<br />
En mettant une valeur négative, vous désactivez le désarchivage</i>)</td>\n";
			echo "<td valign='top'><input type='text' name='unzipped_max_filesize' value='";
			$unzipped_max_filesize=getSettingValue('unzipped_max_filesize');
			if($unzipped_max_filesize==""){
				echo '10';
			}
			else {
				echo $unzipped_max_filesize;
			}
			echo "' size='3' /> Mo";
			echo "</td>\n";
		}
		else{
			echo "<td style='font-variant: small-caps;'>En mettant en place la bibliothèque 'pclzip.lib.php' dans le dossier '/lib/', vous pouvez envoyer des fichiers Zippés vers le serveur.<br />Voir <a href='http://www.phpconcept.net/pclzip/index.php' style=''>http://www.phpconcept.net/pclzip/index.php</a></td>\n";
			echo "<td>&nbsp;</td>\n";
		}
		echo "</tr>\n";
	?>

	<tr>
		<td style="font-variant: small-caps;">
		<a name='delais_apres_cloture'></a>
		Nombre de jours avant déverrouillage de l'accès aux appréciations des bulletins pour les responsables et les élèves une fois la période close&nbsp;:<br />
		<div style='font-variant: normal; font-style: italic; font-size: small;'>Sous réserve:<br />
		<ul>
			<li style='font-variant: normal; font-style: italic; font-size: small;'>de créer des comptes pour les responsables et élèves,</li>
			<li style='font-variant: normal; font-style: italic; font-size: small;'>d'autoriser l'accès aux bulletins simplifiés ou aux graphes dans <a href='droits_acces.php'>Droits d'accès</a></li>
			<li style='font-variant: normal; font-style: italic; font-size: small;'>d'opter pour le mode de déverrouillage automatique sur le critère "période close".</li>
		</ul>
		</div>
		</td>
		<td valign='top'>
			<?php
			$delais_apres_cloture=getSettingValue("delais_apres_cloture");
			if($delais_apres_cloture=="") {$delais_apres_cloture=0;}
			echo "<input type='text' name='delais_apres_cloture' size='2' value='$delais_apres_cloture' />\n";
			?>
		</td>
	</tr>


	<tr>
		<td style="font-variant: small-caps;">
		N° d'enregistrement à la CNIL : <br />
		<span class='small'>Conformément à l'article 16 de la loi 78-17 du 6 janvier 1978, dite loi informatique et liberté,
		cette installation de GEPI doit faire l'objet d'une déclaration de traitement automatisé d'informations nominatives auprès
		de la CNIL. Si ce n'est pas encore le cas, laissez libre le champ ci-contre</span>
		</td>
		<td><input type="text" name="num_enregistrement_cnil" size="20" value="<?php echo(getSettingValue("num_enregistrement_cnil")); ?>" />
		</td>
	</tr>
</table>
<input type="hidden" name="is_posted" value="1" />
<center><input type="submit" name = "OK" value="Enregistrer" style="font-variant: small-caps;" /></center>
</form>
<hr />
<form enctype="multipart/form-data" action="param_gen.php" method="post" name="form2" style="width: 100%;">
<table border='0' cellpadding="5" cellspacing="5" summary='Logo'>
<?php
echo "<tr><td colspan=2 style=\"font-variant: small-caps;\"><b>Logo de l'établissement : </b></td></tr>\n";
echo "<tr><td colspan=2>Le logo est visible sur les bulletins officiels, ainsi que sur la page d'accueil publique des cahiers de texte</td></tr>\n";
echo "<tr><td>Modifier le Logo (png, jpg et gif uniquement) : ";
echo "<input type=\"file\" name=\"doc_file\" />\n";
echo "<input type=\"submit\" name=\"valid_logo\" value=\"Enregistrer\" /><br />\n";
echo "Supprimer le logo : <input type=\"submit\" name=\"sup_logo\" value=\"Supprimer le logo\" /></td>\n";


$nom_fic_logo = getSettingValue("logo_etab");

$nom_fic_logo_c = "../images/".$nom_fic_logo;
if (($nom_fic_logo != '') and (file_exists($nom_fic_logo_c))) {
echo "<td><b>Logo actuel : </b><br /><img src=\"".$nom_fic_logo_c."\" border='0' alt=\"logo\" /></td>\n";
} else {
echo "<td><b><i>Pas de logo actuellement</i></b></td>\n";
}
echo "</tr></table></form>\n";
?>

<hr />
<form enctype="multipart/form-data" action="param_gen.php" method="post" name="form3" style="width: 100%;">
<table border='0' cellpadding="5" cellspacing="5" summary='Pmv'>
	<tr>
		<td style="font-variant: small-caps;">
		Tester la présence du module phpMyVisite (<i>pmv.php</i>) :</td>
	<td>
		<input type="radio" name="gepi_pmv" id="gepi_pmv_y" value="y" <?php if(getSettingValue("gepi_pmv")!="n"){echo 'checked';} ?> /><label for='gepi_pmv_y' style='cursor: pointer;'> Oui</label><br />
		<input type="radio" name="gepi_pmv" id="gepi_pmv_n" value="n" <?php if(getSettingValue("gepi_pmv")=="n"){echo 'checked';} ?> /><label for='gepi_pmv_n' style='cursor: pointer;'> Non</label><br />
	</td>
	</tr>
</table>

<input type="hidden" name="is_posted" value="1" />
<center><input type="submit" name = "OK" value="Enregistrer" style="font-variant: small-caps;" /></center>

<table summary='Remarque'><tr><td valign='top'><i>Remarque:</i></td><td>Il arrive que ce test de présence provoque un affichage d'erreur (<i>à propos de pmv.php</i>).<br />
Dans ce cas, désactivez simplement le test.</td></tr></table>
</form>
<p><br /></p>

<?php
require("../lib/footer.inc.php");
?>
