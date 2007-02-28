<?php
/*
 * Last modification  : 30/09/2006
 *
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
// Initialisations files
require_once("../lib/initialisations.inc.php");

// Resume session
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
die();
};

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
die();
}



if (isset($_POST['impression'])) {

    //$imp = $_POST['impression'];
    $imp = html_entity_decode_all_version($_POST['impression']);

    if (!saveSetting("Impression", $imp)) {

        $msg = "Erreur lors de l'enregistrement !";

    } else {

        $msg = "Enregistrement réussi !";

    }

}

//**************** EN-TETE *****************
$titre_page = "Outil de gestion | Impression des paramètres";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<form enctype="multipart/form-data" action="modify_impression.php" method=post name=formulaire>
<p class=bold><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a>| <input type=submit value=Enregistrer></p>

<?php

if (!loadSettings()) {
    die("Erreur chargement settings");
}

$impression = getSettingValue("Impression");
echo "<h3 class='gepi'>Fiche d'information au format html :</H3>";
echo "<p>Lors de la création d'un utilisateur, il vous est possible d'imprimer une feuille d'information contenant les paramètres de connexion à GEPI, ainsi que le texte ci-dessous. Attention, ce texte est au format html.</p>";
echo "<div class='small'><textarea name=impression rows=40 cols=100 wrap='virtual'>";
echo "$impression";
echo "</textarea></div>";
?>
</form>
<?php require("../lib/footer.inc.php");?>