<?php
/*
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();

} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}


if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

if (isset($_POST['sup'])) {
  $call_data = sql_query("SELECT indice_aid FROM aid_config");
  $sup_all = "";
  $liste_cible = '';
  for ($i=0; ($row=sql_row($call_data,$i)); $i++) {
      $id = $row[0];
      $temp = "sup".$id;
      if (isset($_POST[$temp])) {

        $test = sql_count(sql_query("SELECT indice_aid FROM aid WHERE indice_aid='".$id."'"));
        if ($test != 0) {
           $sup_all = 'no';
        } else {
           $liste_cible = $liste_cible.$id.";";
        }
      }
  }
  $_SESSION['chemin_retour'] = $_SERVER['REQUEST_URI']."?sup_all=".$sup_all;
  header("Location: ../lib/confirm_query.php?liste_cible=$liste_cible&action=del_type_aid".add_token_in_url(false));

}
  if (isset($_GET['sup_all'])) $sup_all = $_GET['sup_all']; else $sup_all = '';

  if ($sup_all=='no') $msg = "Une ou plusieurs catégories aid n'ont pas pu être supprimées car elles contiennent des aid.";

//**************** EN-TETE *********************
$titre_page = "Gestion des AID";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

echo "<p class=bold>";
echo "<a href=\"../accueil_admin.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a>";
echo "| <a href=\"config_aid.php\">Ajouter une catégorie d'AID</a> |";
$test_outils_comp = sql_query1("select count(outils_complementaires) from aid_config where outils_complementaires='y'");
if ($test_outils_comp != 0) {
    echo " <a href=\"config_aid_fiches_projet.php\">Configurer les fiches projet</a> |";
}
echo "</p><p class=\"medium\">";

$call_data = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM aid_config ORDER BY order_display1, order_display2, nom");
$nb_aid = mysqli_num_rows($call_data);
if ($nb_aid == 0) {
   echo "<p class='grand'>Il n'y a actuellement aucune catégorie d'AID</p>";
} else {
    echo "<form action=\"index.php\" name=\"formulaire\" method=\"post\">";
    echo "<table class='boireaus' border='1' cellpadding='3' summary=\"Catégories d'AID\">\n";
    echo "<tr><th><p>Nom - Modifications</p></th>\n";
    echo "<th><p>Liste des aid de la catégorie</p></th>\n";
    echo "<th><p>Nom complet de l'AID</p></th>\n";
    echo "<th><p><input type=\"submit\" name=\"sup\" value=\"Supprimer\" /></p></th></tr>\n";

    $i=0;
	$alt=1;
    while ($i < $nb_aid) {
        $nom_aid = @mysql_result($call_data, $i, "nom");
        $nom_complet_aid = @mysql_result($call_data, $i, "nom_complet");
        $indice_aid = @mysql_result($call_data, $i, "indice_aid");
        $outils_complementaires  = @mysql_result($call_data, $i, "outils_complementaires");
        if ($outils_complementaires=='y')
            $display_outils = "<br /><span class='small'>(Outils complémentaires activés)</span>";
        else
            $display_outils="";
        if ((getSettingValue("num_aid_trombinoscopes")==$indice_aid) and (getSettingValue("active_module_trombinoscopes")=='y'))
            $display_trombino = "<br /><span class='small'>(Gestion des accès élèves au trombinoscope)</span>";
        else
            $display_trombino="";

		$alt=$alt*(-1);
        echo "<tr class='lig$alt'><td><p><a href='config_aid.php?indice_aid=$indice_aid'>$nom_aid</a> $display_outils $display_trombino</p></td>";
        echo "<td><p><a href='index2.php?indice_aid=$indice_aid'>Liste des aid de la catégorie</a></p></td>";
        echo "<td><p>$nom_complet_aid</p></td>";
        echo "<td><center><p><input type=\"checkbox\" name=\"sup".$indice_aid."\" /></p></center></td></tr>";
        $i++;
    }
    echo "</table></form>";
}
require("../lib/footer.inc.php");
?>