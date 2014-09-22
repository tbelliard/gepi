<?php
/**
*
*  @copyright Copyright 2001, 2014 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

/**
 * Fichiers d'initialisation
 */
require_once("./lib/initialisations.inc.php");

// INSERT INTO setting SET name='GepiResp_obtenir_compte_et_motdepasse', value='y';
//On vérifie si le dispositif est activé
if (!getSettingAOui("GepiResp_obtenir_compte_et_motdepasse")) {
    die("Le dispositif de réclamation de compte/mot de passe n'est pas activé.");
}

$nom=isset($_POST["nom"]) ? $_POST["nom"] : "";
$prenom=isset($_POST["prenom"]) ? $_POST["prenom"] : "";
$email=isset($_POST["email"]) ? $_POST["email"] : "";
$statut_demandeur=isset($_POST["statut_demandeur"]) ? $_POST["statut_demandeur"] : "";
$description=isset($_POST["description"]) ? $_POST["description"] : "";

$captcha=isset($_POST["captcha"]) ? $_POST["captcha"] : "";

$msg="";

// enregistrement des données
if (isset($_POST['is_posted'])) {
	if(($nom=="")||($prenom=="")||($email=="")||($description=="")) {
		$msg="<p style='color:red'>ERREUR : Un des champs n'a pas été rempli.</p>";
	}
	else {

		if($_POST['captcha'] == $_SESSION['captcha']) {
			$titre="Demande de compte et mot de passe : $nom $prenom";
			$mode="statut";
			if(getSettingAOui('RegBaseAdm_obtenir_compte_et_motdepasse')) {
				$texte="La demande de compte suivante a été formulée par ";
				if($statut_demandeur=="parent") {
					$texte.="<a href=\"./utilisateurs/edit_responsable.php?critere_recherche_login=".preg_replace("/[^A-Za-z]/", "%", $nom)."\">$nom $prenom</a>";
				}
				elseif($statut_demandeur=="eleve") {
					$texte.="<a href=\"./utilisateurs/edit_eleve.php?critere_recherche_login=".preg_replace("/[^A-Za-z]/", "%", $nom)."\">$nom $prenom</a>";
				}
				else {
					$texte.="$nom $prenom";
				}
				$texte.=" (<a href=\"mailto:$email\">$email</a>)";
				$texte.=" (<em>statut déclaré lors de la demande&nbsp;: ".$statut_demandeur."</em>)";
				$texte.=" le ".strftime("%d/%m/%Y à %H:%M")."\nDescription de la demande:\n".$description;
				$destinataire="administrateur";
				enregistre_infos_actions($titre,$texte,$destinataire,$mode);
			}

			$texte="La demande de compte suivante a été formulée par $nom $prenom";
			$texte.=" (<a href=\"mailto:$email\">$email</a>)";
			$texte.=" (<em>statut déclaré lors de la demande&nbsp;: ".$statut_demandeur."</em>)";
			$texte.=" le ".strftime("%d/%m/%Y à %H:%M")."\nDescription de la demande:\n".$description;
			if(getSettingAOui('RegBaseScol_obtenir_compte_et_motdepasse')) {
				$destinataire="scolarite";
				enregistre_infos_actions($titre,$texte,$destinataire,$mode);
			}
			if(getSettingAOui('RegBaseCpe_obtenir_compte_et_motdepasse')) {
				$destinataire="cpe";
				enregistre_infos_actions($titre,$texte,$destinataire,$mode);
			}

			if(getSettingAOui('SendMail_obtenir_compte_et_motdepasse')) {
				$titre="Demande de compte et mot de passe : $nom $prenom";
				//$texte=nl2br("La demande suivante a été formulée par $nom $prenom ($email) (statut déclaré lors de la demande : ".$statut_demandeur.")\nDescription de la demande:\n".preg_replace("/[\\\]{1,}n/","\n",$description));
				$description_nettoyee=preg_replace('/(\\\n)+/',"\n",$description);
				$texte="La demande suivante a été formulée par $nom $prenom ($email) (statut déclaré lors de la demande : ".$statut_demandeur.")\nDescription de la demande:\n".$description_nettoyee;

				$destinataire=getSettingValue('gepiDemandeCompteMdpAdress');
				if($destinataire=="") {
					$destinataire=getSettingValue('gepiAdminAdress');
				}

				$ajout_headers="";
				if(check_mail($email)) {
					$ajout_headers="Reply-To: $email\r\n";
				}

				if($destinataire!="") {
					envoi_mail($titre, $texte, $destinataire, $ajout_headers);
				}
			}

			$suite="y";

		}
		else {
			$msg.="Le captcha n'est pas bon.<br />";
			unset($_SESSION['captcha']);
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
<title><?php echo getSettingValue("gepiSchoolName"); ?> : Récupération de compte et mot de passe...</title>
<link rel="stylesheet" type="text/css" href="./style.css" />
<script src="lib/functions.js" type="text/javascript" language="javascript"></script>
<link rel="shortcut icon" type="image/x-icon" href="./favicon.ico" />
<link rel="icon" type="image/ico" href="./favicon.ico" />

<?php
// Styles paramétrables depuis l'interface:
if($style_screen_ajout=='y'){
	// La variable $style_screen_ajout se paramètre dans le /lib/global.inc
	// C'est une sécurité... il suffit de passer la variable à 'n' pour désactiver ce fichier CSS 
	// et éventuellement rétablir un accès après avoir imposé une couleur noire sur noire
	echo "<link rel='stylesheet' type='text/css' href='$gepiPath/style_screen_ajout.css' />\n";
}

echo "
</head>
";

if(isset($suite)) {

	if(getSettingAOui('Imprimer_obtenir_compte_et_motdepasse')) {
		echo "<body>
<div style='margin:1em;'>
<h2>".getSettingValue('gepiSchoolName')." : Demande de compte</h2>

<p>Je souhaite obtenir (<em>ou récupérer</em>) un compte et mot de passe pour accéder aux données me concernant ou concernant mon ou mes enfants scolarisés dans l'établissement.</p>

<table class='boireaus boireaus_alt'>
	<tr><td>Nom</td><td>$nom</td></tr>
	<tr><td>Prénom</td><td>$prenom</td></tr>
	<tr><td>Email</td><td>$email</td></tr>
	<tr><td>Statut</td><td>$statut_demandeur</td></tr>
	<tr>
		<td valign='top'>Description de la demande&nbsp;:</td>
		<td>
			".preg_replace("/\\\\n/","<br />",nl2br($description))."
		</td>
	</tr>
</table>
<p>Le ".strftime("%d/%m/%Y à %H:%M").".</p>
<p>Signature&nbsp;:</p>
<p><br /></p>
<p><br /></p>
<p style='text-decoration:blink; color:red;' class='noprint'>Document à imprimer et à remettre à l'Administration.</p>
<p class='noprint'><a href='./login.php'><img src='./images/icons/back.png' alt='Retour' class='back_link'/> Retour à la page de connexion</a></p>

</div>";
	}
	else {
		echo "<body>
<div style='margin:1em;'>
<h2>".getSettingValue('gepiSchoolName')." : Demande de compte</h2>

<p>Votre demande a été enregistrée.</p>

<p class='noprint'><a href='./login.php'><img src='./images/icons/back.png' alt='Retour' class='back_link'/> Retour à la page de connexion</a></p>

</div>";
	}

	require("./lib/footer.inc.php");
	die();
}

echo "<body onload=\"document.getElementById('nom').focus()\">

<div class='norme' style='text-align:center;'>
	<p class='bold'>
		<a href='./login.php'><img src='./images/icons/back.png' alt='Retour' class='back_link'/> Retour à la page de connexion</a>
	</p>
</div>

<h2 class='gepi'>Demande de compte/mot de passe</h2>

<div align='center'>

	<span style='color:red'>$msg</span>

	<p>Vous avez oublié vos compte et mot de passe, ou vous souhaitez obtenir un compte pour accéder aux données concernant votre enfant.<br />
	Veuillez compléter le formulaire ci-dessous.</p>

	<form enctype=\"multipart/form-data\" name= \"formulaire\" action=\"".$_SERVER['PHP_SELF']."\" method='post'>
	<table class='boireaus boireaus_alt'>
		<tr><td>Nom</td><td><input type='text' name='nom' size='40' value=\"$nom\" /></td></tr>
		<tr><td>Prénom</td><td><input type='text' name='prenom' size='40' value=\"$prenom\" /></td></tr>
		<tr><td>Email</td><td><input type='text' name='email' size='40' value=\"$email\" /></td></tr>
		<tr>
			<td>Statut</td>
			<td>
				<select name='statut_demandeur'>
					<option value='parent'>parent ou responsable</option>
					<option value='eleve'>élève</option>
					<option value='autre'>autre</option>
				</select>
			</td>
		</tr>
		<tr>
			<td valign='top'>Enfants/élèves</td>
			<td>
				<p>Dans le cas d'une demande parent/responsable, veuillez préciser les nom, prénom et classe<br />de l'un au moins de vos enfants scolarisés dans l'établissement.<br />
				Dans le cas d'une demande élève, veuillez préciser vos nom, prénom et classe.</p>
				<textarea name='description' cols='50' rows='4'>".preg_replace("/\\\\n/","\n",$description)."</textarea>
			</td>
		</tr>
	</table>

	<strong><a href='http://fr.wikipedia.org/wiki/Captcha' target='_blank' title=\"Captcha : Dispositif destiné à contrôler que c'est bien un humain et non une machine/robot qui valide le formulaire.\">Captcha</a></strong><br />
	<label for='captcha'>Combien font ".captcha()." ?</label><br /><input type='text' name='captcha' id='captcha' autocomplete=\"off\" /><br />
	<span style='font-size:x-small'>(réponse attendue en chiffres)</span>
	<br />

	<input type='hidden' name='is_posted' value='y' />
	<input type='submit' value='Valider' />

	</form>

</div>

<p><br /></p>

<p><em>NOTES&nbsp;:</em></p>
<ul>";

if(getSettingAOui('Imprimer_obtenir_compte_et_motdepasse')) {
	echo "
	<li>
		Un document va être généré.<br />
		Vous devrez imprimer le document et votre enfant devra déposer cette demande à l'Administration de l'établissement pour finaliser la demande.<br />
		Cette démarche est destinée à éviter des usurpations d'identité.
	</li>";
}
echo "
	<li>
		En précisant votre adresse mail, vous pourrez par la suite recevoir par mail les informations demandées.<br />
		(<em>Il est généralement plus facile de copier/coller les informations reçues que de les taper</em>)
	</li>
</ul>

</body>
</html>
";

?>
