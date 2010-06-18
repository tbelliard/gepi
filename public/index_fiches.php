<?php
//error_reporting (E_ALL);
/*
 * Last modification  : 12/05/2005
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Gabriel Fischer
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
$niveau_arbo = "public";
require_once("../lib/initialisations.inc.php");
require_once("lib/functions.inc");


//**************** EN-TETE *****************
$titre_page = "Fiches projet";
$page_accueil = "index_fiches.php";
require_once("lib/header.inc");
//**************** FIN EN-TETE *************

// Initialisation des variables
$indice_aid = isset($_POST["indice_aid"]) ? $_POST["indice_aid"] : (isset($_GET["indice_aid"]) ? $_GET["indice_aid"] : NULL);
$annee_scolaire = isset($_POST["annee_scolaire"]) ? $_POST["annee_scolaire"] : (isset($_GET["annee_scolaire"]) ? $_GET["annee_scolaire"] : NULL);

// Y-a-t-il le choix entre plusieurs années ?
if (!isset($annee_scolaire))  {
    $sql = "select distinct annee from archivage_aids where fiche_publique ='y'";
    $res = mysql_query($sql);
    $nb_annee = mysql_num_rows($res);
    if ($nb_annee >= 1) {
        echo "<form name=\"form1\" action=\"index_fiches.php\" method=\"post\">\n";
        echo "<center><h1 class='gepi'>".getSettingValue("gepiSchoolName"). "<br />";
        echo "Consultation des fiches projet</h1>";
        echo "<h2>Choisissez l'année :</h2>\n";
        echo "<select name=\"annee_scolaire\" size=\"1\">\n";
        echo "<option value=\"annee_courante\">".getSettingValue("gepiYear")."</option>";
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
    } else {
      $annee_scolaire = getSettingValue("gepiYear");
    }
}

if ($annee_scolaire=="annee_courante")
    $annee_scolaire = getSettingValue("gepiYear");

if ($annee_scolaire == getSettingValue("gepiYear"))
    $annee_courante=TRUE;
else
    $annee_courante=FALSE;

if (($indice_aid =='') and ($annee_scolaire!=''))  {
  echo "<center><h1 class='gepi'>".getSettingValue("gepiSchoolName"). " - année scolaire " . $annee_scolaire."<br />";
  if ($annee_courante)
    $call_aid = mysql_query("select * from aid_config where outils_complementaires='y' order by nom");
  else
    $call_aid = mysql_query("select * from archivage_types_aid where outils_complementaires='y' and annee='".$annee_scolaire."' order by nom");

  $nb_projet = mysql_num_rows($call_aid);
  if ($nb_projet!=0) {
        $i = 0;
        $k=0;
        while ($i < $nb_projet) {
          if ($annee_courante) {
            $indice_aid = mysql_result($call_aid,$i,"indice_aid");
            $nb_fiches_publiques[$indice_aid] = sql_query1("SELECT count(id) FROM aid WHERE indice_aid='".$indice_aid."' and fiche_publique='y'");
          } else {
            $indice_aid = mysql_result($call_aid,$i,"id");
            $nb_fiches_publiques[$indice_aid] = sql_query1("SELECT count(id) FROM archivage_aids WHERE id_type_aid ='".$indice_aid."' and fiche_publique='y'");
          }
          if ($nb_fiches_publiques[$indice_aid]!=0)
            $k++;
          $i++;
        }
        if ($k!=0) {
            echo "Consultation des fiches projet</h1></center>";
            echo "<ul>\n";
            $i = 0;
            while ($i < $nb_projet) {
              if ($annee_courante)
                $indice_aid = mysql_result($call_aid,$i,"indice_aid");
              else
                $indice_aid = mysql_result($call_aid,$i,"id");
              if ($nb_fiches_publiques[$indice_aid]!=0) {
                    //$nb_fiches_publiques = sql_query1("SELECT count(aid) FROM aid WHERE indice_aid='$indice_aid' and fiche_publique='y'");
                    $nom = mysql_result($call_aid,$i,"nom");
                    $nom_complet = mysql_result($call_aid,$i,"nom_complet");
                    echo "<li><a href='index_fiches.php?indice_aid=".$indice_aid."&amp;annee_scolaire=".$annee_scolaire."'>".$nom_complet."</a> (".$nom.")</li>\n";
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

// Vérification de la validité de $indice_aid et $aid_id
if ($annee_courante) {
  if (!VerifAidIsAcive($indice_aid,"",""))  {
    echo "<p>Vous tentez d'accéder à des outils qui ne sont pas activés. veuillez contacter l'administrateur.</p></body></html>";
    die();
  }
} else {
  if (!VerifAidIsAcive($indice_aid,"","y"))  {
    echo "<p>* Vous tentez d'accéder à des outils qui ne sont pas activés. veuillez contacter l'administrateur.</p></body></html>";
    die();
  }
}
if ($annee_courante) {
  $nb_fiches_publiques = sql_query1("SELECT count(id) FROM aid WHERE indice_aid='".$indice_aid."' and fiche_publique='y'");
  $nom_projet = sql_query1("select nom from aid_config where indice_aid='".$indice_aid."'");
}else{
  $nb_fiches_publiques = sql_query1("SELECT count(id) FROM archivage_aids WHERE id_type_aid='".$indice_aid."' and fiche_publique='y'");
  $nom_projet = sql_query1("select nom from archivage_types_aid  where id='".$indice_aid."'");
}
echo "<center><h3 class='gepi'>".getSettingValue("gepiSchoolName"). " - année scolaire " . $annee_scolaire;
if ($nb_fiches_publiques ==0) {
     echo "<br />Aucune fiche projet n'est actuellement disponible.</h3></center>";
     include "../lib/footer.inc.php";
     die();

}
echo "<br />Liste des projets ".$nom_projet."</H3></center>";


$_login = "";
$message_avertissement = "Avertissement : le travail scolaire auquel vous allez accéder est en phase de construction, ce qui explique les défauts éventuels.";
$non_defini = "-";
if (!$annee_courante)
    $annee = $annee_scolaire;

include "../aid/fiches.inc.php";
?>