<?php
/** 
 * Fonctions de manipulation du gepi_alea contre les attaques CSRF
 * 
 * $Id: share-csrf.inc.php 7667 2011-08-09 20:02:54Z regis $
 * 
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * 
 * @package Initialisation
 * @subpackage gepi_alea
 *
*/


/** Génération d'une variable aléatoire 
 * 
 * Génère un nombre aléatoire et le stocke en $_SESSION
 * 
 * @see getSettingValue()
 */
function generate_token() {
    if (!isset($_SESSION["gepi_alea"])) {
		$length = rand(35, 45);
		for($len=$length,$r='';strlen($r)<$len;$r.=chr(!mt_rand(0,2)? mt_rand(48,57):(!mt_rand(0,1) ? mt_rand(65,90) : mt_rand(97,122))));
		// Virer le gepi_alea par la suite
		$_SESSION["gepi_alea"] = $r;
	
		if(getSettingValue('csrf_log')=='y') {
			$csrf_log_chemin=getSettingValue('csrf_log_chemin');
			if($csrf_log_chemin=='') {$csrf_log_chemin="/home/root/csrf";}
			if(isset($_SESSION['login'])) {
				$f=fopen("$csrf_log_chemin/csrf_".$_SESSION['login'].".log","a+");
				fwrite($f,"Initialisation de la session ".strftime("%a %d/%m/%Y %H:%M:%S")." avec\n\$_SESSION['gepi_alea']=".$_SESSION['gepi_alea']."\n");
				fwrite($f,"session_id()=".session_id()."\n");
				fclose($f);
			}
		}
    }
}

/**
 * Renvoie une balise <input type='hidden'... /> avec la variable $_SESSION['gepi_alea']
 * 
 * Dans une page, il ne devrait y avoir qu'un seul appel à add_token_field(TRUE), les autres... 
 * dans les autres formulaires étant avec add_token_field()
 * 
 * Appels pour insérer le champ 'csrf_alea' dans des formulaires inclus dans le code
 * de messages du panneau d'affichage (table 'messages') : 
 * add_token_field(TRUE,FALSE) ou add_token_field(FALSE,FALSE)
 * 
 * @todo On pourrait utiliser une variable globale pour... si l'id csrf_alea est déjà défini ne plus l'ajouter...
 * @param bool $avec_id ajoute un argument id='csrf_alea' à la balise si TRUE
 * @param bool $avec_gepi_alea remplace le nombre aléatoire par "_CRSF_ALEA_" si FALSE
 * @return text la balise <input...>
 */
function add_token_field($avec_id=FALSE,$avec_gepi_alea=TRUE) {
	
	if ($avec_gepi_alea) $gepi_alea=$_SESSION['gepi_alea']; else $gepi_alea="_CRSF_ALEA_";
	
    if($avec_id) {
        return "<input type='hidden' name='csrf_alea' id='csrf_alea' value='".$gepi_alea."' />\n";
    }
    else {
        return "<input type='hidden' name='csrf_alea' value='".$gepi_alea."' />\n";
    }
}

/**
 * Insère le champ 'csrf_alea' dans une adresse de page
 * 
 * appels pour insérer le champ 'csrf_alea' dans des liens inclus dans le code
 * de messages du panneau d'affichage (table 'messages') :
 * add_token_in_url(TRUE,FALSE) ou add_token_in_url(FALSE,FALSE)
 * 
 * @param bool $html_chars echappe le & si TRUE
 * @param bool $avec_gepi_alea remplace le nombre aléatoire par "_CRSF_ALEA_" si FALSE
 * @return text le texte à ajouter à l'URL
 */
function add_token_in_url($html_chars = TRUE, $avec_gepi_alea=TRUE) {
	
	if ($avec_gepi_alea) $gepi_alea=$_SESSION['gepi_alea']; else $gepi_alea="_CRSF_ALEA_";

	if($html_chars) {
		return "&amp;csrf_alea=".$gepi_alea;
	}
	else {
		return "&csrf_alea=".$gepi_alea;
	}
}

/**
 * Insère $_SESSION['gepi_alea'] dans une fonction javascript
 *
 * @return text $_SESSION['gepi_alea']
 */
function add_token_in_js_func() {
	return $_SESSION['gepi_alea'];
}

/**
 * Vérifie que le csrf_alea est bon
 * 
 * Avant le Header, on appelle check_token()
 * 
 * Après le Header, on appelle check_token(FALSE)
 *
 * @global int
 * @param bool $redirection 
 * @see getSettingValue()
 * @see action_alea_invalide()
 */
function check_token($redirection=TRUE) {
	global $niveau_arbo;

	$csrf_alea=isset($_POST['csrf_alea']) ? $_POST['csrf_alea'] : (isset($_GET['csrf_alea']) ? $_GET['csrf_alea'] : "");

	if(isset($niveau_arbo)) {
		if($niveau_arbo=="0") {
		}
		elseif($niveau_arbo==1) {
			$pref_arbo="..";
		}
		elseif($niveau_arbo==2) {
			$pref_arbo="../..";
		}
		elseif($niveau_arbo==3) {
			$pref_arbo="../../..";
		}
		elseif ($niveau_arbo == "public") {
			$pref_arbo="..";
			// A REVOIR... SI C'EST PUBLIC, ON N'EST PAS LOGUé
			// NORMALEMENT, EN PUBLIC on ne devrait pas avoir de page sensible
		}
	}
	else {
		$pref_arbo="..";
	}

	if(getSettingValue('csrf_mode')=='strict') {
		if($csrf_alea!=$_SESSION['gepi_alea']) {
			action_alea_invalide();
			if($redirection) {
				header("Location: $pref_arbo/accueil.php?msg=Opération non autorisée");
			}
			else {
				echo "<p style='color:red'>Opération non autorisée</p>\n";
				require("$pref_arbo/lib/footer.inc.php");
			}
			die();
		}
	}
	elseif(getSettingValue('csrf_mode')=='mail_seul') {
		if($csrf_alea!=$_SESSION['gepi_alea']) {
			action_alea_invalide();
		}
	}
	else {
		if($csrf_alea!=$_SESSION['gepi_alea']) {
			// Sans mail
			action_alea_invalide(FALSE);
		}
	}
}

/**
 * Actions en cas d'attaque CSRF
 *
 * Construit un message à envoyé à l'administrateur et à enregistrer dans les logs
 *
 * @param bool $envoyer_mail Un courriel est envoyé à l'administrateur si TRUE
 * @see getSettingValue()
 * @see envoi_mail()
 */
function action_alea_invalide($envoyer_mail=TRUE) {
	
	// NE pas donner dans le mail les valeurs du token pour éviter des problèmes lors d'une éventuelle capture du mail.

	$details="La personne victime de l'attaque était ".$_SESSION['login'].".\n";
	$details.="La page cible était ".$_SERVER['PHP_SELF']." avec les variables suivantes:\n";
	$details.="Variables en \$_POST:\n";
	foreach($_POST as $key => $value) {
      $details.="   \$_POST[$key]=$value\n";
	}

	$details.="Variables en \$_GET:\n";
	foreach($_GET as $key => $value) {
		$details.="   \$_GET[$key]=$value\n";
	}

	if($envoyer_mail) {
		// Envoyer un mail à l'admin
		$envoi_mail_actif=getSettingValue('envoi_mail_actif');
		if($envoi_mail_actif!="n") {
			$destinataire=getSettingValue('gepiAdminAdress');
			if($destinataire!='') {
				$sujet="Attaque CSRF";
				$message="La variable csrf_alea ne coincide pas avec le gepi_alea en SESSION.\n";
				$message.=$details;
				envoi_mail($sujet, $message,$destinataire);
			}
		}
	}

	if(getSettingValue('csrf_log')=='y') {
		$csrf_log_chemin=getSettingValue('csrf_log_chemin');
		if($csrf_log_chemin=='') {$csrf_log_chemin="/home/root/csrf";}
		$f=fopen("$csrf_log_chemin/csrf_".$_SESSION['login'].".log","a+");
		fwrite($f,"Alerte CSRF ".strftime("%a %d/%m/%Y %H:%M:%S")." avec\n");
		fwrite($f,"\$_SESSION['gepi_alea']=".$_SESSION['gepi_alea']."\n");
		fwrite($f,$details."\n");
		fwrite($f,"================================================\n");
		fclose($f);
	}
}


?>
