<?php
/*
 *
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

//**************** EN-TETE *****************
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *************
$fiche=isset($_POST["fiche"]) ? $_POST["fiche"] : (isset($_GET["fiche"]) ? $_GET["fiche"] : "personnels");
$user_login=isset($_POST['user_login']) ? $_POST['user_login'] : (isset($_GET['user_login']) ? $_GET['user_login'] : NULL);

if(!isset($user_login)) {
	$nom = 'BONNOT';
	$prenom = 'Jean';
	$identifiant = "JBONNOT";
	$mdp = "5Cdff45DF";
	$email = 'jbonnot@ici.fr';
	$fiche_demo="y";
}
else {
	$sql="SELECT * FROM utilisateurs WHERE login='".$user_login."';";
	$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p style='color:red'>Le login proposé (<em>".$user_login."</em>) n'existe pas</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$lig=mysqli_fetch_object($res);
	$nom = casse_mot($lig->nom, "maj");
	$prenom = casse_mot($lig->prenom, "majf2");
	$identifiant = $user_login;
	$email = $lig->email;

	if($fiche=="eleves") {
		$chaine_classes=get_chaine_liste_noms_classes_from_ele_login($user_login);
	}
	elseif($fiche=="responsables") {
		$tab=get_enfants_from_resp_login($user_login, 'avec_classe');
		$chaine_enfants="";
		for($loop=1;$loop<count($tab);$loop+=2) {
			if($loop>1) {
				$chaine_enfants.=", ";
			}
			$chaine_enfants.=$tab[$loop];
		}
	}

	$fiche_demo="n";
}

switch ($fiche) {
case 'personnels' :
	$impression = getSettingValue("Impression");
	$nb_fiches = getSettingValue("ImpressionNombre");
	break;
case 'parents' :
	$impression = getSettingValue("ImpressionFicheParent");
	$nb_fiches = getSettingValue("ImpressionNombreParent");
	break;
case 'responsables' :
	$impression = getSettingValue("ImpressionFicheParent");
	$nb_fiches = getSettingValue("ImpressionNombreParent");
	break;
case 'eleves' :
	$impression = getSettingValue("ImpressionFicheEleve");
	$nb_fiches = getSettingValue("ImpressionNombreEleve");
	break;
}

if($fiche_demo=="n") {
	$nb_fiches=1;
	// Sinon on va mettre deux fois la même fiche dans la page.
	// Dans le cas d'une fiche de démo, voir si ça tient dans la page est bien.

	//++++++++++++++++++++++++++++++
	$mail_user=get_mail_user($user_login);

	echo "<div id='div_compte_rendu_envoi_mail' style='text-align:center;' class='noprint'></div>\n";

	echo "<div id='lien_mail' style='float:right; width:16px; display:none' class='noprint'><a href=\"javascript:afficher_div('div_envoi_FB_par_mail','y',10,10)\" title=\"Envoyer par mail la Fiche Bienvenue de $user_login.\"><img src='../images/icons/courrier_envoi.png' class='icon16' alt='Mail' /></a></div>
	<script type='text/javascript'>document.getElementById('lien_mail').style.display=''</script>\n";
	//echo "</div>\n";
https://127.0.0.1/steph/gepi_git_trunk/gestion/modele_fiche_information.php?user_login=abele&fiche=eleves
	$titre_infobulle="Envoi Fiche Bienvenue par mail";
	$texte_infobulle="<form action='".$_SERVER['PHP_SELF']."' name='form_envoi_fb_mail' method='post'>
<input type='hidden' name='envoi_mail' value='y' />
<input type='hidden' name='fiche' value='$fiche' />";
	if(isset($user_login)) {
		$texte_infobulle.="
<input type='hidden' name='user_login' value='$user_login' />";
	}
	$texte_infobulle.="
<p>Précisez à quelle adresse vous souhaitez envoyer la fiche bienvenue&nbsp;:<br />
Mail&nbsp;:&nbsp;<input type='text' name='mail_dest' value='$mail_user' />
<input type='submit' value='Envoyer' id='button_submit_form_envoi_fb_mail' onclick='afficher_envoi_mail_en_cours()' />
<img src='../images/spinner.gif' class='icon16' title='Envoi en cours' alt='Envoi en cours' style='display:none' id='img_envoi_fb_mail' />
</form>
<script type='text/javascript'>
	function afficher_envoi_mail_en_cours() {
		document.getElementById('button_submit_form_envoi_fb_mail').style.display='none';
		document.getElementById('img_envoi_fb_mail').style.display='';
	}
</script>";
	$tabdiv_infobulle[]=creer_div_infobulle('div_envoi_FB_par_mail',$titre_infobulle,"",$texte_infobulle,"",30,0,'y','y','n','n');
	//++++++++++++++++++++++++++++++
}

$lignes_FB="";
for ($i=0;$i<$nb_fiches;$i++) {
	$lignes_FB.="<table><tr><td>A l'attention de </td><td><span class = \"bold\">" . $nom . " " . $prenom . "</span></td></tr>
	<tr><td>Nom de login : </td><td class = \"bold\">" . $identifiant. "</td></tr>";
	if(isset($mdp)) {
		$lignes_FB.="
	<tr><td>Mot de passe : </td><td class = \"bold\">" . $mdp . "</td></tr>";
	}
	if(isset($email)) {
		$lignes_FB.="
	<tr><td>Adresse E-mail : </td><td class = \"bold\">" . $email . "</td></tr>";
	}

	if(isset($chaine_enfants)) {
		$lignes_FB.="
	<tr><td>Responsable de : </td><td class = \"bold\">" . $chaine_enfants . "</td></tr>";
	}
	elseif(isset($chaine_classes)) {
		$lignes_FB.="
	<tr><td>Classe : </td><td class = \"bold\">" . $chaine_classes . "</td></tr>";
	}

	$lignes_FB.="
</table>";

	if($fiche_demo=="y") {
		$lignes_FB.="<p style='font-variant:small-caps;color:red;'>La ligne donnant le mot de passe de l'utilisateur
ne figure sur la fiche <b>QUE SI</b> cette dernière est imprimée dès la création de l'utilisateur.</p>";
	}

	$lignes_FB.=$impression;

	echo $lignes_FB;
}

//++++++++++++++++++++++++++++++
$mail_dest=isset($_POST['mail_dest']) ? $_POST['mail_dest'] : NULL;
$envoi_mail=isset($_POST['envoi_mail']) ? $_POST['envoi_mail'] : "n";

if($envoi_mail=="y") {
	if(!check_mail($_POST['mail_dest'])) {
		$message="L'adresse mail choisie '".$_POST['mail_dest']."' est invalide.";
		echo "<p style='color:red; text-align:center;' class='noprint'>$message</p>
		<script type='text/javascript'>
			document.getElementById('div_compte_rendu_envoi_mail').innerHTML=\"<span style='color:red'>$message</span>\";
		</script>\n";
	}
	else {
		$sujet="Fiche Bienvenue Gepi";
		$message="Bonjour(soir),\nVoici votre Fiche Bienvenue Gepi :\n".$lignes_FB;
		$destinataire=$_POST['mail_dest'];
		$header_suppl="";
		if((isset($_SESSION['email']))&&(check_mail($_SESSION['email']))) {
			$header_suppl.="Bcc:".$_SESSION['email']."\r\n";
		}
		$envoi=envoi_mail($sujet, $message, $destinataire, $header_suppl, "html");
		if($envoi) {
			$message="La Fiche Bienvenue a été expédié à l'adresse mail choisie '".$_POST['mail_dest']."'.";
			echo "<p style='color:green; text-align:center;' class='noprint'>$message</p>
			<script type='text/javascript'>
				document.getElementById('div_compte_rendu_envoi_mail').innerHTML=\"<span style='color:green'>$message</span>\";
			</script>\n";
		}
		else {
			$message="Echec de l'envoi de la Fiche Bienvenue à l'adresse mail choisie '".$_POST['mail_dest']."'.";
			echo "<p style='color:red; text-align:center;' class='noprint'>$message</p>
			<script type='text/javascript'>
				document.getElementById('div_compte_rendu_envoi_mail').innerHTML=\"<span style='color:red'>$message</span>\";
			</script>\n";
		}
	}
}
//++++++++++++++++++++++++++++++

require("../lib/footer.inc.php");
?>
