<?php

/* @version $Id$ */

// ============== Initialisation ===================
$affiche_connexion = 'yes';
$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}


// Sécurité
if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}

check_token();

// Initialisation des variables
$destinataire=isset($_POST["destinataire"]) ? $_POST["destinataire"] : (isset($_GET["destinataire"]) ? $_GET["destinataire"] : NULL);
$sujet_mail=isset($_POST["sujet_mail"]) ? $_POST["sujet_mail"] : (isset($_GET["sujet_mail"]) ? $_GET["sujet_mail"] : NULL);
$message_mail=isset($_POST["message"]) ? $_POST["message"] : (isset($_GET["message"]) ? $_GET["message"] : NULL);

//debug_var();

// ========== Fin de l'initialisation de la page =============
/*
$fich=fopen("/tmp/envoi_mail.txt","a+");
fwrite($fich,"===============================\n");
fwrite($fich,"\$destinataire=$destinataire\n");
fwrite($fich,"Message avant filtrage:\n");
fwrite($fich,"\$message_mail=$message_mail\n");
*/
$message_mail=preg_replace("/\\\\n/","\n",$message_mail);
$message_mail=stripslashes($message_mail);
/*
fwrite($fich,"\nMessage apres filtrage:\n");
fwrite($fich,"\$message_mail=$message_mail\n");
fwrite($fich,"===============================\n");
fclose($fich);
*/
//echo "\$destinataire=$destinataire<br />\n";
//echo "<pre>\$message_mail=$message_mail</pre>\n";



/*
	$gepiPrefixeSujetMail=getSettingValue("gepiPrefixeSujetMail") ? getSettingValue("gepiPrefixeSujetMail") : "";
	if($gepiPrefixeSujetMail!='') {$gepiPrefixeSujetMail.=" ";}

	$expediteur=retourne_email($_SESSION['login']);
	if($expediteur=='') {
		$expediteur="Mail automatique Gepi <ne-pas-repondre@".$_SERVER['SERVER_NAME'].">";
	}

	$sujet_mail = $gepiPrefixeSujetMail."GEPI : $sujet_mail";
	$sujet_mail = "=?UTF-8?B?".base64_encode($sujet_mail)."?=\r\n";

	$headers = "X-Mailer: PHP/" . phpversion()."\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/plain; charset=UTF-8\r\n";
	$headers .= "From: $expediteur\r\n"."Reply-to: $expediteur\r\n";

	// On envoie le mail
	$envoi = mail($destinataire,
		$sujet_mail,
		$message_mail,
		$headers);
*/
$ajout_header="";

$expediteur=retourne_email($_SESSION['login']);
if(check_mail($expediteur)) {
	$tab_param_mail['from']=$expediteur;
	$tab_param_mail['replyto']=$expediteur;
	$ajout_header.="From: $expediteur\r\n"."Reply-to: $expediteur\r\n";
}
$tab_param_mail['destinataire']=$destinataire;

$envoi = envoi_mail($sujet_mail, $message_mail, $destinataire, $ajout_header, "plain", $tab_param_mail);

if($envoi) {echo " <img src='../images/enabled.png' width='20' height='20' alt='Message envoyé avec succès' title='Message envoyé avec succès' />";}
else {echo " <img src='../images/icons/flag.png' width='17' height='18' alt='Echec de l envoi du message' title='Echec de l envoi du message' />";}
?>
