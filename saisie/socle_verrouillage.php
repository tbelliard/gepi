<?php
/*
*
* Copyright 2001, 2017 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

$sql="SELECT 1=1 FROM droits WHERE id='/saisie/socle_verrouillage.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/saisie/socle_verrouillage.php',
administrateur='V',
professeur='F',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Socle: Verrouillage',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

if((!getSettingAOui("SocleSaisieComposantes"))||(!getSettingAOui("SocleOuvertureSaisieComposantes_".$_SESSION["statut"]))) {
	/*
	if(($_SESSION['statut']=="professeur")&&(getSettingAOui("SocleOuvertureSaisieComposantes_PP"))&&(is_pp($_SESSION["login"]))) {
		// Accès autorisé
	}
	else {
		header("Location: ../accueil.php?msg=Accès non autorisé");
		die();
	}
	*/
	header("Location: ../accueil.php?msg=Accès non autorisé");
	die();
}

$msg="";

if(isset($_POST['enregistrer_Saisie_Socle'])) {
	check_token();

	$nb_reg=0;

	if(isset($_POST['SocleOuvertureSaisieComposantes'])) {
		if(!saveSetting("SocleOuvertureSaisieComposantes", $_POST['SocleOuvertureSaisieComposantes'])) {
			$msg.="Erreur lors de l'ouverture/fermeture des saisies de composantes du socle.<br />";
		}
		else {
			$nb_reg++;
		}
	}

	$msg.=$nb_reg." paramètre(s) enregistré(s) <em>(".strftime("le %d/%m/%Y à %H:%M:%S").")</em>.<br />";

}

$themessage  = 'Des valeurs ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
$message_enregistrement = "Les modifications ont été enregistrées !";
//**************** EN-TETE *****************
$titre_page = "Saisie Socle";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

$SocleOuvertureSaisieComposantes=getSettingAOui("SocleOuvertureSaisieComposantes");

echo "<p class='bold'><a href=\"../accueil.php\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

if((acces("/saisie/saisie_socle.php", $_SESSION["statut"]))&&(getSettingAOui("SocleSaisieComposantes_".$_SESSION["statut"]))) {
	echo " | <a href=\"saisie_socle.php\" onclick=\"return confirm_abandon (this, change, '$themessage')\">Saisie des bilans de composantes du socle</a>";
}

echo "</p>

<form action='".$_SERVER['PHP_SELF']."' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field();
?>
		<p style='margin-top:1em; margin-left:3em; text-indent:-3em;'>
			État d'ouverture ou non de la saisie des Bilans de composantes du socle dans Gepi&nbsp;:<br />
			<input type="radio" 
				   id="SocleOuvertureSaisieComposantes_y" 
				   name="SocleOuvertureSaisieComposantes"
					<?php if($SocleOuvertureSaisieComposantes) {echo " checked ";} ?>
				   value="y" 
				   onchange="change_style_radio()" />
			<label for="SocleOuvertureSaisieComposantes_y" id='texte_SocleOuvertureSaisieComposantes_y'>Saisie des <em>Bilans de composantes du socle</em> ouverte</label>
			<br />
			<input type="radio" 
				   id="SocleOuvertureSaisieComposantes_n" 
				   name="SocleOuvertureSaisieComposantes"
					<?php if(!$SocleOuvertureSaisieComposantes) {echo " checked ";} ?>
				   value="n" 
				   onchange="change_style_radio()" />
			<label for="SocleOuvertureSaisieComposantes_n" id='texte_SocleOuvertureSaisieComposantes_n'>
				Saisie des <em>Bilans de composantes du socle</em> fermée
			</label>
		</p>
		<input type="hidden" name="enregistrer_Saisie_Socle" value="y" />
		<input type="submit" value="Valider" />

	</fieldset>
</form>

<?php
echo "<script type='text/javascript'>
".js_change_style_radio("change_style_radio", "n", "y")."

change_style_radio();

item=document.getElementsByTagName('input');
for(i=0;i<item.length;i++) {
	if(item[i].getAttribute('type')=='checkbox') {
		checkbox_change(item[i].getAttribute('id'));
	}
}
</script>";

require("../lib/footer.inc.php");
?>
