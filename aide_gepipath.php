<?php
/*
 * Copyright 2001-2004 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
// cela ici concerne le mot de passe
$variables_non_protegees = 'yes';

// Initialisations files
require_once("./lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();

$valid = isset($_POST["valid"]) ? $_POST["valid"] : 'no';

if (isset($_POST['submit'])) {
    if (isset($_POST['login']) && isset($_POST['no_anti_inject_password'])) {
        $md5password = md5($NON_PROTECT['password']);
        $sql = "select upper(login) login, password, prenom, nom, statut from utilisateurs where login = '" . $_POST['login'] . "' and password = '".$md5password."' and etat != 'inactif' and statut='administrateur' ";
        $res_user = sql_query($sql);
        $num_row = sql_count($res_user);
        if ($num_row == 1) {
            $valid='yes';
        } else {
            $message = "Identifiant ou mot de passe incorrect, ou bien vous n'êtes pas administrateur.";
        }
    }
}


?>
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
    <HTML>
    <HEAD>
    <link REL="stylesheet" href="style.css" type="text/css" />
    <TITLE>Aide à la configuration de connect.inc.php</TITLE>
    <link rel="shortcut icon" type="image/x-icon" href="./favicon.ico" />
    <link rel="icon" type="image/ico" href="./favicon.ico" />
    </head>
    <BODY>
<?php
if (($resultat_session == '0') and ($valid!='yes')) {
    ?>
    <form action="aide_gepipath.php" method='POST' style="width: 100%; margin-top: 24px; margin-bottom: 48px;">
    <div class="center">
    <H2><?php echo "Aide à la configuration de connect.inc.php<br />(Accès administrateur)"; ?></H2>

    <?php
    if (isset($message)) {
        echo("<p><font color=red>" . $message . "</font></p>");
    }
    ?>
    <fieldset style="padding-top: 8px; padding-bottom: 8px; width: 40%; margin-left: auto; margin-right: auto;">
    <legend style="font-variant: small-caps;">Identifiez-vous</legend>
    <table style="width: 100%; border: 0;" cellpadding="5" cellspacing="0">
    <tr>
    <td style="text-align: right; width: 40%; font-variant: small-caps;"><label for="login">Identifiant</label></td>
    <td style="text-align: center; width: 60%;"><input type="text" name="login" size="16" /></td>
    </tr>
    <tr>
    <td style="text-align: right; width: 40%; font-variant: small-caps;"><label for="no_anti_inject_password">Mot de passe</label></td>
    <td style="text-align: center; width: 60%;"><input type="password" name="no_anti_inject_password" size="16" /></td>
    </tr>
    </table>
    <input type="submit" name="submit" value="Envoyer" style="font-variant: small-caps;" />
    </fieldset>
    </div>
    </form>
    </body>
    </html>
    <?php
    die();
};

if ((isset($_SESSION['statut'])) and ($_SESSION['statut'] != 'administrateur')) {

   echo "<center><p class=grand><font color=red>Vous n'avez pas les droits suffisants pour accéder à cette page.</font><p></center></body></html>";

   die();

}

echo "<center><p class=grand>Aide à la configuration du fichier connect.inc.php de GEPI<p></center>";



// Valeur actuelle de gepipath

echo "<p>La valeur de la variable <b>\$gepiPath</b> actuellement enregistrée dans le fichier connect.inc.php

est : <b>".$gepiPath."</b><br /><br />";



$url = parse_url($_SERVER['REQUEST_URI']);

$temp = $url['path'];

$d = strlen($temp) - strlen("aide_gepipath.php") - 1;

$gepi_path = substr($temp, 0, $d);

if ($gepi_path != $gepiPath) {

    echo "Cette valeur semble incorrecte. La valeur correcte de <b>\$gepiPath</b> est : <b>".$gepi_path."</b>";

} else {

    echo "<b>Cette valeur semble correcte.</b>";

}



?>

</body></html>