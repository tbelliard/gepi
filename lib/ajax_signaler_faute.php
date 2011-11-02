<?php

/*
 * $Id: ajax_signaler_faute.php 6678 2011-03-22 17:58:07Z crob $
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

@set_time_limit(0);

// Initialisations files
require_once("../lib/initialisations.inc.php");

//extract($_GET, EXTR_OVERWRITE);
//extract($_POST, EXTR_OVERWRITE);

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

//INSERT INTO droits SET id='/lib/ajax_signaler_faute.php',administrateur='V',professeur='V',cpe='V',scolarite='V',eleve='F',responsable='F',secours='F',autre='V',description='Envoi de mail pour signaler une faute dans une appréciation',statut='';
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

check_token();

header('Content-Type: text/html; charset=ISO-8859-15');

/*
$signalement_login_eleve=isset($_POST['signalement_login_eleve']) ? $_POST['signalement_login_eleve'] : "";
$signalement_id_groupe=isset($_POST['signalement_id_groupe']) ? $_POST['signalement_id_groupe'] : "";
$signalement_message=isset($_POST['signalement_message']) ? $_POST['signalement_message'] : "";
*/

$signalement_login_eleve=isset($_GET['signalement_login_eleve']) ? $_GET['signalement_login_eleve'] : "";
$signalement_id_groupe=isset($_GET['signalement_id_groupe']) ? $_GET['signalement_id_groupe'] : "";

$signalement_id_classe=isset($_GET['signalement_id_classe']) ? $_GET['signalement_id_classe'] : "";
$signalement_num_periode=isset($_GET['signalement_num_periode']) ? $_GET['signalement_num_periode'] : "";

$signalement_message=isset($_GET['signalement_message']) ? $_GET['signalement_message'] : "";

//echo "<pre>$signalement_message</pre>";

//$signalement_message=my_ereg_replace("\\\\n","<br />",$signalement_message);
$signalement_message=my_ereg_replace("\\\\n","\n",$signalement_message);
$signalement_message=stripslashes($signalement_message);

if(($signalement_login_eleve=='')||($signalement_id_groupe=='')||($signalement_message=='')) {
	echo "<span style='color:red'> KO</span>";
	return false;
	die();
}

if(!preg_match('/^[0-9]*$/',$signalement_id_groupe)) {
	echo "<span style='color:red'> KO</span>";
	return false;
	die();
}

// Contrôler que la personne est autorisée à faire le signalement





$envoi_mail_actif=getSettingValue('envoi_mail_actif');

if($envoi_mail_actif!='n') {
	// Recherche des destinataires
	$sql="SELECT u.email FROM j_groupes_professeurs jgp, utilisateurs u WHERE u.login=jgp.login AND jgp.id_groupe='$signalement_id_groupe' AND u.email!='' AND u.email LIKE '%@%';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		echo "<span style='color:red'> KO</span>";
		return false;
		die();
	}

	$ajout_headers="";
	$email_utilisateur=retourne_email($_SESSION['login']);
	if($email_utilisateur!='') {
		$ajout_headers="Reply-to: $email_utilisateur";
	}

	// On considère que le signalement est un succès, si le mail est envoyé pour au moins un destinataire
	$temoin=false;
	while($lig=mysql_fetch_object($res)) {
		$destinataire=$lig->email;

		// On met 
		$sujet="[GEPI]: Signalement par ".casse_mot($_SESSION['prenom'],'majf2')." ".$_SESSION['nom'];

		//if(envoi_mail($sujet, nl2br($signalement_message), $destinataire, $ajout_headers)) {$temoin=true;}
		if(envoi_mail($sujet, $signalement_message, $destinataire, $ajout_headers)) {$temoin=true;}
	}

	echo "<span style='color:green'> OK</span>";

	$tab_champs=array('periodes');
	$current_group=get_group($signalement_id_groupe,$tab_champs);
	if(($current_group["classe"]["ver_periode"][$signalement_id_classe][$signalement_num_periode]=='P')&&
	(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite'))) {
		echo " <a href='../bulletin/autorisation_exceptionnelle_saisie_app.php?id_classe=$signalement_id_classe&periode=$signalement_num_periode&id_groupe=$signalement_id_groupe&refermer_page=y' target='_blank' alt='Autorisation exceptionnelle de correction' title='Autorisation exceptionnelle de correction'><img src='../images/icons/wizard.png' width='16' height='16' alt='Autorisation exceptionnelle de correction' title='Autorisation exceptionnelle de correction' /></a>";
	}

	return $temoin;
}
else {
	echo "<span style='color:red'> KO</span>";
	return false;
}
?>
