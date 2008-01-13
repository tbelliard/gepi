<?php
/*
 * @version: $Id$
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);

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

if ($indice_aid =='') {
    header("Location: index.php");
    die();
}
$call_data = mysql_query("SELECT * FROM aid_config WHERE indice_aid = '$indice_aid'");
$nom_aid = @mysql_result($call_data, 0, "nom");

//**************** EN-TETE *********************
$titre_page = "Gestion des ".$nom_aid;
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************


echo "<p class=bold>";
echo "<a href=\"index.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a>";
echo "|<a href=\"add_aid.php?action=add_aid&amp;mode=unique&amp;indice_aid=$indice_aid\">Ajouter un(e) $nom_aid</a>|<a href=\"add_aid.php?action=add_aid&amp;mode=multiple&amp;indice_aid=$indice_aid\">Ajouter des $nom_aid à la chaîne</a>|";
echo "<br />|<a href=\"export_csv_aid.php?indice_aid=$indice_aid\">Importation de données depuis un fichier vers GEPI</a>|";
echo "</p><p class=\"medium\">";
// On va chercher les aid déjà existantes, et on les affiche.
if (!isset($order_by)) {$order_by = "numero,nom";}
$calldata = mysql_query("SELECT * FROM aid WHERE indice_aid='$indice_aid' ORDER BY $order_by");
$nombreligne = mysql_num_rows($calldata);

echo "<table width = 100% cellpadding=3 border=1>";
echo "<tr><td><p><a href='index2.php?order_by=numero,nom&amp;indice_aid=$indice_aid'>N°</a></p></td><td><p><a href='index2.php?order_by=nom&amp;indice_aid=$indice_aid'>Nom</a></p></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
$_SESSION['chemin_retour'] = $_SERVER['REQUEST_URI'];

$i = 0;
while ($i < $nombreligne){

    $aid_nom = @mysql_result($calldata, $i, "nom");
    $aid_num = @mysql_result($calldata, $i, "numero");
    if ($aid_num =='') {$aid_num='&nbsp;';}
    $aid_id = @mysql_result($calldata, $i, "id");
    echo "<tr><td><p class='medium'><b>$aid_num</b></p></td>";
    echo "<td><p class='medium'><a href='add_aid.php?action=modif_aid&amp;aid_id=$aid_id&amp;indice_aid=$indice_aid'><b>$aid_nom</b></a></p></td>";
    echo "<td><p class='medium'><a href='modify_aid.php?flag=prof&amp;aid_id=$aid_id&amp;indice_aid=$indice_aid'>Ajouter, supprimer des professeurs</a></p></td>";
    echo "<td><p class='medium'><a href='modify_aid.php?flag=eleve&amp;aid_id=$aid_id&amp;indice_aid=$indice_aid'>Ajouter, supprimer des élèves</a></p></td>";
    echo "<td><p class='medium'><a href='../lib/confirm_query.php?liste_cible=$aid_id&amp;liste_cible2=$indice_aid&amp;action=del_aid'>supprimer</a></p></td></tr>";


$i++;
}

?>
</table>
<?php require("../lib/footer.inc.php");