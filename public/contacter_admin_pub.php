<?php
/*
 * Last modification  : 10/05/2005
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Patrick Duthilleul
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
$niveau_arbo = "public";
require_once("../lib/initialisations.inc.php");

require_once("lib/auth.php");
$action = isset($_POST["action"]) ? $_POST["action"] : '';
$nama = isset($_POST["nama"]) ? $_POST["nama"] : '';
$message = isset($_POST["message"]) ? $_POST["message"] : '';
$email_reponse = isset($_POST["email_reponse"]) ? $_POST["email_reponse"] : '';


//**************** EN-TETE *****************
require_once("./lib/header.inc");
//**************** FIN EN-TETE *************

?>
<H1 class='gepi'>GEPI - Obtenir de l'aide de l'administrateur.</H1>
<?php

switch($action)
{

//envoi du message
case "envoi":
    //N.B. pour peaufiner, mettre un script de vérification de l'adresse email et du contenu du message !
    $message = "Demandeur : ".$nama."\nEtablissement : ".getSettingValue("gepiSchoolName")."\n".unslashes($message);
    if ($email_reponse == '') {
        echo "<br><br><br><P style=\"text-align: center\">Votre message n'a pas été envoyé : vous devez indiquer une adresse e-mail pour la réponse !</p>";
    } else {
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
    }
    break;

default://formulaire d'envoi
    echo "<form action='contacter_admin_pub.php' method='post' name='doc'>";
    echo "<table cellpadding='5'>";
    echo "<tr><td>Message posté par&nbsp;:</td><td><input type='text' name='nama' value='Indiquez votre nom et votre prénom' size=40 maxlength='256'></td></tr>";
    echo "<tr><td>Votre e-mail pour la réponse (obligatoire)</td><td><input type='text' name='email_reponse' size='40' maxlength='256'></td></tr>";
    echo "<tr><td>Nom et prénom de l'administrateur&nbsp;: </td><td><b>".getSettingValue("gepiAdminNom")." ".getSettingValue("gepiAdminPrenom")."</b></td></tr>";

    echo "<tr><td>Nom de l'établissement : </td><td><b>".getSettingValue("gepiSchoolName")."</b></td></tr>";
    echo "<tr><td colspan=2>Utilisez l'adresse <b><a href=\"mailto:" . getSettingValue("gepiAdminAdress") . "\">".getSettingValue("gepiAdminAdress")."</a></b> ou bien rédigez votre message ci-dessous : </td><td></tr>";
    echo "</table>";
    ?>


    <input type="hidden" name="action" value="envoi">
    <textarea name="message" cols="80" rows="8">Contenu du message : </textarea><br>
    <input type="submit" value="Envoyer le message">

    </form>
    <?php
    break;
}
?>
</body>
</html>