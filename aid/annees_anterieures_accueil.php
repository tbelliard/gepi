<?php
/*
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

$resultat_session = $session_gepi->security_check();
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

// Initialisation des variables
// Initialisation des variables
$indice_aid = isset($_POST["indice_aid"]) ? $_POST["indice_aid"] : (isset($_GET["indice_aid"]) ? $_GET["indice_aid"] : NULL);
$nom_projet = sql_query1("select nom from aid_config where indice_aid='".$indice_aid."'");
$annee_scolaire = isset($_POST["annee_scolaire"]) ? $_POST["annee_scolaire"] : (isset($_GET["annee_scolaire"]) ? $_GET["annee_scolaire"] : NULL);

//**************** EN-TETE *********************
$titre_page = "Fiches projets des années antérieures";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

if (!isset($annee_scolaire))  {
    echo "<p class=bold>";
    echo "<a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
    echo "</p>\n";
    $sql = "select distinct annee from archivage_types_aid WHERE outils_complementaires = 'y'";
    $res = mysql_query($sql);
    $nb_annee = mysql_num_rows($res);
    if ($nb_annee >= 1) {
        echo "<form name=\"form1\" action=\"annees_anterieures_accueil.php\" method=\"post\">\n";
        echo "<center><h1 class='gepi'>Fiches projet</h1>";
        echo "<h2>Choisissez l'année :</h2>\n";
        echo "<select name=\"annee_scolaire\" size=\"1\">\n";
        $k = 0;
        while ($k < $nb_annee) {
            $annee_scolaire_ = mysql_result($res,$k,"annee");
            echo "<option value=\"".$annee_scolaire_."\">".$annee_scolaire_."</option>\n";
            $k++;
        }
      echo "</select>\n";
      echo "<input type=\"submit\" name=\"ok\" value=\"Envoyer\" />\n</center></form>\n";
      include "../lib/footer.inc.php";
      die();
    }
}

if (($indice_aid =='') and ($annee_scolaire!=''))  {
  echo "<p class=bold>";
  echo "<a href=\"annees_anterieures_accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
  echo "</p>\n";
  echo "<center><h1 class='gepi'>Année scolaire " . $annee_scolaire."<br />";
  $call_aid = mysql_query("select * from archivage_types_aid where outils_complementaires='y' and annee='".$annee_scolaire."' order by nom");

  $nb_projet = mysql_num_rows($call_aid);
  if ($nb_projet!=0) {
        $i = 0;
        $k=0;
        while ($i < $nb_projet) {
          $indice_aid = mysql_result($call_aid,$i,"id");
          $nb_fiches_publiques[$indice_aid] = sql_query1("SELECT count(id) FROM archivage_aids WHERE id_type_aid ='".$indice_aid."'");
          if ($nb_fiches_publiques[$indice_aid]!=0)
            $k++;
          $i++;
        }
        if ($k!=0) {
            echo "Consultation des fiches projet</h1></center>";
            echo "<ul>\n";
            $i = 0;
            while ($i < $nb_projet) {
              $indice_aid = mysql_result($call_aid,$i,"id");
              if ($nb_fiches_publiques[$indice_aid]!=0) {
                    $nom = mysql_result($call_aid,$i,"nom");
                    $nom_complet = mysql_result($call_aid,$i,"nom_complet");
                    echo "<li><a href='annees_anterieures_accueil.php?indice_aid=".$indice_aid."&amp;annee_scolaire=".$annee_scolaire."'>".$nom_complet."</a> (".$nom.")</li>\n";
              }
              $i++;
            }
            echo "</ul>\n";
        } else
            echo "Aucune fiche projet n'est actuellement disponible.</h1></center>";
    } else {
        echo "Aucune fiche projet n'est actuellement disponible.</h1></center>";
    }
    include "../lib/footer.inc.php";
    die();
}

$nom_projet = sql_query1("select nom from archivage_types_aid where id='".$indice_aid."'");
echo "<p class=bold>";
echo "<a href=\"annees_anterieures_accueil.php?annee_scolaire=".$annee_scolaire."\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
echo "</p>\n";
echo "<center><H3>Liste des projets ".$nom_projet."</H3></center>";
$_login = $_SESSION["login"];
$message_avertissement = "";
$non_defini = "<font color='red'>Non défini</font>";
$annee=$annee_scolaire;
include "./fiches.inc.php";

?>