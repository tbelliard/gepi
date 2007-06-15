<?php
/*
 * $Id$
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Patrick Duthilleul
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

// On précise de ne pas traiter les données avec la fonction anti_inject
$traite_anti_inject = 'no';
// Initialisations files
require_once("../lib/initialisations.inc.php");

// Resume session
$resultat_session = resumeSession();
if ($resultat_session == '0') {
   header("Location: ../logout.php?auto=1");
   die();
};
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

$action = isset($_POST["action"]) ? $_POST["action"] : '';
$nama = isset($_POST["nama"]) ? $_POST["nama"] : '';
$message = isset($_POST["message"]) ? $_POST["message"] : '';
$email_reponse = isset($_POST["email_reponse"]) ? $_POST["email_reponse"] : '';
$casier = isset($_POST["casier"]) ? $_POST["casier"] : '';

//**************** EN-TETE *****************
require_once("../lib/header.inc");
//**************** FIN EN-TETE *************

?>
<H1 class='gepi'>GEPI - Obtenir de l'aide de l'administrateur.</H1>
<?php
switch($action)
{
//envoi du message
case "envoi":
//N.B. pour peaufiner, mettre un script de vérification de l'adresse email et du contenu du message !

$corps_message=$message;

$message = "Demandeur : ".$nama."\n";
$message = "Statut : ".$_SESSION['statut']."\n";
$message .= "Etablissement : ".getSettingValue("gepiSchoolName")."\n".unslashes($message);

$message.="\n".$corps_message."\n";

if ($_SESSION['statut'] != "responsable" AND $_SESSION['statut'] != "eleve") {
	$message .= "\n\nMode de réponse : ".($email_reponse =="" ? "dans le casier =>$casier" :"par email.");
} else {
	$message .= "\n\nMode de réponse : par email (si spécifié)";
}

$envoi = mail(getSettingValue("gepiAdminAdress"),
    "Demande d'aide dans GEPI",
    $message,
   "From: ".($email_reponse != "" ? "$nama <$email_reponse>" : getSettingValue("gepiAdminAdress"))."\r\n"
   .($email_reponse != "" ? "Reply-To: $nama <$email_reponse>\r\n" :"")
   ."X-Mailer: PHP/" . phpversion());
if ($envoi) {
    echo "<br><br><br><P style=\"text-align: center\">Votre message été envoyé,vous recevrez rapidement<br>une réponse dans votre ".($email_reponse =="" ? "casier" :"boîte aux lettres électronique").", veuillez ".($email_reponse =="" ? "le" :"la")." consulter régulièrement.<br><br><br><a href=\"javascript:self.close();\">Fermer</a></p>";
} else {
    echo "<br><br><br><P style=\"text-align: center\"><font color=\"red\">ATTENTION : impossible d'envoyer le message, contactez l'administrateur pour lui signaler l'erreur ci-dessus.</font>            </p>";
}
break;
default://formulaire d'envoi
echo "<table cellpadding='5'>";
echo "<tr><td>Message posté par :</td><td><b>".$_SESSION['prenom'] . " " . $_SESSION['nom']."</b></td></tr>";
echo "<tr><td>Nom et prénom de l'administrateur : </td><td><b>".getSettingValue("gepiAdminNom")." ".getSettingValue("gepiAdminPrenom")."</b></td></tr>";

echo "<tr><td>Nom de l'établissement : </td><td><b>".getSettingValue("gepiSchoolName")."</b></td></tr>";
echo "<tr><td colspan=2>Utilisez l'adresse <b><a href=\"mailto:" . getSettingValue("gepiAdminAdress") . "\">".getSettingValue("gepiAdminAdress")."</a></b> ou bien rédigez votre message ci-dessous : </td><td></tr>";
echo "</table>";
?>
<form action="contacter_admin.php" method="post" name="doc">
<input type="hidden" name="nama" value="<?php echo $_SESSION['prenom']." ".$_SESSION['nom']; ?>" />
<input type="hidden" name="action" value="envoi" />
<textarea name="message" cols="50" rows="5">Contenu du message : </textarea><br />
E-mail pour la réponse : <?php if ($_SESSION['statut'] != "responsable" AND $_SESSION['statut'] != "eleve") echo "(facultatif, une réponse vous sera adressée dans votre casier si vous ne précisez pas d'e-mail)";?><br/>
<input type="text" name="email_reponse" size="40" maxlength="256" /><br/>
<?php if ($_SESSION['statut'] != "responsable" AND $_SESSION['statut'] != "eleve") { ?>
Ou numéro de votre casier en salle des professeurs pour la réponse :
<input type="text" name="casier" size="40" maxlength="256" value="Casier N°" /><br/><br/>
<?php } ?>
<input type="submit" value="Envoyer le message" />

</form>
<?php
break;
}
require("../lib/footer.inc.php");
?>