<?php

/*
 *
 * Copyright 2001, 2015 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

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

header('Content-Type: text/html; charset=utf-8');

//debug_var();

$signalement_app_grp=isset($_POST['signalement_app_grp']) ? $_POST['signalement_app_grp'] : "";
$signalement_login_eleve=isset($_POST['signalement_login_eleve']) ? $_POST['signalement_login_eleve'] : "";
$signalement_id_groupe=isset($_POST['signalement_id_groupe']) ? $_POST['signalement_id_groupe'] : "";
$signalement_id_classe=isset($_POST['signalement_id_classe']) ? $_POST['signalement_id_classe'] : "";
$signalement_num_periode=isset($_POST['signalement_num_periode']) ? $_POST['signalement_num_periode'] : "";

$signalement_message=isset($NON_PROTECT['signalement_message']) ? traitement_magic_quotes($NON_PROTECT['signalement_message']) : "";

/*
$f=fopen("/tmp/debug_mail_signalement_faute.txt","a+");
fwrite($f,"========================================"."\n");
fwrite($f,"++++++++++++++++++++++++++++++++++++++++"."\n");
fwrite($f,"========================================"."\n");
fwrite($f,strftime("%Y%m%d à %H%M%S")."\n");
fwrite($f,$signalement_message."\n");

fwrite($f,"========================================"."\n");
fwrite($f,strftime("%Y%m%d à %H%M%S")."\n");
fwrite($f,$signalement_message."\n");
*/

$signalement_message=preg_replace("/\\\\n/","\n",$signalement_message);
$signalement_message=stripslashes($signalement_message);

/*
fwrite($f,"========================================"."\n");
fwrite($f,$signalement_message."\n");
fwrite($f,"========================================"."\n");
fclose($f);
*/


if((($signalement_app_grp=="")&&($signalement_login_eleve==''))||($signalement_id_groupe=='')||($signalement_message=='')) {
	echo "<span style='color:red' title='Erreur lors du signalement de faute'> KO</span>";
	return false;
	die();
}

if(!preg_match('/^[0-9]*$/',$signalement_id_groupe)) {
	echo "<span style='color:red' title='Erreur lors du signalement de faute'> KO</span>";
	return false;
	die();
}

// Contrôler que la personne est autorisée à faire le signalement

$envoi_mail_actif=getSettingValue('envoi_mail_actif');

// Recherche des destinataires
$sql="SELECT u.login, u.email FROM j_groupes_professeurs jgp, utilisateurs u WHERE u.login=jgp.login AND jgp.id_groupe='$signalement_id_groupe';";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)==0) {
	echo "<span style='color:red' title='Erreur lors du signalement de faute: Aucun destinataire trouvé???'> KO</span>";
	return false;
}
else {
	$pos_crsf_alea=strpos($signalement_message,"_CSRF_ALEA_");
	if($pos_crsf_alea!==false) {
		echo "<span style='color:res' title='ERREUR'> Contenu interdit</span>";
		return false;
	}
	else {
		$ajout_headers="";
		$email_utilisateur=retourne_email($_SESSION['login']);
		if($email_utilisateur!='') {
			$ajout_headers="Reply-to: $email_utilisateur";
			$tab_param_mail['replyto']=$email_utilisateur;
		}

		// On considère que le signalement est un succès, si le mail est envoyé pour au moins un destinataire
		$temoin=false;
		$temoin2=true;
		while($lig=mysqli_fetch_object($res)) {

			if(($envoi_mail_actif!='n')&&(check_mail($lig->email))) {
				$destinataire=$lig->email;
				$tab_param_mail['destinataire']=$destinataire;

				$sujet="[GEPI]: Signalement par ".casse_mot($_SESSION['prenom'],'majf2')." ".$_SESSION['nom'];

				//if(envoi_mail($sujet, nl2br($signalement_message), $destinataire, $ajout_headers)) {$temoin=true;}
				if(envoi_mail($sujet, $signalement_message, $destinataire, $ajout_headers,"plain",$tab_param_mail)) {$temoin=true;}
			}

			// On dépose un message en page d'accueil:
			$statuts_destinataires="_";
			// Deux semaines d'affichage
			$date_debut=time();
			$date_fin=$date_debut+2*7*24*3600;
			$date_decompte=$date_fin;

			$contenu_cor="<strong>Signalement par ".casse_mot($_SESSION['prenom'],'majf2')." ".$_SESSION['nom']."</strong><br />".mysqli_real_escape_string($GLOBALS['mysqli'], nl2br($signalement_message));

			if(!set_message($contenu_cor,$date_debut,$date_fin,$date_decompte,$statuts_destinataires,$lig->login)) {
				$temoin2=false;
			}

		}

		if(($temoin)&&($temoin2)) {
			echo "<span style='color:green' title=\"Signalement de faute effectué : Mail et message en page d'accueil.\"> OK</span>";
		}
		elseif($temoin2) {
			echo "<span style='color:green' title=\"Signalement de faute effectué en page d'accueil, mais pas de mail envoyé.\"><img src='$gepiPath/images/icons/mail_echec.png' class='icone16' alt='Echec mail' >OK</span>";
		}
		else {
			echo "<span style='color:red' title=\"Echec du signalement de faute.\">KO</span>";
		}

		$tab_champs=array('periodes');
		$current_group=get_group($signalement_id_groupe,$tab_champs);
		if(($current_group["classe"]["ver_periode"][$signalement_id_classe][$signalement_num_periode]=='P')&&
		(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite'))) {
			echo " <a href='../bulletin/autorisation_exceptionnelle_saisie_app.php?id_classe=$signalement_id_classe&periode=$signalement_num_periode&id_groupe=$signalement_id_groupe&refermer_page=y' target='_blank' alt='Autorisation exceptionnelle de correction' title='Autorisation exceptionnelle de correction'><img src='../images/icons/wizard.png' width='16' height='16' alt='Autorisation exceptionnelle de correction' title='Autorisation exceptionnelle de correction' /></a>";
		}

		return $temoin;
	}
}
?>
