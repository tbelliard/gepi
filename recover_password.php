<?php
/*
 *
 * Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
$niveau_arbo = 0;

// On indique qu'il faut crée des variables non protégées (voir fonction cree_variables_non_protegees())
// ceci pour que les mots de passe ne soient pas altérés
$variables_non_protegees = 'yes';

// Initialisations files
require_once("./lib/initialisations.inc.php");

if (getSettingValue("enable_password_recovery") != "yes") {
	echo "<p>Vous n'avez pas à être ici.</p>";
	die();
}
$message = false;

if (isset($_POST['login'])) {
	$email = (isset($_POST['email'])) ? $_POST['email'] : "noemail";
	$user_login = (!empty($_POST['login'])) ? $_POST['login'] : "nologin";
	// Le formulaire de demande a été posté, on vérifie et on envoit un mail
	$test = mysqli_query($GLOBALS["mysqli"], "SELECT statut FROM utilisateurs WHERE (" .
			"login = '" . $user_login . "' and " .
			"email = '" . $email . "')");
	if (mysqli_num_rows($test) == 1) {
		// On a un utilisateur qui a bien ces coordonnées.

		// On va maintenant vérifier son statut, et s'assurer que le statut en question
		// est bien autorisé à utiliser l'outil de réinitialisation
		$user_statut = mysql_result($test, 0);
		$ok = false;

		if (
			($user_statut == "administrateur" AND getSettingValue("GepiPasswordReinitAdmin") == "yes") OR
			($user_statut == "professeur" AND getSettingValue("GepiPasswordReinitProf") == "yes") OR
			($user_statut == "scolarite" AND getSettingValue("GepiPasswordReinitScolarite") == "yes") OR
			($user_statut == "cpe" AND getSettingValue("GepiPasswordReinitCpe") == "yes") OR
			($user_statut == "eleve" AND getSettingValue("GepiPasswordReinitEleve") == "yes") OR
			($user_statut == "responsable" AND getSettingValue("GepiPasswordReinitParent") == "yes")
		) {
			$ok = true;
		} else {
			$ok = false;
		}

    // dans le cas d'un SSO, existence d'utilisateurs SSO repérés grâce au champ password vide.
    $testpassword = sql_query1("select password from utilisateurs where login = '".$user_login."'");
    if ($testpassword == -1) {
			$ok = false;
			$sso='yes';
		} else {
      $sso='no';
    }

		if (!$ok) {
			$message = "Pour des raisons de sécurité, votre statut utilisateur ne vous permet pas de réinitialiser votre mot de passe par cette procédure. Vous devrez donc contacter l'administrateur pour obtenir un nouveau mot de passe.";
      if ($sso=='yes')
    			$message = "Ce n'est pas GEPI qui gère votre compte mais un système d'authentification unique. Vous ne pouvez donc pas réinitialiser votre mot de passe par cette procédure.";
		} else {
			//On envoie un mail!
			// On génère le ticket
	        $length = rand(85, 100);
	        for($len=$length,$r='';mb_strlen($r)<$len;$r.=chr(!mt_rand(0,2)? mt_rand(48,57):(!mt_rand(0,1) ? mt_rand(65,90) : mt_rand(97,122))));
	        $ticket = $r;
	        // On enregistre le ticket dans la base
	        $expiration_timestamp = time()+15*60;
	        $expiration_date = date("Y-m-d G:i:s", $expiration_timestamp);
	        $res = mysqli_query($GLOBALS["mysqli"], "UPDATE utilisateurs SET " .
	        		"password_ticket = '" . $ticket . "', " .
	        		"ticket_expiration = '" . $expiration_date . "' WHERE (" .
	        		"login = '" . $user_login . "')");
	        if ($res) {
	        	// Si l'enregistrement s'est bien passé, on envoi le mail
	        	$ticket_url = "";
            // avec la possibilité de forcer le https si le serveur le cache
            if ((!empty($_SERVER['HTTPS']) and $_SERVER['HTTPS'] != "Off") OR (getSettingValue("use_https") == "y")) {
	        		$ticket_url .= "https://";
	        	} else {
	        		$ticket_url .= "http://";
	        	}
	        	$ticket_url .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . "?ticket=".$ticket;
	        	$mail_content = "Bonjour,\n" .
	        			"Afin de réinitialiser votre mot de passe, veuillez cliquer sur le lien suivant : .\n" .
	        			$ticket_url . "\n" .
	        			"Vous pouvez également copier/coller l'adresse complète dans votre navigateur.\n" .
	        			"Ce lien doit être utilisé avant l'heure suivante : " .
	        			date("G:i:s",$expiration_timestamp) ."\n" .
	        			", sous peine de n'être plus valide.\n";

	        	//- Debug - echo $mail_content;
	        	//- Debug - if ($mail_content) {
	        	if (mail($email, "Gepi - réinitialisation de votre mot de passe", $mail_content)) {
	        		$message = "Un courriel vient de vous être envoyé.";
	        	} else {
	        		$message = "Erreur lors de l'envoi du courriel.";
	        	}
	        } else {
	        	echo ((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false));
	        }
		} // Fin: statut autorisé
	} else {
		$message = "Votre identifiant ou votre courriel n'est pas valide.";
	}
}

if (isset($_POST['no_anti_inject_password'])) {
	// Une réinitialisation de mot de passe vient d'être validée
	// On vérifie que le mot de passe et sa confirmation sont correctes et
	// que le mot de passe répond aux critères de sécurité requis
	$message = false;
	// On récupère le statut de l'utilisateur associé au ticket, et l'heure d'expiration :
	$req = mysqli_query($GLOBALS["mysqli"], "SELECT statut, UNIX_TIMESTAMP(ticket_expiration) expiration, login FROM utilisateurs WHERE password_ticket = '" . $_GET['ticket'] . "'");
	if (mysqli_num_rows($req) != 1) {
		$message = "Erreur : le lien n'est pas valide ! <a href='recover_password.php'>Cliquez ici</a> pour formuler une nouvelle demande de changement de mot de passe.";
	} else {
		$user_status = mysql_result($req, 0, "statut");
		$expiration = mysql_result($req, 0, "expiration");
		if ($expiration < time()) {
			$message = "Erreur : le délai de sécurité pour l'utilisation du lien est dépassé. Vous pouvez reformuler une demande en <a href='recover_password.php'>cliquant ici</a>.";
		} else {
			if (($user_status == 'professeur') or ($user_status == 'cpe') or ($user_status == 'responsable') or ($user_status == 'eleve')) {
			    // Mot de passe comportant des lettres et des chiffres
			    $flag = 0;
			} else {
			    // Mot de passe comportant des lettres et des chiffres et au moins un caractère spécial
			    $flag = 1;
			}

			if ($NON_PROTECT["password"] != $NON_PROTECT["confirmation"]) {
				$message = "Mot de passe et confirmation non identiques !";
			} else if (!(verif_mot_de_passe($NON_PROTECT['password'],$flag))) {
				$message = "Mot de passe non conforme.";
			}
			if (!$message) {
				// Si aucune erreur n 'a été renvoyée, on enregistre le mot de passe
                                $user_login = mysql_result($req, 0, "login");
                                $res = Session::change_password_gepi($user_login,$NON_PROTECT["password"]);
				if ($res) {
                                        $res = mysqli_query($GLOBALS["mysqli"], "UPDATE utilisateurs SET password_ticket = '' WHERE password_ticket = '" . $_GET['ticket'] . "'");
					$update_successful = true;
				} else {
					$message = "Erreur lors de la mise à jour de votre mot de passe.";
				}
			}
		}
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<META HTTP-EQUIV="Pragma" CONTENT="no-cache" />
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache" />
<META HTTP-EQUIV="Expires" CONTENT="0" />
<title><?php echo getSettingValue("gepiSchoolName"); ?> : Récupération du mot de passe...</title>
<link rel="stylesheet" type="text/css" href="./style.css" />
<script src="lib/functions.js" type="text/javascript" language="javascript"></script>
<link rel="shortcut icon" type="image/x-icon" href="./favicon.ico" />
<link rel="icon" type="image/ico" href="./favicon.ico" />
<?php
	// Styles paramétrables depuis l'interface:
	if($style_screen_ajout=='y'){
		// La variable $style_screen_ajout se paramètre dans le /lib/global.inc
		// C'est une sécurité... il suffit de passer la variable à 'n' pour désactiver ce fichier CSS et éventuellement rétablir un accès après avoir imposé une couleur noire sur noire
		echo "<link rel='stylesheet' type='text/css' href='$gepiPath/style_screen_ajout.css' />\n";
	}
?>
</head>
<body onload="document.getElementById('login').focus()">
<div>
<?php
echo "<div class='center'>";

// Inutile d'aller plus loin si les connexions ont été désactivées.
if ((getSettingValue("disable_login"))=='yes') {
	echo "<br/><br/><font color=\"red\" size=\"+1\">Le site est en cours de maintenance et temporairement inaccessible.<br />Veuillez nous excuser de ce dérangement et réessayer de vous connecter ultérieurement.</font><br>";
	echo "</div></body></html>";
}

if (isset($update_successful)) {
	echo "<p style='margin-top: 100px; color:red;'>Votre mot de passe a été mis à jour avec succès.</p>";
	echo "<p class=bold style='margin-left: auto; margin-right: auto; margin-top: 40px;'><a href=\"login.php\"><img src='./images/icons/back.png' alt='Retour' class='back_link'/> Retour page de login</a></p>";
	echo "</div></body></html>";
	die();
}

if (isset($_GET['ticket']) and !isset($update_successful)) {

	// Un ticket a été proposé. Il a déjà été filtré contre les injections.
	$error = false;
	$ticket = $_GET['ticket'];
	if (mb_strlen($ticket) < 85) {
		$error = true;
	} else {
		$test = mysqli_query($GLOBALS["mysqli"], "SELECT statut FROM utilisateurs WHERE password_ticket = '" . $ticket . "'");
		if (mysqli_num_rows($test) != "1") {
			$error = true;
		} else {
			// Si on arrive là, c'est que le ticket est valide !
			// On affiche le formulaire pour changer le mot de passe.
			$user_status = mysql_result($test, 0);
			if (($user_status == 'professeur') or ($user_status == 'cpe') or ($user_status == 'responsable') or ($user_status == 'eleve')) {
			    // Mot de passe comportant des lettres et des chiffres
			    $flag = 0;
			} else {
			    // Mot de passe comportant des lettres et des chiffres et au moins un caractère spécial
			    $flag = 1;
			}
	?>
<form action="recover_password.php?ticket=<?php echo $ticket; ?>" method="post" style="width: 100%; margin-top: 24px; margin-bottom: 48px;">
<?php    echo "<p style='margin-top: 50px; color:red; margin-bottom: 30px;width: 80%;margin-left: auto; margin-right: auto;'><b>Attention : le mot de passe doit comporter ".getSettingValue("longmin_pwd") ." caractères minimum. ";
    if ($flag == 1)
        echo "Il doit comporter au moins une lettre, au moins un chiffre et au moins un caractère spécial (#, *,...).";
    else
        echo "Il doit comporter au moins une lettre et au moins un chiffre.";
?>
<fieldset id="login_box" style="width: 50%; margin-top: 0;">
<div id="header">
<h2>Réinitialisation du mot de passe</h2>
</div>
<table style="width: 85%; border: 0; margin-top: 10px; margin-right: 15px; margin-left: auto;" cellpadding="3" cellspacing="0">
  <tr>
  	<td colspan="2" style="padding-bottom: 15px; text-align: right;">
  	<?php
		if ($message) {
			echo("<p style='color: red; margin:0;padding:0;'>" . $message . "</p></td></tr>");
		} else {
			echo "<p style='margin:0;padding:0;'>Veuillez saisir et confirmer votre nouveau mot de passe</p>";
		}
	?>
  	</td>
  </tr>
  <tr>
    <td style="text-align: right; width: 50%; font-variant: small-caps;"><label for="password">Mot de passe</label></td>
    <td style="text-align: center; width: 40%;"><input type="password" id="password" name="no_anti_inject_password" size="16" tabindex="1" /></td>
  </tr>
  <tr>
    <td style="text-align: right; width: 50%; font-variant: small-caps;"><label for="confirmation">Confirmation</label></td>
    <td style="text-align: center; width: 40%;"><input type="password" id="confirmation" name="no_anti_inject_confirmation" size="16" tabindex="2" /></td>
  </tr>
  <tr>
    <td style="text-align: center; padding-top: 10px;"><a class='small' href='login.php'>Retour page de login</a>
    </td>
    <td style="text-align: center; width: 40%; padding-top: 20px;"><input type="submit" name="submit" value="Valider" style="font-variant: small-caps;" tabindex="3" /></td>
  </tr>
</table>
</fieldset>
</form>
</div>


	<?php
		}
	}

	if ($error) {
		echo "<p style='margin-top: 100px; color:red;'>Votre ticket n'est pas valide.</p>";
	}

} else {
?>

<p style='margin-top: 60px;padding-left: 20%; padding-right: 20%;'>Afin de réinitialiser votre mot de passe, vous devez valider ce formulaire en indiquant votre identifiant et votre adresse de courriel.
Cette adresse doit être déjà associée à votre compte au sein de Gepi.
<br/>Si vos identifiant et adresse sont corrects, vous recevrez à cette adresse les instructions pour réinitialiser votre mot de passe.<br/>
<span class='red'>Vous devez réinitialiser votre mot de passe dans les 15 minutes suivant la validation de ce formulaire.</span></p>
<form action="recover_password.php" method="post" style="width: 100%; margin-top: 24px; margin-bottom: 48px;">
<fieldset id="login_box" style="width: 50%; margin-top: 0;">
<div id="header">
<h2>Mot de passe perdu</h2>
</div>
<table style="width: 85%; border: 0; margin-top: 10px; margin-right: 15px; margin-left: auto;" cellpadding="3" cellspacing="0">
  <tr>
  	<td colspan="2" style="padding-bottom: 15px; text-align: right;">
  	<?php
		if ($message) {
			echo("<p style='color: red; margin:0;padding:0 0 0 30px;'>" . $message . "</p></td></tr>");
		} else {
			echo "<p style='margin:0;padding:0;'>Veuillez indiquer votre identifiant et votre courriel</p>";
	?>
  	</td>
  </tr>
  <tr>
    <td style="text-align: right; width: 50%; font-variant: small-caps;"><label for="login">Identifiant</label></td>
    <td style="text-align: center; width: 40%;"><input type="text" id="login" name="login" size="16" tabindex="1" /></td>
  </tr>
  <tr>
    <td style="text-align: right; width: 50%; font-variant: small-caps;"><label for="email">Courriel</label></td>
    <td style="text-align: center; width: 40%;"><input type="text" id="email" name="email" size="16" tabindex="2" /></td>
  </tr>
  <tr>
    <td style="text-align: center; padding-top: 10px;"><a class='small' href='login.php'>Retour page de login</a>
    </td>
    <td style="text-align: center; width: 40%; padding-top: 20px;"><input type="submit" name="submit" value="Valider" style="font-variant: small-caps;" tabindex="3" /></td>
  </tr>
  <?php } ?>
</table>
</fieldset>
</form>
</div>

<?php } ?>

<script language="javascript" type="text/javascript">
<!--
	//function mel(destinataire){
	//	chaine_mel = "mailto:"+destinataire+"?subject=[GEPI]";
	function pigeon(a,b){
		chaine_mel = "mailto:"+a+"_CHEZ_"+b+"?subject=[GEPI]";
		//chaine_mel += "&body=Bonjour,\r\nCordialement.\r\n";
		//chaine_mel += "&body=Bonjour,\\r\\nCordialement.\\r\\n";
		chaine_mel += "&body=Pour que le mail parvienne à son destinataire, pensez à remplacer la chaine de caractères _CHEZ_ par un @";
		//chaine_mel += "&body=Bonjour";
		location.href = chaine_mel;
	}

	/*
	function pigeon2(tab){
		chaine_tmp="";
		for(i=0;i<tab.length;i=i+2){
			chaine_tmp=chaine_tmp+","+tab[i]+"_CHEZ_"+tab[i+1];
		}
		alert("chaine_tmp="+chaine_tmp);
		chaine_mel = "mailto:"+a+"_CHEZ_"+b+"?subject=[GEPI]";
		//chaine_mel += "&body=Bonjour,\r\nCordialement.\r\n";
		//chaine_mel += "&body=Bonjour,\\r\\nCordialement.\\r\\n";
		chaine_mel += "&body=Pour que le mail parvienne à son destinataire, pensez à remplacer la chaine de caractères _CHEZ_ par un @";
		//chaine_mel += "&body=Bonjour";
		location.href = chaine_mel;
	}
	*/

	function pigeon2(){
		chaine_tmp="";
		for(i=0;i<adm_adr.length;i=i+2){
			chaine_tmp=chaine_tmp+","+adm_adr[i]+"_CHEZ_"+adm_adr[i+1];
		}
		chaine_tmp=chaine_tmp.substring(1);
		//alert("chaine_tmp="+chaine_tmp);
		chaine_mel = "mailto:"+chaine_tmp+"?subject=[GEPI]";
		//chaine_mel += "&body=Bonjour,\r\nCordialement.\r\n";
		//chaine_mel += "&body=Bonjour,\\r\\nCordialement.\\r\\n";
		chaine_mel += "&body=Pour que le mail parvienne à son destinataire, pensez à remplacer la chaine de caractères _CHEZ_ par un @";
		//chaine_mel += "&body=Bonjour";
		location.href = chaine_mel;
	}


-->
</script>

<div class="center" style="margin-bottom: 32px;">
<?php
	if(getSettingValue("gepiAdminAdressPageLogin")!='n'){
		$gepiAdminAdress=getSettingValue("gepiAdminAdress");
		//$tmp_adr=explode("@",$gepiAdminAdress);
		//echo("<a href=\"javascript:pigeon('$tmp_adr[0]','$tmp_adr[1]');\">[Contacter l'administrateur]</a> \n");

		//echo "$gepiAdminAdress<br />";

		$compteur=0;
		$tab_adr=array();
		$tmp_adr1=explode(",",$gepiAdminAdress);
		for($i=0;$i<count($tmp_adr1);$i++){
			//echo "\$tmp_adr1[$i]=$tmp_adr1[$i]<br />";
			$tmp_adr2=explode("@",$tmp_adr1[$i]);
			//echo "\$tmp_adr2[0]=$tmp_adr2[0]<br />";
			//echo "\$tmp_adr2[1]=$tmp_adr2[1]<br />";
			if((isset($tmp_adr2[0]))&&(isset($tmp_adr2[1]))) {
				$tab_adr[$compteur]=$tmp_adr2[0];
				$compteur++;
				$tab_adr[$compteur]=$tmp_adr2[1];
				$compteur++;
			}
		}

		echo "<script type='text/javascript'>\n";
		echo "adm_adr=new Array();\n";
		for($i=0;$i<count($tab_adr);$i++){
			echo "adm_adr[$i]='$tab_adr[$i]';\n";
		}
		echo "</script>\n";

		if(count($tab_adr)>0){
			//echo("<a href=\"javascript:pigeon2(adm_adr);\">[Contacter l'administrateur]</a> \n");
			echo("<p><a href=\"javascript:pigeon2();\">[Contacter l'administrateur]</a></p>\n");
		}
	}
?>
</div>
<div id="login_footer">
<a href="http://gepi.mutualibre.org">GEPI : Outil de gestion, de suivi, et de visualisation graphique des résultats scolaires (écoles, collèges, lycées)</a><br />
Copyright &copy; 2001-2013
</div>
</div>
</body>
</html>
